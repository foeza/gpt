<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $uses = array();
	public $components = array(
		'RmImage', 'Captcha', 'RmRecycleBin', 
		'RmPage', 'RmCommon', 'RmApiProject',
	);
	public $helpers = array(
		'FileUpload.UploadForm', 'Property',
	);

	function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow(array(
			'home', 'developers', 'about', 'career', 
			'faq', 'developer_detail', 'contact', 'apps',
			'launcher', 'search', 'maintenance',
			'list_product', 'detail_product_unit', 'unit_list', 'detail_unit',
			'migrasi_data', 'admin_view', 'stocks_booking', 'booking', 'test_app'
		));

		$pageList = array($this->params->controller => array(
			'blog' => 'is_blog', 
			'faq' => 'is_faq', 
			'developers' => 'is_developer_page', 
			'career' => 'is_career',
		));
		$companyConfig = Configure::read('Config.Company.data.UserCompanyConfig');
		$pageRules = $companyConfig ? array_intersect_key($companyConfig, array_flip($pageList[$this->params->controller])) : array();

		if($pageRules){
			$isAllowed = $this->RmCommon->authPage($pageList, $pageRules);
			if($isAllowed === FALSE){
				$this->redirect('/');
			}
		}

		$this->RmPage->_callRedirect301();
	}

	public function home(){
		$this->RmPage->callBeforeViewHomepage();

	}

	public function admin_slides(){
		$personalPackageID	= Configure::read('User.data.UserConfig.membership_package_id');
		$isAdmin			= Common::validateRole('admin');
		$isAgent			= Common::validateRole('agent');
		$isCompanyAdmin		= Common::validateRole('company_admin');

		if(!$isAgent || ($isAgent && $personalPackageID)){
			$module_title = __('Slide Utama');
			$this->loadModel('BannerSlide');

			$options =  $this->BannerSlide->_callRefineParams($this->params, array(
				'limit' => Configure::read('__Site.config_new_table_pagination'),
			));
			$elements = $this->RmCommon->_callRefineParams($this->params);

			$this->paginate = $this->BannerSlide->getData('paginate', $options, $elements);
			$values = $this->paginate('BannerSlide');

			$this->set('active_menu', 'slide');
			$this->set(compact(
				'values', 'module_title'
			));
		}
		else{
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman tersebut'), 'error', $this->Auth->loginRedirect);
		}
	}

	public function admin_add_slide() {
		$personalPackageID	= Configure::read('User.data.UserConfig.membership_package_id');
		$isAdmin			= Common::validateRole('admin');
		$isAgent			= Common::validateRole('agent');
		$isCompanyAdmin		= Common::validateRole('company_admin');

		if(!$isAgent || ($isAgent && $personalPackageID)){
			$module_title = __('Tambah Slide Utama');
	    	$urlRedirect = array(
	            'controller' => 'pages',
	            'action' => 'slides',
	            'admin' => true
	        );

	    	$user_id = $this->user_id;
			$user = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $user_id
				),
			));

			if( !empty($user) ) {
				$this->loadModel('BannerSlide');
				$data = $this->request->data;
				$save_path = Configure::read('__Site.general_folder');
				
				$data = $this->RmImage->_uploadPhoto( $data, 'BannerSlide', 'photo', $save_path );
				$data = $this->RmCommon->_callBeforeSaveBanner($data, 'BannerSlide');

				$result = $this->BannerSlide->doSave($data);
				$this->RmCommon->setProcessParams($result, $urlRedirect);

				$this->request->data = $this->RmCommon->_callBeforeRenderBanner($this->request->data, 'BannerSlide');

				$this->set('active_menu', 'slide');
				$this->set(compact(
					'module_title'
				));
				$this->render('slide_form');
			} else {
				$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
			}
		}
		else{
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman tersebut'), 'error', $this->Auth->loginRedirect);
		}
	}

	public function admin_edit_slide( $banner_id ) {
        $personalPackageID	= Configure::read('User.data.UserConfig.membership_package_id');
		$isAdmin			= Common::validateRole('admin');
		$isAgent			= Common::validateRole('agent');
		$isCompanyAdmin		= Common::validateRole('company_admin');

		if(!$isAgent || ($isAgent && $personalPackageID)){
	        $module_title = __('Edit Slide Utama');
	        $urlRedirect = array(
	            'controller' => 'pages',
	            'action' => 'slides',
	            'admin' => true
	        );

	        $this->loadModel('BannerSlide');
	        $banner = $this->BannerSlide->getData('first', array(
	        	'conditions' => array(
					'BannerSlide.id' => $banner_id,
				),
			));

			if( !empty($banner) ) {
			//	capture old photo, image file has to be deleted when new image file uploaded
				$oldPhoto = isset($banner['BannerSlide']['photo']) && $banner['BannerSlide']['photo'] ? $banner['BannerSlide']['photo'] : NULL;

				$data = $this->request->data;
				$save_path = Configure::read('__Site.general_folder');
				
				$data = $this->RmImage->_uploadPhoto( $data, 'BannerSlide', 'photo', $save_path );
				$data = $this->RmCommon->_callBeforeSaveBanner($data, 'BannerSlide');
				$result = $this->BannerSlide->doSave( $data, $banner, $banner_id );

			//	if user upload new photo, delete old photo
				if(isset($this->request->data['BannerSlide']['photo']['name'])){
					$uploadPhoto = $this->request->data['BannerSlide']['photo']['name'];

					if($uploadPhoto && $oldPhoto && $result['status'] == 'success'){
						$permanent = FALSE;
						$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
					}
				}

				$this->RmCommon->setProcessParams($result, $urlRedirect);

				$this->request->data = $this->RmCommon->_callBeforeRenderBanner($this->request->data, 'BannerSlide');

				$this->set('active_menu', 'slide');
				$this->set(compact(
					'module_title'
				));
				$this->render('slide_form');
			} else {
				$this->RmCommon->redirectReferer(__('Slide tidak ditemukan'));
			}
		}
		else{
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman tersebut'), 'error', $this->Auth->loginRedirect);
		}
    }

    public function admin_delete_multiple_slide() {
    	$personalPackageID	= Configure::read('User.data.UserConfig.membership_package_id');
		$isAdmin			= Common::validateRole('admin');
		$isAgent			= Common::validateRole('agent');
		$isCompanyAdmin		= Common::validateRole('company_admin');

		if(!$isAgent || ($isAgent && $personalPackageID)){
			$data = $this->request->data;
			$id = $this->RmCommon->filterEmptyField($data, 'BannerSlide', 'id');

			$this->loadModel('BannerSlide');
			$result = $this->BannerSlide->doDelete( $id );
			$this->RmCommon->setProcessParams($result, false, array(
				'redirectError' => true,
			));
		}
		else{
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman tersebut'));
		}
    }

	public function admin_partnerships(){

		$module_title = __('Partnership');
		$this->loadModel('Partnership');

        $options =  $this->Partnership->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);

        $this->paginate = $this->Partnership->getData('paginate', $options);
		$values = $this->paginate('Partnership');

		$this->set('active_menu', 'partnership');
		$this->set(compact(
			'values', 'module_title'
		));
	}

	public function admin_add_partnership() {

		$module_title = __('Tambah Partnership');
    	$urlRedirect = array(
            'controller' => 'pages',
            'action' => 'partnerships',
            'admin' => true
        );

    	$user_id = $this->user_id;
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id
			),
		));

		if( !empty($user) ) {
			$this->loadModel('Partnership');
			$data = $this->request->data;
			$save_path = Configure::read('__Site.logo_photo_folder');
			
			$data = $this->RmImage->_uploadPhoto( $data, 'Partnership', 'photo', $save_path );
			$data = $this->RmCommon->_callBeforeSaveBanner($data, 'Partnership');

			$result = $this->Partnership->doSave( $data, $user_id );
			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->set('active_menu', 'partnership');
			$this->set(compact(
				'module_title'
			));
			$this->render('partnership_form');
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	public function admin_edit_partnership( $partnership_id ) {
        	
		$module_title = __('Edit Partnership');
        $urlRedirect = array(
            'controller' => 'pages',
            'action' => 'partnerships',
            'admin' => true
        );

        $this->loadModel('Partnership');
        $partnership = $this->Partnership->getData('first', array(
        	'conditions' => array(
				'Partnership.id' => $partnership_id,
			),
		));

		if( !empty($partnership) ) {
		//	capture old photo, image file has to be deleted when new image file uploaded
			$oldPhoto = isset($partnership['Partnership']['photo']) && $partnership['Partnership']['photo'] ? $partnership['Partnership']['photo'] : NULL;

			$data = $this->request->data;
			$save_path = Configure::read('__Site.logo_photo_folder');
			
			$data = $this->RmImage->_uploadPhoto( $data, 'Partnership', 'photo', $save_path );
			$data = $this->RmCommon->_callBeforeSaveBanner($data, 'Partnership');
			
			$result = $this->Partnership->doSave( $data, $this->user_id, $partnership, $partnership_id );

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['Partnership']['photo']['name'])){
				$uploadPhoto = $this->request->data['Partnership']['photo']['name'];

				if($uploadPhoto && $oldPhoto && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->set('active_menu', 'partnership');
			$this->set(compact(
				'module_title'
			));
			$this->render('partnership_form');
		} else {
			$this->RmCommon->redirectReferer(__('Partnership tidak ditemukan'));
		}
    }

    public function admin_delete_multiple_partnership() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Partnership', 'id');

		$this->loadModel('Partnership');
    	$result = $this->Partnership->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

	public function admin_faq_categories() {

		$module_title = __('Kategori FAQ');
		$this->loadModel('FaqCategory');

        $options =  $this->FaqCategory->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);

        $this->paginate = $this->FaqCategory->getData('paginate', $options);
		$values = $this->paginate('FaqCategory');

		$this->set('active_menu', 'category_faq');
		$this->set(compact(
			'values', 'module_title'
		));
	}

	public function admin_add_faq_category() {

    	$module_title = __('Tambah Kategori FAQ');
    	$urlRedirect = array(
            'controller' => 'pages',
            'action' => 'faq_categories',
            'admin' => true
        );

    	$user_id = $this->user_id;
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id
			),
		));

		if( !empty($user) ) {
			
			$this->loadModel('FaqCategory');
			$data = $this->request->data;
			$result = $this->FaqCategory->doSave( $data );
			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->set('active_menu', 'category_faq');
			$this->set(compact(
				'module_title'
			));
			$this->render('faq_category_form');
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

    public function admin_edit_faq_category( $faq_category_id ) {
        
        $module_title = __('Edit Kategori FAQ');
        $urlRedirect = array(
            'controller' => 'pages',
            'action' => 'faq_categories',
            'admin' => true
        );

        $this->loadModel('FaqCategory');
        $faq_category = $this->FaqCategory->getData('first', array(
        	'conditions' => array(
				'FaqCategory.id' => $faq_category_id,
			),
		));

		if( !empty($faq_category) ) {
			$data = $this->request->data;
			$result = $this->FaqCategory->doSave( $data, $faq_category, $faq_category_id );
			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->set('active_menu', 'category_faq');
			$this->set(compact(
				'module_title'
			));
			$this->render('faq_category_form');
		} else {
			$this->RmCommon->redirectReferer(__('Kategori FAQ tidak ditemukan'));
		}
    }

    public function admin_delete_multiple_faq_category() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'FaqCategory', 'id');

		$this->loadModel('FaqCategory');
    	$result = $this->FaqCategory->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

	public function admin_faqs() {

		$module_title = __('FAQ');
		$this->loadModel('Faq');

		$options =  $this->Faq->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
			'contain' => array(
				'FaqCategory',
			),
		));
		$this->RmCommon->_callRefineParams($this->params);
        $this->paginate = $this->Faq->getData('paginate', $options);
		$values = $this->paginate('Faq');
		$categories = $this->Faq->FaqCategory->getData('list');

		$this->set('active_menu', 'faq');
		$this->set(compact(
			'values', 'module_title',
			'categories'
		));
	}

	public function admin_add_faq() {

    	$module_title = __('Tambah FAQ');
    	$urlRedirect = array(
            'controller' => 'pages',
            'action' => 'faqs',
            'admin' => true
        );

    	$user_id = $this->user_id;
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id
			),
		));

		if( !empty($user) ) {
			
			$this->loadModel('Faq');
			$this->loadModel('FaqCategory');
			$faq_categories = $this->FaqCategory->getData('list');

			$data = $this->request->data;
			$result = $this->Faq->doSave( $data );
			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->set('active_menu', 'faq');
			$this->set(compact(
				'faq_categories', 'module_title'
			));
			$this->render('faq_form');
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

    public function admin_edit_faq( $faq_id ) {
        
        $module_title = __('Edit FAQ');
        $urlRedirect = array(
            'controller' => 'pages',
            'action' => 'faqs',
            'admin' => true
        );

        $this->loadModel('Faq');
        $faq = $this->Faq->getData('first', array(
        	'conditions' => array(
				'Faq.id' => $faq_id,
			),
		));

		if( !empty($faq) ) {
			$this->loadModel('FaqCategory');
		    $faq_categories = $this->FaqCategory->getData('list');

			$data = $this->request->data;
			$result = $this->Faq->doSave( $data, $faq, $faq_id );
			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->set('active_menu', 'faq');
			$this->set(compact(
				'faq_categories', 'module_title'
			));
			$this->render('faq_form');
		} else {
			$this->RmCommon->redirectReferer(__('FAQ tidak ditemukan'));
		}
    }

	public function admin_delete_multiple_faq() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Faq', 'id');

		$this->loadModel('Faq');
    	$result = $this->Faq->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    function admin_search ( $action, $_admin = true ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			'admin' => $_admin,
		);
		$this->RmCommon->processSorting($params, $data);
	}

	function search ( $action = 'developers', $addParam = false ) {
		$this->admin_search($action, $addParam);
	}

	function developers(){
		$this->loadModel('ApiAdvanceDeveloper');

		$url_without_http = Configure::read('__Site.domain');
		$module_title = __('Developers');

		$title_for_layout 		= sprintf(__('%s Properti - %s'), $module_title, $url_without_http);
		$keywords_for_layout 	= sprintf(__('Developer Properti di  %s'), $url_without_http);
		$description_for_layout	= sprintf(__('Cari Developer di %s dengan harga properti terjangkau!'), $url_without_http);

		$parent_id = Configure::read('Principle.id');

		$this->ApiAdvanceDeveloper->unbindModel(array(
			'hasMany' => array(
				'ApiRequestDeveloper', 
			), 
		));
		$this->ApiAdvanceDeveloper->bindModel(array(
            'hasOne' => array(
                'ApiRequestDeveloper' => array(
                    'className' => 'ApiRequestDeveloper',
                    'foreignKey' => false,
                    'conditions' => array(
			            'ApiRequestDeveloper.api_advance_developer_id = ApiAdvanceDeveloper.original_id',
                	),
                ),
            )
        ), false);

		// special case condition developer 
		// old data and data from primedev
		$options =  $this->ApiAdvanceDeveloper->_callRefineParams($this->params, array(
			'conditions' => array(
				'OR' => array(
                	array(
                		'ApiAdvanceDeveloper.type_developer' => 'project_primedev',
                		'ApiRequestDeveloper.end_date >=' => date('Y-m-d'),
                		'ApiRequestDeveloper.principle_id' => $parent_id,
						'ApiRequestDeveloper.status_request' => 'approved',
            		),
                	array(
                		'ApiAdvanceDeveloper.type_developer' => 'old_data',
                		'ApiAdvanceDeveloper.user_id' => $parent_id,
                		'OR' => array(
		                    array(
		                        'ApiAdvanceDeveloper.start_date' => NULL,
		                        'ApiAdvanceDeveloper.end_date' => NULL,
		                    ),
		                    array(
		                        'ApiAdvanceDeveloper.start_date' => '0000-00-00',
		                        'ApiAdvanceDeveloper.end_date' => '0000-00-00',
		                    ),
		                    array(
		                        'ApiAdvanceDeveloper.start_date <=' => date('Y-m-d'),
		                        'ApiAdvanceDeveloper.end_date' => '0000-00-00',
		                    ),
		                    array(
		                        'ApiAdvanceDeveloper.start_date' => '0000-00-00',
		                        'ApiAdvanceDeveloper.end_date >=' => date('Y-m-d'),
		                    ),
		                    array(
		                        'ApiAdvanceDeveloper.start_date <=' => date('Y-m-d'),
		                        'ApiAdvanceDeveloper.end_date' => NULL,
		                    ),
		                    array(
		                        'ApiAdvanceDeveloper.start_date' => NULL,
		                        'ApiAdvanceDeveloper.end_date >=' => date('Y-m-d'),
		                    ),
		                    array(
		                        'ApiAdvanceDeveloper.start_date <=' => date('Y-m-d'),
		                        'ApiAdvanceDeveloper.end_date >=' => date('Y-m-d'),
		                    ),
	                    ),
            		),
            	),
			),
			'limit' => 12,
			'contain' => array(
				'ApiRequestDeveloper',
			),
			'fields' => array('ApiRequestDeveloper.*', 'ApiAdvanceDeveloper.*'),
			'order' => array(
				'ApiAdvanceDeveloper.order' => 'ASC',
				'ApiAdvanceDeveloper.created' => 'DESC'
			),
		));
		$this->RmCommon->_callRefineParams($this->params);

        $this->paginate = $this->ApiAdvanceDeveloper->getData('paginate', $options, array(
        	'company' => true,
    	));

		$values = $this->paginate('ApiAdvanceDeveloper');

		foreach ($values as $key => &$value) {
			$id_project = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.id');
			$type_developer = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.type_developer');

			if ($type_developer == 'project_primedev') {
				$total_product = $this->ApiAdvanceDeveloper->ApiAdvanceDeveloperProduct->getData('count', array(
	        		'conditions' => array(
	        			'ApiAdvanceDeveloperProduct.project_id' => $id_project,
	        		)), array(
						'status' => 'active',
				));

				$total_unit = $this->ApiAdvanceDeveloper->ApiAdvanceDeveloperProductUnit->getData('count', array(
	        		'conditions' => array(
	        			'ApiAdvanceDeveloperProductUnit.project_id' => $id_project,
	        		)), array(
						'status' => 'active',
				));

				$value = $this->ApiAdvanceDeveloper->getMergeList($value, array(
		            'contain' => array(
		            	'PropertyType',
		            	'Region',
		            	'City',
		            	'ApiAdvanceDeveloperCompany' => array(
		            		'Region',
		            		'City'
		            	),
		            ),
		        ));

				// total product
				$value['TotalProduct'] = $total_product;
				// total productUnit
				$value['TotalProductUnit'] = $total_unit;
			}

		}
		
		$populers = $this->User->Property->populers(5);
		
		// Khusus EasyLiving
		$advices = $this->User->Advice->getData('all', array(
			'limit' => 3,
		));
		$advices = $this->User->Advice->getDataList($advices);

		$this->RmCommon->_callRequestSubarea('Search');
		$this->RmCommon->getDataRefineProperty();

		$this->RmCommon->_layout_file(array(
			'map',
			'map-cozy',
		));

		$this->set('active_menu', 'developers');
		$this->set(compact(
			'module_title', 'title_for_layout',
			'keywords_for_layout', 'description_for_layout',
			'values', 'populers', 'advices'
		));
	}

	function developer_detail( $id = false, $slug = false ) {
		$url_without_http = Configure::read('__Site.domain');

		$this->loadModel('ApiAdvanceDeveloper');
		$value = $this->ApiAdvanceDeveloper->getData('first', array(
			'conditions'=> array(
				'ApiAdvanceDeveloper.id' => $id,
			),
		), array(
        	'status' => 'active',
        	'company' => true,
    	));
		
		if( !empty($value) ) {
			$title = $this->RmCommon->filterEmptyField($value, 'ApiAdvanceDeveloper', 'name');
			$short_content = $this->RmCommon->filterEmptyField($value, 'ApiAdvanceDeveloper', 'promo');
			$photo = $this->RmCommon->filterEmptyField($value, 'ApiAdvanceDeveloper', 'logo');

			$related = $this->ApiAdvanceDeveloper->getDataRelated($id);
    		$populers = $this->User->Property->populers(5);

			$module_title = $title;

			$dataView = $this->RmCommon->_callSaveVisitor($id, 'BannerDeveloperView', 'banner_developer_view_id');
			$this->ApiAdvanceDeveloper->BannerDeveloperView->doSave($dataView);

			$title_for_layout 		= sprintf('%s - %s', $title, $url_without_http);
			$description_for_layout = sprintf(__('Cari %s di %s dengan harga properti terjangkau!'), $title, $url_without_http);
			$keywords_for_layout 	= sprintf('%s di %s', $title, $url_without_http);

			$this->set('title_for_layout', $title_for_layout);
			$this->set('description_for_layout', $description_for_layout);
			$this->set('keywords_for_layout', $keywords_for_layout);

			if(!empty($photo)) {
				$og_meta = array(
					'title' => $title,
					'image' => $photo,
					'path' => Configure::read('__Site.general_folder'),
					'description' => $short_content,
				);
			}

			// Khusus EasyLiving
			$advices = $this->User->Advice->getData('all', array(
				'limit' => 3,
			));
			$advices = $this->User->Advice->getDataList($advices);
			$adviceCategories = $this->User->Advice->AdviceCategory->getData('list');

			$this->RmCommon->_callRequestSubarea('Search');
			$this->RmCommon->getDataRefineProperty();

			$this->set('active_menu', 'developers');
			$this->set(compact(
				'value', 'og_meta', 'related',
				'module_title', 'populers',
				'advices', 'adviceCategories'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Berita tidak ditemukan'));
		}
	}

	// $origin_id = original_id in primedev
	function list_product( $origin_id = false ) {
		// new get data from api primedev
		$this->RmApiProject->dataProject($origin_id);

	}

	// $origin_id = original_id in primedev
	function detail_product_unit( $origin_id = false ) {
		// new get data from api primedev
		$this->RmApiProject->dataProduct($origin_id);

	}

	// detail unit from the product $id = original_id in primedev
	function detail_unit($origin_id = false) {
		// new get data from api primedev
		$this->RmApiProject->dataProductUnit($origin_id);
	
	}

	function unit_list ( $origin_id = false ) {
		// new get data from api primedev
		$this->RmApiProject->listUnit($origin_id);

	}

	function about() {	
		$url_without_http = Configure::read('__Site.domain');
		$module_title = __('Tentang Kami');

		$title_for_layout 		= sprintf('%s %s', $module_title, $url_without_http);
		$description_for_layout = sprintf(__('Media online properti terpercaya di indonesia %s dengan harga properti terjangkau!'), $url_without_http);
		$keywords_for_layout	= sprintf('tentang kami %s', $url_without_http);

		$agents = $this->User->populers();
		$populers = $this->User->Property->populers(5);

		$this->RmCommon->_callRequestSubarea('Search');
		$this->RmCommon->getDataRefineProperty();
		$this->RmCommon->_callCounterListing();

		$this->RmCommon->_layout_file(array(
			'map',
			'map-cozy',
		));
		$this->set('active_menu', 'about');
		$this->set(compact(
			'agents', 'module_title', 'populers', 'title_for_layout', 'description_for_layout',
			'keywords_for_layout'
		));
	}

	function contact($option=false) {
		$url_without_http = Configure::read('__Site.domain');
		$this->set('active_menu', 'contact');

		$module_title = __('Hubungi Kami');

		$title_for_layout 		= sprintf('%s - %s', $module_title, $url_without_http);
		$description_for_layout = sprintf(__('hubungi kami di %s Dapatkan properti pilhan anda dengan cara menghubungi kami'), $url_without_http);
		$keywords_for_layout	= sprintf('hubungi kami %s', $url_without_http);

		$userAdmins = $this->User->getListAdmin( true, true );

		// Proses Contact		
		$data = $this->RmUser->_callMessageBeforeSave($userAdmins);
		$result = $this->User->Message->doSendMany($data, $userAdmins);

		$this->RmCommon->setProcessParams($result, array(
			'controller' => 'pages',
			'action' => 'contact',
			'admin' => false,
		), array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));
		
    	$populers = $this->User->Property->populers(5);

    	$this->RmCommon->_layout_file(array(
			'map',
			'map-cozy',
		));
		$this->set('captcha_code', $this->Captcha->generateEquation());
		$this->set(compact(
			'populers', 'module_title', 'title_for_layout', 'description_for_layout',
			'keywords_for_layout'
		));
	}

	function faq(){
		$this->loadModel('FaqCategory');
		$this->loadModel('Faq');
		
		$module_title = __('FAQ');
		$url_without_http = Configure::read('__Site.domain');

		$title_for_layout = sprintf('%s %s', $module_title, $url_without_http);
		$description_for_layout = sprintf(__('Cara cepat jual rumah %s'), $url_without_http);
		$keywords_for_layout = __('jual rumah, beli rumah, iklan rumah, rumah, tanah, ruko, rukan, apartemen, gudang, kantor');

		$values = false;
		$options2 =  $this->Faq->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
			'contain' => array(
				'FaqCategory',
			),
		));

		$this->paginate = $this->Faq->getData('paginate', $options2);
		$datas = $this->paginate('Faq');

		if (!empty($datas)) {
			$templevel=0;   
			$newkey=0;

			$grouparr[$templevel]="";
			foreach ($datas as $key => $val) {
				if (!empty($val['FaqCategory'])) {
					$data_faq_category['FaqCategory'] = $val['FaqCategory'];

					if ($templevel == $val['FaqCategory']['id']) {
						$grouparr[$templevel]['Faq'][$newkey] = $val;
						$grouparr[$templevel] = array_merge($grouparr[$templevel], $data_faq_category);
					} else {
						$grouparr[$val['FaqCategory']['id']]['Faq'][$newkey] = $val;
						$grouparr[$val['FaqCategory']['id']] = array_merge($grouparr[$val['FaqCategory']['id']], $data_faq_category);
					}
					$newkey++; 

				}
				      
			}

			$values = $grouparr;
		}

    	$populers = $this->User->Property->populers(5);
		$agents = $this->User->populers(4);
		$propertyTypes = $this->User->Property->PropertyType->getData('list', array(
            'cache' => __('PropertyType.List'),
        ));

        $this->set('active_menu', 'faq');
		$this->set(compact(
			'title_for_layout', 'description_for_layout', 'keywords_for_layout',
			'module_title', 'values', 'populers', 'agents', 'propertyTypes'
		));
	}

	function apps( $theme_id = false ) {
		$value = $this->data_company;
		$is_admin = Configure::read('User.admin');
		$options = array();
		$elements = array(
			'company' => true,
		);
		$is_launcher = $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'is_launcher');

		if( !empty($is_launcher) ) {
			if(!empty($theme_id) ) {
				$options['conditions']['UserCompanyLauncher.theme_launcher_id'] = $theme_id;
			}
			else{
				$elements['chosen'] = true;
			}

			$launcher = $this->User->UserCompanyLauncher->getData('first', $options, $elements);
			if(!empty($launcher['UserCompanyLauncher'])){
				$theme_launcher_id = $this->RmCommon->filterEmptyField($launcher, 'UserCompanyLauncher', 'theme_launcher_id');
				$launcher = $this->User->UserCompanyLauncher->ThemeLauncher->getMerge($launcher, $theme_launcher_id);
			}
			else{
				if($theme_id){
				//	launcher belum ada settingan nya, maka load default value dari RmCommon
					$launcher = $this->User->UserCompanyLauncher->ThemeLauncher->getData('first', array('conditions' => array('ThemeLauncher.id' => $theme_id)));

					$launcherColors = Configure::read('Global.Data.launcher_colors');
					if($launcherColors){
						$launcher['UserCompanyLauncher'] = $launcherColors;
						$launcher['UserCompanyLauncher']['button_top'] = 'top';
					}
				}
				else{
					$this->redirect('/');
				}
			}

			$theme	= $this->RmCommon->filterEmptyField($launcher, 'ThemeLauncher', 'slug');
			$name	= $this->RmCommon->filterEmptyField($value, 'UserCompany', 'name');

			$title_for_layout = sprintf(__('LAUNCHER - %s'), $name);

			$this->set(compact('launcher', 'title_for_layout'));
			$this->theme = ucwords($theme);
		}
		else{
			$this->redirect('/');
		}
	}

	function launcher () {
		$value = $this->data_company;
		$is_launcher = $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'is_launcher');
		$launcherUrl = $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'launcher_url');

		if(!empty($is_launcher) && !empty($launcherUrl)) {
			$this->set('title_for_layout', __('Download Launcher'));

			$this->RmCommon->_layout_file('launcher');
			$this->set('_breadcrumb', false);
			$this->set('_launcher_download', false);
		} else {
			$this->RmCommon->redirectReferer(__('Page tidak ditemukan'));
		}
	}

	function _dataSaveMigration($data) {
		if ( !empty($data) ) {
			$result['ApiAdvanceDeveloper'] = array(
	    		'original_id' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'id'),
	    		'user_id' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'user_id'),
	    		'logo' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'photo'),
	    		'name' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'title'),
	    		'promo' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'short_description'),
	    		'description' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'description'),
	    		'url' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'url'),
	    		'order' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'order'),
	    		'start_date' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'start_date'),
	    		'end_date' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'end_date'),
	    		'is_article' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'is_article'),
	    		'status' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'status'),
	    		'created' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'created'),
	    		'modified' => $this->RmCommon->filterEmptyField($data, 'BannerDeveloper', 'modified'),
	    	);
		}

		return $result;
	}

	// sementara, untuk backup ambil data-data lama yang ada di table BannerDeveloper
	function migrasi_data(){
		$this->loadModel('ApiAdvanceDeveloper');
		$this->loadModel('BannerDeveloper');
		$datas = $this->BannerDeveloper->getData('all', array(), array(
			'company' => false,
		));

    	foreach ($datas as $key => $value) {
    		$data_migrasi = $this->_dataSaveMigration($value);
			$result = $this->ApiAdvanceDeveloper->doMigrationBanner($data_migrasi);
    		$msg = __('%s. <br>', $result['msg']);
			echo $msg;
    	}
    	die();
	}

	function maintenance() {
		$this->layout = false;
	}

	function admin_view( $type = null ) {
		$this->layout = 'Emails/html/default';

		if( !empty($type) ) {
			$this->render($type);
		} else {
			$this->RmCommon->redirectReferer(__('Halaman tidak ditemukan'));
		}
	}

	function stocks_booking($project_id, $product_unit_id, $product_id = false, $currenct_blok = false){
		$this->RmBooking = $this->Components->load('RmBooking');

		$this->RmBooking->cartValidationData();
		
		$status_selling = $this->RmBooking->checkAllowSelling($project_id);

		if(!empty($status_selling)){
			$link = 'transactions/booking_stocks';

			if(!empty($product_unit_id)){
				$link .= '/product_unit_id:'.$product_unit_id;
			}
			if(!empty($product_id)){
				$link .= '/product_id:'.$product_id;
			}

			if(!empty($currenct_blok)){
				$link .= '/blok:'.$currenct_blok;
			}

			$data = array();
			$cart_data = $this->Session->read($this->RmBooking->session_cart_name);

			$product_unit_stock_id = Common::hashEmptyField($cart_data, 'BookingDetail.product_unit_stock_id');

			if(!empty($product_unit_stock_id)){
				$data = hash::insert($data, 'BookingDetail.product_unit_stock_id', $product_unit_stock_id);
			}

			$result = $this->RmCommon->getAPI($link, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $project_id,
                    ),
                ),
                'post' => $data,
            ));

			$this->set(array(
				'data' 				=> $result,
				'project_id'		=> $project_id,
				'product_unit_id' 	=> $product_unit_id,
				'product_id' 		=> $product_id,
				'cart_data'			=> $cart_data,
				'currenct_blok'		=> $currenct_blok
			));
		}

		$this->set(array(
			'status_selling' => $status_selling
		));

		$this->render('/Elements/blocks/projects/bookings/table_list_stocks');
	}

	function booking($product_unit_id, $product_unit_stock_id){
		$params = $this->params->params;

		$product_id = Common::hashEmptyField($params, 'named.product_id');

		$data_unit = $this->RmApiProject->rawTypeUnitData($product_unit_id, $product_id);

		$this->RmBooking = $this->Components->load('RmBooking');

		$cart_data = $this->Session->read($this->RmBooking->session_cart_name);

		if(!empty($product_unit_id) && !empty($product_unit_stock_id) && !empty($data_unit)){
			$project_id = Common::hashEmptyField($data_unit, 'ProductUnit.project_id');
			
			$link = sprintf('transactions/check_availability/%s', $product_unit_stock_id);

			$result = $this->RmCommon->getAPI($link, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $project_id,
                    ),
                ),
            ));

			$status = Common::hashEmptyField($result, 'status');

			if($status == 1){
				$data = $this->request->data;

				if(!empty($cart_data)){
					$cart_product_unit_stock_id = Common::hashEmptyField($cart_data, 'BookingDetail.product_unit_stock_id');

					if($cart_product_unit_stock_id == $product_unit_stock_id){
						$result = array(
							'msg' => __('Unit ini sedang dalam cart.'),
							'status' => 0
						);
					}

					$this->request->data = $cart_data;
				}

				$status = Common::hashEmptyField($result, 'status');

				if($status == 1){
					$this->RmBooking->_callBeforeSaveIdentity($data, $product_id, $product_unit_id, $product_unit_stock_id, $project_id);

					$cart_data = $this->Session->read($this->RmBooking->session_cart_name);
				}
			}
		}else{
			$result = array(
				'msg' => __('Data tidak ditemukan'),
				'status' => 0
			);
		}

		$this->set('cart_data', $cart_data);
		$this->set('config', $result);
	}
}
