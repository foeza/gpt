<?php
	$dataCompany = Configure::read('Config.Company.data');

	$invoiceID		= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'id');
	$userID			= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'user_id');
	$invoiceNumber	= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'invoice_number');
	$status			= $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrder', 'status');

	$invoiceStatus = $this->Rumahku->filterEmptyField($params, 'UserIntegratedOrderAddon', 'payment_status');
	if($invoiceStatus == 'cancelled'){
		$status = $invoiceStatus;
	}

	switch($status){
		case 'cancelled' :
			$showResponse = false;
			$content = 'Data form integrasi Anda telah dibatalkan';
		break;
		case 'request' : 
			$showResponse = true;
			$content = 'Selamat, data form integrasi Anda telah berhasil disimpan, berikut adalah rincian form integrasi :';
		break;
		case 'renewal' : 
			$showResponse = true;
			$content = 'Selamat, data form integrasi Anda telah berhasil disimpan, berikut adalah rincian form integrasi :';
		break;
		default : 
			$showResponse = false;
			$content = null; 
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

	echo $this->element('emails/html/user_integrated/order_detail', array(
		'params' => $params,
	));

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="margin-bottom:15px;" height="2" bgcolor="#E3E3E3"></td>
		</tr>
		<?php
				if($showResponse && $invoiceID) {
		?>
		<tr>
			<td>
				<?php
					$refererURL	= $this->Rumahku->filterEmptyField($dataCompany, 'UserCompanyConfig', 'domain', FULL_BASE_URL);

					$invoiceToken	= md5($invoiceNumber . $invoiceID . $userID);
					$detailURL		= array(
						'controller'	=> 'users', 
						'action'		=> 'checkout_addon', 
						'admin'			=> true, 
						$invoiceID, 
						$invoiceNumber, 
						$invoiceToken, 
					);

					$detailURL	= $this->Html->url($detailURL);
					$detailURL	= preg_replace('/([^:])(\/{2,})/', '$1/', $refererURL.$detailURL);
					$linkText	= 'Lakukan Pembayaran';
					$message	= 'Anda dapat melakukan pembayaran dengan menekan tombol di bawah ini :';

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
		<?php		
				}
		?>
	</tbody>
</table>