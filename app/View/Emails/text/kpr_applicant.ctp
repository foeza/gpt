<?php
		$prime_url = FULL_BASE_URL;
		$currency = Configure::read('__Site.config_currency_symbol');
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Kpr', 'mls_id');
		$property_price = $this->Rumahku->filterEmptyField($params, 'Kpr', 'property_price');
		$kpr_application = $this->Rumahku->filterEmptyField($params, 'Kpr', 'KprApplication');
		$ktp = $this->Rumahku->filterEmptyField($kpr_application, 'KprApplication', 'ktp');
		$name = $this->Rumahku->filterEmptyField($kpr_application, 'KprApplication', 'name');
		$email = $this->Rumahku->filterEmptyField($kpr_application, 'KprApplication', 'email');
		$phone = $this->Rumahku->filterEmptyField($kpr_application, 'KprApplication', 'phone');
		$dp = $this->Rumahku->filterEmptyField($params, 'Kpr', 'down_payment');
		$loan_price = $this->Rumahku->filterEmptyField($params, 'Kpr', 'loan_price');
		$credit_total = $this->Rumahku->filterEmptyField($params, 'Kpr', 'credit_total');
		$id = $this->Rumahku->filterEmptyField($params, 'kpr_id');
		$code = $this->Rumahku->filterEmptyField($params, 'Kpr', 'code');

		$customDp = $this->Rumahku->getFormatPrice($dp, 
			'-', $currency);
		$customPrice = $this->Rumahku->getFormatPrice($property_price, 
			'-', $currency); 
		$customLoanPrice = $this->Rumahku->getFormatPrice($loan_price, 
			'-', $currency);

		$customMlsId = $mls_id;
		if( $mls_id != '-' ) {
			$customMlsId = $this->Html->link($mls_id, $bank_url.$this->Html->url(array(
				'controller' => 'properties',
				'action' => 'detail',
				$mls_id,
				'application' => $id,
				'admin' => true,
			)), array(
				'target' => '_blank',
			));
		}

		echo __('Pemohon');
		echo "\n\n";

		printf(__('Tanggal Pengajuan : %s'), date('d/m/Y'));
		echo "\n";

		printf(__('KTP : %s'), $ktp);	
		echo "\n";

		printf(__('Nama : %s'), $name);	
		echo "\n";

		printf(__('Email : %s'), $email);	
		echo "\n";

		printf(__('No. Telp : %s'), $phone);	
		echo "\n\n";

		echo __('Rincian Properti KPR');
		echo "\n\n";

		printf(__('Properti ID : %s'), $customMlsId);
		echo "\n";

		printf(__('Harga Properti : %s'), $customPrice);	
		echo "\n";

		printf(__('Uang Muka : %s'), $customDp);	
		echo "\n";

		printf(__('Jumlah Pinjaman : %s'), $customLoanPrice);	
		echo "\n";

		printf(__('Jangka Waktu : %s Thn'), $credit_total);	
		echo "\n\n";

		echo __('Untuk melihat detil permohonan KPR dan menindak lanjuti permohonan, klik di sini:');
		echo "\n\n";

		$link = $prime_url.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));
		echo __('Lihat Detil Permohonan');
		echo "\n";
		echo $link;
?>