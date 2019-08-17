<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'Message', 'id');
		$name = $this->Rumahku->filterEmptyField($params, 'Message', 'name');
		$email = $this->Rumahku->filterEmptyField($params, 'Message', 'email');
		$phone = $this->Rumahku->filterEmptyField($params, 'Message', 'phone');
		$message = $this->Rumahku->filterEmptyField($params, 'Message', 'message');

		printf(__('Anda mendapat pesan dari pengunjung yang melihat profil Anda di %s'), FULL_BASE_URL);
		echo "\n\n";

		printf(__('Pengirim: %s'), $name);
		echo "\n";
		printf(__('Email: %s'), $email);
		echo "\n";
		printf(__('No Telp: %s'), $phone);
		echo "\n";
		printf(__('Pesan: %s'), $message);
?>