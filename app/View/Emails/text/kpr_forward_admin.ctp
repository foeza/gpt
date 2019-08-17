<?php 
		$_site_name = Configure::read('__Site.site_name');
		$id = $this->Rumahku->filterEmptyField($params, 'KprBank', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'KprBank', 'code');
		$bank_name = $this->Rumahku->filterEmptyField($params, 'Bank', 'name');
		$bank_phone = $this->Rumahku->filterEmptyField($params, 'Bank', 'phone');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');
		$status_KPR = $this->Rumahku->filterEmptyField($params, 'status_KPR');
		$kprBankDate = $this->Rumahku->filterEmptyField($params, $status_KPR);
		$action_date = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'action_date');
		$note = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'note');

		$client = $this->Rumahku->filterEmptyField($params, 'UserClient', 'full_name');
		$no_hp = $this->Rumahku->filterEmptyField($params, 'UserClient', 'no_hp');

		printf(__('Admin %s telah melanjutkan/meneruskan pengajuan KPR %s ke %s'), $_site_name, $code, $bank_name);
	    echo "\n\n";

	    echo $bank_name;
	    echo "\n";

		printf(__('No. Tlp: %s'), $bank_phone);
	    echo "\n\n";

	    if( !empty($mls_id) ) {
	    	$photo = $this->Rumahku->filterEmptyField($params, 'Property', 'photo');
       	 	$title = $this->Rumahku->filterEmptyField($params, 'Property', 'title');
        	$property_path = Configure::read('__Site.property_photo_folder');
	    	$label = $this->Property->getNameCustom($params);
	        $slug = $this->Rumahku->toSlug($label);
	        $price = $this->Property->getPrice($params);

			echo __('Informasi Aplikasi sebagai berikut:');
	    	echo "\n";

	    	echo $label;
	    	echo "\n";

			echo $title;
	    	echo "\n";

			echo $price;
	    	echo "\n\n";
	    }

	    printf(__('Nama Pembeli: %s'), $client);
    	echo "\n";

		printf(__('No. Tlp: %s'), $no_hp);
    	echo "\n\n";

		echo __('Untuk melihat detil permohonan KPR Anda:');
    	echo "\n";

		$link = $domain.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));
    	echo $link;
?>
