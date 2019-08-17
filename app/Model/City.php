<?php
class City extends AppModel {
	var $name = 'City';
	var $displayField = 'name';

    var $belongsTo = array(
        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'region_id',
        ),
    );

	/**
	* 	@param string $find - all, list, paginate
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	*		string paginate - Pick opsi query
	* 	@param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
	* 	@param boolean $is_merge - True merge default opsi dengan opsi yang diparsing, False gunakan hanya opsi yang diparsing
	*/
	function getData($find = 'all', $options = array(), $elements = array()) {
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');
		$default_options = array(
			'order' => array(
				'City.order' => 'ASC',
				'City.name' => 'ASC'
			),
			'conditions' => array(
				'City.status' => 1,
			),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['City.modified >'] = $lastupdated;
        }

		if( !empty($options)){
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
                  'City.id',
                  'City.region_id',
                  'City.slug',
                  'City.name',
                  'City.location',
                  'City.latitude',
                  'City.longitude',
                );
            }
        }

        return $options;
    }

    function getMerge ( $data, $id = false, $modelName = 'City', $fieldName = 'City.id', $options = array() ) {
        if( empty($data[$modelName]) && !empty($id) ) {
            $value = $this->getCity($id, $fieldName, $options);

            if( !empty($value['City']) ) {
                $data[$modelName] = $value['City'];
            }
        }

        return $data;
    }

    function getByKeyword ( $keyword ) {
    	return $this->getData('list', array(
            'conditions' => array(
                'City.name LIKE' => '%'.$keyword.'%',
            ),
            'fields' => array(
            	'City.id', 'City.id',
        	),
        ));
    }

    function getCity($id, $fieldName = 'City.id', $options = array()){
        $result = array();
        $options['conditions'][$fieldName] = $id;

        if( empty($options['order']) ) {
            $options['order'] = array(
                'City.order' => 'ASC',
                'City.name' => 'ASC'
            );
        }

        if(!empty($id)){
            $result = $this->find('first', $options);
        }

        return $result;
    }
}
?>