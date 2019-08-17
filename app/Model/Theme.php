<?php
class Theme extends AppModel {
	var $name = 'Theme';

	var $hasMany = array(
		'ThemeConfig' => array(
			'className' => 'ThemeConfig',
			'foreignKey' => 'theme_id'
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'conditions'=> array(
				'Theme.status'=> 1, 
			),
			'order'=> array(
				'Theme.id' => 'ASC',
			),
			'fields' => array(),
			'contain' => array(),
			'group' => array(),
		);

		$ownerType = Common::hashEmptyField($elements, 'owner_type', 'company');
		$ownerType = in_array($ownerType, array('all', 'company', 'agent')) ? $ownerType : 'company';

		if(in_array($ownerType, array('company', 'agent'))){
			$default_options['conditions'][$this->alias.'.owner_type'] = $ownerType;
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
        if(!empty($options['cache'])){
            $default_options['cache'] = $options['cache'];
                
            if(!empty($options['cacheConfig'])){
                $default_options['cacheConfig'] = $options['cacheConfig'];
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

	function getMerge ( $data, $id, $elements = array() ) {
		if( empty($data['Theme']) ) {
            $cache = $this->filterEmptyField($elements, 'cache');
            $options = $this->buildCache($cache, array(
				'conditions' => array(
					'Theme.id' => $id,
				),
			));

            $elements = Hash::remove($elements, 'cache');

			$value = $this->getData('first', $options, $elements);

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}

	function _callFieldForAPI ( $find, $options ) {
		if( !in_array($find, array( 'list', 'count' )) ) {
			$rest_api = Configure::read('Rest.token');

			if( !empty($rest_api) ) {
				$options['fields'] = array(
				  'Theme.id',
				  'Theme.slug',
				  'Theme.name',
				  'Theme.photo'
				);
			}
		}

		return $options;
	}
}
?>