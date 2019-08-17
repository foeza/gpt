<?php 
		$_config = !empty($_config)?$_config:false;
		$params = !empty($params)?$params:false;

		$login_primesystem = __('http://www.primesystem.id/admin/users/login');
		$company_name = $this->Rumahku->filterEmptyField($_config, 'UserCompany', 'name');

		$email = $this->Rumahku->filterEmptyField($params, 'email');
		$password = $this->Rumahku->filterEmptyField($params, 'password');
		$group_id = $this->Rumahku->filterEmptyField($params, 'group_id');
		$titleUser = $this->Rumahku->_callTitleUser($group_id);
		$slugUser = $this->Rumahku->_callTitleUser($group_id, true);
		$urlUserManual = sprintf('%s/files/user_manual/prime/%s.doc', FULL_BASE_URL, $slugUser);

		$even = 'padding: 5px 8px;line-height: 20px;vertical-align: top;text-align: left;background-color: #f4f4f4;';
		$odd = $even.'background-color: transparent;';

		echo $this->Html->tag('p', sprintf(__('Anda telah terdaftar sebagai %s. Silakan login sesuai dengan informasi Akun yang tertera dibawah.'), $titleUser), array(
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
		echo $this->Html->tag('p', __('Anda dapat mengunjungi link berikut untuk melakukan Login:'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;',
		));
		echo $this->Html->tag('p', $this->Html->link($login_primesystem, $login_primesystem, array(
			'style' => 'color: #06c; font-size: 14px; margin: 5px 0 0; line-height: 20px;',
		)), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;',
		));
?>
