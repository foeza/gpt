<?php
App::uses('AppController', 'Controller');

class PropertiesController extends AppController {
	public $uses = array(
		'Property',
	);

	public $helpers = array(
		'FileUpload.UploadForm',
		'Property',
		'Paginator',
	);

	public $components = array(
		'RmMarketTrend', 'RmSetting',
		'RmProperty', 'Captcha', 'RmEbroschure',
		'RmCrm', 'RmImage',
		'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
	            'admin_index' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				     	'paging', 'properties'
				 	),
			 	),
	            'admin_refresh' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
			 	'admin_refresh_all' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_delete' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_sold' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_unsold' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_premium' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_unpremium' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_activate' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_api_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors',
				 	),
			 	),
	            'admin_api_edit' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors', 'data',
				 	),
			 	),
			 	'admin_edit_medias' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'dataMedias'
				 	),
			 	),
			 	'admin_api_photo' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'session_id', 'data'
				 	),
			 	),
			 	'admin_api_videos' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'dataVideos'
				 	),
			 	),
			 	'admin_api_document_upload' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'admin_document_delete' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'admin_document_edit' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'admin_api_detail' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'documents'
				 	),
			 	),
			 	'admin_api_find_properties' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				     	'paging', 'refresh_all', 'properties'
				 	),
			 	),
			 	'admin_deactivate' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'admin_sold_preview' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 
				  		'data', 'propertySold'
				 	),
			 	)
    		),
    	),
	);

	function beforeFilter() {
		parent::beforeFilter();
		$params = $this->params->params;
		$draft = Common::hashEmptyField($params, 'named.draft');

		$dataCompany	= isset($this->data_company) ? $this->data_company : NULL;
		$companyID		= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompany', 'id', 0);
		$this->Property->companyID = $companyID;
		$this->User->UserCompanyEbrochure->companyID = $companyID;

		$isShowTrend	= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'mt_is_show_trend');
		$allowedMethods	= array(
			'find', 'search', 'detail',
			'leads', 'admin_share',
			'shorturl', 'contact', 'price_movement',
		//	'admin_easy_add', 
		//	'admin_easy_preview', 
		//	'admin_easy_media', 
		);

		$marketTrendMethods = array(
			'admin_market_trend', 
			'market_trend', 
			'property_statistic', 
			'backprocess_area_stat', 
			'backprocess_area_summary', 
		);

		if($isShowTrend){
			$allowedMethods = array_merge($allowedMethods, $marketTrendMethods);
		}
		else{
			if(empty($isShowTrend) && in_array($this->action, $marketTrendMethods)){
				$this->RmCommon->redirectReferer(__('Halaman tidak ditemukan'), 'error');
			}
		}

		$this->Auth->allow($allowedMethods);

		if($draft){
			$active_menu = 'property_draft';
		} else {
			$active_menu = 'property_list';
		}

		$this->set('active_menu', $active_menu);
		$this->draft_id = Configure::read('__Site.PropertyDraft.id');
	}

	function admin_search ( $action = 'index', $_admin = true, $addParam = false ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			$addParam,
			'admin' => $_admin,
		);

		$this->RmCommon->processSorting($params, $data);
	}

	function client_search ( $action = 'index', $_client = true ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			'client' => $_client,
		);
		$this->RmCommon->processSorting($params, $data);
	}

	function search ( $action = 'index', $addParam = false ) {
		$this->admin_search($action, $addParam);
	}

	function _callDataSupport ( $step = false, $data = false ) {
		if( $step == $this->basicLabel ) {
			$propertyActions = $this->Property->PropertyAction->getData('list', array(
	            'cache' => __('PropertyAction.List'),
	        ));
			$propertyTypes = $this->Property->PropertyType->getData('list', array(
	            'cache' => __('PropertyType.List'),
	        ));
		} else if( $step == $this->assetLabel ) {
			$property_type_id = $this->RmCommon->filterEmptyField($data, 'Property', 'property_type_id');
			$property_action_id = $this->RmCommon->filterEmptyField($data, 'Property', 'property_action_id');
			$lot_unit_id = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'lot_unit_id');

			$is_lot = $this->RmCommon->filterEmptyField($data, 'PropertyType', 'is_lot');
			$is_space = $this->RmCommon->filterEmptyField($data, 'PropertyType', 'is_space');

			$data = $this->Property->PropertyAsset->LotUnit->getMerge($data, $lot_unit_id, 'LotUnit', false, array(
                'cache' => array(
                    'name' => __('LotUnit.%s', $lot_unit_id),
                ),
            ));
			$lot_unit_slug = $this->RmCommon->filterEmptyField($data, 'LotUnit', 'slug');

			if( !empty($data['LotUnit']) ) {
				$lotUnitName = $this->RmProperty->getLotUnit($lot_unit_slug, 'format', 'top');
			} else {
				$lotUnitName = $this->RmProperty->getLotUnit('m2', 'format', 'top');
			}

			$propertyConditions = $this->Property->PropertyAsset->PropertyCondition->getData('list', array(
	            'cache' => __('PropertyCondition.List'),
	        ));
			$propertyDirections = $this->Property->PropertyAsset->PropertyDirection->getData('list', array(
	            'cache' => __('PropertyDirection.List'),
	        ));
			$viewSites = $this->Property->PropertyAsset->ViewSite->getData('list', array(
				'conditions' => array(
					'ViewSite.property_type_id' => $property_type_id,
				),
			));

			$certificates = $this->Property->Certificate->getData('list', false, array(
				'property_type_id' => $property_type_id,
			));
			$currencies = $this->Property->Currency->getData('list', array(
				'fields' => array(
					'Currency.id', 'Currency.alias',
				),
				'cache' => __('Currency.alias'),
			));
			$facilities = $this->Property->PropertyFacility->Facility->getData('list');
			$periods = $this->Property->PropertyPrice->Period->getData('list', array(
	            'cache' => __('Period.List'),
	        ));

			if( !empty($is_space) ) {
				$lotUnits = $this->Property->PropertyAsset->LotUnit->getData('list', false, array(
					'property_action_id' => $property_action_id,
					'is_space' => $is_space,
				));
			} else {
				$lotUnits = $this->Property->PropertyAsset->LotUnit->getData('list', array(
					'fields' => array(
						'LotUnit.id', 'LotUnit.slug',
					),
					'group' => array(
						'LotUnit.slug',
					),
					'cache' => __('LotUnit.GroupSlug.List'),
				), array(
					'is_lot' => $is_lot,
				));
			}

			$this->set('type_commission', $this->RmCommon->getGlobalVariable('type_commision_cobroke'));
		} else if( $step == $this->addressLabel ) {
			if( !empty($this->request->data['PropertyAddress']['city_id']) ) {
				$city_id = $this->RmCommon->filterEmptyField($this->request->data, 'PropertyAddress', 'city_id');
				$subareas = $this->User->UserProfile->Subarea->getSubareas('list', false, $city_id);
			}
		}

		$this->set(compact(
			'propertyActions', 'propertyTypes',
			'subareas', 'propertyConditions', 'propertyDirections',
			'certificates', 'currencies', 'facilities',
			'lotUnits', 'lotUnitName', 'session_id',
			'periods', 'viewSites'
		));
	}

	function dataRevision($id, $step, $data){
		$data_revision = $this->Property->PropertyRevision->getRevision($id, 'active', $step);
		$data_revision = $this->RmProperty->shapingArrayRevision($data_revision);

		if(isset($data_revision['PropertyPointPlus']['format_arr'])){
			$data_revision['PropertyPointPlus'] = unserialize($data_revision['PropertyPointPlus']['format_arr']);

			unset($data_revision['PropertyPointPlus']['format_arr']);
		}

		if(isset($data_revision['PropertyFacility']['format_arr'])){
			$data_revision['PropertyFacility'] = unserialize($data_revision['PropertyFacility']['format_arr']);

			unset($data_revision['PropertyFacility']['format_arr']);
		}

		if(isset($data_revision['PropertyPrice']['format_arr'])){
			$data_revision['PropertyPrice'] = unserialize($data_revision['PropertyPrice']['format_arr']);

			unset($data_revision['PropertyPrice']['format_arr']);
		}

		if(empty($data_revision['Property']['agent_email']) && isset($data['Property']['agent_email'])){
			$data_revision['Property']['agent_email'] = $data['Property']['agent_email'];
		}
		if(empty($data_revision['Property']['client_email']) && isset($data['Property']['client_email'])){
			$data_revision['Property']['client_email'] = trim($data['Property']['client_email']);
		}
		/*end get data revision*/

		return $data_revision;
	}

	function _callDataProperty ( $id, $step = false, $with_revision = true, $set_render = true ) {
		$dataBasic = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
			),
		), array(
			'status' => 'all',
			'admin_mine' => true,
		));

		if( !empty($dataBasic) ) {
			$dataAsset = $this->Property->PropertyAsset->getData('first', array(
				'conditions' => array(
					'PropertyAsset.property_id' => $id,
				),
			));

			$dataAddress = $this->Property->PropertyAddress->getMerge(array(), $id, false);

			$value = $dataBasic;
			$value = array_merge($value, $dataAsset);
			$value = array_merge($value, $dataAddress);

			$session_id = $this->RmCommon->filterEmptyField($value, 'Property', 'session_id', String::uuid());

			/*get data revision*/
			if( !empty($with_revision) ) {
				$data_revision = $this->dataRevision($id, $step, $this->request->data);
			} else {
				$data_revision = array();
			}

			$value = $this->RmProperty->_callBeforeView($value, $data_revision);
			$value = $this->RmProperty->mergeArrayRecursive($value, $data_revision);

			$property_type_id = $this->RmCommon->filterEmptyField($value, 'Property', 'property_type_id');
			$property_direction_id = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'property_direction_id');
			$property_condition_id = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'property_condition_id');
			$view_site_id = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'view_site_id');
			$lot_unit_id = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'lot_unit_id');

			$value = $this->Property->PropertyAddress->getMerge($value, $id);
			$value = $this->Property->PropertyAsset->LotUnit->getMerge($value, $lot_unit_id, 'LotUnit', false, array(
                'cache' => array(
                    'name' => __('LotUnit.%s', $lot_unit_id),
                ),
            ));
			$value = $this->Property->PropertyAsset->PropertyDirection->getMerge($value, $property_direction_id, 'PropertyAsset');
			$value = $this->Property->PropertyAsset->PropertyCondition->getMerge($value, $property_condition_id, 'PropertyAsset');
			$value = $this->Property->PropertyAsset->ViewSite->getMerge($value, $view_site_id, $property_type_id, 'PropertyAsset');

			$property['Property'] = $this->RmCommon->filterEmptyField($value, 'Property');
			$dataBasic = $this->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
				),
			));

			if( !empty($dataBasic['Property']) ) {
				$value = array_merge($value, $dataBasic);
			}
			if( !empty($value['UserClient']) ) {
				$disabledClient = true;
			}

			$value = $this->Property->_callBeforeViewEdit($value, $data_revision);
			$value = $this->RmCommon->dataConverter($value, array(
				'date' => array(
					'Property' => array(
						'contract_date',
					),
				)
			), true);

			if( !empty($set_render) ) {
				$this->set(compact(
					'session_id', 'dataBasic',
					'dataAsset', 'id', 'dataAddress',
					'disabledClient'
				));
			}

			return $value;
		} else {
			if( !empty($returnBoolean) ) {
				return false;
			} else {
				$redirect	= array();
				$prefix		= $this->params->prefix;

				if($prefix == 'admin'){
					$redirect = array('action' => 'index');
				}
				else if(empty($prefix)){
					$redirect = array(
						'action'			=> 'find', 
						'property_action'	=> 'dijual', 
					);
				}

				$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error', $redirect);
			}
		}
	}

	function _callSessionProperty ( $step = false ) {
		$dataBasic = $this->RmProperty->_callDataSession( $this->basicLabel );
		$dataAddress = $this->RmProperty->_callDataSession( $this->addressLabel );
		$dataAsset = $this->RmProperty->_callDataSession( $this->assetLabel );
		$dataMedias = $this->RmProperty->_callDataSession( $this->mediaLabel );

		if( empty($dataBasic) && $step == $this->addressLabel ) {
			$this->RmCommon->redirectReferer(__('Mohon lengkapi info dasar properti Anda'), 'error', array(
				'controller' => 'properties',
				'action' => 'sell',
				'admin' => true,
			));
		} else if( empty($dataAddress) && $step == $this->assetLabel ) {
			$this->RmCommon->redirectReferer(__('Mohon lengkapi info alamat properti Anda'), 'error', array(
				'controller' => 'properties',
				'action' => 'address',
				'admin' => true,
			));
		} else if( empty($dataAsset) && $step == $this->mediaLabel ) {
			$this->RmCommon->redirectReferer(__('Mohon lengkapi info spesifikasi properti Anda'), 'error', array(
				'controller' => 'properties',
				'action' => 'specification',
				'admin' => true,
			));
		} else {
			$session_id = Common::hashEmptyField($dataBasic, 'Property.session_id', String::uuid());
			$property_type_id = Common::hashEmptyField($dataBasic, 'Property.property_type_id');
			$property_action_id = Common::hashEmptyField($dataBasic, 'Property.property_action_id');
			$client_id = Common::hashEmptyField($dataBasic, 'Property.client_id');

			$dataBasic = $this->User->Property->PropertyType->getMerge($dataBasic, $property_type_id, 'PropertyType.id', array(
                'cache' => array(
                    'name' => __('PropertyType.%s', $property_type_id),
                ),
            ));
			$dataBasic = $this->User->Property->PropertyAction->getMerge($dataBasic, $property_action_id, 'PropertyAction.id', array(
				'cache' => array(
					'name' => __('PropertyAction.%s', $property_action_id),
				),
			));

			if( !empty($client_id) ) {
				$dataBasic = $this->User->getClient($dataBasic, $client_id);
				$disabledClient = true;

				$client_email = $this->RmCommon->filterEmptyField($dataBasic, 'UserClient', 'client_email');
				$dataBasic['Property']['client_email'] = $client_email;
			}

			$data = $dataBasic;
			$data = $this->RmProperty->_callMergeSessionAddress($data, $dataAddress);

			if( !empty($dataAsset) ) {
				if( !empty($dataAsset['Property']) ) {
					$dataAssetProperty = $dataAsset['Property'];

					$data['Property'] = array_merge($data['Property'], $dataAssetProperty);

					unset($dataAsset['Property']);
				}

				if( !empty($dataAsset['PropertyType']) ) {
					unset($dataAsset['PropertyType']);
				}

				if( !empty($data['PageConfig']) ) {
					unset($dataAsset['PageConfig']);
				}


				$data = array_merge($data, $dataAsset);
			}

			if( !empty($dataMedias) ) {
				if( !empty($data) ) {
					$data = array_merge($data, $dataMedias);
				} else {
					$data = $dataMedias;
				}
			}

			$this->set(compact(
				'session_id', 'dataBasic',
				'dataAsset', 'dataAddress',
				'dataMedias', 'disabledClient'
			));

			return $data;
		}
	}

	function _callSessionMedias ( $session_id ) {
		$photoMedia = $this->Property->PropertyMedias->getData('first', array(
			'conditions' => array(
				'PropertyMedias.session_id' => $session_id,
			),
			'order' => array(
				'PropertyMedias.primary' => 'DESC'
			)
		), array(
			'status' => 'all',
		));
		$videoMedia = $this->Property->PropertyVideos->getData('first', array(
			'conditions' => array(
				'PropertyVideos.session_id' => $session_id,
			),
		), array(
			'status' => 'all',
		));
		return array_merge($photoMedia, $videoMedia);
	}

	// API
	public function admin_api_add() {
		$data = $this->request->data;

		if( !empty($data) ) {
			$session_id = $data['Property']['session_id'] = $this->RmCommon->filterEmptyField($data, 'Property', 'session_id', String::uuid());

			$code = $this->RmCommon->createRandomNumber( 3, 'bcdfghjklmnprstvwxyz0123456789', 30);
			$user_data = Configure::read('User.data');

			$user_code = $this->RmCommon->filterEmptyField($user_data, 'code');

			$data['Property']['mls_id'] = $this->Property->generateMLSID($code, $user_code);

			$data = $this->RmProperty->_callAssetUtility( $data );
			$data = $this->Property->PropertyAddress->_callBeforeSave($data);
			$data = $this->RmProperty->_callPriceRequestData( $data );

			$media = $this->_callSessionMedias($session_id);

			$data['Property']['photo'] = $this->RmCommon->filterEmptyField($media, 'PropertyMedias', 'name');
			$data['PropertyMedias']['id'] = $this->RmCommon->filterEmptyField($media, 'PropertyMedias', 'id');

			$property_type_id = $this->RmCommon->filterEmptyField($data, 'Property', 'property_type_id');
			$property_action_id = $this->RmCommon->filterEmptyField($data, 'Property', 'property_action_id');

			$data = $this->Property->PropertyType->getMerge($data, $property_type_id, 'PropertyType.id', array(
                'cache' => array(
                    'name' => __('PropertyType.%s', $property_type_id),
                ),
            ));
			$data = $this->Property->PropertyAction->getMerge($data, $property_action_id, 'PropertyAction.id', array(
				'cache' => array(
					'name' => __('PropertyAction.%s', $property_action_id),
				),
			));

			$data = $this->RmProperty->_callBeforeSave($data, false, false);

			$result = $this->Property->doSaveAll( $data );

			$property_id = $this->RmCommon->filterEmptyField($result, 'id');

			$this->create_ebrosur($property_id);

			$this->RmCoBroke = $this->Components->load('RmCoBroke');
			$this->RmCoBroke->create_cobroke($property_id);

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'properties',
				'action' => 'address',
				'draft' => $this->draft_id,
				'admin' => true,
			));
		}
	}

	// API
	public function admin_api_edit( $id = false ) {
		$data = $this->request->data;
		$data = $this->_callDataProperty($id);
		$data = $this->Property->PageConfig->getMerge($data, $id, 'property');

		if( !empty($data) ) {
			$dataRequest = $this->request->data;
			$validate = $this->RmProperty->checkRevision($data);

			if( !empty($dataRequest) ) {
				$dataRequest['Property']['id'] = $this->RmCommon->filterEmptyField($data, 'Property', 'id');
				$dataRequest['PropertyAddress']['id'] = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'id');
				$dataRequest['PropertyAsset']['id'] = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'id');

				$dataRequest = $this->Property->PropertyAddress->_callBeforeSave($dataRequest);
				$dataRequest = $this->RmProperty->_callBeforeSave($dataRequest, false, false);

				$dataToRevision = $dataRequest;
				$dataRequest = $this->RmProperty->_callAssetUtility( $dataRequest );

				$result = $this->Property->doSaveAll( $dataRequest, $validate );

				if( !empty($validate) ) {
					$this->saveRevision($result, $id, $dataToRevision);
				}

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'properties',
					'action' => 'address',
					'draft' => $this->draft_id,
					'admin' => true,
				));
			} else {
				$this->set(compact(
					'data'
				));
			}
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_easy_add(){
		$isAjax	= $this->RequestHandler->isAjax();
		$record = array();
		$result	= array('data' => $record);

		$redirectURL = array(
			'admin'			=> true,
			'controller'	=> 'properties',
			'action'		=> 'index',
		);

		$this->_callDataSupport('Asset');

		if($this->request->data){
			if($isAjax){
				$data	= $this->RmProperty->callBeforeViewEasyMode($this->request->data, false);
			}
			else{
				$data = $this->RmProperty->callBeforeSaveEasyMode($this->request->data, $record);
				$data = Hash::insert($data, 'is_easy_mode', true);

				$sessionID = Common::hashEmptyField($data, 'Property.session_id');

			//	get primary media
				$sessionName	= Configure::read('__Site.Property.SessionName');
				$propertyMedias	= CakeSession::read(sprintf($sessionName, $this->mediaLabel));
				$propertyMedias	= Common::hashEmptyField($propertyMedias, 'PropertyMedias', array());

				$updateMedias	= array();
				$primaryID		= false;
				$primaryName	= false;

				if($propertyMedias){
				//	cuma 1 image, pas edit baru multiple. apus sisanya
					foreach($propertyMedias as $key => $propertyMedia){
						$mediaID	= Common::hashEmptyField($propertyMedia, 'id');
						$mediaName	= Common::hashEmptyField($propertyMedia, 'name');
						$primary	= Common::hashEmptyField($propertyMedia, 'primary', 0);

						if(empty($primaryName) && $primary){
							$primaryID		= $mediaID;
							$primaryName	= $mediaName;
						}

						$updateMedias[] = array('PropertyMedias' => array(
							'id'		=> $mediaID, 
							'primary'	=> $primary, 
							'status'	=> 0, 
						));
					}

					if(empty($primaryName)){
						$primaryID		= Common::hashEmptyField($propertyMedias, '0.id');
						$primaryName	= Common::hashEmptyField($propertyMedias, '0.name');
						$updateMedias	= Hash::insert($updateMedias, '0.PropertyMedias.primary', 1);
						$updateMedias	= Hash::insert($updateMedias, '0.PropertyMedias.status', 1);
					}

				//	set primary image
				//	$data = Hash::insert($data, 'Property.photo', $primaryName);
				}

			//	set primary image (sekarang jadi wajib)
				$data = Hash::insert($data, 'Property.photo', $primaryName);

			//	SAVE PROCESS ======================================================================================================

				$actionID = Common::hashEmptyField($data, 'Property.property_action_id', 1);

				if($actionID == 1){
					$data = Hash::remove($data, 'PropertyPrice');
				}

				$data = $this->Property->PropertyAddress->_callBeforeSave($data);
				$data = $this->RmProperty->_callBeforeSave($data);
				$data = $this->RmProperty->_callAssetUtility($data);

			//	setting show map / address disini karena pas add ga ada toggle kaya add versi biasa

				$isHideAddress	= $this->RmCommon->_callAllowAccess('is_hidden_address_property');
				$isHideMap		= $this->RmCommon->_callAllowAccess('is_hidden_map');

				$data = Hash::insert($data, 'PropertyAddress.hide_address', $isHideAddress);
				$data = Hash::insert($data, 'PropertyAddress.hide_map', $isHideMap);

			//	REMOVE SOME VALIDATION
				$this->Property->validator()->remove('title')->remove('description');

				$validData = $this->Property->saveAll($data, array('validate' => 'only'));

				if($validData){
				//	SAVE
					$validate	= false;
					$result		= $this->Property->doSaveAll($data, $validate, array(
						'is_easy_mode' => true, 
					));

				//	update media
					$recordID	= Common::hashEmptyField($result, 'id');
					$status		= Common::hashEmptyField($result, 'status', 'error');

					if($status == 'success' && $recordID){
						$isAdmin		= Configure::read('User.admin');
						$companyData	= Common::config('Config.Company.data', array());
						$isNeedApproval	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_approval_property');
						$isAutoApprove	= ($isAdmin && $isNeedApproval) || empty($isNeedApproval);

					//	flow si iksan, au ah ikutin aja
						if(Hash::check($data, 'Property.is_cobroke')){
							$isCoBroke = Common::hashEmptyField($data, 'Property.is_cobroke');

							if($isCoBroke){
								$this->Property->CoBrokeProperty->doCoBroke($recordID, 'active');
							}
							else{
								$this->Property->CoBrokeProperty->CoBrokeChangeStatus($recordID, $data);
							}
						}

					//	kata iksan langsung ga usah di cek asal ada id
					//	if($isAutoApprove){
						//	cobroke 
							$this->RmCoBroke = $this->Components->load('RmCoBroke');
							$this->RmCoBroke->create_cobroke($recordID, true);
					//	}

					//	update property media
						$isAutoGenerate = false;

						if($updateMedias){
						//	flush media (cuma 1 yang aktif)
							if($primaryID && $sessionID){
								$disableMedias = $this->Property->PropertyMedias->updateAll(array(
									'PropertyMedias.status' => 0, 
								), array(
									'PropertyMedias.id <>'		=> $primaryID, 
									'PropertyMedias.session_id'	=> $sessionID, 
								));
							}

							$updateMedias = Hash::insert($updateMedias, '{n}.PropertyMedias.property_id', $recordID);
							$updateMedias = $this->Property->PropertyMedias->saveAll($updateMedias, array(
							//	'validate' => 'only', 
							));

							if(empty($updateMedias)){
								$status	= 'error';
								$result = array_replace($result, array(
									'status'	=> $status, 
									'msg'		=> 'Berhasil menyimpan properti, namun terjadi kegagalan saat menyimpan media properti.', 
								));
							}
							else{
								$isAutoGenerate = $this->RmEbroschure->isAllowGenerateEbrochure();

								$companyData	= Common::config('Config.Company.data', array());
								$isBuilder		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_ebrochure_builder');

								if($isAutoGenerate && $isBuilder){
								//	ebrochure versi baru
									$isAutoGenerate = true;
								}
								else if($isAutoApprove){
								//	ebrochure versi lama
									$this->create_ebrosur($recordID);
									
									$isAutoGenerate = false;
								}
							}
						}

						if($isAutoGenerate){
							$redirectURL = array(
								'admin'			=> true, 
								'controller'	=> 'ebrosurs', 
								'action'		=> 'regenerate', 
								'property_id'	=> $recordID, 
							);
						}
						else{
							$allowEdit		= $this->RmCommon->_callAllowAccess('is_edit_property');
							$redirectURL	= array('admin' => true, 'controller' => 'properties');

							if($allowEdit && $recordID){
								$redirectURL= array_merge($redirectURL, array('action' => 'easy_preview', $recordID));
							}
							else{
								$redirectURL= array_merge($redirectURL, array('action' => 'index'));
							}
						}
					}

					$this->set(array('recordID' => $recordID));
				}
				else{
				//	invalid data
				//	debug($this->Property->validationErrors);exit;
				//	post data price kalo gagal balikin formatnya kaya semula, karena pas callBeforeSaveEasyMode ganti format array-nya
					$prices	= Common::hashEmptyField($this->data, 'PropertyPrice', array());
					$data	= Hash::insert($data, 'PropertyPrice', $prices);

					$status	= 'error';
					$result	= Hash::insert($result, 'status', $status);
					$result	= Hash::insert($result, 'validationErrors', $this->Property->validationErrors);
					$result = Hash::insert($result, 'Log', array(
						'activity'	=> __('Gagal menambah properti, mohon lengkapi semua data yang diperlukan'),
						'data'		=> $data,
						'error'		=> 1,
					));
				}

			//	===================================================================================================================

			//	$result	= Hash::insert($result, 'data', $data);

				if($status == 'success'){
					$this->RmProperty->_callDeleteSession();
				}
			}
		}
		else{
		//	master data
			$companyData	= Configure::read('Config.Company.data');
			$isCobroke		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_co_broke');
			$isOpenCoBroke	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_open_cobroke');

			$data = array(
				'Property' => array(
					'property_action_id'	=> 1, 
					'property_type_id'		=> 1, 
					'is_cobroke'			=> $isCobroke && $isOpenCoBroke, 
				), 
			);

		//	remove session (on first load)
			$this->RmProperty->_callDeleteSession();
		}

		$data	= $this->RmProperty->callBeforeViewEasyMode($data, false);
		$result	= Hash::insert($result, 'data', $data);

		$this->RmCommon->setProcessParams($result, $redirectURL);
		
		$this->RmCommon->_layout_file(array('map', 'fileupload'));

		$moduleTitle		= 'Tambah Properti';
		$render				= 'easy_add_form';
		$commissionTypes	= $this->RmCommon->getGlobalVariable('type_commision_cobroke');

		$this->set(array(
			'module_title'		=> $moduleTitle,
			'active_menu'		=> 'property_list',
			'commissionTypes'	=> $commissionTypes, 
		));

		$sessionID = Common::hashEmptyField($this->request->data, 'Property.session_id', String::uuid());
		$this->request->data = Hash::insert($this->request->data, 'Property.session_id', $sessionID);

		$this->render($render);
	}

	public function admin_easy_preview($recordID = null){
	//	note :
	//	property udah ke save jadi urusan media langsung ke save lewat proses ajax (tidak di handle disini)

		$allowEdit		= $this->RmCommon->_callAllowAccess('is_edit_property');
		$redirectURL	= array(
			'admin'			=> true,
			'controller'	=> 'properties',
			'action'		=> 'index',
		);

		if($allowEdit){
			$record = $this->_callDataProperty($recordID);
			$record = $this->Property->PageConfig->getMerge($record, $recordID, 'property');
			$record = $this->Property->CoBrokeProperty->getMerge($record, $recordID);

		//	untuk primary photo
			$primaryMedia = $this->Property->PropertyMedias->getData('first', array(
				'conditions' => array(
					'PropertyMedias.property_id'	=> $recordID,
					'PropertyMedias.primary'		=> 1,
				),
			), array(
				'status' => 'all', 
			));

			if($primaryMedia){
				$mediaName	= Common::hashEmptyField($primaryMedia, 'PropertyMedias.name');
				$record		= Hash::insert($record, 'Property.photo', $mediaName);
			}

			if($record){
				$data			= $this->request->data;
				$hasRevision	= $this->RmProperty->checkRevision($record);
				$result			= array();

				if($data){
					$isAjax				= $this->RequestHandler->isAjax();
				//	$userID				= Common::hashEmptyField($record, 'Property.user_id');
					$sessionID			= Common::hashEmptyField($record, 'Property.session_id');
					$price				= Common::hashEmptyField($record, 'Property.price', 0);
					$photo				= Common::hashEmptyField($record, 'Property.photo');
					$propertyAddressID	= Common::hashEmptyField($record, 'PropertyAddress.id');
					$propertyAssetID	= Common::hashEmptyField($record, 'PropertyAsset.id');
					
					/* BEGIN - edit by iksan untuk persiapan toggling co broke */
					$is_cobroke			= Common::hashEmptyField($record, 'Property.is_cobroke');
					/* END - edit by iksan untuk persiapan toggling co broke */

					$actionID = Common::hashEmptyField($data, 'Property.property_action_id', 1);

					if($actionID == 1){
						$data = Hash::remove($data, 'PropertyPrice');
					}
					else{
						$priceCurrencies	= Common::hashEmptyField($data, 'PropertyPrice.currency_id', array());
						$tempPropertyPrice	= array();

						if($priceCurrencies){
							foreach($priceCurrencies as $key => $priceCurrencyID){
								$pricePeriodID	= Common::hashEmptyField($data, sprintf('PropertyPrice.period_id.%s', $key));
								$priceValue		= Common::hashEmptyField($data, sprintf('PropertyPrice.price.%s', $key));

								if($priceValue){
									$tempPropertyPrice['currency_id'][]	= $priceCurrencyID;
									$tempPropertyPrice['price'][]		= $priceValue;
									$tempPropertyPrice['period_id'][]	= $pricePeriodID;
								}
							}
						}

						$data = Hash::insert($data, 'PropertyPrice', $tempPropertyPrice);
					}

					$groupID = Configure::read('User.group_id');

					if(in_array($groupID, Configure::read('__Site.Admin.Company.id')) || in_array($groupID, Configure::read('__Site.Admin.List.id')) || $groupID > 20){
						$agentEmail = Common::hashEmptyField($data, 'Property.agent_email');

						if($agentEmail){
							$agentData = $this->User->getData('first', array(
								'conditions' => array(
									'User.email' => $agentEmail, 
								)
							), array(
								'company'	=> true,
								'admin'		=> true,
								'role'		=> 'agent',
							));

							$agentID	= Common::hashEmptyField($agentData, 'User.id');
							$data		= Hash::insert($data, 'Property.user_id', $agentID);
						}
					}

					$data = Hash::insert($data, 'Property.id', $recordID);
					$data = Hash::insert($data, 'Property.session_id', $sessionID);
					$data = Hash::insert($data, 'PropertyAddress.id', $propertyAddressID);
					$data = Hash::insert($data, 'PropertyAsset.id', $propertyAssetID);

					$data = $this->Property->PropertyAddress->_callBeforeSave($data);
					$data = $this->RmProperty->_callBeforeSave($data);

					$revisionData = $data;

					$data = $this->RmProperty->_callAssetUtility($data);

					//	save
					$result	= $this->Property->doSaveAll($data, $hasRevision, array('is_easy_mode' => true));
					$status = Common::hashEmptyField($result, 'status', 'error');

					if($hasRevision){
						$this->saveRevision($result, $recordID, $revisionData);
					}

					if($isAjax){
						if($status == 'success'){
							/* 
								BEGIN - toggle hanya berlaku untuk properti yang sudah aktif dan toggle is_cobroke juga aktif 
							*/
							$this->RmCoBroke = $this->Components->load('RmCoBroke');

							$this->RmCoBroke->togglingByProperty($recordID, $data);
							/* 
								END - toggle hanya berlaku untuk properti yang sudah aktif dan toggle is_cobroke juga aktif 
							*/
						}
						else{
							$validationErrors	= Common::hashEmptyField($result, 'validationErrors', array());
							$inputErrors		= Common::parseValidationError('Property', $validationErrors);
							$result				= Hash::insert($result, 'inputErrors', $inputErrors);
						}

						$this->autoLayout = false;
						$this->autoRender = false;

						$this->RmCommon->setProcessParams($result, false, array(
							'noRedirect' => true,
							'flash' => false,
						));

						if($status == 'success'){
							$result = Hash::insert($result, 'redirect', Router::url(array(
								'admin'			=> true, 
								'controller'	=> 'properties', 
								'action'		=> 'index', 
							), true));
						}

						echo(json_encode($result));
						exit;
					}
				}
				else{
					$data = $record;
				}

				$data	= $this->RmProperty->callBeforeViewEasyMode($data);
				$result	= Hash::insert($result, 'data', $data);

				$commissionTypes = $this->RmCommon->getGlobalVariable('type_commision_cobroke');
				$this->RmCommon->setProcessParams($result, $redirectURL);

				$this->set(array(
					'active_menu'		=> 'property_list',
					'module_title'		=> __('Edit Properti'),
					'commissionTypes'	=> $commissionTypes, 
				));

				$this->RmCommon->_layout_file(array('map', 'fileupload'));
				$this->render('easy_add_preview');
			}
			else{
				$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error', $redirectURL);
			}
		}
		else{
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman tersebut.'), 'error', $redirectURL);
		}
	}

	public function admin_easy_media($recordID = null, $activeTab = 'photo'){
		$allowEdit = $this->RmCommon->_callAllowAccess('is_edit_property');

		if($allowEdit){
			$record		= $this->_callDataProperty($recordID);
			$isAjax		= $this->RequestHandler->isAjax();
			$isAdmin	= Configure::read('User.admin');

			$propertyMedias		= array();
			$propertyVideos		= array();
			$propertyDocuments	= array();
			$layoutFile			= array();
			$activeTab			= in_array($activeTab, array('photo', 'video', 'document')) ? $activeTab : 'photo';

			if($record){
				$record = $this->Property->PageConfig->getMerge($record, $recordID, 'property');

				$sessionID		= Common::hashEmptyField($record, 'Property.session_id');
				$active			= Common::hashEmptyField($record, 'Property.active');
				$propertySlug	= $this->RmProperty->getNameCustom($record);
				$propertySlug	= $this->RmCommon->toSlug($propertySlug);

				$record = Hash::insert($record, 'Property.slug', $propertySlug);

				$elementOptions	= array('status' => 'all');
				if($activeTab == 'photo'){
				//	PROPERTY IMAGE

				//	udah di include di easy preview
				//	$layoutFile		= array_merge($layoutFile, array('fileupload'));
					$propertyMedias	= $this->Property->PropertyMedias->getData('all', array(
						'conditions' => array(
							'PropertyMedias.property_id' => $recordID,
						),
					), $elementOptions);
				}
				else if($activeTab == 'video'){
				//	PROPERTY VIDEO

					$layoutFile		= array_merge($layoutFile, array('google_api'));
					$propertyVideos	= $this->Property->PropertyVideos->getData('all', array(
						'conditions' => array(
							'PropertyVideos.property_id' => $recordID,
						),
					), $elementOptions);

					$data = $this->request->data;

					if($data){
						$data	= $this->RmProperty->_callValidateVideo($this->request->data, $recordID, $sessionID);
						$result = $this->Property->PropertyVideos->doSaveVideo($data, $recordID);
						$status = Common::hashEmptyField($result, 'status', 'error');

						if($status == 'success'){
							if(empty($isAdmin) && $active){
								$this->Property->inUpdateChange($recordID);
							}

						//	reload list
							$propertyVideos	= $this->Property->PropertyVideos->getData('all', array(
								'conditions' => array(
									'PropertyVideos.property_id' => $recordID,
								),
							), $elementOptions);
						}

						$this->RmCommon->setProcessParams($result, false, array(
							'noRedirect'	=> true, 
							'ajaxFlash'		=> true, 
						));
					}
				}
				else if($activeTab == 'document'){
				//	PROPERTY DOCUMENT

					$this->paginate = $this->User->CrmProject->CrmProjectDocument->getData('paginate', array(
						'limit'			=> 10,
						'conditions'	=> array(
							'CrmProjectDocument.document_type'	=> 'property',
							'CrmProjectDocument.owner_id'		=> $recordID,
						),
					), array(
						'company' => true,
					));

					$this->loadModel('CrmProjectDocument');
					$propertyDocuments = $this->paginate('CrmProjectDocument');
					$propertyDocuments = $this->User->CrmProject->CrmProjectDocument->getDataList($propertyDocuments);
				}
			}

			$categoryMedias = $this->Property->PropertyMedias->CategoryMedias->getData('list', array(
	            'cache' => __('Property.CategoryMedias.List'),
	        ));
			Configure::write('__Site.CategoryMedias.Data', $categoryMedias);

		//	debug($layoutFile);exit;

			$this->RmCommon->_layout_file($layoutFile);
			$this->set(array(
				'isAjax'			=> $isAjax, 
				'record'			=> $record, 
				'propertyMedias'	=> $propertyMedias, 
				'propertyVideos'	=> $propertyVideos, 
				'propertyDocuments'	=> $propertyDocuments, 
				'activeTab'			=> $activeTab, 
				'_wrapper_ajax'		=> 'property_media_wrapper', 
			));
		}
		else{
			$result = array(
				'status'	=> 'error', 
				'msg'		=> __('Anda tidak memiliki hak untuk mengakses fasilitas tersebut.')
			);

			$this->RmCommon->setProcessParams($result, false, array(
				'noRedirect'	=> true, 
				'ajaxFlash'		=> true, 
			));
		}

		$this->set('allowEdit', $allowEdit);

		$this->layout = 'ajax';
		$this->render('easy_add_media');
	}

	public function backprocess_toggle_specification($recordID = null){
	//	cuma buat toggle doang
		$record = $this->Property->getData('first', array(
			'conditions' => array('Property.id' => $recordID),
		), array(
			'status'		=> 'all',
			'admin_mine'	=> true,
		));

		$record = array_merge($record, $this->Property->PropertyAsset->getData('first', array(
			'conditions' => array('PropertyAsset.property_id' => $recordID),
		)));

	//	replace post data
		$record	= array_replace_recursive($record, $this->request->data);

		$typeID			= Common::hashEmptyField($record, 'Property.property_type_id');
		$actionID		= Common::hashEmptyField($record, 'Property.property_action_id');
		$lotUnitID		= Common::hashEmptyField($record, 'PropertyAsset.lot_unit_id');
		$directionID	= Common::hashEmptyField($record, 'PropertyAsset.property_direction_id');
		$conditionID	= Common::hashEmptyField($record, 'PropertyAsset.property_condition_id');
		$viewSiteID		= Common::hashEmptyField($record, 'PropertyAsset.view_site_id');

		$record = $this->Property->PropertyAsset->PropertyDirection->getMerge($record, $directionID, 'PropertyAsset');
		$record = $this->Property->PropertyAsset->PropertyCondition->getMerge($record, $conditionID, 'PropertyAsset');
		$record = $this->Property->PropertyAsset->ViewSite->getMerge($record, $viewSiteID, $typeID, 'PropertyAsset');
		$record	= $this->Property->getDataList($record, array(
			'contain' => array(
				'MergeDefault', 
			//	'PropertyAsset',
			),
		));

		$isLot		= Common::hashEmptyField($record, 'PropertyType.is_lot');
		$isSpace	= Common::hashEmptyField($record, 'PropertyType.is_space');

		$record = $this->RmProperty->callBeforeViewEasyMode($record, false);

		if($isSpace){
			$lotUnits = $this->Property->PropertyAsset->LotUnit->getData('all', false, array(
				'property_action_id'	=> $actionID,
				'is_space'				=> $isSpace,
			));
		}
		else{
			$lotUnits = $this->Property->PropertyAsset->LotUnit->getData('all', array(
				'fields'	=> array('LotUnit.id', 'LotUnit.slug'),
				'group'		=> array('LotUnit.slug'),
			), array(
				'is_lot' => $isLot,
			));
		}

		$this->set(array(
			'record'	=> $record, 
			'lotUnits'	=> $lotUnits, 
		));

		$this->render('/Elements/blocks/properties/forms/easy_mode_specification');
	}

	public function admin_sell() {
		$step = $this->basicLabel;
		$dataBasic = $this->_callSessionProperty($step);

		$data = $this->request->data;
		$data = $this->RmProperty->_callBeforeSave($data, $dataBasic, false);
		$result = $this->Property->doBasic( $data, $dataBasic, true );

		if( !empty($result) ) {
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'properties',
				'action' => 'address',
				'draft' => $this->draft_id,
				'admin' => true,
			));
		}

		$this->_callDataSupport($step);

		$this->set('module_title', __('Tambah Properti'));
		$this->set(compact(
			'step'
		));
	}

	public function admin_address() {
		$step = $this->addressLabel;
		$dataAddress = $this->_callSessionProperty($step);
		$this->RmCommon->_layout_file('map');

		$data = $this->request->data;
		$data = $this->Property->PropertyAddress->_callBeforeSave($data);
		$data = $this->RmProperty->_callBeforeSave($data, $dataAddress, false);
		$result = $this->Property->PropertyAddress->doAddress( $data, $dataAddress, true );
		
		if( !empty($result) ) {
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'properties',
				'action' => 'specification',
				'draft' => $this->draft_id,
				'admin' => true,
			));
		}

		$urlBack = array(
			'controller' => 'properties',
			'action' => 'sell',
			'draft' => $this->draft_id,
			'admin' => true,
		);

		$this->_callDataSupport($step);

		$this->set(compact(
			'step', 'urlBack'
		));
		$this->render('admin_sell');
	}

	public function admin_specification() {
		$step = $this->assetLabel;
		$dataAsset = $this->_callSessionProperty($step);
		$dataAsset = $this->RmProperty->_callChangeToRequestData( $dataAsset, 'PropertyFacility', 'facility_id', true );
		$dataAsset = $this->RmProperty->_callChangeToRequestData( $dataAsset, 'PropertyPointPlus', 'name' );
		$dataAsset = $this->RmProperty->_callPriceRequestData( $dataAsset );

		$data = $this->request->data;
		$data = $this->RmProperty->_callBeforeSave($data, $dataAsset, false);
		$result = $this->Property->PropertyAsset->doSave( $data, $dataAsset, true );

		if( !empty($result) ) {
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'properties',
				'action' => 'medias',
				'draft' => $this->draft_id,
				'admin' => true,
			));
		}

		$urlBack = array(
			'controller' => 'properties',
			'action' => 'address',
			'draft' => $this->draft_id,
			'admin' => true,
		);

		$this->_callDataSupport($step, $dataAsset);

		$this->set(compact(
			'step', 'urlBack'
		));
		$this->render('sell');
	}

	public function processProperty( $validate = true, $dataMedias = false ) {
		$dataBasic = $this->RmProperty->_callDataSession( $this->basicLabel );
		$dataAddress = $this->RmProperty->_callDataSession( $this->addressLabel );
		$dataAsset = $this->RmProperty->_callDataSession( $this->assetLabel );

		$mls_id = Common::hashEmptyField($dataBasic, 'Property.mls_id');
		$session_id = Common::hashEmptyField($dataBasic, 'Property.session_id');

		if( empty($mls_id) ) {
			$code = $this->RmCommon->createRandomNumber( 3, 'bcdfghjklmnprstvwxyz0123456789', 30);
			// Set Generate MLS ID
			$dataBasic['Property']['mls_id'] = $this->Property->generateMLSID($code);
		}

		$dataBasic['Property']['refresh_date'] = date('Y-m-d H:i:s');
		$validateBasic = $this->Property->doBasic( $dataBasic, false, $validate );
		$property_id = !empty($validateBasic['id'])?$validateBasic['id']:false;

		if( !empty($property_id) ) {
			$dataAddress['PropertyAddress']['property_id'] = $property_id;
			$dataAsset['PropertyAsset']['property_id'] = $property_id;
		}

		$validateAddress = $this->Property->PropertyAddress->doAddress( $dataAddress, false, $validate, $property_id );

		// Just Taken Data for Asset
		$dataAsset = $this->RmProperty->_callChangeToRequestData( $dataAsset, 'PropertyFacility', 'facility_id' );
		$dataAsset = $this->RmProperty->_callChangeToRequestData( $dataAsset, 'PropertyPointPlus', 'name' );
		$validateAsset = $this->Property->PropertyAsset->doSave( $dataAsset, false, $validate, $property_id );

		$statusBasic =!empty($validateBasic['status'])?$validateBasic['status']:'error';
		$statusAddress =!empty($validateAddress['status'])?$validateAddress['status']:'error';
		$statusAsset =!empty($validateAsset['status'])?$validateAsset['status']:'error';

		if( empty($session_id) || $statusBasic == 'error' ) {
			$this->RmCommon->redirectReferer(__('Mohon lengkapi info dasar properti Anda'), 'error', array(
				'controller' => 'properties',
				'action' => 'sell',
				'admin' => true,
			));
		} else if ( $statusAddress == 'error' ) {
			$this->RmCommon->redirectReferer(__('Mohon lengkapi info alamat properti Anda'), 'error', array(
				'controller' => 'properties',
				'action' => 'address',
				'admin' => true,
			));
		} else if ( $statusAsset == 'error' ) {
			$this->RmCommon->redirectReferer(__('Mohon lengkapi info spesifikasi properti Anda'), 'error', array(
				'controller' => 'properties',
				'action' => 'specification',
				'admin' => true,
			));
		} else if( !empty($property_id) ) {

			$this->RmProperty->_callDeleteSession();
			$photo_id = $this->RmCommon->filterEmptyField($dataMedias, 'PropertyMedias', 'id');
			$photo_name = $this->RmCommon->filterEmptyField($dataMedias, 'PropertyMedias', 'name');
			$is_approval_property = $this->RmCommon->filterEmptyField($this->data_company, 'is_approval_property');

			$this->Property->PropertyVideos->doChange( $property_id, $session_id );
			$this->User->CrmProject->CrmProjectDocument->doSaveDocumentProperty($session_id, $property_id);

			if( !empty($this->draft_id) ) {
				$this->Property->PropertyDraft->doCompleted($this->draft_id, $property_id);
			}

			if( !empty($photo_name) ) {
				$this->Property->PropertyMedias->doSavePhoto( $property_id, $session_id, $photo_name, $photo_id );
			}

			/* masukin ke queue sync */
			$approval_set = ((Configure::read('User.admin') && !empty($is_approval_property)) || empty($is_approval_property)) ? true : false;

			if( $approval_set ){
				$property = $this->RmProperty->mergeArrayRecursive($dataBasic, $dataAddress);
				$property = $this->RmProperty->mergeArrayRecursive($property, $dataAsset);

				$all_medias = $this->getAllMedias($property_id);
				$property 	= $this->RmProperty->mergeArrayRecursive($property, $all_medias);

			//	ebrochure lama masuk "create_ebrosur" yang baru redirect ke "regenerate"
				$companyData	= Common::config('Config.Company.data', array());
				$isBuilder		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_ebrochure_builder');

				if(empty($isBuilder)){
					$this->create_ebrosur($property_id);
				}

				$this->RmCoBroke = $this->Components->load('RmCoBroke');
				$this->RmCoBroke->create_cobroke($property_id);
			}

			/* END masukin ke queue sync */

			return $property_id;
		} else {
			return true;
		}
	}

	public function admin_medias () {
		if( $this->processProperty() ) {
			$step = $this->mediaLabel;
			$dataProperty = $this->_callSessionProperty($step);
			$session_id = Common::hashEmptyField($dataProperty, 'Property.session_id');
			
			$categoryMedias = $this->Property->PropertyMedias->CategoryMedias->getData('list', array(
	            'cache' => __('Property.CategoryMedias.List'),
	        ));
			Configure::write('__Site.CategoryMedias.Data', $categoryMedias);

			$this->RmCommon->_layout_file('fileupload');
			$this->autoRender = false;

			if( !empty($this->request->data) ) {
				$data = $this->request->data;
				$propertyMedia = $this->_callSessionMedias($session_id);

				if( !empty($propertyMedia) ) {
					$property_id = $this->processProperty( false, $propertyMedia );

	            	$this->RmCommon->_saveLog(__('Berhasil menambahkan properti'), $dataProperty, $property_id);

				//	untuk generate ebrochure versi lama udah di handle di dalam $this->processProperty()
				//	versi baru handle disini
					$isAutoGenerate	= $this->RmEbroschure->isAllowGenerateEbrochure();

					$companyData	= Common::config('Config.Company.data', array());
					$isBuilder		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_ebrochure_builder');

					if($isAutoGenerate && $isBuilder){
						$this->RmCommon->redirectReferer('', 'success', array(
							'admin'			=> true, 
							'controller'	=> 'ebrosurs', 
							'action'		=> 'regenerate', 
							'property_id'	=> $property_id, 
						));
					}
					else{
						$this->RmCommon->redirectReferer(__('Properti berhasil disimpan. Properti Anda akan segera muncul pada daftar properti apabila kami telah menyetujui properti Anda'), 'success', array(
							'controller' => 'properties',
							'action' => 'index',
							'admin' => true,
						));
					}

				} else {
	            	$this->RmCommon->_saveLog(__('Gagal menambahkan properti'), $dataProperty);
					$this->RmCommon->redirectReferer(__('Mohon mengunggah foto properti'), 'error', array(
						'controller' => 'properties',
						'action' => 'medias',
						'draft' => $this->draft_id,
						'admin' => true,
					));
				}
			} else {
				$dataMedias = $this->Property->PropertyMedias->getData('all', array(
					'conditions' => array(
						'PropertyMedias.session_id' => $session_id,
					),
				), array(
					'status' => 'all',
				));
			}

			$urlBack = array(
				'controller' => 'properties',
				'action' => 'specification',
				'draft' => $this->draft_id,
				'admin' => true,
			);

			$this->set(compact(
				'step', 'dataMedias', 'urlBack'
			));
			$this->render('sell');
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_videos () {
		$step = $this->mediaLabel;
		$sub_step = 'video';
		$dataProperty = $this->_callSessionProperty($step);
		$session_id = $this->RmCommon->filterEmptyField($dataProperty, 'Property', 'session_id');

		if( $this->processProperty() ) {
			if( !empty($this->request->data['PropertyVideos']) ) {
				$data = $this->RmProperty->_callValidateVideo( $this->request->data, false, $session_id );
				$result = $this->Property->PropertyVideos->doSaveVideo( $data );

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'properties',
					'action' => 'videos',
					'draft' => $this->draft_id,
					'admin' => true,
					'subtab' => 'url-content',
				));
			} else if( !empty($this->request->data) ) {
				$propertyMedia = $this->_callSessionMedias($session_id);

				if( !empty($propertyMedia['PropertyMedias']) ) {
					$property_id = $this->processProperty( false, $propertyMedia );

            		$this->RmCommon->_saveLog(__('Berhasil menambahkan properti'), $dataProperty, $property_id);
					$this->RmCommon->redirectReferer(__('Properti berhasil disimpan. Properti Anda akan segera muncul pada daftar properti apabila kami telah menyetujui properti Anda'), 'success', array(
						'controller' => 'properties',
						'action' => 'index',
						'admin' => true,
					));
				} else {
					$this->RmCommon->redirectReferer(__('Mohon mengunggah foto properti'), 'error', array(
						'controller' => 'properties',
						'action' => 'medias',
						'draft' => $this->draft_id,
						'admin' => true,
					));
				}
			}

			$dataVideos = $this->Property->PropertyVideos->getData('all', array(
				'conditions' => array(
					'PropertyVideos.session_id' => $session_id,
				),
			), array(
				'status' => 'all',
			));

			$urlBack = array(
				'controller' => 'properties',
				'action' => 'specification',
				'draft' => $this->draft_id,
				'admin' => true,
			);

		//	untuk backlink youtube
			$propertySlug = $this->RmProperty->getNameCustom($dataProperty);
			$propertySlug = $this->RmCommon->toSlug($propertySlug);
			$dataProperty['Property']['slug'] = $propertySlug;

			$this->set(array(
				'property' => $dataProperty,
			));

			$this->set(compact(
				'step', 'dataVideos', 'urlBack',
				'sub_step'
			));

			$this->RmCommon->_layout_file(array(
				'fileupload',
				'google_api'
			));

			$this->render('sell');
		}
	}

	public function admin_documents() {
		$this->loadModel('CrmProjectDocument');

		$step = $this->mediaLabel;
		$sub_step = 'documents';
		$dataProperty = $this->_callSessionProperty($step);
		$session_id = $this->RmCommon->filterEmptyField($dataProperty, 'Property', 'session_id');
		$mls_id = $this->RmCommon->filterEmptyField($dataProperty, 'Property', 'mls_id');
		$owner_name = sprintf(__('%s | Properti'), $mls_id);

		if( $this->processProperty() ) {
			if( !empty($this->request->data) ) {
				$propertyMedia = $this->_callSessionMedias($session_id);

				if( !empty($propertyMedia['PropertyMedias']) ) {
					$this->processProperty( false, $propertyMedia );
					$this->RmCommon->redirectReferer(__('Properti berhasil disimpan. Properti Anda akan segera muncul pada daftar properti apabila kami telah menyetujui properti Anda'), 'success', array(
						'controller' => 'properties',
						'action' => 'index',
						'admin' => true,
					));
				} else {
					$this->RmCommon->redirectReferer(__('Mohon mengunggah foto properti'), 'error', array(
						'controller' => 'properties',
						'action' => 'medias',
						'draft' => $this->draft_id,
						'admin' => true,
					));
				}
			}

			$this->paginate = $this->User->CrmProject->CrmProjectDocument->getData('paginate', array(
				'conditions' => array(
					'CrmProjectDocument.document_type' => 'property',
					'CrmProjectDocument.owner_id' => $session_id,
				),
				'limit' => 10,
			), array(
				'company' => true,
			));
			$documents = $this->paginate('CrmProjectDocument');

			$urlBack = array(
				'controller' => 'properties',
				'action' => 'specification',
				'draft' => $this->draft_id,
				'admin' => true,
			);

			$this->RmCommon->_layout_file('fileupload');
			$this->set(compact(
				'step', 'documents', 'urlBack',
				'sub_step', 'id', 'owner_name'
			));
			$this->render('sell');
		}
	}

	public function admin_index(){
		if( !$this->RmCommon->_callIsDirector() ) {
			$options = array(
				'order' => array(
                	// 'Property.change_date' => 'DESC',
	                // 'Property.featured' => 'DESC',
                	'Property.id' => 'DESC',
	            ),
	            'type_merge' => 'regular_merge',
			);

			$properties = $this->RmProperty->_callBeforeViewProperties($options, array(
				'other_contain' => true,
	            'contain_data' => array(
	                'MergeDefault',
	                'PropertyAddress',
	                'PropertyAsset',
	                'PropertySold',
	                'PropertyNotification',
	                'PropertyStatusListing',
	                'User',
	                'Approved',
	                'Client',
	                'CoBrokeProperty',
	                'UserActivedAgentDetail',
					), 
				));

			$refresh_all = $this->Property->isAllowRefresh();
			
			// Check OpenListing & Khusus sales manager hanya boleh edit properti yg dibawahnya aja
			$user_login_id = Configure::read('User.id');
			$isOpenListing	= Common::_callAllowAccess('is_open_listing');

			if( !empty($isOpenListing) ) {
				$childList = $this->User->getUserParent($user_login_id);
			} else {
				$childList = array();
			}
			// END

			$packages = array();

			$this->set(array(
				'module_title' => __('Daftar Properti'),
				'properties' => $properties,
				'refresh_all' => $refresh_all, 
				'packages' => $packages,
				'childList' => $childList,
			));

			$this->RmCommon->renderRest(array(
	            'is_paging' => true
	        ));
		} else {
			$this->RmCommon->redirectReferer(__('Halaman tidak ditemukan'), 'error');
		}
	}

	public function admin_edit( $id = false ) {
		if( $this->RmCommon->_callAllowAccess('is_edit_property') ) {
			$step = $this->basicLabel;
			$property = $this->_callDataProperty($id, $step);

			$data = $this->request->data;
			$data = $this->RmProperty->_callBeforeSave($data, $property, false);
			$validate = $this->RmProperty->checkRevision($property);

			$result = $this->Property->doBasic( $data, $property, $validate, $id, false );

			/*akan di masukkan ke queue sync*/
			if(!empty($result['status']) && $result['status'] == 'success' && !empty($result['id'])){
				$property = $this->User->Property->property_fix($id);
			}
			/*akan di masukkan ke queue sync*/

			$this->saveRevision($result, $id, $data, $step);
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'properties',
				'action' => 'edit_address',
				$id,
				'admin' => true,
			));

			$this->_callDataSupport($step, $property);

			if( $this->RmCommon->filterEmptyField($this->request->data, 'UserClient') ) {
				$disabledClient = true;
			}

			$this->set('module_title', __('Edit Info Dasar'));
			$this->set(compact(
				'step', 'id', 'disabledClient'
			));
			$this->render('admin_sell');
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_edit_address( $id = false ) {
		$step = $this->addressLabel;
		$dataAddress = $this->_callDataProperty($id);

		$address_id = $this->RmCommon->filterEmptyField($dataAddress, 'PropertyAddress', 'id');

		$data = $this->request->data;
		$data = $this->Property->PropertyAddress->_callBeforeSave($data);
		$data = $this->RmProperty->_callBeforeSave($data, $dataAddress, false);
		$validate = $this->RmProperty->checkRevision($dataAddress);

		$result = $this->Property->PropertyAddress->doAddress( $data, $dataAddress, $validate, $id, $address_id, false );
		$this->saveRevision($result, $id, $data, $step);

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'properties',
			'action' => 'edit_specification',
			$id,
			'admin' => true,
		));

		$urlBack = array(
			'controller' => 'properties',
			'action' => 'edit',
			$id,
			'admin' => true,
		);

		$this->RmCommon->_layout_file('map');
		$this->_callDataSupport($step);

		$this->set('module_title', __('Edit Alamat Properti'));
		$this->set(compact(
			'step', 'urlBack',
			'id'
		));
		$this->render('admin_sell');
	}

	public function admin_edit_specification( $id = false ) {
		$step = $this->assetLabel;
		$dataAsset = $this->_callDataProperty($id);

		$asset_id = $this->RmCommon->filterEmptyField($dataAsset, 'PropertyAsset', 'id');

		$data = $this->request->data;
		$data = $this->RmProperty->_callBeforeSave($data, $dataAsset, false);
		$validate = $this->RmProperty->checkRevision($dataAsset);

		$result = $this->Property->PropertyAsset->doSave( $data, $dataAsset, $validate, $id, $asset_id, false );
		$this->saveRevision($result, $id, $data, $step);
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'properties',
			'action' => 'edit_medias',
			$id,
			'admin' => true,
		));
		$urlBack = array(
			'controller' => 'properties',
			'action' => 'edit_address',
			$id,
			'admin' => true,
		);

		$this->_callDataSupport($step, $dataAsset);

		$this->set('module_title', __('Edit Spesifikasi'));
		$this->set(compact(
			'step', 'urlBack'
		));
		$this->render('sell');
	}

	public function admin_edit_medias ( $id = false ) {
		$step = $this->mediaLabel;
		$sub_step = 'photo';
		$dataMedias = $this->Property->PropertyMedias->getData('all', array(
			'conditions' => array(
				'PropertyMedias.property_id' => $id,
			),
		), array(
			'status' => 'all',
		));
		$property = $this->_callDataProperty($id);
		$session_id = $this->RmCommon->filterEmptyField($property, 'Property', 'session_id');

		$categoryMedias = $this->Property->PropertyMedias->CategoryMedias->getData('list', array(
            'cache' => __('Property.CategoryMedias.List'),
        ));

		Configure::write('__Site.CategoryMedias.Data', $categoryMedias);

		$loadasset = Common::hashEmptyField($this->params->named, 'loadasset', true, array('isset' => true));

		if($loadasset){
			$this->RmCommon->_layout_file('fileupload');
		}

		$urlBack = array(
			'controller' => 'properties',
			'action' => 'edit_specification',
			$id,
			'admin' => true,
		);

		$this->set('module_title', __('Edit Foto, Video, & Dokumen'));
		$this->set(compact(
			'step', 'urlBack', 'sub_step', 'dataMedias',
			'id', 'session_id'
		));
		$this->render('sell');

		$this->RmCommon->renderRest();
	}

	public function admin_edit_videos($id = false){
		$this->RmCommon->_layout_file(array(
			'fileupload',
			'google_api'
		));

		$step = $this->mediaLabel;
		$sub_step = 'video';
		$isAdmin = Configure::read('User.admin');

		$dataVideos = $this->Property->PropertyVideos->getData('all', array(
			'conditions' => array(
				'PropertyVideos.property_id' => $id,
			),
		), array(
			'status' => 'all',
		));
		$property 	= $this->_callDataProperty($id);

		$session_id = $this->RmCommon->filterEmptyField($property, 'Property', 'session_id');
		$active 	= $this->RmCommon->filterEmptyField($property, 'Property', 'active');

		$data = $this->RmProperty->_callValidateVideo( $this->request->data, $id, $session_id );
		$result = $this->Property->PropertyVideos->doSaveVideo( $data, $id );
		$status = $this->RmCommon->filterEmptyField($result, 'status');

		if( $status == 'success' ){
			if(empty($isAdmin) && !empty($active)){
				$r = $this->User->Property->inUpdateChange($id);
			}
		}

		$this->RmCommon->setProcessParams($result, array(
			'admin' => TRUE,
			'controller' => 'properties',
			'action' => 'admin_edit_videos',
			$id,
			'subtab' => 'url-content',
		));

		$urlBack = array(
			'controller' => 'properties',
			'action' => 'edit_specification',
			$id,
			'admin' => true,
		);

	//	untuk backlink youtube
		$propertySlug = $this->RmProperty->getNameCustom($property);
		$propertySlug = $this->RmCommon->toSlug($propertySlug);
		$property['Property']['slug'] = $propertySlug;

		$this->set('module_title', __('Edit Foto, Video, & Dokumen'));
		$this->set(compact(
			'property',
			'step', 'sub_step', 'urlBack',
			'dataVideos', 'id', 'session_id'
		));

		$this->render('sell');
	}

	public function admin_edit_documents( $id = false ) {
		$this->loadModel('CrmProjectDocument');

		$step = $this->mediaLabel;
		$sub_step = 'documents';
		$isAdmin = Configure::read('User.admin');

		$this->paginate = $this->User->CrmProject->CrmProjectDocument->getData('paginate', array(
			'conditions' => array(
				'CrmProjectDocument.document_type' => 'property',
				'CrmProjectDocument.owner_id' => $id,
			),
			'limit' => 10,
		), array(
			'company' => true,
		));

		$documents = $this->paginate('CrmProjectDocument');
		$documents = $this->User->CrmProject->CrmProjectDocument->getDataList($documents);

		$property = $this->_callDataProperty($id);
		$session_id = $this->RmCommon->filterEmptyField($property, 'Property', 'session_id');

		$urlBack = array(
			'controller' => 'properties',
			'action' => 'edit_specification',
			$id,
			'admin' => true,
		);

		$this->RmCommon->_layout_file('fileupload');
		$this->set('module_title', __('Edit Foto, Video, & Dokumen'));
		$this->set(compact(
			'step', 'sub_step', 'urlBack',
			'documents', 'id', 'session_id'
		));
		$this->render('sell');
	}

	public function admin_document_add( $id = false ) {
		if( is_numeric($id) ) {
			$property = $this->_callDataProperty($id);
		} else {
			$step = $this->mediaLabel;
			$property = $this->_callSessionProperty($step);
			$id = $this->RmCommon->filterEmptyField($property, 'Property', 'session_id');
		}

		if( !empty($property) ) {
		//	tambahan kalo request lewat easy mode ===================================================

			$isAjax			= $this->RequestHandler->isAjax();
			$postParams		= Common::hashEmptyField($this->request->data, 'params', array());
			$isEasyMode		= Common::hashEmptyField($this->request->data, 'is_easy_mode');
			$wrapperAjax	= Common::hashEmptyField($this->request->data, '_wrapper_ajax');

			$postParams		= Hash::combine($postParams, '{n}.name', '{n}.value');
			$isEasyMode		= Common::hashEmptyField($postParams, 'is_easy_mode', $isEasyMode);
			$wrapperAjax	= Common::hashEmptyField($postParams, '_wrapper_ajax', $wrapperAjax);
			$resultOptions	= array(
				'ajaxFlash'		=> true,
				'ajaxRedirect'	=> true,
			);

			if($isAjax && $isEasyMode && $wrapperAjax){
				$this->layout = 'ajax';
				$this->set(array(
					'_wrapper_ajax'	=> $wrapperAjax, 
					'_data_reload'	=> false, 
					'is_easy_mode'	=> true, 
				));
			}

			$this->request->data = Hash::remove($this->request->data, 'params');
			$this->request->data = Hash::remove($this->request->data, 'is_easy_mode');
			$this->request->data = Hash::remove($this->request->data, '_wrapper_ajax');
			$this->request->data = Hash::remove($this->request->data, 'CrmProjectDocument.colview_default');

		//	=========================================================================================

			$data = $this->request->data;

			if( !empty($data['CrmProjectDocument']) ) {
				$property['CrmProject']['property_id'] = $id;
				$session_id = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument', 'session_id');

				$data = $this->RmCrm->_callBeforeSaveDocument($data, $property);
				$dataSave = $this->RmCommon->filterEmptyField($data, 'SaveDocument');
				$result = $this->User->CrmProject->CrmProjectDocument->doSaveMany($dataSave, false, true);

				$propertyID		= $id;
				$urlRedirect	= array();

				if($isEasyMode && $propertyID){
					$urlRedirect = array(
						'admin'			=> true,
						'controller'	=> 'properties',
						'action'		=> 'easy_media',
						$propertyID,
						'document', 
					);
				}

				$this->RmCommon->setProcessParams($result, $urlRedirect, $resultOptions);
			//	$this->RmCommon->setProcessParams($result, false, array(
			//		'ajaxFlash' => true,
			//		'ajaxRedirect' => true,
			//	));
			} else {
				$session_id = String::uuid();
			}

			$documentCategories = $this->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('list', array(
				'conditions' => array(
					'DocumentCategory.type' => 'property',
				),
			));

			$this->set('_flash', false);
			$this->set(compact(
				'property', 'id', 'session_id',
				'documentCategories', 'result'
			));
			$this->render('/Crm/admin_project_document_add');
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_document_edit( $id = false ) {
	//	tambahan kalo request lewat easy mode ===================================================

		$isAjax			= $this->RequestHandler->isAjax();
		$postParams		= Common::hashEmptyField($this->request->data, 'params', array());
		$isEasyMode		= Common::hashEmptyField($this->request->data, 'is_easy_mode');
		$wrapperAjax	= Common::hashEmptyField($this->request->data, '_wrapper_ajax');

		$postParams		= Hash::combine($postParams, '{n}.name', '{n}.value');
		$isEasyMode		= Common::hashEmptyField($postParams, 'is_easy_mode', $isEasyMode);
		$wrapperAjax	= Common::hashEmptyField($postParams, '_wrapper_ajax', $wrapperAjax);
		$resultOptions	= array(
			'ajaxFlash'		=> true,
			'ajaxRedirect'	=> true,
		);

		if($isAjax && $isEasyMode && $wrapperAjax){
			$this->layout = 'ajax';
			$this->set(array(
				'_wrapper_ajax'	=> $wrapperAjax, 
				'_data_reload'	=> false, 
				'is_easy_mode'	=> true, 
			));
		}

		$this->request->data = Hash::remove($this->request->data, 'params');
		$this->request->data = Hash::remove($this->request->data, 'is_easy_mode');
		$this->request->data = Hash::remove($this->request->data, '_wrapper_ajax');
		$this->request->data = Hash::remove($this->request->data, 'CrmProjectDocument.colview_default');

	//	=========================================================================================

		$value = $this->User->CrmProject->CrmProjectDocument->getData('first', array(
			'conditions' => array(
				'CrmProjectDocument.id' => $id,
				'CrmProjectDocument.document_type' => 'property',
			),
		), array(
			'company' => true,
		));

		if( !empty($value) ) {
			$data = $this->request->data;
			$owner_id = $this->RmCommon->filterEmptyField($value, 'CrmProjectDocument', 'owner_id');

			$result = $this->User->CrmProject->CrmProjectDocument->doEdit($data, $value, $id);

			$propertyID		= $owner_id;
			$urlRedirect	= array();

		//	debug($this->request->data);

			if($isEasyMode && $propertyID){
				$urlRedirect = array(
					'admin'			=> true,
					'controller'	=> 'properties',
					'action'		=> 'easy_media',
					$propertyID,
					'document', 
				);
			}

			$this->RmCommon->setProcessParams($result, $urlRedirect, $resultOptions);

			$neighbors = $this->User->CrmProject->CrmProjectDocument->find(
				'neighbors',
				array(
					'field' => 'CrmProjectDocument.id',
					'value' => $id,
					'conditions' => array(
						'CrmProjectDocument.company_id' => Configure::read('Principle.id'),
						'CrmProjectDocument.owner_id' => $owner_id,
						'CrmProjectDocument.document_type' => 'property',
						'CrmProjectDocument.status' => 1,
					),
					'order' => array(
						'CrmProjectDocument.created' => 'DESC',
						'CrmProjectDocument.id' => 'DESC',
					),
				)
			);
			$documentCategories = $this->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('list', array(
				'conditions' => array(
					'DocumentCategory.type' => 'property',
				),
			));
			$urlDoc = array(
				'controller' => 'properties',
				'action' => 'admin_document_edit',
				'admin' => true,
			);

			if($this->Rest->isActive()){
				$value = $data;
			}

			$this->set(compact(
				'value', 'id', 'neighbors',
				'documentCategories', 'urlDoc'
			));
			$this->render('/Crm/admin_project_document_edit');
		} else {
			if(!$this->Rest->isActive()){
				$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
			}
		}

		$data = $this->RmCommon->_callUnset(array(
			'CrmProjectDocument' => array(
				'id',
				'save_path',
				'company_id',
				'crm_project_activity_id',
				'document_type',
				'session_id',
				'name',
				'file',
				'owner_id',
				'status',
				'created',
				'modified'
			)
		), $value);

		$this->set('data', $data);

		$this->RmCommon->renderRest();
	}

	public function admin_document_delete( $id = false ) {
	//	tambahan kalo request lewat easy mode ===================================================

		$isAjax			= $this->RequestHandler->isAjax();
		$isEasyMode		= Common::hashEmptyField($this->request->data, 'is_easy_mode');
		$wrapperAjax	= Common::hashEmptyField($this->request->data, '_wrapper_ajax');
		$resultOptions	= array();

		if($isAjax && $isEasyMode && $wrapperAjax){
			$this->layout	= 'ajax';
			$resultOptions	= array(
				'ajaxFlash'		=> true,
				'ajaxRedirect'	=> true,
			);

			$this->set(array(
				'_wrapper_ajax'	=> $wrapperAjax, 
				'_data_reload'	=> false, 
				'is_easy_mode'	=> true, 
			));
		}

		$this->request->data = Hash::remove($this->request->data, 'params');
		$this->request->data = Hash::remove($this->request->data, 'is_easy_mode');
		$this->request->data = Hash::remove($this->request->data, '_wrapper_ajax');
		$this->request->data = Hash::remove($this->request->data, 'CrmProjectDocument.colview_default');

	//	=========================================================================================

		$data = $this->request->data;
		$media_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'media_id');
		$id = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument', 'id', $media_id);

		$result = $this->User->CrmProject->CrmProjectDocument->doDelete( $id );
		$propertyID = $this->User->CrmProject->CrmProjectDocument->field('CrmProjectDocument.owner_id', array(
			'CrmProjectDocument.id' => $id,
		));

		$urlRedirect = array();

		if($isEasyMode && $propertyID){
			$urlRedirect = array(
				'admin'			=> true,
				'controller'	=> 'properties',
				'action'		=> 'easy_media',
				$propertyID,
				'document', 
			);
		}

		$this->RmCommon->setProcessParams($result, $urlRedirect, $resultOptions);
		$this->RmCommon->renderRest();
	}

	function admin_refresh( $id = false ){
		$is_refresh_listing	= $this->RmCommon->filterEmptyField($this->data_company, 'UserCompanyConfig', 'is_refresh_listing');
		$authGroupID		= Configure::read('User.group_id');
		$isIndependent		= Common::validateRole('independent_agent', $authGroupID);

		if( !empty($is_refresh_listing) || $isIndependent ) {
			$date = $this->RmCommon->currentDate('Y-m-d');
			$value = $this->Property->getData('first', array(
				'conditions' => array(
					'Property.id' => $id,
					'OR' => array(
						"DATE_FORMAT(Property.refresh_date, '%Y-%m-%d') <>" => $date,
						'Property.refresh_date' => NULL,
					),
				),
			), array(
				'admin_mine' => true,
			));

			if( !empty($value) ){
				$result = $this->Property->doRefresh($id);
				$this->RmCommon->setProcessParams($result);
			} else {
				$this->RmCommon->redirectReferer(__('Properti tidak ditemukan atau sudah pernah di-refresh sebelumnya'), 'error');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	function admin_refresh_all(){
		$is_refresh_listing	= $this->RmCommon->filterEmptyField($this->data_company, 'UserCompanyConfig', 'is_refresh_listing');
		$authGroupID		= Configure::read('User.group_id');
		$isIndependent		= Common::validateRole('independent_agent', $authGroupID);

		if( !empty($is_refresh_listing) || $isIndependent ) {
			$date = $this->RmCommon->currentDate('Y-m-d');
			$id = $this->Property->getData('list', array(
				'conditions' => array(
					'OR' => array(
						"DATE_FORMAT(Property.refresh_date, '%Y-%m-%d') <>" => $date,
						'Property.refresh_date' => NULL,
					),
				),
				'fields' => array(
					'Property.id', 'Property.id',
				),
			), array(
				'admin_mine' => true,
			));

			if( !empty($id) ){
				$result = $this->Property->doRefresh($id);
				$this->RmCommon->setProcessParams($result);
			} else {
				$this->RmCommon->redirectReferer(__('Properti tidak ditemukan atau sudah pernah di-refresh sebelumnya'), 'error');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	function admin_premium( $id = false ){
		$value = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
				'Property.featured' => false,
			),
		), array(
			'admin_mine' => true,
		));

		if( !empty($value) ){
			$data_config = false;

			$result = $this->Property->doPremium($id, $data_config, $membership_agent, $value);
			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_unpremium($id = FALSE){
		$result = $this->Property->doUnPremium($id, $this->data_company);
		$this->RmCommon->setProcessParams($result, FALSE, array('redirectError' => TRUE));
	}

	function _calDataSoldConvertion ( $data, $reverse = false ) {
		return $this->RmCommon->dataConverter($data, array(
			'date' => array(
				'PropertySold' => array(
					'sold_date',
					'end_date',
				),
			),
			'price' => array(
				'PropertySold' => array(
					'price_sold',
				),
			)
		), $reverse);
	}

	public function admin_sold( $id = false ) {
		$user_id = $this->user_id;
		if(Configure::read('User.admin')){
			$user_id = false;
		}

		$property = $this->Property->getProperty('first', $user_id, $id, 'all');

		if( !empty($property) ) {
			$property = $this->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertyMediasCount',
					'User',
					'CoBrokeProperty'
				),
			));

			$property_action_id = Common::hashEmptyField($property, 'Property.property_action_id');
			$user_id 			= Common::hashEmptyField($property, 'Property.user_id');

			$property = $this->Property->PropertyAction->getMerge($property, $property_action_id, 'PropertyAction.id', array(
				'cache' => array(
					'name' => __('PropertyAction.%s', $property_action_id),
				),
			));
			$property = $this->User->getMerge($property, $user_id);

			if( !empty($this->request->data) ) {
				$data = $this->request->data;
				$data['PropertySold']['property_action_id'] = $property_action_id;
				$data['PropertySold']['property_id'] = $id;

				$data = $this->_calDataSoldConvertion($data);
				$data = $this->RmProperty->_callBeforeSold($data, $id);

				$result = $this->Property->PropertySold->doSave( $data, $id );
				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
				));
			} else {

				$client_id = Common::hashEmptyField($property, 'Property.client_id');
				$data_client = $this->User->getMerge(array(), $client_id);

				$this->request->data['PropertySold']['sold_by_name'] = Common::hashEmptyField($property, 'User.email');
				$this->request->data['PropertySold']['currency_id'] = Common::hashEmptyField($property, 'Property.currency_id');
				$this->request->data['PropertySold']['price_sold'] = Common::hashEmptyField($property, 'Property.price');
				$this->request->data['PropertySold']['period_id'] = Common::hashEmptyField($property, 'Property.period_id');
			}

			$periods 	= $this->Property->PropertyPrice->Period->getData('list', array(
	            'cache' => __('Period.List'),
	        ));
			$currencies = $this->Property->Currency->getData('list', array(
				'fields' => array(
					'Currency.id', 'Currency.alias',
				),
				'cache' => __('Currency.alias'),
			));

			if(!empty($property['CoBrokeProperty'])){
				$co_broke_id = Common::hashEmptyField($property, 'CoBrokeProperty.id');

				$list_user_cobroke = $this->User->CoBrokeUser->getData('list', array(
					'conditions' => array(
						'CoBrokeUser.co_broke_property_id' => $co_broke_id
					),
					'fields' => array(
						'CoBrokeUser.id', 'CoBrokeUser.name'
					)
				), array(
					'status' => 'approve',
					'admin_reverse' => true
				));

				$this->set(compact('list_user_cobroke'));
			}

			$this->set('type_commission', $this->RmCommon->getGlobalVariable('type_commision_cobroke'));

			$this->set(compact(
				'property', 'currencies', 'periods'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_sold_preview( $id = false ) {
		$value = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
			),
		), array(
			'status' => 'sold',
			'admin_mine' => Configure::read('User.admin')
		));

		if( !empty($value) ) {
			$propertySold = $this->Property->getDataList($value, array(
				'contain' => array(
					'PropertySold',
				),
			));
			$value = $this->Property->getDataList($value, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertyMediasCount',
					'User',
				),
			));
			$currency_id = $this->RmCommon->filterEmptyField($propertySold, 'PropertySold', 'currency_id');
			$sold_by_id = $this->RmCommon->filterEmptyField($propertySold, 'PropertySold', 'sold_by_id');
			$property_action_id = $this->RmCommon->filterEmptyField($propertySold, 'Property', 'property_action_id');

			$propertySold = $this->Property->Currency->getMerge($propertySold, $currency_id, 'Currency.id', array(
				'cache' => array(
					'name' => __('Currency.%s', $currency_id),
				),
			));
			$propertySold = $this->User->getMerge($propertySold, $sold_by_id);
			$propertySold = $this->Property->PropertyAction->getMerge($propertySold, $property_action_id, 'PropertyAction.id', array(
				'cache' => array(
					'name' => __('PropertyAction.%s', $property_action_id),
				),
			));

			$period_id = $this->RmCommon->filterEmptyField($propertySold, 'PropertySold', 'period_id');

			if(!empty($propertySold['PropertySold']) && !empty($period_id)){
				$propertySold['PropertySold'] = $this->Property->Period->getMerge($propertySold['PropertySold'], $period_id);
			}

			$this->RmCommon->_callDataForAPI($value, 'manual');

			$this->set('_item_spec', false);
			$this->set('_item_sold', true);
			$this->set(compact(
				'value', 'propertySold'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_unsold( $id = false ) {
		$user_id = $this->user_id;
		if(Configure::read('User.admin')){
			$user_id = false;
		}

		$property = $this->Property->getProperty('first', $user_id, $id, 'sold');

		if( !empty($property) ) {
			$result = $this->Property->doUnsold( $id );
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'properties',
				'action' => 'index',
				'admin' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_delete( $id = false ) {
		if( $this->RmCommon->_callAllowAccess('is_delete_property') ) {
			$isAdmin = Configure::read('User.Admin.Rumahku');
			$value = $this->Property->getData('first', array(
				'conditions' => array(
					'Property.id' => $id,
					'Property.deleted' => false,
				),
			), array(
				'status' => 'all',
				'admin_mine' => !empty($isAdmin)?false:true,
				'company' => !empty($isAdmin)?false:true,
			));

			if( !empty($value) ) {
				$result = $this->Property->doToggle( $id, 'deleted', 'menghapus properti', 1 );
				$this->RmCommon->setProcessParams($result);
			} else {
				$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_add_draft() {
		$draft_id = Configure::read('__Site.PropertyDraft.id');
		$draft = $this->Property->PropertyDraft->getMerge($draft_id);
		$id = $this->RmCommon->filterEmptyField($draft, 'PropertyDraft', 'id');

		$property = $this->_callSessionProperty();
		$data = $this->request->data;

		$data = $this->RmProperty->_callBeforeSave($data, false, false);
		$data = $this->RmProperty->_callBeforeSaveDraft( $data, $property );
		$session_id = $this->RmCommon->filterEmptyField($data, 'Property', 'session_id');
		$result = $this->Property->PropertyDraft->doSave( $data, $session_id, $id );

		$status = $this->RmCommon->filterEmptyField($result, 'status');

		if( $status == 'success' ) {
			$this->RmProperty->_callDeleteSession();
		}

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'properties',
			'action' => 'drafts',
			'admin' => true,
		));
	}

	public function admin_drafts() {
		$options =  $this->Property->PropertyDraft->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_admin_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->Property->PropertyDraft->getData('paginate', $options);
		$values = $this->paginate('PropertyDraft');

		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$content = $this->RmCommon->filterEmptyField($value, 'PropertyDraft', 'content');
				$property = unserialize($content);

				if( !empty($property) ) {
					$property = $this->Property->getDataList($property, array(
						'contain' => array(
							'MergeDefault',
							'PropertyAsset',
						),
					));
				}

				if( empty($property) ) {
					$property = array();
				}

				$value = array_merge($value, $property);
				$values[$key] = $value;
			}
		}

		$this->set('_draft', true);
		$this->set('fullDisplay', false);
		$this->set('active_menu', 'property_draft');
		$this->set('module_title', __('Draft Properti'));
		$this->set(compact(
			'values'
		));
	}

	public function admin_draft_delete( $id = false ) {
		$value = $this->Property->PropertyDraft->getData('first', array(
			'conditions' => array(
				'PropertyDraft.id' => $id,
			),
		));

		if( !empty($value) ) {
			$result = $this->Property->PropertyDraft->doToggle( $id );
			$this->RmCommon->setProcessParams($result);
		} else {
			$this->RmCommon->redirectReferer(__('Draft tidak ditemukan'), 'error');
		}
	}

	public function admin_draft_edit( $id = false ) {
		$value = $this->Property->PropertyDraft->getById($id);
		$dataAsset = array();
		$dataBasic['Property'] = $this->RmCommon->filterEmptyField($value, 'Property');
		$dataAddress['PropertyAddress'] = $this->RmCommon->filterEmptyField($value, 'PropertyAddress');
		$dataMedias['PropertyMedias'] = $this->RmCommon->filterEmptyField($value, 'PropertyMedias');

		$value['Property'] = $this->RmCommon->_callSet(array(
			'price',
			'period_id',
			'certificate_id',
			'commission',
			'currency_id',
		), $dataBasic['Property']);

		$dataAsset = $this->RmCommon->_callMergeRecursive($dataAsset, $value, array(
			'Property',
			'PropertyAsset',
			'PropertyFacility',
			'PropertyPointPlus',
			'PropertyPrice',
			'PageConfig',
		));

		$this->Session->write(sprintf('Session.PropertyDraft.%s.%s', $this->basicLabel, $id), $dataBasic);
		$this->Session->write(sprintf('Session.PropertyDraft.%s.%s', $this->addressLabel, $id), $dataAddress);
		$this->Session->write(sprintf('Session.PropertyDraft.%s.%s', $this->assetLabel, $id), $dataAsset);
		$this->Session->write(sprintf('Session.PropertyDraft.%s.%s', $this->mediaLabel, $id), $dataMedias);
		$this->redirect(array(
			'controller' => 'properties',
			'action' => 'sell',
			'draft' => $id,
			'admin' => true,
		));
	}

	function find(){
		$params = $this->params->params;
		$id_highlight = Common::hashEmptyField($params, 'named.id_status_listing');

		if (!empty($id_highlight)) {
			$this->set('active_menu', 'highlight');
		} else {
			$this->set('active_menu', 'list_properties');
		}

	//	cache setting
		$controller		= $this->name;
		$action			= Inflector::camelize($this->action);
		$dataCompany	= isset($this->data_company) ? $this->data_company : NULL;
		$companyID		= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompany', 'id', 0);

		if($this->params->property_action){
			$propertyAction = $this->params->property_action == 'dijual' ? 1 : 2;

			$this->request->params['named']['property_action'] = $propertyAction;
		}
		else{
			$propertyAction	= Common::hashEmptyField($params, 'named.property_action', 1);
		}

		$currentPage	= Common::hashEmptyField($params, 'named.page', 1);
		$cacheName		= $controller.'.'.$action.'.'.$companyID.'.'.$propertyAction.'.'.$currentPage;
		$cacheConfig	= 'properties_find';

	//	$cacheData		= Cache::read($cacheName, $cacheConfig);
		$namedParams	= array_keys($params['named']);
		$nonFilter		= array('page', 'property_action', 'show');
		$filterParams	= array_diff($namedParams, $nonFilter);

		$options = $this->Property->_callRefineParams($params, array(
			'limit' => 24,
		));

		if (!empty($id_highlight)) {
			$highlight_option = array(
				'conditions' => array(
					'Property.property_status_id' => $id_highlight
				),
			);
			$options = array_merge_recursive($options, $highlight_option);
		}

		$_property_action = $propertyAction;
		$property_action = $this->Property->PropertyAction->getMerge(array(), $_property_action, 'PropertyAction.id', array(
			'cache' => array(
				'name' => __('PropertyAction.%s', $_property_action),
			),
		));
		$actionName = $this->RmCommon->filterEmptyField($property_action, 'PropertyAction', 'name');

		if(!empty($property_action)){
			$this->RmCommon->_callRefineParams($params);
			$displayShow	= Common::hashEmptyField($params, 'named.show', 'grid');
			$displayStyle	= sprintf('%s-style', $displayShow);

			if(empty($filterParams) && !empty($cacheData)){
			//	find all query, get results from cache (if exist)
				$this->request->params['paging']		= $cacheData['paging'];
				$this->request->params['named']			= $cacheData['named'];
				$this->request->params['named']['show']	= $displayShow;
				$this->request->params['pass']			= $cacheData['pass'];
				$this->request->query					= $cacheData['query'];

				$properties = $cacheData['result'];
			}
			else{
				$this->paginate = $this->Property->getData('paginate', $options, array(
					'status' => 'active-pending-sold',
					'company' => true,
					'skip_is_sales' => true,
				));
				
				$properties = $this->paginate('Property');

				if( !empty($properties) ) {
					foreach ($properties as $key => &$value) {
						$value = $this->Property->getDataList($value, array(
							'contain' => array(
								'MergeDefault',
								'PropertyAddress',
								'PropertyAsset',
								'PropertySold',
								'User',
								'PropertyMediasCount',
								'PropertyStatusListing',
							),
						));

						if( $this->RmCommon->_callIsDirector() ) {
							$value = $this->User->getMergeList($value, array(
								'contain' => array(
									'UserCompanyConfig' => array(
										'primaryKey' => 'user_id',
										'foreignKey' => 'parent_id',
									),
								),
							));
						}
					}
				}

				if( empty($filterParams) ){
				//	find all query, generate cache
					$cacheData = array(
						'paging'	=> $this->request->params['paging'],
						'named'		=> $this->request->params['named'],
						'pass'		=> $this->request->params['pass'],
						'query'		=> $this->request->query,
						'result'	=> $properties
					);

					Cache::write($cacheName, $cacheData, $cacheConfig);
				}
			}

			$this->RmCommon->_callRequestSubarea('Search');
			$this->RmCommon->getDataRefineProperty();

			// Khusus Realsite
			$agents = $this->User->populers();

			if( $displayShow == 'grid' ){
				$displayStyle = sprintf('%s1', $displayStyle);
			}

			$url_without_http = Configure::read('__Site.domain');

			$UserCompany 	= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompany');
			$company_name 	= $this->RmCommon->filterEmptyField($UserCompany, 'name', false, '');

			$module_title = sprintf(__('Properti %s'), $actionName);

			$page = $this->RmCommon->filterEmptyField($params, 'named', 'page', '');

			if(!empty($page)){
				$page = sprintf(__(' Page %s'), $page);
			}

			$title_for_layout = sprintf('%s%s - %s', $module_title, $page, $url_without_http);
			$keywords_for_layout = sprintf(__('%s murah di %s'), $module_title, $url_without_http);
			$description_for_layout = sprintf(__('Cari %s%s di %s dengan harga properti terjangkau!'), $module_title, $page, $url_without_http);
	        
	        $companyData = Configure::read('Config.Company.data');
	        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

			if( $group_id == 4 ) {
				$options = $this->User->getData('paginate', false, array(
					'status' => 'semi-active',
					'company' => true,
					'role' => 'principle',
				));
				$companies =  $this->RmCommon->_callCompanies('principle', $options);
				$this->set(array(
					'list_companies' => $companies,
				));
			}

			$mt_propertyTypes	= $this->RmMarketTrend->getCompanyPropertyType();
			$mt_location		= $this->RmUser->getLocation($this->params->named, array(
				'use_default'	=> true, 
				'market_trend'	=> true, 
			));

			$this->set('flag_menu', 'properties');
			$this->set(compact(
				'properties', 'displayShow', '_property_action',
				'agents', 'module_title',
				'title_for_layout', 'keywords_for_layout', 'description_for_layout',
				'displayStyle', 'certificates', 'mt_location', 'mt_propertyTypes'
			));
		} else {
			$this->redirect(array(
				'controller' => 'properties',
				'action' => 'find',
				'property_action' => 'dijual',
				'admin' => false,
			), array(
				'status' => 301
			));
		}
	}

	public function detail(){
		$module_title = __('Detail Properti');
		$mlsid = $this->RmCommon->filterEmptyField($this->params, 'mlsid');
		$slug = $this->RmCommon->filterEmptyField($this->params, 'slug');

		if( !empty($mlsid) ){
		//	cache setting
			$controller		= $this->name;
			$action			= Inflector::camelize($this->action);
			$dataCompany	= isset($this->data_company) ? $this->data_company : NULL;
			$companyID		= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompany', 'id', 0);
			$cacheName		= $controller.'.'.$action.'.'.$companyID.'.'.$mlsid;
			$cacheConfig	= 'properties_detail';
			$cacheData		= Cache::read($cacheName, $cacheConfig);

			if( !empty($cacheData['result']) ){
				$this->request->params['named']	= $cacheData['named'];
				$this->request->params['pass']	= $cacheData['pass'];
				$this->request->query			= $cacheData['query'];
				$value							= $cacheData['result'];
			}
			else{
				$value = $this->Property->getData('first', array(
					'conditions' => array(
						'Property.mls_id' => $mlsid,
					),
				), array(
					'status' => 'active-pending-sold',
					'restrict_type' => 'mine',
					'company' => true,
					'skip_is_sales' => true,
				));
				$value = $this->Property->getDataList($value, array(
					'contain' => array(
						'MergeDefault',
						'PropertyAddress',
						'PropertyAsset',
						'PropertySold',
						'PropertyFacility',
						'PropertyPointPlus',
						'PropertyStatusListing',
						'PropertyPrice',
						'User',
					),
				));

				if(!Configure::read('User.admin')){
					$cacheData = array(
						'named'		=> $this->request->params['named'],
						'pass'		=> $this->request->params['pass'],
						'query'		=> $this->request->query,
						'result'	=> $value
					);

					Cache::write($cacheName, $cacheData, $cacheConfig);
				}
			}

			if( !empty($value) ) {
				$id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
				$user_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');
				$title = $this->RmCommon->filterEmptyField($value, 'Property', 'title');
				$photo = $this->RmCommon->filterEmptyField($value, 'Property', 'photo');
				$description = $this->RmCommon->filterEmptyField($value, 'Property', 'description');
				$active = $this->RmCommon->filterEmptyField($value, 'Property', 'active');
				$period_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'period_id');

				$value = $this->Property->PageConfig->getMerge($value, $id);
				$value = $this->User->UserProfile->getMerge($value, $user_id, true);
				$value = $this->User->UserConfig->getMerge($value, $user_id);
				$label = $this->RmProperty->getNameCustom($value);
				$label = $this->RmCommon->toSlug($label);

				if(!empty($value['PropertySold'])){
					$value['PropertySold'] = $this->Property->Period->getMerge($value['PropertySold'], $period_id);
				}

				if( $label == $slug ) {
					if( !empty($active) ) {
						$statusMedia = 'active';
					} else {
						$statusMedia = 'all';
					}

					$value = $this->Property->PropertyMedias->getMerge($value, $id, 'all', $statusMedia);
					$value = $this->Property->PropertyVideos->getMerge($value, $id, 'all', $statusMedia);

					// Get Bank Exclusive
					// $bankKpr = $this->User->Kpr->KprBank->Bank->getKpr();
					// Get All Bank Active n Bank Only Product
					$list_banks = $this->User->Kpr->KprBank->Bank->list_banks($value);
					
					// Proses Contact
					$data = $this->request->data;
					$hastag = $this->RmCommon->filterEmptyField($data, 'Message', 'hastag');
					$data = $this->RmUser->_callMessageBeforeSave($user_id, $id);
					$result = $this->User->Message->doSend($data, $value);
					
					$base_url = FULL_BASE_URL;
					$url_detail = $base_url.$this->params->here;

					$this->RmCommon->setProcessParams($result, $url_detail,array(
					//	google recaptcha
					//	'ajaxFlash' => true,
					//	'ajaxRedirect' => true,
					));

					$dataView = $this->RmCommon->_callSaveVisitor($id, 'PropertyView');
					$this->Property->PropertyView->doSave($dataView);

					$neighbours = $this->Property->getNeighbours( $value );
					$agents = $this->User->populers();

					$this->RmCommon->_callRequestSubarea('Search');
					$this->RmCommon->getDataRefineProperty();

					$og_meta = array(
						'title' => $title,
						'image' => $photo,
						'path' => Configure::read('__Site.property_photo_folder'),
						'description' => $description,
						'size' => 'company'
					);
					$this->RmCommon->_layout_file(array(
						'map',
						'map-cozy',
						'bank',
					));

					$meta_title = $this->RmCommon->filterEmptyField($value, 'PageConfig', 'meta_title');
					$meta_keyword = $this->RmCommon->filterEmptyField($value, 'PageConfig', 'meta_keyword');
					$meta_description = $this->RmCommon->filterEmptyField($value, 'PageConfig', 'meta_description');

					$url_without_http = Configure::read('__Site.domain');

					$property_type 	= $this->RmCommon->filterEmptyField($value, 'PropertyType', 'name');
					$property_act 	= $this->RmCommon->filterEmptyField($value, 'PropertyAction', 'name');
					$property_act_id= $this->RmCommon->filterEmptyField($value, 'PropertyAction', 'id');

					$PropertyAddress = $this->RmCommon->filterEmptyField($value, 'PropertyAddress');

					$subarea_name 	= $this->RmCommon->filterEmptyField($PropertyAddress, 'Subarea', 'name');
					$zip 			= $this->RmCommon->filterEmptyField($PropertyAddress, 'zip');
					$city_name 		= $this->RmCommon->filterEmptyField($PropertyAddress, 'City', 'name');

					if(empty($meta_title)){
						$meta_title = sprintf('%s %s %s, %s %s - %s', $property_type, $property_act, $subarea_name, $city_name, $mlsid, $url_without_http);
					}

					if(empty($meta_keyword)){
						$meta_keyword = sprintf('%s %s %s, %s %s %s di %s', $property_type, $property_act, $subarea_name, $city_name, $zip, $mlsid, $url_without_http);
					}

					if(empty($meta_description)){
        				if($property_act_id == 2){
        					$price = $this->RmProperty->_callRentPrice($value, false, false, false);
        				}else{
        					$price = $this->RmProperty->getPrice($value, false, false, false);
        				}

						$meta_description = sprintf(__('%s %s %s, %s %s %s %s di %s dengan harga properti terjangkau!'), $property_type, $property_act, $subarea_name, $city_name, $zip, $mlsid, $price, $url_without_http);
					}

					$this->set('title_for_layout', $meta_title);
					$this->set('keywords_for_layout', $meta_keyword);
					$this->set('description_for_layout', $meta_description);

					$this->set('_canonical', true);
					$this->set('captcha_code', $this->Captcha->generateEquation());
					$this->set('active_menu', 'list_properties');
					$this->set(compact(
						'value', 'module_title',
						'og_meta', 'neighbours', 'agents', 
						'bankKpr', 'list_banks'
					));
				} else {
					$this->redirect(array(
						'controller' => 'properties',
						'action' => 'detail',
						'mlsid' => $mlsid,
						'slug' => $label,
						'admin' => false,
					), array(
						'status' => 301
					));
				}
			} else {
				$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
			}
		}else{
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	function contact( $id = false, $no_modal = false ){
		$module_title = __('Kirim Pesan');
		$value = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
			),
		), array(
			'status' => 'active-pending-sold',
			'restrict_type' => 'mine',
			'company' => true,
			'skip_is_sales' => true,
		));

		if( !empty($value) ){
			$user_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');

			$data = $this->RmUser->_callMessageBeforeSave($user_id, $id);

			$isAjax		= $this->RequestHandler->isAjax();
			$result		= $this->User->Message->doSend($data, $value);
			$status		= Common::hashEmptyField($result, 'status', 'error');
			$message	= Common::hashEmptyField($result, 'msg');

			if( $status == 'success' ) {
				$message = __('Selamat, pesan Anda telah berhasil dikirim. Pastikan data informasi yg Anda berikan benar dan valid, karena sewaktu-waktu Agen dapat menghubungi Anda');

				$result['data']	= 'reset';
				$result['msg']	= $message;

				$this->set('message', $message);
			}

			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash'		=> $isAjax,
				'modal'			=> !empty($no_modal)?false:'success',
				'redirectError'	=> true, 
			));

			$this->set('captcha_code', $this->Captcha->generateEquation());
			$this->set(compact(
				'value', 'module_title'
			));
		}else{
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'));
		}
	}

	function admin_preview($id){
		if(!empty($id)){
			$data_revision = $this->Property->PropertyRevision->getRevision($id);
			$data = $this->request->data;
			$temp_arr = array();

			$last_modify = false;

			if(!empty($data_revision)){
				$arr_created_rev = Set::extract('/PropertyRevision/created', $data_revision);

				$last_modify = max($arr_created_rev);
			}

			if(!empty($data)){
				$property = $this->_callDataProperty($id, false, false);
				$property = $this->Property->PropertyNotification->getMerge($property, $id);
				$property = $this->Property->PropertyPrice->generatePrice($property);

				$result = array(
					'msg' => __('Berhasil melakukan persetujuan revisi properti.'),
					'status' => 'success'
				);

				if(Configure::read('User.admin') && (!empty($data_revision) || !empty($data['PropertyMedias']['options_id']) || !empty($data['PropertyVideos']['options_id']))){
					$all_medias = array();

					if(!empty($data['PropertyMedias']['options_id']) || !empty($data['PropertyVideos']['options_id'])){
						// update media

						if(!empty($data['PropertyMedias']['options_id'])){
							unset($property['Property']['photo']);
							$this->User->Property->PropertyMedias->approveMultiple($id, $data['PropertyMedias']['options_id']);
						}else{
							$this->User->Property->PropertyMedias->declineApproval($id);
						}

						// update video
						if(!empty($data['PropertyVideos']['options_id'])){
							$this->User->Property->PropertyVideos->approveMultiple($id, $data['PropertyVideos']['options_id']);
						}else{
							$this->User->Property->PropertyVideos->declineApproval($id);
						}

						if(empty($data_revision)){
							$this->User->Property->inUpdateChange($id, false);
						}

						/*ambil data media untuk di sync*/
						$all_medias = $this->getAllMedias($id, 'active');
					}

					$data_revision = $this->RmProperty->shapingArrayRevision($data_revision);
					$data = $this->RmProperty->generateRequestDataRevision($data);

					if(!empty($data)){
					 	$property = $this->RmCommon->_callUnset(array(
	                        'Property' => array(
	                        	'client_email',
                        	),
	                    ), $property);

						$result = $this->RmProperty->compareDataRevision($data, $property, $data_revision);
						$result = $this->RmProperty->_callBeforeSave($result, $result);

						$co_broke_commision = $this->RmCommon->filterEmptyField($result, 'Property', 'co_broke_commision');

						$this->Property->removeValidate();
						$result = $this->Property->saveRevisiData($result);

						if($result['status'] == 'success'){
							$this->Property->PropertyRevision->unactivateRevision($id);

							$this->Property->CoBrokeProperty->deleteCoBroke($id, $co_broke_commision);
						}
					}

					if(!empty($property['PropertyAsset'])){
						$temp_arr['PropertyAsset'] = $property['PropertyAsset'];
						unset($property['PropertyAsset']);
					}

					$data_revision = array_merge($data_revision, $all_medias);
				}else{
					$this->User->Property->PropertyMedias->declineApproval($id);
					$this->User->Property->PropertyVideos->declineApproval($id);
				}

				$this->User->Property->inUpdateChange($id, false);

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'properties',
					'action' => 'preview',
					$id,
					'admin' => true,
				));
			} else {
				$property = $this->_callDataProperty($id);

				if(!empty($data_revision)){
					$data_revision = $this->RmProperty->shapingArrayRevision($data_revision);
				}
			}

			if( !empty($property) ){
        		$user_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id');
				$property = $this->Property->PropertyNotification->getMerge($property, $id);
				$property = $this->Property->PropertyPrice->generatePrice($property);

				if(!empty($temp_arr['PropertyAsset'])){
					$property = $this->RmProperty->mergeArrayRecursive($property, $temp_arr);
				}

				$property = $this->Property->getDataList($property, array(
					'contain' => array(
						'PropertyVideos'
					),
				));
				$property = $this->Property->PropertyMedias->getMerge($property, $id, 'all', 'all');
				$count_property = $this->Property->getData('count',array(
					'conditions' => array(
						'Property.user_id' => $user_id,
					),
				));

				$module_title = __('Pratinjau Properti');
				$this->RmCommon->_layout_file('map');

				$this->loadModel('Facility');
				$facilities = $this->Facility->getData('list');

				$this->set(compact(
					'property', 'data_revision', 'module_title', 'count_property',
					'facilities', 'last_modify'
				));
				$this->set('tabs_action_type', 'image_approval');
			}else{
				$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
			}
		}else{
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	function create_ebrosur($property_id){
		$result = false;

		$config = $this->RmCommon->filterEmptyField($this->data_company, 'UserCompanyConfig');

		$is_brochure = $this->RmCommon->filterEmptyField($config, 'is_brochure');
		$auto_create_ebrochure = $this->RmCommon->filterEmptyField($config, 'auto_create_ebrochure');
		$is_approval_property = $this->RmCommon->filterEmptyField($config, 'is_approval_property');

		$approval_set = ((Configure::read('User.admin') && !empty($is_approval_property)) || empty($is_approval_property)) ? true : false;

		if( !empty($property_id) && $approval_set && $is_brochure && $auto_create_ebrochure ){
			$property = $this->Property->getData('first',
				array(
					'conditions' => array(
						'Property.id' => $property_id
					),
				),
				array(
					'status' => 'all'
				)
			);

			if(!empty($property)){
				$property = $this->Property->getDataList($property, array(
					'contain' => array(
						'MergeDefault',
						'PropertyAddress',
						'PropertyAsset',
						'PropertySold',
						'User'
					),
				));

				/*begin - Rule price properti*/
				App::import('Helper', 'Property');
				$Property = new PropertyHelper(new View(null));
				$data_component_price = $Property->getPrice($property, false, true);

				$data_price = $this->RmCommon->filterEmptyField($data_component_price, 'price', false);
				if(!empty($data_price)){
					$property['Property']['price'] = $data_price;
				}

				$property['Property']['period_id'] = $this->RmCommon->filterEmptyField($data_component_price, 'period_id', false);
				$property['PropertyAsset']['lot_unit_id'] = $this->RmCommon->filterEmptyField($data_component_price, 'lot_unit_id', false);
				/*end - Rule price properti*/

				$property = $this->Property->PropertyMedias->getMergePrimaryPhoto($property, $property_id);
				$property = $this->User->UserProfile->getMerge($property, $property['Property']['user_id']);

				if(!empty($property['PropertyMedias'])){
					$data = $this->RmEbroschure->convertSetPropertyToEbrosur($this->data_company, $property);

					$result = $this->RmEbroschure->_callBeforeSave($data, $this->data_company, $this->data_company, $property['User'], false, array(), false);

					if(!empty($result['status']) && $result['status'] == 'success'){
						$result = true;
					}else{
						$this->RmCommon->_saveLog(__('[EBROSUR] Gagal membuat ebrosur, gagal menyimpan'), $data, $property_id, 1, 307);
					}
				}else{
					$message_error = __('Ebrosur gagal di buat dikarenakan Anda belum memilih foto utama dari properti Anda, silakan membuat ebrosur secara manual.');
					$this->RmCommon->setCustomFlash($message_error, 'error');
				
					$this->RmCommon->_saveLog($message_error, false, $property_id, 1, 307);
				}
			}else{
				$this->RmCommon->_saveLog(__('[EBROSUR] Gagal membuat ebrosur, data properti tidak ditemukan'), false, $property_id, 1, 307);
			}
		}

		return $result;
	}

	function admin_approval($id = null){
		$redirectURL = array(
			'admin'			=> true, 
			'controller'	=> 'properties', 
			'action'		=> 'index', 
		);

		if( !empty($id) && Configure::read('User.admin') ){
			$config = $this->RmCommon->filterEmptyField($this->data_company, 'UserCompanyConfig');
			$result = $this->Property->approve($id, $this->data_company);

			if(!empty($result['status']) && $result['status'] == 'success' ){
				$isAdmin		= Configure::read('User.admin');
				$companyData	= Common::config('Config.Company.data', array());
				$isNeedApproval	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_approval_property');
				$isBuilder		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_ebrochure_builder');

				$isAutoApprove	= ($isAdmin && $isNeedApproval) || empty($isNeedApproval);
				$isAutoGenerate = $this->RmEbroschure->isAllowGenerateEbrochure();

				if($isAutoGenerate && $isBuilder){
				//	ebrochure versi baru
					$redirectURL = array(
						'admin'			=> true, 
						'controller'	=> 'ebrosurs', 
						'action'		=> 'regenerate', 
						'property_id'	=> $id, 
					);
				}
				else if($isAutoApprove){
				//	ebrochure versi lama
					$this->create_ebrosur($id);
					$redirectURL = array();
				}

				$this->RmCoBroke = $this->Components->load('RmCoBroke');
				$this->RmCoBroke->create_cobroke($id);
			}

			$this->RmCommon->setProcessParams($result, $redirectURL, array(
				'redirectError' => true, 
			));
		}
		else{
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error', $redirectURL);
		}
	}

	function admin_rejected( $id = false ){
		$property = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
			),
		), array(
			'status' => 'pending-or-update',
		));

		if( !empty($property) ) {
			if( !empty($this->request->data) ) {
				$data = $this->request->data;
				$data['PropertyNotification']['property_id'] = $id;

				$result = $this->Property->PropertyNotification->doSave( $data, $property, $id );
				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
				));
			}

			$this->set('_flash', false);
			$this->set('_flash_error', true);
			$this->set(compact(
				'property', 'result'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	/*
		fungsi ini jalan jika properti sudah pernah disetujui sebelumnya
		tetapi tidak terjual atau tersewa
	*/
	function saveRevision($result, $property_id, $data, $step = false){
		$is_admin = Configure::read('User.admin');
		$approval = Configure::read('Config.Approval.Property');
		$property = $this->_callDataProperty($property_id, false, false, false);
		
		$result_status = $this->RmCommon->filterEmptyField($result, 'status');

		$co_broke_type = Common::hashEmptyField($data, 'Property.co_broke_type');

		if( empty($is_admin) && !empty($approval) ) {
			$this->RmCoBroke = $this->Components->load('RmCoBroke');

			$property = $this->RmProperty->_callProcessPricePeriod($property);

			$property = $this->RmCommon->dataConverter($property, array(
                'date' => array(
                    'Property' => array(
                        'contract_date',
                    ),
                )
            ));

			$active = $this->RmCommon->filterEmptyField($property, 'Property', 'active');
			$status = $this->RmCommon->filterEmptyField($property, 'Property', 'status');
			$deleted = $this->RmCommon->filterEmptyField($property, 'Property', 'deleted');

			if( !empty($result['client_id']) ) {
				$data['Property']['client_id'] = $result['client_id'];
			}

			$data = $this->RmUser->_callConvertClientEmail($data);
			$property = $this->RmUser->_callConvertClientEmail($property);

			if( $result_status == 'success' && !empty($active) && !empty($status) ){
				$revision_data = $this->RmProperty->_callSetDataRevision($property_id, $data, $property, $step);
				$revision_result = $this->Property->PropertyRevision->doSave($revision_data, $property, $property_id, $step);

				if($revision_result){
					$this->Property->inUpdateChange($property_id);

					// $this->loadModel('CoBrokeProperty');
					// $this->CoBrokeProperty->doRefresh($property_id);
				}

				/*
						ini di buat fungsi sendiri karena tipe co broke tidak butuh otoritas ketika update
				*/
				$this->RmCoBroke->saveTypeCobroke($property_id, $co_broke_type);

				return $revision_result;
			}else if( $result_status == 'success' && empty($active) && empty($status) && empty($deleted) ){
				$this->Property->inUpdateChange($property_id);
				
				/*
						ini di buat fungsi sendiri karena tipe co broke tidak butuh otoritas ketika update
				*/
				$this->RmCoBroke->saveTypeCobroke($property_id, $co_broke_type);
			}
		}

	}

	public function leads( $id = false ){
		$value = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
			),
		), array(
			'status' => 'active-pending-sold',
			'company' => true,
			'skip_is_sales' => true,
		));

		if( !empty($value) ) {
			$dataLead = $this->RmCommon->_callSaveVisitor($id, 'PropertyLead');
			$this->Property->PropertyLead->doSave($dataLead);
		}

		$this->layout = false;
		$this->render(false);
	}

	public function admin_share( $id = false, $action_type = false ) {
		$user_id = $this->user_id;
		if(Configure::read('User.admin')){
			$user_id = false;
		}

		$property = $this->Property->getProperty('first', $user_id, $id, 'all');

		if( !empty($property) && !empty($action_type) ) {

			if( !empty($this->request->data) ) {
				$this->loadModel('SharingProperty');
				$data = $this->request->data;
				$values = array();
				$template = false;

				if( $action_type == 'visitor' ) {

					$template = 'property_visitor_share';
					$options =  $this->Property->_callRefineParams($this->params, array(
						'limit' => Configure::read('__Site.config_admin_pagination'),
						'conditions' => array(
							'Property.id' => $id,
						),
						'order' => array(
							'PropertyView.created' => 'DESC',
						),
					));
					$elements = array(
						'status' => 'all',
						'admin_mine' => true,
					);

					$propertyOptions = $this->Property->getData('paginate', $options, $elements);
					$propertyOptions['contain'][] = 'Property';

					$this->paginate = $propertyOptions;
					$values = $this->paginate('PropertyView');

					if( !empty($values) ) {
						foreach ($values as $key => $value) {
							$property_id = $this->RmCommon->filterEmptyField($value, 'PropertyView', 'property_id');
							$user_id = $this->RmCommon->filterEmptyField($value, 'PropertyView', 'user_id');

							$value = $this->User->getMerge($value, $user_id);

							if( isset($value['User']) ) {
								$value = $this->User->UserProfile->getMerge($value, $user_id);
							}
							$value = $this->Property->getMerge($value, $property_id);
							$value = $this->Property->getDataList($value, array(
								'contain' => array(
									'MergeDefault',
								),
							));

							$values[$key] = $value;
						}
					}

				} else if( $action_type == 'lead' ) {

					$template = 'property_lead_share';
					$options =  $this->Property->_callRefineParams($this->params, array(
						'limit' => Configure::read('__Site.config_admin_pagination'),
						'order' => array(
							'PropertyLead.created' => 'DESC',
						),
					));
					$elements = array(
						'status' => 'all',
						'admin_mine' => true,
					);

					$propertyOptions = $this->Property->getData('paginate', $options, $elements);
					$propertyOptions['contain'][] = 'Property';

					$this->paginate = $propertyOptions;
					$values = $this->paginate('PropertyLead');

					if( !empty($values) ) {
						foreach ($values as $key => $value) {
							$property_id = $this->RmCommon->filterEmptyField($value, 'PropertyLead', 'property_id');
							$user_id = $this->RmCommon->filterEmptyField($value, 'PropertyLead', 'user_id');

							$value = $this->User->getMerge($value, $user_id);

							if( isset($value['User']) ) {
								$value = $this->User->UserProfile->getMerge($value, $user_id);
							}
							$value = $this->Property->getMerge($value, $property_id);
							$value = $this->Property->getDataList($value, array(
								'contain' => array(
									'MergeDefault',
								),
							));

							$values[$key] = $value;
						}
					}

				} else if( $action_type == 'hotlead' ) {

					$template = 'property_hotlead_share';
					$options =  $this->Property->_callRefineParams($this->params, array(
						'limit' => Configure::read('__Site.config_admin_pagination'),
						'conditions' => array(
							'Property.id' => $id,
						),
						'order' => array(
							'Message.created' => 'DESC',
						),
					));
					$elements = array(
						'status' => 'all',
						'admin_mine' => true,
					);

					$propertyOptions = $this->Property->getData('paginate', $options, $elements);
					$propertyOptions['contain'][] = 'Property';

					$this->paginate = $propertyOptions;
					$values = $this->paginate('Message');

					if( !empty($values) ) {
						foreach ($values as $key => $value) {
							$property_id = $this->RmCommon->filterEmptyField($value, 'Message', 'property_id');
							$user_id = $this->RmCommon->filterEmptyField($value, 'Message', 'user_id');

							$value = $this->Property->getMerge($value, $property_id);
							$value = $this->Property->getDataList($value, array(
								'contain' => array(
									'MergeDefault',
								),
							));

							$values[$key] = $value;
						}
					}
				}

				$result = $this->SharingProperty->doSave( $data, $property, $values, $id, $template );
				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
				));
			}

			$this->set('captcha_code', $this->Captcha->generateEquation());
			$this->set(compact(
				'property', 'result'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	function client_index(){
		$module_title = __('Properti Saya');
		$tabs_action_type = array(
			'controller' => 'properties',
			'action' => 'client_index',
			'client' => true,
		);

		$options =  $this->Property->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_admin_pagination'),
			'conditions' => array(
				'Property.client_id' => $this->user_id,
			),
		));

		$elements = $this->RmCommon->_callRefineParams($this->params);
		$elements['status'] = $this->RmCommon->filterEmptyField($elements, 'status', false, 'all');

		$this->paginate = $this->Property->getData('paginate', $options, $elements);

		$properties = $this->paginate('Property');
		$properties = $this->Property->getDataList($properties, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'PropertyNotification',
				'PropertyMediasCount',
				'User',
			),
		));

		$this->set('active_menu', 'properti');
		$this->set(compact(
			'module_title', 'title_for_layout',
			'properties', 'tabs_action_type'
		));
	}

	function client_report_visitor( $id = false ) {

		$property = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
				'Property.client_id' => $this->user_id,
			),
		), array(
			'status' => 'active-pending-sold',
			'company' => false,
		));

		if( !empty($property) ) {

			$property = $this->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertySold',
					'User',
				),
			));

			$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
			$export = $this->RmCommon->filterEmptyField($params, 'named', 'export');

			$params = $this->RmCommon->defaultSearch($params, array(
				'date_from' => date('d-m-Y', strtotime('today - 30 days')),
				'date_to' => date('d-m-Y'),
			));
			$paramsModel = $this->RmCommon->dataConverter($params, array(
				'date' => array(
					'named' => array(
						'date_from',
						'date_to',
					),
				),
			));

			$options =  $this->Property->PropertyView->_callRefineParams($paramsModel, array(
				'limit' => Configure::read('__Site.config_admin_pagination'),
				'conditions' => array(
					'Property.id' => $id,
					'Property.client_id' => $this->user_id,
				),
				'order' => array(
					'PropertyView.created' => 'DESC',
				),
			), 'PropertyView');

			$elements = array(
				'status' => 'active-pending-sold',
				'company' => false,
			);

			$this->RmCommon->_callRefineParams($params);
			$propertyOptions = $this->Property->getData('paginate', $options, $elements);

			$propertyOptions['contain'][] = 'Property';
			$propertyOptions = $this->RmProperty->_callSearchDefaultBind($propertyOptions, 'PropertyView');

			if( $export == 'excel' ) {
				unset($propertyOptions['limit']);
				$values = $this->Property->PropertyView->getData('all', $propertyOptions);
			} else {
				$this->paginate = $propertyOptions;
				$values = $this->paginate('PropertyView');
			}

			if( !empty($values) ) {
				foreach ($values as $key => $value) {
					$user_id = $this->RmCommon->filterEmptyField($value, 'PropertyView', 'user_id');
					$value = $this->User->getMerge($value, $user_id);
					if( isset($value['User']) ) {
						$value = $this->User->UserProfile->getMerge($value, $user_id);
					}
					$values[$key] = $value;
				}
			}

			$this->RmCommon->_layout_file(array(
				'gchart',
			));
			$chartProperties = $this->Property->_callChartProperties( $id, 'visitors', $paramsModel['named']['date_from'], $paramsModel['named']['date_to'], $propertyOptions );

			$this->set('module_title', __('Laporan Pengunjung Properti'));
			$this->set('active_menu', 'properti');
			$this->set('action_type', 'visitors');
			$this->set(compact(
				'chartProperties', 'values', 'property', 'id'
			));

			if( $export == 'excel' ) {
				$date_from = $this->RmCommon->filterEmptyField($paramsModel, 'named', 'date_from');
				$date_to = $this->RmCommon->filterEmptyField($paramsModel, 'named', 'date_to');
				$periods = $this->RmCommon->getCombineDate($date_from, $date_to);
				$this->set('period_title', $periods);

				$this->layout = false;
				$this->render('client_report_visitor_excel');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	function client_report_lead( $id = false ) {

		$property = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
				'Property.client_id' => $this->user_id,
			),
		), array(
			'status' => 'all',
			'skip_is_sales' => true,
		));

		if( !empty($property) ) {

			$property = $this->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertySold',
					'User',
				),
			));

			$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
			$export = $this->RmCommon->filterEmptyField($params, 'named', 'export');

			$params = $this->RmCommon->defaultSearch($params, array(
				'date_from' => date('d-m-Y', strtotime('today - 30 days')),
				'date_to' => date('d-m-Y'),
			));
			$paramsModel = $this->RmCommon->dataConverter($params, array(
				'date' => array(
					'named' => array(
						'date_from',
						'date_to',
					),
				),
			));

			$options =  $this->Property->PropertyLead->_callRefineParams($paramsModel, array(
				'limit' => Configure::read('__Site.config_admin_pagination'),
				'conditions' => array(
					'Property.id' => $id,
					'Property.client_id' => $this->user_id,
				),
				'order' => array(
					'PropertyLead.created' => 'DESC',
				),
			), 'PropertyLead');
			$elements = array(
				'status' => 'all',
				'skip_is_sales' => true,
			);

			$this->RmCommon->_callRefineParams($params);

			$propertyOptions = $this->Property->getData('paginate', $options, $elements);
			$propertyOptions['contain'][] = 'Property';
			$propertyOptions = $this->RmProperty->_callSearchDefaultBind($propertyOptions, 'PropertyLead');

			if( $export == 'excel' ) {
				unset($propertyOptions['limit']);
				$values = $this->Property->PropertyLead->getData('all', $propertyOptions);
			} else {
				$this->paginate = $propertyOptions;
				$values = $this->paginate('PropertyLead');
			}

			if( !empty($values) ) {
				foreach ($values as $key => $value) {
					$user_id = $this->RmCommon->filterEmptyField($value, 'PropertyLead', 'user_id');
					$value = $this->User->getMerge($value, $user_id);
					if( isset($value['User']) ) {
						$value = $this->User->UserProfile->getMerge($value, $user_id);
					}
					$values[$key] = $value;
				}
			}

			$this->RmCommon->_layout_file(array(
				'gchart',
			));
			$chartProperties = $this->Property->_callChartProperties( $id, 'lead', $paramsModel['named']['date_from'], $paramsModel['named']['date_to'], $propertyOptions );

			$this->set('module_title', __('Laporan Lead Properti'));
			$this->set('active_menu', 'properti');
			$this->set('action_type', 'lead');
			$this->set(compact(
				'chartProperties', 'values', 'property', 'id'
			));

			if( $export == 'excel' ) {
				$date_from = $this->RmCommon->filterEmptyField($paramsModel, 'named', 'date_from');
				$date_to = $this->RmCommon->filterEmptyField($paramsModel, 'named', 'date_to');
				$periods = $this->RmCommon->getCombineDate($date_from, $date_to);
				$this->set('period_title', $periods);

				$this->layout = false;
				$this->render('client_report_lead_excel');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	function client_report_hotlead( $id = false ) {

		$property = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
				'Property.client_id' => $this->user_id,
			),
		), array(
			'status' => 'all',
			'skip_is_sales' => true,
		));

		if( !empty($property) ) {

			$property = $this->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertySold',
					'User',
				),
			));

			$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
			$export = $this->RmCommon->filterEmptyField($params, 'named', 'export');

			$params = $this->RmCommon->defaultSearch($params, array(
				'date_from' => date('d-m-Y', strtotime('today - 30 days')),
				'date_to' => date('d-m-Y'),
			));
			$paramsModel = $this->RmCommon->dataConverter($params, array(
				'date' => array(
					'named' => array(
						'date_from',
						'date_to',
					),
				),
			));

			$options =  $this->Property->Message->_callRefineParams($paramsModel, array(
				'limit' => Configure::read('__Site.config_admin_pagination'),
				'conditions' => array(
					'Property.id' => $id,
					'Property.client_id' => $this->user_id,
				),
				'order' => array(
					'Message.created' => 'DESC',
				),
			), 'Message');
			$elements = array(
				'status' => 'all',
				'skip_is_sales' => true,
			);

			$this->RmCommon->_callRefineParams($params);
			$propertyOptions = $this->Property->getData('paginate', $options, $elements);
			$propertyOptions['contain'][] = 'Property';

			if( $export == 'excel' ) {
				unset($propertyOptions['limit']);
				$values = $this->Property->Message->getData('all', $propertyOptions);
			} else {
				$this->paginate = $propertyOptions;
				$values = $this->paginate('Message');
			}

			$this->RmCommon->_layout_file(array(
				'gchart',
			));
			$chartProperties = $this->Property->_callChartProperties( $id, 'hotlead', $paramsModel['named']['date_from'], $paramsModel['named']['date_to'], $propertyOptions );

			$this->set('module_title', __('Laporan Hotlead Properti'));
			$this->set('active_menu', 'properti');
			$this->set('action_type', 'hotlead');
			$this->set(compact(
				'chartProperties', 'values', 'property', 'id'
			));

			if( $export == 'excel' ) {
				$date_from = $this->RmCommon->filterEmptyField($paramsModel, 'named', 'date_from');
				$date_to = $this->RmCommon->filterEmptyField($paramsModel, 'named', 'date_to');
				$periods = $this->RmCommon->getCombineDate($date_from, $date_to);
				$this->set('period_title', $periods);

				$this->layout = false;
				$this->render('client_report_hotlead_excel');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	function client_solds(){
		$module_title = __('Dijual / Disewa');
		$tabs_action_type = array(
			'controller' => 'properties',
			'action' => 'client_solds',
			'client' => true,
		);

		$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
		$export = $this->RmCommon->filterEmptyField($params, 'named', 'export');

		$options =  $this->Property->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_admin_pagination'),
			'conditions' => array(
				'PropertySold.client_id' => $this->user_id,
				'PropertySold.status' => 1,
			)
		));

		$elements = $this->RmCommon->_callRefineParams($this->params);
		$elements['status'] = $this->RmCommon->filterEmptyField($elements, 'status', false, 'sold');

		$this->RmCommon->_callRefineParams($this->params);
		$propertyOptions = $this->Property->getData('paginate', $options, $elements);
		$propertyOptions['contain'][] = 'Property';

		$this->Property->PropertySold->bindModel(array(
			'belongsTo' => array(
				'Currency' => array(
					'foreignKey' => false,
					'conditions' => array(
						'Currency.id = Property.currency_id',
					),
				),
			)
		), false);

		if( in_array('PropertySold', $propertyOptions['contain']) ) {
			$key = array_search('PropertySold', $propertyOptions['contain']);
			unset($propertyOptions['contain'][$key]);
		}

		if( $export == 'excel' ) {
			$properties = $this->Property->PropertySold->getData('all', $options);
		} else {
			$this->loadModel('PropertySold');
			$this->paginate = $propertyOptions;
			$properties = $this->paginate('PropertySold');
		}

		$properties = $this->Property->getDataList($properties, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'PropertyNotification',
				'PropertyMediasCount',
				'User',
			),
		));

		$this->set('active_menu', 'properti');
		$this->set(compact(
			'module_title', 'title_for_layout',
			'properties', 'tabs_action_type'
		));
	}

	function getAllMedias($property_id, $status = 'all'){
		$property_medias['PropertyMedias'] = $this->User->Property->PropertyMedias->getData('all', array(
			'conditions' => array(
				'PropertyMedias.property_id' => $property_id
			)
		), array(
			'status' => $status
		));

		$property_videos['PropertyVideos'] = $this->User->Property->PropertyVideos->getData('all', array(
			'conditions' => array(
				'PropertyVideos.property_id' => $property_id
			)
		), array(
			'status' => $status
		));

		return array_merge($property_medias, $property_videos);
	}

	function shorturl( $mls_id = false ) {
		$property = $this->Property->getData('first', array(
			'conditions' => array(
				'LOWER(Property.mls_id)' => strtolower($mls_id),
			),
		), array(
			'status' => 'active-pending-sold',
			'company' => true,
			'skip_is_sales' => true,
		));

		if ( !empty($property) ) {
			$property = $this->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
				),
			));
			$slug = $this->RmProperty->getNameCustom($property, false, true);

			$this->redirect(array(
				'controller' => 'properties',
				'action' => 'detail',
				'mlsid' => $mls_id,
				'slug' => $slug,
				'admin' => false,
			), array(
				'status' => 301
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_activate( $id = false ) {
		$value = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
			),
		), array(
			'admin_mine' => true,
			'status' => 'inactive',
		));

		if( !empty($value) ){
			$result = $this->Property->doActivate($id, $value);
			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_api_photo( $id = false ) {
        $isAdmin = Configure::read('User.admin');

		$result = array(
			'msg' => __('Gagal melakukan upload foto'),
			'status' => 'error'
		);

		$files = $this->RmCommon->filterEmptyField($this->request->data, 'Files');
		$session_id = $this->RmCommon->filterEmptyField($this->request->data, 'PropertyMedias', 'session_id', String::uuid());

		if( !empty($files) ) {
			if( !empty($id) ) {
				$property = $this->User->Property->getData('first', array(
		        	'conditions' => array(
		        		'Property.id' => $id,
		    		),
		    	), array(
		    		'status' => 'all',
		    		'admin_mine' => true,
		    	));

		    	$active = $this->RmCommon->filterEmptyField($property, 'Property', 'active');
			}else{
				$property = array();
			}

			if( !empty($property) ){
				$session_id = $this->RmCommon->filterEmptyField($property, 'Property', 'session_id');

				$primary_photo = $this->User->Property->PropertyMedias->getData('first', array(
	                'conditions' => array(
	                    'PropertyMedias.property_id' => $id,
	                )
	            ), array(
	                'status' => 'primary'
	            ));
			}

			$dataProperty = array(
				'session_id' => $session_id,
				'property_id' => $id,
			);

			$info = array();
			$propertyFolder = Configure::read('__Site.property_photo_folder');

			$is_error_upload = 0;
			foreach ($files as $key => $value) {
				$prefixImage = String::uuid();
				$file_name = $this->RmCommon->filterEmptyField($value, 'name');
				$category_media_id = $this->RmCommon->filterEmptyField($value, 'category_media_id');
				$title = $this->RmCommon->filterEmptyField($value, 'title');

				$data = $this->RmImage->upload($value, $propertyFolder, $prefixImage);
				$photo_name = $this->RmCommon->filterEmptyField($data, 'imageName');

				$date = date('Y-m-d H:i:s');

				$data = array_merge($data, array(
					'PropertyMedias' => array_merge(array(
						'alias' 			=> $file_name,
						'name' 				=> $photo_name,
						'category_media_id' => $category_media_id,
						'title' 			=> $title,
						'status'			=> 1,
						'primary'			=> 0,
						'approved'			=> 0,
						'modified' 			=> $date,
						'created' 			=> $date,
					), $dataProperty),
				));

				$file = $this->User->Property->PropertyMedias->doSave($data, $session_id);
				$file_id = $this->RmCommon->filterEmptyField($file, 'PropertyMedias', 'id');
				$thumbnail_url = $this->RmCommon->filterEmptyField($file, 'PropertyMedias', 'thumbnail_url');

				$data['PropertyMedias']['thumbnail_url'] = $thumbnail_url;
				$data['PropertyMedias']['id'] = $file_id;

				if( !empty($property) && empty($primary_photo) ) {
                    $this->User->Property->PropertyMedias->doRePrimary($id, $property);
					$file['data']['primary'] = 1;
				}

				if( !empty($file_id) ) {
					$log_msg = __('Berhasil unggah foto properti');
					$error = false;
				} else {
					$log_msg = __('Gagal unggah foto properti');
					$error = true;

					$is_error_upload++;
				}

				if(isset($file['status']) && $file['status'] == 'success'){
					$file['status'] = 1;
				}else{
					$file['status'] = 0;
				}

				if($this->Rest->isActive() && is_bool($id) && $id == false){
					$data['PropertyMedias']['property_id'] = null;
				}

            	$this->RmCommon->_saveLog(__('%s #%s', $log_msg, $file_id), $data, $id, $error);

            	$data['PropertyMedias']['category_media_id'] = $this->RmCommon->filterEmptyField($value, 'category_media_id', false, null);
				$data['PropertyMedias']['title'] = $this->RmCommon->filterEmptyField($value, 'title', false, null);

            	$medias_data['PropertyMedias'] = $this->RmCommon->filterEmptyField($data, 'PropertyMedias');
            	$info[] = array_merge($file, $medias_data);
			}

  			if(!empty($id) && empty($isAdmin) && !empty($active) ){
  				$this->User->Property->inUpdateChange($id);
	        }

	        if(empty($is_error_upload)){
	        	$result = array(
		        	'msg' => __('Berhasil mengunggah foto.'),
		        	'status' => 'success'
		        );
	        }else{
	        	if(count($info) == $is_error_upload){
	        		$msg_error = __('Gagal mengunggah foto.');
	        	}else{
	        		$msg_error = __('Berhasil upload tapi ada Sebagian foto tidak berhasil diunggah.');
	        	}

	        	$result = array(
		        	'msg' => $msg_error,
		        	'status' => 'error'
		        );
	        }

	        $data = $info;

	        $this->set(compact('result', 'data', 'session_id'));
		}

		$this->RmCommon->setProcessParams($result);
	}

	public function admin_api_videos($id = false){
		$isAdmin = Configure::read('User.admin');

		$property 	= $this->_callDataProperty($id);

		$session_id = $this->RmCommon->filterEmptyField($property, 'Property', 'session_id', String::uuid());
		$active 	= $this->RmCommon->filterEmptyField($property, 'Property', 'active');

		$data = $this->RmProperty->_callValidateVideo( $this->request->data, $id, $session_id );
		$result = $this->Property->PropertyVideos->doSaveVideo( $data, $id );
		$status = $this->RmCommon->filterEmptyField($result, 'status');

		if( $status == 'success' ){
			if(empty($isAdmin) && !empty($active)){
				$r = $this->User->Property->inUpdateChange($id);
			}
		}

		$this->RmCommon->setProcessParams($result, false);

		$dataVideos = $this->Property->PropertyVideos->getData('all', array(
			'conditions' => array(
				'PropertyVideos.property_id' => $id,
				'PropertyVideos.session_id' => $session_id
			),
		), array(
			'status' => 'all'
		));

		$this->set(compact(
			'property',
			'urlBack',
			'dataVideos', 'id', 'session_id'
		));

		$this->RmCommon->renderRest();
	}

	public function admin_api_document_upload( $id = false ) {
    	$files = $this->RmCommon->filterEmptyField($this->request->data, 'Files', 'data');
    	$document_category_id = $this->RmCommon->filterEmptyField($this->request->data, 'CrmProjectDocument', 'document_category_id');
    	$title = $this->RmCommon->filterEmptyField($this->request->data, 'CrmProjectDocument', 'title');

		$session_id = $this->RmCommon->filterEmptyField($this->request->data, 'session_id', false, String::uuid());

		$result = array(
			'msg' => __('Gagal melakukan upload dokumen'),
			'status' => 'error'
		);

		if( !empty($files) ) {
			$temp[] = $files;
			$files = $temp;
			
			$info = array();
			$saveFolder = Configure::read('__Site.document_folder');

			Configure::write('__Site.allowed_ext', array('jpg', 'jpeg', 'png', 'gif', 'pdf', 'xls', 'xlsx'));

			$error = 0;
			foreach ($files as $key => $val) {
				$prefixImage = String::uuid();
				$file_name = $this->RmCommon->filterEmptyField($val, 'name');

				$data = $this->RmImage->upload($val, $saveFolder, $prefixImage, array(
					'fullsize' => true,
				));

				$photo_name = $this->RmCommon->filterEmptyField($data, 'imageName');

				$data = array_merge($data, array(
					'CrmProjectDocument' => array(
						'owner_id' => !empty($id)?$id:0,
						'session_id' => $session_id,
						'document_category_id' => $document_category_id,
						'save_path' => $saveFolder,
						'name' => $file_name,
						'file' => $photo_name,
						'title' => $title,
						'document_type' => 'property'
					),
				));

				$file = $this->User->CrmProject->CrmProjectDocument->doSave($data);

				if(!empty($file['status']) && $file['status'] == 'error'){
					$error++;
				}

				$data['CrmProjectDocument']['thumbnail_url'] = $this->RmCommon->filterEmptyField($file, 'CrmProjectDocument', 'thumbnail_url');
				$data['CrmProjectDocument']['id'] = $this->RmCommon->filterEmptyField($file, 'CrmProjectDocument', 'id');

				$document_data['CrmProjectDocument'] = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument');

				$info[] = array_merge($file, $document_data);
			}

			$data = $info;

			if(!empty($error)){
				$result = array(
					'msg' => __('Gagal melakukan upload dokumen'),
					'status' => 'error'
				);
			}else{
				$result = array(
					'msg' => __('Berhasil melakukan upload dokumen'),
					'status' => 'success'
				);
			}

			$this->set(compact('data'));
		}

		$this->RmCommon->setProcessParams($result);
	}

	function admin_api_detail($id){
		$data = $this->_callDataProperty($id);

		if(!empty($data)){
			App::uses('PropertyHelper', 'View/Helper');
	        $PropertyHelper = new PropertyHelper(new View());

			$data = $this->Property->getDataList($data, array(
				'contain' => array(
					'PropertySold',
					'PropertyFacility',
					'PropertyPointPlus',
					'PropertyPrice',
				),
			));

			$data['Property']['price_alias'] = $PropertyHelper->getPrice($data);

			$data = $this->Property->PageConfig->getMerge($data, $id, 'property');

			$active = $this->RmCommon->filterEmptyField($data, 'Property', 'active');

			if( !empty($active) ) {
				$statusMedia = 'active';
			} else {
				$statusMedia = 'all';
			}

			$data = $this->Property->PropertyMedias->getMerge($data, $id, 'all', $statusMedia);
			$data = $this->Property->PropertyVideos->getMerge($data, $id, 'all', $statusMedia);

			$documents = $this->User->CrmProject->CrmProjectDocument->getData('all', array(
				'conditions' => array(
					'CrmProjectDocument.document_type' => 'property',
					'CrmProjectDocument.owner_id' => $id,
				),
				'limit' => 10,
			), array(
				'company' => true,
			));

			$documents = $this->User->CrmProject->CrmProjectDocument->getDataList($documents);

			$this->set(compact('data', 'documents'));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function admin_info($recordID = NULL){
		$title = __('Daftar Properti');
		$user = $this->RmUser->getUser($recordID);

		if( !empty($user) ) {
			$groupName = Common::hashEmptyField($user, 'Group.name', false, array(
				'type' => 'strtolower',
			));
			$groupId = Common::hashEmptyField($user, 'Group.id');

			// $this->RmUser->_callRoleActiveMenu($user);
			$active_menu = $this->RmUser->getActive($groupName, 'user');

			$values = array();
			$options = $this->RmProperty->_callRoleCondition($user);

			if(!empty($options)){
				$options['conditions']['Property.principle_id'] = Common::_getHeadLinerID($groupId, $user);
				$values = $this->RmProperty->_callBeforeViewProperties($options, array(
					'company' => false,
				));
			}


			$this->set(array(
				'module_title' => $title,
				'title_for_layout' => $title,
				'properties' => $values,
				'currUser' => $user,
				'recordID' => $recordID,
				'active_tab' => 'Properti',
				'urlBack' => array(
					'controller' => 'users',
					'action' => 'directors',
					'admin' => true,
				),
				'active_menu' => $active_menu,
				'getCookieId' => $this->RmCommon->getCookieUser(),
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	function admin_deactivate($id){
		$value = $this->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
			),
		), array(
			'admin_mine' => true,
			'status' => 'all',
		));

		if( !empty($value) ){
			$result = $this->Property->doActivate($id, $value, false);
			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}

		$this->RmCommon->renderRest();
	}

	function admin_api_find_properties(){
		$result = array(
			'msg' => '',
			'status' => 'success'
		);

		$data = $this->request->data;
		$elements = array(
			'status' => $this->RmCommon->filterEmptyField($this->params->params, 'named', 'status', 'all'),
			'admin_mine' => true
		);
	
		$params['named'] = $this->RmCommon->getSearchParamsApi();

		if(!empty($elements['status']) && $elements['status'] != 'all'){
			$params['named']['status'] = $elements['status'];
		}

		$options	= $this->Property->_callRefineParams($params, array(
			'limit' => Configure::read('__Site.config_admin_pagination'),
		));

		$elements['restrict_api'] = false;
		$properties = $this->RmProperty->_callBeforeViewProperties($options, $elements);

		// $this->paginate	= $this->Property->getData('paginate', $options, $elements);
		// $properties = $this->paginate('Property');

		if(!empty($properties)){
			App::uses('PropertyHelper', 'View/Helper');
	        $PropertyHelper = new PropertyHelper(new View());

			foreach ($properties as $key => $value) {
				$property =& $properties[$key];

				$property['Property']['price_alias'] = $PropertyHelper->getPrice($value, __('(Harga belum ditentukan)'));
			}

			// $properties = $this->Property->getDataList($properties, array(
	  //           'contain' => array(
		 //            'MergeDefault',
		 //            'PropertyAddress',
		 //            'PropertyAsset',
		 //            'PropertySold',
		 //            'PropertyNotification',
		 //            'User',
		 //            'Approved',
		 //            'Client'
		 //        ),
	  //       ));

	  //       if(!empty($properties)){
	  //       	App::uses('PropertyHelper', 'View/Helper');
	            
	  //           $PropertyHelper = new PropertyHelper(new View());
	  //           foreach ($properties as $key => $value) {
	  //               if($this->Rest->isActive()){
	  //                   $user_full_name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');
	  //                   $user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
	                    
	  //                   $change_date = $this->RmCommon->filterEmptyField($value, 'Property', 'change_date');
	  //                   if(!empty($change_date)){
	  //                       $change_date = date ("Y-m-d", strtotime("+".Configure::read('__Site.config_expired_listing_in_year')." Year", strtotime($change_date)));
	  //                   }

	  //                   $properties[$key]['Property']['expired_date'] = $change_date;
	                    
	  //                   if(empty($user_full_name)){
	  //                       $data_user = $this->User->getData('first', array(
	  //                           'conditions' => array(
	  //                               'User.id' => $user_id
	  //                           )
	  //                       ));
	                        
	  //                       $properties[$key]['User'] = $this->RmCommon->filterEmptyField($data_user, 'User');
	  //                   }

	  //                   $properties[$key]['Property']['specifications'] = $PropertyHelper->getSpec($value, array(), false, false);
	  //               }

	  //               if(!empty($value['PropertySold'])){
	  //                   $period_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'period_id');
	  //                   $currency_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'currency_id');

	  //                   $value['PropertySold'] = $this->User->Property->Period->getMerge($value['PropertySold'], $period_id);
	  //                   $value['PropertySold'] = $this->User->Property->Currency->getMerge($value['PropertySold'], $currency_id);

	  //                   $properties[$key]['PropertySold'] = $value['PropertySold'];
	  //               }
	  //           }
	  //       }
		}else{
			$result['msg'] = __('Data tidak ditemukan');
		}

		$refresh_all = $this->Property->_callAllowRefreshAll();
		
		$this->set(compact('properties', 'refresh_all'));

		$this->RmCommon->renderRest(array(
			'is_paging' => true,
			'params' => $params['named']
		));
	}

	public function admin_status_listing_categories() {
		$module_title = __('Daftar Kategori Properti');
		$this->loadModel('PropertyStatusListing');

        $options =  $this->PropertyStatusListing->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));

		$this->RmCommon->_callRefineParams($this->params);

		$authGroupID	= $this->Auth->user('group_id');
		$elements		= array();

		if($authGroupID == 1){
			$elements = array('company' => false, 'mine' => true);
		}

        $this->paginate = $this->PropertyStatusListing->getData('paginate', $options, $elements);
		$values = $this->paginate('PropertyStatusListing');

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'active_menu' => 'property_category',
		));
	}

	public function admin_add_status_listing_category() {
    	$module_title = __('Tambah Kategori Properti');
    	$urlRedirect = array(
            'controller' => 'properties',
            'action' => 'status_listing_categories',
            'admin' => true
        );

    	$user_id = $this->user_id;
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id
			),
		));

		if( !empty($user) ) {
			
			$this->loadModel('PropertyStatusListing');

			// $data_category = 
			$data = $this->request->data;
			$result = $this->PropertyStatusListing->doSave( $data );
			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->RmCommon->_layout_file('color-picker');
			$this->set(array(
				'module_title' => $module_title,
				'active_menu' => 'property_category',
			));
			$this->render('status_listing_category_form');
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_edit_status_listing_category( $status_listing_category_id ) {
        
        $module_title = __('Edit Kategori Properti');
        $urlRedirect = array(
            'controller' => 'properties',
            'action' => 'status_listing_categories',
            'admin' => true
        );

        $this->loadModel('PropertyStatusListing');
        $status_category = $this->PropertyStatusListing->getData('first', array(
        	'conditions' => array(
				'PropertyStatusListing.id' => $status_listing_category_id,
			),
		));

		if( !empty($status_category) ) {
			$data = $this->request->data;
			$result = $this->PropertyStatusListing->doSave( $data, $status_category, $status_listing_category_id );
			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->RmCommon->_layout_file('color-picker');
			$this->set(array(
				'module_title' => $module_title,
				'active_menu' => 'property_category',
			));
			$this->render('status_listing_category_form');
		} else {
			$this->RmCommon->redirectReferer(__('Status Kategori tidak ditemukan'));
		}
    }

    public function admin_status_listing( $id = false ) {
		$user_id = $this->user_id;
		if(Configure::read('User.admin')){
			$user_id = false;
		}

		$property = $this->Property->getProperty('first', $user_id, $id, 'all');

		if( !empty($property) ) {
			$property = $this->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertyMediasCount',
					'User',
					'PropertyStatusListing',
				),
			));

			$property_action_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_action_id');
			$user_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id');

			$property = $this->Property->PropertyAction->getMerge($property, $property_action_id, 'PropertyAction.id', array(
				'cache' => array(
					'name' => __('PropertyAction.%s', $property_action_id),
				),
			));
			$property = $this->User->getMerge($property, $user_id);

			if( !empty($this->request->data) ) {
				$data = $this->request->data;

				$result = $this->Property->doChangeStatusCategory( $data, $id, array(
					'validate_status_category' => true
				));
				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
					'ajaxRedirect' => false,
					'noRedirect' => true
				));
			} else {
				$this->request->data['Property']['property_status_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'property_status_id');
			}

			$this->set('category_status', $this->RmCommon->getGlobalVariable('category_status'));

			$this->set(compact(
				'property'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

    public function admin_delete_multiple_status_listing_category() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'PropertyStatusListing', 'id');

		$this->loadModel('PropertyStatusListing');
    	$result = $this->PropertyStatusListing->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    public function admin_remove_status_category( $id ) {
    	$result = $this->Property->doRemoveCategory( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

	public function admin_market_trend(){
		$this->market_trend();
	}

    public function market_trend(){
		$this->loadModel('ViewUnionPropertySubarea');

		$dataCompany		= isset($this->data_company) ? $this->data_company : array();
		$companyID			= Hash::get($dataCompany, 'UserCompany.id', 0);

		$propertyTypes		= $this->RmMarketTrend->getCompanyPropertyType();
		$propertyFilters	= $this->RmMarketTrend->getPropertyFilter($companyID);
		$propertyFilters	= $this->RmMarketTrend->parsePropertyFilter($propertyFilters);

		$location = $this->RmUser->getLocation($this->params->named, array(
			'use_default'	=> true, 
			'market_trend'	=> true, 
		));

		$this->request->data['Search']['region']	= $regionID		= Hash::get($location, 'Region.id');
		$this->request->data['Search']['city']		= $cityID		= Hash::get($location, 'City.id');
		$this->request->data['Search']['subarea']	= $subareaID	= Hash::get($location, 'Subarea.id');

		$this->RmCommon->_callRequestSubarea('Search');

		$period			= Hash::get($this->params->named, 'period', 6);
		$periodTo		= date('Y-m-d');
		$periodFrom		= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $periodTo, $period - 1)));

		$lastPeriodTo	= date('Y-m-t', strtotime(sprintf('%s - 1 MONTH', $periodFrom)));
		$lastPeriodFrom	= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $lastPeriodTo, $period - 1)));

	/*	coba speed
		$typeID	= Hash::combine($propertyTypes, '{n}.PropertyType.id', '{n}.PropertyType.id');
		$params	= array(
			'region_id'			=> $regionID, 
			'city_id'			=> $cityID, 
			'subarea_id'		=> $subareaID, 
			'property_type_id'	=> $typeID, 
		);

	//	append all data
		$lastSummary	= $this->RmMarketTrend->getSummary(array_merge($params, array('period_from' => $lastPeriodFrom, 'period_to' => $lastPeriodTo)));
		$currentSummary	= $this->RmMarketTrend->getSummary(array_merge($params, array('period_from' => $periodFrom, 'period_to' => $periodTo)));
		$summaries		= array(
			'period'			=> $period, 
			'period_from'		=> $periodFrom, 
			'period_to'			=> $periodTo, 
			'last_period'		=> $lastSummary, 
			'current_period'	=> $currentSummary, 
		);

	//	B:PARENT LOCATION SUMMARY ================================================================================================================
	//	rule grab data untuk parent location : 
	//		- jika pencarian sampai level subarea, yang jadi  parent adalah city
	//		- jika pencarian sampai level city, yang jadi parent adalah region (cancelled, hanya lakukan jika pencarian sampe subarea)

		if($subareaID){
			$params	= Hash::remove($params, 'subarea_id');

			$lastSummary	= $this->RmMarketTrend->getSummary(array_merge($params, array('period_from' => $lastPeriodFrom, 'period_to' => $lastPeriodTo)));
			$currentSummary	= $this->RmMarketTrend->getSummary(array_merge($params, array('period_from' => $periodFrom, 'period_to' => $periodTo)));

			$summaries = Hash::insert($summaries, 'parent_last_period', $lastSummary);
			$summaries = Hash::insert($summaries, 'parent_current_period', $currentSummary);
		}

	//	debug($summaries);exit;

	//	E:PARENT LOCATION SUMMARY ================================================================================================================
	*/

		$active_menu	= 'market_trend';
		$summaries		= array(
			'period'		=> $period, 
			'period_from'	=> $periodFrom, 
			'period_to'		=> $periodTo, 
		);

		$this->set(compact(
			'summaries', 
			'location', 
			'propertyTypes', 
			'propertyFilters',
			'active_menu'
		));

		if($this->params->prefix == 'admin'){
			$this->RmCommon->_layout_file('admin_market_trend');
			$this->set(array(
				'module_title' => __('Market Trend'), 
			));
		}
		else{
			$this->layout = 'market_trend';
		}
	}

	public function property_statistic($actionID = false, $chartType = false){
		$data			= $this->request->data;
		$namedParams	= $this->params->named;

		$actionID	= Hash::get($data, 'Chart.property_action_id', $actionID);
		$typeID		= Hash::get($data, 'Chart.property_type_id', array());
		$typeSlug	= Hash::get($data, 'Chart.property_type');
		$regionID	= Hash::get($data, 'Chart.region_id');
		$cityID		= Hash::get($data, 'Chart.city_id');
		$subareaID	= Hash::get($data, 'Chart.subarea_id');
		$period		= Hash::get($data, 'Chart.period', 6);

		if(empty($typeID) && empty($typeSlug)){
			$propertyTypes	= $this->RmMarketTrend->getCompanyPropertyType();
			$typeID			= Hash::combine($propertyTypes, '{n}.PropertyType.id', '{n}.PropertyType.id');
			$typeSlug		= Hash::combine($propertyTypes, '{n}.PropertyType.id', '{n}.PropertyType.slug');
		}

		if(empty($regionID) && empty($cityID) && empty($subareaID)){
			$location = $this->RmUser->getLocation($namedParams, array(
				'use_default'	=> true, 
				'market_trend'	=> true, 
			));

			$regionID	= Hash::get($location, 'Region.id');
			$cityID		= Hash::get($location, 'City.id');
			$subareaID	= Hash::get($location, 'Subarea.id');
		}

		$period			= Hash::get($namedParams, 'period', $period);
		$periodTo		= date('Y-m-d');
		$periodFrom		= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $periodTo, $period - 1))); // - 1 karna bulan berjalan juga diitung

		$lastPeriodTo	= date('Y-m-t', strtotime(sprintf('%s - 1 MONTH', $periodFrom)));
		$lastPeriodFrom	= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $lastPeriodTo, $period - 1)));

		$params = array(
			'property_action_id'	=> $actionID, 
			'property_type_id'		=> $typeID, 
			'period'				=> $period, 
			'period_from'			=> $periodFrom, 
			'period_to'				=> $periodTo, 
			'region_id'				=> $regionID, 
			'city_id'				=> $cityID, 
			'subarea_id'			=> $subareaID, 
			'is_sold'				=> 1, 
		);

	//	current
		$currentPeriodStatistic	= $this->RmMarketTrend->getStatistic($params);
		$lastPeriodStatistic	= $this->RmMarketTrend->getStatistic(array_replace($params, array(
			'period_from'	=> $lastPeriodFrom, 
			'period_to'		=> $lastPeriodTo, 
		)));

	//	debug($params);
	//	debug($lastPeriodStatistic);
	//	debug($currentPeriodStatistic);
	//	exit;

		if($chartType == 'movement'){
		//	convert value chart (last vs current) ke bentuk persentase
			$movements				= $this->RmMarketTrend->getMovement($currentPeriodStatistic, $lastPeriodStatistic);
			$currentPeriodStatistic	= array_replace($currentPeriodStatistic, $movements);
		//	debug($currentPeriodStatistic);exit;
		}
	//	else{
		//	itung pergerakan (dari summary)
			$currentSummary	= Hash::get($currentPeriodStatistic, 'summary', array());
			$lastSummary	= Hash::get($lastPeriodStatistic, 'summary', array());
			$summaryFields	= array('avg_price_measure', 'avg_lot_price', 'min_price', 'max_price');

			foreach($summaryFields as $summaryField){
				$currentValues = Hash::get($currentSummary, $summaryField, array());

				if($currentValues){
					foreach($currentValues as $rowIndex => $currentValue){
						$lastValue = Hash::get($lastSummary, sprintf('%s.%s', $summaryField, $rowIndex), 0);

						if(empty($lastValue) && $currentValue || empty($currentValue) || $lastValue == $currentValue){
						//	STAGNAN
						//	empty $lastValue && $currentValue	===> data baru (ga bisa di bilang naik)
						//	empty $currentValue					===> data lama ada tapi data baru ga ada (ga ada pergerakan, bukan berarti turun)
						//	$lastValue == $currentValue			===> data lama sama dengan data baru

							$percentage	= 0;
						}
						else if($lastValue && $currentValue){
							$percentage	= abs($currentValue - $lastValue) / ($lastValue / 100);
							$percentage	= number_format($percentage, 2, '.', '');

							if($lastValue < $currentValue){
							//	INCREMENT

							}
							else{
							//	DECREMENT
								$percentage = $percentage * -1;
							}
						}

						$currentSummary[$summaryField.'_percentage'][$rowIndex] = $percentage;
					}
				}
			}

			$currentPeriodStatistic	= Hash::insert($currentPeriodStatistic, 'summary', $currentSummary);
	//	}

		$this->autoLayout = FALSE;
		$this->autoRender = FALSE;

		return json_encode($currentPeriodStatistic);
	}

	public function backprocess_area_stat(){
		$this->loadModel('ViewUnionPropertySubarea');

		$dataCompany		= isset($this->data_company) ? $this->data_company : array();
		$companyID			= Hash::get($dataCompany, 'UserCompany.id', 0);

		$propertyTypes		= $this->RmMarketTrend->getCompanyPropertyType();
		$propertyFilters	= $this->RmMarketTrend->getPropertyFilter($companyID);
		$propertyFilters	= $this->RmMarketTrend->parsePropertyFilter($propertyFilters);

		$location = $this->RmUser->getLocation($this->params->named, array(
			'use_default'	=> true, 
			'market_trend'	=> true, 
		));

		$period			= Hash::get($this->params->named, 'period', 6);
		$periodTo		= date('Y-m-d');
		$periodFrom		= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $periodTo, $period - 1)));

		$lastPeriodTo	= date('Y-m-t', strtotime(sprintf('%s - 1 MONTH', $periodFrom)));
		$lastPeriodFrom	= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $lastPeriodTo, $period - 1)));

		$regionID	= Hash::get($location, 'Region.id');
		$cityID		= Hash::get($location, 'City.id');
		$subareaID	= Hash::get($location, 'Subarea.id');

		$typeID	= Hash::combine($propertyTypes, '{n}.PropertyType.id', '{n}.PropertyType.id');
		$params	= array(
			'region_id'			=> $regionID, 
			'city_id'			=> $cityID, 
			'subarea_id'		=> $subareaID, 
			'property_type_id'	=> $typeID, 
		);

	//	append all data
		$lastSummary	= $this->RmMarketTrend->getSummary(array_merge($params, array('period_from' => $lastPeriodFrom, 'period_to' => $lastPeriodTo)));
		$currentSummary	= $this->RmMarketTrend->getSummary(array_merge($params, array('period_from' => $periodFrom, 'period_to' => $periodTo)));
		$summaries		= array(
			'period'			=> $period, 
			'period_from'		=> $periodFrom, 
			'period_to'			=> $periodTo, 
			'last_period'		=> $lastSummary, 
			'current_period'	=> $currentSummary, 
		);

		$render = '/Elements/blocks/market_trend/stat';

		if($this->params->action == 'backprocess_area_summary'){
			$render = '/Elements/blocks/market_trend/summary';

		//	B:PARENT LOCATION SUMMARY ================================================================================================================
		//	rule grab data untuk parent location : 
		//		- jika pencarian sampai level subarea, yang jadi  parent adalah city
		//		- jika pencarian sampai level city, yang jadi parent adalah region (cancelled, hanya lakukan jika pencarian sampe subarea)

			if($subareaID){
				$params	= Hash::remove($params, 'subarea_id');

				$lastSummary	= $this->RmMarketTrend->getSummary(array_merge($params, array('period_from' => $lastPeriodFrom, 'period_to' => $lastPeriodTo)));
				$currentSummary	= $this->RmMarketTrend->getSummary(array_merge($params, array('period_from' => $periodFrom, 'period_to' => $periodTo)));

				$summaries = Hash::insert($summaries, 'parent_last_period', $lastSummary);
				$summaries = Hash::insert($summaries, 'parent_current_period', $currentSummary);
			}

		//	E:PARENT LOCATION SUMMARY ================================================================================================================
		}

		$this->set(compact(
			'summaries', 
			'location', 
			'propertyTypes'
		));

		$this->autoLayout = false;
		$this->render($render);
	}

	public function backprocess_area_summary(){
		$this->backprocess_area_stat();
	}

	public function admin_generate_rank(){
		$parentID	= Configure::read('Principle.id');
		$result		= array(
			'status'	=> 'error', 
			'msg'		=> __('Data tidak valid'), 
		);

		$namedParams	= $this->params->named;
		$referer		= Common::hashEmptyField($namedParams, 'referer', 'agent_rank');
		$namedParams	= Hash::remove($namedParams, 'referer');

		if($parentID){
		//	update agent ranks by parent
			$periodYear		= Common::hashEmptyField($namedParams, 'period_year', date('Y'));
			$periodMonth	= Common::hashEmptyField($namedParams, 'period_month', date('m'));
			$periodMonth	= str_pad($periodMonth, 2, '0', STR_PAD_LEFT);

			$result	= $this->User->updateUserRank($parentID, null, array(
				'period_date' => sprintf('%s-%s-01', $periodYear, $periodMonth), 
			));
		}

		$this->RmCommon->setProcessParams($result, array_replace_recursive(array(
			'plugin'	=> false, 
			'admin'		=> true, 
			'action'	=> $referer, 
		), $namedParams), array(
			'redirectError'	=> true,
		));
	}

	public function admin_agent_rank(){
		$namedParams	= $this->params->named;
		$typeID			= Common::hashEmptyField($namedParams, 'typeid');
		$allowedTypes	= array(1, 3, 7, 2);

		$typeID		= in_array($typeID, $allowedTypes) ? $typeID : 1;
		$parentID	= Configure::read('Principle.id');

		if($parentID){
			$currentDate	= strtotime(date('Y-m-d'));
			$periodYear		= Common::hashEmptyField($namedParams, 'period_year', date('Y', $currentDate));
			$periodMonth	= Common::hashEmptyField($namedParams, 'period_month', date('m', $currentDate));
			$periodMonth	= str_pad($periodMonth, 2, '0', STR_PAD_LEFT);
			$sort			= Common::hashEmptyField($namedParams, 'sort');
			$direction		= Common::hashEmptyField($namedParams, 'direction', 'asc');

			$this->User->AgentRank->virtualFields = array(
				'property_count'		=> 'sum(AgentRank.property_count)', 
				'sold_property_count'	=> 'sum(AgentRank.sold_property_count)', 
				'sell_property_count'	=> 'sum(case when AgentRank.property_action_id = 1 then AgentRank.property_count else 0 end)', 
				'rent_property_count'	=> 'sum(case when AgentRank.property_action_id = 2 then AgentRank.property_count else 0 end)', 
			//	'sell_price_measure'	=> 'sum(case when AgentRank.property_action_id = 1 then AgentRank.price_measure else 0 end)', 
			//	'rent_price_measure'	=> 'sum(case when AgentRank.property_action_id = 2 then AgentRank.price_measure else 0 end)', 
			);

			$agentRanks = $this->User->AgentRank->getData('all', array(
				'fields' => array(
					'AgentRank.parent_id', 
					'AgentRank.user_id', 
					'AgentRank.property_type_id', 
					'AgentRank.property_count', 
					'AgentRank.sold_property_count', 
					'AgentRank.sell_property_count', 
					'AgentRank.rent_property_count', 
					'AgentRank.period_year', 
					'AgentRank.period_month', 
				), 
				'conditions' => array(
					'AgentRank.parent_id'			=> $parentID, 
					'AgentRank.period_year'			=> $periodYear, 
					'AgentRank.period_month'		=> $periodMonth, 
					'AgentRank.property_type_id'	=> $typeID, 
				), 
				'order' => array(
					'AgentRank.period_year'			=> 'desc', 
					'AgentRank.period_month'		=> 'desc',  
					'AgentRank.sold_property_count'	=> 'desc', 
					'AgentRank.property_count'		=> 'desc', 
				), 
				'group' => array(
					'AgentRank.period_year', 
					'AgentRank.period_month', 
					'AgentRank.parent_id', 
					'AgentRank.user_id', 
					'AgentRank.property_type_id', 
				), 
			));

		//	order nya pake hash bukan pake query
		//	if($sort){
		//		$order = array($sort => $direction);
		//	}
		//	else{
				$agentID	= Hash::extract($agentRanks, '{n}.AgentRank.user_id');
				$order		= array();

				if($agentID){
				//	order user by rank
					$order = array(
						sprintf('FIELD(User.id, %s)', implode(', ', array_reverse($agentID))) => 'DESC', 
					);
				}

				$order = array_merge($order, array('User.full_name'	=> 'ASC'));
		//	}

		//	get user list
			$options = $this->User->_callRefineParams($this->params->params, array(
				'limit'			=> false, 
				'order'			=> $order, 
				'conditions'	=> array(
					'date_format(User.created, "%Y") <=' => $periodYear, 
					'date_format(User.created, "%m") <=' => $periodMonth, 
				), 
			));

			$records = $this->User->getData('all', $options, array(
				'company'	=> $parentID, 
				'status'	=> 'active', 
				'role'		=> 'agent', 
			));

			if($records){
			//	$agentRanks = Hash::combine($agentRanks, '{n}.AgentRank.user_id', '{n}');

				foreach($records as $key => &$record){
					$agentID	= Common::hashEmptyField($record, 'User.id');
				//	$agentRank	= Common::hashEmptyField($agentRanks, $agentID, array());
				//	$record		= array_merge($record, $agentRank);

					$agentRank	= Hash::extract($agentRanks, sprintf('{n}.AgentRank[user_id=%s]', $agentID));
					$agentRank	= $agentRank ? array_shift($agentRank) : array();

					$agentRank	= Hash::insert($agentRank, 'rank', $key + 1);
					$record		= Hash::insert($record, 'AgentRank', $agentRank);
				}
			}

			if($sort && $records){
				$records = Hash::sort($records, sprintf('{n}.%s', $sort), $direction);

			//	buat ngibulin paging
				$this->request->params['paging'] = array(
					'User' => array(
						'page'		=> 1, 
						'pageCount'	=> 1,
						'current'	=> count($records),
						'count'		=> count($records),
						'prevPage'	=> false,
						'nextPage'	=> false,
						'limit'		=> false,
						'paramType'	=> 'named', 
						'options'	=> array(
							'sort'		=> $sort, 
							'direction'	=> $direction, 
						), 
					), 
				);
			}

			$propertyTypes = $this->Property->PropertyType->getData('all', array(
				'order'			=> array(sprintf('field(PropertyType.id, %s)', implode(', ', $allowedTypes))), 
				'conditions'	=> array(
					'PropertyType.id' => $allowedTypes, 
				), 
			));

			$years	= array();
			$months	= Configure::read('__Site.monthly.named');

			if($months){
				krsort($months);
				$tempMonths = array();

				foreach($months as $key => $month){
					$tempMonths[$key + 1] = $month;
				}

				$months = $tempMonths;
			}

			$upperYear	= date('Y');
			$bottomYear = $upperYear - 50;

			for($year = $upperYear; $year > $bottomYear; $year--){
				$years[$year] = $year;
			}

			$this->request->data['Search'] = $namedParams;

			$this->set(array(
				'module_title'	=> __('Rank'), 
				'records'		=> $records, 
				'propertyTypes'	=> $propertyTypes, 
				'years'			=> $years, 
				'months'		=> $months, 
				'active_menu'	=> 'agent_rank',
			));
		}
		else{
			$this->RmCommon->redirectReferer(__('Hanya karyawan perusahaan yang boleh mengakses halaman ini'), 'error');
		}
	}

	public function admin_price_movement(){
		$namedParams	= $this->params->named;
		$actionID		= Common::hashEmptyField($namedParams, 'actionid');
		$typeID			= Common::hashEmptyField($namedParams, 'typeid');
		$allowedActions	= array(1, 2);
		$allowedTypes	= array(1, 3, 7, 2);

		$actionID	= in_array($actionID, $allowedActions) ? $actionID : 1;
		$typeID		= in_array($typeID, $allowedTypes) ? $typeID : 1;
		$parentID	= Configure::read('Principle.id');

		if($parentID){
			$currentDate	= strtotime(date('Y-m-d'));
			$periodYear		= Common::hashEmptyField($namedParams, 'period_year', date('Y', $currentDate));
			$periodMonth	= Common::hashEmptyField($namedParams, 'period_month', date('m', $currentDate));
			$periodMonth	= str_pad($periodMonth, 2, '0', STR_PAD_LEFT);
			$sort			= Common::hashEmptyField($namedParams, 'sort');
			$direction		= Common::hashEmptyField($namedParams, 'direction', 'asc');

			$this->loadModel('AgentRank');
			$this->AgentRank->virtualFields = array(
				'property_count'				=> 'sum(AgentRank.property_count)', 
				'price_measure'					=> 'sum(AgentRank.price_measure)', 
				'price_measure_min'				=> 'min(AgentRank.price_measure_min)', 
				'price_measure_max'				=> 'max(AgentRank.price_measure_max)', 
				'price_measure_average'			=> 'sum(AgentRank.price_measure) / sum(AgentRank.property_count)', 
				'sold_property_count'			=> 'sum(AgentRank.sold_property_count)', 
				'sold_price_measure'			=> 'sum(AgentRank.sold_price_measure)', 
				'sold_price_measure_min'		=> 'min(AgentRank.sold_price_measure_min)', 
				'sold_price_measure_max'		=> 'max(AgentRank.sold_price_measure_max)', 
				'sold_price_measure_average'	=> 'sum(AgentRank.sold_price_measure) / sum(AgentRank.sold_property_count)', 
			);

			$options = $this->AgentRank->_callRefineParams($this->params->params, array(
				'contain' => array(
					'Region', 
					'City', 
					'Subarea', 
				), 
				'fields' => array(
					'AgentRank.parent_id', 
					'AgentRank.property_type_id', 
					'AgentRank.property_count', 
					'AgentRank.sold_property_count', 
					'AgentRank.price_measure', 
					'AgentRank.price_measure_min', 
					'AgentRank.price_measure_max', 
					'AgentRank.price_measure_average', 
					'AgentRank.sold_price_measure', 
					'AgentRank.sold_price_measure_min', 
					'AgentRank.sold_price_measure_max', 
					'AgentRank.sold_price_measure_average', 
					'AgentRank.period_year', 
					'AgentRank.period_month', 
					'Region.id', 
					'Region.slug', 
					'Region.name', 
					'City.id', 
					'City.slug', 
					'City.name', 
					'Subarea.id', 
					'Subarea.slug', 
					'Subarea.name', 
				), 
				'conditions' => array(
					'AgentRank.parent_id'			=> $parentID, 
					'AgentRank.period_year'			=> $periodYear, 
					'AgentRank.period_month'		=> $periodMonth, 
					'AgentRank.property_action_id'	=> $actionID, 
					'AgentRank.property_type_id'	=> $typeID, 
					'AgentRank.region_id >'			=> 0, 
					'AgentRank.city_id >'			=> 0, 
					'AgentRank.subarea_id >'		=> 0, 
				), 
				'order' => array(
					'AgentRank.period_year'			=> 'desc', 
					'AgentRank.period_month'		=> 'desc',  
				//	'AgentRank.sold_property_count'	=> 'desc', 
				//	'AgentRank.property_count'		=> 'desc', 
					'Region.name'					=> 'asc', 
					'City.name'						=> 'asc', 
					'Subarea.name'					=> 'asc', 
				), 
				'group' => array(
					'AgentRank.period_year', 
					'AgentRank.period_month', 
					'AgentRank.property_action_id', 
					'AgentRank.property_type_id', 
					'AgentRank.city_id', 
					'AgentRank.subarea_id', 
				), 
			));

			$this->paginate = $this->AgentRank->getData('paginate', $options);

			$records			= $this->paginate('AgentRank');
			$propertyActions	= $this->Property->PropertyAction->getData('all');
			$propertyTypes		= $this->Property->PropertyType->getData('all', array(
				'order'			=> array(sprintf('field(PropertyType.id, %s)', implode(', ', $allowedTypes))), 
				'conditions'	=> array(
					'PropertyType.id' => $allowedTypes, 
				), 
			));

			$years	= array();
			$months	= Configure::read('__Site.monthly.named');

			if($months){
				krsort($months);
				$tempMonths = array();

				foreach($months as $key => $month){
					$tempMonths[$key + 1] = $month;
				}

				$months = $tempMonths;
			}

			$upperYear	= date('Y');
			$bottomYear = $upperYear - 50;

			for($year = $upperYear; $year > $bottomYear; $year--){
				$years[$year] = $year;
			}

			$this->request->data['Search'] = $namedParams;

			$this->set(array(
				'module_title'		=> __('Rate Properti'), 
				'records'			=> $records, 
				'propertyActions'	=> $propertyActions, 
				'propertyTypes'		=> $propertyTypes, 
				'years'				=> $years, 
				'months'			=> $months, 
				'active_menu'		=> 'price_movement',
			));
		}
		else{
			$this->RmCommon->redirectReferer(__('Hanya karyawan perusahaan yang boleh mengakses halaman ini'), 'error');
		}
	}

	public function price_movement(){
		$this->admin_price_movement();
		$this->render('price_movement');
	}
}