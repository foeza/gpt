<?php
class Country extends AppModel {
	var $name = 'Country';

	function getData($find = 'all', $options = array(), $elements = array()) {
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');

		$default_options = array(
			'order' => array(
				'Country.name' => 'ASC'
			),
			'conditions' => array(
				'Country.status' => 1,
			),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['Country.modified >'] = $lastupdated;
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
                  'Country.id',
                  'Country.name',
                );
            }
        }

        return $options;
    }
}
?>