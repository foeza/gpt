<?php
/**
 * TargetProjectSale Model
 *
 * @property TargetProjectSale $TargetProjectSale
 * @property Project $Project
 * @property Company $Company
 * @property TargetProjectSaleDetail $TargetProjectSaleDetail
 */
class TargetProjectSale extends AppModel {

	public $useTable = 'target_project_sales'; 
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'company_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'company id harus diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'company id harus berupa angka',
			),
		),
		'year_period' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Target tahun mohon diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Target tahun harus berupa angka',
			),
			'validateYear' => array(
				'rule' => array('validateYear'),
			),
		),
		'target_revenue' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Target per 1 tahun mohon diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Target penjualan harus berupa angka',
			),
			'validateTarget' => array(
				'rule' => array('validateTarget'),
				'message' => 'Target per 1 tahun mohon diisi',
			)
		),
	);

	public $hasMany = array(
		'TargetProjectSaleDetail' => array(
			'className' => 'TargetProjectSaleDetail',
			'foreignKey' => 'target_project_sales_id',
			'dependent' => false,
		),
		'TargetProjectSaleLog' => array(
			'className' => 'TargetProjectSaleLog',
			'foreignKey' => 'target_project_sales_id',
			'dependent' => false,
		),
	);

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Currency' => array(
			'className' => 'Currency',
			'foreignKey' => 'currency_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	function validateYear($data){
		$result = true;
		$global_data = $this->data;
		$company_id = Configure::read('Config.Company.data.UserCompany.user_id');

		$id 			= Common::hashEmptyField($global_data, 'TargetProjectSale.id');
		$year_period 	= (int) Common::hashEmptyField($global_data, 'TargetProjectSale.year_period');

		$message = '';
		if(!empty($year_period)){
			$current_year = (int) date('Y');

			if($year_period < $current_year){
				$result = false;

				$message = __('Tahun target penjualan tidak boleh lebih kecil dari tahun ini.');
			}
			
			if($result == true){
				
				$conditions = array(
					'TargetProjectSale.year_period' => $year_period
				);

				if(!empty($id)){
					$conditions['TargetProjectSale.id <>'] = $id;
				}

				$data_year_period = $this->getData('first', array(
					'conditions' => $conditions
				));

				if(!empty($data_year_period)){
					$result = false;

					$message = __('Tahun target penjualan ini sudah pernah dimasukkan sebelumnya.');
				}
			}
		}



		if(!empty($message)){
			$this->validator()->getField('year_period')->getRule('validateYear')->message = $message;
		}

		return $result;
	}

	function validateTarget($data){
		$result = true;
		$global_data = $this->data;

		$target_revenue = Common::hashEmptyField($global_data, 'TargetProjectSale.target_revenue');
		
		if( empty($target_revenue) ){
			$result = false;
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ){
		$modified_from = Common::hashEmptyField($data, 'named.modified_from', false, array(
            'addslashes' => true,
        ));
        $modified_to = Common::hashEmptyField($data, 'named.modified_to', false, array(
            'addslashes' => true,
        ));
    	$date_from = Common::hashEmptyField($data, 'named.date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = Common::hashEmptyField($data, 'named.date_to', false, array(
            'addslashes' => true,
        ));

        if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(TargetProjectSale.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(TargetProjectSale.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}

		if( !empty($date_from) ) {
        	$default_options['conditions']["DATE_FORMAT(TargetProjectSale.created, '%Y-%m-%d') >="] = $date_from;
	        
			if( !empty($date_to) ) {
	        	$default_options['conditions']["DATE_FORMAT(TargetProjectSale.created, '%Y-%m-%d') <="] = $date_to;
	        }
        }

        $default_options = $this->defaultOptionParams($data, $default_options, array(
			'year_period' => array(
				'field'=> 'TargetProjectSale.year_period',
				'type' => 'like',
			),
			'target_revenue' => array(
				'field'=> 'TargetProjectSale.target_revenue',
				'type' => 'like',
			),
			'target_listing' => array(
				'field'=> 'TargetProjectSale.target_listing',
				'type' => 'like',
			),
			'target_ebrosur' => array(
				'field'=> 'TargetProjectSale.target_ebrosur',
				'type' => 'like',
			),
		));

		return $default_options;
	}


	function getData( $find = 'all', $options = array(), $elements = array() ){
    	$status = Common::hashEmptyField($elements, 'status');
    	$company = Common::hashEmptyField($elements, 'company', true, array(
    		'isset' => true,
		));

    //	$company_id = (int) Configure::read('Global.Data.Project.company_id');
    	$company_id = Configure::read('Config.Company.data.UserCompany.user_id');

		$default_options = array(
			'conditions'=> array(
				'TargetProjectSale.status' => 1
			),
			'fields' => array(), 
			'contain' => array(), 
			'joins'	=> array(),
			'order' => array(),
			'limit' => 20, 
		);

		if($company == true && $status != 'all_data_company'){
			$default_options['conditions']['TargetProjectSale.company_id'] = $company_id;
		}

		return $this->merge_options($default_options, $options, $find);
	}

	public function doSave($data, $id = false){
		$result = false;

		if ( !empty($data) ) {

			if(!empty($id)){
				$this->validator()->add('reason_change', array(
					'notempty' => array(
						'rule' => array('notempty'),
						'message' => 'Mohon masukkan alasan perubahan',
					),
				));
			}

			$flag = $this->saveAll($data, array(
                'validate' => 'only',
                'deep' => true
            ));
            
			if( $flag ) {

				$validateYearTarget = $this->validateYearTarget($data, $id);

				$status_target_project_sales 	= Common::hashEmptyField($validateYearTarget, 'status');
				$data_target_project_sales 		= Common::hashEmptyField($validateYearTarget, 'data');

				if( $status_target_project_sales == 'success' ) {

					$flag = $this->saveAll($data, array(
		            	'deep' => true
		            ));

					if($flag){
						$msg = __('Berhasil menyimpan informasi target penjualan');
						if(!empty($id)){
							$msg = __('Berhasil menyimpan perubahan informasi target penjualan');
						}
						
						$id = $this->id;

						$result = array(
							'msg' => $msg,
							'status' => 'success',
							'id' => $id,
							'Log' => array(
								'activity' => $msg,
								'document_id' => $id,
							),
						);
					}else{
						$msg = __('Gagal menyimpan target penjualan. Mohon masukkan data-data yang dibutuhkan');
						$result = array(
							'msg' => $msg,
							'status' => 'error',
						);
					}
				}else{
					$msg = __('Gagal menyimpan target penjualan. Mohon masukkan data-data yang dibutuhkan');
					if($status_target_project_sales == 'error'){
						$msg = __('Gagal menyimpan target penjualan. sudah ada target penjualan di tahun tersebut');	
					}

					$result = array(
						'msg' => $msg,
						'status' => 'error',
					);
				}
			}else{
				$result = array(
					'msg' => __('Gagal menyimpan target penjualan. Mohon masukkan data-data yang dibutuhkan'),
					'status' => 'error',
					'data' => $data,
					'validationErrors' => $this->validationErrors,
				);
			}
		}

		return $result;
	}

	function doDelete( $id ) {
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'TargetProjectSale.id' => $id,
			),
		);

		$value = $this->getData('all', $options);

		if ( !empty($value) ) {
			$default_msg = 'menghapus target penjualan';

			$flag = $this->updateAll(array(
				'TargetProjectSale.status' => 0,
			), array(
				'TargetProjectSale.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $id,
					),
				);
            } else {
				$result = array(
					'msg' => sprintf(__('Gagal %s'), $default_msg),
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => sprintf(__('Gagal %s. Data tidak ditemukan'), $default_msg) ,
				'status' => 'error',
			);
		}

		return $result;
	}

	function validateYearTarget($data, $id = false){
		$year_period = Common::hashEmptyField($data, 'TargetProjectSale.year_period');

		$result = 'success';
		$data_year = array();
		if(!empty($year_period)){
			$conditions = array(
				'TargetProjectSale.year_period' => $year_period
			);

			if(!empty($id)){
				$conditions['TargetProjectSale.id <>'] = (int) $id;
			}

			$data_year = $check_data = $this->getData('first', array(
				'conditions' => $conditions
			), array(
				'company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			));

			if(!empty($check_data)){
				$result = 'error';
			}
		}

		return array(
			'status' => $result,
			'data' => $data_year
		);
	}

	function getTarget($year, $quarter = false, $first_target = false){
		$this->unbindModel(array(
			'hasMany' => array(
				'TargetProjectSaleDetail', 
			), 
		));

		$this->bindModel(array(
			'hasOne' => array(
				'TargetProjectSaleDetail' => array(
					'foreignKey' => 'target_project_sales_id', 
				), 
			), 
		), false);

		$conditions = array(
			'TargetProjectSale.year_period' => $year,
		);

		if(!empty($quarter) && $quarter != 12){
			$conditions['OR'] = array(
				'TargetProjectSaleDetail.target_revenue <>' => 0,
				'TargetProjectSaleDetail.target_listing <>' => 0,
			);

			$conditions['TargetProjectSaleDetail.month_target'] = $quarter;
		}else{
			$conditions['TargetProjectSaleDetail.month_target <>'] = array(1,3,6);
		}

		$result = $this->getData('first', array(
			'conditions' => $conditions,
			'contain' => array(
				'TargetProjectSaleDetail' => array(
					'order' => array(
						'TargetProjectSaleDetail.month_target' => 'ASC'
					)
				)
			)
		), array(
			'company' => true,
			'status' => 'active'
		));

		$this->unbindModel(array(
			'hasOne' => array(
				'TargetProjectSaleDetail', 
			), 
		));

		$this->bindModel(array(
			'hasMany' => array(
				'TargetProjectSaleDetail' => array(
					'foreignKey' => 'target_project_sales_id', 
				), 
			), 
		), false);

		if(empty($result) && $first_target == 'other-options'){
			$this->unBindModel(array(
	            'hasOne' => array(
	                'TargetProjectSaleDetail'
	            ),
	        ));

	        $this->bindModel(array(
				'hasMany' => array(
					'TargetProjectSaleDetail' => array(
						'foreignKey' => 'target_project_sales_id', 
					), 
				), 
			), false);

			$result = $this->getData('first', array(
				'conditions' => array(
					'TargetProjectSale.year_period' => $year
				),
				'contain' => array(
					'TargetProjectSaleDetail' => array(
						'order' => array(
							'TargetProjectSaleDetail.month_target' => 'ASC'
						)
					)
				)
			), array(
				'company' => true,
				'status' => 'active'
			));

			$TargetProjectSale = Common::hashEmptyField($result, 'TargetProjectSale');
			$TargetProjectSaleDetail = Common::hashEmptyField($result, 'TargetProjectSaleDetail');

			if(!empty($TargetProjectSale) && !empty($TargetProjectSaleDetail)){
				$result_reference =& $result['TargetProjectSaleDetail'];
				
				$any_detail = false;
				foreach ($TargetProjectSaleDetail as $key => $value) {
					$target_revenue = (float) Common::hashEmptyField($value, 'target_revenue');
					$target_listing 	= (float) Common::hashEmptyField($value, 'target_listing');

					if(!empty($target_revenue) || !empty($target_listing)){
						$result_reference = $value;
						$any_detail = true;
						break;
					}
				}

				if(!$any_detail){
					unset($result['TargetProjectSaleDetail']);
				}
			}
		}

		return $result;
	}
}