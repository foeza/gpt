<?php

	$siteName		= Configure::read('__Site.site_name');
	$currency		= Configure::read('__Site.config_currency_code');
	$orderItemTypes	= Configure::read('Global.Data.order_item_types');

	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');

//	order detail
	$orderNumber	= $this->Rumahku->filterEmptyField($params, 'UserOrder', 'order_number');
	$itemType		= $this->Rumahku->filterEmptyField($params, 'UserOrder', 'item_type');
	$itemTypeName	= $this->RumahKu->filterEmptyField($orderItemTypes, $itemType);
	$orderDate		= $this->Rumahku->filterEmptyField($params, 'UserOrder', 'created');
	$orderDate		= $this->Rumahku->formatDate($orderDate, 'd/m/Y H:i');
	$fullName		= $this->Rumahku->filterEmptyField($params, 'User', 'name');
	$email			= $this->Rumahku->filterEmptyField($params, 'User', 'email');
	$companyName	= $this->Rumahku->filterEmptyField($params, 'UserCompany', 'name');
	$itemName		= NULL;

	if($itemType == 'deposit'){
		$itemName		= $this->Rumahku->filterEmptyField($params, 'DepositPackage', 'name');
		$durationType	= NULL;
	}
	else if(in_array($itemType, array('priority_agent', 'priority_property'))){
		$itemName		= $itemTypeName;
		$durationType	= __('Minggu');
	}
	else{
		$itemName		= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'name');
		$durationType	= __('Bulan');
	}

//	invoice detail
	$decimalPlaces	= 2;
	$invoiceID		= $this->Rumahku->filterEmptyField($params, 'Payment', 'id');
	$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'Payment', 'invoice_number');
	$baseAmount		= $this->Rumahku->filterEmptyField($params, 'Payment', 'base_amount', 0);
	$baseAmount		= $this->Rumahku->getCurrencyPrice($baseAmount, 0, $currency, $decimalPlaces);
	$discountAmount	= $this->Rumahku->filterEmptyField($params, 'Payment', 'discount_amount', 0);
	$discountAmount	= $this->Rumahku->getCurrencyPrice($discountAmount, 0, $currency, $decimalPlaces);
	$totalAmount	= $this->Rumahku->filterEmptyField($params, 'Payment', 'total_amount', 0);
	$totalAmount	= $this->Rumahku->getCurrencyPrice($totalAmount, 0, $currency, $decimalPlaces);
	$expiredDate	= $this->Rumahku->filterEmptyField($params, 'Payment', 'expired_date');
	// $transExpDate	= $this->Rumahku->filterEmptyField($params, 'Payment', 'transfer_expired_date');
	$paymentChannel	= $this->Rumahku->filterEmptyField($params, 'Payment', 'payment_channel');
	$paymentStatus	= $this->Rumahku->filterEmptyField($params, 'Payment', 'payment_status');
	$paymentDate	= $this->Rumahku->filterEmptyField($params, 'Payment', 'payment_datetime');
	$voucherCode	= $this->Rumahku->filterEmptyField($params, 'VoucherCode', 'code');
	$paymentMethod	= $paymentChannel == '05' ? 'ATM Transfer' : 'Pembayaran Via Alfamart';

	// data new record
	$new_read_record = $this->Rumahku->filterEmptyField($params, 'new_read_record');
	$paymentCode     = $this->Rumahku->filterEmptyField($new_read_record, 'Payment', 'payment_code');
	$transExpDate	 = $this->Rumahku->filterEmptyField($new_read_record, 'Payment', 'transfer_expired_date');

	$dateFormat		= 'd M Y H:i';
	$orderDate		= $this->Rumahku->formatDate($orderDate, $dateFormat);
	$expiredDate	= $this->Rumahku->formatDate($expiredDate, $dateFormat);
	$transExpDate	= $this->Rumahku->formatDate($transExpDate, $dateFormat);
	$paymentDate	= $this->Rumahku->formatDate($paymentDate, $dateFormat);

	$NL = "\n";
//	$NL = $this->Html->tag('br');

	echo($NL.$NL);
	echo(__('Terima kasih telah melakukan pemesanan').$NL.$NL);
	echo(__('Untuk dapat menikmati fitur dan layanan %s, silakan lakukan pembayaran sesuai dengan informasi di bawah ini :', $siteName).$NL.$NL);

	echo($this->element('emails/text/memberships/order_detail', array(
		'params'	=> $params, 
		'showOrder'	=> false, 
	)));

	echo($NL);
	echo(__('Kode Pembayaran : %s', $paymentCode).$NL);
	echo(__('Tgl. Jatuh Tempo : %s', $transExpDate).$NL.$NL);
	echo(__('Cara Melakukan Pembayaran').$NL.$NL);

	$steps = array(
		'Masukkan PIN ATM Anda', 
		'Pilih "Transfer". (Jika menggunakan ATM BCA, pilih "Lainnya" kemudian pilih "Transfer")', 
		'Pilih "Rekening Bank Lain"', 
		'Masukkan kode bank (Kode Permata adalah 013) diikuti dengan 16 digit kode pembayaran ['.$payment_code.'] sebagai rekening tujuan, kemudian pilih "Benar"', 
		'Masukkan jumlah yang tepat sebagai nilai transaksi Anda. Jumlah transfer yang salah akan mengakibatkan pembayaran gagal', 
		'Pastikan kode bank, kode pembayaran dan jumlah pembayaran sudah benar, kemudian pilih "Benar"', 
		'Selesai', 
	);

	echo(__('Pembayaran Melalui ATM :').$NL);

	foreach($steps as $key => $step){
		echo(__('%s. %s', $key + 1, $step).$NL);
	}

	$steps = array(
		'Masuk ke akun Internet Banking Anda', 
		'Pilih "Transfer", kemudian pilih "Rekening Bank Lain". Masukkan kode bank (Kode Permata adalah 013) sebagai rekening tujuan', 
		'Masukkan jumlah yang tepat sebagai nilai transaksi Anda', 
		'Masukkan 16 digit kode pembayaran ['.$payment_code.'] sebagai nomor tujuan', 
		'Pastikan kode bank, kode pembayaran dan jumlah pembayaran sudah benar, kemudian pilih "Benar"', 
		'Selesai', 
	);

	echo($NL);
	echo(__('Pembayaran Melalui Internet Banking (Metode ini tidak bisa dilakukan menggunakan KlikBCA) :').$NL);

	foreach($steps as $key => $step){
		echo(__('%s. %s', $key + 1, $step).$NL);
	}

	echo($NL.$NL);

?>