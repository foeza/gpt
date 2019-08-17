<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'KprBank', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'KprBank', 'code');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');


		printf(__('Jadwal Akad Kredit KPR - %s'), $code);
		echo "\n\n";

		echo __('Kami informasikan bahwa Agen dibawah ini :');
		echo "\n";

		echo $this->element('emails/text/kpr/agent');

		if(!empty($mls_id)){
			echo __('Informasi properti sebagai berikut :');
			echo "\n";
			echo $this->element('emails/text/properties/info');
			echo "\n";
		}

		echo __('Telah menentukan jadwal proses akad kredit untuk bertemu dengan klien :');
		echo "\n";
		echo $this->element('emails/text/kpr/akad_kredit');
		echo "\n";
		echo __('Untuk melihat detil Aplikasi KPR Anda, klik di sini :');
		echo "\n";

		$link = $domain.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));

		echo $this->Html->link(__('Lihat Detil'), $url, array(
			'target' => '_blank',
		));
?>