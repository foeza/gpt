<?php 

	$options		= !empty($options) ? $options : array();
	$packages		= !empty($packages) ? $packages : array();
	$codeMechanisms	= !empty($codeMechanisms) ? $codeMechanisms : array('manual' => __('Manual'), 'auto' => __('Otomatis'));
	$discountTypes	= !empty($discountTypes) ? $discountTypes : array('nominal' => __('Nominal'), 'percentage' => __('Persentase'));
	$periodTypes	= !empty($periodTypes) ? $periodTypes : array('periodic' => __('Periodik'), 'unlimited' => __('Tidak Terbatas'));
	$applyTo		= !empty($applyTo) ? $applyTo : array('all' => __('Semua Paket Membership'), 'manual' => __('Paket Membership Terpilih'));
	$defaultLength	= 9;
	$currency		= Configure::read('__Site.config_currency_code');

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

//	voucher code
	$label = $this->Form->label('Voucher.code_mechanism', __('Kode Voucher *'), array('class' => 'control-label'));
	$input = $this->Html->tag('div', $label, array('class' => 'col-xl-2 col-sm-4 col-xs-12 control-label taright'));
	$input.= $this->Form->input('Voucher.code_mechanism', array(
		'div'			=> array('class' => 'relative col-sm-3 col-xs-12'),
		'label' 		=> FALSE, 
		'required'		=> FALSE, 
		'class'			=> 'form-control', 
		'options'		=> $codeMechanisms, 
	));
	$input.= $this->Form->input('VoucherCode.0.code', array(
		'id'		=> 'VoucherCodeCode', 
		'div'		=> array('id' => 'voucher-code-placeholder', 'class' => 'relative col-sm-5 col-xs-12'),
		'label' 	=> FALSE, 
		'required'	=> FALSE, 
		'class'		=> 'form-control', 
		'maxlength'	=> $defaultLength
	));

	$input = $this->Html->div('form-group', 
		$this->Html->div('row', 
			$this->Html->div('col-sm-8', 
				$this->Html->div('row', $input)
			)
		)
	);
	echo($input);

//	voucher length
	$label = $this->Form->label('Voucher.length', __('Panjang Kode voucher *'), array('class' => 'control-label'));
	$input = $this->Html->tag('div', $label, array('class' => 'col-xl-2 col-sm-4 col-xs-12 control-label taright'));
	$input.= $this->Form->input('Voucher.length', array(
		'type'		=> 'text', 
		'div'		=> array('class' => 'relative col-sm-8 col-xs-12'),
		'label' 	=> FALSE, 
		'required'	=> FALSE, 
		'class'		=> 'form-control input_number', 
		'default'	=> $defaultLength, 
	));

	$input = $this->Html->div('form-group', 
		$this->Html->div('row', 
			$this->Html->div('col-sm-8', 
				$this->Html->div('row', $input)
			)
		), array(
			'id' => 'voucher-length-placeholder', 
		)
	);
	echo($input);

//	voucher prefix
	$label = $this->Form->label('Voucher.prefix', __('Prefiks Kode Voucher'), array('class' => 'control-label'));
	$input = $this->Html->tag('div', $label, array('class' => 'col-xl-2 col-sm-4 col-xs-12 control-label taright'));
	$input.= $this->Form->input('Voucher.prefix', array(
		'type'		=> 'text', 
		'div'		=> array('class' => 'relative col-sm-3 col-xs-12'),
		'label' 	=> FALSE, 
		'required'	=> FALSE, 
		'class'		=> 'form-control', 
		'maxlength'	=> 2, 
	));

	$input = $this->Html->div('form-group', 
		$this->Html->div('row', 
			$this->Html->div('col-sm-8', 
				$this->Html->div('row', $input)
			)
		), array(
			'id' => 'voucher-prefix-placeholder', 
		)
	);
	echo($input);

	echo($this->Rumahku->buildInputForm('VoucherCode.0.usage_limit', array_merge(
		$options, 
		array(
			'label'			=> __('Maksimum Jumlah Pemakaian *'), 
			'type'			=> 'text', 
			'inputClass'	=> 'input_number', 
			'attributes'	=> array(
				'id'		=> 'VoucherCodeUsageLimit', 
				'default'	=> 1
			)
		)
	)));

	echo($this->Rumahku->buildInputForm('Voucher.period_type', array_merge($options, array('label' => __('Masa Berlaku'), 'options' => $periodTypes))));

//	datepicker
	$label = $this->Form->label('Voucher.period_date', __('Tanggal Berlaku *'), array('class' => 'control-label'));
	$input = $this->Html->tag('div', $label, array('class' => 'col-xl-2 col-sm-4 col-xs-12 control-label taright'));
	$input.= $this->Form->input('Voucher.period_date', array(
		'div'			=> array('class' => 'relative col-sm-8 col-xs-12'),
		'label' 		=> FALSE, 
		'required'		=> FALSE, 
		'class'			=> 'form-control date-range', 
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

	echo($this->Rumahku->buildInputForm('Voucher.apply_to', array_merge(
		$options, 
		array(
			'label'		=> __('Berlaku Untuk *'), 
			'options'	=> $applyTo
		)
	)));

//	discount
	$label = $this->Form->label('Voucher.discount_type', __('Potongan *'), array('class' => 'control-label'));
	$input = $this->Html->tag('div', $label, array('class' => 'col-xl-2 col-sm-4 col-xs-12 control-label taright'));
	$input.= $this->Form->input('Voucher.discount_type', array(
		'div'		=> array('class' => 'relative col-sm-3 col-xs-12'),
		'label' 	=> FALSE, 
		'required'	=> FALSE, 
		'class'		=> 'form-control', 
		'options'	=> $discountTypes, 
		'data-role'	=> 'discount-type-selector', 
	));

	$addon = $this->Html->div('input-group-addon at-right', '%', array('data-role' => 'percentage-code', 'style' => 'display:none;'));
	$addon.= $this->Html->div('input-group-addon at-left', trim($currency), array('data-role' => 'currency-code'));
	$input.= $this->Html->div('relative col-sm-5 col-xs-12', 
		$this->Form->input('Voucher.discount_value', array(
			'type'		=> 'text', 
			'div'		=> array('class' => 'input-group'),
			'label' 	=> FALSE, 
			'required'	=> FALSE, 
			'class'		=> 'form-control input_number input_price has-side-control at-left', 
			'default'	=> 0, 
			'data-role'	=> 'discount-value-input', 
			'before'	=> $addon
		))
	);
	$input = $this->Html->div('form-group', 
		$this->Html->div('row', 
			$this->Html->div('col-sm-8', 
				$this->Html->div('row', $input)
			)
		), 
		array('id' => 'discount-placeholder')
	);
	echo($input);

	echo($this->element('blocks/vouchers/forms/package_detail', array('packages' => $packages, 'discountTypes' => $discountTypes)));

//	echo($this->Rumahku->buildInputToggle('Voucher.status', array_merge($options, array('label' => __('Aktif *')))));

?>