<?php
class UserSettingEmail extends AppModel {
	var $name = 'UserSettingEmail';
	var $validate = array(
		'email' => array(
            'email' => array(
                'rule' => array('email'),
                'allowEmpty' => true,
                'message' => 'Format email salah',
            ),
		),
	);

    function getDataModel ( $data ) {
        $dataSave = array();

        if( !empty($data['UserSettingEmail']['name']) ) {
            $values = array_filter($data['UserSettingEmail']['name']);

            foreach ($values as $key => $value) {
                $dataSave[]['UserSettingEmail'] = array(
                    'email' => $value,
                );
            }
        }

        return $dataSave;
    }

    function getRequestData ( $data, $id ) {
        $values = $this->find('list', array(
        	'conditions' => array(
        		'UserSettingEmail.user_setting_id' => $id,
    		),
    		'order' => array(
    			'UserSettingEmail.id' => 'ASC',
			),
			'fields' => array(
				'UserSettingEmail.id', 'UserSettingEmail.email',
			),
    	));
        $requestData = array();

        if( !empty($values) ) {
            foreach ($values as $id => $email) {
                $requestData['UserSettingEmail']['name'][] = $email;
            }
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function doSave( $datas, $parent_id, $is_validate = false ) {
        $result = false;
        $default_msg = __('menambahkan list email perusahaan');

        if( !empty($parent_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'UserSettingEmail.user_setting_id' => $parent_id,
            ));
        }

        if ( !empty($datas) ) {
            foreach ($datas as $key => $data) {
                $this->create();

                if( !empty($parent_id) ) {
                    $data['UserSettingEmail']['user_setting_id'] = $parent_id;
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
}
?>