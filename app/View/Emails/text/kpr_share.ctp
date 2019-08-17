<?php
		$currency = Configure::read('__Site.config_currency_symbol');
		$dataSharingKpr = $this->Rumahku->filterEmptyField($params, 'params_sharing_kpr', 'SharingKpr');
		$datakprBank = $this->Rumahku->filterEmptyField($params, 'params_log_kpr', 'KprBank');
		$datakprBankInstallment = $this->Rumahku->filterEmptyField($params, 'params_log_kpr', 'KprBankInstallment');
		$dataBank = $this->Rumahku->filterEmptyField($params, 'params_log_kpr', 'Bank');

		$logid = $this->Rumahku->filterEmptyField($datakprBank, 'id');
		$bank_code = $this->Rumahku->filterEmptyField($dataBank, 'code');

		$sender_name = $this->Rumahku->filterEmptyField($dataSharingKpr, 'sender_name');
		$mls_id = $this->Rumahku->filterEmptyField($dataSharingKpr, 'mls_id');
		$property_price = $this->Rumahku->filterEmptyField($datakprBankInstallment, 'property_price');
		$dp_price = $this->Rumahku->filterEmptyField($datakprBankInstallment, 'down_payment');
		$loan_price = $this->Rumahku->filterEmptyField($datakprBankInstallment, 'loan_price');
		$dp = $this->Kpr->_setPercentDp($property_price, $dp_price);

		$credit_total = $this->Rumahku->filterEmptyField($datakprBankInstallment, 'credit_total');

		$customPrice = $this->Rumahku->getFormatPrice($property_price);
		$customLoanPrice = $this->Rumahku->getFormatPrice($loan_price);
		$customDpPrice = $this->Rumahku->getFormatPrice($dp_price);
        $customLoanTime = sprintf("%s Bulan (%s Tahun)", $credit_total*12, $credit_total);

		printf(__('Anda mendapatkan email yang berisi tentang info KPR dari %s, silahkan klik link di bawah ini untuk melihat detail data KPR.'), $sender_name);

		echo "\n\n";

		printf(__('Properti ID : %s'), $mls_id);
		echo "\n";		

		printf(__('Harga Properti : %s %s'), $currency, $property_price);
		echo "\n";

		printf(__('Uang Muka : %s %% ( %s %s )'), $dp, $currency, $customDpPrice);
		echo "\n";

		printf(__('Jumlah Pinjaman : %s %s'), $currency, $customLoanPrice);
		echo "\n";

		printf(__('Jangka Waktu : %s'), $customLoanTime);
		echo "\n\n";

		$link = $this->Html->url(array(
			'controller' => 'kpr',
			'action' => 'bank_calculator',
			'slug' => 'kalkulator-kpr',
			'bank_code' => $bank_code,
			'mls_id' => $mls_id,
			'logid' => $logid,
		), true);
		
		echo $this->Html->link(__('Lihat Rincian'), $link, array(
			'style' => 'text-decoration: none; cursor: pointer;'
		));
?>