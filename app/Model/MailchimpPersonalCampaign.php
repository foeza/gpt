<?php
class MailchimpPersonalCampaign extends AppModel{
	var $name = 'MailchimpPersonalCampaign';

	var $validate = array(
		'title_campaign' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan judul Anda.',
			),
		),
		'subject_campaign' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan subjek email Anda.',
			),
		),
		'content_campaign' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan konten email Anda.',
			),
		),
		'to_email' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan email penerima.',
			),
			'multipleEmail' => array(
				'rule' => array('multipleEmail'),
				'message' => 'Format email Anda salah.',
			)
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
	var $hasMany = array(
		'MailchimpPersonalEmail' => array(
			'className' => 'MailchimpPersonalEmail',
			'foreignKey' => 'mailchimp_personal_campaign_id'
		)
	);
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
            'foreignKey' => 'user_id',
		)
	);

	function multipleEmail($data){
		$result = true;
		if(!empty($data['to_email'])){
			$emails = explode(',', $data['to_email']);

			if(is_array($emails)){
				$hostname = '(?:[_\p{L}0-9][-_\p{L}0-9]*\.)*(?:[\p{L}0-9][-\p{L}0-9]{0,62})\.(?:(?:[a-z]{2}\.)?[a-z]{2,})';
				$regex = '/^[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[\p{L}0-9!#$%&\'*+\/=?^_`{|}~-]+)*@' . $hostname . '$/ui';

				foreach ($emails as $key => $email) {
					if (!empty($email) && preg_match($regex, $email) === 1) {
					    $result = true;
					}else{
						$result = false;

						break;
					}
				}				
			}
		}else{
			$result = false;
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
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'fields'=> array(),
            'limit'=> array(),
		);

		if( !empty($mine) && !empty($mine_access)) {
            $user_login_id = Configure::read('User.id');
            $default_options['conditions']['MailchimpPersonalCampaign.user_id'] = $user_login_id;
        }

        if( !empty($company) ) {
            $parent_id = Configure::read('Principle.id');
            $default_options['conditions']['MailchimpPersonalCampaign.company_id'] = $parent_id;
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

	function doSave($data, $value = array(), $id = false, $validation = false){
		$result = false;

		if(!empty($data)){
			$data = $value;
			$text = 'mengirim';
			$data['MailchimpPersonalCampaign']['company_id'] = Configure::read('Principle.id');

			if(empty($validation)){
				if(!empty($id)){
					$this->id = $id;

					$text = 'mengubah';
				}else{
					$this->create();
				}
			}

			$email_from = !empty($data['MailchimpPersonalCampaign']['email_from']) ? $data['MailchimpPersonalCampaign']['email_from'] : false;

			$this->set($data);

			$validate = true;
			if(!empty($validation)){
				$validate = $this->validates();
			}

			if($validate){
				unset($data['MailchimpPersonalCampaign']['yes_save']);
				
				$this->set($data);

				if(empty($validation)){

					if($this->save()){
						$msg = sprintf(__('Berhasil %s email'), $text);
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

						$to_email = explode(',', $data['MailchimpPersonalCampaign']['to_email']);

						$result['SendEmail'] = $this->send_mail($email_from, $to_email, $data['MailchimpPersonalCampaign']['subject_campaign'], $data['MailchimpPersonalCampaign']['content_campaign']);
					}else{
						$result = array(
							'msg' => sprintf(__('Gagal %s email'), $text),
							'status' => 'error'
						);
					}
				}
			}else{
				$result = array(
					'msg' => sprintf(__('Gagal %s email'), $text),
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
                    MailchimpPersonalCampaign.title_campaign
                ) AGAINST(\'%s\' IN BOOLEAN MODE)', $keyword
            );

            $default_options['conditions'] = array(
                'OR' => array(
                    'MailchimpPersonalCampaign.title_campaign LIKE ' => '%'.$keyword.'%',
                    'MATCH(
                        MailchimpPersonalCampaign.title_campaign
                    ) AGAINST(? IN BOOLEAN MODE)' => $keyword,
                )
            );

            $default_options['order'] = array(
                'MailchimpPersonalCampaign.find_keyword' => 'DESC',
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
				'MailchimpPersonalCampaign.id' => $id,
			),
		));

		if ( !empty($template) ) {
			$id = Set::extract('/MailchimpPersonalCampaign/id', $template);
			
			$default_msg = __('menghapus email');

			$flag = $this->updateAll(array(
				'MailchimpPersonalCampaign.status' => 0,
			), array(
				'MailchimpPersonalCampaign.id' => $id,
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
				'msg' => __('Gagal menghapus email. Data tidak ditemukan'),
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

            $data['MailchimpPersonalCampaign']['user_id'] = $user_id;

            if(!empty($data['MailchimpPersonalCampaign']['to_email'])){
            	$to_email = explode(',', $data['MailchimpPersonalCampaign']['to_email']);

            	if(empty($to_email[count($to_email)-1])){
            		unset($to_email[count($to_email)-1]);

            		$data['MailchimpPersonalCampaign']['to_email'] = implode(',', $to_email);
            	}
            }

            $this->set($data);

            if( $this->validates() ) {
                $flagSave = true;

                if( !empty($validate) && !empty($save_session) ) {
                	$session_text = '__Site.MailchimpPersonalCampaign.SessionName.Basic';
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
                        'msg' => __('Berhasil menyimpan pengaturan email Anda'),
                        'status' => 'success',
                        'id' => $id,
                    );
                } else {
                    $result = array(
                        'msg' => __('Gagal menyimpan pengaturan email Anda, mohon lengkapi semua data yang diperlukan'),
                        'status' => 'error',
                    );
                }
            } else {
                $result = array(
                    'msg' => __('Gagal menyimpan pengaturan email Anda, mohon lengkapi semua data yang diperlukan'),
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

	function doTemplate($step_template = 'basic', $id = false, $mailchimp_personal_campaign_id = false){
		$result = array();

		if((!empty($id) && in_array($step_template, array('basic', 'saved'))) || $step_template == 'new'){
			$data['MailchimpPersonalCampaign']['type_template'] = $step_template;
			$data['MailchimpPersonalCampaign']['id_template'] = $id;

			$session_text = '__Site.MailchimpPersonalCampaign.SessionName.Template';
			$session_content_text = '__Site.MailchimpPersonalCampaign.SessionName.Content';
        	if(!empty($mailchimp_personal_campaign_id)){
        		$session_text = $session_text.'.'.$mailchimp_personal_campaign_id;
        		$session_content_text = $session_content_text.'.'.$mailchimp_personal_campaign_id;
        	}

        	$content = CakeSession::read($session_content_text);
        	if( !empty($content['MailchimpPersonalCampaign']['content_campaign']) ){
        		$content['MailchimpPersonalCampaign']['content_campaign'] = '';

        		CakeSession::write($session_content_text, $content);
        	}
        	
			CakeSession::write($session_text, $data);

			$result = array(
	            'msg' => __('Berhasil menyimpan pengaturan email template'),
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

            $data['MailchimpPersonalCampaign']['user_id'] = $user_id;
            
            $this->set($data);

            if( $this->validates() ) {
                $flagSave = true;

                if( !empty($validate) && !empty($save_session) ) {
                	$session_text = '__Site.MailchimpPersonalCampaign.SessionName.Content';
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
                        'msg' => __('Berhasil menyimpan konten Anda'),
                        'status' => 'success',
                        'id' => $id,
                    );
                } else {
                    $result = array(
                        'msg' => __('Gagal menyimpan konten Anda, mohon lengkapi semua data yang diperlukan'),
                        'status' => 'error',
                    );
                }
            } else {
                $result = array(
                    'msg' => __('Gagal menyimpan konten Anda, mohon lengkapi semua data yang diperlukan'),
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

	function send_mail($email_from, $to_email = array(), $subject_campaign, $content_campaign){
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
	                    		'content' => $content_campaign,
	                    		'from' => $email_from
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
                		'content' => $content_campaign,
                		'from' => $email_from
                	)
            	);
			}
		}

		return $result;
	}
}
?>