<?php
App::uses('AppController', 'Controller');

class CrmController extends AppController {
	public $helpers = array(
		'Crm', 'FileUpload.UploadForm',
		'Property'
	);
	public $components = array(
		'RmCrm', 'RmProperty',
		'RmImage','RmKpr',
		'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
	            'admin_projects' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'token', 'values',
				 	),
			 	),
	            'admin_project_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_project_edit' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_project_detail' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'activities', 'documentCategories', 'clients',
				 	),
			 	),
	            'admin_activity_edit' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_followup' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_edit_followup' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_project_clients' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'value',
				 	),
			 	),
	            'admin_project_contract' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'value',
				 	),
			 	),
	            'admin_project_document' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'value', 'documents',
				 	),
			 	),
	    //         'admin_project_document_add' => array(
				 //  	'extract' => array(
				 //  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				 // 	),
			 	// ),
	    //         'admin_project_document_edit' => array(
				 //  	'extract' => array(
				 //  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				 // 	),
			 	// ),
			 	'admin_project_upload_documents' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
	            'admin_project_document_delete' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_change_status' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'id', 'attributeSets'
				 	),
			 	),
	            'admin_status' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_cancel' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_activity_delete' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
    		),
    	),
	);

	function beforeFilter() {
		parent::beforeFilter();
	}

	function admin_search ( $action, $_admin = true, $addParam = false ) {
		$data = $this->request->data;
		$named = $this->RmCommon->filterEmptyField($this->params, 'named');
		$params = array(
			'action' => $action,
			'admin' => $_admin,
			$addParam,
		);

		if( !empty($named) ) {
			$params = array_merge($params, $named);
		}

		$this->RmCommon->processSorting($params, $data);
	}

	function admin_projects () {
		$this->loadModel('CrmProject');
		$module_title = __('Kegiatan (CRM)');
		$params = $this->params->params;

		$options =  $this->User->CrmProject->_callRefineParams($params, array(
			'group' => array(
    			'CrmProject.id',
			),
			'limit' => 15,
		));

		$elements = $this->RmCommon->_callRefineParams($params);

        $this->paginate = $this->User->CrmProject->getData('paginate', $options, $elements);
		$values = $this->paginate('CrmProject');

		if( !empty($values) ) {
	 		$virtualFields = Common::hashEmptyField($this->User->CrmProject->virtualFields, 'crmprojectactivity_count');
	 		
	 		if( !empty($virtualFields) ) {
				unset($this->User->CrmProject->virtualFields['crmprojectactivity_count']);
	 		}

			foreach ($values as $key => &$value) {
				$value = $this->User->CrmProject->getMergeList($value, array(
					'contain' => array(
						'Agent' => array(
							'uses' => 'User',
						),
						'Property' => array(
							'elements' => array(
								'company' => false,
								'status' => false,
							),
						),
						'CrmActivityCount' => array(
							'uses' => 'CrmProjectActivity',
							'type' => 'count',
						),
						'UserClient' => array(
							'primaryKey' => 'user_id',
							'foreignKey' => 'client_id',
							'elements' => array(
								'status' => false,
								'company' => false,
							),
							'contain' => array(
								'User',
							),
						),
						'AttributeSet',
					),
				));
				$value = $this->User->CrmProject->Property->getMergeList($value, array(
					'contain' => array(
						'PropertyType',
						'PropertyAction',
						'PropertyAddress' => array(
							'contain' => array(
								'Region',
								'City',
								'Subarea',
							),
						),
					),
				));
				
				$crm_project_id = Common::hashEmptyField($value, 'CrmProject.id');
				$value = $this->RmUser->_callLastActivity($value, array(
					'crm_project_id' => $crm_project_id,
				));
			}
		}

		$attributeSets = $this->User->CrmProject->AttributeSet->getData('list', array(
			'conditions' => array(
				'AttributeSet.scope' => 'crm',
			),
		));

		$this->set('active_menu', 'project');
		$this->set('class_body', 'body-crm');
		$this->set(compact(
			'module_title', 'values',
			'attributeSets'
		));
	}

	function admin_project_add ( $property_id = false ) {
		$module_title = __('Tambah Kegiatan (CRM)');
		$data = $this->request->data;
		$property_id = $this->RmCommon->filterEmptyField($data, 'Property', 'id', $property_id);

		if( !empty($property_id) ) {
			$flag = $this->User->Property->getData('first', array(
				'conditions' => array(
					'Property.id' => $property_id,
				),
			), array(
				'admin_mine' => true,
			));
		} else {
			$flag = true;
		}

		if( !empty($flag) ) {
			if( !empty($data) ) {
				$data = $this->RmCrm->_callBeforeSave($data);

				$user_id = Common::hashEmptyField($data, 'CrmProject.user_id');
				$data = $this->User->Property->_callPropertyMerge($data, $property_id, 'Property.id', false, $user_id);

				$result = $this->User->CrmProject->doSave( $data );
				$crm_project_id = $this->RmCommon->filterEmptyField($result, 'id');
				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'crm',
					'action' => 'project_detail',
					$crm_project_id,
					'admin' => true,
				));
				$this->request->data = $this->RmCommon->dataConverter($this->request->data, array(
					'date' => array(
						'CrmProject' => array(
							'project_date',
						),
					),
				), true);
			} else {
				$data = $this->User->Property->_callPropertyMerge($data, $property_id, 'Property.id');
				$data['CrmProject']['project_date'] = $this->RmCommon->currentDate('d/m/Y');
				
				$this->request->data = $data;
			}

			$client = $this->RmCommon->filterEmptyField($this->request->data, 'Client');
			$user_id = Common::hashEmptyField($this->request->data, 'CrmProject.user_id');
			$clientJobTypes = $this->User->Kpr->KprApplication->JobType->getList();

			if( !empty($client) ) {
				$this->request->data['Owner'] = $this->request->data['User'] = $client;
			}

			$this->set('active_menu', 'project');
			$this->set(compact(
				'module_title', 'clientJobTypes', 'user_id'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	function admin_project_edit ( $id = false ) {
		$module_title = __('Edit Kegiatan (CRM)');
		$data = $this->request->data;

		$value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
        		'CrmProject.attribute_set_id <>' => Configure::read('__Site.Global.Variable.CRM.Cancel'),
    		),
    	));

		if( !empty($value) ) {
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'UserClient',
					'Property',
				),
			));
			$value = $this->User->CrmProject->getMergeList($value, array(
				'contain' => array(
					'Agent' => array(
						'uses' => 'User',
					),
				),
			));
			$value = $this->User->Property->getDataList($value, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAsset',
					'User',
				),
			));
			$value['CrmProject']['client_email'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'email');

			if( !empty($data) ) {
				$property_id = $this->RmCommon->filterEmptyField($data, 'Property', 'id');
				$data['CrmProject']['client_job_type'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'job_type');

				$data = $this->RmCrm->_callBeforeSave($data);
				$data = $this->User->Property->_callPropertyMerge($data, $property_id, 'Property.id');
			}

			$result = $this->User->CrmProject->doSave( $data, $value, $id );
			$crm_project_id = $this->RmCommon->filterEmptyField($result, 'id');
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'crm',
				'action' => 'project_detail',
				$crm_project_id,
				'admin' => true,
			));

			if( empty($data) ) {
			 	$this->request->data = Hash::insert($this->request->data, 'CrmProject.user', Common::hashEmptyField($value, 'Agent.client_email'));
			}

			$this->request->data = $this->RmCommon->dataConverter($this->request->data, array(
				'date' => array(
					'CrmProject' => array(
						'project_date',
					),
				),
			), true);
			$client = $this->RmCommon->filterEmptyField($this->request->data, 'Owner');
			$user_id = Common::hashEmptyField($this->request->data, 'CrmProject.user_id');
			$clientJobTypes = $this->User->Kpr->KprApplication->JobType->getList();

			if( !empty($client) ) {
				$this->request->data['User'] = $client;
			}

			$this->set('active_menu', 'project');
			$this->set(compact(
				'module_title', 'id', 'clientJobTypes', 'user_id'
			));
			$this->render('admin_project_add');
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	public function admin_project_detail( $id = false ) {
		$module_title = __('Detil Kegiatan (CRM)');
		$subareas = $dataMedias = false;

        $value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$this->loadModel('CrmProjectActivity');
			$attribute_set_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'attribute_set_id');
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'UserClient',
					'Property',
					'Agent',
				),
			));

			$property_id = $this->RmCommon->filterEmptyField($value,'Property','id');
			$value = $this->User->Property->PropertyAddress->getMerge($value,$property_id);
			$value = $this->User->Property->getDataList($value, array(
				'contain' => array(
					'MergeDefault',
				),
			));
			$value = $this->User->CrmProject->AttributeSet->getMergeList($value, array(
				'contain' => array(
					'NextAttributeSet' => array(
						'uses' => 'AttributeSet',
						'primaryKey' => 'id',
						'foreignKey' => 'next_attribute_set_id',
					),
				),
			));
			$value = $this->RmKpr->_callDataByCRM($value);
			$data = $this->request->data;

			if( !empty($data) ) {
				$session_id = $this->RmCommon->filterEmptyField($data, 'CrmProjectActivity', 'session_id');
				$attribute_set_id = $this->RmCommon->filterEmptyField($data, 'CrmProjectActivity', 'attribute_set_id', $attribute_set_id);

				$data = $this->RmCrm->_callBeforeSaveActivity($data, $value, $attribute_set_id);
				$result = $this->User->CrmProject->CrmProjectActivity->doSave( $data, $value, $id );
				$region_id = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'region_id');				
				$city_id = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'city_id');				
				// $subarea_id = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'subarea_id');				

				$subareas = $this->User->Kpr->KprApplication->Subarea->getData('list',array(
					'conditions' => array(
						'region_id' => $region_id,
						'city_id' => $city_id,
					),
					'fields' => array('id','name'),
					'contain' => 0
				));

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'crm',
					'action' => 'project_detail',
					$id,
					'admin' => true,
				));
				$this->request->data = $this->RmCrm->_callBeforeRenderActivity($this->request->data);

				$dataMedias = $this->User->CrmProject->CrmProjectDocument->getData('all', array(
		        	'conditions' => array(
						'CrmProjectDocument.session_id' => $session_id,
					),
				));
			} else {
        		$session_id = String::uuid();
				$this->request->data['CrmProjectActivity']['attribute_set_id'] = $attribute_set_id;
				$this->request->data['CrmProjectPayment']['price'] = $this->RmCommon->filterEmptyField($value, 'Property', 'price_measure');
				$this->request->data = $this->RmCrm->_callBeforeViewKPR($value, $this->request->data);
			}

			$this->paginate = $this->User->CrmProject->CrmProjectActivity->getData('paginate', array(
				'conditions' => array(
					'CrmProjectActivity.crm_project_id' => $id,
				),
				'limit' => 10,
			));
			$activities = $this->paginate('CrmProjectActivity');
			$activities = $this->User->CrmProject->CrmProjectActivity->getDataList($activities);

			$this->RmCrm->_callGlobal($value, $attribute_set_id);
			$this->RmCrm->_callBeforeRender($value);

			$this->set(array(
				'value' => $value,
				'subareas' => $subareas,
				'activities' => $activities,
				'session_id' => $session_id,
				'dataMedias' => $dataMedias,
				'module_title' => $module_title,
				'active_menu' => 'project',
				'active_module' => 'crm',
				'crm_step' => 'activity',
				'active_tab' => 'detail',
			));

		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	public function admin_activity_edit( $id = false ) {
		$module_title = __('Edit Aktivitas');

        $value = $this->User->CrmProject->CrmProjectActivity->getData('first', array(
        	'conditions' => array(
        		'CrmProjectActivity.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$activity_id = $id;
			$crm_project_id = $this->RmCommon->filterEmptyField($value, 'CrmProjectActivity', 'crm_project_id', null);
			$attribute_set_id = $this->RmCommon->filterEmptyField($value, 'CrmProjectActivity', 'attribute_set_id', null);

			$value = $this->User->CrmProject->CrmProjectActivity->getDataList($value);
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'UserClient',
					'Property',
					'Agent',
				),
			));

			$session_id = $this->RmCommon->filterEmptyField($value, 'CrmProjectActivity', 'session_id');
			$kpr_application_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'id', null);

			$data = $this->request->data;
			$data = $this->RmCrm->_callBeforeSaveActivity($data, $value, $attribute_set_id);
			$data = $this->RmCrm->_callBeforeSaveKPR($data, $value);

			$result = $this->User->CrmProject->CrmProjectActivity->doSave( $data, $value, $crm_project_id, $id );
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'crm',
				'action' => 'project_detail',
				$crm_project_id,
				'admin' => true,
			), array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
			));
			$this->request->data = $this->RmCrm->_callBeforeRenderActivity($this->request->data);
			$this->RmCrm->_callGlobal($value, $attribute_set_id);
			$bankApplyCategories = $this->User->Kpr->BankApplyCategory->getData('list', array(
				'fields' => array(
					'BankApplyCategory.id', 'BankApplyCategory.category_name',
				),
			));
			$this->RmCommon->_callRequestSubarea('KprApplication');

			$this->set('active_module', 'crm');
			$this->set('active_menu', 'project');
			$this->set(compact(
				'module_title', 'value',
				'crm_project_id', 'session_id',
				'activity_id', 'bankApplyCategories',
				'kpr_application_id'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	public function admin_follow_up( $id = false ) {
		$module_title = __('Follow Up Aktivitas');

        $value = $this->User->CrmProject->CrmProjectActivity->getData('first', array(
        	'conditions' => array(
        		'CrmProjectActivity.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$crm_project_id = $this->RmCommon->filterEmptyField($value, 'CrmProjectActivity', 'crm_project_id', null);
			$value = $this->User->CrmProject->CrmProjectActivity->getDataList($value, $id);

			$dataFollowup = $this->RmCommon->filterEmptyField($value, 'CrmProjectActivityFollowup');

			if( empty($dataFollowup) ) {
				$data = $this->request->data;
				$data = $this->RmCommon->dataConverter($data, array(
					'date' => array(
						'CrmProjectActivityFollowup' => array(
							'activity_date',
						),
					),
				));

				$result = $this->User->CrmProject->CrmProjectActivity->CrmProjectActivityFollowup->doSave( $data, $value, $id );
				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'crm',
					'action' => 'project_detail',
					$crm_project_id,
					'admin' => true,
				), array(
					'ajaxFlash' => true,
					'ajaxRedirect' => true,
				));
				$this->request->data = $this->RmCommon->dataConverter($this->request->data, array(
					'date' => array(
						'CrmProjectActivityFollowup' => array(
							'activity_date',
						),
					),
				), true);
				$this->request->data = $this->RmCrm->_callBeforeRenderActivity($this->request->data);

				$this->set('active_menu', 'project');
				$this->set(compact(
					'module_title', 'value',
					'crm_project_id'
				));
			} else {
				$this->RmCommon->redirectReferer(__('Aktivitas tidak ditemukan'));
			}
		} else {
			$this->RmCommon->redirectReferer(__('Aktivitas tidak ditemukan'));
		}
	}

	public function admin_edit_followup( $id = false ) {
		$module_title = __('Edit Aktivitas Follow Up');

        $value = $this->User->CrmProject->CrmProjectActivity->CrmProjectActivityFollowup->getData('first', array(
        	'conditions' => array(
        		'CrmProjectActivityFollowup.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$crm_project_activity_id = $this->RmCommon->filterEmptyField($value, 'CrmProjectActivityFollowup', 'crm_project_activity_id', null);
			$value = $this->User->CrmProject->CrmProjectActivity->getMerge($value, $crm_project_activity_id);
			$value = $this->User->CrmProject->CrmProjectActivity->getDataList($value, $crm_project_activity_id);
			$crm_project_id = $this->RmCommon->filterEmptyField($value, 'CrmProjectActivity', 'crm_project_id', null);

			$data = $this->request->data;
			$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'CrmProjectActivityFollowup' => array(
						'activity_date',
					),
				),
			));

			$result = $this->User->CrmProject->CrmProjectActivity->CrmProjectActivityFollowup->doSave( $data, $value, $crm_project_activity_id, $id );
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'crm',
				'action' => 'project_detail',
				$crm_project_id,
				'admin' => true,
			), array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
			));
			$this->request->data = $this->RmCommon->dataConverter($this->request->data, array(
				'date' => array(
					'CrmProjectActivityFollowup' => array(
						'activity_date',
					),
				),
			), true);

			$this->set('active_menu', 'project');
			$this->set(compact(
				'module_title', 'value',
				'crm_project_id'
			));

			$this->render('admin_follow_up');
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	public function admin_project_clients( $id = false ) {
		$module_title = __('Daftar Klien');

        $value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'UserClient',
					'Property',
				),
			));
			$this->RmCrm->_callGlobal($value);

			$this->set('active_menu', 'project');
			$this->set('active_tab', 'clients');
			$this->set(compact(
				'module_title', 'value', 'id'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	public function admin_project_contract( $id = false ) {
        $value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$client_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'client_id');
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'UserClient',
					'Property',
					'Agent',
				),
			));
			$value = $this->User->Property->getDataList($value, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAsset',
				),
			));
			$this->RmCrm->_callGlobal($value);

			$this->set('active_menu', 'project');
			$this->set('active_tab', 'contract');
			$this->set(compact(
				'value'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'), 'error');
		}
    }

    public function admin_project_document( $id = false ) {
    	$value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$this->loadModel('CrmProjectDocument');
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'Property',
				),
			));
			$property_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'property_id');
			$user_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'user_id');
			$client_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'client_id');
			$owner_id = $this->RmCommon->filterEmptyField($value, 'Property', 'client_id');

			$this->paginate = $this->User->CrmProject->CrmProjectDocument->getData('paginate', array(
				'conditions' => array(
					'OR' => array(
						array(
							'CrmProjectDocument.document_type' => 'project',
							'CrmProjectDocument.owner_id' => $id,
						),
						array(
							'CrmProjectDocument.document_type' => 'property',
							'CrmProjectDocument.owner_id' => $property_id,
						),
						array(
							'CrmProjectDocument.document_type' => 'client',
							'CrmProjectDocument.owner_id' => $client_id,
						),
						array(
							'CrmProjectDocument.document_type' => 'owner',
							'CrmProjectDocument.owner_id' => $owner_id,
						),
						array(
							'CrmProjectDocument.document_type' => 'agent',
							'CrmProjectDocument.owner_id' => $user_id,
						),
					),
				),
	        	'order' => array(
					'CrmProjectDocument.id' => 'ASC',
        		),
				'limit' => 10,
			), array(
				'company' => true,
			));
			$documents = $this->paginate('CrmProjectDocument');
			$documents = $this->User->CrmProject->CrmProjectDocument->getDataList($documents);
			$this->RmCommon->_layout_file('fileupload');

			$this->RmCrm->_callGlobal($value);
			
			$this->set('active_menu', 'project');
			$this->set('active_tab', 'document');
			$this->set(compact(
				'value', 'id', 'documents'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'), 'error');
		}
	}

	public function admin_project_document_add( $id = false ) {
    	$value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));
    	
		if( !empty($value) ) {
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'Property',
				),
			));
			$data = $this->request->data;

    		if( !empty($data['CrmProjectDocument']) ) {
				$session_id = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument', 'session_id');

				$data = $this->RmCrm->_callBeforeSaveDocument($data, $value);
				$dataSave = $this->RmCommon->filterEmptyField($data, 'SaveDocument');
				$result = $this->User->CrmProject->CrmProjectDocument->doSaveMany($dataSave, false, true);
				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
					'ajaxRedirect' => true,
				));

				$dataMedias = $this->User->CrmProject->CrmProjectDocument->getData('all', array(
		        	'conditions' => array(
						'CrmProjectDocument.session_id' => $session_id,
					),
				));
			} else {
        		$session_id = String::uuid();
			}

			$documentCategories = $this->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('list', false, array(
				'type' => 'project',
			));

			$this->set(compact(
				'value', 'id', 'session_id', 'dataMedias',
				'documentCategories'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'), 'error');
		}
	}

	public function admin_project_document_edit( $crm_id = null, $id = false ) {
    	$value = $this->User->CrmProject->CrmProjectDocument->getData('first', array(
        	'conditions' => array(
        		'CrmProjectDocument.id' => $id,
    		),
    	), array(
    		'company' => true,
    	));

		if( !empty($value) ) {
			$data = $this->request->data;

	    	$crm = $this->User->CrmProject->getData('first', array(
	        	'conditions' => array(
	        		'CrmProject.id' => $crm_id,
	    		),
	    	));
			$crm = $this->User->CrmProject->getDataList($crm, array(
				'contain' => array(
					'Property',
				),
			));

			$result = $this->User->CrmProject->CrmProjectDocument->doEdit($data, $value, $id);
			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
			));

			$property_id = $this->RmCommon->filterEmptyField($crm, 'CrmProject', 'property_id', null);
			$user_id = $this->RmCommon->filterEmptyField($crm, 'CrmProject', 'user_id', null);
			$client_id = $this->RmCommon->filterEmptyField($crm, 'CrmProject', 'client_id', null);
			$owner_id = $this->RmCommon->filterEmptyField($crm, 'Property', 'client_id', null);

			$neighbors = $this->User->CrmProject->CrmProjectDocument->find(
		        'neighbors',
		        array(
		        	'field' => 'CrmProjectDocument.id',
		        	'value' => $id,
		        	'conditions' => array(
		        		'CrmProjectDocument.company_id' => Configure::read('Principle.id'),
		        		'CrmProjectDocument.status' => 1,
		        		'OR' => array(
							array(
								'CrmProjectDocument.document_type' => 'project',
								'CrmProjectDocument.owner_id' => $crm_id,
							),
							array(
								'CrmProjectDocument.document_type' => 'property',
								'CrmProjectDocument.owner_id' => $property_id,
							),
							array(
								'CrmProjectDocument.document_type' => 'client',
								'CrmProjectDocument.owner_id' => $client_id,
							),
							array(
								'CrmProjectDocument.document_type' => 'owner',
								'CrmProjectDocument.owner_id' => $owner_id,
							),
							array(
								'CrmProjectDocument.document_type' => 'agent',
								'CrmProjectDocument.owner_id' => $user_id,
							),
						),
	        		),
	        	)
		    );
			$documentCategories = $this->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('list', false, array(
				'type' => 'project',
			));

			$this->set(compact(
				'value', 'id', 'neighbors',
				'documentCategories', 'crm_id'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'), 'error');
		}
	}

	public function admin_project_upload_documents( $id = false ) {
    	$value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));
    	
		if( !empty($value) ) {
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'Property',
				),
			));
			$data = $this->request->data;

    		if( !empty($data) ) {
				$save_path = Configure::read('__Site.document_folder');
				$crmProjectDocument = Set::extract('/CrmProjectDocument/file/name', $data);
				$crmProjectDocument = array_filter($crmProjectDocument);
				$dataSave = array();

    			if( !empty($crmProjectDocument) ) {
					$client_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'client_id');
					$user_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'user_id');
					$property_id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
					$owner_id = $this->RmCommon->filterEmptyField($value, 'Property', 'client_id');

					if( !empty($data['CrmProjectDocument']['file']) ) {
						$msgError = false;

						foreach ($data['CrmProjectDocument']['file'] as $category_id => $img) {
							$documentCategory = $this->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('first', array(
								'conditions' => array(
									'DocumentCategory.id' => $category_id,
								),
							));
							$type = $this->RmCommon->filterEmptyField($documentCategory, 'DocumentCategory', 'type');
							$category = $this->RmCommon->filterEmptyField($documentCategory, 'DocumentCategory', 'name');

							if( !empty($img['name']) ) {
								$dataImg['CrmProjectDocument']['file'] = $img;
								$dataUpload = $this->RmImage->_uploadPhoto($dataImg, 'CrmProjectDocument', 'file', $save_path, false, Configure::read('__Site.allowed_all_ext'));
								$upload = $this->RmCommon->filterEmptyField($dataUpload, 'Upload');
								$error = $this->RmCommon->filterEmptyField($upload, 'file', 'error');

								if( !empty($error) ) {
									$message = $this->RmCommon->filterEmptyField($upload, 'file', 'message');
									$msgError = $message;
								} else {
									$file_name = $this->RmCommon->filterEmptyField($dataUpload, 'CrmProjectDocument', 'file_name');
									$file_title = $category;

									$dataUpload['CrmProjectDocument']['document_category_id'] = $category_id;
									$dataUpload['CrmProjectDocument']['document_type'] = $type;
									$dataUpload['CrmProjectDocument']['name'] = $file_name;
									$dataUpload['CrmProjectDocument']['title'] = $file_title;

									switch ($type) {
										case 'client':
											$owner_file_id = $client_id;
											break;
										case 'project':
											$owner_file_id = $id;
											break;
										case 'owner':
											$owner_file_id = $owner_id;
											break;
										case 'agent':
											$owner_file_id = $user_id;
											break;
										case 'property':
											$owner_file_id = $property_id;
											break;
									}
									$dataUpload['CrmProjectDocument']['owner_id'] = !empty($owner_file_id)?$owner_file_id:0;
									$dataSave[] = $dataUpload;
								}
							}
						}
					}
					
					$result = $this->User->CrmProject->CrmProjectDocument->doSaveMany($dataSave, false, true);

					if( !empty($msgError) ) {
						$this->RmCommon->redirectReferer($msgError, 'error');
					} else {
						$this->RmCommon->setProcessParams($result);
					}
				} else {
					$this->RmCommon->redirectReferer(__('Mohon unggah dokumen terlebih dahulu'), 'error');
				}
			} else {
				$this->RmCommon->redirectReferer(__('Mohon unggah dokumen terlebih dahulu'), 'error');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'), 'error');
		}
	}

	public function admin_project_document_delete( $id = false ) {
    	$value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$data = $this->request->data;
			$id = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument', 'id');

	    	$result = $this->User->CrmProject->CrmProjectDocument->doDelete( $id );
			$this->RmCommon->setProcessParams($result);
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'), 'error');
		}
	}

	// public function admin_submmission_nonaktif($id){
	// 	$result 	= $this->User->CrmProject->KprApplication->KprApplicationRequest->getData('first',array('conditions' => array('id' => $id)));
	// 	$bank_id 	= $this->RmCommon->filterEmptyField($result,'KprApplicationRequest','bank_id');
	// 	$result 	= $this->User->CrmProject->KprApplication->KprApplicationRequest->Bank->getMerge($result,$bank_id);
	// 	$this->set(comapct('result'));
	// }

	public function admin_change_status( $id = false, $type = false ) {
        $value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));
    	$attributeSets = $this->User->CrmProject->AttributeSet->getData('first', array(
			'conditions' => array(
				'AttributeSet.scope' => 'crm',
				'AttributeSet.slug' => $type,
			),
		));

		if( !empty($value) && !empty($attributeSets) ) {
			$this->set(compact(
				'type', 'attributeSets',
				'id'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'), 'error');
		}
	}

	public function admin_status( $id = false, $type = false ) {
        $value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));
    	$value = $this->User->CrmProject->getDataList($value, array(
			'contain' => array(
				'UserClient',
				'Property',
				'Agent',
			),
		));

    	$attributeSets = $this->User->CrmProject->AttributeSet->getData('first', array(
			'conditions' => array(
				'AttributeSet.scope' => 'crm',
				'AttributeSet.slug' => $type,
			),
		));

		if( !empty($value) && !empty($attributeSets) ) {
			$attribute_set_id = $this->RmCommon->filterEmptyField($attributeSets, 'AttributeSet', 'id');
			$data = $this->RmCrm->_callBeforeSaveActivity( false, $value, false, $attribute_set_id );

			$result = $this->User->CrmProject->doStatus( $id, $value, $attributeSets, $data );
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'crm',
				'action' => 'project_detail',
				$id,
				'admin' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'), 'error');
		}
	}

	// public function admin_project_submission( $id = false ){

	// 	$this->loadModel('KprApplicationRequest');
	// 	$module_title = __('Pengajuan Kpr');
 //        $value = $this->User->CrmProject->getData('first', array(
 //        	'conditions' => array(
 //        		'CrmProject.id' => $id,
 //    		),
 //    	));

 //    	if( !empty($value) ) {
 //    		$attribute_set_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'attribute_set_id');
 //    		$value = $this->User->CrmProject->getDataList($value, array(
	// 			'contain' => array(
	// 				'Property'
	// 			),
	// 		));
 //    		$value = $this->User->CrmProject->Property->getDataList($value,array(
 //    				'contain' => array(
 //    						'PropertyAddress'
 //    				)
 //    		));

	// 		$kpr_application_id 		= $this->RmCommon->filterEmptyField($value,'KprApplication','id');
			
	// 		$kpr_application_requests 	= $this->User->CrmProject->KprApplication->KprApplicationRequest->getData('all',array(
	// 					'conditions' => array(
	// 								'KprApplicationRequest.kpr_application_id' => $kpr_application_id
	// 					)
	// 		));

	// 		$kpr_application_requests 	= $this->User->CrmProject->KprApplication->KprApplicationRequest->getDataList($kpr_application_requests);

	// 		$req_complete = $this->User->KprApplication->KprApplicationRequest->getData('count',array(
	// 				'conditions' => array(
	// 					'KprApplicationRequest.crm_project_id' => $id,
	// 					'KprApplicationRequest.assign_project' => 1
	// 				)
	// 		));
	// 		$kpr_application_requests['KprApplicationRequest'] = $kpr_application_requests;
	// 		$req_complete = $this->User->KprApplication->KprApplicationRequest->getData('count',array(
	// 			'conditions' => array(
	// 				'KprApplicationRequest.crm_project_id' => $id,
	// 				'KprApplicationRequest.assign_project' => 1
	// 			)
	// 		));

	// 		$this->set('active_menu', 'project');
	// 		$this->set('active_tab', 'submission');
	// 		$this->set(compact(
	// 			'module_title', 'value',
	// 			'id','kpr_application_requests','req_complete'
	// 		));

 //    	}else {
	// 		$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
	// 	}


	// }

	// public function admin_submission_nonaktif(){

	// 	$data = $this->request->data;
	// 	$id = $this->RmCommon->filterEmptyField($data, 'KprApplicationRequest', 'id');
 //    	$result = $this->User->KprApplication->KprApplicationRequest->doToggle( $id );
	// 	$this->RmCommon->setProcessParams($result, false, array(
	// 		'redirectError' => true,
	// 	));
		
	// }

	public function admin_project_payment( $id = false ) {
		$this->loadModel('Bank');

		$module_title = __('Pembayaran');
        $value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$attribute_set_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'attribute_set_id');
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'Property',
				),
			));
			$value = $this->User->Property->getDataList($value, array(
				'contain' => array(
					'MergeDefault',
				),
			));
			$projectStatus = $this->RmCommon->filterEmptyField($value, 'AttributeSet', 'slug');
			if($projectStatus == 'finalisasi'){

				$documentCategories = $this->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('all', array(
					'conditions' => array(
						'DocumentCategory.is_required' => 1,
					),
				));
				$document_category_id = Set::extract('/DocumentCategory/id', $documentCategories);
				$documentCategories = $this->User->CrmProject->CrmProjectDocument->getByCategories($documentCategories, $value);
			}

			$crm_project_payment_id = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'id');
			$kprApplication 		= $this->RmCommon->filterEmptyField($value, 'KprApplication');

			if( !empty($kprApplication) ) {
				$bank_apply_category_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'bank_apply_category_id');
				$kpr_region_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'region_id');
				$kpr_city_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'city_id');
				$kpr_subarea_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'subarea_id');
				$kpr_application_id = $this->RmCommon->filterEmptyField($value,'KprApplication','id');

				$value = $this->User->CrmProject->CrmProjectActivity->KprApplication->BankApplyCategory->getMerge($value, $bank_apply_category_id);
				$value = $this->User->UserProfile->Region->getMerge($value, $kpr_region_id, 'KprApplicationRegion', array(
					'cache' => array(
						'name' => __('Region.%s', $kpr_region_id),
					),
				));
				$value = $this->User->UserProfile->City->getMerge($value, $kpr_city_id, 'KprApplicationCity', 'City.id', array(
					'cache' => __('City.%s', $kpr_city_id),
				));
				$value = $this->User->UserProfile->Subarea->getMerge($value, $kpr_subarea_id, 'KprApplicationSubarea', 'Subarea.id', array(
					'cache' => __('Subarea.%s', $kpr_subarea_id),
					'cacheConfig' => 'subareas',
				));
				$value = $this->User->Kpr->getMerge($value, $id,'Kpr.crm_project_id');

				// $kpr_request    = $this->User->CrmProject->CrmProjectActivity->KprApplication->KprApplicationRequest->get('all',array('conditions' => array('kpr_application_id' => $kpr_application_id)));
				// $kpr_request 	= $this->User->CrmProject->CrmProjectActivity->KprApplication->KprApplicationRequest->
			}

			$data = $this->request->data;

			if( !empty($data) ) {
				$data = $this->RmCommon->dataConverter($data, array(
					'price' => array(
						'CrmProjectPayment' => array(
							'price',
						),
					),
				));

				$result = $this->User->CrmProject->CrmProjectPayment->doSave( $data, $id, $crm_project_payment_id );
				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'crm',
					'action' => 'project_payment',
					$id,
					'admin' => true,
				));
			} else {
				$this->request->data['CrmProjectActivity']['attribute_set_id'] = $attribute_set_id;
			}

			$bankKpr = $this->User->Kpr->KprBank->Bank->getKpr();
			$this->RmCrm->_callGlobal($value);

			$this->set('active_menu', 'project');
			$this->set('active_tab', 'payment');
			$this->set(compact(
				'module_title', 'value',
				'id', 'bankKpr','kpr_request','documentCategories'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	public function admin_project_payment_add( $id = false ) {
		$module_title = __('Informasi Pembayaran');

        $value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$property_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'property_id', null);
			$value = $this->User->Property->getMerge($value, $property_id);
			$value = $this->User->Property->getDataList($value, array(
				'contain' => array(
					'MergeDefault',
				),
			));

			$data = $this->request->data;

			if( !empty($data) ) {
				$data = $this->RmCommon->dataConverter($data, array(
					'price' => array(
						'CrmProjectPayment' => array(
							'price',
							'down_payment',
						),
					),
				));

				$result = $this->User->CrmProject->CrmProjectPayment->doSave( $data, $id );
				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'crm',
					'action' => 'project_payment',
					$id,
					'admin' => true,
				), array(
					'ajaxFlash' => true,
					'ajaxRedirect' => true,
				));
			} else {
				$price = $this->RmCommon->filterEmptyField($value, 'Property', 'price');
				$this->request->data['CrmProjectPayment']['price'] = $price;
			}

			$this->RmCrm->_callGlobal($value);

			$this->set('active_menu', 'project');
			$this->set('active_tab', 'payment');
			$this->set(compact(
				'module_title', 'value'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	public function admin_project_payment_edit( $id = false, $crm_project_payment_id = false ) {
		$module_title = __('Edit Informasi Pembayaran');

        $value = $this->User->CrmProject->CrmProjectPayment->getData('first', array(
        	'conditions' => array(
        		'CrmProjectPayment.id' => $crm_project_payment_id,
        		'CrmProjectPayment.crm_project_id' => $id,
    		),
    	));

		$value = $this->User->CrmProject->getMerge($value, $id);
		$value = $this->User->Kpr->getMerge($value, $id,'Kpr.crm_project_id');

		if( !empty($value['CrmProjectPayment']) && !empty($value['CrmProject']) ) {
			$payment_type = $this->RmCommon->filterEmptyField($value, 'CrmProjectPayment', 'type');
			$kpr_status = $this->RmCommon->filterEmptyField($value, 'Kpr', 'document_status', 'pending');

			if( $payment_type == 'kpr' && $kpr_status != 'pending' ) {
				$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
			} else {
				$value = $this->User->CrmProject->getDataList($value, array(
					'contain' => array(
						'Property',
					),
				));
				$value = $this->User->Property->getDataList($value, array(
					'contain' => array(
						'MergeDefault',
					),
				));

				$data = $this->request->data;
				$data = $this->RmCrm->_callBeforeSavePayment($data, $value);

				$result = $this->User->CrmProject->CrmProjectPayment->doSave( $data, $id, $crm_project_payment_id, $value, false, true );
				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'crm',
					'action' => 'project_payment',
					$id,
					'admin' => true,
				), array(
					'ajaxFlash' => true,
					'ajaxRedirect' => true,
				));
				$this->request->data = $this->RmCrm->_callBeforeViewPayment($this->request->data);

				$this->RmCrm->_callGlobal($value);

				$this->set('_flash', false);
				$this->set('active_menu', 'project');
				$this->set('active_tab', 'payment');
				$this->set(compact(
					'module_title', 'value'
				));
				$this->render('admin_project_payment_add');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	public function admin_project_kpr( $id = false ) {
		$module_title = __('Edit Informasi Pembayaran');
        $value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'Property',
					'UserClient',
				),
			));
			$value = $this->User->Property->getDataList($value, array(
				'contain' => array(
					'MergeDefault',
				),
			));

			$kpr_application_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'id');
			$property_type = $this->RmCommon->filterEmptyField($value, 'Property', 'property_type_id');
			$data = $this->request->data;

			if( !empty($data) ) {
				$data = $this->RmCrm->_callBeforeSaveKPR($data, $value);
				$result = $this->User->CrmProject->KprApplication->doSave( $data, $kpr_application_id );
				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
					'ajaxRedirect' => true,
				));
			} else {
				$this->request->data = $this->RmCrm->_callBeforeViewKPR($value);
			}

			$this->request->data = $this->RmCommon->dataConverter($this->request->data, array(
				'date' => array(
					'KprApplication' => array(
						'birthday',
					),
				),
			), true);

			$this->RmCrm->_callGlobal($value);
			$this->RmCommon->_callRequestSubarea('KprApplication');

			$bankApplyCategories = $this->User->KprApplication->BankApplyCategory->getData('list', array(
				'fields' => array(
					'BankApplyCategory.id', 'BankApplyCategory.category_name',
				),
			));

			$this->set('active_menu', 'project');
			$this->set('active_tab', 'payment');
			$this->set('active_module', 'crm');
			$this->set('lblMandatory', false);
			$this->set(compact(
				'module_title', 'value',
				'bankApplyCategories', 'kpr_application_id'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Project tidak ditemukan'));
		}
	}

	public function admin_cancel() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'CrmProject', 'id');

    	$result = $this->User->CrmProject->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

	public function admin_attribute_set( $id = false, $attribute_set_id = false ) {
		$value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));

		if( !empty($value) ) {
			$crm_project_id = $id;
			$session_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'session_id');
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'UserClient',
					'Property',
				),
			));
			$this->RmCrm->_callGlobal($value);

			$attributeSetValue = $this->User->CrmProject->AttributeSet->getData('first', array(
				'conditions' => array(
					'AttributeSet.id' => $attribute_set_id,
				),
			));
			$attributeSetValue = $this->User->CrmProject->AttributeSet->getDataList($attributeSetValue);
			$this->request->data['CrmProjectActivity']['attribute_set_id'] = $attribute_set_id;

			$this->set(compact(
				'attributeSetValue', 'value',
				'session_id', 'crm_project_id',
				'attribute_set_id'
			));
			$this->render('/Elements/blocks/crm/forms/add_activity');
		} else {
			$this->RmCommon->redirectReferer(__('Gagal melakukan proses'), 'error');
		}
	}

	public function admin_attributes( $id, $attribute_option = false, $wrapperAttribute = false ) { 
		$value = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $id,
    		),
    	));
		$data = $this->request->data;
		$data_attribute_option = Common::hashEmptyField($data, 'CrmProjectActivityAttributeOption.attribute_option_id');

		if( empty($attribute_option) && !empty($data_attribute_option) ) {
			$data_attribute_option = reset($data_attribute_option);

			if( !empty($data_attribute_option) ) {
				$attribute_option = $data_attribute_option;
			}
		}

		if( is_numeric($attribute_option) ) {
			$conditions = array(
				'AttributeOption.id' => $attribute_option,
			);
		} else {
			$conditions = array(
				'AttributeOption.slug' => $attribute_option,
			);
		}

		$payment_type = false;
    	$attributeOption = $this->User->CrmProject->AttributeSet->AttributeSetOption->Attribute->AttributeOption->getData('first', array(
			'conditions' => $conditions,
		));

		if( !empty($value) ) {		
			$value = $this->User->CrmProject->getDataList($value, array(
				'contain' => array(
					'Property',
					'UserClient',
				),
			));
			$value = $this->RmKpr->_callDataByProperty($value);

			$property_id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
			$value = $this->User->Property->PropertyAddress->getMerge($value,$property_id);

			$crm_project_id = $id;
			$session_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'session_id');
			$activity_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'activity_id');
			$payment_type = $this->RmCommon->filterEmptyField($this->params, 'named', 'payment');

			$attribute_option_id = $this->RmCommon->filterEmptyField($attributeOption, 'AttributeOption', 'id');
			$attribute_option_id = $this->RmCommon->filterEmptyField($attributeOption, 'AttributeOption', 'id');
			$attribute_set = $this->RmCommon->filterEmptyField($value, 'AttributeSet', 'slug');
			$attribute_set = $this->RmCommon->filterEmptyField($this->params, 'named', 'attribute_set', $attribute_set);
			
			$attributeSet = $this->User->CrmProject->AttributeSet->getData('first', array(
				'conditions' => array(
					'AttributeSet.slug' => $attribute_set,
				),
			));

			$attribute_set_id = $this->RmCommon->filterEmptyField($attributeSet, 'AttributeSet', 'id');

			$property_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'property_id');
			$value = $this->User->Property->getMerge($value, $property_id);
			$value = $this->User->Property->getDataList($value, array(
				'contain' => array(
					'MergeDefault',
				),
			));
			$bank_apply_category_id = $this->RmCommon->filterEmptyField($value,'KprApplication','bank_apply_category_id');
			$value = $this->User->Kpr->BankApplyCategory->getMerge($value,$bank_apply_category_id);
			$property_type_id = $this->RmCommon->filterEmptyField($value, 'Property', 'property_type_id');
			$attributeOption = $this->User->CrmProject->AttributeSet->AttributeSetOption->Attribute->AttributeOption->getMerge(array(), $attribute_option_id, 'AttributeOption', 'first');
			$attribute_id = $this->RmCommon->filterEmptyField($attributeOption, 'AttributeOption', 'attribute_id');

			$this->request->data = $this->RmCrm->_callBeforeViewKPR($value);
			$price_measure = $this->RmCommon->filterEmptyField($value, 'Property', 'price_measure');
			$sold_price = $this->RmCommon->filterEmptyField($value, 'Property', 'sold_price', $price_measure);
			$sold_date = $this->RmCommon->filterEmptyField($value, 'Property', 'sold_date');

			$this->request->data['CrmProjectPayment']['price'] = $sold_price;
			$this->request->data['CrmProjectPayment']['sold_date'] = $sold_date;
			$attributeSetValue['AttributeSetOption'][] = $this->User->CrmProject->AttributeSet->AttributeSetOption->Attribute->AttributeOption->getChids(array(), $attribute_option_id);

			$full_input = true;
			$addClass = $wrapperAttribute;

			switch ($payment_type) {
				case 'kpr':
					$this->request->data = $this->RmCrm->_callBeforeViewKPR($value, $this->request->data);
					// $this->request->data = $this->RmCrm->_callBeforeViewAppReq($value, $this->request->data);
					$region_id = $this->RmCommon->filterEmptyField($this->request->data, 'KprApplication', 'region_id');
					$property_type_id = $this->RmCommon->filterEmptyField($value,'Property','property_type_id');
											
					$region_id = $this->RmCommon->filterEmptyField($value,'PropertyAddress','region_id');
					$city_id = $this->RmCommon->filterEmptyField($value,'PropertyAddress','city_id');
					$price = $this->RmCommon->filterEmptyField($value,'Property','price'); 

					// GET Bank
					$kpr_application_id = $this->RmCommon->filterEmptyField($value,'KprApplication','id');

					$crm_project_payment = $this->RmCommon->filterEmptyField($value,'CrmProjectPayment');

					// $banks = $this->User->KprApplication->KprApplicationRequest->getDataBank($kpr_application_id,$crm_project_payment);
					// $banks = $this->User->KprApplication->KprApplicationRequest->Bank->BankCommissionSetting->getKomisi($banks, array(
					// 	'property_type_id' => $property_type_id,
					// 	'region_id' => $region_id,
					// 	'city_id' => $city_id,
					// 	'price' => $price,
					// ));

					// $banks = $this->RmKpr->getSummaryKpr($value,$banks);

					// $count_bank = $this->RmKpr->getCountSummary($banks);
					// End Bank

					$this->RmCommon->_callRequestSubarea('KprApplication');
					$this->request->data['KprApplication']['bank_apply_category_id'] = $this->RmCrm->_callSetApplicationType($property_type_id);
					break;
			}

			if( $attribute_option == 'informasi-pembayaran' || $payment_type == 'kpr' ) {
				$bankApplyCategories = $this->User->Kpr->BankApplyCategory->getData('list', array(
					'fields' => array(
						'BankApplyCategory.id', 'BankApplyCategory.category_name',
					),
				));
			}

			$classCol = $this->RmCrm->_callSetColActivity( $attribute_option, $payment_type );

			$crm_client_id = $this->RmCommon->filterEmptyField($value, 'CrmProject', 'client_id');
			$client_id = $this->RmCommon->filterEmptyField($data, 'CrmProjectActivity', 'client_id');

			if( !empty($client_id) && $crm_client_id != $client_id ) {
				$userClient = $this->User->UserClient->getMerge(array(), $client_id);
				
				$value['CurrentClient'] = Common::hashEmptyField($userClient, 'UserClient');
			} else {
				$value['CurrentClient'] = Common::hashEmptyField($value, 'UserClient');
			}

			$this->set('active_module', 'crm');
			$this->set(compact(
				'attributeSetValue', 'full_input',
				'wrapperAttribute', 'addClass', 'value',
				'session_id', 'crm_project_id', 'activity_id',
				'attribute_set_id', 'attribute_option_id',
				'attribute_id','banks','kpr_req', 'classCol', 'count_bank',
				'bankApplyCategories'
			));

			$this->render('/Elements/blocks/crm/forms/additional_input');
		} else {
			return false;
		}
	}

	public function admin_project_load_more( $property_id = false, $user_id = false ) {
		$relations = $this->User->CrmProject->getData('all', array(
			'conditions' => array(
				'CrmProject.property_id' => $property_id,
				'CrmProject.user_id' => $user_id,
			),
		), array(
			'status' => 'active',
		));
		$relations = $this->User->CrmProject->getDataList($relations, array(
			'contain' => array(
				'UserClient',
			),
		), false);

		$this->set(compact(
			'relations', 'property_id', 'user_id'
		));
	}

	public function admin_activity_delete( $id = false ) {
		$this->loadModel('CrmProjectActivity');
    	$value = $this->User->CrmProject->CrmProjectActivity->getData('first', array(
        	'conditions' => array(
        		'CrmProjectActivity.id' => $id,
    		),
    	));
		$crm_project_id = $this->RmCommon->filterEmptyField($value, 'CrmProjectActivity', 'crm_project_id', null);
		$crmProject = $this->User->CrmProject->getData('first', array(
        	'conditions' => array(
        		'CrmProject.id' => $crm_project_id,
    		),
    	));
		$this->autoRender = false;

		if( !empty($value) && !empty($crmProject) ) {
	    	$result = $this->User->CrmProject->CrmProjectActivity->doDelete( $id, $value );
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'crm',
				'action' => 'project_detail',
				$crm_project_id,
				'admin' => true,
			), array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Aktivitas tidak ditemukan'), 'error');
		}
	}

	// function admin_updateKprComplete($id){
	// 	if($id){
	// 		$data = $this->User->KprApplication->KprApplicationRequest->getData('first',array(
	// 			'conditions' => array(
	// 				'KprApplicationRequest.id' => $id
	// 				)
	// 			));
	// 		$crm_project_id = $this->RmCommon->filterEmptyField($data,'KprApplicationRequest','crm_project_id');
	// 		if(!empty($data)){
	// 			$result = $this->User->KprApplication->KprApplicationRequest->doUpdateComplete($data);
	// 			$this->RmCommon->setProcessParams($result, array(
	// 				'controller' => 'crm',
	// 				'action' => 'project_submission',
	// 				$crm_project_id,
	// 				'admin' => true,
	// 			));
	// 		}
	// 	}else{
	// 		$msg = __('Data tidak ditemukan');
	// 		$this->RmCommon->redirectReferer($msg);
	// 	}
	// }

	function admin_detailView($type_file = false, $save_path = false, $kpr_application_id = false){
		$value = $this->User->KprApplication->getData('first',array(
			'conditions' => array(
				'KprApplication.id' => $kpr_application_id
			),
		), array(
			'admin_mine' => true,
		));

		if(!empty($value)){
			$value = $this->RmCrm->_callBeforeViewKPR($value);
			$filename = sprintf('%s_hide',$type_file);

			if(!empty($save_path)){
				$value['KprApplication']['save_path'] = $save_path;
			}

			if(!empty($type_file)){
				$value['KprApplication']['filename'] = $filename;
			}

			$this->set(compact('value'));
			$this->render('admin_detail_view');
		}else{
			$this->RmCommon->redirectReferer(__('foto tidak ditemukan'), 'error');
		}
	}

	function admin_categories ( $id = null ) {
		$this->loadModel('CrmProjectActivity');

		$value = $this->CrmProjectActivity->CrmProjectActivityAttributeOption->AttributeOption->getData('first', array(
			'conditions' => array(
				'AttributeOption.id' => $id,
			),
		));

		if( !empty($value) ) {
			$title = __('Aktivitas');
			$params = $this->params->params;
	    	$slug = Common::hashEmptyField($params, 'named.status', 'all');

			$this->RmCommon->_callRefineParams($params);
			$options =  $this->CrmProjectActivity->_callRefineParams($params, array(
	        	'conditions' => array(
	        		'CrmProjectActivityAttributeOption.attribute_option_id' => $id,
        		),
				'group' => array(
					'CrmProjectActivity.id',
				),
				'limit' => 15,
			));

	        $this->paginate = $this->CrmProjectActivity->getData('paginate', $options, array(
				'status' => 'active',
				'mine' => true,
				'company' => true,
				'attribute_option' => true,
			));
			$values = $this->paginate('CrmProjectActivity');

			if( !empty($values) ) {
				foreach ($values as $key => &$value) {
					$value = $this->CrmProjectActivity->getMergeList($value, array(
						'contain' => array(
							'AttributeSet',
							'UserClient' => array(
								'uses' => 'User',
								'primaryKey' => 'id',
								'foreignKey' => 'client_id',
								'elements' => array(
									'status' => false,
									'company' => false,
								),
								'contain' => array(
									'User',
								),
							),
						),
					));
					$value = $this->CrmProjectActivity->CrmProjectActivityAttributeOption->getMergeList($value, array(
						'contain' => array(
							'AttributeOption',
						),
					));
				}
			}
			$this->RmCrm->_callAttributeOptions();

			$this->set('active_menu', 'kpi_marketing');
			$this->set('module_title', $title);
			$this->set(array(
				'values' => $values,
				'module_title' => $title,
				'active_menu' => 'kpi_marketing',
				'current_id' => $id,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Aktivitas tidak ditemukan'));
		}
	}

	function admin_get_agent ( $keyword = null ) {
		$keyword = explode('|', $keyword);

		if( !empty($keyword) ) {
			$keyword = reset($keyword);
			$keyword = trim($keyword);

			$value = $this->User->getData('first', array(
				'conditions'	=> array(
					'User.email' => $keyword, 
				), 
			), array(
				'role'		=> 'agent', 
				'status'	=> 'active', 
				'company'	=> true, 
			));

			$this->set(array(
				'user_id' => Common::hashEmptyField($value, 'User.id'),
			));
		}
		
		$this->render('/Elements/blocks/crm/property');
	}
}
