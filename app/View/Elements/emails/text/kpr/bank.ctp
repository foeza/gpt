<?php 
	    $bank_contact = $this->Rumahku->filterEmptyField($params, 'BankContact');

		$bank = $this->Rumahku->filterEmptyField($params, 'Bank', 'name');
		$bank_phone = $this->Rumahku->filterEmptyField($params, 'Bank', 'phone');
	    $phone_contact_arr = Set::classicExtract($bank_contact, '{n}.BankContact.phone');
		$phone_center = $this->Rumahku->filterEmptyField($params, 'Bank', 'phone_center');
		$fax = $this->Rumahku->filterEmptyField($params, 'Bank', 'fax');
		$email = $this->Rumahku->filterEmptyField($params, 'Bank', 'email');

		echo $bank;
		echo "\n";
		printf(__('Email. %s'), $email);
		echo "\n";

		if(!empty($bank_contact)){
			foreach($phone_contact_arr AS $key => $phone_contact){
				printf(__('No Tlp %s. %s'), ($key+1), $phone_contact);
				echo "\n";
			}
		}else{
			printf(__('No Tlp. %s'), $bank_phone);		
			echo "\n";
		}

		if( !empty($phone_center) ) {
			echo "\n";
			printf(__('Call Center. %s'), $phone_center);
		}

		if( !empty($fax) ) {
			echo "\n";
			printf(__('Fax. %s'), $fax);
		}
		
		echo "\n\n";
?>