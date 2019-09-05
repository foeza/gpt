<?php
class RmUserComponent extends Component {
	var $components = array('Auth', 'RmCommon', 'RumahkuApi', 'RmProperty'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}
	
	/**
	* Proses pencarian
	*
	* @param array $refine - Menampung parameter pencarian
	* @return array - hasil pencarian
	*/
	function processRefine($refine = false) {
		if(!$refine) {
			return false;
		}
		
		$refine_conditions = array();
		if(!empty($refine)) {
			if(isset($refine['User']['group_id']) && $refine['User']['group_id']) {
				$refine_group = $refine['User']['group_id'];
				$refine_conditions['User']['group_id'] = $refine_group;
			}
			if(isset($refine['User']['parent_id']) && $refine['User']['parent_id']) {
				$refine_group = $refine['User']['parent_id'];
				$refine_conditions['User']['parent_id'] = $refine_group;
			}
			if(isset($refine['User']['fullname']) && $refine['User']['fullname']) {
				$refine_user = $refine['User']['fullname'];
				$refine_conditions['User']['fullname'] = $refine_user;
			}
			if(isset($refine['User']['email']) && $refine['User']['email']) {
				$refine_user = $refine['User']['email'];
				$refine_conditions['User']['email'] = $refine_user;
			}
			if(isset($refine['User']['status']) && is_numeric($refine['User']['status'])) {
				$refine_user = $refine['User']['status'];
				$refine_conditions['User']['status'] = $refine_user;
			}
			if(isset($refine['UserMembership']['membership_id']) && $refine['UserMembership']['membership_id']) {
				$refine_conditions['UserMembership']['membership'] = $refine['UserMembership']['membership_id'];
			}
		}
		return $refine_conditions;
	}

	/**
	* Generate URL
	*
	* @param array $refine - Menampung parameter pencarian
	* @param array $parameters - Hasil generate URL
	* @return array - Hasil generate URL
	*/
	function generateSearchURL($refine) {
		$parameters = array();
		if(isset($refine['User']) && !empty($refine['User'])) {
			foreach($refine['User'] as $param => $value) {
				if( $param == 'fullname' ) $parameters[] = Sanitize::paranoid(trim($param)).':'.rawurlencode(trim($value));
				else $parameters[] = Sanitize::paranoid(trim($param)).':'.trim($value);
			}
		}
		
		return $parameters;
	}

	/**
	*
	*	membuat random karakter
	*	
	*	@param int $default : jumlah karakter yang ingin di random
	*	@param string $variable : value karakter
	*	@param int $modRndm : modulus
	*	@return string $pass
	*/
	function createRandomNumber( $default= 4, $variable = 'bcdfghjklmnprstvwxyz', $modRndm = 20 ) {
        $chars = $variable;
        srand((double)microtime()*1000000);
        $pass = array() ;

        $i = 1;
        while ($i != $default) {
            $num = rand() % $modRndm;
            $tmp = substr($chars, $num, 1);
            $pass[] = $tmp;
            $i++;
        }
        $pass[] = rand(1,9);

        return $pass;
    }

    /**
	*
	*	random array
	*	
	*	@param array $arr : data array
	*	@param int $num : jumlah shuffle
	*	@return array $hasil
	*/
    function array_random($arr, $num = 1) {
	    shuffle($arr);
	    
	    $r = array();
	    for ($i = 0; $i < $num; $i++) {
	        $r[] = $arr[$i];
	    }
	    return $num == 1 ? $r[0] : $r;
	}

	/**
	*
	*	generate activation code
	*	
	*	@return string code
	*/
	function _generateCode( $type = 'activation', $string = false, $length = 4 ) {
		switch ($type) {
			case 'reset':
				$result = md5(time().Configure::read('Security.salt').String::uuid());
				break;

			case 'username':
				$stringArr = explode('@', $string);
				$result = !empty($stringArr[0])?$stringArr[0]:false;
				break;

			case 'user_code':
				$new_code = '';
				$flag = true;

				while ($flag) {
					$new_code = $this->createRandomNumber($length);
					$rand_code = $this->array_random($new_code, count($new_code));
					$str_code = strtoupper(implode('', $rand_code));
					$check_user = $this->controller->User->getdata('count', array(
						'conditions'=> array(
							'User.code'=> $str_code,
						),
					), array(
						'status' => false,
					));
					
					if( empty($check_user) ) {
						$flag = false;
					}
				}

				$result = $str_code;
				break;

			case 'password':
				$new_code = '';
				$new_code = $this->createRandomNumber($length, 'bcdfghjklmnprstvwxyz0123456789');
				$rand_code = $this->array_random($new_code, count($new_code));
				$result = strtoupper(implode('', $rand_code));
				break;
			
			default:
				$result = md5(date('mdY').String::uuid());
				break;
		}

		return $result;
	}

	function _getUserFullName( $user, $action = false, $model = 'User', $field_fullname = 'full_name' ) {
		$name = '';

		if( $action == 'reverse' ) {
			$fullname = !empty($user[$model][$field_fullname])?$user[$model][$field_fullname]:false;
			$split = explode(' ',$fullname);
			$first_name = false;
			$last_name = false;

			if( !empty($split[0]) ) {
				$first_name = $split[0];

				if( !empty($split[1]) ) {
					unset($split[0]);
					$last_name = implode(' ', $split);
				}
			}

			$name = array(
				'first_name' => $first_name,
				'last_name' => $last_name,
			);
		} else {
			if( !empty($user[$model][$field_fullname]) ) {
				$name = $user[$model][$field_fullname];
			} else {
				if(!empty($user[$model]['first_name'])) {
					$name = $user[$model]['first_name'];
				}
				if(!empty($user[$model]['last_name'])) {
					$name = sprintf('%s %s', $name, $user['User']['last_name']);
				}
			}

			$name = trim($name);
		}

		return $name;
	}

	function _callSplitName( $name, $fieldName = false ) {
		$nameArr = explode(' ', $name);

		if( $fieldName == 'first_name' && !empty($nameArr[0]) ) {
			$name = trim($nameArr[0]);
		} else if( $fieldName == 'last_name' && count($nameArr) > 1 ) {
			unset($nameArr[0]);
			$name = implode(' ', $nameArr);;
		} else {
			$name = false;
		}

		return $name;
	}

	function _checkExpiredResetPassword ( $data ) {
		$reminder_time = !empty($data['PasswordReset']['reminder_time'])?$data['PasswordReset']['reminder_time']:0;
		$expired_time = !empty($data['PasswordReset']['expired_time']) ? $data['PasswordReset']['expired_time']:8640;
		$expired = time() - $expired_time;

		if ($reminder_time > $expired) {
			return true;
		} else {
			return false;
		}
	}

	function getUserPercentageCompletion( $user_id ) {

		$group_id = Configure::read('User.group_id');
		$user = $this->controller->User->getData('first', array(
    		'conditions' => array(
    			'User.id' => $user_id,
			),
		));
		$user = $this->controller->User->UserConfig->getMerge($user, $user_id);

		$profileDataCompletion = !empty($user['UserConfig']['progress_user']) ? $user['UserConfig']['progress_user'] : 0;
		$sosmedDataCompletion = !empty($user['UserConfig']['progress_sosmed']) ? $user['UserConfig']['progress_sosmed'] : 0;
		$professionDataCompletion = !empty($user['UserConfig']['progress_profession']) ? $user['UserConfig']['progress_profession'] : 0;

		if( in_array($group_id, array(3,5,18,19,20)) ) {
			$total_percentage = $profileDataCompletion;
			$remaining_percentage = 100 - $total_percentage;
		} else {
			$total_percentage = round( ($profileDataCompletion + $sosmedDataCompletion + $professionDataCompletion) / 3 );
			$remaining_percentage = 100 - $total_percentage;
		}

		return array(
			'total_percentage' => $total_percentage,
			'remaining_percentage' => $remaining_percentage
		);
	}

	function setClientFromRKU($data, $group_id = false){
		$datasave = false;

		if(!empty($data) && $group_id == 10){
			$email = $this->RmCommon->filterEmptyField($data, 'KprBank', 'client_email');
			$password = $this->Auth->password($this->_generateCode('user_code', false, 6));
			$parent_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'parent_id');

			$datasave['User']['group_id'] = $group_id;
			$datasave['User']['code'] = $this->_generateCode('user_code');
			$datasave['User']['parent_id'] = $parent_id;
			$datasave['User']['username'] = $email;
			$datasave['User']['first_name'] = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'first_name');
			$datasave['User']['last_name'] = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'last_name');
			$datasave['User']['password'] = $password;
			$datasave['User']['email'] = $email;
			$datasave['User']['gender_id'] = $this->RmCommon->filterEmptyField($data, 'KprApplication', 'gender_id');
			$datasave['User']['active'] = true;
			$datasave['User']['deleted'] = false;
			$datasave['UserClient']['company_id'] = $parent_id;
			$datasave['UserClient']['agent_id'] = $this->RmCommon->filterEmptyField($data, 'KprBank', 'agent_id');
			$datasave['UserClient']['client_type_id'] = 1;
			$datasave['UserClient']['username'] = $email;
			$datasave['UserClient']['password'] = $password;
			$datasave['UserClient'] = array_merge($datasave['UserClient'], $data['KprApplication']);
			$datasave['UserClient'] = array_merge($datasave['UserClient']['KprApplicationJob'], $datasave['UserClient']);
			$datasave = $this->RmCommon->dataConverter($datasave, array(
				'unset' => array(
					'UserClient' => array(
						'id',
						'kpr_bank_id',
						'KprApplicationJob',
					),
				),
			));

			$value = $this->controller->User->getData('first', array(
				'conditions' => array(
					'User.email LIKE' => '%'.$email.'%',
				),
			));
			$user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
			$value = $this->controller->User->UserClient->getMerge($value, $user_id, $parent_id);
			
			if(!empty($value)){
				$datasave['User']['id'] = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$datasave['User']['group_id'] = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');
				$datasave['UserClient']['id'] = $this->RmCommon->filterEmptyField($value, 'UserClient', 'id');
				$datasave['UserClient']['user_id'] = $this->RmCommon->filterEmptyField($value, 'User', 'id');
			}
		}
		return $datasave;
	}

	function createClientOnfly($data, $model = 'User', $client_type_id = 1){
		$data_temp = array();
		
		$this->User = ClassRegistry::init('User');
		$this->UserClient = ClassRegistry::init('UserClient');

		$password 	= $this->_generateCode('user_code', false, 6);
		$email 		= Common::hashEmptyField($data, $model.'.email');

		$check_user = $this->User->getData('first', array(
			'conditions' => array(
				'User.email' => $email,
			)
		), array(
			'role' => 'client',
			'status' => 'active'
		));

		if(empty($check_user)){
			$data_temp = Hash::insert($data_temp, 'UserClient', array(
				'full_name' 		=> Common::hashEmptyField($data, $model.'.full_name'),
				'client_type_id' 	=> $client_type_id,
				'company_id' 		=> $this->controller->parent_id,
				'token' 			=> String::uuid(),
				'password' 			=> $password,
				'auth_password' 	=> $this->Auth->password($password),
				'no_hp' 			=> Common::hashEmptyField($data, $model.'.no_hp'),
			));

			$data_temp = Hash::insert($data_temp, 'User', array(
				'group_id' 			=> 10,
				'full_name' 		=> Common::hashEmptyField($data, $model.'.full_name'),
				'email' 			=> $email,
				'code' 				=> $this->_generateCode('user_code'),
			));

			$data_temp = Hash::insert($data_temp, 'UserProfile', array(
				'no_hp' 			=> Common::hashEmptyField($data, $model.'.no_hp'),
			));

			$data_temp = Hash::insert($data_temp, 'UserConfig', array(
				'activation_code' 	=> $this->_generateCode()
			));

			$data_client['UserClient'] = Common::hashEmptyField($data_temp, 'UserClient');

			unset($data_temp['UserClient']);

			$flag = $this->User->saveAll($data_temp);

			if(!empty($flag)){
				$data_client = Hash::insert($data_client, 'UserClient.user_id', $this->User->id);

				$this->UserClient->saveAll($data_client);
			}
		}
	}

	function _callDataRegister ( $data, $group_id = false ) {
	//	register client
		if( $group_id == 10 && !empty($data) ) {
			$data['User']['password'] = $this->_generateCode('user_code', false, 6);

			if( !empty($data['UserClient']) ) {
				$userClient = $data['UserClient'];
			} else {
				$userClient = array();
			}

			$data['UserClient'] = array_merge($userClient, $data['UserProfile']);
			$data['UserClient']['full_name'] = $data['User']['full_name'];
			$data['UserClient']['client_type_id'] = $data['User']['client_type_id'];
			$data['UserClient']['gender_id'] = !empty($data['User']['gender_id'])?$data['User']['gender_id']:NULL;
			$data['UserClient']['photo'] = $this->RmCommon->filterEmptyField($data, 'User', 'photo');
			$data['UserClient']['company_id'] = $this->controller->parent_id;
			$data['UserClient']['token'] = String::uuid();
		}

	//	check deleted user by email
		$email			= Common::hashEmptyField($data, 'User.email', false);
		$deletedUser	= $this->controller->User->getData('first', array(
			'conditions' => array(
				'User.email' => $email, 
			), 
		), array(
			'status' => 'deleted', 
		));

		$userID		= Common::hashEmptyField($deletedUser, 'User.id');
		$userCode	= Common::hashEmptyField($deletedUser, 'User.code');

		if($userID){
			$data = Hash::insert($data, 'User.id', $userID);
		}

		if( !empty($data['User']['password']) ) {
			$data['User']['auth_password'] = $this->Auth->password($data['User']['password']);

			$data['User']['code'] = $userCode ?: $this->_generateCode('user_code');

			if(empty($userID)){
			//	hanya untuk user baru (deleted user ga butuh ini)
				$data['UserConfig']['activation_code']	= $this->_generateCode();
			}
		}
		else if( isset($data['User']['current_password']) ) {
			if( !empty($data['User']['current_password']) ) {
				$data['User']['current_password'] = $this->Auth->password($data['User']['current_password']);
			}
			if( !empty($data['User']['new_password']) ) {
				$data['User']['new_password_ori'] = $data['User']['new_password'];
				$data['User']['new_password'] = $this->Auth->password($data['User']['new_password']);
			}
			if( !empty($data['User']['new_password_confirmation']) ) {
				$data['User']['new_password_confirmation'] = $this->Auth->password($data['User']['new_password_confirmation']);
			}
		}
		
		if( !empty($data['UserConfig']['commission']) ) {
			$data = $this->RmCommon->dataConverter($data, array(
                'price' => array(
                    'UserConfig' => array(
                        'commission',
                    ),
                )
            ));
		}

		if( !empty($data['User']['membership_package_id']) ) {
			$data['User']['membership_package_id'] = $data['User']['membership_package_id'];
		}

		return $data;
	}

	function _callUserBeforeSave ( $data, $user ) {
		if( !empty($data) ) {
			$is_edited_username = false;
			$username_disabled = $this->RmCommon->filterEmptyField($user, 'UserConfig', 'username_disabled');
			$username = $this->RmCommon->filterEmptyField($user, 'User', 'username');
			$usernameEdited = $this->RmCommon->filterEmptyField($data, 'User', 'username');

			if ( $username != $usernameEdited && empty($username_disabled) ) {
				$data['UserConfig']['username_disabled'] = true;
			} else if( !empty($username_disabled) ) {
				$data['User']['username'] = $user['User']['username'];
				$this->controller->request->data = $data;
			} else {
				unset($data['User']['username']);
			}

			if(!empty($user)){
				$password = Common::hashEmptyField($data, 'User.password');
				$password_confirmation = Common::hashEmptyField($data, 'User.password_confirmation');

				if(empty($password) && empty($password_confirmation)){
					$data = Common::_callUnset($data, array(
						'User' => array(
							'password',
							'password_confirmation',
						),
					));
				}
			}

		}

		return $data;
	}

	function _callUserCompanyBeforeSave ( $data = false, $value = false ) {
		if( !empty($data) ) {
			$name = $this->RmCommon->filterEmptyField($data, 'UserCompany', 'name');
			
			$user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id', null);

			if( !empty($user_id) ) {
				$data['User']['id'] = $user_id;
				$data['User']['modified'] = $this->RmCommon->currentDate();
			}

			if ( !empty($name) ) {
				$data['UserCompany']['slug'] = $this->RmCommon->toSlug($name);
			}
		}

		return $data;
	}

	function refreshAuth ( $user_id ) {
		$user_login_id = Configure::read('User.id');

		if( $user_id == $user_login_id ) {
			$user = $this->controller->User->getMerge(array(), $user_id);
			if( Configure::read('User.group_id') == 10 ) {

				$client_id = $this->RmCommon->filterEmptyField( $this->Auth->user(), 'UserClient', 'id' );
				$client = $this->controller->User->UserClient->getData('first', array(
					'conditions' => array(
						'UserClient.id' => $client_id,
						'UserClient.user_id' => $user_id,
						'UserClient.company_id' => Configure::read('Principle.id'),
					),
				));

				if( isset($client['UserClient']) ) {
					$user['User']['UserClient'] = $client['UserClient'];
				}
			}

			$this->Auth->login($user, false, true);
		}
	}

	function userRegister($data, $email, $options = array(), $modelName = 'KprApplication'){
		$first_name = $this->RmCommon->filterEmptyField($options, 'first_name');
		$last_name = $this->RmCommon->filterEmptyField($options, 'last_name');
		$no_hp = $this->RmCommon->filterEmptyField($options, 'no_hp');

		$gender_id = $this->RmCommon->filterEmptyField($data, $modelName, 'gender_id', null);
		$region_id = $this->RmCommon->filterEmptyField($data, $modelName, 'region_id', null);
		$city_id = $this->RmCommon->filterEmptyField($data, $modelName, 'city_id', null);
		$subarea_id = $this->RmCommon->filterEmptyField($data, $modelName, 'subarea_id', null);
		$address = $this->RmCommon->filterEmptyField($data, $modelName, 'address');
		$zip = $this->RmCommon->filterEmptyField($data, $modelName, 'zip');
		$birthplace = $this->RmCommon->filterEmptyField($data, $modelName, 'birthplace');
		$ktp = $this->RmCommon->filterEmptyField($data, $modelName, 'ktp');
		$status_marital = $this->RmCommon->filterEmptyField($data, $modelName, 'status_marital');
		$birthday = $this->RmCommon->filterEmptyField($data, $modelName, 'birthday');
		$phone = $this->RmCommon->filterEmptyField($data, $modelName, 'phone');
		$no_hp_2 = $this->RmCommon->filterEmptyField($data, $modelName, 'no_hp_2');
		$job_type = $this->RmCommon->filterEmptyField($data, $modelName, 'job_type_id', null);

		$user = $this->controller->User->find('first', array(
			'conditions' => array(
				'email' => trim($email),
			),
		));

		$user_id = !empty($this->user_id)?$this->user_id:$this->RmCommon->filterEmptyField($user, 'User', 'id');

		if(!empty($user_id)){
			$data['User']['id'] = $user_id;
		}else{
			$lenPassword = 6;
			$password = $this->createRandomNumber($lenPassword);
			$password = $this->array_random($password, $lenPassword);
			$password = strtoupper(implode('', $password));
			$generatePassword = $this->Auth->password($password);
			
			$data['User']['first_name'] = $first_name;
			$data['User']['last_name'] = $last_name;
			$data['User']['email'] = $email;
			$data['User']['gender_id'] = $gender_id;
			$data['User']['code'] = $this->_generateCode('user_code');
			$data['User']['beforehash'] = $password;
			$data['User']['password'] = $generatePassword;
			$data['User']['password_confirmation'] = $generatePassword;
			$data['User']['active'] = TRUE;
			$data['User']['deleted'] = FALSE;
			
			$data['UserProfile'] = array(
				'no_hp' => $no_hp,
				'region_id' => $region_id,
				'city_id' => $city_id,
				'subarea_id' => $subarea_id,
				'address' => $address,
				'zip' => $zip,
				'birthplace' => $birthplace,
				'ktp' => $ktp,
				'status_marital' => $status_marital,
				'birthday' => $birthday,
				'phone' => $phone,
				'no_hp' => $no_hp,
				'no_hp_2' => $no_hp_2,
				'job_type' => $job_type,
			);
		}
		return $data;
	}

	function _callMessageBeforeSave( $to_id = false, $property_id = 0, $data = array() ) {
		$data = $data ?: $this->controller->request->data;

		if($data){
			$dataUser = Configure::read('User.data');
			$from_id = $this->controller->user_id;

			$name = false;
			$phone = false;
			$autoRegister = false;
			$dataMsg = false;

			$useRecaptcha	= Hash::check($data, 'g-recaptcha-response');
			$email			= Common::hashEmptyField($data, 'Message.email');
			$message		= Common::hashEmptyField($data, 'Message.message');

			if($useRecaptcha){
				$security_code	= Common::hashEmptyField($data, 'g-recaptcha-response');
				$data			= Hash::remove($data, 'g-recaptcha-response');
			}
			else{
				$security_code = Common::hashEmptyField($data, 'Message.security_code');
			}

			if( !empty($email) && empty($dataUser) ) {
				$dataUser = $this->controller->User->getData('first', array(
					'conditions' => array(
						'User.email' => $email,
					),
				), array(
					'status' => 'semi-active',
				));

				$dataUser = $this->RmCommon->filterEmptyField($dataUser, 'User');
				$from_id = $this->RmCommon->filterEmptyField($dataUser, 'id');

				if( !empty($dataUser) ) {
					$dataUser = $this->controller->User->UserProfile->getMerge($dataUser, $from_id);
				}
			}

			if( !empty($dataUser) ) {
				$email = $this->RmCommon->filterEmptyField($dataUser, 'email');
				
				$phone = $this->RmCommon->filterEmptyField($data, 'Message', 'phone');
				$name = $this->RmCommon->filterEmptyField($data, 'Message', 'name');

				if( empty($phone) ) {
					$phone = $this->RmCommon->filterEmptyField($dataUser, 'UserProfile', 'no_hp');
				}
				if( empty($name) ) {
					$name = $this->RmCommon->filterEmptyField($dataUser, 'full_name');
				}
			} else {
				$name = $this->RmCommon->filterEmptyField($data, 'Message', 'name');
				$phone = $this->RmCommon->filterEmptyField($data, 'Message', 'phone');
			}

			$region_id = $this->RmCommon->filterEmptyField($dataUser, 'UserProfile', 'region_id', null);
			$city_id = $this->RmCommon->filterEmptyField($dataUser, 'UserProfile', 'city_id', null);
			$subarea_id = $this->RmCommon->filterEmptyField($dataUser, 'UserProfile', 'subarea_id', null);

			if( empty($from_id) ) {
				$randomPassword = $this->createRandomNumber(6);
				$randomPassword = $this->array_random($randomPassword, count($randomPassword));
				$randomPassword = strtoupper(implode('', $randomPassword));

				$user_check = $this->controller->User->getData('first', array(
					'conditions' => array(
						'User.email' => $email
					)
				));
				
				$from_id = $this->RmCommon->filterEmptyField($user_check, 'User', 'id');

				if(empty($from_id)){
					$autoRegister = array(
	                    'User' => array(
	                        'first_name' => $this->_callSplitName($name, 'first_name'),
	                        'last_name' => $this->_callSplitName($name, 'last_name'),
	                        'email' => $email,
							'password' => $randomPassword,
							'password_confirmation' => $randomPassword,
							'active' => 1,
	                    ),
	                    'UserProfile' => array(
	                        'no_hp' => $phone,
	                    ),
	                );
				}
			}

			$curr_date = date('Y-m-d H:i:s');

			$tmpDataMsg = array(
				'property_id' => $property_id,
				'from_id' => $from_id,
				'region_id' => $region_id,
				'city_id' => $city_id,
				'subarea_id' => $subarea_id,
				'name' => $name,
				'email' => $email,
				'phone' => $phone,
				'message' => $message,
				'status' => $this->RmCommon->filterEmptyField($data, 'Message', 'status', true, array('type' => 'isset')),
				'created' => $this->RmCommon->filterEmptyField($data, 'Message', 'created', $curr_date, array('type' => 'isset')),
				'modified' => $this->RmCommon->filterEmptyField($data, 'Message', 'modified', $curr_date, array('type' => 'isset')),
			);

			if(!Configure::read('__Site.is_rest')){
				$tmpDataMsg['utm'] = FULL_BASE_URL;
			}else{
				$tmpDataMsg['utm'] = $this->RmCommon->filterEmptyField($data, 'Message', 'from_type_view');
			}

			if( !empty($to_id) && is_array($to_id) ) {
				foreach ($to_id as $key => $toId) {
					$dataMsg[$key]['Message'] = $tmpDataMsg;
					$dataMsg[$key]['Message']['to_id'] = $toId;
					$dataMsg[$key]['Message']['security_code'] = $security_code;
				}
			} else {
				$dataMsg = $tmpDataMsg;
				$dataMsg['to_id'] = $to_id;
				$dataMsg['security_code'] = $security_code;
			}

			if($useRecaptcha){
				$SecurityCode = !empty($security_code);
			}
			else{
				if(Configure::read('__Site.is_rest')){
					$SecurityCode = true;
				}else{
					$SecurityCode = $this->controller->Captcha->validates( $security_code );
				}	
			}

			return array(
				'data' => array(
					'Message' => $dataMsg,
                    'Register' => $autoRegister,
                    'SecurityCode' => $SecurityCode,
					'Data' => array(
						'Message' => $tmpDataMsg,
					),
				),
			);
		} else {
			return false;
		}
	}

	function _callDataClient ( $data, $swap_data_only = false ) {
		$data_request = $this->controller->request->data;

		if( !empty($swap_data_only) ) {		
			if( !isset($data['UserClient']['agent_pic_email']) ) {
				$agent_id = $this->RmCommon->filterEmptyField($data, 'UserClient', 'agent_id');
				$email = $this->RmCommon->filterEmptyField($data, 'User', 'email');
				unset($data['User']);
				
				$data = $this->controller->User->getMerge($data, $agent_id);
				$agent_pic_email = $this->RmCommon->filterEmptyField($data, 'User', 'email');
				$data['UserClient']['agent_pic_email'] = $agent_pic_email;
				$data['UserClient']['email'] = $email;
			}
		} else {
			if( !empty($data['UserProfile']['Region']) ) {
				$data['Region'] = $data['UserProfile']['Region'];
				unset($data['UserProfile']['Region']);
			}
			if( !empty($data['UserProfile']['City']) ) {
				$data['City'] = $data['UserProfile']['City'];
				unset($data['UserProfile']['City']);
			}
			if( !empty($data['UserProfile']['Subarea']) ) {
				$data['Subarea'] = $data['UserProfile']['Subarea'];
				unset($data['UserProfile']['Subarea']);
			}
		}

		if( empty($data_request) && !empty($data) ) {
			$this->callContentLable($data);
		}

		return $data;
	}

	function setConvertSetToV3($data){
        $parent_id = $this->RmCommon->filterEmptyField($data, 'User', 'parent_id');

        if(!empty($parent_id)){
        	unset($data['User']['parent_id']);
        }

        $arrModel = array(
        	'UserProfile',
        	'UserCompany'
        );

        foreach ($arrModel as $key => $value) {
        	if(!empty($data[$value]['id'])){
        		unset($data[$value]['id']);
        	}

        	if(!empty($data[$value]['user_id'])){
        		unset($data[$value]['user_id']);
        	}
        }

        $group_id = $this->RmCommon->filterEmptyField($data, 'User', 'group_id');
		$client_type_id = $this->RmCommon->filterEmptyField($data, 'User', 'client_type_id');

		switch ($group_id) {
			case '10':
				$data['User']['group_id'] = 1;
				$data['User']['parent_id'] = 0;
				break;
			case '2':
				$user = $this->controller->User->getData('first', array(
					'conditions' => array(
						'User.id' => $parent_id
					)
				));

				if(!empty($user['User']['user_id_target'])){
					$data['User']['parent_id'] = $user['User']['user_id_target'];
				}
				break;
			case '3':
				$data['UserCompany']['have_company_website'] = 1;
				break;
		}

		if(!empty($client_type_id)){
			switch ($client_type_id) {
				case 1:
					$user_type_id = 2; // Saya ingin membeli rumah
					break;
				case 2:
					$user_type_id = 1; // saya ingin menjual properti
					break;
				default:
					$user_type_id = 3; // saya ingin mencari informasi properti
					break;
			}
			
			$data['User']['user_type_id'] = $user_type_id;
		}

        return $data;
    }

    function _callRestValidateAccess () {
		$version = Configure::read('Rest.validate');
		$status = $this->RmCommon->filterEmptyField($version, 'status');
		$msg = $this->RmCommon->filterEmptyField($version, 'msg');

		if( !empty($version) ) {
			if( $status < 0 ) {
				$this->controller->redirect(array(
					'controller' => 'users',
					'action' => 'message',
					$msg,
					$status,
					'admin' => false,
					'plugin' => false,
					'api' => true,
					'ext' => 'json',
				));
			} else {
				$this->RmCommon->setCustomFlash($msg, $status, false, false);
			}
		} else {
			$this->RmCommon->setCustomFlash(__('Unaccepted User'), 'error', false, false);
		}
	}

    function _callCheckAPI ( $user, $data ) {
    	$user = false;
		$device = $this->RmCommon->filterEmptyField($data, 'device');
		$device_handheld = $this->RmCommon->filterEmptyField($data, 'device_handheld');
		$appversion = $this->RmCommon->filterEmptyField($data, 'appversion');
		$token = $this->RmCommon->filterEmptyField($data, 'token');
		$passkey = $this->RmCommon->filterEmptyField($data, 'passkey', false, $token);

		if( !empty($device) ) {
			$this->controller->loadModel('Setting');

			$slugDevice = $this->RmCommon->toSlug($device);
			$setting = $this->controller->Setting->find('first', array(
				'conditions' => array(
					'slug' => $slugDevice,
				),
			));
			$access_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
			$value = $this->RmCommon->filterEmptyField($setting, 'Setting', 'value');
			$link = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
			$allow_passkey = $this->RmCommon->filterEmptyField($setting, 'Setting', 'token');

			$data = $this->RmCommon->_callUnset(array(
				'device',
				'appversion',
				'passkey',
				'CakeCookie',
				'username',
				'class',
			), $data);

			$version['id'] = $access_id;
			$version['passkey'] = $passkey;

			if( $passkey != $allow_passkey ) {
				$version['status'] = -4;
				$version['msg'] = __('Passkey tidak valid atau sudah tidak berlaku');
			} else 
			if( $value != $appversion ) {
				$version['status'] = -2;
				$version['link'] = $link;
				$version['appversion'] = $value;
				$version['msg'] = __('Apakah anda ingin download aplikasi terbaru?');
			} else if( empty($setting) ) {
				$version['status'] = -3;
				$version['msg'] = __('Platform device tidak terdaftar');
			} else {
				$version['status'] = 1;
				$version['msg'] = __('User accepted');
			}
		} else {
			$version['status'] = -5;
			$version['msg'] = __('Anda tidak memiliki hak untuk mengakses halaman ini.');
		}

		Configure::write('Rest.validate', $version);

		if( $version['status'] === 1 ){
			if( !empty($token) ) {
				$verify_token = $this->controller->User->_callVerifyToken($token);

				if( !empty($verify_token) ) {
					$user = $this->RmCommon->filterEmptyField($verify_token, 'User');
					$user_id = Common::hashEmptyField($user, 'id');

					//Log Activity
					$this->RmCommon->doLogView($user_id, $user, array(
						'slug' => 'daily',
						'is_mobile' => true,
						'device' => $device_handheld,
					));
					// 

					$params = $this->controller->params->params;

					$this->controller->Auth->login($user, false, true, $params);

					Configure::write('Rest.token', $token);
				} else {
					Configure::write('Rest.validate', array(
						'status' => -1,
						'msg' => __('Tidak dapat mengakses halaman ini dikarenakan akun milik Anda sedang digunakan pada perangkat lain'),
					));
				}
				
				$data = $this->RmCommon->_callUnset(array(
					'token',
					'passkey',
				), $data);
			} else if( !empty($passkey) ) {				
				Configure::write('Rest.token', $passkey);
			}
		}

		$this->_callRestValidateAccess();
		$this->controller->set(compact(
			'appversion', 'device'
		));

		if(!empty($device)){
			$this->controller->request->data = $data;
		}
		return $user;
    }

    function tokenCheck($user, $params){
    	$credential_data = $this->controller->Rest->credentials();
    	$data_request = $this->controller->request->data;
    	$data_request = Common::hashEmptyField($data_request, 'Attribute', array());

    	if( empty($data_request) ) {
    		$data_input = $this->controller->request->input();

    		if( !empty($_REQUEST['device']) ) {
    			$data_request = $_REQUEST;
    		} else if( !empty($data_input) ) {
    			$data_request = json_decode($data_input, true);
    		}
    	}
    	if( !empty($credential_data) ) {
    		if( empty($data_request) && !empty($_REQUEST) ) {
    			$data_request = $_REQUEST;
    		}

    		$data_request = array_merge($data_request, $credential_data);
    	}

    	$device = Common::hashEmptyField($data_request, 'device', null, array(
    		'type' => 'strtolower',
		));
    	$action = $this->RmCommon->filterEmptyField($params, 'action');

		if( in_array($device, array( 'android', 'ios' )) && $this->controller->Rest->isActive() && $action != 'api_message' ){
			if(isset($data_request['RumahkuV2'])){
				unset($data_request['RumahkuV2']);
			}
			
			$data = $this->RmCommon->getMergePost($data_request);
			return $this->_callCheckAPI($user, $data);
		} else {
	    	$token = $this->RmCommon->filterEmptyField($params, 'named', 'token');
	    	$controller = $this->RmCommon->filterEmptyField($params, 'controller');

	    	if( !empty($token) ){
		    	if( !in_array($action, array( 'admin_login', 'login', 'messages' )) ){
		    		$this->controller->redirect(array(
		    			'controller' => 'users',
		    			'action' => 'login',
		    			'token' => $token,
		    			'admin' => true
		    		));
		    	} else if( !in_array($controller, array( 'api_users' )) ) {
		    		$data_api = $this->RumahkuApi->api_access($token, 'validate_token');
		    		
					if(!empty($data_api)){
						$data_api = json_decode($data_api, true);
						$data_api = $data_api['data'];

						if(!empty($data_api['status']) && !empty($data_api['email'])){
							$this->controller->request->data['User']['username'] = $data_api['email'];
							$this->controller->request->data['User']['token'] = $token;
						}else{
							$this->RmCommon->setProcessParams(array(
								'msg' => __('Token Anda tidak valid!!'),
								'status' => 'error'
							), array(
								'controller' => 'users',
				    			'action' => 'login',
				    			'admin' => true
							), array(
								'redirectError' => true
							));
						}
					}
		    	}
		    }
		  //   else if( $this->controller->Rest->isActive() && $controller != 'users' && $action != 'api_message' ){
				// Configure::write('Rest.validate', array(
				// 	'status' => -5,
				// 	'msg' => __('Anda tidak memiliki hak untuk mengakses halaman ini'),
				// ));
				// $this->_callRestValidateAccess();
		  //   }
		}

		return $user;
    }

    function _callConvertClientEmail ( $data, $modalName = 'Property' ) {
		if( !empty($data[$modalName]['client_email']) ) {
			$pos = strpos($data[$modalName]['client_email'], '|');

			if( !empty($pos) ) {
				$client_email = explode('|', $data[$modalName]['client_email']);

				if( !empty($client_email[0]) ) {
					$data[$modalName]['client_email'] = trim($client_email[0]);
				}
			}
		}

		return $data;
    }

    function _callGetClientData( $data, $modelName = 'CrmProject' ) {
    	if(!empty($data[$modelName]['client_email'])){
        	$email = $data[$modelName]['client_email'];
            $email = $this->RmCommon->getEmailConverter($email);
            $user_data = $this->controller->User->getData('first', array(
                'conditions' => array(
                    'User.email' => $email
                )
            ), array(
                'role' => 'client',
                'status' => 'semi-active',
            ));

            if( !empty($user_data) ) {
                $data[$modelName]['client_email'] = $email;
                $data[$modelName]['client_id'] = $this->RmCommon->filterEmptyField($user_data, 'User', 'id', '');
            } else {
                $data[$modelName]['client_id'] = false;
                $data[$modelName]['client_password'] = $this->_generateCode('password', false, 6);
                $data[$modelName]['client_auth_password'] = $this->controller->Auth->password($data[$modelName]['client_password']);
                $data[$modelName]['client_code'] = $this->_generateCode('user_code');
            }
        }

        return $data;
    }

    function _callBeforeView ( $data , $value = false) {
		$parent_email = $this->RmCommon->filterEmptyField($data, 'Parent', 'email');
		$group_id = Common::hashEmptyField($value, 'User.group_id');
		$group_id = Common::hashEmptyField($data, 'User.group_id', $group_id);

		if($group_id){
			switch ($group_id) {
				case '2':
					$this->controller->set('user_type', 'agent');
					break;
			}
		}

		if( !empty($parent_email) ) {
			$data['User']['parent_email'] = $parent_email;
		}

		$subareaID = Common::hashEmptyField($data, 'UserProfile.subarea_id');

		if($subareaID){
			$location		= $this->RmCommon->getViewLocation($subareaID);
			$locationName	= Common::hashEmptyField($location, 'ViewLocation.location_name');

			$data['UserProfile']['location_name'] = $locationName;
		}

    	return $data;
    }

    function _callAuthLoginRest ( $user ) {
		$token = String::uuid();
		$user_id = $this->RmCommon->filterEmptyField($user, 'id');
		$group_id = $this->RmCommon->filterEmptyField($user, 'group_id', false, null);

		$user = $this->controller->User->UserConfig->getMerge($user, $user_id);
		$user = $this->controller->User->Group->getMerge($user, $group_id);

		$user_config_id = $this->RmCommon->filterEmptyField($user, 'UserConfig', 'id', null);

		if( $this->controller->Rest->isActive() ){
			$this->controller->User->updateToken( $user_id, $user_config_id, $token );
			$this->controller->set('token', $token);

			$User = $this->Auth->user();
			$User = $this->controller->User->getAllNeed($User, $user_id, $group_id);

			$this->controller->set('data', $User);

			$company_data = $this->getDataCompanyFromApi($User);

			$this->controller->set('company_data', $company_data);
    		return true;
    	} else {
    		return false;
    	}
    }

    function getDataCompanyFromApi($user_data = array(), $url = false, $params = array()){
		if(!empty($user_data)){
			$group_id 	= $this->RmCommon->filterEmptyField($user_data, 'group_id');
			$parent_id 	= $this->RmCommon->filterEmptyField($user_data, 'parent_id');
			$id 		= $this->RmCommon->filterEmptyField($user_data, 'id');

			$isIndependent = Common::validateRole('independent_agent', $group_id);

			if(empty($isIndependent)){
				if($group_id == 3){
					$parent_id = $id;
				}

				$user_data = $this->controller->User->getDataCompany($url, array_merge(array(
					// 'company_principle_id' => $parent_id
				), $params));

				if(empty($user_data)){
				//	personal page
					$user_data = $this->controller->User->getPersonalPageData($url, $params);
				}
			}
			else{
			//	independent pasti prime domain
				$params		= array_merge($params, array('user_id' => $id));
				$user_data	= $this->controller->User->getPersonalPageData($url, $params);
			}

			return $user_data;
		}else{
		//	get company config
			$user_data = $this->controller->User->getDataCompany($url, $params);

			if(empty($user_data)){
			//	personal page
				$user_data = $this->controller->User->getPersonalPageData($url, $params);
			}

			return $user_data;
		}
	}

	function _callRoleBySlug ( $type ) {
		$params = $this->controller->params;
		$slug = $this->RmCommon->filterEmptyField($params, 'slug');
		$role = $type;

		switch ($slug) {
			case 'director':
				$data = $this->controller->request->data;
				$role = __('%s-director', $type);

				if( !empty($data) ) {
					$this->controller->request->data['User']['role'] = 'director';
				}

				$this->controller->set('active_menu', 'director_admin');
				break;
			
			default:
				$this->controller->set('active_menu', 'principle_admin');
				break;
		}

		return $role;
	}

	function saveApiDataMigrate($data){
		$this->User = $this->controller->User;

		$email 		= $this->RmCommon->filterEmptyField($data, 'User', 'email');
		$username 	= $this->RmCommon->filterEmptyField($data, 'User', 'username');
		$code 		= $this->RmCommon->filterEmptyField($data, 'User', 'code');
		
		/*has many*/
		$UserLanguage 			= $this->RmCommon->filterEmptyField($data, 'UserLanguage');
		$UserPropertyType 		= $this->RmCommon->filterEmptyField($data, 'UserPropertyType');
		$UserSpecialist 		= $this->RmCommon->filterEmptyField($data, 'UserSpecialist');
		$UserAgentCertificate 	= $this->RmCommon->filterEmptyField($data, 'UserAgentCertificate');
		$UserClientType 		= $this->RmCommon->filterEmptyField($data, 'UserClientType');

		$exist_user = $this->User->getData('first', array(
			'conditions' => array(
				'User.email' => $email
			)
		), array(
			'status' => 'all'
		));

		if(empty($exist_user)){
			$data['User']['user_id_target'] = $this->RmCommon->filterEmptyField($data, 'User', 'id');

			$data = $this->RmCommon->_callUnset(array(
				'User' => array(
					'id'
				),
				'UserProfile' => array(
					'id',
					'user_id'
				)
			), $data);
				

			if(!empty($UserPropertyType)){
				unset($data['UserPropertyType']);
				foreach ($UserPropertyType as $key => $value) {
					$value = $this->RmCommon->_callUnset(array(
						'UserPropertyType' => array(
							'id',
							'user_id'
						),
					), $value);

					$data['UserPropertyType'][] = $this->RmCommon->filterEmptyField($value, 'UserPropertyType');
				}
			}

			if(!empty($UserSpecialist)){
				unset($data['UserSpecialist']);
				foreach ($UserSpecialist as $key => $value) {
					$value = $this->RmCommon->_callUnset(array(
						'UserSpecialist' => array(
							'id',
							'user_id'
						),
					), $value);

					$data['UserSpecialist'][] = $this->RmCommon->filterEmptyField($value, 'UserSpecialist');
				}
			}

			if(!empty($UserLanguage)){
				unset($data['UserLanguage']);
				foreach ($UserLanguage as $key => $value) {
					$value = $this->RmCommon->_callUnset(array(
						'UserLanguage' => array(
							'id',
							'user_id'
						),
					), $value);

					$data['UserLanguage'][] = $this->RmCommon->filterEmptyField($value, 'UserLanguage');
				}
			}

			if(!empty($UserAgentCertificate)){
				unset($data['UserAgentCertificate']);
				foreach ($UserAgentCertificate as $key => $value) {
					$value = $this->RmCommon->_callUnset(array(
						'UserAgentCertificate' => array(
							'id',
							'user_id'
						),
					), $value);

					$data['UserAgentCertificate'][] = $this->RmCommon->filterEmptyField($value, 'UserAgentCertificate');
				}
			}

			if(!empty($UserClientType)){
				unset($data['UserClientType']);
				foreach ($UserClientType as $key => $value) {
					$value = $this->RmCommon->_callUnset(array(
						'UserClientType' => array(
							'id',
							'user_id'
						),
					), $value);

					$data['UserClientType'][] = $this->RmCommon->filterEmptyField($value, 'UserClientType');
				}
			}


			$exist_unique = $this->User->getData('first', array(
				'conditions' => array(
					'OR' => array(
						array('User.username' => $username),
						array('User.code' => $code),
					)
				)
			), array(
				'status' => 'all'
			));

			if(!empty($exist_unique)){
				$exist_username 	= $this->RmCommon->filterEmptyField($exist_unique, 'User', 'username');
				$exist_code 		= $this->RmCommon->filterEmptyField($exist_unique, 'User', 'code');
			
				if($exist_username == $username){
					$data['User']['username'] = '';
				}

				if($exist_code == $code){
					$data['User']['code'] = $this->_generateCode('user_code');
				}
			}
			
			$this->User->removeValidator();
			$this->User->saveAll($data);
		}
	}

	function userExist($data, $options = array()){
		if(!empty($data)){
			$model = $this->RmCommon->filterEmptyField($options, 'model', false, 'User');
			$modelTarget = $this->RmCommon->filterEmptyField($options, 'modelTarget', false, 'User');
			$fieldTarget = $this->RmCommon->filterEmptyField($options, 'fieldTarget', false, 'id');

			$userProfile = $this->RmCommon->filterEmptyField($data, $model, 'UserProfile');
			$no_hp = $this->RmCommon->filterEmptyField($userProfile, 'no_hp');

			$email = $this->RmCommon->filterEmptyField($data, $model, 'email');
			$first_name = $this->RmCommon->filterEmptyField($data, $model, 'first_name');
			$last_name = $this->RmCommon->filterEmptyField($data, $model, 'last_name');
			$last_name = $this->RmCommon->filterEmptyField($data, $model, 'last_name');

			$value = $this->userRegister(array(), $email, array(
				'first_name' => $first_name,
				'last_name' => $last_name,
				'no_hp' => $no_hp,
			));
			$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');

			if(empty($id)){
				$id = $this->controller->User->doSave($data);
			}
			
			$data[$modelTarget][$fieldTarget] = $id;
		}
		return $data;
	}

	function _callBeforeViewUser () {
    	$populers = $this->controller->User->Property->populers(5);
		$propertyTypes = $this->controller->User->Property->PropertyType->getData('list', array(
            'cache' => __('PropertyType.List'),
        ));
		$displayShow = $this->RmCommon->filterEmptyField($this->controller->params, 'named', 'show', 'grid');

		$this->RmCommon->_layout_file(array(
			'map',
			'map-cozy',
		));
		$this->controller->set(compact(
			'displayShow', 'populers', 'propertyTypes'
		));
	}


	function UserExisting($datas){
		if(!empty($datas)){
			foreach($datas AS $key => $data){
				// debug($data);die();
				$dataUser = $this->setClientFromRKU($data, 10);	
				$user_id = $this->RmCommon->filterEmptyField($dataUser, 'User', 'id');
				$client_id = $this->RmCommon->filterEmptyField($dataUser, 'UserClient', 'id');

				if(empty($user_id)){
					$user_id = $this->controller->User->apiSave($dataUser);
				}else{
					$user_id = $this->controller->User->UserClient->apiSave($dataUser, $client_id);
				}

				if(!empty($user_id)){
					$data['KprBank']['user_id'] = $user_id;
				}
				$datas[$key] = $data;
			}
		}
		return $datas;
	}


	// function UserExisting($datas){
	// 	if(!empty($datas)){
	// 		foreach($datas AS $key => $data){
	// 			$user = $this->RmCommon->filterEmptyField($data, 'User');
	// 			if(!empty($user)){
	// 				$email = $this->RmCommon->filterEmptyField($user, 'email');
	// 				$data = $this->getDataEmail($data, $email, array(
	// 					'KprBank' => 'agent_id',
	// 					'Property' => 'user_id',
	// 				));
	// 			}
	// 			$datas[$key] = $data;
	// 		}
	// 	}
	// 	return $datas;
	// }

	function getDataEmail($data, $email, $changes = array()){

		if(!empty($data) && !empty($email)){
			$value = $this->controller->User->find('first', array(
				'conditions' => array(
					'User.email'=> $email,
				),
			));

			$data = $this->controller->User->UserCompany->CompanyExist($data, array(
				'User' => 'parent_id'
			));

			$dataSave['User'] = $this->RmCommon->filterEmptyField($data, 'User');
			$dataSave = $this->RmCommon->dataConverter($dataSave, array(
				'unset' => array(
					'User' => array(
						'id',
						'UserProfile' => array(
							'id',
							'user_id',
						),
					),
				),
			));

			if(!empty($value)){
				$dataSave['User']['id'] = $this->RmCommon->filterEmptyField($value, 'User', 'id');
			}

			$dataSave['UserProfile'] = $this->RmCommon->filterEmptyField($dataSave, 'User', 'UserProfile');
			$dataSave = $this->RmCommon->dataConverter($dataSave, array(
				'unset' => array(
					'User' => array(
						'UserProfile',
						'UserCompany',
					),
				),
			));
			$result = $this->controller->User->apiSaveAll($dataSave);
			$id = $this->RmCommon->filterEmptyField($result, 'id');
			$this->RmCommon->setProcessParams($result, false, array(
				'noRedirect' => true,
			));

			if(!empty($id)){
				if(!empty($changes)){
					foreach($changes AS $modelName => $field){
						$data[$modelName][$field] = $id;
					}
				}
			}
		}
		return $data;
	}

	function _callBeforeSaveContact ( $value, $redirect = false ) {
		$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');

		// Proses Contact
		$data = $this->_callMessageBeforeSave($id);
		$result = $this->controller->User->Message->doSend($data, $value, false, 'message_agent');
		$this->RmCommon->setProcessParams($result, $redirect, array(
			'ajaxFlash' => true,
			'ajaxRedirect' => true,
		));

		if( $this->controller->is_ajax ) {
			$this->controller->layout = false;
		}
	}

	function _callBeforeViewCompany ( $value ) {
		$id = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'id');
		$slug = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'slug');

		$user_id = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'user_id');
		$dataView = $this->RmCommon->_callSaveVisitor($user_id, 'UserView', 'profile_id');
		$this->controller->User->UserView->doSave($dataView);

    	$populers = $this->controller->User->Property->populers(5, array(
    		'parent_id' => $user_id,
		));
		$propertyTypes = $this->controller->User->Property->PropertyType->getData('list', array(
            'cache' => __('PropertyType.List'),
        ));
		
		$this->_callBeforeSaveContact($value, array(
			'controller' => 'users',
			'action' => 'company',
			$id,
			$slug,
			'admin' => false,
		));
		
		$this->RmCommon->_callRequestSubarea('Search');
		$this->controller->set('captcha_code', $this->controller->Captcha->generateEquation());
		$this->controller->set(compact(
			'populers', 'propertyTypes'
		));
	}

	function _callBeforeViewPrinciples ( $options = array() ) {
		$options =  $this->controller->User->_callRefineParams($this->controller->params, array_merge_recursive(array(
			'conditions' => array(
				''
			),
			'group' => array(
				'User.id',
			),
			'order' => array(
				'User.created' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		), $options));
		$this->RmCommon->_callRefineParams($this->controller->params);
		$this->controller->paginate = $this->controller->User->getData('paginate', $options, array(
			'status' => 'semi-active',
			'company' => true,
			'admin' => true,
			'role' => 'principle',
		));
		$values = $this->controller->paginate('User');

		if( !empty($values) ){
			foreach( $values as $key => &$value ) {
				$value = $this->controller->User->getMergeList($value, array(
					'contain' => array(
						'UserProfile',
						'UserConfig',
						'Group',
						'UserCompany',
						'UserCompanyConfig',
					),
				));

				$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

    			$value = $this->getCounterPrinciple($value);
				$value = $this->_callGetLogView($id, $value);

    			// Under Development
    			// $value['Client']['cnt'] = $this->User->Property->_callPrinciplePropertyCount($id);

				if( !empty($parent_id) ) {
					$value = $this->controller->User->getMerge( $value, $parent_id, false, 'Parent' );
					$value = $this->controller->User->UserCompany->getMerge( $value, $parent_id, 'UserCompanyParent' );
				}
			}
		}

		$this->controller->set('_breadcrumb', false);
		return $values;
	}

	function _callGetLogView ( $id, $value ) {
		$logViewOptions = array(
			'conditions' => array(
				'LogView.user_id' => $id,
			),
			'order'=> array(
				'LogView.created' => 'DESC',
				'LogView.id' => 'DESC',
			),
		);

		if( empty($value['LogView']) ) {
			$logView = $this->controller->User->LogView->getData('first', array_merge_recursive($logViewOptions, array(
				'conditions' => array(
					'LogView.type' => 'daily',
				),
			)));
			$value['LogView'] = Common::hashEmptyField($logView, 'LogView');
		}

		if( empty($value['LogLogin']) ) {
			$logLogin = $this->controller->User->LogView->getData('first', array_merge_recursive($logViewOptions, array(
				'conditions' => array(
					'LogView.type' => 'login',
				),
			)));
			$value['LogLogin'] = Common::hashEmptyField($logLogin, 'LogView');
		}

		return $value;
	}

	function _callBeforeViewUserList($options = array(), $params = false, $user = false){
		$non_agent = Common::hashEmptyField($this->controller->params->params, 'named.non_agent');

		// ==== default opt and default element ====
		$default_element = array(
			'status' => array(
				'active',
				'non-active',
			),
			'role' => 'user-general',
			'tree_division' => true,
		);

		$options   =  $this->controller->User->_callRefineParams($this->controller->params, array_merge_recursive(array(
			'group' => array(
				'User.id',
			),
			'order' => array(
				'User.created' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		), $options));

		$this->RmCommon->_callRefineParams($this->controller->params);

		if ($non_agent) {
			$principle_id = $options['conditions']['User.parent_id'];
			$options = array(
				'conditions' => array(
					'User.parent_id'   => $principle_id,
					
				),
			);
			$default_element = array(
				'status' => 'active',
				'role'   => 'non-agent',
			);
		}

		$this->controller->paginate = $this->controller->User->getData('paginate', $options, $default_element);
		$values = $this->controller->paginate('User');

		if( !empty($values) ){
			foreach( $values as $key => &$value ) {
				$admin_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');
					
				$value = $this->controller->User->getAllNeed($value, $admin_id);
				$value = $this->controller->User->getMergeList($value, array(
					'contain' => array(
						'Parent' => array(
							'uses' => 'User',
							'primaryKey' => 'id',
							'foreignKey' => 'superior_id',
						),
						'Group',
						'UserCompany' => array(
							'uses' => 'UserCompany',
							'primaryKey' => 'user_id',
							'foreignKey' => 'parent_id',
						),
					),
				));
				$value = $this->_callGetLogView($admin_id, $value);

				$value['Property']['cnt'] = $this->controller->User->Property->getData('count', array(
    				'conditions' => array(
    					'Property.user_id' => $admin_id,
    					'Property.principle_id' => Common::_getHeadLinerID($group_id, $value),
					),
				), array(
    				'company' => false,
				));
    			$value['Property']['cnt_sold'] = $this->controller->User->Property->getData('count', array(
    				'conditions' => array(
    					'Property.user_id' => $admin_id,
    					'Property.principle_id' => Common::_getHeadLinerID($group_id, $value),
    					'Property.sold' => 1,
					),
				), array(
    				'company' => false,
				));

				$value['User']['user_client_count'] = $this->userClient(array(
					'agent_id' => $admin_id,
				));

				$value = $this->controller->User->UserCompanyEbrochure->getMerge( $value, $admin_id, 'first' );

				if($this->controller->Rest->isActive()){
					$parent = $this->controller->User->getData('first', array(
						'conditions' => array(
							'User.id' => $parent_id
						)
					));
					
					if(!empty($parent['User'])){
						$value['Parent'] = $parent['User'];
					}
				}
			}
		}
		$divisiOptions = $this->controller->User->Group->getDivisionCompany($params, $user);

		$this->RmCommon->_callDataForAPI($values, 'manual');

		$this->controller->set(array(
			'values' => $values,
			'divisiOptions' => $divisiOptions,
		));

		$this->RmCommon->renderRest(array(
			'is_paging' => true
		));
	}

	function _callBeforeViewAgents ( $options = array() ) {
		$options =  $this->controller->User->_callRefineParams($this->controller->params, array_merge_recursive(array(
			'contain' => array(
				'UserCompanyConfigParent',
			),
			'conditions' => array(
				'UserCompanyConfigParent.user_id NOT' => NULL,
			),
			'order' => array(
				'User.created' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'group' => array(
				'User.id',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		), $options));
		$this->RmCommon->_callRefineParams($this->controller->params);
		
		$this->controller->User->bindModel(array(
            'hasOne' => array(
                'UserCompanyConfigParent' => array(
                    'className' => 'UserCompanyConfig',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'UserCompanyConfigParent.user_id = User.parent_id',
                	),
                ),
            )
        ), false);

		$this->controller->paginate = $this->controller->User->getData('paginate', $options, array(
			'status' => array(
				'active',
				'non-active',
			),
			'role' => 'agent',
			'company' => true,
			'admin' => true,
		));
		$values = $this->controller->paginate('User');

		if( !empty($values) ){
			foreach( $values as $key => $value ) {
				$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
				$group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');

				$value = $this->controller->User->getAllNeed($value, $id, $group_id);
				$value = $this->controller->User->UserRemoveAgent->getMerge( $value, $id );
				$value = $this->controller->User->UserCompany->getMerge( $value, $parent_id );
				$value = $this->_callGetLogView($id, $value);

    			$value['Property']['cnt'] = $this->controller->User->Property->getData('count', array(
    				'conditions' => array(
    					'Property.user_id' => $id,
					),
				), array(
    				'company' => false,
				));
    			$value['Property']['cnt_sold'] = $this->controller->User->Property->getData('count', array(
    				'conditions' => array(
    					'Property.user_id' => $id,
    					'Property.sold' => 1,
					),
				), array(
    				'company' => false,
				));

				$value['User']['user_client_count'] = $this->userClient(array(
					'agent_id' => $id,
				));

				$value = $this->controller->User->UserCompanyEbrochure->getMerge( $value, $id, 'first' );

				if($this->controller->Rest->isActive()){
					$value = $this->controller->User->advanceDataUser( $value, $id);
					$value = $this->controller->User->getMerge( $value, $parent_id, false, 'Parent' );
				}

				$values[$key] = $value;
			}
		}

		$this->RmCommon->_callDataForAPI($values, 'manual');

		return $values;
	}

	function userClient($params = array()){
		$default_conditions = array();
		$agent_id = Common::hashEmptyField($params, 'agent_id');
		$principle_id = Common::hashEmptyField($params, 'principle_id', $this->controller->parent_id);

		if($agent_id){
			$default_conditions['UserClient.agent_id'] = $agent_id;
		}

		if($principle_id){
			$default_conditions['UserClient.company_id'] = $principle_id;
		}

		$options = $this->controller->User->UserClient->_callRefineParams(false, array(
			'conditions' => array_merge(array(
				'UserClient.status' => 1,
			), $default_conditions),
			'contain' => array(
				// 'User'
			),
			'order' => array(
				'UserClient.created' => 'DESC',
			),
		));
		$user_options = array_merge($options, $this->controller->User->getData('paginate', $options, array(
			'status' => 'all',
		)));
		return $this->controller->User->UserClient->getData('count', $user_options);
	}

	function _callRoleCondition ( $value = false ) {
		$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
		$group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');

		switch ($group_id) {
			case '4':
				$options = array(
					'conditions' => array(
						'Parent.parent_id' => $id,
					),
					'contain' => array(
						'Parent',
					),
				);
				break;
			case '3':
				$options = array(
					'conditions' => array(
						'User.parent_id' => $id,
					),
				);
				break;
			
			default:
				$options = array(
					'conditions' => array(
						'User.id' => $id,
					),
				);
				break;
		}

		return $options;
	}

	function _callRoleConditionInfo ( $recordID = false, $type = false ) {
		$value = Configure::read('User.data');
		$id = $this->RmCommon->filterEmptyField($value, 'id');
		$group_id = $this->RmCommon->filterEmptyField($value, 'group_id');
		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		$options = array();
		$company = true;

		if( $group_id == 4 || !empty($admin_rumahku) ) {
			$options = array(
				'conditions' => array(
					'User.id' => $recordID
				),
			);
		} else {
			switch ($type) {
				case 'self':
					$options = array(
						'conditions' => array(
							'User.id' => $id,
						),
					);
					$company = false;
					break;
				
				default:
					$options = array(
						'conditions' => array(
							'User.id' => $recordID
						),
					);
					break;
			}
		}

		$value = $this->controller->User->getData('first', $options, array(
			'company' => $company,
			'parent' => true,
			'status' => false,
			'admin' => true,
			'role' => array(
				'director', 'principle',
			),
		));

		return $value;
	}

	function _callRoleActiveMenu ( $value = false ) {
		$auth_data = Configure::read('User.data');
		$auth_id = $this->RmCommon->filterEmptyField($auth_data, 'id');

		$group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');
		$user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');

		$flag = ($auth_id <> $user_id);

		switch ($group_id) {
			case '4':
                $this->controller->set('active_menu', 'director');
                $this->controller->set('role', 'director');
				break;
			case '3':
				if($flag){
                	$this->controller->set('active_menu', 'principal');
				} else {
					$this->controller->set('active_menu', 'user');
				}
				break;
			
			default:
                $this->controller->set('active_menu', 'user');
				break;
		}
	}

	function apiBeforeSave($data){
		$userClient['Client'] = $this->RmCommon->filterEmptyField($data, 'User');
		$userAgent['Agent'] = $this->RmCommon->filterEmptyField($data, 'ToUser');
		
		$email_message = $this->RmCommon->filterEmptyField($data, 'Message', 'email');
		$emailClient = $this->RmCommon->filterEmptyField($userClient, 'Client', 'email', $email_message);
		$emailAgent = $this->RmCommon->filterEmptyField($userAgent, 'Agent', 'email');
		$mls_id = $this->RmCommon->filterEmptyField($data, 'Property', 'mls_id');


		// CEK EXISTING CLIENT
		$existing_client = $this->controller->User->getData('first', array(
			'conditions' => array(
				'User.email' => trim($emailClient),
			),
		), array(
			'status' => 'semi-active'
		));

		if(empty($existing_client)){

			if(!empty($userClient['Client'])){
				$dataSave['User'] = $this->RmCommon->filterEmptyField($userClient, 'Client');
			}else{
				$name_arr = $this->_getUserFullName($data, 'reverse', 'Message', 'name');
				$first_name = $this->RmCommon->filterEmptyField($name_arr, 'first_name');
				$last_name = $this->RmCommon->filterEmptyField($name_arr, 'last_name');
				$full_name = $this->RmCommon->filterEmptyField($data, 'Message', 'name');
				$phone = $this->RmCommon->filterEmptyField($data, 'Message', 'phone');

				$dataSave['User'] = array(
					'group_id' => '1',
					'first_name' => $first_name,
					'last_name' => $last_name,
					'email' => $emailClient,
					'full_name' => $full_name,
					'UserProfile' => array(
						'phone' => $phone,
					),
				);
			}
			
			$dataSave['User']['active'] = true;
			$dataSave = $this->RmCommon->dataConverter($dataSave, array(
				'unset' => array(
					'User' => array(
						'id',
						'modified',
					),
				),
			));
			$this->controller->User->removeValidator();
			if($this->controller->User->saveAll($dataSave)){
				$data['Message']['from_id'] = $this->controller->User->id;
			}
		}else{
			$user_id = $this->RmCommon->filterEmptyField($existing_client, 'User', 'id');
			$this->controller->User->id = $user_id;
			$this->controller->User->set('active', true);
			$this->controller->User->save();

			$data['Message']['from_id'] = $user_id;
		}
		//

		// get ID agent
		$existing_agent = $this->controller->User->getData('first', array(
			'conditions' => array(
				'User.email' => trim($emailAgent),
				'User.group_id' => '2',
			),
		));

		if(!empty($existing_agent)){
			$data['Message']['to_id'] = $this->RmCommon->filterEmptyField($existing_agent, 'User', 'id');
		}

		// get id properties
		$mls_id = $this->RmProperty->checkMlsID($mls_id, 9, 'C');
		$get_property = $this->controller->User->Property->getData('first', array(
			'conditions' => array(
				'mls_id' => $mls_id,
			),
		), array(
			'status' => false,
			'company' => false,
		));
		$get_property = $this->controller->User->Property->getMergeList($get_property, array(
			'contain' => array(
				'PropertyAddress' => array(
					'contain' => array(
						'Region',
						'City',
						'Subarea',
					),
				),
			),
		));

		if(!empty($get_property)){
			$data['Message']['property_id'] = $this->RmCommon->filterEmptyField($get_property, 'Property', 'id');
			$data = array_merge($data, $get_property);
		}

		// change instance
		$data['Message']['instanace'] = 'rumahku';
		$data['Message']['read'] = FALSE;
		$data = $this->RmCommon->dataConverter($data, array(
			'unset' => array(
				'Message' => array(
					'id',
				),
			),
		));

		return $data;
	}

	function getCounterPrinciple($value){
		$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
		$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');

		$agent_count = $this->controller->User->getAgents( $id, true, 'count' );
		$property_count = $this->controller->User->Property->_callPrinciplePropertyCount($id, 'all');

		if(empty($property_count)){
			$property_count = 0;
		}

		$value['Property']['cnt'] = $property_count;
		$value['Agent']['cnt'] = $agent_count;
		$value['Admin']['cnt'] = $this->controller->User->_callUserCount($id, 5);
		$value['EbrosurCount'] = $this->controller->User->UserCompanyEbrochure->getData('count', array(
			'conditions' => array(
				'User.parent_id' => $id,
			),
			'contain' => array(
				'User',
			),
		), array(
			'company' => false,
		));

		$value['divisionCount'] = $this->controller->User->Group->getDivisionCompany(array(
			'userID' => $id,
			'slug' => 'principal',
			'type' => 'count',
		));

		$value['UserCount'] = $this->controller->User->getUserList( 'count', array(
			'slug' => 'principal',
			'userID' => $id,
		));

		$value['ClientCount'] = $this->userClient(array(
			'principle_id' => $id,
		));

		return $value;
	}

	function beforeSaveActived( $data = false, $value = false){

		if( !empty($data['UserActivedAgent']) ){
			// set data agent decline yang ingin di non aktifkan
			// $userActivedAgentId = Common::hashEmptyField($data, 'UserActivedAgent.id');
			// $userActivedAgentId = array_shift($userActivedAgentId);

			$userActivedAgentId = Common::hashEmptyField($value, 'User.id', 0);
			$data['UserActivedAgent']['agent_decilne_id'] = $userActivedAgentId;
			// 

			// set data agent assign yang ingin memberikan propertinya untuk agent tersebut
			$agent_email = Common::hashEmptyField($data, 'UserActivedAgent.agent_email');
			$agent = $this->controller->User->getData('first', array(
				'conditions' => array(
					'User.group_id' => '2',
					'User.email' => trim($agent_email),
					'User.id <>' => $userActivedAgentId,
				),	
			));

			$agent_id = Common::hashEmptyField($agent, 'User.id');
			$data['UserActivedAgent']['agent_assign_id'] = $agent_id;
			// 

			// company_id or parent_id
			$parent_id = Common::hashEmptyField($value, 'User.parent_id', null);
			if($parent_id){
				$data['UserActivedAgent']['parent_id'] = $parent_id;
			}

			// change data properti, client, client_relations
			$data_arr = Configure::read('__Site.dataAgent');

			foreach ($data_arr as $model => $dataModel) {
				$this->controller->loadModel($model);

				$field = Common::hashEmptyField($dataModel, 'field');
				$field_count = Common::hashEmptyField($dataModel, 'field_count');

				$values = $this->controller->{$model}->getData('all', array(
					'conditions' => array(
						sprintf('%s.%s', $model, $field) => $userActivedAgentId,
					),
				));

				if(!empty($values)){
					$data['UserActivedAgent'][$field_count] = count($values);

					foreach ($values as $key => $value) {
						$value_id = Common::hashEmptyField($value, sprintf('%s.id', $model));

						$temp[] = array(
							'UserActivedAgentDetail' => array(
								'type' => $model,
								'document_id' => $value_id,
								'agent_decilne_id' => $userActivedAgentId,
								'agent_assign_id' => $agent_id,
								'ownership' => 'agent',
							),
						);
					}

					if(!empty($temp)){
						$data['UserActivedAgentDetail'] = $temp;
					}
				}
			}

			$data = $this->RmCommon->_callUnset( array(
				'UserActivedAgent' => array(
					'id',
					'colview_default',
				),
			), $data);
		}
		return $data;
	}

	function getInvestment($value, $type = 'count'){
        $data_arr = Configure::read('__Site.dataAgent');
        $user_id = Common::hashEmptyField($value, 'User.id');

        foreach ($data_arr as $model => $dataModel) {
        	$this->controller->loadModel($model);

        	$field = Common::hashEmptyField($dataModel, 'field');
			$field_count = Common::hashEmptyField($dataModel, 'field_count');

			$values = $this->controller->{$model}->getData( $type, array(
				'conditions' => array(
					sprintf('%s.%s', $model, $field) => $user_id,
				),
			));

			$value['User'][$field_count] = $values;
        }

        return $value;
    }

    function getThemeConfig($theme = false){
    	if(!empty($theme)){
    		$this->controller->loadModel('Theme');
    		$theme_id = Common::hashEmptyField($theme, 'Theme.id');
    		$patch_name = 'home';
    		$principle_id = Configure::read('Principle.id');

    		$values = $this->controller->Theme->ThemeConfig->getData('all', array(
    			'conditions' => array(
    				'ThemeConfig.theme_id' => $theme_id,
    				'ThemeConfig.patch_name' => $patch_name,
    			),
    		));

    		if(!empty($values)){
    			foreach ($values as $key => $value) {
    				$slug = Common::hashEmptyField($value, 'ThemeConfig.slug'); 
    				$val = Common::hashEmptyField($value, 'ThemeConfig.value'); 
    				
    				$theme['UserCompanySetting'][$slug] = !empty($theme['UserCompanySetting'][$slug]) ? $theme['UserCompanySetting'][$slug] : $val;
    			}

    			$theme['ThemeConfig'] = $values;

    		}
    	}
    	return $theme;
    }

	function _callBeforeViewListUser ( $options = array() ) {
		$options =  $this->controller->User->_callRefineParams($this->controller->params, array_merge_recursive(array(
			'order' => array(
				'User.created' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'group' => array(
				'User.id',
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		), $options));
		$this->RmCommon->_callRefineParams($this->controller->params);

		$this->controller->paginate = $this->controller->User->getData('paginate', $options, array(
			'status' => 'semi-active',
			'company' => true,
			'admin' => true,
		));
		$values = $this->controller->paginate('User');

		if( !empty($values) ){
			foreach( $values as $key => &$value ) {
				$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$value = $this->controller->User->getMergeList($value, array(
					'contain' => array(
						'UserProfile',
						'UserConfig',
						'Group',
						'UserCompany',
						'UserParent' => array(
							'uses' => 'User',
							'primaryKey' => 'parent_id',
							'foreignKey' => 'id',
							'contain' => array(
								'UserCompany',
							),
							'elements' => array(
								'status' => 'all',
							),
						),
					),
				));

				$value = $this->controller->User->UserRemoveAgent->getMerge( $value, $id );
				$value = $this->_callGetLogView($id, $value);

    			$value['Property']['cnt'] = $this->controller->User->Property->getData('count', array(
    				'conditions' => array(
    					'Property.user_id' => $id,
					),
				), array(
    				'company' => false,
				));
    			$value['Property']['cnt_sold'] = $this->controller->User->Property->getData('count', array(
    				'conditions' => array(
    					'Property.user_id' => $id,
    					'Property.sold' => 1,
					),
				), array(
    				'company' => false,
				));

				$parent_id = $this->controller->parent_id;

    			$options = $this->controller->User->UserClient->_callRefineParams(false, array(
					'conditions' => array(
						'UserClient.company_id' => $parent_id,
						'UserClient.agent_id' => $id,
						'UserClient.status' => 1,
					),
					'contain' => array(
						'User'
					),
					'order' => array(
						'UserClient.created' => 'DESC',
					),
				));
    			$user_options = array_merge($options, $this->controller->User->getData('paginate', $options, array(
					'status' => 'all',
				)));
				$value['User']['user_client_count'] = $this->controller->User->UserClient->getData('count', $user_options);

				$value = $this->controller->User->UserCompanyEbrochure->getMerge( $value, $id, 'first' );

				if($this->controller->Rest->isActive()){
					$value = $this->controller->User->advanceDataUser( $value, $id);
					$value = $this->controller->User->getMerge( $value, $parent_id, false, 'Parent' );
				}

				$values[$key] = $value;
			}
		}

		$this->RmCommon->_callDataForAPI($values, 'manual');

		return $values;
	}

	function getUser($user_id  =false, $type = 'first'){
		$params = $this->controller->params->params;
		$config_user_id = Configure::read('Principle.id');

		$user_id = !empty($user_id) ? $user_id : Common::hashEmptyField($params, 'named.user_id', $config_user_id);

		$value = $this->controller->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id,
			),
		), array(
			'status' => 'semi-active',
		));

		if($type == 'first'){
			return $this->controller->User->getMergeList($value, array(
				'contain' => array(
					'Group',
				),
			));
		} else {
			$group_id = Common::hashEmptyField($value, 'User.group_id');

			if(!in_array($group_id, array(3, 4))){
				return Common::hashEmptyField($value, 'User.parent_id');
			}
			return $user_id;
		}
	}

	function getUrlBack($user = false){
		$params = $this->controller->params->params;
		$named_user_id = Common::hashEmptyField($params, 'named.user_id');

		$auth_data = Configure::read('User.data');
		$auth_id = Common::hashEmptyField($auth_data, 'id');

		$user_id = Common::hashEmptyField($user, 'User.id');

		if(!empty($user) && ($user_id <> $auth_id) && !empty($named_user_id) ){
			$params_id = !empty($named_user_id) ? $named_user_id : $user_id;

			$urlBack = array(
				'controller' => 'users',
				'action' => 'user_info',
				$params_id,
				'admin' => true,
			);
		} else {
			$urlBack = array(
				'controller' => 'users',
				'action' => 'user_info',
				'admin' => true,
			);
		}

		return $urlBack;
	} 

	function getActive($groupName = false, $slug = 'user', $id = false){
		$active_menu = $groupName;
		$tab = true;
		$group_id = Configure::read('User.data.group_id');

		if(!empty($id)){
			$active_menu = 'user';
			$tab = true;
		} else {
			if($group_id > 20){
				$active_menu = $slug;
			} else {
				switch ($groupName) {
					case 'principal':
						
						if($group_id == 3){
							$active_menu = $slug;
							$tab = false;
						}

						break;
					
					case 'director':
						if($group_id == 4){
							$active_menu = $slug;
							$tab = false;
						}
						break;
					default:
						switch ($group_id) {
							case '3':
								$active_menu = 'principal';
								$tab = false;
								break;

							case '4':
								$active_menu = 'director';
								$tab = false;
								break;
							default:
								if( $this->RmCommon->_isCompanyAdmin() ) {
									$active_menu = 'principal';
									$tab = false;
								} else {
									$active_menu = $slug;
								}
								break;
						}
						break;
				}
			}
		}

		$this->controller->set('tab', $tab);

		return $active_menu;
	}

/*	====================================================================================
	name	: getLocation
	desc	: get location data (region + city) based on given city slug (optional)
	params	: 
		String	$citySlug	optional, when empty ip address will be used instead
======================================================================================*/

	public function getLocation($params = array(), $options = array()){
		$useDefault		= Hash::get($options, 'use_default', false);
		$marketTrend	= Hash::get($options, 'market_trend', false);

		$region		= false;
		$city		= false;
		$subarea	= false;
		$location	= array();

		if(array_intersect(array_keys($params), array('region', 'city', 'subarea'))){
			$region		= Hash::get($params, 'region', $region);
			$city		= Hash::get($params, 'city', $city);
			$subarea	= Hash::get($params, 'subarea', $subarea);
		}
		else if($useDefault){
			$companyData = Configure::read('Config.Company.data');

			if($marketTrend){
				$region		= Hash::get($companyData, 'UserCompanyConfig.mt_region_id');
				$city		= Hash::get($companyData, 'UserCompanyConfig.mt_city_id');
				$subarea	= Hash::get($companyData, 'UserCompanyConfig.mt_subarea_id');
			}

			if(empty($marketTrend) || $marketTrend && empty($region) && empty($city) && empty($subarea)){
				$region		= Hash::get($companyData, 'UserCompany.region_id');
				$city		= Hash::get($companyData, 'UserCompany.city_id');
				$subarea	= Hash::get($companyData, 'UserCompany.subarea_id');
			}
		}

		$modelName	= false;
		$conditions	= array();

	//	b:build conditions ====================================================================

		if(!empty($region)){
			$modelName	= 'Region';
			$fieldName	= is_numeric($region) ? 'id' : 'slug';
			$contain	= array();
			$conditions	= array_merge($conditions, array(
				sprintf('%s.%s', $modelName, $fieldName) => $region, 
			));
		}

		if(!empty($city)){
			$modelName	= 'City';
			$fieldName	= is_numeric($city) ? 'id' : 'slug';
			$contain	= array('Region');
			$conditions	= array_merge($conditions, array(
				sprintf('%s.%s', $modelName, $fieldName) => $city, 
			));
		}

		if(!empty($subarea)){
			$modelName	= 'Subarea';
			$fieldName	= is_numeric($subarea) ? 'id' : 'slug';
			$contain	= array('Region', 'City');
			$conditions	= array_merge($conditions, array(
				sprintf('%s.%s', $modelName, $fieldName) => $subarea, 
			));
		}

	//	e:build conditions ====================================================================

		if($modelName){
			$location = $this->controller->User->UserProfile->$modelName->getData('first', array(
				'contain'		=> $contain, 
				'conditions'	=> $conditions, 
			));

			Configure::write('User.location', $location);
		}

		return $location;
	}

	function QuickStat(){
		$date = date('Y-m-d');
		// get agent
		$User = $this->controller->User;

		// naik via FTP
		$options = $User->UserCompanyConfig->_callRefineParams(array(
			'named' => array(
				// 'status' => 'active',
			),
		), array(
			'fields' => array(
				'UserCompanyConfig.user_id', 'UserCompanyConfig.user_id' 
			),
		));
		$company_list_active = $User->UserCompanyConfig->getData('list', $options);
		$agent = $User->getData('count', array(
			'conditions' => array(
				'User.deleted' => 0,
				'User.group_id' => '2',
				'User.parent_id <>' => false,
				'User.parent_id' => $company_list_active,
			),
		), array(
			'status' => false,
		));
		// 

		// get principle
		$principle = $User->UserCompanyConfig->getData('count', array(
			'conditions' => array(
				'UserCompanyConfig.end_date >=' => $date,
			),
		));

		// get listing
		$property = $User->Property->getData('count', false, array(
			'mine' => false,
			'company' => false,
		));

		$agent  =$this->generatePlus($agent);
		$principle  =$this->generatePlus($principle);
		$property  =$this->generatePlus($property);
		
		$this->controller->set(array(
			'user_count' => $agent,
			'principle_count' => $principle,
			'property_count' => $property,
		));
	}

	function generatePlus($value = false){
		if($value){
			$strlen = strlen($value);
			$strlen = $strlen - 1;


			if($strlen > 0){
				$value = round($value, sprintf('-%s', $strlen), PHP_ROUND_HALF_DOWN);
			}
		}
		return $value;
	}
	
	function _callBeforeViewClient () {
		$data = $this->controller->request->data;
		
		$clientTypes = $this->controller->User->ClientType->find('list');
		$clientMasterReference = $this->controller->User->UserClient->UserClientMasterReference->getData('list');

	//	pake location picker, udah ga perlu ini lagi
	//	$this->RmCommon->_callRequestSubarea('UserClient');

	//	if (!empty($data['UserClient']['additional_city_id'])) {
	//		$subareas = $this->controller->User->UserProfile->Subarea->getData('list', array(
	//			'conditions' => array(
	//				'Subarea.city_id' => $data['UserClient']['additional_city_id'],
	//			),
	//		));

	//		$this->controller->set(array(
	//			'subareas_additional' => $subareas,
	//		));
	//	}

		$subareaID				= Common::hashEmptyField($data, 'UserClient.subarea_id');
		$additionalSubareaID	= Common::hashEmptyField($data, 'UserClient.additional_subarea_id');

		$locationID	= array_filter(array($subareaID, $additionalSubareaID));
		$locationID	= array_unique($locationID);

		if($locationID){
			$this->controller->loadModel('ViewLocation');

			$locations = $this->controller->ViewLocation->getData('all', array(
				'conditions' => array('ViewLocation.subarea_id' => $locationID),
			));

			$locations = array_filter(array(
				'location'				=> Hash::extract($locations, sprintf('{n}.ViewLocation[subarea_id=%s]', $subareaID)), 
				'additional_location'	=> Hash::extract($locations, sprintf('{n}.ViewLocation[subarea_id=%s]', $additionalSubareaID)), 
			));

			if($locations){
				foreach($locations as $locationType => $location){
					$location = array_shift($location);

					if($location){
						$regionName		= Common::hashEmptyField($location, 'region_name');
						$cityName		= Common::hashEmptyField($location, 'city_name');
						$subareaID		= Common::hashEmptyField($location, 'subarea_id');
						$subareaName	= Common::hashEmptyField($location, 'subarea_name');
						$locationName	= array_filter(array($subareaName, $cityName, $regionName));
						$locationName	= implode(', ', $locationName);

						$fieldName = sprintf('UserClient.%s_name', $locationType);

						$this->controller->request->data = Hash::insert($this->controller->request->data,$fieldName, $locationName);
					}
				}
			}
		}

		$this->controller->set(array(
			'clientTypes' => $clientTypes,
			'clientMasterReference' => $clientMasterReference,
		));
	}
	
	function _callBeforeViewListClient () {
		$clientTypes = $this->controller->User->ClientType->find('list');
		$list_reference = $this->controller->User->UserClient->UserClientMasterReference->getData('list');
		$list_reference = $list_reference + array('NULL' => 'Unknown');
		
		$this->controller->set(array(
			'clientTypes' => $clientTypes,
			'list_reference' => $list_reference,
		));
	}

	function _callLastActivity ( $value, $options = array() ) {
		$client_id = Common::hashEmptyField($options, 'client_id');
		$agent_id = Common::hashEmptyField($options, 'agent_id');
		$crm_project_id = Common::hashEmptyField($options, 'crm_project_id');

		$model_options = array(
			'conditions' => array(
				'CrmProjectActivity.step <>' => 'change_status',
			), 
		);

		if( !empty($client_id) ) {
			$model_options['conditions']['CrmProjectActivity.client_id'] = $client_id;
		}
		if( !empty($agent_id) ) {
			$model_options['conditions']['CrmProject.user_id'] = $agent_id;
			$model_options['contain'][] = 'CrmProject';
		}
		if( !empty($crm_project_id) ) {
			$model_options['conditions']['CrmProjectActivity.crm_project_id'] = $crm_project_id;
		}

		$lastActivity = $this->controller->User->CrmProjectActivity->getData('first', $model_options);

		if( !empty($lastActivity) ) {					
			$crm_project_actvity_id = Common::hashEmptyField($lastActivity, 'CrmProjectActivity.id');
			$lastActivity = $this->controller->User->CrmProjectActivity->CrmProjectActivityAttributeOption->getMerge($lastActivity, $crm_project_actvity_id);

			$value = Hash::insert($value, 'LastActivity', Common::hashEmptyField($lastActivity, 'CrmProjectActivity'));
			$value = Hash::insert($value, 'LastActivity.note', Common::_callNoteActivity($lastActivity));
	 	}

	 	return $value;
	}

	function callSosmedClientBeforeSave( $value = null, $id = null ) {
		$data = $this->controller->request->data;
		$principle_id = Configure::read('Principle.id');

		if ( !empty($data) ) {
			$name = Hash::get($data, 'UserClientSosmedReference.name');
			$url = Hash::get($data, 'UserClientSosmedReference.url');
			$name = trim($name);
			$url = trim($url);

			$wrappedURL = $url ? $this->RmCommon->wrapWithHttpLink($url) : NULL;

		 	$data = Hash::insert($data, 'UserClientSosmedReference.principle_id', $principle_id);
		 	$data = Hash::insert($data, 'UserClientSosmedReference.name', $name);
		 	$data = Hash::insert($data, 'UserClientSosmedReference.url', $wrappedURL);

		 	if( !empty($id) ) {
			 	$data = Hash::insert($data, 'UserClientSosmedReference.id', $id);
			}

            $result = $this->controller->User->UserClient->UserClientSosmedReference->doSave($data, $id);
			$this->RmCommon->setProcessParams($result, array(
				'action' => 'sosmed_reference',
				'admin' => true,
			));
		} else if( !empty($value) ) {
			$data = $value;
		} else {
			$data['UserClientSosmedReference']['active'] = true;
		}
		
		$this->controller->request->data = $data;
	}

	function callContentLable( $value = array() ) {
		// value default
		$contentValue = $value;
		$listContentLabel = Configure::read('__Site.Global.Variable.ListContentLabel');
		$idLabelSosmed = Common::hashEmptyField($listContentLabel, 'sosmed');

		if (!empty($value)) {
			$client_ref_id = Common::hashEmptyField($value, 'UserClient.client_ref_id');

			if ($client_ref_id == $idLabelSosmed) {

				$id = Common::hashEmptyField($value, 'UserClient.client_ref_sosmed_id');
				$valueModel	= $this->controller->User->UserClient->UserClientSosmedReference->getData('first', array(
					'conditions' => array(
						'UserClientSosmedReference.id' => $id, 
					), 
				));

				$contentLable = Common::hashEmptyField($valueModel, 'UserClientSosmedReference.name');
				$contentValue = Hash::insert($value, 'UserClient.content_sosmed', $contentLable);

				$this->controller->set(array(
					'value_added' => $contentValue,
				));
			}

		}
	}

	function _callCheckParent ( $parent_id = null ) {
		$parent_id = Common::hashEmptyField($this->controller->params->params, 'named.parent_id', $parent_id);
		$parent_id = Common::hashEmptyField($this->controller->params->params, 'named.user_id', $parent_id);

		if( !empty($parent_id) ) {
			$value = $this->controller->User->getData('first', array(
				'conditions' => array(
					'User.id' => $parent_id,
				),
			), array(
				'role' => array(
					'director',
					'principle',
				),
			));

			if( empty($value) ) {
				$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
			} else {
				$this->controller->set('current', $value);
				
				return $value;
			}
		} else {
			return false;
		}
	}

	function apiUserBeforeSave($data = false){
		if($data){
			$code  = Common::hashEmptyField($data, 'User.code');
			// check exist
			$bankUser = $this->controller->BankUser->getData('first', array(
				'conditions' => array(
					'BankUser.code' => trim($code),
				),
			), array(
				'status' => 'all',
			));

			$data = Common::_callUnset($data, array(
				'UserProfile' => array(
					'id',
					'user_id',
				),
			));

			if(empty($bankUser)){
				$data = Common::_callUnset($data, array(
					'User' => array(
						'id',
					),
					'UserProfile' => array(
						'id',
						'user_id',
					),
				));

			} else {
				$bankUserId = Common::hashEmptyField($bankUser, 'BankUser.id');
				
				$bankUser = $this->controller->BankUser->BankUserProfile->getMerge($bankUser, $bankUserId);
				$bankUserProfileId = Common::hashEmptyField($bankUser, 'BankUserProfile.id');

				$data['User']['id'] = $bankUserId;
			}

			$temp = $data;
			unset($data);

			$data['BankUser'] = Common::hashEmptyField($temp, 'User');
			$data['BankUserProfile'] = Common::hashEmptyField($temp, 'UserProfile');
		}
		return $data;
	}

	public function callBeroreSavePersonalPage($record = array(), $options = array()){
		$record		= (array) $record;
		$options	= (array) $options;
		$data		= $this->controller->request->data;

		if($data){
			$logoPath	= Configure::read('__Site.logo_photo_folder');
			$userID		= Common::hashEmptyField($record, 'User.id', null);
			$configID	= Common::hashEmptyField($record, 'UserConfig.id', null);

			$data = Hash::insert($data, 'UserConfig.id', $configID);
			$data = Hash::insert($data, 'UserConfig.user_id', $userID);

			$data = $this->controller->RmImage->_uploadPhoto($data, 'UserConfig', 'logo', $logoPath);
			$data = $this->controller->RmUser->_callUserBeforeSave($data, $record);

			$date	= Common::hashEmptyField($data, 'UserConfig.date');
			$domain	= Common::hashEmptyField($data, 'UserConfig.personal_web_url');

			if($date){
				$date		= $this->controller->RmCommon->_callConvertDateRange(array(), $date);
				$liveDate	= Common::hashEmptyField($date, 'date_from');
				$endDate	= Common::hashEmptyField($date, 'date_to');

				$data = Hash::insert($data, 'UserConfig.live_date', $liveDate);
				$data = Hash::insert($data, 'UserConfig.end_date', $endDate);
			}

			if($domain){
				$useProtocol = Common::strposArray($domain, array('http://', 'https://'));

				if($useProtocol === false){
					$domain	= sprintf('http://%s', $domain);
					$data	= Hash::insert($data, 'UserConfig.personal_web_url', $domain);
				}
			}

			$saveFlag	= $this->controller->User->UserConfig->doSave($record, $data, $userID);
			$result		= array(
				'status'	=> $saveFlag ? 'success' : 'error', 
				'msg'		=> __('%s menyimpan pengaturan', $saveFlag ? 'Berhasil' : 'Gagal'), 
				'data'		=> $data, 
			);

			$newLogo = Common::hashEmptyField($data, 'UserConfig.logo');
			$oldLogo = Common::hashEmptyField($record, 'UserConfig.logo');

			if(($newLogo != $oldLogo) && $saveFlag){
				$this->controller->RmRecycleBin->delete($oldLogo, $logoPath, null, false);
			}

			$this->controller->RmCommon->setProcessParams($result, array(), array('noRedirect' => true));
		}
		else{
			$data		= $record;
			$logo		= Common::hashEmptyField($data, 'UserConfig.logo');
			$liveDate	= Common::hashEmptyField($data, 'UserConfig.live_date');
			$endDate	= Common::hashEmptyField($data, 'UserConfig.end_date');

		//	append data
			$data = Hash::insert($data, 'UserConfig.logo_hide', $logo);

			if($liveDate && $endDate){
				$liveDate	= date('d/m/Y', strtotime($liveDate));
				$endDate	= date('d/m/Y', strtotime($endDate));

				$data = Hash::insert($data, 'UserConfig.date', sprintf('%s - %s', $liveDate, $endDate));
			}
		}

		$this->controller->request->data = $data;

		$themes = $this->controller->User->UserConfig->Theme->getData('list', array(
			'order' => array('Theme.name' => 'asc'), 
		), array(
			'owner_type' => 'agent', 
		));

		$packages = $this->controller->User->UserConfig->MembershipPackage->getData('list', array(
			'order' => array('MembershipPackage.name' => 'asc'), 
		), array(
			'package_type' => 'agent', 
		));

		$this->controller->set(compact('themes', 'packages'));
	}

	public function facebookConnect(){
		App::import('Vendor', 'Facebook/facebook');

		require_once(APP . 'Vendor' . DS  . 'Facebook' . DS . 'autoload.php');

		$globalFacebook	= Configure::read('__Site.facebook');
		$clientID		= Common::hashEmptyField($globalFacebook, 'client_id');
		$clientSecret	= Common::hashEmptyField($globalFacebook, 'client_secret');
		$clientVersion	= Common::hashEmptyField($globalFacebook, 'client_version');

		$this->Facebook = new Facebook\Facebook(array(
			'app_id'				=> $clientID,
			'app_secret'			=> $clientSecret,
			'default_graph_version'	=> $clientVersion, 
		));

		return $this->Facebook;
	}

	public function getFacebookToken(){
		$code			= Common::hashEmptyField($this->controller->params->query, 'code');
		$ipAddress		= $this->controller->RequestHandler->getClientIP();
		$cacheName		= sprintf('User.facebook.%s', Inflector::underscore($ipAddress));
		$cacheConfig	= 'default';
		$cacheData		= Cache::read($cacheName, $cacheConfig);
		$expired		= Common::hashEmptyField($cacheData, 'expired', 0);

		$currentDate	= date('Y-m-d H:i:s');
		$isExpired		= strtotime($currentDate) >= $expired;

		if(empty($cacheData) || $isExpired){
			if(!$this->Facebook){
				$this->Facebook = $this->facebookConnect();
			}

			$helper = $this->Facebook->getRedirectLoginHelper();

			try{
				$accessToken = $helper->getAccessToken();
			}
			catch(Facebook\Exceptions\FacebookResponseException $e){
			//	when graph returns an error
				$this->error = __('Graph returned an error: %s', $e->getMessage());
				return null;
			}
			catch(Facebook\Exceptions\FacebookSDKException $e){
			//	when validation fails or other local issues
				$this->error = __('Facebook SDK returned an error: %s', $e->getMessage());
				return null;
			}

			if(!isset($accessToken)){
				if($helper->getError()){
					$this->error = __('Error : %s [%s]', $helper->getError(), $helper->getErrorCode());

					if($helper->getErrorReason()){
						$this->error = __('%s. Reason : %s', $helper->getErrorReason());
					}
				}
				else{
					$this->error = __('Bad request');
				}

				return null;
			}
			else{
				$oAuth2Client	= $this->Facebook->getOAuth2Client();
				$tokenMetadata	= $oAuth2Client->debugToken($accessToken);

				$expiresAtDate	= $tokenMetadata->getField('expires_at');
				$expiresAtDate	= $expiresAtDate->format('Y-m-d H:i:s');

				$expired		= strtotime($expiresAtDate);
				$expiresIn		= $expired - strtotime($currentDate);
				$expiresIn		= abs($expiresIn);

				$cacheData = array(
					'expires_in'	=> $expiresIn, 
					'expired'		=> $expired, 
					'data'			=> array(
						'access_token' => (string) $accessToken, 
					), 
				);

			//	set cache
				Cache::write($cacheName, $cacheData, $cacheConfig);
			}
		}

		return $cacheData;
	}

	public function getFacebookProfile(){
		if(!$this->Facebook){
			$this->Facebook = $this->facebookConnect();
		}
		
		$tokenData = $this->getFacebookToken();

		if($tokenData){
			$accessToken = Common::hashEmptyField($tokenData, 'data.access_token');

			try{
				$profileData = $this->Facebook->get('/me?fields=id,name,first_name,last_name,email,link', $accessToken);
				$profileData = $profileData->getGraphUser();

			//	populate our social profile
				$profileID			= isset($profileData['id']) ? $profileData['id'] : '';
				$profileName		= isset($profileData['name']) ? $profileData['name'] : '';
				$profileFirstName	= isset($profileData['first_name']) ? $profileData['first_name'] : '';
				$profileLastName	= isset($profileData['last_name']) ? $profileData['last_name'] : '';
				$profileEmail		= isset($profileData['email']) ? $profileData['email'] : '';
				$profileLink		= isset($profileData['link']) ? $profileData['link'] : '';
				$profileURL			= 'https://graph.facebook.com/' . $profileID . '/picture?width=150&height=150';

				$socialProfile['SocialProfile']['social_network_name']	= 'Facebook';
				$socialProfile['SocialProfile']['social_network_id']	= $profileID;
				$socialProfile['SocialProfile']['email']				= $profileEmail;
				$socialProfile['SocialProfile']['display_name']			= $profileName;
				$socialProfile['SocialProfile']['first_name']			= $profileFirstName;
				$socialProfile['SocialProfile']['last_name']			= $profileLastName;
				$socialProfile['SocialProfile']['link']					= $profileLink;
				$socialProfile['SocialProfile']['picture']				= $profileURL;
				$socialProfile['SocialProfile']['created']				= date('Y-m-d h:i:s');
				$socialProfile['SocialProfile']['modified']				= date('Y-m-d h:i:s');

				return $socialProfile;
			}
			catch(Facebook\Exceptions\FacebookResponseException $e){
			//	when graph returns an error
				$this->error = 'Graph returned an error: ' . $e->getMessage();
				return null;
			}
			catch(Facebook\Exceptions\FacebookSDKException $e){
			//	when validation fails or other local issues
				$this->error = 'Facebook SDK returned an error: ' . $e->getMessage();
				return null;
			}
		}
		else{
			return null;
		}
	}

	public function getUserHistory($userID = null, $options = array()){
		$userID		= (int) $userID ?: Common::config('User.id');
		$histories	= array();

		if($userID){
			$options	= (array) $options;
			$duration	= Common::hashEmptyField($options, 'duration', true, array('type' => 'isset'));

			$this->controller->loadModel('UserHistory');
			$this->controller->paginate = $this->controller->UserHistory->getData('paginate', array(
				'limit'			=> 10, 
				'conditions'	=> array(
					'UserHistory.user_id'	=> $userID, 
					'not'					=> array(
						'UserHistory.type' => 'update', 
					), 
				), 
			));

			$histories = $this->controller->paginate('UserHistory');
			$histories = $this->controller->User->getMergeList($histories, array(
				'contain' => array(
					'UserHistory' => array(
						'contain' => array(
							'Group', 
							'Principle' => array(
								'contain' => array(
									'UserProfile', 
									'UserCompany', 
								), 
							), 
						), 
					), 
				), 
			));

			if($duration){
				$today = date('Y-m-d H:i:s');

				foreach($histories as $key => &$history){
					$historyID			= Common::hashEmptyField($history, 'UserHistory.id');
					$historyType		= Common::hashEmptyField($history, 'UserHistory.type');
					$currentDatetime	= Common::hashEmptyField($history, 'UserHistory.created');

					if(empty($key)){
						$recentDatetime = $this->controller->UserHistory->field('UserHistory.created', array(
							'UserHistory.id >'		=> $historyID, 
							'UserHistory.user_id'	=> $userID, 
							'UserHistory.type !='	=> 'update', 
						));

						$recentDatetime = $recentDatetime ?: $today;
					}
					else{
						$recentHistory	= Common::hashEmptyField($histories, $key - 1);	
						$recentDatetime	= Common::hashEmptyField($recentHistory, 'UserHistory.created');
					}

					if($historyType != 'update' && $currentDatetime && $recentDatetime){
						$duration = Common::getDateInterval($currentDatetime, $recentDatetime, array(
							'components' => array('y', 'm', 'd'), 
						));
					}
					else{
						$duration = '';
					}

					$history = Hash::insert($history, 'UserHistory.duration', $duration);
					$history = Hash::insert($history, 'UserHistory.duration_from', $currentDatetime);
					$history = Hash::insert($history, 'UserHistory.duration_to', $recentDatetime);
				}
			}
		}

		return $histories;
	}

	public function setPermission($groupID = null){
		$permissions = array();

		if(empty($groupID) && $this->Auth->loggedIn()){
			$groupID = $this->Auth->user('group_id');
		}

		if($groupID){
			$cacheConfig	= 'permission';
			$cacheName		= sprintf('Permission.%s', $groupID);
			$permissions	= Cache::read($cacheName, $cacheConfig);

			if(empty($permissions)){
			//	component
				$aclComponent = $this->controller->Acl;

			//	get aro
				$aro = $aclComponent->Aro->find('first', array(
					'recursive'		=> -1, 
					'conditions'	=> array(
						'Aro.foreign_key' => $groupID, 
					), 
				));

				$aroID = Common::hashEmptyField($aro, 'Aro.id', 0);

			//	get all permissions list
				$aclComponent->Aco->unbindModel(array(
					'hasAndBelongsToMany' => array('Aro'), 
				));

				$aclComponent->Aco->bindModel(array(
					'hasOne' => array(
						'Permission' => array(
							'tableName'		=> 'aros_acos', 
							'alias'			=> 'Permission', 
							'conditions'	=> array(
								'Permission.aco_id = Aco.id', 
								'Permission.aro_id' => $aroID, 
							), 
						), 
					)
				));

				$acos = $aclComponent->Aco->find('all', array(
					'contain'	=> array('Permission'), 
					'order'		=> array(
						'Aco.lft' => 'ASC', 
					), 
				));

				$isAdmin		= $this->RmCommon->_isAdmin($groupID);
				// $defaultAllowed	= $isAdmin ? 0 : -1;
				$defaultAllowed	= 0;

				foreach($acos as $key => $aco){
					$acoID		= Hash::get($aco, 'Aco.id');
					$acoAlias	= Hash::get($aco, 'Aco.alias');
					$parentID	= Hash::get($aco, 'Aco.parent_id');
					$allowed	= Hash::get($aco, 'Permission._create', $defaultAllowed); // ambil 1 aja, isi nya sama semua

					if($acoID){
						if($parentID && isset($permissions[$parentID])){
						//	note : 
						//	1 allowed, 0 inherit, -1 deny
						//	kalo 0, liat parent, kalo parent 1, otomatis ikut 1

							$parentPath		= $permissions[$parentID]['path'];
							$parentAllowed	= $permissions[$parentID]['allowed'];

							if($allowed == 0){
								$allowed = $parentAllowed;
							}

							$permissions[$acoID] = array(
								'path'		=> $parentPath . '/' . $acoAlias, 
								'allowed'	=> $allowed, 
							);
						}
						else{
							$permissions[$acoID] = array(
								'path'		=> $acoAlias, 
								'allowed'	=> $allowed, 
							);
						}
					}
				}

			//	debug($permissions);exit;

				$permissions = Hash::extract($permissions, '{n}[allowed=1].path');
			//	debug($permissions);exit;

			//	write cache
				Cache::write($cacheName, $permissions, $cacheConfig);
			//	$queryLog = $this->controller->User->queryLog(true);
			}

		//	write result as config
			Configure::write(sprintf('Permission.%s', $groupID), $permissions);
		}

	//	debug($permissions);exit;

		return $permissions;
	}
}
?>