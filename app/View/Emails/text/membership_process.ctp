<?php

	$siteName			= Configure::read('__Site.site_name');
	$currency			= Configure::read('__Site.config_currency_code');
	$paymentChannels	= Configure::read('__Site.payment_channels');
	$orderItemTypes		= Configure::read('Global.Data.order_item_types');

	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');

	$invoiceID		= $this->Rumahku->filterEmptyField($params, 'Payment', 'id');
	$userID			= $this->Rumahku->filterEmptyField($params, 'Payment', 'user_id');
	$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'Payment', 'invoice_number');
	$orderNumber	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'order_number');
	$status			= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'status');
	$statusMessage	= 'Pesanan Paket Membership Profesional Anda telah';

//	untuk pembatalan invoice numpang sini, karna layoutnya sama
	$invoiceStatus = $this->Rumahku->filterEmptyField($params, 'Payment', 'payment_status');
	if($invoiceStatus == 'cancelled'){
		$status = $invoiceStatus;
	}

	$byText = !empty($is_admin) ? sprintf('dengan nomor order %s', $orderNumber) : 'Anda';

//	cancel itu request by user, sisanya eksekusi oleh admin. makanya ga pake "maaf"
	switch($status){
		case 'cancelled' :
			$showResponse	= false;
			$content		= sprintf('Pesanan Paket Membership Profesional %s telah dibatalkan', $byText);
		break;
		case 'approved' : 
			$showResponse	= true;
			$content		= sprintf('Selamat, pemesanan Paket Membership Profesional %s telah disetujui', $byText);
		break;
		case 'renewal' : 
			$showResponse	= true;
			$content		= sprintf('Selamat, pemesanan Paket Membership Profesional %s telah disetujui', $byText);
		break;
		case 'rejected' : 
			$showResponse	= false;
			$content		= sprintf('Mohon maaf, pemesanan Paket Membership Profesional %s telah kami tolak', $byText);
		break;
		default : 
			$showResponse	= false;
			$content		= null; 
		break;
	}

	$NL = "\n";
//	$NL = $this->Html->tag('br');
	echo($NL.$NL);
	echo(__($content).$NL);

	$showRemark		= in_array($status, array('approved', 'renewal')) === false;
	$showInvoice	= in_array($status, array('approved', 'renewal'));

	echo($this->element('emails/text/memberships/order_detail', array(
		'params'		=> $params, 
		'showMessage'	=> true, 
		'showRemark'	=> $showRemark, 
		'showInvoice'	=> $showInvoice, 
	)));

	$refererURL = $this->Rumahku->filterEmptyField($params, 'Payment', 'referer_url', FULL_BASE_URL);
	if($showResponse && $invoiceID){
		if(strpos($refererURL, '/admin') !== false){
			$detailURL = array(
				'controller'	=> 'payments', 
				'action'		=> 'view', 
				'admin'			=> false, 
				$invoiceID, 
				$invoiceNumber, 
			);
		}
		else{
			$invoiceToken	= md5($invoiceNumber . $invoiceID . $userID);
			$detailURL		= array(
				'controller'	=> 'payments', 
				'action'		=> 'checkout', 
				'admin'			=> false, 
				$invoiceID, 
				$invoiceNumber, 
				$invoiceToken, 
			);
		}

	//	remove extra slash
		$detailURL	= $this->Html->url($detailURL);
		$detailURL	= preg_replace('/([^:])(\/{2,})/', '$1/', $refererURL.$detailURL);
		$detailURL	= str_replace('/admin/admin', '/admin', $detailURL);
		$message	= 'Anda dapat melakukan pembayaran dengan mengunjungi url di bawah ini :';
	}
	else{
		$detailURL	= Configure::read('__Site.membership_request_url');
		$message	= 'Anda dapat mengajukan paket membership kembali dengan mengunjungi url di bawah ini :';
	}

	echo($NL.$NL);
	echo(__($message).$NL);
	echo($detailURL.$NL.$NL);

?>