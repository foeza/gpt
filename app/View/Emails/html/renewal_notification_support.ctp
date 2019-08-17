<?php

	$siteName		= Configure::read('__Site.site_name');
	$logoPath		= Configure::read('__Site.logo_photo_folder');

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

	$PICSalesName	= $this->Rumahku->filterEmptyField($params, 'PICSales', 'full_name');
	$PICSalesEmail	= $this->Rumahku->filterEmptyField($params, 'PICSales', 'email');

	$photoSize = $this->Rumahku->_rulesDimensionImage($logoPath, 'large', 'size');
	$logoImage = $this->Rumahku->photo_thumbnail(array(
		'save_path'	=> $logoPath, 
		'src'		=> $logo, 
		'size'		=> 'xm', 
	), array(
		'title' => sprintf('%s Logo', $companyName), 
		'alt'	=> Inflector::slug($companyName, '-').'-logo', 
	));

	$logoImage	= $this->Html->tag('span', $logoImage);
	$expMessage	= 'akan segera';

	if(strtotime(date('Y-m-d')) > strtotime($endDate)){
		$expMessage = 'telah';
	}

	$dateFormat	= 'd M Y';
	$liveDate	= $this->Rumahku->formatDate($liveDate, $dateFormat);
	$endDate	= $this->Rumahku->formatDate($endDate, $dateFormat);

	$tableStyle		= 'margin: 0 auto 30px; background: #FFFFFF; text-align: left; font-size: 14px; border-top: 5px solid #E3E3E3; width: 550px;';
	$cellStyle		= 'padding: 10px 30px;';
	$fieldStyle		= 'width: 45%; color: #8B8B8B;' . $cellStyle;
	$valueStyle		= 'width: 55%;' . $cellStyle;
	$boldStyle		= 'margin: 0; font-weight: bold;';
	$buttonStyle	= 'padding: 12px 30px; border: none; text-decoration: none; background: #1a1a1e; color: #FFFFFF;';

	$content = 'Berikut ini merupakan Principal dengan masa berlaku Paket Membership Profesional yang %s berakhir, dengan detail sebagai berikut :';

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="margin-bottom: 20px; display: block; text-align: center;">
				<?php

					echo($this->Html->tag('p', __($content, $expMessage, $this->Html->tag('strong', $siteName)), array(
						'style' => 'margin: 0; color: #8B8B8B; line-height: 28px;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>
<table style="<?php echo($tableStyle); ?>" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="padding: 15px 30px; border-bottom: 1px dashed #E3E3E3;" colspan="2">
				<?php echo($this->Html->tag('p', __('Rincian Paket Membership'), array('style' => $boldStyle.'font-size: 14px;'))); ?>
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
				<?php echo($this->Html->tag('p', $getInterval, array('style' => 'margin: 0;'))); ?>
			</td>
		</tr>
		<tr>
			<td style="<?php echo($fieldStyle . 'padding-bottom: 15px;'); ?>" valign="top">
				<?php echo($this->Html->tag('p', __('Periode Tayang'), array('style' => $boldStyle))); ?>
			</td>
			<td style="<?php echo($valueStyle . 'padding-bottom: 15px;'); ?>" valign="top">
				<?php

					// $liveDate = date($dateFormat, strtotime($endDate . ' - ' . $monthDuration . ' month'));
					echo($this->Html->tag('p', sprintf('%s - %s', $liveDate, $endDate), array('style' => 'margin: 0;')));

				?>
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
			<td>
				<?php

					$detailURL = $this->Html->url(array(
						'controller'	=> 'membership_orders', 
						'action'		=> 'add', 
						'admin'			=> true, 
						$principleID,
					), true);

					$detailURL = $this->Html->link(__('Proses Perpanjangan'), $detailURL, array(
						'style'		=> 'display: block; color: #fff; text-align: center; text-decoration: none; margin: 0 20px;', 
						'target'	=> '_blank', 
					));

					echo($this->Html->tag('p', __('Untuk memproses perpanjangan Paket Membership Profesional Principal bersangkutan, Anda bisa menekan tombol berikut :'), array(
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
								<?php echo($detailURL); ?>
							</td>
						</tr>
					</tbody>
				</table>
			</td>
		</tr>
		<tr>
			<td align="center">
				<?php

					$sendEmailURL = $this->Html->url(array(
						'controller'	=> 'users', 
						'action'		=> 'view_principle', 
						'admin'			=> true, 
						$principleID, 
					), true);

					$sendEmailURL = $this->Html->link(__('di sini'), $sendEmailURL, array(
						'target' => '_blank', 
					));

					echo($this->Html->tag('p', __('atau, Anda dapat mengirim email notifikasi ke Principal yang bersangkutan : %s', $sendEmailURL), array(
						'style'	=> 'padding: 20px 0; margin: 0; color: #8b8b8b; line-height: 24px;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>