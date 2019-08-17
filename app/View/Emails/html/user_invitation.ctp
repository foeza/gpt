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
	$liveDate	= $this->Rumahku->formatDate($liveDate, $dateFormat);
	$endDate	= $this->Rumahku->formatDate($endDate, $dateFormat);

	$tableStyle		= 'margin: 0 auto 30px; background: #FFFFFF; text-align: left; font-size: 14px; border-top: 5px solid #E3E3E3; width: 550px;';
	$cellStyle		= 'padding: 10px 30px;';
	$fieldStyle		= 'width: 45%; color: #8B8B8B;' . $cellStyle;
	$valueStyle		= 'width: 55%;' . $cellStyle;
	$boldStyle		= 'margin: 0; font-weight: bold;';
	$buttonStyle	= 'padding: 12px 30px; border: none; text-decoration: none; background: #1a1a1e; color: #FFFFFF;';

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="margin-bottom: 20px; display: block; text-align: center;">
				<?php

					if($receiverEmail == $principleEmail){
						$content = __('Selamat, Anda telah terdaftar sebagai Principal di %s.', $this->Html->tag('strong', $siteName));
					}
					else{
						$content = __('Website pesanan Anda telah selesai kami proses.');
					}

					echo($this->Html->tag('p', $content, array(
						'style' => 'margin: 0; color: #8B8B8B; line-height: 28px;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>

<?php

	if($receiverEmail != $principleEmail){

		?>
		<table style="<?php echo($tableStyle); ?>" cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<tr>
					<td style="padding: 15px 30px; border-bottom: 1px dashed #E3E3E3;" colspan="2">
						<?php echo($this->Html->tag('p', __('Rincian Pemesanan'), array('style' => $boldStyle.'font-size: 14px;'))); ?>
					</td>
				</tr>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('No. Invoice'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $this->Html->tag('strong', $invoiceNumber), array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Nama Principal'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $fullName, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Email Principal'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php

							echo($this->Html->tag('p', $this->Html->link($email, sprintf('mailto:%s', $email)), array(
								'style' => 'margin: 0;', 
							)));

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
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Domain'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php

							echo($this->Html->tag('p', $this->Html->link($domain, $domain, array(
								'target' => '_blank',
							)), array(
								'style' => 'margin: 0;', 
							)));

						?>
					</td>
				</tr>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Nama Paket'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', $packageName, array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<tr>
					<td style="<?php echo($fieldStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Durasi'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('%s bulan', $monthDuration), array('style' => 'margin: 0;'))); ?>
					</td>
				</tr>
				<tr>
					<td style="<?php echo($fieldStyle . 'padding-bottom: 15px;'); ?>" valign="top">
						<?php echo($this->Html->tag('p', __('Periode Tayang'), array('style' => $boldStyle))); ?>
					</td>
					<td style="<?php echo($valueStyle . 'padding-bottom: 15px;'); ?>" valign="top">
						<?php

							$liveDate = date($dateFormat, strtotime($endDate . ' - ' . $monthDuration . ' month'));
							echo($this->Html->tag('p', sprintf('%s - %s', $liveDate, $endDate), array('style' => 'margin: 0;')));

						?>
					</td>
				</tr>
			</tbody>
		</table>
		<?php

	}

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="margin-bottom:15px;" height="2" bgcolor="#E3E3E3"></td>
		</tr>
		<tr>
			<td>
				<?php

					if($receiverEmail == $principleEmail){
						$content = __('Silakan login dengan menekan tombol di bawah untuk dapat mengelola dan memaksimalkan produktifitas perusahaan Anda.');
					}
					else{
						$content = 'Email pemberitahuan dan akses website telah kami kirimkan kepada Principal %s dengan email tujuan %s.';
						$content = __($content, $this->Html->tag('strong', $principleName), $this->Html->tag('strong', $this->Html->link($principleEmail, sprintf('mailto:%s', $principleEmail))));
					}

					echo($this->Html->tag('p', $content, array(
						'style'	=> 'padding: 20px 0; margin: 0; color: #8b8b8b; line-height: 24px;', 
						'align'	=> 'center', 
					)));

				?>
			</td>
		</tr>

		<?php if($receiverEmail == $principleEmail): ?>
		<tr>
			<td>
				<table width="auto" cellspacing="0" cellpadding="0" align="center">
					<tbody>
						<tr>
							<td height="40" bgcolor="1A1A1E">
								<?php

									$domain.= substr($domain, -1) == '/' ? '' : '/';

									$principleToken	= $this->Rumahku->filterEmptyField($params, 'UserConfig', 'token');
									$detailURL		= $this->Html->url(array(
										'controller'	=> 'users', 
										'action'		=> 'verify', 
										'admin'			=> true, 
										$principleID, 
										$principleToken, 
									));

									$detailURL = preg_replace('/([^:])(\/{2,})/', '$1/', $domain.$detailURL);
									$detailURL = $this->Html->link(__('Masuk Ke %s', $siteName), $detailURL, array(
										'style'		=> 'display: block; color: #fff; text-align: center; text-decoration: none; margin: 0 20px;', 
										'target'	=> '_blank', 
										'escape'	=> false, 
									));

									echo($detailURL);

								?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td height="20"></td>
		</tr>
		<?php endif; ?>

		<tr>
			<td style="margin-bottom:15px;" height="2" bgcolor="#E3E3E3"></td>
		</tr>
		<tr>
			<td>
				<?php

					echo($this->Html->tag('p', __('Anda dapat mengunduh Buku Panduan penggunaan website kami di sini :'), array(
						'style'	=> 'padding: 20px 0; margin: 0; color: #8b8b8b; line-height: 24px;', 
						'align'	=> 'center', 
					)));

				?>
			</td>
		</tr>
		<tr>
			<td>
				<table width="auto" cellspacing="0" cellpadding="0" align="center">
					<tbody>
						<tr>
							<td height="40" bgcolor="1A1A1E">
								<?php

									$downloadURL = Configure::read('__Site.user_manual_download_url');
									echo($this->Html->link(__('Unduh'), $downloadURL, array(
										'style'		=> 'display: block; color: #fff; text-align: center; text-decoration: none; margin: 0 20px;', 
										'target'	=> '_blank', 
										'escape'	=> false, 
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

<table style="margin: 20px auto 30px; background: #1A1A1E; font-size: 12px;" width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td>
				<table style="padding: 20px 30px; color: #FFFFFF;" width="100%" cellspacing="0" cellpadding="0" border="0">
					<tbody>
						<tr>
							<td style="padding: 15px 0 30px; text-align: center;">
								<?php echo($this->Html->tag('p', __('Butuh bantuan?, silakan hubungi kami di :'), array('style' => $boldStyle.'font-size: 14px;'))); ?>
							</td>								
						</tr>
						<tr>
							<td style="line-height: 26px; border-top: 1px dashed rgba(227,227,227, 0.3); padding: 10px 0;">
								<table width="100%" cellpadding="0" cellspacing="0">
									<tbody>
										<tr>
											<td colspan="2">
												<?php echo($this->Html->tag('strong', $siteName)); ?>
											</td>
										</tr>
										<tr>
											<td width="30%"><?php echo($this->Html->tag('strong', __('Support Email'))); ?></td>
											<td>
												<?php
													$primeEmail = $this->Html->link($primeEmail, sprintf('mailto:%s', $primeEmail), array(
														'style' => 'color: #EEEEEE; ', 
													));
													echo($this->Html->tag('strong', $primeEmail));
												?>
											</td>
										</tr>
										<tr>
											<td width="30%"><?php echo($this->Html->tag('strong', __('Phone'))); ?></td>
											<td><?php echo($this->Html->tag('strong', $primePhone)); ?></td>
										</tr>
										<tr>
											<td><?php echo($this->Html->tag('strong', __('WhatsApp'))); ?></td>
											<td><?php echo($this->Html->tag('strong', $primeWhatsApp)); ?></td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
	</tbody>
</table>

<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="margin-bottom:15px;" height="2" bgcolor="#E3E3E3"></td>
		</tr>
		<tr>
			<td align="center">
				<?php

					$siteName	= $this->Html->tag('strong', $siteName);
					$message	= __('Terima kasih telah menggunakan %s sebagai media pemasaran properti Anda.', $siteName);

					echo($this->Html->tag('p', $message, array(
						'style' => 'padding: 20px 0; margin: 0; color: #8b8b8b; line-height: 24px;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>