<?php
class CoBrokeUser extends AppModel {
    var $name = 'CoBrokeUser';
    var $displayField = 'name';

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id',
        ),
        'CoBrokeProperty' => array(
            'className' => 'CoBrokeProperty',
            'foreignKey' => 'co_broke_property_id',
            'counterCache' => array(
                'co_broke_user_approve_count' => array(
                    'CoBrokeUser.approved' => 1,
                ),
                'co_broke_user_count' => array(
                    'CoBrokeUser.status' => 1,
                )
            )
        ),
    );

    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Silakan msukkan nama Anda'
            ),
        ),
        'address' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Silakan masukkan alamat diri Anda'
            ),
        ),
        'phone' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Silakan masukkan no telepon Anda'
            ),
            'notMatch' => array(
                'rule' => array('validatePhoneNumber'),
                'allowEmpty'=> true,
                'message' => 'Format No. Telepon e.g. +6281234567 or 0812345678'
            ),
            'minLength' => array(
                'rule' => array('minLength', 6),
                'allowEmpty'=> true,
                'message' => 'Minimal 6 digit',
            ),
            'maxLength' => array(
                'rule' => array('maxLength', 20),
                'allowEmpty'=> true,
                'message' => 'Maksimal 20 digit',
            ),
        ),
        'request_commission' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Masukkan pengajuan komisi Co-Broke'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Pengajuan komisi Co-Broke harus berupa angka'
            ),
            'comparison' => array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Persentase komisi Co-Broke harus lebih dari 0'
            ),
            'validateRequestCommission' => array(
                'rule' => array('validateRequestCommission', 'request_commission', 'type_price_commission'),
                'message' => 'Ada kesalahan terhadap nilai yang diinput'
            )
        ),
        'revision_commission' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Masukkan pengajuan perubahan komisi Co-Broke'
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Revisi pengajuan komisi Co-Broke harus berupa angka'
            ),
            'comparison' => array(
                'rule' => array('comparison', '>', 0),
                'message' => 'Persentase perubahan komisi Co-Broke harus lebih dari 0'
            ),
            'validateRequestCommission' => array(
                'rule' => array('validateRequestCommission', 'revision_commission', 'revision_type_price_commission'),
                'message' => 'Ada kesalahan terhadap nilai yang diinput'
            )
        ),
        'type_commission' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Masukkan tipe asal komisi'
            ),
            'multiple' => array(
                'rule' => array('multiple', array(
                    'in'  => array('in_corp', 'out_corp'),
                )),
                'message' => 'Silakan pilih tipe asal komisi apakah dari "Penjualan Properti" atau "Komisi Agen" '
            )
        ),
        'revision_type_commission' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Masukkan revisi tipe asal komisi'
            ),
            'multiple' => array(
                'rule' => array('multiple', array(
                    'in'  => array('in_corp', 'out_corp'),
                )),
                'message' => 'Silakan pilih revisi tipe asal komisi apakah dari "Penjualan Properti" atau "Komisi Agen" '
            )
        ),
        'agent_email' => array(
            'validateRequester' => array(
                'rule' => array('validateRequester'),
                'message' => 'Masukkan email agen'
            )
        )
    );

    function validateRequestCommission($data, $commission_field = 'request_commission', $type_field = 'type_price_commission'){
        $result = true;

        $global_data = $this->data;

        $type_price_commission  = Common::hashEmptyField($global_data, 'CoBrokeUser.'.$type_field);
        $request_commission     = Common::hashEmptyField($data, $commission_field);

        if(!empty($request_commission)){
            if($request_commission > 0){
                if($type_price_commission == 'percentage' && $request_commission > 100){
                    $result = false;

                    $this->validator()
                    ->getField($commission_field)
                    ->getRule('validateRequestCommission')
                    ->message = __('Tidak boleh lebih dari 100%');
                }
            }else{
                $result = false;
            }
        }else{
            $result = false;
        }

        return $result;
    }

    function validateRequester($data){
        $result = true;

        $global_data = $this->data;

        $co_broke_property_id = Common::hashEmptyField($global_data, 'CoBrokeUser.co_broke_property_id');
        $data = Common::hashEmptyField($data, 'agent_email');
        $User = Configure::read('User.data');
        $is_admin = Configure::read('User.admin');

        if(!empty($is_admin)){
            if(empty($data)){
                $result = false;
            }else{
                $data_agent = $this->User->getData('first', array(
                    'conditions' => array(
                        'User.email' => $data
                    )
                ), array(
                    'company' => true,
                    'role' => 'agent'
                ));

                if(empty($data_agent)){
                    $result = false;
                }else{
                    $agent_id = Common::hashEmptyField($data_agent, 'User.id');

                    $agent_id = $this->getData('first', array(
                        'conditions' => array(
                            'CoBrokeUser.user_id' => $agent_id,
                            'CoBrokeUser.co_broke_property_id' => $co_broke_property_id,
                            'CoBrokeUser.status' => 1,
                        )
                    ), array(
                        'status' => 'all'
                    ));

                    if(!empty($agent_id)){
                        $result = false;

                        $this->validator()
                        ->getField('agent_email')
                        ->getRule('validateRequester')
                        ->message = __('Agen ini sudah pernah melakukan permintaan co broke sebelumnya');
                    }
                }
            }
        }

        return $result;
    }

    function ValidateBtCommission($data){
        $global_data = $this->data;

        $result = true;

        if( !empty($global_data['CoBrokeUser']['with_bt']) && empty($data['bt_commission']) ){
            $result = false;
        }

        return $result;
    }

/**
    *   @param array $data - data array "phone", "no_hp", "no_hp_2"
    *   @return boolean true or false
    */
    function validatePhoneNumber($data) {
        $phoneNumber = false;
        if( !empty($data['phone']) ) {
            $phoneNumber = $data['phone'];
        } else if( !empty($data['no_hp']) ) {
            $phoneNumber = $data['no_hp'];
        } else if( !empty($data['no_hp_2']) ) {
            $phoneNumber = $data['no_hp_2'];
        }

        if(!empty($phoneNumber)) {
            if (preg_match('/^[0-9]{1,}$/', $phoneNumber)==1 
                || ( substr($phoneNumber, 0,1)=="+" 
                && preg_match('/^[0-9]{1,}$/', substr($phoneNumber, 1,strlen($phoneNumber)))==1 )) {
                return true;
            }
        }
        return false;
    }

    /**
    *   @param string $find - all, list, paginate
    *       string all - Pick semua field berupa array
    *       string list - Pick semua field berupa key dan value array
    *       string paginate - Pick opsi query
    *   @param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
    *   @param boolean $is_merge - True merge default opsi dengan opsi yang diparsing, False gunakan hanya opsi yang diparsing
    */
    function getData($find = 'all', $options = array(), $elements = array()) {
        $status = isset($elements['status']) ? $elements['status'] : 'approve';
        $mine = isset($elements['mine']) ? $elements['mine'] : false;
        $owner_property = isset($elements['owner_property']) ? $elements['owner_property'] : false;
        $admin_reverse = isset($elements['admin_reverse']) ? $elements['admin_reverse'] : false;

        $user_login_id = Configure::read('User.id');
        $is_admin = Configure::read('User.admin');

        if($admin_reverse){
            $is_admin = false;
        }

        $default_options = array(
            'conditions'=> array(),
            'order' => array(),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['CoBrokeUser.status'] = 1;
                break;
            case 'decline':
                $default_options['conditions'] = array(
                    'CoBrokeUser.declined' => 1
                );
                break;
            case 'pending':
                $default_options['conditions'] = array(
                    'CoBrokeUser.status' => 1,
                    'CoBrokeUser.approved' => 0,
                    'CoBrokeUser.declined' => 0,
                );
                break;
            case 'approve':
                $default_options['conditions'] = array(
                    'CoBrokeUser.status' => 1,
                    'CoBrokeUser.approved' => 1,
                    'CoBrokeUser.declined' => 0,
                );
                break;
        }

        if(!empty($mine)){
            $default_options['conditions']['CoBrokeUser.user_id'] = $user_login_id;
        }

        if( !empty($is_admin) ) {
            $parent_id = Configure::read('Principle.id');
            $agent_id = $this->User->getAgents( $parent_id, true );

            array_push($agent_id, $parent_id);

            $default_options['conditions']['CoBrokeUser.user_id'] = $agent_id;
        }

        if( !empty($options)){
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
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }

        return $result;
    }

    function doSave($data, $value = array(), $id = false){
        $result = array();

        if(!empty($data)){
            if(!empty($id)){
                $this->id = $id;

                $data['CoBrokeUser']['id'] = $id;

                $text_message = 'edit permintaan';
            }else{
                $this->create();

                $text_message = 'permintaan';
            }

            $is_change_address = $this->filterEmptyField($data, 'CoBrokeUser', 'is_change_address');

            $address_validate = true;
            if($is_change_address){
                $this->User->UserProfile->set($data);
                $address_validate = $this->User->UserProfile->validates($data);

                $this->validator()->remove('address');
            }

            $User       = Configure::read('User.data');
            $group_id   = Configure::read('User.data.group_id');
            $is_admin   = Configure::read('User.admin');
            
            $user_id_login = $this->filterEmptyField($User, 'id');

            $agent_email = Common::hashEmptyField($data, 'CoBrokeUser.user_id');
            $prepare_email_agent = array();
            $agent_parent_id = false;
            $agent_id = false;

            if(empty($is_admin)){
                if(empty($id)){
                    $data['CoBrokeUser']['user_id'] = $user_id_login;
                }
            }else{
                $agent_id = Common::hashEmptyField($data, 'CoBrokeUser.user_id');

                $agent_data = $this->User->findById($agent_id);

                if(!empty($agent_data)){
                    $agent_full_name    = Common::hashEmptyField($agent_data, 'User.full_name');
                    $agent_email        = Common::hashEmptyField($agent_data, 'User.email');
                    $agent_parent_id    = Common::hashEmptyField($agent_data, 'User.parent_id');

                    $prepare_email_agent = array(
                        'to_name' => $agent_full_name,
                        'to_email' => $agent_email,
                        'subject' => __('Assign Co Broke'),
                    );
                }
            }
            
            $this->set($data);
            
            if($this->validates() && $address_validate){
                if($this->save($data)){
                    $co_broke_property_id = Common::hashEmptyField($data, 'CoBrokeUser.co_broke_property_id');

                    if(empty($id)){
                        $msg = __('Berhasil melakukan permintaan Co Broke');
                    }else{
                        $msg = __('Berhasil melakukan edit aplikasi Co Broke');
                    }

                    $recordID = $this->id;
                    $result = array(
                        'id' => $recordID, 
                        'status' => 'success',
                        'msg' => $msg,
                        'Log' => array(
                            'document_id' => $recordID, 
                            'activity' => $msg,
                        )
                    );

                    if(empty($id)){
                        if(!empty($data['UserProfile'])){
                            $User = Configure::read('User.data');
                            $user_profile_id = Common::hashEmptyField($User, 'UserProfile.id');

                            $this->User->UserProfile->doSave($data, $user_id_login, $user_profile_id);
                        }

                        $value = $this->CoBrokeProperty->getData('first', array(
                            'conditions' => array(
                                'CoBrokeProperty.id' => $co_broke_property_id
                            )
                        ));

                        $value = $this->CoBrokeProperty->Property->getDataList($value, array(
                            'contain' => array(
                                'MergeDefault',
                                'PropertyAddress',
                                'PropertyAsset',
                                'User'
                            ),
                        ));

                        $value = $this->User->getDataList($value, array(
                            'is_full' => false,
                            'Parent'
                        ));

                        $data = array_merge($data, $value);
                        
                        $mls_id     = Common::hashEmptyField($data, 'Property.mls_id');
                        $title      = Common::hashEmptyField($data, 'Property.title');
                        $user_id    = Common::hashEmptyField($data, 'Property.user_id');
                        $parent_id  = Common::hashEmptyField($data, 'User.parent_id');

                        $full_name  = Common::hashEmptyField($data, 'User.full_name');
                        $email      = Common::hashEmptyField($data, 'User.email');
                        
                        $code_cobroke = Common::hashEmptyField($data, 'CoBrokeProperty.code');

                        $name       = Common::hashEmptyField($data, 'CoBrokeUser.name');

                        $textNotification = sprintf('"%s - #%s"', $title, $mls_id);

                        $result = array_merge($result, array(
                            'Log' => array(
                                'document_id' => $this->id,
                                'activity' => sprintf(__('user %s melakukan pengajuan Co Broke'), $user_id_login),
                            )
                        ));

                        $result['Notification'][] = array(
                            'user_id' => $user_id,
                            'name' => sprintf(__('Anda mendapatkan permintaan pengajuan Co Broke #%s untuk properti %s dari %s'), $code_cobroke, $textNotification, $name),
                            'link' => array(
                                'controller' => 'co_brokes',
                                'action' => 'brokers',
                                $co_broke_property_id,
                                'admin' => true
                            ),
                            'include_role' => array(
                                'role' => array('admin', 'principle'),
                                'from_parent_id' => $parent_id
                            )
                        );

                        $email = array(
                            'to_name' => $full_name,
                            'to_email' => $email,
                            'subject' => __('Permintaan Co Broke'),
                            'template' => 'co_broke_request',
                            'data' => $data,
                            'include_role' => array(
                                'role' => array('admin', 'principle'),
                                'from_parent_id' => $parent_id
                            )
                        );

                        if(!empty($is_admin)){
                            $role = array();
                            switch ($group_id) {
                                case 3:
                                    $role = array('admin');
                                break;
                                case 5:
                                    $role = array('principle');
                                break;
                                default:
                                    $role = array('admin', 'principle');
                                break;
                            }

                            if(!empty($prepare_email_agent)){
                                $send_email_agent = array_merge($prepare_email_agent, array(
                                    'template' => 'assign_co_broke',
                                    'data' => array_merge($data, array(
                                        'is_assign' => true
                                    ))
                                ));

                                if(!empty($role)){
                                    $send_email_agent['include_role'] = array(
                                        'role' => $role,
                                        'from_parent_id' => $agent_parent_id
                                    );
                                }

                                $result['SendEmail'][] = $send_email_agent;
                            }
                            
                             if(!empty($agent_id)){
                                $result['Notification'][] = array(
                                    'user_id' => $agent_id,
                                    'name' => sprintf(__('Penunjukan kerja sama pengajuan Co Broke #%s untuk properti %s'), $code_cobroke, $textNotification),
                                    'link' => array(
                                        'controller' => 'co_brokes',
                                        'action' => 'listing',
                                        'admin' => true
                                    ),
                                );
                            }
                        }

                        $result['SendEmail'][] = $email;
                    }
                }else{
                    $msg = sprintf(__('Gagal melakukan %s Co Broke'), $text_message);

                    $result = array(
                        'status' => 'error',
                        'msg' => $msg,
                        'Log' => array(
                            'activity' => $msg,
                            'data' => $data,
                            'document_id' => $id,
                            'error' => 1
                        ),
                    );
                }
            }else{
                $msg = sprintf(__('Gagal melakukan %s Co Broke'), $text_message);

                $result = array(
                    'status' => 'error',
                    'msg' => $msg,
                    'validateErrors' => $this->validationErrors,
                    'Log' => array(
                        'activity' => $msg,
                        'data' => $data,
                        'document_id' => $id,
                        'error' => 1
                    ),
                );
            }
        }else if(!empty($value)){
            $result['data'] = $value;
        }

        return $result;
    }

    function getMerge( $data, $id = false, $mine = false, $element = array(), $fieldName = 'CoBrokeUser.co_broke_property_id' ) {
        if( empty($data['CoBrokeUser']) ) {

            $conditions[$fieldName] = $id;

            $find = 'all';
            if($mine){
                $find = 'first';

                $user_id = Configure::read('User.id');
                $conditions['CoBrokeUser.user_id'] = $user_id;
            }

            $value = $this->getData($find, array(
                'conditions' => $conditions,
            ), $element);

            if( !empty($value) && !$mine) {
                $data['CoBrokeUser'] = $value;
            }else if( !empty($value) && $mine) {
                $data = array_merge($data, $value);
            }
        }

        return $data;
    }

    function doApprove($id){
        $cobroke_data = $broker_data = $this->completeDataCoBrokeUser($id);

        $owner_data		= Common::hashEmptyField($broker_data, 'owner_data', array());
        $broker_data	= Common::hashEmptyField($broker_data, 'broker_data', array());

        if(!empty($broker_data)){
            $code = Common::hashEmptyField($broker_data, 'CoBrokeProperty.code');
            $name = Common::hashEmptyField($broker_data, 'CoBrokeUser.name');

            $request_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.request_commission', 0);
            $final_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_commission', $request_commission);

            $request_type_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.type_commission', 0);
            $final_type_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_type_commission', $request_type_commission);

            $type_price_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.type_price_commission', 0);
            $final_type_price_commission = Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_type_price_commission', $type_price_commission);

			$user_id = Common::hashEmptyField($broker_data, 'Property.user_id');
			$user_id_login = Configure::read('User.id');
			$user_id_co_broke = Common::hashEmptyField($broker_data, 'CoBrokeUser.user_id');

			$full_name = Common::hashEmptyField($broker_data, 'User.full_name');
			$email = Common::hashEmptyField($broker_data, 'User.email');

			$is_admin = Configure::read('User.admin');
			$is_allow = false;

		//	b:auto approve untuk internal ==================================================================

			$authUserID		= Configure::read('User.id');
            $isAdmin		= Configure::read('User.admin');

            $ownerID		= Common::hashEmptyField($owner_data, 'User.id');
			$ownerParentID	= Common::hashEmptyField($owner_data, 'User.parent_id');

			$brokerID		= Common::hashEmptyField($broker_data, 'User.id');
			$brokerParentID	= Common::hashEmptyField($broker_data, 'User.parent_id');

            $isAllow = false;

            if($isAdmin || ($ownerID == $authUserID)){
            //  admin atau yang login sama dengan pemilik property
                $isAllow = true;
            }
            else if($ownerParentID == $brokerParentID){
            //  test dari settingan auto approve co broke
				$companyData	= Configure::read('Config.Company.data');
				$isCoBroke		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_co_broke');
				$coBrokeType	= Common::hashEmptyField($companyData, 'UserCompanyConfig.default_type_co_broke');
				$isAutoApprove	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_auto_approve_cobroke');
				$isAllow        = $isCoBroke && $coBrokeType == 'in_corp' && $isAutoApprove;
			}

		//	e:auto approve untuk internal ==================================================================

            $data_temp['CoBrokeUser']['final_commission']               = $final_commission;
            $data_temp['CoBrokeUser']['final_type_commission']          = $final_type_commission;
            $data_temp['CoBrokeUser']['final_type_price_commission']    = $final_type_price_commission;

            if($isAllow){
                if($this->updateStatus('approve', $id, $data_temp)){
                    $msg = sprintf(__('Selamat! aplikasi co broke Anda dengan kode %s telah disetujui'), $code);

                    $cobroke_data['status'] = 'approve';
                    $cobroke_data['broker_data']['CoBrokeUser']['final_commission'] = $final_commission;
                    $cobroke_data['broker_data']['CoBrokeUser']['final_type_commission'] = $final_type_commission;
                    $cobroke_data['broker_data']['CoBrokeUser']['final_type_price_commission'] = $final_type_price_commission;

                    $cobroke_data['type_commission'] = Configure::read('Config.Type.CoBroke.Commission');

                    $result = array(
                        'status' => 'success',
                        'msg' => __('Berhasil melakukan persetujuan co broke, harap hubungi agen terkait untuk kepentingan kerjasama selanjutnya.'),
                        'Log' => array(
                            'document_id' => $this->id,
                            'activity' => __('Berhasil melakukan persetujuan Co Broke'),
                        ),
						'Notification' => array(
							array(
								'user_id' => $user_id_co_broke,
								'name' => $msg,
								'link' => array(
									'controller' => 'co_brokes',
									'action' => 'listing',
									'admin' => true
								),
							),
						),
						'SendEmail' => array(
							array(
								'to_name' => $full_name,
								'to_email' => $email,
								'subject' => $msg,
								'template' => 'co_broke_approval',
								'data' => $cobroke_data
							), 
                        ),
                    );
                }else{
                    $msg = __('Gagal melakukan persetujuan co broke, harap hubungi administrator kami.');
                    $result = array(
                        'status' => 'error',
                        'msg' => $msg,
                        'Log' => array(
                            'activity' => $msg,
                            'data' => $cobroke_data,
                            'document_id' => $id,
                            'error' => 1,
                        ),
                    ); 
                }
            }else{
                $msg = __('Anda tidak diijinkan untuk melakukan persetujuan co broke.');
                $result = array(
                    'status' => 'error',
                    'msg' => $msg,
                    'Log' => array(
                        'activity' => $msg,
                        'document_id' => $id,
                        'error' => 1,
                    ),
                );
            }
        }else{
            $msg = __('Gagal menyetujui aplikasi Co Broke');
            $result = array(
                'status' => 'error',
                'msg' => $msg,
                'Log' => array(
                    'activity' => $msg,
                    'document_id' => $id,
                    'error' => 1,
                ),
            );
        }

        return $result;
    }

    function doReject($data, $id){
        $cobroke_data = $cobroke_user = $this->completeDataCoBrokeUser($id);

        $owner_property = Common::hashEmptyField($cobroke_user, 'owner_data');
        $cobroke_user = Common::hashEmptyField($cobroke_user, 'broker_data');

        if(!empty($cobroke_user)){
            if(!empty($data)){
                $this->validator()->add('decline_reason', 'required', array(
                    'rule' => 'notempty',
                    'message' => __('Mohon diisi alasan penolakan aplikasi ini')
                ));

                $this->id = $id;

                $data['CoBrokeUser']['id'] = $id;

                if($this->saveAll($data, array('validate' => 'first')) && $this->updateStatus('decline', $id)){
                    $cobroke_data['status'] = 'disapprove';

                    $code = Common::hashEmptyField($cobroke_user, 'CoBrokeProperty.code');
                    $user_id_co_broke = Common::hashEmptyField($cobroke_user, 'CoBrokeUser.user_id');
                    $full_name = Common::hashEmptyField($cobroke_user, 'User.full_name');
                    $email = Common::hashEmptyField($cobroke_user, 'User.email');

                    $decline_reason = Common::hashEmptyField($data, 'CoBrokeUser.decline_reason');

                    $cobroke_data = array_merge($data, $cobroke_data);
                    
                    $result = array(
                        'status' => 'success',
                        'msg' => __('Berhasil melakukan penolakan pengajuan co broke.'),
                        'Log' => array(
                            'document_id' => $this->id,
                            'activity' => sprintf(__('Berhasil melakukan penolakan Co Broke dengan alasan : %s'), $decline_reason),
                        ),
                        'Notification' => array(
                            'user_id' => $user_id_co_broke,
                            'name' => sprintf(__('Maaf! pengajuan aplikasi Co Broke dengan kode #%s telah ditolak.'), $code),
                            'link' => array(
                                'controller' => 'co_brokes',
                                'action' => 'listing',
                                'admin' => true
                            ),
                        ),
                        'SendEmail' => array(
                            'to_name' => $full_name,
                            'to_email' => $email,
                            'subject' => sprintf(__('Pengajuan aplikasi Co Broke dengan kode #%s telah ditolak.'), $code),
                            'template' => 'co_broke_approval',
                            'data' => $cobroke_data,
                        ),
                    );
                }else{
                    $msg = __('Gagal melakukan penolakan pengajuan co broke, silakan hubungi administrator kami.');
                    $result = array(
                        'status' => 'error',
                        'msg' => $msg,
                        'Log' => array(
                            'activity' => $msg,
                            'data' => $cobroke_data,
                            'document_id' => $id,
                            'error' => 1,
                        ),
                    );
                }
            }else{
                $result = array();
            }
        }else{
            $msg = __('Gagal menolak aplikasi Co Broke');
            $result = array(
                'status' => 'error',
                'msg' => $msg,
                'Log' => array(
                    'activity' => $msg,
                    'document_id' => $id,
                    'error' => 1, 
                ),
            );
        }

        return $result;
    }

    function updateStatus($status, $id, $data = array()){
        switch ($status) {
            case 'active':
                $data['CoBrokeUser']['status'] = 1;
                break;
            case 'decline':
                $data['CoBrokeUser']['declined'] = 1;
                $data['CoBrokeUser']['approved'] = 0;
                break;
            case 'approve':
                $data['CoBrokeUser']['status'] = 1;
                $data['CoBrokeUser']['approved'] = 1;
                $data['CoBrokeUser']['declined'] = 0;
                break;
            case 'normalize':
                $data['CoBrokeUser']['status'] = 1;
                $data['CoBrokeUser']['approved'] = 0;
                $data['CoBrokeUser']['declined'] = 0;
                $data['CoBrokeUser']['revision_commission'] = 0;
                $data['CoBrokeUser']['final_commission'] = 0;
                $data['CoBrokeUser']['revision_type_commission'] = NULL;
                $data['CoBrokeUser']['final_type_commission'] = NULL;
                $data['CoBrokeUser']['final_type_price_commission'] = 'percentage';

                $this->validator()
                ->remove('revision_commission')
                ->remove('final_commission')
                ->remove('revision_type_commission')
                ->remove('final_type_commission')
                ->remove('final_type_price_commission');
                break;
        }

        $this->id = $id;

        $this->set($data);
        
        return $this->save();
    }

    function completeDataCoBrokeUser($id, $status = 'active'){
        $cobroke_user = $this->getData('first', array(
            'conditions' => array(
                'CoBrokeUser.id' => $id
            )
        ), array(
            'status' => $status,
            'admin_reverse' => true
        ));

        $owner_property = array();

        if(!empty($cobroke_user)){
            $cobroke_user = $this->getMergeList($cobroke_user, array(
                'contain' => array(
                    'CoBrokeProperty',
                    'User'
                )
            ));

            $cobroke_user = $this->CoBrokeProperty->Property->getDataList($cobroke_user, array(
                'contain' => array(
                    'MergeDefault',
                    'PropertyAddress',
                    'PropertyAsset',
                ),
            ));

            $cobroke_user = $this->User->getDataList($cobroke_user, array(
                'is_full' => false,
                'Parent'
            ));

            $owner_property_id = $this->filterEmptyField($cobroke_user, 'Property', 'user_id');

            $owner_property = $this->User->getData('first', array(
                'conditions' => array(
                    'User.id' => $owner_property_id
                )
            ), array(
                'status' => 'all',
                'company' => false
            ));

            if(!empty($owner_property)){
                $parent_id = $this->filterEmptyField($owner_property, 'User', 'parent_id');
                $owner_property = $this->User->UserProfile->getMerge($owner_property, $owner_property_id);
                $owner_property = $this->User->UserCompany->getMerge($owner_property, $parent_id);
                $owner_property = $this->User->UserCompany->getMergeList($owner_property, array(
                    'contain' => array(
                        'Region' => array(
                            'cache' => true,
                        ),
                        'City' => array(
                            'cache' => true,
                        ),
                        'Subarea' => array(
                            'cache' => array(
                                'name' => 'Subarea',
                                'config' => 'subareas',
                            ),
                        ),
                    )
                ));

                $owner_property = $this->User->UserCompanyConfig->getMerge($owner_property, $parent_id);
            }
        }
        
        return array(
            'broker_data' => $cobroke_user,
            'owner_data' => $owner_property
        );
    }

    function sendEmailSoldCoBroke($property_id, $sold_by_id = false, $is_cobroke = false){
        $co_broke_property = $this->CoBrokeProperty->getData('first', array(
            'conditions' => array(
                'CoBrokeProperty.property_id' => $property_id
            )
        ));

        $result = array();
        if(!empty($co_broke_property)){
            $co_broke_property_id = $this->filterEmptyField($co_broke_property, 'CoBrokeProperty', 'id');
            $code = $this->filterEmptyField($co_broke_property, 'CoBrokeProperty', 'code');

            $other_conditions = $mine_conditions = array(
                'CoBrokeUser.co_broke_property_id' => $co_broke_property_id
            );

            // if(!empty($sold_by_id) && !empty($is_cobroke)){
            //     $mine_conditions['CoBrokeUser.user_id'] = $sold_by_id;    
            //     $other_conditions['CoBrokeUser.user_id <>'] = $sold_by_id;
            // }

            $cobroke_data = $this->getData('first', array(
                'conditions' => $mine_conditions
            ), array(
                'status' => 'all',
                'admin_reverse' => true
            ));

            $cobroke_data = array_merge($cobroke_data, $co_broke_property);

            $cobroke_data = $this->CoBrokeProperty->Property->getDataList($cobroke_data, array(
                'contain' => array(
                    'MergeDefault',
                    'PropertyAddress',
                    'PropertyAsset',
                    'PropertySold'
                ),
            ));

            if(!empty($cobroke_data['PropertySold'])){
                $period_id = $this->filterEmptyField($cobroke_data, 'PropertySold', 'period_id');
                $currency_id = $this->filterEmptyField($cobroke_data, 'PropertySold', 'currency_id');

                $cobroke_data['PropertySold'] = $this->CoBrokeProperty->Property->Period->getMerge($cobroke_data['PropertySold'], $period_id);
                $cobroke_data['PropertySold'] = $this->CoBrokeProperty->Property->Currency->getMerge($cobroke_data['PropertySold'], $currency_id);
            }

            $cobroke_user = $this->getData('list', array(
                'conditions' => $other_conditions,
                'fields' => array(
                    'CoBrokeUser.user_id', 'CoBrokeUser.user_id'
                )
            ), array(
                'status' => 'all',
                'admin_reverse' => true
            ));

            if(!empty($cobroke_user)){
                $user = $this->User->getData('all', array(
                    'conditions' => array(
                        'User.id' => $cobroke_user
                    ),
                    'fields' => array(
                        'User.id', 'User.email', 'User.full_name'
                    )
                ), array(
                    'status' => 'all'
                ));

                if(!empty($user)){
                    $result = array();
                    foreach ($user as $key => $val) {
                        $id_user = $this->filterEmptyField($val, 'User', 'id');
                        $email = $this->filterEmptyField($val, 'User', 'email');
                        $full_name = $this->filterEmptyField($val, 'User', 'full_name');
                        
                        $result['SendEmail'][] = array(
                            'to_name' => $full_name,
                            'to_email' => $email,
                            'subject' => sprintf(__('Pemberitahuan! Co Broke #%s telah terjual'), $code),
                            'template' => 'co_broke_sold_announce',
                            'data' => $cobroke_data
                        );

                        $result['Notification'][] = array(
                            'Notification' => array(
                                'user_id' => $id_user,
                                'name' => sprintf(__('Pemberitahuan! Co Broke #%s telah terjual'), $code),
                                'link' => array(
                                    'controller' => 'co_brokes',
                                    'action' => 'listing',
                                    'admin' => true
                                ),
                            ),
                        );
                    }
                }
            }
        }

        return $result;
    }

    function customBindModel($options, $params){
        $sort = $this->filterEmptyField($params, 'named', 'sort');
        $direction = $this->filterEmptyField($params, 'named', 'direction', 'ASC');
        $price = $this->filterEmptyField($params, 'named', 'price');

        if(!empty($options['contain'])){
            $bindModel['Property'] = array(
                'className' => 'Property',
                'foreignKey' => false,
                'conditions' => array(
                    'CoBrokeProperty.property_id = Property.id'
                )
            );

            if(in_array('PropertyAddress', $options['contain'])){
                $bindModel['PropertyAddress'] = array(
                    'className' => 'PropertyAddress',
                    'foreignKey' => false,
                    'conditions' => array(
                        'CoBrokeProperty.property_id = PropertyAddress.property_id'
                    )
                );
            }

            if(in_array('PropertyAsset', $options['contain'])){
                $bindModel['PropertyAsset'] = array(
                    'className' => 'PropertyAsset',
                    'foreignKey' => false,
                    'conditions' => array(
                        'CoBrokeProperty.property_id = PropertyAsset.property_id'
                    )
                );

                if(isset($this->CoBrokeProperty->virtualFields['total_baths'])){
                    $this->virtualFields['total_baths'] = 'COALESCE(PropertyAsset.baths, 0) + COALESCE(PropertyAsset.baths_maid, 0)';
                }
                if(isset($this->CoBrokeProperty->virtualFields['total_beds'])){
                    $this->virtualFields['total_beds'] = 'COALESCE(PropertyAsset.beds, 0) + COALESCE(PropertyAsset.beds_maid, 0)';
                }
            }

            if(in_array('PropertyType', $options['contain'])){
                $bindModel['PropertyType'] = array(
                    'className' => 'PropertyType',
                    'foreignKey' => false,
                    'conditions' => array(
                        'Property.property_type_id = PropertyType.id'
                    )
                );
            }

            if(in_array('PropertySold', $options['contain']) || $sort == 'CoBrokeProperty.price_converter' || !empty($price)){
                $bindModel['PropertySold'] = array(
                    'className' => 'PropertySold',
                    'foreignKey' => false,
                    'conditions' => array(
                        'CoBrokeProperty.property_id = PropertySold.property_id'
                    )
                );

                $this->virtualFields['price_converter'] = '
                    CASE WHEN Property.sold = 1 THEN 
                        PropertySold.price_sold 
                    ELSE 
                        CASE 
                        WHEN Property.price_measure > 0 THEN
                            Property.price_measure 
                        ELSE
                            Property.price
                        END
                    END
                ';
            }

            if(isset($this->CoBrokeProperty->virtualFields)){
                unset($this->CoBrokeProperty->virtualFields);
            }

            $this->bindModel(array(
                'hasOne' => $bindModel
            ), false);
        }
    }

    function revisionRequestCommission($data, $id){
        if(!empty($data)){
            $this->id = $id;

            $this->set($data);

            if($this->validates($data)){
                $cobroke_data = $this->completeDataCoBrokeUser($id);

                $cobroke_user = Common::hashEmptyField($cobroke_data, 'broker_data');
                $owner_data = Common::hashEmptyField($cobroke_data, 'owner_data');

                $full_name = Common::hashEmptyField($cobroke_user, 'User.full_name');
                $email = Common::hashEmptyField($cobroke_user, 'User.email');
                $user_id_requester = Common::hashEmptyField($cobroke_user, 'User.id');

                $title_property = Common::hashEmptyField($cobroke_user, 'Property.title');

                $cobroke_data['broker_data']['CoBrokeUser']['revision_commission'] = Common::hashEmptyField($data, 'CoBrokeUser.revision_commission');
                $cobroke_data['broker_data']['CoBrokeUser']['revision_type_commission'] = Common::hashEmptyField($data, 'CoBrokeUser.revision_type_commission');
                $cobroke_data['broker_data']['CoBrokeUser']['revision_type_price_commission'] = Common::hashEmptyField($data, 'CoBrokeUser.revision_type_price_commission');

                if($this->save($data)){
                    $cobroke_data['type_commission'] = Configure::read('Config.Type.CoBroke.Commission');

                    $result = array(
                        'msg' => __('Berhasil melakukan revisi komisi Co-Broke'),
                        'status' => 'status',
                        'Log' => array(
                            'document_id' => $this->id,
                            'activity' => sprintf(__('user ID %s melakukan revisi komisi Co-Broke ID %s'), Configure::read('User.id'), $this->id) ,
                        ),
                        'SendEmail' => array(
                            'to_name' => $full_name,
                            'to_email' => $email,
                            'subject' => __('Revisi Komisi Permintaan Co-Broke'),
                            'template' => 'co_broke_request_revision',
                            'data' => $cobroke_data,
                        ),
                        'Notification' => array(
                            'user_id' => $user_id_requester,
                            'name' => sprintf(__('Pengajuan komisi Co Broke Anda pada properti "%s" telah direvisi, silakan lakukan peninjauan kembali.'), $title_property),
                            'link' => array(
                                'controller' => 'co_brokes',
                                'action' => 'listing',
                                'admin' => true
                            )
                        ),
                    );
                }else{
                    $msg = __('Gagal melakukan revisi komisi Co-Broke');
                    $result = array(
                        'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'data' => $cobroke_data,
                            'document_id' => $id,
                            'error' => 1,
                        ),
                    );
                }
            }else{
                $msg = __('Gagal melakukan revisi komisi Co-Broke');
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'validationErrors' => $this->validationErrors,
                    'Log' => array(
                        'activity' => $msg,
                        'document_id' => $id,
                        'error' => 1,
                    ),
                );
            }
        }else{
            $result = array();
        }

        return $result;
    }

    function doApproveRevision($id, $approve = true){
        if(!empty($id)){
            $cobroke_data = $this->completeDataCoBrokeUser($id);

            if(!empty($cobroke_data)){
				$owner_data		= Common::hashEmptyField($cobroke_data, 'owner_data', array());
				$broker_data	= Common::hashEmptyField($cobroke_data, 'broker_data', array());

			//	data pemilik property
				$owner_id		= Common::hashEmptyField($owner_data, 'User.id');
				$parent_id		= Common::hashEmptyField($owner_data, 'User.parent_id');
				$owner_name		= Common::hashEmptyField($owner_data, 'User.full_name');
				$owner_email	= Common::hashEmptyField($owner_data, 'User.email');

				$co_broke_property_id = Common::hashEmptyField($owner_data, 'CoBrokeProperty.id');

			//	data pengaju broker
				$broker_id		= Common::hashEmptyField($broker_data, 'User.id');
				$broker_name	= Common::hashEmptyField($broker_data, 'User.full_name');

				$revision_type_price_commission	= Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_type_price_commission');
				$revision_commission			= Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_commission');
				$revision_type_commission		= Common::hashEmptyField($broker_data, 'CoBrokeUser.revision_type_commission');

				$this->id = $id;

				if($approve){
					$text_message = 'menyetujui';

					$final_type_price_commission	= $revision_type_price_commission;
					$final_commission				= $revision_commission;
					$final_type_commission			= $revision_type_commission;
				}
				else{
					$text_message = 'menolak';

					$final_type_price_commission	= '';
					$final_commission				= 0;
					$final_type_commission			= '';

					$this->set('declined', 1);
					$this->set('decline_reason', '');
				}

				$this->set('approved', $approve);
				$this->set('final_type_price_commission', $final_type_price_commission);
				$this->set('final_commission', $final_commission);
				$this->set('final_type_commission', $final_type_commission);

                if($this->save()){
					$cobroke_data['status_approval'] = $approve; 
					$cobroke_data['type_commission'] = Configure::read('Config.Type.CoBroke.Commission');

					$cobroke_data['broker_data']['CoBrokeUser']['final_type_price_commission']	= $final_type_price_commission; 
					$cobroke_data['broker_data']['CoBrokeUser']['final_commission']				= $final_commission; 
					$cobroke_data['broker_data']['CoBrokeUser']['final_type_commission']		= $final_type_commission; 

                    $result = array(
                        'msg' => sprintf(__('Berhasil %s revisi Co-Broke'), $text_message),
                        'status' => 'success',
                        'Log' => array(
                            'document_id' => $this->id,
                            'activity' => sprintf(__('user ID %s %s revisi komisi Co-Broke ID %s'), Configure::read('User.id'), $text_message, $this->id),
                        ),
                        'SendEmail' => array(
                            'to_name' => $owner_name,
                            'to_email' => $owner_email,
                            'subject' => __('Revisi Komisi Co-Broke'),
                            'template' => 'co_broke_deal_revision',
                            'data' => $cobroke_data,
                            'include_role' => array(
                                'role' => array('admin', 'principle'),
                                'from_parent_id' => $parent_id
                            )
                        ),
                        'Notification' => array(
                            'user_id' => $owner_id,
                            'name' => sprintf(__('%s telah %s revisi komisi Co-Broke yang Anda ajukan.'), $broker_name, $text_message),
                            'link' => array(
                                'controller' => 'co_brokes',
                                'action' => 'brokers',
                                $co_broke_property_id,
                                'admin' => true
                            ),
                            'include_role' => array(
                                'role' => array('admin', 'principle'),
                                'from_parent_id' => $parent_id
                            )
                        ),
                    );
                }else{
                    $result = array(
                        'msg' => __('Gagal %s revisi Co-Broke'),
                        'status' => 'error'
                    );
                }
            }else{
                $result = array(
                    'msg' => __('Data tidak ditemukan'),
                    'status' => 'error'
                );
            }
        }else{
            $result = array(
                'msg' => __('Data tidak ditemukan'),
                'status' => 'error'
            );
        }
        
        return $result;    
    }
}
?>