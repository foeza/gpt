<?php
class AdviceCategory extends AppModel {
	var $name = 'AdviceCategory';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Kategori harap diisi',
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
				'AdviceCategory.modified' => 'DESC'
			),
		);

        switch ($status) {
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'AdviceCategory.status' => 1,
            	));
                break;
            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'AdviceCategory.status' => 0,
            	));
                break;
        }

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['AdviceCategory.user_id'] = $parent_id;
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
        }

		if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
	}

	public function doSave( $data, $advice_category = false, $id = false ) {
		$result = false;
		$default_msg = __('%s kategori');

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();
				$default_msg = sprintf($default_msg, __('menambah'));
			}

			$data['AdviceCategory']['user_id'] = Configure::read('Principle.id');
			$this->set($data);

			if ( $this->validates() ) {
				if( $this->save() ) {
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $advice_category,
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
							'old_data' => $advice_category,
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
		} else if( !empty($advice_category) ) {
			$result['data'] = $advice_category;
		}

		return $result;
	}

	function doDelete( $id ) {
		
		$result = false;
		$advice_category = $this->getData('all', array(
        	'conditions' => array(
				'AdviceCategory.id' => $id,
			),
		));

		if ( !empty($advice_category) ) {
			$name = Set::extract('/AdviceCategory/name', $advice_category);
			$name = implode(', ', $name);
			$default_msg = sprintf(__('menghapus kategori %s'), $name);

			$flag = $this->updateAll(array(
				'AdviceCategory.status' => 0,
	    		'AdviceCategory.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'AdviceCategory.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $advice_category,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $advice_category,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus kategori. Data tidak ditemukan'),
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
			$default_options['conditions']['AdviceCategory.name LIKE'] = '%'.$keyword.'%';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(AdviceCategory.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(AdviceCategory.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(AdviceCategory.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(AdviceCategory.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		
		return $default_options;
	}

	function getMerge( $data, $category_id = false ) {
		if( !empty($data[0]['Advice']) ) {
			foreach ($data as $key => $value) {
				$category_id = !empty($value['Advice']['advice_category_id'])?$value['Advice']['advice_category_id']:false;
				$category = $this->getData('first', array(
					'conditions' => array(
						'AdviceCategory.id' => $category_id,
					),
				), array(
					'company' => false,
					'status' => 'all',
				));

				if( !empty($category) ) {
					$data[$key] = array_merge($data[$key], $category);
				}
			}
		} else if( empty($data['AdviceCategory']) ) {
			$category = $this->getData('first', array(
				'conditions' => array(
					'AdviceCategory.id' => $category_id,
				),
			), array(
				'company' => false,
				'status' => 'all',
			));

			if( !empty($category) ) {
				$data = array_merge($data, $category);
			}
		}

		return $data;
	}
}
?>