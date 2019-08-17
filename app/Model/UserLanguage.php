<?php
class UserLanguage extends AppModel {
	var $name = 'UserLanguage';

    var $belongsTo = array(
        'Language' => array(
            'className' => 'Language',
            'foreignKey' => 'language_id'
        ),
    );

	var $validate = array(
		'user_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'User harap dipilih'
			),
		),
		'language_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Bahasa harap dipilih'
			),
		),
	);

    function getDataModel ( $data ) {
        $dataSave = array();

        if( !empty($data['UserLanguage']['language_id']) ) {
            $values = array_filter($data['UserLanguage']['language_id']);

            foreach ($values as $key => $value) {
                $dataSave[]['UserLanguage'] = array(
                    'language_id' => $value,
                );
            }
        }
        
        if( !empty($data['UserLanguage']['other_id']) ) {
            $text = !empty($data['UserLanguage']['other_text'])?$data['UserLanguage']['other_text']:false;

            $dataSave[]['UserLanguage'] = array(
                'language_id' => -1,
                'other_text' => $text,
            );
        }

        return $dataSave;
    }

    function getRequestData ( $data, $user_id ) {
        $values = $this->find('all', array(
        	'conditions' => array(
        		'UserLanguage.user_id' => $user_id,
    		),
    		'order' => array(
    			'UserLanguage.id' => 'ASC',
			),
    	));
    	$requestData = array();

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = !empty($value['UserLanguage']['language_id'])?$value['UserLanguage']['language_id']:false;
                $other_text = !empty($value['UserLanguage']['other_text'])?$value['UserLanguage']['other_text']:false;

                if( $id == -1 ) {
                    $requestData['UserLanguage']['other_id'] = true;
                    $requestData['UserLanguage']['other_text'] = $other_text;
                } else {
                    $requestData['UserLanguage']['language_id'][$id] = true;
                }
            }
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function doSave( $datas, $id = false, $user_id, $is_validate = false ) {
        $result = false;
        $default_msg = __('menambahkan bahasa');

        if( !empty($user_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'UserLanguage.user_id' => $user_id,
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
                    $data['UserLanguage']['user_id'] = $user_id;
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

    function getMerge ( $data, $id = false, $fieldName = 'UserLanguage.user_id' ) {
        if( empty($data['UserLanguage']) ) {
            $value = $this->find('all', array(
                'conditions' => array(
                    $fieldName => $id,
                ),
            ));

            if( !empty($value) ) {
                $data['UserLanguage'] = $value;
            }
        }

        return $data;
    }
}
?>