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
?>
<p style="color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;">
	<?php
			printf(__('Terima kasih atas kepercayaan Anda dan selamat bergabung di %s.'), $_site_name);
	?>
</p>
<p style="color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;"><?php echo __('Anda mengambil keputusan tepat dengan mendaftarkan diri bersama kami.'); ?></p>
<p style="color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;">
	<?php
			printf(__('%s merupakan portal properti terbesar di Indonesia. Kami selalu berusaha memberikan yang terbaik, sehingga Anda dapat dengan mudah menjual, membeli, atau mencari informasi mengenai properti dan real-estate.'), $_site_name);
	?>
</p>
<p style="color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;">
	<?php
			printf(__('Untuk mendapatkan akses ke seluruh fitur di website %s, Anda harus mengaktifkan akun Anda. Untuk mengaktifkan akun Anda, silahkan klik link dibawah ini.'), $_site_name);
	?>
</p>
<p style="color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;">
	<?php
			echo $this->Html->link(__('Aktifkan akun'), $url, array(
				'style'=>'color: #00af00; text-decoration: none; font-size: 14px;', 
				'target'=> '_blank'
			));
	?>
</p>
<p style="color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;">
	<?php
			echo __('Apabila Anda tidak dapat mengklik link diatas, Anda juga dapat mengaktifkan akun Anda dengan mengunjungi website dibawah ini');
	?>:
</p>
<p style="color: #00af00; text-decoration: none; font-size: 14px; margin: 0; word-wrap: break-word;"><?php echo $url; ?></p>
<p style="color: #303030; font-size: 14px; margin: 20px 0 0; line-height: 20px;"><?php printf(__('Seluruh kru dan staff %s mengucapkan selamat datang, dan selamat bergabung bersama kami.'), $_site_name); ?></p>