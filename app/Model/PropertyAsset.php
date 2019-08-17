<?php
class PropertyAsset extends AppModel {
	var $name = 'PropertyAsset';

    var $validate = array(
		'lot_size' => array(
            'validLotSize' => array(
                'rule' => array('validLotSize'),
                'message' => 'Mohon masukkan jumlah luas tanah',
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Luas tanah harus berupa angka',
            ),
		),
		'building_size' => array(
            'validBuildingSize' => array(
                'rule' => array('validBuildingSize', 'building_size'),
                'message' => 'Mohon masukkan jumlah luas bangunan',
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Luas bangunan harus berupa angka',
            ),
		),
		'beds' => array(
            'validateRooms' => array(
                'rule' => array('validateRooms', 'beds'),
                'message' => 'Mohon masukkan jumlah kamar tidur',
            ),
			'numeric' => array(
				'rule' => array('numeric'),
                'allowEmpty' => true,
				'message' => 'Mohon masukkan angka untuk jumlah kamar tidur',
			),
		),
		'baths' => array(
            'validateRooms' => array(
                'rule' => array('validateRooms', 'baths'),
                'message' => 'Mohon masukkan jumlah kamar mandi',
            ),
			'numeric' => array(
				'rule' => array('numeric'),
                'allowEmpty' => true,
				'message' => 'Mohon masukkan angka untuk jumlah kamar mandi',
			),
		),
		'beds_maid' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => true,
				'message' => 'Mohon masukkan angka untuk jumlah kamar tidur extra',
			),
		),
		'baths_maid' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => true,
				'message' => 'Mohon masukkan angka untuk jumlah kamar mandi extra',
			),
		),
		'level' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => true,
				'message' => 'Mohon masukkan angka untuk jumlah lantai',
			),
		),
		'electricity' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty' => true,
				'message' => 'Mohon masukkan angka untuk jumlah daya listrik',
			),
		),
        'lot_unit_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon masukkan satuan unit properti Anda',
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Mohon masukkan satuan unit properti Anda',
            ),
        ),
        'lot_width' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Mohon masukkan lebar tanah properti Anda dengan angka',
            ),
        ),
        'lot_length' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Mohon masukkan panjang tanah properti Anda dengan angka',
            ),
        ),
        'cars' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Mohon masukkan angka untuk garasi',
            ),
        ),
        'carports' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Mohon masukkan angka untuk carport',
            ),
        ),
        'phoneline' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'allowEmpty' => true,
                'message' => 'Mohon masukkan angka untuk jumlah line telepon',
            ),
        ),
    );

	var $belongsTo = array(
        'Electricity' => array(
            'className' => 'Electricity',
            'foreignKey' => 'electricity',
        ),
		'PropertyDirection' => array(
			'className' => 'PropertyDirection',
			'foreignKey' => 'property_direction_id',
		),
		'PropertyCondition' => array(
			'className' => 'PropertyCondition',
			'foreignKey' => 'property_condition_id',
		),
        'Property' => array(
            'className' => 'Property',
            'foreignKey' => 'property_id',
        ),
        'LotUnit' => array(
            'className' => 'LotUnit',
            'foreignKey' => 'lot_unit_id',
        ),
        'ViewSite' => array(
            'className' => 'ViewSite',
            'foreignKey' => 'view_site_id',
        ),
	);
    
    function validateRooms($data, $field){
        $property_data = array();
        if(!empty($this->Property->data)){
            $property_data = $this->Property->data;
        }

        $property_type_id = $this->filterEmptyField($property_data, 'Property', 'property_type_id');
        $property_type_id = $this->filterEmptyField($property_data, 'PropertyType', 'id', $property_type_id);
        $property_type_id = $this->filterEmptyField($this->data, 'PropertyType', 'id', $property_type_id);

        $is_residence = $this->filterEmptyField($property_data, 'PropertyType', 'is_residence');
        $is_residence = $this->filterEmptyField($this->data, 'PropertyType', 'is_residence', $is_residence);

        if($property_type_id){
            $is_residence = $this->Property->PropertyType->field('PropertyType.is_residence', array(
                'PropertyType.id' => $property_type_id, 
            ));
        }

        $data_field = $this->filterEmptyField($property_data, 'PropertyAsset', $field);
        $data_field = $this->filterEmptyField($data, $field, false, $data_field);

        if ( $property_type_id == 21 ) {
            return true;
        } else {
            if( !empty($is_residence) ){
                if(!empty($data_field) && is_numeric($data_field) && $data_field >= 1){
                    return true;
                }else{
                    return false;
                }
            }else{
                return true;
            }
        }
    }

    function validLotSize( $data ) {
        $property_data = array();
        if(!empty($this->Property->data)){
            $property_data = $this->Property->data;
        }

        $property_type_id = $this->filterEmptyField($property_data, 'Property', 'property_type_id');
        $property_type_id = $this->filterEmptyField($property_data, 'PropertyType', 'id', $property_type_id);
        $property_type_id = $this->filterEmptyField($this->data, 'PropertyType', 'id', $property_type_id);

        $is_lot = $this->filterEmptyField($property_data, 'PropertyType', 'is_lot');
        $is_lot = $this->filterEmptyField($this->data, 'PropertyType', 'is_lot', $is_lot);

        if($property_type_id){
            $is_lot = $this->Property->PropertyType->field('PropertyType.is_lot', array(
                'PropertyType.id' => $property_type_id, 
            ));
        }

        $lot_size = $this->filterEmptyField($property_data, 'PropertyAsset', 'lot_size');
        $lot_size = $this->filterEmptyField($data, 'lot_size', false, $lot_size);

        if( !empty($is_lot) ){
            if( empty($lot_size) ) {
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    function validBuildingSize( $data ) {
        $property_data = array();
        if(!empty($this->Property->data)){
            $property_data = $this->Property->data;
        }

        $property_type_id = $this->filterEmptyField($property_data, 'Property', 'property_type_id');
        $property_type_id = $this->filterEmptyField($property_data, 'PropertyType', 'id', $property_type_id);
        $property_type_id = $this->filterEmptyField($this->data, 'PropertyType', 'id', $property_type_id);

        $is_building = $this->filterEmptyField($property_data, 'PropertyType', 'is_building');
        $is_building = $this->filterEmptyField($this->data, 'PropertyType', 'is_building', $is_building);

        $building_size = $this->filterEmptyField($property_data, 'PropertyAsset', 'building_size');
        $building_size = $this->filterEmptyField($data, 'building_size', false, $building_size);

        if($property_type_id){
            $is_building = $this->Property->PropertyType->field('PropertyType.is_building', array(
                'PropertyType.id' => $property_type_id, 
            ));
        }

        if ( $property_type_id == 21 ) {
            return true;
        } else {
            if( !empty($is_building) ){
                if( empty($building_size) ) {
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }

    function validateLotUnit () {
        $data = $this->data;

        $property_data = array();
        if(!empty($this->Property->data)){
            $property_data = $this->Property->data;
        }

        $is_space = $this->filterEmptyField($property_data, 'PropertyType', 'is_space');
        $is_space = $this->filterEmptyField($data, 'PropertyType', 'is_space', $is_space);

        $lot_unit_id = $this->filterEmptyField($property_data, 'PropertyAsset', 'lot_unit_id');
        $lot_unit_id = $this->filterEmptyField($data, 'PropertyAsset', 'lot_unit_id', $lot_unit_id);

        if( !empty($is_space) && empty($lot_unit_id) ) {
            return false;
        } else {
            return true;
        }
    }

    function doSave( $data, $value = false, $validate = false, $property_id = false, $id = false, $save_session = true ) {
        $result = false;

        if ( !empty($data) ) {
            if( empty($validate) ) {
            	if( !empty($id) ) {
                    $this->id = $id;
                } else {
                    $this->create();

                    if( !empty($property_id) ) {
                        $data['PropertyAsset']['property_id'] = $property_id;
                    }
                }
            }

            if(isset($data['Property']['co_broke_commision'])){
                $co_broke_commision = $this->filterEmptyField($data, 'Property', 'co_broke_commision');
            }

            // Utk Check Validasi berdasarkan tipe Properti
            if( !empty($value['PropertyType']) ) {
                $data['PropertyType'] = $value['PropertyType'];
            }

            $dataFacility = $this->Property->PropertyFacility->getDataModel($data);
            $dataPoinPlus = $this->Property->PropertyPointPlus->getDataModel($data);
            
            if( isset($data['PropertyAsset']['year_built']) ) {
                $data['PropertyAsset']['year_built'] = Common::hashEmptyField($data, 'PropertyAsset.year_built');
            }

            $this->set($data);
            $assetValidate = $this->validates();

            if( !empty($property_id) ) {
            	$this->Property->id = $property_id;
            } else {
            	$this->Property->create();
            }

            $this->Property->set($data);
            
            $propertyValidate = $this->Property->validates();

            $facilityValidates = $this->Property->PropertyFacility->doSave($dataFacility, false, false, false, true);
            $statusFacility = !empty($facilityValidates['status'])?$facilityValidates['status']:false;

            $pointPlusValidates = $this->Property->PropertyPointPlus->doSave($dataPoinPlus, false, false, false, true);
            $statusPointPlus = !empty($pointPlusValidates['status'])?$pointPlusValidates['status']:false;

            $priceValidates = $this->Property->PropertyPrice->doSave($data, false, false, false, true);
            $statusPrice = !empty($priceValidates['status'])?$priceValidates['status']:false;

            if( $assetValidate && $propertyValidate && $statusFacility != 'error' && $statusPrice != 'error' ) {
                $flagSave = true;

                if( !empty($validate) ) {
                    if( !empty($save_session) ) {
                        $sessionName = Configure::read('__Site.Property.SessionName');
                        CakeSession::write(sprintf($sessionName, 'Asset'), $data);
                    }
                } else {
                    $flagSave = $this->save();
                    $id = $this->id;

                    if( !empty($flagSave) ) {
                    	$this->Property->save();
                        $property_id = $this->Property->id;
                        $this->Property->PropertyFacility->doSave($dataFacility, false, false, $property_id);
                        $this->Property->PropertyPointPlus->doSave($dataPoinPlus, false, false, $property_id);
                        $this->Property->PropertyPrice->doSave($data, false, false, $property_id);

                        if(!empty($property_id) && isset($data['Property']['co_broke_commision'])){
                            $this->Property->CoBrokeProperty->deleteCoBroke($property_id, $co_broke_commision);
                        }

                        if(!empty($data['Property']['is_cobroke'])){
                            $this->Property->CoBrokeProperty->doCoBroke($property_id, 'active');
                        }else if(isset($data['Property']['is_cobroke']) && empty($data['Property']['is_cobroke'])){
                            $this->Property->CoBrokeProperty->CoBrokeChangeStatus($property_id, $data);
                        }
                    }
                }

                if( !empty($flagSave) ) {
                    $msg = __('Berhasil menyimpan informasi spesifikasi properti Anda #%s', $id);
                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                    );

                    if( empty($validate) ) {
                        $result['Log'] = array(
                            'activity' => $msg,
                            'document_id' => $property_id,
                        );
                    }
                } else {
                    $msg = __('Gagal menyimpan informasi spesifikasi properti Anda, mohon lengkapi semua data yang diperlukan');
                    $result = array(
                    	'msg' => __('Gagal menyimpan informasi spesifikasi properti Anda, mohon lengkapi semua data yang diperlukan'),
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'document_id' => $property_id,
                            'error' => 1,
                        ),
                    );
                }

                /*merubah status co broke*/
                if(!empty($property_id)){
                    $this->Property->CoBrokeProperty->CoBrokeChangeStatus($property_id, $data);
                }
            } else {
                if( $statusPrice == 'error' && !empty($priceValidates['msg']) ) {
                    $msg = $priceValidates['msg'];
                } else {
                    $msg = __('Gagal menyimpan informasi spesifikasi properti Anda, mohon lengkapi semua data yang diperlukan');
                }

                $validationErrors = array();

                if(!empty($this->validationErrors)){
                    $validationErrors = array_merge($validationErrors, $this->validationErrors);
                }

                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'validationErrors' => $validationErrors
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function getData( $find, $options = false, $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order' => array(),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($options) && $is_merge ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if( isset($options['contain']) && empty($options['contain']) ) {
                $default_options['contain'] = false;
            } else if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }
        
        $default_options = $this->_callFieldForAPI($find, $default_options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
				  'PropertyAsset.id',
				  'PropertyAsset.property_id',
				  'PropertyAsset.property_direction_id',
				  'PropertyAsset.property_condition_id',
				  'PropertyAsset.lot_unit_id',
				  'PropertyAsset.view_site_id',
				  'PropertyAsset.building_size',
				  'PropertyAsset.lot_size',
				  'PropertyAsset.lot_width',
				  'PropertyAsset.lot_length',
				  'PropertyAsset.lot_dimension',
				  'PropertyAsset.beds',
				  'PropertyAsset.beds_maid',
				  'PropertyAsset.baths',
				  'PropertyAsset.baths_maid',
				  'PropertyAsset.cars',
				  'PropertyAsset.carports',
				  'PropertyAsset.electricity',
				  'PropertyAsset.phoneline',
				  'PropertyAsset.furnished',
				  'PropertyAsset.level',
				  'PropertyAsset.year_built',
				  'PropertyAsset.property_view',
                );
            }
        }

        return $options;
    }

    function getMerge ( $data, $property_id, $with_contain = true ) {
        if( empty($data['PropertyAsset']) && !empty($property_id) ) {
            $propertyAsset = $this->getData('first', array(
                'conditions' => array(
                    'PropertyAsset.property_id' => $property_id,
                ),
            ));

            if( !empty($propertyAsset) ) {
                $data = array_merge($data, $propertyAsset);
            }
        }

        if( !empty($data['PropertyAsset']) ) {
            if( !empty($with_contain) ) {
                $electricity_id = !empty($data['PropertyAsset']['electricity'])?$data['PropertyAsset']['electricity']:false;
                $direction_id = !empty($data['PropertyAsset']['property_direction_id'])?$data['PropertyAsset']['property_direction_id']:false;
                $condition_id = !empty($data['PropertyAsset']['property_condition_id'])?$data['PropertyAsset']['property_condition_id']:false;
                $lot_unit_id = !empty($data['PropertyAsset']['lot_unit_id'])?$data['PropertyAsset']['lot_unit_id']:false;
                $view_site_id = !empty($data['PropertyAsset']['view_site_id'])?$data['PropertyAsset']['view_site_id']:false;
                $property_type_id = !empty($data['Property']['property_type_id'])?$data['Property']['property_type_id']:false;

                if( !empty($electricity_id) && empty($data['PropertyAsset']['Electricity']) ) {
                    $electricity = $this->Electricity->getData('first', array(
                        'conditions' => array(
                            'Electricity.id' => $electricity_id,
                        ),
                    ));

                    if( !empty($electricity) ) {
                        $data['PropertyAsset'] = array_merge($data['PropertyAsset'], $electricity);
                    }
                }

                if( !empty($direction_id) && empty($data['PropertyAsset']['PropertyDirection']) ) {
                    $direction = $this->PropertyDirection->getData('first', array(
                        'conditions' => array(
                            'PropertyDirection.id' => $direction_id,
                        ),
                    ));

                    if( !empty($direction) ) {
                        $data['PropertyAsset'] = array_merge($data['PropertyAsset'], $direction);
                    }
                }

                if( !empty($condition_id) && empty($data['PropertyAsset']['PropertyCondition']) ) {
                    $condition = $this->PropertyCondition->getData('first', array(
                        'conditions' => array(
                            'PropertyCondition.id' => $condition_id,
                        ),
                    ));

                    if( !empty($condition) ) {
                        $data['PropertyAsset'] = array_merge($data['PropertyAsset'], $condition);
                    }
                }

                if( !empty($lot_unit_id) && empty($data['PropertyAsset']['LotUnit']) ) {
                    $lotUnit = $this->LotUnit->getData('first', array(
                        'conditions' => array(
                            'LotUnit.id' => $lot_unit_id,
                        ),
                        'cache' => __('LotUnit.%s', $lot_unit_id),
                    ));

                    if( !empty($lotUnit) ) {
                        $data['PropertyAsset'] = array_merge($data['PropertyAsset'], $lotUnit);
                    }
                }

                if( !empty($view_site_id) && empty($data['PropertyAsset']['ViewSite']) ) {
                    $data = $this->ViewSite->getMerge($data, $view_site_id, $property_type_id, 'PropertyAsset');
                }
            }
        }

        return $data;
    }
}
?>