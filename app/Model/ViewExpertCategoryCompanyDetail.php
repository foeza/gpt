<?php
class ViewExpertCategoryCompanyDetail extends AppModel {
	var $name = 'ViewExpertCategoryCompanyDetail';

	var $belongsTo = array(
		'ExpertCategoryCompany' => array(
			'className' => 'ExpertCategoryCompany',
			'foreignKey' => 'expert_category_company_id'
		),
	);

	public function getData( $find = 'all', $options = array(), $elements = array()  ) {
		$company_id = Common::hashEmptyField($elements, 'company_id', Configure::read('Principle.id'), array(
			'isset' => true,
		));

		$default_options = array(
			'conditions'=> array(
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(
            	'ViewExpertCategoryCompanyDetail.point' => 'DESC',
            ),
		);

		if($company_id){
			$default_options['conditions'][]['OR'] = array(
				array( 'ViewExpertCategoryCompanyDetail.company_id' => $company_id ),
				array( 'ViewExpertCategoryCompanyDetail.company_id' => 0 ),
			);
		}

		return $this->merge_options($default_options, $options, $find);
	}
}
?>