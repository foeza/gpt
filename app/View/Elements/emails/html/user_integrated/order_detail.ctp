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
						if( !empty($packageNameR123) ) {
				?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Nama Membership Rumah 123'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $packageNameR123, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php
						}

						if( !empty($packageNameOLX) ) {
				?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Nama Membership OLX'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $packageNameOLX, array('style' => 'margin: 0;'))); ?>
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

				<?php if ($is_all_addon): ?>

					<tr>
						<td style="<?php echo($fieldStyle); ?>" valign="top">
							<?php echo($this->Html->tag('p', __('Email All Addon'), array('style' => $boldStyle))); ?>
						</td>
						<td style="<?php echo($valueStyle); ?>" valign="top">
							<?php

								echo $this->Html->tag('p',
									$this->Html->link($email_all_addon, sprintf('mailto:%s', $email_all_addon)), array(
										'style' => 'margin: 0;', 
								));

							?>
						</td>
					</tr>

				<?php else: ?>

					<?php if ($addon_r123): ?>
						<tr>
							<td style="<?php echo($fieldStyle); ?>" valign="top">
								<?php echo($this->Html->tag('p', __('Email Addon R123'), array('style' => $boldStyle))); ?>
							</td>
							<td style="<?php echo($valueStyle); ?>" valign="top">
								<?php

									echo $this->Html->tag('p',
										$this->Html->link($email_r123, sprintf('mailto:%s', $email_r123)), array(
											'style' => 'margin: 0;', 
									));

								?>
							</td>
						</tr>
					<?php endif ?>

					<?php if ($addon_olx): ?>
						<tr>
							<td style="<?php echo($fieldStyle); ?>" valign="top">
								<?php echo($this->Html->tag('p', __('Email Addon OLX'), array('style' => $boldStyle))); ?>
							</td>
							<td style="<?php echo($valueStyle); ?>" valign="top">
								<?php

									echo $this->Html->tag('p',
										$this->Html->link($email_olx, sprintf('mailto:%s', $email_olx)), array(
											'style' => 'margin: 0;', 
									));

								?>
							</td>
						</tr>
					<?php endif ?>

				<?php endif ?>

				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('No. Telepon'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php

							echo $this->Html->tag('p',
								$this->Html->link($phone, sprintf('tel:%s', $phone)), array(
									'style' => 'margin: 0;', 
							));

						?>
					</td>
				</tr>

				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Nama Perusahaan'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $companyName, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<tr>
					<td style="<?php echo($fieldStyle . 'padding-bottom: 15px;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Tanggal Kirim'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'padding-bottom: 15px;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', $orderDate, array('style' => 'margin: 0;'))); ?>
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

				<?php if(!empty($packageNameR123)): ?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Harga Membership Rumah 123'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'text-align: right;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', $packagePriceR123, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php endif; ?>

				<?php if(!empty($packageNameOLX)): ?>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Harga Membership OLX'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'text-align: right;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', $packagePriceOLX, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<?php endif; ?>

				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Potongan'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'text-align: right;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', sprintf('(%s)', $discountAmount), array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>

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