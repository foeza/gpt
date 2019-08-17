<?php 
		$client = $this->Rumahku->filterEmptyField($params, 'Agent', 'full_name');
		$email = $this->Rumahku->filterEmptyField($params, 'Agent', 'email');
		
		$bank_name = $this->Rumahku->filterEmptyField($params, 'KprBankTransfer', 'bank_name');
		$account_name = $this->Rumahku->filterEmptyField($params, 'KprBankTransfer', 'name_account');
		$account_number = $this->Rumahku->filterEmptyField($params, 'KprBankTransfer', 'no_account');
		$no_npwp = $this->Rumahku->filterEmptyField($params, 'KprBankTransfer', 'no_npwp');
?>
<table cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td>
              	<table style="line-height: 1.5em;">
          			<?php  
          					echo $this->Rumahku->_callLbl('table', __('Nama Agen'), sprintf(__(': %s'), $client));
          					echo $this->Rumahku->_callLbl('table', __('Email'), sprintf(__(': %s'), $email));

          					echo $this->Rumahku->_callLbl('table', __('Nama Bank'), sprintf(__(': %s'), $bank_name));
          					echo $this->Rumahku->_callLbl('table', __('Nama Rekening'), sprintf(__(': %s'), $account_name));
          					echo $this->Rumahku->_callLbl('table', __('No. Rekening'), sprintf(__(': %s'), $account_number));
          					echo $this->Rumahku->_callLbl('table', __('No. NPWP'), sprintf(__(': %s'), $no_npwp));
          			?>
              	</table>
			</td>
		</tr>
	</tbody>
</table>