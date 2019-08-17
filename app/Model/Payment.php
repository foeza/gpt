<?php
class Payment extends AppModel {
	public $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id'
		),
		'Principle'	=> array(
			'className'		=> 'User', 
			'foreignKey'	=> 'principle_id', 
		), 
		'MembershipPackage' => array(
			'foreignKey' => 'membership_package_id'
		),
		'MembershipOrder' => array(
			'foreignKey' => 'membership_order_id'
		),

	);

	public $hasOne = array(
		'VoucherCode' => array(
			'foreignKey' => FALSE,
			'conditions' => array(
				'VoucherCode.id = Payment.voucher_code_id'
			)
		)
	);

	public $validate = array(
		'payment_channel'	=> array(
			'notempty' => array(
				'rule'		=> array('notempty'),
				'message'	=> 'Mohon pilih Metode Pembayaran',
			),
		),
	);

	// function isNumber($data) {
 //        foreach ($data as $key => $value) {
 //            if( !is_numeric($value) ) {
 //                return false; 
 //            } else if( $value <= 0 ) {
 //                return false;
 //            } else {
 //                return true;
 //            }
 //        }
 //    }

	public function beforeFind($query){
		parent::beforeFind($query);

		$statuses		= '"new", "pending", "process", "waiting", "failed", "renewal"';
		$currentDate	= date('Y-m-d');
		$paymentStatus	= '
			case when '.$this->alias.'.payment_status in('.$statuses.') and '.$this->alias.'.expired_date <= now() then
				"expired"
			else
				'.$this->alias.'.payment_status
			end
		';

		$this->virtualFields = array_merge($this->virtualFields, array( 
			'payment_status' => $paymentStatus, 
		));

		return $query;
	}

	public function _callRefineParams($data = '', $defaultOptions = NULL){
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
    	$invoice_number = $this->filterEmptyField($data, 'named', 'invoice_number', false, array(
        	'addslashes' => true,
    	));
    	$name = $this->filterEmptyField($data, 'named', 'name', false, array(
        	'addslashes' => true,
    	));
    	$company_name = $this->filterEmptyField($data, 'named', 'company_name', false, array(
        	'addslashes' => true,
    	));
    	$package_name = $this->filterEmptyField($data, 'named', 'package_name', false, array(
        	'addslashes' => true,
    	));
    	$phone = $this->filterEmptyField($data, 'named', 'phone', false, array(
        	'addslashes' => true,
    	));
    	$email = $this->filterEmptyField($data, 'named', 'email', false, array(
        	'addslashes' => true,
    	));
    	$voucher_code = $this->filterEmptyField($data, 'named', 'voucher_code', false, array(
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

    	$status = $this->filterEmptyField($data, 'named', 'status', false, array(
			'addslashes' => true,
		));

        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));

        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));

        if($status){
        	$defaultOptions['conditions'][$this->alias.'.payment_status'] = $status;
        }

		if($keyword){
			$defaultOptions['conditions']['OR'] = array(
				'MATCH(Payment.invoice_number) AGAINST(? IN BOOLEAN MODE)' => $keyword,
				'Payment.invoice_number LIKE' => '%'.$keyword.'%',
				'VoucherCode.code LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($invoice_number) ) {
			$defaultOptions['conditions']['Payment.invoice_number LIKE'] = '%'.$invoice_number.'%';
		}
		if( !empty($name) ) {
			$defaultOptions['conditions']['MembershipOrder.name LIKE'] = '%'.$name.'%';
			$defaultOptions['contain'][] = 'MembershipOrder';
		}
		if( !empty($company_name) ) {
			$defaultOptions['conditions']['MembershipOrder.company_name LIKE'] = '%'.$company_name.'%';
			$defaultOptions['contain'][] = 'MembershipOrder';
		}
		if( !empty($package_name) ) {
			$defaultOptions['conditions']['MembershipOrder.membership_package_id'] = $package_name;
			$defaultOptions['contain'][] = 'MembershipOrder';
		}
		if( !empty($email) ) {
			$defaultOptions['conditions']['MembershipOrder.email LIKE'] = '%'.$email.'%';
			$defaultOptions['contain'][] = 'MembershipOrder';
		}
		if( !empty($phone) ) {
			$defaultOptions['conditions']['MembershipOrder.phone LIKE'] = '%'.$phone.'%';
			$defaultOptions['contain'][] = 'MembershipOrder';
		}
		if( !empty($date_from) ) {
			$defaultOptions['conditions']['DATE_FORMAT(Payment.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$defaultOptions['conditions']['DATE_FORMAT(Payment.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$defaultOptions['conditions']['DATE_FORMAT(Payment.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$defaultOptions['conditions']['DATE_FORMAT(Payment.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}

		$defaultOptions['order'][$sort] = $direction;

		return $defaultOptions;
	}

	public function getData($findType = 'all', $options = array()){
		$defaultOptions = array(
			'recursive' => 1
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

	public function createInvoice($data = NULL){
	//	1 order 1 invoice, jika ingin buat invoice baru, cancel invoice sebelumnya
		$result		= array();
		$orderID	= $this->filterEmptyField($data, 'MembershipOrder', 'id');

		if($orderID){
			$order = $this->MembershipOrder->getData('first', array(
				'recursive'		=> 1,
				'conditions'	=> array(
					'MembershipOrder.id'		=> $orderID,
					'MembershipOrder.status'	=> array('approved', 'renewal'), 
				)
			));

			if($order){
				$data = array_replace_recursive($data, $order);

				$autoCreate		= TRUE;
				$userCheck		= $this->checkAccount($data, $autoCreate);
				$userStatus		= $this->filterEmptyField($userCheck, 'status', null, 'error');
				$userMessage	= $this->filterEmptyField($userCheck, 'msg');

				if($userStatus == 'error'){
					$result = array(
						'status'	=> $userStatus, 
						'msg'		=> $userMessage, 
					);

					return $result;
				}
				else{
					$continue			= TRUE;
					$email				= $this->filterEmptyField($data, 'MembershipOrder', 'email');
					$principleEmail		= $this->filterEmptyField($data, 'MembershipOrder', 'principle_email');
					$isPrinciple		= $this->filterEmptyField($data, 'MembershipOrder', 'is_principle');

					$userID = $this->User->field('User.id', array(
						'User.email' => $email, 
					));

					if($isPrinciple){
						$principleID = $userID;
					}
					else{
						$principleID = $this->User->field('User.id', array(
							'User.email' => $principleEmail, 
						));
					}

					$invoices = $this->getData('all', array(
						'recursive'		=> -1,
						'conditions'	=> array(
							'Payment.user_id'				=> $userID,
							'Payment.membership_order_id'	=> $orderID,
							'Payment.payment_status'		=> array('pending', 'process')
						)
					));

					if($invoices){
					//	cancel invoice lama yang masih aktif untuk order terpilih
						$continue = $this->updateAll(array(
							'Payment.payment_status' => "'cancelled'", 
						), array(
							'Payment.membership_order_id' => $orderID, 
						));
					}

					if($continue === FALSE){
						$result = array('status' => 'error', 'msg' => __('Gagal mengubah data Invoice.'));
					}
					else{
						$invoiceNumber	= $this->generateInvoiceNumber();
						$refererURL		= $this->filterEmptyField($data, 'Payment', 'referer_url', FULL_BASE_URL);
						$packageID		= $this->filterEmptyField($data, 'MembershipPackage', 'id');
						$baseAmount		= $this->filterEmptyField($data, 'MembershipPackage', 'price', 0);
						$totalAmount	= $baseAmount;
						$currencyCode	= 360;
						$paymentStatus	= 'pending';
						$currentDate	= date('YmdHis');
						$sessionID		= md5($userID.$currentDate);
						$expiredDate	= date('Y-m-d H:i:s', strtotime('+1 day'));
						$invoiceData	= array(
							'user_id'				=> $userID,
							'principle_id'			=> $principleID,
							'membership_order_id'	=> $orderID,
							'membership_package_id'	=> $packageID,
							'currency_code'			=> $currencyCode,
							'invoice_number'		=> $invoiceNumber,
							'base_amount'			=> $baseAmount,
							'total_amount'			=> $totalAmount,
							'payment_status'		=> $paymentStatus,
							'expired_date'			=> $expiredDate, 
							'session_id'			=> $sessionID, 
							'referer_url'			=> $refererURL, 
						);

						$invoice = $this->save($invoiceData);
						if($invoice){
						//	pemesan
							$user = $this->User->read(null, $userID);

						//	principle
							$principle = $this->User->read(null, $principleID);
							$principle = $this->filterEmptyField($principle, 'User');
							$principle = array(
								'Principle' => $principle, 
							);

							$principle = $this->User->UserCompany->getMerge($principle, $principleID);
						//	$principle = $this->User->UserCompanyConfig->getMerge($principle, $principleID);

						//	merge all
							$invoice = array_replace_recursive($invoice, $user, $principle);

						//	update order
							$updateOrder = $this->MembershipOrder->updateAll(array(
								'MembershipOrder.user_id'		=> sprintf("'%s'", $userID), 
								'MembershipOrder.principle_id'	=> sprintf("'%s'", $principleID), 
							), array(
								'MembershipOrder.id' => $orderID, 
							));

							if($updateOrder){
								$result = array('status' => 'success', 'msg' => __('Berhasil membuat Invoice.'), 'data' => $invoice);
							}
							else{
								$result = array('status' => 'error', 'msg' => __('Gagal mengubah data Request.'));
							}
						}
						else{
							$result = array('status' => 'error', 'msg' => __('Gagal membuat Invoice.'));
						}
					}
				}
			}
			else{
				$result = array('status' => 'error', 'msg' => __('Gagal membuat Invoice, Order tidak valid.'));
			}
		}
		else{
			$result = array('status' => 'error', 'msg' => __('Gagal membuat Invoice, data tidak valid.'));
		}

		return $result;
	}

	public function checkAccount($data = NULL, $autoCreate = TRUE){
		if($data){
			$email			= $this->filterEmptyField($data, 'MembershipOrder', 'email');
			$email			= strtolower($email);
			$principleEmail	= $this->filterEmptyField($data, 'MembershipOrder', 'principle_email');
			$companyName	= $this->filterEmptyField($data, 'MembershipOrder', 'company_name');
			$domain			= $this->filterEmptyField($data, 'MembershipOrder', 'domain');
			$templateID		= $this->filterEmptyField($data, 'MembershipOrder', 'template_id');
			$themeID		= $this->filterEmptyField($data, 'MembershipOrder', 'theme_id');
			$isPrinciple	= $this->filterEmptyField($data, 'MembershipOrder', 'is_principle');
			$countLimit		= 1;

			if(empty($isPrinciple)){
				$countLimit = 2;
			}

			$options = array('limit' => $countLimit);

		//	debug($data);
		//	debug($countLimit);exit;

			if(empty($isPrinciple)){
			//	bukan principle, jadi data ada 2 (data pemesan sama data principle)
				$options = array_replace($options, array(
					'limit'			=> $countLimit, 
					'conditions'	=> array(
						'User.email' => array($email, $principleEmail), 
					//	'User.email' => array('jeffrobertindonesia@rumahku.com', 'jenpropertyindonesia@rumahku.com'), 
					), 
				));
			}
			else{
				$options = array_replace($options, array(
					'conditions' => array(
						'User.email' => $email, 
					), 
				));
			}

		//	get user
			$this->User->virtualFields = array(
				'username'	=> 'LOWER(User.username)',
				'email'		=> 'LOWER(User.email)',
			);

			$users = $this->User->getData('all', $options, array('status' => 'all'));

			if(empty($users) && $autoCreate === false){
			//	skip create user
				return array('status' => 'success');
			}
			else{
				$saveData			= array();
				$userID				= null;
				$principleID		= null;
				$groupID			= 1;
				$principleGroupID	= 3;

				if($users){
				//	data pemesan
					$userData	= Hash::extract($users, sprintf('{n}.User[email=%s]', $email));
					$userData	= $userData ? array_shift($userData) : array();
					$userID		= $this->filterEmptyField($userData, 'id');
					$groupID	= $this->filterEmptyField($userData, 'group_id', null, $groupID);

				//	data principal (note cuma principle yang group nya dipaksa jadi 3)
					$principleData	= Hash::extract($users, sprintf('{n}.User[email=%s]', $principleEmail));
					$principleData	= $principleData ? array_shift($principleData) : array();
					$principleID	= $this->filterEmptyField($principleData, 'id');
				}

				for($index = 0; $index < $countLimit; $index++){
					$groupID = ($index > 0 || $isPrinciple) ? $principleGroupID : $groupID;

					$userID		= $index > 0 ? $principleID : $userID;
					$prefix		= $index > 0 ? 'principle_' : '';

					$code			= $this->filterEmptyField($data, 'MembershipOrder', sprintf('%scode', $prefix), '');
					$activationCode	= $this->filterEmptyField($data, 'MembershipOrder', sprintf('%sactivation_code', $prefix));
					$fullName		= $this->filterEmptyField($data, 'MembershipOrder', sprintf('%sname', $prefix));
					$email			= $this->filterEmptyField($data, 'MembershipOrder', sprintf('%semail', $prefix));
					$phone			= $this->filterEmptyField($data, 'MembershipOrder', sprintf('%sphone', $prefix));

				//	additional pas edit
					$profileID			= $this->filterEmptyField($data, 'MembershipOrder', sprintf('%sprofile_id', $prefix));
					$companyID			= $this->filterEmptyField($data, 'MembershipOrder', sprintf('%scompany_id', $prefix));
					$companyConfigID	= $this->filterEmptyField($data, 'MembershipOrder', sprintf('%scompany_config_id', $prefix));

					$saveData[$index]['User']['id']			= $userID;
					$saveData[$index]['User']['group_id']	= $groupID;
					$saveData[$index]['User']['deleted']	= 0;
					$saveData[$index]['User']['active']		= 1;
					$saveData[$index]['User']['status']		= 1;

					if(empty($userID)){
					//	create
						$fullName	= $fullName ? explode(' ', $fullName, 2) : array();
						$firstName	= $this->filterEmptyField($fullName, 0);
						$lastName	= $this->filterEmptyField($fullName, 1);

						$saveData[$index]['User']['first_name']				= $firstName;
						$saveData[$index]['User']['last_name']				= $lastName;
						$saveData[$index]['User']['code']					= $code;
						$saveData[$index]['User']['email']					= $email;
						$saveData[$index]['UserConfig']['activation_code']	= $activationCode;
						$saveData[$index]['UserConfig']['token']			= String::uuid();
						$saveData[$index]['UserProfile']['phone']			= $phone;
						$saveData[$index]['UserProfile']['no_hp']			= $phone;

					//	user company hanya di insert pada saat create principal baru
					//	untuk update data (saat renewal), pengubahan data dilakukan pada saat paid

						if($index > 0 || $isPrinciple){
							$saveData[$index]['UserCompany']['name'] = $companyName;
						}
					}

				/*
					if($profileID){
						$saveData[$index]['UserProfile']['id'] = $profileID;
					}

				//	$saveData[$index]['UserProfile']['phone'] = $phone;

					if($index > 0 || $isPrinciple){
					//	ga selalu di bawa, jadi pake if
						if($companyID){
							$saveData[$index]['UserCompany']['id'] = $companyID;
						}

						if($companyName){
							$saveData[$index]['UserCompany']['name'] = $companyName;
						}

						if($companyConfigID){
							$saveData[$index]['UserCompanyConfig']['id'] = $companyConfigID;
						}

						if($domain){
							$saveData[$index]['UserCompanyConfig']['domain'] = $domain;
						}

						if($templateID){
							$saveData[$index]['UserCompanyConfig']['template_id'] = $templateID;
						}

						if($themeID){
							$saveData[$index]['UserCompanyConfig']['theme_id'] = $themeID;
						}
					}
				*/
				}

			//	debug($data);
			//	debug($saveData);exit;

			//	insert / update records
				$saved = $this->User->saveAll($saveData, array(
					'validate'	=> false, 
					'deep'		=> true, 
				));

				$result = array(
					'status'	=> $saved ? 'success' : 'error', 
					'msg'		=> __('%s menyimpan data.', $saved ? 'Berhasil' : 'Gagal'), 
				);
			}
		}
		else{
			$result = array(
				'status'	=> 'error', 
				'msg'		=> __('Data tidak valid.'), 
			);
		}

		return $result;
	}

	public function generateInvoiceNumber(){
	//	variable penting untuk switch app antar aplikasi rumahku
		$identifier	= Configure::read('__Site.invoice_prefix');

	//	invoice number format > APPIDENTIFIER + INV + YYYY + MM + DD + 00001 (16 digit)
		$currentDate		= date('Ymd');
		$documentPrefix		= sprintf('%s-INV%s', $identifier, $currentDate);

	//	get last invoice number
		$lastDocumentNumber = $this->field('invoice_number', array('invoice_number LIKE' => $documentPrefix.'%'), 'invoice_number DESC');

	//	generate new invoice number based on last one
		$incrementNumber	= substr($lastDocumentNumber, -4) + 1;
		$incrementNumber	= str_pad($incrementNumber, 4, 0, STR_PAD_LEFT);
		$newDocumentNumber	= $documentPrefix.$incrementNumber;

		return $newDocumentNumber;
	}

	public function setPaymentStatus($data, $newStatus = 'pending', $refererURL = null){
		$allowedStatus = array('pending', 'process', 'cancelled', 'expired', 'paid', 'waiting');

		if($data && in_array($newStatus, $allowedStatus)){
			$recordID = $this->filterEmptyField($data, 'Payment', 'id');

			if($recordID){
				$saveData = array(
					'Payment' => array(
						'id'				=> $recordID, 
						'payment_status'	=> $newStatus, 
					)
				);

				if($refererURL){
					$saveData = array_merge_recursive($saveData, array(
						'Payment' => array(
							'referer_url' => $refererURL, 
						), 
					));
				}

				if($newStatus == 'waiting'){
				//	set expired date untuk transfer atm / alfamart
					$expDuration = Configure::read('__Site.expired_hour_duration');
					$expDatetime = sprintf('+ %s hour', $expDuration);
					$expDatetime = date('Y-m-d H:i:s', strtotime($expDatetime));

					$saveData = array_merge_recursive($saveData, array(
						'Payment' => array(
							'transfer_expired_date' => $expDatetime,
						), 
					));
				}

				if($this->save($saveData)){
					$data = array_replace_recursive($data, $saveData);
				}
			}
		}

		return $data;
	}

	public function doCheckout($data = NULL){
		$result = array(
			'status'	=> 'error',
			'msg'		=> __('Gagal menyimpan data. Tidak ada data untuk disimpan'),
			'data'		=> $data,
		);

		if($data){
		//	jangan percaya data post
			$paymentID	= !empty($data['Payment']['id']) ? trim($data['Payment']['id']) : NULL;
			$userID		= !empty($data['Payment']['user_id']) ? trim($data['Payment']['user_id']) : NULL;
			$conditions	= array(
				'conditions' => array(
					'Payment.id'				=> $paymentID,
					'Payment.payment_status'	=> array('pending', 'process', 'failed') // failed untuk payment gagal mau nembak lagi
				)
			);

			$isAdmin = Configure::read('User.Admin.Rumahku');
			if(!$isAdmin){
				$conditions['conditions'] = array_merge(
					$conditions['conditions'],
					array('Payment.user_id' => $userID)
				);
			}

			$record = $this->getData('first', $conditions);

			if($record){
				$invoiceNumber	= $this->filterEmptyField($record, 'Payment', 'invoice_number');
				$baseAmount		= $this->filterEmptyField($record, 'Payment', 'base_amount');
				$discountAmount	= $this->filterEmptyField($record, 'Payment', 'discount_amount');
				$totalAmount	= $this->filterEmptyField($record, 'Payment', 'total_amount');
				$paymentStatus	= $this->filterEmptyField($record, 'Payment', 'payment_status');
				$currentDate	= date('YmdHis');
				$sessionID		= md5($userID.$currentDate);
				$itemAmount		= 1;
				$currency		= $this->filterEmptyField($record, 'Payment', 'currency_code', 360);

				$voucherCode	= $this->filterEmptyField($data, 'Payment', 'voucher_code');
				$paymentChannel	= $this->filterEmptyField($data, 'Payment', 'payment_channel');
				$agreement	= $this->filterEmptyField($data, 'Payment', 'agreement');

			//	prepare update data
				$updateData = array(
					'currency_code'		=> $currency,
					'session_id'		=> $sessionID,
					'payment_channel'	=> $paymentChannel,
					'agreement' 		=> $agreement,
				);

				if($paymentChannel == '15'){
					$tenor		= $this->filterEmptyField($data, 'Payment', 'tenor', '00');
					$promoID	= str_pad($tenor, 3, 0, STR_PAD_LEFT);
					$updateData	= array_merge(
						$updateData,
						array(
							'installment_acquirer'	=> '',
							'tenor'					=> $tenor,
							'promo_id'				=> $promoID
						)
					);
				}

				if($voucherCode){
					$this->Voucher	= ClassRegistry::init('Voucher');
					$voucher		= $this->Voucher->getVoucher($voucherCode, $invoiceNumber);
					$voucherStatus	= $this->filterEmptyField($voucher, 'status', NULL, 'error');

					if($voucher && $voucherStatus == 'success'){
						$voucher		= $this->filterEmptyField($voucher, 'data');
						$voucherCodeID	= $this->filterEmptyField($voucher, 'voucher_code_id');
						$discountType	= $this->filterEmptyField($voucher, 'discount_type', NULL, 'nominal');
						$discountValue	= $this->filterEmptyField($voucher, 'discount_value', NULL, 0);
						$isUsed			= $this->filterEmptyField($voucher, 'is_used', NULL, FALSE);

						if($discountType == 'percentage'){
							$discountValue = ($baseAmount / 100) * $discountValue;
							$discountValue = floor($discountValue);
						}

						$discountAmount	= $discountValue;
						$totalAmount	= $baseAmount - $discountAmount; // discount cuma sekali, jadi pengurangan selalu balik ke base_amount

						if($totalAmount < 0){
						//	jika ada kasus transaksi dengan menggunakan voucher dan nilai voucher lebih besar dari nilai invoice
							$totalAmount = 0;
						}

						$discountAmount	= number_format($discountAmount, 2, '.', '');
						$totalAmount	= number_format($totalAmount, 2, '.', '');
						$updateData		= array_merge($updateData, array(
							'voucher_code_id'	=> $voucherCodeID,
							'discount_amount'	=> $discountAmount,
							'total_amount'		=> $totalAmount,
						));
					}
				}

			//	SET AUTO "PAID" IF GRAND TOTAL IS LESS THAN OR EQUAL 0 ==========================
				if($totalAmount <= 0){
					$paymentStatus	= 'paid';
					$updateData		= array_merge($updateData, array(
						'payment_status'	=> $paymentStatus,
						'payment_datetime'	=> date('Y-m-d H:i:s')
					));
				}
			//	=================================================================================
				$this->set($updateData);
			//	$this->validates();
			//	debug($this->validationErrors);exit;
				
				if($this->validates() && $this->save()){
					$data = array_replace_recursive($data, $this->read());

				//	log voucher usage
					if($voucherCode && !empty($voucherStatus) && $voucherStatus == 'success' && $isUsed === FALSE){
						$voucherUsageLog = $this->Voucher->logUsage($voucherCodeID, $paymentID);
					}

					if($paymentStatus == 'paid'){
					//	PEMBAYARAN YANG OTOMATIS LUNAS (BY DISCOUNT / VOUCHER)
						$result = $this->processPaidInvoice(array_replace_recursive($record, $data));
					}
					else{
					//	PEMBAYARAN VIA DOKU
						$mallID				= !empty($data['Payment']['mall_id']) ? $data['Payment']['mall_id'] : NULL;
						$sharedKey			= !empty($data['Payment']['shared_key']) ? $data['Payment']['shared_key'] : NULL;
						$chainMerchant		= 'NA';
						$secretWord			= sha1($totalAmount.$mallID.$sharedKey.$invoiceNumber);
						$name				= !empty($record['MembershipOrder']['name']) ? $record['MembershipOrder']['name'] : NULL;
						$email				= !empty($record['MembershipOrder']['email']) ? $record['MembershipOrder']['email'] : NULL;
						$packageName		= !empty($record['MembershipPackage']['name']) ? $record['MembershipPackage']['name'] : NULL;
						$additionalData		= '';
						$basket				= sprintf('%s,%s,%s,%s', $packageName, $totalAmount, $itemAmount, $totalAmount);
						$postData			= array(
							'MALLID'			=> $mallID,
							'CHAINMERCHANT'		=> $chainMerchant,
							'AMOUNT'			=> $totalAmount,
							'PURCHASEAMOUNT'	=> $totalAmount,
							'TRANSIDMERCHANT'	=> $invoiceNumber,
							'WORDS'				=> $secretWord,
							'REQUESTDATETIME'	=> $currentDate,
							'CURRENCY'			=> $currency,
							'PURCHASECURRENCY'	=> $currency,
							'SESSIONID'			=> $sessionID,
							'NAME'				=> $name,
							'EMAIL'				=> $email,
							'ADDITIONALDATA'	=> $additionalData,
							'BASKET'			=> $basket,
							'PAYMENTCHANNEL'	=> $paymentChannel, 
						);

						if($paymentChannel == '15'){
							$tenor		= !empty($data['Payment']['tenor']) ? $data['Payment']['tenor'] : '00';
							$promoID	= str_pad($tenor, 3, 0, STR_PAD_LEFT);
							$postData	= array_merge($postData, array(
								'INSTALLMENT_ACQUIRER'	=> '', 
								'TENOR'					=> $tenor, 
								'PROMOID'				=> $promoID, 
							));
						}

						$result['status']		= 'success';
						$result['msg']			= __('Berhasil menyimpan data Invoice.');
						$result['post_data']	= $postData;
					}
				}
				else{
					$result['status']	= 'error';
					$result['msg']		= __('Gagal menyimpan data Invoice.');
					$result['validationErrors'] = $this->validationErrors;
				}
			}
			else{
				$result['status']	= 'error';
				$result['msg']		= __('Invoice tidak ditemukan.');
			}
		}

		return $result;
	}

	public function doToggle($id = NULL){
		if(is_array($id)){
			$id = array_filter($id);
		}

		$options = array(
			'conditions' => array(
				'Payment.id'				=> $id,
				'Payment.payment_status'	=> array('pending', 'process'),
			),
		);

		$records = $this->getData('all', $options);
		if($records){
			$invoiceNumbers	= Set::extract('/Payment/invoice_number', $records);
			$invoiceNumbers	= implode(', ', $invoiceNumbers);

			$flag	= $this->updateAll(array('Payment.payment_status' => "'cancelled'"), array('Payment.id' => $id));
			$result	= array(
				'status'	=> $flag ? 'success' : 'error', 
				'msg'		=> __('%s membatalkan Invoice : %s', ($flag ? 'Berhasil' : 'Gagal'), $invoiceNumbers),
				'data'		=> $records, 
			);
		}
		else{
			$result = array('status' => 'error', 'msg' => __('Gagal membatalkan Invoice. Data tidak ditemukan'));
		}

		return $result;
	}

	public function filterExpiredDocument(){
		$expiredDocuments = $this->getData('all', array(
			'recursive'		=> 1,
			'conditions'	=> array(
				'Payment.payment_status' => array('pending', 'process', 'failed'),
				'DATE_FORMAT(Payment.expired_date, \'%Y-%m-%d %H:%i\') <=' => date('Y-m-d H:i'),
			)
		));

		return $expiredDocuments;
	}

	public function processPaidInvoice($record = null){
		$result = array(
			'status'	=> 'error', 
			'msg'		=> __('Tidak ada data untuk diproses'), 
			'data'		=> $record, 
		);

		if($record){
		//	update user company config, set paket aktif + tanggal tayang
			$userID			= $this->filterEmptyField($record, 'Payment', 'user_id');
			$principleID	= $this->filterEmptyField($record, 'Payment', 'principle_id');
			$invoiceNumber	= $this->filterEmptyField($record, 'Payment', 'invoice_number');

			$isPrinciple	= $this->filterEmptyField($record, 'MembershipOrder', 'is_principle');
			$loopCount		= $isPrinciple ? 1 : 2;

			$packageID		= $this->filterEmptyField($record, 'MembershipPackage', 'id');
			$monthDuration	= $this->filterEmptyField($record, 'MembershipPackage', 'month_duration', 0);

			$companyConfig	= $this->User->UserCompanyConfig->getData('first', array(
				'conditions' => array(
					'UserCompanyConfig.user_id' => $principleID, 
				), 
			));

		//	save data
		//	1. pemesan		: User, UserProfile
		//	2. principal	: User, UserProfile, UserCompany, UserCompanyConfig

			$configID = $this->filterEmptyField($companyConfig, 'UserCompanyConfig', 'id');
			$saveData = array();

			for($index = 0; $index < $loopCount; $index++){
				$prefix		= $index > 0 ? 'principle_' : null;
				$fullName	= $this->filterEmptyField($record, 'MembershipOrder', sprintf('%sname', $prefix));
				$email		= $this->filterEmptyField($record, 'MembershipOrder', sprintf('%semail', $prefix));
				$phone		= $this->filterEmptyField($record, 'MembershipOrder', sprintf('%sphone', $prefix));

				$fullName	= $fullName ? explode(' ', $fullName, 2) : array();
				$firstName	= $this->filterEmptyField($fullName, 0);
				$lastName	= $this->filterEmptyField($fullName, 1);

				$tempUserID	= ($index > 0 || $isPrinciple) ? $principleID : $userID;
				$profileID	= $this->User->UserProfile->field('UserProfile.id', array(
					'UserProfile.user_id' => $tempUserID, 
				));

				$saveData[$index] = array(
					'User' => array(
						'id'			=> $tempUserID, 
						'first_name'	=> $firstName, 
						'last_name'		=> $lastName, 
					), 
					'UserProfile' => array(
						'id'		=> $profileID, 
						'user_id'	=> $tempUserID, 
						'phone'		=> $phone, 
					), 
				);

				if($index > 0 || $isPrinciple){
					$companyName	= $this->filterEmptyField($record, 'MembershipOrder', 'company_name');
					$domain			= $this->filterEmptyField($record, 'MembershipOrder', 'domain');
					$themeID		= $this->filterEmptyField($record, 'MembershipOrder', 'theme_id');
					$templateID		= $this->filterEmptyField($record, 'MembershipOrder', 'template_id');

					$oldPackageID	= $this->filterEmptyField($companyConfig, 'UserCompanyConfig', 'membership_package_id');
					$liveDate		= $this->filterEmptyField($companyConfig, 'UserCompanyConfig', 'live_date');
					$endDate		= $this->filterEmptyField($companyConfig, 'UserCompanyConfig', 'end_date');

					if($oldPackageID != $packageID){
					//	kalo paket beda dengan sebelumnya, paket lama angus
						$newLiveDate	= date('Y-m-d');
						$newEndDate		= date('Y-m-d', strtotime(sprintf('%s +%s month', $newLiveDate, $monthDuration)));
					}
					else{
					//	kalo sama paket lama di extend
						$newLiveDate	= $liveDate;
						$newEndDate		= date('Y-m-d', strtotime(sprintf('%s +%s month', $endDate, $monthDuration)));
					}

					$companyID = $this->User->UserCompany->field('UserCompany.id', array(
						'UserCompany.user_id' => $tempUserID, 
					));

					$saveData[$index]['UserCompany']['id']		= $companyID;
					$saveData[$index]['UserCompany']['user_id']	= $tempUserID;
					$saveData[$index]['UserCompany']['name']	= $companyName;

					$saveData[$index]['UserCompanyConfig']['id']					= $configID;
					$saveData[$index]['UserCompanyConfig']['user_id']				= $tempUserID;
					$saveData[$index]['UserCompanyConfig']['membership_package_id']	= $packageID;
					$saveData[$index]['UserCompanyConfig']['live_date']				= $newLiveDate;
					$saveData[$index]['UserCompanyConfig']['end_date']				= $newEndDate;

					if($domain){
						$saveData[$index]['UserCompanyConfig']['domain'] = $domain;
					}

					if($themeID){
						$saveData[$index]['UserCompanyConfig']['theme_id'] = $themeID;
					}

					if($templateID){
						$saveData[$index]['UserCompanyConfig']['template_id'] = $templateID;
					}
				}
			}

		//	buat debug
		//	$this->setPaymentStatus($record, 'process');

			$saved = $this->User->saveAll($saveData, array(
				'validate'	=> false, 
				'deep'		=> true, 
			));

			$result	= array(
				'status'	=> $saved ? 'success' : 'error', 
				'msg'		=> __('%s menyimpan data Invoice <strong>%s</strong>', ($saved ? 'Berhasil' : 'Gagal'), $invoiceNumber), 
				'data'		=> $record, 
			);

		//	debug($saveData);
		//	debug($record);
		//	debug($companyConfig);
		//	debug($result);
		//	exit;
		}

		return $result;
	}
}
?>
