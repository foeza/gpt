<?php
/**
 * TargetProjectSaleLog Model
 *
 * @property TargetProjectSaleLog $TargetProjectSaleLog
 * @property Project $Project
 * @property Company $Company
 * @property TargetProjectSaleLogDetail $TargetProjectSaleLogDetail
 */
class TargetProjectSaleLog extends AppModel {

	public $useTable = 'target_project_sales_logs'; 
	
/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'target_project_sales_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'target_project_sales id harus diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'target_project_sales id harus berupa angka',
			),
		),
		'old_data' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Data lama mohon diisi',
			),
		),
		'new_data' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Data baru mohon diisi',
			),
		),
		'reason_change' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Data baru mohon diisi',
			),
		),
	);

	public $belongsTo = array(
		'TargetProjectSale' => array(
			'className' => 'TargetProjectSale',
			'foreignKey' => 'target_project_sales_id',
			'dependent' => false,
			'counterCache' => 'target_project_sale_log_count'
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'dependent' => false,
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'conditions'=> array(),
			'fields' => array(), 
			'contain' => array(), 
			'joins'	=> array(),
			'order' => array(),
			'limit' => 20, 
		);

		return $this->merge_options($default_options, $options, $find);
	}
}