<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'code');
		$commission = $this->Rumahku->filterEmptyField($params, 'KprCommissionPaymentConfirm', 'commission');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$commission = $this->Rumahku->getCurrencyPrice($commission);
		
		$link = $domain.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));

		printf(__('Pembayaran Provisi KPR %s'), $code);
		echo "\n\n";

		echo __('Dibayarkan oleh:');
		echo "\n";
		echo $this->element('emails/text/kpr/bank');

		printf(__('Provisi telah dibayarkan kepada Rumahku.com sebesar %s'), $commission);
?>