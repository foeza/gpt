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

		echo $this->Html->tag('p', sprintf(__('Terima kasih atas kepercayaan Anda dan selamat bergabung di %s.'), $company_name), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;',
		));
		echo $this->Html->tag('p', sprintf(__('Untuk mendapatkan akses ke seluruh fitur klien di website %s, silahkan klik link dibawah ini.'), $company_name), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;',
		));
		echo $this->Html->tag('p', $this->Html->link($url, $url, array(
				'style'=> 'color: #06c; text-decoration: none; font-size: 14px;', 
				'target'=> '_blank'
			)), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;',
		));
		echo $this->Html->tag('p', sprintf(__('Seluruh staff dan agen %s mengucapkan selamat datang, dan selamat bergabung bersama kami.'), $company_name), array(
			'style' => 'color: #303030; font-size: 14px; margin: 20px 0; line-height: 20px;',
		));
		
		echo $this->Html->tag('p', __('Anda dapat mengunduh Buku Panduan penggunaan website Kami disini:'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;',
		));
		echo $this->Html->tag('p', $this->Html->link($urlUserManual, $urlUserManual, array(
				'style'=> 'color: #06c; text-decoration: none; font-size: 14px;', 
				'target'=> '_blank'
			)), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;',
		));
?>