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
		$site_phone = Configure::read('__Site.site_phone');

		$even = 'padding: 5px 8px;line-height: 20px;vertical-align: top;text-align: left;background-color: #f4f4f4;';
		$odd = $even.'background-color: transparent;';
		
		echo $this->Html->tag('p', sprintf(__('Anda telah terdaftar sebagai %s di %s. Silakan login sesuai dengan informasi Akun yang tertera dibawah.'), $titleUser, Configure::read('__Site.site_name')), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;',
		));
?>
<table style="border: 1px solid #dddddd;border-collapse: separate;width: 100%;margin-bottom: 20px;max-width: 100%;background-color: transparent;border-spacing: 0;font-family: Helvetica Neue, Helvetica, rial, sans-serif;font-size: 14px;line-height: 20px;color: #333333;">
	<tbody>
		<?php 
				$contentTr = $this->Html->tag('th', __('Email'), array(
					'style' => 'font-weight: bold;color:#303030;'.$even,
				));
				$contentTr .= $this->Html->tag('td', $email, array(
					'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
				));

				echo $this->Html->tag('tr', $contentTr);

				$contentTr = $this->Html->tag('th', __('Password'), array(
					'style' => 'font-weight: bold;color:#303030;'.$even,
				));
				$contentTr .= $this->Html->tag('td', $password, array(
					'style' => 'width: 70%;border-left: 1px solid #dddddd;'.$odd,
				));

				echo $this->Html->tag('tr', $contentTr);
		?>
	</tbody>
</table>
<?php
		echo $this->Html->tag('p', __('Team Support Kami akan segera menghubungi Anda atau silakan menghubungi Kami di:'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;',
		));
		echo $this->Html->tag('p', sprintf('%s | %s', $site_name, $site_email), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;font-weight: bold;',
		));
		// echo $this->Html->tag('p', sprintf(__('Sales and Support: %s'), $site_phone), array(
		// 	'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;font-weight: bold;',
		// ));
		echo $this->Html->tag('p', sprintf(__('WhatsApp: %s'), $site_wa), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;font-weight: bold;',
		));
		
		echo $this->Html->tag('p', __('Anda dapat mengunduh Buku Panduan penggunaan website Kami disini:'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 25px 0 0; line-height: 20px;',
		));
		echo $this->Html->tag('p', $this->Html->link($urlUserManual, $urlUserManual, array(
				'style'=> 'color: #06c; text-decoration: none; font-size: 14px;', 
				'target'=> '_blank'
			)), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;',
		));
?>
