<?php
class ActivityPus extends AppModel {
	var $belongsTo = array(
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => 'user_company_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
    	$company = Common::hashEmptyField($elements, 'company', true, array(
    		'isset' => true,
		));
    	$status = Common::hashEmptyField($elements, 'status');
		
		$default_options = array(
			'conditions' => array(
				'ActivityPus.activity_status' => 'open',
			),
			'order' => array(
				'ActivityPus.pus' => 'DESC',
			),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
            'limit' => array(),
		);

        if( !empty($company) ) {
            $principle_id = Configure::read('Principle.id');
            $default_options['conditions']['ActivityPus.principle_id'] = $principle_id;
        }

        switch ($status) {
        	case 'open':
           		$default_options['conditions']['ActivityPus.activity_status'] = 'open';
        		break;
        }

		return $this->merge_options($default_options, $options, $find);
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$sort = Common::hashEmptyField($data, 'named.sort');

		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'date_from' => array(
				'field' => 'DATE_FORMAT(ActivityPus.periode_date, \'%Y-%m\') >=',
			),
			'date_to' => array(
				'field' => 'DATE_FORMAT(ActivityPus.periode_date, \'%Y-%m\') <=',
			),
			'period_year' => array(
				'field' => 'DATE_FORMAT(ActivityPus.periode_date, \'%Y\')',
			),
			'period_month' => array(
				'field' => 'DATE_FORMAT(ActivityPus.periode_date, \'%c\')',
			),
			'name' => array(
				'field'=> 'CONCAT(User.first_name, " ", IFNULL(User.last_name, ""))',
				'type' => 'like',
				'contain' => array(
					'User',
				),
			),
		));

		if( !empty($sort) ) {
        	$sortAgent = strpos($sort, 'User.');

        	if( is_numeric($sortAgent) ) {
	            $default_options['contain'][] = 'User';
	        }
        }

		return $default_options;
	}
}
?>