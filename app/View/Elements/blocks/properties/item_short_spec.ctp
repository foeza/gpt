<?php 
		$mls_id = $this->Rumahku->filterEmptyField($value, 'Property', 'mls_id');
		$price = $this->Property->getPrice($value, __('(Harga belum ditentukan)'));
		
		$customMlsId = sprintf(__('ID Properti: %s'), $this->Html->tag('strong', $mls_id));
		$specs = $this->Property->getSpec($value, array(
			'Property' => array(
				array(
					'name' => 'commission',
					'label' => __('Komisi'),
					'addText' => ' %',
				),
			),
		));

		echo $this->Html->tag('div', $price, array(
			'class' => 'price',
		));
		echo $this->Html->tag('div', $customMlsId);
		echo $this->Html->tag('div', $specs, array(
			'class' => 'specs',
		));		
?>