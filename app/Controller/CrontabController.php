<?php
App::uses('AppController', 'Controller');

class CrontabController extends AppController {
	public $components = array(
		'RmProperty', 'RumahkuApi', 'RmUser',
		'PhpExcel.PhpExcel', 'RmMigrate',
		'RmRecycleBin', 'RmKpr', 'RmImage',
		'RmAdvice', 'RmMigrateCompany',
		'RmReport', 'RmNewsletter',
		'RmActivity',
	);
	var $uses = array(
		'Queue', 'Setting'
	);
	
	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array(
			'converter_price', 'generate_facility_and_pointplus',
			'generate_property_revision', 'send_campaign', 'birthday',
			'generate_ebrosur', 'inactive_listings', 'notification_inactive_listings',
			'notification_expired_rent_property', 'generate_user_profiles',
			'generate_clients', 'generate_property_clients',
			'daily', 'weekly', 'monthly', 'minutely', 'generate_property', 
			'filter_expired_document', 'check_payment_status', 
			'renewal_notification', 'send_email_error_launcher',
			'add_kpr_banks', 'restore_photo',
			'migrateAgent', 'migrateProperty', 'direct_update_property',
			'migrateClientOwner', 'sync_application', 
			'kpr_cancel', 'kpr_credit_process', 'sync_kpr_log_calculate',
			'change_password', 'duplicate_property_user',
			'forward_application_to_bank', 'admin_kpr_migration',
			'migrate_data', 'report_execute', 'regenerate_photo',
			'generate_multiple_mls', 'auto_sent_kpr',
			'daily_accumulation_property', 'accumulate_report',
			'accumulate_user_report',
			'generate_integration_json', 
			'upload_integration_json', 
			'generate_agent_rank', 'activity_category_generator',
			'point_category_generator', 'push_generator',
			'push_rank', 'activity_listing_generator', 'activity_ebrosur_generator',
			'push_category_generator'
		));

		$this->autoLayout = false;
		$this->autoRender = false;
	}

	function auto_sent_kpr($limit = 20){
		$this->loadModel('KprBank');

		// $modified = "2016-11-27 00:00:00";

		$sent_day_apps = $this->RmCommon->filterEmptyField($this->global_variable, 'sent_app_day');

		if($sent_day_apps){
			foreach($sent_day_apps AS $day => $title){
				$cnt = 0;
				// $list = $this->User->UserCompanyConfig->getData('list', array(
				// 	'conditions' => array(
				// 		'UserCompanyConfig.is_sent_app' => true,
				// 		'UserCompanyConfig.sent_app_day' => $day,
				// 	),
				// 	'fields' => array('user_id', 'user_id'),
				// ));

				$this->KprBank->virtualFields['cnt_expired'] = 'DATEDIFF(NOW(), KprBank.created)';

				$values = $this->KprBank->getData('all', array(
					'conditions' => array(
						'KprBank.from_kpr' => 'frontend',
						'KprBank.forward_app' => false,
						'KprBank.document_status' => 'process',
						'KprBank.code' => NULL,
						'KprBank.cnt_expired >' => $day,
						// 'Kpr.company_id' => $list,
						// 'KprBank.modified >= ' => $modified,
					),
					'contain' => array(
						'Kpr',
					),
					'limit' => $limit,
				));
				
				if(!empty($values)){
					foreach($values AS $key => $value){
						$id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'id');

						$this->KprBank->id = $id;
						$this->KprBank->set('forward_app', TRUE);

						if($this->KprBank->save()){
							$cnt += 1;
						}
					}
					$msg = __(' auto kirim ke bank dengan banyak data %s, untuk diatas %s hari <br>', $cnt, $day);

					if($cnt){
						$msg = sprintf('Berhasil %s', $msg);
					}else{
						$msg = sprintf('Gagal %s', $msg);
					}
					echo $msg;
				}else{
					echo __('Data tidak ditemukan untuk diatas %s hari <br>', $day);
				}

			}
		}
		die();
	}

	// function admin_kpr_migration($limit = 15){
	// 	$values = $this->User->Kpr->getData('all', array(
	// 		'conditions' => array(
	// 			'Kpr.snyc_migration' => FALSE,
	// 		),
	// 		'limit' => $limit,
	// 	), array(
	// 		'company' => false,
	// 		'status' => 'all',
	// 	));

	// 	if(!empty($values)){
	// 		foreach($values AS $key => $value){
	// 			$dataKprBanks = array();
	// 			$kpr = $this->RmCommon->filterEmptyField($value, 'Kpr');
	// 			$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
	// 			$value = $this->User->Kpr->KprSpouseParticular->getMerge($value, $kpr_id);
	// 			$value = $this->User->Kpr->KprBank->getMergeAll($value, $kpr_id, array(
	// 				'foreignKey' => 'KprBank.kpr_application_id',
	// 			));
	// 			$kpr_banks = $this->RmCommon->filterEmptyField($value, 'KprBank');

	// 			if(!empty($kpr_banks)){
	// 				foreach($kpr_banks AS $key => $kpr_bank){
	// 					$kpr_application_request_id = $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'id');
	// 					$bank_id = $this->RmCommon->filterEmptyField($kpr_bank, 'KprBank', 'bank_id');
	// 					$kpr_bank = $this->User->Kpr->KprBank->KprRequestConfirm->getMerge($kpr_bank, $kpr_application_request_id, 'KprRequestConfirm.kpr_application_request_id');
	// 					$kpr_bank = $this->User->Kpr->KprBank->KprCommissionPayment->getMerge($kpr_bank, $kpr_application_request_id);
	// 					$kpr_application_confirm_id = $this->RmCommon->filterEmptyField($kpr_bank, 'KprRequestConfirm', 'id');

	// 					if(empty($kpr_application_confirm_id)){
	// 						$kpr_bank = $this->User->Kpr->KprBank->KprRequestConfirm->KprCommissionPaymentConfirm->getMerge($kpr_bank, $kpr_application_confirm_id);
	// 					}

	// 					$kpr_bank = $this->User->Kpr->KprBank->Bank->getMerge($kpr_bank, $bank_id);
	// 					$kpr_bank = $this->User->Kpr->KprBank->Bank->BankSetting->getMerge($kpr_bank, $bank_id);

	// 					$dataKprBanks[$key] = $this->RmKpr->generateBeforeSave($kpr_bank, $kpr);
	// 				}
	// 			}
				
	// 			$dataSave = $this->RmKpr->G_kpr_application($dataKprBanks, $value, 'primesystem');
	// 			$dataSave = $this->RmCommon->dataConverter($dataSave, array(
	// 				'unset' => array(
	// 					'KprBank' => array(
	// 						0 => array(
	// 							'KprBankInstallment' => array(
	// 								'unpaid_agent',
	// 								'unpaid_rumahku'
	// 							),
	// 						),
	// 					),
	// 				),
	// 			));
	// 			$id = $this->User->Kpr->dosaveAllMigration($dataSave);
	// 			$this->User->Kpr->updateSnycMigration($id);
	// 		}
	// 	}else{
	// 		echo __('Data tidak ditemukan ');
	// 	}
	// 	die();
	// }

	function sync_kpr_log_calculate(){
		$url = Configure::read('__Site.kpr_url');
		$this->autoLayout = false;
		$this->autoRender = false;
		$result = array();

		$values = $this->User->Kpr->KprBank->getData('all', array(
			'conditions' => array(
				'KprBank.type' => 'logkpr',
				'KprBank.type_log' => 'app-calculate',
				'KprBank.snyc' => FALSE
			),
			'limit' => Configure::read('__Site.config_limit_crontab')
		), array(
			'company' => false,
		));

		if(!empty($values)){
			foreach($values AS $key => $value){
				$kpr_bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'id');
				$kpr_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'kpr_id');
				$value = $this->User->Kpr->getMerge($value, $kpr_id, 'Kpr.id', array(
					'elements' => array(
						'company' => false,
					)
				));

				$value = $this->User->Kpr->KprBank->KprBankInstallment->getMerge($value, $kpr_bank_id, array(
					'fieldName' => 'kpr_bank_id',
				));

				$value = $this->RmCommon->_callUnset(array(
					'KprBank' => array(
						'created',
						'modified',
					),
					'KprBankInstallment' => array(
						'id',
						'kpr_bank_id',
						'created',
						'modified',
					),
				), $value);

				$url = sprintf('%s/kpr/api_kpr_log_calculate.json', $url);
				$value['utm'] = $this->_base_url;
				$data_api = $this->RumahkuApi->api_access($value, 'api_kpr_log_calculate', $url, 'kpr');
				// debug($data_api);die();
				if(!empty($data_api)){
					$data_api = json_decode($data_api, true);
					$data_api = $this->RmCommon->filterEmptyField($data_api, 'data');

					$status = $this->RmCommon->filterEmptyField($data_api, 'result', 'status');
					$msg = $this->RmCommon->filterEmptyField($data_api, 'result', 'msg');

					if($status == 'success'){
						$result = $this->User->Kpr->KprBank->doUpdateLog($kpr_bank_id);
						$msg = $this->RmCommon->filterEmptyField($result, 'msg');
						$this->RmCommon->setProcessParams($result, false, array(
							'noRedirect' => true
						));
						echo $msg.'<br>';
					}else{
						$log_msg = __('[data API Rusak]');
                    	$this->RmCommon->_saveLog($log_msg, $value, $kpr_bank_id, true, 306);
						echo $msg.'<br>';
					}

				}else{
					$msg = __('API tidak bisa di akses <br>');
					$log_msg = sprintf(__('[KPR-APPLICATION] %s'), $msg);
                    $this->RmCommon->_saveLog($log_msg, $value, $kpr_bank_id, true, 304);

                    echo $msg.'<br>';
				}
			}
		}else{
			$msg = __('Data tidak ditemukan');
			$log_msg = __('[KPR-APPLICATION]');
        	$this->RmCommon->_saveLog($log_msg, array(), array(), true, 301);

			echo $msg.'<br>';
		}
	}

	// Kirim Referral / Request Provisi KPR Ke Bank
	function add_kpr_banks(){
		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'process-kpr-api'
			),
		));

		if(!empty($setting)){
			$url = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
			$this->autoLayout = false;
			$this->autoRender = false;
			$result = array();

			$kpr_list = $this->User->Kpr->KprBank->getData('list', array(
				'conditions' => array(
					'KprBank.from_kpr' => array('backend', 'approved', 'frontend'),
				),
				'group' => 'KprBank.kpr_id',
				'fields' => array('kpr_id', 'kpr_id'),
			), array(
				'document_status' => 'pending',
			));

			$values = $this->User->Kpr->getData( 'all', array(
				'conditions' => array(
					'Kpr.id' => $kpr_list,
				),
				'limit' => 3,
			), array(
				'company' => false,
				'status_sync' => 'sync-pending',
			));

			$values = $this->User->Kpr->getMergeList($values, array(
				'contain' => array(
					'User' => array('Kpr', 'agent_id', true, 'Agent'),
					'Property' => array('Kpr','property_id'),
				)
			));
			$values = $this->User->Kpr->Property->getDataList($values,array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertyMedias'
				)
			));
			if(!empty($values)){
				foreach($values As $key => $value){
					$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
					$id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
					$code = $this->RmCommon->filterEmptyField($value, 'Kpr', 'code');
					$agent_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'agent_id');
					$agent_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'agent_id');
					$client_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id');
					$owner_id = $this->RmCommon->filterEmptyField($value, 'Property', 'client_id');
					$parent_id = $this->RmCommon->filterEmptyField($value, 'Agent', 'parent_id');
					$value = $this->User->getMerge($value, $parent_id, false, 'Principle');
					$value = $this->User->UserProfile->getMerge($value, $parent_id, false, 'PrincipleProfile');
					$value = $this->User->UserCompany->getMerge($value, $parent_id);
					$value = $this->User->UserProfile->getMerge($value, $agent_id);
					$value = $this->User->UserClient->getMerge($value, $owner_id, $parent_id, 'UserOwner');
					$value = $this->User->UserClient->getMerge($value, $client_id, false, 'Client');

					$kpr_banks = $this->User->Kpr->KprBank->getMergeAll($value['Kpr'], $kpr_id, array(
						'with_contain' => TRUE,
						// 'limit' => array(
						// 	'limit' => 3,
						// ),
						'condition_options' => array(
							'KprBank.from_kpr' => array('backend', 'approved', 'frontend'),
						),
						'elements' => array(
							'document_status' => 'pending',
						),
					));

					if(!empty($kpr_banks['KprBank'])){
						foreach($kpr_banks['KprBank'] As $key => $kprBank){
							$kprBank = array_merge($value, $kprBank);
							$application_status = $this->RmCommon->filterEmptyField($kprBank, 'KprBank', 'application_status');

							if($application_status == 'completed'){
								$kprBank = $this->User->Kpr->KprApplication->mergeApplication( $kprBank, $kpr_id );
								$kprBank['documentCategories'] = $this->RmKpr->getDocumentSort( array(
										'DocumentCategory.is_required' => 1,
										'DocumentCategory.id <>' => array( 3, 7, 19, 20),
									), array(
										'id' => $kpr_id,
										'owner_id' => !empty($client_id)?$client_id:0,
										'document_type' => 'kpr_application',
								), $kprBank);
							}

							$url = sprintf('%s/kpr/api_kpr_banks.json', $url);
							$kprBank['utm'] = $this->_base_url;
							$data_api = $this->RumahkuApi->api_access($kprBank, 'api_kpr_banks', $url, 'kpr');

							if(!empty($data_api)){
								$data_api = json_decode($data_api, true);
								$data_api = $this->RmCommon->filterEmptyField($data_api, 'data');
								$status = $this->RmCommon->filterEmptyField($data_api, 'result', 'status');
								$msg = $this->RmCommon->filterEmptyField($data_api, 'result', 'msg');

								if($status == 'success'){
									$data =  $this->RmCommon->filterEmptyField($data_api, 'result', 'data');
									if(!empty($data)){

										$kpr_bank_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'prime_kpr_bank_id');
										$code = $this->RmCommon->filterEmptyField($data, 'KprBank', 'code');
										$result_data = $this->User->Kpr->KprBank->doUpdateSnyc($data, 'snyc', $application_status);
										$this->RmCommon->setProcessParams($result_data, false, array(
											'noRedirect' => true
										));
										printf(__('Berhasil kirim pengajuan Kpr #%s, %s<br>'), $kpr_bank_id, $code);
									}
								}else{
									$log_msg = __('[data API Rusak]');
			                    	$this->RmCommon->_saveLog($log_msg, $value, $id, true, 306);

									echo $msg.'<br>';
								}
							}else{
								$msg = __('API tidak bisa di akses<br>');
								$log_msg = sprintf(__('[KPR-APPLICATION] %s'), $msg);
			                    $this->RmCommon->_saveLog($log_msg, $value, $id, true, 304);
			                    echo $msg.'<br>';
							}
						}
					}else{
						$msg = __('Pengajuan KPR tidak ditemukan');
						$log_msg = __('[KPR-APPLICATION]');
			        	$this->RmCommon->_saveLog($log_msg, array(), array(), true, 301);
						echo $msg.'<br>';
					}
				}
			}else{
				$msg = __('Data tidak ditemukan');
				$log_msg = __('[KPR-APPLICATION]');
	        	$this->RmCommon->_saveLog($log_msg, array(), array(), true, 301);
				echo $msg.'<br>';
			}
		}else{
			echo __('not get permission / no token');
		}

		die();
	}

	function converter_price () {
		$values = $this->User->Property->getData('all', array(
			'conditions' => array(
				'Property.price_measure' => 0,
				'Property.price <>' => 0,
			),
			'limit' => 50,
		), array(
			'status' => 'all',
			'company' => false,
		));
		
		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$value = $this->User->Property->getDataList($value, array(
					'contain' => array(
						'MergeDefault',
						'PropertyAsset',
					),
				));

				$id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
                $price_measure = $this->RmProperty->getMeasurePrice($value);

				$this->User->Property->id = $id;
				$this->User->Property->set('price_measure', $price_measure);
				$this->User->Property->save();
			}
			echo __('Telah berhasil melakukan konversi harga properti');
		} else {
			echo __('Tidak ada properti utk dilakukan konversi harga');
		}

		die();
	}

	function insertNewsletter ( $name, $period = false ) {
		if( !empty($name) ) {
			if( is_array($name) ) {
				foreach ($name as $key => $val_name) {
					$data[] = array(
						'name' => $val_name,
						'periode' => $period,
					);
				}

				$name = implode(', ', $name);
			} else {
				$data[] = array(
					'name' => $name,
					'periode' => $period,
				);
			}

			if($this->Queue->Newsletter->saveMany($data)){
				echo ('Berhasil menyimpan cronjob '.$name)."<br><br>";
			} else {
				echo ('Gagal menyimpan cronjob '.$name)."<br><br>";
			}
		} else {
			echo ('Gagal menyimpan cronjob')."<br><br>";
		}
	}

	function newsletterComplete ( $id, $status = 1, $sent = 1 ) {
		$this->Queue->Newsletter->set('status', $status);
		$this->Queue->Newsletter->set('sent', $sent);
		$this->Queue->Newsletter->id = $id;
		if($this->Queue->Newsletter->save()){
			$this->Queue->deleteAll(array(
				'Queue.newsletter_id' => $id,
			));
			return true;
		} else {
			return false;
		}
	}

	function insertQueue ( $document_id, $document_type, $newsletter_id, $sent = 1 ) {
		$this->Queue->create();
		$this->Queue->set('document_id', $document_id);
		$this->Queue->set('document_type', $document_type);
		$this->Queue->set('newsletter_id', $newsletter_id);
		$this->Queue->set('sent', $sent);

		if($this->Queue->save()){
			return true;
		} else {
			return false;
		}
	}

	function minutely( $minute = 1 ) {
		switch ($minute) {
			case 15:
				$this->send_campaign();
				$this->change_password();
				// $this->sync_api();
			break;
			case 30:
				$newsletter = $this->Queue->Newsletter->getData('first', array(
					'order' => array(
						'Newsletter.id' => 'ASC'
					),
				));

				if($newsletter){
					$newsletter_id = $this->RmCommon->filterEmptyField($newsletter, 'Newsletter', 'id');
					$newsletter_name = $this->RmCommon->filterEmptyField($newsletter, 'Newsletter', 'name');

						switch ($newsletter_name) {
							case 'request_ebrosur_daily':
								if( !$this->send_request_ebrosur($newsletter_id, 2) ) {
									$this->newsletterComplete( $newsletter_id );
								}
							break;
							case 'request_ebrosur_weekly':
								if( !$this->send_request_ebrosur($newsletter_id, 3) ) {
									$this->newsletterComplete( $newsletter_id );
								}
							break;
							case 'request_ebrosur_monthly':
								if( !$this->send_request_ebrosur($newsletter_id, 4) ) {
									$this->newsletterComplete( $newsletter_id );
								}
							break;
							default:
								$checkParams = explode('/', $newsletter_name);

								if( count($checkParams) > 1 ) {
									$newsletter_name = $checkParams[0];
									$newsletter_param = $checkParams[1];

									if( !$this->$newsletter_name($newsletter_param) ) {
										$this->newsletterComplete( $newsletter_id );
									}
								} else {
									if( !$this->$newsletter_name($newsletter_id) ) {
										$this->newsletterComplete( $newsletter_id );
									}
								}
							break;
						}
				} else {
					echo __('Tidak proses cronjob')."<br><br>";
				}

			//	GENERATE AGENT RANKS FOR ACTIVE COMPANIES ==================================================================

				// $this->generate_agent_rank();

			//	============================================================================================================
			break;
		}

		die();
	}

	function daily(){
		$this->insertNewsletter(array(
			'birthday',
			'request_ebrosur_daily',
			'activity_category_generator',
			'point_category_generator',
			'push_category_generator',
			'push_generator',
			'push_rank',
			'activity_listing_generator',
			'activity_ebrosur_generator',
			'generate_agent_rank',
			// 'accumulate_report/daily',
			'accumulate_report',
			'accumulate_report/year',
			// 'accumulate_user_report/daily',
			'accumulate_user_report',
			'accumulate_user_report/year',
		), 'daily');
		// $this->filter_expired_document('invoice');
		$this->renewal_notification();
		$this->auto_sent_kpr();

		// $this->generate_integration_json();
		// $this->upload_integration_json();
	}

	function weekly(){
		$this->insertNewsletter('request_ebrosur_weekly', 'weekly');
	}

	function monthly(){
		$this->insertNewsletter('request_ebrosur_monthly', 'monthly');
	}

	function send_request_ebrosur($newsletter_id = false, $cronjob_period_id, $limit = 100){
		$date = date('Y-m-d');

		$ebrosurs = $this->User->EbrosurRequest->getData('all', array(
			'conditions' => array(
				'EbrosurRequest.cronjob_period_id' => $cronjob_period_id,
				'OR' => array(
					'DATE_FORMAT(EbrosurRequest.last_send, \'%Y-%m-%d\') <' => $date,
					'EbrosurRequest.last_send' => null,
				)
			),
			'limit' => $limit,
			'order' => array(
				'EbrosurRequest.id' => 'ASC',
			)
		));
		
		if(!empty($ebrosurs)){
			foreach ($ebrosurs as $key => $value) {
				$user_id = $this->RmCommon->filterEmptyField($value, 'EbrosurRequest', 'user_id');
				$id = $this->RmCommon->filterEmptyField($value, 'EbrosurRequest', 'id');
				$flagSend = false;

				$value = $this->User->EbrosurRequest->EbrosurAgentRequest->getMerge($value);
				$value = $this->User->EbrosurRequest->EbrosurTypeRequest->getMerge($value);
				$value = $this->User->EbrosurRequest->EbrosurClientRequest->getMerge($value);

				$agent_id_collect = array();

				if(!empty($value['EbrosurAgentRequest'])){
					$agent_id_collect = $temp_agent_id = Set::extract('/EbrosurAgentRequest/agent_id', $value['EbrosurAgentRequest']);
					unset($value['EbrosurAgentRequest']);
					
					foreach ($temp_agent_id as $key => $value_agent_id) {
						$value['EbrosurAgentRequest']['agent_id'][$value_agent_id] = $value_agent_id;
					}
				}

				if(!empty($value['EbrosurTypeRequest'])){
					$temp_property_type = Set::extract('/EbrosurTypeRequest/property_type_id', $value['EbrosurTypeRequest']);
					unset($value['EbrosurTypeRequest']);

					foreach ($temp_property_type as $key => $value_type) {
						$value['EbrosurTypeRequest']['property_type_id'][$value_type] = $value_type;
					}
				}

				$list_client = array();
				if(!empty($value['EbrosurClientRequest'])){
					$list_client = Set::extract('/User/email', $value['EbrosurClientRequest']);
				}
				
				$condition = $this->User->EbrosurRequest->_callSetConditionFromRequest($value);
				
				$value = $this->User->getMerge($value, $user_id);
				$value = $this->User->EbrosurRequest->getMergeDefault($value);

				$full_name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name', null);
				$email = $this->RmCommon->filterEmptyField($value, 'User', 'email', null);

                if(!empty($condition)){
                	$group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id');
                	
                    if($group_id == 2){
                    	$agent_id_collect = $condition['ebrosur_condition']['UserCompanyEbrochure.user_id'] = $user_id;
                    }
                    
	                $count_ebrosur = $this->User->UserCompanyEbrochure->getEbrosurRequest('count', $condition);
	                $UserCompanyEbrochure = $this->User->UserCompanyEbrochure->getEbrosurRequest('all', $condition);
	                
	                $value['agent_id'] = $agent_id_collect;

                    if(!empty($UserCompanyEbrochure)){
                    	$mail_param = array(
	                		'data_support' => $value,
	                    	'data_ebrosur' => $UserCompanyEbrochure,
	                    	'data_count' => $count_ebrosur,
	                	);
	                	
	                	if(!empty($list_client)){
	                		$mail_param['bcc'] = true;
	                		$email = $list_client;
	                	}

	                	$is_send = $this->RmCommon->sendEmail(
                        	$full_name,
                        	$email,
                        	'ebrosur_request',
                        	__('Daftar Permintaan eBrosur'),
                        	$mail_param
                        );

                        if(!empty($is_send)){
							$flagSend = true;
                        }
                    }
                }

				if( !empty($flagSend) ) {
					$this->insertQueue( $id, 'request_ebrosur', $newsletter_id );
				} else {
					$this->insertQueue( $id, 'request_ebrosur', $newsletter_id, 0 );
				}
            	
            	$this->User->EbrosurRequest->set_last_send($id);
			}
		} else {
			return false;
		}

		die();
	}

	## SENT DATA APPLICATION 
	function sync_application(){

		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'process-kpr-api'
			),
		));
		if(!empty($setting)){
			$url = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
			$default_conditions = array(
				'KprBank.code <>' => NULL,
				'OR' => array(
					array(
						'KprBank.application_status' => 'completed',
						'KprBank.document_status <>' => array(
							'pending',
							'process',
							'cancel',
							'approved_admin',
							'rejected_admin',
							'rejected_proposal',
							'proposal_without_comiission',
							'rejected_bank',
							'rejected_credit',
						),
					),
					array(
						'KprBank.document_status' => 'proposal_without_comiission',
						'KprBank.application_status' => 'resend',
					),
				),
			);
			
			$kpr_list = $this->User->Kpr->KprBank->getData('list', array(
				'conditions' => array(
					'KprBank.application_snyc' => FALSE,
					$default_conditions,
				),
				'group' => array('KprBank.kpr_id'),
				'fields' => array('KprBank.id', 'KprBank.kpr_id'),
			));

			$values = $this->User->Kpr->getData('all', array(
				'conditions' => array(
					'Kpr.id' => $kpr_list,
				),
				'limit' => 3,
			), array(
				'company' => false,
			));
			
			if(!empty($values)){
				foreach($values AS $key => $value){
					$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id', null);
					$property_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_id', null);
					$agent_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'agent_id', null);

					$value = $this->User->Kpr->Property->getMerge($value, $property_id);
					$value = $this->User->getMerge($value, $agent_id, false, 'Agent');
					
					$parent_id = $this->RmCommon->filterEmptyField($value, 'Agent', 'parent_id');
					$value = $this->User->getMerge($value, $parent_id, false, 'Principle');
					$value = $this->User->UserCompany->getMerge($value, $parent_id);

					$client_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id');
					$value = $this->User->Kpr->KprApplication->mergeApplication( $value, $kpr_id );
					$kpr_application_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'id');

					$kpr_application_particular_id = $this->RmCommon->filterEmptyField($value, 'KprApplicationParticular', 'id');
					$owner_id = $this->RmCommon->filterEmptyField($value, 'Property', 'client_id');

					$value = $this->User->UserClient->getMerge($value, $owner_id, false, 'Owner');
					$value = $this->User->UserProfile->getMerge($value, $owner_id, true, 'OwnerProfile');

					$kpr_banks = $this->User->Kpr->KprBank->getData('all', array(
						'conditions' => array(
							'KprBank.kpr_id' => $kpr_id,
							'KprBank.application_snyc' => FALSE,
							$default_conditions,
						)
					));

					// if(!empty($kpr_banks)){
					// 	$value = array_merge( $value, $kpr_banks);
					// }
					
					$value['documentCategories'] = $this->RmKpr->getDocumentSort( array(
							'DocumentCategory.is_required' => 1,
							'DocumentCategory.id <>' => array( 3, 7, 19, 20),
						), array(
							'id' => $kpr_id,
							'owner_id' => !empty($client_id)?$client_id:0,
							'document_type' => 'kpr_application',
					), $value);

					if(!empty($kpr_application_particular_id)){
						$value['documentCategoriesSpouse'] = $this->RmKpr->getDocumentSort( array(
								'DocumentCategory.is_required' => 1,
								'DocumentCategory.id <>' => array( 1, 2, 3, 4, 5, 6, 7, 15, 16, 17 ),
							), array(
								'id' => $kpr_id,
								'owner_id' => !empty($client_id)?$client_id:0,
								'document_type' => 'kpr_spouse_particular',
						), $value);	
					}
					$url = sprintf('%s/kpr/api_sync_application.json', $url);

					$value['utm'] = $this->_base_url;
					if(!empty($kpr_banks)){
						foreach($kpr_banks AS $key => $kpr_bank){
							$kpr_bank = array_merge($value, $kpr_bank);
							$data_api = $this->RumahkuApi->api_access($kpr_bank, 'api_sync_application', $url, 'kpr');
							
							if(!empty($data_api)){
								$data_api = json_decode($data_api, true);
								$data_api = $this->RmCommon->filterEmptyField($data_api, 'data', 'result');
								$status = $this->RmCommon->filterEmptyField($data_api, 'status');

								if($status == 'success'){
									$result = $this->User->Kpr->KprBank->doUpdateSnycApplication($data_api, 'application_snyc');
									$flag = $this->RmCommon->filterEmptyField($result, 'flag');
									$status = $this->RmCommon->filterEmptyField($result, 'status');
									$code = $this->RmCommon->filterEmptyField($result, 'code');
									
									if(!empty($flag) && $status == 'success'){
										printf(__('Berhasil kirim Aplikasi KPR #%s<br>'), $code);				
									}else{
										printf(__('Gagal kirim Aplikasi KPR #%s, karena ada beberapa kpr_bank_id NULL<br>'), $code);					
									}

								}else{
									$msg = $this->RmCommon->filterEmptyField($data_api, 'msg');
									echo $msg;
									$log_msg = sprintf(__('[KPR-AGENT-INFORMATION] %s'), $msg);
			                    	$this->RmCommon->_saveLog($log_msg, $value, false, true, 306);
								}

							}else{
								$msg =  __('Tidak ada pengembalian result data API ATAU data Putus di System KPR<br>');
								echo $msg;
								$log_msg = sprintf(__('[KPR-AGENT-INFORMATION] %s'), $msg);
			                	$this->RmCommon->_saveLog($log_msg, $value, false, true, 301);
							}
						}
					}else{
						echo __('Tidak memiliki pengajuan KPR, silakan ajukan terlebih dahulu');
					}

				}
			}else{
				echo __('Data tidak ditemukan <br>');
			}
		}else{
			echo __('not get permission / no token');
		}
		die();
	}

	// SENT Aplikasi KPR ke Bank
	// function sync_informasi_kpr(){
	// 	$url = Configure::read('__Site.kpr_url');

	// 	$queues = $this->User->KprApplication->KprApplicationRequest->getData( 'all', array(
	// 		'conditions' => array(
	// 			'OR' => array(
	// 				array(
	// 					'KprApplicationRequest.snyc' => array(FALSE, 0, null),
	// 					'KprApplicationRequest.aprove_proposal' => array(TRUE, 1),
	// 					'KprApplicationRequest.rejected_commission' => array(FALSE, 0, null),
	// 				),
	// 				array(
	// 					'KprApplicationRequest.snyc' => array(FALSE, 0, null),
	// 					'KprApplicationRequest.aprove_proposal' => array(TRUE, 1),
	// 					'KprApplicationRequest.rejected_commission' => array(TRUE, 1),
	// 					'KprApplicationRequest.resend' => array(TRUE, 1),
	// 				),
	// 			),
				
	// 		),
	// 		'limit' => Configure::read('__Site.config_limit_crontab'),
	// 	));
	// 	## REJECT APPLICATION
	// 	$queues2 = $this->User->KprApplication->KprApplicationRequest->getData( 'all', array(
	// 		'conditions' => array(
	// 			'KprApplicationRequest.status' => array(FALSE, 0, null),
	// 			'KprApplicationRequest.cancel_project' => array(FALSE, 0, null),
	// 			'KprApplicationRequest.snyc' => array(FALSE, 0, null),
	// 			'KprApplicationRequest.aprove_proposal' => array(TRUE, 1),
	// 			'KprApplicationRequest.rejected_commission' => array(TRUE, 1),
	// 			'KprApplicationRequest.resend' => array(TRUE, 1),
	// 		),
	// 		'limit' => Configure::read('__Site.config_limit_crontab'),
	// 	));
	// 	## MERGE VALUE APPLIKASI KPR DAN REJECT KPR
	// 	$queues = array_merge( $queues2, $queues);
	// 	$result = array();

	// 	if(!empty($queues)){
	// 		foreach($queues AS $key => $value){
	// 			$application_id = $this->RmCommon->filterEmptyField($value, 'KprApplicationRequest', 'kpr_application_id');
	// 			$value = $this->User->KprApplication->getMerge( $value, $application_id);
	// 			$value = $this->User->KprApplication->KprSpouseParticular->getMerge( $value, $application_id);
	// 			$client_id = $this->RmCommon->filterEmptyField($value, 'KprApplication', 'user_id');
	// 			$value['documentCategories'] = $this->RmKpr->getDocumentSort( array(
	// 					'DocumentCategory.is_required' => 1,
	// 					'DocumentCategory.id <>' => array( 3, 7, 19, 20),
	// 				), array(
	// 					'id' => $application_id,
	// 					'owner_id' => $client_id,
	// 					'document_type' => 'kpr_application',
	// 			), $value);	
	// 			$value['documentCategoriesSpouse'] = $this->RmKpr->getDocumentSort( array(
	// 					'DocumentCategory.is_required' => 1,
	// 					'DocumentCategory.id <>' => array( 1, 2, 3, 4, 5, 6, 7, 15, 16, 17 ),
	// 				), array(
	// 					'id' => $application_id,
	// 					'owner_id' => $client_id,
	// 					'document_type' => 'kpr_spouse_particular',
	// 			), $value);	

	// 			$url = sprintf('%s/kpr/api_information_kpr.json', $url);

	// 			$value['utm'] = $this->_base_url;
	// 			$data_api = $this->RumahkuApi->api_access($value, 'api_information_kpr', $url,'kpr');

	// 			if(!empty($data_api)){
	// 				$data_api = json_decode($data_api, true);
	// 				$data_api = $data_api['data'];

	// 				$id = $this->RmCommon->filterEmptyField( $data_api, 'id');
	// 				$status = $this->RmCommon->filterEmptyField( $data_api, 'status');
	// 				$cancel_kpr = $this->RmCommon->filterEmptyField( $data_api, 'cancel_kpr');

	// 				if($status == 'success'){
	// 					$result_data = $this->User->KprApplication->KprApplicationRequest->doUpdateSnycApplication( $data_api, $cancel_kpr);
	// 					$this->RmCommon->setProcessParams($result_data,false,array(
	// 						'noRedirect' => true
	// 					));

	// 					printf(__('Berhasil kirim Informasi Kpr ID: %s'), $application_id);

	// 				}else{
	// 					sprintf(__('Gagal Kirim Informasi Kpr ID: %s'), $application_id);
	// 				}

	// 			}else{
	// 				echo __('Gagal Kirim Informasi Kpr');
	// 			}

	// 		}
	// 		die();

	// 	}else{
	// 		echo __('Data tidak ditemukan');
	// 	}
	// 	die();
	// }

	function kpr_cancel(){

		App::import('Helper', 'Html');

        $this->Html = new HtmlHelper(new View(null));

		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'process-kpr-api'
			),
		));

		if(!empty($setting)){
			$url = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');

			$kpr_list = $this->User->Kpr->KprBank->getData('list',array(
				'conditions' => array(
					'KprBank.snyc' => 0,
					'KprBank.document_status' => array('cancel'),
					'KprBank.code <>' => NULL,
				),
				'fields' => array('KprBank.id', 'KprBank.kpr_id'),
				'group' => array('KprBank.kpr_id'),
			));

			$values = $this->User->Kpr->getData('all', array(
				'conditions' => array(
					'Kpr.id' => $kpr_list,
				),
				'limit' => Configure::read('__Site.config_limit_crontab'),
			), array(
				'company' => false,
			));	
			$result = array();

			if(!empty($values)){
				foreach($values AS $key => $value){
					$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
					$kprBanks = $this->User->Kpr->KprBank->getData('all', array(
						'conditions' => array(
							'KprBank.kpr_id' => $kpr_id,
							'KprBank.snyc' => 0,
							'KprBank.document_status' => 'cancel',
							'KprBank.code <>' => NULL,
						),
					));

					if(!empty($kprBanks)){
						foreach($kprBanks AS $loop => $kprBank){
							$kpr_bank_id = $this->RmCommon->filterEmptyField($kprBank, 'KprBank', 'id');
							$document_status = $this->RmCommon->filterEmptyField($kprBank, 'KprBank', 'document_status');
							$kprBank = $this->User->Kpr->KprBank->KprBankDate->getFromSlug($kprBank, $kpr_bank_id, array(
								'find' => 'first',
								'conditionOptions' => array(
									'KprBankDate.slug' => $document_status,
								),
							));
							$KprBank = array_merge($kprBank, $value);
							$url = sprintf('%s/kpr/api_kpr_cancel.json', $url);
							$data_api = $this->RumahkuApi->api_access($KprBank, 'api_kpr_cancel', $url,'kpr');
							
							if(!empty($data_api)){
								$data_api = json_decode($data_api, true);
								$data = $this->RmCommon->filterEmptyField($data_api, 'data', 'result');
								$status = $this->RmCommon->filterEmptyField($data, 'status');

								if($status == 'success'){
									$result = $this->User->Kpr->KprBank->doUpdateApiCancel($data);
									$msg = $this->RmCommon->filterEmptyField($result, 'msg');
									$this->RmCommon->setProcessParams($result,false,array(
										'noRedirect' => true
									));
									echo sprintf('%s <br>', $msg);
								}else{
									$url = $this->Html->url(array(
										'controller' => 'kpr',
										'action' => 'clear_snyc',
										'slug' => 'cancel',
										$kpr_bank_id,
									));

									$msg = $this->RmCommon->filterEmptyField($data, 'msg');
									$msg .= $this->Html->link(__(' Clear'), $url);
									echo sprintf('%s <br>', $msg);
								}
							}else{
								echo __('data API tidak ditemukan / rusak ');
							}
						}
					}else{
						echo __('Pengajuan tidak ditemukan <br>');
					}
				}
			}else{
				echo __('Data tidak ditemukan');
				die();
			}
		}else{
			echo __('not get permission / no token');
		}
		die();
	}

	// Pengiriman Data Agent, dan info bank transfer Provisi
	function kpr_credit_process(){

		$url = Configure::read('__Site.kpr_url');

		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'process-kpr-api'
			),
		));

		if(!empty($setting)){
			$url = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');

			$kpr_list = $this->User->Kpr->KprBank->getData('list',array(
				'conditions' => array(
					'KprBank.snyc' => 0,
					'KprBank.document_status' => array('credit_process'),
					'KprBank.code <>' => NULL,
				),
				'fields' => array('KprBank.id', 'KprBank.kpr_id'),
				'group' => array('KprBank.kpr_id')
			));

			$values = $this->User->Kpr->getData('all', array(
				'conditions' => array(
					'Kpr.id' => $kpr_list,
				),
				'limit' => Configure::read('__Site.config_limit_crontab')
			), array(
				'company' => false,
			));		
			$values = $this->User->Kpr->getCreditProcessCrontabList($values);
			$result = array();
			
			if(!empty($values)){
				foreach($values AS $key => $value){
					$url = sprintf('%s/kpr/api_kpr_credit_process.json', $url);
					$value['utm'] = $this->_base_url;
					$data_api = $this->RumahkuApi->api_access($value, 'kpr_credit_process', $url,'kpr');
					// echo($data_api);die();

					if(!empty($data_api)){
						$data_api = json_decode($data_api, true);
						$data_api = $data_api['data'];

						$kpr_bank_id_arr = $this->RmCommon->filterEmptyField($data_api, 'result', 'kpr_bank_id_arr');
						$status_arr = $this->RmCommon->filterEmptyField($data_api, 'result', 'status_arr');

						if(!empty($kpr_bank_id_arr)){
							foreach($kpr_bank_id_arr AS $key => $kpr_bank_id){
									$val_kpr = $this->User->Kpr->KprBank->getData('first', array(
										'conditions' => array(
											'KprBank.id' => $kpr_bank_id,
										),
									));
									$kpr_id = $this->RmCommon->filterEmptyField($val_kpr, 'KprBank', 'kpr_id');
									$val_kpr = $this->User->Kpr->getMerge($val_kpr, $kpr_id);
									$code = $this->RmCommon->filterEmptyField($val_kpr, 'KprBank', 'code');
								if(!empty($status_arr[$key]) && $status_arr[$key] == 'success'){

									$result_data = $this->User->Kpr->KprBank->doUpdateCreditProcess($val_kpr);
									$this->RmCommon->setProcessParams($result_data,false,array(
										'noRedirect' => true
									));
									printf(__('Berhasil Proses Akad Kredit dengan KODE #%s<br>'), $code);

								}else{
									$msg = sprintf(__('Gagal Proses Akad Kredit dengan KODE #%s<br>'), $code);
									echo $msg;
									$log_msg = sprintf(__('[KPR-AGENT-INFORMATION] %s'), $msg);
	                    			$this->RmCommon->_saveLog($log_msg, $value, $code, true, 306);

								}
							}
						}else{
							$msg =  __('ID KPR bank tidak ditemukan <br>');
							echo $msg;
							$log_msg = sprintf(__('[KPR-AGENT-INFORMATION] %s'), $msg);
	                    	$this->RmCommon->_saveLog($log_msg, $value, false, true, 306);
						}

					}else{
						$msg =  __('Data API Eror');
						$log_msg = sprintf(__('[KPR-AGENT-INFORMATION] %s'), $msg);
	                	$this->RmCommon->_saveLog($log_msg, $value, false, true, 301);
					}
				} 
				die();
			}else{
				echo __('Data tidak ditemukan<br>');
			}
		}else{
			echo __('not get permission / no token');
		}
		die();	

	}

	function add_property($id_target, $data = false){
		$data = $this->User->Property->find('first', array(
			'conditions' => array(
				'Property.id' => $id_target,
			)
		));
		$data = $this->User->Property->getDataList($data, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'PropertyMedias',
				'PropertyVideos',
				'PropertyPrice',
				'PropertyFacility',
				'PropertyPointPlus',
			),
		));
		$result = array();

		if(!empty($data)){
			$data = $this->RmProperty->setConvertSetToV3($data);
			$user_id = $this->RmCommon->filterEmptyField($data, 'Property', 'user_id');
			
			$result_user = $this->create_user($user_id);
			
			$user_id = $this->RmCommon->filterEmptyField($result_user, 'id');
			
			if(!is_array($user_id)){
				if(!empty($user_id)){
					$data['Property']['user_id'] = $user_id;
				}

				$data['Property']['active'] = 1;
				$data['Property']['status'] = 0;
				$data['Property']['sold'] = 0;
				$data['Property']['published'] = 1;
				$data['Property']['deleted'] = 0;

				unset($data['Property']['id']);
				
				$data_api = $this->RumahkuApi->api_access($data, 'sell');
				
				if(!empty($data_api)){
					$data_api = json_decode($data_api, true);
					$data_api = $data_api['data'];

					if(!empty($data_api['status'])){
						if(!empty($data_api['id'])){
							$this->User->Property->id = $id_target;
							$this->User->Property->set('property_id_target', $data_api['id']);

							if($this->User->Property->save()){
								$id_target = $data_api['id'];
								
								if(!empty($data['PropertyMedias'])){
									$this->add_media($id_target, $data);
								}

								$result = array(
									'msg' => __('id properti target tidak ditemukan.'),
									'status' => true,
									'code_error' => 200,
									'id' => $id_target
								);
							}
						}else{
							$result = array(
								'msg' => 'id properti target tidak ditemukan.',
								'status' => false,
								'code_error' => 306,
							);
						}
					}else{
						$result = $data_api;
					}
				}else{
					$result = array(
						'msg' => 'tidak bisa akses api',
						'status' => false,
						'code_error' => 304,
						'data' => $data
					);
				}
			}else{
				$result = array(
					'msg' => 'id target user tidak ditemukan',
					'status' => false,
					'code_error' => 302
				);
			}
		}else{
			$result = array(
				'msg' => 'data properti tidak ditemukan',
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-ADD-PROPERTY] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $data, $id_target, $result['status'], $result['code_error']);
		}
		
		return $result;
	}

	function create_user( $user_id ){
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $user_id
			)
		), array(
			'status' => 'all'
		));
		$result = array();
		
		if(!empty($user)){
			$group_id = $this->RmCommon->filterEmptyField($user, 'User', 'group_id');
			$user = $this->User->UserProfile->getMerge($user, $user['User']['id']);
			$company_id = $user['User']['id'];

			if($group_id != 3){
				$company_id = $user['User']['parent_id'];
				$parent = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $company_id
					)
				), array(
					'status' => 'active'
				));

				if(empty($parent['User']['user_id_target'])){
					$result_parent = $this->create_user($company_id);

					$id_parent = $this->RmCommon->filterEmptyField($result_parent, 'id');

					$user['User']['parent_id'] = $id_parent;
				}
			}

			$user = $this->User->UserCompany->getMerge($user, $company_id);
			$user = $this->RmUser->setConvertSetToV3($user);

			unset($user['User']['id']);

			if(empty($user['User']['user_id_target'])){
				$user_data = $this->RumahkuApi->api_access($user, 'create_user');

				if(!empty($user_data)){
					$user_data = json_decode($user_data, true);
					$user_data = $user_data['data'];
					
					if( !empty($user_data['status']) && !empty($user_data['id']) ){
						if(!empty($user_data['id'])){
							$this->User->id = $user_id;

							$this->User->set('user_id_target', $user_data['id']);

							if($this->User->save()){
								$result = array(
									'msg' => __('Berhasil menyimpan user_id_target.'),
									'status' => true,
									'code_error' => 200,
									'id' => $user_data['id']
								);
							}else{
								$result = array(
									'msg' => 'Gagal menyimpan user_id_target.',
									'status' => false,
									'code_error' => 306
								);
							}
						}else{
							$result = $user_data;

							$result['code_error'] = 306;
						}
					}else{
						$result = $user_data;

						$result['code_error'] = 306;
					}
				}else{
					$result = array(
						'msg' => 'tidak bisa akses api',
						'status' => false,
						'code_error' => 304
					);
				}
			}else{
				$result = array(
					'msg' => __('User sudah pernah di sync'),
					'status' => true,
					'code_error' => 200,
					'id' => $user['User']['user_id_target']
				);
			}
		}else{
			$result = array(
				'msg' => sprintf('data user tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg'])){
			$log_msg = sprintf(__('[SYNC-CREATE-USER] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $user, $user_id, $result['status'], $result['code_error'], array('user_id' => $user_id));
		}
		
		return $result;
	}

	function add_media($property_id_target, $data, $model = 'PropertyMedias', $action = 'add_media'){
		$result = array();

		if(!empty($data)){
			$data = $this->RmProperty->setConvertSetToV3($data);
			$data = $this->RmProperty->__callSetMedia($data, $property_id_target);

			$data['property_id_target'] = $property_id_target;

			if(!empty($data['PropertyVideos']) && $model == 'PropertyVideos'){
				$model = 'PropertyVideos';
			}

			$data['model'] = $model;
			
			$data_api = $this->RumahkuApi->api_access($data, $action);
			
			if(!empty($data_api)){
				$data_api = json_decode($data_api, true);
				$data_api = $data_api['data'];
				
				if(!empty($data_api['status'])){
					$result = array(
						'msg' => sprintf(__('Berhasil menambah media %s properti'), $model),
						'status' => true,
						'code_error' => 200
					);
				}else{
					$result = $data_api;

					$result['code_error'] = 306;
				}
			}else{
				$result = $data_api;
				$result['code_error'] = 304;
			}
		}else{
			$result = array(
				'msg' => ('data tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-ADD-MEDIA] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $data, $property_id_target, $result['status'], $result['code_error']);
		}

		return $result;
	}

	function add_video($property_id_target, $data){
		$result = array();

		if(!empty($data)){
			$this->add_media($property_id_target, $data, 'PropertyVideos');
		}else{
			$result = array(
				'msg' => ('data tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg'])){
			$log_msg = sprintf(__('[SYNC-ADD-VIDEO] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $data, $property_id_target, $result['status'], $result['code_error']);
		}

		return $result;
	}

	function edit_user($id_target, $data_sync){
		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id_target
			)
		));

		$result = array();
		
		if(!empty($user)){
			$user = $this->User->UserProfile->getMerge($user, $user['User']['id']);
			$user = $this->User->UserCompany->getMerge($user, $user['User']['id']);
			
			$user = $this->RmUser->setConvertSetToV3($user);
			
			$data_api = $this->RumahkuApi->api_access($user, 'edit_user');
			
			if(!empty($data_api)){
				$data_api = json_decode($data_api, true);
				$data_api = $data_api['data'];
				
				if( !empty($data_api['status']) ){
					if(!empty($data_api['id'])){
						$this->User->id = $id_target;

						$this->User->set('user_id_target', $data_api['id']);

						if($this->User->save()){
							$result = array(
								'msg' => __('Berhasil mengubah user'),
								'status' => true,
								'code_error' => 200
							);
						}else{
							$result = array(
								'msg' => __('Gagal menyimpan user_id_target'),
								'status' => false,
								'code_error' => 306
							);
						}
					}else{
						$result = $data_api;

						$result['code_error'] = 306;
					}
				}else{
					$result = $data_api;

					$result['code_error'] = 306;
				}
			}else{
				$result = array(
					'msg' => 'tidak bisa akses api',
					'status' => false,
					'code_error' => 304
				);
			}
		}else{
			$result = array(
				'msg' => __('data user tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-EDIT-USER] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $user, $id_target, $result['status'], $result['code_error']);
		}
		
		return $result;
	}

	function property_sold($id_target, $data_sync){
		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id_target,
			)
		), array(
			'status' => 'sold',
			'admin_mine' => false,
			'sold_mine' => false,
			'company' => false
		));

		$result = array();
		
		if(!empty($property)){
			if( !empty($property['Property']['property_id_target']) ){
				$user_id = $this->RmCommon->filterEmptyField($property, 'User', 'user_id');

				$property = $this->User->UserProfile->getMerge($property, $user_id);

				$property_id_target = $this->RmCommon->filterEmptyField($property, 'Property', 'property_id_target');
				$user_id_target = $this->RmCommon->filterEmptyField($property, 'User', 'user_id_target');
				
				$data_sync['PropertySold']['property_id'] = $property_id_target;
				$data_sync['Property']['user_id'] = $user_id_target;

				$data = $this->RmProperty->setConvertSetToV3Sold($data_sync);

				$data_api = $this->RumahkuApi->api_access($data, 'property_sold');
				
				if(!empty($data_api)){
					$data_api = json_decode($data_api, true);
					$data_api = $data_api['data'];
					
					if( !empty($data_api['status']) ){
						$result = array(
							'msg' => __('Berhasil melakukan properti terjual'),
							'status' => true,
							'code_error' => 200
						);
					}else{
						$result = $data_api;

						$result['code_error'] = 306;
					}
				}else{
					$result = array(
						'msg' => 'tidak bisa akses api',
						'status' => false,
						'code_error' => 304
					);
				}
			}else{
				$id = $this->RmCommon->filterEmptyField($property, 'Property', 'id');

				$result_property = $this->add_property($id);
					
				$property_id_result = $this->RmCommon->filterEmptyField($result_property, 'id');

				$cek = !empty($property_id_result) ? true : false;

				if( !empty($cek) ){
					$this->property_sold($id, $data_sync);
				}else{
					$result = array(
						'msg' => __('properti tidak berhasil di sync'),
						'status' => false,
						'code_error' => 302
					);
				}
			}
		}else{
			$result = array(
				'msg' => __('data properti tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg'])){
			$log_msg = sprintf(__('[SYNC-PROPERTY-SOLD] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $property, $id_target, $result['status'], $result['code_error']);
		}

		return $result;
	}

	function property_unsold($id_target, $data_sync){
		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id_target,
			)
		), array(
			'admin_mine' => false,
			'sold_mine' => false,
			'company' => false
		));

		$result = array();

		if(!empty($property)){
			$user_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id');
			
			$property = $this->User->getMerge($property, $user_id);
			
			$property_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_id_target');
			
			$user_id = $this->RmCommon->filterEmptyField($property, 'User', 'user_id_target');

			$data['property_id'] = $property_id;
			$data['user_id'] = $user_id;
			
			$data_api = $this->RumahkuApi->api_access($data, 'property_unsold');
			
			if(!empty($data_api)){
				$data_api = json_decode($data_api, true);
				$data_api = $data_api['data'];
				
				if( !empty($data_api['status']) ){
					$result = array(
						'msg' => __('Berhasil melakukan pembatalan properti terjual'),
						'status' => true,
						'code_error' => 200
					);
				}else{
					$result = $data_api;

					$result['code_error'] = 306;
				}
			}else{
				$result = array(
					'msg' => __('tidak bisa akses api'),
					'status' => false,
					'code_error' => 304
				);
			}
		}else{
			$result = array(
				'msg' => __('data properti tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-PROPERTY-UNSOLD] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $property, $id_target, $result['status'], $result['code_error']);
		}

		return $result;
	}

	function property_delete($id_target){
		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id_target,
			)
		), array(
			'admin_mine' => false,
			'sold_mine' => false,
			'company' => false,
			'status' => 'deleted'
		));
		
		$result = array();

		if(!empty($property)){
			$property_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_id_target');
			$user_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id');

			$property = $this->User->getMerge($property, $user_id);

			$user_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id_target');

			$data['property_id'] = $property_id;
			$data['user_id'] = $user_id;

			$data_api = $this->RumahkuApi->api_access($data, 'property_delete');
			
			if(!empty($data_api)){
				$data_api = json_decode($data_api, true);
				$data_api = $data_api['data'];
				
				if( !empty($data_api['status']) ){
					$result = array(
						'msg' => __('Berhasil menghapus properti'),
						'status' => true,
						'code_error' => 200
					);
				}else{
					$result = $data_api;

					$result['code_error'] = 306;
				}
			}else{
				$result = $data_api;

				$result['code_error'] = 304;
			}
		}else{
			$result = array(
				'msg' => __('data properti tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-PROPERTY-DELETE] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $property, $id_target, $result['status'], $result['code_error']);
		}

		return $result;
	}

	function property_revision($id_target, $data_sync){
		$target_data = $this->User->Property->getTargetID($id_target);

		$property_id_target = $this->RmCommon->filterEmptyField($target_data, 'data_target', 'property_id_target');
		$user_id_target = $this->RmCommon->filterEmptyField($target_data, 'data_target', 'user_id_target');

		$result = array();
		$data = array();

		if(!empty($target_data)){
			$id = $this->RmCommon->filterEmptyField($target_data, 'data_target', 'id');
			$user_id = $this->RmCommon->filterEmptyField($target_data, 'data_target', 'user_id');

			if( !empty($property_id_target) && !empty($user_id_target) ){
				$data = $this->RmProperty->setConvertSetToV3($data_sync);
				$data = array_merge($data, $target_data);

				$data_api = $this->RumahkuApi->api_access($data, 'property_revision');
				
				if(!empty($data_api)){
					$data_api = json_decode($data_api, true);
					$data_api = $data_api['data'];
					
					if( !empty($data_api['status']) ){
						$result = array(
							'msg' => __('Berhasil mengubah properti'),
							'status' => true,
							'code_error' => 200
						);
					}else{
						$result = $data_api;

						$result['code_error'] = 306;
					}
				}else{
					$result = $data_api;

					$result['code_error'] = 304;
				}
			}else{
				$cek_property = true;
				$cek_user = true;

				if(empty($property_id_target)){
					$result_property = $this->add_property($id);
					
					$property_id_result = $this->RmCommon->filterEmptyField($result_property, 'id');

					$cek_property = !empty($property_id_result) ? true : false;
				}
				
				if(empty($user_id_target)){
					$result_user = $this->create_user($user_id);

					$user_id = $this->RmCommon->filterEmptyField($result_user, 'id');

					$cek_user = !empty($user_id) ? true : false;
				}
				
				if(!empty($cek_user) && !empty($cek_property)){
					$this->property_revision($id_target, $data_sync);
				}else{
					$result = array(
						'msg' => __('data property atau user tidak berhasil di sync'),
						'status' => false,
						'code_error' => 302
					);
				}
			}
		}else{
			$result = array(
				'msg' => __('data properti revision tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-PROPERTY-REVISION] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $data, $id_target, $result['status'], $result['code_error']);
		}

		return $result;
	}

	function generate_facility_and_pointplus($parent_id = false, $limit = 100){
   		$users = $this->User->getData('list', array(
   			'conditions' => array(
   				'User.parent_id' => $parent_id
   			)
   		), array(
   			'status' => 'all'
   		));
		$facilities = $this->User->Property->PropertyAsset->getData('all', array(
			'conditions' => array(
				'Property.user_id' => $users,
				'PropertyAsset.sync' => 0,
				'OR' => array(
					'PropertyAsset.property_facilities not' => null,
					'PropertyAsset.property_facilities_others not' => null,
					'PropertyAsset.property_point_plus not' => null,
				)
			),
			'contain' => array(
				'Property',
			),
			'order' => array(
				'PropertyAsset.id' => 'DESC'
			),
			// 'limit' => $limit
		));
		// var_dump($facilities);die();

		$this->loadModel('PropertyFacilityMigrate');
		
		if(!empty($facilities)){
			foreach ($facilities as $key => $value) {
				$property_facilities = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'property_facilities');
				$property_facilities_others = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'property_facilities_others');
				$property_point_plus = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'property_point_plus');

				$id = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'id');
				$property_id = $this->RmCommon->filterEmptyField($value, 'PropertyAsset', 'property_id');

				if(!empty($property_facilities) || !empty($property_facilities_others) || !empty($property_point_plus)){
					$property_id = intval($property_id);
					$facility_arr = array();

					if(!empty($property_facilities)){
						$property_facilities = explode(',', $property_facilities);

						foreach ($property_facilities as $key => $value) {
							$facility_arr[] = array(
								'property_id' => $property_id,
								'facility_id' => intval($value),
							);
						}
					}

					if(!empty($property_facilities_others)){
						$facility_arr[] = array(
							'property_id' => $property_id,
							'facility_id' => -1,
							'other_text' => $property_facilities_others
						);
					}
					
					if(!empty($facility_arr)){
						$this->PropertyFacilityMigrate->create();
						
						$this->PropertyFacilityMigrate->saveMany($facility_arr);
					}

					if(!empty($property_point_plus)){
						$property_point_plus = unserialize($property_point_plus);

						if(!empty($property_point_plus)){
							$point_plus = array();
							foreach ($property_point_plus as $key => $value) {
								$point_plus[] = array(
									'property_id' => $property_id,
									'name' => $value
								);
							}

							$this->User->Property->PropertyPointPlus->create();

							$this->User->Property->PropertyPointPlus->saveMany($point_plus);
						}
					}
				}

				$this->User->Property->PropertyAsset->id = $id;
				$this->User->Property->PropertyAsset->set('sync', 1);

				$this->User->Property->PropertyAsset->save();
			}
		}else{
			$this->send_mail_generate('property_facilities dan property_point_plus');
		}
	}

	function generate_property_revision($parent_id = false, $limit = 100){
   		$users = $this->User->getData('list', array(
   			'conditions' => array(
   				'User.parent_id' => $parent_id
   			)
   		), array(
   			'status' => 'all'
   		));

		$property_revision = $this->User->Property->PropertyRevision->getData('all', array(
			'conditions' => array(
				'PropertyRevision.sync' => 0,
				'PropertyRevision.status' => 1,
				'Property.user_id' => $users,
			),
			'contain' => array(
				'Property',
			),
			// 'limit' => $limit,
		));
		
		if(!empty($property_revision)){
			$this->loadModel('PropertyRevisionMigrate');
			$this->loadModel('PropertyRevisionFails');

			foreach ($property_revision as $key => $value) {
				$temp = $revisions = $this->RmCommon->filterEmptyField($value, 'PropertyRevision', 'revisions');
				$property_id = $this->RmCommon->filterEmptyField($value, 'PropertyRevision', 'property_id');
				$id = $this->RmCommon->filterEmptyField($value, 'PropertyRevision', 'id');
				
				if(!empty($revisions)){

					$revisions = @unserialize($revisions);
					if ($revisions !== false) {
					    $revisions = unserialize($temp);
					}

					if(!empty($revisions)){
						$revisions = $this->RmProperty->setConvertSetToNewCompany($revisions);
	        			$data_revision = $this->RmProperty->shapingArrayRevision($revisions, false);
						
						if(!empty($data_revision)){

							foreach ($data_revision as $key => $value) {
								$data_revision[$key]['property_id'] = $property_id;

								if(!empty($value['model']) && $value['model'] == 'Property'){
									$data_revision[$key]['step'] = 'Basic';
								}else if(!empty($value['model']) && $value['model'] == 'PropertyAddress'){
									$data_revision[$key]['step'] = 'Address';
								}else if(!empty($value['model']) && $value['model'] == 'PropertyAsset'){
									$data_revision[$key]['step'] = 'Asset';
								}
							}
							
							if($this->PropertyRevisionMigrate->saveMany($data_revision)){
								$this->User->Property->inUpdateChange($property_id, true, true);
							}
						}
					}else{
						$this->PropertyRevisionFails->create();
						$this->PropertyRevisionFails->set(array(
							'property_id' => $property_id,
							'value' => $temp
						));
						$this->PropertyRevisionFails->save();
					}
				}

				$this->User->Property->PropertyRevision->id = $id;
				$this->User->Property->PropertyRevision->set('sync', 1);

				$this->User->Property->PropertyRevision->save();
			}
		}else{
			$this->send_mail_generate('property_revision');
		}
	}

	function send_mail_generate($nama_table, $message = false){
		if(empty($message)){
			$message = sprintf(__('tabel %s telah berhasil di sync'), $nama_table);
		}
		$mail_param['content'] = $message;
		// $mail_param['debug'] = 'view';

		$this->RmCommon->sendEmail(
        	'RnD',
        	'ichsan@rumahku.com',
        	'netral',
        	$mail_param['content'],
        	$mail_param
        );
	}

	function send_campaign(){
		$hour = date('H');
		$menit = date('i');

		if($menit <= 30){
			$time_1 = $hour.':00:00';
			$time_2 = $hour.':30:00';
		}else{
			$time_1 = $hour.':30:00';
			$time_2 = $hour.':59:00';
		}
			
		$campaign = $this->User->MailchimpCampaign->getData('all', array(
			'conditions' => array(
				'MailchimpCampaign.type_period' => 'scheduled',
				'MailchimpCampaign.date_send' => date('Y-m-d'),
				'MailchimpCampaign.time_send >=' => $time_1,
				'MailchimpCampaign.time_send <=' => $time_2,
				'MailchimpCampaign.is_send' => 0
			),
			'order' => array(
				'MailchimpCampaign.id' => 'ASC'
			),
			'limit' => 10,
		), array(
			'company' => false,
		));

		if(!empty($campaign)){
			foreach ($campaign as $key => $value) {
				$company_id = $this->RmCommon->filterEmptyField($value, 'MailchimpCampaign', 'company_id');
				$subject_campaign = $this->RmCommon->filterEmptyField($value, 'MailchimpCampaign', 'subject_campaign');
				$content_campaign = $this->RmCommon->filterEmptyField($value, 'MailchimpCampaign', 'content_campaign');
				$mailchimp_list_id = $this->RmCommon->filterEmptyField($value, 'MailchimpCampaign', 'mailchimp_list_id');
				$user_id = $this->RmCommon->filterEmptyField($value, 'MailchimpCampaign', 'user_id');
				$id = $this->RmCommon->filterEmptyField($value, 'MailchimpCampaign', 'id');
				$email_from = $this->RmCommon->filterEmptyField($value, 'MailchimpCampaign', 'email_from');

				$content_campaign = $this->RmCommon->replaceCodeDate($content_campaign);
   				
   				$company = $this->User->UserCompany->getMerge(array(), $company_id);
   				$company = $this->User->getMerge($company, $company_id);

   				$value = $this->User->MailchimpCampaign->getMergeList($value, array(
   					'contain' => array(
   						'MailchimpList',
   					),
   				));

				if(!empty($mailchimp_list_id)){
					$to_email = $this->RmNewsletter->get_email_campaign($value);
					
					if(!empty($to_email)){
						$mail_param = array(
		            		'content' => $content_campaign,
							'dataCompany' => $company,
		            		'bcc' => $to_email,
		            		'from' => $email_from
		            	);
		            	
		                $cek = $this->RmCommon->sendEmail(
		                	null,
		                	$to_email,
		                	'netral',
		                	$subject_campaign,
		                	$mail_param
		                );
					}
				}

		    	$this->User->MailchimpCampaign->id = $id;
				$this->User->MailchimpCampaign->set('is_send', 1);

		        if( empty($cek) ){
		    		$this->User->MailchimpCampaign->set('is_error', 1);

		    		$log_msg = sprintf(__('[SEND-CAMPAIGN] Gagal mengirim campaign email dengan ID %s'), $id);
		        	$this->RmCommon->_saveLog($log_msg, $value, $id, true, 307);
		    	}

		    	if(!$this->User->MailchimpCampaign->save()){
		    		$log_msg = sprintf(__('[SEND-CAMPAIGN]] Gagal melakukan save data status campaign email dengan ID %s'), $id);
		        	$this->RmCommon->_saveLog($log_msg, $value, $id, true, 305);
		    	}
			}
			
			echo 'Berhasil di kirim';
		} else {
			echo 'Campaign tidak tersedia';
		}
	}

	/*
	*	*|AGENT_NAME|* = untuk membuat nama agen PIC Klien
	*	*|AGENT_PHOTO|* = untuk membuat foto agen PIC Klien
	*/
	function birthday(){
		$users = $this->User->UserClient->getData('all', array(
			'conditions' => array(
				"DATE_FORMAT(UserClient.birthday, '%m-%d')" => date('m-d'),
				'UserClient.is_get_birthday_email' => 1,
			),
		), array(
			'status' => 'active',
			'company' => false,
		));

		if(!empty($users)){
			foreach ($users as $key => $value) {
				$user_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'user_id');

				$value = $this->User->getMerge($value, $user_id);

				$company_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'company_id');
				$Client = $this->RmCommon->filterEmptyField($value, 'User');
				$client_name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');
				$client_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$email = $this->RmCommon->filterEmptyField($value, 'User', 'email');

   				$company = $this->User->UserCompany->getMerge(array(), $company_id);
   				$company = $this->User->getMerge($company, $company_id);
   				$company = $this->User->getMergeList($company, array(
					'contain' => array(
						'UserCompanyConfig',
					),
				));

				$content = '';
				if(!empty($company['UserCompanyConfig']['id'])){
					$template = $this->User->UserCompanyConfig->MailchimpTemplate->getData('first', array(
						'conditions' => array(
							'MailchimpTemplate.user_company_config_id' => $company['UserCompanyConfig']['id'],
							'MailchimpTemplate.type_template' => 'birthday',
							'MailchimpTemplate.is_primary_birthday' => 1
						)
					));

					if (!empty($template['MailchimpTemplate']['template_content'])) {
						$content = $template['MailchimpTemplate']['template_content'];
					}
				}

				$template = 'birthday';
				$layout = 'default';
            	if(!empty($content)){
            		$template = 'netral';
            		$layout = false;
            	}

            	$mail_param = array(
                	'content' => $content,
                	'dataClient' => $Client,
                	'layout' => $layout,
                	'with_greet' => false,
					'dataCompany' => $company,
					'headerDomain' => 'company',
            	);
            	
                $cek = $this->RmCommon->sendEmail(
                	$client_name,
                	$email,
                	$template,
                	sprintf(__('Selamat Ulang Tahun %s'), ucwords($client_name)),
                	$mail_param
                );

                if(!$cek){
                	$log_msg = sprintf(__('[SEND-BIRTHDAY]] Gagal mengirim ucapan ulang tahun ke email %s'), $email);
		        	$this->RmCommon->_saveLog($log_msg, $value, $user_id, true, 307);
                }
			}
		}

		echo 'berhasil kirim email ulang tahun';
		return false;
	}

	function generate_ebrosur($principle_id = 11667){
		$users = $this->User->getData('list', array(
			'conditions' => array(
				'OR' => array(
					'User.parent_id' => $principle_id,
					'User.id' => $principle_id
				)
			)
		));

		if(!empty($users)){
			$UserCompanyEbrochure = $this->User->UserCompanyEbrochure->getData('all', array(
				'conditions' => array(
					'UserCompanyEbrochure.user_id' => $users,
					'UserCompanyEbrochure.is_generate_photo' => 0
				),
				'fields' => array(
					'UserCompanyEbrochure.ebrosur_photo'
				),
				'limit' => 100
			));
			
			if(!empty($UserCompanyEbrochure)){
				foreach ($UserCompanyEbrochure as $key => $value) {
					$ebrosur_photo = $this->RmCommon->filterEmptyField($value, 'UserCompanyEbrochure', 'ebrosur_photo');
					$id = $this->RmCommon->filterEmptyField($value, 'UserCompanyEbrochure', 'id');

					$set_data['UserCompanyEbrochure']['is_generate_photo'] = 1;

					$image_name = $this->RmImage->copy_image_to_uploads($ebrosur_photo);
					
					if(!empty($ebrosur_photo) && !empty($image_name)){
						$set_data['UserCompanyEbrochure']['ebrosur_photo'] = $image_name;
					}

					$this->User->UserCompanyEbrochure->id = $id;
					$this->User->UserCompanyEbrochure->set('is_generate_photo', 1);
					$this->User->UserCompanyEbrochure->save();
				}
			}
		}
	}

   	function inactive_listings(){
   		$user['User'] = $this->Auth->user();
   		$expired_day = Configure::read('__Site.config_expired_listing_in_year');
		$options = array(
			'conditions' => array(
				'FLOOR(DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), DATE_FORMAT(Property.change_date, \'%Y-%m-%d\'))/365) >=' => $expired_day,
			),
			'limit' => 10,
		);

   		$tempProperties = $properties = $this->User->Property->getData('all', $options, array(
   			'company' => false,
		));
		$properties = $this->User->Property->getDataList($properties, array(
			'contain' => array(
				'User',
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
			),
		));

   		if( !empty($properties) ) {
   			$users = array();
   			$flagUpdate = true;

   			foreach ($properties as $id => $value) {
   				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
   				$property_id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');
   				$mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');
   				$change_date = $this->RmCommon->filterEmptyField($value, 'Property', 'change_date');

   				$change_date = $this->RmCommon->formatDate($change_date, 'Y-m-d');

   				$company = $this->User->UserCompany->getMerge(array(), $parent_id);
   				$company = $this->User->getMerge($company, $parent_id);

   				$email = $this->RmCommon->filterEmptyField($value, 'User', 'email');
   				$full_name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');

   				if( !empty($email) ) {
					if( !$this->User->Property->updateAll(array(
						'Property.status' => 0,
						'Property.inactive' => 1,
						'Property.inactive_date' => "'".date('Y-m-d H:i:s')."'"
					), array(
						'Property.id' => $property_id,
					))) {
   						$flagUpdate = false;
						$msg = sprintf(__('Gagal mengubah status properti menjadi inactive karena tidak terupdate/refresh selama %s hari dan mengirimkan email pemberitahuan'), $expired_day);
						$this->RmCommon->_saveLog( sprintf(__('%s %s'), $msg, $mls_id));
					} else {
						$msg = sprintf(__('Berhasil mengubah status properti menjadi inactive karena tidak terupdate/refresh selama %s hari dan mengirimkan email pemberitahuan'), $expired_day);
						$this->RmCommon->_saveLog( sprintf(__('%s %s'), $msg, $mls_id));
					}
					
					$users[$email]['SendEmail'] = array(
						'to_name' => $full_name,
						'to_email' => $email,
					);

					if( !empty($users[$email]['mls_id']) ) {
						$lenMlsId = count($users[$email]['mls_id']);
					} else {
						$lenMlsId = 0;
					}

					if( $lenMlsId < 3 ) {
						$users[$email]['mls_id'][] = $mls_id;
					}

					$users[$email]['format_mls_id'][] = sprintf('#%s - Last Update %s', $mls_id, $change_date);
					$users[$email]['Property'][] = $value;
				}
	   		}

	   		if( empty($flagUpdate) ) {
				echo __('Gagal mengubah status properti menjadi inactive');
	   		} else if( !empty($users) ) {
	   			$sendEmail = array();
	   			$tmp_mls_id = false;

	   			foreach ($users as $key => $value) {
   					$dataMail = $this->RmCommon->filterEmptyField($value, 'SendEmail');
   					$mls_id = $this->RmCommon->filterEmptyField($value, 'mls_id');
   					$format_mls_id = $this->RmCommon->filterEmptyField($value, 'format_mls_id');
   					$dataProperty['Property'] = $this->RmCommon->filterEmptyField($value, 'Property');

   					if( !empty($mls_id) ) {
   						$mls_id = implode(', ', $mls_id);
   					}
   					if( !empty($format_mls_id) ) {
   						$tmp_mls_id .= ', '.implode(', ', $format_mls_id);
   					}

   					if( !empty($dataProperty['Property']) && count($dataProperty['Property']) >= 5 ) {
   						$mls_id .= __(', dan beberapa properti lainnya');
   					}

	   				$sendEmail['SendEmail'][] = array_merge($dataMail, array(
	   					'template' => 'inactive_listings',
	   					'subject' => sprintf(__('Properti Anda dengan ID: %s telah Kami non-aktifkan, dikarenakan selama %s hari tidak ada update/refresh terhadap properti tersebut.'), $mls_id, $expired_day),
	   					'data' => array_merge($dataProperty, array(
	   						'expired_day' => $expired_day,
   							'dataCompany' => $company,
   						)),
   					));
	   			}

	   			if( !empty($sendEmail) ) {
					if( $this->RmCommon->validateEmail($sendEmail) ) {
						$msg = sprintf(__('Berhasil mengubah status properti menjadi inactive karena tidak terupdate/refresh selama %s hari dan mengirimkan email pemberitahuan'), $expired_day);
						$this->RmCommon->_saveLog( sprintf(__('%s %s'), $msg, $tmp_mls_id));
					} else {
						$msg = sprintf(__('Gagal mengirimkan email pemberitahuan properti tidak terupdate/refresh selama %s hari'), $expired_day);
						$this->RmCommon->_saveLog( sprintf(__('%s %s'), $msg, $tmp_mls_id), false, false, 1);
			   		}
				} else {
					$msg = __('Tidak ada email untuk dikirimkan pemberitahuan properti inactive');
					$this->RmCommon->_saveLog( sprintf(__('%s %s'), $msg, $tmp_mls_id), false, false, 1);
		   		}
	   		} else {
				$msg = __('Tidak ada email untuk dikirimkan pemberitahuan properti inactive');
	   		}
	   	} else {
			$msg = sprintf(__('Properti tidak terupdate/refresh selama %s hari tidak ditemukan'), $expired_day);
		}

		echo $msg;
   		die();
   	}
	
   	function notification_inactive_listings(){
   		$expired_day = Configure::read('__Site.config_expired_listing') - 7;
		$options = array(
			'conditions' => array(
				'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), DATE_FORMAT(Property.change_date, \'%Y-%m-%d\'))' => $expired_day,
			),
			'group' => array(
				'Property.user_id',
			),
			'limit' => 200,
		);

   		$properties = $this->User->Property->getData('all', $options, array(
   			'company' => false,
		));
		$properties = $this->User->Property->getDataList($properties, array(
			'contain' => array(
				'User',
			),
		));

   		if( !empty($properties) ) {
   			$sendEmail = array();
   			$notifications = array();

   			foreach ($properties as $id => $value) {
   				$user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
   				$email = $this->RmCommon->filterEmptyField($value, 'User', 'email');
   				$full_name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');

   				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
   				$company = $this->User->UserCompany->getMerge(array(), $parent_id);
   				$company = $this->User->getMerge($company, $parent_id);

   				if( !empty($email) ) {
			   		$properties = $this->User->Property->getData('all', array(
						'conditions' => array(
							'Property.user_id' => $user_id,
							'DATEDIFF(DATE_FORMAT(NOW(), \'%Y-%m-%d\'), DATE_FORMAT(Property.change_date, \'%Y-%m-%d\'))' => $expired_day,
						),
						'limit' => 3,
					), array(
			   			'company' => false,
					));
					$properties = $this->User->Property->getDataList($properties, array(
						'contain' => array(
							'User',
							'MergeDefault',
							'PropertyAddress',
							'PropertyAsset',
						),
					));
					
					$titleNotif = sprintf(__('Properti Anda tidak terupdate/refresh selama %s hari'), $expired_day);
					$sendEmail['SendEmail'][] = array(
						'to_name' => $full_name,
						'to_email' => $email,
	   					'template' => 'notification_inactive_listings',
	   					'subject' => $titleNotif,
	   					'data' => array(
	   						'Property' => $properties,
	   						'expired_day' => $expired_day,
   							'dataCompany' => $company,
   						),
					);
   					$notifications['Notification'] = array(
                        'user_id' => $user_id,
                        'name' => $titleNotif,
                        'link' => array(
                            'controller' => 'properties',
                            'action' => 'index',
            				'status' => 'incoming-inactive',
                            'admin' => true,
                        ),
                    );
				}
	   		}

   			if( !empty($sendEmail) || !empty($notifications) ) {
				if( $this->RmCommon->validateEmail($sendEmail) && $this->RmCommon->_saveNotification($notifications) ) {
					echo __('Berhasil mengirimkan notifikasi dan email pemberitahuan');
				} else {
					echo __('Gagal mengirimkan email pemberitahuan');
		   		}
			} else {
				echo __('Tidak ada email untuk dikirimkan pemberitahuan');
	   		}
	   	} else {
			printf(__('Properti tidak terupdate/refresh selama %s hari tidak ditemukan'), $expired_day);
		}

   		die();
   	}
	
   	function notification_expired_rent_property(){
   		$expired_day = Configure::read('__Site.config_expired_rent');
		$options = array(
			'conditions' => array(
                'DATEDIFF(DATE_FORMAT(PropertySold.end_date, \'%Y-%m-%d\'), DATE_FORMAT(NOW(), \'%Y-%m-%d\'))' => $expired_day,
                // 'Property.mls_id' => 'VY9M0T41',
			),
			'contain' => array(
				'PropertySold',
			),
			'group' => array(
				'Property.user_id',
			),
			// 'limit' => 1,
		);

   		$properties = $this->User->Property->getData('all', $options, array(
   			'company' => false,
   			'status' => 'active-pending-sold',
		));
		$properties = $this->User->Property->getDataList($properties, array(
			'contain' => array(
				'User',
			),
		));

   		if( !empty($properties) ) {
   			$sendEmail = array();
   			$notifications = array();

   			foreach ($properties as $id => $value) {
   				$user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
   				$email = $this->RmCommon->filterEmptyField($value, 'User', 'email');
   				$full_name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');

   				$parent_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
   				$company = $this->User->UserCompany->getMerge(array(), $parent_id);
   				$company = $this->User->getMerge($company, $parent_id);

   				if( !empty($email) ) {
			   		$properties = $this->User->Property->getData('all', array(
						'conditions' => array(
							'Property.user_id' => $user_id,
							'Property.property_action_id' => 2,
               				'DATEDIFF(DATE_FORMAT(PropertySold.end_date, \'%Y-%m-%d\'), DATE_FORMAT(NOW(), \'%Y-%m-%d\'))' => $expired_day,
						),
						'contain' => array(
							'PropertySold',
						),
						'limit' => 3,
					), array(
			   			'company' => false,
						'status' => 'active-pending-sold',
					));
					$properties = $this->User->Property->getDataList($properties, array(
						'contain' => array(
							'User',
							'MergeDefault',
							'PropertyAddress',
							'PropertyAsset',
						),
					));

					$titleNotif = sprintf(__('Masa sewa Properti Anda akan segera berakhir'), $expired_day);
					$sendEmail['SendEmail'][] = array(
						'to_name' => $full_name,
						'to_email' => $email,
	   					'template' => 'notification_expired_rent_property',
	   					'subject' => $titleNotif,
	   					'data' => array(
	   						'Property' => $properties,
	   						'expired_day' => $expired_day,
   							'dataCompany' => $company,
   						),
					);
   					$notifications['Notification'] = array(
                        'user_id' => $user_id,
                        'name' => $titleNotif,
                        'link' => array(
                            'controller' => 'properties',
                            'action' => 'index',
            				'status' => 'incoming-rent',
                            'admin' => true,
                        ),
                    );
				}
	   		}

   			if( !empty($sendEmail) || !empty($notifications) ) {
				if( $this->RmCommon->validateEmail($sendEmail) && $this->RmCommon->_saveNotification($notifications) ) {
					echo __('Berhasil mengirimkan notifikasi dan email pemberitahuan');
				} else {
					echo __('Gagal mengirimkan email pemberitahuan');
		   		}
			} else {
				echo __('Tidak ada email untuk dikirimkan pemberitahuan');
	   		}
	   	} else {
			printf(__('Properti tidak terupdate/refresh selama %s hari tidak ditemukan'), $expired_day);
		}

   		die();
   	}

   	function generate_clients () {
   		$this->loadModel('PropertyIpaAsset');
		Configure::write('debug', 2);

   		$values = $this->PropertyIpaAsset->find('all', array(
   			'conditions' => array(
   				array(
   					'PropertyIpaAsset.client_name <>' => ''
				),
   				array(
   					'PropertyIpaAsset.client_name <>' => '-'
				),
			),
			'order' => array(
				'PropertyIpaAsset.id' => 'ASC',
			),
			'offset' => 0,
			'limit' => 500,
		));

   		if( !empty($values) ) {
   			foreach ($values as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'PropertyIpaAsset', 'id');
				$name = $this->RmCommon->filterEmptyField($value, 'PropertyIpaAsset', 'client_name');
				$phone = $this->RmCommon->filterEmptyField($value, 'PropertyIpaAsset', 'client_phone');
				$property_id = $this->RmCommon->filterEmptyField($value, 'PropertyIpaAsset', 'property_id');
				$phone = trim($phone);

				$value = $this->User->Property->getMerge($value, $property_id);
				$agent_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id');

				$first_name = $this->RmUser->_callSplitName($name, 'first_name');
				$last_name = $this->RmUser->_callSplitName($name, 'last_name');

				if( empty($name['last_name']) ) {
					$nameEmail = $this->RmCommon->toSlug(sprintf('%s %s', $first_name, $id), false, '_');
				} else {
					$nameEmail = $this->RmCommon->toSlug($name, false, '_');
				}

				$checkClient = $this->User->UserProfile->getData('first', array(
					'conditions' => array(
						'UserProfile.no_hp' => $phone,
						'User.group_id' => 10,
					),
					'contain' => array(
						'User',
					),
				));

				if( empty($checkClient) ) {
					$email = sprintf('%s@dummy.com', $nameEmail);
					$dataUser = array(
						'User' => array(
							'client_type_id' => 2,
							'group_id' => 10,
							'code' => $this->RmUser->_generateCode('user_code'),
							'email' => $email,
							'first_name' => $first_name,
							'last_name' => $last_name,
							'password' => $this->RmUser->_generateCode('user_code', false, 6),
						),
					);

					if( !empty($dataUser) ) {
						$user = $this->User->find('first', array(
							'conditions' => array(
								'User.email' => $email,
							),
							'contain' => array(
								'UserProfile',
								'UserClient',
							),
						));

						if( !empty($user) ) {
							$user_id = $this->RmCommon->filterEmptyField($user, 'User', 'id');
							$user_profile_id = $this->RmCommon->filterEmptyField($user, 'UserProfile', 'id');
							$user_client_id = $this->RmCommon->filterEmptyField($user, 'UserClient', 'id');
						} else {
							$this->User->create();

							if( $this->User->save($dataUser) ) {
								$user_id = $this->User->id;
							}
						}

						if( !empty($user_id) ) {
							if( empty($user_profile_id) ) {
								$dataUserProfile = array(
									'UserProfile' => array(
										'user_id' => $user_id,
										'no_hp' => $phone,
									),
								);
								
								$this->User->UserProfile->create();
								$this->User->UserProfile->save($dataUserProfile);
							}

							if( empty($user_client_id) ) {
								$dataUserClient = array(
									'UserClient' => array(
										'company_id' => 11667,
										'user_id' => $user_id,
										'agent_id' => $agent_id,
										'first_name' => $first_name,
										'last_name' => $last_name,
										'no_hp' => $phone,
									),
								);

								$this->User->UserClient->create();
								if( $this->User->UserClient->save($dataUserClient) ) {
									$this->User->Property->updateAll(array(
										'Property.client_id' => $user_id,
									), array(
										'Property.id' => $property_id,
									));
								}
							}

							debug($user_id);
						}
					}
				} else {
					$user_id = $this->RmCommon->filterEmptyField($checkClient, 'User', 'id');
					$this->User->Property->updateAll(array(
						'Property.client_id' => $user_id,
					), array(
						'Property.id' => $property_id,
					));
				}
   			}
   		}

   		die();
   	}

   	function generate_user_profiles($parent_id, $limit = 1000){
   		$users = $this->User->getData('list', array(
   			'conditions' => array(
   				'User.parent_id' => $parent_id
   			)
   		), array(
   			'status' => 'all'
   		));

   		if(!empty($users)){
   			
   			array_push($users, $parent_id);

   			$user_profiles = $this->User->UserProfile->getData('all', array(
				'conditions' => array(
					'UserProfile.sync' => 0,
					'UserProfile.user_id' => $users,
				),
				'limit' => $limit
			));

			if(!empty($user_profiles)){
				foreach ($user_profiles as $key => $value) {
					$user_id = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'user_id');
					$id = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'id');

					$value = $this->User->UserConfig->getMerge($value, $user_id);
					$language = $this->RmCommon->filterEmptyField($value, 'UserConfig', 'languages');
					$user_property_types = $this->RmCommon->filterEmptyField($value, 'UserConfig', 'user_property_types');
					$client_types = $this->RmCommon->filterEmptyField($value, 'UserConfig', 'client_types');
					$certifications = $this->RmCommon->filterEmptyField($value, 'UserConfig', 'certifications');
					$other_certifications = $this->RmCommon->filterEmptyField($value, 'UserConfig', 'other_certifications');
					$specialists = $this->RmCommon->filterEmptyField($value, 'UserConfig', 'specialists');

					$language = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'languages', $language);
					$user_property_types = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'user_property_types', $user_property_types);
					$client_types = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'client_types', $client_types);
					$certifications = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'certifications', $certifications);
					$other_certifications = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'other_certifications', $other_certifications);
					$specialists = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'specialists', $specialists);

					if(!empty($language)){
						$language = explode(',', $language);
						$temp = array();

						foreach ($language as $key => $value) {
							$lang = trim(strtolower($value));
							$language_que = $this->User->UserLanguage->Language->find('first', array(
								'conditions' => array(
									'LOWER(Language.name) LIKE' => '%'.$lang.'%'
								)
							));

							if(!empty($language_que['Language']['id'])){
								$temp[$key]['UserLanguage']['language_id'] = $language_que['Language']['id'];
							}else{
								$temp[$key]['UserLanguage']['language_id'] = -1;
								$temp[$key]['UserLanguage']['other_text'] = $lang;
							}

							$temp[$key]['UserLanguage']['user_id'] = $user_id;
						}
						
						if(!empty($temp)){
							$this->User->UserLanguage->doSave($temp, false, $user_id);
						}
					}
					
					if(!empty($user_property_types)){
						$user_property_types = explode(',', $user_property_types);
						$temp = array();

						foreach ($user_property_types as $key => $value) {
							$lang = trim(strtolower($value));

							if($lang == 'ruko atau rukan'){
								$lang = 'ruko';
							}

							$property_type = $this->User->UserPropertyType->PropertyType->find('first', array(
								'conditions' => array(
									'LOWER(PropertyType.name) LIKE' => '%'.$lang.'%'
								)
							));

							if(!empty($property_type['PropertyType']['id'])){
								$temp[$key]['UserPropertyType']['property_type_id'] = $property_type['PropertyType']['id'];
								$temp[$key]['UserPropertyType']['user_id'] = $user_id;
							}
						}
						
						if(!empty($temp)){
							$this->User->UserPropertyType->doSave($temp, false, $user_id);
						}
					}

					if(!empty($client_types)){
						$client_types = explode(',', $client_types);
						$temp = array();

						foreach ($client_types as $key => $value) {
							$lang = trim(strtolower($value));

							if(in_array($lang, array('pembeli properti', 'penyewa'))){
								$lang = 'pembeli/penyewa properti';
							}

							$client_type = $this->User->UserClientType->ClientType->find('first', array(
								'conditions' => array(
									'LOWER(ClientType.name) LIKE' => '%'.$lang.'%'
								)
							));

							if(!empty($client_type['ClientType']['id'])){
								$temp[$key]['UserClientType']['client_type_id'] = $client_type['ClientType']['id'];
								$temp[$key]['UserClientType']['user_id'] = $user_id;
							}
						}
						
						if(!empty($temp)){
							$this->User->UserClientType->doSave($temp, false, $user_id);
						}
					}

					if(!empty($certifications) || !empty($other_certifications)){
						if(!empty($certifications)){
							$certifications = explode(',', $certifications);
							$temp = array();
							
							foreach ($certifications as $key => $value) {
								$lang = trim(strtolower($value));

								$agent_certification = $this->User->UserAgentCertificate->AgentCertificate->find('first', array(
									'conditions' => array(
										'LOWER(AgentCertificate.name) LIKE' => '%'.$lang.'%'
									)
								));

								if(!empty($agent_certification['AgentCertificate']['id'])){
									$temp[$key]['UserAgentCertificate']['agent_certificate_id'] = $agent_certification['AgentCertificate']['id'];
									$temp[$key]['UserAgentCertificate']['user_id'] = $user_id;
								}
							}
							
							if(!empty($temp)){
								$this->User->UserAgentCertificate->doSave($temp, false, $user_id);
							}
						}

						if(!empty($other_certifications)){
							$temp['UserAgentCertificate']['agent_certificate_id'] = -1;
							$temp['UserAgentCertificate']['other_certifications'] = $other_certifications;
							$temp['UserAgentCertificate']['user_id'] = $user_id;

							$this->User->UserAgentCertificate->create();
							$this->User->UserAgentCertificate->set($temp);
							$this->User->UserAgentCertificate->save();
						}
					}

					if(!empty($specialists)){
						$specialists = explode(',', $specialists);
						$temp = array();

						foreach ($specialists as $key => $value) {
							$lang = trim(strtolower($value));

							$Specialist = $this->User->UserSpecialist->Specialist->find('first', array(
								'conditions' => array(
									'LOWER(Specialist.name) LIKE' => '%'.$lang.'%'
								)
							));
							
							if(!empty($Specialist['Specialist']['id'])){
								$temp[$key]['UserSpecialist']['specialist_id'] = $Specialist['Specialist']['id'];
								$temp[$key]['UserSpecialist']['user_id'] = $user_id;
							}
						}
						
						if(!empty($temp)){
							$this->User->UserSpecialist->doSave($temp, false, $user_id);
						}
					}

					$this->User->UserProfile->id = $id;
					$this->User->UserProfile->set('sync', 1);
					$this->User->UserProfile->save();
				}
			}
   		}
   	}

   	function generate_property_clients () {
   		$this->loadModel('PropertyIpaAsset');

   		$properties = $this->PropertyIpaAsset->find('all', array(
   			'conditions' => array(
				array(
					'PropertyIpaAsset.client_phone <>' => '',
				),
				array(
					'PropertyIpaAsset.client_phone <>' => '-',
				),
			),
			'offset' => 2000,
			'limit' => 500,
   		));


   		if( !empty($properties) ) {
   			foreach ($properties as $key => $value) {
				$property_id = $this->RmCommon->filterEmptyField($value, 'PropertyIpaAsset', 'property_id');
				$client_phone = $this->RmCommon->filterEmptyField($value, 'PropertyIpaAsset', 'client_phone');
				$client_phone = trim($client_phone);
				
				$client = $this->User->UserProfile->getData('first', array(
					'conditions' => array(
						'UserProfile.no_hp' => $client_phone,
					),
				));
				$user_id = $this->RmCommon->filterEmptyField($client, 'UserProfile', 'user_id');

				if( !empty($user_id) ) {
					$this->User->Property->set('client_id', $user_id);
					$this->User->Property->id = $property_id;
					$this->User->Property->save();

					var_dump($property_id);
				}
   			}
   		}

   		die();
   	}

   	function remove_agent($id_target, $data_sync){
   		$user = $this->User->getData('first', array(
			'conditions' => array(
				'User.id' => $id_target
			)
		), array(
			'status' => 'deleted'
		));

		$result = array();
		
		$data = array();

		if(!empty($user)){
			if(!empty($user['User']['user_id_target'])){
				$parent_id = $this->RmCommon->filterEmptyField($user, 'User', 'parent_id');
				$email = $this->RmCommon->filterEmptyField($user, 'User', 'email');

				$parent = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $parent_id
					)
				), array(
					'status' => 'active'
				));

				$data['UserRemoveAgent']['parent_id'] = $this->RmCommon->filterEmptyField($parent, 'User', 'user_id_target');
				$data['UserRemoveAgent']['reason'] = $this->RmCommon->filterEmptyField($data_sync, 'UserRemoveAgent', 'reason_principle');
				$data['UserRemoveAgent']['user_id'] = $this->RmCommon->filterEmptyField($user, 'User', 'user_id_target');
				$data['UserRemoveAgent']['email'] = $email;

				$data_api = $this->RumahkuApi->api_access($data, 'remove_agent');
				
				if(!empty($data_api)){
					$data_api = json_decode($data_api, true);
					$data_api = $data_api['data'];
					
					if( !empty($data_api['status']) ){
						$result = array(
							'msg' => __('Berhasil menghapus agen'),
							'status' => true,
							'code_error' => 200
						);
					}else{
						$result = $data_api;

						$result['code_error'] = 306;
					}
				}else{
					$result = array(
						'msg' => __('api tidak bisa di akses'),
						'status' => false,
						'code_error' => 304
					);
				}
			}else if(!empty($user['User']['id'])){
				$result_user = $this->create_user($user['User']['id']);

				$user_id = $this->RmCommon->filterEmptyField($result_user, 'id');

				if(!empty($user_id)){
					$this->remove_agent($id_target, $data_sync);
				}
			}
			
		} else {
			$result = array(
				'msg' => __('data user tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-REMOVE-AGENT] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $data, $id_target, $result['status'], $result['code_error']);
		}

		return $result;
   	}

   	function photo_primary($id_target, $data_sync){
   		$result = array();

   		$media = array();
   		
   		if(!empty($id_target)){
   			$media = $this->User->Property->PropertyMedias->getData('first', array(
   				'conditions' => array(
   					'PropertyMedias.id' => $id_target
   				)
   			), array(
   				'status' => 'all'
   			));
   			
   			if(!empty($media)){
   				$property_id = $this->RmCommon->filterEmptyField($media, 'PropertyMedias', 'property_id');

   				$media = $this->User->Property->getMerge($media, $property_id);

   				$user_data = $this->RumahkuApi->api_access($media, 'photo_primary');
				
				if(!empty($user_data)){
					$user_data = json_decode($user_data, true);
					$user_data = $user_data['data'];
					
					if( !empty($user_data['status']) ){
						$result = array(
							'msg' => __('Berhasil menjadikan foto utama properti'),
							'status' => true,
							'code_error' => 200
						);
					}else{
						$result = $user_data;

						$result['code_error'] = 306;
					}
				}else{
					$result = array(
		   				'msg' => __('api tidak bisa di akses'),
		   				'status' => false,
		   				'code_error' => 304
		   			);
				}
   			}else{
   				$result = array(
	   				'msg' => __('media tidak ditemukan'),
	   				'status' => false,
	   				'code_error' => 302
	   			);
   			}
   		}else{
   			$result = array(
   				'msg' => __('id target tidak ditemukan'),
   				'status' => false,
   				'code_error' => 301
   			);
   		}

   		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-PHOTO-PRIMARY] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $media, $id_target, $result['status'], $result['code_error']);
		}

   		return $result;
   	}

   	function direct_update_property($property_id){
   		$data = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $property_id,
			)
		), array(
			'admin_mine' => false,
			'sold_mine' => false,
			'company' => false
		));

		$data = $this->User->Property->getDataList($data, array(
			'contain' => array(
				'MergeDefault',
				'PropertyAddress',
				'PropertyAsset',
				'PropertySold',
				'PropertyMedias',
				'PropertyVideos',
				'PropertyPrice',
				'User'
			),
		));

		$user_id_target = $this->RmCommon->filterEmptyField($data, 'User', 'user_id_target');
		$property_id_target = $this->RmCommon->filterEmptyField($data, 'Property', 'property_id_target');

		$result = array();
		if(!empty($data) && !empty($user_id_target) && !empty($property_id_target)){
			$data = $this->RmProperty->setConvertSetToV3($data);
			
			if(!empty($data['User'])){
				unset($data['User']);
			}

			$arr = array(
                'PropertyAddress',
                'PropertyAsset',
                'PropertySold'
            );

            foreach ($arr as $key => $value) {
                if(!empty($data[$value]['id'])){
                    
                    unset($data[$value]['id']);

                    $data[$value]['property_id'] = $property_id_target;
                }
            }

			$data['Property']['user_id'] = $user_id_target;
			$data['Property']['id'] = $property_id_target;
			
			$data_api = $this->RumahkuApi->api_access($data, 'direct_update_property');
			
			if(!empty($data_api)){
				$data_api = json_decode($data_api, true);
				$data_api = $data_api['data'];

				if(!empty($data_api['status'])){
					$result = array(
						'msg' => __('Berhasil melakukan direct update'),
						'status' => true,
						'code_error' => 200
					);
				}else{
					$result = $data_api;

					$result['code_error'] = 306;
				}
			}else{
				$result = array(
					'msg' => 'tidak bisa akses api',
					'status' => false,
					'code_error' => 304
				);
			}
		}else{

			$cek_property = true;
			if(empty($property_id_target)){
				$result_property = $this->add_property($property_id);
				
				$property_id_result = $this->RmCommon->filterEmptyField($result_property, 'id');

				$cek_property = !empty($property_id_result) ? true : false;
			}
			
			$cek_user = true;
			if(empty($user_id_target)){
				$result_user = $this->create_user($user_id);

				$user_id = $this->RmCommon->filterEmptyField($result_user, 'id');

				$cek_user = !empty($user_id) ? true : false;
			}

			if(!empty($cek_property) && !empty($cek_user)){
				$this->direct_update_property($property_id);
			}else{
				$result = array(
					'msg' => 'data properti tidak ditemukan',
					'status' => false,
					'code_error' => 301
				);
			}
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-DIRECT-UPDATE-PROPERTY] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $data, $property_id, $result['status'], $result['code_error']);
		}

		return $result;
   	}

   	/*
	* $type_generate di gunakan untuk action terhadap properti tersebut, terdapat beberapa value
	*
	* - all : untuk menambahkan properti lalu memasukkan update ke pending properti update
	* - add : untuk menambahkan properti
	* - edit : untuk menambahkan data update ke pending properti update
	* - direct-update : menambah lalu mengupdate secara langsung tanpa masuk ke pending update
   	*/
   	function generate_property($type_generate = 'all', $limit = 2){
   		$date_from = '2015-01-01';
   		$date_to = date('Y-m-d');

   		$default_conditions = array(
			'DATE_FORMAT(Property.created, \'%Y-%m-%d\') >=' => $date_from,
			'DATE_FORMAT(Property.created, \'%Y-%m-%d\') <=' => $date_to,
			'Property.user_id' => 41877,
		);

		switch ($type_generate) {
			case 'edit':
				$default_conditions['Property.in_update'] = 1;
			break;
			case 'add':
				$default_conditions['Property.property_id_target'] = null;
			break;
		}

   		$properties = $this->User->Property->getData('all', array(
   			'conditions' => $default_conditions,
   			'fields' => array(
   				'Property.id', 
   				'Property.in_update',
   				'Property.property_id_target',
   				'Property.user_id'
   			),
   			'order' => array(
   				'Property.id' => 'ASC'
   			),
   			'limit' => $limit
   		), array(
   			'status' => 'all',
   			'company' => false
   		));
   		
   		if(!empty($properties)){
   			$properties = $this->User->Property->getDataList($properties, array(
				'contain' => array(
					'User'
				),
			));

   			$error_listing = array();
   			foreach ($properties as $key => $val) {
   				$property_id = $this->RmCommon->filterEmptyField($val, 'Property', 'id');
   				$property_id_target = $this->RmCommon->filterEmptyField($val, 'Property', 'property_id_target');
   				$user_id_target = $this->RmCommon->filterEmptyField($val, 'User', 'user_id_target');
   				$user_id = $this->RmCommon->filterEmptyField($val, 'Property', 'user_id');
   				$in_update = $this->RmCommon->filterEmptyField($val, 'Property', 'in_update');

   				if(empty($property_id_target)){
   					$result_property = $this->add_property($property_id);
					
					$property_id_result = $this->RmCommon->filterEmptyField($result_property, 'id');
					$msg_result = $this->RmCommon->filterEmptyField($result_property, 'msg', false, ' - ');
					$status_result = $this->RmCommon->filterEmptyField($result_property, 'status');

   					$cek = $this->add_property($property_id);

	   				if(!$status_result){
	   					$error_listing[] = sprintf('%s : %s', $property_id, $msg_result);
	   				}
   				}

   				if($type_generate == 'direct-update'){
   					if(empty($user_id_target)){
   						$this->create_user($user_id);
   					}

   					$this->direct_update_property($property_id);
   				}

				if($in_update){
					$property = $this->User->Property->property_fix($property_id);
					$cek = $this->property_revision($property_id, $property);

					if(is_array($cek) && $cek['status'] == false){
						$msg = !empty($cek['msg']) ? $cek['msg'] : ' - ';
						$error_listing[] = sprintf('%s : %s', $property_id, $msg);
					}
   				}
   			}

   			if(!empty($error_listing)){
   				printf('properti yang tidak bisa di tambahkan');
   				echo "\n";

   				foreach ($error_listing as $key => $value) {
   					echo $value."\n";
   				}
   			}else{
   				echo 'Berhasil memasukkan properti';
   			}
   		}else{
   			echo 'properti tidak ditemukan';
   		}

   		$this->layout = false;
   		$this->render(false);
   	}

//	send renewal notification to rumahku support team
//	runtime : daily at 00:00
	public function renewal_notification(){
		$records = $this->User->UserCompanyConfig->getData('all', array(
			'conditions' => array(
				'OR' => array(
					'UserCompanyConfig.end_date = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 MONTH),"%Y-%m-%d")', 
					'UserCompanyConfig.end_date = DATE_FORMAT(DATE_ADD(NOW(), INTERVAL 1 WEEK),"%Y-%m-%d")', 
				)
			)
		));

		if($records){
			$this->loadModel('MembershipPackage');

			$errorCounter = 0;
			foreach($records as $key => &$record){
				$userID		= $this->RmCommon->filterEmptyField($record, 'UserCompanyConfig', 'user_id');
				$PICSalesID	= $this->RmCommon->filterEmptyField($record, 'UserCompanyConfig', 'pic_sales_id');
				$user		= $this->User->getData('first', array('conditions' => array('User.id' => $userID)));
				$PICSales	= $this->User->getData('first', array('conditions' => array('User.id' => $PICSalesID)));
				$PICSales	= $this->RmCommon->filterEmptyField($PICSales, 'User');
				$packageID	= $this->RmCommon->filterEmptyField($record, 'UserCompanyConfig', 'membership_package_id');
				$package	= $this->MembershipPackage->getData('first', array('conditions' => array('MembershipPackage.id' => $packageID)));

				$record	= array_merge($user, $record, array('PICSales' => $PICSales), $package);
				$record	= $this->User->UserProfile->getMerge($record, $userID);
				$record	= $this->User->UserCompany->getMerge($record, $userID);

			//	send email to customer support
				$debugMode = Configure::read('debug');
				if($debugMode > 0){
					$email = array(
						'supportrumahku@yopmail.com', 
						'anggarumahku@yopmail.com', 
						'wulanrumahku@yopmail.com', 
						'rikarumahku@yopmail.com'
					);
				}
				else{
					$email = Configure::read('__Site.send_email_from');
				}

				$fullName	= __('Support %s', Configure::read('__Site.site_name'));
				$subject	= 'Reminder Paket Membership Pro';
				$template	= 'renewal_notification_support';
				$emailData	= array_merge(
					$record, 
					array(
					//	'debug' => 'view'
					)
				);

				$isSent = $this->RmCommon->sendEmail($fullName, $email, $template, $subject, $emailData);
				if(!$isSent){
					$errorCounter++;
				}
			}

			if($errorCounter){
				echo(sprintf('Gagal mengirim Email sebanyak : %s kali<br>', $errorCounter));
			}
		}

		$this->layout		= FALSE;
   		$this->autoRender	= FALSE;

		echo('Proses selesai.<br>');
		echo(sprintf('Total Company : %s.', count($records)));
	}

//	check invoice payment status to doku
//	runtime : hourly
	public function check_payment_status(){
		$this->loadModel('Payment');
		$waitingInvoices = $this->Payment->getData('all', array(
			'conditions' => array(
				'Payment.payment_status' => 'waiting', 
			)
		));

		if($waitingInvoices){
			$mallID				= Configure::read('__Site.doku_mall_id');
			$sharedKey			= Configure::read('__Site.doku_shared_key');
			$checkPaymentURL	= Configure::read('__Site.doku_check_payment_url');
			$chainMerchant		= 'NA';
			$errorCounter		= 0;

			foreach($waitingInvoices as $key => $waitingInvoice){
				$invoiceNumber	= $this->RmCommon->filterEmptyField($waitingInvoice, 'Payment', 'invoice_number');
				$sessionID		= $this->RmCommon->filterEmptyField($waitingInvoice, 'Payment', 'session_id');
				$secretWords	= sha1($mallID.$sharedKey.$invoiceNumber);
				$postData		= array(
					'MALLID'			=> $mallID, 
					'CHAINMERCHANT'		=> $chainMerchant, 
					'TRANSIDMERCHANT'	=> $invoiceNumber,  
					'SESSIONID'			=> $sessionID, 
					'WORDS'				=> $secretWords
				);

				$result	= $this->curl_request($checkPaymentURL, $postData);
				$data	= $this->RmCommon->filterEmptyField($result, 'data');
				$flag_failed = false;

				if($data){
				//	data value is xml, we must format it first to array
					App::uses('Xml', 'Utility');
					$data = Xml::toArray(Xml::build($data));

				//	response code : 0000 > success, 5509 > expired
					$responseCode	= $this->RmCommon->filterEmptyField($data, 'PAYMENT_STATUS', 'RESPONSECODE');
					$responseMsg	= $this->RmCommon->filterEmptyField($data, 'PAYMENT_STATUS', 'RESULTMSG');

					$invoiceID		= $this->RmCommon->filterEmptyField($waitingInvoice, 'Payment', 'id');
					$fullName		= $this->RmCommon->filterEmptyField($waitingInvoice, 'User', 'full_name');
					$email			= $this->RmCommon->filterEmptyField($waitingInvoice, 'User', 'email');
					$principleEmail	= $this->RmCommon->filterEmptyField($waitingInvoice, 'Principle', 'email');
					$sendEmail		= false;

					if(in_array($responseCode, array('0000', '5509'))){
					//	update invoice status
						
						$newStatus		= $responseCode == '0000' ? 'paid' : 'expired';
						$waitingInvoice	= $this->Payment->setPaymentStatus($waitingInvoice, $newStatus);

						if($responseCode == '0000'){
							$result		= $this->Payment->processPaidInvoice($waitingInvoice);
							$status		= $this->RmCommon->filterEmptyField($result, 'status', null, 'error');
							$message	= $this->RmCommon->filterEmptyField($result, 'msg');

						//	kirim email ke user makasih udah membayar ===============================================

							$subject		= 'Informasi pembayaran transaksi';
							$template		= 'paid_invoice_notification';

							$financeEmail	= Configure::read('Global.Data.finance_email');
							$senderEmail	= Configure::read('__Site.send_email_from');
							$params			= array_merge($waitingInvoice, array(
								'from'	=> $senderEmail, 
								'bcc'	=> array(
									$financeEmail, 
									$principleEmail, 
								), 
							//	'debug'	=> 'view', 
							));

							$emailSent = $this->RmCommon->sendEmail($fullName, $email, $template, $subject, $params);

						//	=========================================================================================
						}
						else{
							$sendEmail	= true;
							$message	= 'NA';
						}
					}
				//	else if($responseCode == '5516'){
					else{
						$flag_failed = true;
					}

				//	process debugging
				//	echo(sprintf('Invoice : %s.<br>', $invoiceNumber));
				//	echo(sprintf('Response Code : %s.<br>', $responseCode));
				//	echo(sprintf('Response Message : %s.<br>', $responseMsg));
				//	echo(sprintf('Update Config : %s.<hr>', $message));
				} else {
					$flag_failed = true;
				}

				if( !empty($flag_failed) ) {
				//	transaction not found, di sistem kecatat, di doku ga ada record, set expired 1 x 24 jam dari expired date dokumen
					$expiredDate = $this->RmCommon->filterEmptyField($waitingInvoice, 'Payment', 'expired_date');
					$expiredDate = date('Y-m-d', strtotime(sprintf('%s +1 day', $expiredDate)));
					$currentDate = date('Y-m-d');

					if(strtotime($currentDate) > strtotime($expiredDate)){
						$newStatus		= 'expired';
						$waitingInvoice	= $this->Payment->setPaymentStatus($waitingInvoice, $newStatus);
						$sendEmail		= true;
					}

					$message = 'NA';
				}

				if($sendEmail){
					$subject	= 'Notifikasi Paket Membership Pro';
					$template	= 'membership_expired';
					$params		= array_merge($waitingInvoice, array(
						'document_type'	=> 'invoice', 
					//	'debug'			=> 'view', 
					));

					$isSent = $this->RmCommon->sendEmail($fullName, $email, $template, $subject, $params);
					if(!$isSent){
						$errorCounter++;
					}
				}
			}

			if($errorCounter){
				echo(sprintf('Gagal mengirim Email sebanyak : %s kali<br>', $errorCounter));
			}
		}

		$this->layout		= false;
   		$this->autoRender	= false;

		echo('Proses selesai.<br>');
		echo(sprintf('Total Invoice (Waiting) : %s.', count($waitingInvoices)));
	}

//	http / https post method and get return value
	function curl_request($strTargetURL = NULL, $arrData = NULL, $intTimeout = 60){
		$result = NULL;

		if($strTargetURL && $arrData){
			$ch = curl_init();

			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_URL, $strTargetURL);
			curl_setopt($ch, CURLOPT_SSLVERSION, 3); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arrData, '', '&'));

			$request = curl_exec($ch);
			curl_close($ch);

			$result = array('status' => 'success', 'msg' => __('Valid Request'), 'data' => $request);
		}
		else{
			$result = array('status' => 'error', 'msg' => __('Invalid Request'));
		}

		return $result;
	}

//	filter expired membership order / invoice document
//	runtime : daily at 00:00
	public function filter_expired_document($documentType = 'request'){
		if(in_array($documentType, array('request', 'invoice'))){
			if($documentType == 'request'){
				$modelName	= 'MembershipOrder';
				$updateData	= array('status' => "'expired'");
			}
			else{
				$modelName	= 'Payment';
				$updateData	= array('payment_status' => "'expired'");
			}

			$this->loadModel($modelName);
			$expiredDocuments = $this->$modelName->filterExpiredDocument();

			if($expiredDocuments){
				$id		= Set::extract(sprintf('/%s/id', $modelName), $expiredDocuments);
				$update	= $this->$modelName->updateAll($updateData, array($modelName.'.id' => $id));

				if($update){
				//	send notification email to users
					$errorCounter = 0;
					foreach($expiredDocuments as $key => $expiredDocument){
						$firstName		= $this->RmCommon->filterEmptyField($expiredDocument, 'User', 'first_name');
						$lastName		= $this->RmCommon->filterEmptyField($expiredDocument, 'User', 'last_name');
						$fullName		= trim(sprintf('%s %s', $firstName, $lastName));
						$email			= $this->RmCommon->filterEmptyField($expiredDocument, 'User', 'email');
						$subject		= 'Notifikasi Paket Membership Pro';
						$template		= 'membership_expired';
						$additionalData	= array(
							'document_type'	=> $documentType, 
						//	'debug'			=> 'view'
						);

						$data	= array_merge($expiredDocument, $additionalData);
						$isSent	= $this->RmCommon->sendEmail($fullName, $email, $template, $subject, $data);

						if(!$isSent){
							$errorCounter++;
						}
					}

					echo('Sukses<br><br>');
					if($errorCounter){
						echo(sprintf('Gagal mengirim Email sebanyak : %s kali<br><br>', $errorCounter));
					}
				}
				else{
					echo('Gagal<br><br>');
				}
			}
			else{
				echo('Dokumen tidak ditemukan.<br><br>');
			}
		}
		else{
			echo('Jenis dokumen tidak valid.<br><br>');
		}

		$this->layout		= FALSE;
		$this->autoRender	= FALSE;
	}

	function property_refresh($id_target, $data_sync){
		$data = array();
		$property_id = false;
		
		if(!empty($data_sync)){
			$status 				= $this->RmCommon->filterEmptyField($data_sync, 'status');
			$user_id 				= $this->RmCommon->filterEmptyField($data_sync, 'user_id');
			$property_id_target 	= $this->RmCommon->filterEmptyField($data_sync, 'property_id_target');
			$property_id 			= $this->RmCommon->filterEmptyField($data_sync, 'property_id');
			$parent_id 				= $this->RmCommon->filterEmptyField($data_sync, 'parent_id');

			$data = array(
				'status' => $status
			);
			$sent = true;
			if(empty($property_id_target) && !empty($property_id)){
				$cek = $this->add_property($property_id);

				if(!empty($cek) && !is_array($cek)){
					$data['property_id'] = $cek;
				}else{
					$sent = false;
				}
			}else if(!empty($property_id_target)){
				$data['property_id'] = $property_id_target;
			}

			if(!empty($user_id)){
				$user = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $user_id
					)
				));

				$group_id 		= $this->RmCommon->filterEmptyField($user, 'User', 'group_id');
				$user_id_target = $this->RmCommon->filterEmptyField($user, 'User', 'user_id_target');

				if($group_id != 2){
					$data['admin'] = true;
					
					if($group_id != 3){
						$user = $this->User->getData('first', array(
							'conditions' => array(
								'User.id' => $parent_id
							)
						));

						$user_id_target = $this->RmCommon->filterEmptyField($user, 'User', 'user_id_target');
						$user_id = $this->RmCommon->filterEmptyField($user, 'User', 'id');
					}
				}

				if(!empty($user_id_target)){
					$data['user_id'] = $user_id_target;
				}else{
					$result_user = $this->create_user($user_id);

					$user_id = $this->RmCommon->filterEmptyField($result_user, 'id');
			
					if(!empty($user_id)){
						$data['user_id'] = $user_id;
					}else{
						$sent = false;
					}
				}
			}

			if($sent){
				$data_api = $this->RumahkuApi->api_access($data, 'property_refresh');
				
				if(!empty($data_api)){
					$data_api = json_decode($data_api, true);
					$data_api = $data_api['data'];

					if(!empty($data_api['status'])){
						$result = array(
							'msg' => __('Berhasil melakukan refresh properti'),
							'status' => true,
							'code_error' => 200
						);
					}else{
						$result = $data_api;

						$result['code_error'] = 306;
					}
				}else{
					$result = array(
						'msg' => 'tidak bisa akses api',
						'status' => false,
						'code_error' => 304
					);
				}
			}else{
				$result = array(
					'msg' => 'Data tidak valid',
					'status' => false,
					'code_error' => 307
				);
			}
				
		}else{
			$result = array(
				'msg' => 'data properti tidak ditemukan',
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-PROPERTY-REFRESH] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $data, $property_id, $result['status'], $result['code_error']);
		}

		return $result;
	}

	function migrateAgent ( $parent_id = 0 ) {
		$this->autoLayout = false;
		$this->autoRender = false;

		$this->loadModel('KutaUser');
		$this->loadModel('SyncError');

		$parent_email = 'admin@raywhitekuta.com';
		$pathPhoto = APP.'webroot'.DS.'img'.DS.'raywhitekuta'.DS.'agent';
		$propertyFolder = Configure::read('__Site.profile_photo_folder');

		$values = $this->KutaUser->find('all', array(
			'conditions' => array(
				'KutaUser.role_id' => 3,
			),
			// 'limit' => 5,
		));

		$this->User->UserProfile->validator()
	        ->remove('address')
	        ->remove('zip')
	        ->remove('no_hp')
	        ->remove('no_hp_2');

		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$value = $this->RmCommon->dataConverter($value, array(
					'phone' => array(
						'KutaUser' => array(
							'phone',
						),
					),
				));

				$marketingid = $this->RmCommon->filterEmptyField($value, 'KutaUser', 'id');
				$username = $this->RmCommon->filterEmptyField($value, 'KutaUser', 'username');
				$nama = $this->RmCommon->filterEmptyField($value, 'KutaUser', 'name');
				$email = $this->RmCommon->filterEmptyField($value, 'KutaUser', 'email');
				$pwd = '123456raywhitekuta';

				$photofile = $this->RmCommon->filterEmptyField($value, 'KutaUser', 'image');
				$address = $this->RmCommon->filterEmptyField($value, 'KutaUser', 'address');
				$phone = $this->RmCommon->filterEmptyField($value, 'KutaUser', 'phone', '');
				
				$value = $this->User->getMerge($value, $email, false, 'User', 'User.email');
				$user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');

				$phones = explode('/', $phone);
				$no_hp = !empty($phones[0])?trim($phones[0]):false;
				$no_hp_2 = !empty($phones[1])?trim($phones[1]):false;

				if( empty($user_id) ) {
					$data = array(
						'User' => array(
							'password' => $pwd,
							'username' => $username,
							'email' => $email,
							'full_name' => $nama,
							'parent_email' => $parent_email,
							'rku_user_id' => $marketingid,
						),
						'UserProfile' => array(
							'address' => $address,
							'no_hp' => $no_hp,
							'no_hp_2' => $no_hp_2,
						),
					);
				} else {
					$data = array(
						'User' => array(
							'parent_id' => $parent_id,
							'group_id' => 2,
							'username' => $username,
							'full_name' => $nama,
							'parent_email' => $parent_email,
							'rku_user_id' => $marketingid,
						),
						'UserProfile' => array(
							'address' => $address,
							'no_hp' => $no_hp,
							'no_hp_2' => $no_hp_2,
						),
					);
				}

				if( !empty($photofile) ) {
					$picPath = sprintf('%s/%s', $pathPhoto, $photofile);
					$prefixImage = String::uuid();

					$picData = $this->RmImage->upload_by_path($picPath, $propertyFolder, $prefixImage);
					$imageName = $this->RmCommon->filterEmptyField($picData, 'imageName');

					$data['User']['photo'] = $imageName;
				}

				$data = $this->RmUser->_callDataRegister($data);
				
				if( empty($user_id) ) {
					$result = $this->User->doAdd( $data, $parent_id, false, 2, false );
					$id = $this->RmCommon->filterEmptyField($result, 'id');
				} else {
					$result = $this->User->doEdit( $user_id, $value, $data );
					$id = $user_id;
				}

				$status = $this->RmCommon->filterEmptyField($result, 'status');
				$validationErrors = $this->RmCommon->filterEmptyField($result, 'validationErrors');

				if( $status == 'error' ) {
					$this->SyncError->create();
					$this->SyncError->set('module', 'create_user');
					$this->SyncError->set('id_target', $username);
					$this->SyncError->save();
					
					printf(__('Gagal menambahkan agent %s <br>'), $username);

					if( !empty($validationErrors)) {
						var_dump($validationErrors);
						printf(__('<br>END agent %s <br>'), $username);
					}

					echo '<br>';
				} else if( $status == 'success' && !empty($id) ){
					printf(__('Berhasil menambahkan agent %s <br><br>'), $username);
				}
			}
		} else {
			echo __('Agen tidak tersedia');
		}

		die();
	}

	function migrateProperty () {
		$this->autoLayout = false;
		$this->autoRender = false;
		$is_admin = Configure::read('User.admin');

		// if( !empty($is_admin) ) {
			$this->loadModel('KutaProperty');
			$this->loadModel('KutaPropertyText');
			$this->loadModel('KutaUser');
			// $this->loadModel('LotpropertyListing');
			$this->loadModel('SyncError');

			$this->User->Property->validator()
	        ->remove('property_type_id')
	        ->remove('price')
	        ->remove('title')
	        ->remove('description');

	        $this->User->Property->PropertyAsset->validator()
	        ->remove('lot_size')
	        ->remove('building_size')
	        ->remove('beds')
	        ->remove('beds_maid')
	        ->remove('baths')
	        ->remove('baths_maid');

	        $this->User->Property->PropertyAddress->validator()
	        ->remove('address')
	        ->remove('region_id')
	        ->remove('city_id')
	        ->remove('subarea_id')
	        ->remove('zip');

			$pathPhoto = APP.'webroot'.DS.'img'.DS.'raywhitekuta'.DS.'listing';
			$propertyFolder = Configure::read('__Site.property_photo_folder');
			$values = $this->KutaProperty->find('all', array(
				'conditions' => array(
					// 'KutaProperty.id' => 450,
					'KutaPropertyCategoryRelation.category_id <>' => NULL,
					// 'LotpropertyListing.arahhadap !=' => '',
				),
				'contain' => array(
					'KutaPropertyCategoryRelation',
					'KutaPropertyAgent',
				),
				'order' => array(
					'KutaProperty.id' => 'ASC',
				),
				'offset' => 385,
				'limit' => 250,
			));

			if( !empty($values) ) {
				foreach ($values as $key => $value) {
					$code = $this->RmCommon->createRandomNumber( 3, 'bcdfghjklmnprstvwxyz0123456789', 30);
					$listingid = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'id');
					$mktg = $this->RmCommon->filterEmptyField($value, 'KutaPropertyAgent', 'agent_id');
					$category_id = $this->RmCommon->filterEmptyField($value, 'KutaPropertyCategoryRelation', 'category_id');

					$value = $this->KutaPropertyText->getMerge($value, $listingid);
					$value = $this->KutaProperty->KutaPropertyCategoryRelation->KutaPropertyCategoryText->getMerge($value, $category_id);

					$harga = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'true_price');
					$harga = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'true_rent_price', $harga);
					$type_id = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'type_id');

					if( in_array($type_id, array( 2, 10 )) ) {
						$property_action_id = 2;
					} else {
						$property_action_id = 1;
					}

					$jdl = $this->RmCommon->filterEmptyField($value, 'KutaPropertyText', 'name');
					$ket = $this->RmCommon->filterEmptyField($value, 'KutaPropertyText', 'description', false, false);

					$lt = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'land_area');
					$lb = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'build_area');

					$jumlahlantai = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'floor');

					$kt = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'bedrooms', 0);
					$km = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'bathrooms', 0);

					$garasi = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'garage');
					$dayalistrik = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'electricity', 0);
					$jalurtelepon = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'phoneline');
					$sold = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'is_sold');
					
					$now = date('Y-m-d');
					debug('phase 1');

					$value = $this->KutaUser->getMerge($value, $mktg);
					$email = $this->RmCommon->filterEmptyField($value, 'KutaUser', 'email');
					
					$value = $this->User->getMerge($value, $email, false, 'User', 'User.email');
					$user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
					$user_code = $this->RmCommon->filterEmptyField($value, 'User', 'code');

					$value = $this->RmMigrate->_callMigrateKuta($value);
					debug('phase 2');

					$propertyType = $this->RmCommon->filterEmptyField($value, 'PropertyType');
					$property_type_id = $this->RmCommon->filterEmptyField($value, 'PropertyType', 'id');
					$property_type_name = $this->RmCommon->filterEmptyField($value, 'PropertyType', 'name');
					$property_action_name = $this->RmCommon->filterEmptyField($value, 'PropertyAction', 'name');

					$region_id = $this->RmCommon->filterEmptyField($value, 'Region', 'id');
					$city_id = $this->RmCommon->filterEmptyField($value, 'City', 'id');
					$latitude = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'latitude');
					$longitude = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'longitude');
					$location = sprintf('%s,%s', $latitude, $longitude);

					$city = $this->RmCommon->filterEmptyField($value, 'City', 'name');
					$region = $this->RmCommon->filterEmptyField($value, 'Region', 'name');

					$session_id = String::uuid();
					$primaryPhoto = false;
					$mls_id = $this->User->Property->generateMLSID($code, $user_code);
					// $photos = $this->LotpropertyListingphoto->find('all', array(
					// 	'conditions' => array(
					// 		'LotpropertyListingphoto.listingid' => $listingid,
					// 		'LotpropertyListingphoto.status' => true,
					// 	),
					// ));

					if( !empty($harga) ) {
						$price_measure = $this->RmProperty->getMeasurePrice($value);
						$value['Property']['price'] = $harga;
					} else {
						$price_measure = 0;
						$value['Property']['price'] = 0;
					}

					if( empty($property_type_id) || empty($harga) ) {
						$status = 0;
						$active = 0;
					} else {
						$status = 1;
						$active = 1;
					}
					debug('phase 3');

					$dataImg = array();
					$data = array(
						'Property' => array(
							'user_id' => $user_id,
							'mls_id' => $mls_id,
							'property_action_id' => $property_action_id,
							'property_type_id' => $property_type_id,
							'rku_property_id' => $listingid,
							'title' => $jdl,
							'description' => $ket,
							'price' => $harga,
							'publish_date' => $now,
							'change_date' => $now,
							'active' => $active,
							'status' => $status,
							'session_id' => $session_id,
							'created' => $now,
							'price_measure' => $price_measure,
							// 'photo' => '/2016/03/1/56de816b-12b0-484c-87dc-0a3c65ca98e3.jpg',
							'keyword' => sprintf('%s, %s %s di %s, %s', $jdl, $property_type_name, $property_action_name, $city, $region),
						),
						'PropertyAsset' => array(
							'building_size' => $lb,
							'lot_size' => $lt,
							'beds' => $kt,
							'baths' => $km,
							'cars' => $garasi,
							'electricity' => $dayalistrik,
							'phoneline' => $jalurtelepon,
						),
						'PropertyAddress' => array(
							'region_id' => $region_id,
							'city_id' => $city_id,
							'address' => $city,
							'location' => $location,
							'latitude' => $latitude,
							'longitude' => $longitude,
							'hide_address' => true,
						),
						'PropertyType' => $propertyType,
					);

	debug($data);
					$basic = $this->User->Property->doBasic( $data );
					$property_id = !empty($basic['id'])?$basic['id']:false;
					debug($property_id);

					if( !empty($property_id) ) {
						$data['PropertyAddress']['property_id'] = $property_id;
						$data['PropertyAsset']['property_id'] = $property_id;
					}

					$address = $this->User->Property->PropertyAddress->doAddress( $data, false, false, $property_id );
					debug('Address');
					$asset = $this->User->Property->PropertyAsset->doSave( $data, false, false, $property_id );
					debug('Asset');
					// debug($this->User->Property->validationErrors);die();

					// if( !empty($photos) ) {
					// 	foreach ($photos as $key => $photo) {
					// 		$pic = $this->RmCommon->filterEmptyField($photo, 'LotpropertyListingphoto', 'filename');
					// 		$primary = $this->RmCommon->filterEmptyField($photo, 'LotpropertyListingphoto', 'primary');
					// 		$caption = $this->RmCommon->filterEmptyField($photo, 'LotpropertyListingphoto', 'caption');

					// 		if( !empty($pic) ) {
					// 			$picPath = sprintf('%s/%s', $pathPhoto, $pic);
					// 			$prefixImage = String::uuid();

					// 			$picData = $this->RmImage->upload_by_path($picPath, $propertyFolder, $prefixImage);
					// 			$imageName = $this->RmCommon->filterEmptyField($picData, 'imageName');

					// 			if( !empty($primary) ) {
					// 				$primaryPhoto = $imageName;
					// 				$primary = true;
					// 			} else {
					// 				$primary = false;
					// 			}

					// 			$dataImg[]['PropertyMedias'] = array(
					// 				'property_id' => $property_id,
					// 				'session_id' => $session_id,
					// 				'alias' => $pic,
					// 				'title' => $caption,
					// 				'name' => $imageName,
					// 				'primary' => $primary,
					// 				'approved' => 1,
					// 			);
					// 		}
					// 	}
					// }
					
					// for ($i=0; $i <= 5; $i++) { 
					// 	if( empty($i) ) {
					// 		$image = $this->RmCommon->filterEmptyField($value, 'KutaProperty', 'mainimage');
					// 		$primary = 1;
					// 	} else {
					// 		$image = $this->RmCommon->filterEmptyField($value, 'KutaProperty', sprintf('image%s', $i));
					// 		$primary = 0;
					// 	}
					// 	if( !empty($image) ) {
					// 		$picPath = sprintf('%s/%s', $pathPhoto, $image);
					// 		$prefixImage = String::uuid();

					// 		$picData = $this->RmImage->upload_by_path($picPath, $propertyFolder, $prefixImage);
					// 		$imageName = $this->RmCommon->filterEmptyField($picData, 'imageName');

					// 		$dataImg[]['PropertyMedias'] = array(
					// 			'property_id' => $property_id,
					// 			'session_id' => $session_id,
					// 			'alias' => false,
					// 			'title' => false,
					// 			'name' => $imageName,
					// 			'primary' => $primary,
					// 			'approved' => 1,
					// 		);

					// 		if( empty($i) ) {
					// 			$primaryPhoto = $imageName;
					// 		}
					// 	}
					// }

					// if( !empty($dataImg) ) {
					// 	$statusMedias = $this->User->Property->PropertyMedias->saveMany($dataImg);

					// 	if( !empty($primaryPhoto) ) {
					// 		$this->User->Property->id = $property_id;
					// 		$this->User->Property->set('photo', $primaryPhoto);
					// 		$this->User->Property->save();
					// 	}
					// } else {
					// 	$statusMedias = true;
					// }
						$statusMedias = true;

					$statusBasic =!empty($basic['status'])?$basic['status']:'error';
					$statusAddress =!empty($address['status'])?$address['status']:'error';
					$statusAsset =!empty($asset['status'])?$asset['status']:'error';

					if( $statusBasic == 'error' ) {
						$module = 'create_property_basic';
						$title = '';
					} else if ( $statusAddress == 'error' ) {
						$module = 'create_property_address';
						$title = __('Alamat');
					} else if ( $statusAsset == 'error' ) {
						$module = 'create_property_asset';
						$title = __('Spesifikasi');
					} else if( empty($statusMedias) ) {
						$module = 'create_property_medias';
						$title = __('Media');
					}

					if( !empty($module) ) {
						$this->SyncError->create();
						$this->SyncError->set('module', $module);
						$this->SyncError->set('id_target', $listingid);
						$this->SyncError->save();
						
						printf(__('Gagal menambahkan %s property %s <br>'), $title, $listingid);
						debug($mls_id);
						debug($this->User->Property->validationErrors);
						debug($this->User->Property->PropertyAddress->validationErrors);
						debug($this->User->Property->PropertyAsset->validationErrors);

						if( !empty($validationErrors)) {
							var_dump($validationErrors);
							printf(__('<br>END property %s <br>'), $listingid);
						}

						echo '<br>';
						die();
					} else {
						printf(__('Berhasil menambahkan property %s <br><br>'), $listingid);
					}
				}
			} else {
				echo __('Property tidak tersedia');
			}
		// } else {
		// 	echo $this->Auth->authError;
		// }

		die();
	}

	function migrateClientOwner () {
		$this->autoLayout = false;
		$this->autoRender = false;

		$this->loadModel('LotpropertyMarketing');
		$this->loadModel('LotpropertyListing');

		$values = $this->LotpropertyListing->find('all', array(
			'conditions' => array(
				'LotpropertyListing.listingid >' => 1653,
				'LotpropertyListing.ownername !=' => '',
			),
			'order' => array(
				'LotpropertyListing.listingid' => 'ASC',
			),
			'limit' => 100,
			// 'offset' => 4,
		));
		Configure::write('debug', 2);
		// debug($values);die();

        $this->User->UserProfile->validator()->remove('no_hp');
        $this->User->UserClient->validator()->remove('no_hp');
        $company_agent_id = $this->User->getAgents( 104481, true );

		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$listingid = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'listingid');
				$ownername = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'ownername');
				$owneraddress = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'owneraddress');
				$ownerphone = $this->RmCommon->filterEmptyField($value, 'LotpropertyListing', 'ownerphone');
		        
		        $client_email = sprintf('%s.%s', $ownername, $ownerphone);
				$client_email = $this->RmCommon->toSlug($client_email, false, '.');
				$client_email  = sprintf('%s@dummy.com', $client_email);

	            $user_data = $this->User->getData('first', array(
	                'conditions' => array(
	                    'User.email' => $client_email
	                )
	            ), array(
	                'role' => 'client',
	                'status' => 'semi-active',
	            ));
	            $property = $this->User->Property->find('first', array(
	                'conditions' => array(
	                    'Property.property_id_target' => $listingid,
	                    'Property.user_id' => $company_agent_id,
	                ),
	            ));
				$property_id = $this->RmCommon->filterEmptyField($property, 'Property', 'id');
				$agent_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id');
                $client_id = $this->RmCommon->filterEmptyField($user_data, 'User', 'id', '');

                $data['Property']['client_name'] = $ownername;
                $data['Property']['client_hp'] = $ownerphone;
                $data['Property']['company_id'] = 104481;
                $data['Property']['agent_id'] = $agent_id;

	            if( !empty($user_data) ) {
	                $data['Property']['client_email'] = $client_email;
	                $data['Property']['client_id'] = $client_id;
	            } else {
	                $data['Property']['client_id'] = false;
	                $data['Property']['client_email'] = $client_email;
	                $data['Property']['client_password'] = $this->RmUser->_generateCode('password', false, 6);
	                $data['Property']['client_auth_password'] = $this->Auth->password($data['Property']['client_password']);
	                $data['Property']['client_code'] = $this->RmUser->_generateCode('user_code');
	            }

				// $data = $this->User->addClient($data, 'Property', 'client_id', 'agent_id');

				if( !empty($property_id) ) {
					$this->User->Property->set('client_id', $client_id);
					$this->User->Property->id = $property_id;

					$flag = $this->User->Property->save();
				} else {
					$flag = false;
				}
				
				if( !empty($flag) ) {
					printf(__('Gagal menambahkan Klien %s - %s <br><br>'), $client_email, $listingid);
				} else {
					printf(__('Berhasil menambahkan Klien %s - %s <br><br>'), $client_email, $listingid);
				}
			}
			
			die();
		} else {
			echo __('Klien tidak tersedia');
		}

		die();
	}

	function send_email_error_launcher(){
		$users = $this->User->UserCompanyConfig->getData('all', array(
			'order' => array(
				'UserCompanyConfig.id' => 'ASC',
			),
			// 'limit' => 1,
		));
		// Configure::write('debug', 2);
		// debug($users);die();

		if(!empty($users)){
			foreach ($users as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'id');
				$user_id = $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'user_id');
				
				$value = $this->User->getMerge($value, $user_id);
				$value = $this->User->UserCompany->getMerge($value, $user_id);

				$email 	= $this->RmCommon->filterEmptyField($value, 'User', 'email');
				$name 	= $this->RmCommon->filterEmptyField($value, 'UserCompany', 'name');

				$mail_param = array(
					'with_greet' => false,
					'data' => $value,
					'_attachments' => Configure::read('__Site.webroot_files_path').DS.'surat-penjelasan-gangguan-launcher.jpg',
					'logoDefault' => true,
					// 'debug' => 'text'
				);

				if(!empty($email)){
					$this->RmCommon->sendEmail(
                    	$name,
                    	$email,
                    	'send_email_error_launcher',
                    	__('Penjelasan Gangguan Launcher PRIME SYSTEM'),
                    	$mail_param
                    );
				}

				echo __('Terkirim ke %s #%s</br>', $email, $id);
			}

			echo 'Anda telah berhasil mengirimkan email';
		}
	}

	function delete_all_media($property_id_target, $options){
		$result = array();

		$data['model'] = $this->RmCommon->filterEmptyField($options, 'model');

		$data['property_id_target'] = $property_id_target;

		if(!empty($data['model'])){
			$data_api = $this->RumahkuApi->api_access($data, 'delete_all_media');
			
			if(!empty($data_api)){
				$data_api = json_decode($data_api, true);
				$data_api = $data_api['data'];
				
				if(!empty($data_api['status'])){
					$result = array(
						'msg' => __('Berhasil menghapus media'),
						'status' => true,
						'code_error' => 200
					);
				}else{
					$result = $data_api;

					$result['code_error'] = 306;
				}
			}else{
				$result = $data_api;
				$result['code_error'] = 304;
			}
		}else{
			$result = array(
				'msg' => __('model tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-DELETE-ALL-MEDIA] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $data, $property_id_target, $result['status'], $result['code_error']);
		}

		return $result;
	}

	function property_premium($id_target, $options = array()){
		$is_premium = isset($options['is_premium']) ? $options['is_premium'] : true;

		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $id_target,
			)
		), array(
			'admin_mine' => false,
			'sold_mine' => false,
			'company' => false
		));

		$result = array();

		if(!empty($property)){
			$user_id = $this->RmCommon->filterEmptyField($property, 'Property', 'user_id');
			
			$property = $this->User->getMerge($property, $user_id);
			
			$property_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_id_target');
			
			$user_id = $this->RmCommon->filterEmptyField($property, 'User', 'user_id_target');

			$data['property_id'] = $property_id;
			$data['user_id'] = $user_id;
			$data['is_premium'] = $is_premium;
			
			$data_api = $this->RumahkuApi->api_access($data, 'property_premium');
			
			if(!empty($data_api)){
				$data_api = json_decode($data_api, true);
				$data_api = $data_api['data'];
				
				if( !empty($data_api['status']) ){
					$result = array(
						'msg' => __('Berhasil menjadikan premium properti'),
						'status' => true,
						'code_error' => 200
					);
				}else{
					$result = $data_api;

					$result['code_error'] = 306;
				}
			}else{
				$result = array(
					'msg' => __('tidak bisa akses api'),
					'status' => false,
					'code_error' => 304
				);
			}
		}else{
			$result = array(
				'msg' => __('data properti tidak ditemukan'),
				'status' => false,
				'code_error' => 301
			);
		}

		if(!empty($result['msg']) && empty($result['status'])){
			$log_msg = sprintf(__('[SYNC-PROPERTY-PREMIUM] %s'), $result['msg']);
        	$this->RmCommon->_saveLog($log_msg, $property, $id_target, $result['status'], $result['code_error']);
		}

		return $result;
	}

	function restore_photo ( $type = 'properties' ) {
		$params = $this->params;

		switch ($type) {
			case 'advices':
				$this->RmAdvice->_callGeneratePhoto($params);
				break;
			
			default:
				$this->RmProperty->_callGeneratePhoto($params);
				break;
		}

		die();
	}

	function regenerate_photo ( $slug = false ) {
		$this->loadModel('Setting');
		$datetime = date('H:i');
		

		if( $datetime > '21:00' || $datetime < '09:00' ) {
			$params = $this->params;
			$passkey = $this->RmCommon->filterEmptyField($params, 'named', 'passkey');

			$setting = $this->Setting->find('first', array(
				'conditions' => array(
					'Setting.slug' => $slug,
					'Setting.token' => $passkey,
				),
			));
			$created = $this->RmCommon->filterEmptyField($setting, 'Setting', 'value');

			if( !empty($created) ) {
				$setting_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
				$last_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'temp', 0);

				$properties = $this->User->Property->getData('all', array(
					'conditions' => array(
						'DATE_FORMAT(Property.created, \'%Y-%m-%d\') <=' => '2016-06-01',
						'Property.created >' => $created,
						'Property.id >' => $last_id,
					),
					'order' => array(
						'Property.id' => 'ASC',
					),
					'limit' => 30,
				), array(
					'status' => 'all',
					'company' => false,
				));

				if( !empty($properties) ) {
					foreach ($properties as $key => $value) {
						$id = $this->RmCommon->filterEmptyField($value, 'Property', 'id', 0);
						$this->RmProperty->_callGeneratePhoto(array(
							'named' => array(
								'id' => $id,
							),
						), $value);
					}

					$this->Setting->id = $setting_id;
					$this->Setting->set('temp', $id);
					$this->Setting->save();
				} else {
	                printf(__('Regenerate thumbnail telah selesai'));

					$this->Setting->id = $setting_id;
					$this->Setting->set('value', '');
					$this->Setting->save();
				}
			}
		}

		die();
	}

	function restore_recycle_photo () {
		$file_path = '/2016/04/6/570471ff-6d50-4eaa-8ea2-0a9065ca98e3.jpg';
		$savePath = 'properties';
		$result = $this->RmRecycleBin->restore($file_path, $savePath);
		
		Configure::write('debug', 2);
		debug($result);die();
	}

	function change_password () {
		$this->loadModel('Setting');
		App::import('Helper', 'Html');

        $this->Html = new HtmlHelper(new View(null));
		$url = Configure::read('__Site.kpr_url');
		$flag = FALSE;

		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => 'rku-change-password-api',
			),
		));
		$setting_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
		$domain = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
		$passkey = $this->RmCommon->filterEmptyField($setting, 'Setting', 'token');
		$lastupdated = $this->RmCommon->filterEmptyField($setting, 'Setting', 'value');

		$apiUrl = $domain.$this->Html->url(array(
			'controller' => 'Api',
			'action' => 'change_password',
			'?' => array(
				'passkey' => $passkey,
				'lastupdated' => $lastupdated,
			),
			'ext' => 'json',
			'admin' => false,
		));
		$apiUrl = htmlspecialchars_decode($apiUrl);
		$dataApi = @file_get_contents($apiUrl);
		$dataApi = json_decode($dataApi, TRUE);

		$data = $this->RmCommon->filterEmptyField($dataApi, 'data');
		$datas = $this->RmCommon->filterEmptyField($data, 'data');
		$lastupdated_pass = $lastupdated;

		if( !empty($datas) ) {
			$dataSave = array();

			foreach ($datas as $key => $value) {
				$email = $this->RmCommon->filterEmptyField($value, 'User', 'email');
				$password = $this->RmCommon->filterEmptyField($value, 'User', 'password');
				$lastupdated_pass = $this->RmCommon->filterEmptyField($value, 'PasswordReset', 'modified');
				$passwordReset = $this->RmCommon->filterEmptyField($value, 'PasswordReset');

				$value = $this->User->getMerge($value, $email, false, 'UserSync', 'User.email');
				$user_id = $this->RmCommon->filterEmptyField($value, 'UserSync', 'id');

				if( !empty($user_id) ) {
					$passwordReset = $this->RmCommon->_callUnset(array(
						'id', 
					), $passwordReset);

					$dataSave[] = array(
						'PasswordReset' => array_merge($passwordReset, array(
							'user_id' => $user_id,
						)),
						'User' => array(
							'id' => $user_id,
							'password' => $password,
						),
					);
				}
			}

			if(!empty($dataSave)){
				$flag = $this->User->PasswordReset->saveAll($dataSave, array(
	                'deep' => true,
	            ), false);

				if( $flag ) {
					$this->Setting->set('value', $lastupdated_pass);
					$this->Setting->id = $setting_id;
					$this->Setting->save();

					echo __('Berhasil sync data pergantian password <br>');
				} else {
					echo __('Gagal menyimpan data pergantian password <br>');
				}
			}else{
				echo __('Data Bank tidak ditemukan <br>');
			}
		} else {
			echo __('Data tidak tersedia');
		}
		die();
	}

	function duplicate_property_user($user_id, $limit = 60){
		$this->loadModel('QueueUserDuplicateProperty');
		$this->Property = $this->User->Property;
		
		$data_offset = $this->QueueUserDuplicateProperty->offset_data($user_id);

		if(!empty($data_offset)){
			$offset_property_id = $this->RmCommon->filterEmptyField($data_offset, 'offset_property_id');
			$offset_user_id = $this->RmCommon->filterEmptyField($data_offset, 'offset_user_id');
			$offset_id = $this->RmCommon->filterEmptyField($data_offset, 'id');

			$properties = $this->Property->getData('all', array(
				'conditions' => array(
					'Property.id >' => $offset_property_id,
					'Property.user_id' => $user_id
				),
				'limit' => $limit,
				'order' => array(
					'Property.id' => 'ASC'
				)
			), array(
				'status' => 'all'
			));

			if(!empty($properties)){
				$data_offset_user = $this->User->getData('first', array(
					'conditions' => array(
						'User.id' => $offset_user_id
					)
				), array(
					'role' => 'agent',
					'status' => 'all',
				));

				$userCode = $this->RmCommon->filterEmptyField($data_offset_user, 'User', 'code');

				$properties = $this->Property->getDataList($properties, array(
					'contain' => array(
						'PropertyAddress',
						'PropertyAsset',
						'PropertySold',
						'PropertyFacility',
						'PropertyPointPlus',
						'PropertyPrice',
					),
				));

				$property_id = 0;
				foreach ($properties as $key => $value) {
					$property_id = $this->RmCommon->filterEmptyField($value, 'Property', 'id');

					$value = $this->Property->PropertyMedias->getMerge($value, $property_id, 'all');
					$value = $this->Property->PropertyVideos->getMerge($value, $property_id, 'all');
					
					$new_code = $this->RmUser->createRandomNumber( 3, 'bcdfghjklmnprstvwxyz0123456789', 30);
					$rand_code = $this->RmUser->array_random($new_code, count($new_code));

					$data = $this->RmProperty->convertToDuplicateData($value);

					$data['Property']['user_id'] 	= $offset_user_id;
					$data['Property']['mls_id'] 	= $this->Property->generateMLSID($rand_code, $userCode);
					
					$this->Property->removeValidate();

					if(!$this->Property->saveAll($data)){
						debug($this->Property->validationErrors);
					}

					$this->QueueUserDuplicateProperty->last_offset_property($offset_id, $property_id);
				}

				$this->QueueUserDuplicateProperty->checkPropertySync($offset_id, $user_id, $offset_user_id, $property_id);

				printf(__('berhasil duplicate data properti terakhir %s'), $property_id);
			}else{
				$this->QueueUserDuplicateProperty->complete_offset($offset_id, $user_id, $offset_user_id);

				printf(__('berhasil duplicate data properti untuk user %s'), $offset_user_id);
			}
		}else{
			$this->send_mail_generate('property', __('berhasil duplicate data properti'));
		}
	}

	function migrate_data(){
		$this->loadModel('MigrateCompany');

		$migrate_data = $this->MigrateCompany->getData('first', array(
			'order' => array(
				'MigrateCompany.id' => 'ASC'
			)
		), array(
			'status' => 'process-non-process'
		));

		if(!empty($migrate_data)){
			$this->MigrateCompany->in_proccess($migrate_data);

			$migrate_company_id = $this->RmCommon->filterEmptyField($migrate_data, 'MigrateCompany', 'id');
			$user_id = $this->RmCommon->filterEmptyField($migrate_data, 'MigrateCompany', 'user_id');

			$migrate_data = $this->User->getMerge($migrate_data, $user_id);
			
			$principle_email = $this->RmCommon->filterEmptyField($migrate_data, 'User', 'email');

			$migrate_config = $this->MigrateCompany->MigrateConfigCompany->getData('first', array(
				'conditions' => array(
					'MigrateConfigCompany.migrate_company_id' => $migrate_company_id,
				),
				'order' => array(
					'MigrateConfigCompany.order' => 'ASC'
				)
			), array(
				'status' => 'pending-progress'
			));

			if(!empty($migrate_config)){
				$document_status 	= $this->RmCommon->filterEmptyField($migrate_config, 'MigrateConfigCompany', 'document_status');
				$slug 				= $this->RmCommon->filterEmptyField($migrate_config, 'MigrateConfigCompany', 'slug');
				$config_id 			= $this->RmCommon->filterEmptyField($migrate_config, 'MigrateConfigCompany', 'id');
				$value 				= $this->RmCommon->filterEmptyField($migrate_config, 'MigrateConfigCompany', 'value');
				
				if($document_status == 'pending'){
					$this->MigrateCompany->MigrateConfigCompany->updateStatus($config_id, 'progress');
				}

				$data_api = $this->get_migrate_data_api($slug, $principle_email, $value);

				if(!empty($data_api)){
					switch ($slug) {
						case 'agents':
							$lastupdated = $this->migrate_agent_data($data_api, $migrate_config);
							break;
						case 'properties':
							$lastupdated = $this->migrate_properties_data($data_api, $migrate_config);
							break;
						// case 'ebrosur':
						// 	$lastupdated = $this->migrate_ebrosur_data($data_api, $migrate_config);
						// 	break;
						// case 'berita':
						// 	$lastupdated = $this->migrate_berita_data($data_api, $migrate_config);
						// 	break;
						// case 'karir':
						// 	$lastupdated = $this->migrate_karir_data($data_api, $migrate_config);
						// 	break;
						// case 'banner_developer':
						// 	$lastupdated = $this->migrate_banner_developer_data($data_api, $migrate_config);
						// 	break;
						// case 'banner_home':
						// 	$lastupdated = $this->migrate_banner_home_data($data_api, $migrate_config);
						// 	break;
						// case 'faqs':
						// 	$lastupdated = $this->migrate_faqs_data($data_api, $migrate_config);
						// 	break;
						// case 'partnerships':
						// 	$lastupdated = $this->migrate_partnerships_data($data_api, $migrate_config);
						// 	break;
						case 'messages':
							$lastupdated = $this->migrate_messages_data($data_api, $migrate_config);
							break;
					}

					if(!empty($lastupdated)){
						$this->MigrateCompany->MigrateConfigCompany->updateValue($config_id, $lastupdated);
					}

					$data_api = $this->get_migrate_data_api($slug, $principle_email, $lastupdated);

					if(empty($data_api)){
						$this->MigrateCompany->MigrateConfigCompany->updateStatus($config_id, 'completed');

						$migrate_config = $this->MigrateCompany->MigrateConfigCompany->getData('first', array(
							'conditions' => array(
								'MigrateConfigCompany.migrate_company_id' => $migrate_company_id,
							),
							'order' => array(
								'MigrateConfigCompany.order' => 'ASC'
							)
						), array(
							'status' => 'pending-progress'
						));

						if(empty($migrate_config)){
							$this->MigrateCompany->statusComplete($migrate_company_id);
						}
					}
				}else{
					$this->MigrateCompany->MigrateConfigCompany->updateStatus($config_id, 'completed');
				}
			}else{
				$this->MigrateCompany->statusComplete($migrate_company_id);
			}
		}

		echo __('sync selesai');
	}

	function migrate_agent_data($data, $config){
		$parent_id = $this->RmCommon->filterEmptyField($config, 'MigrateConfigCompany', 'user_id');
		
		if(!empty($data)){

			$lastupdated = '';
			foreach ($data as $key => $value) {
				$value['User']['parent_id'] = $parent_id;

				$lastupdated = $this->RmCommon->filterEmptyField($value, 'User', 'created');

				$this->RmUser->saveApiDataMigrate($value);
			}

			return $lastupdated;
		}else{
			return false;
		}
	}

	function migrate_properties_data($data, $config){
		if(!empty($data)){

			$lastupdated = '';
			foreach ($data as $key => $value) {
				$lastupdated = $this->RmCommon->filterEmptyField($value, 'Property', 'created');

				$this->RmProperty->saveApiDataMigrate($value);
			}

			return $lastupdated;
		}else{
			return false;
		}
	}

	// function migrate_ebrosur_data($data, $config){
	// 	if(!empty($data)){

	// 		$this->RmEbroschure = $this->Components->load('RmEbroschure');

	// 		$lastupdated = '';
	// 		foreach ($data as $key => $value) {
	// 			$lastupdated = $this->RmCommon->filterEmptyField($value, 'PropertyClaimBanner', 'created');
				
	// 			$mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');
	// 			$email 	= $this->RmCommon->filterEmptyField($value, 'User', 'email');
				
	// 			$exist_data_property = $this->User->Property->getData('first', array(
	// 	            'conditions' => array(
	// 	                'Property.mls_id' => $mls_id
	// 	            )
	// 	        ), array(
	// 	            'status' => 'all'
	// 	        ));

	// 	        $exist_data_user = $this->User->getData('first', array(
	// 	            'conditions' => array(
	// 	                'User.email' => $email
	// 	            )
	// 	        ), array(
	// 	            'status' => 'all'
	// 	        ));

	// 	        if(!empty($exist_data_user)){
	// 	        	$user_id 		= $value['PropertyClaimBanner']['user_id'] = $this->RmCommon->filterEmptyField($exist_data_user, 'User', 'id');
	// 	        	$property_id 	= $value['PropertyClaimBanner']['property_id'] = $this->RmCommon->filterEmptyField($exist_data_property, 'Property', 'id');

	// 	        	$value = $this->RmMigrateCompany->setConvertEbrosurToV2($value, $user_id, $property_id);
		        	
	// 				$this->RmEbroschure->saveApiDataMigrate($value);
	// 	        }
	// 		}

	// 		return $lastupdated;
	// 	}else{
	// 		return false;
	// 	}
	// }

	// function migrate_berita_data($data, $config){
	// 	if(!empty($data)){

	// 		$this->RmAdvice = $this->Components->load('RmAdvice');

	// 		$lastupdated = '';
	// 		foreach ($data as $key => $value) {
	// 			$lastupdated = $this->RmCommon->filterEmptyField($value, 'CompanyAdvice', 'created');

	// 			$this->RmAdvice->saveApiDataMigrate($value);
	// 		}

	// 		return $lastupdated;
	// 	}else{
	// 		return false;
	// 	}
	// }

	// function migrate_karir_data($data, $config){
	// 	if(!empty($data)){
	// 		$this->loadModel('Career');

	// 		$user_id = $this->RmCommon->filterEmptyField($config, 'MigrateConfigCompany', 'user_id');

	// 		$lastupdated = '';
	// 		foreach ($data as $key => $value) {
	// 			$lastupdated = $this->RmCommon->filterEmptyField($value, 'CompanyCareer', 'created');

	// 			$value = $this->RmMigrateCompany->setConvertCareerToV2($value, $user_id);

	// 			$this->Career->doSave($value, false, false, true);
	// 		}

	// 		return $lastupdated;
	// 	}else{
	// 		return false;
	// 	}
	// }

	// function migrate_banner_developer_data($data, $config){
	// 	if(!empty($data)){
	// 		$this->loadModel('BannerDeveloper');

	// 		$this->RmImage = $this->Components->load('RmImage');

	// 		$user_id = $this->RmCommon->filterEmptyField($config, 'MigrateConfigCompany', 'user_id');

	// 		$lastupdated = '';
	// 		foreach ($data as $key => $value) {
	// 			$lastupdated = $this->RmCommon->filterEmptyField($value, 'BannerWebPrinciple', 'created');
				
	// 			$value = $this->RmMigrateCompany->setConvertDeveloperToV2($value, $user_id, 'developer');

	// 			$photo = $this->RmCommon->filterEmptyField($value, 'BannerDeveloper', 'photo');

	// 			$image_name = $this->RmImage->copy_image_to_uploads($photo, 'banner_web_principle', Configure::read('__Site.general_folder'), 'photo');

	// 			if(!empty($photo) && !empty($image_name)){
	// 				$value['BannerDeveloper']['photo'] = $image_name;
	// 			}

	// 			$this->BannerDeveloper->doSave($value, false, false, true);
	// 		}

	// 		return $lastupdated;
	// 	}else{
	// 		return false;
	// 	}
	// }

	// function migrate_banner_home_data($data, $config){
	// 	if(!empty($data)){
	// 		$this->loadModel('BannerSlide');

	// 		$this->RmImage = $this->Components->load('RmImage');

	// 		$user_id = $this->RmCommon->filterEmptyField($config, 'MigrateConfigCompany', 'user_id');

	// 		$lastupdated = '';
	// 		foreach ($data as $key => $value) {
	// 			$lastupdated = $this->RmCommon->filterEmptyField($value, 'BannerWebPrinciple', 'created');
				
	// 			$value = $this->RmMigrateCompany->setConvertDeveloperToV2($value, $user_id);

	// 			$photo = $this->RmCommon->filterEmptyField($value, 'BannerSlide', 'photo');

	// 			$image_name = $this->RmImage->copy_image_to_uploads($photo, 'banner_web_principle', Configure::read('__Site.general_folder'), 'photo');
			
	// 			if(!empty($photo) && !empty($image_name)){
	// 				$value['BannerSlide']['photo'] = $image_name;
	// 			}
				
	// 			$this->BannerSlide->doSave($value, false, false, true);
	// 		}

	// 		return $lastupdated;
	// 	}else{
	// 		return false;
	// 	}
	// }

	// function migrate_faqs_data($data, $config){
	// 	if(!empty($data)){
	// 		$this->loadModel('Faq');

	// 		$user_id = $this->RmCommon->filterEmptyField($config, 'MigrateConfigCompany', 'user_id');

	// 		$lastupdated = '';
	// 		foreach ($data as $key => $value) {
	// 			$lastupdated = $this->RmCommon->filterEmptyField($value, 'FaqCompany', 'created');
				
	// 			$value = $this->RmMigrateCompany->setConvertFaqToV2($value, $user_id);

	// 			$name 		= $this->RmCommon->filterEmptyField($value, 'FaqCategory', 'name');
	// 			$user_id 	= $this->RmCommon->filterEmptyField($value, 'FaqCategory', 'user_id');
				
	// 			$faq_category = $this->Faq->FaqCategory->getData('first', array(
	// 				'conditions' => array(
	// 					'FaqCategory.name' => $name,
	// 					'FaqCategory.user_id' => $user_id,
	// 				)
	// 			), array(
	// 				'company' => false
	// 			));
				
	// 			$faq_category_id = $this->RmCommon->filterEmptyField($faq_category, 'FaqCategory', 'id');

	// 			if(empty($faq_category_id)){
	// 				$result = $this->Faq->FaqCategory->doSave($value, false, false, true);

	// 				$faq_category_id = $this->RmCommon->filterEmptyField($result, 'id');
	// 			}

	// 			$value['Faq']['faq_category_id'] = $faq_category_id;

	// 			$this->Faq->doSave($value, false, false, true);
	// 		}

	// 		return $lastupdated;
	// 	}else{
	// 		return false;
	// 	}
	// }

	// function migrate_partnerships_data($data, $config){
	// 	if(!empty($data)){
	// 		$this->loadModel('Partnership');

	// 		$this->RmImage = $this->Components->load('RmImage');

	// 		$user_id = $this->RmCommon->filterEmptyField($config, 'MigrateConfigCompany', 'user_id');

	// 		$lastupdated = '';
	// 		foreach ($data as $key => $value) {
	// 			$lastupdated = $this->RmCommon->filterEmptyField($value, 'PartnerWebPrinciple', 'created');
							
	// 			$value['Partnership'] = $value['PartnerWebPrinciple'];

	// 			$value['Partnership']['company_id'] = $user_id;

	// 			if(isset($value['PartnerWebPrinciple'])){
	// 				unset($value['PartnerWebPrinciple']);
	// 			}

	// 			if(isset($value['Partnership']['id'])){
	// 				unset($value['Partnership']['id']);
	// 			}

	// 			$photo = $this->RmCommon->filterEmptyField($value, 'Partnership', 'photo');

	// 			$image_name = $this->RmImage->copy_image_to_uploads($photo, 'partner_web_principle', Configure::read('__Site.logo_photo_folder'), 'photo');
			
	// 			if(!empty($photo) && !empty($image_name)){
	// 				$value['Partnership']['photo'] = $image_name;
	// 			}
				
	// 			$this->Partnership->doSave($value, false, false, true);
	// 		}

	// 		return $lastupdated;
	// 	}else{
	// 		return false;
	// 	}
	// }

	function migrate_messages_data($data, $config){
		if(!empty($data)){
			$this->loadModel('Partnership');

			$this->RmMessage = $this->Components->load('RmMessage');

			$lastupdated = '';
			foreach ($data as $key => $value) {
				$lastupdated = $this->RmCommon->filterEmptyField($value, 'Message', 'created');
					
				$this->RmMessage->saveApiDataMigrate($value);
			}

			return $lastupdated;
		}else{
			return false;
		}
	}

	function get_migrate_data_api($slug, $principle_email, $value){
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'slug' => 'prime-migrate',
			),
		));

		$setting_id = $this->RmCommon->filterEmptyField($setting, 'Setting', 'id');
		$domain = $this->RmCommon->filterEmptyField($setting, 'Setting', 'link');
		$token = $this->RmCommon->filterEmptyField($setting, 'Setting', 'token');

		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));

		$apiUrl = $domain.$this->Html->url(array(
			'controller' => 'migrates',
			'action' => $slug,
			'?' => array(
				'principle_email' => $principle_email,
				'token' => $token,
				'lastupdated' => $value,
			),
			'ext' => 'json',
			'admin' => false,
			'api' => true,
		));
		
		$apiUrl = htmlspecialchars_decode($apiUrl);
		
		$dataApi = @file_get_contents($apiUrl);
		$dataApi = json_decode($dataApi, TRUE);

		$data = $this->RmCommon->filterEmptyField($dataApi, 'data');
		$data_api = $this->RmCommon->filterEmptyField($data, 'data');

		return $data_api;
	}

	function report_execute( $id = false ){
		$options = array(
			'conditions' => array(
				'Report.on_progress' => 0,
			),
			'order' => array(
				'Report.created' => 'ASC',
				'Report.id' => 'ASC',
			),
			'limit' => 50,
		);

		if( !empty($id) ) {
			$options['conditions']['Report.id'] = $id;
		}

		$values = $this->User->Report->getData('all', $options, array(
			'status' => array(
				'pending', 'progress',
			),
		));
		$msg = false;

		if( !empty($values) ) {
			$reports_id = Set::extract('/Report/id', $values);
			$this->User->Report->updateAll(array(
				'Report.on_progress' => 1,
			), array(
				'Report.id' => $reports_id,
			));

			foreach ($values as $key => $value) {
				$value = $this->User->Report->getMergeList($value, array(
					'contain' => array(
						'ReportQueue' => array(
							'type' => 'first',
						),
						'ReportDetail',
					),
				));

				$report = $this->RmCommon->filterEmptyField($value, 'Report');
				$id = $this->RmCommon->filterEmptyField($report, 'id');
				$prefix = $this->RmCommon->filterEmptyField($report, 'session_id');
				$filename = $this->RmCommon->filterEmptyField($report, 'filename');
				$currency_total_data = $this->RmCommon->filterEmptyField($report, 'total_data');

				$title = $this->RmCommon->filterEmptyField($value, 'Report', 'title');
				$type = $this->RmCommon->filterEmptyField($value, 'Report', 'report_type_id');
				$fetched_data = $this->RmCommon->filterEmptyField($value, 'Report', 'fetched_data');

				$limit = 50;
				$last_id = $this->RmCommon->filterEmptyField($value, 'ReportQueue', 'last_id', 0);
				$report_queue_id = $this->RmCommon->filterEmptyField($value, 'ReportQueue', 'id');
				$previously_fetched_data = $this->RmCommon->filterEmptyField($value, 'ReportQueue', 'fetched_data');

				$params = $this->RmReport->_callDataSearch($value);

				switch ($type) {
					case 'performance':
						$dataReport = $this->RmReport->_callDataPerformance($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( 'UserCompanyConfig', $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					case 'summary':
						$dataReport = $this->RmReport->_callDataSummary($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( 'UserCompany', $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					case 'properties':
						$dataReport = $this->RmReport->_callDataProperties($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( 'Property', $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					case 'visitors':
						$dataReport = $this->RmReport->_callDataVisitors($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( 'PropertyView', $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					case 'messages':
						$dataReport = $this->RmReport->_callDataMessages($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( 'Message', $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					case 'agents':
						$dataReport = $this->RmReport->_callDataAgents($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( 'User', $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					case 'kprs':
						$dataReport = $this->RmReport->_callDataKprs($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( 'KprBank', $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					case 'commissions':
						$dataReport = $this->RmReport->_callDataCommissions($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( 'Property', $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					case 'clients':
						$is_agent = Common::isAgent();

						if( !empty($is_agent) ) {
							$modelName = 'UserClientRelation';
						} else {
							$modelName = 'UserClient';
						}

						$dataReport = $this->RmReport->_callDataClients($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( $modelName, $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					case 'users':
						$dataReport = $this->RmReport->_callDataUsers($params, $fetched_data, $limit);
						$resultReport = $this->RmReport->_callProcess( 'User', $id, $value, $dataReport );
						$result = $this->RmReport->_callSaveDataExport( $title, $value, $dataReport, $resultReport );

						if( !empty($result) ) {
							$msg = __('Berhasil generate %s...<br><br>', $title);
						} else {
							$msg = __('Gagal melakukan generate %s...<br><br>', $title);
						}
						break;
					
					default:
						$msg = __('No report type to be processed...<br><br>');
						break;
				}

				if( empty($id) ) {
					echo $msg;
				}
			}

			if( !empty($id) ) {
				$value = $this->User->Report->getData('first', $options);
				$value = $this->User->Report->getMergeList($value, array(
					'contain' => array(
						'ReportDetail',
					),
				));

				$this->set('value', $value);
				$this->render('/Elements/blocks/reports/detail_download');
			} else {
				echo $msg;
				die();
			}
		} else {
			$msg = __('No report to be processed...<br>');
			echo $msg;
			die();
		}
	}

	function generate_multiple_mls () {
		$this->User->Property->virtualFields['cnt'] = 'COUNT(Property.mls_id)';
		$values = $this->User->Property->getData('all', array(
			'conditions' => array(
				'Property.mls_id <>' => '',
			),
			'group' => array(
				'Property.mls_id HAVING COUNT(Property.mls_id) > 1',
			),
			'limit' => 100,
		), array(
			'status' => 'all',
			'company' => false,
		));

		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');

				unset($this->User->Property->virtualFields['cnt']);
				$mls = $this->User->Property->getData('all', array(
					'conditions' => array(
						'Property.mls_id' => $mls_id,
					),
					'order' => array(
						'Property.change_date' => 'DESC',
						'Property.created' => 'DESC',
					),
				), array(
					'status' => 'all',
					'company' => false,
				));

				if( !empty($mls) ) {
					unset($mls[0]);

					foreach ($mls as $key => $val) {
						$id = $this->RmCommon->filterEmptyField($val, 'Property', 'id');
						$curr_mls_id = $this->RmCommon->filterEmptyField($val, 'Property', 'mls_id');
						$mls_id = substr($curr_mls_id, 1, strlen($curr_mls_id));
						$mls_id = __('%s%s', $key, $mls_id);

						$this->User->Property->set('mls_id', $mls_id);
						$this->User->Property->id = $id;
						$this->User->Property->save();

						echo __('Change %s - %s <br><br>', $curr_mls_id, $mls_id);
					}
				}
			}
		}

		die();
	}

	function _checkExistingAccumulateReport ( $optionExist, $user_company_id, $name ) {
		// $nowDate = date($formatPhp);

		// if( $date == $nowDate ) {
			$optionExist['conditions']['ReportAccumulate.name'] = $name;
			$optionExist['conditions']['ReportAccumulate.user_company_id'] = $user_company_id;
			$existDate = $this->ReportAccumulate->find('first', $optionExist);
			$lastID = Common::hashEmptyField($existDate, 'ReportAccumulate.id');
		// } else {
		// 	$lastID = false,
		// }

		return $lastID;
	}

	function accumulate_report ( $period = 'month', $start_date = NULL, $end_date = NULL ) {
		$this->loadModel('ReportAccumulate');

		switch ($period) {
			case 'daily':
        		$format = '\'%Y-%m-%d\'';
        		$formatPhp = 'Y-m-d';
        		$dateAdd = "day";
				break;

			case 'year':
        		$format = '\'%Y\'';
        		$formatPhp = 'Y';
        		$dateAdd = "years";
				break;
			
			default:
        		$format = '\'%Y-%m\'';
        		$formatPhp = 'Y-m';
        		$dateAdd = "months";
				break;
		}

		$lastDate = $this->ReportAccumulate->find('first', array(
			'conditions' => array(
				'ReportAccumulate.periode' => $period,
				'ReportAccumulate.name LIKE' => 'propert%',
			),
			'order' => array(
				'ReportAccumulate.id' => 'DESC',
			),
		));

		$lastDate = Common::hashEmptyField($lastDate, 'ReportAccumulate.date', false, array(
			'date' => $formatPhp,
		));

		if( empty($lastDate) ) {
			$lastproperty = $this->User->Property->find('first', array(
				'conditions' => array(
					'Property.created NOT' => NULL,
					'Property.created <>' => '0000-00-00 00:00:00',
				),
				'order' => array(
					'Property.id' => 'ASC',
				),
			));
			$nextDate = Common::hashEmptyField($lastproperty, 'Property.created', false, array(
				'date' => $formatPhp,
			));
		} else {
			if( $period == 'year' ) {
				$nextDate = $lastDate + 1;
			} else {
				$nextDate = date($formatPhp, strtotime($lastDate.' +1 '.$dateAdd));
			}

			if( $nextDate > $lastDate ) {
				$nextDate = $lastDate;
			}
		}

		$flag = true;
		$result = array();
		$flagElement = !empty($admin_rumahku)?false:true;

		if( !empty($start_date) ) {
			$nextDate = $start_date;
		}

		$lastDate = $nextDate;

		$this->User->Property->virtualFields['cnt'] = 'COUNT(Property.id)';

		while ($flag) {
			$contentArr = array(
				'date' => $nextDate,
				'periode' => $period,
			);
			$optionExist = array(
				'conditions' => array(
					'ReportAccumulate.periode' => $period,
					'ReportAccumulate.date' => $nextDate,
				),
			);
			$optionsDefault = array(
				'fields' => array(
					'Property.id',
					'Property.report_name',
					'Property.cnt',
					'Property.user_id',
					'Property.principle_id',
				),
				'group' => array(
					'Property.principle_id',
				),
			);

			// Total Properti
			$propetyOptions = array_merge(array(
				'conditions' => array(
					'Property.principle_id <>' => 0,
					'DATE_FORMAT(Property.created, '.$format.') <=' => $nextDate,
				),
			), $optionsDefault);

			$this->User->Property->virtualFields['report_name'] = "'properties'";
			$values = $this->User->Property->find('all', $propetyOptions);
			
			// Monthly Agent
			$this->User->Property->virtualFields['report_name'] = "'property_monthly'";
			$monthly_agent = $this->User->Property->find('all', array_merge(array(
				'conditions' => array(
					'Property.principle_id <>' => 0,
					'DATE_FORMAT(Property.created, '.$format.')' => $nextDate,
				),
			), $optionsDefault));
			$values = array_merge($values, (!empty($monthly_agent)?$monthly_agent:array()));

			// Properti Aktif
			$this->User->Property->virtualFields['report_name'] = "'property_active'";
			$propertyElements = array(
	            'company' => false,
			);
			$property_active = $this->User->Property->getData('all', $propetyOptions, array_merge($propertyElements, array(
				'status' => 'active-pending',
			)));
			$values = array_merge($values, (!empty($property_active)?$property_active:array()));

			// Properti Sold
			$this->User->Property->bindModel(array(
				'hasOne' => array(
					'PropertySold' => array(
						'className' => 'PropertySold',
						'foreignKey' => 'property_id',
						'conditions' => array(
							'PropertySold.sold_by_id = Property.user_id',
							'PropertySold.status' => 1,
						),
					),
				)
			), false);

			$this->User->Property->virtualFields['total_sold'] = 'SUM(PropertySold.price_sold)';
			$this->User->Property->virtualFields['report_name'] = "'property_sold'";
			$soldOptions = array_merge_recursive(array(
				'fields' => array(
					'Property.id',
					'Property.cnt',
					'Property.report_name',
					'Property.user_id',
					'Property.principle_id',
					'Property.total_sold',
				),
				'conditions' => array(
					'DATE_FORMAT(PropertySold.sold_date, '.$format.') <=' => $nextDate,
				),
				'contain' => array(
					'PropertySold',
				),
			), $optionsDefault);
			$property_sold = $this->User->Property->getData('all', $soldOptions, array_merge($propertyElements, array(
				'status' => 'sold',
			)));
			$values = array_merge($values, (!empty($property_sold)?$property_sold:array()));

			unset($this->User->Property->virtualFields['total_sold']);

			if( !empty($values) ) {
				foreach ($values as $key => $value) {
					$cnt = Common::hashEmptyField($value, 'Property.cnt', 0);
					$report_name = Common::hashEmptyField($value, 'Property.report_name');
					$user_id = Common::hashEmptyField($value, 'Property.user_id', 0);
					$user_company_id = Common::hashEmptyField($value, 'Property.principle_id', 0);
					// $group_id = Common::hashEmptyField($value, 'User.group_id', 0);

					// if( in_array($group_id, array( 3,4 )) ) {
					// 	$user_company_id = $user_id;
					// }

					$result[]['ReportAccumulate'] = array_merge($contentArr, array(
						// 'id' => $this->_checkExistingAccumulateReport($optionExist, $user_company_id, $report_name),
						'user_company_id' => $user_company_id,
						'name' => $report_name,
						'value' => $cnt,
					), $contentArr);

					switch ($report_name) {
						case 'property_sold':
							$price_sold = Common::hashEmptyField($value, 'Property.total_sold', 0);
							$report_name = 'property_price_sold';
							$result[]['ReportAccumulate'] = array_merge($contentArr, array(
								// 'id' => $this->_checkExistingAccumulateReport($optionExist, $user_company_id, $report_name),
								'user_company_id' => $user_company_id,
								'name' => $report_name,
								'value' => $price_sold,
							));
							break;
					}
				}
			}

			if( $period == 'year' ) {
				$nextDate += 1;
			} else {
				$nextDate = strtotime('+1 '.$dateAdd, strtotime($nextDate));
				$nextDate = date($formatPhp, $nextDate);
			}
			
			if( !empty($end_date) ) {
				$endDate = $end_date;
			} else {
			$endDate = date($formatPhp, strtotime('+1 '.$dateAdd));
			}

			if( $nextDate == $endDate ) {
				$flag = false;
			}
		}

		$flag = $this->ReportAccumulate->saveMany($result);
		
		if( $period == 'year' ) {
			$nextDate = $nextDate - 1;
		} else {
			$lastDate = Common::formatDate($lastDate, $formatPhp);
			$nextDate = date($formatPhp, strtotime('-1 '.$dateAdd, strtotime($nextDate)));
		}

		if( !empty($flag) ) {
			echo __('Sukses akumulasi laporan properti periode %s s/d %s', $lastDate, $nextDate);
		} else {
			echo __('Gagal akumulasi laporan properti periode %s s/d %s', $lastDate, $nextDate);
		}
		
		echo "<br>";
		return false;
	}

	function accumulate_user_report ( $period = 'month', $start_date = NULL, $end_date = NULL ) {
		$this->loadModel('ReportAccumulate');

		switch ($period) {
			case 'daily':
        		$format = '\'%Y-%m-%d\'';
        		$formatPhp = 'Y-m-d';
        		$dateAdd = "day";
				break;

			case 'year':
        		$format = '\'%Y\'';
        		$formatPhp = 'Y';
        		$dateAdd = "years";
				break;
			
			default:
        		$format = '\'%Y-%m\'';
        		$formatPhp = 'Y-m';
        		$dateAdd = "months";
				break;
		}

		$lastDate = $this->ReportAccumulate->find('first', array(
			'conditions' => array(
				'ReportAccumulate.periode' => $period,
				'ReportAccumulate.name LIKE' => 'user%',
			),
			'order' => array(
				'ReportAccumulate.id' => 'DESC',
			),
		));

		$lastDate = Common::hashEmptyField($lastDate, 'ReportAccumulate.date', false, array(
			'date' => $formatPhp,
		));

		if( empty($lastDate) ) {
			$lastuser = $this->User->find('first', array(
				'conditions' => array(
					'User.created NOT' => NULL,
					'User.created <>' => '0000-00-00 00:00:00',
				),
				'order' => array(
					'User.id' => 'ASC',
				),
			));
			$nextDate = Common::hashEmptyField($lastuser, 'User.created', false, array(
				'date' => $formatPhp,
			));
		} else {
			if( $period == 'year' ) {
				$nextDate = $lastDate + 1;
			} else {
				$nextDate = date($formatPhp, strtotime($lastDate.' +1 '.$dateAdd));
			}

			if( $nextDate > $lastDate ) {
				$nextDate = $lastDate;
			}
		}

		$flag = true;
		$result = array();
		$flagElement = !empty($admin_rumahku)?false:true;

		if( !empty($start_date) ) {
			$nextDate = $start_date;
		}

		$lastDate = $nextDate;

		$this->User->virtualFields['cnt'] = 'COUNT(DISTINCT User.id)';
		
		$this->User->bindModel(array(
            'hasOne' => array(
                'UserCompanyConfigParent' => array(
                    'className' => 'UserCompanyConfig',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'UserCompanyConfigParent.user_id = User.parent_id',
                	),
                ),
            )
        ), false);

		while ($flag) {
			$contentArr = array(
				'date' => $nextDate,
				'periode' => $period,
			);
			$optionExist = array(
				'conditions' => array(
					'ReportAccumulate.periode' => $period,
					'ReportAccumulate.date' => $nextDate,
				),
			);
			$optionsDefault = array(
				'group' => array(
					'User.parent_id',
				),
			);

			// // Total Agent
			$this->User->virtualFields['report_name'] = "'users'";
			$userElements = array(
				'status' => 'all',
				'role' => 'agent',
				'admin_rumahku' => false,
			);
			$userOptions = array_merge(array(
				'conditions' => array(
					'DATE_FORMAT(User.created, '.$format.') <=' => $nextDate,
				),
				'fields' => array(
					'User.cnt',
					'User.report_name',
					'User.parent_id',
				),
			), $optionsDefault);
			$values = $this->User->getData('all', $userOptions, $userElements);
			
			// Monthly Agent
			$this->User->virtualFields['report_name'] = "'user_monthly'";
			$user_monthly = $this->User->getData('all', array_merge(array(
				'conditions' => array(
					'DATE_FORMAT(User.created, '.$format.')' => $nextDate,
				),
			), $optionsDefault), $userElements);
			$values = array_merge($values, (!empty($user_monthly)?$user_monthly:array()));

			// User Aktif
			$this->User->virtualFields['report_name'] = "'user_active'";
			$activeOptions = $userOptions;
			$activeOptions = array_merge_recursive($activeOptions, array(
				'contain' => array(
					'UserCompanyConfigParent',
				),
				'conditions' => array(
					'UserCompanyConfigParent.user_id NOT' => NULL,
					'DATE_FORMAT(UserCompanyConfigParent.end_date, \'%Y-%m-%d\') >=' => date('Y-m-d'),
				),
			));
			$user_active = $this->User->getData('all', $activeOptions, array_merge($userElements, array(
				'status' => 'active-pending',
			)));
			$values = array_merge($values, (!empty($user_active)?$user_active:array()));

			// // User Non-Aktif
			$this->User->virtualFields['report_name'] = "'user_inactive'";
			$this->User->virtualFields['cnt'] = 'COUNT(DISTINCT User.id)';
			$inactiveOptions = $userOptions;
			$inactiveOptions = array_merge_recursive($inactiveOptions, array(
				'contain' => array(
					'UserCompanyConfigParent',
				),
				'conditions' => array(
					'OR' => array(
						array(
		                	'User.status' => 0,
		                	'User.active' => 0,
							'User.deleted' => 0,
		            	),
						array(
							'UserCompanyConfigParent.user_id' => NULL,
						),
						array(
							'DATE_FORMAT(UserCompanyConfigParent.end_date, \'%Y-%m-%d\') <' => date('Y-m-d'),
						),
						array(
							'User.deleted' => 1,
						),
					),
				),
			));
			$user_inactive = $this->User->getData('all', $inactiveOptions, array_merge($userElements, array(
				'status' => 'all',
			)));
			$values = array_merge($values, (!empty($user_inactive)?$user_inactive:array()));

			// // User Deleted
			$this->User->virtualFields['report_name'] = "'user_deleted'";
			$user_deleted = $this->User->getData('all', $userOptions, array_merge($userElements, array(
				'status' => 'deleted',
			)));
			$values = array_merge($values, (!empty($user_deleted)?$user_deleted:array()));

			if( !empty($values) ) {
				foreach ($values as $key => $value) {
					$cnt = Common::hashEmptyField($value, 'User.cnt', 0);
					$report_name = Common::hashEmptyField($value, 'User.report_name');
					$user_company_id = Common::hashEmptyField($value, 'User.parent_id', 0);
					
					$result[]['ReportAccumulate'] = array_merge($contentArr, array(
						// 'id' => $this->_checkExistingAccumulateReport($optionExist, $user_company_id, $report_name),
						'user_company_id' => $user_company_id,
						'name' => $report_name,
						'value' => $cnt,
					));
				}
			}

			if( $period == 'year' ) {
				$nextDate += 1;
			} else {
				$nextDate = strtotime('+1 '.$dateAdd, strtotime($nextDate));
				$nextDate = date($formatPhp, $nextDate);
			}

			if( !empty($end_date) ) {
				$endDate = $end_date;
			} else {
			$endDate = date($formatPhp, strtotime('+1 '.$dateAdd));
			}

			if( $nextDate == $endDate ) {
				$flag = false;
			}
		}

		$flag = $this->ReportAccumulate->saveMany($result);
		
		if( $period == 'year' ) {
			$nextDate = $nextDate - 1;
		} else {
			$lastDate = Common::formatDate($lastDate, $formatPhp);
			$nextDate = date($formatPhp, strtotime('-1 '.$dateAdd, strtotime($nextDate)));
		}

		if( !empty($flag) ) {
			echo __('Sukses akumulasi laporan user periode %s s/d %s', $lastDate, $nextDate);
		} else {
			echo __('Gagal akumulasi laporan user periode %s s/d %s', $lastDate, $nextDate);
		}
		
		echo "<br>";
		return false;
	}

//	api integration rumah123
//	generate json file to be uploaded to rumah 123
//	runtime : daily at 00:00
	public function generate_integration_json(){
		$apiConfig		= Configure::read('Config.Integration');
		$crontabLimit	= Common::hashEmptyField($apiConfig, 'api.rumah123.crontab_limit', 100);

		$extention	= $this->params->ext;
		$datetime	= date('Y-m-d H:i:s');
		$params		= array();
		$requestURL	= array(
			'api'			=> true, 
			'plugin'		=> false, 
			'controller'	=> 'api_properties', 
			'action'		=> 'data_listing', 
			'ext'			=> $extention, 
			'?'				=> array(
				'format'	=> false, 
				'limit'		=> $crontabLimit, 
			), 
		);

		$response	= $this->requestAction($requestURL, $params);
		$results	= array();
		$updateData	= array();

		if($response && is_array($response)){
			$this->RmApiProperty = $this->Components->load('RmApiProperty');

			$date	= date('Y-m-d', strtotime($datetime));
			$time	= date('H-i-s', strtotime($datetime));
			$year	= date('Y', strtotime($datetime));
			$month	= date('m', strtotime($datetime));
			$day	= date('d', strtotime($datetime));
			$hour	= date('H', strtotime($datetime));
			$minute	= date('i', strtotime($datetime));
			$second	= date('s', strtotime($datetime));

			$folderPath	= sprintf('uploads/json/%s/%s/%s/', $year, $month, $day);
			$counter	= array();

			foreach($response as $key => $record){
				$logID			= Common::hashEmptyField($record, 'UserIntegratedSyncProperty.id');
				$logFilePath	= Common::hashEmptyField($record, 'UserIntegratedSyncProperty.file_path');
				$isGenerated	= Common::hashEmptyField($record, 'UserIntegratedSyncProperty.is_generated', 0);
				$isSent			= Common::hashEmptyField($record, 'UserIntegratedSyncProperty.is_sent', 0);

			//	format data ke 123
				$record = $this->RmApiProperty->formatListing(array($record));
				$record = array_shift($record);

				$agentID = Common::hashEmptyField($record, 'agent.r123agent_id');

				if($agentID){
				//	counter
					if(isset($counter[$agentID])){
						$counter[$agentID]+= 1;
					}
					else{
						$counter[$agentID] = 1;
					}

					if(empty($isGenerated) || $isGenerated && file_exists($logFilePath) === false){
					//	format filename clientid_2014-07-16_09-10-45_01.json
						if($isGenerated){
							$filePath = $logFilePath;
						}
						else{
							$fileName = sprintf('%s_%s_%s_%s.json', $agentID, $date, $time, $counter[$agentID]);	
							$filePath = $folderPath . $fileName;
						}

						if(file_exists(dirname($filePath)) === false || is_dir(dirname($filePath)) === false){
							mkdir(dirname($filePath), '0755', true);
						}

						$fileHandler	= fopen($filePath, 'w');
						$fileWrite		= fwrite($fileHandler, json_encode(array(
							'ad' => array($record), 
						)));
					}

					if($fileWrite === false){
						$status		= 'error';
						$message	= __('Cannot write to file (%s)', $fileName);
					}
					else{
						$status		= 'success';
						$message	= __('File (%s) generated', $fileName);

					//	append log data
						$updateData[] = array(
							'UserIntegratedSyncProperty' => array(
								'id'			=> $logID, 
								'file_path'		=> $filePath, 
								'is_generated'	=> 1, 
							), 
						);
					}

					$results[] = array(
						'status'	=> $status, 
						'message'	=> $message, 
					);
				}
				else{
				//	agent id is mandatory
					$results[] = array(
						'status'	=> 'error', 
						'message'	=> __('Invalid Agent'), 
						'data'		=> $record, 
					);
				}
			}
		}
		else{
			$results = array(
				'status'	=> 'error', 
				'message'	=> __('No response'), 
			);	
		}

	//	display generate results
		debug($results);

		if($updateData){
			$this->loadModel('UserIntegratedSyncProperty');

			$updated = $this->UserIntegratedSyncProperty->saveAll($updateData);
			$results = array(
				'status'	=> $updated ? 'success' : 'error', 
				'message'	=> __('%s menyimpan data', $updated ? 'Berhasil' : 'Gagal'), 
			);

			debug($results);
		}

		$this->layout		= false;
		$this->autoRender	= false;
		exit;
	}

//	api integration rumah123
//	upload generated json to rumah 123
//	runtime : daily at 00:00
	public function upload_integration_json(){
		$apiConfig		= Configure::read('Config.Integration');
		$apiClient		= Common::hashEmptyField($apiConfig, 'api.rumah123.client');
		$apiKey			= Common::hashEmptyField($apiConfig, 'api.rumah123.key');
		$apiBaseURL		= Common::hashEmptyField($apiConfig, 'api.rumah123.base_url');
		$crontabLimit	= Common::hashEmptyField($apiConfig, 'api.rumah123.crontab_limit', 100);

		if($apiConfig && $apiBaseURL){
			$this->loadModel('UserIntegratedSyncProperty');
				$syncProperties = $this->UserIntegratedSyncProperty->getData('all', array(
				'limit'			=> $crontabLimit, 
				'conditions'	=> array(
					'UserIntegratedSyncProperty.file_path <>'	=> '', 
					'UserIntegratedSyncProperty.is_sent'		=> 0, 
				), 
			));

			if($syncProperties){
				$apiURL	= $apiBaseURL.'/v1/listing/upload/json?key='.$apiKey;
				$data	= array();

				foreach($syncProperties as $key => $syncProperty){
					$logID		= Common::hashEmptyField($syncProperty, 'UserIntegratedSyncProperty.id');
					$filePath	= Common::hashEmptyField($syncProperty, 'UserIntegratedSyncProperty.file_path');
					$fileExt	= pathinfo($filePath, PATHINFO_EXTENSION);

					if(file_exists($filePath) && $fileExt == 'json'){
						$filePath = realpath($filePath);

						if(function_exists('curl_file_create')){
						//	php 5.5+
							$filePath = curl_file_create($filePath);
						}
						else{
							$filePath = '@' . $filePath;
						}

						$response = Common::httpRequest($apiURL, array(
							'file' => $filePath, 
						), array(
						//	'debug'			=> true, 
							'ssl_version'	=> 1, 
							'method'		=> 'POST', 
							'header'		=> array(
								'content_type' => 'multipart/form-data', 
							)
						));

						$responseData		= Common::hashEmptyField($response, 'response', array());
						$transactionID		= Common::hashEmptyField($response, 'trx_id');
						$responseCode		= Common::hashEmptyField($response, 'code');
						$responseMessage	= Common::hashEmptyField($response, 'message');

					//	append update data
						$data[] = array(
							'UserIntegratedSyncProperty' => Hash::filter(array(
								'id'		=> $logID, 
								'is_sent'	=> $responseCode == '200' ? 1 : 0, 
								'last_sync'	=> date('Y-m-d H:i:s'), 
								'response'	=> json_encode($responseData), 
							)), 
						);
					}
				}

				if($data){
					$this->loadModel('UserIntegratedSyncProperty');

					$updated = $this->UserIntegratedSyncProperty->saveAll($data);
					$results = array(
						'status'	=> $updated ? 'success' : 'error', 
						'message'	=> __('%s menyimpan data', $updated ? 'Berhasil' : 'Gagal'), 
					);

					debug($results);
				}
			}
		}
		else{
			echo('Invalid API config');
		}

		$this->layout		= false;
		$this->autoRender	= false;

		exit;
	}

	public function generate_agent_rank(){
		$this->autoLayout = false;
		$this->autoRender = false;

		echo('Generate agent rank (start) : '.date('Y-m-d H:i:s').'<hr>');

		$currentDate	= date('Y-m-d');
		$currentYear	= date('Y', strtotime($currentDate));
		$currentMonth	= date('m', strtotime($currentDate));

		$generatedCompanies	= $this->User->AgentRank->getData('list', array(
			'fields'		=> array('AgentRank.parent_id'), 
			'group'			=> array('AgentRank.parent_id'), 
			'conditions'	=> array(
				'AgentRank.period_year'		=> $currentYear, 
				'AgentRank.period_month'	=> $currentMonth, 
				'AgentRank.status'			=> 1, 
			), 
		));

	//	$generatedCompanies	= Hash::extract($generatedCompanies, '{n}.AgentRank.parent_id');
		$crontabLimit		= 2; //Configure::read('__Site.config_limit_crontab');
		$activeCompanies	= $this->User->UserCompanyConfig->getData('all', array(
			'limit'			=> $crontabLimit, 
			'order'			=> array('UserCompanyConfig.user_id'), 
			'contain'		=> array('User', 'UserCompany'), 
			'conditions'	=> array(
				'UserCompanyConfig.live_date !='	=> null, 
				'UserCompanyConfig.end_date !='		=> null, 
				'UserCompanyConfig.live_date <='	=> $currentDate, 
				'UserCompanyConfig.end_date >='		=> $currentDate, 
				'UserCompanyConfig.user_id !='		=> $generatedCompanies, 
			), 
		));

		$activeParents = Hash::extract($activeCompanies, '{n}.User.id');
		$activeParents = Hash::filter($activeParents);

		if($activeCompanies){
			foreach($activeCompanies as $key => $company){
				$parentID		= Common::hashEmptyField($company, 'User.id');
				$fullName		= Common::hashEmptyField($company, 'User.full_name');
				$companyName	= Common::hashEmptyField($company, 'UserCompany.name');

				$result		= $this->User->updateUserRank($parentID);
				$message	= Common::hashEmptyField($result, 'msg');

				if($message){
					$message = array_filter(array($message, $companyName ?: __('Principal %s', $fullName)));
					$message = implode(' untuk ', $message);

					$result	= Hash::insert($result, 'Log.activity', $message);
					$result	= Hash::remove($result, 'msg');

					echo($message.'<hr>');
				}

				$this->RmCommon->setProcessParams($result, false, array(
					'noRedirect' => true, 
				));
			}
		}
		else{
			echo('Tidak ada data ranking untuk digenerate<hr>');
		}

		echo('Generate agent rank (end) : '.date('Y-m-d H:i:s').'<hr>');

		return false;
	}

	function activity_category_generator () {
		$this->loadModel('ActivityUser');
		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'activity-category-generator'
			),
		));
		$limit = 50;

		if( !empty($setting) ) {
			$setting_id = Common::hashEmptyField($setting, 'Setting.id');
			$offset = Common::hashEmptyField($setting, 'Setting.offset', 0);
			$temp = Common::hashEmptyField($setting, 'Setting.temp');

			$this->ActivityUser->virtualFields['point'] = 'SUM(ActivityUser.point)';
			$this->ActivityUser->virtualFields['root_expert_category_id'] = 'IFNULL(ExpertCategory.root_id, ExpertCategory.id)';
			
			$values = $this->ActivityUser->getData('all', array(
				'conditions' => array(
					'ActivityUser.activity_status' => 'approved',
				),
				'contain' => array(
					'ExpertCategory',
				),
				'order' => array(
					'ActivityUser.principle_id',
					'ActivityUser.user_id',
				),
				'group' => array(
					'ActivityUser.user_id',
					'ActivityUser.root_expert_category_id',
				),
				'limit' => $limit,
				// 'offset' => $offset,
			), array(
				'mine' => false,
				'company' => false,
			));

			if( !empty($values) ) {
				$this->ActivityUser->ActivityCategoryPoint->virtualFields['max_point'] = 'MAX(ActivityCategoryPoint.point)';
				$updateConditions = array();

				foreach ($values as $key => &$value) {
					$id = Common::hashEmptyField($value, 'ActivityUser.activity_id');
					$expert_category_id = Common::hashEmptyField($value, 'ActivityUser.expert_category_id', 0);
					$root_expert_category_id = Common::hashEmptyField($value, 'ActivityUser.root_expert_category_id', 0);
					$current_point = Common::hashEmptyField($value, 'ActivityUser.point', 0);
					$user_company_id = Common::hashEmptyField($value, 'ActivityUser.user_company_id');
					$principle_id = Common::hashEmptyField($value, 'ActivityUser.principle_id');
					$user_id = Common::hashEmptyField($value, 'ActivityUser.user_id');

					$value = $this->ActivityUser->getMergeList($value, array(
						'contain' => array(
							'User',
							'ActivityCategoryPoint' => array(
								'type' => 'first',
								'conditions' => array(
									'ActivityCategoryPoint.expert_category_id' => $root_expert_category_id,
								),
							),
						)
					));

					$last_category_point = Common::hashEmptyField($value, 'ActivityCategoryPoint.point', 0);

					$total_point = $last_category_point = $current_point;

					// if( $total_point < 0 ) {
					// 	$total_point = 0;
					// }

					$data[] = array(
						'ActivityCategoryPoint' => array(
							'user_company_id' => $user_company_id,
							'principle_id' => $principle_id,
							'user_id' => $user_id,
							'expert_category_id' => $root_expert_category_id,
							'periode_date' => date('Y-m-01'),
							'point' => $total_point,
						),
					);

					$updateConditions['OR'][] = array(
						'ActivityUser.user_id' => $user_id,
						'ActivityUser.expert_category_id' => $root_expert_category_id,
					);

					if( $root_expert_category_id <> $expert_category_id ) {
						$expert_categories = $this->ActivityUser->getData('list', array(
							'conditions' => array(
								'ActivityUser.user_id' => $user_id,
								'ActivityUser.root_expert_category_id' => $root_expert_category_id,
								'ActivityUser.activity_status' => 'approved',
							),
							'fields' => array(
								'ActivityUser.expert_category_id',
								'ActivityUser.expert_category_id',
							),
							'contain' => array(
								'ExpertCategory',
							),
							'group' => array(
								'ActivityUser.expert_category_id',
							),
						), array(
							'mine' => false,
							'company' => false,
						));

						if( !empty($expert_categories) ) {
							$updateConditions['OR'][] = array(
								'ActivityUser.user_id' => $user_id,
								'ActivityUser.expert_category_id' => $expert_categories,
							);
						}
					}
				}
				
				unset($this->ActivityUser->virtualFields);

				if( $this->ActivityUser->ActivityCategoryPoint->saveMany($data) ) {
					$this->ActivityUser->updateAll(array(
						'ActivityUser.activity_status' => "'confirm'",
					), $updateConditions);

					$this->Setting->saveAll(array(
						'id' => $setting_id,
						'offset' => $offset + $limit,
					));
					
					echo __('Sukses menyimpan aktivitas PUS %s s/d %s', $offset, $limit);

					return true;
				} else {
					echo __('Gagal menyimpan aktivitas PUS');
					return false;
				}
			} else {
				$this->Setting->saveAll(array(
					'id' => $setting_id,
					'offset' => 0,
				));

				if( !empty($offset) ) {
					$this->Setting->updateAll(array(
						'Setting.value' => true,
					), array(
						'Setting.slug' => 'point-category-generator',
					));
				}

				echo __('Tidak ada aktivitas');
				return false;
			}
		} else {
			echo __('API tidak bisa di akses');
			return false;
		}
	}

	function point_category_generator () {
		$this->loadModel('ActivityCategoryPoint');
		$this->loadModel('ActivityCategoryPus');
		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'point-category-generator',
				'Setting.value' => true,
			),
		));
		$limit = 50;

		if( !empty($setting) ) {
			$setting_id = Common::hashEmptyField($setting, 'Setting.id');
			$offset = Common::hashEmptyField($setting, 'Setting.offset', 0);

			$this->ActivityCategoryPoint->virtualFields['total_point'] = 'SUM(ActivityCategoryPoint.point)';
			$values = $this->ActivityCategoryPoint->getData('all', array(
				'conditions' => array(
					'ActivityCategoryPoint.activity_status' => array( 'pending', 'confirm' ),
					// 'ActivityCategoryPoint.activity_status' => array( 'pending' ),
				),
				'order' => array(
					'ActivityCategoryPoint.principle_id',
					'ActivityCategoryPoint.user_id',
				),
				'group' => array(
					'ActivityCategoryPoint.expert_category_id',
					'ActivityCategoryPoint.user_id',
				),
				'limit' => $limit,
				'offset' => $offset,
			));

			if( !empty($values) ) {
				$this->ActivityCategoryPoint->virtualFields['max_point'] = 'MAX(ActivityCategoryPoint.point)';
				$updateConditions = array();
				$updatePusConditions = array();

				foreach ($values as $key => &$value) {
					$user_company_id = Common::hashEmptyField($value, 'ActivityCategoryPoint.user_company_id');
					$principle_id = Common::hashEmptyField($value, 'ActivityCategoryPoint.principle_id');
					$user_id = Common::hashEmptyField($value, 'ActivityCategoryPoint.user_id');
					$expert_category_id = Common::hashEmptyField($value, 'ActivityCategoryPoint.expert_category_id');
					$total_point = Common::hashEmptyField($value, 'ActivityCategoryPoint.total_point', 0);

					$expert_category = $this->ActivityCategoryPoint->getData('first', array(
						'conditions' => array(
							'ActivityCategoryPoint.expert_category_id' => $expert_category_id,
						),
						'fields' => array(
							'ActivityCategoryPoint.expert_category_id',
							'ActivityCategoryPoint.max_point',
						),
						'group' => 'ActivityCategoryPoint.expert_category_id',
					));
					$max_point = Common::hashEmptyField($expert_category, 'ActivityCategoryPoint.max_point', 0);

					// if( $max_point < 0 ) {
					// 	$max_point = 0;
					// }

					if( empty($max_point) ) {
						$pus = 0;
					} else {
						$pus = ($total_point / $max_point) * 100;
					}

					if( $pus < 0 ) {
						$pus = 0;
					}

					$data[] = array(
						'ActivityCategoryPus' => array(
							'user_company_id' => $user_company_id,
							'principle_id' => $principle_id,
							'expert_category_id' => $expert_category_id,
							'user_id' => $user_id,
							'periode_date' => date('Y-m-d'),
							'total_point' => $total_point,
							'max_point' => $max_point,
							'pus' => $pus,
						),
					);

					$updateConditions['OR'][] = array(
						'ActivityCategoryPoint.user_id' => $user_id,
						'ActivityCategoryPoint.expert_category_id' => $expert_category_id,
					);
					$updatePusConditions['OR'][] = array(
						'ActivityCategoryPus.user_id' => $user_id,
						'ActivityCategoryPus.expert_category_id' => $expert_category_id,
					);
				}
				
				if($this->ActivityCategoryPus->saveAll($data, array(
					'validate' => 'only',
					'deep' => true,
				))) {
					$this->ActivityCategoryPoint->updateAll(array(
						'ActivityCategoryPoint.activity_status' => "'confirm'",
					), $updateConditions);
					$this->ActivityCategoryPus->updateAll(array(
						'ActivityCategoryPus.activity_status' => "'closed'",
						'ActivityCategoryPus.status' => 0,
					), $updatePusConditions);
					
					if( $this->ActivityCategoryPus->saveMany($data) ) {
						$this->Setting->save(array(
							'id' => $setting_id,
							'offset' => $offset + $limit,
						));
						
						echo __('Sukses menyimpan PUS %s s/d %s', $offset, $limit);
						return true;
					} else {
						echo __('Gagal menyimpan PUS');
						return false;
					}
				} else {
					echo __('Gagal menyimpan PUS');
					return false;
				}
			} else {
				$this->Setting->save(array(
					'id' => $setting_id,
					'offset' => 0,
				));

				// if( !empty($offset) ) {
					$this->Setting->updateAll(array(
						'Setting.value' => '""',
					), array(
						'Setting.slug' => 'point-category-generator',
					));

					$this->Setting->updateAll(array(
						'Setting.value' => true,
					), array(
						'Setting.slug' => 'push-category-generator',
					));
				// }

				echo __('Tidak ada PUS');
				return false;
			}
		} else {
			echo __('API tidak bisa di akses');
			return false;
		}
	}

	function push_category_generator () {
		$this->loadModel('ActivityCategoryPus');
		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'push-category-generator',
				'Setting.value' => true,
			),
		));
		$limit = 50;

		if( !empty($setting) ) {
			$setting_id = Common::hashEmptyField($setting, 'Setting.id');
			$offset = Common::hashEmptyField($setting, 'Setting.offset', 0);

			$values = $this->ActivityCategoryPus->getData('all', array(
				'conditions' => array(
					'ActivityCategoryPus.activity_status' => 'open',
				),
				'order' => array(
					'ActivityCategoryPus.user_id',
				),
				'group' => array(
					'ActivityCategoryPus.user_id',
					'ActivityCategoryPus.expert_category_id',
				),
				'limit' => $limit,
				'offset' => $offset,
			), array(
				'company' => false,
			));

			if( !empty($values) ) {
				$this->ActivityCategoryPus->virtualFields['max_point'] = 'MAX(ActivityCategoryPus.total_point)';

				foreach ($values as $key => &$value) {
					$id = Common::hashEmptyField($value, 'ActivityCategoryPus.id');
					$user_company_id = Common::hashEmptyField($value, 'ActivityCategoryPus.user_company_id');
					$principle_id = Common::hashEmptyField($value, 'ActivityCategoryPus.principle_id');
					$user_id = Common::hashEmptyField($value, 'ActivityCategoryPus.user_id');
					$expert_category_id = Common::hashEmptyField($value, 'ActivityCategoryPus.expert_category_id');
					$total_point = Common::hashEmptyField($value, 'ActivityCategoryPus.total_point');

					$expert_category = $this->ActivityCategoryPus->getData('first', array(
						'conditions' => array(
							'ActivityCategoryPus.expert_category_id' => $expert_category_id,
							'ActivityCategoryPus.principle_id' => $principle_id,
						),
						'fields' => array(
							'ActivityCategoryPus.expert_category_id',
							'ActivityCategoryPus.max_point',
						),
						'group' => 'ActivityCategoryPus.expert_category_id',
					));
					$max_point = Common::hashEmptyField($expert_category, 'ActivityCategoryPus.max_point', 0);

					if( empty($max_point) ) {
						$pus = 0;
					} else {
						$pus = ($total_point / $max_point) * 100;
					}

					if( $pus < 0 ) {
						$pus = 0;
					}

					$data[] = array(
						'ActivityCategoryPus' => array(
							'id' => $id,
							'max_point' => $max_point,
							'pus' => round($pus, 2),
						),
					);
				}
				
				if( $this->ActivityCategoryPus->saveMany($data) ) {
					$this->Setting->save(array(
						'id' => $setting_id,
						'offset' => $offset + $limit,
					));
					
					echo __('Sukses menyimpan Total PUS kategori User %s s/d %s', $offset, $limit);
					return true;
				} else {
					echo __('Gagal menyimpan Total PUS kategori User');
					return false;
				}
			} else {
				$this->Setting->save(array(
					'id' => $setting_id,
					'offset' => 0,
				));

				if( !empty($offset) ) {
					$this->Setting->updateAll(array(
						'Setting.value' => '""',
					), array(
						'Setting.slug' => 'push-category-generator',
					));

					$this->Setting->updateAll(array(
						'Setting.value' => true,
					), array(
						'Setting.slug' => 'push-generator',
					));
				}

				echo __('Tidak ada Total PUS kategori User');
				return false;
			}
		} else {
			echo __('API tidak bisa di akses');
			return false;
		}
	}

	function push_generator () {
		$this->loadModel('ActivityCategoryPus');
		$this->loadModel('ActivityPus');
		$this->loadModel('ExpertCategory');
		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'push-generator',
				'Setting.value' => true,
			),
		));
		$limit = 50;

		if( !empty($setting) ) {
			$setting_id = Common::hashEmptyField($setting, 'Setting.id');
			$offset = Common::hashEmptyField($setting, 'Setting.offset', 0);

			$this->ActivityCategoryPus->virtualFields['total_point'] = 'SUM(ActivityCategoryPus.total_point)';
			$this->ActivityCategoryPus->virtualFields['total_pus'] = 'SUM(ActivityCategoryPus.pus)';
			$values = $this->ActivityCategoryPus->getData('all', array(
				'conditions' => array(
					'ActivityCategoryPus.activity_status' => 'open',
				),
				'order' => array(
					'ActivityCategoryPus.user_id',
				),
				'group' => array(
					'ActivityCategoryPus.user_id',
				),
				'limit' => $limit,
				// 'offset' => $offset,
			), array(
				'company' => false,
			));

			$this->ExpertCategory->unbindModel(array(
				'hasMany' => array(
					'ExpertCategoryActive', 
				), 
			));
			$this->ExpertCategory->bindModel(array(
	            'hasOne' => array(
	                'ExpertCategoryActive',
	            )
	        ), false);

			if( !empty($values) ) {
				$updateConditions = array();
				$updatePusConditions = array();

				$expert_category_principles = Set::extract('/ActivityCategoryPus/principle_id', $values);
				$expert_category_principles = array_unique($expert_category_principles);

				$this->ExpertCategory->virtualFields['cnt'] = 'COUNT(ExpertCategory.company_id)';
				$expert_category = $this->ExpertCategory->getData('list', array(
					'conditions' => array(
						array(
							'OR' => array(
								array( 'ExpertCategory.parent_id' => null ),
								array( 'ExpertCategory.parent_id' => 0 ),
							),
						),
						array(
							'OR' => array(
								array( 'ExpertCategoryActive.company_id' => $expert_category_principles ),
								array( 'ExpertCategory.company_id' => 0 ),
							),
						),
						'ExpertCategoryActive.actived' => 1,
					),
					'fields' => array(
						'ExpertCategory.company_id',
						'ExpertCategory.cnt',
					),
					'contain' => array(
						'ExpertCategoryActive',
					),
					'group' => array(
						'ExpertCategory.company_id',
					),
				), array(
					'company_id' => false,
				));

				foreach ($values as $key => &$value) {
					$user_company_id = Common::hashEmptyField($value, 'ActivityCategoryPus.user_company_id');
					$principle_id = Common::hashEmptyField($value, 'ActivityCategoryPus.principle_id');
					$user_id = Common::hashEmptyField($value, 'ActivityCategoryPus.user_id');
					$total_pus = Common::hashEmptyField($value, 'ActivityCategoryPus.total_pus', 0);
					$total_point = Common::hashEmptyField($value, 'ActivityCategoryPus.total_point', 0);

					$expert_category_cnt = Common::hashEmptyField($expert_category, $principle_id, 0);
					$default_expert_category_cnt = !empty($expert_category[0])?$expert_category[0]:0;
					$total_expert_category_cnt = $expert_category_cnt + $default_expert_category_cnt;

					if( empty($total_expert_category_cnt) ) {
						$pus = 0;
					} else {
						$pus = $total_pus / $total_expert_category_cnt;
					}
					
					$data[] = array(
						'ActivityPus' => array(
							'user_company_id' => $user_company_id,
							'principle_id' => $principle_id,
							'user_id' => $user_id,
							'periode_date' => date('Y-m-d'),
							'total_expert_category' => $total_expert_category_cnt,
							'total_point' => $total_point,
							'total_pus' => $total_pus,
							'pus' => $pus,
						),
					);

					$updateConditions['OR'][] = array(
						'ActivityCategoryPus.user_id' => $user_id,
					);
					$updatePusConditions['OR'][] = array(
						'ActivityPus.user_id' => $user_id,
					);
				}

				$updatePusConditions['DATE_FORMAT(ActivityPus.periode_date, \'%Y-%m\')'] = date('Y-m');
				
				if($this->ActivityPus->saveAll($data, array(
					'validate' => 'only',
					'deep' => true,
				))) {
					$this->ActivityCategoryPus->updateAll(array(
						'ActivityCategoryPus.activity_status' => "'closed'",
					), $updateConditions);
					$this->ActivityPus->updateAll(array(
						'ActivityPus.activity_status' => "'closed'",
						'ActivityPus.status' => 0,
					), $updatePusConditions);
					
					if( $this->ActivityPus->saveMany($data) ) {
						$this->Setting->save(array(
							'id' => $setting_id,
							'offset' => $offset + $limit,
						));
						
						echo __('Sukses menyimpan PUS User %s s/d %s', $offset, $limit);
						return true;
					} else {
						echo __('Gagal menyimpan PUS User');
						return false;
					}
				} else {
					echo __('Gagal menyimpan PUS User');
					return false;
				}
			} else {
				$this->Setting->save(array(
					'id' => $setting_id,
					'offset' => 0,
				));

				if( !empty($offset) ) {
					$this->Setting->updateAll(array(
						'Setting.value' => '""',
					), array(
						'Setting.slug' => 'push-generator',
					));

					$this->Setting->updateAll(array(
						'Setting.value' => true,
					), array(
						'Setting.slug' => 'push-rank',
					));
				}

				echo __('Tidak ada PUS User');
				return false;
			}
		} else {
			echo __('API tidak bisa di akses');
			return false;
		}
	}

	function push_rank () {
		$this->loadModel('ActivityPus');
		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'push-rank',
				'Setting.value' => true,
			),
		));
		$limit = 50;

		if( !empty($setting) ) {
			$setting_id = Common::hashEmptyField($setting, 'Setting.id');
			$offset = Common::hashEmptyField($setting, 'Setting.offset', 0);

			$values = $this->ActivityPus->getData('all', array(
				'conditions' => array(
					'ActivityPus.activity_status' => 'open',
				),
				'order' => array(
					'ActivityPus.principle_id',
				),
				'group' => array(
					'ActivityPus.principle_id',
				),
				'limit' => $limit,
				'offset' => $offset,
			), array(
				'company' => false,
			));

			if( !empty($values) ) {
				$updateConditions = array();

				foreach ($values as $key => &$value) {

					$principle_id = Common::hashEmptyField($value, 'ActivityPus.principle_id');
					$db = ConnectionManager::getDataSource('master');
					$db->rawQuery('SET @r=0;
						UPDATE activity_puses SET rank= @r:= (@r+1)
						where principle_id = \''.$principle_id.'\'
						AND activity_status = \'open\'
						ORDER BY pus DESC, total_point DESC');

					$updateConditions['OR'][] = array(
						'ActivityPus.principle_id' => $principle_id,
					);
				}

				$this->Setting->save(array(
					'id' => $setting_id,
					'offset' => $offset + $limit,
				));
				
				echo __('Sukses menyimpan PUS User %s s/d %s', $offset, $limit);
				return true;
			} else {
				$this->Setting->save(array(
					'id' => $setting_id,
					'offset' => 0,
				));

				if( !empty($offset) ) {
					$this->Setting->updateAll(array(
						'Setting.value' => '""',
					), array(
						'Setting.slug' => 'push-rank',
					));
				}

				echo __('Tidak ada PUS User');
				return false;
			}
		} else {
			echo __('API tidak bisa di akses');
			return false;
		}
	}

	function activity_listing_generator () {
		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'activity-listing-generator',
			),
		));
		$limit = 50;

		if( !empty($setting) ) {
			$this->loadModel('ExpertCategory');

			$setting_id = Common::hashEmptyField($setting, 'Setting.id');
			$offset = Common::hashEmptyField($setting, 'Setting.offset', 0);
			$temp = Common::hashEmptyField($setting, 'Setting.temp');
			$current_day = date('N', strtotime('-1 DAY'));
			$current_date = date('Y-m-d', strtotime('-1 DAY'));

			$expert_category = $this->ExpertCategory->getData('first', array(
				'conditions' => array(
					'ExpertCategory.type' => 'property',
				),
			), array(
				'company_id' => false,
			));

			$list_companies = $this->User->UserCompanyConfig->getData('list', array(
				'fields' => array(
					'UserCompanyConfig.user_id',
				),
				'conditions' => array(
					'UserCompanyConfig.is_expert_system' => true,
				),
			), array(
				'status' => 'published',
			));

			$values = $this->User->Property->getData('all', array(
				'conditions' => array(
					'DATE_FORMAT(Property.created, \'%Y-%m-%d\')' => $current_date,
					'Property.principle_id' => $list_companies,
				),
				'order' => array(
					'Property.principle_id' => 'ASC',
					'Property.user_id' => 'ASC',
				),
				'group' => array(
					'Property.user_id',
				),
				'limit' => $limit,
				'offset' => $offset,
			), array(
				'status' => 'active-pending-sold',
				'company' => false,
			));

			if( !empty($values) ) {
				$expert_category_id = Common::hashEmptyField($expert_category, 'ExpertCategory.id');

				$dataUser = array();
				$dataSave = array(
					'expert_category_id' => $expert_category_id,
					'action_date' => $current_date,
					'activity_status' => 'approved',
				);

				foreach ($values as $key => $value) {
					$id = Common::hashEmptyField($value, 'Property.user_id');
					$parent_id = Common::hashEmptyField($value, 'Property.principle_id');
					$company_id = Common::hashEmptyField($value, 'Property.company_id');

					$sell_value = $this->User->Property->getData('count', array(
						'conditions' => array(
							'Property.user_id' => $id,
							'Property.principle_id' => $parent_id,
							'DATE_FORMAT(Property.created, \'%Y-%m-%d\')' => $current_date,
						)
					), array(
						'status' => 'active-pending-sold',
						'action_type' => 'sell',
						'company' => false,
					));
					$rent_value = $this->User->Property->getData('count', array(
						'conditions' => array(
							'Property.user_id' => $id,
							'Property.principle_id' => $parent_id,
							'DATE_FORMAT(Property.created, \'%Y-%m-%d\')' => $current_date,
						)
					), array(
						'status' => 'active-pending-sold',
						'action_type' => 'rent',
						'company' => false,
					));
					$total_value = $sell_value + $rent_value;

					$pointData = $this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('all', array(
						'conditions' => array(
							'ViewExpertCategoryCompanyDetail.expert_category_type' => 'property',
							'ViewExpertCategoryCompanyDetail.company_id' => $parent_id,
							'OR' => array(
								array(
									__('
										CASE WHEN ViewExpertCategoryCompanyDetail.compare = \'less_than\'
											THEN ViewExpertCategoryCompanyDetail.value > %s
										WHEN ViewExpertCategoryCompanyDetail.compare = \'more_than\'
											THEN ViewExpertCategoryCompanyDetail.value < %s
										ELSE ViewExpertCategoryCompanyDetail.value = %s
										END
									', $sell_value, $sell_value, $sell_value),
									'ViewExpertCategoryCompanyDetail.value_end' => 'jual',
									'ViewExpertCategoryCompanyDetail.slug' => 'property_action',
								),
								array(
									__('
										CASE WHEN ViewExpertCategoryCompanyDetail.compare = \'less_than\'
											THEN ViewExpertCategoryCompanyDetail.value > %s
										WHEN ViewExpertCategoryCompanyDetail.compare = \'more_than\'
											THEN ViewExpertCategoryCompanyDetail.value < %s
										ELSE ViewExpertCategoryCompanyDetail.value = %s
										END
									', $rent_value, $rent_value, $rent_value),
									'ViewExpertCategoryCompanyDetail.value_end' => 'sewa',
									'ViewExpertCategoryCompanyDetail.slug' => 'property_action',
								),
								array(
									__('
										CASE WHEN ViewExpertCategoryCompanyDetail.compare = \'less_than\'
											THEN ViewExpertCategoryCompanyDetail.value > %s
										WHEN ViewExpertCategoryCompanyDetail.compare = \'more_than\'
											THEN ViewExpertCategoryCompanyDetail.value < %s
										ELSE ViewExpertCategoryCompanyDetail.value = %s
										END
									', $total_value, $total_value, $total_value),
									'ViewExpertCategoryCompanyDetail.value_end' => 'all_action',
									'ViewExpertCategoryCompanyDetail.slug' => 'property_action',
								),
								array(
									'ViewExpertCategoryCompanyDetail.slug' => 'day',
									'ViewExpertCategoryCompanyDetail.value' => $current_day,
								),
							),
						),
					), array(
						'company_id' => false,
					));
					$dataValid = $this->RmActivity->checkDataValid($pointData, $expert_category_id);

					$data_valid = Common::hashEmptyField($dataValid, 'data_valid');
					$default_expert_category_component = Common::hashEmptyField($dataValid, 'default_expert_category_component');

					if( !empty($data_valid) ) {
						// Untuk Sorting urutan selain schema di atas
						$this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['order_sort'] = 'CASE WHEN ViewExpertCategoryCompanyDetail.type = \'schema\' THEN 1 ELSE 0 END';

						foreach ($data_valid as $key => $valid) {
							$from = !empty($valid[0])?$valid[0]:null;
							$curr_id = !empty($valid[1])?$valid[1]:null;

        					$default_exp_cat_comp = $this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('first', array(
								'conditions' => array(
									'ViewExpertCategoryCompanyDetail.type' => array( 'conditions', 'other', 'property_action', 'schema' ),
									'ViewExpertCategoryCompanyDetail.from' => $from,
									'ViewExpertCategoryCompanyDetail.expert_category_component_active_id' => $curr_id,
								),
								'order' => array(
									'ViewExpertCategoryCompanyDetail.order_sort',
								),
							), array(
								'company_id' => false,
							));

        					if( !empty($default_expert_category_component) ) {
								$point_type = Common::hashEmptyField($default_exp_cat_comp, 'ViewExpertCategoryCompanyDetail.point_type');
								
								if( $from == 'plus' ) {
									$point = Common::hashEmptyField($default_exp_cat_comp, 'ViewExpertCategoryCompanyDetail.point_plus', 0);
								} else {
									$point = Common::hashEmptyField($default_exp_cat_comp, 'ViewExpertCategoryCompanyDetail.point_min', 0);
									$point = abs($point) * -1;
								}

								$dataUser[] = array_merge($dataSave, array(
									'user_company_id' => $company_id,
									'principle_id' => $parent_id,
									'expert_category_component_active_id' => $curr_id,
									'user_id' => $id,
									'point_type' => $point_type,
									'point' => $point,
									'value' => $total_value,
								));
							}
						}

						unset($this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['order_sort']);
						unset($this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['label']);
						unset($this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['cnt']);
					}
				}
				
				if( $this->ExpertCategory->ActivityUser->saveMany($dataUser) ) {
					$this->Setting->saveAll(array(
						'id' => $setting_id,
						'offset' => $offset + $limit,
					));
					
					echo __('Sukses menyimpan aktivitas Listing %s s/d %s', $offset, $limit);
					return true;
				} else {
					echo __('Gagal menyimpan aktivitas Listing');
					return false;
				}
			} else {
				$this->Setting->saveAll(array(
					'id' => $setting_id,
					'offset' => 0,
				));

				// if( !empty($offset) ) {
					$this->Setting->updateAll(array(
						'Setting.value' => '""',
					), array(
						'Setting.slug' => 'activity-listing-generator',
					));

					$this->Setting->updateAll(array(
						'Setting.value' => true,
					), array(
						'Setting.slug' => 'activity-category-generator',
					));
				// }

				echo __('Tidak ada aktivitas');
				return false;
			}
		} else {
			echo __('API tidak bisa di akses');
			return false;
		}
	}

	function activity_ebrosur_generator () {
		$this->loadModel('Setting');
		$setting = $this->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'activity-ebrosur-generator'
			),
		));
		$limit = 30;

		if( !empty($setting) ) {
			$this->loadModel('ExpertCategory');

			$setting_id = Common::hashEmptyField($setting, 'Setting.id');
			$offset = Common::hashEmptyField($setting, 'Setting.offset', 0);
			$temp = Common::hashEmptyField($setting, 'Setting.temp');

			$current_day = date('N', strtotime('-1 DAY'));
			$current_date = date('Y-m-d', strtotime('-1 DAY'));

			$list_companies = $this->User->UserCompanyConfig->getData('list', array(
				'fields' => array(
					'UserCompanyConfig.user_id',
				),
				'conditions' => array(
					'UserCompanyConfig.is_expert_system' => true,
				),
			), array(
				'status' => 'published',
			));

			$values = $this->User->UserCompanyEbrochure->getData('all', array(
				'conditions' => array(
					'User.group_id' => Configure::read('__Site.Role.company_agent'),
					'DATE_FORMAT(UserCompanyEbrochure.created, \'%Y-%m-%d\')' => $current_date,
					'UserCompanyEbrochure.principle_id' => $list_companies,
				),
				'contain' => array(
					'User',
				),
				'group' => array(
					'UserCompanyEbrochure.user_id',
				),
				'order' => array(
					'UserCompanyEbrochure.principle_id',
					'UserCompanyEbrochure.user_id',
				),
				'limit' => $limit,
				'offset' => $offset,
			), array(
				'company' => false,
				'admin' => true,
			));
			$expert_category = $this->ExpertCategory->getData('first', array(
				'conditions' => array(
					'ExpertCategory.company_id' => 0,
					'ExpertCategory.type' => 'ebrosur',
				),
			), array(
				'company_id' => false,
			));

			if( !empty($values) ) {
				$expert_category_id = Common::hashEmptyField($expert_category, 'ExpertCategory.id');

				$dataUser = array();
				$dataSave = array(
					'expert_category_id' => $expert_category_id,
					'action_date' => $current_date,
					'activity_status' => 'approved',
				);

				foreach ($values as $key => $value) {
					$id = Common::hashEmptyField($value, 'UserCompanyEbrochure.user_id');
					$parent_id = Common::hashEmptyField($value, 'UserCompanyEbrochure.principle_id');
					$company_id = Common::hashEmptyField($value, 'UserCompanyEbrochure.company_id');

					$sell_value = $this->User->UserCompanyEbrochure->getData('count', array(
						'conditions' => array(
							'UserCompanyEbrochure.user_id' => $id,
							'DATE_FORMAT(UserCompanyEbrochure.created, \'%Y-%m-%d\')' => $current_date,
							'UserCompanyEbrochure.principle_id' => $parent_id,
						)
					), array(
						'action_type' => 'sell',
						'company' => false,
						'admin' => true,
					));
					$rent_value = $this->User->UserCompanyEbrochure->getData('count', array(
						'conditions' => array(
							'UserCompanyEbrochure.user_id' => $id,
							'UserCompanyEbrochure.principle_id' => $parent_id,
							'DATE_FORMAT(UserCompanyEbrochure.created, \'%Y-%m-%d\')' => $current_date,
						)
					), array(
						'action_type' => 'rent',
						'company' => false,
						'admin' => true,
					));
					$total_value = $sell_value + $rent_value;

					$pointData = $this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('all', array(
						'conditions' => array(
							'ViewExpertCategoryCompanyDetail.expert_category_type' => 'ebrosur',
							'ViewExpertCategoryCompanyDetail.company_id' => $parent_id,
							'OR' => array(
								array(
									__('
										CASE WHEN ViewExpertCategoryCompanyDetail.compare = \'less_than\'
											THEN ViewExpertCategoryCompanyDetail.value > %s
										WHEN ViewExpertCategoryCompanyDetail.compare = \'more_than\'
											THEN ViewExpertCategoryCompanyDetail.value < %s
										ELSE ViewExpertCategoryCompanyDetail.value = %s
										END
									', $sell_value, $sell_value, $sell_value),
									'ViewExpertCategoryCompanyDetail.value_end' => 'jual',
									'ViewExpertCategoryCompanyDetail.slug' => 'ebrosur_action',
								),
								array(
									__('
										CASE WHEN ViewExpertCategoryCompanyDetail.compare = \'less_than\'
											THEN ViewExpertCategoryCompanyDetail.value > %s
										WHEN ViewExpertCategoryCompanyDetail.compare = \'more_than\'
											THEN ViewExpertCategoryCompanyDetail.value < %s
										ELSE ViewExpertCategoryCompanyDetail.value = %s
										END
									', $rent_value, $rent_value, $rent_value),
									'ViewExpertCategoryCompanyDetail.value_end' => 'sewa',
									'ViewExpertCategoryCompanyDetail.slug' => 'ebrosur_action',
								),
								array(
									__('
										CASE WHEN ViewExpertCategoryCompanyDetail.compare = \'less_than\'
											THEN ViewExpertCategoryCompanyDetail.value > %s
										WHEN ViewExpertCategoryCompanyDetail.compare = \'more_than\'
											THEN ViewExpertCategoryCompanyDetail.value < %s
										ELSE ViewExpertCategoryCompanyDetail.value = %s
										END
									', $total_value, $total_value, $total_value),
									'ViewExpertCategoryCompanyDetail.value_end' => 'all_action',
									'ViewExpertCategoryCompanyDetail.slug' => 'ebrosur_action',
								),
								array(
									'ViewExpertCategoryCompanyDetail.slug' => 'day',
									'ViewExpertCategoryCompanyDetail.value' => $current_day,
								),
							),
						),
					), array(
						'company_id' => false,
					));
					$dataValid = $this->RmActivity->checkDataValid($pointData, $expert_category_id);

					$data_valid = Common::hashEmptyField($dataValid, 'data_valid');
					$default_expert_category_component = Common::hashEmptyField($dataValid, 'default_expert_category_component');

					if( !empty($data_valid) ) {
						// Untuk Sorting urutan selain schema di atas
						$this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['order_sort'] = 'CASE WHEN ViewExpertCategoryCompanyDetail.type = \'schema\' THEN 1 ELSE 0 END';

						foreach ($data_valid as $key => $valid) {
							$from = !empty($valid[0])?$valid[0]:null;
							$curr_id = !empty($valid[1])?$valid[1]:null;

        					$default_exp_cat_comp = $this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('first', array(
								'conditions' => array(
									'ViewExpertCategoryCompanyDetail.type' => array( 'conditions', 'other', 'ebrosur_action', 'schema' ),
									'ViewExpertCategoryCompanyDetail.from' => $from,
									'ViewExpertCategoryCompanyDetail.expert_category_component_active_id' => $curr_id,
								),
								'order' => array(
									'ViewExpertCategoryCompanyDetail.order_sort',
								),
							), array(
								'company_id' => false,
							));

        					if( !empty($default_expert_category_component) ) {
								$point_type = Common::hashEmptyField($default_exp_cat_comp, 'ViewExpertCategoryCompanyDetail.point_type');
								
								if( $from == 'plus' ) {
									$point = Common::hashEmptyField($default_exp_cat_comp, 'ViewExpertCategoryCompanyDetail.point_plus', 0);
								} else {
									$point = Common::hashEmptyField($default_exp_cat_comp, 'ViewExpertCategoryCompanyDetail.point_min', 0);
									$point = abs($point) * -1;
								}

								$dataUser[] = array_merge($dataSave, array(
									'user_company_id' => $company_id,
									'principle_id' => $parent_id,
									'expert_category_component_active_id' => $curr_id,
									'user_id' => $id,
									'point_type' => $point_type,
									'point' => $point,
									'value' => $total_value,
								));
							}
						}

						unset($this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['order_sort']);
						unset($this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['label']);
						unset($this->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['cnt']);
					}
				}
				
				if( $this->ExpertCategory->ActivityUser->saveMany($dataUser) ) {
					$this->Setting->saveAll(array(
						'id' => $setting_id,
						'offset' => $offset + $limit,
					));
					
					echo __('Sukses menyimpan aktivitas E-Brosur %s s/d %s', $offset, $limit);
					return true;
				} else {
					echo __('Gagal menyimpan aktivitas E-Brosur');
					return false;
				}
			} else {
				$this->Setting->saveAll(array(
					'id' => $setting_id,
					'offset' => 0,
				));

				// if( !empty($offset) ) {
					$this->Setting->updateAll(array(
						'Setting.value' => true,
					), array(
						'Setting.slug' => 'activity-listing-generator',
					));
				// }

				echo __('Tidak ada aktivitas');
				return false;
			}
		} else {
			echo __('API tidak bisa di akses');
			return false;
		}
	}
}