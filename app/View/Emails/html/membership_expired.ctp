<?php

	$siteName		= Configure::read('__Site.site_name');
	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');
	$orderID		= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'id');
	$senderName		= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'name');
	$senderCompany	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'company_name');
	$senderEmail	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'email');
	$senderPhone	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'phone');
	$senderMessage	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'message');
	$orderStatus	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'status');
	$orderDate		= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'created');
	$packageName	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'name');
	$packageSlug	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'slug');
	$invoiceID		= $this->Rumahku->filterEmptyField($params, 'Payment', 'id');
	$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'Payment', 'invoice_number');
	$itemAmount		= 1;
	$currency		= Configure::read('__Site.config_currency_code');
	$currency		= trim($currency);
	$baseAmount		= $this->Rumahku->filterEmptyField($params, 'Payment', 'base_amount', 0);
	$baseAmount		= $this->Number->currency($baseAmount, $currency, array('places' => 2));
	$totalAmount	= $this->Rumahku->filterEmptyField($params, 'Payment', 'total_amount', 0);
	$totalAmount	= $this->Number->currency($totalAmount, $currency, array('places' => 2));
	$documentType	= $this->Rumahku->filterEmptyField($params, 'document_type');
	$subjectType	= $documentType == 'request' ? 'Pengajuan' : 'Invoice';

	$even	= 'padding: 5px 10px;line-height: 20px;text-align: left;vertical-align: top;border-top: 1px solid #dddddd;';
	$odd	= $even.'background-color: #f5eee6;';

	$noticeText = $this->Html->tag('strong', __('%s Paket Membership Profesional', $subjectType));
	$noticeText = __('Berikut kami sampaikan informasi bahwa %s Anda sudah %s.', $noticeText, $this->Html->tag('strong', __('Kadaluarsa')));

	echo($this->Html->tag('p', $noticeText));
	echo($this->Html->tag('p', __('Berikut detail pemesanan Anda:')));
	echo($this->Html->tag('h3', __('Detail Order'), array(
		'style' => 'padding:0;margin: 0 0 5px;', 
	)));

?>
<table style="border: 1px solid #dddddd;border-collapse: separate;width: 100%;margin-bottom: 20px;max-width: 100%;background-color: transparent;border-spacing: 0;font-family: Helvetica Neue, Helvetica, rial, sans-serif;font-size: 14px;line-height: 20px;color: #333333;">
	<tbody>
		<tr>
			<td style="<?php echo $even; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Subyek'); ?></strong></td>
			<td width="70%" style="<?php echo $odd; ?>border-left: 1px solid #dddddd;border-top:none;"><?php echo($subject); ?></td>
		</tr>

		<?php if($packageName): ?>
		<tr>
			<td style="<?php echo $even; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Nama Paket'); ?></strong></td>
			<td width="70%" style="<?php echo $even; ?>border-left: 1px solid #dddddd;"><?php echo $packageName; ?></td>
		</tr>
		<?php endif; ?>

		<tr>
			<td style="<?php echo $even; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Nama'); ?></strong></td>
			<td width="70%" style="<?php echo $odd; ?>border-left: 1px solid #dddddd;"><?php echo $senderName; ?></td>
		</tr>
		<tr>
			<td style="<?php echo $even; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Nama Perusahaan'); ?></strong></td>
			<td width="70%" style="<?php echo $even; ?>border-left: 1px solid #dddddd;"><?php echo $senderCompany; ?></td>
		</tr>
		<tr>
			<td style="<?php echo $even; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Email'); ?></strong></td>
			<td width="70%" style="<?php echo $odd; ?>border-left: 1px solid #dddddd;"><?php echo $senderEmail; ?></td>
		</tr>
		<tr>
			<td style="<?php echo $even; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('No. Telp'); ?></strong></td>
			<td width="70%" style="<?php echo $even; ?>border-left: 1px solid #dddddd;"><?php echo $senderPhone; ?></td>
		</tr>
		<tr>
			<td style="<?php echo $even; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Pesan'); ?></strong></td>
			<td width="70%" style="<?php echo $odd; ?>border-left: 1px solid #dddddd;"><?php echo $senderMessage; ?></td>
		</tr>
		<tr>
			<td style="<?php echo $even; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Tanggal Kirim'); ?></strong></td>
			<td width="70%" style="<?php echo $even; ?>border-left: 1px solid #dddddd;"><?php echo $orderDate; ?></td>
		</tr>
	</tbody>
</table>

<?php if(!empty($invoiceNumber)): ?>
<?php

	echo($this->Html->tag('h3', __('Detail Invoice'), array(
		'style' => 'padding:0;margin: 0 0 5px;', 
	)));

?>
<table style="border: 1px solid #dddddd;border-collapse: separate;width: 100%;margin-bottom: 20px;max-width: 100%;background-color: transparent;border-spacing: 0;font-family: Helvetica Neue, Helvetica, rial, sans-serif;font-size: 14px;line-height: 20px;color: #333333;">
	<tbody>
		<tr>
			<td style="<?php echo $odd; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Nomor Invoice'); ?></strong></td>
			<td width="70%" style="<?php echo $odd; ?>border-left: 1px solid #dddddd;border-top:none;"><?php echo($invoiceNumber); ?></td>
		</tr>
		<tr>
			<td style="<?php echo $odd; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Harga'); ?></strong></td>
			<td width="70%" style="<?php echo $even; ?>border-left: 1px solid #dddddd;border-top:none;"><?php echo($baseAmount); ?></td>
		</tr>
		<?php /* 
		<tr>
			<td style="<?php echo $odd; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Jumlah'); ?></strong></td>
			<td width="70%" style="<?php echo $odd; ?>border-left: 1px solid #dddddd;border-top:none;"><?php echo($itemAmount); ?></td>
		</tr>
		*/ ?>
		<tr>
			<td style="<?php echo $odd; ?>border-top:none;background-color: #1e1e22;color:#ffffff;text-align: left;"><strong><?php echo __('Total'); ?></strong></td>
			<td width="70%" style="<?php echo $odd; ?>border-left: 1px solid #dddddd;border-top:none;"><?php echo($totalAmount); ?></td>
		</tr>
	</tbody>
</table>
<?php endif; ?>
<p>
	Jika Anda tertarik untuk memilih Paket Membership Profesional lainnya Anda bisa klik link berikut : 
	<?php

		$url = Configure::read('__Site.membership_request_url');
		echo($this->Html->link(__('Lihat Paket Membership Profesional'), $url, array('style'=>'color: #00af00; text-decoration: none;', 'target'=> '_blank')));

	?>
</p>
<hr style="border: none;border-top:1px dotted #E5E5E5;">