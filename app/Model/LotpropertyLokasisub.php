<?php
class LotpropertyLokasisub extends AppModel {
	var $name = 'LotpropertyLokasisub';

    function getMerge ( $data, $id = false, $fieldName = 'LotpropertyLokasisub.lokasisubid' ) {
        if( empty($data['LotpropertyLokasisub']) ) {
            $value = $this->find('first', array(
                'conditions' => array(
                    $fieldName => $id,
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