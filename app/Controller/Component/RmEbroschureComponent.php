<?php
class RmEbroschureComponent extends Component {
	var $components = array('RmImage', 'RmCommon', 'RmProperty'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function convertSetPropertyToEbrosur($data_company, $property){		
		$data['UserCompanyEbrochure']['city_id'] = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'city_id', null);
		$data['UserCompanyEbrochure']['subarea_id'] = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'subarea_id', null);
		$data['UserCompanyEbrochure']['region_id'] = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'region_id', null);
		$data['UserCompanyEbrochure']['property_media_id'] = $this->RmCommon->filterEmptyField($property, 'PropertyMedias', 'id', null);
		$data['UserCompanyEbrochure']['name'] = $this->RmCommon->filterEmptyField($property, 'User', 'full_name');
		$data['UserCompanyEbrochure']['phone'] = $this->RmCommon->filterEmptyField($property, 'UserProfile', 'no_hp');
		$data['UserCompanyEbrochure']['property_title'] = $this->RmCommon->filterEmptyField($property, 'Property', 'title');
		$data['UserCompanyEbrochure']['property_action_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'property_action_id', null);
		$data['UserCompanyEbrochure']['property_type_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'property_type_id', null);
		$data['UserCompanyEbrochure']['property_price'] = $this->RmCommon->filterEmptyField($property, 'Property', 'price');
		$data['UserCompanyEbrochure']['property_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'id', null);
		$data['UserCompanyEbrochure']['mls_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'mls_id');
		
		$description = $this->RmCommon->filterEmptyField($property, 'Property', 'description', '', array(
			'urldecode' => false
		));
		$additional_description = $this->RmProperty->getSpesification($property, array(
			'to_string' => true
		));
		$data['UserCompanyEbrochure']['description'] = $this->getDescrioptionEbrosur($data_company, $description, $additional_description);

		$data['UserCompanyEbrochure']['user_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id', null);
		$data['UserCompanyEbrochure']['currency_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'currency_id', null);
		$data['UserCompanyEbrochure']['period_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'period_id', null);
		$data['UserCompanyEbrochure']['lot_unit_id'] = $this->RmCommon->filterEmptyField($property, 'PropertyAsset', 'lot_unit_id', null);
		$data['UserCompanyEbrochure']['background_color'] = 5;
	
		return $data;
	}
	
	function _callBeforeSave($data, $config, $dataCompany, $data_user, $regenerate = false, $value = array(), $redirect = true){
		$params = $this->controller->params->params;

		$validate = true;
		$isAdmin = $this->RmCommon->_isAdmin();

		if(!empty($data)){
			$this->Property = ClassRegistry::init('Property');

			$id 				= Common::hashEmptyField($value, 'UserCompanyEbrochure.id');

			$city_id 			= Common::hashEmptyField($data, 'UserCompanyEbrochure.city_id');
			$subarea_id 		= Common::hashEmptyField($data, 'UserCompanyEbrochure.subarea_id');
			$filename 			= Common::hashEmptyField($data, 'UserCompanyEbrochure.filename_hide');
			$file_upload 		= Common::hashEmptyField($data, 'UserCompanyEbrochure.filename');
			$user_id_ebrosur 	= Common::hashEmptyField($data, 'UserCompanyEbrochure.user_id');
			$background_color 	= Common::hashEmptyField($data, 'UserCompanyEbrochure.background_color');
			$property_id 		= Common::hashEmptyField($data, 'UserCompanyEbrochure.property_id');
			$user_id 			= Common::hashEmptyField($data_user, 'id');

			if(empty($background_color) && (empty($config['UserCompanyConfig']['brochure_custom_sell']) || empty($config['UserCompanyConfig']['brochure_custom_rent']))){
				$data['UserCompanyEbrochure']['background_color'] = 5;
			}
			
			if(isset($data['UserCompanyEbrochure']['agent_email']) && empty($user_id_ebrosur)){
				$agent_email = Common::hashEmptyField($data, 'UserCompanyEbrochure.agent_email');

				$user = $this->controller->User->getData('first', array(
					'conditions' => array(
						'User.email' => $agent_email
					)
				), array(
					'role' => 'agent',
					'company' => true,
					'status' => 'semi-active',
					'admin' => true,
				));

				if(!empty($user['User']['id'])){
					$data_user = $user['User'];
					$user = $this->controller->User->UserProfile->getMerge($user, $user['User']['id']);

					$full_name = Common::hashEmptyField($user, 'User.full_name');
					$no_hp = Common::hashEmptyField($user, 'UserProfile.no_hp');

					$data['UserCompanyEbrochure']['user_id'] = $user['User']['id'];
					$data['UserCompanyEbrochure']['name'] = $full_name;
					$data['UserCompanyEbrochure']['phone'] = $no_hp;
				}
			}else{
				$data['UserCompanyEbrochure']['user_id'] = !empty($user_id_ebrosur) ? $user_id_ebrosur : $user_id;

				$user = $this->controller->User->getData('first', array(
					'conditions' => array(
						'User.id' => $data['UserCompanyEbrochure']['user_id']
					)
				), array(
					'role' => 'agent',
					'company' => true,
					'status' => 'semi-active',
					'admin' => true,
				));

				if(!empty($user['User']['id'])){
					$data_user = $user['User'];
					$user = $this->controller->User->UserProfile->getMerge($user, $user['User']['id']);

					$full_name = Common::hashEmptyField($user, 'User.full_name');
					$no_hp = Common::hashEmptyField($user, 'UserProfile.no_hp');

					$data['UserCompanyEbrochure']['name'] = $full_name;
					$data['UserCompanyEbrochure']['phone'] = $no_hp;
				}
			}

			if(empty($file_upload) && !empty($filename)){
				$filename = '';
			}
			else if(!empty($file_upload) && !is_array($file_upload) && empty($filename)){
				if(strpos(basename($file_upload), 'ebrochure-studio') !== false){
				//	regenerate ebrochure versi baru ke versi lama (paksa cari foto utama property)
					$filename = '';
				}
				else{
				//	versi lama
					$filename = $file_upload;
				}
			}

			$property_media_id = Common::hashEmptyField($data, 'UserCompanyEbrochure.property_media_id');
			$user_code = Common::hashEmptyField($data_user, 'code');

			$currency_id = Common::hashEmptyField($data, 'UserCompanyEbrochure.currency_id');
			
			$location = '';
			if(!empty($city_id) || !empty($subarea_id)){
				if(!empty($subarea_id)){
					$subarea = $this->controller->User->UserProfile->Subarea->getSubareaByID($subarea_id);
					$subarea = Common::hashEmptyField($subarea, 'Subarea.name', '');

					$location .= $subarea.', ';
				}

				if(!empty($city_id)){
					$city = $this->controller->User->UserProfile->City->getCity($city_id);
					$city = Common::hashEmptyField($city, 'City.name', '');

					$location .= $city;
				}
			}

			$data['UserCompanyEbrochure']['location'] = $location;

			$generate_code = $this->controller->User->UserCompanyEbrochure->generateCode($user_code);

			$data['UserCompanyEbrochure']['code'] = Common::hashEmptyField($data, 'UserCompanyEbrochure.code', $generate_code);
			
			if(isset($data['UserCompanyEbrochure']['created'])){
				unset($data['UserCompanyEbrochure']['created']);
			}
			
			if(!empty($data['UserCompanyEbrochure']['mls_id'])){
				$temp = explode(',', $data['UserCompanyEbrochure']['mls_id']);

				if(!empty($temp[0])){
					$property = $this->Property->getData('first', array(
						'conditions' => array(
							'Property.mls_id' => $temp[0]
						)
					), array(
						'status' => 'active-pending-sold',
						'skip_is_sales' => true,
					));

					if(!empty($property['Property']['id'])){
						$property_id = $property['Property']['id'];
						$data['UserCompanyEbrochure']['property_id'] = $property_id;
						$data['UserCompanyEbrochure']['mls_id'] = $property['Property']['mls_id'];
					}
				}
			}
			
			$property_photo = '';
			$path_photo_property = '';

			if(!empty($filename)){
				$path_photo_property = Configure::read('__Site.ebrosurs_photo');
				$property_photo = $filename; 
			} else if( !empty($property_id) ) {
				$options = array(
					'conditions' => array(
						'PropertyMedias.property_id' => $property_id,
					),
				);

				if(!empty($property_media_id)) {
					$options['conditions']['PropertyMedias.id'] = $property_media_id;
				}

				$path_photo_property = Configure::read('__Site.property_photo_folder');
				$property_media = $this->Property->PropertyMedias->getData('first', $options, array(
					'status' => 'all'
				));
				
				if(!empty($property_media['PropertyMedias']['name'])){
					$property_photo = $property_media['PropertyMedias']['name']; 
				}
			}

			$data['UserCompanyEbrochure']['_property_photo'] = $property_photo;
			$data['UserCompanyEbrochure']['path_photo_property'] = $path_photo_property;
			$data['UserCompanyEbrochure']['property_price'] = str_replace(',', '', trim($this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'property_price')));

			$data = $this->controller->User->UserCompanyEbrochure->Currency->getMerge($data, $currency_id, 'Currency.id', array(
				'cache' => array(
					'name' => __('Currency.%s', $currency_id),
				),
			));
			
			$this->controller->User->UserCompanyEbrochure->set($data);
			
			if( $this->controller->User->UserCompanyEbrochure->validates($data) ){
				if(!empty($config['UserCompanyConfig']['is_brochure']) || !empty($isAdmin)){
					$data['UserCompanyEbrochure']['ebrosur_photo'] = $this->make_ebrosur($data, $config, $dataCompany, $data_user, $regenerate);
				}
				
				if(!empty($data['UserCompanyEbrochure']['ebrosur_photo'])){
					$validate = false;
				}
			}

		//	buat bantu flag kirim email
			$isCreate = true;

			// Judul Ebrosur
			$property_title = Common::hashEmptyField($data, 'UserCompanyEbrochure.property_title');
			$data['UserCompanyEbrochure']['name'] = $property_title;

			if(!empty($id)){
				$data['UserCompanyEbrochure']['id'] = $id;

				$isCreate = false;
			}

			/*save data*/
			$result = $this->controller->User->UserCompanyEbrochure->doSave($data, false, $validate, $id);

			if(!empty($redirect)){
				$status = Common::hashEmptyField($result, 'status');
				$id 	= Common::hashEmptyField($result, 'id');

				if(!empty($id) && !empty($status) && $status == 'success'){
					$ebrosur_photo = Common::hashEmptyField($value, 'UserCompanyEbrochure.ebrosur_photo');
					$dimension = array_keys($this->RmImage->_rulesDimensionImage(Configure::read('__Site.ebrosurs_photo')));

					$this->RmCommon->deletePathPhoto(Configure::read('__Site.ebrosurs_photo'), $ebrosur_photo, $dimension);

					if($isCreate){
					//	open listing : send notification and email to property owner if logged in user not equal property owner
						$notifications	= $this->controller->User->UserCompanyEbrochure->prepareNotification($data, 'create');
						$result			= array_merge($result, $notifications);
					}
				}

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'ebrosurs',
					'action' => 'detail',
					$id,
					'admin' => true,
				));
			}else{
				return $result;
			}
		}else{
			$cobroke_id = Common::hashEmptyField($params, 'named.cobroke_id');

			if(!empty($value)){
				$city_id 			= Common::hashEmptyField($value, 'UserCompanyEbrochure.city_id');
				$subarea_id 		= Common::hashEmptyField($value, 'UserCompanyEbrochure.subarea_id');
				$filename 			= Common::hashEmptyField($value, 'UserCompanyEbrochure.filename');
				$property_media_id 	= Common::hashEmptyField($value, 'UserCompanyEbrochure.property_media_id');
				$property_id 		= Common::hashEmptyField($value, 'UserCompanyEbrochure.property_id');

				$agent_email 		= Common::hashEmptyField($value, 'User.email');

				$value['UserCompanyEbrochure']['agent_email'] = $agent_email;

				if(!empty($city_id) || !empty($subarea_id)){
					$location = '';

					if(!empty($subarea_id)){
						$subarea = $this->controller->User->UserProfile->Subarea->getSubareaByID($subarea_id);
						$subarea = Common::hashEmptyField($subarea, 'Subarea.name', '');

						if(!empty($subarea)){
							$location .= $subarea.', ';
						}
					}

					if(!empty($city_id)){
						$city = $this->controller->User->UserProfile->City->getCity($city_id);
						$city = Common::hashEmptyField($city, 'City.name', '');

						$location .= $city;
					}
					
					$value['UserCompanyEbrochure']['location'] = $location;
				}

				if(!empty($filename) || !empty($property_media_id)){
					$property_photo = '';
					$path_photo_property = '';

					if(strpos(basename($filename), 'ebrochure-studio') !== false){
					//	regenerate ebrochure versi baru ke versi lama (paksa cari foto utama property)
						$filename = '';
					}

					if(!empty($filename)){
						$path_photo_property = Configure::read('__Site.ebrosurs_photo');
						$value['UserCompanyEbrochure']['filename_hide'] = $property_photo = $filename; 
					} else if( !empty($property_id) ) {
						$path_photo_property = Configure::read('__Site.property_photo_folder');
						$options = array(
							'conditions' => array(
								'PropertyMedias.property_id' => $property_id
							)
						);

						if(!empty($property_media_id)) {
							$options['conditions']['PropertyMedias.id'] = $property_media_id;
						}

						$property_media = $this->controller->User->Property->PropertyMedias->getData('first', $options, array(
							'status' => 'all'
						));

						$property_photo = Common::hashEmptyField($property_media, 'PropertyMedias.name'); 
					}

					$value['UserCompanyEbrochure']['_property_photo'] = $property_photo;
					$value['UserCompanyEbrochure']['path_photo_property'] = $path_photo_property;
				}

				$this->controller->request->data = $value;
			}else{
				$this->propertyFromCoBroke($cobroke_id);
			}
		}
	}

	function make_ebrosur($data, $config, $dataCompany, $data_user, $regenerate = false){
		if(!empty($data['UserCompanyEbrochure']) && !empty($data['Currency'])){
			$data_symbol = $data['Currency'];
			$data = $data['UserCompanyEbrochure'];

			$name = $this->RmCommon->filterEmptyField($data, 'name');
			$phone = $this->RmCommon->filterEmptyField($data, 'phone');
			$description = $this->RmCommon->filterEmptyField($data, 'description', false, '', array(
				'urldecode' => false
			));
			$title = $this->RmCommon->filterEmptyField($data, 'property_title');
			$price = str_replace(',', '', $this->RmCommon->filterEmptyField($data, 'property_price'));
			$property_action_id = Common::hashEmptyField($data, 'property_action_id', null);
			$property_type_id = Common::hashEmptyField($data, 'property_type_id', null);
			$background_color = $this->RmCommon->filterEmptyField($data, 'background_color');
			$location = $this->RmCommon->filterEmptyField($data, 'location');
			$property_photo = $this->RmCommon->filterEmptyField($data, '_property_photo');
			$path_photo_property = $this->RmCommon->filterEmptyField($data, 'path_photo_property');
			$photo_profile = $this->RmCommon->filterEmptyField($data_user, 'photo');
			$code = $this->RmCommon->filterEmptyField($data, 'code');
			$created = $this->RmCommon->filterEmptyField($data, 'created');
			$period_id = Common::hashEmptyField($data, 'period_id', null);
			$lot_unit_id = Common::hashEmptyField($data, 'lot_unit_id', null);
			$note_price = $this->RmCommon->filterEmptyField($data, 'note_price');

			$delta_x_code = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'delta_x_code');
			$delta_y_code = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'delta_y_code');
			$delta_x_created = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'delta_x_created');
			$delta_y_created = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'delta_y_created');
			$with_mls_id = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'with_mls_id');
			$layout = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'type_custom_ebrochure');
			$brochure_content_color = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'brochure_content_color');
			$brochure_footer_color = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'brochure_footer_color');
			
			$delta_y_mlsid = '';
			$delta_x_mlsid = '';
			if(!empty($with_mls_id)){
				$delta_x_mlsid = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'delta_x_mlsid');
				$delta_y_mlsid = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'delta_y_mlsid');
			}

			$mls_id = $this->RmCommon->filterEmptyField($data, 'mls_id');

			$type_property = $this->controller->User->Property->PropertyType->getData('first', array(
				'conditions' => array(
					'PropertyType.id' => $property_type_id
				),
				'cache' => __('PropertyType.%s', $property_type_id)
			));

			$price = sprintf('%s %s,-', $data_symbol['symbol'], number_format($price));

			if(!empty($lot_unit_id)){
				$lotUnits = $this->controller->User->Property->PropertyAsset->LotUnit->getData('first', array(
					'conditions' => array(
						'LotUnit.id' => $lot_unit_id
					),
					'cache' => __('LotUnit.%s', $lot_unit_id),
				));

				$lot_name = $this->RmCommon->filterEmptyField($lotUnits, 'LotUnit', 'slug');

				if(!empty($lot_name)){
					$price .= ' / '.ucfirst($lot_name);
				}
			}

			if(!empty($period_id)){
				$period = $this->controller->User->Property->PropertyPrice->Period->getData('first', array(
					'conditions' => array(
						'Period.id' => $period_id
					),
					'cache' => __('Period.%s', $period_id),
				));

				$period_name = $this->RmCommon->filterEmptyField($period, 'Period', 'name');

				if(!empty($period_name)){
					$price .= ' '.$period_name;
				}
			}

			if(!empty($note_price)){
				$price .= ' '.$note_price;
			}

			$price = $this->RmCommon->truncate($price, 45, '');

			if(!empty($type_property['PropertyType']['name'])){
				$type_property = $type_property['PropertyType']['name'];
			}else{
				$type_property = '';
			}

			$background_class = '';
			if(!empty($background_color)){
				$color_banner_option = $this->RmCommon->getGlobalVariable('color_banner_option');
				
				if(!empty($color_banner_option[$background_color]['background_class'])){
					$background_class = $color_banner_option[$background_color]['background_class'];
				}
			}
			
			$logo_photo = '';

			if(!empty($dataCompany['UserCompany']['logo'])){
				$logo_photo = $dataCompany['UserCompany']['logo']; 
			}
			// else if(!empty($config['UserCompanyConfig']['logo_company'])){
			// 	$logo_photo = $config['UserCompanyConfig']['logo_company']; 
			// }

		//	DOUBLE WORD FIX =======================================================================================================

			if($title){
				$is_contain	= $type_property && strpos(strtolower($title), strtolower($type_property)) !== false;
				$title		= $is_contain ? $title : sprintf('%s %s', $type_property, $title);
			}

			if($title && $location){
				$location = explode(', ', $location);

				foreach($location as $locationKey => $locationName){
					if(strpos(strtolower($title), strtolower($locationName)) !== false){
						unset($location[$locationKey]);
					}
				}

				$location = implode(', ', array_filter($location));
			}

		//	=======================================================================================================================

			$ebrochure_options = array(
				'property_photo' => array(
					'url' => $property_photo,
					'path' => $path_photo_property
				),
				'logo_company' => array(
					'url' => $logo_photo,
					'path' => Configure::read('__Site.logo_photo_folder')
				),
				'photo_profile' => array(
					'url' => $photo_profile,
					'path' => Configure::read('__Site.profile_photo_folder')
				),
				'name' => $name,
				'phone' => $phone,
				'description' => $description,
				'price' => $price,
			//	'title' => sprintf('%s %s', $type_property, $title),
				'title' => $title,
				'location' => $location,
				'property_action_id' => $property_action_id,
				'background_color' => $background_color,
				'background_class' => $background_class,
				'background_sell' => $config['UserCompanyConfig']['brochure_custom_sell'],
				'background_rent' => $config['UserCompanyConfig']['brochure_custom_rent'],
				'code' => $code,
				'created' => $created,
				'delta_x_code' => $delta_x_code,
				'delta_y_code' => $delta_y_code,
				'delta_x_created' => $delta_x_created,
				'delta_y_created' => $delta_y_created,
				'delta_x_mlsid' => $delta_x_mlsid,
				'delta_y_mlsid' => $delta_y_mlsid,
				'mls_id' => $mls_id,
				'with_mls_id' => $with_mls_id,
				'layout' => $layout,
				'brochure_content_color' => $brochure_content_color,
				'brochure_footer_color' => $brochure_footer_color,
				'regenerate' => $regenerate
			);

			if(!empty($property_photo)){
				return $this->RmImage->create_ebrosur($ebrochure_options);
			}else{
				return '';
			}
		}
	}

	function _callGetAllSession ( $step ) {
        $sessionName = '__Site.EbrosurRequest.SessionName.%s';

        switch ($step) {
            case 'all':
                $dataBasic = $this->controller->Session->read(sprintf($sessionName, $this->controller->basicLabel));
                $dataSpecification = $this->controller->Session->read(sprintf($sessionName, $this->controller->spesificationLabel));
                $dataAgent = $this->controller->Session->read(sprintf($sessionName, $this->controller->agentLabel));

                $data = array();

                if( !empty($dataBasic) ) {
                    $data = array_merge($data, $dataBasic);
                }
                if( !empty($dataSpecification) ) {
                    $data = array_merge($data, $dataSpecification);
                }
                if( !empty($dataAgent) ) {
                    $data = array_merge($data, $dataAgent);
                }
                break;
            
            default:
                $data = $this->controller->Session->read(sprintf($sessionName, $step));
                break;
        }

        return $data;
    }

    function _callDeleteSession () {
        $sessionName = '__Site.EbrosurRequest.SessionName.%s';

        $this->controller->Session->delete(sprintf($sessionName, $this->controller->basicLabel));
        $this->controller->Session->delete(sprintf($sessionName, $this->controller->agentLabel));
        $this->controller->Session->delete(sprintf($sessionName, $this->controller->spesificationLabel));
    }

    function _callDataSession ( $step ) {
        $data = $this->_callGetAllSession($step);

        if( is_array($data) ) {
            if( !empty($data) && is_array($data) ) {
                foreach ($data as $key => $value) {
                    if( is_array($data[$key]) ) {
                        $data[$key] = array_filter($value, function($var) {
                            return ($var != '');
                        });
                    }
                }
            }
            
            $data = array_filter($data, function($var) {
                return ($var != '');
            });
        }

        return $data;
    }

    function _callBeforeSaveSpesification($data){
    	$model = 'EbrosurRequest';

    	$data = $this->_setFieldMinMax($data, $model, 'price');
    	$data = $this->_setFieldMinMax($data, $model, 'lot_size');
    	$data = $this->_setFieldMinMax($data, $model, 'building_size');

    	return $data;
    }

    function getDescrioptionEbrosur($dataCompany, $description, $additional_description, $total_karakter = 200){
    	$is_description_ebrochure 	= $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'is_description_ebrochure');
		$is_specification_ebrochure = $this->RmCommon->filterEmptyField($dataCompany, 'UserCompanyConfig', 'is_specification_ebrochure');

    	if(!empty($is_description_ebrochure) || !empty($is_specification_ebrochure)){
			if(!empty($is_description_ebrochure) && !empty($is_specification_ebrochure)){
				$count_additional = strlen($additional_description);

				$total_karakter -= $count_additional;

				$description = $this->RmCommon->truncate($description, $total_karakter);

				$description = $additional_description.' '.$description;
			}else if(!empty($is_specification_ebrochure)){
				$description = $additional_description;
			}else{
				$description = $this->RmCommon->truncate($description, 200);
			}
		}else if(!empty($additional_description)){
			$description = $additional_description;
		}else{
			$description = $this->RmCommon->truncate($description, 200);
		}

		return $description;
    }

    function saveApiDataMigrate($data){
    	$this->User = ClassRegistry::init('User');

		$user_id 		= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'user_id');
		$code 			= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'code');
		$property_id 	= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'property_id');
		$ebrosur_photo 	= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'ebrosur_photo');
		$property_photo	= $this->RmCommon->filterEmptyField($data, 'UserCompanyEbrochure', 'property_photo');
		$id 			= false;
		
		if(!empty($data) && !empty($user_id)){
			unset($data['UserCompanyEbrochure']['id']);

			if(!empty($code)){
				$ebrosur = $this->User->UserCompanyEbrochure->getData('first', array(
					'conditions' => array(
						'UserCompanyEbrochure.code' => $code
					)
				), array(
					'status' => 'all',
					'mine' => false,
					'company' => false
				));
				
				$data['UserCompanyEbrochure']['id'] = $id = $this->RmCommon->filterEmptyField($ebrosur, 'UserCompanyEbrochure', 'id');
			}

			if(!empty($property_id)){
				$property = $this->User->Property->getData('first', array(
					'conditions' => array(
						'Property.id' => $property_id
					)
				), array(
					'company' => false,
					'status' => 'all'
				));

				$data['UserCompanyEbrochure']['mls_id'] = $this->RmCommon->filterEmptyField($property, 'Property', 'mls_id');
			}
			
			$image_name = $this->RmImage->copy_image_to_uploads($ebrosur_photo);
			
			if(!empty($ebrosur_photo) && !empty($image_name)){
				$data['UserCompanyEbrochure']['ebrosur_photo'] = $image_name;
			}

			if(!empty($property_photo)){
				$image_name_property = $this->RmImage->copy_image_to_uploads($property_photo, Configure::read('__Site.property_photo_folder'), Configure::read('__Site.ebrosurs_photo'), 'filename');
			
				if(!empty($image_name_property)){
					$data['UserCompanyEbrochure']['filename'] = $image_name_property;
				}
			}

			$result = $this->User->UserCompanyEbrochure->doSave($data, false, false, $id, true);
			$this->RmCommon->setProcessParams($result, false, array(
				'noRedirect' => true
			));
		}
	}

	function _callBeforeViewEbrosurs ( $options = array(), $elements = array( 'mine' => true ) ) {
		$options =  $this->controller->UserCompanyEbrochure->_callRefineParams($this->controller->params, array_merge_recursive(array(
			'order' => array(
				'UserCompanyEbrochure.id' => 'DESC',
				'UserCompanyEbrochure.name' => 'ASC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		), $options));

		$this->RmCommon->_callRefineParams($this->controller->params);
		$this->controller->paginate	= $this->controller->UserCompanyEbrochure->getData('paginate', $options, $elements);

		$ebrosurs = $this->controller->paginate('UserCompanyEbrochure');
		$ebrosurs = $this->controller->UserCompanyEbrochure->getMergeList($ebrosurs);

		if($this->RmCommon->Rest->isActive() && !empty($ebrosurs)){
			foreach ($ebrosurs as $key => $value) {
				$user_id = $this->RmCommon->filterEmptyField($value, 'UserCompanyEbrochure', 'user_id');

				$ebrosurs[$key] = $this->controller->User->getMerge($value, $user_id);
			}
		}

		$this->RmCommon->_callDataForAPI($ebrosurs);

		return $ebrosurs;
	}

    function _callRoleCondition ( $value ) {
        $id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
        $group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');
        $options = array();

        switch ($group_id) {
            case '4':
				$principle_id = $this->controller->User->getAgents($id, true, 'list', false, array(
					'role' => 'principle',
				));
				
				if(!empty($principle_id)){
	                $options = array(
						'conditions' => array(
							'User.parent_id' => $principle_id,
						),
						'contain' => array(
							'User',
						),
					);
				}
                break;
            case '3':
                $options = array(
                    'conditions' => array(
                        'OR' => array(
                        	'User.parent_id' => $id,
                        	'UserCompanyEbrochure.user_id' => $id,
                    	),
                    ),
                    'contain' => array(
                        'User',
                    ),
                );
                break;
            case '2':
                $options = array(
                    'conditions' => array(
                        'UserCompanyEbrochure.user_id' => $id,
                    ),
                );
                break;
        }

        return $options;
    }

    function _callBeforSaveRequestAPI($data){
    	if(!empty($data['EbrosurClientRequest']['client_id'])){
    		$data_client_id = $data['EbrosurClientRequest']['client_id'];
    		$temp = array();

    		foreach ($data_client_id as $key => $value) {
    			$temp[$value] = 1;
    		}

    		$data['EbrosurClientRequest']['client_id'] = $temp;
    	}

    	if(!empty($data['EbrosurTypeRequest']['property_type_id'])){
    		$data_client_id = $data['EbrosurTypeRequest']['property_type_id'];
    		$temp = array();

    		foreach ($data_client_id as $key => $value) {
    			$temp[$value] = true;
    		}

    		$data['EbrosurTypeRequest']['property_type_id'] = $temp;
    	}

    	return $data;
    }

    function callAgentClient($agent_id = false){
		$prefix = Configure::read('App.prefix');

		if($prefix == 'admin'){
			$field = 'agent_id';
			$foreign_key = 'user_id';
		}else{
			$field = 'user_id';
			$foreign_key = 'agent_id';
		}

		$user_id = $this->controller->user_id;
		
		if(Configure::read('User.group_id') != 2){
			$user_id = $agent_id;
		}

		$default_contain = array(
			'User' => array(
				'className' => 'User',
				'foreignKey' => $foreign_key,
			),
		);
		$default_order = array(
			'User.full_name' => 'ASC'
		);
		$default_condition = array(
			'UserClientRelation.'.$field => $user_id,
		);

		if(Configure::write('User.admin') && $prefix == 'admin'){
			$default_contain = array_merge($default_contain, array('UserClient'));
			$default_order = array(
				'UserClient.full_name' => 'ASC'
			);
			$default_condition['UserClient.company_id'] = Configure::read('Principle.id');
		}

		$options = $this->controller->UserClientRelation->_callRefineParams($this->controller->params, array(
			'conditions' => $default_condition,
			'contain' => $default_contain,
			'order' => $default_order,
			'group' => array(
				'UserClientRelation.'.$foreign_key
			),
			'limit' => 21
		));

		$this->controller->paginate = $this->controller->UserClientRelation->getData('paginate', $options);
		$agents = $this->controller->paginate('UserClientRelation');
		
		foreach( $agents as $key => $value ) {
			$user_id = $this->RmCommon->filterEmptyField($value, 'UserClientRelation', $foreign_key);
			$value = $this->controller->User->UserProfile->getMerge( $value, $user_id );

			if( !empty($agents) && $prefix == 'client' ) {
				$value['User']['count_property'] = $this->controller->User->Property->getData('count', array(
					'conditions' => array(
						'Property.user_id' => $user_id
					)
				), array(
					'status' => 'active-pending',
					'skip_is_sales' => true,
				));
			}else{
				$client_type_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'client_type_id');

				$value = $this->controller->User->ClientType->getMerge($value, $client_type_id);
			}

			$agents[$key] = $value;
		}

		$this->RmCommon->_callDataForAPI($agents, 'manual');
		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
    }

    function _setFieldMinMax($data, $model, $field){
    	$temp_field = $field;

    	$data_field = $this->RmCommon->filterEmptyField($data, $model, $field);

    	if( !empty($data_field) ) {
            $firstString = substr($data_field, 0, 1);

            if( in_array($firstString, array( '>', '<' )) ) {
                $data_field = substr($data_field, 1);

                $field_tobe = 'min_'.$field;
                if($firstString == '<'){
                	$field_tobe = 'max_'.$field;
                }

                $data[$model][$field_tobe] = $data_field;
            } else {
                $data_field = explode('-', $data_field);
                $min = !empty($data_field[0])?$data_field[0]:false;
                $max = !empty($data_field[1])?$data_field[1]:false;

                if( !empty($min) ) {
                    $data[$model]['min_'.$field] = $min;
                }
                if( !empty($max) ) {
                    $data[$model]['max_'.$field] = $max;
                }
            }

            unset($data[$model][$temp_field]);
        }

    	return $data;
    }

    function propertyFromCoBroke($cobroke_id){
    	if(!empty($cobroke_id)){
			$this->CoBrokeUser = ClassRegistry::init('CoBrokeUser');

			$coBroke_data = $this->getDataCoBrokeUser($cobroke_id);

			$property_id = Common::hashEmptyField($coBroke_data, 'Property.id');

			if(!empty($property_id)){
				$property_medias = $this->CoBrokeUser->CoBrokeProperty->Property->PropertyMedias->getData('all', array(
					'conditions' => array(
						'PropertyMedias.property_id' => $property_id
					)
				), array(
					'status' => 'all'
				));

				$coBroke_data = $this->CoBrokeUser->CoBrokeProperty->Property->getDataList($coBroke_data, array(
					'contain' => array(
						'MergeDefault',
						'PropertyAddress',
						'PropertyAsset',
						'PropertySold',
					),
				));

				$this->setDataForm($coBroke_data);

				$this->controller->set('property_medias', $property_medias);
			}else{
				$this->RmCommon->redirectReferer(__('Co Broke tidak ditemukan.'));
			}
		}
    }

    function setDataForm($property){
    //	$requestRef =& $this->controller->request->data;

    	$property_id		= Common::hashEmptyField($property, 'Property.id', null);
    	$mls_id 			= Common::hashEmptyField($property, 'Property.mls_id');
    	$property_action_id = Common::hashEmptyField($property, 'Property.property_action_id', null);
		$property_type_id 	= Common::hashEmptyField($property, 'Property.property_type_id', null);
		$title 				= Common::hashEmptyField($property, 'Property.title');
		$region_id 			= Common::hashEmptyField($property, 'PropertyAddress.region_id', null);
		$city_id 			= Common::hashEmptyField($property, 'PropertyAddress.city_id', null);
		$subarea_id 		= Common::hashEmptyField($property, 'PropertyAddress.subarea_id', null);
		$region_name		= Common::hashEmptyField($property, 'PropertyAddress.Region.name');
		$city_name	 		= Common::hashEmptyField($property, 'PropertyAddress.City.name');
		$subarea_name 		= Common::hashEmptyField($property, 'PropertyAddress.Subarea.name');
		$currency_id 		= Common::hashEmptyField($property, 'Property.currency_id', null);
		$plain_desc 		= $description = Common::hashEmptyField($property, 'Property.description', '');
		$email 				= Common::hashEmptyField($property, 'User.email', '');
		$price 				= Common::hashEmptyField($property, 'Property.price');

		$location_name = array_filter(array($subarea_name, $city_name, $region_name));
		$location_name = implode(', ', $location_name);

		/*Rule price properti*/
		App::import('Helper', 'Property'); 
		$Property = new PropertyHelper(new View(null));
		$data_component_price = $Property->getPrice($property, false, true);

		$data_price = Common::hashEmptyField($data_component_price, 'price', false);
		if(!empty($data_price)){
			$price = $data_price;
		}

		$period_id 		= Common::hashEmptyField($data_component_price, 'period_id', null);
		$lot_unit_id 	= Common::hashEmptyField($data_component_price, 'lot_unit_id', null);
		/*end Rule price properti*/

		$plain_spec = $additional_description = $this->RmProperty->getSpesification($property, array(
			'to_string' => true
		));

		$location = array_filter(array($subarea_name, $city_name));
		$location = implode(', ', $location);

		$description = $this->getDescrioptionEbrosur(Configure::read('Config.Company.data'), $description, $additional_description);

		$requestRef['UserCompanyEbrochure'] = array(
			'mls_id' 					=> $mls_id,
			'property_action_id' 		=> $property_action_id,
			'property_type_id' 			=> $property_type_id,
			'property_title' 			=> $title,
			'property_price' 			=> $price,
			'region_id' 				=> $region_id,
			'city_id' 					=> $city_id,
			'subarea_id' 				=> $subarea_id,
			'description' 				=> $description,
			'background_color' 			=> !empty($color) ? $color : '',
			'currency_id' 				=> $currency_id,
			'period_id' 				=> $period_id,
			'lot_unit_id' 				=> $lot_unit_id,
			'agent_email' 				=> $email,
			'description_property'		=> $plain_desc,
			'specification_property'	=> $plain_spec,
			'location' 					=> $location,
			'location_name'				=> $location_name, 
			'property_id' 				=> $property_id,
		);

		foreach(array('Region', 'City', 'Subarea') as $locationModel){
			$modelPath = sprintf('PropertyAddress.%s', $locationModel);

			if(Hash::check($property, $modelPath)){
				$requestRef[$locationModel] = Common::hashEmptyField($property, $modelPath, array());
			}
		}

		$this->controller->request->data = $requestRef;

		$this->RmCommon->_callRequestSubarea('UserCompanyEbrochure');
		$this->controller->set('data', $requestRef);
    }

    function getDataCoBrokeUser($cobroke_id){
    	$user_login_id 	= Configure::read('User.id');
		$group_id 		= Configure::read('User.group_id');
		$company_id 	= Configure::read('Config.Company.data.UserCompanyConfig.user_id');

    	$this->CoBrokeUser = ClassRegistry::init('CoBrokeUser');

		$cobroke_conditions = array(
			'CoBrokeUser.id' => $cobroke_id,
			'CoBrokeUser.approved' => 1,
			'User.parent_id' => $company_id
		);

		if($group_id == 2){
			$cobroke_conditions['CoBrokeUser.user_id'] = $user_login_id;
		}

		$coBroke_data = $this->CoBrokeUser->find('first', array(
			'conditions' => $cobroke_conditions,
			'contain' => array(
				'User'
			)
		));

		$coBroke_data = $this->CoBrokeUser->getMergeList($coBroke_data, array(
			'contain' => array(
				'CoBrokeProperty'
			)
		));

		return $coBroke_data;
    }

	public function callBeforeSaveBuilder($ebrochure = array(), $options = array()){
		$ebrochure	= (array) $ebrochure;
		$options	= (array) $options;
		$return		= Common::hashEmptyField($options, 'return');

		$data		= $this->controller->request->data;
		$result		= array(
			'data' => $ebrochure, 
		);

		$ebrochureID = Common::hashEmptyField($ebrochure, 'UserCompanyEbrochure.id', 0);

		$isAjax		= $this->controller->RequestHandler->isAjax();
		$redirect	= false;
		$options	= array();

		if($isAjax){
			$options = array('noRedirect' => true);
		}

		if($data){
		//	save template juga lewat sini, beda save tablenya aja
			$params		= $this->controller->params;
			$isTemplate	= Common::hashEmptyField($params->query, 'template');

			if($isTemplate){
				$savePath	= Configure::read('__Site.ebrosurs_template');
				$modelName	= 'EbrochureTemplate';
				$fieldName	= 'thumbnail';
			}
			else{
				$savePath	= Configure::read('__Site.ebrosurs_photo');
				$modelName	= 'UserCompanyEbrochure';
				$fieldName	= 'ebrosur_photo';
			}

		//	post data template / ebrochure
			$recordID		= Common::hashEmptyField($data, sprintf('%s.id', $modelName), '');
			$name			= Common::hashEmptyField($data, sprintf('%s.name', $modelName), '');
			$description	= Common::hashEmptyField($data, sprintf('%s.description', $modelName), '');
			$layout			= Common::hashEmptyField($data, sprintf('%s.layout', $modelName), '', array('urldecode' => false));
			$filename		= Common::hashEmptyField($data, sprintf('%s.filename', $modelName), '', array('urldecode' => false));

		//	global post data
			$authUserID		= Common::config('User.id', 0);
			$principleID	= Common::config('Principle.id', 0);
			$isAgent		= Common::validateRole('agent');
			$isAdmin		= Common::validateRole('admin');
			$isCompanyAdmin	= Common::validateRole('company_admin');

			if($isAgent){
				$userID = $authUserID;
			}
			else{
				$userID = Common::hashEmptyField($data, 'UserCompanyEbrochure.user_id');
			}

			$propertyID	= Common::hashEmptyField($data, 'UserCompanyEbrochure.property_id', 0);

			$layoutData		= $layout ? json_decode($layout, true) : array();
			$orientation	= Common::hashEmptyField($layoutData, 'orientation', 'landscape');

		//	remove biar ga berat
			$data = Hash::remove($data, 'UserCompanyEbrochure.filename');

		//	upload base 64 image
			$thumbnail = $this->uploadBase64Image($filename, array(
				'type'			=> $isTemplate ? 'template' : 'ebrochure', 
				'orientation'	=> $orientation, 
			));

			if($thumbnail){
				if($isTemplate){
				//	save template
					$this->controller->loadModel('EbrochureTemplate');

					$uniqueID		= String::uuid();
					$templateName	= sprintf('%s-template-%s', $uniqueID, date('Ymd'));

					if($recordID){
						$template = $this->controller->EbrochureTemplate->getData('first', array(
							'conditions' => array(
								'EbrochureTemplate.id' => $recordID, 
							), 
						), array(
							'company' => true, 
						));

						$templateUserID = Common::hashEmptyField($template, 'EbrochureTemplate.user_id');

						if((empty($isAdmin) && empty($isCompanyAdmin)) && ($templateUserID != $authUserID)){
						//	jika user_id template beda dengan user login, save template sebagai template baru
							$recordID				= null;
							$templatePrincipleID	= $principleID;
							$templateUserID			= $authUserID;
						}
						else{
							$templatePrincipleID	= Common::hashEmptyField($template, 'EbrochureTemplate.principle_id', 0);
							$templateName			= Common::hashEmptyField($template, 'EbrochureTemplate.name');
						}
					}
					else{
						$templatePrincipleID	= $principleID;
						$templateUserID			= $authUserID;
					}

					if(empty($recordID)){
					//	berlaku hanya untuk template baru, atau save template lain dengan id baru
						if($isAdmin){
						//	kalo admin prime yang bikin jadi global semua company
							$templatePrincipleID = 0;
						}
						else if($isCompanyAdmin){
						//	kalo admin company yang bikin jadi global di company nya
							$templateUserID = 0;
						}
					}

					$saveData = array(
						'EbrochureTemplate' => array(
							'id'			=> $recordID, 
							'principle_id'	=> $templatePrincipleID, 
							'user_id'		=> $templateUserID, 
							// 'name'			=> $name ?: $templateName, 
							'name'			=> $name, 
							'description'	=> $description, 
							'thumbnail'		=> $thumbnail, 
							'layout'		=> $layout, 
						), 
					);

				//	debug(json_decode($layout, true));exit;
					$result = $this->controller->EbrochureTemplate->doSave($saveData);
				}
				else{
				//	save ebrochure
				//	sebagian input tidak ada di ebrochure buider, jadi ambil sebagian data dari default value property
					$ebrochurePropertyID	= Common::hashEmptyField($ebrochure, 'Property.id');
					$propertyData			= array();

					if(($propertyID && $ebrochurePropertyID) && ($propertyID == $ebrochurePropertyID)){
						$propertyData = $ebrochure;
					}
					else{
						$propertyData = $this->controller->User->Property->find('first', array(
							'conditions' => array(
							//	'Property.id' => $ebrochurePropertyID, 
								'Property.id' => $propertyID, 
							), 
						));

						$propertyData = $this->controller->User->Property->getDataList($propertyData, array(
							'contain' => array(
								'MergeDefault',
								'PropertyAddress',
								'PropertyAsset',
							//	'User',
							),
						));
					}

					$propertyID		= Common::hashEmptyField($propertyData, 'Property.id', $propertyID);
					$mlsID			= Common::hashEmptyField($propertyData, 'Property.mls_id', '');
					$title			= Common::hashEmptyField($propertyData, 'Property.title', '');
					$propertyDesc	= Common::hashEmptyField($propertyData, 'Property.description', '');
					$typeID			= Common::hashEmptyField($propertyData, 'Property.property_type_id', 0); 
					$actionID		= Common::hashEmptyField($propertyData, 'Property.property_action_id', 0);
					$currencyID		= Common::hashEmptyField($propertyData, 'Property.currency_id', 1);
					$price			= Common::hashEmptyField($propertyData, 'Property.price', 0);
					$priceMeasure	= Common::hashEmptyField($propertyData, 'Property.price_measure', 0);
					$specification	= $this->RmProperty->getSpesification($propertyData, array('to_string' => true));
					
					if(empty($description)){
						$description = $propertyDesc;
					}

					$lotUnitID		= Common::hashEmptyField($propertyData, 'PropertyAsset.lot_unit_id', 0);

					$regionID		= Common::hashEmptyField($propertyData, 'PropertyAddress.region_id', 0);
					$cityID			= Common::hashEmptyField($propertyData, 'PropertyAddress.city_id', 0);
					$subareaID		= Common::hashEmptyField($propertyData, 'PropertyAddress.subarea_id', 0);

				//	user data based on selected user (from autocomplete (for admin) or auth user id (for agent))
					$user = $this->controller->User->getData('first', array(
						'conditions' => array(
							'User.id' => $userID, 
						),
					), array(
						'role'		=> 'agent',
						'status'	=> 'semi-active',
						'company'	=> true,
						'admin'		=> true,
					));

					$userEmail			= Common::hashEmptyField($user, 'User.email');
					$userFullName		= Common::hashEmptyField($user, 'User.full_name');
					$userPrincipleID	= Common::hashEmptyField($user, 'User.parent_id');

					// if(empty($name)){
					// //	concat user name + email
					// 	$name = array_filter(array($userFullName, $userEmail ? '(' . $userEmail . ')' : null));
					// 	$name = implode(' ', $name);

					// //	concat user name + email + property title
					// 	$name = array_filter(array($name, $title));
					// 	$name = implode(' - ', $name);
					// }

					$saveData = array(
						'UserCompanyEbrochure' => array(
							'id'						=> $recordID, 
							'principle_id'				=> $userPrincipleID, 
							'name'						=> $name, 
							'description'				=> $description, 
							'user_id'					=> $userID, 
							'agent_email'				=> $userEmail, 
							'property_id'				=> $propertyID, 
							'mls_id'					=> $mlsID, 
							'property_title'			=> $title, 
							'property_type_id'			=> $typeID, 
							'property_action_id'		=> $actionID, 
							'currency_id'				=> $currencyID, 
							'property_price'			=> $price, 
							'description_property'		=> $propertyDesc, 
							'specification_property'	=> $specification, 
							'region_id'					=> $regionID, 
							'city_id'					=> $cityID, 
							'subarea_id'				=> $subareaID, 
							'layout'					=> $layout, 
							'ebrosur_photo'				=> $thumbnail, 
							'filename'					=> $thumbnail, 
						), 
					);

					$this->UserCompanyEbrochure = $this->controller->User->UserCompanyEbrochure;

					if(empty($ebrochureID)){
						$userCode		= Common::hashEmptyField($user, 'User.code');
						$ebrochureCode	= $this->UserCompanyEbrochure->generateCode($userCode);

					//	append save data
						$saveData = Hash::insert($saveData, 'UserCompanyEbrochure.code', $ebrochureCode);
					}

				//	$saveData	= Hash::filter($saveData);
					$layoutData	= $this->parseEbrochureLayout($layout);
					$saveData	= array_replace_recursive($saveData, $layoutData);

				//	save data
					$validationRules = $this->UserCompanyEbrochure->validate;

					foreach($validationRules as $validationField => $validationRule){
						if($validationField != 'agent_email'){
							$this->UserCompanyEbrochure->validator()->remove($validationField);
						}
					}

				//	debug($this->controller->data);
				//	debug($saveData);
				//	debug($validationRules);
				//	exit;

					$result = $this->UserCompanyEbrochure->saveBuilderData($saveData);
				}
			}
			else{
				$result = Hash::insert($result, 'status', 'error');
				$result = Hash::insert($result, 'msg', __('Gagal menyimpan %s, data tidak valid', $isTemplate ? 'eBrosur' : 'Template'));
			}

		//	ini di unset biar pas save log ga hang (file base64 hasil generate ga butuh)
		//	dan harus selalu kosong, karena tiap save selalu dikasih hasil generate base64 baru
			$this->controller->request->data['UserCompanyEbrochure']['filename'] = null;

			$status = Common::hashEmptyField($result, 'status');

			if(empty($isTemplate) && $status == 'success'){
				$recordID	= Common::hashEmptyField($result, 'id');
				$redirect	= Common::hashEmptyField($result, 'redirect', array());
				$redirect	= $redirect ? $redirect : Router::url(array(
					'admin'			=> true,
					'controller'	=> 'ebrosurs',
					'action'		=> 'detail',
					$recordID,
				), true);

				$result = Hash::insert($result, 'redirect', $redirect);
			}
		}

	//	berat amat
		$result = Hash::remove($result, 'Log');

		if($return){
			return $result;
		}
		else{
		//	$this->RmCommon->_callDataForAPI($ebrochure, 'manual');
			$this->RmCommon->setProcessParams($result, $redirect, array(
				'noRedirect' => true, 
			));

			if($isAjax){
				$this->autoRender = false;
				$this->autoLayout = false;

			//	$result = Hash::remove($result, 'Log');
				echo(json_encode($result));
				exit;
			}
		}
	}

	public function callBeforeRegenerateEbrochure($ebrochure = array()){
		$ebrochure	= (array) $ebrochure;
		$data		= $this->controller->request->data;
		$result		= array('data' => $ebrochure);

		$propertyID		= Common::hashEmptyField($this->controller->params->named, 'property_id');
		$ebrochureID	= Common::hashEmptyField($ebrochure, 'UserCompanyEbrochure.id', 0);
		$ebrochureID	= Common::hashEmptyField($data, 'UserCompanyEbrochure.id', $ebrochureID);
		$redirect		= array();

		if($ebrochureID){
			$redirect = array(
				'admin'			=> true, 
				'controller'	=> 'ebrosurs', 
				'action'		=> 'detail', 
				$ebrochureID, 
			);
		}
		else if($propertyID){
			$companyData	= Common::config('Config.Company.data', array());
			$isAllowEdit	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_edit_property');
			$isEasyMode		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_easy_mode');
			$redirect		= array(
				'admin'			=> true, 
				'controller'	=> 'properties', 
			);			

			if($isAllowEdit){
				$redirect = array_merge($redirect, array(
					'action' => $isEasyMode ? 'easy_preview' : 'edit', 
					$propertyID, 
				));
			}
			else{
				$redirect= array_merge($redirect, array(
					'action' => 'index', 
				));
			}
		}

		if($data && ($ebrochureID || $propertyID)){
			if($propertyID){
				$userID				= $this->controller->User->Property->field('Property.user_id', array('Property.id' => $propertyID));
				$userPrincipleID	= $this->controller->User->field('User.parent_id', array('User.id' => $userID));

				$this->controller->request->data['UserCompanyEbrochure']['user_id']			= $userID;
				$this->controller->request->data['UserCompanyEbrochure']['principle_id']	= $userPrincipleID;

				$data	= Hash::insert($data, 'UserCompanyEbrochure.user_id', $userID);
				$data	= Hash::insert($data, 'UserCompanyEbrochure.principle_id', $userPrincipleID);
				$result	= $this->callBeforeSaveBuilder($data, array(
					'return' => true, // biar ga eksekusi setprosessparam
				));
			}
			else{
				$propertyID	= Common::hashEmptyField($data, 'UserCompanyEbrochure.property_id', $propertyID);
				$layout		= Common::hashEmptyField($data, 'UserCompanyEbrochure.layout');
				$filename	= Common::hashEmptyField($data, 'UserCompanyEbrochure.filename', '', array('urldecode' => false));

				$layoutData		= $layout ? json_decode($layout, true) : array();
				$orientation	= Common::hashEmptyField($layoutData, 'orientation', 'landscape');
				$thumbnail		= $this->uploadBase64Image($filename, array(
					'type'			=> 'ebrochure', 
					'orientation'	=> $orientation, 
				));

				if($thumbnail){
					$this->UserCompanyEbrochure = $this->controller->User->UserCompanyEbrochure;

				//	save data
					$validationRules = $this->UserCompanyEbrochure->validate;

					foreach($validationRules as $validationField => $validationRule){
						$this->UserCompanyEbrochure->validator()->remove($validationField);
					}

					$result = $this->UserCompanyEbrochure->saveBuilderData(array(
						'UserCompanyEbrochure' => array(
							'id'			=> $ebrochureID, 
							'property_id'	=> $propertyID, 
							'ebrosur_photo'	=> $thumbnail, 
							'filename'		=> $thumbnail, 
							'layout'		=> $layout, 
						), 
					));
				}
				else{
					$result = array_merge($result, array(
						'status'	=> 'error', 
						'msg'		=> __('Gagal menyimpan eBrosur, data tidak valid'), 
					));
				}
			}
		}

		$this->RmCommon->setProcessParams($result, $redirect, array(
			'redirectError' => true, 
		));
	}

	public function uploadBase64Image($image = null, array $options = null){
		$image		= (string) $image;
		$options	= (array) $options;

		$imageInfo		= explode(',', $image);
		$imageData		= isset($imageInfo[1]) ? base64_decode($imageInfo[1]) : null;
		$ebrochurePhoto	= '';

		if($imageData){
			$stringStart	= strlen('data:image/');
			$stringEnd		= strpos($imageInfo[0], ';');
			$extension		= substr($imageInfo[0], $stringStart, $stringEnd - $stringStart);

			if($extension == 'jpeg'){
				$extension = 'jpg';
			}

			$uploadPath		= Configure::read('__Site.upload_path');
			$uploadType		= Common::hashEmptyField($options, 'type', 'ebrochure');
			$orientation	= Common::hashEmptyField($options, 'orientation', 'landscape');

			if($uploadType == 'template'){
				$savePath	= Configure::read('__Site.ebrosurs_template');
				$modelName	= 'EbrochureTemplate';
				$fieldName	= 'thumbnail';
			}
			else{
				$savePath	= Configure::read('__Site.ebrosurs_photo');
				$modelName	= 'UserCompanyEbrochure';
				$fieldName	= 'ebrosur_photo';
			}

			$uniqueID	= String::uuid();
			$imageName	= $uniqueID . '-ebrochure-studio.' . $extension;
			$uploadTo	= $uploadPath . DS . $savePath . DS;

			$subPath = $this->RmImage->generateSubPathFolder($imageName);
			$subPath = $this->RmImage->makeDir($uploadTo, $subPath);

			$ebrochurePhoto	= '/' . $subPath . $imageName;
			$ebrochurePhoto	= str_replace(DS, '/', $ebrochurePhoto);

			$uploadPath	= str_replace('/', DS, $uploadTo . $subPath . $imageName);

			if(file_exists($uploadPath) === false){
				$uploaded	= file_put_contents($uploadPath, $imageData);
				$uploadData	= array(
					$fieldName => $ebrochurePhoto, 
				);

				if($uploaded){
					$this->RmImage->_generateThumbnail($uploadData, $fieldName, $savePath, array(
						'type_image' => $orientation, 
					));
				}
				else{
					$ebrochurePhoto = '';
				}
			}
		}

		return $ebrochurePhoto;
	}

//	untuk extract data dari object canvas terus dimasukkan ke database
//	sebelum pake builder pake input biasa
	public function parseEbrochureLayout($layerData = null){
		$saveData = array();
		$data = $this->controller->request->data;

		if($layerData){
			$layerData		= is_array($layerData) ? $layerData : json_decode($layerData, true);
			$saveData		= array();
			$predefinedID	= array(
			//	'prime-property-type'			=> 'UserCompanyEbrochure.', 
			//	'prime-property-action'			=> 'UserCompanyEbrochure.', 
			//	'prime-property-photo'			=> 'UserCompanyEbrochure.', 
				'prime-property-id'				=> 'UserCompanyEbrochure.mls_id', 
				// 'prime-property-title'			=> 'UserCompanyEbrochure.name', 
			//	'prime-property-price'			=> 'UserCompanyEbrochure.', 
			//	'prime-property-keyword'		=> 'UserCompanyEbrochure.', 
				'prime-property-description'	=> 'UserCompanyEbrochure.description_property', 
				'prime-property-specification'	=> 'UserCompanyEbrochure.specification_property', 
			//	'prime-property-location'		=> 'UserCompanyEbrochure.', 
			//	'prime-agent-photo'				=> 'UserCompanyEbrochure.', 
			//	'prime-agent-name'				=> 'UserCompanyEbrochure.', 
			//	'prime-agent-phone'				=> 'UserCompanyEbrochure.', 
				'prime-agent-email'				=> 'UserCompanyEbrochure.user_id', 
			//	'prime-company-logo'			=> 'UserCompanyEbrochure.', 
			//	'prime-company-name'			=> 'UserCompanyEbrochure.', 
			//	'prime-company-phone'			=> 'UserCompanyEbrochure.', 
			//	'prime-company-email'			=> 'UserCompanyEbrochure.', 
			//	'prime-company-address'			=> 'UserCompanyEbrochure.', 
			);

			$objects	= Common::hashEmptyField($layerData, 'objects', array());
			$agentData	= array();

			foreach($objects as $key => $object){
				$primeID	= Common::hashEmptyField($object, 'prime_id');
				$objectType	= Common::hashEmptyField($object, 'type');

				if($primeID && array_key_exists($primeID, $predefinedID) && in_array($objectType, array('text', 'i-text', 'textbox'))){
					$fieldName	= $predefinedID[$primeID];
					$fieldValue	= Hash::get($saveData, $fieldName);

					if(empty($fieldValue)){
						$fieldValue = Common::hashEmptyField($object, 'text');

						if($primeID == 'prime-agent-email'){
							$agentData = $this->controller->User->find('first', array(
								'conditions' => array(
									'User.email' => $fieldValue, 
								), 
							));

							$fieldValue = Common::hashEmptyField($agentData, 'User.id', 0);
						}
					}

					if($fieldValue){
						$saveData = Hash::insert($saveData, $fieldName, $fieldValue);
					}
				}
			}

			// Ambil Name/judul Ebrosur apabila User ada input masukan
			if( !empty($data['UserCompanyEbrochure']['name']) ) {
				$saveData = Hash::insert($saveData, 'UserCompanyEbrochure.name', $data['UserCompanyEbrochure']['name']);
			}

			// if($agentData){
			// //	generate ebrochure name based on agent name + property title
			// 	$agentID		= Common::hashEmptyField($agentData, 'User.id', 0);
			// 	$agentFullName	= Common::hashEmptyField($agentData, 'User.full_name');
			// 	$agentEmail		= Common::hashEmptyField($agentData, 'User.email');

			// 	$ebrochureName	= Common::hashEmptyField($saveData, 'UserCompanyEbrochure.name', '');
			// 	$ebrochureName	= array_filter(array($agentFullName, $ebrochureName));
			// 	$ebrochureName	= implode(' - ', $ebrochureName);

			// //	re-insert ebrochure name
			// 	$saveData = Hash::insert($saveData, 'UserCompanyEbrochure.name', $ebrochureName);
			// }

			$saveData = Hash::filter($saveData);
		}

		return $saveData;
	}

	public function setBuilderData($ebrochure = array()){
		$layout = array();

		if($ebrochure){
			$userID		= Common::hashEmptyField($ebrochure, 'User.id');
			$propertyID	= Common::hashEmptyField($ebrochure, 'Property.id');
			$layout		= Common::hashEmptyField($ebrochure, 'UserCompanyEbrochure.layout');
			$layout		= $layout ? json_decode($layout, true) : array();

			if($userID){
				$userFullName	= Common::hashEmptyField($ebrochure, 'User.full_name');
				$userEmail		= Common::hashEmptyField($ebrochure, 'User.email');
				$keyword		= __('%s | %s', $userFullName, $userEmail);

				$this->controller->request->data = Hash::insert($this->controller->request->data, 'Search.agent_keyword', $keyword);
			}

			if($propertyID){
				$propertyMlsID	= Common::hashEmptyField($ebrochure, 'Property.mls_id');
				$propertyTitle	= Common::hashEmptyField($ebrochure, 'Property.title');
				$keyword		= __('%s %s', $propertyMlsID, $propertyTitle);

				$this->controller->request->data = Hash::insert($this->controller->request->data, 'Search.property_keyword', $keyword);
			}
		}
		else if(Hash::check($this->controller->params->named, 'property_id')){
			$propertyID		= Common::hashEmptyField($this->controller->params->named, 'property_id');
			$propertyData	= $propertyID ? $this->controller->requestAction(array(
				'admin'			=> false, 
				'controller'	=> 'ajax', 
				'action'		=> 'get_property', 
				'all', 
				$propertyID, 
			)) : array();

			if($propertyData){
				$propertyData	= json_decode($propertyData, true);
				$propertyMlsID	= Common::hashEmptyField($propertyData, 'property_id', '');
				$propertyTitle	= Common::hashEmptyField($propertyData, 'property_title', '');

				$this->controller->request->data = array_replace_recursive($this->controller->request->data, array(
					'Search' => array(
						'property_keyword'	=> __('%s %s', $propertyMlsID, $propertyTitle), 
						'agent_keyword'		=> Common::hashEmptyField($propertyData, 'agent_label', ''), 
					), 
					'UserCompanyEbrochure' => array(
						'property_id'	=> $propertyID, 
						'user_id'		=> Common::hashEmptyField($propertyData, 'agent_id', ''), 
					), 
				));				
			}
		}

		$background		= Common::hashEmptyField($layout, 'background', 'rgba(255, 255, 255, 1)');
		$orientation	= Common::hashEmptyField($layout, 'orientation', 'landscape');

		$this->controller->request->data = Hash::insert($this->controller->request->data, 'Canvas.background_color', $background);
		$this->controller->request->data = Hash::insert($this->controller->request->data, 'Canvas.orientation', $orientation);

	//	ini di unset biar pas save log ga hang (file base64 hasil generate ga butuh)
	//	dan harus selalu kosong, karena tiap save selalu dikasih hasil generate base64 baru
		$this->controller->request->data['UserCompanyEbrochure']['filename'] = null;

		$templates = $this->controller->requestAction(array(
			'admin'			=> false, 
			'controller'	=> 'ajax', 
			'action'		=> 'get_ebrochure_template', 
		));

		$this->controller->set(array(
			'templates'		=> $templates, 
			'companyData'	=> $this->getCompanyData(), 
		));
	}

	public function getMergeData(array $ebrochure = array()){
		if(Hash::check($ebrochure, 'UserCompanyEbrochure')){
			$ebrochure = $this->controller->User->UserCompanyEbrochure->getMergeList($ebrochure);
			$ebrochure = $this->controller->User->getMergeList($ebrochure, array('contain' => array('UserProfile')));
			$ebrochure = $this->controller->User->Property->getMergeList($ebrochure, array('contain' => array('PropertyAddress')));

			$propertyID		= Common::hashEmptyField($ebrochure, 'Property.id');
			$description	= Common::hashEmptyField($ebrochure, 'Property.description', '', array('urldecode' => false));
			$specifications	= $this->RmProperty->getSpesification($ebrochure, array('to_string' => true));

			$ebrochure['UserCompanyEbrochure']['specification_property']	= $specifications;
			$ebrochure['UserCompanyEbrochure']['description_property']		= $description;

		//	get media data
			$propertyMedias = $this->controller->User->Property->PropertyMedias->getData('all', array(
				'contain'		=> array('CategoryMedias'), 
				'conditions'	=> array(
					'PropertyMedias.property_id' => $propertyID, 
				), 
			));

			$ebrochure = Hash::insert($ebrochure, 'PropertyMedias', $propertyMedias);
		}

		return $ebrochure;
	}

	public function replaceLayout(array $sourceData = array(), array $replacementData = array(), array $options = array()){
		$propertyID		= Common::hashEmptyField($options, 'property_id');
		$forceReplace	= Common::hashEmptyField($options, 'force_replace');

		if(($sourceData && $replacementData) || ($replacementData && ($propertyID || $forceReplace))){
			$replacementObjects		= Common::hashEmptyField($replacementData, 'objects', array());
			$replacementOrientation	= Common::hashEmptyField($replacementData, 'orientation', 'landscape');
			$sourceCollections		= array(
				'prime-property-type'			=> array(), 
				'prime-property-action'			=> array(), 
				'prime-property-photo'			=> array(), 
				'prime-property-id'				=> array(),  
				'prime-property-title'			=> array(),  
				'prime-property-price'			=> array(), 
				'prime-property-keyword'		=> array(), 
				'prime-property-description'	=> array(),  
				'prime-property-specification'	=> array(),  
				'prime-property-location'		=> array(), 
				'prime-agent-photo'				=> array(), 
				'prime-agent-name'				=> array(), 
				'prime-agent-phone'				=> array(), 
				'prime-agent-email'				=> array(),  
				'prime-company-logo'			=> array(), 
				'prime-company-name'			=> array(), 
				'prime-company-phone'			=> array(), 
				'prime-company-email'			=> array(), 
				'prime-company-address'			=> array(), 
				'prime-company-domain'			=> array(), 
				'prime-copyright'				=> array(), 
			);

		//	debug($replacementOrientation);exit;

		//	mapping source data biar extract cuma sekali
			foreach($sourceCollections as $primeId => $collections){
				$collections = Hash::extract($sourceData, sprintf('objects.{n}[prime_id=%s]', $primeId));

				$sourceCollections[$primeId] = $collections;
			}

			$propertyData = $propertyID ? $this->controller->requestAction(array(
				'admin'			=> false, 
				'controller'	=> 'ajax', 
				'action'		=> 'get_property', 
				'all', 
				$propertyID, 
			)) : array();

			if($propertyData){
				$propertyData = json_decode($propertyData, true);
			}

			if($forceReplace){
				$propertyData = array_replace($propertyData, $this->getCompanyData());
			}

			$baseUrl		= Router::fullbaseUrl();
			$propertyIndex	= 0; // yang punya increment cuma photo property

		//	replace value $replacementLayout layout dari value $sourceLayout
			foreach($replacementObjects as $replacementIndex => &$replacement){
				$primeId		= Common::hashEmptyField($replacement, 'prime_id');
				$type			= Common::hashEmptyField($replacement, 'type');
				$isLogo			= in_array($primeId, array('prime-company-logo', 'prime-copyright'));
				$isCopyright	= $primeId == 'prime-copyright';

				if(in_array($type, array('image', 'text', 'i-text', 'textbox')) && empty($isCopyright)){
					$sourceIndex = 0;

					if($primeId == 'prime-property-photo'){
						$sourceIndex = $propertyIndex;

					//	check next photo index
					//	kalo exist set increment + 1, kalo ga ada increment balik 0
						if(Hash::check($sourceCollections, sprintf('%s.%s', $primeId, $sourceIndex + 1))){
							$propertyIndex++;
						}
						else{
							$propertyIndex = 0;
						}
					}

				//	tarik data dari $sourceCollections
					$field	= 'text';
					$value	= '';

					if($type == 'image'){
						$field = 'src';
					}

					if(Hash::check($sourceCollections, sprintf('%s.%s', $primeId, $sourceIndex))){
						$value = Common::hashEmptyField($sourceCollections, sprintf('%s.%s.%s', $primeId, $sourceIndex, $field), '');
					}
					else if($propertyData){
					//	ambil dari $propertyData
						$sourceField = str_replace(array('prime-', '-'), array('', '_'), $primeId);

						if(in_array($primeId, array('prime-company-logo', 'prime-agent-photo', 'prime-property-photo'))){
							$sourceField = sprintf('%s.%s.url', $sourceField, $sourceIndex);
						}

						$value = Common::hashEmptyField($propertyData, $sourceField, '');
					}

					if($value){
					//	timpa value replacement
						$replacement = Hash::insert($replacement, $field, $value);

						if($type == 'image'){
							$curl = curl_init(htmlentities($value));

							curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
							curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
							curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);

							$imageData = curl_exec($curl);

							curl_close($curl);

							if($imageData){
								$image			= @imagecreatefromstring($imageData);
								$imageWidth		= @imagesx($image);
								$imageHeight	= @imagesy($image);

						//	$imagePath = str_replace(array($baseUrl.'/', 'https://'), array('', 'http://'), $value);
						//	$imageInfo = @getimagesize(htmlentities($imagePath));

						//	if($imageInfo){
						//		$imageWidth			= Common::hashEmptyField($imageInfo, 0);
						//		$imageHeight		= Common::hashEmptyField($imageInfo, 1);
								$replacementWidth	= Common::hashEmptyField($replacement, 'width');
								$replacementHeight	= Common::hashEmptyField($replacement, 'height');
								$replacementScaleX	= Common::hashEmptyField($replacement, 'scaleX', 1);
								$replacementScaleY	= Common::hashEmptyField($replacement, 'scaleY', 1);

								if($imageWidth && $imageHeight && $replacementWidth && $replacementHeight){
								//	if($primeId == 'prime-company-logo'){
								//	//	ini yang susah, ukuran gambar pake fullsize makanya ada "original_" size nya
								//	//	kalo yang laen ukuran fixed
								//		$replacementWidth	= Common::hashEmptyField($replacement, 'original_width', $replacementWidth);
								//		$replacementHeight	= Common::hashEmptyField($replacement, 'original_height', $replacementHeight);
								//		$replacementScaleX	= Common::hashEmptyField($replacement, 'original_scaleX', $replacementScaleX);
								//		$replacementScaleY	= Common::hashEmptyField($replacement, 'original_scaleY', $replacementScaleY);

								//	//	ukuran object di canvas
								//		$scaledReplacementWidth		= $replacementWidth * $replacementScaleX;
								//		$scaledReplacementHeight	= $replacementHeight * $replacementScaleY;

								//		$scaleX	= $scaledReplacementWidth / $imageWidth;
								//		$scaleY	= $scaledReplacementHeight / $imageHeight;
								//		$scale	= $scaleX;

								//		if(($imageWidth * $scaleX) <= $scaledReplacementWidth && ($imageHeight * $scaleY) > $scaledReplacementHeight){
								//		//	kalo width udah bener tapi height nya nyundul, pake scale Y
								//			$scale = $scaleY;
								//		}

								//	//	if(!empty($this->controller->params->query['debug'])){
								//	//		debug('before : ');
								//	//		debug(array(
								//	//			'source'	=> $value, 
								//	//			'dimension'	=> $imageWidth . '('.$scaleX.')' . ' X ' . $imageHeight . '('.$scaleY.')', 
								//	//			'original'	=> $replacementWidth . '('.$scaledReplacementWidth.')' . ' X ' . $replacementHeight . '('.$scaledReplacementHeight.')', 
								//	//		));

								//	//		debug('after : ');
								//	//		debug(array(
								//	//			'dimension'	=> ($imageWidth * $scaleX) . ' Xsd ' . ($imageHeight * $scaleY), 
								//	//			'original'	=> $scaledReplacementWidth . ' X ' . $scaledReplacementHeight, 
								//	//		));

								//	//		debug('new width : ' . $imageWidth . ' * ' . $scaleX . ' = ' . ($imageWidth * $scaleX));
								//	//		debug('new height : ' . $imageHeight . ' * ' . $scaleY . ' = ' . ($imageHeight * $scaleY));
								//	//	}
								//	}
								//	else{
									//	sebenernya cuma butuh scale karna widthnya udah sesuai sama width gambar asli
									//	set width sama height pake ukuran asli, nanti di resize sama fabric.js dari scale
										$scaleX	= ($replacementWidth * $replacementScaleX) / $imageWidth;
										$scaleY	= ($replacementHeight * $replacementScaleY) / $imageHeight;
										$scale	= $scaleX;
								//	}

									$replacement = array_replace($replacement, array(
										'width'			=> $imageWidth, 
										'height'		=> $imageHeight, 
										'scaleX'		=> $scale, 
										'scaleY'		=> $scale, 
										'crossOrigin'	=> 'anonymous', 
									));
								}

							//	if(!empty($this->controller->params->query['debug'])){
							//		debug($replacement);
							//		exit;
							//	}
							}
						}
					}
				}
			}

			$replacementData = Hash::insert($replacementData, 'objects', $replacementObjects);
		}

		return $replacementData;
	}

	public function getCompanyData(){
		$companyData	= Common::config('Config.Company.data', array());
		$result			= array();

		if($companyData){
		//	force replace biasanya data property juga kosong
			App::import('Helper', 'Rumahku');

			$RumahkuHelper	= new RumahkuHelper(new View());
			$savePath		= Configure::read('__Site.logo_photo_folder');

			$companyLogo		= Common::hashEmptyField($companyData, 'UserCompany.logo', '');
			$companyName		= Common::hashEmptyField($companyData, 'UserCompany.name', '');
			$companyZip			= Common::hashEmptyField($companyData, 'UserCompany.zip', '');
			$companyEmail		= Common::hashEmptyField($companyData, 'UserCompany.contact_email', '');
			$companyDomain		= Common::hashEmptyField($companyData, 'UserCompanyConfig.domain', '');

			$companyPhone = array_filter(array(
				Common::hashEmptyField($companyData, 'UserCompany.phone'), 
				Common::hashEmptyField($companyData, 'UserCompany.phone_2'), 
			));

			$companyLocation = array_filter(array(
				Common::hashEmptyField($companyData, 'UserCompany.Subarea.name'), 
				Common::hashEmptyField($companyData, 'UserCompany.City.name'), 
				Common::hashEmptyField($companyData, 'UserCompany.Region.name'), 
			));

			$companyLocation	= trim(implode(', ', $companyLocation) . ' ' . $companyZip);
			$companyAddress		= array_filter(array(
				Common::hashEmptyField($companyData, 'UserCompany.address', ''), 
				Common::hashEmptyField($companyData, 'UserCompany.additional_address', ''), 
				$companyLocation, 
			));

			$companyPhone		= implode(', ', $companyPhone);
			$companyAddress		= implode('. ', $companyAddress);
			$companyLogo		= $RumahkuHelper->photo_thumbnail(array(
				'save_path'	=> $savePath, 
				'src'		=> $companyLogo, 
				'size'		=> 'xxsm',
				'url'		=> true,
				'fullbase'	=> true, 
			));

			$result = array(
				'company_name'		=> $companyName, 
				'company_address'	=> $companyAddress, 
				'company_email'		=> $companyEmail, 
				'company_phone'		=> $companyPhone, 
				'company_domain'	=> $companyDomain, 
				'company_logo'		=> array(
				//	format as multiple
					array(
						'text'	=> 'Logo Perusahaan', 
						'url'	=> $companyLogo, 
					), 
				), 
			);
		}

		return $result;
	}


	public function isAllowGenerateEbrochure(){
		$companyData	= Common::config('Config.Company.data', array());
		$isEbrochure	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_brochure');
		$isBuilder		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_ebrochure_builder');
		$isAutoCreate	= Common::hashEmptyField($companyData, 'UserCompanyConfig.auto_create_ebrochure');
		$isAllowed		= false;

		if($isEbrochure && $isAutoCreate){
			$isAdmin		= Configure::read('User.admin');
			$isNeedApproval	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_approval_property');

			if(($isAdmin && $isNeedApproval) || empty($isNeedApproval)){
			//	$companyData	= Common::config('Config.Company.data', []);
			//	$templateID		= Common::hashEmptyField($companyData, 'UserCompanyConfig.ebrochure_template_id', 0);

			//	$template = $this->controller->User->UserCompanyConfig->EbrochureTemplate->getData('first', array(
			//		'conditions' => array(
			//			'EbrochureTemplate.id' => $templateID, 
			//		), 
			//	), array(
			//		'company' => true, 
			//	));

			//	$isAllowed = !empty($template);

			//	bisa pake default prime
				$isAllowed = true;
			}
		}

		return $isAllowed;
	}
}
?>