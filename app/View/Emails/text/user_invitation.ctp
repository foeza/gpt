<?php

	$siteName		= Configure::read('__Site.site_name');
	$primeProfile	= Configure::read('__Site.company_profile');
	$primeEmail		= $this->Rumahku->filterEmptyField($primeProfile, 'email');
	$primePhone		= $this->Rumahku->filterEmptyField($primeProfile, 'phone');
	$primePhone2	= $this->Rumahku->filterEmptyField($primeProfile, 'phone2');
	$primeWhatsApp	= Configure::read('Global.Data.whatsapp_number');

	if($primePhone && $primePhone2){
		$primePhone = sprintf('%s / %s', $primePhone, $primePhone2);
	}
	else{
		$primePhone = sprintf('%s%s', $primePhone, $primePhone2);
		$primePhone = trim($primePhone);
	}

	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');

	$invoiceID		= $this->Rumahku->filterEmptyField($params, 'Payment', 'id');
	$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'Payment', 'invoice_number');

	$userID			= $this->Rumahku->filterEmptyField($params, 'User', 'id');
	$fullName		= $this->Rumahku->filterEmptyField($params, 'User', 'full_name');
	$email			= $this->Rumahku->filterEmptyField($params, 'User', 'email');
	$companyName	= $this->Rumahku->filterEmptyField($params, 'UserCompany', 'name');
	$domain			= $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain');
	$liveDate		= $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'live_date');
	$endDate		= $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'end_date');

	$principleID	= $this->Rumahku->filterEmptyField($params, 'Principle', 'id');
	$principleName	= $this->Rumahku->filterEmptyField($params, 'Principle', 'full_name');
	$principleEmail	= $this->Rumahku->filterEmptyField($params, 'Principle', 'email');

	$isPrinciple	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'is_principle');
	$packageName	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'name');
	$monthDuration	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'month_duration', 0);
	
	$dateFormat	= 'd M Y';
	$liveDate	= date('Y-m-d', strtotime($endDate . ' - ' . $monthDuration . ' month'));
	$liveDate	= $this->Rumahku->formatDate($liveDate, $dateFormat);
	$endDate	= $this->Rumahku->formatDate($endDate, $dateFormat);

	if(substr($domain, -1) != '/'){
		$domain.= '/';
	}

	$NL = "\n";
	$NL = $this->Html->tag('br');

	if($receiverEmail == $principleEmail){
		$content = __('Selamat, Anda telah terdaftar sebagai Principal di %s.', $siteName);
	}
	else{
		$content = __('Website pesanan Anda telah selesai kami proses.');
	}

	echo($NL.$NL);
	echo(__($content).$NL.$NL);

	if($receiverEmail == $principleEmail){
		$content		= 'Silakan login dengan mengunjungi url di bawah untuk dapat mengelola dan memaksimalkan produktifitas perusahaan Anda.';
		$principleToken	= $this->Rumahku->filterEmptyField($params, 'UserConfig', 'token');
		$detailURL		= $this->Html->url(array(
			'controller'	=> 'users', 
			'action'		=> 'verify', 
			'admin'			=> true, 
			$principleID, 
			$principleToken, 
		));

		$detailURL = preg_replace('/([^:])(\/{2,})/', '$1/', $domain.$detailURL);
	}
	else{
		echo(__('Rincian Pemesanan').$NL.$NL);
		echo(__('No. Invoice : %s', $invoiceNumber).$NL);
		echo(__('Nama Principal : %s', $principleName).$NL);
		echo(__('Email Principal : %s', $principleEmail).$NL);
		echo(__('Nama Perusahaan : %s', $companyName).$NL);
		echo(__('Domain Website : %s', $domain).$NL);
		echo(__('Durasi : %s bulan', $monthDuration).$NL);
		echo(__('Periode Tayang : %s - %s', $liveDate, $endDate).$NL.$NL);

		$content = 'Email pemberitahuan dan akses website telah kami kirimkan kepada Principal %s dengan email tujuan %s.';
		$content = __($content, $principleName, $principleEmail);
	}

	echo(__($content).$NL);

	if(!empty($detailURL)){
		echo($detailURL.$NL);
	}

	echo($NL);
	echo(__('Anda dapat mengunduh Buku Panduan penggunaan website kami dengan mengunjungi url di bawah ini :').$NL);
	echo(Configure::read('__Site.user_manual_download_url'));
	echo($NL.$NL);
	echo(__('Butuh bantuan?, silakan hubungi kami di :').$NL.$NL);
	echo($siteName.$NL);
	echo(__('Support Email : %s', $principleEmail).$NL);
	echo(__('Phone : %s', $primePhone).$NL);
	echo(__('WhatsApp : %s', $primeWhatsApp).$NL.$NL);
	echo(__('Terima kasih telah menggunakan %s sebagai media pemasaran properti Anda.', $siteName).$NL.$NL);

?>