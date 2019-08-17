<?php
App::uses('AppController', 'Controller');
App::uses('NumberHelper', 'View/Helper');

class KprController extends AppController {
	public $name = 'Kpr';
	public $components = array(
		'RmKpr', 'RmProperty', 'RmImage', 'Captcha',
		'RmBooking', 'Rest.Rest' => array(
            'debug' => 2,
            'actions' => array(
	            'api_action_KPR' => array(
	            	'extract' => array(
	                	'result'
	            	),
	            ),
	            'api_paid_commission' => array(
	            	'extract' => array(
	                	'result'
	            	),
	            ),
	            'api_action_credit_agreement' => array(
	            	'extract' => array(
	                	'result'
	            	),
	            ),
	            'api_kpr_refferals' => array(
	            	'extract' => array(
	            		'msg', 'status', 
	            		'data'
	            	)
	            ),
	            'admin_add' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'api_index' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				     	'paging', 'data'
				 	),
			 	),
			 	'backprocess_get_property' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device',
				 		'data', 'banks',
					)
			 	),
	            'admin_application' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'validationErrors', 'data',
				  		'documentCategories', 'documentCategoriesSpouse',
				 	),
			 	),
	            'api_filing' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'banks', 'validationErrors',
				 	),
			 	),
	            'admin_change_status' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				  		'id',
				 	),
			 	),
	            'admin_delete_kpr' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				 	),
			 	),
			 	'api_application_detail' => array(
			 		'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				 	),
			 	),
	            'admin_update_kpr' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device', 'data',
				  		'validationErrors',
				 	),
			 	),
	            'api_bank_applications' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				     	'data'
				 	),
			 	),
	            'api_appraisal' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				     	'data'
				 	),
			 	),
	            'api_calculator' => array(
				  	'extract' => array(
				  		'msg', 'status', 'link', 'appversion', 'device',
				     	'data'
				 	),
			 	),
			 	'api_get_properties' => array(
			 		'extract' => array(
				 		'msg', 'status', 'link', 'appversion', 'device',
				 		'paging', 'data',
					)
			 	)
            ),
		),
	);
	public $helpers = array(
		'Kpr', 'Crm',
		'FileUpload.UploadForm',
	);

	function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow(array(
			'bank_calculator', 'share_kpr', 'api_kpr_confirmation',
			'admin_search' , 'admin_bank_list', 'api_action_KPR',
			'api_paid_commission', 'admin_foward_application', 
			'select_product', 'detail_installment', 'api_action_credit_agreement',
			'clear_snyc', 'product_list', 'application_product', 'ajax_compare_detail', 
			'compare', 'kpr_success', 'generate_setting_product', 'detail_banks',
			'application_banks', 'direct_link', 'admin_sales',
			'admin_notice_toggle'
		));
		$this->set('active_menu', 'kpr_list');
	}

	function generate_setting_product($limit = 10){ // hanya buat generate ketika bank product aktif
		$values = $this->User->Kpr->KprBank->getData('all', array(
			'conditions' => array(
				'KprBank.setting_id' => null,
			),
			'limit' => $limit
		));

		if(!empty($values)){
			foreach ($values as $key => $value) {
				$bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'bank_id');
				$value = $this->User->Kpr->KprBank->Bank->BankSetting->getMerge($value, $bank_id);

				if(!empty($value['BankSetting'])){
					$kpr_bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'id');
					$modified = $this->RmCommon->filterEmptyField($value, 'KprBank', 'modified');
					$bank_setting_id = $this->RmCommon->filterEmptyField($value, 'BankSetting', 'id');
					
					$this->User->Kpr->KprBank->id = $kpr_bank_id;
					$this->User->Kpr->KprBank->set('setting_id', $bank_setting_id);
					$this->User->Kpr->KprBank->set('modified', $modified);
					$this->User->Kpr->KprBank->save();
				}
			}
			echo __('Berhasil generate bank default');
		}else{
			echo __('Data tidak tersedia');
		}
		die();
	}

	function admin_search ( $action = 'index', $_admin = true, $addParam = false ) {
		$data = $this->request->data;
		$params = array(
			'action' => $action,
			$addParam,
			'admin' => $_admin,
		);
		$this->RmCommon->processSorting($params, $data);
	}

	function api_action_KPR(){
		$this->loadModel('ApiUser');
		$data = $this->request->data;
		$apikey = $this->RmCommon->filterEmptyField($data, 'Kpr', 'apikey');
		$api_secret = $this->RmCommon->filterEmptyField($data, 'Kpr', 'apipass');
		$access = $this->ApiUser->get_access($apikey, $api_secret);

		if(!empty($access)){
			$data = $this->RmCommon->filterEmptyField($data, 'Kpr', 'merge_vars');
			$prime_kpr_bank_id = $this->RmCommon->filterEmptyField($data, 'KprBank', 'prime_kpr_bank_id');
			$document_status_KPR = $this->RmCommon->filterEmptyField($data, 'KprBank', 'document_status');

			if(!empty($prime_kpr_bank_id)){
				$value = $this->User->Kpr->KprBank->MergeEmailKPR($prime_kpr_bank_id);
				$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
				$document_status = $this->RmCommon->filterEmptyField($value, 'KprBank', 'document_status');

					$data = $this->RmKpr->doBeforeSaveActionKPR($data);
					$result = $this->User->Kpr->KprBank->doUpdateActionKPR($data, $value);
					$status = $this->RmCommon->filterEmptyField($result, 'status');

					if($status == 'success'){
						## UPDATE SUMARRY KPR
						$result_summary = $this->User->Kpr->_summaryDocumentStatus($kpr_id);
						$this->RmCommon->setProcessParams($result_summary, false, array(
							'noRedirect' => true
						));
						##
						$value = $this->User->Kpr->KprBank->KprBankDate->getFromSlug($value, $prime_kpr_bank_id);
						$kpr_bank_date = $this->RmCommon->filterEmptyField($value, 'KprBankDate');
						$result_credit_agreement = $this->User->Kpr->KprBank->KprBankCreditAgreement->doSaveActionKPR($data, $value);

						$value = $this->User->Kpr->KprBank->KprBankCreditAgreement->getMerge($value, $prime_kpr_bank_id, array(
							'fieldName' => 'kpr_bank_id'
						));

						$log_document_status = Set::classicExtract($kpr_bank_date, '{n}.KprBankDate.slug');

						if(in_array('approved_bank', $log_document_status)){
							$status_confirm = 'confirm'; 
						}else{
							$status_confirm = 'no_confirm';
						}
						# get VALUE INSTALLMENT N Provisi
						$value = $this->User->Kpr->KprBank->KprBankInstallment->getMerge($value, $prime_kpr_bank_id, array(
							'elements' => array(
								'status' => $status_confirm,
							),
							'fieldName' => 'kpr_bank_id',
						));

						$kpr_bank_installment_id = $this->RmCommon->filterEmptyField($value, 'KprBankInstallment', 'id');

						$value = $this->User->Kpr->KprBank->KprBankInstallment->KprBankCommission->getMerge($value, $kpr_bank_installment_id, array(
							'elements' => array(
								'status' => $status_confirm,
							),
							'find' => 'all',
							'fieldName' => 'kpr_bank_installment_id',
						));
						## LINK untuk notif dan email
						$link = array(
	                        'controller' => 'kpr',
	                        'action' => 'application_detail',
	                        $kpr_id,
	                        'admin' => true,
	                    );
						## sendEmail 
						$this->RmKpr->sendEmailActionKPR($data, $value, array(
							'link' => $link,
						));
						##	
						$this->RmCommon->setProcessParams($result_credit_agreement, false, array(
							'noRedirect' => true
						));
					}
			}else{
				$result = array(
					'status' => 'error',
				);
			}
		}else{
			$result = array(
				'status' => 'error',
				'msg' => __('Forbidden authorization API Key and API Secreet'),
			);
		}
		$this->set('result', $result);
	}

	function application_banks(){
		$this->loadModel('Bank');
		$this->loadModel('KprApplicationValidation');
		
		$kpr_id = false;
		$named = $this->RmCommon->filterEmptyField($this->params, 'named');
		$property_id = $this->RmCommon->filterEmptyField($named, 'property_id');
		$bank_id = $this->RmCommon->filterEmptyField($named, 'bank_id');
		$name_cookie = sprintf('filling_%s', $property_id);
		$session_id = $this->Cookie->read($name_cookie);

		// load css bank
		$this->RmCommon->_layout_file(array(
			'bank',
		));

		$kpr = $this->User->Kpr->getData('first', array(
			'conditions' => array(
				'Kpr.session_id' => $session_id,
			),
			'order' => false,
		));

		$kpr_id = $this->RmCommon->filterEmptyField($kpr, 'Kpr', 'id');
		$kpr_bank = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.kpr_id' => $kpr_id,
				'KprBank.bank_id' => $bank_id
			),
			'order' => false,
		));
		$kpr_bank = $this->User->Kpr->KprBank->getMergeList($kpr_bank, array(
			'contain' => array(
				'KprBankInstallment' => array(
					'type' => 'first',
				),
			),
		));

		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $property_id,
			),
		));

		$property = $this->User->Property->getMergeList($property, array(
			'contain' => array(
				'PropertyAddress' => array(
					'contain' => array(
						'Region',
						'City',
						'Subarea',
					),
				),
				'PropertyAsset',
				'PropertyFacility',
				'PropertyPointPlus',
			),
		));
		
		$on_progress_kpr = Common::hashEmptyField($property, 'Property.on_progress_kpr');

		if(empty($on_progress_kpr)){
			if(!empty($session_id) && !empty($kpr_bank)){
				$data = $this->request->data;

				$bank_exclusive = $this->Bank->isExclusive($bank_id); 

				if(!empty($data)){
					$data = $this->RmKpr->beforeSaveProductKpr($data, $kpr);
					$result = $this->User->Kpr->KprApplication->doSaveProductKpr($data, $property_id);
					$flash = $this->RmCommon->filterEmptyField($result, 'flash');
					$status = $this->RmCommon->filterEmptyField($result, 'status');

					if($status == 'success'){
						$kpr_id = $this->RmCommon->filterEmptyField($result, 'kpr_id');

						if(!empty($data['CrmProjectDocument'])){
							$documents['CrmProjectDocument'] = $data['CrmProjectDocument'];
							$data = $this->RmCommon->_callUnset( array(
								'CrmProjectDocument'
							), $data);
						}

			        	if(!empty($documents['CrmProjectDocument'])){
			        		foreach($documents['CrmProjectDocument'] AS $key => $document){
			        			$document['CrmProjectDocument']['owner_id'] = !empty($kpr_id)?$kpr_id:0;
			        			$documents['CrmProjectDocument'][$key] = $document;
			        		}
			        	}

						if(!empty($documents['CrmProjectDocument'])){
							$resultDocument = $this->User->CrmProject->CrmProjectDocument->doSaveMany($documents['CrmProjectDocument'], false, true);
							$log_msg = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'activity');
							$old_data = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'old_data');
							$document_id = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'document_id');
							$error = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'error');
							$code_error = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'code_error');
							$validation_data = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'validation_data');
			    			$this->RmCommon->_saveLog($log_msg, $old_data, $document_id, $error, $code_error, $validation_data);
						}

						$this->Cookie->delete($name_cookie);
					}else{
						$result = $this->RmCommon->dataConverter($result, array(
							'date' => array(
								'data' => array(
									'KprApplication' => array(
										'birthday'
									),
								),
							),
						), true);
					}

	                $this->RmCommon->setProcessParams($result, array(
						'controller' => 'kpr',
						'action' => 'kpr_success',
						$kpr_id,
						'sessionID' => $session_id,
						'admin' => false,
					), array(
						'flash' => $flash,
					));
				}else{
					$loan_price = $this->RmCommon->filterEmptyField($kpr_bank, 'KprBankInstallment', 'loan_price');
					$bank_apply_category_id = $this->RmCommon->filterEmptyField($kpr, 'Kpr', 'bank_apply_category_id');
					$this->request->data = array(
						'KprApplication' => array(
							'bank_apply_category_id' => $bank_apply_category_id,
							'loan_price' => $loan_price,
						),
					);
				}

				$this->RmCommon->_callRequestSubarea('KprApplication');

				$bankApplyCategories = $this->User->Kpr->BankApplyCategory->getList();
				$jobTypes = $this->User->Kpr->KprApplication->JobType->getList();

				$title = __('Form Aplikasi KPR');
				$this->set(array(
					'bankApplyCategories' => $bankApplyCategories,
					'jobTypes' => $jobTypes,
					'application_banks' => true,
					'property' => $property,
					'bank_exclusive' => $bank_exclusive,
					'title_for_layout' => $title,
					'module_title' => $title,
				));
			}else{
				$this->RmCommon->redirectReferer(__('Anda belum mengajukan promo KPR, silakan pilih terlebih dahulu'), 'error', array_merge(array(
					'controller' => 'kpr',
					'action' => 'detail_banks',
				), $named));
			}
		}else{
			$this->RmCommon->redirectReferer(__('Mohon maaf, properti ini sudah dalam tahap proses KPR'), 'error');
		}
	}

	function application_product(){
		$kpr_id = false;
		$named = $this->RmCommon->filterEmptyField($this->params, 'named');
		$property_id = $this->RmCommon->filterEmptyField($named, 'property_id');
		$name_cookie = sprintf('filling_%s', $property_id);
		$session_id = $this->Cookie->read($name_cookie);

		// load css bank
		$this->RmCommon->_layout_file(array(
			'bank',
		));

		if(!empty($session_id)){
			$data = $this->request->data;

			$value = $this->User->Kpr->getData('first', array(
				'conditions' => array(
					'Kpr.session_id' => $session_id,
				),
			));

		//	personal page
			$companyData	= Configure::read('Config.Company.data');
			$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
			$isIndependent	= Common::validateRole('independent_agent', $userGroupID);

			$property = $this->User->Property->getData('first', array(
				'conditions' => array(
					'Property.id' => $property_id,
				),
			), array(
				'company' => empty($isIndependent), 
			));

			$property = $this->User->Property->getMergeList($property, array(
				'contain' => array(
					'PropertyAddress' => array(
						'contain' => array(
							'Region',
							'City',
							'Subarea',
						),
					),
					'PropertyAsset',
					'PropertyFacility',
					'PropertyPointPlus',
				),
			));

			if(!empty($data)){
				$data = $this->RmKpr->beforeSaveProductKpr($data, $value);
				$result = $this->User->Kpr->KprApplication->doSaveProductKpr($data, $property_id);
				$flash = $this->RmCommon->filterEmptyField($result, 'flash');
				$status = $this->RmCommon->filterEmptyField($result, 'status');

				if($status == 'success'){
					$kpr_id = $this->RmCommon->filterEmptyField($result, 'kpr_id');

					if(!empty($data['CrmProjectDocument'])){
						$documents['CrmProjectDocument'] = $data['CrmProjectDocument'];
						$data = $this->RmCommon->_callUnset( array(
							'CrmProjectDocument'
						), $data);
					}

		        	if(!empty($documents['CrmProjectDocument'])){
		        		foreach($documents['CrmProjectDocument'] AS $key => $document){
		        			$document['CrmProjectDocument']['owner_id'] = !empty($kpr_id)?$kpr_id:0;
		        			$documents['CrmProjectDocument'][$key] = $document;
		        		}
		        	}

					if(!empty($documents['CrmProjectDocument'])){
						$resultDocument = $this->User->CrmProject->CrmProjectDocument->doSaveMany($documents['CrmProjectDocument'], false, true);
						$log_msg = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'activity');
						$old_data = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'old_data');
						$document_id = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'document_id');
						$error = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'error');
						$code_error = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'code_error');
						$validation_data = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'validation_data');
		    			$this->RmCommon->_saveLog($log_msg, $old_data, $document_id, $error, $code_error, $validation_data);
					}

					$this->Cookie->delete($name_cookie);
				}else{
					$result = $this->RmCommon->dataConverter($result, array(
						'date' => array(
							'data' => array(
								'KprApplication' => array(
									'birthday'
								),
							),
						),
					), true);
				}

                $this->RmCommon->setProcessParams($result, array(
					'controller' => 'kpr',
					'action' => 'kpr_success',
					$kpr_id,
					'sessionID' => $session_id,
					'admin' => false,
				), array(
					'flash' => $flash,
				));

			}else{
				$bank_apply_category_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'bank_apply_category_id');
				$this->request->data = array(
					'KprApplication' => array(
						'bank_apply_category_id' => $bank_apply_category_id,
					),
				);
			}

			$this->RmCommon->_callRequestSubarea('KprApplication');

			$bankApplyCategories = $this->User->Kpr->BankApplyCategory->getList();
			$jobTypes = $this->User->Kpr->KprApplication->JobType->getList();

			$title = __('Form Aplikasi KPR');
			$this->set(array(
				'application_product' => 'active',
				'active_module' => 'crm',
				'jobTypes' => $jobTypes,
				'property' => $property,
				'captcha_code' =>  $this->Captcha->generateEquation(),
				'bankApplyCategories' => $bankApplyCategories,
				'title_for_layout' => $title,
				'module_title' => $title,
			));
		}else{
			$this->RmCommon->redirectReferer(__('Anda belum mengajukan promo KPR, silakan pilih terlebih dahulu'), 'error', array_merge(array(
				'controller' => 'kpr',
				'action' => 'select_product',
			), $named));
		}
	}

	function kpr_success($id = false){
		$session_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'sessionID');

		$this->RmCommon->_layout_file(array(
			'bank',
		));

		$value = $this->User->Kpr->getData('first', array(
			'conditions' => array(
				'Kpr.id' => $id,
				'Kpr.session_id' => $session_id,
			),
		), array(
			'company' => false, 
		));

		if(!empty($value)){
			$property_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_id');
			$value = $this->User->Property->getMerge($value, $property_id);
			$value = $this->User->Property->PropertyAddress->getMerge($value, $property_id);

			$client_name = $this->RmCommon->filterEmptyField($value, 'Kpr', 'client_name');
			$this->set(array(
				'value' => $value,
				'title_for_layout' => __('Selamat KPR Berhasil dikirim oleh %s', $client_name),
			));
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function compare($compare_id  = false, $uuid = false){
		$this->loadModel('KprCompare');

		// load css bank
		$this->RmCommon->_layout_file(array(
			'bank',
		));

		$value = $this->KprCompare->getData('first', array(
			'conditions' => array(
				'KprCompare.id' => $compare_id,
				'KprCompare.uuid' => $uuid,
			),
		));

		if(!empty($value)){
			$value = $this->KprCompare->getMergeList($value, array(
				'contain' => array(
					'Property' => array(
						'contain' => array(
							'PropertyAddress',
						),
					),
					'KprCompareDetail' => array(
						'contain' => array(
							'KprBankInstallment',
							'Bank',
							'BankProduct',
						),
					),
				),
			));

			if(!empty($value['KprCompareDetail'])){
				$property['Property'] = $this->RmCommon->filterEmptyField($value, 'Property');
				$property['PropertyAddress'] = $this->RmCommon->filterEmptyField($value, 'Property', 'PropertyAddress');

				foreach($value['KprCompareDetail'] AS $key => $val){
					$val = $this->RmKpr->getSummaryKprProduct($property, $val, 'KprBankInstallment');

					$value['KprCompareDetail'][$key] = $val;
				}
			}

			//  set data input disabled
			$down_payment = $this->RmCommon->filterEmptyField($value, 'KprCompare', 'down_payment');
			$periode_installment = $this->RmCommon->filterEmptyField($value, 'KprCompare', 'periode_installment');

			$this->request->data = array(
				'down_payment' => $down_payment,
				'periode_installment' => $periode_installment,
			);
			// end set data input disabled

			$this->set(array(
				'value' => $value,
				'title_for_layout' => __('Bandingkan promo bank pilihan Anda')
			));
		}else{
			$this->RmCommon->redirectReferer(__('Kpr bandingkan tidak ditemukan'));
		}

	}

	function detail_banks(){
		$date_now = date('Y-m-d');

		// load css bank
		$this->RmCommon->_layout_file(array(
			'bank',
		));

		$params = $this->params->params;
		$named = Common::hashEmptyField($params, 'named');
		$bank_id = Common::hashEmptyField($named, 'bank_id');
		$property_id = Common::hashEmptyField($named, 'property_id');
		$name_cookie = sprintf('filling_%s', $property_id);

		$session_id = $this->Cookie->read( $name_cookie );
		$session_id = !empty($session_id) ? $session_id : String::uuid();

	//	personal page
		$companyData	= Configure::read('Config.Company.data');
		$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $userGroupID);

		$property = $this->User->Property->getData('first', array(
			'fields' => array(
				'Property.id',
				'Property.on_progress_kpr',
				'Property.price',
				'Property.price_measure',
				'Property.property_type_id',
				'Property.mls_id',
				'Property.user_id',
				'Property.keyword',
			),
			'conditions' => array(
				'Property.id' => $property_id,
			),
			'order' => false,
		), array(
			'company' => empty($isIndependent), 
		));

		if( !empty($property) ) {
			$property = $this->User->Property->getMergeList($property, array(
				'contain' => array(
					'PropertyAddress' => array(
						'contain' => array(
							'Region',
							'City',
							'Subarea',
						),
					),
					'PropertyAsset',
					'PropertyFacility',
					'PropertyPointPlus',
				),
			));

			$bank_exclusive = $this->User->Kpr->Bank->isExclusive($bank_id);
			$on_progress_kpr = Common::hashEmptyField($property, 'Property.on_progress_kpr');
			
			if(!empty($property) && empty($on_progress_kpr) && !empty($bank_exclusive)){
				$data = $this->request->data;

				$kpr = $this->User->Kpr->getData('first', array(
					'conditions' => array(
						'Kpr.session_id' => $session_id,
					),
					'order' => false,
				));
				
				if( !empty($kpr) ) {
					$kpr_id = Common::hashEmptyField($kpr, 'Kpr.id');
					$kpr = $this->User->Kpr->KprBank->getMerge($kpr, $kpr_id, 'Kpr', 'all', array(
						'KprBank.document_status' => 'cart_kpr',
					));
				}

				$bank_setting_ids = Common::hashEmptyField($data, 'Kpr.id');
				$falg_duplicated = $this->RmKpr->bankDuplicated($bank_setting_ids);

				if(!empty($falg_duplicated)){
					$named = $this->RmCommon->dataConverter($named, array(
						'unset' => array(
							'id'
						),
					));

					$data = $this->RmCommon->dataConverter($data, array(
						'unset' => array(
							'Kpr' => array(
								'id',
							),
						),
					));
				}

				$value = $this->RmKpr->getSelectProduct($named, true, $kpr, $property);

				if(!empty($data)){
					$data = $this->RmCommon->dataConverter($data, array(
						'price' => array(
							'Kpr' => array(
								'down_payment'
							),
						),
					));

					$down_payment = Common::hashEmptyField($data, 'Kpr.down_payment');
					$dp = Common::hashEmptyField($data, 'Kpr.dp');
					$periode_installment = Common::hashEmptyField($data, 'Kpr.periode_installment');

					$result = $this->RmKpr->doSaveProduct($data, $value, array(
						'name_cookie' => $name_cookie,
						'session_id' => $session_id,
					));

					$status = Common::hashEmptyField($result, 'status');

					if(!empty($falg_duplicated)){
						$result['msg'] = __('Gagal, anda hanya bisa mengajukan satu promo dari setiap bank');

					}else if($status == 'success'){
					  	$bank_name = Common::hashEmptyField($bank_exclusive, 'Bank.name');
						$result['msg'] = __('Anda telah berhasil memilih promo. Mohon lengkapi form aplikasi KPR dibawah ini', $bank_name);
					}

					$named = array_merge($named, array(
						'dp' => $dp,
						'down_payment' => $down_payment,
						'periode_installment' => $periode_installment,
					));

					$this->RmCommon->setProcessParams($result, array_merge(array(
						'controller' => 'kpr',
						'action' => 'application_banks',
					), $named));
				}

				$bankName = Common::hashEmptyField($bank_exclusive, 'Bank.name');
				$named['dp'] = Common::hashEmptyField($value, 'BankSetting.dp');
				$this->request->data['Kpr'] = $named;

				$title = __('promo %s', $bankName);
				$this->set(array(
					'named' => $named,
					'property' => $property,
					'detail_banks' => true,
					'_flash' => false,
					'bank_exclusive' => $bank_exclusive,
					'title_for_layout' => $title,
					'module_title' => $title,
				));
			}else{
				$this->RmCommon->redirectReferer(__('data tidak ditemukan'));
			}
		}else{
			$this->RmCommon->redirectReferer(__('data tidak ditemukan'));
		}
	}


	function select_product(){
		$this->loadModel('BankProduct');
		$data = $this->request->data;
		$params = $this->params;

		$params['named'] = !empty($data['Kpr']) ? $data['Kpr'] : $params['named'];
		$named = $this->RmCommon->filterEmptyField($params, 'named');

		$property_id = $this->RmCommon->filterEmptyField($named, 'property_id');

	//	personal page
		$companyData	= Configure::read('Config.Company.data');
		$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $userGroupID);

		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $property_id,
			),
		), array(
			'company' => empty($isIndependent), 
		));

		$set_params = $this->BankProduct->getParamIndicator(array(
			'BankSetting' => array(
				'dp' => Configure::read('__Site.KPR.min_dp'),
				'periode_installment' => Configure::read('__Site.KPR.tenor')
			),
		), array(), $property);

		$set_dp = $this->RmCommon->filterEmptyField($set_params, 'BankSetting', 'dp');
		$set_tenor = $this->RmCommon->filterEmptyField($set_params, 'BankSetting', 'periode_installment');
		
		// load css bank
		$this->RmCommon->_layout_file(array(
			'bank',
		));

		$property = $this->User->Property->getMergeList($property, array(
			'contain' => array(
				'PropertyAddress' => array(
					'contain' => array(
						'Region',
						'City',
						'Subarea',
					),
				),
				'PropertyAsset',
				'PropertyFacility',
				'PropertyPointPlus',
			),
		));

		$property_price = $this->RmCommon->filterEmptyField($property, 'Property', 'price_measure');
		$property_action_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_action_id');
		$property_type_id = $this->RmCommon->filterEmptyField($property, 'Property', 'property_type_id');
		$on_progress_kpr = $this->RmCommon->filterEmptyField($property, 'Property', 'on_progress_kpr');

		if($property_action_id == 1 && in_array($property_type_id, array(1, 3, 7)) && empty($on_progress_kpr)){
			$name_cookie = sprintf('filling_%s', $property_id);
			$session_id = $this->Cookie->read($name_cookie);
			$session_id = !empty($session_id) ? $session_id : String::uuid();

			$kpr = $this->User->Kpr->getData('first', array(
				'conditions' => array(
					'Kpr.session_id' => $session_id,
				),
			));
			
			$down_payment = $this->RmCommon->filterEmptyField($named, 'down_payment');
			$named['down_payment'] = !empty($down_payment) ? $down_payment : $this->RmKpr->_callCalcDp($property_price, $set_dp);
			$named['dp'] = $this->RmKpr->_callDpPercent($property_price, $named['down_payment']);

			$credit_total = $this->RmCommon->filterEmptyField($named, 'periode_installment');
			$named['periode_installment'] = !empty($credit_total) ? $credit_total : $set_tenor;

			$named = $this->RmCommon->dataConverter($named, array(
				'price' => array(
					'down_payment'
				),
			)); 
			
			$kpr_id = $this->RmCommon->filterEmptyField($kpr, 'Kpr', 'id');
			$kpr = $this->User->Kpr->KprBank->getMerge($kpr, $kpr_id, 'Kpr', 'all');
			$bank_setting_ids = $this->RmCommon->filterEmptyField($data, 'Kpr', 'id');
			$falg_duplicated = $this->RmKpr->bankDuplicated($bank_setting_ids);

			if(!empty($falg_duplicated)){
				$named = $this->RmCommon->dataConverter($named, array(
					'unset' => array(
						'id'
					),
				));

				$data = $this->RmCommon->dataConverter($data, array(
					'unset' => array(
						'Kpr' => array(
							'id',
						),
					),
				));
			}

			$value = $this->RmKpr->getSelectProduct($named, true, $kpr, $property);

			if(!empty($value)){
				if(!empty($data)){
					$result = $this->RmKpr->doSaveProduct($data, $value, array(
						'name_cookie' => $name_cookie,
						'session_id' => $session_id,
					));
					$status = $this->RmCommon->filterEmptyField($result, 'status');

					if(!empty($falg_duplicated)){
						$result['msg'] = __('Gagal, anda hanya bisa mengajukan satu promo dari setiap bank');
					} else if($status == 'success'){
						$result['msg'] = __('Anda telah berhasil memilih Bank. Mohon lengkapi form aplikasi KPR dibawah ini');
					}

					$this->RmCommon->setProcessParams($result, array_merge(array(
						'controller' => 'kpr',
						'action' => 'application_product',
					), $named));
				}

				$this->request->data = array(
					'Kpr' => $named,
				);

				$title = __('Pilih Bank KPR');
				$this->set(array(
					'kpr' => $kpr,
					'view_name_product' => true,
					'value' => $value,
					'named' => $named,
					'property' => $property,
					'select_product' => 'active',
					'title_for_layout' => $title,
					'module_title' => $title,
				));
			}else{
				$this->RmCommon->redirectReferer(__('Promo bank tidak ditemukan'));
			}
		}else{
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'));
		}
	}

	function detail_installment($mls_id = false, $setting_id = false){
	//	personal page
		$companyData	= Configure::read('Config.Company.data');
		$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $userGroupID);

		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.mls_id' => $mls_id,
			),	
			'fields' => array(
				'Property.id',
				'Property.price_measure',
			),
			'order' => false,
		), array(
			'status' => 'active-pending-sold',
			'restrict_type' => 'mine',
			'company' => empty($isIndependent),
			'skip_is_sales' => true,
		));

		$bank_setting = $this->User->Kpr->Bank->BankSetting->getData('first', array(
			'conditions' => array(
				'BankSetting.id' => $setting_id,
			),
			'fields' => array(
				'BankSetting.id',
				'BankSetting.bank_id',
				'BankSetting.product_id',
				'BankSetting.interest_rate_fix',
				'BankSetting.periode_fix',
				'BankSetting.interest_rate_cabs',
				'BankSetting.periode_cab',
				'BankSetting.interest_rate_float',
			),
		), array(
			'type' => 'all',
		));

		if(!empty($property) && !empty($bank_setting)){
			$bank_setting = $this->User->Kpr->Bank->BankSetting->getMergeList($bank_setting, array(
				'contain' => array(
					'Bank' => array(
						'fields' => array(
							'Bank.id',
							'Bank.name',
						),
					),
					'BankProduct' => array(
						'fields' => array(
							'BankProduct.id',
							'BankProduct.name',
							'BankProduct.text_promo',
						),
					),
				),
			));

			$params = $this->params->params;
			$kpr_data = $this->RmKpr->beforeViewInstallmentDetail($property, $bank_setting, $params);

			$this->set(array(
				'kpr_data' => $kpr_data,
				'bank_setting' => $bank_setting,
				'property' => $property,
				'title_for_layout' => 'detail cicilan'
			));
		}else{
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'));
		}
	}

	function bank_calculator( $periode_installment = false, $interest_rate_fix = false ) {
		$mls_id = $this->RmCommon->filterEmptyField($this->params, 'named', 'mls_id');

		$property = $this->User->Property->getData('first', array(
        	'conditions' => array(
        		'Property.mls_id' => $mls_id,
    		),
    	), array(
			'status' => 'active-pending-sold',
			'skip_is_sales' => true,
    	));

		if(!empty($property)){
			$params = $this->params;
			$bank_code = $this->RmCommon->filterEmptyField($params, 'bank_code'); // Slug di Routing
			$print = $this->RmCommon->filterEmptyField($params, 'named', 'export');
			$apply = $this->RmCommon->filterEmptyField($params, 'named', 'apply');
			$bankKpr = $bank = $this->User->Kpr->KprBank->Bank->getKpr($bank_code,false);
			$bank_id = $this->RmCommon->filterEmptyField($bank, 'Bank', 'id');
			$bankKpr = $this->User->Kpr->KprBank->Bank->BankContact->getMerge($bankKpr, $bank_id);
			$excel = ($print == 'excel')?true:false;
			$loan_summary = array();

			$bankApplyCategories = $this->User->Kpr->BankApplyCategory->getList();
			$jobTypes = $this->User->Kpr->KprApplication->JobType->getList();

	    	$property = $this->User->Property->getDataList($property, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertySold',
				),
			));

			$data = $this->request->data;

			if( !empty($data) ) {
				$apply = $this->RmCommon->filterEmptyField($data, 'Kpr', 'apply');
				$this->RmCommon->_callRequestSubarea('Kpr.KprApplication');
				$kpr = $this->RmCommon->filterEmptyField($data, 'Kpr');
				$terms_condition = $this->RmCommon->filterEmptyField($kpr, 'KprApplication', 'terms_condition');

				$data = $this->RmKpr->beforeSaveFrontEnd($data, $mls_id, array(
					'bank' => $bank,
					'property' => $property,
				));	

				if(!empty($data['CrmProjectDocument'])){
					$documents['CrmProjectDocument'] = $data['CrmProjectDocument'];
					$data = $this->RmCommon->_callUnset( array(
						'CrmProjectDocument'
					), $data);
				}
				$check_exist = $this->RmKpr->checkExistKpr($data);
		        $result = $this->User->Kpr->doSaveAllFrontEnd($data, $terms_condition, $property, $check_exist);
		        $status = $this->RmCommon->filterEmptyField($result, 'status');

		        if( $status == 'success' ) {
		        	$id = $this->RmCommon->filterEmptyField($result, 'id');
		        	if(!empty($documents['CrmProjectDocument'])){
		        		foreach($documents['CrmProjectDocument'] AS $key => $document){
		        			$document['CrmProjectDocument']['owner_id'] = !empty($id)?$id:0;
		        			$documents['CrmProjectDocument'][$key] = $document;
		        		}
		        	}

					if(!empty($documents['CrmProjectDocument'])){
						$resultDocument = $this->User->CrmProject->CrmProjectDocument->doSaveMany($documents['CrmProjectDocument'], false, true);
						$log_msg = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'activity');
						$old_data = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'old_data');
						$document_id = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'document_id');
						$error = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'error');
						$code_error = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'code_error');
						$validation_data = $this->RmCommon->filterEmptyField($resultDocument, 'Log', 'validation_data');
		    			$this->RmCommon->_saveLog($log_msg, $old_data, $document_id, $error, $code_error, $validation_data);
					}

					$label = $this->RmProperty->getNameCustom($property);
					$slug = $this->RmCommon->toSlug($label);

					$url = array(
	                    'controller'=> 'properties', 
	                    'action' => 'detail',
	                    'mlsid' => $mls_id,
	                    'slug'=> $slug, 
	                    'admin'=> false,
	                );
			       	$this->RmCommon->setProcessParams($result, $url);	
		        }

				if( $this->RequestHandler->isAjax() ){
					$this->set(compact(
						'result', 'bankApplyCategories', 'jobTypes'
					));
					$this->render('/Elements/blocks/kpr/ajax_save_kpr');
				} else {
					$this->RmCommon->setCustomFlash($result['msg'], $result['status']);
				}
			} else if($this->user_id) {
				$this->request->data = $this->RmKpr->fill_form();
				$this->RmCommon->_callRequestSubarea('Kpr.KprApplication');
			}

			$apply_kpr = ($apply == 'kpr')?true:false;

			$this->request->data['Kpr'] = $loan_summary['Kpr'] = $this->RmKpr->viewBankCalculator($data, $bank, $property, array(
				'periode_installment' => $periode_installment,
				'interest_rate_fix' => $interest_rate_fix,
			));

			$kpr_data = $this->RmKpr->calculate_kpr_installment_detail( $bankKpr, $this->request->data );

			$this->Set(array(
				'excel' => $excel,
				'mls_id' => $mls_id,
				'bankKpr' => $bankKpr,
				'kpr_data' => $kpr_data,
				'jobTypes' => $jobTypes,
				'apply_kpr' => $apply_kpr,
				'property' => $property,
				'loan_summary' => $loan_summary,
				'module_title' => __('Kalkulator KPR'),
				'interest_rate_fix' => $interest_rate_fix,
				'bankApplyCategories' => $bankApplyCategories,
				'periode_installment' => $periode_installment,
				'title_for_layout' => __('Kalkulator KPR'),
			));

			if( !empty($excel) ) {
				$this->layout = false;
				$this->render('bank_calculator_excel');
			}
		}else{
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'));
		}
	}

	function saveUserOnApply( $new_user ) {
        $new_user['User'] = $this->RmCommon->_callSet(array(
			'name',
			'email',
			'gender_id',
			'birthday',
			'address',
			'address_2',
			'region_id',
			'city_id',
			'subarea_id',
			'zip',
			'phone',
			'no_hp',
			'no_hp_2',
			'first_name',
			'last_name',
			'user_id',
		), $new_user['User'] );
		$birthday = $this->RmCommon->getDate($new_user['User']['birthday']);
		
		$new_user['User']['full_name'] = $this->RmCommon->filterEmptyField($new_user, 'User', 'name', false);
		$new_user['User']['birthday'] = !empty($birthday)?$birthday:NULL;

		$new_address = str_replace(PHP_EOL, ' ', $this->RmCommon->filterEmptyField($new_user, 'User', 'address_2', false));
		$new_user['User']['address'] = $new_address;
		unset($new_user['User']['address2']);

		$filtered_field = array(
			'user_id',
			'gender_id',
			'email',
			'name',
			'first_name',
			'last_name',
			'full_name',
		);
		$new_user['UserProfile'] = $this->RmCommon->_callUnset( $filtered_field, $new_user['User'] );
		$new_user['User'] = $this->RmCommon->_callSet( $filtered_field, $new_user['User'] );

		$this->RmCommon->registerUser( $new_user );
	}

	function share_kpr($kpr_bank_id = false){
		if(!empty($kpr_bank_id)){
			$this->loadModel('SharingKpr');
			$is_ajax = $this->RequestHandler->isAjax();
			$log_kpr = $this->SharingKpr->KprBank->getFirstData($kpr_bank_id);
			$bank_id = $this->RmCommon->filterEmptyField($log_kpr, 'KprBank', 'bank_id');
			$log_kpr = $this->SharingKpr->KprBank->Bank->getMerge($log_kpr, $bank_id);
			$data = $this->request->data;

			if( !empty($data) ){
				$mls_id = false;
				
				if( !empty($this->params['named']['mls_id'])) {
					$mls_id = $this->params['named']['mls_id'];
					$data['SharingKpr']['mls_id'] = $mls_id;
				}
				
				if(!empty($kpr_bank_id)){
					$data['SharingKpr']['kpr_bank_id'] = $kpr_bank_id;
				}

				$result = $this->SharingKpr->doSave($data, $log_kpr);
				$status = $this->RmCommon->filterEmptyField($result, 'status');

				if( empty($is_ajax) ){
					$this->RmCommon->setProcessParams($result, array(
						'controller' => 'kpr',
						'action' => 'share_kpr',
						$log_kpr_id,
						$mls_id,
						'admin' => false,
					));
				} 
				if( $status == 'success' ) {
					$this->RmCommon->setProcessParams($result, false, array(
						'ajaxFlash' => true,
						'ajaxRedirect' => false,
					));
				}
			} else {
				if( $this->user_id ) {
					$this->request->data['SharingKpr']['sender_name'] = $this->Auth->user('full_name');
				}
			}
			$this->set('module_title', __('Bagikan Info KPR'));
			$this->set('_flash', false);
			$this->set('captcha_code', $this->Captcha->generateEquation());
			$this->set(compact(
				'is_ajax', 'log_kpr', 'result'
			));

			if( $is_ajax ) {
				$this->render('/Ajax/share_kpr');
			}
		}else{
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'), 'error');
		}
		
	}

	function admin_add ( $crm_project_id = false ) {
		$params = $this->params->params;

		$module_title = __('Pengajuan KPR');
		$data = $this->request->data;
		$value = false;
		$dataSave = array();
		
		$property_id = Common::hashEmptyField($params, 'named.property');
		$property_id = Common::hashEmptyField($data, 'Property.id', $property_id);
		$kpr_application_id = Common::hashEmptyField($params, 'named.kpr_application_id');

		if(!empty($crm_project_id) || !empty($property_id)){
			$value = $this->User->Kpr->KprFromCrm($value, array(
				'crm_project_id' => $crm_project_id,
				'property_id' => $property_id,
			));
		}else if(!empty($kpr_application_id)){
			$value = $this->User->Kpr->KprApplication->kprFromApplication($kpr_application_id);
		}

		if( !empty($data) ) {
			$data['Kpr']['crm_project_id'] = Common::hashEmptyField($value, 'CrmProject.id');
			$data['Kpr']['session_id'] = String::uuid();

			$dataSave = $this->RmKpr->_callBeforeSave($data);
			$result = $this->User->Kpr->doSaveAll( $dataSave );

			$id 			= Common::hashEmptyField($result, 'id');
			$result_data 	= Common::hashEmptyField($result, 'Log.data');
			$client_id 		= Common::hashEmptyField($result_data, 'Kpr.user_id');
 			$status 		= Common::hashEmptyField($result, 'status');
 			
			$dataSave = $this->RmKpr->buildDocument($dataSave, array(
				'save_path' => Configure::read('__Site.document_folder'),
				'owner_id' => !empty($id)?$id:0,
				'document_type' => 'kprs',
				'options' => array(
					'crm_project_id' => $crm_project_id,
					'property_id' => $property_id,
					'kpr_id' => $id,
					'client_id' => $client_id,
				),
			));
			$this->request->data['CrmProjectDocument'] = Common::hashEmptyField($dataSave, 'CrmProjectDocument');

 			if( ($status == 'success') && !empty($id) ){
				$project_document 		= Common::hashEmptyField($dataSave, 'CrmProjectDocument', array());
				$project_document_mstr 	= Common::hashEmptyField($dataSave, 'CrmProjectDocumentMstr', array());

				$dataDocument = array_merge($project_document,$project_document_mstr);
				
				if(!empty($dataDocument)){
					$resultDocument = $this->User->CrmProject->CrmProjectDocument->doSaveMany($dataDocument, false, true);

					$log_msg 			= Common::hashEmptyField($resultDocument, 'Log.activity');
					$old_data 			= Common::hashEmptyField($resultDocument, 'Log.old_data');
					$document_id 		= Common::hashEmptyField($resultDocument, 'Log.document_id');
					$error 				= Common::hashEmptyField($resultDocument, 'Log.error');
					$code_error 		= Common::hashEmptyField($resultDocument, 'Log.code_error');
					$validation_data 	= Common::hashEmptyField($resultDocument, 'Log.validation_data');

        			$this->RmCommon->_saveLog($log_msg, $old_data, $document_id, $error, $code_error, $validation_data);
				}
 			}

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'kpr',
				'action' => 'index',
				'admin' => true,
			));
		}

		$this->RmKpr->_callBeforeView($value, $dataSave);

		$documentCategories = $this->RmKpr->getDocumentSort(array(
			'DocumentCategory.is_required' => 1,
			'DocumentCategory.id' => Configure::read('__Site.Global.Variable.KPR.document_client'),
		), array(
			'document_type' => 'kpr_application',
		), $value);
		
		$this->set(compact(
			'module_title', 'documentCategories'
		));

		$this->render('admin_add_ver2');
	}

	public function admin_edit( $id = false ) {
		$value = $this->User->Kpr->getData('first', array(
			'conditions' => array(
				'Kpr.id' => $id,
			),
		), array(
			'admin_mine' => true,
		));
		$user_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id');
		$value = $this->User->getMerge($value, $user_id);
		$value = $this->User->UserClient->getMerge($value, $user_id);

		if( !empty($value) ) {
			$step = 'Basic';
			$module_title = __('Aplikasi KPR');
			$data = $this->request->data;

			$client_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id');
			$crm_project_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'crm_project_id');

			if( !empty($data) ) {
				$data['Kpr']['crm_project_id'] = $this->RmCommon->filterEmptyField($value, 'Kpr', 'crm_project_id');
				$data = $this->RmKpr->_callBeforeSave($data, $id);
				$result = $this->User->Kpr->doSave( $data, $id );

				$property_id = $this->RmCommon->filterEmptyField($data, 'Property', 'id');
				$id = $this->RmCommon->filterEmptyField($result, 'id');
				$result_data = $this->RmCommon->filterEmptyField($result, 'log', 'data');
	 			$status = $this->RmCommon->filterEmptyField($result, 'status');

				$data = $this->RmKpr->buildDocument($data, array(
					'save_path' => Configure::read('__Site.document_folder'),
					'owner_id' => !empty($id)?$id:0,
					'client_id' => $client_id,
					'document_type' => 'kprs',
					'options' => array(
						'kpr_id' => $id,
						'crm_project_id' => $crm_project_id,
						'property_id' => $property_id,
					),
				));

	 			if( $status == 'success' ) {
					$project_document = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocument', false, array());
					$project_document_mstr = $this->RmCommon->filterEmptyField($data, 'CrmProjectDocumentMstr', false, array());

					$dataDocument = array_merge($project_document,$project_document_mstr);

					if(!empty($dataDocument)){
						$resultDocument = $this->User->CrmProject->CrmProjectDocument->doSaveMany($dataDocument, false, true);
						$this->RmCommon->setProcessParams($resultDocument, false, array(
							'noRedirect' => true,
							'flash' => false,
						));
					}

					$this->RmCommon->setProcessParams($result, array(
						'controller' => 'kpr',
						'action' => 'filing',
						$id,
						'admin' => true,
					));
				}
			}
			$this->RmKpr->_callBeforeView($data, $value);
			$this->set(array(
				'module_title' => $module_title,
				'id' => $id,
				'value' => $value,
				'step' => $step,
			));

			$this->render('admin_add');
		} else {
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'));
		}
	}

	function admin_application_detail_excel($kpr_bank_id = false){
		$params = $this->params;
		$export = $this->RmCommon->filterEmptyField($params, 'named', 'export');
		$print = $this->RmCommon->filterEmptyField($params, 'named', 'print');

		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $kpr_bank_id,
			),
		));

		$value = $this->User->Kpr->KprBank->getMergeList($value, array(
			'contain' => array(
				'Kpr' => array(
					'conditions' => array(
						'document_status <>' => 'cart_kpr',
					),
					'elements' => array(
						'admin_mine' => true,
					),
				),
			),
		));

		if(!empty($value['Kpr'])){

			$kpr_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
			$client_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'user_id');
			$property_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_id');
			$agent_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'agent_id');
			$company_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'company_id');

			$value = $this->User->Kpr->KprBank->KprBankInstallment->getMerge($value, $kpr_bank_id, array(
				'fieldName' => 'Kpr_bank_id',
				'order' => array(
					'KprBankInstallment.status_confirm' => 'DESC',
				),
				'elements' => array(
					'status' => 'all',
				),
			));

			$value = $this->User->getMerge($value, $agent_id, false, 'Agent');
			$value = $this->User->UserCompany->getMerge($value, $company_id);
			$value = $this->User->Property->getMerge($value, $property_id);
			$value = $this->User->Property->PropertyAddress->getMerge($value, $property_id, true);
			$bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'bank_id');
			$value = $this->User->Kpr->KprBank->Bank->getMerge($value, $bank_id);
			$value = $this->User->Kpr->KprBank->Bank->BankConfirmation->getMerge($value, $bank_id, 'BankConfirmation.bank_id');

			$value['Document']['Application'] = $this->RmKpr->getDocumentSort(
				array(
					'DocumentCategory.is_required' => 1,
					'DocumentCategory.id <>' => array( 3, 7, 19, 20),
				), array(
					'id' => $kpr_id,
					'owner_id' => !empty($client_id)?$client_id:0,
					'property_id' => $property_id,
					'document_type' => 'kpr_application',
			), $value);

			$value['Document']['ApplicationSpouse'] = $this->RmKpr->getDocumentSort(
				array(
					'DocumentCategory.is_required' => 1,
					'DocumentCategory.id <>' => array( 1, 2, 3, 4, 5, 6, 7, 15, 16, 17),
				), array(
					'id' => $kpr_id,
					'owner_id' => !empty($client_id)?$client_id:0,
					'document_type' => 'kpr_spouse_particular',
			), $value);

			$this->layout = false;
			$this->set(array(
				'value' => $value,
				'export' => $export,
				'print' => $print,
			));
			$this->render('admin_application_detail_excel');
		} else {
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'));
		}
	}

	function admin_application ( $id = false , $kpr_bank_id = false) {
		$value = $this->User->Kpr->getMerge(array(), $id, 'Kpr.id', array(
			'elements' => array(
				'admin_mine' => true,
			),
		));
		$data = $this->request->data;

		if( !empty($value) ) {
			$value = $this->User->Kpr->KprApplication->mergeApplication($value, $id, true);
			$application = true;
			$step = 'Application';
			$module_title = __('Aplikasi KPR');

			$doc_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'last_kpr_id', $id);
			$client_id = Common::hashEmptyField($value, 'Kpr.user_id');	
			$regions = $this->User->Kpr->KprApplication->Region->getSelectList();
			$jobTypes = $this->User->Kpr->KprApplication->JobType->getList();
			## CEK APLIKASI CLIENT PERNAH MENGAJUKAN SEBELUMYA ?
			$this->RmKpr->_callCookieNotice($id, 'application_form');

			if( !empty($data) ) {
				$data['Kpr']['id'] = $id;

				$data = $this->RmKpr->filterKprApplicationParticular($data, $value, $id);
				$data = $this->RmKpr->_callBeforeSaveKprApplication($data, $value);

				$saveDocument = $this->User->CrmProject->CrmProjectDocument->doBeforeSave($data);
	 			$status_document = $this->RmCommon->filterEmptyField($saveDocument, 'status');
	 			$msg_error = $this->RmCommon->filterEmptyField($saveDocument, 'msg');
				$this->RmCommon->setProcessParams( $saveDocument, false, array(
					'flash' => false,
					'noRedirect' => true,
				));

				$data = $this->RmCommon->_callUnset(array(
					'CrmProjectDocument',
					'CrmProjectDocumentMstr',
					'ParticularDocument'
				), $data);

				$msg = __('melakukan pengisian aplikasi KPR');
				$result = $this->User->Kpr->KprApplication->doSaveAll( $data, $id, false, $msg, $status_document );
	 			$msg_error = $this->RmCommon->filterEmptyField($result, 'msg', false, $msg_error);
				$result_id = $this->RmCommon->filterEmptyField($result, 'id');
				$status = $this->RmCommon->filterEmptyField($result, 'status');

				if( $status == 'success' && $status_document == 'success' ) {
	 				$dataDocument = $this->RmCommon->filterEmptyField($saveDocument, 'DataDocument');
					$this->User->CrmProject->CrmProjectDocument->doSaveMany($dataDocument, false, true);
					$this->User->Kpr->updateApplication($data, $id);

					if($kpr_bank_id){
						$url_direct = array(
							'controller' => 'kpr',
							'action' => 'application_detail',
							'admin' => TRUE,
							$kpr_bank_id,
						);
					}else{
						$url_direct = array(
							'controller' => 'kpr',
							'action' => 'index',
							'admin' => true,
						);
					}
					$this->RmCommon->setProcessParams( $result, $url_direct);
					
				} else {
					$this->RmCommon->setProcessParams($result);
				}
			}else{
				$value = $this->User->Kpr->KprApplication->lastApplyKpr($value);
			}
			
			$documentCategories = $this->RmKpr->getDocumentSort(array(
				'DocumentCategory.is_required' => 1,
				'DocumentCategory.id <>' => array( 3, 7, 19, 20),
			), array(
				'id' => $doc_id,
				'owner_id' => $client_id,
				'document_type' => 'kpr_application',
			), $value);

			$documentCategoriesSpouse = $this->RmKpr->getDocumentSort( 
				array(
					'DocumentCategory.is_required' => 1,
					'DocumentCategory.id <>' => array( 1, 2, 3, 4, 5, 6, 7, 15, 16, 17),
				), array(
				'id' => $doc_id,
				'owner_id' => $client_id,
				'document_type' => 'kpr_spouse_particular',
			), $value);
			$this->request->data = $this->RmKpr->_callBeforeViewKprApplication( $data, $value);
			$bankApplyCategories = $this->User->Kpr->BankApplyCategory->getData('list', array(
				'fields' => array(
					'BankApplyCategory.id', 'BankApplyCategory.category_name', 
				),
			));

			$this->RmCommon->_callRequestSubarea('KprApplication', TRUE);
			$this->RmCommon->_callDataForAPI($value, 'manual');

			$this->set(compact(
				'module_title', 'value', 'id',
				'step', 'application', 'documentCategories', 
				'documentCategoriesSpouse', 'jobTypes', 
				'regions', 'bankApplyCategories'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'));
		}
	}

	function admin_filing ( $id = false ) {
		$admin_prime = Configure::read('User.Admin.Rumahku');
		$value = $this->User->Kpr->getData('first', array(
			'conditions' => array(
				'Kpr.id' => $id,
			),
		), array(
			'available' => true,
			'admin_mine' => !empty($admin_prime)?false:true,
			'company' => !empty($admin_prime)?false:true,
		));

		if( !empty($value) ) {
			$value = $this->RmKpr->checkExistingKPR($value);
			$kpr_existing_count = Common::hashEmptyField($value, 'Kpr.kpr_existing_count');

			if( !empty($kpr_existing_count) ) {
				$this->RmCommon->redirectReferer(__('Mohon maaf, properti yang anda ingin ajukan KPR sudah diajukan oleh klien lain.'), 'error', array(
					'controller' => 'kpr',
					'action' => 'index',
					'admin' => true,
				));
			} else {
				$bank_setting_ids = false;
				$availableKpr = $this->RmProperty->availableKpr($value);

				if($availableKpr){
					$step = 'Bank';
					$module_title = __('Pengajuan KPR');
					$kpr = $this->RmCommon->filterEmptyField($value, 'Kpr', false, array());
					$property_id = $this->RmCommon->filterEmptyField($kpr, 'property_id');
					$crm_project_id = $this->RmCommon->filterEmptyField($kpr, 'crm_project_id');

					## GET VALUE KPR APPLICATION AND KPR APPLICATION PASANGAN/PARTICULAR
					$value['Kpr'] = $this->User->Kpr->KprApplication->mergeApplication( $value['Kpr'], $id);
					$value = $this->User->Property->getMerge($value, $property_id, true);
					$value = $this->User->Property->PropertyAddress->getMerge($value,$property_id);
					$value = $this->User->Kpr->KprBank->getMergeAll($value, $id);
					$value = $this->User->Property->getDataList($value, array(
			            'contain' => array(
                			'MergeDefault',
			            	'PropertyAsset',
                			'User',
		            	),
			        ));

					## CHECK KPR FROM CRM
					$value = $this->User->Kpr->CrmProject->getMerge($value, $crm_project_id);
					$crm_project = $this->RmCommon->filterEmptyField($value, 'CrmProject');
					$value = $this->User->Kpr->CrmProject->CrmProjectPayment->getMerge($value, $crm_project_id,'CrmProjectPayment.crm_project_id');
					##############################
					$data = $this->request->data;

					if( !empty($data) ) {
						$bank_setting_ids = $this->RmCommon->filterEmptyField($data, 'Bank', 'id');
						$flag_duplicated = $this->RmKpr->bankDuplicated($bank_setting_ids);

						if(empty($flag_duplicated)){
							$dataSave = $this->RmKpr->_saveKprBank($data, $value);
							$result = $this->User->Kpr->KprBank->doSaveAll( $dataSave, $id, $value );
						}else{
							$result = array(
								'msg' => __('Anda hanya bisa mengajukan satu promo dari setiap bank'),
								'status' => 'error',
							);

							$data = $this->RmCommon->dataConverter($data, array(
								'unset' => array(
									'Bank',
								),
							));
						}

						$this->RmCommon->setProcessParams($result, array(
							'controller' => 'kpr',
							'action' => 'index',
							'admin' => true,
						));

						if(!empty($value['KprBank'])){
							$data['KprBank'] = $value['KprBank'];
						}
					}

					$data = (empty($data) && !empty($value))?$value:$data;
					$this->request->data = $data;

					$banks = $this->User->Kpr->callGetBank($data, $bank_setting_ids);

					$this->set(array(
						'active_menu' => 'kpr_add',
						'module_title' => $module_title,
						'value' => $value,
						'id' => $id,
						'banks' => $banks,
						'show_banks' => true,
					));
				}else{
					$this->RmCommon->redirectReferer(__('Mohon maaf, properti yang anda ingin ajukan KPR sudah diajukan oleh klien lain.'));
				}
			}
		} else {
			$this->RmCommon->redirectReferer(__('Mohon lengkapi terlebih dahulu tahap informasi properti, kemudian klik lanjut untuk beralih ke halaman berikutnya'));
		}
	}

	function admin_filing_developer ( $id = false ) {
		$admin_prime = Configure::read('User.Admin.Rumahku');
		$value = $this->User->Kpr->getData('first', array(
			'conditions' => array(
				'Kpr.id' => $id,
			),
		), array(
			'available' => true,
			'admin_mine' => !empty($admin_prime)?false:true,
			'company' => !empty($admin_prime)?false:true,
		));

		if( !empty($value) ) {
			$value = $this->RmKpr->checkExistingKPR($value);
			$kpr_existing_count = Common::hashEmptyField($value, 'Kpr.kpr_existing_count');

			if( !empty($kpr_existing_count) ) {
				$this->RmCommon->redirectReferer(__('Mohon maaf, properti yang anda ingin ajukan KPR sudah diajukan oleh klien lain.'), 'error', array(
					'controller' => 'kpr',
					'action' => 'index',
					'admin' => true,
				));
			} else {
				$bank_setting_ids = false;
				$module_title = __('Pengajuan KPR');

				$value = $this->Kpr->callMergeList($value, array(
					'contain' => array(
						'InvoiceCollector' => array(
							'foreignKey' => 'booking_code',
							'primaryKey' => 'booking_code',
						),
						'KprApplication' => array(
							'order'=> array(
								'KprApplication.parent_id' => 'ASC',
								'KprApplication.created' => 'ASC',
							),
						),
						'KprBank' => array(
							'type' => 'list',
							'fields' => array(
								'KprBank.bank_id',
							),
						),
					),
				));
				$value = $this->User->Kpr->_callMergeProperty($value);

				$data = $this->request->data;

				if( !empty($data) ) {
					$bank_setting_ids = $this->RmCommon->filterEmptyField($data, 'Bank', 'id');
					$flag_duplicated = $this->RmKpr->bankDuplicated($bank_setting_ids);

					if(empty($flag_duplicated)){
						$dataSave = $this->RmKpr->_saveKprBank($data, $value);
						$result = $this->User->Kpr->doSaveDeveloper( $dataSave, $id );
					}else{
						$result = array(
							'msg' => __('Anda hanya bisa mengajukan satu promo dari setiap bank'),
							'status' => 'error',
						);

						$data = $this->RmCommon->dataConverter($data, array(
							'unset' => array(
								'Bank',
							),
						));
					}

					$this->RmCommon->setProcessParams($result, array(
						'controller' => 'kpr',
						'action' => 'index',
						'admin' => true,
					));

					if(!empty($value['KprBank'])){
						$data['KprBank'] = $value['KprBank'];
					}
				}

				$data = (empty($data) && !empty($value))?$value:$data;
				$this->request->data = $data;

				$booking_code = Common::hashEmptyField($value, 'InvoiceCollector.booking_code');
				$price = Common::hashEmptyField($value, 'Kpr.property_price');
				$bank_ids = Common::hashEmptyField($value, 'KprBank', array());
				$bank_ids = implode(',', $bank_ids);

				$booking = $this->RmKpr->_callBooking($booking_code);
				$project_id = Common::hashEmptyField($booking, 'project.id');
				$company_id = Common::hashEmptyField($booking, 'project.company_id');

				
				$records = $this->RmCommon->getAPI(array(
					'controller' => 'kpr',
					'action' => 'bank_promo',
					'admin' => false,
					'ext' => 'json',
				), array(
		            'header' => array(
		                'slug' => 'api-bank-list-promo',
		                'data' => array(
			            	'project' => $project_id,
		            	),
		            ),
					'get' => array(
			            'price' => $price,
			            'bank_existing' => $bank_ids,
					),
		        ));
				$banks['qualify'] = Common::hashEmptyField($records, 'data');

				$this->set(array(
					'active_menu' => 'kpr_add',
					'module_title' => $module_title,
					'value' => $value,
					'id' => $id,
					'banks' => $banks,
					'show_banks' => true,
				));
				$this->render('admin_filing');
			}
		} else {
			$this->RmCommon->redirectReferer(__('Mohon lengkapi terlebih dahulu tahap informasi properti, kemudian klik lanjut untuk beralih ke halaman berikutnya'));
		}
	}

	function api_filing ( $id = false ) {
		$admin_prime = Configure::read('User.Admin.Rumahku');
		$value = $this->User->Kpr->getData('first', array(
			'conditions' => array(
				'Kpr.id' => $id,
			),
		), array(
			'available' => true,
			'admin_mine' => !empty($admin_prime)?false:true,
			'company' => !empty($admin_prime)?false:true,
		));

		if( !empty($value) ) {
			$value = $this->RmKpr->checkExistingKPR($value);
			$kpr_existing_count = Common::hashEmptyField($value, 'Kpr.kpr_existing_count');

			if( !empty($kpr_existing_count) ) {
				$this->RmCommon->redirectReferer(__('Mohon maaf, properti yang anda ingin ajukan KPR sudah diajukan oleh klien lain.'), 'error', array(
					'controller' => 'kpr',
					'action' => 'index',
					'admin' => true,
				));
			} else {
				$bank_setting_ids = false;
				$availableKpr = $this->RmProperty->availableKpr($value);

				if($availableKpr){
					$step = 'Bank';
					$module_title = __('Pengajuan KPR');
					$kpr = $this->RmCommon->filterEmptyField($value, 'Kpr', false, array());
					$crm_project_id = $this->RmCommon->filterEmptyField($kpr, 'crm_project_id');

					## GET VALUE KPR APPLICATION AND KPR APPLICATION PASANGAN/PARTICULAR
					$value['Kpr'] = $this->User->Kpr->KprApplication->mergeApplication( $value['Kpr'], $id);
					$value = $this->User->Kpr->KprBank->getMergeAll($value, $id);
					$value = $this->User->Kpr->_callMergeProperty($value);
					$value = $this->User->Property->getDataList($value, array(
			            'contain' => array(
                			'User',
		            	),
			        ));

					## CHECK KPR FROM CRM
					$value = $this->User->Kpr->CrmProject->getMerge($value, $crm_project_id);
					$crm_project = $this->RmCommon->filterEmptyField($value, 'CrmProject');
					$value = $this->User->Kpr->CrmProject->CrmProjectPayment->getMerge($value, $crm_project_id,'CrmProjectPayment.crm_project_id');
					##############################
					$data = $this->request->data;

					if( !empty($data) ) {
						$bank_setting_ids = $this->RmCommon->filterEmptyField($data, 'Bank', 'id');
						$flag_duplicated = $this->RmKpr->bankDuplicated($bank_setting_ids);

						if(empty($flag_duplicated)){
							$dataSave = $this->RmKpr->_saveKprBank($data, $value);
							$result = $this->User->Kpr->KprBank->doSaveAll( $dataSave, $id, $value );
						}else{
							$result = array(
								'msg' => __('Anda hanya bisa mengajukan satu promo dari setiap bank'),
								'status' => 'error',
							);

							$data = $this->RmCommon->dataConverter($data, array(
								'unset' => array(
									'Bank',
								),
							));
						}

						$this->RmCommon->setProcessParams($result, array(
							'controller' => 'kpr',
							'action' => 'index',
							'admin' => true,
						));

						if(!empty($value['KprBank'])){
							$data['KprBank'] = $value['KprBank'];
						}
					}

					$data_request = $data;
					$data = (empty($data) && !empty($value))?$value:$data;
					$this->request->data = $data;

					$banks = $this->User->Kpr->callGetBank($data, $bank_setting_ids);
					$value = $this->RmKpr->_buildDataFiling($value);

					$this->set(array(
						'data' => $value,
						'id' => $id,
						'banks' => $banks,
					));
				}else{
					$this->RmCommon->redirectReferer(__('Mohon maaf, properti yang anda ingin ajukan KPR sudah diajukan oleh klien lain.'));
				}
			}
		} else {
			$this->RmCommon->redirectReferer(__('Mohon lengkapi terlebih dahulu tahap informasi properti, kemudian klik lanjut untuk beralih ke halaman berikutnya'));
		}
	}

	public function admin_delete_kpr($id){
		$flag = false;
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id,
			)
		));
		$value = $this->User->Kpr->KprBank->getMergeList($value, array(
			'contain' => array(
				'Kpr' => array(
					'elements' => array(
						'admin_mine' => true,
					),
				),
			),
		));

		if(!empty($value['KprBank']) && !empty($value['Kpr'])){
			$bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'bank_id');
			$from_kpr = $this->RmCommon->filterEmptyField($value, 'KprBank', 'from_kpr');
			$application_snyc = $this->RmCommon->filterEmptyField($value, 'KprBank', 'application_snyc');
			$value = $this->User->Kpr->KprBank->Bank->getMerge( $value, $bank_id);
			$document_status = $this->RmCommon->filterEmptyField($value, 'KprBank', 'document_status');

			$flag = in_array($document_status, array('process', 'approved_bank', 'approved_credit', 'approved_verification'));
			$data = $this->request->data;

			if( $flag || (empty($application_snyc) && $document_status == 'proposal_without_comiission') ) {
				if( !empty($data) ) {
					$result = $this->User->Kpr->KprBank->doDeleteKpr( $value, $data );
					$this->RmCommon->setProcessParams($result, false, array(
						'ajaxFlash' => true,
					));
				}
			}else{
				$this->RmCommon->redirectReferer(__('Bank KPR tidak ditemukan'));
			}
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}

	}

	public function admin_application_detail( $kpr_bank_id = false ) {
		$area = array();

		$value = $this->User->Kpr->KprBank->getMergeAll(array(), $kpr_bank_id, array(
			'type' => 'first',
			'foreignKey' => 'KprBank.id',
			'with_contain' => TRUE,
			'slugIndex' => TRUE,
			'installmentElements' => array(
				'status' => 'all',
			)
		));

		$value = $this->User->Kpr->KprBank->getMergeList($value, array(
			'contain' => array(
				'KprBankTransfer',
				'KprBankCreditAgreement',
				'Kpr' => array(
					'conditions' => array(
						'document_status <>' => 'cart_kpr',
					),
					'elements' => array(
						'admin_mine' => true,
					),
				),
				'BankUser' => array(
					'BankUserProfile',
				),
			),
		));

		if( !empty($value['Kpr']) ) {
			$id = Common::hashEmptyField($value, 'Kpr.id');
			$client_id = Common::hashEmptyField($value, 'Kpr.user_id');
			$agent_id = Common::hashEmptyField($value, 'Kpr.agent_id');
			$company_id = Common::hashEmptyField($value, 'Kpr.company_id');
			$crm_project_id = Common::hashEmptyField($value, 'Kpr.crm_project_id');
			$bank_apply_category_id = Common::hashEmptyField($value, 'Kpr.bank_apply_category_id');
			$document_type = Common::hashEmptyField($value, 'Kpr.document_type');

			switch ($document_type) {
				case 'developer':
					$booking_code = Common::hashEmptyField($value, 'Kpr.booking_code');
					$booking = $this->RmKpr->_callBooking($booking_code);

					if( !empty($booking) ) {
						$value['Product'] = Common::hashEmptyField($booking, 'items');
					}
					break;
			}

			## GET VALUE KPR APPLICATION AND KPR APPLICATION PASANGAN/PARTICULAR
			$value['Kpr'] = $this->User->Kpr->KprApplication->mergeApplication( $value['Kpr'], $id);
			##########

			$value = $this->User->getMerge($value, $client_id, false, 'Client');
			$value = $this->User->UserClient->getMerge($value, $client_id, $company_id);
			$value = $this->User->UserCompany->getMerge($value, $company_id);
			$value = $this->User->UserCompanyConfig->getMerge($value, $company_id);
			$value = $this->User->getMerge($value, $agent_id, false, 'Agent');
			$value = $this->User->Kpr->BankApplyCategory->getMerge($value, $bank_apply_category_id);
			$value = $this->User->Kpr->CrmProject->getMerge($value, $crm_project_id);

			$value = $this->User->UserClient->getMergeList($value, array(
				'contain' => array(
					'JobType' => array(
						'position' => 'inside',
					),
				),
			));

			$value = $this->User->Kpr->_callMergeProperty($value);			
			$value = $this->User->Kpr->KprBank->setKprFrontend($value);			
			$cnt_data = $this->User->Kpr->KprBank->getCountSummary( $id );

			$value = $this->RmKpr->getListDocument($value);
			$paymentAppraisals = $this->User->CrmProject->CrmProjectDocument->getData('all', array(
	        	'conditions' => array(
					'CrmProjectDocument.owner_id' => $kpr_bank_id,
					'CrmProjectDocument.document_type' => 'payment_appraisal',
					'CrmProjectDocument.session_id NOT' => NULL,
				),
			));

			if(!empty($cnt_data)){
				$value['Kpr']['cnt_data'] = $cnt_data;
			}

			$this->RmCommon->_callDataForAPI($value, 'manual');
			Configure::write('Cookie.Helper', $this->Cookie);
			$urlDownloadExcel = array(
	            'controller' => 'kpr',
	            'action' => 'application_detail_excel',
	            $kpr_bank_id,
	            'admin' => true,
	        );

			$this->RmCommon->_layout_file('fileupload');
			$this->set(array(
				'module_title' => __('Aplikasi KPR'),
				'value' => $value,
				'id' => $id,
				'paymentAppraisals' => $paymentAppraisals,
				'layout_css' => array(
					'admin/report',
					'file_upload/jquery.fileupload-ui.css',
				),
				'urlDownloadExcel' => array_merge($urlDownloadExcel, array(
		            'export' => true,
				)),
				'urlPrint' => array_merge($urlDownloadExcel, array(
		            'print' => true,
				)),
			));
		} else {
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'));
		}
	}

	public function api_application_detail( $kpr_bank_id = false ) {
		$value = $this->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $kpr_bank_id,
				'KprBank.document_status <>' => 'cart_kpr',
			),
		), array(
			'mine' => true,
		));

		if( !empty($value) ) {
			$value = $this->Kpr->mergeList($value, array(
				'contain' => array(
					'User' => array(
						'foreignKey' => 'agent_id',
						'primaryKey' => 'id',
						'elements' => array(
							'status' => 'all',
						),
					),
					'CrmProject',
					'BankApplyCategory',
					'KprApplication' => array(
						'type' => 'first',
						'conditions' => array(
							'KprApplication.parent_id' => NULL,
						),
						'contain' => array(
							'KprApplicationSpouse' => array(
								'uses' => 'KprApplication',
								'type' => 'first',
								'foreignKey' => 'id',
								'primaryKey' => 'parent_id',
							),
						),
					),
				),
			));
			$value = $this->Kpr->KprBank->getMergeList($value, array(
				'contain' => array(
					'Bank',
					'KprBankInstallment' => array(
						'contain' => array(
							'KprBankCommission' => array(
								'type' => 'first',
								'conditions' => array(
									'KprBankCommission.type' => 'agent',
								),
								'order' => array(
									'KprBankCommission.status_confirm' => 'ASC',
									'KprBankCommission.id' => 'ASC',
								),
								'elements' => array(
									'status' => 'all',
								),
							),
						),
						'order' => array(
							'KprBankInstallment.status_confirm' => 'ASC',
							'KprBankInstallment.id' => 'ASC',
						),
						'elements' => array(
							'status' => 'all',
						),
					),
				),
			));
			$value = $this->User->Kpr->KprBank->setKprFrontend($value);
			$value = $this->User->Kpr->_callMergeProperty($value);
			$value = $this->RmKpr->getListDocument($value, array(
				'data_set' => false,
				'document_merge' => true,
			));
			$value = $this->RmKpr->_callDescription($value);
			
			$kpr_bank_progress = Configure::read('__Site.Global.Variable.KPR.list_status_progress');
			$kpr_bank_dates = $this->Kpr->KprBank->KprBankDate->getFromSlug(array(), $kpr_bank_id);
			$kpr_bank_timeline = array();

			foreach ($kpr_bank_progress as $status => $val) {
				$kpr_bank_date = Common::hashEmptyField($kpr_bank_dates, $status);
				$document_status_text = Common::hashEmptyField($val, 'text');

				$tmp = array(
					'document_status_text' => $document_status_text,
					'document_status_date' => false,
				);

				if( !empty($kpr_bank_date) ) {
					$tmp['document_status_date'] = Common::hashEmptyField($kpr_bank_date, 'KprBankDate.action_date'); 
				}
				
				$kpr_bank_timeline[] = $tmp;
			}

			$kpr_document_status = Common::hashEmptyField($value, 'Kpr.document_status');
			$kpr_bank_document_status = Common::hashEmptyField($value, 'KprBank.document_status');
			$is_hold = Common::hashEmptyField($value, 'KprBank.is_hold');
			$kprBankInstallment = Common::hashEmptyField($value, 'KprBankInstallment');

			if( !empty($kprBankInstallment) ) {
				foreach ($kprBankInstallment as $key => &$bankInstallment) {
					$bankInstallment = KprCommon::_callTotalPaymentKPR($bankInstallment, 'KprBankInstallment');
				}

				$value['KprBankInstallment'] = $kprBankInstallment;
			}

			$value['StatusProgress'] = $kpr_bank_timeline;
			$value['KprBank']['document_status_text'] = $this->RmKpr->_callStatus($kpr_bank_document_status, $is_hold);
			$value['KprBank']['document_status_color'] = $this->RmKpr->_callColor($kpr_bank_document_status, $is_hold);
			$value['KprBank']['document_status_note'] = $this->RmKpr->dataNotice($value);
			$value['KprBank']['is_document_edit'] = KprCommon::_callPermissionEditDocument($kpr_document_status, $kpr_bank_document_status);
            $value['Property']['expired_date'] = $this->RmProperty->_callExpiredProperty($value);

			App::import('Helper', 'Property');
			$PropertyHelper = new PropertyHelper(new View(null));
            $value['Property']['specifications'] = $PropertyHelper->getSpec($value, array(), false, false);
            $value = Common::_callUnset($value, array(
				'PropertyAsset'
			));

	        $value['KprBank']['Action'] = array(
				'download_excel' => $this->RmCommon->url(array(
		            'controller' => 'kpr',
		            'action' => 'application_detail_excel',
		            $kpr_bank_id,
		            'admin' => true,
		            'export' => true,
		        ), true),
			);

			$this->RmCommon->_callDataForAPI($value, 'manual');
		} else {
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'));
		}
	}

	public function admin_update_kpr_non_komisi($id = false){
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id,
				'KprBank.document_status' => 'approved_bank',
			),
		));

		$value = $this->User->Kpr->KprBank->getMergeList($value, array(
			'contain' => array(
				'Kpr' => array(
					'elements' => array(
						'admin_mine' => true,
					),
				),
				'Bank',
			),
		));

		if(!empty($value['KprBank']) && !empty($value['Kpr'])){
			$result = $this->User->Kpr->KprBank->doUpdateComplete($value, false);
			$this->RmCommon->setProcessParams($result);
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_update_kpr($id = false){
		$flag = false;
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id,
				'KprBank.document_status' => 'approved_bank',
				'KprBank.is_hold' => false,
			),
		));
		$value = $this->User->Kpr->KprBank->getMergeList($value, array(
			'contain' => array(
				'Kpr' => array(
					'elements' => array(
						'admin_mine' => true,
					),
				),
				'Bank',
			),
		));

		if(!empty($value['KprBank']) && !empty($value['Kpr'])){
			$kpr_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'kpr_id');
			$value = $this->User->Kpr->getMerge($value, $kpr_id);
			$user_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'agent_id');
			$value = $this->User->getMerge($value, $user_id, true);
			$value = $this->User->UserProfile->getMerge($value, $user_id);
			$data = $this->request->data;

			if(!empty($data)){

				$kpr_bank_transfer = $this->RmCommon->filterEmptyField($data, 'KprBankTransfer');
				$check_term = $this->RmCommon->filterIssetField( $data, 'KprBankTransfer', 'check_term');
				$user_id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$profile_id = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'id');

				$flag_bank_transfer = $this->User->Kpr->KprBank->KprBankTransfer->doSave($data, array(
					'kpr_bank_id' => $id,
					'agent_id' => $user_id,
				));

				if($flag_bank_transfer){
					$flag = $this->User->UserProfile->doSave(array(
						'UserProfile' => $kpr_bank_transfer,
					), $user_id, $profile_id, true);
					$result['status'] = !empty($flag)? 'success': 'error';
					$result = $this->User->Kpr->KprBank->doUpdateComplete($value);
				}else{
					$result = array(
						'msg' => __('Gagal melakukan Proses Akad Kredit'),
						'status' => 'error',
					);
				}

				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
				));
				
			}else{
				$full_name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name');
				$full_name_akun = $this->RmCommon->filterEmptyField($value, 'UserProfile','name_account');
				$full_name = !empty($full_name_akun)?$full_name_akun:$full_name;
				$rekening_bank = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'bank_name');
				$no_rekening = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'no_account');
				$no_npwp = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'no_npwp');	

				$set['KprBankTransfer'] = array(
					'name_account' => $full_name,
					'bank_name' => $rekening_bank,
					'no_account' => $no_rekening,
					'no_npwp' => $no_npwp,
				);
				$this->request->data = $set;
			}

			$this->set(compact('value'));
		} else {
			$this->RmCommon->redirectReferer(__('Bank tidak ditemukan'));
		}
	}

	function admin_change_status($id = false, $status = null){
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id,
				'KprBank.document_status' => array( $status ),
				'KprBank.is_hold' => false,
			),
		));

		if( !empty($value) ){
			$result = $this->User->Kpr->KprBank->doChangeStatus($value, $status);
			$this->RmCommon->setProcessParams($result);
		} else {
			$this->RmCommon->redirectReferer(__('Bank tidak ditemukan'));
		}
	}

	public function admin_index(){
		$this->loadModel('Kpr');
		$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');
		$export = $this->RmCommon->filterEmptyField($params, 'named', 'export');

		$this->RmCommon->_callRefineParams($params);
		$paramsModel = $this->RmCommon->dataConverter($params, array(
			'date' => array(
				'named' => array(
					'date_from',
					'date_to',
				),
			),
		));

		$options = $this->User->Kpr->_callRefineParams($paramsModel, array(
			'order' => array(
				'Kpr.modified' => 'DESC',
				'Kpr.id' => 'DESC',
			),
		), $this->Auth->user('group_id'));

		$optionsStatus = array(
			'admin_mine' => true,
			'status' => 'application',
		);

		if( $export == 'excel' ) {
	        $values = $this->User->Kpr->getData('all', $options, $optionsStatus);
		} else {
			$options['conditions']['Kpr.property_id <>'] = 0;
			$options['limit'] = 5;
			$this->paginate = $this->User->Kpr->getData('paginate', $options, $optionsStatus);
			$values = $this->paginate('Kpr');
		}

		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$id = Common::hashEmptyField($value, 'Kpr.id');
				$property_id = Common::hashEmptyField($value, 'Kpr.property_id');
				$agent_id = Common::hashEmptyField($value, 'Kpr.agent_id');
				$crm_project_id = Common::hashEmptyField($value, 'Kpr.crm_project_id');
				$document_type = Common::hashEmptyField($value, 'Kpr.document_type');

				$kprBanks = $this->Kpr->KprBank->getData('all', array(
					'conditions' => array(
						'KprBank.kpr_id' => $id,
						'KprBank.document_status <>' => 'cart_kpr',
					),
				));

				$value = $this->RmKpr->getListDocument($value, false);
				$value = $this->RmKpr->checkExistingKPR($value);

				$kprBanks = $this->Kpr->KprBank->callMergeList($kprBanks, array(
					'contain' => array(
						'Bank',
						'BankSetting' => array(
							'elements' => array(
								'type' => 'all',
							),
							'contain' => array(
								'BankProduct',
							),
						),
						'KprBankInstallment' => array(
							'type' => 'first',
							'order' => array(
								'KprBankInstallment.status_confirm' => 'DESC',
							),
							'elements' => array(
								'status' => 'all',
							),
						),
					),
				));

				if(!empty($kprBanks)){
					$value['KprBank'] = $kprBanks;
					$value['Kpr']['count'] = count($kprBanks);
				}

				$value = $this->User->Kpr->_callMergeProperty($value);

				if( $document_type != 'developer' ) {
					$value = $this->User->getMerge($value, $agent_id, false, 'Agent');
			    	$value = $this->User->Property->getDataList($value, array(
						'contain' => array(
				            'PropertyNotification',
				            'User',
				            'Approved',
				            'Client',
						),
					));
				}

				$value = $this->User->Kpr->CrmProject->getMerge($value, $crm_project_id);
				$values[$key] = $value;
			}
		}
		
		$date_from = $this->RmCommon->filterEmptyField($paramsModel, 'named', 'date_from');
		$date_to = $this->RmCommon->filterEmptyField($paramsModel, 'named', 'date_to');
		$periods = $this->RmCommon->getCombineDate($date_from, $date_to);

		$this->RmCommon->_callDataForAPI($values, 'manual');

    	$this->set('module_title', __('Aplikasi KPR'));
		$this->set(compact(
			'values', 'export', 'periods'
		));

        if($export == 'excel'){
            $this->layout = 'ajax';
        }

        $this->RmCommon->renderRest(array(
            'is_paging' => true
        ));
	}

	public function api_index(){
		$params['named'] = $this->RmCommon->filterEmptyField($this->params, 'named');

		$paramsModel = $this->RmCommon->dataConverter($params, array(
			'date' => array(
				'named' => array(
					'date_from',
					'date_to',
				),
			),
		));

		$this->RmCommon->_callRefineParams($params);
		$options = $this->Kpr->_callRefineParams($paramsModel, array(
			'conditions' => array(
				'Kpr.property_id <>' => 0,
			),
			'order' => array(
				'Kpr.modified' => 'DESC',
				'Kpr.id' => 'DESC',
			),
			'limit' => Configure::read('__Site.config_pagination'),
		), Configure::read('User.group_id'));

		$optionsStatus = array(
			'admin_mine' => true,
			'status' => 'application',
		);

		$this->paginate = $this->Kpr->getData('paginate', $options, $optionsStatus);
		$values = $this->paginate('Kpr');

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$value = Common::_callUnset($value, array(
					'KprBank',
				));

				$id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'id');
				$property_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_id');
				$agent_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'agent_id');
				$crm_project_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'crm_project_id');
				$document_status = $this->RmCommon->filterEmptyField($value, 'Kpr', 'document_status');

				$value['Kpr']['count'] = $this->Kpr->KprBank->getData('count', array(
					'conditions' => array(
						'KprBank.kpr_id' => $id,
						'KprBank.document_status <>' => 'cart_kpr',
					),
				));
				
				$status_color = Configure::read('__Site.Global.Variable.KPR.status_color');
				$value['Kpr']['document_status_text'] = $this->RmKpr->_callStatus($document_status);
				$value['Kpr']['document_status_color'] = Common::hashEmptyField($status_color, $document_status);

				$value = $this->RmKpr->getListDocument($value, array(
					'data_set' => false,
				));
				$value = $this->RmKpr->checkExistingKPR($value);

				$value = $this->User->getMerge($value, $agent_id, false, 'Agent');
				$value = $this->User->Kpr->_callMergeProperty($value);
				$value = $this->User->Kpr->CrmProject->getMerge($value, $crm_project_id);
			}
		}

		$this->RmCommon->_callDataForAPI($values, 'manual');

		$this->set(compact(
			'values', 'export', 'periods'
		));
        $this->RmCommon->renderRest(array(
            'is_paging' => true
        ));
	}

	public function api_appraisal(){
		$params = $this->params->params;
		$paramsModel = $this->RmCommon->dataConverter($params, array(
			'date' => array(
				'named' => array(
					'date_from',
					'date_to',
				),
			),
		));

		$this->RmCommon->_callRefineParams($params);
		$paramsModel['named']['status'] = 'approved_bank';
		$options = $this->Kpr->_callRefineParams($paramsModel, array(
			'conditions' => array(
				'Kpr.property_id <>' => 0,
			),
			'order' => array(
				'Kpr.modified' => 'DESC',
				'Kpr.id' => 'DESC',
			),
			'limit' => Configure::read('__Site.config_pagination'),
		), Configure::read('User.group_id'));

		$optionsStatus = array(
			'admin_mine' => true,
			'status' => 'application',
		);

		$this->paginate = $this->Kpr->getData('paginate', $options, $optionsStatus);
		$values = $this->paginate('Kpr');

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$kpr_bank_id = Common::hashEmptyField($value, 'KprBank.id');
				$document_status = Common::hashEmptyField($value, 'KprBank.document_status');
				
				$status_color = Configure::read('__Site.Global.Variable.KPR.status_color');
				$value['Kpr']['document_status_text'] = $this->RmKpr->_callStatus($document_status);
				$value['Kpr']['document_status_color'] = Common::hashEmptyField($status_color, $document_status);

				$value = $this->Kpr->KprBank->getMergeList($value, array(
					'contain' => array(
						'Bank',
						'KprBankInstallment' => array(
							'type' => 'first',
							'elements' => array(
								'status' => 'confirm',
							),
						),
					),
				));
				$value = $this->User->Kpr->_callMergeProperty($value);
				$value['Action'] = array(
					array(
						'text' => __('Tolak'),
						'url' => $this->RmCommon->url(array_merge(Configure::read('__Site.Global.Variable.KPR.action.cancel'), array(
							$kpr_bank_id,
							'ext' => 'json',
						)), true),
					),
					array(
						'text' => __('Setujui'),
						'url' => $this->RmCommon->url(array_merge(Configure::read('__Site.Global.Variable.KPR.action.credit_process'), array(
							$kpr_bank_id,
							'ext' => 'json',
						)), true),
					),
				);
			}
		}

		$this->RmCommon->_callDataForAPI($values, 'manual');

		$this->set(compact(
			'values', 'export', 'periods'
		));
        $this->RmCommon->renderRest(array(
            'is_paging' => true
        ));
	}

	public function api_bank_applications( $kpr_id = null ){
		$params = $this->params->params;
		$document_status = Common::hashEmptyField($params, 'named.status');

		$this->RmCommon->_callRefineParams($params);
		$paramsModel = $this->RmCommon->dataConverter($params, array(
			'date' => array(
				'named' => array(
					'date_from',
					'date_to',
				),
			),
		));

		$options = $this->Kpr->KprBank->_callRefineParams($paramsModel, array(
			'conditions' => array(
				'KprBank.kpr_id' => $kpr_id,
				'KprBank.document_status <>' => 'cart_kpr',
			),
		));
		$values = $this->Kpr->KprBank->getData('all', $options, array(
			'mine' => true,
		));

		if( !empty($values) ) {
			foreach ($values as $key => &$value) {
				$kpr_bank_id = Common::hashEmptyField($value, 'KprBank.id');
				$current_document_status = Common::hashEmptyField($value, 'KprBank.document_status');
				$is_hold = Common::hashEmptyField($value, 'KprBank.is_hold');

				$value = $this->Kpr->KprBank->getMergeList($value, array(
					'contain' => array(
						'Kpr',
						'Bank' => $this->Kpr->KprBank->Bank->_callFieldForAPI(),
						'KprBankInstallment' => array(
							'type' => 'first',
							'order' => array(
								'KprBankInstallment.status_confirm' => 'DESC',
							),
							'elements' => array(
								'status' => 'all',
							),
						),
					),
				));

				$kpr_bank_date = $this->User->Kpr->KprBank->KprBankDate->getFromSlug(array(), $kpr_bank_id, array(
					'find' => 'first',
				));

				$value['KprBank']['document_status_text'] = $this->RmKpr->_callStatus($current_document_status, $is_hold);
				$value['KprBank']['document_status_color'] = $this->RmKpr->_callColor($current_document_status, $is_hold);
				$value['KprBank']['document_status_note'] = $this->RmKpr->dataNotice($value);

				if( $current_document_status == $document_status && empty($is_hold) ) {
					$value['KprBank']['Action'] = array(
						'selected' => $this->RmCommon->url(array(
                            'controller' => 'kpr',
                            'action' => 'change_status',
                            $kpr_bank_id,
                            $current_document_status,
                            'admin' => true,
                        ), true),
					);
				}
			}
		}

		$this->RmCommon->_callDataForAPI($values, 'manual');
	}

	public function ajax_compare_detail(){
		$this->autoLayout = false;
		$this->autoRender = false;
		$data = $this->request->data;
		$this->loadModel('KprCompare');
		$uuid = String::uuid();

		$bank_setting_id = $this->RmCommon->filterEmptyField($data, 'Kpr', 'id');
		if(!empty($bank_setting_id)){
			$data = $this->RmKpr->beforeSaveKprCompare($data, $uuid);
			$result = $this->KprCompare->doSaveAll($data);
			$compare_id = $this->RmCommon->filterEmptyField($result, 'document_id');

			$this->RmCommon->filterEmptyField($result, false, array(
				'noRedirect' => TRUE,
			));

			$result  = array(
				'id' => $compare_id,
				'uuid' => $uuid,
			);

			return json_encode($result);
		}
	}

	public function product_list(){
		$data = $this->request->data;
		$named = $this->RmCommon->filterEmptyField($data, 'Kpr');

		$bank_id = $this->RmCommon->filterEmptyField($named, 'bank_id');
		$property_id = $this->RmCommon->filterEmptyField($named, 'property_id');
		$view_name_product = $this->RmCommon->filterEmptyField($named, 'view_name_product');

		$property = $this->User->Property->getData('first', array(
			'conditions' => array(
				'Property.id' => $property_id,
			),
		));

		$detailBank = !empty($bank_id) ? true : false;

		if(!empty($property)){
			$data_kpr = Common::hashEmptyField($named, 'id', array());
			$data_kpr = array_filter($data_kpr);

			if( !empty($data_kpr) ) {
				$kpr = array();
			} else {
				$name_cookie = sprintf('filling_%s', $property_id);
				$session_id = $this->Cookie->read($name_cookie);
				$session_id = !empty($session_id) ? $session_id : String::uuid();

				$kpr = $this->User->Kpr->getData('first', array(
					'conditions' => array(
						'Kpr.session_id' => $session_id,
					),
				));

				if( !empty($kpr) ) {				
					$kpr_id = $this->RmCommon->filterEmptyField($kpr, 'Kpr', 'id');
					$kpr = $this->User->Kpr->KprBank->getMerge($kpr, $kpr_id, 'Kpr', 'all');
				}
			}

			$value = $this->RmKpr->getSelectProduct($named, true, $kpr, $property);

			$this->set(array(
				'value' => $value,
				'detailBank' => $detailBank,
				'view_name_product' => $view_name_product,
			));
		}else{
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'));
		}
	}

	public function admin_bank_list( $kpr_id = false ) {
		$data = $this->request->data;
		$value = $this->User->Kpr->getMerge(array(), $kpr_id, 'Kpr.id', array(
			'elements' => array(
				'admin_mine' => true,
			),
		));
		$value = $this->User->Kpr->getMergeList($value, array(
			'contain' => array(
				'KprBank' => array(
					'Kpr', 'id', 'Kpr', 'all'
				),
			),
		));
		$value = $this->User->Kpr->_callMergeProperty($value);

		$value['Kpr'] = array_merge($value['Kpr'], $data['Kpr']);
		$checked = $this->RmCommon->filterEmptyField($data, 'Bank', 'id');
		$banks = $this->Kpr->callGetBank($value, $checked);
		$this->set(compact(
			'banks', 'value'
		));
	}

	function admin_resend_application($kpr_bank_id){
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $kpr_bank_id,
			),
		));

		if(!empty($value)){
			$document_status = $this->RmCommon->filterEmptyField($value, 'KprBank', 'document_status');
			$application_status = $this->RmCommon->filterEmptyField($value, 'KprBank', 'application_status');

			if($document_status == 'proposal_without_comiission' && $application_status <> 'resend'){
				$result = $this->User->Kpr->KprBank->resend_application($value);
				$this->RmCommon->setProcessParams($result);
			}else{
				$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
			}
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
		}
	}

	function admin_notice_toggle ( $id = false, $type = false ) {
		$this->RmCommon->_callUserLogin();
		$this->layout = false;
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id,
			),
		));

		$value = $this->User->Kpr->KprBank->getMergeList($value, array(
			'contain' => array(
				'Kpr' => array(
					'elements' => array(
						'admin_mine' => true,
					),
				),
			),
		));

		if( !empty($value['Kpr']) ) {
			$msg = $this->RmKpr->_callCookieNotice($id, $type);

			$this->set(compact(
				'msg'
			));
		} else {
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'));
		}
	}

	// reschedule pk from agent (optional)
	function admin_reschedule_pk($id = false) {
		$info  = ' melakukan reschedule';
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id
			),
		));

		$value = $this->User->Kpr->KprBank->getMergeList($value, array(
			'contain' => array(
				'Kpr' => array(
					'elements' => array(
						'admin_mine' => true,
					),
				),
			),
		));

		$document_status = Common::hashEmptyField($value, 'KprBank.document_status');
		$type_action     = Common::hashEmptyField($this->params->params, 'named.type');

		if( !empty($value) && $document_status == 'approved_credit' || $type_action == 'request_pk') {
			$slug = $doc_status = 'reschedule_pk';
			$is_reschedule = true;

			// agen do request PK date
			if (!empty($type_action)) {
				$info          = 'mengatur jadwal akad kredit';
				$is_reschedule = false;
				$doc_status    = 'credit_process';
				$slug          = $type_action;
			}

			$data = $this->request->data;

			if($data){
				
				$data = $this->RmCommon->dataConverter($data,array(
					'date' => array(
						'KprBankDate' => array(
							'process_date',
						)
	 				)
				));

				$process_date = Common::hashEmptyField($data, 'KprBankDate.process_date');
				$process_time = Common::hashEmptyField($data, 'KprBankDate.process_time');
				$concat_date  = sprintf('%s %s:00', $process_date, $process_time);
				$note         = Common::hashEmptyField($data, 'KprBankDate.note');

				$dataSave = array(
					'KprBank' => array(
						'id' => $id,
						'document_status' => $doc_status,
						'modified' => date('Y-m-d H:i:s'),
					),
					'KprBankDate' => array(
						array(
							'KprBankDate' => array(
								'slug'        => $slug,
								'action_date' => $concat_date,
								'note'        => $note,
							),
						),
					),
				);

				$validate = $this->User->Kpr->KprBank->saveAll($dataSave, array(
					'validate' => 'only',
				));

				$message = __('mengatur jadwal.');

				if($validate){
					$this->User->Kpr->KprBank->saveAll($dataSave);

					$kpr_id = Common::hashEmptyField($value, 'KprBank.kpr_id');
					$this->Kpr->_summaryDocumentStatus($kpr_id, false, array(
						'flag_reschedule_pk' => $is_reschedule
					));
					
					$result = array(
						'msg' => __('Sukses %s', $message),
						'status' => 'success',
						'id' => $this->id,
						'Log' => array(
							'activity' => __('Sukses %s', $message),
							'document_id' => $this->id,
						),
					);
				}else{
					$result = array(
						'msg' => __('Gagal %s', $message),
						'status' => 'error',
						'Log' => array(
							'activity' => __('Gagal %s', $message),
							'document_id' => $this->id,
						),
					);
				}

				$urlRedirect = array(
		            'controller' => 'kpr',
		            'action' => 'application_detail',
		            $id,
		            'admin' => true,
		        );

				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
				));

			}

			$message_info = __('Anda dapat %s dengan mengisikan form berikut :', $info);

			$this->set(array(
				'value'        => $value,
				'title'        => 'Atur Jadwal Akad',
				'message_info' => $message_info,
			));
		}else{
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'));
		}

	}

	function admin_completed($id = false){
		$text_default = __('confirm jadwal Akad');

		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id
			),
		));

		$value = $this->User->Kpr->KprBank->getMergeList($value, array(
			'contain' => array(
				'Kpr' => array(
					'elements' => array(
						'admin_mine' => true,
					),
				),
				'KprBankInstallment' => array(
					'type' => 'first',
					'elements' => array(
						'status' => 'confirm',
					),
				),
			),
		));

		$document_status = Common::hashEmptyField($value, 'KprBank.document_status');

		if( !empty($value) && $document_status == 'approved_credit'){
			$kprBankInstallmentID = Common::hashEmptyField($value, 'KprBankInstallment.id');

			$data = $this->request->data;

			if($data){
				$dataSave = array(
					'KprBank' => array(
						'id' => $id,
						'document_status' => 'approved_credit',
						'modified' => date('Y-m-d H:i:s'),
					),
					'KprBankDate' => array(
						array(
							'KprBankDate' => array(
								'slug' => 'confirm_pk',
								'action_date' => date('Y-m-d H:i:s'),
								'note' => 'Menyetujui PK',
							),
						),
					),
				);

				if(!empty($data['KprBank']['send_email'])){
					$dataSave['KprBankInstallment'] = array(
						array(
							'KprBankInstallment' => array(
								'id' => $kprBankInstallmentID,
								'kpr_bank_id' => $id,
								'is_claim' => true,
							),
						),
					);
				}

				$validate = $this->User->Kpr->KprBank->saveAll($dataSave, array(
					'validate' => 'only',
				));

				if($validate){
					$this->User->Kpr->KprBank->saveAll($dataSave);

					$kpr_id = Common::hashEmptyField($value, 'KprBank.kpr_id');
					$this->Kpr->_summaryDocumentStatus($kpr_id);
					
					$result = array(
						'msg' => __('Sukses, %s', $text_default),
						'status' => 'success',
						'id' => $this->id,
						'Log' => array(
							'activity' => __('Sukses, ', $text_default),
							'document_id' => $this->id,
						),
					);
				}else{
					$result = array(
						'msg' => __('Gagal, %s', $text_default),
						'status' => 'error',
						'Log' => array(
							'activity' => __('Gagal, %s', $text_default),
							'document_id' => $this->id,
						),
					);
				}
				$this->RmCommon->setProcessParams($result);
			}

			$this->set(array(
				'value' => $value,
			));
		}else{
			$this->RmCommon->redirectReferer(__('Aplikasi tidak ditemukan'));
		}

	}

	function clear_snyc($kpr_bank_id){
		$group_id = Configure::read('User.group_id');
		$slug = $this->RmCommon->filterEmptyField($this->params, 'named', 'slug');

		if(in_array($group_id, array(19, 20))){
			$value = $this->User->Kpr->KprBank->getData('first', array(
				'conditions' => array(
					'KprBank.id' => $kpr_bank_id,
				),
			));

			$document_status = $this->RmCommon->filterEmptyField($value, 'KprBank', 'document_status');
			$code = $this->RmCommon->filterEmptyField($value, 'KprBank', 'code');

			if($slug == $document_status){
				$this->Kpr->KprBank->id = $kpr_bank_id;
				$this->Kpr->KprBank->set('snyc', TRUE);

				if($this->Kpr->KprBank->save()){
					echo __('Berhasil clear kode #', $code);
				}else{
					echo __('Gagal clear kode #', $code);
				}

			}else{
				echo __('document status tidak sesuai.');
			}
		}else{
			echo __('Clear tidak bisa dilakukan hanya superadmin primesystem');
		}
		die();
	}

	function api_paid_commission(){
		$this->loadModel('ApiUser');
		$datas = $this->request->data;
		$apikey = $this->RmCommon->filterEmptyField($datas, 'Kpr', 'apikey');
		$apipass = $this->RmCommon->filterEmptyField($datas, 'Kpr', 'apipass');
		$access = $this->ApiUser->get_access($apikey, $apipass);

		if(!empty($access)){
			$datas = $this->RmCommon->filterEmptyField($datas, 'Kpr', 'merge_vars');
			$params = array();

			if(!empty($datas[0])){
				foreach($datas As $key => $data){
					$status_confirm = $this->RmCommon->filterEmptyField($data, 'KprBankCommission', 'status_confirm');
					$paid_status = $this->RmCommon->filterEmptyField($data, 'KprBankCommission', 'paid_status');
					$sent = $this->RmCommon->filterEmptyField($data, 'KprBankCommission', 'sent');
					if(!empty($status_confirm) && $paid_status == 'approved' && empty($sent)){
						$result = $this->User->Kpr->KprBank->KprBankInstallment->KprBankCommission->api_paid_commission($data);
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
							$value = $this->User->Kpr->KprBank->KprBankTransfer->getMerge($value, $prime_kpr_bank_id, array(
								'fieldName' => 'kpr_bank_id'
							));
							$value = $this->User->getMerge($value, $agent_id, TRUE, 'Agent');
							$this->RmKpr->sendEmailCommission($data, $value);
						}

						$params[] = array(
							'status' => $status,
							'data' => $data,
						);
						$this->RmCommon->setProcessParams($result, false, array(
							'noRedirect' => true
						));
					}
				}
			}
		}else{
			$params = array(
				'status' => 'error',
				'msg' => __('Forbidden authorization API Key and API Secreet'),
			);
		}

		
		$this->set('result', $params);
	}

	function admin_all_forward($id = false){
		$values = $this->User->Kpr->KprBank->getData('all', array(
			'conditions' => array(
				'KprBank.kpr_id' => $id,
				'KprBank.forward_app' => false,
				'KprBank.from_kpr' => 'frontend',
			),
		));

		if(!empty($values)){
			$values = $this->User->Kpr->KprBank->getMergeList($values, array(
				'contain' => array(
					'Bank',
				),
			));
			$result = $this->User->Kpr->KprBank->allContinueApps($values);
			$this->RmCommon->setProcessParams($result);
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
		}
	}

	function admin_foward_application($id = false){
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id,
			)
		));

		if(!empty($value)){
			$from_kpr = $this->RmCommon->filterEmptyField($value, 'KprBank', 'from_kpr');
			$forward_app = $this->RmCommon->filterEmptyField($value, 'KprBank', 'forward_app');
			$bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'bank_id');
			$data = $this->request->data;

			if(!empty($data)){
				if($from_kpr == 'frontend' && empty($forward_app)){
					$result = $this->User->Kpr->KprBank->continueApps($value, $data);
					$this->RmCommon->setProcessParams($result, false, array(
						'ajaxFlash' => true,
					));
				}else{
					$this->RmCommon->redirectReferer(__('Project tidak ditemukan'), 'error');
				}
			}

			$this->User->Kpr->KprBank->Bank->BankUser->virtualFields['concat_name'] = 'CONCAT(BankUser.email, \' | \', BankUser.full_name)';
			$sales = $this->User->Kpr->KprBank->Bank->BankUser->getData('list', array(
				'fields' => array(
					'BankUser.id',
					'BankUser.concat_name',
				),
			), array(
				'bank' => $bank_id,
			));

			$this->set(array(
				'sales' => $sales,
			));
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
		}
		
	}

	public function admin_info( $recordID = NULL ) {
		$title = __('KPR');
		$user = $this->RmUser->getUser($recordID);

		if( !empty($user) ) {
			$values = array();
        	$this->RmCommon->_callRefineParams($this->params);
			$this->RmUser->_callRoleActiveMenu($user);

			$options = $this->RmKpr->_callRoleCondition($user);

			if(!empty($options)){
				$options = $this->Kpr->_callRefineParams($this->params, array_merge_recursive($options, array(
					'contain' => array(
						'Kpr',
					),
					'order' => array(
						'Kpr.id' => 'DESC',
					),
	            	'limit' => Configure::read('__Site.config_admin_pagination'),
				)));
				$options = $this->Kpr->KprBank->_callRefineParams($this->params, $options);
				$this->paginate	= $this->Kpr->getData('paginate', $options, array(
					'status' => 'application',
				));
				$values = $this->paginate('KprBank');
			}

			if( !empty($values) ) {
				$this->Kpr->bindModel(array(
					'hasOne' => array(
						'UserCompany' => array(
							'className' => 'UserCompany',
							'foreignKey' => false,
							'conditions' => array(
								'UserCompany.user_id = Kpr.company_id',
							),
						),
					)
				), false);

				foreach ($values as $key => &$value) {
					$value = $this->RmCommon->dataConverter($value,array(
						'date' => array(
							'Kpr' => array(
								'created',
							),
						),
					), true);

					$value = $this->Kpr->KprBank->callMergeList($value, array(
						'contain' => array(
							'Bank',
							'KprBankInstallment' => array(
								'order'=> array(
									'KprBankInstallment.status_confirm' => 'DESC',
									'KprBankInstallment.modified' => 'DESC',
								),
							),
						),
					));
					$value = $this->Kpr->callMergeList($value, array(
						'contain' => array(
							'BankApplyCategory',
							'Property' => array(
								'PropertyAddress' => array(
									'contain' => array(
										'Region' => array(
											'cache' => true,
										),
										'City' => array(
											'cache' => true,
										),
									),
								),
							),
							'AgentProperty' => array(
								'uses' => 'User',
								'foreignKey' => 'agent_id',
								'primaryKey' => 'id',
								'elements' => array(
									'status' => 'all',
								),
							),
							'UserCompany' => array(
								'uses' => 'UserCompany',
								'foreignKey' => 'company_id',
								'primaryKey' => 'user_id',
							),
							'KprApplication' => array(
								'order'=> array(
									'KprApplication.parent_id' => 'ASC',
									'KprApplication.created' => 'ASC',
								),
							),
						),
					));
					$value['Document'] = $this->RmKpr->getDocuments($value);
				}
			}

			$documentCategories = $this->User->CrmProject->CrmProjectDocument->DocumentCategory->getData( 'all', array(
				'conditions' => array(
					'DocumentCategory.is_required' => 1,
					'DocumentCategory.id <>' => array(3, 7, 19, 20),
				),
				'order' => array(
					'DocumentCategory.order' => 'ASC',
					'DocumentCategory.id' => 'ASC',
				),
			));

			$this->RmCommon->_layout_file('freeze');
			$this->set(array(
				'documentCategories' => $documentCategories,
				'module_title' => $title,
				'title_for_layout' => $title,
				'values' => $values,
				'currUser' => $user,
				'recordID' => $recordID,
				'active_tab' => 'KPR',
			));
		} else {
			$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
		}
	}

	function direct_link(){
		$this->loadModel('BankBanner');
		$url = false;
		$date_now = date('Y-m-d');
		$id = $this->RmCommon->filterEmptyField($this->params, 'named', 'id');

		$value = $this->BankBanner->getData('first', array(
			'conditions' => array(
				'BankBanner.id' => $id,
				'OR' => array(
					array(
						'BankBanner.start_date <=' => $date_now,
						'BankBanner.end_date >=' => $date_now,
					),
					array(
						'BankBanner.start_date <=' => $date_now,
						'BankBanner.end_date' => NULL,
					),
					array(
						'BankBanner.start_date' => NULL,
						'BankBanner.end_date >=' => $date_now,
					),
					array(
						'BankBanner.start_date' => NULL,
						'BankBanner.end_date' => NULL,
					),
				),
			),
		));

		$value = $this->BankBanner->getMergeList($value, array(
			'contain' => array(
				'Bank',
			),
		));

		if(!empty($value)){
			$bank_id = $this->RmCommon->filterEmptyField($value, 'Bank', 'id');

			$dataView = $this->RmCommon->_callSaveVisitor($bank_id, 'BankSlideView', 'document_id');
			$dataView = $this->RmKpr->doBeforeSaveView($dataView, $value);
			$this->BankBanner->Bank->BankSlideView->doSave($dataView);

			$url = $this->RmCommon->filterEmptyField($value, 'BankBanner', 'url');
		}
		
		if( empty($url) ) {
			$this->RmCommon->setCustomFlash(__('URL tidak valid'), 'error');
			$this->redirect($this->referer());
		} else {
			$this->set('url', $url);
			$this->layout = 'redirect';
		}
	}

	function backprocess_get_property( $mls_id = false ){
		$params = $this->params->params;
		$this->layout = false;
		$this->theme = false;
		$data = $this->request->data;

		$crm_project_id = Common::hashEmptyField($params, 'named.crm_project_id', 0);
		$kpr = Common::hashEmptyField($params, 'named.kpr');
		$mls_id = Common::hashEmptyField($data, 'Property.mls_id', $mls_id);

		$data = $this->RmCommon->dataConverter($data, array(
			'price' => array(
				'Kpr' => array(
					'property_price',
				),
			),
		));

		$value = $this->User->Property->_callPropertyMerge(array(), $mls_id, 'Property.mls_id');
		$value = $this->User->CrmProject->getMerge($value, $crm_project_id);
		$value = $this->User->CrmProject->CrmProjectPayment->getMerge($value, $crm_project_id, 'CrmProjectPayment.crm_project_id');
		
		$property_id = Common::hashEmptyField($value, 'Property.id', 0);
		$title = Common::hashEmptyField($value, 'Property.title');
		$owner_id = Common::hashEmptyField($value, 'Property.client_id');
		$value = $this->User->getMerge($value, $owner_id, false, 'Owner');

		$price = Common::hashEmptyField($value, 'Property.price_measure');
		$price = Common::hashEmptyField($value, 'CrmProjectPayment.price', $price);
		$price = Common::hashEmptyField($data, 'Kpr.property_price', $price);

		$value['Kpr']['property_price'] = $price;
		$value['Kpr']['sold_date'] = date('d/m/Y');
		$value['Kpr']['kpr_date'] = date('d/m/Y');

		$documentCategories = $this->RmKpr->_callDocumentCategories(array(
			'DocumentCategory.is_required' => 1,
			'DocumentCategory.id' => array( 1,5 ),
		), array(
			'crm_project_id' => $crm_project_id,
			'property_id' => $property_id,
		));

		$client = $this->RmCommon->filterEmptyField($value, 'Client');
		$value['ClientProfile'] = $client;

		$value = Common::_callUnset($value, array(
			'Client',
		));

		$mandatory = __('*');

		$value['Kpr']['property_title'] = __('%s, %s', $mls_id, $title);
		$this->request->data = $value;

		if( empty($data) ) {
			$data = $value;
		}

		$banks = $this->User->Kpr->callGetBank($data);
		
		if( $this->Rest->isActive() ) {
			if(!empty($banks)){
				foreach ($banks as $key => $val_bank) {
					$refData =& $banks[$key];

					$desc_promo = Common::hashEmptyField($val_bank, 'BankProduct.desc_promo', '', array(
						'type' => array('strip_tags')
					));

					$term_conditions = Common::hashEmptyField($val_bank, 'Bank.term_conditions', '', array(
						'type' => array('strip_tags')
					));

					$desc_promo = str_replace(array("\r", '&nbsp;'), '', $desc_promo);
					$term_conditions = str_replace(array("\r", '&nbsp;'), '', $term_conditions);

					$refData = hash::insert($refData, 'BankProduct.desc_promo', $desc_promo);
					$refData = hash::insert($refData, 'Bank.term_conditions', $term_conditions);
				}
			}

			$this->RmCommon->_callDataForAPI($value, 'manual');
		}

		$this->set(array(
			'mandatory' => $mandatory,
			'value' => $value,
			'documentCategories' => $documentCategories,
			'banks' => $banks,
			'show_banks' => true,
		));

		$this->render('/Elements/blocks/kpr/property_ver2');
	}

	function backprocess_get_bank( $bank_setting_id = false ){
		$this->layout = false;
		$this->theme = false;
		$data = $this->request->data;
		$data = $this->RmCommon->dataConverter($data, array(
			'price' => array(
				'Kpr' => array(
					'property_price',
				),
			),
		));

		$value = $this->User->Kpr->KprBank->Bank->BankSetting->getData('first', array(
			'conditions' => array(
				'BankSetting.id' => $bank_setting_id,
			),
		), array(
			'type' => 'product',
		));
		$value = $this->User->Kpr->KprBank->Bank->BankSetting->getMergeList($value, array(
			'contain' => array(
				'BankProduct',
				'Bank',
			),
		));

		$values[] = $value;
		$credit_total = Common::hashEmptyField($data, 'Bank.credit_total.'.$bank_setting_id);
		$property_price = Common::hashEmptyField($data, 'Kpr.property_price');
		$city_id = Common::hashEmptyField($data, 'PropertyAddress.city_id');
		$down_payment = Common::hashEmptyField($data, 'Bank.down_payment.'.$bank_setting_id);
		$down_payment = Common::_callPriceConverter($down_payment);

		$city = $this->User->UserProfile->City->getCity($city_id);
		$city_name = Common::hashEmptyField($city, 'City.name');

		$banks = $this->User->Kpr->KprBank->Bank->BankCommissionSetting->getKomisi($values, array(
			'property_type_id' => Common::hashEmptyField($data, 'Property.property_type_id'),
			'region_id' => Common::hashEmptyField($data, 'PropertyAddress.region_id'),
			'city_id' => $city_id,
			'price' => $property_price,
			'credit_total' => $credit_total,
			'dp' => Common::hashEmptyField($data, 'Bank.dp.'.$bank_setting_id),
			'down_payment' => $down_payment,
		));
		$rest_api = Configure::read('Rest.token');
		
		if( !empty($rest_api) ) {
			$value = Common::hashEmptyField($banks, 'qualify');

			if( !empty($value) ) {
				$value = reset($value);
				$data = array_merge($data, $value);
				$note = Common::hashEmptyField($value, 'BankCommissionSetting.description');
		        $promo = Common::hashEmptyField($value, 'Bank.promo_text');
		        $promo = Common::hashEmptyField($value, 'BankProduct.text_promo', $promo);
		        $promo_name = Common::hashEmptyField($value, 'BankProduct.name');

				$data = array(
					'BankSetting' => $this->RmKpr->setKprBankInstallment($data, array(
						'property_price' => $property_price,
						'credit_total' => $credit_total,
						'down_payment' => $down_payment,
					)),
				);
				$commission = Common::hashEmptyField($data, 'BankSetting.commission');
				$provision = Common::hashEmptyField($data, 'BankSetting.provision');

				$promo_terms = KprCommon::_callTermsConditions(array(
					'commission' => $commission,
					'commission_percent' => $provision,
					'city_name' => $city_name,
					'note' => $note,
				));

				$data['BankSetting']['Description'] = array(
					'promo_name' => $promo_name,
					'promo_info' => $promo,
					'promo_terms' => $promo_terms,
				);
			}

			$this->set(array(
				'data' => $data,
			));
		} else {
			$this->set(array(
				'value' => $data,
				'banks' => $banks,
				'show_banks' => true,
			));

			$this->render('/Elements/blocks/kpr/bank_list_ver2');
		}
	}

	function api_calculator( $bank_setting_id = false ){
		$this->backprocess_get_bank($bank_setting_id);
	}

	public function admin_sales($id = NULL){
		$user_id = $this->user_id;

		if( !empty($user_id) ) {
			$value = $this->User->Kpr->KprBank->BankUser->getData('first', array(
				'conditions' => array(
					'BankUser.id' => $id
				),
			));

			if( !empty($value) ){
				$value = $this->User->Kpr->KprBank->BankUser->getMergeList($value, array(
					'contain' => array(
						'Bank' => array(
							'BankContact',
						),
						'BankBranch',
						'BankUserProfile',
					),
				));

				$title = __('Informasi Marketing Bank');
				$this->set(array(
					'module_title' => $title,
					'title_for_layout' => $title,
					'value' => $value,
				));
			}
			else{
				$this->RmCommon->redirectReferer(__('User tidak ditemukan'));
			}
		}
		else{
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki akses terhadap konten tersebut.'));
		}
	}

	function admin_developer ( $param_booking_code = null ) {
        $kpr_developer = $this->User->ApiAdvanceDeveloper->getData('first', false, array(
			'company' => true,
			'status' => 'request_valid',
			'status_request' => 'approved',
		));
		
		if( !empty($kpr_developer) ) {
			$params = $this->params->params;

			$module_title = __('Pengajuan KPR');
			$data = $this->request->data;

			$booking_code = Common::hashEmptyField($data, 'Kpr.booking_code', $param_booking_code);

			$this->RmCommon->_callRefineParams($this->params);
			$value = $this->RmKpr->_callBooking($booking_code);
			$on_progress_kpr = Common::hashEmptyField($value, 'InvoiceCollector.on_progress_kpr');
			$dataSave = array();

			if( !empty($param_booking_code) && !empty($on_progress_kpr) ) {
				$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
			} else if( !empty($data) ) {
				$dataSave = $this->RmKpr->_callBeforeDeveloperSave($data, $value);
				$result = $this->User->Kpr->doSaveDeveloper( $dataSave );

				$id 			= Common::hashEmptyField($result, 'id');
				$result_data 	= Common::hashEmptyField($result, 'Log.data');
				$client_id 		= Common::hashEmptyField($result_data, 'KprApplication.user_id');
	 			$status 		= Common::hashEmptyField($result, 'status');
	 			
				$dataSave = $this->RmKpr->buildDocument($dataSave, array(
					'save_path' => Configure::read('__Site.document_folder'),
					'owner_id' => !empty($id)?$id:0,
					'client_id' => $client_id,
					'document_type' => 'kprs',
					'options' => array(
						'kpr_id' => $id,
					),
				));
				$this->request->data['CrmProjectDocument'] = Common::hashEmptyField($dataSave, 'CrmProjectDocument');

	 			if( ($status == 'success') && !empty($id) ){
					$project_document 		= Common::hashEmptyField($dataSave, 'CrmProjectDocument', array());
					$project_document_mstr 	= Common::hashEmptyField($dataSave, 'CrmProjectDocumentMstr', array());

					$dataDocument = array_merge($project_document,$project_document_mstr);
					
					if(!empty($dataDocument)){
						$resultDocument = $this->User->CrmProject->CrmProjectDocument->doSaveMany($dataDocument, false, true);

						$log_msg 			= Common::hashEmptyField($resultDocument, 'Log.activity');
						$old_data 			= Common::hashEmptyField($resultDocument, 'Log.old_data');
						$document_id 		= Common::hashEmptyField($resultDocument, 'Log.document_id');
						$error 				= Common::hashEmptyField($resultDocument, 'Log.error');
						$code_error 		= Common::hashEmptyField($resultDocument, 'Log.code_error');
						$validation_data 	= Common::hashEmptyField($resultDocument, 'Log.validation_data');

	        			$this->RmCommon->_saveLog($log_msg, $old_data, $document_id, $error, $code_error, $validation_data);
					}
	 			}

				$this->RmCommon->setProcessParams($result, array(
					'controller' => 'kpr',
					'action' => 'index',
					'admin' => true,
				));
			}

			$this->RmKpr->_callBeforeDeveloperView($value, $dataSave);
			
			$this->set(array(
				'module_title' => $module_title,
				'param_booking_code' => $param_booking_code,
				'_angular' => true,
			));
		} else {
			$this->redirect(array(
				'controller' => 'kpr',
				'action' => 'add',
				'admin' => true,
			));
		}
	}
	
	function admin_options () {
		$group_id = Configure::read('User.group_id');
		$isAgent = Configure::read('__Site.Role.company_agent');
        $kpr_developer = $this->User->ApiAdvanceDeveloper->getData('first', false, array(
			'company' => true,
			'status' => 'request_valid',
			'status_request' => 'approved',
		));

		if( !empty($kpr_developer) && in_array($group_id, $isAgent) ) {
			$this->set(array(
				'module_title' => __('Pengajuan KPR'),
				'active_menu' => 'kpr_add',
			));
		} else {
			$this->redirect(array(
				'controller' => 'kpr',
				'action' => 'add',
				'admin' => true,
			));
		}
	}

	function backprocess_booking(){
		$data = $this->request->data;
		$booking_code = Common::hashEmptyField($data, 'query');

		$values = $this->Kpr->InvoiceCollector->getData('list', array(
			'conditions' => array(
				'InvoiceCollector.booking_code LIKE' => '%'.$booking_code.'%',
			),
			'fields' => array(
				'InvoiceCollector.booking_code',
			),
			'limit' => 5,
		), array(
			'kpr' => true,
		));
		$values = $this->RmCommon->convertDataAutocomplete($values);

		$this->autoLayout = false;
		$this->autoRender = false;

  		return json_encode($values);
	}

	function backprocess_booking_info( $param_booking_code = null ){
		$value = $this->RmKpr->_callBooking($param_booking_code);
		$this->RmKpr->_callBeforeDeveloperView($value);

		$this->set(array(
			'param_booking_code' => $param_booking_code,
		));
		$this->render('/Elements/blocks/kpr/developers/forms/info');
	}

	function api_get_properties(){
		$elements = array(
			'admin_mine' => true,
		);

		if( $this->Rest->isActive() ) {
			$keyword = Common::hashEmptyField($this->request->data, 'keyword');
			$this->paginate = $this->User->Property->getListCompanyProperties($this->parent_id, $keyword, $elements, null, false, array(), 'paginate');
			$properties = $this->paginate('Property');
			
			$this->autoRender = true;
			$this->RmCommon->_callDataForAPI($properties, 'manual');
	        $this->RmCommon->renderRest(array(
	            'is_paging' => true
	        ));
		} else {
			$keyword = $this->RmCommon->filterEmptyField($this->request->data, 'query');
			$this->autoLayout = false;
			$this->autoRender = false;

			$properties = $this->User->Property->getListCompanyProperties($this->parent_id, $keyword, $elements);
			$properties = $this->RmCommon->convertDataAutocomplete($properties);

			return json_encode($properties);
		}
	}

	function admin_process_appraisal($id = false, $kpr_status = NULL){
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id,
			)
		), array(
			'document_status' => 'approved_verification',
		));

		if(!empty($value)){
			$data = $this->request->data;

			if(!empty($data)){
				$note = Common::hashEmptyField($data, 'CrmProjectDocument.note');
				$session_id = Common::hashEmptyField($data, 'CrmProjectDocument.session_id');

				$documentUploaded = $this->User->CrmProject->CrmProjectDocument->getData('first', array(
					'conditions' => array(
						'CrmProjectDocument.session_id' => $session_id,
						'CrmProjectDocument.document_type' => 'payment_appraisal',
					),
				));

				if (!empty($documentUploaded)) {
					$result = $this->User->Kpr->KprBank->doChangeStatus($value, $kpr_status, $note);
					$status = Common::hashEmptyField($result, 'status', 'success');
					
					if( $status == 'success' ) {
						$flag = $this->User->CrmProject->CrmProjectDocument->updateAll(array(
							'CrmProjectDocument.owner_id' => $id,
						), array(
							'CrmProjectDocument.session_id' => $session_id,
							'CrmProjectDocument.document_type' => 'payment_appraisal',
						));

					}

				} else {
					$result = array(
						'status' => 'error',
						'msg'    => 'Mohon upload bukti pembayaran terlebih dahulu',
					);
				}
				
				$this->RmCommon->setProcessParams($result, false, array(
					'ajaxFlash' => true,
				));
			}

			$session_id = String::uuid();
			$this->set(array(
				'id' => $id,
				'session_id' => $session_id,
				'_wrapper_ajax' => 'wrapper-modal-write-2form',
			));
		}else{
			$this->RmCommon->redirectReferer(__('Data tidak ditemukan'), 'error');
		}
		
	}

	public function admin_process_appraisal_upload( $id = false, $session_id = null ) {
		$value = $this->User->Kpr->KprBank->getData('first', array(
			'conditions' => array(
				'KprBank.id' => $id,
			)
		), array(
			'document_status' => 'approved_verification',
		));
    	
		$options = array(
			'error' => true,
			'message' => __('Mohon ungah bukti pembayaran terlebih dahulu'),
		);
		$this->autoLayout = false;
		$this->autoRender = false;

		if( !empty($this->request->data['files']) ) {
			$files = $this->request->data['files'];
			$info = array();
			$saveFolder = Configure::read('__Site.document_folder');

			foreach ($files as $key => $val) {
				$prefixImage = String::uuid();
				$file_name = $this->RmCommon->filterEmptyField($val, 'name');

				$data = $this->RmImage->upload($val, $saveFolder, $prefixImage, array(
					'fullsize' => true,
					'allowed_all_ext' => true
				));

				$full_base_url = FULL_BASE_URL;
				$path_uploaded = sprintf('/img/view/%s/fullsize', $saveFolder);
				$photo_name    = $this->RmCommon->filterEmptyField($data, 'imageName');

				$save_file_img = sprintf('%s%s%s',$full_base_url, $path_uploaded, $photo_name);

				$data = array_merge($data, array(
					'CrmProjectDocument' => array(
						'document_type' => 'payment_appraisal',
						'owner_id' => 0,
						'session_id' => !empty($session_id)?$session_id:0,
						'save_path' => $saveFolder,
						'name' => $file_name,
						'file' => $save_file_img,
					),
				));
				
				$file = $this->User->CrmProject->CrmProjectDocument->doSave($data);
				$info[] = $file;
			}

	  		return json_encode($info);
		} else {
			return false;
		}
	}
}
?>