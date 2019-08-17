<?php
class MailchimpCampaign extends AppModel{
	var $name = 'MailchimpCampaign';

	var $validate = array(
		'title_campaign' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan judul campaign Anda.',
			),
		),
		'subject_campaign' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan subjek email Anda.',
			),
		),
		'mailchimp_list_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih grup email Anda.',
			),
		),
		'type_period' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih waktu pengiriman email Anda.',
			),
			'validateDateSend' => array(
				'rule' => array('validateDateSend'),
				'message' => 'Silahkan masukkan field tanggal dan waktu lalu set waktu pengiriman tidak boleh sama dengan waktu sekarang atau kurang.',
			),
		),
		'content_campaign' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan konten email Anda.',
			),
		),
		'email_from' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan email pengirim.',
			),
			'email' => array(
				'rule' => array('email'),
				'message' => 'Format email salah',
			),
			'maxLength' => array(
				'rule' => array('maxLength', 128),
				'message' => 'Panjang email maksimal 128 karakter',
			),
			'minLength' => array(
				'rule' => array('minLength', 5),
				'message' => 'Panjang email minimal 5 karakter',
			),
		),
	);

	var $belongsTo = array(
		'MailchimpList' => array(
			'className' => 'MailchimpList',
            'foreignKey' => 'mailchimp_list_id',
		),
		'User' => array(
			'className' => 'User',
            'foreignKey' => 'user_id',
		)
	);

	function validateDateSend($data){
		$result = true;

		if($data['type_period'] == 'scheduled' && (empty($this->data['MailchimpCampaign']['date_send']) || empty($this->data['MailchimpCampaign']['time_send']) )){
			$result = false;
		}else if($data['type_period'] == 'scheduled' && !empty($this->data['MailchimpCampaign']['date_send']) && !empty($this->data['MailchimpCampaign']['time_send'])){
			$time = sprintf('%s %s', $this->data['MailchimpCampaign']['date_send'], $this->data['MailchimpCampaign']['time_send']);
			$curr_time = date('Y-m-d H:i');
			
			if(strtotime($time) <= strtotime($curr_time)){
				return false;
			}
		}

		return $result;
	}

	function getData( $find = 'all', $options = array(), $elements = array() ) {
        $mine = isset($elements['mine'])?$elements['mine']:false;
        $company = isset($elements['company'])?$elements['company']:true;

        $group_id = Configure::read('User.data.group_id');
        $admin_rumahku = Configure::read('User.Admin.Rumahku');

        $mine_access = true;
        if($group_id == 3 || $admin_rumahku){
        	$mine_access = false;
        }

		$default_options = array(
			'conditions'=> array(
				'MailchimpCampaign.status' => 1
			),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'fields'=> array(),
            'limit'=> array(),
		);

		if( !empty($mine) && !empty($mine_access) ) {
            $user_login_id = Configure::read('User.id');
            $default_options['conditions']['MailchimpCampaign.user_id'] = $user_login_id;
        }

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['MailchimpCampaign.company_id'] = $parent_id;
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
			
			$data = $value;
			$text = 'menambah';
			$data['MailchimpCampaign']['company_id'] = Configure::read('Principle.id');

			if(empty($validation)){
				if(!empty($id)){
					$this->id = $id;

					$text = 'mengubah';
				}else{
					$this->create();
				}
			}

			if(!empty($data['MailchimpCampaign']['type_period']) && $data['MailchimpCampaign']['type_period'] == 'directly'){
				$data['MailchimpCampaign']['is_send'] = 1;

				$data['MailchimpCampaign']['date_send'] = date('Y-m-d');
				$data['MailchimpCampaign']['time_send'] = date('h:i:s');
				$data['MailchimpCampaign']['is_send'] = 1;
				$data['MailchimpCampaign']['is_pending'] = 0;
			}else{
				$data['MailchimpCampaign']['is_send'] = 0;
				$data['MailchimpCampaign']['is_pending'] = 0;
			}

			$email_from = !empty($data['MailchimpCampaign']['email_from']) ? $data['MailchimpCampaign']['email_from'] : false;

			$this->set($data);

			$validate = true;
			if(!empty($validation)){
				$validate = $this->validates();
			}

			$email_validation = true;
			$type_period = $this->filterEmptyField($data, 'MailchimpCampaign', 'type_period');
			
			if($type_period == 'directly'){
				$to_email = array();
				$is_user_internal = Common::hashEmptyField($data, 'MailchimpList.is_user_internal');

				if($is_user_internal){

					$params= array(
						'groupClient' => Common::hashEmptyField($data, 'GroupClient'),
						'groupUser' => Common::hashEmptyField($data, 'GroupUser'),
					);
					$data = Common::_callUnset($data, array(
						'GroupClient',
						'GroupUser',
					));

					$to_email = $this->User->getCountUserInternal($params, 'list');
				}else{
					$user_list = $this->MailchimpList->MailchimpListDetail->getData('all', array(
						'conditions' => array(
							'MailchimpListDetail.mailchimp_list_id' => $data['MailchimpCampaign']['mailchimp_list_id'],
							'MailchimpListDetail.status' => 1
						)
					));

					if(!empty($user_list)){
						$to_email = Set::extract('/MailchimpListDetail/email', $user_list);
					}
				}

				if(empty($to_email)){
					$email_validation = false;
				}
			}

			if($validate && $email_validation){
				unset($data['MailchimpCampaign']['yes_save']);
				
				$this->set($data);

				if(empty($validation)){
					if($this->save()){
						$msg = sprintf(__('Berhasil %s campaign email'), $text);
						$result = array(
							'msg' => $msg,
							'status' => 'success',
							'id' => $this->id,
							'Log' => array(
								'activity' => $msg,
								'old_data' => $data,
								'document_id' => $this->id,
							)
						);

						if(!empty($to_email) && $type_period == 'directly'){
							$content_campaign = $this->replaceCodeDate($data['MailchimpCampaign']['content_campaign']);

							if( is_array($to_email) ) {
								$to_email = array_filter($to_email);
							}

							$result['SendEmail'] = array(
		                    	'to_name' => null,
		                    	'to_email' => $to_email,
		                    	'subject' => $data['MailchimpCampaign']['subject_campaign'],
		                    	'template' => 'netral',
		                    	'data' => array(
		                    		'layout' => false,
		                    		'content' => $content_campaign,
		                    		'bcc' => $to_email,
		                    		'from' => $email_from
		                    	)
		                	);
						}
					}else{
						$result = array(
							'msg' => sprintf(__('Gagal %s campaign email'), $text),
							'status' => 'error'
						);
					}
				}
			}else{
				$result = array(
					'msg' => sprintf(__('Gagal %s campaign email.'), $text),
					'status' => 'error'
				);

				if(empty($email_validation)){
					$result['msg'] .= __(' List email tidak tersedia');
				}

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
        $add_type = $this->filterEmptyField($data, 'named', 'add_type', false, array(
            'addslashes' => true,
        ));

        if( !empty($keyword) ) {
            $this->virtualFields['find_keyword'] = sprintf(
                'MATCH(
                    MailchimpCampaign.title_campaign
                ) AGAINST(\'%s\' IN BOOLEAN MODE)', $keyword
            );

            $default_options['conditions'] = array_merge($default_options['conditions'], array(
                'OR' => array(
                    'MailchimpCampaign.title_campaign LIKE ' => '%'.$keyword.'%',
                    'MATCH(
                        MailchimpCampaign.title_campaign
                    ) AGAINST(? IN BOOLEAN MODE)' => $keyword,
                )
            ));

            $default_options['order'] = array(
                'MailchimpCampaign.find_keyword' => 'DESC',
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

		if(!empty($add_type)){
			switch ($add_type) {
				case 'sended':
					$default_options['conditions']['MailchimpCampaign.is_send'] = 1;
				break;
				case 'scheduled':
					$default_options['conditions']['MailchimpCampaign.type_period'] = 'scheduled';
				break;
			}
		}
		
        return $default_options;
    }

    function doDelete( $id ) {
		$result = false;
		$template = $this->getData('all', array(
        	'conditions' => array(
				'MailchimpCampaign.id' => $id,
			),
		));

		if ( !empty($template) ) {
			$id = Set::extract('/MailchimpCampaign/id', $template);
			
			$default_msg = __('menghapus template');

			$flag = $this->updateAll(array(
				'MailchimpCampaign.status' => 0,
			), array(
				'MailchimpCampaign.id' => $id,
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

	function doBasic( $data, $value = false, $validate = false, $id = false, $save_session = true ) {
        $result = false;
        $user_id = Configure::read('User.id');

        if ( !empty($data) ) {
            if( empty($validate) ) {
                if( !empty($id) ) {
                    $this->id = $id;
                } else {
                    $this->create();
                }
            }

            $data['MailchimpCampaign']['user_id'] = $user_id;
            
            $this->set($data);

            if( $this->validates() ) {
                $flagSave = true;

                if( !empty($validate) && !empty($save_session) ) {
                	$session_text = '__Site.MailchimpCampaign.SessionName.Basic';
                	if(!empty($id)){
                		$session_text = $session_text.'.'.$id;
                	}
                    CakeSession::write($session_text, $data);
                } else {
                    $flagSave = $this->save();
                    $id = $this->id;
                }

                if( !empty($flagSave) ) {
                    $result = array(
                        'msg' => __('Berhasil menyimpan pengaturan campaign Anda'),
                        'status' => 'success',
                        'id' => $id,
                    );
                } else {
                    $result = array(
                        'msg' => __('Gagal menyimpan pengaturan campaign Anda, mohon lengkapi semua data yang diperlukan'),
                        'status' => 'error',
                    );
                }
            } else {
                $result = array(
                    'msg' => __('Gagal menyimpan pengaturan campaign Anda, mohon lengkapi semua data yang diperlukan'),
                    'status' => 'error',
                );
            }

            if($result['status'] == 'error'){
                $result['data'] = $data;    
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }
        
        return $result;
    }

	function doTemplate($step_template = 'basic', $template = false, $mailchimp_campaign_id = false, $modelName = false){
		$result = array();
		$id = Common::hashEmptyField($template, sprintf('%s.id', $modelName));

		if((!empty($id) && in_array($step_template, array('basic', 'saved'))) || $step_template == 'new'){
			$data['MailchimpCampaign']['type_template'] = $step_template;
			$data['MailchimpCampaign']['id_template'] = $id;

			$session_text = '__Site.MailchimpCampaign.SessionName.Template';
			$session_content 	= '__Site.MailchimpCampaign.SessionName.Content';
			$session_read_text 		= CakeSession::read($session_text);
			$session_read_content 	= CakeSession::read($session_content);

        	if(!empty($mailchimp_campaign_id)){
        		$session_text = $session_text.'.'.$mailchimp_campaign_id;
        		$session_content = $session_content.'.'.$mailchimp_campaign_id;
        	}

        	if($step_template == 'basic'){
        		$id_template = $this->filterEmptyField($session_read_text, 'MailchimpCampaign', 'id_template');

        		if($id_template != $id){
        			$data['MailchimpCampaign']['content_campaign'] = Common::hashEmptyField($template, sprintf('%s.template_content', $modelName));
        		}
        	}
			CakeSession::write($session_text, $data);

			if(!empty($mailchimp_campaign_id)){
				$old_data = $this->findById($mailchimp_campaign_id);
				$id_template = $this->filterEmptyField($old_data, 'MailchimpCampaign', 'id_template');

				if($id_template != $id){
        			CakeSession::write($session_content, $data);
        		}

        		$data['MailchimpCampaign']['is_pending'] = 1;

				$this->id = $mailchimp_campaign_id;

				$this->set($data);

				$result = $this->save();
			}

			$result = array(
	            'msg' => __('Berhasil menyimpan pengaturan template'),
	            'status' => 'success',
	        );
		}

		return $result;
	}

	function doContent($data, $value = false, $validate = false, $id = false, $save_session = true){
		$result = false;
        $user_id = Configure::read('User.id');

        if ( !empty($data) ) {
            if( empty($validate) ) {
                if( !empty($id) ) {
                    $this->id = $id;
                } else {
                    $this->create();
                }
            }
            
            $data['MailchimpCampaign']['user_id'] = $user_id;
            
            $this->set($data);
            if( $this->validates() ) {
                $flagSave = true;

                if( !empty($validate) && !empty($save_session) ) {
                	$session_text = '__Site.MailchimpCampaign.SessionName.Content';
		        	if(!empty($id)){
		        		$session_text = $session_text.'.'.$id;
		        	}
		        	
                    CakeSession::write($session_text, $data);
                } else {
                    $flagSave = $this->save();
                    $id = $this->id;
                }

                if( !empty($flagSave) ) {
                	if(!empty($id)){
                		$this->updateAll(
                			array(
                				'MailchimpCampaign.is_pending' => (int) 1
                			),
                			array(
                				'MailchimpCampaign.id' => $id
                			)
                		);
                	}

                    $result = array(
                        'msg' => __('Berhasil menyimpan konten campaign Anda'),
                        'status' => 'success',
                        'id' => $id,
                    );
                } else {
                    $result = array(
                        'msg' => __('Gagal menyimpan konten campaign Anda, mohon lengkapi semua data yang diperlukan'),
                        'status' => 'error',
                    );
                }
            } else {
                $result = array(
                    'msg' => __('Gagal menyimpan konten campaign Anda, mohon lengkapi semua data yang diperlukan'),
                    'status' => 'error',
                );
            }

            if($result['status'] == 'error'){
                $result['data'] = $data;    
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }
        
        return $result;
	}

	function replicate($id){
		$result = array(
			'msg' => __('Data campaign tidak ditemukan.'),
			'status' => 'error'
		);

		$User = Configure::read('User.data');
		$email = !empty($User['email']) ? $User['email'] : '';

		if(!empty($id)){
			$template = $this->getData('first', array(
				'conditions' => array(
					'MailchimpCampaign.id' => $id
				)
			));
			
			if(!empty($template)){
				$count_copy = $this->getData('count', array(
					'conditions' => array(
						'MailchimpCampaign.title_campaign LIKE' => '%'.$template['MailchimpCampaign']['title_campaign'].'%',
						'MailchimpCampaign.status' => 1
					)
				));

				$text_name = $template['MailchimpCampaign']['title_campaign'];

				$template['MailchimpCampaign']['title_campaign'] = sprintf('%s %s', $text_name, $count_copy+1 );

				unset($template['MailchimpCampaign']['created']);
				unset($template['MailchimpCampaign']['modified']);
				unset($template['MailchimpCampaign']['id']);
				unset($template['MailchimpCampaign']['date_send']);
				unset($template['MailchimpCampaign']['time_send']);
				
				$template['MailchimpCampaign']['type_period'] = 'directly';
				$template['MailchimpCampaign']['is_send'] = 0;
				$template['MailchimpCampaign']['is_stop'] = 0;
				$template['MailchimpCampaign']['status'] = 1;
				$template['MailchimpCampaign']['email_from'] = $email;
				$template['MailchimpCampaign']['is_pending'] = 1;

				$this->create();

				$this->set($template);

				if($this->save()){
					$msg = sprintf(__('Selamat! Anda berhasil melakukan replikasi campaign "%s".'), $text_name);
					$result = array(
						'msg' => $msg,
						'id' => $this->id,
						'status' => 'success',
						'Log' => array(
							'activity' => sprintf('Anda berhasil melakukan replikasi campaign ID #s', $this->id),
							'old_data' => $template,
							'document_id' => $this->id,
						)
					);
				}
			}
		}

		return $result;
	}

	function doStop($id){
		$campaign = $this->getData('first', array(
			'conditions' => array(
				'MailchimpCampaign.id' => $id,
				'MailchimpCampaign.is_pending' => 0,
				'MailchimpCampaign.is_send' => 0 
			)
		));

		$result = array(
			'msg' => __('Campaign tidak ditemukan'),
			'status' => 'error'
		);

		if(!empty($campaign)){
			$this->updateAll(
				array(
					'MailchimpCampaign.is_pending' => 1,
					'MailchimpCampaign.is_stop' => 1
				),
				array(
					'MailchimpCampaign.id' => $id,
					'MailchimpCampaign.is_pending' => 0,
					'MailchimpCampaign.is_send' => 0 
				)
			);

			$msg = __('Berhasil menunda kirim campaign');

			$result = array(
				'msg' => $msg,
				'status' => 'success',
				'Log' => array(
					'activity' => $msg.' ID #'.$id,
					'old_data' => $campaign,
					'document_id' => $id,
				)
			);
		}

		return $result;
	}

	function send_mail($to_email = array(), $subject_campaign, $content_campaign){
		$result = array();
		if(!empty($to_email)){
			if(is_array($to_email)){
				foreach ($to_email as $key => $email) {
					if(!empty($email)){
						$result[] = array(
	                    	'to_name' => null,
	                    	'to_email' => $email,
	                    	'subject' => $subject_campaign,
	                    	'template' => 'netral',
	                    	'data' => array(
	                    		'layout' => false,
	                    		'content' => $content_campaign
	                    	)
	                	);
					}
				}
			}else{
				$result[] = array(
                	'to_name' => null,
                	'to_email' => $to_email,
                	'subject' => $subject_campaign,
                	'template' => 'netral',
                	'data' => array(
                		'layout' => false,
                		'content' => $content_campaign
                	)
            	);
			}
		}

		return $result;
	}

	function get_string_between($string, $start, $end){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}

	function replaceCodeDate($content){
		$cek_custom_date = strstr($content, '*|DATE:');
		$cek_date = strstr($content, '*|DATE|*');

		if(!empty($cek_date)){
			$content = str_replace('*|DATE|*', date('d M Y'), $content);
		}
		
		if(!empty($cek_custom_date)){
			$content_explode = explode('*|', $content);
			
			$temp_date = array();
			foreach ($content_explode as $key => $value) {
				$flagPos = strpos($value, '|*');
				
				if(!empty($flagPos)){
					$date_format = $this->get_string_between($value, 'DATE:', '|*');
					
					if(!empty($date_format)){
						$temp_date[] = array(
							'format' => sprintf('*|DATE:%s|*', $date_format),
							'date' => date($date_format)
						);
					}
				}
			}

			if(!empty($temp_date)){
				foreach ($temp_date as $key => $value) {
					$content = str_replace($value['format'], $value['date'], $content);
				}
			}
		}
		
		return $content;
	}
}
?>