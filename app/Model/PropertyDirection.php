<?php
class PropertyDirection extends AppModel {
	var $name = 'PropertyDirection';

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
            $default_options['conditions']['PropertyDirection.modified >'] = $lastupdated;
        }

        return $this->merge_options($default_options, $options, $find);
	}

    function getMerge ( $data, $id = false, $modelName = false, $fieldName = 'PropertyDirection.id', $empty = false ) {
        if( empty($data['PropertyDirection']) && !empty($id) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    $fieldName => $id,
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

        if( !empty($empty) && empty($data['PropertyDirection']) ) {
            $data['PropertyDirection'] = array();
        }

        return $data;
    }
}
?>