<?php
App::uses('AppController', 'Controller');

class CoBrokesController extends AppController {
	public $components = array(
		'RmProperty', 'RmCoBroke'
	);

	public $helpers = array(
		'CoBroke', 'Property'
	);

	public $uses = array(
		'CoBrokeProperty', 'CoBrokeUser', 'Property'
	);

	function beforeFilter() {
		parent::beforeFilter();

		$is_co_broke = $this->RmCommon->filterEmptyField($this->data_company, 'UserCompanyConfig', 'is_co_broke');
		if(empty($is_co_broke)){
			$this->RmCommon->redirectReferer(__('Anda tidak dapat mengakses halaman tersebut.'), 'error', array(
				'controller' => 'users',
				'action' => 'account',
				'admin' => true
			));
		}

		// $this->set('active_menu', 'co_brokes');
	}

	function admin_search ( $action = 'index', $_admin = true ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			'admin' => $_admin,
		);
		$this->RmCommon->processSorting($params, $data);
	}

	function admin_index() {
		$options =  $this->User->Property->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_admin_pagination'),
			'order' => array(
				'CoBrokeProperty.created' => 'DESC'
			),
			'contain' => array(
				'Property'
			),
			'conditions' => array(
				'Property.status' => 1,
				'Property.published' => 1,
				'Property.deleted' => 0,
				'Property.sold' => 0,
				'Property.inactive' => 0
			),
			'co_broke' => true,
			'limit' => '15',
		));

		$options = $this->CoBrokeProperty->customBindModel($options, $this->params);

		$options['conditions']['Property.is_cobroke'] = 1;

		$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
		$params = $this->RmCommon->defaultSearch($params, array(
			'filter' => 'CoBrokeProperty.created-desc',
		));

		$this->RmCommon->_callRefineParams($params);

		$this->paginate = $this->CoBrokeProperty->getData('paginate', $options, array(
			'status' => $this->RmCommon->filterEmptyField($params, 'named', 'status', 'publish'),
			'channel' => true
		));

		$values = $this->paginate('CoBrokeProperty');

		$values = $this->Property->getDataList($values, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'User'
			),
		));

		$values = $this->User->getDataList($values, array(
			'is_full' => false,
			'Parent'
		));

		if(!empty($values)){
			foreach ($values as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'CoBrokeProperty', 'id');

				$values[$key] = $this->CoBrokeUser->getMerge($value, $id, true, array('status' => 'all'));
			}
		}

		$module_title = __('Co-Broke Channel');

		$this->RmProperty->_callSupportAdvancedSearch();

		$tab_active = 'channel';

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'tab_active' => $tab_active,
			'active_menu' => 'co_broke_channel',
		));
	}

	function backprocess_make_cobroke($property_id){
		$property = $this->Property->getProperty('first', false, $property_id);

		$status = false;

		if( !empty($property) ){
			$data = $this->request->data;

			$data = $this->RmCommon->dataConverter($data, array(
                'price' => array(
                    'Property' => array(
                        'co_broke_commision'
                    ),
                )
            ));
			
			$result = $this->CoBrokeProperty->doMakeCoBroke($data, $property_id);

			$status = $this->RmCommon->filterEmptyField($result, 'status');

			if(!empty($status) && $status == 'success'){
				$this->RmProperty->rePropertyRevision($data, $property_id, $this->assetLabel);
			}
		}else{
			 $result = array();
		}

		$this->set(compact('property', 'status'));

		$this->set('type_commission', $this->RmCommon->getGlobalVariable('type_commision_cobroke'));

		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => false,
			'noRedirect' => true
		));

		if($status == 'success'){
			$this->set('_flash', false);
			$this->layout = 'ajax';
		}
	}

	// cobroke_note
	function backprocess_cobroke_note($property_id){
		$property = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $property_id,
			),
		), array(
			'mine' => false,
			'company' => false,
		));

		if( !empty($property) ) {
			$property = $this->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertyMediasCount',
					'User',
				),
			));

		} else {
			 $property = array();
		}

		$this->theme  = false;
		$this->layout = 'ajax';
		$this->set(compact('property'));

	}

	function backprocess_stop_toggle($property_id){
		$result = $this->CoBrokeProperty->doStopToggle($property_id);

		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true
		));
	}

	function admin_request_cobroke($co_broke_id, $email = false){
		$User = Configure::read('User.data');
		$User = $this->User->UserProfile->getMerge($User, $this->user_id, true);

	//	DEFAULT DATA BROKER AMBIL DARI USER LOGIN
		$brokerID		= Common::hashEmptyField($User, 'id');
		$brokerParentID	= Common::hashEmptyField($User, 'parent_id');

		$value = $this->CoBrokeProperty->getData('first', array(
			'conditions' => array(
				'CoBrokeProperty.id' => $co_broke_id
			)
		));

		$value = $this->Property->getDataList($value, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'User'
			),
		));

		$value = $this->User->getDataList($value, array(
			'is_full' => false,
			'Parent',
		));

		$data = $this->request->data;
		$data = $this->RmCoBroke->_callBeforeSaveUser($data, $value, $co_broke_id);

		$result = $this->CoBrokeUser->doSave($data);
		$status = $this->RmCommon->filterEmptyField($result, 'status');

	//	auto approve cobroke internal only =====================================================================

		$coBrokeUserID = Common::hashEmptyField($result, 'id');

		if($status == 'success' && $coBrokeUserID){
		//	note : validasi udah ada di dalem

			$approvalResult	= $this->CoBrokeUser->doApprove($coBrokeUserID);
			$approvalStatus	= Common::hashEmptyField($approvalResult, 'status');

			if($approvalStatus == 'success'){
			//	multiple format
				$notifications	= Common::hashEmptyField($result, 'Notification', array());
				$sendEmails		= Common::hashEmptyField($result, 'SendEmail', array());

			//	multiple format
				$approvalMessage		= Common::hashEmptyField($approvalResult, 'msg');
				$approvalNotifications	= Common::hashEmptyField($approvalResult, 'Notification', array());
				$approvalEmails			= Common::hashEmptyField($approvalResult, 'SendEmail', array());

			//	append ke result atas
				$result	= Hash::insert($result, 'msg', $approvalMessage);
				$result = Hash::insert($result, 'Notification', array_merge($notifications, $approvalNotifications));
				$result = Hash::insert($result, 'SendEmail', array_merge($sendEmails, $approvalEmails));
			}
		}

	//	========================================================================================================

		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
		));

		if(!empty($email) && empty($this->request->data)){
			$data_user_email = $this->User->getData('first', array(
				'conditions' => array(
					'User.email' => $email
				)
			), array(
				'company' => true,
				'role' => 'agent'
			));

			if(!empty($data_user_email)){
				$agent_id = Common::hashEmptyField($data_user_email, 'User.id');
				$data_user_email = $this->User->UserProfile->getMerge($data_user_email, $agent_id, true);

				$UserProfile = Common::hashEmptyField($data_user_email, 'UserProfile');

				$this->request->data['CoBrokeUser'] = array(
					'name' 			=> Common::hashEmptyField($data_user_email, 'User.full_name'),
					'phone' 		=> Common::hashEmptyField($UserProfile, 'no_hp', false, array(
						'urldecode' => false,
					)),
					'agent_email' 	=> $email
				);

				$this->request->data['UserProfile'] = $UserProfile;

				$User = $this->request->data;

			//	GANTI DATA BROKER JADI USER BY EMAIL
				$brokerID		= Common::hashEmptyField($data_user_email, 'User.id');
				$brokerParentID	= Common::hashEmptyField($data_user_email, 'User.parent_id');
			}
		}

		if(empty($data['UserProfile'])){
			$this->request->data['UserProfile'] = $this->RmCommon->filterEmptyField($User, 'UserProfile');
		}

		$this->RmCommon->_callRequestSubarea('UserProfile');

		$regionName		= Common::hashEmptyField($this->data, 'UserProfile.Region.name');
		$cityName		= Common::hashEmptyField($this->data, 'UserProfile.City.name');
		$subareaName	= Common::hashEmptyField($this->data, 'UserProfile.Subarea.name');
		$locationName	= array_filter(array($subareaName, $cityName, $regionName));

		if($locationName){
			$this->request->data = Hash::insert($this->request->data, 'UserProfile.location_name', implode(', ', $locationName));
		}

		if($status == 'success'){
			$this->set('_flash', false);
		}

		$url_here = array(
			'controller' => 'co_brokes',
			'action' => 'request_cobroke',
			$co_broke_id,
			'admin' => true
		);

	//	DISABLE COMMISSION INPUT (JIKA AUTO APPROVE CO BROKE) ========================================================================

		$companyData	= Configure::read('Config.Company.data');
		$isCoBroke		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_co_broke');
		$coBrokeType	= Common::hashEmptyField($companyData, 'UserCompanyConfig.default_type_co_broke');
		$isAutoApprove	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_auto_approve_cobroke');

		$disableCommission = false;

		if($isCoBroke && $coBrokeType == 'in_corp' && $isAutoApprove){
			$isAdmin		= Configure::read('User.admin');
			$ownerParentID	= Common::hashEmptyField($value, 'User.parent_id');

			if(($isAdmin && in_array($ownerParentID, array($brokerID, $brokerParentID))) || ($brokerParentID == $ownerParentID)){
				$disableCommission = true;
			}
		}

	//	==============================================================================================================================

		$this->layout = false;
		$this->set(array(
			'disable_commission' => $disableCommission, 
			'type_commission' => $this->RmCommon->getGlobalVariable('type_commision_cobroke'),
			'data' => $value,
			'data_user' => $User,
			'status' => $status,
			'co_broke_id' => $co_broke_id,
			'url_here' => $url_here, 
		));
	}

	function admin_me(){
		$options =  $this->User->Property->_callRefineParams($this->params, array(
			'conditions' => array(
				'Property.co_broke_commision <>' => 0
			),
			'limit' => Configure::read('__Site.config_admin_pagination'),
			'order' => array(
				'CoBrokeProperty.created' => 'DESC'
			),
			'contain' => array(
				'Property'
			),
			'co_broke' => true,
			'limit' => '15',
		));

		$options = $this->CoBrokeProperty->customBindModel($options, $this->params);

		$options['conditions']['Property.is_cobroke'] = 1;

		$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
		$params = $this->RmCommon->defaultSearch($params, array(
			'filter' => 'CoBrokeProperty.created-desc',
		));

		$this->RmCommon->_callRefineParams($params);

		$elements['status'] = $this->RmCommon->filterEmptyField($params, 'named', 'status', 'active');

		$this->paginate = $this->CoBrokeProperty->getData('paginate', $options, array_merge($elements, array(
			'mine' => true,
			'admin_mine' => true
		)));

		$values = $this->paginate('CoBrokeProperty');

		$values = $this->Property->getDataList($values, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'User'
			),
		));

		$values = $this->User->getDataList($values, array(
			'is_full' => false,
			'Parent'
		));

		if(!empty($values)){
			foreach ($values as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'CoBrokeProperty', 'id');

				if(!empty($value['PropertySold'])){
					$period_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'period_id');
                    $currency_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'currency_id');

                    $value['PropertySold'] = $this->Property->Period->getMerge($value['PropertySold'], $period_id);
                    $value['PropertySold'] = $this->Property->Currency->getMerge($value['PropertySold'], $currency_id);

                    $values[$key]['PropertySold'] = $value['PropertySold'];
				}

				$values[$key] = $this->CoBrokeUser->getMerge($value, $id, true, array('status' => 'all'));
			}
		}

		$module_title = __('Listing Saya');

		$this->RmProperty->_callSupportAdvancedSearch();

		$tab_active = 'mine';

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'tab_active' => $tab_active,
			'active_menu' => 'co_broke_me',
		));
	}

	function admin_brokers($co_broke_property_id = false){
		$co_broke = $this->CoBrokeProperty->getData('first', array(
			'conditions' => array(
				'CoBrokeProperty.id' => $co_broke_property_id,
				'Property.is_cobroke' => 1
			),
			'contain' => array(
				'Property'
			)
		));

		if(!empty($co_broke)){
			$co_broke = $this->Property->getDataList($co_broke, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'User'
				),
			));

			$co_broke = $this->User->getDataList($co_broke, array(
				'is_full' => false,
				'Parent'
			));

			$options =  $this->User->_callRefineParams($this->params, array(
				'conditions' => array(
					'CoBrokeUser.co_broke_property_id' => $co_broke_property_id
				),
				'limit' => Configure::read('__Site.config_admin_pagination'),
				'order' => array(
					'CoBrokeUser.created' => 'DESC'
				),
				'contain' => array(
					'User'
				),
				'co_broke' => true
			));

			$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
			$params = $this->RmCommon->defaultSearch($params, array(
				'filter' => 'CoBrokeUser.created-desc',
			));

			$this->RmCommon->_callRefineParams($params);

			$this->paginate = $this->CoBrokeUser->getData('paginate', $options, array(
				'status' => 'all',
				'admin_reverse' => true
			));

			$values = $this->paginate('CoBrokeUser');

			$values = $this->CoBrokeUser->getMergeList($values, array(
				'contain' => array(
					'User' => array(
						'UserProfile'
					)
				)
			));

			$values = $this->User->getDataList($values, array(
				'is_full' => false,
				'Parent'
			));

			$module_title = __('List Broker');
			$tab_active = 'mine';

			$this->set(array(
				'values' => $values,
				'module_title' => $module_title,
				'tab_active' => $tab_active,
				'active_menu' => 'co_broke_me',
				'co_broke' => $co_broke,
				'co_broke_property_id' => $co_broke_property_id,
				'type_commission' => $this->RmCommon->getGlobalVariable('type_commision_cobroke'),
			));

		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
		}
	}

	function admin_listing(){
		$options =  $this->User->Property->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_admin_pagination'),
			'order' => array(
				'CoBrokeUser.modified' => 'DESC'
			),
			'contain' => array(
				'CoBrokeProperty',
				'Property'
			),
			'co_broke' => true,
			'limit' => '15',
		));

		$options = $this->CoBrokeProperty->customBindModel($options, $this->params);

		$options['conditions']['Property.is_cobroke'] = 1;

		$this->CoBrokeUser->customBindModel($options, $this->params);

		$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
		$params = $this->RmCommon->defaultSearch($params, array(
			'filter' => 'CoBrokeUser.modified-desc',
		));

		$status = Common::hashEmptyField($params, 'named.status', 'all');

		$this->RmCommon->_callRefineParams($params);

		$this->paginate = $this->CoBrokeUser->getData('paginate', $options, array(
			'mine' => true,
			'status' => $status
		));

		$values = $this->paginate('CoBrokeUser');

		$values = $this->CoBrokeProperty->getMergeList($values, array(
			'contain' => array(
				'Property' => array(
					'elements' => array(
						'status' => 'all',
						'company' => false
					)
				)
			)
		));

		$values = $this->Property->getDataList($values, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'User'
			),
		));

		$values = $this->User->getDataList($values, array(
			'is_full' => false,
			'Parent'
		));

		if(!empty($values)){
			foreach ($values as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'CoBrokeProperty', 'id');

				if(!empty($value['PropertySold'])){
					$period_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'period_id');
                    $currency_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'currency_id');

                    $value['PropertySold'] = $this->Property->Period->getMerge($value['PropertySold'], $period_id);
                    $value['PropertySold'] = $this->Property->Currency->getMerge($value['PropertySold'], $currency_id);

                    $values[$key]['PropertySold'] = $value['PropertySold'];
				}

				$values[$key] = $this->CoBrokeUser->getMerge($value, $id, true, array('status' => 'all'));
			}
		}

		$module_title = __('Listing Co-Broke');

		$this->RmProperty->_callSupportAdvancedSearch();

		$tab_active = 'my_cobroke';

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'tab_active' => $tab_active,
			'active_menu' => 'co_broke_listing',
		));
	}

	function backprocess_print($id, $pdf = false){
		$value = $this->CoBrokeUser->completeDataCoBrokeUser($id);

		$owner_property = Common::hashEmptyField($value, 'owner_data');
		$value = Common::hashEmptyField($value, 'broker_data');

		if(!empty($value)){
			$final_commission = Common::hashEmptyField($value, 'CoBrokeUser.final_commission');
			$final_type_commission = Common::hashEmptyField($value, 'CoBrokeUser.final_type_commission');
			$final_type_price_commission = Common::hashEmptyField($value, 'CoBrokeUser.final_type_price_commission');

			$code = Common::hashEmptyField($value, 'CoBrokeProperty.code');

			$cobroke_requirement = $this->RmCoBroke->getRequirementCoBroke($owner_property, $final_commission, $final_type_commission, $final_type_price_commission);

			$title_for_layout = sprintf(__('Cetak Aplikasi Form Co-Broke #%s'), $code);

			$this->set(compact(
				'value', 'title_for_layout', 'owner_property',
				'cobroke_requirement', 'pdf'
			));

			$this->layout = false;
			$this->RmCommon->printPDF($pdf);
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
		}
	}

	function backprocess_approve($id){
		$result = $this->CoBrokeUser->doApprove($id);
		
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true
		));
	}

	function admin_rejected($id){
		$data = $this->request->data;

		$result = $this->CoBrokeUser->doReject($data, $id);

		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
		));
	}

	function  backprocess_unrejected($id){
		if($this->CoBrokeUser->updateStatus('normalize', $id)){
			$result = array(
				'msg' => __('Berhasil menghilangkan status ditolak'),
				'status' => 'success',
				'Log' => array(
                    'document_id' => $id,
                    'activity' => __('Berhasil menghilangkan status ditolak'),
                ),
			);
		}else{
			$result = array(
				'msg' => __('Gagal menghilangkan status ditolak'),
				'status' => 'error'
			);
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function backprocess_edit_cobroke($id){
		$is_admin = Configure::read('User.admin');
		$User = Configure::read('User.data');
		$User = $this->User->UserProfile->getMerge($User, $this->user_id, true);

		$value = $this->CoBrokeUser->completeDataCoBrokeUser($id);

		$value = $this->RmCommon->filterEmptyField($value, 'broker_data');

		$status = false;
		if(!empty($value)){
			$co_broke_id = $this->RmCommon->filterEmptyField($value, 'CoBrokeUser', 'co_broke_property_id');
			$user_requester_id = $this->RmCommon->filterEmptyField($value, 'CoBrokeUser', 'user_id');

			$data = $this->request->data;

			$data = $this->RmCoBroke->_callBeforeSaveUser($data, $value, $co_broke_id);
			
			$result = $this->CoBrokeUser->doSave($data, $value, $id);

			$status = $this->RmCommon->filterEmptyField($result, 'status');

			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true,
			));

			if(empty($data['UserProfile'])){
				$this->request->data['UserProfile'] = $this->RmCommon->filterEmptyField($User, 'UserProfile');
			}

			$this->RmCommon->_callRequestSubarea('UserProfile');
		}
		
		$this->set('data', $value);
		$this->set('data_user', $User);
		$this->set('is_edit', true);

		if($status == 'success'){
			$this->set('_flash', false);
		}

		$this->set('status', $status);

		$this->set('type_commission', $this->RmCommon->getGlobalVariable('type_commision_cobroke'));

		if(!empty($value['CoBrokeUser']['revision_commission']) || !empty($value['CoBrokeUser']['final_commission'])){
			$this->set('show_request_commission', false);
		}

		if(($user_requester_id != $this->user_id)){
			if(!$is_admin){
				$this->set('toggle_address', false);
			}
		}

		$this->render('admin_request_cobroke');
	}

	function admin_detail_property($id){
		$params = $this->params->params;

		$no_form = Common::hashEmptyField($params, 'named.no_form');

		$value = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
			),
		), array(
			'status' => 'active-pending-sold',
			'company' => false,
		));

		$value = $this->Property->getDataList($value, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'PropertyFacility',
				'PropertyPointPlus',
				'PropertyPrice',
				'User',
				'CoBrokeProperty'
			),
		));

		$co_broke_property_id = $this->RmCommon->filterEmptyField($value, 'CoBrokeProperty', 'id');

		$value = $this->User->getDataList($value, array(
			'is_full' => false,
			'Parent'
		));

		$data_cobroke_user = $this->CoBrokeUser->getData('first', array(
			'conditions' => array(
				'CoBrokeUser.co_broke_property_id' => $co_broke_property_id,
				'CoBrokeUser.user_id' => $this->user_id
			)
		), array(
			'status' => 'all'
		));

		if(!empty($data_cobroke_user)){
			$value['CoBrokeUser'] = Common::hashEmptyField($data_cobroke_user, 'CoBrokeUser');
		}

		$dataView = $this->RmCommon->_callSaveVisitor($co_broke_property_id, 'CoBrokePropertyView', 'co_broke_property_id');
		$this->CoBrokeProperty->CoBrokePropertyView->doSave($dataView);

		$this->set('no_form', $no_form);

		$this->set('value', $value);
	}

	function backprocess_delete_co_broke($co_broke_id = false){
		$result = $this->CoBrokeProperty->doDelete($co_broke_id);

		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true
		));
	}

	function admin_approval(){
		$isAdmin = $this->RmCommon->_isAdmin();
		$group_id = Configure::read('User.group_id');
		$is_admin_approval_cobroke = Common::hashEmptyField($this->data_company, 'UserCompanyConfig.is_admin_approval_cobroke');

		if( (!empty($is_admin_approval_cobroke) || !empty($isAdmin)) && in_array($group_id, array(2,3,4,5,19,20)) ){
			$options =  $this->User->Property->_callRefineParams($this->params, array(
				'conditions' => array(
					'Property.co_broke_commision <>' => 0,
					'Property.commission <>' => 0,
					'Property.is_cobroke' => 1,
					'Property.in_update' => 0
				),
				'limit' => Configure::read('__Site.config_admin_pagination'),
				'order' => array(
					'Property.change_date' => 'DESC'
				),
				'contain' => array(
					'Property'
				),
				'co_broke' => true,
				'limit' => '15',
			));

			$options = $this->CoBrokeProperty->customBindModel($options, $this->params);

			$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
			$params = $this->RmCommon->defaultSearch($params, array(
				'filter' => 'Property.change_date-desc',
			));

			$this->RmCommon->_callRefineParams($params);

			$this->paginate = $this->CoBrokeProperty->getData('paginate', $options, array(
				'mine' => true,
				'admin_mine' => true,
				'status' => 'pending-approve'
			));

			$values = $this->paginate('CoBrokeProperty');

			$values = $this->Property->getDataList($values, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'User'
				),
			));

			$values = $this->User->getDataList($values, array(
				'is_full' => false,
				'Parent'
			));

			if(!empty($values)){
				foreach ($values as $key => $value) {
					$id = $this->RmCommon->filterEmptyField($value, 'CoBrokeProperty', 'id');

					$values[$key] = $this->CoBrokeUser->getMerge($value, $id, true, array('status' => 'all'));
				}
			}

			$module_title = __('Approval');

			$this->RmProperty->_callSupportAdvancedSearch();

			$tab_active = 'approval';

			$this->set(array(
				'values' => $values,
				'module_title' => $module_title,
				'tab_active' => $tab_active,
				'active_menu' => 'co_broke_approval',
			));
		}else{
			$this->RmCommon->redirectReferer(__('Anda tidak dapat mengakses halaman tersebut.'), 'error', array(
				'controller' => 'users', 
				'action' => 'account',
				'admin' => true,
			));
		}
	}

	function backprocess_approve_cobroke($id, $type = 'approve'){
		$data = $this->request->data;

		$result = $this->CoBrokeProperty->doApproveRequest($data, $id, $type);

		$options_set = array(
			'redirectError' => true
		);

		if($type == 'reject'){
			$status = $this->RmCommon->filterEmptyField($result, 'status');

			$this->set('status', $status);

			if($status == 'success'){
				$this->set('_flash', false);
			}

			$options_set = array(
				'ajaxFlash' => true
			);
		}

		$this->RmCommon->setProcessParams($result, false, $options_set);
	}

	function admin_revision_request($co_broke_property_id, $id_co_broke_user){
		$value = $this->CoBrokeUser->find('first', array(
			'conditions' => array(
				'CoBrokeUser.id' => $id_co_broke_user,
				'CoBrokeUser.co_broke_property_id' => $co_broke_property_id,
				'CoBrokeUser.status' => 1
			)
		), array(
			'status' => 'active'
		));

		$data = $this->request->data;

		if(!empty($data)){
			$data = $this->RmCommon->dataConverter($data, array(
                'price' => array(
                    'CoBrokeUser' => array(
                        'revision_commission'
                    ),
                )
            ));
		}

		$result = $this->CoBrokeUser->revisionRequestCommission($data, $id_co_broke_user);

		$status = Common::hashEmptyField($result, 'status');

		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
		));

		if($status == 'success'){
			$this->set('_flash', false);
		}

		$this->set('type_commission', $this->RmCommon->getGlobalVariable('type_commision_cobroke'));

		$this->set('value', $value);
		$this->set('status', $status);
	}

	function backprocess_approve_revision($id){
		$result = $this->CoBrokeUser->doApproveRevision($id);

		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true
		));
	}

	function backprocess_diapprove_revision($id){
		$result = $this->CoBrokeUser->doApproveRevision($id, false);

		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true
		));
	}
}
