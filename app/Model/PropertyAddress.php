<?php
class PropertyAddress extends AppModel {
	var $name = 'PropertyAddress';
	var $displayField = 'address';

	var $validate = array(
		'property_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon lengkapi info dasar properti Anda',
			),
		),
		'address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan alamat properti Anda',
			),
		),
		'country_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih negara lokasi properti Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih negara lokasi properti Anda',
			),
		),
		'location_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih lokasi sesuai dengan daftar area yang muncul pada panel inputan. Apabila pilihan area tidak muncul silakan refresh halaman Properti Anda.',
			),
		),
		'region_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih provinsi lokasi properti Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih provinsi lokasi properti Anda',
			),
		),
		'city_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih kota lokasi properti Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih kota lokasi properti Anda',
			),
		),
		'subarea_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih area properti Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih area properti Anda',
			),
		),
		'zip' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan kode pos properti Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Format kode pos tidak valid, mohon hanya memasukkan angka',
			),
		),
		'no' => array(
			'validateNo' => array(
				'rule' => array('validateNo'),
				'message' => 'Mohon masukkan no alamat properti Anda',
			),
		),
	);

	var $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
		),
		'Subarea' => array(
			'className' => 'Subarea',
			'foreignKey' => 'subarea_id',
		)
	);

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
				  'PropertyAddress.id',
				  'PropertyAddress.property_id',
				  'PropertyAddress.country_id',
				  'PropertyAddress.region_id',
				  'PropertyAddress.city_id',
				  'PropertyAddress.subarea_id',
				  'PropertyAddress.address',
				  'PropertyAddress.address2',
				  'PropertyAddress.zip',
				  'PropertyAddress.no',
				  'PropertyAddress.rw',
				  'PropertyAddress.rt',
				  'PropertyAddress.location',
				  'PropertyAddress.latitude',
				  'PropertyAddress.longitude',
				  'PropertyAddress.hide_address',
				  'PropertyAddress.hide_map',
                );
            }
        }

        return $options;
    }

    function doAddress( $data, $value = false, $validate = false, $property_id = false, $id = false, $save_session = true ) {
        $result = false;

        if ( !empty($data) ) {
        	$propertyValidate = true;
			$dataCompany = Configure::read('Config.Company.data');
        	$defaultHideAddress = !empty($dataCompany['UserCompanyConfig']['is_hidden_address_property'])?$dataCompany['UserCompanyConfig']['is_hidden_address_property']:false;

        	if( !empty($defaultHideAddress) ) {
        		$data['PropertyAddress']['hide_address'] = 1;
        	}

            if( empty($validate) ) {
                if( !empty($id) ) {
                    $this->id = $id;
                } else {
                    $this->create();
                }
            }

            if( !empty($data['Property']) ) {
            	$dataProperty['Property'] = $data['Property'];

            	if( !empty($property_id) ) {
	            	$this->Property->id = $property_id;
	            } else {
	            	$this->Property->create();
	            }

			//	remove validasi price kalo ga diset.
				if(Hash::check($dataProperty, 'Property.price') === false){
					$this->Property->validator()->remove('price');
				}

	            $this->Property->set($dataProperty);
	            $propertyValidate = $this->Property->validates();
            }
            
            $this->set($data);

            if( $this->validates() && !empty($propertyValidate) ) {
                $flagSave = true;

                if( !empty($validate) ) {
                    if( !empty($save_session) ) {
	                	$sessionName = Configure::read('__Site.Property.SessionName');
	                    	
	                	if( !empty($dataProperty['Property']) ) {
		                    $dataBasic = CakeSession::read(sprintf($sessionName, 'Basic'));

		                    if( !empty($dataBasic['Property']) ) {
		                    	$dataBasic['Property'] = array_merge($dataBasic['Property'], $dataProperty['Property']);
		                    } else {
		                    	$dataBasic['Property'] = $dataProperty['Property'];
		                    }
	                    	CakeSession::write(sprintf($sessionName, 'Basic'), $dataBasic);
		                }

	                    CakeSession::write(sprintf($sessionName, 'Address'), $data);
	                }
                } else {
                    $flagSave = $this->save();
                    $id = $this->id;

                	if( !empty($flagSave) && !empty($dataProperty) ) {
                		$this->Property->save();
                	}
                }

                if( !empty($flagSave) ) {
                    $msg = __('Berhasil menyimpan informasi alamat properti Anda #%s', $id);
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
                    $msg = __('Gagal menyimpan informasi alamat properti Anda, mohon lengkapi semua data yang diperlukan');
                    $result = array(
                    	'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'document_id' => $property_id,
                            'error' => 1,
                        ),
                    );
                }
            } else {
            	$validationErrors = array();

                if(!empty($this->validationErrors)){
                    $validationErrors = array_merge($validationErrors, $this->validationErrors);
                }

                $result = array(
                    'msg' => __('Gagal menyimpan informasi alamat properti Anda, mohon lengkapi semua data yang diperlukan'),
                    'status' => 'error',
                    'validationErrors' => $validationErrors
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function getMerge ( $data, $property_id, $with_contain = true ) {
		if( empty($data['PropertyAddress']) && !empty($property_id) ) {
			$propertyAddress = $this->getData('first', array(
				'conditions' => array(
					'PropertyAddress.property_id' => $property_id,
				),
			));
			
			if( !empty($propertyAddress) ) {
				$data = array_merge($data, $propertyAddress);
			}
		}

		if( !empty($with_contain) ){
			if( !empty($data['PropertyAddress']['region_id']) ) {
				$region_id = $data['PropertyAddress']['region_id'];

				$region = $this->Region->getData('first', array(
					'conditions' => array(
						'Region.id' => $region_id,
					),
                	'cache' => __('Region.%s', $region_id),
				));

				if( !empty($region) ) {
					$data['PropertyAddress'] = array_merge($data['PropertyAddress'], $region);
				}
			}

			if( !empty($data['PropertyAddress']['city_id']) ) {
				$city_id = $data['PropertyAddress']['city_id'];

				$city = $this->City->getData('first', array(
					'conditions' => array(
						'City.id' => $city_id,
					),
                	'cache' => __('City.%s', $city_id),
				));

				if( !empty($city) ) {
					$data['PropertyAddress'] = array_merge($data['PropertyAddress'], $city);
				}
			}

			if( !empty($data['PropertyAddress']['subarea_id']) ) {
				$subarea_id = $data['PropertyAddress']['subarea_id'];

				$subarea = $this->Subarea->getData('first', array(
					'conditions' => array(
						'Subarea.id' => $subarea_id,
					),
					'contain' => false,
					'cache' => __('Subarea.%s', $subarea_id),
					'cacheConfig' => 'subareas',
				));

				if( !empty($subarea) ) {
					$data['PropertyAddress'] = array_merge($data['PropertyAddress'], $subarea);
				}
			}

			if($data){
				$regionName		= Common::hashEmptyField($data, 'PropertyAddress.Region.name');
				$cityName		= Common::hashEmptyField($data, 'PropertyAddress.City.name');
				$subareaName	= Common::hashEmptyField($data, 'PropertyAddress.Subarea.name');
				$locationName	= array_filter(array($subareaName, $cityName, $regionName));

			 	$data = Hash::insert($data, 'PropertyAddress.location_name', implode(', ', $locationName));
			}
		}
		
		return $data;
	}

    function validateNo ( $data ) {
        $dataCompany = Configure::read('Config.Company.data');
        $is_mandatory_no_address = !empty($dataCompany['UserCompanyConfig']['is_mandatory_no_address'])?$dataCompany['UserCompanyConfig']['is_mandatory_no_address']:false;

        if( !empty($is_mandatory_no_address) && empty($this->data['PropertyAddress']['no']) ) {
            return false;
        } else {
            return true;
        }
    }

    function _callBeforeSave ( $data ) {
        $region_id = !empty($data['PropertyAddress']['region_id'])?$data['PropertyAddress']['region_id']:false;
        $city_id = !empty($data['PropertyAddress']['city_id'])?$data['PropertyAddress']['city_id']:false;
        $subarea_id = !empty($data['PropertyAddress']['subarea_id'])?$data['PropertyAddress']['subarea_id']:false;

        $data = $this->Region->getMerge($data, $region_id, 'Region', array(
			'cache' => array(
				'name' => __('Region.%s', $region_id),
			),
		));
        $data = $this->City->getMerge($data, $city_id, 'City', 'City.id', array(
			'cache' => __('City.%s', $city_id),
		));
        $data = $this->Subarea->getMerge($data, $subarea_id, 'Subarea', 'Subarea.id', array(
			'cache' => __('Subarea.%s', $subarea_id),
			'cacheConfig' => 'subareas',
		));

        if( !empty($data) ) {
			$regionName		= Common::hashEmptyField($data, 'Region.name');
			$cityName		= Common::hashEmptyField($data, 'City.name');
			$subareaName	= Common::hashEmptyField($data, 'Subarea.name');
			$locationName	= array_filter(array($subareaName, $cityName, $regionName));

		 	$data = Hash::insert($data, 'PropertyAddress.location_name', implode(', ', $locationName));
		 }

        return $data;
    }
}
?>