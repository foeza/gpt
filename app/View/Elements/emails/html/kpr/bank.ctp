<?php     
		$bank = $this->Rumahku->filterEmptyField($params, 'Bank', 'name');
		$bank_phone = $this->Rumahku->filterEmptyField($params, 'Bank', 'phone');
    $bank_contact = $this->Rumahku->filterEmptyField($params, 'BankContact');
    $phone_contact_arr = Set::classicExtract($bank_contact, '{n}.BankContact.phone');
		$phone_center = $this->Rumahku->filterEmptyField($params, 'Bank', 'phone_center');
		$fax = $this->Rumahku->filterEmptyField($params, 'Bank', 'fax');
		$email = $this->Rumahku->filterEmptyField($params, 'Bank', 'email');
?>
<table cellpadding="0" cellspacing="0" border="0" style="margin-bottom:20px;">
	<tbody>
		<tr>
			<td>
      			<?php  
      				echo $this->Html->tag('div', $bank, array(
      						'style' => 'font-size: 14px;font-size: 16px;font-weight: bold;'
  						));
      				echo $this->Html->tag('div', sprintf(__('Email. %s'), $email), array(
      						'style' => 'font-size: 14px;'
  						));

              if(!empty($phone_contact_arr)){
                foreach($phone_contact_arr AS $key => $phone_contact){
                  echo $this->Html->tag('div', sprintf(__('No Tlp %s. %s'), ($key+1), $phone_contact), array(
                      'style' => 'font-size: 14px;'
                  ));
                }
              }else{
        				echo $this->Html->tag('div', sprintf(__('No Tlp. %s'), $bank_phone), array(
        						'style' => 'font-size: 14px;'
    						));
              }

      					if( !empty($phone_center) ) {
            				echo $this->Html->tag('div', sprintf(__('Call Center. %s'), $phone_center), array(
    	      						'style' => 'font-size: 14px;'
    	  						));
          			}

          			if( !empty($fax) ) {
      						echo $this->Html->tag('div', sprintf(__('Fax. %s'), $fax), array(
  	      						'style' => 'font-size: 14px;'
  	  						));
      					}
      			?>
			</td>
		</tr>
	</tbody>
</table>