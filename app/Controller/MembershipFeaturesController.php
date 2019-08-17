<?php
App::uses('AppController', 'Controller');
class MembershipFeaturesController extends AppController {
	public $uses = array('MembershipPackageFeature');

	public function beforeFilter(){
		parent::beforeFilter();
		$this->set('active_menu', 'membership_feature');
	}

	public function admin_search($action, $_admin = TRUE){
		$data	= $this->request->data;
		$named	= $this->RmCommon->filterEmptyField($this->params, 'named');
		$params	= array('action' => $action, 'admin' => $_admin);

		if(!empty($named)){
			$params = array_merge($params, $named);
		}

		$this->RmCommon->processSorting($params, $data);
	}

	public function admin_index(){
		$groupID = Configure::read('User.group_id');

		if(in_array($groupID, array(19, 20)) === FALSE){
			$errorMsg = __d('cake', 'You are not authorized to access that location.');
			$this->RmCommon->redirectReferer($errorMsg, 'error', Configure::read('User.dashboard_url'));
		}

		$options = $this->MembershipPackageFeature->_callRefineParams($this->params, array(
			'conditions' => array(
				'MembershipPackageFeature.is_deleted' => 0, 
			),
			'order' => array(
				'MembershipPackageFeature.created' => 'DESC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));

		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate	= $this->MembershipPackageFeature->getData('paginate', $options);
		$records		= $this->paginate('MembershipPackageFeature');

		$this->set(array(
			'module_title'		=> __('Fitur Membership'), 
			'title_for_layout'	=> __('Fitur Membership')
		));

		$this->set(compact('records'));
	}

	public function admin_add(){
		if($this->request->data){
			$data	= $this->request->data;
			$result	= $this->MembershipPackageFeature->doSave($data);
			$this->RmCommon->setProcessParams($result, array('controller' => $this->params->controller, 'action' => 'index', 'admin' => TRUE));
		}

		$this->set(array(
			'module_title'		=> __('Tambah Fitur Membership'), 
			'title_for_layout'	=> __('Tambah Fitur Membership'), 
		));

		$this->render('admin_form');
	}

	public function admin_edit($featureID = NULL){
		$record = $this->MembershipPackageFeature->getData('first', array(
			'conditions' => array(
				'MembershipPackageFeature.id'			=> $featureID,
				'MembershipPackageFeature.is_deleted'	=> 0,
			)
		));

		if($record){
			$data	= $this->request->data;
			$result	= array('data' => $record);

			if($data){
				$data['MembershipPackageFeature']['id'] = $featureID;
				$result = $this->MembershipPackageFeature->doSave($data);
			}

			$this->RmCommon->setProcessParams($result, array('controller' => $this->params->controller, 'action' => 'index', 'admin' => TRUE));

			$this->set(array(
				'module_title'		=> __('Edit Fitur Membership'), 
				'title_for_layout'	=> __('Edit Fitur Membership'), 
			));

			$this->render('admin_form');
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
			$this->redirect(array('action' => 'index', 'admin' => TRUE));
		}
	}

	public function admin_delete(){
		$data	= $this->request->data;
		$id		= $this->RmCommon->filterEmptyField($data, 'MembershipPackageFeature', 'id');
    	$result = $this->MembershipPackageFeature->doToggle($id);

		$this->RmCommon->setProcessParams($result, FALSE, array('redirectError' => TRUE));
	}
}
?>