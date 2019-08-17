<?php
class PropertyLog extends AppModel {
	var $name = 'PropertyLog';

	var $validate = array(
		'property_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan properti id',
			),
		),
		'parent_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan perusahaan id',
			),
		),
		'group_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan group id',
			),
		),
		'user_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan user id',
			),
		),
		'action' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan slug',
			),
		),
		'date' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan tanggal action',
			),
		),
	);

	var $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => 'parent_id',
		),
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'contain' => array(), 
			'conditions' => array(),
			'order' => array(),
			'field' => array()
		);

		return $this->merge_options($default_options, $options, $find);
	}

	function _callRefineParams($data = array(), $default_options = false){
		$sort = Common::hashEmptyField($data, 'named.sort');

		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'name' => array(
				'type' => 'like',
				'field' => 'CONCAT(User.first_name, " ", User.last_name)',
				'contain' => 'User',
			),
			'mls_id' => array(
				'type' => 'like',
				'field' => 'Property.mls_id',
				'contain' => 'Property',
			),
			'desc' => array(
				'field' => 'PropertyLog.action',
			),
		));

		if($sort){
			$sortUser = strpos($sort, 'User.');
			$sortProperty = strpos($sort, 'Property.');

			if(is_numeric($sortUser)){
				$default_options['contain'][] = 'User';
			}

			if(is_numeric($sortProperty)){
				$default_options['contain'][] = 'Property';
			}
		}

		return $default_options;
	}
}
?>