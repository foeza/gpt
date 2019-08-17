<?php
class MailchimpList extends AppModel{
	var $name = 'MailchimpList';

	var $validate = array(
		'name_group' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama grup email Anda.',
			),
		),
		'description' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan deksripsi grup email Anda.',
			),
		),
		'flag_internal' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih group user minimal 1.',
			),
		),
	);

	var $hasMany = array(
		'MailchimpListDetail' => array(
			'className' => 'MailchimpListDetail',
			'foreignKey' => 'mailchimp_list_id',
		),
		'MailchimpListInternal' => array(
			'className' => 'MailchimpListInternal',
			'foreignKey' => 'mailchimp_list_id',
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ) {
		$company = isset($elements['company']) ? $elements['company']:true;
		$default_options = array(
			'conditions'=> array(
				'MailchimpList.status' => 1,
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

            $default_options['conditions']['MailchimpList.user_company_config_id'] = $user_company_config_id;
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
					$data['MailchimpList']['id'] = $id;

					$text = 'mengubah';
				}
			}

			$flag = $this->saveAll($data, array(
				'validate' => 'only',
				'deep' => true,
			));

			if($flag){
				if(empty($validation)){

					if(!empty($id)){
						$this->MailchimpListInternal->deleteAll(array(
							'MailchimpListInternal.mailchimp_list_id' => $id,
						));
					}					

					if($this->saveAll($data, array(
						'deep' => true,
					))){
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
						$msg = sprintf(__('Gagal %s grup email'), $text);

						$result = array(
							'msg' => $msg,
							'status' => 'error',
							'Log' => array(
								'activity' => $msg,
								'data' => $data,
								'error' => 1,
							),
						);
					}
				}
			}else{
				$msg = sprintf(__('Gagal %s grup email'), $text);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'validationErrors' => $this->validationErrors,
					'Log' => array(
						'activity' => $msg,
						'data' => $data,
						'error' => 1,
					),
				);
			}
		}else if(!empty($value)){

			$mailchimpListInternals = Common::hashEmptyField($value, 'MailchimpListInternal');

			if(!empty($mailchimpListInternals)){
				$value = Common::_callUnset($value, array(
					'MailchimpListInternal',
				));

				$lists = Hash::combine($mailchimpListInternals, '{n}.MailchimpListInternal.group_id', '{n}.MailchimpListInternal.group_id');

				if(!empty($lists)){
					$value['MailchimpListInternal']['id'] = $lists;
				}
			}

			$result['data'] = $value;
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));
        $name_group = $this->filterEmptyField($data, 'named', 'name_group', false, array(
            'addslashes' => true,
        ));
        $description = $this->filterEmptyField($data, 'named', 'description', false, array(
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
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));

        if( !empty($keyword) ) {
            $default_options['conditions']['OR'] = array(
                'MailchimpList.name_group LIKE ' => '%'.$keyword.'%',
                'MailchimpList.description LIKE ' => '%'.$keyword.'%',
            );
        }
		if( !empty($name_group) ) {
			$default_options['conditions']['MailchimpList.name_group LIKE'] = '%'.$name_group.'%';
		}
		if( !empty($description) ) {
			$default_options['conditions']['MailchimpList.description LIKE'] = '%'.$description.'%';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(MailchimpList.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(MailchimpList.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(MailchimpList.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(MailchimpList.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
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
			$grup = $this->getData('all', array(
	        	'conditions' => array(
					'MailchimpList.id' => $id,
				),
			));

			if ( !empty($grup) ) {
				$id = Set::extract('/MailchimpList/id', $grup);
				
				$default_msg = __('menghapus grup email');

				$flag = $this->updateAll(array(
					'MailchimpList.status' => 0,
				), array(
					'MailchimpList.id' => $id,
				));

				$id = implode(', ', $id);

	            if( $flag ) {
					$msg = sprintf(__('Berhasil %s dengan ID %s'), $default_msg, $id);
	                $result = array(
						'msg' => sprintf(__('Berhasil %s'), $default_msg),
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $grup,
						),
					);
	            } else {
					$msg = sprintf(__('Gagal %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'error',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $grup,
							'error' => 1,
						),
					);
				}
			} else {
				$result = array(
					'msg' => __('Gagal menghapus grup email. Data tidak ditemukan'),
					'status' => 'error',
				);
			}

			return $result;
		}
}
?>