<?php
class VoucherCodeUsages extends AppModel {
	public $validate = array(
		'payment_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukan Invoice yang menggunakan voucher'
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon masukan Invoice yang menggunakan voucher'
			),
		),
		'voucher_code_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukan voucher yang digunakan'
			),
		),
	);

	public $belongsTo = array(
		'VoucherCode' => array(
			'foreignKey' => 'voucher_code_id',
		)
	);
}
?>