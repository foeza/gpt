<?php

	$siteName			= Configure::read('__Site.site_name');
	$currency			= Configure::read('__Site.config_currency_code');
	$paymentChannels	= Configure::read('__Site.payment_channels');
	$orderItemTypes		= Configure::read('Global.Data.order_item_types');

	$orderID		= $this->Rumahku->filterEmptyField($params, 'MembershipOrder', 'id');
	$packageID		= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'id');
	$packageSlug	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'slug');
	$packageName	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'name');
	$monthDuration	= $this->Rumahku->filterEmptyField($params, 'MembershipPackage', 'month_duration', 0);

	$receiverName	= $this->Rumahku->filterEmptyField($params, 'name');
	$receiverEmail	= $this->Rumahku->filterEmptyField($params, '_email');
	$subject		= $this->Rumahku->filterEmptyField($params, 'subject');

?>
<table width="100%" cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<td style="padding: 30px 0 20px; margin-bottom: 20px; display: block; text-align: center;">
				<?php

					echo($this->Html->tag('p', __($subject), array(
						'style' => 'margin: 0; font-size: 18px; color: #434343;', 
					)));

				?>
			</td>
		</tr>
	</tbody>
</table>
<?php

	echo($this->element('emails/html/memberships/order_detail', array(
		'params'		=> $params, 
		'showMessage'	=> true, 
		'showRemark'	=> true, 
		'showInvoice'	=> false, 
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
					$detailURL	= $this->Html->url(array(
						'controller'	=> 'membership_orders', 
						'action'		=> 'view', 
						'admin'			=> true, 
						$orderID, 
						$packageSlug, 
					), true);

					$detailURL = $this->Html->link(__('Proses Pengajuan'), $detailURL, array(
						'style'		=> 'display: block; color: #fff; text-align: center; text-decoration: none; margin: 0 20px;', 
						'target'	=> '_blank', 
					));

					$message = __('Untuk memproses pengajuan, silakan tekan tombol di bawah ini : ');
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