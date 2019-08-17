<?php
App::uses('AppController', 'Controller');

class BankController extends AppController {
	public $components = array(
		'RmKpr',
		'Rest.Rest' => array(
            'actions' => array(
	            'api_sync_bank' => array(
	            	'extract' => array(
			  			'msg', 'status', 'link', 'appversion', 'device',
	                	'id', 'validationErrors'
	            	),
	            ),
	            'api_sync_bank_setting' => array(
	            	'extract' => array(
			  			'msg', 'status', 'link', 'appversion', 'device',
	                	'id', 'validationErrors'
	            	),
	            ),
	            'api_sync_commission_setting' => array(
	            	'extract' => array(
			  			'msg', 'status', 'link', 'appversion', 'device',
	                	'id', 'validationErrors'
	            	),
	            ),
	            'api_sync_commission_Setting_loan' => array(
	            	'extract' => array(
			  			'msg', 'status', 'link', 'appversion', 'device',
	                	'id', 'validationErrors'
	            	),
	            ),
	            'api_promo_info' => array(
	            	'extract' => array(
			  			'msg', 'status', 'link', 'appversion', 'device',
	                	'data',
	            	),
	            ),
            ),
            'debug' => 2,
		),
	);

	function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->allow(array(
			'get_sync_bank_kpr', 
			'update_data', 
			'update_data_lead_kpr', 
			'update_data_log_kpr',
			'admin_index',
			'api_sync_bank',
			'api_sync_bank_setting',
			'api_sync_commission_setting',
			'api_sync_commission_Setting_loan',
		));
	}

	public function admin_index() {
		$options =  $this->Bank->_callRefineParams($this->params);
		$this->RmCommon->_callRefineParams($this->params);

        $this->paginate = $this->Bank->getData('paginate', $options);
		$values = $this->paginate('Bank');

		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'Bank', 'id');
				$value = $this->Bank->BankSetting->getMerge($value, $id);
				$values[$key] = $value;
			}
		}

    	$this->set('module_title', __('Daftar Bank'));
		$this->set('active_menu', 'bank');
		$this->set(compact(
			'values'
		));
	}

	public function api_sync_bank(){
		$data = $this->request->data;
		$result = $this->_api_saveSyncBank($data['Bank']['merge_vars']);	
	}

	function _api_saveSyncBank($data){
		$result = $this->Bank->doSaveSyncBank($data);
		$this->set('msg',$result['msg']);
		$this->set('status',$result['status']);
		$this->set('id',$result['id']);
		$this->set('validationErrors',$result['validationErrors']);
	}

	public function api_sync_bank_setting(){
		$data = $this->request->data;
		$result = $this->_api_saveSyncBankSetting($data['Bank']['merge_vars']);	
	}

	function _api_saveSyncBankSetting($data){
		$result = $this->Bank->BankSetting->doSaveSyncBankSetting($data);
		$this->set('msg',$result['msg']);
		$this->set('status',$result['status']);
		$this->set('id',$result['id']);
		$this->set('validationErrors',$result['validationErrors']);
	}

	public function api_sync_commission_setting(){
		$data = $this->request->data;
		$result = $this->_api_saveSyncCommissionSetting($data['Bank']['merge_vars']);	
	}

	function _api_saveSyncCommissionSetting($data){

		$result = $this->Bank->BankCommissionSetting->doSaveSync($data);	
		$this->set('msg',$result['msg']);
		$this->set('status',$result['status']);
		$this->set('id',$result['id']);
		$this->set('validationErrors',$result['validationErrors']);
	}

	public function api_sync_commission_Setting_loan(){
		$data = $this->request->data;
		$result = $this->_api_sync_commission_Setting_loan($data['Bank']['merge_vars']);
	}

	function _api_sync_commission_Setting_loan($data){
		$result = $this->Bank->BankCommissionSetting->BankCommissionSettingLoan->doSaveSync($data);	
		$this->set('msg',$result['msg']);
		$this->set('status',$result['status']);
		$this->set('id',$result['id']);
		$this->set('validationErrors',$result['validationErrors']);
	}

	public function admin_promos() {
		$params = $this->params->params;
		$conditions = $this->Bank->getData('conditions', array(
			'conditions' => array(
				'Bank.name <>' => NULL,
			),
		));

		$options =  $this->Bank->BankProduct->_callRefineParams($params, array(
			'conditions' => $conditions,
			'contain' => array(
				'Bank',
			),
		));
		$this->RmCommon->_callRefineParams($params);

		$this->paginate = $this->Bank->BankProduct->getData('paginate', $options, array(
			'status' => 'publish',
		));
		$values = $this->paginate('BankProduct');
		$values = $this->Bank->BankProduct->getMergeList($values, array(
			'contain' => array(
				'BankSetting' => array(
					'elements' => array(
						'type' => 'product',
					),
				),
			),
		));

    	$this->set('module_title', __('Daftar Promo Bank'));
		$this->set('active_menu', 'bank_promo');
		$this->set(compact(
			'values'
		));
	}

	public function admin_promo_info( $id = null ) {
		$value = $this->Bank->BankProduct->getData('first', array(
			'conditions' => array(
				'BankProduct.id' => $id,
			),
		), array(
			'status' => 'publish',
		));

		if(!empty($value)){
			$title = __('Informasi Promo KPR');
			$value = $this->Bank->BankProduct->getMergeList($value, array(
				'contain' => array(
					'Bank',
					'BankSetting' => array(
						'elements' => array(
							'type' => 'product',
						),
					),
				),
			));
			$bank_id = Common::hashEmptyField($value, 'BankProduct.bank_id');

			$this->paginate = $this->Bank->BankUser->getData('paginate', array(
				'conditions' => array(
					'BankUser.bank_id' => $bank_id,
				),
			));
			$sales = $this->paginate('BankUser');
			$sales = $this->Bank->BankUser->getMergeList($sales, array(
				'contain' => array(
					'BankUserProfile',
				),
			));

			$this->RmCommon->_callDataForAPI($value, 'manual');
			$this->set(array(
				'active_menu' => 'bank_promo',
				'module_title' => $title,
				'title_for_layout' => $title,
				'value' => $value,
				'sales' => $sales,
			));
		} else {
			$this->RmCommon->redirectReferer(__('Promo tidak tersedia'));
		}
	}

	public function api_promo_info( $id = null ) {
		$value = $this->Bank->BankProduct->getData('first', array(
			'conditions' => array(
				'BankProduct.id' => $id,
			),
			'fields' => array(
				'BankProduct.id',
				'BankProduct.bank_id',
				'BankProduct.name',
				'BankProduct.date_from',
				'BankProduct.date_to',
				'BankProduct.text_promo',
			),
		), array(
			'status' => 'publish',
		));

		if(!empty($value)){
			$title = __('Informasi Promo KPR');
			$value = $this->Bank->BankProduct->getMergeList($value, array(
				'contain' => array(
					'Bank' => array(
						'fields' => array(
							'Bank.id',
							'Bank.code',
							'Bank.name',
							'Bank.logo',
							'Bank.promo_text',
						),
					),
					'BankSetting' => array(
						'fields' => array(
							'BankSetting.id',
							'BankSetting.interest_rate_fix',
							'BankSetting.interest_rate_cabs',
							'BankSetting.interest_rate_float',
							'BankSetting.periode_installment',
							'BankSetting.periode_fix',
							'BankSetting.periode_cab',
							'BankSetting.dp',
							'BankSetting.provision',
							'BankSetting.category_appraisal',
							'BankSetting.appraisal',
							'BankSetting.category_administration',
							'BankSetting.administration',
						),
						'elements' => array(
							'type' => 'product',
						),
					),
				),
			));
			$bank_id = Common::hashEmptyField($value, 'BankProduct.bank_id');

			$category_appraisal = Common::hashEmptyField($value, 'BankSetting.category_appraisal');
			$appraisal = Common::hashEmptyField($value, 'BankSetting.appraisal');
			$category_administration = Common::hashEmptyField($value, 'BankSetting.category_administration');
			$administration = Common::hashEmptyField($value, 'BankSetting.administration');

			$appraisal = Common::getNameNominalOrPercent($category_appraisal, $appraisal, false);
			$administration = Common::getNameNominalOrPercent($category_administration, $administration, false);

	        $promo = Common::hashEmptyField($value, 'Bank.promo_text');
	        $promo = Common::hashEmptyField($value, 'BankProduct.text_promo', $promo);

			$value = Common::_callUnset($value, array(
				'Bank' => array(
					'promo_text',
				),
				'BankSetting' => array(
					'category_appraisal',
					'category_administration',
				),
				'BankProduct' => array(
					'text_promo',
				),
			));

			$value['BankSetting']['appraisal'] = $appraisal;
			$value['BankSetting']['administration'] = $administration;
			$value['BankProduct']['promo_info'] = $promo;
			$value['BankProduct']['promo_terms'] = KprCommon::_callTermsConditions(array(
				'provision' => false,
			));

			$this->RmCommon->_callDataForAPI($value, 'manual');
		} else {
			$this->RmCommon->redirectReferer(__('Promo tidak tersedia'));
		}
	}
} 
?>