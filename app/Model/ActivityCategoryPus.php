<?php
class ActivityCategoryPus extends AppModel {
	var $belongsTo = array(
		'ExpertCategory' => array(
			'className' => 'ExpertCategory',
			'foreignKey' => 'expert_category_id',
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
		
		$default_options = array(
			'conditions' => array(
				'ActivityCategoryPus.status' => 1,
			),
			'order' => array(
				'ActivityCategoryPus.pus' => 'DESC',
			),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
            'limit' => array(),
		);

        if( !empty($company) ) {
            $principle_id = Configure::read('Principle.id');
            $default_options['conditions']['ActivityCategoryPus.principle_id'] = $principle_id;
        }

		return $this->merge_options($default_options, $options, $find);
	}
}
?>