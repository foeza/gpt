<?php
		$columns  = 'col-xs-12 col-sm-6 col-md-6';

		$discountAmount   = Common::hashEmptyField($record, 'UserIntegratedOrderAddon.discount_price', 0);
		$totalAmount 	  = Common::hashEmptyField($record, 'UserIntegratedOrderAddon.total_price', 0);

		$R123packagePrice = Common::hashEmptyField($record, 'UserIntegratedAddonPackageR123.price', 0);
		$R123packageName  = Common::hashEmptyField($record, 'UserIntegratedAddonPackageR123.name');

		$OLXpackagePrice  = Common::hashEmptyField($record, 'UserIntegratedAddonPackageOLX.price', 0);
		$OLXpackageName   = Common::hashEmptyField($record, 'UserIntegratedAddonPackageOLX.name');

		$voucherCode 	  = Common::hashEmptyField($record, 'VoucherCode.code', 'N/A');

		echo($this->Html->tag('h3', __('Detail Invoice'), array('class' => 'custom-heading')));

		$template = $this->Html->tag('div', $this->Html->tag('b', __('Nomor Invoice')), array('class' => $columns));
		$template.= $this->Html->tag('div', $this->Html->tag('b', $invoiceNumber), array('class' => $columns));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);
		
		if ($addon_r123) {
			$template = $this->Html->tag('div', $this->Html->tag('b', __('Nama Membership Rumah 123')), array('class' => $columns));
			$template.= $this->Html->tag('div', $R123packageName, array('class' => $columns));
			$template = $this->Html->tag('div', $template, array('class' => 'row'));

			echo($template);

			$template = $this->Html->tag('div', $this->Html->tag('b', sprintf('%s (%s)', __('Harga Membership'), $currency)), array('class' => $columns));
			$template.= $this->Html->tag('div', 
				$this->Number->currency($R123packagePrice, '', array(
					'places' => $places, 
				)), 
				array(
					'class' => $columns, 
				)
			);

			$template = $this->Html->tag('div', $template, array('class' => 'row'));

			echo($template);
		}

		if ($addon_olx) {
			$template = $this->Html->tag('div', $this->Html->tag('b', __('Nama Membership OLX')), array('class' => $columns));
			$template.= $this->Html->tag('div', $OLXpackageName, array('class' => $columns));
			$template = $this->Html->tag('div', $template, array('class' => 'row'));

			echo($template);
		}

		$template = $this->Html->tag('div', $this->Html->tag('b', __('Voucher')), array('class' => $columns));
		$template.= $this->Html->tag('div', $voucherCode, array('class' => $columns));
		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

		$discountAmount = $this->Number->currency($discountAmount, '', array('places' => $places));
		$discountAmount = sprintf('(%s)', $discountAmount);

		$template = $this->Html->tag('div', $this->Html->tag('b', sprintf('%s (%s)', __('Potongan'), $currency)), array('class' => $columns));
		$template.= $this->Html->tag('div', $discountAmount, array('class' => $columns));

		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

		echo($this->Html->tag('hr'));
		$template = $this->Html->tag('div', $this->Html->tag('b', sprintf('%s (%s)', __('Total'), $currency)), array('class' => $columns));
		$template.= $this->Html->tag('div', 
			$this->Number->currency($totalAmount, '', array(
				'places' => $places, 
			)), 
			array(
				'class' => $columns, 
			)
		);

		$template = $this->Html->tag('div', $template, array('class' => 'row'));

		echo($template);

?>