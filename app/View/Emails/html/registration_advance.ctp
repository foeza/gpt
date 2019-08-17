<?php 
		$even = 'padding: 5px 8px;line-height: 20px;text-align: left;vertical-align: top;border-top: 1px solid #dddddd;';
		$odd = $even.'background-color: #f5eee6;';

		if( !empty($params['activation_code']) ) {
			$activation_link = FULL_BASE_URL.$this->Html->url(array(
				'controller' => 'users',
				'action' => 'verify',
				urlencode($params['email']),
				$params['activation_code'],
				'admin' => false
			));
		}
?>
<p style="color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;"><?php printf(__('Terima kasih atas kepercayaan Anda dan selamat bergabung di %s.'), Configure::read('__Site.site_name')); ?></p>
<p style="color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;"><?php printf(__('Anda mengambil keputusan tepat dengan mendaftarkan diri bersama kami')); ?>.</p>
<p style="color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;"><?php printf(__('%s merupakan portal properti terbesar di Indonesia. Kami selalu berusaha memberikan yang terbaik, sehingga Anda dapat dengan mudah menjual, membeli, atau mencari informasi mengenai properti dan real-estate.'), Configure::read('__Site.site_name')); ?></p>
<table style="border: 1px solid #dddddd;border-collapse: separate;width: 100%;margin-bottom: 20px;max-width: 100%;background-color: transparent;border-spacing: 0;font-family: Helvetica Neue, Helvetica, rial, sans-serif;font-size: 14px;line-height: 20px;color: #333333;">
	<tbody>
		<?php 
				if( !empty($params['username']) ) {
		?>
		<tr>
			<th style="<?php echo $even; ?>border-top:none;background-color: #5eab1f;color:#ffffff;"><strong><?php echo __('Username'); ?></strong></th>
			<td style="<?php echo $even; ?>%sborder-top:none;"><?php echo $params['username']; ?></td>
		</tr>
		<?php 
				}
		?>
		<tr>
			<th style="<?php echo $even; ?>border-top:none;background-color: #5eab1f;color:#ffffff;"><strong><?php echo __('Email'); ?></strong></th>
			<td style="<?php echo $odd; ?>%sborder-top:none;"><?php echo $params['email']; ?></td>
		</tr>
		<tr>
			<th style="<?php echo $even; ?>border-top:none;background-color: #5eab1f;color:#ffffff;"><strong><?php echo __('Password'); ?></strong></th>
			<td style="<?php echo $even; ?>%sborder-top:none;"><?php echo $params['password']; ?></td>
		</tr>
	</tbody>
</table>
<?php 
		if( !empty($params['activation_code']) ) {
			echo $this->Html->tag('p', sprintf(__('Untuk mendapatkan akses ke seluruh fitur di website %s, Anda harus mengaktifkan akun Anda. Untuk mengaktifkan akun Anda, silahkan klik link dibawah ini.'), Configure::read('__Site.site_name')), array(
				'style' => 'color: #303030; font-size: 14px; margin: 20px 0; line-height: 20px;',
			));

			echo $this->Html->tag('p', $this->Html->link(__('Aktifkan akun'), $activation_link, array(
				'style'=>'color: #00af00; text-decoration: none; font-size: 14px;', 
				'target'=> '_blank'
			)), array(
				'style' => 'border: 1px solid #ccc; padding: 10px; text-align: center; background: #ffffd8; margin: 10px 0;',
			));

			echo $this->Html->tag('p', __('Apabila Anda tidak dapat mengklik link diatas, Anda juga dapat mengaktifkan akun Anda dengan mengunjungi website dibawah ini'), array(
				'style' => 'color: #303030; font-size: 14px; margin: 20px 0 0; line-height: 20px;',
			));
			
			echo $this->Html->tag('p', $activation_link, array(
				'style' => 'color: #00af00; text-decoration: none; font-size: 14px; margin: 5px 0; word-wrap: break-word;',
			));
		}

		echo $this->Html->tag('p', sprintf(__('Seluruh kru dan staff %s mengucapkan selamat datang, dan selamat bergabung bersama kami.'), Configure::read('__Site.site_name')), array(
			'style' => 'color: #303030; font-size: 14px; margin: 20px 0; line-height: 20px;',
		));
?>