<?php
		echo $this->Rumahku->buildInputForm('UserCompanyEbrochure.lot_unit_id', array(
			'frameClass' => 'col-sm-12',
			'labelClass' => 'col-xl-2 col-sm-4 control-label taleft',
			'id' => 'ebrosur-lot-unit',
			'empty' => __('Pilih Satuan Luas'),
            'label' => __('Satuan Luas'),
            'formGroupClass' => 'form-group box-lot-unit-ebrosur'
        ));
?>