<?php
		$data = $params['SharingProperty'];
		$property = $params['propertyData'];

		$id = $this->Rumahku->filterEmptyField($property, 'Property', 'id');
		$link_detail = FULL_BASE_URL.$this->Html->url(array(
			'controller' => 'properties',
			'action' => 'report_detail',
			$id,
		));

		printf(__('Anda mendapatkan email yang berisi tentang laporan properti dari %s, silahkan klik link di bawah ini untuk melihat detail laporan.'), $data['sender_name']);
		echo "\n\n";
		
		foreach( $params['values'] as $row ) {
			printf(__('Kota : %s')."\n", $row[0]);
			printf(__('Pengunjung : %s')."\n", $row[1]);
			printf(__('Lead : %s')."\n", $row[2]);
			printf(__('Hot Lead : %s')."\n", $row[3]);

			echo "\n\n";
		}
		
		printf(__('Klik Link : %s'), $link_detail);
?>