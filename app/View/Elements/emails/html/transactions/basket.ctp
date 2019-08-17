<?php

	$record		= empty($record) ? array() : $record;
	$options	= empty($options) ? array() : $options;

	if($record && is_array($record)){
		$tableOptions	= Common::hashEmptyField($options, 'table', array());
		$inlineCSS		= Common::hashEmptyField($options, 'inline_css', false, array('isset' => true));
		$showTotal		= Common::hashEmptyField($options, 'show_total', true, array('isset' => true));
		$showDiscount	= Common::hashEmptyField($options, 'show_discount', true, array('isset' => true));

		if($inlineCSS){
			$cssString = 'border: 1px solid #DFDFE8; padding: 10px; margin-left:-1px';

			$cssOptsHeader = array(
				'counter'	=> array('style' => sprintf('text-align: center; background-color: #F6F6F6; %s', $cssString)), 
				'name'		=> array('style' => sprintf('text-align: left; background-color: #F6F6F6; %s', $cssString)), 
				'price'		=> array('style' => sprintf('text-align: right; background-color: #F6F6F6; %s', $cssString)), 
			);

			$cssOpts = array(
				'counter'	=> array('style' => sprintf('text-align: center; %s', $cssString)), 
				'name'		=> array('style' => sprintf('text-align: left; border: 1px solid #DFDFE8; margin-left:-1px')), 
				'price'		=> array('style' => sprintf('text-align: right; %s', $cssString)), 
			);
		}
		else{
			$cssOptsHeader = array(
				'counter'	=> array('class' => 'tacenter'), 
				'name'		=> array('class' => 'align-left'), 
				'price'		=> array('class' => 'align-right'), 
			);

			$cssOpts = $cssOptsHeader;
		}

		$cssOptsHeader = array_merge_recursive($cssOptsHeader, array(
			'counter'	=> array('width' => '5%', 'nowrap' => true), 
			'name'		=> array('width' => false, 'nowrap' => true), 
			'price'		=> array('width' => 125, 'nowrap' => true), 
		));

		$cssOpts = array_merge_recursive($cssOpts, array(
			'counter'	=> array('width' => '5%', 'valign' => 'top'), 
			'name'		=> array('width' => false, 'valign' => 'top'), 
			'price'		=> array('width' => 125, 'valign' => 'top'), 
		));

	//	TEMPLATING
		$template = $this->Html->tableHeaders(array(
			array('No'			=> $cssOptsHeader['counter']), 
			array('Nama Item'	=> $cssOptsHeader['name']), 
			array('Harga Item'	=> $cssOptsHeader['price']), 
		), array(
			'class' => 'main-head', 
		));

		$discountAmount	= Common::hashEmptyField($record, 'OrderPayment.discount_amount', 0);
		$totalAmount	= Common::hashEmptyField($record, 'OrderPayment.total_amount', 0);
		$baskets		= Hash::extract($record, 'OrderPaymentDetail.{n}.Basket.{n}');
		$discounts		= Hash::extract($record, 'OrderPaymentDetail.{n}.VoucherCode');
		$counter		= 1;

		$cssReset	= array('style' => 'margin: 0; padding: 0;');
		$cssCred	= array('style' => 'margin: 0; padding: 0; color: red; font-size: 12px;');

		if($baskets && is_array($baskets)){
			$tableData = array();

			foreach($baskets as $key => $basket){
				$itemName		= Common::hashEmptyField($basket, 'Basket.item_name', 'N/A');
				$itemQty		= Common::hashEmptyField($basket, 'Basket.number_item', 0);
				$itemPrice		= Common::hashEmptyField($basket, 'Basket.price', 0);
				$itemDiscount	= Common::hashEmptyField($basket, 'Basket.discount', 0);
				$itemTotal		= Common::hashEmptyField($basket, 'Basket.total_price', 0);

				$itemPrice	= $this->Rumahku->getCurrencyPrice($itemPrice, '-');
				$itemTotal	= $this->Rumahku->getCurrencyPrice($itemTotal, '-');

				$innerStyle	= 'padding: 10px 10px 5px 10px;text-align: right;';
				$innerStyle	= $inlineCSS ? sprintf('%sborder-left: 1px solid #DFDFE8;', $innerStyle) : $innerStyle;
				$innerTable	= '
					<tr>
						<td valign="top" style="padding: 10px 10px 5px 10px;">
							'.$this->Html->tag('p', __($itemName), $cssReset).'
						</td>
						<td valign="top" width="125" align="right" style="'.$innerStyle.'">
							'.$this->Html->tag('p', $itemPrice, $cssReset).'
						</td>
					</tr>
				';

				if(floatval($itemDiscount) > 0){
					$itemDiscount	= $this->Rumahku->getCurrencyPrice($itemDiscount, '-');
					$innerStyle		= 'padding: 0px 10px 10px 10px; text-align: right;';
					$innerStyle		= $inlineCSS ? sprintf('%sborder-left: 1px solid #DFDFE8;', $innerStyle) : $innerStyle;

					$innerTable.= '
						<tr>
							<td valign="top" style="padding: 0px 10px 10px 10px;">
								'.$this->Html->tag('p', __('Potongan'), $cssCred).'
							</td>
							<td valign="top" width="125" align="right" style="'.$innerStyle.'">
								'.$this->Html->tag('p', __('- %s', $itemDiscount), $cssCred).'
							</td>
						</tr>
					';
				}

				$innerTable = $this->Html->tag('table', $innerTable, array(
					'width'			=> '100%', 
					'style'			=> 'margin: 0;', 
					'cellpadding'	=> 0, 
					'cellspacing'	=> 0, 
				));

				$tableData[] = array(
					array($this->Html->tag('p', $counter, $cssReset), $cssOpts['counter']), 
					array($innerTable, array_merge($cssOpts['name'], array(
						'colspan'	=> 2, 
						'class'		=> 'padding-vert-1'
					))), 
				);

				$counter++;
			}

			$template.= $this->Html->tableCells($tableData);

		//	if($showDiscount && floatval($discountAmount) > 0){
		//		$discountAmount	= $this->Rumahku->getCurrencyPrice($discountAmount, '-');

		//		$template.= $this->Html->tableCells(array(
		//			'&nbsp;', 
		//			array(__('Potongan'), $cssOpts['name']), 
		//			array($discountAmount, $cssOpts['price'])
		//		), array(
		//			'class' => 'discount-placeholder', 
		//		), null, false, false);
		//	}

			if($showTotal && floatval($totalAmount) > 0){
				$totalAmount = $this->Rumahku->getCurrencyPrice($totalAmount, '-');

				$template.= $this->Html->tableCells(array(
					'&nbsp;', 
					array(__('Total Pembayaran'), $cssOpts['name']), 
					array($totalAmount, $cssOpts['price'])
				), array(
					'class' => 'total-placeholder', 
				), null, false, false);
			}
		}

		$tableOptions = is_array($tableOptions) ? $tableOptions : array();
		$tableOptions = array_replace(array(
			'width'			=> '100%', 
			'class'			=> 'table invoice-basket', 
			'cellspacing'	=> 0, 
			'cellpadding'	=> 0, 
		), $tableOptions);

		echo($this->Html->tag('table', $template, $tableOptions));
	}

?>