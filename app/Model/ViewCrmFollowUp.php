<?php
class ViewCrmFollowUp extends AppModel {
	var $name = 'ViewCrmFollowUp';

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
        $mine = isset($elements['mine'])?$elements['mine']:true;
        $company = isset($elements['company'])?$elements['company']:true;
        $adminRumahku = Configure::read('User.Admin.Rumahku');

		$default_options = array(
			'conditions' => array(),
			'order' => array(
				'ViewCrmFollowUp.activity_datetime' => 'DESC',
				'ViewCrmFollowUp.id' => 'DESC',
			),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
            'limit' => array(),
		);

        if( !empty($mine) ) {
            $user_admin = Configure::read('User.admin');
            $user_login_id = Configure::read('User.id');

            $data_arr = $this->User->getUserParent($user_login_id);
			$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
			$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

            if( (empty($user_admin) && empty($adminRumahku)) || !empty($is_sales) ) {
                $default_options['conditions']['ViewCrmFollowUp.user_id'] = $user_ids;
                $company = false;
            } else {
            	$company = true;
            }
        }

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $agent_id = $this->User->getAgents($parent_id, true, 'list', false, array(
            	'role' => 'all',
        	));

            $default_options['conditions']['ViewCrmFollowUp.user_id'] = $agent_id;
        }

        return $this->merge_options($default_options, $options, $find);
	}
}
?>