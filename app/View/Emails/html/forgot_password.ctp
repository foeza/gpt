<?php 
		$group = $this->Rumahku->filterEmptyField($params, 'PasswordReset', 'group', 'admin');
		$reset_code = $this->Rumahku->filterEmptyField($params, 'PasswordReset', 'reset_code');
		
		$url = $this->Html->url(array(
			'controller' => 'users',
			'action' => 'password_reset',
			$reset_code,
			$group => true,
		), true);
		
		echo $this->Html->tag('p', __('Anda telah melakukan permintaan untuk mereset password Anda. Untuk melanjutkan proses reset, silahkan kunjungi link di bawah ini.'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 5px; line-height: 20px;'
		));
		echo $this->Html->tag('p', $this->Html->link(__('Klik disini untuk reset password'), $url, array(
			'style'=>'color: #00af00; text-decoration: none; font-size: 14px;', 
			'target'=> '_blank'
		)), array(
			'style' => 'color: #303030; font-size: 14px; margin: 0 0 20px; line-height: 20px;'
		));
		echo $this->Html->tag('p', __('Jika Anda tidak dapat mengklik link diatas, Anda juga dapat mereset password Anda dengan mengunjungi URL di bawah ini :'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 10px; line-height: 20px;'
		));
		echo $this->Html->tag('p', $url, array(
			'style' => 'color: #00af00; text-decoration: none; font-size: 14px; margin: 0 0 20px; padding: 0;'
		));
		echo $this->Html->tag('p', __('Kode reset hanya berlaku selama dua hari. Setelah itu, Anda harus mengulang proses reset password.'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 10px; line-height: 20px;'
		));
		echo $this->Html->tag('p', __('Jika Anda tidak merasa melakukan permintaan reset password, mohon periksa kembali akun Anda, dan mengganti password Anda apabila dirasa perlu, untuk keamanan akun Anda dan mencegah hal-hal yang tidak diinginkan.'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 10px; line-height: 20px;'
		));
?>