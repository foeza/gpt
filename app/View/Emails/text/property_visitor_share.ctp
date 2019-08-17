<?php
		$data = $params['SharingProperty'];
		$property = $params['propertyData'];

		$id = $this->Rumahku->filterEmptyField($property, 'Property', 'id');
		$link_detail = FULL_BASE_URL.$this->Html->url(array(
			'controller' => 'properties',
			'action' => 'report_visitor_detail',
			$id,
		));

		printf(__('Anda mendapatkan email yang berisi tentang laporan pengunjung properti dari %s, silahkan klik link di bawah ini untuk melihat detail laporan.'), $data['sender_name']);
		echo "\n\n";
		
		foreach( $params['values'] as $key => $value ) {
			$visit_date = $this->Rumahku->filterEmptyField($value, 'PropertyView', 'created');
            $name = $this->Rumahku->filterEmptyField($value, 'User', 'full_name', '-');
            $email = $this->Rumahku->filterEmptyField($value, 'User', 'email', '-');
            $no_hp = $this->Rumahku->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
            $browser = $this->Rumahku->filterEmptyField($value, 'PropertyView', 'browser', '-');
            $utm = $this->Rumahku->filterEmptyField($value, 'PropertyView', 'utm', '-');

			$customVisitDate = $this->Rumahku->formatDate($visit_date, 'd M Y');
			
			printf(__('Tanggal Kunjung : %s')."\n", $customVisitDate);
			printf(__('Nama : %s')."\n", $name);
			printf(__('Email : %s')."\n", $email);
			printf(__('No. HP : %s')."\n", $no_hp);
			printf(__('Browser : %s')."\n", $browser);
			printf(__('UTM : %s')."\n", $utm);
			
			echo "\n\n";
		}

		printf(__('Klik Link : %s'), $link_detail);
?>