<?php 
		$for_staff = !empty($for_staff)?$for_staff : false;
		$credit_date = $this->Rumahku->filterEmptyField($params, 'KprBankCreditAgreement', 'action_date');
		$note = $this->Rumahku->filterEmptyField($params, 'KprBankCreditAgreement', 'note', false, false, 'EOL');
		$date = $this->Rumahku->formatDate($credit_date, 'd M Y');
		$time = $this->Rumahku->formatDate($credit_date, 'H:i');

		if($for_staff){
			$staff_bank = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_name');
			$staff_phone = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_hp');
			$title = __('Nama Klien');
			$title_phone = __('No. Handphone');
		}else{
			$staff_bank = $this->Rumahku->filterEmptyField($params, 'KprBankCreditAgreement', 'staff_name');
			$staff_phone = $this->Rumahku->filterEmptyField($params, 'KprBankCreditAgreement', 'staff_phone');
			$title = __('Staff / Bertemu dengan');
			$title_phone = __('Kontak staff bank');
		}
?>
<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
	<tbody>
		<tr>
			<td>
				<?php  
						echo $this->Rumahku->_callLbl('table', __('Tanggal'), sprintf(__(': %s'), $date));
      					echo $this->Rumahku->_callLbl('table', __('Pukul'), sprintf(__(': %s'), $time));
      					echo $this->Rumahku->_callLbl('table', $title, sprintf(__(': %s'), $staff_bank));
      					echo $this->Rumahku->_callLbl('table', $title_phone, sprintf(__(': %s'), $staff_phone));
      					echo $this->Rumahku->_callLbl('table', __('Lokasi & Keterangan'), sprintf(__(': %s'), $note));
				?>
			</td>
		</tr>
	</tbody>
</table>