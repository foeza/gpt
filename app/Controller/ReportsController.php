<?php
class ReportsController extends AppController {
	public $components = array(
		'RmReport', 'RmProperty',
		'RmImage', 'RmKpr', 'RmCrm',
		'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
	            'admin_performance_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'paging', 'graphic', 'headers', 'data',
				 	),
			 	),
	            'admin_summary_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'paging', 'graphic', 'headers', 'data',
				 	),
			 	),
	            'admin_agent_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'paging', 'graphic', 'headers', 'data',
				 	),
			 	),
	            'admin_property_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'paging', 'graphic', 'headers', 'data',
				 	),
			 	),
	            'admin_visitor_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'paging', 'graphic', 'headers', 'data',
				 	),
			 	),
	            'admin_message_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'paging', 'graphic', 'headers', 'data',
				 	),
			 	),
	            'admin_commission_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'paging', 'graphic', 'headers', 'data',
				 	),
			 	),
	            'admin_kpr_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'paging', 'graphic', 'headers', 'data',
				 	),
			 	),
	            'admin_detail' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'paging', 'headers', 'data',
				 	),
			 	),
	            'admin_graphic' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'title', 'data',
				 	),
			 	),
    		),
    	),
	);
	public $uses = array(
		'Report',
	);
	public $helpers	= array(
		'Report',
	);
	
	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow(array(
			'accumulate_report', 'admin_detail',
			'admin_download', 'api_kpi_render', 'backprocess_export', 

			/*report allow*/
			'backprocess_kpi_activity', 'backprocess_kpi_crm',
			'backprocess_crm_category', 'backprocess_chart_share',
			'admin_share', 'admin_share_detail', 'admin_share_detail_module',

			/*report property*/
			'backprocess_selling', 'backprocess_ebrochures', 
			'backprocess_leads_property', 'backprocess_ratio_property',
			'backprocess_property_price', 'backprocess_listing_property', 			 
			'backprocess_top_search_area', 'backprocess_target_listing_property', 
			'backprocess_top_sold_property_area', 'admin_top_sold_property_area',

			/*report KPR*/
			'backprocess_kpr_organizer', 'backprocess_overview_kpr_filling',
			'backprocess_value_filling_kpr', 'backprocess_filling_area',
			'backprocess_status_kpr',

			/*report user*/
			'backprocess_top_agent', 'backprocess_client_section', 'api_top_agent',

			// Report client
			'backprocess_client_section',
			'backprocess_budget_client',
			'backprocess_payment_client',
			'backprocess_potential_clients',
			'admin_generate',
		));
		
		$this->limit = 30;
		$this->limit_paging = 30;
	}

	public function admin_search($action = 'index', $id = FALSE){
		$data	= $this->request->data;
		$params	= array(
			'action' => $action,
			$id,
		);

		$this->RmCommon->processSorting($params, $data);
	}

	function admin_search_params ( $action, $additional = false ) {
		$data = $this->request->data;
		$params = $this->params->params;

		$pass = Common::hashEmptyField($params, 'pass');

		$params = array(
			'action' => $action,
			'admin' => true,
		);

		if($pass){
			unset($pass[0]);
			$params = array_merge($params, $pass);
		}

		if(!empty($additional)){
			array_push($params, $additional);
		}

		$this->RmCommon->processSorting($params, $data);
	}

	function admin_search_report ( $action, $_admin = true, $addParam = false ) {
		$data = $this->request->data;

		$data = $this->RmReport->formatFilter($data);
		$param_search['Search'] = Common::hashEmptyField($data, 'Search');

		$named = $this->RmCommon->filterEmptyField($this->params, 'named');
		$params = array(
			'action' => $action,
			$addParam,
			'admin' => $_admin,
		);

		if( !empty($named) ) {
			$params = array_merge($params, $named);
		}
		
		$this->RmCommon->processSorting($params, $param_search);
	}

	function admin_performance() {
		$this->RmCommon->_callRefineParams($this->params);
		$options = $this->Report->_callRefineParams($this->params);
		$this->paginate	= $this->Report->getData('paginate', $options, array(
			'mine' => true,
			'role' => 'performance',
		));
		$values = $this->paginate('Report');

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$value = $this->Report->getMergeList($value, array(
					'contain' => array(
						'ReportDetail',
					),
				));
				$value = $this->RmReport->_callDetail($value);
			}
		}

		$title = __('Laporan Performa');
		$this->set(array(
			'values' => $values, 
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'laporan', 
			'urlAdd' => array(
            	'controller' => 'reports',
	            'action' => 'performance_add',
	            'admin' => true,
        	), 
		));
		$this->render('admin_index');
	}

	function admin_performance_add () {
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data);
			$dataSave = $this->RmReport->formatFilter($dataSave, array(
				'skip_datasearch' => true,
			));

			$dataReport = $this->RmReport->_callDataPerformance($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan Performa'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'UserCompanyConfig', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewPerformance();
		$this->RmReport->_callAddBeforeView(__('Laporan Performa - [%id%]'));

		$title = __('Buat Laporan Performa');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_performance', 
		));
	}

	function admin_client_add () {
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'clients');
			$dataReport = $this->RmReport->_callDataClients($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan Klien'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'UserClient', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			} else {
				$this->request->data = $data;
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewClient();
		$this->RmReport->_callAddBeforeView(__('Laporan Klien - [%id%]'));

		$title = __('Buat Laporan Klien');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_client', 
		));
	}

	function admin_agent_add () {
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'agents');
			$dataReport = $this->RmReport->_callDataAgents($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan Agen'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'User', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			} else {
				$this->request->data = $data;
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewAgent();
		$this->RmReport->_callAddBeforeView(__('Laporan Agen - [%id%]'));

		$title = __('Buat Laporan Agen');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_agent', 
		));
	}

	public function admin_summary() {
		$this->RmCommon->_callRefineParams($this->params);
		$options = $this->Report->_callRefineParams($this->params);
		$this->paginate	= $this->Report->getData('paginate', $options, array(
			'mine' => 'admin',
			'role' => 'summary',
		));
		$values = $this->paginate('Report');

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$value = $this->Report->getMergeList($value, array(
					'contain' => array(
						'ReportDetail',
					),
				));
				$value = $this->RmReport->_callDetail($value);
			}
		}

		$title = __('Laporan Summary');
		$this->set(array(
			'values' => $values, 
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'laporan', 
			'urlAdd' => array(
            	'controller' => 'reports',
	            'action' => 'summary_add',
	            'admin' => true,
        	), 
		));
		$this->render('admin_index');
	}

	function admin_summary_add () {
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'summary');
			$dataReport = $this->RmReport->_callDataSummary($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan Summary'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'UserCompany', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewSummary();
		$this->RmReport->_callAddBeforeView(__('Laporan Summary - [%id%]'));

		$title = __('Buat Laporan Summary');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_summary', 
		));
	}

	function admin_growth_behavior(){
		$this->loadModel('Log');
		$params = $this->params->params;
			
		$default_options = array(
			'group' => array(
				'model',
				'action',
			),
		);

		$this->Log->virtualFields['cnt'] = 'COUNT(Log.id)';
		$options = $this->Log->_callRefineParams($params, $default_options);
		$this->paginate = $this->Log->getData('paginate', $options);
		$values = $this->paginate('Log');
		debug($values);die();
	}

	function admin_detail( $id = false ) {
		$this->RmCommon->_callUserLogin();
   		$value = $this->Report->getData('first', array(
   			'conditions' => array(
   				'Report.id' => $id,
			),
		), array(
			'mine' => true,
		));

		if( !empty($value) ) {
			$value = $this->Report->getMergeList($value, array(
				'contain' => array(
					'ReportDetail',
				),
			));

			// start: init model table view (mau menggunakan freeze table atau new table)
			$report_type = Common::hashEmptyField($value, 'Report.report_type_id');
			$haystack_report_type = array( 'performance' );
			
			if ( in_array($report_type, $haystack_report_type) ) {
				$table_view = 'new_table';

			} else {
				$table_view = 'freeze_table';
			}
			// end: init model table view (mau menggunakan freeze table atau new table)

			$value['table_view'] = $table_view;

			$this->RmReport->_callDetailBeforeView($value);

			$title = $this->RmCommon->filterEmptyField($value, 'Report', 'title', __('Lihat Laporan'));
			$module_title = __('Lihat Laporan');

			$this->RmReport->activeMenu($value);
			$this->set(array(
				'title' => $title, 
				'module_title' => $module_title, 
				'title_for_layout' => $module_title, 
			));
			
			if( $this->Rest->isActive() ) {
    			App::uses('HtmlHelper', 'View/Helper');
           		$this->Html = new HtmlHelper(new View(null));

				$graphic = $this->Html->url(array(
					'controller' => 'reports',
					'action' => 'graphic',
					$id,
					'ext' => 'json',
					'admin' => true,
				));
			} else {
				$graphic = false;
			}

			$this->set('graphic', $graphic);
			$this->RmCommon->renderRest(array(
				'is_paging' => true,
				'params' => array(
					'controller' => 'reports',
					'action' => 'detail',
					$id,
					'admin' => true,
				),
			));
		} else {
			$this->RmCommon->redirectReferer();
		}
	}

	function admin_graphic( $id = false ) {
   		$value = $this->Report->getData('first', array(
   			'conditions' => array(
   				'Report.id' => $id,
			),
		), array(
			'mine' => true,
		));

		if( !empty($value) ) {
			$value = $this->Report->getMergeList($value, array(
				'contain' => array(
					'ReportDetail',
				),
			));
			$this->RmReport->_callGraphicBeforeView($value);

			$title = $this->RmCommon->filterEmptyField($value, 'Report', 'title', __('Lihat Laporan'));
			$module_title = __('Lihat Laporan');
			$this->set(array(
				'title' => $title, 
				'module_title' => $module_title, 
				'title_for_layout' => $module_title, 
				'active_menu' => 'laporan', 
			));
			$this->RmCommon->renderRest(array(
				'params' => array(
					'controller' => 'reports',
					'action' => 'detail',
					$id,
					'admin' => true,
				),
			));
		} else {
			$this->RmCommon->redirectReferer();
		}
	}

	function admin_download( $id = false ) {
		$this->RmCommon->_callUserLogin();
   		$value = $this->Report->getData('first', array(
   			'conditions' => array(
   				'Report.id' => $id,
			),
		), array(
			'mine' => true,
		));

		if( !empty($value) ) {
			$filename = $this->RmCommon->filterEmptyField($value, 'Report', 'filename');
			$basename = $this->RmCommon->filterEmptyField($value, 'Report', 'title');
			$path = Configure::read('__Site.report_folder');

			$filepath = $this->RmImage->_callGetFolderUploadPath($filename, $path);

			$this->set(compact(
				'filepath',
				'basename'
			));
			$this->layout = false;
			$this->render('/Elements/blocks/common/download');
		} else {
			$this->RmCommon->redirectReferer();
		}
	}

	function admin_delete ( $id = false ) {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'Report', 'id', $id);
    	$result = $this->Report->doDelete($id);
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
	}

	function admin_properties() {
		$this->RmCommon->_callRefineParams($this->params);
		$options = $this->Report->_callRefineParams($this->params);
		$this->paginate	= $this->Report->getData('paginate', $options, array(
			'mine' => true,
			'role' => 'properties',
		));
		$values = $this->paginate('Report');

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$value = $this->Report->getMergeList($value, array(
					'contain' => array(
						'ReportDetail',
					),
				));
				$value = $this->RmReport->_callDetail($value);
			}
		}

		$title = __('Laporan Properti');
		$this->set(array(
			'values' => $values, 
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'laporan', 
			'urlAdd' => array(
            	'controller' => 'reports',
	            'action' => 'property_add',
	            'admin' => true,
        	), 
		));
		$this->render('admin_index');
	}

	function admin_property_add () {
		$this->Property = $this->User->Property;
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'properties');
			$dataReport = $this->RmReport->_callDataProperties($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan Properti'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'Property', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewProperty();
		$this->RmReport->_callAddBeforeView(__('Laporan Properti Periode [%periode_date%]'));

		$title = __('Buat Laporan Properti');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_property', 
		));
	}

	function admin_visitors() {
		$this->RmCommon->_callRefineParams($this->params);
		$options = $this->Report->_callRefineParams($this->params);
		$this->paginate	= $this->Report->getData('paginate', $options, array(
			'mine' => true,
			'role' => 'visitors',
		));
		$values = $this->paginate('Report');

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$value = $this->Report->getMergeList($value, array(
					'contain' => array(
						'ReportDetail',
					),
				));
				$value = $this->RmReport->_callDetail($value);
			}
		}

		$title = __('Laporan Pengunjung');
		$this->set(array(
			'values' => $values, 
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'laporan', 
			'urlAdd' => array(
            	'controller' => 'reports',
	            'action' => 'visitor_add',
	            'admin' => true,
        	), 
		));
		$this->render('admin_index');
	}

	function admin_visitor_add () {
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'visitors');
			$dataReport = $this->RmReport->_callDataVisitors($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan Pengunjung'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'PropertyView', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewVisitor();
		$this->RmReport->_callAddBeforeView(__('Laporan Pengunjung Periode [%periode_date%]'));

		$title = __('Buat Laporan Pengunjung');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_visitor', 
		));
	}

	function admin_messages() {
		$this->RmCommon->_callRefineParams($this->params);
		$options = $this->Report->_callRefineParams($this->params);
		$this->paginate	= $this->Report->getData('paginate', $options, array(
			'mine' => true,
			'role' => 'messages',
		));
		$values = $this->paginate('Report');

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$value = $this->Report->getMergeList($value, array(
					'contain' => array(
						'ReportDetail',
					),
				));
				$value = $this->RmReport->_callDetail($value);
			}
		}

		$title = __('Laporan Pesan');
		$this->set(array(
			'values' => $values, 
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'laporan', 
			'urlAdd' => array(
            	'controller' => 'reports',
	            'action' => 'message_add',
	            'admin' => true,
        	), 
		));
		$this->render('admin_index');
	}

	function admin_message_add () {
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'messages');
			$dataReport = $this->RmReport->_callDataMessages($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan Pesan'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'Message', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewMessage();
		$this->RmReport->_callAddBeforeView(__('Laporan Pesan Periode [%periode_date%]'));

		$title = __('Buat Laporan Pesan');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_message', 
		));
	}

	function admin_kpr_add () {
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'kprs');
			$dataReport = $this->RmReport->_callDataKprs($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan KPR'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'KprBank', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewMessage();
		$this->RmReport->_callAddBeforeView(__('Laporan KPR Periode [%periode_date%]'));

		$title = __('Buat Laporan KPR');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_kpr', 
		));
	}

	function admin_commission_add () {
		$this->Property = $this->User->Property;
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'commissions');
			$dataReport = $this->RmReport->_callDataCommissions($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan Komisi'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'Property', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewProperty();
		$this->RmReport->_callAddBeforeView(__('Laporan Komisi Periode [%periode_date%]'));

		$title = __('Buat Laporan Komisi');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_commission', 
		));
	}

	function admin_generate ( $report_type = false ) {
		$this->RmCommon->_callUserLogin();
		
		$params = $this->params->params;
		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		if( !empty($params) ) {
			$date_from = $this->RmCommon->filterEmptyField($params, 'named', 'date_from', false, array(
				'date' => 'd/m/Y',
			));
			$date_to = $this->RmCommon->filterEmptyField($params, 'named', 'date_to', false, array(
				'date' => 'd/m/Y',
			));
			$status = $this->RmCommon->filterEmptyField($params, 'named', 'status');
			$title = $this->RmCommon->filterEmptyField($params, 'named', 'title');
			$sort = $this->RmCommon->filterEmptyField($params, 'named', 'sort');
			$direction = $this->RmCommon->filterEmptyField($params, 'named', 'direction', 'ASC');

			$periode = __('%s - %s', $date_from, $date_to);

			if( $periode == '- - -' ) {
				$title = __('Laporan %s', $title);
				$periode = false;
			} else {
				$title = __('Laporan %s [%periode_date%]', $title);
			}

			$data = array(
				'Search' => array(
					'title' => $title,
					'Periode' => array(
						'date' => $periode,
					),
				),
			);

			if( !empty($sort) ) {
				$data['Search']['Sort']['sort'] = __('%s-%s', $sort, $direction);
			}

			if( !empty($admin_rumahku) ) {
		        $companyData = Configure::read('Config.Company.data');
		        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

           		if( $group_id == 4 ) {
					$principle_id = $this->User->getAgents($this->parent_id, true, 'list', false, array(
						'role' => 'principle',
					));
					
					$data['Search']['Perusahaan']['principle_id'] = $principle_id;
				} else {
					$data['Search']['Perusahaan']['principle_id'][] = $this->parent_id;
				}
			}

			switch ($report_type) {
				case 'commissions':
					$property_action = $this->RmCommon->filterEmptyField($params, 'named', 'property_action');

					$data['Search']['Jenis']['property_action'] = $property_action;

					$dataSave = $this->RmReport->_callAddBeforeSave($data, $report_type);
					$dataReport = $this->RmReport->_callDataCommissions($dataSave, false, $this->limit);
					$modelName = 'Property';
					break;

				case 'properties':
					$sold_date_from = $this->RmCommon->filterEmptyField($params, 'named', 'sold_date_from', false, array(
						'date' => 'd/m/Y',
					));
					$sold_date_to = $this->RmCommon->filterEmptyField($params, 'named', 'sold_date_to', false, array(
						'date' => 'd/m/Y',
					));
					$region 	= Common::hashEmptyField($params, 'named.region');
					$city 		= Common::hashEmptyField($params, 'named.city');
					$subareas 	= Common::hashEmptyField($params, 'named.subareas');

					if(!empty($region)){
						$data['Search']['Provinsi']['region'] = $region;
					}
					if(!empty($city)){
						$data['Search']['Kota']['city'] = $city;
					}
					if(!empty($subareas)){
						$data['Search']['Area']['subareas'][$subareas] = 1;
					}

					if(!empty($status)){
						$data['Search']['Status']['status'][$status] = true;
					}

					if(!empty($params['named']['sold_date_from']) && !empty($params['named']['sold_date_to'])){
						$periode_sold = __('%s - %s', $sold_date_from, $sold_date_to);
						$data['Search']['Tgl_Terjual']['sold_date'] = $periode_sold;
					}

					$dataSave = $this->RmReport->_callAddBeforeSave($data, $report_type);
					$dataReport = $this->RmReport->_callDataProperties($dataSave, false, $this->limit);
					$modelName = 'Property';
					break;

				case 'visitors':
					$dataSave = $this->RmReport->_callAddBeforeSave($data, $report_type);
					$dataReport = $this->RmReport->_callDataVisitors($dataSave, false, $this->limit);
					$modelName = 'Property';
					break;

				case 'agents':
					$dataSave = $this->RmReport->_callAddBeforeSave($data, $report_type);
					$dataReport = $this->RmReport->_callDataAgents($dataSave, false, $this->limit);
					$modelName = 'User';
					break;

				case 'clients':
					$data['Search']['title'] = __('Laporan Klien');
					$params = Common::hashEmptyField($params, 'named');
					$is_agent = Common::isAgent();

					if( !empty($params) ) {
						foreach ($params as $key => $param) {
							$data['Search'][ucwords($key)] = array(
								$key => $param,
							);
						}
					}

					$dataSave = $this->RmReport->_callAddBeforeSave($data, $report_type);
					$dataReport = $this->RmReport->_callDataClients($dataSave, false, $this->limit);

					if( !empty($is_agent) ) {
						$modelName = 'UserClientRelation';
					} else {
						$modelName = 'UserClient';
					}
					break;

				case 'report_pus':
					$period_month = Common::hashEmptyField($params, 'named.period_month');
					$period_year = Common::hashEmptyField($params, 'named.period_year');

					$data['Search']['title'] = __('Laporan PUS Marketing - Periode %s %s', date('F', strtotime(__('%s/%s/01', $period_year, $period_month))), $period_year);
					$data['Search']['params']['named'] = Common::hashEmptyField($params, 'named');

					$dataSave = $this->RmReport->_callAddBeforeSave($data, $report_type);
					$dataReport = $this->RmReport->_callDataReport_pus($dataSave, false, $this->limit);
					$modelName = 'ActivityPus';
					break;

				case 'report_point':
					$period_month = Common::hashEmptyField($params, 'named.period_month');
					$period_year = Common::hashEmptyField($params, 'named.period_year');

					$data['Search']['title'] = __('Laporan POIN Marketing - Periode %s %s', date('F', strtotime(__('%s/%s/01', $period_year, $period_month))), $period_year);
					$data['Search']['params']['named'] = Common::hashEmptyField($params, 'named');

					$dataSave = $this->RmReport->_callAddBeforeSave($data, $report_type);
					$dataReport = $this->RmReport->_callDataReport_point($dataSave, false, $this->limit);
					$modelName = 'ActivityUser';
					break;
				
				default:
					$this->RmCommon->redirectReferer();
					break;
			}

			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan Properti'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( $modelName, $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->RmCommon->setProcessParams($result, array(
				'action' => 'detail',
				$id,
				'sort' => $sort,
				'direction' => $direction,
				'admin' => true,
			), array(
				'redirectError' => true,
			));
		} else {
			$this->RmCommon->redirectReferer();
		}
	}

	function accumulate_report () {
		$effectiveDate = '2016-01';
		$flag = true;
		$result = array();
		
		$this->User->Property->bindModel(array(
            'hasOne' => array(
                'UserCompanyConfig' => array(
                    'className' => 'UserCompanyConfig',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'UserCompanyConfig.user_id = User.parent_id',
                	),
                ),
            )
        ), false);

		$this->User->Property->virtualFields['cnt'] = 'COUNT(Property.id)';
		// $this->User->Log->virtualFields['cnt'] = 'COUNT(Log.id)';

		while ($flag) {
			$value = $this->User->Property->find('first', array(
				'contain' => array(
					'User',
					'UserCompanyConfig',
				),
				'conditions' => array(
					'UserCompanyConfig.id <>' => NULL,
					'DATE_FORMAT(CASE WHEN Property.publish_date IS NULL THEN Property.created WHEN Property.publish_date = \'0000-00-00 00:00:00\' THEN Property.created ELSE Property.publish_date END, \'%Y-%m\') <=' => $effectiveDate,
				),
			));
			// $inactive = $this->User->Log->find('first', array(
			// 	'conditions' => array(
			// 		'Log.name LIKE' => '%meng-nonaktifkan%',
			// 		'DATE_FORMAT(Log.created, \'%Y-%m\') <=' => $effectiveDate,
			// 		'DATE_FORMAT(Log.created, \'%Y-%m\') <>' => '0000-00-00 00:00:00',
			// 	),
			// ));
			// $deleted = $this->User->Log->find('first', array(
			// 	'conditions' => array(
			// 		'Log.name LIKE' => '%menghapus properti%',
			// 		'DATE_FORMAT(Log.created, \'%Y-%m\') <=' => $effectiveDate,
			// 		'DATE_FORMAT(Log.created, \'%Y-%m\') <>' => '0000-00-00 00:00:00',
			// 	),
			// ));
			
			$result[$effectiveDate]['published'] = Common::hashEmptyField($value, 'Property.cnt', 0);
			// $result[$effectiveDate]['inactive'] = Common::hashEmptyField($inactive, 'Log.cnt', 0);
			// $result[$effectiveDate]['deleted'] = Common::hashEmptyField($deleted, 'Log.cnt', 0);

			$effectiveDate = strtotime("+1 months", strtotime($effectiveDate));
			$effectiveDate = date('Y-m', $effectiveDate);

			if( $effectiveDate == '2017-04' ) {
				$flag = false;
			}
		}
		
		// $this->User->Property->unbindModel(
		// 	array('hasMany' => array('PropertySold'))
		// );
		// $this->User->Property->bindModel(array(
  //           'hasOne' => array(
  //               'PropertySold' => array(
  //                   'className' => 'PropertySold',
  //                   'foreignKey' => 'property_id',
  //               ),
  //           )
  //       ), false);

		// $this->User->Property->PropertySold->virtualFields['total_price_sold'] = 'SUM(PropertySold.price_sold)';
		
		// $effectiveDate = '2017-01';
		// $flag = true;

		// while ($flag) {
		// 	$sold = $this->User->Property->find('first', array(
		// 		'contain' => array(
		// 			'User',
		// 			'UserCompanyConfig',
		// 			'PropertySold',
		// 		),
		// 		'conditions' => array(
		// 			'UserCompanyConfig.id <>' => NULL,
		// 			'PropertySold.status' => 1,
		// 			'Property.sold' => 1,
		// 			'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m\') <=' => $effectiveDate,
		// 			'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m\') <>' => '1998-05',
		// 			array(
		// 				array( 'PropertySold.sold_date <>' => '0000-00-00 00:00:00' ),
		// 				array( 'PropertySold.sold_date <>' => NULL ),
		// 			),
		// 		),
		// 	));
			
		// 	$result[$effectiveDate]['sold']['cnt'] = Common::hashEmptyField($sold, 'Property.cnt', 0);
		// 	$result[$effectiveDate]['sold']['price'] = Common::hashEmptyField($sold, 'PropertySold.total_price_sold', 0);

		// 	$effectiveDate = strtotime("+1 months", strtotime($effectiveDate));
		// 	$effectiveDate = date('Y-m', $effectiveDate);

		// 	if( $effectiveDate == '2017-04' ) {
		// 		$flag = false;
		// 	}
		// }

		// while ($flag) {
		// 	$deleted = $this->User->Property->find('first', array(
		// 		'contain' => array(
		// 			'User',
		// 			'UserCompanyConfig',
		// 		),
		// 		'conditions' => array(
		// 			'UserCompanyConfig.id <>' => NULL,
		// 			'Property.deleted' => 1,
		// 			'DATE_FORMAT(Property.modified, \'%Y-%m\') <=' => $effectiveDate,
		// 			'DATE_FORMAT(Property.modified, \'%Y-%m\') <>' => '0000-00-00 00:00:00',
		// 		),
		// 	));
			
		// 	$result[$effectiveDate]['deleted'] = Common::hashEmptyField($deleted, 'Property.cnt', 0);

		// 	$effectiveDate = strtotime("+1 months", strtotime($effectiveDate));
		// 	$effectiveDate = date('Y-m', $effectiveDate);

		// 	if( $effectiveDate == '2017-04' ) {
		// 		$flag = false;
		// 	}
		// }
			debug($result);die();
	}

	function admin_growth_add () {
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'growth');
			$dataSave = $this->RmReport->formatFilter($dataSave, array(
				'skip_datasearch' => true,
			));
			// debug($dataSave);
			$dataReport = $this->RmReport->_callDataGrowth($dataSave, false, $this->limit);
			// debug($dataReport);die();
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Growth Prime for Agent'));
	
			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'UserCompanyConfig', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeView(__('Growth Prime for Agent - [%id%]'));
		$this->RmReport->_callAddBeforeViewGrowth();

		$title = __('Growth Prime for Agent');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_growth',
		));
	}

	function admin_user_add  () {
		$data = $this->request->data;

		if( !empty($data) ) {
			$dataSave = $this->RmReport->_callAddBeforeSave($data, 'users');
			$dataReport = $this->RmReport->_callDataUsers($dataSave, false, $this->limit);
			$result = $this->Report->doSave($dataSave, $dataReport);

			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$status = $this->RmCommon->filterEmptyField($result, 'status');
			$title = $this->RmCommon->filterEmptyField($result, 'title', false, __('Laporan User'));

			if( $status == 'success' ) {
				$resultReport = $this->RmReport->_callProcess( 'User', $id, $dataSave, $dataReport );
				$this->RmReport->_callSaveDataExport( $title, $dataSave, $dataReport, $resultReport );
			} else {
				$this->request->data = $data;
			}

			$this->RmReport->_callProcessDetail($result, $id);
		}

		$this->RmReport->_callAddBeforeViewUser();
		$this->RmReport->_callAddBeforeView(__('Laporan User - [%id%]'));


		$title = __('Buat Laporan User');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_user', 
		));
	}

	function admin_crm(){
		$this->loadModel('CrmProjectActivity');

		$params = $this->params->params;
		$this->RmCommon->_callRefineParams($params);

		$this->CrmProjectActivity->unbindModel(array(
			'hasMany' => array(
				'CrmProjectActivityAttributeOption', 
			), 
		));

		$this->CrmProjectActivity->bindModel(array(
			'hasOne' => array(
				'CrmProjectActivityAttributeOption' => array(
					'foreignKey' => 'crm_project_activity_id', 
				), 
			), 
		), false);


		$this->CrmProjectActivity->virtualFields['cnt'] = 'COUNT(CrmProjectActivity.id)';
		$options =  $this->CrmProjectActivity->_callRefineParams($params, array(
			'conditions' => array(
				'CrmProjectActivityAttributeOption.attribute_option_id NOT' => NULL,
				'CrmProjectActivityAttributeOption.attribute_option_id <=' => Configure::read('__Site.Global.Variable.CRM.AttributeOpton.limit_id'),
			),
			'contain' => array(
				'CrmProjectActivityAttributeOption',
			),
			'order' => array(
				'CrmProjectActivity.activity_date' => 'DESC',
			),
			'group' => array(
				'CrmProjectActivityAttributeOption.attribute_option_id',
				'CrmProjectActivity.activity_date',
			),
			'limit' => 10,
		));
		$elements = array(
			'status' => 'active',
			'mine' => true,
			'company' => true,
		);
		$this->paginate = $this->CrmProjectActivity->getData('paginate', $options, $elements);
		$activities = $this->paginate('CrmProjectActivity');
		$activities = $this->CrmProjectActivity->CrmProjectActivityAttributeOption->getMergeList($activities, array(
			'contain' => array(
				'AttributeOption',
			),
		));
		$attributeOptions = $this->CrmProjectActivity->AttributeOption->getData('list', false, array(
			'parent' => true,
		));
		$this->RmCommon->_layout_file('report');

		$title = __('Laporan CRM');
		$this->set(array(
			'activities' => $activities, 
			'attributeOptions' => $attributeOptions, 
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'report_crm', 
		));
	}

	function backprocess_crm_category(){
		$this->loadModel('CrmProjectActivity');
		
		$resultRows = array();
		$params = $this->params->params;
		$data = $this->request->data;
		
		if( !empty($data) ) {
			$date = Common::hashEmptyField($data, 'Search.date');
			$params['named'] = Common::_callConvertDateRange(array(), $date, array(
				'date_from' => 'date_from',
				'date_to' => 'date_to',
			));
		}

		$period = Common::hashEmptyField($params, 'named.period');
		$autoload = Common::hashEmptyField($params, 'named.autoload');
		$page = Common::hashEmptyField($params, 'named.page');
		$period_date = Common::_callPeriodeDate($period);
		
		$date_from = Common::hashEmptyField($params, 'named.date_from', Common::hashEmptyField($period_date, 'periode_date_from'));
		$date_to = Common::hashEmptyField($params, 'named.date_to', Common::hashEmptyField($period_date, 'periode_date_to'));

		$param_custom = array(
			'named' => array(
				'date_from' => $date_from,
				'date_to' => $date_to,
			),
		);

		$this->CrmProjectActivity->unbindModel(array(
			'hasMany' => array(
				'CrmProjectActivityAttributeOption', 
			), 
		));

		$this->CrmProjectActivity->bindModel(array(
			'hasOne' => array(
				'CrmProjectActivityAttributeOption' => array(
					'foreignKey' => 'crm_project_activity_id', 
				), 
			), 
		), false);

		$this->CrmProjectActivity->virtualFields['cnt'] = 'COUNT(CrmProjectActivity.id)';
		
		$this->RmCommon->_callRefineParams($params);
		$options =  $this->CrmProjectActivity->_callRefineParams($param_custom, array(
			'contain' => array(
				'CrmProjectActivityAttributeOption',
			),
			'order' => array(
				'CrmProjectActivity.activity_date' => 'ASC',
			),
		));
		$elements = array(
			'status' => 'active',
			'mine' => true,
			'company' => true,
		);
		$options = $this->CrmProjectActivity->getData('paginate', $options, $elements);

		$data_count = array();
		$attributeOptions = $this->CrmProjectActivity->AttributeOption->getData('list', false, array(
			'parent' => true,
		));

		if( !empty($attributeOptions) ) {
			foreach ($attributeOptions as $attribute_option_id => $val) {
				$options['conditions']['CrmProjectActivityAttributeOption.attribute_option_id'] = $attribute_option_id;

				$this->paginate = array_merge($options, array(
					'group' => array(
						'CrmProjectActivity.activity_date',
					),
					'limit' => 10,
				));
				$activities = $this->paginate('CrmProjectActivity');

				$activity = $this->CrmProjectActivity->getData('first', $options, $elements);
				$activity_count = Common::hashEmptyField($activity, 'CrmProjectActivity.cnt', 0);
				$data_count[$attribute_option_id] = $activity_count;
				$date = false;

				if( !empty($activities) ) {
					foreach ($activities as $key => $value) {
						$date = Common::hashEmptyField($value, 'CrmProjectActivity.activity_date', null, array(
							'date' => 'Y-m-d',
						));
						$cnt = Common::hashEmptyField($value, 'CrmProjectActivity.cnt', 0);

						$resultRows[$date][0] = $date;
						$resultRows[$date][$attribute_option_id] = $cnt;
				
						foreach ($attributeOptions as $val_id => $val) {
							if( empty($resultRows[$date][$val_id]) ) {
								$resultRows[$date][$val_id] = 0;
							}
						}
					}
				}
			}
		}

		if( !empty($resultRows) || $page == 1 ) {
			$result = array(
				'period' => $period,
				'currentPeriod' => Common::hashEmptyField($period_date, 'title'),
				'activities' => $resultRows,
			);

			if( !empty($data_count) ) {
				foreach ($data_count as $attribute_option_id => $cnt) {
					$result[$attribute_option_id.'_count'] = $cnt;
				}
			}

			if( !empty($resultRows) ) {
				ksort($resultRows);

				$result['cols'] = array(
					array(
						'label' => __('Tanggal'), 
						'type' => 'string', 
					),
				);

				if( !empty($attributeOptions) ) {
					foreach ($attributeOptions as $attribute_option_id => $attribute_option) {
						$result['cols'][] = array(
							'label' => $attribute_option, 
							'type' => 'number', 
						);
					}
				}

				foreach ($resultRows as $key => $value) {
					$data = array(
						array( 'v' => Common::formatDate($value[0], 'd/m/Y') ),
					);

					unset($value[0]);

					foreach ($value as $key => $val) {
						$data[] = array(
							'v' => $val,
						);
					}

					$result['rows'][] = array(
						'c' => $data,
					);
				}
			}
		} else {
			$result = null;
		}

		$this->autoRender = false;

		if( !empty($result) ) {
			return json_encode($result);
		} else {
			return null;
		}
	}

	function admin_crm_category( $value = null ){
		$this->loadModel('AttributeOption');

		if( empty($value) ) {
			$params = $this->params->params;
			$data = $this->request->data;

			$json = Common::hashEmptyField($data, 'json');
			$value = json_decode($json, true);
		}

		$periode_date_to = date('Y-m-t');
		$periode_date_from = date ("Y-m-01", strtotime('-1 month', strtotime($periode_date_to)));
		$attributeOptions = $this->AttributeOption->getData('all', false, array(
			'parent' => true,
		));
		
		$currentPeriod = Common::hashEmptyField($value, 'currentPeriod', Common::getCombineDate($periode_date_from, $periode_date_to));
		$period = Common::hashEmptyField($value, 'period');
		$activities = Common::hashEmptyField($value, 'activities');

		if( !empty($attributeOptions) ) {
			foreach ($attributeOptions as $key => $val) {
				$id = Common::hashEmptyField($val, 'AttributeOption.id');
				$attr_name = Common::hashEmptyField($val, 'AttributeOption.name');
				$slug = Common::toSlug($attr_name, false, '_');

				$cnt = Common::hashEmptyField($value, $id.'_count', 0);
				$label_var = $slug.'_count';

				$this->set($label_var, $cnt);
				$value = Common::_callUnset($value, array(
					$id.'_count',
				));
			}
		}

		$this->request->data['Search']['period'] = $period;
		$value = Common::_callUnset($value, array(
			'currentPeriod',
			'period',
		));

		$this->set(array(
			'autoload' => true,
			'activities' => $activities,
			'currentPeriod' => $currentPeriod,
			'attributeOptions' => $attributeOptions,
		));

		$this->render('/Elements/blocks/reports/charts/crm');
	}

	function admin_kpi_marketing(){
		$this->RmCommon->_layout_file('report');

		$split_window = $this->RmCommon->splitWindow();

		$title = __('KPI Marketing');
		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'kpi_marketing',
			'split_window' => $split_window
		));
	}

	function api_kpi_render( $type = null, $value = null ){
		$this->RmReport->_callBeforeViewJson($value, '/Elements/blocks/reports/dashboard/charts/'.$type);
	}

	function backprocess_kpi_crm(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('CrmProject');
			
			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;

			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			
			if( $page == 1 ) {
				$param_custom = array(
					'named' => array(
						'date_from' => $date_from,
						'date_to' => $date_to,
					),
				);

				$attributeSet = $this->CrmProject->AttributeSet->getData('all', false, array(
					'show' => true,
				));

				if( !empty($attributeSet) ) {
					$this->CrmProject->virtualFields['cnt'] = 'COUNT(CrmProject.id)';
					$this->RmCommon->_callRefineParams($params);
					
					foreach ($attributeSet as $key => &$value) {
						$attribute_set_id = Common::hashEmptyField($value, 'AttributeSet.id');
						$reference_id = Common::hashEmptyField($value, 'AttributeSet.reference_id');

						if( !empty($reference_id) ) {
							$attribute_set_id = array(
								$attribute_set_id,
								$reference_id,
							);
						}

						$options =  $this->CrmProject->_callRefineParams($param_custom, array(
							'conditions' => array(
								'CrmProject.attribute_set_id' => $attribute_set_id,
							),
							'group' => array(
								'CrmProject.attribute_set_id',
							),
						));
						$crm = $this->CrmProject->getData('first', $options, array(
							'status' => 'active',
						));

						$attribute = Common::hashEmptyField($value, 'AttributeSet.name');
						$cnt = Common::hashEmptyField($crm, 'CrmProject.cnt', 0);

						$value['CrmProject']['cnt'] = $cnt;

						$resultRows[$attribute][0] = $attribute;
						$resultRows[$attribute][1] = $cnt;
					}
				}

				if( !empty($resultRows) || $page == 1 ) {
					$result = array(
						'date_from' => $date_from,
						'date_to' => $date_to,
						'activities' => $attributeSet,
						'year' => $year
					);

					if( !empty($resultRows) ) {
						$result['cols'] = array(
							array(
								'label' => __('Status'), 
								'type' => 'string', 
							),
							array(
								'label' => __('Leads'), 
								'type' => 'number', 
							),
						);

						foreach ($resultRows as $key => $val) {
							$result['rows'][] = array(
								'c' => array(
									array( 'v' => $val[0] ),
									array( 'v' => $val[1] ),
								),
							);
						}
					}
				} else {
					$result = null;
				}
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_kpi_activity(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('CrmProjectActivity');
			
			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;

			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');
			$date_diff_month = Common::hashEmptyField($data, 'Search.periode_id');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			
			if( $page == 1 ) {
				$date_from = Common::hashEmptyField($params, 'named.date_from');
				$date_to = Common::hashEmptyField($params, 'named.date_to');

				$date_diff = Common::dateDiff($date_from, $date_to);
				// $date_diff_month = Common::hashEmptyField($date_diff, 'm');
				$date_diff_day = Common::hashEmptyField($date_diff, 'days', 0) + 1;

				$last_date_from = date ("Y-m-01", strtotime('-'.$date_diff_month.' month', strtotime($date_from)));
				$last_date_to = date ("Y-m-t", strtotime('-1 month', strtotime($date_from)));

				$this->CrmProjectActivity->virtualFields['cnt'] = 'COUNT(CrmProjectActivity.id)';
				
				$this->RmCommon->_callRefineParams($params);
				$defaultOptions =  $this->CrmProjectActivity->_callRefineParams(array(), array(
					'order' => array(
						'CrmProjectActivityAttributeOption.attribute_option_id' => 'ASC',
					),
				));
				$elements = array(
					'status' => 'active',
					'mine' => true,
					'company' => true,
				);

				$options = $defaultOptions;
				$options['conditions']['CrmProjectActivityAttributeOption.attribute_option_id NOT'] = NULL;
				$options['conditions']['CrmProjectActivityAttributeOption.attribute_option_id <='] = Configure::read('__Site.Global.Variable.CRM.AttributeOpton.limit_id');
				$options['conditions']['DATE_FORMAT(CrmProjectActivity.activity_date, \'%Y-%m-%d\') >='] = $date_from;
				$options['conditions']['DATE_FORMAT(CrmProjectActivity.activity_date, \'%Y-%m-%d\') <='] = $date_to;
				$options['conditions']['CrmProject.is_cancel'] = 0;
				$options['contain'] = array(
					'CrmProject',
					'CrmProjectActivityAttributeOption',
				);
				$options['group'] = array(
					'CrmProjectActivityAttributeOption.attribute_option_id',
				);

				$this->CrmProjectActivity->unbindModel(array(
					'hasMany' => array(
						'CrmProjectActivityAttributeOption', 
					), 
				));

				$this->CrmProjectActivity->bindModel(array(
					'hasOne' => array(
						'CrmProjectActivityAttributeOption' => array(
							'foreignKey' => 'crm_project_activity_id', 
						), 
					), 
				), false);

				$attributeOptions = $this->CrmProjectActivity->AttributeOption->getData('all', false, array(
					'parent' => true,
					'show' => true,
				));
				$flag_activities = false;

				if( !empty($attributeOptions) ) {
					foreach ($attributeOptions as $key => &$attrOption) {
						$attribute_option_id = Common::hashEmptyField($attrOption, 'AttributeOption.id');

						$activityOptions = $options;
						$activityOptions['conditions']['CrmProjectActivityAttributeOption.attribute_option_id'] = $attribute_option_id;
						$crmActivity = $this->CrmProjectActivity->getData('first', $activityOptions, $elements);

						$attrOption['CrmProjectActivity']['cnt'] = Common::hashEmptyField($crmActivity, 'CrmProjectActivity.cnt');

						if( !empty($crmActivity) ) {
							$flag_activities = true;
						}
					}
				}

				// Total FollowUp
				$options = Common::_callUnset($options, array(
					'group',
				));
				$data_activity = $this->CrmProjectActivity->getData('first', $options, $elements);

				// CRM FollowUP
				$this->loadModel('ViewCrmFollowUp');
				$this->ViewCrmFollowUp->virtualFields['cnt'] = 'COUNT(ViewCrmFollowUp.id)';
				$followUpOption = array(
					'conditions' => array(
						'DATE_FORMAT(ViewCrmFollowUp.activity_date, \'%Y-%m-%d\') >=' => $date_from,
						'DATE_FORMAT(ViewCrmFollowUp.activity_date, \'%Y-%m-%d\') <=' => $date_to,
					),
				);
				$data_followup = $this->ViewCrmFollowUp->getData('first', $followUpOption);

				// Last Activity
				$options['conditions']['DATE_FORMAT(CrmProjectActivity.activity_date, \'%Y-%m-%d\') >='] = $last_date_from;
				$options['conditions']['DATE_FORMAT(CrmProjectActivity.activity_date, \'%Y-%m-%d\') <='] = $last_date_to;
				$data_last_activity = $this->CrmProjectActivity->getData('first', $options, $elements);

				// CRM Belum FollowUP
				$this->loadModel('ViewCrmUnfollowUp');
				$this->ViewCrmUnfollowUp->virtualFields['cnt'] = 'COUNT(ViewCrmUnfollowUp.id)';
				$followUpOption = array(
					'conditions' => array(
						'DATE_FORMAT(ViewCrmUnfollowUp.project_date, \'%Y-%m-%d\') >=' => $date_from,
						'DATE_FORMAT(ViewCrmUnfollowUp.project_date, \'%Y-%m-%d\') <=' => $date_to,
					),
				);
				$data_unfollowup = $this->ViewCrmUnfollowUp->getData('first', $followUpOption);

				if( !empty($attributeOptions) ) {
					foreach ($attributeOptions as $key => &$val) {
						$attribute_option_id = intval(Common::hashEmptyField($val, 'AttributeOption.id'));
						$attribute = Common::hashEmptyField($val, 'AttributeOption.name');
						$cnt = intval(Common::hashEmptyField($val, 'CrmProjectActivity.cnt', 0));

						$target = $this->User->Group->GroupTarget->getTarget('first', array(
							'conditions' => array(
								'GroupTarget.attribute_option_id' => $attribute_option_id,
							),
						));
						$target_value = Common::hashEmptyField($target, 'GroupTarget.value', 0) * $date_diff_day;

						if( empty($attribute) ) {
							$attribute = __('Lainnya');
							$val['AttributeOption']['name'] = $attribute;
						}

						$resultRows[$attribute][0] = $attribute;
						$resultRows[$attribute][1] = $cnt;
						$val['AttributeOption']['target'] = $target_value;
					}
				}

				if( !empty($resultRows) || $page == 1 ) {
					$result = array(
						'flag_activities' => $flag_activities,
						'date_from' => $date_from,
						'date_to' => $date_to,
						'activities' => $attributeOptions,
						'total_activity' => Common::hashEmptyField($data_activity, 'CrmProjectActivity.cnt'),
						'total_followup' => Common::hashEmptyField($data_followup, 'ViewCrmFollowUp.cnt'),
						'total_unfollowup' => Common::hashEmptyField($data_unfollowup, 'ViewCrmUnfollowUp.cnt'),
						'total_last_activity' => Common::hashEmptyField($data_last_activity, 'CrmProjectActivity.cnt'),
						'year' => $year
					);

					if( !empty($resultRows) ) {
						$result['rows'][] = array(
							__('Status'), 
							__('Aktivitas'), 
						);

						foreach ($resultRows as $key => $value) {
							$result['rows'][] = array(
								$value[0],
								$value[1],
							);
						}
					}
				} else {
					$result = null;
				}
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function admin_overview_kpr(){
		$title = __('Laporan KPR');

		$this->RmCommon->_layout_file('report');

		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'overview_report_kpr', 
		));
	}

	function backprocess_overview_kpr_filling(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_kpr',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('Kpr');
			
			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;

			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			$periode_id = Common::hashEmptyField($data, 'Search.periode_id');
			$period_date = Common::_callPeriodeDate($period);

			if($page == 1){
				$param_custom = array(
					'named' => array(
						'date_from' => $date_from,
						'date_to' => $date_to,
					),
				);
				
				$this->Kpr->virtualFields['cnt'] = 'COUNT(Kpr.id)';

				$this->RmCommon->_callRefineParams($params);
				$options =  $this->Kpr->_callRefineParams($param_custom, array(
					'conditions' => array(
						'Kpr.property_id <>' => 0,
					),
					'order' => array(
						'Kpr.id' => 'ASC',
					),
				));

				$elements = array(
					'admin_mine' => true,
					'status' => 'application',
				);

				$options = $this->Kpr->getData('paginate', $options, $elements);

				$periode_arr = Common::reportFormatDate($periode_id);
				$groupByFormat 	= Common::hashEmptyField($periode_arr, 'groupByFormat');
				$resultFormat 	= Common::hashEmptyField($periode_arr, 'resultFormat');

				$this->paginate = array_merge($options, array(
					'group' => array(
						$groupByFormat,
					),
					'limit' => 12,
				));

				$kprs = $this->paginate('Kpr');

				$kpr = $this->Kpr->getData('first', $options, $elements);
				$kpr_count = Common::hashEmptyField($kpr, 'Kpr.cnt', 0);

				if( !empty($kprs) ) {
					foreach ($kprs as $key => $kpr) {
						$date = Common::hashEmptyField($kpr, 'Kpr.created', null, array(
							'date' => 'Y-m-d',
						));
						$cnt = Common::hashEmptyField($kpr, 'Kpr.cnt', 0);

						$resultRows[$date][0] = $date;
						$resultRows[$date][1] = $cnt;
					}
				}

				if( !empty($resultRows) || $page == 1 ) {
					$result = array(
						'date_from' => $date_from,
						'date_to' => $date_to,
						'period' => $period,
						'named' => $param_custom,
						'kpr_count' => $kpr_count,
						'activities' => $kprs,
						// 'currentPeriod' => Common::hashEmptyField($period_date, 'title'),
					);

					if( !empty($resultRows) ) {
						ksort($resultRows);

						$result['cols'] = array(
							array(
								'label' => __('Tanggal'), 
								'type' => 'string',
							),
							array(
								'label' => __('Pengajuan'), 
								'type' => 'number', 
							),
						);

						foreach ($resultRows as $key => $value) {
							$result['rows'][] = array(
								'c' => array(
									array( 'v' => Common::formatDate($value[0], $resultFormat) ),
									array( 'v' => $value[1] ),
								),
							);
						}
					}
				}
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
			
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function api_overview_kpr_filling($kprs = false){
		if( empty($kprs) ) {
			$params = $this->params->params;
			$data = $this->request->data;

			$json = Common::hashEmptyField($data, 'json');
			$kprs = json_decode($json, true);
		}
	}

	function backprocess_value_filling_kpr($type = false){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_kpr',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('Kpr');
			
			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;

			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$direct_view = Common::hashEmptyField($params, 'named.direct_view');
			$page = Common::hashEmptyField($params, 'named.page', 1);

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			$periode_id = Common::hashEmptyField($data, 'Search.periode_id');
			$period_date = Common::_callPeriodeDate($period);

			if($page == 1){
				$param_custom = array(
					'named' => array(
						'date_from' => $date_from,
						'date_to' => $date_to,
					),
				);

				$periode_arr = Common::reportFormatDate($periode_id);
				$groupByFormat 	= Common::hashEmptyField($periode_arr, 'groupByFormat');
				$resultFormat 	= Common::hashEmptyField($periode_arr, 'resultFormat');

				$data_arr = $this->RmReport->getFillingPorvision($param_custom, $type, $groupByFormat);
				$kprs = Common::hashEmptyField($data_arr, 'kprs');
				$options = Common::hashEmptyField($data_arr, 'options');
				$elements = Common::hashEmptyField($data_arr, 'elements');

				$getCount = $this->RmReport->getCountFilling($options, $elements);
				$total_filling = Common::hashEmptyField($getCount, 'total_filling');				
				$total_provision = Common::hashEmptyField($getCount, 'total_provision');				

				if( !empty($kprs) ) {
					foreach ($kprs as $key => $kpr) {
						$date = Common::hashEmptyField($kpr, 'Kpr.created', null, array(
							'date' => 'Y-m-d',
						));
						$cnt = Common::hashEmptyField($kpr, 'Kpr.cnt', 0);

						$resultRows[$date][0] = $date;
						$resultRows[$date][1] = $cnt;
					}
				}

				if( !empty($resultRows) || $page == 1 ) {
					$result = array(
						'date_from' => $date_from,
						'date_to' => $date_to,
						'period' => $period,
						'named' => $param_custom,
						'total_filling' => $total_filling,
						'total_provision' => $total_provision,
						'activities' => $kprs,
						'active_tab' => $type,
						// 'currentPeriod' => Common::hashEmptyField($period_date, 'title'),
					);

					if( !empty($resultRows) ) {
						ksort($resultRows);

						$textType = ($type == 'filling') ? __('Nilai Pengajuan') : __('Komisi');

						$result['cols'] = array(
							array(
								'label' => __('Tanggal'), 
								'type' => 'string',
							),
							array(
								'label' => $textType, 
								'type' => 'number', 
							),
						);

						foreach ($resultRows as $key => $value) {
							$result['rows'][] = array(
								'c' => array(
									array( 'v' => Common::formatDate($value[0], $resultFormat) ),
									array( 'v' => $value[1] ),
								),
							);
						}
					}
				}
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if(!$direct_view){
				if( !empty($result) ) {
					return json_encode($result);
				} else {
					return null;
				}
			} else {
				$this->RmReport->_callBeforeViewJson($result, '/Elements/blocks/reports/dashboard/charts/value_filling_kpr');
			}
			
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_property_price(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_kpr',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('Kpr');

			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;
			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');	

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			if( $page == 1 ) {
				$param_custom = array(
					'named' => array(
						'date_from' => $date_from,
						'date_to' => $date_to,
					),
				);

				$this->RmCommon->_callRefineParams($params);

				$options =  $this->Kpr->_callRefineParams($param_custom, array(
					'conditions' => array(
						'Kpr.property_id <>' => 0,
					),
					'order' => array(
						'Kpr.id' => 'ASC',
					),
				));

				$elements = array(
					'admin_mine' => true,
					'status' => 'application',
				);

				$kprs = $this->Kpr->getData('all', $options, $elements);
				// $kprs = $this->paginate('Kpr');
				
				$data_arr = $this->RmReport->rangeProperties($kprs);
				
				$temps = Common::hashEmptyField($data_arr, 'temps');
				$resultRows = Common::hashEmptyField($data_arr, 'resultRows');
				$number = Common::hashEmptyField($data_arr, 'number');
				$top_price = Common::hashEmptyField($data_arr, 'top_price');

				if( !empty($resultRows) || $page == 1 ) {
					$result = array(
						'autoload' => true,
						'period' => $period,
						// 'currentPeriod' => Common::hashEmptyField($period_date, 'title'),
						'activities' => $temps,
						'date_from' => $date_from,
						'date_to' => $date_to,
						'cnt_data' => $number,
						'top_price' => $top_price,
					);

					if( !empty($resultRows) ) {
						$result['rows'][] = array(
							__('Status'),
							__('Aktivitas'),
						);

						foreach ($resultRows as $key => $value) {
							$result['rows'][] = array(
								$value[0],
								$value[1],
							);
						}
					}
				} else {
					$result = null;
				}
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				// print_r($result);die();
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_filling_area(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_kpr',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('Kpr');
			
			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;

			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			// $period_date = Common::_callPeriodeDate($period);

			if($page == 1){
				$param_custom = array(
					'named' => array(
						'date_from' => $date_from,
						'date_to' => $date_to,
					),
				);
				$result = $resultRows = array();
				
				$this->Kpr->virtualFields['cnt'] = 'COUNT(Kpr.id)';

				$this->RmCommon->_callRefineParams($params);
				$options =  $this->Kpr->_callRefineParams($param_custom, array(
					'conditions' => array(
						'Kpr.property_id <>' => 0,
					),
					'contain' => array(
						'ViewPropertyRelation'
					),
					'order' => array(
						'Kpr.id' => 'ASC',
					),
				));

				$elements = array(
					'admin_mine' => true,
					'status' => 'application',
				);

				$options = $this->Kpr->getData('paginate', $options, $elements);

				$this->paginate = array_merge($options, array(
					'group' => array(
						'ViewPropertyRelation.subarea_id', 
					),
					'order' => array(
						'Kpr.cnt' => 'DESC',
					),
					'limit' => 5,
				));

				$kprs = $this->paginate('Kpr');

				if($kprs){
					foreach ($kprs as $key => &$kpr) {
						$subarea_id = Common::hashEmptyField($kpr, 'ViewPropertyRelation.subarea_id');
						$city_id = Common::hashEmptyField($kpr, 'ViewPropertyRelation.city_id');

						$kpr = $this->Kpr->Property->PropertyAddress->City->getMerge($kpr, $city_id);
						$kpr = $this->Kpr->Property->PropertyAddress->Subarea->getMerge($kpr, $subarea_id);
							
						$cnt = Common::hashEmptyField($kpr, 'Kpr.cnt');

						$city = Common::hashEmptyField($kpr, 'City.name');
						$subarea = Common::hashEmptyField($kpr, 'Subarea.name');
						$zip = Common::hashEmptyField($kpr, 'ViewPropertyRelation.zip');

						$resultRows[] = array(
							'name' => sprintf('%s, %s %s', $subarea, $city, $zip),
							'cnt' => $cnt,
						);
					}
				}

				if( !empty($resultRows) || $page == 1 ) {
					$result = array(
						'autoload' => true,
						'period' => $period,
						// 'currentPeriod' => Common::hashEmptyField($period_date, 'title'),
						'activities' => $resultRows,
						'date_from' => $date_from,
						'date_to' => $date_to,
					);
				}
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_kpr_organizer(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_kpr',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('Kpr');
			$this->loadModel('KprBank');
			
			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;

			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			// $period_date = Common::_callPeriodeDate($period);

			if($page == 1){
				$param_custom = array(
					'named' => array(
						'date_from' => $date_from,
						'date_to' => $date_to,
					),
				);
				$result = $options = array();

				$this->Kpr->KprBank->virtualFields['cnt'] = 'COUNT(KprBank.setting_id)';

				$this->RmCommon->_callRefineParams($params);

				$elements = array(
					'admin_mine' => true,
					'status' => 'application',
				);

				$options = $this->Kpr->getData('paginate', $options, $elements);
				
				$options = $this->KprBank->_callRefineParams($param_custom, $options);
				$options = $this->KprBank->getData('paginate', array_merge($options, array(
					'contain' => array(
						'Kpr',
					),
				)), array(
					'status_kpr' => 'apply_kpr',
				));

				$this->paginate = array_merge($options, array(
					'group' => array(
						'KprBank.bank_id',
						 // 'KprBank.setting_id',
					),
					'order' => array(
						'KprBank.cnt' => 'DESC',
					),
					'limit' => 4,
				));

				$kpr_banks = $this->paginate('KprBank');

				// count setujui bank dan proses bank
				$approve_banks = $this->KprBank->getData('first', array_merge_recursive($options, array(
					'conditions' => array(
						'KprBank.document_status' => array(
							'approved_bank', 
							'credit_process', 
							'approved_credit',
							'completed',
						),
					),
				)), $elements);
				$process_banks = $this->KprBank->getData('first', array_merge_recursive($options, array(
					'conditions' => array(
						'KprBank.document_status' => array(
							'process', 
							'approved_proposal', 
							'proposal_without_comiission',
						),
					),
				)), $elements);

				$approve_bank = Common::hashEmptyField($approve_banks, 'KprBank.cnt');
				$process_bank = Common::hashEmptyField($process_banks, 'KprBank.cnt');

				if($kpr_banks){
					foreach ($kpr_banks as $key => &$value) {
						$bank_id = Common::hashEmptyField($value, 'KprBank.bank_id');
						$setting_id = Common::hashEmptyField($value, 'KprBank.setting_id');

						$value = $this->KprBank->callMergeList($value, array(
							'contain' => array(
								'Bank' => array(
									'elements' => array(
										'status' => 'all',
									),
								),
								'BankSetting' => array(
									'elements' => array(
										'status' => 'all',
										'type' => false,
									),
								),
							),
						));
						$value = $this->KprBank->BankSetting->callMergeList($value, array(
							'contain' => array(
								'BankProduct' => array(
									'elements' => array(
										'status' => 'all',
									),
								),
							),
						));

						$resultRows[] = $this->RmReport->KprOrganizer($value);
					}
				}

				if( !empty($resultRows) || $page == 1 ) {
					$result = array(
						'autoload' => true,
						'period' => $period,
						// 'currentPeriod' => Common::hashEmptyField($period_date, 'title'),
						'organizers' => $resultRows,
						'date_from' => $date_from,
						'date_to' => $date_to,
						'approve_bank' => $approve_bank,
						'process_bank' => $process_bank,
					);
				}
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function admin_overview_clients(){
		$title = __('Laporan Klien');

		$this->RmCommon->_layout_file('report');

		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'overview_report_client', 
		));
	}

	function backprocess_client_section($type = false){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_clients',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$resultRows = array();
			$tips = $maxCount = false;
			$params = $this->params->params;
			$data = $this->request->data;

			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');
			// $type = Common::hashEmptyField($params, 'named.type');
			$num = Common::hashEmptyField($params, 'named.num');
			$top_section = Common::hashEmptyField($params, 'named.top_section');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			if( $page == 1 ) {
				$param_custom = array(
					'named' => array(
						'date_from' => $date_from,
						'date_to' => $date_to,
					),
				);

				$this->RmCommon->_callRefineParams($params);
				$values = $this->RmReport->getSourceClient($type, $param_custom);

				$cnt_data = Common::hashEmptyField($values, 'cnt_data');
				// $top_age = Common::hashEmptyField($values, 'top_age');
				$top_value = Common::hashEmptyField($values, 'top_value');

				$values = Common::_callUnset($values, array(
					'cnt_data',
					'top_age',
					'top_value',
				));

				if(!empty($values)){
					$temp = array();

					$values = $this->RmReport->doSummary($values, array(
						'type' => $type,
						'top_section' => $top_section,
					));

					$top_val = 0;
					$top = 0;
					$top_age = array();

					foreach ($values as $key => &$val) {
						$reference = Common::hashEmptyField($val, 'UserClient.name');
						$cnt = Common::hashEmptyField($val, 'UserClient.cnt', 0);

						if(!empty($cnt) && empty($top_section)){
							if($key == 0){
								$top_val = $cnt;
								$temp[] = array(
									'reference' => $reference,
									'cnt' => $cnt,
								);
							} else {
								if($top_val == $cnt){
									$temp[] = array(
										'reference' => $reference,
										'cnt' => $cnt,
									);
								}
							}
						}

						if($cnt > $top){
							$top_age['name'] = $reference;
							$top_age['value'] = Common::_callTargetPercentage($cnt, $cnt_data);
							
							$top = $cnt;
						}

						if($reference){
							$resultRows[$reference][0] = __('%s', $reference);
							$resultRows[$reference][1] = intval($cnt);
						}
					}

					if($temp){
						$tips = Hash::Extract($temp, '{n}.reference');
						$maxCount = Hash::Extract($temp, '{n}.cnt');
						
						$tips = implode(', ', $tips);
						$maxCount = array_shift($maxCount);
					}

					if( !empty($resultRows) || $page == 1 ) {
						$result = array(
							'autoload' => true,
							'tips' => $tips,
							'top_section' => $top_section,
							'maxCount' => $maxCount,
							'period' => $period,
							// 'currentPeriod' => Common::hashEmptyField($period_date, 'title'),
							'activities' => $values,
							'cnt_data' => $cnt_data,
							'top_age' => $top_age,
							'num' => $num,
							'date_from' => $date_from,
							'date_to' => $date_to,
						);

						if( !empty($resultRows) ) {
							$result['rows'][] = array(
								__('Status'),
								__('Aktivitas'),
							);

							foreach ($resultRows as $key => $value) {
								$result['rows'][] = array(
									$value[0],
									$value[1],
								);
							}
						}
					} else {
						$result = null;
					}
				} else {
					$result = null;
				}
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function admin_overview(){
		$title = __('Overview');

		$this->RmCommon->_layout_file('report');

		$this->set(array(
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'overview_report',
		));
	}

	function backprocess_top_agent(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			)
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewTopAgent');
			
			$resultRows = array();
			$params = $this->params->params;

			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');

			$data 	= $this->request->data;

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			
			if( $page == 1 ) {
				$top_agent = $this->ViewTopAgent->topAgent($date_from, $date_to);

				$result = array(
					'top_agent' => $top_agent,
					'JsonType' => 'content',
					'year' => $year,
					'date_from' => $date_from,
					'date_to' => $date_to
				);
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function api_top_agent($value= null){
		$this->RmReport->_callBeforeViewJson($value, '/Elements/blocks/reports/agents/top_agent');
	}

	function admin_top_agents(){
		$this->loadModel('ViewTopAgent');

		$params = $this->params->params;

		$this->RmCommon->_callRefineParams($params);

		$date_from 	= Common::hashEmptyField($params, 'named.date_from');
		$date_to 	= Common::hashEmptyField($params, 'named.date_to');

		$default_options = $this->ViewTopAgent->topAgent(false, false, 20, 'paginate');
		$default_options['contain'] = array(
			'User',
			'UserProfile'
		);
		$options =  $this->ViewTopAgent->_callRefineParams($params, $default_options);
		$this->paginate = $this->ViewTopAgent->getData('paginate', $options);
		
		$values = $this->paginate('ViewTopAgent');

		$title = __('Top Agen');

		$this->set(array(
			'values' => $values,
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'overview_report', 
		));
	}

	function backprocess_top_search_area(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			)
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewTopPropertyArea');
			
			$resultRows = array();
			$params = $this->params->params;

			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');

			$data 	= $this->request->data;

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			
			if( $page == 1 ) {
				$top_search_area = $this->ViewTopPropertyArea->topSearch($date_from, $date_to);

				$result = array(
					'top_search_area' => $top_search_area,
					'JsonType' => 'content',
					'year' => $year,
					'date_from' => $date_from,
					'date_to' => $date_to
				);
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function api_top_search_area($value= null){
		$this->RmReport->_callBeforeViewJson($value, '/Elements/blocks/reports/properties/top_search_area');
	}

	function admin_top_search_areas(){
		$this->loadModel('ViewTopPropertyArea');

		$params = $this->params->params;

		$this->RmCommon->_callRefineParams($params);

		$date_from 	= Common::hashEmptyField($params, 'named.date_from');
		$date_to 	= Common::hashEmptyField($params, 'named.date_to');

		$default_options = $this->ViewTopPropertyArea->topSearch(false, false, 5, 'paginate');

		$options =  $this->ViewTopPropertyArea->_callRefineParams($params, $default_options);
		$this->paginate = $this->ViewTopPropertyArea->getData('paginate', $options);
		
		$values = $this->paginate('ViewTopPropertyArea');

		$values = $this->ViewTopPropertyArea->getMergeList($values, array(
			'contain' => array(
				'User' => array(
					'contain' => array(
						'UserProfile',
					),
				),
			),
		));

		$title = __('Top Lokasi Properti');

		$this->set(array(
			'values' => $values,
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'overview_report', 
		));
	}

	function backprocess_top_sold_property_area(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			),
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewTopSoldPropertyArea');
			
			$resultRows = array();
			$params = $this->params->params;

			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			
			if( $page == 1 ) {
				$data_sold = $this->ViewTopSoldPropertyArea->topSold($date_from, $date_to);

				$fields_arr = $temp_row[] = array(
					'Area',
					'Nilai'
				);

				$total = 0;
				if(!empty($data_sold)){
					if(!empty($data_sold)){
						foreach ($data_sold as $key => $value) {
							$custom_name = Common::hashEmptyField($value, 'ViewTopSoldPropertyArea.custom_name');
							$cnt = (int) Common::hashEmptyField($value, 'ViewTopSoldPropertyArea.cnt', 0);

							$total += $cnt;

							$temp_row[] = array(
								$custom_name,
								$cnt
							);
						}
					}
				}

				$rows = $temp_row;

				$fields = $this->RmCommon->fieldsChart($fields_arr);

				$result = array(
					'total' => $total,
					'list_data' => $data_sold,
					'rows' => $rows,
					'cols' => $fields,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'year' => $year
				);
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function admin_top_sold_property_area(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			),
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewTopSoldPropertyArea');

			$params = $this->params->params;

			$date_from 	= Common::hashEmptyField($params, 'named.date_from');
			$date_to 	= Common::hashEmptyField($params, 'named.date_to');

			$default_options = $this->ViewTopSoldPropertyArea->topSold(false, false, 5, 'paginate');

			$options =  $this->ViewTopSoldPropertyArea->_callRefineParams($params, $default_options);
			$this->paginate = $this->ViewTopSoldPropertyArea->getData('paginate', $options);
			
			$values = $this->paginate('ViewTopSoldPropertyArea');

			$values = $this->ViewTopSoldPropertyArea->getMergeList($values, array(
				'contain' => array(
					'User' => array(
						'contain' => array(
							'UserProfile',
						),
					),
				),
			));

			$title = __('Top Area Terjual / Tersewa');

			$this->set(array(
				'values' => $values,
				'module_title' => $title, 
				'title_for_layout' => $title, 
				'active_menu' => 'overview_report',
				'date_from' => $date_from, 
				'date_to' => $date_to 
			));
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_listing_property(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			)
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewChartAddListing');
			
			$resultRows = array();
			$params = $this->params->params;

			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			
			if( $page == 1 ) {
				$properties = $this->ViewChartAddListing->dataListing($date_from, $date_to);
				
				$rows = Common::hashEmptyField($properties, 'rows');
				$total = Common::hashEmptyField($properties, 'total', 0);

				if(!empty($rows)){
					$rows = $this->RmCommon->rowsChart($rows);
				}

				$fields_arr = array(
					'Periode',
					'Nilai'
				);

				$fields = $this->RmCommon->fieldsChart($fields_arr);

				$result = array(
					'total' => $total,
					'rows' => $rows,
					'cols' => $fields,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'year' => $year
				);
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_percentage_property(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'csa',
				'admin' => true,
				'backprocess' => false,
			)
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('Log');
			
			$resultRows = array();
			$params = $this->params->params;

			$data = $this->request->data;

			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');

			$company_ids = Common::hashEmptyField($data, 'Search.company_id');

			$date = $this->RmCommon->getDateRangeReport();
			$date_prev = $this->RmCommon->getDateRangeCompare($date);

			// if(!empty($company_ids)){
			// 	$params['named']['company_id'] = $company_ids;
			// }
			
			if( $page == 1 ) {
				$data_arr = $this->RmReport->getPropertyLog(array(
					'current' => $date,
					'prev' => $date_prev,
				),  $company_ids );

				$result = array(
					'values_current' => Common::hashEmptyField($data_arr, 'values_current'),
					'propertyLog_current' => Common::hashEmptyField($data_arr, 'propertyLog_current'),
					'values_prev' => Common::hashEmptyField($data_arr, 'values_prev'),
					'propertyLog_prev' => Common::hashEmptyField($data_arr, 'propertyLog_prev'),
					'date_current' => $date,
					'date_prev' => $date_prev,
					'company_ids' => $company_ids,
				);
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	// function admin_percentage_add_property(){
	// 	$this->loadModel('Property');

	// 	$params = $this->params->params;
	// 	$this->RmCommon->_callRefineParams($params);
	// 	debug($params);die();
	// 	// $options = 
	// }

	function admin_add_listing_property(){
		$this->loadModel('Property');

		$params = $this->params->params;

		$this->RmCommon->_callRefineParams($params);

		$options =  $this->Property->_callRefineParams($params, array(
			'conditions' => array(
				'Property.status' => 1,
				'Property.published' => 1,
			),
			'group' => array(
				'Property.user_id', 'CONCAT(YEAR(Property.created),"-",MONTH(Property.created))'
			),
			'order' => array(
				'Property.created' => 'ASC'
			)
		));
		
		$this->paginate = $this->Property->getData('paginate', $options, array(
			'mine' => true,
			'company' => true
		));
		
		$values = $this->paginate('Property');

		$values = $this->Property->getDataList($values, array(
			'contain' => array(
				'MergeDefault',
                'PropertyAddress',
                'PropertyAsset',
                'PropertySold',
			),
		));

		$title = __('Listing Properti');

		$this->set(array(
			'values' => $values,
			'module_title' => $title, 
			'title_for_layout' => $title, 
			'active_menu' => 'overview_report', 
		));
	}

	function backprocess_target_listing_property(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			),
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewChartAddListing');
			
			$resultRows = array();
			$params = $this->params->params;

			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			
			if( $page == 1 ) {
				$sum_cnt = $this->ViewChartAddListing->getSumListing($date_from, $date_to);
				$target_config = $this->RmReport->getTargetConfig($params);

				$result = array(
					'sum_cnt' 		=> $sum_cnt,
					'target_config' => $target_config,
					'date_from' 	=> $date_from,
					'date_to' 		=> $date_to,
					'year' 			=> $year
				);
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_selling(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false
			),
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false
			)
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewRevenueProperty');

			$params = $this->params->params;
			
			$page 			= Common::hashEmptyField($params, 'named.page', 1);
			$direct_view 	= Common::hashEmptyField($params, 'named.direct_view');
			$year 			= Common::hashEmptyField($params, 'named.year');
			$type 			= Common::hashEmptyField($params, 'named.type', 'komisi');
			$referal 		= Common::hashEmptyField($params, 'named.referal');
			$tips 			= Common::hashEmptyField($params, 'named.tips', 1, array(
				'isset' => true
			));

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			if( $page == 1 ) {
				$result 		= $this->ViewRevenueProperty->getRevenue($date_from, $date_to, $type);
				$target_config 	= $this->RmReport->getTargetConfig($params);

				$prev_target	= $this->ViewRevenueProperty->prevRevenue($date_from, $date_to);

				$rows 	= Common::hashEmptyField($result, 'rows', array());
				$fields = Common::hashEmptyField($result, 'fields', array());

				if(!empty($rows)){
					$rows = $this->RmCommon->rowsChart($rows);
				}

				$fields = $this->RmCommon->fieldsChart($fields);

				$result['JsonType'] = 'content';
				$result['cols'] = $fields;
				$result['rows'] = $rows;
				$result['target_config'] = $target_config;
				$result['prev_target'] = $prev_target;
				$result['type'] = $type;

				$result['year'] = $year;
				$result['date_from'] = $date_from;
				$result['date_to'] = $date_to;
				$result['referal'] = $referal;
				$result['tips'] = $tips;
			}else{
				$result = array();
			}

			$this->autoRender = false;

			if(!$direct_view){
				if( !empty($result) ) {
					return json_encode($result);
				} else {
					return null;
				}
			}else{
				$this->set('tips', 2);
				$this->RmReport->_callBeforeViewJson($result, '/Elements/blocks/reports/dashboard/charts/target_revenue');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_ratio_property(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewUnionRatioUserProperty');

			$params = $this->params->params;
			
			$page 			= Common::hashEmptyField($params, 'named.page', 1);
			$year 			= Common::hashEmptyField($params, 'named.year');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			if( $page == 1 ) {
				$result = $this->ViewUnionRatioUserProperty->getRatio($date_from, $date_to);

				$rows 	= Common::hashEmptyField($result, 'rows', array());
				$fields = Common::hashEmptyField($result, 'fields', array());

				if(!empty($rows)){
					$rows = $this->RmCommon->rowsChart($rows);
				}

				$fields = $this->RmCommon->fieldsChart($fields);

				$result['JsonType'] = 'content';
				$result['cols'] = $fields;
				$result['rows'] = $rows;

				$result['year'] = $year;
				$result['date_from'] = $date_from;
				$result['date_to'] = $date_to;
			}else{
				$result = array();
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_ebrochures(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false
			),
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewChartEbrochures');
			$this->loadModel('UserCompanyEbrochure');

			$params = $this->params->params;
			
			$page 			= Common::hashEmptyField($params, 'named.page', 1);
			$year 			= Common::hashEmptyField($params, 'named.year');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			if( $page == 1 ) {
				$result 		= $this->ViewChartEbrochures->getRevenue($date_from, $date_to);
				$target_config 	= $this->RmReport->getTargetConfig($params);

				$prev_target	= $this->ViewChartEbrochures->prevRevenue($date_from, $date_to);

				$all_ebrosur = $this->UserCompanyEbrochure->getData('count', array(), array(
					'mine' => true
				));

				$rows 	= Common::hashEmptyField($result, 'rows', array());
				$fields = Common::hashEmptyField($result, 'fields', array());

				if(!empty($rows)){
					$rows = $this->RmCommon->rowsChart($rows);
				}

				$fields = $this->RmCommon->fieldsChart($fields);

				$result['JsonType'] = 'content';
				$result['cols'] = $fields;
				$result['rows'] = $rows;
				$result['target_config'] = $target_config;
				$result['prev_target'] = $prev_target;
				$result['all_ebrosur'] = $all_ebrosur;

				$result['year'] = $year;
				$result['date_from'] = $date_from;
				$result['date_to'] = $date_to;
			}else{
				$result = array();
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_leads_property(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false
			),
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if( !empty($checkAcl) ) {
			$this->loadModel('ViewUnionPropertyLeads');

			$params = $this->params->params;
			
			$page 			= Common::hashEmptyField($params, 'named.page', 1);
			$direct_view 	= Common::hashEmptyField($params, 'named.direct_view');
			$year 			= Common::hashEmptyField($params, 'named.year');
			$type 			= Common::hashEmptyField($params, 'named.type', 'visitor');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			if( $page == 1 ) {

				if($type == 'visitor'){
					$result = $this->User->Property->PropertyView->getVisitor($date_from, $date_to);
				}else{
					$result = $this->ViewUnionPropertyLeads->getLeads($date_from, $date_to);
				}

				$top_property_views = $this->User->Property->PropertyView->topVisitor($date_from, $date_to);
				$temp['Property'] = Common::hashEmptyField($top_property_views, 'Property');

				$temp = $this->User->Property->getDataList($temp, array(
		            'contain' => array(
		            	'MergeDefault',
		                'PropertyAddress',
		                'PropertyAsset',
		            ),
		        ));
		        $top_property_views = $temp;

				$visitor_summary = $this->RmReport->summaryPropertyVisitor($date_from, $date_to);

				$rows 	= Common::hashEmptyField($result, 'rows', array());
				$fields = Common::hashEmptyField($result, 'fields', array());

				if(!empty($rows)){
					$rows = $this->RmCommon->rowsChart($rows);
				}

				$fields = $this->RmCommon->fieldsChart($fields);

				$result['JsonType'] = 'content';
				$result['cols'] = $fields;
				$result['rows'] = $rows;
				$result['type'] = $type;
				$result['top_property_views'] = $top_property_views;
				$result['visitor_summary'] = $visitor_summary;

				$result['year'] = $year;
				$result['date_from'] = $date_from;
				$result['date_to'] = $date_to;
			}else{
				$result = array();
			}

			$this->autoRender = false;

			if(!$direct_view){
				if( !empty($result) ) {
					return json_encode($result);
				} else {
					return null;
				}
			}else{
				$this->RmReport->_callBeforeViewJson($result, '/Elements/blocks/reports/dashboard/charts/leads_property');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_budget_client(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_clients',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('UserClient');
			$user_login_id = Configure::read('User.id');
			$data = $this->request->data;

			$resultRows = array();

			$data_arr = $this->User->getUserParent($user_login_id);
			$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
			$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

			$params = $this->params->params;

			$page = Common::hashEmptyField($params, 'named.page', 1);
			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			$param_custom = array(
				'named' => array(
					'date_from' => $date_from,
					'date_to' => $date_to,
				),
			);

			$date_from = Common::HashEmptyField($params, 'named.date_from');
			$date_to = Common::HashEmptyField($params, 'named.date_to');

			if($is_sales){
				$conditions = array(
					'UserClient.agent_id' => $user_ids,
				);
			} else {
				$conditions = array(
					'UserClient.company_id' => $this->parent_id,
				);
			}

			$this->UserClient->virtualFields['cnt'] = 'COUNT(UserClient.id)';

			$options = $this->UserClient->_callRefineParams($param_custom, array(
				'conditions' => array_merge(array(
					'UserClient.status' => 1,
					'UserClient.range_budget <>' => NULL,
				), $conditions),
				'order' => array(
					'UserClient.created' => 'DESC',
				),
			));

			$this->RmCommon->_callRefineParams($params);

			$user_options = array_merge($options, $this->User->getData('paginate', $options, array(
				'status' => 'all',
			)));

			$user_options['contain'][] = 'User';

			$avg_data = $this->RmReport->AVGSoldRent($data_arr, $param_custom);
			$avg_sold = Common::hashEmptyField($avg_data, 'avg_sold');
			$avg_rent = Common::hashEmptyField($avg_data, 'avg_rent');

			$this->paginate = array_merge($user_options, array(
				'order' => array(
					'FIELD(UserClient.range_budget, "under50jt", "50-100", "100-200", "200-500", "500-800", "800-1,5m", "1,5m-5m", "5m-10m", "10m-50m", "uper50m"), DESC'
				),
				'group' => array(
					'UserClient.range_budget',
				),
			));

			$values = $this->paginate('UserClient');

			if($values){
				$budgetOptions = Configure::read('Global.Data.budget_client');
				foreach ($values as $key => $value) {
					$flag = Common::hashEmptyField($value, 'UserClient.range_budget');
					$budget = Common::hashEmptyField($budgetOptions, $flag);
					$cnt = Common::hashEmptyField($value, 'UserClient.cnt', 0);

					$resultRows[$flag][0] = $budget;
					$resultRows[$flag][1] = $cnt;
				}
			}

			if( !empty($resultRows) || $page == 1) {
				$result = array(
					'period' => $period,
					'avg_sold' => $avg_sold,
					'avg_rent' => $avg_rent,
					'page_title' => __('Budget Klien'),
					'params' => $param_custom['named'],
					'activities' => $values,
					'date_from' => $date_from,
					'date_to' => $date_to,
				);

				if( !empty($resultRows) ) {
					// ksort($resultRows);
					
					$result['cols'] = array(
						array(
							'label' => __('range harga'), 
							'type' => 'string', 
						),
						array(
							'label' => 'range harga',
							'type' => 'number', 
						),
					);

					foreach ($resultRows as $key => $value) {
						$result['rows'][] = array(
							'c' => array(
								array( 'v' => $value[0] ),
								array( 'v' => $value[1] ),
								// array( 'v' => $value[2] ),
							),
						);
					}
				}
			} else {
				$result = null;
			}

			$this->autoRender = false;
			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}

		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_payment_client(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_clients',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$resultRows = array();
			$tips = $maxCount = false;
			$params = $this->params->params;
			$data = $this->request->data;

			$type = 'payment';

			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');
			// $type = Common::hashEmptyField($params, 'named.type');
			$num = Common::hashEmptyField($params, 'named.num');
			$top_section = Common::hashEmptyField($params, 'named.top_section');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			if( $page == 1 ) {
				$param_custom = array(
					'named' => array(
						'date_from' => $date_from,
						'date_to' => $date_to,
					),
				);

				$this->RmCommon->_callRefineParams($params);

				$values = $this->RmReport->getSourceClient($type, $param_custom);
				$cnt_data = Common::hashEmptyField($values, 'cnt_data');
				$values = Common::_callUnset($values, array(
					'cnt_data',
				));

				if(!empty($values)){
					$temp = array();

					$values = $this->RmReport->doSummary($values, array(
						'type' => $type,
						'top_section' => $top_section,
					));

					$top_val = 0;
					foreach ($values as $key => &$val) {
						$reference = Common::hashEmptyField($val, 'UserClient.name');
						$cnt = Common::hashEmptyField($val, 'UserClient.cnt', 0);

						if(!empty($cnt) && empty($top_section)){
							if($key == 0){
								$top_val = $cnt;
								$temp[] = array(
									'reference' => $reference,
									'cnt' => $cnt,
								);
							} else {
								if($top_val == $cnt){
									$temp[] = array(
										'reference' => $reference,
										'cnt' => $cnt,
									);
								}
							}
						}

						if($reference){
							$resultRows[$reference][0] = __('%s', $reference);
							$resultRows[$reference][1] = intval($cnt);
						}
					}

					if($temp){
						$tips = Hash::Extract($temp, '{n}.reference');
						$maxCount = Hash::Extract($temp, '{n}.cnt');
						
						$tips = implode(', ', $tips);
						$maxCount = array_shift($maxCount);
					}

					if( !empty($resultRows) || $page == 1 ) {
						$result = array(
							'autoload' => true,
							'tips' => $tips,
							'period' => $period,
							// 'currentPeriod' => Common::hashEmptyField($period_date, 'title'),
							'activities' => $values,
							'cnt_data' => $cnt_data,
							'type' => $type,
						);

						if( !empty($resultRows) ) {
							$result['rows'][] = array(
								__('Status'),
								__('Aktivitas'),
							);

							foreach ($resultRows as $key => $value) {
								$result['rows'][] = array(
									$value[0],
									$value[1],
								);
							}
						}
					} else {
						$result = null;
					}
				} else {
					$result = null;
				}
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				// print_r($result);die();
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_potential_clients(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_clients',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('UserClient');
			$user_login_id = Configure::read('User.id');
			$data = $this->request->data;

			$resultRows = array();

			$data_arr = $this->User->getUserParent($user_login_id);
			$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
			$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

			$params = $this->params->params;

			$page = Common::hashEmptyField($params, 'named.page', 1);
			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');
			$period_date = Common::_callPeriodeDate($period);

			$date 		= $this->RmCommon->getDateRangeReport();

			if($date){
				$params['named']['date_from'] = Common::hashEmptyField($date, 'date_from');
				$params['named']['date_to'] = Common::hashEmptyField($date, 'date_to');
			}

			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			// if( !empty($data) ) {
			// 	$date = Common::hashEmptyField($data, 'Search.date');
			// 	$params['named'] = Common::_callConvertMonthRange($params['named'], $date, array(
			// 		'date_from' => 'date_from',
			// 		'date_to' => 'date_to',
			// 		'format' => 'Y-m-d'
			// 	));
			// }

			if($is_sales){
				$conditions = array(
					'UserClient.agent_id' => $user_ids,
				);
			} else {
				$conditions = array(
					'UserClient.company_id' => $this->parent_id,
				);
			}

			$this->UserClient->virtualFields['cnt'] = 'COUNT(UserClient.id)';

			$options = $this->UserClient->_callRefineParams($params, array(
				'conditions' => array_merge(array(
					'UserClient.status' => 1,
				), $conditions),
				'order' => array(
					'DATE_FORMAT(UserClient.created, \'%Y-%m\')' => 'ASC',
				),
			));

			$this->RmCommon->_callRefineParams($this->params);

			// $user_options = array_merge($options, $this->User->getData('paginate', $options, array(
			// 	'status' => 'all',
			// )));

			// $user_options['contain'][] = 'User';
			$this->paginate = array_merge($options, array(
				'group' => array(
					'DATE_FORMAT(UserClient.created, \'%Y-%m\')',
				),
			));
			$values = $this->paginate('UserClient');
			$client = $this->UserClient->getData('first', $options);
			$client_count = Common::hashEmptyField($client, 'UserClient.cnt', 0);

			if($values){
				foreach ($values as $key => $value) {
					$date = Common::hashEmptyField($value, 'UserClient.created', null, array(
						'date' => 'M/Y',
					));
					$cnt = Common::hashEmptyField($value, 'UserClient.cnt', 0);

					$resultRows[$date][0] = $date;
					$resultRows[$date][1] = $cnt;
				}
			}

			if( !empty($resultRows) || $page == 1) {
				$result = array(
					'autoload' => true,
					'period' => $period,
					'page_title' => __('Klien'),
					'list_data' => $values,
					'total' => $client_count,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'year' => $year
				);

				if( !empty($resultRows) ) {
					// ksort($resultRows);
					
					$result['cols'] = array(
						array(
							'label' => __('bln/thn'), 
							'type' => 'string', 
						),
						array(
							'label' => 'total klien',
							'type' => 'number', 
						),
					);

					foreach ($resultRows as $key => $value) {
						$result['rows'][] = array(
							'c' => array(
								array( 'v' => $value[0] ),
								array( 'v' => $value[1] ),
								// array( 'v' => $value[2] ),
							),
						);
					}
				}
			} else {
				$result = null;
			}
			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_status_kpr(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'overview_kpr',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$data = $this->request->data;

			$element = array(
				'admin_mine' => true,
				'company' => false,
			);

			$params = $this->params->params;

			$period = Common::hashEmptyField($params, 'named.period');
			$autoload = Common::hashEmptyField($params, 'named.autoload');
			$page = Common::hashEmptyField($params, 'named.page');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			$period_date = Common::_callPeriodeDate($period);
			$param_custom = array(
				'named' => array(
					'date_from' => $date_from,
					'date_to' => $date_to,
				),
			);

			$total_kpr = $this->User->Kpr->getCountKpr($element, array(
				'conditions' => array(
					'date_format(Kpr.created, \'%Y-%m\') >=' => $date_from,
					'date_format(Kpr.created, \'%Y-%m\') <=' => $date_to,
				),
			));

			if( !empty($total_kpr) && is_array($total_kpr) && $page == 1){
				$result = array(
					'autoload' => true,
					'period' => $period,
					'page_title' => __('Status KPR'),
					'activities' => $total_kpr,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'page' => $page,
					'JsonType' => 'content',
				);
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	public function backprocess_export(){
		$this->autoLayout = false;
		$this->autoRender = false;

		$data = $this->data;

	//	untuk testing
	//	$data = array(
	//		'Export' => array(
	//			'title'	=> 'Testing Report', 
	//			'page'	=> array(
	//				'https://facebook.github.io/react-native/img/header_logo.png', 
	//			), 
	//		), 
	//	);

		if($data){
			$dataCompany = $this->data_company;
			$companyName = Common::hashEmptyField($dataCompany, 'UserCompany.name');

		//	extract post data
			$datetime	= date('Y-m-d.Hi');
			$title		= sprintf('report-%s', $datetime);
			$title		= Hash::get($data, 'Export.title', $title);
			$period		= Hash::get($data, 'Export.period');
			$subject	= Hash::get($data, 'Export.subject');
			$pages		= Hash::get($data, 'Export.page', array());

		//	prepare pdf layout
			App::import('Vendor','xtcpdf');

			$pdf = new XTCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

			$documentAuthor		= $companyName;
			$documentTitle		= $title;
			$documentSubject	= $subject ?: $title;

			$pdf->SetCreator(PDF_CREATOR);
			$pdf->SetAuthor($documentAuthor);
			$pdf->SetTitle($documentTitle);
			$pdf->SetSubject($documentSubject);
		//	$pdf->SetKeywords('TCPDF, PDF, example, test, guide');

			$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
			$pdf->SetFont('helvetica', '', 10, '', true);

			$marginTop		= 5;
			$marginLeft		= 0;
			$marginRight	= 0;

		//	$pdf->SetAutoPageBreak(true, PDF_MARGIN_TOP);
			$pdf->SetAutoPageBreak(false, 0);
			$pdf->SetMargins($marginLeft, $marginTop, $marginRight);
		//	$pdf->SetHeaderMargin($marginTop);
		//	$pdf->SetFooterMargin($marginTop);

			$headingTitle = $documentAuthor ? sprintf('%s (%s)', $title, $documentAuthor) : $title;

			$heading = '<table cellspacing="0" cellpadding="0" border="0">';
			$heading.= '<tr><td><p style="padding:10px 0px;">' . $headingTitle . '</p></td></tr>';

			if($period){
				$heading.= '<tr><td><p style="font-size:8px !important; padding:10px 0px;">' . $period . '</p></td></tr>';
			}

			$heading.= '</table>';

			if($pages){
				foreach($pages as $key => $page){
				//	page berupa gambar base64
					$pageHtml = '<table width="100%" cellpadding="0" cellspacing="0">';

					if($key == 0){
					//	$pageHtml.= '<tr><td><h1 style="padding:10px 20px;margin:0;">' . $title . '</h1></td></tr>';
					//	$pageHtml.= '<tr><td><span style="padding:0 20px;margin:0;color:#DDDDDD; font-size:10px; text-transform:uppercase;">' . $documentAuthor . '</span></td></tr>';
					}

					$pageHtml.= '<tr><td align="center"><img src="' . $page . '"></td></tr>';
					$pageHtml.= '</table>';

				//	echo($pageHtml);exit;

					$pdf->setPrintHeader(false);
					$pdf->setPrintFooter(false);

				//	add new page
					$pdf->AddPage();

				//	header
					$pdf->SetFont('helvetica', '', 10, '', true);
					$pdf->writeHTML($heading, true, false, true, false, 'C');

     			//	content
					$pdf->writeHTML($pageHtml, true, false, true, false, 'C');

				//	footer
					$pdf->SetY(-10);
					$pdf->SetFont('helvetica', '', 5);

				//	paging
					$page	= $pdf->getAliasNumPage();
					$count	= $pdf->getAliasNbPages();

					$pdf->Cell(0, 10, sprintf('Page %s/%s', $page, $count), 0, false, 'C', 0, '', 0, false, 'T', 'M');

				// reset pointer to the last page
					$pdf->lastPage();
				}
			}

		//	render output
			header('Content-type: application/pdf');
			echo($pdf->Output(sprintf('%s_%s.pdf', $title, $datetime), 'D'));
			exit;
		}
		else{
			$this->RmCommon->redirectReferer('Invalid Parameter');
		}
	}

	function admin_log_detail($group_id = false, $date_from = false, $date_to = false, $company_ids = false){
		$this->loadModel('PropertyLog');

		$params = $this->params->params;

		$title = __('Detail Property Usage');

		$parent_id = !empty($company_ids) ? json_decode($company_ids) : array();

		$orConditions = array(
			'Group.id >' => '21',
			'Group.id' => $group_id,
		);

		if($parent_id){
			$orConditions['Group.user_id'] = $parent_id;
		}

		$group = $this->User->Group->getData('first', array(
			'conditions' => array(
				'OR' => array(
					$orConditions,
					array(
						'Group.id' => array_merge(array('2'), Configure::read('__Site.Admin.Company.id')),
					),
				),
			),
		));

		if($group){
			$default_options = array(
				'conditions' => array(
					'PropertyLog.group_id' => $group_id,
					'PropertyLog.group_id <>' => Configure::read('__Site.Admin.List.id'),
					'PropertyLog.date >=' => $date_from,
					'PropertyLog.date <=' => $date_to,
				),
			);

			if($parent_id){
				$default_options['PropertyLog.parent_id'] = $parent_id;
			}

			$this->RmCommon->_callRefineParams($this->params);
			$options = $this->PropertyLog->_callRefineParams($this->params->params, $default_options);

			$this->RmCommon->_layout_file('report');

			$this->paginate = $this->PropertyLog->getData('paginate', $options);
			$values = $this->paginate('PropertyLog');
			$values = $this->PropertyLog->getMergeList($values, array(
				'contain' => array(
					'User',
					'Property' => array(
						'elements' => array(
							'status' => array(
								'all',
							),
							'company' => false,
						),
					),
				),
			));

			$values = $this->PropertyLog->Property->getMergeList($values, array(
				'contain' => array(
					'PropertyAddress' => array(
						'contain' => array(
							'Region',
							'City',
							'Subarea',
						),
					),
				),
			));

			$this->set(array(
				'group' => $group, 
				'module_title' => $title, 
				'title_for_layout' => $title, 
				'active_menu' => 'csa_report',
				'date_from' => $date_from,
				'date_to' => $date_to,
				'company_ids' => $company_ids,
				'values' => $values,
				'actionLists' => Configure::read('Global.Data.property_log'),
				'urlBack' => array(
					'controller' => 'reports',
					'action' => 'overview',
					'admin' => true,
				),
			));
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function admin_csa(){
		$title = __('CSA Overview');
		$this->RmCommon->_layout_file(array(
			'select2',
			'report',
		));

		$companies =  $this->RmCommon->_callCompanies('all');

		$this->set(array(
			'module_title' => $title,
			'companies' => $companies,
			'title_for_layout' => $title, 
			'active_menu' => 'csa_report',
			'export' => array(
				'title' => __('CSA Overview'),
			),
		));
	}

	function admin_activity_detail(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'csa',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$title = __('Activity Detail');

			$this->loadModel('Log');
			$this->loadModel('Acos');

			$params = $this->params->params;
			$default_options = array();

			$acos_id = Common::hashEmptyField($params, 'named.acosid');
			$device = Common::hashEmptyField($params, 'named.device');

			// on time
			$hour = Common::hashEmptyField($params, 'named.hour');
			$day = Common::hashEmptyField($params, 'named.day');

			$parent_id = Common::hashEmptyField($params, 'named.company_id', false, array(
				'type' => 'json_decode',
			));

			if($parent_id){
				$params = Hash::insert($params, 'named.company_id', $parent_id);

				$company_lists = $this->Log->UserCompany->getData('list', array(
					'conditions' => array(
						'UserCompany.user_id' => $parent_id,
					),
				));
			}

			$on_time = array(
				'flag' => (isset($params['named']['hour']) && isset($params['named']['day']) ) ? true : false,
				'hour' => $hour,
				'day' => $day,
			);
			// 

			if($acos_id){
				$aco = $this->Acos->find('first', array(
					'conditions' => array(
						'Acos.id'=> $acos_id,
					),
				));

				$action = Common::hashEmptyField($aco, 'Acos.alias');

				$default_options['conditions'][] = array(
					'Log.model' => $action,
				);
			}

			$options = $this->Log->_callRefineParams($params, $default_options);
			$this->paginate = $this->Log->getData('paginate', $options, array(
				'activity' => true,
				'on_time' => $on_time,
			));

			$values = $this->paginate('Log');
			$values = $this->Log->getMergeList($values, array(
				'contain' => array(
					'UserCompany' => array(
						'uses' => 'UserCompany',
						'primaryKey' => 'user_id',
						'foreignKey' => 'parent_id',
					),
					'User',
				),
			));

			$this->set(array(
				'device' => $device,
				'aco' => !empty($aco) ? $aco : false,
				'aco_parent' => !empty($aco_parent) ? $aco_parent : false,
				'module_title' => $title,
				'title_for_layout' => $title, 
				'acos_id' => $acos_id, 
				'on_time' => $on_time,
				'values' => $values,
				'date_from' => Common::hashEmptyField($params, 'named.date_from'),
				'date_to' => Common::hashEmptyField($params, 'named.date_to'),
				'company_lists' => !empty($company_lists) ? $company_lists : array(),
				'active_menu' => 'csa_report',
				'urlBack' => array(
					'controller' => 'reports',
					'action' => 'csa',
					'admin' => true,
				),
			));
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_chart_on_device(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'csa',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('Log');

			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;

			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');

			$company_ids = Common::hashEmptyField($data, 'Search.company_id');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_prev 	= $this->RmCommon->getDateRangeCompare($date);

			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			$params['named'] = array_merge($params['named'], array(
				'date_from' => $date_from,
				'date_to' => $date_to,
			));

			if(!empty($company_ids)){
				$params['named']['company_id'] = $company_ids;
			}

			$param_prev['named'] = $date_prev;

			if( $page == 1 ) {
				$values = array();
				$fields_arr = $temp_row[] = array(
					'Area',
					'Nilai'
				);

				$this->Log->virtualFields['cnt'] = 'COUNT(Log.id)';

				// present
				$options = $this->Log->_callRefineParams($params, array());
				$options_prev = $this->Log->_callRefineParams($param_prev, array());

				$devices = array(
					'Mobile' => 'mobile',
					'Dekstop' => 'browser',
				);

				$dataNull = false;
				$total = 0;
				foreach ($devices as $name => $device) {
					$options['conditions']['Log.device'] = $device;
					$options_prev['conditions']['Log.device'] = $device;

					$value = $this->Log->getData('first', $options, array(
						'activity' => true,
					));
					$valuePrev = $this->Log->getData('first', $options_prev, array(
						'activity' => true,
					));

					$value = Hash::insert($value, 'Log.device', $device);

					$cnt = (int) Common::hashEmptyField($value, 'Log.cnt', 0);

					$total += $cnt;

					$temp_row[] = array(
						$name,
						$cnt
					);

					$values[] = $value;
					$prev_values[] = $valuePrev;

					if($cnt){
						$dataNull = true;
					}
				}
				//
				$rows = $temp_row;

				if(empty($dataNull)){
					unset($values);
				}

				$fields = $this->RmCommon->fieldsChart($fields_arr);

				$result = array(
					'total' => $total,
					'list_data' => !empty($values) ? $values : array(),
					'prev_values' => $prev_values,
					'rows' => $rows,
					'cols' => $fields,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'year' => $year,
					'company_ids' => $company_ids,
				);

			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}

		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_chart_activity_page($page = null){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'csa',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('Log');

			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;
			// debug($params);die();

			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');
			$direct_view = Common::hashEmptyField($params, 'named.direct_view');

			// untuk sorting
			$sort = Common::hashEmptyField($params, 'named.sort');

			$company_ids = Common::hashEmptyField($data, 'Search.company_id');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			$params['named'] = array_merge($params['named'], array(
				'date_from' => $date_from,
				'date_to' => $date_to,
			));

			if(!empty($company_ids)){
				$params['named']['company_id'] = $company_ids;
			}

			if( $page == 1 || $page == 'true' ) {
				$default_options = array(
					'conditions' => array(
						'Acos.parent_id' => '1',
						'Acos.alias <>' => Configure::read('__Site.exlude_acos'),
					),
					'order' => array(
						'Acos.alias' => 'ASC',
					),
				);
				$values = $this->Log->Acos->find('all', $default_options);

				if($values){
					$options = $this->Log->_callRefineParams($params, array());

					$cntCompanies = $this->RmReport->callCompany($params, array(
						'find' => 'count',
					));

					$this->Log->virtualFields['cnt'] = 'COUNT(Log.id)';
					$this->Log->virtualFields['parent_cnt'] = 'COUNT(DISTINCT Log.parent_id)';

					$dataNull = false;
	
					foreach ($values as $key => &$aco) {
						$alias = Common::hashEmptyField($aco, 'Acos.alias');

						$aco['Activity'] = $this->Log->getData('first', array_merge_recursive($options, array(
							'fields' => array(
								'Log.cnt',
								'Log.parent_cnt',
							),
							'conditions' => array(
								'Log.model' => $alias,
							),
							'group' => array(
								'Log.model',
							),
						)), array(
							'activity' => true,
						));

						$values[$key] = $aco;

						if(!empty($aco['Activity'])){
							$dataNull = true;
						} else {
							$aco['Activity']['Log'] = array(
								'cnt' => 0,
								'parent_cnt' => 0,
							);
						}
					}

					if($sort){
						$sortLog = strpos($sort, 'Log.');
						$direction = Common::hashEmptyField($params, 'named.direction');

						if( is_numeric($sortLog) ){
							switch ($sort) {
								case 'Log.cnt':
									$values = Hash::sort($values, '{n}.Activity.Log.cnt', $direction);
									break;
								
								case 'Log.parent_cnt':
									$values = Hash::sort($values, '{n}.Activity.Log.parent_cnt', $direction);
									break;

								case 'Log.parent_notactive_cnt':
									$opposite = ($direction == 'asc') ? 'desc' : 'asc';
									$values = Hash::sort($values, '{n}.Activity.Log.parent_cnt', $opposite);
									break;
							}
						}
					}

					$values['cntActivity'] = $this->User->UserCompany->Log->getData('first', $options, array(
						'activity' => true,
					));
				}

				if(empty($dataNull)){
					unset($values);
				}

				if(!empty($direction)){
					switch ($direction) {
						case 'asc':
							$direction = 'desc';
							break;
						default:
							$direction = 'asc';
							break;
					}
				}

				$result = array(
					'list_data' => !empty($values) ? $values : array(),
					'date_from' => $date_from,
					'date_to' => $date_to,
					'year' => $year,
					'cntCompanies' => $cntCompanies,
					'company_ids' => $company_ids,
					'sort' => $sort,
					'direction' => !empty($direction) ? $direction : 'asc',
				);

			} else {
				$result = null;
			}

			$this->autoRender = false;

			if(!$direct_view){
				if( !empty($result) ) {
					return json_encode($result);
				} else {
					return null;
				}
			} else {
				$this->RmReport->_callBeforeViewJson($result, '/Elements/blocks/reports/dashboard/charts/chart_activity_page');
			}

		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_chart_on_time(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'csa',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('Log');

			$resultRows = array();
			$params = $this->params->params;
			$data = $this->request->data;

			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');

			$company_ids = Common::hashEmptyField($data, 'Search.company_id');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');

			$params['named'] = array_merge($params['named'], array(
				'date_from' => $date_from,
				'date_to' => $date_to,
			));

			if(!empty($company_ids)){
				$params['named']['company_id'] = $company_ids;
			}

			if( $page == 1 ) {
				$countCompany =  $this->RmReport->callCompany($params, array(
					'find' => 'count',
				));

				$days = array('0', '1', '2', '3', '4', '5', '6');

				$this->Log->virtualFields['cnt'] = 'COUNT(Log.id)';
				$this->Log->virtualFields['hour'] = 'DATE_FORMAT(Log.created, \'%H\')';
				$this->Log->virtualFields['parent_cnt'] = 'COUNT(DISTINCT Log.parent_id)';

				$max_val = 0;
				$isset = false;

				foreach ($days as $key => $day) {
					$default_options = array(
						'conditions' => array(
							'DATE_FORMAT(Log.created, \'%w\')' => $day
						),
						'group' => array(
							'DATE_FORMAT(Log.created, \'%H\')',
						),
						'order' => array(
							'Log.hour' => 'ASC'
						),
						'fields' => array(
							'Log.hour', 'Log.cnt', 'Log.parent_cnt'
						),
					);

					$options = $this->Log->_callRefineParams($params, $default_options);
					$values = $this->Log->getData('all', $options, array(
						'activity' => true,
					));
					$cnt_arr = Hash::Extract($values, '{n}.Log.cnt');

					if(!empty($values) && (max($cnt_arr) > $max_val)){

						$isset = true;
						$max_val = max($cnt_arr);
					}

					$resultRows[$day] = $values;
				}

				$resultRows = $this->RmReport->_callDurationTime($resultRows, $max_val);
				$rows = Common::hashEmptyField($resultRows, 'temp');
				$dividers = Common::hashEmptyField($resultRows, 'divider');

				$result = array(
					'list_data' => !empty($isset) ? $rows : array(),
					'dividers' => $dividers,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'year' => $year,
					'company_ids' => $company_ids,
					'countCompany' => $countCompany,
				);

			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function admin_company_detail(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'csa',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('Log');
			$params = $this->params->params;
			$title = __('Daftar Perusahaan');

			$slug = Common::hashEmptyField($params, 'named.slug');
			$date_to = Common::hashEmptyField($params, 'named.date_to');
			$date_from = Common::hashEmptyField($params, 'named.date_from');

			// pinjam function dari sini utk filter
			$param_search = array();
			$data = $this->request->data;

			if (!empty($data)) {
				$request_data = $this->RmReport->formatFilter($data);

				$param_search['Search'] = Common::hashEmptyField($request_data, 'Search');

				$date_from = Common::hashEmptyField($request_data, 'Search.date_from');
				$date_to   = Common::hashEmptyField($request_data, 'Search.date_to');

			}

			// debug($request_data);
			// refine params search form report
			$this->RmReport->reportRefineParams($params);
			
			$this->RmReport->_callAddBeforeView();
			$this->RmReport->_callAddBeforeViewGrowth();

			$values = $this->RmReport->_callBeforeCSA($param_search);
			// debug($values);die();

			$this->RmCommon->_layout_file(array(
				'report',
			));

			$url_form = array(
				'controller' => 'reports',
				'action'     => 'search_report',
				'company_detail',
				'slug'       => 'module-all',
				'admin'      => true,
			);

			$namedParams = $this->params->named;
			$export		 = $this->RmCommon->filterEmptyField($namedParams, 'export');
			
			$this->set(array(
				'request_data' => $param_search,
				'url_form' => $url_form,
				'values' => $values,
				'slug' => $slug,
				'date_to' => $date_to,
				'date_from' => $date_from,
				'active_menu' => 'csa_report',
				'module_title' => $title, 
				'title_for_layout' => $title,
				'params' => $params,
				'urlBack' => array(
					'controller' => 'reports',
					'action' => 'csa',
					'admin' => true,
				),
			));

			if($export == 'excel'){
				$this->layout = FALSE;
				$this->render('admin_company_detail_excel');
			}

		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function backprocess_chart_share(){
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$resultRows = array();
			$tips = $maxCount = false;
			$params = $this->params->params;

			$page = Common::hashEmptyField($params, 'named.page');
			$year = Common::hashEmptyField($params, 'named.year');
			$group_id = Common::hashEmptyField($params, 'named.group_id');

			$date 		= $this->RmCommon->getDateRangeReport();
			$date_from 	= Common::hashEmptyField($date, 'date_from');
			$date_to 	= Common::hashEmptyField($date, 'date_to');
			$cnt_data 	= 0;
			$top_data   = array();
			$temp_row   = array();
			$values 	= array();

			if( $page == 1 ) {
				$param_custom = array(
					'named' => array(
						'date_from' => $date_from,
						'date_to' => $date_to,
						'group_id' => $group_id,
					),
				);

				$this->RmCommon->_callRefineParams($params);
				$values = $this->RmReport->getShare($param_custom);

				$cnt_data = Common::hashEmptyField($values, 'cnt_data');

				$values = Common::_callUnset($values, array(
					'cnt_data',
				));

				if(!empty($values)){
					$temp = array();

					$top_val = 0;
					$top = 0;

					foreach ($values as $key => &$val) {
						$reference = Common::hashEmptyField($val, 'ShareLog.name');
						$cnt = Common::hashEmptyField($val, 'ShareLog.cnt', 0);

						if($cnt > $top){
							$top_data['name'] = $reference;
							$top_data['value'] = Common::_callTargetPercentage($cnt, $cnt_data);
							
							$top = $cnt;
						}

						if($reference){
							$resultRows[$reference][0] = __('%s', $reference);
							$resultRows[$reference][1] = intval($cnt);
						}
					}

					if( !empty($resultRows) || $page == 1 ) {
						if( !empty($resultRows) ) {

							$temp_row[] = array(
								__('Sosmed'),
								__('Aktivitas'),
							);

							foreach ($resultRows as $key => $value) {
								$temp_row[] = array(
									$value[0],
									$value[1],
								);
							}
						}
					}
				}

				$result = array(
					'autoload' => true,
					'activities' => $values,
					'cnt_data' => $cnt_data,
					'top_data' => $top_data,
					'date_from' => $date_from,
					'date_to' => $date_to,
					'group_id' => is_array($group_id)?implode(',', $group_id):$group_id,
					'year' => $year,
					'rows' => $temp_row,
				);
			} else {
				$result = null;
			}

			$this->autoRender = false;

			if( !empty($result) ) {
				return json_encode($result);
			} else {
				return null;
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function admin_share() {
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('ShareLog');
			$module_title = $title_for_layout = __('Laporan Aktivitas Share');
			$group_id = Configure::read('User.group_id');
			$agentCompany = Configure::read('__Site.Role.company_agent');
			$params = $this->params->params;
			$named = Common::hashEmptyField($params, 'named', array());

			if( in_array($group_id, $agentCompany) ) {
				$this->redirect(array_merge(array(
					'controller' => 'reports',
					'action' => 'share_detail',
					'admin' => true,
				), $named));
			} else {
				$this->ShareLog->virtualFields['cnt'] = 'COUNT(ShareLog.id)';
				$options = $this->ShareLog->_callRefineParams($params, array(
					'group' => array(
						'ShareLog.user_id',
						'ShareLog.sosmed',
					),
					'order' => array(
						'ShareLog.cnt' => 'DESC',
					),
				));
				$this->RmCommon->_callRefineParams($params);

				$this->paginate = $this->ShareLog->getData('paginate', $options, array(
					'mine' => false,
				));
				$values = $this->paginate('ShareLog');

				$this->RmReport->_callBeforeViewShareDetail($values);
				$this->set('active_menu', 'overview_report');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function admin_share_detail() {
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('ShareLog');
			$module_title = $title_for_layout = __('Laporan Aktivitas Share per User');

			$params = $this->params->params;
			$options = $this->ShareLog->_callRefineParams($params, array(
				'order' => array(
					'ShareLog.created' => 'DESC',
				),
				'group' => array(
					'ShareLog.document_id',
					'ShareLog.type',
					'ShareLog.sosmed',
				),
			));
			$this->RmCommon->_callRefineParams($params);

			$this->ShareLog->virtualFields['cnt'] = 'COUNT(ShareLog.id)';
			$this->paginate = $this->ShareLog->getData('paginate', $options);
			$values = $this->paginate('ShareLog');

			$this->RmReport->_callBeforeViewShareDetail($values);
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}

	function admin_share_detail_module() {
		$checkAcl = $this->RmCommon->_callCheckAclMultiple(array(
			array(
				'controller' => 'reports',
				'action' => 'kpi_marketing',
				'admin' => true,
				'backprocess' => false,
			),
			array(
				'controller' => 'reports',
				'action' => 'overview',
				'admin' => true,
				'backprocess' => false,
			),
		));

		if($checkAcl){
			$this->loadModel('ShareLog');
			$module_title = $title_for_layout = __('Laporan Aktivitas Share per User');

			$params = $this->params->params;
			$options = $this->ShareLog->_callRefineParams($params, array(
				'order' => array(
					'ShareLog.created' => 'DESC',
				),
			));
			$this->RmCommon->_callRefineParams($params);

			$this->paginate = $this->ShareLog->getData('paginate', $options);
			$values = $this->paginate('ShareLog');

			$this->RmReport->_callBeforeViewShareDetail($values);
		} else {
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak akses ke halaman tersebut'));
		}
	}
}
?>