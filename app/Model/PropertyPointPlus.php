<?php
class PropertyPointPlus extends AppModel {
	var $name = 'PropertyPointPlus';
	var $displayField = 'name';
	var $validate = array(
		'property_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Properti harap dipilih'
			),
		),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nilai lebih harap dipilih'
			),
		),
	);

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
                  'PropertyPointPlus.id',
                  'PropertyPointPlus.name',
                );
            }
        }

        return $options;
    }

    function getDataModel ( $data ) {
        $dataSave = array();

        if( !empty($data['PropertyPointPlus']['name']) ) {
            $values = array_filter($data['PropertyPointPlus']['name']);

            foreach ($values as $key => $value) {
                $dataSave[]['PropertyPointPlus'] = array(
                    'name' => $value,
                );
            }
        }

        return $dataSave;
    }

    function getRequestData ( $data, $property_id ) {
        $dataPoinPlus = $this->getMerge(array(), $property_id);
        $requestData = array();
        $rest_api = Configure::read('Rest.token');

        if( !empty($rest_api) ) {
            $requestData = $dataPoinPlus;
        } else if( !empty($dataPoinPlus) ) {
            $dataPoinPlus = !empty($dataPoinPlus['PropertyPointPlus'])?$dataPoinPlus['PropertyPointPlus']:false;

            foreach ($dataPoinPlus as $value) {
                $name = !empty($value['PropertyPointPlus']['name'])?$value['PropertyPointPlus']['name']:false;

                $requestData['PropertyPointPlus']['name'][] = $name;
            }
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function doSave( $datas, $value = false, $id = false, $property_id, $is_validate = false ) {
        $result = false;

        if( !empty($property_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'PropertyPointPlus.property_id' => $property_id,
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
                    $data['PropertyPointPlus']['property_id'] = $property_id;
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
                            'msg' => __('Gagal menambahkan nilai lebih'),
                            'status' => 'error',
                        );
                    }
                } else {
                    $result = array(
                        'msg' => __('Gagal menambahkan nilai lebih'),
                        'status' => 'error',
                    );
                }
            }

            if( empty($result) ) {
                $result = array(
                    'msg' => __('Berhasil menambahkan nilai lebih'),
                    'status' => 'success',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function getMerge ( $data, $id ) {
        if( empty($data['PropertyPointPlus']) && !empty($id) ) {
            $value = $this->getData('all', array(
                'conditions' => array(
                    'PropertyPointPlus.property_id' => $id,
                ),
            ));

            if( !empty($value) ) {
                $data['PropertyPointPlus'] = $value;
            }
        }

        return $data;
    }
}
?>