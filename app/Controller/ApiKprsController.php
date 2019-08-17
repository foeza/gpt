<?php
App::uses('AppController', 'Controller');
App::uses('NumberHelper', 'View/Helper');

class ApiKprsController extends AppController {
	public $name = 'Kpr';

	public $components = array(
		'RmKpr', 'RmProperty', 'RmImage', 'Captcha',
		'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
	            'kpr_add_banks' => array(
	            	'extract' => array(
	                	'result',
	                ),
	            ),
	            'call_bank_kprs' => array(
	            	'extract' => array(
	                	'data',
	                ),
	            ),
	            'call_applicaton_kprs' => array(
	            	'extract' => array(
	                	'data',
	                ),
	            ),
	            'call_generate_marital' => array(
	            	'extract' => array(
	                	'data',
	                ),
	            ),
            ),
		),
	);

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array(
			'call_bank_kprs', 'call_applicaton_kprs', 'get_action_kpr',
			'get_commission_kpr', 'get_application_kpr_rumahku', 'bank_data',
			'call_generate_marital', 'bank_users', 'get_queue_mailblast',
		));

		$this->layout = 'ajax';	
		$this->autoLayout = false;
		$this->autoRender = true;
   	}

   	function call_generate_marital(){
   		$data = false;
   		$params = $this->params->query;
   		$lists = $this->RmCommon->filterEmptyField($params, 'lists');

   		if($lists){
   			foreach($lists AS $key => $code){
   				$value = $this->User->Kpr->KprBank->getData('first', array(
   					'conditions' => array(
   						'KprBank.code' => $code,
   					),
   				));

   				if($value){
   					$kpr_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'kpr_id');
   					$value = $this->User->Kpr->getMerge($value, $kpr_id);
   					$user_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id');
   					$value = $this->User->getMerge($value, $user_id, true);

   					$application = $this->User->Kpr->KprApplication->getData('first', array(
   						'conditions' => array(
   							'KprApplication.kpr_id' => $kpr_id,
   							'KprApplication.parent_id' => null
   						),
   					));

   					$status_marital_profile = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'status_marital');
   					$status_marital = $this->RmCommon->filterEmptyField($application, 'KprApplication', 'status_marital', $status_marital_profile);

   					$data[$code] = $status_marital;
   				}else{
   					$data[$code] = false;
   				}
   			}
   		}
   		$this->set('data', $data);
   	}

   	function bank_users($token = false){
   		$this->loadModel('Setting');
		App::import('Helper', 'Html');

		$this->Html = new HtmlHelper(new View(null));
		$url = Configure::read('__Site.kpr_url');
		$flag = FALSE;

		$slug = 'bank-user';
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => $slug,
				'token' => $token,
			),
		));

		if($setting){
			$setting_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
			$domain = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
			$passkey = $this->RmCommon->filterEmptyField($setting, 'Setting', 'token');
			$value = $this->RmCommon->filterEmptyField($setting, 'Setting', 'value');
			$temp = $this->RmCommon->filterEmptyField($setting, 'Setting', 'temp');
			$offset = $this->RmCommon->filterEmptyField($setting, 'Setting', 'offset');

			$params = array(
				'token' => $passkey,
				'slug' => $slug,
				'offset' => $offset,
				'lastDate' => $value,
			);

			$apiUrl = $domain.$this->Html->url(array(
				'controller' => 'api_kprs',
				'action' => 'user_synchronize',
				'?' => $params,
				'ext' => 'json',
				'admin' => false,
				// 'api' => true,
			));

			$apiUrl = htmlspecialchars_decode($apiUrl);
			$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
			$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data', 'data');
			$status = $this->RmCommon->filterEmptyField($dataApi, 'status');
			$datas = $this->RmCommon->filterEmptyField($dataApi, 'User');
			$msg = $this->RmCommon->filterEmptyField($dataApi, 'msg');
			$data_offset = $this->RmCommon->filterEmptyField($dataApi, 'offset');

			if(!empty($status) && !empty($datas)){
				$this->loadModel('BankUser');

				foreach ($datas as $key => $data) {
					$modified = Common::HashEmptyField($data, 'User.modified');
					$data = $this->RmUser->apiUserBeforeSave($data);
					
					$result = $this->BankUser->doSaveAll($data);

					$old_data = Common::HashEmptyField($result, 'data');
					$activity = Common::HashEmptyField($result, 'Log.activity');
					$document_id = Common::HashEmptyField($result, 'Log.document_id');

					$this->RmCommon->_saveLog($activity, $old_data, $document_id);		

					printf ('%s <br>', $activity);
				}

				if(!empty($data_offset)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', $data_offset);
					$this->Setting->set('temp', $modified);
					$this->Setting->save();
				}
			} else {
				if(!empty($setting_id) && !empty($temp)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', 0);
					$this->Setting->set('value', $temp);
					$this->Setting->set('temp', '');
					$this->Setting->save();
				}
				echo __('Data tidak ditemukan');
			}
		} else {
			echo __('not get permission / no token');
		}
		die();
   	}

   	function bank_data () {
		$this->loadModel('Setting');
		App::import('Helper', 'Html');

        $this->Html = new HtmlHelper(new View(null));
		$url = Configure::read('__Site.kpr_url');
		$flag = FALSE;

		$slug = 'bank-api';
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => $slug,
			),
		));

		$domain = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
		$passkey = $this->RmCommon->filterEmptyField($setting, 'Setting', 'token');

		$lastupdated = $this->User->Kpr->KprBank->Bank->lastModifiedBank();
		$apiUrl = $domain.$this->Html->url(array(
			'controller' => 'Api',
			'action' => 'bank_synchronize',
			'?' => array_merge(array(
				'token' => $passkey,
				'slug' => $slug,
			), $lastupdated),
			'ext' => 'json',
			'admin' => false,
		));

		$apiUrl = htmlspecialchars_decode($apiUrl);
		$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);

		$status = $this->RmCommon->filterEmptyField($dataApi, 'data', 'status');
		$data = $this->RmCommon->filterEmptyField($dataApi, 'data', 'data');

		if(!empty($status) && !empty($data) ) {
			
			$bank = $this->RmCommon->filterEmptyField($data, 'Bank');
			if(!empty($bank)){
				$flag = $this->User->Kpr->KprBank->Bank->bank_sync($bank);
				if( $flag ) {
					echo __('Berhasil sync data Bank API <br>');
				} else {
					echo __('Gagal menyimpan data Bank API <br>');
				}
			}else{
				echo __('Data Bank tidak ditemukan <br>');
			}

			$bankBanner = $this->RmCommon->filterEmptyField($data, 'BankBanner');
			if(!empty($bankBanner)){
				$flag = $this->User->Kpr->KprBank->Bank->BankBanner->bank_sync($bankBanner);
				
				if( $flag ) {
					echo __('Berhasil sync data BankBanner API <br>');
				} else {
					echo __('Gagal menyimpan data BankBanner API <br>');
				}
			}else{
				echo __('Data BankBanner tidak ditemukan <br>');
			}

			$bankProduct = $this->RmCommon->filterEmptyField($data, 'BankProduct');
			if(!empty($bankProduct)){
				$flag = $this->User->Kpr->KprBank->Bank->BankProduct->bank_sync($bankProduct);
				if( $flag ) {
					echo __('Berhasil sync data BankPoduct API <br>');
				} else {
					echo __('Gagal menyimpan data BankPoduct API <br>');
				}
			}else{
				echo __('Data BankPoduct tidak ditemukan <br>');
			}

			$bankProductSpec = $this->RmCommon->filterEmptyField($data, 'BankProductSpec');
			if(!empty($bankProductSpec)){
				$flag = $this->User->Kpr->KprBank->Bank->BankProduct->BankProductSpec->bank_sync($bankProductSpec);
				if( $flag ) {
					echo __('Berhasil sync data BankPoductSpec API <br>');
				} else {
					echo __('Gagal menyimpan data BankPoductSpec API <br>');
				}
			}else{
				echo __('Data BankPoduct tidak ditemukan <br>');
			}

			$bankProductSpecCompany = $this->RmCommon->filterEmptyField($data, 'BankProductSpecCompany');
			if(!empty($bankProductSpecCompany)){
				$flag = $this->User->Kpr->KprBank->Bank->BankProduct->BankProductSpec->BankProductSpecCompany->bank_sync($bankProductSpecCompany);
				if( $flag ) {
					echo __('Berhasil sync data BankPoductSpecCompany API <br>');
				} else {
					echo __('Gagal menyimpan data BankPoductSpecCompany API <br>');
				}
			}else{
				echo __('Data BankPoduct tidak ditemukan <br>');
			}

			$bank_contact = $this->RmCommon->filterEmptyField($data, 'BankContact');
			if(!empty($bank_contact)){
				$flag = $this->User->Kpr->KprBank->Bank->BankContact->bank_sync($bank_contact);
				if( $flag ) {
					echo __('Berhasil sync data BankContact API <br>');
				} else {
					echo __('Gagal menyimpan data BankContact API <br>');
				}
			}else{
				echo __('Data BankContact tidak ditemukan <br>');
			}
			

			$bank_settings = $this->RmCommon->filterEmptyField($data, 'BankSetting');
			if(!empty($bank_settings)){
				$flag = $this->User->Kpr->KprBank->Bank->BankSetting->bank_sync($bank_settings);
				if( $flag ) {
					echo __('Berhasil sync data BankSetting API <br>');
				} else {
					echo __('Gagal menyimpan data BankSetting API <br>');
				}
			}else{
				echo __('Data BankSetting tidak ditemukan <br>');
			}
			
			$bank_com_settings = $this->RmCommon->filterEmptyField($data, 'BankCommissionSetting');
			if(!empty($bank_com_settings)){
				$flag = $this->User->Kpr->KprBank->Bank->BankCommissionSetting->bank_sync($bank_com_settings);
				if( $flag ) {
					echo __('Berhasil sync data BankCommissionSetting API <br>');
				} else {
					echo __('Gagal menyimpan data BankCommissionSetting API <br>');
				}
			}else{
				echo __('Data BankCommissionSetting tidak ditemukan <br>');
			}
			

			$bank_com_setting_loans = $this->RmCommon->filterEmptyField($data, 'BankCommissionSettingLoan');
			if(!empty($bank_com_setting_loans)){
				$flag = $this->User->Kpr->KprBank->Bank->BankCommissionSetting->BankCommissionSettingLoan->bank_sync($bank_com_setting_loans);
				if( $flag ) {
					echo __('Berhasil sync data BankCommissionSettingLoan API <br>');
				} else {
					echo __('Gagal menyimpan data BankCommissionSettingLoan API <br>');
				}
			}else{
				echo __('Data BankCommissionSettingLoan tidak ditemukan <br>');
			}
		} else {
			echo __('Data tidak tersedia');
		}
		die();
	}

   	function get_application_kpr_rumahku(){
		$this->loadModel('Setting');
		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));
		$url = Configure::read('__Site.site_default');
		$flag = FALSE;

		$slug = 'rumahku-kpr-api';
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => 'rumahku-kpr-api',
			),
		));

		if(!empty($setting)){
			$setting_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
			$domain = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
			$passkey = $this->RmCommon->filterEmptyField($setting, 'Setting', 'token');
			$value = $this->RmCommon->filterEmptyField($setting, 'Setting', 'value');
			$temp = $this->RmCommon->filterEmptyField($setting, 'Setting', 'temp');
			$offset = $this->RmCommon->filterEmptyField($setting, 'Setting', 'offset');

			$params = array(
				'passkey' => $passkey,
				'slug' => $slug,
				'offset' => $offset,
				'lastDate' => $value,
			);

			$apiUrl = $domain.$this->Html->url(array(
				'controller' => 'api_kprs',
				'action' => 'share_application_kprs',
				'?' => $params,
				'ext' => 'json',
				'admin' => false,
				// 'api' => true,
			));
			$apiUrl = htmlspecialchars_decode($apiUrl);
			$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
			$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data', 'data');
			$status = $this->RmCommon->filterEmptyField($dataApi, 'status');
			$data = $this->RmCommon->filterEmptyField($dataApi, 'KprBank');
			$msg = $this->RmCommon->filterEmptyField($dataApi, 'msg');
			$data_offset = $this->RmCommon->filterEmptyField($dataApi, 'offset');

			if(!empty($data)){
				$data = $this->RmProperty->PropertyExist($data, 'KprBank');
				$data = $this->RmUser->UserExisting($data);
				$data = $this->RmKpr->beforeSaveShareKprs($data, $setting);
				$modified = $this->RmCommon->filterEmptyField($data, 'modified');
				$result = $this->User->Kpr->doSaveAllShareKprs($data);
				$this->RmCommon->setProcessParams($result,false,array(
					'noRedirect' => true
				));

				$msg_arr = $this->RmCommon->filterEmptyField($result, 'msg_arr');
				if(!empty($msg_arr)){
					foreach ($msg_arr as $key => $msg) {
						echo $msg.'<br>';
					}
				}

				if(!empty($data_offset)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', $data_offset);
					$this->Setting->set('temp', $modified);
					$this->Setting->save();
				}
			}else{
				if(!empty($setting_id) && !empty($temp)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', 0);
					$this->Setting->set('value', $temp);
					$this->Setting->set('temp', '');
					$this->Setting->save();
				}
				echo __('Data tidak tersedia');
			}
		}else{
			echo __('not get permission / no token');
		}
		die();	
	}

   	function get_commission_kpr(){
   		$this->KprBankCommission = ClassRegistry::init('KprBankCommission');
   		$this->loadModel('Setting');
		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));

		$slug = 'process-commission-api';
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => $slug,
			),
		));

		if(!empty($setting)){
			$setting_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
			$domain = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
			$passkey = $this->RmCommon->filterEmptyField($setting, 'Setting', 'token');
			$value = $this->RmCommon->filterEmptyField($setting, 'Setting', 'value');
			$temp = $this->RmCommon->filterEmptyField($setting, 'Setting', 'temp');
			$offset = $this->RmCommon->filterEmptyField($setting, 'Setting', 'offset');

			$params = array(
				'token' => $passkey,
				'slug' => $slug,
				'offset' => $offset,
				'lastDate' => $value,
			);

			$apiUrl = $domain.$this->Html->url(array(
				'controller' => 'api_kprs',
				'action' => 'call_commission_kprs',
				'?' => $params,
				'ext' => 'json',
				'admin' => false,
				// 'api' => true,
			));

			$apiUrl = htmlspecialchars_decode($apiUrl);
			$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
			$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data', 'data');
			$status = $this->RmCommon->filterEmptyField($dataApi, 'status');
			$datas = $this->RmCommon->filterEmptyField($dataApi, 'KprBank');
			$msg = $this->RmCommon->filterEmptyField($dataApi, 'msg');
			$data_offset = $this->RmCommon->filterEmptyField($dataApi, 'offset');

			if(!empty($status) && !empty($datas)){
				foreach($datas AS $key => $data){
					$modified = $this->RmCommon->filterEmptyField($data, 'KprBank', 'modified');
					$kpr_bank_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'prime_kpr_bank_id');
					$dataSet['KprBank'] = $this->RmCommon->filterEmptyField($data, 'KprBank');
					$dataSet['KprBankInstallment'] = $this->RmCommon->filterEmptyField($data, 'KprBankInstallment');
					$kprBankCommissions = $this->RmCommon->filterEmptyField($data, 'KprBankCommission');

					$dataSet = $this->User->Kpr->KprBank->KprBankTransfer->getMerge($dataSet, $kpr_bank_id, array(
						'fieldName' => 'kpr_bank_id',
					));
					
					if(!empty($kprBankCommissions)){
						foreach($kprBankCommissions AS $loop => $kprBankCommission){
							$paid_status = $this->RmCommon->filterEmptyField($kprBankCommission, 'KprBankCommission', 'paid_status');

							if($paid_status == 'approved'){
								$dataSet = array_merge($dataSet, $kprBankCommission);
								$result = $this->KprBankCommission->api_paid_commission($dataSet);
								$msg = $this->RmCommon->filterEmptyField($result, 'msg');
								$status = $this->RmCommon->filterEmptyField($result, 'status');

								if($status == 'success'){
									$prime_kpr_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'prime_kpr_id');
									$prime_kpr_bank_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'prime_kpr_bank_id');

									$value = $this->User->Kpr->getData('first', array(
										'conditions' => array(
											'Kpr.id' => $prime_kpr_id
										),
									));

									$agent_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'agent_id');
									$bank_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'bank_id');
									$value = $this->User->Kpr->KprBank->Bank->getMerge($value, $bank_id);
									$value = $this->User->Kpr->KprBank->getMerge($value, $prime_kpr_bank_id, 'Kpr', 'first', array(), 'KprBank.id');
									$value = $this->User->Kpr->KprBank->KprBankTransfer->getMerge($value, $prime_kpr_bank_id, array(
										'fieldName' => 'kpr_bank_id'
									));
									$value = $this->User->getMerge($value, $agent_id, TRUE, 'Agent');
									$this->RmKpr->sendEmailCommission($dataSet, $value);

								}

								if($status <> 'done'){
									printf ('%s <br>', $msg);
								}

							}
						}
					}
				}

				if(!empty($data_offset)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', $data_offset);
					$this->Setting->set('temp', $modified);
					$this->Setting->save();
				}
			}else{

				if(!empty($setting_id) && !empty($temp)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', 0);
					$this->Setting->set('value', $temp);
					$this->Setting->set('temp', '');
					$this->Setting->save();
				}
				echo __('Data tidak ditemukan');
			}

		}else{
			echo __('not get permission / no token');
		}
		die();
   	}

   	function get_action_kpr(){
		$this->loadModel('Setting');
		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));

		$slug = 'process-kpr-api';
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => $slug,
			),
		));

		if(!empty($setting)){
			$setting_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
			$domain = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
			$passkey = $this->RmCommon->filterEmptyField($setting, 'Setting', 'token');
			$value = $this->RmCommon->filterEmptyField($setting, 'Setting', 'value');
			$temp = $this->RmCommon->filterEmptyField($setting, 'Setting', 'temp');
			$offset = $this->RmCommon->filterEmptyField($setting, 'Setting', 'offset');

			$params = array(
				'token' => $passkey,
				'slug' => $slug,
				'offset' => $offset,
				'lastDate' => $value,
			);

			$apiUrl = $domain.$this->Html->url(array(
				'controller' => 'api_kprs',
				'action' => 'call_prime_kprs',
				'?' => $params,
				'ext' => 'json',
				'admin' => false,
				// 'api' => true,
			));

			$apiUrl = htmlspecialchars_decode($apiUrl);
			$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
			$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data', 'data');
			$status = $this->RmCommon->filterEmptyField($dataApi, 'status');
			$datas = $this->RmCommon->filterEmptyField($dataApi, 'KprBank');
			$msg = $this->RmCommon->filterEmptyField($dataApi, 'msg');
			$data_offset = $this->RmCommon->filterEmptyField($dataApi, 'offset');

			if(!empty($status) && !empty($datas)){
				foreach($datas AS $key => $data){
					$modified = Common::hashEmptyField($data, 'KprBank.modified');
					$document_status = Common::hashEmptyField($data, 'KprBank.document_status');
					
					switch ($document_status) {
						case 'approved_credit':
							$result = $this->RmKpr->api_action_credit_agreement($data);
							break;
						default:
							$result = $this->RmKpr->api_action_kpr($data);
							break;
					}
					$msg = $this->RmCommon->filterEmptyField($result, 'msg');
					printf ('%s <br>', $msg);
				}

				if(!empty($data_offset)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', $data_offset);
					$this->Setting->set('temp', $modified);
					$this->Setting->save();
				}
			}else{
				if(!empty($setting_id) && !empty($temp)){
					$this->Setting->id = $setting_id;
					$this->Setting->set('offset', 0);
					$this->Setting->set('value', $temp);
					$this->Setting->set('temp', '');
					$this->Setting->save();
				}
				echo __('Data tidak ditemukan');
			}
		}else{
			echo __('not get permission / no token');
		}
		die();
   	}

   	function call_applicaton_kprs(){
   		$params = $this->params->query;
		$this->RmCommon->_callCheckAPI($params);	

		$params = $this->request->data;
		$status = $this->RmCommon->filterEmptyField($params, 'status');

		$lastDate = $this->RmCommon->filterEmptyField($params, 'lastDate', false, false, array(
			'type' => 'trailing_slash',
		));

		$offset = $this->RmCommon->filterEmptyField($params, 'offset', false, 0);
		$limit = 10;

		$options = array(
			'conditions' => array(
				'KprBank.is_generate' => false,
				'KprBank.application_status' => 'completed',
				'OR' => array(
					array(
						'KprBank.document_status' => array('process'),
					),
					array(
						'KprBank.document_status' => 'proposal_without_comiission',
						'KprBank.application_snyc' => FALSE,
					),
				),
			),
			'order' => array('KprBank.application_date' => 'ASC'),
			'limit' => $limit,
		);

		if( $lastDate ){
			$options['conditions']['KprBank.application_date >'] = $lastDate;
		}

		if( $offset ) {
			$options['offset'] = $offset;
		}

		$values = $this->User->Kpr->KprBank->getData('all', $options);
		if(!empty($values)){
			foreach($values AS $key => $value){
				$kpr_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'kpr_id', 0);

				$value = $this->User->Kpr->getMerge($value, $kpr_id, 'Kpr.id', array(
					'elements' => array(
						'company' => false,
					),
				));
				
				if(!empty($value['Kpr'])){
					$property_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_id', 0);
					$agent_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'agent_id');
					$client_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id', 0);

					$value = $this->User->Kpr->KprApplication->getMerge($value, $kpr_id, array(
						'find' => 'all',
						'fieldName' => 'KprApplication.kpr_id',
					));

					// $value = $this->User->Kpr->KprApplication->mergeApplication( $value, $kpr_id );

					$value['documentCategories'] = $this->RmKpr->getDocumentSort( array(
						'DocumentCategory.is_required' => 1,
							'DocumentCategory.id <>' => array( 3, 7, 19, 20),
						), array(
							'id' => $kpr_id,
							'owner_id' => $client_id,
							'property_id' => $property_id,
							'document_type' => 'kpr_application',
					), $value);

					$value['documentCategoriesSpouse'] = $this->RmKpr->getDocumentSort( array(
							'DocumentCategory.is_required' => 1,
							'DocumentCategory.id <>' => array( 1, 2, 3, 4, 5, 6, 7, 15, 16, 17 ),
						), array(
							'id' => $kpr_id,
							'owner_id' => $client_id,
							'document_type' => 'kpr_spouse_particular',
					), $value);	

					$value = $this->User->getMerge($value, $agent_id, false, 'Agent');
					$value = $this->User->Kpr->Property->getMerge($value, $property_id);

					$parent_id = $this->RmCommon->filterEmptyField($value, 'Agent', 'parent_id');
					$value = $this->User->getMerge($value, $parent_id, false, 'Principle');
					$value = $this->User->UserCompany->getMerge($value, $parent_id);

					$kpr_application_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'id');

					$owner_id = $this->RmCommon->filterEmptyField($value, 'Property', 'client_id');

					$value = $this->User->UserClient->getMerge($value, $owner_id, false, 'Owner');
					$value = $this->User->UserProfile->getMerge($value, $owner_id, true, 'OwnerProfile');
				}
				
				$values[$key] = $value;
			}

			if( isset($offset) ) {
				$params['offset'] = $offset+$limit;
			}
		}

		if($status){
			$params['KprBank'] = $values;
			$params['status'] = $status;	
		}
		$this->RmCommon->_callDataForAPI($params, 'manual');
   	}

	function call_bank_kprs(){
		$params = $this->params->query;
		$this->RmCommon->_callCheckAPI($params);

		$params = $this->request->data;
		$status = $this->RmCommon->filterEmptyField($params, 'status');
		$option_conditions = $this->RmCommon->filterEmptyField($params, 'conditions');

		$lastDate = $this->RmCommon->filterEmptyField($params, 'lastDate', false, false, array(
			'type' => 'trailing_slash',
		));

		$offset = $this->RmCommon->filterEmptyField($params, 'offset', false, 0);
		$limit = 20;

		$options = array(
			'conditions' => array(
				'KprBank.is_hold' => false,
				'KprBank.is_generate' => false,
				'OR' => array(
					array(
						'OR' => array(
							array(
								'KprBank.document_status' => 'process',
								'OR' => array(
									array(
										'KprBank.from_kpr' => 'backend',
									),
									array(
										'KprBank.from_kpr' => 'frontend',
										'KprBank.forward_app' => true,
									),
								),
							),
							array(
								'OR' => array(
									array(
										'KprBank.code <>' => false,
									),
									array(
										'KprBank.code <>' => NULL,
									),
								),
								'KprBank.document_status' => array(
									'completed',
									'reschedule_pk', 
									'approved_credit',
									'credit_process', 
									'process_appraisal', 
									'process',
									'cancel', 
								),
							),
						),
					),
					array(
						'KprBank.document_status' => 'proposal_without_comiission',
						'KprBank.application_snyc' => TRUE,
					),
				),
			),
			'order' => array('KprBank.modified' => 'ASC'),
			'limit' => $limit,
		);

		if(!empty($option_conditions)){
			$options['conditions'] = array_merge($options['conditions'], $option_conditions);
		}else{
			if( $lastDate ){
				$options['conditions']['KprBank.modified >'] = $lastDate;
			}
		}

		if( $offset ) {
			$options['offset'] = $offset;
		}

		$values = $this->User->Kpr->KprBank->getData('all', $options);

		if(!empty($values)){
			foreach($values As $key => &$value){
				$value = $this->User->Kpr->KprBank->getMergeList($value, array(
					'contain' => array(
						'BankSetting' => array(
							'elements' => array(
								'type' => 'all',
							),
						),
						'KprBankInstallment' => array(
							'order' => array(
								'KprBankInstallment.created' => 'ASC'
							),
							'elements' => array(
								'status' => 'all',
							),
							'contain' => array(
								'KprBankCommission',
							),
						),
						'KprBankDate',
						'Kpr' => array(
							'contain' => array(
								'KprApplication',
							),
							'elements' => array(
								'company' => false,
							),
						),
						'Bank',
					),
				));

				$value = $this->User->Kpr->getMergeList($value, array(
					'contain' => array(
						'User' => array('Kpr', 'agent_id', true, 'Agent'),
					)
				));
				$value = $this->User->Kpr->_callMergeProperty($value);			

				$booking_code = Common::hashEmptyField($value, 'Kpr.booking_code');
				$kpr_id = $id = Common::hashEmptyField($value, 'Kpr.id');
				$code = Common::hashEmptyField($value, 'Kpr.code');
				$agent_id = Common::hashEmptyField($value, 'Kpr.agent_id');
				$client_id = Common::hashEmptyField($value, 'Kpr.user_id', 0);
				$property_id = Common::hashEmptyField($value, 'Kpr.property_id', 0);
				$principle_id = Common::hashEmptyField($value, 'Kpr.company_id');

				$kpr_bank_id = Common::hashEmptyField($value, 'KprBank.id');
				$sales_id = Common::hashEmptyField($value, 'KprBank.sales_id');
				$document_status = Common::hashEmptyField($value, 'KprBank.document_status');

				$owner_id = Common::hashEmptyField($value, 'Property.client_id', 0);
				$parent_id = Common::hashEmptyField($value, 'Agent.parent_id');

				$value = $this->User->getMerge($value, $parent_id, false, 'Principle');
				$value = $this->User->UserProfile->getMerge($value, $parent_id, false, 'PrincipleProfile');
				$value = $this->User->UserCompany->getMerge($value, $parent_id);
				$value = $this->User->UserCompanyConfig->getMerge($value, $parent_id);
				$value = $this->User->UserProfile->getMerge($value, $agent_id);
				$value = $this->User->UserClient->getMerge($value, $owner_id, $parent_id, 'UserOwner');
				$value = $this->User->UserClient->getMerge($value, $client_id, false, 'Client');

				$value = $this->User->Kpr->BankUser->getMerge($value, $sales_id, array(
					'fieldName' => 'id',
				));

				$booking = $this->RmKpr->_callBooking($booking_code, $principle_id);
				$value = array_merge($value, $booking);

				if($document_status == 'credit_process'){
					$value = $this->User->Kpr->KprBank->KprBankTransfer->getMerge($value, $kpr_bank_id, array(
						'fieldName' => 'kpr_bank_id'
					));
				}
				$value['documentCategories'] = $this->RmKpr->getDocumentSort( array(
						'DocumentCategory.is_required' => 1,
						'DocumentCategory.id <>' => array( 3, 7, 19, 20),
					), array(
						'id' => $kpr_id,
						'owner_id' => $client_id,
						'property_id' => $property_id,
						'document_type' => 'kpr_application',
				), $value);

				$value['documentCategoriesSpouse'] = $this->RmKpr->getDocumentSort( array(
						'DocumentCategory.is_required' => 1,
						'DocumentCategory.id <>' => array( 1, 2, 3, 4, 5, 6, 7, 15, 16, 17 ),
					), array(
						'id' => $kpr_id,
						'owner_id' => $client_id,
						'document_type' => 'kpr_spouse_particular',
				), $value);

				$paymentAppraisals = $this->User->CrmProject->CrmProjectDocument->getData('all', array(
		        	'conditions' => array(
						'CrmProjectDocument.owner_id' => $kpr_bank_id,
						'CrmProjectDocument.document_type' => 'payment_appraisal',
						'CrmProjectDocument.session_id NOT' => NULL,
					),
				));
				$value['PaymentAppraisal'] = $paymentAppraisals;
				
			}

	    	if( isset($offset) ) {
				$params['offset'] = $offset+$limit;
			}
		}
		
		if($status){
			$params['KprBank'] = $values;
			$params['status'] = $status;	
		}
		$this->RmCommon->_callDataForAPI($params, 'manual');
	}

	function get_queue_mailblast(){
		$this->loadModel('Setting');
		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));

		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => 'queue-mailblast',
			),
		));

		if(!empty($setting)) {
			$setting_id = Common::hashEmptyField($setting, 'Setting.id');
			$domain     = Common::hashEmptyField($setting, 'Setting.link');
			$passkey    = Common::hashEmptyField($setting, 'Setting.token');
			$slug       = Common::hashEmptyField($setting, 'Setting.slug');
			$temp       = Common::hashEmptyField($setting, 'Setting.temp');

			$params = array(
				'token' => $passkey,
				'slug' => $slug,
			);

			$apiUrl = $domain.$this->Html->url(array(
				'controller' => 'Api',
				'action' => 'call_queue_mailblast',
				'?' => $params,
				'ext' => 'json',
				'admin' => false,
			));

			$apiUrl  = htmlspecialchars_decode($apiUrl);
			$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);

			$datas   = $this->RmCommon->filterEmptyField($dataApi, 'data', 'data');

			if(!empty($datas)) {
				$tmpData = false;

				// format the data and blast to user
				foreach($datas AS $key => $data){
					$id_campaign = Common::hashEmptyField($data, 'BankProductCampaign.id');
					$subject     = Common::hashEmptyField($data, 'BankProductCampaign.subject_campaign');
					$modified    = Common::hashEmptyField($data, 'BankProductCampaign.modified');
					
					$result = $this->RmKpr->save_queue_mailblast($data);

					$status = Common::hashEmptyField($result, 'status');
					$msg    = Common::hashEmptyField($result, 'msg');

					if ($status == 'success') {
						$tmpData[] = array(
							'id' => $id_campaign,
							'subject' => $subject,
						);
					}

					printf ('%s <br>', $msg);
				}

				// after blasting the queue, then update mailblast in primekpr and set as sended
				// data parsing: id mailblast and subject
				if (!empty($tmpData)) {
					$data_to_update['DataToUpdate'] = $tmpData;
					$send_data = array_merge($data_to_update, $setting);

					$updated  = $this->RmKpr->update_queue_mailblast($send_data);
					$msg_resp = Common::hashEmptyField($updated, 'msg');
					printf ('<br> %s <br>', $msg_resp);
				}

				if($status == 'success'){
					$this->Setting->id = $setting_id;
					$this->Setting->set('temp', $modified);
					$this->Setting->save();
				}
			} else {
				echo __('Tidak ada antrian(queue). Data tidak ditemukan.');
			}
		} else {
			echo __('not get permission / no token');
		}

		die();
   	}

}

?>