<?php
		echo $this->Html->tag('h2', __('User Anda telah di Non-Aktifkan'), array(
			'style' => 'color: #303030; font-size: 16px; margin: 20px 0; line-height: 20px;'
		));
		echo $this->Html->tag('p', sprintf(__('Terhitung sejak kami mengirimkan email ini, Anda tidak akan terdaftar lagi sebagai Agen/Admin dari %s'), $this->Html->link(FULL_BASE_URL, FULL_BASE_URL, array(
				'style' => 'color: #0462F0; font-size: 14px; margin: 0; padding: 0;'
			))), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));
		echo $this->Html->tag('p', sprintf(__('Untuk selanjut nya Anda tetap dapat beraktivitas sebagai User terdaftar di %s'), $this->Html->link(Configure::read('__Site.site_default'), Configure::read('__Site.site_default'), array(
				'style' => 'color: #0462F0; font-size: 14px; margin: 0; padding: 0;'
			))), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));
?>