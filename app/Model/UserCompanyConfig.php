<?php
class UserCompanyConfig extends AppModel {
	var $name = 'UserCompanyConfig';

	var $validate = array(
		'theme_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih tema website Anda.',
			),
		),
		'principle_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan email principle.',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email Anda salah.',
			),
			'validateEmailRole' => array(
				'rule' => array('validateEmailRole'),
				'message' => 'Email yang Anda masukkan tidak terdaftar.',
			),
		),
		'pic_sales_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih PIC website Anda.',
			),
		),
		'favicon' => array(
			'imageupload' => array(
	            'rule' => array('extension',array('jpeg','jpg','png','gif')),
	            'required' => false,
	            'allowEmpty' => true,
	            'message' => 'Foto harap diisi dan berekstensi (jpeg, jpg, png, gif)'
	        ),
		),
		'max_admin' => array(
			'numeric' => array(
				'allowEmpty' => true,
				'rule' => array('numeric'),
				'message' => 'Harus berupa Angka.',
			),
		),
		'max_agent' => array(
			'numeric' => array(
				'allowEmpty' => true,
				'rule' => array('numeric'),
				'message' => 'Harus berupa Angka.',
			),
		),
		'premium_listing' => array(
			'numeric' => array(
				'allowEmpty' => true,
				'rule' => array('numeric'),
				'message' => 'Harus berupa Angka.',
			),
		),
		'brochure_custom_sell' => array(
			'imageupload' => array(
	            'rule' => array('extension',array('jpeg','jpg','png','gif')),
	            'allowEmpty' => true,
	            'message' => 'Foto harap diisi dan berekstensi (jpeg, jpg, png, gif)'
	        ),
		),
		'brochure_custom_rent' => array(
			'imageupload' => array(
	            'rule' => array('extension',array('jpeg','jpg','png','gif')),
	            'allowEmpty' => true,
	            'message' => 'Foto harap diisi dan berekstensi (jpeg, jpg, png, gif)'
	        ),
		),
		'type_custom_ebrochure' => array(
			'typeEbrosurVlidation' => array(
				'rule' => array('typeEbrosurVlidation'),
				'message' => 'Harap pilih tipe eBrosur.',
			),
		), 
		'launcher_url' => array(
			'validateLauncherUrl' => array(
				'rule' => array('validateLauncherUrl'),
				'message' => 'Mohon masukkan URL untuk Launcher',
			),
		),
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
		'domain_zimbra' => array(
			'validateURL' => array(
				'rule' => array('validateURL'),
				'message' => 'Format Domain Zimbra salah',
			),
		), 
		'mt_property_type' => array(
			'validateCountPropertyType' => array(
				'rule'		=> array('validateCountPropertyType'),
				'message'	=> 'Maksimal tipe properti yang bisa dipilih hanya 4',
			),
		),
		'default_co_broke_commision' => array(
			'validateCoBroke' => array(
				'rule'		=> array('validateCoBroke'),
				'message'	=> 'Masukkan komisi broker jika ingin melakukan co broke automatis',
			),
		),
		'default_agent_commission' => array(
			'validateCoBroke' => array(
				'rule'		=> array('validateCoBroke'),
				'message'	=> 'Masukkan komisi agen jika ingin melakukan co broke automatis',
			),
		)
	);

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'Theme' => array(
			'className' => 'Theme',
			'foreignKey' => 'theme_id',
		),
		'Template' => array(
			'className' => 'Template',
			'foreignKey' => 'template_id',
		),
		'EbrochureTemplate' => array(
			'foreignKey' => 'ebrochure_template_id',
		),
		'MembershipPackage' => array(
			'className' => 'MembershipPackage',
			'foreignKey' => 'membership_package_id',
		),
	);

	var $hasOne = array(
		'MailchimpConfig' => array(
			'className' => 'MailchimpConfig',
			'foreignKey' => 'user_company_config_id',
		),
	);

	var $hasMany = array(
		'MailchimpTemplate' => array(
			'className' => 'MailchimpTemplate',
			'foreignKey' => 'user_company_config_id',
		),
		'MailchimpList' => array(
			'className' => 'MailchimpList',
			'foreignKey' => 'user_company_config_id',
		),
	);

	function validateCoBroke($data){
		$field = key($data);

		$result = true;
		$global_data = $this->data;

		$is_open_cobroke = Common::hashEmptyField($global_data, 'UserCompanyConfig.is_open_cobroke');
		$default_type_price_co_broke_commision = Common::hashEmptyField($global_data, 'UserCompanyConfig.default_type_price_co_broke_commision');
		$default_co_broke_commision = Common::hashEmptyField($global_data, 'UserCompanyConfig.default_co_broke_commision');
		$default_type_co_broke_commission = Common::hashEmptyField($global_data, 'UserCompanyConfig.default_type_co_broke_commission');
		$default_agent_commission = Common::hashEmptyField($global_data, 'UserCompanyConfig.default_agent_commission');

		if(!empty($is_open_cobroke)){
			if((empty($default_type_price_co_broke_commision) || empty($default_co_broke_commision)) && in_array($field, array('default_type_price_co_broke_commision', 'default_co_broke_commision'))){
				$result = false;
			}else if(empty($default_agent_commission) && $field == 'default_agent_commission'){
				$result = false;
			}else if(empty($default_type_co_broke_commission) && $field == 'default_type_co_broke_commission'){
				$result = false;
			}else if($default_type_co_broke_commission == 'percentage' && $default_co_broke_commision > 100){
				$result = false;

                $this->validator()
                ->getField('default_co_broke_commision')
                ->getRule('validateCoBroke')
                ->message = __('Tidak boleh lebih dari 100%');
			}
		}

		return $result;
	}

	function validateCountPropertyType($data){
		$fieldName		= key($data);
		$value			= array_filter(array_shift($data));
		$isShowTrend	= Common::hashEmptyField($this->data, $this->alias.'.mt_is_show_trend');

		if($isShowTrend){
			if(empty($value)){
				return __('Mohon pilih tipe properti');
			}
			else if(count($value) > 4){
				return false;
			}
			else{
				$this->data[$this->alias][$fieldName] = json_encode($value);
				return true;
			}
		}
		else{
			$this->data[$this->alias][$fieldName] = json_encode($value);
			return true;
		}
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

	function validateLauncherUrl(){
		$isLauncher		= isset($this->data['UserCompanyConfig']['is_launcher']) && $this->data['UserCompanyConfig']['is_launcher'] == 1 ? TRUE : FALSE;
		$launcherUrl	= isset($this->data['UserCompanyConfig']['launcher_url']) ? $this->data['UserCompanyConfig']['launcher_url'] : NULL;

		return TRUE;
	}

	function validateEmailRole($data){
		$result = false;

		$key = key($data);

		if($data[$key]){
			$user = $this->User->getData('first', array(
				'conditions' => array(
					'User.email' => $data[$key],
					'User.group_id' => array( 3,4 ),
				)
			), array(
				'status' => 'semi-active',
			));

			if(!empty($user['User']['id'])){
				$result = true;
				$this->data['UserCompanyConfig']['user_id'] = $user['User']['id'];
			}
		}

		return $result;
	}

	function typeEbrosurVlidation($data){
		$result = true;
		if(!empty($this->data['UserCompanyConfig']['is_brochure']) && empty($data['type_custom_ebrochure'])){
			$result = false;
		}

		return $result;
	}

	function doSave( $data, $value, $id = false, $user_id = false, $url = FULL_BASE_URL ){
		$result = false;

		if ( !empty($data) ) {
			$result = array(
	            'msg' => __('Gagal menyimpan data pengaturan Anda.'),
	            'status' => 'error',
	        );

	        $uploadError = !empty($data['Upload']['error'])?$data['Upload']['error']:false;
	        $uploadMsg = !empty($data['Upload']['message'])?$data['Upload']['message']:false;

	        if(empty($data['UserCompanyConfig']['is_brochure'])){
	        	$data['UserCompanyConfig']['auto_create_ebrochure'] = '';
	        	$data['UserCompanyConfig']['type_custom_ebrochure'] = '';
	        	$data['UserCompanyConfig']['brochure_custom_sell'] = '';
	        	$data['UserCompanyConfig']['brochure_custom_rent'] = '';
	        	$data['UserCompanyConfig']['brochure_custom_sell'] = '';
	        	$data['UserCompanyConfig']['delta_x_code'] = $data['UserCompanyConfig']['delta_y_code'] = '';
	        	$data['UserCompanyConfig']['delta_x_created'] = $data['UserCompanyConfig']['delta_y_created'] = '';
	        	$data['UserCompanyConfig']['delta_x_mlsid'] = $data['UserCompanyConfig']['delta_y_mlsid'] = '';
	        	$data['UserCompanyConfig']['with_mls_id'] = '';
	        }else{
	        	if(empty($data['UserCompanyConfig']['type_custom_ebrochure'])){
	        		$data['UserCompanyConfig']['type_custom_ebrochure'] = 'landscape';
	        	}
	        }

	        if( empty($uploadError) ) {

				$data['UserCompanyConfig']['user_id'] = $user_id;
				$data['UserCompanyConfig']['domain'] = $url;

				if(!empty($id)){
					$this->id = $id;
				}else{
					$this->create();
				}

				// unset package bundling if not configure
				$data_unset = array();
				if ( empty($data['UserCompanyConfig']['is_block_premium_listing']) ) {
					$data_unset = $this->_unsetPackageBundling($this->id);
					$unset_company_config = Common::hashEmptyField($data_unset, 'UserCompanyConfig', array());
					$company_config = Common::hashEmptyField($data, 'UserCompanyConfig', array());

					$data['UserCompanyConfig'] = array_merge($company_config, $unset_company_config);
				}

				$this->set($data);

				$usercompany_validation = $this->validates($data);
				$mailchimp_validation = $this->MailchimpConfig->doSave($data, false, true);

				if($usercompany_validation && $mailchimp_validation){
					$data['UserCompanyConfig']['process_delete'] = true;

					if($this->save($data)){

						$this->MailchimpConfig->doSave($data, $this->id);
						
						$result = array(
				            'msg' => __('Berhasil menyimpan data pengaturan Anda.'),
				            'status' => 'success',
				            'RefreshAuth' => array(
				            	'id' => $user_id,
			            	),
							'Log' => array(
								'activity' => __('Berhasil menyimpan data pengaturan Perusahaan'),
								'document_id' => $this->id,
							),
				        );
					}
				}else{
					$result['data'] = $data;
				}
			} else {
				$validationErrors = array();

				if(!empty($this->validationErrors)){
					$validationErrors = array_merge($validationErrors, $this->validationErrors);
				}
				
				$result = array(
		            'msg' => $uploadMsg,
		            'status' => 'error',
		            'data' => $data,
		            'validationErrors' => $validationErrors
		        );
			}
		}  else if( !empty($value) ) {
			$email = !empty($value['User']['email'])?$value['User']['email']:false;
			$favicon = !empty($value['UserCompanyConfig']['favicon'])?$value['UserCompanyConfig']['favicon']:false;
			$brochure_custom_sell = !empty($value['UserCompanyConfig']['brochure_custom_sell'])?$value['UserCompanyConfig']['brochure_custom_sell']:false;
			$brochure_custom_rent = !empty($value['UserCompanyConfig']['brochure_custom_rent'])?$value['UserCompanyConfig']['brochure_custom_rent']:false;
			$about_bg = !empty($value['UserCompanyConfig']['about_bg'])?$value['UserCompanyConfig']['about_bg']:false;
			$is_ebrosur_frontend = Common::hashEmptyField($value, 'UserCompanyConfig.is_ebrosur_frontend', true, array(
				'isset' => true,
			));

			$value['UserCompanyConfig']['principle_email'] = $email;
			$value['UserCompanyConfig']['favicon_hide'] = $favicon;
			$value['UserCompanyConfig']['brochure_custom_sell_hide'] = $brochure_custom_sell;
			$value['UserCompanyConfig']['brochure_custom_rent_hide'] = $brochure_custom_rent;
			$value['UserCompanyConfig']['about_bg_hide'] = $about_bg;
            $value['UserCompanyConfig']['is_ebrosur_frontend'] = $is_ebrosur_frontend;

			$result['data'] = $value;
		}

		return $result;
	}

	// if uncheck the configure then unset the id package in field premium_listing
	function _unsetPackageBundling( $id = false ) {
		if (!empty($id)) {
			return $this->set('premium_listing', NULL);
		}

	}

	function getMerge( $data, $id, $with_contain = false ) {
		if( !empty($id) ) {
			$value = $this->find('first', array(
				'conditions' => array(
					'UserCompanyConfig.user_id' => $id,
				),
			));
			
			if( !empty($value) ) {
				if( !empty($with_contain) ) {
					$theme_id = !empty($value['UserCompanyConfig']['theme_id'])?$value['UserCompanyConfig']['theme_id']:false;
					$template_id = !empty($value['UserCompanyConfig']['template_id'])?$value['UserCompanyConfig']['template_id']:false;
					$value = $this->Theme->getMerge($value, $theme_id, array(
						'cache' => array(
							'name' => __('Theme.%s', $theme_id),
						),
					));
					$value = $this->Template->getMerge($value, $template_id, array(
						'cache' => array(
							'name' => __('Template.%s', $template_id),
						),
					));
				}
				
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}

	function getData( $find = 'all', $options = array(), $elements = array() ) {
		$admin_rumahku = Configure::read('User.Admin.Rumahku');
		$mine = $this->filterEmptyField($elements, 'mine');
		$status = Common::hashEmptyField($elements, 'status');

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'fields'=> array(),
            'limit'=> array(),
		);

		if( !empty($mine) ) {
	        if( empty($admin_rumahku) ) {
		        $options['conditions']['User.parent_id'] = Configure::read('Principle.id');
		        $options['contain'][] = 'User';
		        $options['contain'][] = 'UserCompany';
		    }
		}

		switch ($status) {
			case 'published':
				$defaultOptions['conditions']['OR'] = array(
					array(
						'UserCompanyConfig.live_date' => NULL,
						'UserCompanyConfig.end_date' => NULL,
					),
					array(
						'UserCompanyConfig.live_date' => '0000-00-00',
						'UserCompanyConfig.end_date' => '0000-00-00',
					),
					array(
						'UserCompanyConfig.live_date <=' => date('Y-m-d'),
						'UserCompanyConfig.end_date' => '0000-00-00',
					),
					array(
						'UserCompanyConfig.live_date' => '0000-00-00',
						'UserCompanyConfig.end_date >=' => date('Y-m-d'),
					),
					array(
						'UserCompanyConfig.live_date <=' => date('Y-m-d'),
						'UserCompanyConfig.end_date' => NULL,
					),
					array(
						'UserCompanyConfig.live_date' => NULL,
						'UserCompanyConfig.end_date >=' => date('Y-m-d'),
					),
					array(
						'UserCompanyConfig.live_date <=' => date('Y-m-d'),
						'UserCompanyConfig.end_date >=' => date('Y-m-d'),
					),
				);
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
            if(!empty($options['offset'])){
                $default_options['offset'] = $options['offset'];
            }
		}

		if( !empty($options['contain']) && in_array('UserCompany', $options['contain']) ) {
			$this->bindModel(array(
	            'hasOne' => array(
	                'UserCompany' => array(
	                    'className' => 'UserCompany',
	                    'foreignKey' => false,
	                    'conditions' => array(
	                    	'UserCompanyConfig.user_id = UserCompany.user_id',
	                	),
	                ),
	            )
	        ), false);
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
                  $this->alias.'.id',
                  $this->alias.'.user_id',
                  $this->alias.'.domain',
                  $this->alias.'.theme_id',
                  $this->alias.'.template_id',
                  $this->alias.'.pic_sales_id',
                  $this->alias.'.membership_package_id',
                  $this->alias.'.pph',
                  $this->alias.'.brochure_custom_sell',
                  $this->alias.'.brochure_custom_rent',
                  $this->alias.'.type_custom_ebrochure',
                  $this->alias.'.auto_create_ebrochure',
                  $this->alias.'.delta_x_code',
                  $this->alias.'.delta_y_code',
                  $this->alias.'.delta_x_created',
                  $this->alias.'.delta_y_created',
                  $this->alias.'.delta_x_mlsid',
                  $this->alias.'.delta_y_mlsid',
                  $this->alias.'.with_mls_id',
                  $this->alias.'.watermark_type',
                  $this->alias.'.favicon',
                  $this->alias.'.logo_company',
                  $this->alias.'.about_bg',
                  $this->alias.'.google_analytic',
                  $this->alias.'.meta_title',
                  $this->alias.'.meta_description',
                  $this->alias.'.form_api_code',
                  $this->alias.'.facebook_appid',
                  $this->alias.'.max_admin',
                  $this->alias.'.max_agent',
                  $this->alias.'.premium_listing',
                  $this->alias.'.property_listing',
                  $this->alias.'.sub_header_content',
                  $this->alias.'.header_content',
                  $this->alias.'.footer_content',
                  $this->alias.'.cobroke_requirement',
                  $this->alias.'.is_approval_property',
                  $this->alias.'.is_brochure',
                  $this->alias.'.is_launcher',
                  $this->alias.'.launcher_url',
                  $this->alias.'.is_hidden_address_property',
                  $this->alias.'.is_hidden_map',
                  $this->alias.'.is_blog',
                  $this->alias.'.is_faq',
                  $this->alias.'.is_developer_page',
                  $this->alias.'.is_refresh_listing',
                  $this->alias.'.is_agent_personal_website',
                  $this->alias.'.is_home_banner_title',
                  $this->alias.'.is_career',
                  $this->alias.'.is_home_right_filter_cozy',
                  $this->alias.'.is_bg_footer_easyliving',
                  $this->alias.'.is_bt_commission',
                  $this->alias.'.is_edit_property',
                  $this->alias.'.is_delete_property',
                  $this->alias.'.contract_date',
                  $this->alias.'.live_date',
                  $this->alias.'.end_date',
                  $this->alias.'.is_kolisting_koselling',
                  $this->alias.'.is_mandatory_client',
                  $this->alias.'.is_mandatory_no_address',
                  $this->alias.'.is_display_address',
                  $this->alias.'.is_full_logo',
                  $this->alias.'.is_block_premium_listing',
                  $this->alias.'.is_restrict_approval_property',
                  $this->alias.'.brochure_content_color',
                  $this->alias.'.brochure_footer_color',
                  $this->alias.'.language',
                  $this->alias.'.is_description_ebrochure',
                  $this->alias.'.is_specification_ebrochure',
                  $this->alias.'.is_sent_app',
                  $this->alias.'.sent_app_day',
                  $this->alias.'.limit_listing_home',
                  $this->alias.'.limit_agent_home',
                  $this->alias.'.hide_powered',
                  $this->alias.'.domain_zimbra',
                  $this->alias.'.modified',
                  $this->alias.'.created',
                  $this->alias.'.is_co_broke',
                  $this->alias.'.is_admin_approval_cobroke',
                );
            }
        }

        return $options;
    }

	function doChosen( $theme_id = false, $company_config_id = false ) {
    	$user_id = Configure::read('Principle.id');
        $default_msg = __('memilih tema');

    	if( !empty($company_config_id) ) {
        	$this->id = $company_config_id;
        } else {
        	$this->create();
        }

        $this->set('theme_id', $theme_id);

        if( $this->save() ) {
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

    function delete_template_ebrosur($user_company_config_id, $field){
    	$this->id = $user_company_config_id;
    	$this->set($field, NULL);

    	if($this->save()){
    		$result = array(
    			'msg' => __('Berhasil menghapus template eBrosur'),
    			'status' => 'success'
    		);
    	}else{
    		$result = array(
    			'msg' => __('Gagal menghapus template eBrosur'),
    			'status' => 'error'
    		);
    	}

    	return $result;
    }

    public function afterSave($created, $options = array()){
    	$data = $this->data;
    	$id = $this->id;
    	
		$id = Common::hashEmptyField($data, 'UserCompanyConfig.id', $id);
		$user_id = Common::hashEmptyField($data, 'UserCompanyConfig.user_id');
		$process_delete = Common::hashEmptyField($data, 'UserCompanyConfig.process_delete');

		if( !empty($process_delete) && !empty($user_id) ) {			
			$this->deleteAll(array(
				'UserCompanyConfig.id <>' => $id,
				'UserCompanyConfig.user_id' => $user_id,
			));
		}

		$dataCompany = Configure::read('Config.Company.data');
		$company_id = !empty($dataCompany['UserCompany']['id'])?$dataCompany['UserCompany']['id']:false;
		$cacheGroups	= array(
			'Properties.Home' => 'properties__home_', 
			'Properties.Find' => 'properties__find_',
			'Properties.Detail' => 'properties__detail_',
		);

	//	clear "find" cache
		foreach($cacheGroups as $cacheGroup => $cacheNameInfix){
			$cachePath	= CACHE.$cacheGroup;
			$wildCard	= '*'.$cacheNameInfix.$company_id.'*';
			$cleared	= clearCache($wildCard, $cacheGroup, NULL);
		}
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$region_id = $this->filterEmptyField($data, 'Search', 'region_id', false, array(
        	'addslashes' => true,
    	));
    	$city_id = $this->filterEmptyField($data, 'Search', 'city_id', false, array(
        	'addslashes' => true,
    	));
    	$subareas = $this->filterEmptyField($data, 'Search', 'subareas', false, array(
        	'addslashes' => true,
    	));
    	
		$region_id = $this->filterEmptyField($data, 'named', 'region_id', $region_id, array(
        	'addslashes' => true,
    	));
		$city_id = $this->filterEmptyField($data, 'named', 'city_id', $city_id, array(
        	'addslashes' => true,
    	));
		$subareas = $this->filterEmptyField($data, 'named', 'subareas', $subareas, array(
        	'addslashes' => true,
    	));
		$user_id = $this->filterEmptyField($data, 'named', 'user_id', false, array(
        	'addslashes' => true,
    	));
		$pic_id = $this->filterEmptyField($data, 'named', 'pic_id', false, array(
        	'addslashes' => true,
    	));
		$status = $this->filterEmptyField($data, 'named', 'status', false, array(
        	'addslashes' => true,
    	));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));
        $company_id = $this->filterEmptyField($data, 'named', 'company_id', false, array(
            'addslashes' => true,
        ));

        if( !empty($user_id) ) {
        	$user_id = explode(',', $user_id);
			$default_options['conditions']['UserCompany.user_id'] = $user_id;
            $default_options['contain'][] = 'UserCompany';
        }

        if( !empty($pic_id) ) {
        	$pic_id = explode(',', $pic_id);
			$default_options['conditions']['UserCompanyConfig.pic_sales_id'] = $pic_id;
        }

		if( !empty($region_id) ) {
			$default_options['conditions']['UserCompany.region_id'] = $region_id;
            $default_options['contain'][] = 'UserCompany';
		}
		if( !empty($city_id) ) {
			$default_options['conditions']['UserCompany.city_id'] = $city_id;
            $default_options['contain'][] = 'UserCompany';
		}
		if( !empty($subareas) ) {
			$default_options['conditions']['UserCompany.subarea_id'] = $subareas;
            $default_options['contain'][] = 'UserCompany';
		}
		if( !empty($company_id) ) {
			$company_id = explode(',', $company_id);
			$default_options['conditions']['UserCompanyConfig.user_id'] = $company_id;
		}
		if( !empty($status) ) {
			switch ($status) {
				case 'expired':
					$default_options['conditions']['DATE_FORMAT(UserCompanyConfig.end_date, \'%Y-%m-%d\') <'] = date('Y-m-d');
					break;
				case 'active':
					$default_options['conditions']['DATE_FORMAT(UserCompanyConfig.end_date, \'%Y-%m-%d\') >='] = date('Y-m-d');
					break;
			}
		}
		switch ($sort) {
			case 'User.group_id':
            	$default_options['contain'][] = 'User';
				break;
			case 'UserCompany.name':
				$this->bindModel(array(
		            'hasOne' => array(
		                'UserCompany' => array(
		                    'className' => 'UserCompany',
		                    'foreignKey' => false,
		                    'conditions' => array(
		                    	'UserCompanyConfig.user_id = UserCompany.user_id',
		                	),
		                ),
		            )
		        ), false);
            	$default_options['contain'][] = 'UserCompany';
				break;
		}

		return $default_options;
	}

	// s: update membership bundling rku all company
	function updateMembershipRKU( $value = false, $data_package = array() ) {
        $default_msg = __('update data company');
		$result = array(
            'msg' => __('Gagal, data tidak ditemukan.'),
            'status' => 'error',
        );
		
        if (!empty($value)) {
			$company_config_id = $this->filterEmptyField($value, 'UserCompanyConfig', 'id');

			$company_name = Common::hashEmptyField($value, 'User.UserCompany.name');

        	$id_package   = $this->filterEmptyField($data_package, 'MembershipPackage', 'id', false);
        	$name_package = $this->filterEmptyField($data_package, 'MembershipPackage', 'name', false);

	        $flag = $this->updateAll(array(
				'UserCompanyConfig.is_block_premium_listing' => 1,
	    		'UserCompanyConfig.premium_listing' => $id_package,
			), array(
				'UserCompanyConfig.id' => $company_config_id,
			));

	        if( $flag ) {
	            $result = array(
	                'msg' => sprintf(__('Berhasil %s, %s, membership: %s'), $default_msg, $company_name, $name_package),
	                'status' => 'success',
	            );
	        } else {
	            $result = array(
	                'msg' => sprintf(__('Gagal %s, %s, membership: %s'), $default_msg, $company_name, $name_package),
	                'status' => 'error',
	            );
	        }

        }

        return $result;
    }
    // e: update membership bundling rku all company

	function _callCount( $city_id, $data = false, $status = 'all' ){
		$default_options = array(
			'conditions' => array(
				'UserCompany.city_id' => $city_id,
			),
			'contain' => array(
				'UserCompany',
			),
		);

		$type = $this->filterEmptyField($data, 'named', 'type', false, array(
        	'addslashes' => true,
    	));
		$group_type = $this->filterEmptyField($data, 'named', 'group_type', false, array(
        	'addslashes' => true,
    	));

        if( empty($type) ) {
        	$type = $group_type;
        }

		if( !empty($type) ) {
			switch ($type) {
				case 'group':
            		$default_options['conditions']['User.group_id'] = 4;
					break;
				case 'company':
            		$default_options['conditions']['User.group_id'] = 3;
					break;
			}
			
            $default_options['contain'][] = 'User';
		}

		if( !empty($status) ) {
			switch ($status) {
				case 'expired':
					$default_options['conditions'][]['OR'] = array(
						'DATE_FORMAT(UserCompanyConfig.end_date, \'%Y-%m-%d\') <' => date('Y-m-d'),
						'UserCompanyConfig.end_date' => NULL,
					);
					break;
				case 'active':
					$default_options['conditions']['DATE_FORMAT(UserCompanyConfig.end_date, \'%Y-%m-%d\') >='] = date('Y-m-d');
					break;
			}
		}
		
		$this->bindModel(array(
            'hasOne' => array(
                'UserCompany' => array(
                    'className' => 'UserCompany',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'UserCompanyConfig.user_id = UserCompany.user_id',
                	),
                ),
            )
        ), false);

		return $this->getData('count', $default_options, array(
			'mine' => true,
		));
	}
}
?>