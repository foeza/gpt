<?php
class BannerSlide extends AppModel {
	var $name = 'BannerSlide';
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
		'order' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Order harap diisi dengan angka',
	            'allowEmpty' => true,
			),
		),
		'start_date' => array(
			'checkAvailableStartDate' => array(
				'rule' => array('checkAvailableStartDate'),
				'message' => 'Mohon masukkan tgl mulai tayang',
			),
		),
	);

	function checkAvailableStartDate () {
		if( !empty($this->data['BannerSlide']['end_date']) && empty($this->data['BannerSlide']['start_date']) ) {
			return false;
		} else {
			return true;
		}
	}

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$status = isset($elements['status'])?$elements['status']:'all';

	//	personal page (kalo bukan personal page user ini isinya principal)
		$isPersonalPage	= Configure::read('Config.Company.is_personal_page');
		$companyData	= Configure::read('Config.Company.data');
		$companyGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $companyGroupID);

		if($isPersonalPage){
			$userID = Common::hashEmptyField($companyData, 'User.id');
		}
		else{
			$principleID	= Configure::read('Principle.id');
			$authUserID		= Configure::read('User.id');
			$authGroupID	= Configure::read('User.group_id');
			$isCompanyAgent	= Common::validateRole('company_agent', $authGroupID);

			if($isCompanyAgent){
				$currentDomain	= Router::fullbaseUrl();
				$personalDomain = Configure::read('User.data.UserConfig.personal_web_url');

				$currentDomain	= str_replace(array('http://', 'https://', '/'), null, $currentDomain);
				$personalDomain	= str_replace(array('http://', 'https://', '/'), null, $personalDomain);

				if($currentDomain == $personalDomain){
				//	pake id agent kalo domain sama dengan domain personal page
					$userID = $authUserID;
				}
				else{
				//	posisi lagi di halaman company
					$request	= Router::getRequest();
					$adminPage	= Common::hashEmptyField($request->params, 'admin');

					if($adminPage){
						$personalPackageID = Configure::read('User.data.UserConfig.membership_package_id');
						$userID = $personalPackageID ? $authUserID : null;
					}
					else{
						$userID = $principleID;
					}
				}
			}
			else{
				$userID = $principleID;
			}
		}

		$default_options = array(
			'conditions' => array(
				'BannerSlide.user_id' => $userID,
				'BannerSlide.status' => 1,
			),
			'order' => array(
				'BannerSlide.order' => 'ASC',
				'BannerSlide.created' => 'DESC',
			),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
            'limit' => array(),
		);

        switch ($status) {
            case 'active':
                $default_options['conditions']['OR'] = array(
                    array(
                        'BannerSlide.start_date' => NULL,
                        'BannerSlide.end_date' => NULL,
                    ),
                    array(
                        'BannerSlide.start_date' => '0000-00-00',
                        'BannerSlide.end_date' => '0000-00-00',
                    ),
                    array(
                        'BannerSlide.start_date'.' <=' => date('Y-m-d'),
                        'BannerSlide.end_date' => '0000-00-00',
                    ),
                    array(
                        'BannerSlide.start_date' => '0000-00-00',
                        'BannerSlide.end_date'.' >=' => date('Y-m-d'),
                    ),
                    array(
                        'BannerSlide.start_date'.' <=' => date('Y-m-d'),
                        'BannerSlide.end_date' => NULL,
                    ),
                    array(
                        'BannerSlide.start_date' => NULL,
                        'BannerSlide.end_date'.' >=' => date('Y-m-d'),
                    ),
                    array(
                        'BannerSlide.start_date'.' <=' => date('Y-m-d'),
                        'BannerSlide.end_date'.' >=' => date('Y-m-d'),
                    ),
                );
                break;
            case 'inactive':
                $default_options['conditions']['BannerSlide.end_date <'] = date('Y-m-d');
                $default_options['conditions']['BannerSlide.end_date <>'] = NULL;
                $default_options['conditions']['BannerSlide.end_date <>'] = '0000-00-00';
                break;
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

	public function doSave( $data, $banner = false, $id = false, $is_api = false ) {
		$result = false;
		$default_msg = __('%s data banner');
		$principleID	= Configure::read('Principle.id');
		$authUserID		= Configure::read('User.id');
		$authGroupID	= Configure::read('User.group_id');
		$isAgent		= Common::validateRole('agent', $authGroupID);

		if ( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();

				if(!$is_api){
				//	agent bisa masuk banner slide (hanya untuk agent yang punya personal page)
				//	jadi pas insert banner, kepemilikan jadi milik agent, bukan principle

					$data['BannerSlide']['user_id'] = $isAgent ? $authUserID : $principleID;
				}

				$default_msg = sprintf($default_msg, __('menambah'));
			}

			$data['BannerSlide']['title'] 	= !empty($data['BannerSlide']['title']) ? trim($data['BannerSlide']['title']) : '';
			$data['BannerSlide']['url'] 	= !empty($data['BannerSlide']['url']) ? trim($data['BannerSlide']['url']) : '';
			$data['BannerSlide']['order'] 	= !empty($data['BannerSlide']['order']) ? trim($data['BannerSlide']['order']) : 0;

			if( empty($data['BannerSlide']['order']) ) {
				unset($data['BannerSlide']['order']);
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
			$photo = !empty($banner['BannerSlide']['photo'])?$banner['BannerSlide']['photo']:false;

			$banner['BannerSlide']['photo_hide'] = $photo;
			$result['data'] = $banner;
		}

		return $result;
	}

	function doDelete( $id ) {
		
		$result = false;
		$banner = $this->getData('all', array(
        	'conditions' => array(
				'BannerSlide.id' => $id,
			),
		));

		if ( !empty($banner) ) {
			$title = Set::extract('/BannerSlide/title', $banner);
			$title = implode(', ', $title);
			$default_msg = sprintf(__('menghapus slide %s'), $title);

			$flag = $this->updateAll(array(
				'BannerSlide.status' => 0,
				'BannerSlide.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'BannerSlide.id' => $id,
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
				'msg' => __('Gagal menghapus slide. Data tidak ditemukan'),
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
        $status = $this->filterEmptyField($data, 'named', 'status', false, array(
            'addslashes' => true,
        ));
        $is_video = $this->filterEmptyField($data, 'named', 'is_video', false, array(
            'addslashes' => true,
        ));

		if( !empty($keyword) ) {
			$default_options['conditions']['OR'] = array(
				'BannerSlide.title LIKE' => '%'.$keyword.'%',
			);
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(BannerSlide.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(BannerSlide.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(BannerSlide.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(BannerSlide.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		
		if( !empty($status) ) {
			$today = date('Y-m-d');

			if( $status == 'active' || $status == 'inactive' ) {
				if( $status == 'developer_active' ) {
					$default_options['conditions']['DATE_FORMAT(BannerSlide.start_date, \'%Y-%m-%d\') <='] = $today;
					$default_options['conditions']['DATE_FORMAT(BannerSlide.end_date, \'%Y-%m-%d\') >='] = $today;
				} else if( $status == 'developer_inactive' ) {
					$default_options['conditions']['AND'] = array(
						'OR' => array(
							'DATE_FORMAT(BannerSlide.start_date, \'%Y-%m-%d\') >' => $today,
							'DATE_FORMAT(BannerSlide.end_date, \'%Y-%m-%d\') <' => $today,
						)
					);
				}

				$default_options['order'] = array(
					'BannerSlide.order' => 'ASC',
					'BannerSlide.created' => 'DESC',
				);
			}
		}
		if( !empty($is_video) ) {
			switch ($is_video) {
				case 'yes':
					$default_options['conditions']['BannerSlide.is_video'] = true;
					break;
				case 'no':
					$default_options['conditions']['BannerSlide.is_video'] = false;
					break;
			}
		}
		
		return $default_options;
	}
	
	public function afterSave($created, $options = array()){
		$dataCompany = Configure::read('Config.Company.data');
		$company_id = $this->filterEmptyField($dataCompany, 'UserCompany', 'id');
		
		Cache::delete(__('BannerSlide.HomePage.%s', $company_id), 'default');
	}
}
?>