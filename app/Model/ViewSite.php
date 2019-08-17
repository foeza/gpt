<?php
class ViewSite extends AppModel {
	var $name = 'ViewSite';

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
            $default_options['conditions']['ViewSite.modified >'] = $lastupdated;
        }

        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'ViewSite.id',
                  'ViewSite.slug',
                  'ViewSite.name',
                );
            }
        }

        return $options;
    }

    function getMerge ( $data, $id = false, $type_id, $modelName = false, $fieldName = 'ViewSite.id' ) {
        if( empty($data['ViewSite']) && !empty($id) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    $fieldName => $id,
                    'ViewSite.property_type_id' => $type_id,
                ),
            ));

            if( !empty($value) ) {
                if( !empty($modelName) ) {
                    $data[$modelName] = array_merge($data[$modelName], $value);
                } else {
                    $data = array_merge($data, $value);
                }
            }
        }

        return $data;
    }
}
?>