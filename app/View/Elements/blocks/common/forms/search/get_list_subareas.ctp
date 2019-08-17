<?php 
		$values = !empty($values)?$values:false;
		$fieldNameArea = !empty($fieldNameArea)?$fieldNameArea:'Search.subareas';

		echo $this->Rumahku->buildInputDropdown($fieldNameArea,  array(
        	'frameClass' => 'loc-select mb0',
			'label' => false,
			'empty' => __('- Semua Area (Pilih Kota Dahulu) -'),
            'fieldName' => 'subarea_id',
            'options' => $values,
            '_checkbox' => true,
        ));
?>