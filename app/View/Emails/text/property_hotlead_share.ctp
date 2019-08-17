<?php
		$data = $params['SharingProperty'];
		$property = $params['propertyData'];

		$id = $this->Rumahku->filterEmptyField($property, 'Property', 'id');
		$link_detail = FULL_BASE_URL.$this->Html->url(array(
			'controller' => 'properties',
			'action' => 'report_hotlead_detail',
			$id,
		));

		printf(__('Anda mendapatkan email yang berisi tentang laporan hotlead properti dari %s, silahkan klik link di bawah ini untuk melihat detail laporan.'), $data['sender_name']);
		echo "\n\n";
		
		foreach( $params['values'] as $key => $value ) {
			$date = $this->Rumahku->filterEmptyField($value, 'Message', 'created');
            $name = $this->Rumahku->filterEmptyField($value, 'Message', 'name', '-');
            $email = $this->Rumahku->filterEmptyField($value, 'Message', 'email', '-');
            $no_hp = $this->Rumahku->filterEmptyField($value, 'Message', 'phone', '-');
            $message = $this->Rumahku->filterEmptyField($value, 'Message', 'message', '-');
            $utm = $this->Rumahku->filterEmptyField($value, 'Message', 'utm', '-');
			$customDate = $this->Rumahku->formatDate($date, 'd M Y');
			
			printf(__('Tanggal : %s')."\n", $customDate);
			printf(__('Nama : %s')."\n", $name);
			printf(__('Email : %s')."\n", $email);
			printf(__('No. HP : %s')."\n", $no_hp);
			printf(__('Message : %s')."\n", $message);
			printf(__('UTM : %s')."\n", $utm);
			
			echo "\n\n";
		}

		printf(__('Klik Link : %s'), $link_detail);
?>