<?php
		$company = Configure::read('Config.Company.data');
		$company_name = $this->Rumahku->filterEmptyField($company, 'UserCompany', 'name');
		$principle_website = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain');

		echo $this->Html->tag('p', sprintf(__('Terhitung sejak kami mengirimkan email ini, Anda tidak lagi terdaftar sebagai bagian dari Group %s'), $this->Html->link($company_name, FULL_BASE_URL, array(
				'style' => 'color: #0462F0; font-size: 14px; margin: 0; padding: 0;'
			))), array(
			'style' => 'color: #303030; font-size: 14px; margin: 25px 0 20px; line-height: 20px;'
		));

		if( !empty($principle_website) ) {
			echo $this->Html->tag('p', sprintf(__('Untuk selanjut nya Anda tetap dapat beraktivitas sebagai Perusahaan Independent di %s'), $this->Html->link($principle_website, $principle_website, array(
					'style' => 'color: #0462F0; font-size: 14px; margin: 0; padding: 0;'
				))), array(
				'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
			));
		} else {
			echo $this->Html->tag('p', sprintf(__('Untuk selanjut nya Anda tetap dapat beraktivitas di %s'), $this->Html->link(Configure::read('__Site.main_website'), Configure::read('__Site.main_website'), array(
					'style' => 'color: #0462F0; font-size: 14px; margin: 0; padding: 0;'
				))), array(
				'style' => 'color: #303030; font-size: 14px; margin: 5px 0 20px; line-height: 20px;'
			));
		}
?>