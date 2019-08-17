<?php
	$dataCompany = Configure::read('Config.Company.data');

	$invoiceID		= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'id');
	$userID			= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'user_id');
	$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'invoice_number');
	$status			= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'status');

	$invoiceStatus = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'payment_status');
	if($invoiceStatus == 'cancelled'){
		$status = $invoiceStatus;
	}

	switch($status){
		case 'cancelled' :
			$showResponse = false;
			$content = 'Data form integrasi Anda telah dibatalkan';
		break;
		case 'request' : 
			$showResponse = true;
			$content = 'Selamat, data form integrasi Anda telah berhasil disimpan, berikut adalah rincian form integrasi :';
		break;
		case 'renewal' : 
			$showResponse = true;
			$content = 'Selamat, data form integrasi Anda telah berhasil disimpan, berikut adalah rincian form integrasi :';
		break;
		default : 
			$showResponse = false;
			$content = null; 
		break;
	}

	$NL = "\n";
//	$NL = $this->Html->tag('br');
	echo($NL.$NL);
	echo(__($content).$NL);

	echo $this->element('emails/text/user_integrated/order_detail', array(
		'params' => $params,
	));

	$refererURL = $this->Rumahku->filterEmptyField($params, 'Payment', 'referer_url', FULL_BASE_URL);
	if($showResponse && $invoiceID){
		$refererURL	= $this->Rumahku->filterEmptyField($dataCompany, 'UserCompanyConfig', 'domain', FULL_BASE_URL);

		$invoiceToken	= md5($invoiceNumber . $invoiceID . $userID);
		$detailURL		= array(
			'controller'	=> 'users', 
			'action'		=> 'checkout_addon', 
			'admin'			=> false, 
			$invoiceID, 
			$invoiceNumber, 
			$invoiceToken, 
		);

	//	remove extra slash
		$detailURL	= $this->Html->url($detailURL);
		$detailURL	= preg_replace('/([^:])(\/{2,})/', '$1/', $refererURL.$detailURL);
		$detailURL	= str_replace('/admin/admin', '/admin', $detailURL);
		$message	= 'Anda dapat melakukan pembayaran dengan menekan tombol di bawah ini :';

		echo($NL.$NL);
		echo(__($message).$NL);
		echo($detailURL.$NL.$NL);
	}
?>