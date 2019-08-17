<?php
class TargetRevenueController extends AppController {
	
	public $uses = array('TargetProjectSale');

	public $components = array(
		'RmTarget',
	);

	/**
	 * beforeFitler
	 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->set('active_menu', 'target_revenue');
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

	function admin_index(){
		$params = $this->params->params;

		$this->RmCommon->_callRefineParams($params);

		$options =  $this->TargetProjectSale->_callRefineParams($params, array(
			'order' => array(
				'TargetProjectSale.created' => 'DESC'
			)
		));

		$status = Common::hashEmptyField($params, 'named.status');

		$this->paginate = $this->TargetProjectSale->getData('paginate', $options, array(
			'status' => $status
		));

		$values = $this->paginate('TargetProjectSale');

		$values = $this->TargetProjectSale->getMergeList($values, array(
			'contain' => array(
				'Currency'
			)
		));

		$this->set(array(
			'values' => $values,
		));

		$title = __('Target Penjualan');

		$this->set(array(
			'module_title' => $title,
			'title_for_layout' => $title,
		));
	}

	function admin_add(){
		$this->RmTarget->_callBeforeSaveTargetSales();

		$title = __('Tambah Target Penjualan');

		$_breadcrumb = array(
			'sub_button' => array(
				'text' => __('Batal'),
				'url' => array(
					'action' => 'target_sales',
					'admin' => true,
				),
			),
			'main_button' => array(
				'text' => __('Simpan'),
			),
		);

		$this->set(array(
			'module_title' => $title,
			'title_for_layout' => $title,
		));

		$this->render('admin_form');
	}

	function admin_edit($id = null) {
		if (!$this->TargetProjectSale->exists($id)) {
			throw new NotFoundException(__('Invalid TargetProjectSale'));
		}else{
			$value = $this->TargetProjectSale->getData('first', array(
				'conditions' => array(
					'TargetProjectSale.id' => $id
				)
			));

			$value = $this->TargetProjectSale->getMergeList($value, array(
				'contain' => array(
					'TargetProjectSaleDetail',
				)
			));

			$data_history = $this->TargetProjectSale->TargetProjectSaleLog->find('first', array(
				'conditions' => array(
					'TargetProjectSaleLog.target_project_sales_id' => $id
				),
				'order' => array(
					'TargetProjectSaleLog.id' => 'DESC'
				)
			));

			$data_history = $this->TargetProjectSale->TargetProjectSaleLog->getMergeList($data_history, array(
				'contain' => array(
					'User'
				)
			));

			if(empty($value)){
				$this->RmCommon->redirectReferer(__('Target penjualan tidak ditemukan.'));
			}

			$this->RmTarget->_callBeforeSaveTargetSales($value, $id);
		}

		$title = __('Edit Target Penjualan');

		$_breadcrumb = array(
			'sub_button' => array(
				'text' => __('Batal'),
				'url' => array(
					'action' => 'target_sales',
					'admin' => true,
				),
			),
			'main_button' => array(
				'text' => __('Simpan'),
			),
		);

		$this->set(array(
			'data_history' => $data_history,
			'module_title' => $title,
			'title_for_layout' => $title,
			'value' => $value,
			'current_id' => $id
		));

		$this->render('admin_form');
	}

	function admin_delete($id = null) {
		$data = $this->request->data;
		$id = Common::hashEmptyField($data, 'Search.id');

    	$result = $this->TargetProjectSale->doDelete( $id );
		$this->RmCommon->setProcessParams($result, false, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
			'redirectError' => true,
		));
	}

	public function admin_info( $id = null ) {
		$value = $this->TargetProjectSale->getData('first', array(
			'conditions' => array(
				'TargetProjectSale.id' => $id
			)
		));

		if(!empty($value)){
			$value = $this->TargetProjectSale->getMergeList($value, array(
				'contain' => array(
					'Currency',
					'TargetProjectSaleDetail',
				)
			));

			$year_period = Common::hashEmptyField($value, 'TargetProjectSale.year_period');

			$data_history = $this->TargetProjectSale->TargetProjectSaleLog->find('all', array(
				'conditions' => array(
					'TargetProjectSaleLog.target_project_sales_id' => $id
				),
				'order' => array(
					'TargetProjectSaleLog.id' => 'DESC'
				)
			));

			$data_history = $this->TargetProjectSale->TargetProjectSaleLog->getMergeList($data_history, array(
				'contain' => array(
					'User' => array(
						'contain' => array(
							'Group'
						)
					)
				)
			));

			$title = __('Informasi Target Penjualan Tahun %s', $year_period);
			
			$this->set(array(
				'data_history' => $data_history,
				'id' => $id,
				'current_id' => $id,
				'module_title' => $title,
				'title_for_layout' => $title,
				'value' => $value,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Target penjualan tidak ditemukan.'));
		}
	}
}
