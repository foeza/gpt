<?php
// buat hybrid auth
// session_start();
App::uses('Controller', 'Controller');
App::uses('Common', 'Utility');
App::uses('KprCommon', 'Utility');

class AppController extends Controller {
	var $uses = array(
		'User',
	);
	var $basicLabel = 'Basic';
	var $addressLabel = 'Address';
	var $assetLabel = 'Asset';
	var $mediaLabel = 'Medias';
	var $agentLabel = 'Agent';
	var $spesificationLabel = 'Spesification';
	var $templateLabel = 'Template';
	var $contentLabel = 'Content';
	var $confirmationLabel = 'Confirmation';

	public $components = array(
		'Acl', 'Auth', 'Session', 'Cookie', 
		'RequestHandler', 'RmCommon', 'RmUser',
		'Rest.Rest' => array(
			'debug' => 2,
			'auth' => array(
				// 'requireSecure' => true,
				'keyword' => 'PrimeAgent',
				'fields' => array(
					'apikey' => 'passkey',
				),
			),
			'meta' => array(
				'enable' => false
			),
			'ratelimit' => array(
				'enable' => false,
			)
		),
		'Maintenance.Maintenance' => array(
			'maintenanceUrl' => array(
				'controller' => 'pages',
				'action' => 'maintenance',
				'admin'=> false,
			),
			'allowedAction' => array('users' => array('*')

			),
			'allowedIp' => array('162.158.167.223, 10.10.11.18'), // allowed IP address when maintanance status
		), // allowed action when maintanance status
		'MobileDetect.MobileDetect', 
	);

	public $helpers = array(
		'Rumahku', 
		'Paginator', 
		'Minify.Minify', 
		'AclLink.AclLink',
	);

	public function beforeFilter(){
		$this->RmCommon->AllowOriginRequest();

		$_site_name  = 'GROSIR PASAR TASIK';
		$_site_email = 'gptsupport@yopmail.com';

		Configure::write('__Site.site_name', $_site_name);
		Configure::write('__Site.send_email_from', $_site_email);
		Configure::write('__Site.nabangshop', 'https://www.instagram.com/nabangshop/');

		$this->_base_url  = $_base_url = $this->RmCommon->manage_base_url();
		// $site_url_default = $this->RmCommon->checkRootDomain($_base_url);
		// debug($site_url_default);die();
		Configure::write('__Site.site_default', $_base_url);

		// untuk RmFileManager
		Configure::write('__Site.file_manager_path', 'files' . DS . 'recycle_bin' . DS);
		
		// Set is Ajax
		$this->is_ajax = $isAjax = $this->RequestHandler->isAjax();

		// Set Configure Variable KPR
		$this->RmCommon->configureKPR();

		// Set Configure Variable Doku dan Membership
		$this->RmCommon->configureMembership();

		// Set User Log-in
		$this->Auth->autoRedirect = false;
		
		$p_query = $this->params->query;
		$logged_group = false;
		$logged_in = false;
		$User = $this->Auth->user();

		// token login
		$User = $this->RmUser->tokenCheck($User, $this->params);

		// Get Data Company
		$this->data_company = $dataCompany = $_config = $this->RmUser->getDataCompanyFromApi($User,$_base_url,$p_query);
		$parent_id 			= Common::hashEmptyField($dataCompany, 'UserCompany.user_id');

		// Set Configure Global
		$this->RmCommon->_setConfigVariable($dataCompany);

		// set Variable Parent
		$this->parent_id = $parent_id;

		Configure::write('Principle.id', $parent_id);
		Configure::write('Config.Company.principle_id', $parent_id);
		Configure::write('Config.Company.is_personal_page', 0);

		// Set Variable Global
		$this->global_variable = $_global_variable = $this->RmCommon->_set_global_variable($_config);
		Configure::write('Global.Data', $_global_variable);

		// Prefix
		$prefix = $this->RmCommon->filterEmptyField($this->params, 'prefix');
		Configure::write('App.prefix', $prefix);
		Configure::write('App.Params.Action', $this->action);

		// validate for illegal access
		$this->RmCommon->_callValidateIllegalAccess();

		if( !empty($User) ) {
			$logged_in = true;
			$this->user_id = $this->RmCommon->filterEmptyField($User, 'id');
			$logged_group = $this->RmCommon->filterEmptyField($User, 'group_id');
			$rest = Configure::read('Rest.validate');

			if(empty($rest)){
				// set log activity
				$this->RmCommon->doLogView($this->user_id, $User, array(
					'slug' => 'daily',
					'cookie_time' => '1 DAY',
				));
				// 
			}

			// Set Data User & Principle to Global
			Configure::write('User.id', $this->user_id);
			Configure::write('User.group_id', $logged_group);

			// set dashboard based on logged in group
			$dashboardUrl = $this->RmCommon->_callDashboardUrl();
			
			$User = $this->User->getAllNeed($User, $this->user_id, $logged_group);
			Configure::write('User.data', $User);

			// Check Is Admin
			if( $this->RmCommon->_isAdmin() ) {
				Configure::write('User.Admin.Rumahku', true);
			}
			if($this->RmCommon->_isAdmin() || $this->RmCommon->_isCompanyAdmin()){
				Configure::write('User.companyAdmin', true);
			}
			if( $this->RmCommon->_isAdmin() || $this->RmCommon->_isCompanyAdmin() || configure::read('User.group_id') > 20) {
				Configure::write('User.admin', true);
			}

			if( $prefix == 'admin' && !$this->Rest->isActive() ) {
				$agent_company_id = $this->agent_id = $this->User->getAgents( $this->parent_id, true );

				$controller_name = $this->params->params['controller'];
				if($controller_name != 'messages' || ($controller_name == 'messages' && $this->action != 'admin_read')){
					$notificationMessages = $this->User->Message->getNotif();
				}
				
				$notifications = $this->User->Notification->getNotif();
			}
		}

		// set config
		$this->RmCommon->_setConfigData($dataCompany);

		// Set Layout - Base on Prefix
		$theme_path = $this->RmCommon->getThemePath();

		$this->RmCommon->_layout( $this->params, $theme_path );
		$this->RmCommon->_setTour();

		//	set list permissions, pengganti aclLinkHelper
		$authGroupID = $this->Auth->user('group_id');

		$this->RmUser->setPermission($authGroupID);

		$repeated_img = '/images/repeated-image.jpg';

		$this->set(compact(
			'User', 'logged_group', 'logged_in', 
			'_global_variable', '_site_name',
			'_site_email', 'dataCompany',
			'isAjax', 'notificationMessages',
			'_config', 'theme_path',
			'notifications', 'agent_company_id', 'repeated_img'
		));
	}

	public function admin_search ( $action, $addParam = false ) {
		$params = Common::_search($this, $action, $addParam);
		$this->redirect($params);
	}

	public function search ( $action, $addParam = false ) {
		$params = Common::_search($this, $action, $addParam);
		$this->redirect($params);
	}

	function isAuthorized($user) {
		return false;
		// return true;
	}
}