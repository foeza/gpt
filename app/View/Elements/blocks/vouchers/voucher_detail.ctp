<?php

	$applyTo		= $this->Rumahku->filterEmptyField($this->request->data, 'Voucher', 'apply_to', 'all');
	$voucherCodes	= $this->Rumahku->filterEmptyField($this->request->data, 'VoucherCode');
	$discountTypes	= !empty($discountTypes) ? $discountTypes : array('nominal' => __('Nominal'), 'percentage' => __('Persentase'));
	$inputs			= '';
	$currency		= Configure::read('__Site.config_currency_code');

//	detail package
	$columns	= array(
		'package_name'		=> array('name' => __('Nama Paket')),
		'discount_type'		=> array('name' => __('Jenis Potongan')),
		'discount_value'	=> array('name' => __('Jumlah Potongan'), 'class' => 'taright')
	);
	$columns	= $this->Rumahku->_generateShowHideColumn($columns, 'field-table', array(
		'hideshow' => false,
	));
	$contents	= '';

	echo($this->Html->tag('h2', __('Detail Potongan'), array('class' => 'sub-heading')));

	if(!empty($columns)){
		$columns = $this->Html->tag('thead', $this->Html->tag('tr', $columns));
	}

	if($applyTo == 'all'){
		$discountType	= $this->Rumahku->filterEmptyField($this->request->data, 'Voucher', 'discount_type', 'nominal');
		$discountValue	= $this->Rumahku->filterEmptyField($this->request->data, 'Voucher', 'discount_value', 0);
		$typeName		= $this->Rumahku->filterEmptyField($discountTypes, $discountType);
		$decimalPlaces	= floatval($discountValue) - intval($discountValue) == 0 ? 0 : 2;

		$discountValue = $this->Number->currency($discountValue, '', array(
			'places' => $decimalPlaces, 
		));

		$discountValue = $discountType == 'nominal' ? $currency.' '.$discountValue : $discountValue.' %';

		$content = array(
			__('Semua Paket Membership'), 
			$typeName, 
			array($discountValue, array(
				'class' => 'taright'
			))
		);

		$contents.= $this->Html->tableCells(array($content));
		$contents = $this->Html->tag('table', $columns.$this->Html->tag('tbody', $contents), array('class' => 'table grey'));
	}
	else{
		$voucherDetails = $this->Rumahku->filterEmptyField($this->request->data, 'VoucherDetail');
		if($voucherDetails){
			foreach($voucherDetails as $key => $voucherDetail){
				$discountType	= $this->Rumahku->filterEmptyField($voucherDetail, 'discount_type');
				$discountValue	= $this->Rumahku->filterEmptyField($voucherDetail, 'discount_value', NULL, 0);
				$package		= $this->Rumahku->filterEmptyField($voucherDetail, 'MembershipPackage');
				$packageName	= $this->Rumahku->filterEmptyField($package, 'name');
				$typeName		= $this->Rumahku->filterEmptyField($discountTypes, $discountType);

				$discountValue = $this->Number->currency($discountValue, '', array(
					'places' => (is_int($discountValue) ? 0 : 2), 
				));
				$discountValue = $discountType == 'nominal' ? $currency.' '.$discountValue : $discountValue.' %';

				$content = array(
					$packageName, 
					$typeName, 
					array($discountValue, array(
						'class' => 'taright'
					))
				);

				$contents.= $this->Html->tableCells(array($content));
			}

			$contents = $this->Html->tag('table', $columns.$this->Html->tag('tbody', $contents), array('class' => 'table grey'));
		}
		else{
			$contents = $this->Html->tag('div', __('Data tidak ditemukan'), array('class' => 'wrapper-border'));
		}
	}

	$contents = $this->Html->div('table-responsive', $contents);
	echo($contents);

//	detail voucher code
	$columns	= array(
		'voucher_code'	=> array('name' => __('Kode Voucher')),
		'usage_limit'	=> array('name' => __('Maksimum Jumlah Pemakaian'), 'class' => 'taright'),
		'usage_count'	=> array('name' => __('Jumlah Terpakai'), 'class' => 'taright'),
		'created'		=> array('name'	=> __('Tanggal Dibuat'), 'class' => 'taright')
	);
	$columns	= $this->Rumahku->_generateShowHideColumn($columns, 'field-table', array(
		'hideshow' => false,
	));
	$contents	= '';

	echo($this->Html->tag('h2', __('Detail Kode Voucher ('.count($voucherCodes).')'), array('class' => 'sub-heading')));

	if(!empty($columns)){
		$columns = $this->Html->tag('thead', $this->Html->tag('tr', $columns));
	}

	if($voucherCodes){
		foreach($voucherCodes as $key => $voucherCode){
			$code			= $this->Rumahku->filterEmptyField($voucherCode, 'code');
			$usageLimit		= $this->Rumahku->filterEmptyField($voucherCode, 'usage_limit', NULL, 0);
			$usageCount		= $this->Rumahku->filterEmptyField($voucherCode, 'usage_count', NULL, 0);
			$created		= $this->Rumahku->filterEmptyField($voucherCode, 'created');
			$created		= date('d/m/Y H:i', strtotime($created));

			$usageLimit = $this->Number->currency($usageLimit, '', array('places' => 0));
			$usageCount = $this->Number->currency($usageCount, '', array('places' => 0));
			$content	= array(
				$code, 
				array($usageLimit, array('class' => 'taright')), 
				array($usageCount, array('class' => 'taright')), 
				array($created, array('class' => 'taright'))
			);

			$contents.= $this->Html->tableCells(array($content));
		}

		$contents = $this->Html->tag('table', $columns.$this->Html->tag('tbody', $contents), array('class' => 'table grey'));
	}
	else{
		$contents = $this->Html->tag('div', __('Data tidak ditemukan'), array('class' => 'wrapper-border'));
	}

	$contents = $this->Html->div('table-responsive', $contents);
	echo($contents);

?>