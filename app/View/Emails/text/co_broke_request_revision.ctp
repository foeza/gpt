<?php 
		$owner_property = Common::hashEmptyField($params, 'owner_data');
		$broker_data 	= Common::hashEmptyField($params, 'broker_data');
		$status 		= Common::hashEmptyField($params, 'status');
		$type_commission = Common::hashEmptyField($params, 'type_commission');
		
		/*KOMISI*/
		$revision_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_commission');
		$request_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.request_commission');

		$request_type_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.type_commission');
		$revision_type_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_type_commission');
		$final_type_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.final_type_commission');

		$revision_type_price_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_type_price_commission');
		$type_price_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.type_price_commission');

		$request_text = $this->CoBroke->commissionName($request_commission, $request_type_commission, $type_price_commission);
		$revision_text = $this->CoBroke->commissionName($revision_commission, $revision_type_commission, $revision_type_price_commission);
		/*KOMISI*/
		
		$senderName 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.name');
		$senderAddress 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.address');
		$senderPhone 	= Common::hashEmptyField($broker_data, 'CoBrokeUser.phone');
		$id 			= Common::hashEmptyField($broker_data, 'CoBrokeUser.id');

		$code 			= Common::hashEmptyField($broker_data, 'CoBrokeProperty.code');
		$title 			= Common::hashEmptyField($broker_data, 'Property.title');

		$UserProfile 	= Common::hashEmptyField($owner_property, 'UserProfile');
		$email 			= Common::hashEmptyField($owner_property, 'User.email');
		$phone 			= Common::hashEmptyField($UserProfile, 'phone');
		$no_hp 			= Common::hashEmptyField($UserProfile, 'no_hp');
		$no_hp_2 		= Common::hashEmptyField($UserProfile, 'no_hp_2');
		$pin_bb 		= Common::hashEmptyField($UserProfile, 'pin_bb');

		$UserCompany 		= Common::hashEmptyField($owner_property, 'UserCompany');
		$company_phone 		= Common::hashEmptyField($UserCompany, 'phone');
		$company_phone_2 	= Common::hashEmptyField($UserCompany, 'phone_2');
		$contact_name 		= Common::hashEmptyField($UserCompany, 'contact_name');
		$contact_email 		= Common::hashEmptyField($UserCompany, 'contact_email');
		$fax 				= Common::hashEmptyField($UserCompany, 'fax');
		$address 			= Common::hashEmptyField($UserCompany, 'address');

		$company_region 	= Common::hashEmptyField($UserCompany, 'Region.name');
		$company_city 		= Common::hashEmptyField($UserCompany, 'City.name');
		$company_subarea 	= Common::hashEmptyField($UserCompany, 'Subarea.name');

		$domain = Common::hashEmptyField($owner_property, 'UserCompanyConfig.domain');
		
		if(!empty($domain) && substr($domain, -1) == '/'){
			$domain = rtrim('/', $domain);
		}

		$location = '';
		
		if(!empty($company_region) || !empty($company_city) || !empty($company_subarea)){
			$location = sprintf('%s, %s, %s', $company_region, $company_city, $company_subarea);
		}

		$approve_url = $this->Html->url(array(
			'controller' => 'co_brokes',
			'action' => 'approve_revision',
			$id,
			'backprocess' => true
		));

		$disapprove_url = $this->Html->url(array(
			'controller' => 'co_brokes',
			'action' => 'diapprove_revision',
			$id,
			'backprocess' => true
		));

		if(!empty($domain)){
			$approve_url = $domain.$approve_url;
			$disapprove_url = $domain.$disapprove_url;
		}

		$title 			= Common::hashEmptyField($broker_data, 'Property.title');
		$change_date 	= Common::hashEmptyField($broker_data, 'Property.change_date');
		$bt_commision 	= Common::hashEmptyField($broker_data, 'Property.bt');
		
		$code = Common::hashEmptyField($broker_data, 'CoBrokeProperty.code');
		$id = Common::hashEmptyField($broker_data, 'CoBrokeProperty.id');

		$user_name = Common::hashEmptyField($broker_data, 'User.full_name');
		$company_name = Common::hashEmptyField($broker_data, 'UserCompany.name');

		$label = $this->Property->getNameCustom($broker_data);
		$price = $this->Property->getPrice($broker_data, __('(Harap hubungi Agen terkait)'));

		$specs = $this->Property->getSpec($broker_data, array(), false, false);

		
		if(!empty($specs)){
			$temp_spec = array();
			foreach ($specs as $key => $value) {
				$temp_spec[] = sprintf('%s : %s', $value['name'], $value['value']);
			}

			$specs = implode(', ', $temp_spec);
		}

		printf(__('Anda mendapatkan revisi tentang komisi Co-broke dari agen properti yang bersangkutan. dari nilai yang Anda ajukan sebesar %s menjadi %s'), $request_text, $revision_text);

		echo "\n\n";
		printf(__('Setujui : %s'), $approve_url);
		echo "\n";
		printf(__('Tolak : %s'), $disapprove_url);
		echo "\n\n";

		printf(__('Harap hubungi pihak agen atau principle yang bersangkutan untuk mengikuti tahap kerjasama lebih lanjut, terima kasih.'), $code, $title);
		echo "\n\n";
		
		echo __('Data Properti:')."\n\n";

		echo $label."\n";
		echo $title."\n";
		echo $price."\n";
		printf(__('Kode Co-Broke: #%s'), $code); echo "\n";
		printf(__('Spesifikasi: %s'), $specs); echo "\n";
		
		if(!empty($revision_commission)){
			printf(__('Komisi : %s'), $revision_text);
		}
		echo "\n";

		if(!empty($UserProfile)){
			echo "\n".__('Kontak Agen')."\n";

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
			
		if(!empty($UserCompany)){
			echo __('Kontak Perusahaan');

			if(!empty($contact_email)){
				printf(__('Email : %s'), $contact_email);
				echo "\n";
			}
			if(!empty($company_phone)){
				printf(__('No. Telp : %s'), $company_phone);
				echo "\n";
			}
			if(!empty($company_phone_2)){
				printf(__('No. Telp 2 : %s'), $company_phone_2);
				echo "\n";
			}
			if(!empty($fax)){
				printf(__('Fax : %s'), $fax);
				echo "\n";
			}
			if(!empty($address) || !empty($location)){
				$temp = '';

				if(!empty($location)){
					$temp = $location;
				}
				if(!empty($address)){
					if(!empty($location)){
						$temp .= ' - ';
					}

					$temp .= $address;
				}

				printf(__('Alamat : %s'), $temp);
				echo "\n";
			}
		}

		echo $content;
?>