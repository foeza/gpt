<?php
class RmSettingComponent extends Component {
	public $components = array(
		'RmCommon', 'RmImage', 'RmUser', 'Auth',
        'RmKpr',
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

    function _callBeforeCompanyConfigSave ( $data, $old_data = false ) {
        if( !empty($data) ) {
	        $save_path_general = Configure::read('__Site.general_folder');

			$data = $this->RmImage->_uploadPhoto( $data, 'UserCompanyConfig', 'favicon', $save_path_general, true );

			if( isset($data['UserCompanyConfig']['date']) ) {
				$date = $this->RmCommon->filterEmptyField( $data, 'UserCompanyConfig', 'date' );

				$data = $this->RmCommon->dataConverter($data, array(
					'date' => array(
						'UserCompanyConfig' => array(
							'contract_date',
						),
					)
				));

				$params	= $this->RmCommon->_callConvertDateRange(array(), $date);
				$data['UserCompanyConfig']['live_date']	= $this->RmCommon->filterEmptyField( $params, 'date_from' );
				$data['UserCompanyConfig']['end_date']	= $this->RmCommon->filterEmptyField( $params, 'date_to' );
			}
            
            $mtLocation = Common::hashEmptyField($data, 'UserCompanyConfig.mt_location_name');

        }

        return $data;
    }

    function _callBeforeCompanyConfigView ( $data, $value = array() ) {
        $data   = (array) $data;
        $value  = (array) $value;

        if( !empty($data) ) {
			$live_date = $this->RmCommon->filterEmptyField( $data, 'UserCompanyConfig', 'live_date' );
			$end_date = $this->RmCommon->filterEmptyField( $data, 'UserCompanyConfig', 'end_date' );

			$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'UserCompanyConfig' => array(
						'contract_date',
					),
				)
			), true);

			$data['UserCompanyConfig']['date'] = $this->RmCommon->_callReverseDateRange($live_date, $end_date);

            $mtRegionID     = Common::hashEmptyField($data, 'UserCompanyConfig.mt_region_id');
            $mtCityID       = Common::hashEmptyField($data, 'UserCompanyConfig.mt_city_id');
            $mtSubareaID    = Common::hashEmptyField($data, 'UserCompanyConfig.mt_subarea_id');

        } else {
            $data['UserCompanyConfig']['is_ebrosur_frontend'] = true;

            $mtRegionID     = Common::hashEmptyField($value, 'UserCompanyConfig.mt_region_id');
            $mtCityID       = Common::hashEmptyField($value, 'UserCompanyConfig.mt_city_id');
            $mtSubareaID    = Common::hashEmptyField($value, 'UserCompanyConfig.mt_subarea_id');
        }

        if($mtSubareaID){
            $this->controller->loadModel('ViewLocation');

            $location = $this->controller->ViewLocation->getData('first', array(
                'conditions' => array(
                    'ViewLocation.region_id'    => $mtRegionID, 
                    'ViewLocation.city_id'      => $mtCityID, 
                    'ViewLocation.subarea_id'   => $mtSubareaID, 
                ), 
            ));

            $locationName = array_filter(array(
                Common::hashEmptyField($location, 'ViewLocation.subarea_name'), 
                Common::hashEmptyField($location, 'ViewLocation.city_name'), 
                Common::hashEmptyField($location, 'ViewLocation.region_name'), 
            ));

            $data = Hash::insert($data, 'UserCompanyConfig.mt_location_name', implode(', ', $locationName));
        }

        $data = Hash::insert($data, 'UserCompanyConfig.mt_region_id', $mtRegionID);
        $data = Hash::insert($data, 'UserCompanyConfig.mt_city_id', $mtCityID);
        $data = Hash::insert($data, 'UserCompanyConfig.mt_subarea_id', $mtSubareaID);

        $data = Hash::insert($data, 'UserCompanyConfig.region_id', $mtRegionID);
        $data = Hash::insert($data, 'UserCompanyConfig.city_id', $mtCityID);
        $data = Hash::insert($data, 'UserCompanyConfig.subarea_id', $mtSubareaID);
        return $data;
    }

    function _callBeforeSaveMobileAppVersion( $value = null, $id = null ){
    	$requestData =& $this->controller->request->data;
    	$data = $requestData;
		$params = $this->controller->params->params;
		$this->MobileAppVersion = ClassRegistry::init('MobileAppVersion');

		if ( !empty($data) ) {
			if( !empty($id) ) {
			 	$data = Hash::insert($data, 'MobileAppVersion.id', $id);
			}

			$result = $this->MobileAppVersion->doSave($data, $id);

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'mobile_app_versions',
				'admin' => true,
			));
		}else{
			$requestData = $value;
		}

		$this->controller->set(array(
			'active_menu' => 'mobile_app_versions',
		));
    }

    function generateKprName($data){
        $monthly = Configure::read('__Site.monthly.getName');
        $birthdayPlace = Common::hashEmptyField($data, 10);
        $dp = Common::hashEmptyField($data, 12, Configure::read('__Site.KPR.min_dp'));
        $tenor = Common::hashEmptyField($data, 14);

        $birth_arr = explode(',' , $birthdayPlace);
        $birthPlace = !empty($birth_arr[0]) ? trim($birth_arr[0]) : false;
        $birthDay = !empty($birth_arr[1]) ? trim($birth_arr[1]) : false;

        // tenor jika ada text tahun akan dihilangkan 
        $tenor = str_replace('tahun', '', $tenor);
        $tenor = str_replace('Tahun', '', $tenor);
        // 

        // format date
        if($birthDay){
            $birth_arr = explode(' ', $birthDay);

            if(!empty($birth_arr[1])){
                $birth_arr[1] = Common::hashEmptyField($monthly, $birth_arr[1]);
            }
            $birthDay = implode('/', $birth_arr);
        }

        if($dp){
            $dp = str_replace('%', '', $dp);
        }

        $data = array(
            'agentEmail' => Common::hashEmptyField($data, 2),
            'bankCode' => Common::hashEmptyField($data, 3),
            'clientName' => Common::hashEmptyField($data, 4),
            'clientEmail' => Common::hashEmptyField($data, 5),
            'clientNoHp' => Common::hashEmptyField($data, 6),
            'clientAddress' => Common::hashEmptyField($data, 7),
            'jobName' => Common::hashEmptyField($data, 8),
            'marriedStatus' => Common::hashEmptyField($data, 9),
            'ktp' => Common::hashEmptyField($data, 11),
            'dp' => $dp,
            'plafond' => Common::hashEmptyField($data, 13),
            'tenor' => trim($tenor),
            'mls_id' => Common::hashEmptyField($data, 15),
            'propertyPrice' => Common::hashEmptyField($data, 16),
            'birthPlace' => $birthPlace,
            'birthDay' => !empty($birthDay)?$birthDay:NULL,
        );

        return $this->RmCommon->dataConverter($data, array(
            'date' => array(
                'soldDate',
                'birthDay',
            ),
            'price' => array(
                'plafond',
                'propertyPrice',
            ),
        ));
    }

}
?>