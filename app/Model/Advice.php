<?php
class Advice extends AppModel {
//	untuk clear cache
	var $companyID;

	var $name = 'Advice';
	var $validate = array(
		'advice_category_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Kategori harap dipilih',
			),
		),
		'title' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Judul harap diisi',
			),
		),
		'photo' => array(
			'imageupload' => array(
	            'rule' => array('extension',array('jpeg','jpg','png','gif')),
	            'required' => false,
	            'allowEmpty' => false,
	            'message' => 'Foto harap diisi dan berekstensi (jpeg, jpg, png, gif)'
	        ),
		),
		'short_content' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Keterangan singkat harap diisi',
			),
		),
		'content' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Konten harap diisi',
			),
		),
	);

	var $belongsTo = array(
		'AdviceCategory' => array(
			'className' => 'AdviceCategory',
			'foreignKey' => 'advice_category_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'Author' => array(
			'className' => 'User',
			'foreignKey' => 'author_id',
		),
	);

	var $hasMany = array(
		'AdviceView' => array(
			'className' => 'AdviceView',
			'foreignKey' => 'advice_id',
		),
	);

	public function afterSave($created, $options = array()){
		$dataCompany = Configure::read('Config.Company.data');
		$company_id = !empty($dataCompany['UserCompany']['id'])?$dataCompany['UserCompany']['id']:false;
		$companyID = $this->companyID ? $this->companyID : $company_id;

	//	find		
		$cacheGroup		= 'Advices.Find';
		$cacheNameInfix	= 'advices__index_';
		$cachePath		= CACHE.$cacheGroup;
		$wildCard		= '*'.$cacheNameInfix.$companyID.'*';
		$cleared		= clearCache($wildCard, $cacheGroup, NULL);

	//	detail
		if(isset($this->id) && $this->id){
		//	untuk save, update
			$cacheGroup		= 'Advices.Detail';
			$cacheConfig	= 'advices_detail';
			$cacheName		= sprintf($cacheGroup.'.%s.%s', $companyID, $this->id);

			Cache::delete($cacheName, $cacheConfig);
		}
		else if(isset($options['record_id']) && $options['record_id']){
		//	untuk update all
			$recordID		= $options['record_id'];
			$cacheGroup		= 'Advices.Detail';
			$cacheNameInfix	= 'advices__detail_';
			$wildCard		= array();

			foreach($recordID as $id){
				$wildCard[] = '*'.$cacheNameInfix.$companyID.'_'.$id;
			}

			$cleared = clearCache($wildCard, $cacheGroup, NULL);
		}
		
		Cache::delete(__('Advice.HomePage.%s', $companyID), 'default');
		Cache::clearGroup('Advices.Find');
	}

	public function getData( $find = 'all', $options = array(), $elements = array()  ) {
		$status = isset($elements['status']) ? $elements['status']:'active';
		$company = isset($elements['company']) ? $elements['company']:true;
		$admin = !empty($elements['admin']) ? $elements['admin']:false;

		$default_options = array(
			'conditions'=> array(
				'Advice.status' => 1,
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
				'Advice.modified'=>'DESC',
				'Advice.id'=>'DESC',
			),
		);

        switch ($status) {
            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'Advice.active' => 0,
            	));
                break;
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'Advice.active' => 1,
            	));
                break;
            case 'deleted':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'Advice.status' => 0,
            	));
                break;
        }

        if($admin){
        	$isAdmin = Configure::read('User.admin');
        	if($isAdmin){
        		$default_options['conditions']['Advice.active'] = array(0,1);
        	}
        }

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['Advice.user_id'] = $parent_id;
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

	public function doSave( $data, $user_id = false, $advice = false, $id = false, $is_api = false ) {
		$result = false;

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = __('mengubah artikel');
			} else {
				$this->create();
				$data['Advice']['author_id'] = $user_id;
				$default_msg = __('menambah artikel');
			}

			if(!$is_api){
				$data['Advice']['user_id'] = Configure::read('Principle.id');
			}
			
			$this->set($data);

			if ( $this->validates() ) {
				if( $this->save() ) {
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $advice,
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
							'old_data' => $advice,
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
		} else if( !empty($advice) ) {
			$photo = !empty($advice['Advice']['photo'])?$advice['Advice']['photo']:false;
			$advice['Advice']['photo_hide'] = $photo;
			$result['data'] = $advice;
		}else{
			$advice['Advice']['active'] = TRUE;
			$result['data'] = $advice;
		}

		return $result;
	}

	function doDelete( $id ) {
		$result = false;
		$advice = $this->getData('all', array(
        	'conditions' => array(
				'Advice.id' => $id,
			),
		));

		if ( !empty($advice) ) {
			$title = Set::extract('/Advice/title', $advice);
			$title = implode(', ', $title);
			$default_msg = sprintf(__('menghapus artikel %s'), $title);

			$flag = $this->updateAll(array(
				'Advice.status' => 0,
	    		'Advice.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'Advice.id' => $id,
			));

            if( $flag ) {
			//	trigger aftersave untuk clear cache, updateAll tidak men-trigger afterSave 
				$options = array('record_id' => $id);
				$this->afterSave($flag, $options);

				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $advice,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $advice,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus artikel. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
        	'addslashes' => true,
    	));
    	$title = $this->filterEmptyField($data, 'named', 'title', false, array(
        	'addslashes' => true,
    	));
    	$category = $this->filterEmptyField($data, 'named', 'category', false, array(
        	'addslashes' => true,
    	));
    	$category_name = $this->filterEmptyField($data, 'named', 'category_name', false, array(
        	'addslashes' => true,
    	));
    	$author = $this->filterEmptyField($data, 'named', 'author', false, array(
        	'addslashes' => true,
    	));
    	$short_content = $this->filterEmptyField($data, 'named', 'short_content', false, array(
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
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));

		if( !empty($keyword) ) {
			$default_options['conditions']['OR']['Advice.title LIKE'] = '%'.$keyword.'%';
			$default_options['conditions']['OR']['Advice.short_content LIKE'] = '%'.$keyword.'%';
			$default_options['conditions']['OR']['Advice.content LIKE'] = '%'.$keyword.'%';
		}
		if( !empty($title) ) {
			$default_options['conditions']['Advice.title LIKE'] = '%'.$title.'%';
		}
		if( !empty($category) ) {
			$default_options['conditions']['Advice.advice_category_id'] = $category;
		}
		if( !empty($category_name) ) {
			$default_options['conditions']['AdviceCategory.name LIKE'] = '%'.$category_name.'%';
            $default_options['contain'][] = 'AdviceCategory';
		}
		if( !empty($author) ) {
			$default_options['conditions']['CONCAT(Author.first_name,\' \',IFNULL(Author.last_name, \'\')) LIKE'] = '%'.$author.'%';
            $default_options['contain'][] = 'Author';
		}
		if( !empty($short_content) ) {
			$default_options['conditions']['Advice.short_content LIKE'] = '%'.$short_content.'%';
		}
		if(!empty($order)){
			$default_options['conditions']['Advice.order'] = $order;
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(Advice.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(Advice.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(Advice.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(Advice.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}

		if( !empty($status) ) {
			switch ($status) {
				case 'inactive':
					$default_options['conditions']['Advice.active'] = false;
					break;
				case 'active':
					$default_options['conditions']['Advice.active'] = true;
					break;
			}
		}

		if( !empty($sort) ) {
        	$sortCategory = strpos($sort, 'AdviceCategory.');
        	$sortUser = strpos($sort, 'Author.');

        	if( is_numeric($sortCategory) ) {
	            $default_options['contain'][] = 'AdviceCategory';
	        } else if( is_numeric($sortUser) ) {
	            $default_options['contain'][] = 'Author';
	        }
        }
		
		return $default_options;
	}

    public function getDataList($data) {
        if( !empty($data) ) {
            if( !empty($data['Advice']) ) {
            	$category_id = !empty($data['Advice']['advice_category_id'])?$data['Advice']['advice_category_id']:false;
                $data = $this->AdviceCategory->getMerge( $data, $category_id );
            } else {
                foreach ($data as $key => $value) {
            		$category_id = !empty($value['Advice']['advice_category_id'])?$value['Advice']['advice_category_id']:false;
            		$author_id = !empty($value['Advice']['author_id'])?$value['Advice']['author_id']:false;

                	$value = $this->AdviceCategory->getMerge( $value, $category_id );
                	$value = $this->User->getMerge( $value, $author_id );
                	$data[$key] = $value;
                }
            }
        }

        return $data;
    }

    function getDataRelated ( $id = false, $category_id = false, $limit = 3 ) {
    	return $this->getData('all', array(
			'conditions'=> array(
				'Advice.id <>' => $id,
				'Advice.advice_category_id' => $category_id,
			),
			'limit' => $limit,
		));
    }

    function populers ( $limit = 5 ) {
        $options = $this->getData('paginate');
        $conditions = !empty($options['conditions'])?$options['conditions']:false;

        $this->AdviceView->virtualFields['cnt'] = 'COUNT(AdviceView.advice_id)';
        $values = $this->AdviceView->getData('all', array(
            'contain' => array(
                'Advice',
            ),
            'conditions' => $conditions,
            'order' => array(
                'cnt' => 'DESC',
            ),
            'group' => array(
                'AdviceView.advice_id',
            ),
            'limit' => $limit,
        ));
        $values = $this->AdviceCategory->getMerge($values);

        return $values;
    }

    function doActived($value, $status){
    	if($value){
    		$id = $this->filterEmptyField($value, 'Advice', 'id');
    		$title = $this->filterEmptyField($value, 'Advice', 'title');

	    	if($status){
	    		$msg = __('aktifkan artikel "%s"', $title);
	    	}else{
	    		$msg = __('non aktifkan artikel "%s"', $title);
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
}
?>