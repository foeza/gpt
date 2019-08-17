<?php
class ViewExpertCategoryCompany extends AppModel {
	var $name = 'ViewExpertCategoryCompany';

	var $belongsTo = array(
		'ExpertCategory' => array(
			'className' => 'ExpertCategory',
			'foreignKey' => 'expert_category_id'
		),
	);

	public function getData( $find = 'all', $options = array(), $elements = array()  ) {
		$allow_me = Common::hashEmptyField($elements, 'allow_me');
		$is_listing = Common::hashEmptyField($elements, 'is_listing');
		$company_id = Common::hashEmptyField($elements, 'company_id', Configure::read('Principle.id'), array(
			'isset' => true,
		));
		$isCompanyAdmin	= Configure::read('User.companyAdmin');

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
			),
		);

		if( !empty($company_id) ) {
			$default_options['conditions'][]['OR'] = array(
				array( 'ViewExpertCategoryCompany.company_id' => 0 ),
				array( 'ViewExpertCategoryCompany.company_id' => $company_id ),
			);
		}
		if( !empty($allow_me) ) {
			if( empty($isCompanyAdmin) ) {
				$default_options['conditions']['ViewExpertCategoryCompany.is_allow_agent'] = true;
			}
		}
		if( !empty($is_listing) ) {
			if( empty($isCompanyAdmin) ) {
				$default_options['conditions']['ViewExpertCategoryCompany.expert_category_type'] = NULL;
			}
		}

		return $this->merge_options($default_options, $options, $find);
	}
}
?>