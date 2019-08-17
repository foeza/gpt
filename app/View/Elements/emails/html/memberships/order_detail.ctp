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

		$tableStyle	= 'margin: 0 auto 30px; background: #FFFFFF; text-align: left; font-size: 14px; border-top: 5px solid #E3E3E3; width: 100%;';
		$cellStyle	= 'padding: 10px 30px;';
		$fieldStyle	= 'width: 45%; color: #8B8B8B;' . $cellStyle;
		$valueStyle	= 'width: 55%;' . $cellStyle;
		$boldStyle	= 'margin: 0; font-weight: bold;';

		?>

		<?php if($showOrder && $orderID): ?>
		<table style="<?php echo($tableStyle); ?>" cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<tr>
					<td style="padding: 15px 30px; border-bottom: 1px dashed #E3E3E3;" colspan="2">
						<?php echo($this->Html->tag('p', __('Detail Order'), array('style' => $boldStyle.'font-size: 14px;'))); ?>
					</td>
				</tr>

				<?php if($subject): ?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Subyek'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $subject, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Nomor Order'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $this->Html->tag('strong', $orderNumber), array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php
						if( !empty($packageName) ) {
				?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Nama Paket'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $packageName, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php
						}
				?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Diajukan Oleh'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $name, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Email'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php

							echo($this->Html->tag('p', $this->Html->link($email, sprintf('mailto:%s', $email)), array(
								'style' => 'margin: 0;', 
							)));

						?>
					</td>
				</tr>
				<?php
						if( !empty($phone) ) {
				?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('No. Telepon'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php

							echo($this->Html->tag('p', $this->Html->link($phone, sprintf('tel:%s', $phone)), array(
								'style' => 'margin: 0;', 
							)));

						?>
					</td>
				</tr>
				<?php
						}
				?>

				<?php
						if(empty($isPrinciple)):
							if( !empty($principleName) ) {
				?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Nama Principal'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $principleName, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php
							}

							if( !empty($principleEmail) ) {
				?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Email Principal'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php

							echo($this->Html->tag('p', $this->Html->link($principleEmail, sprintf('mailto:%s', $principleEmail)), array(
								'style' => 'margin: 0;', 
							)));

						?>
					</td>
				</tr>
				<?php
							}
						endif;
				?>

				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Nama Perusahaan'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $companyName, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>

				<?php
						if( !empty($domain) ) {
				?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Domain Website'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $domain, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>

				<?php
						}

						if($showMessage && $message):
				?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Pesan'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $message, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php endif; ?>

				<?php if($showRemark && $remark): ?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Keterangan'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $remark, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<td style="<?php echo($fieldStyle . 'padding-bottom: 15px;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Tgl. Pemesanan'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'padding-bottom: 15px;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', $orderDate, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>

				<tr>
					<td style="<?php echo($fieldStyle . 'padding-bottom: 15px;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Tgl. Kadaluarsa Invoice'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'padding-bottom: 15px;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', $expiredDate, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php endif; ?>

		<?php if($showInvoice && $invoiceID): ?>
		<table style="<?php echo($tableStyle); ?>" cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<tr>
					<td style="padding: 15px 30px; border-bottom: 1px dashed #E3E3E3;">
						<?php echo($this->Html->tag('p', __('No. Invoice'), array('style' => $boldStyle.'font-size: 14px;'))); ?>
					</td>
					<td style="padding: 15px 30px; border-bottom: 1px dashed #E3E3E3;">
						<?php

							echo($this->Html->tag('p', $invoiceNumber, array(
								'style' => $boldStyle.'font-size: 14px; text-align: right;', 
							)));

						?>
					</td>
				</tr>

				<?php if($paymentMethod): ?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Metode Pembayaran'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'text-align: right;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', $paymentMethod, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Harga'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'text-align: right;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', $baseAmount, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>

				<?php if($voucherCode): ?>
				<?php /* takut voucher disebar, kan ada voucher yang bisa berulang2
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Kode Voucher'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'text-align: right;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', $voucherCode, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				*/ ?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Potongan'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'text-align: right;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', sprintf('(%s)', $discountAmount), array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<td colspan="2">
						<table style="margin-top: 10px;padding: 10px 15px; background: #1a1a1e; font-weight: bold; color: #FFFFFF;" width="100%" cellspacing="0" cellpadding="0" border="0">
							<tbody>
								<tr>
									<td style="padding: 5px 10px;" width="50%">
										<?php echo($this->Html->tag('p', __('Total Pembayaran'), array('style' => 'margin: 0;'))); ?>
									</td>
									<td style="padding: 5px 10px; text-align: right; font-size: 18px;" width="50%">
										<?php echo($this->Html->tag('p', $totalAmount, array('style' => 'margin: 0;'))); ?>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			</tbody>
		</table>
		<?php endif; ?>

		<?php

	}

?>