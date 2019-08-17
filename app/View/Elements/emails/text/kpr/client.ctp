<?php 
		$client = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_name');
		$client = $this->Rumahku->filterEmptyField($params, 'UserClient', 'full_name', $client);
		$no_hp = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_hp');
		$no_hp = $this->Rumahku->filterEmptyField($params, 'UserClient', 'no_hp', $no_hp);
		$email = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_email', '-');
		$email = $this->Rumahku->filterEmptyField($params, 'UserClient', 'email', $email);

		printf(__('Nama Klien: %s'), $client);
		echo "\n";
		printf(__('No. Tlp: %s'), $no_hp);
		echo "\n";
		printf(__('Email: %s'), $email);
		echo "\n\n";
?>