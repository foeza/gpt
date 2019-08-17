<?php
App::uses('AppController', 'Controller');

class ActivitiesController extends AppController {
	var $uses = array(
		'Activity', 'ExpertCategory',
	);
	public $helpers = array(
		'FileUpload.UploadForm', 'Expert',
	);
	public $components = array(
		'RmImage', 'RmRecycleBin', 'Captcha', 
		'RmActivity', 'RmReport'
	);

	function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow(array(
			'ranks',
		));
	}

	function admin_add(){
		$module_title = $title_for_layout = __('Tambah Aktivitas');

		$data = $this->request->data;

		if($data){
			$data = $this->RmActivity->doBeforeSave($data);
			$result = $this->Activity->doSave($data);

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'activities',
				'action' => 'index',
				'admin' => true,
			));
		}

		$this->RmActivity->doBeforeView();

		$this->set(array(
			'module_title' => $module_title,
			'title_for_layout' => $title_for_layout,
		));
	}

	function backprocess_get_action( $expert_category_id = null ){
		$render = $this->RmActivity->pick_expert_category($expert_category_id);
		$this->render($render);
	}

	function backprocess_get_type( $expert_category_id = null, $type = null ){
		$render = $this->RmActivity->pick_component_category($expert_category_id, $type);
		$this->render($render);
	}

	function backprocess_get_point_type( $expert_category_id = null, $activity_type = null, $expert_category_component_active_id = null ){
		$render = $this->RmActivity->pick_component($expert_category_id, $activity_type, $expert_category_component_active_id);
		$this->render($render);
	}

	function backprocess_get_point( $expert_category_id = null, $expert_category_component_active_id = null, $check_point_type = null ){
		$render = $this->RmActivity->pick_get_point($expert_category_id, $expert_category_component_active_id, $check_point_type);
		$this->render($render);
	}

	function backprocess_get_input_value( $expert_category_component_active_id = null ){
		$render = $this->RmActivity->pick_input_value($expert_category_component_active_id);
		$this->render($render);
	}

	function admin_index() {
		$module_title = $title_for_layout = __('Daftar Aktivitas');
		$params = $this->params->params;

		$options = $this->Activity->ActivityUser->_callRefineParams($params, array(
			'order' => array(
				'ActivityUser.action_date' => 'DESC',
				'ActivityUser.id' => 'DESC',
			),
		));

		$this->RmCommon->_callRefineParams($params);
		$this->paginate = $this->Activity->ActivityUser->getData('paginate', $options);
		$values = $this->paginate('ActivityUser');
		$values = $this->Activity->ActivityUser->getMergeList($values, array(
			'contain' => array(
				'ExpertCategory' => array(
					'elements' => array(
						'with_default' => true,
					),
				),
				'ExpertCategoryComponentActive' => array(
					'contain' => array(
						'ExpertCategoryComponent',
					),
					'elements' => array(
						'is_company' => false,
					),
				),
				'User' => array(
					'elements' => array(
						'status' => false,
					),
				), 
			), 
		));

		$this->set('active_menu', 'expert_activities');
		$this->set(compact(
			'values', 'module_title'
		));
	}
	
	function admin_edit( $id = null ) {
		$module_title = $title_for_layout = __('Edit Aktivitas');
		$data = $this->request->data;

		$value = $this->Activity->ActivityUser->getData('first', array(
			'conditions' => array(
				'ActivityUser.id' => $id,
			),
		), array(
			'allow' => 'edit',
		));

		if( !empty($value) ) {
			$value = $this->Activity->ActivityUser->getMergeList($value, array(
				'contain' => array(
					'User' => array(
						'elements' => array(
							'status' => false,
						),
					), 
				), 
			));
			$value = $this->Activity->ActivityUser->User->getMergeList($value, array(
				'contain' => array(
					'Group', 
					'UserConfig',
				), 
			));

			if( !empty($data) ){
				$activity_id = Common::hashEmptyField($value, 'Activity.id');
				$user_id = Common::hashEmptyField($value, 'ActivityUser.user_id');

				$data = $this->RmActivity->doBeforeSave($data, $activity_id, array(
					$user_id => $user_id,
				), $id);

				$data = Common::_callUnset($data, array(
					'Activity',
				));
				$result = $this->Activity->ActivityUser->doSave($data);

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'activities',
					'action' => 'index',
					'admin' => true,
				));
			} else {
				$value['Activity'] = Common::hashEmptyField($value, 'ActivityUser');
				$this->request->data = $value;
			}

			$this->RmActivity->doBeforeView();

			$this->set(array(
				'id' => $id,
				'value' => $value,
				'module_title' => $module_title,
				'title_for_layout' => $title_for_layout,
			));
			$this->render('admin_add');
		} else {
			$this->RmCommon->redirectReferer(__('Aktivitas tidak ditemukan'));
		}
	}
	
	function admin_detail( $id = null ) {
		$module_title = $title_for_layout = __('Info Aktivitas');
		$value = $this->Activity->ActivityUser->getData('first', array(
			'conditions' => array(
				'ActivityUser.id' => $id,
			),
		));

		if( !empty($value) ) {
			$value = $this->Activity->ActivityUser->getMergeList($value, array(
				'contain' => array(
					'User' => array(
						'elements' => array(
							'status' => false,
						),
					), 
					'ProcessBy' => array(
						'uses' => 'User',
						'type' => 'first',
						'primaryKey' => 'id',
						'foreignKey' => 'process_by',
						'elements' => array(
							'status' => 'all',
						),
					),
				), 
			));
			$value = $this->Activity->ActivityUser->User->getMergeList($value, array(
				'contain' => array(
					'Group', 
					'UserConfig',
				), 
			));

			$value['Activity'] = Common::hashEmptyField($value, 'ActivityUser');
			$this->request->data = $value;

			$this->RmActivity->doBeforeView();

			$this->set(array(
				'id' => $id,
				'value' => $value,
				'module_title' => $module_title,
				'title_for_layout' => $title_for_layout,
			));
			$this->render('admin_add');
		} else {
			$this->RmCommon->redirectReferer(__('Aktivitas tidak ditemukan'));
		}
	}
	
	function admin_delete( $id = null ) {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Search', 'id', $id);

    	$result = $this->Activity->ActivityUser->doDelete($id);
		
		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
			'redirectError' => true,
		));
	}
	
	function admin_approved( $id = null ) {
    	$result = $this->Activity->ActivityUser->doStatus($id, array(), __('menyetujui aktivitas'), 'approved');
		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
			'redirectError' => true,
		));
	}
	
	function admin_rejected( $id = null ) {
		$data = $this->request->data;

		if( !empty($data['ActivityUser']) ) {
	    	$result = $this->Activity->ActivityUser->doStatus($id, $data, __('menolak aktivitas'), 'rejected');
			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true,
				'ajaxRedirect' => true,
			));
		}

		$this->render('admin_rejected');
	}
	
	function admin_approves( $id = null ) {
		$data = $this->admin_approved($id);
	}
	
	function admin_rejects() {
		$data = $this->request->data;
		
		if( !empty($data['Search']['id']) ) {
			$id = $data['Search']['id'];
			$data = $this->admin_rejected( $id );
		} else {
			$this->RmCommon->modalMessage(__('Data tidak tersedia'));
		}
	}
	
	function admin_history( $user_id = null ) {
		$this->loadModel('ExpertCategory');

		$module_title = $title_for_layout = __('History PUS');
		$params = $this->params->params;
		$period_month = Common::hashEmptyField($params, 'named.period_month', date('mm'));
		$period_year = Common::hashEmptyField($params, 'named.period_year', date('Y'));
		$isAgent = Common::isAgent();
		$current_user_id = Configure::read('User.id');

		if( !empty($isAgent) && $current_user_id != $user_id ) {
			$this->redirect(array(
				'action' => 'history',
				$current_user_id,
			));
		} else {
			$user = $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $user_id,
				),
			), array(
				'company' => true,
			));

			if( !empty($user) ) {
				$principle_id = Configure::read('Principle.id');
				$user = $this->User->getMergeList($user, array(
					'contain' => array(
						'Group',
						'UserProfile',
					),
				));

				$this->ExpertCategory->unbindModel(
					array('hasMany' => array('ExpertCategoryActive'))
				);
				$this->ExpertCategory->bindModel(array(
		            'hasOne' => array(
		                'ExpertCategoryActive' => array(
		                    'className' => 'ExpertCategoryActive',
		                    'foreignKey' => 'expert_category_id',
		                    'conditions' => array(
					            'ExpertCategoryActive.actived' => true,
		                	),
		                ),
		            ),
		        ), false);

				$options =  $this->ExpertCategory->_callRefineParams($params, array(
					'conditions' => array(
						array(
							'OR' => array(
								array( 'ExpertCategoryActive.company_id' => $principle_id ),
								array( 'ExpertCategory.company_id' => 0 ),
							),
						),
						'ExpertCategoryActive.actived' => 1,
					),
					'contain' => array(
						'ExpertCategoryActive',
					),
					'group' => array(
						'ExpertCategory.id',
					),
		            'order' => array(
						'ExpertCategoryActive.order'=>'ASC',
						'ExpertCategory.name'=>'ASC',
						'ExpertCategory.company_id'=>'ASC',
						'ExpertCategory.parent_id'=>'ASC',
					),
				));
				$this->RmCommon->_callRefineParams($params);
				$values = $this->ExpertCategory->getData('all', $options, array(
					'status' => 'root',
					'with_default' => false,
					'company_id' => false,
				));

				if( !empty($values) ) {
					$this->ExpertCategory->ActivityCategoryPus->virtualFields['total_pus'] = 'SUM(ActivityCategoryPus.pus)';

					$total_agents = $this->User->getData('count', false, array(
						'status' => 'active',
						'company' => true,
						'role' => 'agent',
					));

					foreach ($values as $key => &$value) {
						$expert_category_id = Common::hashEmptyField($value, 'ExpertCategory.id');
						$value = $this->ExpertCategory->getMergeList($value, array(
							'contain' => array(
								'ActivityCategoryPus' => array(
									'type' => 'first',
									'conditions' => array(
										'ActivityCategoryPus.user_id' => $user_id,
									),
									'order' => array(
										'ActivityCategoryPus.id' => 'DESC',
										'ActivityCategoryPus.user_id',
									),
									'group' => array(
										'ActivityCategoryPus.user_id',
									),
								),
							),
						));
						
						$pus = $this->ExpertCategory->ActivityCategoryPus->getData('first', array(
							'conditions' => array(
								'ActivityCategoryPus.expert_category_id' => $expert_category_id,
							),
						));
						$value['ActivityCategoryPus']['total_pus'] = Common::hashEmptyField($pus, 'ActivityCategoryPus.total_pus');

						// $value = $this->ExpertCategory->_callExpertCategoryPus($value, $user_id);
					}
				}

				$months	= Configure::read('__Site.monthly.options');

				$this->set('active_menu', 'expert_pus');
				$this->set(compact(
					'values', 'module_title', 'title_for_layout',
					'months', 'period_year', 'period_month', 'user',
					'total_agents', 'total_pus'
				));
			}
		}
	}

	function admin_reset () {
		$this->loadModel('ActivityPus');
		$values = $this->User->getData('list', array(
			'fields' => array(
				'User.id',
				'User.parent_id',
			),
			'order' => array(
				'User.full_name' => 'ASC',
				'User.id' => 'ASC',
			),
			'group' => array(
				'User.id',
			),
		), array(
			'company' => true,
			'role' => 'agent',
		));

		if( !empty($values) ) {
			$principle_ids = array();
			$updatePusConditions = array();

			foreach ($values as $user_id => $principle_id) {
				$value = $this->User->UserCompany->getMerge(array(), $principle_id);
				$user_company_id = Common::hashEmptyField($value, 'UserCompany.id');

				$data[] = array(
					'ActivityPus' => array(
						'user_company_id' => $user_company_id,
						'principle_id' => $principle_id,
						'user_id' => $user_id,
						'periode_date' => date('Y-m-d'),
						'total_expert_category' => 0,
						'total_point' => 0,
						'total_pus' => 0,
						'pus' => 0,
						'is_reset' => true,
					),
				);

				$updatePusConditions['OR'][] = array(
					'ActivityPus.user_id' => $user_id,
				);
				$principle_ids[] = $principle_id;
			}
			
			$updatePusConditions['DATE_FORMAT(ActivityPus.periode_date, \'%Y-%m\')'] = date('Y-m');

			if($this->ActivityPus->saveAll($data, array(
				'validate' => 'only',
				'deep' => true,
			))) {
				$this->ActivityPus->updateAll(array(
					'ActivityPus.activity_status' => "'closed'",
					'ActivityPus.status' => 0,
				), $updatePusConditions);
				$this->Activity->ActivityUser->updateAll(array(
					'ActivityUser.activity_status' => "'closed'",
				), array(
					'ActivityUser.activity_status' => 'confirm',
					'ActivityUser.principle_id' => $principle_ids,
				));
				
				if( $this->ActivityPus->saveMany($data) ) {
					$this->RmCommon->redirectReferer(__('Berhasil me-reset PUS User'), 'success');
				} else {
					$this->RmCommon->redirectReferer(__('Gagal me-reset PUS User'));
				}
			} else {
				$this->RmCommon->redirectReferer(__('Gagal me-reset PUS User'));
			}
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}
	
	function admin_ranks() {
		$this->loadModel('ActivityPus');

		$module_title = $title_for_layout = __('PUS');
		$params = $this->params->params;
		$period_month = Common::hashEmptyField($params, 'named.period_month', date('n'));
		$period_year = Common::hashEmptyField($params, 'named.period_year', date('Y'));

		if( empty($params['named']['period_month']) ||empty($params['named']['period_year'])  ) {
		 	$params = Hash::insert($params, 'named.period_month', date('n'));
		 	$params = Hash::insert($params, 'named.period_year', date('Y'));
		}
		
    	$optionsPus = $this->ActivityPus->_callRefineParams($params, array(
            'conditions' => array(
            	'ActivityPus.user_id = User.id',
	            'ActivityPus.activity_status' => 'open',
            ),
    	));

		$this->ActivityPus->virtualFields['pus'] = 'IFNULL(ActivityPus.pus, 0)';
		$this->ActivityPus->virtualFields['rank_check'] = 'CASE WHEN ActivityPus.rank IS NULL THEN 1 ELSE 0 END';
		$this->ActivityPus->virtualFields['rank'] = 'IFNULL(ActivityPus.rank, 0)';
		$this->User->bindModel(array(
            'hasOne' => array(
                'ActivityPus' => array(
                    'className' => 'ActivityPus',
                    'foreignKey' => false,
                    'conditions' => Common::hashEmptyField($optionsPus, 'conditions', array()),
                ),
            )
        ), false);

		$options =  $this->User->_callRefineParams($params, array(
			'order' => array(
				'ActivityPus.rank_check' => 'ASC',
				'ActivityPus.rank' => 'ASC',
				'ActivityPus.total_point' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'group' => array(
				'User.id',
			),
			'contain' => array(
				'ActivityPus',
			),
		));
		$this->RmCommon->_callRefineParams($params);
		$values = $this->User->getData('all', $options, array(
			'status' => 'active',
			'company' => true,
			'role' => 'agent',
		));

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$value = $this->ActivityPus->getMergeList($value, array(
					'contain' => array(
						'LastActivityPus' => array(
							'uses' => 'ActivityPus',
							'type' => 'first',
							'foreignKey' => 'user_id',
							'primaryKey' => 'user_id',
							'conditions' => array(
								'ActivityPus.activity_status' => 'closed',
							),
							'order' => array(
								'ActivityPus.periode_date' => 'DESC',
								'ActivityPus.id' => 'DESC',
							),
						),
					),
				));
			}
		}

		$months	= Configure::read('__Site.monthly.options');

		$this->set(array(
			'active_menu' => 'expert_pus',
			'export' => array(
				array(
					'title' => __('Print'),
					'url' => 'javascript:void(0)',
					'icon' => 'rv4-print',
					'options' => array(
						'onclick' => 'window.print();', 
						'class' => 'btn default disinblock floright crumb-buton',
						'allow' => true,
					),
				),
				array(
					'title' => __('Export'),
					'url' => array(
						'controller' => 'reports',
						'action' => 'generate',
						'report_pus',
						'period_month' => $period_month,
						'period_year' => $period_year,
					),
					'icon' => 'rv4-download',
					'options' => array(
						'class' => 'btn green default disinblock floright crumb-buton',
					),
				),
				array(
					'title' => __('Reset PUS'),
					'url' => array(
						'controller' => 'activities',
						'action' => 'reset',
						'admin' => true,
					),
					'icon' => 'rv4-refresh',
					'alert' => __('Anda yakin ingin me-reset total PUS & Poin ?'),
				),
			),
		));
		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'title_for_layout' => $title_for_layout,
			'months' => $months,
			'period_year' => $period_year,
			'period_month' => $period_month,
			'_widget_help' => false,
			'_breadcrumb' => true,
		));
	}
	
	function admin_points() {
		$module_title = $title_for_layout = __('Laporan POIN Marketing');
		$params = $this->params->params;
		$period_month = Common::hashEmptyField($params, 'named.period_month', date('n'));
		$period_year = Common::hashEmptyField($params, 'named.period_year', date('Y'));

		if( empty($params['named']['period_month']) ||empty($params['named']['period_year'])  ) {
		 	$params = Hash::insert($params, 'named.period_month', $period_month);
		 	$params = Hash::insert($params, 'named.period_year', $period_year);
		}
		
    	$optionsPoint = $this->Activity->ActivityUser->_callRefineParams($params, array(
            'conditions' => array(
            	'ActivityUser.user_id = User.id',
            	'ActivityUser.principle_id = User.parent_id',
	            'ActivityUser.activity_status' => 'confirm',
            ),
    	));

		$this->Activity->ActivityUser->virtualFields['total_point'] = 'SUM(IFNULL(ActivityUser.point, 0))';
		$this->User->unbindModel(
			array('hasMany' => array('ActivityUser'))
		);
		$this->User->bindModel(array(
            'hasOne' => array(
                'ActivityUser' => array(
                    'className' => 'ActivityUser',
                    'foreignKey' => false,
                    'conditions' => Common::hashEmptyField($optionsPoint, 'conditions', array()),
                ),
            )
        ), false);

		$options =  $this->User->_callRefineParams($params, array(
			'order' => array(
				'User.full_name' => 'ASC',
				'ActivityUser.total_point' => 'DESC',
			),
			'group' => array(
				'User.id',
			),
			'contain' => array(
				'ActivityUser',
			),
		));
		$this->RmCommon->_callRefineParams($params);
		$values = $this->User->getData('all', $options, array(
			'status' => 'active',
			'company' => true,
			'role' => 'agent',
		));

		if( !empty($values) ) {

			foreach ($values as $key => &$value) {
				$user_id = Common::hashEmptyField($value, 'User.id');
				
				$optionsCategory = $this->Activity->ActivityUser->_callRefineParams($params, array(
					'fields' => array(
						'ActivityUser.expert_category_id',
						'ActivityUser.total_point',
					),
					'conditions' => array(
						'ActivityUser.user_id' => $user_id,
					),
					'group' => array(
						'ActivityUser.expert_category_id',
					),
				));
				$categories = $this->Activity->ActivityUser->getData('list', $optionsCategory, array(
					'status' => 'confirm',
				));

				$value['ExpertCategory'] = $categories;
			}
		}

		$months	= Configure::read('__Site.monthly.options');
		$categories = $this->ExpertCategory->getData('list', array(
			'fields' => array(
				'ExpertCategory.id',
				'ExpertCategory.name',
			),
            'order' => array(
				'ExpertCategory.name'=>'ASC',
				'ExpertCategory.company_id'=>'ASC',
				'ExpertCategory.created'=>'ASC',
				'ExpertCategory.parent_id'=>'ASC',
			),
			'group' => array(
				'ExpertCategory.id',
			),
		), array(
			'status' => 'root',
			'with_default' => true,
		));

		$this->RmCommon->_layout_file('freeze');
		$this->set(array(
			'categories' => $categories,
			'active_menu' => 'expert_point',
			'export' => array(
				array(
					'title' => __('Print'),
					'url' => 'javascript:void(0)',
					'icon' => 'rv4-print',
					'options' => array(
						'onclick' => 'window.print();', 
						'class' => 'btn default disinblock floright crumb-buton',
					),
				),
				array(
					'title' => __('Export'),
					'url' => array(
						'controller' => 'reports',
						'action' => 'generate',
						'report_point',
						'period_month' => $period_month,
						'period_year' => $period_year,
					),
					'icon' => 'rv4-download',
					'options' => array(
						'class' => 'btn green default disinblock floright crumb-buton',
					),
				),
			),
		));
		$this->set(array(
			'values' => $values,
			'module_title' => $module_title,
			'title_for_layout' => $title_for_layout,
			'months' => $months,
			'period_year' => $period_year,
			'period_month' => $period_month,
			'_widget_help' => false,
			'_breadcrumb' => true,
		));
	}
	
	function ranks() {
		$this->admin_ranks();
		$this->render('ranks');
	}
}
?>