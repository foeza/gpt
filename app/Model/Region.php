<?php
class Region extends AppModel {
	var $name = 'Region';

	function getData($find = 'all', $options = array(), $elements = array()) {
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');

		$default_options = array(
			'order' => array(
				'Region.order' => 'ASC',
				'Region.name' => 'ASC'
			),
			'conditions' => array(
				'Region.status' => 1,
			),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['Region.modified >'] = $lastupdated;
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
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['cache'])){
                $default_options['cache'] = $options['cache'];
                    
                if(!empty($options['cacheConfig'])){
                    $default_options['cacheConfig'] = $options['cacheConfig'];
                }
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        // $default_options = $this->_callFieldForAPI($find, $default_options);

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}

		return $result;
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'Region.id',
                  'Region.slug',
                  'Region.code',
                  'Region.name',
                  'Region.location',
                  'Region.latitude',
                  'Region.longitude',
                );
            }
        }

        return $options;
    }

    function getMerge ( $data, $id = false, $modelName = 'Region', $elements = array() ) {
        if( empty($data[$modelName]) && !empty($id) ) {
            $cache = $this->filterEmptyField($elements, 'cache');
            $options = $this->buildCache($cache, array(
                'conditions' => array(
                    'Region.id' => $id,
                ),
            ));

            $value = $this->getData('first', $options);

            if( !empty($value['Region']) ) {
                $data[$modelName] = $value['Region'];
            }
        }
        return $data;
    }

    function getByKeyword ( $keyword ) {
    	return $this->getData('list', array(
            'conditions' => array(
                'Region.name LIKE' => '%'.$keyword.'%',
            ),
            'fields' => array(
            	'Region.id', 'Region.id',
        	),
        ));
    }
}
?>