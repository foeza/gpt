<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'code');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain');
		$mls_id = $this->Rumahku->filterEmptyField($params, 'Property', 'mls_id');
		$note = $this->Rumahku->filterEmptyField($params, 'KprApplicationRequest', 'note');

		printf(__('Referral KPR %s telah disetujui'), $code);
		echo "\n\n";
		echo __('Referral Anda telah disetujui oleh:');
		echo "\n";
		echo $this->element('emails/text/kpr/bank');

		echo __('Informasi Pengajuan sebagai berikut:');
		echo "\n";
		echo $this->element('emails/text/properties/info');
		echo $this->element('emails/text/kpr/client');

		echo __('Mohon maaf, Anda tidak mendapatkan provisi untuk Pengajuan KPR ini.');
		echo "\n\n";

	    if( !empty($note) ) {
			echo __('Keterangan dari Bank :');
			echo "\n";
			echo $note;
			echo "\n\n";
		}

		echo __('Lihat Detil:');

		echo $domain.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));
?>