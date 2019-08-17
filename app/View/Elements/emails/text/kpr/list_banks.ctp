<?php
		
		$bank_name = $this->Rumahku->filterEmptyField($val, 'Bank', 'name');
		$kpr_bank_installments = $this->Rumahku->filterEmptyField($val, 'KprBank', 'KprBankInstallment');
		$kpr_bank_installment = !empty($kpr_bank_installments[0]) ? $kpr_bank_installments[0] : false;

		$header_title = $this->Html->tag('h1', __('Rincian Properti KPR %s', $bank_name), array(
			'style' => 'border-bottom: 1px solid #ccc; font-size: 18px; font-weight: 700; padding-bottom: 10px; color: #000;'
		));

		$property_price = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'property_price');
		$down_payment = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'down_payment');
		$loan_price = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'loan_price');
		$total_first_credit = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'total_first_credit');
		$credit_total = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'credit_total');
		$interest_rate_fix = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'interest_rate_fix');
		$interest_rate_cabs = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'interest_rate_cabs');
		$periode_fix = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'periode_fix');
		$periode_cab = $this->Rumahku->filterEmptyField($kpr_bank_installment, 'KprBankInstallment', 'periode_cab');

		$customDp = $this->Rumahku->getCurrencyPrice($down_payment, '-');
		$customPrice = $this->Rumahku->getCurrencyPrice($property_price, '-'); 
		$customLoanPrice = $this->Rumahku->getCurrencyPrice($loan_price, '-');
		$customFirstCredit = $this->Rumahku->getCurrencyPrice($total_first_credit, '-');

		echo  __('Rincian KPR %s', $bank_name);
		echo "\n";
		echo "\n";
		printf(__('Harga Properti %s'), $customPrice);
		echo "\n";
		printf(__('Jumlah Pinjaman %s'), $customLoanPrice);
		echo "\n";
		printf(__('Cicilan %s'), $customFirstCredit);
		echo "\n";
		printf(__('Jangka Waktu %s'), sprintf('%s Tahun', $credit_total));
?>

