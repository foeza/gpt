<?php
class RmGroupComponent extends Component {
	var $components = array(
		'RmCommon', 'RmUser'
	); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	function _callBeforeSave($value = null, $id = null, $user = false ) {
		$data = $this->controller->request->data;
		$params = $this->controller->params->params;

		$params_user_id = Common::hashEmptyField($params, 'named.user_id');

		$recordID = Common::hashEmptyField($user, 'User.id');
		$slug = Common::hashEmptyField($user, 'Group.name', false, array(
			'type' => 'strtolower',
		));

		$user_company_id = Common::hashEmptyField($params, 'named.user_id', Configure::read('Principle.id'));
		$User = Configure::read('User.data');

		if ( !empty($data) ) {			
			$name = Common::hashEmptyField($data, 'Group.name');
		 	$data = $this->RmCommon->dataConverter($data, array(
		 		'ucfirst' => array(
		 			'Group' => array('name'),
		 		),
		 	));

			if( !empty($id) ) {
			 	$data = Hash::insert($data, 'Group.id', $id);

			 	// check apakah yang di edit tidak mempunyai company_id
			 	$group = $this->controller->Group->getData('count', array(
			 		'conditions' => array(
			 			'Group.id' => $id,
			 			'Group.user_id' => false,
			 		),
			 	));
			 	if(!empty($group)){
			 		$not_acl = true;
			 		$data = Common::_callUnset($data, array(
			 			'Group' => array(
			 				'name',
			 			),
			 		));
			 	}
			 	// 

			 	if( empty($user_company_id) ) {
					$data['Group']['user_id'] = 0;
			 	} else {
					$data['Group']['slug'] = $this->RmCommon->toSlug($name);
			 	}
			} else {
				$generate_aros = true;
				$data['Group']['user_id'] = $user_company_id;
				$data['Group']['slug'] = $this->RmCommon->toSlug($name);
			}

			if( !empty($data['GroupCompany']) ) {
				$groupCompany = $data['GroupCompany'];
				$groupCompanyFilter = array_filter($groupCompany);

				unset($data['GroupCompany']);

				// if( !empty($groupCompanyFilter) ) {
					$groupCompany = array_merge($groupCompany, array(
						'group_id' => $id,
						'user_id' => $user_company_id,
					));
					$data['GroupCompany'][]['GroupCompany'] = $groupCompany;
				// }
			}

			$this->controller->loadModel('Aro');
        	$this->controller->Aro->setDataSource('master');
        	
			$result = $this->controller->Group->doSave($data, $id, $user_company_id);
			$id = Common::hashEmptyField($result, 'id');
			$status = Common::hashEmptyField($result, 'status');

			if($status == 'success'){
				if( !empty($generate_aros) ) {
					$this->grantExtendRule('default');
					$this->RmCommon->manageAcl($id, 53, 'allow');
					// $this->_callUpdateAros($id);
				}
			}

			if(!empty($not_acl)){
				$url = array(
					'controller' => 'groups',
					'action' => 'index', 
				);
				if($user_company_id){
					$url[] = $user_company_id;
				}
			} else {
				$url = array(
					'controller' => 'acl',
					'action' => 'group_permissions', 
					$id,
					'user_id' => !empty($params_user_id) ? $params_user_id : null,
					'plugin' => 'acl_manager',
				);
			}

			$this->RmCommon->setProcessParams($result, $url);

		} else if( !empty($value) ) {
			$value = $this->controller->Group->getMergeList($value, array(
				'contain' => array(
					'GroupCompany' => array(
						'type' => 'first',
						'conditions' => array(
							'GroupCompany.user_id' => $user_company_id,
						),
					),
				)
			));

		 	$value = Hash::insert($value, 'Group.parent_id', Common::hashEmptyField($value, 'GroupCompany.parent_id', null));
			$data = $value;
		} else {
			$data['Group']['active'] = true;
		}


		$this->controller->request->data = $data;

		$parents = $this->controller->Group->getDivisionCompany(array(
			'userID' => $recordID,
			'slug' => $slug,
			'exlude' => $id,
			'is_parent' => false,
		));

		if(empty($params_user_id)){
			$this->controller->set(array(
				'self' => true,
			));
		}

		$this->controller->set(array(
			'value' => $value,
			'parents' => $parents,
		));
	}

	function getExtendRule(){
		$crmRole = array(
			'controllers/Crm/admin_attributes',
			'controllers/Crm/admin_change_status',
			'controllers/Crm/admin_follow_up',
			'controllers/Crm/admin_edit_followup',
			'controllers/Crm/admin_activity_edit',
			'controllers/Crm/admin_activity_delete',
			'controllers/Crm/admin_project_clients',
			'controllers/Crm/admin_project_contract',
			'controllers/Crm/admin_status',
			'controllers/Crm/admin_project_upload_documents',
			'controllers/Crm/admin_project_document',
			'controllers/Crm/admin_project_document_add',
			'controllers/Crm/admin_project_document_edit',
			'controllers/Crm/admin_project_document_delete',
			'controllers/Crm/admin_change_status',
			'controllers/Crm/admin_project_payment',
			'controllers/Crm/admin_project_payment_add',
			'controllers/Crm/admin_project_payment_edit',
			'controllers/Crm/admin_project_kpr',
			'controllers/Crm/admin_project_detail',
			'controllers/Crm/admin_cancel',
			'controllers/Crm/admin_submmission_nonaktif',
			'controllers/Crm/admin_project_submission',
			'controllers/Crm/admin_submission_nonaktif',
			'controllers/Crm/admin_updateKprComplete',
			'controllers/Crm/admin_detailView',
			'controllers/Crm/admin_project_load_more', 
		);
		$reportRole = array(
			'controllers/Reports/admin_detail',
			'controllers/Reports/admin_download',
		);

		return array(
			'controllers/Properties/admin_index' => array(
				'controllers/Properties/admin_info',
			),
			'controllers/Properties/admin_status_listing' => array(
				'controllers/Properties/admin_remove_status_category',
			),
			'controllers/Properties/admin_sell' => array(
				'controllers/Properties/admin_address',
				'controllers/Properties/admin_specification',
				'controllers/Properties/admin_documents',
				'controllers/Properties/admin_document_add',
				'controllers/Properties/admin_document_delete',
				'controllers/Properties/admin_document_edit',
				'controllers/Properties/admin_easy_add',
				'controllers/Properties/admin_easy_media',
				'controllers/Ajax/document_upload',
				'controllers/Properties/admin_medias',
				'controllers/Properties/admin_videos',
				'controllers/Ajax/property_photo',
				'controllers/Ajax/property_photo_primary',
				'controllers/Ajax/property_photo_title',
				'controllers/Ajax/property_photo_order',
				'controllers/Ajax/property_photo_delete',
			),
			'controllers/Properties/admin_edit' => array(
				'controllers/Properties/admin_edit_address',
				'controllers/Properties/admin_edit_specification',
				'controllers/Properties/admin_edit_documents',
				'controllers/Properties/admin_document_add',
				'controllers/Properties/admin_document_delete',
				'controllers/Properties/admin_document_edit',
				'controllers/Properties/admin_easy_preview',
				'controllers/Properties/admin_easy_media',
				'controllers/Ajax/document_upload',
				'controllers/Properties/admin_edit_medias',
				'controllers/Properties/admin_edit_videos',
				'controllers/Ajax/property_photo',
				'controllers/Ajax/property_photo_primary',
				'controllers/Ajax/property_photo_title',
				'controllers/Ajax/property_photo_order',
				'controllers/Ajax/property_photo_delete',
			),
			'controllers/Properties/admin_approval' => array(
				'controllers/Properties/admin_preview',
				'controllers/Properties/admin_rejected',
				'controllers/Ajax/admin_delete_media',
				'controllers/Ajax/admin_delete_video',
			),
			'controllers/Properties/admin_sold' => array(
				'controllers/Properties/admin_sold_preview',
			),
			'controllers/Properties/admin_market_trend' => array(
				'controllers/Properties/market_trend',
				'controllers/Properties/proprety_statistic',
			),
			'controllers/Crm/admin_projects' => $crmRole,
			// 'controllers/Crm/admin_project_add' => $crmRole,
			// 'controllers/Crm/admin_project_edit' => $crmRole,
			'controllers/Newsletters/admin_lists' => array(
				'controllers/Newsletters/admin_detail_lists',
				'controllers/Newsletters/admin_detail_list_clients',
				'controllers/Newsletters/admin_detail_list_users',
				'controllers/Newsletters/admin_preview_detail',
			),
			'controllers/Newsletters/admin_add_campaign' => array(
				'controllers/Newsletters/admin_template_campaign',
				'controllers/Newsletters/admin_content_campaign',
				'controllers/Newsletters/admin_summary_campaign',
				'controllers/Newsletters/admin_success_campaign',
			),
			'controllers/Newsletters/admin_replicate_campaign' => array(
				'controllers/Newsletters/admin_edit_campaign',
				'controllers/Newsletters/admin_edit_template_campaign',
				'controllers/Newsletters/admin_edit_content_campaign',
				'controllers/Newsletters/admin_edit_summary_campaign',
				'controllers/Newsletters/admin_success_campaign',
			),
			'controllers/Newsletters/admin_campaigns' => array(
				'controllers/Newsletters/admin_detail_email',
			),
			'controllers/Newsletters/admin_personals' => array(
				'controllers/Newsletters/admin_detail_email',
			),
			'controllers/Newsletters/admin_add_personal_email' => array(
				'controllers/Newsletters/admin_template_personal_email',
				'controllers/Newsletters/admin_content_personal_email',
				'controllers/Newsletters/admin_summary_personal_email',
				'controllers/Newsletters/admin_success_personal_email',
			),
			'controllers/Newsletters/admin_templates' => array(
				'controllers/Newsletters/admin_primary_birthday',
				'controllers/Newsletters/admin_preview_template',
				'controllers/Newsletters/admin_preview_template_detail',
				'controllers/Newsletters/admin_detail_list_users',
			),
			'controllers/Kpr/admin_index' => array(
				'controllers/Kpr/admin_application_detail',
				'controllers/Kpr/admin_notice_toggle',
				'controllers/Bank/admin_promos',
				'controllers/Bank/admin_promo_info',
			),
			'controllers/Kpr/admin_add' => array(
				'controllers/Kpr/admin_developer',
				'controllers/Kpr/admin_filing',
				'controllers/Kpr/admin_all_forward',
				'controllers/Kpr/admin_application',
				'controllers/Kpr/admin_application_detail',
				'controllers/Kpr/admin_application_detail_excel',
				'controllers/Kpr/admin_bank_list',
				'controllers/Kpr/admin_completed',
				'controllers/Kpr/admin_edit',
				'controllers/Kpr/admin_foward_application',
				'controllers/Kpr/admin_info',
				'controllers/Kpr/admin_notice_toggle',
				'controllers/Kpr/admin_resend_application',
				'controllers/Kpr/admin_update_kpr',
				'controllers/Kpr/admin_update_kpr_non_komisi',
				'controllers/Kpr/admin_options',
				'controllers/Kpr/ajax_compare_detail',
				'controllers/Kpr/backprocess_get_property',
				'controllers/Ajax/get_properties',
				'controllers/Ajax/backprocess_get_property',
				'controllers/Ajax/backprocess_get_bank',
			),
			'controllers/Payments/admin_index' => array(
				'controllers/Payments/admin_view',
				'controllers/Payments/admin_checkout',
			),
			'controllers/MembershipOrders/admin_add' => array(
				'controllers/MembershipOrders/admin_view',
				'controllers/MembershipOrders/admin_add',
				'controllers/MembershipOrders/admin_cancel',
			),
			'controllers/Settings/admin_theme_selection' => array(
				'controllers/Ajax/admin_theme',
				'controllers/Settings/admin_customizations',
			),
			'controllers/Messages/admin_index' => array(
				'controllers/Messages/admin_read',
				'controllers/Messages/admin_reply',
				'controllers/Messages/admin_filter',
				'controllers/Ajax/list_users',
			),
			'controllers/Users/admin_account' => array(
				'controllers/Users/admin_dashboard',
				'controllers/Users/admin_edit',
				'controllers/Users/admin_security',
			),
			'controllers/Users/admin_principles' => array(
				'controllers/Users/admin_info',
				'controllers/Users/admin_user_info',
				'controllers/Properties/admin_info',
				'controllers/Ebrosurs/admin_info',
				'controllers/Ebrosurs/admin_detail',
				'controllers/Groups/admin_index',
			),
			'controllers/Users/admin_user_info' => array(
				'controllers/Users/admin_info',
				// 'controllers/Users/admin_change_password',
			),
			'controllers/Groups/admin_checkall' => array(
				'controllers/Groups/admin_grant_toggle',
				'controllers/AclManagers/Acls/admin_group_permissions',
			),
			'controllers/Groups/admin_grant_toggle' => array(
				'controllers/Groups/admin_toggles',
			),
			'controllers/Users/admin_actived_agent' => array(
				'controllers/Groups/admin_inactived_agent',
			),
			'controllers/Users/admin_add' => array(
				'controllers/Users/backprocess_group_parent',
			),
			'controllers/Users/admin_edit_user' => array(
				'controllers/Users/backprocess_group_parent',
			),
			'controllers/Ebrosurs/admin_index' => array(
				'controllers/Ebrosurs/admin_detail',
				'controllers/Ebrosurs/admin_client_properties',
			),
			'controllers/Ebrosurs/admin_add' => array(
				'controllers/Ajax/list_company_properties',
				'controllers/Ajax/get_form_ebrosur',
				'controllers/Ebrosurs/admin_builder',
				'controllers/Ebrosurs/admin_generate',
				'controllers/Ebrosurs/admin_regenerate',
			),
			'controllers/Ebrosurs/admin_edit' => array(
				'controllers/Ajax/list_company_properties',
				'controllers/Ajax/get_form_ebrosur',
				'controllers/Ebrosurs/admin_builder',
				'controllers/Ebrosurs/admin_generate',
				'controllers/Ebrosurs/admin_regenerate',
			),
			'controllers/Ebrosurs/admin_request_ebrosurs' => array(
				'controllers/Ajax/change_request_ebrosur_period',
				'controllers/Ebrosurs/admin_api_agent_clients',
				'controllers/Ebrosurs/client_delete_multiple',
				'controllers/Ebrosurs/admin_mail',
				'controllers/Ebrosurs/admin_add_client',
				'controllers/Ebrosurs/admin_api_list_target_request',
				'controllers/Ebrosurs/admin_info',
				'controllers/Ebrosurs/client_add',
				'controllers/Ebrosurs/client_add_agent',
				'controllers/Ebrosurs/client_add_specification',
			),
			'controllers/Ebrosurs/admin_request_add' => array(
				'controllers/Ebrosurs/admin_add_specification',
				'controllers/Ebrosurs/admin_add_client',
				'controllers/Ebrosurs/admin_request_success',
			),
			'controllers/CoBrokes/backprocess_make_cobroke' => array(
				'controllers/CoBrokes/admin_brokers',
				'controllers/CoBrokes/admin_approval',
				'controllers/CoBrokes/admin_detail_property',
				'controllers/CoBrokes/admin_index',
				'controllers/CoBrokes/admin_listing',
				'controllers/CoBrokes/admin_me',
				'controllers/CoBrokes/admin_rejected',
				'controllers/CoBrokes/admin_request_cobroke',
				'controllers/CoBrokes/admin_revision_request',
				'controllers/CoBrokes/backprocess_approve',
				'controllers/CoBrokes/backprocess_approve_cobroke',
				'controllers/CoBrokes/backprocess_approve_revision',
				'controllers/CoBrokes/backprocess_delete_co_broke',
				'controllers/CoBrokes/backprocess_diapprove_revision',
				'controllers/CoBrokes/backprocess_edit_cobroke',
				'controllers/CoBrokes/backprocess_print',
				'controllers/CoBrokes/backprocess_stop_toggle',
				'controllers/CoBrokes/backprocess_unrejected',
			),
			'controllers/CoBrokes/admin_client_relation' => array(
				'controllers/CoBrokes/admin_client_related_agents'
			),
			'controllers/User/admin_clients' => array(
				'controllers/User/admin_client_info'
			),
			'controllers/Rules/admin_read_rules' => array(
				'controllers/Rules/search',
				'controllers/Rules/admin_search',

			),
			'controllers/Rules/admin_category_rules' => array(
				'controllers/Rules/admin_add_category_rules',
				'controllers/Rules/admin_delete_multiple_category_rules',
				'controllers/Rules/admin_edit_category_rules',

			),
			'controllers/Rules/admin_company_rules' => array(
				'controllers/Rules/admin_add_company_rules',
				'controllers/Rules/admin_edit_company_rules',
				'controllers/Rules/admin_delete_multiple_rules',
				'controllers/Rules/backprocess_ajax_list_subcategories',
				'controllers/Rules/admin_actived',

			),
			// 'controllers/CoBrokes/admin_index' => array(
			// 	'controllers/CoBrokes/admin_request_cobroke',
			// 	'controllers/CoBrokes/admin_detail_property',
			// ),
			'controllers/Reports/admin_overview' => array(
				'controllers/Users/admin_clients',
				'controllers/Reports/admin_top_agents',
				'controllers/Reports/admin_share',
				'controllers/Reports/admin_share_detail',
				'controllers/Reports/admin_share_detail_module',
			),
			'controllers/Reports/admin_kpi_marketing' => array(
				'controllers/Crm/admin_projects',
				'controllers/Reports/admin_crm',
				'controllers/Ebrosurs/admin_index',
			),
			'controllers/Reports/admin_overview_clients' => array(
				'controllers/Users/admin_agent_clients',
				'controllers/Users/admin_clients',
			),
			'controllers/Reports/admin_overview_kpr' => array(
				'controllers/Kpr/admin_index',
			),
			'controllers/Properties/admin_premium' => array(
				'controllers/Properties/admin_unpremium',
			),
		);
	}

	function grantExtendRule($type = false){

		switch ($type) {
			case 'default':
				$role = array(
					'53' => array(
						'controllers/Users/admin_account',
						'controllers/Users/admin_dashboard',
						'controllers/Users/admin_edit',
						'controllers/Users/admin_security',
						'controllers/ajax/get_properties',
						'controllers/ajax/get_crm_property',
						'controllers/ajax/list_users',
						'controllers/ajax/get_data_client',
						'controllers/reports/admin_generate',
					),
				);
				break;
				
			default:
				$role = $this->getExtendRule();
				break;

		}

		Configure::write('AclManager.grant_rule', $role);
		Configure::write('AclManager.grant_multiple', array(
			'controllers/Kpr/admin_application_detail' => array(
				'controllers/Kpr/admin_index',
				'controllers/Kpr/admin_add',
				'controllers/Kpr/admin_options',
			),
		));
	}

	function _callUpdateAros ( $id ) {
		$this->controller->loadModel('Group');
		$this->controller->loadModel('Aro');

		$this->Group = $this->controller->Group;
		$this->Aro = $this->controller->Aro;

		$value = $this->Group->findById($id);

		if( !empty($value) ) {
			$this->Group->create();
			$this->Group->id = $id;

			// Extracted from AclBehavior::afterSave (and adapted)
			$parent = $this->Group->parentNode();

			if (!empty($parent)) {
				$parent = $this->Group->node($parent, 'Aro');
			}

			$data = array(
				'parent_id' => isset($parent[0]['Aro']['id']) ? $parent[0]['Aro']['id'] : null,
				'model' => $this->Group->name,
				'foreign_key' => $this->Group->id
			);
			
			// Creating ARO
			$this->Aro->create($data);
			$this->Aro->save();
		}
	}

	function getSuperior($data = false, $options = array()){
		$recordID = Common::hashEmptyField($options, 'recordID');
		$group_id = Common::hashEmptyField($data, 'User.group_id');

		$group = $this->controller->User->Group->GroupCompany->getData('first', array(
			'conditions' => array(
				'GroupCompany.group_id' => $group_id,
				'GroupCompany.user_id' => $recordID,
			),
		));

		$group = $this->controller->User->Group->GroupCompany->getMergeList($group, array(
			'contain' => array(
				'Group' => array(
					'uses' => 'Group',
					'primaryKey' => 'id',
					'foreignKey' => 'parent_id',
				),
			),
		));
		if(!empty($group['Group'])){
			$parent_group_id = Common::hashEmptyField($group, 'Group.id');

			$userList = $this->controller->User->getData('list', array(
				'conditions' => array(
					'User.group_id' => $parent_group_id,
					'User.parent_id' => $recordID,
				),
				'fields' => array(
					'User.id', 'User.full_name',
				),
			));
			
			if($userList){
				$this->controller->set(array(
					'userList' => $userList, 
				));
			}
		}
	}
	
	function _callBeforeSaveTarget( $principle_id = null, $user_id = null ) {
		$data = $this->controller->request->data;
		$group_id = Configure::read('__Site.Global.Variable.Company.agent');

		if( empty($principle_id) ) {
			$principle_id = Configure::read('Principle.id');
		}

		if ( !empty($data) ) {
			$dataTarget = Common::hashEmptyField($data, 'GroupTarget');
			$dataSave = array();

			foreach ($dataTarget as $key => &$val) {
				$attribute_option_id = Common::hashEmptyField($val, 'attribute_option_id');
				
				$val['group_id'] = $group_id;
				$val['principle_id'] = $principle_id;

				if( !empty($user_id) ) {
					$val['user_id'] = $user_id;
				}

				if( !empty($attribute_option_id) ) {
					$dataSave[$attribute_option_id] = $val;
				} else {
					$dataSave[] = $val;
				}
			}

			$dataSave = array_values($dataSave);

			$result = $this->controller->Group->GroupTarget->doSave($dataSave, $group_id, $principle_id, $user_id);
			$params = $this->controller->params->params;
			$parent_params = Common::_callCompanyParamParentId($params);

			if( !empty($user_id) ) {
				$urlRedirect = array_merge(array(
					'controller' => 'groups',
					'action' => 'target_edit', 
					$user_id,
					'plugin' => false,
					'admin' => true,
				), $parent_params);
			} else {
				$urlRedirect = array(
					'controller' => 'groups',
					'action' => 'target', 
					'plugin' => false,
					'admin' => true,
				);
			}

			$this->RmCommon->setProcessParams($result, $urlRedirect);
		} else {
			if( !empty($user_id) ) {
				$values = $this->controller->Group->GroupTarget->getData('all', array(
					'conditions' => array(
						'GroupTarget.group_id' => $group_id,
						'GroupTarget.user_id' => $user_id,
					),
				));

				if( empty($values) ) {
					$values = $this->controller->Group->GroupTarget->getData('all', array(
						'conditions' => array(
							'GroupTarget.group_id' => $group_id,
							'GroupTarget.user_id' => 0,
						),
					));
				}
			} else {
				$values = $this->controller->Group->GroupTarget->getData('all', array(
					'conditions' => array(
						'GroupTarget.group_id' => $group_id,
						'GroupTarget.user_id' => 0,
					),
				));
			}

			if( !empty($values) ) {
				foreach ($values as $key => $value) {
					$attribute_option_id = Common::hashEmptyField($value, 'GroupTarget.attribute_option_id');

					$data['GroupTarget'][$attribute_option_id] = Common::hashEmptyField($value, 'GroupTarget');
				}
			}
		}

		$this->controller->request->data = $data;

		$title = __('Target Aktivitas Agen');
		$attributeOptions = $this->controller->Group->GroupTarget->AttributeOption->getData('list', false, array(
			'parent' => true,
		));

		$this->controller->set(array(
			'group_id' => $group_id,
			'principle_id' => $principle_id,
			'attributeOptions' => $attributeOptions,
			'module_title' => $title,
			'title_for_layout' => $title,
		));
	}
}
?>