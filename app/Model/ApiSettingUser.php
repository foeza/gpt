<?php
class ApiSettingUser extends AppModel {
	var $name = 'ApiSettingUser';

	var $validate = array(
		'user_key' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Username harap diisi',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 15),
				'message' => 'Panjang username maksimal 15 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 3),
				'message' => 'Panjang username minimal 3 karakter',
			),
		),
		'secret_key' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Password harap diisi',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 64),
				'message' => 'Panjang password maksimal 64 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'Panjang password minimal 6 karakter',
			),
		),
		'parent_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'perusahaan harap diisi',
			),
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$type = Common::hashEmptyField($elements, 'type', 'guest');
		$company = Common::hashEmptyField($elements, 'company', false);

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
		);

		if($type){
			switch ($type) {
				case 'master':
					$default_options['conditions']['ApiSettingUser.type'] = 'master';
					break;
				
				default:
					$default_options['conditions']['ApiSettingUser.type'] = 'guest';
					break;
			}
		}

		if($company){
			$company_id = Configure::read('Principle.id');

			$default_options['conditions']['ApiSettingUser.parent_id'] = $company_id;
		}

		if(!empty($options['conditions'])){
			$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
		}

		if( $find == 'conditions' && !empty($default_options['conditions']) ) {
			$result = $default_options['conditions'];
		} else if( $find == 'paginate' ) {
			if( empty($default_options['limit']) ) {
				$default_options['limit'] = Configure::read('__Site.config_admin_pagination');
			}
			
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
		return $result;
	}

	function doSave($data, $recordID, $value = array()){
		if($data){
			$data = Hash::insert($data, 'ApiSettingUser.parent_id', $recordID);
			
			if($value){
				$id = Common::hashEmptyField($value, 'ApiSettingUser.id');
				$this->id = $id;
			} else {
				$this->create();
			}

			$this->set($data);
			$validate = $this->validates();

			if($validate){
				if($this->save()){
					$msg = __('Berhasil merubahkan token API');

					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'data' => $data,
							'error' => 1,
						),
					);
				} else {
					$msg = __('Gagal merubahkan token API');

					$result = array(
						'msg' => $msg,
						'status' => 'error',
						'Log' => array(
							'activity' => $msg,
							'data' => $data,
							'error' => 1,
						),
					);
				}
			} else {
				$msg = __('Gagal merubahkan token API');

				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'data' => $data,
						'error' => 1,
					),
					'validationErrors' => $this->validationErrors,
				);
			}
		} else if($value){
			$result['data'] = $value;
		}
		return $result;
	}

}