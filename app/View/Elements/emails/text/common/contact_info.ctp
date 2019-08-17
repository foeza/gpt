<?php
		$site_name = Configure::read('__Site.site_name');
		$site_email = Configure::read('__Site.send_email_from');
		$site_wa = Configure::read('__Site.site_wa');
		$site_phone = Configure::read('__Site.site_phone');

		echo __('Apabila Anda tidak merasa melakukan perubahan tersebut. Mohon hubungi kami di:');
		echo "\n";

		printf('%s | %s', $site_name, $site_email);
		echo "\n";

		// printf(__('Sales and Support: %s'), $site_phone);
		// echo "\n";

		printf(__('WhatsApp: %s'), $site_wa);
?>