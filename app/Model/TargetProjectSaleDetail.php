<?php
/**
 * TargetProjectSaleDetail Model
 *
 * @property TargetProjectSaleDetail $TargetProjectSaleDetail
 * @property Project $Project
 * @property Company $Company
 * @property TargetProjectSaleDetailDetail $TargetProjectSaleDetailDetail
 */
class TargetProjectSaleDetail extends AppModel {

	public $useTable = 'target_project_sales_details'; 
	
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
		'month_target' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Target periode mohon diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Target periode harus berupa angka',
			),
			'validatePeriodValidate' => array(
				'rule' => array('validatePeriodValidate'),
				'message' => 'Target periode sudah pernah ada sebelumnya',
			)
		),
	);

	public $belongsTo = array(
		'TargetProjectSale' => array(
			'className' => 'TargetProjectSale',
			'foreignKey' => 'target_project_sales_id',
			'dependent' => false,
		),
	);

	public $hasMany = array(
		'TargetProjectSaleDetailReport' => array(
			'className' => 'TargetProjectSaleDetailReport',
			'foreignKey' => 'target_project_sales_detail_id',
			'dependent' => false,
		)
	);

	function validatePeriodValidate($data){
		$result = true;
		$global_data = $this->data;

		$month_target 				= Common::hashEmptyField($data, 'month_target');
		$target_project_sales_id 	= Common::hashEmptyField($global_data, 'TargetProjectSaleDetail.target_project_sales_id');
		$id = Common::hashEmptyField($global_data, 'TargetProjectSaleDetail.id');
		
		if( !empty($target_project_sales_id) && !empty($month_target) ){
			$conditions = array(
				'TargetProjectSaleDetail.target_project_sales_id' => $target_project_sales_id,
				'TargetProjectSaleDetail.month_target' => $month_target,
			);

			if(!empty($id)){
				$conditions['TargetProjectSaleDetail.id <>'] = $id;
			}

			$data_detail = $this->getData('first', array(
				'conditions' => $conditions
			));

			if(!empty($data_detail)){
				$result = false;
			}
		}

		return $result;
	}


	function getData( $find = 'all', $options = array(), $elements = array() ){
		$status = Common::hashEmptyField($elements, 'status');
    	$company = Common::hashEmptyField($elements, 'company', true, array(
    		'isset' => true,
		));

    	$project_id = Common::hashEmptyField($elements, 'project_id', Configure::read('Global.Data.Project.id'), array(
    		'isset' => true,
		));

    	$company_id = Configure::read('Config.Company.data.UserCompany.user_id');

		$default_options = array(
			'conditions'=> array(),
			'fields' => array(), 
			'contain' => array(
				'TargetProjectSale'
			), 
			'joins'	=> array(),
			'order' => array(
				'TargetProjectSaleDetail.month_target' => 'ASC'
			),
			'limit' => 20, 
		);

		if( !empty($project_id) ) {
			$default_options['conditions']['TargetProjectSale.project_id'] = $project_id;
		}

		if($company == true && $status != 'all_data_company'){
			$default_options['conditions']['TargetProjectSale.company_id'] = $company_id;
		}

		return $this->merge_options($default_options, $options, $find);
	}
}