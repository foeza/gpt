<?php
class ActivityCategoryPoint extends AppModel {
	var $name = 'ActivityCategoryPoint';
	var $displayField = 'name';

	var $belongsTo = array(
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
		),
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'conditions' => array(
				'ActivityCategoryPoint.status' => 1,
			),
			'order' => array(
				'ActivityCategoryPoint.id',
			),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
            'limit' => array(),
		);

		return $this->merge_options($default_options, $options, $find);
	}

	function getPoint( $user_id = null ) {
		if( !empty($user_id) ) {
			$this->virtualFields['total_point'] = 'SUM(ActivityCategoryPoint.point)';
			$value = $this->find('first', array(
				'conditions' => array(
					'ActivityCategoryPoint.user_id' => $user_id,
					'ActivityCategoryPoint.activity_status' => 'confirm',
				),
			));

			return Common::hashEmptyField($value, 'ActivityCategoryPoint.total_point', 0);
		} else {
			return null;
		}
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$sort = Common::hashEmptyField($data, 'named.sort');

		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'date_from' => array(
				'field' => 'DATE_FORMAT(ActivityCategoryPoint.periode_date, \'%Y-%m\') >=',
			),
			'date_to' => array(
				'field' => 'DATE_FORMAT(ActivityCategoryPoint.periode_date, \'%Y-%m\') <=',
			),
			'period_year' => array(
				'field' => 'DATE_FORMAT(ActivityCategoryPoint.periode_date, \'%Y\')',
			),
			'period_month' => array(
				'field' => 'DATE_FORMAT(ActivityCategoryPoint.periode_date, \'%c\')',
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