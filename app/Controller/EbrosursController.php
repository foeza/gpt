<?php
App::uses('AppController', 'Controller');

class EbrosursController extends AppController {

	public $uses = array(
		'UserCompanyEbrochure'
	);

	public $components = array(
		'RmImage', 'RmEbroschure', 'RmProperty',
		'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
	            'admin_index' => array(
				  	'extract' => array(
				  		'paging', 'msg', 'status', 'link', 'appversion', 'device',
				  		'data',
				 	),
			 	),
	            'admin_edit' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors',
				 	),
			 	),
	            'admin_detail' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data',
				 	),
			 	),
			 	'admin_request_ebrosurs' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'data',
				 	),
			 	),
			 	'admin_api_request_add' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors', 'agents'
				 	),
			 	),
			 	'admin_api_list_target_request' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'list_user'
				 	),
			 	),
			 	'admin_delete_multiple_ebrosurs' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'admin_delete_multiple' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'admin_regenerate' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'admin_mail' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors'
				 	),
			 	),
			 	'admin_api_agent_clients' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'data'
				 	),
			 	)
		 	),
	 	),
	);
	
	public $helpers = array(
		'Rumahku', 'Paginator', 'Ebrosur',
	);

	function beforeFilter() {
		parent::beforeFilter();

		$authGroupID	= Common::config('User.group_id', 0);
		$permissions	= Common::config('Permission.'.$authGroupID, array());

		$companyData	= Configure::read('Config.Company.data');
		$companyID		= Common::hashEmptyField($companyData, 'UserCompany.id', 0);

		$this->UserCompanyEbrochure->companyID = $companyID;

		$this->set('active_menu', 'ebrosurs');

		$allowedActions	= array('index', 'search', 'detail');

		if($this->params->action == 'admin_generate'){
			$acoPath = implode('/', array('controllers', $this->name, 'admin_regenerate'));

			if($permissions && in_array($acoPath, $permissions)){
				$allowedActions[] = $this->params->action;
			}
		}

		$this->Auth->allow($allowedActions);

		$pageList		= array($this->params->controller => array('index' => 'is_brochure'));
		$companyConfig	= Configure::read('Config.Company.data.UserCompanyConfig');
		$pageRules		= $companyConfig ? array_intersect_key($companyConfig, array_flip($pageList[$this->params->controller])) : array();

		if($pageRules){
			$isAllowed = $this->RmCommon->authPage($pageList, $pageRules);
			if($isAllowed === FALSE){
				$this->redirect('/');
			}
		}

		$isBrochure = $this->RmCommon->filterEmptyField($this->data_company, 'UserCompanyConfig', 'is_brochure');
		$isAdmin 	= $this->RmCommon->_isAdmin();

		if($isBrochure || $isAdmin){
			
		}else{
			$this->redirect('/');
		}
	}

	function admin_search ( $action, $addParam = false ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			$addParam,
			'admin' => true,
		);

		$this->RmCommon->processSorting($params, $data);
	}

	function search ( $action, $prefix = false ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			'admin' => false
		);

		if(!empty($prefix)){
			$params[$prefix] = true;
		}

		$this->RmCommon->processSorting($params, $data);
	}

	public function admin_add() {
		$data = $this->request->data;
		
		$save_path_ebrosur = Configure::read('__Site.ebrosurs_photo');
		$data = $this->RmImage->_uploadPhoto( $data, 'UserCompanyEbrochure', 'filename', $save_path_ebrosur );

		$this->RmEbroschure->_callBeforeSave($data, $this->data_company, $this->data_company, Configure::read('User.data'));

		/*Set Default Data*/
        $this->_callCreateEbrosurSupport();

        $this->set(array(
        	'active_menu' => 'e_brosur_list',
        	'module_title' => __('Buat eBrosur'),
        ));
		/*END Set Default Data*/
	}

	public function admin_edit($id) {		
		if(Configure::read('User.admin')){
			$mine = false;
		} else {
			$mine = true;
		}

		$ebrosur = $this->UserCompanyEbrochure->getData('first', array(
			'conditions' => array(
				'UserCompanyEbrochure.id' => $id
			),
		), array(
			'mine' => $mine
		));

		if( !empty($ebrosur) ) {
			$ebrosur = $this->UserCompanyEbrochure->getMergeList($ebrosur);

			$ebrosur['UserCompanyEbrochure']['specification_property'] = $this->RmProperty->getSpesification($ebrosur, array(
				'to_string' => true
			));
			$ebrosur['UserCompanyEbrochure']['description_property'] = $this->RmCommon->filterEmptyField($ebrosur, 'Property', 'description', '', array(
				'urldecode' => false
			));

			$data = $this->request->data;

			$save_path_ebrosur = Configure::read('__Site.ebrosurs_photo');
			$data = $this->RmImage->_uploadPhoto( $data, 'UserCompanyEbrochure', 'filename', $save_path_ebrosur );

			$result = $this->RmEbroschure->_callBeforeSave($data, $this->data_company, $this->data_company, Configure::read('User.data'), false, $ebrosur);

			// if(!empty($result['status']) && $result['status'] == 'success'){
			// 	$ebrosur_photo = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'ebrosur_photo');
			// 	$dimension = array_keys($this->RmImage->_rulesDimensionImage(Configure::read('__Site.ebrosurs_photo')));

			// 	$this->RmCommon->deletePathPhoto(Configure::read('__Site.ebrosurs_photo'), $ebrosur_photo, $dimension);

			// 	if($this->Rest->isActive()){
			// 		$ebrosur = $this->UserCompanyEbrochure->getData('first', array(
			// 			'conditions' => array(
			// 				'UserCompanyEbrochure.id' => $id
			// 			),
			// 		), array(
			// 			'mine' => $mine
			// 		));

			// 		$ebrosur = $this->UserCompanyEbrochure->getMergeList($ebrosur);
			// 	}
			// }

			$id = $this->RmCommon->filterEmptyField($result, 'id');

			$url = array(
				'controller' => 'ebrosurs',
				'action' => 'detail',
				$id,
				'admin' => true,
			);
			$options = false;

			if(!empty($result['redirect'])){
				$url = $result['redirect'];
				$options = array(
					'redirectError' => true
				);
			}

			$this->RmCommon->_callDataForAPI($ebrosur, 'manual');
			$this->RmCommon->setProcessParams($result, $url, $options);

			/*Set Default Data*/
	        $this->_callCreateEbrosurSupport($ebrosur);

	        $this->set(array(
	        	'active_menu' => 'e_brosur_list',
	        	'module_title' => __('Edit eBrosur'),
	        ));
			/*END Set Default Data*/

			$this->render('admin_add');
		} else {
			$this->RmCommon->redirectReferer(__('Ebrosur tidak ditemukan.'), 'error', array(
				'controller' => 'ebrosurs',
				'action' => 'index',
				'client' => false,
				'admin' => true
			));
		}
	}

	function _callCreateEbrosurSupport($value = array()){
		$this->Property = $this->User->Property;

		$data = $this->request->data;

		if(empty($data) && !empty($value)){
			$data = $value;
		}

	//	open listing
		$companyData	= Configure::read('Config.Company.data');
		$isOpenListing	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_open_listing');

		$elements	= array('status' => 'active-pending-sold');
		$propertyID	= Common::hashEmptyField($data, 'UserCompanyEbrochure.property_id');

		if($isOpenListing){
			$propertyID = Common::hashEmptyField($this->params->named, 'property_id', $propertyID);
			$elements	= array_merge($elements, array(
				'company' => false, 
			));
		}

		if($propertyID){
			$property_medias = $this->Property->PropertyMedias->getData('all', array(
				'conditions' => array(
					'PropertyMedias.property_id' => $propertyID, 
				),
			), array(
				'status' => 'all', 
			));

			$property = $this->User->Property->getData('first', array(
				'conditions' => array(
					'Property.id' => $propertyID, 
				)
			), $elements);

			if(!empty($property)){
				$property = $this->User->Property->getDataList($property, array(
					'contain' => array(
						'MergeDefault',
						'PropertyAddress',
						'PropertyAsset',
						'PropertySold',
						'User', 
					),
				));

				if(empty($data)){
				//	data source property (ebochure belom jadi)
					$this->RmEbroschure->setDataForm($property);

					$authUserID		= Configure::read('User.id');
					$propertyUserID	= Common::hashEmptyField($property, 'Property.user_id');

					if($authUserID != $propertyUserID){
						$this->request->data['UserCompanyEbrochure']['name'] = Configure::read('User.data.full_name');
					}
				}
				else{
					$data = $this->UserCompanyEbrochure->getMergeList($data, array(
						'contain' => array(
							'User' => array(
								'status' => 'all',
							),
						),
					));
					$this->request->data['User'] = Common::hashEmptyField($data, 'User');

				//	data source ebrochure
					$this->request->data['UserCompanyEbrochure']['specification_property'] = $this->RmProperty->getSpesification($property, array(
						'to_string' => true
					));

					$this->request->data['UserCompanyEbrochure']['description_property'] = $this->RmCommon->filterEmptyField($property, 'Property', 'description', '', array(
						'urldecode' => false
					));

				//	yang atas ga usah pake set location udah di setting di $this->RmEbroschure->setDataForm
					$locationName = Common::hashEmptyField($data, 'UserCompanyEbrochure.location_name');

					if(empty($locationName)){
						$regionName		= Common::hashEmptyField($data, 'Region.name');
						$cityName		= Common::hashEmptyField($data, 'City.name');
						$subareaName	= Common::hashEmptyField($data, 'Subarea.name');
						$locationName	= array_filter(array($subareaName, $cityName, $regionName));
						$locationName	= implode(', ', $locationName);

						$this->request->data['UserCompanyEbrochure']['location_name'] = $locationName;
					}
				}
			}

			$this->set('property_medias', $property_medias);
		}

		$property_action_id = $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'property_action_id');
		$property_type_id = $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'property_type_id');

		$property_type = $this->Property->PropertyType->getData('first', array(
			'conditions' => array(
				'PropertyType.id' => $property_type_id
			),
			'cache' => __('PropertyType.%s', $property_type_id)
		));

		$propertyActions = $this->Property->PropertyAction->getData('list', array(
            'cache' => __('PropertyAction.List'),
        ));
		$propertyTypes = $this->Property->PropertyType->getData('list', array(
            'cache' => __('PropertyType.List'),
        ));
		$currencies = $this->Property->Currency->getData('list', array(
			'fields' => array(
				'Currency.id', 'Currency.alias',
			),
			'cache' => __('Currency.alias'),
		));
		$periods = $this->Property->PropertyPrice->Period->getData('list', array(
            'cache' => __('Period.List'),
        ));

		$lotUnits = $this->Property->PropertyAsset->LotUnit->getData('list', array(
			'fields' => array(
				'LotUnit.id', 'LotUnit.slug',
			),
			'group' => array(
				'LotUnit.slug',
			),
			'cache' => __('LotUnit.GroupSlug.List'),
		), array(
			'is_lot' => true,
		));

		$color_scheme = $this->RmCommon->getGlobalVariable('color_banner_option');

		$this->RmCommon->_callRequestSubarea('UserCompanyEbrochure');
		
		$this->set(compact(
			'propertyActions', 'propertyTypes', 'color_scheme',
			'currencies', 'periods', 'lotUnits'
		));
	}

	function admin_index(){
		$ebrosurs = $this->RmEbroschure->_callBeforeViewEbrosurs();

		if($this->Rest->isActive() && empty($ebrosurs) ){
			$ebrosurs = null;
		}

		$module_title = __('Daftar eBrosur');
		$this->set(array(
			'ebrosurs' => $ebrosurs,
			'module_title' => $module_title,
			'module_title' => $module_title,
			'active_menu' => 'e_brosur_list',
		));

		$this->RmCommon->_callDataForAPI($ebrosurs, 'manual');

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	function admin_delete_multiple_ebrosurs(){
		$data = $this->request->data;

		$media_id = $this->RmCommon->filterEmptyField($this->params->params, 'named', 'media_id');
		if(!empty($media_id) && $this->Rest->isActive()){
			$media_id = explode(',', $media_id);
		}else{
			$media_id = array();
		}

		$id = $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'id', $media_id);
		
    	$result = $this->UserCompanyEbrochure->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));

		$this->RmCommon->renderRest();
	}

	function admin_detail($id = false){
		if(!empty($id)){
        	$user_admin = Configure::read('User.Admin.Rumahku');
			$detail = $this->UserCompanyEbrochure->getData('first', array(
				'conditions' => array(
					'UserCompanyEbrochure.id' => $id
				),
			), array(
				'mine' => true,
				'admin' => $user_admin,
			));

			$detail = $this->UserCompanyEbrochure->getMergeList($detail);

			if(!empty($detail['UserCompanyEbrochure']['ebrosur_photo'])){
			//	$ebrosur_photo = $detail['UserCompanyEbrochure']['ebrosur_photo'];
			//	$filename = APP.'webroot'.DS.str_replace('/', DS, 'img/view/ebrosur/xl'.$ebrosur_photo);
			
			//	if(!file_exists($filename)){
			//		$detail = $this->RmEbroschure->_callBeforeSave($detail, $this->data_company, $this->data_company, $this->Auth->user());
					
			//		$this->UserCompanyEbrochure->doSave($detail['data']);

			//		$detail = $detail['data'];
			//	}

				$detail = $this->User->Property->PropertyType->getMerge($detail, $detail['UserCompanyEbrochure']['property_type_id'], 'PropertyType.id', array(
                    'cache' => array(
                        'name' => __('PropertyType.%s', $detail['UserCompanyEbrochure']['property_type_id']),
                    ),
                ));
				
				$neighbors = $this->UserCompanyEbrochure->getNeighbor($id);
				
				$module_title = __('Daftar eBrosur / <span class="sub-crumb">Lihat eBrosur</span>');
				$sdkscript = true;

				$this->set(array(
					'neighbors' => $neighbors,
					'module_title' => $module_title,
					'sdkscript' => $sdkscript,
					'active_menu' => 'e_brosur_list',
				));
			}else{
				$this->RmCommon->setProcessParams(array(
					'msg' => __('Ebrosur tidak ditemukan.'),
					'status' => 'error'
				), array(
					'controller' => 'ebrosurs',
					'action' => 'index',
					'admin' => true
				), array(
					'redirectError' => true,
				));
			}

			$propertyTypes = $this->User->Property->PropertyType->getData('list', array(
	            'cache' => __('PropertyType.List'),
	        ));
			$this->RmCommon->_callDataForAPI($detail);

			$this->set(array(
				'active_menu' => 'e_brosur_list',
				'detail' => $detail,
				'propertyTypes' => $propertyTypes,
			));
		}

		$this->RmCommon->renderRest();
	}

	function client_index(){
		$this->loadModel('EbrosurRequest');
		$this->loadModel('CronjobPeriod');

		$options =  $this->EbrosurRequest->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_pagination'),
			'conditions' => array(
				'EbrosurRequest.user_id' => $this->user_id
			),
			'limit' => 9
		));
		
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->EbrosurRequest->getData('paginate', $options);

		$ebrosurs = $this->paginate('EbrosurRequest');

		if(!empty($ebrosurs)){
			foreach ($ebrosurs as $key => $value) {
				$certificate_id = $this->RmCommon->filterEmptyField($value, 'EbrosurRequest', 'certificate_id');
				$property_direction_id = $this->RmCommon->filterEmptyField($value, 'EbrosurRequest', 'property_direction_id');

				$value = $this->EbrosurRequest->EbrosurAgentRequest->getMerge($value, 5);
				$value = $this->EbrosurRequest->EbrosurTypeRequest->getMerge($value);
				$value = $this->User->Property->Certificate->getMerge($value, $certificate_id, false, 'Certificate.id', array(
	    			'cache' => array(
	    				'name' => __('Certificate.%s', $certificate_id)
    				),
    			));
				$value = $this->User->Property->PropertyAsset->PropertyDirection->getMerge($value, $property_direction_id);

				$ebrosurs[$key] = $this->EbrosurRequest->getMergeDefault($value);
			}
		}

		$periods = $this->CronjobPeriod->getData('list');
		$module_title = __('eBrosur');

		$this->set('active_menu', 'ebrosur');

		$this->set(compact(
			'module_title', 'ebrosurs', 'periods', 'active_menu'
		));
	}

	function client_add(){
		$step = $this->basicLabel;
		$step_word = __('Properti apa yang Anda inginkan?');
		$dataBasic = $this->_callSessionEbrosurRequest($step);

		$data = $this->request->data;
		$result = $this->User->EbrosurRequest->doBasic( $data, $dataBasic, true );
		
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'ebrosurs',
			'action' => 'add_specification',
			'client' => true,
		));
		$this->_callDataSupport($step);

		$this->set('active_menu', 'ebrosur');

		$this->set(compact(
			'step', 'step_word'
		));
	}

	function client_add_specification(){
		$step = __('Specification');
		$step_word = __('Spesifikasi properti apa yang Anda harapkan?');
		$dataSpecification = $this->_callSessionEbrosurRequest($step);
		
		$data = $this->request->data;
		$data = $this->RmEbroschure->_callBeforeSaveSpesification($data);

		$result = $this->User->EbrosurRequest->doSpecification( $data, $dataSpecification, true );

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'ebrosurs',
			'action' => 'add_agent',
			'client' => true,
		));

		$urlBack = array(
            'controller' => 'ebrosurs',
            'action' => 'add',
			'client' => true,
        );
		
		$this->_callDataSupport($step);

		$this->set(array(
			'active_menu' => 'ebrosur',
			'step' => $step,
			'step_word' => $step_word,
			'urlBack' => $urlBack,
		));
		$this->render('client_add');
	}

	function client_add_agent(){
		$step = __('Agent');
		$step_word = __('Anda dapat memilih agen properti kami');
		$dataAgent = $this->_callSessionEbrosurRequest($step);
		
		$data = $this->request->data;

		$result = $this->User->EbrosurRequest->doAgent( $data, $dataAgent, true );

		if(!empty($result['status']) && $result['status'] == 'success'){
			$dataAgent = $this->_callSessionEbrosurRequest();
			
			$result = $this->User->EbrosurRequest->doSave( $dataAgent, false, false );

			if(!empty($result['status']) && $result['status'] == 'success'){
				$this->RmEbroschure->_callDeleteSession();
			}
		}

		$url_referer = false;
		if(!empty($result['redirect'])){
			$url_referer = $result['redirect'];
		}

		$this->RmCommon->setProcessParams($result, $url_referer, array(
			'redirectError' => true
		));
		
		$urlBack = array(
            'controller' => 'ebrosurs',
            'action' => 'add_specification',
			'client' => true,
        );

		$this->_callDataSupport($step);

		$this->set('active_menu', 'ebrosur');

		$this->RmCommon->_layout_file('scroll_paginate');

		$this->set(compact(
			'step', 'step_word', 'urlBack'
		));

		if($this->is_ajax){
			$this->layout = false;
			$this->render('ajax_user_paginate');
		}else{
			$this->render('client_add');
		}
	}

	function client_success($id = false){
		$eBrosur = $this->User->EbrosurRequest->getData('first', array(
			'conditions' => array(
				'EbrosurRequest.id' => $id
			),
			'contain' => array(
				'CronjobPeriod'
			)
		));

		if(!empty($eBrosur['CronjobPeriod']['slug'])){
			$period = $eBrosur['CronjobPeriod']['slug'];
		}else{
			$this->RmCommon->redirectReferer(__('Permintaan ebrosur tidak ditemukan.'), 'error', array(
				'controller' => 'ebrosurs',
				'action' => 'index',
				'client' => true,
				'admin' => false
			));
		}

		$this->set('period', $period);
	}

	function admin_request_success($id = false){
		$eBrosur = $this->User->EbrosurRequest->getData('first', array(
			'conditions' => array(
				'EbrosurRequest.id' => $id
			),
			'contain' => array(
				'CronjobPeriod'
			)
		));
		
		if(!empty($eBrosur['CronjobPeriod']['slug'])){
			$period = $eBrosur['CronjobPeriod']['slug'];
			
			$this->set('period', $period);
		}else{
			$this->RmCommon->redirectReferer(__('Permintaan ebrosur tidak ditemukan.'), 'error', array(
				'controller' => 'ebrosurs',
				'action' => 'request_ebrosurs',
				'client' => false,
				'admin' => true
			));
		}
	}

	function _callDataSupport ( $step = false, $data = false ) {
		$this->loadModel('Property');
		
		if ( $step == 'Basic' ) {
			$this->loadModel('CronjobPeriod');

			$propertyActions = $this->Property->PropertyAction->getData('list', array(
	            'cache' => __('PropertyAction.List'),
	        ));
			$propertyTypes = $this->Property->PropertyType->getData('list', array(
	            'cache' => __('PropertyType.List'),
	        ));
			$periods = $this->CronjobPeriod->getData('list');

			if( !empty($this->request->data['PropertyAddress']['city_id']) ) {
				$city_id = $this->RmCommon->filterEmptyField($this->request->data, 'PropertyAddress', 'city_id');
				$subareas = $this->User->UserProfile->Subarea->getSubareas('list', false, $city_id);
			}

			if(Configure::read('User.admin')){
				$options = array();
				$auth_id = Configure::read('User.id');

				$this->User->virtualFields['email_name'] = 'CONCAT(User.first_name, \' \', IFNULL(User.last_name, \'\'),  \' | \', User.email)';

				$data_arr = $this->User->getUserParent($auth_id);
				$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
				$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

				if($is_sales){
					$options['conditions']['User.id'] = $user_ids;
				}
				$agents = $this->User->getData('list', array_merge_recursive($options, array(
					'fields' => array(
						'User.id', 'User.email_name'
					),
					'order' => array(
						'User.full_name' => 'ASC'
					)
				)), array(
					'status' => 'semi-active',
					'role' => 'agent',
					'company' => true,
				));
				
				$this->set('agents', $agents);
			}
		} else if ( $step == 'Specification' ) {
			$property_type_id = $this->RmCommon->filterEmptyField($data, 'Property', 'property_type_id');
			$property_action_id = $this->RmCommon->filterEmptyField($data, 'Property', 'property_action_id');

			$certificates = $this->Property->Certificate->getData('list', false, array(
				'property_type_id' => $property_type_id,
			));
			$propertyDirections = $this->Property->PropertyAsset->PropertyDirection->getData('list', array(
	            'cache' => __('PropertyDirection.List'),
	        ));
			$currencies = $this->Property->Currency->getData('list', array(
				'fields' => array(
					'Currency.id', 'Currency.alias',
				),
				'cache' => __('Currency.alias'),
			));
		} else if ( $step == 'Agent' ) {
			$dataBasic = $this->RmEbroschure->_callDataSession( $this->basicLabel );

			$prefix = Configure::read('App.prefix');

			if($prefix == 'admin'){
				$field = 'agent_id';
				$foreign_key = 'user_id';
			}else{
				$field = 'user_id';
				$foreign_key = 'agent_id';
			}

			$user_id = $this->user_id;
			
			if(Configure::read('User.group_id') != 2){
				$user_id = $this->RmCommon->filterEmptyField($data, 'EbrosurRequest', 'user_id');
				$user_id = $this->RmCommon->filterEmptyField($dataBasic, 'EbrosurRequest', 'user_id', $user_id);
			}

			$default_contain = array(
				'User' => array(
					'className' => 'User',
					'foreignKey' => $foreign_key,
				),
			);
			$default_order = array(
				'User.full_name' => 'ASC'
			);
			$default_condition = array(
				'UserClientRelation.'.$field => $user_id,
				'User.status' => true,
				'User.deleted' => false,
			);

			if(Configure::write('User.admin') && $prefix == 'admin'){
				$default_contain = array_merge($default_contain, array('UserClient'));
				$default_order = array(
					'UserClient.full_name' => 'ASC'
				);

				/* ini di komen karena di UserClientRelation udah di relasikan dengan company_id */
				// $default_condition['UserClient.company_id'] = Configure::read('Principle.id');
				$default_condition['UserClient.status'] = 1;
			}

			$this->loadModel('UserClientRelation');
			$options = $this->UserClientRelation->_callRefineParams($this->params, array(
				'conditions' => $default_condition,
				'contain' => $default_contain,
				'order' => $default_order,
				'group' => array(
					'UserClientRelation.'.$foreign_key
				),
				'limit' => 21
			));
			
			$this->RmCommon->_callRefineParams($this->params);

			$this->paginate = $this->UserClientRelation->getData('paginate', $options);
			$agents = $this->paginate('UserClientRelation');
			
			foreach( $agents as $key => $value ) {
				$user_id = $this->RmCommon->filterEmptyField($value, 'UserClientRelation', $foreign_key);
				$value = $this->User->UserProfile->getMerge( $value, $user_id );

				if( !empty($agents) && $prefix == 'client' ) {
					$value['User']['count_property'] = $this->User->Property->getData('count', array(
						'conditions' => array(
							'Property.user_id' => $user_id
						)
					), array(
						'status' => 'active-pending'
					));
				}else{
					$client_type_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'client_type_id');

					$value = $this->User->ClientType->getMerge($value, $client_type_id);
				}

				$agents[$key] = $value;
			}
			
			if($this->Rest->isActive()){
				$this->set('list_user', $agents);
			}else{
				$this->set('agents', $agents);
			}
		}

		$this->set(compact(
			'propertyActions', 'propertyTypes',
			'subareas', 'propertyDirections',
			'currencies', 'periods', 'certificates'
		));
	}

	function _callSessionEbrosurRequest ( $step = false ) {
        $dataBasic = $this->RmEbroschure->_callDataSession( $this->basicLabel );
        $dataSpecification = $this->RmEbroschure->_callDataSession( $this->spesificationLabel );
        $dataAgent = $this->RmEbroschure->_callDataSession( $this->agentLabel );

        if( empty($dataBasic) && $step == 'Specification' ) {
        	$this->RmCommon->redirectReferer(__('Mohon lengkapi info dasar permintaan eBrosur Anda'), 'error', array(
				'controller' => 'ebrosurs',
				'action' => 'add',
				'client' => true,
				'admin' => false
			));
        } else if( empty($dataSpecification) && $step == 'Agent' ) {
        	$this->RmCommon->redirectReferer(__('Mohon lengkapi info alamat permintaan eBrosur Anda'), 'error', array(
				'controller' => 'ebrosurs',
				'action' => 'add_specification',
				'client' => true,
				'admin' => false
			));
        } else {
			$session_id = $this->RmCommon->filterEmptyField($dataBasic, 'EbrosurRequest', 'session_id', String::uuid());
			$property_type_id = $this->RmCommon->filterEmptyField($dataBasic, 'EbrosurRequest', 'property_type_id');
			$property_action_id = $this->RmCommon->filterEmptyField($dataBasic, 'EbrosurRequest', 'property_action_id');

			$data = $dataBasic;

			if(!empty($dataSpecification['EbrosurRequest'])){
				$data['EbrosurRequest'] = array_merge($data['EbrosurRequest'], $dataSpecification['EbrosurRequest']);
			}

			if(!empty($dataAgent)){
				$data = array_merge($data, $dataAgent);
			}

			$this->set(compact(
				'session_id', 'dataBasic',
				'dataSpecification', 'dataAgent'
			));

			return $data;
		}
	}

	function download($id, $path){
		if(!empty($id) && !empty($path)){
			switch ($path) {
				case Configure::read('__Site.ebrosurs_photo'):
					$detail = $this->UserCompanyEbrochure->getData('first', array(
						'conditions' => array(
							'UserCompanyEbrochure.id' => $id
						)
					), array(
						'mine' => true,
					));

					if(!empty($detail['UserCompanyEbrochure']['ebrosur_photo'])){
						$file_name = $detail['UserCompanyEbrochure']['ebrosur_photo'];
						$filepath = $this->RmImage->fileExist(Configure::read('__Site.ebrosurs_photo'), 'xl', $file_name);
					}
				break;
			}
			
			if( !empty($filepath) ) {
	            $this->set(compact('filepath'));

	            $this->layout = false;
	            $this->render('/Elements/blocks/common/download');
	        } else {
	            $this->RmCommon->redirectReferer(__('File eBrosur tidak ditemukan.'), 'error');
	        }
	    }else{
	    	$this->RmCommon->redirectReferer(__('File eBrosur tidak ditemukan.'), 'error');
	    }
	}

	function index(){
	//	cache setting
        $companyData = Configure::read('Config.Company.data');
        $company_group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');
		$is_ebrosur_frontend = $this->RmCommon->filterEmptyField($companyData, 'UserCompanyConfig', 'is_ebrosur_frontend');

		if( !empty($is_ebrosur_frontend) ) {
			$controller		= $this->name;
			$action			= Inflector::camelize($this->action);
			$dataCompany	= isset($this->data_company) ? $this->data_company : NULL;
			$companyID		= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompany', 'id', 0);
			$currentPage	= $this->RmCommon->filterEmptyField($this->params, 'named', 'page', 1);
			$cacheName		= $controller.'.'.$action.'.'.$companyID.'.'.$currentPage;
			$cacheConfig	= 'ebrosurs_find';
			// $cacheData		= Cache::read($cacheName, $cacheConfig);
			$namedParams	= array_keys($this->params['named']);
			$nonFilter		= array('page');
			$filterParams	= array_diff($namedParams, $nonFilter);
			$options = array(
				'limit' => 21,
				'conditions' => array(
					'UserCompanyEbrochure.status' => true,
				),
				'order' => array(
					'UserCompanyEbrochure.id' => 'DESC',
					'UserCompanyEbrochure.name' => 'ASC',
				),
			);

			if( $company_group_id == 4 ) {
				$principle_id = $this->User->getAgents($this->parent_id, true, 'list', false, array(
					'role' => 'principle',
				));
				$options['conditions']['User.parent_id'] = $principle_id;
				$options['contain'][] = 'User';
			} else {
				$agents = $this->User->getAgents($this->parent_id, true, 'list', false, array(
					'role' => 'all',
					'skip_is_sales' => true,
				));
				$options['conditions']['or'] = array(
					array(
						'UserCompanyEbrochure.user_id' => $this->parent_id,
					),
					array(
						'UserCompanyEbrochure.user_id' => $agents,
					),
				);
			}

			$options =  $this->UserCompanyEbrochure->_callRefineParams($this->params, $options);

			$is_search = $options['is_search'];
			unset($options['is_search']);
			
			$this->RmCommon->_callRefineParams($this->params);
			
			if(empty($filterParams) && !empty($cacheData)){
			//	find all query, get results from cache (if exist)
				$this->request->params['paging']	= $cacheData['paging'];
				$this->request->params['named']		= $cacheData['named'];
				$this->request->params['pass']		= $cacheData['pass'];
				$this->request->query				= $cacheData['query'];
				$brosurs							= $cacheData['result'];
			}else{
				$this->paginate = $this->UserCompanyEbrochure->getData('paginate', $options, array(
					'mine' => false,
					'admin' => true,
					'company' => false,
					'is_sales' => false,
					'status' => 'active',
				));
				
				$brosurs = $this->paginate('UserCompanyEbrochure');

				if(empty($filterParams)){
				//	find all query, generate cache
					$cacheData = array(
						'paging'	=> $this->request->params['paging'], 
						'named'		=> $this->request->params['named'], 
						'pass'		=> $this->request->params['pass'], 
						'query'		=> $this->request->query, 
						'result'	=> $brosurs
					);

					Cache::write($cacheName, $cacheData, $cacheConfig);
				}
			}

			$this->RmCommon->_callRequestSubarea('Search');
			$this->RmCommon->_layout_file('ebrosur');

			$module_title 	= __('DAFTAR E-BROSUR');
			$pageNum 		= $this->RmCommon->filterEmptyField($this->passedArgs, 'page', false, 1);
			$autoplay = $this->RmCommon->filterEmptyField($_GET, 'autoplay');
			$propertyActions = $this->UserCompanyEbrochure->Property->PropertyAction->getData('list', array(
	            'cache' => __('PropertyAction.List'),
	        ));
			$propertyTypes = $this->UserCompanyEbrochure->Property->PropertyType->getData('list', array(
	            'cache' => __('PropertyType.List'),
	        ));
			$propertyDirections = $this->User->Property->PropertyAsset->PropertyDirection->getData('list', array(
	            'cache' => __('PropertyDirection.List'),
	        ));
			$certificates = $this->User->Property->Certificate->getData('list', array(
	            'cache' => __('Certificate.List'),
			));

			$is_interval = false;

			if( !$is_search && !empty($pageNum) && $pageNum == 1){
				$is_interval = true;
			}

			$url_without_http = Configure::read('__Site.domain');

			$title_for_layout 		= sprintf('Ebrosurs %s', $url_without_http);
			$description_for_layout = sprintf(__('ebrosur di %s dengan harga properti terjangkau!'), $url_without_http);
			$keywords_for_layout	= sprintf(__('ebrosur Properti di %s'), $url_without_http);
			
			$this->set(compact(
				'brosurs', 'module_title', 'is_interval',
				'pageNum', 'autoplay', 'propertyActions',
				'propertyTypes', 'propertyDirections',
				'certificates', 'title_for_layout',
				'description_for_layout', 'keywords_for_layout'
			));
		} else {
			$this->redirect('/');
		}
	}

	function detail($id, $print = false){
		if(!empty($id)){
			$this->layout = false;

		//	cache setting
			$controller		= $this->name;
			$action			= Inflector::camelize($this->action);
			$dataCompany	= isset($this->data_company) ? $this->data_company : NULL;
			$companyID		= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompany', 'id', 0);
			$cacheName		= $controller.'.'.$action.'.'.$companyID.'.'.$id;
			$cacheConfig	= 'ebrosurs_detail';
			$cacheData		= Cache::read($cacheName, $cacheConfig);

			if($cacheData){
				$this->request->params['named']	= $cacheData['named'];
				$this->request->params['pass']	= $cacheData['pass'];
				$this->request->query			= $cacheData['query'];
				$detail							= $cacheData['result'];
			}
			else{
				$detail = $this->UserCompanyEbrochure->getData('first', array(
					'conditions' => array(
						'UserCompanyEbrochure.id' => $id
					),
				));

				$cacheData = array(
					'named'		=> $this->request->params['named'], 
					'pass'		=> $this->request->params['pass'], 
					'query'		=> $this->request->query, 
					'result'	=> $detail
				);

				Cache::write($cacheName, $cacheData, $cacheConfig);
			}

			if(!empty($detail)){
				$ebrosur_photo = $this->RmCommon->filterEmptyField($detail, 'UserCompanyEbrochure', 'ebrosur_photo');
				$property_title = $this->RmCommon->filterEmptyField($detail, 'UserCompanyEbrochure', 'property_title');
				$description = $this->RmCommon->filterEmptyField($detail, 'UserCompanyEbrochure', 'description');
				$property_type_id = $this->RmCommon->filterEmptyField($detail, 'UserCompanyEbrochure', 'property_type_id');

				$detail = $this->User->Property->PropertyType->getMerge($detail, $property_type_id, 'PropertyType.id', array(
                    'cache' => array(
                        'name' => __('PropertyType.%s', $property_type_id),
                    ),
                ));

				if(!empty($detail['PropertyType']['name'])){
					$property_title = sprintf('%s %s', $detail['PropertyType']['name'], $property_title);
				}

				$og_meta = array(
	                'title' => $property_title,
	                'image' => $ebrosur_photo,
	                'path' => Configure::read('__Site.ebrosurs_photo'),
	                'description' => $description,
	                'size' => 'm', // 'xl', whatssapp cuma mau gambar <= 300kb, jangan dikasih xl
	            );

				$this->set(compact('detail', 'print', 'og_meta', 'property_title'));
			}else{
				$this->RmCommon->redirectReferer(__('Ebrosur tidak ditemukan.'), 'error', array(
					'controller' => 'ebrosurs',
					'action' => 'index',
					'admin' => false,
				));
			}
		}else{
			$this->RmCommon->redirectReferer(__('Ebrosur tidak ditemukan.'), 'error', array(
				'controller' => 'ebrosurs',
				'action' => 'index',
				'admin' => false,
			));
		}
	}

	function client_delete_multiple(){
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'EbrosurRequest', 'id');
		$this->loadModel('EbrosurRequest');
		
    	$result = $this->EbrosurRequest->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_delete_multiple(){
		$data = $this->request->data;

		$media_id = $this->RmCommon->filterEmptyField($this->params->params, 'named', 'media_id');
		if(!empty($media_id) && $this->Rest->isActive()){
			$media_id = explode(',', $media_id);
		}else{
			$media_id = array();
		}

		$id = $this->RmCommon->filterEmptyField($data, 'EbrosurRequest', 'id', $media_id);
		$this->loadModel('EbrosurRequest');
		
    	$result = $this->EbrosurRequest->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_mail($id){
		$result = array();
		if(!empty($id)){
			$ebrosur = $this->UserCompanyEbrochure->getData('first', array(
				'contain'		=> array('Region', 'City', 'Subarea'), 
				'conditions'	=> array(
					'UserCompanyEbrochure.id' => $id, 
				), 
			), array(
				'company' => true
			));

			if(!empty($ebrosur)){
				$property_type_id = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'property_type_id');
				$property_action_id = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'property_action_id');
				$currency_id = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'currency_id');

				$ebrosur = $this->User->Property->PropertyType->getMerge($ebrosur, $property_type_id, 'PropertyType.id', array(
                    'cache' => array(
                        'name' => __('PropertyType.%s', $property_type_id),
                    ),
                ));
				$ebrosur = $this->User->Property->PropertyAction->getMerge($ebrosur, $property_action_id, 'PropertyAction.id', array(
					'cache' => array(
						'name' => __('PropertyAction.%s', $property_action_id),
					),
				));
				$ebrosur = $this->User->Property->Currency->getMerge($ebrosur, $currency_id, 'Currency.id', array(
					'cache' => array(
						'name' => __('Currency.%s', $currency_id),
					),
				));

				$data	= $this->request->data;
				$result	= $this->UserCompanyEbrochure->sendMail($data, $ebrosur, $this->data_company);
				$status	= Common::hashEmptyField($result, 'status');

				if($status == 'success'){
				//	open listing : send notification and email to property owner if logged in user not equal property owner
					$notifications = $this->UserCompanyEbrochure->prepareNotification($ebrosur, 'email');

					if($notifications){
						$notificationEmails	= Common::hashEmptyField($notifications, 'SendEmail', array());
						$notifications		= Hash::remove($notifications, 'SendEmail');

						$result['SendEmail'][] = $notificationEmails;
						$result = array_merge($result, $notifications);
					}
				}
			}else{
				$this->RmCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
			}

			$this->set('ebrosur', $ebrosur);
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => false,
			'ajaxFlash' => true
		));
	}

	function admin_request_add(){
		$step = $this->basicLabel;
		$step_word = __('Properti apa yang Anda inginkan?');
		$dataBasic = $this->_callSessionEbrosurRequest($step);

		$data = $this->request->data;
		$result = $this->User->EbrosurRequest->doBasic( $data, $dataBasic, true );
		
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'ebrosurs',
			'action' => 'add_specification',
			'admin' => true,
		));
		$this->_callDataSupport($step);

		if(Configure::read('User.admin')){
			$urlBack = array(
	            'controller' => 'ebrosurs',
	            'action' => 'add_agent',
				'admin' => true,
	        );

	        $this->set('urlBack', $urlBack);
		}

		$this->set(array(
			'step' => $step,
			'step_word' => $step_word,
			'active_menu' => 'e_brosur_request',
		));
	}

	function admin_add_specification(){
		$step = __('Specification');
		$step_word = __('Spesifikasi properti apa yang Anda harapkan?');
		$dataSpecification = $this->_callSessionEbrosurRequest($step);
			
		$data = $this->request->data;
		$data = $this->RmEbroschure->_callBeforeSaveSpesification($data);

		$result = $this->User->EbrosurRequest->doSpecification( $data, $dataSpecification, true );

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'ebrosurs',
			'action' => 'add_client',
			'admin' => true,
		));

		$urlBack = array(
            'controller' => 'ebrosurs',
            'action' => 'request_add',
			'admin' => true,
        );
		
		$this->_callDataSupport($step);

		$this->set(array(
			'active_menu' => 'e_brosur_request',
			'step' => $step,
			'step_word' => $step_word,
			'urlBack' => $urlBack,
		));
		$this->render('admin_request_add');
	}

	function admin_add_client(){
		$step = __('Agent');
		$step_word = __('Pilih klien yang akan menerima eBrosur');
		$dataAgent = $this->_callSessionEbrosurRequest($step);

		$data = $this->request->data;

		$result = $this->User->EbrosurRequest->doClient( $data, $dataAgent, true );

		if(!empty($result['status']) && $result['status'] == 'success'){
			$dataAgent = $this->_callSessionEbrosurRequest();
			$result = $this->User->EbrosurRequest->doSaveClient( $dataAgent, false, false );
			
			if(!empty($result['status']) && $result['status'] == 'success'){
				$this->RmEbroschure->_callDeleteSession();
			}
		}

		$url_referer = false;
		if(!empty($result['redirect'])){
			$url_referer = $result['redirect'];
		}

		$this->RmCommon->setProcessParams($result, $url_referer, array(
			'redirectError' => true
		));
		
		$urlBack = array(
            'controller' => 'ebrosurs',
            'action' => 'add_specification',
			'admin' => true,
        );

		$this->_callDataSupport($step);

		$this->RmCommon->_layout_file('scroll_paginate');

		$this->set(array(
			'active_menu' => 'e_brosur_request',
			'step' => $step,
			'step_word' => $step_word,
			'urlBack' => $urlBack,
		));

		if($this->is_ajax){
			$this->set('type', 'klien');
			$this->layout = false;
			$this->render('ajax_user_paginate');
		}else{
			$this->render('admin_request_add');
		}
	}

	function admin_request_ebrosurs(){
		$this->loadModel('EbrosurRequest');
		$this->loadModel('CronjobPeriod');

		$user_id = $this->user_id;
		if(Configure::read('User.admin')){
			$user_id = $this->User->getAgents($this->parent_id, true, 'list', false, array('role' => 'all'));
		}

		$options =  $this->EbrosurRequest->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_pagination'),
			'conditions' => array(
				'EbrosurRequest.user_id' => $user_id
			),
			'limit' => 9
		));
		
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->EbrosurRequest->getData('paginate', $options);

		$ebrosurs = $this->paginate('EbrosurRequest');

		$count_client = 5;
		if($this->Rest->isActive()){
			$count_client = 6;
		}

		if(!empty($ebrosurs)){
			foreach ($ebrosurs as $key => $value) {

				$certificate_id = $this->RmCommon->filterEmptyField($value, 'EbrosurRequest', 'certificate_id');
				$property_direction_id = $this->RmCommon->filterEmptyField($value, 'EbrosurRequest', 'property_direction_id');

				$value = $this->EbrosurRequest->EbrosurClientRequest->getMerge($value, $count_client, true);
				$value = $this->EbrosurRequest->EbrosurTypeRequest->getMerge($value, true);
				$value = $this->User->Property->Certificate->getMerge($value, $certificate_id, false, 'Certificate.id', array(
	    			'cache' => array(
	    				'name' => __('Certificate.%s', $certificate_id)
    				),
    			));
				$value = $this->User->Property->PropertyAsset->PropertyDirection->getMerge($value, $property_direction_id, false, 'PropertyDirection.id', true);

				$ebrosurs[$key] = $this->EbrosurRequest->getMergeDefault($value, true);
			}
		}

		$this->RmCommon->_callDataForAPI($ebrosurs, 'manual');

		$periods = $this->CronjobPeriod->getData('list');
		$module_title = __('eBrosur');

		$this->set(array(
			'active_menu' => 'e_brosur_request',
			'module_title' => __('eBrosur'),
			'ebrosurs' => $ebrosurs,
			'periods' => $periods,
		));

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	public function admin_regenerate($ebrochureID = false){
		$companyData	= Common::config('Config.Company.data', []);
		$templateID		= Common::hashEmptyField($companyData, 'UserCompanyConfig.ebrochure_template_id', 0);
		$result			= array();

		$principleID	= Common::config('Principle.id', 0);
		$template		= $this->User->UserCompanyConfig->EbrochureTemplate->getData('first', array(
			'order'			=> array('EbrochureTemplate.is_default' => 'asc'), 
			'conditions'	=> array(
				'or' => array(
					'EbrochureTemplate.id' => $templateID, 
				//	kalo belum setting template, pake default setting prime
					array(
						'EbrochureTemplate.principle_id'	=> array(0, $principleID), 
						'EbrochureTemplate.is_default'		=> 1,  
					), 
				), 
			), 
		));

		if($template){
		//	setelah create property langsung auto create ebrochure (tergantung setting company)
			$propertyID	= Common::hashEmptyField($this->params->named, 'property_id');
			$ebrochure	= array();
			$layoutData	= array();

			if($ebrochureID){
				$isAdmin	= Configure::read('User.Admin.Rumahku');
				$ebrochure	= $this->UserCompanyEbrochure->getData('first', array(
					'conditions' => array(
						'UserCompanyEbrochure.id' => $ebrochureID, 
					),
				), array(
					'admin'				=> $isAdmin, 
					'mine'				=> true, 
				));
			}

			if($ebrochure || $propertyID){
				if($ebrochure){
					$ebrochure	= $this->RmEbroschure->getMergeData($ebrochure);
					$propertyID	= Common::hashEmptyField($ebrochure, 'UserCompanyEbrochure.property_id', 0);
					$layout		= Common::hashEmptyField($ebrochure, 'UserCompanyEbrochure.layout', '');
					$layoutData	= $layout ? json_decode($layout, true) : array();
				}

				$templateLayout	= Common::hashEmptyField($template, 'EbrochureTemplate.layout', '');
				$templateData	= $templateLayout ? json_decode($templateLayout, true) : array();

			//	replace layout
				$layoutData = $this->RmEbroschure->replaceLayout($layoutData, $templateData, array(
					'property_id' => $propertyID, 
				));

			//	append layout to ebrochure (after replaced)
				$ebrochure = Hash::insert($ebrochure, 'UserCompanyEbrochure.property_id', $propertyID);
				$ebrochure = Hash::insert($ebrochure, 'UserCompanyEbrochure.layout', json_encode($layoutData));

				$this->RmEbroschure->callBeforeRegenerateEbrochure($ebrochure);
				$this->RmEbroschure->setBuilderData($ebrochure);

			//	set default data
				$this->_callCreateEbrosurSupport($ebrochure);
				$this->set(array(
					'ebrochure'		=> $ebrochure, 
					'regenerate'	=> true, 
				));

				App::uses('Folder', 'Utility');
				App::uses('File', 'Utility');

				$this->layout = 'ebrochure_builder';
				$this->render('ebrochure_builder');
			}
			else{
				$this->RmCommon->redirectReferer(__('Ebrosur tidak ditemukan.'), 'error', array(
					'admin'			=> true, 
					'controller'	=> 'ebrosurs',
					'action'		=> 'index',
				));
			}
		}
		else{
			$this->RmCommon->redirectReferer(__('Template perusahaan belum ditentukan, silakan hubungi Administrator Anda.'), 'error', array(
				'admin'			=> true, 
				'controller'	=> 'ebrosurs',
				'action'		=> 'index',
			));
		}
	}

//	ini regenerate versi lama
	function admin_generate($id = false){
		$result = array(
			'status' => 'error',
			'msg' => __('eBrosur tidak ditemukan.')
		);

		if(!empty($id)){
			$ebrosur = $this->UserCompanyEbrochure->getData('first', array(
				'conditions' => array(
					'UserCompanyEbrochure.id' => $id
				)
			), array(
				'mine' => true
			));
			
			if(!empty($ebrosur)){
				$currency_id = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'currency_id');
				$ebrosur_photo = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'ebrosur_photo');
				$user_id = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'user_id');

				$ebrosur = $this->User->getMerge($ebrosur, $user_id);
				$ebrosur = $this->User->UserProfile->getMerge($ebrosur, $user_id);

				$name = $this->RmCommon->filterEmptyField($ebrosur, 'User', 'full_name');
				$phone = $this->RmCommon->filterEmptyField($ebrosur, 'UserProfile', 'no_hp');

				$user = array();
				if(!empty($ebrosur['User'])){
					$user = $ebrosur['User'];
				}

				$ebrosur['UserCompanyEbrochure']['name'] = $name;
				$ebrosur['UserCompanyEbrochure']['phone'] = $phone;

				$ebrosur = $this->User->Property->Currency->getMerge($ebrosur, $currency_id, 'Currency.id', array(
					'cache' => array(
						'name' => __('Currency.%s', $currency_id),
					),
				));

				$ebrosur['UserCompanyEbrochure']['_property_photo'] = $ebrosur_photo;

				$new_ebrosur_photo = $this->RmEbroschure->_callBeforeSave($ebrosur, $this->data_company, $this->data_company, $user, true);
				
				if(!empty($new_ebrosur_photo['data']['UserCompanyEbrochure']['ebrosur_photo']) && $new_ebrosur_photo['validate'] == false){
					$ebrosur['UserCompanyEbrochure']['ebrosur_photo'] = $new_ebrosur_photo['data']['UserCompanyEbrochure']['ebrosur_photo'];

					$result = $this->UserCompanyEbrochure->doSave($ebrosur);

					if(!empty($result['status']) && $result['status'] == 'success'){
						$dimension = array_keys($this->RmImage->_rulesDimensionImage(Configure::read('__Site.ebrosurs_photo')));

						$this->RmCommon->deletePathPhoto(Configure::read('__Site.ebrosurs_photo'), $ebrosur_photo, $dimension);
					}
				}
			}

			if($this->Rest->isActive()){
				$ebrosur_data['UserCompanyEbrochure']['ebrosur_photo'] = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'ebrosur_photo');
				
				$this->set('data', $ebrosur_data);
			}
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));

		$this->RmCommon->renderRest();
	}

	public function admin_info($recordID = NULL){
		$title = __('Daftar eBrosur');
		$user = $this->RmUser->getUser($recordID);

		if( !empty($user) ) {
			$ebrosurs = array();
			$this->RmUser->_callRoleActiveMenu($user);
			$options = $this->RmEbroschure->_callRoleCondition($user);

			if(!empty($options)){
				$ebrosurs = $this->RmEbroschure->_callBeforeViewEbrosurs($options, array(
					'mine' => false,
					'company' => false,
				));
			}

			$this->set(array(
				'module_title' => $title,
				'title_for_layout' => $title,
				'ebrosurs' => $ebrosurs,
				'currUser' => $user,
				'recordID' => $recordID,
				'active_tab' => 'eBrosur',
				'urlBack' => array(
					'controller' => 'users',
					'action' => 'directors',
					'admin' => true,
				),
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	function admin_api_request_add(){
		$data = $this->request->data;

		$data['EbrosurRequest']['session_id'] = $this->RmCommon->filterEmptyField($data, 'EbrosurRequest', 'session_id', String::uuid());

		$data = $this->RmEbroschure->_callBeforSaveRequestAPI($data);

		$result = $this->User->EbrosurRequest->doSaveAll($data);

		if(Configure::read('User.admin')){
				$this->User->virtualFields['email_name'] = 'CONCAT(User.first_name, \' \', IFNULL(User.last_name, \'\'),  \' | \', User.email)';

				$agents = $this->User->getData('list', array(
					'fields' => array(
						'User.id', 'User.email_name'
					),
					'order' => array(
						'User.full_name' => 'ASC'
					)
				), array(
					'status' => 'semi-active',
					'role' => 'agent',
					'company' => true,
				));
				
				$this->set('agents', $agents);
			}

		$this->RmCommon->setProcessParams($result, false);
	}

	function admin_api_list_target_request(){
		$this->request->data['EbrosurRequest']['user_id'] = 71060;

		$data = $this->request->data;

		$this->_callDataSupport('Agent', $data);

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	function admin_api_agent_clients($agent_id){
		$this->loadModel('UserClientRelation');

		$this->RmEbroschure->callAgentClient($agent_id);
	}

	public function admin_builder($ebrochureID = null){
		$type			= Common::hashEmptyField($this->params->named, 'type');
		$templateID		= Common::hashEmptyField($this->params->named, 'templateid');
		$ebrochure		= array();
		$template		= array();

		$type = in_array($type, array('ebrochure', 'template')) ? $type : 'ebrochure';

		if($type == 'template'){
			$ebrochureID = null;
		}
		else{
			if($ebrochureID && $templateID){
				$templateID = null;
			}
		}

		if($ebrochureID){
		//	edit ebrochure
			$isAdmin	= Configure::read('User.Admin.Rumahku');
			$ebrochure	= $this->UserCompanyEbrochure->getData('first', array(
				'conditions' => array(
					'UserCompanyEbrochure.id' => $ebrochureID, 
				),
			), array(
				'admin'				=> $isAdmin, 
				'mine'				=> true, 
			));

			if(empty($ebrochure)){
				$this->RmCommon->redirectReferer(__('Ebrosur tidak ditemukan.'), 'error', array(
					'action'	=> 'index',
					'admin'		=> true, 
				));
			}
		}
		else if($templateID){
		//	using template
			$this->loadModel('EbrochureTemplate');

		//	get selected template
			$template = $this->EbrochureTemplate->getData('first', array(
				'conditions' => array(
					'EbrochureTemplate.id' => $templateID, 
				), 
			), array(
				'company' => true, 
			));

			$authUserID		= Common::config('User.id');
			$isAdmin		= Common::validateRole('admin');
			$isCompanyAdmin	= Configure::read('User.admin');

			$userID			= Common::hashEmptyField($template, 'EbrochureTemplate.user_id');
			$principleID	= Common::hashEmptyField($template, 'EbrochureTemplate.principle_id');

			if( (($isAdmin || ($isCompanyAdmin && $principleID) || ($principleID && $authUserID == $userID)) && $type == 'template') || ( $type == 'ebrochure' ) ){
				$templateLayout	= Common::hashEmptyField($template, 'EbrochureTemplate.layout', '');
				$templateData	= $templateLayout ? json_decode($templateLayout, true) : array();
				$templateData	= $this->RmEbroschure->replaceLayout(array(), $templateData, array(
					'force_replace' => true, 
				));

			//	layout tetep masuk ke UserCompanyEbrochure
				$ebrochure = array('UserCompanyEbrochure' => array(
					'layout' => json_encode($templateData), 
				));
			}
			else{
				$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengubah template tersebut.'), 'error', array(
					'admin'		=> true, 
					'action'	=> 'templates',
				));
			}
		}

		if($ebrochure){
			$ebrochure = $this->RmEbroschure->getMergeData($ebrochure);
		}

		$this->RmEbroschure->callBeforeSaveBuilder($ebrochure);
		$this->RmEbroschure->setBuilderData($ebrochure);

	//	set default data
		$this->_callCreateEbrosurSupport($ebrochure);

		if($template){
			$templateID				= Common::hashEmptyField($template, 'EbrochureTemplate.id');
			$templateName			= Common::hashEmptyField($template, 'EbrochureTemplate.name');
			$templateDescription	= Common::hashEmptyField($template, 'EbrochureTemplate.description');

			$this->request->data = Hash::insert($this->request->data, 'EbrochureTemplate', array(
				'id'			=> $templateID,
				'name'			=> $templateName, 
				'description'	=> $templateDescription, 
			));
		}

		$this->set(array(
			'active_menu'	=> 'e_brosur_list',
			'module_title'	=> __('Ebrosur Builder'),
			'ebrochure'		=> $ebrochure, 
			'type'			=> $type, 
		));

		App::uses('Folder', 'Utility');
		App::uses('File', 'Utility');

		$this->layout = 'ebrochure_builder';
		$this->render('ebrochure_builder');
	}

	public function admin_templates(){
		$this->loadModel('EbrochureTemplate');
		$companyData = Configure::read('Config.Company.data');
		$isBuilder = Common::hashEmptyField($companyData, 'UserCompanyConfig.is_ebrochure_builder');

		if( !empty($isBuilder) ) {
			$limit		= Configure::read('__Site.config_new_table_pagination');
			$options	= $this->EbrochureTemplate->_callRefineParams($this->params->params, array(
				'limit' => $limit,
			));

			$this->RmCommon->_callRefineParams($this->params);
			$this->paginate	= $this->EbrochureTemplate->getData('paginate', $options, array(
				'company' => true, 
			));

			$records = $this->paginate('EbrochureTemplate');
			$records = $this->EbrochureTemplate->getMergeList($records, array(
				'contain' => array(
					'User', 
				), 
			));

			if($this->Rest->isActive() && empty($records)){
				$records = null;
			}

			$this->set(array(
				'module_title'	=> __('Daftar Template eBrosur'),
				'active_menu'	=> 'e_brosur_templates',
				'records'		=> $records,
			));

			$this->RmCommon->_callDataForAPI($records, 'manual');
			$this->RmCommon->renderRest(array(
				'is_paging' => true, 
			));
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman ini.'));
		}
	}

	public function admin_template_preview($recordID = null){
		$this->loadModel('EbrochureTemplate');

		$companyData = Configure::read('Config.Company.data');
		$isBuilder = Common::hashEmptyField($companyData, 'UserCompanyConfig.is_ebrochure_builder');
		$record = $this->EbrochureTemplate->getData('first', array(
			'conditions' => array(
				'EbrochureTemplate.id' => $recordID, 
			), 
		), array(
			'company' => true, 
		));

		if( !empty($isBuilder) && !empty($record) ){
			$neighbors = $this->EbrochureTemplate->find('neighbors', array(
				'field'			=> 'EbrochureTemplate.id', 
			//	'conditions'	=> $conditions,
				'value'			=> $recordID, 
			));

			$this->set(array(
				'module_title'	=> __('Pratinjau Template eBrosur'),
				'active_menu'	=> 'e_brosur_templates',
				'record'		=> $record, 
				'neighbors'		=> $neighbors, 
			));
		}
		else{
			$this->RmCommon->redirectReferer(__('Ebrosur tidak ditemukan.'), 'error', array(
				'admin'			=> true, 
				'controller'	=> 'ebrosurs',
				'action'		=> 'templates',
			));
		}
	}

	public function admin_template_delete(){
		$this->loadModel('EbrochureTemplate');

		$companyData = Configure::read('Config.Company.data');
		$isBuilder = Common::hashEmptyField($companyData, 'UserCompanyConfig.is_ebrochure_builder');

		if( !empty($isBuilder) ) {
			$recordID	= Common::hashEmptyField($this->data, 'EbrochureTemplate.id');
			$result		= $this->EbrochureTemplate->doToggle($recordID, 'delete');

			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman ini.'));
		}
	}
}