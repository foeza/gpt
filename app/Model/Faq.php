<?php
class Faq extends AppModel {
	var $name = 'Faq';
	var $displayField = 'name';
	var $validate = array(
		'faq_category_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Kategori FAQ harap dipilih',
			),
		),
		'question' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Pertanyaan harap diisi',
			),
		),
		'answer' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jawaban harap diisi',
			),
		),
	);

	var $belongsTo = array(
		'FaqCategory' => array(
			'className' => 'FaqCategory',
			'foreignKey' => 'faq_category_id',
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
				'Faq.created' => 'DESC',
				'Faq.id' => 'DESC',
			),
		);

        switch ($status) {
            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'Faq.status' => 0,
            	));
                break;
            
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'Faq.status' => 1,
            	));
                break;
        }

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['Faq.user_id'] = $parent_id;
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

	function getMerge ( $find = 'all', $data, $faq_category_id, $options = false, $is_merge = true ) {
		$result = array();
		$default_options = array(
			'conditions' => array(
				'Faq.faq_category_id' => $faq_category_id,
			),
		);

		if(!empty($options)){
			$default_options = array_merge_recursive($default_options, $options);
		}
		
		if( empty($data['Faq']) ) {
			$faq = $this->getData($find, $default_options);

			if( !empty($faq) ) {
				
				if ( $is_merge ) {
					$result = array_merge($data, $faq);
				} else {
					foreach($faq as $key => $datafaq) {
						$faq[$key] = array_merge($datafaq, $data);
					}
					$result = $faq;
				}
			}
		}

		return $result;
	}

	public function doSave( $data, $faq = false, $id = false, $is_api = false ) {
		$result = false;
		$default_msg = __('%s data FAQ');

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();
				$default_msg = sprintf($default_msg, __('menambah'));
			}

			if(!$is_api){
				$data['Faq']['user_id'] = Configure::read('Principle.id');
			}
			
			$data['Faq']['question'] = trim($data['Faq']['question']);
			$data['Faq']['answer'] = trim($data['Faq']['answer']);

			$this->set($data);
			if ( $this->validates() ) {
				if( $this->save() ) {
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $faq,
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
							'old_data' => $faq,
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
		} else if( !empty($faq) ) {
			$result['data'] = $faq;
		}

		return $result;
	}

	function doDelete( $id ) {
		
		$result = false;
		$faq = $this->getData('all', array(
        	'conditions' => array(
				'Faq.id' => $id,
			),
		));

		if ( !empty($faq) ) {
			$question = Set::extract('/Faq/question', $faq);
			$question = implode(', ', $question);
			$default_msg = sprintf(__('menghapus FAQ %s'), $question);

			$flag = $this->updateAll(array(
				'Faq.status' => 0,
	    		'Faq.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'Faq.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $faq,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $faq,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus FAQ. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));
        $category = $this->filterEmptyField($data, 'named', 'category', false, array(
            'addslashes' => true,
        ));
        $answer = $this->filterEmptyField($data, 'named', 'answer', false, array(
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
				'Faq.question LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($category) ) {
			$default_options['conditions']['Faq.faq_category_id'] = $category;
		}
		if( !empty($answer) ) {
			$default_options['conditions']['Faq.answer LIKE'] = '%'.$answer.'%';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(Faq.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(Faq.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(Faq.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(Faq.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		
		return $default_options;
	}

	function getMergeByCategory( $data, $category_id = false ) {
		if( !empty($data['FaqCategory']) ) {
			foreach ($data as $key => $value) {
				$category_id = !empty($value['id'])?$value['id']:false;
				$faq = $this->getData('all', array(
					'conditions' => array(
						'Faq.faq_category_id' => $category_id
					),
				), array(
					'company' => false,
				));

				if( !empty($faq) ) {
					$data['Faq'] = $faq;
				}
			}
		} else if( empty($data['Faq']) ) {
			$faq = $this->getData('all', array(
				'conditions' => array(
					'Faq.faq_category_id' => $category_id
				),
			), array(
				'company' => false,
			));

			if( !empty($faq) ) {
				$data['Faq'] = $faq;
			}
		}

		return $data;
	}
}
?>