<?php
		$assign_name = Common::hashEmptyField($params, 'Assign.User.full_name');
		$group_id = Common::hashEmptyField($params, 'Assign.User.group_id');
		$reason = Common::hashEmptyField($params, 'UserActivedAgent.reason');
		$company_name = Common::hashEmptyField($params, 'Decline.UserCompany.name'); 
		$groupName = Common::hashEmptyField($params, 'Decline.Group.name'); 
		$groupName = strtolower($groupName);
		
		echo __('Akun anda telah di non-aktifkan oleh admin untuk sementara waktu dikarenakan %s.', $reason);
		echo "\n\n";
		printf(__('Terhitung sejak kami mengirimkan email ini, Anda sudah tidak terdaftar sebagai %s dari %s'), $groupName, $company_name);
		echo "\n";
		printf(__('Untuk selanjut nya Anda tetap dapat beraktivitas sebagai User terdaftar di %s'), Configure::read('__Site.site_default'));
		echo "\n";

		if($group_id == 2){
			printf(__('Untuk properti dan klien anda, admin sudah mengalihkan ke agen %s, properti dan klien anda dapat kembali sewaktu anda akan diaktifkan oleh admin.', $this->Html->tag('strong', $assign_name)));		
		}

?>