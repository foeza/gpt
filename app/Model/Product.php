<?php
App::uses('AppModel', 'Model');
/**
 * Product Model
 *
 * @property Project $Project
 * @property Company $Company
 * @property User $User
 * @property Region $Region
 * @property City $City
 * @property Subarea $Subarea
 * @property Mls $Mls
 * @property ProductAddress $ProductAddress
 * @property ProductConfig $ProductConfig
 * @property ProductDocument $ProductDocument
 * @property ProductFacility $ProductFacility
 * @property ProductLead $ProductLead
 * @property ProductMedia $ProductMedia
 * @property ProductUnitView $ProductUnitView
 * @property ProductUnit $ProductUnit
 * @property ProductVideo $ProductVideo
 * @property ProductView $ProductView
 */
class Product extends AppModel {

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'mls_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Kode produk harap dimasukkan'
			),
			'validateUniqueCode' => array(
				'rule' => array('validateUniqueCode'),
				'message' => 'Kode produk sudah terdaftar'
			),
			'alphaNumeric' => array(
                'rule' => 'alphaNumeric',
                'required' => true,
                'message' => 'Kode hanya boleh berupa huruf dan angka'
            ),
		),
		'project_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Project id harus berupa angka'
			),
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Project id harap dimasukkan'
			),
		),
		'company_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Company id harus berupa angka',
			),
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Company id harap dimasukkan'
			),
		),
		'user_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'User id harus berupa angka',
			),
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'User id harap dimasukkan'
			),
		),
		'name' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Mohon masukkan nama produk',
				'required' => true,
			),
		),
		'description' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Mohon masukkan deskripsi produk',
				'required' => true,
			),
			'validationPlainMinChar' => array(
				'rule' => array('validationPlainMinChar', 30),
				'message' => 'Minimal isi deskripsi minimal 30 karakter',
				'required' => true,
			),
		),
		'region_id' => array(
			'validateLocation' => array(
				'rule' => array('validateLocation'),
				'message' => 'Mohon pilih provinsi',
				'required' => true,
			),
		),
		'city_id' => array(
			'validateLocation' => array(
				'rule' => array('validateLocation'),
				'message' => 'Mohon pilih kota',
				'required' => true,
			),
		),
		'subarea_id' => array(
			'validateLocation' => array(
				'rule' => array('validateLocation'),
				'message' => 'Mohon pilih area',
				'required' => true,
			),
		),
		'zip' => array(
			'validateLocation' => array(
				'rule' => array('validateLocation'),
				'message' => 'Mohon pilih kode pos',
				'required' => true,
			),
		),
		'address' => array(
			'validateLocation' => array(
				'rule' => array('validateLocation'),
				'message' => 'Mohon masukkan alamat',
				'required' => true,
			),
		),
		'media_id' => array(
			'notBlank' => array(
				'rule' => array('notBlank'),
				'message' => 'Mohon masukkan foto produk dan pilih foto utama',
				'required' => true,
			),
		)
	);

	// The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Project' => array(
			'className' => 'Project',
			'foreignKey' => 'project_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Company' => array(
			'className' => 'Company',
			'foreignKey' => 'company_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Region' => array(
			'className' => 'Region',
			'foreignKey' => 'region_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'City' => array(
			'className' => 'City',
			'foreignKey' => 'city_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Subarea' => array(
			'className' => 'Subarea',
			'foreignKey' => 'subarea_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProductStatusSell' => array(
			'className' => 'ProductStatusSell',
			'foreignKey' => 'product_status_sell_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'ProductStatusBuild' => array(
			'className' => 'ProductStatusBuild',
			'foreignKey' => 'product_status_build_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasOne = array(
		'ProductAddress' => array(
			'className' => 'ProductAddress',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ProductConfig' => array(
			'className' => 'ProductConfig',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

	public $hasMany = array(
		'ProductFacility' => array(
			'className' => 'ProductFacility',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ProductUnit' => array(
			'className' => 'ProductUnit',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ProductView' => array(
			'className' => 'ProductView',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ProductUnitRelation' => array(
			'className' => 'ProductUnitRelation',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'ProductUnitStock' => array(
			'className' => 'ProductUnitStock',
			'foreignKey' => 'product_id',
			'dependent' => false,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
		'DocumentMedia' => array(
		//	'className' => 'DocumentMedia',
			'foreignKey' => 'document_id',
		//	'conditions' => array(
		//		'DocumentMedia.document_type' => 'product'
		//	)
		),
	);

	public function __construct($id = false, $table = null, $ds = null){
		parent::__construct($id, $table, $ds);

		$is_admin_site = Configure::read('__Site.Global.Config.is-admin-site');

		if( !empty($is_admin_site) ) {
			$field = sprintf('CONCAT(%s.mls_id, \' - \', %s.name)', $this->alias, $this->alias);
		} else {
			$field = sprintf('%s.name', $this->alias);
		}

		$this->virtualFields = array_merge($this->virtualFields, array( 
			'title' => $field, 
			'label' => $field, 
		));
	}

/*
	public function beforeFind($query){
		parent::beforeFind($query);

	//	untuk autocomplete dan page data
		$this->virtualFields = array_merge($this->virtualFields, array( 
			'label' => sprintf('CONCAT(%s.mls_id, \' - \', %s.name)', $this->alias, $this->alias)
		));

		return $query;
	}
*/

	function validateUniqueCode($data){
		$global = $this->data;

		$result = true;
		if(!empty($data['mls_id'])){
			$id = Common::hashEmptyField($global, 'Product.id');

			$conditions = array(
				'Product.mls_id' => $data['mls_id']
			);

			if(!empty($id)){
				$conditions['Product.id <>'] = $id;
			}
		
			$check_data = $this->getData('count', array(
				'conditions' => $conditions
			), array(
				'status' => 'all',
				'company' => false
			));

			if($check_data > 0){
				$result = false;
			}
		}

		return $result;
	}

	function validateLocation($data){
		$global_data = $this->data;

		$key_field = key($data);

		$is_same_address_produk = Common::hashEmptyField($global_data, 'Product.is_same_address_produk');

		$result = true;
		if(empty($is_same_address_produk) && empty($data[$key_field])){
			$result = false;
		}

		return $result;
	}

	function validationPlainMinChar($data, $min_char = 30){
		$key_field = key($data);

		$result = true;
		$string = Common::hashEmptyField($data, $key_field);

		if(!empty($string)){
			$string = strip_tags($string);
			$count_len = strlen($string);

			if($count_len < $min_char){
				$result = false;	
			}
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$keyword = Common::hashEmptyField($data, 'named.keyword', false, array(
			'addslashes' => true,
		));
		$name = Common::hashEmptyField($data, 'named.name', false, array(
        	'addslashes' => true,
    	));
    	$sold = Common::hashEmptyField($data, 'named.sold', false, array(
        	'addslashes' => true,
    	));
    	$product_unit_count = Common::hashEmptyField($data, 'named.product_unit_count', false, array(
        	'addslashes' => true,
    	));
    	$product_unit_stock_count = Common::hashEmptyField($data, 'named.product_unit_stock_count', false, array(
        	'addslashes' => true,
    	));
		$mls_id = Common::hashEmptyField($data, 'named.mls_id', false, array(
        	'addslashes' => true,
    	));
		$promo_title = Common::hashEmptyField($data, 'named.promo_title', false, array(
        	'addslashes' => true,
    	));
		$tag_line = Common::hashEmptyField($data, 'named.tag_line', false, array(
        	'addslashes' => true,
    	));
    	$modified_from = Common::hashEmptyField($data, 'named.modified_from', false, array(
            'addslashes' => true,
        ));
        $modified_to = Common::hashEmptyField($data, 'named.modified_to', false, array(
            'addslashes' => true,
        ));
    	$date_from = Common::hashEmptyField($data, 'named.date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = Common::hashEmptyField($data, 'named.date_to', false, array(
            'addslashes' => true,
        ));
        $publish_from = Common::hashEmptyField($data, 'named.publish_from', false, array(
            'addslashes' => true,
        ));
        $publish_to = Common::hashEmptyField($data, 'named.publish_to', false, array(
            'addslashes' => true,
        ));

        $status_selling = Common::hashEmptyField($data, 'named.status_selling', false, array(
            'addslashes' => true,
        ));
        $status_building = Common::hashEmptyField($data, 'named.status_building', false, array(
            'addslashes' => true,
        ));
    	
    	if(!empty($sold)){
    		if($sold == 'active'){
    			$sold = 1;
    		}else{
    			$sold = 0;
    		}
    		
    		$default_options['conditions']['Product.sold'] = $sold;
    	}
		if( !empty($name) ) {
			$default_options['conditions']['Product.name LIKE'] = '%'.$name.'%';
		}
		if( !empty($mls_id) ) {
			$default_options['conditions']['Product.mls_id LIKE'] = '%'.$mls_id.'%';
		}
		if( !empty($promo_title) ) {
			$default_options['conditions']['Product.promo_title LIKE'] = '%'.$promo_title.'%';
		}
		if( !empty($tag_line) ) {
			$default_options['conditions']['Product.tag_line LIKE'] = '%'.$tag_line.'%';
		}
		if( !empty($product_unit_count) ) {
			$default_options['conditions']['Product.product_unit_count'] = $product_unit_count;
		}
		if( !empty($product_unit_stock_count) ) {
			$default_options['conditions']['Product.product_unit_stock_count'] = $product_unit_stock_count;
		}
		if( !empty($status_selling) ) {
			$default_options['conditions']['Product.product_status_sell_id'] = $status_selling;
		}
		if( !empty($status_building) ) {
			$default_options['conditions']['Product.product_status_build_id'] = $status_building;
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(Product.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(Product.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}

		if( !empty($date_from) ) {
        	$default_options['conditions']["DATE_FORMAT(Product.created, '%Y-%m-%d') >="] = $date_from;
	        
			if( !empty($date_to) ) {
	        	$default_options['conditions']["DATE_FORMAT(Product.created, '%Y-%m-%d') <="] = $date_to;
	        }
        }

        if( !empty($publish_from) ) {
        	$default_options['conditions']["DATE_FORMAT(Product.publish_date, '%Y-%m-%d') >="] = $publish_from;
	        
			if( !empty($publish_to) ) {
	        	$default_options['conditions']["DATE_FORMAT(Product.publish_date, '%Y-%m-%d') <="] = $publish_to;
	        }
        }

		if(!empty($keyword)){
			$default_options['conditions'][]['OR'] = array(
				sprintf('%s.mls_id LIKE', $this->alias) => '%'.$keyword.'%', 
				sprintf('%s.name LIKE', $this->alias) => '%'.$keyword.'%', 
				sprintf('CONCAT(%s.mls_id, " - ", %s.name) LIKE', $this->alias, $this->alias) => '%'.$keyword.'%', 
			);
		}

	//	debug($default_options);exit;

		return $default_options;
	}

	function getData( $find = 'all', $options = array(), $elements = array() ){
    	$role = Common::hashEmptyField($elements, 'role');
    	$status = Common::hashEmptyField($elements, 'status');
    	$scheduled_status = Common::hashEmptyField($elements, 'scheduled_status', '');
    	$company = Common::hashEmptyField($elements, 'company', true, array(
    		'isset' => true,
		));
    	$document_type = Common::hashEmptyField($elements, 'document_type', true, array(
    		'isset' => true,
		));
    	$project_id = Common::hashEmptyField($elements, 'project_id', Configure::read('Global.Data.Project.id'), array(
    		'isset' => true,
		));

    	$company_id = (int) Common::hashEmptyField($elements, 'company_id', Configure::read('Global.Data.Project.company_id'), array(
    		'isset' => true,
		));
    	$is_super_admin = Configure::read('__Site.is_super_admin');

    	$User = Configure::read('Config.User');

		$user_id = Common::hashEmptyField($User, 'id');

		$default_options = array(
			'contain'=> array(), 
			'conditions'=> array(
				'Product.document_type' => 'publish',
				'Product.status' => 1
			),
			'order'=> array(
				'Product.created' => 'DESC'
			),
		);

		if( !empty($project_id) ) {
			$default_options['conditions']['Product.project_id'] = $project_id;
		}

		if($company == true && $status != 'all_data_company'){
			$default_options['conditions']['Product.company_id'] = $company_id;
		}

		switch ($status) {
			case 'active':
				$default_options['conditions']['Product.active'] = 1;
				break;
			case 'inactive':
				$default_options['conditions']['Product.status'] = 1;
				$default_options['conditions']['Product.active'] = 0;
				break;
			case 'draft':
				$default_options['conditions']['Product.active'] = 1;
				$default_options['conditions']['Product.document_type'] = 'draft';
				$default_options['conditions']['Product.user_id'] = $user_id;
				break;
			case 'available':
				$default_options['conditions']['Product.active'] = 1;
				$default_options['conditions'][]['OR'] = array(
					'Product.product_status_sell_id <>' => 3,
					'Product.product_status_sell_id' => NULL,
				);
				break;
			case 'all_data_company':
				$company_id = Common::_callCompanyId();
				$default_options = $this->callAllActionDataCompany(true, $company_id, $default_options, 'Product.company_id', $this->Company);
				// $default_options['conditions']['Product.active'] = 1;
				break;
		}

		$current_date = date('Y-m-d H:i:s');

		switch ($scheduled_status) {
			case 'scheduled':
				$default_options['conditions']['OR'] = array(
					'DATE_FORMAT(Product.schedule_date, \'%Y-%m-%d %H:%i:%s\') > ' => $current_date
				);
				break;
			case 'onair':
				$default_options['conditions']['OR'][] = array(
					'Product.schedule_date' => null
				);

				$default_options['conditions']['OR'][] = array(
					'DATE_FORMAT(Product.schedule_date, \'%Y-%m-%d %H:%i:%s\') <= ' => $current_date
				);
				break;
		}

		if(empty($document_type)){
			unset($default_options['conditions']['Product.document_type']);
		}

		return $this->merge_options($default_options, $options, $find);
	}

	function getMerge( $data, $id, $is_root = false ){
    	if( empty($data['Product']) ) {
			$value = $this->getData('first', array(
				'conditions'=> array(
					'Product.id' => $id, 
					'Product.status' => 1, 
				),
			));

			if( !empty($value) && !$is_root ){
				$data = array_merge($data, $value);
			}
		}

        return $data;
	}

	public function doSave($data, $id = false, $draft = false ){
		$result = false;

		if ( !empty($data) ) {

			$other_facility = array();
			if(!empty($data['ProductFacility']['other_text'])){
				$other = Common::formatHasManyInput('ProductFacility.other_text', $data, false, array(
					'facility_id' => -1
				));

				$other_facility = Common::hashEmptyField($other, 'ProductFacility', array());
			}
			
			$data = Common::formatHasManyInput('ProductFacility.facility_id', $data);

			$facility = Common::hashEmptyField($data, 'ProductFacility', array());

			if(!empty($facility) || !empty($other_facility)){
				$facility = array_merge($facility, $other_facility);
			}

			if(!empty($facility)){
				$data['ProductFacility'] = $facility;
			}

			if(!$draft){
				$flag = $this->saveAll($data, array(
	                'validate' => 'only',
	            ));
			}else{
				$flag = true;
			}

			if(!empty($draft)){
				$data['Product']['document_type'] = 'draft';
				$validate = false;
			}else{
				$data['Product']['document_type'] = 'publish';
				$validate = true;
			}

			if( $flag ) {
				if( !empty($id) ) {
					$this->ProductFacility->deleteAll(array(
						'ProductFacility.product_id' => $id,
					));
					$this->DocumentMedia->deleteAll(array(
						'DocumentMedia.document_id' => $id,
						'DocumentMedia.document_type' => 'product',
					));
				}

	            $flag = $this->saveAll($data, array('validate' => $validate));

				if( $flag ) {
					if(!empty($draft)){
						$msg = __('Berhasil menyimpan informasi produk ke dalam draft');
						$id = $this->id;

						$result = array(
							'msg' => $msg,
							'status' => 'success',
							'id' => $id
						);
					}else{
						$msg = __('Berhasil menyimpan informasi produk');
						$id = $this->id;

						$result = array(
							'msg' => $msg,
							'status' => 'success',
							'id' => $id,
							'Log' => array(
								'activity' => $msg,
								'document_id' => $id,
							),
						);
					}
				}else{
					$msg = __('Gagal menyimpan produk. Mohon masukkan data-data yang dibutuhkan');
					$result = array(
						'msg' => $msg,
						'status' => 'error',
						'data' => $data,
					);
				}
			}else{
				$msg = __('Gagal menyimpan produk. Mohon masukkan data-data yang dibutuhkan');
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'data' => $data,
					'validationErrors' => $this->validationErrors
				);
			}
		}

		return $result;
	}

	function getCompleteData($id){
		$value = $this->getData('first', array(
			'conditions' => array(
				'Product.id' => $id
			)
		));

		if(!empty($value)){
			$value = $this->getMergeList($value, array(
				'contain' => array(
					'User',
					'ProductFacility',
					'ProductAddress',
					'ProductConfig',
					'DocumentMedia'
				)
			));
		}

		return $value;
	}

	function generateMLSID( $rand_code, $initial = false ) {

		if( empty($initial) ) {
			$user = Configure::read('Config.User');
			$initial = !empty($user['code'])?$user['code']:trim(substr($user['first_name'], 0, 5));
		}

		if( !empty($initial) ) {
			$initial = str_pad($initial, 5, '0', STR_PAD_RIGHT);
			$new_code = '';
			$flag = true;
			$mls_id = false;

			while ($flag) {
				$str_code = strtoupper(implode('', $rand_code));
				$mls_id = sprintf('%s%s', $initial, $str_code);

				$check = $this->getData('count', array(
					'conditions'=> array(
						'Product.mls_id'=> $mls_id,
					),
				), array(
					'status' => false,
					'company' => false,
				));
				
				if( empty($check) ) {
					$flag = false;
				}
			}
			return $mls_id;
		} else {
			return false;   
		}
	}

	function doDuplicate($id){
		$value = $this->getCompleteData($id);

		if(!empty($value)){
			$User = Configure::read('Config.User');

			$user_id 		= Common::hashEmptyField($User, 'id');
			$group_id 		= Common::hashEmptyField($User, 'group_id');
			$user_code 		= Common::hashEmptyField($User, 'code');

			$product_name 	= Common::hashEmptyField($value, 'Product.name', '');

			if($group_id > 10 && $group_id < 21){
				$value['Product']['user_id'] = (int) $user_id;
			}else{
				$user_code = Common::hashEmptyField($value, 'User.code');
			}

			$code = $this->createRandomNumber( 3, 'bcdfghjklmnprstvwxyz0123456789', 30);
			
			$value['Product']['mls_id'] = $this->generateMLSID($code, $user_code);
			$value['Product']['name'] = 'Copy - '.$product_name;
			$value['Product']['publish_date'] = date('Y-m-d H:i:s');
			
			$data_other_facility = Common::formatHasManyInput('ProductFacility.other_text', $value, true);

			$value = Common::formatHasManyInput('ProductFacility.facility_id', $value, true);
			$facility = Common::hashEmptyField($value, 'ProductFacility', array());
			$other_facility = Common::hashEmptyField($data_other_facility, 'ProductFacility', array());

			if(!empty($facility) || !empty($other_facility)){
				$facility = array_merge($facility, $other_facility);

				$value['ProductFacility'] = $facility;
			}

			$value = Common::formatHasManyMultipleInput(array(
				'DocumentMedia.company_id',
				'DocumentMedia.project_id',
				'DocumentMedia.media_id',
				'DocumentMedia.document_type',
				'DocumentMedia.document_sub_type'
			), $value, true);

			$value = Common::formatHasManyMultipleInput(array(
				'DocumentMedia.company_id',
				'DocumentMedia.project_id',
				'DocumentMedia.media_id',
				'DocumentMedia.document_type',
				'DocumentMedia.document_sub_type'
			), $value);

			$value = Common::_callUnset($value, array(
				'User',
				'Product' => array(
					'id',
					'product_unit_stock_count',
					'product_unit_count',
					'created',
					'modified',
				),
				'ProductAddress' => array(
					'id',
					'product_id',
					'created',
					'modified'
				),
				'ProductConfig' => array(
					'id',
					'product_id',
					'product_unit_count',
					'created',
					'modified'
				)
			));

			$result = $this->doSave($value);

			if(!empty($result['status']) && $result['status'] == 'success' ){
				$msg = __('Berhasil melakukan duplikasi produk');
				$id = $this->id;

				$result = array(
					'status' => 'success',
					'msg' => $msg,
					'Log' => array(
						'activity' => $msg,
						'document_id' => $id,
					)
				);
			}else{
				$result = array(
					'status' => 'error',
					'msg' => __('Gagal melakukan duplikasi produk')
				);
			}
		}else{
			$result = array(
				'status' => 'error',
				'msg' => __('Gagal melakukan duplikasi produk')
			);
		}

		return $result;
	}

	function doDelete( $id ) {
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'Product.id' => $id,
			),
		);

		$value = $this->getData('all', $options, array(
			'status' => 'all',
			'active' => 'all'
		));

		if ( !empty($value) ) {
			$default_msg = __('menghapus produk');

			$flag = $this->updateAll(array(
				'Product.status' => 0
			), array(
				'Product.id' => $id,
			));

            if( $flag ) {
            	$this->afterSave();
            	
				$msg = __('Berhasil %s', $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $id,
					),
				);
            } else {
				$result = array(
					'msg' => __('Gagal %s', $default_msg),
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal %s. Data tidak ditemukan', $default_msg),
				'status' => 'error',
			);
		}

		return $result;
	}

	function doToggle( $id, $active ) {	
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'Product.id' => $id,
			),
		);

		$value = $this->getData('all', $options, array(
			'status' => 'all',
			'active' => 'all'
		));

		if ( !empty($value) ) {
			if( !empty($active) ) {
				$default_msg = __('mengaktifkan produk');
				$rule = array(
					'Product.active' => 1
				);
			} else {
				$default_msg = __('nonaktifkan produk');
				$rule = array(
					'Product.active' => 0
				);
			}

			$flag = $this->updateAll($rule, array(
				'Product.id' => $id
			));

            if( $flag ) {
				$msg = __('Berhasil %s', $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $id,
					),
				);
            } else {
				$result = array(
					'msg' => __('Gagal %s', $default_msg),
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal %s. Data tidak ditemukan', $default_msg),
				'status' => 'error',
			);
		}

		return $result;
	}

	function doChangeStatus($value, $id, $field){
		$data = array();

		$access = false;
		if( in_array($field, array('product_status_build_id', 'product_status_sell_id')) ){
			$access = true;
		}

		if($access){
			$this->id = $id;

			if($this->saveField($field, $value)){
				if($value == 3){
					$this->sold = $id;

					$value_sold = 1;
				}else{
					$value_sold = 0;
				}

				$this->saveField('sold', $value_sold);

				$this->validateSold($id);

				$msg = __('Berhasil melakukan ubah status');

				$result = array(
					'status' => 'success',
					'msg' => $msg,
					'Log' => array(
						'activity' => sprintf('%s %s', $msg, $field),
						'document_id' => $id,
					)
				);
			}else{
				$result = array(
					'status' => 'error',
					'msg' => __('Gagal melakukan ubah status'),
					'validationErrors' => $this->validationErrors
				);
			}
		}else{
			$result = array(
				'status' => 'error',
				'msg' => __('Gagal melakukan ubah status'),
			);
		}

		return $result;
	}

	function getPageData ( $project_id, $options = array(), $elements = array() ) {
		$options = array_merge_recursive($options, array(
			'contain' => array(
				'Project', 
			), 
		));

		$values = $this->getData('all', $options, $elements);
		$values = $this->getMergeList($values, array(
			'contain' => array(
				'Region',
				'City',
				'Subarea',
				'DocumentMedia', 
			),
		));

		$result = $this->buildPageData($project_id, 'Project', 'Product', $values);
		return $result;
	}

	public function loadPageData($type = 'all', $options = array(), $elements = array()){
		$results = array();

	//	debug($options);exit;

		if(in_array($type, array('all', 'paginate'))){
			$contentType	= Common::hashEmptyField($elements, 'content_type');
			$options		= array_merge_recursive($options, array(
				'group'		=> array('Product.id'), 
				'contain'	=> array(
					'Project', 
				//	'ProductAddress', 
					'ProductUnitRelation', 
				),
			));

		//	hasMany ga bisa conditions yang di contain, jadi harus di bind ulang pake hasOne cuma untuk mancing, 
		//	makanya diatas ada group by
			$this->unbindModel(array(
				'hasMany' => array(
					'ProductUnitRelation', 
				), 
			));

			$this->bindModel(array(
				'hasOne' => array(
					'ProductUnitRelation' => array(
						'foreignKey' => 'product_id', 
					), 
				), 
			));

			$elements = Hash::remove($elements, 'content_type');
			$elements = array_replace($elements, array(
				'status' => 'active', 
			));

			$this->virtualFields['title'] = 'Product.name';
			$this->virtualFields['label'] = 'Product.name';

			$results = $this->getData($type, $options, $elements);

		//	debug($this->queryLog(true));
		//	debug($results);exit;

			if($type == 'all' && $contentType){
				$results = $this->loadAdditionalPageData($results);
				$results = $this->formatPageData($results, $contentType);
			//	debug($results);exit;
			}
		}

		return $results;
	}

	public function loadAdditionalPageData($records = array()){
		if($records){
		//	remove product unit relation, ini data kurang komplit dan formatnya hasOne
			$records = Hash::remove($records, '{n}.ProductUnitRelation');

		//	mancing virtual field Media
			$field = 'CASE WHEN ISNULL(%s.title) OR %s.title = "" THEN REPLACE(%s.alias, CONCAT(".", SUBSTRING_INDEX(%s.alias, ".", -1)), "") ELSE %s.title END';
			$field = sprintf($field, 'Media', 'Media', 'Media', 'Media', 'Media');

			$this->DocumentMedia->Media->virtualFields['title'] = $field;
			$this->DocumentMedia->Media->virtualFields['label'] = $field;

		//	replace dengan format hasMany
			$records = $this->getMergeList($records, array(
				'contain' => array(
					'ProductAddress', 
					'ProductUnitRelation' => array(
						'contain' => array(
							'ProductUnit' => array(
								'conditions' => array(
									'ProductUnit.active' => 1, 
									'ProductUnit.status' => 1, 
								)
							),  
						), 
					), 
					'Subarea', 
					'City', 
					'Region', 
					'ProductFacility' => array('Facility'), 
					'ProductStatusSell', 
					'ProductStatusBuild', 
				//	kalo tanpa format unit pake ini
					'DocumentMedia' => array(
						'contain'		=> array(
							'MediaCategory', 
							'Media' => array(
								'elements' => array(
									'type' => false
								), 
							), 
						), 
						'conditions'	=> array('DocumentMedia.document_type' => 'product'), 
					), 
				//	kalo pake format unit pake init
				//	'ProductMedia' => array(
				//		'uses'			=> 'DocumentMedia', 
				//		'contain'		=> array('Media'), 
				//		'conditions'	=> array('DocumentMedia.document_type' => 'product'), 
				//	), 
				), 
			));

			$records = $this->Project->getMergeList($records, array(
				'contain' => array(
					'ProjectMedia' => array(
						'uses'			=> 'DocumentMedia', 
						'contain'		=> array('Media'), 
						'conditions'	=> array('DocumentMedia.document_type' => 'project_logo'), 
					),
				), 
			));

		//	merge product unit media
			foreach($records as &$record){
				$productUnits = Common::hashEmptyField($record, 'ProductUnitRelation', array());
				if($productUnits){
					foreach($productUnits as $key => $productUnit){
						$productID		= Common::hashEmptyField($productUnit, 'ProductUnitRelation.product_id');
						$productUnitID	= Common::hashEmptyField($productUnit, 'ProductUnitRelation.product_unit_id');

						if(empty($productUnitID)){
							unset($productUnits[$key]);
						}
						else{
							$productUnits[$key] = $this->ProductUnitRelation->ProductUnit->getMergeList($productUnit, array(
								'contain' => array(
									'ProductUnitSpecification' => array(
										'UnitMaterial', 
									), 
									'DocumentMedia' => array(
										'contain'		=> array('Media'), 
										'conditions'	=> array('DocumentMedia.document_type' => 'product_unit'), 
									),
								), 
							));

						//	append stock info
							$blocks = $this->ProductUnit->ProductUnitStock->getData('list', array(
								'fields'		=> array(
									'ProductUnitStock.id', 
									'ProductUnitStock.blok', 
								), 
								'conditions'	=> array(
									'ProductUnitStock.product_id'		=> $productID, 
									'ProductUnitStock.product_unit_id'	=> $productUnitID, 
								), 
							));

							$stockCounts		= $this->ProductUnitStock->getInformationData($productID, $blocks);
							$productUnits[$key]	=  array_merge($productUnits[$key], array(
								'ProductUnitStockInfo' => $stockCounts, 
							));
						}
					}
				}

				$record = Hash::insert($record, 'ProductUnit', $productUnits);
				$record = Hash::remove($record, 'ProductUnitRelation');
			}

		//	debug($records);exit;
		//	get contentheader image
		//	$records = $this->Project->getMergeList($records, array(
		//		'contain' => array(
		//			'ProjectMedia' => array(
		//				'uses'		=> 'DocumentMedia', 
		//				'contain'	=> array('Media'), 
		//			), 
		//		), 
		//	));

		//	debug($records);exit;
		}

		return $records;
	}

	function _callProduct ( $data, $id, $elements = array( 'status' => 'available' ) ) {
		if( !empty($id) ) {
			$value = $this->getData('first', array(
				'conditions' => array(
					'Product.id' => $id,
				),
			), $elements);

			if( !empty($value) ) {
				$value = $this->getMergeList($value, array(
					'contain' => array(
						'Region',
						'City',
						'Subarea',
						'ProductStatusBuild',
						'ProductStatusSell',
					),
				));

				$status_selling = Configure::read('__Site.Global.Variable.Product.status_selling');
				$status_building = Configure::read('__Site.Global.Variable.Product.status_building');

 				$product_id = Common::hashEmptyField($value, 'Product.id');
 				$label = Common::hashEmptyField($value, 'Product.label');
 				$address = Common::_callLocation($value, 'Product', 'full');

 				$data = Hash::insert($data, 'Product.id', $product_id);
 				$data = Hash::insert($data, 'Product.status_selling', Common::hashEmptyField($value, 'ProductStatusSell.name'));
 				$data = Hash::insert($data, 'Product.status_building', Common::hashEmptyField($value, 'ProductStatusBuild.name'));
 				$data = Hash::insert($data, 'Product.address', $address);
 				$data = Hash::insert($data, 'Crm.product_name', $label);
			}
		}

		return $data;
	}

	function doSoldToggle( $id, $active ) {	
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'Product.id' => $id,
			),
		);

		$value = $this->getData('all', $options, array(
			'status' => 'all',
			'active' => 'all'
		));

		if ( !empty($value) ) {
			if( !empty($active) ) {
				$default_msg = __('mengaktifkan status terjual produk');
				$rule = array(
					'Product.sold' => 1
				);
			} else {
				$default_msg = __('nonaktifkan status terjual produk');
				$rule = array(
					'Product.sold' => 0
				);
			}

			$flag = $this->updateAll($rule, array(
				'Product.id' => $id
			));

            if( $flag ) {
            	if(isset($rule['Product.sold']) && $rule['Product.sold'] == 1){
            		$this->id = $id;

            		$this->saveField('product_status_sell_id', 3);
            	}

            	$this->validateSold($id);

				$msg = __('Berhasil %s', $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $id,
					),
				);
            } else {
				$result = array(
					'msg' => __('Gagal %s', $default_msg),
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal %s. Data tidak ditemukan', $default_msg),
				'status' => 'error',
			);
		}

		return $result;
	}

	function validateSold($product_id){
		$data = $this->findById($product_id);

		if(!empty($data)){
			$project_id = Common::hashEmptyField($data, 'Product.project_id');

			$list_product_id = $this->getData('count', array(
				'conditions' => array(
					'Product.project_id' => $project_id,
					'Product.sold' => 0,
				),
			));

			if(empty($list_product_id)){
				$this->Project->doSoldToggle($project_id, 1);
			}
		}
	}

	function repairProduct($id, $data){
		if(is_array($id)){
			$temp_id = array();
			foreach ($id as $key_id => $value) {
				if(!empty($data) && !empty($id) && !array_key_exists($key_id, $data)){
					$temp_id[] = $key_id;
				}
			}

			$data = $this->mergingRepairData($temp_id, $data);
		}else{
			if(!empty($data) && !empty($id) && !array_key_exists($id, $data)){			
				$data = $this->mergingRepairData($id, $data);
			}
		}

		return $data;
	}

	function mergingRepairData($id, $data){
		$data_search = $this->getData('list', array(
			'conditions' => array(
				'Product.id' => $id
			),
			'fields' => array(
				'Product.id', 'Product.name'
			)
		));

		if(!empty($data_search)){
			$data = $data_search+$data;
		}

		return $data;
	}

	function listProduct($conditions = array()){
		$project_id = (int) Configure::read('Global.Data.Project.id');

		$text_condition = '';

		if(!empty($conditions)){
			if(is_array($conditions)){
				$text_condition = implode('.', $conditions);
			}else{
				$text_condition = $conditions;
			}

			$text_condition = '.'.$text_condition;
		}

		$cacheConfig	= 'daily';
		$cacheName		= sprintf('Product.produk.%s%s', $project_id, $text_condition);
		$cacheData		= Cache::read($cacheName, $cacheConfig);

		if(!empty($cacheData) && empty($conditions)){
			$result = $cacheData;
		}else{
			$result = $this->getData('list', array(
				'group' => array(
					'Product.name'
				),
				'fields' => array(
					'Product.id', 'Product.name'	
				),
				'order' => array(
					'Product.name' => 'ASC'
				)
			));

			Cache::write($cacheName, $result, $cacheConfig);
		}

		return $result;
	}

	public function afterSave($created, $options = array()){
		$project_id = (int) Configure::read('Global.Data.Project.id');
		
		Cache::delete(__('Product.produk.%s', $project_id), 'daily');
	}
}
