<?php
class UserClient extends AppModel {

	var $name = 'UserClient';
	var $validate = array(
		'photo' => array(
			'imageupload' => array(
	            'rule' => array('extension',array('jpeg','jpg','png','gif')),
	            'allowEmpty' => true,
	            'message' => 'Foto harap diisi dan berekstensi (jpeg, jpg, png, gif)'
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
		'content_event' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih event',
			),
		),
		'content_sosmed' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih sosmed',
			),
		),
		'client_ref_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih sumber data klien',
			),
		),
		'additional_region_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih provinsi',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Provinsi ID harus berupa angka',
			),
		),
		'additional_city_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih kota',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Kota ID harus berupa angka',
			),
		),
		'additional_subarea_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih area',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Area ID harus berupa angka',
			),
		),
		'additional_zip' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan kode pos',
			),
		),
		'client_ref_walk_in' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan informasi/keterangan',
			),
		),
		'client_ref_another_option' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan keterangan',
			),
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan email',
			),
			'validateEmail' => array(
				'rule' => array('validateEmail'),
				'message' => 'Email yang Anda masukkan telah terdaftar. Anda dapat memilih jenis "Klien terdaftar" untuk melanjutkan proses.',
			),
		),
		'full_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama lengkap harap diisi',
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
		'phone' => array(
			'notMatch' => array(
				'rule' => array('validatePhoneNumber'),
				'allowEmpty'=> true,
				'message' => 'Format No. Telepon e.g. +6281234567 or 0812345678'
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'allowEmpty'=> true,
				'message' => 'Minimal 6 digit',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 20),
				'allowEmpty'=> true,
				'message' => 'Maksimal 20 digit',
			),
		),
		'no_hp' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nomor handphone Anda',
			),
			'notMatch' => array(
				'rule' => array('validatePhoneNumber'),
				'message' => 'Format No. handphone e.g. +6281234567 or 0812345678'
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'Minimal 6 digit',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 20),
				'message' => 'Maksimal 20 digit',
			),
		),
		'no_hp_2' => array(
			'notMatch' => array(
				'rule' => array('validatePhoneNumber'),
				'allowEmpty'=> true,
				'message' => 'Format No. handphone 2 e.g. +6281234567 or 0812345678'
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'allowEmpty'=> true,
				'message' => 'Minimal 6 digit',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 20),
				'allowEmpty'=> true,
				'message' => 'Maksimal 20 digit',
			),
		),
		'pin_bb' => array(
			'minLength' => array(
				'rule' => array('minLength', 6),
				'allowEmpty'=> true,
				'message' => 'Minimal 6 karakter',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 8),
				'allowEmpty'=> true,
				'message' => 'Maksimal 8 karakter',
			),
		),
	);

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
        'ClientType' => array(
            'className' => 'ClientType',
            'foreignKey' => 'client_type_id'
        ),
        'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id'
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id'
		),
		'Subarea' => array(
			'className' => 'Subarea',
			'foreignKey' => 'subarea_id'
		),
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => false,
			'conditions' => array(
				'UserCompany.user_id = UserClient.company_id',
			)
		),
        'UserClientMasterReference' => array(
            'className' => 'UserClientMasterReference',
            'foreignKey' => 'client_ref_id'
        ),
        'UserClientSosmedReference' => array(
            'className' => 'UserClientSosmedReference',
            'foreignKey' => 'client_ref_sosmed_id'
        ),
        'JobType' => array(
            'className' => 'JobType',
            'foreignKey' => 'job_type'
        ),
		'Agent' => array(
			'className' => 'User',
			'foreignKey' => 'agent_id',
		),
    );
	var $hasOne = array(
		'ViewUserClient' => array(
			'className' => 'ViewUserClient',
			'foreignKey' => 'id',
		),
    );

	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		
		$this->virtualFields['full_name'] = sprintf('CONCAT(%s.first_name, " ", IFNULL(%s.last_name, \'\'))', $this->alias, $this->alias);
		
		$this->virtualFields['birthday'] = sprintf('CASE WHEN %s.birthday = \'0000-00-00\' THEN NULL ELSE %s.birthday END', $this->alias, $this->alias);
		$this->virtualFields['day_birth'] = sprintf('DAY(%s.birthday)', $this->alias);
		$this->virtualFields['month_birth'] = sprintf('MONTH(%s.birthday)', $this->alias);
		$this->virtualFields['year_birth'] = sprintf('YEAR(%s.birthday)', $this->alias);
		$this->virtualFields['gender_id'] = sprintf('CASE WHEN %s.gender_id = \'0\' THEN NULL ELSE %s.gender_id END', $this->alias, $this->alias);
		$this->virtualFields['address'] = sprintf('CASE WHEN %s.address = \'\' THEN NULL ELSE %s.address END', $this->alias, $this->alias);
	}

	function validateEmail () {
		$email = Common::hashEmptyField($this->data, 'UserClient.email');
		$id = Common::hashEmptyField($this->data, 'UserClient.id');

		if( !empty($email) ) {
			$emailExist = $this->ViewUserClient->getData('first', array(
				'conditions' => array(
					'ViewUserClient.email' => $email,
					'ViewUserClient.id <>' => $id,
				),
			));
			$user_exist_id = Common::hashEmptyField($emailExist, 'ViewUserClient.id');

			if( !empty($user_exist_id) ) {
				return false;
			}
		}

		return true;
	}

	function beforeSave( $options = array() ) {
		
		$full_name = !empty( $this->data['UserClient']['full_name'] ) ? $this->data['UserClient']['full_name'] : false;
		if ( $full_name ) {
			$arr_name = explode(' ', $full_name);
		
			$first_name = $arr_name[0];
			unset($arr_name[0]);
			$last_name = implode(' ', $arr_name);

			$this->data['UserClient']['first_name'] = $first_name;
			$this->data['UserClient']['last_name'] = $last_name;
		}

		if( !empty($this->data['UserClient']['day_birth']) ) {
			$day_birth = !empty( $this->data['UserClient']['day_birth'] ) ? $this->data['UserClient']['day_birth'] : false;
			$month_birth = !empty( $this->data['UserClient']['month_birth'] ) ? $this->data['UserClient']['month_birth'] : false;
			$year_birth = !empty( $this->data['UserClient']['year_birth'] ) ? $this->data['UserClient']['year_birth'] : false;

			if ( $day_birth && $month_birth && $year_birth ) {
				$birthday = $year_birth.'-'.$month_birth.'-'.$day_birth;
				$this->data['UserClient']['birthday'] = date('Y-m-d', strtotime($birthday));
			}
		} else if( !empty($this->data['UserProfile']['day_birth']) ) {
			$day_birth = !empty( $this->data['UserProfile']['day_birth'] ) ? $this->data['UserProfile']['day_birth'] : false;
			$month_birth = !empty( $this->data['UserProfile']['month_birth'] ) ? $this->data['UserProfile']['month_birth'] : false;
			$year_birth = !empty( $this->data['UserProfile']['year_birth'] ) ? $this->data['UserProfile']['year_birth'] : false;

			if ( $day_birth && $month_birth && $year_birth ) {
				$birthday = $year_birth.'-'.$month_birth.'-'.$day_birth;
				$this->data['UserClient']['birthday'] = date('Y-m-d', strtotime($birthday));
			}
		}
		
		return true;
	}

	function validateUserEmail($data) {
		if( !empty($data) ) {
			$email = false;
			if( !empty($data['agent_pic_email']) ) {
				$email = $data['agent_pic_email'];
			}

			$user = $this->User->getData('first', array(
				'conditions' => array(
					'User.email' => $email,
				),
			), array(
				'role' => 'agent',
				'status' => 'semi-active',
                'company' => true,
                'admin' => true,
			));

			if(!empty($user)){
				return true;
			}
		}		
		return false;
	}

	function validatePhoneNumber($data) {
		$phoneNumber = false;
		if( !empty($data['phone']) ) {
			$phoneNumber = $data['phone'];
		} else if( !empty($data['no_hp']) ) {
			$phoneNumber = $data['no_hp'];
		} else if( !empty($data['no_hp_2']) ) {
			$phoneNumber = $data['no_hp_2'];
		}

		if(!empty($phoneNumber)) {
	        if (preg_match('/^[0-9]{1,}$/', $phoneNumber)==1 
	        	|| ( substr($phoneNumber, 0,1)=="+" 
	        	&& preg_match('/^[0-9]{1,}$/', substr($phoneNumber, 1,strlen($phoneNumber)))==1 )) {
	        	return true;
	        }
	    }
        return false;
    }

    /**
	* 	@param array $data['current_password'] - password active user
	* 	@return boolean true or false
	*/
	function checkCurrentPassword() {
		$data = $this->dataValidation;
		if( !empty($data['password']) && !empty($data['User']['UserClient']) ) {
			$current_password = $data['User']['UserClient']['password'];
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
			if( $this->data['UserClient']['password'] === $data['password_confirmation'] ) {
				return true;
			} else {
				return false; 
			}
		} else {
			return true;
		}
	}

	/**
	* 	@param array $data['new_password_confirmation'] - password baru
	* 	@return boolean true or false
	*/
	function matchNewPasswords($data) {
		if($this->data['UserClient']['new_password']) {
			if($this->data['UserClient']['new_password'] === $data['new_password_confirmation']) {
				return true;
			}
			return false; 
		} else {
			return true;
		}
	}

	function getData( $find = 'all', $options = array(), $elements = array() ) {
        $status = isset($elements['status']) ? $elements['status']:'active';
        $company = isset($elements['company']) ? $elements['company']:true;
		$mine = isset($elements['mine'])?$elements['mine']:false;
		$adminRumahku = isset($elements['adminRumahku'])?$elements['adminRumahku']:Configure::read('User.Admin.Rumahku');
        $admin = Configure::read('User.admin');

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(),
		);

		switch ($status) {
			case 'active':
				$default_options['conditions']['UserClient.status'] = 1;
				break;
		}

        if( !empty($company) && empty($adminRumahku) ) {
            $companyData = Configure::read('Config.Company.data');
            $parent_id = Configure::read('Principle.id');
            $group_id = $this->filterEmptyField($companyData, 'User', 'group_id');

        	if( $group_id == 4 ) {
				$principle_id = $this->User->getAgents($parent_id, true, 'list', false, array(
					'role' => 'principle',
				));
				$default_options['conditions']['UserClient.company_id'] = $principle_id;
        	} else {
				$default_options['conditions']['UserClient.company_id'] = Configure::read('Principle.id');
        	}
        }

		if( !empty($mine) ) {
			if( !empty($mine) && empty($admin)) {
				$user_login_id = Configure::read('User.id');

				$data_arr = $this->User->getUserParent($user_login_id);
				$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
				$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

				$this->bindModel(array(
					'hasOne' => array(
						'UserClientRelation' => array(
							'foreignKey' => false,
							'conditions' => array(
								'UserClientRelation.user_id = UserClient.user_id',
								'UserClientRelation.company_id = UserClient.company_id',
							),
						),
					)
				), false);

				if($is_sales){
					$default_options['conditions']['UserClientRelation.agent_id'] = $user_ids;
					$default_options['contain'][] = 'UserClientRelation';
				}

				$contains = Common::hashEmptyField($options, 'contain', array());

				if($is_sales || in_array('UserClientRelation', $contains)){
					$default_options['group'] = array(
						'UserClientRelation.user_id',
						'UserClientRelation.agent_id',
					);
				}
			}
		}

        return $this->merge_options($default_options, $options, $find);
	}

	function getMerge( $data, $id, $company_id = false , $modelName = 'UserClient') {
		if( !empty($id) ) {
			$email = false;
			$options = array(
	            'conditions' => array(
	                'UserClient.user_id' => $id,
	            ),
	        );

	        if( !empty($company_id) ) {
	        	$options['conditions']['UserClient.company_id'] = $company_id;
	        }
	        $value = $this->getData('first', $options, array(
	        	'status' => 'all',
        	));

	        if(!empty($value) ){
	        	$value = $this->User->getMerge($value, $id);
	            $data[$modelName] = $value['UserClient'];

	            if( !empty($value['User']['email']) ) {
	            	$email = $value['User']['email'];
	            }
	        } else if( !empty($data['User']) ) {
	        	if( !empty($data[$modelName]) ) {
		    		$data[$modelName] += $data['User'];
		    	} else {
	    			$data[$modelName] = $data['User'];
		    	}

	        	if( !empty($data['UserProfile']) ) {
	        		$data[$modelName] += $data['UserProfile'];
	        	}
	        }else{
	        	$value = $this->User->getData('first', array(
	        		'conditions' => array(
	        			'User.id' => $id,
	        		),
	        	));
	        	$data[$modelName] = !empty($value['User']) ? $value['User'] : array();
	        }

	    	$email = !empty($data['User']['email'])?$data['User']['email']:$email;
	    	$full_name = !empty($data[$modelName]['full_name'])?$data[$modelName]['full_name']:false;

	    	if( !empty($email) && !empty($full_name) ) {
	    		$data[$modelName]['client_email'] = sprintf('%s | %s', $email, $full_name);
	    		$data[$modelName]['email'] = $email;
	    	}
	    }

        return $data;
    }

    function getMergeAddress ( $data, $modelName = 'UserClient' ) {
		if( !empty($data[$modelName]) ) {
			if( !empty($data[$modelName]['region_id']) ) {
				$region_id = $data[$modelName]['region_id'];

				$region = $this->Region->getData('first', array(
					'conditions' => array(
						'Region.id' => $region_id,
					),
            		'cache' => __('Region.%s', $region_id),
				));

				if( !empty($region) ) {
					$data[$modelName] = array_merge($data[$modelName], $region);
				}
			}

			if( !empty($data[$modelName]['city_id']) ) {
				$city_id = $data[$modelName]['city_id'];

				$city = $this->City->getData('first', array(
					'conditions' => array(
						'City.id' => $city_id,
					),
					'cache' => __('City.%s', $city_id),
				));

				if( !empty($city) ) {
					$data[$modelName] = array_merge($data[$modelName], $city);
				}
			}

			if( !empty($data[$modelName]['subarea_id']) ) {
				$subarea_id = $data[$modelName]['subarea_id'];

				$subarea = $this->Subarea->find('first', array(
					'conditions' => array(
						'Subarea.id' => $subarea_id,
					),
            		'cache' => __('Subarea.%s', $subarea_id),
					'cacheConfig' => 'subareas',
				));

				if( !empty($subarea) ) {
					$data[$modelName] = array_merge($data[$modelName], $subarea);
				}
			}
		}

		return $data;
	}

	function getMergeClient( $data, $id, $company_id = false , $modelName = 'UserClient', $options = array()) {
		$default_options = array(
            'conditions' => array(
                'UserClient.user_id' => $id,
            ),
        );

        if( !empty($options) ) {
        	$default_options = array_merge_recursive($default_options, $options);
        }

        if( !empty($company_id) ) {
        	$default_options['conditions']['UserClient.company_id'] = $company_id;
        }

        $value = $this->getData('first', $default_options);

        if( !empty($value) ) {
        	$data[$modelName] = $value['UserClient'];
        }

        return $data;
    }

    function apiSave($data, $id = false){
    	$user_id = $this->filterEmptyField($data, 'User', 'id');

    	if(!empty($id)){
    		$this->id = $id;
    	}else{
    		$this->create();
    	}

    	if($this->save($data,false)){
    		return $user_id;
    	}else{
    		return false;
    	}
    }

	public function doSave( $data, $client = false, $user_id = false, $id = false ) {
		$result = false;
		$default_msg = __('memperbarui data klien');
		$_sendEmailClient = false;

		$user_profile_id = !empty($client['UserProfile']['id']) ? $client['UserProfile']['id'] : false;

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$data['UserClient']['id'] = $id;
			} else {
				$this->create();
				$data['UserClient']['user_id'] = $user_id;
			}

			$data['UserClient']['full_name'] = trim($data['UserClient']['full_name']);
			$data['UserClient']['address'] = trim($data['UserClient']['address']);
			$data['UserClient']['zip'] = trim($data['UserClient']['zip']);
			$data['UserClient']['line'] = trim($data['UserClient']['line']);

			if( !empty($data['UserClient']['email']) ) {
				$data['User']['email'] = trim($data['UserClient']['email']);
			}

			$this->set($data);

			if ( $this->validates() ) {
				$current_agent_pic_email = !empty($client['UserClient']['agent_pic_email']) ? $client['UserClient']['agent_pic_email']: false;
				$agent_pic_email = !empty($data['UserClient']['agent_pic_email'])?$data['UserClient']['agent_pic_email']:false;
				$agent = $this->User->getData('first', array(
					'conditions' => array(
						'User.email' => $agent_pic_email,
					),
				), array(
					'role' => 'agent',
					'status' => 'semi-active',
	                'company' => true,
	                'admin' => true,
				));

				if( !empty($agent) && (strcasecmp($current_agent_pic_email, $agent_pic_email) != 0) ) {
					$current_agent_id = !empty($client['UserClient']['agent_id']) ? $client['UserClient']['agent_id']: false;
					$current_agent_name = !empty($client['User']['full_name']) ? $client['User']['full_name']: false;
					$client_name = !empty($client['UserClient']['full_name']) ? $client['UserClient']['full_name']: false;
					
					$new_agent_id = !empty($agent['User']['id']) ? $agent['User']['id']:false;
					$new_agent_name = !empty($agent['User']['full_name']) ? $agent['User']['full_name']:false;

					// Send notification to old agent pic
					$this->User->Notification->doSave(array(
						'Notification' => array(
							'user_id' => $current_agent_id,
							'name' => sprintf('Klien Anda ( %s ) telah dipindahkan ke agen %s', $client_name, $new_agent_name),
						),
					));

					$data['UserClient']['agent_id'] = $new_agent_id;

					//  UPDATE AGENT PIC
					$this->updateAll(array(
						'UserClient.agent_id' => $new_agent_id,
					), array(
						'UserClient.id' => $id,
						'UserClient.user_id' => $user_id,
						'UserClient.agent_id' => $current_agent_id,
						'UserClient.company_id' => Configure::read('Principle.id'),
					));
					
					$this->User->UserClientRelation->deleteAll(array(
			            'UserClientRelation.primary' => 1,
			            'UserClientRelation.user_id' => $user_id,
						'UserClientRelation.agent_id' => $current_agent_id,
						'UserClientRelation.company_id' => Configure::read('Principle.id'),
			        ));
					$this->User->UserClientRelation->doSave( $new_agent_id, $user_id );
					
					$mail_params = array(
						'client_name' => $client_name,
						'new_agent_name' => $new_agent_name,
					);
					$_sendEmailClient = array(
						'to_name' => $current_agent_name,
	                	'to_email' => $current_agent_pic_email,
	                	'subject' => sprintf('Pemindahan klien utama'),
	                	'template' => 'move_client',
	                	'data' => $mail_params,
	                );
				}

				if( $this->save($data) ) {
					$this->User->UserProfile->doSave($data, $user_id, $user_profile_id);

					if( !empty($data['User']) ) {
						$this->User->doSave($data, $user_id);
					}

					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $client,
							'document_id' => $id,
						),
					);
					if( !empty($_sendEmailClient) ) {
						$result['SendEmail'] = $_sendEmailClient;
					}
				} else {
					$msg = sprintf(__('Gagal %s'), $default_msg);
					$result = array(
						'msg' => sprintf(__('Gagal %s'), $default_msg),
						'status' => 'error',
						'data' => $data,
						'Log' => array(
							'activity' => $msg,
							'old_data' => $client,
							'document_id' => $id,
							'error' => 1,
						),
						'validationErrors' => $this->validationErrors,
					);
				}
			} else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'data' => $data,
					'validationErrors' => $this->validationErrors,
					'Log' => array(
						'activity' => $msg,
						'old_data' => $client,
						'document_id' => $id,
						'error' => 1,
					),
				);
			}
		} else if( !empty($client) ) {
			$photo = !empty($client['UserClient']['photo'])?$client['UserClient']['photo']:false;

			$client['UserClient']['photo_hide'] = $photo;
			$result['data'] = $client;
		}

		return $result;
	}

	function doToggle( $id ) {	
		$result = false;
		$default_msg = __('menghapus klien');

		$isAdmin = Configure::read('User.admin');
		$user_id = Configure::read('User.id');
        $parent_id = Configure::read('Principle.id');

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'UserClient.id' => $id,
			),
		);

		$values = $this->getData('all', $options, array(
			'status' => 'all',
		));

		if ( !empty($values) ) {
			$name = Set::extract('/UserClient/full_name', $values);
			$name = implode(', ', $name);
			$options = array(
				'UserClient.id' => $id,
			);

			if( !empty($isAdmin) ) {
				$agent_id = $this->User->getAgents($parent_id, true, 'list', false, array(
	            	'role' => 'all',
	        	));
				$options['UserClient.agent_id'] = $agent_id;
			} else {
				$options['UserClient.agent_id'] = $user_id;
			}

			$flag = $this->updateAll(array(
				'UserClient.status' => 0,
				'UserClient.modified' => "'".date('Y-m-d H:i:s')."'",
			), $options);

            if( $flag ) {

            	foreach( $values as $key => $value ) {
					$options = array(
			            'UserClientRelation.user_id '=> $user_id,
			        );

					if( !empty($isAdmin) ) {
						$agent_id = $this->User->getAgents($parent_id, true, 'list', false, array(
			            	'role' => 'all',
			        	));
						$options['UserClientRelation.company_id'] = $parent_id;
					} else {
						$options['UserClientRelation.agent_id'] = $user_id;
					}
            		
            		$this->User->UserClientRelation->deleteAll($options);
            	}

		        $implode_client_id = implode(",", $id);
				$msg = sprintf(__('Berhasil %s %s'), $default_msg, $name);
				$log_msg = sprintf('Berhasil %s #%s', $default_msg, $implode_client_id);

                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $log_msg,
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

	function doInviteClient( $client_id, $data_company ) {
		$result = false;
		$default_msg = __('mengirimkan invitasi kepada klien');

		if( !empty($client_id) ) {

			$client = $this->getData('first', array(
				'conditions' => array(
					'UserClient.id' => $client_id,
				),
			));

			if( !empty($client) ) {
				$client = $this->getMergeList($client, array(
					'contain' => array(
						'User', 
					), 
				));
				$token = $this->filterEmptyField($client, 'UserClient', 'token');

				if( empty($token) ) {
					$token = String::uuid();
					$dataClient['UserClient']['token'] = $token;

					$this->id = $client_id;
					$this->set($dataClient);
					$this->save($dataClient);
				}
					
				$email = $this->filterEmptyField($client, 'User', 'email');
				$full_name = $this->filterEmptyField($client, 'UserClient', 'full_name');
				$company_name = $this->filterEmptyField($data_company, 'UserCompany', 'name');

				$mail_params = array(
					'client_id' => $client_id,
					'token' => $token,
					'full_name' => $full_name,
					'company_name' => $company_name,
				);

				$msg = sprintf(__('Sukses %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'success',
					'SendEmail' => array(
						'to_name' => $full_name,
	                	'to_email' => $email,
	                	'subject' => sprintf('Invitasi login sebagai klien %s', $company_name),
	                	'template' => 'invite_client',
	                	'data' => $mail_params,
	                ),
	                'Log' => array(
						'activity' => $msg,
						'old_data' => $client,
						'document_id' => $client_id,
					),
				);
			} else {
				$msg = sprintf(__('Gagal %s. Klien tidak ditemukan'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'error' => 1
					),
				);
			}
		} else {
			$msg = sprintf(__('Gagal %s'), $default_msg);
			$result = array(
				'msg' => $msg,
				'status' => 'error',
				'Log' => array(
					'activity' => $msg,
					'error' => 1
				),
			);
		}

		return $result;
	}

	public function doVerifyNewPassword( $data, $id, $user, $token ) {
		$result = false;
		$default_msg = __('memperbarui password');

		if ( !empty($data) ) {
			
			$this->set($data);
			if ( $this->validates() ) {
				$this->id = $id;
				$auth_password = !empty($data['UserClient']['auth_password']) ? $data['UserClient']['auth_password'] : false;

				$this->set('password', $auth_password);
				$this->set('change_password', 1);
				$this->set('token', String::uuid());

				if( $this->save() ) {
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => sprintf(__('Sukses %s'), $default_msg),
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $user,
							'document_id' => $id,
						),
					);
				} else {
					$msg = sprintf(__('Gagal %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'error',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $user,
							'document_id' => $id,
							'error' => 1,
						),
					);
				}
			} else {
				$result = array(
					'msg' => sprintf(__('Gagal %s'), $default_msg),
					'status' => 'error',
				);
			}
		}

		return $result;
	}

	public function doEditPassword( $id, $user, $data, $data_company = false ) {
		$result = false;
		$default_msg = __('memperbarui password');

		if ( !empty($data) ) {
			$data['UserClient'] = $data['User'];
			unset($data['User']);

			$email = !empty($user['User']['email'])?$user['User']['email']:false;
			$full_name = !empty($user['User']['full_name'])?$user['User']['full_name']:false;
			$current_password = !empty($data['UserClient']['current_password'])?$data['UserClient']['current_password'] : false;
			$new_password = !empty($data['UserClient']['new_password'])?$data['UserClient']['new_password']:false;
			$new_password_ori = !empty($data['UserClient']['new_password_ori'])?$data['UserClient']['new_password_ori']:false;

			$this->set($data);
			$this->dataValidation = array(
				'User' => $user['User'],
				'password' => $current_password,
			);
			
			if ( $this->validates() && !empty($new_password) ) {
				$this->id = $id;
				$this->set('password', $new_password);

				if( $this->save() ){
					$msg = sprintf(__('Berhasil %s %s'), $default_msg, $full_name);
					$company_name = !empty($data_company['UserCompany']['name'])?$data_company['UserCompany']['name']:false;
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
                        	'subject' => sprintf(__('Informasi akun %s Anda telah diperbarui'), $company_name),
                        	'template' => 'change_password',
                        	'data' => $dataEmail,
	                    ),
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
					);
				}
			} else {
				$result = array(
					'msg' => sprintf(__('Gagal %s'), $default_msg),
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
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));
        $client_type = $this->filterEmptyField($data, 'named', 'client_type', false, array(
        	'addslashes' => true,
    	));
        $name = $this->filterEmptyField($data, 'named', 'name', false, array(
        	'addslashes' => true,
    	));
		$company = $this->filterEmptyField($data, 'named', 'company', false, array(
        	'addslashes' => true,
    	));
		$email = $this->filterEmptyField($data, 'named', 'email', false, array(
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
		$gender = $this->filterEmptyField($data, 'named', 'gender', false, array(
        	'addslashes' => true,
    	));
    	$budget = $this->filterEmptyField($data, 'named', 'budget', false, array(
        	'addslashes' => true,
    	));
    	$date_from = $this->filterEmptyField($data, 'named', 'date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = $this->filterEmptyField($data, 'named', 'date_to', false, array(
            'addslashes' => true,
        ));
		$principle_id = $this->filterEmptyField($data, 'named', 'principle_id', false, array(
        	'addslashes' => true,
    	));
		$references = $this->filterEmptyField($data, 'named', 'user_client_reference', false, array(
        	'addslashes' => true,
    	));
		$agent = $this->filterEmptyField($data, 'named', 'agent', false, array(
        	'addslashes' => true,
    	));
        
		if( !empty($keyword) ) {
			$type = $this->ClientType->find('list', array(
				'conditions' => array(
					'ClientType.name LIKE' => '%'.$keyword.'%',
				),
				'fields' => array(
					'ClientType.id', 'ClientType.id',
				),
			));

			$default_options['conditions']['OR'] = array(
				'CONCAT(UserClient.first_name, " ", IFNULL(UserClient.last_name, \'\')) LIKE' => '%'.$keyword.'%',
				'User.email LIKE' => '%'.$keyword.'%',
			);

			if( !empty($type) ) {
				$default_options['conditions']['OR']['UserClient.client_type_id'] = $type;
			}
		}
		if( !empty($client_type) ) {
			$default_options['conditions']['UserClient.client_type_id'] = $client_type;
		}
		if( !empty($name) ) {
			$name = trim($name);
			$default_options['conditions']['CONCAT(UserClient.first_name,\' \',IFNULL(UserClient.last_name, \'\')) LIKE'] = '%'.$name.'%';
		}
		if( !empty($company) ) {
			$default_options['conditions']['UserCompany.name LIKE'] = '%'.$company.'%';
			$default_options['contain'][] = 'UserCompany';
		}
		if( !empty($email) ) {
			$email = trim($email);
			$default_options['conditions']['User.email LIKE'] = '%'.$email.'%';
			$default_options['contain'][] = 'User';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(UserClient.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(UserClient.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($phone_profile) ) {
			$default_options['conditions']['UserClient.phone LIKE'] = '%'.$phone_profile.'%';
		}
		if( !empty($no_hp) ) {
			$default_options['conditions']['UserClient.no_hp LIKE'] = '%'.$no_hp.'%';
		}
		if( !empty($no_hp_2) ) {
			$default_options['conditions']['UserClient.no_hp_2 LIKE'] = '%'.$no_hp_2.'%';
		}
		if( !empty($pin_bb) ) {
			$default_options['conditions']['UserClient.pin_bb LIKE'] = '%'.$pin_bb.'%';
		}
		if( !empty($line) ) {
			$default_options['conditions']['UserClient.line LIKE'] = '%'.$line.'%';
		}
		if( !empty($gender) ) {
			$default_options['conditions']['User.gender_id'] = $gender;
			$default_options['contain'][] = 'User';
		}
		if( !empty($budget) ){
			$default_options['conditions']['UserClient.range_budget'] = $budget;
		}
		if( !empty($principle_id) ) {
			if( !is_array($principle_id) ) {
				$principle_id = explode(',', $principle_id);
			}

			$default_options['conditions']['UserClient.company_id'] = $principle_id;
		}
		if( !empty($agent) ) {
			$default_options['conditions']['CONCAT(Agent.first_name, " ", IFNULL(Agent.last_name, \'\')) LIKE'] = '%'.$agent.'%';
			$default_options['contain'][] = 'Agent';
		}

		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'user_client_reference' => array(
				'field' => 'UserClient.client_ref_id',
			),
		));


        if( !empty($sort) ) {
	        $default_options['order'][$sort] = $direction;
    
        	$company = strpos($sort, 'UserCompany.');

        	if( is_numeric($company) ) {
	            $default_options['contain'][] = 'UserCompany';
	        }
        }
        if ($references == 'NULL') {
			$default_options = Common::_callUnset($default_options, array(
				'UserClient.client_ref_id'
			));
        	$default_options['conditions']['UserClient.client_ref_id'] = NULL;
        }
		return $default_options;
	}

	function doUpdate($data){
		if(!empty($data['UserClient'])){
			
			$user_id = $this->filterEmptyField($data, 'UserClient', 'user_id');
 			$value = $this->getData('first', array(
				'conditions' => array(
					'UserClient.user_id' => $user_id,
				),
			));

			if(!empty($value)){
				$id = $this->filterEmptyField($value, 'UserClient', 'id');
				$client_name = $this->filterEmptyField($data, 'UserClient', 'client_name');
				$data['UserClient']['full_name'] = $client_name;

				$this->id = $id;	
				$this->set($data);
				if($this->validates()){
					if($this->save()){
						return true;
					}else{
						return false;
					}
				}else{
					return false;
				}
			}else{
				return false;
			}
		}
	}
}
?>