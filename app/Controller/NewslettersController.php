<?php
App::uses('AppController', 'Controller');

class NewslettersController extends AppController {

	var $components = array(
		'RmNewsletter', 'RmProperty',
	);

	function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow(array('download_xls', 'download_file', 'admin_test'));

		$this->set('active_menu', 'email_blast');
	}

	function admin_search ( $action, $additional = false ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			'admin' => true,
		);

		if(!empty($additional)){
			array_push($params, $additional);
		}

		$this->RmCommon->processSorting($params, $data);
	}

	function admin_templates(){
		$this->loadModel('MailchimpTemplate');

		$options =  $this->MailchimpTemplate->_callRefineParams($this->params, array(
			'conditions' => array(
				'MailchimpTemplate.user_company_config_id' => $this->data_company['UserCompanyConfig']['id'],
				'MailchimpTemplate.status' => 1
			),
			'order' => array(
				'MailchimpTemplate.created' => 'DESC'
			),
			'limit' => 10
		));
		
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->MailchimpTemplate->getData('paginate', $options);

		$templates = $this->paginate('MailchimpTemplate');

		$module_title = __('Template Email');
		$tab_active = __('template');

		$this->set(compact(
			'module_title', 'templates', 'tab_active'
		));
	}

	function admin_add_template(){
		$data = $this->request->data;

		$data = $this->RmNewsletter->_callBeforeSave($data, $this->data_company);
		$result = $this->User->UserCompanyConfig->MailchimpTemplate->doSave($data, false, false, false, $this->data_company);
		
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'templates',
			'admin' => true,
		));

		$module_title = __('Tambah Template Email');
		$tab_active = 'template';

		$this->RmCommon->_layout_file(array(
			'ckeditor',
		));

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'templates',
			'admin' => true
		);

		$default_template = $this->RmCommon->renderViewToVariable('netral', 'admin', array(
			'logoDefault' => false,
			'dataCompany' => $this->data_company
		));

		$this->set(compact(
			'module_title', 'tab_active', 'urlBack', 'default_template'
		));

		$this->render('admin_add_template');
	}

	function admin_edit_template($id){
		$data = $this->request->data;

		$dataTemplate = $this->User->UserCompanyConfig->MailchimpTemplate->getData('first', array(
			'conditions' => array(
				'MailchimpTemplate.id' => $id
			)
		));

		$data = $this->RmNewsletter->_callBeforeSave($data, $this->data_company);
		$result = $this->User->UserCompanyConfig->MailchimpTemplate->doSave($data, $dataTemplate, $id, false, $this->data_company);

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'templates',
			'admin' => true,
		));

		$module_title = __('Newsletter');
		$tab_active = 'template';

		$this->RmCommon->_layout_file(array(
			'ckeditor',
		));

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'templates',
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'tab_active', 'urlBack'
		));

		$this->render('admin_add_template');
	}

	function admin_delete_multiple_template(){
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'MailchimpTemplate', 'id');
		
    	$result = $this->User->UserCompanyConfig->MailchimpTemplate->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_preview_template($id = false){
		$this->getDataDetailTemplate($id);
	}

	function admin_preview_template_detail($id = false){
		$this->getDataDetailTemplate($id, false);

		$this->layout = false;
	}

	function getDataDetailTemplate($id, $with_flash = true){
		$module_title = 'Detail Template';

		if(!empty($id)){
			$template = $this->User->UserCompanyConfig->MailchimpTemplate->getData('first', array(
				'conditions' => array(
					'MailchimpTemplate.id' => $id
				)
			));
		}

		if(empty($template)){
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 'error'
			);
			
			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true,
				'flash' => $with_flash
			));
		}

		$this->set(compact('template', 'id', 'module_title'));
	}

	function admin_replicate_template($id){
		$result = $this->User->UserCompanyConfig->MailchimpTemplate->replicate( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_lists(){
		$this->loadModel('MailchimpList');

		$options =  $this->MailchimpList->_callRefineParams($this->params, array(
			'conditions' => array(
				'MailchimpList.user_company_config_id' => $this->data_company['UserCompanyConfig']['id'],
				'MailchimpList.status' => 1
			),
			'order' => array(
				'MailchimpList.created' => 'DESC'
			),
			'limit' => 10
		));
		
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->MailchimpList->getData('paginate', $options);

		$lists = $this->paginate('MailchimpList');

		if(!empty($lists)){
			$lists = $this->RmNewsletter->beforeViewList($lists);
		}

		$module_title = __('Group Email');

		$this->set('active_menu', 'group_list');
		$this->set(compact(
			'module_title', 'lists'
		));
	}

	function admin_add_list(){
		$data = $this->request->data;

		$data = $this->RmNewsletter->_callBeforeSave($data, $this->data_company, 'MailchimpList');
		$is_user_internal = Common::hashEmptyField($data, 'MailchimpList.is_user_internal');
		$groupIds = Hash::Combine($data, 'MailchimpListInternal.{n}.MailchimpListInternal.group_id', 'MailchimpListInternal.{n}.MailchimpListInternal.group_id');
		$groupIds = !empty($groupIds) ? $groupIds : array();

		$result = $this->User->UserCompanyConfig->MailchimpList->doSave($data, false);
		$url = array(
			'controller' => 'newsletters',
			'action' => 'lists',
			'admin' => true,
		);

		if(!empty($result['status']) && !empty($result['id']) && $result['status'] == 'success'){
			$action = 'detail_lists';

			if($is_user_internal){
				$action = 'detail_list_users';
				$count = count($groupIds);

				if($count == 1 && in_array('10', $groupIds)) {
					$action = 'detail_list_clients';
				}
			}

			$url = array(
				'controller' => 'newsletters',
				'action' => $action,
				$result['id'],
				'admin' => true,
			);
		}
		
		$this->RmCommon->setProcessParams($result, $url);

		$module_title = __('Tambah Grup Email');

		$groups = $this->RmNewsletter->getGroups();

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'lists',
			'admin' => true
		);

		$this->set('active_menu', 'group_list');
		$this->set(compact(
			'module_title', 'urlBack', 'groups'
		));
	}

	function admin_edit_list($id){
		$data = $this->request->data;
		$is_user_internal = Common::hashEmptyField($data, 'MailchimpList.is_user_internal');
		$groupIds = Hash::Combine($data, 'MailchimpListInternal.{n}.MailchimpListInternal.group_id', 'MailchimpListInternal.{n}.MailchimpListInternal.group_id');
		$groupIds = !empty($groupIds) ? $groupIds : array();

		$dataTemplate = $this->User->UserCompanyConfig->MailchimpList->getData('first', array(
			'conditions' => array(
				'MailchimpList.id' => $id
			)
		));
		$dataTemplate = $this->User->UserCompanyConfig->MailchimpList->getMergeList($dataTemplate, array(
			'contain' => array(
				'MailchimpListInternal',
			),
		));

		$data = $this->RmNewsletter->_callBeforeSave($data, $this->data_company, 'MailchimpList');
		$result = $this->User->UserCompanyConfig->MailchimpList->doSave($data, $dataTemplate, $id);

		$action = 'detail_lists';
		if($is_user_internal){
			$action = 'detail_list_users';
			$count = count($groupIds);

			if($count == 1 && in_array('10', $groupIds)) {
				$action = 'detail_list_clients';
			}
		}

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => $action,
			$id,
			'admin' => true,
		));

		$module_title = __('Edit Grup Email');

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'lists',
			'admin' => true
		);

		$groups = $this->RmNewsletter->getGroups();

		$this->set('active_menu', 'group_list');
		$this->set(compact(
			'module_title', 'urlBack', 'groups'
		));

		$this->render('admin_add_list');
	}

	function admin_delete_multiple_lists(){
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'MailchimpList', 'id');
		
    	$result = $this->User->UserCompanyConfig->MailchimpList->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_detail_list_clients($list_id = false){
		$value = $this->User->UserCompanyConfig->MailchimpList->getData('first', array(
			'conditions' => array(
				'MailchimpList.id' => $list_id,
			),
		));

		if(!empty($value)){
			$detail_lists = false;
			$module_title = __('Detail Grup Email');
			$value = $this->RmNewsletter->beforeViewList($value);
			$tabs = Common::hashEmptyField($value, 'MailchimpList.tabs');
			$groupIds = Common::hashEmptyField($value, 'GroupClient');

			if($groupIds){
				$detail_lists = $this->RmNewsletter->getDetailClient($this->params);
			}
			$clientTypes = $this->User->ClientType->find('list');

			$this->set(array(
				'tabs' => $tabs,
				'value' => $value,
				'list_id' => $list_id,
				'clientTypes' => $clientTypes,
				'detail_lists' => $detail_lists,
				'module_title' => $module_title,
				'active_tab' => 'Klien',
				'active_menu' => 'group_list',
			));

		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_detail_list_users($list_id = false){
		$value = $this->User->UserCompanyConfig->MailchimpList->getData('first', array(
			'conditions' => array(
				'MailchimpList.id' => $list_id,
			),
		));

		if(!empty($value)){
			$detail_lists = array();
			$module_title = __('Detail Grup Email');
			$value = $this->RmNewsletter->beforeViewList($value);
			$tabs = Common::hashEmptyField($value, 'MailchimpList.tabs');
			$groupIds = Common::hashEmptyField($value, 'GroupUser');

			if($groupIds){
				$detail_lists = $this->RmNewsletter->getDetailUser($this->params, $groupIds);
			}

			$groupOptions = $this->User->Group->getData('list', array(
				'conditions' => array(
					'Group.id' => $groupIds,
				),
			));

			$this->set(array(
				'value' => $value,
				'tabs' => $tabs,
				'list_id' => $list_id,
				'detail_lists' => $detail_lists,
				'module_title' => $module_title,
				'groupOptions' => $groupOptions,
				'active_tab' => 'User',
				'active_menu' => 'group_list',
			));

		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_detail_lists($list_id, $import = false){

		$value = $this->User->UserCompanyConfig->MailchimpList->getData('first', array(
			'conditions' => array(
				'MailchimpList.id' => $list_id,
			),
		));

		if(!empty($value)){
			$data = $this->request->data;
			$data = $this->RmNewsletter->_callBeforeSaveList($list_id, $data);
			$result = $this->User->UserCompanyConfig->MailchimpList->MailchimpListDetail->doSave($data, false);
			
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'newsletters',
				'action' => 'detail_lists',
				$list_id,
				$import,
				'admin' => true,
			));
			
			$detail_lists = $this->RmNewsletter->getDetailList($this->params, $list_id);
			$module_title = __('Detail Grup Email');

			$this->set('active_menu', 'group_list');
			$this->set(compact(
				'module_title', 'detail_lists', 'list_id', 'import',
				'value'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_add_detail_list($list_id){
		$data = $this->request->data;
		$data = $this->RmNewsletter->_callBeforeSaveList($list_id, $data);

		$result = $this->User->UserCompanyConfig->MailchimpList->MailchimpListDetail->doSave($data, false);
		
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'detail_lists',
			$list_id,
			'admin' => true,
		));

		$module_title = __('Tambah Detail Grup Email');

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'detail_lists',
			$list_id,
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'urlBack'
		));
	}

	function admin_edit_detail_list($list_id, $id){
		$data = $this->request->data;

		$dataTemplate = $this->User->UserCompanyConfig->MailchimpList->MailchimpListDetail->getData('first', array(
			'conditions' => array(
				'MailchimpListDetail.id' => $id
			)
		));

		$data = $this->RmNewsletter->_callBeforeSaveList($list_id, $data);
		$result = $this->User->UserCompanyConfig->MailchimpList->MailchimpListDetail->doSave($data, $dataTemplate, $id);

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'detail_lists',
			$list_id,
			'admin' => true,
		));

		$module_title = __('Edit Detail Grup Email');

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'detail_lists',
			$list_id,
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'urlBack', 'list_id', 'id'
		));
	}

	function admin_delete_multiple_detail_lists(){
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'MailchimpListDetail', 'id');
		
    	$result = $this->User->UserCompanyConfig->MailchimpList->MailchimpListDetail->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_campaigns(){
		$this->loadModel('MailchimpCampaign');

		$options = $this->MailchimpCampaign->_callRefineParams($this->params, array(
			'order' => array(
				'MailchimpCampaign.created' => 'DESC'
			),
			'contain' => array(
				'MailchimpList'
			),
			'limit' => 10
		));
		
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->MailchimpCampaign->getData('paginate', $options, array(
			'mine' => true,
			'company' => true,
		));

		$templates = $this->paginate('MailchimpCampaign');

		$module_title = __('Email Campaign');
		$tab_active = __('campaign');

		$this->set(compact(
			'module_title', 'tab_active', 'templates'
		));
	}

	function admin_add_campaign(){
		$step = $this->basicLabel;
		$dataBasic = $this->_callSessionCampaign($step);
		
		$data = $this->request->data;
		$data = $this->RmNewsletter->_callBeforeSaveCampaign($data, $dataBasic);

		$result = $this->User->MailchimpCampaign->doBasic( $data, $dataBasic, true );

		$result = $this->RmNewsletter->_callBeforeRender($result);
		
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'template_campaign',
			'admin' => true,
		));

		$module_title = __('Tambah Campaign');
		$sub_title = __('Tentukan subjek email dan penerima email Anda');

		$this->_callDataSupport($step);

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'campaigns',
			'admin' => true
		);

		$is_add = true;

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'urlBack', 'is_add'
		));
	}

	function admin_template_campaign($step_template = 'basic', $id = false){
		$step = $this->templateLabel;
		$dataBasic = $this->_callSessionCampaign($step);

		$template_arr = $this->RmNewsletter->getTemplate($step_template, $id);
		$template = Common::hashEmptyField($template_arr, 'template');
		$modelName = Common::hashEmptyField($template_arr, 'modelName');
		
		$result = $this->User->MailchimpCampaign->doTemplate( $step_template, $template, false, $modelName );
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'content_campaign',
			'admin' => true,
		));
		
		$module_title = __('Tambah Campaign');
		$sub_title = __('Tentukan template email yang Anda inginkan');

		$is_add = true;

		$this->_callDataSupport($step);

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'add_campaign',
			'admin' => true
		);

		$urlNext = array(
			'controller' => 'newsletters',
			'action' => 'content_campaign',
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'is_add', 'step_template', 'urlBack', 'urlNext'
		));

		$this->render('admin_add_campaign');
	}

	function admin_content_campaign($type_template = false, $id = false){
		$step = $this->contentLabel;
		$dataBasic = $this->_callSessionCampaign($step);
		
		$data = $this->request->data;
		$result = $this->User->MailchimpCampaign->doContent( $data, $dataBasic, true );

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'summary_campaign',
			'admin' => true,
		));

		$module_title = __('Tambah Campaign');
		$sub_title = __('Konten email');

		$this->RmCommon->_layout_file(array(
			'ckeditor',
		));

		$this->_callDataSupport($step);

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'template_campaign',
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'urlBack'
		));

		$this->render('admin_add_campaign');
	}

	function admin_summary_campaign(){
		$step = $this->confirmationLabel;
		$dataBasic = $this->_callSessionCampaign($step);
		$dataBasic = $this->RmNewsletter->getList($dataBasic);

		$data = $this->request->data;
		$result = $this->User->MailchimpCampaign->doSave( $data, $dataBasic, false, false, $this->data_company );

		$url = array(
			'controller' => 'newsletters',
			'action' => 'success_campaign',
			'admin' => true,
		);

		if(!empty($result['id']) && !empty($result['status']) && $result['status'] == 'success'){
			$this->RmNewsletter->_callDeleteSession();
			$url[] = $result['id'];
		}

		$this->RmCommon->setProcessParams($result, $url);
		
		$module_title = __('Tambah Campaign');
		$sub_title = __('Konfirmasi');

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'content_campaign',
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'urlBack'
		));

		$this->render('admin_add_campaign');
	}

	function admin_success_campaign($id){
		$company = false;
		if(Configure::read('User.admin')){
			$company = true;
		}

		$campaign = $this->User->MailchimpCampaign->getData('first', array(
			'conditions' => array(
				'MailchimpCampaign.id' => $id
			)
		), array(
			'company' => $company,
		));

		if(!empty($campaign['MailchimpCampaign']['type_period'])){
			$period = $campaign['MailchimpCampaign']['type_period'];

			$this->set('period', $period);
		}else{
			$this->RmCommon->redirectReferer(__('Campaign tidak ditemukan.'), 'error', array(
				'controller' => 'newsletters',
				'action' => 'campaigns',
				'admin' => true
			));
		}
	}

	function admin_replicate_campaign($id){
		$result = $this->User->MailchimpCampaign->replicate( $id );

		$url = array(
			'controller' => 'newsletters',
			'action' => 'admin_edit_campaign',
			'admin' => true,
		);

		if(!empty($result['id']) && !empty($result['status']) && $result['status'] == 'success'){
			$url[] = $result['id'];
		}else{
			$url = array(
				'controller' => 'newsletters',
				'action' => 'campaigns',
				'admin' => true,
			);
		}

		$this->RmCommon->setProcessParams($result, $url, array(
			'redirectError' => true,
		));
	}

	function admin_edit_campaign($id){
		$step = $this->basicLabel;
		$dataBasic = $this->_callSessionCampaign($step, $id);
		
		$data = $this->request->data;
		$data = $this->RmNewsletter->_callBeforeSaveCampaign($data, $dataBasic);
		$result = $this->User->MailchimpCampaign->doBasic($data, $dataBasic, true, $id );

		$result = $this->RmNewsletter->_callBeforeRender($result);

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'edit_template_campaign',
			$id,
			'admin' => true,
		));

		$module_title = __('Edit Campaign');
		$sub_title = __('Tentukan subjek email dan penerima email Anda');

		$this->_callDataSupport($step);

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'campaigns',
			'admin' => true
		);

		$is_add = false;

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'urlBack', 'is_add'
		));

		$this->render('admin_add_campaign');
	}

	function admin_edit_template_campaign($id, $step_template = 'basic', $id_template = false){
		$step = $this->templateLabel;
		$dataBasic = $this->_callSessionCampaign($step, $id);

		$template_arr = $this->RmNewsletter->getTemplate($step_template, $id_template);
		$template = Common::hashEmptyField($template_arr, 'template');
		$modelName = Common::hashEmptyField($template_arr, 'modelName');

		$result = $this->User->MailchimpCampaign->doTemplate( $step_template, $template, $id, $modelName );

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'edit_content_campaign',
			$id,
			'admin' => true,
		));
		
		$module_title = __('Edit Campaign');
		$sub_title = __('Tentukan template email yang Anda inginkan');

		$is_add = false;

		$this->_callDataSupport($step);

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'edit_campaign',
			$id,
			'admin' => true
		);

		$urlNext = array(
			'controller' => 'newsletters',
			'action' => 'edit_content_campaign',
			$id,
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'is_add', 'step_template', 'urlBack', 'urlNext'
		));

		$this->render('admin_add_campaign');
	}

	function admin_edit_content_campaign($id){
		$step = $this->contentLabel;
		$dataBasic = $this->_callSessionCampaign($step, $id);
		
		$data = $this->request->data;
		$result = $this->User->MailchimpCampaign->doContent( $data, $dataBasic, true, $id );

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'edit_summary_campaign',
			$id,
			'admin' => true,
		));

		$module_title = __('Edit Campaign');
		$sub_title = __('Konten email');

		$this->RmCommon->_layout_file(array(
			'ckeditor',
		));

		$this->_callDataSupport($step);

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'edit_template_campaign',
			$id,
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'urlBack'
		));

		$this->render('admin_add_campaign');
	}

	function admin_edit_summary_campaign($id){
		$step = $this->confirmationLabel;
		$dataBasic = $this->_callSessionCampaign($step, $id);
		$dataBasic = $this->RmNewsletter->getList($dataBasic);

		$data = $this->request->data;
		$result = $this->User->MailchimpCampaign->doSave( $data, $dataBasic, $id, false, $this->data_company );

		$url = array(
			'controller' => 'newsletters',
			'action' => 'success_campaign',
			'admin' => true,
		);

		if(!empty($result['id']) && !empty($result['status']) && $result['status'] == 'success'){
			$this->RmNewsletter->_callDeleteSession($id);
			$url[] = $result['id'];
		}

		$this->RmCommon->setProcessParams($result, $url);
		
		$module_title = __('Edit Campaign');
		$sub_title = __('Konfirmasi');

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'edit_content_campaign',
			$id,
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'urlBack'
		));

		$this->render('admin_add_campaign');
	}

	function _callSessionCampaign($step = false, $id = false, $model = 'MailchimpCampaign'){
		$dataBasic = $this->RmNewsletter->_callDataSession( $this->basicLabel, $id, $model );
        $dataTemplate = $this->RmNewsletter->_callDataSession( $this->templateLabel, $id, $model );
        $dataContent = $this->RmNewsletter->_callDataSession( $this->contentLabel, $id, $model );
        $dataConfirmation = $this->RmNewsletter->_callDataSession( $this->confirmationLabel, $id, $model );

        $data_campaign = array();
        if(!empty($id)){
    		$data_campaign = $this->User->MailchimpCampaign->getData('first', array(
				'conditions' => array(
					'MailchimpCampaign.id' => $id
				)
			));

    		$data_campaign = $this->RmNewsletter->data_step($data_campaign, $model);

			$dataBasic = !empty($dataBasic) ? $dataBasic : $data_campaign['basic'];
			$dataTemplate = !empty($dataTemplate) ? $dataTemplate : $data_campaign['template'];
			$dataContent = !empty($dataContent) ? $dataContent : $data_campaign['content'];
			$dataConfirmation = !empty($dataConfirmation) ? $dataConfirmation : $data_campaign['confirmation'];
    	}

        if( (empty($dataBasic) && $step == $this->templateLabel) || (empty($data_campaign) && !empty($id)) ) {
        	$url = array(
				'controller' => 'newsletters',
				'action' => 'add_campaign',
				'admin' => true,
			);

			if($model == 'MailchimpPersonalCampaign'){
				$url = array(
					'controller' => 'newsletters',
					'action' => 'add_personal_email',
					'admin' => true,
				);
			}
        	$this->RmCommon->redirectReferer(__('Mohon lengkapi pengaturan campaign Anda'), 'error', $url);
        } else if( (empty($dataTemplate) && $step == $this->contentLabel) || (empty($data_campaign) && !empty($id)) ) {
        	$url = array(
				'controller' => 'newsletters',
				'action' => 'template_campaign',
				'admin' => true,
			);

			if($model == 'MailchimpPersonalCampaign'){
				$url = array(
					'controller' => 'newsletters',
					'action' => 'template_personal_email',
					'admin' => true,
				);
			}

        	$this->RmCommon->redirectReferer(__('Mohon pilih template email atau klik tab buat baru'), 'error', $url);
        } else if( (empty($dataContent[$model]['content_campaign']) && $step == $this->confirmationLabel) || (empty($data_campaign) && !empty($id)) ) {
        	$url = array(
				'controller' => 'newsletters',
				'action' => 'content_campaign',
				'admin' => true,
			);

			if($model == 'MailchimpPersonalCampaign'){
				$url = array(
					'controller' => 'newsletters',
					'action' => 'content_personal_email',
					'admin' => true,
				);
			}

        	$this->RmCommon->redirectReferer(__('Mohon isi konten email yang ingin Anda kirimkan'), 'error', $url);
        } else {
        	$data = $dataBasic;

        	if(empty($this->request->data)){
        		if(is_bool($data)){
        			$data = array();
        		}

        		$data[$model]['email_from'] = $this->Auth->user('email');
        	}

        	if(!empty($dataBasic['MailchimpCampaign']['mailchimp_list_id'])){
        		$list = $this->User->UserCompanyConfig->MailchimpList->getData('first', array(
        			'conditions' => array(
        				'MailchimpList.id' => $dataBasic['MailchimpCampaign']['mailchimp_list_id'],
        				'MailchimpList.status' => 1
        			)
        		));

        		if(!empty($list)){
        			$list = $this->RmNewsletter->getList($list);
        			$is_user_internal = Common::hashEmptyField($list, 'MailchimpList.is_user_internal');
        			$params = array(
        				'groupClient' => Common::hashEmptyField($list, 'GroupClient'),
        				'groupUser' => Common::hashEmptyField($list, 'GroupUser'),
        			);

        			$list = Common::_callUnset($list, array(
        				'GroupClient',
        				'GroupUser',
        			));

        			$data['MailchimpList'] = $list['MailchimpList'];
        			$dataBasic = $data;

        			if($is_user_internal){
        				$count = $this->User->getCountUserInternal($params);
        			} else {
	        			$count = $this->User->UserCompanyConfig->MailchimpList->MailchimpListDetail->getData('count', array(
		        			'conditions' => array(
		        				'MailchimpListDetail.mailchimp_list_id' => $dataBasic['MailchimpCampaign']['mailchimp_list_id'],
		        				'MailchimpListDetail.status' => 1
		        			),
		        		));
        			}
	        		$this->set('count_list', $count);
        		}
        	}

        	if(!empty($dataTemplate[$model]) && !empty($data[$model])){
        		$data[$model] = array_merge($data[$model], $dataTemplate[$model]);
        	}

        	if(!empty($dataContent[$model]) && !empty($data[$model])){
        		$data[$model] = array_merge($data[$model], $dataContent[$model]);
        	}

        	$content = $this->getTemplateData($dataTemplate, $model);
        	
        	if(!empty($data_campaign)){
				$data = $dataBasic = $this->RmProperty->mergeArrayRecursive($data_campaign, $data);

				$type_template = $this->RmCommon->filterEmptyField($data_campaign, 'MailchimpCampaign', 'type_template');
				$id_template = $this->RmCommon->filterEmptyField($data_campaign, 'MailchimpCampaign', 'id_template');
				$type_template_temp = $this->RmCommon->filterEmptyField($dataTemplate, 'MailchimpCampaign', 'type_template');
				$id_template_temp = $this->RmCommon->filterEmptyField($dataTemplate, 'MailchimpCampaign', 'id_template');

				if(empty($this->request->data) && !empty($type_template_temp) && !empty($id_template_temp) && ($type_template != $type_template_temp || $id_template != $id_template_temp) ){
					if(!empty($dataContent)){
						$data[$model]['content_campaign'] = $this->RmCommon->filterEmptyField($dataContent, $model, 'content_campaign');
					}else{
						$data[$model]['content_campaign'] = $content;
					}
	        	}
	    	}
        	
        	$this->set(compact(
				'dataBasic', 'dataTemplate',
				'dataContent', 'dataConfirmation', 
				'id'
			));

        	return $data;
        }
	}

	function _callDataSupport ( $step = false, $is_personal = false ) {
		if( $step == $this->basicLabel && !$is_personal ) {
			$lists = $this->User->UserCompanyConfig->MailchimpList->getData('list', array(
				'fields' => array(
					'MailchimpList.id',
					'MailchimpList.name_group'
				)
			));

			// $logged_group = Configure::read('User.group_id');

			// if($logged_group == 4){
			// 	$lists = $this->User->getData('list', array(
			// 		'conditions' => array(
			// 			'User.parent_id' => $this->user_id
			// 		),
			// 		'contain' => array(
			// 			'UserCompany'
			// 		),
			// 		'fields' => array(
			// 			'User.id',
			// 			'UserCompany.name'
			// 		)
			// 	), array(
			// 		'status' => 'active',
			// 		'role' => 'principle'
			// 	));
				
			// 	if(!empty($lists)){
			// 		$temp = array();
			// 		foreach ($lists as $user_id => $value) {
			// 			$temp['all-agent-priniple-'.$user_id] = sprintf(__('Semua Agen %s'), $value);
			// 		}

			// 		$lists = $temp;
			// 	}
			// }

		} else if( $step == $this->templateLabel ) {
			$this->loadModel('MailchimpTemplateBasic');

			$templates = $this->User->UserCompanyConfig->MailchimpTemplate->getData('all');
			$template_basic = $this->MailchimpTemplateBasic->getData('all');
		} else if( $step == $this->contentLabel ) {
			$dataTemplate = $this->RmNewsletter->_callDataSession( $this->templateLabel );

			$model = 'MailchimpCampaign';
			if($is_personal){
				$model = 'MailchimpPersonalCampaign';
			}

			$this->getTemplateData($dataTemplate, $model);
		}

		$this->set(compact(
			'lists', 'templates', 'template_basic'
		));
	}

	function getTemplateData($dataTemplate, $model = 'MailchimpCampaign'){
		if(!empty($dataTemplate[$model]['type_template']) && !empty($dataTemplate[$model]['id_template'])){
			$template_email = '';
			$template = '';

			$type_template = $this->RmCommon->filterEmptyField($dataTemplate, $model, 'type_template');

			if($type_template == 'saved'){

				$template = $this->User->UserCompanyConfig->MailchimpTemplate->getData('first', array(
					'conditions' => array(
						'MailchimpTemplate.id' => $dataTemplate[$model]['id_template'],
						'MailchimpTemplate.status' => 1
					)
				));
				
				if(!empty($template['MailchimpTemplate']['template_content'])){
					$template_email = $template['MailchimpTemplate']['template_content'];

					$template = $template['MailchimpTemplate']['name_template'];
				}
			}else if($type_template == 'basic'){
				$this->loadModel('MailchimpTemplateBasic');

				$template = $this->MailchimpTemplateBasic->getData('first', array(
					'conditions' => array(
						'MailchimpTemplateBasic.id' => $dataTemplate[$model]['id_template'],
						'MailchimpTemplateBasic.status' => 1
					)
				));

				if(!empty($template['MailchimpTemplateBasic']['template_content'])){
					$template_email = $template['MailchimpTemplateBasic']['template_content'];

					$template = $template['MailchimpTemplateBasic']['name_template'];
				}
			}
			
			$this->set('template', $template);
			$this->set('type_template', $type_template);
			$this->set('template_email', $template_email);
		}
	}

	function admin_delete_multiple_campaign(){
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'MailchimpCampaign', 'id');
		
    	$result = $this->User->MailchimpCampaign->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_stop_campaign($id){
		$result = $this->User->MailchimpCampaign->doStop( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_personals(){
		$this->loadModel('MailchimpPersonalCampaign');

		$options =  $this->MailchimpPersonalCampaign->_callRefineParams($this->params, array(
			'conditions' => array(
				'MailchimpPersonalCampaign.status' => 1
			),
			'order' => array(
				'MailchimpPersonalCampaign.created' => 'DESC'
			),
			'limit' => 10
		));
		
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->MailchimpPersonalCampaign->getData('paginate', $options, array(
			'mine' => true,
			'company' => true,
		));

		$personals = $this->paginate('MailchimpPersonalCampaign');

		$module_title = __('Personal Email');
		$tab_active = __('personal');

		$this->set(compact(
			'module_title', 'tab_active', 'personals'
		));
	}

	function admin_add_personal_email(){
		$step = $this->basicLabel;
		$dataBasic = $this->_callSessionCampaign($step, false, 'MailchimpPersonalCampaign');
		
		$data = $this->request->data;
		$result = $this->User->MailchimpPersonalCampaign->doBasic( $data, $dataBasic, true );
		
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'template_personal_email',
			'admin' => true,
		));

		$module_title = __('Tambah Email Personal');
		$sub_title = __('Tentukan subjek email dan penerima email Anda');

		$this->_callDataSupport($step, true);

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'personals',
			'admin' => true
		);

		$is_add = true;

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'urlBack', 'is_add'
		));
	}

	function admin_template_personal_email($step_template = 'basic', $id = false){
		$step = $this->templateLabel;
		$dataBasic = $this->_callSessionCampaign($step, false, 'MailchimpPersonalCampaign');

		$result = $this->User->MailchimpPersonalCampaign->doTemplate( $step_template, $id );

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'content_personal_email',
			'admin' => true,
		));
		
		$module_title = __('Tambah Email Personal');
		$sub_title = __('Tentukan template email yang Anda inginkan');

		$is_add = true;

		$this->_callDataSupport($step, true);

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'add_personal_email',
			'admin' => true
		);

		$urlNext = array(
			'controller' => 'newsletters',
			'action' => 'content_personal_email',
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'is_add', 'step_template', 'urlBack', 'urlNext'
		));

		$this->render('admin_add_personal_email');
	}

	function admin_content_personal_email($type_template = false, $id = false){
		$step = $this->contentLabel;
		$dataBasic = $this->_callSessionCampaign($step, false, 'MailchimpPersonalCampaign');
		
		$data = $this->request->data;
		$result = $this->User->MailchimpPersonalCampaign->doContent( $data, $dataBasic, true );

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'newsletters',
			'action' => 'summary_personal_email',
			'admin' => true,
		));

		$module_title = __('Tambah Email Personal');
		$sub_title = __('Konten email');

		$this->RmCommon->_layout_file(array(
			'ckeditor',
		));

		$this->_callDataSupport($step, true);

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'template_personal_email',
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'urlBack'
		));

		$this->render('admin_add_personal_email');
	}

	function admin_summary_personal_email(){
		$step = $this->confirmationLabel;
		$model = 'MailchimpPersonalCampaign';
		$dataBasic = $this->_callSessionCampaign($step, false, $model);

		$data = $this->request->data;
		$result = $this->User->MailchimpPersonalCampaign->doSave( $data, $dataBasic, false );

		$url = array(
			'controller' => 'newsletters',
			'action' => 'success_personal_email',
			'admin' => true,
		);

		if(!empty($result['id']) && !empty($result['status']) && $result['status'] == 'success'){
			$this->RmNewsletter->_callDeleteSession(false, $model);
			$url[] = $result['id'];
		}

		$this->RmCommon->setProcessParams($result, $url);
		
		$module_title = __('Tambah Email Personal');
		$sub_title = __('Konfirmasi');

		$urlBack = array(
			'controller' => 'newsletters',
			'action' => 'content_personal_email',
			'admin' => true
		);

		$this->set(compact(
			'module_title', 'sub_title', 'step', 'urlBack'
		));

		$this->render('admin_add_personal_email');
	}

	function admin_success_personal_email($id){
		$company = false;
		if(Configure::read('User.admin')){
			$company = true;
		}

		$personal_email = $this->User->MailchimpPersonalCampaign->getData('first', array(
			'conditions' => array(
				'MailchimpPersonalCampaign.id' => $id
			)
		), array(
			'company' => $company
		));
	}

	function admin_delete_multiple_personals(){
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'MailchimpPersonalCampaign', 'id');
		
    	$result = $this->User->MailchimpPersonalCampaign->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_detail_email($id = false, $modul = false){
		$this->getDataDetailCempaign($id, $modul);
	}

	function admin_preview_detail($id = false, $modul = false){
		$this->getDataDetailCempaign($id, $modul, false);

		$this->layout = false;
	}

	function getDataDetailCempaign($id, $modul, $with_flash = true){
		$module_title = 'Detail';

		if(!empty($id)){
			if($modul == 'personals'){
				$campaign = $this->User->MailchimpPersonalCampaign->getData('first', array(
					'conditions' => array(
						'MailchimpPersonalCampaign.id' => $id
					)
				));
				$model = 'MailchimpPersonalCampaign';
			}else{
				$campaign = $this->User->MailchimpCampaign->getData('first', array(
					'conditions' => array(
						'MailchimpCampaign.id' => $id
					),
					'contain' => array(
						'MailchimpList'
					)
				));
				$model = 'MailchimpCampaign';
			}
		}

		if(empty($campaign)){
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 'error'
			);

			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true,
				'flash' => $with_flash
			));
		}

		$this->set(compact('campaign', 'model', 'id', 'modul', 'module_title'));
	}

	function admin_primary_birthday($id){
		$result = $this->User->UserCompanyConfig->MailchimpTemplate->primary_birthday($id, $this->data_company);
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function download_xls(){
		$filepath = Configure::read('__Site.webroot_files_path').DS.'format_email.xls';

		$this->set('filepath', $filepath);

	    $this->layout = false;
	    $this->render('/Elements/blocks/common/download');
	}

	function download_file($file_name){
		$filepath = Configure::read('__Site.webroot_files_path').DS.$file_name;

		$this->set('filepath', $filepath);

	    $this->layout = false;
	    $this->render('/Elements/blocks/common/download');
	}

	function admin_test(){
		// $email = array(
		// 	'ican46asik@yahoo.com',
		// 	'muhammad.iksan3107@gmail.com',
		// 	'muhammad.iksan@live.com',
		// );

		$email = 'muhammad.iksan@live.com';

		$email_bcc = array(
			'        ',
			'  ',
			'   ',
			' '
		);

		$params = array(
			'bcc' => $email_bcc,
			'content' => __('ini kuust banget ini'),
			'debug' => 'view'
		);

		$this->RmCommon->sendEmail('admin', $email, 'netral', 'test email', $params);
	}
}
