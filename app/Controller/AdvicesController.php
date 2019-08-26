<?php
App::uses('AppController', 'Controller');
class AdvicesController extends AppController {
	var $uses = array(
		'Advice'
	);
	public $components = array(
		'RmImage', 'RmRecycleBin'
	);
	public $helpers = array(
		'Social',
	);

	function beforeFilter(){
		parent::beforeFilter();

		$dataCompany = isset($this->data_company) ? $this->data_company : NULL;
		$companyID = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompany', 'id', 0);
		$this->Advice->companyID = $companyID;

		$this->Auth->allow(array('index', 'search','read',));

		$pageList		= array($this->params->controller => array('index' => 'is_blog', 'read' => 'is_blog'));
		$companyConfig	= Configure::read('Config.Company.data.UserCompanyConfig');
		$pageRules		= $companyConfig ? array_intersect_key($companyConfig, array_flip($pageList[$this->params->controller])) : array();

		if($pageRules){
			$isAllowed = $this->RmCommon->authPage($pageList, $pageRules);
			if($isAllowed === FALSE){
				$this->redirect('/');
			}
		}
		
		// $this->label_title = Configure::read('Global.Data.translates.id.blog');
		$this->label_title = __('Artikel');

		$this->set('global_label_title', $this->label_title);
	}

	function admin_index(){
		$module_title = $title_for_layout = __('Daftar %s', $this->label_title);
		$options =  $this->Advice->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));

		$this->RmCommon->_callRefineParams($this->params);
        $this->paginate = $this->Advice->getData('paginate', $options, array(
        	'status' => 'status-active',
        ));
		$values = $this->paginate('Advice');
		$values = $this->Advice->getDataList($values);

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'title_for_layout' => $title_for_layout,
			'active_menu' => 'advice',
			'_breadcrumb' => false,
		));
	}

	function admin_add() {

    	$module_title = __('Tambah %s', $this->label_title);
    	$urlRedirect = array(
            'controller' 	=> 'advices',
            'action' 		=> 'index',
            'admin' 		=> true
        );

    	$user_id = $this->user_id;
		$advice_categories = $this->Advice->AdviceCategory->getData('list');

		$data = $this->request->data;
		$save_path = Configure::read('__Site.advice_photo_folder');
		
		$data = $this->RmImage->_uploadPhoto( $data, 'Advice', 'photo', $save_path );
		$data = $this->_setSlug($data);
		
		$result = $this->Advice->doSave( $data, $user_id );
		$this->RmCommon->setProcessParams($result, $urlRedirect);
		$this->RmCommon->_layout_file(array(
			'ckeditor',
		));

		$this->set('active_menu', 'advice');
		$this->set(compact(
			'advice_categories', 'module_title'
		));

		$this->render('admin_advice_form');
	}

	function admin_edit( $advice_id = false ) {
        
        $module_title = __('Edit %s', $this->label_title);
        $urlRedirect = array(
            'controller' => 'advices',
            'action' => 'index',
            'admin' => true
        );

        $advice = $this->Advice->getData('first', array(
        	'conditions' => array(
				'Advice.id' => $advice_id,
			),
		), array(
			'status' => 'status-active'
		));

		if( !empty($advice) ) {
		//	capture old photo, image file has to be deleted when new image file uploaded
			$oldPhoto = isset($advice['Advice']['photo']) && $advice['Advice']['photo'] ? $advice['Advice']['photo'] : NULL;

			$advice_categories = $this->Advice->AdviceCategory->getData('list');

			$data = $this->request->data;
			$save_path = Configure::read('__Site.advice_photo_folder');

			$data = $this->RmImage->_uploadPhoto( $data, 'Advice', 'photo', $save_path );
			$data = $this->_setSlug($data);
			$result = $this->Advice->doSave( $data, $this->user_id, $advice, $advice_id );

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['Advice']['photo']['name'])){
				$uploadPhoto = $this->request->data['Advice']['photo']['name'];

				if($uploadPhoto && $oldPhoto && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, $urlRedirect);
			$this->RmCommon->_layout_file('ckeditor');

			$this->set('active_menu', 'advice');
			$this->set(compact('advice_categories', 'module_title'));
			$this->render('admin_advice_form');
		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
    }

    public function admin_delete_multiple_advice() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Advice', 'id');

    	$result = $this->Advice->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

	public function admin_advice_categories() {

		$module_title = __('Kategori %s', $this->label_title);
        $options =  $this->Advice->AdviceCategory->_callRefineParams($this->params, array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);

        $this->paginate = $this->Advice->AdviceCategory->getData('paginate', $options);
		$values = $this->paginate('AdviceCategory');

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'active_menu' => 'advice_category',
			'_breadcrumb' => false,
		));
	}

	public function admin_add_advice_category() {

    	$module_title = __('Tambah Kategori %s', $this->label_title);
    	$urlRedirect = array(
            'controller' => 'advices',
            'action' => 'advice_categories',
            'admin' => true
        );
			
		$data = $this->request->data;
		$result = $this->Advice->AdviceCategory->doSave( $data );
		$this->RmCommon->setProcessParams($result, $urlRedirect);

		$this->set('active_menu', 'advice_category');
		$this->set(compact(
			'module_title'
		));
		$this->render('advice_category_form');
	}

    public function admin_edit_advice_category( $advice_category_id ) {
        
        $module_title = __('Edit Kategori %s', $this->label_title);
        $urlRedirect = array(
            'controller' => 'advices',
            'action' => 'advice_categories',
            'admin' => true
        );

        $advice_category = $this->Advice->AdviceCategory->getData('first', array(
        	'conditions' => array(
				'AdviceCategory.id' => $advice_category_id,
			),
		));

		if( !empty($advice_category) ) {
			$data = $this->request->data;
			$result = $this->Advice->AdviceCategory->doSave( $data, $advice_category, $advice_category_id );
			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->set('active_menu', 'advice_category');
			$this->set(compact(
				'module_title'
			));
			$this->render('advice_category_form');
		} else {
			$this->RmCommon->redirectReferer(__('Kategori tidak ditemukan'));
		}
    }

    public function admin_delete_multiple_advice_category() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Search', 'id');

    	$result = $this->Advice->AdviceCategory->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    function _setSlug( $data ) {
    	if( !empty($data) ) {
    		$title = $this->RmCommon->filterEmptyField($data, 'Advice', 'title');
    		if( !empty($title) ){
    			$data['Advice']['slug'] = $this->RmCommon->toSlug($title);
    		}
    	}

    	return $data;
    }

	function admin_search ( $action, $_admin = true ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			'admin' => $_admin,
		);
		$this->RmCommon->processSorting($params, $data);
	}

	function search ( $action = 'index', $addParam = false ) {
		$this->admin_search($action, $addParam);
	}

	function index() {
		$url_without_http = Configure::read('__Site.domain');
		$module_title = $this->label_title;

		$title_for_layout 		= sprintf('%s %s', $module_title, $url_without_http);
		$description_for_layout = sprintf(__('%s Properti di %s'), $module_title, $url_without_http);
		$keywords_for_layout	= sprintf(__('Halaman %s di %s Dapatkan informasi perkembangan properti update setiap hari'), $module_title, $url_without_http);

	//	cache setting
		$controller		= $this->name;
		$action			= Inflector::camelize($this->action);
		$dataCompany	= isset($this->data_company) ? $this->data_company : NULL;
		$companyID		= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompany', 'id', 0);
		$currentPage	= $this->RmCommon->filterEmptyField($this->params, 'named', 'page', 1);
		$cacheName		= $controller.'.'.$action.'.'.$companyID.'.'.$currentPage;
		$cacheConfig	= 'advices_find';
		$cacheData		= Cache::read($cacheName, $cacheConfig);
		$namedParams	= array_keys($this->params['named']);
		$nonFilter		= array('page');
		$filterParams	= array_diff($namedParams, $nonFilter);

	//	remove special chars
		// if(isset($this->params->named['keyword']) && $this->params->named['keyword']){
		// 	$keyword = urldecode($this->params->named['keyword']);
		// 	$this->request->params['named']['keyword'] = $this->RmCommon->cleanSpecialChar(trim($keyword), ' ');
		// }

		$options = $this->Advice->_callRefineParams($this->params, array('limit' => 20));
		$this->RmCommon->_callRefineParams($this->params);

		if(empty($filterParams) && $cacheData){
		//	find all query, get results from cache (if exist)
			$this->request->params['paging']	= $cacheData['paging'];
			$this->request->params['named']		= $cacheData['named'];
			$this->request->params['pass']		= $cacheData['pass'];
			$this->request->query				= $cacheData['query'];
			$values								= $cacheData['result'];
		}
		else{
			$this->Advice->virtualFields['null_last'] = 'ISNULL(Advice.order)';

			$options['order'] = array(
				'null_last' => 'ASC',
				'Advice.order' => 'ASC',
				'Advice.modified' => 'DESC',
				'Advice.id' => 'DESC'
			);

			$this->paginate = $this->Advice->getData('paginate', $options);
			$values = $this->paginate('Advice');
			$values = $this->Advice->getDataList($values);

			if(empty($filterParams)){
			//	find all query, generate cache
				$cacheData = array(
					'paging'	=> $this->request->params['paging'], 
					'named'		=> $this->request->params['named'], 
					'pass'		=> $this->request->params['pass'], 
					'query'		=> $this->request->query, 
					'result'	=> $values
				);

				Cache::write($cacheName, $cacheData, $cacheConfig);
			}
		}

    	$populers = $this->User->Property->populers(5);
		$adviceCategories = $this->Advice->AdviceCategory->getData('list');

		// Khusus EasyLiving
		$advices = $this->User->Advice->getData('all', array(
			'limit' => 3,
		));
		$advices = $this->User->Advice->getDataList($advices);
		$propertyTypes = $this->User->Property->PropertyType->getData('list', array(
			'cache' => __('PropertyType.List'),
		));
		
		$this->set('active_menu', 'advices');
		$this->set(compact(
			'values', 'module_title', 'title_for_layout', 'description_for_layout', 'keywords_for_layout',
			'populers', 'adviceCategories', 'advices', 'propertyTypes'
		));
	}

	function read( $id = false, $slug = false ) {
	//	cache setting
		$isAdmin = Configure::read('User.admin');
		$group_id = Configure::read('User.group_id');
		$controller		= $this->name;
		$action			= Inflector::camelize($this->action);
		$dataCompany	= isset($this->data_company) ? $this->data_company : NULL;
		$companyID		= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompany', 'id', 0);
		$cacheName		= $controller.'.Detail.'.$companyID.'.'.$id;
		$cacheConfig	= 'advices_detail';
		$cacheData		= Cache::read($cacheName, $cacheConfig);
		
		if(!empty($cacheData) && empty($isAdmin)){
			$this->request->params['named']	= $cacheData['named'];
			$this->request->params['pass']	= $cacheData['pass'];
			$this->request->query			= $cacheData['query'];
			$value							= $cacheData['result'];
		}
		else{

			$value = $this->Advice->getData('first', array(
				'conditions'=> array(
					'Advice.id' => $id,
				),
			), array(
				'admin' => TRUE,	
			));

			if(empty($isAdmin)){
				$cacheData = array(
					'named'		=> $this->request->params['named'], 
					'pass'		=> $this->request->params['pass'], 
					'query'		=> $this->request->query, 
					'result'	=> $value
				);

				Cache::write($cacheName, $cacheData, $cacheConfig);
			}
		}

		$_slug = $this->RmCommon->filterEmptyField($value, 'Advice', 'slug');
		if( $slug != $_slug ) {
			$this->redirect(array(
				'controller' => 'advices',
				'action' => 'read',
				$id,
				$_slug,
			));
		}

		if( !empty($value) ) {
			$category_id = $this->RmCommon->filterEmptyField($value, 'Advice', 'advice_category_id');
			$author_id = $this->RmCommon->filterEmptyField($value, 'Advice', 'author_id');
			$title = $this->RmCommon->filterEmptyField($value, 'Advice', 'title');
			$short_content = $this->RmCommon->filterEmptyField($value, 'Advice', 'short_content');
			$photo = $this->RmCommon->filterEmptyField($value, 'Advice', 'photo');
			$meta_title = $this->RmCommon->filterEmptyField($value, 'Advice', 'meta_title');
			$meta_description = $this->RmCommon->filterEmptyField($value, 'Advice', 'meta_description');

    		$value = $this->Advice->AdviceCategory->getMerge($value, $category_id);
			$value = $this->User->getMerge($value, $author_id);

			$related = $this->Advice->getDataRelated($id, $category_id);
			$adviceCategories = $this->Advice->AdviceCategory->getData('list');
			$module_title = $title;

			$dataView = $this->RmCommon->_callSaveVisitor($id, 'AdviceView', 'advice_id');
			$this->Advice->AdviceView->doSave($dataView);

			$this->set('title_for_layout', $title);
			$this->set('description_for_layout', $short_content);
			$this->set('keywords_for_layout', $title);

			if( !empty($photo) || ($meta_title || $meta_description) ) {
				$og_meta = array(
					'title' => $meta_title ?: $title,
					'description' => $meta_description ?: $short_content,
					'image' => $photo,
					'path' => Configure::read('__Site.advice_photo_folder'),
				);
			}

			$this->set('active_menu', 'advices');
			$this->set(compact(
				'value', 'og_meta', 'related',
				'module_title', 'adviceCategories'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_actived($id = false){
		$value = $this->Advice->getData('first', array(
			'conditions' => array(
				'Advice.id' => $id,
			),
		), array(
			'status' => 'status-active',
		));

		if(!empty($value)){
			$active = $this->RmCommon->filterEmptyField($value, 'Advice', 'active');
			$status = !empty($active) ? false : TRUE;
			$result = $this->Advice->doActived($value, $status);
			$this->RmCommon->setProcessParams($result);
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}
}
?>