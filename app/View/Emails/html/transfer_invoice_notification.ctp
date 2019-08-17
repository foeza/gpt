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
	$transExpDate	= $this->Rumahku->filterEmptyField($params, 'Payment', 'transfer_expired_date');
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
	$paymentDate	= $this->Rumahku->formatDate($paymentDate, $dateFormat);
	$transExpDate	= $this->Rumahku->formatDate($transExpDate, $dateFormat);

	$tableStyle		= 'margin: 0 auto 30px; background: #FFFFFF; text-align: left; font-size: 14px; border-top: 5px solid #E3E3E3; width: 550px;';
	$cellStyle		= 'padding: 10px 30px;';
	$fieldStyle		= 'width: 45%; color: #8B8B8B;' . $cellStyle;
	$valueStyle		= 'width: 55%;' . $cellStyle;
	$boldStyle		= 'margin: 0; font-weight: bold;';
	$buttonStyle	= 'padding: 12px 30px; border: none; text-decoration: none; background: #1a1a1e; color: #FFFFFF;';

	$content = 'Untuk dapat menikmati fitur dan layanan %s, silakan lakukan pembayaran sesuai dengan informasi di bawah ini :';

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="padding: 30px 0 20px; display: block; text-align: center;">
				<?php

					echo($this->Html->tag('p', __('Terima kasih telah melakukan pemesanan'), array(
						'style' => 'margin: 0; font-size: 18px; color: #434343;', 
					)));

				?>
			</td>
		</tr>
		<tr>
			<td style="margin-bottom: 20px; display: block; text-align: center;">
				<?php

					echo($this->Html->tag('p', __($content, $this->Html->tag('strong', $siteName)), array(
						'style' => 'margin: 0; color: #8B8B8B; line-height: 28px;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>
<?php

	echo($this->element('emails/html/memberships/order_detail', array(
		'params'	=> $params, 
		// 'showOrder'	=> false, 
	)));

?>
<table style="padding: 15px 30px; margin: 0 auto 30px; background: #FFFFFF; text-align: left; text-align: center; border: 1px solid #e3e3e3" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="display: block; margin-bottom: 15px;">
				<?php

					echo($this->Html->tag('p', __('Kode Pembayaran'), array(
						'style' => 'margin: 0 0 10px; font-size: 14px; color: #8B8B8B;', 
					)));

				?>
			</td>
		</tr>
		<tr>
			<td>
				<?php echo($this->Html->tag('p', $paymentCode, array('style' => $boldStyle))); ?>
			</td>
		</tr>
		<tr>
			<td>
				<?php

					echo($this->Html->tag('p', __('Pembayaran paling lambat tanggal %s ', $this->Html->tag('strong', $transExpDate)), array(
						'style' => 'font-size: 12px; margin-top:15px;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>
<table style="margin-bottom: 20px; background: #1a1a1e; text-align: left; font-size: 12px;" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td>
				<table style="margin: auto; padding: 30px; color: #FFFFFF;" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td style="padding: 15px 0 30px; text-align: center;">
								<?php echo($this->Html->tag('p', __('Cara Melakukan Pembayaran'), array('style' => $boldStyle.'font-size: 14px;'))); ?>
							</td>								
						</tr>
						<tr>
							<td style="padding: 15px 0; border-bottom: 1px dashed rgba(227,227,227, 0.3);">
								<?php echo($this->Html->tag('p', __('Pembayaran Melalui ATM'), array('style' => $boldStyle))); ?>
							</td>
						</tr>
						<tr>
							<td style="line-height: 26px;">
								<?php 

									$steps = array(
										'Masukkan PIN ATM Anda', 
										'Pilih "Transfer". (Jika menggunakan ATM BCA, pilih "Lainnya" kemudian pilih "Transfer")', 
										'Pilih "Rekening Bank Lain"', 
										'Masukkan kode bank (Kode Permata adalah 013) diikuti dengan 16 digit kode pembayaran <strong>'.$paymentCode.'</strong> sebagai rekening tujuan, kemudian pilih "Benar"', 
										'Masukkan jumlah yang tepat sebagai nilai transaksi Anda. Jumlah transfer yang salah akan mengakibatkan pembayaran gagal', 
										'Pastikan kode bank, kode pembayaran dan jumlah pembayaran sudah benar, kemudian pilih "Benar"', 
										'Selesai', 
									);

									foreach($steps as &$step){
										$step = $this->Html->tag('li', $this->Html->tag('p', $step, array(
											'style' => 'margin: 0;', 
										)));
									}

									echo($this->Html->tag('ol', implode('', $steps), array(
										'style' => 'padding-left: 15px;', 
									)));

								?>
							</td>
						</tr>
						<tr>
							<td style="padding: 15px 0; border-bottom: 1px dashed rgba(227,227,227, 0.3);">
								<?php echo($this->Html->tag('p', __('Pembayaran Melalui Internet Banking'), array('style' => $boldStyle))); ?>
							</td>
						</tr>
						<tr>
							<td>
								<?php

									echo($this->Html->tag('p', __('Metode ini tidak bisa dilakukan menggunakan KlikBCA.'), array(
										'style' => 'margin: 15px 0 0;', 
									)));
								?>
							</td>
						</tr>
						<tr>
							<td style="line-height: 26px;">
								<?php 

									$steps = array(
										'Masuk ke akun Internet Banking Anda', 
										'Pilih "Transfer", kemudian pilih "Rekening Bank Lain". Masukkan kode bank (Kode Permata adalah 013) sebagai rekening tujuan', 
										'Masukkan jumlah yang tepat sebagai nilai transaksi Anda', 
										'Masukkan 16 digit kode pembayaran <strong>'.$paymentCode.'</strong> sebagai nomor tujuan', 
										'Pastikan kode bank, kode pembayaran dan jumlah pembayaran sudah benar, kemudian pilih "Benar"', 
										'Selesai', 
									);

									foreach($steps as &$step){
										$step = $this->Html->tag('li', $this->Html->tag('p', $step, array(
											'style' => 'margin: 0;', 
										)));
									}

									echo($this->Html->tag('ol', implode('', $steps), array(
										'style' => 'padding-left: 15px;', 
									)));

								?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>