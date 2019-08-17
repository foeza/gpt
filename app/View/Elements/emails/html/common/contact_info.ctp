<?php
		$site_name = Configure::read('__Site.site_name');
		$site_email = Configure::read('__Site.send_email_from');
		$site_wa = Configure::read('__Site.site_wa');
		$site_phone = Configure::read('__Site.site_phone');

		echo $this->Html->tag('p', __('Apabila Anda tidak merasa melakukan perubahan tersebut. Mohon hubungi kami di:'), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;'
		));
		echo $this->Html->tag('p', sprintf('%s | %s', $site_name, $this->Html->link($site_email, 'mailto:'.$site_email, array(
			'style' => 'color: #303030;',
		))), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;font-weight: bold;',
		));
		// echo $this->Html->tag('p', sprintf(__('Sales and Support: %s'), $this->Html->link($site_phone, 'tel:'.$site_phone, array(
		// 	'style' => 'color: #303030;',
		// ))), array(
		// 	'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;font-weight: bold;',
		// ));
		echo $this->Html->tag('p', sprintf(__('WhatsApp: %s'), $this->Html->link($site_wa, 'tel:'.$site_wa, array(
			'style' => 'color: #303030;',
		))), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 0; line-height: 20px;font-weight: bold;',
		));
?>