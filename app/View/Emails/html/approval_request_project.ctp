<?php
		$company_name = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'company_name');
		$project_name = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'project_name');
		$commision = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'commision');
		$start_date = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'start_date');
		$end_date = $this->Rumahku->filterEmptyField($params, 'ApiRequestDeveloper', 'end_date');

		$customStart = $this->Rumahku->getIndoDateCutom($start_date);
		$customEnd = $this->Rumahku->getIndoDateCutom($end_date);
		
		$contacts = $this->Rumahku->filterEmptyField($params, 'ApiAdvanceDeveloper', 'Contact');

		$i= 1;
		foreach ($contacts as $key => $value) {
			$result = array();
			$phone = $this->Rumahku->filterEmptyField($value, 'ApiDeveloperContactInfo', 'value');
			$tmp_val['phone'.$i] = $phone;
			$i++;
		}
		$result['info_contact'] = $tmp_val;
		$phone1 = $this->Rumahku->filterEmptyField($result, 'info_contact', 'phone1');

		$commision =  $this->Number->format($commision, array(
		    'places' => 1,
		    'before' => '',
		    'escape' => false,
		    'decimals' => '.'
		));
?>

<tr>
	<td align="center">
		<p style="margin: 0; padding-bottom: 30px; font-size: 20px; color: #24242f;">
		<?php echo __('Konfirmasi Request Project'); ?></p>
	</td>
</tr>

<tr>
	<td>
		<p style="margin-top: 0;">Hai! <?php echo $company_name; ?></p>
		<p style="margin-top: 0;">Selamat! Permintaan Anda untuk menampilkan project <?php echo $project_name; ?> telah kami terima dan akan tayang dari tanggal <?php echo $customStart; ?> sampai dengan <?php echo $customEnd; ?>.</p>
		<p style="margin-top: 0;">Info menarik untuk Anda!<br/>
		Dapatkan komisi sebesar <?php echo $commision; ?>% untuk setiap unit project yang berhasil Anda jual.</p>
		<p>Untuk informasi lebih lanjut silakan hubungi <?php echo $phone1; ?></p>
		Terimakasih
	</td>
</tr>