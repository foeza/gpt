<?php
class MailchimpTemplate extends AppModel{
	var $name = 'MailchimpTemplate';

	var $validate = array(
		'name_template' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama template Anda.',
			)
		),
		'template_content' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan konten template Anda.',
			),
		),
		'type_template' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih tipe template Anda.',
			),
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ) {
		$company = isset($elements['company']) ? $elements['company']:true;
		$default_options = array(
			'conditions'=> array(
				'MailchimpTemplate.status' => 1
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'fields'=> array(),
            'limit'=> array(),
		);

        if( !empty($company) ) {
            $dataCompany = Configure::read('Config.Company.data');
            $user_company_config_id = !empty($dataCompany['UserCompanyConfig']['id'])?$dataCompany['UserCompanyConfig']['id']:false;

            $default_options['conditions']['MailchimpTemplate.user_company_config_id'] = $user_company_config_id;
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

	function doSave($data, $value = array(), $id = false, $validation = false, $data_company = array()){
		$result = false;
		if(!empty($data)){
			$text = 'menambah';
			if(empty($validation)){
				if(!empty($id)){
					$this->id = $id;

					$text = 'mengubah';
				}else{
					$this->create();
				}
			}

			$this->set($data);

			$validate = true;
			if(!empty($validation)){
				$validate = $this->validates();
			}

			if($validate){
				if(empty($validation)){
					if($this->save()){

						if(!empty($data['MailchimpTemplate']['type_template']) && $data['MailchimpTemplate']['type_template'] == 'birthday' && !empty($data['MailchimpTemplate']['is_primary_birthday'])){
							$this->primary_birthday($this->id, $data_company);
						}

						$msg = sprintf(__('Berhasil %s template email'), $text);
						$result = array(
							'msg' => $msg,
							'status' => 'success',
							'Log' => array(
								'activity' => $msg,
								'old_data' => $data,
								'document_id' => $this->id,
							)
						);
					}else{
						$result = array(
							'msg' => sprintf(__('Gagal %s template email'), $text),
							'status' => 'error'
						);
					}
				}
			}else{
				$result = array(
					'msg' => sprintf(__('Gagal %s template email'), $text),
					'status' => 'error'
				);
			}
		}else if(!empty($value)){
			$result['data'] = $value;
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));

        if( !empty($keyword) ) {
            $this->virtualFields['find_keyword'] = sprintf(
                'MATCH(
                    MailchimpTemplate.name_template,
                ) AGAINST(\'%s\' IN BOOLEAN MODE)', $keyword
            );

            $default_options['conditions'] = array(
                'OR' => array(
                    'MailchimpTemplate.name_template LIKE ' => '%'.$keyword.'%',
                    'MATCH(
                        MailchimpTemplate.name_template,
                    ) AGAINST(? IN BOOLEAN MODE)' => $keyword,
                )
            );

            $default_options['order'] = array(
                'MailchimpTemplate.find_keyword' => 'DESC',
            );
        }

        if( !empty($sort) ) {
	        $direction = $this->filterEmptyField($data, 'named', 'direction', false, array(
	            'addslashes' => true,
	        ));

			$default_options['order'] = array(
                $sort => $direction,
            );
		}

        return $default_options;
    }

    function doDelete( $id ) {
		$result = false;
		$template = $this->getData('all', array(
        	'conditions' => array(
				'MailchimpTemplate.id' => $id,
			),
		));

		if ( !empty($template) ) {
			$id = Set::extract('/MailchimpTemplate/id', $template);
			
			$default_msg = __('menghapus template');

			$flag = $this->updateAll(array(
				'MailchimpTemplate.status' => 0,
			), array(
				'MailchimpTemplate.id' => $id,
			));

			$id = implode(', ', $id);

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s dengan ID %s'), $default_msg, $id);
                $result = array(
					'msg' => sprintf(__('Berhasil %s '), $default_msg),
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $template,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $template,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus template newsletter. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	function replicate($id){
		$result = array(
			'msg' => __('Data template tidak ditemukan.'),
			'status' => 'error'
		);

		if(!empty($id)){
			$template = $this->getData('first', array(
				'conditions' => array(
					'MailchimpTemplate.id' => $id,
					'MailchimpTemplate.status' => 1
				)
			));

			if(!empty($template)){
				$count_copy = $this->getData('count', array(
					'conditions' => array(
						'MailchimpTemplate.name_template LIKE' => '%'.$template['MailchimpTemplate']['name_template'].'%',
						'MailchimpTemplate.status' => 1
					)
				));

				$text_name = $template['MailchimpTemplate']['name_template'];

				$template['MailchimpTemplate']['name_template'] = sprintf('%s %s', $text_name, $count_copy+1 );

				unset($template['MailchimpTemplate']['created']);
				unset($template['MailchimpTemplate']['modified']);
				unset($template['MailchimpTemplate']['id']);
				unset($template['MailchimpTemplate']['is_primary_birthday']);

				$this->create();

				$this->set($template);

				if($this->save()){
					$msg = sprintf(__('Selamat! Anda berhasil melakukan replikasi template "%s".'), $text_name);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => sprintf('Anda berhasil melakukan replikasi template ID #s', $this->id),
							'old_data' => $template,
							'document_id' => $this->id,
						)
					);
				}
			}
		}

		return $result;
	}

	function primary_birthday($id, $data_company){
		$config_id = !empty($data_company['UserCompanyConfig']['id']) ? $data_company['UserCompanyConfig']['id'] : false;

		if(!empty($config_id) && !empty($id)){
			$this->updateAll(
				array(
					'is_primary_birthday' => 0,
				),
				array(
					'user_company_config_id' => $config_id,
					'type_template' => 'birthday'
				)
			);

			$this->updateAll(
				array(
					'is_primary_birthday' => 1,
				),
				array(
					'user_company_config_id' => $config_id,
					'type_template' => 'birthday',
					'id' => $id
				)
			);

			return array(
				'msg' => __('Berhasil mengubah status template ulang tahun'),
				'status' => 'success'
			);
		}else{
			return array(
				'msg' => __('Gagal mengubah status template ulang tahun'),
				'status' => 'error'
			);
		}
	}
}
?>