<?php
class EbrosurRequest extends AppModel {
	var $name = 'EbrosurRequest';
	var $displayField = 'name';

    var $validate = array(
        'user_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih agen',
            ),
        ),
        'property_action_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon masukkan status properti Anda',
            ),
        ),
        'region_id' => array(
            'validateMLSID' => array(
                'rule' => array('validateMLSID'),
                'message' => 'Mohon pilih provinsi properti Anda',
            ),
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'Mohon pilih provinsi properti Anda',
            // ),
        ),
        'city_id' => array(
            'validateMLSID' => array(
                'rule' => array('validateMLSID'),
                'message' => 'Mohon pilih kota properti Anda',
            ),
            // 'notempty' => array(
            //     'rule' => array('notempty'),
            //     'message' => 'Mohon pilih kota properti Anda',
            // ),
        ),
        'cronjob_period_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih waktu pengiriman eBrosur Anda',
            ),
        ),
        'agent_id' => array(
            'validationAgent' => array(
                'rule' => array('validationAgent'),
                'message' => 'Mohon pilih agen yang Anda inginkan',
            ),
        )
    );

    var $hasMany = array(
        'EbrosurTypeRequest' => array(
            'className' => 'EbrosurTypeRequest',
            'foreignKey' => 'ebrosur_request_id',
        ),
        'EbrosurAgentRequest' => array(
            'className' => 'EbrosurAgentRequest',
            'foreignKey' => 'ebrosur_request_id',
        ),
        'EbrosurClientRequest' => array(
            'className' => 'EbrosurClientRequest',
            'foreignKey' => 'ebrosur_request_id',
        ),
    );

    var $belongsTo = array(
        'CronjobPeriod' => array(
            'className' => 'CronjobPeriod',
            'foreignKey' => 'cronjob_period_id',
        ),
        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'region_id',
        ),
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city_id',
        ),
        'PropertyAction' => array(
            'className' => 'PropertyAction',
            'foreignKey' => 'property_action_id',
        ),
        'Certificate' => array(
            'className' => 'Certificate',
            'foreignKey' => 'certificate_id',
        ),
        'PropertyDirection' => array(
            'className' => 'PropertyDirection',
            'foreignKey' => 'property_direction_id',
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
    );

    function validateMLSID($data){
        $result = true;
        $key_field = key($data);
        
        if(empty($this->data['EbrosurRequest']['mls_id'])){
            if(empty($data[$key_field])){
                $result = false;
            }
        }

        return $result;
    }

    function validationAgent($data){
        $result = false;

        if( !empty($data['agent_id']) && is_array($data['agent_id'])){
            foreach ($data['agent_id'] as $key => $value) {
                if(!empty($value)){
                    $result = true;

                    break;
                }
            }
        }

        return $result;
    }

	function getData( $find = 'all', $options = array(), $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $company = isset($elements['company'])?$elements['company']:true;

		$default_options = array(
			'conditions' => array(),
			'order'=> array(
				'EbrosurRequest.created' => 'DESC',
			),
			'fields' => array(),
			'contain' => array(),
			'group' => array(),
		);

        switch ($status) {
            case 'active':
                $statusConditions = array(
                    'EbrosurRequest.status' => 1
                );
                break;
            
            default:
                $statusConditions = array(
                    'EbrosurRequest.status'=> array(0, 1),
                );
                break;
        }

        if( !empty($statusConditions) ) {
            $default_options['conditions'] = $statusConditions;
        }

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['EbrosurRequest.company_id'] = $parent_id;
        }

		if(!empty($options['conditions'])){
			$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
		}
        if(!empty($options['order'])){
            $default_options['order'] = $options['order'];
        }
        if( isset($options['contain']) && empty($options['contain']) ) {
            $default_options['contain'] = false;
        } else if(!empty($options['contain'])){
            $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getMerge ( $data, $id ) {
		if( empty($data['EbrosurRequest']) ) {
			$value = $this->getData('first', array(
				'conditions' => array(
					'EbrosurRequest.id' => $id,
				),
			));

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}

    function doSpecification( $data, $value = false, $validate = false, $property_id = false, $id = false, $save_session = true ) {
        $result = false;
        $company_id = Configure::read('Principle.id');

        if ( !empty($data) ) {
            if( empty($validate) ) {
                if( !empty($id) ) {
                    $this->id = $id;
                } else {
                    $this->create();
                    $data['EbrosurRequest']['company_id'] = $company_id;
                }
            }

            $this->set($data);
            if( $this->validates()) {
                $flagSave = true;

                if(!empty($validate) && !empty($save_session)){
                    $sessionName = '__Site.EbrosurRequest.SessionName.Spesification';
                    CakeSession::write($sessionName, $data);
                }else{
                    $flagSave = $this->save();
                }
            	
                if($flagSave){
                    $result = array(
                        'msg' => __('Berhasil menyimpan spesifikasi permintaan eBrosur Anda'),
                        'status' => 'success',
                    );
                }else{
                    $result = array(
                        'msg' => __('Gagal menyimpan spesifikasi permintaan eBrosur Anda, mohon lengkapi semua data yang diperlukan'),
                        'status' => 'error',
                    );
                }
            } else {
                $result = array(
                    'msg' => __('Gagal menyimpan spesifikasi permintaan eBrosur Anda, mohon lengkapi semua data yang diperlukan'),
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $value = $this->_setFieldMinMax($value, 'price');

            if(!empty($value['EbrosurRequest']['price'])){
                $price = (string) $value['EbrosurRequest']['price'];
                $price_check = strpos($price, '<');
                
                if($price_check !== false){
                    $price_check = str_replace('<', '', $value['EbrosurRequest']['price']);
                    $value['EbrosurRequest']['price'] = '0-'.$price_check;
                }
            }

            $value = $this->_setFieldMinMax($value, 'lot_size');
            $value = $this->_setFieldMinMax($value, 'building_size');

            $result['data'] = $value;
        }
        return $result;
    }

    function doAgent( $data, $value = false, $validate = false, $id = false, $save_session = true ) {
        $result = false;

        if ( !empty($data) ) {

            $flag_validate = true;

            if($validate){
                $flag_validate = $this->EbrosurAgentRequest->validate($data);
            }
            
            if( $flag_validate ) {
                $result = array(
                    'msg' => __('Berhasil menyimpan agen permintaan eBrosur Anda.'),
                    'status' => 'success',
                    'redirect' => array(
                        'controller' => 'ebrosurs',
                        'action' => 'success',
                        'client' => true,
                    ),
                );

                if(!empty($validate) && !empty($save_session)){
                    $sessionName = '__Site.EbrosurRequest.SessionName.Agent';
                    CakeSession::write($sessionName, $data);
                }else{
                    $result_save = $this->EbrosurAgentRequest->doSave($data, $id);

                    if(!$result_save){
                        $result = array(
                            'msg' => __('Gagal menyimpan agen permintaan eBrosur Anda.'),
                            'status' => 'error'
                        );
                    }
                }
            } else {
                $result = array(
                    'msg' => __('Gagal menyimpan agen permintaan eBrosur Anda, mohon pilih agen yang Anda inginkan.'),
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }
        return $result;
    }

    function doClient( $data, $value = false, $validate = false, $id = false, $save_session = true ) {
        $result = false;

        if ( !empty($data) ) {

            $flag_validate = true;

            if($validate){
                $flag_validate = $this->EbrosurClientRequest->validate($data);
            }
            
            if( $flag_validate ) {
                $result = array(
                    'msg' => __('Berhasil menyimpan klien permintaan eBrosur Anda.'),
                    'status' => 'success',
                    'redirect' => array(
                        'controller' => 'ebrosurs',
                        'action' => 'request_success',
                        'admin' => true,
                    ),
                );

                if(!empty($validate) && !empty($save_session)){
                    $sessionName = '__Site.EbrosurRequest.SessionName.Agent';
                    CakeSession::write($sessionName, $data);
                }else{
                    $result_save = $this->EbrosurClientRequest->doSave($data, $id);

                    if(!$result_save){
                        $result = array(
                            'msg' => __('Gagal menyimpan klien permintaan eBrosur Anda.'),
                            'status' => 'error'
                        );
                    }
                }
            } else {
                $result = array(
                    'msg' => __('Gagal menyimpan klien permintaan eBrosur Anda, mohon pilih klien yang Anda inginkan.'),
                    'status' => 'error',
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }
        return $result;
    }

    function doBasic( $data, $value = false, $validate = false, $id = false, $save_session = true ) {
        $result = false;
        $user_id = Configure::read('User.id');
        $company_id = Configure::read('Principle.id');

        if ( !empty($data) ) {
            if( empty($validate) ) {
                if( !empty($id) ) {
                    $this->id = $id;
                } else {
                    $this->create();
                    $data['EbrosurRequest']['company_id'] = $company_id;
                }
            }

            if(!Configure::read('User.admin')){
                $data['EbrosurRequest']['user_id'] = $user_id;
            }else{
                $data['EbrosurRequest']['created_by'] = $user_id;
            }

            if(!empty($data['EbrosurTypeRequest']['property_type_id'])){
                if(is_array($data['EbrosurTypeRequest']['property_type_id'])){
                    $value = array_filter($data['EbrosurTypeRequest']['property_type_id']);
                    
                    if( !empty($value) ) {
                        $result = array();

                        foreach ($value as $id => $boolean) {
                            if( !empty($id) ) {
                                $result[$id] = true;
                            }
                        }

                        $value = $result;
                    }else{
                        $value = '';
                    }

                    $data['EbrosurTypeRequest']['property_type_id'] = $value;
                }
            }else{
                $data['EbrosurTypeRequest']['property_type_id'] = '';
            }
            
            $this->set($data);

            if( $this->validates() ) {
                $flagSave = true;

                if( !empty($validate) && !empty($save_session) ) {
                    CakeSession::write('__Site.EbrosurRequest.SessionName.Basic', $data);
                } else {
                    $flagSave = $this->save();
                    $id = $this->id;

                    if(!empty($flagSave['EbrosurTypeRequest']['property_type_id'])){
                        $this->EbrosurTypeRequest->doSave($flagSave, $id);
                    }
                }

                if( !empty($flagSave) ) {
                    $result = array(
                        'msg' => __('Berhasil menyimpan informasi dasar eBrosur Anda'),
                        'status' => 'success',
                        'id' => $id,
                    );
                } else {
                    $result = array(
                        'msg' => __('Gagal menyimpan eBrosur Anda, mohon lengkapi semua data yang diperlukan'),
                        'status' => 'error',
                    );
                }
            } else {
                $result = array(
                    'msg' => __('Gagal menyimpan eBrosur Anda, mohon lengkapi semua data yang diperlukan'),
                    'status' => 'error',
                );
            }

            if($result['status'] == 'error'){
                $result['data'] = $data;    
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function doSave($data, $validate = false, $id = false ){
        $company_id = Configure::read('Principle.id');
        $company_data = Configure::read('Config.Company.data');
        $company_email = !empty($company_data['UserCompany']['contact_email'])?$company_data['UserCompany']['contact_email']:false;
        $client_id = !empty($data['EbrosurRequest']['user_id'])?$data['EbrosurRequest']['user_id']:false;
        
        if( empty($validate) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
                $data['EbrosurRequest']['company_id'] = $company_id;
            }
        }

        $agent_id_collect = array();
        if(!empty($data['EbrosurAgentRequest']['agent_all'])){
           $agents = $this->User->UserClientRelation->getData('list', array(
                'conditions' => array(
                    'UserClientRelation.user_id' => $client_id,
                    'UserClient.company_id' => $company_id
                ),
                'contain' => array(
                    'UserClient'
                ),
                'fields' => array(
                    'UserClientRelation.agent_id', 'UserClientRelation.agent_id'
                )
            ));
           
           if(!empty($agents)){
                $agent_id_collect = $data['EbrosurAgentRequest']['agent_id'] = $agents;
           }
        }

        $this->set($data);
        
        if( $this->validates()) {
            $flagSave = true;

            $result = array(
                'msg' => __('Berhasil menyimpan permintaan eBrosur Anda, eBrosur akan dikirimkan sesuai dengan waktu pengiriman yang Anda telah atur sebelumnya'),
                'status' => 'success',
                'redirect' => array(
                    'controller' => 'ebrosurs',
                    'action' => 'success',
                    'client' => true,
                ),
            );

            if(empty($validate)){
                $send_directly = false;

                if(!empty($data['EbrosurRequest']['cronjob_period_id']) && $data['EbrosurRequest']['cronjob_period_id'] == 1){
                    $condition = $this->_callSetConditionFromRequest($data);
                    
                    if(!empty($condition)){
                        $condition['UserCompanyEbrochure.status'] = 1;

                        $this->UserCompanyEbrochure = ClassRegistry::init('UserCompanyEbrochure');

                        $UserCompanyEbrochure = $this->UserCompanyEbrochure->getEbrosurRequest('all', $condition);

                        if(!empty($UserCompanyEbrochure)){
                            $send_directly = true;
                        }else{
                            $result = array(
                                'msg' => __('Gagal membuat permintaan eBrosur dikarenakan eBrosur yang di minta tidak ditemukan. Silahkan atur ulang permintaan Anda atau atur waktu pengiriman eBrosur Anda'),
                                'status' => 'error',
                                'redirect' => array(
                                    'controller' => 'ebrosurs',
                                    'action' => 'add',
                                    'client' => true,
                                ),
                            );
                        }
                    }
                }
                
                $ebrosur_request_id = false;
                if($result['status'] == 'success'){
                    $flagSave = $this->save();
                    
                    if($flagSave){
                        $ebrosur_request_id = $this->id;

                        $result['redirect'][] = $ebrosur_request_id;
                        
                        $this->EbrosurAgentRequest->doSave($flagSave, $ebrosur_request_id);
                        $this->EbrosurTypeRequest->doSave($flagSave, $ebrosur_request_id);
                    }

                    if(!empty($ebrosur_request_id)){
                        $msg = sprintf(__('Berhasil meelakukan permintaan eBrosur dengan ID #%s'), $ebrosur_request_id);
                        $result['Log'] = array(
                            'activity' => $msg,
                            'old_data' => $flagSave,
                            'document_id' => $ebrosur_request_id,
                        );
                    }
                }
                
                if(!empty($send_directly) && !empty($flagSave)){

                    $this->set_last_send($ebrosur_request_id);

                    $User = Configure::read('User.data');
                    $result['msg'] = __('Berhasil mengirimkan permintaan eBrosur Anda.');

                    $data_support = $this->getData('first', array(
                        'conditions' => array(
                            'EbrosurRequest.id' => $ebrosur_request_id,
                        )
                    ));

                    if(!empty($data_support)){
                        $data_support = $this->EbrosurAgentRequest->getMerge($data_support);
                        $data_support = $this->EbrosurTypeRequest->getMerge($data_support);

                        $data_support = $this->getMergeDefault($data_support);
                    }

                    $data_support['agent_id'] = $agent_id_collect;

                    $count_ebrosur = $this->UserCompanyEbrochure->getEbrosurRequest('count', $condition);
                    
                    $result['SendEmail'] = array(
                        'to_name' => $User['full_name'],
                        'to_email' => $User['email'],
                        'subject' => __('Daftar Permintaan eBrosur'),
                        'template' => 'ebrosur_request',
                        'data' => array(
                            'data_support' => $data_support,
                            'data_ebrosur' => $UserCompanyEbrochure,
                            'data_count' => $count_ebrosur,
                            'from' => $company_email,
                        ),
                    );
                }
            }
        } else {
            $result = array(
                'msg' => __('Gagal menyimpan agen permintaan eBrosur Anda, mohon pilih agen yang Anda inginkan.'),
                'status' => 'error',
            );
        }

        return $result;
    }

    function doSaveClient($data, $validate = false, $id = false ){
        $company_id = Configure::read('Principle.id');
        $agent_id = !empty($data['EbrosurRequest']['user_id'])?$data['EbrosurRequest']['user_id']:false;

        $agent_data = $this->User->getMerge(array(), $agent_id);
        $agent_email = !empty($agent_data['User']['email'])?$agent_data['User']['email']:false;

        if( empty($validate) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
                $data['EbrosurRequest']['company_id'] = $company_id;
            }
        }

        if(!empty($data['UserCompanyEbrochure']['mls_id'])){
            $data['UserCompanyEbrochure']['mls_id'] = trim($data['UserCompanyEbrochure']['mls_id']);
        }

        if(!empty($data['EbrosurClientRequest']['client_all']) && !empty($agent_id)){
           $agents = $this->User->UserClientRelation->getData('list', array(
                'conditions' => array(
                    'UserClientRelation.agent_id' => $agent_id,
                    'UserClient.company_id' => $company_id
                ),
                'contain' => array(
                    'UserClient'
                ),
                'fields' => array(
                    'UserClientRelation.user_id', 'UserClientRelation.user_id'
                )
            ));
           
           if(!empty($agents)){
                $data['EbrosurClientRequest']['client_id'] = $agents;
           }
        }

        $this->set($data);

        if( $this->validates() ) {
            $flagSave = true;

            $result = array(
                'msg' => __('Berhasil menyimpan permintaan eBrosur Anda, eBrosur akan dikirimkan sesuai dengan waktu pengiriman yang Anda telah atur sebelumnya'),
                'status' => 'success',
                'redirect' => array(
                    'controller' => 'ebrosurs',
                    'action' => 'request_success',
                    'admin' => true,
                ),
            );

            if(empty($validate)){
                $send_directly = false;

                if(!empty($data['EbrosurRequest']['cronjob_period_id']) && $data['EbrosurRequest']['cronjob_period_id'] == 1){
                    $condition = $this->_callSetConditionFromRequest($data);
                    
                    if(!empty($condition)){
                        $condition['UserCompanyEbrochure.status'] = 1;

                        $this->UserCompanyEbrochure = ClassRegistry::init('UserCompanyEbrochure');

                        $UserCompanyEbrochure = $this->UserCompanyEbrochure->getEbrosurRequest('all', $condition, array(
                            'company' => true,
                            'mine' => true
                        ));

                        if(!empty($UserCompanyEbrochure)){
                            $send_directly = true;
                        }else{
                            $result = array(
                                'msg' => __('Gagal membuat permintaan eBrosur dikarenakan eBrosur yang di minta tidak ditemukan. Silahkan atur ulang permintaan Anda atau atur waktu pengiriman eBrosur Anda'),
                                'status' => 'error',
                                'redirect' => array(
                                    'controller' => 'ebrosurs',
                                    'action' => 'request_add',
                                    'admin' => true,
                                ),
                            );
                        }
                    }
                }

                $ebrosur_request_id = false;
                if($result['status'] == 'success'){
                    $flagSave = $this->save();
                    
                    if($flagSave){
                        $ebrosur_request_id = $this->id;

                        $result['redirect'][] = $ebrosur_request_id;
                        
                        $this->EbrosurClientRequest->doSave($flagSave, $ebrosur_request_id);
                        $this->EbrosurTypeRequest->doSave($flagSave, $ebrosur_request_id);
                    }

                    if(!empty($ebrosur_request_id)){
                        $msg = sprintf(__('Berhasil meelakukan permintaan eBrosur dengan ID #%s'), $ebrosur_request_id);
                        $result['Log'] = array(
                            'activity' => $msg,
                            'old_data' => $flagSave,
                            'document_id' => $ebrosur_request_id,
                        );
                    }
                }
                
                if(!empty($send_directly) && !empty($flagSave)){

                    $this->set_last_send($ebrosur_request_id);

                    $User = Configure::read('User.data');
                    $result['msg'] = __('Berhasil mengirimkan permintaan eBrosur Anda.');

                    $data_support = $this->getData('first', array(
                        'conditions' => array(
                            'EbrosurRequest.id' => $flagSave['EbrosurRequest']['id'],
                        )
                    ));

                    if(!empty($data_support)){
                        $data_support = $this->EbrosurClientRequest->getMerge($data_support);
                        $data_support = $this->EbrosurTypeRequest->getMerge($data_support);

                        $data_support = $this->getMergeDefault($data_support);
                    }

                    $data_support['agent_id'] = $agent_id;

                    if(!empty($data['EbrosurClientRequest']['client_id'])){
                        $this->User = ClassRegistry::init('User');

                        $clients = $this->User->getData('all', array(
                            'conditions' => array(
                                'User.id' => array_keys($data['EbrosurClientRequest']['client_id'])
                            )
                        ));

                        $count_ebrosur = $this->UserCompanyEbrochure->getEbrosurRequest('count', $condition, array(
                            'company' => true,
                            'mine' => true
                        ));

                        if(!empty($clients)){
                            foreach ($clients as $key => $client) {
                                $result['SendEmail'][] = array(
                                    'to_name' => $client['User']['full_name'],
                                    'to_email' => $client['User']['email'],
                                    'subject' => __('Daftar Permintaan eBrosur'),
                                    'template' => 'ebrosur_request',
                                    'data' => array(
                                        'data_support' => $data_support,
                                        'data_ebrosur' => $UserCompanyEbrochure,
                                        'data_count' => $count_ebrosur,
                                        'from' => $agent_email,
                                    ),
                                );
                            }
                        }
                    }
                }
            }
        } else {
            $result = array(
                'msg' => __('Gagal menyimpan agen permintaan eBrosur Anda, mohon pilih agen yang Anda inginkan.'),
                'status' => 'error',
                ''
            );
        }
        
        return $result;
    }

    function _callSetConditionFromRequest($data){
        $result = array();

        if(!empty($data)){

            unset($data['count_user']);
            unset($data['EbrosurRequest']['company_id']);
            unset($data['EbrosurRequest']['cronjob_period_id']);
            unset($data['EbrosurRequest']['session_id']);
            unset($data['EbrosurRequest']['current_region_id']);
            unset($data['EbrosurRequest']['current_city_id']);
            unset($data['EbrosurRequest']['id']);
            unset($data['EbrosurRequest']['created']);
            unset($data['EbrosurRequest']['modified']);
            unset($data['EbrosurRequest']['last_send']);
            unset($data['EbrosurRequest']['created_by']);
            unset($data['EbrosurRequest']['lot_size']);
            unset($data['EbrosurRequest']['building_size']);
            unset($data['EbrosurClientRequest']);

            if(!empty($data['EbrosurAgentRequest'])){
                unset($data['EbrosurRequest']['user_id']);
            }

            if(!empty($data['EbrosurAgentRequest']['agent_all'])){
                unset($data['EbrosurAgentRequest']['agent_all']);
            }

            $asset_field = array(
                'beds', 
                'baths', 
                'min_lot_size', 
                'max_lot_size', 
                'min_building_size', 
                'max_building_size', 
                'furnished', 
                'property_direction_id'
            );

            $property_field = array(
                'certificate_id'
            );

            foreach ($data as $model => $model_value) {
                foreach ($model_value as $field => $value) {
                    $model = 'UserCompanyEbrochure';
                    $arr_condition = 'ebrosur_condition';

                    if(in_array($field, $asset_field)){
                        $model = 'PropertyAsset';
                        $arr_condition = 'property_asset_condition';
                    }

                    if(in_array($field, $property_field)){
                        $model = 'Property';
                        $arr_condition = 'property_asset_condition';
                    }

                    if($field == 'agent_id'){
                        $field = 'user_id';
                    }

                    if(!empty($value) && is_array($value)){
                        $temp_arr = array();
                        foreach ($value as $key => $val) {
                            if(!empty($val)){
                                $temp_arr[] = $key;
                            }
                        }

                        $value = $temp_arr;
                    }

                    if($field == 'mls_id' && !empty($value)){
                        $value = explode(',', trim($value));
                    }

                    if(in_array($field, array('min_price', 'max_price'))){
                        if(!empty($data['EbrosurRequest']['min_price']) && !empty($data['EbrosurRequest']['max_price'])){
                            if($field == 'min_price' && !empty($value)){
                                $field = 'property_price >=';
                            }

                            if($field == 'max_price' && !empty($value)){
                                $field = 'property_price <=';
                            }
                        }else{
                            if($field == 'min_price' && !empty($value)){
                                $field = 'property_price >';
                            }

                            if($field == 'max_price' && !empty($value)){
                                $field = 'property_price <';
                            }
                        }
                    }

                    if(in_array($field, array('beds', 'baths')) && !empty($value)){
                        $field .= ' >=';
                    }

                    if(in_array($field, array('min_lot_size', 'max_lot_size'))){
                        if(!empty($data['EbrosurRequest']['min_lot_size']) && !empty($data['EbrosurRequest']['max_lot_size'])){
                            if($field == 'min_lot_size' && !empty($value)){
                                $field = 'lot_size >=';
                            }

                            if($field == 'max_lot_size' && !empty($value)){
                                $field = 'lot_size <=';
                            }
                        }else{
                            if($field == 'min_lot_size' && !empty($value)){
                                $field = 'lot_size >';
                            }

                            if($field == 'max_lot_size' && !empty($value)){
                                $field = 'lot_size <';
                            }
                        }
                    }

                    if(in_array($field, array('min_building_size', 'max_building_size'))){
                        if(!empty($data['EbrosurRequest']['min_building_size']) && !empty($data['EbrosurRequest']['max_building_size'])){
                            if($field == 'min_building_size' && !empty($value)){
                                $field = 'building_size >=';
                            }

                            if($field == 'max_building_size' && !empty($value)){
                                $field = 'building_size <=';
                            }
                        }else{
                            if($field == 'min_building_size' && !empty($value)){
                                $field = 'building_size >';
                            }

                            if($field == 'max_building_size' && !empty($value)){
                                $field = 'building_size <';
                            }
                        }

                    }

                    if(!in_array($field, array('sorter', 'client_all', 'client_id')) && !empty($value)){
                        $result[$arr_condition][$model.'.'.$field] = $value;
                    }else if($field == 'sorter' && !empty($value)){
                        $explode = explode('-', $value);
                        $result['order'][$model.'.'.$explode[0]] = strtoupper($explode[1]);
                    }
                }
            }
        }
        
        return $result;
    }

    function getMergeDefault( $data, $empty = false ) {
        $certificate_id = !empty($data['EbrosurRequest']['certificate_id'])?$data['EbrosurRequest']['certificate_id']:false;
        $property_direction_id = !empty($data['EbrosurRequest']['property_direction_id'])?$data['EbrosurRequest']['property_direction_id']:false;
        $action_id = !empty($data['EbrosurRequest']['property_action_id'])?$data['EbrosurRequest']['property_action_id']:false;
        $region_id = !empty($data['EbrosurRequest']['region_id'])?$data['EbrosurRequest']['region_id']:false;
        $city_id = !empty($data['EbrosurRequest']['city_id'])?$data['EbrosurRequest']['city_id']:false;

        if( !empty($action_id) && empty($data['PropertyAction']) ) {
            $action = $this->PropertyAction->getData('first', array(
                'conditions' => array(
                    'PropertyAction.id' => $action_id,
                ),
                'cache' => __('PropertyAction.%s', $action_id),
            ));

            if( !empty($action) ) {
                $data = array_merge($data, $action);
            }
        }

        if( !empty($empty) && empty($data['PropertyAction']) ) {
            $data['PropertyAction'] = array();
        }

        if( !empty($certificate_id) && empty($data['Certificate']) ) {
            $certificate = $this->Certificate->getData('first', array(
                'conditions' => array(
                    'Certificate.id' => $certificate_id,
                ),
                'cache' => __('Certificate.%s', $certificate_id),
            ));

            if( !empty($certificate) ) {
                $data = array_merge($data, $certificate);
            }
        }

        if( !empty($empty) && empty($data['Certificate']) ) {
            $data['Certificate'] = array();
        }

        if( !empty($property_direction_id) && empty($data['PropertyDirection']) ) {
            $PropertyDirection = $this->PropertyDirection->getData('first', array(
                'conditions' => array(
                    'PropertyDirection.id' => $property_direction_id,
                ),
            ));

            if( !empty($PropertyDirection) ) {
                $data = array_merge($data, $PropertyDirection);
            }
        }

        if( !empty($empty) && empty($data['PropertyDirection']) ) {
            $data['PropertyDirection'] = array();
        }

        if( !empty($region_id) && empty($data['Region']) ) {
            $Region = $this->Region->getData('first', array(
                'conditions' => array(
                    'Region.id' => $region_id,
                ),
                'cache' => __('Region.%s', $region_id),
            ));

            if( !empty($Region) ) {
                $data = array_merge($data, $Region);
            }
        }

        if( !empty($empty) && empty($data['Region']) ) {
            $data['Region'] = array();
        }

        if( !empty($city_id) && empty($data['City']) ) {
            $City = $this->City->getData('first', array(
                'conditions' => array(
                    'City.id' => $city_id,
                ),
                'cache' => __('City.%s', $city_id),
            ));

            if( !empty($City) ) {
                $data = array_merge($data, $City);
            }
        }

        if( !empty($empty) && empty($data['City']) ) {
            $data['City'] = array();
        }

        return $data;
    }

    function doDelete( $id ) {
        
        $result = false;
        $ebrosurs = $this->getData('all', array(
            'conditions' => array(
                'EbrosurRequest.id' => $id,
            ),
        ));

        if ( !empty($ebrosurs) ) {
            $default_msg = __('menghapus permintaan eBrosur');

            $flag = $this->updateAll(array(
                'EbrosurRequest.status' => 0,
                'EbrosurRequest.modified' => "'".date('Y-m-d H:i:s')."'",
            ), array(
                'EbrosurRequest.id' => $id,
            ));

            if( $flag ) {
                $msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $ebrosurs,
                    ),
                );
            } else {
                $msg = sprintf(__('Gagal %s'), $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $ebrosurs,
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result = array(
                'msg' => __('Gagal menghapus permintaan eBrosur. Data tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));
        
        $default_options['conditions'] = !empty($default_options['conditions']) ? $default_options['conditions'] : array();

        if(!empty($sort) && !empty($direction)){
            $default_options['order'] = array(
                $sort => $direction
            );
        }

        return $default_options;
    }

    function change_period($ebrosur_request_id, $cronjob_period_id){
        return $this->updateAll(array(
            'EbrosurRequest.cronjob_period_id' => $cronjob_period_id,
        ), array(
            'EbrosurRequest.id' => $ebrosur_request_id,
        ));
    }

    function set_last_send($id = false){
        if(!empty($id)){
            $nowDate = date('Y-m-d h:i:s');

            return $this->updateAll(array(
                'EbrosurRequest.last_send' => "'".$nowDate."'",
            ), array(
                'EbrosurRequest.id' => $id,
            ));
        }else{
            return false;
        }
    }

    function doSaveAll($data){
        $this->create();

        $client_all = $this->filterEmptyField($data, 'EbrosurClientRequest', 'client_all');

        if(isset($data['EbrosurClientRequest']['client_all'])){
            unset($data['EbrosurClientRequest']['client_all']);
        }

        if($this->saveAll($data, array('validate' => 'only', 'deep' => true))){
            $data['EbrosurClientRequest']['client_all'] = $client_all;
            
            $result = $this->doSaveClient($data, false, false);
        }else{
            $result = array(
                'msg' => __('Gagal menyimpan agen permintaan eBrosur Anda, mohon pilih agen yang Anda inginkan.'),
                'status' => 'error',
                'validationErrors' => $this->validationErrors
            );
        }

        return $result;
    }

    function _setFieldMinMax($data, $field){
        $min_text = 'min_'.$field;
        $max_text = 'max_'.$field;

        if(!empty($data['EbrosurRequest'][$min_text]) && !empty($data['EbrosurRequest'][$max_text])){
            $data['EbrosurRequest'][$field] = sprintf('%s-%s', $data['EbrosurRequest'][$min_text], $data['EbrosurRequest'][$max_text]);
        }else if(!empty($data['EbrosurRequest'][$min_text])){
            $data['EbrosurRequest'][$field] = '>'.$data['EbrosurRequest'][$min_text];
        }else if(!empty($data['EbrosurRequest'][$max_text])){
            $data['EbrosurRequest'][$field] = '<'.$data['EbrosurRequest'][$max_text];
        }

        return $data;
    }
}
?>