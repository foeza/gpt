<?php
class Message extends AppModel {
	var $name = 'Message';
	var $displayField = 'message';

	var $validate = array(
		'to_id' => array(
            'checkReceive' => array(
                'rule' => array('checkReceive'),
                'message' => 'Penerima tidak ditemukan',
            ),
		),
        'to_email' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon masukkan email tujuan',
            ),
            'email' => array(
                'rule' => array('email'),
                'message' => 'Format email salah',
            ),
            'checkSendEmail' => array(
                'rule' => array('checkSendEmail'),
                'message' => 'Email tidak terdaftar',
            ),
        ),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama Anda',
			),
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan alamat email Anda',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email Anda salah',
			),
		),
		'phone' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nomor telepon Anda',
			),
			'notMatch' => array(
				'rule' => array('validatePhoneNumber'),
				'message' => 'Format nomor telepon salah. Contoh yang benar: +6281234567 atau 0812345678',
			),
			'minLength' => array(
				'rule' => array('minLength', 6),
				'message' => 'Nomor telepon minimal 6 karakter',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 15),
				'message' => 'Nomor telepon maksimal 15 karakter',
			),
		),
		'message' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan pesan',
			),
		),
        'security_code' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon centang untuk menandakan Anda bukan robot',
            ),
        ),
	);

    var $hasOne = array(
		'MessageTrash' => array(
			'className' => 'MessageTrash',
			'foreignKey' => 'message_id',
		),
	);

    var $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'from_id',
        ),
        'ToUser' => array(
            'className' => 'ToUser',
            'foreignKey' => 'to_id',
        ),
        'Property' => array(
            'className' => 'Property',
            'foreignKey' => 'property_id',
        ),
        'MessageCategory' => array(
            'className' => 'MessageCategory',
            'foreignKey' => 'message_category_id',
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['cnt_group'] = 'Message.from_id+Message.to_id';
    }

    function checkReceive() {
        $parent_id = Configure::read('Principle.id');

        if( !empty($parent_id) ) {
            $to_id = Common::hashEmptyField($this->data, 'Message.to_id');
            
            if( !empty($to_id) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    function checkSendEmail() {
        $parent_id = Configure::read('Principle.id');

        if( !empty($parent_id) ) {
            $to_email = !empty($this->data['Message']['to_email'])?$this->data['Message']['to_email']:false;

            $existEmail = $this->User->getData('first', array(
                'conditions' => array(
                    'User.email' => $to_email,
                ),
            ));

            if( !empty($existEmail) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

	function validatePhoneNumber($data) {
        if (preg_match('/^[0-9]{1,}$/', $data['phone'])==1 || ( substr($data['phone'], 0,1)=="+" && preg_match('/^[0-9]{1,}$/', substr($data['phone'], 1,strlen($data['phone'])))==1 ))
           return true; 
        else return false;
    }

	function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $mine = isset($elements['mine'])?$elements['mine']:false;
        $user_login_id = Configure::read('User.id');

        $default_options = array(
            'conditions'=> array(),
			'order' => array(
				'Message.created' => 'DESC',
				'Message.id' => 'DESC',
			),
			'contain' => array(
				'MessageTrash' => array(
					'conditions' => array(
						'MessageTrash.user_id' => $user_login_id,
					),
				),
			),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
					'Message.status' => 1,
					'MessageTrash.id' => NULL,
            	));
                break;
            
            case 'trash':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
					'Message.status' => 1,
					'MessageTrash.id NOT' => NULL,
                ));
                break;
            
            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
					'Message.status' => 0,
                ));
                break;
        }

        if( !empty($mine) ) {
            $user_admin = Configure::read('User.companyAdmin');
            $group_id = Configure::read('User.group_id');

            if( !empty($user_admin) ) {
                $parent_id = Configure::read('Principle.id');
                
                if( $group_id == 4 ){
                    $parent_id = $this->User->getAgents($parent_id, true, 'list', false, array('role' => 'principle'));
                }

                $default_options['conditions']['AND']['OR'] = array(
                    'User.parent_id' => $parent_id,
                    'ToUser.parent_id' => $parent_id,
                    'ToUser.id' => $parent_id
                );

                if( !in_array($group_id, array( 3,4 )) ){
                    $admin_list = $this->User->getAgents($parent_id, true, 'list', false, array('role' => 'admin'));

                    if(!empty($admin_list[$user_login_id])){
                        unset($admin_list[$user_login_id]);
                    }

                    $default_options['conditions']['AND']['ToUser.id NOT'] = $admin_list;
                }

                $default_options['contain'][] = 'User';
                $default_options['contain'][] = 'ToUser';
            } else {
                $data_arr = $this->User->getUserParent($user_login_id);
                $user_ids = Common::hashEmptyField($data_arr, 'user_ids');
                $is_sales = Common::hashEmptyField($data_arr, 'is_sales');

                $user_ids = (array) $user_ids;
                $user_ids = array_filter(array_merge($user_ids, array($user_login_id)));

            	$default_options['conditions']['AND']['OR'] = array(
                    'Message.to_id' => $user_ids,
                    'Message.from_id' => $user_ids,
                );
            }
        }

        return $this->merge_options($default_options, $options, $find);
    }

    function getLastMsg ( $data, $from_id, $to_id ) {
        $value = $this->getData('first', array(
            'conditions' => array(
                'OR' => array(
                    array(
                        'Message.from_id' => $from_id,
                        'Message.to_id' => $to_id,
                    ),
                    array(
                        'Message.from_id' => $to_id,
                        'Message.to_id' => $from_id,
                    ),
                ),
            ),
            'contain' => array(
                'User',
                'ToUser',
                'Property'
            ),
        ));
        if( !empty($value) ) {
            $data['LastMessage'] = $value;

		//	personal page
			$userID			= Common::hashEmptyField($value, 'Message.to_id');

			if($userID){
				$userConfig	= $this->User->UserConfig->getMerge(array(), $userID);
				$data		= Hash::insert($data, 'LastMessage.ToUser.UserConfig', array_shift($userConfig));
			}
        }

        return $data;
    }

    function getCountUnRead ( $data, $from_id, $to_id ) {
        $is_rest = Configure::write('__Site.is_rest');
        $is_admin = Configure::read('User.admin');
        $user_id = Configure::read('User.id');
        $parent_id = Configure::read('Principle.id');

        if($is_rest){
            if(!empty($is_admin)){
                $value = $this->filterEmptyField($data, 'LastMessage', false, $data);

                $read = $this->filterEmptyField($value, 'Message', 'read');

                $to_user_group_id = $this->filterEmptyField($data, 'ToUser', 'group_id');
                $user_group_id = $this->filterEmptyField($data, 'User', 'group_id');
                
                $agent_id = $this->User->getAgents( $parent_id, true, 'list', false, array('role' => 'all') );
                $conditions['OR'] = array(
                    array(
                        'Message.from_id' => $from_id,
                        'Message.to_id' => $user_id,
                    ),
                    array(
                        'Message.from_id' => $from_id,
                        'Message.to_id' => $to_id,
                    ),
                    array(
                        'Message.from_id' => $to_id,
                        'Message.to_id' => $from_id,
                    ),
                );

                if($to_user_group_id == 5 && $user_group_id == 5){
                    
                }else{
                    if(empty($read)){
                        
                    }else{
                        $conditions['Message.from_id NOT'] = $agent_id;
                    }
                }
            }else{
                if($from_id == $user_id){
                    $from_id = $to_id;
                }
                
                $conditions = array(
                    'Message.from_id' => $from_id,
                    'Message.to_id' => $user_id,
                );
            }

            $value = $this->getData('count', array(
                'conditions' => array_merge($conditions,array(
                    'Message.read' => 0,
                )) ,
            ));

            $data['Message']['count_unread'] = $value;
        }

        return $data;
    }

    public function getDataContain($value) {
        $user_id = Configure::read('User.id');
        
    	$property_id = !empty($value['Message']['property_id'])?$value['Message']['property_id']:false;
        $from_id = !empty($value['Message']['from_id'])?$value['Message']['from_id']:false;
        $to_id = !empty($value['Message']['to_id'])?$value['Message']['to_id']:false;
		$message_category_id = !empty($value['Message']['message_category_id'])?$value['Message']['message_category_id']:false;

		$value = $this->Property->getMerge($value, $property_id);
        $value = $this->Property->getMergeDefault($value);
        $value = $this->PropertyAddress->getMerge($value, $property_id);
        $value = $this->User->getMerge($value, $from_id);
        $value = $this->getLastMsg($value, $from_id, $to_id);
        $value = $this->getCountUnRead($value, $from_id, $to_id);
        $value = $this->MessageCategory->getMerge($value, $message_category_id);

        $messageContent = !empty($value['Message']['message']) ? $value['Message']['message'] : false;

        if ( !empty($value['Property']['mls_id']) ) {
            $mls_id = ' - '.$value['Property']['mls_id'];
        } else {
            $mls_id = ' ';
        }

        $value['MessageContent'] = $messageContent.$mls_id; 

        $last_data_message = !empty($value['LastMessage']) ? $value['LastMessage'] : array();

        $last_user_id = !empty($last_data_message['User']['id']) ? $last_data_message['User']['id'] : false;
        $last_to_user_id = !empty($last_data_message['ToUser']['id']) ? $last_data_message['ToUser']['id'] : false;

        if(!is_array($user_id) && $user_id == $last_user_id && !empty($last_data_message['ToUser'])){
            $value['LastMessage']['User'] = $last_data_message['ToUser'];
            $value['LastMessage']['ToUser'] = $last_data_message['User'];
                       
        }
		return $value;
    }

    public function getDataList($data, $type = 'many') {
        if( !empty($data) ){
            $this->Property = ClassRegistry::init('Property');
            $this->PropertyAddress = ClassRegistry::init('PropertyAddress');

            if( $type == 'many' ) {
				foreach ($data as $key => $value) {
					$value = $this->getDataContain($value);
					$data[$key] = $value;
				}
			} else if( $type == 'single' ) {
				$data = $this->getDataContain($data);
			}
		}

        return $data;
    }

    function apiSave($data){
        $result = false;
        $default_msg = __('mengirim pesan');

        if(!empty($data['Message'])){
            $this->create();
            $this->set($data);
            $data['Message']['full_base_url'] = $this->filterEmptyField($data, 'Message', 'utm');

            $to_id = $this->filterEmptyField($data, 'Message', 'to_id');
            $from_id = $this->filterEmptyField($data, 'Message', 'from_id');
            $from_name = $this->filterEmptyField($data, 'Message', 'name');
            $to_name = $this->filterEmptyField($data, 'Message', 'to_name');
            $to_email = $this->filterEmptyField($data, 'Message', 'to_email');
            $site_name = $this->filterEmptyField($data, 'Message', 'utm');

            if($site_name){
                $site_name = preg_replace('#^https?://#', '', $site_name);
            }

            if($this->save()){
                $result = array(
                    'msg' => sprintf(__('Berhasil %s ke %s'), $default_msg, $to_name),
                    'status' => 'success',
                    'id' => $to_id,
                    'from_id' => $from_id,
                    'SendEmail' => array(
                        'to_name' => $to_name,
                        'to_email' => $to_email,
                        'template' => 'message',
                        'subject' => sprintf(__('Anda mendapatkan pesan dari %s di %s'),$from_name, $site_name),
                        'data' => $data,
                    ),
                );
            }else{
                $result = array(
                    'msg' => __('Gagal %s, mohon lengkapi semua data yang diperlukan', $default_msg),
                    'status' => 'error',
                ); 
            }

        }else{
            $result = array(
                'msg' => __('Gagal %s, data Message tidak ada', $default_msg),
                'status' => 'error',
            );
        }
        return $result;
    }

	function doSave( $data, $from_id = false, $to_id = false, $is_api = false ) {
        $result = false;
        $default_msg = __('mengirim pesan');
        $is_admin = Configure::read('User.admin');

        if ( !empty($data) ) {
            $send_message = true;

            if(!$is_api){
                $this->User = ClassRegistry::init('User');

                $userData = Configure::read('User.data');
                $site_name = Configure::read('__Site.site_name');

                $user_id_login = !empty($userData['id']) ? $userData['id'] : false;

                if((!empty($from_id) && !empty($to_id) && $user_id_login == $from_id || $user_id_login == $to_id) || (empty($from_id) && empty($to_id))){
                    if($user_id_login == $to_id){
                        $temp_from = $from_id;
                        $from_id = $user_id_login;
                        $to_id = $temp_from;
                    }else{
                        $from_id = $user_id_login;
                    }
                }else if($is_admin){
                    $parent_id = Configure::read('Principle.id');

                    $agent_id = $this->User->getAgents( $parent_id, true, 'list', false, array('role' => 'all') );

                    if(in_array($from_id, $agent_id) && in_array($to_id, $agent_id)){
                        $send_message = false;
                    }else{
                        if(!in_array($from_id, $agent_id) && in_array($to_id, $agent_id)){
                            $temp_from = $from_id;
                            $from_id = $to_id;
                            $to_id = $temp_from;
                        }
                    }
                }
                if( !empty($to_id) ) {
                    $conditionsToData = array(
                        'User.id' => $to_id,
                    );
                } else {
                    $to_email = !empty($data['Message']['to_email'])?$data['Message']['to_email']:false;
                    $conditionsToData = array(
                        'User.email' => $to_email,
                    );
                }

                $userData = $this->User->UserProfile->getMerge($userData, $from_id);
                $toData = $this->User->getData('first', array(
                    'conditions' => $conditionsToData,
                ));
                $toData = !empty($toData)?$toData:array();

                $to_id = !empty($toData['User']['id'])?$toData['User']['id']:false;
                $to_name = !empty($toData['User']['full_name'])?$toData['User']['full_name']:false;
                $to_email = !empty($toData['User']['email'])?$toData['User']['email']:false;
                $from_name = !empty($userData['full_name'])?$userData['full_name']:false;
                $from_email = !empty($userData['email'])?$userData['email']:false;

                $data['Message']['to_id'] = $to_id;
                $data['Message']['from_id'] = $from_id;
                $data['Message']['name'] = $from_name;
                $data['Message']['email'] = $from_email;
                $data['Message']['phone'] = !empty($userData['UserProfile']['no_hp'])?$userData['UserProfile']['no_hp']:false;
            }else{
                $this->removeValidate();
            }

            // get instance last message
            $default_options = array(
                'conditions'=> array(
                    'Message.from_id' => $to_id,
                    'Message.to_id' => $from_id,
                ),
                'order' => array(
                    'Message.created' => 'DESC',
                    'Message.id' => 'DESC',
                ),
            );
            $value = $this->getData('first', $default_options);
            $instanace = $this->filterEmptyField($value, 'Message', 'instanace');

            if($instanace == 'rumahku'){
                $data['Message']['instanace'] = 'to_rumahku';
                $data['Message']['utm'] = $this->filterEmptyField($value, 'Message', 'utm');
            }
            // 

            $this->create();
            $this->set($data);

            if($send_message){
                if( $this->validates() ) {
                    if( $this->save() ) {
                        $id = $this->id;

                        if(!$is_api && $instanace <> 'rumahku'){
                            if( !empty($toData) ) {
                                $data['ToUser'] = $this->filterEmptyField($toData, 'User');
                            }
                            if( !empty($userData) ) {
                                $data['FromUser'] = $userData;
                            }

                            $result = array(
                                'msg' => sprintf(__('Berhasil %s'), $default_msg),
                                'status' => 'success',
                                'id' => $to_id,
                                'from_id' => $from_id,
                                'SendEmail' => array(
                                    'to_name' => $to_name,
                                    'to_email' => $to_email,
                                    'subject' => sprintf(__('Anda mendapatkan pesan dari %s di %s'),$from_name, $site_name),
                                    'template' => 'message',
                                    'data' => $data,
                                ),
                            );
                        }else{
                            $result = array(
                                'msg' => sprintf(__('Berhasil %s'), $default_msg),
                                'status' => 'success'
                            );
                        }
                            
                    } else {
                        $result = array(
                            'msg' => sprintf(__('Gagal %s'), $default_msg),
                            'status' => 'error',
                        );
                    }
                } else {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s, mohon lengkapi semua data yang diperlukan'), $default_msg),
                        'status' => 'error',
                    );
                }
            }else if(!$send_message && $is_admin){
                $result = array(
                    'msg' => __('Anda tidak bisa melakukan kirim pesan di karenakan kedua agen tersebut adalah agen Anda sendiri.'),
                    'status' => 'error',
                );
            }
                
        }

        return $result;
    }

    function doRead( $from_id = false, $to_id = false ) {
        $user_id = Configure::read('User.id');
        $is_admin = Configure::read('User.admin');

        if( !empty($is_admin) && $from_id != $user_id && $to_id != $user_id ) {
            $parent_id = Configure::read('Principle.id');

            $user_id = $this->User->getAgents( $parent_id, true, 'list', false, array('role' => 'all') );

            $user_id[$parent_id] = $parent_id;
        }

        return $this->updateAll(array(
            'Message.read' => 1,
        ), array(
            'Message.read' => 0,
            'Message.to_id' => $user_id,
            'OR' => array(
                array(
                    'Message.from_id' => $from_id,
                    'Message.to_id' => $to_id,
                ),
                array(
                    'Message.from_id' => $to_id,
                    'Message.to_id' => $from_id,
                ),
            ),
        ));
    }

    function getNotif () {
        $user_id = Configure::read('User.id');
        $is_admin = Configure::read('User.admin');
        $group_id = Configure::read('User.group_id');
        $parent_id = Configure::read('Principle.id');

        $options = array(
            'conditions' => array(
                'Message.to_id' => $user_id
            ),
            'group' => array(
                'Message.from_id'
            ),
            'order' => array(
                'Message.max_id' => 'DESC'
            )
        );

        if( !empty($is_admin) ) {
            if($group_id != 5){
                $user_id = $this->User->getAgents( $parent_id, true );    
            }else{
                $temp_id = array();
                $temp_id[$user_id] = $user_id;

                $user_id = $temp_id;
            }
            
            $user_id[$parent_id] = $parent_id;

            $options['group'] = 'Message.cnt_group';
            $options['conditions']['Message.to_id'] = $user_id;
        }

        $this->virtualFields['max_id'] = 'MAX(Message.id)';

        $dataOptions = $options;
        $dataOptions['limit'] = 5;

        $data = $this->getData('all', $dataOptions);
        unset($this->virtualFields['max_id']);

        $data = $this->getDataList($data);

        $cntOptions = $options;
        $cntOptions['conditions']['Message.read'] = 0;
        $cnt = $this->getData('count', $cntOptions, array(
            'mine' => true,
        ));

        return array(
            'cnt' => $cnt,
            'data' => $data,
        );
    }

    function getTotalHotleadReport( $property_id = false, $filter_by = 'city_id', $fromDate = false, $toDate = false ) {

        $result = array();
        $default_options = array(
            'conditions' => array(),
            'fields' => array(),
            'group' => array(),
        );
        if( !empty($property_id) ) {
            $default_options['conditions'] = array_merge($default_options['conditions'], array(
                'Message.property_id' => $property_id,
            ));
        }

        if( !empty($filter_by) ) {
            $this->virtualFields['cnt'] = 'COUNT(Message.id)';
            $this->virtualFields[$filter_by] = 'Message.'.$filter_by;
            $default_options['group'] = array(
                'Message.'.$filter_by,
            );
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( !empty($fromDate) && !empty($toDate) ) {
            $conditionsDate = array(
                'DATE_FORMAT(Message.created, \'%Y-%m-%d\') >=' => $fromDate,
                'DATE_FORMAT(Message.created, \'%Y-%m-%d\') <=' => $toDate,
            );
            $default_options['conditions'] = array_merge($default_options['conditions'], $conditionsDate);
        }

        $result = $this->getData('all', $default_options);
        return $result;
    }

    function getTotalHotlead( $property_id = false, $fromDate = false, $toDate = false, $options = false, $filter_per_property = false, $type = 'all' ) {
        
        $this->virtualFields['cnt'] = 'COUNT(Message.property_id)';
        $this->virtualFields['created'] = 'DATE_FORMAT(Message.created, \'%Y-%m-%d\')';
        
        $values = array();
        $default_options = array(
            'conditions' => array(),
            'group' => array(
                'DATE_FORMAT(Message.created, \'%Y-%m-%d\')',
            ),
            'contain' => array(
                'Property',
            ),
            'order' => false,
        );

        if( !empty($property_id) ) {
            $default_options['conditions'] = array(
                'Message.property_id' => $property_id,
            );
        }
        if( !empty($filter_per_property) ) {
            $default_options['group'] = array_merge($default_options['group'], array(
                'Message.property_id',
            ));
        }
        if( !empty($fromDate) && !empty($toDate) ) {
            $conditionsDate = array(
                'DATE_FORMAT(Message.created, \'%Y-%m-%d\') >=' => $fromDate,
                'DATE_FORMAT(Message.created, \'%Y-%m-%d\') <=' => $toDate,
            );
            $default_options['conditions'] = array_merge($default_options['conditions'], $conditionsDate);
        }

        if( !empty($options) ) {
            if( isset($options['conditions']) ) {
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if( isset($options['contain']) ) {
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
                $default_options['contain'] = array_unique($default_options['contain']);
            }
        }

        if( $type == 'all' ) {
            $values = $this->getData('all', $default_options);
        }

        $total = $this->getData('count', $default_options);

        return array(
            'data' => !empty($values)?$values:false,
            'total' => $total,
        );
    }

    public function doSend( $data, $value = false, $validate = false, $template_email = 'message_property' ) {
        $result = false;
        $default_msg = __('mengirim pesan');
        $ccTo = $this->User->getEmailAdmin();
        $is_rest = Configure::read('__Site.is_rest');

        if ( !empty($data['data']) ) {
            $data = $data['data'];
            $dataUser = !empty($data['Register'])?$data['Register']:false;
            $securityCode = !empty($data['Message']['security_code'])?$data['Message']['security_code']:false;
            $to_id = !empty($data['Message']['to_id'])?$data['Message']['to_id']:false;

            if( !empty($to_id) && empty($value) ) {
                $value = $this->User->getMerge(array(), $to_id);
            }

            $to_name = !empty($value['User']['full_name'])?$value['User']['full_name']:false;
            $to_email = !empty($value['User']['email'])?$value['User']['email']:false;
            $ccTo[] = $to_email;

            if($is_rest){
                $this->removeValidate();
            }

            $this->create();
            $this->set($data);
            $flagMsg = $this->validates();

            $flagUser = $this->User->doMessageRegister($dataUser, false, true);
            $first_name = !empty($dataUser['User']['first_name'])?$dataUser['User']['first_name']:false;
            $last_name = !empty($dataUser['User']['last_name'])?$dataUser['User']['last_name']:false;

            if(in_array(true, array($first_name, $last_name))){
                $from_name = trim(sprintf('%s %s', $first_name, $last_name));
            }else{
                $from_name = $this->filterEmptyField($data, 'Message', 'name');
            }
            $from_email = !empty($dataUser['User']['email'])?$dataUser['User']['email']:false;

            if(!$from_email){
                $from_email = $this->filterEmptyField($data, 'Message', 'email');
            }

            if ( !empty($flagMsg) && !empty($flagUser) && !empty($securityCode) ) {
                if( empty($validate) ) {
                    $flagMsg = $this->save();
                }

                if( $flagMsg ) {
                    $id = $this->id;
                    $data['Message']['id'] = $id;

                    if( empty($validate) && !empty($dataUser) ) {
                        $this->User->doMessageRegister($dataUser, $id);
                        $IDmessage = $this->_getIDMessage($id);
                        $fromID = $this->filterEmptyField($IDmessage, 'Message', 'from_id');
                        
                        $data['Message']['from_id'] = $fromID;
                    }
                    if( !empty($value) ) {
                        $data = array_merge($data, $value);
                    }

                    $msg = sprintf(__('Berhasil %s'), $default_msg);
                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $msg,
                            'document_id' => $id,
                        ),
                        'id' => $id,
                    );

                    if(!Configure::read('__Site.is_rest')){
                        $full_base_url = preg_replace('#^https?://#', '', FULL_BASE_URL);

                        $result['SendEmail'] = array(
                            'to_name' => $to_name,
                            'to_email' => $to_email,
                            'subject' => sprintf(__('Anda mendapatkan pesan dari %s di %s'),$from_name, $full_base_url),
                            'template' => $template_email,
                            'data' => $data,
                        );
                    }
                } else {
                    $msg = sprintf(__('Gagal %s'), $default_msg);
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                        'data' => $data,
                        'Log' => array(
                            'activity' => $msg,
                            'error' => 1,
                        ),
                    );
                }
            } else {
                if( empty($flagMsg) || empty($flagUser) ) {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                        'data' => $data,
                    );
                } else if( empty($securityCode) ) {
                    $result = array(
                    //	'msg' => __('Mohon centang untuk menandakan Anda bukan robot'),
                    	'msg' => __('Sistem kami mendeteksi Anda sebagai Bot. Coba muat ulang halaman atau gunakan Browser lain.'), 
                        'status' => 'error',
                        'data' => $data,
                    );
                }
            }
        } else if( !empty($data) ) {
            $result = $data;
        }

        return $result;
    }

    public function doSendMany( $data, $adminId = false ) {
        $result = false;
        $default_msg = __('Terima kasih telah menghubungi, Kami akan segera merespon pesan Anda');

        if ( !empty($data['data']) ) {
            $flagMsg = true;
            $data = !empty($data['data'])?$data['data']:false;
            $dataUser = !empty($data['Register'])?$data['Register']:false;
            $securityCode = !empty($data['SecurityCode'])?$data['SecurityCode']:false;
            $dataMsg = !empty($data['Message'])?$data['Message']:false;

            $flagUser = $this->User->doMessageRegister($dataUser, false, true);
            $contact_email = Configure::read('Config.Company.data.UserCompany.contact_email');

            $emailList = $this->User->getData('list', array(
                'conditions' => array(
                    'User.id' => $adminId,
                ),
                'fields' => array(
                    'User.id', 'User.email',
                ),
            ), array(
                'status' => 'semi-active',
            ));
            $emailList = !empty($emailList)?$emailList:array();

            if( !empty($contact_email) ) {
                $emailList = array_values($emailList);
                $emailList = array_merge($emailList, array(
                    $contact_email,
                ));
            }

            $mainData = !empty($data['Data'])?$data['Data']:false;
            $from_name = !empty($mainData['Message']['name'])?$mainData['Message']['name']:false;

            if( !empty($dataMsg) ) {
			//	handler multiple tapi data yang dikirim bisa single? maen foreach aja -_-
			//	di tambahin ini biar format multipe terus

				$isMultiple = Hash::numeric(array_keys($dataMsg));

				if(empty($isMultiple)){
					$dataMsg = array(array('Message' => $dataMsg));
				}

				foreach($dataMsg as $key => $value){
					if($flagMsg){
						$dataResult = $this->doSend(array(
							'data' => array_merge($value, array(
								'SecurityCode' => true, 
							)), 
						), false, true);

						$dataStatus	= Common::hashEmptyField($dataResult, 'status');
						$flagMsg	= $dataStatus != 'error';
					}
				}
            }

            if ( !empty($flagMsg) && !empty($flagUser) && !empty($securityCode) ) {
                $msgId = array();

                if( !empty($dataMsg) ) {
                    foreach ($dataMsg as $key => $value) {
                        $dataResult = $this->doSend(array(
							'data' => array_merge($value, array(
								'SecurityCode' => true, 
							)), 
						));

                        $msgId[] = Common::hashEmptyField($dataResult, 'id');
                    }

                    $msgId = array_filter($msgId);
                }

                $this->User->doMessageRegister($dataUser, $msgId);

                $result = array(
                    'msg' => $default_msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $default_msg,
                    ),
                    'SendEmail' => array(
                        'to_email' => $emailList,
                        'subject' => sprintf(__('Anda mendapatkan pesan dari %s'), $from_name),
                        'template' => 'message',
                        'data' => $mainData,
                    ),
                );
            } else {
                $data = !empty($data['Message'][0])?$data['Message'][0]:false;

                if( empty($flagMsg) || empty($flagUser) ) {
                    $result = array(
                        'msg' => __('Gagal mengirim pesan, silakan coba kembali'),
                        'status' => 'error',
                        'data' => $data,
                    );
                } else if( empty($securityCode) ) {
                    $result = array(
                        'msg' => __('Mohon centang untuk menandakan Anda bukan robot'),
                        'status' => 'error',
                        'data' => $data,
                    );
                }
            }
        }

        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false, $modelName = 'Message' ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));
        $dateFrom = $this->filterEmptyField($data, 'named', 'date_from', false, array(
            'addslashes' => true,
        ));
        $dateTo = $this->filterEmptyField($data, 'named', 'date_to', false, array(
            'addslashes' => true,
        ));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));

        $region = $this->filterEmptyField($data, 'named', 'region', false, array(
            'addslashes' => true,
        ));
        $city = $this->filterEmptyField($data, 'named', 'city', false, array(
            'addslashes' => true,
        ));
        $subareas = $this->filterEmptyField($data, 'named', 'subareas', false, array(
            'addslashes' => true,
        ));


        $type = $this->filterEmptyField($data, 'named', 'type', false, array(
            'addslashes' => true,
        ));
        $beds = $this->filterEmptyField($data, 'named', 'beds', false, array(
            'addslashes' => true,
        ));
        $baths = $this->filterEmptyField($data, 'named', 'baths', false, array(
            'addslashes' => true,
        ));
        $lot_size = $this->filterEmptyField($data, 'named', 'lot_size', false, array(
            'addslashes' => true,
        ));
        $building_size = $this->filterEmptyField($data, 'named', 'building_size', false, array(
            'addslashes' => true,
        ));
        $lot_width = $this->filterEmptyField($data, 'named', 'lot_width', false, array(
            'addslashes' => true,
        ));
        $lot_length = $this->filterEmptyField($data, 'named', 'lot_length', false, array(
            'addslashes' => true,
        ));
        $price = $this->filterEmptyField($data, 'named', 'price', false, array(
            'addslashes' => true,
        ));
        $certificate = $this->filterEmptyField($data, 'named', 'certificate', false, array(
            'addslashes' => true,
        ));
        $condition = $this->filterEmptyField($data, 'named', 'condition', false, array(
            'addslashes' => true,
        ));
        $furnished = $this->filterEmptyField($data, 'named', 'furnished', false, array(
            'addslashes' => true,
        ));
        $property_action = $this->filterEmptyField($data, 'named', 'property_action', false, array(
            'addslashes' => true,
        ));
        $property_direction = $this->filterEmptyField($data, 'named', 'property_direction', false, array(
            'addslashes' => true,
        ));
        $include_me = $this->filterEmptyField($data, 'named', 'include_me', false, array(
            'addslashes' => true,
        ));
        $principle_id = $this->filterEmptyField($data, 'named', 'principle_id', false, array(
            'addslashes' => true,
        ));
        
        if( !empty($keyword) ) {
            $this->Property->virtualFields['find_keyword'] = sprintf(
                'MATCH(
                    Property.title,
                    Property.keyword
                ) AGAINST(\'%s\' IN BOOLEAN MODE)', $keyword
            );

            $default_options['conditions']['OR'] = array(
                'Message.name LIKE' => '%'.$keyword.'%',
                'Message.email LIKE' => '%'.$keyword.'%',
                'Property.mls_id LIKE ' => '%'.$keyword.'%',
                'MATCH(
                    Property.title,
                    Property.keyword
                ) AGAINST(? IN BOOLEAN MODE)' => $keyword,
            );

            $default_options['order'] = array(
                'Property.find_keyword' => 'DESC',
            );
            $default_options['contain'][] = 'Property';
        }

        if( !empty($region) ) {
            $default_options['conditions']['PropertyAddress.region_id'] = $region;
            $default_options['contain'][] = 'PropertyAddress';
        }
        if( !empty($city) ) {
            $default_options['conditions']['PropertyAddress.city_id'] = $city;
            $default_options['contain'][] = 'PropertyAddress';
        }
        if( !empty($subarea) ) {
            $default_options['conditions']['PropertyAddress.subarea_id'] = $subarea;
            $default_options['contain'][] = 'PropertyAddress';
        }
        if( !empty($subareas) ) {
            $subareas = urldecode($subareas);
            $subareas = explode(',', $subareas);
            $default_options['conditions']['PropertyAddress.subarea_id'] = $subareas;
            $default_options['contain'][] = 'PropertyAddress';
        }

        if( !empty($property_action) ) {
            $default_options['conditions']['Property.property_action_id'] = $property_action;
        }
        if( !empty($type) ) {
            $type = urldecode($type);
            $type = explode(',', $type);
            $default_options['conditions']['Property.property_type_id'] = $type;
        }
        if( !empty($beds) ) {
            $this->virtualFields['total_beds'] = 'beds+beds_maid';
            $default_options['conditions']['total_beds >='] = $beds;
            $default_options['conditions']['PropertyType.is_residence'] = 1;

            $default_options['contain'][] = 'PropertyAsset';
            $default_options['contain'][] = 'PropertyType';
        }
        if( !empty($baths) ) {
            $this->virtualFields['total_baths'] = 'baths+baths_maid';
            $default_options['conditions']['total_baths >='] = $baths;
            $default_options['conditions']['PropertyType.is_residence'] = 1;

            $default_options['contain'][] = 'PropertyAsset';
            $default_options['contain'][] = 'PropertyType';
        }
        if( !empty($lot_size) ) {
            $default_options['conditions']['PropertyAsset.lot_size <='] = $lot_size;
            $default_options['conditions']['AND']['OR'][]['PropertyType.is_lot'] = 1;
            $default_options['conditions']['AND']['OR'][]['PropertyType.is_space'] = 1;

            $default_options['contain'][] = 'PropertyAsset';
            $default_options['contain'][] = 'PropertyType';
        }
        if( !empty($building_size) ) {
            $default_options['conditions']['PropertyAsset.building_size <='] = $building_size;
            $default_options['conditions']['AND']['OR'][]['PropertyType.is_building'] = 1;
            $default_options['conditions']['AND']['OR'][]['PropertyType.is_space'] = 1;
            
            $default_options['contain'][] = 'PropertyAsset';
            $default_options['contain'][] = 'PropertyType';
        }
        if( !empty($lot_width) ) {
            $default_options['conditions']['PropertyAsset.lot_width'] = $lot_width;
            $default_options['contain'][] = 'PropertyAsset';
        }
        if( !empty($lot_length) ) {
            $default_options['conditions']['PropertyAsset.lot_length'] = $lot_length;
            $default_options['contain'][] = 'PropertyAsset';
        }

        if( !empty($price) ) {
            $default_options['contain'][] = 'PropertySold';

            $price = explode('-', $price);
            $min_price = !empty($price[0])?$price[0]:false;
            $max_price = !empty($price[1])?$price[1]:false;

            if( !empty($min_price) ) {
                $default_options['conditions']['(CASE WHEN Property.sold = 1 THEN PropertySold.price_sold ELSE Property.price_measure END) >='] = $min_price;
            }
            if( !empty($max_price) ) {
                $default_options['conditions']['(CASE WHEN Property.sold = 1 THEN PropertySold.price_sold ELSE Property.price_measure END) <='] = $max_price;
            }
        }

        if( !empty($certificate) ) {
            $certificates = $this->Property->Certificate->getData('list', array(
                'conditions' => array(
                    'Certificate.slug' => $certificate,
                ),
                'fields' => array(
                    'Certificate.id', 'Certificate.id',
                ),
                'cache' => __('Certificate.Slug.List.%s', $certificate),
            ));

            if( !empty($certificates) ) {
                $default_options['conditions']['Property.certificate_id'] = $certificates;
            } else {
                $default_options['conditions']['Property.certificate_id'] = $certificate;
            }
        }
        
        if( !empty($condition) ) {
            $default_options['conditions']['PropertyAsset.property_condition_id'] = $condition;
            $default_options['contain'][] = 'PropertyAsset';
        }
        if( !empty($furnished) ) {
            $default_options['conditions']['PropertyAsset.furnished'] = $furnished;
            $default_options['contain'][] = 'PropertyAsset';
        }
        if( !empty($property_direction) ) {
            $default_options['conditions']['PropertyAsset.property_direction_id'] = $property_direction;
            $default_options['contain'][] = 'PropertyAsset';
        }

        if( !empty($include_me) ) {
            $user_login_id = Configure::read('User.id');

            $default_options['conditions'][]['OR'] = array(
                'Message.from_id' => $user_login_id,
                'Message.to_id' => $user_login_id,
            );
        }

        if( !empty($principle_id) ) {
            if( !is_array($principle_id) ) {
                $principle_id = explode(',', $principle_id);
            }
            
            $default_options['conditions'][]['OR'] = array(
                'User.parent_id' => $principle_id,
                'ToUser.parent_id' => $principle_id,
                'ToUser.id' => $principle_id
            );
            $default_options['contain'][] = 'User';
            $default_options['contain'][] = 'ToUser';
        }

        if( !empty($default_options['contain']) ) {
            $default_options['contain'] = array_unique($default_options['contain']);
        }

        if( !empty($dateFrom) ) {
            $field = 'created';
            $default_options['conditions']['DATE_FORMAT('.$modelName.'.'.$field.', \'%Y-%m-%d\') >='] = $dateFrom;
            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT('.$modelName.'.'.$field.', \'%Y-%m-%d\') <='] = $dateTo;
            }
        }

        if( !empty($sort) ) {
            switch ($sort) {
                case 'Property.mls_id':
                    $default_options['contain'][] = 'Property';
                    break;
            }
        }

        return $default_options;
    }

    function removeValidate () {
        $this->validator()
        ->remove('to_id')
        ->remove('to_email')
        ->remove('name')
        ->remove('email')
        ->remove('phone')
        ->remove('message');
    }

    function _callPrincipleCount( $principle_id = false, $status = 'active', $params = null ){
        if( !empty($principle_id) ) {
            $date_from = Common::hashEmptyField($params, 'named.date_from');
            $date_to   = Common::hashEmptyField($params, 'named.date_to');

            $default_options = array(
                'conditions' => array(
                    'OR' => array(
                        'User.parent_id' => $principle_id,
                        'ToUser.parent_id' => $principle_id,
                        'ToUser.id' => $principle_id
                    ),
                ),
                'contain' => array(
                    'User',
                    'ToUser',
                ),
                'order' => false,
            );

            if (!empty($date_from) && !empty($date_to)) {
                $default_options['conditions']['DATE_FORMAT(Message.created, \'%Y-%m-%d\') >='] = $date_from;
                $default_options['conditions']['DATE_FORMAT(Message.created, \'%Y-%m-%d\') <='] = $date_to;

            }
            
            $result = $this->getData('count', $default_options, array(
                'status' => $status,
            ));
            // debug($result);die();
        
        } else {
            $result = false;
        }

        return $result;

    }

    function _callAgentCount( $user_id = false, $status = 'active', $params = null ){
        $options = $this->_callRefineParams($params, array(
            'conditions' => array(
                'OR' => array(
                    'Message.to_id' => $user_id,
                    'Message.from_id' => $user_id,
                ),
            ),
            'order' => false,
        ));

        return $this->getData('count', $options, array(
            'status' => $status,
        ));
    }

    function _getIDMessage( $idMessage ) {
        return $this->getData('first', array(
            'conditions' => array(
                'Message.id' => $idMessage,
            ),
        ));
    }
}
?>