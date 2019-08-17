<?php

App::uses('AppModel', 'Model');

class EbrochureTemplate extends AppModel{
	public $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id', 
		), 
		'Principle' => array(
			'className'		=> 'User', 
			'foreignKey'	=> 'principle_id', 
		), 
	);

	public $validate = array(
		'name'	=> array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan nama',
			),
		),
		'layout'	=> array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Mohon masukkan layout',
			),
		),
	);

	public function _callRefineParams($data = null, $defaultOptions = array()){
		$modelAlias		= $this->alias;
		$defaultOptions = $this->defaultOptionParams($data, $defaultOptions, array(
			'keyword' => array(
				'type'		=> 'like',
				'contain'	=> 'User', 
				'field'		=> array(
					'OR' => array(
						$modelAlias.'.name',
						$modelAlias.'.description',
						'TRIM(CONCAT(TRIM(User.first_name), " ", TRIM(User.last_name)))', 
					),
				),
			),
			'name' => array(
				'type'	=> 'like',
				'field'	=> $modelAlias.'.name',
			),
			'description' => array(
				'type'	=> 'like',
				'field'	=> $modelAlias.'.description',
			),
			'user_fullname' => array(
				'type'		=> 'like',
				'contain'	=> 'User', 
				'field'		=>  array(
					'OR' => array(
						$modelAlias.'.name',
						$modelAlias.'.description',
						'TRIM(CONCAT(TRIM(User.first_name), " ", TRIM(User.last_name)))', 
					),
				),
			), 
			'date_from' => array(
				'field' => 'DATE_FORMAT('.$modelAlias.'.created, \'%Y-%m-%d\') >=',
			),
			'date_to' => array(
				'field' => 'DATE_FORMAT('.$modelAlias.'.created, \'%Y-%m-%d\') <=',
			),
			'modified_from' => array(
				'field' => 'DATE_FORMAT('.$modelAlias.'.modified, \'%Y-%m-%d\') >=',
			),
			'modified_to' => array(
				'field' => 'DATE_FORMAT('.$modelAlias.'.modified, \'%Y-%m-%d\') <=',
			),
		));

		$sort = Common::hashEmptyField($data, 'named.sort');

		if($sort){
			$sort		= explode('.', trim($sort));
			$sortModel	= Common::hashEmptyField($sort, 0);
			$sortField	= Common::hashEmptyField($sort, 1);

			if($sortModel){
				$defaultOptions['contain'][] = $sortModel;
			}
		}

		return $defaultOptions;
	}

	public function doSave($data = array()){
		$data		= (array) $data;
		$status		= 'error';
		$message	= 'Data yang Anda masukkan tidak valid';

		$recordID = Common::hashEmptyField($data, $this->alias.'.id');

		if($data){
			if($this->saveAll($data)){
				$recordID	= $this->id;
				$data		= $this->read(null, $recordID);
				$status		= 'success';
				$message	= __('Berhasil menyimpan data');
			}
			else{
				$message = __('Gagal menyimpan data');
			}
		}

		$result = array(
			'status'	=> $status,
			'msg'		=> $message,
			'data'		=> $data, 
			'id'		=> $recordID,
			'Log'		=> array(
				'document_id'	=> $recordID,
				'activity'		=> $message,
				'error'			=> $status == 'error', 
			),
		);

		if($this->validationErrors){
			$result = Hash::insert($result, 'validationErrors', $this->validationErrors);
		}

		return $result;
	}

	public function getData($find = 'all', $options = array(), $elements = array()){
		$authUserID		= Common::config('User.id');
		$principleID	= Common::config('Principle.id');
    	$isAdmin 		= Configure::read('User.admin');

		$defaultOptions	= array(
			'conditions'	=> array(),
			'contain'		=> array(),
			'fields'		=> array(),
			'group'			=> array(),
			'order'			=> array(
				$this->alias.'.principle_id' => 'DESC', 
				$this->alias.'.id' => 'DESC', 
			),
		);

		$status		= Common::hashEmptyField($elements, 'status', 'active');
		$company	= Common::hashEmptyField($elements, 'company', false);

		if(in_array($status, array('active', 'inactive'))){
			$defaultOptions['conditions'][$this->alias.'.status'] = $status == 'active';
		}

		if($company){
			if( !empty($isAdmin) ){
				$data_arr = $this->User->getUserParent($authUserID);
				$is_sales = Common::hashEmptyField($data_arr, 'is_sales');


				if( !empty($is_sales) ) {
					$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
					$defaultOptions['conditions'][]['OR'] = array(
						array(
							$this->alias.'.user_id' => $user_ids,
							$this->alias.'.principle_id' => $principleID,
						),
						array(
							'User.group_id NOT' => Configure::read('__Site.Role.agent'),
							$this->alias.'.user_id NOT' => $user_ids,
							$this->alias.'.principle_id' => $principleID,
							//	made by user (pake or jadi support personal page)
						),
						array(
							$this->alias.'.principle_id' => 0,
						),
					);
					$defaultOptions['contain'][] = 'User';
				} else {
					$defaultOptions['conditions'][$this->alias.'.principle_id'] = array(0, $principleID);
				}
			}
			else{
				$defaultOptions['conditions'][]['OR'] = array(
					array(
						$this->alias.'.user_id' => $authUserID,
						$this->alias.'.principle_id' => $principleID,
						//	made by user (pake or jadi support personal page)
					),
					array(
						'User.group_id NOT' => Configure::read('__Site.Role.agent'),
						$this->alias.'.user_id <>' => $authUserID,
						$this->alias.'.principle_id' => $principleID,
						//	made by user (pake or jadi support personal page)
					),
					array(
						$this->alias.'.principle_id' => 0,
						//	made by prime admin / company admin
					),
				);
				$defaultOptions['contain'][] = 'User';
			}
		}

		return $this->merge_options($defaultOptions, $options, $find);
	}

	public function doToggle($recordID = null, $type = 'delete', $elements = array()){	
		$status		= 'error';
		$message	= __('Data yang Anda masukkan tidak valid');

		if($recordID){
			$recordID = is_array($recordID) ? array_filter($recordID) : $recordID;
			$elements = array_replace(array(
				'company' => true, 
			), $elements);

			$conditions = array($this->alias.'.id' => $recordID);
			$records	= $this->getData('all', array('conditions' => $conditions), $elements);

			if($records){
				$validTypes = array(
					'delete', 
					'restore', 
					'activate', 
					'disable', 
				);

				$type = in_array($type, $validTypes) ? $type : 'delete';
				$data = array();

				if(in_array($type, array('delete', 'restore'))){
					$action	= $type == 'delete' ? 'menghapus' : 'mengembalikan';
					$value	= $type == 'delete' ? 0 : 1;
					$data	= array(
						sprintf('%s.status', $this->alias) => $value, 
					);
				}

				$recordName = Hash::extract($records, sprintf('{n}.%s.name', $this->alias));
				$recordName = implode(', ', $recordName);

				$flag		= $this->updateAll($data, $conditions);
				$status		= $flag ? 'success' : 'error';
				$message	= $flag ? 'Berhasil' : 'Gagal';

				if($recordName){
					$message = __('%s %s %s', $message, $action, $recordName);
				}
				else{
					$message = __('%s %s data terpilih', $message, $action);
				}
			}
			else{
				$message = __('Data tidak ditemukan');
			}
		}

		$result = array(
			'status'	=> $status, 
			'msg'		=> $message, 
			'data'		=> $recordID, 
			'Log'		=> array(
				'activity' => $message,
				'old_data' => $recordID,
			),
		);

		return $result;
	}
}