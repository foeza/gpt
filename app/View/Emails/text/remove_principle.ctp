<?php
		$company = Configure::read('Config.Company.data');
		$company_name = $this->Rumahku->filterEmptyField($company, 'UserCompany', 'name');
		$principle_website = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain');

		echo __('Terhitung sejak kami mengirimkan email ini, Anda tidak lagi terdaftar sebagai bagian dari Group %s', $company_name);
		echo "\n";

		if( !empty($principle_website) ) {
			echo __('Untuk selanjut nya Anda tetap dapat beraktivitas sebagai Perusahaan Independent di %s', $principle_website);
		} else {
			echo __('Untuk selanjut nya Anda tetap dapat beraktivitas di %s', Configure::read('__Site.main_website'));
		}
?>