<?php 
		$data = $this->Rumahku->filterEmptyField($params, 'Message');
		$name = $this->Rumahku->filterEmptyField($params, 'Message', 'name', false, true, 'ucwords');
		$email = $this->Rumahku->filterEmptyField($params, 'Message', 'email');
		$phone = $this->Rumahku->filterEmptyField($params, 'Message', 'phone');
		$message = $this->Rumahku->filterEmptyField($params, 'Message', 'message');
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');

		$group_id = $this->Rumahku->filterEmptyField($params, 'User', 'group_id');
		$parent_id = $this->Rumahku->filterEmptyField($params, 'User', 'parent_id');

		$full_base_url = $this->Rumahku->filterEmptyField($params, 'Message', 'full_base_url', FULL_BASE_URL, true, 'link-target-blank');
		$userCompany = $this->Rumahku->filterEmptyField($params, 'ToUser', 'UserCompany');
		$domain = $this->Rumahku->filterEmptyField($userCompany, 'domain', false, false, false, 'trailing_slash');
		$category = $this->Rumahku->filterEmptyField($params, 'MessageCategory', 'name');

		if(!empty($params['Property'])){
			$title = sprintf(__('Anda mendapat pesan dari iklan properti yang ditayangkan di %s'), $full_base_url);
		}else{
			$title = sprintf(__('Anda mendapat pesan dari %s di %s'), $name, $full_base_url);
		}

		echo $title;
		echo "\n\n";
		
		if( $group_id == 1 || empty($parent_id) ) {
			$from_id = $this->Rumahku->filterEmptyField($params, 'Message', 'from_id');
			$from_slug = $this->Rumahku->filterEmptyField($params, 'FromUser', 'username');

			if($domain){
				$readUrl = $domain.$this->Html->url(array(
					'controller' => 'users',
					'action' => 'profile',
					$from_id,
					$from_slug,
					'admin' => false,
				));
			}else{
		        $readUrl = $this->Html->url(array(
					'controller' => 'users',
					'action' => 'profile',
					$from_id,
					$from_slug,
					'admin' => false,
				), true);
			}
		} else {
			if($domain){
				$readUrl = $domain.$this->Html->url(array(
					'controller' => 'messages',
					'action' => 'index',
					'admin' => true,
				));
			}else{
		        $readUrl = $this->Html->url(array(
					'controller' => 'messages',
					'action' => 'index',
					'admin' => true,
				), true);
			}
		}

		// printf(__('Anda mendapat pesan dari %s di %s'), $name, $full_base_url);
		// echo "\n\n";

		printf(__('Pengirim: %s'), $name);
		echo "\n";

		if( !empty($category) ) {
			echo __('Subject: %s', $category);
			echo "\n";
		}

		printf(__('Email: %s'), $email);
		echo "\n";
		printf(__('No Telp: %s'), $phone);
		echo "\n";
		printf(__('Pesan: %s'), $message);

		if( !empty($readUrl) ) {
			echo "\n\n";
			printf(__('Balas Pesan: %s'), $readUrl);
		}

		if(!empty($mls_id)){
			echo "\n";
			echo $this->element('emails/text/properties/info');
		}
?>