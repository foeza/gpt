<?php
App::uses('Component', 'Controller');
App::uses('Router', 'Routing');
App::uses('Security', 'Utility');
App::uses('Debugger', 'Utility');
App::uses('Hash', 'Utility');
App::uses('CakeSession', 'Model/Datasource');
App::uses('BaseAuthorize', 'Controller/Component/Auth');
App::uses('BaseAuthenticate', 'Controller/Component/Auth');
class AuthComponent extends Component {
	const ALL = 'all';

	// Customize
	public $authorize = array(
	    'Controller',
	    'Actions' => array(
	    	'actionPath' => 'controllers'
    	),
	);
	public $userModel = 'User';
	public $loginRedirect = array(
    	'controller' => 'users', 
    	'action' => 'account',
    	'admin' => true,
	);
	public $logoutRedirect = array(
		'controller' => 'users', 
		'action' => 'login',
    	'admin' => true,
	);

	public $components = array('Session', 'RequestHandler','RmCommon', 'RmUser', 'RumahkuApi');
	public $authenticate = array('Form');
	protected $_authenticateObjects = array();
	protected $_authorizeObjects = array();
	public $ajaxLogin = null;
	public $flash = array(
		'element' => 'default',
		'key' => 'auth',
		'params' => array()
	);
	public static $sessionKey = 'Auth.User';
	protected static $_user = array();
	public $loginAction = array(
		'controller' => 'users',
		'action' => 'login',
    	'admin' => true,
		'plugin' => null
	);
	public $loginAdminRedirect = null;
	public $authError = null;
	public $unauthorizedRedirect = true;
	public $changeEmail = null;
	public $allowedActions = array();
	public $request;
	public $response;
	protected $_methods = array();
	public function initialize(Controller $controller) {
		$this->request = $controller->request;
		$this->response = $controller->response;
		$this->_methods = $controller->methods;
		$this->controller = $controller;

		if (Configure::read('debug') > 0) {
			Debugger::checkSecurityKeys();
		}
	}
	public function startup(Controller $controller) {
		$methods = array_flip(array_map('strtolower', $controller->methods));
		$action = strtolower($controller->request->params['action']);

		$isMissingAction = (
			$controller->scaffold === false &&
			!isset($methods[$action])
		);

		if ($isMissingAction) {
			return true;
		}

		if (!$this->_setDefaults()) {
			return false;
		}

		if ($this->_isAllowed($controller)) {
			return true;
		}

		if (!$this->_getUser()) {
			return $this->_unauthenticated($controller);
		}

		if (empty($this->authorize) || $this->isAuthorized($this->user())) {
			return true;
		}


		if( $controller->Rest->isActive() ) {
			Configure::write('Rest.token', null);
			$controller->redirect(array(
				'controller' => 'users',
				'action' => 'message',
				$this->authError,
				'error',
				'ext' => 'json',
				'admin' => false,
				'api' => true,
			));
		} else {
			$this->Session->setFlash($this->authError, $this->flashElement, array(), 'error');
			$controller->redirect('/');
		}
	}
	protected function _isAllowed(Controller $controller) {
		$action = strtolower($controller->request->params['action']);
		if (in_array($action, array_map('strtolower', $this->allowedActions))) {
			return true;
		}
		return false;
	}
	protected function _unauthenticated(Controller $controller) {
		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		$auth = $this->_authenticateObjects[count($this->_authenticateObjects) - 1];
		if ($auth->unauthenticated($this->request, $this->response)) {
			return false;
		}

		if ($this->_isLoginAction($controller)) {
			return true;
		}

		if ( !$controller->request->is('ajax') ) {
			if( $controller->Rest->isActive() ) {
				return true;
			} else {
				$this->flash($this->authError);
				$this->Session->write('Auth.redirect', $controller->request->here(false));
				$controller->redirect($this->loginAction);
				return false;
			}
		}
		if (!empty($this->ajaxLogin)) {
			$controller->viewPath = 'Elements';
			echo $controller->render($this->ajaxLogin, $this->RequestHandler->ajaxLayout);
			$this->_stop();
			return false;
		}
		$controller->redirect(null, 403);
		return false;
	}
	protected function _isLoginAction(Controller $controller) {
		$url = '';
		if (isset($controller->request->url)) {
			$url = $controller->request->url;
		}
		$url = Router::normalize($url);
		$loginAction = Router::normalize($this->loginAction);

		if ($loginAction == $url) {
			if (empty($controller->request->data)) {
				if (!$this->Session->check('Auth.redirect') && env('HTTP_REFERER')) {
					$this->Session->write('Auth.redirect', $controller->referer(null, true));
				}
			}
			return true;
		}
		return false;
	}
	protected function _unauthorized(Controller $controller) {
		if ($this->unauthorizedRedirect === false) {
			throw new ForbiddenException($this->authError);
		}

		$this->flash($this->authError);
		if ($this->unauthorizedRedirect === true) {
			$default = '/';

			if (!empty($this->loginRedirect)) {
				$default = $this->loginRedirect;
			}
			$url = $controller->referer($default, true);
		} else {
			$url = $this->unauthorizedRedirect;
		}
		$controller->redirect($url, null, true);
		return false;
	}
	protected function _setDefaults() {
		$defaults = array(
			'logoutRedirect' => $this->loginAction,
			'authError' => __d('cake', 'You are not authorized to access that location.')
		);
		foreach ($defaults as $key => $value) {
			if (!isset($this->{$key}) || $this->{$key} === true) {
				$this->{$key} = $value;
			}
		}
		return true;
	}
	public function isAuthorized($user = null, CakeRequest $request = null) {
		if (empty($user) && !$this->user()) {
			return false;
		}
		if (empty($user)) {
			$user = $this->user();
		}
		if (empty($request)) {
			$request = $this->request;
		}
		if (empty($this->_authorizeObjects)) {
			$this->constructAuthorize();
		}
		foreach ($this->_authorizeObjects as $authorizer) {
			if ($authorizer->authorize($user, $request) === true) {
				return true;
			}
		}
		return false;
	}
	public function constructAuthorize() {
		if (empty($this->authorize)) {
			return;
		}
		$this->_authorizeObjects = array();
		$config = Hash::normalize((array)$this->authorize);
		$global = array();
		if (isset($config[AuthComponent::ALL])) {
			$global = $config[AuthComponent::ALL];
			unset($config[AuthComponent::ALL]);
		}
		foreach ($config as $class => $settings) {
			list($plugin, $class) = pluginSplit($class, true);
			$className = $class . 'Authorize';
			App::uses($className, $plugin . 'Controller/Component/Auth');
			if (!class_exists($className)) {
				throw new CakeException(__d('cake_dev', 'Authorization adapter "%s" was not found.', $class));
			}
			if (!method_exists($className, 'authorize')) {
				throw new CakeException(__d('cake_dev', 'Authorization objects must implement an %s method.', 'authorize()'));
			}
			$settings = array_merge($global, (array)$settings);
			$this->_authorizeObjects[] = new $className($this->_Collection, $settings);
		}
		return $this->_authorizeObjects;
	}
	public function allow($action = null) {
		$args = func_get_args();
		if (empty($args) || $action === null) {
			$this->allowedActions = $this->_methods;
			return;
		}
		if (isset($args[0]) && is_array($args[0])) {
			$args = $args[0];
		}
		$this->allowedActions = array_merge($this->allowedActions, $args);
	}
	public function deny($action = null) {
		$args = func_get_args();
		if (empty($args) || $action === null) {
			$this->allowedActions = array();
			return;
		}
		if (isset($args[0]) && is_array($args[0])) {
			$args = $args[0];
		}
		foreach ($args as $arg) {
			$i = array_search($arg, $this->allowedActions);
			if (is_int($i)) {
				unset($this->allowedActions[$i]);
			}
		}
		$this->allowedActions = array_values($this->allowedActions);
	}
	public function mapActions($map = array()) {
		if (empty($this->_authorizeObjects)) {
			$this->constructAuthorize();
		}
		foreach ($this->_authorizeObjects as $auth) {
			$auth->mapActions($map);
		}
	}
	public function login($user = null, $verify = false, $allowChanges = false, $params = null) {
		$this->_setDefaults();
		
		if( empty($params) ) {
			$params = $this->controller->params->params;
		}

		$pass_token = Common::_callPassToken($params);

		if ( empty($user) && ( !empty($this->request->data) || !empty($pass_token) ) ) {
			$user = $this->identify($this->request->data, $verify);
		}

		$msg = !empty($user['msg']['status'])?$user['msg']['status']:false;

		if ( !empty($allowChanges) || $msg == 'success' ) {
			unset($user['msg']);

			if( !empty($user['User']) ) {
				$user = $user['User'];
			}
			
			$this->Session->write(self::$sessionKey, $user);
			
			return $this->loggedIn();
		}else{
			$this->RmCommon->setCustomFlash($user['msg']['errormessage'], 'error');
			return false;
		}
	}
	public function logout( $activity = false ) {
		$this->_setDefaults();
		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		$user = $this->user();
		foreach ($this->_authenticateObjects as $auth) {
			$auth->logout($user);
		}
		$this->Session->delete(self::$sessionKey);
		$this->Session->delete('Auth.redirect');

		switch ($activity) {
			case 'edit_email':
				$this->flash['element'] = 'success';
				$this->flash($this->changeEmail);
				break;
		}

		return Router::normalize($this->logoutRedirect);
	}
	public static function user($key = null) {
		if (!empty(self::$_user)) {
			$user = self::$_user;
		} elseif (self::$sessionKey && CakeSession::check(self::$sessionKey)) {
			$user = CakeSession::read(self::$sessionKey);
		} else {
			return null;
		}
		if ($key === null) {
			return $user;
		} else if($key === 'Auth'){
			return CakeSession::read('Auth');
		}
		return Hash::get($user, $key);
	}
	protected function _getUser() {
		$user = $this->user();
		if ($user) {
			$this->Session->delete('Auth.redirect');
			return true;
		}

		if (empty($this->_authenticateObjects)) {
			$this->constructAuthenticate();
		}
		foreach ($this->_authenticateObjects as $auth) {
			$result = $auth->getUser($this->request);
			if (!empty($result) && is_array($result)) {
				self::$_user = $result;
				return true;
			}
		}

		return false;
	}
	public function redirect( $url = null, $group_id = null ) {
		return $this->redirectUrl($url);
	}
	public function redirectUrl( $url = null, $group_id = null ) {
		if ($url !== null) {
			$redir = $url;
			$this->Session->write('Auth.redirect', $redir);
		} elseif ($this->Session->check('Auth.redirect')) {
			$redir = $this->Session->read('Auth.redirect');
			$this->Session->delete('Auth.redirect');
			
			if (Router::normalize($redir) == Router::normalize($this->loginAction)) {
				$redir = $this->loginRedirect;
			}
		} elseif ( $this->loginRedirect && !empty($group_id) && $group_id > 10 ) {
			$redir = $this->loginAdminRedirect;
		} elseif ($this->loginRedirect) {
			$redir = $this->loginRedirect;
		} else {
			$redir = '/';
		}
		if (is_array($redir)) {
			return Router::url($redir + array('base' => false));
		}
		return $redir;
	}

	function &getModel($name = null) {
		$model = null;
		if (!$name) {
			$name = $this->userModel;
		}
		
		$model = ClassRegistry::init($name);

		if (empty($model)) {
			trigger_error(__('Auth::getModel() - Model is not set or could not be found', true), E_USER_WARNING);
			return null;
		}

		return $model;
	}

	public function identify($data = false, $verify = false) {
		$model = $this->getModel();

		$pass_token = Common::_callPassToken($this->controller->params->params);
		$token = $this->RmCommon->filterEmptyField($data, 'User', 'token');
		$token = $this->RmCommon->filterEmptyField($data, 'UserConfig', 'token', $token);

		$password = $this->RmCommon->filterEmptyField($data, 'User', 'password');
		$prefix = Configure::read('App.prefix');

		if( !empty($data['User']['username']) ) {
			$username = $data['User']['username'];
		} else if( !empty($data['User']['email']) ) {
			$username = $data['User']['email'];
		} else {
			$username = false;
		}

		$msg = array(
			'status' => 'error',
			'errormessage' => __('Gagal melakukan login, username atau password Anda tidak valid'),
		);
		$parent_id = Configure::read('Principle.id');
		$user_by_token = array();

		if(!empty($username) && !empty($token)){
		//	note: login by token tapi cek token nya mana?????

			$user_by_token = $model->getData('first', array(
				'conditions' => array(
					array(
						'OR' => array(
							'User.username' => $username,
							'User.email' => $username,
						),
					),
					array(
						'OR' => array(
							array(
								'User.parent_id' => $parent_id,
							),
							array(
								'User.group_id' => array( 3, 4 ),
								'User.id' => $parent_id,
							),
							array(
								'User.group_id' => Configure::read('__Site.Admin.List.id'),
							),
							array(
								'User.group_id' => array(10),
							),
						),
					),
				),
			), array(
				'status' => 'semi-active',
			));

			if(!empty($user_by_token)){
				$user_by_token = $model->UserConfig->getMerge($user_by_token, $user_by_token['User']['id']);

				$verify = true;
				$password = $user_by_token['User']['password'];
			}
		}

		if( ( !empty($username) && (!empty($password) || !empty($token)) ) || !empty($pass_token) ) {
			if( !$verify && !empty($data['User']['password']) ){
				$password = $this->password($data['User']['password']);
			}

			if( $prefix == 'client' ) {
				$user = $model->UserClient->getData('first', array(
					'conditions' => array(
						'OR' => array(
							'UserClient.username' => $username,
							'User.email' => $username,
						),
						'UserClient.password' => $password,
						'UserClient.company_id' => $parent_id,
						'AND' => array(
							'OR' => array(
								'User.group_id' => array(10),
							),
						),
					),
					'contain' => array(
						'User',
					),
				));

				if( !empty($user) ) {
					$user['User']['UserClient'] = $user['UserClient'];
					unset($user['UserClient']);
				}
			} else {
				$is_rest = Configure::read('__Site.is_rest');

				if( !empty($pass_token) ) {
					$social_login	= Common::hashEmptyField($this->controller->params->named, 'social_login');
					$admin_id_list	= Configure::read('__Site.Admin.List.id');
					$options		= array(
						'contain'		=> array('UserConfig'),
						'conditions'	=> array(
							'UserConfig.token' => $pass_token,
						),
					);

					if($social_login){
					//	add parent
						$options['conditions']['or'] = array(
							array(
								'User.parent_id' => $parent_id,
							),
							array(
								'User.group_id' => array( 3, 4 ),
								'User.id'		=> $parent_id,
							),
							array(
								'User.group_id' => $admin_id_list, 
							),
						);
					}

					$user = $model->getData('first', $options, array(
						'status' => 'active', 
					));
				} else {
					if($is_rest){
						$or_conditions = array(
							array(
								'User.parent_id <>' => 0,
								'User.group_id <>' => 10,
							),
							array(
								'User.group_id' => Configure::read('__Site.Admin.List.id'),
							),
						);
					}else{
						$or_conditions = array(
							array(
								'OR' => array(
									array(
										'User.group_id' => array( 2, 5),
									),
									array(
										'User.group_id > ' => 20, 
									),
								),
								'User.parent_id' => $parent_id,
							),
							array(
								'User.group_id' => array( 3, 4 ),
								'User.id' => $parent_id,
							),
							array(
								'User.group_id' => Configure::read('__Site.Admin.List.id'),
							),
						);

					//	untuk personal web ==============================================================

						$isPrimeDomain = Common::isPrimeDomain();

						if($isPrimeDomain){
							$or_conditions = array_merge($or_conditions, array(
							 	array('User.group_id' => array(1, 2)), 
							));
						}

					//	=================================================================================
					}

					$user = $model->getData('first', array(
						'conditions' => array(
							'OR' => array(
								'User.username' => $username,
								'User.email' => $username,
							),
							'User.password' => $password,
							'AND' => array(
								'OR' => $or_conditions,
							),
						),
						'contain' => false,
					), array(
						'status' => 'active',
					));
				}
			}

			if( !empty($user) ) {
				$isValidClient = true;
				$parent_id = $this->RmCommon->filterEmptyField($user, 'User', 'parent_id');
				$group_id = $this->RmCommon->filterEmptyField($user, 'User', 'group_id');
				$user_id = $this->RmCommon->filterEmptyField($user, 'User', 'id');
				$username = $this->RmCommon->filterEmptyField($user, 'User', 'username');
				$email = $this->RmCommon->filterEmptyField($user, 'User', 'email' );
				$user_config_id = $this->RmCommon->filterEmptyField( $user_by_token, 'UserConfig', 'id' );

				if(!empty($user_by_token) && !empty($email)){
					if(empty($user_by_token['UserConfig']['token'])){
						$model->updateToken($user_id, $user_config_id, $token);
					}else{
						$data_api = $this->RumahkuApi->api_access($email, 'update_token');
						
						if(!empty($data_api)){
							$data_api = json_decode($data_api, true);
							$data_api = $data_api['data'];

							if(!empty($data_api['status']) && !empty($data_api['token'])){
								$model->updateToken($user_id, $user_config_id, $data_api['token']);
							}
						}
					}
				}

				if( $group_id == 10 ) {
					if( empty($user['User']['UserClient']) ) {
						$user['msg'] = $msg;
						$isValidClient = false;
					}
				}

				$device_id = $this->RmCommon->filterEmptyField($data, 'UserConfig', 'device_id');
				$device = $this->RmCommon->filterEmptyField($_POST, 'device');

				$user['msg']['status'] = 'success';
				$user = $model->UserConfig->getMerge($user, $user_id);

				$user_config_id = $this->RmCommon->filterEmptyField( $user, 'UserConfig', 'id' );
				$dataConfig = array(
					'UserConfig' => array(
						'last_login' => $this->RmCommon->currentDate(),
					),
				);

				/*terpaksa hardcode karena ini inisialisasi field*/
				$device_allow = Configure::read('__Site.Device.field');

				if(!empty($device) && isset($device_allow[$device]) && !empty($device_id) ){
					$dataConfig['UserConfig'][$device_allow[$device]] = $device_id;
				}

				if( empty($user_config_id) ) {
					$dataConfig['UserConfig']['user_id'] = $user_id;
					$dataConfig['UserConfig']['activation_code'] = $this->RmUser->_generateCode();
				}

				// set log activity
				$this->RmCommon->doLogView($user_id, $user, array(
					'slug' => 'login',
					'is_cookie' => false,
				));
				// 

				$model->UserConfig->doUpdateLastLogin($user_config_id, $dataConfig);

				// log activity
				$activity = __('Berhasil login dengan username : %s', $username ?: $email);
				$this->RmCommon->_saveLog($activity, $user, $user_id, 0, false, array(
					'user_id' => $user_id,
					'parent_id' => $parent_id,
					'group_id' => $group_id,
				));
			} else {
				$user['msg'] = $msg;
			}

			return $user;
		}

		return array(
			'msg' => $msg
		);
	}
	public function constructAuthenticate() {
		if (empty($this->authenticate)) {
			return;
		}
		$this->_authenticateObjects = array();
		$config = Hash::normalize((array)$this->authenticate);
		$global = array();
		if (isset($config[AuthComponent::ALL])) {
			$global = $config[AuthComponent::ALL];
			unset($config[AuthComponent::ALL]);
		}
		foreach ($config as $class => $settings) {
			list($plugin, $class) = pluginSplit($class, true);
			$className = $class . 'Authenticate';
			App::uses($className, $plugin . 'Controller/Component/Auth');
			if (!class_exists($className)) {
				throw new CakeException(__d('cake_dev', 'Authentication adapter "%s" was not found.', $class));
			}
			if (!method_exists($className, 'authenticate')) {
				throw new CakeException(__d('cake_dev', 'Authentication objects must implement an %s method.', 'authenticate()'));
			}
			$settings = array_merge($global, (array)$settings);
			$this->_authenticateObjects[] = new $className($this->_Collection, $settings);
		}
		return $this->_authenticateObjects;
	}
	public static function password($password) {
		return Security::hash($password, null, true);
	}
	public function loggedIn() {
		return (boolean)$this->user();
	}
	public function flash($message) {
		if ($message === false) {
			return;
		}
		$this->Session->setFlash($message, $this->flash['element'], $this->flash['params'], $this->flash['key']);
	}

	public function clear(){
		return $this->controller->Session->destroy() && $this->controller->Cookie->destroy();
	}
}
