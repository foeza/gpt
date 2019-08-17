<?php
class ActivityUser extends AppModel {
	var $validate = array(
		'action_date' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Tgl aktivitas harap dipilih',
			),
		),
		'user_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Agen harap dipilih',
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
		'value' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nilai pencapaian harap diisi',
			),
		),
		'reason' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukan alasan & keterangan',
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
		'ExpertCategoryComponentActive' => array(
			'className' => 'ExpertCategoryComponentActive',
			'foreignKey' => 'expert_category_component_active_id',
		),
		'ViewExpertCategoryCompanyDetail' => array(
			'className' => 'ViewExpertCategoryCompanyDetail',
			'foreignKey' => false,
			'conditions' => array(
				'ActivityUser.expert_category_component_active_id = ViewExpertCategoryCompanyDetail.expert_category_component_active_id',
			),
		),
		'Activity' => array(
			'className' => 'Activity',
			'foreignKey' => 'activity_id',
		),
		'ActivityCategoryPoint' => array(
			'className' => 'ActivityCategoryPoint',
			'foreignKey' => 'activity_id',
		),
	);

	function beforeSave($options = array()){
		$principle_id = Common::hashEmptyField($this->data, 'ActivityUser.principle_id');
		$company_id = Common::hashEmptyField($this->data, 'ActivityUser.company_id');

		if( empty($principle_id) ) {
			$principle_id = Configure::read('Principle.id');
			$this->data = Hash::insert($this->data, 'ActivityUser.principle_id', $principle_id);
		}

		if( empty($company_id) ) {
			$company_id = Configure::read('Config.Company.data.UserCompany.id');
			$this->data = Hash::insert($this->data, 'ActivityUser.user_company_id', $company_id);
		}
	}

	function getData( $find = 'all', $options = array(), $elements = array() ){
    	$status = Common::hashEmptyField($elements, 'status', 'active');
    	$allow = Common::hashEmptyField($elements, 'allow');
    	$company = Common::hashEmptyField($elements, 'company', true, array(
    		'isset' => true,
		));
    	$mine = Common::hashEmptyField($elements, 'mine', true, array(
    		'isset' => true,
		));
        $adminRumahku = Configure::read('User.Admin.Rumahku');

		$default_options = array(
			'conditions' => array(
				'ActivityUser.status' => 1
			),
			'order' => array(
				'ActivityUser.created' => 'DESC',
			),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
            'limit' => array(),
            'offset' => array(),
		);
        switch ($status) {
            case 'active':
                $default_options['conditions']['ActivityUser.active'] = 1;
                break;
            case 'inactive':
                $default_options['conditions']['ActivityUser.active'] = 0;
                break;
            case 'confirm':
                $default_options['conditions']['ActivityUser.active'] = 1;
                $default_options['conditions']['ActivityUser.activity_status'] = array( 'confirm' );
                break;
        }

        if( !empty($mine) ) {
            $user_admin = Configure::read('User.admin');
            $user_login_id = Configure::read('User.id');

            $data_arr = $this->User->getUserParent($user_login_id);
			$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
			$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

            if( (empty($user_admin) && empty($adminRumahku)) || !empty($is_sales) ) {
                $default_options['conditions']['ActivityUser.user_id'] = $user_ids;
                $company = false;
            } else {
            	$company = true;
            }
        }

        if( !empty($company) ) {
            $principle_id = Configure::read('Principle.id');
            $default_options['conditions']['ActivityUser.principle_id'] = $principle_id;
        }

        if( $allow == 'edit' ) {
			$isAdmin = Configure::read('User.companyAdmin');

			if( !empty($isAdmin) ) {
				$default_options['conditions']['ActivityUser.activity_status'] = array( 'pending', 'approved' );
			} else {
				$default_options['conditions']['ActivityUser.activity_status'] = 'pending';
			}
        }

		return $this->merge_options($default_options, $options, $find);
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$sort = Common::hashEmptyField($data, 'named.sort');
		$period_year = Common::hashEmptyField($data, 'named.period_year');
		$period_month = Common::hashEmptyField($data, 'named.period_month');

		if( !empty($period_year) && !empty($period_month) ) {
			$periode = __('%s-%s', $period_year, $period_month);
			$data = Hash::insert($data, 'named.periode', Common::formatDate($periode, 'Y-m'));
		}

		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'date_from' => array(
				'field' => 'DATE_FORMAT(ActivityUser.created, \'%Y-%m-%d\') >=',
			),
			'date_to' => array(
				'field' => 'DATE_FORMAT(ActivityUser.created, \'%Y-%m-%d\') <=',
			),
			'document_date_from' => array(
				'field' => 'DATE_FORMAT(ActivityUser.action_date, \'%Y-%m-%d\') >=',
			),
			'document_date_to' => array(
				'field' => 'DATE_FORMAT(ActivityUser.action_date, \'%Y-%m-%d\') <=',
			),
			'periode' => array(
				'field' => 'DATE_FORMAT(ActivityUser.action_date, \'%Y-%m\') <=',
			),
			'name' => array(
				'field'=> 'CONCAT(User.first_name, " ", IFNULL(User.last_name, ""))',
				'type' => 'like',
				'contain' => array(
					'User',
				),
			),
			'category' => array(
				'field'=> 'ExpertCategory.name',
				'type' => 'like',
				'contain' => array(
					'ExpertCategory',
				),
			),
			'component' => array(
				'field'=> 'ViewExpertCategoryCompanyDetail.name',
				'type' => 'like',
				'contain' => array(
					'ViewExpertCategoryCompanyDetail',
				),
			),
			'status' => array(
				'field'=> 'ActivityUser.activity_status',
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

	public function doSave( $data ) {
		$result = false;

		if ( !empty($data) ) {
			$dataSave = Common::hashEmptyField($data, 'ActivityUser');

			if ( $this->saveAll($dataSave) ) {
				// $id = $this->id;
				$msg = __('Berhasil menyimpan aktivitas user');

				$result = array(
					// 'id' => $id,
					'data' => $data,
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						// 'old_data' => $value,
						// 'document_id' => $id,
					),
				);
			} else {
				$msg = __('Gagal menyimpan aktivitas user');
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'data' => $data,
					'Log' => array(
						'activity' => $msg,
						// 'old_data' => $value,
						// 'document_id' => $id,
						'error' => 1,
					),
				);
			}
		}

		return $result;
	}

	function doDelete( $id, $elements = array( 'allow' => 'edit' ) ) {	
		$result = false;
		$isAdmin = Configure::read('User.companyAdmin');

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'ActivityUser.id' => $id,
			),
		);

		$value = $this->getData('all', $options, $elements);

		$default_msg = __('menghapus aktivitas');

		if ( !empty($value) ) {
			$flag = $this->updateAll(array(
				'ActivityUser.status' => 0,
			), array(
				'ActivityUser.id' => $id,
			));

            if( $flag ) {
				$msg = __('Berhasil %s', $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $id,
					),
				);
            } else {
				$result = array(
					'msg' => __('Gagal %s', $default_msg),
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal %s. Data tidak ditemukan', $default_msg),
				'status' => 'error',
			);
		}

		return $result;
	}

	function doStatus( $id, $data, $msg, $activity_status ) {	
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$value = $this->getData('all', array(
        	'conditions' => array(
				'ActivityUser.id' => $id,
				'ActivityUser.activity_status' => array( 'pending', 'approved' ),
			),
		));
		$default_msg = $msg;

		if ( !empty($value) ) {
			if( !empty($data) ) {
				$flag = $this->saveAll($data, array(
					'validate' => 'only',
				));
			} else {
				$flag = true;
			}
			
			if( !empty($flag) ) {
            	$user_login_id = Configure::read('User.id');
				$reason = Common::hashEmptyField($data, 'ActivityUser.reason');

				$flag = $this->updateAll(array(
					'ActivityUser.process_by' => $user_login_id,
					'ActivityUser.reason' => "'".$reason."'",
					'ActivityUser.activity_status' => "'".$activity_status."'",
				), array(
					'ActivityUser.id' => $id,
				));

	            if( $flag ) {
					$msg = __('Berhasil %s', $default_msg);
	                $result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $id,
						),
					);
	            } else {
					$result = array(
						'msg' => __('Gagal %s', $default_msg),
						'status' => 'error',
					);
				}
			} else {
				$result = array(
					'msg' => __('Gagal %s', $default_msg),
					'status' => 'error',
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal %s. Data tidak ditemukan', $default_msg),
				'status' => 'error',
			);
		}

		return $result;
	}
}
?>