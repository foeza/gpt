<?php
class ShareLog extends AppModel {
	var $name = 'ShareLog';

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
		),
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => 'parent_id',
		),
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'document_id',
			'conditions' => array(
				'ShareLog.type' => 'property',
			),
		),
		'Advice' => array(
			'className' => 'Advice',
			'foreignKey' => 'document_id',
			'conditions' => array(
				'ShareLog.type' => 'berita',
			),
		),
	);
	
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

    	$this->virtualFields['user_id'] = 'CASE WHEN ShareLog.group_id IN (2, 5, 3, 4) OR ShareLog.group_id > 20 THEN ShareLog.user_id ELSE 0 END';
	}

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$company = Common::hashEmptyField($elements, 'company', true, array(
			'isset' => true,
		));
		$mine = Common::hashEmptyField($elements, 'mine', true, array(
			'isset' => true,
		));

		$on_time = Common::hashEmptyField($elements, 'on_time');

        $admin_rumahku = Configure::read('User.Admin.Rumahku');
		$isCompanyAdmin	= Configure::read('User.companyAdmin');

		$default_options = array(
			'conditions'=> array(),
			'order'=> array(
				'ShareLog.created' => 'DESC',
				'ShareLog.id' => 'DESC',
			),
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);

        if( !empty($company) && empty($admin_rumahku) ) {
			$group_id = Configure::read('User.group_id');
        	$is_agent = Configure::read('__Site.Role.company_agent');

        	if( empty($isCompanyAdmin) && !empty($mine) && in_array($group_id, $is_agent) ) {
				$user_login_id = Configure::read('User.id');
				$default_options['conditions']['ShareLog.user_id'] = $user_login_id;
        	} else {
	            $companyData = Configure::read('Config.Company.data');
	            $parent_id = Configure::read('Principle.id');
	            $group_id = $this->filterEmptyField($companyData, 'User', 'group_id');

	        	if( $group_id == 4 ) {
					$principle_id = $this->User->getAgents($parent_id, true, 'list', false, array(
						'role' => 'principle',
					));

					$default_options['conditions']['ShareLog.parent_id'] = $principle_id;
	        	} else {
					$default_options['conditions'][]['OR'] = array(
						'ShareLog.parent_id' => $parent_id,
						'ShareLog.user_id' => $parent_id,
					);
	        	}
	        }
        }

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
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }

		if( $find == 'paginate' ) {
			if( empty($default_options['limit']) ) {
				$default_options['limit'] = Configure::read('__Site.config_admin_pagination');
			}

			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$user_id = Common::hashEmptyField($data, 'named.user_id', false, array(
        	'addslashes' => true,
        	'isset' => true,
    	));
		$sort = Common::hashEmptyField($data, 'named.sort', false, array(
        	'addslashes' => true,
        	'isset' => true,
    	));

		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'name' => array(
				'field' => array(
					'OR' => array(
						'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\'))',
						'IFNULL(User.first_name, \'Unknown\')',
						'CASE WHEN ShareLog.group_id IN (2, 5, 3, 4) OR ShareLog.group_id > 20 THEN CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) ELSE \'Unknown\' END'
					),
				),
				'type' => 'like',
				'contain' => array(
					'User',
				),
			),
			'date_from' => array(
				'field' => 'DATE_FORMAT(ShareLog.created, \'%Y-%m-%d\') >=',
			),
			'date_to' => array(
				'field' => 'DATE_FORMAT(ShareLog.created, \'%Y-%m-%d\') <=',
			),
			'module_type' => array(
				'type' => 'like',
				'field' => array(
					'OR' => array(
						'CONCAT(\'#\', Property.mls_id, \' - \', Property.title)',
						'Advice.title',
					),
				),
				'contain' => array(
					'Property',
					'Advice',
				),
			),
			'type_name' => array(
				'type' => 'like',
				'field' => 'ShareLog.type',
			),
			'sosmed' => array(
				'field'=> 'ShareLog.sosmed',
				'type' => 'like',
			),
			'group_id' => array(
				'field'=> 'ShareLog.group_id',
			),
			'document_id' => array(
				'field'=> 'ShareLog.document_id',
			),
		));

		if( is_numeric($user_id) ) {
			$default_options['conditions']['ShareLog.user_id'] = $user_id;
		}

		switch ($sort) {
			case 'Group.name':
				$default_options['contain'][] = 'Group';
				break;
			case 'User.full_name':
				$default_options['contain'][] = 'User';
				break;
		}

		return $default_options;
	}

	function doSave ( $data ) {
		$this->create();

		if($this->save($data)) {
			return true;	
		} else {
			return false;
		}
	}
}
?>