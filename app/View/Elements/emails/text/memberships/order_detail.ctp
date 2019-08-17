<?php

	$params			= empty($params) ? null : $params;
	$showRemark		= isset($showRemark) ? $showRemark : false;
	$showMessage	= isset($showMessage) ? $showMessage : false;
	$showOrder		= isset($showOrder) ? $showOrder : true;
	$showInvoice	= isset($showInvoice) ? $showInvoice : true;

	if($params){
		$siteName			= Configure::read('__Site.site_name');
		$currency			= Configure::read('__Site.config_currency_code');
		$paymentChannels	= Configure::read('__Site.payment_channels');
		$orderItemTypes		= Configure::read('Global.Data.order_item_types');

		$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
		$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
		$subject		= $this->Rumahku->filterEmptyField($params, 'subject');

	//	order detail
		$orderID		= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'id');
		$orderNumber	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'order_number');
		$name			= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'name');
		$email			= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'email');
		$phone			= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'phone');
		$principleName	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'principle_name');
		$principleEmail	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'principle_email');
		$principlePhone	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'principle_phone');
		$companyName	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'company_name');
		$domain			= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'domain');
		$message		= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'message');
		$remark			= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'remark');
		$isPrinciple	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'is_principle');
		$status			= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'status');
		$isPrinciple	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'is_principle');
		$orderDate		= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'created');
		$expiredDate	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'expired_date');

		$companyName	= $this->Rumahku->filterEmptyField($params, 'UserCompany', 'name', $companyName);
		$domain			= $this->Rumahku->filterEmptyField($params, 'UserCompanyConfig', 'domain', $domain);

	//	package detail
		$packageID		= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'id');
		$packageSlug	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'slug');
		$packageName	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'name');
		$monthDuration	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'month_duration', 0);

	//	invoice detail
		$invoiceID		= $this->Rumahku->filterEmptyField($params, 'Payment', 'id');
		$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'Payment', 'invoice_number');
		$paymentCode	= $this->Rumahku->filterEmptyField($params, 'Payment', 'payment_code');
		$baseAmount		= $this->Rumahku->filterEmptyField($params, 'Payment', 'base_amount', 0);
		$discountAmount	= $this->Rumahku->filterEmptyField($params, 'Payment', 'discount_amount', 0);
		$totalAmount	= $this->Rumahku->filterEmptyField($params, 'Payment', 'total_amount', 0);
		// $expiredDate	= $this->Rumahku->filterEmptyField($params, 'Payment', 'expired_date');
		$transExpDate	= $this->Rumahku->filterEmptyField($params, 'Payment', 'transfer_expired_date');
		$paymentChannel	= $this->Rumahku->filterEmptyField($params, 'Payment', 'payment_channel');
		$paymentTenor	= $this->Rumahku->filterEmptyField($params, 'Payment', 'tenor');
		$paymentStatus	= $this->Rumahku->filterEmptyField($params, 'Payment', 'payment_status');
		$paymentDate	= $this->Rumahku->filterEmptyField($params, 'Payment', 'payment_datetime');
		$voucherCode	= $this->Rumahku->filterEmptyField($params, 'VoucherCode', 'code');

		$baseAmount		= $this->Rumahku->getCurrencyPrice($baseAmount, 0);
		$discountAmount	= $this->Rumahku->getCurrencyPrice($discountAmount, 0);
		$totalAmount	= $this->Rumahku->getCurrencyPrice($totalAmount, 0);
		$paymentMethod	= $this->Rumahku->filterEmptyField($paymentChannels, $paymentChannel);

		$dateFormat		= 'd M Y H:i';
		$orderDate		= $this->Rumahku->formatDate($orderDate, $dateFormat);
		$expiredDate	= $this->Rumahku->formatDate($expiredDate, $dateFormat);
		$transExpDate	= $this->Rumahku->formatDate($transExpDate, $dateFormat);
		$paymentDate	= $this->Rumahku->formatDate($paymentDate, $dateFormat);

		$NL = "\n";
	//	$NL = $this->Html->tag('br');

	//	DETAIL ORDER ===============================================================================

		if($showOrder && $orderID){
			echo($NL);
			echo(__('Detail Order').$NL.$NL);

			if($subject){
				echo(__('Subyek : %s', $subject).$NL);
			}

			echo(__('Nomor Order : %s', $orderNumber).$NL);
			
			if( !empty($packageName) ) {
				echo(__('Nama Paket : %s', $packageName).$NL);
			}
			
			echo(__('Diajukan Oleh : %s', $name).$NL);
			echo(__('Email : %s', $email).$NL);
			
			if( !empty($phone) ) {
				echo(__('No. Telepon : %s', $phone).$NL);
			}

			if($isPrinciple){
				if( !empty($principleName) ) {
					echo(__('Nama Principal : %s', $principleName).$NL);
				}

				if( !empty($principleEmail) ) {
					echo(__('Email Principal : %s', $principleEmail).$NL);
				}
			}

			echo(__('Nama Perusahaan : %s', $companyName).$NL);
			
			if( !empty($domain) ) {
				echo(__('Domain Website : %s', $domain).$NL);
			}

			if($showMessage && $message){
				echo(__('Pesan : %s', $message).$NL);
			}

			if($showMessage && $remark){
				echo(__('Keterangan : %s', $remark).$NL);
			}

			echo(__('Tgl. Pemesanan : %s', $orderDate).$NL);
			echo($NL.$NL);

			echo(__('Tgl. Kadaluarsa Invoice : %s', $expiredDate).$NL);
			echo($NL.$NL);

			
		}

	//	============================================================================================

	//	DETAIL INVOICE =============================================================================

		if($showInvoice && $invoiceID){
			echo(__('Detail Invoice').$NL.$NL);
			echo(__('Nomor Invoice : %s', $invoiceNumber).$NL);

			if($paymentMethod){
				echo(__('Metode Pembayaran : %s', $paymentMethod).$NL);
			}

			echo(__('Harga : %s', $baseAmount).$NL);

			if($voucherCode){
			//	echo(__('Kode Voucher : %s', $voucherCode).$NL);
				echo(__('Potongan : (%s)', $discountAmount).$NL);
			}

			echo(__('Total Pembayaran : %s', $totalAmount).$NL);
		}

	//	============================================================================================

	}

?>