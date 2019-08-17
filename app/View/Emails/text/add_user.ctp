<?php 
		$_config = !empty($_config)?$_config:false;
		$params = !empty($params)?$params:false;

		$company_url = $this->Html->url(array(
			'controller' => 'users',
			'action' => 'login',
			'admin' => true,
		), true);
		$company_name = $this->Rumahku->filterEmptyField($_config, 'UserCompany', 'name');

		$email = $this->Rumahku->filterEmptyField($params, 'email');
		$password = $this->Rumahku->filterEmptyField($params, 'password');
		$group_id = $this->Rumahku->filterEmptyField($params, 'group_id');
		$titleUser = $this->Rumahku->_callTitleUser($group_id);
		$slugUser = $this->Rumahku->_callTitleUser($group_id, true);
		$urlUserManual = sprintf('%s/files/user_manual/prime/%s.doc', FULL_BASE_URL, $slugUser);

		printf(__('Anda telah terdaftar sebagai %s di Perusahaan %s. Silakan login sesuai dengan informasi Akun yang tertera dibawah.'), $titleUser, $company_name);
		echo "\n\n";

		printf(__('Email: %s'), $email);
		echo "\n";
		printf(__('Password: %s'), $password);
		echo "\n\n";
		
		echo __('Anda dapat mengunjungi link berikut untuk melakukan Login:');
		echo "\n";
		echo $company_url;
		echo "\n\n";

		echo __('Anda dapat mengunduh Buku Panduan penggunaan website Kami disini:');
		echo "\n";
		echo $urlUserManual;
?>
