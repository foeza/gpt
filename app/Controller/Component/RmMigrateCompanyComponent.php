<?php
class RmMigrateCompanyComponent extends Component {
	var $components = array('RmCommon'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function __callBeforeSave($data, $value = array()){
		if(!empty($data)){
			$user_id = $this->RmCommon->filterEmptyField($data, 'MigrateCompany', 'user_id');

			/*Krusial*/
			$data['MigrateAdvanceCompany']['is_agent'] 		= 1;
			$data['MigrateAdvanceCompany']['is_property'] 	= 1;
			/*Krusial*/

			$MigrateAdvance = !empty($data['MigrateAdvanceCompany']) ? $data['MigrateAdvanceCompany'] : array();

			$data_migrates = $this->__getConfigMigrate();

			if(!empty($data_migrates)){
				$this->MigrateConfigCompany = $this->controller->MigrateCompany->MigrateConfigCompany;

				$temp = array();
				foreach ($data_migrates as $field => $val) {
					$slug = $this->RmCommon->filterEmptyField($val, 'slug');
					$order = $this->RmCommon->filterIssetField($val, 'order');

					if(!empty($data['MigrateAdvanceCompany'][$field])){
						$check_data = $this->MigrateConfigCompany->getData('first', array(
							'conditions' => array(
								'MigrateConfigCompany.user_id' => $user_id,
								'MigrateConfigCompany.slug' => $slug
							),
							'order' => array(
								'MigrateConfigCompany.created' => 'DESC'
							)
						));

						$value_last_data = $this->RmCommon->filterEmptyField($check_data, 'MigrateConfigCompany', $slug);

						$temp[] = array(
							'user_id' 	=> $user_id,
							'slug' 		=> $slug,
							'order' 	=> $order,
							'value' 	=> $value_last_data
						);
					}
				}

				if(!empty($temp)){
					$data['MigrateConfigCompany'] = $temp;
				}
			}

			$migrate_company_id = $this->RmCommon->filterEmptyField($value, 'MigrateCompany', 'id');

			if(!empty($value['MigrateAdvanceCompany']['id'])){
				$data['MigrateAdvanceCompany']['id'] = $value['MigrateAdvanceCompany']['id'];
			}
			if(!empty($value['MigrateAdvanceCompany']['migrate_company_id'])){
				$data['MigrateAdvanceCompany']['migrate_company_id'] = $value['MigrateAdvanceCompany']['migrate_company_id'];
			}

			if(isset($data['MigrateAdvanceCompany']['check_all'])){
				unset($data['MigrateAdvanceCompany']['check_all']);
			}
		}

		return $data;
	}

	function setConvertToCompanyWebV2($data){
		$arr_model = array(
			'UserProfile',
			'UserCompany'
		);

		$group_id = $this->RmCommon->filterEmptyField($data, 'User', 'group_id');
		
		if(intval($group_id) == 3){
			$data['User']['parent_id'] = 0;
		}

		if(!empty($data['UserCompany']['user_id'])){
			unset($data['UserCompany']['user_id']);
		}

		foreach ($arr_model as $key => $model) {
			if(!empty($data[$model]['id'])){
				unset($data[$model]['id']);
			}
		}

		$data['UserConfig']['personal_page']  				= $this->RmCommon->filterEmptyField($data, 'User', 'personal_page');
		$data['UserConfig']['personal_page_publish']  		= $this->RmCommon->filterEmptyField($data, 'User', 'personal_page_publish');
		$data['UserConfig']['username_disabled']  			= $this->RmCommon->filterEmptyField($data, 'User', 'username_disabled');
		$data['UserConfig']['user_sales_id'] 				= $this->RmCommon->filterEmptyField($data, 'User', 'user_sales_id');
		$data['UserConfig']['user_sales_date'] 				= $this->RmCommon->filterEmptyField($data, 'User', 'user_sales_date');
		$data['UserConfig']['activation_code'] 				= $this->RmCommon->filterEmptyField($data, 'User', 'activation_code');
		$data['UserConfig']['point'] 						= $this->RmCommon->filterEmptyField($data, 'User', 'point');
		$data['UserConfig']['contribution_point'] 			= $this->RmCommon->filterEmptyField($data, 'User', 'contribution_point');
		$data['UserConfig']['contribution_point_expired'] 	= $this->RmCommon->filterEmptyField($data, 'User', 'contribution_point_expired');
		$data['UserConfig']['token'] 						= $this->RmCommon->filterEmptyField($data, 'User', 'token');
		$data['UserConfig']['personal_page_publish'] 		= $this->RmCommon->filterEmptyField($data, 'User', 'personal_page_publish');
		$data['UserConfig']['personal_page'] 				= $this->RmCommon->filterEmptyField($data, 'User', 'personal_page');
		$data['UserConfig']['personal_page_publish']  		= $this->RmCommon->filterEmptyField($data, 'User', 'personal_page_publish');
		$data['UserConfig']['register_by'] 					= $this->RmCommon->filterEmptyField($data, 'User', 'register_by');
		$data['UserConfig']['personal_page']  				= $this->RmCommon->filterEmptyField($data, 'User', 'personal_page');
		$data['UserConfig']['last_activity'] 				= $this->RmCommon->filterEmptyField($data, 'User', 'last_activity');
		$data['UserConfig']['last_login'] 					= $this->RmCommon->filterEmptyField($data, 'User', 'last_login');
		$data['UserConfig']['commission'] 					= $this->RmCommon->filterEmptyField($data, 'User', 'sharing_commission');

		if(empty($data['UserConfig']['token'])){
			unset($data['UserConfig']['token']);
		}

		$language 				= $this->RmCommon->filterEmptyField($data, 'UserProfile', 'languages');
		$user_property_types 	= $this->RmCommon->filterEmptyField($data, 'UserProfile', 'user_property_types');
		$client_types 			= $this->RmCommon->filterEmptyField($data, 'UserProfile', 'client_types');
		$certifications 		= $this->RmCommon->filterEmptyField($data, 'UserProfile', 'certifications');
		$other_certifications 	= $this->RmCommon->filterEmptyField($data, 'UserProfile', 'other_certifications');
		$specialists 			= $this->RmCommon->filterEmptyField($data, 'UserProfile', 'specialists');
		$address 				= $this->RmCommon->filterEmptyField($data, 'UserProfile', 'address');
		$address2 				= $this->RmCommon->filterEmptyField($data, 'UserProfile', 'address2');

		$data['UserProfile']['address'] = '';
		if(!empty($address)){
			$data['UserProfile']['address'] = $address;
		}

		if(!empty($address2)){
			$data['UserProfile']['address'] .= ', '.$address2;

			unset($data['UserProfile']['address2']);
		}

		if(!empty($language)){
			$this->Language = ClassRegistry::init('Language'); 

			$language = explode(',', $language);

			foreach ($language as $key => $value) {
				$lang = trim(strtolower($value));
				$language_que = $this->Language->find('first', array(
					'conditions' => array(
						'LOWER(Language.name) LIKE' => '%'.$lang.'%'
					)
				));

				if(!empty($language_que['Language']['id'])){
					$data['UserLanguage']['language_id'][$key] = $language_que['Language']['id'];
				}else{
					$data['UserLanguage']['language_id'][$key] = -1;
					$data['UserLanguage']['other_text'][$key] = $lang;
				}
			}

			unset($data['UserProfile']['languages']);
		}
		
		if(!empty($user_property_types)){
			$this->PropertyType = ClassRegistry::init('PropertyType'); 

			$user_property_types = explode(',', $user_property_types);

			foreach ($user_property_types as $key => $value) {
				$lang = trim(strtolower($value));

				if($lang == 'ruko atau rukan'){
					$lang = 'ruko';
				}

				$property_type = $this->PropertyType->find('first', array(
					'conditions' => array(
						'LOWER(PropertyType.name) LIKE' => '%'.$lang.'%'
					)
				));

				if(!empty($property_type['PropertyType']['id'])){
					$data['UserPropertyType']['property_type_id'][$key] = $property_type['PropertyType']['id'];
				}
			}

			unset($data['UserProfile']['user_property_types']);
		}

		if(!empty($client_types)){
			$this->ClientType = ClassRegistry::init('ClientType'); 

			$client_types = explode(',', $client_types);

			foreach ($client_types as $key => $value) {
				$lang = trim(strtolower($value));

				if(in_array($lang, array('pembeli properti', 'penyewa'))){
					$lang = 'pembeli/penyewa properti';
				}

				$client_type = $this->ClientType->find('first', array(
					'conditions' => array(
						'LOWER(ClientType.name) LIKE' => '%'.$lang.'%'
					)
				));

				if(!empty($client_type['ClientType']['id'])){
					$data['UserClientType']['client_type_id'][$key] = $client_type['ClientType']['id'];
				}
			}

			unset($data['UserProfile']['client_types']);
		}

		if(!empty($certifications) || !empty($other_certifications)){
			$this->AgentCertificate = ClassRegistry::init('AgentCertificate'); 

			if(!empty($certifications)){
				$certifications = explode(',', $certifications);
				
				foreach ($certifications as $key => $value) {
					$lang = trim(strtolower($value));

					$agent_certification = $this->AgentCertificate->find('first', array(
						'conditions' => array(
							'LOWER(AgentCertificate.name) LIKE' => '%'.$lang.'%'
						)
					));

					if(!empty($agent_certification['AgentCertificate']['id'])){
						$data['UserAgentCertificate']['agent_certificate_id'][$key] = $agent_certification['AgentCertificate']['id'];
					}
				}
			}

			if(!empty($other_certifications)){
				$data['UserAgentCertificate']['other_id'] = 1;
				$data['UserAgentCertificate']['other_certifications'] = $other_certifications;
			}

			unset($data['UserProfile']['certifications']);
			unset($data['UserProfile']['other_certifications']);
		}

		if(!empty($data['User']['code'])){
			unset($data['User']['code']);
		}

		if(!empty($specialists)){
			$this->Specialist = ClassRegistry::init('Specialist'); 

			$specialists = explode(',', $specialists);

			foreach ($specialists as $key => $value) {
				$lang = trim(strtolower($value));

				$Specialist = $this->Specialist->find('first', array(
					'conditions' => array(
						'LOWER(Specialist.name) LIKE' => '%'.$lang.'%'
					)
				));
				
				if(!empty($Specialist['Specialist']['id'])){
					$data['UserSpecialist']['specialist_id'][$key] = $Specialist['Specialist']['id'];
				}
			}

			unset($data['UserProfile']['specialists']);
		}

		return $data;
	}

	function setConvertPropertyToCompanyWebV2($data){
		/*Property*/
		$id 				= $this->RmCommon->filterEmptyField($data, 'Property', 'id');
		$active 			= $this->RmCommon->filterEmptyField($data, 'Property', 'active');
		$status 			= $this->RmCommon->filterEmptyField($data, 'Property', 'status');
		$sold 				= $this->RmCommon->filterEmptyField($data, 'Property', 'sold');
		$published 			= $this->RmCommon->filterEmptyField($data, 'Property', 'published');
		$deleted 			= $this->RmCommon->filterEmptyField($data, 'Property', 'deleted');
		$inactive 			= $this->RmCommon->filterEmptyField($data, 'Property', 'inactive');
		$is_priority 		= $this->RmCommon->filterEmptyField($data, 'Property', 'is_priority');
		$lot_unit 			= $this->RmCommon->filterEmptyField($data, 'Property', 'lot_unit');
		$property_action_id = $this->RmCommon->filterEmptyField($data, 'Property', 'property_action_id');
		$lot_size 			= $this->RmCommon->filterEmptyField($data, 'Property', 'lot_size');
		$lot_width 			= $this->RmCommon->filterEmptyField($data, 'Property', 'lot_width');
		$lot_length 		= $this->RmCommon->filterEmptyField($data, 'Property', 'lot_length');
		$lot_dimension 		= $this->RmCommon->filterEmptyField($data, 'Property', 'lot_dimension');
		$title 				= $this->RmCommon->filterEmptyField($data, 'Property', 'title');
		$currency_id 		= $this->RmCommon->filterEmptyField($data, 'Property', 'currency_id');
		$user_id 			= $this->RmCommon->filterEmptyField($data, 'Property', 'user_id');
		$property_view		= $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'property_view');
		/*END Property*/

		$property_type_name		= $this->RmCommon->filterEmptyField($data, 'PropertyType', 'name');
		$property_action_name	= $this->RmCommon->filterEmptyField($data, 'PropertyAction', 'name');

		$property_sold_status	= $this->RmCommon->filterEmptyField($data, 'PropertySold', 'active');

		$session_id = $data['Property']['session_id'] = String::uuid();

		/*PropertyRevision*/
		$temp = $revisions = $this->RmCommon->filterEmptyField($data, 'PropertyRevision', 'revisions');
		if(!empty($revisions)){
			$revisions = @unserialize($revisions);
			if ($revisions !== false) {
			    $revisions = unserialize($temp);
			}

			if(!empty($revisions)){
				$data['Property']['in_update'] = 1;

				$revisions 		= $this->setConvertSetToNewCompany($revisions);
	        	$data_revision 	= $this->shapingArrayRevision($revisions, false);

	        	foreach ($data_revision as $key => $value) {
					if(!empty($value['model']) && $value['model'] == 'Property'){
						$data_revision[$key]['step'] = 'Basic';
					}else if(!empty($value['model']) && $value['model'] == 'PropertyAddress'){
						$data_revision[$key]['step'] = 'Address';
					}else if(!empty($value['model']) && $value['model'] == 'PropertyAsset'){
						$data_revision[$key]['step'] = 'Asset';
					}
				}

				$data['PropertyRevision'] = $data_revision;
			}
		}
		/*END PropertyRevision*/

		$data['Property']['property_id_target'] = $id;

		$arr_model = array(
			'Property',
			'PropertyAddress',
			'PropertyAsset',
			'PropertySold'
		);

		foreach ($arr_model as $key => $model) {
			if(!empty($data[$model]['id'])){
				unset($data[$model]['id']);
			}
			if(!empty($data[$model]['property_id'])){
				unset($data[$model]['property_id']);
			}
		}

		if ( !empty($active) && empty($status) && empty($sold) && !empty($published) && empty($deleted) ){ /*pending*/
			$data['Property']['active'] = 1;
			$data['Property']['status'] = 1;
			$data['Property']['sold'] = 0;
			$data['Property']['published'] = 1;
			$data['Property']['deleted'] = 0;
		} else if (  !empty($active) && !empty($status) && !empty($sold) && !empty($published) && empty($deleted) && empty($inactive) ){ /*sold*/
			$data['Property']['sold'] = 1;
		} else if ( empty($active) && !empty($published) && empty($deleted) ){ /*inactive*/
			$data['Property']['status'] = 0;
			$data['Property']['deleted'] = 0;
			$data['Property']['published'] = 1;
		} else if ( empty($published) && empty($deleted) ){ /*Unpublish*/
			$data['Property']['deleted'] = 0;
			$data['Property']['published'] = 0;
		} 

		$data['Property']['priority'] 			= $is_priority;
		$data['PropertyAsset']['lot_size'] 		= $lot_size;
		$data['PropertyAsset']['lot_width'] 	= $lot_width;
		$data['PropertyAsset']['lot_length'] 	= $lot_length;
		$data['PropertyAsset']['lot_dimension'] = $lot_dimension;
		$data['PropertyAsset']['view_site_id'] 	= $property_view;
		
		$data['PropertyAsset']['lot_unit_id']	= $lot_unit;
		if($lot_unit == 1 && $property_action_id == 2){
			$data['PropertyAsset']['lot_unit_id']	= 2;
		}

		if(!empty($data['PropertySold'])){
			$data['PropertySold']['status'] = $property_sold_status;
			$data['PropertySold']['currency_id'] = $currency_id;
			$data['PropertySold']['property_action_id'] = $property_action_id;
			$data['PropertySold']['sold_by_name'] = $this->RmCommon->filterEmptyField($data, 'User', 'full_name');
			$data['PropertySold']['sold_by_id'] = $user_id;

			$sold_date = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'sold_date');
			
			if(!empty($sold_date)){
				$data['PropertySold']['sold_date'] = date('Y-m-d', strtotime($sold_date));
			}else{
				unset($data['PropertySold']['sold_date']);
			}

			if($property_action_id == 2 && !empty($data['PropertySold']['modified'])){
				$data['PropertySold']['end_date'] = date('Y-m-d', strtotime($data['PropertySold']['modified']));
			}
		}

		/*PropertyAsset*/
		$property_facilities		= $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'property_facilities');
		$property_facilities_others	= $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'property_facilities_others');
		$property_point_plus		= $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'property_point_plus');
		
		if(!empty($property_facilities) || !empty($property_facilities_others) || !empty($property_point_plus)){
			$facility_arr = array();

			if(!empty($property_facilities)){
				$property_facilities = explode(',', $property_facilities);

				foreach ($property_facilities as $key => $value) {
					$facility_arr['facility_id'][] = intval($value);
				}
			}

			if(!empty($property_facilities_others)){
				$facility_arr['other_id'] = 1;
				$facility_arr['other_text'] = $property_facilities_others;
			}
			
			if(!empty($facility_arr)){
				$data['PropertyFacility'] = $facility_arr;
			}

			if(!empty($property_point_plus)){
				$property_point_plus = unserialize($property_point_plus);

				if(!empty($property_point_plus)){
					$point_plus = array();
					foreach ($property_point_plus as $key => $value) {
						$point_plus['name'][] = $value;
					}

					$data['PropertyPointPlus'] = $point_plus;
				}
			}
		}
		/*END PropertyAsset*/

		/*PropertyMedias*/
		$property_medias = $this->RmCommon->filterEmptyField($data, 'PropertyMedias', 'data', array());
		
		$temp_property_medias = array();
		if(!empty($property_medias)){
			foreach ($property_medias as $key => $val_medias) {
				$data_property_medias = $this->RmCommon->filterEmptyField($val_medias, 'PropertyMedias');
				$type_media = $this->RmCommon->filterEmptyField($val_medias, 'PropertyMedias', 'type');
				$name = $this->RmCommon->filterEmptyField($val_medias, 'PropertyMedias', 'name');
				$main_photo = $this->RmCommon->filterEmptyField($val_medias, 'PropertyMedias', 'main_photo');

				unset($data_property_medias['id']);
				unset($data_property_medias['property_id']);

				$data_property_medias['primary'] = $main_photo;
				unset($data_property_medias['main_photo']);

				if($type_media == 2){
					$temp_property_medias['PropertyVideos'][$key]['PropertyVideos'] = $data_property_medias;
					$temp_property_medias['PropertyVideos'][$key]['PropertyVideos']['youtube_id'] = $name;
					$temp_property_medias['PropertyVideos'][$key]['PropertyVideos']['url'] = 'https://www.youtube.com/watch?v='.$name;
					$temp_property_medias['PropertyVideos'][$key]['PropertyVideos']['session_id'] = $session_id;
				}else{
					$temp_property_medias['PropertyMedias'][$key]['PropertyMedias'] = $data_property_medias;
					$temp_property_medias['PropertyMedias'][$key]['PropertyMedias']['session_id'] = $session_id;
				}
			}

			unset($data['PropertyMedias']);

			if(!empty($temp_property_medias['PropertyMedias'])){
				$data['PropertyMedias'] = $temp_property_medias['PropertyMedias'];
			}
			if(!empty($temp_property_medias['PropertyVideos'])){
				$data['PropertyVideos'] = $temp_property_medias['PropertyVideos'];
			}
		}
		/*END PropertyMedias*/

		/*PropertyAddress*/
		$property_address 	= $this->RmCommon->filterEmptyField($data, 'PropertyAddress');
		$address		  	= $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'address');
		$address2		  	= $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'address2');
		$subarea_name		= $this->RmCommon->filterEmptyField($property_address, 'Subarea', 'name', '');
		$city_name			= $this->RmCommon->filterEmptyField($property_address, 'City', 'name', '');
		$region_name		= $this->RmCommon->filterEmptyField($property_address, 'Region', 'name', '');
		$zip				= $this->RmCommon->filterEmptyField($property_address, 'Subarea', 'zip', '');

		$data['PropertyAddress']['address'] = '';
		if(!empty($address)){
			$data['PropertyAddress']['address'] = $address;
		}

		if(!empty($address2)){
			$data['PropertyAddress']['address'] .= ', '.$address2;
		}

		$data['Property']['keyword'] = sprintf('%s %s %s di %s, %s, %s %s, %s', $title, $property_type_name, $property_action_name, $subarea_name, $city_name, $region_name, $zip, $address );
		/*END PropertyAddress*/

		/*PropertyPrice*/
		$price = 0;
		$data_property_price = $this->RmCommon->filterEmptyField($data, 'PropertyPrice');
		if(!empty($data_property_price)){
			$temp_price = array();
			$temp_period = 0;
			foreach ($data_property_price as $key => $value) {
				$currency_id 	= $this->RmCommon->filterEmptyField($value, 'PropertyPrice', 'currency_id');
				$period			= $this->RmCommon->filterEmptyField($value, 'PropertyPrice', 'period');
				$price			= $this->RmCommon->filterEmptyField($value, 'PropertyPrice', 'price');
				
				switch ($period) {
					case 'day':
						$period = 1;
					break;
					case 'week':
						$period = 2;
					break;
					case 'month':
						$period = 3;
					break;
					case 'year':
						$period = 4;
					break;
				}

				$temp_price['PropertyPrice']['currency_id'][$key] 	= $currency_id;
				$temp_price['PropertyPrice']['price'][$key] 		= $price;
				$temp_price['PropertyPrice']['period_id'][$key]		= $period;

				if($period > $temp_period && $property_action_id == 2){
					$temp_period = $period;
					$data['Property']['price'] 			= $price;
					$data['Property']['currency_id'] 	= $currency_id;
					$data['Property']['period_id'] 		= $period;
				}
			}

			$data['PropertyPrice'] = $temp_price['PropertyPrice'];
		}
		/*END PropertyPrice*/

		$arr_model_unset = array(
			'User',
			'PropertyType',
			'PropertyAction',
			'Certificate',
			'Currency'
		);

		foreach ($arr_model_unset as $key => $model) {
			if(!empty($data[$model])){
				unset($data[$model]);
			}
		}

		return $data;
	}

	function setConvertSetToNewCompany($data){
        $PropertyFacility 		= $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'facility_id');
        $PropertyFacilityOther 	= $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'other_text');
        $PropertyPointPlus 		= $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'name');
        $PropertyPrice 			= $this->RmCommon->filterEmptyField($data, 'PropertyPrice');
        $lot_width 				= $this->RmCommon->filterEmptyField($data, 'Property', 'lot_width');
        $lot_length 			= $this->RmCommon->filterEmptyField($data, 'Property', 'lot_length');
        $lot_size 				= $this->RmCommon->filterEmptyField($data, 'Property', 'lot_size');
        $lot_unit 				= $this->RmCommon->filterEmptyField($data, 'Property', 'lot_unit');
        $photo 					= $this->RmCommon->filterEmptyField($data, 'Property', 'photo');

        if(!empty($lot_width)){
            $data['PropertyAsset']['lot_width'] = $lot_width;

            unset($data['Property']['lot_width']);
        }

        if(!empty($lot_length)){
            $data['PropertyAsset']['lot_length'] = $lot_length;

            unset($data['Property']['lot_length']);
        }

        if(!empty($lot_size)){
            $data['PropertyAsset']['lot_size'] = $lot_size;

            unset($data['Property']['lot_size']);
        }

        if(!empty($lot_unit)){
            $data['PropertyAsset']['lot_unit_id'] = $lot_unit;

            unset($data['Property']['lot_unit']);
        }

        if(empty($photo) && !empty($data['PropertyMedias'])){
            foreach ($data['PropertyMedias'] as $key => $value) {
                if(!empty($value['PropertyMedias']['primary']) && !empty($value['PropertyMedias']['name'])){
                    $photo = $value['PropertyMedias']['name'];
                }
            }

            $data['Property']['photo'] = $photo;
        }

        if(!empty($PropertyPointPlus)){
            $temp_arr = array();
            foreach ($PropertyPointPlus as $key => $value) {
                if(!empty($value)){
                    $temp_arr[$key] = $value;
                }
            }

            $data['PropertyAsset']['property_point_plus'] = $temp_arr;

            unset($data['PropertyPointPlus']);
        }

        if(!empty($PropertyFacility)){
            $temp_arr = array();
            foreach ($PropertyFacility as $key => $value) {
                if(!empty($value)){
                    $temp_arr[$value] = $value;
                }
            }

            if(!empty($PropertyFacilityOther)){
                $data['PropertyAsset']['others'] = 1;
                $data['PropertyAsset']['property_facilities_others'] = $PropertyFacilityOther;
            }else{
                $data['PropertyAsset']['others'] = 0;
            }

            $data['PropertyAsset']['property_facilities'] = $temp_arr;

            unset($data['PropertyFacility']);
        }

        if(!empty($PropertyPrice['currency_id'])){
            $day = array(
                'day' => '1',
                'week' => '2',
                'month' => '3',
                'year' => '4',
            );

            $temp_arr = array();

            $temp_arr['PropertyPeriod']['currency_id'] = $PropertyPrice['currency_id'];
            $temp_arr['PropertyPeriod']['price'] = $PropertyPrice['price'];

            foreach ($PropertyPrice['period_id'] as $key => $value) {
                if(!empty($PropertyPrice['price'][$key])){
                    $temp_arr['PropertyPeriod']['period_price'][$key] = $temp_arr['PropertyPeriod']['period'][$key] = $day[$PropertyPrice['period_id'][$key]];
                }
            }

            $data['PropertyPeriod'] = $temp_arr['PropertyPeriod'];

            unset($data['PropertyPrice']);
        }

        return $data;
    }

    function shapingArrayRevision($data, $shaping_from_model_revision = true){
        $arr = array();
        $temp_model = '';

        if($shaping_from_model_revision){
            foreach ($data as $key => $value) {
                $value = $value['PropertyRevision'];
                
                if($temp_model != $value['model']){
                    $temp_model = $value['model'];
                }

                $arr[$temp_model][$value['field']] = $value['value'];
            }
        }else{
            $property_id = '';
            if(!empty($data['property_id'])){
                $property_id = $data['property_id'];
                unset($data['property_id']);
            }

            $step = '';
            if(!empty($data['step'])){
                $step = $data['step'];
                unset($data['step']);
            }

            foreach ($data as $model => $value_data) {
                foreach ($value_data as $field => $value) {
                    $arr[] = array(
                        'property_id' => $property_id,
                        'model' => $model,
                        'field' => $field,
                        'value' => $value,
                        'step' => $step
                    );
                }
            }
        }

        return $arr;
    }

    function setConvertEbrosurToV2($data, $user_id = false, $property_id = false){
    	$UserCompanyEbrochure = $this->RmCommon->filterEmptyField($data, 'PropertyClaimBanner');

    	if(!empty($UserCompanyEbrochure)){
    		$code 			= $this->RmCommon->filterEmptyField($data, 'PropertyClaimBanner', 'banner_number');
    		$property_photo = $this->RmCommon->filterEmptyField($data, 'PropertyClaimBanner', 'property_photo');
    		$filename 		= $this->RmCommon->filterEmptyField($data, 'PropertyClaimBanner', 'filename');
    		$action_banner 	= $this->RmCommon->filterEmptyField($data, 'PropertyClaimBanner', 'action_banner');
    		$property_action_id = $this->RmCommon->filterEmptyField($data, 'PropertyClaimBanner', 'property_action_id');

    		$data['UserCompanyEbrochure'] = $UserCompanyEbrochure;

    		unset($data['PropertyClaimBanner']);
    		
    		$data['UserCompanyEbrochure']['user_id'] 		= $user_id;
    		$data['UserCompanyEbrochure']['property_id'] 	= $property_id;
    		$data['UserCompanyEbrochure']['code'] 			= $code;
    		$data['UserCompanyEbrochure']['filename'] 		= $property_photo;

    		if(!empty($filename)){
    			$data['UserCompanyEbrochure']['ebrosur_photo'] = $filename.'.jpg';
    		}

    		if(empty($property_action_id) && !empty($action_banner)){
    			$data['UserCompanyEbrochure']['property_action_id'] = ($action_banner == 'dijual') ? 1 : 2;
    		}

    		if(!empty($data['UserCompanyEbrochure']['id'])){
				unset($data['UserCompanyEbrochure']['id']);
			}
    	}

    	return $data;
    }

    function setConvertCareerToV2($data, $user_id){
    	if(!empty($data['CompanyCareer'])){
    		$data['Career'] = $data['CompanyCareer'];

    		unset($data['CompanyCareer']);

    		$data['Career']['user_id'] = $user_id;

    		unset($data['Career']['id']);

    		$data['CareerRequirement'] = array();
    		if(!empty($data['CompanyCareerRequirement'])){
    			foreach ($data['CompanyCareerRequirement'] as $key => $value) {
    				$name = $this->RmCommon->filterEmptyField($value, 'name', false, '');
    				
    				if(!empty($name)){
    					$data['CareerRequirement']['name'][$key] = $name;
    				}
    			}
    			unset($data['CompanyCareerRequirement']);
    		}
    	}
    	
    	return $data;
    }

    function setConvertDeveloperToV2($data, $parent_id, $action = ''){
    	if(!empty($data)){
    		$model = 'BannerSlide';
	    	if($action == 'developer'){
	    		$model = 'BannerDeveloper';
	    	}

	    	$data[$model] = $data['BannerWebPrinciple'];

	    	$data[$model]['user_id'] = $parent_id;

	    	$data[$model]['short_description'] = $this->RmCommon->filterEmptyField($data, $model, 'description', '');

	    	if(isset($data[$model]['id'])){
	    		unset($data[$model]['id']);
	    	}

	    	unset($data['BannerWebPrinciple']);
    	}

    	return $data;
    }

    function setConvertFaqToV2($data, $parent_id){
    	if(!empty($data['FaqCompany'])){
    		$data['Faq'] = $data['FaqCompany'];
    		unset($data['FaqCompany']);
    		unset($data['Faq']['id']);
    		unset($data['Faq']['faq_category_id']);

    		$data['Faq']['user_id'] = $parent_id;
    	}

    	if(!empty($data['FaqCategoryCompany'])){
    		$data['FaqCategory'] = $data['FaqCategoryCompany'];
    		unset($data['FaqCategoryCompany']);
    		unset($data['FaqCategory']['id']);

    		$data['FaqCategory']['user_id'] = $parent_id;
    	}
    	
    	return $data;
    }

    function callConfigModel($type){
    	$result = array();

    	switch ($type) {
    		case 'agent':
    			$result = 'MigrateAgentCompany';
    			break;
    	}

    	return $result;
    }

    function __callBeforeView($user_id = false){
    	$this->User = $this->controller->User;
    	$this->MigrateCompany = $this->controller->MigrateCompany;

		$this->User->virtualFields['company_name'] = 'TRIM(CONCAT(UserCompany.name, \' | \', User.first_name, \' \', IFNULL(User.last_name, \'\')))';
		
		$list_principle = $this->User->getData('list', array(
			'conditions' => array(
				'User.group_id' => 3,
				'UserCompanyConfig.id <>' => null,
				'User.company_name <>' => null
			),
			'fields' => array(
				'User.id', 'User.company_name'
			),
			'contain' => array(
				'UserCompany',
				'UserCompanyConfig'
			),
			'order' => array(
				'UserCompany.name' => 'ASC'
			)
		), array(
			'status' => 'all'
		));

		$data_migrates = $this->__getConfigMigrate();
		
		$this->controller->set(compact('list_principle', 'data_migrates'));
    }

    function __getConfigMigrate(){
    	return array(
			'is_agent' => array(
				'text' => __('Agen'),
				'checkbox' => false,
				'slug' => 'agents',
				'order' => 1
			), 
			'is_property' => array(
				'text' => __('Properti'),
				'checkbox' => false,
				'slug' => 'properties',
				'order' => 2
			), 
			// 'is_ebrosur' => array(
			// 	'text' => __('Ebrosur'),
			// 	'checkbox' => true,
			// 	'slug' => 'ebrosur',
			// 	'order' => 3
			// ), 
			// 'is_berita' => array(
			// 	'text' => __('Berita'),
			// 	'checkbox' => true,
			// 	'slug' => 'berita',
			// 	'order' => 4
			// ), 
			// 'is_career' => array(
			// 	'text' => __('Karir'),
			// 	'checkbox' => true,
			// 	'slug' => 'karir',
			// 	'order' => 5
			// ), 
			// 'is_banner_developer' => array(
			// 	'text' => __('Banner Developer'),
			// 	'checkbox' => true,
			// 	'slug' => 'banner_developer',
			// 	'order' => 6
			// ), 
			// 'is_banner_home' => array(
			// 	'text' => __('Banner Home'),
			// 	'checkbox' => true,
			// 	'slug' => 'banner_home',
			// 	'order' => 7
			// ), 
			// 'is_faq' => array(
			// 	'text' => __('FAQ'),
			// 	'checkbox' => true,
			// 	'slug' => 'faqs',
			// 	'order' => 8
			// ), 
			// 'is_partnership' => array(
			// 	'text' => __('Partnership'),
			// 	'checkbox' => true,
			// 	'slug' => 'partnerships',
			// 	'order' => 9
			// ), 
			'is_message' => array(
				'text' => __('Pesan'),
				'checkbox' => true,
				'slug' => 'messages',
				'order' => 10
			), 
		);
    }
}
?>