<?php

	$siteName			= Configure::read('__Site.site_name');
	$currency			= Configure::read('__Site.config_currency_code');
	$paymentChannels	= Configure::read('__Site.payment_channels');
	$orderItemTypes		= Configure::read('Global.Data.order_item_types');

	$orderID		= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'id');
	$packageID		= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'id');
	$packageSlug	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'slug');
	$packageName	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'name');
	$monthDuration	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'month_duration', 0);

	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');

	$NL = "\n";
//	$NL = $this->Html->tag('br');

	$detailURL = $this->Html->url(array(
		'controller'	=> 'membership_orders', 
		'action'		=> 'view', 
		'admin'			=> true, 
		$orderID, 
		$packageSlug, 
	), true);

	echo($NL.$NL);
	echo(__($subject).$NL.$NL);

	echo($this->element('emails/text/memberships/order_detail', array(
		'params'		=> $params, 
		'showMessage'	=> true, 
		'showRemark'	=> true, 
		'showInvoice'	=> false, 
	)));

	echo(__('Untuk memproses pengajuan, silakan kunjungi url di bawah ini : ').$NL);
	echo($detailURL.$NL.$NL);

?>