<?php
class Rule extends AppModel {
	var $name = 'Rule';
	var $validate = array(
		'root_category_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Harap pilih kategori rule',
			),
		),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Judul harap diisi',
			),
		),
		'short_desc' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Keterangan singkat harap diisi',
			),
		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Deskripsi harap diisi',
			),
		),
		'order' => array(
			'numeric'		=> array(
				'rule'		=> array('numeric'),
				'message'	=> 'Inputan harus berupa angka',
	            'allowEmpty' => true,
			),
			'greaterThan'	=> array(
				'rule'		=> array('isNumber', 'order'),
				'message'	=> 'Nomor harus lebih besar dari 0',
			),
		),
	);

	var $belongsTo = array(
		'RuleCategory' => array(
			'className' => 'RuleCategory',
			'foreignKey' => 'rule_category_id',
		),
		'UserCompany' => array(
			'foreignKey' => 'company_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	public function getData( $find = 'all', $options = array(), $elements = array()  ) {
		$status  = isset($elements['status']) ? $elements['status']:'active';
		$company = isset($elements['company']) ? $elements['company']:true;
		$admin   = !empty($elements['admin']) ? $elements['admin']:false;

		$default_options = array(
			'conditions'=> array(
				'Rule.status' => 1,
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
				'Rule.modified'=>'DESC',
				'Rule.id'=>'DESC',
			),
		);

        switch ($status) {
            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'Rule.active' => 0,
            	));
                break;
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'Rule.active' => 1,
            	));
                break;
            case 'deleted':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'Rule.status' => 0,
            	));
                break;
        }

        if($admin){
        	$isAdmin = Configure::read('User.admin');
        	if($isAdmin){
        		$default_options['conditions']['Rule.active'] = array(0,1);
        	}
        }

        if( !empty($company) ) {
            $company_data = Configure::read('Config.Company.data');
			$company_id	  = Common::hashEmptyField($company_data, 'UserCompany.id', 0);

            $default_options['conditions']['Rule.company_id'] = $company_id;
        }

		if( !empty($options) ) {
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
	        if(!empty($options['cache'])){
	            $default_options['cache'] = $options['cache'];
	                
	            if(!empty($options['cacheConfig'])){
	                $default_options['cacheConfig'] = $options['cacheConfig'];
	            }
	        }
        }

		if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
	}

	public function doSave( $data, $rules = false, $id = false ) {
		$result = false;

		$default_msg  = __('%s data rules');
		$company_data = Configure::read('Config.Company.data');
		$company_id	  = Common::hashEmptyField($company_data, 'UserCompany.id', 0);
		$user_id      = Configure::read('User.data.id');

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();
				$default_msg = sprintf($default_msg, __('menambah'));
				
			}

			$data['Rule']['company_id'] = $company_id;
			$data['Rule']['user_id'] 	= $user_id;

			$rule_category_id = Common::hashEmptyField($data, 'Rule.rule_category_id');
			$root_category_id = Common::hashEmptyField($data, 'Rule.root_category_id');
			if (empty($rule_category_id)) {
				$data['Rule']['rule_category_id'] 	= $root_category_id;
			}

			$rule_name = $data['Rule']['name'];
			// debug($data);die();
			$this->set($data);

			if ( $this->validates() ) {
				if( $this->save() ) {
					$msg = sprintf(__('Berhasil %s, %s'), $default_msg, $rule_name);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $rules,
							'document_id' => $id,
						),
					);

					// first create or add new
					if (empty($id)) {
						$result = $this->_sendEmailToUser($result, $this->id);
					}

				} else {
					$msg = sprintf(__('Gagal %s'), $default_msg);
					$result = array(
						'msg' => sprintf(__('Gagal %s'), $default_msg),
						'status' => 'error',
						'data' => $data,
						'Log' => array(
							'activity' => $msg,
							'old_data' => $rules,
							'document_id' => $id,
							'error' => 1,
						),
					);
				}
			} else {
				$result = array(
					'msg' => sprintf(__('Gagal %s'), $default_msg),
					'status' => 'error',
					'data' => $data,
				);
			}
		} else if( !empty($rules) ) {
			$result['data'] = $rules;
		}

		return $result;
	}

	function doDelete( $id ) {
		$result = false;
		$rule = $this->getData('all', array(
        	'conditions' => array(
				'Rule.id' => $id,
			),
		), array(
			'status' => 'all'
		));
// debug($rule);die();
		if ( !empty($rule) ) {
			$title = Set::extract('/Rule/name', $rule);
			$title = implode(', ', $title);
			$default_msg = sprintf(__('menghapus rule %s'), $title);

			$flag = $this->updateAll(array(
				'Rule.status' => 0,
	    		'Rule.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'Rule.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $rule,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $rule,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
    	$name = $this->filterEmptyField($data, 'named', 'name', false, array(
        	'addslashes' => true,
    	));
    	$sub_category = $this->filterEmptyField($data, 'named', 'sub_category', false, array(
        	'addslashes' => true,
    	));
    	$root_category = $this->filterEmptyField($data, 'named', 'root_category', false, array(
        	'addslashes' => true,
    	));
    	$short_desc = $this->filterEmptyField($data, 'named', 'short_desc', false, array(
        	'addslashes' => true,
    	));
    	$date_from = $this->filterEmptyField($data, 'named', 'date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = $this->filterEmptyField($data, 'named', 'date_to', false, array(
            'addslashes' => true,
        ));
    	$modified_from = $this->filterEmptyField($data, 'named', 'modified_from', false, array(
            'addslashes' => true,
        ));
        $modified_to = $this->filterEmptyField($data, 'named', 'modified_to', false, array(
            'addslashes' => true,
        ));
        $order = $this->filterEmptyField($data, 'named', 'order', false, array(
        	'addslashes' => true,
    	));
    	$status = $this->filterEmptyField($data, 'named', 'status', false, array(
        	'addslashes' => true,
    	));
        $filter = $this->filterEmptyField($data, 'named', 'filter', false, array(
            'addslashes' => true,
        ));

		if( !empty($keyword) ) {
			$default_options['conditions']['OR']['Rule.name LIKE'] = '%'.$keyword.'%';
			$default_options['conditions']['OR']['Rule.short_desc LIKE'] = '%'.$keyword.'%';
			$default_options['conditions']['OR']['Rule.description LIKE'] = '%'.$keyword.'%';
		}
		if( !empty($name) ) {
			$default_options['conditions']['Rule.name LIKE'] = '%'.$name.'%';
		}
		if( !empty($sub_category) ) {
			$default_options['conditions']['RuleCategory.name LIKE'] = '%'.$sub_category.'%';
            $default_options['contain']['RuleCategory'] = 'ParentRuleCategory';
		}
		if( !empty($root_category) ) {
			$default_options['conditions']['Rule.root_category_id'] = $root_category;
		}
		if( !empty($short_desc) ) {
			$default_options['conditions']['Rule.short_desc LIKE'] = '%'.$short_desc.'%';
		}
		if(!empty($order)){
			$default_options['conditions']['Rule.order'] = $order;
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(Rule.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(Rule.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(Rule.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(Rule.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}

		if( !empty($status) ) {
			switch ($status) {
				case 'inactive':
					$default_options['conditions']['Rule.active'] = false;
					break;
				case 'active':
					$default_options['conditions']['Rule.active'] = true;
					break;
			}
		}
		if( !empty($filter) ) {
        	$default_options['conditions']['Rule.root_category_id'] = $filter;
        }

		return $default_options;
	}

    function doActived($value, $status){
    	if($value){
    		$id = $this->filterEmptyField($value, 'Rule', 'id');
    		$title = $this->filterEmptyField($value, 'Rule', 'name');

	    	if($status){
	    		$msg = __('mengaktifkan Rule "%s"', $title);
	    	}else{
	    		$msg = __('menonaktifkan Rule "%s"', $title);
	    	}

	    	$this->id = $id;
			$this->set('active',  $status);

			if($this->save()){
				$msg = __('Berhasil %s', $msg);
				$result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'document_id' => $id,
					),
				);
			}else{
				$msg = __('Gagal %s', $msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'error' => 1,
					),
				);
			}
    	}else{
    		$msg = __('Gagal %s', $msg);
			$result = array(
				'msg' => $msg,
				'status' => 'error',
				'Log' => array(
					'activity' => $msg,
					'error' => 1,
				),
			);
    	}
		return $result;
    }

    function _sendEmailToUser($result, $id_rules){
    	$parent_id = Configure::read('Principle.id');
        if(!empty($result) && !empty($id_rules)){
            $data = $this->getData('first', array(
                'conditions' => array(
                    'Rule.id' => $id_rules,
                )
            ));

            // get all email company with the principle also
            $email = $this->User->getData('list', array(
            	'conditions' => array(
        			'OR' => array(
        				array(
		            		'User.parent_id' => $parent_id,
				        ),
				        array(
		            		'User.id' => $parent_id,
				        ),
    				),
    			),
                'fields' => array(
                    'User.id','User.email'
                )
            ));

            $rule_name = Common::hashEmptyField($data, 'Rule.name');

            if(!empty($email)){                

                $notification = array();

                foreach ($email as $id => $value) {

					$link_rule = array(
                        'controller' => 'rules',
                        'action' => 'read_rules',
                        'id_rule' => $id_rules,
                        'admin' => true
                    );

                    // send email 
					// $data['link_rule'] = $link_rule;
     //            	$sendemail[] =  array(
	    //                 'to_name' => 'Seluruh Karyawan',
	    //                 // 'to_email' => $value,
	    //                 'to_email' => 'riscaagent@yopmail.com',
	    //                 'subject' => sprintf(__('Informasi Rule Baru')) ,
	    //                 'template' => 'new_rule_info',
	    //                 'data' => $data,
	    //                 // 'debug' => 'view',
	    //             );

                	// create link to read notification
                    $notification[] = array(
                        'Notification' => array(
                            'user_id' => $id,
                            'name' => __('Hai, Ada peraturan baru, "%s". Silakan dibaca!', $rule_name),
                            'link' => $link_rule,
                        ),
                    );
                }

                // $result['SendEmail'] = $sendemail;
                $result['Notification'] = $notification;
            }
        }
        // debug($result);die();
        return $result;
    }

}
?>