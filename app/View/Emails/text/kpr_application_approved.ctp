<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'Kpr', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'Kpr', 'code');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');

	    printf(__('Aplikasi KPR %s telah disetujui'), $code);
	    echo "\n\n";

	    echo __('Aplikasi Anda telah disetujui oleh:');
	    echo "\n";

		echo $this->element('emails/text/kpr/bank');

		if( !empty($mls_id) ) {
			echo __('Informasi Pengajuan sebagai berikut:');
	    	echo "\n";

			echo $this->element('emails/text/properties/info');
	    }

		echo $this->element('emails/text/kpr/info');

		echo __('Untuk melihat detil Aplikasi KPR Anda:');
    	echo "\n";

		$link = $domain.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));
    	echo $link;
?>