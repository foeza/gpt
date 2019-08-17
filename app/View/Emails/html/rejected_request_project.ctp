<?php
		$reason_rejected = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'reason_rejected');
		$project_name = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'project_name');
?>

<tr>
	<td align="center">
		<p style="margin: 0; padding-bottom: 30px; font-size: 20px; color: #24242f;">
		<?php echo __('Konfirmasi Request Project'); ?></p>
	</td>
</tr>

<tr>
	<td>
		<p style="margin-top: 0;">Mohon maaf! Permintaan untuk menampilkan project <?php echo $project_name; ?>, pada web Anda kami tolak dengan alasan <?php echo $reason_rejected; ?>. Harap hubungi admin support kami untuk informasi lebih lanjut. Terimakasih.</p><br/>
	</td>
</tr>