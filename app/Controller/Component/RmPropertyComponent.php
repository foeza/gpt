<?php
class RmPropertyComponent extends Component {
    public $components = array(
        'RmCommon', 'RmUser', 'Rest.Rest'
    );

    function initialize(Controller $controller, $settings = array()) {
        $this->controller = $controller;
    }
    
    function getMeasurePrice ( $property, $price = 0 ) {
        $is_space = $this->RmCommon->filterEmptyField($property, 'PropertyType', 'is_space');
        $is_building = $this->RmCommon->filterEmptyField($property, 'PropertyType', 'is_building');
        
        $dataAsset = $this->RmCommon->filterEmptyField($property, 'PropertyAsset');
        $measure = $this->RmCommon->filterEmptyField($dataAsset, 'LotUnit', 'measure');
        $measure = $this->RmCommon->filterEmptyField($property, 'LotUnit', 'measure', $measure);

        $price = $this->RmCommon->filterEmptyField($property, 'PropertySold', 'price_sold', $price);

        if( !empty($property['PropertySold']['Currency']['id']) ) {
            $currencySold = $property['PropertySold'];
            $rate = $this->RmCommon->filterEmptyField($currencySold, 'Currency', 'rate', 1);
        } else {
            $rate = $this->RmCommon->filterEmptyField($property, 'Currency', 'rate', 1);
        }

        if( empty($price) ) {
            $price = $this->RmCommon->filterEmptyField($property, 'Property', 'price');
        }

        if( !empty($is_space) && !empty($measure) ){
            if( !empty($is_building) ){
                $measure_size = $this->RmCommon->filterEmptyField($property, 'PropertyAsset', 'building_size');
            } else {
                $measure_size = $this->RmCommon->filterEmptyField($property, 'PropertyAsset', 'lot_size');
            }

            $price = $price * $measure_size;
        }
        
        return $price * $rate;
    }

    function _callValidateVideo( $datas, $property_id = false, $session_id = false ) {
        $data = false;

        if ( !empty($datas['PropertyVideos']['name']) ) {
            $idx = 0;

            foreach ($datas['PropertyVideos']['name'] as $key => $value) {
                $value = trim($value);
                $youtube_id = $this->RmCommon->_callGetYoutubeID($value);
                $youtube_title = $this->RmCommon->_callTitleYoutubeID($youtube_id);

                $youtube_title = $this->RmCommon->filterIssetField($datas['PropertyVideos'], 'title', $key, $youtube_title);

                $data[$idx]['PropertyVideos']['url'] = $value;
                $data[$idx]['PropertyVideos']['youtube_id'] = $youtube_id;
                $data[$idx]['PropertyVideos']['title'] = $youtube_title;
                $data[$idx]['PropertyVideos']['session_id'] = $session_id;

                if( !empty($property_id) ) {
                    $data[$idx]['PropertyVideos']['property_id'] = $property_id;
                }

                $idx++;
            }
        }

        return $data;
    }

    function _callPriceRequestData ( $data ) {
        $requestData = array();

        if( !empty($data['PropertyPrice']) ) {
            foreach ($data['PropertyPrice'] as $key => $value) {
                $currency_id = !empty($value['currency_id'])?$value['currency_id']:false;
                $period_id = !empty($value['period_id'])?$value['period_id']:false;
                $price = !empty($value['price'])?$value['price']:false;
                $price = $this->RmCommon->_callPriceConverter($price);

                $requestData['PropertyPrice']['currency_id'][$key] = $currency_id;
                $requestData['PropertyPrice']['period_id'][$key] = $period_id;
                $requestData['PropertyPrice']['price'][$key] = $price;
            }
            
            $data = array_merge($data, $requestData);
        }

        return $data;
    }

    function _callChangeToRequestData ( $data, $modelName, $fieldName, $is_boolean = false ) {
        $requestData = array();

        if( !empty($data[$modelName]) ) {
            foreach ($data[$modelName] as $key => $value) {
                $result = $this->RmCommon->filterEmptyField($value, $modelName, $fieldName);

                if( !empty($result) ) {
                    if( $result == -1 ) {
                        $other_text = $this->RmCommon->filterEmptyField($value, $modelName, 'other_text');

                        if( !empty($other_text) ) {
                            $requestData[$modelName]['other_id'] = true;
                            $requestData[$modelName]['other_text'] = $other_text;
                        }
                    } else { // Selain pilihan other/lainnya
                        if( !empty($is_boolean) ) {
                            $requestData[$modelName][$fieldName][$result] = true;
                        } else {
                            $requestData[$modelName][$fieldName][$key] = $result;
                        }
                    }
                }
            }
        }

        if( is_array($data) ) {
            $data = array_merge($data, $requestData);
        } else {
            $data = $requestData;
        }

        return $data;
    }

    function getLotUnit ( $size = 'm2', $action_type = false, $position = false ) {
        $lblUnit = array(
            1 => __('m2'),
            2 => __('m2'),
            3 => __('hektar'),
            4 => __('are'),
        );
        $size = !empty($lblUnit[$size])?$lblUnit[$size]:$size;

        if( !empty($size) ) {
            switch ($action_type) {
                case 'format':
                    $lotUnit = str_split($size);
                    $unit_name = end($lotUnit);

                    if( !is_numeric($unit_name) ) {
                        $unit_name = false;
                    } else {
                        array_pop($lotUnit);

                        $size = implode('', $lotUnit);
                    }

                    switch ($position) {
                        case 'top':

                            return sprintf('%s<sup>%s</sup>', $size, $unit_name);
                            break;
                        
                        default:
                            # code...
                            break;
                    }
                    break;
                
                default:
                    return ucwords($size);
                    break;
            }
        } else {
            return false;
        }
    }

    function _callDataClient ( $data, $property = false ) {
        $disabledClient = false;
        $client_email = $this->RmCommon->filterEmptyField($data, 'Property', 'client_email');
        $client_id = $this->RmCommon->filterEmptyField($property, 'Property', 'client_id');
        $restActive = $this->Rest->isActive();

        if(!empty($client_email)){
            $client_email = $this->RmCommon->getEmailConverter($client_email);
            $user_data = $this->controller->User->getData('first', array(
                'conditions' => array(
                    'User.email' => $client_email
                ), 
                'fields' => array(
                    'User.id',
                    'User.full_name',
                ),
            ), array(
            //	client bisa diinput siapa aja, kalo di pator role 'client' nanti bakal error duplicate entry email
            //	statusnya all, karena ada kemungkinan client di delete (untuk client yang udah di delete nanti paksa status active lagi)
            	'status' => 'all', 
            ));

            $data = Hash::insert($data, 'Property.client_email', $client_email);

            if( !empty($user_data) ) {
                $user_id = Common::hashEmptyField($user_data, 'User.id', '');
                $company_id = Configure::read('Config.Company.data.UserCompany.user_id');
                
                $user_data = $this->controller->User->UserClient->getMerge($user_data, $user_id, $company_id);
                
                $user_full_name = Common::hashEmptyField($user_data, 'User.full_name', '');
                $user_no_hp = Common::hashEmptyField($user_data, 'UserClient.no_hp', '');

                $data = Hash::insert($data, 'Property.client_id', $user_id);
                $data = Hash::insert($data, 'Property.client_name', Common::hashEmptyField($data, 'Property.client_name', $user_full_name));
                $data = Hash::insert($data, 'Property.client_hp', Common::hashEmptyField($data, 'Property.client_hp', $user_no_hp));

                $disabledClient = true;
            } else {
                $data = Hash::insert($data, 'Property.client_password', $this->RmUser->_generateCode('password', false, 6));
                $data = Hash::insert($data, 'Property.client_auth_password', $this->controller->Auth->password($data['Property']['client_password']));
                $data = Hash::insert($data, 'Property.client_code', $this->RmUser->_generateCode('user_code'));
            }

            if($restActive){
                if(isset($data['Property']['client_name']) && empty($data['Property']['client_name'])){
                    unset($data['Property']['client_name']);
                }
                
                if(isset($data['Property']['client_hp']) && empty($data['Property']['client_hp'])){
                    unset($data['Property']['client_hp']);
                }
            }
        } else if( empty($client_id) ) {
            $data['Property']['client_id'] = NULL;
        }

        if(!$restActive){
            $this->controller->set('disabledClient', $disabledClient);
        }
        
        return $data;
    }

    function _callBeforeSave ( $data, $property = false, $is_easymode = true ) {
        if( !empty($data) ) {
            $this->Property = $this->controller->User->Property;
            $isActiveRest = $this->Rest->isActive();

            $property = !empty($property)?$property:array();
            $dataAddress = Common::hashEmptyField($property, 'PropertyAddress');

            $data = $this->RmCommon->dataConverter($data, array(
                'price' => array(
                    'Property' => array(
                        'price',
                        'co_broke_commision'
                    ),
                ),
                'date' => array(
                    'Property' => array(
                        'contract_date',
                    ),
                )
            ));

            $data = $this->_callDataClient($data, $property);

            if( isset($data['Property']['commission']) ) {
                $commission = Common::hashEmptyField($data, 'Property.commission', 0);
                $data = Hash::insert($data, 'Property.commission', $commission);
            }

            $price = Common::hashEmptyField($data, 'Property.price');
            $dataTitle = Common::hashEmptyField($data, 'Property.title');

            $dataRegion = Common::hashEmptyField($data, 'Region.name');

            $city = Common::hashEmptyField($dataAddress, 'City.name');
            $city = Common::hashEmptyField($data, 'City.name', $city);

            $subarea = Common::hashEmptyField($dataAddress, 'Subarea.name');
            $subarea = Common::hashEmptyField($data, 'Subarea.name', $subarea);

            $zip = Common::hashEmptyField($dataAddress, 'zip');
            $zip = Common::hashEmptyField($data, 'PropertyAddress.zip', $zip);

            $address = Common::hashEmptyField($dataAddress, 'address');
            $address = Common::hashEmptyField($data, 'PropertyAddress.address', $address);
            $no = Common::hashEmptyField($dataAddress, 'no');
            $no = Common::hashEmptyField($data, 'PropertyAddress.no', $no);
            $rt = Common::hashEmptyField($dataAddress, 'rt');
            $rt = Common::hashEmptyField($data, 'PropertyAddress.rt', $rt);
            $rw = Common::hashEmptyField($dataAddress, 'rw');
            $rw = Common::hashEmptyField($data, 'PropertyAddress.rw', $rw);

            if( !empty($is_easymode) ) {
                $userID     = Common::hashEmptyField($data, 'Property.user_id');
                $agentEmail = Common::hashEmptyField($data, 'Property.agent_email');

                if($agentEmail){
                    $userID = $this->controller->User->field('User.id', array('User.email' => $agentEmail));
                }

                $data = Hash::insert($data, 'Property.user_id', $userID);
            }

            if( !empty($data['Property']['currency_id']) ) {
                $property = $this->RmCommon->_callUnset(array(
                    'Currency',
                ), $property);

                $property = $this->Property->Currency->getMerge($property, $data['Property']['currency_id'], 'Currency.id', array(
                    'cache' => array(
                        'name' => __('Currency.%s', $data['Property']['currency_id']),
                    ),
                ));
            }
            if( !empty($data['Property']['property_action_id']) ) {
                $property = $this->RmCommon->_callUnset(array(
                    'PropertyAction',
                ), $property);
                $property = $this->Property->PropertyAction->getMerge($property, $data['Property']['property_action_id'], 'PropertyAction.id', array(
                    'cache' => array(
                        'name' => __('PropertyAction.%s', $data['Property']['property_action_id']),
                    ),
                ));
            }
            if( !empty($data['Property']['property_type_id']) ) {
                $property = $this->RmCommon->_callUnset(array(
                    'PropertyType',
                ), $property);
                $property = $this->Property->PropertyType->getMerge($property, $data['Property']['property_type_id'], 'PropertyType.id', array(
                    'cache' => array(
                        'name' => __('PropertyType.%s', $data['Property']['property_type_id']),
                    ),
                ));
            }
            if( !empty($data['PropertyFacility']['facility_id']) ) {
                $data['PropertyFacility']['facility_id'] = array_filter($data['PropertyFacility']['facility_id']);
            }
            if( !empty($data['PropertyPointPlus']['name']) ) {
                $data['PropertyPointPlus']['name'] = array_filter($data['PropertyPointPlus']['name']);
            }
            if( !empty($data['PropertyAsset']) ) {
                $property['PropertyAsset'] = $data['PropertyAsset'];

                if( !empty($data['PropertyAsset']['lot_unit_id']) ) {
                    $property = $this->RmCommon->_callUnset(array(
                        'LotUnit',
                    ), $property);
                    $property = $this->Property->PropertyAsset->LotUnit->getMerge($property, $data['PropertyAsset']['lot_unit_id'], 'LotUnit', false, array(
                        'cache' => array(
                            'name' => __('LotUnit.%s', $data['PropertyAsset']['lot_unit_id']),
                        ),
                    ));
                }
            }

            $data['Property']['price_measure'] = $this->getMeasurePrice($property, $price);

            $title = Common::hashEmptyField($property, 'Property.title');
            $region = Common::hashEmptyField($dataAddress, 'Region.name');

            if( !empty($dataTitle) ) {
                $title = $dataTitle;
            }
            if( !empty($dataRegion) ) {
                $region = $dataRegion;
            }

            $action = Common::hashEmptyField($property, 'PropertyAction.name');
            $type = Common::hashEmptyField($property, 'PropertyType.name');
            $data['Property']['keyword'] = sprintf('%s, %s %s di %s, %s, %s %s, %s, No %s, RT %s, RW %s', $title, $type, $action, $subarea, $city, $region, $zip, $address, $no, $rt, $rw);

            if( !empty($data['PropertyPrice']) ) {
                $data = $this->_callProcessPricePeriod($data);
            }
        }

        return $data;
    }

    function _callBeforeSaveDraft ( $data, $property ) {
        $user_id = Configure::read('User.id');

        $property = $this->_callDataClient($property);

        if( is_array($property) ) {
            $property = array_filter($property);
        }

        $title = $this->RmCommon->filterEmptyField($property, 'Property', 'title', '');
        $photo = $this->RmCommon->filterEmptyField($property, 'Property', 'photo', '');

        $action = $this->RmCommon->filterEmptyField($property, 'PropertyAction', 'name', '');
        $type = $this->RmCommon->filterEmptyField($property, 'PropertyType', 'name', '');

        $region_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'region_id');
        $city_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'city_id');
        $subarea_id = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'subarea_id');
        $address = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'address', '');

        $no = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'no', '');
        $rt = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'rt', '');
        $rw = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'rw', '');
        $zip = $this->RmCommon->filterEmptyField($property, 'PropertyAddress', 'zip', '');

        $data = $this->_callDataClient($data, $property);
        $dataTitle = $this->RmCommon->filterEmptyField($data, 'Property', 'title', $title);
        $dataPhoto = $this->RmCommon->filterEmptyField($data, 'Property', 'photo', $photo);

        $dataRegion = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'region_id', $region_id);
        $dataCity = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'city_id', $city_id);
        $dataSubarea = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'subarea_id', $subarea_id);
        $dataAddress = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'address', $address);

        $dataNo = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'no', $no);
        $dataRt = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'rt', $rt);
        $dataRw = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'rw', $rw);
        $dataZip = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'zip', $zip);

        $dataAction = $this->RmCommon->filterEmptyField($data, 'PropertyAction', 'name', $action);
        $dataType = $this->RmCommon->filterEmptyField($data, 'PropertyType', 'name', $type);

        $property = $this->_callChangeToRequestData( $property, 'PropertyFacility', 'facility_id' );
        $property = $this->_callChangeToRequestData( $property, 'PropertyPointPlus', 'name' );

        $dataRegionArr = $this->controller->User->UserProfile->Region->getMerge(array(), $dataRegion);
        $dataCityArr = $this->controller->User->UserProfile->City->getMerge(array(), $dataCity);
        $dataSubareaArr = $this->controller->User->UserProfile->Subarea->getMerge(array(), $dataSubarea);

        $dataRegionName = $this->RmCommon->filterEmptyField($dataRegionArr, 'Region', 'name');
        $dataCityName = $this->RmCommon->filterEmptyField($dataCityArr, 'City', 'name');
        $dataSubareaName = $this->RmCommon->filterEmptyField($dataSubareaArr, 'Subarea', 'name');

        $dataKeyword = sprintf('%s, %s %s di %s, %s, %s %s, %s, No %s, RT %s, RW %s', $dataTitle, $dataType, $dataAction, $dataSubareaName, $dataCityName, $dataRegionName, $dataZip, $dataAddress, $dataNo, $dataRt, $dataRw);
        $data['Property']['keyword'] = $dataKeyword;
        
        $propertyPrice = $this->RmCommon->filterEmptyField($property, 'PropertyPrice');
        $dataPropertyPrice = $this->RmCommon->filterEmptyField($data, 'PropertyPrice');

        $property = $this->RmCommon->_callUnset(array(
            'PropertyType',
            'PropertyAction',
            'PropertyPrice',
        ), $property);

        $data = $this->RmCommon->_callMergeRecursive($property, $data, array(
            'Property',
            'PropertyAddress',
            'PropertyAsset',
            'PropertyFacility',
            'PropertyPointPlus',
            'PageConfig',
        ));

        if( empty($dataPropertyPrice) && !empty($propertyPrice) ) {
            $data['PropertyPrice'] = $propertyPrice;
        } else {
            $data['PropertyPrice'] = $dataPropertyPrice;
        }

        $data['PropertyDraft']['user_id'] = $user_id;
        $data['PropertyDraft']['region_id'] = $dataRegion;
        $data['PropertyDraft']['city_id'] = $dataCity;
        $data['PropertyDraft']['subarea_id'] = $dataSubarea;
        $data['PropertyDraft']['title'] = $dataTitle;
        $data['PropertyDraft']['keyword'] = $dataKeyword;
        $data['PropertyDraft']['photo'] = $dataPhoto;
        $data['PropertyDraft']['address'] = $dataAddress;
        $data['PropertyDraft']['zip'] = $dataZip;
        $data['PropertyDraft']['no'] = $dataNo;
        $data['PropertyDraft']['rt'] = $dataRt;
        $data['PropertyDraft']['rw'] = $dataRw;

        return $data;
    }

    function _callGetAllSession ( $step ) {
        $sessionName = Configure::read('__Site.Property.SessionName');

        switch ($step) {
            case 'all':
                $dataBasic = $this->controller->Session->read(sprintf($sessionName, $this->controller->basicLabel));
                $dataAddress = $this->controller->Session->read(sprintf($sessionName, $this->controller->addressLabel));
                $dataAsset = $this->controller->Session->read(sprintf($sessionName, $this->controller->assetLabel));
                $dataMedias = $this->controller->Session->read(sprintf($sessionName, $this->controller->mediaLabel));

                $data = array();

                if( !empty($dataBasic) ) {
                    $data = array_merge($data, $dataBasic);
                }
                if( !empty($dataAddress) ) {
                    $data = array_merge($data, $dataAddress);
                }
                if( !empty($dataAsset) ) {
                    $data = array_merge($data, $dataAsset);
                }
                if( !empty($dataMedias) ) {
                    $data = array_merge($data, $dataMedias);
                }
                break;
            
            default:
                $data = $this->controller->Session->read(sprintf($sessionName, $step));
                break;
        }

        return $data;
    }

    function _callDeleteSession () {
        $sessionName = $this->RmCommon->_callDefaultSessionName();

        $this->controller->Session->delete(sprintf($sessionName, $this->controller->basicLabel));
        $this->controller->Session->delete(sprintf($sessionName, $this->controller->addressLabel));
        $this->controller->Session->delete(sprintf($sessionName, $this->controller->assetLabel));
        $this->controller->Session->delete(sprintf($sessionName, $this->controller->mediaLabel));
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

    function _callProcessPricePeriod ( $data ) {
        $dataPropertyPrice = array();

        $dataPrice = $this->RmCommon->filterEmptyField($data, 'PropertyPrice');
        $arrCurrencyId = $this->RmCommon->filterEmptyField($dataPrice, 'currency_id', false, array());
        $arrCurrencyId = array_filter($arrCurrencyId);
        $arrCurrencyId = array_values($arrCurrencyId);

        if( !empty($arrCurrencyId) ) {

            unset($data['PropertyPrice']);
            $temp_rate = array();
            foreach ($arrCurrencyId as $key => $currency_id) {
                $period_id = $this->RmCommon->filterIssetField($dataPrice, 'period_id', $key);
                $price = $this->RmCommon->filterIssetField($dataPrice, 'price', $key);

                if(!empty($temp_rate[$currency_id])){
                    $data_currency = $temp_rate[$currency_id];
                }else{
                    $temp = array();
                    $data_currency = $temp_rate[$currency_id] = $this->controller->Property->Currency->getMerge($temp, $currency_id, 'Currency.id', array(
                        'cache' => array(
                            'name' => __('Currency.%s', $currency_id),
                        ),
                    ));
                }

                $customPrice = $this->RmCommon->_callPriceConverter($price);

                $data['PropertyPrice'][] = array(
                    'currency_id' => $currency_id,
                    'period_id' => $period_id,
                    'price' => $customPrice,
                    'price_measure' => $this->getMeasurePrice($data_currency, $customPrice)
                    // 'price_measure' => $this->getMeasurePrice($data_currency, intval($customPrice))
                );
            }

            if( !empty($data['PropertyPrice'])) {
                $arrPeriod = Set::extract('/PropertyPrice/period_id', $data);
                $maxPeriod = max($arrPeriod);
                $idxKey = array_search($maxPeriod, $arrPeriod);

                if( !empty($data['PropertyPrice'][$idxKey]) ) {
                    $dataPrice = $data['PropertyPrice'][$idxKey];

                    $data['Property']['currency_id'] = $this->RmCommon->filterIssetField($dataPrice, 'currency_id');
                    $data['Property']['period_id'] = $this->RmCommon->filterIssetField($dataPrice, 'period_id');
                    $data['Property']['price'] = $this->RmCommon->filterIssetField($dataPrice, 'price');
                }
            }
        }

        return $data;
    }

    function reformArray( $resultTable = false, $dataCities = false ) {
        $values = array();
        if( !empty($resultTable) ) {
            $this->City = ClassRegistry::init('City');

            for( $i = 0; $i < count($dataCities); $i++ ) {
                $values[$i] = array();
                $values[$i][0] = $dataCities[$i];
                for( $j = 0; $j < count($resultTable); $j++ ) {                 
                    $value = 0;
                    for( $k = 0; $k < count($resultTable[$j]); $k++ ) {
                        if( $dataCities[$i] == $resultTable[$j][$k][0] ) {
                            $value = $resultTable[$j][$k][1];
                            break;
                        }
                    }
                    $values[$i][$j+1] = $value;
                }
            }
            foreach ($values as $key => $value) {
                $value = $this->City->getMerge(array(), $value[0], 'City', 'City.id', array(
                    'cache' => __('City.%s', $value[0]),
                ));
                if( !empty($value) ) {
                    $values[$key][0] = $value['City']['name'];
                }
            }
        }

        return $values;
    }

    function getNameCustom( $data, $only_location = false, $toSlug = false, $divider = ',' ) {
        $dataAddress = !empty($data['PropertyAddress'])?$data['PropertyAddress']:false;

        $subarea = $this->RmCommon->filterEmptyField($dataAddress, 'Subarea', 'name');
        $city = $this->RmCommon->filterEmptyField($dataAddress, 'City', 'name');
        $zip = $this->RmCommon->filterEmptyField($dataAddress, 'zip');

        if( !empty($subarea) && !empty($city) ) {
            $location = sprintf('%s%s %s %s', $subarea, $divider, $city, $zip);
        } else {
            $location = '';
        }

        if( !empty($only_location) ) {
            $result = $location;
        } else {
            $type = strtolower($this->RmCommon->filterEmptyField($data, 'PropertyType', 'name'));
            $action = strtolower($this->RmCommon->filterEmptyField($data, 'PropertyAction', 'name'));
            
            $result = trim(sprintf(__('%s %s %s'), $type, $action, $location));
        }

        $result = ucwords($result);

        if( !empty($toSlug) ) {
            $result = $this->RmCommon->toSlug($result);
        }

        return $result;
    }

    function _callSetDataRevision($property_id, $new_data, $old_data, $step){
        $result = array();
        
        if(!empty($property_id) && !empty($new_data) && !empty($old_data)){
            $not_allow_field = array('session_id', 'agent_email', 'bt', 'co_broke_commision', 'type_co_broke_commission', 'is_cobroke', 'co_broke_type');

            $model_special = array(
                'PropertyPrice', 
                'name' => 'PropertyPointPlus', 
                'facility_id' => 'PropertyFacility',
            );

            foreach ($new_data as $key_model => $value_data) {
                $result['property_id'] = $property_id;
                $result['step'] = $step;

                foreach ($value_data as $key_field => $value) {
                    $model_exist = false;
                    $old_data[$key_model] = !empty($old_data[$key_model])?$old_data[$key_model]:array();

                    if(array_key_exists($key_model, $new_data) && array_key_exists($key_model, $old_data)){
                        $model_exist = true;
                    }

                    if( $key_model == 'PropertyFacility' ) {
                        if( !empty($new_data[$key_model][$key_field]) && is_array($new_data[$key_model][$key_field]) ) {
                            $new_data[$key_model][$key_field] = array_filter($new_data[$key_model][$key_field]);
                        }
                    }

                    $field_exist = false;
                    if($model_exist && array_key_exists($key_field, $new_data[$key_model]) && array_key_exists($key_field, $old_data[$key_model])){
                        $field_exist = true;
                    }
                    
                    $is_revision = false;
                    if($field_exist && $model_exist && $new_data[$key_model][$key_field] != $old_data[$key_model][$key_field]){
                        $is_revision = true;
                    }

                    if( in_array($key_model, $model_special) && !empty($new_data[$key_model]) ){
                        $model_exist = true;
                        $field_exist = true;

                        if( $new_data[$key_model] != $old_data[$key_model] ) {
                            $is_revision = true;
                            $key_field = 'format_arr';
                            $fieldName = array_keys($model_special, $key_model,false);

                            if( !empty($fieldName[0]) ) {
                                $fieldName = $fieldName[0];

                                if( !empty($new_data[$key_model][$fieldName]) ) {
                                    $new_data[$key_model][$fieldName] = array_filter($new_data[$key_model][$fieldName]);
                                    $flagNewData = true;
                                } else {
                                    $flagNewData = false;
                                }
                            } else {
                                $flagNewData = true;
                            }

                            if( !empty($flagNewData) ) {
                                if( !empty( $new_data[$key_model]) ) {
                                    $new_data[$key_model] = array_filter($new_data[$key_model]);
                                }

                                if( isset($new_data[$key_model]) ) {
                                    $new_data[$key_model][$key_field] = serialize($new_data[$key_model]);
                                }
                            }
                        }
                    }

                    if( $model_exist && $field_exist && $is_revision && !in_array($key_field, $not_allow_field)){
                        if( isset($new_data[$key_model][$key_field]) ) {
                            $result[$key_model][$key_field] = $new_data[$key_model][$key_field];

                            if($key_model == 'Property' && $key_field == 'property_action_id' && $new_data[$key_model][$key_field] == 1){
                                $result['Property']['period_id'] = '';
                            }
                        }
                    }

                }

            }
        }

        return $this->shapingArrayRevision($result, false);
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
                if( !empty($value_data) ) {
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
        }

        return $arr;
    }

    /**
    *
    *   function menggabung data array
    *
    *   @return array data
    */
    function mergeArrayRecursive() {
        $arrays = func_get_args();
        $base = array_shift($arrays);
        if(!is_array($base)) $base = empty($base) ? array() : array($base);
        foreach($arrays as $append) {
            if(!is_array($append)) $append = array($append);
            
            foreach($append as $key => $value) {
                if(!array_key_exists($key, $base) and !is_numeric($key)) {
                    $base[$key] = $append[$key];
                    continue;
                }
                if( !empty($base[$key]) && (is_array($value) or is_array($base[$key]))) {
                    $base[$key] = $this->mergeArrayRecursive($base[$key], $append[$key]);
                } else if(is_numeric($key)) {
                    if(!in_array($value, $base)) $base[] = $value;
                } else {
                    $base[$key] = $value;
                }
            }
        }
        return $base;
    }

    function generateRequestDataRevision($data){
        $result_arr = array();
        if(!empty($data['PropertyRevision'])){
            foreach ($data['PropertyRevision'] as $key => $value) {
                $explode_arr = explode('-', $key);
                $model = !empty($explode_arr[0])?$explode_arr[0]:false;
                $fields = !empty($explode_arr[1])?$explode_arr[1]:false;
                $explode_field = explode(',', $fields);

                if( $model == 'PropertyPrice' ) {
                    $result_arr['Property']['price'] = 1;
                    $result_arr['Property']['currency_id'] = 1;
                    $result_arr['Property']['period_id'] = 1;
                }

                foreach ($explode_field as $key => $field) {
                    $result_arr[$model][$field] = $value;
                }
            }
        }

        return $result_arr;
    }

    function compareDataRevision($data, $data_property, $data_revision){
        if(!empty($data)){
            foreach ($data as $model => $value_data) {
                foreach ($value_data as $field => $value) {
                    if(in_array($model, array('PropertyPrice', 'PropertyPointPlus', 'PropertyFacility'))){
                        $data_property[$model] = unserialize($data_revision[$model][$field]);
                        unset($data_property[$model][$field]);
                    }else if(!empty($value) && array_key_exists($model, $data_revision) && array_key_exists($field, $data_revision[$model])){
                        $data_property[$model][$field] = $data_revision[$model][$field];
                    }
                }
            }
        }

        return $data_property;
    }

    function shapingPropertyPriceArray($values){
        $requestData = array();

        if( !empty($values['PropertyPrice']) ) {

            unset($values['PropertyPrice']['format_arr']);

            foreach ($values['PropertyPrice'] as $key => $value) {
                $currency_id = !empty($value['currency_id'])?$value['currency_id']:false;
                $period_id = !empty($value['period_id'])?$value['period_id']:false;
                $price = !empty($value['price'])?$value['price']:false;

                $requestData['PropertyPrice']['currency_id'][$key] = $currency_id;
                $requestData['PropertyPrice']['period_id'][$key] = $period_id;
                $requestData['PropertyPrice']['price'][$key] = $price;
            }
        }
        
        return $requestData;
    }

    function getSpesification ( $data, $options = array() ) {

        $to_string = !empty($options['to_string']) ? true : false;

        $result = '';
        $is_lot = $this->RmCommon->filterEmptyField($data, 'PropertyType', 'is_lot');
        $is_building = $this->RmCommon->filterEmptyField($data, 'PropertyType', 'is_building');
        $is_residence = $this->RmCommon->filterEmptyField($data, 'PropertyType', 'is_residence');

        $dataAsset = $this->RmCommon->filterEmptyField($data, 'PropertyAsset');
        $level = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'level');

        $lot_name = $this->RmCommon->filterEmptyField($dataAsset, 'LotUnit', 'slug');
        $lot_name = $this->RmCommon->filterEmptyField($data, 'LotUnit', 'slug', $lot_name);

        $building_size = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'building_size');
        $lot_size = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'lot_size');
        $beds = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'beds');
        $beds_maid = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'beds_maid');
        $baths = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'baths');
        $baths_maid = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'baths_maid');
        $direction = $this->RmCommon->filterEmptyField($dataAsset, 'PropertyDirection', 'name');

        $certificate_name = $this->getCertificate($data);
        $spec = array();

        if( !empty($certificate_name) ) {
            $spec[] = array(
                'name' => __('Sertifikat'),
                'value' => $certificate_name,
            );
        }

        if( !empty($is_residence) ) {
            if( !empty($beds) ) {
                if( !empty($beds_maid) ) {
                    $beds = sprintf('%s + %s', $beds, $beds_maid);
                }

                $spec[] = array(
                    'name' => __('KT'),
                    'value' => $beds,
                );
            }
            if( !empty($baths) ) {
                if( !empty($baths_maid) ) {
                    $baths = sprintf('%s + %s', $baths, $baths_maid);
                }

                $spec[] = array(
                    'name' => __('KM'),
                    'value' => $baths,
                );
            }
        }

        if( !empty($is_lot) ) {
            if( !empty($lot_size) ) {
                $spec[] = array(
                    'name' => __('LT'),
                    'value' => sprintf('%s%s', $lot_size, $lot_name)
                );
            }
        }

        if( !empty($is_building) ) {
            if( !empty($building_size) ) {
                $spec[] = array(
                    'name' => __('LB'),
                    'value' => sprintf('%s%s', $building_size, $lot_name),
                );
            }
            if( !empty($level) ) {
                $spec[] = array(
                    'name' => __('Lantai'),
                    'value' => $level
                );
            }
            
            if( !empty($direction) ) {
                $spec[] = array(
                    'name' => __('Arah Bangunan'),
                    'value' => $direction
                );
            }
        }

        if($to_string){
            $spec = $this->toStringSpesification($spec);
        }

        return $spec;
    }

    function getCertificate ( $data ) {
        $certificate_id = $this->RmCommon->filterEmptyField($data, 'Property', 'certificate_id');
        $others_certificate = $this->RmCommon->filterEmptyField($data, 'Property', 'others_certificate');
        $certificate = $this->RmCommon->filterEmptyField($data, 'Certificate', 'name');
        $certificate_name = false;

        if( $certificate_id == -1 && !empty($others_certificate) ) {
            $certificate_name = $others_certificate;
        } else if( !empty($certificate) ) {
            $certificate_name = $certificate;
        }

        return $certificate_name;
    }

    function toStringSpesification($data){
        $result = '';

        if(!empty($data)){
            foreach ($data as $key => $value) {
                if(!empty($value['name']) && !empty($value['value'])){
                    $result .= sprintf('%s: %s', $value['name'], $value['value']);

                    if($key+1 < count($data)){
                        $result .= ', ';
                    }
                }
            }
        }

        return $result;
    }

    function checkRevision ( $value ) {
        $active = $this->RmCommon->filterEmptyField($value, 'Property', 'active');
        $status = $this->RmCommon->filterEmptyField($value, 'Property', 'status');
        $is_admin = Configure::read('User.admin');
        $approval = Configure::read('Config.Approval.Property');

        // kalo properti sudah tidak pending, savenya di ganti jadi validate
        if( !empty($active) && !empty($status) && empty($is_admin) && !empty($approval) ){
            $validate = true;
        } else {
            $validate = false;
        }

        return $validate;
    }

    function _callBeforeView( $value, $data_revision ) {
        /*merging data revision*/
        if(empty($this->controller->request->data)){
            $temp_price  = array();

            if(!empty($data_revision['PropertyPrice'])){
                $temp_price = $data_revision['PropertyPrice'];
                unset($data_revision['PropertyPrice']);
            }
            if(isset($data_revision['PropertyFacility'])){
                $temp_facility = $data_revision['PropertyFacility'];
                unset($data_revision['PropertyFacility']);
            }
            if(isset($data_revision['PropertyPointPlus'])){
                $temp_pointplus = $data_revision['PropertyPointPlus'];
                unset($data_revision['PropertyPointPlus']);
            }

            $value = $this->mergeArrayRecursive($value, $data_revision);

            if(!empty($temp_price)){
                $value['PropertyPrice'] = $temp_price;
            }
            if(isset($temp_facility)){
                $value['PropertyFacility'] = $temp_facility;
            }
            if(isset($temp_pointplus)){
                $value['PropertyPointPlus'] = $temp_pointplus;
            }
        }
        /*end merging data revision*/

        if(!empty($data_revision['PropertyPrice']['format_arr'])){
            if(!empty($data_revision['Property']['property_action_id']) &&  $data_revision['Property']['property_action_id'] == 1){
                unset($data_revision['PropertyPrice']);
                $value['Property']['period_id'] = '';
            }else{
                $PropertyPrice['PropertyPrice'] = unserialize($data_revision['PropertyPrice']['format_arr']);
                $PropertyPrice  = $this->shapingPropertyPriceArray($PropertyPrice);
                
                $data_revision['PropertyPrice'] = $PropertyPrice['PropertyPrice'];
                unset($data_revision['PropertyPrice']['format_arr']);
            }
        }

        return $value;
    }

    function checkPratinjau ( $data ) {
        $in_update = $this->RmCommon->filterEmptyField($data, 'Property', 'in_update');
        $active = $this->RmCommon->filterEmptyField($data, 'Property', 'active');
        $status = $this->RmCommon->filterEmptyField($data, 'Property', 'status');
        $sold = $this->RmCommon->filterEmptyField($data, 'Property', 'sold');

        if( ( empty($active) || !empty($in_update) || !empty($sold) ) && !empty($status) ) {
            return true;
        } else {
            return false;
        }
    }

    function _callBeforeSold( $data, $property_id ) {
        $sold_by            = Common::hashEmptyField($data, 'PropertySold.sold_by_name');
        $arrClient          = Common::hashEmptyField($data, 'PropertySold.client_name');
        $is_cobroke         = Common::hashEmptyField($data, 'PropertySold.is_cobroke');
        $is_bt_commision    = Common::hashEmptyField($data, 'PropertySold.is_bt_commision');
        $sold_by_coBroke_id = Common::hashEmptyField($data, 'PropertySold.sold_by_coBroke_id');

        $arrClient = trim($arrClient);
        $client_email = false;
        $client_name = false;

        if( substr_count($arrClient, '|') == 1 ) {
            $arrClient = explode(' | ', $arrClient);
            if( count($arrClient) == 2 ) {
                $client_email = trim($arrClient[0]);
                $client_name = trim($arrClient[1]);
            }
        } else {
            $client_email = $client_name;
        }

        if(!empty($is_cobroke)){
            if(!empty($sold_by_coBroke_id)){
                $soldBy = $this->controller->User->CoBrokeUser->find('first', array(
                    'conditions' => array(
                        'CoBrokeUser.id' => $sold_by_coBroke_id,
                    ),
                    'contain' => array(
                        'User'
                    )
                ));
            }
        }else{
            $soldBy = $this->controller->User->getData('first', array(
                'conditions' => array(
                    'User.email' => $sold_by,
                ),
            ), array(
                'status' => 'all',
                'company' => true,
            ));
        }

        $client = $this->controller->User->getData('first', array(
            'conditions' => array(
                'User.email' => $client_email,
                'User.first_name <>' => '',
            ),
        ), array(
            'status' => 'all',
        ));
        $client_id = Common::hashEmptyField($client, 'User.id');
        $client = $this->controller->User->UserClient->getMerge($client, $client_id);

        if( !empty($soldBy) ) {
            $currency_id = Common::hashEmptyField($data, 'PropertySold.currency_id');

            $data['PropertySold']['sold_by_id']     = Common::hashEmptyField($soldBy, 'User.id');
            $data['PropertySold']['sold_by_name']   = Common::hashEmptyField($soldBy, 'User.full_name');

            $property = $this->controller->User->Property->getMerge(array(), $property_id);

            $currency_id = Common::hashEmptyField($property, 'Property.currency_id');
            $currency_id = Common::hashEmptyField($data, 'PropertySold.currency_id', $currency_id);

            $bt                 = Common::hashEmptyField($property, 'Property.bt');
            $is_bt_commision    = Common::hashEmptyField($data, 'PropertySold.is_bt_commision');

            if(empty($is_bt_commision)){
                $data = $this->RmCommon->_callUnset(array(
                    'PropertySold' => array(
                        'bt_name',
                        'bt_address',
                        'bt_commission_percentage',
                        'bt_type_commission'
                    )
                ), $data);
            }

            $currency = $this->controller->User->Property->Currency->getMerge(array(), $currency_id, 'Currency.id', array(
                'cache' => array(
                    'name' => __('Currency.%s', $currency_id),
                ),
            ));

            $agent_id = $soldBy['User']['id'];
            $agent_data = $this->controller->User->getMerge(array(), $agent_id);
            $agent_data = $this->controller->User->UserConfig->getMerge($agent_data, $agent_id);
            $agent_data = $this->controller->User->UserCompanyConfig->getMerge($agent_data, $soldBy['User']['parent_id']);
            
            $rate       = Common::hashEmptyField($currency, 'Currency.rate', 1);
            $price_sold = Common::hashEmptyField($data, 'PropertySold.price_sold', 0);

            $price_sold = $price_sold * $rate;

            $commission = Common::hashEmptyField($property, 'Property.commission', 0);
            $sharingtocompany_percentage = Common::hashEmptyField($agent_data, 'UserConfig.sharingtocompany', 0);

            $sharingtoagent_percentage = 100 - $sharingtocompany_percentage;
            
            $royalty_percentage = Common::hashEmptyField($agent_data, 'UserConfig.royalty', 0);
            $pph_percentage     = Common::hashEmptyField($agent_data, 'UserCompanyConfig.pph', 0);

            $total_commission       = ( $price_sold * $commission ) / 100;
            
            $agent_commission_gross = ( $total_commission * $sharingtoagent_percentage ) / 100;
            
            $royalty                = ( $total_commission * $royalty_percentage ) / 100;
            
            $data['PropertySold']['temp_commision'] = $temp_agent_commission = $total_commission - $royalty;

            /*Perhitungan BT*/
            $data = $this->_setBtCommission($data);

            $data['PropertySold']['agent_commission_gross'] = $agent_commission_gross;

            /*Perhitungan CoBroke*/
            $data = $this->soldByCoBroke($data, $sold_by_coBroke_id);

            $temp_agent_commission = Common::hashEmptyField($data, 'PropertySold.temp_commision');
            
            $share_company          = ($temp_agent_commission * $sharingtocompany_percentage) / 100;
            $temp_agent_commission  = $temp_agent_commission - $share_company;

            $pph = ( $temp_agent_commission * $pph_percentage ) / 100;
            
            $agent_commission_net = $temp_agent_commission - $pph;

            $data['PropertySold']['sharingtocompany_percentage'] = $sharingtocompany_percentage;
            $data['PropertySold']['royalty_percentage'] = $royalty_percentage;
            $data['PropertySold']['royalty'] = $royalty;
            $data['PropertySold']['pph_percentage'] = $pph_percentage;
            $data['PropertySold']['pph'] = $pph;

            $data['PropertySold']['rate'] = $rate;
            $data['PropertySold']['commission'] = $commission;
            $data['PropertySold']['total_commission'] = $total_commission;
            
            $data['PropertySold']['agent_commission_net'] = $agent_commission_net;

            $broker_type_commision  = Common::hashEmptyField($data, 'PropertySold.broker_type_commision');
            $broker_commission      = Common::hashEmptyField($data, 'PropertySold.broker_commission');

            /* versi 1 */
            // $data['PropertySold']['company_commission'] = ( $total_commission * $sharingtocompany_percentage ) / 100;

            /* versi 2 */
            $data['PropertySold']['company_commission'] = $share_company;

            if(!empty($broker_type_commision) && !empty($broker_commission)){
                if($broker_type_commision == 'out_corp'){
                    $data['PropertySold']['agent_commission_net'] = $agent_commission_net - $broker_commission;
                }
            }
        }

        if( !empty($client) ) {
            $data['PropertySold']['client_id'] = Common::hashEmptyField($client, 'User.id');
            $data['PropertySold']['client_name'] = Common::hashEmptyField($client, 'UserClient.full_name');
        }

        return $data;
    }

    function _callGetClientData( $property = false ) {
        $client_owner_id = $this->RmCommon->filterEmptyField($property, 'Property', 'client_id');
        if( !empty($client_owner_id) ) {
            $client = $this->controller->User->getMerge(array(), $client_owner_id);
            if( !empty($client['User']) ) {
                $property['ClientOwner'] = $client['User'];
            }   
        }

        $client_buyer_id = $this->RmCommon->filterEmptyField($property, 'PropertySold', 'client_id');
        if( !empty($client_buyer_id) ) {
            $client = $this->controller->User->getMerge(array(), $client_buyer_id);
            if( !empty($client['User']) ) {
                $property['ClientBuyer'] = $client['User'];
            }   
        }

        return $property;
    }

    function setConvertSetToV3($data){
        $property_id_target = $this->RmCommon->filterEmptyField($data, 'Property', 'property_id_target');
        $photo = $this->RmCommon->filterEmptyField($data, 'Property', 'photo');
        $PropertyFacility = $this->RmCommon->filterEmptyField($data, 'PropertyFacility', 'facility_id');
        $PropertyFacilityOther = $this->RmCommon->filterEmptyField($data, 'PropertyFacility', 'other_text');
        $PropertyPointPlus = $this->RmCommon->filterEmptyField($data, 'PropertyPointPlus', 'name');
        $PropertyPrice = $this->RmCommon->filterEmptyField($data, 'PropertyPrice');
        $lot_width = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'lot_width');
        $lot_length = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'lot_length');
        $lot_size = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'lot_size');
        $lot_unit_id = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'lot_unit_id');
        $year_built = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'year_built');
        $no = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'no');
        $rt = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'rt');
        $rw = $this->RmCommon->filterEmptyField($data, 'PropertyAddress', 'rw');
        $period_id = $this->RmCommon->filterEmptyField($data, 'Property', 'period_id');
        $with_no_photo = $this->RmCommon->filterEmptyField($data, 'with_no_photo');

        $year_built = intval($year_built);
        $day = array(
            '1' => 'day',
            '2' => 'week',
            '3' => 'month',
            '4' => 'year',
        );

        if(!empty($period_id)){
            $data['Property']['period'] = $day[$period_id];

            unset($data['Property']['period_id']);
        }

        $address2 = '';
        if(!empty($no)){
            $address2 .= 'No. '.$no;
        }

        if(!empty($rt)){
            $address2 .= ' RT '.$rt;
        }

        if(!empty($rw)){
            $address2 .= ' RW '.$rw;
        }

        if(!empty($address2)){
            $data['PropertyAddress']['address2'] = $address2;
        }

        if(!empty($lot_width)){
            $data['Property']['lot_width'] = $lot_width;

            unset($data['PropertyAsset']['lot_width']);
        }

        if(!empty($lot_length)){
            $data['Property']['lot_length'] = $lot_length;

            unset($data['PropertyAsset']['lot_length']);
        }

        if(!empty($lot_size)){
            $data['Property']['lot_size'] = $lot_size;

            unset($data['PropertyAsset']['lot_size']);
        }

        if(!empty($lot_unit_id)){
            $data['Property']['lot_unit'] = $lot_unit_id;

            unset($data['PropertyAsset']['lot_unit_id']);
        }

        if(empty($year_built)){
            unset($data['PropertyAsset']['year_built']);
        }

        if(empty($photo) && !empty($data['PropertyMedias'])){
            foreach ($data['PropertyMedias'] as $key => $value) {
                if(!empty($value['PropertyMedias']['primary']) && !empty($value['PropertyMedias']['name'])){
                    $photo = $value['PropertyMedias']['name'];
                }
            }

            $data['Property']['photo'] = $photo;
        }

        $data['PropertyAsset']['property_point_plus'] = '';
        if(!empty($PropertyPointPlus)){
            $temp_arr = array();
            foreach ($PropertyPointPlus as $key => $value) {
                if(!empty($value)){
                    $temp_arr[$key] = $value;
                }
            }

            $data['PropertyAsset']['property_point_plus'] = $temp_arr;

            unset($data['PropertyPointPlus']);
        }else if(!empty($data['PropertyPointPlus'])){
            $temp_arr = array();
            foreach ($data['PropertyPointPlus'] as $key => $value) {
                $val = $this->RmCommon->filterEmptyField($value, 'PropertyPointPlus', 'name');

                if(!empty($value)){
                    $temp_arr[$key] = $val;
                }
            }

            $data['PropertyAsset']['property_point_plus'] = serialize($temp_arr);

            unset($data['PropertyPointPlus']);
        }

        $data['PropertyAsset']['property_facilities'] = '';
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
        }else if(!empty($data['PropertyFacility'])){
            $temp_arr = array();

            foreach ($data['PropertyFacility'] as $key => $value) {
                $facility_id = $this->RmCommon->filterEmptyField($value, 'PropertyFacility', 'facility_id');
                $other_text = $this->RmCommon->filterEmptyField($value, 'PropertyFacility', 'other_text');
                
                if(!empty($facility_id) && $facility_id > 0){
                    $temp_arr[$facility_id] = $facility_id;
                }else{
                    $data['PropertyAsset']['others'] = 1;
                    $data['PropertyAsset']['property_facilities_others'] = $other_text;
                }
            }

            $data['PropertyAsset']['property_facilities'] = implode(',', array_filter($temp_arr));

            unset($data['PropertyFacility']);
        }

        if( !empty($PropertyPrice)){
            if(!empty($PropertyPrice['currency_id'])){
                $temp_arr = array();

                $temp_arr['PropertyPeriod']['currency_id'] = $PropertyPrice['currency_id'];
                $temp_arr['PropertyPeriod']['price'] = $PropertyPrice['price'];

                foreach ($PropertyPrice['period_id'] as $key => $value) {
                    if(!empty($PropertyPrice['price'][$key])){
                        $temp_arr['PropertyPeriod']['period_price'][$key] = $temp_arr['PropertyPeriod']['period'][$key] = $day[$PropertyPrice['period_id'][$key]];
                    }
                }

                $data['PropertyPeriod'] = $temp_arr['PropertyPeriod'];
            }else{
                $day = array(
                    '1' => 'day',
                    '2' => 'week',
                    '3' => 'month',
                    '4' => 'year',
                );

                $temp_arr = array();

                foreach ($PropertyPrice as $key => $val_price) {
                    if(!empty($val_price['PropertyPrice']['price'])){
                        $temp_arr['PropertyPeriod']['price'][$key] = $val_price['PropertyPrice']['price'];
                        $temp_arr['PropertyPeriod']['period_price'][$key] = $temp_arr['PropertyPeriod']['period'][$key] = $day[$val_price['PropertyPrice']['period_id']];
                        $temp_arr['PropertyPeriod']['currency_id'][$key] = $val_price['PropertyPrice']['currency_id'];
                    }
                }

                if(!empty($temp_arr)){
                    $data['PropertyPeriod'] = $temp_arr['PropertyPeriod'];
                }
            }

            unset($data['PropertyPrice']);
        }

        if(empty($data['Property']['photo']) && !empty($data['Property']['mls_id'])){
            $property = $this->controller->User->Property->getData('first', array(
                'conditions' => array(
                    'Property.mls_id' => $data['Property']['mls_id']
                )
            ), array(
                'status' => 'all'
            ));
            $data['Property']['photo'] = $this->RmCommon->filterEmptyField($property, 'Property', 'photo');
        }

        $model = array(
            'PropertyAddress',
            'PropertyAsset',
            'PropertyFacility',
            'PropertyPointPlus',
            'PropertyMedias',
            'PropertyVideos'
        );

        foreach ($model as $key => $value) {
            if(!empty($property_id_target)){
                if(!empty($data[$value]['property_id']) && in_array($value, array('PropertyAddress', 'PropertyAsset'))){
                    $data[$value]['property_id'] = $property_id_target;
                }else if(in_array($value, array('PropertyFacility', 'PropertyPointPlus'))){
                    $data[$value]['property_id'] = $property_id_target;
                }else if(in_array($value, array('PropertyMedias', 'PropertyVideos'))){
                    if(!empty($data[$value])){
                        foreach ($data[$value] as $key => $val) {
                            $data[$value][$key]['PropertyMedias']['property_id'] = $property_id_target;
                        }
                    }
                }
            }else{
                if(!empty($data[$value]['property_id']) && in_array($value, array('PropertyAddress', 'PropertyAsset'))){
                    unset($data[$value]['property_id']);
                }else if(in_array($value, array('PropertyFacility', 'PropertyPointPlus'))){
                    unset($data[$value]['property_id']);
                }else if(in_array($value, array('PropertyMedias', 'PropertyVideos'))){
                    if(!empty($data[$value])){
                        foreach ($data[$value] as $key => $val) {
                            unset($data[$value][$key][$value]['property_id']);
                        }
                    }
                }
            }

            if(isset($data[$value]['id'])){
                unset($data[$value]['id']);
            }
        }

        $date = date('Y-m-d h:i:s');

        if(!empty($data['PropertyMedias'])){
            $temp_arr = array();

            foreach ($data['PropertyMedias'] as $key => $value) {
                $name_media = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'name', 0);
                $alias_media = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'alias');
                $primary_media = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'primary');
                $title_media = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'title');
                $order_media = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'order');
                $created_media = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'created', $date);
                $modified_media = $this->RmCommon->filterEmptyField($value, 'PropertyMedias', 'modified', $date);

                $temp_arr[$key]['PropertyMedias'] = array(
                    'name' => $name_media,
                    'alias' => $alias_media,
                    'type' => 1,
                    'main_photo' => $primary_media,
                    'title' => $title_media,
                    'order' => $order_media,
                    'created' => $created_media,
                    'modified' => $modified_media,
                );
            }

            $data['PropertyMedias'] = $temp_arr;
        }

        if(!empty($data['PropertyVideos'])){
            $temp_arr = array();

            foreach ($data['PropertyVideos'] as $key => $value) {
                $order_video = $this->RmCommon->filterEmptyField($value, 'PropertyVideos', 'order', 0);
                $created_video = $this->RmCommon->filterEmptyField($value, 'PropertyVideos', 'created', $date);
                $modified_video = $this->RmCommon->filterEmptyField($value, 'PropertyVideos', 'modified', $date);
                $youtube_id = $this->RmCommon->filterEmptyField($value, 'PropertyVideos', 'youtube_id');
                $title = $this->RmCommon->filterEmptyField($value, 'PropertyVideos', 'title');

                $temp_arr[$key]['PropertyMedias'] = array(
                    'name' => $youtube_id,
                    'type' => 2,
                    'title' => $title,
                    'salt_id' => String::uuid(),
                    'order' => $order_video,
                    'created' => $created_video,
                    'modified' => $modified_video,
                );
            }

            $data['PropertyVideos'] = $temp_arr;
        }

        if(!empty($with_no_photo)){
            unset($data['Property']['photo']);
            unset($data['with_no_photo']);
        }

        return $data;
    }

    function __callSetMedia($data, $property_id, $model = 'PropertyMedias'){
        if(!empty($data[$model]) && !empty($property_id)){
            foreach ($data[$model] as $key => $value) {
                $data[$model][$key]['PropertyMedias']['property_id'] = $property_id;
            }
        }
        
        return $data;
    }

    function setConvertSetToV3Sold($data){
        $result_arr = array();

        if(!empty($data)){
            $property_id = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'property_id');
            $price_sold = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'price_sold');
            $sold_date = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'sold_date');

            $result_arr['PropertySold'] = array(
                'property_id' => $property_id,
                'price_sold' => $price_sold,
                'sold_date' => $sold_date,
                'active' => 1
            );
        }

        return $result_arr;
    }

    function _callSearchDefaultBind( $propertyOptions = false, $modelName = false ) {

        if( !empty($modelName) ) {
            if( in_array('PropertyAddress', $propertyOptions['contain']) ) {
                $this->controller->Property->$modelName->bindModel(array(
                    'hasOne' => array(
                        'PropertyAddress' => array(
                            'foreignKey' => false,
                            'conditions' => array(
                                'PropertyAddress.property_id = '.$modelName.'.property_id',
                            ),
                        ),
                    )
                ), false);
            } 
            if( in_array('PropertyAsset', $propertyOptions['contain']) ) {
                $this->controller->Property->$modelName->bindModel(array(
                    'hasOne' => array(
                        'PropertyAsset' => array(
                            'foreignKey' => false,
                            'conditions' => array(
                                'PropertyAsset.property_id = '.$modelName.'.property_id',
                            ),
                        ),
                    )
                ), false);
            }
            if( in_array('PropertyType', $propertyOptions['contain']) ) {
                $this->controller->Property->$modelName->bindModel(array(
                    'hasOne' => array(
                        'PropertyType' => array(
                            'foreignKey' => false,
                            'conditions' => array(
                                'PropertyType.id = Property.property_type_id',
                            ),
                        ),
                    )
                ), false);
            }
            if( in_array('PropertySold', $propertyOptions['contain']) ) {
                $this->controller->Property->$modelName->bindModel(array(
                    'hasOne' => array(
                        'PropertySold' => array(
                            'foreignKey' => false,
                            'conditions' => array(
                                'PropertySold.property_id = '.$modelName.'.property_id',
                            ),
                        ),
                    )
                ), false);
            }
        }

        return $propertyOptions;
    }

    function setConvertSetToNewCompany($data){
        $PropertyFacility = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'facility_id');
        $PropertyFacilityOther = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'other_text');
        $PropertyPointPlus = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'name');
        $PropertyPrice = $this->RmCommon->filterEmptyField($data, 'PropertyPrice');
        $lot_width = $this->RmCommon->filterEmptyField($data, 'Property', 'lot_width');
        $lot_length = $this->RmCommon->filterEmptyField($data, 'Property', 'lot_length');
        $lot_size = $this->RmCommon->filterEmptyField($data, 'Property', 'lot_size');
        $lot_unit = $this->RmCommon->filterEmptyField($data, 'Property', 'lot_unit');
        
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

    function _callSupportAdvancedSearch ( $return_value = false ) {
        $propertyActions = $this->controller->Property->PropertyAction->getData('list', array(
            'cache' => __('PropertyAction.List'),
        ));
        $propertyTypes = $this->controller->Property->PropertyType->getData('list', array(
            'cache' => __('PropertyType.List'),
        ));
        $regions = $this->controller->Property->PropertyAddress->Region->getData('list', array(
            'cache' => 'Region.List',
        ));
        $propertyDirections = $this->controller->Property->PropertyAsset->PropertyDirection->getData('list', array(
            'cache' => __('PropertyDirection.List'),
        ));
        $propertyConditions = $this->controller->Property->PropertyAsset->PropertyCondition->getData('list', array(
            'cache' => __('PropertyCondition.List'),
        ));

        $categoryStatus = $this->RmCommon->getGlobalVariable('category_status');

        $data = $this->controller->request->data;
        $region = $this->RmCommon->filterEmptyField($data, 'Search', 'region');
        $city = $this->RmCommon->filterEmptyField($data, 'Search', 'city');

        if( !empty($region) ) {
            $cities = $this->controller->Property->PropertyAddress->City->getData('list', array(
                'conditions' => array(
                    'City.region_id' => $region,
                ),
            ));

            if( !empty($city) ) {
                $subareas = $this->controller->Property->PropertyAddress->Subarea->getSubareas('list', $region, $city);
            }
        }

        $this->controller->set(compact(
            'propertyActions', 'propertyTypes', 'regions',
            'propertyDirections', 'propertyConditions',
            'cities', 'subareas', 'categoryStatus'
        ));

        if( !empty($return_value) ) {
            $certificates = Configure::read('__Site.Certificates');
            
            return array(
                'propertyActions' => $propertyActions,
                'propertyTypes' => $propertyTypes,
                'propertyDirections' => $propertyDirections,
                'propertyConditions' => $propertyConditions,
                'certificates' => $certificates,
            );
        }
    }

    function getShortPropertyType( $property = false, $with_location = true ) {
        $customPropertyType = false;
        if( !empty($property) ) {
            $property_type = $this->RmCommon->filterEmptyField($property, 'PropertyType', 'name');
            $status = $this->RmCommon->filterEmptyField($property, 'PropertyAction', 'name', '-');

            $customPropertyType = sprintf('%s %s', $property_type, $status);

            if( !empty($with_location) ) {
                $addresses = $this->RmCommon->filterEmptyField($property, 'PropertyAddress');
                $location = $this->RmCommon->filterEmptyField($addresses, 'Subarea', 'name');

                if( !empty($location) ) {
                    $customPropertyType = sprintf('%s, %s', $customPropertyType, $location);
                }
            }
        }

        return $customPropertyType;
    }

    function _callMergeSessionAddress ( $data, $dataAddress ) {
        if( !empty($dataAddress['PropertyAddress']) ) {
            $data['PropertyAddress'] = $dataAddress['PropertyAddress'];

            if( !empty($dataAddress['Region']) ) {
                $data['PropertyAddress']['Region'] = $dataAddress['Region'];
            }
            if( !empty($dataAddress['City']) ) {
                $data['PropertyAddress']['City'] = $dataAddress['City'];
            }
            if( !empty($dataAddress['Subarea']) ) {
                $data['PropertyAddress']['Subarea'] = $dataAddress['Subarea'];
            }
        }

        return $data;
    }

    public function getTypeLot($data) {
        $dataAsset = $this->RmCommon->filterEmptyField($data, 'PropertyAsset');
        $is_lot = $this->RmCommon->filterEmptyField($data, 'PropertyType', 'is_lot');
        $is_building = $this->RmCommon->filterEmptyField($data, 'PropertyType', 'is_building');
        $is_space = $this->RmCommon->filterEmptyField($data, 'PropertyType', 'is_space');
        $action_id = $this->RmCommon->filterEmptyField($data, 'Property', 'property_action_id');

        $measure = $this->RmCommon->filterEmptyField($data, 'LotUnit', 'measure');
        $measure = $this->RmCommon->filterEmptyField($dataAsset, 'LotUnit', 'measure', $measure);

        if( !empty($measure) ) {
            if( $is_lot && !$is_building && $action_id == 1 ) {
                return true;
            } else if( $is_space ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    function getPrice( $data, $empty = false, $just_data_array = false, $display_price_sold = true ){
        $for_data_array = array();

        $dataAsset = $this->RmCommon->filterEmptyField($data, 'PropertyAsset');

        $property_action_id = $this->RmCommon->filterEmptyField($data, 'Property', 'property_action_id');
        $price = $this->RmCommon->filterEmptyField($data, 'Property', 'price');
        $sold = $this->RmCommon->filterEmptyField($data, 'Property', 'sold');
        $dataSold = $this->RmCommon->filterEmptyField($data, 'PropertySold');

        if( empty($display_price_sold) && !empty($sold) ) {
            $display = false;
        } else {
            $display = true;
        }

        if( !empty($display) ) {
            $period = $this->RmCommon->filterEmptyField($data, 'Period', 'name');
            $period_id = $this->RmCommon->filterEmptyField($data, 'Period', 'id');
            $currency = $this->RmCommon->filterEmptyField($data, 'Currency', 'symbol');

            $lot_unit = $this->RmCommon->filterEmptyField($data, 'LotUnit', 'slug');
            $lot_unit_id = $this->RmCommon->filterEmptyField($data, 'PropertyAsset', 'lot_unit_id');
            $lot_unit = $this->RmCommon->filterEmptyField($dataAsset, 'LotUnit', 'slug', $lot_unit);
            $lot_unit = ucwords($lot_unit);

            $lot_type = $this->getTypeLot($data);

            if( !empty($sold) && !empty($dataSold) ) {
                $propertySold = $this->RmCommon->filterEmptyField($data, 'PropertySold');
                $price = $this->RmCommon->filterEmptyField($propertySold, 'price_sold');
                $currency = $this->RmCommon->filterEmptyField($propertySold, 'Currency', 'symbol');

                $lot_unit = false;
            } else if( !$lot_type ) {
                $lot_unit = false;
            }

            $for_data_array['price'] = $price;

            if( !empty($price) ) {
                App::uses('NumberHelper', 'View/Helper');
                $NumberHelper = new NumberHelper(new View());

                $price = $NumberHelper->currency($price, $currency.' ', array('places' => 0));

                if( !empty($lot_unit) ) {
                    $price = sprintf('%s / %s', $price, $lot_unit);

                    $for_data_array['lot_unit_id'] = $lot_unit_id;
                }
                if( $property_action_id == 2 && !empty($period) ) {
                    $price = sprintf('%s %s', $price, $period);

                    $for_data_array['period_id'] = $period_id;
                }
            } else if( !empty($empty) ) {
                $price = $empty;
            }

            if($just_data_array == true){
                return $for_data_array;
            }else{
                return $price;
            }
        } else {
            return $empty;
        }
    }

    function _callRentPrice ( $value ) {
        App::uses('NumberHelper', 'View/Helper');
        $NumberHelper = new NumberHelper(new View());
        
        $sold = $this->RmCommon->filterEmptyField($value, 'Property', 'sold');
        $price = $this->RmCommon->filterEmptyField($value, 'Property', 'price');
        $period = $this->RmCommon->filterEmptyField($value, 'Period', 'name');
        $symbol = $this->RmCommon->filterEmptyField($value, 'Currency', 'symbol');
        
        if( empty($display_price_sold) && !empty($sold) ) {
            $display = false;
        } else {
            $display = true;
        }

        if( !empty($display) ) {
            

            $customPrice = $NumberHelper->currency($price, $symbol.' ', array('places' => 0));
            
            return sprintf('%s %s', $customPrice, $period);
        } else {
            return '';
        }
    }

    function _callAssetUtility ( $data ) {
        $data = $this->_callChangeToRequestData( $data, 'PropertyFacility', 'facility_id', true );
        $data = $this->_callChangeToRequestData( $data, 'PropertyPointPlus', 'name' );
        $dataConfig = $this->RmCommon->filterEmptyField( $data, 'PageConfig' );

        $resultFacility = $this->controller->User->Property->PropertyFacility->getDataModel($data);
        $resultPointPlus = $this->controller->User->Property->PropertyPointPlus->getDataModel($data);
        $resultConfig = $this->controller->User->Property->PageConfig->getDataModel($dataConfig, 'property');

        $data = $this->RmCommon->_callUnset(array(
            'PropertyFacility',
            'PropertyPointPlus',
            'PageConfig',
        ), $data);

        if( !empty($resultFacility) ) {
            $data['PropertyFacility'] = $resultFacility;
        }
        if( !empty($resultPointPlus) ) {
            $data['PropertyPointPlus'] = $resultPointPlus;
        }
        if( !empty($resultConfig) ) {
            $data['PageConfig'] = $resultConfig;
        }

        return $data;
    }

    function _callGeneratePhoto ( $params ) {
        $options = array(
            'conditions' => array(),
            'order' => array(
                'Property.id' => 'ASC',
            ),
            'limit' => 1,
        );

        $user_id = $this->RmCommon->filterEmptyField($params, 'named', 'user_id');
        $id = $this->RmCommon->filterEmptyField($params, 'named', 'id');
        $mls_id = $this->RmCommon->filterEmptyField($params, 'named', 'mls_id');
        $parent_id = $this->RmCommon->filterEmptyField($params, 'named', 'parent_id');
        $min_id = $this->RmCommon->filterEmptyField($params, 'named', 'min_id');
        $limit = $this->RmCommon->filterEmptyField($params, 'named', 'limit', 10);

        if( !empty($user_id) ) {
            $options['conditions']['Property.user_id'] = $user_id;
        }
        if( !empty($id) ) {
            $options['conditions']['Property.id'] = $id;
        }
        if( !empty($mls_id) ) {
            $options['conditions']['Property.mls_id'] = $mls_id;
        }
        if( !empty($parent_id) ) {
            $agent_id = $this->User->getAgents( $parent_id, true );
            $options['conditions']['Property.user_id'] = $agent_id;
        }
        if( !empty($min_id) ) {
            $options['conditions']['Property.id >'] = $min_id;
        }
        if( !empty($limit) ) {
            $options['limit'] = $limit;
        }

        $values = $this->controller->User->Property->getData('all', $options, array(
            'status' => 'all',
            'company' => false,
        ));
        $values = $this->controller->User->Property->getDataList($values,array(
            'contain' => array(
                'User',
            )
        ));
        // Configure::write('debug', 2);
        // debug($options);die();

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $property_id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
                $mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');
                $parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

                $medias = $this->controller->User->Property->PropertyMedias->getData('all', array(
                    'conditions' => array(
                        'PropertyMedias.property_id' => $property_id,
                    )
                ), array(
                    'status' => 'all'
                ));

                $dataCompany = $this->controller->User->getDataCompany(false, array(
                    'company_principle_id' => $parent_id,
                ));
                Configure::write('Config.Company.data', $dataCompany);
                $this->controller->data_company = $dataCompany;

                if( !empty($medias) ) {
                    foreach ($medias as $key => $media) {
                        $id = $this->RmCommon->filterEmptyField($media, 'PropertyMedias', 'id');
                        $file_path = $this->RmCommon->filterEmptyField($media, 'PropertyMedias', 'name');
                        $savePath = 'properties';
                        $result = $this->controller->RmImage->restore($file_path, $savePath);
                        $status = $this->RmCommon->filterEmptyField($result, 'status');
                        $message = $this->RmCommon->filterEmptyField($result, 'message');

                        if( !empty($status) ) {
                            printf(__('Berhasil recreate thumbnail #%s - %s #%s<br><br>'), $id, $property_id, $mls_id);
                        } else {
                            printf(__('%s - %s #%s<br><br>'), $message, $property_id, $mls_id);

                        }
                    }
                } else {
                    printf(__('Media tidak ditemukan - %s #%s<br><br>'), $property_id, $mls_id);
                }
            }
        } else {
            printf(__('Property tidak tersedia<br><br>'));
        }
    }

    function convertToDuplicateData($data){
        $PropertyFacility = $this->RmCommon->filterEmptyField($data, 'PropertyFacility');
        $PropertyPointPlus = $this->RmCommon->filterEmptyField($data, 'PropertyPointPlus');
        $PropertyPrice = $this->RmCommon->filterEmptyField($data, 'PropertyPrice');
        $PropertyMedias = $this->RmCommon->filterEmptyField($data, 'PropertyMedias');
        $PropertyVideos = $this->RmCommon->filterEmptyField($data, 'PropertyVideos');

        $session_id = String::uuid();
        $date = date('Y-m-d h:i:s');

        $data['Property']['session_id'] = $session_id;
        $data['Property']['created'] = $data['Property']['modified'] = $date;

        $data = $this->RmCommon->_callUnset(array(
            'Property' => array(
                'id',
                'user_id',
                'mls_id'
            ),
            'PropertyAddress' => array(
                'id',
                'property_id',
                'Region',
                'City',
                'Subarea'
            ),
            'PropertyAsset' => array(
                'id',
                'property_id',
                'LotUnit',
                'ViewSite',
                'PropertyDirection',
                'PropertyCondition'
            ),
            'PropertySold' => array(
                'id',
                'property_id',
            ),
            'PropertyFacility',
            'PropertyPointPlus',
            'PropertyPrice',
        ), $data);

        if(!empty($PropertyFacility)){
            foreach ($PropertyFacility as $key => $value) {
                $val_data = $this->RmCommon->_callUnset(array(
                    'PropertyFacility' => array(
                        'id',
                        'property_id',
                    )
                ), $value);

                $data['PropertyFacility'][$key] = $this->RmCommon->filterEmptyField($val_data, 'PropertyFacility');
            }
        }

        if(!empty($PropertyPointPlus)){
            foreach ($PropertyPointPlus as $key => $value) {
                $val_data = $this->RmCommon->_callUnset(array(
                    'PropertyPointPlus' => array(
                        'id',
                        'property_id',
                    )
                ), $value);

                 $data['PropertyPointPlus'][$key] = $this->RmCommon->filterEmptyField($val_data, 'PropertyPointPlus');
            }
        }

        if(!empty($PropertyPrice)){
            foreach ($PropertyPrice as $key => $value) {
                $val_data = $this->RmCommon->_callUnset(array(
                    'PropertyPrice' => array(
                        'id',
                        'property_id',
                    )
                ), $value);

                 $data['PropertyPrice'][$key] = $this->RmCommon->filterEmptyField($val_data, 'PropertyPrice');
            }
        }

        if(!empty($PropertyMedias)){
            foreach ($PropertyMedias as $key => $value) {
                $val_data = $this->RmCommon->_callUnset(array(
                    'PropertyMedias' => array(
                        'id',
                        'property_id',
                    )
                ), $value);

                $data['PropertyMedias'][$key] = $this->RmCommon->filterEmptyField($val_data, 'PropertyMedias');
                $data['PropertyMedias'][$key]['session_id'] = $session_id;
            }
        }

        if(!empty($PropertyVideos)){
            foreach ($PropertyVideos as $key => $value) {
                $val_data = $this->RmCommon->_callUnset(array(
                    'PropertyVideos' => array(
                        'id',
                        'property_id',
                    )
                ), $value);

                 $data['PropertyVideos'][$key] = $this->RmCommon->filterEmptyField($val_data, 'PropertyVideos');
                 $data['PropertyVideos'][$key]['session_id'] = $session_id;
            }
        }

        return $data;
    }

    function checkMlsID($mls_id, $length, $char){
        $len_mls_id = strlen($mls_id);
        
        if($len_mls_id == $length){
            $substr = substr($mls_id, 0, 1);

            if($substr == $char){
                $mls_id = substr($mls_id, 1);
                return $mls_id;
            }else{
                return $mls_id;
            }
        }else{
            return $mls_id;
        }
    }

    function PropertyExist($datas, $model){ // model for replace property_id
        if(!empty($datas)){
            if(is_array($datas) && !empty($datas[0])){
                foreach($datas AS $key => $data){
                    $mls_id = $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');
                    $mls_id = $this->checkMlsID($mls_id, 9, 'C');
                    $value = $this->controller->User->Property->find('first', array(
                        'conditions' => array(
                            'Property.mls_id' => $mls_id,
                        ),
                    ));

                    if(!empty($value)){
                        $id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
                        $agent_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');
                        $mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');
                        $keyword = $this->RmCommon->filterEmptyField($value, 'Property', 'keyword');
                        
                        $agent = $this->controller->User->getData('first', array(
                            'conditions' => array(
                                'User.id' => $agent_id
                            ),
                        ));

                        $parent_id = $this->RmCommon->filterEmptyField($agent, 'User', 'parent_id');

                        $data['KprBank']['property_id'] = $id;
                        $data['KprBank']['agent_id'] = $agent_id;
                        $data['KprBank']['mls_id'] = $mls_id;
                        $data['KprBank']['keyword'] = $keyword;
                        $data['KprBank']['parent_id'] = $parent_id;
                        $datas[$key] = $data;
                    }else{
                        unset($datas[$key]);
                    }
                }
            }else{
                $mls_id = $this->RmCommon->filterEmptyField($datas, 'Property', 'mls_id');
                $mls_id = $this->checkMlsID($mls_id, 9, 'C');

                $value = $this->controller->User->Property->find('first', array(
                    'conditions' => array(
                        'Property.mls_id' => $mls_id,
                    ),
                ));

                if(!empty($value)){
                    $id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
                    $agent_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');
                    $mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');
                    $keyword = $this->RmCommon->filterEmptyField($value, 'Property', 'keyword');
                    $datas['KprBank']['property_id'] = $id;
                    $datas['KprBank']['agent_id'] = $agent_id;
                    $datas['KprBank']['mls_id'] = $mls_id;
                    $datas['KprBank']['keyword'] = $keyword;
                }else{
                    $datas = false;
                }
            }
            return $datas;
        }else{
            return false;
        }
    }

    function saveApiDataMigrate($data){
        $this->Property = $this->controller->User->Property;

        $email      = $this->RmCommon->filterEmptyField($data, 'User', 'email');
        $group_user = $this->RmCommon->filterEmptyField($data, 'User', 'group_id');
        $mls_id     = $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');

        /*has many*/
        $PropertyFacility   = $this->RmCommon->filterEmptyField($data, 'PropertyFacility');
        $PropertyPointPlus  = $this->RmCommon->filterEmptyField($data, 'PropertyPointPlus');
        $PropertyMedias     = $this->RmCommon->filterEmptyField($data, 'PropertyMedias');
        $PropertyVideos     = $this->RmCommon->filterEmptyField($data, 'PropertyVideos');
        $PropertyPrice      = $this->RmCommon->filterEmptyField($data, 'PropertyPrice');

        if(isset($data['User']['email'])){
            unset($data['User']);
        }

        $exist_data = $this->Property->getData('first', array(
            'conditions' => array(
                'Property.mls_id' => $mls_id
            )
        ), array(
            'status' => 'all',
            'skip_is_sales' => true,
        ));

        $exist_user_data = $this->Property->User->getData('first', array(
            'conditions' => array(
                'User.email' => $email
            )
        ), array(
            'status' => 'all'
        ));

        $str_len = 0;
        if(!empty($mls_id)){
            $str_len = strlen($mls_id);
        }

        if(empty($exist_data) && !empty($exist_user_data['User']['id']) && $str_len < 9 && $group_user == 2){
            $data['Property']['user_id'] = $this->RmCommon->filterEmptyField($exist_user_data, 'User', 'id');
            
            $data = $this->RmCommon->_callUnset(array(
                'Property' => array(
                    'id'
                ),
                'PropertyAddress' => array(
                    'id',
                    'property_id'
                ),
                'PropertyAsset' => array(
                    'id',
                    'property_id'
                ),
                'PropertySold' => array(
                    'id',
                    'property_id'
                )
            ), $data);

            if(!empty($PropertyFacility)){
                unset($data['PropertyFacility']);
                foreach ($PropertyFacility as $key => $value) {
                    $value = $this->RmCommon->_callUnset(array(
                        'PropertyFacility' => array(
                            'id',
                            'property_id'
                        ),
                    ), $value);

                    $data['PropertyFacility'][] = $this->RmCommon->filterEmptyField($value, 'PropertyFacility');
                }
            }

            if(!empty($PropertyPointPlus)){
                unset($data['PropertyPointPlus']);
                foreach ($PropertyPointPlus as $key => $value) {
                    $value = $this->RmCommon->_callUnset(array(
                        'PropertyPointPlus' => array(
                            'id',
                            'property_id'
                        ),
                    ), $value);

                    $data['PropertyPointPlus'][] = $this->RmCommon->filterEmptyField($value, 'PropertyPointPlus');
                }
            }

            if(!empty($PropertyMedias)){
                unset($data['PropertyMedias']);
                foreach ($PropertyMedias as $key => $value) {
                    $value = $this->RmCommon->_callUnset(array(
                        'PropertyMedias' => array(
                            'id',
                            'property_id'
                        ),
                    ), $value);

                    $data['PropertyMedias'][] = $this->RmCommon->filterEmptyField($value, 'PropertyMedias');
                }
            }

            if(!empty($PropertyVideos)){
                unset($data['PropertyVideos']);
                foreach ($PropertyVideos as $key => $value) {
                    $value = $this->RmCommon->_callUnset(array(
                        'PropertyVideos' => array(
                            'id',
                            'property_id'
                        ),
                    ), $value);

                    $data['PropertyVideos'][] = $this->RmCommon->filterEmptyField($value, 'PropertyVideos');
                }
            }

            if(!empty($PropertyPrice)){
                unset($data['PropertyPrice']);
                foreach ($PropertyPrice as $key => $value) {
                    $value = $this->RmCommon->_callUnset(array(
                        'PropertyPrice' => array(
                            'id',
                            'property_id'
                        ),
                    ), $value);

                    $data['PropertyPrice'][] = $this->RmCommon->filterEmptyField($value, 'PropertyPrice');
                }
            }

            $this->Property->removeValidate();
            $this->Property->saveAll($data);
        }
    }

    function _callLotUnitApi ( $lastupdated = false ) {
        $result = array();
        $actions = $this->controller->User->Property->PropertyAction->getData('list', array(
            'cache' => __('PropertyAction.List'),
        ));
        $lotUnits = $this->controller->User->Property->PropertyAsset->LotUnit->getData('all', false, array(
            'lastupdated' => $lastupdated,
        ));
        $lotUnits = $this->controller->User->Property->PropertyAsset->LotUnit->getMergeList($lotUnits, array(
            'contain' => array(
                'PropertyAction' => array(
                    'cache' => true,
                ),
            )
        ));

        if( !empty($lotUnits) ) {
            foreach ($lotUnits as $key => $value) {
                $action_id = $this->RmCommon->filterEmptyField($value, 'LotUnit', 'property_action_id');

                if( !empty($action_id) && !empty($actions[$action_id]) ) {
                    $result[$actions[$action_id]][] = $this->RmCommon->_callUnset(array(
                        'PropertyAction',
                    ), $value);
                } else if( !empty($actions) ) {
                    foreach ($actions as $key => $val) {
                        $result[$val][] = $this->RmCommon->_callUnset(array(
                            'PropertyAction',
                        ), $value);
                    }
                }
            }
        }

        return $result;
    }

    function getStatus ( $data, $model = 'Property' ) {
        $dataCompany = Configure::read('Config.Company.data');
        $is_restrict_approval_property = isset($dataCompany['UserCompanyConfig']['is_restrict_approval_property']) ? $dataCompany['UserCompanyConfig']['is_restrict_approval_property'] : false;

        $id = $this->RmCommon->filterEmptyField($data, $model, 'id');
        $active = $this->RmCommon->filterEmptyField($data, $model, 'active', 0);
        $status = $this->RmCommon->filterEmptyField($data, $model, 'status', 0);
        $sold = $this->RmCommon->filterEmptyField($data, $model, 'sold', 0);
        $published = $this->RmCommon->filterEmptyField($data, $model, 'published', 0);
        $deleted = $this->RmCommon->filterEmptyField($data, $model, 'deleted', 0);
        $in_update = $this->RmCommon->filterEmptyField($data, $model, 'in_update', 0);
        $property_action_id = $this->RmCommon->filterEmptyField($data, $model, 'property_action_id', 0);
        $action_name = $this->RmCommon->filterEmptyField($data, 'PropertyAction', 'inactive_name', 0);

        if( $in_update && $status && !$sold && $published && !$deleted ) {
            $labelStatus = __('Updated');
        } else if( !$active && $status && !$sold && $published && !$deleted && !empty($is_restrict_approval_property) ) {
            $labelStatus = __('Pratinjau');
        } else if( $sold ) {
            if( $property_action_id == 2 ) {
                $labelStatus = __('Tersewa');
            } else {
                $labelStatus = __('Terjual');
            }
        } else if( $status && !$sold && $published && !$deleted ) {
            $labelStatus = __('Tayang');
        } else if( !$status && $published && !$deleted ) {
            $labelStatus = __('Non-Aktif/Rejected');
        } else if( !$published && !$deleted ) {
            $labelStatus = __('Unpublish');
        } else {
            $labelStatus = false;
        }

        return $labelStatus;
    }

    function _callExpiredProperty ( $value ) {
        $change_date = Common::hashEmptyField($value, 'Property.change_date');

        if(!empty($change_date)){
            $change_date = date ("Y-m-d", strtotime("+".Configure::read('__Site.config_expired_listing_in_year')." Year", strtotime($change_date)));
        }

        return $change_date;
    }

	function _callBeforeViewProperties ( $options = array(), $elements = array( 'admin_mine' => true ) ) {
        $isOpenListing  = Common::_callAllowAccess('is_open_listing');
        
        $restActive = $this->Rest->isActive();
		$type_merge = $this->RmCommon->filterEmptyField($options, 'type_merge', false, 'recursive_merge');
		$option_merge = array(
			'limit' => Configure::read('__Site.config_new_table_pagination'),
			'order' => array(
				// 'Property.featured' => 'DESC',
				// 'Property.change_date' => 'DESC',
				'Property.id' => 'DESC',
			),
		);

		if($type_merge == 'recursive_merge'){
			$option_merge = array_merge_recursive($option_merge, $options);
		}else{
			$option_merge = array_merge($option_merge, $options);
		}

        if(isset($options['type_merge'])){
            unset($options['type_merge']);
        }

        $options =  $this->controller->User->Property->_callRefineParams($this->controller->params, $option_merge);
        $params['named'] = $this->RmCommon->filterEmptyField($this->controller->params, 'named');

        // default filter milik saya jika open listing
        if (!empty($isOpenListing)) {
            $status = 'mine';
        } else {
            $status = false;
        }

        $params = $this->RmCommon->defaultSearch($params, array(
            'filter' => 'Property.created-desc',
            'status' => $status,
        ));

        $elements = array_merge($this->RmCommon->_callRefineParams($params), $elements);
        $elements['status'] = $this->RmCommon->filterEmptyField($elements, 'status', false, 'all');
        $other_contain = $this->RmCommon->filterEmptyField($elements, 'other_contain', false, false);
        $contain = $this->RmCommon->filterEmptyField($elements, 'contain_data');

		$authGroupID = Configure::read('User.group_id');
        $status = Common::hashEmptyField($params, 'named.status', 'all');

		if($authGroupID == 1){
		//  personal page
			$elements = array('mine' => true); 
		}
		else{
            $_admin			= Configure::read('User.Admin.Rumahku');
            $companyData    = Configure::read('Config.Company.data');
			$companyID		= Common::hashEmptyField($companyData, 'UserCompany.id');
            $isOpenListing	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_open_listing');
			$isCompanyAgent = Common::validateRole('company_agent', $authGroupID);

			if($isOpenListing){
				$elements = array(
					'mine'			=> $status == 'mine', 
					'parent'		=> true,
					'skip_is_sales'	=> true,
				);
			}
			else{
				$elements = array_merge(array(
					'mine'			=> $isCompanyAgent, 
					'parent'		=> true,
					'admin_mine'	=> !$_admin,
					'skip_is_sales'	=> true,
				), $elements);
			}
		}

    //  https://basecamp.com/1789306/projects/10415456/todos/366370462
    //  hati2 status ada "mine", jadi kalo ada status ini ganti aja jadi all (karena cuma dipake untuk build element di atas)
        $status = $status != 'mine' ? $status : 'all';
		$elements = Hash::insert($elements, 'status', $status);

		$this->controller->paginate = $this->controller->User->Property->getData('paginate', $options, $elements);
        $properties = $this->controller->paginate('Property');

        $this->controller->User->Property->virtualFields = $this->RmCommon->_callUnset(array(
            'price_converter',
            'total_beds',
            'total_baths',
        ), $this->controller->Property->virtualFields);

        if (!$other_contain) {
            $contain = array(
                'MergeDefault',
                'PropertyAddress',
                'PropertyAsset',
                'PropertySold',
                'PropertyNotification',
                'PropertyProductCategory',
                'User',
                'Approved',
                'Client',
                'CoBrokeProperty',
                'UserActivedAgentDetail',
            );
        }

        if (empty($other_contain)) {
            if(!$restActive){
                array_push($contain, 'PropertyMediasCount');
            }
        }

        $properties = $this->controller->User->Property->getDataList($properties, array(
            'contain' => $contain,
        ));        

        if($restActive){
            App::uses('PropertyHelper', 'View/Helper');
            $PropertyHelper = new PropertyHelper(new View());
        }

        if(!empty($properties)){
            foreach ($properties as $key => $value) {
                $value = $this->controller->User->Property->getMergeList($value, array(
                    'contain' => array(
                        'UserCompanyEbrochure', 
                        // 'PropertyView' => array(
                        //     'type' => 'count',
                        //     'order' => false,
                        // ),
                    ),
                ));
                $properties[$key] = $value;
                
                $user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');

                // get properti premium by id agent
                // validate package bundling to show btn premium/unpremium
                $prop_count = $this->controller->Property->_callAgentPropertyCount($user_id, 'active-pending-sold', array(
                    'custom_conditions' => array(
                        'conditions' => array(
                            'Property.featured' => 1,
                        ),
                    ),
                ));
                $properti_premium['PremiumProperty'] = $prop_count;
                $properties[$key]['User'] = array_merge($value['User'], $properti_premium);

                if($restActive){
                    $user_full_name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');

                    $properties[$key]['Property']['expired_date'] = $this->_callExpiredProperty($value);
                    
                    if(empty($user_full_name)){
                        $data_user = $this->controller->User->getData('first', array(
                            'conditions' => array(
                                'User.id' => $user_id
                            )
                        ));
                        
                        $properties[$key]['User'] = $this->RmCommon->filterEmptyField($data_user, 'User');
                    }

                    $properties[$key]['Property']['specifications'] = $PropertyHelper->getSpec($value, array(), false, false);
                }

                if(!empty($value['PropertySold'])){
                    $period_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'period_id');
                    $currency_id = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'currency_id');

                    $value['PropertySold'] = $this->controller->User->Property->Period->getMerge($value['PropertySold'], $period_id);
                    $value['PropertySold'] = $this->controller->User->Property->Currency->getMerge($value['PropertySold'], $currency_id);

                    $properties[$key]['PropertySold'] = $value['PropertySold'];
                }

                $parent_id = Common::hashEmptyField($value, 'User.parent_id');
                $value = $this->controller->User->UserCompanyConfig->getMerge($value, $parent_id);
                $properties[$key]['UserCompanyConfig'] = !empty($value['UserCompanyConfig']) ? $value['UserCompanyConfig'] : array();
            }
        }

        $this->_callSupportAdvancedSearch();

        return $properties;
    }

    function _callRoleCondition ( $value ) {
        $id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
        $group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');
        $parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
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
                
                $this->controller->set('active_menu', 'director');
                break;
            case '3':
                $options = array(
                    'conditions' => array(
                        array(
                            'OR' => array(
                                'User.parent_id' => $id,
                                'Property.user_id' => $id,
                            ),
                        ),
                    ),
                    'contain' => array(
                        'User',
                    ),
                );
                
                $this->controller->set('active_menu', 'principal');
                break;
            default:
                $options = array(
                    'conditions' => array(
                        'User.id' => $id,
                    ),
                    'contain' => array(
                        'User',
                    ),
                );
                $this->controller->set('active_menu', 'agent');
                break;
        }

        return $options;
    }

    function soldByCoBroke($data, $sold_by_coBroke_id){
        $soldBy = $this->controller->User->CoBrokeUser->find('first', array(
            'conditions' => array(
                'CoBrokeUser.id' => $sold_by_coBroke_id,
            ),
            'contain' => array(
                'User'
            )
        ));

        $total_commission               = Common::hashEmptyField($data, 'PropertySold.total_commission');
        $sharingtocompany_percentage    = Common::hashEmptyField($data, 'PropertySold.sharingtocompany_percentage');
        $company_commission             = Common::hashEmptyField($data, 'PropertySold.company_commission');
        $agent_commission_gross         = Common::hashEmptyField($data, 'PropertySold.agent_commission_gross');
        $royalty_percentage             = Common::hashEmptyField($data, 'PropertySold.royalty_percentage');
        $pph_percentage                 = Common::hashEmptyField($data, 'PropertySold.pph_percentage');

        $price_sold                     = Common::hashEmptyField($data, 'PropertySold.price_sold');

        $pengurangan_komisi_agen = 0;
        $is_bt_commision = Common::hashEmptyField($data, 'PropertySold.is_bt_commision');

        $bt_commission = 0;

        if($is_bt_commision){
            $bt_commission_percentage   = Common::hashEmptyField($data, 'PropertySold.bt_commission_percentage');
            $bt_type_commission         = Common::hashEmptyField($data, 'PropertySold.bt_type_commission');
            $company_commission         = Common::hashEmptyField($data, 'PropertySold.company_commission');

            if($bt_type_commission == 'in_corp'){
                $data['PropertySold']['bt_commission'] = ($price_sold * $bt_commission_percentage) / 100;
            }else if($bt_type_commission == 'out_corp'){
                $data['PropertySold']['bt_commission'] = $bt_commission = ($agent_commission_gross * $bt_commission_percentage) / 100;

                $pengurangan_komisi_agen += $bt_commission;

                $agent_commission_gross = $agent_commission_gross - $bt_commission;
            }
        }

        if( !empty($soldBy) ){
            $type_commission    = Common::hashEmptyField($soldBy, 'CoBrokeUser.final_type_commission');
            $final_commission   = Common::hashEmptyField($soldBy, 'CoBrokeUser.final_commission', 0);
            $final_type_price_commission = Common::hashEmptyField($soldBy, 'CoBrokeUser.final_type_price_commission', 'percentage');

            if($type_commission == 'in_corp'){
                if($final_type_price_commission == 'percentage'){
                    $broker_commission = ($price_sold * $final_commission) / 100;
                    $data['PropertySold']['broker_percentage'] = $final_commission;
                }else{
                    $broker_commission = $final_commission;
                    $data['PropertySold']['broker_percentage'] = 0;
                }

                $data['PropertySold']['broker_commission']      = $broker_commission;
                $data['PropertySold']['broker_type_commision']  = $type_commission;
            }else if($type_commission == 'out_corp'){
                if($final_type_price_commission == 'percentage'){
                    $broker_commission = ($agent_commission_gross * $final_commission) / 100;
                    $data['PropertySold']['broker_percentage'] = $final_commission;
                }else{
                    $broker_commission = $final_commission;
                    $data['PropertySold']['broker_percentage'] = 0;
                }

                $data['PropertySold']['broker_commission'] = $broker_commission;
                $data['PropertySold']['broker_type_commision'] = $type_commission;

                $pengurangan_komisi_agen += $broker_commission;

                $agent_commission_gross = $agent_commission_gross - $broker_commission;
            }

            $data['PropertySold']['broker_type_price_commission'] = $final_type_price_commission;

            // $royalty = ($agent_commission_gross * $royalty_percentage ) / 100;
            // $temp_commision = $agent_commission_gross = $agent_commission_gross - $royalty;
            // $pph = ( $agent_commission_gross * $pph_percentage ) / 100;
            
            // $agent_commission_net = $agent_commission_gross - $pph;

            // $data['PropertySold']['agent_commission_net'] = $agent_commission_net;   
        }

        return $data;
    }

    function availableKpr($value){
        $kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
        $property_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_id');
        
        $property = $this->controller->User->Property->getData('first', array(
            'conditions' => array(
                'Property.id' => $property_id,
            ),
        ));
        $on_progress_kpr = $this->RmCommon->filterEmptyField($property, 'Property', 'on_progress_kpr');

        if(($kpr_id == $on_progress_kpr) || empty($on_progress_kpr)){
            return true;
        }else{
            return false;
        }
    }

    function rePropertyRevision($data, $property_id, $step){
        $is_admin = Configure::read('User.admin');
        $approval = Configure::read('Config.Approval.Property');

        $this->Property = ClassRegistry::init('Property');

        if( empty($is_admin) && !empty($approval) && !empty($data) ) {
            $collect_model = array_keys($data);

            if(isset($collect_model['Property'])){
                unset($collect_model['Property']);
            }

            $contain = !empty($collect_model) ? $collect_model : false;

            $property = $this->Property->getData('first', array(
                'conditions' => array(
                    'Property.id' => $property_id
                ),
            ));

            if(!empty($contain)){
                $property = $this->Property->getMergeList($property, array(
                    'contain' => $contain
                ));
            }

            if(!empty($property)){
                $revision_data = $this->_callSetDataRevision($property_id, $data, $property, $step);

                if(!empty($revision_data)){

                    foreach ($revision_data as $key => $value) {
                        $property_id = $this->RmCommon->filterEmptyField($value, 'property_id');
                        $model = $this->RmCommon->filterEmptyField($value, 'model');
                        $field = $this->RmCommon->filterEmptyField($value, 'field');

                        $this->Property->PropertyRevision->deleteAll(array(
                            'PropertyRevision.property_id' => $property_id,
                            'PropertyRevision.model' => $model,
                            'PropertyRevision.field' => $field,
                        ), true, true);
                    }

                    if($this->Property->PropertyRevision->saveMany($revision_data)){
                        $this->Property->inUpdateChange($property_id);
                    }
                }
            }
        }else if($is_admin){
            $this->Property->id = $property_id;

            $this->Property->set($data);
            
            $this->Property->save($data);
        }
    }

    function BeforeSavePropertyView($data, $modelName = 'PropertyView'){
        if(!empty($data)){
            $this->Property = ClassRegistry::init('Property');
            // get property_id in data property
            $mls_id = $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');
            $mls_id = $this->checkMlsID($mls_id, 9, 'C');

            $property  = $this->Property->getData('first', array(
                'conditions' => array(
                    'Property.mls_id' => $mls_id,
                ),
            ));
            $property_id = $this->RmCommon->filterEmptyField($property, 'Property', 'id');

            if($property_id){
                $data[$modelName]['property_id'] = $property_id; 
                $data['Property']['mls_id'] = $mls_id; 
            }
            // 

            //  get client in db prime
            $email = $this->RmCommon->filterEmptyField($data, 'Client', 'email');

            if($email){
                $client = $this->Property->User->getData('first', array(
                    'conditions' => array(
                        'User.email' => trim($email),
                    ),
                ));

                if(!empty($client)){
                    $user_id = $this->RmCommon->filterEmptyField($client, 'User', 'id');
                }else{
                    $dataSave['User'] = $this->RmCommon->filterEmptyField($data, 'Client');
                    $dataSave['User']['active'] = TRUE;
                    $dataSave = $this->RmCommon->dataConverter($dataSave, array(
                        'unset' => array(
                            'User' => array(
                                'id',
                            ),
                        ),
                    ));
                    $this->Property->User->removeValidator();
                    if($this->Property->User->saveAll($dataSave)){
                        $user_id = $this->Property->User->id;
                    }
                }
                $data[$modelName]['user_id'] = $user_id;
            }

            $data = $this->RmCommon->dataConverter($data, array(
                'unset' => array(
                    $modelName => array(
                        'id',
                    ),
                ),
            ));
                
            if(!empty($data['SettingMediaPartner'])){
                $data[$modelName]['instanace'] = $this->RmCommon->filterEmptyField($data, 'SettingMediaPartner', 'name_theme');
            }else{
                $data[$modelName]['instanace'] = 'rumahku';
            }
        }
        return $data;
    }

    function arebiFormat($data){
        if(!empty($data)){
            App::uses('HtmlHelper', 'View/Helper');
            App::uses('RumahkuHelper', 'View/Helper');
            App::uses('PropertyHelper', 'View/Helper');
            $HtmlHelper = new HtmlHelper(new View());
            $RumahkuHelper = new RumahkuHelper(new View());
            $PropertyHelper = new PropertyHelper(new View());

            $temp = array();
            foreach ($data as $key => $value) {
                $PropertyAddress = $this->RmCommon->filterEmptyField($value, 'PropertyAddress');
                $city = $this->RmCommon->filterEmptyField($PropertyAddress, 'City', 'name');
                $subarea = $this->RmCommon->filterEmptyField($PropertyAddress, 'Subarea', 'name');
                $zip = $this->RmCommon->filterEmptyField($PropertyAddress, 'zip');
                $title = $this->RmCommon->filterEmptyField($value, 'Property', 'title', false, array(
                    'urldecode' => false,
                ));
                $mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');
                $photo = $this->RmCommon->filterEmptyField($value, 'Property', 'photo');

                $ParentInfo = $this->RmCommon->filterEmptyField($value, 'ParentInfo');

                $principle_name = $this->RmCommon->filterEmptyField($ParentInfo, 'UserCompany', 'name');
                
                $logo_path = Configure::read('__Site.logo_photo_folder');
                $logo = $this->RmCommon->filterEmptyField($ParentInfo, 'UserCompany', 'logo');

                $customLogo = $RumahkuHelper->photo_thumbnail(array(
                    'save_path' => $logo_path, 
                    'src'=> $logo, 
                    'size' => 'xxsm',
                    'url' => true
                ));

                $domain = $this->RmCommon->filterEmptyField($ParentInfo, 'UserCompanyConfig', 'domain');

                $label = $PropertyHelper->getNameCustom($value);

                $slug = $RumahkuHelper->toSlug($label);
                $url = $HtmlHelper->url(array(
                    'controller'=> 'properties', 
                    'action' => 'detail',
                    'mlsid' => $mls_id,
                    'slug'=> $slug, 
                    'admin'=> false,
                ));

                $photoProperty = $RumahkuHelper->photo_thumbnail(array(
                    'save_path' => Configure::read('__Site.property_photo_folder'), 
                    'src'=> $photo, 
                    'size' => 'm',
                    'url' => true
                ), array(
                    'alt' => $title,
                    'title' => $title,
                    'class' => 'default-thumbnail',
                ));

                $location = sprintf('%s, %s %s', $subarea, $city, $zip);
                $price = $this->getPrice($value);
                $specifications = $PropertyHelper->getSpec($value, array(), false, false);
                $principle_logo = $domain.$customLogo;
                $photo_property = $domain.$photoProperty;
                $url_detail = $domain.$url;

                $temp[] = array(
                    'location' => $location,
                    'title' => $title,
                    'price' => $price,
                    'specifications' => $specifications,
                    'principle_logo' => $principle_logo,
                    'principle_name' => $principle_name,
                    'photo_property' => $photo_property,
                    'url_detail' => $url_detail
                );
            }

            $data = $temp;
        }

        return $data;
    }

    function _setBtCommission($data){
        $price_sold = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'price_sold');
        $temp_commision = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'temp_commision');
        $is_bt_commision = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'is_bt_commision');

        if(!empty($is_bt_commision)){
            $bt_commission_percentage = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'bt_commission_percentage');
            $bt_type_commission = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'bt_type_commission');
            $company_commission = $this->RmCommon->filterEmptyField($data, 'PropertySold', 'company_commission');

            if($bt_type_commission == 'in_corp'){
                $data['PropertySold']['bt_commission'] = ($price_sold * $bt_commission_percentage) / 100;
            }else if($bt_type_commission == 'out_corp'){
                $data['PropertySold']['temp_commision'] = $data['PropertySold']['bt_commission'] = ($temp_commision * $bt_commission_percentage) / 100;
            }
        }

        return $data;
    }

    // check and get the data membership agent
    function checkAgentMembership( $data = false ) {
        $result = false;
        $is_admin = Configure::read('User.admin');

        // if log in as admin check from this data
        if (!empty($data) && $is_admin) {
            $agent_id = Common::hashEmptyField($data, 'Property.user_id');
            
            $data_user = $this->controller->User->getData('first', array(
                'conditions' => array(
                    'User.id' => $agent_id,
                ),
            ));

            $package_id = Common::hashEmptyField($data_user, 'User.membership_package_id');
            $user_id    = Common::hashEmptyField($data_user, 'User.id');

            if (!empty($package_id)) {
                $path_link = sprintf('api/memberships/list_package/get_package:1/package_id:%s/status:all', $package_id);

                $opsi_link = array(
                    'validate_as_admin' => true,
                    'custom_link'       => true,
                    'user_own'          => true,
                    'path_link'         => $path_link,
                    'cache_page'        =>  sprintf('properties_admin_index_%s', $user_id),
                );

                $result = array();
            }
            
        }
        
        return $result;
    }

    function getLocationName( $data, $divider = ',' ) {
        $dataAddress = !empty($data['PropertyAddress'])?$data['PropertyAddress']:false;

        $subarea = $this->RmCommon->filterEmptyField($dataAddress, 'Subarea', 'name');
        $city = $this->RmCommon->filterEmptyField($dataAddress, 'City', 'name');
        $region = $this->RmCommon->filterEmptyField($dataAddress, 'Region', 'name');
        $zip = $this->RmCommon->filterEmptyField($dataAddress, 'zip');

        if( !empty($subarea) && !empty($city) ) {
            $location = sprintf('%s%s %s', $subarea, $divider, $city);
        } else {
            $location = '';
        }

        if( !empty($region) ) {
            $location = sprintf('%s, %s %s', $location, $region, $zip);
        } else {
            $location = sprintf('%s %s', $location, $zip);
        }

        return $location;
    }

	public function callBeforeViewEasyMode($record = array(), $mergeDefault = true){
	//	MASTER DATA ====================================================================

		$propertyModel		= $this->controller->Property;
		$propertyActions	= $propertyModel->PropertyAction->getData('all', array('cache' => 'PropertyAction.Data'));
		$propertyTypes		= $propertyModel->PropertyType->getData('all', array('cache' => 'PropertyType.Data'));
		$periods			= $propertyModel->PropertyPrice->Period->getData('all', array('cache' => 'Period.Data'));
		$currencies			= $propertyModel->Currency->getData('all', array('cache' => 'Currency.Data'));
		$facilities			= $propertyModel->PropertyFacility->Facility->getData('all', array('cache' => 'Facility.Data'));

		$cityID		= Common::hashEmptyField($record, 'PropertyAddress.city_id', null);
		$subareas	= array();

		if($cityID) {
			$subareas = $propertyModel->PropertyAddress->Subarea->getSubareas('list', false, $cityID);
		}

		$this->controller->set(compact(
			'propertyActions', 'propertyTypes', 
            'periods', 'currencies', 'facilities', 'subareas'
		));

	//	================================================================================

	//	MERGE ADDITIONAL RECORD RELATION ===============================================

		$record = (array) $record;

		if($record){
            $record = $propertyModel->getDataList($record, array(
                'contain' => array(
                    'MergeDefault',
                ),
            ));

			if($mergeDefault){
				$record = $propertyModel->getDataList($record, array(
					'contain' => array(
						'PropertyAddress',
						'PropertyAsset',
						'PropertyProductCategory',
						'Approved',
						'Client',
						'UserActivedAgentDetail',
					),
				));
			}

			$recordID	= Common::hashEmptyField($record, 'Property.id');
			$sessionID	= Common::hashEmptyField($record, 'Property.session_id');
			$userID		= Common::hashEmptyField($record, 'Property.user_id', null);
			$agentEmail = Common::hashEmptyField($record, 'Property.agent_email');
			$typeID		= Common::hashEmptyField($record, 'Property.property_type_id', null);
			$actionID	= Common::hashEmptyField($record, 'Property.property_action_id', null);
            $isLot      = Common::hashEmptyField($record, 'PropertyType.is_lot');
            $isSpace    = Common::hashEmptyField($record, 'PropertyType.is_space');

			if(empty($userID) && $agentEmail){
				$userID = $this->controller->User->field('User.id', array('User.email' => $agentEmail));
				$record	= Hash::insert($record, 'Property.user_id', $userID);
			}

			$record	= $propertyModel->getDataList($record, array('contain' => array('User')));
			$record	= $this->controller->User->getDataList($record, array('contain' => array('UserProfile')));
			$record = array_merge($record, array(
				'Agent'			=> Common::hashEmptyField($record, 'User'), 
				'AgentProfile'	=> Common::hashEmptyField($record, 'UserProfile'), 
			));

            if( !empty($typeID) ) {
                $certificates = $propertyModel->Certificate->getData('all', false, array('property_type_id' => $typeID));
                $viewSites = $propertyModel->PropertyAsset->ViewSite->getData('all', array(
                    'conditions' => array('ViewSite.property_type_id' => $typeID),
                ));
            } else {
                $certificates = false;
                $viewSites = false;
            }

            if($isSpace){
                $lotUnits = $propertyModel->PropertyAsset->LotUnit->getData('all', false, array(
                    'property_action_id'    => $actionID,
                    'is_space'              => $isSpace,
                ));
            }
            else{
                $lotUnits = $propertyModel->PropertyAsset->LotUnit->getData('all', array(
                    'fields'    => array('LotUnit.id', 'LotUnit.slug'),
                    'group'     => array('LotUnit.slug'),
                ), array(
                    'is_lot' => $isLot,
                ));
            }

			Configure::write('__Site.CategoryMedias.Data', $propertyModel->PropertyMedias->CategoryMedias->getData('list'));

            $regionName     = Common::hashEmptyField($record, 'Region.name');
            $cityName       = Common::hashEmptyField($record, 'City.name');
            $subareaName    = Common::hashEmptyField($record, 'Subarea.name');

            $regionName     = Common::hashEmptyField($record, 'PropertyAddress.Region.name', $regionName);
            $cityName       = Common::hashEmptyField($record, 'PropertyAddress.City.name', $cityName);
            $subareaName    = Common::hashEmptyField($record, 'PropertyAddress.Subarea.name', $subareaName);
            $locationName   = array_filter(array($subareaName, $cityName, $regionName));
            $locationName   = implode(', ', $locationName);

            $record = Hash::insert($record, 'PropertyAddress.location_name', $locationName);

			$this->controller->set(array(
				'certificates'		=> $certificates, 
				'viewSites'			=> $viewSites, 
                'lotUnits'          => $lotUnits, 
			));

			$this->RmCommon->_layout_file('fileupload');
			return $record;
		}

	//	================================================================================
	}

	public function callBeforeSaveEasyMode($data, $property = array()){
		$data		= (array) $data; 
		$property	= (array) $property;

		if($data){
            $data_company = $this->controller->data_company;

            $companyID      = Common::hashEmptyField($data_company, 'UserCompany.id', null);
            $isActiveRest   = $this->controller->Rest->isActive();
            $propertyModel  = $this->controller->Property;

            // initialize data co broke
            $default_agent_commission = Common::hashEmptyField($data_company, 'UserCompanyConfig.default_agent_commission', 0);
            $default_co_broke_commision = Common::hashEmptyField($data_company, 'UserCompanyConfig.default_co_broke_commision', 0);
            $default_type_co_broke = Common::hashEmptyField($data_company, 'UserCompanyConfig.default_type_co_broke', 'both');
            $default_type_co_broke = Common::hashEmptyField($data_company, 'UserCompanyConfig.default_type_co_broke', 'both');
            $default_type_price_co_broke_commision = Common::hashEmptyField($data_company, 'UserCompanyConfig.default_type_price_co_broke_commision', 'percentage');
            $default_type_co_broke_commission = Common::hashEmptyField($data_company, 'UserCompanyConfig.default_type_co_broke_commission', 'in_corp');

		//	khusus region city subarea di handle Editable
			$editables	= Common::hashEmptyField($data, 'Editable', array());
			$data		= Hash::remove($data, 'Editable');

			if($editables){
				foreach($editables as $editableModel => $editableFields){
					$dataFields = Common::hashEmptyField($data, $editableModel, array());
					$dataFields = array_merge($dataFields, $editableFields);

				//	append ke data
					$data = Hash::insert($data, $editableModel, $dataFields);
				}
			}

			$data = Hash::insert($data, 'Property.company_id', $companyID);
			$data = $this->RmCommon->dataConverter($data, array(
				'price' => array(
					'Property' => array(
						'price',
						'co_broke_commision', 
					),
				),
				'date' => array(
					'Property' => array(
						'contract_date',
					),
				), 
			));

			$data = $this->_callDataClient($data, $property);

		//	record data
			$recordID	= Common::hashEmptyField($property, 'Property.id');
			$sessionID	= Common::hashEmptyField($property, 'Property.session_id');
			$userID		= Common::hashEmptyField($property, 'Property.user_id', null);
			$agentEmail	= Common::hashEmptyField($property, 'Property.agent_email');
			$actionID	= Common::hashEmptyField($property, 'Property.property_action_id', null);
			$typeID		= Common::hashEmptyField($property, 'Property.property_type_id', null);
			$currencyID	= Common::hashEmptyField($property, 'Property.currency_id', null);
			$price		= Common::hashEmptyField($property, 'Property.price', 0);
			$mlsID		= Common::hashEmptyField($property, 'Property.mls_id');
			$title		= Common::hashEmptyField($property, 'Property.title');
			$regionID	= Common::hashEmptyField($property, 'PropertyAddress.region_id', null);
			$cityID		= Common::hashEmptyField($property, 'PropertyAddress.city_id', null);
			$subareaID	= Common::hashEmptyField($property, 'PropertyAddress.subarea_name');
			$zip		= Common::hashEmptyField($property, 'PropertyAddress.zip');
			$no			= Common::hashEmptyField($property, 'PropertyAddress.no');
			$rt			= Common::hashEmptyField($property, 'PropertyAddress.rt');
			$rw			= Common::hashEmptyField($property, 'PropertyAddress.rw');

		//	post data
			$recordID	= Common::hashEmptyField($data, 'Property.id', $recordID);
			$sessionID	= Common::hashEmptyField($data, 'Property.session_id', $sessionID);
			$userID		= Common::hashEmptyField($data, 'Property.user_id', $userID);
			$agentEmail	= Common::hashEmptyField($data, 'Property.agent_email', $agentEmail);
			$actionID	= Common::hashEmptyField($data, 'Property.property_action_id', $actionID);
			$typeID		= Common::hashEmptyField($data, 'Property.property_type_id', $typeID);
			$currencyID	= Common::hashEmptyField($data, 'Property.currency_id', $currencyID);
			$price		= Common::hashEmptyField($data, 'Property.price', $price);
			$mlsID		= Common::hashEmptyField($data, 'Property.mls_id', $mlsID);
			$title		= Common::hashEmptyField($data, 'Property.title', $title);
			$regionID	= Common::hashEmptyField($data, 'PropertyAddress.region_id', $regionID);
			$cityID		= Common::hashEmptyField($data, 'PropertyAddress.city_id', $cityID);
			$subareaID	= Common::hashEmptyField($data, 'PropertyAddress.subarea_id', $subareaID);
			$zip		= Common::hashEmptyField($data, 'PropertyAddress.zip', $zip);
			$no			= Common::hashEmptyField($data, 'PropertyAddress.no', $no);
			$rt			= Common::hashEmptyField($data, 'PropertyAddress.rt', $rt);
            $rw         = Common::hashEmptyField($data, 'PropertyAddress.rw', $rw);
            $commission = Common::hashEmptyField($data, 'Property.commission', $default_agent_commission);
            $co_broke_commision = Common::hashEmptyField($data, 'Property.co_broke_commision', $default_co_broke_commision);
            $co_broke_type = Common::hashEmptyField($data, 'Property.co_broke_type', $default_type_co_broke);
            $type_price_co_broke_commision = Common::hashEmptyField($data, 'Property.type_price_co_broke_commision', $default_type_price_co_broke_commision);
			$type_co_broke_commission = Common::hashEmptyField($data, 'Property.type_co_broke_commission', $default_type_co_broke_commission);

            $cobroke_note = Common::hashEmptyField($data, 'Property.cobroke_note');

		//	additional post data
			$facilityID		= Common::hashEmptyField($data, 'PropertyFacility.facility_id', array());
			$pointPlusNames	= Common::hashEmptyField($data, 'PropertyPointPlus.name', array());

			$facilityID		= array_filter($facilityID);
			$pointPlusNames	= array_filter($pointPlusNames);

            $data = Hash::insert($data, 'Property.commission', $commission);
            $data = Hash::insert($data, 'Property.co_broke_commision', $co_broke_commision);
            $data = Hash::insert($data, 'Property.co_broke_type', $co_broke_type);
            $data = Hash::insert($data, 'Property.type_price_co_broke_commision', $type_price_co_broke_commision);
            $data = Hash::insert($data, 'Property.type_co_broke_commission', $type_co_broke_commission);
            $data = Hash::insert($data, 'Property.cobroke_note', $cobroke_note);

			if($recordID){
				$data = Hash::insert($data, 'Property.id', $recordID);
			}

			if($sessionID){
				$data = Hash::insert($data, 'Property.session_id', $sessionID);
			}

			if(empty($userID) && $agentEmail){
				$userAgent = $this->controller->User->find('first', array(
                    'conditions' => array(
                        'User.email' => $agentEmail,
                    ),
                    'fields' => array(
                        'User.id',
                        'User.code',
                    ),
                ));

                $userID = Common::hashEmptyField($userAgent, 'User.id');
                $userCode = Common::hashEmptyField($userAgent, 'User.code');
			} else {
                $userCode = false;
            }

			$data = Hash::insert($data, 'Property.user_id', $userID);

			if(empty($mlsID)){
				$code	= $this->RmCommon->createRandomNumber(3, 'bcdfghjklmnprstvwxyz0123456789', 30);
				$mlsID	= $this->controller->Property->generateMLSID($code, $userCode);
				$data	= Hash::insert($data, 'Property.mls_id', $mlsID);
			}

			if($currencyID){
				$property = Hash::remove($property, 'Currency');
				$property = $propertyModel->Currency->getMerge($property, $currencyID, 'Currency.id', array(
					'cache' => array(
						'name' => __('Currency.%s', $currencyID),
					),
				));

				$data = Hash::insert($data, 'Property.currency_id', $currencyID);
			}

			if($actionID){
				$property = Hash::remove($property, 'PropertyAction');
				$property = $propertyModel->PropertyAction->getMerge($property, $actionID, 'PropertyAction.id', array(
					'cache'	=> array(
						'name' => __('PropertyAction.%s', $actionID),
					),
				));

				$data = Hash::insert($data, 'Property.property_action_id', $actionID);
			}

			if($typeID){
				$property = Hash::remove($property, 'PropertyType');
				$property = $propertyModel->PropertyType->getMerge($property, $typeID, 'PropertyType.id', array(
					'cache' => array(
						'name' => __('PropertyType.%s', $typeID),
					),
				));

				$data = Hash::insert($data, 'Property.property_type_id', $typeID);
			}

			if($facilityID){
				$data = Hash::insert($data, 'PropertyFacility.facility_id', $facilityID);
			}

			if($pointPlusNames){
				$data = Hash::insert($data, 'PropertyPointPlus.name', $pointPlusNames);
			}

			$data = Hash::insert($data, 'Property.price_measure', $this->getMeasurePrice($property, $price));

			$propertyAssets = Common::hashEmptyField($data, 'PropertyAsset', array());

			if($propertyAssets){
				$property	= Hash::insert($property, 'PropertyAsset', $propertyAssets);
				$lotUnitID	= Common::hashEmptyField($data, 'PropertyAsset.lot_unit_id');

				if($lotUnitID){
					$property = Hash::remove($property, 'LotUnit');
					$property = $propertyModel->PropertyAsset->LotUnit->getMerge($property, $lotUnitID, 'LotUnit', false, array(
						'cache' => array(
							'name' => __('LotUnit.%s', $lotUnitID),
						),
					));
				}
			}

			$propertyPrices = Common::hashEmptyField($data, 'PropertyPrice', array());

			if($propertyPrices){
				$data = $this->_callProcessPricePeriod($data);
			}

			$locationData = $propertyModel->PropertyAddress->Region->getMerge(array(), $regionID);
			$locationData = $propertyModel->PropertyAddress->City->getMerge($locationData, $cityID);
			$locationData = $propertyModel->PropertyAddress->Subarea->getMerge($locationData, $subareaID);

			$data = Hash::insert($data, 'PropertyAddress.region_id', $regionID);
			$data = Hash::insert($data, 'PropertyAddress.city_id', $cityID);
			$data = Hash::insert($data, 'PropertyAddress.subarea_id', $subareaID);

		//	GENERATE KEYWORD ======================================================================================

			$actionName	= Common::hashEmptyField($property, 'PropertyAction.name');
			$typeName	= Common::hashEmptyField($property, 'PropertyType.name');

			$regionName		= Common::hashEmptyField($locationData, 'Region.name');
			$cityName		= Common::hashEmptyField($locationData, 'City.name');
			$subareaName	= Common::hashEmptyField($locationData, 'Subarea.name');

			$subTitle = array($typeName, $actionName);
			$subTitle = trim(implode(' ', array_filter($subTitle)));
			$keywords = array(
				'title'		=> array($title, $subTitle), 
				'location'	=> array($subareaName, $cityName, $regionName, $zip), 
			);

			if($no){ $keywords['location'][] = sprintf('No %s', $no); }
			if($rt){ $keywords['location'][] = sprintf('RT %s', $rt); }
			if($rw){ $keywords['location'][] = sprintf('RW %s', $rw); }

			foreach($keywords as &$keyword){
				$keyword = trim(implode(', ', array_filter($keyword)));
			}

			$keywords	= trim(implode(' di ', array_filter($keywords)));
			$title		= $title ?: $keywords;

		//	=======================================================================================================

			$data = Hash::insert($data, 'Property.title', $title);
			$data = Hash::insert($data, 'Property.keyword', $keywords);
		}

		return $data;
	}
}
?>