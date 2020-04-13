<?php
class PropertyProductCategory extends AppModel {
	var $name = 'PropertyProductCategory';
	var $displayField = 'name';
	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama Kategori properti harap diisi',
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
				'PropertyProductCategory.modified' => 'DESC'
			),
		);

        switch ($status) {
        	case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'PropertyProductCategory.status' => 1,
            	));
                break;

            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'PropertyProductCategory.status' => 0,
            	));
                break;
        }

		$isAgent = Common::validateRole('agent'); 
		
		if( !empty($company) || $isAgent ) {
			$isPersonalPage	= Configure::read('Config.Company.is_personal_page');
			$userID			= array();

			if($isPersonalPage){
			//	frontend
				$userID[] = Configure::read('Config.Company.data.User.id');
			}
			else{
			//	by login
				$principleID	= Configure::read('Principle.id');
				$authGroupID	= Configure::read('User.data.group_id');
				$isAgent		= Common::validateRole('agent', $authGroupID);

				$userID	= array($principleID);

				if($isAgent){
					$userID[] = Configure::read('User.data.id');
				}
			}
			
			$default_options['conditions']['PropertyProductCategory.user_id'] = array_filter($userID);
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
            // if(!empty($options['cache'])){
            //     $default_options['cache'] = $options['cache'];
                
            //     if(!empty($options['cacheConfig'])){
            //         $default_options['cacheConfig'] = $options['cacheConfig'];
            //     }
            // }
	    }

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getMerge( $data, $category_id = false ) {
		if( !empty($data) && !empty($category_id) ) {
			$data_category = $this->getData('first', array(
				'conditions' => array(
					'PropertyProductCategory.id' => $category_id
				),
			));

			if( !empty($data_category) ) {
				$data = array_merge($data, $data_category);
			}
		}

		return $data;
	}

	public function doSave( $data = false, $data_exist = false, $id = false ) {
		$result = false;
		$default_msg = __('%s data kategori produk');

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();
				$default_msg = sprintf($default_msg, __('menambah'));
			}

			$principleID	= Configure::read('Principle.id');
			$authUserID		= Configure::read('User.data.id');

			$data['PropertyProductCategory']['user_id'] = $principleID ?: $authUserID;
			$data['PropertyProductCategory']['name'] = !empty($data['PropertyProductCategory']['name']) ? trim($data['PropertyProductCategory']['name']) : '';
			
			$this->set($data);
			if ( $this->validates() ) {
				if( $this->save() ) {
					$category_id = $this->id;
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'id' => $category_id,
						'Log' => array(
							'activity' => $msg,
							'old_data' => $data_exist,
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
							'old_data' => $data_exist,
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
					'Log' => array(
						'activity' => sprintf(__('Gagal %s'), $default_msg),
						'old_data' => $data_exist,
						'document_id' => $id,
						'error' => 1,
					),
				);
			}
		} else if( !empty($data_exist) ) {
			$result['data'] = $data_exist;
		}

		return $result;
	}

	function doDelete( $id ) {
		$result = false;
		$data_categories = $this->getData('all', array(
        	'conditions' => array(
				'PropertyProductCategory.id' => $id,
			),
		));

		if ( !empty($data_categories) ) {
			$name = Set::extract('/PropertyProductCategory/name', $data_categories);
			$name = implode(', ', $name);
			$default_msg = sprintf(__('menghapus kategori produk %s'), $name);

			$flag = $this->updateAll(array(
				'PropertyProductCategory.status' => 0,
	    		'PropertyProductCategory.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'PropertyProductCategory.id' => $id,
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
						'old_data' => $data_categories,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $data_categories,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus kategori produk. Data tidak ditemukan'),
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
				'PropertyProductCategory.name LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(PropertyProductCategory.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(PropertyProductCategory.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(PropertyProductCategory.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(PropertyProductCategory.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		
		return $default_options;
	}

	public function afterSave($created, $options = array()){
		$parent_id = Configure::read('Principle.id');
		
		Cache::delete(__('PropertyProductCategory.List.%s', $parent_id), 'default');
	}
}
?>