<?php
class EbrosurHelper extends AppHelper {
	var $helpers = array(
		'Rumahku', 'Html', 'Number',
	);

	function _callGetPrice( $data ) {
		$price = $this->Rumahku->filterEmptyField($data, 'UserCompanyEbrochure', 'property_price');
		$note_price = $this->Rumahku->filterEmptyField($data, 'UserCompanyEbrochure', 'note_price');
		$currency = $this->Rumahku->filterEmptyField($data, 'Currency', 'symbol');
		$lot_name = $this->Rumahku->filterEmptyField($data, 'LotUnit', 'slug');
		$period_name = $this->Rumahku->filterEmptyField($data, 'Period', 'name');

		$price = $this->Rumahku->getCurrencyPrice($price, 0, $currency);

		if(!empty($lot_name)){
			$price .= ' / '.ucfirst($lot_name);
		}
		if(!empty($period_name)){
			$price .= ' '.$period_name;
		}

		if(!empty($note_price)){
			$price .= ' '.$note_price;
		}

		return $price;
	}
}