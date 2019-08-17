<?php
App::uses('AppController', 'Controller');
class GroupsController extends AppController {
	public $components = array('RmGroup', 'RmUser');

	function beforeFilter() {
		parent::beforeFilter();

		$this->set(array(
			'active_menu' => 'groups',
		));
	}

	function admin_search ( $action, $_admin = true, $addParam = false ) {
		$data = $this->request->data;
		$named = $this->RmCommon->filterEmptyField($this->params, 'named');
		$params = array(
			'action' => $action,
			$addParam,
			'admin' => $_admin,
		);

		if( !empty($named) ) {
			$params = array_merge($params, $named);
		}
		
		$this->RmCommon->processSorting($params, $data);
	}

	public function admin_index($id = false) {
		$auth_group_id = configure::read('User.group_id');

		$elements = array();
		$params = $this->params->params;
		$cookie = Common::hashEmptyField($params, 'named.cookie');

		$logged = Configure::read('User.data');

		$user_id = !empty($id) ? $id : Common::hashEmptyField($logged, 'id');

		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id,
			),
		));

		if($cookie){
			$this->RmCommon->setCookieUser($user);
		}

		$group_id = Common::hashEmptyField($user, 'User.group_id');
		$parent_id = in_array($group_id, array(3, 4)) ? Common::hashEmptyField($user, 'User.id') : Common::hashEmptyField($user, 'User.parent_id');

		$principle = $this->RmUser->getUser($parent_id);
		$recordID = Common::hashEmptyField($principle, 'User.id');

		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id,
			),
		));

		if(!empty($value)){
			$value = $this->User->getMergeList($value, array(
				'contain' => array(
					'Group',
				),
			));

			$groupName = Common::hashEmptyField($value, 'Group.name', false, array(
				'type' => 'strtolower',
			));
			$this->Group->virtualFields['order_admin'] =  'CASE WHEN Group.user_id = 0 THEN 1 ELSE 0 END';

			$conditions = $this->Group->getDivisionCompany(array(
				'userID' => $recordID,
				'slug' => $groupName,
				'type' => 'paginate',
			));

			$this->RmCommon->_callRefineParams($params);
			$options =  $this->Group->_callRefineParams($params, array(
				'conditions' => $conditions,
				'order' => array(
					'Group.order_admin' => 'DESC',
					'Group.name' => 'ASC',
				),
				'group' => array(
					'Group.id',
				),
			));
			$this->paginate = $this->Group->getData('paginate', $options);

			$values = $this->paginate('Group');

			$values = $this->Group->getMergeList($values, array(
				'contain' => array(
					'User' => array(
						'contain' => array(
							'UserCompany',
						),
					),
					'GroupCompany' => array(
						'type' => 'first',
						'conditions' => array(
							'GroupCompany.user_id' => $recordID,
						),
						'contain' => array(
							'Group' => array(
								'uses' => 'Group',
								'foreignKey' => 'parent_id',
								'primaryKey' => 'id',
							),
						),
					),
				)
			));
			
			$title = __('Divisi');

			$active_menu = $this->RmUser->getActive($groupName, 'division', $id);

			$this->set(array(
				// 'tab' => (!empty($id)) ? true : false,
				'currUser' => $value,
				'values' => $values,
				'module_title' => $title,
				'title_for_layout' => $title,
				'recordID' => $user_id,
				'active_tab' => !empty($parent_id)?'division':'Divisi',
				'active_menu' => $active_menu,
				'getCookieId' => $this->RmCommon->getCookieUser(),
			));

			if(empty($id)){
				$this->set(array(
					'active_menu' => 'division',
					'self' => true,
				));
			}
			
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

/**
 * add method
 *
 * @return void
 */
	public function admin_add() {
		$title = __('Tambah Divisi');
		$user_id  = Common::hashEmptyField($this->params->params, 'named.user_id');
		$value = $this->RmUser->getUser($user_id);
		$recordID = Common::hashEmptyField($value, 'User.id');

		if(!empty($value)){
			$recordID = Common::hashEmptyField($value, 'User.id');
			$active_menu = Common::hashEmptyField($value, 'Group.name', false, array(
				'type' => 'strtolower',
			));
			$active_menu = $this->RmUser->getActive($active_menu, 'division');

			$this->RmGroup->_callBeforeSave(false, false, $value);

			$this->set(array(
				'recordID' => $recordID,
				'module_title' => $title,
				'title_for_layout' => $title,
				'active_menu' => $active_menu,
			));

			if(empty($user_id)){
				$this->set(array(
					'self' => true,
				));
			}

		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}

	}

	public function admin_edit($id = null){
		$user_id  = Common::hashEmptyField($this->params->params, 'named.user_id');
		$user = $this->RmUser->getUser($user_id);
		$recordID = Common::hashEmptyField($user, 'User.id');

		if(!empty($user)){
			$active_menu = Common::hashEmptyField($user, 'Group.name', false, array(
				'type' => 'strtolower',
			));
			$active_menu = $this->RmUser->getActive($active_menu, 'division');

			$value = $this->Group->getData('first', array(
				'conditions' => array(
					'Group.id' => $id,
					'OR' => array(
						array(
							'Group.id >' => 20,
							'Group.user_id' => $recordID,
						),
						array(
							'Group.id' => array('2', '5'),
							'Group.user_id' => false,
							'Group.is_prime' => true,
						),
					),
				),
			));

			if(!empty($value)){
				$title = __('Edit Divisi');

				$this->RmGroup->_callBeforeSave($value, $id, $user);

				$this->set(array(
					'active_menu' => $active_menu,
					'recordID' => $recordID,
					'module_title' => $title,
					'title_for_layout' => $title,
					'self' => empty($user_id) ? true : false,  
				));
				$this->render('admin_add');
			} else {
				$this->RmCommon->redirectReferer(__('Divisi tidak ditemukan'));
			}
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}


	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	// public function admin_edit($id = null) {
	// 	if (!$this->Group->exists($id)) {
	// 		throw new NotFoundException(__('Invalid Divisi'));
	// 	}else{
	// 		$parent = $this->RmUser->_callCheckParent('value');
	// 		$parent_id = Common::hashEmptyField($parent, 'Company.id');
	// 		$is_super_admin = Configure::read('__Site.is_super_admin');

	// 		if( ( !empty($parent_id) && !empty($is_super_admin) ) || empty($is_super_admin) ) {
	// 			$value = $this->Group->getData('first', array(
	// 				'conditions' => array(
	// 					'Group.id' => $id,
	// 					// 'Group.company_id <>' => 0,
	// 				)
	// 			), array(
	// 				'company' => !empty($parent_id)?$parent_id:false,
	// 				'status' => false,
	// 			));

	// 			if( !empty($value) ) {
	// 				$this->RmGroup->_callBeforeSave($parent_id, $value, $id);

	// 				$title = __('Edit Divisi');
	// 				$this->RmGroup->_callGroupBeforeView($parent, __('Edit'));

	// 				$this->set(array(
	// 					'id' => $id,
	// 					'module_title' => $title,
	// 					'title_for_layout' => $title,
	// 				));
						
	// 				$this->render('admin_form');
	// 			} else {
	// 				$this->RmCommon->redirectReferer(__('Divisi tidak ditemukan'));
	// 			}
	// 		} else {
	// 			$this->RmCommon->redirectReferer(__('Divisi tidak ditemukan'));
	// 		}
	// 	}
	// }

/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Group', 'id', $id);

    	$result = $this->Group->doDelete($id, array(
			'status' => 'all',
		));
		
		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
			'redirectError' => true,
		));
	}

	public function admin_toggles( $active = false ) {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Search', 'id');

		$parent_id = $this->RmUser->_callCheckParent();
    	$result = $this->Group->doToggle($id, $active, array(
			'company' => !empty($parent_id)?$parent_id:false,
			'status' => false,
		));
		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
			'redirectError' => true,
		));
	}

	public function admin_toggle( $id = null ) {
		$value = $this->Group->getData('first', array(
			'conditions' => array(
				'Group.id' => $id,
				'Group.id <>' => array( '2', '3', '5'),
			),
		), array(
			'status' => 'all',
		));

		if ( !empty($value) ) {
			$active = Common::hashEmptyField($value, 'Group.active');
	    	$result = $this->Group->doToggle( $id, !$active );
			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
				'redirectError' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Divisi tidak ditemukan'));
		}
	}

	/**
 * otorisasi method
 *
 * @return void
 */
	// function otorisasi($group_id) {
	// 	if (!$this->Group->exists($group_id)) {
	// 		throw new NotFoundException(__('Invalid Divisi'));
	// 	}

	// 	$aros = $this->Acl->Aro->find('first', array(
	// 		'conditions' => array(
	// 			'Aro.model' => 'Group',
	// 			'Aro.foreign_key' => $group_id
	// 		)
	// 	));

	// 	$group = $this->Group->dataById($group_id);

	// 	$aros = $this->RmCommon->nodeAclTree($aros);
		
	// 	$this->set(compact('aros', 'group'));
	// }

	function admin_grant_toggle($group_id, $aco, $perm){
		$this->RmGroup->grantExtendRule();
		$this->RmCommon->manageAcl($group_id, $aco, $perm);

		$this->redirect($this->referer());
	}

	function admin_checkall($group_id, $aco_id, $perm){
		$this->RmGroup->grantExtendRule();

		// check group_id
		$principle = $this->User->Group->pickPrinciple($group_id);
		$principle_group_id = Common::hashEmptyField($principle, 'User.group_id');
		// 

		$auth_principle = $this->data_company;
		$auth_principle_group_id = Common::hashEmptyField($auth_principle, 'User.group_id');

		$auth_principle_group_id = ($principle_group_id <> $auth_principle_group_id) ? $principle_group_id : $auth_principle_group_id;

		$this->Acl->Aco->unbindModel(array(
			'hasAndBelongsToMany' => 'Aro'
		));

		$conditions[] = array(
			'OR' => array(
				array('Aco.parent_id' => $aco_id),
				array('Aco.id' => array( 1,$aco_id ))
			),
			'Aco.label NOT' => NULL,
		);

		if($aco_id == 'false' || empty($aco_id)){
			$conditions = array(
				'Aco.label NOT' => NULL,
			);
		}

		$conditions[]['OR'] = array(
			array(
				'Aco.accessible' => null,
			),
			array(
				'Aco.accessible' => $auth_principle_group_id,
			),
		);

		$acos = $this->Acl->Aco->find('all', array(
			'conditions' => $conditions,
			'order' => array(
				'Aco.parent_id' => 'ASC',
			),
		));

		if(!empty($acos)){
			$temp = '';
			foreach ($acos as $key => $value) {
				$temp['Aco'][] = $value['Aco'];
			}
			
			$acos = $this->RmCommon->nodeAclTree($temp);

			foreach ($acos as $key => $aco) {
				$childs = Common::hashEmptyField($aco, 'Child');
				$controller = Common::hashEmptyField($aco, 'alias');

				// take out karena dia allow semua fungsi di controller
				// $this->RmCommon->manageAcl($group_id, $controller, $perm);

				if(!empty($childs)){
					foreach ($childs as $key => $value) {
						$aco = $this->RmCommon->filterEmptyField($value, 'alias');
						$aco = sprintf('controllers-%s-%s', $controller, $aco);

						$result = $this->RmCommon->manageAcl($group_id, $aco, $perm);
					}
				}
			}
		}

		if($this->is_ajax){
			$this->redirect($this->referer());	
		}else{
			$msg = __('Berhasil mengizinkan semua hak akses');
			if($perm == 'deny'){
				$msg = __('Berhasil menolak semua hak akses');
			}

			$this->RmCommon->redirectReferer($msg, 'success');
		}
	}

	public function admin_target() {
		$this->RmGroup->_callBeforeSaveTarget();

		$this->set(array(
			'active_menu' => 'target_activities',
		));
		$this->render('/Elements/blocks/users/groups/forms/target');
	}

	public function admin_target_edit( $id = null ) {
		$parent = $this->RmUser->_callCheckParent();
		$parent_id = Common::hashEmptyField($parent, 'User.id');
		$is_super_admin = Configure::read('User.Admin.Rumahku');

		if( ( !empty($parent_id) && !empty($is_super_admin) ) || empty($is_super_admin) ) {
			$value = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $id,
				)
			), array(
				'company' => !empty($parent_id)?$parent_id:false,
				'status' => false,
			));

			if( !empty($value) ) {
				$this->RmGroup->_callBeforeSaveTarget($parent_id, $id);

				$this->set(array(
					'id' => $id,
					'user' => $value,
					'active_tab' => 'Target Aktivitas',
				));

				$this->render('admin_target_edit');
			} else {
				$this->RmCommon->redirectReferer(__('Divisi tidak ditemukan'));
			}
		} else {
			$this->RmCommon->redirectReferer(__('Divisi tidak ditemukan'));
		}
	}
}
