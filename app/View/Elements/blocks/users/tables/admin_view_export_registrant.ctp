<?php

	$currency	= Configure::read('__Site.config_currency_code');
	$currency	= $currency ? trim($currency) : NULL;
	$values		= isset($values) ? $values : NULL;
	$filename	= 'List-Registrant';
	$date = date("Y-m-d");

	if($values){
		$no = 1;
		$content = '';
		foreach ($values as $key => $value) {
			$recordID 		  = Common::hashEmptyField($value, 'UserIntegratedOrderAddon.id');
			$userID 		  = Common::hashEmptyField($value, 'UserIntegratedOrderAddon.user_id');
			$invNumber 		  = Common::hashEmptyField($value, 'UserIntegratedOrderAddon.invoice_number');
			$R123packagePrice = Common::hashEmptyField($value, 'UserIntegratedOrderAddon.r123_base_price', 0);

			$package_name 	  = Common::hashEmptyField($value, 'UserIntegratedAddonPackageR123.name', '-');

			$val_address 	  = Common::hashEmptyField($value, 'UserIntegratedOrder');
			$city 		  	  = Common::hashEmptyField($val_address, 'City.name');
			$area 		  	  = Common::hashEmptyField($val_address, 'Subarea.name');
			$zip 		  	  = Common::hashEmptyField($val_address, 'Subarea.zip');

			$address 		  = Common::hashEmptyField($value, 'UserIntegratedOrder.address');
			$full_address	  = sprintf(__('%s, %s, %s, %s'), $address, $area, $city, $zip);

			$name 		  	  = Common::hashEmptyField($value, 'UserIntegratedOrder.name_applicant');
			$company_name 	  = Common::hashEmptyField($value, 'UserIntegratedOrder.company_name');
			$phone 			  = Common::hashEmptyField($value, 'UserIntegratedOrder.phone');
			$order_date 	  = Common::hashEmptyField($value, 'UserIntegratedOrder.created');

			$all_addon 	  	  = Common::hashEmptyField($value, 'UserIntegratedOrder.is_email_all_addon');
			$mail_all_addon   = Common::hashEmptyField($value, 'UserIntegratedOrder.email_all_addon');

			$addon_r123 	  = Common::hashEmptyField($value, 'UserIntegratedOrder.addon_r123');
			$email_r123 	  = Common::hashEmptyField($value, 'UserIntegratedOrder.email_r123');

			$addon_olx 	  	  = Common::hashEmptyField($value, 'UserIntegratedOrder.addon_olx');
			$email_olx 	  	  = Common::hashEmptyField($value, 'UserIntegratedOrder.email_olx');

			if ($all_addon) {
				$email = $mail_all_addon;
			} else {
				if ($addon_r123) {
					$email = $email_r123;
				}
			}

			$dateFormat		= 'd M Y H:i';
			$order_date		= $this->Rumahku->formatDate($order_date, $dateFormat);

			$rows = array(
				array(
						$no, $name, $full_address, $company_name, $phone, $email, $package_name, $order_date
				), 
			);

			$content.= $this->Html->tableCells($rows);
			$no++;
		}

		$thead_rows = array(
			array(
					'No', 'Nama Agent','Alamat Agent', 'Nama Perusahaan', 'Telephone', 'Email', 'Paket Membership', 'Tgl. Daftar'
			), 
		);
		$thead_content = $this->Html->tag('thead', $this->Html->tableCells($thead_rows));
		$content = $this->Html->tag('tbody', $content);

		$content = $this->Html->tag('table', $thead_content.$content);
		$filename = $filename.'_'.$date;
	}
	else{
		$content = $this->Html->tag('div', __('Data tidak ditemukan'), array('class' => 'wrapper-border'));
	}
// debug($content);die();
	header('Pragma: public');
	header('Expires: 0');
	header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename='.$filename.'.xls');
	header('Content-Transfer-Encoding: binary');

	echo($content);

?>