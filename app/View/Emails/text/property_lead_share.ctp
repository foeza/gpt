<?php
		$data = $params['SharingProperty'];
		$property = $params['propertyData'];

		$id = $this->Rumahku->filterEmptyField($property, 'Property', 'id');
		$link_detail = FULL_BASE_URL.$this->Html->url(array(
			'controller' => 'properties',
			'action' => 'report_lead_detail',
			$id,
		));

		printf(__('Anda mendapatkan email yang berisi tentang laporan lead properti dari %s, silahkan klik link di bawah ini untuk melihat detail laporan.'), $data['sender_name']);
		echo "\n\n";
		
		foreach( $params['values'] as $key => $value ) {
			$date = $this->Rumahku->filterEmptyField($value, 'PropertyLead', 'created');
			$name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name', '-');
			$email = $this->Rumahku->filterEmptyField($value, 'User', 'email', '-');
			$no_hp = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
			$browser = $this->Rumahku->filterEmptyField($value, 'PropertyLead', 'browser', '-');
			$utm = $this->Rumahku->filterEmptyField($value, 'PropertyLead', 'utm', '-');
			$customDate = $this->Rumahku->formatDate($date, 'd M Y');
			
			printf(__('Tanggal : %s')."\n", $customDate);
			printf(__('Nama : %s')."\n", $name);
			printf(__('Email : %s')."\n", $email);
			printf(__('No. HP : %s')."\n", $no_hp);
			printf(__('Browser : %s')."\n", $browser);
			printf(__('UTM : %s')."\n", $utm);
			
			echo "\n\n";
		}

		printf(__('Klik Link : %s'), $link_detail);
?>