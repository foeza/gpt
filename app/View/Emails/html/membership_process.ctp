<?php

	$siteName			= Configure::read('__Site.site_name');
	$currency			= Configure::read('__Site.config_currency_code');
	$paymentChannels	= Configure::read('__Site.payment_channels');
	$orderItemTypes		= Configure::read('Global.Data.order_item_types');

	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');
	$is_admin		= $this->Rumahku->filterEmptyField($params, 'is_admin');

	$invoiceID		= $this->Rumahku->filterEmptyField($params, 'Payment', 'id');
	$userID			= $this->Rumahku->filterEmptyField($params, 'Payment', 'user_id');
	$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'Payment', 'invoice_number');
	$status			= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'status');
	$orderNumber	= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'order_number');
	$statusMessage	= 'Pesanan Paket Membership Profesional Anda telah';

//	untuk pembatalan invoice numpang sini, karna layoutnya sama
	$invoiceStatus = $this->Rumahku->filterEmptyField($params, 'Payment', 'payment_status');
	if($invoiceStatus == 'cancelled'){
		$status = $invoiceStatus;
	}

	$byText = !empty($is_admin) ? sprintf('dengan nomor order %s', $orderNumber) : 'Anda';

//	cancel itu request by user, sisanya eksekusi oleh admin. makanya ga pake "maaf"
	switch($status){
		case 'cancelled' :
			$showResponse	= false;
			$content		= sprintf('Pesanan Paket Membership Profesional %s telah dibatalkan', $byText);
		break;
		case 'approved' : 
			$showResponse	= true;
			$content		= sprintf('Selamat, pemesanan Paket Membership Profesional %s telah disetujui', $byText);
		break;
		case 'renewal' : 
			$showResponse	= true;
			$content		= sprintf('Selamat, pemesanan Paket Membership Profesional %s telah disetujui', $byText);
		break;
		case 'rejected' : 
			$showResponse	= false;
			$content		= sprintf('Mohon maaf, pemesanan Paket Membership Profesional %s telah kami tolak', $byText);
		break;
		default : 
			$showResponse	= false;
			$content		= null; 
		break;
	}

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="margin-bottom: 20px; display: block; text-align: center;">
				<?php

					echo($this->Html->tag('p', __($content), array(
						'style' => 'margin: 0; color: #8B8B8B; line-height: 28px;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>
<?php

	$showRemark		= in_array($status, array('approved', 'renewal')) === false;
	$showInvoice	= in_array($status, array('approved', 'renewal'));

	echo($this->element('emails/html/memberships/order_detail', array(
		'params'		=> $params, 
		'showMessage'	=> true, 
		'showRemark'	=> $showRemark, 
		'showInvoice'	=> $showInvoice, 
	)));

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="margin-bottom:15px;" height="2" bgcolor="#E3E3E3"></td>
		</tr>
		<tr>
			<td>
				<?php

					$siteName	= $this->Html->tag('strong', $siteName);
					$refererURL	= $this->Rumahku->filterEmptyField($params, 'Payment', 'referer_url', FULL_BASE_URL);

					if($showResponse && $invoiceID){
						if(strpos($refererURL, '/admin') !== false){
							$detailURL = array(
								'controller'	=> 'payments', 
								'action'		=> 'view', 
								'admin'			=> false, 
								$invoiceID, 
								$invoiceNumber, 
							);
						}
						else{
							$invoiceToken	= md5($invoiceNumber . $invoiceID . $userID);
							$detailURL		= array(
								'controller'	=> 'payments', 
								'action'		=> 'checkout', 
								'admin'			=> false, 
								$invoiceID, 
								$invoiceNumber, 
								$invoiceToken, 
							);
						}

					//	remove extra slash
						$detailURL	= $this->Html->url($detailURL);
						$detailURL	= preg_replace('/([^:])(\/{2,})/', '$1/', $refererURL.$detailURL);
						$detailURL	= str_replace('/admin/admin', '/admin', $detailURL);
						$linkText	= 'Lakukan Pembayaran';
						$message	= 'Anda dapat melakukan pembayaran dengan menekan tombol di bawah ini :';
					}
					else{
						$detailURL	= Configure::read('__Site.membership_request_url');
						$linkText	= 'Ajukan Paket Membership';
						$message	= 'Anda dapat mengajukan paket membership kembali dengan menekan tombol di bawah ini :';
					}

					$detailURL = $this->Html->link(__($linkText), $detailURL, array(
						'style'		=> 'display: block; color: #fff; text-align: center; text-decoration: none; margin: 0 20px;', 
						'target'	=> '_blank', 
					));

					echo($this->Html->tag('p', $message, array(
						'style'	=> 'padding: 20px 0; margin: 0; color: #8b8b8b; line-height: 24px;', 
						'align'	=> 'center', 
					)));

				?>
			</td>
		</tr>

		<?php if($detailURL): ?>
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
		<?php endif; ?>

	</tbody>
</table>