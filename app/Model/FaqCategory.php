<?php
class FaqCategory extends AppModel {
	var $name = 'FaqCategory';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Kategori FAQ harap diisi',
			),
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ) {
		$status = isset($elements['status']) ? $elements['status']:'active';
        $company = isset($elements['company']) ? $elements['company']:true;

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
				'FaqCategory.modified' => 'DESC'
			),
		);

        switch ($status) {
        	case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'FaqCategory.status' => 1,
            	));
                break;

            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'FaqCategory.status' => 0,
            	));
                break;
        }

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['FaqCategory.user_id'] = $parent_id;
        }
		
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

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getMerge( $data, $faq_category_id = false ) {
		if( !empty($data[0]['Faq']) ) {
			foreach ($data as $key => $value) {
				$faq_category_id = !empty($value['Faq']['faq_category_id'])?$value['Faq']['faq_category_id']:false;
				$faq_category = $this->getData('first', array(
					'conditions' => array(
						'FaqCategory.id' => $faq_category_id
					),
				), array(
					'company' => false,
				));

				if( !empty($faq_category) ) {
					$data[$key] = array_merge($data[$key], $faq_category);
				}
			}
		} else if( empty($data['FaqCategory']) ) {
			$faq_category = $this->getData('first', array(
				'conditions' => array(
					'FaqCategory.id' => $faq_category_id
				),
			), array(
				'company' => false,
			));

			if( !empty($faq_category) ) {
				$data = array_merge($data, $faq_category);
			}
		}

		return $data;
	}

	public function doSave( $data = false, $faq_category = false, $id = false, $is_api = false ) {
		$result = false;
		$default_msg = __('%s data kategori FAQ');

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();
				$default_msg = sprintf($default_msg, __('menambah'));
			}

			if(!$is_api){
				$data['FaqCategory']['user_id'] = Configure::read('Principle.id');
			}
			
			$data['FaqCategory']['name'] = !empty($data['FaqCategory']['name']) ? trim($data['FaqCategory']['name']) : '';
			
			$this->set($data);
			if ( $this->validates() ) {
				if( $this->save() ) {
					$faq_category_id = $this->id;
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'id' => $faq_category_id,
						'Log' => array(
							'activity' => $msg,
							'old_data' => $faq_category,
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
							'old_data' => $faq_category,
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
		} else if( !empty($faq_category) ) {
			$result['data'] = $faq_category;
		}

		return $result;
	}

	function doDelete( $id ) {
		
		$result = false;
		$faq_category = $this->getData('all', array(
        	'conditions' => array(
				'FaqCategory.id' => $id,
			),
		));

		if ( !empty($faq_category) ) {
			$name = Set::extract('/FaqCategory/title', $faq_category);
			$name = implode(', ', $name);
			$default_msg = sprintf(__('menghapus kategori FAQ %s'), $name);

			$flag = $this->updateAll(array(
				'FaqCategory.status' => 0,
	    		'FaqCategory.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'FaqCategory.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $faq_category,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $faq_category,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus kategori FAQ. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
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
				'FaqCategory.name LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(FaqCategory.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(FaqCategory.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(FaqCategory.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(FaqCategory.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		
		return $default_options;
	}
}
?>