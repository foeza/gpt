<?php
	$params			= empty($params) ? null : $params;
	$showOrder		= isset($showOrder) ? $showOrder : true;
	$showInvoice	= isset($showInvoice) ? $showInvoice : true;

	if($params){
		$siteName = Configure::read('__Site.site_name');
		$currency = Configure::read('__Site.config_currency_code');
		$paymentChannels = Configure::read('__Site.payment_channels');
		$orderItemTypes = Configure::read('Global.Data.order_item_types');

		$subject = $this->Rumahku->filterEmptyField($params, 'subject');

	//	order detail
		$orderID		 = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'id');
		$orderNumber	 = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'order_number');
		$name			 = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'name_applicant');
		$phone			 = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'phone');
		$companyName	 = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'company_name');
		$is_all_addon    = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'is_email_all_addon');
		$email_all_addon = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'email_all_addon');

	//  package detail r123
		$packageNameR123  = $this->Rumahku->filterEmptyField($params, 'UserIntegratedAddonPackageR123', 'name');
		$packagePriceR123 = $this->Rumahku->filterEmptyField($params, 'UserIntegratedAddonPackageR123', 'price', 0);
		$packagePriceR123 = $this->Rumahku->getCurrencyPrice($packagePriceR123, 0);
		$addon_r123		= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'addon_r123');
		$email_r123		= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'email_r123');

	//  package detail olx
		$packageNameOLX  = $this->Rumahku->filterEmptyField($params, 'UserIntegratedAddonPackageOLX', 'name');
		$packagePriceOLX = $this->Rumahku->filterEmptyField($params, 'UserIntegratedAddonPackageOLX', 'price', 0);
		$packagePriceOLX = $this->Rumahku->getCurrencyPrice($packagePriceOLX, 0);
		$addon_olx		 = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'addon_olx');
		$email_olx		 = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'email_olx');

		$orderDate		= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'created');

	//  invoice detail
		$invoiceID		= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'id');
		$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'invoice_number');
		$paymentCode	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'payment_code');
		$discountAmount	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'discount_price', 0);
		$totalAmount	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'total_price', 0);
		$expiredDate	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'expired_date');
		$transExpDate	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'transfer_expired_date');
		$paymentChannel	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'payment_channel');
		$paymentTenor	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'tenor');
		$paymentStatus	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'payment_status');
		$paymentDate	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'payment_datetime');

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
			
			if( !empty($packageNameR123) ) {
				echo(__('Nama Membership Rumah 123 : %s', $packageNameR123).$NL);
			}

			if( !empty($packageNameOLX) ) {
				echo(__('Nama Membership OLX : %s', $packageNameOLX).$NL);
			}
			
			echo(__('Diajukan Oleh : %s', $name).$NL);

			if( $is_all_addon ) {
				echo(__('Email All Addon : %s', $email_all_addon).$NL);
			} else {
				if ($addon_r123) {
					echo(__('Email Addon R123 : %s', $email_r123).$NL);
				}
				if ($addon_olx) {
					echo(__('Email Addon OLX : %s', $email_olx).$NL);
				}
			}
			
			echo(__('No. Telepon : %s', $phone).$NL);
			echo(__('Nama Perusahaan : %s', $companyName).$NL);

			echo(__('Tgl. Kirim : %s', $orderDate).$NL);
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

			if (!empty($packageNameR123)) {
				echo(__('Harga Membership Rumah 123 : %s', $packageNameR123).$NL);
			}

			if (!empty($packageNameOLX)) {
				echo(__('Harga Membership OLX : %s', $packageNameOLX).$NL);
			}

			echo(__('Total Pembayaran : %s', $totalAmount).$NL);
		}

	//	============================================================================================

	}

?>