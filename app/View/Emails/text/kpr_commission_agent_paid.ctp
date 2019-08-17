<?php 
		$id = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'id');
		$code = $this->Rumahku->filterEmptyField($params, 'KprApplication', 'code');
		$domain = $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', FULL_BASE_URL);
		$commission = $this->Rumahku->filterEmptyField($params, 'KprCommissionPaymentConfirm', 'commission');
		$link = $domain.$this->Html->url(array(
			'controller' => 'kpr', 
			'action' => 'application_detail',
			$id,
			'admin' => true,
		));
		$commission = $this->Rumahku->getCurrencyPrice($commission);

		printf(__('Pembayaran Provisi KPR %s'), $code);
		echo "\n";
		printf(__('Sebesar %s'), $code, $commission);
		echo "\n\n";

		echo __('Dibayarkan oleh:');
		echo "\n";
		echo $this->element('emails/text/kpr/bank');

		echo __('Provisi dibayarkan kepada:');
		echo "\n";
		echo $this->element('emails/text/kpr/agent_commission');

		echo __('Lihat Detil:');
		echo "\n";
		echo $link;
?>