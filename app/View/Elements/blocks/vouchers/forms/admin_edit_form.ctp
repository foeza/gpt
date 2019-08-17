<?php 

	$options		= !empty($options) ? $options : array();
	$packages		= !empty($packages) ? $packages : array();
	$codeMechanisms	= !empty($codeMechanisms) ? $codeMechanisms : array('manual' => __('Manual'), 'auto' => __('Otomatis'));
	$discountTypes	= !empty($discountTypes) ? $discountTypes : array('nominal' => __('Nominal'), 'percentage' => __('Persentase'));
	$periodTypes	= !empty($periodTypes) ? $periodTypes : array('periodic' => __('Periodik'), 'unlimited' => __('Tidak Terbatas'));
	$applyTo		= !empty($applyTo) ? $applyTo : array('all' => __('Semua Paket Membership'), 'manual' => __('Paket Membership Terpilih'));
	$defaultLength	= 9;
	$postData		= !empty($postData) ? $postData : array();

/*
	echo($this->Rumahku->buildInputForm('Voucher.code', array_merge(
		$options, 
		array(
			'label' => __('Kode'), 
		)
	)));
*/

	echo($this->Rumahku->buildInputForm('Voucher.name', array_merge(
		$options, 
		array(
			'label' => __('Nama Voucher *'), 
		)
	)));

	$codeMechanism = $this->Rumahku->filterEmptyField($this->data, 'Voucher', 'code_mechanism');

	if($codeMechanism == 'manual'){
	//	kalo add pake is('post') kalo edit pake is('put')
		if($postData){
			$voucherCode = Hash::get($postData, 'VoucherCode.0.code', '');
		}
		else{
			$voucherPrefix = $this->Rumahku->filterEmptyField($this->data, 'Voucher', 'prefix');

			$voucherCode = Hash::get($this->data, 'VoucherCode.0.code', '');
			$voucherCode = substr($voucherCode, strlen($voucherPrefix));
		}
	}
	else{
		echo($this->Rumahku->buildInputForm('Voucher.length', array_merge(
			$options, 
			array(
				'label'			=> __('Panjang Kode voucher *'), 
				'type'			=> 'text', 
				'inputClass'	=> 'input_number', 
			)
		)));

		echo($this->Rumahku->buildInputForm('Voucher.prefix', array_merge(
			$options, 
			array(
				'label' => __('Prefiks Kode Voucher'), 
				'class' => 'relative col-sm-3 col-xs-12', 
			)
		)));
	}

	$label = $this->Form->label('Voucher.code_mechanism', __('Kode Voucher *'), array('class' => 'control-label'));
	$input = $this->Html->tag('div', $label, array('class' => 'col-xl-2 col-sm-4 col-xs-12 control-label taright'));
	$input.= $this->Form->input('Voucher.code_mechanism', array(
		'div'			=> array('class' => 'relative col-sm-3 col-xs-12'),
		'label' 		=> FALSE, 
		'required'		=> FALSE, 
		'class'			=> 'form-control', 
		'options'		=> $codeMechanisms, 
		'disabled'		=> TRUE
	));
	
	if($codeMechanism == 'manual'){
		$input.= $this->Form->input('VoucherCode.0.code', array(
			'id'		=> 'VoucherCodeCode', 
			'div'		=> array('id' => 'voucher-code-placeholder', 'class' => 'relative col-sm-5 col-xs-12'),
			'label' 	=> FALSE, 
			'required'	=> FALSE, 
			'class'		=> 'form-control', 
			'maxlength'	=> $defaultLength, 
			'value'		=> $voucherCode, 
		));	
	}

	$input = $this->Html->div('form-group', 
		$this->Html->div('row', 
			$this->Html->div('col-sm-8', 
				$this->Html->div('row', $input)
			)
		)
	);
	echo($input);

//	kalo add pake is('post') kalo edit pake is('put')
	if($postData){
		$usageLimit = Hash::get($postData, 'VoucherCode.0.usage_limit', 0);
	}
	else{
		$usageLimit = 0;
	}

	echo($this->Rumahku->buildInputForm('VoucherCode.0.usage_limit', array_merge(
		$options, 
		array(
			'label'			=> __('Tambah Jumlah Pemakaian'), 
			'type'			=> 'text', 
			'inputClass'	=> 'input_number', 
			'attributes'	=> array(
				'id'		=> 'VoucherCodeUsageLimit', 
				'value'		=> $usageLimit, 
			)
		)
	)));

	echo($this->Rumahku->buildInputForm('Voucher.period_type', array_merge($options, array('label' => __('Masa Berlaku'), 'options' => $periodTypes))));

//	datepicker
	$label = $this->Form->label('Voucher.period_date', __('Tanggal Berlaku *'), array('class' => 'control-label'));
	$input = $this->Html->tag('div', $label, array('class' => 'col-xl-2 col-sm-4 col-xs-12 control-label taright'));
	$input.= $this->Form->input('Voucher.period_date', array(
		'div'		=> array('class' => 'relative col-sm-8 col-xs-12'),
		'label' 	=> FALSE, 
		'required'	=> FALSE, 
		'class'		=> 'form-control date-range', 
	));

	$input = $this->Html->div('form-group', 
		$this->Html->div('row', 
			$this->Html->div('col-sm-8', 
				$this->Html->div('row', $input)
			)
		), 
		array('id' => 'period-date-placeholder')
	);
	echo($input);

	echo($this->element('blocks/vouchers/voucher_detail'));

?>