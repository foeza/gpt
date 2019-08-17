<?php

App::uses('AppController', 'Controller');

class ProjectsController extends AppController {
	public $uses = array(
		'ApiAdvanceDeveloper',
		'ApiRequestDeveloper',
	);

	public $components = array(
		'RmCommon', 'RmProject', 'RmImage', 'RmRecycleBin', 'RmBooking',
		'Rest.Rest' => array(
			'actions' => array(
	            'api_get_data_request_project' => array(
	            	'extract' => array(
	                	'paging', 'data'
	                ),
	            )
			),
            'debug' => 2,
        ),
	);
	public $helpers = array(
		'FileUpload.UploadForm'
	);

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array(
			'get_update_status_projects', 'get_projects_from_primedev', 'get_result_request_primedev'
		));
	}

	public function get_update_status_projects() {
		$params = $this->params;
		$this->RmProject->getDataUpdateProjects();
	}

	public function get_projects_from_primedev() {
		$params = $this->params;
		$this->RmProject->getDataProjects();
	}

	public function get_result_request_primedev() {
		$params = $this->params;
		$this->RmProject->getDataResultRequest();
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

	public function admin_index() {
		$parent_id = $this->parent_id;
		$is_super_admin = Configure::read('User.Admin.Rumahku');
		$this->RmCommon->_callRefineParams($this->params);
		$params = $this->params->params;

		$options = array(
			'conditions' => array(
				'ApiAdvanceDeveloper.active' => 1,
				'ApiAdvanceDeveloper.status' => 1,
				'ApiAdvanceDeveloper.type_developer' => 'project_primedev',
        		'ApiAdvanceDeveloper.end_date >=' => date('Y-m-d'),
        		'ApiAdvanceDeveloper.id <>' => 965,
			),
		);

		$data_projects = $this->RmProject->_callBeforeViewProjects( $options, array(
			'status' => 'active',
		));

		$this->set('module_title', __('Daftar Project'));
		$this->set('active_menu', 'developer_list');
		$this->set(compact(
			'data_projects', 'parent_id', 'is_super_admin'
		));
	}

	// S: detail project
	public function admin_detail($id = false) {
		if (!empty($id)) {
			$detail_project = $this->ApiAdvanceDeveloper->getData('first', array(
				'conditions' => array(
					'ApiAdvanceDeveloper.id' => $id,
				),
			));

			$detail_project = $this->ApiAdvanceDeveloper->getMergeList($detail_project, array(
	            'contain' => array(
	            	'ApiAdvanceDeveloperCompany',
	            ),
	        ));

	        // merge region, city, parentCompany
			$detail_project = $this->ApiAdvanceDeveloper->getMerge($detail_project, true);
		} else {
			$detail_project = __('Maaf, detail proyek tidak ditemukan.');
		}

		$this->set(compact(
			'detail_project'
		));
	}
	// E: detail project

	// S: request project
	public function admin_request_project($id = false) {
		$data_company_applicant = $this->data_company;
		$user_id = $this->user_id;
		if (!empty($id)) {
			$data_project = $this->ApiAdvanceDeveloper->getData('first', array(
				'conditions' => array(
					'ApiAdvanceDeveloper.original_id' => $id,
					'ApiAdvanceDeveloper.type_developer' => 'project_primedev',
				),
			));

			if (!empty($data_project)) {
				$data_project = $this->User->getMerge($data_project, $user_id, true);
				$data_project = $this->ApiAdvanceDeveloper->getMergeList($data_project, array(
		            'contain' => array(
		            	'ApiAdvanceDeveloperCompany',
		            ),
		        ));

		        // merge region, city, parentCompany
				$data_project = $this->ApiAdvanceDeveloper->getMerge($data_project, true);
			}

			if( !empty($this->request->data) ) {
				$data_request = $this->request->data;
				$dataFormat = $this->RmProject->_callFormatSaveForm($data_project, $data_company_applicant, $data_request);

				$result = $this->ApiRequestDeveloper->doSave( $dataFormat );
				
				if(!empty($result['status']) && $result['status'] == 'success' && !empty($result['id'])){
					$this->set('_flash', false);
				}		

				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
				));
			}

			$this->set(compact(
				'data_project'
			));
		}
	}
	// E: request project

	public function admin_cancel_request_project($id = false) {
		$data_request = $this->ApiRequestDeveloper->getData('first', array(
			'conditions' => array(
				'ApiRequestDeveloper.id' => $id,
			),
		));

		$id_request = $this->RmCommon->filterEmptyField($data_request, 'ApiRequestDeveloper', 'id');

		if( !empty($this->request->data) ) {
			$data = $this->request->data;
			$result = $this->ApiRequestDeveloper->_cancelRequest( $data, $id_request );
			$this->RmCommon->setProcessParams($result, false, array(
				'ajaxFlash' => true,
			));
		}
	}

	public function admin_list_request() {
		$data_company = $this->data_company;

		$this->RmCommon->_callRefineParams($this->params);
		$params = $this->params->params;

		$principle_id = Common::hashEmptyField($data_company, 'User.id');
		$date_now = date('Y-m-d');

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

		$options = array(
			'conditions' => array(
				'ApiAdvanceDeveloper.active' => 1,
				'ApiAdvanceDeveloper.status' => 1,
				'ApiRequestDeveloper.principle_id' => $principle_id,
				// 'OR' => array(
				// 	array(
				// 		'ApiRequestDeveloper.end_date >=' => $date_now,
				// 	),
				// 	array(
				// 		'ApiRequestDeveloper.end_date' => null,
				// 	),
				// ),
			),
			'contain' => array(
				'ApiRequestDeveloper',
			),
			'fields' => array('ApiRequestDeveloper.*', 'ApiAdvanceDeveloper.*'),
		);

		$data_request_projects = $this->RmProject->_callBeforeViewRequest($options);

		$this->set('module_title', __('My Request'));
		$this->set('active_menu', 'request_list');
		$this->set(compact(
			'data_request_projects'
		));
	}

	public function api_get_data_request_project() {
		$params = $this->params->query;
		$passkey = $this->RmCommon->filterEmptyField($params, 'passkey');
		$lastupdated = $this->RmCommon->filterEmptyField($params, 'lastupdated', false, false, array(
			'type' => 'trailing_slash',
		));

		$options = $this->ApiRequestDeveloper->_callRefineParams($this->params, array(
			'conditions' => array(
				'ApiRequestDeveloper.status_request' => 'pending',
			),
			// 'limit' => 10,
			'order' => array(
				'ApiRequestDeveloper.modified' => 'ASC',
			),
		));

		if( !empty($lastupdated) ) {
			$options['conditions']['ApiRequestDeveloper.modified >'] = $lastupdated;
		}

		$data_request = $this->ApiRequestDeveloper->getData('all', $options);

		Configure::write('Rest.token', true);
		$this->RmCommon->_callDataForAPI($data_request, 'manual');

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	// S: Old developer goes here ----
    public function admin_developers(){
    	$parent_id = Configure::read('Principle.id');
		$module_title = __('Developer');
		$this->loadModel('ApiAdvanceDeveloper');

		$options =  $this->ApiAdvanceDeveloper->_callRefineParams($this->params, array(
			'conditions' => array(
				'ApiAdvanceDeveloper.type_developer' => 'old_data',
				'ApiAdvanceDeveloper.user_id' => $parent_id,
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
			'order' => array(
				'ApiAdvanceDeveloper.order' => 'ASC',
				'ApiAdvanceDeveloper.created' => 'DESC',
			),
		));
		$this->RmCommon->_callRefineParams($this->params);

        $this->paginate = $this->ApiAdvanceDeveloper->getData('paginate', $options, array(
        	'company' => true,
        ));
        // debug($this->paginate);die();
		$values = $this->paginate('ApiAdvanceDeveloper');
		
		$this->set('active_menu', 'developer');
		$this->set(compact(
			'values', 'module_title'
		));
	}

	public function admin_add_developer() {

		$module_title = __('Tambah Developer');
    	$urlRedirect = array(
            'controller' => 'projects',
            'action' => 'developers',
            'admin' => true
        );

    	$user_id = $this->user_id;
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id
			),
		));

		if( !empty($user) ) {
			$this->loadModel('ApiAdvanceDeveloper');
			$data = $this->request->data;
			$save_path = Configure::read('__Site.general_folder');
			
			$data = $this->RmImage->_uploadPhoto( $data, 'ApiAdvanceDeveloper', 'logo', $save_path );
			$data = $this->RmCommon->_callBeforeSaveBanner($data, 'ApiAdvanceDeveloper');

			$result = $this->ApiAdvanceDeveloper->doSaveBannerDeveloper( $data );
			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->request->data = $this->RmCommon->_callBeforeRenderBanner($this->request->data, 'ApiAdvanceDeveloper');

			$this->RmCommon->_layout_file(array(
				'ckeditor',
			));
			
			$this->set(array(
				'module_title' => $module_title,
				'active_menu' => 'developer',
			));
			
			$this->render('developer_form');
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

    public function admin_edit_developer( $banner_id ) {
        $module_title = __('Edit Developer');
        $urlRedirect = array(
            'controller' => 'projects',
            'action' => 'developers',
            'admin' => true
        );

        $this->loadModel('ApiAdvanceDeveloper');
        $banner = $this->ApiAdvanceDeveloper->getData('first', array(
        	'conditions' => array(
				'ApiAdvanceDeveloper.id' => $banner_id,
			),
		), array(
			'company' => true,
		));

		if( !empty($banner) ) {
		//	capture old photo, image file has to be deleted when new image file uploaded
			$oldPhoto 	= $this->RmCommon->filterEmptyField($banner, 'ApiAdvanceDeveloper', 'logo');
			
			$data		= $this->request->data;
			$save_path	= Configure::read('__Site.general_folder');

			$data		= $this->RmImage->_uploadPhoto( $data, 'ApiAdvanceDeveloper', 'logo', $save_path );
			$data		= $this->RmCommon->_callBeforeSaveBanner($data, 'ApiAdvanceDeveloper');
			$result		= $this->ApiAdvanceDeveloper->doSaveBannerDeveloper( $data, $banner, $banner_id );

		//	if user upload new photo, delete old photo
			if(isset($this->request->data['ApiAdvanceDeveloper']['logo']['name'])){
				$uploadPhoto = $this->request->data['ApiAdvanceDeveloper']['logo']['name'];

				if($uploadPhoto && $oldPhoto && $result['status'] == 'success'){
					$permanent = FALSE;
					$isDeleted = $this->RmRecycleBin->delete($oldPhoto, $save_path, NULL, $permanent);
				}
			}

			$this->RmCommon->setProcessParams($result, $urlRedirect);

			$this->request->data = $this->RmCommon->_callBeforeRenderBanner($this->request->data, 'ApiAdvanceDeveloper');

			$this->RmCommon->_layout_file(array(
				'ckeditor',
			));
			$this->set('active_menu', 'developer');
			$this->set(compact(
				'module_title'
			));
			$this->render('developer_form');
		} else {
			$this->RmCommon->redirectReferer(__('Banner developer tidak ditemukan'));
		}
    }

	public function admin_delete_multiple_developer() {
		$data = $this->request->data;
		$id = $this->RmCommon->filterEmptyField($data, 'ApiAdvanceDeveloper', 'id');

		$this->loadModel('ApiAdvanceDeveloper');
    	$result = $this->ApiAdvanceDeveloper->doDeleteOldBanner( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'redirectError' => true,
		));
    }

    function admin_bookings(){
    	$this->RmCommon->_callRefineParams($this->params);

		$isAdmin = Configure::read('User.admin');

		if(empty($isAdmin)){
		//	https://basecamp.com/1789306/projects/10415456/todos/368530755
		//	view dibatas by sales yang ngajuin
			$this->request->params['named']['api_requester'] = Configure::read('User.data.email');
		}

		$link = 'transactions/booking_list';
		$link = $this->RmCommon->generateParamsApi($link);

		$records = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
            ),
        ));

        $values = Common::hashEmptyField($records, 'data', array());

        if( !empty($values) ) {
        	foreach ($values as $key => &$value) {
				$invoice_number = Common::hashEmptyField($value, 'invoice');
				$value = $this->User->Kpr->InvoiceCollector->getMerge($value, $invoice_number);
        	}
        }

		$this->set(array(
			'records' 		=> $values,
			'paging' 		=> Common::hashEmptyField($records, 'paging'),
			'active_menu'	=> 'booking_list',
			'module_title'	=> __('Booking List'),
		));

		$this->RmBooking->getListProject('approved');
    }

    function admin_add_booking($project_id = false){
    	$this->RmBooking->saveFastBooking($project_id);

    	$this->set('active_menu', 'booking_list');
    	$this->set('module_title', __('Tambah Booking'));
    	$this->set(array(
    		'project_id' 	=> $project_id,
			'layout_css'	=> array(
				'booking/admin_booking'
			)
		));
    }

	function admin_invoice($invoice_number = null){
		$this->loadModel('InvoiceCollector');

		$invoice_data = $this->InvoiceCollector->getData('first', array(
			'conditions' => array(
				'InvoiceCollector.invoice_number' => $invoice_number
			)
		));

		if(!empty($invoice_data)){
			$isAdmin		= Configure::read('User.admin');
			$project_id 	= Common::hashEmptyField($invoice_data, 'InvoiceCollector.project_id');
			$invoice_number	= Common::hashEmptyField($invoice_data, 'InvoiceCollector.invoice_number');
			$booking_code	= Common::hashEmptyField($invoice_data, 'InvoiceCollector.booking_code');

			if(empty($isAdmin)){
			//	https://basecamp.com/1789306/projects/10415456/todos/368530755
			//	view dibatas by sales yang ngajuin
				$this->request->params['named']['api_requester'] = Configure::read('User.data.email');
			}

			$link = 'transactions/invoice/'.$invoice_number;
			$link = $this->RmCommon->generateParamsApi($link);

			$record = $this->RmCommon->getAPI($link, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $project_id,
                    ),
                ),
            ));

			$title = __('Invoice');

			$this->set(array(
				'booking_code'		=> $booking_code,
				'record'			=> $record,
				'layout_css'		=> array(
					'booking/admin_booking'
				)
			));
			$this->set('module_title', __('Invoice'));
			$this->set('active_menu', 'invoice_booking');
		}else{
			$this->RmCommon->redirectReferer(__('Invoice tidak ditemukan'));
		}
	}

	// E: Old developer goes here ----

	function admin_confirm_transfer($invoice_number){
		$this->loadModel('InvoiceCollector');

		$reqRef =& $this->request->data;

		$invoice_data = $this->InvoiceCollector->getData('first', array(
			'conditions' => array(
				'InvoiceCollector.invoice_number' => $invoice_number
			)
		));

		if(!empty($invoice_data)){
			$project_id 	= Common::hashEmptyField($invoice_data, 'InvoiceCollector.project_id');
			$invoice_number = Common::hashEmptyField($invoice_data, 'InvoiceCollector.invoice_number');

			/*proses konfirmasi*/
			$this->RmBooking->confirmTransfer($project_id, $invoice_number);

			/*BEGIN - support data*/
			$link 	= 'transactions/invoice/'.$invoice_number;

			$record = $this->RmCommon->getAPI($link, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $project_id,
                    ),
                ),
            ));
			$record = Common::hashEmptyField($record, 'data');

			$this->RmBooking->getBanks($project_id);

			if(empty($this->request->data['Booking']) && !empty($record)){
				$total_amount 		= Common::hashEmptyField($record, 'OrderPayment.total_amount');
				$payment_datetime 	= Common::hashEmptyField($record, 'OrderPayment.payment_datetime');
				$user_name 			= Common::hashEmptyField($record, 'OrderPaymentProfile.user_name');
				
				$reqRef = Hash::insert($reqRef, 'Booking.total_transfer', $total_amount);
				$reqRef = Hash::insert($reqRef, 'Booking.name_account', $user_name);

				if(!empty($payment_datetime)){
					$payment_datetime = date('d/m/Y H:i', strtotime($payment_datetime));

					$reqRef = Hash::insert($reqRef, 'Booking.date_transfer', $payment_datetime);
				}
			}
			/*END - support data*/

			$title = __('Invoice');

			$this->set(array(
				'record' => $record
			));

			$this->set('module_title', __('Invoice'));
			$this->set('active_menu', 'developer_list');
		}else{
			$this->RmCommon->redirectReferer(__('Invoice tidak ditemukan'));
		}
	}

	function admin_abandoned_bookings(){

		$this->RmCommon->_callRefineParams($this->params);
    	
    	$link 	= 'transactions/abandoned_booking';
    	$link 	= $this->RmCommon->generateParamsApi($link);

		$records = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
            ),
        ));

		$this->RmBooking->getListProject();

		$this->set(array(
			'records' 		=> Common::hashEmptyField($records, 'data'),
			'active_menu'	=> 'abandoned_bookings',
			'module_title'	=> __('Abandoned Booking List'),
		));
	}

	function admin_detail_abandoned($booking_code){
		$this->admin_detail_booking($booking_code);

		$this->set(array(
			'active_menu'	=> 'abandoned_bookings',
			'module_title'	=> __('Detail Abandoned Booking'),
		));

		$this->render('admin_detail_booking');
	}

	function admin_detail_booking($booking_code){
		$this->RmCommon->_callRefineParams($this->params);
    	
    	$link 	= 'transactions/detail_order/'.$booking_code;

		$records = $this->RmCommon->getAPI($link, array(
            'header' => array(
                'slug' => 'primedev-api',
            ),
        ));
        $data_order = Common::hashEmptyField($records, 'data');

        if( !empty($data_order) ) {
			$this->set(array(
				'data_order' 	=> $data_order,
				'gallery' 		=> Common::hashEmptyField($records, 'gallery'),
				'active_menu'	=> 'booking_list',
				'module_title'	=> __('Detail Booking'),
				'layout_css'	=> array(
					'booking/admin_booking'
				)
			));
		} else {
			$this->RmCommon->redirectReferer(__('Booking tidak ditemukan'));
		}
	}
}

?>