<?php
class ViewUserCompanyEbrochure extends AppModel {
	var $name = 'ViewUserCompanyEbrochure';

	var $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
		'PropertyAction' => array(
			'className' => 'PropertyAction',
			'foreignKey' => 'property_action_id',
		),
		'PropertyType' => array(
			'className' => 'PropertyType',
			'foreignKey' => 'property_type_id',
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
		),
		'Subarea' => array(
			'className' => 'Subarea',
			'foreignKey' => 'subarea_id',
		),
		'Currency' => array(
            'className' => 'Currency',
            'foreignKey' => 'currency_id',
        ),
		'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
		'Period' => array(
			'className' => 'Period',
			'foreignKey' => 'period_id',
		),
        'LotUnit' => array(
            'className' => 'LotUnit',
            'foreignKey' => 'lot_unit_id',
        ),
	);

	function getData($find, $options = array(), $elements = array()){
    	$mine = isset($elements['mine'])?$elements['mine']:false;
    	$action_type = isset($elements['action_type'])?$elements['action_type']:false;
    	$company = isset($elements['company'])?$elements['company']:true;

    	$parent_id = Configure::read('Principle.id');
    	$is_admin = Configure::read('User.companyAdmin');
	    $user_login_id = Configure::read('User.id');
        $companyData = Configure::read('Config.Company.data');

		$default_options = array(
			'conditions' => array(),
			'contain' => array(),
			'order' => array(
				'ViewUserCompanyEbrochure.id' => 'DESC'
			),
			'fields' => array(),
		);

		if( !empty($mine) ) {
            $default_options['conditions']['ViewUserCompanyEbrochure.user_id'] = $user_login_id;
        } else if( !empty($company) ) {
            $company_group_id = $this->filterEmptyField($companyData, 'User', 'group_id');

        	if( $company_group_id == 4 ) {
				$principle_id = $this->User->getAgents($parent_id, true, 'list', false, array(
					'role' => 'principle',
				));

				$default_options['conditions']['ViewUserCompanyEbrochure.principle_id'] = $principle_id;
        	} else {
	            $default_options['conditions']['ViewUserCompanyEbrochure.principle_id'] = $parent_id;
        	}
        }

        switch ($action_type) {
        	case 'sell':
	            $default_options['conditions']['ViewUserCompanyEbrochure.property_action_id'] = 1;
        		break;
        	case 'rent':
	            $default_options['conditions']['ViewUserCompanyEbrochure.property_action_id'] = 2;
        		break;
        }

		return $this->merge_options($default_options, $options, $find);
	}
}
?>