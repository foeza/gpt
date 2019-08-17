<?php
class MailchimpListDetail extends AppModel{
	var $name = 'MailchimpListDetail';

	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama Anda.',
			),
		),
		'email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan email Anda.',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email yang Anda masukkan salah.',
			)
		),
	);

	function getData( $find = 'all', $options = array() ) {
		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'fields'=> array(),
            'limit'=> array(),
		);

		if( !empty($options) ){
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

		}

		if( $find == 'conditions' && !empty($default_options['conditions']) ) {
			$result = $default_options['conditions'];
		} else if( $find == 'paginate' ) {
			if( empty($default_options['limit']) ) {
				$default_options['limit'] = Configure::read('__Site.config_admin_pagination');
			}
			
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
		
        return $result;
	}

	function doSave($data, $value = array(), $id = false, $validation = false){
		$result = false;

		if(!empty($data)){
			$text = 'menambah';
			if(empty($validation)){
				if(!empty($id)){
					$this->id = $id;

					$text = 'mengubah';
				}else{
					$this->create();
				}
			}

			$import = false;

			if(!empty($data['import_data'])){
				$import = true;
				$data = $data['import_data'];
			}

			$this->set($data);

			$validate = true;
			if(!empty($validation)){
				$validate = $this->validates();
			}

			if($validate){
				if(empty($validation) || $import){
					$msg = sprintf(__('Berhasil %s detail grup email'), $text);
					if($import){
						if($this->saveMany($data)){
							$msg = sprintf('%s, dengan import excel', $msg);
							$result = array(
								'msg' => $msg,
								'status' => 'success',
								'Log' => array(
									'activity' => $msg,
									'old_data' => $data,
									// 'document_id' => sprintf('%s', $msg),
								)
							);
						}else{
							$msg = sprintf(__('Gagal %s detail grup email'), $text);
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
					}else if($this->save()){
						$result = array(
							'msg' => $msg,
							'status' => 'success',
							'Log' => array(
								'activity' => $msg,
								'old_data' => $data,
								// 'document_id' => sprintf('%s ID #%s', $msg, $this->id),
							)
						);
					}else{
						$msg = sprintf(__('Gagal %s detail grup email'), $text);

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
			}else{
				$msg = sprintf(__('Gagal %s detail grup email'), $text);

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
		}else if(!empty($value)){
			$result['data'] = $value;
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
		$email = $this->filterEmptyField($data, 'named', 'email', false, array(
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
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        
        if( !empty($keyword) ) {
            $default_options['conditions']['OR'] = array(
                'MailchimpListDetail.name LIKE ' => '%'.$keyword.'%',
                'MailchimpListDetail.email LIKE ' => '%'.$keyword.'%',
            );
        }

		if( !empty($name) ) {
			$default_options['conditions']['MailchimpListDetail.name LIKE'] = '%'.$name.'%';
		}
		if( !empty($email) ) {
			$default_options['conditions']['MailchimpListDetail.email LIKE'] = '%'.$email.'%';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(MailchimpListDetail.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(MailchimpListDetail.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(MailchimpListDetail.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(MailchimpListDetail.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}

        if( !empty($sort) ) {
	        $direction = $this->filterEmptyField($data, 'named', 'direction', false, array(
	            'addslashes' => true,
	        ));

			$default_options['order'] = array(
                $sort => $direction,
            );
		}

        return $default_options;
    }

    function doDelete( $id ) {
			$result = false;
			$grup = $this->getData('all', array(
	        	'conditions' => array(
					'MailchimpListDetail.id' => $id,
				),
			));

			if ( !empty($grup) ) {
				$id = Set::extract('/MailchimpListDetail/id', $grup);
				
				$default_msg = __('menghapus detail grup email');

				$flag = $this->updateAll(array(
					'MailchimpListDetail.status' => 0,
				), array(
					'MailchimpListDetail.id' => $id,
				));

				$id = implode(', ', $id);

	            if( $flag ) {
					$msg = sprintf(__('Berhasil %s dengan ID %s'), $default_msg, $id);
	                $result = array(
						'msg' => sprintf(__('Berhasil %s'), $default_msg),
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $grup,
						),
					);
	            } else {
					$msg = sprintf(__('Gagal %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'error',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $grup,
							'error' => 1,
						),
					);
				}
			} else {
				$result = array(
					'msg' => __('Gagal menghapus detail grup email. Data tidak ditemukan'),
					'status' => 'error',
				);
			}

			return $result;
		}
}
?>