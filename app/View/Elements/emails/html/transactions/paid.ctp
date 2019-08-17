<?php

	$params				= empty($params) ? array() : $params;

	$paymentChannels	= Configure::read('Config.PaymentConfig.payment_channels');
	$paymentTenors		= Configure::read('Config.PaymentConfig.payment_tenors');

	$dateOption			= array(
		'time' => 'H:i', 
	//	'zone' => false, 
	);

	$cssRules = array(
		'table'			=> 'margin: 0 auto 30px; background: #FFFFFF; text-align: left; font-size: 14px; border-top: 5px solid #dfdfe8;', 
		'heading'		=> 'margin: 0 0 10px 0; padding: 15px 30px; border-bottom: 1px dashed	 #dfdfe8;font-size: 14px; font-weight: bold;', 
		'label'			=> 'margin: 0; padding: 6px 30px; color: #9d9db4; font-weight: bold;', 
		'content'		=> 'margin: 0; padding: 6px 30px;', 
		'green_link'	=> 'color: #05a558; text-decoration: none;', 
		'button'		=> 'padding: 15px 30px; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; color: #ffffff; text-decoration: none; background: #24242f; border-radius: 3px;', 
	);

//	invoice
	$orderPaymentID	= Common::hashEmptyField($params, 'OrderPayment.id');
	$invoiceNumber	= Common::hashEmptyField($params, 'OrderPayment.invoice_number', 'N/A');
	$baseAmount		= Common::hashEmptyField($params, 'OrderPayment.base_amount', 0);
	$discountAmount	= Common::hashEmptyField($params, 'OrderPayment.discount_amount', 0);
	$totalAmount	= Common::hashEmptyField($params, 'OrderPayment.total_amount', 0);
	$paymentChannel	= Common::hashEmptyField($params, 'OrderPayment.payment_channel');
	$tenor			= Common::hashEmptyField($params, 'OrderPayment.tenor');
	$createdDate	= Common::hashEmptyField($params, 'OrderPayment.created');
	$expiredDate	= Common::hashEmptyField($params, 'OrderPayment.expired_date');
	$paymentDate	= Common::hashEmptyField($params, 'OrderPayment.payment_datetime');

	$createdDate	= $this->Rumahku->getIndoDateCutom($createdDate, $dateOption);
	$expiredDate	= $this->Rumahku->getIndoDateCutom($expiredDate, $dateOption);
	$paymentDate	= $this->Rumahku->getIndoDateCutom($paymentDate, $dateOption);

//	company
	$companyID		= Common::hashEmptyField($params, 'OrderPaymentProfile.company_id');
	$companyName	= Common::hashEmptyField($params, 'OrderPaymentProfile.company_name', 'N/A');
	$companyEmail	= Common::hashEmptyField($params, 'OrderPaymentProfile.company_email');
	$companyPhone	= Common::hashEmptyField($params, 'OrderPaymentProfile.company_phone');
	$companyAddress	= Common::hashEmptyField($params, 'OrderPaymentProfile.company_address', 'N/A');

//	project
	$projectID		= Common::hashEmptyField($params, 'OrderPaymentProfile.project_id');
	$projectName	= Common::hashEmptyField($params, 'OrderPaymentProfile.project_name', 'N/A');
	$projectEmail	= Common::hashEmptyField($params, 'OrderPaymentProfile.project_email');
	$projectPhone	= Common::hashEmptyField($params, 'OrderPaymentProfile.project_phone');
	$projectAddress	= Common::hashEmptyField($params, 'OrderPaymentProfile.project_address', 'N/A');
	$projectDomain	= Common::hashEmptyField($params, 'OrderPaymentProfile.project_domain');

	$linkOpts = array(
		'target'	=> 'blank', 
		'escape'	=> false, 
		'style'		=> $cssRules['green_link'], 
	);

	if($projectEmail){
		$projectEmail = $this->Html->link($projectEmail, sprintf('mailto:%s', $projectEmail), $linkOpts);
	}
	else{
		$projectEmail = 'N/A';
	}

	if($projectDomain){
		$projectDomain = $this->Html->link($projectDomain, $projectDomain, $linkOpts);
	}
	else{
		$projectDomain = 'N/A';
	}

//	subscriber
	$userName		= Common::hashEmptyField($params, 'OrderPaymentProfile.user_name', 'N/A');
	$userEmail		= Common::hashEmptyField($params, 'OrderPaymentProfile.user_email');
	$userPhone		= Common::hashEmptyField($params, 'OrderPaymentProfile.user_phone');

	if($userEmail){
		$userEmail = $this->Html->link($userEmail, sprintf('mailto:%s', $userEmail), $linkOpts);
	}
	else{
		$userEmail = 'N/A';
	}

	if($userPhone){
		$userPhone = $this->Html->link($userPhone, sprintf('tel:%s', $userPhone), $linkOpts);
	}
	else{
		$userPhone = 'N/A';
	}

	################################################################################################

	$styleInvoiceLabel = 'padding: 15px 30px; margin: 0; font-size: 14px; font-weight: bold; border-bottom: 1px dashed #dfdfe8;';
	$styleInvoiceValue = 'padding: 15px 30px; margin: 0; border-bottom: 1px dashed #dfdfe8; font-size: 14px; font-weight: bold; text-align: right;';

	echo $this->Email->TableRow(__('Invoice'), $invoiceNumber, array(
		'labelStyle' => $styleInvoiceLabel,
		'valueStyle' => $styleInvoiceValue,
	));

	echo $this->Email->TableRow(__('Nama Pemesan'), $userName);
	echo $this->Email->TableRow(__('Email Pemesan'), $userEmail);
	echo $this->Email->TableRow(__('No. Telepon Pemesan'), $userPhone);

	echo $this->Email->TableRow(__('Nama Project'), $projectName);
	echo $this->Email->TableRow(__('Domain'), $projectDomain);
	echo $this->Email->TableRow(__('Tanggal Checkout'), $createdDate);
	echo $this->Email->TableRow(__('Tanggal Pembayaran'), $paymentDate);
	echo $this->Email->TableRow(__('Metode Pembayaran'), Common::hashEmptyField($paymentChannels, $paymentChannel));

	if($paymentChannel == '15'){
		$tenor = $tenor ? __('%s Bulan', $tenor) : __('Full Payment');

		echo($this->Email->TableRow(__('Tenor'), $tenor));
	}
	 else if($paymentChannel == '99'){
		$bankName		= Common::hashEmptyField($params, 'Bank.name');
		$accountNumber	= Common::hashEmptyField($params, 'Bank.BankConfirmation.account_number');

		echo $this->Email->TableRow(__('Bank Tujuan'), $bankName);
		echo $this->Email->TableRow(__('No Rekening Tujuan'), $accountNumber);
	}

	echo $this->Email->TableRow(__('Status'), $this->Html->tag('span', __('Lunas'), array(
		'style' => 'color:#ffffff;font-size:11px;text-decoration:none;outline:none;background-color:#16a765!important;margin:0;padding:5px 25px 5px;border:1px solid #16a765!important;',
	)));

?>
<tr>
	<td colspan="2">
		<hr style="border-top: 5px solid #dfdfe8;margin-top: 30px;">
	</td>
</tr>
<tr>
	<td colspan="2">
		<p style="<?php echo($cssRules['heading']); ?>">Informasi Pesanan</p>
	</td>
</tr>
<tr>
	<td colspan="2">
		<?php

		//	item list
			echo($this->element('emails/html/transactions/basket', array(
				'record'	=> $params,
				'options'	=> array(
					'show_total'	=> false, 
					'show_discount'	=> false, 
					'inline_css'	=> true, 
					'table'			=> array(
						'style' => 'border-collapse: collapse;', 
					), 
				), 
			)));

		?>
	</td>
</tr>
<tr>
	<td colspan="2">
		<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-top: 10px;padding: 10px 15px; background: #24242f; font-weight: bold; color: #FFFFFF;">
			<tbody>
				<tr>
					<td width="50%" style="padding: 5px 10px;"><p style="margin: 0;">Total Pembayaran</p></td>
					<td width="50%" style="padding: 5px 10px; text-align: right; font-size: 18px;">
						<p style="margin: 0;">
							<?php

								$baseAmount		= $this->Rumahku->getCurrencyPrice($baseAmount, '-');
								$discountAmount	= $this->Rumahku->getCurrencyPrice($discountAmount, '-');
								$totalAmount	= $this->Rumahku->getCurrencyPrice($totalAmount, '-');

								echo($totalAmount);

							?>
						</p>
					</td>
				</tr>
			</tbody>
		</table>
	</td>
</tr>