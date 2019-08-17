<?php 
		$site_url = Configure::read('__Site.site_name');
		$client_id = $this->Rumahku->filterEmptyField($params, 'client_id');
		$token = $this->Rumahku->filterEmptyField($params, 'token');
		$full_name = $this->Rumahku->filterEmptyField($params, 'full_name');
		$company_name = $this->Rumahku->filterEmptyField($params, 'company_name');

		$url = FULL_BASE_URL.$this->Html->url(array(
			'controller' => 'users',
			'action' => 'verify',
			$client_id,
			$token,
			'admin' => false,
			'client' => true,
		));
		$urlUserManual = sprintf('%s/files/user_manual/prime/client.doc', FULL_BASE_URL);

		printf(__('Terima kasih atas kepercayaan Anda dan selamat bergabung di %s.'), $company_name);
		echo "\n\n";

		printf(__('Untuk mendapatkan akses ke seluruh fitur klien di website %s, silahkan klik link dibawah ini.'), $company_name);
		echo "\n";
		echo $url;
		echo "\n\n";

		printf(__('Seluruh staff dan agen %s mengucapkan selamat datang, dan selamat bergabung bersama kami.'), $company_name);
		echo "\n\n";

		echo __('Anda dapat mengunduh Buku Panduan penggunaan website Kami disini:');
		echo "\n";
		echo $urlUserManual;
?>