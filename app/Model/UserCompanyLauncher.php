<?php
class UserCompanyLauncher extends AppModel {
	var $name		= 'UserCompanyLauncher';
	var $validate	= array(
		'user_id'			=> array(
			'notempty'		=> array(
				'rule'		=> array('notempty'),
				'message'	=> 'Mohon pilih user company',
			),
		),
		'theme_launcher_id'	=> array(
			'notempty'		=> array(
				'rule'		=> array('notempty'),
				'message'	=> 'Mohon pilih tema launcher',
			),
		),
		'body_bg_img' => array(
			'imageupload'	=> array(
	            'rule'			=> array('extension',array('jpeg','jpg','png','gif')),
	            'required'		=> FALSE,
	            'allowEmpty'	=> TRUE,
	            'message'		=> 'Harap mengisi Background Gambar dengan ekstensi jpeg, jpg, png atau gif.'
	        ),
		),
		'logo' => array(
			'imageupload'	=> array(
	            'rule'			=> array('extension',array('jpeg','jpg','png','gif')),
	            'required'		=> FALSE,
	            'allowEmpty'	=> TRUE,
	            'message'		=> 'Harap mengisi Logo dengan ekstensi jpeg, jpg, png atau gif.'
	        ),
		),
	);
	var $belongsTo	= array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
		'ThemeLauncher' => array(
			'className' => 'ThemeLauncher',
			'foreignKey' => 'theme_launcher_id',
		),
	);

	function doSave($data, $value, $id = false, $theme_id = false){
    	$user_id	= Configure::read('Principle.id');
		$result		= array();

		if($data){
			if(!empty($id)){
				$this->id = $id;

			//	untuk background image dan logo, jika dikosongkan tidak usah mengupdate field table
				$backgroundType	= isset($data['UserCompanyLauncher']['background_type']) ? $data['UserCompanyLauncher']['background_type'] : NULL;
				$backgroundImg	= isset($data['UserCompanyLauncher']['body_bg_img']) ? $data['UserCompanyLauncher']['body_bg_img'] : NULL;
				$logoImg		= isset($data['UserCompanyLauncher']['logo']) ? $data['UserCompanyLauncher']['logo'] : NULL;
				$resetTheme		= isset($data['UserCompanyLauncher']['reset']) && $data['UserCompanyLauncher']['reset'] ? TRUE : FALSE;

				if($resetTheme === FALSE && $backgroundType == 'image' && empty($backgroundImg)){
					unset($data['UserCompanyLauncher']['body_bg_img']);
				}

				if($resetTheme === FALSE && empty($logoImg)){
					unset($data['UserCompanyLauncher']['logo']);
				}

				unset($data['UserCompanyLauncher']['background_type'], $data['UserCompanyLauncher']['reset']);
			}
			else{
				$this->create();
			}

			$data['UserCompanyLauncher']['user_id']				= $user_id;
			$data['UserCompanyLauncher']['theme_launcher_id']	= $theme_id;

			$this->set($data);

			$uploadStatus	= isset($data['Upload']) ? $data['Upload'] : NULL;
			$bgImgStatus	= isset($uploadStatus['body_bg_img']['error']) && $uploadStatus['body_bg_img']['error'] ? TRUE : FALSE;
			$bgImgMessage	= isset($uploadStatus['body_bg_img']['message']) ? $uploadStatus['body_bg_img']['message'] : NULL;
			$logoImgStatus	= isset($uploadStatus['logo']['error']) && $uploadStatus['logo']['error'] ? TRUE : FALSE;
			$logoImgMessage	= isset($uploadStatus['logo']['message']) ? $uploadStatus['logo']['message'] : NULL;

			if($this->validates($data) && $bgImgStatus === FALSE && $logoImgStatus === FALSE){
				if($this->save($data)){
					$result = array('status' => 'success', 'msg' => __('Berhasil menyimpan data pengaturan launcher'));
				}
				else{
					$result = array('status' => 'error', 'msg' => __('Gagal menyimpan data pengaturan launcher'), 'data' => $data);
				}
			}
			else{
				$result = array('status' => 'error', 'msg' => __('Gagal menyimpan data pengaturan launcher'), 'data' => $data);

				if($bgImgMessage){
					$this->validationErrors['body_bg_img'][] = $bgImgMessage;
				}

				if($logoImgMessage){
					$this->validationErrors['logo'][] = $logoImgMessage;
				}
			}
		}
		else if($value){
			$body_bg	= !empty($value['UserCompanyLauncher']['body_bg_img']) ? $value['UserCompanyLauncher']['body_bg_img'] : false;
			$logo		= !empty($value['UserCompanyLauncher']['logo']) ? $value['UserCompanyLauncher']['logo'] : false;

			$value['UserCompanyLauncher']['body_bg_hide']	= $body_bg;
			$value['UserCompanyLauncher']['logo_hide']		= $logo;

			$result['data'] = $value;
		}

		return $result;
	}

	function getMerge( $data, $id ){
		$value = $this->find('first', array(
			'conditions' => array(
				'UserCompanyLauncher.user_id' => $id,
			),
		));
		
		if( !empty($value) ) {
			$theme_launcher_id = !empty($value['UserCompanyLauncher']['theme_launcher_id'])?$value['UserCompanyLauncher']['theme_launcher_id']:false;

			$value = $this->ThemeLauncher->getMerge($value, $theme_launcher_id);
			$data = array_merge($data, $value);
		}

		return $data;
	}

	function getData( $find = 'all', $options = array(), $element = array() ) {
		$chosen = isset($element['chosen'])?strval($element['chosen']):'all';
		$company = isset($element['company'])?$element['company']:false;

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'fields'=> array(),
            'limit'=> array(),
		);

		if( $chosen != 'all' ) {
			$default_options['conditions']['UserCompanyLauncher.chosen'] = $chosen;
		}
		if( !empty($company) ) {
    		$user_id = Configure::read('Principle.id');
			$default_options['conditions']['UserCompanyLauncher.user_id'] = $user_id;
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

		$default_options = $this->_callFieldForAPI($find, $default_options);

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

	function _callFieldForAPI ( $find, $options ) {
		if( !in_array($find, array( 'list', 'count' )) ) {
			$rest_api = Configure::read('Rest.token');

			if( !empty($rest_api) ) {
				$options['fields'] = array(
					'UserCompanyLauncher.id',
					'UserCompanyLauncher.theme_launcher_id',
					'UserCompanyLauncher.header_bg',
					'UserCompanyLauncher.footer_bg',
					'UserCompanyLauncher.footer_color',
					'UserCompanyLauncher.button_active_bg',
					'UserCompanyLauncher.button_active_color',
					'UserCompanyLauncher.button_bg',
					'UserCompanyLauncher.button_color',
					'UserCompanyLauncher.body_bg_color',
					'UserCompanyLauncher.body_bg_img',
					'UserCompanyLauncher.logo',
					'UserCompanyLauncher.button_top',
				);
			}
		}

		return $options;
	}

    function doChosen( $theme_id = false, $id = false ) {
    	$user_id = Configure::read('Principle.id');
        $default_msg = __('memilih tema');

    	if( !empty($id) ) {
        	$this->id = $id;
        } else {
        	$this->create();
        }

        $this->set('user_id', $user_id);
        $this->set('theme_launcher_id', $theme_id);
        $this->set('chosen', 1);

        if( $this->save() ) {
        	$id = $this->id;

            $this->updateAll(array( 
                'UserCompanyLauncher.chosen' => 0,
            ), array( 
                'UserCompanyLauncher.user_id' => $user_id,
                'UserCompanyLauncher.id <>' => $id,
            ));

            $result = array(
                'msg' => sprintf(__('Berhasil %s'), $default_msg),
                'status' => 'success',
            );
        } else {
            $result = array(
                'msg' => sprintf(__('Gagal %s'), $default_msg),
                'status' => 'error',
            );
        }

        return $result;
    }
}
?>