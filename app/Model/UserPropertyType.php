<?php
class UserPropertyType extends AppModel {
	var $name = 'UserPropertyType';

    var $belongsTo = array(
        'PropertyType' => array(
            'className' => 'PropertyType',
            'foreignKey' => 'property_type_id'
        ),
    );

	var $validate = array(
		'user_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'User harap dipilih'
			),
		),
		'property_type_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Tipe properti harap dipilih'
			),
		),
	);

    function getDataModel ( $data ) {
        $dataSave = array();

        if( !empty($data['UserPropertyType']['property_type_id']) ) {
            $values = array_filter($data['UserPropertyType']['property_type_id']);

            foreach ($values as $key => $value) {
                $dataSave[]['UserPropertyType'] = array(
                    'property_type_id' => $value,
                );
            }
        }

        return $dataSave;
    }

    function getRequestData ( $data, $user_id ) {
        $values = $this->find('list', array(
        	'conditions' => array(
        		'UserPropertyType.user_id' => $user_id,
    		),
    		'order' => array(
    			'UserPropertyType.id' => 'ASC',
			),
			'fields' => array(
				'UserPropertyType.id', 'UserPropertyType.property_type_id',
			),
    	));
    	$requestData = array();

        if( !empty($values) ) {
            foreach ($values as $id => $property_type_id) {
                $requestData['UserPropertyType']['property_type_id'][$property_type_id] = true;
            }
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function doSave( $datas, $id = false, $user_id, $is_validate = false ) {
        $result = false;
        $default_msg = __('menambahkan tipe properti');

        if( !empty($user_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'UserPropertyType.user_id' => $user_id,
            ));
        }

        if ( !empty($datas) ) {            
            foreach ($datas as $key => $data) {
                if( empty($id) ) {
                    $this->create();
                } else {
                    $this->id = $id;
                }

                if( !empty($user_id) ) {
                    $data['UserPropertyType']['user_id'] = $user_id;
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
                            'msg' => sprintf(__('Gagal %s'), $default_msg),
                            'status' => 'error',
                        );
                    }
                } else {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                    );
                }
            }
        }

        if( empty($result) ) {
            $result = array(
                'msg' => sprintf(__('Berhasil %s'), $default_msg),
                'status' => 'success',
            );
        }

        return $result;
    }

    function getMerge ( $data, $id = false, $fieldName = 'UserPropertyType.user_id' ) {
        if( empty($data['UserPropertyType']) && !empty($id) ) {
            $value = $this->find('all', array(
                'conditions' => array(
                    $fieldName => $id,
                ),
            ));

            if( !empty($value) ) {
                $data['UserPropertyType'] = $value;
            }
        }

        return $data;
    }
}
?>