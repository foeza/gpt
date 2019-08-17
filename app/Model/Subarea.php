<?php
class Subarea extends AppModel {
	var $name = 'Subarea';
	var $belongsTo = array(
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	function getSubareas( $find = 'list', $region_id = false, $city_id = false ) {
		$conditions = array();

		if( !empty($region_id) ) {
			$conditions['Subarea.region_id'] = $region_id;
		}
		if( !empty($city_id) ) {
			$conditions['Subarea.city_id'] = $city_id;
		}

		return $this->getData($find, array(
			'conditions' => $conditions,
		));
	}

	/**
	* 	@param string $find - all, list, paginate, count
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	*		string count - Pick jumah data
	*		string paginate - Pick opsi query
	* 	@param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
	* 	@param boolean $is_merge - True merge default opsi dengan opsi yang diparsing, False gunakan hanya opsi yang diparsing
	* 	@return array - hasil array atau opsi 
	*/
	public function getData( $find = 'all', $options = array(), $elements = array() ){
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');
		$default_options = array(
			'order' => array(
				'Subarea.name' => 'ASC',
				'Subarea.id' => 'ASC',
			),
			'conditions' => array(
				'Subarea.status' => 1,
			),
			'contain' => array(
				'Country',
				'Region',
				'City',
			),
            'fields'=> array(),
            'group'=> array(),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['Subarea.modified <>'] = NULL;
            $default_options['conditions']['Subarea.modified >'] = $lastupdated;
        }
	
        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'Subarea.id',
                  'Subarea.slug',
                  'Subarea.name',
                  'Subarea.zip',
                  'Subarea.location',
                  'Subarea.latitude',
                  'Subarea.longitude',
                );
            }
        }

        return $options;
    }

    function getMerge ( $data, $id = false, $modelName = 'Subarea', $fieldName = 'Subarea.id', $options = array() ) {
        if( empty($data[$modelName]) && !empty($id) ) {
            $value = $this->getSubareaByID($id, $fieldName, $options);

            if( !empty($value['Subarea']) ) {
                $data[$modelName] = $value['Subarea'];
            }
        }

        return $data;
    }

    function getByKeyword ( $keyword ) {
    	return $this->getData('list', array(
            'conditions' => array(
                'Subarea.name LIKE' => '%'.$keyword.'%',
            ),
            'fields' => array(
            	'Subarea.id', 'Subarea.id',
        	),
        ));
    }

    function getSubareaByID($id, $fieldName = 'Subarea.id', $options = array()){
        $result = array();
        $options['conditions'][$fieldName] = $id;
        $options['contain'] = false;

        if( empty($options['order']) ) {
	        $options['order'] = array(
				'Subarea.order' => 'ASC',
				'Subarea.name' => 'ASC'
			);
	    }

        if(!empty($id)){
            $result = $this->getData('first', $options);
        }

        return $result;
    }
}
?>