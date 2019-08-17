<?php
class ViewActivityUser extends AppModel {

	var $belongsTo = array(
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => 'user_company_id',
		),
		'ExpertCategory' => array(
			'className' => 'ExpertCategory',
			'foreignKey' => 'expert_category_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'ExpertCategoryComponentActive' => array(
			'className' => 'ExpertCategoryComponentActive',
			'foreignKey' => 'expert_category_component_active_id',
		),
		'ViewExpertCategoryCompanyDetail' => array(
			'className' => 'ViewExpertCategoryCompanyDetail',
			'foreignKey' => false,
			'conditions' => array(
				'ActivityUser.expert_category_component_active_id = ViewExpertCategoryCompanyDetail.expert_category_component_active_id',
			),
		),
	);
	var $hasOne = array(
		'ViewD' => array(
            'className' => 'ViewD',
			'foreignKey' => 'user_id', 
        ),
	);

	var $hasMany = array(
		'ActivityUser' => array(
			'className' => 'ActivityUser',
			'foreignKey' => 'activity_id',
		),
	);

	function beforeSave($options = array()){
		$company_id = Configure::read('Config.Company.data.UserCompany.id');
		$principle_id = Configure::read('Principle.id');
		
		$this->data = Hash::insert($this->data, 'ActivityUser.principle_id', $principle_id);
		$this->data = Hash::insert($this->data, 'ActivityUser.user_company_id', $company_id);
	}
	
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$fullName = sprintf('trim(concat(trim(%s.first_name), " ", trim(%s.last_name)))', $this->alias, $this->alias);
		$this->virtualFields['full_name']		= $fullName;
	}

	function getData( $find = 'all', $options = array(), $elements = array() ){
    	$company = Common::hashEmptyField($elements, 'company', true, array(
    		'isset' => true,
		));

		$default_options = array(
			'conditions' => array(),
			'order' => array(
				'ViewActivityUser.point' => 'DESC',
				'ViewActivityUser.created' => 'DESC',
			),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
            'limit' => array(),
		);

        if( !empty($company) ) {
            $principle_id = Configure::read('Principle.id');
            $default_options['conditions']['ViewActivityUser.parent_id'] = $principle_id;
        }

		return $this->merge_options($default_options, $options, $find);
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$sort = Common::hashEmptyField($data, 'named.sort');

		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'date_from' => array(
				'field' => 'DATE_FORMAT(ViewActivityUser.created, \'%Y-%m-%d\') >=',
			),
			'date_to' => array(
				'field' => 'DATE_FORMAT(ViewActivityUser.created, \'%Y-%m-%d\') <=',
			),
			'document_date_from' => array(
				'field' => 'DATE_FORMAT(ViewActivityUser.action_date, \'%Y-%m-%d\') >=',
			),
			'document_date_to' => array(
				'field' => 'DATE_FORMAT(ViewActivityUser.action_date, \'%Y-%m-%d\') <=',
			),
			'name' => array(
				'field'=> 'CONCAT(User.first_name, " ", IFNULL(User.last_name, ""))',
				'type' => 'like',
				'contain' => array(
					'User',
				),
			),
			'category' => array(
				'field'=> 'ExpertCategory.name',
				'type' => 'like',
				'contain' => array(
					'ExpertCategory',
				),
			),
			'component' => array(
				'field'=> 'ViewExpertCategoryCompanyDetail.name',
				'type' => 'like',
				'contain' => array(
					'ViewExpertCategoryCompanyDetail',
				),
			),
			'status' => array(
				'field'=> 'ViewActivityUser.activity_status',
			),
		));

		if( !empty($sort) ) {
        	$sortAgent = strpos($sort, 'Agent.');
        	$sortClient = strpos($sort, 'ViewUserClient.');
        	$sortAttributeSet = strpos($sort, 'AttributeSet.');
        	$sortViewProperty = strpos($sort, 'ViewProperty.');

        	if( is_numeric($sortAgent) ) {
	            $default_options['contain'][] = 'Agent';
	        }
	        if( is_numeric($sortClient) ) {
	            $default_options['contain'][] = 'ViewUserClient';
	        }
	        if( is_numeric($sortAttributeSet) ) {
	            $default_options['contain'][] = 'AttributeSet';
	        }
	        if( is_numeric($sortViewProperty) ) {
	            $default_options['contain'][] = 'ViewProperty';
	        }
        }
        if( $sort== 'CrmProject.crmprojectactivity_count' ) {
            $default_options = $this->callBindHasMany('CrmProjectActivity', $default_options, 'crm_project_id');
        }

		return $default_options;
	}
}
?>