<?php

	$postData = !empty($postData) ? $postData : NULL;

	if($postData){
		$paymentChannel	= $this->Rumahku->filterEmptyField($postData, 'PAYMENTCHANNEL');
		$content		= '';

		if($paymentChannel == '03' && empty($xmlResult) == FALSE && $xmlResult != 'STOP'){
		//	BCA PAYMENT METHOD
			$xmlResult		= simplexml_load_string($xmlResult);
			$dokuPaymentURL	= empty($xmlResult->REDIRECTURL) ? NULL : $xmlResult->REDIRECTURL;
			$redirectParams	= empty($xmlResult->REDIRECTPARAMETER) ? NULL : explode(';;', $xmlResult->REDIRECTPARAMETER);
			$formData		= array_map('urldecode', $redirectParams);

			if($formData){
				foreach($formData as $key => $inputValue){
					$data	= explode('||', $inputValue);
					$name	= isset($data[0]) ? $data[0] : NULL;
					$value	= isset($data[1]) ? $data[1] : NULL;

					$content.= $this->Form->input($name, array('type' => 'hidden', 'name' => $name, 'value' => $value));
				}
			}
		}
		else if($paymentChannel != '03'){
		//	OTHER PAYMENT METHODS
			$dokuPaymentURL = Configure::read('__Site.doku_payment_url');

			foreach($postData as $name => $value){
				$content.= $this->Form->input($name, array('type' => 'hidden', 'name' => $name, 'value' => $value));
			}
		}

		if($content){
			echo($this->Form->create('UserIntegratedOrderAddon', array('id' => 'doku-payment-form', 'url' => $dokuPaymentURL)));
			echo($content);
			echo($this->Form->end());
			echo($this->Html->tag('div', __('Sedang melakukan proses pembayaran...'), array('class' => 'margin-vert-30 wrapper-border')));
		}
		else{
			$backButton = $this->Html->link(__('Kembali'), array('action' => 'index', 'admin' => TRUE), array('class' => 'btn default floright'));
			echo($this->Html->tag('div', __('Ups, terjadi kesalahan para proses pembayaran, silakan coba kembali. ').$backButton, array('class' => 'wrapper-border')));
		}
	}

?>