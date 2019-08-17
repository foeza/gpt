<?php
App::uses('AppModel', 'Model');
class UserClientSosmedReference extends AppModel {
	public $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama sosmed',
			),
			'validateNameSosmed' => array(
				'rule' => array('validateNameSosmed'),
				'message' => 'Nama sosmed sudah ada.',
			),
		),

		'url' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan url sosmed',
			),
			'validateURL' => array(
				'rule' => array('validateURL'),
				'message' => 'Format url salah',
			),
			'validateURLSosmed' => array(
				'rule' => array('validateURLSosmed'),
				'message' => 'URL sosmed sudah ada.',
			),
		),
	);

	public $hasMany = array(
		'UserClient' => array(
			'className' => 'UserClient',
			'foreignKey' => 'client_ref_sosmed_id',
		),
	);

	function validateNameSosmed () {
		$result = true;
    	$parent_id = Configure::read('Principle.id');
		$name = Common::hashEmptyField($this->data, 'UserClientSosmedReference.name');
		$id_data = Common::hashEmptyField($this->data, 'UserClientSosmedReference.id');

		if( !empty($name) ) {
			$nameExist = $this->getData('first', array(
				'conditions' => array(
					'UserClientSosmedReference.name' => $name,
					'UserClientSosmedReference.principle_id' => $parent_id,
				),
			), array(
				'status' => 'all'
			));
			
			$sosmed_exist_name = Common::hashEmptyField($nameExist, 'UserClientSosmedReference.name');
			
			if (!empty($id_data)) {
				$result = true; 
			} else {
				if ($name == $sosmed_exist_name) {
					$result = false;
				}
			}
		}

		return $result;
	}

	function validateURLSosmed () {
		$result = true;
    	$parent_id = Configure::read('Principle.id');
		$url = Common::hashEmptyField($this->data, 'UserClientSosmedReference.url');
		$id_data = Common::hashEmptyField($this->data, 'UserClientSosmedReference.id');

		if( !empty($url) ) {
			$urlExist = $this->getData('first', array(
				'conditions' => array(
					'UserClientSosmedReference.url' => $url,
					'UserClientSosmedReference.principle_id' => $parent_id,
				),
			), array(
				'status' => 'all'
			));
			
			$sosmed_exist_url = Common::hashEmptyField($urlExist, 'UserClientSosmedReference.url');
			
			if (!empty($id_data)) {
				$result = true;
			} else {
				if( $url == $sosmed_exist_url ) {
					$result = false;
				}
			}
		}

		return $result;
	}
	
	function getData($find = 'all', $options = array(), $elements = array()) {
    	$parent_id = Configure::read('Principle.id');
		
		$company = Common::hashEmptyField($elements, 'company', true);
		$status = Common::hashEmptyField($elements, 'status', 'active');

		$default_options = array(
			'conditions' => array(
				'UserClientSosmedReference.status' => 1,
			),
			'contain' => array(),
			'order' => array(
				'UserClientSosmedReference.created' => 'DESC',
				'UserClientSosmedReference.id' => 'DESC',
			),
		);

		if(!empty($company)){
			$default_options['conditions']['UserClientSosmedReference.principle_id'] = $parent_id;
		}

		if( !empty($status) ) {
			switch ($status) {
				case 'inactive':
					$default_options['conditions']['UserClientSosmedReference.active'] = 0;
					break;
				case 'active':
					$default_options['conditions']['UserClientSosmedReference.active'] = 1;
					break;
			}
		}

        return $this->merge_options($default_options, $options, $find);
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$name = Common::hashEmptyField($data, 'named.name', false, array(
        	'addslashes' => true,
    	));
    	$url_sosmed = Common::hashEmptyField($data, 'named.url_sosmed', false, array(
        	'addslashes' => true,
    	));
    	$status = Common::hashEmptyField($data, 'named.status', false, array(
        	'addslashes' => true,
    	));
    	$modified_from = Common::hashEmptyField($data, 'named.modified_from', false, array(
            'addslashes' => true,
        ));
        $modified_to = Common::hashEmptyField($data, 'named.modified_to', false, array(
            'addslashes' => true,
        ));
    	$date_from = Common::hashEmptyField($data, 'named.date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = Common::hashEmptyField($data, 'named.date_to', false, array(
            'addslashes' => true,
        ));
        $sort = Common::hashEmptyField($data, 'named.sort', false, array(
            'addslashes' => true,
        ));
        $keyword = Common::hashEmptyField($data, 'named.keyword', false, array(
            'addslashes' => true,
        ));
    	
    	if( !empty($name) ) {
			$default_options['conditions']['UserClientSosmedReference.name LIKE'] = '%'.$name.'%';
		}
		if( !empty($url_sosmed) ) {
			$default_options['conditions']['UserClientSosmedReference.url LIKE'] = '%'.$url_sosmed.'%';
		}
		if( !empty($keyword) ) {
			$default_options['conditions']['UserClientSosmedReference.name LIKE'] = '%'.$keyword.'%';
		}
		if( !empty($status) ) {
			switch ($status) {
				case 'inactive':
					$default_options['conditions']['UserClientSosmedReference.active'] = 0;
					break;
				case 'active':
					$default_options['conditions']['UserClientSosmedReference.active'] = 1;
					break;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(UserClientSosmedReference.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(UserClientSosmedReference.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		if( !empty($date_from) ) {
        	$default_options['conditions']["DATE_FORMAT(UserClientSosmedReference.created, '%Y-%m-%d') >="] = $date_from;
	        
			if( !empty($date_to) ) {
	        	$default_options['conditions']["DATE_FORMAT(UserClientSosmedReference.created, '%Y-%m-%d') <="] = $date_to;
	        }
        }

		return $default_options;
	}

	function popupAddNew( $data ) {
		$result = false;

		if( !empty($data) ) {
    		$parent_id = Configure::read('Principle.id');
			$data['UserClientSosmedReference']['principle_id'] = $parent_id;

			$this->create();
			$this->set($data);

			if ( $this->save() ) {
				$id = $this->id;
				$msg = __('Berhasil menambahkan data sosmed');

	            $result = array(
					'msg' => $msg,
					'status' => 'success',
					'id' => $id,
					'Log' => array(
						'activity' => $msg,
					),
				);
			} else {
				$msg = __('Gagal. menambahkan data sosmed');
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'validationErrors' => $this->validationErrors,
					'Log' => array(
						'activity' => $msg,
					),
				);
			}
		}

		return $result;
	}

	public function doSave($data, $id = false ){
		$result = false;

		if ( !empty($data) ) {
            $flag = $this->saveAll($data);

			if( $flag ) {
				$msg = __('Berhasil menyimpan sosmed');
				$id = $this->id;

				$result = array(
					'msg' => $msg,
					'status' => 'success',
					'id' => $id,
					'Log' => array(
						'activity' => $msg,
						'document_id' => $id,
					),
				);
			}else{
				$msg = __('Gagal menyimpan sosmed. Mohon masukkan data-data yang dibutuhkan');
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'data' => $data,
				);
			}
		}

		return $result;
	}

	function doToggle( $id, $active ) {
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'UserClientSosmedReference.id' => $id,
			),
		);

		$value = $this->getData('all', $options, array(
			'status' => 'all',
		));

		if ( !empty($value) ) {
			if( !empty($active) ) {
				$default_msg = __('mengaktifkan sosmed');
			} else {
				$default_msg = __('nonaktifkan sosmed');
			}

			$flag = $this->updateAll(array(
				'UserClientSosmedReference.active' => $active,
			), array(
				'UserClientSosmedReference.id' => $id,
			));

            if( $flag ) {
				$msg = __('Berhasil %s', $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $id,
					),
				);
            } else {
				$result = array(
					'msg' => __('Gagal %s', $default_msg),
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal %s. Data tidak ditemukan', $default_msg),
				'status' => 'error',
			);
		}

		return $result;
	}

	function doDelete( $id ) {
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'UserClientSosmedReference.id' => $id,
			),
		);

		$value = $this->getData('all', $options, array(
			'status' => 'all',
		));

		if ( !empty($value) ) {
			$default_msg = __('menghapus sosmed');

			$flag = $this->updateAll(array(
				'UserClientSosmedReference.status' => 0,
			), array(
				'UserClientSosmedReference.id' => $id,
			));

            if( $flag ) {
				$msg = __('Berhasil %s', $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $id,
					),
				);
            } else {
				$result = array(
					'msg' => __('Gagal %s', $default_msg),
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal %s. Data tidak ditemukan', $default_msg),
				'status' => 'error',
			);
		}

		return $result;
	}

}
