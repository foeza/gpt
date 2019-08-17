<?php
		$_site_name = !empty($_site_name)?$_site_name:false;
		$email = !empty($params['email'])?urlencode($params['email']):false;
		$activation_code = !empty($params['activation_code'])?$params['activation_code']:false;
		$url = $this->Html->url(array(
			'controller' => 'users',
			'action' => 'verify',
			$email,
			$activation_code,
			'admin' => false
		), true);

		printf(__('Terima kasih atas kepercayaan Anda dan selamat bergabung di %s.'), $_site_name);
		echo "\n\n";

		echo __('Anda mengambil keputusan tepat dengan mendaftarkan diri bersama kami.');
		echo "\n\n";

		printf(__('%s merupakan portal properti terbesar di Indonesia. Kami selalu berusaha memberikan yang terbaik, sehingga Anda dapat dengan mudah menjual, membeli, atau mencari informasi mengenai properti dan real-estate.'), $_site_name);
		echo "\n\n";

		printf(__('Untuk mendapatkan akses ke seluruh fitur di website %s, Anda harus mengaktifkan akun Anda. Untuk mengaktifkan akun Anda, silahkan mengunjungi website dibawah ini.'), $_site_name);
		echo "\n\n";

		echo $url;
		echo "\n\n";

		printf(__('Seluruh kru dan staff %s mengucapkan selamat datang, dan selamat bergabung bersama kami.'), $_site_name);
?>
