<?php
		$invoiceNumber	= Common::hashEmptyField($params, 'OrderPayment.invoice_number');

		$linkDetail		= array(
			'admin'			=> true, 
			'controller'	=> 'projects', 
			'action'		=> 'invoice',
			$invoiceNumber,
		);

		$linkDetail = $this->Html->url($linkDetail, true);
?>
<tr>
	<td>
			<?php
					$text = __('Selamat, Invoice %s telah berhasil dibayar', $invoiceNumber);

					echo $this->Html->tag('p', $text, array(
						'style' => 'font-weight:normal;font-size:14px;line-height:1.6;margin:0 0 20px;padding:0;',
					));
			?>
			<table border="0" cellpadding="0" cellspacing="0" width="100%" style="margin-bottom: 30px;background: #FFFFFF;text-align: left;font-size: 14px;border-top: 5px solid #dfdfe8;">
				<tbody>
					<?php
							echo $this->element('emails/html/transactions/paid');
					?>
				</tbody>
			</table>
	</td>
</tr>
<tr>
	<td colspan = "2">
		<span style="font-weight:normal;font-size:14px;line-height:1.6;margin:0 0 20px;padding:0;">Klik tombol dibawah ini untuk melihat detail pembayaran.</span><br><br>
	</td>
</tr>
<tr >
	<td  align="center" colspan = "2" style="padding:15px;">
		<?php
				echo $this->Html->link(__('Detail'), $linkDetail, array(
					'style' => 'padding: 15px 30px; font-size: 12px; text-transform: uppercase; letter-spacing: 2px; color: #ffffff; text-decoration: none; background: #24242f; border-radius: 3px;',
					'escape' => false,
					'target' => '_blank',
				));
		?>
	</td>
</tr>