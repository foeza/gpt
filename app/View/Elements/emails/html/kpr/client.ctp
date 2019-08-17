<?php 
		$client = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_name');
		$client = $this->Rumahku->filterEmptyField($params, 'UserClient', 'full_name', $client);
		$no_hp = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_hp');
		$no_hp = $this->Rumahku->filterEmptyField($params, 'UserClient', 'no_hp', $no_hp);
		$email = $this->Rumahku->filterEmptyField($params, 'Kpr', 'client_email', '-');
		$email = $this->Rumahku->filterEmptyField($params, 'UserClient', 'email', $email);
?>
<table cellpadding="0" cellspacing="0" border="0">
	<tbody>
		<tr>
			<td>
              	<table style="line-height: 1.5em;">
          			<?php  
          					echo $this->Rumahku->_callLbl('table', __('Nama Klien'), sprintf(__(': %s'), $client));
          					echo $this->Rumahku->_callLbl('table', __('No. Tlp'), sprintf(__(': %s'), $no_hp));
          					echo $this->Rumahku->_callLbl('table', __('Email'), sprintf(__(': %s'), $email));
          			?>
              	</table>
			</td>
		</tr>
	</tbody>
</table>