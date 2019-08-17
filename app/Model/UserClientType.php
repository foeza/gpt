<?php
class UserClientType extends AppModel {
	var $name = 'UserClientType';

    var $belongsTo = array(
        'ClientType' => array(
            'className' => 'ClientType',
            'foreignKey' => 'client_type_id'
        ),
    );

	var $validate = array(
		'user_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'User harap dipilih'
			),
		),
		'client_type_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Tipe klien harap dipilih'
			),
		),
	);

    function getDataModel ( $data ) {
        $dataSave = array();

        if( !empty($data['UserClientType']['client_type_id']) ) {
            $values = array_filter($data['UserClientType']['client_type_id']);

            foreach ($values as $key => $value) {
                $dataSave[]['UserClientType'] = array(
                    'client_type_id' => $value,
                );
            }
        }

        return $dataSave;
    }

    function getRequestData ( $data, $user_id ) {
        $values = $this->find('list', array(
        	'conditions' => array(
        		'UserClientType.user_id' => $user_id,
    		),
    		'order' => array(
    			'UserClientType.id' => 'ASC',
			),
			'fields' => array(
				'UserClientType.id', 'UserClientType.client_type_id',
			),
    	));
    	$requestData = array();

        if( !empty($values) ) {
            foreach ($values as $id => $client_type_id) {
                $requestData['UserClientType']['client_type_id'][$client_type_id] = true;
            }
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function doSave( $datas, $id = false, $user_id, $is_validate = false ) {
        $result = false;
        $default_msg = __('menambahkan tipe klien');

        if( !empty($user_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'UserClientType.user_id' => $user_id,
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
                    $data['UserClientType']['user_id'] = $user_id;
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

    function getMerge ( $data, $id = false, $fieldName = 'UserClientType.user_id' ) {
        if( empty($data['UserClientType']) && !empty($id) ) {
            $value = $this->find('all', array(
                'conditions' => array(
                    $fieldName => $id,
                ),
            ));

            if( !empty($value) ) {
                $data['UserClientType'] = $value;
            }
        }

        return $data;
    }
}
?>