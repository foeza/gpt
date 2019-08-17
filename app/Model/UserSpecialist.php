<?php
class UserSpecialist extends AppModel {
	var $name = 'UserSpecialist';

    var $belongsTo = array(
        'Specialist' => array(
            'className' => 'Specialist',
            'foreignKey' => 'specialist_id'
        ),
    );

	var $validate = array(
		'user_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'User harap dipilih'
			),
		),
		'specialist_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jenis spesialis harap dipilih'
			),
		),
	);

    function getDataModel ( $data ) {
        $dataSave = array();

        if( !empty($data['UserSpecialist']['specialist_id']) ) {
            $values = array_filter($data['UserSpecialist']['specialist_id']);

            foreach ($values as $key => $value) {
                $dataSave[]['UserSpecialist'] = array(
                    'specialist_id' => $value,
                );
            }
        }

        return $dataSave;
    }

    function getRequestData ( $data, $user_id ) {
        $values = $this->find('list', array(
        	'conditions' => array(
        		'UserSpecialist.user_id' => $user_id,
    		),
    		'order' => array(
    			'UserSpecialist.id' => 'ASC',
			),
			'fields' => array(
				'UserSpecialist.id', 'UserSpecialist.specialist_id',
			),
    	));
    	$requestData = array();

        if( !empty($values) ) {
            foreach ($values as $id => $specialist_id) {
                $requestData['UserSpecialist']['specialist_id'][$specialist_id] = true;
            }
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function doSave( $datas, $id = false, $user_id, $is_validate = false ) {
        $result = false;
        $default_msg = __('menambahkan spesialis user');

        if( !empty($user_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'UserSpecialist.user_id' => $user_id,
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
                    $data['UserSpecialist']['user_id'] = $user_id;
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

    function getMerge ( $data, $id = false, $fieldName = 'UserSpecialist.user_id' ) {
        if( empty($data['UserSpecialist']) ) {
            $value = $this->find('all', array(
                'conditions' => array(
                    $fieldName => $id,
                ),
            ));

            if( !empty($value) ) {
                $data['UserSpecialist'] = $value;
            }
        }

        return $data;
    }
}
?>