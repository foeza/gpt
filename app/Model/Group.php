<?php

App::uses('AppModel', 'Model');

class Group extends AppModel {
    public $name = 'Group';
    public $useTable = "groups";
    public $actsAs = array(
    	'Acl' => array('type' => 'requester'),
    );

    public function parentNode() {
	    return null;
	}

    var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama group harap diisi'
			)
		)
	);

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);
	public $hasMany = array(
		'GroupCompany' => array(
			'className' => 'GroupCompany',
			'foreignKey' => 'group_id',
			// 'dependent' => false,
		),
		'GroupTarget' => array(
			'className' => 'GroupTarget',
			'foreignKey' => 'group_id',
		),
	);

    /**
	* 	@param string $find - all, list, paginate, count
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	*		string count - Pick jumah data
	*		string paginate - Pick opsi query
	* 	@param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
	* 	@return result
	*/
    function getData( $find = 'all', $options = array(), $elements = array() ){
		$is_admin_prime = Configure::read('User.Admin.Rumahku');
    	$role = $this->filterEmptyField($elements, 'role');
    	$group_id = $this->filterEmptyField($elements, 'group_id');
    	$company = Common::hashEmptyField($elements, 'company', false, array(
    		'isset' => false,
		));
  //   	$status = Common::hashEmptyField($elements, 'status', 'active', array(
  //   		'isset' => true,
		// ));

		$default_options = array(
			'conditions'=> array(
				'Group.status' => 1, 
			),
		);

		switch ($role) {
			case 'adminRku':
				$default_options['conditions']['Group.user_id'] = false;
				$default_options['conditions']['Group.is_prime'] = false;
				$default_options['conditions']['Group.id > '] = 10;
				break;
			case 'internal':
				$group_id = !empty($group_id) ? $group_id : Configure::read('Config.Company.data.User.group_id');

				$default_groups = array( 2, 5, 10);
				if($group_id){
					switch ($group_id) {
						case '4':
							$default_groups[] = 3;
							$default_groups = array_diff($default_groups, array(10));
							break;
					}
				}

				$default_options['conditions']['Group.id'] = $default_groups;
				break;
		}

		switch (!empty($status)) {
			case 'active':
				$default_options['conditions'][$this->alias.'.active'] = 1;
				break;
			case 'inactive':
				$default_options['conditions'][$this->alias.'.active'] = 0;
				break;
			// case 'all':
			// 	$default_options['conditions'][$this->alias.'.active'] = array(0,1);
			// 	break;
		}

		// if( !empty($company) && empty($is_admin_prime) ){
		if( !empty($company) ){
			$user_company_id = Configure::read('Principle.id');
			$default_options['conditions'][]['OR'] = array(
				array(
					$this->alias.'.user_id' => $user_company_id,
				),
				array(
					$this->alias.'.id' => array( 2,5 ),
					$this->alias.'.user_id' => 0,
				),
			);
		}

		if(!empty($options)){
			$default_options = array_merge_recursive($default_options, $options);
		}
		
		$default_options = $this->_callFieldForAPI($find, $default_options);

		if( $find == 'paginate' ) {			
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
					'Group.id',
					'Group.name',
				);
			}
		}

		return $options;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$pass_user_id = Common::hashEmptyField($data, 'pass', Configure::read('Principle.id'), array(
			'type' => 'array_shift',
		));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $user_id = $this->filterEmptyField($data, 'named', 'user_id', $pass_user_id, array(
            'addslashes' => true,
        ));
        $parent = $this->filterEmptyField($data, 'named', 'parent', false, array(
            'addslashes' => true,
        ));

        $sortParent = strpos($sort, 'GroupParent.');

        if(!empty($parent) || is_numeric($sortParent)){
        	$this->unbindModel(array(
				'hasMany' => array(
					'GroupCompany', 
				), 
			));

			if( empty($user_id) ) {
				$user_id = Configure::read('Principle.id');
			}

			$groupCompanyConditions = array(
				'GroupCompany.user_id' => $user_id,
			);

			$this->bindModel(array(
				'hasOne' => array(
					'GroupCompany' => array(
						'foreignKey' => 'group_id', 
						'conditions' => $groupCompanyConditions, 
					), 
					'GroupParent' => array(
						'className' => 'Group',
						'foreignKey' => false,
						'conditions' => array(
							'GroupCompany.parent_id = GroupParent.id', 
						), 
					), 
				), 
			), false);
        }

  //       if(!empty($parent)){
  //       	$parent_options = $default_options;
  //       	$parent_options['conditions']['name like'] = '%'.$parent.'%';
  //       	$parent_options['fields'] = array(
  //       		'Group.id', 'Group.id'
  //       	);
		// 	$group_ids = $this->getData('list', $parent_options);

		// 	if(!empty($group_ids)){
		// 		$parent_ids = $this->GroupCompany->getData('list', array(
		// 			'conditions' => array(
		// 				'GroupCompany.parent_id' => $group_ids,
		// 			),
		// 			'fields' => array(
		// 				'GroupCompany.group_id', 'GroupCompany.group_id'
		// 			),
		// 		));
		// 		$default_options['conditions']['Group.id'] = $parent_ids;
		// 	}
		// }

		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'name' => array(
				'field'=> 'Group.name',
				'type' => 'like',
			),
			'active' => array(
				'field' => 'Group.active',
				'type' => 'boolean',
			),
			'modified_from' => array(
				'field' => 'DATE_FORMAT(Group.modified, \'%Y-%m-%d\') >=',
			),
			'modified_to' => array(
				'field' => 'DATE_FORMAT(Group.modified, \'%Y-%m-%d\') <=',
			),
			'date_from' => array(
				'field' => 'DATE_FORMAT(Group.created, \'%Y-%m-%d\') >=',
			),
			'date_to' => array(
				'field' => 'DATE_FORMAT(Group.created, \'%Y-%m-%d\') <=',
			),
			'parent' => array(
				'field'=> 'GroupParent.name',
				'type' => 'like',
				'contain' => array(
					'GroupCompany',
					'GroupParent',
				),
			),
			'user_id' => array(
				'field'=> 'Group.user_id',
			),
		));

		if( !empty($sort) ) {
			if( is_numeric($sortParent) ) {
	            $default_options['contain'][] = 'GroupCompany';
	            $default_options['contain'][] = 'GroupParent';
	        }

        	// if( is_numeric($sortUser) ) {
	        //     $default_options['contain'][] = 'User';
	        // }
        }
		return $default_options;
	}

    function getMerge( $data, $group_id, $is_root = false ){
    	if( empty($data['Group']) && !empty($group_id) ) {
			$group = $this->getData('first', array(
				'conditions'=> array(
					'Group.id' => $group_id, 
					'Group.status' => 1, 
				),
			));

			if( !empty($group) && !$is_root ){
				$data = array_merge($data, $group);
			}
		}

        return $data;
	}

	function doSave($data, $id = false, $user_company_id = false){
		if(!empty($id)){
			$data['Group']['id'] = $id;
		}

		$this->set($data);

		if(!empty($data)){
			$flag = $this->saveAll($data, array(
				'validate' => 'only',
			));

			if($flag){
				if( !empty($id) ) {
					$user_id = Set::extract($data, '/GroupCompany/user_id');
					$user_id = !empty($user_id) ? $user_id : $user_company_id;

					$this->GroupCompany->deleteAll(array(
						'GroupCompany.user_id' => $user_id,
						'GroupCompany.group_id' => $id,
					));
				}

				if($this->saveAll($data)){
					$id = $this->id;
					$msg = __('Berhasil menyimpan divisi'); 

					$result = array(
						'status' => 'success',
						'msg' => $msg,
						'Log' => array(
							'activity' => $msg,
							'document_id' => $id,
						),
						'id' => $id,
					);
				}else{
					$msg = __('Gagal menyimpan divisi');

					$result = array(
						'status' => 'error',
						'msg' => $msg,
						'Log' => array(
							'activity' => $msg,
							'data' => $data,
							'error' => 1
						),
					);
				}
			}else{
				$msg = __('Gagal menyimpan divisi');
				$result = array(
					'status' => 'error',
					'msg' => $msg,
					'Log' => array(
						'activity' => $msg,
						'data' => $data,
						'error' => 1
					),
				);
			}
		}else{
			$msg = __('Gagal menyimpan divisi');
			$result = array(
				'status' => 'error',
				'msg' => $msg,
				'Log' => array(
					'activity' => $msg,
					'data' => $data,
					'error' => 1
				),
			);
		}

		return $result;
	}

	function updatePermission(){
		$count = 0;
		$type = 'Aro';

		$items = $this->find('all');
		foreach ($items as $item) {

			$item = $item[$this->alias];
			$this->create();
			$this->id = $item['id'];

			try {
				$node = $this->node();
			} catch (Exception $e) {
				$node = false;
			}
			
			// Node exists
			if ($node) {
				$parent = $this->parentNode();
				if (!empty($parent)) {
					$parent = $this->node($parent, $type);
				}
				$parent = isset($parent[0][$type]['id']) ? $parent[0][$type]['id'] : null;
				
				// Parent is incorrect
				if ($parent != $node[0][$type]['parent_id']) {
					// Remove Aro here, otherwise we've got duplicate Aros
					// TODO: perhaps it would be nice to update the Aro with the correct parent
					$this->Acl->Aro->delete($node[0][$type]['id']);
					$node = null;
				}
			}
			
			// Missing Node or incorrect
			if (empty($node)) {
				
				// Extracted from AclBehavior::afterSave (and adapted)
				$parent = $this->parentNode();
				if (!empty($parent)) {
					$parent = $this->node($parent, $type);
				}
				$data = array(
					'parent_id' => isset($parent[0][$type]['id']) ? $parent[0][$type]['id'] : null,
					'model' => $this->name,
					'foreign_key' => $this->id
				);
				
				// Creating ARO
				$this->Acl->{$type}->create($data);
				$this->Acl->{$type}->save();
				$count++;
			}
		}
	}

	function getdataChildParent($id = false, $slug = 'child'){
		if(!empty($id)){
			$options = array();

			switch ($slug) {
				case 'child':
					$options = array(
						'conditions' => array(
							'GroupCompany.parent_id' => $id,
						),
					);
					$field = 'group_id';
					break;

				case 'parent':
					$options = array(
						'conditions' => array(
							'GroupCompany.group_id' => $id,
						),
					);
					$field = 'parent_id';
					break;
			}
			$value = $this->GroupCompany->getData('first', $options);

			if($value){
				$field_id =  Common::hashEmptyField($value, sprintf('GroupCompany.%s', $field));

				return $this->getData('first', array(
					'conditions' => array(
						'Group.id' => $field_id,
					),
				));
			}
		}	
		return false;
	}

	function doDelete( $ids, $elements = null ) {	
		$result = false;

		if( is_array($ids) ) {
			$ids = array_filter($ids);
		}

		if($ids){
			$dataName = array();
			foreach ($ids as $key => $id) {
				$value = $this->getData('first', array(
		        	'conditions' => array(
						'Group.id' => $id,
						'Group.id >' => 21,
					),
				), $elements);
				$id = Common::hashEmptyField($value, 'Group.id');
				$principle_id = Common::hashEmptyField($value, 'Group.user_id');
				$dataName[] = Common::hashEmptyField($value, 'Group.name');

				$flag = $this->updateAll(array(
					'Group.status' => 0,
				), array(
					'Group.id' => $id,
				));

				if($flag){
					$child = $this->getdataChildParent($id);
					$parent = $this->getdataChildParent($id, 'parent');
					if(!empty($child) && empty($parent)){
						$child_id = Common::hashEmptyField($child, 'Group.id');

						$this->GroupCompany->deleteAll(array(
							'GroupCompany.group_id' => $id,
						));

						$this->GroupCompany->updateAll(array(
							'GroupCompany.parent_id' => NULL,
						), array(
							'GroupCompany.group_id' => $child_id,
						));

						// change superior ke null
						$this->User->updateAll(array(
							'User.superior_id' => NULL,
						), array(
							'User.group_id' => $child_id,
						));

					} else if(!empty($child) && !empty($parent)){
						$child_id = Common::hashEmptyField($child, 'Group.id');
						$parent_id = Common::hashEmptyField($parent, 'Group.id');

						// get user
						$superiors = $this->User->getData('all', array(
							'conditions' => array(
								'User.group_id' => $id,
							),
						));

						if($superiors){
							foreach ($superiors as $loop => $superior) {
								$user_id = Common::hashEmptyField($superior, 'User.id');
								$superior_id = Common::hashEmptyField($superior, 'User.superior_id');

								// ubah null group yang didelete untuk superior
								$this->User->updateAll(array(
									'User.superior_id' => 0,
								), array(
									'User.id' => $user_id,
								));
								//

								// change superior
								$this->User->updateAll(array(
									'User.superior_id' => !empty($superior_id) ? $superior_id : NULL,
								), array(
									'User.superior_id' => $user_id,
								));
								// 
							}
						}
						// 
						$this->GroupCompany->deleteAll(array(
		            		'GroupCompany.group_id' => $child_id,
		            	));

						// buat relasi antara childnya dan parentnya dari group yang didelete
						$dataSave['GroupCompany'] = array(
							'parent_id' => $parent_id,
							'group_id' => $child_id,
							'user_id' => $principle_id,
						);

						$this->GroupCompany->create();
						$this->GroupCompany->save($dataSave);
						//
					}

					$this->GroupCompany->deleteAll(array(
	            		'GroupCompany.group_id' => $id,
	            	));
				}

			}
			$name = implode(', ', $dataName);
			$default_msg = __('menghapus divisi %s', $name);
		}

		if ( !empty($dataName) ) {
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
				'Log' => array(
					'activity' => $msg,
					'error' => 1,
				),
			);
		}

		return $result;
	}

	function doToggle( $id, $active) {	
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$this->virtualFields['order_admin'] =  'CASE WHEN (Group.id >= 7 AND Group.id <= 21) OR Group.id = 2 THEN 1 ELSE 0 END';

		$options = array(
        	'conditions' => array(
				'Group.id' => $id,
			),
		);

		if( !empty($active) ) {
			$default_msg = __('mengaktifkan divisi');
		} else {
			$default_msg = __('nonaktifkan divisi');
		}

		$value = $this->getData('all', $options, array(
			'status' => 'all',
		));

		$name = Hash::Extract($value, '{n}.Group.name');
		$name = implode(', ', $name);

		$default_msg = sprintf('%s %s', $default_msg, $name);

		if ( !empty($value) ) {
			$flag = $this->updateAll(array(
				'Group.active' => $active,
			), array(
				'Group.id' => $id,
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

	function getDivisionCompany($params = array(), $user = false){
		$userID = Common::hashEmptyField($params, 'userID');
		$slug = Common::hashEmptyField($params, 'slug');
		$exlude = Common::hashEmptyField($params, 'exlude');
		$type = Common::hashEmptyField($params, 'type', 'list');
		$is_parent = Common::hashEmptyField($params, 'is_parent', true, array(
			'isset' => true,
		));

		if(!empty($user)){
			$parent_id = Common::hashEmptyField($user, 'User.parent_id');
			$group_id = Common::hashEmptyField($user, 'User.group_id');
			if(!in_array($group_id, array(3, 4))){
				$userID = $parent_id;
			}

		}

		$options['OR'][0] = array(
			'Group.is_prime' => true,
		);

		switch ($slug) {
			case 'director':
				$options['OR'][0]['Group.id !='] = array('2', '3');
				break;
			
			default:
				$options['OR'][0]['Group.id !='] = array('3');
				break;
		}

		$options['OR'][1]['Group.user_id'] = $userID;
		
		if(!empty($exlude)){
			if(!is_array($exlude)){
				$exlude = array($exlude);
			}
			$options['OR'][1]['Group.id !='] = $exlude;
		}

		if($type <> 'paginate'){
			if($type == 'list'){
				return $this->GroupCompany->getBuildTree($options, $userID, $exlude, $is_parent);
			} else {
				return $this->getData( $type, array(
					'conditions' => $options,
				));
			}
		} else {
			return $options;
		}
	}

	function pickPrinciple($group_id = false){
		$value = $this->getData('first', array(
			'conditions' => array(
				'Group.id' => $group_id,
			),
		), array(
			'status' => 'all',
		));

		if(!empty($value)){
			$principle_id = Common::hashEmptyField($value, 'Group.user_id');

			return $this->User->getData('first', array(
				'conditions' => array(
					'User.id' => $principle_id,
				),
			));

		} else {
			return false;
		}
	}

}
