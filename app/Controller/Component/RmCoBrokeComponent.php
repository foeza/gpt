<?php
class RmCoBrokeComponent extends Component {
	var $components = array(
		'RmCommon'
	); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function _callBeforeSaveUser($data, $value, $co_broke_property_id = false){
		if(!empty($data)){
			$data['CoBrokeUser']['co_broke_property_id'] = $co_broke_property_id;
			$data['CoBrokeUser']['address'] = $this->RmCommon->filterEmptyField($data, 'CoBrokeUser', 'address');

			$is_change_address = $this->RmCommon->filterEmptyField($data, 'CoBrokeUser', 'is_change_address');
			$region_id = $this->RmCommon->filterEmptyField($data, 'UserProfile', 'region_id');
			$city_id = $this->RmCommon->filterEmptyField($data, 'UserProfile', 'city_id');
			$subarea_id = $this->RmCommon->filterEmptyField($data, 'UserProfile', 'subarea_id');
			$zip = $this->RmCommon->filterEmptyField($data, 'UserProfile', 'zip');
			$address = $this->RmCommon->filterEmptyField($data, 'UserProfile', 'address');
			$agent_email = $this->RmCommon->filterEmptyField($data, 'CoBrokeUser', 'agent_email');

			if(!empty($is_change_address) && !empty($region_id) && !empty($city_id) && !empty($subarea_id) && !empty($zip)){
				$this->UserProfile = ClassRegistry::init('UserProfile');

				$region = $this->UserProfile->Region->getData('first', array(
					'conditions' => array(
						'Region.id' => $region_id
					)
				));
				$city = $this->UserProfile->City->getData('first', array(
					'conditions' => array(
						'City.id' => $city_id
					)
				));
				$subarea = $this->UserProfile->Subarea->getData('first', array(
					'conditions' => array(
						'City.id' => $subarea_id
					)
				));

				$region_name = $this->RmCommon->filterEmptyField($region, 'Region', 'name');
				$city_name = $this->RmCommon->filterEmptyField($city, 'City', 'name');
				$subarea_name = $this->RmCommon->filterEmptyField($subarea, 'Subarea', 'name');

				$data['CoBrokeUser']['address'] = sprintf('%s, %s, %s - %s %s', $address, $region_name, $city_name, $subarea_name, $zip);
			}

			if(!empty($agent_email)){
				$this->User = ClassRegistry::init('User');

				$User = $this->User->getData('first', array(
					'conditions' => array(
						'User.email' => $agent_email
					)
				));

				$data['CoBrokeUser']['user_id'] = $this->RmCommon->filterEmptyField($User, 'User', 'id', 0);
			}

			$data = $this->RmCommon->dataConverter($data, array(
                'price' => array(
                    'CoBrokeUser' => array(
                        'request_commission'
                    ),
                )
            ));
		}

		return $data;
	}

	function getRequirementCoBroke($data, $commission, $final_type_commission, $final_type_price_commission = 'percentage'){

		App::import('Helper', 'CoBroke');

		// App::import('helper', 'CoBroke');
		$this->CoBroke = new CoBrokeHelper(new View());

		$default_requirement = __("
			1. Nilai komisi [%KOMISI%] untuk CO.BROKING, termasuk teamnya\n
			2. Perjanjian ini hanya berlaku khusus untuk proyek tersebut diatas\n
			3. Pembagian komisi dilakukan saat komisi telah dibayar lunas\n"
		);

        $commission = $this->CoBroke->commissionName($commission, $final_type_commission, $final_type_price_commission);

        $cobroke_requirement = Common::hashEmptyField($data, 'UserCompanyConfig.cobroke_requirement', $default_requirement, array(
        	'type' => 'EOL'
        ));

        if(!empty($cobroke_requirement)){
        	$cobroke_requirement = str_replace('[%KOMISI%]', $commission, $cobroke_requirement);
        }

        $cobroke_requirement = str_replace(array('% dari [%TIPE-KOMISI%]', '[%TIPE-KOMISI%]'), '', $cobroke_requirement);

        return $cobroke_requirement;
	}

	function create_cobroke($property_id, $add_step = false){
		$this->Property = ClassRegistry::init('Property');

		$data_company = Configure::read('Config.Company.data');

		$config 				= Common::hashEmptyField($data_company, 'UserCompanyConfig');
		$is_co_broke 			= Common::hashEmptyField($config, 'is_co_broke');
		$is_open_cobroke 		= Common::hashEmptyField($config, 'is_open_cobroke');
		$is_approval_property 	= Common::hashEmptyField($config, 'is_approval_property');
		$is_admin_approval_cobroke = Common::hashEmptyField($config, 'is_admin_approval_cobroke');

		$approval_set = ((Configure::read('User.admin') && !empty($is_approval_property)) || empty($is_approval_property)) ? true : false;

		if( !empty($property_id) && $approval_set && $is_co_broke && $is_open_cobroke ){
			$property = $this->Property->getData('first',
				array(
					'conditions' => array(
						'Property.id' => $property_id
					),
				),
				array(
					'status' => 'all'
				)
			);
			
			$data = array();

			if(!empty($property)){
				$commission 					= Common::hashEmptyField($config, 'default_agent_commission');
				$type_price_co_broke_commision 	= Common::hashEmptyField($config, 'default_type_price_co_broke_commision');
				$co_broke_commision 			= Common::hashEmptyField($config, 'default_co_broke_commision');
				$type_co_broke_commission 		= Common::hashEmptyField($config, 'default_type_co_broke_commission');
				$co_broke_type 					= Common::hashEmptyField($config, 'default_type_co_broke');
				
				if($add_step === false){
					$commission 					= Common::hashEmptyField($property, 'Property.commission', $commission);
					$co_broke_commision 			= Common::hashEmptyField($property, 'Property.co_broke_commision', $co_broke_commision);
					$type_price_co_broke_commision 	= Common::hashEmptyField($property, 'Property.type_price_co_broke_commision', $type_price_co_broke_commision);
					$type_co_broke_commission 		= Common::hashEmptyField($property, 'Property.type_co_broke_commission', $type_co_broke_commission);
					$co_broke_type 					= Common::hashEmptyField($property, 'Property.co_broke_type', $co_broke_type);					
				}

				$data = hash::insert($data, 'Property.commission', $commission);
				$data = hash::insert($data, 'Property.co_broke_commision', $co_broke_commision);
				$data = hash::insert($data, 'Property.type_price_co_broke_commision', $type_price_co_broke_commision);
				$data = hash::insert($data, 'Property.type_co_broke_commission', $type_co_broke_commission);
				$data = hash::insert($data, 'Property.co_broke_type', $co_broke_type);

				if(!empty($is_admin_approval_cobroke)){
					$data = hash::insert($data, 'Property.force_approve', 1);
				}
			
				$result = $this->Property->CoBrokeProperty->doMakeCoBroke($data, $property_id);

				$this->RmCommon->setProcessParams($result, false, array(
					'noRedirect' => true,
					'ajaxFlash' => false,
					'ajaxRedirect' => false,
					'redirectError' => false
				));
			}
		}
	}

	function saveTypeCobroke($property_id, $co_broke_type){
		$result = false;
		if(!empty($property_id) && !empty($co_broke_type)){
			$this->Property = ClassRegistry::init('Property');

			$this->Property->id = $property_id;

			$this->Property->set('co_broke_type', $co_broke_type);

			$result = $this->Property->save();
		}

		return $result;
	}

	/*
		fungsi toggle hanya berlaku untuk properti yang sudah aktif dan toggle is_cobroke juga aktif 
	*/
	function togglingByProperty($property_id, $data){
		if(!empty($data)){
			$this->CoBrokeProperty = ClassRegistry::init('CoBrokeProperty');

			if(!empty($data['Property']['is_cobroke'])){
                $this->CoBrokeProperty->doCoBroke($property_id, 'active');
            }else if(isset($data['Property']['is_cobroke']) && empty($data['Property']['is_cobroke'])){
                $this->CoBrokeProperty->CoBrokeChangeStatus($property_id, $data);
            }
		}
	}
}
?>