<?php
class UserClientRelation extends AppModel {
	var $name = 'UserClientRelation';

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'agent_id',
		),
		'UserClient' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Client' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Company' => array(
			'className' => 'User',
			'foreignKey' => 'company_id',
		),
		'UserClient' => array(
			'className' => 'UserClient',
			'foreignKey' => false,
			'conditions' => array(
				'UserClient.user_id = UserClientRelation.user_id',
				'UserClient.company_id = UserClientRelation.company_id',
			)
		),
	);

	public function getData( $find = 'all', $options = array(), $elements = array()  ) {
		$status = isset($elements['status']) ? $elements['status']:'active';
		$company = !empty($elements['company']) ? $elements['company']:true;
        $adminRumahku = isset($elements['adminRumahku'])?$elements['adminRumahku']:Configure::read('User.Admin.Rumahku');

		$default_options = array(
			'conditions'=> array(
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
				'UserClientRelation.id'=>'DESC',
			),
		);

        if( !empty($company) && empty($adminRumahku) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['UserClientRelation.company_id'] = $parent_id;
        }

        return $this->merge_options($default_options, $options, $find);
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));
        $client_type = $this->filterEmptyField($data, 'named', 'client_type', false, array(
        	'addslashes' => true,
    	));
        $name = $this->filterEmptyField($data, 'named', 'name', false, array(
        	'addslashes' => true,
    	));
		$email = $this->filterEmptyField($data, 'named', 'email', false, array(
        	'addslashes' => true,
    	));
		$phone_profile = $this->filterEmptyField($data, 'named', 'phone_profile', false, array(
        	'addslashes' => true,
    	));
		$no_hp = $this->filterEmptyField($data, 'named', 'no_hp', false, array(
        	'addslashes' => true,
    	));
		$no_hp_2 = $this->filterEmptyField($data, 'named', 'no_hp_2', false, array(
        	'addslashes' => true,
    	));
		$pin_bb = $this->filterEmptyField($data, 'named', 'pin_bb', false, array(
        	'addslashes' => true,
    	));
		$line = $this->filterEmptyField($data, 'named', 'line', false, array(
        	'addslashes' => true,
    	));
		$gender = $this->filterEmptyField($data, 'named', 'gender', false, array(
        	'addslashes' => true,
    	));
    	$date_from = $this->filterEmptyField($data, 'named', 'date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = $this->filterEmptyField($data, 'named', 'date_to', false, array(
            'addslashes' => true,
        ));
		
		if( !empty($keyword) ) {
			$this->unbindModel(array(
				'belongsTo'	=> array(
					'UserClient'
				), 
			));
			$this->bindModel(array(
                'hasOne' => array(
                    'UserClient' => array(
                        'foreignKey' => false,
                        'conditions' => array(
                            'UserClient.user_id = User.id',
                            'UserClient.company_id' => Configure::read('Principle.id'),
                            'UserClient.status' => 1,
                        ),
                    ),
                    'ClientType' => array(
                        'foreignKey' => false,
                        'conditions' => array(
                            'ClientType.id = UserClient.client_type_id',
                        ),
                    ),
                )
            ), false);

			$default_options['conditions']['OR'] = array(
				'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE' => '%'.$keyword.'%',
				'User.email LIKE' => '%'.$keyword.'%',
				'ClientType.name LIKE' => '%'.$keyword.'%',
			);
			$default_options['contain'][] = 'UserClient';
			$default_options['contain'][] = 'ClientType';
		}
		if( !empty($client_type) ) {
			$default_options['conditions']['UserClient.client_type_id'] = $client_type;
			$default_options['contain'][] = 'UserClient';
		}
		if( !empty($name) ) {
			$name = trim($name);
			$default_options['conditions']['CONCAT(UserClient.first_name,\' \',IFNULL(UserClient.last_name, \'\')) LIKE'] = '%'.$name.'%';
			$default_options['contain'][] = 'UserClient';
		}
		if( !empty($email) ) {
			$email = trim($email);
			$default_options['conditions']['User.email LIKE'] = '%'.$email.'%';
			$default_options['contain'][] = 'User';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(UserClient.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(UserClient.created, \'%Y-%m-%d\') <='] = $date_to;
			}
			$default_options['contain'][] = 'UserClient';
		}
		if( !empty($phone_profile) ) {
			$default_options['conditions']['UserClient.phone LIKE'] = '%'.$phone_profile.'%';
			$default_options['contain'][] = 'UserClient';
		}
		if( !empty($no_hp) ) {
			$default_options['conditions']['OR']['UserClient.no_hp LIKE'] = '%'.$no_hp.'%';
			$default_options['conditions']['OR']['UserProfile.no_hp LIKE'] = '%'.$no_hp.'%';
			$default_options['contain'][] = 'UserClient';
			$default_options['contain'][] = 'UserProfile';

			$this->bindModel(array(
                'belongsTo' => array(
                    'UserProfile' => array(
                        'foreignKey' => false,
                        'conditions' => array(
                            'UserClientRelation.agent_id = UserProfile.user_id'
                        ),
                    ),
                )
            ), false);
		}
		if( !empty($no_hp_2) ) {
			$default_options['conditions']['UserClient.no_hp_2 LIKE'] = '%'.$no_hp_2.'%';
			$default_options['contain'][] = 'UserClient';
		}
		if( !empty($pin_bb) ) {
			$default_options['conditions']['UserClient.pin_bb LIKE'] = '%'.$pin_bb.'%';
			$default_options['contain'][] = 'UserClient';
		}
		if( !empty($line) ) {
			$default_options['conditions']['UserClient.line LIKE'] = '%'.$line.'%';
			$default_options['contain'][] = 'UserClient';
		}
		if( !empty($gender) ) {
			$default_options['conditions']['User.gender_id'] = $gender;
			$default_options['contain'][] = 'User';
		}


        if( !empty($sort) ) {
	        $default_options['order'][$sort] = $direction;
        	$userClient = strpos($sort, 'UserClient.');
        	$user = strpos($sort, 'User.');

        	if( is_numeric($userClient) ) {
	            $default_options['contain'][] = 'UserClient';
	        } else if( is_numeric($user) ) {
	            $default_options['contain'][] = 'User';
	        }
        }

		return $default_options;
	}

	function doSave( $agent_id, $client_id, $primary = 1 ) {
		$value = $this->getData('first', array(
			'conditions' => array(
				'UserClientRelation.company_id' => Configure::read('Principle.id'),
				'UserClientRelation.user_id' => $client_id,
				'UserClientRelation.agent_id' => $agent_id,
			),
			'fields' => array(
				'UserClientRelation.id',
			),
		));

		if( !empty($value) ) {
			$id = !empty($value['UserClientRelation']['id'])?$value['UserClientRelation']['id']:false;
			$this->id = $id;
		} else {
			$this->create();
		}

		return $this->save(array(
			'UserClientRelation' => array(
				'company_id' => Configure::read('Principle.id'),
				'user_id' => $client_id,
				'agent_id' => $agent_id,
				'primary' => $primary,
			),
		));
	}

	public function doSaveMapping( $client_id = false, $agent_id = false, $current_relation = false, $agent_pic_id = null ) {
		$result = false;
		$default_msg = __('menyimpan relasi klien dengan agen');
		$set_empty = false;

		if( empty($agent_id) ) {
			$set_empty = true;
			$agent_id = $this->getData('all', array(
				'conditions' => array(
					'UserClientRelation.user_id' => $client_id,
				),
			), array(
				'company' => true,
				'adminRumahku' => false,
			));
		}

		$statusDelete = $this->deleteAll(array(
            'UserClientRelation.user_id '=> $client_id,
			'UserClientRelation.company_id' => Configure::read('Principle.id'),
            'UserClientRelation.agent_id <> '=> $agent_pic_id,
        ));

		$status = 'success';
		$error = 0;
		$log_msg = sprintf('Berhasil %s', $default_msg);

		if( !empty($set_empty) ) {
			$explode_agent_id = Set::extract('/UserClientRelation/agent_id', $agent_id);
			$value = implode(",", $explode_agent_id);

			if( !empty($statusDelete) ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
	            $log_msg = sprintf('%s #%s', $log_msg, $value);
			} else {
				$status = 'error';
            	$msg = sprintf(__('Gagal %s'), $default_msg);
            	$log_msg = sprintf('%s, namun gagal menyimpan #%s', $log_msg, $value);
            	$error = 1;
			}
		} else {
			foreach ($agent_id as $key => $value) {
	            $this->create();
	            $this->set('company_id', Configure::read('Principle.id'));
	            $this->set('user_id', $client_id);
	            $this->set('agent_id', $value);

	            if( $this->save() ) {
	            	$msg = sprintf(__('Berhasil %s'), $default_msg);
	            	$log_msg = sprintf('%s #%s', $log_msg, $value);
	            } else {
	            	$status = 'error';
	            	$msg = sprintf(__('Gagal %s'), $default_msg);
	            	$log_msg = sprintf('%s, namun gagal menyimpan #%s', $log_msg, $value);
	            	$error = 1;
	            	break;
	            }
	        }
		}

		$result = array(
			'msg' => $msg,
			'status' => $status,
			'Log' => array(
				'activity' => $log_msg,
				'old_data' => $current_relation,
				'document_id' => $client_id,
				'error' => $error,
			),
		);

		return $result;
	}
}
?>