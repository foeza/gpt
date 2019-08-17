<?php

	$packages		= !empty($packages) ? $packages : array();
	$discountTypes	= !empty($discountTypes) ? $discountTypes : array('nominal' => __('Nominal'), 'percentage' => __('Persentase'));
	$totalInputs	= count($this->Rumahku->filterEmptyField($this->request->data, 'VoucherDetail'));
	$totalInputs	= $totalInputs < 1 ? 1 : $totalInputs;
	$inputs			= '';
	$currency		= Configure::read('__Site.config_currency_code');

	for($i = 0; $i < $totalInputs; $i++){
		$label = $this->Form->label('VoucherDetail.'.$i.'.membership_package_id', $i + 1, array('class' => 'control-label'));
		$input = $this->Html->tag('div', $label, array('class' => 'col-sm-1 control-label taright'));
		$input.= $this->Form->input('VoucherDetail.'.$i.'.membership_package_id', array(
			'div'		=> array('class' => 'relative col-sm-4 col-xs-12'),
			'label' 	=> FALSE, 
			'required'	=> FALSE, 
			'class'		=> 'form-control', 
			'data-role'	=> 'membership-package-selector', 
			'options'	=> $packages
		));
		$input.= $this->Form->input('VoucherDetail.'.$i.'.discount_type', array(
			'div'		=> array('class' => 'relative col-sm-2 col-xs-12'),
			'label' 	=> FALSE, 
			'required'	=> FALSE, 
			'class'		=> 'form-control', 
			'options'	=> $discountTypes, 
			'data-role'	=> 'discount-type-selector', 
		));

		$addon = $this->Html->div('input-group-addon at-right', '%', array('data-role' => 'percentage-code', 'style' => 'display:none;'));
		$addon.= $this->Html->div('input-group-addon at-left', trim($currency), array('data-role' => 'currency-code'));
		$input.= $this->Html->div('relative col-sm-3 col-xs-12', 
			$this->Form->input('VoucherDetail.'.$i.'.discount_value', array(
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
		$input.= $this->Html->div('relative col-sm-2 col-xs-12', $this->Html->link('Hapus', FALSE, array('data-role' => 'remove-membership', 'class' => 'btn btn-lg default')));
		$input = $this->Html->div('form-group', 
			$this->Html->div('row', 
				$this->Html->div('col-sm-12', 
					$this->Html->div('row', $input)
				)
			)
		);

		$inputs.= $input;
	}

	$toolbox	= $this->Html->link('Tambah', FALSE, array('data-role' => 'add-membership', 'class' => 'btn green floright'));
	$toolbox	= $this->Html->div('form-group', $this->Html->div('row', $this->Html->div('col-xs-12', $toolbox)));
	$content	= $this->Html->tag('h2', __('Detail Potongan'), array('class' => 'sub-heading')).$toolbox.$inputs;
	$content	= $this->Html->div('hide', $content, array('id' => 'package-placeholder'));

	echo($content);

?>