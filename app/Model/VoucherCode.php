<?php
class VoucherCode extends AppModel {
	public $validate = array(
		'code' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukan Kode Voucher'
			),
			'isUnique' => array(
				'rule' => array('isUnique'),
				'message' => 'Kode voucher telah terdaftar, mohon masukan Kode Voucher lain'
			),
			'minLength' => array(
				'rule' => array('minLength', 4),
				'message' => 'Panjang Kode Voucher harus lebih besar atau sama dengan %s'
			),
		),
		'usage_limit' => array(
			'greaterThanZero' => array(
				'rule' => array('greaterThanZero'),
				'message' => 'Maksimum Jumlah Pemakaian harus diisi dan lebih besar dari 0'
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Maksimum Jumlah Pemakaian harus berupa angka'
			),
		),
	);

	public function greaterThanZero($data){
		$value = array_shift($data);

		if(is_numeric($value) === FALSE || $value <= 0){
			return FALSE;
		}

		return TRUE;
	}

	public $belongsTo = array(
		'Voucher' => array(
			'foreignKey' => 'voucher_id',
		),
	);

	public $hasMany = array(
		'VoucherCodeUsages' => array(
			'foreignKey'	=> 'voucher_code_id',
			'dependent'		=> TRUE,
		),
	);

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
		$default_options = array(
			'condition' => array(
				'VoucherCode.status'		=> 1,
				'VoucherCode.is_deleted'	=> 0,
			),
		);

		if(!empty($options)){
			$default_options = array_merge($default_options, $options);
		}

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getMerge($data, $recordID){
		if( $data && empty($data['Voucher']) && !empty($recordID) ){
			$record = $this->getData('first', array(
				'contain'		=> array(
					'Voucher'
				), 
				'conditions'	=> array(
					'VoucherCode.id'			=> $recordID,
					'VoucherCode.status'		=> 1,
					'VoucherCode.is_deleted'	=> 0,
				),
			));

			if(empty($record) === FALSE){
				$data = array_merge($data, $record);
			}
		}

		return $data;
	}
}
?>