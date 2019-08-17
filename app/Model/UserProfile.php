<?php
class UserProfile extends AppModel {
	var $name = 'UserProfile';
	var $validate = array(
		'address' => array(
			'valLocation' => array(
				'rule' => array('valLocation', 'address'),
				'message' => 'Mohon masukkan alamat Anda',
			),
		),
		'country_id' => array(
			'valLocation' => array(
				'rule' => array('valLocation', 'country_id'),
				'message' => 'Mohon pilih negara Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty'=> true,
				'message' => 'Mohon pilih negara Anda',
			),
		),
		'region_id' => array(
			'valLocation' => array(
				'rule' => array('valLocation', 'region_id'),
				'message' => 'Mohon pilih provinsi Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty'=> true,
				'message' => 'Mohon pilih provinsi Anda',
			),
		),
		'city_id' => array(
			'valLocation' => array(
				'rule' => array('valLocation', 'city_id'),
				'message' => 'Mohon pilih kota Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty'=> true,
				'message' => 'Mohon pilih kota Anda',
			),
		),
		'subarea_id' => array(
			'valLocation' => array(
				'rule' => array('valLocation', 'subarea_id'),
				'message' => 'Mohon pilih area Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty'=> true,
				'message' => 'Mohon pilih area Anda',
			),
		),
		'zip' => array(
			'valLocation' => array(
				'rule' => array('valLocation', 'zip'),
				'message' => 'Mohon masukkan kode pos',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty'=> true,
				'message' => 'Kode pos tidak valid. Mohon hanya mengisi dengan angka',
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
			'validateWa' => array(
				'rule' => array('validateWa'),
				'message'=> 'Mohon masukkan No. Handphone Anda',
			),
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
		'website' => array(
			'url' => array(
				'rule' => array('url'),
				'allowEmpty'=> true,
				'message'=> 'Format website salah',
			),
		),
		'rekening_nama_akun' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama akun',
			),
		),
		'rekening_bank' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama bank',
			),
		),
		'no_rekening' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan no. rekening',
			),
		),
		'no_npwp' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan no. NPWP',
			),
		),
	);
	
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Country' => array(
			'className' => 'Country',
			'foreignKey' => 'country_id',
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
		)
	);

	function validateWa () {
		$data = $this->data;
		$no_hp_2 = $this->filterEmptyField($data, 'UserProfile', 'no_hp_2');
		$no_hp_2_is_whatsapp = $this->filterEmptyField($data, 'UserProfile', 'no_hp_2_is_whatsapp');

		if( !empty($no_hp_2_is_whatsapp) && empty($no_hp_2) ) {
			return false;
		} else {
			return true;
		}
	}

	function valLocation ( $data, $fieldName ) {
		$data = $this->data;
		$group_id = $this->filterEmptyField($data, 'User', 'group_id');
		$value = $this->filterEmptyField($data, 'UserProfile', $fieldName);

		if( empty($value) ) {
			if( $group_id == 10 ) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	function beforeSave( $options = array() ) {

		$day_birth = !empty( $this->data['UserProfile']['day_birth'] ) ? $this->data['UserProfile']['day_birth'] : false;
		$month_birth = !empty( $this->data['UserProfile']['month_birth'] ) ? $this->data['UserProfile']['month_birth'] : false;
		$year_birth = !empty( $this->data['UserProfile']['year_birth'] ) ? $this->data['UserProfile']['year_birth'] : false;

		if ( $day_birth && $month_birth && $year_birth ) {
			$birthday = $year_birth.'-'.$month_birth.'-'.$day_birth;
			$this->data['UserProfile']['birthday'] = date('Y-m-d', strtotime($birthday));
		}

		return true;
	}

	function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);
		
		$this->virtualFields['birthday'] = sprintf('CASE WHEN %s.birthday = \'0000-00-00\' THEN NULL ELSE %s.birthday END', $this->alias, $this->alias);
		$this->virtualFields['day_birth'] = sprintf('DAY(%s.birthday)', $this->alias, $this->alias);
		$this->virtualFields['month_birth'] = sprintf('MONTH(%s.birthday)', $this->alias, $this->alias);
		$this->virtualFields['year_birth'] = sprintf('YEAR(%s.birthday)', $this->alias, $this->alias);
		$this->virtualFields['address'] = sprintf('CASE WHEN %s.address = \'\' THEN NULL ELSE %s.address END', $this->alias, $this->alias);
	}
	
	/**
	* 	@param string $find - all, list, paginate, count
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	*		string count - Pick jumah data
	*		string paginate - Pick opsi query
	* 	@param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
	* 	@return array - hasil array atau opsi 
	*/
    function getData( $find = 'all', $options = array() ){
		if( $find == 'paginate' ) {
			$result = $options;
		} else {
			$result = $this->find($find, $options);
		}
        return $result;
	}

	/**
	* 	@param array $data - data array "phone", "no_hp", "no_hp_2"
	* 	@return boolean true or false
	*/
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

    function getMerge ( $data, $user_id, $with_contain = false, $modelName = 'UserProfile' ) {

    	if( empty($data[$modelName]) && !empty($user_id) ) {
			$userProfile = $this->getData('first', array(
				'conditions' => array(
					'UserProfile.user_id' => $user_id
				),
			));

			if(!empty($userProfile)){
				$data[$modelName] = $userProfile['UserProfile'];
			}
		}

		if( !empty($data[$modelName]) ) {
			if( !empty($with_contain) ){
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
		}

		return $data;
	}

	function doSave( $data, $user_id, $id = false ,$check_term = true) {
		if ( !empty($id) ) {
			$this->id = $id;
		} else {
			$this->create();
			$data['UserProfile']['user_id'] = $user_id;
		}
		$this->set($data);
		if($this->validates()){
			if($check_term){
				if ( $this->save() ) {
					return true;
				} else {
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
?>