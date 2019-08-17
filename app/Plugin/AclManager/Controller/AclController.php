<?php
/**
 * Acl Manager
 *
 * A CakePHP Plugin to manage Acl
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @author        Frédéric Massart - FMCorz.net
 * @copyright     Copyright 2011, Frédéric Massart
 * @link          http://github.com/FMCorz/AclManager
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
class AclController extends AclManagerAppController {
	public $paginate = array();
	protected $_authorizer = null;
	protected $acos = array();

	public $components = array('RmCommon', 'RequestHandler');

	/**
	 * beforeFitler
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		
		/**
		 * Loading required Model
		 */
		$aros = Configure::read('AclManager.models');
		foreach ($aros as $aro) {
			$this->loadModel($aro);
		}
		
		/**
		 * Pagination
		 */
		$aros = Configure::read('AclManager.aros');
		foreach ($aros as $aro) {
			$limit = Configure::read("AclManager.{$aro}.limit");
			$limit = empty($limit) ? 4 : $limit;
			$this->paginate[$this->{$aro}->alias] = array(
				'recursive' => -1,
				'limit' => $limit
			);
		}

		$this->set('module_title', 'Access Control List');
		
		$this->Auth->allow(array(
			'group_permissions',
		));
	}

	/**
	 * Delete everything
	 */
	public function drop() {
		$this->Acl->Aco->deleteAll(array("1 = 1"));
		$this->Acl->Aro->deleteAll(array("1 = 1"));
		$this->Session->setFlash(__("Both ACOs and AROs have been dropped"), 'flash_success');
		$this->redirect(array("action" => "index"));
	}
	
	/**
	 * Delete all permissions
	 */
	public function drop_perms() {
		if ($this->Acl->Aro->Permission->deleteAll(array("1 = 1"))) {
			$this->Session->setFlash(__("Permissions dropped"), 'flash_success');
		} else {
			$this->Session->setFlash(__("Error while trying to drop permissions"), 'flash_error');
		}
		$this->redirect(array("action" => "index"));
	}

	/**
	 * Index action
	 */
	public function index() {
	}

	/**
	 * Manage Permissions
	 */
	public function permissions() {

		// Saving permissions
		if ($this->request->is('post') || $this->request->is('put')) {
			$perms =  isset($this->request->data['Perms']) ? $this->request->data['Perms'] : array();
			foreach ($perms as $aco => $aros) {
				$action = str_replace(":", "/", $aco);
				foreach ($aros as $node => $perm) {
					list($model, $id) = explode(':', $node);
					$node = array('model' => $model, 'foreign_key' => $id);
					if ($perm == 'allow') {
						$this->Acl->allow($node, $action);
					}
					elseif ($perm == 'inherit') {
						$this->Acl->inherit($node, $action);
					}
					elseif ($perm == 'deny') {
						$this->Acl->deny($node, $action);
					}
				}
			} 
		}
		
		$model = isset($this->request->params['named']['aro']) ? $this->request->params['named']['aro'] : null;
		if (!$model || !in_array($model, Configure::read('AclManager.aros'))) {
			$model = Configure::read('AclManager.aros');
			$model = $model[0];
		}

		$Aro = $this->{$model};

		$this->paginate = array(
			'conditions' => array(
				sprintf('%s.id', $Aro->alias) => array(1, 2, 3, 4, 5, 10, 11, 19, 20),
			),
			'limit' => Configure::read('AclManager.Group.limit')
		);
		$aros = $this->paginate($Aro->alias);
		$permKeys = $this->_getKeys();
		
		/**
		 * Build permissions info
		 */
		$this->acos = $acos = $this->Acl->Aco->find('all', array('order' => 'Aco.lft ASC', 'recursive' => 1));
		$perms = array();
		$parents = array();
		foreach ($acos as $key => $data) {
			$aco =& $acos[$key];
			$aco = array('Aco' => $data['Aco'], 'Aro' => $data['Aro'], 'Action' => array());
			$id = $aco['Aco']['id'];
			
			// Generate path
			if ($aco['Aco']['parent_id'] && isset($parents[$aco['Aco']['parent_id']])) {
				$parents[$id] = $parents[$aco['Aco']['parent_id']] . '/' . $aco['Aco']['alias'];
			} else {
				$parents[$id] = $aco['Aco']['alias'];
			}
			$aco['Action'] = $parents[$id];

			// Fetching permissions per ARO
			$acoNode = $aco['Action'];

			foreach($aros as $aro) {
				$aroId = $aro[$Aro->alias][$Aro->primaryKey];
				$evaluate = $this->_evaluate_permissions($permKeys, array('id' => $aroId, 'alias' => $Aro->alias), $aco, $key);
				
				$perms[str_replace('/', ':', $acoNode)][$Aro->alias . ":" . $aroId . '-inherit'] = $evaluate['inherited'];
				$perms[str_replace('/', ':', $acoNode)][$Aro->alias . ":" . $aroId] = $evaluate['allowed'];
			}
		}


		$this->request->data = array('Perms' => $perms);

		$this->set('aroAlias', $Aro->alias);
		$this->set('aroDisplayField', $Aro->displayField);
		$this->set(compact('acos', 'aros'));
	}

	public function group_permissions($group_id = null) {
		$params = $this->params->params;
		$user_id = Common::hashEmptyfield($params, 'named.user_id');
		$user = $this->RmUser->getUser($user_id);
		$recordID = Common::HashEmptyField($user, 'User.id');

		$principle = $this->data_company;
		$principle_id = Common::HashEmptyField($principle, 'User.id');
		$principle_group_id = Common::HashEmptyField($principle, 'User.group_id');
		
		if($principle_id <> $recordID){
			$principle_group_id = Common::HashEmptyField($user, 'User.group_id');
		}

		if(!empty($user)){
			$elements['status'] = false;

			if(empty($recordID)){
				$elements['company'] = true;
			}

			$group = $this->Group->getData('first', array(
				'conditions' => array(
					'Group.id' => $group_id,
					'Group.user_id' => $recordID,
				)
			), $elements);

			if( !empty($group) ) {
				$model = isset($this->request->params['named']['aro']) ? $this->request->params['named']['aro'] : null;
				if (!$model || !in_array($model, Configure::read('AclManager.aros'))) {
					$model = Configure::read('AclManager.aros');
					$model = $model[0];
				}

				$Aro = $this->{$model};
				$is_super_admin = Configure::read('User.Admin.Rumahku');

				$conditions = array(
					sprintf('%s.id', $Aro->alias) => $group_id
				);
				$aros = $this->{$Aro->alias}->find('all', array(
					'conditions' => $conditions
				));

				$permKeys = $this->_getKeys();
				
				/**
				 * Build permissions info
				 */
				$this->Acl->Aco->bindModel(array(
		            'belongsTo' => array(
						'AcoParent' => array(
							'className' => 'Aco',
							'foreignKey' => 'parent_id',
						),
		            )
		        ), false);

				$options = array(
					'conditions' => array(
						'OR' => array(
							'Aco.alias NOT' => NULL,
							'AcoParent.alias NOT' => NULL,
						),
						'OR' => array(
							array(
								'Aco.accessible' => NULL,
							),
							array(
								'Aco.accessible' => $principle_group_id,
							),
						),
						'Aco.label NOT' => NULL,
					),
					'contain' => array(
						'AcoParent' => array(
							'conditions' => array(
								'OR' => array(
									array(
										'AcoParent.accessible' => NULL,
									),
									array(
										'AcoParent.accessible' => $principle_group_id,
									),
								),
							),
						),
					),
					'order' => array(
						'Aco.lft' => 'ASC',
						'Aco.label' => 'ASC',
						'Aco.order' => 'ASC',
					), 
					'recursive' => 1,
				);

				$this->acos = $acos = $this->Acl->Aco->find('all', $options);
				$perms = array();
				$parents = array();

				foreach ($acos as $key => $data) {
					$aco =& $acos[$key];
					$aco = array('Aco' => $data['Aco'], 'Aro' => $data['Aro'], 'Action' => array());
					$id = $aco['Aco']['id'];
					$label = $aco['Aco']['label'];
					
					// Generate path
					if ($aco['Aco']['parent_id'] && isset($parents[$aco['Aco']['parent_id']])) {
						$parents[$id] = $parents[$aco['Aco']['parent_id']] . '/' . $aco['Aco']['alias'];
					} else {
						$parents[$id] = $aco['Aco']['alias'];
					}
					$Action = $aco['Action'] = $parents[$id];

					if( empty($label) ){
						unset($acos[$key]);
					}else{
						// Fetching permissions per ARO
						$acoNode = $aco['Action'];
						foreach($aros as $aro) {
							$aroId = $aro[$Aro->alias][$Aro->primaryKey];
							$evaluate = $this->_evaluate_permissions($permKeys, array('id' => $aroId, 'alias' => $Aro->alias), $aco, $key);
							
							$perms[str_replace('/', ':', $acoNode)][$Aro->alias . ":" . $aroId . '-inherit'] = $evaluate['inherited'];
							$perms[str_replace('/', ':', $acoNode)][$Aro->alias . ":" . $aroId] = $evaluate['allowed'];
						}
					}
				}
				
				$childs = array();
				
				if(!empty($acos)){
					$temp = array();
					$parent_node = false;
					$tempAcos = $acos;

					foreach ($acos as $key => $val) {
						$action = Common::hashEmptyfield($val, 'Action');
						$id = Common::hashEmptyfield($val, 'Aco.id');
						$parent_id = Common::hashEmptyfield($val, 'Aco.parent_id');
						$ident = substr_count($action, '/');
						
						if($ident != 1){
							unset($tempAcos[$key]);
							$childs[$parent_id][] = $val;
						}
					}

					$acos = $tempAcos;
				}

				$group_name = Common::hashEmptyfield($group, 'Group.name');
				$this->request->data = array('Perms' => $perms);
				$this->set('aroAlias', $Aro->alias);
				$this->set('aroDisplayField', $Aro->displayField);
				$this->set(compact('acos', 'aros', 'group', 'childs'));

				$title = __('Kelola Akses - %s', $group_name);

				$active_menu = Common::hashEmptyField($user, 'Group.name', false, array(
					'type' => 'strtolower',
				));
				$active_menu = $this->RmUser->getActive($active_menu, 'division');

				$this->set(array(
					'module_title' => $title,
					'title_for_layout' => $title,
					'group_id' => $group_id,
					'group_name' => $group_name,
					'active_menu' => $active_menu,
					'recordID' => $recordID,
					'self' => empty($user_id) ? true : false,
				));
			} else {
				$this->RmCommon->redirectReferer(__('Group tidak ditemukan'));
			}
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}
	
	/**
	 * Recursive function to find permissions avoiding slow $this->Acl->check().
	 */
	private function _evaluate_permissions($permKeys, $aro, $aco, $aco_index) { 
		$permissions = Set::extract("/Aro[model={$aro['alias']}][foreign_key={$aro['id']}]/Permission/.", $aco);
		$permissions = array_shift($permissions);	

		$allowed = false;
		$inherited = false;
		$inheritedPerms = array();
		$allowedPerms = array();
		
		/**
		 * Manually checking permission
		 * Part of this logic comes from DbAcl::check()
		 */
		foreach ($permKeys as $key) {
			if (!empty($permissions)) {
				if ($permissions[$key] == -1) {
					$allowed = false;
					break;
				} elseif ($permissions[$key] == 1) {
					$allowedPerms[$key] = 1;
				} elseif ($permissions[$key] == 0) {
					$inheritedPerms[$key] = 0;
				}
			} else {
				$inheritedPerms[$key] = 0;
			}
		}
		
		if (count($allowedPerms) === count($permKeys)) {
			$allowed = true;
		} elseif (count($inheritedPerms) === count($permKeys)) {
			if ($aco['Aco']['parent_id'] == null) {
				$this->lookup +=1;
				$acoNode = (isset($aco['Action'])) ? $aco['Action'] : null;
				$aroNode = array('model' => $aro['alias'], 'foreign_key' => $aro['id']);
				$allowed = $this->Acl->check($aroNode, $acoNode);
				$this->acos[$aco_index]['evaluated'][$aro['id']] = array(
					'allowed' => $allowed,
					'inherited' => true
				);
			}
			else {
				/**
				 * Do not use Set::extract here. First of all it is terribly slow, 
				 * besides this we need the aco array index ($key) to cache are result.
				 */
				$parent_aco = null;
				foreach ($this->acos as $key => $a) {
					if ($a['Aco']['id'] == $aco['Aco']['parent_id']) {
						$parent_aco = $a;
						break;
					}
				}
				// Return cached result if present
				if (isset($parent_aco['evaluated'][$aro['id']])) {
					return $parent_aco['evaluated'][$aro['id']];
				}
				
				// Perform lookup of parent aco
				$evaluate = $this->_evaluate_permissions($permKeys, $aro, $parent_aco, $key);
				
				// Store result in acos array so we need less recursion for the next lookup
				$this->acos[$key]['evaluated'][$aro['id']] = $evaluate;
				$this->acos[$key]['evaluated'][$aro['id']]['inherited'] = true;
				
				$allowed = $evaluate['allowed'];
			}
			$inherited = true;
		}
		
		return array(
			'allowed' => $allowed,
			'inherited' => $inherited,
		);
	}

	/**
	 * Update ACOs
	 * Sets the missing actions in the database
	 */
	public function update_acos() {
		
		$count = 0;
		$knownAcos = $this->_getAcos();
		
		// Root node
		$aco = $this->_action(array(), '');
		if (!$rootNode = $this->Acl->Aco->node($aco)) {
			$rootNode = $this->_buildAcoNode($aco, null);
			$count++;
		}
		$knownAcos = $this->_removeActionFromAcos($knownAcos, $aco);
		
		// Loop around each controller and its actions
		$allActions = $this->_getActions();
		foreach ($allActions as $controller => $actions) {
			if (empty($actions)) {
				continue;
			}
			
			$parentNode = $rootNode;
			list($plugin, $controller) = pluginSplit($controller);
			
			// Plugin
			$aco = $this->_action(array('plugin' => $plugin), '/:plugin/');
			$aco = rtrim($aco, '/');		// Remove trailing slash
			$newNode = $parentNode;
			if ($plugin && !$newNode = $this->Acl->Aco->node($aco)) {
				$newNode = $this->_buildAcoNode($plugin, $parentNode);
				$count++;
			}
			$parentNode = $newNode;
			$knownAcos = $this->_removeActionFromAcos($knownAcos, $aco);
			
			// Controller
			$aco = $this->_action(array('controller' => $controller, 'plugin' => $plugin), '/:plugin/:controller');
			if (!$newNode = $this->Acl->Aco->node($aco)) {
				$newNode = $this->_buildAcoNode($controller, $parentNode);
				$count++;
			}
			$parentNode = $newNode;
			$knownAcos = $this->_removeActionFromAcos($knownAcos, $aco);

			// Actions
			foreach ($actions as $action) {
				$aco = $this->_action(array(
					'controller' => $controller,
					'action' => $action,
					'plugin' => $plugin
				));
				if (!$node = $this->Acl->Aco->node($aco)) {
					$this->_buildAcoNode($action, $parentNode);
					$count++;
				}
				$knownAcos = $this->_removeActionFromAcos($knownAcos, $aco);
			}
		}

		// Some ACOs are in the database but not in the controllers
		if (count($knownAcos) > 0) {
			$acoIds = Set::extract('/Aco/id', $knownAcos);
			$this->Acl->Aco->deleteAll(array('Aco.id' => $acoIds));
		}
		
		$this->Session->setFlash(sprintf(__("%d ACOs have been created/updated"), $count), 'flash_success');
		$this->redirect($this->request->referer());
	}

	/**
	 * Update AROs
	 * Sets the missing AROs in the database
	 */
	public function update_aros() {
	
		// Debug off to enable redirect
		Configure::write('debug', 0);
		
		$count = 0;
		$type = 'Aro';
			
		// Over each ARO Model
		$objects = Configure::read("AclManager.aros");
		foreach ($objects as $object) {
			
			$Model = $this->{$object};

			$items = $Model->find('all');
			foreach ($items as $item) {
	
				$item = $item[$Model->alias];
				$Model->create();
				$Model->id = $item['id'];

				try {
					$node = $Model->node();
				} catch (Exception $e) {
					$node = false;
				}
				
				// Node exists
				if ($node) {
					$parent = $Model->parentNode();
					if (!empty($parent)) {
						$parent = $Model->node($parent, $type);
					}
					$parent = isset($parent[0][$type]['id']) ? $parent[0][$type]['id'] : null;
					
					// Parent is incorrect
					if ($parent != $node[0][$type]['parent_id']) {
						// Remove Aro here, otherwise we've got duplicate Aros
						// TODO: perhaps it would be nice to update the Aro with the correct parent
						$this->Acl->Aro->delete($node[0][$type]['id']);
						$node = null;
					}
				}
				
				// Missing Node or incorrect
				if (empty($node)) {
					
					// Extracted from AclBehavior::afterSave (and adapted)
					$parent = $Model->parentNode();
					if (!empty($parent)) {
						$parent = $Model->node($parent, $type);
					}
					$data = array(
						'parent_id' => isset($parent[0][$type]['id']) ? $parent[0][$type]['id'] : null,
						'model' => $Model->name,
						'foreign_key' => $Model->id
					);
					
					// Creating ARO
					$this->Acl->{$type}->create($data);
					$this->Acl->{$type}->save();
					$count++;
				}
			}
		}
		
		$this->Session->setFlash(sprintf(__("%d AROs have been created"), $count), 'flash_success');
		$this->redirect($this->request->referer());
	}

	/**
	 * Gets the action from Authorizer
	 */
	protected function _action($request = array(), $path = '/:plugin/:controller/:action') {
		$plugin = empty($request['plugin']) ? null : Inflector::camelize($request['plugin']) . '/';
		$params = array_merge(array('controller' => null, 'action' => null, 'plugin' => null), $request);
		$request = new CakeRequest(null, false);
		$request->addParams($params);	
		$authorizer = $this->_getAuthorizer();
		return $authorizer->action($request, $path);
	}

	/**
	 * Build ACO node
	 *
	 * @return node
	 */
	protected function _buildAcoNode($alias, $parent_id = null) {
		if (is_array($parent_id)) {
			$parent_id = $parent_id[0]['Aco']['id'];
		}
		$this->Acl->Aco->create(array('alias' => $alias, 'parent_id' => $parent_id));
		$this->Acl->Aco->save();
		return array(array('Aco' => array('id' => $this->Acl->Aco->id)));
	}

	/**
	 * Returns all the Actions found in the Controllers
	 * 
	 * Ignores:
	 * - protected and private methods (starting with _)
	 * - Controller methods
	 * - methods matching Configure::read('AclManager.ignoreActions')
	 * 
	 * @return array('Controller' => array('action1', 'action2', ... ))
	 */
	protected function _getActions() {
		$ignore = Configure::read('AclManager.ignoreActions');
		$methods = get_class_methods('Controller');
		foreach($methods as $method) {
			$ignore[] = $method;
		}
		
		$controllers = $this->_getControllers();
		$actions = array();
		foreach ($controllers as $controller) {
		    
		    list($plugin, $name) = pluginSplit($controller);
			
		    $methods = get_class_methods($name . "Controller");
			$methods = array_diff($methods, $ignore);
			foreach ($methods as $key => $method) {
				if (strpos($method, "_") === 0 || in_array($controller . '/' . $method, $ignore)) {
					unset($methods[$key]);
				}
			}
			$actions[$controller] = $methods;
		}
		
		return $actions;
	}

	/**
	 * Returns all the ACOs including their path
	 */
	protected function _getAcos() {
		$acos = $this->Acl->Aco->find('all', array('order' => 'Aco.lft ASC', 'recursive' => -1));
		$parents = array();
		foreach ($acos as $key => $data) {
			
			$aco =& $acos[$key];
			$id = $aco['Aco']['id'];
			
			// Generate path
			if ($aco['Aco']['parent_id'] && isset($parents[$aco['Aco']['parent_id']])) {
				$parents[$id] = $parents[$aco['Aco']['parent_id']] . '/' . $aco['Aco']['alias'];
			} else {
				$parents[$id] = $aco['Aco']['alias'];
			}
			$aco['Aco']['action'] = $parents[$id];
		}
		return $acos;
	}

	/**
	 * Gets the Authorizer object from Auth
	 */
	protected function _getAuthorizer() {
		if (!is_null($this->_authorizer)) {
			return $this->_authorizer;
		}
		$authorzeObjects = $this->Auth->_authorizeObjects;
		foreach ($authorzeObjects as $object) {
			if (!$object instanceOf ActionsAuthorize) {
				continue;
			}
			$this->_authorizer = $object; 
			break;
		}
		if (empty($this->_authorizer)) {
			$this->Session->setFlash(__("ActionAuthorizer could not be found"), 'flash_error');
			$this->redirect($this->referer());
		}
		return $this->_authorizer;
	}

	/**
	 * Returns all the controllers from Cake and Plugins
	 * Will only browse loaded plugins
	 *
	 * @return array('Controller1', 'Plugin.Controller2')
	 */
	protected function _getControllers() {
		
		// Getting Cake controllers
		$objects = array('Cake' => array());
		$objects['Cake'] = App::objects('Controller');
		$unsetIndex = array_search("AppController", $objects['Cake']);
		if ($unsetIndex !== false) {
			unset($objects['Cake'][$unsetIndex]);
		}
		
		// App::objects does not return PagesController
		if (!in_array('PagesController', $objects['Cake'])) {
		    array_unshift($objects['Cake'], 'PagesController');
		}
		
		// Getting Plugins controllers
		$plugins = CakePlugin::loaded();
		foreach ($plugins as $plugin) {
			$objects[$plugin] = App::objects($plugin . '.Controller');
			$unsetIndex = array_search($plugin . "AppController", $objects[$plugin]);
			if ($unsetIndex !== false) {
				unset($objects[$plugin][$unsetIndex]);
			}
		}

		// Around each controller
		$return = array();
		foreach ($objects as $plugin => $controllers) {
			$controllers = str_replace("Controller", "", $controllers);
			foreach ($controllers as $controller) {
				if ($plugin !== "Cake") {
					$controller = $plugin . "." . $controller;
				}
				if (App::import('Controller', $controller)) {
					$return[] = $controller;
				}
			}
		}

		return $return;
	}

	/**
	 * Returns permissions keys in Permission schema
	 * @see DbAcl::_getKeys()
	 */
	protected function _getKeys() {
		$keys = $this->Acl->Aro->Permission->schema();
		$newKeys = array();
		$keys = array_keys($keys);
		foreach ($keys as $key) {
			if (!in_array($key, array('id', 'aro_id', 'aco_id'))) {
				$newKeys[] = $key;
			}
		}
		return $newKeys;
	}
	
	/**
	 * Returns an array without the corresponding action
	 */
	protected function _removeActionFromAcos($acos, $action) {
		foreach ($acos as $key => $aco) {
			if ($aco['Aco']['action'] == $action) {
				unset($acos[$key]);
				break;
			}
		}
		return $acos;
	}

	public function manage(){
		$isAjax = $this->RequestHandler->isAjax();

		if($isAjax){
			if($this->request->data){
				$perms		= Hash::get($this->request->data, 'Perms', array());
				$groupList	= array();

				foreach($perms as $aco => $aros){
					$action = str_replace(':', '/', $aco);

					foreach($aros as $node => $perm){
						list($model, $id) = explode(':', $node);

						$node			= array('model' => $model, 'foreign_key' => $id);
						$groupList[]	= $id;

						if($perm == 'allow'){
							$this->Acl->allow($node, $action);
						}
						else if($perm == 'inherit'){
							$this->Acl->inherit($node, $action);
						}
						else if($perm == 'deny'){
							$this->Acl->deny($node, $action);
						}
					}
				}

				foreach($groupList as $groupID){
				//	delete acl cache
					$cacheConfig	= 'permission';
					$cacheName		= sprintf('Permission.%s', $groupID);

					Cache::delete($cacheName, $cacheConfig);
				}

				$this->autoRender = false;

				return json_encode(array(
					'status'	=> 'success', 
					'msg'		=> __('Berhasil menyimpan data'), 
				));
			}

			$this->layout = false;
		}

		$model		= Hash::get($this->request->named, 'aro');
		$aroModels	= (array) Configure::read('AclManager.aros');

		if(empty($model) || ($aroModels && in_array($model, $aroModels) === false)){
			$model = Hash::get($aroModels, 0);
		}

		if(empty($this->$model)){
			$this->$model = ClassRegistry::init($model);
		}

		$limit = 3;//Configure::read('AclManager.Group.limit');

		$this->paginate = array(
			'limit'			=> $limit, 
			'conditions'	=> array(
				sprintf('%s.id', $this->$model->alias) => array(1, 2, 3, 4, 5, 10, 11, 19, 20),
			),
		);

	//	get group list
		$aros = $this->paginate($this->$model->alias);

	//	get permission field list
		$permissionFields = $this->_getKeys();

		$acoID		= Hash::get($this->params->named, 'acoid');
		$options	= array(
			'recursive'	=> 1, 
			'contain'	=> array('AcoChild'), 
			'order'		=> array(
			//	'Aco.lft', 
				'Aco.alias', 
			), 
		);

		$this->Acl->Aco->bindModel(array(
			'hasMany' => array(
				'AcoChild' => array(
					'className'		=> 'Aco', 
					'foreignKey'	=> 'parent_id', 
					'conditions'	=> array('AcoChild.parent_id !=' => 1), 
					'fields'		=> array('AcoChild.id', 'AcoChild.alias'), 
				), 
			), 
		), false);

		if($acoID){
			$rowClass	= 'acl-method-row';
			$options	= Hash::insert($options, 'conditions', array(
				'Aco.parent_id' => $acoID, 
			));
		}
		else{
			$rowClass	= 'acl-controller-row';
			$options	= Hash::insert($options, 'conditions', array(
			//	controller bisa langsung allow semua jadi mening di umpetin
				'Aco.alias !=' => 'controllers', 
				'or' => array(
					array('Aco.parent_id' => null), 
					array('Aco.parent_id' => 1), 
				), 
			));
		}

		$this->acos = $acos = $this->Acl->Aco->find('all', $options);
		$perms		= array();
		$parents	= array();

		if($acos){
		//	B:GET PARENTS LIST =================================================================================================

			$mainParent = $this->Acl->Aco->find('first', array(
				'conditions' => array(
					'Aco.alias' => 'controllers', 
				), 
			));

			$mainParentID		= Common::hashEmptyfield($mainParent, 'Aco.id');
			$mainParentAlias	= Common::hashEmptyfield($mainParent, 'Aco.alias');

		//	insert parent list (root aco)
			$parents[$mainParentID]	= $mainParentAlias;

			if($acoID){
				$controllerData		= $this->Acl->Aco->find('first', array('conditions' => array('Aco.id' => $acoID)));
				$controllerID		= Common::hashEmptyfield($controllerData, 'Aco.id');
				$controllerAlias	= Common::hashEmptyfield($controllerData, 'Aco.alias');
				$controllerParentID	= Common::hashEmptyfield($controllerData, 'Aco.parent_id');

				$isInsidePlugin	= $controllerParentID != $mainParentID;

				if($isInsidePlugin){
				//	plugin
					$pluginData		= $this->Acl->Aco->find('first', array('conditions' => array('Aco.id' => $controllerParentID)));
					$pluginID		= Common::hashEmptyfield($pluginData, 'Aco.id');
					$pluginAlias	= Common::hashEmptyfield($pluginData, 'Aco.alias');

					$parents[$pluginID]		= $mainParentAlias . '/' . $pluginAlias;
					$parents[$controllerID]	= $mainParentAlias . '/' . $pluginAlias . '/' . $controllerAlias;
				}
				else{
				//	controller
					$parents[$controllerID]	= $mainParentAlias . '/' . $controllerAlias;
				}
			}

		//	E:GET PARENTS LIST =================================================================================================
		}

		foreach($acos as $key => $data){
			$acoChild	= Hash::get($data, 'AcoChild');
			$data		= Hash::insert($data, 'Aco.child_count', count($acoChild));

			$aco =& $acos[$key];
			$aco = array(
				'Aco'		=> $data['Aco'], 
				'Aro'		=> $data['Aro'], 
				'Action'	=> array(), 
			);

		//	generate path
			$acoID		= Hash::get($aco, 'Aco.id');
			$acoAlias	= Hash::get($aco, 'Aco.alias');
			$parentID	= Hash::get($aco, 'Aco.parent_id');

			if($parentID && isset($parents[$parentID])){
				$parents[$acoID] = $parents[$parentID] . '/' . $acoAlias;
			}
			else{
				$parents[$acoID] = $acoAlias;
			}

			$aco['Action'] = $parents[$acoID];

		//	fetching permissions per aro
			$acoNode = $aco['Action'];

			foreach($aros as $aro){
				$aroId		= $aro[$this->$model->alias][$this->$model->primaryKey];
				$evaluate	= $this->_evaluate_permissions($permissionFields, array('id' => $aroId, 'alias' => $this->$model->alias), $aco, $key);
				
				$perms[str_replace('/', ':', $acoNode)][$this->$model->alias . ":" . $aroId . '-inherit'] = $evaluate['inherited'];
				$perms[str_replace('/', ':', $acoNode)][$this->$model->alias . ":" . $aroId] = $evaluate['allowed'];
			}
		}

	//	set default input value
		$this->request->data = array('Perms' => $perms);

		$this->RmCommon->_layout_file(array('acl'));
		$this->set(array(
			'aroAlias'			=> $this->$model->alias, 
			'aroDisplayField'	=> $this->$model->displayField, 
			'aros'				=> $aros, 
			'acos'				=> $acos, 
			'rowClass'			=> $rowClass, 
		));
	}
}