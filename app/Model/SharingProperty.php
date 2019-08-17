<?php
class SharingProperty extends AppModel {
	
	public $useTable = false;
	var $name = 'SharingProperty';
	var $validate = array(
		'sender_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama pengirim harap diisi',
			),
			// 'minLength' => array(
			// 	'rule' => array('minLength', 3),
			// 	'message' => 'Panjang nama minimal 3 karakter',
			// ),
			// 'maxLength' => array(
			// 	'rule' => array('maxLength', 64),
			// 	'message' => 'Panjang nama maksimal 64 karakter',
			// ),
		),
		'receiver_name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama penerima harap diisi',
			),
			// 'minLength' => array(
			// 	'rule' => array('minLength', 3),
			// 	'message' => 'Panjang nama minimal 3 karakter',
			// ),
			// 'maxLength' => array(
			// 	'rule' => array('maxLength', 64),
			// 	'message' => 'Panjang nama maksimal 64 karakter',
			// ),
		),
		'receiver_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email harap diisi',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
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
		'receiver_phone' => array(
			'notMatch' => array(
				'rule' => array('validatePhoneNumber'),
				'message' => 'Format No. Telepon e.g. +6281234567 or 0812345678',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'Minimal 6 karakter',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 20),
				'message' => 'Maksimal 20 karakter',
			),
		),
	);

	function validatePhoneNumber( $data ) {
		if( isset( $data['receiver_phone'] ) && !empty($data['receiver_phone']) ) {
			if (preg_match('/^[0-9]{1,}$/', $data['receiver_phone'])==1 || ( substr($data['receiver_phone'], 0,1)=="+" && preg_match('/^[0-9]{1,}$/', substr($data['receiver_phone'], 1,strlen($data['receiver_phone'])))==1 )){
				return true; 
			}else{
				return false; 
			}
		}else{
			return true;
		}
    }

	function doSave( $data, $property, $values = array(), $id, $template = false ) {
		$result = array(
			'msg' => '',
			'status' => 'error'
		);

		$securityCode = !empty($data['SharingProperty']['security_code'])?$data['SharingProperty']['security_code']:false;
		$this->set($data);

		$fieldList = array(
			'fieldList' => array(
				'sender_name',
				'receiver_name',
				'receiver_email',
				'receiver_phone'
			)
		);
		$validateData = $this->validates($fieldList);

		if( $validateData && !empty($securityCode) ){
			$msg_text = sprintf(__('Laporan properti berhasil dibagikan ke %s dengan email %s', $data['SharingProperty']['receiver_name'], $data['SharingProperty']['receiver_email']));
			$data['propertyData'] = $property;
			$data['values'] = $values;
	
			$result = array(
				'msg' => $msg_text,
				'status' => 'success',
				'SendEmail' => array(
                	'to_name' => $data['SharingProperty']['receiver_name'],
                	'to_email' => $data['SharingProperty']['receiver_email'],
                	'subject' => __('Share Data Laporan Properti'),
                	'template' => $template,
                    'data' => $data,
            	),
            	'Log' => array(
                    'activity' => $msg_text,
                    'document_id' => $id,
                ),
			);
		} else {
			if( empty($validateData) ) {
				$result['msg'] = __('Gagal bagikan laporan properti');
			} else {
				$result['msg'] = __('Mohon centang untuk menandakan Anda bukan robot');
			}
		}

		return $result;
	}
}
?>