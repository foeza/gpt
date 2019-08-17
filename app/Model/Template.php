<?php
class Template extends AppModel {
	var $name = 'Template';

	function getData( $find = 'all', $options = array() ){
		$default_options = array(
			'conditions'=> array(
				'Template.status'=> 1, 
			),
			'order'=> array(
				'Template.id' => 'ASC',
			),
			'fields' => array(),
			'contain' => array(),
			'group' => array(),
		);

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

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getMerge ( $data, $id, $elements = array() ) {
		if( empty($data['Template']) ) {
            $cache = $this->filterEmptyField($elements, 'cache');
            $options = $this->buildCache($cache, array(
				'conditions' => array(
					'Template.id' => $id,
				),
			));

			$value = $this->getData('first', $options);

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}
}
?>