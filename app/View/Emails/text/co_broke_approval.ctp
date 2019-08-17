<?php 
		$owner_property = Common::hashEmptyField($params, 'owner_data');
		$broker_data 	= Common::hashEmptyField($params, 'broker_data');
		$status 		= Common::hashEmptyField($params, 'status');
		$decline_reason = Common::hashEmptyField($params, 'CoBrokeUser.decline_reason');
		$type_commission = Common::hashEmptyField($params, 'type_commission');
		
		$senderName 				= Common::hashEmptyField($broker_data, 'CoBrokeUser.name');
		$senderAddress 				= Common::hashEmptyField($broker_data, 'CoBrokeUser.address');
		$senderPhone 				= Common::hashEmptyField($broker_data, 'CoBrokeUser.phone');
		$id 						= Common::hashEmptyField($broker_data, 'CoBrokeUser.id');
		$final_commission 			= Common::hashEmptyField($broker_data, 'CoBrokeUser.final_commission');
		$final_type_commission 		= Common::hashEmptyField($broker_data, 'CoBrokeUser.final_type_commission');
		$final_type_price_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.final_type_price_commission');

		$commission_text = $this->CoBroke->commissionName($final_commission, $final_type_commission, $final_type_price_commission);

		$code = Common::hashEmptyField($broker_data, 'CoBrokeProperty.code');

		$title = Common::hashEmptyField($broker_data, 'Property.title');

		$UserProfile = Common::hashEmptyField($owner_property, 'UserProfile');
		$email = Common::hashEmptyField($owner_property, 'User.email');
		$phone = Common::hashEmptyField($UserProfile, 'phone');
		$no_hp = Common::hashEmptyField($UserProfile, 'no_hp');
		$no_hp_2 = Common::hashEmptyField($UserProfile, 'no_hp_2');
		$pin_bb = Common::hashEmptyField($UserProfile, 'pin_bb');

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

		$domain 			= Common::hashEmptyField($owner_property, 'UserCompanyConfig.domain');
		
		if(!empty($domain) && substr($domain, -1) == '/'){
			$domain = rtrim('/', $domain);
		}

		$location = '';
		
		if(!empty($company_region) || !empty($company_city) || !empty($company_subarea)){
			$location = sprintf('%s, %s, %s', $company_region, $company_city, $company_subarea);
		}

		if($status == 'approve'){
			$url = $this->Html->url(array(
				'controller' => 'co_brokes',
				'action' => 'print',
				$id,
				'backprocess' => true
			));

			if(!empty($domain)){
				$url = $domain.$url;
			}

			$header = sprintf(__('Selamat! pengajuan aplikasi Co-Broke Anda dengan kode Co-Broke #%s dan judul properti "%s" telah disetujui.'), $code, $title);

			$content = sprintf(__('Cetak Aplikasi : %s'), $url)."\n";
			$content .= sprintf(__('Download Aplikasi : %s'), $url.'/1')."\n";

			$content .= sprintf(__('Harap hubungi pihak agen atau principle yang bersangkutan untuk mengikuti tahap kerjasama lebih lanjut, terima kasih.'), $code, $title)."\n\n";
		}else{
			$header = sprintf(__('Maaf, pengajuan aplikasi Co-Broke Anda dengan kode Co-Broke #%s dan judul properti "%s" tidak disetujui oleh agen yang bersangkutan dengan alasan : %s'), $code, $title, $decline_reason)."\n";

			$content = __('Harap hubungi agen atau perusahaan terkait jika ingin melakukan kerja sama ini lebih lanjut, terima kasih.')."\n";
		}

		echo $header;

		if($status == 'approve'){
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

			echo "\n\n";
			echo __('Data Properti:')."\n";

			echo $label."\n";
			echo $title."\n";
			echo sprintf(__('Harga : %s'), $price)."\n";
			echo sprintf(__('Kode Co-Broke: #%s'), $code)."\n\n";
			
			echo __('Spesifikasi:')."\n";
			
			if(!empty($specs)){
				foreach ($specs as $key => $value) {
					$name = Common::hashEmptyField($value, 'name');
					$val = Common::hashEmptyField($value, 'value');

					echo sprintf(__('%s : %s'), $name, $val)."\n";
				}

				echo "\n";
			}

			if(!empty($final_commission)){
				echo sprintf('Komisi: %s', $commission_text);
				echo "\n";
			}

			if(!empty($UserProfile)){
				echo __('Kontak Agen')."\n";

				if(!empty($email)){
					echo sprintf(__('Email : %s'), $email)."\n";
				}
				if(!empty($phone)){
					echo sprintf(__('No. Telp : %s'), $phone)."\n";
				}
				if(!empty($phone)){
					echo sprintf(__('No. Telp : %s'), $phone)."\n";
				}
				if(!empty($no_hp)){
					echo sprintf(__('No. Hp : %s'), $no_hp)."\n";
				}
				if(!empty($no_hp_2)){
					echo sprintf(__('No. Hp 2 : %s'), $no_hp_2)."\n";
				}
				if(!empty($pin_bb)){
					echo sprintf(__('PIN BB : %s'), $pin_bb)."\n";
				}

				echo "\n";
			}

			if(!empty($UserCompany)){
				echo __('Kontak Perusahaan')."\n";

				if(!empty($contact_email)){
					echo sprintf(__('Email : %s'), $contact_email)."\n";
				}
				if(!empty($company_phone)){
					echo sprintf(__('No. Telp : %s'), $company_phone)."\n";
				}
				if(!empty($company_phone_2)){
					echo sprintf(__('No. Telp 2 : %s'), $company_phone_2)."\n";
				}
				if(!empty($fax)){
					echo sprintf(__('Fax : %s'), $fax)."\n";
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

					echo sprintf(__('Alamat : %s'), $temp)."\n";
				}
				
				echo "\n";
			}
		}

		echo $content;
?>