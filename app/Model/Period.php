<?php
class Period extends AppModel {
	var $name = 'Period';
	var $displayField = 'name';

	function getData( $find = 'all', $options = array(), $elements = array() ){
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');
		$default_options = array(
			'conditions' => array(),
			'order'=> array(),
			'fields' => array(),
			'contain' => array(),
			'group' => array(),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['Period.modified >'] = $lastupdated;
        }

        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'Period.id',
                  'Period.name',
                );
            }
        }

        return $options;
    }

	function getMerge ( $data, $id, $elements = array() ) {
		if( empty($data['Period']) && !empty($id) ) {
            $cache = $this->filterEmptyField($elements, 'cache');
            $options = $this->buildCache($cache, array(
				'conditions' => array(
					'Period.id' => $id,
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