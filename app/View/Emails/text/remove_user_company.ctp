<?php
		echo __('User Anda telah di Non-Aktifkan');
		echo "\n\n";

		printf(__('Terhitung sejak kami mengirimkan email ini, Anda tidak akan terdaftar lagi sebagai Agen/Admin dari %s'), FULL_BASE_URL);
		echo "\n";
		printf(__('Untuk selanjut nya Anda tetap dapat beraktivitas sebagai User terdaftar di %s'), Configure::read('__Site.site_default'));
?>