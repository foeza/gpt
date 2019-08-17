<?php

		$text = Common::hashEmptyField($params, 'text');
		$is_rollback = Common::hashEmptyField($params, 'is_rollback');
		$rollback_reason = Common::hashEmptyField($params, 'rollback_reason');
		$is_property = Common::hashEmptyField($params, 'UserActivedAgent.is_property');
		$userAgentDetails = Common::hashEmptyField($params, 'UserActivedAgentDetail');
		$company_name = Common::hashEmptyField($params, 'Decline.UserCompany.name');
		$assign_name = Common::hashEmptyField($params, 'Assign.full_name');

		echo __('Selamat bergabung kembali.');
		echo "\n\n";

		// if($rollback_reason){
		// 	echo __('Keterangan: %s', $rollback_reason);
		// }

		echo "\n\n";
		echo __('Terhitung sejak kami mengirimkan email ini, Anda dapat kembali beraktifitas sebagai agen dari %s', $company_name);
		echo "\n";

		echo sprintf(__('Untuk selanjutnya anda dapat beraktifitas sebagai agen/marketing di %s & %s'), Configure::read('__Site.site_default'), FULL_BASE_URL);
		echo "\n";

		if(!empty($is_rollback)){
			echo __('Seluruh data properti dan klien yang sebelumnya dialihkan kepada %s sudah kami kembalikan ke akun Anda.', $assign_name);
			echo "\n";
		}
?>