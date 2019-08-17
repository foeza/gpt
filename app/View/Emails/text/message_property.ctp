<?php 
        $title = $this->Rumahku->filterEmptyField($params, 'Property', 'title');
        $mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');

		$id = $this->Rumahku->filterEmptyField($params, 'Message', 'id');
		$name = $this->Rumahku->filterEmptyField($params, 'Message', 'name');
		$email = $this->Rumahku->filterEmptyField($params, 'Message', 'email');
		$phone = $this->Rumahku->filterEmptyField($params, 'Message', 'phone');
		$message = $this->Rumahku->filterEmptyField($params, 'Message', 'message');

		printf(__('Anda mendapat pesan dari iklan properti yang ditayangkan di %s'), FULL_BASE_URL);
		echo "\n\n";

		if( !empty($mls_id) ) {
	        $label = $this->Property->getNameCustom($params);
	        $slug = $this->Rumahku->toSlug($label);
	        $price = $this->Property->getPrice($params);
	        
			echo $label;
			echo "\n";

			echo $title;
			echo "\n";

			echo $price;
			echo "\n\n";
		}

		printf(__('Pengirim: %s'), $name);
		echo "\n";
		printf(__('Email: %s'), $email);
		echo "\n";
		printf(__('No Telp: %s'), $phone);
		echo "\n";
		printf(__('Pesan: %s'), $message);
?>