<?php
class SharingKpr extends AppModel {
	var $name = 'SharingKpr';

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

	var $belongsTo = array(
		'LogKpr' => array(
			'className' => 'LogKpr',
			'foreignKey' => 'log_kpr_id',
		),
		'KprBank' => array(
			'className' => 'KprBank',
			'foreignKey' => 'kpr_bank_id',
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

	function doSave( $data = false, $dataLogkpr = false ) {
		$msg = array(
			'msg' => '',
			'status' => 'error'
		);
		$securityCode = $this->filterEmptyField($data, 'SharingKpr', 'security_code');
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
			$this->create();
			if( $this->save($data) ) {
				$id = $this->id;
				$receiver_name = $this->filterEmptyField($data, 'SharingKpr', 'receiver_name');
				$receiver_email = $this->filterEmptyField($data, 'SharingKpr', 'receiver_email');
				$log_kpr_id = !empty($data['SharingKpr']['log_kpr_id'])?$data['SharingKpr']['log_kpr_id']:false;

				$mail_params = array(
					'params_sharing_kpr' => $data,
					'params_log_kpr' => $dataLogkpr,
				);

				$msg_text = sprintf(__('Perhitungan KPR berhasil dibagikan ke %s dengan email %s', $receiver_name, $receiver_email));
				$msg = array(
					'msg' => $msg_text,
					'status' => 'success',
					'SendEmail' => array(
                    	'to_name' => $receiver_name,
                    	'to_email' => $receiver_email,
                    	'subject' => __('Share Data KPR'),
                    	'template' => 'kpr_share',
                        'data' => $mail_params, 
                	),
                	'Log' => array(
                        'activity' => $msg_text,
                        'document_id' => $id,
                    ),
				);
			} else {
				$msg['msg'] = __('Gagal bagikan data KPR');
			}
		}else{
			if( empty($validateData) ) {
				$msg['msg'] = __('Gagal bagikan data KPR');
			} else {
				$msg['msg'] = __('Mohon centang untuk menandakan Anda bukan robot');
			}
		}

		return $msg;
	}

	function data_sync(){
    	$result = $this->find('list', array(
   			'conditions' => array(
   				'SharingKpr.sync' => 0
   			),
   			'fields' => array(
   				'SharingKpr.id'
   			)
   		));

   		if(!empty($result)){
   			$this->updateAll(
			    array('SharingKpr.sync' => 1),
			    array(
			    	'SharingKpr.sync' => 0,
			    	'SharingKpr.id' => $result
			    )
			);

			$result = $this->find('all', array(
	   			'conditions' => array(
	   				'SharingKpr.sync' => 1,
	   				'SharingKpr.id' => $result
	   			)
	   		));

	   		if(!empty($result)){
	   			$this->LogKpr = ClassRegistry::init('LogKpr');

	   			foreach ($result as $key => $value) {
	   				$log_kpr = $this->LogKpr->getFirstData($value['SharingKpr']['log_kpr_id']);
	   				$result[$key] = array_merge($result[$key], $log_kpr);
	   			}
	   		}
   		}

   		return $result;
    }
}
?>