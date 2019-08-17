<?php
		$data = $this->request->data;
		$type = !empty($type) ? $type : 'down_payment';
		$disabled = !empty($disabled) ? TRUE : FALSE;
		$sliderClass = !empty($sliderClass) ? $sliderClass : false;
		$target_label = !empty($target_label) ? $target_label : false;
		$target_percent = !empty($target_percent) ? $target_percent : false;
		$attributes = !empty($attributes) ? $attributes : false;
		$label = !empty($label) ? $label : false;
		$down_payment = $this->Rumahku->filterEmptyField($data, 'down_payment');
		$down_payment = $this->Rumahku->filterEmptyField($data, 'Kpr', 'down_payment', $down_payment);
		$periode_installment = $this->Rumahku->filterEmptyField($data, 'periode_installment');
		$periode_installment = $this->Rumahku->filterEmptyField($data, 'Kpr', 'periode_installment', $periode_installment);
		
		$data_url = $this->Rumahku->filterEmptyField($attributes, 'data-url');
		$data_form = $this->Rumahku->filterEmptyField($attributes, 'data-form');
		$data_wrapper_write = $this->Rumahku->filterEmptyField($attributes, 'data-wrapper-write');

		echo $this->Html->tag('div', FALSE, array(
			'class' => $sliderClass,
			'data-url' => $data_url,
			'data-form' => $data_form,
			'disabled' => $disabled,
			'target-label' => $target_label,
			'target-percent' => $target_percent,
			'data-wrapper-write' => $data_wrapper_write,
		));

		switch ($type) {
			case 'down_payment':
				echo $this->Html->tag('div', false, array(
					'class' => 'down-payment',
					'data-rel' => $down_payment
				));
				break;
			
			case 'periode_installment':
				echo $this->Html->tag('div', false, array(
					'class' => 'credit-total',
					'data-rel' => $periode_installment
				));
				break;
		}

		if($label){
			$left = $this->Rumahku->filterEmptyField($label, 'left');
			$right = $this->Rumahku->filterEmptyField($label, 'right');

			$leftCustom = $this->Html->tag('span', $left, array(
				'class' => 'left'
			));
			$rightCustom = $this->Html->tag('span', $right, array(
				'class' => 'right'
			));

			echo $this->Html->tag('div', $this->Html->tag('div', $this->Html->tag('div', sprintf('%s%s', $leftCustom, $rightCustom), array(
				'class' => 'slider-label',
			)), array(
				'class' => 'col-sm-12',
			)), array(
				'class' => 'row',
			));
		}
?>