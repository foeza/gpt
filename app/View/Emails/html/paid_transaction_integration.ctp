<?php

	$siteName			= Configure::read('__Site.site_name');
	$currency			= Configure::read('__Site.config_currency_code');
	$paymentChannels	= Configure::read('__Site.payment_channels');
	$orderItemTypes		= Configure::read('Global.Data.order_item_types');

	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');

//	invoice detail
	$invoiceID		= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'id');
	$userID			= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'user_id');
	$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'invoice_number');


	$content = 'Terima kasih telah melakukan pembayaran untuk transaksi dengan nomor invoice %s. ';
	$content.= 'Selanjutnya, Customer Support kami akan menghubungi Anda, maksimal 2 x 24 jam setelah akun Anda siap digunakan.';

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="margin-bottom: 20px; display: block; text-align: center;">
				<?php

					echo($this->Html->tag('p', __($content, $invoiceNumber), array(
						'style' => 'margin: 0; color: #8B8B8B; line-height: 28px;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>
<?php

	echo($this->element('emails/html/user_integrated/order_detail', array(
		'params' => $params, 
	)));

?>
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