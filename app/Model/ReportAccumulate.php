<?php

App::uses('AppModel', 'Model');

class ReportAccumulate extends AppModel {

	public $belongsTo = array(
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => false,
			'conditions' => array(
				'UserCompany.user_id = ReportAccumulate.user_company_id'
			),
		)
	);

	public function _callRefineParams( $data = '', $default_options = false ) {
		$default_options = $this->defaultOptionParams($data, $default_options, array(
			'region_id' => array(
				'field'=> 'UserCompany.region_id',
				'contain' => array(
					'UserCompany',
				),
			),
			'city_id' => array(
				'field'=> 'UserCompany.city_id',
				'contain' => array(
					'UserCompany',
				),
			),
			'user_id' => array(
				'field'=> 'ReportAccumulate.user_company_id',
			),
		));

		return $default_options;
	}

    function getMerge( $data, $group_id, $is_root = false ){
    	if( empty($data['Group']) ) {
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

	function doSave($data, $id = false){
		if(!empty($id)){
			$this->id = $id;
			$data['Group']['id'] = $id;
		}else{
			$this->create();
		}

		$this->set($data);

		if(!empty($data)){
			if($this->validates($data)){

				if( !empty($id) ) {
					$user_id = Set::extract($data, '/GroupCompany/user_id');

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
					$result = array(
						'status' => 'error',
						'msg' => __('Gagal menyimpan divisi'),
					);
				}
			}else{
				$result = array(
					'status' => 'error',
					'msg' => __('Gagal menyimpan divisi'),
				);
			}
		}else{
			$result = array(
				'status' => 'error',
				'msg' => __('Gagal menyimpan divisi'),
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

	function doDelete( $id, $elements = null ) {	
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$options = array(
        	'conditions' => array(
				'Group.id' => $id,
				'Group.company_id <>' => 0,
			),
		);

		$value = $this->getData('all', $options, $elements);

		$default_msg = __('menghapus divisi');

		if ( !empty($value) ) {
			$flag = $this->updateAll(array(
				'Group.status' => 0,
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

	function doToggle( $id, $active, $elements = null ) {	
		$result = false;

		if( is_array($id) ) {
			$id = array_filter($id);
		}

		$this->virtualFields['order_admin'] =  'CASE WHEN (Group.id >= 7 AND Group.id <= 21) OR Group.id = 2 THEN 1 ELSE 0 END';
		$options = array(
        	'conditions' => array(
				'Group.id' => $id,
				'Group.order_admin' => 0,
			),
		);

		$value = $this->getData('all', $options, $elements);
		
		if( !empty($active) ) {
			$default_msg = __('mengaktifkan divisi');
		} else {
			$default_msg = __('nonaktifkan divisi');
		}

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
}
