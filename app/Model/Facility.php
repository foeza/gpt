<?php
class Facility extends AppModel {
	var $name = 'Facility';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama fasilitas'
			),
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');
		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['Facility.modified >'] = $lastupdated;
        }

        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'Facility.id',
                  'Facility.name',
                );
            }
        }

        return $options;
    }

	function getMerge ( $data, $id = false ) {
		if( empty($data['Facility']) && !empty($id) ) {
			$value = $this->getData('first', array(
				'conditions' => array(
					'Facility.id' => $id,
				),
			));

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}
}
?>