<?php
class RuleCategory extends AppModel {
	var $name = 'RuleCategory';

	var $displayField = 'name';

	var $belongsTo = array(
		'ParentRuleCategory' => array(
			'className' => 'RuleCategory',
			'foreignKey' => 'parent_id'
		),
	);

	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Nama kategori harap diisi',
			),
		),
		'photo' => array(
			'imageupload' => array(
	            'rule' => array('extension',array('jpeg','jpg','png','gif')),
	            'required' => false,
	            'allowEmpty' => true,
	            'message' => 'Harap pilih foto berekstensi (jpeg, jpg, png, gif)'
	        ),
		),
	);

	function getData( $find = 'all', $options = array(), $elements = array() ) {
		$status = isset($elements['status']) ? $elements['status']:'active';
		$company = isset($elements['company']) ? $elements['company']:true;

		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
				'RuleCategory.modified' => 'DESC'
			),
		);

        switch ($status) {
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'RuleCategory.status' => 1,
            	));
                break;
            case 'non-active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                	'RuleCategory.status' => 0,
            	));
                break;
        }

        if( !empty($company) ) {
            $company_data = Configure::read('Config.Company.data');
			$company_id	  = Common::hashEmptyField($company_data, 'UserCompany.id', 0);

            $default_options['conditions']['RuleCategory.company_id'] = $company_id;
        }

		if( !empty($options) ) {
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

		if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
	}

	public function doSave( $data, $rule_category = false, $id = false ) {
		$result = false;

		$default_msg  = __('%s data kategori rule');
		$company_data = Configure::read('Config.Company.data');
		$company_id	  = Common::hashEmptyField($company_data, 'UserCompany.id', 0);
		$user_id      = Configure::read('User.data.id');

		if ( !empty($data) ) {

			if( !empty($id) ) {
				$this->id = $id;
				$default_msg = sprintf($default_msg, __('mengubah'));
			} else {
				$this->create();
				$default_msg = sprintf($default_msg, __('menambah'));
			}

			$data['RuleCategory']['company_id'] = $company_id;
			$data['RuleCategory']['user_id'] 	= $user_id;

			$this->set($data);

			if ( $this->validates() ) {
				if( $this->save() ) {
					$msg = sprintf(__('Berhasil %s'), $default_msg);
					$result = array(
						'msg' => $msg,
						'status' => 'success',
						'Log' => array(
							'activity' => $msg,
							'old_data' => $rule_category,
							'document_id' => $id,
						),
					);
				} else {
					$msg = sprintf(__('Gagal %s'), $default_msg);
					$result = array(
						'msg' => sprintf(__('Gagal %s'), $default_msg),
						'status' => 'error',
						'data' => $data,
						'Log' => array(
							'activity' => $msg,
							'old_data' => $rule_category,
							'document_id' => $id,
							'error' => 1,
						),
					);
				}
			} else {
				$validationErrors = array();

					if(!empty($this->validationErrors)){
						$validationErrors = array_merge($validationErrors, $this->validationErrors);
					}
				$result = array(
					'msg' => sprintf(__('Gagal %s'), $default_msg),
					'status' => 'error',
					'data' => $data,
					'validationErrors' => $validationErrors,
				);
			}
		} else if( !empty($rule_category) ) {
			$photo = !empty($rule_category['RuleCategory']['photo'])?$rule_category['RuleCategory']['photo']:false;
			$rule_category['RuleCategory']['photo_hide'] = $photo;
			$result['data'] = $rule_category;
		}

		return $result;
	}

	function doDelete( $id ) {
		
		$result = false;
		$rule_category = $this->getData('all', array(
        	'conditions' => array(
				'RuleCategory.id' => $id,
			),
		));

		if ( !empty($rule_category) ) {
			$name = Set::extract('/RuleCategory/name', $rule_category);
			$name = implode(', ', $name);
			$default_msg = sprintf(__('menghapus kategori rule %s'), $name);

			$flag = $this->updateAll(array(
				'RuleCategory.status' => 0,
	    		'RuleCategory.modified' => "'".date('Y-m-d H:i:s')."'",
			), array(
				'RuleCategory.id' => $id,
			));

            if( $flag ) {
				$msg = sprintf(__('Berhasil %s'), $default_msg);
                $result = array(
					'msg' => $msg,
					'status' => 'success',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $rule_category,
					),
				);
            } else {
				$msg = sprintf(__('Gagal %s'), $default_msg);
				$result = array(
					'msg' => $msg,
					'status' => 'error',
					'Log' => array(
						'activity' => $msg,
						'old_data' => $rule_category,
						'error' => 1,
					),
				);
			}
		} else {
			$result = array(
				'msg' => __('Gagal menghapus kategori rule. Data tidak ditemukan'),
				'status' => 'error',
			);
		}

		return $result;
	}

	public function _callRefineParams( $data = '', $default_options = false ) {
		$keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
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
        $category = $this->filterEmptyField($data, 'named', 'category', false, array(
            'addslashes' => true,
        ));

		if( !empty($keyword) ) {
			$default_options['conditions']['RuleCategory.name LIKE'] = '%'.$keyword.'%';
		}
		if( !empty($date_from) ) {
			$default_options['conditions']['DATE_FORMAT(RuleCategory.created, \'%Y-%m-%d\') >='] = $date_from;

			if( !empty($date_to) ) {
				$default_options['conditions']['DATE_FORMAT(RuleCategory.created, \'%Y-%m-%d\') <='] = $date_to;
			}
		}
		if( !empty($modified_from) ) {
			$default_options['conditions']['DATE_FORMAT(RuleCategory.modified, \'%Y-%m-%d\') >='] = $modified_from;

			if( !empty($modified_to) ) {
				$default_options['conditions']['DATE_FORMAT(RuleCategory.modified, \'%Y-%m-%d\') <='] = $modified_to;
			}
		}
		if( !empty($category) ) {
			$default_options['conditions']['RuleCategory.parent_id'] = $category;
		}
		
		return $default_options;
	}

	function getMergeParent( $data, $category_id = false ) {
		if( !empty($data['RuleCategory']) ) {
			$rule_category_id = Common::hashEmptyField($data, 'Rule.rule_category_id');
			$root_category_id = Common::hashEmptyField($data, 'Rule.root_category_id');

			// get data parent
			$parent_id = Common::hashEmptyField($data, 'RuleCategory.parent_id');
			$category  = $this->getData('first', array(
				'conditions' => array(
					'RuleCategory.id' => $parent_id,
				),
			), array(
				'status' => 'all',
			));

			// data parent it self
			if ( empty($category) && $rule_category_id == $root_category_id ) {
				$dataParent['ParentRuleCategory'] = $data['RuleCategory'];
			} else {
				$dataParent['ParentRuleCategory'] = $category['RuleCategory'];
			}
			if( !empty($dataParent) ) {
				$data = array_merge($data, $dataParent);
			}
		}

		return $data;
	}

	/*============ S: tree rule category ============
	===============================================*/
	function getTreeDatas($company_id = false){
		$categories = $this->getData('all', array(
			'conditions' => array(
				'RuleCategory.company_id' => $company_id,
			),
			'order' => array(
				'RuleCategory.parent_id' => 'DESC',
			),
		));

		$categories = Common::buildTree($categories, 0, array(
			'model' 	   => 'RuleCategory',
			'parent_field' => 'id',
			'child_field'  => 'parent_id',
		));

		$categories = Hash::sort($categories, '{n}.RuleCategory.name', 'ASC');
		return $this->tree_list(false, $categories, '---');

	}

	function tree_list($true = false, $result = null, $dashChild = '___'){
		if(!isset($result)){
			$result  = $this->find('threaded', array(
				'conditions' => array(
					'RuleCategory.status'=> 1, 
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

	function Tree( $dataList, $space = '', $dashChild = '___', $options = array() ){

		$trace_sub_cat = Common::hashEmptyField($options, 'trace_sub_cat', false);

		$data = array();
		foreach($dataList AS $key => $result){
			if(!empty($result['RuleCategory'])){
				$data[] = array(
					'id'   => $result['RuleCategory']['id'],
					'name' => $space.$result['RuleCategory']['name']);
			}

			if ( $trace_sub_cat && empty($result['children'])) {
				$id = Common::hashEmptyField($result, 'RuleCategory.id', false);
				$data_sub['children'] = $this->getData('all', array(
					'conditions' => array(
						'RuleCategory.parent_id' => $id,
					),
					'fields' => array('id', 'parent_id', 'name'),
					'order'  => array(
						'RuleCategory.name' => 'ASC',
					),
				));

				$result = array_merge_recursive($result, $data_sub);

			}

			if(!empty($result['children'])){
				$space_add = $dashChild.$space;
				$data_child = $this->Tree($result['children'], $space_add, $dashChild);
				$data = array_merge($data,$data_child);
				
			}
			
		}
		return $data;
	}

	// auto fil parent_id category content add /edit rules
	function generateTableOfContent($parent_id=false){
		$result   = false;
        $new_data = false;
        $company_data = Configure::read('Config.Company.data');
		$company_id	  = Common::hashEmptyField($company_data, 'UserCompany.id', 0);

		$default_options = array(
			'conditions' => array(
				'RuleCategory.company_id'=> $company_id,
				'RuleCategory.status'=> 1,
			),
			'fields' 	=> array('id', 'parent_id', 'name'),
			'order'  	=> array('name ASC')
		);

		if (!empty($parent_id)) {
			$options = array(
				'conditions' => array(
					'RuleCategory.parent_id'=> $parent_id,
				),
			);

			$default_options = array_merge_recursive($default_options, $options);
		}

		// get data rule category, sort by name
		$result = $this->find('threaded', $default_options);

		if (!empty($parent_id)) {
			$generateTree = $this->Tree($result, false, '---', array(
				'trace_sub_cat' => true,
			));

			if (!empty($generateTree)) {
				foreach($generateTree AS $key => $value){			
					$new_data[$value['id']] = $value['name'];
				}
			}

			return $new_data;
		}
		
		return $result;
	}
	/*============ E: tree rule category ============
	===============================================*/

}
?>