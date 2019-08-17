<?php 
		$for_staff = !empty($for_staff)?$for_staff : false;
		$credit_date = $this->Rumahku->filterEmptyField($params, 'KprBankCreditAgreement', 'action_date');
		$note = $this->Rumahku->filterEmptyField($params, 'KprBankCreditAgreement', 'note', false, false, 'EOL');
		$date = $this->Rumahku->formatDate($credit_date, 'd M Y');
		$time = $this->Rumahku->formatDate($credit_date, 'H:i');

		if($for_staff){
			$staff_bank = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_name');
			$staff_phone = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_hp');
			$title = __('Nama Klien');
		}else{
			$staff_bank = $this->Rumahku->filterEmptyField($params, 'KprBankCreditAgreement', 'staff_name');
			$staff_phone = $this->Rumahku->filterEmptyField($params, 'KprBankCreditAgreement', 'staff_phone');
			$title = __('Staff / Bertemu dengan : ');
		}

		printf(__('Tanggal. : %s'), $date);
		echo "\n";
		printf(__('Pukul. : %s'), $time);
		echo "\n";
		printf(__('%s %s'), $title, $staff_bank);
		echo "\n";
		printf(__('No. Handphone : %s'), $staff_phone);
		echo "\n";
		printf(__('Lokasi & Keterangan : %s'), $note);
		echo "\n\n";
?>