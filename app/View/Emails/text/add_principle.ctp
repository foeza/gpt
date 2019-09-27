<?php 
		$_config = !empty($_config)?$_config:false;
		$params = !empty($params)?$params:false;

		$email = $this->Rumahku->filterEmptyField($params, 'email');
		$password = $this->Rumahku->filterEmptyField($params, 'password');
		$group_id = $this->Rumahku->filterEmptyField($params, 'group_id');
		$titleUser = $this->Rumahku->_callTitleUser($group_id);
		$slugUser = $this->Rumahku->_callTitleUser($group_id, true);
		$urlUserManual = sprintf('%s/files/user_manual/prime/%s.doc', FULL_BASE_URL, $slugUser);

		$site_name = Configure::read('__Site.site_name');
		$site_email = Configure::read('__Site.send_email_from');
		$site_wa = Configure::read('__Site.site_wa');

		printf(__('Anda telah terdaftar sebagai %s di %s. Silakan login sesuai dengan informasi Akun yang tertera dibawah.'), $titleUser, Configure::read('__Site.site_name'));
		echo "\n\n";

		printf(__('Email: %s'), $email);
		echo "\n";
		printf(__('Password: %s'), $password);
		echo "\n\n";

		echo __('Team Support Kami akan segera menghubungi Anda atau silakan menghubungi Kami di:');
		echo "\n";
		printf('%s | %s', $site_name, $site_email);
		echo "\n";
		printf(__('WhatsApp: %s'), $site_wa);
		echo "\n\n";

		echo __('Anda dapat mengunduh Buku Panduan penggunaan website Kami disini:');
		echo "\n";
		echo $urlUserManual;
?>
