<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController {
	public $uses = array();
	public $components = array(
		'RmImage', 'Captcha', 'RmRecycleBin', 
		'RmPage', 'RmCommon',
	);
	public $helpers = array(
		'FileUpload.UploadForm', 'Property',
	);

	function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow(array(
			'home', 'about', 'faq', 'contact', 'apps', 'search', 'maintenance',
			'admin_view'
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

	function about() {
		$url_without_http = Configure::read('__Site.domain');
		$module_title = __('Tentang Kami');

		$title_for_layout 		= __('%s %s', $module_title, $url_without_http);
		$description_for_layout = __('Grosir Pasar Tasik terpercaya di indonesia %s dengan harga terjangkau!', $url_without_http);
		$keywords_for_layout	= __('tentang kami %s', $url_without_http);

		$this->RmCommon->_callRequestSubarea('Search');
		$this->RmCommon->getDataRefineProperty();
		$this->RmCommon->_callCounterListing();

		$this->RmCommon->_layout_file(array(
			'map',
			'map-cozy',
		));
		$this->set('active_menu', 'about');
		$this->set(compact(
			'module_title', 'title_for_layout', 'description_for_layout', 'keywords_for_layout'
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

}
