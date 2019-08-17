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

	$getInterval    = $this->Rumahku->dateInterval( $liveDate, $endDate );

	$principleID	= $this->Rumahku->filterEmptyField($params, 'Payment', 'principle_id');

	$packageName	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'name');
	$monthDuration	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'month_duration', 0);
	
	$expMessage	= 'akan segera';
	if(strtotime(date('Y-m-d')) > strtotime($endDate)){
		$expMessage = 'telah';
	}

	$dateFormat	= 'd M Y';
	// $liveDate	= date('Y-m-d', strtotime($endDate . ' - ' . $monthDuration . ' month'));
	$liveDate	= $this->Rumahku->formatDate($liveDate, $dateFormat);
	$endDate	= $this->Rumahku->formatDate($endDate, $dateFormat);
	$content	= 'Berikut ini merupakan Principal dengan masa berlaku Paket Membership Profesional yang %s berakhir, dengan detail sebagai berikut :';

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
	echo(__('Durasi : %s', $getInterval).$NL);
	echo(__('Periode Tayang : %s - %s', $liveDate, $endDate).$NL);

	$detailURL = $this->Html->url(array(
		'controller'	=> 'membership_orders', 
		'action'		=> 'add', 
		'admin'			=> true, 
		$principleID,
	), true);

	echo($NL.$NL);
	echo(__('Untuk memproses perpanjangan Paket Membership Profesional Principal bersangkutan, Anda bisa mengunjungi url di bawah ini :').$NL);
	echo($detailURL.$NL.$NL);

	$sendEmailURL = $this->Html->url(array(
		'controller'	=> 'users', 
		'action'		=> 'view_principle', 
		'admin'			=> true, 
		$principleID, 
	), true);

	echo(__('atau, Anda dapat mengirim email notifikasi ke Principal yang bersangkutan dengan mengunjungi url di bawah ini :').$NL);
	echo($sendEmailURL.$NL.$NL);

?>