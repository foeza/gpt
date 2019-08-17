<?php 
		$data_sold = $this->Rumahku->filterEmptyField($propertySold, 'PropertySold');

		$dataAsset = $this->Rumahku->filterEmptyField($value, 'PropertyAsset');

		$price = $this->Rumahku->filterEmptyField($data_sold, 'price_sold');
		$sold_date = $this->Rumahku->filterEmptyField($data_sold, 'sold_date');
		$end_date = $this->Rumahku->filterEmptyField($data_sold, 'end_date');
		$sold_by_name = $this->Rumahku->filterEmptyField($data_sold, 'sold_by_name');
		$note = $this->Rumahku->filterEmptyField($data_sold, 'note');
		$period_name = $this->Rumahku->filterEmptyField($data_sold, 'Period', 'name');

		$lot_unit = $this->Rumahku->filterEmptyField($value, 'LotUnit', 'slug');
		$lot_unit_id = $this->Rumahku->filterEmptyField($value, 'PropertyAsset', 'lot_unit_id');
		$lot_unit = $this->Rumahku->filterEmptyField($dataAsset, 'LotUnit', 'slug', $lot_unit);
		$lot_unit = ucwords($lot_unit);

		$action_id = $this->Rumahku->filterEmptyField($propertySold, 'PropertyAction', 'id');
		$action_name = $this->Rumahku->filterEmptyField($propertySold, 'PropertyAction', 'inactive_name');
		$currency = $this->Rumahku->filterEmptyField($propertySold, 'Currency', 'symbol');

		$name = $this->Rumahku->filterEmptyField($propertySold, 'User', 'full_name', $sold_by_name);

		$customPrice = $this->Rumahku->getCurrencyPrice($price, 0, $currency);
		// $customDate = $this->Rumahku->getCombineDate($sold_date, $end_date, false, false);
		$customDate = date('d M Y', strtotime($sold_date));
		$lot_type = $this->Property->getTypeLot($value);
		
		if( !empty($lot_unit) && $lot_type ) {
			$customPrice = sprintf('%s / %s', $customPrice, $lot_unit);
		}

		if(!empty($period_name)){
			$customPrice .= ' '.$period_name;
		}
?>
<div class="mt20">
	<?php 
			echo $this->Html->tag('div', sprintf(__('Harga %s : %s'), $action_name, $this->Html->tag('strong', $customPrice)));
			echo $this->Html->tag('div', sprintf(__('Tgl %s : %s'), $action_name, $this->Html->tag('strong', $customDate)));

			if(!empty($name)){
				echo $this->Html->tag('div', sprintf(__('%s Oleh : %s'), $action_name, $this->Html->tag('strong', $name)));
			}

			if( !empty($note) ) {
				echo $this->Html->tag('div', sprintf(__('Keterangan : %s'), $this->Html->tag('strong', $note)));
			}
	?>
</div>