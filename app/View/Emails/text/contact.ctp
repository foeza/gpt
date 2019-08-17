<?php
		$senderName = $this->Rumahku->filterEmptyField($params, 'Contact', 'name');
		$senderEmail = $this->Rumahku->filterEmptyField($params, 'Contact', 'email');
		$senderPhone = $this->Rumahku->filterEmptyField($params, 'Contact', 'phone');
		$senderMessage = $this->Rumahku->filterEmptyField($params, 'Contact', 'message');
		$messageSubject = $this->Rumahku->filterEmptyField($params, 'Contact', 'subject');

		printf(__('Anda mendapatkan pesan baru dari %s di %s.'), $senderName, FULL_BASE_URL);
		echo "\n\n";

		printf('%s : %s', __('Subyek'), $messageSubject);
		echo "\n";

		printf('%s : %s', __('Pengirim'), $senderName);
		echo "\n";

		printf('%s : %s', __('Email'), $senderEmail);
		echo "\n";

		printf('%s : %s', __('No. Telp'), $senderPhone);
		echo "\n";

		printf('%s : %s', __('Pesan'), $senderMessage);
		echo "\n\n";
?>