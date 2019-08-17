<?php
		$group = $this->Rumahku->filterEmptyField($params, 'PasswordReset', 'group', 'admin');
		$reset_code = $this->Rumahku->filterEmptyField($params, 'PasswordReset', 'reset_code');
		
		$url = $this->Html->url(array(
			'controller' => 'users',
			'action' => 'password_reset',
			$reset_code,
			$group => true,
		), true);

		echo __('Anda telah melakukan permintaan untuk mereset password Anda. Untuk melanjutkan proses reset, silahkan kunjungi link di bawah ini.');
		echo "\n\n";

		echo $url;
		echo "\n\n";

		echo __('Kode reset hanya berlaku selama dua hari. Setelah itu, Anda harus mengulang proses reset password.');
		echo "\n\n";

		echo __('Jika Anda tidak merasa melakukan permintaan reset password, mohon periksa kembali akun Anda, dan mengganti password Anda apabila dirasa perlu, untuk keamanan akun Anda dan mencegah hal-hal yang tidak diinginkan.');
?>
