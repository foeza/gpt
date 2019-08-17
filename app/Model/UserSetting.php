<?php
class UserSetting extends AppModel {
	var $name = 'UserSetting';

	var $hasMany = array(
		'UserSettingEmail' => array(
			'className' => 'UserSettingEmail',
			'foreignKey' => 'user_setting_id'
		),
	);

	var $validate = array(
		'sign_date' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Tgl kontrak harap dipilih',
			),
			'date' => array(
				'rule' => array('date'),
				'message' => 'Tgl kontrak tidak valid',
			),
		),
		'from_date' => array(
			'validateFromDate' => array(
				'rule' => array('validateFromDate'),
				'message' => 'Tgl tayang tidak valid',
			),
			'date' => array(
				'rule' => array('date'),
				'allowEmpty' => true,
				'message' => 'Tgl tayang tidak valid',
			),
		),
		'to_date' => array(
			'validateToDate' => array(
				'rule' => array('validateToDate'),
				'message' => 'Tgl tayang harap dipilih',
			),
			'date' => array(
				'rule' => array('date'),
				'allowEmpty' => true,
				'message' => 'Tgl berhakir tayang tidak valid',
			),
		),
		'limit_agent' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jumlah agen harap diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Jumlah agen harap diisi dengan angka',
			),
		),
		'limit_listing' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jumlah listing harap diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Jumlah listing harap diisi dengan angka',
			),
		),
		'limit_premium' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jumlah permium listing harap diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Jumlah permium listing harap diisi dengan angka',
			),
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ) {
        $mine = isset($elements['mine']) ? $elements['mine']:false;

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
		);

        if( !empty($mine) ) {
            $user_login_id = Configure::read('User.id');
            $default_options['conditions']['UserSetting.user_id'] = $user_login_id;
        }

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

		if( $find == 'conditions' && !empty($default_options['conditions']) ) {
			$result = $default_options['conditions'];
		} else if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
		
        return $result;
	}

	function validateFromDate(){
		$result = true;

		if( !empty($this->data['UserSetting']['to_date']) ) {
			if( empty($this->data['UserSetting']['from_date']) ) {
				$result = false;
			} else if( !empty($this->data['UserSetting']['from_date']) ) {
				if( $this->data['UserSetting']['from_date'] > $this->data['UserSetting']['to_date'] ) {
					$result = false;
				}
			}
		}

		return $result;
	}

	function validateToDate(){
		$result = true;

		if( !empty($this->data['UserSetting']['from_date']) ) {
			if( !empty($this->data['UserSetting']['to_date']) ) {
				if( $this->data['UserSetting']['from_date'] > $this->data['UserSetting']['to_date'] ) {
					$result = false;
				}
			}
		}

		return $result;
	}

    function doSave( $data, $value = false, $user_id = false, $id = false ) {
        $result = false;
        $default_msg = __('menyimpan pengaturan website');

        if ( !empty($data) ) {
        	if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
            }

            $data['UserSetting']['user_id'] = $user_id;
            $dataEmail = $this->UserSettingEmail->getDataModel($data);

            $this->set($data);
            $settingValidate = $this->validates();

            $emailValidates = $this->UserSettingEmail->doSave($dataEmail, false, true);
            $statusEmail = !empty($emailValidates['status'])?$emailValidates['status']:false;

            if( $settingValidate && $statusEmail == 'success' ) {
                $flagSave = true;

                if( empty($validate) ) {
                    $flagSave = $this->save();
                    $id = $this->id;

                    if( !empty($flagSave) ) {
                        $this->UserSettingEmail->doSave($dataEmail, $id);
                    }
                }

                if( !empty($flagSave) ) {
                    $result = array(
                        'msg' => sprintf(__('Berhasil %s'), $default_msg),
                        'status' => 'success',
                    );
                } else {
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
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }
}
?>