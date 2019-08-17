<?php
App::uses('AppModel', 'Model');

class GroupCompany extends AppModel {
	public $belongsTo = array(
		'Group' => array(
			'className' => 'Group',
			'foreignKey' => 'group_id',
			'conditions' => '',
			'fields' => '',
			'order' => '',
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'conditions'=> array(
				'GroupCompany.status' => TRUE,
			),
			'order'=> array(
				$this->alias.'.id' => 'ASC',
			),
			'fields' => array()
		);

		return $this->merge_options($default_options, $options, $find);
	}

	function Tree( $dataList, $space = '', $dashChild = '___' ){

		$data = array();
		foreach($dataList AS $key => $result){
			$result = $this->getMergeList($result, array(
				'contain' => array(
					'Group',
				),
			));

			if(!empty($result['Group'])){
				$data[] = array(
					'id'   => $result['Group']['id'],
					'name' => $space.$result['Group']['name']);
			}

			if(!empty($result['children'])){
				$space_add = $dashChild.$space;
				$data_child = $this->Tree($result['children'], $space_add, $dashChild);
				$data = array_merge($data,$data_child);
				

			}
			
		}
		return $data;
	}

	function tree_list($true = false, $result = null, $dashChild = '___'){
		if(!isset($result)){
			$result  = $this->find('threaded', array(
				'conditions' => array(
					'GroupCompany.status'=> 1, 
				),
			));
		}

		$generateTree = $this->Tree($result, false, $dashChild);
		$row = false;

		if($true){
			foreach($generateTree AS $key => $value){
				if(count($generateTree) > ($key+1)){
					if(substr_count($generateTree[$key]['name'], $dashChild) < substr_count($generateTree[$key+1]['name'], $dashChild)){
						$row[$value['name']] = array();
					}
					else{
						$row[$value['id']] = $value['name'];
					}
				}else{
					$row[$value['id']] = $value['name'];
				}
							
			}

		}else{
			foreach($generateTree AS $key => $value){			
				$row[$value['id']] = $value['name'];
			}
		}
		
		return $row;
	}

	function getBuildTree($options = array(), $userID = false, $exclude = array(), $is_parent = false){
		if(!empty($exclude) && !is_array($exclude)){
			$exclude = array(
				$exclude,
			);
		} else if(empty($exclude)){
			$exclude = array();
		}

		if(empty($is_parent)){
			$exclude[] = 2;
			$exclude[] = 5;
		}

		$groups = $this->getData('all', array(
			'conditions' => array(
				'GroupCompany.user_id' => $userID,
				'GroupCompany.group_id !=' => $exclude,
			),
			'order' => array(
				'GroupCompany.parent_id' => 'DESC',
			),
		));
		$groups = $this->getMergeList($groups, array(
			'contain' => array(
				'Group',
			),
		));

		$group_ids = Hash::Combine($groups, '{n}.GroupCompany.group_id', '{n}.GroupCompany.group_id');

		$parentGroups = array();
		if($is_parent){
			// parent
			$parentGroups = $this->Group->getData('all', array(
				'conditions' => array_merge($options, array(
					'Group.id !=' => array_merge($group_ids, $exclude),
				)),
			));

			if(!empty($parentGroups)){
				foreach ($parentGroups as $key => &$value) {
					$group_id = Common::hashEmptyField($value, 'Group.id');
					$value['GroupCompany'] = array(
						'user_id' => $userID,
						'group_id' => $group_id,
						'parent_id' => false,
					);
				}
			}
		}

		$groups = array_merge($groups, $parentGroups);
		$groups = Common::buildTree($groups, 0, array(
			'model' => 'GroupCompany',
			'alias' => 'Group',
			'parent_field' => 'group_id',
			'child_field' => 'parent_id',
		));

		$groups = Hash::sort($groups, '{n}.Group.name', 'ASC');
		return $this->tree_list(false, $groups, '---');
	}
}