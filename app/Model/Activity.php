<?php
class Activity extends AppModel {
	var $name = 'Activity';
	var $displayField = 'name';
	var $validate = array(
		'action_date' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Tgl aktivitas harap dipilih',
			),
		),
		'expert_category_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Kategori aktivitas harap dipilih',
			),
		),
		'expert_category_component_active_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Komponen harap dipilih',
			),
		),
		'type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jenis perolehan harap dipilih',
			),
		),
		'point_type' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Tipe poin harap dipilih',
			),
		),
		// 'value' => array(
		// 	'notempty' => array(
		// 		'rule' => array('notempty'),
		// 		'message' => 'Nilai pencapaian harap diisi',
		// 	),
		// ),
		'flag_user' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon pilih user yang melakukan aktivitas',
			),
		),
	);

	var $belongsTo = array(
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => 'user_company_id',
		),
		'ExpertCategory' => array(
			'className' => 'ExpertCategory',
			'foreignKey' => 'expert_category_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	var $hasMany = array(
		'ActivityUser' => array(
			'className' => 'ActivityUser',
			'foreignKey' => 'activity_id',
		),
		'ViewActivityUser' => array(
			'className' => 'ViewActivityUser',
			'foreignKey' => 'activity_id',
		),
		'ActivityCategoryPoint' => array(
			'className' => 'ActivityCategoryPoint',
			'foreignKey' => 'activity_id',
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
    	$status = Common::hashEmptyField($elements, 'status', 'active');
    	$company = Common::hashEmptyField($elements, 'company', true, array(
    		'isset' => true,
		));
    	$mine = Common::hashEmptyField($elements, 'mine', true, array(
    		'isset' => true,
		));
        $adminRumahku = Configure::read('User.Admin.Rumahku');

		$default_options = array(
			'conditions' => array(
				'Activity.status' => 1
			),
			'order' => array(
				'Activity.created' => 'DESC',
			),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
            'limit' => array(),
		);
        switch ($status) {
            case 'active':
                $default_options['conditions']['Activity.active'] = 1;
                break;
            case 'inactive':
                $default_options['conditions']['Activity.active'] = 0;
                break;
        }

        if( !empty($company) ) {
            $principle_id = Configure::read('Principle.id');
            $default_options['conditions']['Activity.principle_id'] = $principle_id;
        }

		return $this->merge_options($default_options, $options, $find);
	}

	function beforeSave($options = array()){
		$id = $this->id;
		$id = Common::hashEmptyField($this->data, 'Activity.id', $id);

		if( empty($id) ) {
			$company_id = Configure::read('Config.Company.data.UserCompany.id');
			$principle_id = Configure::read('Principle.id');

			$this->data = Hash::insert($this->data, 'Activity.principle_id', $principle_id);
			$this->data = Hash::insert($this->data, 'Activity.user_company_id', $company_id);
		}
	}

	public function doSave( $data, $value = false ) {
		$result = false;
		$id = Common::hashEmptyField($data, 'Activity.id');

		if ( !empty($data) ) {
			if ( $this->saveAll($data) ) {
				$id = $this->id;
				$msg = __('Berhasil menyimpan aktivitas user');

				$result = array(
					'id' => $id,
					'data' => $data,
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $value,
						'document_id' => $id,
					),
					// 'Notification' => array(
     //                    'user_id' => $agent_id,
     //                    'name' => sprintf(__('Klien %s telah ditambahkan kedalam Project CRM Anda oleh %s'), $client_name, $user_login_name),
     //                    'link' => array(
     //                        'controller' => 'crm',
     //                        'action' => 'project_detail',
     //                        $id,
     //                        'admin' => true,
     //                    ),
     //                ),
     //                'SendEmail' => array(
     //                    'to_name' => $agent_name,
     //                    'to_email' => $agent_email,
     //                    'subject' => sprintf(__('CRM Project Klien %s'), $client_name),
     //                    'template' => 'crm_notice',
     //                    'data' => $data,
     //                ),
				);
			} else {
				$msg = __('Gagal menyimpan aktivitas user');
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
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));
        $status = $this->filterEmptyField($data, 'named', 'status', false, array(
            'addslashes' => true,
        ));
		$sort = Common::hashEmptyField($data, 'named.sort');

		if( !empty($keyword) ) {
			$properties = $this->Property->getData('list', array(
				'conditions' => array(
					'Property.mls_id LIKE' => '%'.$keyword.'%',
				),
				'fields' => array(
					'Property.id', 'Property.id',
				),
				'limit' => 50,
			), array(
				'admin_mine' => true,
				'skip_is_sales' => true,
			));
			$users = $this->User->getData('list', array(
				'conditions' => array(
					'OR' => array(
						'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE' => '%'.$keyword.'%',
						'User.email LIKE' => '%'.$keyword.'%',
					),
				),
				'fields' => array(
					'User.id', 'User.id',
				),
				'limit' => 50,
			), array(
				'company' => true,
			));

			$default_options['conditions']['OR'] = array(
				'CrmProject.name LIKE' => '%'.$keyword.'%',
				'CrmProject.property_id' => $properties,
				'CrmProject.user_id' => $users,
			);
		}
		if( !empty($status) ) {
			$attributeSet = $this->AttributeSet->getData('first', array(
				'conditions' => array(
					'AttributeSet.slug' => $status,
					'AttributeSet.scope' => 'crm',
				),
			));
			$attribute_set_id = !empty($attributeSet['AttributeSet']['id'])?$attributeSet['AttributeSet']['id']:false;

			if( !empty($attribute_set_id) ) {
				$default_options['conditions']['CrmProject.attribute_set_id'] = $attribute_set_id;
			}
		}

		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'date_from' => array(
				'field' => 'DATE_FORMAT(CrmProject.created, \'%Y-%m-%d\') >=',
			),
			'date_to' => array(
				'field' => 'DATE_FORMAT(CrmProject.created, \'%Y-%m-%d\') <=',
			),
			'date_range_from' => array(
				'field' => 'DATE_FORMAT(CrmProject.project_date, \'%Y-%m-%d\') >=',
			),
			'date_range_to' => array(
				'field' => 'DATE_FORMAT(CrmProject.project_date, \'%Y-%m-%d\') <=',
			),
			'modified_from' => array(
				'field' => 'DATE_FORMAT(CrmProject.modified, \'%Y-%m-%d\') >=',
			),
			'modified_to' => array(
				'field' => 'DATE_FORMAT(CrmProject.modified, \'%Y-%m-%d\') <=',
			),
			'name' => array(
				'field'=> 'CrmProject.name',
				'type' => 'like',
			),
			'attribute_set_id' => array(
				'field'=> 'CrmProject.attribute_set_id',
			),
			'property' => array(
				'contain' => array(
					'ViewProperty',
				),
				'field' => array(
					'OR' => array(
						'ViewProperty.label',
						'ViewProperty.mls_id',
					),
				),
				'type' => 'like',
			),
			'agent_name' => array(
				'field'=> 'CONCAT(Agent.first_name, " ", IFNULL(Agent.last_name, ""))',
				'type' => 'like',
				'contain' => array(
					'Agent',
				),
			),
			'agent_email' => array(
				'field'=> 'Agent.email',
				'type' => 'like',
				'contain' => array(
					'Agent',
				),
			),
			'client_name' => array(
				'contain' => array(
					'ViewUserClient',
				),
				'field' => 'ViewUserClient.full_name',
				'type' => 'like',
			),
			'client_email' => array(
				'contain' => array(
					'ViewUserClient',
				),
				'field' => 'ViewUserClient.email',
				'type' => 'like',
			),
			'client_no_hp' => array(
				'contain' => array(
					'ViewUserClient',
				),
				'field' => 'ViewUserClient.no_hp',
				'type' => 'like',
			),
			'document_date_from' => array(
				'field' => 'DATE_FORMAT(CrmProject.completed_date, \'%Y-%m-%d\') >=',
			),
			'document_date_to' => array(
				'field' => 'DATE_FORMAT(CrmProject.completed_date, \'%Y-%m-%d\') <=',
			),
		));

		if( !empty($sort) ) {
        	$sortAgent = strpos($sort, 'Agent.');
        	$sortClient = strpos($sort, 'ViewUserClient.');
        	$sortAttributeSet = strpos($sort, 'AttributeSet.');
        	$sortViewProperty = strpos($sort, 'ViewProperty.');

        	if( is_numeric($sortAgent) ) {
	            $default_options['contain'][] = 'Agent';
	        }
	        if( is_numeric($sortClient) ) {
	            $default_options['contain'][] = 'ViewUserClient';
	        }
	        if( is_numeric($sortAttributeSet) ) {
	            $default_options['contain'][] = 'AttributeSet';
	        }
	        if( is_numeric($sortViewProperty) ) {
	            $default_options['contain'][] = 'ViewProperty';
	        }
        }
        if( $sort== 'CrmProject.crmprojectactivity_count' ) {
            $default_options = $this->callBindHasMany('CrmProjectActivity', $default_options, 'crm_project_id');
        }

		return $default_options;
	}
}
?>