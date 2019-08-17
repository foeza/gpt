<?php
class CoBrokeProperty extends AppModel {
	var $name = 'CoBrokeProperty';
	var $displayField = 'name';

    var $belongsTo = array(
        'Property' => array(
            'className' => 'Property',
            'foreignKey' => 'property_id',
        )
    );

    var $hasMany = array(
        'CoBrokeUser' => array(
            'className' => 'CoBrokeUser',
            'foreignKey' => 'co_broke_property_id',
        ),
        'CoBrokePropertyView' => array(
            'className' => 'CoBrokePropertyView',
            'foreignKey' => 'co_broke_property_id',
        )
    );

    var $validate = array(
        'code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'ID Co Broke harap diisi',
            ),
            'isUnique' => array(
                'rule' => array('isUnique'),
                'on' => 'create',
                'message' => 'ID Co broke telah terdaftar',
            ),
        ),
    );

	/**
	* 	@param string $find - all, list, paginate
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	*		string paginate - Pick opsi query
	* 	@param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
	* 	@param boolean $is_merge - True merge default opsi dengan opsi yang diparsing, False gunakan hanya opsi yang diparsing
	*/
	function getData($find = 'all', $options = array(), $elements = array()) {
        $status = isset($elements['status']) ? $elements['status'] : 'active';
        $mine = isset($elements['mine']) ? $elements['mine'] : false;
        $admin_mine = isset($elements['admin_mine'])?$elements['admin_mine']:false;
        $channel = isset($elements['channel'])?$elements['channel']:false;

        $user_login_id = Configure::read('User.id');
        $is_admin = Configure::read('User.admin');

        $default_options = array(
            'conditions'=> array(),
            'order' => array(),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions']['CoBrokeProperty.status'] = 1;
                break;
            case 'decline':
                $default_options['conditions']['CoBrokeProperty.decline'] = 1;
                break;
            case 'pending-approve':
                $default_options['conditions'] = array(
                    'CoBrokeProperty.status' => 1,
                    'CoBrokeProperty.approve' => 0,
                );
                break;
            case 'stopped':
                $default_options['conditions'] = array(
                    'CoBrokeProperty.status' => 1,
                    'CoBrokeProperty.active' => 0,
                    'CoBrokeProperty.approve' => 1,
                );
                break;
            case 'publish':
                $default_options['conditions'] = array(
                    'CoBrokeProperty.status' => 1,
                    'CoBrokeProperty.active' => 1,
                    'CoBrokeProperty.approve' => 1,
                );
                break;
            case 'deleted':
                $default_options['conditions'] = array(
                    'CoBrokeProperty.status' => 0,
                    'CoBrokeProperty.active' => 0,
                );
                break;
        }

        if( !empty($mine) || !empty($admin_mine) ) {                
            if( !empty($mine) && !$is_admin) {
                $default_options['conditions']['Property.user_id'] = $user_login_id;
                $default_options['contain'][] = 'Property';
            } else if( !empty($admin_mine) ) {
                if( !empty($is_admin) ) {
                    $company = true;
                    $mine = false;
                } else {
                    $default_options['contain'][] = 'Property';
                    $default_options['conditions']['Property.user_id'] = $user_login_id;
                    $company = false;
                }
            }
        }

        if(in_array($status, array('in_office', 'out_office'))){
            $default_options['conditions']['CoBrokeProperty.status'] = 1;
            $default_options['conditions']['CoBrokeProperty.active'] = 1;
            $default_options['conditions']['CoBrokeProperty.approve'] = 1;
        }
        
        if( !empty($company) && empty($mine) ) {
            $parent_id = Configure::read('Principle.id');
            $agent_id = $this->Property->User->getAgents( $parent_id, true );

            if($admin_mine){
                array_push($agent_id, $parent_id);
            }

            $default_options['contain'][] = 'Property';
            $default_options['conditions']['Property.user_id'] = $agent_id;
            // $default_options['conditions'][] = array(
            //     'Property.user_id <>' => 0,
            //     'Property.user_id NOT' => NULL,
            // );
        }

        /*
            Begin - conditions status
        */
        if(!empty($channel)){
            $parent_id = Configure::read('Principle.id');
            $agent_id = $values = $this->CoBrokeUser->User->getData('list', array(
                'conditions' => array(
                    'User.parent_id' => $parent_id,
                ),
                'fields' => array(
                    'User.id', 'User.id',
                ),
            ));

            if(in_array($status, array('in_office', 'out_office'))){
                if($status == 'in_office'){
                    $default_options['conditions']['Property.co_broke_type'] = array(
                        'in_corp', 'both'
                    );

                    $default_options['conditions']['Property.user_id'] = $agent_id;
                }else{
                    $default_options['conditions']['Property.co_broke_type'] = array(
                        'out_corp', 'both'
                    );
                }
            }else{
                $default_options['conditions']['OR'][] = array(
                    'Property.co_broke_type'    => array('out_corp', 'both')
                );

                $default_options['conditions']['OR'][] = array(
                    'Property.co_broke_type'    => array('in_corp', 'both'),
                    'Property.user_id'          => $agent_id
                );
            }

        }else{
            if(in_array($status, array('in_office', 'out_office'))){
                if($status == 'in_office'){
                    $status_conditions = array(
                        'in_corp', 'both'
                    );
                }else{
                    $status_conditions = array(
                        'out_corp', 'both'
                    );
                }

                $default_options['conditions']['Property.co_broke_type'] = $status_conditions;
            }
        }

        $temp_or = Common::hashEmptyField($options, 'conditions.OR');

        if(!empty($temp_or)){
            if(!empty($default_options['conditions']['OR']) && count($default_options['conditions']['OR']) > 1){
                $second_temp_or =& $default_options['conditions']['OR'];

                foreach ($second_temp_or as $key => $val_arr) {
                    $temp_loop =& $second_temp_or[$key];

                    $temp_loop['OR'] = $temp_or;
                }
                
                unset($options['conditions']['OR']);
            }
        }
        /*
            End - conditions status
        */

		if( !empty($options)){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
                
                $default_options['contain'][] = 'Property';
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

    function getMerge ( $data, $id = false, $fieldName = 'CoBrokeProperty.property_id', $elements = array() ) {
        if( empty($data['CoBrokeProperty']) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    $fieldName => $id,
                ),
            ), $elements);

            if( !empty($value) ) {
                $data = array_merge($data, $value);
            }
        }

        return $data;
    }

    function doCoBroke($property_id, $status_reload = false, $force_admin_approvel = false){
        $data = $this->Property->getData('first', array(
            'conditions' => array(
                'Property.id' => $property_id,
                'Property.sold' => 0
            )
        ), array(
            'status' => 'active',
            'mine' => true
        ));

        $data_company = Configure::read('Config.Company.data');
        $is_admin_approval_cobroke = Common::hashEmptyField($data_company, 'UserCompanyConfig.is_admin_approval_cobroke');

        if( !empty($data)){

            $sold = $this->filterEmptyField($data, 'Property', 'sold');
            $property_action_id = $this->filterEmptyField($data, 'Property', 'property_action_id');

            if(empty($sold)){
                $cek_data = $this->getData('first', array(
                    'conditions' => array(
                        'CoBrokeProperty.property_id' => $property_id
                    )
                ), array(
                    'status' => (!empty($status_reload) && $status_reload == 'active') ? 'all' : 'active'
                ));

                $is_admin = Configure::read('User.admin');

                if (!empty($cek_data)){
                    if(!empty($status_reload) && $status_reload == 'active'){
                        $co_broke_id = $this->filterEmptyField($cek_data, 'CoBrokeProperty', 'id');

                        $this->id = $co_broke_id;
                        
                        $this->set('active', 1);
                        $this->set('status', 1);

                        if(($is_admin_approval_cobroke && !$is_admin) || $force_admin_approvel){
                            $this->set('approve', 0);
                        }
                        
                        if($this->save()){
                            $result = array(
                                'status' => 'success',
                                'msg' => __('Berhasil menjadikan Co-Broke kembali.'),
                                'Log' => array(
                                    'document_id' => $this->id,
                                    'activity' => sprintf('Berhasil menjadikan properti ID %s menjadi Co-Broke.', $property_id),
                                )
                            );

                            $result = $this->sendEmailApprovalRequestToAdmin($result, $property_id);
                        }else{
                            $result = array(
                                'status' => 'error',
                                'msg' => __('Properti ini sudah pernah di jadikan Co-Broke.')
                            );
                        }
                    }else{
                        $result = array(
                            'status' => 'error',
                            'msg' => __('Properti ini sudah pernah di jadikan Co-Broke.')
                        );
                    }
                }else{
                    $this->create();

                    $data['CoBrokeProperty']['property_id'] = $property_id;
                    $data['CoBrokeProperty']['change_date'] = date('Y-m-d H:i:s');

                    $code = $this->createRandomNumber( 4, 'bcdfghjklmnprstvwxyz0123456789', 30);
                    $data['CoBrokeProperty']['code'] = $this->generateCode($code);

                    if(($is_admin_approval_cobroke && !$is_admin) || $force_admin_approvel){
                        $data['CoBrokeProperty']['approve'] = 0;
                    }

                    $this->set($data);

                    if($this->save()){
                        $result = array(
                            'status' => 'success',
                            'msg' => __('Berhasil menjadikan Co-Broke.'),
                            'Log' => array(
                                'document_id' => $this->id,
                                'activity' => sprintf('Berhasil menjadikan properti ID %s menjadi Co-Broke.', $property_id),
                            )
                        );

                        $result = $this->sendEmailApprovalRequestToAdmin($result, $property_id);
                    }else{
                        $result = array(
                            'status' => 'error',
                            'msg' => __('Gagal menjadikan Co-Broke.')
                        );
                    }
                }
            }else{
                if($property_action_id == 2){
                    $text = 'Tersewa';
                }else{
                    $text = 'Terjual';
                }

                $result = array(
                    'status' => 'error',
                    'msg' => sprintf(__('Properti ini tidak bisa di jadikan Co-Broke karena sudah %s.'), $text)
                );
            }

        } else {
            $result = array(
                'status' => 'error',
                'msg' => __('properti tidak ditemukan.')
            );
        }

        return $result;
    }

    function doStopToggle($property_id){
        $data = $this->Property->getData('first', array(
            'conditions' => array(
                'Property.id' => $property_id,
                'Property.sold' => 0
            )
        ), array(
            'status' => 'active',
            'mine' => true
        ));

        if(!empty($data)){
            $cek_data = $this->getData('first', array(
                'conditions' => array(
                    'CoBrokeProperty.property_id' => $property_id
                )
            ), array(
                'status' => 'active'
            ));

            if(!empty($cek_data)){
                $id = $this->filterEmptyField($cek_data, 'CoBrokeProperty', 'id');
                $active = $this->filterEmptyField($cek_data, 'CoBrokeProperty', 'active');

                $this->id = $id;

                $value = 0;
                $text = __('menghentikan');
                if(empty($active)){
                    $value = 1;
                    $text = __('melanjutkan kembali');
                }

                $default_msg = sprintf(__('%s properti Co Broke.'), $text);

                $this->set('active', $value);

                if($this->save()){
                    $msg = __('Berhasil %s', $default_msg);

                    $result = array(
                        'status' => 'success',
                        'msg' => $msg,
                        'Log' => array(
                            'document_id' => $this->id,
                            'activity' => sprintf('Berhasil %s properti ID %s menjadi Co Broke.', $text, $property_id),
                        )
                    );
                }else{
                    $msg = __('Gagal %s', $default_msg);

                    $result = array(
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'data' => $data,
                            'error' => 1,
                        ),
                        'msg' => sprintf(__('Gagal %s properti Co Broke.'), $text)
                    );
                }
            }else{
                $msg = __('Gagal %s', $default_msg);

                $result = array(
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'data' => $data,
                        'error' => 1,
                    ),
                    'msg' => __('Data Co Broke tidak ditemukan.')
                );
            }
        }else{
            $msg = __('Gagal %s', $default_msg);

            $result = array(
                'status' => 'error',
                'msg' => __('properti tidak ditemukan.')
            );
        }

        return $result;
    }

    function generateCode($rand_code){
        $flag = true;
        $code = false;
        $idx = 0;
        while ($flag) {
            $code = strtoupper(implode('', $rand_code));
            $code .= $idx;

            $check = $this->getData('count', array(
                'conditions'=> array(
                    'CoBrokeProperty.code'=> $code,
                ),
            ), array(
                'status' => false,
                'company' => false,
            ));
            
            if( empty($check) ) {
                $flag = false;
                $idx++;
            }
        }

        return $code;
    }

    function customBindModel($options, $params){
        $sort = $this->filterEmptyField($params, 'named', 'sort');
        $direction = $this->filterEmptyField($params, 'named', 'direction', 'ASC');
        $price = $this->filterEmptyField($params, 'named', 'price');

        if(!empty($options['contain'])){
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

                if(isset($this->Property->virtualFields['total_baths'])){
                    $this->virtualFields['total_baths'] = 'COALESCE(PropertyAsset.baths, 0) + COALESCE(PropertyAsset.baths_maid, 0)';
                }
                if(isset($this->Property->virtualFields['total_beds'])){
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

            if(in_array('PropertySold', $options['contain']) || $sort == 'CoBrokeProperty.price_converter'){
                $bindModel['PropertySold'] = array(
                    'className' => 'PropertySold',
                    'foreignKey' => false,
                    'conditions' => array(
                        'CoBrokeProperty.property_id = PropertySold.property_id'
                    )
                );
            }

            if( (!empty($sort) && $sort == 'CoBrokeProperty.price_converter') || !empty($price) ) {
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

                $options['order']['price_converter'] = $direction;
                $options['contain'][] = 'PropertySold';
            }

            // unset($this->Property->virtualFields);
            
            if(!empty($bindModel)){
                $this->bindModel(array(
                    'hasOne' => $bindModel
                ), false);
            }
        }

        return $options;
    }

    function deleteCoBroke($property_id, $co_broke_commision = false){
        $co_broke = $this->getData('first', array(
            'conditions' => array(
                'CoBrokeProperty.property_id' => $property_id
            )
        ), array(
            'status' => 'all'
        ));

        if(empty($co_broke_commision)){
            $field = array(
                'CoBrokeProperty.status' => 1,
                'CoBrokeProperty.active' => 0,
            );

            $co_broke_id = $this->filterEmptyField($co_broke, 'CoBrokeProperty', 'id');

            $co_broke_count = $this->CoBrokeUser->getData('count', array(
                'conditions' => array(
                    'CoBrokeUser.co_broke_property_id' => $co_broke_id
                )
            ), array(
                'status' => 'active'
            ));

            if(empty($co_broke_count)){
                $field = array(
                    'CoBrokeProperty.status' => 0,
                    'CoBrokeProperty.active' => 0,
                );
            }
        }else{
            $field = array(
                'CoBrokeProperty.status' => 1,
                'CoBrokeProperty.active' => 1,
            );
        }

        $this->updateAll(
            $field,
            array(
                'CoBrokeProperty.property_id' => $property_id
            )
        );
    }

    function CoBrokeChangeStatus($property_id, $data){
        $property = $this->Property->getData('first', array(
            'conditions' => array(
                'Property.id' => $property_id
            )
        ), array(
            'status' => 'all'
        ));

        $result = false;
        if(!empty($property)){
            $property = $this->getMerge($property, $property_id, 'CoBrokeProperty.property_id', array(
                'status' => 'all'
            ));

            $this->Property->clear();

            $force_admin_approvel = false;
            if(!empty($data['Property']['force_approve'])){
                $force_admin_approvel = true;
            }

            $arr_field = array(
                'bt', 
                'co_broke_commision', 
                'type_price_co_broke_commision',
                'type_co_broke_commission', 
                'is_cobroke',
                'co_broke_type'
            );
            
            if(!empty($data['Property'])){
                foreach ($data['Property'] as $key => $value) {
                    if(!in_array($key, $arr_field)){
                        unset($data['Property'][$key]);
                    }
                }
                
                $this->Property->set($data);
            }
            
            $this->Property->id = $property_id;

            $new_co_broke_commision = $this->filterIssetField($data, 'Property', 'co_broke_commision');
            $old_co_broke_commision = $this->filterIssetField($property, 'Property', 'co_broke_commision');
            
            $new_type_price_co_broke_commision = $this->filterIssetField($data, 'Property', 'type_price_co_broke_commision');
            $new_type_co_broke_commission = $this->filterIssetField($data, 'Property', 'type_co_broke_commission');

            $is_cobroke = $this->filterIssetField($data, 'Property', 'is_cobroke');

            $this->Property->set('co_broke_commision', $new_co_broke_commision);
            $this->Property->set('type_price_co_broke_commision', $new_type_price_co_broke_commision);
            $this->Property->set('type_co_broke_commission', $new_type_co_broke_commission);

            /*kalo sebelumnya ada trus di buat 0*/
            if(!empty($old_co_broke_commision) && empty($new_co_broke_commision)){
                $this->deleteCoBroke($property_id, $new_co_broke_commision);

                /*
                    balikan ke keadaan default
                */
                $this->Property->set('co_broke_commision', 0);
                $this->Property->set('type_price_co_broke_commision', 'percentage');
                $this->Property->set('type_co_broke_commission', 'in_corp');

                $this->Property->set('is_cobroke', 0);
            }else{
                if(!empty($is_cobroke)){


                    $this->doCoBroke($property_id, 'active', $force_admin_approvel);
                }else if(empty($is_cobroke) && !empty($property['CoBrokeProperty'])){
                    $this->Property->set('co_broke_commision', 0);

                    $this->deleteCoBroke($property_id);
                }

                $this->Property->set('is_cobroke', $is_cobroke);
            }

            $result = $this->Property->save();
        }

        return $result;
    }

    function doMakeCoBroke($data, $property_id){
        $result = array();
        if(!empty($data)){            
            $data['Property']['is_cobroke'] = 1;
            
            $this->Property->set($data);

            $this->Property->validator()
                ->add('type_co_broke_commission', array(
                    'notempty' => array(
                        'rule' => 'notempty',
                        'message' => __('Mohon diisi alasan penolakan aplikasi ini')
                    ),
                    'multiple' => array(
                        'rule' => array('multiple', array(
                            'in'  => array('in_corp', 'out_corp'),
                        )),
                        'message' => 'Silakan pilih revisi tipe asal komisi apakah dari "Penjualan Properti" atau "Komisi Agen" '
                    )
                ))
                ->add('co_broke_type', array(
                    'notempty' => array(
                        'rule' => 'notempty',
                        'message' => __('Mohon di pilih tipe Co-Broke')
                    ),
                    'multiple' => array(
                        'rule' => array('multiple', array(
                            'in'  => array('in_corp', 'out_corp', 'both'),
                        )),
                        'message' => 'Silakan pilih tipe Co-Broke'
                    )
                ));

            $default_msg = 'menjadikan Co-Broke';

            if($this->Property->validates($data)){
                if($this->CoBrokeChangeStatus($property_id, $data)){
                    $is_approval_property   = Configure::read('Config.Company.data.UserCompanyConfig.is_approval_property');
                    $is_admin_approval_cobroke = Configure::read('Config.Company.data.UserCompanyConfig.is_admin_approval_cobroke');

                    $msg = __('Berhasil %s', $default_msg);

                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $msg,
                            'data' => $data,
                            'document_id' => $property_id,
                        ),
                    );

                    if(!empty($is_approval_property) || !empty($is_admin_approval_cobroke)){
                        $result['msg'] = __('Berhasil mengajukan properti untuk dijadikan co-broke. Properti anda sedang dalam tahap peninjauan oleh admin');
                    }

                    $is_admin = Configure::read('User.admin');
                    
                    if($is_admin){
                        $commission                 = Common::hashEmptyField($data, 'Property.commission');
                        $co_broke_commision         = Common::hashEmptyField($data, 'Property.co_broke_commision');
                        $type_co_broke_commission   = Common::hashEmptyField($data, 'Property.type_co_broke_commission');
                        
                        $data = $this->Property->getData('first', array(
                            'conditions' => array(
                                'Property.id' => $property_id,
                                'Property.sold' => 0
                            )
                        ), array(
                            'status' => 'active',
                            'mine' => true
                        ));

                        $data = $this->Property->getDataList($data, array(
                            'contain' => array(
                                'MergeDefault',
                                'PropertyAddress',
                                'PropertyPrice',
                                'User',
                            ),
                        ));

                        $data['Property']['commission'] = $commission;
                        $data['Property']['co_broke_commision'] = $co_broke_commision;
                        $data['Property']['type_co_broke_commission'] = $type_co_broke_commission;

                        $full_name      = Common::hashEmptyField($data, 'User.full_name');
                        $email          = Common::hashEmptyField($data, 'User.email');
                        $requester_id   = Common::hashEmptyField($data, 'User.id');

                        $mls_id         = Common::hashEmptyField($data, 'Property.mls_id');
                        $title          = Common::hashEmptyField($data, 'Property.title');

                        $result['SendEmail'] = array(
                            'to_name' => $full_name,
                            'to_email' => $email,
                            'subject' => __('Pemberitahuan! Properti Anda telah dijadikan Co-Broke'),
                            'template' => 'cobroke_admin_made',
                            'data' => $data,
                        );

                        $result['Notification'] = array(
                            'user_id' => $requester_id,
                            'name' => sprintf(__('Properti "%s - %s" telah dijadikan Co-Broke oleh admin/principle'), $mls_id, $title),
                            'link' => array(
                                'controller' => 'co_brokes',
                                'action' => 'me',
                                'admin' => true
                            ),
                        );
                    }else{
                        $result = $this->sendEmailApprovalRequestToAdmin($result, $property_id);
                    }
                }else{
                    $msg = __('Gagal %s', $default_msg);
                    $result = array(
                        'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'data' => $data,
                            'error' => 1,
                        ),
                    );
                }
            }else{
                $msg = __('Gagal %s', $default_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'data' => $data,
                        'error' => 1,
                    ),
                );
            }
        }

        return $result;
    }

    function doDelete($co_broke_id){
        $co_broke_data = $this->getData('count', array(
            'conditions' => array(
                'CoBrokeProperty.id' => $co_broke_id
            )
        ), array(
            'mine' => true,
            'admin_mine' => true
        ));

        if(!empty($co_broke_data)){
            $property_id = $this->filterEmptyField($co_broke_data, 'CoBrokeProperty', 'property_id');

            $co_broke = $this->CoBrokeUser->getData('count', array(
                'conditions' => array(
                    'CoBrokeUser.co_broke_property_id' => $co_broke_id
                )
            ), array(
                'status' => 'approve',
                'admin_reverse' => true
            ));
            
            if(empty($co_broke)){
                $this->updateAll(
                    array(
                        'CoBrokeProperty.status' => 0,
                        'CoBrokeProperty.active' => 0,
                    ),
                    array(
                        'CoBrokeProperty.id' => $co_broke_id
                    )
                );

                $this->Property->id = $property_id;
                $this->Property->set('is_cobroke', 0);
                $this->Property->save();

                $result = array(
                    'msg' => __('Properti Co-Broke ini berhasil di hapus'),
                    'status' => 'success',
                    'Log' => array(
                        'document_id' => $co_broke_id,
                        'activity' => sprintf('Berhasil menghapus Co-Broke #%s.', $co_broke_id),
                    )
                );
            }else{
                $result = array(
                    'msg' => __('Properti Co-Broke ini tidak bisa di hapus dikarenakan telah memiliki broker yang di setujui'),
                    'status' => 'error'
                );
            }
        }else{
            $result = array(
                'msg' => __('Data tidak ditemukan.'),
                'status' => 'error'
            );
        }

        return $result;
    }

    function doApproveRequest($data, $id, $type){
        $data_cobroke = $this->getData('first', array(
            'conditions' => array(
                'CoBrokeProperty.id' => $id
            )
        ));

        if(!empty($data_cobroke) && (($type == 'reject' && !empty($data)) || ($type == 'approve') ) ){
            $property_id = Common::hashEmptyField($data_cobroke, 'CoBrokeProperty.property_id');

            $data_cobroke = $this->Property->getMerge($data_cobroke, $property_id);

            $user_id    = Common::hashEmptyField($data_cobroke, 'Property.user_id');
            $code       = Common::hashEmptyField($data_cobroke, 'CoBrokeProperty.code');

            $data_cobroke = $this->Property->getDataList($data_cobroke, array(
                'contain' => array(
                    'MergeDefault',
                    'PropertyAddress',
                    'PropertyPrice',
                    'User',
                ),
            ));

            $full_name      = Common::hashEmptyField($data_cobroke, 'User.full_name');
            $email          = Common::hashEmptyField($data_cobroke, 'User.email');
            $requester_id   = Common::hashEmptyField($data_cobroke, 'User.id');

            $this->id = $id;

            if($type == 'approve'){
                $text = 'menyetujui';
                $text_subject = 'setujui';
                $greet_subject = 'Selamat';

                $data['CoBrokeProperty']['status'] = 1;
                $data['CoBrokeProperty']['active'] = 1;
                $data['CoBrokeProperty']['approve'] = 1;
                $data['CoBrokeProperty']['decline'] = 0;
            }else{
                $text = 'menolak';
                $text_subject = 'tolak';
                $greet_subject = 'Maaf';

                $data['CoBrokeProperty']['status'] = 0;
                $data['CoBrokeProperty']['approve'] = 0;
                $data['CoBrokeProperty']['decline'] = 1;
            }

            $this->set($data);

            $validate = true;
            if($type == 'reject'){
                $this->validator()->add('decline_reason', 'required', array(
                    'rule' => 'notempty',
                    'message' => __('Mohon diisi alasan penolakan aplikasi ini')
                ));

                $validate = $this->validates($data);
            }

            if($validate){
                if($this->save()){
                    $msg = sprintf(__('Anda berhasil %s Co-Broke'), $text);
                    $msgLog = sprintf(__('Anda berhasil %s Co-Broke dengan ID #%s'), $text, $id);
                    
                    $data_cobroke['input_data'] = $data;
                    $data_cobroke['approval'] = true;

                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'SendEmail' => array(
                            'to_name' => $full_name,
                            'to_email' => $email,
                            'subject' => sprintf(__('%s, Listing Co-Broke Anda di%s'), $greet_subject, $text_subject) ,
                            'template' => 'cobroke_approval_request',
                            'data' => $data_cobroke,
                        ),
                        'Notification' => array(
                            'user_id' => $requester_id,
                            'name' => sprintf(__('%s pengajuan aplikasi Co Broke dengan kode #%s telah di%s.'), $greet_subject, $code, $text_subject),
                            'link' => array(
                                'controller' => 'co_brokes',
                                'action' => 'me',
                                'admin' => true
                            ),
                        ),
                        'Log' => array(
                            'document_id' => $this->id,
                            'activity' => $msgLog,
                        )
                    );
                }else{
                    $msg = sprintf(__('Anda gagal %s Co-Broke'), $text);
                    $result = array(
                        'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'data' => $data_cobroke,
                            'document_id' => $id,
                            'error' => 1,
                        ),
                    );
                }
            }else{
                $msg = sprintf(__('Anda gagal %s Co-Broke'), $text);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'document_id' => $id,
                        'error' => 1,
                    ),
                );
            }
        }else{
            if($type != 'reject'){
                $result = array(
                    'msg' => __('Data tidak ditemukan.'),
                    'status' => 'error'
                );
            }else{
                $result = array();
            }
        }

        return $result;
    }

    function sendEmailApprovalRequestToAdmin($result, $property_id){
        $data_company = Configure::read('Config.Company.data');
        $is_admin_approval_cobroke = $this->filterEmptyField($data_company, 'UserCompanyConfig', 'is_admin_approval_cobroke');

        if(!empty($is_admin_approval_cobroke)){
            $data = $this->Property->getData('first', array(
                'conditions' => array(
                    'Property.id' => $property_id,
                    'Property.sold' => 0
                )
            ), array(
                'status' => 'active',
                'mine' => true
            ));

            $data = $this->Property->getDataList($data, array(
                'contain' => array(
                    'MergeDefault',
                    'PropertyAddress',
                    'PropertyPrice',
                    'User',
                ),
            ));

            $data['request'] = true;
            $data['bbc'] = true;

            $email = $this->Property->User->getData('list', array(
                'fields' => array(
                    'User.id','User.email'
                )
            ), array(
                'role' => 'admin',
                'company' => true
            ));
            
            $full_name = $this->filterEmptyField($data, 'User', 'full_name');

            if(!empty($email)){
                $parent_id = Configure::read('Principle.id');
                $parent_data = $this->CoBrokeUser->User->getData('first', array(
                    'conditions' => array(
                        'User.id' => $parent_id
                    )
                ), array(
                    'role' => 'principle'
                ));
                
                $result['SendEmail'][] =  array(
                    'to_name' => 'Admin',
                    'to_email' => $email,
                    'subject' => sprintf(__('Agen %s mengajukan penambahan status properti menjadi Co-Broke'), $full_name) ,
                    'template' => 'cobroke_approval_request',
                    'data' => $data,
                );

                $notification = array();

                foreach ($email as $id => $value) {
                    $notification[] = array(
                        'Notification' => array(
                            'user_id' => $id,
                            'name' => sprintf(__('Agen %s mengajukan penambahan status properti menjadi Co-Broke'), $full_name),
                            'link' => array(
                                'controller' => 'co_brokes',
                                'action' => 'approval',
                                'admin' => true
                            ),
                        ),
                    );
                }

                if(!empty($parent_data)){
                    $full_name_principle = $this->filterEmptyField($parent_data, 'User', 'full_name');
                    $email_principle = $this->filterEmptyField($parent_data, 'User', 'email');

                    $result['SendEmail'][] =  array(
                        'to_name' => $full_name_principle,
                        'to_email' => $email_principle,
                        'subject' => sprintf(__('Agen %s mengajukan penambahan status properti menjadi Co-Broke'), $full_name) ,
                        'template' => 'cobroke_approval_request',
                        'data' => $data,
                    );

                    $notification[] = array(
                        'Notification' => array(
                            'user_id' => $parent_id,
                            'name' => sprintf(__('Agen %s mengajukan penambahan status properti menjadi Co-Broke'), $full_name),
                            'link' => array(
                                'controller' => 'co_brokes',
                                'action' => 'approval',
                                'admin' => true
                            ),
                        ),
                    );
                }

                $result['Notification'] = $notification;
            }
        }
        
        return $result;
    }
}
?>