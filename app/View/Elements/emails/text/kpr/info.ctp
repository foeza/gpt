<?php
		$dp = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'down_payment');
		$loan_price = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'loan_price');
		$credit_total = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'credit_total');
		$total_first_credit = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'total_first_credit');
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');

		$total_first_credit = $this->Rumahku->getCurrencyPrice($total_first_credit);
		$dp = $this->Rumahku->getCurrencyPrice($dp);
		$loan_price = $this->Rumahku->getCurrencyPrice($loan_price);
   	 	$price = $this->Rumahku->filterEmptyField($params, 'KprBankInstallment', 'property_price');
		$product_units = Common::hashEmptyField($params, 'KprProduct');

   	 	if( !empty($price) ) {
        	$price = $this->Rumahku->getCurrencyPrice($price);
        } else {
        	$price = $this->Property->getPrice($params);
        }

		echo __('Rincian Informasi KPR');
		echo "\n";

		if( !empty($product_units) ) {
			printf(__('Unit ID: %s'), $mls_id);
			echo "\n";
		} else {
			printf(__('Properti ID: %s'), $mls_id);
			echo "\n";
		}

		printf(__('Harga Properti: %s'), $price);
		echo "\n";
		printf(__('Uang Muka: %s'), $dp);
		echo "\n";
		printf(__('Jumlah Pinjaman: %s'), $loan_price);
		echo "\n";
		printf(__('Jangka Waktu: %s Tahun'), $credit_total);
		echo "\n";
		printf(__('Angsuran Per Bulan: %s'), $total_first_credit);
		echo "\n\n";
?>