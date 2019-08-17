<?php

	$siteName		= Configure::read('__Site.site_name');

	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');

	$userID			= $this->Rumahku->filterEmptyField($params, 'User', 'id');
	$fullName		= $this->Rumahku->filterEmptyField($params, 'User', 'full_name');
	$email			= $this->Rumahku->filterEmptyField($params, 'User', 'email');
	$phone			= $this->Rumahku->filterEmptyField($params, 'UserProfile', 'phone');
	$companyName	= $this->Rumahku->filterEmptyField($params, 'UserCompany', 'name');
	$logo			= $this->Rumahku->filterEmptyField($params, 'UserCompany', 'logo');
	$domain			= $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain');
	$liveDate		= $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'live_date');
	$endDate		= $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'end_date');

	$principleID	= $this->Rumahku->filterEmptyField($params, 'Payment', 'principle_id');

	$packageName	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'name');
	$monthDuration	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'month_duration', 0);
	
	$expMessage	= 'akan segera';
	if(strtotime(date('Y-m-d')) > strtotime($endDate)){
		$expMessage = 'telah';
	}

	$dateFormat	= 'd M Y';
	$liveDate	= $this->Rumahku->formatDate($liveDate, $dateFormat);
	$endDate	= $this->Rumahku->formatDate($endDate, $dateFormat);
	$liveDate	= date($dateFormat, strtotime($endDate . ' - ' . $monthDuration . ' month'));
	$content	= 'Masa tayang website Anda %s berakhir. Untuk tetap dapat menggunakan akses dan fitur %s, silakan perpanjang masa tayang website Anda.';

	$NL = "\n";
//	$NL = $this->Html->tag('br');

	echo($NL.$NL);
	echo(__($content, $expMessage, $siteName).$NL.$NL);
	echo(__('Rincian Paket Membership').$NL.$NL);

	if($subject){
		echo(__('Subyek : %s', $subject).$NL);
	}

	echo(__('Nama Principal : %s', $fullName).$NL);
	echo(__('Email Principal : %s', $email).$NL);
	echo(__('Nama Perusahaan : %s', $companyName).$NL);
	echo(__('Domain Website : %s', $domain).$NL);
	echo(__('Nama Paket : %s', $packageName).$NL);
	echo(__('Durasi : %s bulan', $monthDuration).$NL);
	echo(__('Periode Tayang : %s - %s', $liveDate, $endDate).$NL);
	
	$domain.= (substr($domain, -1) == '/') ? '' : '/';
	$membershipURL	= Configure::read('__Site.membership_request_url');
	$detailURL		= array(
		'controller'	=> 'membership_orders', 
		'action'		=> 'add', 
		'admin'			=> true, 
		$principleID,
	);

//	remove extra slash
	$detailURL = $this->Html->url($detailURL);
	$detailURL = preg_replace('/([^:])(\/{2,})/', '$1/', $domain.$detailURL);

	echo($NL.$NL);
	echo(__('Perpanjang masa tayang website Anda dengan mengunjungi url di bawah ini :').$NL);
	echo($detailURL.$NL.$NL);

	echo(__('atau, Anda dapat memilih paket membership lainnya dengan mangunjungi url di bawah ini :').$NL);
	echo($membershipURL.$NL.$NL);

?>