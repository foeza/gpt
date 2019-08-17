<?php
class Log extends AppModel {
	var $name = 'Log';
	var $validate = array(
		'admin' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'error' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
		'status' => array(
			'boolean' => array(
				'rule' => array('boolean'),
			),
		),
	);

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => 'parent_id',
		),
		'Acos' => array(
			'className' => 'Acos',
			'foreignKey' => 'model',
		),
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$activity = Common::hashEmptyField($elements, 'activity');
		$admin = Common::hashEmptyField($elements, 'admin');
		$company = Common::hashEmptyField($elements, 'company', true, array(
			'isset' => true,
		));

		$on_time = Common::hashEmptyField($elements, 'on_time');

        $admin_rumahku = Configure::read('User.Admin.Rumahku');

		$default_options = array(
			'conditions'=> array(
				'Log.status'=> 1,
				'Log.admin' => false,
			),
			'order'=> array(
				'Log.created' => 'DESC',
				'Log.id' => 'DESC',
			),
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);

		if($activity){
			$default_options['conditions'][] = array(
				'Log.model <>' => array(
					'ajax',
					'ApiKprs',
					'api_kprs',
					'crontab',
					'api',
				),
				array('Log.parent_id <>' => 0),
				array('Log.user_id <>' => NULL),
				array('Log.user_id <>' => 0),
			);
		}

		if($admin){
			$default_options['conditions']['Log.admin'] = true;
		}

		if(!empty($on_time['flag'])){
			$hour = Common::hashEmptyField($on_time, 'hour');
			$day = Common::hashEmptyField($on_time, 'day');

			$default_options['conditions'][] = array(
				'DATE_FORMAT(Log.created, \'%w\')' => (int) $day,
				'DATE_FORMAT(Log.created, \'%k\')' => (int) $hour,
			);
		}

        if( !empty($company) && empty($admin_rumahku) ) {
            $companyData = Configure::read('Config.Company.data');
            $parent_id = Configure::read('Principle.id');
            $group_id = $this->filterEmptyField($companyData, 'User', 'group_id');

        	if( $group_id == 4 ) {
				$principle_id = $this->User->getAgents($parent_id, true, 'list', false, array(
					'role' => 'principle',
				));

				$default_options['conditions']['User.parent_id'] = $principle_id;
				$default_options['contain'][] = 'User';
        	} else {
				$default_options['conditions'][]['OR'] = array(
					'User.parent_id' => $parent_id,
					'Log.user_id' => $parent_id,
				);
				$default_options['contain'][] = 'User';
        	}
        }

        return $this->merge_options($default_options, $options, $find);
	}

	function doSave ( $data ) {
		$this->create();

		if($this->save($data)) {
			return true;	
		} else {
			return false;
		}
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$empty_field_opts	= array('addslashes' => true);

		$keyword = Common::hashEmptyField($data, 'named.keyword', false, $empty_field_opts);
		$device = Common::hashEmptyField($data, 'named.device', false, $empty_field_opts);
		$principle_id = Common::hashEmptyField($data, 'named.principle_id', false, $empty_field_opts);
		$company_ids = Common::hashEmptyField($data, 'named.company_id', false, $empty_field_opts);
		$group_id = Common::hashEmptyField($data, 'named.group_id', false, $empty_field_opts);

		// date
		$date_from = Common::hashEmptyField($data, 'named.date_from', false, $empty_field_opts);
		$date_to = Common::hashEmptyField($data, 'named.date_to', false, $empty_field_opts);

		$type = Common::hashEmptyField($data, 'named.type', false, $empty_field_opts);
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));

		if( !empty($keyword) ) {
			$default_options['contain'][] = 'User';
			$default_options['conditions']['OR'] = array(
				'Log.name LIKE' => '%'.$keyword.'%',
				'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE' => '%'.$keyword.'%',
			);
		}

		if($company_ids){
			$default_options['conditions'][]['Log.parent_id'] = $company_ids;
		}

		if($group_id){
			$default_options['conditions'][]['Log.group_id'] = $group_id;
		}

		if($device){
			$default_options['conditions'][]['Log.device'] = $device;
		}

		if( !empty($principle_id) ) {
			if( !is_array($principle_id) ) {
				$principle_id = explode(',', $principle_id);
			}
			
			$default_options['conditions']['Log.parent_id'] = $principle_id;
		}

		if($date_from){
			$default_options['conditions']['Log.created >='] = $date_from;
		}

		if($date_to){
			$default_options['conditions']['Log.created <='] = $date_to;
		}

		if($type){
			$default_options['conditions']['Log.mobile'] = $type;
		}

		switch ($sort) {
			case 'UserCompany.name':
            	$default_options['contain'][] = 'UserCompany';
				break;
		}

		return $default_options;
	}

	function logActivity( $info = NULL, $user = false, $requestHandler = false, $params = fase, $error = 0, $options = false ){
		$log = array();

		if( !empty($options) ) {
			$log = array_merge($log, $options);
		}

		if( !empty($user['User']['id']) ) {
			$log['Log']['user_id'] = $user['User']['id'];
		}

		if( !empty($user['User']['email']) ) {
			$info = sprintf('( %s ) %s', $user['User']['email'], $info);
		}
		
		$log['Log']['name'] = $info;
		$log['Log']['model'] = $params['controller'];
		$log['Log']['action'] = $params['action'];

		if( !empty($requestHandler) ) {
			$ip_address = $requestHandler->getClientIP();
			$log['Log']['ip'] = $ip_address;

			$log['Log']['user_agent'] = env('HTTP_USER_AGENT');
			
			if( !empty($log['Log']['user_agent']) ) {
				// $user_agents = get_browser($log['Log']['user_agent'], true);
				$log['Log']['browser'] = !empty($user_agents['browser'])?implode(' ', array($user_agents['browser'], $user_agents['version'])):'';
				$log['Log']['os'] = !empty($user_agents['platform'])?$user_agents['platform']:'';
			} else {
				$user_agents = '';
				$log['Log']['browser'] = '';
				$log['Log']['os'] = '';
			}
			$log['Log']['from'] = $requestHandler->getReferer();
		}

		$log['Log']['data'] = serialize( $params['data'] );
		$log['Log']['named'] = serialize( $params['named'] );
		$log['Log']['admin'] = !empty($params['admin'])?1:0;
		$log['Log']['error'] = $error;

		$admin_id = Configure::read('Auth.Admin.id');
		if( !empty($admin_id) ) {
			$log['Log']['admin_id'] = $admin_id;
		}
		
		$this->create();
		if($this->save($log)) {
			return true;	
		} else {
			return false;
		}
	}
}
?>