<?php
class Partnership extends AppModel {
	var $name = 'Partnership';
	var $displayField = 'name';
	var $validate = array(
		'photo' => array(
			'imageupload' => array(
	            'rule' => array('extension',array('jpeg','jpg','png','gif')),
	            'required' => false,
	            'allowEmpty' => false,
	            'message' => 'Foto harap diisi dan berekstensi (jpeg, jpg, png, gif)'
	        ),
		),
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama partnership harap diisi',
			),
		)
	);

	function getData( $find = 'all', $options = array(), $is_merge = true ){
		$default_options = array(
			'conditions' => array(
				'Partnership.company_id' => Configure::read('Principle.id'),
				'Partnership.status' => 1,
			), 
			'order' => array(
				'Partnership.created' => 'DESC',
				'Partnership.id' => 'DESC',
			), 
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
		);

		if( $is_merge ){
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
		}else {
			$default_options = $options;
		}

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}
	
	public function doSave( $data, $user_id = false, $partnership = false, $id = false, $is_api = false ) {
		$result = false;
		$default_msg = __('%s data partnership');

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();
				$data['Partnership']['user_id'] = $user_id;
				$default_msg = sprintf($default_msg, __('menambah'));
			}

			$data['Partnership']['title'] 	= !empty($data['Partnership']['title']) ? trim($data['Partnership']['title']) : '';
			$data['Partnership']['url'] 	= !empty($data['Partnership']['url']) ? trim($data['Partnership']['url']) : '';

			if(!$is_api){
				$data['Partnership']['company_id'] = Configure::read('Principle.id');
			}

			$this->set($data);
			if ( $this->validates() ) {
				if( $this->save($data) ) {
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $partnership,
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
							'old_data' => $partnership,
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
		} else if( !empty($partnership) ) {
			$photo = !empty($partnership['Partnership']['photo'])?$partnership['Partnership']['photo']:false;
			$partnership['Partnership']['photo_hide'] = $photo;
			$result['data'] = $partnership;
		}

		return $result;
	}

	function doDelete( $id ) {
		
		$result = false;
		$partnership = $this->getData('all', array(
        	'conditions' => array(
				'Partnership.id' => $id,
			),
		));

		if ( !empty($partnership) ) {
			$title = Set::extract('/Partnership/title', $partnership);
			$title = implode(', ', $title);
			$default_msg = sprintf(__('menghapus partnership %s'), $title);

			$flag = $this->updateAll(array(
				'Partnership.status' => 0,
	    		'Partnership.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'Partnership.id' => $id,
			));

            if( $flag ) {
				$options = array('record_id' => $id);
				$this->afterSave($flag, $options);
				
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $partnership,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $partnership,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus partnership. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
    	$url = $this->filterEmptyField($data, 'named', 'url', false, array(
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
				'Partnership.title LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($url) ) {
			$default_options['conditions']['Partnership.url LIKE'] = '%'.$url.'%';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(Partnership.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(Partnership.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(Partnership.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(Partnership.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		
		return $default_options;
	}
	
	public function afterSave($created, $options = array()){
		$dataCompany = Configure::read('Config.Company.data');
		$company_id = $this->filterEmptyField($dataCompany, 'UserCompany', 'id');
		
		Cache::delete(__('Partnership.HomePage.%s', $company_id), 'default');
	}
}
?>