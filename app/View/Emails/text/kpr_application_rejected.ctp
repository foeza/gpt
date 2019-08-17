<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'Kpr', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'Kpr', 'code');
		$status_KPR = $this->Rumahku->filterEmptyField($params, 'status_KPR');
		$kprBankDate = $this->Rumahku->filterEmptyField($params, $status_KPR);
		$action_date = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'action_date');
		$note = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'note');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$bank = $this->Rumahku->filterEmptyField($params, 'Bank', 'name');

		$link = $domain.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));

		printf(__('Penolakan KPR %s oleh %s'), $code, $bank);
		echo "\n\n";

		echo __('Kami informasikan bahwa bank dibawah ini:');
		echo "\n";
		echo $this->element('emails/text/kpr/bank');

		echo __('Menolak Aplikasi KPR yang Anda ajukan untuk:');
		echo "\n";
		echo $this->element('emails/text/properties/info');
		echo $this->element('emails/text/kpr/client');

		echo __('Alasan Penolakan :');
		echo "\n";
		echo $note;
		echo "\n\n";

		echo __('Lihat Detil:');
		echo $link;
?>