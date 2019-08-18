<?php
class RmSettingComponent extends Component {
	public $components = array(
		'RmCommon', 'RmImage', 'RmUser', 'Auth',
        'RmKpr',
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

    function _callBeforeCompanyConfigSave ( $data, $old_data = false ) {
        if( !empty($data) ) {
	        $save_path_general = Configure::read('__Site.general_folder');

			$data = $this->RmImage->_uploadPhoto( $data, 'UserCompanyConfig', 'favicon', $save_path_general, true );

			if( isset($data['UserCompanyConfig']['date']) ) {
				$date = $this->RmCommon->filterEmptyField( $data, 'UserCompanyConfig', 'date' );

				$data = $this->RmCommon->dataConverter($data, array(
					'date' => array(
						'UserCompanyConfig' => array(
							'contract_date',
						),
					)
				));

				$params	= $this->RmCommon->_callConvertDateRange(array(), $date);
				$data['UserCompanyConfig']['live_date']	= $this->RmCommon->filterEmptyField( $params, 'date_from' );
				$data['UserCompanyConfig']['end_date']	= $this->RmCommon->filterEmptyField( $params, 'date_to' );
			}
            
            $mtLocation = Common::hashEmptyField($data, 'UserCompanyConfig.mt_location_name');

        }

        return $data;
    }

    function _callBeforeCompanyConfigView ( $data, $value = array() ) {
        $data   = (array) $data;
        $value  = (array) $value;

        if( !empty($data) ) {
			$live_date = $this->RmCommon->filterEmptyField( $data, 'UserCompanyConfig', 'live_date' );
			$end_date = $this->RmCommon->filterEmptyField( $data, 'UserCompanyConfig', 'end_date' );

			$data = $this->RmCommon->dataConverter($data, array(
				'date' => array(
					'UserCompanyConfig' => array(
						'contract_date',
					),
				)
			), true);

			$data['UserCompanyConfig']['date'] = $this->RmCommon->_callReverseDateRange($live_date, $end_date);

            $mtRegionID     = Common::hashEmptyField($data, 'UserCompanyConfig.mt_region_id');
            $mtCityID       = Common::hashEmptyField($data, 'UserCompanyConfig.mt_city_id');
            $mtSubareaID    = Common::hashEmptyField($data, 'UserCompanyConfig.mt_subarea_id');

        } else {
            $data['UserCompanyConfig']['is_ebrosur_frontend'] = true;

            $mtRegionID     = Common::hashEmptyField($value, 'UserCompanyConfig.mt_region_id');
            $mtCityID       = Common::hashEmptyField($value, 'UserCompanyConfig.mt_city_id');
            $mtSubareaID    = Common::hashEmptyField($value, 'UserCompanyConfig.mt_subarea_id');
        }

        if($mtSubareaID){
            $this->controller->loadModel('ViewLocation');

            $location = $this->controller->ViewLocation->getData('first', array(
                'conditions' => array(
                    'ViewLocation.region_id'    => $mtRegionID, 
                    'ViewLocation.city_id'      => $mtCityID, 
                    'ViewLocation.subarea_id'   => $mtSubareaID, 
                ), 
            ));

            $locationName = array_filter(array(
                Common::hashEmptyField($location, 'ViewLocation.subarea_name'), 
                Common::hashEmptyField($location, 'ViewLocation.city_name'), 
                Common::hashEmptyField($location, 'ViewLocation.region_name'), 
            ));

            $data = Hash::insert($data, 'UserCompanyConfig.mt_location_name', implode(', ', $locationName));
        }

        $data = Hash::insert($data, 'UserCompanyConfig.mt_region_id', $mtRegionID);
        $data = Hash::insert($data, 'UserCompanyConfig.mt_city_id', $mtCityID);
        $data = Hash::insert($data, 'UserCompanyConfig.mt_subarea_id', $mtSubareaID);

        $data = Hash::insert($data, 'UserCompanyConfig.region_id', $mtRegionID);
        $data = Hash::insert($data, 'UserCompanyConfig.city_id', $mtCityID);
        $data = Hash::insert($data, 'UserCompanyConfig.subarea_id', $mtSubareaID);
        return $data;
    }

    function _callBeforeSaveMobileAppVersion( $value = null, $id = null ){
    	$requestData =& $this->controller->request->data;
    	$data = $requestData;
		$params = $this->controller->params->params;
		$this->MobileAppVersion = ClassRegistry::init('MobileAppVersion');

		if ( !empty($data) ) {
			if( !empty($id) ) {
			 	$data = Hash::insert($data, 'MobileAppVersion.id', $id);
			}

			$result = $this->MobileAppVersion->doSave($data, $id);

			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'settings',
				'action' => 'mobile_app_versions',
				'admin' => true,
			));
		}else{
			$requestData = $value;
		}

		$this->controller->set(array(
			'active_menu' => 'mobile_app_versions',
		));
    }

    function generateKprName($data){
        $monthly = Configure::read('__Site.monthly.getName');
        $birthdayPlace = Common::hashEmptyField($data, 10);
        $dp = Common::hashEmptyField($data, 12, Configure::read('__Site.KPR.min_dp'));
        $tenor = Common::hashEmptyField($data, 14);

        $birth_arr = explode(',' , $birthdayPlace);
        $birthPlace = !empty($birth_arr[0]) ? trim($birth_arr[0]) : false;
        $birthDay = !empty($birth_arr[1]) ? trim($birth_arr[1]) : false;

        // tenor jika ada text tahun akan dihilangkan 
        $tenor = str_replace('tahun', '', $tenor);
        $tenor = str_replace('Tahun', '', $tenor);
        // 

        // format date
        if($birthDay){
            $birth_arr = explode(' ', $birthDay);

            if(!empty($birth_arr[1])){
                $birth_arr[1] = Common::hashEmptyField($monthly, $birth_arr[1]);
            }
            $birthDay = implode('/', $birth_arr);
        }

        if($dp){
            $dp = str_replace('%', '', $dp);
        }

        $data = array(
            'agentEmail' => Common::hashEmptyField($data, 2),
            'bankCode' => Common::hashEmptyField($data, 3),
            'clientName' => Common::hashEmptyField($data, 4),
            'clientEmail' => Common::hashEmptyField($data, 5),
            'clientNoHp' => Common::hashEmptyField($data, 6),
            'clientAddress' => Common::hashEmptyField($data, 7),
            'jobName' => Common::hashEmptyField($data, 8),
            'marriedStatus' => Common::hashEmptyField($data, 9),
            'ktp' => Common::hashEmptyField($data, 11),
            'dp' => $dp,
            'plafond' => Common::hashEmptyField($data, 13),
            'tenor' => trim($tenor),
            'mls_id' => Common::hashEmptyField($data, 15),
            'propertyPrice' => Common::hashEmptyField($data, 16),
            'birthPlace' => $birthPlace,
            'birthDay' => !empty($birthDay)?$birthDay:NULL,
        );

        return $this->RmCommon->dataConverter($data, array(
            'date' => array(
                'soldDate',
                'birthDay',
            ),
            'price' => array(
                'plafond',
                'propertyPrice',
            ),
        ));
    }

    function getGenerateKPR($data){
        $dataSave = array();
        $parent_id = Configure::read('Principle.id');
        $data = $this->generateKprName($data);
        $mls_id = Common::hashEmptyField($data, 'mls_id');

        ########################## CHECK KPR SUDAH DIINPUT ATAU BELUM ###################
        $kpr = $this->controller->User->Kpr->getData('first', array(
            'conditions' => array(
                'Kpr.is_generate' => true,
                'Kpr.mls_id' => $mls_id,
            ),
        ));
        #################################################################################

        ########################## PROPERTI ##########################
        $property = $this->controller->User->Property->getData('first', array(
            'conditions' => array(
                'Property.mls_id' => $mls_id,
            ),
        ), array(
            'status' => 'all'
        ));
        ##############################################################

        if($property && empty($kpr)){
            $agentEmail = Common::hashEmptyField($data, 'agentEmail');
            $bankCode = Common::hashEmptyField($data, 'bankCode');

            // data client
            $clientName = Common::hashEmptyField($data, 'clientName');
            $clientEmail = Common::hashEmptyField($data, 'clientEmail');
            $clientNoHp = Common::hashEmptyField($data, 'clientNoHp');
            $clientAddress = Common::hashEmptyField($data, 'clientAddress');
            $jobName = Common::hashEmptyField($data, 'jobName');
            $marriedStatus = Common::hashEmptyField($data, 'marriedStatus');
            $birthPlace = Common::hashEmptyField($data, 'birthPlace');
            $birthDay = Common::hashEmptyField($data, 'birthDay');
            $ktp = Common::hashEmptyField($data, 'ktp');

            $property_id = Common::hashEmptyField($property, 'Property.id');
            $property_type_id = Common::hashEmptyField($property, 'Property.property_type_id');
            $keyword = Common::hashEmptyField($property, 'Property.keyword');
            $price = Common::hashEmptyField($property, 'Property.price_measure');

            $propertyPrice = Common::hashEmptyField($data, 'propertyPrice', $price);

            // param KPR
            $plafond = Common::hashEmptyField($data, 'plafond');
            $dpPercent = Common::hashEmptyField($data, 'dp');

            if(empty($dpPercent)){
                $downPayment = $propertyPrice - $plafond;
                $dpPercent = $this->RmKpr->_callDpPercent($propertyPrice, $downPayment);
            
            } else {
                $downPayment = $this->RmKpr->_callCalcDp($propertyPrice, $dpPercent);
            }

            if(empty($plafond)){
                $plafond = $this->RmKpr->calcLoanFromDp($propertyPrice, $downPayment);
            }

            $tenor = Common::hashEmptyField($data, 'tenor');
            // 

            $name_arr = explode(' ', $clientName);
            $firstName = Common::hashEmptyField($name_arr, 0);
            $lastName = Common::hashEmptyField($name_arr, 1, '');

            $statusMarital = ($marriedStatus == 'Menikah') ? 'marital' : 'single';

            $elements = array(
                'company' => true,
                'status' => array(
                    'not-active',
                    'active',
                ),
            );

            ###################### JOB ###################################
            $job = $this->controller->User->UserClient->JobType->getData('first', array(
                'conditions' => array(
                    'JobType.name' => $jobName,
                ),
            ));
            $jobTypeID = Common::hashEmptyField($job, 'JobType.id', 0);
            ##############################################################

            ###################### agent ###################################
            $agent = $this->controller->User->getData('first', array(
                'conditions' => array(
                    'User.email' => $agentEmail,
                    'User.group_id' => 2,
                ),
            ), $elements);

            if($agent){
                $agent_id = Common::hashEmptyField($agent, 'User.id');
            } else {
                $name_arr = explode('@', $agentEmail);
                $full_name = !empty($name_arr[0]) ? $name_arr[0] : false;

                $dataSave = array(
                    'User' => array(
                        'full_name' => $full_name,
                        'email' => $agentEmail,
                        'password' => $this->Auth->password($agentEmail),
                        'code' => $this->RmUser->_generateCode('user_code'),
                        'group_id' => '2',
                        'parent_id' => $parent_id,
                        'active' => true,
                        'status' => true,
                    ),
                );
                $this->controller->User->create();
                $flag = $this->controller->User->save($dataSave, false);
                $agent_id = $this->controller->User->id;
            }
            ####################################################################

            ###################### Client ###################################
            $client = $this->controller->User->UserClient->getData('first', array(
                'conditions' => array(
                    'User.email' => $clientEmail,
                    'UserClient.company_id' => $parent_id,
                ),
                'contain' => array(
                    'User',
                ),
            ));

            $dataClient = array(
                'full_name' => $clientName,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'no_hp' => $clientNoHp,
                'address' => $clientAddress,
                'birthplace' => $birthPlace,
                'birthday' => $birthDay,
                'status_marital' => $statusMarital,
                'ktp' => $ktp,
                'job_type' => $jobTypeID,
            );

            if($client){
                $client_id = Common::hashEmptyField($client, 'User.id');
                $user_client_id = Common::hashEmptyField($client, 'UserClient.id');

                $this->controller->User->UserClient->id = $user_client_id;
                $flag = $this->controller->User->UserClient->save(array(
                    'UserClient' => $dataClient,
                ));

            } else {
                $dataSave = array();
                $elements = Common::_callUnset($elements, array('company'));
                $elements['status'] = 'all';

                $user = $this->controller->User->getData('first', array(
                    'conditions' => array(
                        'User.email' => $clientEmail,
                    ),
                ), $elements);

                if($user){
                    $user_id = Common::hashEmptyField($user, 'User.id');
                    $dataSave['User']['id'] = $user_id;
                    $dataSave['User']['deleted'] = 0;
                    $dataSave['User']['status'] = 1;
                } else {
                    $dataSave['User']= array(
                        'parent_id' => $parent_id,
                        'email' => $clientEmail,
                        'username' => $clientEmail,
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'password' => $this->Auth->password($clientEmail),
                        'code' => $this->RmUser->_generateCode('user_code'),
                        'group_id' => Configure::read('__Site.Global.Variable.Company.client'),
                    );
                }

                $value_arr['UserClient'] = array_merge(array(
                    'company_id' => $parent_id,
                    'agent_id' => $agent_id,
                    'client_type_id' => '1',
                    'username' => $clientEmail,
                    'password' => $this->Auth->password($clientEmail),
                ), $dataClient);

                if(!empty($user_id)){
                    $value_arr['UserClient']['user_id'] = $user_id;
                }

                $dataSave['UserClient'][] = $value_arr;

                $this->controller->User->saveAll($dataSave, array(
                    'validate' => false,
                ));
                $client_id = $this->controller->User->id;
            }
            ####################################################

            ########################## bank ##########################
            $bank = $this->controller->User->Kpr->KprBank->Bank->getData('first', array(
                'conditions' => array(
                    'Bank.code' => $bankCode
                ),
            ));
            $bank_id = Common::hashEmptyField($bank, 'Bank.id');

            ## get exclusive
            $bank_arr = $this->controller->User->Kpr->KprBank->Bank->BankProduct->getExclusive($bank, $bank_id, $property);

            $exlusive = Common::hashEmptyField($bank_arr, 'exlusive');

            $bank_setting_id = Common::hashEmptyField($exlusive, 'BankSetting.id');

            ####################################################
        
			$dataSave = array(
				'Kpr' => array(
                    'type' => 'kpr',
                    'type_log' => 'apply_kpr',
                    'company_id' => $parent_id,
                    'mls_id' => $mls_id,
                    'agent_id' => $agent_id,
                    'property_id' => $property_id,
                    'code' => $this->controller->User->Kpr->generateCode(),
                    'user_id' => $client_id,
                    'client_email' => $clientEmail,
                    'client_name' => $clientName,
                    'client_hp' => $clientNoHp,
                    'client_job_type_id' => $jobTypeID,
                    'address' => $clientAddress,
                    'birthplace' => $birthPlace,
                    'birthday' => $birthDay,
                    'ktp' => $ktp,
                    'keyword' => $keyword,
                    'property_price' => $propertyPrice,
                    'credit_total' => $tenor,
                    'dp' => $dpPercent,
                    'down_payment' => $downPayment,
                    'sold_date' => date('Y-m-d H:i:s'),
                    'document_status' => 'completed',
                    'is_generate' => true,
                ),
                'KprBank' => array(
                    array(
                        'KprBank' => array(
                            'bank_id' => $bank_id,
                            'setting_id' => $bank_setting_id,
                            'property_price' => $propertyPrice,
                            'property_type_id' => $property_type_id,
                            'dp' => $dpPercent,
                            'down_payment' => $downPayment,
                            'credit_total' => $tenor,
                            'loan_price' => $plafond,
                            'document_status' => 'completed',
                            'is_generate' => true,
                        ),
                        'KprBankDate' => array(
                            array(
                                'KprBankDate' => array(
                                    'slug' => 'approved_bi_checking',
                                    'action_date' => date('Y-m-d H:i:s'),
                                    'note' => __('Lulus BI Checking')
                                ),
                            ),
                            array(
                                'KprBankDate' => array(
                                    'slug' => 'approved_verification',
                                    'action_date' => date('Y-m-d H:i:s'),
                                    'note' => __('Lulus dokumen verifikasi')
                                ),
                            ),
                            array(
                                'KprBankDate' => array(
                                    'slug' => 'approved_bank',
                                    'action_date' => date('Y-m-d H:i:s'),
                                    'note' => __('Lulus appraisal')
                                ),
                            ),
                            array(
                                'KprBankDate' => array(
                                    'slug' => 'approved_credit',
                                    'action_date' => date('Y-m-d H:i:s'),
                                    'note' => __('Bank sudah melakukan Akad Kredit')
                                ),
                            ),
                            array(
                                'KprBankDate' => array(
                                    'slug' => 'completed',
                                    'action_date' => date('Y-m-d H:i:s'),
                                    'note' => __('Terima kasih sudah pakai sistem kami prime system')
                                ),
                            ),
                        ),
                        'KprBankInstallment' => array(
                            array(
                                'KprBankInstallment' => $this->RmKpr->setKprBankInstallment($exlusive, array(
                                    'down_payment' => $downPayment,
                                    'property_price' => $propertyPrice,
                                    'credit_total' => $tenor,
                                ))
                            ),
                            array(
                                'KprBankInstallment' => array_merge($this->RmKpr->setKprBankInstallment($exlusive, array(
                                    'down_payment' => $downPayment,
                                    'property_price' => $propertyPrice,
                                    'credit_total' => $tenor,
                                )), array(
                                    'status_confirm' => true
                                ))
                            ),
                        ),
                    ),
                ),
            );
        } else {
            if(!empty($kpr)){
                $flag = true;   
            }
        }

        return array(
            'dataSave' => $dataSave,
            'property' => $property,
            'not_save' => !empty($flag) ? $flag : false,
        );
    }

    /* =====================================================================
       get data package from RKU, and cache them, depends on company and page
       karena ada 2 kondisi dimana company punya settingan sendiri, dan ketika
       kondisi agen punya settingan sendiri maka,
       buat cache berdasar per company per user 
    ===================================================================== */
    function callDataMembershipRKU($options = array()){
        $validate_as_admin = Common::hashEmptyField($options, 'validate_as_admin');
        $do_cache     = Common::hashEmptyField($options, 'do_cache');
        $cache_page   = Common::hashEmptyField($options, 'cache_page');
        $user_own     = Common::hashEmptyField($options, 'user_own');

        $company_id   = Configure::read('Config.Company.data.UserCompany.id');

        // cache kalau yg login agent
        if( !empty($user_own) ) {
            $id_user   = Configure::read('User.data.id');

            $cacheData = sprintf('Data.Membership.RKU.%s.%s.%s', $company_id, $cache_page, $id_user);
            $cacheFlag = sprintf('Flag.Membership.RKU.%s.%s.%s', $company_id, $cache_page, $id_user);

            if ($validate_as_admin) {
                // read cache disini ketika admin ingin mempremiumkan properti
                $cacheData = sprintf('Data.Membership.RKU.%s.%s', $company_id, $cache_page);
                $cacheFlag = sprintf('Flag.Membership.RKU.%s.%s', $company_id, $cache_page);
            }

        } else {
            // cache kalau yg login admin kesini
            $cacheData = 'Data.Membership.RKU.List.Master.Package';
            $cacheFlag = 'Flag.Membership.RKU.List.Master.Package';
        }

        $packages  = Cache::read($cacheData, 'default');
        $flag      = Cache::read($cacheFlag, 'default');

        if( empty($flag) ) {
            $custom_link  = Common::hashEmptyField($options, 'custom_link', false);
            $path_link    = Common::hashEmptyField($options, 'path_link');

            if ($custom_link) {
                $link = $path_link;
            } else {
                $link = sprintf('api/memberships/list_package/get_package:1');
            }
            $result = $this->RmCommon->getAPI($link, array(
                'header' => array(
                    'slug' => 'api-membership-rku',
                ),
            ));

            $packages = Common::hashEmptyField($result, 'data');

            // selain login sebagai admin cache berdasarkan company id, dan user id
            if ($do_cache) {
                Cache::write($cacheData, $packages, 'default');
                Cache::write($cacheFlag, true, 'default');
            }

        }

        // debug($packages);die();
        return $packages;

    }

    // get package info, in page properti admin_index, info agent
    function validatePackageRKU($options = array()){
        $status     = 'active';
        $do_cache   = true;
        $is_SA      = $this->RmCommon->_isAdmin();
        $is_admComp = $this->RmCommon->_isCompanyAdmin();

        $action     = Common::hashEmptyField($this->controller->params->params, 'action');

        $company    = Configure::read('Config.Company.data');
        $is_config  = Common::hashEmptyField($company, 'UserCompanyConfig.is_block_premium_listing');
        $package_id = Common::hashEmptyField($company, 'UserCompanyConfig.premium_listing', 0); // package id config by company
        
        // log in as admin
        $membership_package_id = false;
        if ($is_SA && $action || $is_admComp && $action) {
            $pass  = Common::hashEmptyField($this->controller->params->params, 'pass');

            if (!empty($pass)) {
                $agent_id   = $pass[0];
                $data_agent = $this->RmUser->getUser($agent_id);

                $membership_package_id = Common::hashEmptyField($data_agent, 'User.membership_package_id');
                $is_config = 1;
            }

            $do_cache = false;

        } else {
            // is membership id agent not empty?
            $membership_package_id = Configure::read('User.data.membership_package_id'); // user package id
            $is_config = 1;
        }

        if ( !empty($membership_package_id)) {
            // if membership_package_id is not empty
            $package_id = $membership_package_id;
            $status     = 'all';

        }

        $path_link = sprintf('api/memberships/list_package/get_package:%s/package_id:%s/status:%s', $is_config, $package_id, $status);

        $cache_page  = Common::hashEmptyField($options, 'cache_page');

        $opsi_link = array(
            'custom_link'   => true,
            'user_own'   => true,
            'path_link'     => $path_link,
            'cache_page'    => $cache_page,
            'do_cache'      => $do_cache,
        );

        $packages = $this->callDataMembershipRKU($opsi_link);

        return $packages;
    }

    function _callBeforeSaveKPR($data){
    	App::import('Vendor', 'excelreader/excel_reader2');

    	$dataimports = $result = array();

        $message = array(
            'status' => 'error',
            'msg' => __('Data tidak bisa diproses, mohon lihat kembali format .xls yang kami berikan, isi sesuai tabel'),
        );

    	if(!empty($data['KprBank']['import'])){
    		$type = Common::hashEmptyField($data, 'KprBank.import.type');

    		if($type == 'application/vnd.ms-excel'){
    			$tmp_name = Common::hashEmptyField($data, 'KprBank.import.tmp_name');
    			$dataimport = new Spreadsheet_Excel_Reader($tmp_name, false);

    			$dataimports = $dataimport->dumptoarray();
                
                if($dataimports){
                    foreach ($dataimports as $key => $data) {
                        if($key > 1){
                            $data = $this->getGenerateKPR($data);

                            // set data duplicate not save
                            $mls_id = Common::hashEmptyField($data, 'property.Property.mls_id');
                            $not_save = Common::hashEmptyField($data, 'not_save');

                            if($mls_id && $not_save){
                                $not_save_arr[] = $mls_id;
                            }
                            // 

                            $result = $this->controller->User->Kpr->generateSaveAll($data, $result);
                        }
                    }

                    if($result){
                        $mls_id  = implode(', ', $result);

                        $msg = __('Berhasil simpan data KPR dengan kode properti : %s', $mls_id);

                        $message = array(
                            'status' => 'success',
                            'msg' => $msg,
                            'Log' => array(
                                'activity' => $msg,
                                'old_data' => $dataimports,
                                'document_id' => $mls_id,
                            ),
                        );
                    } else {
                        $message = array(
                            'status' => 'error',
                            'msg' => __('Data tidak bisa diproses, file yang anda upload terjadi  duplikat, check kembali properti ID anda'),
                        );
                    }
                }

                $this->RmCommon->setProcessParams($message);

    		} else {
    			$data['KprBank']['import'] = '';

                $result = array(
                    'msg' => __('Gagal mengimport data.'),
                    'status' => 'error'
                );

                $this->RmCommon->setProcessParams($result, false, array(
                    'redirectError' => true,
                ));
    		}
    	}
    	return $data;
    }
}
?>