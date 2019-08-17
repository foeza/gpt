<?php
class UserCompany extends AppModel {
	var $name = 'UserCompany';
	var $displayField = 'name';

	var $validate = array(
		'user_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
			),
			'numeric' => array(
				'rule' => array('numeric'),
			),
		),
		'logo' => array(
			'imageupload' => array(
	            'rule' => array('extension',array('jpeg','jpg','png','gif')),
	            'required' => false,
	            'allowEmpty' => false,
	            'message' => 'Logo harap diisi dan berekstensi (jpeg, jpg, png, gif)'
	        ),
		),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama Perusahaan harap diisi',
			),
		),
		'address' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Alamat Perusahaan harap diisi',
			),
		),
		'region_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih provinsi',
			),
		),
		'city_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih kota',
			),
		),
		'subarea_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih area',
			),
		),
		'zip' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan kode pos',
			),
		),
		'contact_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama kontak',
			),
		),
		'contact_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan email kontak',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
			),
		),
		'phone' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'No. Telepon harap diisi',
			),
			'notMatch' => array(
				'rule' => array('validatePhoneNumber'),
				'message' => 'Format No. Telepon e.g. +6281234567 or 0812345678',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'No. Telepon minimal 6 karakter',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 14),
				'message' => 'No. Telepon maksimal 14 digit',
			),
		),
		'phone_2' => array(
			'notMatch' => array(
				'rule' => array('validatePhoneNumber'),
				'allowEmpty'=> true,
				'message' => 'Format No. Telepon 2 e.g. +6281234567 or 0812345678',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'allowEmpty'=> true,
				'message' => 'No. Telepon 2 minimal 6 karakter',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 14),
				'allowEmpty'=> true,
				'message' => 'No. Telepon 2 maksimal 14 digit',
			),
		),
		'fax' => array(
			'notMatch' => array(
				'rule' => array('validatePhoneNumber'),
				'allowEmpty'=> true,
				'message' => 'Format No. fax e.g. +6281234567 or 0812345678',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'allowEmpty'=> true,
				'message' => 'Minimal 6 karakter',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 14),
				'allowEmpty'=> true,
				'message' => 'Maksimal 14 digit',
			),
		),
	);

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
		),
		'Subarea' => array(
			'className' => 'Subarea',
			'foreignKey' => 'subarea_id',
		),
	);

	var $hasMany = array(
		'Log' => array(
			'className' => 'Log',
			'foreignKey' => 'parent_id',
		),
	);

	function validatePhoneNumber($data) {
		$phoneNumber = false;
		if( !empty($data['phone']) ) {
			$phoneNumber = $data['phone'];
		} else if( !empty($data['phone_2']) ) {
			$phoneNumber = $data['phone_2'];
		} else if( !empty($data['fax']) ) {
			$phoneNumber = $data['fax'];
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

    function CompanyExist($data, $changes = array()){
    	$usercompany['UserCompany']  = $this->filterEmptyField($data, 'User', 'UserCompany');
    	$user['User']  = $this->filterEmptyField($data, 'User', 'UserParent');
    	$usercompany = $this->callUnset($usercompany, array(
    		'UserCompany' => array(
    			'id',
    		),
    	));
    	if(!empty($usercompany['UserCompany']) && !empty($changes)){
    		$slug = $this->filterEmptyField($usercompany, 'UserCompany', 'slug');

    		$value = $this->getData('first', array(
    			'conditions' => array(
    				'UserCompany.slug' => $slug,
    			),
    		));

    		if(!empty($value)){
    			$id = $this->filterEmptyField($value, 'UserCompany', 'id');
    			$this->id = $id;
    		}else{
    			$this->create();
    		}

    		$this->save($usercompany, false);
    	}

    	return $data;
    }

    function doSave( $user_id, $value, $data, $id = false ) {
		$result = false;

		if(!empty($data['is_api'])){
			$this->removeValidator();
		}

		if ( !empty($data) ) {
			if ( !empty($id) ) {
				$data['UserCompany']['id'] = $id;
			} else {
				$data['UserCompany']['user_id'] = $user_id;
			}

			$data['UserCompany']['name'] 			= !empty($data['UserCompany']['name']) ? trim($data['UserCompany']['name']) : '';
			$data['UserCompany']['address'] 		= !empty($data['UserCompany']['address']) ? trim($data['UserCompany']['address']) : '';
			$data['UserCompany']['zip'] 			= !empty($data['UserCompany']['zip']) ? trim($data['UserCompany']['zip']) : '';
			$data['UserCompany']['contact_name'] 	= !empty($data['UserCompany']['contact_name']) ? trim($data['UserCompany']['contact_name']) : '';
			$data['UserCompany']['contact_email'] 	= !empty($data['UserCompany']['contact_email']) ? trim($data['UserCompany']['contact_email']) : '';
			$data['UserCompany']['phone'] 			= !empty($data['UserCompany']['phone']) ? trim($data['UserCompany']['phone']) : '';
			$data['UserCompany']['fax'] 			= !empty($data['UserCompany']['fax']) ? trim($data['UserCompany']['fax']) : '';
			
			if( $this->saveAll($data) ){
				$result = array(
					'msg' => __('Sukses memperbarui profil perusahaan'),
					'status' => 'success',
					'data' => $data,
				);
			} else {
				$validationErrors = array();

				if(!empty($this->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->validationErrors);
				}

				$result = array(
					'msg' => __('Gagal memperbarui profil perusahaan. Silahkan coba lagi'),
					'status' => 'error',
					'data' => $data,
					'validationErrors' => $validationErrors
				);
			}
		} else if( !empty($value) ) {
			$logoName = !empty($value['UserCompany']['logo'])?$value['UserCompany']['logo']:false;

			$value['UserCompany']['logo_hide'] = $logoName;
			$result['data'] = $value;
		}

		return $result;
	}

	function getData( $find = 'all', $options = array(), $elements = array() ){
        $status = isset($elements['status']) ? $elements['status']:false;
        $admin = isset($elements['admin']) ? $elements['admin']:true;
        $rest = isset($elements['rest']) ? $elements['rest']:true;
        $company = $this->filterEmptyField($elements, 'company');
        $admin_rumahku = Configure::read('User.Admin.Rumahku');

		$default_options = array(
			'conditions'=> array(
				'UserCompany.status' => 1,
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
		);

		if( !empty($company) && ( empty($admin_rumahku) || empty($admin) ) ) {
            $companyData = Configure::read('Config.Company.data');
            $parent_id = Configure::read('Principle.id');
            $group_id = $this->filterEmptyField($companyData, 'User', 'group_id');

            if( $group_id == 4 ) {
				$default_options['conditions']['User.parent_id'] = $parent_id;
				$default_options['contain'][] = 'User';
            }
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
            if(!empty($options['offset'])){
                $default_options['offset'] = $options['offset'];
            }
		}

		if( !empty($status) && in_array('User', $default_options['contain']) ) {
			$default_options['conditions'] = array_merge($default_options['conditions'], $this->User->getData('conditions', false, array(
            	'status' => $status,
        	)));
		}

		if( !empty($rest) ) {
        	$default_options = $this->_callFieldForAPI($find, $default_options);
        }
        
		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
		
        return $result;
	}

	function _callFieldForAPI ( $find, $options ) {
		if( !in_array($find, array( 'list', 'count' )) ) {
			$rest_api = Configure::read('Rest.token');

			if( !empty($rest_api) ) {
				$options['fields'] = array(
				  'UserCompany.id',
				  'UserCompany.user_id',
				  'UserCompany.country_id',
				  'UserCompany.region_id',
				  'UserCompany.city_id',
				  'UserCompany.subarea_id',
				  'UserCompany.slug',
				  'UserCompany.name',
				  'UserCompany.logo',
				  'UserCompany.description',
				  'UserCompany.address',
				  'UserCompany.zip',
				  'UserCompany.phone',
				  'UserCompany.phone_2',
				  'UserCompany.contact_name',
				  'UserCompany.contact_email',
				  'UserCompany.fax',
				  'UserCompany.location',
				  'UserCompany.longitude',
				  'UserCompany.latitude',
				);
			}
		}

		return $options;
	}

	function getMerge ( $data, $user_id = false, $modelName = 'UserCompany' ) {
		if( empty($data[$modelName]) && !empty($user_id) ){
			$usercompany = $this->getData('first', array(
				'conditions' => array(
					'UserCompany.user_id' => $user_id,
				)
			));

			if(!empty($usercompany)){
				$data[$modelName] = $usercompany['UserCompany'];
			}
		}

		return $data;
	}

	function getListParents(){
		$data = $this->getData('list', array(
			'conditions' => array(
				'UserCompany.status' => 1,
			),
		), array(
			'status' => 'active',
		));

		return $data;
	}

	function doSaveLogo( $data, $user_id ) {
        $result = new stdClass();

        if ( !empty($data) ) {
        	if( !empty($data['error']) ){
	  			$result->error = 1;
	  			$result->message = $data['message'];
	  		}else{

	  			$flag_exists = true;
	  			$userCompany = $this->getData('first', array(
					'conditions' => array(
						'UserCompany.user_id' => $user_id
					),
				));

	  			// VALIDATE IF COMPANY DATA IS EXISTS
				if ( !empty($userCompany) ) {
					$userCompanyID = $userCompany['UserCompany']['id'];
					$this->id = $userCompanyID;
				} else {
					$this->create();
					$data['UserCompany']['user_id'] = $user_id;
					$flag_exists = false;
				}
	            
	            $this->set($data);

	            if( !$this->save() ) {
					$result->error = 1;
	  				$result->message = __('Gagal menyimpan logo perusahaan');
	            } else {

	            	if( !$flag_exists ) {
	            		$userCompanyID = $this->id;
	            	}

	            	if(!empty($data['imagePath'])){
			  			$result->thumbnail_url = $data['imagePath'];
		  			}

		  			if(!empty($data['name'])){
			  			$result->name = $data['name'];
		  			}

	            	$result->url = array(
	            		'controller' => 'users',
	            		'action' => 'photo_crop',
	            		'id' => $userCompanyID,
	            		'action_name' => 'logo',
	            		'admin' => false
	            	);
	  			}
	  		}
        }

        return $result;
    }

	function doCroppedPhoto( $user_company_id, $save_path, $data, $caller ) {
		
		$result = false;

		if ( !empty($data) ) {

			$params = $data['UserCompany'];
			$thumbnail = $caller->RmImage->cropPhoto(300, $params['x1'], $params['y1'], $params['x2'], $params['y2'], $params['w'], $params['h'], $params['imagePath'], $params['imagePath'], $save_path, $params['w_img'], $params['h_img']);
			
			if( $thumbnail ) {
				$this->id = $user_company_id;
				$params['logo'] = $thumbnail;
				
				if($this->save($params)) {
					$result = array(
						'msg' => __('Sukses memperbarui logo perusahaan.'),
						'status' => 'success',
					);
				} else {
					$result = array(
						'msg' => __('Gagal memperbarui logo perusahaan. Silahkan coba lagi'),
						'status' => 'error',
					);
				}
			} else {
				$result = array(
					'msg' => __('Gagal memperbarui logo perusahaan. Silahkan coba lagi'),
					'status' => 'error',
				);
			}
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
		$name = $this->filterEmptyField($data, 'named', 'name', false, array(
        	'addslashes' => true,
    	));
		$region_id = $this->filterEmptyField($data, 'named', 'region_id', false, array(
        	'addslashes' => true,
    	));
		$type = $this->filterEmptyField($data, 'named', 'type', false, array(
        	'addslashes' => true,
    	));
		$group_type = $this->filterEmptyField($data, 'named', 'group_type', false, array(
        	'addslashes' => true,
    	));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));
        $location = $this->filterEmptyField($data, 'named', 'location', false, array(
            'addslashes' => true,
        ));

        if( empty($type) ) {
        	$type = $group_type;
        }

		if( !empty($keyword) ) {
			$default_options['conditions']['OR'] = array(
				'UserCompany.name LIKE' => '%'.$keyword.'%',
			);

			$users = $this->User->getData('list', array(
                'conditions' => array(
                    'OR' => array(
                        'User.first_name LIKE' => '%'.$keyword.'%',
                        'User.last_name LIKE' => '%'.$keyword.'%',
                        'User.email LIKE' => '%'.$keyword.'%',
                    ),
                    'group_id' => array(3),
                ),
                'fields' => array(
                    'User.id'
                ),
            ));
            $cities = $this->City->getByKeyword($keyword);

            if( !empty($users) ) {
                $default_options['conditions']['OR']['UserCompany.user_id'] = $users;
            }
            if( !empty($cities) ) {
                $default_options['conditions']['OR']['UserCompany.city_id'] = $cities;
            }
		}
		if( !empty($name) ) {
            $default_options['conditions']['UserCompany.name LIKE'] = '%'.$name.'%';
		}
		if( !empty($location) ) {
            $default_options['conditions'][0]['OR']['Region.name LIKE'] = '%'.$location.'%';
            $default_options['conditions'][0]['OR']['City.name LIKE'] = '%'.$location.'%';
            $default_options['conditions'][0]['OR']['Subarea.name LIKE'] = '%'.$location.'%';

            $default_options['contain'][] = 'Region';
            $default_options['contain'][] = 'City';
            $default_options['contain'][] = 'Subarea';
		}
		if( !empty($region_id) ) {
            $default_options['conditions']['UserCompany.region_id'] = $region_id;
		}
		if( !empty($type) ) {
			switch ($type) {
				case 'group':
            		$default_options['conditions']['User.group_id'] = 4;
					break;
				case 'company':
            		$default_options['conditions']['User.group_id'] = 3;
					break;
			}
            $default_options['contain'][] = 'User';
		}

		if( !empty($default_options['contain']) ) {
            $default_options['contain'] = array_unique($default_options['contain']);
        }

		return $default_options;
	}

	function removeValidator(){
		$this->validator()->remove('logo');
		$this->validator()->remove('name');
		$this->validator()->remove('address');
		$this->validator()->remove('region_id');
		$this->validator()->remove('city_id');
		$this->validator()->remove('zip');
		$this->validator()->remove('contact_name');
		$this->validator()->remove('contact_email');
		$this->validator()->remove('phone');
		$this->validator()->remove('fax');
	}
}
?>