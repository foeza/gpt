<?php
class BannerDeveloper extends AppModel {
	var $name = 'BannerDeveloper';
	var $displayField = 'name';

	var $hasMany = array(
		'BannerDeveloperView' => array(
			'className' => 'BannerDeveloperView',
			'foreignKey' => 'banner_developer_view_id',
		),
	);

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
				'message' => 'Nama Developer harap diisi',
			),
		),
		'start_date' => array(
			'checkAvailableStartDate' => array(
				'rule' => array('checkAvailableStartDate'),
				'message' => 'Mohon masukkan tgl mulai tayang',
			),
		),
		'url' => array(
			'checkUrl' => array(
				'rule' => array('checkUrl'),
				'message' => 'Mohon masukkan URL',
			),
		),
		'short_description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan keterangan singkat developer',
			),
		),
		'description' => array(
			'checkDesc' => array(
				'rule' => array('checkDesc'),
				'message' => 'Mohon masukkan deskripsi developer',
			),
		),
		'order' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Mohon harus berupa angka',
	            'allowEmpty' => true,
			),
		),
	);

	function checkUrl () {
		if( empty($this->data['BannerDeveloper']['is_article']) && empty($this->data['BannerDeveloper']['url']) ) {
			return false;
		} else {
			return true;
		}
	}

	function checkDesc () {
		if( !empty($this->data['BannerDeveloper']['is_article']) && empty($this->data['BannerDeveloper']['description']) ) {
			return false;
		} else {
			return true;
		}
	}

	function checkAvailableStartDate () {
		if( !empty($this->data['BannerDeveloper']['end_date']) && empty($this->data['BannerDeveloper']['start_date']) ) {
			return false;
		} else {
			return true;
		}
	}

	function getData( $find = 'all', $options = array(), $elements = array() ){
        $company = isset($elements['company']) ? $elements['company']:true;
        $status = isset($elements['status'])?$elements['status']:'all';

		$default_options = array(
			'conditions'=> array(
				'BannerDeveloper.status' => 1
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order'=> array(
				'BannerDeveloper.order' => 'ASC',
				'BannerDeveloper.created' => 'DESC',
			),
		);

        switch ($status) {
            case 'active':
                $default_options['conditions']['OR'] = array(
                    array(
                        'BannerDeveloper.start_date' => NULL,
                        'BannerDeveloper.end_date' => NULL,
                    ),
                    array(
                        'BannerDeveloper.start_date' => '0000-00-00',
                        'BannerDeveloper.end_date' => '0000-00-00',
                    ),
                    array(
                        'BannerDeveloper.start_date'.' <=' => date('Y-m-d'),
                        'BannerDeveloper.end_date' => '0000-00-00',
                    ),
                    array(
                        'BannerDeveloper.start_date' => '0000-00-00',
                        'BannerDeveloper.end_date'.' >=' => date('Y-m-d'),
                    ),
                    array(
                        'BannerDeveloper.start_date'.' <=' => date('Y-m-d'),
                        'BannerDeveloper.end_date' => NULL,
                    ),
                    array(
                        'BannerDeveloper.start_date' => NULL,
                        'BannerDeveloper.end_date'.' >=' => date('Y-m-d'),
                    ),
                    array(
                        'BannerDeveloper.start_date'.' <=' => date('Y-m-d'),
                        'BannerDeveloper.end_date'.' >=' => date('Y-m-d'),
                    ),
                );
                break;
            case 'inactive':
                $default_options['conditions']['BannerDeveloper.end_date <'] = date('Y-m-d');
                $default_options['conditions']['BannerDeveloper.end_date <>'] = NULL;
                $default_options['conditions']['BannerDeveloper.end_date <>'] = '0000-00-00';
                break;
        }

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['BannerDeveloper.user_id'] = $parent_id;
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
        if(!empty($options['offset'])){
            $default_options['offset'] = $options['offset'];
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

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	public function doSave( $data, $banner = false, $id = false, $is_api = false ) {
		if($is_api){
			$this->removeValidate();
		}

		$result = false;
		$default_msg = __('%s data banner developer');

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();
				$default_msg = sprintf($default_msg, __('menambah'));
			}

			if(!$is_api){
				$data['BannerDeveloper']['user_id'] = Configure::read('Principle.id');
			}
			
			$data['BannerDeveloper']['title'] = !empty($data['BannerDeveloper']['title']) ? trim($data['BannerDeveloper']['title']) : '';
			$data['BannerDeveloper']['url'] = !empty($data['BannerDeveloper']['url']) ? trim($data['BannerDeveloper']['url']) : '';
			$data['BannerDeveloper']['order'] = !empty($data['BannerDeveloper']['order']) ? trim($data['BannerDeveloper']['order']) : 0;
			
			if( isset($data['BannerDeveloper']['is_article']) && $data['BannerDeveloper']['is_article'] == '0' ) {
				$data['BannerDeveloper']['description'] = '';
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
							'old_data' => $banner,
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
							'old_data' => $banner,
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
		} else if( !empty($banner) ) {
			$photo = !empty($banner['BannerDeveloper']['photo'])?$banner['BannerDeveloper']['photo']:false;
			$banner['BannerDeveloper']['photo_hide'] = $photo;
			$result['data'] = $banner;
		}

		return $result;
	}

	function doDelete( $id ) {
		
		$result = false;
		$banner = $this->getData('all', array(
        	'conditions' => array(
				'BannerDeveloper.id' => $id,
			),
		));

		if ( !empty($banner) ) {
			$title = Set::extract('/BannerDeveloper/title', $banner);
			$title = implode(', ', $title);
			$default_msg = sprintf(__('menghapus banner developer %s'), $title);

			$flag = $this->updateAll(array(
				'BannerDeveloper.status' => 0,
			), array(
				'BannerDeveloper.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $banner,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $banner,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus banner developer. Data tidak ditemukan'),
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
        $short_description = $this->filterEmptyField($data, 'named', 'short_description', false, array(
            'addslashes' => true,
        ));
        $is_article = $this->filterEmptyField($data, 'named', 'is_article', false, array(
            'addslashes' => true,
        ));
        $status = $this->filterEmptyField($data, 'named', 'status', false, array(
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

        if( !empty($title) ) {
			$default_options['conditions']['BannerDeveloper.title LIKE'] = '%'.$title.'%';
		}
		if( !empty($short_description) ) {
			$default_options['conditions']['BannerDeveloper.short_description LIKE'] = '%'.$short_description.'%';
		}
		if( !empty($is_article) ) {
			switch ($is_article) {
				case 'yes':
					$default_options['conditions']['BannerDeveloper.is_article'] = true;
					break;
				case 'no':
					$default_options['conditions']['BannerDeveloper.is_article'] = false;
					break;
			}
		}
		if( !empty($keyword) ) {
			$default_options['conditions']['OR'] = array(
				'BannerDeveloper.title LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(BannerDeveloper.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(BannerDeveloper.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(BannerDeveloper.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(BannerDeveloper.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		
		if( !empty($status) ) {
			$today = date('Y-m-d');

			if( $status == 'developer_active' || $status == 'developer_inactive' ) {
				if( $status == 'developer_active' ) {
					$default_options['conditions']['DATE_FORMAT(BannerDeveloper.start_date, \'%Y-%m-%d\') <='] = $today;
					$default_options['conditions']['DATE_FORMAT(BannerDeveloper.end_date, \'%Y-%m-%d\') >='] = $today;
				} else if( $status == 'developer_inactive' ) {
					$default_options['conditions']['AND'] = array(
						'OR' => array(
							'DATE_FORMAT(BannerDeveloper.start_date, \'%Y-%m-%d\') >' => $today,
							'DATE_FORMAT(BannerDeveloper.end_date, \'%Y-%m-%d\') <' => $today,
						)
					);
				}

				$default_options['order'] = array(
					'BannerDeveloper.order' => 'ASC',
					'BannerDeveloper.created' => 'DESC',
				);
			} else if( $status == 'article' || $status == 'nonarticle' )  {
				if( $status == 'article' ) {
					$default_options['conditions']['BannerDeveloper.is_article'] = 1;
				} else if( $status == 'nonarticle' ) {
					$default_options['conditions']['BannerDeveloper.is_article'] = 0;
				}

				$default_options['order'] = array(
					'BannerDeveloper.created' => 'DESC',
				);
			}
		}

		return $default_options;
	}

    function getDataRelated ( $id = false, $limit = 3 ) {
    	return $this->getData('all', array(
			'conditions'=> array(
				'BannerDeveloper.id <>' => $id,
			),
			'limit' => $limit,
		));
    }

    function removeValidate () {
        $this->validator()
        ->remove('photo')
        ->remove('title')
        ->remove('start_date')
        ->remove('url')
        ->remove('short_description')
        ->remove('description')
        ->remove('order');
    }
	
	public function afterSave($created, $options = array()){
		$dataCompany = Configure::read('Config.Company.data');
		$company_id = $this->filterEmptyField($dataCompany, 'UserCompany', 'id');
		
		Cache::delete(__('BannerDeveloper.HomePage.%s', $company_id), 'default');
	}
}
?>