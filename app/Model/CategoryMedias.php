<?php
class CategoryMedias extends AppModel {
	var $name = 'CategoryMedias';

	function getData( $find, $options = false, $elements = array() ){
        $default_options = array(
            'conditions'=> array(),
			'order' => array(),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        return $this->merge_options($default_options, $options, $find);
    }

    function getMerge ( $data, $id ) {
        if( empty($data['CategoryMedias']) && !empty($id) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    'CategoryMedias.id' => $id,
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