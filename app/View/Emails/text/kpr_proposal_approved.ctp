<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'KprBank', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'KprBank', 'code');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');
		$status_KPR = $this->Rumahku->filterEmptyField($params, 'status_KPR');
		$kprBankDate = $this->Rumahku->filterEmptyField($params, $status_KPR);
		$action_date = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'action_date');
		$note = $this->Rumahku->filterEmptyField($kprBankDate, 'KprBankDate', 'note');

		$subject = Common::hashEmptyField($params, 'subject');

	    printf($subject);
	    echo "\n\n";

		echo $this->element('emails/text/kpr/bank');

		echo $this->element('emails/text/kpr/body_email_KPR', array(
			'status_KPR' => $status_KPR,
		)); 

	    if( !empty($note) ) {
			echo __('Keterangan dari Bank :');
			echo "\n";
			echo $note;
			echo "\n\n";
		}

		if( !empty($mls_id) ) {
  			echo __('Informasi Pengajuan sebagai berikut:');
			echo "\n";
			echo $this->element('emails/text/properties/info');
		}

		echo $this->element('emails/text/kpr/client');
		echo $this->element('emails/text/kpr/info');

		echo __('Untuk melihat detil permohonan KPR Anda:');
    	echo "\n";

		$link = $domain.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));
    	echo $link;
?>