<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'code');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$link = $domain.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));

		printf(__('Jadwal Akad Kredit KPR %s'), $code);
		echo "\n\n";

		echo __('Kami informasikan bahwa bank dibawah ini:');
		echo "\n";
		echo $this->element('emails/text/kpr/bank');

		echo __('Telah menetapkan jadwal akad kredit sebagai berikut:');
		echo "\n";
		echo $this->element('emails/text/kpr/akad_kredit');

		echo __('Lihat Detil:');
		echo "\n";
		echo $link;
?>