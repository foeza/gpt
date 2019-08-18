<?php
class User extends AppModel {
	var $name = 'User';

	/*mesti ada ketika mau pake ACL*/
	public $actsAs = array('Acl' => array('type' => 'requester', 'enabled' => false));

	public function bindNode($user) {
	    return array('model' => 'Group', 'foreign_key' => $user['User']['group_id']);
	}

	public function parentNode() {
        if (!$this->id && empty($this->data)) {
            return null;
        }
        if (isset($this->data['User']['group_id'])) {
            $groupId = $this->data['User']['group_id'];
        } else {
            $groupId = $this->field('group_id');
        }
        if (!$groupId) {
            return null;
        }
        return array('Group' => array('id' => $groupId));
    }
    /*end mesti ada ketika mau pake ACL*/

	var $hasOne = array(
		'UserConfig' => array(
			'className' => 'UserConfig',
			'foreignKey' => 'user_id',
		),
		'UserProfile' => array(
			'className' => 'UserProfile',
			'foreignKey' => 'user_id'
		),
		'UserIntegratedConfig' => array(
			'className' => 'UserIntegratedConfig',
			'foreignKey' => 'user_id'
		),
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => 'user_id'
		),
		'UserSetting' => array(
			'className' => 'UserSetting',
			'foreignKey' => 'user_id'
		),
		'UserCompanyConfig' => array(
			'className' => 'UserCompanyConfig',
			'foreignKey' => 'user_id'
		),
		'UserCompanySetting' => array(
			'className' => 'UserCompanySetting',
			'foreignKey' => 'user_id'
		),
		'UserCompanyLauncher' => array(
			'className' => 'UserCompanyLauncher',
			'foreignKey' => 'user_id'
		),
	);

	var $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id'
		),
		'ClientType' => array(
			'className' => 'ClientType',
			'foreignKey' => 'client_type_id',
		),
		'Parent' => array(
			'className' => 'User',
			'foreignKey' => 'parent_id'
		),
		'UserGroupParent' => array(
			'className' => 'User',
			'foreignKey' => 'superior_id',
		),
	);

	var $hasMany = array(
		'Message' => array(
			'className' => 'Message',
			'foreignKey' => 'to_id'
		),
		'Log' => array(
			'className' => 'Log',
			'foreignKey' => 'user_id'
		),
		'UserRemoveAgent' => array(
			'className' => 'UserRemoveAgent',
			'foreignKey' => 'user_id'
		),
		'UserActivedAgent' => array(
			'className' => 'UserActivedAgent',
			'foreignKey' => 'user_id'
		),
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'user_id'
		),
		'UserClientType' => array(
			'className' => 'UserClientType',
			'foreignKey' => 'user_id'
		),
		'UserPropertyType' => array(
			'className' => 'UserPropertyType',
			'foreignKey' => 'user_id'
		),
		'UserSpecialist' => array(
			'className' => 'UserSpecialist',
			'foreignKey' => 'user_id'
		),
		'UserLanguage' => array(
			'className' => 'UserLanguage',
			'foreignKey' => 'user_id'
		),
		'UserAgentCertificate' => array(
			'className' => 'UserAgentCertificate',
			'foreignKey' => 'user_id'
		),
		'Advice' => array(
			'className' => 'Advice',
			'foreignKey' => 'user_id'
		),
		'Partnership' => array(
			'className' => 'Partnership',
			'foreignKey' => 'user_id'
		),
		'UserView' => array(
			'className' => 'UserView',
			'foreignKey' => 'user_id'
		),
		'PasswordReset' => array(
			'className' => 'PasswordReset',
			'foreignKey' => 'user_id'
		),
		'UserCompanyEbrochure' => array(
			'className' => 'UserCompanyEbrochure',
			'foreignKey' => 'user_id'
		),
		'ViewUserCompanyEbrochure' => array(
			'className' => 'ViewUserCompanyEbrochure',
			'foreignKey' => 'user_id'
		),
		'Notification' => array(
			'className' => 'Notification',
			'foreignKey' => 'user_id'
		),
		'Kpr' => array(
			'className' => 'Kpr',
			'foreignKey' => 'user_id'
		),
		'LogKpr' => array(
			'className' => 'LogKpr',
			'foreignKey' => 'user_id'
		),
		'EbrosurRequest' => array(
			'className' => 'EbrosurRequest',
			'foreignKey' => 'user_id'
		),
		'CrmProject' => array(
			'className' => 'CrmProject',
			'foreignKey' => 'user_id'
		),
		'UserClientRelation' => array(
			'className' => 'UserClientRelation',
			'foreignKey' => 'user_id'
		),
		'MailchimpCampaign' => array(
			'className' => 'MailchimpCampaign',
			'foreignKey' => 'user_id'
		),
		'UserClient' => array(
			'className' => 'UserClient',
			'foreignKey' => 'user_id'
		),
		'MailchimpPersonalCampaign' => array(
			'className' => 'MailchimpPersonalCampaign',
			'foreignKey' => 'user_id'
		),
		'CoBrokeUser' => array(
			'className' => 'CoBrokeUser',
			'foreignKey' => 'user_id'
		),
		'Report' => array(
			'className' => 'Report',
			'foreignKey' => 'user_id',
		),
		'UserIntegratedOrder' => array(
			'className' => 'UserIntegratedOrder',
			'foreignKey' => 'user_id'
		),
		'UserIntegratedOrderAddon' => array(
			'className' => 'UserIntegratedOrderAddon',
			'foreignKey' => 'user_id',
		),
		'LogView' => array(
			'className' => 'LogView',
			'foreignKey' => 'user_id',
		),
		'InvoiceCollector' => array(
			'className' => 'InvoiceCollector',
			'foreignKey' => 'company_id',
		),
		'ApiAdvanceDeveloper' => array(
			'className' => 'ApiAdvanceDeveloper',
			'foreignKey' => 'user_id',
		),
		'PropertyLog' => array(
			'className' => 'PropertyLog',
			'foreignKey' => 'user_id',
		),
		'AgentRank' => array(
			'foreignKey' => 'user_id', 
		),
		'SocialProfile' => array(
            'className' => 'SocialProfile',
			'foreignKey' => 'user_id', 
        ),
		'ActivityUser' => array(
            'className' => 'ActivityUser',
			'foreignKey' => 'user_id', 
        ),
		'UserHistory' => array(
			'foreignKey' => 'user_id', 
		),
		'CrmProjectActivity' => array(
			'foreignKey' => 'user_id', 
		),
	);

	var $validate = array(
		'photo' => array(
			// 'valPhoto' => array(
			// 	'rule' => array('valPhoto'),
			// 	'message' => 'Mohon unggah foto profil Anda.',
			// ),
			'imageupload' => array(
	            'rule' => array('extension',array('jpeg','jpg','png','gif')),
	            'message' => 'Foto harap diisi dan berekstensi (jpeg, jpg, png, gif)',
	            'allowEmpty' => true,
	        ),
		),
		'username' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Username harap diisi',
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Username telah terdaftar',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 15),
				'message' => 'Panjang username maksimal 15 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 3),
				'message' => 'Panjang username minimal 3 karakter',
			),
			'validateSlug' => array(
				'rule' => array('validateSlug'),
				'message' => 'Karakter yang diijinkan hanya huruf, angka, ".", "-" dan harus diawali serta diakhiri dengan huruf atau angka'
			),
			'validateUsername' => array(
				'rule' => array('validateUsername'),
				'message' => 'Anda telah melakukan perubahan username sebelumnya. Silahkan hubungi Administrator Kami untuk informasi lebih detail.'
			),
		),
		'full_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama lengkap harap diisi',
			),
			'alphabetSpace' => array(
				'rule' => array('custom', '/^[a-zA-Z ]*$/i'),
				'message' => 'Nama Lengkap hanya boleh mengandung karakter alphabet dan spasi.',
			), 
		),
		'gender_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jenis Kelamin harap diisi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Format Jenis Kelamin harus angka',
			),
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email harap diisi',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Email telah terdaftar',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Panjang email maksimal 128 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 5),
				'message' => 'Panjang email minimal 5 karakter',
			),
		),
		'agent_pic_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email harap diisi',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
			),
			'agent_pic_email' => array(
				'rule' => array('validateUserEmail'),
				'message' => 'Email agen yang Anda masukkan tidak terdaftar.',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Panjang email maksimal 128 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 5),
				'message' => 'Panjang email minimal 5 karakter',
			),
		),
		'password' => array(
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
		'current_password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan password Anda',
			),
			'checkCurrentPassword' => array(
				'rule' => array('checkCurrentPassword'),
				'message' => 'Password lama Anda salah',
			),
		),
		'password_confirmation' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Konfirmasi password harap diisi',
			),
			'notMatch' => array(
				'rule' => array('matchPasswords'),
				'message' => 'Konfirmasi password anda tidak sesuai',
			),
		),
		'new_password' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Password baru harap diisi',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'Panjang password baru minimal 6 karakter',
			),
		),
		'new_password_confirmation' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Konfirmasi password baru harap diisi',
			),
			'matchNewPasswords' => array(
				'rule' => array('matchNewPasswords'),
				'message' => 'Konfirmasi password anda tidak sesuai',
			),
		),
		'forgot_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Silahkan masukkan Email Anda',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format Email salah',
			),
			'forgot_email' => array(
				'rule' => array('validateUserEmail'),
				'message' => 'Email yang Anda masukkan belum terdaftar atau Anda belum mengaktifkan akun ini.',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Panjang email maksimal 128 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 5),
				'message' => 'Panjang email minimal 5 karakter',
			),
		),
		'group_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih divisi Anda.',
			),
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih divisi Anda.',
			),
		),
		'superior_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih atasan Anda.',
			),
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih atasan Anda.',
			),
		),
		'client_type_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih tipe klien',
			),
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih tipe klien',
			),
		),
		'parent_email' => array(
			'validateUserEmail' => array(
				'rule' => array('validateUserEmail'),
				'message' => 'Email yang Anda masukkan tidak terdaftar.',
			),
		),
	);

	function valPhoto () {
		$data = $this->data;
		$group_id = $this->filterEmptyField($data, 'User', 'group_id');
		$photo = $this->filterEmptyField($data, 'User', 'photo');

		if( empty($photo) ) {
			if( $group_id == 10 ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}
	
	function validateParent($data){
		$input_data = $this->data;

		if ( !empty($data['parent_email']) || (!empty($input_data['User']['group_id']) && $input_data['User']['group_id'] == 3) ) {
			return true;
		}
		return false;
	}

	function validateUsername($data){
		if ( !empty($this->dataValidation['User']) && !empty($this->dataValidation['UserConfig']) ) {
			if ( $this->dataValidation['User']['username'] != $data['username'] ) {
				$is_username_disabled = $this->dataValidation['UserConfig']['username_disabled'];
				if ( $is_username_disabled ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	* 	@param array $data['new_password_confirmation'] - password baru
	* 	@return boolean true or false
	*/
	function matchNewPasswords($data) {
		if($this->data['User']['new_password']) {
			if($this->data['User']['new_password'] === $data['new_password_confirmation']) {
				return true;
			}
			return false; 
		} else {
			return true;
		}
	}

	/* 
		VALIDATION FUNCTION 
	*/

	/**
	* 	@param array $data['forgot_email'] - email user
	* 	@return boolean true or false
	*/
	function validateUserEmail($data) {
		$group_id = $this->filterEmptyField($this->data, 'User', 'group_id');
		$parent_email = $this->filterEmptyField($this->data, 'User', 'parent_email');
		
		if( $group_id != 3 || ( $group_id == 3 && !empty($parent_email) ) ) {
			if( !empty($data) ) {
				$email = false;
				if( !empty($data['forgot_email']) ) {
					$email = $data['forgot_email'];
				} else if( !empty($data['agent_pic_email']) ) {
					$email = $data['agent_pic_email'];
				} else if (!empty($data['parent_email'])) {
					$email = $data['parent_email'];
				}

				$optionUser = array(
					'conditions'=> array(
						'User.email' => $email,
					)
				);
				$user = $this->find('first', $optionUser);

				if(!empty($user)){
					return true;
				}
			}
			
			return false;
		} else {
			return true;
		}
	}

	/**
	*	@param array $data['username'] - username user
	*	@return boolean true or false
	*/
    function validateSlug($data) {
    	$data['username'] = strtolower($data['username']);

    	if( preg_match('/\s/', $data['username']) ) {
    		return false;
    	} else if( substr($data['username'], 0, 1) == '.' || substr($data['username'], 0, 1) == '-' ) {
    		return false;
    	} else if( substr($data['username'], -1) == '.' || substr($data['username'], -1) == '-' ) {
    		return false;
    	} else if (preg_match('{^_?[a-z0-9_\\.\\- ]+$}i', $data['username'])==1) {
           return true; 
        } else {
        	return false;
        }
    }

    /**
	* 	@param array $data['current_password'] - password active user
	* 	@return boolean true or false
	*/
	function checkCurrentPassword() {

		$data = $this->dataValidation;
		if( !empty($data['password']) && !empty($data['User']) ) {
			$current_password = $data['User']['password'];
			if($data['password'] == $current_password) {
				return true;
			} else {
				return false;
			}
		}
		return false; 
	}

	/**
	* 	@param array $data['password_confirmation'] - password_confirmation
	* 	@return boolean true or false
	*/
	function matchPasswords($data) {
		if($data['password_confirmation']) {
			if( $this->data['User']['password'] === $data['password_confirmation'] ) {
				return true;
			} else {
				return false; 
			}
		} else {
			return true;
		}
	}

	/* 
		END OF VALIDATION FUNCTION 
	*/

	function _callGetParent ( $data, $group_id = false, $parent_is_must = true, $parent_id = false ) {
		$admin_rumahku = Configure::read('User.Admin.Rumahku');
		$parent_email = !empty($data['User']['parent_email'])?$data['User']['parent_email']:false;
		$group_id_login = Configure::read('User.group_id');

		if($group_id == 3){
			$role = 'director';
		}else{
			$role = $this->filterEmptyField($data, 'User', 'role', 'principle');
		}

		if($group_id_login <= 5 && $group_id_login >= 3 && in_array($group_id, array(2,5))){
			$data['User']['parent_id'] = Configure::read('Principle.id');
		}else{

			if(!empty($parent_email)){
				$parent = $this->getData('first', array(
					'conditions' => array(
						'User.email' => $parent_email,
					),
				), array(
					'status' => 'all',
					'role' => $role,
				));

				$parent_id = $this->filterEmptyField($parent, 'User', 'id');
			}
			
			if($parent_is_must || !empty($parent_id)){
				$data['User']['parent_id'] = $parent_id;
			}				
		}

		if( $role == 'director' ) {
			$msg = __('Mohon pilih email direktur');
		} else {
			$msg = __('Mohon pilih email principle');
		}

		return $data;
	}

	function doEdit( $user_id, $user, $data, $parent_is_must = true, $parent_id = false ) {
		$result = false;
		$is_edited_username = false;

		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		$user_profile_id = !empty($user['UserProfile']['id'])?$user['UserProfile']['id']:false;
		$group_id = !empty($user['User']['group_id'])?$user['User']['group_id']:false;

		if ( !empty($data) ) {
			$this->dataValidation = $user;
			$is_api = !empty($data['is_api']) ? trim($data['is_api']) : false;

			$data['User']['full_name'] = !empty($data['User']['full_name']) ? trim($data['User']['full_name']) : '';
			$data['UserProfile']['address'] = !empty($data['UserProfile']['address']) ? trim($data['UserProfile']['address']) : '';
			$data['UserProfile']['zip'] = !empty($data['UserProfile']['zip']) ? trim($data['UserProfile']['zip']) : '';
			$data['UserProfile']['no_hp'] = !empty($data['UserProfile']['no_hp']) ? trim($data['UserProfile']['no_hp']) : '';
			$data['UserProfile']['line'] = !empty($data['UserProfile']['line']) ? trim($data['UserProfile']['line']) : '';

			if($is_api){
				$this->removeValidator();
			}

			if($admin_rumahku){
				$data = $this->_callGetParent($data, $group_id, $parent_is_must, $parent_id);
			}

			$this->id = $user_id;
			$this->set($data);
			$this->UserProfile->set($data);
			$this->UserConfig->set($data);

			$userValidates = $this->validates();
			$userProfileValidates = $this->UserProfile->validates();
			$userConfigValidates = $this->UserConfig->validates();

			if ( $userValidates && $userProfileValidates && $userConfigValidates ) {
				$all_field = count($data['UserProfile']) + count($data['User']);
				$filled_field = count(array_filter($data['UserProfile'])) + count(array_filter($data['User']));
				$percentage = round(($filled_field / $all_field) * 100);
				$data['UserConfig']['progress_user'] = $percentage;

				// Utk cegah data kosong
				if( isset($data['User']['parent_id']) && empty($data['User']['parent_id']) ) {
					$data['User']['parent_id'] = 0;
				}

			//	sebelum di save, grab group id yang lama
				$old_user_data = $this->getData('first', array(
					'contain'		=> array('UserProfile', 'Group'), 
					'conditions'	=> array(
						'User.id' => $user_id,
					),
				), array(
					'status' => array('active', 'non-active'),
				));

				$principle_id = Common::hashEmptyField($data, 'User.parent_id', $parent_id);
				$new_group_id = Common::hashEmptyField($data, 'User.group_id');
				$old_group_id = Common::hashEmptyField($old_user_data, 'User.group_id');

				if( $this->save($data) ) {
					$save = $this->UserProfile->doSave($data, $user_id, $user_profile_id);

					if ( !empty($save) ) {
						if ( !empty($data['UserConfig']) ) {
							$this->UserConfig->doSave($user, $data, $user_id);
						}

					//	save history
						$history_type = $new_group_id == $old_group_id ? 'update' : 'promotion';

						$this->UserHistory->doSave(array(
							'UserHistory' => array(
								'principle_id'	=> !empty($principle_id)?$principle_id:0, 
								'user_id'		=> $user_id, 
								'group_id'		=> $new_group_id, 
								'type'			=> $history_type, 
								'old_data'		=> serialize($old_user_data), 
							), 
						));

						$result = array(
							'msg' => __('Sukses memperbarui profil'),
							'status' => 'success',
						);
					} else {
						$result = array(
							'msg' => __('Gagal memperbarui profil. Silakan coba lagi'),
							'status' => 'error',
						);
					}
				}
			} else {
				$validationErrors = array();

				if(!empty($this->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->validationErrors);
				}

				if(!empty($this->UserProfile->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->UserProfile->validationErrors);
				}

				if(!empty($this->UserConfig->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->UserConfig->validationErrors);
				}

				$result = array(
					'msg' => __('Gagal memperbarui profil. Silakan coba lagi'),
					'status' => 'error',
					'validationErrors' => $validationErrors
				);
			}

			$result = array_merge($result, array(
				'Log' => array(
					'activity'		=> $result['msg'],
					'old_data'		=> $user,
					'document_id'	=> $user_id,
				),
			));
		} else if( !empty($user) ) {
			$photo = !empty($user['User']['photo'])?$user['User']['photo']:false;
			$user['User']['photo_hide'] = $photo;

			$user = Common::_callUnset($user, array(
				'User' => array(
					'password',
				),
			));
			$result['data'] = $user;
		}

		return $result;
	}

	function doEditNonCompanies( $user_id, $user, $data ) {
		
		$result = false;
		$is_edited_username = false;
		$user_profile_id = !empty($user['UserProfile']['id'])?$user['UserProfile']['id']:false;
		
		if ( !empty($data) ) {
			$this->dataValidation = $user;
			$is_api = !empty($data['is_api']) ? trim($data['is_api']) : false;
			$group_id = $this->filterEmptyField($data, 'User', 'group_id');

			$data['User']['full_name'] = !empty($data['User']['full_name']) ? trim($data['User']['full_name']) : '';
			$data['UserProfile']['address'] = !empty($data['UserProfile']['address']) ? trim($data['UserProfile']['address']) : '';
			$data['UserProfile']['zip'] = !empty($data['UserProfile']['zip']) ? trim($data['UserProfile']['zip']) : '';
			$data['UserProfile']['no_hp'] = !empty($data['UserProfile']['no_hp']) ? trim($data['UserProfile']['no_hp']) : '';
			$data['UserProfile']['line'] = !empty($data['UserProfile']['line']) ? trim($data['UserProfile']['line']) : '';

			if($is_api){
				$this->removeValidator();
			}

			$data = $this->_callGetParent($data, $group_id);

			$this->id = $user_id;
			$this->set($data);
			$this->UserProfile->set($data);
			$this->UserConfig->set($data);

			$userValidates = $this->validates();
			$userProfileValidates = $this->UserProfile->validates();
			$userConfigValidates = $this->UserConfig->validates();

			if ( $userValidates && $userProfileValidates && $userConfigValidates ) {
				$all_field = count($data['UserProfile']) + count($data['User']);
				$filled_field = count(array_filter($data['UserProfile'])) + count(array_filter($data['User']));
				$percentage = round(($filled_field / $all_field) * 100);
				$data['UserConfig']['progress_user'] = $percentage;

			//	sebelum di save, grab group id yang lama
				$old_user_data = $this->getData('first', array(
					'contain'		=> array('UserProfile', 'Group'), 
					'conditions'	=> array(
						'User.id' => $user_id,
					),
				), array(
					'status' => array('active', 'non-active'),
				));

				if( $this->save($data) ) {
					$save = $this->UserProfile->doSave($data, $user_id, $user_profile_id);

					if ( !empty($save) ) {
						if ( !empty($data['UserConfig']) ) {
							$this->UserConfig->doSave($user, $data, $user_id);
						}

					//	save history
						$principle_id = Common::hashEmptyField($data, 'User.parent_id', Configure::read('Principle.id'));

						$this->UserHistory->doSave(array(
							'UserHistory' => array(
								'principle_id'	=> !empty($principle_id)?$principle_id:0, 
								'user_id'		=> $user_id, 
								'group_id'		=> $group_id, 
								'type'			=> 'recruit', 
								'old_data'		=> serialize($old_user_data), 
							), 
						));

						$result = array(
							'msg' => __('Sukses memperbarui profil'),
							'status' => 'success',
						);
					} else {
						$result = array(
							'msg' => __('Gagal memperbarui profil. Silahkan coba lagi'),
							'status' => 'error',
						);
					}
				}
			} else {
				$validationErrors = array();

				if(!empty($this->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->validationErrors);
				}

				if(!empty($this->UserProfile->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->UserProfile->validationErrors);
				}

				if(!empty($this->UserConfig->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->UserConfig->validationErrors);
				}

				$result = array(
					'msg' => __('Gagal memperbarui data profil. Silahkan coba lagi'),
					'status' => 'error',
					'validationErrors' => $validationErrors
				);

			}
		} else if( !empty($user) ) {
			$photo = !empty($user['User']['photo'])?$user['User']['photo']:false;

			$user['User']['photo_hide'] = $photo;
			$result['data'] = $user;
		}

		return $result;
	}

	function _callValidationErrors ( $groupName, $data ) {
		$result = false;

		$validationErrors = array();

		if(!empty($this->validationErrors)){
			$validationErrors = array_merge($validationErrors, $this->validationErrors);
		}

		if(!empty($this->UserProfile->validationErrors)){
			$validationErrors = array_merge($validationErrors, $this->UserProfile->validationErrors);
		}

		if(!empty($this->UserConfig->validationErrors)){
			$validationErrors = array_merge($validationErrors, $this->UserConfig->validationErrors);
		}

		$msg = sprintf(__('Gagal menambah %s. Silahkan coba lagi.'), $groupName);
		$result = array(
			'msg' => $msg,
			'status' => 'error',
			'data' => $data,
			'validationErrors' => $validationErrors,
			'Log' => array(
				'activity' => $msg,
				'data' => $data,
				'error' => 1,
			),
		);

		return $result;
	}

	function doAdd( $data, $parent_id, $parent_name = false, $group_id = 2, $sendEmail = true ) {

		$result = false;
		$admin_rumahku = Configure::read('User.Admin.Rumahku');
		$current_group_id = Configure::read('User.group_id');
		$current_user_id = Configure::read('User.id');
		$current_parent_id = Configure::read('Principle.id');
		$company = Configure::read('Config.Company.data');
		$company_name = $this->filterEmptyField($company, 'UserCompany', 'name');
		$group_company_group_id = $this->filterEmptyField($company, 'User', 'group_id');

		$action = Configure::read('App.Params.Action');

		if ( !empty($data) ) {
		//	jaga2 untuk add user yang udah terdaftar sebelumnya (tapi udah deleted)
			$userID		= Common::hashEmptyField($data, 'User.id');
			$oldData	= array();

			if($userID){
				if(Hash::check($data, 'UserProfile')){
					$userProfileID = $this->UserProfile->field('UserProfile.id', array('UserProfile.user_id' => $userID));

					$data = Hash::insert($data, 'UserProfile.id', $userProfileID);
					$data = Hash::insert($data, 'UserProfile.user_id', $userID);
				}
				
				if(Hash::check($data, 'UserConfig')){
					$userConfigID = $this->UserConfig->field('UserConfig.id', array('UserConfig.user_id' => $userID));

					$data = Hash::insert($data, 'UserConfig.id', $userConfigID);
					$data = Hash::insert($data, 'UserConfig.user_id', $userID);
				}

			//	old data
				$oldData = $this->getData('first', array(
					'contain'		=> array('UserProfile', 'Group'), 
					'conditions'	=> array(
						'User.id' => $userID,
					),
				), array(
					'status' => 'deleted',
				));
			}

			$data['User']['full_name'] = !empty($data['User']['full_name']) ? trim($data['User']['full_name']) : '';

			if(!empty($data['UserProfile']['address'])){
				$data['UserProfile']['address'] = trim($data['UserProfile']['address']);
			}

			if(!empty($data['UserProfile']['zip'])){
				$data['UserProfile']['zip'] = trim($data['UserProfile']['zip']);
			}

			if(!empty($data['UserProfile']['line'])){
				$data['UserProfile']['line'] = trim($data['UserProfile']['line']);
			}

			$data['UserProfile']['no_hp'] = !empty($data['UserProfile']['no_hp']) ? trim($data['UserProfile']['no_hp']) : '';
			$is_api = !empty($data['is_api']) ? trim($data['is_api']) : false;

			if($is_api){
				$this->removeValidator();
			}

			if(empty($data['User']['group_id']) && !empty($group_id)){
				$data['User']['group_id'] = $group_id;
			}

			if( !empty($admin_rumahku) ) {
				$data = $this->_callGetParent($data, $group_id);
			}

			$parent	= true;
			$groups	= array(1 => 'User', 2 => 'Agen', 3 => 'Principal', 5 => 'Admin', 10 => 'Klien');

			if(array_key_exists($group_id, $groups)){
				$groupName = Common::hashEmptyField($groups, $group_id, false);
			}
			else{
				if($group_id && $group_id <= 20 && !in_array($group_id, array(2, 5))){ // in_array ini ga perlu seharusnya udah ketangkep sama atas
					$parent		= false;
					$groupName	= __('Admin Primesystem');
				}
				else{
					$groupName = $this->Group->field('Group.name', array('Group.id' => $group_id));
				}
			}

			if( $parent && !empty($parent_id) ){
				$data['User']['parent_id'] = $parent_id;
			}

			if(empty($userID)){
				$this->create();
			}

			$this->set($data);
			$this->UserProfile->set($data);
			$this->UserConfig->set($data);

			$userValidates = $this->validates();
			$userProfileValidates = $this->UserProfile->validates();
			$userConfigValidates = $this->UserConfig->validates();

			// override condition for add rku admin
			if ( $action == 'admin_add_rku_admin' && $group_id = 2 ) {
				$groupName = __('Admin Primesystem');
			}

			if( $group_id == 10 ) {
				$client_email = !empty($data['User']['email'])?$data['User']['email']:false;
				$client_type_id = !empty($data['User']['client_type_id'])?$data['User']['client_type_id']:false;
				$agent_pic_email = !empty($data['User']['agent_pic_email'])?$data['User']['agent_pic_email']:false;

				if( $current_group_id == 2 ) {
					$agent_id = $current_user_id;
					$company_id = $current_parent_id;
					$data['UserClient']['agent_id'] = $current_user_id;
				} else {
					$agent = $this->getData('first', array(
						'conditions' => array(
							'User.email' => $agent_pic_email,
						),
					), array(
						'role' => 'agent',
						'status' => 'semi-active',
						'company' => true,
						'admin' => true,
					));
					$data['UserClient']['agent_pic_email'] = $agent_pic_email;
					$data['UserClient']['agent_id'] = !empty($agent['User']['id'])?$agent['User']['id']:false;
					$agent_id = !empty($agent['User']['id'])?$agent['User']['id']:false;
					$company_id = !empty($agent['User']['parent_id'])?$agent['User']['parent_id']:false;
				}

				$user = $this->getData('first', array(
					'conditions' => array(
						'User.email' => $client_email,
					),
				), array(
					'status' => 'semi-active',
				));

				if( !empty($user) ) {

					$this->UserClient->set($data);

					if( $this->UserClient->validates() ) {
						$id = $user['User']['id'];
						$data['UserClient']['user_id'] = $id;

						$client = $this->UserClient->getData('first', array(
							'conditions' => array(
								'UserClient.user_id' => $id,
								'UserClient.company_id' => $parent_id,
							),
						));

						if( empty($client) ) {
							$this->UserClient->create();

							if( $this->UserClient->save($data) ) {
								$client_id = $this->UserClient->id;

								$this->UserClientRelation->doSave( $data['UserClient']['agent_id'], $id );
								$result = array(
									'msg' => 'Sukses menambah '.$groupName,
									'status' => 'success',
									'data' => $data,
									'id' => $id,
									'client_id' => $client_id,
								);
							} else {
								$result = $this->_callValidationErrors($groupName, $data);
							}
						} else {
							$result = $this->_callValidationErrors($groupName, $data);
						}
					} else {
						$result = $this->_callValidationErrors($groupName, $data);
					}

					return $result;
				}
			}

			if ( $userValidates && $userProfileValidates && $userConfigValidates ) {
				$data['User']['password_ori'] = !empty($data['User']['password'])?$data['User']['password']:false;
				$data['User']['password'] = !empty($data['User']['auth_password'])?$data['User']['auth_password']:false;
				$data['User']['password_confirmation'] = !empty($data['User']['password'])?$data['User']['password']:false;
				$data['User']['active'] = 1;
				$data['User']['deleted'] = 0;

				if( $group_id == 10 ) {
					$data['UserClient']['password'] = $data['User']['password'];
					$data['UserClient']['agent_id'] = $agent_id;
					unset($data['User']['parent_id']);
				} else if( empty($admin_rumahku) ) {
					if( in_array($group_company_group_id, array( 4 )) ) {
						if( $group_id == 3 ) {
							$data['User']['parent_id'] = $current_parent_id;
						} else {
							$parent_email = $this->filterEmptyField($data, 'User', 'parent_email');
							
							if( !empty($parent_email) ) {
								$parent = $this->getMerge(array(), $parent_email, false, 'Parent', 'User.email');
								$data['User']['parent_id'] = $this->filterEmptyField($parent, 'Parent', 'id');
							}
						}
					} else {
						$data['User']['parent_id'] = $parent_id;
					}
				}

				$all_field = count($data['UserProfile']) + count($data['User']);
				$filled_field = count(array_filter($data['UserProfile'])) + count(array_filter($data['User']));
				$percentage = round(($filled_field / $all_field) * 100);
				$data['UserConfig']['progress_user'] = $percentage;

				// Utk cegah data kosong
				if( isset($data['User']['parent_id']) && empty($data['User']['parent_id']) ) {
					$data['User']['parent_id'] = 0;
				}

				$this->set($data);

				if( $this->save($data) ) {
					$id = $this->id;
					$save = $this->UserProfile->doSave($data, $id);

					if($is_api){
						$this->id = $id;
						$this->set('user_id_target', $id);
						$this->save();
					}

				//	save history
					$data = $oldData ?: $data;

					$this->UserHistory->doSave(array(
						'UserHistory' => array(
							'principle_id'	=> $parent_id, 
							'user_id'		=> $id, 
							'group_id'		=> $group_id, 
							'type'			=> 'recruit', 
							'old_data'		=> serialize($data), 
						), 
					));
					
					if ( $save ) {
						$data['UserConfig']['user_id'] = $id;
						$data['UserConfig']['first_register'] = 1;

						$this->UserConfig->create();
						$this->UserConfig->set($data);
						$this->UserConfig->save();

						if( $group_id == 10 ) {
							$data['UserClient']['user_id'] = $id;

							if( !empty($client_id) ) {
								$this->UserClient->id = $client_id;
							} else {
								$this->UserClient->create();
							}

							$this->UserClient->set($data);
							$this->UserClient->save();
							$client_id = $this->UserClient->id;

							$this->UserClientRelation->doSave( $agent_id, $id );
						}

						$result = array(
							'msg' => 'Sukses menambah '.$groupName,
							'status' => 'success',
							'data' => $data,
							'id' => $id,
							'client_id' => !empty($client_id)?$client_id:null,
						);

						if( !empty($sendEmail) ) {
							$full_name = !empty($data['User']['full_name'])?$data['User']['full_name']:false;
							$email = !empty($data['User']['email'])?$data['User']['email']:false;
							$password_ori = !empty($data['User']['password_ori'])?$data['User']['password_ori']:false;
							$site_name = Configure::read('__Site.site_name');

							$group_id = !empty($data['User']['group_id'])?$data['User']['group_id']:false;;

							$mail_params = array(
								'parent_fullname' => $parent_name,
								'email' => $email,
								'password' => $password_ori,
								'group_id' => $group_id,
							);

							if( $group_id == 3 ) {
								$subject = sprintf(__('Website Anda telah terdaftar di %s'), $site_name);
								$email_template = 'add_principle';
								$mail_params['logoDefault'] = true;
							} elseif ($group_id >= 11) {
								$subject = sprintf(__('Anda telah terdaftar sebagai %s'), $groupName);
								$email_template = 'add_admin_rumahku';
							} else {
								$subject = sprintf(__('Anda telah terdaftar di %s'), $company_name);
								$email_template = 'add_user';
							}

							$result['SendEmail'] = array(
	                        	'to_name' => $full_name,
	                        	'to_email' => $email,
	                        	'subject' => $subject,
	                        	'template' => $email_template,
	                        	'data' => $mail_params,
		                    );
						}
					} else {
						$result = $this->_callValidationErrors($groupName, $data);
					}
				} else {
					$result = $this->_callValidationErrors($groupName, $data);
				}
			} else {
				$result = $this->_callValidationErrors($groupName, $data);
			}

		}

		return $result;
	}
	
	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		$fullName = sprintf('trim(concat(trim(%s.first_name), " ", trim(%s.last_name)))', $this->alias, $this->alias);

		$this->virtualFields['full_name']		= $fullName;
    	$this->virtualFields['client_email']	= sprintf('CONCAT(%s.email, " | ", %s)', $this->alias, $fullName);
	}

	function beforeSave( $options = array() ) {
		
		$full_name = !empty( $this->data['User']['full_name'] ) ? $this->data['User']['full_name'] : false;
		if ( $full_name ) {
			$arr_name = explode(' ', $full_name);
		
			$first_name = $arr_name[0];
			unset($arr_name[0]);
			$last_name = implode(' ', $arr_name);

			$this->data['User']['first_name'] = $first_name;
			$this->data['User']['last_name'] = $last_name;
		}
		return true;
	}

	function getData( $find = 'all', $options = array(), $elements = array() ) {
        $role = isset($elements['role']) ? $elements['role']:'all';
        $status = isset($elements['status']) ? $elements['status']:'active';
        $company = isset($elements['company']) ? $elements['company']:false;
        $admin = isset($elements['admin']) ? $elements['admin']:false;
        $parent = isset($elements['parent']) ? $elements['parent']:false;
        $tree_division = isset($elements['tree_division']) ? $elements['tree_division']:false;
        $admin_rumahku = isset($elements['admin_rumahku']) ? $elements['admin_rumahku']:Configure::read('User.Admin.Rumahku');
        $include_principle = isset($elements['include_principle']) ? $elements['include_principle']:false;

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
		);
		
		if( !empty($status) ) {
			if( !is_array($status) ) {
				$status = array(
					$status,
				);
			}

			$statusResult = array();

			foreach ($status as $key => $statusName) {
		        switch ($statusName) {
		            case 'non-activation':
		                $statusResult[] = array(
							$this->alias.'.status' => 1,
		                	$this->alias.'.active' => 0,
							$this->alias.'.deleted' => 0,
		            	);
		                break;

		            case 'deleted':
		                $statusResult[] = array(
							$this->alias.'.deleted' => 1,
		            	);
		                break;

		            case 'non-active':
		                $statusResult[] = array(
		                	$this->alias.'.status' => 0,
		                	$this->alias.'.active' => 0,
							$this->alias.'.deleted' => 0,
		            	);
		                break;
		            
		            case 'active':
		                $statusResult[] = array(
							$this->alias.'.status' => 1,
							$this->alias.'.active' => 1,
							$this->alias.'.deleted' => 0,
		            	);
		                break;
		            
		            case 'semi-active':
		            	if( !empty($admin_rumahku) ) {
			                $statusResult[] = array(
								$this->alias.'.deleted' => 0,
			            	);
		            	} else {
			                $statusResult[] = array(
			                	$this->alias.'.status' => 1,
								$this->alias.'.deleted' => 0,
			            	);
			            }
		                break;

		            case 'all' : 
		            //	untuk invoice ada autocreate user biar ga error ini : 
		            //	Integrity constraint violation: 1062 Duplicate entry 'useremail@host.com' for key 'email'
		            //	jadi harus semua di cek data email nya (walaupun user udah di "delete")
		            	break;

		            default:
		            	if( !empty($admin_rumahku) ) {
			                $statusResult[] = array(
								$this->alias.'.deleted' => 0,
			            	);
		            	} else {
			                $statusResult[] = array(
			                	$this->alias.'.status' => 1,
								$this->alias.'.deleted' => 0,
			            	);
			            }
		                break;
		        }
			}

			$statusFinalResult[]['OR'] = $statusResult;
			$default_options['conditions'] = array_merge($default_options['conditions'], $statusFinalResult);
	    }

        if( !empty($role) ) {
	        if( !is_array($role) ) {
	        	$roles = array(
	        		$role,
	    		);
	        } else {
	        	$roles = $role;
	        }

	        foreach ($roles as $key => $role) {
		        switch ($role) {
					case 'non-agent':
						$default_options['conditions'][]['OR'] = array(
							$this->alias.'.group_id >' => 20,
							$this->alias.'.group_id' => array(5),
						);
						break;
					case 'director':
						$default_options['conditions'][$this->alias.'.group_id'][] = 4;
						break;
		        	case 'admin-director':
		            	$default_options['conditions'][$this->alias.'.group_id'][] = 5;
		            	$default_options['conditions']['Parent.group_id'] = 4;
		            	$default_options['contain']['Parent']['fields'] = array('Parent.*');
		        		break;
		        	case 'agent':
		            	$default_options['conditions'][$this->alias.'.group_id'][] = 2;
		        		break;
		        	case 'admin':
		            	$default_options['conditions'][$this->alias.'.group_id'][] = 5;
		            	$default_options['conditions']['Parent.group_id'] = 3;
		            	$default_options['contain']['Parent']['fields'] = array('Parent.*');
		        		break;
		    		case 'principle':
		            	$default_options['conditions'][$this->alias.'.group_id'][] = 3;
		        		break;
		        	case 'client':
		            	$default_options['conditions'][$this->alias.'.group_id'][] = 10;
		        		break;
					case 'user':
						$default_options['conditions'][$this->alias.'.group_id'][] = 1;
						break;
					case 'adminRku':
						$default_options['conditions'][$this->alias.'.group_id >'] = 10;
						$default_options['conditions'][$this->alias.'.group_id <='] = 20;
						break;
					case 'superadmin':
						$default_options['conditions'][$this->alias.'.group_id'][] = 20;
						break;
					case 'pic':
						$default_options['conditions'][$this->alias.'.group_id'] = array( 13,14 );
						break;
					case 'user-general':
						$default_options['conditions'][]['OR'] = array(
							$this->alias.'.group_id' => array(2, 5),
							$this->alias.'.group_id >' => 20,
						);
						break;
					case 'user-company':
						$default_options['conditions'][]['OR'] = array(
							$this->alias.'.group_id' => array(2, 5, 3, 4),
							$this->alias.'.group_id >' => 20,
						);
						break;
		        }
		    }
	    }

        if( !empty($admin) ) {
        	if( !empty($admin_rumahku) ) {
        		$admin = true;
        	} else {
        		$admin = false;
        	}
        }

        if( !empty($company) && empty($admin) ) {
            $companyData = Configure::read('Config.Company.data');
            $group_id = $this->filterEmptyField($companyData, 'User', 'group_id');

            if( is_numeric($company) ) {
            	$parent_id = $company;
            } else {
            	$parent_id = Configure::read('Principle.id');
            }

        	if( ( $role == 'agent' || !empty($parent) ) && $group_id == 4 ) {
        		if( !empty($include_principle) ) {
		            $default_options['conditions'][]['OR'] = array(
		            	array( 'Parent.parent_id' => $parent_id ),
		            	array( $this->alias.'.parent_id' => $parent_id ), 
		            	array( $this->alias.'.id' => Configure::read('Principle.id') ),
	            	);
		        } else {
		            $default_options['conditions'][]['OR'] = array(
		            	'Parent.parent_id' => $parent_id,
		            	$this->alias.'.parent_id' => $parent_id,
	            	);
		        }
	            $default_options['contain'][] = 'Parent';
        	} else {
        		if( !empty($include_principle) ) {
		            $default_options['conditions'][]['OR'] = array(
		            	array( $this->alias.'.parent_id' => $parent_id ),
		            	array( $this->alias.'.id' => Configure::read('Principle.id') ),
	            	);
		        } else {
		            $default_options['conditions'][$this->alias.'.parent_id'] = $parent_id;
		        }
	        }
        }

        if($tree_division){
        	$default_options = $this->getUserTree($default_options);
        }

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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
			if(isset($options['offset'])){
				$default_options['offset'] = $options['offset'];
			}

		}

        $default_options = $this->_callFieldForAPI($find, $default_options);

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

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count', 'conditions' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
				  'User.id',
				  'User.client_type_id',
				  'User.parent_id',
				  'User.group_id',
				  'User.code',
				  'User.username',
				  'User.first_name',
				  'User.last_name',
				  'User.email',
				  'User.gender_id',
				  'User.photo',
				  'User.deleted',
				  'User.active',
				  'User.status',
				  'User.full_name',
				  'User.modified',
				  'User.created',
                );
            }
        }

        return $options;
    }

	function getDataCompany ( $url, $params = false ) {
		$parent_id = !empty($params['company_principle_id'])?$params['company_principle_id']:false;
        $rest_api = Configure::read('Rest.token');

		if( !empty($parent_id) ) {
			$conditions = array(
				'UserCompanyConfig.user_id' => $parent_id,
			);
		} else {
			$url = !empty($_SERVER['HTTP_HOST'])?str_replace('www.', '', $_SERVER['HTTP_HOST']):$url;
			$conditions = array(
				'REPLACE(REPLACE(REPLACE(UserCompanyConfig.domain, \'www.\', \'\'), \'http://\', \'\'), \'https://\', \'\') LIKE' => $url.'%',
			);
		}

		$user = $this->UserCompanyConfig->getData('first', array(
			'conditions' => $conditions,
		));

		if( !empty($user) ) {
			$user_id = Common::hashEmptyField($user, 'UserCompanyConfig.user_id');
			$theme_id = Common::hashEmptyField($user, 'UserCompanyConfig.theme_id');
			$template_id = Common::hashEmptyField($user, 'UserCompanyConfig.template_id');
			$flag_theme_id = $theme_id;

			if( !empty($params) && isset($params['theme_id']) ) {
				$flag_theme_id = $params['theme_id'];
				$flag_theme = $this->UserCompanyConfig->Theme->getMerge(array(), $params['theme_id'], array(
					'cache' => array(
						'name' => __('Theme.%s', $params['theme_id']),
					),
				));
				$user['FlagSettingTheme'] = isset( $flag_theme['Theme'] ) ? $flag_theme['Theme'] : false;
			}
	
			$user = $this->getMerge( $user, $user_id, true );
			$user = $this->UserCompany->getMerge( $user, $user_id );
			$user = $this->UserCompanySetting->getMerge($user, $user_id, $flag_theme_id);
			$user = $this->UserCompanyConfig->Theme->getMerge($user, $theme_id, array(
				'cache' => array(
					'name' => __('Theme.%s', $theme_id),
				),
			));

			$user = $this->UserCompanyConfig->getMergeList($user, array(
				'contain' => array(
					'MembershipPackage', 
				), 
			));

			// limit dashboard
			$data_arr = array(
				'limit_top_agent' => __('Top Agen'),
		    	'limit_property_list' => __('Listing Properti'),
		    	'limit_property_popular' => __('Properti Terpopuler'),
		    	'limit_latest_news' => __('Berita Terkini'),
			);

			foreach ($data_arr as $field => $customName) {
				$themeConfig = $this->UserCompanyConfig->Theme->ThemeConfig->getData('first', array(
					'conditions' => array(
						'ThemeConfig.theme_id' => $flag_theme_id,
						'ThemeConfig.patch_name' => 'home',
						'ThemeConfig.slug' => $field,
					),
				));
				
				$theme_value = Common::hashEmptyField($themeConfig, 'ThemeConfig.value', 0);
				$value = Common::hashEmptyField($user, sprintf('UserCompanySetting.%s', $field), $theme_value);
				$val = Common::hashEmptyField($params, $field, $value);
				$user['UserCompanySetting'][$field] = $val;
			}
			// 

			$region_id = !empty($user['UserCompany']['region_id'])?$user['UserCompany']['region_id']:false;
			$city_id = !empty($user['UserCompany']['city_id'])?$user['UserCompany']['city_id']:false;
			$subarea_id = !empty($user['UserCompany']['subarea_id'])?$user['UserCompany']['subarea_id']:false;

			$location = $this->UserProfile->Region->getMerge(array(), $region_id, 'Region', array(
				'cache' => array(
					'name' => __('Region.%s', $region_id),
				),
			));
			$location = $this->UserProfile->City->getMerge($location, $city_id, 'City', 'City.id', array(
				'cache' => __('City.%s', $city_id),
			));
			$location = $this->UserProfile->Subarea->getMerge($location, $subarea_id, 'Subarea', 'Subarea.id', array(
				'cache' => __('Subarea.%s', $subarea_id),
				'cacheConfig' => 'subareas',
			));

			if( !empty($location) && !empty($user['UserCompany']) ) {
				$user['UserCompany'] = array_merge($user['UserCompany'], $location);
			}
		}

        if( !empty($rest_api) ) {
			$user['UserCompanyConfig'] = Common::hashEmptyField($user, 'UserCompanyConfig', array());
			$user['UserCompanyConfig'] = Common::hashEmptyField($user, 'UserCompanyConfig', array());
			$user['UserCompanySetting'] = Common::hashEmptyField($user, 'UserCompanySetting', array());
			$user['Theme'] = Common::hashEmptyField($user, 'Theme', array());
			$user['User'] = Common::hashEmptyField($user, 'User', array());
			$user['UserCompany'] = Common::hashEmptyField($user, 'UserCompany', array());
		}

		return $user;
	}

	function getMerge( $data, $user_id = false, $with_contain = false, $modelName = 'User', $fieldName = 'User.id' ) {
		if( empty($data[$modelName]) && !empty($user_id) ) {
			$user = $this->getData('first', array(
				'conditions' => array(
					$fieldName => $user_id
				),
			), array(
				'status' => 'all',
			));

			if( !empty($user) ) {
				$data[$modelName] = $user['User'];
			
				if( !empty($with_contain) ) {
					$userConfig = $this->UserConfig->getData('first', array(
						'conditions' => array(
							'UserConfig.user_id' => $user_id
						),
					));

					$userProfile = $this->UserProfile->getData('first', array(
						'conditions' => array(
							'UserProfile.user_id' => $user_id
						),
					));


					if( !empty($userConfig) ){
						$data = array_merge($data, $userConfig);
					}

					if( !empty($userProfile) ){
						$data = array_merge($data, $userProfile);
					}
				}
			}
		}

		return $data;
	}

	function getMergeAll( $data, $user_id = false, $fieldName = 'User.id', $modelName = 'User' ) {

		if( empty($data[$modelName]) ) {

			$users = $this->getData('all', array(
				'conditions' => array(
					$fieldName => $user_id
				),
			), array(
				'status' => 'semi-active',
			));

			if( !empty($users) ) {
				$data[$modelName] = $users;
			}
		}

		return $data;
	}

	function getClient( $data, $id = false ) {
		if( empty($data['Client']) ) {
			$user = $this->getData('first', array(
				'conditions' => array(
					'id' => $id,
				),
			), array(
				'status' => 'semi-active',
			));

			if( !empty($user) ) {
				$user = $this->UserProfile->getMerge($user, $id);
				$user = $this->UserClient->getMerge($user, $id);

				$data = array_merge($data, $user);
			}
		}

		return $data;
	}

	function getAllNeed( $data, $user_id = false, $group_id = false, $with_contain = false ) {
		$data = $this->UserProfile->getMerge($data, $user_id, $with_contain);
		$data = $this->UserConfig->getMerge($data, $user_id);
		$data = $this->Group->getMerge($data, $group_id);

		$data = $this->UserConfig->getMergeList($data, array(
			'contain' => array(
				'MembershipPackage', 
			), 
		));

		return $data;
	}

	function doSavePhoto( $data, $user_id ) {
        $result = new stdClass();
        $result_arr = array();

        $is_rest = Configure::read('__Site.is_rest');

        if ( !empty($data) ) {
        	if( !empty($data['error']) ){
	  			$result->error = 1;
	  			$result->message = $data['message'];
	  		}else{
	            $this->id = $user_id;
	            $this->set($data);

	            if( !$this->save() ) {
					$result->error = 1;
	  				$result->message = __('Gagal menyimpan foto profil');

	  				// untuk kebutuhan API
                    $result_arr = array(
                        'status'    => 'error',
                        'msg'       => $result->message
                    );
	            } else {
	            	$result_arr = array(
                        'status'    => 'success',
                        'msg'       => __('Berhasil mengunggah foto')
                    );

	            	$temp_arr = array();
	            	if(!empty($data['imagePath'])){
			  			$temp_arr['User']['thumbnail_url'] = $result->thumbnail_url = $data['imagePath'];
		  			}

		  			if(!empty($data['name'])){
			  			$result->name = $data['name'];
		  			}

		  			if( Configure::read('User.group_id') == 10 ) {
		  				if( Configure::read('Global.Data.MobileDetect.mobile') ) {
		  					$result->url = '/client/users/edit/';
		  				} else {
		  					$result->url = '/client/users/photo_crop/';
		  				}
		  			} else {
		  				if( Configure::read('Global.Data.MobileDetect.mobile') ) {
		  					$result->url = '/admin/users/edit/';
		  				} else {
		  					$result->url = '/admin/users/photo_crop/';
		  				}
		  			}

		  			$result_arr = array_merge($result_arr, $temp_arr);
	  			}
	  		}
        }

        if($is_rest){
            $result = $result_arr;
        }

        return $result;
    }

    function doCroppedPhoto( $user_id, $data, $photoName ) {
		$result = false;

		if ( !empty($data) ) {
			
			if( !empty($photoName) ) {
				
				$this->id = $user_id;
				$this->set('photo', $photoName);
				
				if($this->save()) {
					$result = array(
						'msg' => __('Sukses memperbarui foto profil Anda.'),
						'status' => 'success',
						'RefreshAuth' => array(
							'id' => $user_id,
						),
					);
				} else {
					$result = array(
						'msg' => __('Gagal memperbarui foto profil Anda. Silahkan coba lagi'),
						'status' => 'error',
					);
				}
			} else {
				$result = array(
					'msg' => __('Gagal memperbarui foto profil Anda. Silahkan coba lagi'),
					'status' => 'error',
				);
			}
		}

		return $result;
	}

	function doToggle( $id ) {	
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'User.id' => $id,
			),
		);

		$value = $this->getData('all', $options, array(
			'status' => 'all',
			'company' => true,
			'admin' => true,
		));

		if ( !empty($value) ) {
			$name = Set::extract('/User/full_name', $value);

			$name = implode(', ', $name);
			$default_msg = sprintf(__('menghapus user %s'), $name);

			$flag = $this->updateAll(array(
				'User.deleted' => 1,
				'User.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'User.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
				);

				if( !empty($value) ) {
					foreach ($value as $key => $user) {
						$group_id = !empty($user['User']['group_id'])?$user['User']['group_id']:false;
						$full_name = !empty($user['User']['full_name'])?$user['User']['full_name']:false;
						$email = !empty($user['User']['email'])?$user['User']['email']:false;

						if( in_array($group_id, array( 2,5 )) ) {
							$result['SendEmail'][] = array(
	                        	'to_name' => $full_name,
	                        	'to_email' => $email,
	                        	'subject' => __('Notifikasi Pemberhentian'),
	                        	'template' => 'remove_user_company',
	                    	);
						}
					}
				}
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus user. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	function doRemoveParent( $id ) {	
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'User.id' => $id,
			),
		);

		$value = $this->getData('all', $options, array(
			'status' => 'all',
			'company' => true,
			'admin' => true,
		));

		if ( !empty($value) ) {
			$value = $this->getMergeList($value, array(
				'contain' => array(
					'UserCompanyConfig',
				),
			));

			$name = Set::extract('/User/full_name', $value);
			$emails = Set::extract('/User/email', $value);

			$name = implode(', ', $name);
			$default_msg = sprintf(__('menghapus user %s'), $name);

			$flag = $this->updateAll(array(
				'User.parent_id' => 0,
	    		'User.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'User.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
				);

				if( !empty($value) ) {
					foreach ($value as $key => $user) {
						$full_name = $this->filterEmptyField($user, 'User', 'full_name');
						$email = $this->filterEmptyField($user, 'User', 'email');

						$result['SendEmail'][] = array(
                        	'to_name' => $full_name,
                        	'to_email' => $email,
                        	'subject' => __('Notifikasi Pemberhentian'),
                        	'template' => 'remove_principle',
                        	'data' => $user,
                    	);
					}
				}
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus user. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	function getAgents ( $user_id, $list_agent = false, $findby = 'list', $limit = false, $options = array() ) {
		$logged_id = Configure::read('User.id');
		$logged_group = Configure::read('User.group_id');

		// get turunan divisi
		$data_arr = $this->getUserParent($logged_id);
		$is_sales = Common::hashEmptyField($data_arr, 'is_sales');
		$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
		// 

		$role_user = !empty($options['role']) ? $options['role'] : 'agent';
		$data_show = !empty($options['data_show']) ? $options['data_show'] : false;
		$skip_is_sales = !empty($options['skip_is_sales']) ? $options['skip_is_sales'] : false;
		$result = false;

		if( !empty($user_id) ) {
			if ( $logged_group == 3 || !empty($list_agent) ) { // USER GROUP = ADMIN
				switch ($role_user) {
					case 'agent':
						$group_id = 2;
						break;
					case 'principle':
						$group_id = 3;
						break;
					case 'admin':
						$group_id = 5;
						break;
					case 'all':
						$group_id = array(2,5);
						break;
					default:
						$group_id = 2;
						break;
				}

				$options = array(
					'conditions' => array(
						'User.parent_id' => $user_id,
						'User.group_id' => $group_id
					),
				);

				if($is_sales && ( empty($data_show) ) && empty($skip_is_sales) ){
					$options['conditions']['User.id'] = $user_ids;
				}

				if( $findby == 'list' ) {
					$options['fields'] = array(
						'User.id', 'User.id',
					);
				}
				if( !empty($limit) ) {
					$options['limit'] = $limit;
				}

				$result = $this->getData($findby, $options, array(
					'status' => 'active',
				));
				// debug($result);die();

			} else { // USER GROUP != ADMIN
				if( $findby == 'count' ) {
					$result = 0;
				} else {
					$result = $user_id;
				}
			}
		} else {
			$result = ( $findby == 'count' )?0:array();
		}

		return $result;
	}

	function getClientAgentCompanies(){
		$values = $this->UserClientRelation->getData('all', array(
			'conditions' => array(
				'UserClientRelation.primary' => 1,
			),
		));

		foreach( $values as $key => $value ) {
			$client_id = !empty($value['UserClientRelation']['user_id'])?$value['UserClientRelation']['user_id']:false;
			$agent_id = !empty($value['UserClientRelation']['agent_id'])?$value['UserClientRelation']['agent_id']:false;
			$company_id = !empty($value['UserClientRelation']['company_id'])?$value['UserClientRelation']['company_id']:false;

			$value = $this->UserClient->getMerge( $value, $client_id, $company_id );
			$value = $this->getMerge( $value, $client_id );
			$value = $this->UserCompany->getMerge( $value, $company_id );
			
			$values[$key] = $value;
		}

		return $values;
	}

	function populers ( $limit = 3, $param_query = null ) {
		$dataCompany = Configure::read('Config.Company.data');
		$company_id = $this->filterEmptyField($dataCompany, 'UserCompany', 'id');

    	$cacheName = __('User.Populers.%s', $company_id);
    	$cacheConfig = 'default';
		// $values = Cache::read($cacheName, $cacheConfig);

		if( empty($values) || !empty($param_query) ) {
			$this->Property->virtualFields = false;
			$this->Property->virtualFields['cnt_property'] = 'COUNT(Property.id)';
            
            $parent_id = Configure::read('Principle.id');
            $agent_id = $this->Property->User->getAgents($parent_id, true, 'list', false, array(
            	'skip_is_sales' => true,
            ));
			$values = $this->Property->getData('all', array(
				'conditions' => array(
					'Property.user_id' => $agent_id,
				),
				'order' => array(
					'cnt_property' => 'DESC',
				),
				'limit' => $limit,
				'group' => array(
					'Property.user_id',
				),
			), array(
				'company' => false,
				'status' => 'active-pending'
			));

			if( !empty($values) ) {
				foreach ($values as $key => $value) {
	        		$user_id = Common::hashEmptyField($value, 'Property.user_id');

					$value = $this->getMerge($value, $user_id);
					$value = $this->UserProfile->getMerge( $value, $user_id, true );
	            	$value = $this->UserConfig->getMerge( $value, $user_id );

	            	$values[$key] = $value;
				}
			}
			
			Cache::write($cacheName, $values, $cacheConfig);
		}

		return $values;
	}

    function _callUserCount( $principle_id = false, $group_id = false ){
        $default_options = array(
            'conditions' => array(
                'User.parent_id' => $principle_id,
            ),
        );

        if( !empty($group_id) ) {
        	$default_options['conditions']['User.group_id'] = $group_id;
        }
        
        return $this->getData('count', $default_options, array(
            'status' => 'semi-active'
        ));
    }

	public function doSaveProfession( $user_id, $user, $data ) {
		$result = false;

		if ( !empty($data) ) {
			$dataClientType = $this->UserClientType->getDataModel($data);
			$dataPropertyType = $this->UserPropertyType->getDataModel($data);
			$dataSpecialist = $this->UserSpecialist->getDataModel($data);
			$dataLanguage = $this->UserLanguage->getDataModel($data);
			$dataAgentCertificate = $this->UserAgentCertificate->getDataModel($data);

			// SET PROGRESS
			$user_client_type_field = !empty($data['UserClientType']['client_type_id']) ? count(array_filter($data['UserClientType']['client_type_id'])) : false;
			$user_property_type_field = !empty($data['UserPropertyType']['property_type_id']) ? count(array_filter($data['UserPropertyType']['property_type_id'])) : false;
			$user_specialist_field = !empty($data['UserSpecialist']['specialist_id']) ? count(array_filter($data['UserSpecialist']['specialist_id'])) : false;
			$user_language_field = !empty($data['UserLanguage']['language_id']) ? count(array_filter($data['UserLanguage']['language_id'])) : false;
			$user_agent_certificate_field = !empty($data['UserAgentCertificate']['agent_certificate_id']) ? count(array_filter($data['UserAgentCertificate']['agent_certificate_id'])) : false;

			if( $user_client_type_field ) {
				$user_client_type_field = 1;
			}
			if( $user_property_type_field ) {
				$user_property_type_field = 1;
			}
			if( $user_specialist_field ) {
				$user_specialist_field = 1;
			}
			if( $user_language_field ) {
				$user_language_field = 1;
			}
			if( $user_agent_certificate_field ) {
				$user_agent_certificate_field = 1;
			}

			$all_field = count($data['UserConfig']) + (count($data) - 1);
			$filled_field = count(array_filter($data['UserConfig'])) + $user_client_type_field + $user_property_type_field + $user_specialist_field + $user_language_field + $user_agent_certificate_field;
			$percentage = round(($filled_field / $all_field) * 100);
			$data['UserConfig']['progress_profession'] = $percentage;

            $clientTypeValidates = $this->UserClientType->doSave($dataClientType, false, false, true);
            $statusClientType = !empty($clientTypeValidates['status'])?$clientTypeValidates['status']:false;

            $propertyTypeValidates = $this->UserPropertyType->doSave($dataPropertyType, false, false, true);
            $statusPropertyType = !empty($propertyTypeValidates['status'])?$propertyTypeValidates['status']:false;

            $specialistValidates = $this->UserSpecialist->doSave($dataSpecialist, false, false, true);
            $statusSpecialist = !empty($specialistValidates['status'])?$specialistValidates['status']:false;

            $languageValidates = $this->UserLanguage->doSave($dataLanguage, false, false, true);
            $statusLanguage = !empty($languageValidates['status'])?$languageValidates['status']:false;

            $agentCertificateValidates = $this->UserAgentCertificate->doSave($dataAgentCertificate, false, false, true);
            $statusAgentCertificate = !empty($agentCertificateValidates['status'])?$agentCertificateValidates['status']:false;

			if ( $statusClientType == 'success' && $statusPropertyType == 'success' && $statusSpecialist == 'success' && $statusLanguage == 'success' && $statusAgentCertificate == 'success' ) {
                $this->UserClientType->doSave($dataClientType, false, $user_id);
                $this->UserPropertyType->doSave($dataPropertyType, false, $user_id);
                $this->UserSpecialist->doSave($dataSpecialist, false, $user_id);
                $this->UserLanguage->doSave($dataLanguage, false, $user_id);
                $this->UserAgentCertificate->doSave($dataAgentCertificate, false, $user_id);

				$save = $this->UserConfig->doSave($user, $data, $user_id);

				$result = array(
					'msg' => __('Sukses memperbarui data profesi Anda'),
					'status' => 'success',
				);
			} else {
				$result = array(
					'msg' => __('Gagal memperbarui data profesi Anda. Silahkan coba lagi'),
					'status' => 'error',
				);
			}
		} else {
			$result['data'] = $user;
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
    	$search = $this->filterEmptyField($data, 'named', 'search', false, array(
        	'addslashes' => true,
    	));
		$name = $this->filterEmptyField($data, 'named', 'name', false, array(
        	'addslashes' => true,
    	));
    	$group_id = $this->filterEmptyField($data, 'named', 'group_id', false, array(
        	'addslashes' => true,
    	));
		$parent_company = $this->filterEmptyField($data, 'named', 'parent_company', false, array(
        	'addslashes' => true,
    	));
		$parent = $this->filterEmptyField($data, 'named', 'parent', false, array(
        	'addslashes' => true,
    	));
		$company = $this->filterEmptyField($data, 'named', 'company', false, array(
        	'addslashes' => true,
    	));
		$email = $this->filterEmptyField($data, 'named', 'email', false, array(
        	'addslashes' => true,
    	));
		$phone = $this->filterEmptyField($data, 'named', 'phone', false, array(
        	'addslashes' => true,
    	));
		$address = $this->filterEmptyField($data, 'named', 'address', false, array(
        	'addslashes' => true,
    	));
		$principle_id = $this->filterEmptyField($data, 'named', 'principle_id', false, array(
        	'addslashes' => true,
    	));
    	$loginDateFrom = $this->filterEmptyField($data, 'named', 'last_login_from', false, array(
            'addslashes' => true,
        ));
        $loginDateTo = $this->filterEmptyField($data, 'named', 'last_login_to', false, array(
            'addslashes' => true,
        ));
		$website = $this->filterEmptyField($data, 'named', 'website', false, array(
        	'addslashes' => true,
    	));
		$phone_profile = $this->filterEmptyField($data, 'named', 'phone_profile', false, array(
        	'addslashes' => true,
    	));
		$no_hp = $this->filterEmptyField($data, 'named', 'no_hp', false, array(
        	'addslashes' => true,
    	));
		$no_hp_2 = $this->filterEmptyField($data, 'named', 'no_hp_2', false, array(
        	'addslashes' => true,
    	));
		$pin_bb = $this->filterEmptyField($data, 'named', 'pin_bb', false, array(
        	'addslashes' => true,
    	));
		$line = $this->filterEmptyField($data, 'named', 'line', false, array(
        	'addslashes' => true,
    	));
		$phone_company = $this->filterEmptyField($data, 'named', 'phone_company', false, array(
        	'addslashes' => true,
    	));
		$phone_company_2 = $this->filterEmptyField($data, 'named', 'phone_company_2', false, array(
        	'addslashes' => true,
    	));
		$fax_company = $this->filterEmptyField($data, 'named', 'fax_company', false, array(
        	'addslashes' => true,
    	));
		$gender = $this->filterEmptyField($data, 'named', 'gender', false, array(
        	'addslashes' => true,
    	));
		$contact_name = $this->filterEmptyField($data, 'named', 'contact_name', false, array(
        	'addslashes' => true,
    	));
		$contact_email = $this->filterEmptyField($data, 'named', 'contact_email', false, array(
        	'addslashes' => true,
    	));
    	$date_from = $this->filterEmptyField($data, 'named', 'date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = $this->filterEmptyField($data, 'named', 'date_to', false, array(
            'addslashes' => true,
        ));
    	$group = $this->filterEmptyField($data, 'named', 'group', false, array(
        	'addslashes' => true,
    	));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));
    	$transaction_from = Common::hashEmptyField($data, 'named.transaction_from', false, array(
            'addslashes' => true,
        ));
        $transaction_to = Common::hashEmptyField($data, 'named.transaction_to', false, array(
            'addslashes' => true,
        ));
    	$log_view_from = $this->filterEmptyField($data, 'named', 'log_view_from', false, array(
            'addslashes' => true,
        ));
        $log_view_to = $this->filterEmptyField($data, 'named', 'log_view_to', false, array(
            'addslashes' => true,
        ));
        $user_company_status = $this->filterEmptyField($data, 'named', 'user_company_status', false, array(
            'addslashes' => true,
        ));

        $is_admin = Common::hashEmptyField($data, 'Report.is_admin');
        $is_super_admin = Configure::read('User.Admin.Rumahku');

    	$logView = strpos($sort, 'LogView.');
    	$logLogin = strpos($sort, 'LogLogin.');

		if( !empty($loginDateFrom) || !empty($log_view_from) || is_numeric($logView) || is_numeric($logLogin) ) {
			$this->unbindModel(
				array('hasMany' => array('LogView'))
			);
			$this->bindModel(array(
				'hasOne' => array(
					'LogView' => array(
						'className' => 'LogView',
						'foreignKey' => 'user_id',
						'conditions' => array(
							'LogView.type' => 'daily',
						),
					),
					'LogLogin' => array(
						'className' => 'LogView',
						'foreignKey' => 'user_id',
						'conditions' => array(
							'LogLogin.type' => 'login',
						),
					),
				),
			), false);
			
			if( is_numeric($logView) ) {
            	$this->LogView->virtualFields['created'] = 'MAX(LogView.created)';
				$default_options['contain'][] = 'LogView';
			} else if( is_numeric($logLogin) ) {
            	$this->LogLogin->virtualFields['created'] = 'MAX(LogLogin.created)';
				$default_options['contain'][] = 'LogLogin';
			}
		}
    	
		if( !empty($keyword) ) {
			$keyword = trim($keyword);
			$default_options['conditions']['OR'] = array(
				'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE' => '%'.$keyword.'%',
				'User.email LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($search) ) {
			$default_options['conditions']['OR'] = array(
				'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE' => '%'.$search.'%',
				'User.email LIKE' => '%'.$search.'%',
				'UserCompany.name LIKE' => '%'.$search.'%',
			);
			$default_options['contain'][] = 'UserCompany';
		}
		if( !empty($name) ) {
			$name = trim($name);
			$default_options['conditions']['CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE'] = '%'.$name.'%';
		}
		if( !empty($group_id) ) {
			$default_options['conditions']['User.group_id'] = trim($group_id);
		}
		if( !empty($company) ) {
			$default_options['conditions']['UserCompany.name LIKE'] = '%'.$company.'%';
			$default_options['contain'][] = 'UserCompany';
		}
		if( !empty($parent_company) ) {
			$default_options['conditions']['UserCompanyParent.name LIKE'] = '%'.$parent_company.'%';
			$default_options['contain'][] = 'UserCompanyParent';
		}
		if( !empty($email) ) {
			$email = trim($email);
			$default_options['conditions']['User.email LIKE'] = '%'.$email.'%';
		}
		if( !empty($phone) ) {
			$phone = trim($phone);
			$default_options['conditions']['OR'][]['UserProfile.phone LIKE'] = '%'.$phone.'%';
			$default_options['conditions']['OR'][]['UserProfile.no_hp LIKE'] = '%'.$phone.'%';
			$default_options['conditions']['OR'][]['UserProfile.no_hp_2 LIKE'] = '%'.$phone.'%';
			$default_options['contain'][] = 'UserProfile';
		}
		if( !empty($address) ) {
			$address = trim($address);
			$default_options['conditions']['UserProfile.address LIKE'] = '%'.$address.'%';
		}

		if( !empty($principle_id) && empty($is_admin) && !empty($is_super_admin) ) {
			if( !is_array($principle_id) ) {
				$principle_id = explode(',', $principle_id);
			}
			$default_options['conditions']['User.parent_id'] = $principle_id;
		}
		if( !empty($loginDateFrom) ) {
			$default_options['conditions']['DATE_FORMAT(LogLogin.created, \'%Y-%m-%d\') >='] = $loginDateFrom;

			if( !empty($loginDateTo) ) {
				$default_options['conditions']['DATE_FORMAT(LogLogin.created, \'%Y-%m-%d\') <='] = $loginDateTo;
			}

			$default_options['contain'][] = 'LogLogin';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(User.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(User.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($website) ) {
			$default_options['conditions']['UserCompanyConfig.domain LIKE'] = '%'.$website.'%';
			$default_options['contain'][] = 'UserCompanyConfig';
		}
		if( !empty($phone_profile) ) {
			$default_options['conditions']['UserProfile.phone LIKE'] = '%'.$phone_profile.'%';
			$default_options['contain'][] = 'UserProfile';
		}
		if( !empty($no_hp) ) {
			$default_options['conditions']['UserProfile.no_hp LIKE'] = '%'.$no_hp.'%';
			$default_options['contain'][] = 'UserProfile';
		}
		if( !empty($no_hp_2) ) {
			$default_options['conditions']['UserProfile.no_hp_2 LIKE'] = '%'.$no_hp_2.'%';
			$default_options['contain'][] = 'UserProfile';
		}
		if( !empty($pin_bb) ) {
			$default_options['conditions']['UserProfile.pin_bb LIKE'] = '%'.$pin_bb.'%';
			$default_options['contain'][] = 'UserProfile';
		}
		if( !empty($line) ) {
			$default_options['conditions']['UserProfile.line LIKE'] = '%'.$line.'%';
			$default_options['contain'][] = 'UserProfile';
		}
		if( !empty($phone_company) ) {
			$default_options['conditions']['UserCompany.phone LIKE'] = '%'.$phone_company.'%';
			$default_options['contain'][] = 'UserCompany';
		}
		if( !empty($phone_company_2) ) {
			$default_options['conditions']['UserCompany.phone_2 LIKE'] = '%'.$phone_company_2.'%';
			$default_options['contain'][] = 'UserCompany';
		}
		if( !empty($fax_company) ) {
			$default_options['conditions']['UserCompany.fax LIKE'] = '%'.$fax_company.'%';
			$default_options['contain'][] = 'UserCompany';
		}
		if( !empty($gender) ) {
			$default_options['conditions']['User.gender_id'] = $gender;
		}
		if( !empty($contact_name) ) {
			$default_options['conditions']['UserCompany.contact_name LIKE'] = '%'.$contact_name.'%';
			$default_options['contain'][] = 'UserCompany';
		}
		if( !empty($contact_email) ) {
			$default_options['conditions']['UserCompany.contact_email LIKE'] = '%'.$contact_email.'%';
			$default_options['contain'][] = 'UserCompany';
		}
		if( !empty($group) ) {
			$default_options['conditions']['User.group_id'] = $group;
		}
		if( !empty($log_view_from) ) {
			$default_options['conditions']['DATE_FORMAT(LogView.created, \'%Y-%m-%d\') >='] = $log_view_from;

			if( !empty($log_view_to) ) {
				$default_options['conditions']['DATE_FORMAT(LogView.created, \'%Y-%m-%d\') <='] = $log_view_to;
			}
			$default_options['contain'][] = 'LogView';
		}
		if( !empty($parent) ) {
			$default_options['conditions']['CONCAT(UserGroupParent.first_name, " ", IFNULL(UserGroupParent.last_name, \'\')) like'] = '%'.$parent.'%';
			$default_options['contain'][] = 'UserGroupParent';
		}

		if( !empty($user_company_status) ) {
			switch ($user_company_status) {
				case 'expired':
					$default_options['conditions']['DATE_FORMAT(UserCompanyConfig.end_date, \'%Y-%m-%d\') <'] = date('Y-m-d');
					break;
				case 'active':
					$default_options['conditions']['DATE_FORMAT(UserCompanyConfig.end_date, \'%Y-%m-%d\') >='] = date('Y-m-d');
					break;
			}
			$default_options['contain'][] = 'UserCompanyConfig';
		}

		if( $sort == 'total_property' || $sort == 'total_property_sold' || $sort == 'total_ebrosur' || $sort == 'total_primary' || $sort == 'total_ebrosur_sold' || $sort == 'total_ebrosur_leased' || $sort == 'total_ebrosur_request' || $sort == 'total_client' || $sort == 'active' ) {
            if( $sort == 'total_ebrosur' ) {
            	$optionsEbrosur = array();
            	$this->virtualFields[$sort] = 'COUNT(UserCompanyEbrochure.id)';
            } else if( $sort == 'total_ebrosur_sold' ) {
            	$optionsEbrosur = array(
					'UserCompanyEbrochure.property_action_id' => 1,
				);
            	$this->virtualFields[$sort] = 'COUNT(UserCompanyEbrochure.id)';
            } else if( $sort == 'total_ebrosur_leased' ) {
            	$optionsEbrosur = array(
					'UserCompanyEbrochure.property_action_id' => 2,
				);
            	$this->virtualFields[$sort] = 'COUNT(UserCompanyEbrochure.id)';
            } else if( $sort == 'total_ebrosur_request' ) {
				$this->bindModel(array(
					'hasOne' => array(
						'EbrosurAgentRequest' => array(
							'className' => 'EbrosurAgentRequest',
							'foreignKey' => 'agent_id',
						),
					),
				), false);
				
            	$this->virtualFields[$sort] = 'COUNT(EbrosurAgentRequest.id)';
            	$default_options['contain'][] = 'EbrosurAgentRequest';
            } else if( $sort == 'total_primary' ) {
            	$primaryOptions = $this->Property->getData('paginate', false, array(
            		'status' => 'premium',
            		'company' => false,
        		));
        		$primaryConditions = !empty($primaryOptions['conditions'])?$primaryOptions['conditions']:false;

            	$this->unbindModel(
					array('hasMany' => array('Property'))
				);
				$this->bindModel(array(
					'hasOne' => array(
						'Property' => array(
							'className' => 'Property',
							'foreignKey' => 'user_id',
							'conditions' => $primaryConditions,
						)
					),
				), false);
				
            	$this->virtualFields[$sort] = 'COUNT(Property.id)';
            	$default_options['contain'][] = 'Property';
            
            } else if($sort == 'total_client'){
	            $clientOptions = $this->UserClient->getData('paginate', false, array(
            		'company' => false,
        		));
        		$clientOptions = !empty($clientOptions['conditions'])?$clientOptions['conditions']:false;

        		$this->unbindModel(
					array('hasMany' => array('UserClient'))
				);
				$this->bindModel(array(
					'hasOne' => array(
						'UserClient' => array(
							'className' => 'UserClient',
							'foreignKey' => 'user_id',
							'conditions' => $clientOptions,
						)
					),
				), false);

				$this->virtualFields[$sort] = 'COUNT(UserClient.id)';
            	$default_options['contain'][] = 'UserClient';

            } else if($sort == 'active'){
            	$default_options['order'] = array(
            		'User.status' => $direction,
            	);
            } else {
	            if( $sort == 'total_property' ) {
					$bindArr = array(
						'Property' => array(
							'className' => 'Property',
							'foreignKey' => 'user_id',
						)
					);
					$activeOptions = $this->Property->getData('paginate', false, array(
						'status' => 'active-pending',
						'company' => false,
					));

					$activeConditions = !empty($activeOptions['conditions'])?$activeOptions['conditions']:false;
					
					if( !empty($transaction_from) ) {
	            		$activeConditions = array_merge(array(
	        				'DATE_FORMAT(Property.created, \'%Y-%m-%d\') >=' => $transaction_from,
	        				'DATE_FORMAT(Property.created, \'%Y-%m-%d\') <=' => $transaction_to,
	        			), $activeConditions);
					}

					$bindArr['Property']['conditions'] = $activeConditions;

					$this->unbindModel(
						array('hasMany' => array('Property'))
					);
					$this->bindModel(array(
						'hasOne' => $bindArr,
					), false);
	            	$default_options['contain'][] = 'Property';
	            	$this->virtualFields[$sort] = 'COUNT(Property.id)';
	            } else {
					$this->bindModel(array(
						'hasOne' => array(
							'PropertySold' => array(
								'className' => 'PropertySold',
								'foreignKey' => 'sold_by_id',
								'conditions' => array(
									'PropertySold.status' => 1,
								),
							)
						),
					), false);
	            	$this->virtualFields[$sort] = 'COUNT(PropertySold.id)';
	            	$default_options['contain'][] = 'PropertySold';
	            }
	        }

	        if( in_array($sort, array( 'total_ebrosur', 'total_ebrosur_sold', 'total_ebrosur_leased' )) ) {
				if( !empty($transaction_from) ) {
            		$optionsEbrosur = array_merge(array(
        				'DATE_FORMAT(UserCompanyEbrochure.created, \'%Y-%m-%d\') >=' => $transaction_from,
        				'DATE_FORMAT(UserCompanyEbrochure.created, \'%Y-%m-%d\') <=' => $transaction_to,
        			), $optionsEbrosur);
				}

            	$this->unbindModel(
					array('hasMany' => array('UserCompanyEbrochure'))
				);
				$this->bindModel(array(
					'hasOne' => array(
						'UserCompanyEbrochure' => array(
							'className' => 'UserCompanyEbrochure',
							'foreignKey' => 'user_id',
							'conditions' => array_merge(array(
								'UserCompanyEbrochure.status' => 1,
							), $optionsEbrosur),
						),
					),
				), false);
        		$default_options['contain'][] = 'UserCompanyEbrochure';
	        }

            $default_options['order'][$sort] = $direction;
            $default_options['group'] = array(
            	'User.id',
            );
        } else if( !empty($sort) ) {
        	$companyParent = strpos($sort, 'UserCompanyParent.');
        	$company = strpos($sort, 'UserCompany.');
        	$group = strpos($sort, 'Group.');
        	$parent = strpos($sort, 'Parent.');
        	$userConfig = strpos($sort, 'UserConfig.');
        	$userProfile = strpos($sort, 'UserProfile.');
        	$UserCompanyConfig = strpos($sort, 'UserCompanyConfig.');

        	if( is_numeric($companyParent) ) {
	            $default_options['contain'][] = 'UserCompanyParent';
	        } else if( is_numeric($company) ) {
	            $default_options['contain'][] = 'UserCompany';
	        } else if( is_numeric($parent) ) {
	            $default_options['contain'][] = 'Parent';
	        } else if( is_numeric($userConfig) ) {
	            $default_options['contain'][] = 'UserConfig';
	        } else if( is_numeric($userProfile) ) {
	            $default_options['contain'][] = 'UserProfile';
	        } else if( is_numeric($UserCompanyConfig) ) {
	            $default_options['contain'][] = 'UserCompanyConfig';
	        } else if( is_numeric($group) ){
	        	$default_options['contain'][] = 'Group';
	        }
        }

        if( !empty($default_options['contain']) && in_array('UserCompanyParent', $default_options['contain']) ) {
			$this->bindModel(array(
				'hasOne' => array(
					'UserCompanyParent' => array(
						'className' => 'UserCompany',
						'foreignKey' => false,
						'conditions' => array(
							'UserCompanyParent.user_id = User.parent_id',
							'UserCompanyParent.user_id <>' => 0,
						),
					)
				),
			), false);
        }

		return $default_options;
	}

	function getListSales(){
		return $this->getData('list', array(
			'conditions' => array(
				'User.group_id' => array( 13, 14 ),
			),
			'fields' => array(
				'User.id', 'User.full_name'
			)
		), 'active');
	}

	function getListAdmin( $array_values = false, $include_principle = false ){
		$parentID		= Configure::read('Principle.id');
		$userGroupID	= Configure::read('Config.Company.data.User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $userGroupID);
		$values			= array();

		if(empty($isIndependent) && $parentID){
			$values = $this->getData('list', array(
				'conditions' => array(
					'User.group_id' => 5,
					'User.parent_id' => $parentID,
				),
				'fields' => array(
					'User.id', 'User.id'
				)
			), array(
				'status' => 'semi-active',
			));

			$values = array_unique($values);

			if( !empty($include_principle) ) {
				$values[] = $parentID;
			}

			if( !empty($array_values) ) {
				$values = array_values($values);
			}
		}

		return $values;
	}

	function getEmailAdmin(){
		$values = $this->getData('list', array(
			'conditions' => array(
				'OR' => array(
					array(
						'User.group_id' => 5,
						'User.parent_id' => Configure::read('Principle.id'),
					),
					array(
						'User.id' => Configure::read('Principle.id'),
					),
				),
			),
			'fields' => array(
				'User.id', 'User.email'
			)
		), array(
			'status' => 'semi-active',
		));
		$values = array_unique($values);

		return $values;
	}

    public function getDataList($data, $options = false) {
    	$is_full = isset($options['is_full']) ? $options['is_full'] : true;

    	if(isset($options['is_full'])){
    		unset($options['is_full']);
    	}

        if( !empty($data) ) {
            if( !empty($data['User']) ) {
            	$id = !empty($data['User']['id'])?$data['User']['id']:false;
                $data = $this->UserProfile->getMerge( $data, $id, true );

                if(in_array('Parent', $options)){
                	$parent_id = !empty($data['User']['parent_id'])?$data['User']['parent_id']:false;
                	$data = $this->UserCompany->getMerge( $data, $parent_id );
                }
            } else {
                foreach ($data as $key => $value) {
            		$id = !empty($value['User']['id'])?$value['User']['id']:false;
            		$parent_id = !empty($value['User']['parent_id'])?$value['User']['parent_id']:false;

            		if($is_full){
            			$value = $this->UserProfile->getMerge( $value, $id, true );
	                	$value = $this->UserConfig->getMerge( $value, $id );
            		}

                	if( !empty($options) ) {
                		foreach ($options as $idx => $modelName) {
                			switch ($modelName) {
                				case 'Parent':
                					$value = $this->UserCompany->getMerge( $value, $parent_id );
                					break;
                			}
                		}
                	}

                	$data[$key] = $value;
                }
            }
        }

        return $data;
    }

    function doMessageRegister ( $data, $msg_id = false, $validates = false ) {
    	if( !empty($data) ) {
    		$this->set($data);
            $result = $this->validates();

            if( !empty($result) ) {
                $this->UserProfile->set($data);
                $result = $this->UserProfile->validates();

            	if( !empty($result) && empty($validates) ) {
		            if( $this->save() ) {
			            $user_id = $this->id;

			            $data['UserProfile']['user_id'] = $user_id;
			            $this->UserProfile->save($data);

			            if( !empty($user_id) ) {
			            	$this->Message->updateAll(array(
			            		'Message.from_id' => $user_id,
			            	), array(
			            		'Message.id' => $msg_id,
		            		));
			            }
			        }
			    }
            }
		    
		    return $result;
        } else {
        	return true;
        }
    }

    public function doEditEmail( $user_id, $user, $data, $data_company = false ) {
		$result = false;
		$default_msg = __('memperbarui Email');

		if ( !empty($data) ) {
			
			$full_name = !empty($user['User']['full_name'])?$user['User']['full_name']:false;
			$this->set($data);

			if ( $this->validates() ) {
				$this->id = $user_id;
				$old_email = !empty($user['User']['email'])?$user['User']['email']:false;
				$new_email = !empty($data['User']['email'])?$data['User']['email']:false;

				if( $this->save($data) ) {
					$msg = sprintf(__('Berhasil %s %s dari %s menjadi %s'), $default_msg, $full_name, $old_email, $new_email);
					$company_name = !empty($data_company['UserCompany']['name'])?$data_company['UserCompany']['name']:false;

					$dataEmail = array(
						'old_email' => $old_email,
						'new_email' => $new_email,
					);

					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'RefreshAuth' => array(
							'id' => $user_id,
						),
						'Log' => array(
							'activity' => $msg,
							'old_data' => $user,
							'document_id' => $user_id,
						),
						'SendEmail' => array(
                        	'to_name' => $full_name,
                        	'to_email' => array($old_email, $new_email),
                        	'subject' => sprintf(__('Informasi akun %s Anda telah diperbarui'), $company_name),
                        	'template' => 'change_email',
                        	'data' => $dataEmail,
	                    ),
                    	'data' => $data,
					);
				} else {
					$msg = sprintf(__('Gagal %s %s'), $default_msg, $full_name);
					$result = array(
						'msg' => $msg,
						'status' => 'error',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $user,
							'document_id' => $user_id,
							'error' => 1,
						),
                    	'data' => $data,
						'validationErrors' => $this->validationErrors,
					);
				}
			} else {
				$result = array(
					'msg' => sprintf(__('Gagal %s'), $default_msg),
					'status' => 'error',
                	'data' => $data,
					'validationErrors' => $this->validationErrors,
				);
			}
		} else {
			$result['data'] = $user;
		}

		return $result;
	}

	public function doEditPassword( $id, $user, $data ) {
		$result = false;
		$default_msg = __('memperbarui password');

		if ( !empty($data) ) {
			$email = !empty($user['User']['email'])?$user['User']['email']:false;
			$full_name = !empty($user['User']['full_name'])?$user['User']['full_name']:false;
			$new_password = !empty($data['User']['new_password'])?$data['User']['new_password']:false;
			$new_password_ori = !empty($data['User']['new_password_ori'])?$data['User']['new_password_ori']:false;

			$this->set($data);
			$this->dataValidation = array(
				'User' => $user['User'],
				'password' => $data['User']['current_password'],
			);
			
			if ( $this->validates() && !empty($new_password) ) {
				$this->id = $id;
				$this->set('password', $new_password);

				if( $this->save() ){
					$msg = sprintf(__('Berhasil %s %s'), $default_msg, $full_name);
					$dataEmail = array_merge_recursive($user, $data);

					$result = array(
						'msg' => sprintf(__('Sukses %s'), $default_msg),
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $user,
							'document_id' => $id,
						),
						'SendEmail' => array(
                        	'to_name' => $full_name,
                        	'to_email' => $email,
                        	'subject' => __('Perubahan password Akun'),
                        	'template' => 'change_password',
                        	'data' => $dataEmail,
	                    ),
                    	'data' => $data,
					);
				} else {
					$msg = sprintf(__('Gagal %s %s'), $default_msg, $full_name);
					$result = array(
						'msg' => $msg,
						'status' => 'error',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $user,
							'document_id' => $id,
							'error' => 1,
						),
                    	'data' => $data,
						'validationErrors' => $this->validationErrors,
					);
				}
			} else {
				$result = array(
					'msg' => sprintf(__('Gagal %s'), $default_msg),
					'status' => 'error',
                	'data' => $data,
					'validationErrors' => $this->validationErrors,
				);
			}
		} else {
			$result['data'] = $user;
		}

		return $result;
	}

	function addClient ( $data, $modelName = 'Property', $fieldName = 'client_id', $fieldAgent = 'user_id' ) {
		$agent_id = $this->filterEmptyField($data, $modelName, $fieldAgent, 0);
		$company_id = $this->filterEmptyField($data, $modelName, 'company_id', Configure::read('Principle.id'));

		if( empty($data[$modelName][$fieldName]) && !empty($data[$modelName]['client_email']) ) {
			$email = $this->filterEmptyField($data, $modelName, 'client_email');
			$clientArr = !empty($data[$modelName]['client_name'])?explode(' ', $data[$modelName]['client_name']):false;
			$client_hp = $this->filterEmptyField($data, $modelName, 'client_hp');
			$client_job_type = $this->filterEmptyField($data, $modelName, 'client_job_type_id');
			$code = $this->filterEmptyField($data, $modelName, 'client_code');
			$password = $this->filterEmptyField($data, $modelName, 'client_auth_password');
			$address = $this->filterEmptyField($data, $modelName, 'address');
			$birthday = $this->filterEmptyField($data, $modelName, 'birthday');
			$birthplace = $this->filterEmptyField($data, $modelName, 'birthplace');
			$ktp = $this->filterEmptyField($data, $modelName, 'ktp');
			$gender_id = $this->filterEmptyField($data, $modelName, 'gender_id', null);
			$region_id = $this->filterEmptyField($data, $modelName, 'region_id');
			$city_id = $this->filterEmptyField($data, $modelName, 'city_id');
			$subarea_id = $this->filterEmptyField($data, $modelName, 'subarea_id');
			$zip = $this->filterEmptyField($data, $modelName, 'zip');
			$status_marital = $this->filterEmptyField($data, $modelName, 'status_marital');

			$first_name = '';
			$last_name = '';

			if( !empty($clientArr) ) {
				if( !empty($clientArr[0]) ) {
					$first_name = $clientArr[0];
					unset($clientArr[0]);
				}
				if( !empty($clientArr[1]) ) {
					$last_name = implode(' ', $clientArr);
				}
			}

			$value = $this->getData('first', array(
				'conditions' => array(
					'User.email' => $email,
				),
			), array(
				'status' => 'semi-active',
			));
			$dataSave = array(
				'User' => array(
					'client_type_id' => 2,
					'group_id' => 10,
					'code' => $code,
					'email' => $email,
					'first_name' => $first_name,
					'last_name' => $last_name,
					'password' => $password,
					'active' => 1,
					'gender_id' => $gender_id,
				),
				'UserProfile' => array(
					'no_hp' => $client_hp,
					'job_type' => $client_job_type,
					'address' => $address,
					'birthday' => $birthday,
					'birthplace' => $birthplace,
					'ktp' => $ktp,
					'region_id' => $region_id,
					'city_id' => $city_id,
					'subarea_id' => $subarea_id,
					'zip' => $zip,
					'status_marital' => $status_marital,
				),
			);

			if( empty($value) ) {
				$this->create();

				if( $this->save($dataSave, false) ) {
					$user_id = $this->id;
				} else {
					$user_id = 0;
				}
			} else {
				$user_id = $value['User']['id'];
				$value = $this->UserProfile->getMerge( $value, $user_id );
				$value = $this->UserClient->getMergeClient($value, $user_id, Configure::read('Principle.id'), 'UserClient', array(
					'conditions' => array(
						'UserClient.agent_id' => $agent_id,
					),
				));
				$client_id = $this->filterEmptyField( $value, 'UserClient', 'id' );
			}

			if( empty($value['UserProfile']) && !empty($user_id) ) {
				$dataSave['UserProfile']['user_id'] = $user_id;
				$this->UserProfile->create();
				$this->UserProfile->set($dataSave);
				$this->UserProfile->save();
			}

			if( !empty($dataSave) ) {
				$dataClient['UserClient'] = array(	
					'company_id' => $company_id,
					'user_id' => $user_id,
					'agent_id' => $agent_id,
					'client_type_id' => 2,
					'token' => String::uuid(),
				);
				$dataClient['UserClient'] += $dataSave['User'];
				$dataClient['UserClient'] += $dataSave['UserProfile'];

				if( !empty($client_id) ) {
					$this->UserClient->id = $client_id;
				} else {
					$this->UserClient->create();

					if( in_array($modelName, array( 'CrmProject', 'Kpr' )) ) {
						$dataClient['UserClient']['client_type_id'] = 1;
					}
				}

				$this->UserClient->set($dataClient);
				$this->UserClient->save();
 				$data = Hash::insert($data, 'CrmProject.user_client_id', $this->UserClient->id);
			}

			if( !empty($user_id) ) {
				$data[$modelName][$fieldName] = $user_id;
				$this->UserClientRelation->doSave( $agent_id, $user_id );
			}
		} else if( !empty($data[$modelName][$fieldName]) ) {
			$client_id = $data[$modelName][$fieldName];
			$clientArr = !empty($data[$modelName]['client_name'])?explode(' ', $data[$modelName]['client_name']):false;
			$client_no_hp = !empty($data[$modelName]['client_hp'])?$data[$modelName]['client_hp']:false;

			$first_name = '';
			$last_name = '';

			if( !empty($clientArr) ) {
				if( !empty($clientArr[0]) ) {
					$first_name = $clientArr[0];
					unset($clientArr[0]);
				}
				if( !empty($clientArr[1]) ) {
					$last_name = implode(' ', $clientArr);
				}
			}

			if( in_array($modelName, array( 'Kpr' )) ) {
				$client_job_type = !empty($data[$modelName]['client_job_type_id'])?$data[$modelName]['client_job_type_id']:null;
				$gender_id = !empty($data[$modelName]['gender_id'])?$data[$modelName]['gender_id']:null;
				$birthplace = !empty($data[$modelName]['birthplace'])?$data[$modelName]['birthplace']:null;
				$address = !empty($data[$modelName]['address'])?$data[$modelName]['address']:null;
				$birthday = !empty($data[$modelName]['birthday'])?$data[$modelName]['birthday']:null;
				$ktp = !empty($data[$modelName]['ktp'])?$data[$modelName]['ktp']:null;
				$region_id = $this->filterEmptyField($data, $modelName, 'region_id');
				$city_id = $this->filterEmptyField($data, $modelName, 'city_id');
				$subarea_id = $this->filterEmptyField($data, $modelName, 'subarea_id');
				$zip = $this->filterEmptyField($data, $modelName, 'zip');
				$status_marital = $this->filterEmptyField($data, $modelName, 'status_marital');
				
				$dataSave = array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'no_hp' => $client_no_hp,
					'job_type' => $client_job_type,
					'gender_id' => $gender_id,
					'birthplace' => $birthplace,
					'address' => $address,
					'birthday' => $birthday,
					'ktp' => $ktp,
					'region_id' => $region_id,
					'city_id' => $city_id,
					'subarea_id' => $subarea_id,
					'zip' => $zip,
					'status_marital' => $status_marital,
				);
			} else {
				$dataSave = array(
					'first_name' => $first_name,
					'last_name' => $last_name,
					'no_hp' => $client_no_hp,
				);
			}

			$value = $this->UserClient->getData('first', array(
				'conditions' => array(
					'UserClient.user_id' => $client_id,
					'UserClient.company_id' => $company_id,
				),
				'fields' => array(
					'UserClient.id', 'UserClient.agent_id',
				),
			));

			if( !empty($value) ) {
				$current_agent_id = !empty($value['UserClient']['agent_id'])?$value['UserClient']['agent_id']:false;
				$user_client_id = !empty($value['UserClient']['id'])?$value['UserClient']['id']:false;

				if( !empty($user_client_id) ) {
	 				$data = Hash::insert($data, 'CrmProject.user_client_id', $user_client_id);
				}

				$this->UserClient->id = $user_client_id;
				$this->UserClient->save(array(
					'UserClient' => $dataSave,
				));

				if( $current_agent_id != $agent_id ) {
					$this->UserClientRelation->doSave( $agent_id, $client_id, 0 );
				}
			} else {
				$this->UserClient->create();
				$this->UserClient->save(array(
					'UserClient' => array_merge($dataSave, array(
						'company_id' => Configure::read('Principle.id'),
						'user_id' => $client_id,
						'agent_id' => $agent_id,
						'status' => 1,
					)),
				));
				
 				$data = Hash::insert($data, 'CrmProject.user_client_id', $this->UserClient->id);
				$this->UserClientRelation->doSave( $agent_id, $client_id );
			}

		//	force deleted client back to active
			$this->save(array(
				'User' => array(
					'id'		=> $client_id, 
					'deleted'	=> 0, 
					'active'	=> 1, 
				), 
			), false);
		}

		return $data;
	}

	function updateToken($user_id, $UserConfig_id, $token){
		if(!empty($UserConfig_id) && !empty($token)){
			$this->UserConfig->id = $UserConfig_id;
			
			$this->UserConfig->set(array(
				'user_id' => $user_id,
				'token' => $token,
			));

			return $this->UserConfig->save();
		}else{
			return false;
		}
	}

	function removeValidator(){
		$this->validator()->remove('photo');
		$this->validator()->remove('full_name');
		$this->UserProfile->validator()->remove('address');
		$this->UserProfile->validator()->remove('zip');
		$this->UserProfile->validator()->remove('no_hp');
	}

	function apiSave($data, $validate = false){
		if(!empty($data)){
			$id = false;
			$user['User'] = $this->filterEmptyField($data, 'User');
			$client['UserClient'] = $this->filterEmptyField($data, 'UserClient');

			if(!empty($user['User'])){
				if(empty($user['User']['id'])){
					$this->create();
				}else{
					$this->id = $user['User']['id'];
				}

				$this->save($user, false);
				$id = $this->id;
			}

			if(!empty($client['UserClient'])){
				$client['UserClient']['user_id'] = $id;

				if(empty($client['UserClient']['id'])){
					$this->UserClient->create();
				}else{
					$this->UserClient->id = $client['UserClient']['id'];
				}
				$this->UserClient->save($client, false);
			}
			return $id;
		}else{
			return false;
		}
	}

	function doSave( $data, $id = false ) {
		if ( !empty($id) ) {
			$this->id = $id;
		} else {
			$this->create();
		}

		if ( $this->save($data) ) {
			return $this->id;
		} else {
			return false;
		}
	}

	function _callVerifyToken( $token = false ) {
    	$verify = $this->UserConfig->getData('first', array(
			'conditions' => array(
				'UserConfig.token' => $token,
			),
		));

		if( !empty($verify) ){
			$user_id = !empty($verify['UserConfig']['user_id'])?$verify['UserConfig']['user_id']:false;
			$verify = $this->getMerge($verify, $user_id);
			
			return $verify;
		} else {
			return false;
		}
	}

	function deleteNonCompanies( $id ) {
		
		$result = false;
		$user = $this->getData('all', array(
        	'conditions' => array(
				'User.id' => $id,
			),
		));

		if ( !empty($user) ) {
			$default_msg = __('Menghapus user non companies');

			$flag = $this->updateAll(array(
				'User.deleted' => 1,
	    		'User.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'User.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus user. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	public function countChild($parentID = NULL, $groupID = NULL, $recursive = FALSE){
		$results = array();

		if(in_array($groupID, array(2, 3, 5)) === FALSE){
			return $results;
		}

		if($groupID == 3){
		//	directors want to count their principles
			$users = $this->getData('all', array(
				'conditions' => array(
					'User.parent_id'	=> $parentID, 
					'User.group_id'		=> $groupID, 
				)
			));

			$results['Principle']['count'] = count($users);

			if($recursive && $users){
				$parentID	= Set::extract('/User/id', $users);
				$groupID	= 5;

			//	recursive active, continue search admin
			}
			else{
				return $results;
			}
		}

		if($groupID == 5){
		//	principles want to count their admins
			$results['Admin']['count'] = $this->getData('count', array(
				'conditions' => array(
					'User.parent_id'	=> $parentID, 
					'User.group_id'		=> $groupID, 
				)
			));

			if($recursive){
				$groupID = 2;

			//	recursive active, continue search agent
			}
			else{
				return $results;
			}
		}

		if($groupID == 2){
		//	principles want to count their agents
			$results['Agent']['count'] = $this->getData('count', array(
				'conditions' => array(
					'User.parent_id'	=> $parentID, 
					'User.group_id'		=> $groupID, 
				)
			));
		}

		return $results;
	}

	function doSaveAll($data, $removeFieldValidators = array(), $model_arr = array('User', 'UserProfile')){
		$model = $this->name;

		if(!empty($model_arr)){
			foreach($model_arr AS $key => $modelName){
				$dataSave[$modelName] = $this->filterEmptyField($data, $modelName);
			}

			if(!empty($removeFieldValidators)){
				foreach($removeFieldValidators AS $modelTarget => $fields){
					foreach ($fields as $key => $field) {
						if($model == $modelTarget){
							$this->validator()->remove($field);
						}else{
							$this->$modelTarget->validator()->remove($field);
						}
					}
				}
			}

			if($this->saveAll($dataSave)){
				$result = array(
					'status' => 'success',
                	'id' => $this->id,
				);
			}else{
				$result = array(
					'status' => 'error',
					'data' => $data,
					'validationErrors' => $this->validationErrors,
				);
			}
			return $result;
		}
	}

	function apiSaveAll($data){
		$default_msg = __('melakukan %s User');

		if(!empty($data)){

			$id = $this->filterEmptyField($data, 'User', 'id');

			if(!empty($id)){
				$default_msg = sprintf($default_msg, __('mengubah'));
				$this->UserProfile->deleteAll(array(
					'UserProfile.user_id' => $id,
				));
			}else{
				$default_msg = sprintf($default_msg, __('menambah'));
			}

			if($this->saveAll($data, array('validate' => false))){
				$msg = sprintf(__('Berhasil %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'success',
                	'id' => $this->id,
					'Log' => array(
						'activity' => $msg,
						'document_id' => $this->id,
					),
				);
			}else{
				$result = array(
					'msg' => sprintf(__('Gagal %s'), $default_msg),
					'status' => 'error',
					'data' => $data,
				);
			}
		}else{
			$result = array(
				'msg' => sprintf(__('Gagal %s'), $default_msg),
				'status' => 'error',
				'data' => $data,
			);
		}
		return $result;
	}
	
	public function afterSave($created, $options = array()){
		$dataCompany = Configure::read('Config.Company.data');
		$company_id = $this->filterEmptyField($dataCompany, 'UserCompany', 'id');

		Cache::delete(__('User.Populers.%s', $company_id), 'default');
	}

	/* =============================================================
	   unpremium membership bundling rku user/agent
	   (spesial case apabila agent yg telah membayar sendiri).
	   user yang telah membayar sendiri, ketika unpremium maka status premiumnya ikut company
	   apabila company masih sebagai company premium
	===============================================================*/
	public function removePremiumUser($data = false){
		if($data){
			$id  = Common::hashEmptyField($data, 'User.id');
			$msg = __('mengatur ulang, paket premium user mengikuti settingan company.');

			$this->id = $id;
			$this->set('membership_package_id', NULL);

			if($this->save()){
				$msg	= sprintf(__('Berhasil %s,'), $msg);
				$error	= 0;
			}
			else{
				$msg	= sprintf(__('Gagal %s,'), $msg);
				$error	= 1;
			}
		}
		else{
			$msg	= __('User ini tidak memiliki status Premium.');
			$error	= 1;
			
		}

		$result	= array(
			'msg'		=> $msg, 
			'status'	=> $error ? 'error' : 'success', 
			'log'		=> array('activity' => $msg, 'document_id' => $id, 'error' => $error)
		);

		return $result;
	}


	function advanceDataUser($data, $user_id){
		/*count Properti*/
		$count_property = $this->Property->getData('count', array(
			'conditions' => array(
				'Property.user_id' => $user_id
			)
		));

		/*count Properti premium*/
		$count_property_premium = $this->Property->getData('count', array(
			'conditions' => array(
				'Property.user_id' => $user_id
			)
		), array(
			'status' => 'premium'
		));

		/*count Properti premium*/
		$count_property_sold = $this->Property->getData('count', array(
			'conditions' => array(
				'Property.user_id' => $user_id,
			)
		), array(
			'status' => 'sold'
		));

		/*count ebrosur*/
		$count_ebrochure = $this->UserCompanyEbrochure->getData('count', array(
			'conditions' => array(
				'UserCompanyEbrochure.user_id' => $user_id,
			)
		));

		$data['UserAdvanceInfo'] = array(
			'cnt_property' => $count_property,
			'cnt_property_premium' => $count_property_premium,
			'cnt_property_sold' => $count_property_sold,
			'cnt_ebrochure' => $count_ebrochure
		);

		return $data;
	}

	function getInfoParent($user_id){
		$user = $this->find('first', array(
			'conditions' => array(
				'User.id' => $user_id
			)
		));

		$result = array();
		$parent_id = $this->filterEmptyField($user, 'User', 'parent_id');
		
		if(!empty($user) && !empty($parent_id)){
			$result = $this->find('first', array(
				'conditions' => array(
					'User.id' => $parent_id
				)
			));

			$result = $this->UserCompanyConfig->getMerge($result, $parent_id);
			$result = $this->UserCompany->getMerge($result, $parent_id);
		}

		return $result;
	}

	function getCountUserInternal($params, $type = 'count', $company_id = false){
        if(!empty($params)){
            $list_client = $list_user = array();  

            foreach ($params as $field => $ids) {
                switch ($field) {
                    case 'groupClient':
                        if($ids){
                            $list_client = $this->UserClient->getData( 'list', array(
                                'conditions' => array(
                                    'UserClient.company_id' => !empty($company_id) ? $company_id : Configure::read('Principle.id'),
                                    'UserClient.status' => 1,
                                ),
                                'fields' => array(
                                    'User.id', 'User.email'
                                ),
                                'contain' => array(
                                    'User'
                                ),
                            ));
                        }
                        break;
                    case 'groupUser':
                        if($ids){
                            $list_user = $this->getData( 'list', array(
                                'conditions' => array(
                                    'User.group_id' => $ids,
                                ),
                                'fields' => array(
                                    'User.id', 'User.email'
                                ),
                            ), array(
                                'status' => 'semi-active',
                                'company' => true,
                            ));
                        }
                        break;
                }
            }
            
            $list = array_merge($list_client, $list_user);

            if($type == 'count'){
                $list = count($list);              
            }

            return $list;
        }
    }

    function getUserList($type = 'count', $data_arr = array(), $params = array()){
		$options = Common::hashEmptyField($data_arr, 'options', array());    	

		$userID = Common::hashEmptyField($params, 'named.user_id', Configure::read('Principle.id') );
		$userID = Common::hashEmptyField($data_arr, 'userID', $userID);

    	if($userID){
			$slug = Common::hashEmptyField($data_arr, 'slug');    	
			
			$options['conditions']['User.parent_id'] = $userID;

			switch ($slug) {
				case 'director':
					$options['conditions']['User.group_id <>'] = 3;
					$options['conditions']['Parent.group_id'] = 4;
					$options['contain']['Parent']['fields'] = array('Parent.*');
					break;
				case 'principal':
					$options['conditions']['User.group_id <>'] = 3;
					break;
			}

    	}

    	if($type <> 'conditions'){
    		return $this->getData( $type, $options, array(
    			'status' => array(
    				'active',
    				'non-active',
    				'non-activation',
    			),
    			'role' => 'user-general',
    		));
    	} else {
    		return $options;
    	}
    }

    function _callGetChilds ( $id , $principle_id = false) {
		$values = $this->getData('list', array(
			'conditions' => array(
				'User.superior_id' => $id,
				'User.parent_id' => $principle_id,
			),
			'fields' => array(
				'User.id', 'User.id',
			),
		));

		if( !empty($values) ) {
			$values = array_merge($values, $this->_callGetChilds($values, $principle_id));
		}

		return $values;
	}

    function getUserParent($user_id = false, $params = array()){
    	$principle_id = Configure::read('Principle.id');
    	$group_id = Common::hashEmptyField(Configure::read('User.data'), 'Group.id');

    	$type = Common::hashEmptyField($params, 'type', 'list');
    	$param_group_id = Common::hashEmptyField($params, 'param_group_id', array());
    	$fields = Common::hashEmptyField($params, 'fields', array('User.id', 'User.id'), array(
    		'isset' => true,
    	));

    	if($group_id <> 2 && $group_id > 20){
    		$count = 0;
    		$childList = $this->_callGetChilds($user_id, $principle_id);

    		if($childList){
    			$default_options = array(
	    			'conditions' => array(
	    				'User.id' => $childList,
	    			),
	    		);

	    		if($param_group_id){
	    			$default_options['conditions']['User.group_id'] = $param_group_id;
	    		}

	    		if($fields){
	    			$default_options['fields'] = $fields;
	    		}

	    		// ceck is_sales
	    		if(empty($param_group_id) || in_array(2, $param_group_id)){
	    			$sales_conditions = $default_options;
	    			$sales_conditions['conditions'][]['User.group_id'] = 2;
	    			$count = $this->getData('count', $sales_conditions);
	    		}

	    		if($param_group_id || $type == 'all'){
	    			$childList = $this->getData($type, $default_options);
	    		}
    		}

    		return array(
    			'user_ids' => $childList,
    			'is_sales' => ($count > 0) ? true : false,
    		);

    	} else if($group_id == 2) {
    		return array(
    			'user_ids' => $user_id,
    			'is_sales' => true,
    		);
    	} else {
    		return array(
    			'is_sales' => false,
    		);
    	}
    }

    function childGroup($groups, $group_id){
    	$values = $this->Group->GroupCompany->getData('all', array(
    		'conditions' => array(
    			'GroupCompany.parent_id' => $group_id,
    		),
    	));
    	$values = $this->Group->GroupCompany->getMergeList($values, array(
    		'contain' => array(
    			'Group',
    		),
    	));

    	if(!empty($values)){
    		foreach ($values as $key => $value) {
    			$child_group_id = Common::hashEmptyField($value, 'GroupCompany.group_id');
    			$values = $this->childGroup($values, $child_group_id);
    		}
    	}
    	return array_merge($groups, $values);
    }

    function getUserTree($default_options = array()){
    	$auth_group_id = Configure::read('User.group_id');

    	if(!empty($auth_group_id) && $auth_group_id > 20){
    		$auth_data = Configure::read('User.data');
    		$id = Common::hashEmptyField($auth_data, 'id');

    		$recordIDs = $this->getChildTree(array($id), array());

    		if(!empty($recordIDs)){
    			$default_options['conditions'][]['User.id'] = $recordIDs; 
    		}
    	}
    	return $default_options;
    }

    function getChildTree($superior_ids = false, $data = array()){

    	if($superior_ids){
    		foreach ($superior_ids as $key => $superior_id) {


		    	$lists = $this->getData('list', array(
		    		'conditions' => array(
		    			'User.superior_id' => $superior_id,
		    		),
		    	));

		    	$lists = $this->getChildTree($lists, $lists);
    		}
    	}

    	if(!empty($lists)){
    		$data = array_merge($data, $lists);
    	}

    	return $data;
    }

	public function getPersonalPageData($domain = null, $options = array()){
		$currentURL	= Router::url('/', true);
		$domain		= (string) empty($domain) ? $currentURL : $domain;
		$options	= (array) $options;

	//	build conditions
		$userID		= Common::hashEmptyField($options, 'user_id');
		$themeID	= Common::hashEmptyField($options, 'theme_id');
		$conditions	= array();

		if($userID){
			$conditions = array('UserConfig.user_id' => $userID);
		}
		else{
			$conditions	= array('UserConfig.personal_web_url LIKE' => $domain . '%');
		}

		$record	= $this->UserConfig->getData('first', array(
			'contain'		=> array('User', 'MembershipPackage'), 
			'conditions'	=> $conditions, 
		));

		$userID		= Common::hashEmptyField($record, 'User.id');
		$parentID	= Common::hashEmptyField($record, 'User.parent_id');
		$themeID	= $themeID ?: Common::hashEmptyField($record, 'UserConfig.theme_id');

		$record	= $this->UserProfile->getMerge($record, $userID);
		$record = $this->UserCompanyConfig->Theme->getMerge($record, $themeID, array('owner_type' => 'agent'));
		$record	= $this->UserCompanySetting->getMerge($record, $userID, $themeID);

		$userCompanyConfig = $this->UserCompanyConfig->getData('first', array(
			'conditions' => array(
				'UserCompanyConfig.user_id' => $parentID,
			),
		));

		$record['UserCompanyConfig'] = Common::hashEmptyField($userCompanyConfig, 'UserCompanyConfig');
		

		return $record;
	}

	public function updateUserRank($parentID = null, $userID = null, $options = array()){
		$parentID	= intval($parentID);
		$userID		= intval($userID);
		$options	= (array) $options;
		$periodDate	= Common::hashEmptyField($options, 'period_date');
		$elements	= Common::hashEmptyField($options, 'elements', array(
			'status'	=> 'active', 
			'role'		=> 'agent', 
		));

		if($parentID){
			$elements['company'] = $parentID;
		}

		$currentDate	= strtotime(date('Y-m-d'));
		$periodDate		= strtotime($periodDate);
		$periodDate		= $periodDate > 0 ? $periodDate : $currentDate;

		$periodStart	= date('Y-m-01', $periodDate);
		$periodEnd		= date('Y-m-t', $periodDate);
		$options		= array('limit' => null);

		if($userID){
			$options['conditions']['User.id'] = $userID;
		}

		$users	= $this->getData('all', $options, $elements);
		$data	= array();

		if($users){
			$arrUserID = Hash::extract($users, '{n}.User.id');

		//	filter date sengaja di paksa jadi tanggal 1
			$priceMeasureField	= 'case when coalesce(Property.price_measure, 0) > 0 then Property.price_measure else coalesce(Property.price, 0) end';
			$filterDateField	= '
				date_format(case when Property.publish_date is null or date_format(Property.publish_date, "%Y-%m-%d") = "000-00-00" then 
					Property.created
				else 
					Property.publish_date
				end, "%Y-%m-%d")
			';

			$this->Property->virtualFields = array(
				'property_count'	=> 'count(Property.id)', 
				'price_measure'		=> sprintf('sum(%s)', $priceMeasureField), 
				'price_measure_min'	=> sprintf('min(%s)', $priceMeasureField), 
				'price_measure_max'	=> sprintf('max(%s)', $priceMeasureField), 
				'price_measure_avg'	=> sprintf('sum(%s) / count(Property.id)', $priceMeasureField), 
				'filter_date'		=> $filterDateField, 
			);

			$order = array(
				'Property.user_id', 
				'Property.property_action_id', 
				'Property.property_type_id', 
				'PropertyAddress.region_id', 
				'PropertyAddress.city_id', 
				'PropertyAddress.subarea_id', 
			);

			$groups	= $order;
			$fields	= array_merge($groups, array(
				'Property.property_count', 
				'Property.price_measure', 
				'Property.price_measure_min', 
				'Property.price_measure_max', 
				'Property.price_measure_avg', 
				'Property.filter_date', 
				'PropertyAddress.region_id', 
				'PropertyAddress.city_id', 
				'PropertyAddress.subarea_id', 
			));

			$properties = $this->Property->getData('all', array(
				'fields'		=> $fields, 
				'order'			=> $order, 
				'group'			=> $groups, 
				'contain'		=> array('PropertyAddress'), 
				'conditions'	=> array(
					'Property.user_id' => $arrUserID, 
					'or' => array(
					//	untuk total listing di ambil as of date
						sprintf('%s <=', $filterDateField) => $periodEnd, 
					), 
				), 
			), array(
				'company'	=> false, 
				'mine'		=> true, 
				'status'	=> 'active-or-sold', 
			));

		//	COUNT TOTAL PROPERTY SOLD (AS OF MONTH) =======================================================================================

			$search		= array('Property', 'publish_date');
			$replace	= array('PropertySold', 'sold_date');

			$priceMeasureField	= 'coalesce(PropertySold.price_sold, 0) * coalesce(PropertySold.rate, 1)';
			$filterDateField	= str_replace($search, $replace, $filterDateField);

			$this->Property->virtualFields = array(
				'property_count'	=> 'count(Property.id)', 
				'price_measure'		=> sprintf('sum(%s)', $priceMeasureField), 
				'price_measure_min'	=> sprintf('min(%s)', $priceMeasureField), 
				'price_measure_max'	=> sprintf('max(%s)', $priceMeasureField), 
				'price_measure_avg'	=> sprintf('sum(%s) / count(Property.id)', $priceMeasureField), 
				'filter_date'		=> $filterDateField, 
			); 

			$propertySolds = $this->Property->getData('all', array(
				'order'			=> $order, 
				'group'			=> $groups, 
				'fields'		=> $fields, 
				'contain'		=> array('PropertySold', 'PropertyAddress'), 
				'conditions'	=> array(
				//	untuk total penjualan di ambil hanya untuk bulan aktif
					'Property.user_id'										=> $arrUserID, 
					sprintf('%s >=', $filterDateField) => $periodStart, 
					sprintf('%s <=', $filterDateField) => $periodEnd, 
				), 
			), array(
				'company'	=> false, 
				'mine'		=> false, 
				'status'	=> 'sold', 
			));

		//	===============================================================================================================================

		//	PREPARE SAVE DATA =============================================================================================================

			$periodYear		= date('Y', $periodDate);
			$periodMonth	= date('m', $periodDate);
			$currentDate	= date('Y-m-d H:i:s');

			foreach($users as $user){
				$userID			= Common::hashEmptyField($user, 'User.id');
				$parentID		= Common::hashEmptyField($user, 'User.parent_id');

				if($properties){
				//	loop total listing (available / sold) sebagai total listing (as of date)
					foreach($properties as $propertyKey => $property){
						$propertyUserID	= Common::hashEmptyField($property, 'Property.user_id');

						if($propertyUserID == $userID){
							$actionID			= Common::hashEmptyField($property, 'Property.property_action_id');
							$typeID				= Common::hashEmptyField($property, 'Property.property_type_id');
							$regionID			= Common::hashEmptyField($property, 'PropertyAddress.region_id');
							$cityID				= Common::hashEmptyField($property, 'PropertyAddress.city_id');
							$subareaID			= Common::hashEmptyField($property, 'PropertyAddress.subarea_id');

							$propertyCount		= Common::hashEmptyField($property, 'Property.property_count', 0);
							$priceMeasure		= Common::hashEmptyField($property, 'Property.price_measure', 0);
							$priceMeasureMin	= Common::hashEmptyField($property, 'Property.price_measure_min', 0);
							$priceMeasureMax	= Common::hashEmptyField($property, 'Property.price_measure_max', 0);
							$priceMeasureAvg	= Common::hashEmptyField($property, 'Property.price_measure_avg', 0);

							$soldPropertyCount		= 0;
							$soldPriceMeasure		= 0;
							$soldPriceMeasureMin	= 0;
							$soldPriceMeasureMax	= 0;
							$soldPriceMeasureAvg	= 0;

							if($propertySolds){
								foreach($propertySolds as $propertySoldKey => $propertySold){
									$soldActionID	= Common::hashEmptyField($propertySold, 'Property.property_action_id');
									$soldTypeID		= Common::hashEmptyField($propertySold, 'Property.property_type_id');
									$soldRegionID	= Common::hashEmptyField($propertySold, 'PropertyAddress.region_id');
									$soldCityID		= Common::hashEmptyField($propertySold, 'PropertyAddress.city_id');
									$soldSubareaID	= Common::hashEmptyField($propertySold, 'PropertyAddress.subarea_id');

									if($actionID == $soldActionID && $typeID == $soldTypeID && $subareaID == $soldSubareaID){
										$soldPropertyCount		= Common::hashEmptyField($propertySold, 'Property.property_count', 0);
										$soldPriceMeasure		= Common::hashEmptyField($propertySold, 'Property.price_measure', 0);
										$soldPriceMeasureMin	= Common::hashEmptyField($propertySold, 'Property.price_measure_min', 0);
										$soldPriceMeasureMax	= Common::hashEmptyField($propertySold, 'Property.price_measure_max', 0);
										$soldPriceMeasureAvg	= Common::hashEmptyField($propertySold, 'Property.price_measure_avg', 0);

									//	unset jadi nanti looping berikut nya udah ga berat
										unset($propertySold[$propertySoldKey]);
									}
								}
							}

							$agentRankID = $this->AgentRank->field('AgentRank.id', array(
								'AgentRank.parent_id'			=> $parentID, 
								'AgentRank.user_id'				=> $userID, 
								'AgentRank.property_action_id'	=> $actionID, 
								'AgentRank.property_type_id'	=> $typeID, 
								'AgentRank.region_id'			=> $regionID, 
								'AgentRank.city_id'				=> $cityID, 
								'AgentRank.subarea_id'			=> $subareaID, 
								'AgentRank.period_year'			=> $periodYear, 
								'AgentRank.period_month'		=> $periodMonth, 
							));

							$data[$propertyKey]	= array(
								'AgentRank' => array(
									'id'							=> $agentRankID, 
									'parent_id'						=> $parentID,
									'parent_id'						=> $parentID,
									'user_id'						=> $userID,
									'property_action_id'			=> $actionID,
									'property_type_id'				=> $typeID,
									'region_id'						=> $regionID, 
									'city_id'						=> $cityID, 
									'subarea_id'					=> $subareaID, 

								//	property summary
									'property_count'				=> $propertyCount,
									'price_measure'					=> $priceMeasure,
									'price_measure_min'				=> $priceMeasureMin,
									'price_measure_max'				=> $priceMeasureMax,
									'price_measure_average'			=> $priceMeasureAvg,

								//	sold property summary
									'sold_property_count'			=> $soldPropertyCount,
									'sold_price_measure'			=> $soldPriceMeasure,
									'sold_price_measure_min'		=> $soldPriceMeasureMin,
									'sold_price_measure_max'		=> $soldPriceMeasureMax,
									'sold_price_measure_average'	=> $soldPriceMeasureAvg,

									'period_year'					=> $periodYear,
									'period_month'					=> $periodMonth,
								),
							);

							if($agentRankID){
								$data[$propertyKey] = Hash::insert($data[$propertyKey], 'AgentRank.modified', $currentDate);
							}
							else{
								$data[$propertyKey] = Hash::insert($data[$propertyKey], 'AgentRank.created', $currentDate);
							}

						//	unset jadi nanti looping berikut nya udah ga berat
							unset($properties[$propertyKey]);
						}
					}
				}
			}

			if($data){
				$flag = $this->AgentRank->saveAll($data, array(
					'validate' => 'only', 
				));

				if($flag){
				//	actual save
					$this->AgentRank->saveAll($data);

					$status		= 'success';
					$message	= 'Berhasil meng-generate data';
				}
				else{
					$status		= 'success';
					$message	= 'Gagal meng-generate data';
				}
			}
			else{
				$status		= 'error';
				$message	= 'Tidak ada data untuk disimpan';
			}

		//	===============================================================================================================================			
		}
		else{
			$status		= 'error';
			$message	= 'User tidak ditemukan';
		}

		$result = array(
			'status'	=> $status,
			'msg'		=> $message,
			'data'		=> $data, 
			'Log'		=> array(
				'error'			=> $status == 'error', 
				'activity'		=> $message,
			),
		);

		return $result;
	}

	public function createFromSocialProfile($incomingProfile){
	//	check to ensure that we are not using an email that already exists
		$existingUser = $this->find('first', array(
			'conditions' => array(
				'email' => $incomingProfile['SocialProfile']['email'], 
			), 
		));

		if($existingUser){
		//	this email address is already associated to a member
			return $existingUser;
		}

	//	brand new user
		$socialUser['User']['email'] 	= $incomingProfile['SocialProfile']['email'];
		$socialUser['User']['username']	= str_replace(' ', '_',$incomingProfile['SocialProfile']['display_name']);
		$socialUser['User']['role']		= 'bishop'; // by default all social logins will have a role of bishop
		$socialUser['User']['password']	= date('Y-m-d h:i:s'); // although it technically means nothing, we still need a password for social. setting it to something random like the current time..
		$socialUser['User']['created']	= date('Y-m-d h:i:s');
		$socialUser['User']['modified']	= date('Y-m-d h:i:s');

	//	save and store our ID
		$this->save($socialUser);
		$socialUser['User']['id'] = $this->id;

		return $socialUser;
	}

	public function _callClientCount($userId = null, $date = array()){
		$userId		= (int) $userId;
		$date		= (array) $date;
		$dateFrom	= Common::hashEmptyField($date, 'date_from');
		$dateTo		= Common::hashEmptyField($date, 'date_to');
		$options	= array(
			'group' => array('ViewUserClientRelation.agent_id'), 
		);

		$this->bindModel(array(
			'hasMany' => array(
				'ViewUserClientRelation' => array(
					'foreignKey' => 'agent_id', 
					'conditions' => array(
						'ViewUserClientRelation.status' => 1, 
					), 
				), 
			), 
		));

		if($userId){
			$options['conditions'][]['ViewUserClientRelation.agent_id'] = $userId;
		}

		if($dateFrom && $dateTo){
			$options['conditions'][]['ViewUserClientRelation.relation_created >='] = $dateFrom;
			$options['conditions'][]['ViewUserClientRelation.relation_created <='] = $dateTo;
		}

		$this->ViewUserClientRelation->virtualFields['client_count'] = 'COUNT(DISTINCT ViewUserClientRelation.user_id)';

		$results		= $this->ViewUserClientRelation->find('all', $options);
		$clientCount	= Hash::extract($results, '{n}.ViewUserClientRelation.client_count');

		return array_sum($clientCount);
	}

	public function _callCrmProjectCount($userId = null, $type = 'summary', $date = array()){
		$userId	= (int) $userId;
		$date	= (array) $date;
		$type	= in_array($type, array('summary', 'detail')) ? $type : 'summary';

		$dateFrom	= Common::hashEmptyField($date, 'date_from');
		$dateTo		= Common::hashEmptyField($date, 'date_to');
		$options	= array(
			'group'			=> array('ViewAgentCrmProject.user_id'), 
			'conditions'	=> array(
				'not' => array(
					'ViewAgentCrmProject.step' => 'change_status', 
				), 
			), 
			'fields'		=> array(
				'ViewAgentCrmProject.user_id', 
				'ViewAgentCrmProject.crm_count', 
				'ViewAgentCrmProject.activity_count', 
			), 
		);

		$this->bindModel(array(
			'hasMany' => array(
				'ViewAgentCrmProject' => array('foreignKey' => 'user_id'), 
			), 
		));

		if($userId){
			$options['conditions'][]['ViewAgentCrmProject.user_id'] = $userId;
		}

		if($dateFrom && $dateTo){
			$options['conditions'][]['ViewAgentCrmProject.activity_date >='] = $dateFrom;
			$options['conditions'][]['ViewAgentCrmProject.activity_date <='] = $dateTo;
		}

		if($type == 'detail'){
			$options['fields'][]	= 'ViewAgentCrmProject.attribute_set_id';
			$options['group'][]		= 'ViewAgentCrmProject.attribute_set_id';
		}

		$this->ViewAgentCrmProject->virtualFields['crm_count']		= 'COUNT(DISTINCT ViewAgentCrmProject.crm_project_id)';
		$this->ViewAgentCrmProject->virtualFields['activity_count']	= 'COUNT(DISTINCT ViewAgentCrmProject.crm_project_activity_id)';

		$results = $this->ViewAgentCrmProject->find('all', $options);

	//	$crmCount		= Hash::extract($results, '{n}.ViewAgentCrmProject.crm_count');
	//	$activityCount	= Hash::extract($results, '{n}.ViewAgentCrmProject.activity_count');

	//	if($type == 'detail' && ($crmCount || $activityCount)){
	//		debug($results);exit;
	//	}

	//	return array(
	//		'crm_count'			=> array_sum($crmCount), 
	//		'activity_count'	=> array_sum($activityCount), 
	//	);

		return $results;
	}

	public function _callCrmProjectActivityCount($userId = null, $date = array()){
		$userId	= (int) $userId;
		$date	= (array) $date;

		$this->AttributeOption	= ClassRegistry::init('AttributeOption');
		$attributeOptions		= $this->AttributeOption->getData('all', array(
			'conditions' => array(
				'AttributeOption.status'	=> 1, 
				'AttributeOption.show'		=> 1, 
				'AttributeOption.type'		=> 'option', 
			), 
		));

		$attributeOptionId = Hash::extract($attributeOptions, '{n}.AttributeOption.id');

		$dateFrom	= Common::hashEmptyField($date, 'date_from');
		$dateTo		= Common::hashEmptyField($date, 'date_to');
		$options	= array(
			'conditions'	=> array(
				'ViewAgentCrmProjectActivity.attribute_option_id' => $attributeOptionId, 
				'not' => array(
					'ViewAgentCrmProjectActivity.step' => 'change_status', 
				), 
			), 
			'fields'		=> array(
				'ViewAgentCrmProjectActivity.user_id', 
				'ViewAgentCrmProjectActivity.crm_count', 
				'ViewAgentCrmProjectActivity.activity_count', 
			), 
			'group'			=> array(
				'ViewAgentCrmProjectActivity.user_id', 
				'ViewAgentCrmProjectActivity.crm_project_activity_id', 
				'ViewAgentCrmProjectActivity.attribute_option_id', 
			), 
		);

		$this->bindModel(array(
			'hasMany' => array(
				'ViewAgentCrmProjectActivity' => array('foreignKey' => 'user_id'), 
			), 
		));

		if($userId){
			$options['conditions'][]['ViewAgentCrmProjectActivity.user_id'] = $userId;
		}

		if($dateFrom && $dateTo){
			$options['conditions'][]['ViewAgentCrmProjectActivity.activity_date >='] = $dateFrom;
			$options['conditions'][]['ViewAgentCrmProjectActivity.activity_date <='] = $dateTo;
		}

		$this->ViewAgentCrmProjectActivity->virtualFields['crm_count']		= 'COUNT(DISTINCT ViewAgentCrmProjectActivity.crm_project_id)';
		$this->ViewAgentCrmProjectActivity->virtualFields['activity_count']	= 'COUNT(DISTINCT ViewAgentCrmProjectActivity.crm_project_activity_id)';

		$results = $this->ViewAgentCrmProjectActivity->find('all', $options);

		return $results;
	}
}
?>