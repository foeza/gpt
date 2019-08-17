<?php
class UserCompanySetting extends AppModel {
	var $name = 'UserCompanySetting';
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
	);

	var $validate = array(
		'pph' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'allowEmpty'=> true,
				'message' => 'PPH harus angka',
			),
			'validPercentageInput' => array(
				'rule' => array('validPercentageInput'),
				'message' => 'PPH tidak valid.',
			),
		),
	);
    function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array();

		if(!empty($options)){
			$default_options = array_merge_recursive($default_options, $options);
		}
		
		$default_options = $this->_callFieldForAPI($find, $default_options);

		if( $find == 'paginate' ) {			
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
					$this->alias.'.id',
					$this->alias.'.user_id',
					$this->alias.'.theme_id',
					$this->alias.'.font_type',
					$this->alias.'.font_size',
					$this->alias.'.font_color',
					$this->alias.'.font_menu_color',
					$this->alias.'.font_heading_color',
					$this->alias.'.font_heading_footer_color',
					$this->alias.'.font_link_color',
					$this->alias.'.bg_color',
					$this->alias.'.bg_color_top_header',
					$this->alias.'.bg_footer',
					$this->alias.'.bg_header',
					$this->alias.'.menu_position',
					$this->alias.'.copyright',
					$this->alias.'.bg_image',
					$this->alias.'.logo',
					$this->alias.'.button_color',
					$this->alias.'.main_content_color',
					$this->alias.'.footer_image',
					$this->alias.'.header_image',
					$this->alias.'.is_generate_photo',
					$this->alias.'.is_autoslideshow',
					$this->alias.'.slideshow_interval',
					$this->alias.'.limit_top_agent',
					$this->alias.'.limit_property_list',
					$this->alias.'.limit_property_popular',
					$this->alias.'.limit_latest_news' 
				);
			}
		}

		return $options;
	}

	function validPercentageInput($data) {
		$percentage = false;
		if( !empty($data['pph']) ) {
			$percentage = $data['pph'];
		}

		if( $percentage >= 0 && $percentage <= 100 ) {
			return true;
		}
		return false;
	}

	function getMerge( $data, $user_id, $theme_id = false ){
		if( !empty($user_id) ) {
			$default_conditions = array(
				'conditions' => array(
					'UserCompanySetting.user_id' => $user_id,
				),
			);
			if( !empty($theme_id) ){
				$default_conditions['conditions']['UserCompanySetting.theme_id'] = $theme_id;
			}

			$metas = $this->getData('first', $default_conditions);

			if( !empty($metas) ){
				$data = array_merge( $data, $metas );
			}
		}

		return $data;
	}

	function doSave( $data, $value = false, $id = false ) {
		$result = false;

		if( !empty($data) ) {
			if( !empty($id) ) {
				$this->id = $id;
			} else {
				$this->create();
			}

			$this->set($data);
			if( $this->save($data) ) {
				// delete cache limit dashboard
				$companyID = Common::hashEmptyField($value, 'UserCompany.id', 0);
				
				$cacheConfig = 'default';
				$data_arr = array(
					'properties_home' => array(
						sprintf('Properties.Home.%s', $companyID),
					),
					'default' => array(
						sprintf('Property.Populers.%s', $companyID),
						sprintf('Advice.HomePage.%s', $companyID),
						sprintf('User.Populers.%s', $companyID),
					),
				);

				foreach ($data_arr as $cacheConfig => $cacheNames) {
					if( !empty($cacheNames) ) {
						foreach ($cacheNames as $cacheName) {
							Cache::delete($cacheName, $cacheConfig);
						}
					}
				}
				// 
				$result = array(
		            'msg' => __('Berhasil menyimpan data kustomisasi Anda.'),
		            'status' => 'success',
		        );
			} else {
				$result = array(
		            'msg' => __('Gagal menyimpan data kustomisasi Anda.'),
		            'status' => 'error',
		        );
			}
		} else if( !empty($value) ) {
			$result['data'] = $value;
		}

		return $result;
	}

	function doSaveOwnCompany( $data, $value, $id = false ) {
		$result = false;
		$default_msg = __('menyimpan data pengaturan Anda.');

		if ( !empty($data) ) {
			
			$this->id = $id;
			$this->set($data);

			if ( $this->validates() ) {
				if( $this->save() ) {
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $value,
							'document_id' => $id,
						),
					);
				} else {
					$msg = sprintf(__('Gagal %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'error',
						'data' => $data,
						'Log' => array(
							'activity' => $msg,
							'old_data' => $value,
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
		} else if( !empty($value) ) {
			$result['data'] = $value;
		}

		return $result;
	}
}
?>