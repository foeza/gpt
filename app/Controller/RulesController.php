<?php
App::uses('AppController', 'Controller');
class RulesController extends AppController {
	var $uses = array(
		'Rule'
	);
	public $components = array(
		'RmRule', 'RmImage', 'RmRecycleBin'
	);
	public $helpers = array(
		'Social',
	);

	function beforeFilter(){
		parent::beforeFilter();
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

	/* ==================================================
	================== S: Category Action ===============
	================================================== */
	function admin_category_rules(){
		$module_title = __('Kategori Rule');
        $options =  $this->Rule->RuleCategory->_callRefineParams($this->params, array(
        	'contain' => array(
        		'ParentRuleCategory' => array(
        			'conditions' => array(
        				'ParentRuleCategory.status' => true
        			),
        		),
        	),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));
		$this->RmCommon->_callRefineParams($this->params);

        $this->paginate = $this->Rule->RuleCategory->getData('paginate', $options);
		$values = $this->paginate('RuleCategory');

		$this->RmRule->callDataCategories();

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'active_menu' => 'list_category_rules',
			'_breadcrumb' => false,
		));
	}

	function admin_add_category_rules() {
		$save_path = Configure::read('__Site.general_folder');		
    	$module_title = __('Tambah Kategori Rule');
    	$urlRedirect  = array(
            'controller' 	=> 'rules',
            'action' 		=> 'category_rules',
            'admin' 		=> true
        );

		$data = $this->request->data;
		
		$category_name = Common::hashEmptyField($data, 'RuleCategory.name');

		$data = $this->RmImage->_uploadPhoto( $data, 'RuleCategory', 'photo', $save_path, true );
		if (!empty($category_name)) {
			$data['RuleCategory']['slug'] = $this->RmCommon->toSlug($category_name);
		}

		$result = $this->Rule->RuleCategory->doSave( $data );
		$this->RmCommon->setProcessParams($result, $urlRedirect);

		$this->RmRule->callDataCategories();

		$this->set('active_menu', 'list_category_rules');
		$this->set(compact(
			'module_title'
		));

	}

	public function admin_delete_multiple_category_rules() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Search', 'id');

    	$result = $this->Rule->RuleCategory->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    function backprocess_ajax_list_subcategories($root_id, $options = array()){
    	$call_back_data = Common::hashEmptyField($options, 'call_back_data', false);
    	$sub_categories = $this->Rule->RuleCategory->generateTableOfContent($root_id);

    	$this->set(array(
            'sub_categories' => $sub_categories
        ));
    	if (empty($call_back_data)) {
    		$this->render('/Elements/blocks/rules/list_subcategories');
    	}

	}

    public function admin_edit_category_rules( $category_id ) {
    	$save_path = Configure::read('__Site.general_folder');
        $module_title = __('Edit Kategori Rule');
        $urlRedirect  = array(
            'controller' 	=> 'rules',
            'action' 		=> 'category_rules',
            'admin' 		=> true
        );

        $rule_category = $this->Rule->RuleCategory->getData('first', array(
        	'conditions' => array(
				'RuleCategory.id' => $category_id,
			),
		));

		if( !empty($rule_category) ) {
			//	capture old photo, image file has to be deleted when new image file uploaded
			$photo = $rule_category['RuleCategory']['photo'];
			$oldPhoto = isset($photo) && $photo ? $photo : NULL;

			$data = $this->request->data;
			$category_name = Common::hashEmptyField($data, 'RuleCategory.name');

			$data = $this->RmImage->_uploadPhoto( $data, 'RuleCategory', 'photo', $save_path, true );
			// debug($data);die();
			if (!empty($category_name)) {
				$data['RuleCategory']['slug'] = $this->RmCommon->toSlug($category_name);
			}

			$result = $this->Rule->RuleCategory->doSave( $data, $rule_category, $category_id );

			//	if user upload new photo, delete old photo
			if(isset($this->request->data['RuleCategory']['photo']['name'])){
				$uploadPhoto = $this->request->data['RuleCategory']['photo']['name'];

				if($uploadPhoto && $oldPhoto && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->RmRule->callDataCategories();

			$this->set('active_menu', 'list_category_rules');
			$this->set(compact(
				'module_title'
			));
			$this->render('admin_add_category_rules');
		} else {
			$this->RmCommon->redirectReferer(__('Kategori tidak ditemukan'));
		}
    }
    /* ==================================================
	================== E: Category Action ===============
	================================================== */

	/* ==================================================
	============== S: Company Rules Action ==============
	================================================== */
	public function admin_company_rules() {
		$module_title = $title_for_layout = __('List Rule');
		$options =  $this->Rule->_callRefineParams($this->params, array(
			'contain' => array(
				'RuleCategory'
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		));

		$this->RmCommon->_callRefineParams($this->params);
        $this->paginate = $this->Rule->getData('paginate', $options, array(
        	'status' => 'all'
        ));
        // debug($this->paginate);
		$values = $this->paginate('Rule');

		foreach ($values as $key => &$value) {
			$rule_category_id = Common::hashEmptyField($value, 'Rule.rule_category_id');
			$value = $this->Rule->RuleCategory->getMergeParent($value, $rule_category_id);
		}

		$this->RmRule->callRootCategories();

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'title_for_layout' => $title_for_layout,
			'active_menu' => 'list_rules',
			'_breadcrumb' => false,
		));		
	}

	function admin_add_company_rules() {
    	$module_title = __('Tambah Rules');
    	$urlRedirect = array(
            'controller' 	=> 'rules',
            'action' 		=> 'company_rules',
            'admin' 		=> true
        );

		$data  = $this->request->data;

		$title = Common::hashEmptyField($data, 'Rule.name');
		if (!empty($title)) {
			$data['Rule']['slug'] = $this->RmCommon->toSlug($title);
		}

		$this->RmRule->checkDataCategoryBeforeSave($data);

		$result = $this->Rule->doSave( $data );
		$this->RmCommon->setProcessParams($result, $urlRedirect);
		$this->RmCommon->_layout_file(array(
			'ckeditor',
		));

		$this->RmRule->callRootCategories();

		$this->set('active_menu', 'list_rules');
		$this->set(compact(
			'module_title', 'title_for_layout'
		));

	}

	function admin_edit_company_rules( $rules_id = false ) {
        $module_title = __('Edit Rules');
        $urlRedirect = array(
            'controller' => 'rules',
            'action' => 'company_rules',
            'admin' => true
        );

        $rules = $this->Rule->getData('first', array(
        	'conditions' => array(
				'Rule.id' => $rules_id,
			),
		), array(
			'status' => 'all',
		));

		if( !empty($rules) ) {

			$data = $this->request->data;

			$title = Common::hashEmptyField($data, 'Rule.name');
			if (!empty($title)) {
				$data['Rule']['slug'] = $this->RmCommon->toSlug($title);
			}

			$this->RmRule->checkDataCategoryBeforeSave($rules);

			$result = $this->Rule->doSave( $data, $rules, $rules_id );

			$this->RmCommon->setProcessParams($result, $urlRedirect);
			$this->RmCommon->_layout_file('ckeditor');

			$this->RmRule->callRootCategories();

			$this->set('active_menu', 'list_rules');
			$this->set(compact('module_title'));

			$this->render('admin_add_company_rules');

		} else {
			$this->RmCommon->redirectReferer(__('Rule tidak ditemukan'));
		}
    }

    // read rules
    function admin_index(){

		$module_title = __('Daftar Isi Rule');
  //       $options =  $this->Rule->RuleCategory->_callRefineParams($this->params, array(
  //       	'conditions' => array(
  //       		'RuleCategory.parent_id' => null,
  //       	),
		// 	'limit' => Configure::read('__Site.config_new_table_pagination'),
		// ));
		// $this->RmCommon->_callRefineParams($this->params);

  //       $this->paginate = $this->Rule->RuleCategory->getData('paginate', $options);
		// $values = $this->paginate('RuleCategory');

    	$values = $this->Rule->RuleCategory->generateTableOfContent();
		$table_of_content = $this->RmRule->callDataTableRules($values, ' ', '---');

		$this->set(array(
			'values' => $table_of_content,
			'module_title' => $module_title,
			'active_menu' => 'daftar_isi_rules',
			'_breadcrumb' => false,
		));
	}

	function admin_read_rules(){
		$module_title = __('Read Rule');

		$this->RmCommon->_callRefineParams($this->params);
		$options =  $this->Rule->_callRefineParams($this->params, array(
			'contain' => array(
				'RuleCategory',
				'User',
			),
			'order' => array(
            	'Rule.modified' => 'desc',
        	),
			'limit' => 6,
		));

		$id_rule  	= Common::hashEmptyField($this->params->params, 'named.id_rule');
		$num_page 	= Common::hashEmptyField($this->params->params, 'named.page');

		// single content
		if (!empty($id_rule)) {
			$options['conditions'] = array(
				'Rule.id' => $id_rule,
			);
			$values = $this->Rule->getData('first', $options);
		} else {
	        $this->paginate = $this->Rule->getData('paginate', $options);
			$values = $this->paginate('Rule');
		}

		$this->RmRule->callRootCategories();

		$this->set(array(
			'values' 	   => $values,
			'module_title' => $module_title,
			'active_menu'  => 'rules_company',
			'_breadcrumb'  => false,
		));

		if( !empty($this->is_ajax) && !empty($num_page)) {
			$this->render('/Elements/blocks/rules/list_item');
		}

	}

    public function admin_delete_multiple_rules() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Rule', 'id');

    	$result = $this->Rule->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    function admin_actived($id = false){
		$value = $this->Rule->getData('first', array(
			'conditions' => array(
				'Rule.id' => $id,
			),
		), array(
			'status' => 'active-nonactive'
		));

		if(!empty($value)){
			$active = $this->RmCommon->filterEmptyField($value, 'Rule', 'active');
			$status = !empty($active) ? false : TRUE;
			$result = $this->Rule->doActived($value, $status);
			$this->RmCommon->setProcessParams($result);
		}else{
			$this->RmCommon->redirectReferer(__('Rule tidak ditemukan'));
		}
	}

	/* ==================================================
	============== E: Company Rules Action ==============
	================================================== */

}
?>