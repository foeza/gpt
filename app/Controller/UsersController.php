<?php
App::uses('AppController', 'Controller');

class UsersController extends AppController {
	public $uses = array('User', 'SocialProfile');
	public $helpers = array(
		'FileUpload.UploadForm', 'User',
	);
	public $components = array(
		'RmImage', 'RmProperty', 'Captcha', 'RmRecycleBin', 'RmGroup', 'RmSetting',
		'Hybridauth', 
		'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
            	'backprocess_get_companies' => array(
	            	'extract' => array(
	                	'data',
	                ),
	            ),
	            'api_message' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'token',
				 	),
			 	),
	            'admin_login' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'token', 'data', 'company_data'
				 	),
			 	),
			 	'admin_logout' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'token',
				 	),
			 	),
	            'admin_admins' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'data',
				 	),
			 	),
	            'admin_change_password' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors',
				 	),
			 	),
	            'admin_edit_admin' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_add_admin' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_delete_multiple' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_agents' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'data'
				 	),
			 	),
	            'admin_edit_agent' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_add_agent' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_remove_agent' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors',
				 	),
			 	),
	            'admin_clients' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'data',
				 	),
			 	),
	            'admin_agent_clients' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'data',
				 	),
			 	),
	            'admin_edit_client' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_add_client' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_client_relation' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data',
				 	),
			 	),
	            'admin_delete_client_multiple' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_invite_client' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_client_agent_mapping_multiple' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
	            'admin_client_properties' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'data',
				 	),
			 	),
	            'admin_client_related_agents' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'data', 'agents',
				 	),
			 	),
			 	'admin_notifications' => array(
			 		'extract' => array(
				  		'paging', 'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'api_profile_photo' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'admin_edit' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
			 	'admin_security' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
			 	'admin_principles' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'paging', 'data'
				 	),
			 	),
			 	'admin_edit_principle' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'admin_add_principle' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'id', 'validationErrors',
				 	),
			 	),
			 	'admin_principle_company' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors', 'data'
				 	),
			 	),
			 	'admin_general_social_media' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors',
				 	),
			 	),
			 	'admin_list_principle_prime' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'api_notif' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'notification', 'message'
				 	),
			 	),
			 	'admin_forgotpassword' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
			 	'admin_redirect_notification' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data_redirect'
				 	),
			 	),
			 	'admin_company' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors', 'data'
				 	),
			 	),
			 	'admin_profession' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'support_data', 'data_user'
				 	),
			 	),
			 	'admin_social_media' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data', 'validationErrors',
				 	),
			 	),
			 	'admin_company_social_media' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data', 'validationErrors',
				 	),
			 	),
    		),
    	),
	);
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array(
			'register', 'admin_login', 'admin_forgotpassword', 'admin_redirect_notification',
			'verify', 'admin_logout', 
			'facebook_login', 'facebook_connect',
			'resend', 'agents', 'search', 'profile',
			'admin_password_reset', 'admin_remove_agent',
			'client_login', 'client_verify', 'client_forgotpassword',
			'client_password_reset', 'message',
			'companies', 'company', 'admin_verify', 'admin_verify_password', 
			'api_message', 'admin_security', 'admin_edit',
			'admin_notifications', 'api_notif', 'backprocess_get_companies',
			'redirect_home',
			'gauth',
			'login','add', 'social_login', 'social_endpoint', 
			'facebook_connect', 'admin_account',
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

	function client_search ( $action, $_client = true ) {
		$data = $this->request->data;
		$named = $this->RmCommon->filterEmptyField($this->params, 'named');
		$params = array(
			'action' => $action,
			'client' => $_client,
		);

		if( !empty($named) ) {
			$params = array_merge($params, $named);
		}

		$this->RmCommon->processSorting($params, $data);
	}

	function search ( $action = 'find', $addParam = false ) {
		$this->admin_search($action, $addParam);
	}

	public function admin_login() {
	//	user already logged in, redirect them to dashboard
		$user = $this->Auth->user();
		$params = $this->params->params;
		$token = Common::hashEmptyField($params, 'named.token');

		if($user && !$this->Rest->isActive() && empty($token)){
			$rest = $this->RmUser->_callAuthLoginRest($user);

			if( empty($rest) ) {
				$dashboardUrl = Configure::read('User.dashboard_url');
				$this->redirect($dashboardUrl);
			}
		}
		else{
			$pass_token = Common::_callPassToken($this->params->params);

			if ( !empty($this->request->data) || !empty($pass_token) ) {
				$data = $this->request->data;
				$this->User->set($data);

				if ($this->Auth->login()) {
					$user = $this->Auth->user();
					$rest = $this->RmUser->_callAuthLoginRest($user);

					if( empty($rest) ) {
						$groupID		= $this->Auth->user('group_id');
						$redirectURL	= $this->Auth->redirect();

						if( $groupID == 10 ) {
							$redirectURL = array(
								'controller' => 'ebrosurs',
								'action' => 'index',
								'client' => true,
							);
						}

					//	redirect user
						$this->redirect($redirectURL);
					}
				} else {
					$result = array(
						'msg' => __('Gagal melakukan login, username atau password Anda tidak valid'),
						'status' => 'error'
					);

					$this->RmCommon->setProcessParams($result);

					if( !empty($this->request->data['User']['password']) ) {
						unset($this->request->data['User']['password']);
					}
				}
			}

			$this->layout = 'login';
		}

		$status		= Common::hashEmptyField($this->params->query, 'status');
		$message	= Common::hashEmptyField($this->params->query, 'message');

		if($message){
			$this->Session->setFlash($message, 'flash_' . $status, null, $status);
		}

    	$this->set(compact(
			'user'
		));
	}

	public function admin_logout() {
		$user = Configure::read('User');

		$this->Auth->clear();

		// log activity
		$user_id = Common::hashEmptyField($user, 'data.id');
		$username = Common::hashEmptyField($user, 'data.username');

		if($user_id){
			$activity = __('%s telah melakukan logout', $username);
			$this->RmCommon->_saveLog($activity, $user,  $user_id);
		}

		$group_id = Common::hashEmptyField($user, 'group_id');	
		$url = $this->Auth->logout();

		if( $group_id == 10 ) {
			$url = array(
				'controller' => 'users',
				'action' => 'login',
				'client' => true,
			);
		}

	//	logout hybridauth session
		$this->Hybridauth->logout();

		$url = Router::url($url, true);

		if($this->params->query){
			$url = sprintf('%s?%s', $url, http_build_query($this->params->query));
		}

		return $this->redirect($url);
	}

    public function admin_account(){
		if( $this->RmCommon->_callIsDirector() ) {
			$this->redirect(array(
				'controller' => 'users',
				'action' => 'dashboard',
				'admin' => true,
			));
		} else {
			$is_admin = (Configure::read('User.companyAdmin'));
			$user_login_group_id = Configure::read('User.group_id');
			$isPersonalPage = Configure::read('Config.Company.is_personal_page');

			if( !empty($this->user_id) ) {
				$user = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $this->user_id
					),
				), array(
					'status' => 'semi-active',
				));

				$user = $this->User->getAllNeed($user, $this->user_id);

				$elements = array(
					'admin_mine' => true,
					'company' => false,
				);

				if(empty($isPersonalPage)){
					$total_kpr = $this->User->Kpr->getCountKpr($elements);	
				}
				else{
					$elements = array_merge($elements, array(
						'status' => 'active-pending', 
					));
				}

			//	atas vs ini, sekali hit dapet 2 info
				$this->User->Property->virtualFields = array('count' => 'COUNT(Property.id)');

				$propertyOptions = $this->User->Property->getData('paginate', array(
					'fields'	=> array('Property.featured', 'Property.count'), 
					'group'		=> array('Property.featured'), 
				), $elements);

				$propertyOptions	= Hash::remove($propertyOptions, 'order');
				$propertySumaries	= $this->User->Property->find('all', $propertyOptions);

				$total_regular_listing = Hash::extract($propertySumaries, '{n}.Property[featured=0].count');
				$total_premium_listing = Hash::extract($propertySumaries, '{n}.Property[featured=1].count');

				$total_regular_listing = $total_regular_listing ? array_shift($total_regular_listing) : 0;
				$total_premium_listing = $total_premium_listing ? array_shift($total_premium_listing) : 0;

				$total_listing = $total_regular_listing + $total_premium_listing;

				$this->RmCommon->_layout_file(array(
					'gchart',
					'dashboard',
				));

				$chartProperties = $this->User->Property->_callChartProperties();

				$percentage = $this->RmUser->getUserPercentageCompletion($this->user_id);

				$total_ebrosur = $this->User->UserCompanyEbrochure->getData('count', array(
					'order' => false,
				), array(
					'mine' => true,
				));

				if(empty($isPersonalPage)){
					// REPORT ON BELOW
					$top_ebrosurs = $this->User->UserCompanyEbrochure->_callTopEbrosurs();

					$total_listing_per_agent = $this->User->Property->get_total_listing_per_agent($this->parent_id, array(
						'admin_mine' => (empty($is_admin) && $user_login_group_id > 20) ? true : false,
					));

					$list_unpaid_provision = $this->User->Kpr->KprBank->KprBankInstallment->KprBankCommission->get_total_unpaid($user);

					// for commission purpose
					$chartCommission = $this->User->Property->_callChartProperties(false, 'commissions', false, false, array(
						'conditions' => array(),
						'contain' => array(
							'PropertySold',
						),
						'order' => array(
							'Property.id' => 'DESC',
						),
					));
				}

				$this->set('module_title', __('Dashboard'));
				$this->set('active_menu', 'dashboard');
				$this->set(compact(
					'total_listing_per_agent', 'total_listing_sold_per_agent', 
					'chartProperties', 'chartCommission', 'percentage',
					'total_ebrosur', 'user', 'total_premium_listing',
					'total_listing', 'top_ebrosurs', 'total_kpr',
					'list_unpaid_provision'
				));
			} else {
				$this->RmCommon->redirectReferer(__('Anda tidak memiliki akses terhadap konten tersebut.'));
			}
	    }
    }

    public function admin_dashboard(){
    	$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $this->user_id
			),
		), array(
			'status' => 'semi-active',
		));

		$user = $this->User->getAllNeed($user, $this->user_id);

    	// REPORT ON BELOW
    	$total_listing_per_agent = $this->User->Property->get_total_listing_per_agent($this->parent_id);
    	$total_ebrosur = $this->User->UserCompanyEbrochure->getData('count');

		$element = array(
			'admin_mine' => true,
			'company' => false,
		);
		$elementStatus = array(
			'status' => 'active-pending'
		);
		$elements = array_merge($element, $elementStatus);

		$total_premium_listing = $this->User->Property->getData('count', array(
			'conditions' => array(
				'Property.featured' => 1,
			)
		), $elements);
		$total_listing = $this->User->Property->getData('count', false, $elements);
		$total_kpr = $this->User->Kpr->getCountKpr($element);

    	$this->RmCommon->_layout_file(array(
			'gchart',
			'dashboard',
		));

		$chartProperties = $this->User->Property->_callChartProperties();
		$percentage = $this->RmUser->getUserPercentageCompletion($this->user_id);
		
    	$this->set('module_title', __('Dashboard'));
    	$this->set('active_menu', 'dashboard');
    	$this->set(compact(
			'total_listing_per_agent', 'chartProperties', 'percentage',
			'total_ebrosur', 'user', 'total_premium_listing',
			'total_listing', 'total_kpr'
		));

		$this->render('director_dashboard');
    }

	public function admin_edit (){
		$this->RmCommon->_callUserLogin();

		$module_title = __('Edit Profil');
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $this->user_id
			),
		), array(
			'status' => 'semi-active',
		));

		$user = $this->User->getMergeList($user, array(
			'contain' => array(
				'PropertyPremium' => array(
					'type' => 'count',
					'uses' => 'Property',
					'primaryKey' => 'id',
					'conditions' => array(
						'Property.featured' => true,
					),
				),
			),
		));

		if( !empty($user) ) {
			$group_id = $this->RmCommon->filterEmptyField($user, 'User', 'group_id');
			$user = $this->User->getAllNeed($user, $this->user_id, $group_id);

			$data = $this->request->data;

			if($data){
				$logoPath	= Configure::read('__Site.logo_photo_folder');
				$userID		= Common::hashEmptyField($user, 'User.id');
				$configID	= Common::hashEmptyField($user, 'UserConfig.id');

				$data = Hash::insert($data, 'UserConfig.id', $configID);
				$data = Hash::insert($data, 'UserConfig.user_id', $userID);

				$data = $this->RmImage->_uploadPhoto($data, 'UserConfig', 'logo', $logoPath);
			}

			$data = $this->RmUser->_callUserBeforeSave( $data, $user );
			$result = $this->User->doEdit( $this->user_id, $user, $data, false );

			if( $group_id == 2 ) {
				$redirect = array(
					'controller' => 'users',
					'action' => 'profession',
					'admin' => true,
				);
			} else if( in_array($group_id, array( 3,4,5 )) ) {
				$redirect = array(
					'controller' => 'users',
					'action' => 'company',
					'admin' => true,
				);
			} else {
				$redirect = false;
			}

			$this->RmCommon->setProcessParams($result, $redirect);

			$user_company = $this->User->UserCompany->getListParents();

			$this->RmCommon->_callRequestSubarea('UserProfile');
			$this->RmCommon->_callDataForAPI($user, 'manual');

			$subareaID = Common::hashEmptyField($this->data, 'UserProfile.subarea_id');

			if($subareaID){
				$location		= $this->RmCommon->getViewLocation($subareaID);
				$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

				$this->request->data['UserProfile']['location_name'] = $locationName;
			}

			$this->RmCommon->_layout_file(array(
				'fileupload',
				'ckeditor',
			));

			// check membership here, page admin_edit
			$opsi_validate = array(
				'cache_page' => 'properties_admin_index',
			);
			$packages = $this->RmSetting->validatePackageRKU($opsi_validate);

			$this->set(array(
				'user_company' => $user_company,
				'user' => $user, 
				'module_title' => $module_title,
				'_email' => false,
				'active_menu' => 'profile',
				'step_current' => 'profile',
				'packages' => $packages,
				'value' => $user,
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_profession (){
		$this->set('module_title', __('Informasi Profesi'));
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $this->user_id,
			),
		));

		$user = $this->User->getMergeList($user, array(
			'contain' => array(
				'PropertyPremium' => array(
					'type' => 'count',
					'uses' => 'Property',
					'primaryKey' => 'id',
					'conditions' => array(
						'Property.featured' => true,
					),
				),
			),
		));

		if( !empty($user) ) {
			$group_id = $this->RmCommon->filterEmptyField($user, 'User', 'group_id');
			$user = $this->User->UserConfig->getMerge($user, $this->user_id);
			$result = $this->User->doSaveProfession( $this->user_id, $user, $this->request->data);

			switch ($group_id) {
				case '2':
					$redirect = array(
						'controller' => 'users',
						'action' => 'social_media',
						'admin' => true,
					);
					break;
				
				default:
					$redirect = false;
					break;
			}
			$this->RmCommon->setProcessParams($result, $redirect);

    		$this->request->data = $this->User->UserClientType->getRequestData($this->request->data, $this->user_id);
    		$this->request->data = $this->User->UserPropertyType->getRequestData($this->request->data, $this->user_id);
    		$this->request->data = $this->User->UserSpecialist->getRequestData($this->request->data, $this->user_id);
    		$this->request->data = $this->User->UserLanguage->getRequestData($this->request->data, $this->user_id);
    		$this->request->data = $this->User->UserAgentCertificate->getRequestData($this->request->data, $this->user_id);

			$client_types = $this->User->UserClientType->ClientType->getList();
			$specialists = $this->User->UserSpecialist->Specialist->getList();
			$languages = $this->User->UserLanguage->Language->getList();
			$agent_certificates = $this->User->UserAgentCertificate->AgentCertificate->getList();
			$propertyTypes = $this->User->Property->PropertyType->getData('list', array(
	            'cache' => __('PropertyType.List'),
	        ));

			$opsi_validate = array(
				'cache_page' => 'properties_admin_index',
			);
			$packages = $this->RmSetting->validatePackageRKU($opsi_validate);

			if($this->Rest->isActive()){
				$client_types = $this->RmCommon->listToCakeArray($client_types);
				$specialists = $this->RmCommon->listToCakeArray($specialists);
				$languages = $this->RmCommon->listToCakeArray($languages);
				$agent_certificates = $this->RmCommon->listToCakeArray($agent_certificates);
				$propertyTypes = $this->RmCommon->listToCakeArray($propertyTypes);

				$this->set(array(
					'data_user' => $this->request->data,
					'support_data' => array(
						'client_types' => $client_types,
						'specialists' => $specialists, 
						'languages' => $languages,
						'agent_certificates' => $agent_certificates,
						'propertyTypes' => $propertyTypes,
					)
				));
			}else{
				$this->RmCommon->_layout_file('fileupload');
				$this->set(array(
					'active_menu' => 'profil',
					'step_current' => 'profession',
					'urlBack' => array(
		                'controller' => 'users',
		                'action' => 'edit',
		                'admin' => true,
		            ),
		            'client_types' => $client_types,
					'specialists' => $specialists, 
					'languages' => $languages,
					'agent_certificates' => $agent_certificates,
					'propertyTypes' => $propertyTypes,
					'packages' => $packages,
					'value' => $user,
				));
			}
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_social_media() {

		$module_title = __('Media Sosial');
		$user_id = $this->user_id;
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id
			),
		));

		$user = $this->User->getMergeList($user, array(
			'contain' => array(
				'PropertyPremium' => array(
					'type' => 'count',
					'uses' => 'Property',
					'primaryKey' => 'id',
					'conditions' => array(
						'Property.featured' => true,
					),
				),
			),
		));

		if( !empty($user) ) {
			$user = $this->User->UserConfig->getMerge( $user, $user_id);
			$result = $this->User->UserConfig->doEditSocialMedia( $user_id, $user, $this->request->data );

			$this->RmCommon->setProcessParams($result);
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}

		$opsi_validate = array(
			'cache_page' => 'properties_admin_index',
		);
		$packages = $this->RmSetting->validatePackageRKU($opsi_validate);
		
		$this->RmCommon->_callDataForAPI($user, 'manual');
		$this->RmCommon->_layout_file('fileupload');
		$this->set(array(
			'module_title' => $module_title,
			'user' => $user,
			'active_menu' => 'profil',
			'step_current' => 'social_media',
			'urlBack' => array(
                'controller' => 'users',
                'action' => 'profession',
                'admin' => true,
            ),
			'packages' => $packages,
			'value' => $user,
		));
	}

	public function admin_company_social_media() {

		$module_title = __('Media Sosial');
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $this->parent_id
			),
		));

		if( !empty($user) ) {
			$user = $this->User->UserConfig->getMerge( $user, $this->parent_id);
			$result = $this->User->UserConfig->doEditSocialMedia( $this->parent_id, $user, $this->request->data );

			$this->RmCommon->setProcessParams($result);
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}

		$this->RmCommon->_callDataForAPI($user, 'manual');
		$this->set(array(
			'user' => $user,
			'module_title' => $module_title,
			'step_current' => 'social_media',
			'_no_profile' => false,
			'urlBack' => array(
                'controller' => 'users',
                'action' => 'company',
                'admin' => true,
            ),
		));
	}

	public function admin_directors(){
		$module_title = __('Daftar Group');
		$options =  $this->User->_callRefineParams($this->params, array(
			'group' => array(
				'User.id',
			),
			'order' => array(
				'User.created' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));

		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->User->getData('paginate', $options, array(
			'status' => 'semi-active',
			'role' => 'director',
		));
		$values = $this->paginate('User');

		if( !empty($values) ){
			foreach($values as $key => &$value){
				$value = $this->User->getMergeList($value, array(
					'contain' => array(
						'UserCompanyConfig',
						'UserProfile',
						'UserConfig',
						'UserCompany',
						'PrincipalList' => array(
							'uses' => 'User',
							'primaryKey' => 'parent_id',
							'foreignKey' => 'id',
							'type' => 'list',
							'elements' => array(
								'status' => 'semi-active',
								'role' => 'principle',
							),
							'fields' => array(
								'User.id',
							),
						),
					),
				));

				$id = Common::hashEmptyField($value, 'User.id');
				$principleID = $this->RmCommon->filterEmptyField($value, 'PrincipalList');
				$value = $this->RmUser->_callGetLogView($id, $value);

				if( !empty($principleID) ) {
					$value['UserCount'] = $this->User->getUserList( 'count', array(
						'slug' => 'director',
						'userID' => $id,
					));
					$value['ClientCount'] = $this->RmUser->userClient(array(
						'principle_id' => $principleID,
					));
					$value['PrincipleCount'] = count($principleID);
					$value['PropertyCount'] = $this->User->Property->getData('count', array(
						'conditions' => array(
							'User.parent_id' => $principleID,
						),
						'contain' => array(
							'User',
						),
					), array(
						'status' => 'all',
						'company' => false,
					));
					$value['EbrosurCount'] = $this->User->UserCompanyEbrochure->getData('count', array(
						'conditions' => array(
							'User.parent_id' => $principleID,
						),
						'contain' => array(
							'User',
						),
					), array(
						'company' => false,
					));
					$value['divisionCount'] = $this->User->Group->getDivisionCompany(array(
						'userID' => $id,
						'slug' => 'director',
						'type' => 'count',
					));
				}
			}
		}

		$this->set('active_menu', 'director');
		$this->set(compact(
			'values', 'module_title'
		));
	}

	public function admin_add_director() {
		$module_title = __('Tambah Direktur');
		$step = 'Basic';

		$save_path = Configure::read('__Site.profile_photo_folder');
		$data = $this->request->data;
		$data = $this->RmUser->_callDataRegister($data);

		$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
            'keep_file_name' => true,
		));
		$result = $this->User->doAdd( $data, 0, false, 4 );

		$id = $this->RmCommon->filterEmptyField($result, 'id');
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'users',
			'action' => 'director_company',
			$id,
			'admin' => true,
		));

		$this->request->data = $this->RmCommon->_callUnset(array(
			'User' => array(
				'password',
				'password_confirmation',
			),
		), $this->request->data);

		$urlBack = array(
            'controller' => 'users',
            'action' => 'directors',
            'admin' => true,
        );

		$this->RmCommon->_callRequestSubarea('UserProfile');
		$this->RmCommon->_layout_file('ckeditor');
		$this->set('active_menu', 'director');
		$this->set(compact(
			'module_title', 'step', 'urlBack'
		));
	}

	public function admin_edit_director( $id = false ) {
		$module_title = __('Informasi Direktur');
		$step = 'Basic';
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id
			),
		), array(
			'status' => 'semi-active',
			'role' => 'director',
		));

		if( !empty($value) ) {
			$value = $this->User->UserProfile->getMerge( $value, $id );
			$value = $this->User->UserCompany->getMerge( $value, $id );
			$value = $this->User->UserConfig->getMerge( $value, $id );
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'User', 'photo');
			$save_path = Configure::read('__Site.profile_photo_folder');
			$data = $this->request->data;
			$data = $this->RmUser->_callDataRegister($data);

			$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));
			$data = $this->RmUser->_callUserBeforeSave( $data, $value );
			$result = $this->User->doEdit( $id, $value, $data );

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['User']['photo']['name'])){
				$uploadPhoto = $this->request->data['User']['photo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'director_company',
				$id,
				'admin' => true,
			));

			$this->RmCommon->_callRequestSubarea('UserProfile');

			$subareaID = Common::hashEmptyField($this->data, 'UserProfile.subarea_id');

			if($subareaID){
				$location		= $this->RmCommon->getViewLocation($subareaID);
				$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

				$this->request->data['UserProfile']['location_name'] = $locationName;
			}

			$this->RmCommon->_layout_file('ckeditor');
			$this->set(array(
				'id' => $id,
				'value' => $value,
				'module_title' => $module_title,
				'active_menu' => 'director',
				'step' => $step,
	            'urlCancel' => array(
	                'controller' => 'users',
	                'action' => 'directors',
	                'admin' => true,
	            ),
	            'user' => $value,
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_info($recordID = NULL){
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $recordID
			),
		), array(
			'status' => array(
				'active',
				'non-active',
			),
			'company' => true,
			'parent' => true,
			'admin' => true,
			'include_principle' => true,
		));

		if($value){
			$this->RmCommon->_callRefineParams($this->params);

			$histories = $this->RmUser->getUserHistory($recordID, array('duration' => true));

			$this->RmCommon->setCookieUser($value);

			$parent_id = Common::hashEmptyField($value, 'User.parent_id');

			$user = $this->RmUser->getUser();
			$principle_group_id = Common::hashEmptyField($user, 'Group.id');
			$groupName = Common::hashEmptyField($user, 'Group.name', false, array(
				'type' => 'strtolower',
			));

			$active_menu = $this->RmUser->getActive($groupName, 'user');

			$value = $this->User->getMergeList($value, array(
				'contain' => array(
					'Parent' => array(
						'uses' => 'User',
						'primaryKey' => 'id',
						'foreignKey' => 'superior_id',
						'contain' => array(
							'UserProfile'
						),
					),
					'Group',
					'UserProfile' => array(
						'contain' => array(
							'Region' => array(
								'cache' => true,
							),
							'City' => array(
								'cache' => true,
							),
							'Subarea' => array(
								'cache' => array(
									'name' => 'Subarea',
									'config' => 'subareas',
								),
							),
						),
					),
					'UserCompany' => array(
						'contain' => array(
							'Region' => array(
								'cache' => true,
							),
							'City' => array(
								'cache' => true,
							),
							'Subarea' => array(
								'cache' => array(
									'name' => 'Subarea',
									'config' => 'subareas',
								),
							),
						),
					),
					'UserCompanyConfig',
					'PropertyPremium' => array(
						'type' => 'count',
						'uses' => 'Property',
						'primaryKey' => 'id',
						'conditions' => array(
							'Property.featured' => true,
						),
					),
				),
			));

			$group_title = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');
			$group_title = $this->RmCommon->filterEmptyField($value, 'Group', 'name', $group_title);

			$title = __('Daftar %s', $group_title);
			$group_id = $this->RmCommon->filterEmptyField($value, 'Group', 'id');

			switch ($group_id) {
				case '1':
					$urlBack = array(
						'controller' => 'users',
						'action' => 'non_companies',
						'admin' => true,
					);
					break;
				case '4':
					$urlBack = array(
						'controller' => 'users',
						'action' => 'directors',
						'admin' => true,
					);
					break;
				case '3':
					$urlBack = array(
						'controller' => 'users',
						'action' => 'principles',
						'admin' => true,
					);
					break;
				default:

					$logged_group = Configure::read('User.group_id');

					$urlBack = array(
						'controller' => 'users',
						'action' => 'user_info',
						'admin' => true,
					);

					if($principle_group_id <> 3 || in_array($logged_group, array(20, 4))){
						$urlBack[] = $parent_id;
					}

					break;
			}

			$opsi_validate = array(
				'cache_page' => 'properties_admin_index',
			);
			$packages = $this->RmSetting->validatePackageRKU($opsi_validate);

			$this->RmUser->_callRoleActiveMenu($value);
			$this->set(array(
				'module_title' => $title,
				'title_for_layout' => $title,
				'value' => $value,
				'currUser' => $value,
				'recordID' => $recordID,
				'urlBack' => $urlBack,
				'active_menu' => $active_menu,
				'parent_id' => $parent_id,
				'getCookieId' => $this->RmCommon->getCookieUser(),
				'packages' => $packages,
				'histories' => $histories, 
			));
		}
		else{
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_info_principles($recordID = NULL){
		$title = __('Daftar Principal');

		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $recordID
			),
		), array(
			'status' => false,
			'company' => true,
			'admin' => true,
			'role' => 'director',
		));

		if( !empty($value) ) {
			$email = Common::hashEmptyField($value, 'User.email');
			$value = $this->User->getMergeList($value, array(
				'contain' => array(
					'Group',
				),
			));

			$values = $this->RmUser->_callBeforeViewPrinciples(array(
				'conditions' => array(
					'User.parent_id' => $recordID,
				),
			));

			$this->RmUser->_callRoleActiveMenu($value);

			$this->set(array(
				'module_title' => $title,
				'title_for_layout' => $title,
				'currUser' => $value,
				'values' => $values,
				'recordID' => $recordID,
				'active_tab' => 'Principal',
				'urlBack' => array(
					'controller' => 'users',
					'action' => 'directors',
					'admin' => true,
				),
				'urlAddPrinciple' => array(
	            	'controller' => 'users',
		            'action' => 'add_principle',
		            'director' => $email,
		            'admin' => true,
            	),
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_info_agents($recordID = NULL){
		$title = __('Daftar Agen');

		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $recordID
			),
		), array(
			'company' => true,
			'parent' => true,
			'status' => false,
			'admin' => true,
			'role' => array(
				'director', 'principle',
			),
		));

		if( !empty($value) ) {
			$value = $this->User->getMergeList($value, array(
				'contain' => array(
					'Group',
				),
			));

			$this->RmUser->_callRoleActiveMenu($value);
			$options = $this->RmUser->_callRoleCondition($value);
			$values = $this->RmUser->_callBeforeViewAgents($options);

			$divisiOptions = $this->User->Group->getDivisionCompany(array(
				'userID' => $recordID,
			));

			$this->set(array(
				'module_title' => $title,
				'title_for_layout' => $title,
				'divisiOptions' => $divisiOptions,
				'values' => $values,
				'currUser' => $value,
				'recordID' => $recordID,
				'active_tab' => 'Agen',
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

	public function admin_director_company( $id = false ) {
		$module_title = __('Profil Perusahaan');
		$this->RmCommon->_layout_file(array(
			'map',
			'ckeditor',
		));

		$step = 'Company';
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id
			),
		), array(
			'status' => 'semi-active',
			'role' => 'director',
		));

		if( !empty($value) ) {
			$value = $this->User->UserCompany->getMerge( $value, $id );
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'logo');
			$company_id = $this->RmCommon->filterEmptyField( $value, 'UserCompany', 'id' );

			$data = $this->request->data;
			$save_path = Configure::read('__Site.logo_photo_folder');

			$data = $this->RmImage->_uploadPhoto( $data, 'UserCompany', 'logo', $save_path );
			$data = $this->RmUser->_callUserCompanyBeforeSave( $data, $value );
			$result = $this->User->UserCompany->doSave( $id, $value, $data, $company_id );

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['UserCompany']['logo']['name'])){
				$uploadPhoto = $this->request->data['UserCompany']['logo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'general_social_media',
				$id,
				'admin' => true,
			));

			$urlBack = array(
	            'controller' => 'users',
	            'action' => 'edit_director',
	            $id,
	            'admin' => true,
	        );

			$this->RmCommon->_callRequestSubarea('UserCompany');

			$subareaID = Common::hashEmptyField($this->data, 'UserCompany.subarea_id');

			if($subareaID){
				$location		= $this->RmCommon->getViewLocation($subareaID);
				$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

				$this->request->data['UserCompany']['location_name'] = $locationName;
			}

			$this->set(array(
				'id' => $id,
				'value' => $value,
				'module_title' => $module_title,
				'active_menu' => 'director',
				'step' => $step,
				'urlBack' => $urlBack,
	            'urlCancel' => array(
	                'controller' => 'users',
	                'action' => 'directors',
	                'admin' => true,
	            ),
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}	
	}

	public function admin_principles(){
		$module_title = __('Daftar Principal');
		$values = $this->RmUser->_callBeforeViewPrinciples();
		$this->set('active_menu', 'principal');
		$this->set(compact(
			'values', 'module_title'
		));

		$this->RmCommon->_callDataForAPI($values, 'manual');

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	public function admin_view_principle($recordID = NULL){
		$module_title	= __('Informasi Principal');
		$record			= $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $recordID
			),
		));

		if($record){
			$record			= $this->User->UserCompany->getMerge($record, $recordID);
			$companyConfig	= $this->User->UserCompanyConfig->getData('first', array(
				'conditions' => array(
					'UserCompanyConfig.user_id' => $recordID
				)
			));

			if(empty($companyConfig)){
				$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
				$companyName	= $this->RmCommon->filterEmptyField($record, 'UserCompany', 'name');

				$this->RmCommon->setCustomFlash(__(sprintf('Website Principal <b>%s</b> (<b>%s</b>) belum dikonfigurasi, mohon lakukan konfigurasi terlebih dahulu atau hubungi Adminisrator', $fullName, $companyName)), 'error');
				$this->redirect(array('action' => 'principles', 'admin' => TRUE));
			}
			else{
				$this->loadModel('MembershipPackage');
				$packageID	= $this->RmCommon->filterEmptyField($companyConfig, 'UserCompanyConfig', 'membership_package_id');
				$package	= $this->MembershipPackage->getData('first', array(
					'conditions' => array(
						'MembershipPackage.id' => $packageID
					)
				));

				$record		= array_merge($record, $companyConfig, $package);
				$record		= $this->User->UserProfile->getMerge($record, $recordID);
				$params		= $this->params->named;
				$sendEmail	= $this->RmCommon->filterEmptyField($params, 'send_notification');

				if($params && $sendEmail){
				//	send email notifikasi ke principal
					$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
					$companyName	= $this->RmCommon->filterEmptyField($record, 'UserCompany', 'name');
					$email			= $this->RmCommon->filterEmptyField($record, 'User', 'email');
					$subject		= 'Reminder Paket Membership Pro';
					$template		= 'renewal_notification_principal';
					$emailData		= array_merge(
						$record, 
						array(
						//	'debug' => 'view'
						)
					);

					$isSent	= $this->RmCommon->sendEmail($fullName, $email, $template, $subject, $emailData);
					$status	= $isSent ? 'Berhasil' : 'Gagal';
					$class	= $isSent ? 'success' : 'error';

					$this->RmCommon->setCustomFlash(__(sprintf('%s mengirimkan email notifikasi ke Principal <b>%s</b> (<b>%s</b>)', $status, $fullName, $companyName)), $class);
					$this->redirect(array('action' => 'principles', 'admin' => TRUE));
				}

				$this->set('active_menu', 'principal');
				$this->set(compact('module_title', 'record'));
			}
		}
		else{
			$this->RmCommon->redirectReferer(__('Principal tidak ditemukan'));
			$this->redirect(array('action' => 'principles', 'admin' => TRUE));
		}
	}

	public function admin_add_principle() {
		$module_title = __('Tambah Principal');
		$step = 'Basic';

		$save_path = Configure::read('__Site.profile_photo_folder');
		$data = $this->request->data;
		$data = $this->RmUser->_callDataRegister($data);

		$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
            'keep_file_name' => true,
		));
		$result = $this->User->doAdd( $data, 0, false, 3 );

		$status = $this->RmCommon->filterEmptyField($result, 'status');
		$id = $this->RmCommon->filterEmptyField($result, 'id');

		if($this->Rest->isActive() && $status == 'success' && !empty($id)){
			$this->request->data = array();

			$this->admin_edit_principle($id);
		}

		$id = $this->RmCommon->filterEmptyField($result, 'id');
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'users',
			'action' => 'principle_company',
			$id,
			'admin' => true,
		));

		$this->request->data = $this->RmCommon->_callUnset(array(
			'User' => array(
				'password',
				'password_confirmation',
			),
		), $this->request->data);

		$urlBack = array(
            'controller' => 'users',
            'action' => 'principles',
            'admin' => true,
        );

		$this->RmCommon->_callRequestSubarea('UserProfile');
		$this->RmCommon->_layout_file('ckeditor');

		$this->set(array(
			'id' => $id,
			'step' => $step,
			'urlBack' => $urlBack,
			'active_menu' => 'principal',
			'module_title' => $module_title,
		));
	}

	public function admin_edit_principle( $id = false ) {
		$module_title = __('Informasi Principal');
		$step = 'Basic';
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id
			),
		), array(
			'company' => true,
			'admin' => true,
		));

		if( !empty($value) ) {
			$urlBack = array(
	            'controller' => 'users',
	            'action' => 'principles',
	            'admin' => true,
	        );
			$save_path = Configure::read('__Site.profile_photo_folder');
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'User', 'photo');
			$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

			$value = $this->User->UserProfile->getMerge( $value, $id );
			$value = $this->User->UserCompany->getMerge( $value, $id );
			$value = $this->User->UserConfig->getMerge( $value, $id );
			$value = $this->User->getMerge( $value, $parent_id, false, 'Parent' );

			if($this->Rest->isActive()){
				$value = $this->RmUser->getCounterPrinciple( $value );
			}

			$data = $this->request->data;

			if( !empty($data) ) {
				$data['User']['group_id'] = 3;
				$data = $this->RmUser->_callDataRegister($data);
				$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
		            'keep_file_name' => true,
				));
			}

			$data = $this->RmUser->_callUserBeforeSave( $data, $value );

			$result = $this->User->doEdit( $id, $value, $data );

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['User']['photo']['name'])){
				$uploadPhoto = $this->request->data['User']['photo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'principle_company',
				$id,
				'admin' => true,
			));

			$this->request->data = $this->RmUser->_callBeforeView($this->request->data);

			$this->RmCommon->_callRequestSubarea('UserProfile');

			$subareaID = Common::hashEmptyField($this->data, 'UserProfile.subarea_id');

			if($subareaID){
				$location		= $this->RmCommon->getViewLocation($subareaID);
				$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

				$this->request->data['UserProfile']['location_name'] = $locationName;
			}


			$status = $this->RmCommon->filterEmptyField($result, 'status');

			if($this->Rest->isActive() && $status == 'success'){
				$this->request->data = array();
				$this->admin_edit_principle($id);
			}else{
				$this->RmCommon->_callDataForAPI($value, 'manual');

				$this->RmCommon->_layout_file('ckeditor');
				$this->set(array(
					'id' => $id,
					'value' => $value,
					'module_title' => $module_title,
					'active_menu' => 'principal',
					'step' => $step,
		            'urlCancel' => array(
		                'controller' => 'users',
		                'action' => 'principles',
		                'admin' => true,
		            ),
		            'user' => $value,
				));
			}
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_principle_company( $id = false ) {
		$params = $this->params->params;
		$user = $this->RmUser->getUser($id);

		$module_title = __('Profil Perusahaan');
		$this->RmCommon->_layout_file(array(
			'map',
			'ckeditor',
		));

		$step = 'Company';
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id
			),
		), array(
			'company' => true,
			'admin' => true,
		));

		if( !empty($value) ) {
			$value = $this->User->UserCompany->getMerge( $value, $id );
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'logo');
			$company_id = $this->RmCommon->filterEmptyField( $value, 'UserCompany', 'id' );

			$data = $this->request->data;
			$save_path = Configure::read('__Site.logo_photo_folder');

			$data = $this->RmImage->_uploadPhoto( $data, 'UserCompany', 'logo', $save_path );
			$data = $this->RmUser->_callUserCompanyBeforeSave( $data, $value );
			$result = $this->User->UserCompany->doSave( $id, $value, $data, $company_id );

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['UserCompany']['logo']['name'])){
				$uploadPhoto = $this->request->data['UserCompany']['logo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			if(!empty($result['status']) && $result['status'] == 'success'){
				$value = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $id
					),
				), array(
					'company' => true,
					'admin' => true,
				));

				$value = $this->User->UserCompany->getMerge( $value, $id );
			}

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'general_social_media',
				$id,
				'admin' => true,
			));

			$urlBack = array(
	            'controller' => 'users',
	            'action' => 'edit_principle',
	            $id,
	            'admin' => true,
	        );

			$this->RmCommon->_callRequestSubarea('UserCompany');

			$subareaID = Common::hashEmptyField($this->data, 'UserCompany.subarea_id');

			if($subareaID){
				$location		= $this->RmCommon->getViewLocation($subareaID);
				$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

				$this->request->data['UserCompany']['location_name'] = $locationName;
			}

			if($this->Rest->isActive()){
				$this->RmCommon->_callDataForAPI($value, 'manual');
			}

			$groupName = Common::hashEmptyField($user, 'Group.name', false, array(
				'type' => 'strtolower',
			));

			$active_menu = $this->RmUser->getActive($groupName, 'user');

			$this->set(array(
				'id' => $id,
				'value' => $value,
				'module_title' => $module_title,
				'active_menu' => $active_menu,
				'step' => $step,
				'urlBack' => $urlBack,
	            'urlCancel' => array(
	                'controller' => 'users',
	                'action' => 'principles',
	                'admin' => true,
	            ),
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_general_social_media( $id = false ) {
		$params = $this->params->params;
		$principle = $this->RmUser->getUser($id);

		$module_title = __('Media Sosial');
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
			),
		), array(
			'company' => true,
			'admin' => true,
			'status' => 'semi-active',
			'role' => array(
				'principle',
				'director',
			),
		));

		if( !empty($user) ) {
			$group_id = $this->RmCommon->filterEmptyField($user, 'User', 'group_id');
			$user = $this->User->UserConfig->getMerge( $user, $id);
			$result = $this->User->UserConfig->doEditSocialMedia( $id, $user, $this->request->data );

			switch ($group_id) {
				case '4':
					$urlBack = array(
		                'controller' => 'users',
		                'action' => 'director_company',
		                $id,
		                'admin' => true,
		            );
					$urlCancel = array(
		                'controller' => 'users',
		                'action' => 'directors',
		                'admin' => true,
		            );
					break;
				
				default:
					$urlBack = array(
		                'controller' => 'users',
		                'action' => 'principle_company',
		                $id,
		                'admin' => true,
		            );
					$urlCancel = array(
		                'controller' => 'users',
		                'action' => 'principles',
		                'admin' => true,
		            );
					break;
			}

			$this->RmCommon->setProcessParams($result);

			$groupName = Common::hashEmptyField($principle, 'Group.name', false, array(
				'type' => 'strtolower',
			));

			$active_menu = $this->RmUser->getActive($groupName, 'user');


			$this->set(array(
				'id' => $id,
				'user' => $user,
				'module_title' => $module_title,
				'active_menu' => $active_menu,
				'step' => 'social_media',
				'_no_profile' => false,
				'urlBack' => $urlBack,
	            'urlCancel' => $urlCancel,
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_agents(){
		$module_title = __('Daftar Agen');
		$values = $this->RmUser->_callBeforeViewAgents();

		$this->set('active_menu', 'agent');
		$this->set(compact(
			'values', 'module_title'
		));

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	public function admin_add_agent() {
		$agents = $this->User->getAgents($this->parent_id, true, 'count', false, array('role' => 'agent'));
		$access = $this->RmCommon->getAccessPage($this->data_company, 'max_agent', $agents );
		$parent_name = $this->RmCommon->filterEmptyField($this->data_company, 'User', 'full_name');

		if($access){
			$module_title = __('Tambah Agen');

			$save_path = Configure::read('__Site.profile_photo_folder');
			$data = $this->request->data;
			$data = $this->RmUser->_callDataRegister($data);

			$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));
			$result = $this->User->doAdd( $data, $this->parent_id, $parent_name );
			
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$id = $this->RmCommon->filterEmptyField($result, 'id');

			if($this->Rest->isActive() && $status == 'success' && !empty($id)){
				$this->request->data = array();
				
				$this->admin_edit_agent($id);
			}

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'agents',
				'admin' => true,
			));

			$urlBack = array(
	            'controller' => 'users',
	            'action' => 'agents',
	            'admin' => true,
	        );

			$this->request->data = $this->RmCommon->_callUnset(array(
				'User' => array(
					'password',
					'password_confirmation',
				),
			), $this->request->data);

			$this->RmCommon->_callRequestSubarea('UserProfile');
			$this->set('active_menu', 'agent');
			$this->set(compact(
				'module_title', 'urlBack'
			));
		}else{
			$this->RmCommon->redirectReferer(__('Anda telah mencapai batas pembuatan agen, silahkan lakukan upgrade paket untuk dapat menambah agen kembali.'));
		}
	}

	public function admin_edit_user( $id = false ){
		$save_path = Configure::read('__Site.profile_photo_folder');
		$params = $this->params->params;

		$user_id = Common::hashEmptyField($params, 'named.user_id');
		$user = $this->RmUser->getUser($user_id);
		$recordID = Common::hashEmptyField($user, 'User.id');
		$principle_group_id = Common::hashEmptyField($user, 'User.group_id');

		$_isAdmin = Configure::read('User.Admin.Rumahku');

		if(!empty($user)){
			$module_title = __('Edit User');
			$value = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $id,
					'User.parent_id' => $recordID,
				),
			), array(
				'status' => array(
					'non-active',
					'active',
				),
			));

			if(!empty($value)){
				$group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');
				$oldPhoto = $this->RmCommon->filterEmptyField($value, 'User', 'photo');

				$value = $this->User->getAllNeed( $value, $id );
				$value = $this->User->getMerge( $value, $recordID, false, 'Parent' );

				$data = $this->request->data;
				$data = $this->RmUser->_callDataRegister($data);

				if( !empty($data) ) {
					$data['User']['status'] = 1;
				}

				$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
		            'keep_file_name' => true,
				));
				$data = $this->RmUser->_callUserBeforeSave( $data, $value );
				$result = $this->User->doEdit( $id, $value, $data, true, $recordID);
				//	if user upload new photo, delete old photo
				if(isset($this->request->data['User']['photo']['name'])){
					$uploadPhoto = $this->request->data['User']['photo']['name'];

					if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
						$permanent = FALSE;
						$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
					}
				}

				$url = array(
					'controller' => 'users',
					'action' => 'user_info',
					'admin' => true,
				);

				if(empty($user_id)){
					$this->set(array(
						'self' => true,
					));
				} else {
					$url[] = $recordID;
				}

				$this->RmCommon->setProcessParams($result, $url);

				$this->request->data = $this->RmUser->_callBeforeView($this->request->data, $value);

				$this->RmCommon->_callRequestSubarea('UserProfile');

				$subareaID = Common::hashEmptyField($this->data, 'UserProfile.subarea_id');

				if($subareaID){
					$location		= $this->RmCommon->getViewLocation($subareaID);
					$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

					$this->request->data['UserProfile']['location_name'] = $locationName;
				}

				if(!empty($result['status']) && $result['status'] == 'success'){
					$value = $this->User->getData('first', array(
						'conditions' => array(
							'User.id' => $id,
						),
					), array(
						'role' => 'agent',
						'status' => 'semi-active',
						'company' => true,
						'admin' => true,
					));

					$recordID = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

					$value = $this->User->getAllNeed( $value, $id );
					$value = $this->User->getMerge( $value, $recordID, false, 'Parent' );
				} else {
					$this->RmGroup->getSuperior( $this->request->data, array(
						'recordID' => $recordID,
					));
				}

				$this->RmCommon->_callDataForAPI($value, 'manual');
				$groupName = Common::hashEmptyField($user, 'Group.name', false, array(
					'type' => 'strtolower'
				));

				// view
				$active_menu = $this->RmUser->getActive($groupName, 'user');

				$groups = $this->User->Group->getDivisionCompany(array(
					'userID' => Common::hashEmptyField($user, 'User.id'),
					'slug' => $groupName,
				));

				// call data package membership rku
				$opsi_link = array(
					'user_own' => false,
				);
				$packages = $this->RmSetting->callDataMembershipRKU($opsi_link);

				$this->set(array(
					'user' => $value,
					'groups' => $groups,
					'recordID' => $recordID,
					'module_title' => $module_title,
					'active_menu' => $active_menu,
					'packages' => $packages,
					'edit' => true,
				));

				if($principle_group_id == 3){

					if ($_isAdmin) {
						$this->set(array(
							'access_membership_rku' => true,
						));
					}

					$this->set(array(
						'user_type' => 'user',
					));
				}

				$this->render('admin_add_admin');
			} else {
				$this->RmCommon->redirectReferer(__('Agen tidak ditemukan'));
			}
		}
	}

	public function admin_edit_agent ( $id = false ) {
		$save_path = Configure::read('__Site.profile_photo_folder');

		$module_title = __('Edit Agen');
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
			),
		), array(
			'status' => 'semi-active',
			'company' => true,
			'admin' => true,
		));

		if( !empty($value) ) {
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'User', 'photo');
			$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

			$value = $this->User->getAllNeed( $value, $id );
			$value = $this->User->getMerge( $value, $parent_id, false, 'Parent' );

			$data = $this->request->data;
			$data = $this->RmUser->_callDataRegister($data);

			if( !empty($data) ) {
				$data['User']['status'] = 1;
			}

			$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));
			$data = $this->RmUser->_callUserBeforeSave( $data, $value );
			$result = $this->User->doEdit( $id, $value, $data);

			//	if user upload new photo, delete old photo
			if(isset($this->request->data['User']['photo']['name'])){
				$uploadPhoto = $this->request->data['User']['photo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'agents',
				'admin' => true,
			));

			$this->request->data = $this->RmUser->_callBeforeView($this->request->data);

			$this->RmCommon->_callRequestSubarea('UserProfile');

			$subareaID = Common::hashEmptyField($this->data, 'UserProfile.subarea_id');

			if($subareaID){
				$location		= $this->RmCommon->getViewLocation($subareaID);
				$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

				$this->request->data['UserProfile']['location_name'] = $locationName;
			}

			if(!empty($result['status']) && $result['status'] == 'success'){
				$value = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $id,
					),
				), array(
					'role' => 'agent',
					'status' => 'semi-active',
					'company' => true,
					'admin' => true,
				));

				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

				$value = $this->User->getAllNeed( $value, $id );
				$value = $this->User->getMerge( $value, $parent_id, false, 'Parent' );
			}

			$this->RmCommon->_callDataForAPI($value, 'manual');

			$this->set('active_menu', 'agent');
			$this->set(array(
				'user' => $value, 
				'module_title' => $module_title,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Agen tidak ditemukan'));
		}
	}

	public function admin_delete_multiple() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'User', 'id');

    	$result = $this->User->doToggle( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    public function admin_delete_client_multiple() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'User', 'id');

    	$result = $this->User->UserClient->doToggle( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

	public function admin_company() {
		
		$module_title = __('Profil Perusahaan');
		$this->RmCommon->_layout_file(array(
			'map',
			'ckeditor',
		));

		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $this->parent_id,
			),
		));

		if( !empty($user) ) {
			$group_id 	= $this->RmCommon->filterEmptyField($user, 'User', 'group_id');
			$user		= $this->User->UserCompany->getMerge($user, $this->parent_id);
			$company_id	= $this->RmCommon->filterEmptyField($user, 'UserCompany', 'id');
			$oldPhoto	= $this->RmCommon->filterEmptyField($user, 'UserCompany', 'logo');

			$data = $this->request->data;

			$save_path = Configure::read('__Site.logo_photo_folder');
			$data = $this->RmImage->_uploadPhoto( $data, 'UserCompany', 'logo', $save_path );
			$data = $this->RmUser->_callUserCompanyBeforeSave( $data, $user );
			$result = $this->User->UserCompany->doSave( $this->parent_id, $user, $data, $company_id );

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['UserCompany']['logo']['name'])){
				$uploadPhoto = $this->request->data['UserCompany']['logo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			if( in_array($group_id, array( 3,4,5 )) ) {
				$redirect = array(
					'controller' => 'users',
					'action' => 'company_social_media',
					'admin' => true,
				);
			} else {
				$redirect = false;
			}

			if(!empty($result['status']) && $result['status'] == 'success'){
				$user = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $this->parent_id,
					),
				));

				$user = $this->User->UserCompany->getMerge($user, $this->parent_id);
			}

			$this->RmCommon->setProcessParams($result, $redirect);
			$this->RmCommon->_callRequestSubarea('UserCompany');

			$this->RmCommon->_callDataForAPI($user, 'manual');

			$this->set(array(
				'module_title' => $module_title,
				'step_current' => 'company',
				'urlBack' => array(
	                'controller' => 'users',
	                'action' => 'edit',
	                'admin' => true,
	            ),
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}	
	}

	// Baru Fungsi saja
	// Jangan di hapus
	public function admin_photo() {
		$this->RmCommon->_layout_file('fileupload');
	}

	// Baru Fungsi saja
	// Jangan di hapus
	public function edit_company_logo() {

		$this->RmCommon->_layout_file('fileupload');
	}

	public function admin_photo_crop() {
		$urlRedirect = array(
            'controller' => 'users',
            'action' => 'edit',
            'admin' => true,
        );
		$save_path = Configure::read('__Site.profile_photo_folder');

		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $this->user_id,
			),
		));

		if( !empty($user) ) {
			if( !empty($this->request->data) ) {
				$data = $this->request->data;

				$paramPhoto = $this->RmImage->_callDataPosition($data, 'User');
				$photoName = $this->RmImage->cropPhoto($paramPhoto, $save_path);

				$result = $this->User->doCroppedPhoto( $this->user_id, $data, $photoName );
				$this->RmCommon->setProcessParams($result, $urlRedirect);
			}

    		$this->set('module_title', __('Crop Foto Profil'));
    		$this->set('active_menu', 'profil');
			$this->set(compact(
				'user', 'save_path'
			));

		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_user_edit($id = false){
		$save_path = Configure::read('__Site.profile_photo_folder');

		$module_title = __('Edit User');
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
			),
		), array(
			'role' => 'agent',
			'status' => 'semi-active',
			'company' => true,
			'admin' => true,
		));

		if( !empty($value) ) {
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'User', 'photo');
			$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

			$value = $this->User->getAllNeed( $value, $id );
			$value = $this->User->getMerge( $value, $parent_id, false, 'Parent' );

			$data = $this->request->data;
			$data = $this->RmUser->_callDataRegister($data);

			if( !empty($data) ) {
				$data['User']['status'] = 1;
			}

			$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));
			$data = $this->RmUser->_callUserBeforeSave( $data, $value );
			$result = $this->User->doEdit( $id, $value, $data);

			//	if user upload new photo, delete old photo
			if(isset($this->request->data['User']['photo']['name'])){
				$uploadPhoto = $this->request->data['User']['photo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'agents',
				'admin' => true,
			));

			$this->request->data = $this->RmUser->_callBeforeView($this->request->data);

			$this->RmCommon->_callRequestSubarea('UserProfile');

			if(!empty($result['status']) && $result['status'] == 'success'){
				$value = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $id,
					),
				), array(
					'role' => 'agent',
					'status' => 'semi-active',
					'company' => true,
					'admin' => true,
				));

				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

				$value = $this->User->getAllNeed( $value, $id );
				$value = $this->User->getMerge( $value, $parent_id, false, 'Parent' );
			}

			$this->RmCommon->_callDataForAPI($value, 'manual');

			$this->set('active_menu', 'agent');
			$this->set(array(
				'user' => $value, 
				'module_title' => $module_title,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Agen tidak ditemukan'));
		}
	}

	public function admin_add(){
		// Check Limit Admin
		$params = $this->params->params;
		$role = $this->RmUser->_callRoleBySlug('admin');

		$user_id = Common::hashEmptyField($params, 'named.user_id');
		$user = $this->RmUser->getUser($user_id);
		$recordID = Common::hashEmptyField($user, 'User.id');
		$principle_group_id = Common::hashEmptyField($user, 'User.group_id');

		$parent_id = $this->RmUser->getUser(false, 'id');
		$parent_name = $this->RmCommon->filterEmptyField($this->data_company, 'User', 'full_name');

		$_isAdmin = Configure::read('User.Admin.Rumahku');

		if($user){
			$module_title = __('Tambah User');
			$urlBack = $this->RmUser->getUrlBack($user);

			$save_path = Configure::read('__Site.profile_photo_folder');
			$data = $this->request->data;
			$data = $this->RmUser->_callDataRegister($data);
			$group_id = Common::hashEmptyField($data, 'User.group_id');

			// call data master package membership rku
			$packages = $this->RmSetting->callDataMembershipRKU(array(
				'user_own' => false,
			));

			$data	= $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));
			$result	= $this->User->doAdd( $data, $parent_id, $parent_name, $group_id );

			$status	= $this->RmCommon->filterEmptyField($result, 'status');
			$id		= $this->RmCommon->filterEmptyField($result, 'id');

			$this->RmCommon->setProcessParams($result, $urlBack);

			$this->request->data = $this->RmCommon->_callUnset(array(
				'User' => array(
					'password',
					'password_confirmation',
				),
			), $this->request->data);
			$this->request->data = $this->RmUser->_callBeforeView($this->request->data);

			$this->RmCommon->_callRequestSubarea('UserProfile');

			// view
			$this->RmUser->_callRoleActiveMenu($user);
			$groups = $this->User->Group->getDivisionCompany(array(
				'userID' => Common::hashEmptyField($user, 'User.id'),
				'slug' => Common::hashEmptyField($user, 'Group.name', false, array(
					'type' => 'strtolower'
				)),
			), $user);

			$this->RmGroup->getSuperior($this->request->data, array(
				'recordID' => $recordID,
			));

			$this->set(array(
				'user_company' => $this->data_company,
				'packages' => $packages,
				'module_title' => $module_title,
				'urlBack' => $urlBack,
				'currUser' => $user,
				'groups' => $groups,
				'recordID' => !empty($recordID) ? $recordID : Configure::read('Principle.id'),
			));

			if(empty($user_id)){
				$this->set(array(
					'self' => true,
				));
			}

			if($principle_group_id == 3){
				if ($_isAdmin) {
					$this->set(array(
						'access_membership_rku' => true,
					));
				}

				$this->set(array(
					'user_type' => 'user',
				));
			}

			$this->render('admin_add_admin');
		}else{
			$this->RmCommon->redirectReferer(__('Anda telah mencapai batas pembuatan admin, silahkan lakukan upgrade paket untuk dapat menambah admin kembali.'));
		}
	} 

	public function admin_add_admin() {
		// Check Limit Admin
		$params = $this->params;
		$slug = $this->RmCommon->filterEmptyField($params, 'slug');
		$role = $this->RmUser->_callRoleBySlug('admin');
		$admins = $this->User->getAgents($this->parent_id, true, 'count', false, array(
			'role' => $role,
		));
		$access = $this->RmCommon->getAccessPage($this->data_company, 'max_admin', $admins );
		$parent_name = $this->RmCommon->filterEmptyField($this->data_company, 'User', 'full_name');

		if($access){
			$module_title = __('Tambah Admin');
			$urlBack = array(
				'controller' => 'users',
				'action' => 'admins',
				'slug' => $slug,
				'admin' => true,
			);

			$save_path = Configure::read('__Site.profile_photo_folder');
			$data = $this->request->data;
			$data = $this->RmUser->_callDataRegister($data);

			$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));
			$result = $this->User->doAdd( $data, $this->parent_id, $parent_name, 5 );

			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$id = $this->RmCommon->filterEmptyField($result, 'id');

			if($this->Rest->isActive() && $status == 'success' && !empty($id)){
				$this->request->data = array();
				
				$this->admin_edit_admin($id);
			}

			$this->RmCommon->setProcessParams($result, $urlBack);

			$this->request->data = $this->RmCommon->_callUnset(array(
				'User' => array(
					'password',
					'password_confirmation',
				),
			), $this->request->data);
			$this->request->data = $this->RmUser->_callBeforeView($this->request->data);

			$this->RmCommon->_callRequestSubarea('UserProfile');
			$this->set(compact(
				'user_company', 'subareas', 
				'module_title', 'urlBack'
			));
		}else{
			$this->RmCommon->redirectReferer(__('Anda telah mencapai batas pembuatan admin, silahkan lakukan upgrade paket untuk dapat menambah admin kembali.'));
		}
	}

	function backprocess_group_parent($recordID = false){
		$data = $this->request->data;
		if(!empty($data)){
			$group_id = Common::hashEmptyField($data, 'User.group_id');

			$this->RmGroup->getSuperior($data, array(
				'recordID' => $recordID,
			));
			$this->set('group_id', $group_id);
		}
	}

	public function admin_edit_admin ( $id = false ) {
		$params = $this->params;
		$slug = $this->RmCommon->filterEmptyField($params, 'slug');
		$role = $this->RmUser->_callRoleBySlug('admin');
		$save_path = Configure::read('__Site.profile_photo_folder');

		$module_title = __('Edit Admin');
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
				'User.group_id' => 5,
			),
		), array(
			'role' => $role,
			'status' => 'semi-active',
			'company' => true,
			'admin' => true,
		));

		if( !empty($value) ) {
			$urlBack = array(
				'controller' => 'users',
				'action' => 'admins',
				'slug' => $slug,
				'admin' => true,
			);
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'User', 'photo');
			$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

			$value = $this->User->getAllNeed( $value, $id );
			$value = $this->User->getMerge( $value, $parent_id, false, 'Parent' );
			
			$data = $this->request->data;
			$data = $this->RmUser->_callDataRegister($data);
			$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));
			$data = $this->RmUser->_callUserBeforeSave( $data, $value );
			$result = $this->User->doEdit( $id, $value, $data);

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['User']['photo']['name'])){
				$uploadPhoto = $this->request->data['User']['photo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, $urlBack);
			$this->request->data = $this->RmUser->_callBeforeView($this->request->data);
			
			if(!empty($result['status']) && $result['status'] == 'success'){
				$value = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $id,
						'User.group_id' => 5,
					),
				), array(
					'role' => $role,
					'status' => 'semi-active',
					'company' => true,
					'admin' => true,
				));

				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

				$value = $this->User->getAllNeed( $value, $id );
				$value = $this->User->getMerge( $value, $parent_id, false, 'Parent' );
			}

			$this->RmCommon->_callDataForAPI($value, 'manual');

			$this->RmCommon->_callRequestSubarea('UserProfile');

			$this->set(compact(
				'module_title', 'urlBack'
			));
			$this->set(array(
				'module_title' => $module_title,
				'urlBack' => $urlBack,
				'user' => $value,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Admin tidak ditemukan'));
		}
	}

	public function admin_admins(){
		$module_title = __('Daftar Admin');
		$role = $this->RmUser->_callRoleBySlug('admin');
		$options =  $this->User->_callRefineParams($this->params, array(
			'conditions' => array(),
			'group' => array(
				'User.id',
			),
			'order' => array(
				'User.created' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->User->getData('paginate', $options, array(
			'status' => 'semi-active',
			'role' => $role,
			'company' => true,
			'admin' => true,
		));
		
		$values = $this->paginate('User');

		if( !empty($values) ){
			foreach( $values as $key => &$value ) {
				$admin_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

				$value = $this->User->getAllNeed($value, $admin_id);
				$value = $this->User->UserCompany->getMerge( $value, $parent_id );
				$value = $this->RmUser->_callGetLogView($admin_id, $value);

				if($this->Rest->isActive()){
					$parent = $this->User->getData('first', array(
						'conditions' => array(
							'User.id' => $parent_id
						)
					));
					
					if(!empty($parent['User'])){
						$value['Parent'] = $parent['User'];
					}
				}
			}
		}

		$this->RmCommon->_callDataForAPI($values, 'manual');

		$this->set(compact(
			'values', 'module_title'
		));
		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	function admin_change_password( $user_id = false, $modelName = 'User' ) {
		if( $this->RmCommon->_isAdmin() || $this->RmCommon->_isCompanyAdmin() || Configure::read('User.admin') ) {

			$principle = $this->RmUser->getUser();
			$parent_id = Common::hashEmptyField($principle, 'User.id');
			$groupName = Common::hashEmptyField($principle, 'Group.name', false, array(
				'type' => 'strtolower',
			));

			$active_menu = $this->RmUser->getActive($groupName);

			if( $modelName != 'User' ) {
				$this->loadModel($modelName);
			}

			$user = $this->$modelName->getData('first', array(
				'conditions' => array(
					$modelName.'.id' => $user_id,
				),
			), array(
				'status' => 'semi-active',
				'parent' => true,
				'company' => true,
				'admin' => true,
			));

			if( !empty($user) ) {
				if ( !empty($this->request->data) ) {
					$data = $this->request->data;

					$this->$modelName->id = $user_id;
					$this->$modelName->set($data);

					if( $this->$modelName->validates() ) {
						$email = $this->RmCommon->filterEmptyField($user, $modelName, 'email');
						$new_password = $this->RmCommon->filterEmptyField($data, $modelName, 'new_password');
						$data[$modelName]['password'] = $this->Auth->password($new_password);

						if ( $this->$modelName->save($data) ) {
							$this->RmCommon->redirectReferer(sprintf(__('Berhasil mengubah password %s'), $email), 'success');
						}
					} else {
						$this->RmCommon->redirectReferer(__('Gagal mengubah password'), 'error');
						$this->RmCommon->setValidationError($this->$modelName->validationErrors);
					}

					unset($this->request->data);
				}

				$module_title = __('Ganti Password');

				$this->set(array(
					'active_menu' => $active_menu,
					'module_title' => $module_title,
					'modelName' => $modelName,
				));
			} else {
				$this->RmCommon->redirectReferer(__('User tidak ditemukan.'), 'error');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki akses terhadap konten tersebut.'));
		}
	}

	function admin_password_reset($reset_code = false) {
		$value = $this->User->PasswordReset->getData('first', array(
			'conditions' => array(
				'PasswordReset.reset_code' => $reset_code,
			),
		));
		$loginRedirect = array(
			'controller' => 'users',
			'action' => 'login',
			'admin' => true,
		);

		if( !empty($value) ) {
			$check_expired = $this->RmUser->_checkExpiredResetPassword($value);

			if($check_expired) {
				if (!empty($this->request->data)) {
					$data = $this->request->data;
					$new_password = $this->RmCommon->filterEmptyField($data, 'User', 'new_password');

					$id = $this->RmCommon->filterEmptyField($value, 'PasswordReset', 'id');
					$user_id = $this->RmCommon->filterEmptyField($value, 'PasswordReset', 'user_id');

					$this->User->id = $user_id;
					$this->User->set($data);

					if( $this->User->validates() ) {
						$data['User']['password'] = $this->Auth->password($new_password);

						if ( $this->User->save($data) ) {
							$this->User->PasswordReset->set('status', 0);
							$this->User->PasswordReset->id = $id;
							$this->User->PasswordReset->save();

							$this->RmCommon->redirectReferer(__('Berhasil mengubah password. Silakan masukkan password baru Anda'), 'success', $loginRedirect);
						}
					}

					unset($this->request->data);
				}

				$title_for_layout = $module_title = __('Reset Password');
				$this->set('_greeting', __('Mohon masukkan password baru Anda'));
				$this->set(compact(
					'module_title', 'title_for_layout'
				));
		    	$this->layout = 'login';
			} else {
				$this->RmCommon->redirectReferer(__('Kode reset sudah expired. Silakan lakukan lupa kata sandi untuk pengiriman kode kembali.'), 'error', $loginRedirect);
			}
		} else {
			$this->RmCommon->redirectReferer(__('Kode reset tidak valid. Kode ini mungkin telah digunakan atau sudah tidak berlaku.'), 'error', $loginRedirect);
		}
	}

	public function admin_forgotpassword() {
		$result = false;
        if ( !empty($this->request->data) ) {
        	$data = $this->request->data;
        	$email = $this->RmCommon->filterEmptyField($data, 'User', 'forgot_email');

        	$data['User']['forgot_email'] = $email = trim($email);
        	$this->User->set($data);

        	if( $this->User->validates() ) {
				$user = $this->User->getData('first', array(
					'conditions'=>array(
						'User.email' => $email,
						'User.group_id <>' => 10,
					),
				), array(
					'status' => 'all',
					'company' => true,
					'admin' => true,
					'admin_rumahku' => true
				));

				if(!empty($user)) {
        			$id = $this->RmCommon->filterEmptyField($user, 'User', 'id');
        			$active = $this->RmCommon->filterEmptyField($user, 'User', 'active');

					if( empty($active) ){
						$this->RmCommon->setCustomFlash(__('Harap melakukan aktivasi akun terlebih dahulu sebelum merubah password'), 'error');
					} else {
						$full_name = $this->RmUser->_getUserFullName($user);
						$reset_code = $this->RmUser->_generateCode('reset');
            			$client_ip = $this->RequestHandler->getClientIP();

						$result = $this->User->PasswordReset->doSave(array(
							'company_id' => $this->parent_id,
							'user_id' => $id,
				            'email' => $email,
				            'reset_code' => $reset_code,
            				'client_ip' => $client_ip,
            				'full_name' => $full_name,
						));
					}
				} else {
					$result = array(
						'msg' => __('Gagal melakukan reset password, User tidak ditemukan.'),
						'status' => 'error'
					);
				}
			} else {
				$result = array(
					'msg' => __('Gagal melakukan reset password, mohon masukkan email Anda dengan benar'),
					'status' => 'error'
				);
			}
		}
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'users',
			'action' => 'login',
			'admin' => true,
		));
		$this->set('_greeting', __('Mohon masukkan Email Anda untuk proses pengiriman Reset Password'));
    	$this->layout = 'login';
    }

	function agents(){
		$group_id = $this->RmCommon->filterEmptyField($this->data_company, 'User', 'group_id' );

		if( $group_id == 4 ) {
			$this->RmCommon->redirectReferer(__('Halaman tidak ditemukan'));
		} else {
			$module_title = $title_for_layout = __('Daftar Agen');
			$options =  $this->User->_callRefineParams($this->params, array(
				'order' => array(
					'User.created' => 'DESC',
					'User.full_name' => 'ASC',
				),
				'limit' => 12,
			));
			$this->RmCommon->_callRefineParams($this->params);

			$this->paginate = $this->User->getData('paginate', $options, array(
				'status' => 'semi-active',
				'role' => 'agent',
				'company' => true,
			));
			$values = $this->paginate('User');
			$values = $this->User->getDataList($values);
	    	$populers = $this->User->Property->populers(5);
			$displayShow = $this->RmCommon->filterEmptyField($this->params, 'named', 'show', 'grid');
			
			if( !empty($values) ){
				foreach( $values as $key => $value ) {
					$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');

					$value = $this->User->UserProfile->getMerge( $value, $id, true);
					$values[$key] = $value;
				}
			}

			$url_without_http = Configure::read('__Site.domain');

			$page = $this->RmCommon->filterEmptyField($this->params->params, 'named', 'page', '');

			if(!empty($page)){
				$page = sprintf(__(' Page %s'), $page);
			}

			$title_for_layout 		= sprintf('Agen Properti%s - %s', $page, $url_without_http);
			$keywords_for_layout	= sprintf('Agen Properti di %s', $url_without_http);
			$description_for_layout = sprintf(__('Cari Agen Properti%s di %s Terbaik dan Terpercaya!'), $page, $url_without_http);

			$this->set('title_for_layout', $title_for_layout);
			$this->set('keywords_for_layout', $keywords_for_layout);
			$this->set('description_for_layout', $description_for_layout);

			$active_menu = 'agents';
			$this->RmCommon->_layout_file(array(
				'map',
				'map-cozy',
			));
			$this->set('active_menu', __('agents'));
			$this->set(compact(
				'active_menu', 'values', 'module_title',
				'displayShow', 'populers', 'advices', 'propertyTypes',
				'title_for_layout'
			));
		}
	}

	function profile( $id = false, $slug = false ){
		$module_title = __('Profil Agen');
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
				'User.username' => $slug,
			),
		), array(
			'status' => 'semi-active',
			'role' => 'agent',
			'company' => true,
		));

		if( !empty($value) ){
			$isDirector = $this->RmCommon->_callIsDirector();
			$value = $this->User->getMergeList($value, array(
				'contain' => array(
					'UserProfile',
					'UserConfig',
				),
			));

			if( !empty($isDirector) ) {
				$value = $this->User->getMergeList($value, array(
					'contain' => array(
						'Parent' => array(
							'contain' => array(
								'UserCompany' => array(
									'contain' => array(
										'Region' => array(
											'cache' => true,
										),
										'City' => array(
											'cache' => true,
										),
										'Subarea' => array(
											'cache' => array(
												'name' => 'Subarea',
												'config' => 'subareas',
											),
										),
									),
								),
							),
						),
					),
				));
			}

            $agent_name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');
            $description = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'description');

			$this->paginate = $this->User->Property->getData('paginate', array(
				'conditions' => array(
					'Property.user_id' => $id,
				),
				'limit' => 24,
			), array(
				'status' => 'active-pending-sold',
				'company' => true,
				'skip_is_sales' => true,
			));
			$properties = $this->paginate('Property');
			$properties = $this->User->Property->getDataList($properties, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertySold',
					'PropertyStatusListing',
				),
			));

			$dataView = $this->RmCommon->_callSaveVisitor($id, 'UserView', 'profile_id');
			$this->User->UserView->doSave($dataView);

			// Khusus EasyLiving
			$advices = $this->User->Advice->getData('all', array(
				'limit' => 3,
			));
			$advices = $this->User->Advice->getDataList($advices);
			$propertyTypes = $this->User->Property->PropertyType->getData('list', array(
	            'cache' => __('PropertyType.List'),
	        ));

			// Proses Contact
			$this->RmUser->_callBeforeSaveContact($value, array(
				'controller' => 'users',
				'action' => 'profile',
				$id,
				$slug,
				'admin' => false,
			));

			$total_listing = $this->User->Property->getData('count', false, array(
				'status' => 'active-pending-sold',
				'skip_is_sales' => true,
			));
			$this->RmCommon->_callRequestSubarea('Search');

			$this->RmCommon->_layout_file(array(
				'map',
				'map-cozy',
			));

			$url_without_http = Configure::read('__Site.domain');

			$title_for_layout 		= sprintf('%s - Agen Properti - %s', $agent_name, $url_without_http);
			$keywords_for_layout	= sprintf('%s Agen Properti di %s', $agent_name, $url_without_http);
			$description_for_layout = sprintf(__('Cari %s Agen Properti di %s Terbaik dan Terpercaya!'), $agent_name, $url_without_http);

			$this->set('title_for_layout', $title_for_layout);
			$this->set('keywords_for_layout', $keywords_for_layout);
			$this->set('description_for_layout', $description_for_layout);

			$this->set('captcha_code', $this->Captcha->generateEquation());
			$this->set('active_menu', __('agents'));
			$this->set(compact(
				'value', 'properties',
				'module_title', 'advices', 'propertyTypes',
				'total_listing'
			));
		}else{
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	function message( $id = false ){
		$isDirector = $this->RmCommon->_callIsDirector();
		$module_title = __('Kirim Pesan');
		$elements = array(
			'status' => 'semi-active',
		);

		if( !empty($isDirector) ) {
			$elements['role'] = array(
				'agent', 'principle',
			);
		} else {
			$elements['role'] = 'agent';
			$elements['company'] = true;
		}

		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
			),
		), $elements);

		if( !empty($value) ){
            $slug = $this->RmCommon->filterEmptyField($value, 'User', 'username');
            $group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');

            if( !empty($isDirector) ) {
	            if( $group_id == 3 ) {
	            	$director_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
	            } else {
	            	$tmp_parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
					$parent = $this->User->getMerge( array(), $tmp_parent_id);
	            	$director_id = $this->RmCommon->filterEmptyField($parent, 'User', 'parent_id');
	            }

	            if( $director_id != $this->parent_id ) {
					$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
	            }
	        }

			$value = $this->User->UserProfile->getMerge($value, $id, true);
			$value = $this->User->UserConfig->getMerge( $value, $id);

			switch ($group_id) {
				case '3':
					$type = __('Kami');
					break;
				
				default:
					$type = __('Agen');
					break;
			}

			// Proses Contact
			$data = $this->request->data;

			if( !empty($data) ) {
				$isAjax		= $this->RequestHandler->isAjax();
				$data		= $this->RmUser->_callMessageBeforeSave($id);
				$result		= $this->User->Message->doSend($data, $value, false, 'message_agent');
				$status		= Common::hashEmptyField($result, 'status', 'error');
				$message	= Common::hashEmptyField($result, 'msg');
				$redirect = array(
					'controller'	=> 'users', 
					'action'		=> 'message', 
					$id, 
					'admin'			=> false, 
				);

				$this->RmCommon->setProcessParams($result, $redirect, array(
						'ajaxFlash' => true,
						'ajaxRedirect' => true,
				));
			}

			$this->set('captcha_code', $this->Captcha->generateEquation());
			$this->set(compact(
				'value', 'module_title'
			));
		}else{
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	function admin_security( $tabs_action_type = 'change_email' ) {
		$this->RmCommon->_callUserLogin();

		if( !empty($tabs_action_type) ) {
			
			$data = $this->request->data;
			$user['User'] = $this->Auth->user();

			if( !empty($data) ) {
				if( $tabs_action_type == 'change_email' ) {
					$result = $this->User->doEditEmail( $this->user_id, $user, $data);
				} else if( $tabs_action_type == 'change_password' ) {
					$data = $this->RmUser->_callDataRegister($data);
					$result = $this->User->doEditPassword( $this->user_id, $user, $data);
				}

				if( isset($result['status']) && $result['status'] == 'success' ) {
					$this->RmUser->refreshAuth($this->user_id);
				}

				$this->RmCommon->setProcessParams($result, false, array(
					'restData' => true,
					'rest' => false,
				));
			} else {
				$this->RmCommon->_callDataForAPI($user, 'manual');
			}

			if( $tabs_action_type == 'change_password' ){
				$this->request->data = $this->RmCommon->_callUnset(array(
					'User' => array(
						'current_password',
						'new_password',
						'new_password_confirmation',
					),
				), $data);
			}
		}

		$module_title = $title_for_layout = __('Keamanan');
		$this->set('active_menu', 'security');
		$this->set(compact(
			'module_title', 'title_for_layout',
			'tabs_action_type'
		));
	}

	function admin_redirect_notification ( $id = false ) {
		$value = $this->User->Notification->getData('first', array(
			'conditions' => array(
				'Notification.id' => $id,
			),
		));

		$result = array();
		$link = '';
		$url = '';
		if( !empty($value) ) {
            $link = $this->RmCommon->filterEmptyField($value, 'Notification', 'link');
            $url = unserialize($link);
        	$result = $this->User->Notification->doRead($id);

            if( !empty($url) && is_array($url) ) {
				$this->RmCommon->setProcessParams($result, $url, array(
					'flash' => false,
				));
            } else {
            	$this->redirect(array(
					'controller' => 'users',
					'action' => 'notifications',
					'admin' => true,
				));
            }

		} else {
			$this->redirect(array(
				'controller' => 'users',
				'action' => 'notifications',
				'admin' => true,
			));
		}

		if($this->Rest->isActive()){
			if(!empty($result['status']) && $result['status'] == 'success' && !empty($url) && is_array($url)){
				App::import('Helper', 'Html');
				$Html = new HtmlHelper(new View(null));

				$url = $Html->url($url);

				$this->set('data_redirect', $url);
			}
		}

		$this->RmCommon->renderRest();
	}

	function admin_notifications() {
		$this->RmCommon->_callUserLogin();
		$module_title = $title_for_layout = __('Notifikasi');
		$this->paginate = $this->User->Notification->getData('paginate', array(
			'limit' => 10
		), array(
			'mine' => true,
		));
		
		$result = $this->paginate('Notification');

		if($this->Rest->isActive() && !empty($result)){
			
			App::import('Helper', 'Html');
        	$this->Html = new HtmlHelper(new View(null));

			foreach ($result as $key => $value) {
				$result_content = Common::hashEmptyField($value, 'Notification.link');

				$content = @unserialize($result_content);
				if($content !== false){
					$raw = $result_content = unserialize($result_content);

					$result_content = $this->Html->url($result_content);

					if(isset($raw['action']) && count($raw) == 3 && $raw['action'] == 'index'){
						$result_content .= '/index';
					}else if(!empty($result_content)){
						$arr_content = explode('/', $result_content);
						
						if(count($arr_content) == 3){
							$result_content .= '/index';
						}
					}
				}

				$result[$key]['Notification']['link'] = $result_content;
			}
		}

		$data = $values = $result;
		
		$this->set(compact(
			'values', 'module_title', 'title_for_layout', 'data'
		));

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	function admin_agent_clients() {
		$this->loadModel('UserClientRelation');
		$module_title = $title_for_layout = __('Klien');
		$params = $this->params->params;
		$named = Common::hashEmptyField($params, 'named', array());

		$options = $this->UserClientRelation->_callRefineParams($this->params, array(
			'conditions' => array(
				'UserClientRelation.agent_id' => $this->user_id,			
				'User.deleted' => 0,
				'UserClient.status' => 1,
			),
			'contain' => array(
				'User' => array(
					'className' => 'User',
					'foreignKey' => 'user_id',
				),
				'UserClient',
			),
			'order' => array(
				'UserClientRelation.created' => 'DESC',
			),
			'group' => array(
				'UserClientRelation.company_id',
				'UserClientRelation.user_id',
				'UserClientRelation.agent_id',
			),
		));

		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->UserClientRelation->getData('paginate', $options, array(
			'company' => true,
			'adminRumahku' => false,
		));

		$values = $this->paginate('UserClientRelation');

		if( !empty($values) ){
			foreach( $values as $key => $value ) {
				$user_id = $this->RmCommon->filterEmptyField($value, 'UserClientRelation', 'user_id');
				$agent_id = $this->RmCommon->filterEmptyField($value, 'UserClientRelation', 'agent_id');
				$value = $this->User->UserClient->getMerge( $value, $user_id, $this->parent_id);

				$client_type_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'client_type_id');
				$company_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'company_id');

				$value = $this->User->UserClient->ClientType->getMerge( $value, $client_type_id);
				$value = $this->User->UserCompany->getMerge( $value, $company_id);
				$value = $this->User->getMergeList($value, array(
					'contain' => array(
						'UserProfile', 
					), 
				));
				$value = $this->User->UserClient->getMergeList($value, array(
					'contain' => array(
						'UserClientMasterReference',
					), 
				));
				$value = $this->RmUser->_callLastActivity($value, array(
					'client_id' => $user_id,
					'agent_id' => $agent_id,
				));
				
				$values[$key] = $value;
			}
		}

		$this->RmCommon->_callDataForAPI($values, 'manual');
		$this->RmUser->_callBeforeViewListClient();

		$this->set('active_menu', 'crm_client');
		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'export' => array(
				'title' => __('Export Klien'),
				'url' => array_merge(array(
					'controller' => 'reports',
					'action' => 'generate',
					'clients',
				), $named),
			),
			'_breadcrumb' => true,
		));

		$this->RmCommon->renderRest(array(
			'is_paging' => true,
			'render' => 'admin_clients',
		));
	}

	function admin_clients() {
		$this->loadModel('UserClient');

		$module_title = $title_for_layout = __('Daftar Klien');
		$group_id = Configure::read('User.group_id');
		$user_login_id = Configure::read('User.id');
		$params = $this->params->params;
		$named = Common::hashEmptyField($params, 'named', array());

		$this->set(array(
			'export' => array(
				'title' => __('Export Klien'),
				'url' => array_merge(array(
					'controller' => 'reports',
					'action' => 'generate',
					'clients',
				), $named),
			),
			'_breadcrumb' => true,
		));

		if( $group_id == 2 ) {
			if( $this->Rest->isActive() ){
	            $this->admin_agent_clients();
	        } else {
				$this->redirect(array(
					'controller' => 'users',
					'action' => 'agent_clients',
					'admin' => true,
				));
			}
		} else {
			$data_arr = $this->User->getUserParent($user_login_id);
			$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
			$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

			if($is_sales){
				$conditions = array(
					'UserClient.agent_id' => $user_ids,
				);
			} else {
				$conditions = array(
					'UserClient.company_id' => $this->parent_id,
				);
			}

			$options = $this->User->UserClient->_callRefineParams($params, array(
				'conditions' => array_merge(array(
					'UserClient.status' => 1,
				), $conditions),
				'order' => array(
					'UserClient.created' => 'DESC',
				),
				'limit' => Configure::read('__Site.config_new_table_pagination'),
			));

			$this->RmCommon->_callRefineParams($params);
			$user_options = array_merge($options, $this->User->getData('paginate', $options, array(
				'status' => 'all',
			)));

			$user_options['contain'][] = 'User';
			$this->paginate = $this->UserClient->getData('paginate', $user_options);
			$values = $this->paginate('UserClient');

			if( !empty($values) ) {
				foreach( $values as $key => $value ) {
					$user_id = Common::hashEmptyField($value, 'UserClient.user_id');
					$agent_id = Common::hashEmptyField($value, 'UserClient.agent_id');

					$value = $this->RmCommon->_callUnset(array(
						'User',
					), $value);
					$value = $this->User->UserClient->getMergeList($value, array(
						'contain' => array(
							'UserClientMasterReference',
							'User' => array(
								'elements' => array(
									'status' => false,
								),
							), 
							'UserCompany' => array(
								'primaryKey' => 'user_id',
								'foreignKey' => 'company_id',
							), 
							'ClientType', 
							'Agent' => array(
								'uses' => 'User',
								'primaryKey' => 'id',
								'foreignKey' => 'agent_id',
								'elements' => array(
									'status' => 'all',
								),
							), 
						), 
					));

					$value = $this->RmUser->_callLastActivity($value, array(
						'client_id' => $user_id,
						'agent_id' => $agent_id,
					));

					$values[$key] = $value;
				}
			}

			$this->RmCommon->_callDataForAPI($values, 'manual');
			$this->RmUser->_callBeforeViewListClient();

			$this->set('active_menu', 'crm_client');
			$this->set(compact(
				'values', 'module_title'
			));

			$this->RmCommon->renderRest(array(
				'is_paging' => true
			));
		}
	}

	function admin_add_client() {
		$module_title = $title_for_layout = __('Tambah Klien');
		$save_path = Configure::read('__Site.profile_photo_folder');
		$data = $this->request->data;
		$urlBack = array(
            'controller' => 'users',
            'action' => 'clients',
            'admin' => true,
        );

		$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
            'keep_file_name' => true,
		));
		$data = $this->RmUser->_callDataRegister($data, 10);

		$result = $this->User->doAdd( $data, $this->parent_id, false, 10, false );
		$id = Common::hashEmptyField($result, 'client_id');
		$status = Common::hashEmptyField($result, 'status');

		if( $this->Rest->isActive() && $status == 'success' ){
			$this->request->data = array();
			$this->admin_edit_client($id);
		} else {
			$this->RmCommon->setProcessParams($result, $urlBack);
			$this->request->data = $this->RmCommon->_callUnset(array(
				'User' => array(
					'password',
					'password_confirmation',
				),
			), $this->request->data);

			$this->RmUser->_callBeforeViewClient();

			$this->RmCommon->_callRequestSubarea('UserProfile');
			$this->set('active_menu', 'crm_client');
			$this->set(compact(
				'user_company', 'subareas', 
				'module_title', 'title_for_layout', 'urlBack'
			));
		}
	}

	function admin_edit_client ( $id = false ) {
		$module_title = $title_for_layout = __('Edit Klien');
		$value = $this->User->UserClient->getData('first', array(
			'conditions' => array(
				'UserClient.id' => $id,
			),
		));

		if( !empty($value) ) {
			$user_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'user_id');
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'UserClient', 'photo');

			$value = $this->User->getMerge( $value, $user_id);
			$value = $this->User->UserProfile->getMerge($value, $user_id);

			$swap_data = true;
			if($this->Rest->isActive()){
				$swap_data = false;

				$value = $this->User->UserClient->getMergeList($value, array(
					'contain' => array(
						'ClientType',
						'Agent' => array(
							'uses' => 'User',
							'primaryKey' => 'id',
							'foreignKey' => 'agent_id',
							'elements' => array(
								'status' => 'all',
							),
						), 
					), 
				)); 
			}

			$value = $this->RmUser->_callDataClient($value, $swap_data);
			$save_path = Configure::read('__Site.profile_photo_folder');

			$data = $this->request->data;
			$data = $this->RmImage->_uploadPhoto($data, 'UserClient', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));

			$result = $this->User->UserClient->doSave( $data, $value, $user_id ,$id);
			$status = Common::hashEmptyField($result, 'status');

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['UserClient']['photo']['name'])){
				$uploadPhoto = $this->request->data['UserClient']['photo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			if( $this->Rest->isActive() && $status == 'success' ){
				$this->request->data = array();
				$this->admin_edit_client($id);
			} else {
				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'users',
					'action' => 'clients',
					'admin' => true,
				), array(
					'restData' => true,
					'rest' => false,
				));

				$this->RmCommon->_callDataForAPI($value, 'manual');
				$this->RmUser->_callBeforeViewClient();

				$this->set('active_menu', 'crm_client');
				$this->set(compact(
					'module_title', 'title_for_layout'
				));
			}
		} else {
			$this->RmCommon->redirectReferer(__('Klien tidak ditemukan'));
		}
	}

	function admin_client_activities( $id = false ) {
		$module_title = $title_for_layout = __('Detail Klien');
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
				'User.group_id' => 10,
			),
		), array(
			'status' => 'semi-active',
		));

		if( !empty($user) ) {
			$user = $this->RmUser->_callDataClient( $user );
			$user = $this->User->UserClient->getMerge( $user, $id, $this->parent_id);
			$region_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'region_id');
			$city_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'city_id');
			$subarea_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'subarea_id');

			$user = $this->User->Property->PropertyAddress->Region->getMerge($user, $region_id, 'Region', array(
				'cache' => array(
					'name' => __('Region.%s', $region_id),
				),
			));
			$user = $this->User->Property->PropertyAddress->City->getMerge($user, $city_id, 'City', 'City.id', array(
				'cache' => __('City.%s', $city_id),
			));
			$user = $this->User->Property->PropertyAddress->Subarea->getMerge($user, $subarea_id, 'Subarea', 'Subarea.id', array(
				'cache' => __('Subarea.%s', $subarea_id),
				'cacheConfig' => 'subareas',
			));

			$tabs_action_type = array(
	            'controller' => 'users',
	            'action' => 'client_activities',
	            $id,
	            'admin' => true,
	        );

			$values = array();

			$this->set('active_menu', 'crm_client');
			$this->set(compact(
				'module_title', 'title_for_layout', 'user', 'id',
				'values', 'tabs_action_type'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Klien tidak ditemukan'));
		}
	}

	function admin_client_properties( $id = false ) {
		$module_title = $title_for_layout = __('Detail Klien');
		$user = $this->User->UserClient->getData('first', array(
			'conditions' => array(
				'UserClient.id' => $id,
			),
		));

		if( !empty($user) ) {
			$user_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'user_id');
			$user = $this->User->getMerge( $user, $user_id);
			$user = $this->RmUser->_callDataClient( $user );
			$region_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'region_id');
			$city_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'city_id');
			$subarea_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'subarea_id');

			$user = $this->User->Property->PropertyAddress->Region->getMerge($user, $region_id, 'Region', array(
				'cache' => array(
					'name' => __('Region.%s', $region_id),
				),
			));
			$user = $this->User->Property->PropertyAddress->City->getMerge($user, $city_id, 'City', 'City.id', array(
				'cache' => __('City.%s', $city_id),
			));
			$user = $this->User->Property->PropertyAddress->Subarea->getMerge($user, $subarea_id, 'Subarea', 'Subarea.id', array(
				'cache' => __('Subarea.%s', $subarea_id),
				'cacheConfig' => 'subareas',
			));

			$tabs_action_type = array(
	            'controller' => 'users',
	            'action' => 'client_properties',
	            $id,
	            'admin' => true,
	        );

			$options =  $this->User->Property->_callRefineParams($this->params, array(
				'limit' => Configure::read('__Site.config_pagination'),
				'conditions' => array(
					'Property.client_id' => $user_id,
				),
			));
			$this->paginate = $this->User->Property->getData('paginate', $options, array(
				'company' => true,
				'admin_mine' => true,
				'status' => 'active-pending'
			));
			$values = $this->paginate('Property');

			$contain = array(
	            'MergeDefault',
	            'PropertyAddress',
	            'PropertyAsset',
	            'PropertySold',
	            'PropertyNotification',
	            'User',
	        );

	        if(!$this->Rest->isActive()){
	            array_push($contain, 'PropertyMediasCount');
	        }

			$values = $this->User->Property->getDataList($values, array(
				'contain' => $contain,
			));

			$this->RmCommon->_callDataForAPI($values, 'manual');

			$this->set('active_menu', 'crm_client');
			$this->set(compact(
				'module_title', 'title_for_layout', 'user', 'id',
				'values', 'tabs_action_type'
			));

			$this->RmCommon->renderRest(array(
				'is_paging' => true
			));
		} else {
			$this->RmCommon->redirectReferer(__('Klien tidak ditemukan'));
		}
	}

	function admin_client_related_agents( $id = false ) {
		$module_title = $title_for_layout = __('Detail Klien');
		$_isAdmin = Configure::read('User.admin');

		$user = $this->User->UserClient->getData('first', array(
			'conditions' => array(
				'UserClient.id' => $id,
			),
		));

		if( !empty($user) && !empty($_isAdmin) ) {
			$user_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'user_id');
			$user = $this->User->getMerge( $user, $user_id);
			$user = $this->RmUser->_callDataClient( $user );
			$region_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'region_id');
			$city_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'city_id');
			$subarea_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'subarea_id');

			$user = $this->User->Property->PropertyAddress->Region->getMerge($user, $region_id, 'Region', array(
				'cache' => array(
					'name' => __('Region.%s', $region_id),
				),
			));
			$user = $this->User->Property->PropertyAddress->City->getMerge($user, $city_id, 'City', 'City.id', array(
				'cache' => __('City.%s', $city_id),
			));
			$user = $this->User->Property->PropertyAddress->Subarea->getMerge($user, $subarea_id, 'Subarea', 'Subarea.id', array(
				'cache' => __('Subarea.%s', $subarea_id),
				'cacheConfig' => 'subareas',
			));

			$tabs_action_type = array(
	            'controller' => 'users',
	            'action' => 'client_related_agents',
	            $id,
	            'admin' => true,
	        );

			$this->loadModel('UserClientRelation');		

			$this->RmCommon->_callRefineParams($this->params);	
			
			$related_agents_options = $this->UserClientRelation->_callRefineParams($this->params->params, array(
				'conditions' => array(
					'User.id <>' => NULL,
					'UserClientRelation.user_id' => $user_id,
					'UserClientRelation.company_id' => $this->parent_id,
					'User.deleted' => false,
					'User.status' => true,
				),
				'contain' => array(
					'User',
				),
				'order' => array(
					'UserClientRelation.primary' => 'DESC',
					'UserClientRelation.id' => 'ASC',
				),
				'group' => array(
					'UserClientRelation.user_id',
					'UserClientRelation.agent_id',
				),
			));

			$this->paginate = $this->UserClientRelation->getData('paginate', $related_agents_options);
			$values = $this->paginate('UserClientRelation');

			$agent_pic = $this->User->UserClient->getMerge(array(), $user_id, $this->parent_id);
			$agent_pic_id = $this->RmCommon->filterEmptyField($agent_pic, 'UserClient',  'agent_id');

			if( !empty($values) ) {
				foreach( $values as $key => $value ) {
					$user_id = $this->RmCommon->filterEmptyField($value, 'UserClientRelation', 'agent_id');
					$value = $this->User->getMerge( $value, $user_id);
					$value = $this->User->UserProfile->getMerge( $value, $user_id);
					
					$values[$key] = $value;
				}
			}

			$this->RmCommon->_callDataForAPI($values, 'manual');

			if( $this->Rest->isActive() ){
				$agents_id = Set::extract('/UserClientRelation/agent_id', $values);
				$agents_id[$agent_pic_id] = $agent_pic_id;

				$agents = $this->User->getData('all', array(
					'conditions' => array(
						'User.id NOT' => $agents_id,
					),
					'order' => array(
						'User.full_name' => 'ASC',
						'User.created' => 'DESC',
					),
				), array(
					'status' => 'semi-active',
					'role' => 'agent',
					'company' => true,
				));
			}

			$this->set('active_menu', 'crm_client');
			$this->set(compact(
				'module_title', 'title_for_layout', 'user', 'id',
				'values', 'tabs_action_type', 'agent_pic_id', 'agents'
			));

			$this->RmCommon->renderRest(array(
				'is_paging' => true
			));
		} else {
			$this->RmCommon->redirectReferer(__('Klien tidak ditemukan'));
		}
	}

	function admin_client_relation( $client_id = false ) {
		$module_title = $title_for_layout = __('Tambah Agen Terhubung');
		$agent_pic = $user = $this->User->UserClient->getData('first', array(
			'conditions' => array(
				'UserClient.id' => $client_id,
			),
		));

		if( !empty($user) ) {
			$user_id = $this->RmCommon->filterEmptyField($user, 'UserClient',  'user_id');
			$agent_pic_id = $this->RmCommon->filterEmptyField($user, 'UserClient',  'agent_id');

			$agent_pic = $this->User->getMerge($agent_pic, $agent_pic_id);
			$related_agents_options = array(
				'conditions' => array(
					'UserClientRelation.user_id' => $user_id,
					'UserClientRelation.company_id' => $this->parent_id,
					'UserClientRelation.agent_id <>' => $agent_pic_id,
					'User.deleted' => false,
					'User.status' => true,
				),
				'contain' => array(
					'User' => array(
						'className' => 'User',
						'foreignKey' => 'agent_id',
					),
				),
				'order' => array(
					'UserClientRelation.primary' => 'DESC',
					'UserClientRelation.id' => 'ASC',
				),
			);
			$related_agents = $this->User->UserClientRelation->getData('all', $related_agents_options);

			$agents_id = Set::extract('/User/id', $related_agents);

			$agents_id[$agent_pic_id] = $agent_pic_id;

			$agent_options = array(
				'conditions' => array(
					"NOT" => array(
						'OR' => array(
							"User.id" => $agents_id,
						),
					),
				),
				'order' => array(
					'User.full_name' => 'ASC',
					'User.created' => 'DESC',
				),
			);

			if( !$this->Rest->isActive() ){
				$agents = $this->User->getData('all', $agent_options, array(
					'status' => 'semi-active',
					'role' => 'agent',
					'company' => true,
				));
			}
			
			$this->RmCommon->_callDataForAPI($related_agents);

			$this->set('active_menu', 'crm_client');
			$this->set(compact(
				'module_title', 'title_for_layout', 
				'related_agents', 'agents', 'client_id', 'agent_pic'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Klien tidak ditemukan'));
		}
	}

	public function admin_client_agent_mapping_multiple( $client_id = false ) {
		$client = $user = $this->User->UserClient->getData('first', array(
			'conditions' => array(
				'UserClient.id' => $client_id,
			),
		));

		if( !empty($client) ) {
			$data = $this->request->data;
			$user_id = $this->RmCommon->filterEmptyField($client, 'UserClient',  'user_id');
			$agent_pic_id = $this->RmCommon->filterEmptyField($client, 'UserClient',  'agent_id');

			$agent_id = $this->RmCommon->filterEmptyField($data, 'UserClientRelation', 'id');
			$current_relation = $this->User->UserClientRelation->getData('paginate', array(
				'conditions' => array(
					'UserClientRelation.user_id' => $user_id,
				),
			), array(
				'company' => true,
				'adminRumahku' => false,
			));

			$result = $this->User->UserClientRelation->doSaveMapping($user_id, $agent_id, $current_relation, $agent_pic_id);
	    	$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Klien tidak ditemukan'));
		}
    }

    public function admin_remove_user($id = false){
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
			),
		), array(
			'status' => array(
				'not-active',
				'active'
			),
		));

		$this->render('admin_remove_agent');
    }

    public function admin_remove_agent(){
		$data	= $this->request->data;		
		$params	= $this->RmCommon->filterEmptyField($data, 'params');

		$recordID = Common::hashEmptyField($this->params->named, 'user_id');
		$recordID = $recordID ?: Configure::read('Principle.id');

		$values		= array();
		$agentID	= array();
		$agent		= false;

		if($params){
			foreach($params as $param){
				$paramName = Hash::get($param, 'name');

				if($paramName && strpos($paramName, 'data[User][id]') !== false){
					$agentID[] = Hash::get($param, 'value');
				}
			}
		}
		else if(Hash::check($data, 'UserRemoveAgent.id')){
			$agentID = Hash::get($data, 'UserRemoveAgent.id');
		}

		$agentID = array_filter((array) $agentID);

		if($agentID){
			$agentID = array_unique($agentID);

			foreach($agentID as $id){
				$agentData = $this->User->getMerge(array(), $id);

				if($agentData){
					$groupID	= Common::hashEmptyField($agentData, 'User.group_id');
					$email		= Common::hashEmptyField($agentData, 'User.email');

					if(empty($agent) && $groupID == 2){
						$agent = true;
					}

					$values[$email] = $agentData;
				}
			}

			$values['agent'] = $agent;
		}

	//	additional flag (buat mancing eksekusi save)
		$process = Common::hashEmptyField($data, 'UserRemoveAgent.process');

		if($process){
			$result = $this->User->UserRemoveAgent->doSave($recordID, $data);

			if(!empty($result['status']) && $result['status'] == 'success' && !empty($result['id'])){
				$this->set('_flash', false);
			}

			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true,
			));
		}

		$this->set(array(
			'values'	=> $values,
			'recordID'	=> $recordID,
		));
    }

    public function admin_remove_principle() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'User', 'id');
		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		if( !empty($admin_rumahku) ) {
    		$result = $this->User->doToggle( $id );
    	} else {
    		$result = $this->User->doRemoveParent( $id );
    	}

		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    public function client_edit (){
		$module_title = __('Edit Profil');
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $this->user_id,
			),
		), array(
			'status' => 'semi-active',
		));

		if( !empty($user) ) {
			$id = $this->RmCommon->filterEmptyField($user, 'User', 'id');
			$user = $this->User->UserProfile->getMerge($user, $id);

			$data = $this->request->data;
			$result = $this->User->doEdit( $this->user_id, $user, $data );
			$this->RmCommon->setProcessParams($result);

			$this->RmCommon->_callRequestSubarea('UserProfile');
			$this->RmCommon->_layout_file(array(
				'fileupload',
			));
    		
    		$this->set('active_menu', 'profil');
			$this->set(compact(
				'subareas', 'user', 
				'module_title'
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	function client_security( $tabs_action_type = 'change_email' ) {

		if( !empty($tabs_action_type) ) {
			
			$data = $this->request->data;
			$user['User'] = $this->Auth->user();
			$client_id = $this->RmCommon->filterEmptyField($user['User'], 'UserClient', 'id');

			if( $tabs_action_type == 'change_email' ) {
				$result = $this->User->doEditEmail( $this->user_id, $user, $data, $this->data_company);
			} else if( $tabs_action_type == 'change_password' ) {
				if( !empty($data) ) {
					$data['User'] = $this->RmCommon->filterEmptyField($data, 'UserClient');
				}
				$data = $this->RmUser->_callDataRegister($data);
				$result = $this->User->UserClient->doEditPassword( $client_id, $user, $data, $this->data_company);
			}

			if( isset($result['status']) && $result['status'] == 'success' ) {
				$this->RmUser->refreshAuth($this->user_id);
			}
			
			$this->RmCommon->setProcessParams($result);
			if( $tabs_action_type == 'change_password' ){
				$this->request->data = $this->RmCommon->_callUnset(array(
					'UserClient' => array(
						'current_password',
						'new_password',
						'new_password_confirmation',
					),
				), $data);
			}
		}

		$module_title = $title_for_layout = __('Keamanan');
		$this->set('active_menu', __('pengaturan'));
		$this->set(compact(
			'module_title', 'title_for_layout',
			'tabs_action_type'
		));
	}

	public function client_photo_crop() {
		$urlRedirect = array(
            'controller' => 'users',
            'action' => 'edit',
            'client' => true,
        );
		$save_path = Configure::read('__Site.profile_photo_folder');

		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $this->user_id,
			),
		));

		if( !empty($user) ) {
			if( !empty($this->request->data) ) {
				$data = $this->request->data;

				$paramPhoto = $this->RmImage->_callDataPosition($data, 'User');
				$photoName = $this->RmImage->cropPhoto($paramPhoto, $save_path);

				$result = $this->User->doCroppedPhoto( $this->user_id, $data, $photoName );
				$this->RmCommon->setProcessParams($result, $urlRedirect);
			}

    		$this->set('module_title', __('Crop Foto Profil'));
    		$this->set('active_menu', 'profil');
			$this->set(compact(
				'user', 'save_path'
			));

		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_invite_client( $client_id = false ) {
		if( !empty($client_id) ) {
			$result = $this->User->UserClient->doInviteClient( $client_id, $this->data_company );
			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Klien tidak ditemukan'));
		}
	}

	public function client_verify( $client_id = false, $token = false ) {
		$client = $this->User->UserClient->getData('first', array(
			'conditions' => array(
				'UserClient.id' => $client_id,
				'UserClient.token' => $token,
				'UserClient.company_id' => Configure::read('Principle.id'),
			),
		));
		$loginRedirect = array(
			'controller' => 'users',
			'action' => 'login',
			'client' => true,
		);

		if( !empty($client) ) {
			$user_id = $this->RmCommon->filterEmptyField($client, 'UserClient', 'user_id');
			$user = $this->User->getMerge(array(), $user_id);
			$user['User']['UserClient'] = $client['UserClient'];

			$this->Auth->login($user, false, true);
			$this->redirect(array(
				'controller' => 'users',
				'action' => 'verify_password',
				$token,
				'client' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Token ini mungkin telah digunakan atau sudah tidak berlaku.'), 'error', $loginRedirect);
		}
	}

	public function client_verify_password( $token = false ) {

		$user = $this->Auth->user();
		$client_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'id');
		$client = $this->User->UserClient->getData('first', array(
			'conditions' => array(
				'UserClient.id' => $client_id,
				'UserClient.token' => $token,
				'UserClient.company_id' => Configure::read('Principle.id'),
			),
		));
		$loginRedirect = array(
			'controller' => 'users',
			'action' => 'login',
			'client' => true,
		);

		if( !empty($client) ) {
			if( !empty($this->request->data) ) {
				$data = $this->request->data;
				
				$new_password = $this->RmCommon->filterEmptyField($data, 'UserClient', 'new_password');
				$data['UserClient']['auth_password'] = $this->Auth->password($new_password);

				$result = $this->User->UserClient->doVerifyNewPassword( $data, $client_id, $user, $token);
				if( $result['status'] == 'success' ) {
					$this->RmUser->refreshAuth($this->user_id);
				}
				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'ebrosurs',
					'action' => 'index',
					'client' => true,
				));
			}
		} else {
			$this->RmCommon->redirectReferer(__('Klien tidak ditemukan. Token ini mungkin telah digunakan atau sudah tidak berlaku.'), 'error', $loginRedirect);
		}
		
		$module_title = $title_for_layout = __('Ganti Password');
		$this->set(compact(
			'module_title', 'title_for_layout'
		));
	}

	public function admin_verify($userID = null, $token = null){
		$record	= $this->User->UserConfig->getData('first', array(
			'conditions' => array(
				'UserConfig.user_id'	=> $userID, 
				'UserConfig.token'		=> $token, 
			)
		));

		if($record){
			$record	= $this->User->getMerge($record, $userID);
			$record = $this->User->getMergeList($record, array(
				'contain' => array(
					'UserProfile', 
					'UserCompany', 
					'UserCompanyConfig', 
				), 
			));

			$currentCompany	= $this->RmCommon->filterEmptyField($this->data_company, 'UserCompany', 'id');
			$userCompany	= $this->RmCommon->filterEmptyField($record, 'UserCompany', 'id');

			if($currentCompany == $userCompany){
				$this->Auth->login($record, false, true);
				$this->redirect(array(
					'controller'	=> 'users',
					'action'		=> 'verify_password',
					'admin'			=> true,
					$token, 
				));
			}
		}

		$loginRedirect = array(
			'controller'	=> 'users',
			'action'		=> 'login',
			'admin'			=> true,
		);

		$this->RmCommon->redirectReferer(__('Token ini mungkin telah digunakan atau sudah tidak berlaku.'), 'error', $loginRedirect);
	}

	public function admin_verify_password($token = null){
		$authUserID	= $this->Auth->user('id');
		$record		= $this->User->_callVerifyToken($token);
		$configID	= $this->RmCommon->filterEmptyField($record, 'UserConfig', 'id');

		if($record){

			if($this->request->data){
				$data = $this->request->data;

				$this->User->id = $authUserID;
				$this->User->set($data);

				if($this->User->validates()){
					$userConfigID	= $this->RmCommon->filterEmptyField($record, 'UserConfig', 'id');
					$newPassword	= $this->RmCommon->filterEmptyField($data, 'User', 'new_password');
					$authPassword	= $this->Auth->password($newPassword);

					$data = array_merge_recursive($data, array(
						'User' => array(
							'id'		=> $authUserID, 
							'password'	=> $authPassword, 
						), 
						'UserConfig' => array(
							'id'	=> $userConfigID, 
							'token'	=> String::uuid(), 
						)
					));

					if($this->User->saveAll($data)){
					//	reload auth data
						$this->RmUser->refreshAuth($authUserID);

						$dashboardUrl	= Configure::read('User.dashboard_url');
						$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');

						$this->RmCommon->setCustomFlash(__('Selamat Datang <strong>%s</strong>', $fullName), 'success');
						$this->redirect($dashboardUrl);
					}
				}
				else{
					$this->RmCommon->redirectReferer(__('Gagal mengubah password'), 'error');
					$this->RmCommon->setValidationError($this->User->validationErrors);
				}

				unset($this->request->data);
			}
		}
		else{
			$loginRedirect = array(
				'controller'	=> 'users',
				'action'		=> 'login',
				'admin'			=> true,
			);

			$this->RmCommon->redirectReferer(__('Token ini mungkin telah digunakan atau sudah tidak berlaku.'), 'error', $loginRedirect);
		}

		$module_title = $title_for_layout = __('Ganti Password');
		$this->set(compact('module_title', 'title_for_layout'));
	}

	public function client_login() {
	//	user already logged in, redirect them to dashboard
		$user = $this->Auth->user();
		if( $user ) {
			$dashboardUrl = Configure::read('User.dashboard_url');
			$this->redirect($dashboardUrl);
		} else {
			if ( !empty($this->request->data) ) {
				$data = $this->request->data;
				$this->User->set($data);

				if ( $this->Auth->login() ) {
					$user = $this->Auth->user();

					$is_brochure = Configure::read('Config.Company.data.UserCompanyConfig.is_brochure');

					if($is_brochure){
						$this->redirect(array(
							'controller' => 'ebrosurs',
							'action' => 'index',
							'client' => true,
						));
					}else{
						$this->redirect(array(
							'controller' => 'users',
							'action' => 'agents',
							'client' => true,
						));
					}
				} else {
					$result = array(
						'msg' => __('Gagal melakukan login, username atau password Anda tidak valid'),
						'status' => 'error'
					);
					$this->RmCommon->setProcessParams($result);

					if( !empty($this->request->data['User']['password']) ) {
						unset($this->request->data['User']['password']);
					}
				}
			}

			$this->layout = 'client_login';
		}
	}

	public function client_forgotpassword(){
		$result = false;
		$urlRedirect = false;
        if ( !empty($this->request->data) ) {
        	$data = $this->request->data;
        	$email = $this->RmCommon->filterEmptyField($data, 'User', 'forgot_email');

        	$data['User']['forgot_email'] = $email = trim($email);
        	$this->User->set($data);

        	if( $this->User->validates() ) {
				$user = $this->User->getData('first', array(
					'conditions'=>array(
						'User.email' => $email,
						'User.group_id' => 10,
					),
				), array(
					'status' => 'all',
				));

				if(!empty($user)) {
        			$id = $this->RmCommon->filterEmptyField($user, 'User', 'id');
        			$active = $this->RmCommon->filterEmptyField($user, 'User', 'active');

					if( empty($active) ){
						$this->RmCommon->setCustomFlash(__('Harap melakukan aktivasi akun terlebih dahulu sebelum merubah password'), 'error');
					} else {
						$full_name = $this->RmUser->_getUserFullName($user);
						$reset_code = $this->RmUser->_generateCode('reset');

						$result = $this->User->PasswordReset->doSave(array(
							'company_id' => $this->parent_id,
							'user_id' => $id,
				            'email' => $email,
				            'reset_code' => $reset_code,
            				'full_name' => $full_name,
            				'group' => 'client',
						));
						$urlRedirect = array(
							'controller' => 'users',
							'action' => 'login',
							'client' => true,
						);
					}
				} else {
					$result = array(
						'msg' => __('Gagal melakukan reset password, Klien tidak ditemukan.'),
						'status' => 'error'
					);
				}
			} else {
				$result = array(
					'msg' => __('Gagal melakukan reset password, mohon masukkan email Anda dengan benar'),
					'status' => 'error'
				);
			}
		}

		$this->RmCommon->setProcessParams($result, $urlRedirect);
		$this->set('_greeting', __('Mohon masukkan Email Anda untuk proses pengiriman Reset Password'));
    	$this->layout = 'client_login';
    }

    function client_password_reset( $reset_code = false ) {
		$value = $this->User->PasswordReset->getData('first', array(
			'conditions' => array(
				'PasswordReset.reset_code' => $reset_code,
				'company_id' => $this->parent_id,
			),
		));
		$loginRedirect = array(
			'controller' => 'users',
			'action' => 'login',
			'client' => true,
		);

		if( !empty($value) ) {
			$check_expired = $this->RmUser->_checkExpiredResetPassword($value);

			if($check_expired) {
				if (!empty($this->request->data)) {
					$data = $this->request->data;
					$new_password = $this->RmCommon->filterEmptyField($data, 'UserClient', 'new_password');

					$id = $this->RmCommon->filterEmptyField($value, 'PasswordReset', 'id');
					$user_id = $this->RmCommon->filterEmptyField($value, 'PasswordReset', 'user_id');

					$client = $this->User->UserClient->getMerge(array(), $user_id, $this->parent_id);
					$client_id = $this->RmCommon->filterEmptyField($client, 'UserClient', 'id');

					$this->User->UserClient->id = $client_id;
					$this->User->UserClient->set($data);

					if( $this->User->UserClient->validates() ) {
						$data['UserClient']['password'] = $this->Auth->password($new_password);

						if ( $this->User->UserClient->save($data) ) {
							$this->User->PasswordReset->set('status', 0);
							$this->User->PasswordReset->id = $id;
							$this->User->PasswordReset->save();

							$this->RmCommon->redirectReferer(__('Berhasil mengubah password. Silakan masukkan password baru Anda'), 'success', $loginRedirect);
						}
					}

					unset($this->request->data);
				}

				$title_for_layout = $module_title = __('Reset Password');
				$this->set('_greeting', __('Mohon masukkan password baru Anda'));
				$this->set(compact(
					'module_title', 'title_for_layout'
				));
		    	$this->layout = 'login';
			} else {
				$this->RmCommon->redirectReferer(__('Kode reset sudah expired. Silakan lakukan lupa kata sandi untuk pengiriman kode kembali.'), 'error', $loginRedirect);
			}
		} else {
			$this->RmCommon->redirectReferer(__('Kode reset tidak valid. Kode ini mungkin telah digunakan atau sudah tidak berlaku.'), 'error', $loginRedirect);
		}
	}

	function api_message( $msg = null, $status = null ) {
		$this->RmCommon->setCustomFlash($msg, $status);
	}

	public function admin_non_companies(){
		$module_title = __('Daftar User Non Companies');
		$options =  $this->User->_callRefineParams($this->params, array(
			'group' => array(
				'User.id',
			),
			'order' => array(
				'User.created' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->User->getData('paginate', $options, array(
			'status' => 'semi-active',
			'role' => 'user',
			'admin' => true,
		));
		$values = $this->paginate('User');
			
		if( !empty($values) ){
			foreach( $values as $key => &$value ) {
				$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

				$value = $this->User->getAllNeed($value, $id);
				$value = $this->User->UserRemoveAgent->getMerge( $value, $id );
				$value = $this->RmUser->_callGetLogView($id, $value);
			}
		}

		$this->RmCommon->_callDataForAPI($values);

		$this->set('active_menu', 'non_company');
		$this->set(compact(
			'values', 'module_title'
		));
	}

	public function admin_edit_non_companies ( $id = false ) {
		$save_path = Configure::read('__Site.profile_photo_folder');

		$module_title = __('Edit User Non Companies');
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
			),
		), array(
			'role' => 'user',
			'status' => 'semi-active',
			'company' => true,
			'admin' => true,
		));

		$groups = $this->User->Group->getData('list', array(
			'conditions' => array(
				'Group.is_prime' => 1,
				'Group.id <>' => 3,
			),
			'order' => array(
				'Group.id' => 'ASC'
			),
		));	

		if( !empty($value) ) {
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'User', 'photo');
			$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

			$value = $this->User->getAllNeed( $value, $id );
			$value = $this->User->getMerge( $value, $parent_id, false, 'Parent' );

			$data = $this->request->data;
			$data = $this->RmUser->_callDataRegister($data);
			
			$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));

			$result = $this->User->doEditNonCompanies( $id, $value, $data);

			//	if user upload new photo, delete old photo
			if(isset($this->request->data['User']['photo']['name'])){
				$uploadPhoto = $this->request->data['User']['photo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'non_companies',
				'admin' => true,
			));

			$this->RmCommon->_callRequestSubarea('UserProfile');
			$this->RmCommon->_callDataForAPI($value);

			$subareaID = Common::hashEmptyField($this->data, 'UserProfile.subarea_id');

			if($subareaID){
				$location		= $this->RmCommon->getViewLocation($subareaID);
				$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

				$this->request->data['UserProfile']['location_name'] = $locationName;
			}

			$this->set('active_menu', 'non_company');
			$this->set(compact(
				'module_title', 'groups'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Agen tidak ditemukan'));
		}
	}

	public function admin_remove_non_companies() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'User', 'id');

		$this->loadModel('User');
    	$result = $this->User->deleteNonCompanies( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    public function api_profile_photo() {
    	$result = array(
			'msg' => __('Gagal melakukan upload foto profil'),
			'status' => 'error'
		);

		$files = $this->RmCommon->filterEmptyField($this->request->data, 'Files', 'data');

		if( !empty($files) ) {
			$info = array();
        	$userFolder = Configure::read('__Site.profile_photo_folder');
			$prefixImage = String::uuid();

			//	capture old photo, image file has to be deleted when new image file uploaded
			$user = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $this->user_id
				),
			), array(
				'company' => true,
				'admin' => true,
			));
			
			$oldPhoto	= NULL;

			if($user){
				$oldPhoto = isset($user['User']['photo']) && $user['User']['photo'] ? $user['User']['photo'] : NULL;
			}

			$file_name = $this->RmCommon->filterEmptyField($files, 'name');
			$data = $this->RmImage->upload($files, $userFolder, $prefixImage);
			$photo_name = $this->RmCommon->filterEmptyField($data, 'imageName');

			$data_save = array(
				'User' => array(
					'photo' => $photo_name,
				)
			);

			$data = array_merge($data, $data_save);
			
			$file = $this->User->doSavePhoto($data, $this->user_id);

			if(!empty($file_name) && !empty($oldPhoto) && !empty($file['status']) && $file['status'] == 'success'){
				//	delete old photo
				$permanent = FALSE;
				$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $userFolder, NULL, $permanent);

				$this->RmUser->refreshAuth($this->user_id);
			}

			if(!empty($file['User'])){
				$file['User'] = array_merge($file['User'], $data['User']);
			}

			$result = array(
	        	'msg' => __('Berhasil mengunggah foto profil'),
	        	'status' => 'success'
	        );

	        $this->set('data', $file);
		}

		$this->RmCommon->setProcessParams($result);
	}

	function companies(){
		$group_id = $this->RmCommon->filterEmptyField($this->data_company, 'User', 'group_id' );

		if( $group_id != 4 ) {
			$this->RmCommon->redirectReferer(__('Halaman tidak ditemukan'));
		} else {
			$options = $this->User->getData('paginate', false, array(
				'status' => 'semi-active',
				'company' => true,
				'admin_rumahku' => false,
				'admin' => false,
				'role' => 'principle',
			));
			$conditions = $this->RmCommon->filterEmptyField($options, 'conditions');

			$options =  $this->User->UserCompany->_callRefineParams($this->params, array(
				'conditions' => $conditions,
				'contain' => array(
					'User',
				),
				'order' => array(
					'UserCompany.created' => 'DESC',
					'UserCompany.name' => 'ASC',
				),
				'limit' => 12,
			));
			$this->RmCommon->_callRefineParams($this->params);

			$this->paginate = $this->User->UserCompany->getData('paginate', $options, array(
				'company' => true,
				'admin' => false,
			));
			$values = $this->paginate('UserCompany');

			if( !empty($values) ) {
				foreach ($values as $key => &$value) {
					$value = $this->RmCommon->_callUnset(array(
						'Region',
						'City',
						'Subarea',
					), $value);
								
					$value = $this->User->UserCompany->getMergeList($value, array(
						'contain' => array(
							'User',
							'Region' => array(
								'position' => 'inside',
								'cache' => true,
							),
							'City' => array(
								'position' => 'inside',
								'cache' => true,
							),
							'Subarea' => array(
								'position' => 'inside',
								'cache' => array(
									'name' => 'Subarea',
									'config' => 'subareas',
								),
							),
						),
					));
					$value = $this->User->getMergeList($value, array(
						'contain' => array(
							'UserProfile',
							'UserConfig',
						),
					));
				}
			}

			$title_for_layout = __('Perusahaan Properti');
			$description_for_layout = __('Perusahaan Properti Terbaik dan Terpercaya!');

			$this->RmUser->_callBeforeViewUser();
			$this->set(array(
				'module_title' => __('Daftar Perusahaan'),
				'title_for_layout' => $title_for_layout,
				'description_for_layout' => $description_for_layout,
				'active_menu' => 'companies',
				'values' => $values,
			));
		}
	}

	function company( $id = false, $slug = false ){
		$group_id = $this->RmCommon->filterEmptyField($this->data_company, 'User', 'group_id' );

		if( $group_id != 4 ) {
			$this->RmCommon->redirectReferer(__('Halaman tidak ditemukan'));
		} else {
			$module_title = __('Profil Perusahaan');
			$options = array(
				'conditions' => array(
					'UserCompany.id' => $id,
				),
			);

			if( empty($slug) ) {
				$options['conditions']['OR'] = array(
					array(
						'UserCompany.slug' => NULL,
					),
					array(
						'UserCompany.slug' => '',
					),
				);
			} else {
				$options['conditions']['slug'] = $slug;
			}

			$value = $this->User->UserCompany->getData('first', $options, array(
				'company' => true,
			));

			if( !empty($value) ){
				$user_id = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'user_id');
				$name = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'name');
				$description = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'description', __('Perusahaan Properti Terbaik dan Terpercaya!'), array(
					'strip_tags' => true,
				));

				$value = $this->User->UserCompany->getMergeList($value, array(
					'contain' => array(
						'User',
						'Region' => array(
							'position' => 'inside',
							'cache' => true,
						),
						'City' => array(
							'position' => 'inside',
							'cache' => true,
						),
						'Subarea' => array(
							'position' => 'inside',
							'cache' => array(
								'name' => 'Subarea',
								'config' => 'subareas',
							),
						),
					),
				));
				$value = $this->User->getMergeList($value, array(
					'contain' => array(
						'UserProfile',
						'UserConfig',
						'UserCompanyConfig',
					),
				));

				$this->paginate = $this->User->getData('paginate', array(
					'conditions' => array(
						'User.parent_id' => $user_id,
					),
					'limit' => 24,
				), array(
					'status' => 'semi-active',
					'role' => 'agent',
				));
				$agents = $this->paginate('User');
				$agents = $this->User->getMergeList($agents, array(
					'contain' => array(
						'UserProfile',
						'UserConfig',
					),
				));

				$this->RmUser->_callBeforeViewCompany( $value );
				$this->set(array(
					'module_title' => $name,
					'title_for_layout' => $name,
					'description_for_layout' => $description,
					'value' => $value,
					'agents' => $agents,
				));
			}else{
				$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
			}
		}
	}

	// S: Admin Rumahku
	public function admin_rku_admins(){
		$module_title = __('List Admin Primesystem');
		$options =  $this->User->_callRefineParams($this->params, array(
			'conditions' => array(),
			'group' => array(
				'User.id',
			),
			'order' => array(
				'User.created' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);
		$this->paginate = $this->User->getData('paginate', $options, array(
			'status' => 'active',
			'role' => 'adminRku',
			'admin' => true,
		));
		$values = $this->paginate('User');

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$id = Common::hashEmptyField($value, 'User.id');
				$value = $this->User->getMergeList($value, array(
					'contain' => array(
						'UserProfile',
						'UserConfig',
						'Group',
					),
				));
				$value = $this->RmUser->_callGetLogView($id, $value);
			}
		}

		$groups = $this->User->Group->getData('list', false, array(
			'role' => 'adminRku',
		));

		$this->set('active_menu', 'rku_admin');
		$this->set(compact(
			'values', 'module_title', 'groups'
		));
	}

	// add rku admin
	public function admin_add_rku_admin() {
		$module_title = __('Tambah Admin Primesystem');
		$urlBack = array(
			'controller' => 'users',
			'action' => 'rku_admins',
			'admin' => true,
		);

		$save_path = Configure::read('__Site.profile_photo_folder');
		$data = $this->request->data;
		$data = $this->RmUser->_callDataRegister($data);
		$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
            'keep_file_name' => true,
		));

		$group_id = Common::hashEmptyField($data, 'User.group_id');
		$result = $this->User->doAdd( $data, $this->parent_id, false, $group_id);

		$this->RmCommon->setProcessParams($result, $urlBack);

		$this->request->data = $this->RmCommon->_callUnset(array(
			'User' => array(
				'password',
				'password_confirmation',
			),
		), $this->request->data);
		$this->request->data = $this->RmUser->_callBeforeView($this->request->data);

		$this->RmCommon->_callRequestSubarea('UserProfile');

		$group_rku_admin = $this->User->Group->getData('list', false, array(
			'role' => 'adminRku',
		));

		$this->set('active_menu', 'rku_admin');

		$this->set(compact(
			'user_company', 'subareas', 'group_rku_admin',
			'module_title', 'urlBack'
		));
	}

	// edit rku admin
	public function admin_edit_rku_admin ( $id = false ) {
		$save_path = Configure::read('__Site.profile_photo_folder');

		$module_title = __('Edit Admin Primesystem');
		$value = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id,
			),
		), array(
			'status' => 'semi-active',
			'role' => 'adminRku',
			'admin' => true,
		));

		if( !empty($value) ) {
			$urlBack = array(
				'controller' => 'users',
				'action' => 'rku_admins',
				'admin' => true,
			);
			$oldPhoto = $this->RmCommon->filterEmptyField($value, 'User', 'photo');

			$value = $this->User->getMergeList($value, array(
				'contain' => array(
					'UserProfile',
					'UserConfig',
					'Group',
				),
			));
			
			$data = $this->request->data;
			$data = $this->RmUser->_callDataRegister($data);
			$data = $this->RmImage->_uploadPhoto($data, 'User', 'photo', $save_path, true, array(
	            'keep_file_name' => true,
			));
			$data = $this->RmUser->_callUserBeforeSave( $data, $value );
			$result = $this->User->doEdit( $id, $value, $data);

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['User']['photo']['name'])){
				$uploadPhoto = $this->request->data['User']['photo']['name'];

				if($uploadPhoto && $oldPhoto && isset($result['status']) && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$group_rku_admin = $this->User->Group->getData('list', false, array(
				'role' => 'adminRku',
			));

			$this->RmCommon->setProcessParams($result, $urlBack);

			$this->RmCommon->_callRequestSubarea('UserProfile');

			$subareaID = Common::hashEmptyField($this->data, 'UserProfile.subarea_id');

			if($subareaID){
				$location		= $this->RmCommon->getViewLocation($subareaID);
				$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

				$this->request->data['UserProfile']['location_name'] = $locationName;
			}

			$this->set('active_menu', 'rku_admin');

			$this->set(array(
				'module_title' => $module_title,
				'urlBack' => $urlBack,
				'user' => $value, 
				'group_rku_admin' => $group_rku_admin, 
			));
		} else {
			$this->RmCommon->redirectReferer(__('Admin tidak ditemukan'));
		}
	}
	// E: Admin Rumahku

	function client_agents(){
		$module_title = __('Agen');

		$this->loadModel('UserClientRelation');
		$options = $this->UserClientRelation->_callRefineParams($this->params, array(
			'conditions' => array(
				'UserClientRelation.user_id' => $this->user_id,
				'UserClientRelation.company_id' => $this->parent_id,
				'User.deleted' => false,
				'User.status' => true,
			),
			'contain' => array(
				'User' => array(
					'className' => 'User',
					'foreignKey' => 'agent_id',
				),
			),
		));
		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate = $this->UserClientRelation->getData('paginate', $options);
		$values = $this->paginate('UserClientRelation');
	
		if( !empty($values) ) {
			foreach( $values as $key => $value ) {
				$user_id = $this->RmCommon->filterEmptyField($value, 'UserClientRelation', 'agent_id');
				$value = $this->User->getMerge( $value, $user_id);
				$value = $this->User->UserProfile->getMerge( $value, $user_id);

				$region_id = $this->RmCommon->filterEmptyField( $value, 'UserProfile', 'region_id' );
				$city_id = $this->RmCommon->filterEmptyField( $value, 'UserProfile', 'city_id' );

				$value = $this->User->UserProfile->Region->getMerge( $value, $region_id, 'Region', array(
					'cache' => array(
						'name' => __('Region.%s', $region_id),
					),
				));
				$value = $this->User->UserProfile->City->getMerge( $value, $city_id, 'City', 'City.id', array(
					'cache' => __('City.%s', $city_id),
				));

				$values[$key] = $value;
			}
		}

		$this->set('active_menu', 'agen');
		$this->set(compact(
			'module_title', 'values'
		));
	}

	function admin_list_principle_prime(){
		$date = date('Y-m-d');

		$this->User->UserCompanyConfig->virtualFields['full_name_info'] = 'CONCAT(UserCompany.name, " | ", UserCompanyConfig.domain)';
		$values = $this->User->UserCompanyConfig->getData('all', array(
			'conditions' => array(
				'DATE_FORMAT(UserCompanyConfig.end_date, \'%Y-%m-%d\') >=' => $date,
				'DATE_FORMAT(UserCompanyConfig.live_date, \'%Y-%m-%d\') <=' => $date,
			),
			'contain' => array(
				'UserCompany'
			),
			'fields' => array(
				'UserCompanyConfig.domain', 'UserCompanyConfig.full_name_info'
			)
		));

		$this->RmCommon->_callDataForAPI($values, 'manual');

		$this->RmCommon->renderRest();
	}

	function api_notif(){
		$notification = $this->User->Notification->getNotif();
		$message = $this->User->Message->getNotif();

		$notification['cnt'] = $this->RmCommon->filterEmptyField($notification, 'cnt', false, 0);
		$message['cnt'] = $this->RmCommon->filterEmptyField($message, 'cnt', false, 0);

		$this->set(compact('notification', 'message'));
	}

	public function admin_inactived_agent($id = false){
		$data = $this->request->data;
		$ids = !empty($id) ? array($id) : false;
		$values = false;

		if($ids){
			$value = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $ids,
				),
			), array(
				'status' => array(
					'non-active',
					'active',
				),
			));

			$value = $this->User->getMergeList($value, array(
				'contain' => array(
					'UserProfile' => array(
						'Region',
						'City',
						'Subarea',
					),
				),
			));

			if(!empty($data['UserActivedAgent'])){
				$result = $this->User->UserActivedAgent->doSaveActived($data, $value);

				if( !empty($result['status']) && $result['status'] == 'success' ){
					$this->set('_flash', false);
				}

				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
				));
			}

			$this->set(compact(
		 		'value'
		 	));
		}
	}

	public function admin_actived_agent($id = false) {
		$data = $this->request->data;
		$ids = !empty($id) ? array($id) : false;
		$values = false;

		if($ids){
			$value = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $ids,
				),
			), array(
				'status' => array(
					'active',
				),
			));

			$value = $this->User->getMergeList($value, array(
				'contain' => array(
					'UserProfile' => array(
						'Region',
						'City',
						'Subarea',
					),
				),
			));

			$value = $this->RmUser->getInvestment($value);
			$data = $this->RmUser->beforeSaveActived( $data, $value);
			$result = $this->User->UserActivedAgent->doSave($data, $value);

			if(!empty($result['status']) && $result['status'] == 'success' && !empty($result['id'])){
				$this->set('_flash', false);
			}

			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true,
			));

			$this->set(compact(
		 		'value'
		 	));
		}
    }

    function admin_client_info($recordID = NULL){
    	$this->loadModel('UserClient');
    	$title = __('Daftar Klien');

		$user = $this->RmUser->getUser($recordID);

		if($user){
			$this->RmUser->_callRoleActiveMenu($user);

			$group_id = Common::hashEmptyField($user, 'User.group_id');
			$params = $this->params->params;

			$this->User->UserClient->bindModel(array(
				'hasOne' => array(
					'UserClientRelation' => array(
						'foreignKey' => false,
						'conditions' => array(
							'UserClientRelation.user_id = UserClient.user_id',
							'UserClientRelation.company_id = UserClient.company_id',
						),
					),
				)
			), false);

			if(in_array($group_id, array('3', '4'))){
				$options = array(
					array(
						'OR' => array(
							'UserClient.company_id' => $recordID,
							'UserClientRelation.company_id' => $recordID,
						),
					),
				);
			} else {
				$options = array(
					array(
						'OR' => array(
							'UserClient.company_id' => $this->parent_id,
							'UserClientRelation.company_id' => $this->parent_id,
						),
					),
					array(
						'OR' => array(
							'UserClient.agent_id' => $recordID,
							'UserClientRelation.agent_id' => $recordID,
						),
					),
				);
			}

			$options = $this->User->UserClient->_callRefineParams($params, array(
				'conditions' => array_merge( $options, array(
					'UserClient.status' => 1,
				)),
				'order' => array(
					'UserClient.created' => 'DESC',
				),
				'contain' => array(
					'UserClientRelation',
					'User',
				),
				'limit' => Configure::read('__Site.config_new_table_pagination'),
			));

			$this->RmCommon->_callRefineParams($params);
			$user_options = array_merge($options, $this->User->getData('paginate', $options, array(
				'status' => 'all',
			)));

			$this->paginate = $this->UserClient->getData('paginate', $user_options);
			$values = $this->paginate('UserClient');

			if( !empty($values) ) {
				foreach( $values as $key => $value ) {
					$value = $this->User->UserClient->getMergeList($value, array(
						'contain' => array(
							'UserCompany' => array(
								'primaryKey' => 'user_id',
								'foreignKey' => 'company_id',
							), 
							'ClientType', 
							'Agent' => array(
								'uses' => 'User',
								'primaryKey' => 'id',
								'foreignKey' => 'agent_id',
								'elements' => array(
									'status' => 'all',
								),
							), 
						), 
					));

					$values[$key] = $value;
				}
			}

			$this->RmCommon->_callDataForAPI($values, 'manual');

			$clientTypes = $this->User->ClientType->find('list');

			$this->set(array(
				'module_title' => $title,
				'title_for_layout' => $title,
				'values' => $values,
				'currUser' => $user,
				'recordID' => $recordID,
				'clientTypes' => $clientTypes,
				'active_tab' => 'Klien',
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

	public function admin_index() {
		$module_title = __('Daftar User');
		$values = $this->RmUser->_callBeforeViewListUser();

		$this->set('active_menu', 'users');
		$this->set(compact(
			'values', 'module_title'
		));

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	public function admin_list_package_partner(){

    	$module_title = __('Daftar Package');
    	$this->loadModel('UserIntegratedAddonPackage');
		
		$options =  $this->UserIntegratedAddonPackage->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));

        $this->paginate = $this->UserIntegratedAddonPackage->getData('paginate', $options, array(
        	'status' => 'active',
        ));
		$values = $this->paginate('UserIntegratedAddonPackage');
debug($values);die();
		$this->set('active_menu', 'list_package');
		$this->set(compact(
			'values', 'module_title'
		));
	}

	// form register user integrated
	function admin_register_integration() {
		$this->loadModel('Setting');

		$module_title = $title_for_layout = __('Daftarkan Integrasi');
		$isAjax	= $this->RequestHandler->isAjax();
		$result	= array();
		$redirectURL = false;
		
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $this->user_id
			),
		), array(
			'status' => 'semi-active',
		));

		if( !empty($user) ) {
			$value_url_back = array(
	            'controller' => 'users',
	            'action' => 'account',
	            'admin' => true,
	        );

			$group_id = Common::hashEmptyField($user, 'User.group_id');
			$user = $this->User->getAllNeed($user, $this->user_id, $group_id);

			$req_data = $this->request->data;

			$data = $this->RmUser->callRegisterIntegratedBeforeSave( $user, $req_data );
			$result	= array('data' => $data);
			$save = $this->User->UserIntegratedOrder->doRegister( $data, $req_data );
			
			if (!empty($req_data)) {
				$result = array_replace_recursive($data, $save);
			}

			$status		= $this->RmCommon->filterEmptyField($result, 'status');
			$message	= $this->RmCommon->filterEmptyField($result, 'msg');

			if($isAjax) {
				if($status == 'success'){
					$this->set('result', $result);
				}

				$redirectOpts = array(
					'ajaxFlash'		=> true, 
					'ajaxRedirect'	=> false,
				);
			} else {
				$this->RmCommon->setCustomFlash(__($message), $status);

				if($status == 'success'){
					$paymentData	= $this->RmCommon->filterEmptyField($result, 'data', 'UserIntegratedOrderAddon');
					$invoiceID		= $this->RmCommon->filterEmptyField($paymentData, 'id');
					$invoiceNumber	= $this->RmCommon->filterEmptyField($paymentData, 'invoice_number');
					$userID			= $this->RmCommon->filterEmptyField($paymentData, 'user_id');
					$invoiceToken	= md5($invoiceNumber . $invoiceID . $userID);

					$redirectURL = array(
						'controller'	=> 'users', 
						'action'		=> 'checkout_addon',
						'admin' => true,
						$invoiceID, 
						$invoiceNumber, 
						$invoiceToken, 
					);
				}

				$redirectOpts = array('redirectError' => false);
			}
			// debug($result);die();

			if( !empty($result['data']) && $status == 'error' ){
				// $r123_package_id = Common::hashEmptyField($result, 'data.UserIntegratedOrderAddon.r123_package_id');
				$olx_package_id = Common::hashEmptyField($result, 'data.UserIntegratedOrderAddon.olx_package_id');

				$result['data'] = $this->UserIntegratedAddonPackage->getMerge($result['data'], $olx_package_id);
			}

			$this->RmCommon->setProcessParams($result, $redirectURL, $redirectOpts);
			$this->RmCommon->_callRequestSubarea('UserIntegratedOrder');

			$get_forms = $this->Setting->find('all', array(
				'conditions' => array(
					'slug' => 'form-register-integration',
					'temp' => 1,
				),
			));

			$this->set('active_menu', 'dashboard');
			$this->set(compact(
				'module_title', 'title_for_layout', 'value_url_back', 'get_forms'
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	// after order, form checkout addon
	public function admin_checkout_addon($recordID = NULL, $invoiceNumber = NULL){
		$userID = $this->Auth->user('id');
		$dokumallID = Configure::read('__Site.doku_mall_id');
		$dokuSharedKey = Configure::read('__Site.doku_shared_key');
		$isCompanyAdmin	= $this->RmCommon->_isCompanyAdmin();
		$options = array(
			'conditions' => array(
				'UserIntegratedOrderAddon.id'				=> $recordID,
				'UserIntegratedOrderAddon.user_id'			=> $userID,
				'UserIntegratedOrderAddon.invoice_number'	=> $invoiceNumber,
				'UserIntegratedOrderAddon.payment_status'	=> array('pending', 'process', 'failed'),
			),

		);

		$record = $this->User->UserIntegratedOrderAddon->getData('first', $options);

		if($record){
			$userID			= $this->RmCommon->filterEmptyField($record, 'UserIntegratedOrderAddon', 'user_id');
			$expiredDate	= $this->RmCommon->filterEmptyField($record, 'UserIntegratedOrderAddon', 'expired_date');

			if(strtotime($expiredDate) < strtotime(date('Y-m-d H:i:s'))){
				$message = __('Maaf, Anda tidak bisa melanjutkan transaksi karena Invoice <strong>%s</strong> sudah Kadaluarsa.', $invoiceNumber);
				$this->RmCommon->setCustomFlash($message, 'error');
				$this->redirect(array('controller' => 'users','action' => 'account','admin' => true,));
			}

		//	update status jadi process
			$record	= $this->User->UserIntegratedOrderAddon->setPaymentStatus($record, 'process');
			$data	= $this->request->data;
			$isAjax	= $this->RequestHandler->isAjax();
			$result	= array('data' => $record);

			if($data){
				$data['UserIntegratedOrderAddon']['id'] = $recordID;
				$data['UserIntegratedOrderAddon']['user_id'] = $userID;
				$data['UserIntegratedOrderAddon']['mall_id'] = $dokumallID;
				$data['UserIntegratedOrderAddon']['shared_key'] = $dokuSharedKey;

				$result = $this->User->UserIntegratedOrderAddon->doCheckout($data);
				$status = $this->RmCommon->filterEmptyField($result, 'status');

				if($status == 'success'){
					$record			= $this->RmCommon->filterEmptyField($result, 'data');
					$paymentStatus	= $this->RmCommon->filterEmptyField($record, 'UserIntegratedOrderAddon', 'payment_status');

					if($paymentStatus == 'paid'){

					//	send email paid invoice ===============================================
						$fullName		= $this->RmCommon->filterEmptyField($record, 'User', 'full_name');
						$email			= $this->RmCommon->filterEmptyField($record, 'User', 'email');
						$subject		= 'Informasi pembayaran transaksi';
						$template		= 'paid_invoice_notification';

						$financeEmail	= Configure::read('Global.Data.finance_email');
						$senderEmail	= Configure::read('__Site.email_from_prime');
						$bcc =  array(
							// $financeEmail, 
						//	ga usah dinaikin
							'foezaf13@gmail.com',
							// Configure::read('__Site.prime_leads_email'),
						);
						
						$params = array_merge($record, array(
							'from'	=> $senderEmail, 
							'bcc'	=> $bcc,
						));

						$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, __($subject), $params);

					//	=========================================================================================

						$message = $this->RmCommon->filterEmptyField($result, 'msg', null, __('Berhasil menyimpan data Invoice %s', $invoiceNumber));

						$this->RmCommon->setCustomFlash($message, 'success');
						$this->redirect(array('controller' => 'users','action' => 'account','admin' => true,));
					}
					else{
						$postData = $this->RmCommon->filterEmptyField($result, 'post_data');
						$this->admin_post_payment($recordID, $invoiceNumber, $postData);	
					}
				}
			}

			$this->request->data = $record;

			$this->set(array(
				'module_title'		=> __('Checkout Invoice'),
				'title_for_layout'	=> __('Checkout Invoice'),
				'record'			=> $record,
				'mallID'			=> $dokumallID,
				'sharedKey'			=> $dokuSharedKey,
			));
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
			$this->redirect(array('controller' => 'users','action' => 'account','admin' => true,));
		}
	}

	public function admin_post_payment($recordID, $invoiceNumber, $postData = NULL){
		if($postData){
			$paymentChannel = $this->RmCommon->filterEmptyField($postData, 'PAYMENTCHANNEL');

			if($paymentChannel == '03'){
			//	bca beda cara post + url nya
				$dokuMIPURL	= Configure::read('__Site.doku_payment_mip_url');
				$curl		= curl_init();

				curl_setopt($curl, CURLOPT_URL, $dokuMIPURL);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
				curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
				curl_setopt($curl, CURLOPT_HEADER, FALSE);
				curl_setopt($curl, CURLOPT_SSLVERSION, 3);
				curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData, '', '&'));

				$xmlResult = curl_exec($curl);
				curl_close($curl);

				$this->set(compact('xmlResult'));
			}

			$this->set(compact('postData'));
			$this->render('/Elements/blocks/users/forms/doku_form/checkout_addon');
		}
		else{
			$this->RmCommon->setCustomFlash(__('Gagal melakukan proses pembayaran.'), 'error');
			$this->redirect(array('action' => 'checkout', 'admin' => TRUE, $recordID, $invoiceNumber));
		}
	}

	public function admin_list_registrant(){
		$module_title = __('Invoice User Integrasi');
		$namedParams = $this->params->named;
		$export = $this->RmCommon->filterEmptyField($namedParams, 'export');
		$status = $this->RmCommon->filterEmptyField($namedParams, 'status');

		$options =  $this->User->UserIntegratedOrderAddon->callRefineParams($this->params, array(
			'group' => array(
				'UserIntegratedOrderAddon.id',
			),
			'contain' => array(
				'UserIntegratedOrder' => array(
					'Region',
					'City',
					'Subarea',
				),
				'UserIntegratedAddonPackageR123',
			),
			'order' => array(
				'UserIntegratedOrderAddon.created' => 'DESC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));

		if (!empty($status)) {
			$status_options = array(
				'conditions' => array(
					'UserIntegratedOrderAddon.payment_status' => $status,
				),
			);
			$options = array_merge_recursive($options, $status_options);
		}

		$this->paginate = $this->User->UserIntegratedOrderAddon->getData('paginate', $options);
		$values = $this->paginate('UserIntegratedOrderAddon');

		$this->set('active_menu', 'list_registrant');
		$this->set(compact(
			'values', 'module_title'
		));

		if($export == 'excel'){

			if (!empty($values)) {
				$this->layout = FALSE;
				$this->render('/Elements/blocks/users/tables/admin_view_export_registrant');
			} else {
				$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
				$this->redirect(array('controller' => 'users','action' => 'list_registrant','admin' => true,));
			}

		}

	}

	// detail checkout order addon partner media
	public function admin_view_detail_checkout($recordID = NULL, $invoiceNumber = NULL){
		$conditions = array(
			'conditions' => array(
				'UserIntegratedOrderAddon.id' => $recordID,
				'UserIntegratedOrderAddon.invoice_number' => $invoiceNumber,
			)
		);

		$record	= $this->User->UserIntegratedOrderAddon->getData('first', $conditions);

		if($record){
			$namedParams = $this->params->named;
			$export = $this->RmCommon->filterEmptyField($namedParams, 'export');

			$record = $this->User->getMergeList($record, array(
				'contain' => array(
					'UserIntegratedConfig',
				),
			));

			$this->set('active_menu', 'list_registrant');
			$this->set(array(
				'module_title'		=> __('Detail Invoice'),
				'title_for_layout'	=> __('Detail Invoice'),
				'record'			=> $record, 
			));

			if($export == 'excel'){
				$this->layout = FALSE;
				$this->render('/Elements/blocks/users/tables/admin_view_excel');
			}
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
			$this->redirect(array('controller' => 'users','action' => 'account','admin' => true,));
		}
	}

	// action verified user order addon
	public function admin_verified_user( $id = false, $user_id = false ) {

		$user_config = $this->User->UserIntegratedConfig->getData('first', array(
			'conditions' => array(
				'UserIntegratedConfig.id' => $id,
				'UserIntegratedConfig.user_id' => $user_id,
			),
		));

		if( !empty($user_config) ) {

			$id 		 = Common::hashEmptyField($user_config, 'UserIntegratedConfig.id');
			$is_verified = Common::hashEmptyField($user_config, 'UserIntegratedConfig.is_verified');

			if( !empty($this->request->data) ) {
				$data = $this->request->data;
				$data = $this->RmCommon->dataConverter($data, array(
					'date' => array(
						'UserIntegratedConfig' => array(
							'live_date',
							'end_date',
						),
					),
				));
				
				$result = $this->User->UserIntegratedConfig->doVerify($data, $id);
				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
					'ajaxRedirect' => false,
					'noRedirect' => true
				));
			}

			$this->set(compact(
				'user_config'
			));
			$this->render('/Elements/blocks/users/partner_medias/admin_verified_user');
		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
		}
	}

	/* =============================================================
		function do integrated by email
		input verify by email	
	============================================================= */
	function backprocess_do_integrated(){
		$id_user = Configure::read('User.id');
		$data = $this->request->data;
		$flag_option = $this->params->query;
		$isAjax	= $this->RequestHandler->isAjax();

		$data_integrated = $this->User->UserIntegratedConfig->getData('first', array(
			'conditions' => array(
				'UserIntegratedConfig.user_id' => $id_user,
			),
			'contain' => array(
				'UserIntegratedOrder',
				'UserIntegratedOrderAddon',
			),
		));

		// if user do connect
		if( $isAjax && !empty($data)) {
			$result = $this->User->UserIntegratedConfig->doIntegrated($data, $data_integrated, array(
				'option' => $flag_option,
			));
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'connect_account',
				'admin' => true,
			), array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
				'modal' => 'success',
			));

		} elseif(empty($isAjax)) {
			// if user disconnect
			$result = $this->User->UserIntegratedConfig->doDisconnect($data_integrated, array(
				'option' => $flag_option,
			));
			$this->RmCommon->setProcessParams($result, false);
		}
	}

	function admin_connect_account( $tabs_action_type = 'connect_account' ) {
		$id_user = Configure::read('User.id');
		$data_integrated = $this->User->UserIntegratedConfig->getData('first', array(
			'conditions' => array(
				'UserIntegratedConfig.user_id' => $id_user,
			),
			'contain' => array(
				'UserIntegratedOrder',
				'UserIntegratedOrderAddon' => array(
					'UserIntegratedAddonPackageR123',
					'UserIntegratedAddonPackageOLX',
				),
			),
		));

		$module_title = $title_for_layout = __('Connect Account');
		$this->set('active_menu', 'connect_account');
		$this->set(compact(
			'module_title', 'title_for_layout',
			'tabs_action_type', 'data_integrated'
		));
	}

	function admin_user_info($id = false){
		$module_title = __('Daftar User');
		$auth_id = Configure::read('User.id');

		$user = $this->RmUser->getUser($id);

		if($user){
			$recordID = Common::getRecordParentID($user);
			$groupName = Common::hashEmptyField($user, 'Group.name', false, array(
				'type' => 'strtolower',
			));

			$this->RmUser->_callRoleActiveMenu($user);
			$active_menu = $this->RmUser->getActive($groupName, 'user');
			
			$data_arr = array(
				'userID' => $recordID,
				'slug' => $groupName,
			);

			$options = $this->User->getUserList( 'conditions', $data_arr, $this->params->params);
			$this->RmUser->_callBeforeViewUserList($options, $data_arr, $user);

			$this->set(array(
				'active_tab' => 'user',
				'tab' => ( !empty($id) ) ? true : false,
				'currUser' => $user,
				'recordID' => $recordID,
				'module_title' => $module_title,
				'active_menu' => $active_menu,
				'getCookieId' => $this->RmCommon->getCookieUser(),
			));

			if( empty($id)){
				$this->set(array(
					'active_menu' => 'user',
					'self' => true,
				));
			}

			$this->RmCommon->renderRest(array(
				'is_paging' => true
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_unpremium_user($id = FALSE){
		if( !empty($id) ) {
			$user = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $id,
				)
			));

			if($user){
				$result = $this->User->removePremiumUser($user);
				$this->RmCommon->setProcessParams($result, FALSE, array('redirectError' => TRUE));
			}
			else{
				$this->RmCommon->redirectReferer(__('User tidak ditemukan'), 'error');
			}
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'), 'error');
		}
	}

	// list sosmed reference
	function admin_sosmed_reference() {
		$this->loadModel('UserClientSosmedReference');
		$params = $this->params->params;
		$title = __('Sosmed');

		$options =  $this->UserClientSosmedReference->_callRefineParams($params, array(
			'group' => array(
				'UserClientSosmedReference.id',
			),
		));
		$this->RmCommon->_callRefineParams($params);

		$this->paginate = $this->UserClientSosmedReference->getData('paginate', $options, array(
			'status' => 'all',
		));
		$values = $this->paginate('UserClientSosmedReference');

		$this->set(array(
			'active_menu' => 'crm_sosmed',
			'module_title' => $title,
			'title_for_layout' => $title,
			'values' => $values,
		));

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	// add new sosmed reference
	public function admin_add_sosmed_reference() {
		$title = __('Tambah Sosmed');
		$this->RmUser->callSosmedClientBeforeSave();

		$this->set(array(
			'active_menu' => 'crm_sosmed',
			'module_title' => $title,
			'title_for_layout' => $title,
		));
	}

	// edit sosmed reference
	public function admin_edit_sosmed_reference( $id = null ) {
		$title = __('Edit Sosmed');
		$value = $this->User->UserClient->UserClientSosmedReference->getData('first', array(
			'conditions' => array(
				'UserClientSosmedReference.id' => $id,
			),
		), array(
			'status' => 'all'
		));

		if( !empty($value) ) {
			$this->RmUser->callSosmedClientBeforeSave($value, $id);

			$this->set(array(
				'active_menu' => 'crm_sosmed',
				'module_title' => $title,
				'title_for_layout' => $title,
			));
			$this->render('admin_add_sosmed_reference');
		} else {
			$this->RmCommon->redirectReferer(__('Sosmed tidak ditemukan'));
		}
	}

	public function admin_delete_sosmed_reference() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Search', 'id');

    	$result = $this->User->UserClient->UserClientSosmedReference->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
			'redirectError' => true,
		));
	}

	// single toggle status
	public function admin_sosmed_reference_toggle( $id = null ) {
		$value = $this->User->UserClient->UserClientSosmedReference->getData('first', array(
			'conditions' => array(
				'UserClientSosmedReference.id' => $id,
			),
		), array(
			'status' => 'all',
		));

		if ( !empty($value) ) {
			$active = Common::hashEmptyField($value, 'UserClientSosmedReference.active');
	    	$result = $this->User->UserClient->UserClientSosmedReference->doToggle( $id, !$active );
			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
				'redirectError' => true,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Sosmed tidak ditemukan'));
		}
	}

	// popup add new sosmed reference
	public function backprocess_popup_sosmed_reference() {
		$data = $this->request->data;
		$listContentLabel = Configure::read('__Site.Global.Variable.ListContentLabel');
		$idLabelSosmed = $this->RmCommon->filterEmptyField($listContentLabel, 'sosmed');

		$result = $this->User->UserClient->UserClientSosmedReference->popupAddNew( $data );

		if( $result['status'] == 'success' ) {
			$value['UserClient']['client_ref_id'] = $idLabelSosmed;
			$value['UserClient']['client_ref_sosmed_id'] = $result['id'];
			$value_added = $this->RmUser->callContentLable($value);

			$this->render('/Elements/blocks/users/clients/forms/sosmed');
		}
	}

	public function backprocess_get_content($contentTypeSlug = null, $contentID = null) {
		$isAjax		= true; //$this->RequestHandler->isAjax();
		$data		= $this->request->data;
		$records	= array();

		if($isAjax && $data){

			$contentTypeSlug	= Common::hashEmptyField($data, 'User.Search.content_type', $contentTypeSlug);
			$designType			= Common::hashEmptyField($data, 'User.Search.design_type');
			$keyword			= Common::hashEmptyField($data, 'User.Search.keyword');

				if($contentTypeSlug && $designType && strlen($keyword) >= 3){
					$projectID = Configure::read('Global.Data.Project.id');
					$companyID = Configure::read('Global.Data.Project.company_id');

					$modelName = $contentTypeSlug;

					if($modelName){
					//	inject new params named
						$this->request->params['named'] = array_replace($this->request->params['named'], array(
							'keyword' => $keyword, 
						));

					//	load model
						$this->$modelName = ClassRegistry::init($modelName);
						$params	= $this->params->params;
						$option	= $this->$modelName->_callRefineParams($params, array(
							'limit' => 10, 
						));

						$records = $this->$modelName->getData('all', $option, array(
							'status' => 'active', 
						));

						if($records){
							foreach($records as &$record){
								$recordID	= Common::hashEmptyField($record, $modelName.'.id');
								$label		= Common::hashEmptyField($record, $modelName.'.label', '');
								$name		= Common::hashEmptyField($record, $modelName.'.name', $label);
								$record		= array(
									'label'		=> $name, 
									'reference'	=> $recordID, 
								);
							}
						}
					}
				}
		}

		$this->autoRender = false;
		return $isAjax ? json_encode($records) : $records;
	}

	function backprocess_get_companies(){
		$params = $this->params->query;
		$this->RmCommon->_callCheckAPI($params);	

		$default_options = array(
			'conditions' => array(),
			'fields' => array(
				'user_id', 'name'
			),
			'contain' => array(),
			'order' => array(
				'UserCompany.name' => 'ASC',
			),
		);

		$params = $this->User->UserCompany->getData('list', $default_options);
		$this->RmCommon->_callDataForAPI($params, 'manual');
	}

	function redirect_home ( $action = false ) {
		$this->redirect('/');
	}

	public function admin_personal_config($userID = null){
		if($userID){
			$params			= $this->params->params;
			$principleID	= Common::hashEmptyField($params, 'named.user_id', 0);

			$user = $this->User->getData('first', array(
				'contain'		=> array('UserConfig'), 
				'conditions'	=> array(
					'User.id'						=> $userID,
					'COALESCE(User.parent_id, 0)'	=> $principleID,
				),
			), array(
				'status' => array('non-active', 'active'),
			));

			if($user){
				$this->RmUser->callBeroreSavePersonalPage($user);
				$this->set(array(
					'module_title'	=> __('Personal Website'), 
					'active_tab'	=> 'Personal Website',
					'user'			=> $user, 
					'active_menu'	=> 'agent', 
				));
			}
			else{
				$this->RmCommon->redirectReferer(__('Agen tidak ditemukan'));
			}
		}
		else{
			$this->RmCommon->redirectReferer(__('Agen tidak ditemukan'));
		}
	}

	public function facebook_connect(){
		$facebook	= $this->RmUser->facebookConnect();
		$helper		= $facebook->getRedirectLoginHelper();
		$currentURL	= Router::url(array(
			'controller'	=> 'users', 
			'action'		=> 'social_login', 
			'Facebook', 
		), true);

		$loginURL = $helper->getLoginUrl($currentURL, array('email'));

		$this->redirect($loginURL);
	}

	public function social_login($provider = null){
		$this->autoLayout = false;
		$this->autoRender = false;

		if($provider){
			$connect	= $this->Hybridauth->connect($provider);
			$profile	= $this->Hybridauth->user_profile;
			$error		= $this->Hybridauth->error;

			if($profile){
				$this->_successfulHybridauth($provider, $profile);
			}
			else{
				$message	= __('Gagal masuk menggunakan %s, cobalah beberapa saat lagi. (%s)', $provider, $error);
				$result		= array(
					'status'	=> 'error', 
					'msg'		=> $message, 
					'Log'		=> array(
						'activity' => $message,
					),
				);
			}
		}
		else{
			$message	= __('Media sosial tidak valid');
			$result		= array(
				'status'	=> 'error', 
				'msg'		=> $message, 
				'Log'		=> array(
					'activity' => $message,
				),
			);
		}

	//	kalo sukses ga bakal masuk sini
		$this->Hybridauth->logout();
		$this->RmCommon->setProcessParams($result, $this->Auth->logoutRedirect, array(
			'redirectError' => true, 
		));
	}

//	semua redirect dari provider di set masuk kesini
	public function social_endpoint($provider = null){
		$this->autoLayout = false;
		$this->autoRender = false;

		$error = Common::hashEmptyField($this->params->query, 'error');

		if(empty($error)){
			$this->Hybridauth->processEndpoint();
		}

		$message = $this->Hybridauth->error;

		if($error || ($message && $message != 'no error so far')){
			$provider = Hash::get($_GET, 'hauth_done', $this->Hybridauth->provider);
			$redirect = $this->Auth->logoutRedirect;

			if($error == 'access_denied'){
				$message = __('Otentifikasi gagal. User membatalkan akses, atau [%s] menolak akses tersebut.', $provider);
			}

			$this->RmCommon->setProcessParams(array(
				'status'	=> 'error', 
				'msg'		=> $message, 
			), $redirect, array(
				'redirectError' => true, 
			));
		}
	}

	private function _successfulHybridauth($provider, $incomingProfile){
		$this->autoLayout = false;
		$this->autoRender = false;

		$status		= 'error'; 
		$message	= __('Gagal memverifikasi akun %s Anda, cobalah beberapa saat lagi', $provider);
		$redirect	= $this->Auth->logoutRedirect;

	//	1 - check if user already authenticated using this provider before
		$this->SocialProfile->recursive = -1;

		$socialNetworkID = Common::hashEmptyField($incomingProfile, 'SocialProfile.social_network_id');
		$existingProfile = $this->SocialProfile->find('first', array(
			'conditions' => array(
				'SocialProfile.social_network_id'	=> $socialNetworkID, 
				'SocialProfile.social_network_name'	=> $provider, 
			), 
		));

		$isLoggedIn = $this->Auth->loggedIn();

		if($existingProfile || empty($isLoggedIn)){
		//	2 - if an existing profile is available, then we set the user as connected and log them in
			$email	= Common::hashEmptyField($incomingProfile, 'SocialProfile.email');
			$user	= $this->User->getData('first', array(
				'contain'		=> array('UserConfig'), 
				'conditions'	=> array(
					'User.email' => $email, 
				), 
			), array(
				'status'	=> 'active', 
				'company'	=> false, 
			));

			if($user){
				if(empty($existingProfile)){
					$userID				= Common::hashEmptyField($user, 'User.id');
					$incomingProfile	= Hash::insert($incomingProfile, 'SocialProfile.user_id', $userID);

					$this->SocialProfile->save($incomingProfile);
					$existingProfile = $this->SocialProfile->read(null, $this->SocialProfile->id);
				}

				$user = array_merge($user, $existingProfile);

			//	disini langsung redirect jadi gausah set result
				$this->_doSocialLogin($user, true);
			}
			else{
				$status = 'error'; 

				if($email){
					$message = __('Maaf email %s tidak terdaftar di Prime System', $email);
				}
				else{
					$message = __('Izinkan akses alamat email untuk dapat melanjutkan');
				}
			}
		}
		else{
		//	new profile.
			if($isLoggedIn){
			//	kemungkinan masuk sini kecil, karena pas akses beda domain
			//	user is already logged-in , attach profile to logged in user.
			//	create social profile linked to current user
				$userID				= $this->Auth->user('id');
				$incomingProfile 	= Hash::insert($incomingProfile, 'SocialProfile.user_id', $userID);

			//	set social profile ke user login
				$this->SocialProfile->save($incomingProfile);

				$status		= 'success'; 
				$message	= __('Akun %s Anda sekarang terikat dengan akun Prime System Anda', $provider);
				$redirect	= array(
					'admin'			=> true,
					'controller'	=> 'users',
					'action'		=> 'dashboard',
				);
			}
		//	NOTE : DI PRIMEAGENT KALO GA ADA GA OTOMATIS DIDAFTARIN
		//	else{
		//	//	no-one logged and no profile, must be a registration.
		//		$user = $this->User->createFromSocialProfile($incomingProfile);
		//		$incomingProfile['SocialProfile']['user_id'] = $user['User']['id'];
		//		$this->SocialProfile->save($incomingProfile);

		//	//	log in with the newly created user
		//		$this->_doSocialLogin($user);
		//	}
		}

		$result = array(
			'status'	=> $status, 
			'msg'		=> $message, 
			'Log'		=> array(
				'activity'		=> $message,
				'old_data'		=> $incomingProfile,
				'document_id'	=> empty($userID) ? false : $userID,
			),
		);
		
		if($status == 'error'){
			$this->Hybridauth->logout();
		}

		$this->RmCommon->setProcessParams($result, $redirect, array(
			'redirectError' => true, 
		));
	}

	private function _doSocialLogin($user, $returning = false){
		$this->autoLayout = false;
		$this->autoRender = false;

	//	kalo dah masuk sini user udah pasti ada di database
		$userID			= Common::hashEmptyField($user, 'User.id');
		$groupID		= Common::hashEmptyField($user, 'User.group_id');
		$username		= Common::hashEmptyField($user, 'User.username');
		$userConfigID	= Common::hashEmptyField($user, 'UserConfig.id');
		$provider		= Common::hashEmptyField($user, 'SocialProfile.social_network_name');
		$socialLink		= Common::hashEmptyField($user, 'SocialProfile.link');

	//	prepare save data
		$passToken	= String::uuid();
		$saveData	= array(
			'UserConfig' => array(
				'id'		=> $userConfigID, 
				'user_id'	=> $userID, 
				'token'		=> $passToken, 
			), 
		);

		if($provider && $socialLink){
		//	auto update social link
			switch(strtolower($provider)){
				case 'google' :
					$socialField = 'google_plus';
				break;
				case 'facebook' :
					$socialField = 'facebook';
				break;
				case 'twitter' :
					$socialField = 'twitter';
				break;
				case 'linkedin' :
					$socialField = 'linkedin';
				break;
			}

			if(!empty($socialField)){
				$saveData = Hash::insert($saveData, sprintf('UserConfig.%s', $socialField), $socialLink);
			}
		}

	//	set token before redirect
		$this->User->UserConfig->save($saveData);

		$redirect = Router::url(array(
			'admin'			=> true, 
			'controller'	=> 'users', 
			'action'		=> 'login', 
			'pass_token'	=> $passToken,
			'social_login'	=> true, 
		), true);

		$status		= 'success';
		$message	= __('Masuk menggunakan akun %s Anda', $provider);
		$result		= array(
			'status'	=> $status, 
			'msg'		=> false, // disini jangan di kasih flash dulu, karena nanti masih di validasi sama pass_token di domain tujuan
			'Log'		=> array(
				'activity'		=> $message,
				'old_data'		=> $user,
				'document_id'	=> $userID,
			),
		);

		$this->RmCommon->setProcessParams($result, $redirect);
	}
}