<?php
class ViewUserClient extends AppModel {

	var $name = 'ViewUserClient';

	var $belongsTo = array(
		'UserClient' => array(
			'className' => 'UserClient',
			'foreignKey' => 'id'
		),
    );

	function getData( $find = 'all', $options = array(), $elements = array() ) {
        $status = isset($elements['status']) ? $elements['status']:'active';
        $company = isset($elements['company']) ? $elements['company']:true;
		$mine = isset($elements['mine'])?$elements['mine']:false;
		$adminRumahku = isset($elements['adminRumahku'])?$elements['adminRumahku']:Configure::read('User.Admin.Rumahku');
        $admin = Configure::read('User.admin');

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
		);

		switch ($status) {
			case 'active':
				$default_options['conditions'][$this->alias.'.status'] = 1;
				break;
		}

        if( !empty($company) && empty($adminRumahku) ) {
            $companyData = Configure::read('Config.Company.data');
            $parent_id = Configure::read('Principle.id');
            $group_id = $this->filterEmptyField($companyData, 'User', 'group_id');

        	if( $group_id == 4 ) {
				$principle_id = $this->User->getAgents($parent_id, true, 'list', false, array(
					'role' => 'principle',
				));
				$default_options['conditions'][$this->alias.'.company_id'] = $principle_id;
        	} else {
				$default_options['conditions'][$this->alias.'.company_id'] = Configure::read('Principle.id');
        	}
        }

		if( !empty($mine) ) {
			if( !empty($mine) && empty($admin)) {
				$user_login_id = Configure::read('User.id');

				$data_arr = $this->User->getUserParent($user_login_id);
				$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
				$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

				$this->bindModel(array(
					'hasOne' => array(
						'UserClientRelation' => array(
							'foreignKey' => false,
							'conditions' => array(
								'UserClientRelation.user_id = '.$this->alias.'.user_id',
								'UserClientRelation.company_id = '.$this->alias.'.company_id',
							),
						),
					)
				), false);

				if($is_sales){
					$default_options['conditions']['UserClientRelation.agent_id'] = $user_ids;
					$default_options['contain'][] = 'UserClientRelation';
				}

				$default_options['group'] = array(
					'UserClientRelation.user_id',
					'UserClientRelation.agent_id',
				);
			}
		}

		if( !empty($options) ){
			if(!empty($options['conditions'])){
				$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
			}
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['offset'])){
                $default_options['offset'] = $options['offset'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
		}

		if( $find == 'conditions' && !empty($default_options['conditions']) ) {
			$result = $default_options['conditions'];
		} else if( $find == 'paginate' ) {
			if( empty($default_options['limit']) ) {
				$default_options['limit'] = Configure::read('__Site.config_admin_pagination');
			}
			
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
		
        return $result;
	}
}
?>