<?php
class Career extends AppModel {
	var $name = 'Career';
	var $validate = array(
		'user_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'User harap diisi',
			),
		),
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jenis pekerjaan harap diisi',
			),
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Email tujuan harap diisi',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
			),
		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Deskripsi pekerjaan harap diisi',
			),
		)
	);

	var $hasMany = array(
		'CareerRequirement' => array(
			'className' => 'CareerRequirement',
			'foreignKey' => 'career_id',
		),
	);

	function getData( $find = 'all', $options = array() ) {
        $company = isset($elements['company']) ? $elements['company']:true;

		$default_options = array(
			'conditions'=> array(
				'Career.status' => 1, 
			),
			'contain' => array(
				'CareerRequirement' => array(
					'order'=> array(
						'CareerRequirement.id' => 'ASC',
					),
				),
			),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(
				'Career.modified' => 'DESC',
			),
		);

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['Career.user_id'] = $parent_id;
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

		if( $find == 'conditions' && !empty($default_options['conditions']) ) {
			$result = $default_options['conditions'];
		} else if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
		
        return $result;
	}

	public function doSave( $data = false, $career = false, $id = false, $is_api = false ) {
		$result = false;
		$default_msg = __('%s data karir');

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();
				$default_msg = sprintf($default_msg, __('menambah'));
			}

			if(!$is_api){
				$data['Career']['user_id'] = Configure::read('Principle.id');
			}
			
			$data['Career']['name'] = trim($data['Career']['name']);
			$data['Career']['email'] = trim($data['Career']['email']);

			$this->set($data);
			if ( $this->validates() ) {
				if( $this->save() ) {

					$this->doSaveCareerRequirement( $data, $this->id );

					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $career,
							'document_id' => $id,
						),
					);
				} else {
					$msg = sprintf(__('Gagal %s'), $default_msg);
					$result = array(
						'msg' => sprintf(__('Gagal %s'), $default_msg),
						'status' => 'error',
						'data' => $data,
						'Log' => array(
							'activity' => $msg,
							'old_data' => $career,
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
		} else if( !empty($career) ) {
			$career['CareerRequirement']['name'] = Set::extract('/CareerRequirement/name', $career);
			$result['data'] = $career;
		}

		return $result;
	}

	function doDelete( $id ) {
		
		$result = false;
		$career = $this->getData('all', array(
        	'conditions' => array(
				'Career.id' => $id,
			),
		));

		if ( !empty($career) ) {
			$name = Set::extract('/Career/name', $career);
			$name = implode(', ', $name);
			$default_msg = sprintf(__('menghapus karir %s'), $name);

			$flag = $this->updateAll(array(
				'Career.status' => 0,
	    		'Career.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'Career.id' => $id,
			));

            if( $flag ) {

            	$this->CareerRequirement->updateAll(array(
					'CareerRequirement.status' => 0,
	    			'CareerRequirement.modified' => "'".date('Y-m-d H:i:s')."'",
				), array(
					'CareerRequirement.career_id' => $id,
				));

				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $career,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $career,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus karir. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	function doSaveCareerRequirement( $data, $career_id ) {

		if( !empty($data['CareerRequirement']['name']) ) {
            $data['CareerRequirement']['name'] = array_filter($data['CareerRequirement']['name']);

            $this->CareerRequirement->deleteAll(array(
	            'CareerRequirement.career_id '=> $career_id,
	        ));

	        foreach ($data['CareerRequirement']['name'] as $key => $requirement) {
	            $this->CareerRequirement->create();
	            $this->CareerRequirement->set('name', $requirement);
	            $this->CareerRequirement->set('career_id', $career_id);
	            $this->CareerRequirement->save();
	        }
        }
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));
        $name = $this->filterEmptyField($data, 'named', 'name', false, array(
            'addslashes' => true,
        ));
        $email = $this->filterEmptyField($data, 'named', 'email', false, array(
            'addslashes' => true,
        ));
        $description = $this->filterEmptyField($data, 'named', 'description', false, array(
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

		if( !empty($keyword) ) {
			$default_options['conditions']['OR'] = array(
				'Career.name LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($name) ) {
			$default_options['conditions']['Career.name LIKE'] = '%'.$name.'%';
		}
		if( !empty($email) ) {
			$default_options['conditions']['Career.email LIKE'] = '%'.$email.'%';
		}
		if( !empty($description) ) {
			$default_options['conditions']['Career.description LIKE'] = '%'.$description.'%';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(Career.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(Career.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(Career.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(Career.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		
		return $default_options;
	}
}
?>