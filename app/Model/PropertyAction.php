<?php
class PropertyAction extends AppModel {
	var $name = 'PropertyAction';

	function getData( $find='all', $options = array(), $elements = array() ){
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['PropertyAction.modified >'] = $lastupdated;
        }

        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
				  'PropertyAction.id',
				  'PropertyAction.slug',
				  'PropertyAction.name',
				  'PropertyAction.inactive_name',
                );
            }
        }

        return $options;
    }

    function getMerge ( $data, $id = false, $fieldName = 'PropertyAction.id', $elements = array() ) {
        if( empty($data['PropertyAction']) && !empty($id) ) {
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