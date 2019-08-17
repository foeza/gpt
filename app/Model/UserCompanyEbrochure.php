<?php
class UserCompanyEbrochure extends AppModel {
//	untuk clear cache
	var $companyID;

	var $name = 'UserCompanyEbrochure';

	var $validate = array(
		'agent_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Agen harap dipilih'
			),
			'validateUserId' => array(
				'rule' => array('validateUserId'),
				'message' => 'Agen ini tidak terdaftar'
			),
			'validateUnderExist' => array(
				'rule' => array('validateUnderExist'),
				'message' => 'Agen tidak terdaftar. Silahkan masukkan Agen divisi bawahan Anda',
			),
		),
		'property_type_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Tipe Properti harap dipilih'
			),
		),
		'property_title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Kalimat Promosi harap diisi'
			),
		//	'maxLength' => array(
		//		'rule' => array('maxLength', 60),
		//		'message' => 'Panjang kalimat promosi maksimal 60 karakter',
		//	),
		),
		'property_price' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Harga Properti harap dipilih'
			),
			'numerinMin' => array(
				'rule' => array('numerinMin'),
				'message' => 'Harga Properti harus lebih besar dari 0'
			)
		),
		'region_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Provinsi harap dipilih'
			),
		),
		'city_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Kota harap dipilih'
			),
		),
		'subarea_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Area harap dipilih'
			),
		),
	//	'description' => array(
	//		'notempty' => array(
	//			'rule' => array('notempty'),
	//			'message' => 'Deskripsi harap diisi'
	//		),
	//	),
		'property_media_id' => array(
			'validationPhoto'=> array(
				'rule' => array('validationPhoto'),
				'message' => 'Harap pilih atau unggah foto yang Anda inginkan'
			)
		),
		'filename' => array(
			'validationPhoto'=> array(
				'rule' => array('validationPhoto'),
				'message' => 'Harap pilih atau unggah foto yang Anda inginkan'
			)
		),
		'to_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email penerima harap diisi.'
			),
			'multipleEmail' => array(
				'rule' => array('multipleEmail'),
				'message' => 'Format email Anda salah.',
			)
		),
	);

	var $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
		'PropertyAction' => array(
			'className' => 'PropertyAction',
			'foreignKey' => 'property_action_id',
		),
		'PropertyType' => array(
			'className' => 'PropertyType',
			'foreignKey' => 'property_type_id',
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
		'Currency' => array(
            'className' => 'Currency',
            'foreignKey' => 'currency_id',
        ),
		'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
		'Period' => array(
			'className' => 'Period',
			'foreignKey' => 'period_id',
		),
        'LotUnit' => array(
            'className' => 'LotUnit',
            'foreignKey' => 'lot_unit_id',
        ),
	);

	function validateUnderExist(){
		$group_id = Configure::read('User.group_id');
		$login_id = Configure::read('User.id');

		if($group_id > 20){
			$data = $this->data;
			$data_arr = $this->User->getUserParent($login_id);

			$agent_email = Common::hashEmptyField($data, 'UserCompanyEbrochure.agent_email');
			$agent = $this->User->getData('first', array(
				'conditions' => array(
					'User.email' => trim($agent_email),
				),
			), array(
				'status' => array(
					'active',
					'non-active',
				),
			));

			$superior_id = Common::hashEmptyField($agent, 'User.superior_id');

			if($login_id == $superior_id){
				return true;
			} else {
				return false;
			}

		}
		return true;
	}

	function validateUserId($data){
		$result = true;
		
		if(!empty($this->data['UserCompanyEbrochure']['agent_email'])){
			$agent_email = $this->filterEmptyField($this->data, 'UserCompanyEbrochure', 'agent_email');

			$user = $this->User->getData('first', array(
				'conditions' => array(
					'User.email' => $agent_email
				)
			), array(
				'role' => 'agent',
				'company' => true,
				'status' => 'semi-active',
				'admin' => false,
			));

			if(empty($user)){
				$result = false;
			}
		}

		return $result;
	}

	function beforeSave($options = array()){
		// cetak created ke table
		$group_id = Configure::read('User.group_id');

		$id = $this->id;
		$id = Common::hashEmptyField($this->data, 'UserCompanyEbrochure.id', $id);

		if( empty($id) ) {
			$principle_id = Configure::read('Principle.id');
			$this->data = Hash::insert($this->data, 'UserCompanyEbrochure.principle_id', $principle_id);

			$company_id = Configure::read('Config.Company.data.UserCompany.id');
			$this->data = Hash::insert($this->data, 'UserCompanyEbrochure.company_id', $company_id);
			
			$this->data = Hash::insert($this->data, 'UserCompanyEbrochure.group_id', $group_id);
		}
	}

	public function afterSave($created, $options = array()){
		$dataCompany = Configure::read('Config.Company.data');
		$company_id = !empty($dataCompany['UserCompany']['id'])?$dataCompany['UserCompany']['id']:false;
		$companyID = $this->companyID ? $this->companyID : $company_id;

	//	find
		$cacheGroup		= 'Ebrosurs.Find';
		$cacheNameInfix	= 'ebrosurs__index_';
		$cachePath		= CACHE.$cacheGroup;
		$wildCard		= '*'.$cacheNameInfix.$companyID.'*';
		$cleared		= clearCache($wildCard, $cacheGroup, NULL);

	//	detail
		if(isset($this->id) && $this->id){
		//	untuk save, update
			$cacheGroup		= 'Ebrosurs.Detail';
			$cacheConfig	= 'ebrosurs_detail';
			$cacheName		= sprintf($cacheGroup.'.%s.%s', $companyID, $this->id);

			Cache::delete($cacheName, $cacheConfig);
		}
		else if(isset($options['record_id']) && $options['record_id']){
		//	untuk update all
			$recordID		= $options['record_id'];
			$cacheGroup		= 'Ebrosurs.Detail';
			$cacheNameInfix	= 'ebrosurs__detail_';
			$wildCard		= array();

			foreach($recordID as $id){
				$wildCard[] = '*'.$cacheNameInfix.$companyID.'_'.$id;
			}

			$cleared = clearCache($wildCard, $cacheGroup, NULL);
		}
		
		Cache::clearGroup('Ebrosurs.Find');
	}

	function multipleEmail($data){
		$result = true;
		if(!empty($data['to_email'])){
			$emails = explode(',', $data['to_email']);

			if(is_array($emails)){
				$hostname = '(?:[_\p{L}0-9][-_\p{L}0-9]*\.)*(?:[\p{L}0-9][-\p{L}0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})';
				$regex = '/^[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+)*@' . $hostname . '$/ui';

				foreach ($emails as $key => $email) {
					if (!empty($email) && preg_match($regex, $email) === 1) {
					    $result = true;
					}else{
						$result = false;

						break;
					}
				}				
			}
		}else{
			$result = false;
		}

		return $result;
	}

	function numerinMin($data){
		$result = false;
		
		if(!empty($data['property_price']) && $data['property_price'] > 0){
			$result = true;
		}
		
		return $result;
	}

	function validationPhoto($data){
		$result = false;

		if(!empty($this->data['UserCompanyEbrochure']['filename']) || !empty($this->data['UserCompanyEbrochure']['property_media_id'])){
			$result = true;
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
		$title = $this->filterEmptyField($data, 'named', 'title', false, array(
        	'addslashes' => true,
    	));
		$property_type = $this->filterEmptyField($data, 'named', 'property_type', false, array(
        	'addslashes' => true,
    	));
		$property_title = $this->filterEmptyField($data, 'named', 'property_title', false, array(
        	'addslashes' => true,
    	));
		$location = $this->filterEmptyField($data, 'named', 'location', false, array(
        	'addslashes' => true,
    	));
		$mls_id = $this->filterEmptyField($data, 'named', 'mls_id', false, array(
        	'addslashes' => true,
    	));
		$price = $this->filterEmptyField($data, 'named', 'price', false, array(
        	'addslashes' => true,
    	));
		$description = $this->filterEmptyField($data, 'named', 'description', false, array(
        	'addslashes' => true,
    	));
        $user = $this->filterEmptyField($data, 'named', 'user', false, array(
            'addslashes' => true,
        ));
    	$modified_from = $this->filterEmptyField($data, 'named', 'modified_from', false, array(
            'addslashes' => true,
        ));
        $modified_to = $this->filterEmptyField($data, 'named', 'modified_to', false, array(
            'addslashes' => true,
        ));

		$default_options['conditions'] = !empty($default_options['conditions']) ? $default_options['conditions'] : array();
		$is_search = false;
		$is_property_contain = false;
		$is_property_asset_contain = false;

		//	"OR" conditions
		if( !empty($keyword) ) {
            $default_options['conditions']['OR'] = array(
                'UserCompanyEbrochure.mls_id LIKE ' => '%'.$keyword.'%',
                'UserCompanyEbrochure.code LIKE ' => '%'.$keyword.'%',
                'UserCompanyEbrochure.property_title LIKE ' => '%'.$keyword.'%',
                'UserCompanyEbrochure.name LIKE ' => '%'.$keyword.'%',
            );

            $region = $this->Region->getData('list', array(
				'conditions'	=> array('Region.name LIKE' => '%'.$keyword.'%'),
                'fields'		=> array('Region.id', 'Region.id')
            ));

            $city = $this->City->getData('list', array(
                'conditions'	=> array('City.name LIKE ' => '%'.$keyword.'%'),
                'fields'		=> array('City.id', 'City.id')
            ));

            if( !empty($region) ) {
	            $default_options['conditions']['OR']['UserCompanyEbrochure.region_id'] = $region;
	        }
	        if( !empty($city) ) {
	            $default_options['conditions']['OR']['UserCompanyEbrochure.city_id'] = $city;
	        }
	        if( !empty($subarea) ) {
	            $default_options['conditions']['OR']['UserCompanyEbrochure.subarea_id'] = $subarea;
	        }

	        $is_search = true;
        }
		//	"AND" conditions, sebagian parameter conflict dengan parameter di atas, jadi dipindahkan disini
		$code = $this->filterEmptyField($data, 'named', 'code', false, array(
        	'addslashes' => true,
    	));
    	$name = $this->filterEmptyField($data, 'named', 'name', false, array(
        	'addslashes' => true,
    	));
    	$date_from = $this->filterEmptyField($data, 'named', 'date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = $this->filterEmptyField($data, 'named', 'date_to', false, array(
            'addslashes' => true,
        ));
        $region = $this->filterEmptyField($data, 'named', 'region', false, array(
            'addslashes' => true,
        ));
        $city = $this->filterEmptyField($data, 'named', 'city', false, array(
            'addslashes' => true,
        ));
        $subarea = $this->filterEmptyField($data, 'named', 'subarea', false, array(
            'addslashes' => true,
        ));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));
        $typeid = $this->filterEmptyField($data, 'named', 'typeid', false, array(
            'addslashes' => true,
        ));
        $price = $this->filterEmptyField($data, 'named', 'price', false, array(
            'addslashes' => true,
        ));
        $beds = $this->filterEmptyField($data, 'named', 'beds', false, array(
            'addslashes' => true,
        ));
        $baths = $this->filterEmptyField($data, 'named', 'baths', false, array(
            'addslashes' => true,
        ));
        $lot_size = $this->filterEmptyField($data, 'named', 'lot_size', false, array(
            'addslashes' => true,
        ));
        $building_size = $this->filterEmptyField($data, 'named', 'building_size', false, array(
            'addslashes' => true,
        ));
        $certificate_id = $this->filterEmptyField($data, 'named', 'certificate_id', false, array(
            'addslashes' => true,
        ));
        $furnished = $this->filterEmptyField($data, 'named', 'furnished', false, array(
            'addslashes' => true,
        ));
        $property_action = $this->filterEmptyField($data, 'named', 'property_action', false, array(
            'addslashes' => true,
        ));
        $property_direction_id = $this->filterEmptyField($data, 'named', 'property_direction_id', false, array(
            'addslashes' => true,
        ));

		if(!empty($price)){
			$price = explode('-', $price);

			if(!empty($price[0])){
				$min_price = str_replace('>', '', urldecode($price[0]));
				$default_options['conditions']['UserCompanyEbrochure.property_price >='] = $min_price;
			}

			if(!empty($price[1])){
				$max_price = str_replace('<', '', urldecode($price[1]));

				$default_options['conditions']['UserCompanyEbrochure.property_price <='] = $max_price;
			}
		}

        if($code){
        	$default_options['conditions']['UserCompanyEbrochure.code LIKE'] = '%'.$code.'%';
        	$is_search = true;
        }

        if($name){
        	$name = urldecode($name);
        	$userData = $this->User->getData('list', array(
        		'conditions' => array(
        			'OR' => array(
        				'User.email LIKE' => '%'.$name.'%',
        				'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE' =>  '%'.$name.'%',
    				),
    			),
    			'fields' => array(
    				'User.id', 'User.id',
				),
    		), array(
    			'status' => 'semi-active',
    			'company' => true,
    		));
        	$default_options['conditions']['UserCompanyEbrochure.user_id'] = $userData;
        	$is_search = true;
        }

        $agent = $this->filterEmptyField($data, 'named', 'agent', false, array(
            'addslashes' => true,
        ));

		if($agent){
			$default_options['contain'][]			= 'User';
			$default_options['conditions'][]['or']	= array(
				'User.email LIKE' => '%'.$agent.'%',
				'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE' =>  '%'.$agent.'%',
			);

			$is_search = true;
		}

        if($region){
			$default_options['conditions']['UserCompanyEbrochure.region_id'] = $region;
			$is_search = true;
        }

        if($city){
        	$default_options['conditions']['UserCompanyEbrochure.city_id'] = $city;
        	$is_search = true;
        }

        if($subarea){
        	$default_options['conditions']['UserCompanyEbrochure.subarea_id'] = $subarea;
        	$is_search = true;
        }

        if($date_from){
        	$default_options['conditions']["DATE_FORMAT(UserCompanyEbrochure.created, '%Y-%m-%d') >="] = $date_from;
        	$is_search = true;
        }

        if($date_to){
        	$default_options['conditions']["DATE_FORMAT(UserCompanyEbrochure.created, '%Y-%m-%d') <="] = $date_to;
        	$is_search = true;
        }

		if( !empty($sort) ) {
			$sortUser = strpos($sort, 'User.');
        	$sortType = strpos($sort, 'PropertyType.');
        	$sortCity = strpos($sort, 'City.');
        	$sortProperty = strpos($sort, 'Property.');

        	if( is_numeric($sortType) ) {
	            $default_options['contain'][] = 'PropertyType';
	        } else if( is_numeric($sortCity) ) {
	            $default_options['contain'][] = 'City';
	        } else if( is_numeric($sortProperty) ) {
	            $default_options['contain'][] = 'Property';
	        }
	        else if(is_numeric($sortUser)){
	        	$default_options['contain'][] = 'User';
	        }
        }

        if( !empty($user) ) {
            $default_options['conditions']['UserCompanyEbrochure.user_id'] = explode(',', $user);
        }
        if( !empty($property_action) ) {
        	$property_action = urldecode($property_action);
            $default_options['conditions']['UserCompanyEbrochure.property_action_id'] = $property_action;
        }
        if( !empty($typeid) ) {
        	$typeid = urldecode($typeid);
            $default_options['conditions']['UserCompanyEbrochure.property_type_id'] = explode(',', $typeid);
        }
        if(!empty($beds)){
			$default_options['conditions']['PropertyAsset.beds >='] = $beds;
			$is_property_asset_contain = true;
		}
		if(!empty($baths)){
			$default_options['conditions']['PropertyAsset.baths >='] = $baths;
			$is_property_asset_contain = true;
		}
		if(!empty($lot_size)){
			$default_options['conditions']['PropertyAsset.lot_size <='] = $lot_size;
			$is_property_asset_contain = true;
		}
		if(!empty($building_size)){
			$default_options['conditions']['PropertyAsset.building_size <='] = $building_size;
			$is_property_asset_contain = true;
		}
		if(!empty($certificate_id)){
			$default_options['conditions']['Property.certificate_id'] = $certificate_id;
			$is_property_contain = true;
		}
		if(!empty($furnished)){
			$default_options['conditions']['PropertyAsset.furnished'] = $furnished;
			$is_property_asset_contain = true;
		}
		if(!empty($property_direction_id)){
			$default_options['conditions']['PropertyAsset.property_direction_id'] = $property_direction_id;
			$is_property_asset_contain = true;
		}

		if($is_property_contain){
			$default_options['contain'][] = 'Property';
		}

		if($is_property_asset_contain){
			$this->bindModel(array(
	            'hasOne' => array(
	                'PropertyAsset' => array(
	                    'className' => 'PropertyAsset',
	                    'foreignKey' => false,
	                    'conditions' => array(
	                        'PropertyAsset.property_id = UserCompanyEbrochure.property_id',
	                    ),
	                ),
	            )
	        ), false);
			$default_options['contain'][] = 'PropertyAsset';
		}

        if( !empty($title) ) {
            $default_options['conditions']['UserCompanyEbrochure.name LIKE'] = '%'.$title.'%';
	        $is_search = true;
        }
        if( !empty($property_type) ) {
            $default_options['conditions']['UserCompanyEbrochure.property_type LIKE'] = '%'.$property_type.'%';
	        $is_search = true;
        }
        if( !empty($property_title) ) {
            $default_options['conditions']['UserCompanyEbrochure.property_title LIKE'] = '%'.$property_title.'%';
	        $is_search = true;
        }
        if( !empty($location) ) {
            $default_options['conditions']['UserCompanyEbrochure.location LIKE'] = '%'.$location.'%';
	        $is_search = true;
        }
        if( !empty($mls_id) ) {
            $default_options['conditions']['Property.mls_id LIKE'] = '%'.$mls_id.'%';
			$default_options['contain'][] = 'Property';
	        $is_search = true;
        }
        if( !empty($property_price) ) {
			$firstString = substr($price, 0, 1);

			if( in_array($firstString, array( '>', '<' )) ) {
				$price = substr($price, 1);
				$default_options['conditions']['UserCompanyEbrochure.property_price '.$firstString] = $price;
			} else {
				$price = explode('-', $price);
				$min_price = !empty($price[0])?$price[0]:false;
				$max_price = !empty($price[1])?$price[1]:false;

				if( !empty($min_price) ) {
					$default_options['conditions']['UserCompanyEbrochure.property_price >='] = $min_price;
				}
				if( !empty($max_price) ) {
					$default_options['conditions']['UserCompanyEbrochure.property_price <='] = $max_price;
				}
			}
			
	        $is_search = true;
        }
        if( !empty($description) ) {
            $default_options['conditions']['UserCompanyEbrochure.description LIKE'] = '%'.$description.'%';
	        $is_search = true;
        }
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(UserCompanyEbrochure.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(UserCompanyEbrochure.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}

        if( $sort == 'UserCompanyEbrochure.property_type' || !empty($property_type) ) {
			$this->virtualFields['property_type'] = 'CONCAT(PropertyType.name,\' \',PropertyAction.name)';

            $default_options['contain'][] = 'PropertyType';
            $default_options['contain'][] = 'PropertyAction';
        }
        if( $sort == 'UserCompanyEbrochure.location' || !empty($location) ) {
			$this->virtualFields['location'] = 'CONCAT(City.name,\' \',Subarea.name)';
			
            $default_options['contain'][] = 'City';
            $default_options['contain'][] = 'Subarea';
        }

        $default_options['is_search'] = $is_search;
		return $default_options;
	}

	function getData($find, $options = array(), $elements = array()){
    	$force_non_company = isset($elements['force_non_company'])?$elements['force_non_company']:false;
    	$mine = isset($elements['mine'])?$elements['mine']:false;
    	$company = isset($elements['company'])?$elements['company']:true;
    	$admin = isset($elements['admin'])?$elements['admin']:false;
    	$status = isset($elements['status'])?$elements['status']:'active';
    	$action_type = isset($elements['action_type'])?$elements['action_type']:false;

    	$data_show = Common::hashEmptyField($elements, 'data_show');	
    	$is_sales = Common::hashEmptyField($elements, 'is_sales', true, array(
    		'isset' => true,
    	));

    	$parent_id = Configure::read('Principle.id');
        $companyData = Configure::read('Config.Company.data');

		$default_options = array(
			'conditions' => array(),
			'contain' => array(),
			'order' => array(
				'UserCompanyEbrochure.id' => 'DESC'
			),
			'fields' => array(),
		);

		switch ($status) {
            case 'active':
                $statusConditions = array(
					'UserCompanyEbrochure.status' => 1,
            	);
                break;
            
            case 'all':
                $statusConditions = array(
                    'UserCompanyEbrochure.status' => array(0,1),
                );
                break;

            case 'deleted':
                $statusConditions = array(
                    'UserCompanyEbrochure.status' => 0,
                );
                break;
        }

        if( in_array('PropertyAsset', $default_options['contain']) ) {
	    	$this->bindModel(array(
	            'hasOne' => array(
	                'PropertyAsset' => array(
	                    'className' => 'PropertyAsset',
	                    'foreignKey' => false,
	                    'conditions' => array(
	                        'PropertyAsset.property_id = UserCompanyEbrochure.property_id',
	                    ),
	                ),
	            )
	        ), false);
	    }

        $default_options['conditions'] = $statusConditions;

    	$is_admin = Configure::read('User.admin');
        $group_id = Configure::read('User.group_id');
	    $user_login_id = Configure::read('User.id');
	    
        if( empty($admin) ) {
	        if(!empty($is_sales)){
		        $data_arr = $this->User->getUserParent($user_login_id);
				$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
				$is_sales = Common::hashEmptyField($data_arr, 'is_sales');
	        }

	        if( !empty($is_admin) || ( !empty($data_show) && in_array($group_id, array(2, 3)) ) ) {
	        	if( !(!empty($is_admin) && !empty($is_sales)) ) {
		        	$mine = false;
		        	$is_sales = false;
	        	}
	        }

			if(empty($is_sales)){
				$company = true;
			}

			if($force_non_company){
				$company = false;
			}

			if( !empty($mine) || !empty($is_sales) ) {
	            $default_options['conditions']['UserCompanyEbrochure.principle_id'] = $parent_id;
	            $default_options['conditions']['UserCompanyEbrochure.user_id'] = $user_ids;
	        } else if( !empty($company) ) {
	            $company_group_id = $this->filterEmptyField($companyData, 'User', 'group_id');

	        	if( $company_group_id == 4 ) {
					$principle_id = $this->User->getAgents($parent_id, true, 'list', false, array(
						'role' => 'principle',
					));

					$default_options['conditions']['User.parent_id'] = $principle_id;
					$default_options['contain'][] = 'User';
	        	} else {
		      //   	$agent_id = $this->User->getAgents($parent_id, true, 'list', false, array(
		      //   		'role' => 'all',
		      //   		'data_show' => $data_show,
		    		// ));

		      //       $default_options['conditions']['UserCompanyEbrochure.user_id'] = $agent_id;
		            $default_options['conditions']['UserCompanyEbrochure.principle_id'] = $parent_id;
	        	}
	        }
	    }

        switch ($action_type) {
        	case 'sell':
	            $default_options['conditions']['UserCompanyEbrochure.property_action_id'] = 1;
        		break;
        	case 'rent':
	            $default_options['conditions']['UserCompanyEbrochure.property_action_id'] = 2;
        		break;
        }

        if(!empty($is_admin)){
        	if(isset($default_options['conditions']['UserCompanyEbrochure.user_id'])){
        		$default_options['conditions']['UserCompanyEbrochure.user_id'][$parent_id] = $parent_id;
        	}
        }

        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count', 'conditions' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
				  'UserCompanyEbrochure.id',
				  'UserCompanyEbrochure.user_id',
				  'UserCompanyEbrochure.ebrosur_photo',
				  'UserCompanyEbrochure.name',
				  'UserCompanyEbrochure.code',
				  'UserCompanyEbrochure.phone',
				  'UserCompanyEbrochure.mls_id',
				  'UserCompanyEbrochure.currency_id',
				  'UserCompanyEbrochure.background_color',
				  'UserCompanyEbrochure.property_media_id',
				  'UserCompanyEbrochure.property_action_id',
				  'UserCompanyEbrochure.property_type_id',
				  'UserCompanyEbrochure.property_id',
				  'UserCompanyEbrochure.description',
				  'UserCompanyEbrochure.property_title',
				  'UserCompanyEbrochure.property_price',
				  'UserCompanyEbrochure.property_photo',
				  'UserCompanyEbrochure.note_price',
				  'UserCompanyEbrochure.period_id',
				  'UserCompanyEbrochure.lot_unit_id',
				  'UserCompanyEbrochure.filename',
				  'UserCompanyEbrochure.region_id',
				  'UserCompanyEbrochure.city_id',
				  'UserCompanyEbrochure.subarea_id',
				  'UserCompanyEbrochure.is_description_ebrochure',
				  'UserCompanyEbrochure.is_specification_ebrochure',
				  'UserCompanyEbrochure.created',
				  'UserCompanyEbrochure.modified',
                );
            }
        }

        return $options;
    }

	function doSave($data, $value = false, $is_validate = true, $ebrosur_id = false, $is_api = false){
		if(!empty($is_api)){
			$this->removeValidate();
		}
		
		if(!empty($data)){
			if(!empty($data['UserCompanyEbrochure']['mls_id']) && empty($data['UserCompanyEbrochure']['property_id'])){
				$temp = explode(',', $data['UserCompanyEbrochure']['mls_id']);

				if(!empty($temp[0])){
					$property = $this->Property->getData('first', array(
						'conditions' => array(
							'Property.mls_id' => $temp[0]
						)
					), array(
						'status' => 'active-pending-sold',
					));

					if(!empty($property['Property']['id'])){
						$data['UserCompanyEbrochure']['property_id'] = $property['Property']['id'];
						$data['UserCompanyEbrochure']['mls_id'] = $property['Property']['mls_id'];
					}
				}
			}

			if(!empty($data['UserCompanyEbrochure']['property_price'])){
				$data['UserCompanyEbrochure']['property_price'] = str_replace(',', '', $data['UserCompanyEbrochure']['property_price']);
			}
			
			if(empty($value['UserCompanyEbrochure']['id'])){
				$this->create();
				$text = 'membuat';

				$principle_id = Configure::read('Principle.id');
				$data['UserCompanyEbrochure']['principle_id'] = $principle_id;

				$company_id = Configure::read('Config.Company.data.UserCompany.id');
				$data['UserCompanyEbrochure']['company_id'] = $company_id;
			}else{
				$this->id = $value['UserCompanyEbrochure']['id'];
				$text = 'mengubah';
			}

			$this->set($data);

			$result = array(
	            'msg' => sprintf(__('Gagal %s eBrosur.'), $text),
	            'status' => 'error',
	        );

			$validate = true;
	        if($is_validate){
	        	$validate = $this->validates($data);
	        }

			if($validate){
				if($this->save($data)){
					$id = $this->id;
					$msg = sprintf(__('Berhasil %s eBrosur.'), $text);

					$result = array(
			            'msg' => $msg,
			            'status' => 'success',
			            'id' => $id,
			            'Log' => array(
							'activity' => $msg,
							'old_data' => $data,
						),
			        );
				} else {
					$result['validationErrors'] = $this->validationErrors;
					$result['Log'] = array(
						'activity' => sprintf(__('Gagal %s eBrosur.'), $text),
						'data' => $data,
						'error' => 1
					);
				}
			}else{
				$result['data'] = $data;
				$result['validationErrors'] = $this->validationErrors;
				$result['Log'] = array(
					'activity' => sprintf(__('Gagal %s eBrosur.'), $text),
					'data' => $data,
					'error' => 1
				);
			}
		}else{
			$city_id = !empty($value['UserCompanyEbrochure']['city_id']) ? $value['UserCompanyEbrochure']['city_id'] : false;
			$subarea_id = !empty($value['UserCompanyEbrochure']['subarea_id']) ? $value['UserCompanyEbrochure']['subarea_id'] : false;
			$filename = !empty($value['UserCompanyEbrochure']['filename']) ? $value['UserCompanyEbrochure']['filename'] : false;
			$property_media_id = !empty($value['UserCompanyEbrochure']['property_media_id']) ? $value['UserCompanyEbrochure']['property_media_id'] : false;
			$property_id = $this->filterEmptyField($value, 'UserCompanyEbrochure', 'property_id');

			if(!empty($city_id) || !empty($subarea_id)){
				$location = '';

				if(!empty($subarea_id)){
					$subarea = $this->User->UserProfile->Subarea->getSubareaByID($subarea_id);
					$subarea = !empty($subarea['Subarea']['name']) ? $subarea['Subarea']['name'] : '';

					if(!empty($subarea)){
						$location .= $subarea.', ';
					}
				}

				if(!empty($city_id)){
					$city = $this->User->UserProfile->City->getCity($city_id);
					$city = !empty($city['City']['name']) ? $city['City']['name'] : '';

					$location .= $city;
				}
				
				$value['UserCompanyEbrochure']['location'] = $location;
			}

			if(!empty($filename) || !empty($property_media_id)){
				$property_photo = '';
				$path_photo_property = '';

				if(!empty($filename)){
					$path_photo_property = Configure::read('__Site.ebrosurs_photo');
					$value['UserCompanyEbrochure']['filename_hide'] = $property_photo = $filename; 
				} else if( !empty($property_id) ) {
					$path_photo_property = Configure::read('__Site.property_photo_folder');
					$options = array(
						'conditions' => array(
							'PropertyMedias.property_id' => $property_id
						)
					);

					if(!empty($property_media_id)) {
						$options['conditions']['PropertyMedias.id'] = $property_media_id;
					}

					$property_media = $this->Property->PropertyMedias->getData('first', $options, array(
						'status' => 'all'
					));

					$property_photo = $this->filterEmptyField($property_media, 'PropertyMedias', 'name'); 
				}

				$value['UserCompanyEbrochure']['_property_photo'] = $property_photo;
				$value['UserCompanyEbrochure']['path_photo_property'] = $path_photo_property;
			}

			$result['data'] = $value;
			$user = Configure::read('User.data');

			if(!Configure::read('User.admin') && empty($value) && empty($ebrosur_id)){
				$user = $this->User->UserProfile->getMerge($user, $user['id']);
				
				$result['data']['UserCompanyEbrochure']['user_photo'] = $user['photo'];
				$result['data']['UserCompanyEbrochure']['name'] = $user['full_name'];
				$result['data']['UserCompanyEbrochure']['email'] = $user['email'];
				$result['data']['UserCompanyEbrochure']['phone'] = $user['UserProfile']['no_hp'];
			}

			if(empty($value) && !empty($ebrosur_id)){
				$result = array(
					'msg' => __('eBrosur tidak ditemukan.'),
					'status' => 'error',
					'redirect' => array(
                        'controller' => 'ebrosurs',
                        'action' => 'index',
                        'admin' => true,
                    ),
				);
			}else if(!empty($value['UserCompanyEbrochure']['user_id'])){
				$user = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $value['UserCompanyEbrochure']['user_id']
					)
				), array(
					'company' => true
				));

				$result['data']['UserCompanyEbrochure']['agent_email'] = !empty($user['User']['email']) ? $user['User']['email'] : '';
				$result['data']['UserCompanyEbrochure']['user_photo'] = !empty($user['User']['photo']) ? $user['User']['photo'] : '';
			}
		}

		return $result;
	}

	function doDelete( $id ) {
	
		$result = false;
		$ebrosurs = $this->getData('all', array(
        	'conditions' => array(
				'UserCompanyEbrochure.id' => $id,
			),
		));

		if ( !empty($ebrosurs) ) {
			$title = Set::extract('/UserCompanyEbrochure/mls_id', $ebrosurs);
			$title = implode(', ', $title);
			$default_msg = sprintf(__('menghapus eBrosur %s'), $title);

			$flag = $this->updateAll(array(
				'UserCompanyEbrochure.status' => 0,
	    		'UserCompanyEbrochure.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'UserCompanyEbrochure.id' => $id,
			));

            if( $flag ) {
			//	trigger aftersave untuk clear cache, updateAll tidak men-trigger afterSave 
				$options = array('record_id' => $id);
				$this->afterSave($flag, $options);

				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $ebrosurs,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $ebrosurs,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus eBrosur. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	function getNeighbor($id){
		$options = $this->getData('paginate', array(), array(
			'mine' => true,
		));

		return $this->find('neighbors', array(
			'contain' => $this->filterEmptyField($options, 'contain'),
			'conditions' => $options['conditions'],
			'field' => 'UserCompanyEbrochure.id', 
			'value' => $id
		));
	}

	public function generateCode($userCode = null){
		$userCode	= (string) $userCode;
		$prefix		= 'EB';
		$codePrefix	= array_filter(array($prefix, $userCode));
		$codePrefix	= implode('-', $codePrefix);

	//	get last generated code
		$lastData = $this->getData('first', array(
			'conditions' => array(
				'UserCompanyEbrochure.code LIKE'	=> $codePrefix.'%', 
				'UserCompanyEbrochure.status'		=> 1, 
			),
			'order'=> array(
				'UserCompanyEbrochure.id' => 'DESC',
			),
		));

		if($lastData){
			$lastOrder	= explode('-', $lastData['UserCompanyEbrochure']['code']);
			$lastOrder	= Common::hashEmptyField($lastOrder, 2, 0);
			$counter	= (int) $lastOrder + 1;
		}
		else{
			$counter = 1;
		}

		$continue	= true;
		$newCode	= '';

		while($continue){
			$newCode = $codePrefix.'-'.str_pad($counter, 4, '0', STR_PAD_LEFT);
			$isExist = $this->getData('count', array(
				'limit'			=> 1, 
				'conditions'	=> array(
					'UserCompanyEbrochure.code'=> $newCode,
				),
			));

			if(empty($isExist)){
				$continue = false;
			}
			else{
				$counter++;
			}
		}

		return $newCode;
	}

/*	
	function generateCode($user_code) {
		$config_invoice_prefix = 'EB-';
		$new_order_no = $config_invoice_prefix.$user_code.'-';
		$last_order = $this->getData('first', array(
			'conditions'=> array(
				'UserCompanyEbrochure.code LIKE' => '%'.$user_code.'%',
				'UserCompanyEbrochure.status' => 1
			),
			'order'=> array(
				'UserCompanyEbrochure.id' => 'DESC',
			),
		));

		$new_code = '';
		$flag = true;
		while ($flag) {
			if(!empty($last_order['UserCompanyEbrochure']['code'])) {
				$last_order = explode('-', $last_order['UserCompanyEbrochure']['code']);

				$new_code .= str_pad((int)$last_order[2]+1, 4, '0', STR_PAD_LEFT);
			} else {
				$new_code .= str_pad(1, 4, '0', STR_PAD_LEFT);
			}
			
			$new_code = $new_order_no.$new_code;
			
			$check_data = $this->getData('count', array(
				'conditions'=> array(
					'UserCompanyEbrochure.code'=> $new_code,
				),
			));

			if( empty($check_data) ) {
				$flag = false;
			}
		}
		
		return $new_code;
	}
*/

	function _callTopEbrosurs ( $fromDate = false, $toDate = false, $limit = 5 ) {
        $this->virtualFields['total'] = 'COUNT(UserCompanyEbrochure.id)';
        $options = array(
        	'contain' => false,
            'group' => array(
                'UserCompanyEbrochure.user_id',
            ),
            'order' => array(
                'total' => 'DESC',
            ),
            'limit' => $limit,
    	);

        if( !empty($fromDate) ) {
        	$options['conditions']["DATE_FORMAT(UserCompanyEbrochure.created, '%Y-%m-%d') >="] = $fromDate;
        }
        if( !empty($toDate) ) {
        	$options['conditions']["DATE_FORMAT(UserCompanyEbrochure.created, '%Y-%m-%d') <="] = $toDate;
        }

        $values = $this->getData('all', $options, array(
        	'data_show' => true,
        ));

    	if( !empty($values) ) {
    		foreach ($values as $key => $value) {
    			$user_id = !empty($value['UserCompanyEbrochure']['user_id'])?$value['UserCompanyEbrochure']['user_id']:false;

    			$value = $this->User->getMerge($value, $user_id, false);
    			$values[$key] = $value;
    		}
    	}

    	return $values;
	}

	function getMerge ( $data, $id = false, $list = 'all' ) {
		if( !empty($id) ) {
			$options = array(
				'conditions' => array(
					'UserCompanyEbrochure.user_id' => $id,
				),
	        	'contain' => false,
	    	);

	    	switch ($list) {
	    		case 'first':
        			$this->virtualFields['total'] = 'COUNT(UserCompanyEbrochure.id)';
        			$options['group'][] = 'UserCompanyEbrochure.user_id';
	    			break;
	    	}

	        $values = $this->getData($list, $options, array(
	        	'company' => false,
        	));

	    	if( !empty($values) ) {
	    		switch ($list) {
		    		case 'first':
	    				$data['UserCompanyEbrochure'] = $values['UserCompanyEbrochure'];
		    			break;
	    			default:
	    				$data['UserCompanyEbrochure'] = $values;
		    			break;
		    	}
	    	}
	    }

    	return $data;
	}

	function getDataMerge( $value ) {
		$property_id = !empty($value['UserCompanyEbrochure']['property_id'])?$value['UserCompanyEbrochure']['property_id']:false;
		$property_type_id = !empty($value['UserCompanyEbrochure']['property_type_id'])?$value['UserCompanyEbrochure']['property_type_id']:false;
		$property_action_id = !empty($value['UserCompanyEbrochure']['property_action_id'])?$value['UserCompanyEbrochure']['property_action_id']:false;
		$currency_id = !empty($value['UserCompanyEbrochure']['currency_id'])?$value['UserCompanyEbrochure']['currency_id']:false;
		$period_id = !empty($value['UserCompanyEbrochure']['period_id'])?$value['UserCompanyEbrochure']['period_id']:false;
		$lot_unit_id = !empty($value['UserCompanyEbrochure']['lot_unit_id'])?$value['UserCompanyEbrochure']['lot_unit_id']:false;
		$region_id = !empty($value['UserCompanyEbrochure']['region_id'])?$value['UserCompanyEbrochure']['region_id']:false;
		$city_id = !empty($value['UserCompanyEbrochure']['city_id'])?$value['UserCompanyEbrochure']['city_id']:false;
		$subarea_id = !empty($value['UserCompanyEbrochure']['subarea_id'])?$value['UserCompanyEbrochure']['subarea_id']:false;
		$user_id = !empty($value['UserCompanyEbrochure']['user_id'])?$value['UserCompanyEbrochure']['user_id']:false;

		$parent_id = Common::hashEmptyField($value, 'User.parent_id');
		
		$value = $this->Property->User->UserCompanyConfig->getMerge($value, $parent_id);
		$value = $this->Property->User->getMerge($value, $user_id);
		$value = $this->Property->getMerge($value, $property_id);
		$value = $this->Property->PropertyAsset->getMerge($value, $property_id);
		$value = $this->PropertyType->getMerge($value, $property_type_id, 'PropertyType.id', array(
            'cache' => array(
                'name' => __('PropertyType.%s', $property_type_id),
            ),
        ));
		$value = $this->PropertyAction->getMerge($value, $property_action_id, 'PropertyAction.id', array(
			'cache' => array(
				'name' => __('PropertyAction.%s', $property_action_id),
			),
		));
		$value = $this->Currency->getMerge($value, $currency_id, 'Currency.id', array(
			'cache' => array(
				'name' => __('Currency.%s', $currency_id),
			),
		));
		$value = $this->Period->getMerge($value, $period_id);
		$value = $this->LotUnit->getMerge($value, $lot_unit_id, 'LotUnit', false, array(
            'cache' => array(
                'name' => __('LotUnit.%s', $lot_unit_id),
            ),
        ));
		$value = $this->Region->getMerge($value, $region_id, 'Region', array(
			'cache' => array(
				'name' => __('Region.%s', $region_id),
			),
		));
		$value = $this->City->getMerge($value, $city_id, 'City', 'City.id', array(
			'cache' => __('City.%s', $city_id),
		));
		$value = $this->Subarea->getMerge($value, $subarea_id, 'Subarea', 'Subarea.id', array(
			'cache' => __('Subarea.%s', $subarea_id),
			'cacheConfig' => 'subareas',
		));

		return $value;
	}

	function getMergeList ( $data ) {
		if( !empty($data[0]) ) {
			foreach ($data as $key => $value) {
				$value = $this->getDataMerge($value);
				$data[$key] = $value;
			}
	    } else if( !empty($data['UserCompanyEbrochure']) ) {
			$data = $this->getDataMerge($data);
	    }

    	return $data;
	}

	function sendMail($data, $data_ebrosur, $data_company){
		$result = array();
		if(!empty($data)){

			$this->set($data);

			if($this->validates($data)){
				$user['User'] = Configure::read('User.data');
				$agent_name = !empty($user['User']['full_name']) ? $user['User']['full_name'] : '';
				$company_name = !empty($data_company['UserCompany']['name']) ? sprintf(' | %s', $data_company['UserCompany']['name']) : '';

				$result = array(
					'msg' => __('Selamat! Anda telah berhasil mengirim eBrosur.'),
					'status' => 'success',
				);

				$to_email = explode(',', $data['UserCompanyEbrochure']['to_email']);

				$subject = __('Anda mendapatkan eBrosur dari %s%s', $agent_name, $company_name);
				if(is_array($to_email)){
					foreach ($to_email as $key => $email) {
						if(!empty($email)){
							$result['SendEmail'][] = array(
		                    	'to_name' => null,
		                    	'to_email' => trim($email),
		                    	'subject' => $subject,
		                    	'template' => 'send_ebrosur',
		                    	'data' => array(
		                    		'data_ebrosur' => $data_ebrosur,
		                    		'data_agent' => $user
		                    	),
		                	);
						}
					}
				}else{
					$result['SendEmail'][] = array(
	                	'to_name' => null,
	                	'to_email' => trim($to_email),
	                	'subject' => $subject,
	                	'template' => 'send_ebrosur',
	                	'data' => array(
                    		'data_ebrosur' => $data_ebrosur,
                    		'data_agent' => $user
                    	),
	            	);
				}
			}else{
				$result = array(
					'msg' => __('Gagal mengirim eBrosur.'),
					'status' => 'error',
				);

				$result['validationErrors'] = $this->validationErrors;
			}
		}

		return $result;
	}

	function getEbrosurRequest($find, $conditions = array(), $elements = array()){
		$result = array();
		
		if(!empty($conditions)){
			$mine = isset($elements['mine'])?$elements['mine']:false;
	    	$company = isset($elements['company'])?$elements['company']:false;
	    	$status = isset($elements['status'])?$elements['status']:'active';
	    	$limit = isset($elements['limit'])?$elements['limit']:5;

			$this->bindModel(array(
	            'hasOne' => array(
	                'PropertyAsset' => array(
	                    'className' => 'PropertyAsset',
	                    'foreignKey' => false,
	                    'conditions' => array(
	                        'PropertyAsset.property_id = UserCompanyEbrochure.property_id',
	                    ),
	                ),
	            )
	        ), false);

	        $contain = array(
	        	'Property'
	        );
	        if(!empty($conditions['property_asset_condition'])){
	        	$conditions['ebrosur_condition'] = array_merge($conditions['ebrosur_condition'], $conditions['property_asset_condition']);
	        	$contain[] = 'PropertyAsset';
	        }

	        if(!empty($conditions['ebrosur_condition']['UserCompanyEbrochure.region_id'])){
	        	$contain[] = 'Region';
	        }
	        if(!empty($conditions['ebrosur_condition']['UserCompanyEbrochure.city_id'])){
	        	$contain[] = 'City';
	        }
	        if(!empty($conditions['ebrosur_condition']['UserCompanyEbrochure.subarea_id'])){
	        	$contain[] = 'Subarea';
	        }

	        $options = array(
		        'conditions' => !empty($conditions['ebrosur_condition']) ? $conditions['ebrosur_condition'] : array(),
		        'contain' => $contain,
		        'limit' => $limit,
	        );

	        if(!empty($conditions['order'])){
	        	$options['order'] = $conditions['order'];
	        }

	        $options['order']['UserCompanyEbrochure.created'] = 'DESC';

			$result = $this->getData($find, $options, array(
	        	'status' => $status,
	            'company' => $company,
	            'mine' => $mine
	        ));
	    }

        return $result;
	}

	function removeValidate () {
        $this->validator()
        ->remove('agent_email')
        ->remove('property_type_id')
        ->remove('property_title')
        ->remove('property_price')
        ->remove('region_id')
        ->remove('city_id')
        ->remove('subarea_id')
        ->remove('description')
        ->remove('property_media_id')
        ->remove('filename')
        ->remove('to_email');
    }

	function _callPrincipleCount( $principle_id = false, $status = 'active', $params = null ){
		if( !empty($principle_id) ) {
			$date_from = Common::hashEmptyField($params, 'named.date_from');
			$date_to   = Common::hashEmptyField($params, 'named.date_to');

			$default_options = array(
				'conditions' => array(
					'UserCompanyEbrochure.principle_id' => $principle_id,
				),
				'order' => false,
			);

			if (!empty($date_from) && !empty($date_to)) {
				$default_options['conditions']['DATE_FORMAT(UserCompanyEbrochure.created, \'%Y-%m-%d\') >='] = $date_from;
				$default_options['conditions']['DATE_FORMAT(UserCompanyEbrochure.created, \'%Y-%m-%d\') <='] = $date_to;

			}
			
			$result = $this->getData('count', $default_options, array(
				'force_non_company' => true,
				'is_sales' => true,
				'mine' => false,
				'status' => $status,
			));
			// debug($result);die();
		
		} else {
			$result = false;
		}

		return $result;

	}

	function _callAgentCount( $user_id = false, $status = 'active', $params = null ){
        $options = $this->_callRefineParams($params, array(
        	'conditions' => array(
    			'UserCompanyEbrochure.user_id' => $user_id,
			),
			'order' => false,
		));

		return $this->getData('count', $options, array(
			'status' => $status,
			'company' => false,
			'admin' => true,
		));
	}

	public function prepareNotification($ebrochureData = array(), $action = 'create'){
		$companyData	= Configure::read('Config.Company.data');
		$isEbrochure	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_brochure');
		$isOpenListing	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_open_listing');

		$ebrochureData	= (array) $ebrochureData;
		$notifications	= array();

		if($ebrochureData && $isEbrochure && $isOpenListing){
			$ebrochureData = $this->getMergeList($ebrochureData, array(
				'contain' => array('Property', 'User'), 
			));

			$ebrochureData = $this->User->getMergeList($ebrochureData, array(
				'contain' => array('UserProfile'), 
			));

			$ebrosurUserID		= Common::hashEmptyField($ebrochureData, 'User.id');
			$ebrosurUserName	= Common::hashEmptyField($ebrochureData, 'User.full_name');
			$ebrosurUserEmail	= Common::hashEmptyField($ebrochureData, 'User.email');
			$propertyID			= Common::hashEmptyField($ebrochureData, 'UserCompanyEbrochure.property_id');

			$this->Property = empty($this->Property->alias) ? ClassRegistry::init('Property') : $this->Property;

			$propertyData = $this->Property->find('first', array(
				'contain'		=> array('User'), 
				'conditions'	=> array(
					'Property.id' => $propertyID, 
				), 
			));

			$propertyData = $this->User->getMergeList($propertyData, array(
				'contain' => array('UserProfile'), 
			));

			$propertyUserID		= Common::hashEmptyField($propertyData, 'User.id');
			$propertyUserName	= Common::hashEmptyField($propertyData, 'User.full_name');
			$propertyUserEmail	= Common::hashEmptyField($propertyData, 'User.email');
			$propertyMlsID		= Common::hashEmptyField($propertyData, 'Property.mls_id');

			if($propertyUserID && ($propertyUserID != $ebrosurUserID)){
				$socialMedias = array(
					'twitter'		=> 'Twitter', 
					'linkedin'		=> 'Linkedin', 
					'facebook'		=> 'Facebook', 
					'whatsapp'		=> 'Whats App', 
					'pinterest'		=> 'Pinterest', 
					'googleplus'	=> 'Google+', 
				);

				switch($action){
					case 'create' : 
						$title = 'membuat';
					break;
					case 'email' : 
						$title = 'membagikan melalui email';
					break;
					case 'print' : 
						$title = 'mencetak';
					break;
					default : 
						$title = 'membagikan';

						if(array_key_exists($action, $socialMedias)){
							$title = sprintf('%s melalui %s', $title, $socialMedias[$action]);
						}
					break;
				}

			//	kirim email ke owner property
				$title			= __('Agen %s telah %s Ebrosur dari salah satu listing properti Anda. (ID Properti %s)', $ebrosurUserName, $title, $propertyMlsID);
				$notifications	= array(
					'Notification' => array(
						'user_id'	=> $propertyUserID,
						'name'		=> $title,
						'link'		=> array(
							'admin'			=> true,
							'controller'	=> 'properties',
							'action'		=> 'index',
							'keyword'		=> $propertyMlsID, 
						),
					), 
					'SendEmail' => array(
						'to_email'	=> $propertyUserEmail,
						'to_name'	=> $propertyUserName,
						'subject'	=> $title,
						'data'		=> array(
							'ebrochure'	=> $ebrochureData, 
							'property'	=> $propertyData, 
						),
						'template'	=> 'ebrochure_notification',
					//	'debug'		=> 'view', 
					), 
				);
			}
		}

		return $notifications;
	}

	public function saveBuilderData($data = array()){
		$result = array(
			'status'	=> 'error', 
			'msg'		=> __('Data yang Anda masukkan tidak valid'), 
			'data'		=> $data, 
		);

		if($data){
			$recordID = Common::hashEmptyField($data, sprintf('%s.id', $this->alias), 0);
			$saveFlag = $this->saveAll($data, array(
				'validate' => 'only', 
			));

			if($saveFlag){
				$saveFlag	= $this->saveAll($data);
				$recordID	= $this->id;
				$data		= $this->read(null, $recordID);
			}

			$status		= $saveFlag ? 'success' : 'error';
			$message	= __('%s menyimpan data', $saveFlag ? 'Berhasil' : 'Gagal');
			$result		= array(
				'status'	=> $status,
				'msg'		=> $message,
				'data'		=> $data, 
				'id'		=> $recordID,
				'Log'		=> array(
					'activity'		=> $message,
					'document_id'	=> $recordID,
				),
			);

			if(empty($saveFlag)){
				$result['validationErrors'] = $this->validationErrors;
			} else if( empty($data['UserCompanyEbrochure']['name']) ) {
				$is_admin = Configure::read('User.admin');
				$user_id = Common::hashEmptyField($data, 'UserCompanyEbrochure.user_id');

				if( !empty($user_id) ) {
					$data = $this->Property->User->getMerge($data, $user_id);
					$user_code = Common::hashEmptyField($data, 'User.code');
				} else {
					$user_code = '';
				}

				$this->virtualFields = array('last_name' => 'RIGHT(UserCompanyEbrochure.name, 4)');
				$checkLastData = $this->getData('first', array(
					'conditions' => array(
						'UserCompanyEbrochure.user_id' => $user_id,
						'UserCompanyEbrochure.name LIKE' => '%E-Brochure #'.$user_code.'%',
					),
					'order' => array(
						'UserCompanyEbrochure.id' => 'DESC',
					),
				), array(
					'mine' => !empty($is_admin)?false:true,
				));

				if( !empty($checkLastData) ) {
					$last_number = Common::hashEmptyField($checkLastData, 'UserCompanyEbrochure.last_name');
					$last_number = intval($last_number) + 1;
        			$last_number = str_pad($last_number, 4,'0',STR_PAD_LEFT);

					$label_name = __('E-Brochure #%s%s', $user_code, $last_number);
				} else {
					$label_name = __('E-Brochure #%s0001', $user_code);
				}

				$this->saveAll(array(
					'UserCompanyEbrochure' => array(
						'id' => $recordID,
						'name' => $label_name,
					),
				));
			}
		}

		return $result;
	}
}
?>