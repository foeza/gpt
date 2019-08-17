<?php
class UserConfig extends AppModel {
	var $name = 'UserConfig';
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		'Arebi' => array(
			'className' => 'Arebi',
			'foreignKey' => 'arebi_id'
		),
		'Theme' => array(
			'className' => 'Theme',
			'foreignKey' => 'theme_id',
		),
		'MembershipPackage' => array(
			'className' => 'MembershipPackage',
			'foreignKey' => 'membership_package_id',
		),
	);
	var $validate = array(
		'sharingtocompany' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty'=> true,
				'message' => 'Format sharing to company harus angka',
			),
			'validPercentageInput' => array(
				'rule' => array('validPercentageInput'),
				'message' => 'Sharing to company tidak valid.',
			),
		),
		'token' => array(
			'isUnique' => array(
				'rule' => array('isUnique'),
				'allowEmpty'=> true,
				'message' => 'Token telah terdaftar',
			),
		),
		'royalty' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty'=> true,
				'message' => 'Format royalty harus angka',
			),
			'validPercentageInput' => array(
				'rule' => array('validPercentageInput'),
				'message' => 'Royalty tidak valid.',
			),
		),
		'theme_id' => array(
			'notempty' => array(
				'rule'		=> array('notempty'),
				'message'	=> 'Mohon Pilih Tema',
			),
		), 
		'membership_package_id' => array(
			'notempty' => array(
				'rule'		=> array('notempty'),
				'message'	=> 'Mohon Pilih Paket Membership',
			),
		), 
	);

	function validPercentageInput($data) {
		$percentage = false;
		if( !empty($data['sharingtocompany']) ) {
			$percentage = $data['sharingtocompany'];
		} else if( !empty($data['royalty']) ) {
			$percentage = $data['royalty'];
		}

		if( $percentage >= 0 && $percentage <= 100 ) {
			return true;
		}
		return false;
	}

	function getData( $find = 'all', $options = array() ){
		$options = $this->_callFieldForAPI($find, $options);

		if( $find == 'paginate' ) {
			$result = $options;
		} else {
			$result = $this->find($find, $options);
		}
        return $result;
	}

	function _callFieldForAPI ( $find, $options ) {
		if( !in_array($find, array( 'list', 'count' )) ) {
			$rest_api = Configure::read('Rest.token');

			if( !empty($rest_api) ) {
				$options['fields'] = array(
				  'UserConfig.id',
				  'UserConfig.user_id',
				  'UserConfig.username_disabled',
				  'UserConfig.token',
				  'UserConfig.progress_user',
				  'UserConfig.progress_profession',
				  'UserConfig.progress_sosmed',
				  'UserConfig.award',
				  'UserConfig.experience',
				  'UserConfig.user_property_types',
				  'UserConfig.specialists',
				  'UserConfig.certifications',
				  'UserConfig.other_certifications',
				  'UserConfig.languages',
				  'UserConfig.other_languages',
				  'UserConfig.client_types',
				  'UserConfig.facebook',
				  'UserConfig.twitter',
				  'UserConfig.google_plus',
				  'UserConfig.linkedin',
				  'UserConfig.pinterest',
				  'UserConfig.instagram',
				  'UserConfig.commission',
				  'UserConfig.sharingtocompany',
				  'UserConfig.royalty',
				);
			}
		}

		return $options;
	}

	function getMerge ( $data = array(), $id = false ) {
		if( empty($data['UserConfig']) && !empty($id) ){
			$userConfig = $this->getData('first', array(
				'conditions' => array(
					'UserConfig.user_id' => $id,
				)
			));

			if(!empty($userConfig)){
				$data = array_merge($data, $userConfig);
			}
		}

		return $data;
	}

	public function doEditSocialMedia( $user_id, $user, $data ) {

		$result = false;

		if ( !empty($data) ) {

			$data['UserConfig']['facebook'] = Common::hashEmptyField($data, 'UserConfig.facebook');
			$data['UserConfig']['twitter'] = Common::hashEmptyField($data, 'UserConfig.twitter');
			$data['UserConfig']['google_plus'] = Common::hashEmptyField($data, 'UserConfig.google_plus');
			$data['UserConfig']['linkedin'] = Common::hashEmptyField($data, 'UserConfig.linkedin');
			$data['UserConfig']['pinterest'] = Common::hashEmptyField($data, 'UserConfig.pinterest');
			$data['UserConfig']['instagram'] = Common::hashEmptyField($data, 'UserConfig.instagram');
			
			$all_field = count($data['UserConfig']);
			$filled_field = count(array_filter($data['UserConfig']));
			$percentage = round(($filled_field / $all_field) * 100);
			$data['UserConfig']['progress_sosmed'] = $percentage;

			$save = $this->doSave($user, $data, $user_id);

			if ( !empty($save) ) {
				$result = array(
					'msg' => __('Sukses memperbarui data media sosial Anda'),
					'status' => 'success',
				);
			} else {
				$result = array(
					'msg' => __('Gagal memperbarui data media sosial Anda. Silahkan coba lagi'),
					'status' => 'error',
					'validationErrors' => $this->validationErrors,
				);
			}
		} else if( !empty($user) ) {
			$result['data'] = $user;
		}

		return $result;
	}

	function doSave( $user, $data, $user_id ) {
		if ( !empty($user['UserConfig']['id']) ) {
			$data['UserConfig']['id'] = $user['UserConfig']['id'];
		}

		if( !empty($user_id) ) {
			$data['UserConfig']['user_id'] = $user_id;
			
			$data['User']['id'] = $user_id;
			$data['User']['modified'] = date('Y-m-d H:i:s');
		}

		if ( $this->saveAll($data) ) {
			return true;
		}

		return false;
	}

	function doUpdateLastLogin ( $id, $data ) {
		if(!empty($id)){
			$this->id = $id;
		}else{
			$this->create();
		}

		$this->set($data);
		return $this->save();
	}

	function updateDeviceId($data){
		$device = $this->filterEmptyField($_POST, 'device');
		$device_id = $this->filterEmptyField($data, 'UserConfig', 'device_id');
		$user_login_id = Configure::read('User.id');

		$user_config = $this->getData('first', array(
			'conditions' => array(
				'UserConfig.user_id' => $user_login_id
			)
		));

		$id = $this->filterEmptyField($data, 'UserConfig', 'id');

		$device_allow = Configure::read('__Site.Device.field');

		$valid_type = false;
		if(!empty($device) && isset($device_allow[$device]) ){
			$data['UserConfig'][$device_allow[$device]] = $device_id;
			
			unset($data['UserConfig']['device_id']);

			$this->validator()
                ->add($device_allow[$device], array(
                    'notempty' => array(
                        'rule' => 'notempty',
                        'message' => 'tipe device tidak dikenali'
                    )
                ));

            $valid_type = true;
		}

		if(!empty($user_config) && !empty($valid_type)){
			$this->id = $id;

			$this->set($data);

			if($this->validates($data)){
				if($this->save($data)){
					$result = array(
						'status' => 'success',
						'msg' => __('Berhasil melakukan update device ID'),
						'Log' => array(
							'activity' => sprintf('user dengan id #%s berhasil melakukan update device'),
							'document_id' => $id,
						)
					);
				}else{
					$result = array(
						'status' => 'error',
						'msg' => __('Gagal melakukan update device ID')
					);
				}
			}else{
				$validationErrors = array();

				if(!empty($this->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->validationErrors);
				}

				$result = array(
					'status' => 'error',
					'msg' => __('Gagal melakukan update device ID'),
					'validationErrors' => $validationErrors
				);
			}
		}else{
			$result = array(
				'status' => 'error',
				'msg' => __('Config tidak ditemukan')
			);
		}

		return $result;
	}
}
?>