<?php
class Voucher extends AppModel {
	public $hasMany = array(
		'VoucherDetail'	=> array(
			'foreignKey'	=> 'voucher_id',
			'dependent'		=> TRUE,
		),
		'VoucherCode'	=> array(
			'foreignKey'	=> 'voucher_id',
			'dependent'		=> TRUE
		),
		'VoucherCodeUsage' => array(
			'foreignKey'	=> 'voucher_code_id',
			'dependent'		=> TRUE
		)
	);

	public $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id',
		),
	);

	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukan nama voucher'
			),
		),
		'discount_value' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Potongan harus berupa angka'
			),
			'checkValue' => array(
				'rule' => array('checkValue'),
				'message' => 'Potongan harus berupa angka dan lebih besar dari 0 (Untuk Potongan dengan persentase tidak boleh lebih dari 100%)'
			)
		),
		'length' => array(
			'notempty' => array(
				'rule' => array('notempty', 4),
				'message' => 'Panjang Kode Voucher harap diisi dan harus lebih besar atau sama dengan %s'
			),
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Panjang Kode Voucher harus berupa angka'
			), 
			'minVal' => array(
				'rule' => array('minVal', 4),
				'message' => 'Panjang Kode Voucher harus lebih besar atau sama dengan %s'
			),
		),
		'period_date' => array(
			'checkPeriodDate' => array(
				'rule' => array('checkPeriodDate'),
				'message' => 'Tanggal Berlaku tidak valid',
			),
		), 
		'date_from' => array(
			'checkPeriodDate' => array(
				'rule' => array('checkPeriodDate'),
				'message' => 'Tanggal Berlaku tidak valid',
			),
		), 
		'date_to' => array(
			'checkPeriodDate' => array(
				'rule' => array('checkPeriodDate'),
				'message' => 'Tanggal Berlaku tidak valid',
			),
		), 
	);

	public function minVal($value, $minimum = 0){
		$value = array_shift($value);
		return ($value && $value >= $minimum);
	}

	public function checkPeriodDate($value){
		$value		= array_shift($value);
		$periodType	= $this->filterEmptyField($this->data, 'Voucher', 'period_type');

		if($periodType == 'periodic'){
			$periodDate = $this->filterEmptyField($this->data, 'Voucher', 'period_date');
			$dateFrom	= $this->filterEmptyField(($periodDate ? $periodDate : $this->data), 'date_from');
			$dateTo		= $this->filterEmptyField(($periodDate ? $periodDate : $this->data), 'date_to');

			$this->data['date_from']	= $dateFrom;
			$this->data['date_to']		= $dateTo;

			if($periodDate){
				unset($this->data['Voucher']['period_date']);
			}

			return (!empty($dateFrom) && !empty($dateTo));
		}
		else{
			return true;
		}
	}

	public function _callRefineParams($data = '', $defaultOptions = NULL){
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
    	$status = $this->filterEmptyField($data, 'named', 'status', false, array(
			'addslashes' => true,
		));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));
		$name = $this->filterEmptyField($data, 'named', 'name', false, array(
        	'addslashes' => true,
    	));
		$code_mechanism = $this->filterEmptyField($data, 'named', 'code_mechanism', false, array(
        	'addslashes' => true,
    	));
		$period_type = $this->filterEmptyField($data, 'named', 'period_type', false, array(
        	'addslashes' => true,
    	));
		$apply_to = $this->filterEmptyField($data, 'named', 'apply_to', false, array(
        	'addslashes' => true,
    	));
    	$date_from = $this->filterEmptyField($data, 'named', 'date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = $this->filterEmptyField($data, 'named', 'date_to', false, array(
            'addslashes' => true,
        ));
    	$modified_from = $this->filterEmptyField($data, 'named', 'modified_from', false, array(
            'addslashes' => true,
        ));
        $modified_to = $this->filterEmptyField($data, 'named', 'modified_to', false, array(
            'addslashes' => true,
        ));

		if($status && in_array($status, array('active', 'disabled'))){
			if($status == 'disabled'){
				$defaultOptions = array_merge_recursive($defaultOptions, array(
					'conditions' => array(
						'Voucher.period_type'	=> 'periodic', 
						'Voucher.date_to <'		=> date('Y-m-d'), 
					), 
				));
			}
			else{
				$defaultOptions = array_merge_recursive($defaultOptions, array(
					'conditions' => array(
						'OR' => array(
							'Voucher.period_type' => 'unlimited', 
							array(
								'Voucher.period_type'	=> 'periodic', 
								'Voucher.date_to >='	=> date('Y-m-d'), 
							), 
						), 
					), 
				));
			}
		}

		if($keyword){
			$orConditions = array(
				'MATCH(Voucher.code) AGAINST(? IN BOOLEAN MODE)' => $keyword,
				'MATCH(Voucher.name) AGAINST(? IN BOOLEAN MODE)' => $keyword,
				'Voucher.code LIKE' => '%'.$keyword.'%',
				'Voucher.name LIKE' => '%'.$keyword.'%',
			);

			if($status == 'active'){
				$orConditions = array($orConditions);
			}

			$defaultOptions = array_merge_recursive($defaultOptions, array(
				'conditions' => array(
					'OR' => $orConditions, 
				), 
			));
		}

		if( !empty($name) ) {
			$defaultOptions['conditions']['Voucher.name LIKE'] = '%'.$name.'%';
		}
		if( !empty($period_type) ) {
			$defaultOptions['conditions']['Voucher.period_type'] = $period_type;
		}
		if( !empty($apply_to) ) {
			$defaultOptions['conditions']['Voucher.apply_to'] = $apply_to;
		}
		if( !empty($code_mechanism) ) {
			$defaultOptions['conditions']['Voucher.code_mechanism'] = $code_mechanism;
		}
		if( !empty($date_from) ) {
			$defaultOptions['conditions']['DATE_FORMAT(Voucher.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$defaultOptions['conditions']['DATE_FORMAT(Voucher.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$defaultOptions['conditions']['DATE_FORMAT(Voucher.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$defaultOptions['conditions']['DATE_FORMAT(Voucher.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}

		$defaultOptions['order'][$sort] = $direction;
		return $defaultOptions;
	}

	public function getData($findType = 'all', $options = array()){
		$defaultOptions = array(
			'recursive'	=> 1, 
			'contain'	=> array(
				'VoucherDetail' => array(
					'order' => array(
						'VoucherDetail.created' => 'DESC'
					), 
					'MembershipPackage' => array(
						'order' => array(
							'MembershipPackage.created' => 'DESC'
						), 
					)
				),
				'VoucherCode' => array(
					'order' => array(
						'VoucherCode.created' => 'DESC'
					), 
					'VoucherCodeUsages' => array(
						'order' => array(
							'VoucherCodeUsages.created' => 'DESC'
						), 
					)
				)
			)
		);

		if(!empty($options)){
			$defaultOptions = array_replace_recursive($defaultOptions, $options);
		}

		if($findType == 'paginate'){
			$result = $defaultOptions;
		}
		else{
			$result = $this->find($findType, $defaultOptions);
		}

		return $result;
	}

	function getMerge($data, $recordID){
		if( $data && empty($data['Voucher']) && !empty($recordID) ){
			$record = $this->getData('first', array(
				'contain'		=> FALSE, 
				'conditions'	=> array(
					'Voucher.id'			=> $recordID,
					'Voucher.status'		=> 1,
					'Voucher.is_deleted'	=> 0,
				),
			));

			if(empty($record) === FALSE){
				$data = array_merge($data, $record);
			}
		}

		return $data;
	}

	function getVoucher($voucherCode, $invoiceNumber){
		$result = array('status' => 'error', 'msg' => __('Voucher tidak ditemukan atau sudah tidak berlaku.'));

		$this->Payment = ClassRegistry::init('Payment');
		$this->Payment->unbindModel(array(
			'belongsTo' => array('User', 'MembershipPackage', 'MembershipOrder')
		));

		$payment = $this->Payment->getData('first', array(
			'conditions' => array(
				'Payment.invoice_number'	=> $invoiceNumber, 
				'Payment.payment_status'	=> array('new', 'process', 'failed'), 
			)
		));

		$membershipPackageID	= empty($payment['Payment']['membership_package_id']) ? NULL : $payment['Payment']['membership_package_id'];
		$currentVoucherCodeID	= empty($payment['VoucherCode']['id']) ? NULL : $payment['VoucherCode']['id'];
		$currentVoucherCode		= empty($payment['VoucherCode']['code']) ? NULL : $payment['VoucherCode']['code'];
		$continue				= FALSE;

		if($payment && empty($currentVoucherCode)){
			$isUsed		= FALSE;
			$continue	= TRUE;
			$conditions = array(
				'VoucherCode.code'			=> $voucherCode, 
				'VoucherCode.status'		=> 1, 
				'VoucherCode.is_deleted'	=> 0,  
				'VoucherCode.usage_count < VoucherCode.usage_limit'
			);
		}
		else if($payment && $voucherCode == $currentVoucherCode){
			$isUsed		= TRUE;
			$continue	= TRUE;
			$conditions = array(
				'VoucherCode.code' => $voucherCode, 
			);
		}

		if($continue){
			$voucher = $this->VoucherCode->getData('first', array('conditions' => $conditions));

			if($voucher){
				$voucherID	= empty($voucher['VoucherCode']['voucher_id']) ? NULL : $voucher['VoucherCode']['voucher_id'];
				$voucher	= $this->getMerge($voucher, $voucherID);
				$voucher	= $this->VoucherDetail->getMerge($voucher, $voucherID, $membershipPackageID);						
				$periodType	= empty($voucher['Voucher']['period_type']) ? 'unlimited' : $voucher['Voucher']['period_type'];
				$applyTo	= empty($voucher['Voucher']['apply_to']) ? 'all' : $voucher['Voucher']['apply_to'];

				if($periodType == 'periodic'){
					$currentDate	= date('Y-m-d');
					$dateFrom		= empty($voucher['Voucher']['date_from']) ? NULL : $voucher['Voucher']['date_from'];
					$dateTo			= empty($voucher['Voucher']['date_to']) ? NULL : $voucher['Voucher']['date_to'];

					if(strtotime($dateFrom) > strtotime($currentDate) || strtotime($currentDate) > strtotime($dateTo)){
						$continue = FALSE;
					}
				}

				if($continue){
					if($applyTo == 'manual'){
						$voucherDetail		= empty($voucher['VoucherDetail']) ? NULL : $voucher['VoucherDetail'];
						$allowedPackageID	= Set::extract('/membership_package_id', $voucherDetail);

						if(in_array($membershipPackageID, $allowedPackageID) === FALSE){
							$continue = FALSE;
						}
					}

					if($continue){
						$voucherCodeID	= empty($voucher['VoucherCode']['id']) ? NULL : $voucher['VoucherCode']['id'];
						$voucherCode	= empty($voucher['VoucherCode']['code']) ? NULL : $voucher['VoucherCode']['code'];

						if($applyTo == 'manual'){
							$voucherDetail	= empty($voucher['VoucherDetail'][0]) ? NULL : $voucher['VoucherDetail'][0];
							$discountType	= empty($voucherDetail['discount_type']) ? 'nominal' : $voucherDetail['discount_type'];
							$discountValue	= empty($voucherDetail['discount_value']) ? 0 : $voucherDetail['discount_value'];
						}
						else{
							$discountType	= empty($voucher['Voucher']['discount_type']) ? 'nominal' : $voucher['Voucher']['discount_type'];
							$discountValue	= empty($voucher['Voucher']['discount_value']) ? 0 : $voucher['Voucher']['discount_value'];
						}

						$data = array(
							'voucher_code_id'	=> $voucherCodeID, 
							'voucher_code'		=> $voucherCode, 
							'discount_type'		=> $discountType, 
							'discount_value'	=> $discountValue, 
							'is_used'			=> $isUsed	// buat checkout, kalo TRUE berarti ga usah di log usage nya
						);

						$result = array(
							'status'	=> 'success', 
							'msg'		=> __('Voucher Valid'), 
							'data'		=> $data
						);
					}
				}
			}
		}

		return $result;
	}
	
	public function logUsage($voucherCodeID, $paymentID){
		$voucherCodeUpdate = $this->VoucherCode->updateAll(array('VoucherCode.usage_count' => 'VoucherCode.usage_count + 1'), array('VoucherCode.id' => $voucherCodeID));

		$this->VoucherCodeUsage->create();
		$voucherUsageSave = $this->VoucherCodeUsage->save(array('voucher_code_id' => $voucherCodeID, 'payment_id' => $paymentID));

		return $voucherCodeUpdate && $voucherUsageSave;
	}

	public function doToggle($id = NULL){
		if(is_array($id)){
			$id = array_filter($id);
		}

		$options = array(
			'conditions' => array(
				'Voucher.id'			=> $id,
				'Voucher.is_deleted'	=> 0,
			),
		);

		$records = $this->getData('all', $options);
		if($records){
			$flag = $this->updateAll(array('Voucher.is_deleted' => 1), array('Voucher.id' => $id));

			if($flag){
				$result = array('status' => 'success', 'msg' => __('Berhasil menghapus data Voucher'));
			}
			else{
				$result = array('status' => 'error', 'msg' => __('Gagal menghapus data Voucher'));
			}
		}
		else{
			$result = array('status' => 'error', 'msg' => __('Gagal menghapus data Voucher. Data tidak ditemukan'));
		}

		return $result;
	}

	public function checkValue(){
		$applyTo		= empty($this->data['Voucher']['apply_to']) ? 'all' : $this->data['Voucher']['apply_to'];
		$discountType	= empty($this->data['Voucher']['discount_type']) ? 'nominal' : $this->data['Voucher']['discount_type'];
		$discountValue	= empty($this->data['Voucher']['discount_value']) ? 0 : $this->data['Voucher']['discount_value'];

		if($discountValue <= 0){
			return FALSE;
		}
		else{
			if($discountType == 'percentage' && $discountValue > 100){
				return FALSE;
			}
		}

		return TRUE;
	}
}
?>