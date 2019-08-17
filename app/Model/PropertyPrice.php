<?php
class PropertyPrice extends AppModel {
	var $name = 'PropertyPrice';

    var $belongsTo = array(
        'Period' => array(
            'className' => 'Period',
            'foreignKey' => 'period_id',
        ),
        'Currency' => array(
            'className' => 'Currency',
            'foreignKey' => 'currency_id',
        ),
    );

    var $validate = array(
        'currency_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Currency harap dipilih',
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Currency harap dipilih',
            ),
        ),
        'period_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Periode harga harap dipilih',
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Periode harga harap dipilih',
            ),
        ),
        'price' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Harga sewa harap diisi',
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Harga sewa harus berupa angka',
            ),
            'notNumber' => array(
                'rule' => array('isNumber', 'price'),
                'message' => 'Mohon masukkan harga properti lebih besar dari 0',
            ),
        ),
    );

	function getData( $find='all', $options = array() ){
		$default_options = array(
			'conditions'=> array(
                'PropertyPrice.status' => 1,
            ),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(
            	'PropertyPrice.id' => 'ASC',
        	),
		);

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
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
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
                  'PropertyPrice.id',
                  'PropertyPrice.property_id',
                  'PropertyPrice.currency_id',
                  'PropertyPrice.period_id',
                  'PropertyPrice.price',
                  'PropertyPrice.price_measure',
                );
            }
        }

        return $options;
    }

    function getMerge ( $data, $id = false, $contain = true ) {
        if( empty($data['PropertyPrice']) && !empty($id) ) {
            $values = $this->getData('all', array(
                'conditions' => array(
                    'PropertyPrice.property_id' => $id,
                ),
            ));

            if( !empty($values) ) {
                if( !empty($contain) ) {
                    foreach ($values as $key => $val) {
                        $currency_id = !empty($val['PropertyPrice']['currency_id'])?$val['PropertyPrice']['currency_id']:false;
                        $period_id = !empty($val['PropertyPrice']['period_id'])?$val['PropertyPrice']['period_id']:false;

                        $val = $this->Currency->getMerge($val, $currency_id, 'Currency.id', array(
                            'cache' => array(
                                'name' => __('Currency.%s', $currency_id),
                            ),
                        ));
                        $val = $this->Period->getMerge($val, $period_id);
                        $values[$key] = $val;
                    }
                }

                $data['PropertyPrice'] = $values;
            }
        }

        return $data;
    }

    function getRequestDataApi ( $values, $data ) {
        $requestData = array();

        if( !empty($values) ) {
            $requestData['PropertyPrice'] = $values;
        } else {
            $requestData['PropertyPrice']['property_id'] = !empty($data['Property']['id'])?$data['Property']['id']:false;
            $requestData['PropertyPrice']['currency_id'] = !empty($data['Property']['currency_id'])?$data['Property']['currency_id']:false;
            $requestData['PropertyPrice']['period_id'] = !empty($data['Property']['period_id'])?$data['Property']['period_id']:false;
            $requestData['PropertyPrice']['price'] = !empty($data['Property']['price'])?$data['Property']['price']:false;
            $requestData['PropertyPrice']['price_measure'] = !empty($data['Property']['price_measure'])?$data['Property']['price_measure']:false;

            $requestData['Currency'] = !empty($data['Currency'])?$data['Currency']:false;
            $requestData['Period'] = !empty($data['Period'])?$data['Period']:false;

            $requestData['PropertyPrice'] = array(
                $requestData,
            );
        }

        return $requestData;
    }

    function getRequestData ( $data, $property_id ) {
        if( !empty($data['PropertyPrice']) ) {
            $values = $data['PropertyPrice'];
        } else {
            $values = $this->getMerge(array(), $property_id);
            $values = !empty($values['PropertyPrice'])?$values['PropertyPrice']:false;
        }

        $requestData = array();
        $rest_api = Configure::read('Rest.token');

        if( !empty($rest_api) ) {
            $requestData = $this->getRequestDataApi($values, $data);
        } else if( !empty($values) ) {
            if( !empty($values) ) {
                foreach ($values as $key => $value) {
                    if( !empty($value['PropertyPrice']) ) {
                        $dataTemp = $value['PropertyPrice'];
                    } else {
                        $dataTemp = $value;
                    }

                    $currency_id = !empty($dataTemp['currency_id'])?$dataTemp['currency_id']:false;
                    $period_id = !empty($dataTemp['period_id'])?$dataTemp['period_id']:false;
                    $price = !empty($dataTemp['price'])?$dataTemp['price']:false;

                    $requestData['PropertyPrice']['currency_id'][$key] = $currency_id;
                    $requestData['PropertyPrice']['period_id'][$key] = $period_id;
                    $requestData['PropertyPrice']['price'][$key] = $price;
                }
            }
        } else if( !empty($data['Property']['price']) ) {
            $requestData['PropertyPrice']['currency_id'][0] = !empty($data['Property']['currency_id'])?$data['Property']['currency_id']:false;
            $requestData['PropertyPrice']['period_id'][0] = !empty($data['Property']['period_id'])?$data['Property']['period_id']:false;
            $requestData['PropertyPrice']['price'][0] = !empty($data['Property']['price'])?$data['Property']['price']:false;
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function doSave( $datas, $value = false, $id = false, $property_id, $is_validate = false ) {
        $result = false;

        if( !empty($property_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'PropertyPrice.property_id' => $property_id,
            ));
        }

        if ( !empty($datas['PropertyPrice']) ) {
            foreach ($datas['PropertyPrice'] as $key => $value) {
                if( !empty($value['PropertyPrice']) ) {
                    $data['PropertyPrice'] = $value['PropertyPrice'];
                } else {
                    $data['PropertyPrice'] = $value;
                }

                if( empty($id) ) {
                    $this->create();
                } else {
                    $this->id = $id;
                }

                if( !empty($property_id) ) {
                    $data['PropertyPrice']['property_id'] = $property_id;
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
                            'msg' => __('Mohon lengkapi harga properti'),
                            'status' => 'error',
                        );
                    }
                } else {
                    $result = array(
                        'msg' => __('Mohon lengkapi harga properti'),
                        'status' => 'error',
                    );
                }
            }
        }

        if( empty($result) ) {
            $result = array(
                'msg' => __('Berhasil menambahkan harga properti'),
                'status' => 'success',
            );
        }

        return $result;
    }

    function generatePrice($data){
        if(!empty($data['PropertyPrice']['currency_id'])){
            $request_data = array();

            foreach ($data['PropertyPrice']['currency_id'] as $key => $value) {
                $request_data[$key]['PropertyPrice']['currency'] = $value;
                $request_data[$key]['PropertyPrice']['price'] = !empty($data['PropertyPrice']['price'][$key])?$data['PropertyPrice']['price'][$key]:'';
                $request_data[$key]['PropertyPrice']['period_id'] = !empty($data['PropertyPrice']['period_id'][$key])?$data['PropertyPrice']['period_id'][$key]:'';
                
                $Currency = $this->Currency->getData('first', array(
                    'conditions' => array(
                        'Currency.id' => $value
                    ),
                    'cache' => __('Currency.%s', $value),
                ));

                $Period = $this->Period->getData('first', array(
                    'conditions' => array(
                        'Period.id' => $data['PropertyPrice']['period_id'][$key]
                    ),
                    'cache' => __('Period.%s', $data['PropertyPrice']['period_id'][$key]),
                ));

                $request_data[$key]['Currency']['alias'] = !empty($Currency['Currency']['alias']) ? $Currency['Currency']['alias'] : '';
                $request_data[$key]['Period']['name'] = !empty($Period['Period']['name']) ? $Period['Period']['name'] : '';
            }

            $data['PropertyPrice'] = $request_data;
        }

        return $data;
    }
}
?>