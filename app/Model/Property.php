<?php
class Property extends AppModel {
//	untuk clear cache
	var $name = 'Property';

	var $badword = array(
		'dijual', 'jual', 'disewakan', 'disewa', 'sewa', 'dikontrakan', 'kontrakan', 'kontrak', ' di ', 'murahan', 'murah', 'harga'
	);

	var $validate = array(
		'mls_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'ID Properti harap diisi',
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'on' => 'create',
				'message' => 'ID Properti telah terdaftar',
			),
		),
		'agent_email' => array(
			'validateAgenEmail' => array(
				'rule' => array('validateAgenEmail'),
				'message' => 'Mohon masukkan email agen',
			),
			'validateExistEmail' => array(
				'rule' => array('validateExistEmail'),
				'message' => 'Agen tidak terdaftar. Silahkan masukkan Agen member Perusahaan Anda',
			),
			'validateUnderExist' => array(
				'rule' => array('validateUnderExist'),
				'message' => 'Agen tidak terdaftar. Silahkan masukkan Agen divisi bawahan Anda',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
				'allowEmpty' => true,
			),
		),
		'client_email' => array(
			'validateClientData' => array(
				'rule' => array('validateClientData', 'client_email'),
				'message' => 'Mohon masukkan Email',
			),
			'email' => array(
				'rule' => array('email'),
				'allowEmpty' => true,
				'message' => 'Format Email tidak valid',
			),
		),
		'client_name' => array(
			'validateClientData' => array(
				'rule' => array('validateClientData', 'client_name'),
				'message' => 'Mohon masukkan Nama',
			),
		),
		'client_hp' => array(
			'validateClientData' => array(
				'rule' => array('validateClientData', 'client_hp'),
				'message' => 'Mohon masukkan No. HP',
			),
			'notMatch' => array(
				'rule' => array('validatePhoneNumber'),
				'allowEmpty' => true,
				'message' => 'Format No. HP e.g. +6281234567 or 0812345678'
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'allowEmpty' => true,
				'message' => 'Minimal 6 digit',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 20),
				'allowEmpty' => true,
				'message' => 'Maksimal 20 digit',
			),
		),
		'property_action_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih status properti, dijual atau disewakan',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih status properti, dijual atau disewakan',
			),
		),
		'property_type_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih jenis properti',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih jenis properti',
			),
		),
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan kalimat promosi properti',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 60),
				'message' => 'Mohon masukkan kalimat promosi maksimal 60 karakter',
				'allowEmpty' => true,
			),
			'validateTitleProperty' => array(
				'rule' => array('validateTitleProperty'),
				'message' => 'kata-kata seperti "dijual, jual, disewakan, disewa, sewa, dikontrakan, kontrakan, kontrak, di , murahan, murah, harga" tidak di perkenankan untuk diisi'
			)
		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan deskripsi singkat mengenai listing Anda',
			),
			'minLength' => array(
				'rule' => array('minLength', 30),
				'message' => 'Mohon masukkan deskripsi properti minimum 30 karakter',
				'allowEmpty' => true,
			),
		),
		'price' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan harga properti',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon masukkan angka untuk harga properti',
			),
			'notNumber' => array(
				'rule' => array('isNumber'),
				'message' => 'Mohon masukkan harga properti lebih besar dari 0',
			),
		),
		'certificate_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih jenis sertifikat properti Anda',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon pilih jenis sertifikat properti Anda',
			),
		),
		'commission' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan komisi agen',
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon masukkan angka untuk komisi agen',
			),
			'CommisionValidate' => array(
            	'rule' => array('CommisionValidate'),
                'message' => 'Mohon masukkan komisi terlebih dahulu',
            )
		),
		'bt' => array(
			'numeric' => array(
				'allowEmpty' => true,
				'rule' => array('numeric'),
				'message' => 'Mohon masukkan angka untuk BT properti',
			),
		),
		'others_certificate' => array(
			'validCertificate' => array(
				'rule' => array('validCertificate', 'others_certificate'),
				'message' => 'Mohon masukkan sertifikat lainnya',
			),
		),
		'co_broke_commision' => array(
            'CoBrokeCommisionValidate' => array(
                'rule' => array('CoBrokeCommisionValidate'),
                'message' => 'Mohon masukkan komisi Co-Broke',
            ),
            'numeric' => array(
				'allowEmpty' => true,
				'rule' => array('numeric'),
				'message' => 'Komisi harus berupa angka',
			),
        ),
		'photo' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon upload Foto Properti',
			),
        ), 
	);

	var $belongsTo = array(
		'PropertyAction' => array(
			'className' => 'PropertyAction',
			'foreignKey' => 'property_action_id',
		),
		'Certificate' => array(
			'className' => 'Certificate',
			'foreignKey' => 'certificate_id',
		),
		'PropertyType' => array(
			'className' => 'PropertyType',
			'foreignKey' => 'property_type_id',
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
		'PropertyStatusListing' => array(
			'className' => 'PropertyStatusListing',
			'foreignKey' => 'property_status_id',
		),
		'UserCompany' => array(
			'foreignKey' => 'company_id',
		),
	);

	var $hasOne = array(
		'PropertyAddress' => array(
			'className' => 'PropertyAddress',
			'foreignKey' => 'property_id',
			'order' => array(
				'PropertyAddress.id' => 'ASC',
			),
			'limit' => 1,
		),
		'PropertyAsset' => array(
			'className' => 'PropertyAsset',
			'foreignKey' => 'property_id',
		),
		'UserIntegratedSyncProperty' => array(
			'className' => 'UserIntegratedSyncProperty',
			'foreignKey' => 'property_id',
		),
		'PropertySold' => array(
			'className' => 'PropertySold',
			'foreignKey' => 'property_id',
			'conditions' => array(
				'PropertySold.status' => 1,
			),
		),
		'CoBrokeProperty' => array(
			'className' => 'CoBrokeProperty',
			'foreignKey' => 'property_id',
		),
		'UserActivedAgentDetail' => array(
			'className' => 'UserActivedAgentDetail',
			'foreignKey' => 'property_id',
			'conditions' => array(
				'type' => 'Property',
				'document_status' => 'assign',
			),
		),
	);

	var $hasMany = array(
		'PropertyMedias' => array(
			'className' => 'PropertyMedias',
			'foreignKey' => 'property_id',
		),
		'PropertyVideos' => array(
			'className' => 'PropertyVideos',
			'foreignKey' => 'property_id',
		),
		'PropertyFacility' => array(
			'className' => 'PropertyFacility',
			'foreignKey' => 'property_id',
		),
		'PropertyPointPlus' => array(
			'className' => 'PropertyPointPlus',
			'foreignKey' => 'property_id',
		),
		'PropertyDraft' => array(
			'className' => 'PropertyDraft',
			'foreignKey' => 'property_id',
		),
		'PropertyPrice' => array(
			'className' => 'PropertyPrice',
			'foreignKey' => 'property_id',
		),
		'PropertyView' => array(
			'className' => 'PropertyView',
			'foreignKey' => 'property_id',
		),
		'PropertyLead' => array(
			'className' => 'PropertyLead',
			'foreignKey' => 'property_id',
		),
		'Message' => array(
			'className' => 'Message',
			'foreignKey' => 'property_id',
		),
		'PropertyRevision' => array(
			'className' => 'PropertyRevision',
			'foreignKey' => 'property_id',
		),
		'PropertyNotification' => array(
			'className' => 'PropertyNotification',
			'foreignKey' => 'property_id',
		),
		'PageConfig' => array(
			'className' => 'PageConfig',
			'foreignKey' => 'page_id',
		),
		'PropertyLog' => array(
			'className' => 'PropertyLog',
			'foreignKey' => 'property_id',
		),
		'UserCompanyEbrochure' => array(
			'foreignKey' => 'property_id', 
		), 
	);

	// nilai yang akan dieksekusi ketika save
	function beforeSave($options = array()){
		// cetak created ke table
		$auth_id = Configure::read('User.id');
		$created_by = Common::hashEmptyField($this->data, 'Property.created_by');
		$company_id = Common::hashEmptyField($this->data, 'Property.company_id');

		$id = $this->id;
		$id = Common::hashEmptyField($this->data, 'Property.id', $id);

		if(empty($created_by)){
			$this->data = Hash::insert($this->data, 'Property.created_by', $auth_id);
		}

		if( empty($id) ) {
			$principle_id = Configure::read('Principle.id');
			$this->data = Hash::insert($this->data, 'Property.principle_id', $principle_id);

			if( empty($company_id) ) {
				$company_id = Configure::read('Config.Company.data.UserCompany.id');
				$this->data = Hash::insert($this->data, 'Property.company_id', $company_id);
			}
		}
	}

	function validateTitleProperty($data){
		$badword = $this->badword;
		
		$result = true;
		if(0 < count(array_intersect(array_map('strtolower', explode(' ', $data['title'])), $badword))){
		  	$result = false;
		}

		$pos_string = strstr($data['title'], ' di ');
		if(!empty($pos_string)){
			$result = false;
		}

		return $result;
	}

	function CoBrokeCommisionValidate($data){
        $global_data = $this->data;
        $result = true;

        $type_price_co_broke_commision  = Common::hashEmptyField($global_data, 'Property.type_price_co_broke_commision');
        $co_broke_commision = Common::hashEmptyField($data, 'co_broke_commision');
        $is_cobroke 		= Common::hashEmptyField($global_data, 'Property.is_cobroke');
        
        if(!empty($is_cobroke) && empty($co_broke_commision)){
            $result = false;
        }

        return $result;
    }

    function CommisionValidate($data){
        $global_data = $this->data;
        $result = true;

        if(isset($global_data['Property']['is_cobroke'])){
        	if(!empty($global_data['Property']['is_cobroke']) && empty($data['commission'])){
	        	$result = false;
	        }
        }

        return $result;
    }

	public function afterSave($created, $options = array()){
		$dataCompany = Configure::read('Config.Company.data');
		$authUser = Configure::read('User.data');

		$ids = Common::hashEmptyField($options, 'id');

		$company_id = Common::hashEmptyField($dataCompany, 'UserCompany.id');
		$parent_id = Common::hashEmptyField($dataCompany, 'UserCompany.user_id');
		$user_id = Common::hashEmptyField($authUser, 'id');
		$group_id = Common::hashEmptyField($authUser, 'group_id');

		$cacheGroups = array(
			'Properties.Home'	=> 'properties__home_', 
			'Properties.Find'	=> 'properties__find_', 
			'Properties.Detail'	=> 'market_trend', 
		);

		################### log action properti #########################

		$params = Configure::read('__Site.params');
		$action = Common::hashEmptyField($params, 'action');

		$slug  = false;
		switch ($action) {

			####### create, edit & delete
			case in_array($action, array('admin_easy_add', 'admin_add')):
				$slug = 'add';
				break;
			case 'admin_easy_preview':
				$slug = 'edit';
				break;
			case 'admin_delete':
				$slug = 'deleted';
				break;
			#########################

			####### sold & unsold
			case 'admin_sold':
				$slug = 'sold';
				break;
			case 'admin_unsold':
				$slug = 'unsold';
				break;
			#########################

			####### active & inactive
			case 'admin_deactivate':
				$slug = 'inactive';
				break;
			case 'admin_activate':
				$slug = 'active';
				break;
			#########################

			####### Pratinjau approval by admin
			case 'admin_rejected':
				$slug = 'reject_update';
				break;
			case 'admin_approval':
				$slug = 'update';
				break;
			#########################

			####### Refrash
			case in_array($action, array('admin_refresh_all', 'admin_refresh')):
				$slug = 'refresh';
				break;
			#########################	
		}

		if(empty($this->id) && is_array($ids)){
			$ids = $ids;
		} else {
			$ids = array($this->id);
		}


		if($ids && is_array($ids)){
			foreach ($ids as $key => $id) {
				$dataSave = array(
					'PropertyLog' => array(
						'property_id' => $id,
						'parent_id' => $parent_id,
						'user_id' => $user_id,
						'group_id' => $group_id,
						'action' => $slug,
						'date' => date('Y-m-d'),
					),
				);

				$this->PropertyLog->create();
				$this->PropertyLog->save($dataSave);
			}
		}

		#################################################################

	//	clear "find" cache
		foreach($cacheGroups as $cacheGroup => $cacheNameInfix){
			$cachePath = CACHE.$cacheGroup;

			if($cacheNameInfix == 'market_trend'){
				$wildCard = '*'.$cacheNameInfix.'*';
			}
			else{
				$wildCard = '*'.$cacheNameInfix.$company_id.'*';
			}
			
			$cleared = clearCache($wildCard, $cacheGroup, NULL);
		}

		if(isset($this->id) && $this->id){
			$propertyDetail	= $this->find('first', array(
				'conditions' => array(
					'Property.id' => $this->id,
				),
			));
			$mlsID = isset($propertyDetail['Property']['mls_id']) ? $propertyDetail['Property']['mls_id'] : NULL;
			$cacheConfig = 'properties_detail';
			$cacheName = sprintf('Properties.Detail.%s.%s', $company_id, $mlsID);

			Cache::delete($cacheName, $cacheConfig);
		}

  //   	$cacheName = __('Property.Populers.%s', $company_id);
		// Cache::delete($cacheName, 'default');

		// Cache::delete(__('User.Populers.%s', $company_id), 'default');
		Cache::clearGroup('Properties.Home');
		Cache::clearGroup('Properties.Find');
		Cache::clearGroup('Properties.Detail');
	}
	
	function isNumber($data) {
		foreach ($data as $key => $value) {
			if( !is_numeric($value) ) {
				return false; 
			} else if( $value <= 0 ) {
				return false;
			} else {
				return true;
			}
		}
	}

	function validateAgenEmail($data){
		$result = true;

		$flagRule = (in_array(Configure::read('User.group_id'), Configure::read('__Site.Admin.Company.id')) || Configure::read('User.group_id') > 20);

		if( $flagRule && empty($data['agent_email'])){
			$result = false;
		}

		return $result;
	}

	function validateExistEmail () {
		$user_id = !empty($this->data['Property']['user_id'])?$this->data['Property']['user_id']:false;

		if( !empty($user_id) ) {
			return true;
		} else {
			return false;
		}
	}

	function validateUnderExist(){
		$group_id = Configure::read('User.group_id');
		$login_id = Configure::read('User.id');

		if($group_id > 20){
			$data = $this->data;
			$data_arr = $this->User->getUserParent($login_id);

			$agent_email = Common::hashEmptyField($data, 'Property.agent_email');
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

	function validCertificate( $data, $field ) {
		if( !empty($this->data['Property']['certificate_id']) && $this->data['Property']['certificate_id'] == '-1' && empty($data[$field]) ) {
			return false;
		} else {
			return true;
		}
	}

	function validateClientEmail ( $data ) {
		$dataCompany = Configure::read('Config.Company.data');
		$is_mandatory_client = !empty($dataCompany['UserCompanyConfig']['is_mandatory_client'])?$dataCompany['UserCompanyConfig']['is_mandatory_client']:false;

		if( !empty($is_mandatory_client) && empty($this->data['Property']['client_email']) ) {
			return false;
		} else {
			return true;
		}
	}

	function validateClientData ( $data, $field = false ) {
		$validFields = array('client_email', 'client_name', 'client_hp');

		if(in_array($field, $validFields)){
			$dataCompany	= Configure::read('Config.Company.data');
			$isMandatory	= Common::hashEmptyField($dataCompany, 'UserCompanyConfig.is_mandatory_client');
			$fieldValue		= array_shift($data);

			if($isMandatory){
				return !empty($fieldValue);
			}
			else{
				foreach($validFields as $key => $fieldName){
					$validFields[$fieldName] = Common::hashEmptyField($this->data, sprintf('Property.%s', $fieldName));

					unset($validFields[$key]);
				}

				$validFields = array_filter($validFields);

			//	return true kalo semua bener2 kosong atau, field saat ini sama dengan field yang keisi (jadi yang keluar error cuma field yang kurangnya)
				return empty($validFields) || Hash::check($validFields, $field);
			}
		}
		else{
			return __('Data tidak valid');
		}
	}

	function validatePhoneNumber($data) {
		if(!empty($data['client_hp'])) {
			$phoneNumber = $data['client_hp'];

			if (preg_match('/^[0-9]{1,}$/', $phoneNumber)==1 
				|| ( substr($phoneNumber, 0,1)=="+" 
				&& preg_match('/^[0-9]{1,}$/', substr($phoneNumber, 1,strlen($phoneNumber)))==1 )) {
				return true;
			}
		}
		return false;
	}

	function _callStatusCondition ( $status, $restrict_type = 'restrict', $data = false ) {
		$statusConditions = array();

		switch ($status) {
			case 'active':
				$statusConditions = array(
					'Property.active' => 1,
					'Property.status' => 1,
					'Property.sold' => 0,
					'Property.published' => 1,
					'Property.deleted' => 0,
					'Property.inactive' => 0
				);
				break;
			
			case 'all':
				$statusConditions = array(
					'Property.deleted'=> 0,
				);
				break;
			
			case 'pending':
				$statusConditions = array(
					'Property.status'=> 1,
					'Property.active'=> 0,
					'Property.sold'=> 0,
					'Property.deleted'=> 0,
					'Property.published'=> 1,
					'Property.inactive' => 0
				);
				break;
			
			case 'update':
				$statusConditions = array(
					'Property.in_update'=> 1,
					'Property.status'=> 1,
					'Property.active'=> 1,
					'Property.sold'=> 0,
					'Property.deleted'=> 0,
					'Property.inactive'=> 0,
					'Property.published' => 1,
				);
				break;
			
			case 'sold':
				$statusConditions = array(
					'Property.sold'=> 1,
					'Property.deleted'=> 0,
				);
				break;
			
			case 'inactive':
				$statusConditions = array(
					'Property.sold'=> 0,
					'Property.status'=> 0,
					'Property.deleted'=> 0,
				);
				break;
			
			case 'unpublished':
				$statusConditions = array(
					'Property.published'=> 0,
					'Property.deleted'=> 0,
				);
				break;
			case 'active-or-sold':
				$statusConditions = array(
					'Property.status' => 1,
					'OR' => array(
						'Property.active' => 1,
						'Property.sold' => 1,
					),
					'Property.published' => 1,
					'Property.deleted' => 0,
					'Property.inactive' => 0
				);
				break;
			case 'pending-or-update':
				$statusConditions = array(
					'Property.status'=> 1,
					'Property.sold'=> 0,
					'Property.deleted'=> 0,
					'Property.published'=> 1,
					'OR' => array(
						array(
							'Property.active'=> 0,
						),
						array(
							'Property.in_update'=> 1,
							'Property.active'=> 1,
							'Property.inactive'=> 0,
						),
					),
				);
				break;
			case 'active-pending-sold':
				$statusConditions = array(
					'Property.published' => 1,
					'Property.deleted' => 0,
				);
				$statusConditions = Common::_callPropertyRestrict($statusConditions, $restrict_type);
				
				break;
			case 'active-pending':
				$statusConditions = array(
					'Property.status' => 1,
					'Property.published' => 1,
					'Property.deleted' => 0,
					'Property.sold' => 0,
					'Property.inactive' => 0
				);
				$statusConditions = Common::_callPropertyRestrict($statusConditions, $restrict_type);

				break;
			case 'premium':
				$statusConditions = array(
					'Property.status' => 1,
					'Property.published' => 1,
					'Property.deleted' => 0,
					'Property.sold' => 0,
					'Property.featured' => 1,
					'Property.inactive' => 0
				);
				break;
			case 'incoming-inactive':
				$incoming_expired_day = Configure::read('__Site.config_expired_listing') - 7;
				$statusConditions = array(
					'Property.status' => 1,
					'Property.published' => 1,
					'Property.deleted' => 0,
					'Property.sold' => 0,
					'Property.inactive' => 0,
					'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), DATE_FORMAT(Property.change_date, \'%Y-%m-%d\'))' => $incoming_expired_day,
				);
				break;
			case 'incoming-rent':
				$incoming_expired_day = Configure::read('__Site.config_expired_rent');
				$statusConditions = array(
					'Property.status' => 1,
					'Property.published' => 1,
					'Property.deleted' => 0,
					'Property.inactive' => 0,
					'Property.property_action_id' => 2,
					'DATEDIFF(DATE_FORMAT(PropertySold.end_date, \'%Y-%m-%d\'), DATE_FORMAT(NOW(), \'%Y-%m-%d\'))' => $incoming_expired_day,
				);
				break;
			case 'deleted':
				$statusConditions = array(
					'Property.deleted' => 1
				);
				break;
			case 'cobroke':
				$statusConditions = array(
					'Property.is_cobroke' => 1
				);
				break;
			case 'assign':
				$document_id = $this->filterEmptyField($data, 'named', 'document_id', false, array(
		        	'addslashes' => true,
		    	));
		    	$property_lists = $this->UserActivedAgentDetail->getData('list', array(
		    		'conditions' => array(
		    			'UserActivedAgentDetail.user_actived_agent_id' => $document_id,
		    			'UserActivedAgentDetail.type' => 'Property',
		    		),
		    		'fields' => array(
		    			'UserActivedAgentDetail.document_id', 'UserActivedAgentDetail.document_id'
		    		),
		    	));

		    	if($property_lists){
			    	$statusConditions = array(
						'Property.id' => $property_lists,
					);
		    	}

				break;
		}

		return $statusConditions;
	}

	function getData( $find, $options = false, $elements = array() ){
		$companyData	= Configure::read('Config.Company.data'); 
		$isAdmin		= Configure::read('User.admin');
		$isCompanyAdmin	= Configure::read('User.companyAdmin');
		$authGroupID	= Configure::read('User.data.group_id');
		$companyID		= Common::hashEmptyField($companyData, 'UserCompany.id', 0);

		$user_login_id = Configure::read('User.id');

		$status = isset($elements['status'])?$elements['status']:'active-pending';
		$mine = isset($elements['mine'])?$elements['mine']:false;
		$admin_mine = isset($elements['admin_mine'])?$elements['admin_mine']:false;
		$sold_mine = isset($elements['sold_mine'])?$elements['sold_mine']:false;
		$company = isset($elements['company'])?$elements['company']:true;
		$premium = isset($elements['premium'])?$elements['premium']:false;
		$action_type = isset($elements['action_type'])?$elements['action_type']:'all';
		$restrict_type = isset($elements['restrict_type'])?$elements['restrict_type']:'restrict';
		$rest = isset($elements['rest'])?$elements['rest']:true;

		$restrict_api = isset($elements['restrict_api'])?$elements['restrict_api']:true;

		$dataCompany = Configure::read('Config.Company.data');
		$is_restrict_approval_property = isset($dataCompany['UserCompanyConfig']['is_restrict_approval_property']) ? $dataCompany['UserCompanyConfig']['is_restrict_approval_property'] : false;

	//	personal page
		if($authGroupID == 1){
			$mine = true;
		}

		$default_options = array(
			'conditions'=> array(
				'Property.principle_id NOT' => NULL,
				'Property.user_id <>' => 0,
			),
			'order' => array(
				'Property.sold' => 'ASC',
				'Property.featured' => 'DESC',
				'Property.change_date' => 'DESC',
				'Property.id' => 'DESC',
			),
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);

		$statusConditions = $this->_callStatusCondition($status, $restrict_type);

		switch ($status) {
			case 'incoming-rent':
				$default_options['contain'][] = 'PropertySold';
				break;
		}

		switch ($action_type) {
			case 'sell':
				$statusConditions['Property.property_action_id'] = 1;
				break;
			
			case 'rent':
				$statusConditions['Property.property_action_id'] = 2;
				break;
		}

		if( !empty($sold_mine) ) {
			$this->unbindModel(
				array('hasOne' => array('PropertySold'))
			);

			$this->bindModel(array(
				'hasOne' => array(
					'PropertySold' => array(
						'className' => 'PropertySold',
						'foreignKey' => 'property_id',
						'conditions' => array(
							'PropertySold.sold_by_id = Property.user_id',
							'PropertySold.status' => 1,
						),
					),
				)
			), false);

			$conditionsSoldMine = array(
				'PropertySold.id <>' => NULL,
			);
			$default_options['contain'][] = 'PropertySold';

			if( !empty($statusConditions) ) {
				$default_options['conditions']['OR'][] = $statusConditions;
				$default_options['conditions']['OR'] = array(
					$statusConditions,
					$conditionsSoldMine,
				);
			} else {
				$default_options['conditions'] = $conditionsSoldMine;
			}
		} else if( !empty($statusConditions) ) {
			$default_options['conditions'] = array_merge($default_options['conditions'], $statusConditions);
		}

		if( !empty($mine) || !empty($admin_mine) ) {    
			if( !empty($mine) && !$isCompanyAdmin) {
				$default_options['conditions']['Property.user_id'] = $user_login_id;
			} else if( !empty($admin_mine) ) {
				$data_arr = $this->User->getUserParent($user_login_id);

				$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

				if( !empty($isCompanyAdmin) || empty($is_sales) ) {
					$company = true;
					$mine = false;
				} else {
					$company = false;
					$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
					
					$default_options['conditions']['Property.user_id'] = $user_ids;
				}
			}
		}

		if($company){
			$default_options['conditions']['COALESCE(Property.company_id, 0)'] = $companyID;
		}

		if( !empty($company) && empty($mine) ) {
            $parent_id = Configure::read('Principle.id');

			$skip_is_sales = isset($elements['skip_is_sales'])?$elements['skip_is_sales']:false;

            $group_id	= $this->filterEmptyField($companyData, 'User', 'group_id');
            
        	if( $group_id == 4 ) {
				$principle_id = $this->User->getAgents($parent_id, true, 'list', false, array(
					'role' => 'principle',
					'skip_is_sales' => $skip_is_sales,
				));

				$default_options['conditions']['Property.principle_id'] = $principle_id;
        	} else {
				$default_options['conditions']['Property.principle_id'] = $parent_id;
        	}
		}

		if( !empty($rest) && $restrict_api ) {
			$default_options = $this->_callFieldForAPI($find, $default_options);
		}
        
        return $this->merge_options($default_options, $options, $find);
	}

	function _callFieldForAPI ( $find, $options ) {
		if( !in_array($find, array( 'list', 'count' )) ) {
			$rest_api = Configure::read('Rest.token');

			if( !empty($rest_api) ) {
				$options['fields'] = array(
				  'Property.id',
				  'Property.user_id',
				  'Property.client_id',
				  'Property.mls_id',
				  'Property.property_action_id',
				  'Property.property_type_id',
				  'Property.currency_id',
				  'Property.certificate_id',
				  'Property.contract_date',
				  'Property.others_certificate',
				  'Property.title',
				  'Property.keyword',
				  'Property.description',
				  'Property.photo',
				  'Property.period_id',
				  'Property.price',
				  'Property.price_measure',
				  'Property.commission',
				  'Property.bt',
				  'Property.kolisting_koselling',
				  'Property.featured',
				  'Property.published',
				  'Property.publish_date',
				  'Property.change_date',
				  'Property.refresh_date',
				  'Property.inactive_date',
				  'Property.in_update',
				  'Property.sold',
				  'Property.deleted',
				  'Property.inactive',
				  'Property.active',
				  'Property.status',
				  'Property.session_id',
				  'Property.modified',
				  'Property.created',
				);
			}
		}

		return $options;
	}

	function getProperty ( $find = 'first', $user_id = false, $property_id = false, $status = 'active', $limit = false ) {
		$options = array(
			'conditions' => array(),
		);

		if( !empty($user_id) ) {
			$options['conditions']['Property.user_id'] = $user_id;
		}
		if( !empty($property_id) ) {
			$options['conditions']['Property.id'] = $property_id;
		}
		if( !empty($limit) ) {
			$options['limit'] = $limit;
		}

		$authGroupID	= Configure::read('User.data.group_id');
		$elements		= array(
			'status'		=> $status,
			'admin_mine'	=> Configure::read('User.admin'), 
		);

		if($authGroupID == 1){
			$elements = array_merge($elements, array(
				'mine'			=> true,
				'company'		=> false,
				'skip_is_sales'	=> true,
			));
		}

		return $this->getData($find, $options, $elements);
	}

	function inUpdateChange($property_id, $change_to = true, $by_pass = false){
		$approval = Configure::read('Config.Approval.Property');
		$flag = true;

		if( !empty($approval) || $by_pass ) {
			$default = array(
				'in_update' => $change_to
			);
			
			if(!empty($change_to)){
				$default['status'] = true;
			}

			if( !empty($property_id) ) {
				$property = $this->find('first', array(
					'conditions' => array(
						'Property.id' => $property_id
					),
		    		'fields' => array(
		    			'Property.id',
		    			'Property.active',
		    			'Property.status',
		    		),
				));
			} else {
				$property = array();
			}

			$active = $this->filterEmptyField($property, 'Property', 'active');
			$status = $this->filterEmptyField($property, 'Property', 'status');

			if(!empty($active) || !empty($status)){
				$flag = $this->updateAll(
					$default, 
					array(
						'Property.id' => $property_id,
					)
				);
			}
		}

		return !empty($flag);
	}

	function doBasic( $data, $value = false, $validate = false, $id = false, $save_session = true ) {
		$result	 = false;
		$data = Hash::remove($data, 'is_easy_mode');

		if ( !empty($data) ) {
			$group_id   = Configure::read('User.group_id');
			$is_admin   = Configure::read('User.admin');
			$approval   = Configure::read('Config.Approval.Property');
			$mls_id	 = !empty($data['Property']['mls_id'])?$data['Property']['mls_id']:false;
			$is_api	 = !empty($data['is_api']) ? true : false;

			$pageConfig	 = !empty($data['PageConfig']) ? $data['PageConfig'] : false;
			$syncProperty = !empty($data['UserIntegratedSyncProperty']) ? $data['UserIntegratedSyncProperty'] : false;
			$is_easy_mode = !empty($data['is_easy_mode']);

			if( empty($validate) ) {
				if( !empty($id) ) {
					$this->id = $id;
				} else {
					$this->create();

					if( !empty($is_admin) || empty($approval) ) {
						$data['Property']['active'] = 1;
					}
				}
			}

			$data['Property']['change_date'] = date('Y-m-d H:i:s');

			if( empty($data['Property']['user_id']) ) {
				if( in_array($group_id, Configure::read('__Site.Admin.Company.id')) || in_array($group_id, Configure::read('__Site.Admin.List.id')) || $group_id > 20  ){
					$agent_email = !empty($data['Property']['agent_email'])?$data['Property']['agent_email']:false;

					if(!empty($agent_email)){
						$user_data = $this->User->getData('first', array(
							'conditions' => array(
								'User.email' => $agent_email
							),
							'fields' => array(
								'User.id',
							),
						), array(
							'company' => true,
							'admin' => true,
							'role' => 'agent',
						));

						if(!empty($user_data['User']['id'])){
							$data['Property']['user_id'] = $user_data['User']['id'];
						} else {
							$data['Property']['user_id'] = '';
						}
					}else if(!$is_api){
						$data['Property']['user_id'] = Configure::read('User.id');	
					}
				} else if(!$is_api){
					$data['Property']['user_id'] = Configure::read('User.id');	
				} else if($is_api){
					$data['Property']['user_id'] = !empty($data['Property']['user_id']) ? $data['Property']['user_id'] : false;	
				}
			}

			if($is_api){
				$this->validator()->remove('property_action_id')
					->remove('property_type_id')
					->remove('title')
					->remove('description')
					->remove('price')
					->remove('certificate_id')
					->remove('commission')
					->remove('bt');

				$this->removeValidate();
			}

			if($is_easy_mode){
				$this->validator()->remove('title')->remove('description');
				$this->removeValidate();
			}

			if( !empty($data['Property']['price']) ) {
				$data['Property']['price'] = str_replace(',', '', $data['Property']['price']);
				$data['Property']['price'] = trim($data['Property']['price']);
			}

			$this->set($data);

			if( $this->validates() ) {
				$flagSave = true;
				$data = $this->User->addClient($data);
				$client_id = !empty($data['Property']['client_id'])?$data['Property']['client_id']:false;

				if( !empty($validate) ) {
					if( !empty($save_session) ) {
						$sessionName = Configure::read('__Site.Property.SessionName');
						CakeSession::write(sprintf($sessionName, 'Basic'), $data);
					}
				} else {
					$flagSave = $this->save($data);
					$id = $this->id;

					if( !empty($flagSave) && $group_id == 2 ) {
						if(empty($mls_id) && !empty($id)){
							$data_property = $this->findById($id);

							$mls_id = Common::hashEmptyField($data_property, 'Property.mls_id');
						}

						if(!empty($mls_id)){
							$notifMsg = sprintf(__('Penambahan Properti dengan ID %s pada tanggal %s, harap lakukan peninjauan'), $mls_id, date('d M Y'));
						}else{
							$notifMsg = sprintf(__('Penambahan Properti pada tanggal %s, harap lakukan peninjauan'), date('d M Y'));
						}
						
						$this->User->Notification->doSave(array(
							'Notification' => array(
								'user_id' => 'admin_company',
								'name' => $notifMsg,
								'link' => array(
									'controller' => 'properties',
									'action' => 'index',
									'keyword' => $mls_id,
									'admin' => true,
								),
							),
						));
					}
				}

				if( !empty($flagSave) ) {
					$msg = __('Berhasil menyimpan informasi dasar properti Anda');
					$this->PageConfig->doSave($pageConfig, $id, 'property');

					if(empty($is_easy_mode)){
					//	sync rumah 123 untuk property yang lengkap datanya
					//	easy mode skip sync
						$this->UserIntegratedSyncProperty->doSave($syncProperty, $id);
					}

					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'id' => $id,
						'client_id' => $client_id,
					);

					if( empty($validate) ) {
						$result['Log'] = array(
							'activity' => $msg,
							'document_id' => $id,
						);
					}
				} else {
					$msg = __('Gagal menyimpan properti Anda, mohon lengkapi semua data yang diperlukan');
					$result = array(
						'msg' => $msg,
						'status' => 'error',
						'Log' => array(
							'activity' => $msg,
							'document_id' => $id,
							'error' => 1,
						),
					);
				}
			} else {
				$validationErrors = array();

				if(!empty($this->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->validationErrors);
				}

				$result = array(
					'msg' => __('Gagal menyimpan properti Anda, mohon lengkapi semua data yang diperlukan'),
					'status' => 'error',
					'validationErrors' => $validationErrors
				);
			}
		} else if( !empty($value) ) {
			$value = $this->PageConfig->getMerge($value, $id, 'property');
			$value = $this->User->Property->getMergeList($value, array(
				'contain' => array(
					'UserIntegratedSyncProperty',
				),
			));
			$result['data'] = $value;
		}

		return $result;
	}

	function doToggle( $id = false, $fieldName = 'status', $msg = 'me-nonaktifkan properti', $value = 0 ) {
		$this->id = $id;
		$this->set($fieldName, $value);

		if( $this->save() ) {
			$this->_callRemoveCachePopuler();
			
			$msg = sprintf(__('Berhasil %s'), $msg);
			$result = array(
				'msg' => $msg,
				'status' => 'success',
				'Log' => array(
					'activity' => $msg,
					'document_id' => $id,
				),
			);
		} else {
			$msg = sprintf(__('Gagal %s'), $msg);
			$result = array(
				'msg' => $msg,
				'status' => 'error',
				'Log' => array(
					'activity' => $msg,
					'document_id' => $id,
					'error' => 1,
				),
			);
		}

		return $result;
	}

	function doRefresh( $id) {
		$now_date = date('Y-m-d H:i:s');
		$msg = __('melakukan refresh properti');

		$options = array(
			'Property.refresh_date' => "'".$now_date."'",
			'Property.change_date' => "'".$now_date."'",
			'Property.modified' => "'".$now_date."'",
		);

		$flag = $this->updateAll($options, array(
			'Property.id' => $id,
		));
		
		if( is_array($id) ) {
			$id = false;
		}

		if( $flag ) {
			$this->afterSave($flag, array_merge($options, array(
				'id' => !is_array($id) ? array($id) : $id,
			)));
			$msg = sprintf(__('Berhasil %s. Refresh properti hanya bisa dilakukan sekali dalam sehari.'), $msg);
			$result = array(
				'msg' => $msg,
				'status' => 'success',
				'Log' => array(
					'activity' => $msg,
					'document_id' => $id,
				),
			);
		} else {
			$msg = sprintf(__('Gagal %s'), $msg);
			$result = array(
				'msg' => $msg,
				'status' => 'error',
				'Log' => array(
					'activity' => $msg,
					'document_id' => $id,
					'error' => 1,
				),
			);
		}

		return $result;
	}

	function doPremium( $id, $data_config = false, $data_agent_membership = false, $data_property = false ) {
		$data_user 	  = Configure::read('User.data');

		$limit_premium_property = Common::hashEmptyField($data_config, 'MembershipPackage.limit_premium_property');
		$limit_property 		= (int)$limit_premium_property;

		$is_admin = Configure::read('User.admin');
		$user_id = Common::hashEmptyField($data_property, 'Property.user_id');
		
		$default_options = array(
			'conditions' => array(
				'Property.featured' => 1,
				'Property.user_id'  => $user_id,
			)
		);

		$count_property = $this->getData('count', $default_options, array(
			'company' => false,
		));

		// if agent have own membership (kalo yang gak ngikut setingan company)
		if (!empty($data_agent_membership)) {
			$limit_prop     = Common::hashEmptyField($data_agent_membership, 'MembershipPackage.limit_premium_property');
			$limit_property = (int)$limit_prop;
		}

		$result = array(
			'msg' => __('Anda tidak bisa menjadikan properti ini premium dikarenakan sudah masuk batas premium listing Anda.'),
			'status' => 'error'
		);

		if( $count_property < $limit_property ) {
			$now_date = date('Y-m-d H:i:s');
			$msg = __('mengubah status Properti menjadi premium');

			$this->id = $id;
			$this->set('featured', 1);
			$this->set('featured_date', $now_date);
			$this->set('change_date', $now_date);

			if( $this->save() ) {
				$msg = sprintf(__('Berhasil %s'), $msg);
				$result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'document_id' => $id,
					),
				);
			} else {
				$msg = sprintf(__('Gagal %s'), $msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'document_id' => $id,
						'error' => 1,
					),
				);
			}
		}else{
			$result['Log'] = array(
				'activity' => $result['msg'],
				'document_id' => $id,
				'error' => 1,
			);
		}

		return $result;
	}
	
	public function doUnPremium($id, $config){
		$property = $this->getData('first', array('conditions' => array('Property.id' => $id, 'Property.featured' => 1)), array('admin_mine' => TRUE));
		if($property){
			$date	= date('Y-m-d H:i:s');
			$msg	= __('menghilangkan status Premium pada Properti.');

			$this->id = $id;
			$this->set('featured', 0);
			$this->set('featured_date', $date);
			$this->set('change_date', $date);

			if($this->save()){
				$msg	= sprintf(__('Berhasil %s'), $msg);
				$error	= 0;
			}
			else{
				$msg	= sprintf(__('Gagal %s'), $msg);
				$error	= 1;
			}
		}
		else{
			$msg	= __('Properti ini tidak memiliki status Premium.');
			$error	= 1;
			
		}

		$result	= array(
			'msg'		=> $msg, 
			'status'	=> $error ? 'error' : 'success', 
			'log'		=> array('activity' => $msg, 'document_id' => $id, 'error' => $error)
		);

		return $result;
	}

	function doUnsold( $id ) {
		$result = false;
		$default_msg = __('menghilangkan status terjual atau tersewa properti');

		$this->id = $id;
		$this->set('sold', 0);

		if( $this->save() ) {
			$this->PropertySold->updateAll(array(
				'PropertySold.status' => 0,
	    		'PropertySold.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'PropertySold.property_id' => $id,
			));

			$msg = sprintf(__('Berhasil %s'), $default_msg);

			$result = array(
				'msg' => $msg,
				'status' => 'success',
				'Log' => array(
					'activity' => $msg,
					'document_id' => $id,
					'error' => 1,
				),
			);
		} else {
			$msg = sprintf(__('Gagal %s, mohon lengkapi semua data yang diperlukan'), $default_msg);

			$result = array(
				'msg' => $msg,
				'status' => 'error',
				'Log' => array(
					'activity' => $msg,
					'document_id' => $id,
					'error' => 1,
				),
			);
		}

		return $result;
	}

	function generateMLSID( $rand_code, $initial = false ) {

		if( empty($initial) ) {
			$user = Configure::read('User.data');
			$initial = !empty($user['code'])?$user['code']:trim(substr($user['email'], 0, 5));
		}

		if( !empty($initial) ) {
			$initial = str_pad($initial, 5, '0', STR_PAD_RIGHT);
			$new_code = '';
			$flag = true;
			$last_number = 0;

			$this->virtualFields = array('last_mls_id' => 'RIGHT(Property.mls_id, 3)');
			$check = $this->find('first', array(
				'conditions'=> array(
					'RIGHT(Property.mls_id, 3) REGEXP \'^[0-9]+$\'',
					'Property.mls_id LIKE' => $initial.'%',
				),
				'order' => array(
					'Property.last_mls_id' => 'DESC',
				),
			));

			if( empty($check) ) {
				$mls_id = __('%s001', $initial);
			} else {
				$last_mls_id = Common::hashEmptyField($check, 'Property.last_mls_id');
            	$last_number = $last_mls_id = intval($last_mls_id+1);
        		$last_mls_id = str_pad($last_mls_id, 3,'0',STR_PAD_LEFT);
				$mls_id = __('%s%s', $initial, $last_mls_id);
			}

			$checkExisting = $this->find('first', array(
				'conditions'=> array(
					'Property.mls_id'=> $mls_id,
				),
			));

			if( !empty($checkExisting) || $last_number > 999 ) {
				$mls_id = false;

				while ($flag) {
					$str_code = strtoupper(implode('', $rand_code));
					$mls_id = sprintf('%s%s', $initial, $str_code);

					$check = $this->find('first', array(
						'conditions'=> array(
							'Property.mls_id'=> $mls_id,
						),
						'fields' => array(
							'Property.id'
						),
					));
					
					if( empty($check) ) {
						$flag = false;
					}
				}
			}

			return $mls_id;
		} else {
			return false;   
		}
	}

    function getMerge ( $data, $id ) {

        if( empty($data['Property']) && $id) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    'Property.id' => $id,
                ),
            ), array(
                'company' => false,
                'status' => false,
            ));

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}
		return $data;
	}

	function getMergeDefault( $data ) {
		$certificate_id = !empty($data['Property']['certificate_id'])?$data['Property']['certificate_id']:false;
		$type_id = !empty($data['Property']['property_type_id'])?$data['Property']['property_type_id']:false;
		$action_id = !empty($data['Property']['property_action_id'])?$data['Property']['property_action_id']:false;
		$currency_id = !empty($data['Property']['currency_id'])?$data['Property']['currency_id']:false;
		$period_id = !empty($data['Property']['period_id'])?$data['Property']['period_id']:false;

		if( !empty($type_id) && empty($data['PropertyType']) ) {
			$type = $this->PropertyType->getData('first', array(
				'conditions' => array(
					'PropertyType.id' => $type_id,
				),
				'cache' => __('PropertyType.%s', $type_id)
			));

			if( !empty($type) && is_array($data) ) {
				$data = array_merge($data, $type);
			}
		}

		if( !empty($action_id) && empty($data['PropertyAction']) ) {
			$action = $this->PropertyAction->getData('first', array(
				'conditions' => array(
					'PropertyAction.id' => $action_id,
				),
                'cache' => __('PropertyAction.%s', $action_id),
			));

			if( !empty($action) ) {
				$data = array_merge($data, $action);
			}
		}

		if( !empty($certificate_id) && empty($data['Certificate']) ) {
			$certificate = $this->Certificate->getData('first', array(
				'conditions' => array(
					'Certificate.id' => $certificate_id,
				),
                'cache' => __('Certificate.%s', $certificate_id),
			));

			if( !empty($certificate) ) {
				$data = array_merge($data, $certificate);
			}
		}

		if( !empty($currency_id) && empty($data['Currency']) ) {
			$currency = $this->Currency->getData('first', array(
				'conditions' => array(
					'Currency.id' => $currency_id,
				),
				'cache' => __('Currency.%s', $currency_id),
			));

			if( !empty($currency) ) {
				$data = array_merge($data, $currency);
			}
		}

		if( !empty($period_id) && empty($data['Period']) ) {
			$value = $this->Period->getData('first', array(
				'conditions' => array(
					'Period.id' => $period_id,
				),
				'cache' => __('Period.%s', $period_id),
			));

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}

	function _callDataMerge ( $value, $contains ) {
		$id = !empty($value['Property']['id'])?$value['Property']['id']:false;
		$user_id = !empty($value['Property']['user_id'])?$value['Property']['user_id']:false;
		$approved_by = !empty($value['Property']['approved_by'])?$value['Property']['approved_by']:false;
		$sold = !empty($value['Property']['sold'])?$value['Property']['sold']:false;
		$client_id = !empty($value['Property']['client_id'])?$value['Property']['client_id']:false;
		$category_id = !empty($value['Property']['property_status_id'])?$value['Property']['property_status_id']:false;
		
		if( !empty($contains) ) {
			 foreach ($contains as $contain) {
				if( $contain == 'PropertyMediasCount' ) {
					$value = $this->PropertyMedias->getMerge( $value, $id, 'count', 'active' );
				} else  if( $contain == 'MergeDefault' ) {
					$value = $this->getMergeDefault( $value );
				} else if( $contain == 'User' ) {
					$value = $this->$contain->getMerge( $value, $user_id );
				} else if( $contain == 'Approved' ) {
					$value = $this->User->getMerge( $value, $approved_by, false, 'Approved' );
				} else if( $contain == 'PropertyStatusListing' ) {
					$value = $this->User->Property->PropertyStatusListing->getMerge( $value, $category_id );
				} else if( $contain == 'Client' ) {
					$value = $this->User->UserClient->getMerge( $value, $client_id, Configure::read('Principle.id'), 'ClientProfile' );
				} else if( $contain == 'PageConfig' ) {
					$value = $this->PageConfig->getMerge($value, $id, 'property');
				} else if($contain == 'UserActivedAgentDetail'){
					$value = $this->UserActivedAgentDetail->getMerge($value, $id, 'UserActivedAgentDetail.document_id', 'first', array(
						'UserActivedAgentDetail.type' => 'Property',
                   	 	'UserActivedAgentDetail.document_status' => 'assign',
					));
					
					if(!empty($value['UserActivedAgentDetail'])){
						$agent_decilne_id = Common::hashEmptyField($value, 'UserActivedAgentDetail.agent_decilne_id');
						$value['UserActivedAgentDetail'] = $this->User->getMerge($value['UserActivedAgentDetail'], $agent_decilne_id);
					}
				} else {
					$value = $this->$contain->getMerge( $value, $id );
				}
			}
		}

		return $value;
	}

	public function getDataList($data, $options = false) {
		$contains = !empty($options['contain'])?$options['contain']:false;
		if( !empty($data) ) {
			if( !empty($data['Property']) ) {
				$data = $this->_callDataMerge( $data, $contains );
			} else {
				foreach ($data as $key => $value) {
					$data[$key] = $this->_callDataMerge( $value, $contains );
				}
			}
		}

		return $data;
	}

	function get_total_listing_per_agent( $parent_id = false, $elements = array(), $type = 'interval', $params = array(), $fieldName = 'Property.created'){
		$records = array();

		if(in_array($type, array('interval', 'range'))){
			$companyData	= Common::config('Config.Company.data', array());
			$ownerGroupID	= Common::hashEmptyField($companyData, 'User.group_id', 0);
			$isDirector		= Common::validateRole('director', $ownerGroupID);

			$elements	= (array) $elements;
			$params		= (array) $params;
			$options	= array(
				'conditions'	=> array(
					'User.status' => 1,
					'User.deleted' => 0,
				), 
				'contain'		=> array(
					'User',
				), 
				'group'			=> array('Property.user_id'), 
				'order'			=> array('Property.total_listing' => 'DESC'), 
				'limit'			=> 5, 
			);

			$elements = Hash::insert($elements, 'company', $isDirector);

			if(empty($isDirector)){
				$parent_id = $parent_id ?: Common::config('Principle.id');

				$options['conditions']['Property.principle_id'] = $parent_id;
			}

			if($type == 'range' && $fieldName && $params){
				if(isset($params['date_from']) && isset($params['date_to'])){
					$fieldName = sprintf('DATE_FORMAT(%s, "%Y-%m-%d")', $fieldName);

					$options['conditions'][$fieldName.' >='] = $params['date_from'];
					$options['conditions'][$fieldName.' <='] = $params['date_to'];
				}
			}

			$this->virtualFields = array('total_listing' => 'COUNT(Property.id)');

			$records = $this->getData('all', $options, $elements);
			$records = $this->getMergeList($records, array('contain' => array('User')));
		}

		return $records;
	}

	function _callPrinciplePropertyCount( $parent_id = null, $status = 'active-pending-sold', $params = null ){
		if( !empty($parent_id) ) {
			$date_from = Common::hashEmptyField($params, 'named.date_from');
			$date_to   = Common::hashEmptyField($params, 'named.date_to');

			$default_options = array(
				'conditions' => array(
					'OR' => array(
						'Property.principle_id' => $parent_id,
						'Property.user_id' => $parent_id,
					),
				),
				'order' => false,
			);

			if (!empty($date_from) && !empty($date_to)) {
				// haystack status prop search between in created field
				$status_prop = array('active-pending', 'inactive');

				// search property sold date in tabel propertysold
				if ($status == 'sold') {
					// $default_options['conditions']['PropertySold.sold_date BETWEEN ? and ?'] = array($date_from, $date_to);
					$default_options['conditions']['DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') >='] = $date_from;
					$default_options['conditions']['DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') <='] = $date_to;

					$default_options['contain'][] = 'PropertySold';

				} elseif (in_array($status, $status_prop)) {
					$default_options['conditions']['DATE_FORMAT(Property.created, \'%Y-%m-%d\') >='] = $date_from;
					$default_options['conditions']['DATE_FORMAT(Property.created, \'%Y-%m-%d\') <='] = $date_to;
				}

			}

			$result = $this->getData('count', $default_options, array(
				'company' => false,
				'status' => $status,
			));
			// debug($status);
			// debug($result);die();
		} else {
			$result = false;
		}

		return $result;
	}

	function _callAgentPropertyCount( $user_id = false, $status = 'active-pending-sold', $params = null ){
		$custom_conditions = Common::hashEmptyField($params, 'custom_conditions');

        $options = $this->_callRefineParams($params, array(
			'conditions' => array(
				'Property.user_id' => $user_id,
			),
            'order' => false,
		));

        if (!empty($custom_conditions)) {
        	$options = array_merge_recursive($options, $custom_conditions);
        }

		return $this->getData('count', $options, array(
			'company' => false,
			'status' => $status,
		));
	}

	function _toChartFormat ( $values, $modelName = false, $is_multiple = false ) {
		$result = array();

		if( !empty($values) && !empty($modelName) ) {
			if( $is_multiple ) {
				$total_arr = count($values)+1;
				foreach( $values as $pkey => $pvalue ) {
					foreach ($pvalue as $key => $value) {
						$cnt = !empty($value[$modelName]['cnt'])?(int)$value[$modelName]['cnt']:0;
						$created = !empty($value[$modelName]['created'])?date('d M Y', strtotime($value[$modelName]['created'])):false;
						$found = false;
						foreach( $result as $check_key => $check_value ){
							if( $check_value[0] == $created ) {
								$found = true;
								$result_index = $check_key;
							}
						}

						if( $found ) {
							$result[$result_index][$pkey+1] = $cnt;
						} else {
							$arr = array_fill(0, $total_arr, 0);
							$arr[0] = $created;
							$arr[$pkey+1] = $cnt;
							$result[] = $arr;
						}
					}
				}
			} else {
				foreach ($values as $key => $value) {
					$cnt = !empty($value[$modelName]['cnt'])?(int)$value[$modelName]['cnt']:0;
					$created = !empty($value[$modelName]['created'])?date('d M Y', strtotime($value[$modelName]['created'])):false;
					$result[] = array(
						$created,
						$cnt,
					);
				}
			}
		}

		return $result;
	}

	function _callLoopChart ( $values, $is_multiple = false ) {
		$result = array();

		if( !empty($values) ) {
			if( $is_multiple ) {
				$total_arr = count($values)+1;
				foreach( $values as $pkey => $pvalue ) {
					foreach ($pvalue as $key => $value) {
						$cnt = !empty($value[0]['cnt'])?(int)$value[0]['cnt']:0;
						$created = !empty($value[0]['created'])?date('d M Y', strtotime($value[0]['created'])):false;
						$formatCreated = !empty($value[0]['created'])?date('Y-m-d', strtotime($value[0]['created'])):false;

						$found = false;
						foreach( $result as $check_key => $check_value ){
							if( $check_value[0] == $created ) {
								$found = true;
								$result_index = $formatCreated;
							}
						}

						if( $found ) {
							$result[$result_index][$pkey+1] = $cnt;
						} else {
							$arr = array_fill(0, $total_arr, 0);
							$arr[0] = $created;
							$arr[$pkey+1] = $cnt;
							$result[$formatCreated] = $arr;
						}
					}
				}
			} else {
				foreach ($values as $key => $value) {

					$cnt = !empty($value[0]['cnt'])?(int)$value[0]['cnt']:0;
					$created = !empty($value[0]['created'])?date('d M Y', strtotime($value[0]['created'])):false;
					$formatCreated = !empty($value[0]['created'])?date('Y-m-d', strtotime($value[0]['created'])):false;

					$result[$formatCreated] = array(
						$created,
						$cnt,
					);
				}
			}
		}

		return $result;
	}

	function _callChartProperties ( $property_id = false, $action_type = 'properties', $fromDate = false, $toDate = false, $options = false ) {
		$_admin = Configure::read('User.admin');
		$conditions = array();
		$fromDate = !empty($fromDate)?$fromDate:date('Y-m-d', strtotime('today - 30 days'));
		$toDate = !empty($toDate)?$toDate:date('Y-m-d');
		$values = array();
		$average = 0;
		$default_options_list_property = array(
			'contain' => array(
				'Property',
			),
			'fields' => array(
				'Property.id', 'Property.id',
			),
		);
		$status = array(
			'status' => 'sold',
			'company' => false,
			'admin_mine' => true,
		);

		if( $action_type == 'properties' ) {
			$default_options_sold = array(
				'conditions' => array(
					'PropertySold.sold_by_id' => Configure::read('User.id'),
				),
				'contain' => array(
					'PropertySold' => array(
						'conditions' => array(
							'PropertySold.status' => 1,
						),
					),
				),
				'fields' => array(
					'COUNT(PropertySold.id) AS cnt',
					'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') AS created',
				),
				'group' => array(
					'created',
				),
				'order' => false,
			);
			$default_options_active = array(
				'conditions' => array(),
				'fields' => array(
					'COUNT(Property.id) AS cnt',
					'DATE_FORMAT(Property.created, \'%Y-%m-%d\') AS created',
				),
				'group' => array(
					'DATE_FORMAT(Property.created, \'%Y-%m-%d\')',
				),
				'order' => false,
			);

			if( $_admin ) {
				$default_options_sold['conditions'] = array();
				$default_options_active['conditions'] = array();
			}

			$conditionsSold = array(
				'Property.property_action_id' => 1,
			);
			$conditionsLeased = array(
				'Property.property_action_id' => 2,
			);

			if( !empty($fromDate) && !empty($toDate) ) {
				$conditionsSoldDate = array(
					'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') >=' => $fromDate,
					'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') <=' => $toDate,
				);
				$conditionsActiveDate = array(
					'DATE_FORMAT(Property.created, \'%Y-%m-%d\') >=' => $fromDate,
					'DATE_FORMAT(Property.created, \'%Y-%m-%d\') <=' => $toDate,
				);
				$default_options_sold['conditions'] = array_merge($default_options_sold['conditions'], $conditionsSoldDate);
				$default_options_active['conditions'] = array_merge($default_options_active['conditions'], $conditionsActiveDate);
			}

		//	Get Property Sold ============================================================================================

			$default_options_sold['conditions'] = array_merge($default_options_sold['conditions'], $conditionsSold);
			$valuesPropertySold = $this->getData('all', $default_options_sold, $status);

			$totalPropertySold = Hash::extract($valuesPropertySold, '{n}.{n}.cnt');
			$totalPropertySold = $totalPropertySold ? array_sum($totalPropertySold) : 0;

		//	==============================================================================================================

		//	Get Property Leased ==========================================================================================

			$default_options_sold['conditions'] = array_merge($default_options_sold['conditions'], $conditionsLeased);
			$valuesPropertyLeased = $this->getData('all', $default_options_sold, $status);

			$totalPropertyLeased = Hash::extract($valuesPropertyLeased, '{n}.{n}.cnt');
			$totalPropertyLeased = $totalPropertyLeased ? array_sum($totalPropertyLeased) : 0;

		//	==============================================================================================================

		//	Get Property Active ==========================================================================================

			$status['status'] = 'active-pending';
			$valuesPropertyActive = $this->getData('all', $default_options_active, $status);

			$totalPropertyActive = Hash::extract($valuesPropertyActive, '{n}.{n}.cnt');
			$totalPropertyActive = $totalPropertyActive ? array_sum($totalPropertyActive) : 0;

		//	==============================================================================================================

			$values = $this->_callLoopChart(array($valuesPropertySold, $valuesPropertyLeased, $valuesPropertyActive), true);

			if( !empty($values) ) {
				ksort($values);

				$values = array_values($values);
			}

			// Dashboard Box
			$status['status'] = 'all';
			$totalProperty = $this->getData('count', array(
				'order' => false,
			), $status);

			return array(
				'values' => $values,
				'totalPropertySold' => $totalPropertySold,
				'totalPropertyLeased' => $totalPropertyLeased,
				'totalPropertyActive' => $totalPropertyActive,
				'totalProperty' => $totalProperty,
				'fromDate' => $fromDate,
				'toDate' => $toDate,
			);
		} else if( $action_type == 'visitors' ) {
			$result = $this->PropertyView->getTotalVisitor($property_id, $fromDate, $toDate);
			$values = !empty($result['data'])?$result['data']:false;
			$total_data = !empty($result['total'])?$result['total']:0;

			$values = $this->_toChartFormat($values, 'PropertyView');
			$total_count = 0;
			$average = 0;
			
			if( !empty($values) ) {
				$_data = $this->_callChartAverage($values);
				$total_count = $_data['total'];
				$average = $_data['average'];
			}

			return array(
				'values' => $values,
				'total' => $total_count,
				'averageVisitor' => $average,
				'fromDate' => $fromDate,
				'toDate' => $toDate,
			);
		} else if( $action_type == 'commissions' ) {

			$fromDate = ( $fromDate == date('Y-m-d', strtotime('today - 30 days')) ) ? date('Y-m-01'):$fromDate;
			$toDate = !empty($toDate)?$toDate:date('Y-m-d');

			$result = $this->PropertySold->getTotalCommission( $fromDate, $toDate, $options );
			$total = !empty($result['total'])?$result['total']:0;

			return array(
				'total' => $total,
				'fromDate' => $fromDate,
				'toDate' => $toDate,
			);

		} else if( $action_type == 'lead' ) {
			
			$result = $this->PropertyLead->getTotalLead($property_id, $fromDate, $toDate, $options);
			$values = !empty($result['data'])?$result['data']:false;
			$total_data = !empty($result['total'])?$result['total']:0;

			$values = $this->_toChartFormat($values, 'PropertyLead');
			$total_count = 0;
			$average = 0;
			if( !empty($values) ) {
				$_data = $this->_callChartAverage($values);
				$total_count = $_data['total'];
				$average = $_data['average'];
			}
			
			return array(
				'values' => $values,
				'total' => $total_count,
				'averageLead' => $average,
				'fromDate' => $fromDate,
				'toDate' => $toDate,
			);
		} else if( $action_type == 'hotlead' ) {

			$result = $this->Message->getTotalHotlead($property_id, $fromDate, $toDate, $options);
			$values = !empty($result['data'])?$result['data']:false;
			$total_data = !empty($result['total'])?$result['total']:0;

			$values = $this->_toChartFormat($values, 'Message');
			$total_count = 0;
			$average = 0;
			if( !empty($values) ) {
				$_data = $this->_callChartAverage($values);
				$total_count = $_data['total'];
				$average = $_data['average'];
			}

			return array(
				'values' => $values,
				'total' => $total_count,
				'averageHotlead' => $average,
				'fromDate' => $fromDate,
				'toDate' => $toDate,
			);
		} else if( $action_type == 'property-sold' ) {

			$default_options = array(
				'conditions' => array(
					'PropertySold.status' => 1,
				),
				'contain' => array(
					'PropertySold',
				),
				'group' => array(
					'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\')',
				),
				'order' => false,
			);

			if( !empty($fromDate) && !empty($toDate) ) {
				$conditionsDate = array(
					'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') >=' => $fromDate,
					'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') <=' => $toDate,
				);
				$default_options['conditions'] = array_merge($default_options['conditions'], $conditionsDate);
			}

			if( !empty($options) ) {
				if( isset($options['conditions']) ) {
					$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
				}
				if( isset($options['contain']) ) {
					$default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
				}
			}

			$this->virtualFields['cnt'] = 'COUNT(PropertySold.id)';
			$this->virtualFields['created'] = 'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\')';

			$elements = array(
				'company' => false,
				'admin_mine' => true,
				'status' => 'sold',
			);

			if( isset($default_options['conditions']['PropertySold.sold_by_id']) ) {
				$elements['admin_mine'] = false;
			}

			$values = $this->getData('all', $default_options, $elements);
			$values = $this->_toChartFormat($values, 'Property');

			$total_count = $this->getData('count', array(
				'conditions' => $default_options['conditions'],
				'contain' => $default_options['contain'],
			), $elements);

			$average = 0;
			if( !empty($values) ) {
				$_data = $this->_callChartAverage($values);
				$average = $_data['average'];
			}

			return array(
				'values' => $values,
				'total' => $total_count,
				'averagePropertySold' => $average,
				'fromDate' => $fromDate,
				'toDate' => $toDate,
			);
		} else if( $action_type == 'property-active' ) {

			$this->virtualFields['cnt'] = 'COUNT(Property.id)';
			$this->virtualFields['created'] = 'DATE_FORMAT(Property.created, \'%Y-%m-%d\')';

			$default_options = array(
				'conditions' => array(),
				'contain' => array(),
				'group' => array(
					'DATE_FORMAT(Property.created, \'%Y-%m-%d\')',
				),
				'order' => false,
			);

			if( !empty($property_id) ) {
				$default_options['conditions'] = array(
					'Property.property_id' => $property_id,
				);
			}
			if( !empty($fromDate) && !empty($toDate) ) {
				$conditionsActiveDate = array(
					'DATE_FORMAT(Property.created, \'%Y-%m-%d\') >=' => $fromDate,
					'DATE_FORMAT(Property.created, \'%Y-%m-%d\') <=' => $toDate,
				);
				$default_options['conditions'] = array_merge($default_options['conditions'], $conditionsActiveDate);
			}

			if( !empty($options) ) {
				if( isset($options['conditions']) ) {
					$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
				}
				if( isset($options['contain']) ) {
					$default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
					$default_options['contain'] = array_unique($default_options['contain']);
				}
			}

			$status['status'] = 'active-pending';
			$values = $this->getData('all', $default_options, $status);
			$values = $this->_toChartFormat($values, 'Property');
			
			$totalPropertyActive = $this->getData('count', array(
				'conditions' => $default_options['conditions'], 
				'contain' => $default_options['contain'], 
			), $status);

			$average = 0;
			if( !empty($values) ) {
				$_data = $this->_callChartAverage($values);
				$average = $_data['average'];
			}

			return array(
				'values' => $values,
				'total' => $totalPropertyActive,
				'averagePropertyActive' => $average,
				'fromDate' => $fromDate,
				'toDate' => $toDate,
			);
		}
	}

	public function _callRefineParams( $data = array(), $default_options = false, $modelName = 'Property' ) {	
		$default_options	= $this->_callParams($data, $default_options, $modelName);
		$empty_field_opts	= array('addslashes' => true);

		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, $empty_field_opts);
		$title = $this->filterEmptyField($data, 'named', 'title', false, $empty_field_opts);
    	$dateFrom = $this->filterEmptyField($data, 'named', 'date_from', false, $empty_field_opts);
        $dateTo = $this->filterEmptyField($data, 'named', 'date_to', false, $empty_field_opts);
        $region = $this->filterEmptyField($data, 'named', 'region', false, $empty_field_opts);
        $city = $this->filterEmptyField($data, 'named', 'city', false, $empty_field_opts);
        $subarea = $this->filterEmptyField($data, 'named', 'subarea', false, $empty_field_opts);
        $subareas = $this->filterEmptyField($data, 'named', 'subareas', false, $empty_field_opts);
        $type = $this->filterEmptyField($data, 'named', 'type', false, $empty_field_opts);
        $typeid = $this->filterEmptyField($data, 'named', 'typeid', false, $empty_field_opts);
        $beds = $this->filterEmptyField($data, 'named', 'beds', false, $empty_field_opts);
        $baths = $this->filterEmptyField($data, 'named', 'baths', false, $empty_field_opts);
        $property_status_id = $this->filterEmptyField($data, 'named', 'property_status_id', false, $empty_field_opts);
        $lot_width = $this->filterEmptyField($data, 'named', 'lot_width', false, $empty_field_opts);
        $lot_length = $this->filterEmptyField($data, 'named', 'lot_length', false, $empty_field_opts);
        $lot_size = $this->filterEmptyField($data, 'named', 'lot_size', false, $empty_field_opts);
        $building_size = $this->filterEmptyField($data, 'named', 'building_size', false, $empty_field_opts);
        $certificate = $this->filterEmptyField($data, 'named', 'certificate', false, $empty_field_opts);
        $condition = $this->filterEmptyField($data, 'named', 'condition', false, $empty_field_opts);
        $furnished = $this->filterEmptyField($data, 'named', 'furnished', false, $empty_field_opts);
        $property_action = $this->filterEmptyField($data, 'named', 'property_action', false, $empty_field_opts);
        $property_direction = $this->filterEmptyField($data, 'named', 'property_direction', false, $empty_field_opts);
        $user = $this->filterEmptyField($data, 'named', 'user', false, $empty_field_opts);
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', $empty_field_opts);
        $mlsid = $this->filterEmptyField($data, 'named', 'mlsid', false, $empty_field_opts);
        $price = $this->filterEmptyField($data, 'named', 'price', false, $empty_field_opts);
        $filter = $this->filterEmptyField($data, 'named', 'filter', false, $empty_field_opts);
        $sort = $this->filterEmptyField($data, 'named', 'sort', $filter, $empty_field_opts);
        $sold = $this->filterEmptyField($data, 'named', 'sold', false, $empty_field_opts);
        $name = $this->filterEmptyField($data, 'named', 'name', false, $empty_field_opts);
        $status = $this->filterEmptyField($data, 'named', 'status', false, $empty_field_opts);
        $principle_id = $this->filterEmptyField($data, 'named', 'principle_id', false, $empty_field_opts);

		$co_broke = $this->filterEmptyField($default_options, 'co_broke');

		if(!empty($title)){
			$default_options['conditions']['Property.title LIKE'] = '%' . $title . '%';
		}

        if( !empty($keyword) ) {
            if($co_broke){
            	$field_filter = 'CoBrokeProperty.code LIKE ';
            }else{
				$field_filter = 'Property.mls_id LIKE ';
            }

            $default_options['conditions']['OR'] = array(
                $field_filter => '%'.$keyword.'%',
                'Property.title LIKE' => '%'.$keyword.'%',
                'Property.keyword LIKE' => '%'.$keyword.'%',
                'Property.description LIKE' => '%'.$keyword.'%',
            );

		//	personal page (kalo bukan personal page user ini isinya principal)
			$companyData	= Configure::read('Config.Company.data');
			$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
			$isAgent		= Common::validateRole('agent', $userGroupID);

			if(empty($isAgent)){
				$users = $this->User->getData('list', array(
					'conditions' => array(
						'OR' => array(
							'User.first_name LIKE' => '%'.$keyword.'%',
							'User.last_name' => '%'.$keyword.'%',
							'User.email LIKE' => '%'.$keyword.'%',
						),
					),
					'fields' => array(
						'User.id'
					),
				), array(
					'company' => true,
					'admin' => true,
					'role' => 'agent',
	            ));

				if( !empty($users) ) {
					$default_options['conditions']['OR']['Property.user_id'] = $users;
					$default_options['conditions']['OR']['Property.client_id'] = $users;
				}
			}
		}

		if( !empty($mlsid) ) {
			$default_options['conditions']['Property.mls_id'] = $mlsid;
		}

		$region_id = null;
		if( !empty($region) ) {
			if(is_numeric($region) === false){
				$region = $this->PropertyAddress->Region->getData('first', array(
					'conditions' => array(
						'Region.slug' => $region, 
					)
				));

				$region_id = $region = Common::hashEmptyField($region, 'Region.id');
			}

			$default_options['conditions']['PropertyAddress.region_id'] = $region;
			$default_options['contain'][] = 'PropertyAddress';
		}

		$city_id = null;
		if( !empty($city) ) {
			if(is_numeric($city) === false){
				$city_condition = array(
					'City.slug' => $city, 
				);

				if(!empty($region_id)){
					$city_condition['City.region_id'] = $region_id;
				}

				$city = $this->PropertyAddress->City->getData('first', array(
					'conditions' => $city_condition
				));

				$city_id = $city = Common::hashEmptyField($city, 'City.id');
			}

			$default_options['conditions']['PropertyAddress.city_id'] = $city;
			$default_options['contain'][] = 'PropertyAddress';
		}

		if( !empty($subarea) ) {
			if(is_numeric($subarea) === false){
				$subarea_condition = array(
					'Subarea.slug' => $subarea, 
				);

				if(!empty($region_id)){
					$subarea_condition['Subarea.region_id'] = $region_id;
				}
				if(!empty($city_id)){
					$subarea_condition['Subarea.city_id'] = $city_id;
				}

				$subarea = $this->PropertyAddress->Subarea->getData('first', array(
					'conditions' => $subarea_condition
				));

				$subarea = Common::hashEmptyField($subarea, 'Subarea.id');
			}

			$default_options['conditions']['PropertyAddress.subarea_id'] = $subarea;
			$default_options['contain'][] = 'PropertyAddress';
		}

		if( !empty($subareas) ) {
			$subareas = urldecode($subareas);
			$subareas = explode(',', $subareas);
			$default_options['conditions']['PropertyAddress.subarea_id'] = $subareas;
			$default_options['contain'][] = 'PropertyAddress';
		}

		if( !empty($property_action) ) {
			$default_options['conditions']['Property.property_action_id'] = (int) $property_action;
		}

		if( !empty($type) ) {
			$type = urldecode($type);
			$type = explode(',', $type);

			if(Hash::numeric($type) === false){
				$type = $this->PropertyType->getData('list', array(
					'fields'		=> array('PropertyType.id', 'PropertyType.id'), 
					'conditions'	=> array(
						'PropertyType.slug' => $type, 
					), 
				));
			}

			$default_options['conditions']['Property.property_type_id'] = $type;
		}

		if( !empty($typeid) ) {
			$default_options['conditions']['Property.property_type_id'] = $typeid;
		}

		if( !empty($property_status_id) ) {
			$default_options['conditions']['Property.property_status_id'] = $property_status_id;
		}

		if( !empty($beds) ) {
			$this->virtualFields['total_beds'] = 'COALESCE(beds, 0) + COALESCE(beds_maid, 0)';
			$default_options['conditions']['total_beds >='] = $beds;
			$default_options['conditions']['PropertyType.is_residence'] = 1;

			$default_options['contain'][] = 'PropertyAsset';
			$default_options['contain'][] = 'PropertyType';
		}

		if( !empty($baths) ) {
			$this->virtualFields['total_baths'] = 'COALESCE(baths, 0) + COALESCE(baths_maid, 0)';
			$default_options['conditions']['total_baths >='] = $baths;
			$default_options['conditions']['PropertyType.is_residence'] = 1;

			$default_options['contain'][] = 'PropertyAsset';
			$default_options['contain'][] = 'PropertyType';
		}

		if( !empty($lot_size) ) {
			$lot_size = urldecode($lot_size);

			if( strstr($lot_size, '-') ) {
				$arrSize = explode('-', $lot_size);

				if( count($arrSize) == 2 ) {
					$default_options['conditions']['PropertyAsset.lot_size >='] = $arrSize[0];

					$default_options['conditions']['PropertyAsset.lot_size <='] = $arrSize[1];
				}
			} else if( strstr($lot_size, '<') ) {
				$size = str_replace('<', '', $lot_size);
				$default_options['conditions']['PropertyAsset.lot_size <'] = $size;
			} else if( strstr($lot_size, '>') ) {
				$size = str_replace('>', '', $lot_size);
				$default_options['conditions']['PropertyAsset.lot_size >'] = $size;
			}

			$default_options['conditions']['AND']['OR'][]['PropertyType.is_lot'] = 1;
			$default_options['conditions']['AND']['OR'][]['PropertyType.is_space'] = 1;

			$default_options['contain'][] = 'PropertyAsset';
			$default_options['contain'][] = 'PropertyType';
		}

		if( !empty($building_size) ) {
			$building_size = urldecode($building_size);

			if( strstr($building_size, '-') ) {
				$arrSize = explode('-', $building_size);

				if( count($arrSize) == 2 ) {
					$default_options['conditions']['PropertyAsset.building_size >='] = $arrSize[0];

					$default_options['conditions']['PropertyAsset.building_size <='] = $arrSize[1];
				}
			} else if( strstr($building_size, '<') ) {
				$size = str_replace('<', '', $building_size);
				$default_options['conditions']['PropertyAsset.building_size <'] = $size;
			} else if( strstr($building_size, '>') ) {
				$size = str_replace('>', '', $building_size);
				$default_options['conditions']['PropertyAsset.building_size >'] = $size;
			}

			$default_options['conditions']['AND']['OR'][]['PropertyType.is_building'] = 1;
			$default_options['conditions']['AND']['OR'][]['PropertyType.is_space'] = 1;
			
			$default_options['contain'][] = 'PropertyAsset';
			$default_options['contain'][] = 'PropertyType';
		}

		if( !empty($lot_width) ) {
			$default_options['conditions']['PropertyAsset.lot_width'] = $lot_width;
			$default_options['contain'][] = 'PropertyAsset';
		}

		if( !empty($lot_length) ) {
			$default_options['conditions']['PropertyAsset.lot_length'] = $lot_length;
			$default_options['contain'][] = 'PropertyAsset';
		}

		if( !empty($price) ) {
			$firstString = substr($price, 0, 1);
			$this->virtualFields['price_converter'] = '
				CASE WHEN Property.sold = 1 THEN 
					PropertySold.price_sold 
				ELSE 
					CASE 
					WHEN Property.price_measure > 0 THEN
						Property.price_measure 
					ELSE
						Property.price
					END
				END
			';
			$default_options['contain'][] = 'PropertySold';

			if( in_array($firstString, array( '>', '<' )) ) {
				$price = substr($price, 1);
				$default_options['conditions']['price_converter '.$firstString] = $price;
			} else {
				$price = explode('-', $price);
				$min_price = !empty($price[0])?$price[0]:false;
				$max_price = !empty($price[1])?$price[1]:false;

				if( !empty($min_price) ) {
					$default_options['conditions']['price_converter >='] = $min_price;
				}
				if( !empty($max_price) ) {
					$default_options['conditions']['price_converter <='] = $max_price;
				}
			}
		}

		if( !empty($certificate) ) {
			$certificates = $this->Certificate->getData('list', array(
				'conditions' => array(
					'Certificate.slug' => $certificate,
				),
				'fields' => array(
					'Certificate.id', 'Certificate.id',
				),
                'cache' => __('Certificate.Slug.List.%s', $certificate),
			));

			if( !empty($certificates) ) {
				$default_options['conditions']['Property.certificate_id'] = $certificates;
			} else {
				$default_options['conditions']['Property.certificate_id'] = $certificate;
			}
		}

		if( !empty($user) ) {
			$user = urldecode($user);
			$users = $this->User->getData('list', array(
				'conditions' => array(
					'OR' => array(
						'User.id' => $user,
						'User.email LIKE' => '%'.$user.'%',
						'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE' => '%'.$user.'%',
					),
				),
				'fields' => array(
					'User.id', 'User.id',
				),
				'limit' => 200,
			), array(
				'company' => true,
				'admin' => true,
				'status' => 'semi-active',
			));
			$default_options['conditions']['Property.user_id'] = $users;
		}

		if( !empty($condition) ) {
			$default_options['conditions']['PropertyAsset.property_condition_id'] = $condition;
			$default_options['contain'][] = 'PropertyAsset';
		}

		if( !empty($furnished) ) {
			$default_options['conditions']['PropertyAsset.furnished'] = $furnished;
			$default_options['contain'][] = 'PropertyAsset';
		}

		if( !empty($property_direction) ) {
			$default_options['conditions']['PropertyAsset.property_direction_id'] = $property_direction;
			$default_options['contain'][] = 'PropertyAsset';
		}

		if( $sort == 'Property.price_converter' ) {
			$this->virtualFields['price_converter'] = '
				CASE WHEN Property.sold = 1 THEN 
					PropertySold.price_sold 
				ELSE 
					CASE 
					WHEN Property.price_measure > 0 THEN
						Property.price_measure 
					ELSE
						Property.price
					END
				END
			';
			$default_options['order']['price_converter'] = $direction;
			$default_options['contain'][] = 'PropertySold';
		} else if( $sort == 'property_updated-desc' ) {
			$default_options['order'] = array(
				'Property.change_date' => 'DESC',
				'Property.featured' => 'DESC', 
				'Property.id' => 'DESC',
			);
		} else if( !empty($sort) ) {
        	$sortUser = strpos($sort, 'User.');

        	if( is_numeric($sortUser) ) {
	            $default_options['contain'][] = 'User';
	        }
        }

		if( !empty($sold) ) {
			$default_options['conditions']['Property.sold'] = 1;
		}

		if( !empty($default_options['contain']) ) {
			$default_options['contain'] = array_unique($default_options['contain']);
		}

		if( !empty($dateFrom) ) {
			$field = 'created';
			if( $modelName == 'PropertySold' || $status == 'sold') {
				$field = 'sold_date';

				$modelName = 'PropertySold';
				$default_options['contain'][] = 'PropertySold';
			}

			$default_options['conditions']['DATE_FORMAT('.$modelName.'.'.$field.', \'%Y-%m-%d\') >='] = $dateFrom;
			if( !empty($dateTo) ) {
				$default_options['conditions']['DATE_FORMAT('.$modelName.'.'.$field.', \'%Y-%m-%d\') <='] = $dateTo;
			}
		}

		if( !empty($name) ) {
			$default_options['conditions']['OR'][]['CONCAT(User.first_name,\' \',User.last_name) LIKE'] = '%'.$name.'%';
			$default_options['conditions']['OR'][]['User.email LIKE'] = '%'.$name.'%';
			$default_options['contain'][] = 'User';
		}

		if( !empty($status) ) {
			$status = explode(',', $status);
			$statusConditions = array();

			foreach ($status as $key => $stat) {
				$statusConditions[] = $this->_callStatusCondition($stat, 'restrict', $data);
			}

			if( !empty($statusConditions) ) {
				$default_options['conditions'][]['OR'] = $statusConditions;
			}
		}

		if( !empty($principle_id) ) {
			if( !is_array($principle_id) ) {
				$principle_id = explode(',', $principle_id);
			}
			
			$default_options['conditions']['Property.principle_id'] = $principle_id;
		}

	//	tambahan filter baru dari quick search market trend
		$filterTags		= $this->filterEmptyField($data, 'named', 'tag');
		$filterTags		= explode(',', urldecode($filterTags));
		$filterTags		= array_unique(array_filter($filterTags));
		$filterOptions	= $this->__buildFilterOptions($filterTags, array(
			'options' => $default_options, 
		));

		if($filterOptions){
			if(Hash::check($filterOptions, 'virtualFields')){
				$this->virtualFields = array_merge($this->virtualFields, Hash::get($filterOptions, 'virtualFields', array()));

				$filterOptions = Hash::remove($filterOptions, 'virtualFields');
			}

		//	replace default options
			$default_options = $filterOptions;
		}

		return $default_options;
	}

	public function _callParams( $data = '', $default_options = false, $modelName = 'Property' ) {
		$keyword = $this->filterEmptyField($data, 'named', 'keyword');

		if( empty($keyword) ) {
			$region = $this->filterEmptyField($data, 'region');
			$region_id = $this->filterEmptyField($data, 'named', 'region_id');

			$city = $this->filterEmptyField($data, 'city');
			$city_id = $this->filterEmptyField($data, 'named', 'city_id');

			$subarea = $this->filterEmptyField($data, 'subarea');
			$subarea_id = $this->filterEmptyField($data, 'named', 'subarea_id');
		}

		$type = !empty($data['type'])?trim($data['type']):false;
		$property_action = !empty($data['property_action'])?trim($data['property_action']):false;

		if( !empty($region) || !empty($region_id) ) {
			if( !empty($region) ) {
				$region_id = $this->PropertyAddress->Region->getData('list', array(
					'conditions' => array(
						'Region.slug' => $region,
					),
					'fields' => array(
						'Region.id', 'Region.id',
					),
					'cache' => __('Region.%s', $region),
				));
			}

			$default_options['conditions']['PropertyAddress.region_id'] = $region_id;
			$default_options['contain'][] = 'PropertyAddress';
		}

		if( !empty($city) || !empty($city_id) ) {
			if( !empty($city) ) {
				$city = $this->PropertyAddress->City->getData('first', array(
					'conditions' => array(
						'City.slug' => $city,
					),
					'cache' => __('City.%s', $city),
    				'cacheConfig' => 'cities',
				));
				$city_id = $this->filterEmptyField($city, 'City', 'id');
			}

			$default_options['conditions']['PropertyAddress.city_id'] = $city_id;
			$default_options['contain'][] = 'PropertyAddress';
		}

		if( !empty($subarea) || !empty($subarea_id) ) {
			if( !empty($subarea) ) {
				$subarea_id = $this->PropertyAddress->Subarea->getData('list', array(
					'conditions' => array(
						'Subarea.slug' => $subarea,
					),
					'fields' => array(
						'Subarea.id', 'Subarea.id',
					),
				));
			}

			$default_options['conditions']['PropertyAddress.subarea_id'] = $subarea_id;
			$default_options['contain'][] = 'PropertyAddress';
		}

		if( !empty($subareas) ) {
			$subareas = urldecode($subareas);
			$subareas = explode(',', $subareas);
			$default_options['conditions']['PropertyAddress.subarea_id'] = $subareas;
			$default_options['contain'][] = 'PropertyAddress';
		}

		if( !empty($property_action) ) {
			switch ($property_action) {
				case 'disewakan':
					$property_action_id = 2;
					break;
				
				default:
					$property_action_id = 1;
					break;
			}
			$default_options['conditions']['Property.property_action_id'] = $property_action_id;
		}
		if( !empty($type) ) {
			$type = urldecode($type);
			$type = $this->PropertyType->getData('first', array(
				'conditions' => array(
					'PropertyType.slug' => $type,
				),
				'cache' => __('PropertyType.%s', $type),
			));
		}
		if( !empty($type['PropertyType']['id']) ) {
			$default_options['conditions']['Property.property_type_id'] = $type['PropertyType']['id'];
		}

		return $default_options;
	}


	public function __buildFilterOptions($filterTags = array(), $configs = array()){
		$filterTags		= (array) $filterTags;
		$configs		= (array) $configs;
		$defaultOptions	= array();

		if($filterTags){
			$validTags				= Configure::read('Config.MarketTrend.filter_tag');	
			$filterTags				= array_intersect($filterTags, $validTags);
			$additionalConditions	= array();

			if($filterTags){
				$model			= Hash::get($configs, 'model', $this->alias);
				$defaultOptions	= Hash::get($configs, 'options', array());

				foreach($filterTags as $key => $filterTag){
					if($filterTag == 'affordable'){
					//	query price
						$filterOptions = $defaultOptions;
						$filterOptions['conditions']['Property.price_converter >'] = 0;

						$filterOptions['contain'][]	= 'PropertySold';
						$filterOptions['group']		= array('Property.property_action_id', 'Property.property_type_id');
						$filterOptions['fields']	= array(
							'Property.property_action_id', 
							'Property.property_type_id', 
							'Property.property_count', 
							'Property.price_total', 
						);

						$priceQuery = '
							CASE WHEN Property.sold = 1 THEN
								COALESCE(PropertySold.price_sold, 0)
							ELSE 
								CASE WHEN COALESCE(Property.price_measure, 0) > 0 THEN
									COALESCE(Property.price_measure, 0)
								ELSE
									COALESCE(Property.price, 0)
								END
							END
						';

						$this->virtualFields['property_count']	= 'COUNT(Property.id)';
						$this->virtualFields['price_total']		= 'SUM('.$priceQuery.')';
						$this->virtualFields['price_converter']	= $priceQuery;

						$properties = $this->getData('all', $filterOptions);

						if($properties){
							$propertyPrices = array();

							foreach($properties as $key => $property){
								$actionID		= Hash::get($property, 'Property.property_action_id');
								$typeID			= Hash::get($property, 'Property.property_type_id');
								$propertyCount	= Hash::get($property, 'Property.property_count', 0);
								$priceTotal		= Hash::get($property, 'Property.price_total', 0);

								if($actionID && $typeID){
									$additionalConditions['OR'][] = array(
										'Property.property_action_id'	=> $actionID, 
										'Property.property_type_id'		=> $typeID, 
										'Property.price_converter <='	=> $priceTotal / $propertyCount,  
									);
								}
							}

						//	baru pasang contain kalo ada record
							$defaultOptions['contain'][] = 'PropertySold';
						}
						else{
						//	unset virtual fields yang atas
							$this->virtualFields = Hash::remove($this->virtualFields, 'property_count');
							$this->virtualFields = Hash::remove($this->virtualFields, 'price_total');
							$this->virtualFields = Hash::remove($this->virtualFields, 'price_converter');
						}
					}
					else{
					//	query point of interest
						$placeCategories = array(
						//	key				=> place_specific_category_id
							'transport'		=> array(594, 595, 596, 597, 598, 599, 600, 601, 602, 603, 604, 605, 606, 607, 608, 609, 610, 611, 612, 613, 614, 615, 616, 617, 618, 619, 620, 621, 622, 623, 624, 625, 626, 627, 628, 629, 630, 631, 632, 633, 634, 635, 636, 637, 638, 639, 640, 641, 642, ), 
							'education'		=> array(61, 62, 63, 64, 65, 66, 67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77, 78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, ), 
							'healthcare'	=> array(74, 76, 349, 353, 381, 382, 385, 386, 391, 480, 488, 551, 559, 570, 578, 619),  
							'entertainment'	=> array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60, ), 
						);

						$specificCategoryID = Hash::get($placeCategories, $filterTag, array());

						if($specificCategoryID){
						//	radius dalam kilometer di convert dulu ke mile (3 mile)
							$radius = 3 * 0.621371;

							$dataSource	= $this->getDataSource();
							$subQuery	= $dataSource->buildStatement(array(
								'fields'		=> array('COUNT(Place.id)'), 
								'table'			=> 'places', 
								'alias'			=> 'Place', 
								'limit'     	=> null,
								'offset'		=> null,
								'joins'			=> array(),
								'conditions'	=> array(
									'Place.place_spesific_category_id'	=> $specificCategoryID, 
									'Place.longitude BETWEEN (PropertyAddress.longitude - '. $radius .' / COS(RADIANS(Place.latitude)) * 69) AND (PropertyAddress.longitude + '. $radius .' / COS(RADIANS(Place.latitude)) * 69)', 
									'Place.latitude BETWEEN (PropertyAddress.latitude - ('. $radius .' / 69)) AND (PropertyAddress.latitude + ('. $radius .' / 69))', 
									'3956 * 2 * ASIN(SQRT(POWER(SIN((PropertyAddress.latitude - Place.latitude) *  pi() / 180 / 2), 2) + COS(PropertyAddress.latitude * pi() / 180) * COS(Place.latitude * pi() / 180) * POWER(SIN((PropertyAddress.longitude - Place.longitude) * pi() / 180 / 2), 2))) < '.$radius, 
								),
								'order'			=> null,
								'group'			=> null, 
							), $this);

							$subQueryExpr = $dataSource->expression($subQuery);

							$defaultOptions['virtualFields']	= array('facility_count' => $subQueryExpr->value);
							$defaultOptions['order']			= array('Property.facility_count' => 'DESC');
						}
					}
				}
			}

			if($additionalConditions){
				$defaultOptions['conditions'][] = $additionalConditions;	
			}

			return $defaultOptions;
		}
	}

	function getListCompanyProperties( $company_user_id, $keyword = array(), $elements = array(), $progress_kpr = null, $restrict_active = false, $default_options = array(), $type = 'list' ){
		$rest_api = Configure::read('Rest.token');
		$result = array();

		if( !empty($rest_api) ) {
			$fields = false;
		} else {
			$this->virtualFields['name_property'] = 'CONCAT(Property.mls_id, ", ", Property.title)';
			$fields = array(
				'Property.id', 'Property.name_property'
			);
		}


		$options = array(
			'conditions' => array(),
			'fields' => $fields,
			'limit' => 10,
		);
		$options = array_merge($options, $default_options);

		if(!empty($keyword)){
			$options['conditions'][]['OR'] = array(
				'Property.mls_id LIKE'=> '%'.$keyword.'%', 
				'Property.title LIKE'=> '%'.$keyword.'%', 
				'Property.keyword LIKE'=> '%'.$keyword.'%'
			);
		} else {
			$options['conditions']['Property.sold'] = 0;
		}

		if( empty($progress_kpr) ) {
			$options['conditions']['Property.property_type_id'] = Configure::read('__Site.Global.Variable.KPR.PropertyTypes');
			$options['conditions']['Property.on_progress_kpr'] = FALSE;
			$elements['action_type'] = 'sell';
		}

		if ($restrict_active == true) {
			$options['conditions']['Property.active'] = 1;
		}

		$result = $this->getData($type, $options, $elements);
		return $result;
	}

	function _callChartAverage( $values ) {
		$result = array();
		$total = 0;
		$average = 0;

		if( !empty($values) ) {
			$divisor = count($values);
			for($i = 0; $i < count($values); $i++){
				for($j = 1; $j < count($values[$i]); $j++){
					if( isset($result[$j-1]) ){
						$result[$j-1] = $result[$j-1] + $values[$i][$j];
					} else {
						$result[$j-1] = $values[$i][$j];
					}
				}	
			}

			if( !empty($result[0]) ) {
				$total = !empty($result[0])?$result[0]:0;
				$average = round($result[0] / $divisor);
			}
		}

		return array(
			'total' => $total,
			'average' => $average,
		);
	}

	function getListPropertyByUserId( $user_id = false, $status = 'active-pending' ) {
		$result = array();
		if( !empty($user_id) ) {
			$result = $this->getData('list', array(
				'conditions' => array(
					'Property.user_id' => $user_id,
				),
				'fields' => array(
					'Property.id', 'Property.id',
				),
			), array( 'status' => $status ));
		}
		return $result;
	}

	function populers ( $limit = 5, $options = array(), $param_query = array() ) {
		$companyData	= Configure::read('Config.Company.data');
		$userID			= Common::hashEmptyField($companyData, 'User.id', 0);
		$companyID		= Common::hashEmptyField($companyData, 'UserCompany.id', 0);

		$cacheName		= __('Property.Populers.%s.%s', $companyID, $userID);
		$cacheConfig	= 'default';
		// $values			= Cache::read($cacheName, $cacheConfig);

		if( (is_numeric($limit) && $limit > 0) && (empty($values) || !empty($param_query)) ) {
			$isPersonalPage	= Configure::read('Config.Company.is_personal_page');
			$options		= array(
				'conditions' => array(
					'Property.status' => 1,
					'Property.published' => 1,
					'Property.sold' => 0,
					'Property.deleted' => 0,
					'Property.inactive' => 0,
					'Property.active' => 1,
				),
				'order' => array(
					'Property.sold' => 'ASC',
					'Property.change_date' => 'DESC',
					'Property.id' => 'DESC',
				),
			);

		//	personal page
			$parentID		= Configure::read('Principle.id');
			$userID			= Common::hashEmptyField($companyData, 'User.id');
			$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
			$isIndependent	= Common::validateRole('independent_agent', $userGroupID);

			$parentID = Common::hashEmptyField($options, 'parent_id', $parentID);

			if($isPersonalPage){
				// if(empty($isIndependent) && $parentID){
				// 	$companyID = $this->User->UserCompany->field('UserCompany.id', array(
				// 		'UserCompany.user_id' => $parentID, 
				// 	));
				// }

				$options = array(
					'conditions' => array(
						'Property.user_id' => $userID,
						// 'COALESCE(Property.principle_id, 0)' => $parentID,
					), 
				);
			}

			// $options = $this->getData('paginate', $options, array(
			// 	'status'	=> 'active-pending-sold',
			// 	'company'	=> false,
			// ));

			$options = array(
				'conditions'	=> Common::hashEmptyField($options, 'conditions', array()),
				'limit'			=> $limit,
				// 'contain'		=> array('Property'),
				'group'			=> array('Property.id'),
				// 'order'			=> array('cnt' => 'DESC'),
				'order' => array(
					'Property.modified' => 'DESC',
				),
			);

			if(empty($isPersonalPage)){
				if( $userGroupID == 4 && !empty($parentID) ) {
					$parentID = $this->User->getAgents($parentID, true, 'list', false, array('role' => 'principle'));

					$options['conditions']['Property.principle_id'] = $parentID;
					// $options['conditions']['User.parent_id'] = $parentID;
					// $options['contain'][] = 'User';

					// $this->PropertyView->bindModel(array(
					// 	'belongsTo' => array(
					// 		'User' => array(
					// 			'className' => 'User',
					// 			'foreignKey' => false,
					// 			'conditions' => array(
					// 				'Property.user_id = User.id',
					// 			),
					// 		),
					// 	)
					// ), false);
				} else {
					// $agentID = $this->User->getAgents( $parentID, true );
					$options['conditions']['Property.principle_id'] = $parentID;
					// $options['conditions']['PropertyView.agent_id'] = $agentID;
				}
			}

			// $this->PropertyView->virtualFields['cnt'] = 'COUNT(PropertyView.property_id)';
			$values = $this->getData('all', $options, array(
				'company' => false,
			));

			if(!empty($values) ) {
				foreach ($values as $key => &$value) {					
		            // $value = $this->PropertyView->getMergeList($value, array(
		            //     'contain' => array(
		            //         'Property' => array(
		            //             'elements' => array(
		            //                 'status' => 'all',
		            //                 'company' => false,
		            //             ),
		            //         ),
		            //     ),
		            // ));
					$value = $this->getDataList($value, array(
						'contain' => array(
							'MergeDefault',
							'User',
							'PropertyAsset',
							'PropertyAddress',
							'PropertyStatusListing', 
						),
					));
					
					if( $this->callIsDirector() ) {
						$value = $this->User->getMergeList($value, array(
							'contain' => array(
								'UserCompanyConfig' => array(
									'primaryKey' => 'user_id',
									'foreignKey' => 'parent_id',
								),
							),
						));
					}
				}
			}

			Cache::write($cacheName, $values, $cacheConfig);
		}

		return $values;
	}

	/* ============== PROPERTI TERKAIT ==============
	   search by related agent id, and related area
	   - munculkan properti terkait berdasarkan agent, area dan kota
	   - prioritas order berdasarkan area, kemudian kota properti terkait
	*/
	function getNeighbours ( $data, $limit = 3 ) {
		$id = !empty($data['Property']['id'])?$data['Property']['id']:false;
		$mls_id = !empty($data['Property']['mls_id'])?$data['Property']['mls_id']:false;
		$subarea_id = !empty($data['PropertyAddress']['Subarea']['id'])?$data['PropertyAddress']['Subarea']['id']:false;
		$city_id = !empty($data['PropertyAddress']['City']['id'])?$data['PropertyAddress']['City']['id']:false;
		$property_type_id = !empty($data['Property']['property_type_id'])?$data['Property']['property_type_id']:false;
		$property_action_id = !empty($data['Property']['property_action_id'])?$data['Property']['property_action_id']:false;

		$user_id = !empty($data['User']['id'])?$data['User']['id']:false;

		$orders = array(
			'Property.order_by_subarea' => 'DESC',
			'Property.change_date' => 'DESC',
			'Property.id' => 'DESC',
		);

		if(!empty($subarea_id)){
			$this->virtualFields['order_by_subarea'] = 'CASE WHEN PropertyAddress.subarea_id = '.$subarea_id.' THEN 1 ELSE 0 END';
		}else{
			unset($orders['Property.order_by_subarea']);
		}

	//	personal page
		$parentID		= Configure::read('Principle.id');
		$companyData	= Configure::read('Config.Company.data');
		$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $userGroupID);
		$companyID		= 0;

		if(empty($isIndependent) && $parentID){
			$companyID = $this->User->UserCompany->field('UserCompany.id', array(
				'UserCompany.user_id' => $parentID, 
			));
		}

		$values = $this->getData('all', array(
			'conditions' => array(
				'Property.id <>' => $id,
				'Property.user_id' => $user_id,
				'COALESCE(Property.company_id, 0)' => $companyID,
				'Property.property_type_id' => $property_type_id,
				'Property.property_action_id' => $property_action_id,
				'PropertyAddress.city_id' => $city_id,
			),
			'contain' => array(
				'PropertyAddress',
			),
			'order' => $orders,
			'limit' => $limit,
		), array(
			'status' => 'active-pending-sold',
			'company' => empty($isIndependent),
			'skip_is_sales' => true,
		));

		$value = $this->getDataList($values, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyStatusListing',
				'PropertyAsset',
				'PropertySold',
				'User',
			),
		));

		return $value;
	}

	function approve($id, $config, $is_approve = true){
		$properti = $this->getData('first', 
			array(
				'conditions' => array(
					'Property.id' => $id
				),
			),
			array(
				'status' => 'pending'
			)
		);

	//	$result['msg'] = __('Properti Tidak ditemukan');
		$result = array(
		//	'msg'		=> __('Anda tidak bisa menyetujui properti ini dikarenakan sudah masuk batas listing Anda.'),
			'msg'		=> __('Properti Tidak ditemukan'),
			'status'	=> 'error', 
			'data'		=> $properti, 
		);

		if(!empty($properti)){
			$text 		= 'menyetujui';
			$id 		= !empty($properti['Property']['id'])?$properti['Property']['id']:false;
			$user_id 	= !empty($properti['Property']['user_id'])?$properti['Property']['user_id']:false;
			$mls_id 	= !empty($properti['Property']['mls_id'])?$properti['Property']['mls_id']:false;
			$active 	= !empty($properti['Property']['active'])?$properti['Property']['active']:false;
			$is_cobroke = $this->filterEmptyField($properti, 'Property', 'is_cobroke');

			if(!$is_approve){
				$text = 'menolak';
			}

			$this->id = $id;
			
			if($is_approve){
				$dataOptions = array(
					'status' => 1,
					'active' => 1,
					'publish_date' => date('Y-m-d h:i:s')
				);

				if( empty($active) ) {
					$dataOptions['approved_by'] = Configure::read('User.id');
				}

				$this->set($dataOptions);
			}else{
				$this->set(array(
					'published' => 0,
					'status' => 0,
					'active' => 0
				));
			}

			if($this->save()){
				$this->PropertyVideos->approveMultiple($id);
				
				if($is_approve){
					$result_media = $this->PropertyMedias->approveMultiple($id);
					$notifMsg = sprintf(__('Properti dengan ID %s telah berhasil disetujui per tanggal %s'), $mls_id, date('d M Y'));

					if(!empty($result_media)){
						$properti = array_merge($properti, $result_media);
					}

					if(!empty($is_cobroke)){

						$force_admin_approvel = false;
						$is_admin_approval_cobroke = Configure::read('Config.Company.data.UserCompanyConfig.is_admin_approval_cobroke');
						if(!empty($is_admin_approval_cobroke)){
							$force_admin_approvel = true;
						}

						$this->CoBrokeProperty->doCoBroke($id, 'active', $force_admin_approvel);
					}
				} else {
					$notifMsg = sprintf(__('Revisi properti dengan ID %s telah ditolak'), $mls_id);
				}

				$this->User->Notification->doSave(array(
					'Notification' => array(
						'user_id' => $user_id,
						'name' => $notifMsg,
						'link' => array(
							'controller' => 'properties',
							'action' => 'index',
							'keyword' => $mls_id,
							'admin' => true,
						),
					),
				));

				$msg = sprintf(__('Berhasil %s properti dengan judul "#%s - %s"'), $text, $properti['Property']['mls_id'], $properti['Property']['title']);

				$result = array(
					'status' => 'success',
					'msg' => $msg,
					'data' => $properti,
					'Log' => array(
						'activity' => $msg,
						'old_data' => $properti,
						'document_id' => $id
					),
				);
			}else{
				$msg = sprintf(__('Gagal %s properti dengan judul "#%s - %s"'), $text, $properti['Property']['mls_id'], $properti['Property']['title']);

				$result['msg'] = $msg;
				$result['Log'] = array(
					'activity' => $msg,
					'data' => $properti,
					'document_id' => $id,
					'error' => 1,
				);
			}
		}

		return $result;
	}

	function saveRevisiData($data){
		$result = array(
			'msg' => __('Gagal melakukan persetujuan revisi properti.'),
			'status' => 'error'
		);

		if(!empty($data['Property']['id'])){
			$property_id = $data['Property']['id'];

			$mls_id = !empty($data['Property']['mls_id'])?$data['Property']['mls_id']:false;
			$user_id = !empty($data['Property']['user_id'])?$data['Property']['user_id']:false;
			$active = !empty($data['Property']['active'])?$data['Property']['active']:false;
			
			$data['Property']['in_update'] = 0;
			$data['Property']['active'] = 1;
			$data['Property']['published'] = 1;
			$data['Property']['status'] = 1;
			$data['Property']['deleted'] = 0;
			$data['Property']['sold'] = 0;
			$data['Property']['inactive'] = 0;
			$data['Property']['change_date'] = date('Y-m-d H:i:s');

			$data['is_easy_mode'] = true;

			$this->PropertyNotification->doToggle($property_id);
			$result = $this->doBasic($data, false, false, $property_id);

			if(!empty($result['status']) && $result['status'] == 'success'){
				if(!empty($data['PropertyAddress'])){
					$property_address_id = false;
					if(empty($data['PropertyAddress']['id'])){
						$property_address = $this->PropertyAddress->getData('first', array(
							'conditions' => array(
								'PropertyAddress.property_id' => $property_id
							)
						));

						if(!empty($property_address['PropertyAddress']['id'])){
							$property_address_id = $property_address['PropertyAddress']['id'];
						}
					}else{
						$property_address_id = $data['PropertyAddress']['id'];
					}

					$this->PropertyAddress->doAddress($data, false, false, $property_id, $property_address_id);
				}
				
				if(!empty($data['PropertyAsset']) || !empty($data['PropertyPointPlus']['name']) || !empty($data['PropertyPrice'])){
					$property_asset_id = false;
					if(!empty($data['PropertyAsset'])){
						if(empty($data['PropertyAsset']['id'])){
							$property_asset = $this->PropertyAsset->getData('first', array(
								'conditions' => array(
									'PropertyAsset.property_id' => $property_id
								)
							));

							if(!empty($property_asset['PropertyAsset']['id'])){
								$property_asset_id = $property_asset['PropertyAsset']['id'];
							}
						}else{
							$property_asset_id = $data['PropertyAsset']['id'];
						}
					}

					$this->PropertyAsset->doSave($data, false, false, $property_id, $property_asset_id);
				}

				$this->User->Notification->doSave(array(
					'Notification' => array(
						'user_id' => $user_id,
						'name' => sprintf(__('Revisi Properti dengan ID %s telah berhasil disetujui'), $mls_id),
						'link' => array(
							'controller' => 'properties',
							'action' => 'index',
							'keyword' => $mls_id,
							'admin' => true,
						),
					),
				));
				$result = array(
					'msg' => __('Berhasil melakukan persetujuan revisi properti.'),
					'status' => 'success'
				);
			}
		}

		return $result;
	}

	function removeValidate () {
		$this->validator()->remove('certificate_id');

		$this->PropertyAsset->validator()
		->remove('lot_size', 'numeric')
		->remove('building_size', 'numeric')
		->remove('beds')
		->remove('baths');

		$this->validator()->remove('client_id');
	}

	function _callBeforeViewEdit ( $value, $data_revision ) {
		$id = !empty($value['Property']['id'])?$value['Property']['id']:false;
		$user_id = !empty($value['Property']['user_id'])?$value['Property']['user_id']:false;

		if( $value['Property']['property_action_id'] == 2 ){
			$value = $this->PropertyPrice->getRequestData($value, $id);
		}

		if( Configure::read('User.admin') && !empty($user_id) ){
			$value = $this->User->getMerge($value, $user_id, false, 'Agent');
			$value = $this->User->UserProfile->getMerge($value, $user_id, false, 'AgentProfile');

			if(!empty($value['Agent']['email'])){
				$value['Property']['agent_email'] = $value['Agent']['email'];
			}
		}
		if( !empty($value['Property']['client_id']) ){
			$client_id = $value['Property']['client_id'];
			$value = $this->User->getMerge($value, $client_id, false, 'Client');
			$userClient = $this->User->UserClient->getData('first', array(
	            'conditions' => array(
	                'UserClient.user_id' => $client_id,
	                'UserClient.company_id' => Configure::read('Principle.id'),
	            ),
			));

			if(!empty($userClient) && !empty($value['Client']['email'])){
				$client_email = Common::hashEmptyField($value, 'Client.email');
				$client_full_name = Common::hashEmptyField($userClient, 'UserClient.full_name');

				$value['Property']['client_email'] = sprintf('%s | %s', $client_email, $client_full_name);
				$value['UserClient'] = Common::hashEmptyField($userClient, 'UserClient');
				
				/*untuk kebutuhan API*/
				$value['UserClient']['email'] = $client_email;
				/*END - untuk kebutuhan API*/
			}
		}

		// merge userintegratedconfig
		if( !empty($value['Property']['user_id']) ){
			$user_id = $value['Property']['user_id'];
			$userIntegratedConfig = $this->User->UserIntegratedConfig->getData('first', array(
	            'conditions' => array(
	                'UserIntegratedConfig.user_id' => $user_id,
	            ),
			));

			if (!empty($userIntegratedConfig)) {
				$value = array_merge($value, $userIntegratedConfig);
			}
		}

		if( !isset($value['PropertyFacility']) ) {
			$value = $this->PropertyFacility->getRequestData($value, $id);
		}
		if( !isset($value['PropertyPointPlus']) ) {
			$value = $this->PropertyPointPlus->getRequestData($value, $id);
		}

		return $value;
	}

	function _callPropertyMerge ( $data = array(), $id = false, $fieldName = false, $client_id = false, $agent_id = NULL ) {
		if( !empty($id) ) {
			$conditions = array(
				$fieldName => $id,
			);

			if( !empty($agent_id) ) {
				$conditions['Property.user_id'] = $agent_id;
			}

			$property = $this->getData('first', array(
				'conditions' => $conditions,
			));
			$property = $this->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAsset',
					'PropertyAddress',
				),
			));

			if( !empty($property) ) {
				$user_id = !empty($property['Property']['user_id'])?$property['Property']['user_id']:false;

				if( empty($client_id) && !is_numeric($client_id) ) {
					$client_id = !empty($property['Property']['client_id'])?$property['Property']['client_id']:false;
				}

				$property = $this->User->UserClient->getMerge($property, $client_id, false, 'Client');
				$property = $this->User->getMerge($property, $user_id, false, 'User');
				$data = array_merge($data, $property);
			}
		}

		return $data;
	}

	function getTargetID($id_target){
		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id_target,
			),
		), array(
			'admin_mine' => false,
			'sold_mine' => false,
			'company' => false
		));

		$result = array();

		if(!empty($property)){
			$user_id = !empty($property['Property']['user_id']) ? $property['Property']['user_id'] : false;
			$property = $this->User->getMerge($property, $user_id);

			$property_id_target = !empty($property['Property']['property_id_target']) ? $property['Property']['property_id_target'] : false;
			$user_id_target = !empty($property['User']['user_id_target']) ? $property['User']['user_id_target'] : false;
			$user_id = !empty($property['User']['id']) ? $property['User']['id'] : false;
			$property_id = !empty($property['Property']['id']) ? $property['Property']['id'] : false;

			$result['data_target'] = array(
				'property_id_target' => $property_id_target,
				'user_id_target' => $user_id_target,
				'id' => $property_id,
				'user_id' => $user_id
			);
		}

		return $result;
	}

	function _callRemoveCachePopuler () {
		$companyData = Configure::read('Config.Company.data');
		$userID = Common::hashEmptyField($companyData, 'User.id', 0);
		$companyID = Common::hashEmptyField($companyData, 'UserCompany.id', 0);
		$cacheName = __('Property.Populers.%s.%s', $companyID, $userID);
		
		Cache::delete($cacheName, 'default');
	}
	
	public function doActivate($id, $value = false, $activate = true){
		$mlsid = !empty($value['Property']['mls_id'])?$value['Property']['mls_id']:false;
		$user_admin = Configure::read('User.admin');

		$this->id = $id;

		if($activate){
			$this->set('status', 1);
			$this->set('sold', 0);
			$this->set('published', 1);
			$this->set('deleted', 0);
			$this->set('inactive', 0);

			$msg = sprintf(__('meng-aktifkan kembali properti dengan id #%s'), $mlsid);
		}else{
			$this->set('status', 0);
			$this->set('deleted', 0);
			$this->set('active', 0);
			$this->set('inactive', 0);
			$this->set('in_update', 0);
			$this->set('published', 1);
			$this->set('inactive_date', date('Y-m-d H:i:s'));

			if($user_admin){
				$msg = sprintf(__('admin meng-nonaktifkan properti dengan id #%s'), $mlsid);
			}else{
				$msg = sprintf(__('meng-nonaktifkan properti dengan id #%s'), $mlsid);
			}
		}
			
		$this->set('change_date', date('Y-m-d H:i:s'));

		if( !empty($user_admin) ) {
			$this->set('active', 1);
		} else {
			$this->set('active', 0);
		}

		if($this->save()){
			$this->_callRemoveCachePopuler();
			$msg = sprintf(__('Berhasil %s'), $msg);
			$error = 0;
		}
		else{
			$msg = sprintf(__('Gagal %s'), $msg);
			$error = 1;
		}

		$result = array(
			'msg' => $msg, 
			'status' => $error ? 'error' : 'success', 
			'Log' => array(
				'activity' => $msg,
				'document_id' => $id,
				'error' => $error
			),
		);

		return $result;
	}

	function property_fix($id, $with_mls_id = false){
		$property = $this->getData('first', array(
			'conditions' => array(
				'Property.id' => $id,
			)
		), array(
			'admin_mine' => false,
			'sold_mine' => false,
			'company' => false
		));

		$property = $this->getDataList($property, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'PropertyMedias',
				'PropertyVideos',
				'PropertyFacility',
				'PropertyPointPlus'
			),
		));

		if(!empty($property)){
			$type_property = !empty($property['PropertyType']['name']) ? $property['PropertyType']['name'] : '';
			$action_property = !empty($property['PropertyAction']['name']) ? $property['PropertyAction']['name'] : '';
			$city = !empty($property['PropertyAddress']['City']['name']) ? $property['PropertyAddress']['City']['name'] : '';
			$subarea = !empty($property['PropertyAddress']['Subarea']['name']) ? $property['PropertyAddress']['Subarea']['name'] : '';
			$region = !empty($property['PropertyAddress']['Region']['name']) ? $property['PropertyAddress']['Region']['name'] : '';
			$subareaZip = !empty($property['Subarea']['zip'])?$property['Subarea']['zip']:'';
			$contentZip = !empty($property['PropertyAddress']['zip'])?$property['PropertyAddress']['zip']:$subareaZip;
			
			$title_content = sprintf('%s %s di %s, %s', $action_property, $type_property, $subarea, $city);
			$area_content = sprintf('%s, %s, %s, %s', $subarea, $city, $region, $contentZip);
			$keyword_content = sprintf('%s %s %s', $title_content, $region, $contentZip);
			
			$property['PropertyContent'] = array(
				'title' => $title_content,
				'area' => $area_content,
				'keyword' => $keyword_content,
			);
			
			$arr = array(
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'PropertyMedias',
				'PropertyVideos'
			);

			foreach ($arr as $key => $value) {
				if(!empty($property[$value]['id'])){
					unset($property[$value]['id']);
				}
			}

			if(empty($with_mls_id)){
				unset($property['Property']['mls_id']);
			}
			
			unset($property['Property']['id']);
			unset($property['Property']['photo']);

			$property['with_no_photo'] = true;
		}

		return $property;
	}

	function _callAllowRefreshAll () {
		return $this->getData('count', array(
			'conditions' => array(
				'OR' => array(
					"DATE_FORMAT(Property.refresh_date, '%Y-%m-%d') <>" => date('Y-m-d'),
					'Property.refresh_date' => NULL,
				),
			),
		), array(
			'admin_mine' => true,
		));
	}

	public function isAllowRefresh(){
		$options = $this->getData('paginate', array(
			'fields'		=> array('Property.id'), 
			'conditions'	=> array(
				'DATE_FORMAT(Property.refresh_date, "%Y-%m-%d") <' => date('Y-m-d'),
			),
		), array(
			'mine' => true,
		));

	//	syarat minimal muncul button refresh all => ada property yang refresh datenya kurang dari hari ini
	//	jadi cukup find first aja (speedup)
	//	order ini bikin berat
		$options	= Hash::remove($options, 'order');
		$result		= $this->find('first', $options);

		return !empty($result);
	}

	function Sold($data, $property_id = false){
		$flag = false;
		$msg = sprintf(__('Property ID %s Terjual'), $property_id);
		
		if(!empty($property_id)){
			$property = !empty($data['Property'])?$data['Property']:false;
			$property_sold = !empty($data['PropertySold'])?$data['PropertySold']:false;

			$set_data = array(
				'Property' => array(
					'sold' => true,
				),
				'PropertySold' => $property_sold,
			);

			$this->id = $property_id;

			if($this->save($set_data)) {
				$set_data['PropertySold']['property_id'] = $property_id;
				$result = $this->PropertySold->doSave($set_data, $property_id);
			}else{
				$msg = sprintf(__('Gagal %s'), $msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'document_id' => $property_id,
						'error' => 1,
					),
				);
			}
		}else{
			$result['Log'] = array(
				'activity' => $msg,
				'document_id' => $property_id,
				'error' => 1,
			);
		}

		return $result;
	}

	function _callGetAgentProperty ( $data ) {
		$is_admin   = Configure::read('User.admin');

		if( empty($data['Property']['user_id']) ) {
			if( !empty($is_admin) ){
				$email = !empty($data['Property']['agent_email'])?$data['Property']['agent_email']:false;
				$data['Property']['user_id'] = '';

				if(!empty($email)){
					$user = $this->User->getData('first', array(
						'conditions' => array(
							'User.email' => $email
						)
					), array(
						'company' => true,
						'admin' => true,
						'role' => 'agent',
					));

					if(!empty($user['User']['id'])){
						$data['Property']['user_id'] = $user['User']['id'];
					}
				}
			} else {
				$data['Property']['user_id'] = Configure::read('User.id');	
			}
		}

		return $data;
	}

	function doChangeStatusCategory( $data, $id, $options = array() ) {
		$result = false;

		$validate_status_category = $this->filterEmptyField($options, 'validate_status_category', false);
		$property_status_id = $this->filterEmptyField($data, 'Property', 'property_status_id', null);
		$default_msg = __('mengganti kategori properti');

		if ($validate_status_category) {
			if (empty($property_status_id)) {
				$this->validator()->add('property_status_id', array(
		            'notempty' => array(
		                'rule' => 'notempty',
		                'message' => __('Mohon pilih jenis properti')
		            ),
		        ));
			}

			$this->id = $id;
	        $this->set($data);

			if( $this->save() ) {

				$this->updateAll(array(
					'Property.property_status_id' => $property_status_id,
				), array(
					'Property.id' => $id,
				));

				$msg = sprintf(__('Berhasil %s'), $default_msg);

				$result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $data,
						'document_id' => $id,
					),
				);
			} else {
				$msg = sprintf(__('Gagal %s, mohon lengkapi semua data yang diperlukan'), $default_msg);

				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'data' => $data,
						'document_id' => $id,
						'error' => 1,
					),
				);
			}
		} else {
			$result = true;
		}


		return $result;
	}

	function doSaveAll( $data, $validate = false,  $options = array()) {
		$options	= (array) $options;
		$isEasyMode	= Common::hashEmptyField($options, 'is_easy_mode');

		$result	 = false;
		$group_id   = Configure::read('User.group_id');
		$is_admin   = Configure::read('User.admin');
		$approval   = Configure::read('Config.Approval.Property');
		$mls_id	 = !empty($data['Property']['mls_id'])?$data['Property']['mls_id']:false;
		$pageConfig	 = !empty($data['PageConfig']) ? $data['PageConfig'] : false;
		$id = !empty($data['Property']['id']) ? $data['Property']['id'] : false;

		$session_id = $this->filterEmptyField($data, 'Property', 'session_id');
		$photo_name = $this->filterEmptyField($data, 'Property', 'photo');
		$photo_id 	= $this->filterEmptyField($data, 'PropertyMedias', 'id');

		if ( !empty($data) ) {
			$isPersonalPage	= Configure::read('Config.Company.is_personal_page');

			if($isPersonalPage){
			//	personal page ga butuh approval
				$approval = false;
			}

			if(!empty($photo_id)){
				unset($data['PropertyMedias']);
			}

			$data = $this->_callGetAgentProperty($data);
			$data['Property']['change_date'] = date('Y-m-d H:i:s');

			if(empty($id)){
				$data['Property']['refresh_date'] = date('Y-m-d H:i:s');
			}

			if( empty($id) && (!empty($is_admin) || empty($approval)) ) {
				$data['Property']['active'] = 1;
			}

			if($isEasyMode){
				if(empty($id)){
				//	title dari hasil generate
					$this->validator()->remove('title');
				}
				else{
				//	kalo pake ini pas create malah error -_-'
					$this->validator()->remove('title', 'validateTitleProperty');
				}

				$this->validator()->remove('description');
			}

			$flag = $this->saveAll($data, array(
                'validate' => 'only',
            ));

            if( !empty($flag) ) {
				if( !empty($validate) ) {
					$pageConfig = !empty($data['PageConfig']) ? $data['PageConfig'] : false;
					$this->PageConfig->doSaveMany($pageConfig, $id);

					$msg = __('Berhasil menyimpan informasi dasar properti Anda');
					$result = array(
						'msg' => $msg,
						'status' => 'success',
					);
				} else {
					$is_edit = !empty($id);

	            	if( !empty($id) ) {
		            	$this->PropertyFacility->deleteAll(array(
							'PropertyFacility.property_id' => $id,
						));
						$this->PropertyPointPlus->deleteAll(array(
							'PropertyPointPlus.property_id' => $id,
						));
						$this->PageConfig->deleteAll(array(
							'PageConfig.page_id' => $id,
							'PageConfig.page_type' => 'property',
						));
						$this->PropertyPrice->deleteAll(array(
							'PropertyPrice.property_id' => $id,
						));
		            }

					$flag = $this->saveAll($data);

					if( !empty($flag) ) {
						// Ini karena company_id yg di UserClient menggunakan user_id principle
						$data['Property']['company_id'] = Configure::read('Principle.id');

						$data = $this->User->addClient($data);
						
						$id = $this->id;

						if( empty($is_edit) ) {
							$this->PropertyMedias->doSavePhoto( $id, $session_id, $photo_name, $photo_id );
						}

						if( !empty($data['Property']['client_id']) ) {
							$this->set('client_id', $data['Property']['client_id']);
							$this->id = $id;
							$this->save();
						}

						$msg = __('Berhasil menyimpan informasi dasar properti Anda');
						$result = array(
							'msg' => $msg,
							'status' => 'success',
							'id' => $id,
							'Log' => array(
								'activity' => $msg,
								'document_id' => $id,
							),
						);

						if( $group_id == 2 ) {
							if(empty($mls_id) && !empty($id)){
								$data_property = $this->findById($id);

								$mls_id = Common::hashEmptyField($data_property, 'Property.mls_id');
							}

							if(!empty($mls_id)){
								$notifMsg = sprintf(__('Penambahan Properti dengan ID %s pada tanggal %s, harap lakukan peninjauan'), $mls_id, date('d M Y'));
							}else{
								$notifMsg = sprintf(__('Penambahan Properti pada tanggal %s, harap lakukan peninjauan'), date('d M Y'));
							}
							
							$result['Notification'] = array(
								'user_id' => 'admin_company',
								'name' => $notifMsg,
								'link' => array(
									'controller' => 'properties',
									'action' => 'index',
									'keyword' => $mls_id,
									'admin' => true,
								),
							);
						}
					}
				}
			} else {
				$validationErrors = array();

				if(!empty($this->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->validationErrors);
				}

				$msg = __('Gagal menyimpan properti Anda, mohon lengkapi semua data yang diperlukan');

				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $data,
						'error' => 1,
					),
					'validationErrors' => $validationErrors
				);
			}
		}
		return $result;
	}

	function doRemoveCategory( $id ) {
		
		$result = false;
		$data_property = $this->getData('first', array(
        	'conditions' => array(
				'Property.id' => $id,
			),
		));

		if ( !empty($data_property) ) {

			$flag = $this->updateAll(array(
				'Property.property_status_id' => null,
			), array(
				'Property.id' => $id,
			));

            if( $flag ) {
				$msg = __('Berhasil me-reset data kategori properti');
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $data_property,
					),
				);
            } else {
				$msg = __('Gagal me-reset data properti');
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $data_property,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal me-reset kategori properti. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	public function doTransfer($recordID = null, $companyID = null){
		$status		= 'error';
		$message	= __('Tidak ada data untuk diproses');

		if($recordID){
			$recordID	= is_array($recordID) ? $recordID : array($recordID);
			$conditions	= array(
				sprintf('%s.id', $this->alias) => $recordID,
			);

			$records = $this->getData('all', array('conditions' => $conditions), array(
				'mine'		=> true, 
				'company'	=> false, 
			));

			if($records){
				$authGroupID	= Configure::read('User.data.group_id');
				$companyData	= Configure::read('Config.Company.data'); 
				$companyID		= Common::hashEmptyField($companyData, 'UserCompany.id', $companyID);
				$companyName	= Common::hashEmptyField($companyData, 'UserCompany.name');

				if($companyID){
					$flag = $this->updateAll(array(
						sprintf('%s.company_id', $this->alias) => $companyID,
					), $conditions);

					if($flag){
						$status		= 'success';
						$message	= __('Berhasil mentransfer properti terpilih ke %s', $companyName);
					}
					else{
						$message = __('Gagal mentransfer properti.');
					}
				}
				else{
					$message = __('Gagal mentransfer properti, Anda belum terdaftar di perusahaan agensi.');
				}
			}
			else{
				$message = __('Data tidak ditemukan.');
			}
		}

		$result = array(
			'status'	=> $status, 
			'msg'		=> $message, 
			'data'		=> $recordID, 
			'Log'		=> array(
				'activity'	=> $message,
				'old_data'	=> $recordID,
				'error'		=> $status == 'error',
			),
		);

		return $result;
	}

	public function mergeMedia($data = array(), $options = array()){
		$data		= (array) $data;
		$options	= (array) $options;

		if($data){
			$isMultiple	= Hash::numeric(array_keys($data));
			$data		= $isMultiple ? $data : array($data);

			foreach($data as $key => &$property){
				$recordID = Common::hashEmptyField($property, 'Property.id');
				$isActive = Common::hashEmptyField($property, 'Property.active');

				if($recordID){
					$status		= $isActive ? 'active' : 'all';
					$mediaData	= $this->PropertyMedias->getMerge(array(), $recordID, 'all', $status);
					$mediaData	= $this->PropertyVideos->getMerge($mediaData, $recordID, 'all', $status);

					$property = array_replace_recursive($property, $mediaData);
				}
			}

			$data = $isMultiple ? $data : array_shift($data);
		}

		return $data;
	}
}
?>