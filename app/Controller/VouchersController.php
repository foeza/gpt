<?php
App::uses('AppController', 'Controller');
class VouchersController extends AppController {
	public $uses = array('Voucher');

	public function beforeFilter(){
		parent::beforeFilter();
		$this->Auth->allow(array(
			'validateVoucher' // jangan diapus, untuk client check voucher
		));

		$this->set('active_menu', 'voucher');
	}

	public function admin_search($action, $_admin = TRUE){
		$data	= $this->request->data;
		$named	= $this->RmCommon->filterEmptyField($this->params, 'named');
		$params	= array('action' => $action, 'admin' => $_admin);

		if(!empty($named)){
			$params = array_merge($params, $named);
		}

		$this->RmCommon->processSorting($params, $data);
	}

	public function admin_index(){
		$options = array(
			'conditions' => array(
				'Voucher.status'		=> 1, 
				'Voucher.is_deleted'	=> 0
			), 
			'order' => array(
				'Voucher.created' => 'DESC'
			),
			'limit' => Configure::read('__Site.config_new_table_pagination'),
		);

		$options = $this->Voucher->_callRefineParams($this->params, $options);

		$this->RmCommon->_callRefineParams($this->params);

		$this->paginate	= $this->Voucher->getData('paginate', $options);
		$records		= $this->paginate('Voucher');

		$this->set(array(
			'module_title'		=> __('Daftar Voucher'), 
			'title_for_layout'	=> __('Daftar Voucher')
		));

		$this->set(compact('records'));
	}

	public function admin_add(){
		if($this->request->data){
			$data	= $this->request->data;
			$result	= $this->processVoucher($data);

			$this->RmCommon->setProcessParams($result, array('action' => 'index', 'admin' => TRUE));
		}

		$packages = $this->Voucher->VoucherDetail->MembershipPackage->getData('list', array(
			'conditions' => array(
				'MembershipPackage.status'		=> 1, 
				'MembershipPackage.is_deleted'	=> 0, 
			)
		));

		$this->set(array(
			'module_title'		=> __('Tambah Voucher'), 
			'title_for_layout'	=> __('Tambah Voucher'), 
			'packages'			=> $packages
		));

		$this->render('admin_form');
	}

	public function admin_edit($recordID = NULL){
		$record = $this->Voucher->getData('first', array(
			'conditions' => array(
				'Voucher.id'			=> $recordID, 
				'Voucher.status'		=> 1, 
				'Voucher.is_deleted'	=> 0, 
			)
		));

		if($record){
			$result	= array('data' => $record);
			$data	= $this->request->data;

			if($data){
				$codeMechanism	= Hash::get($record, 'Voucher.code_mechanism', 'auto');
				$length			= Hash::get($record, 'Voucher.length');
				$applyTo		= Hash::get($record, 'Voucher.apply_to', 'all');

				if($codeMechanism == 'manual'){
				//	untuk manual record VoucherCode cuma 1
					$voucherCodeData	= Hash::get($record, 'VoucherCode.0', array());
					$voucherCodeID		= Hash::get($voucherCodeData, 'id');
					$currentLimit		= Hash::get($voucherCodeData, 'usage_limit');

					$data['VoucherCode'][0]['id']				= $voucherCodeID;
					$data['VoucherCode'][0]['current_limit']	= $currentLimit;
				}

				$data['Voucher']['id']				= $recordID;
				$data['Voucher']['code_mechanism']	= $codeMechanism;
				$data['Voucher']['apply_to']		= $applyTo;

				$result			= $this->processVoucher($data);
				$saveStatus		= $this->RmCommon->filterEmptyField($result, 'status');
				$saveMessage	= $this->RmCommon->filterEmptyField($result, 'msg');
				$saveVoucher	= $this->RmCommon->filterEmptyField($result, 'data', 'Voucher');

				if(!empty($record['Voucher']) && !empty($data['Voucher'])){
					$record['Voucher'] = array_replace_recursive($record['Voucher'], $data['Voucher']);
				}

				$this->set('postData', $this->request->data);
				$result = array(
					'status'	=> $saveStatus, 
					'msg'		=> $saveMessage, 
					'data'		=> $record, 
				);
			}
			else{
				$periodType = $this->RmCommon->filterEmptyField($record, 'Voucher', 'period_type');
				if($periodType == 'periodic'){
					$dateFrom	= $this->RmCommon->filterEmptyField($record, 'Voucher', 'date_from');
					$dateTo		= $this->RmCommon->filterEmptyField($record, 'Voucher', 'date_to');

					$result = array_merge_recursive($result, array(
						'data' => array(
							'Voucher' => array(
								'period_date' => $this->RmCommon->_callReverseDateRange($dateFrom, $dateTo), 
							), 
						), 
					));
				}
			}

			$this->RmCommon->setProcessParams($result, array('action' => 'index', 'admin' => TRUE));

			$packages = $this->Voucher->VoucherDetail->MembershipPackage->getData('list', array(
				'conditions' => array(
					'MembershipPackage.status'		=> 1, 
					'MembershipPackage.is_deleted'	=> 0, 
				)
			));

			$this->set(array(
				'module_title'		=> __('Ubah Voucher'), 
				'title_for_layout'	=> __('Ubah Voucher'), 
				'packages'			=> $packages
			));

			$this->render('admin_form');
		}
		else{
			$this->RmCommon->setCustomFlash(__('Data tidak ditemukan.'), 'error');
			$this->redirect(array('action' => 'index', 'admin' => TRUE));
		}
	}

	public function admin_delete($recordID = NULL){
		$data	= $this->request->data;
		$id		= $this->RmCommon->filterEmptyField($data, 'Voucher', 'id');
    	$result = $this->Voucher->doToggle($id);

		$this->RmCommon->setProcessParams($result, FALSE, array('redirectError' => TRUE));
	}

	public function processVoucher($data = NULL){
		$result = array(
			'status'	=> 'error', 
			'msg'		=> __('Proses gagal, tidak ada data untuk diproses.'), 
			'data'		=> $data, 
		);

		if($data){
			$userID			= Configure::read('User.id');
			$voucherID		= $this->RmCommon->filterEmptyField($data, 'Voucher', 'id');
			$code			= $this->RmCommon->filterEmptyField($data, 'Voucher', 'code');
			$name			= $this->RmCommon->filterEmptyField($data, 'Voucher', 'name');
			$length			= $this->RmCommon->filterEmptyField($data, 'Voucher', 'length');
			$prefix			= $this->RmCommon->filterEmptyField($data, 'Voucher', 'prefix');
			$prefix			= strtoupper($prefix);
			$codeMechanism	= $this->RmCommon->filterEmptyField($data, 'Voucher', 'code_mechanism', 'auto');
			$codeMechanism	= in_array($codeMechanism, array('auto', 'manual')) ? $codeMechanism : 'auto';
			$periodType		= $this->RmCommon->filterEmptyField($data, 'Voucher', 'period_type', 'unlimited');
			$periodType		= in_array($periodType, array('unlimited', 'periodic')) ? $periodType : 'unlimited';
			$applyTo		= $this->RmCommon->filterEmptyField($data, 'Voucher', 'apply_to', 'all');
			$applyTo		= in_array($applyTo, array('all', 'manual')) ? $applyTo : 'all';

		//	prepare voucher data
			$voucherData = array(
				'id'				=> $voucherID, 
				'user_id'			=> $userID, 
				'code'				=> $code, 
				'name'				=> $name, 
				'length'			=> $length, 
				'prefix'			=> $prefix, 
				'code_mechanism'	=> $codeMechanism, 
				'period_type'		=> $periodType, 
				'apply_to'			=> $applyTo, 
			);

			if($periodType == 'periodic'){
				$periodDate	= $this->RmCommon->filterEmptyField($data, 'Voucher', 'period_date');
				$periodDate	= $this->RmCommon->_callConvertDateRange(array(), $periodDate);
				$dateFrom	= Hash::get($periodDate, 'date_from');
				$dateTo		= Hash::get($periodDate, 'date_to');

				$voucherData = array_merge($voucherData, array(
					'period_date'	=> $periodDate, 
					'date_from'		=> $dateFrom, 
					'date_to'		=> $dateTo, 
				));
			}
			else{
				$voucherData = array_merge($voucherData, array(
					'date_from'		=> '', 
					'date_to'		=> '', 
				));
			}

		//	prepare voucher code data
			$usageLimit			= Hash::get($data, 'VoucherCode.0.usage_limit', 0);
			$usageLimit			= intval(str_replace(',', '', $usageLimit));
			$voucherCodeData	= array();

			if($codeMechanism == 'auto'){
				if($usageLimit){
					for($i = 0; $i < $usageLimit; $i++){
						$voucherCode = $length ? $this->generateCode($length, $prefix) : null;
						$voucherCodeData[] = array(
							'code'			=> $voucherCode, 
							'usage_limit'	=> 1, 
						);
					}	
				}
				else if(empty($voucherID)){
				//	buat mancing validasi doang, pas add
					$voucherCode		= $length ? $this->generateCode($length, $prefix) : null;
					$voucherCodeData[]	= array(
						'code'			=> $voucherCode, 
						'usage_limit'	=> $usageLimit, 
					);
				}
			}
			else{
				$voucherCode = Hash::get($data, 'VoucherCode.0.code');

				if($voucherID){
				//	edit voucher dengan mekanisme kode manual, cukup edit usage_limit aja
					$voucherCodeID		= Hash::get($data, 'VoucherCode.0.id');
					$currentLimit		= Hash::get($data, 'VoucherCode.0.current_limit');

					$usageLimit			= $currentLimit + $usageLimit;
					$voucherCodeData[]	= array(
						'id'			=> $voucherCodeID, 
						'code'			=> $voucherCode, 
						'usage_limit'	=> $usageLimit, 
					);
				}
				else{
					$voucherCodeData[] = array(
						'code'			=> $voucherCode, 
						'usage_limit'	=> $usageLimit, 
					);
				}

			//	untuk manual length otomatis ambil length code
				$voucherData = array_replace_recursive($voucherData, array(
					'length' => strlen($voucherCode), 
				));
			}

		//	prepare voucher detail data
			$voucherDetailData = array();
			if($applyTo == 'manual'){
				$voucherDetailData	= $this->RmCommon->filterEmptyField($data, 'VoucherDetail');
				$arrMembershipID	= Set::extract('/membership_package_id', $voucherDetailData);

				if($voucherDetailData){
					foreach($voucherDetailData as &$value){
						$discountValue = Hash::get($value, 'discount_value', 0);
						$discountValue = str_replace(',', '', $discountValue);

						$value['discount_value']		= $discountValue;
						$value['selected_package_id']	= $arrMembershipID;
					}
				}
			}
			else{
				if(empty($voucherID)){
				//	untuk insert aja, karena di edit ga ada ubah nominal / persentase
					$discountType	= $this->RmCommon->filterEmptyField($data, 'Voucher', 'discount_type', NULL, 'nominal');
					$discountType	= in_array($discountType, array('nominal', 'percentage')) ? $discountType : 'nominal';
					$discountValue	= $this->RmCommon->filterEmptyField($data, 'Voucher', 'discount_value', NULL, 0);
					$discountValue	= str_replace(',', '', $discountValue);
					$voucherData	= array_merge($voucherData, array(
						'discount_type'		=> $discountType, 
						'discount_value'	=> $discountValue, 
					));
				}
			}

			$saveData = array_merge(
				array('Voucher'			=> $voucherData), 
				array('VoucherCode'		=> $voucherCodeData), 
				array('VoucherDetail'	=> $voucherDetailData)
			);

		//	debug($saveData);exit;

			$saved	= $this->Voucher->saveAll($saveData);
			$result	= array(
				'status'	=> $saved ? 'success' : 'error', 
				'msg'		=> __('%s menyimpan data Voucher.', ($saved ? 'Berhasil' : 'Gagal')), 
			);
		}

		return $result;
	}

	public function generateCode($length, $prefix = NULL){
		$prefix = trim($prefix);
		if($prefix){
			$length = $length - strlen($prefix);
		}

		$randomCode		= $this->RmCommon->createRandomNumber($length, 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789', 33);
		$voucherCode	= strtoupper($prefix).implode('', $randomCode);
		$duplicateCount	= $this->Voucher->VoucherCode->getData('count', array(
			'conditions' => array(
				'VoucherCode.code'		=> $voucherCode,
				'VoucherCode.status'	=> 1,
			)
		));
 
		if($duplicateCount){
			$this->generateCode($length, $prefix);
		}
		else{
			return $voucherCode;
		}
	}

	public function validateVoucher(){
		$this->layout		= FALSE;
		$this->autoRender	= FALSE;

		$data	= $this->request->data;
		$isAjax	= $this->RequestHandler->isAjax();

		if($isAjax === FALSE){
			$result	= array('status' => 'error', 'msg' => __('Invalid method.'));
		}
		else{
			$result	= array('status' => 'error', 'msg' => __('Voucher tidak ditemukan atau sudah tidak berlaku.'));
			if($data){
				$voucherCode	= $this->RmCommon->filterEmptyField($data, 'code');
				$invoiceNumber	= $this->RmCommon->filterEmptyField($data, 'invoice');
				$voucher		= $this->Voucher->getVoucher($voucherCode, $invoiceNumber);
				$voucherStatus	= $this->RmCommon->filterEmptyField($voucher, 'status');

				if($voucherStatus == 'success'){
					$result = $voucher;
				}
			}
		}

		return json_encode($result);
	}
}
?>