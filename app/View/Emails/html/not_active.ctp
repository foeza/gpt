<?php
		$assign_name = Common::hashEmptyField($params, 'Assign.User.full_name');
		$group_id = Common::hashEmptyField($params, 'Assign.User.group_id');
		$reason = Common::hashEmptyField($params, 'UserActivedAgent.reason');
		$company_name = Common::hashEmptyField($params, 'Decline.UserCompany.name'); 
		$groupName = Common::hashEmptyField($params, 'Decline.Group.name'); 
		$groupName = strtolower($groupName);

		echo $this->Html->tag('h2', __('Akun anda telah di non-aktifkan oleh admin untuk sementara waktu dikarenakan %s.', $reason), array(
			'style' => 'color: #303030; font-size: 16px; margin: 20px 0; line-height: 20px;'
		));

		echo $this->Html->tag('p', sprintf(__('Terhitung sejak kami mengirimkan email ini, Anda sudah tidak terdaftar sebagai %s dari %s'), $groupName, $company_name), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));
		echo $this->Html->tag('p', sprintf(__('Untuk selanjutnya anda dapat beraktifitas sebagai user di %s'), $this->Html->link(Configure::read('__Site.site_default'), Configure::read('__Site.site_default'), array(
				'style' => 'color: #0462F0; font-size: 14px; margin: 0; padding: 0;'
			))), array(
			'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
		));

		if($group_id == 2){
			echo $this->Html->tag('p', __('Seluruh data properti dan klien anda sudah dialihkan kepada agen %s.', $this->Html->tag('strong', $assign_name)), array(
				'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
			));
		}
?>