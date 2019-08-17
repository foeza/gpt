<?php
App::uses('AppController', 'Controller');

class ExpertsController extends AppController {
	var $uses = array(
		'ExpertCategory', 'ExpertCategoryComponentActive'
	);

	public $helpers = array(
		'FileUpload.UploadForm', 'Expert',
	);


	public $apiRequestURL = 'http://ww.apiprimesystem.com/Api';

	public $components = array(
		'RmImage', 'RmRecycleBin', 'Captcha', 'RmExpert',
	);

	public function initialize(Controller $controller, $settings = array()) {
		$this->apiRequestURL = Common::hashEmptyField($webConfig, 'WebConfig.value', 'http://ww.apiprimesystem.com/Api');
	}

	function beforeFilter() {
		parent::beforeFilter();
	}

	function admin_info($recordID = false){
		$user = $this->RmUser->getUser($recordID);

		if($user){
			$this->loadModel('ApiSettingUser');

			$title = __('Token API');

			$data = $this->request->data;

			$value = $this->ApiSettingUser->getData('first', array(
				'conditions' => array(
					'ApiSettingUser.parent_id' => $recordID,
				),
			), array(
				'type' => 'master',
			));

			$result = $this->ApiSettingUser->doSave($data, $recordID, $value);
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'users',
				'action' => 'principles',
				'admin' => true
			));

			$this->set(array(
				'module_title' => $title,
				'title_for_layout' => $title,
				'currUser' => $user,
				'recordID' => $recordID,
				'active_tab' => 'Token',
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	function admin_category_delete(){
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'ExpertCategory', 'id');

		$result = $this->ExpertCategory->doToggle( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_category_add(){
		$module_title = $title_for_layout = __('Tambah Kategori');

		$data = $this->request->data;

		if($data){
			$data = $this->RmExpert->doBeforeSaveCategory($data);
			$result = $this->ExpertCategory->doSave($data);

			$document_id = Common::hashEmptyField($result, 'document_id');

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'experts',
				'action' => 'component_list',
				$document_id,
				'admin' => true,
			));
		}

		$parents = $this->ExpertCategory->getParent();

		$this->set(array(
			'parents' => $parents,
			'module_title' => $module_title,
			'title_for_layout' => $title_for_layout,
			'active_menu' => 'expert_categories'
		));
	}

	function admin_category_edit( $id = null ){
		$module_title = $title_for_layout = __('Edit Kategori');
		$value = $this->ExpertCategory->getData('first', array(
			'conditions' => array(
				'ExpertCategory.id' => $id,
			),
		), array(
			'with_default' => true,
		));

		if( !empty($value) ) {
			$data = $this->request->data;

			if($data){
				$data = $this->RmExpert->doBeforeSaveCategory($data, $id);
				$result = $this->ExpertCategory->ExpertCategoryActive->doSave($data, $value);

				$document_id = Common::hashEmptyField($result, 'document_id');

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'experts',
					'action' => 'component_list',
					$document_id,
					'admin' => true,
				));
			} else {
				$company_id = Configure::read('Principle.id');
				$value = $this->ExpertCategory->getMergeList($value, array(
					'contain' => array(
						'ExpertCategoryActive' => array(
							'type' => 'first',
							'conditions' => array(
								'ExpertCategoryActive.company_id' => $company_id,
							),
							'elements' => array(
								'status' => 'all',
							),
						),
					),
				));

				if( !empty($value['ExpertCategoryActive']['id']) ) {
					$valueTmp['ExpertCategory'] = array_merge($value['ExpertCategory'], $value['ExpertCategoryActive']);
				} else {
					$valueTmp = $value;
				}

				$this->request->data = $valueTmp;
			}

			$parents = $this->ExpertCategory->getParent();

			$this->set(array(
				'value' => $value,
				'parents' => $parents,
				'module_title' => $module_title,
				'title_for_layout' => $title_for_layout,
				'active_menu' => 'expert_categories'
			));
			$this->render('admin_category_add');
		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_categories(){
		$params = $this->params->params;

		$module_title = $title_for_layout = __('Kategori PUS');
		$company_id = Configure::read('Principle.id');

		$this->RmCommon->_callRefineParams($params);
		$options =  $this->ExpertCategory->_callRefineParams($params, array(
            'order' => array(
				'ExpertCategory.name'=>'ASC',
				'ExpertCategory.company_id'=>'ASC',
				'ExpertCategory.created'=>'ASC',
				'ExpertCategory.parent_id'=>'ASC',
			),
			'group' => array(
				'ExpertCategory.id',
			),
		));
		$this->paginate = $this->ExpertCategory->getData('paginate', $options, array(
			'with_default' => true,
		));
		$values = $this->paginate('ExpertCategory');

		$contain = array(
			'ExpertCategoryActive' => array(
				'type' => 'first',
				'conditions' => array(
					'ExpertCategoryActive.company_id' => $company_id,
				),
				'elements' => array(
					'status' => 'all',
				),
				'contain' => array(
					'ExpertCategoryCompany' => array(
						'type' => 'count',
					),
					'ParentExpertCategoryActive' => array(
						'uses' => 'ExpertCategory',
						'foreignKey' => 'parent_id',
						'primaryKey' => 'id',
						'elements' => array(
							'with_default' => true,
						),
					),
				),
			),
		);

		$values = $this->ExpertCategory->getMergeList($values, array(
			'contain' => array_merge(array(
				'ParentExpertCategory' => array(
					'uses' => 'ExpertCategory',
					'foreignKey' => 'parent_id',
					'primaryKey' => 'id',
					'elements' => array(
						'with_default' => true,
					),
					'contain' => $contain,
				),
			), $contain),
		));

		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'title_for_layout' => $title_for_layout,
			'active_menu' => 'expert_categories'
		));
	}

	function admin_actived($code, $slug){
		$value = $this->ExpertCategory->getData('first', array(
			'conditions' => array(
				'ExpertCategory.code' => $code,
			),
		), array(
			'with_default' => true,
		));

		if($value){
			$company_id = Configure::read('Principle.id');

			$value = $this->ExpertCategory->getMergeList($value, array(
				'contain' => array(
					'ExpertCategoryActive' => array(
						'type' => 'first',
						'conditions' => array(
							'ExpertCategoryActive.company_id' => $company_id,
						),
						'elements' => array(
							'status' => 'all',
						),
					),
				),
			));

			$result = $this->ExpertCategory->ExpertCategoryActive->doActived($value, $slug);
			$document_id = Common::hashEmptyField($result, 'document_id');
			$url = Common::hashEmptyField($result, 'url');

			$this->RmCommon->setProcessParams($result, $url);
		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_component_list($id = false){
		$category = $this->ExpertCategory->ExpertCategoryActive->getData('first', array(
			'conditions' => array(
				'ExpertCategoryActive.id' => $id,
			),
		), array(
			'is_company' => true,
		));

		$category = $this->ExpertCategory->ExpertCategoryActive->getMergeList($category, array(
			'contain' => array(
				'ExpertCategory' => array(
					'elements' => array(
						'with_default' => true,
					),
				),
			),
		));

		if(!empty($category['ExpertCategory'])){
			$params = $this->params->params;
			$categoryName = Common::hashEmptyField($category, 'ExpertCategory.name');
			$module_title = $title_for_layout = __('Komponen %s', $categoryName);

			$this->RmCommon->_callRefineParams($params);
			$options = $this->ExpertCategoryComponentActive->_callRefineParams($category, $params, array(
				'conditions' => array(
					'ExpertCategoryComponentActive.expert_category_active_id' => $id
				),
				'group' => array(
					'ExpertCategoryComponentActive.id',
				),
				'order' => array(
					'ExpertCategoryComponentActive.expert_category_component_id' => 'ASC',
				),
			));

			$this->paginate = $this->ExpertCategoryComponentActive->getData('paginate', $options);
			$values = $this->paginate('ExpertCategoryComponentActive');

			$values	= $this->ExpertCategoryComponentActive->getMergeList($values, array(
				'contain' => array(
					'ExpertCategoryComponent',
					'ExpertCategoryCompany' => array(
						'contain' => array(
							'Schema' => array(
								'type' => 'first',
								'uses' => 'ExpertCategoryCompanyDetail',
								'conditions' => array(
									'ExpertCategoryCompanyDetail.type' => 'schema',
								),
								'order' => array(
									'ExpertCategoryCompanyDetail.id',
								),
							),
							'ExpertCategoryCompanyDetail' => array(
								'conditions' => array(
									'ExpertCategoryCompanyDetail.type' => 'conditions',
								),
							),
						),
					),
				),
			));

			$this->set(array(
				'id' => $id,
				'category' => $category,
				'values' => $values,
				'module_title' => $module_title,
				'title_for_layout' => $title_for_layout,
				'active_menu' => 'expert_categories'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));	
		}
	}

	function admin_configure($id = false){
		$value = $this->ExpertCategory->ExpertCategoryActive->getData('first', array(
			'conditions' => array(
				'ExpertCategoryActive.id' => $id,
			),
		), array(
			'is_company' => true,
		));

		$value = $this->ExpertCategory->ExpertCategoryActive->getMergeList($value, array(
			'contain' => array(
				'ExpertCategory' => array(
					'elements' => array(
						'with_default' => true,
					),
					'contain' => array(
						'Schema' => array(
							'type' => 'first',
							'uses' => 'ExpertCategoryConfiguration',
							'elements' => array(
								'with_default' => true,
							),
							'conditions' => array(
								'type' => 'scheme',
							),
						),
						'Condition' => array(
							'uses' => 'ExpertCategoryConfiguration',
							'elements' => array(
								'with_default' => true,
							),
							'conditions' => array(
								'type' => 'conditions',
							),
						),
					),
				),
			),
		));

		if(!empty($value['ExpertCategory'])){
			$data = $this->request->data;

			if($data){
				$data = $this->RmExpert->doBeforeConfigure($data, $value);
				$result = $this->ExpertCategory->ExpertCategoryConfiguration->doSave($data, $value);

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'experts',
					'action' => 'component_list',
					$id,
					'admin' => true,
				));
			}

			$module_title = $title_for_layout = __('Konfigurasi Komponen');

			$this->RmExpert->doBeforeViewConfigue($value);

			$this->set(array(
				'value' => $value,
				'module_title' => $module_title,
				'title_for_layout' => $title_for_layout,
			));

		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_component_edit($active_id = false, $id = false){
		$value = $this->ExpertCategory->ExpertCategoryActive->getData('first', array(
			'conditions' => array(
				'ExpertCategoryActive.id' => $active_id,
			),
		), array(
			'is_company' => true,
		));

		$value = $this->ExpertCategory->ExpertCategoryActive->getMergeList($value, array(
			'contain' => array(
				'ExpertCategory' => array(
					'elements' => array(
						'with_default' => true,
					),
					'contain' => array(
						'Schema' => array(
							'type' => 'first',
							'uses' => 'ExpertCategoryConfiguration',
							'elements' => array(
								'with_default' => true,
							),
							'conditions' => array(
								'type' => 'scheme',
							),
						),
						'Condition' => array(
							'uses' => 'ExpertCategoryConfiguration',
							'elements' => array(
								'with_default' => true,
							),
							'conditions' => array(
								'type' => 'conditions',
							),
						),
					),
				),
			),
		));

		if(!empty($value['ExpertCategory'])){
			$component = $this->ExpertCategory->ExpertCategoryActive->ExpertCategoryComponentActive->getData('first', array(
				'conditions' => array(
					'ExpertCategoryComponentActive.id' => $id,
					'ExpertCategoryComponentActive.expert_category_active_id' => $active_id,
				),
			));

			$component = $this->ExpertCategory->ExpertCategoryActive->ExpertCategoryComponentActive->getMergeList($component, array(
				'contain' => array(
					'ExpertCategoryComponent',
					'ExpertCategoryCompany' => array(
						'contain' => array(
							'Schema' => array(
								'uses' => 'ExpertCategoryCompanyDetail',
								'conditions' => array(
									'type' => 'schema',
								),
							),
							'Condition' => array(
								'uses' => 'ExpertCategoryCompanyDetail',
								'conditions' => array(
									'type' => array( 'conditions', 'other', 'property_action', 'ebrosur_action' ),
								),
								'order' => array(
									'ExpertCategoryCompanyDetail.expert_category_company_id',
									'ExpertCategoryCompanyDetail.type',
									'ExpertCategoryCompanyDetail.slug',
									'ExpertCategoryCompanyDetail.value',
									'ExpertCategoryCompanyDetail.value_end',
								),
							),
						),
					),
				),
			));

			if($component){
				$module_title = $title_for_layout = __('Ubah rules komponen ini');

				$data = $this->request->data;

				if($data){
					$data = $this->RmExpert->doBeforeComponentDetail($data, $value, $component);
					$result = $this->ExpertCategory->ExpertCategoryComponent->doSave($data, $component, 'edit');

					$this->RmCommon->setProcessParams($result, array(
						'controller' => 'experts',
						'action' => 'component_list',
						$active_id,
						'admin' => true,
					));
					
					$this->RmExpert->doBeforeSaveViewComponent($data);
				} else {
					$this->RmExpert->doBeforeViewComponent($component);
				}

				$this->RmExpert->doBeforeViewConfigue($value);

				$this->set(array(
					'expert_category_value' => $value,
					'active_id' => $active_id,
					'value' => $value,
					'module_title' => $module_title,
					'title_for_layout' => $title_for_layout,
					'active_menu' => 'expert_categories',
				));

				$this->render('admin_component_add');
			} else {
				$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
			}
		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_component_add($id = false){
		$value = $this->ExpertCategory->ExpertCategoryActive->getData('first', array(
			'conditions' => array(
				'ExpertCategoryActive.id' => $id,
			),
		), array(
			'is_company' => true,
		));

		$value = $this->ExpertCategory->ExpertCategoryActive->getMergeList($value, array(
			'contain' => array(
				'ExpertCategory' => array(
					'elements' => array(
						'with_default' => true,
					),
					'contain' => array(
						'Schema' => array(
							'type' => 'first',
							'uses' => 'ExpertCategoryConfiguration',
							'elements' => array(
								'with_default' => true,
							),
							'conditions' => array(
								'type' => 'scheme',
							),
						),
						'Condition' => array(
							'uses' => 'ExpertCategoryConfiguration',
							'elements' => array(
								'with_default' => true,
							),
							'conditions' => array(
								'type' => 'conditions',
							),
						),
					),
				),
			),
		));

		if(!empty($value['ExpertCategory'])){
			$data = $this->request->data;

			$module_title = $title_for_layout = __('Tambahkan rules komponen ini');

			if($data){
				$data = $this->RmExpert->doBeforeComponentDetail($data, $value);
				$result = $this->ExpertCategory->ExpertCategoryComponent->doSave($data);

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'experts',
					'action' => 'component_list',
					$id,
					'admin' => true,
				));
			}

			$this->RmExpert->doBeforeViewConfigue($value);

			$this->set(array(
				'expert_category_value' => $value,
				'module_title' => $module_title,
				'title_for_layout' => $title_for_layout,
				'active_id' => $id,
				'active_menu' => 'expert_categories',
			));
			
		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function backprocess_element_conditions($slug = false, $type = false, $rel = false){
		$this->autoLayout = false;

		$propertyActions = $this->User->Property->PropertyAction->getData('list', array(
			'fields' => array(
				'PropertyAction.slug',
				'PropertyAction.name',
			),
            'cache' => __('PropertyAction.Slug'),
        ));

		$this->set(array(
			'slug' => $slug,
			'type' => $type,
			'rel' => $rel,
			'propertyActions' => $propertyActions,
		));
	}

	function admin_component_detail($id = false){
		$value = $this->ExpertCategory->ExpertCategoryActive->getData('first', array(
			'conditions' => array(
				'ExpertCategoryActive.id' => $id,
				'ExpertCategoryActive.actived' => true,
			),
		), array(
			'is_company' => true,
		));

		$value = $this->ExpertCategory->ExpertCategoryActive->getMergeList($value, array(
			'contain' => array(
				'ExpertCategory' => array(
					'elements' => array(
						'with_default' => true,
					),
				),
				'ExpertCategoryDetail' => array(
					'order' => array(
						'ExpertCategoryDetail.id' => 'ASC',
					),
				),
			),
		));

		if(!empty($value['ExpertCategory'])){
			$company_id = Common::hashEmptyField($value, 'ExpertCategoryActive.company_id');

			$name = Common::hashEmptyField($value, 'ExpertCategory.name');

			$module_title = $title_for_layout = __('Kategori %s', $name);

			$data = $this->request->data;

			if($data){
				$data = $this->RmExpert->doBeforeSave($data, array(
					'company_id' => $company_id,
					'value' => $value,
				));

				$result = $this->ExpertCategory->ExpertCategoryActive->ExpertCategoryDetail->doSave($data, $value);

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'experts',
					'action' => 'categories',
					'admin' => true,
				));
			} else if(!empty($value['ExpertCategoryDetail'])){
				$this->RmExpert->doBeforeView($value);
			}

			$this->set(array(
				'value' => $value,
				'module_title' => $module_title,
				'title_for_layout' => $title_for_layout,
				'active_menu' => 'expert_categories',
			));

		} else {
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_delete(){
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'ExpertCategoryActive', 'id');

		$result = $this->ExpertCategory->ExpertCategoryActive->ExpertCategoryComponentActive->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_component_toggle($id){
		$result = $this->ExpertCategory->ExpertCategoryActive->ExpertCategoryComponentActive->doToggle($id);
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}
}
?>