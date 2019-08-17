<?php
	$activation_link = FULL_BASE_URL.$this->Html->url(array(
		'controller' => 'users',
		'action' => 'verify',
		urlencode($params['email']),
		$params['activation_code'],
		'admin' => false
	));
 	printf(__('Selamat bergabung di %s. Anda mengambil keputusan tepat dengan bergabung bersama kami.'), Configure::read('__Site.site_name')); echo "\n\n"; 
 	printf(__('%s  Anda mengambil keputusan tepat dengan bergabung bersama kami.'), Configure::read('__Site.site_name')); echo "\n\n"; 
	printf(__('%s merupakan portal properti terbesar di Indonesia. Kami selalu berusaha memberikan yang terbaik, sehingga Anda dapat dengan mudah menjual, membeli, atau mencari informasi mengenai properti dan real-estate.'), Configure::read('__Site.site_name')); echo "\n\n"; 
	printf(__('Username : %s'), $params['username']); echo "\n"; 
	printf(__('Email : %s'), $params['email']); echo "\n"; 
	printf(__('Password : %s'), $params['password']); echo "\n"; 
	printf(__('Untuk mendapatkan akses ke seluruh fitur di website %s, Anda harus mengaktifkan akun Anda. Untuk mengaktifkan akun Anda, silahkan klik link dibawah ini.'), Configure::read('__Site.site_name')); echo "\n\n"; 
	echo $activation_link; echo "\n\n"; 
	printf(__('Seluruh kru dan staff %s mengucapkan selamat datang, dan selamat bergabung bersama kami.'), Configure::read('__Site.site_name')); echo "\n\n"; 
?>
