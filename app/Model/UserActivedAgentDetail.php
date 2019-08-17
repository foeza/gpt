<?php
class UserActivedAgentDetail extends AppModel {
	var $name = 'UserActivedAgentDetail';

	var $belongsTo = array(
		'UserActivedAgent' => array(
			'className' => 'UserActivedAgent',
			'foreignKey' => 'user_actived_agent_id',
		),
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
	);

	var $validate = array(
		'agent_decilne_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'user harap dipilih',
			),
		),
		'agent_assign_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Agen harap diisi',
			),
		),
		'type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Type harap diisi',
			),
		),
	);

	function getData( $find='all', $options = array(), $elements = array() ){
		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(),
		);


        if( !empty($options) ) {
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
            if(!empty($options['cache'])){
                $default_options['cache'] = $options['cache'];
                    
                if(!empty($options['cacheConfig'])){
                    $default_options['cacheConfig'] = $options['cacheConfig'];
                }
            }
		}

        // $default_options = $this->_callFieldForAPI($find, $default_options);

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
		
        return $result;
	}

	function getMerge ( $data, $id , $field = 'UserActivedAgentDetail.id', $type = 'first', $options = array()) {
		if( empty($data['UserActivedAgentDetail']) && !empty($id) ) {
			$value = $this->getData( $type, array(
				'conditions' => array_merge(array(
					$field => $id,
				), $options)
			));

			if(!empty($value)) {
				if( !empty($value[0]) ) {
					$data = array_merge($data, array('UserActivedAgentDetail' => $value));
				} else {
					$data = array_merge($data, $value);
				}
			}
		}

		return $data;
	}


}
?>