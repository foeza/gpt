<?php 
        $dataLogin = Configure::read('User.data');
        $property_path = Configure::read('__Site.property_photo_folder');
        $photo = $this->Rumahku->filterEmptyField($params, 'Property', 'photo');
        $title = $this->Rumahku->filterEmptyField($params, 'Property', 'title');
        $mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');

		$id = $this->Rumahku->filterEmptyField($params, 'CrmProject', 'id');
		$client = $this->Rumahku->filterEmptyField($params, 'CrmProject', 'client_name');
		$client_hp = $this->Rumahku->filterEmptyField($params, 'CrmProject', 'client_hp');
		$user_login_name = $this->Rumahku->filterEmptyField($dataLogin, 'full_name');

        $readUrl = $this->Html->url(array(
			'controller' => 'crm',
			'action' => 'project_detail',
			$id,
			'admin' => true,
		), true);

		printf(__('Klien %s telah ditambahkan kedalam Project CRM Anda oleh %s. Lakukan aktivitas untuk meningkatkan penjualan Properti Anda.'), $client, $user_login_name);
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
		}
?>