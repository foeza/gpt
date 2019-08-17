<?php 
		$client = $this->Rumahku->filterEmptyField($params, 'User', 'full_name');
		$email = $this->Rumahku->filterEmptyField($params, 'User', 'email');
		
		$bank_name = $this->Rumahku->filterEmptyField($params, 'KprApplicationApi', 'rekening_bank');
		$account_name = $this->Rumahku->filterEmptyField($params, 'KprApplicationApi', 'rekening_nama_akun');
		$account_number = $this->Rumahku->filterEmptyField($params, 'KprApplicationApi', 'no_rekening');
		$no_npwp = $this->Rumahku->filterEmptyField($params, 'KprApplicationApi', 'no_npwp');

		printf(__('Nama Agen: %s'), $client);
		echo "\n";
		printf(__('Email: %s'), $email);
		echo "\n";

		printf(__('Nama Bank: %s'), $bank_name);
		echo "\n";
		printf(__('Nama Rekening: %s'), $account_name);
		echo "\n";
		printf(__('No. Rekening: %s'), $account_number);
		echo "\n";
		printf(__('No. MPWP: %s'), $no_npwp);
		echo "\n\n";
?>