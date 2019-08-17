<?php
class LotpropertyLokasi extends AppModel {
	var $name = 'LotpropertyLokasi';

    function getMerge ( $data, $id = false, $fieldName = 'LotpropertyLokasi.lokasiid' ) {
        if( empty($data['LotpropertyLokasi']) ) {
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