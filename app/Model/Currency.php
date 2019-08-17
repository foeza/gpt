<?php
class Currency extends AppModel {
	var $name = 'Currency';
	var $displayField = 'name';

	function getData( $find='all', $options = array(), $elements = array() ){
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');
		$default_options = array(
			'conditions'=> array(
				'Currency.status' => 1,
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['Currency.modified >'] = $lastupdated;
        }

        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'Currency.id',
                  'Currency.name',
                  'Currency.symbol',
                );
            }
        }

        return $options;
    }

	function getMerge ( $data, $id, $fieldName = 'Currency.id', $elements = array() ) {
		if( empty($data['Currency']) && !empty($id) ) {
            $cache = $this->filterEmptyField($elements, 'cache');
            $options = $this->buildCache($cache, array(
                'conditions' => array(
                    $fieldName => $id,
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