<?php
App::uses('AppController', 'Controller');

class SettingsController extends AppController {
	public $components = array(
		'RmImage', 'RmSetting', 'RmRecycleBin',
		'RmMigrateCompany',
		'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
	            'master_data' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'admin_theme_selection' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'backprocess_update_device' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors'
				 	),
			 	),
			 	'admin_general' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors', 'data'
				 	),
			 	),
			 	'admin_launcher' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data', 'launcher'
				 	),
			 	),
			 	'admin_launcher_theme' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data', 'launcher'
				 	),
			 	),
			 	'admin_general_company' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
			 	'admin_delete_template_ebrosur' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device'
				 	),
			 	),
	            'master_subarea' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'data'
				 	),
			 	),
    		),
    	),
	);

	public $uses = array(
		'Attribute',
	);

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array(
			'master_data', 'theme_selection',
			'master_subarea', 'download_xls',
		));
	}

	function _calDataIndexConvertion ( $data, $reverse = false ) {
		return $this->RmCommon->dataConverter($data, array(
			'date' => array(
				'UserSetting' => array(
					'sign_date',
					'from_date',
					'to_date',
				),
			)
		), $reverse);
	}

	function admin_index () {
		$data = $this->request->data;
		$setting = $this->User->UserSetting->getData('first', false, array(
			'mine' => true,
		));
		$setting = $this->_calDataIndexConvertion($setting, true);

		if( !empty($setting) ) {
			$id = $this->RmCommon->filterEmptyField($setting, 'UserSetting', 'id');
			$setting = $this->User->UserSetting->UserSettingEmail->getRequestData($setting, $id);
		} else {
			$id = false;
		}

		$data = $this->_calDataIndexConvertion($data);
		$result = $this->User->UserSetting->doSave( $data, $setting, $this->user_id, $id );
		$this->RmCommon->setProcessParams($result, array(
			'action' => 'index',
			'admin' => true,
		));

		$this->set('module_title', __('Pengaturan Website'));
	}

	function admin_theme_selection(){
		if(!empty($this->user_id)){
			$data = $values = $this->User->UserCompanyConfig->Theme->getData('all', array(
				'cache' => 'Theme.Company.All',
			));

			$this->set('module_title', __('Tampilan Website'));
			$this->set('active_menu', 'theme_selection');
			$this->set(compact(
				'values', 'data'
			));
		}
	}

	public function admin_personal_theme_selection($userID = null){
		$userID		= empty($userID) ? Configure::read('User.id') : $userID;
		$groupID	= Configure::read('User.group_id');
		$isAgent	= Common::validateRole('agent', $groupID);

		if($isAgent && $userID){
			$user = $this->User->getData('first', array(
				'contain'		=> array('UserConfig'), 
				'conditions'	=> array(
					'User.id' => $userID, 
				), 
			));

			$records = $this->User->UserConfig->Theme->getData('all', array(), array(
				'owner_type' => 'agent', 
			));

			$this->set('module_title', __('Tema'));
			$this->set('active_menu', 'personal_theme_selection');
			$this->set(compact('user', 'records'));
		}
		else{
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman tersebut'), 'error');
		}
	}

	function admin_customizations( $theme_id = false ) {
		$selected_theme = $this->User->UserCompanyConfig->Theme->getMerge(array(), $theme_id, array(
			'owner_type'	=> 'all', 
		//	'cache'			=> array(
		//		'name' => __('Theme.%s', $theme_id),
		//	),
		));

		if( !empty($selected_theme) ) {
			$theme_slug = Common::hashEmptyField($selected_theme, 'Theme.slug');
			$owner_type = Common::hashEmptyField($selected_theme, 'Theme.owner_type');

			$authGroupID	= $this->Auth->user('group_id');
			$isAdmin		= Common::validateRole('admin', $authGroupID);
			$isAgent		= Common::validateRole('agent', $authGroupID);
			$isCompanyAdmin	= Common::validateRole('company_admin', $authGroupID);
			$continue		= false;

			if($owner_type == 'company'){
			//	theme company (admin company / admin boleh bantu setting)
				$user_id	= $this->parent_id;
				$continue	= !$isAgent;
			}
			else{
			//	theme agent (admin company / admin tidak boleh bantu setting)
				$user_id	= $this->Auth->user('id');
				$continue	= $isAgent;
			}

			if($continue){
			//	get default settings
				$default_theme_settings = Configure::read('Global.Data.theme_colors.' . strtolower($theme_slug));

				$value = $this->User->UserCompanySetting->getMerge($selected_theme, $user_id, $theme_id);
				$value = $this->User->UserConfig->getMerge($value, $user_id);

				if($owner_type == 'company'){
					$value = $this->User->UserCompany->getMerge($value, $user_id);
					$value = $this->RmUser->getThemeConfig($value);
				}

				$id		= $this->RmCommon->filterEmptyField($value, 'UserCompanySetting', 'id');
				$data	= $this->request->data;

				if($data){
					$data['UserCompanySetting']['theme_id'] = $theme_id;
					$save_path_general = Configure::read('__Site.general_folder');

				//	capture old photo, image file has to be deleted when new image file uploaded
					$setting = $this->User->UserCompanySetting->find('first', array(
						'conditions' => array(
							'UserCompanySetting.id' => $id, 
						), 
					));

					$data['UserCompanySetting']['user_id'] = $user_id;
					$data = $this->RmImage->_uploadPhoto( $data, 'UserCompanySetting', 'footer_image', $save_path_general, true );
					$data = $this->RmImage->_uploadPhoto( $data, 'UserCompanySetting', 'header_image', $save_path_general, true );
					$data = $this->RmImage->_uploadPhoto( $data, 'UserCompanySetting', 'bg_image', $save_path_general, true );

					$reset = $this->RmCommon->filterEmptyField($this->params->query, 'reset', FALSE, 0);

					if( $reset && !empty($default_theme_settings) ) {
						$data['UserCompanySetting'] = array_replace($data['UserCompanySetting'], $default_theme_settings);
						$this->request->data = $data;
					}

					$result	= $this->User->UserCompanySetting->doSave($data, $value, $id);
					$status	= Common::hashEmptyField($result, 'status');

					if($status == 'success'){
						$imageFields = array('bg_image', 'header_image', 'footer_image');

						foreach($imageFields as $imageField){
						//	use isset
							$isUploading = Hash::check($this->data, sprintf('UserCompanySetting.%s.name', $imageField));

							if($isUploading){
								$uploadPhoto	= Common::hashEmptyField($this->data, sprintf('UserCompanySetting.%s.name', $imageField));
								$oldPhoto		= Common::hashEmptyField($setting, sprintf('UserCompanySetting.%s', $imageField));

								if($uploadPhoto && $oldPhoto){
									$permanent = FALSE;
									$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path_general, NULL, $permanent);
								}
							}
						}
					}

					$this->RmCommon->setProcessParams($result, array(
						'controller' => 'settings',
						'action' => 'customizations',
						$theme_id,
						'admin' => true,
					));
				} else {
					if( empty($value['UserCompanySetting']) ) {
						$this->request->data['UserCompanySetting'] = $default_theme_settings;
					} else {
						$this->request->data = $value;
					}

					$footer_image = $this->RmCommon->filterEmptyField($value, 'UserCompanySetting', 'footer_image');
					$value['UserCompanySetting']['footer_image_hide'] = $footer_image;
				}

				$this->RmCommon->_layout_file('color-picker');

				$this->set('category_status', $this->RmCommon->getGlobalVariable('category_status'));

				$this->set('is_theme_setting', true);
				$this->set('active_menu', 'setting');
				$this->set('minimalismenu', false);
				$this->set(compact(
					'value', 'theme_id'
				));
			}
			else{
				$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman tersebut'), 'error');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Tema tidak ditemukan'), 'error');
		}
	}

	function admin_general(){
		$value = $this->User->UserCompanyConfig->getData('first', array(
			'conditions' => array(
				'UserCompanyConfig.domain' => $this->_base_url,
			)
		));

		$id 	 = Common::hashEmptyField($value, 'UserCompanyConfig.id');
		$user_id = Common::hashEmptyField($value, 'UserCompanyConfig.user_id');

		$value 	 = $this->User->getmerge($value, $user_id);
		$data  	 = $this->request->data;
		$data 	 = $this->RmSetting->_callBeforeCompanyConfigSave($data);

		$watermarkType = Common::hashEmptyField($value, 'UserCompanyConfig.watermark_type');

		if(empty($watermarkType)){
			$value['UserCompanyConfig']['watermark_type'] = 'text';
		}

		$result = $this->User->UserCompanyConfig->doSave($data, $value, $id, $this->parent_id, $this->_base_url);

		if($value && $this->request->data){
			$savePathGeneral 	= Configure::read('__Site.general_folder');
			$oldPhotos 			= array();
			$photoPaths			= array(
				'favicon'		=> $savePathGeneral
			);

			$permanent	= FALSE;
			$tempData	= isset($this->request->data['UserCompanyConfig']) ? $this->request->data['UserCompanyConfig'] : NULL;

			foreach($photoPaths as $fieldName => $savePath){
				$oldPhoto = isset($value['UserCompanyConfig'][$fieldName]) && $value['UserCompanyConfig'][$fieldName] ? $value['UserCompanyConfig'][$fieldName] : NULL;

			//	if user upload new photo, delete old photo
				if($oldPhoto && isset($tempData[$fieldName]['name']) && $tempData[$fieldName]['name']){
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $savePath, NULL, $permanent);
				}
			}

			unset($tempData);
		}

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'settings',
			'action' => 'general',
			'admin' => true,
		));

		$this->request->data = $this->RmSetting->_callBeforeCompanyConfigView($this->request->data, $value);

		// list theme
		$themes = $this->User->UserCompanyConfig->Theme->getData('list', array(
			'cache' => 'Theme.List',
		));

		// $templates = $this->User->UserCompanyConfig->Template->getData('list', array(
		// 	'cache' => 'Template.List',
		// ));

		// $ebrochureTemplates = $this->requestAction(array(
		// 	'admin'			=> false, 
		// 	'controller'	=> 'ajax', 
		// 	'action'		=> 'get_ebrochure_template', 
		// 	'type'			=> 'company', 
		// ));

		// $list_sales = $this->User->getListSales();
		$this->RmCommon->_layout_file(array(
			'ckeditor',
			'color-picker'
		));

		$propertyTypes = $this->User->Property->PropertyType->getData('all');

		$this->set('active_menu', 'general');
		$this->set('module_title', __('Umum (Admin)'));
		$this->set(compact(
			'themes', 
			'propertyTypes'
			// 'list_sales', 
			// 'templates', 
			// 'packages', 
			// 'ebrochureTemplates'
		));

		if($this->Rest->isActive() && !empty($result['status']) && $result['status'] == 'success'){
			$value = $this->User->UserCompanyConfig->getData('first', array(
				'conditions' => array(
					'UserCompanyConfig.domain' => $this->_base_url,
				)
			));
		}

		$this->set('type_commission', $this->RmCommon->getGlobalVariable('type_commision_cobroke'));

		$data_val = array(
			'config_data' => $value,
			'themes' => $themes
			// 'list_sales' => $list_sales, 
			// 'templates' => $templates
		);

		$this->RmCommon->_callRequestSubarea('UserCompanyConfig');
		$this->RmCommon->_callDataForAPI($data_val, 'manual');
		$this->RmCommon->renderRest();
	}

	function admin_launcher(){
		$launcher = $this->User->UserCompanyLauncher->getData('first', array(
			'conditions' => array(
				'UserCompanyLauncher.user_id' => $this->parent_id,
			),
		), array(
			'chosen' => true,
		));

		$values = $this->User->UserCompanyLauncher->ThemeLauncher->getData('all');

		$this->RmCommon->_callDataForAPI($values, 'manual');

		$this->set('active_menu', 'launcher');
		$this->set('module_title', __('Launcher'));
		$this->set(compact(
			'values', 'launcher'
		));
	}

	function admin_launcher_theme( $theme_id = false ){
		$theme = $this->User->UserCompanyLauncher->ThemeLauncher->getData('first', array(
			'conditions' => array(
				'ThemeLauncher.id' => $theme_id,
			),
		));

		if( !empty($theme) ) {
			$value = $this->User->UserCompanyLauncher->getData('first', array(
				'conditions' => array(
					'UserCompanyLauncher.theme_launcher_id' => $theme_id,
					'UserCompanyLauncher.user_id' => $this->parent_id,
				),
			));

			$id = $this->RmCommon->filterEmptyField($value, 'UserCompanyLauncher', 'id');
			$data = $this->request->data;

			if( !empty($data) ) {
				$save_path_general	= Configure::read('__Site.general_folder');
				$bodyBgType			= $this->RmCommon->filterEmptyField($data, 'UserCompanyLauncher', 'background_type', 'color');
				$bgFilename			= $this->RmCommon->filterEmptyField($data['UserCompanyLauncher'], 'body_bg_img', 'name');
				$logoFilename		= $this->RmCommon->filterEmptyField($data['UserCompanyLauncher'], 'logo', 'name');

				if($bodyBgType == 'image'){
					if($bgFilename){
						$data = $this->RmImage->_uploadPhoto( $data, 'UserCompanyLauncher', 'body_bg_img', $save_path_general, true );	
					}

					$data['UserCompanyLauncher']['body_bg_img']		= $bgFilename ? $this->RmCommon->filterEmptyField($data, 'UserCompanyLauncher', 'body_bg_img', '') : '';
					$data['UserCompanyLauncher']['body_bg_color']	= '';
				}
				else if($bodyBgType == 'color'){
					$data['UserCompanyLauncher']['body_bg_img']		= '';
					$data['UserCompanyLauncher']['body_bg_color']	= $this->RmCommon->filterEmptyField($data, 'UserCompanyLauncher', 'body_bg_color', '');
				}

				if($logoFilename){
					$data = $this->RmImage->_uploadPhoto( $data, 'UserCompanyLauncher', 'logo', $save_path_general, true );
				}

				$data['UserCompanyLauncher']['logo'] = $logoFilename ? $this->RmCommon->filterEmptyField($data, 'UserCompanyLauncher', 'logo', '') : '';

			//	untuk flag reset theme
				$reset = $this->RmCommon->filterEmptyField($this->params->query, 'reset', FALSE, 0);
				if($reset){
				//	jika dilakukan reset, ambil default value dari RmCommon
					$launcherColors = Configure::read('Global.Data.launcher_colors');
					if($launcherColors){
						$data['UserCompanyLauncher'] = array_replace($data['UserCompanyLauncher'], $launcherColors);
						$data['UserCompanyLauncher']['button_top'] = 'top';
					}
				}

				$data['UserCompanyLauncher']['reset'] = $reset;
			}
			else{
			//	setting default launcher setting jika belum ada setting untuk company
				$launcherColors = Configure::read('Global.Data.launcher_colors');
				if($launcherColors){
					$this->request->data['UserCompanyLauncher'] = array_merge($launcherColors, array('button_top' => 'top'));
				}
			}

			$result = $this->User->UserCompanyLauncher->doSave($data, $value, $id, $theme_id);
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'launcher_theme',
				$theme_id,
				'admin' => true,
			));

			$this->RmCommon->_layout_file('color-picker');
			
			$this->set('active_menu', __('pengaturan'));
			$this->set('module_title', __('Launcher'));
			$this->set('minimalismenu', false);
			$this->set('is_theme_setting', TRUE);

			$this->set(compact('id', 'theme_id'));

			$this->RmCommon->_callDataForAPI($value, 'manual');
		} else {
			$this->RmCommon->redirectReferer(__('Tema tidak ditemukan'));
		}
	}

	function admin_general_company(){
		if( $this->RmCommon->_isCompanyAdmin() ) {
			$value = $this->User->UserCompanyConfig->getData('first', array(
				'conditions' => array(
					'UserCompanyConfig.domain' => $this->_base_url,
				)
			));

			$id				= $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'id');
			$regionID		= $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'mt_region_id');
			$cityID			= $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'mt_city_id');
			$subareaID		= $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'mt_subarea_id');
			$is_brochure	= $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'is_brochure');
			$is_co_broke	= $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'is_co_broke');
			$id				= $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'id');
			$user_id		= $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'user_id');

			$value	= $this->User->getmerge($value, $user_id);
			$data	= $this->request->data;
			$data	= $this->RmSetting->_callBeforeCompanyConfigSave($data);
			$result	= $this->User->UserCompanyConfig->doSave($data, $value, $id, $this->parent_id, $this->_base_url);

			if( !empty($this->request->data)) {
				$savePathEbrosur	= Configure::read('__Site.ebrosurs_photo');
				$oldPhotos			= array();
				$photoPaths			= array(
					'brochure_custom_sell'	=> $savePathEbrosur, 
					'brochure_custom_rent'	=> $savePathEbrosur
				);

				$permanent	= FALSE;
				$tempData	= isset($this->request->data['UserCompanyConfig']) ? $this->request->data['UserCompanyConfig'] : NULL;

				foreach($photoPaths as $fieldName => $savePath){
					$oldPhoto = isset($value['UserCompanyConfig'][$fieldName]) && $value['UserCompanyConfig'][$fieldName] ? $value['UserCompanyConfig'][$fieldName] : NULL;

				//	if user upload new photo, delete old photo
					if($oldPhoto && isset($tempData[$fieldName]['name']) && $tempData[$fieldName]['name']){
						$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $savePath, NULL, $permanent);
					}
				}

				unset($tempData);

				$regionID	= $this->RmCommon->filterEmptyField($this->data, 'UserCompanyConfig', 'mt_region_id');
				$cityID		= $this->RmCommon->filterEmptyField($this->data, 'UserCompanyConfig', 'mt_city_id');
				$subareaID	= $this->RmCommon->filterEmptyField($this->data, 'UserCompanyConfig', 'mt_subarea_id');
			}
			
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'general_company',
				'admin' => true,
			));

			$this->request->data = $this->RmSetting->_callBeforeCompanyConfigView($this->request->data);
			$this->request->data['UserCompanyConfig']['region']		= $regionID;
			$this->request->data['UserCompanyConfig']['city']		= $cityID;
			$this->request->data['UserCompanyConfig']['subarea']	= $subareaID;

			$this->RmCommon->_callRequestSubarea('UserCompanyConfig');

			$propertyTypes = $this->User->Property->PropertyType->getData('all');

			$this->set('active_menu', 'general_company');
			$this->set('module_title', __('Umum'));
			$this->set(compact('is_brochure', 'is_co_broke', 'propertyTypes'));

			if($this->Rest->isActive() && !empty($result['status']) && $result['status'] == 'success'){
				$value = $this->User->UserCompanyConfig->getData('first', array(
					'conditions' => array(
						'UserCompanyConfig.domain' => $this->_base_url,
					)
				));
			}

			$this->set('type_commission', $this->RmCommon->getGlobalVariable('type_commision_cobroke'));

			$this->RmCommon->_callDataForAPI($value, 'manual');

		//	templates
			// $ebrochureTemplates = $this->requestAction(array(
			// 	'admin'			=> false, 
			// 	'controller'	=> 'ajax', 
			// 	'action'		=> 'get_ebrochure_template', 
			// 	'type'			=> 'company', 
			// ));

			// $this->set(array(
			// 	'ebrochureTemplates' => $ebrochureTemplates, 
			// ));
		} else {
			$this->RmCommon->redirectReferer(__('Halaman tidak ditemukan'), 'error');
		}
	}

	function admin_attributes() {
		$title_for_layout = $module_title = __('Attributes');

		$this->RmCommon->_callRefineParams($this->params);
		$options =  $this->Attribute->_callRefineParams($this->params, array(
			'limit' => 20,
		));

		$this->paginate =$this->Attribute->getData('paginate', $options);
		$values = $this->paginate('Attribute');

		$this->set('active_menu', 'catalog');
		$this->set(compact(
			'values', 'module_title', 'title_for_layout'
		));
	}

	function _callDataAttribute () {
		$urlStepProperties = '#';

		$urlBack = array(
			'controller' => 'settings',
			'action' => 'attributes',
			'admin' => true,
		);


		$this->set(compact(
			'urlStepProperties', 'urlBack'
		));
	}

	public function admin_attribute_add() {
		$module_title = __('New Attribute');

		$data = $this->request->data;

		if( !empty($data) ) {
			$data = $this->RmCommon->beforeSave($data, 'Attribute', array(
				'slug' => array(
					'name',
				),
			));
		}

		$result = $this->Attribute->doSave( $data );
		$id = $this->RmCommon->filterEmptyField($result, 'id');
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'settings',
			'action' => 'attribute_options',
			$id,
			'admin' => true
		));

		$this->_callDataAttribute();
		$this->set('step', 'properties');
		$this->set('active_menu', 'catalog');
		$this->set(compact(
			'module_title'
		));
	}

	public function admin_attribute_edit( $id = false ) {
		$module_title = __('Edit Attribute');
		$value = $this->Attribute->getData('first', array(
			'conditions' => array(
				'Attribute.id' => $id,
			),
		));

		if( !empty($value) ) {
			$data = $this->request->data;

			if( !empty($data) ) {
				$data = $this->RmCommon->beforeSave($data, 'Attribute', array(
					'slug' => array(
						'name',
					),
				));
			}

			$result = $this->Attribute->doSave( $data, $value, $id );
			$attribute_id = $this->RmCommon->filterEmptyField($result, 'id');
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'attribute_options',
				$attribute_id,
				'admin' => true
			));

			$this->_callDataAttribute();
			$this->set('step', 'properties');
			$this->set('active_menu', 'catalog');
			$this->set(compact(
				'module_title', 'id'
			));

			$this->render('admin_attribute_add');
		} else {
			$this->RmCommon->redirectReferer(__('Attribute does not exist'), 'error');
		}
	}

	public function admin_attribute_options( $id = false ) {
		$value = $this->Attribute->getData('first', array(
			'conditions' => array(
				'Attribute.id' => $id,
			),
		));

		if( !empty($value) ) {
			$attribute_name = $this->RmCommon->filterEmptyField($value, 'Attribute', 'name');
			$module_title = sprintf(__('%s - Manage Options'), $attribute_name);

			$this->RmCommon->_callRefineParams($this->params);
			$options =  $this->Attribute->AttributeOption->_callRefineParams($this->params, array(
				'conditions' => array(
					'AttributeOption.attribute_id' => $id,
					'AttributeOption.parent_id' => 0,
				),
				'limit' => 20,
			));

			$this->paginate =$this->Attribute->AttributeOption->getData('paginate', $options);
			$values = $this->paginate('AttributeOption');

			$urlStepProperties = array(
				'controller' => 'settings',
				'action' => 'attribute_edit',
				$id,
				'admin' => true,
			);
			$urlBack = array(
				'controller' => 'settings',
				'action' => 'attributes',
				'admin' => true,
			);

			$this->set('step', 'manage-options');
			$this->set('active_menu', 'catalog');
			$this->set(compact(
				'values', 'module_title', 'title_for_layout',
				'id', 'urlStepProperties', 'urlBack'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Attribute does not exist'), 'error');
		}
	}

	function _callDataAttributeOption ( $id ) {
		$urlBack = array(
			'controller' => 'settings',
			'action' => 'attribute_options',
			$id,
			'admin' => true,
		);
		$attributeSets = $this->User->CrmProject->AttributeSet->getData('list');


		$this->set(compact(
			'urlBack', 'attributeSets'
		));
	}

	public function admin_attribute_option_add( $id = false ) {
		$value = $this->Attribute->getData('first', array(
			'conditions' => array(
				'Attribute.id' => $id,
			),
		));

		if( !empty($value) ) {
			$attribute_name = $this->RmCommon->filterEmptyField($value, 'Attribute', 'name');
			$module_title = sprintf(__('%s - New Options'), $attribute_name);

			$data = $this->request->data;

			if( !empty($data) ) {
				$data = $this->RmCommon->beforeSave($data, 'AttributeOption', array(
					'slug' => array(
						'name',
					),
					'order',
				));
			}

			$result = $this->Attribute->AttributeOption->doSave( $data, $id );
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'attribute_options',
				$id,
				'admin' => true
			));

			$this->_callDataAttributeOption($id);
			$this->set('step', 'properties');
			$this->set('active_menu', 'catalog');
			$this->set(compact(
				'module_title', 'id'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Options does not exist'), 'error');
		}
	}

	public function admin_attribute_option_edit( $id = false, $attribute_option_id ) {
		$value = $this->Attribute->AttributeOption->getData('first', array(
			'conditions' => array(
				'AttributeOption.id' => $attribute_option_id,
				'AttributeOption.attribute_id' => $id,
			),
		));

		if( !empty($value) ) {
			$value = $this->Attribute->getMerge($value, $id, false);
			$attribute_name = $this->RmCommon->filterEmptyField($value, 'Attribute', 'name');
			$module_title = sprintf(__('%s - Edit Options'), $attribute_name);

			$data = $this->request->data;

			if( !empty($data) ) {
				$data = $this->RmCommon->beforeSave($data, 'AttributeOption', array(
					'slug' => array(
						'name',
					),
					'order',
				));
			}

			$result = $this->Attribute->AttributeOption->doSave( $data, $id, $value, $attribute_option_id );
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'attribute_options',
				$id,
				'admin' => true
			));

			$urlStepOptions = array(
				'controller' => 'settings',
				'action' => 'attribute_option_childs',
				$attribute_option_id,
				'admin' => true,
			);

			$this->_callDataAttributeOption($id);
			$this->set('step', 'properties');
			$this->set('active_menu', 'catalog');
			$this->set(compact(
				'module_title', 'id', 'urlStepOptions'
			));

			$this->render('admin_attribute_option_add');
		} else {
			$this->RmCommon->redirectReferer(__('Options does not exist'), 'error');
		}
	}

	public function admin_delete_attribute_options() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'AttributeOption', 'id');

		$result = $this->Attribute->AttributeOption->doDelete( $id );
		$this->RmCommon->setProcessParams($result);
	}

	function _callDataAttributeOptionChild ( $value ) {
		$id = $this->RmCommon->filterEmptyField($value, 'AttributeOption', 'id');
		$attribute_id = $this->RmCommon->filterEmptyField($value, 'AttributeOption', 'attribute_id');
		$parent_id = $this->RmCommon->filterEmptyField($value, 'AttributeOption', 'parent_id');
		$parent = $this->RmCommon->filterEmptyField($value, 'Parent');
		$attributeSets = $this->User->CrmProject->AttributeSet->getData('list');

		if( empty($parent_id) ) {
			$urlBack = array(
				'controller' => 'settings',
				'action' => 'attribute_options',
				$attribute_id,
				'admin' => true,
			);
		} else {
			$urlBack = array(
				'controller' => 'settings',
				'action' => 'attribute_option_childs',
				$parent_id,
				'admin' => true,
			);
		}

		if( !empty($parent) ) {
			$urlStepOptions = array(
				'controller' => 'settings',
				'action' => 'attribute_option_childs',
				$id,
				'admin' => true,
			);
		} else {
			$urlStepOptions = '#';
		}

		$this->set(compact(
			'urlBack', 'urlStepOptions',
			'attributeSets'
		));
	}

	public function admin_attribute_option_childs( $id = false ) {
		$value = $this->Attribute->AttributeOption->getData('first', array(
			'conditions' => array(
				'AttributeOption.id' => $id,
			),
		));

		if( !empty($value) ) {
			$attribute_id = $this->RmCommon->filterEmptyField($value, 'AttributeOption', 'attribute_id');
			$parent_id = $this->RmCommon->filterEmptyField($value, 'AttributeOption', 'parent_id');
			$title = $this->RmCommon->filterEmptyField($value, 'AttributeOption', 'name');
			$module_title = sprintf(__('%s - Manage Options'), $title);

			$this->RmCommon->_callRefineParams($this->params);
			$options =  $this->Attribute->AttributeOption->_callRefineParams($this->params, array(
				'conditions' => array(
					'AttributeOption.parent_id' => $id,
				),
				'limit' => 20,
			));

			$this->paginate =$this->Attribute->AttributeOption->getData('paginate', $options);
			$values = $this->paginate('AttributeOption');

			if( !empty($parent_id) ) {
				$urlStepProperties = array(
					'controller' => 'settings',
					'action' => 'attribute_option_child_edit',
					$parent_id,
					$id,
					'admin' => true,
				);
			} else {
				$urlStepProperties = array(
					'controller' => 'settings',
					'action' => 'attribute_option_edit',
					$attribute_id,
					$id,
					'admin' => true,
				);
			}
			$urlStepOptions = '#';

			$this->_callDataAttributeOptionChild($value);
			$this->set('active_menu', 'catalog');
			$this->set('step', 'manage-options');
			$this->set(compact(
				'values', 'module_title', 'title_for_layout',
				'id', 'urlStepOptions', 'urlStepProperties'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Option does not exist'), 'error');
		}
	}

	public function admin_attribute_option_child_add( $option_id = false ) {
		$value = $this->Attribute->AttributeOption->getData('first', array(
			'conditions' => array(
				'AttributeOption.id' => $option_id,
			),
		));

		if( !empty($value) ) {
			$attribute_id = $this->RmCommon->filterEmptyField($value, 'AttributeOption', 'attribute_id');
			$title = $this->RmCommon->filterEmptyField($value, 'AttributeOption', 'name');
			$module_title = sprintf(__('%s - New Options'), $title);

			$data = $this->request->data;

			if( !empty($data) ) {
				$data['AttributeOption']['attribute_id'] = $attribute_id;
				$data = $this->RmCommon->beforeSave($data, 'AttributeOption', array(
					'slug' => array(
						'name',
					),
					'order',
				));
			}

			$result = $this->Attribute->AttributeOption->doSave($data, array(
				'id' => $option_id,
				'fieldName' => 'parent_id',
			));
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'attribute_option_childs',
				$option_id,
				'admin' => true
			));

			$this->_callDataAttributeOptionChild($value);
			$this->set('active_menu', 'catalog');
			$this->set('step', 'properties');
			$this->set(compact(
				'module_title', 'id'
			));

			$this->render('admin_attribute_option_add');
		} else {
			$this->RmCommon->redirectReferer(__('Options does not exist'), 'error');
		}
	}

	public function admin_attribute_option_child_edit( $option_id = false, $attribute_option_id ) {
		$value = $this->Attribute->AttributeOption->getData('first', array(
			'conditions' => array(
				'AttributeOption.id' => $attribute_option_id,
				'AttributeOption.parent_id' => $option_id,
			),
		));

		if( !empty($value) ) {
			$value = $this->Attribute->AttributeOption->getMerge($value, $option_id, 'Parent', 'first');
			$title = $this->RmCommon->filterEmptyField($value, 'Parent', 'name');
			$module_title = sprintf(__('%s - Edit Options'), $title);

			$data = $this->request->data;

			if( !empty($data) ) {
				$attribute_id = $this->RmCommon->filterEmptyField($value, 'Parent', 'attribute_id');
				$data['AttributeOption']['attribute_id'] = $attribute_id;

				$data = $this->RmCommon->beforeSave($data, 'AttributeOption', array(
					'slug' => array(
						'name',
					),
					'order',
				));
			}

			$result = $this->Attribute->AttributeOption->doSave( $data, array(
				'id' => $option_id,
				'fieldName' => 'parent_id',
			), $value, $attribute_option_id );
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'attribute_option_childs',
				$option_id,
				'admin' => true
			));

			$this->_callDataAttributeOptionChild($value);
			$this->set('active_menu', 'catalog');
			$this->set('step', 'properties');
			$this->set(compact(
				'module_title', 'id'
			));

			$this->render('admin_attribute_option_add');
		} else {
			$this->RmCommon->redirectReferer(__('Options does not exist'), 'error');
		}
	}

	function admin_attribute_sets() {
		$this->loadModel('AttributeSet');
		$title_for_layout = $module_title = __('Attribute Sets');

		$this->RmCommon->_callRefineParams($this->params);
		$options =  $this->Attribute->AttributeSetOption->AttributeSet->_callRefineParams($this->params, array(
			'limit' => 20,
		));

		$this->paginate = $this->Attribute->AttributeSetOption->AttributeSet->getData('paginate', $options);
		$values = $this->paginate('AttributeSet');

		$this->set('active_menu', 'catalog_set');
		$this->set(compact(
			'values', 'module_title', 'title_for_layout'
		));
	}

	public function admin_attribute_set_add() {
		$module_title = __('New Attribute Set');

		$data = $this->request->data;

		if( !empty($data) ) {
			$data = $this->RmCommon->beforeSave($data, 'AttributeSet', array(
				'slug' => array(
					'name',
				),
			));
		}

		$result = $this->Attribute->AttributeSetOption->AttributeSet->doSave( $data );
		$id = $this->RmCommon->filterEmptyField($result, 'id');
		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'settings',
			'action' => 'attribute_set_options',
			$id,
			'admin' => true
		));

		$urlStepOptions = '#';
		$this->RmCommon->_layout_file('color-picker');

		$this->set('step', 'properties');
		$this->set('active_menu', 'catalog_set');
		$this->set(compact(
			'module_title', 'urlStepOptions'
		));
	}

	public function admin_attribute_set_edit( $id = false ) {
		$module_title = __('Edit Attribute Set');
		$value = $this->Attribute->AttributeSetOption->AttributeSet->getData('first', array(
			'conditions' => array(
				'AttributeSet.id' => $id,
			),
		));

		if( !empty($value) ) {
			$data = $this->request->data;

			if( !empty($data) ) {
				$data = $this->RmCommon->beforeSave($data, 'AttributeSet', array(
					'slug' => array(
						'name',
					),
				));
			}

			$result = $this->Attribute->AttributeSetOption->AttributeSet->doSave( $data, $value, $id );
			$attribute_set_id = $this->RmCommon->filterEmptyField($result, 'id');
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'attribute_set_options',
				$attribute_set_id,
				'admin' => true
			));

			$urlBack = array(
				'controller' => 'settings',
				'action' => 'attribute_sets',
				'admin' => true,
			);
			$urlStepOptions = array(
				'controller' => 'settings',
				'action' => 'attribute_set_options',
				$id,
				'admin' => true,
			);
			$this->RmCommon->_layout_file('color-picker');

			$this->set('step', 'properties');
			$this->set('active_menu', 'catalog_set');
			$this->set(compact(
				'module_title', 'id', 'urlStepOptions',
				'urlBack'
			));

			$this->render('admin_attribute_set_add');
		} else {
			$this->RmCommon->redirectReferer(__('Attribute does not exist'), 'error');
		}
	}

	public function admin_attribute_set_options( $id = false ) {
		$value = $this->Attribute->AttributeSetOption->AttributeSet->getData('first', array(
			'conditions' => array(
				'AttributeSet.id' => $id,
			),
		));

		if( !empty($value) ) {
			$title = $this->RmCommon->filterEmptyField($value, 'AttributeSet', 'name');
			$module_title = sprintf(__('%s - Manage Options'), $title);

			$data = $this->request->data;

			if( !empty($data) ) {
				$result = $this->Attribute->AttributeSetOption->doSave( $data, $id );
				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'settings',
					'action' => 'attribute_sets',
					'admin' => true
				));
			}

			$targets = $this->Attribute->AttributeSetOption->getData('list', array(
				'conditions' => array(
					'AttributeSetOption.attribute_set_id' => $id,
				),
				'fields' => array(
					'Attribute.id', 'Attribute.name',
				),
				'contain' => array(
					'Attribute',
				),
			));

			if( !empty($targets) ) {
				$targetId = array_keys($targets);
			} else {
				$targetId = false;
			}

			$defaults = $this->Attribute->getData('list', array(
				'fields' => array(
					'Attribute.id', 'Attribute.name',
				),
				'conditions' => array(
					'Attribute.id NOT' => $targetId,
				),
			), array(
				'parent' => true,
			));

			$urlStepProperties = array(
				'controller' => 'settings',
				'action' => 'attribute_set_edit',
				$id,
				'admin' => true,
			);
			$urlBack = array(
				'controller' => 'settings',
				'action' => 'attribute_sets',
				'admin' => true,
			);
			$urlStepOptions = array(
				'controller' => 'settings',
				'action' => 'attribute_set_options',
				$id,
				'admin' => true,
			);

			$this->set('step', 'manage-options');
			$this->set('active_menu', 'catalog_set');
			$this->set(compact(
				'targets', 'module_title', 'title_for_layout',
				'id', 'urlStepProperties', 'urlBack',
				'defaults', 'urlStepOptions'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Attribute set does not exist'), 'error');
		}
	}

	function admin_download( $action_type = false, $id = false, $fieldName = false ){
		if( !empty($id) ){
			switch ( $action_type ) {
				case 'crm_document':
					$value = $this->User->CrmProject->CrmProjectDocument->getData('first', array(
						'conditions' => array(
							'CrmProjectDocument.id' => $id
						)
					));
					$default_path = $this->RmCommon->filterEmptyField($value, 'CrmProjectDocument', 'save_path');
					$save_path = Configure::read('__Site.document_folder');
					$save_path = !empty($default_path)?$default_path:$save_path;

					if( empty($value) ) {
						$this->RmCommon->redirectReferer(__('File tidak ditemukan.'), 'error');
					} else {
						$file_name = $this->RmCommon->filterEmptyField($value, 'CrmProjectDocument', 'file');
						$filepath = $this->RmImage->fileExist($save_path, 'fullsize', $file_name);
					}
				break;
				case 'crm_kpr':
					$value = $this->User->CrmProject->KprApplication->getData('first', array(
						'conditions' => array(
							'KprApplication.id' => $id
						),
					), array(
						'admin_mine' => true,
					));
					$crm_project_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'crm_project_id');
					$value = $this->User->CrmProject->getMerge($value, $crm_project_id);

					if( !empty($crm_project_id) ) {
						$save_path = Configure::read('__Site.document_folder');
					} else {
						$save_path = Configure::read('__Site.general_folder');
					}
					
					// if( empty($value['CrmProject']) ) {
					// 	$this->RmCommon->redirectReferer(__('File tidak ditemukan.'), 'error');
					// } else {
						$file_name = $this->RmCommon->filterEmptyField($value, 'KprApplication', $fieldName);
						$filepath = $this->RmImage->fileExist($save_path, 'fullsize', $file_name);
					// }
				break;
				case 'report':
					$this->loadModel('ReportQueue');
					$value = $this->ReportQueue->getData('first', array(
						'conditions' => array(
							'ReportQueue.id' => $id,
						),
					), array(
						'status' => 'all'
					));

					if( empty($value) ) {
						$this->RmCommon->redirectReferer(__('File tidak ditemukan.'), 'error');
					} else {
						$file_name 	= $this->RmCommon->filterEmptyField($value, 'ReportQueue', $fieldName);
						$filepath	= Configure::read('__Site.webroot_files_path').'/properties/report/excel/'.$file_name;

						if( !file_exists($filepath) ) {
							$filepath = false;
						}
					}
				break;
			}
			
			if( !empty($filepath) ) {
				$this->set(compact('filepath'));

				$this->layout = false;
				$this->render('/Elements/blocks/common/download');
			} else {
				$this->RmCommon->redirectReferer(__('File tidak ditemukan.'), 'error');
			}
		}else{
			$this->RmCommon->redirectReferer(__('File tidak ditemukan.'), 'error');
		}
	}

	function admin_delete_template_ebrosur($field){
		$id = $this->RmCommon->filterEmptyField($this->data_company, 'UserCompanyConfig', 'id');

		if(!empty($id) && !empty($field)){
			$result = $this->User->UserCompanyConfig->delete_template_ebrosur($id, $field);
		}else{
			$result = array(
				'msg' => __('Gagal menghapus template eBrosur'),
				'status' => 'error'
			);
		}

		$this->RmCommon->setProcessParams($result);
	}

	function admin_migrate_company(){
		$this->loadModel('MigrateCompany');

		$module_title = $title_for_layout = __('Adsense Banner');

		$this->RmCommon->_callRefineParams($this->params);

		$options = $this->MigrateCompany->_callRefineParams($this->params, array(
			'contain' => array(
				'User',
				'UserCompany'
			),
			'order' => array(
				'MigrateCompany.created' => 'DESC'
			)
		));

		$this->paginate	= $this->MigrateCompany->getData('paginate', $options);

		$values = $this->paginate('MigrateCompany');

		if(!empty($values)){
			foreach ($values as $key => $value) {
				$migrate_company_id = $this->RmCommon->filterEmptyField($value, 'MigrateCompany', 'id');

				$values[$key] = $this->MigrateCompany->MigrateConfigCompany->getMerge($value, $migrate_company_id);
			}
		}

		$this->set('values', $values);
		$this->set('active_menu', 'migrate_company');
		$this->set('module_title', __('Migrasi Company Web'));
	}

	function admin_add_migrate_company(){
		$this->loadModel('MigrateCompany');

		$data = $this->request->data;

		$data = $this->RmMigrateCompany->__callBeforeSave($data);
		
		$result = $this->MigrateCompany->doSave($data);
		
		$url = array(
			'controller' => 'settings',
			'action' => 'migrate_company',
			'admin' => true
		);

		$this->RmCommon->setProcessParams($result, $url);

		$user_choose_id = $this->RmCommon->filterEmptyField($data, 'MigrateCompany', 'user_id');

		$this->RmMigrateCompany->__callBeforeView($user_choose_id);
		$this->set('active_menu', 'migrate_company');
		$this->set('module_title', __('Tambah Migrasi Company Web'));
	}

	function admin_edit_migrate_company($id = false){
		$this->loadModel('MigrateCompany');

		$migrate = $this->MigrateCompany->getData('first', array(
			'conditions' => array(
				'MigrateCompany.in_proccess' => 0,
				'MigrateCompany.canceled' => 0,
				'MigrateCompany.is_complete_sync' => 0,
				'MigrateCompany.id' => $id
			),
			'contain' => array(
				'MigrateAdvanceCompany',
				'MigrateConfigCompany'
			)
		));

		if(!empty($migrate)){
			$data = $this->request->data;

			$data = $this->RmMigrateCompany->__callBeforeSave($data, $migrate);
			
			$result = $this->MigrateCompany->doSave($data, $migrate, $id);

			$url = array(
				'controller' => 'settings',
				'action' => 'migrate_company',
				'admin' => true
			);

			$this->RmCommon->setProcessParams($result, $url);
			
			$user_choose_id = $this->RmCommon->filterEmptyField($data, 'MigrateCompany', 'user_id');

			$this->RmMigrateCompany->__callBeforeView($user_choose_id);

			$this->set('module_title', __('Edit Migrasi Company Web'));
			$this->set('active_menu', 'migrate_company');
			$this->render('admin_add_migrate_company');
		}else{
			$result = array(
				'status' => 'error',
				'msg' => __('Data tidak ditemukan'),
			);

			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true
			));
		}
	}

	function admin_cancel_migrate_company($id, $type = false){
		$this->loadModel('MigrateCompany');

		$status = 1;
		$text = 'membatalkan';
		$default_status = 'active';

		if($type == 'active'){
			$status = 0;
			$text = 'mengaktifkan';
			$default_status = 'canceled';
		}

		$migrate = $this->MigrateCompany->getData('first', array(
			'conditions' => array(
				'MigrateCompany.in_proccess' => 0,
				'MigrateCompany.id' => $id
			)
		), array(
			'status' => $default_status
		));

		if(!empty($migrate)){
			if( $this->MigrateCompany->statusComplete($id, 'canceled', $status) ){
				$result = array(
					'msg' => sprintf(__('Berhasil %s migrasi'), $text),
					'status' => 'success'
				);
			} else {
				$result = array(
					'msg' => sprintf(__('Gagal %s migrasi'), $text),
					'status' => 'error'
				);
			}
		}else{
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 'error'
			);
		}

		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true
		));
	}

	function master_data (){
		$data = $this->request->data;
		$globalData = Configure::read('Global.Data');
		
		$this->loadModel('Electricity');
		$this->loadModel('CronjobPeriod');
		
		$lastupdated = $this->RmCommon->filterEmptyField($data, 'lastupdated');
        $furnishedOptions = $this->RmCommon->filterEmptyField($globalData, 'furnished');
        $subjectsOptions = $this->RmCommon->filterEmptyField($globalData, 'subjects');
        $genderOptions = $this->RmCommon->filterEmptyField($globalData, 'gender_options');
        $statusMarital = $this->RmCommon->filterEmptyField($globalData, 'status_marital');

		// Location
		$value['Country'] = $this->User->UserProfile->Country->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['Region'] = $this->User->UserProfile->Region->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['City'] = $this->User->UserProfile->City->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));

		// Property
		$value['PropertyAction'] = $this->User->Property->PropertyAction->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['PropertyType'] = $this->User->Property->PropertyType->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));

		$dataCompany = Configure::read('Config.Company.data');
        $lang = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'language', 'id');

		$this->User->Property->Certificate->virtualFields['name'] = sprintf('Certificate.name_%s', $lang);
		$value['Certificate'] = $this->User->Property->Certificate->getData('all', array(
			'fields' => array(
				'Certificate.id',
	            'Certificate.slug',
	            'Certificate.name',
	            'Certificate.name_id',
	            'Certificate.name_en',
	            'Certificate.property_type_id',
	            'Certificate.is_lang',
	            'Certificate.status',
	            'Certificate.created',
	            'Certificate.modified'
			)
		), array(
			'lastupdated' => $lastupdated,
		));
		
		$value['Currency'] = $this->User->Property->Currency->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['PropertyDirection'] = $this->User->Property->PropertyAsset->PropertyDirection->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['PropertyCondition'] = $this->User->Property->PropertyAsset->PropertyCondition->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['LotUnit'] = $this->User->Property->PropertyAsset->LotUnit->getData('all', array(
			// 'group' => array(
			// 	'LotUnit.name',
			// ),
		), array(
			'lastupdated' => $lastupdated,
		));
		$value['ViewSite'] = $this->User->Property->PropertyAsset->ViewSite->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['Facility'] = $this->User->Property->PropertyFacility->Facility->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['Period'] = $this->User->Property->PropertyPrice->Period->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		
		// User
		$value['ClientType'] = $this->User->UserClientType->ClientType->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['Specialist'] = $this->User->UserSpecialist->Specialist->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['Language'] = $this->User->UserLanguage->Language->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['AgentCertificate'] = $this->User->UserAgentCertificate->AgentCertificate->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));
		$value['DocumentCategory'] = $this->User->CrmProject->CrmProjectDocument->DocumentCategory->getData('all', false, array(
			'type' => 'property',
			'lastupdated' => $lastupdated,
		));
		$value['Interior'] = $this->RmCommon->_callGenerateDataModel($furnishedOptions, 'Interior');
		$value['Electricity'] = $this->Electricity->getData('all');

		// global variable
		$color_scheme = $this->RmCommon->getGlobalVariable('color_banner_option');
		$temp = array();
		$idx = 0;
		foreach ($color_scheme as $key => $val) {
			$temp[$idx] = array_merge(array('id' => $key), $val);
			$idx++;
		}
		$value['color_scheme'] = $temp;
		$value['global_options'] = $this->RmCommon->_callSet(array(
			'room_options', 'lot_options', 'price_options', 'subjects'
		), $this->global_variable);

		$language = Configure::read('__Site.language');
		if(!empty($language)){
			foreach ($language as $key => $val) {
				$value['web_language'][] = array(
					'id' => $key,
					'value' => $val,
				);
			}
		}

		$categoryMedias = $this->User->Property->PropertyMedias->CategoryMedias->getData('list');
		$value['CategoryMedia'] = $this->RmCommon->_callGenerateDataModel($categoryMedias, 'CategoryMedia');

		if( !empty($lastupdated) ) {
			$value['Subarea'] = $this->User->UserProfile->Subarea->getData('all', array(
				'contain' => false,
			), array(
				'lastupdated' => $lastupdated,
			));
		}

		$value['Gender'] = $this->RmCommon->_callGenerateDataModel($genderOptions, 'Gender');
		$value['SubjectSupport'] = $this->RmCommon->_callGenerateDataModel($subjectsOptions, 'subjects');
		$value['CompanyStatus'] = $this->RmCommon->_callGenerateDataModel(Configure::read('__Site.UserCompany.Status'), 'status');
		$value['ReportGroupTypes'] = $this->RmCommon->_callGenerateDataModel(Configure::read('__Site.Report.GroupTypes'), 'ReportGroupTypes');
		$value['ReportGroupProperty'] = $this->RmCommon->_callGenerateDataModel(Configure::read('__Site.Report.GroupProperty'), 'ReportGroupProperty');
		$value['ReportStatusProperty'] = $this->RmCommon->_callGenerateDataModel(Configure::read('Property.Status'), 'ReportStatusProperty');
		$value['AdminRumahku'] = $this->RmCommon->_callGenerateDataModel(Configure::read('__Site.Admin.List.id'), 'AdminRumahku');
		$value['StatusMarital'] = $this->RmCommon->_callGenerateDataModel($statusMarital, 'StatusMarital');
		$value['PaymentType'] = $this->RmCommon->_callGenerateDataModel(array(
    		'kpr' => __('KPR'),
    		'cash' => __('Cash'),
		), 'PaymentType');

		$termInstallment = Common::_callPeriodeYear(30);
		$value['TermInstallment'] = $this->RmCommon->_callGenerateDataModel($termInstallment, 'TermInstallment');
		
		$cacheName = 'MasterApi';
		$cacheData = Cache::read($cacheName, 'default');

		if( !empty($cacheData) ) {
			$value['CronjobPeriod'] = $cacheData['CronjobPeriod'];
			$value['AttributeOption'] = $cacheData['AttributeOption'];
			$value['ClientJobType'] = $cacheData['ClientJobType'];
			$value['DocumentClient'] = $cacheData['DocumentClient'];
			$value['DocumentClientSpouse'] = $cacheData['DocumentClientSpouse'];
			$value['DocumentKPR'] = $cacheData['DocumentKPR'];
			$value['BankApplyCategory'] = $cacheData['BankApplyCategory'];
		} else {
			$value['CronjobPeriod'] = $this->CronjobPeriod->getData('all', array(
				'fields' => array(
					'CronjobPeriod.id', 'CronjobPeriod.name'
				)
			));

			$this->loadModel('Attribute');
			$attributeOptions = $this->Attribute->AttributeOption->getData('all', array(
				'conditions' => array(
					'AttributeOption.show' => true,
				),
			));

			if( !empty($attributeOptions) ) {
				$result = array();

				foreach ($attributeOptions as $key => $attr) {
					$id = Common::hashEmptyField($attr, 'AttributeOption.id');
					$parent_id = Common::hashEmptyField($attr, 'AttributeOption.parent_id');

					if( !empty($parent_id) ) {
						$parent = $this->Attribute->AttributeOption->getData('first', array(
							'conditions' => array(
								'AttributeOption.id' => $parent_id,
								'AttributeOption.show' => true,
							),
						));
						$parent_parent_id = Common::hashEmptyField($parent, 'AttributeOption.parent_id');

						if( !empty($parent) ) {
							if( !empty($parent_parent_id) ) {
								$result[$parent_parent_id]['Child'][$parent_id]['Child'][$id] = $attr;
							} else {
								$result[$parent_id]['Child'][$id] = $attr;
							}
						}
					} else {
						$result[$id] = $attr;
					}
				}
			}

			$value['AttributeOption'] = $result;

			Cache::write($cacheName, $value, 'default');
		}

		$this->RmCommon->_callDataForAPI($value, 'manual');
	}
	
	function master_subarea (){
		$data = $this->request->data;
		
		$lastupdated = $this->RmCommon->filterEmptyField($data, 'lastupdated');

		// Location
		$value['Subarea'] = $this->User->UserProfile->Subarea->getData('all', false, array(
			'lastupdated' => $lastupdated,
		));

		$this->RmCommon->_callDataForAPI($value, 'manual');
	}

	function backprocess_update_device(){
		$data = $this->request->data;

		$result = $this->User->UserConfig->updateDeviceId($data);

		$this->RmCommon->setProcessParams($result, false);
	}

	function admin_mobile_app_versions(){
		$module_title = __('Mobile App Version');

		$this->loadModel('MobileAppVersion');

		$options =  $this->MobileAppVersion->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);

        $this->paginate = $this->MobileAppVersion->getData('paginate', $options);
		$values = $this->paginate('MobileAppVersion');

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'active_menu' => 'mobile_app_versions',
		));
	}

	function admin_mobile_app_version_add(){
		$module_title = __('Tambah Mobile App Version');
    	$this->loadModel('PropertyStatusListing');

		$this->RmSetting->_callBeforeSaveMobileAppVersion();

		$this->set(array(
			'module_title' => $module_title,
		));

		$this->render('admin_mobile_app_version_form');
	}

	function admin_mobile_app_version_edit($id){
		$module_title = __('Edit Mobile App Version');
		$this->loadModel('MobileAppVersion');

		if (!$this->MobileAppVersion->exists($id)) {
			throw new NotFoundException(__('Invalid MobileAppVersion'));
		}else{
			$value = $this->MobileAppVersion->getData('first', array(
				'conditions' => array(
					'MobileAppVersion.id' => $id
				)
			));

			if(empty($value)){
				$this->RmCommon->redirectReferer(__('Mobile App Version tidak ditemukan.'));
			}

			$this->RmSetting->_callBeforeSaveMobileAppVersion($value, $id);
		}

		$this->set(array(
			'module_title' => $module_title,
		));

		$this->render('admin_mobile_app_version_form');
	}

	function admin_mobile_app_version_view($id){
		$module_title = __('Info Mobile App Version');
		$this->loadModel('MobileAppVersion');

		if (!$this->MobileAppVersion->exists($id)) {
			throw new NotFoundException(__('Invalid MobileAppVersion'));
		}else{
			$value = $this->MobileAppVersion->getData('first', array(
				'conditions' => array(
					'MobileAppVersion.id' => $id
				)
			));

			if(empty($value)){
				$this->RmCommon->redirectReferer(__('Mobile App Version tidak ditemukan.'));
			}

			$this->set(array(
				'value' => $value, 
				'urlBack' => array(
					'controller' => 'settings',
					'action' => 'mobile_app_versions',
					'admin' => true
				)
			));
		}

		$this->set(array(
			'module_title' => $module_title,
			'active_menu' => 'mobile_app_versions',
		));
	}

	function admin_mobile_app_version_delete($id){
		$data = $this->request->data;
		$id = Common::hashEmptyField($data, 'MobileAppVersion.id');

		$this->loadModel('MobileAppVersion');
    	$result = $this->MobileAppVersion->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_import_kpr(){
		$this->loadModel('Kpr');
		$this->RmCommon->_callRefineParams($this->params->params);
		$options = $this->User->Kpr->_callRefineParams($this->params->params, array(
			'order' => array(
				'Kpr.modified' => 'DESC',
				'Kpr.id' => 'DESC',
			),
		), $this->Auth->user('group_id'));

		$optionsStatus = array(
			'admin_mine' => true,
			'status' => 'application',
		);

		$options['conditions']['Kpr.property_id <>'] = 0;
		$options['conditions']['Kpr.is_generate'] = true;
		$options['limit'] = 5;
		$this->paginate = $this->User->Kpr->getData('paginate', $options, $optionsStatus);
		$values = $this->paginate('Kpr');

		if($values){
			foreach ($values as $key => &$value) {
				$id = Common::hashEmptyField($value, 'Kpr.id');

				$kprBanks = $this->Kpr->KprBank->getData('all', array(
					'conditions' => array(
						'KprBank.kpr_id' => $id,
						'KprBank.document_status <>' => 'cart_kpr',
					),
				));

				$kprBanks = $this->Kpr->KprBank->callMergeList($kprBanks, array(
					'contain' => array(
						'Bank',
						'BankSetting' => array(
							'elements' => array(
								'type' => 'all',
							),
							'contain' => array(
								'BankProduct',
							),
						),
						'KprBankInstallment' => array(
							'type' => 'first',
							'order' => array(
								'KprBankInstallment.status_confirm' => 'DESC',
							),
							'elements' => array(
								'status' => 'all',
							),
						),
					),
				));

				if(!empty($kprBanks)){
					$value['KprBank'] = $kprBanks;
					$value['Kpr']['count'] = count($kprBanks);
				}

				$value = $this->User->Kpr->_callMergeProperty($value);
			}
		}

		## POST IMPORT EXCEL
		$data = $this->request->data;
		if($data){
			$data = $this->RmSetting->_callBeforeSaveKPR($data);
		}
		##

		$this->set(array(
			'values' => $values,
			'active_menu' => 'import_kpr',
			'module_title' => __('Import KPR manual'),
		));
	}

	function download_xls(){
		$filepath = Configure::read('__Site.webroot_files_path').DS.'import_kpr.xls';

		$this->set(array(
			'filepath' => $filepath,
			'basename' => __('templete_kpr'),
			'separator' => '_',
		));

	    $this->layout = false;
	    $this->render('/Elements/blocks/common/download');
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

	public function admin_cache($cacheType = null, $clear = false){
		$viewCache	= array_diff(scandir(CACHE.'views'), array('.', '..'));
		$queryCache	= array_diff(scandir(CACHE), array('.', '..', 'minify', 'views'));

		if($queryCache){
			$temp = array();
			foreach($queryCache as $dir){
				$path = sprintf('%s%s', CACHE, $dir);
				$type = is_dir($path) ? 'group' : 'single';

				$temp[$type][] = $dir;
			}

			$queryCache = $temp;
		}

		if($cacheType && $clear){
			if(in_array($cacheType, array('all', 'view')) && $clear){
				clearCache(array('*', 'views', '.php'));
			}

			if(in_array($cacheType, array('all', 'query', 'single_query', 'group_query')) && $clear){
				if($queryCache){
					if(in_array($cacheType, array('all', 'query', 'group_query')) && !empty($queryCache['group'])){
						foreach($queryCache['group'] as $cacheGroup){
							Cache::clearGroup($cacheGroup);
						}
					}

					if(in_array($cacheType, array('all', 'query', 'single_query'))){
						Cache::clear();
					}
				}
			}

			$this->RmCommon->setCustomFlash(__('Berhasil menghapus cache'), 'success');
			$this->redirect('cache');
		}

		$this->set(compact('viewCache', 'queryCache'));
	}
}
