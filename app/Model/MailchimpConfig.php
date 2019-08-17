<?php
	class MailchimpConfig extends AppModel{
		var $name = 'MailchimpConfig';

		var $validate = array(
			'app_name' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Mohon masukkan nama aplikasi Anda.',
				),
			),
			'client_id' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Mohon masukkan client ID.',
				),
			),
			'secret_key' => array(
				'notempty' => array(
					'rule' => array('notempty'),
					'message' => 'Mohon masukkan secret key.',
				),
			),
		);

		function getData( $find = 'all', $options = array() ) {
			$default_options = array(
				'conditions'=> array(),
				'contain' => array(),
	            'fields'=> array(),
	            'group'=> array(),
	            'fields'=> array(),
	            'limit'=> array(),
			);

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

		function doSave($data, $user_company_config_id = false, $validation = false){
			$id = false;
			$validate = true;
			if(!empty($validation)){
				$this->set($data);

				$validate = $this->validates();
			}

			if(!empty($user_company_config_id)){
				$data['MailchimpConfig']['user_company_config_id'] = $user_company_config_id;
			}

			if($validate){
				if(empty($validation)){
					if(!empty($user_company_config_id)){
						$config = $this->getData('first', array(
							'conditions' => array(
								'MailchimpConfig.user_company_config_id' => $user_company_config_id,
								'MailchimpConfig.status' => 1
							)
						));

						if(!empty($config['MailchimpConfig']['id'])){
							$id = $config['MailchimpConfig']['id'];

							$data['MailchimpConfig']['id'] = $id;
						}
					}

					if(empty($id)){
						$this->create();
					}else{
						$this->id = $id;
					}

					$this->set($data);

					if($this->save()){
						return true;
					}else{
						return false;
					}
				}else{
					return true;
				}
			}else{
				return false;
			}
		}
	}
?>