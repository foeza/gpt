<?php
class MailchimpListInternal extends AppModel{
	var $name = 'MailchimpListInternal';

	var $validate = array(
		'mailchimp_list_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan group email Anda.',
			),
		),
		'mailchimp_list_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan company id Anda.',
			),
		),
		'group_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan group user Anda.',
			),
		),
	);

	var $belongsTo = array(
		'MailchimpList' => array(
			'className' => 'MailchimpList',
			'foreignKey' => 'mailchimp_list_id',
		),
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ) {
		$company = isset($elements['company']) ? $elements['company']:true;
		$default_options = array(
			'conditions'=> array(
				'MailchimpListInternal.status' => 1,
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

            $default_options['conditions']['MailchimpListInternal.user_company_config_id'] = $user_company_config_id;
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
						$msg = sprintf(__('Berhasil %s grup email'), $text);
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
					}else{
						$result = array(
							'msg' => sprintf(__('Gagal %s grup email'), $text),
							'status' => 'error'
						);
					}
				}
			}else{
				$result = array(
					'msg' => sprintf(__('Gagal %s grup email'), $text),
					'status' => 'error'
				);
			}
		}else if(!empty($value)){
			$result['data'] = $value;
		}

		return $result;
	}
}
?>