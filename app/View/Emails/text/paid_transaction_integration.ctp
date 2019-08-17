<?php

	$siteName			= Configure::read('__Site.site_name');
	$currency			= Configure::read('__Site.config_currency_code');
	$paymentChannels	= Configure::read('__Site.payment_channels');
	$orderItemTypes		= Configure::read('Global.Data.order_item_types');

	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');

//	invoice detail
	$invoiceID		= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'id');
	$userID			= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'user_id');
	$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'invoice_number');

	$NL = "\n";
//	$NL = $this->Html->tag('br');

	$content = 'Terima kasih telah melakukan pembayaran untuk transaksi dengan nomor invoice %s. ';
	$content.= 'Selanjutnya, Customer Support kami akan menghubungi Anda, maksimal 2 x 24 jam setelah akun Anda siap digunakan.';

	echo($NL.$NL);
	echo(__($content, $invoiceNumber).$NL.$NL);

	echo($this->element('emails/text/user_integrated/order_detail', array(
		'params' => $params, 
	)));

	echo($NL.$NL);
	echo(__('Terima kasih telah menggunakan %s sebagai media pemasaran properti Anda.', $siteName).$NL.$NL);

?>