<?php
class PropertyStatusListing extends AppModel {
	var $name = 'PropertyStatusListing';
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
				'PropertyStatusListing.modified' => 'DESC'
			),
		);

        switch ($status) {
        	case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'PropertyStatusListing.status' => 1,
            	));
                break;

            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'PropertyStatusListing.status' => 0,
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
			
			$default_options['conditions']['PropertyStatusListing.user_id'] = array_filter($userID);
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

	function getMerge( $data, $category_id = false ) {
		if( !empty($data) && !empty($category_id) ) {
			$data_category = $this->getData('first', array(
				'conditions' => array(
					'PropertyStatusListing.id' => $category_id
				),
			));

			if( !empty($data_category) ) {
				$data = array_merge($data, $data_category);
			}
		}

		return $data;
	}

	public function doSave( $data = false, $status_category = false, $id = false ) {
		$result = false;
		$default_msg = __('%s data kategori properti');

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

			$data['PropertyStatusListing']['user_id'] = $principleID ?: $authUserID;
			$data['PropertyStatusListing']['name'] = !empty($data['PropertyStatusListing']['name']) ? trim($data['PropertyStatusListing']['name']) : '';
			
			$this->set($data);
			if ( $this->validates() ) {
				if( $this->save() ) {
					$status_category_id = $this->id;
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'id' => $status_category_id,
						'Log' => array(
							'activity' => $msg,
							'old_data' => $status_category,
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
							'old_data' => $status_category,
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
						'old_data' => $status_category,
						'document_id' => $id,
						'error' => 1,
					),
				);
			}
		} else if( !empty($status_category) ) {
			$result['data'] = $status_category;
		}

		return $result;
	}

	function doDelete( $id ) {
		
		$result = false;
		$status_listing_category = $this->getData('all', array(
        	'conditions' => array(
				'PropertyStatusListing.id' => $id,
			),
		));

		if ( !empty($status_listing_category) ) {
			$name = Set::extract('/PropertyStatusListing/name', $status_listing_category);
			$name = implode(', ', $name);
			$default_msg = sprintf(__('menghapus kategori properti %s'), $name);

			$flag = $this->updateAll(array(
				'PropertyStatusListing.status' => 0,
	    		'PropertyStatusListing.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'PropertyStatusListing.id' => $id,
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
						'old_data' => $status_listing_category,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $status_listing_category,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus kategori properti. Data tidak ditemukan'),
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
				'PropertyStatusListing.name LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(PropertyStatusListing.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(PropertyStatusListing.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(PropertyStatusListing.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(PropertyStatusListing.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		
		return $default_options;
	}

	public function afterSave($created, $options = array()){
		$parent_id = Configure::read('Principle.id');
		
		Cache::delete(__('PropertyStatusListing.List.%s', $parent_id), 'default');
	}
}
?>