<?php 
		$values = !empty($values)?$values:false;
		echo $this->Rumahku->buildInputDropdown('Search.subarea',  array(
        	'frameClass' => 'loc-select mb0',
			'label' => false,
			'empty' => __('- Area (Pilih Kota Dahulu) -'),
            'fieldName' => 'subarea_id',
            'options' => $values,
            '_checkbox' => true,
        ));
?>