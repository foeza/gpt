<?php 
		$owner_property = Common::hashEmptyField($params, 'owner_data');
		$broker_data 	= Common::hashEmptyField($params, 'broker_data');
		$status 		= Common::hashEmptyField($params, 'status_approval');
		$decline_reason = Common::hashEmptyField($params, 'CoBrokeUser.decline_reason');
		$type_commission = Common::hashEmptyField($params, 'type_commission');
		
		$senderName 			= Common::hashEmptyField($broker_data, 'CoBrokeUser.name');
		$senderAddress 			= Common::hashEmptyField($broker_data, 'CoBrokeUser.address');
		$senderPhone 			= Common::hashEmptyField($broker_data, 'CoBrokeUser.phone');
		$id 					= Common::hashEmptyField($broker_data, 'CoBrokeUser.id');
		$final_commission 		= Common::hashEmptyField($broker_data, 'CoBrokeUser.final_commission');
		$final_type_commission 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.final_type_commission');
		$final_type_price_commission 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.final_type_price_commission');

		$final_text = $this->CoBroke->commissionName($final_commission, $final_type_commission, $final_type_price_commission);

		$code = Common::hashEmptyField($broker_data, 'CoBrokeProperty.code');

		$title = Common::hashEmptyField($broker_data, 'Property.title');

		$UserProfile = Common::hashEmptyField($broker_data, 'UserProfile');
		$email 		= Common::hashEmptyField($broker_data, 'User.email');
		$phone 		= Common::hashEmptyField($UserProfile, 'phone');
		$no_hp 		= Common::hashEmptyField($UserProfile, 'no_hp');
		$no_hp_2 	= Common::hashEmptyField($UserProfile, 'no_hp_2');
		$pin_bb 	= Common::hashEmptyField($UserProfile, 'pin_bb');
		$name_requester = Common::hashEmptyField($broker_data, 'User.full_name');

		$UserCompany = Common::hashEmptyField($owner_property, 'UserCompany');
		$company_phone = Common::hashEmptyField($UserCompany, 'phone');
		$company_phone_2 = Common::hashEmptyField($UserCompany, 'phone_2');
		$contact_name = Common::hashEmptyField($UserCompany, 'contact_name');
		$contact_email = Common::hashEmptyField($UserCompany, 'contact_email');
		$fax = Common::hashEmptyField($UserCompany, 'fax');
		$address = Common::hashEmptyField($UserCompany, 'address');

		$company_region = Common::hashEmptyField($UserCompany, 'Region.name');
		$company_city = Common::hashEmptyField($UserCompany, 'City.name');
		$company_subarea = Common::hashEmptyField($UserCompany, 'Subarea.name');

		$domain = Common::hashEmptyField($owner_property, 'UserCompanyConfig.domain');
		
		if(!empty($domain) && substr($domain, -1) == '/'){
			$domain = rtrim('/', $domain);
		}

		$location = '';
		
		if(!empty($company_region) || !empty($company_city) || !empty($company_subarea)){
			$location = sprintf('%s, %s, %s', $company_region, $company_city, $company_subarea);
		}

		if($status === true){
			$header = sprintf(__('Revisi komisi aplikasi Co-Broke Anda dengan "%s" telah disetujui.'), $name_requester);

			$url = $this->Html->url(array(
				'controller' => 'co_brokes',
				'action' => 'print',
				$id,
				'backprocess' => true
			));

			if(!empty($domain)){
				$url = $domain.$url;
			}
			
			$content = sprintf(__('Cetak Aplikasi : %s'), $url);
			$content .= sprintf(__('Download Aplikasi : %s'), $url.'/1');
		}else{
			$header = sprintf(__('Revisi komisi aplikasi Co-Broke Anda dengan "%s" telah ditolak.'), $name_requester);
			$content =  __('Harap hubungi broker terkait jika ingin melakukan kerja sama ini lebih lanjut, terima kasih.');
		}

		echo $header;

		if($status == 'approve'){
			$title 			= Common::hashEmptyField($broker_data, 'Property.title');
			$change_date 	= Common::hashEmptyField($broker_data, 'Property.change_date');
			$bt_commision 	= Common::hashEmptyField($broker_data, 'Property.bt');
			
			$code 	= Common::hashEmptyField($broker_data, 'CoBrokeProperty.code');
			$id 	= Common::hashEmptyField($broker_data, 'CoBrokeProperty.id');

			$user_name 		= Common::hashEmptyField($broker_data, 'User.full_name');
			$company_name 	= Common::hashEmptyField($broker_data, 'UserCompany.name');

			$label = $this->Property->getNameCustom($broker_data);
			$price = $this->Property->getPrice($broker_data, __('(Harap hubungi Agen terkait)'));

			$specs = $this->Property->getSpec($broker_data);

			echo __('Data Properti:')."\n\n";

			echo $label."\n";
			echo $title."\n";
			echo $price."\n";
			printf(__('Kode Co-Broke: #%s'), $code); echo "\n";

			echo __('Spesifikasi:')."\n\n";
			echo $specs."\n";

			if(!empty($final_commission)){
				printf('Komisi: %s', $final_text);
				echo "\n";
			}

			if(!empty($UserProfile)){
				echo __('Kontak Broker:')."\n\n";

				if(!empty($email)){
					printf(__('Email : %s'), $email);
					echo "\n";
				}
				if(!empty($phone)){
					printf(__('No. Telp : %s'), $phone);
					echo "\n";
				}
				if(!empty($no_hp)){
					printf(__('No. Hp : %s'), $no_hp);
					echo "\n";
				}
				if(!empty($no_hp_2)){
					printf(__('No. Hp 2 : %s'), $no_hp_2);
					echo "\n";
				}
				if(!empty($pin_bb)){
					printf(__('PIN BB : %s'), $pin_bb);
					echo "\n";
				}
			}
		}

		echo $content;
?>