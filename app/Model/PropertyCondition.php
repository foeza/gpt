<?php
class PropertyCondition extends AppModel {
	var $name = 'PropertyCondition';

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
            $default_options['conditions']['PropertyCondition.modified >'] = $lastupdated;
        }

        return $this->merge_options($default_options, $options, $find);
	}

    function getMerge ( $data, $id = false, $modelName = false, $fieldName = 'PropertyCondition.id' ) {
        if( empty($data['PropertyCondition']) && !empty($id) ) {
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

        return $data;
    }
}
?>