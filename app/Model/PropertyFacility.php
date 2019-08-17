<?php
class PropertyFacility extends AppModel {
	var $name = 'PropertyFacility';
	var $displayField = 'name';
	var $validate = array(
		'property_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Properti harap dipilih'
			),
		),
		'facility_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Fasilitas harap dipilih'
			),
            'validateOther' => array(
                'rule' => array('validateOther'),
                'message' => 'Fasilitas lainnya harap diisi'
            ),
		),
	);

    var $belongsTo = array(
        'Facility' => array(
            'className' => 'Facility',
            'foreignKey' => 'facility_id',
        ),
    );

    function validateOther () {
        $id = !empty($this->data['PropertyFacility']['facility_id'])?$this->data['PropertyFacility']['facility_id']:false;
        $other_text = !empty($this->data['PropertyFacility']['other_text'])?trim($this->data['PropertyFacility']['other_text']):false;

        if( $id == -1 && empty($other_text) ) {
            return false;
        } else {
            return true;
        };
    }

    function getData( $find, $options = false, $elements = array() ){
        $default_options = array(
            'conditions'=> array(),
            'order' => array(),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($options) ){
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
                  'PropertyFacility.id',
                  'PropertyFacility.property_id',
                  'PropertyFacility.facility_id',
                  'PropertyFacility.other_text',
                );
            }
        }

        return $options;
    }

    function getDataModel ( $data ) {
        $dataSave = array();

        if( !empty($data['PropertyFacility']['facility_id']) ) {
            $values = array_filter($data['PropertyFacility']['facility_id']);

            foreach ($values as $key => $value) {
                $dataSave[]['PropertyFacility'] = array(
                    'facility_id' => trim($value),
                );
            }
        }
        
        if( !empty($data['PropertyFacility']['other_id']) ) {
            $text = !empty($data['PropertyFacility']['other_text'])?$data['PropertyFacility']['other_text']:false;

            $dataSave[]['PropertyFacility'] = array(
                'facility_id' => -1,
                'other_text' => $text,
            );
        }

        return $dataSave;
    }

    function getRequestData ( $data, $property_id ) {
        $dataFacility = $this->getMerge(array(), $property_id);

    	$requestData = array();
        $rest_api = Configure::read('Rest.token');

        if( !empty($rest_api) ) {
            $requestData = $dataFacility;
        } else if( !empty($dataFacility) ) {
            $dataFacility = !empty($dataFacility['PropertyFacility'])?$dataFacility['PropertyFacility']:false;

            foreach ($dataFacility as $key => $value) {
                $id = !empty($value['PropertyFacility']['facility_id'])?$value['PropertyFacility']['facility_id']:false;
                $other_text = !empty($value['PropertyFacility']['other_text'])?$value['PropertyFacility']['other_text']:false;

                if( $id == -1 ) {
                    $requestData['PropertyFacility']['other_id'] = true;
                    $requestData['PropertyFacility']['other_text'] = $other_text;
                } else {
                    $requestData['PropertyFacility']['facility_id'][$id] = true;
                }
            }
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function doSave( $datas, $value = false, $id = false, $property_id, $is_validate = false ) {
        $result = false;

        if( !empty($property_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'PropertyFacility.property_id' => $property_id,
            ));
        }

        if ( !empty($datas) ) {
            foreach ($datas as $key => $data) {
                if( empty($id) ) {
                    $this->create();
                } else {
                    $this->id = $id;
                }

                if( !empty($property_id) ) {
                    $data['PropertyFacility']['property_id'] = $property_id;
                }

                $this->set($data);

                if( $this->validates() ) {
                    if( $is_validate ) {
                        $flagSave = true;
                    } else {
                        $flagSave = $this->save($data);
                    }

                    if( !$flagSave ) {
                        $result = array(
                            'msg' => __('Gagal menambahkan fasilitas properti'),
                            'status' => 'error',
                        );
                    }
                } else {
                    $result = array(
                        'msg' => __('Gagal menambahkan fasilitas properti'),
                        'status' => 'error',
                    );
                }
            }

            if( empty($result) ) {
                $result = array(
                    'msg' => __('Berhasil menambahkan fasilitas properti'),
                    'status' => 'success',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function getMerge ( $data, $id ) {
        if( empty($data['PropertyFacility']) && !empty($id) ) {
            $value = $this->getData('all', array(
                'conditions' => array(
                    'PropertyFacility.property_id' => $id,
                ),
            ));

            if( !empty($value) ) {
                foreach ($value as $key => $val) {
                    $facility_id = !empty($val['PropertyFacility']['facility_id'])?$val['PropertyFacility']['facility_id']:false;
                    $value[$key] = $this->Facility->getMerge($val, $facility_id);
                }

                $data['PropertyFacility'] = $value;
            }
        }

        return $data;
    }
}
?>