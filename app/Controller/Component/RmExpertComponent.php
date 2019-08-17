<?php
class RmExpertComponent extends Component {
	var $components = array('Auth', 'RmCommon', 'RumahkuApi'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;

		$this->component_from = array(
			'plus' => 'point',
			'pinalty' => 'pinalty',
		);
	}

	function doBeforeView($value = array()){
		$expertCategoryDetails = Common::hashEmptyField($value, 'ExpertCategoryDetail');

		if($expertCategoryDetails){
			foreach($expertCategoryDetails AS $key => $expertCategoryDetail){
				$type = Common::hashEmptyField($expertCategoryDetail, 'ExpertCategoryDetail.type');
				$index = Common::hashEmptyField($expertCategoryDetail, 'ExpertCategoryDetail.index');
				$value = Common::hashEmptyField($expertCategoryDetail, 'ExpertCategoryDetail.value');
				$value_end = Common::hashEmptyField($expertCategoryDetail, 'ExpertCategoryDetail.value_end');
				$point = Common::hashEmptyField($expertCategoryDetail, 'ExpertCategoryDetail.point', NULL);
				$compare = Common::hashEmptyField($expertCategoryDetail, 'ExpertCategoryDetail.compare');

				if($index == 'good'){
					$result['ExpertCategoryDetail']['type'][$key] = $type;
					$result['ExpertCategoryDetail']['value'][$key] = $value;
					$result['ExpertCategoryDetail']['value_end'][$key] = $value_end;
					$result['ExpertCategoryDetail']['compare'][$key] = $compare;
					$result['ExpertCategoryDetail']['point'][$key] = $point;

				} else if($index == 'bad') {
					$result['ExpertCategoryDetail']['point_pinalty'] = $point;
				}
			}
		}

		if(!empty($result)){
			$this->controller->request->data = $result;
		}
	}

	function doBeforeSave($data = false, $params = false){
		$company_id  = Common::hashEmptyField($params, 'company_id');
		$value  = Common::hashEmptyField($params, 'value');

		// default variable
		$expert_category_id = Common::hashEmptyField($value, 'ExpertCategory.id', null);
		$expert_category_active_id = Common::hashEmptyField($value, 'ExpertCategoryActive.id', null);
		$company_id = Common::hashEmptyField($value, 'ExpertCategoryActive.company_id', null);
		// 

		$dataSaves = array();
		$point_pinalty = Common::hashEmptyField($data, 'ExpertCategoryDetail.point_pinalty', NULL);

		$data = Common::_callUnset($data, array(
			'ExpertCategoryDetail' => array(
				'point_pinalty'
			),
		));
		if(!empty($data['ExpertCategoryDetail']['value'])){
			foreach ($data['ExpertCategoryDetail']['value'] as $key => $val) {
				$type = Common::hashEmptyField($data, sprintf('ExpertCategoryDetail.type.%s', $key));
				$point = Common::hashEmptyField($data, sprintf('ExpertCategoryDetail.point.%s', $key), NULL);

				$value = Common::hashEmptyField($data, sprintf('ExpertCategoryDetail.value.%s', $key), NULL);
				$value_end = Common::hashEmptyField($data, sprintf('ExpertCategoryDetail.value_end.%s', $key), NULL);
				$compare = Common::hashEmptyField($data, sprintf('ExpertCategoryDetail.compare.%s', $key), NULL);

				switch ($compare) {
					case 'less_than':
						$value = isset($value) ? $value : false;
						break;
					
					case 'more_than':
						$value_end = isset($value_end) ? $value_end : false;
						break;

					case 'between':
						$value = isset($value) ? $value : false;
						$value_end = isset($value_end) ? $value_end : false;
						break;

					default:
						// $type = 'equal_to';
						$value = isset($value) ? $value : false;
						break;
				}

				$dataSave = array(
					'ExpertCategoryDetail' => array(
						'expert_category_id' => $expert_category_id,
						'expert_category_active_id' => $expert_category_active_id,
						'company_id' => $company_id,
						'type' => $type,
						'index' => 'good',
						'point' => $point,
						'compare' => $compare,
					),
				);

				if(isset($value) || !empty($value)){
					$dataSave['ExpertCategoryDetail']['value'] = $value;
				}

				if(isset($value_end) || !empty($value_end)){
					$dataSave['ExpertCategoryDetail']['value_end'] = $value_end;
				}

				if($dataSave){
					$dataSaves[] = $dataSave;
				}
			}

			// point pinalty sifat optional
			if($point_pinalty){
				$dataSaves[] = array(
					'ExpertCategoryDetail' => array(
						'expert_category_id' => $expert_category_id,
						'expert_category_active_id' => $expert_category_active_id,
						'company_id' => $company_id,
						'type' => 'equal_to',
						'index' => 'bad',
						'point' => $point_pinalty,
					),
				);
			}
		}
		return $dataSaves;
	}

	function doBeforeConfigure($data = false, $value = false){
		$dataSave = array();
		$schema = Common::hashEmptyField($data, 'ExpertCategoryConfiguration.type');
		$configure_ids = Common::hashEmptyField($data, 'ExpertCategoryConfiguration.configure_id', null);
		$company_id = Configure::read('Principle.id');

		// query
		$active_id = Common::hashEmptyField($value, 'ExpertCategoryActive.id', null);
		$expert_category_id = Common::hashEmptyField($value, 'ExpertCategoryActive.expert_category_id', null);
        $expert_category_type = Common::hashEmptyField($value, 'ExpertCategory.type');
        $defaultData = array(
			'company_id' => $company_id,
			'expert_category_id' => $expert_category_id,
			'expert_category_active_id' => $active_id,
		);

		if($schema){
			$dataSave[] = array(
				'ExpertCategoryConfiguration' => array_merge($defaultData, array(
					'type' => 'scheme',
					'value' => $schema,
				)),
			);
		}

		if($configure_ids){
			foreach ($configure_ids as $name => $val) {
				if($val){
					$dataSave[] = array(
						'ExpertCategoryConfiguration' => array_merge($defaultData, array(
							'type' => 'conditions',
							'value' => $name,
						)),
					);
				}
			}
		}

		switch ($expert_category_type) {
			case 'property':
				$dataSave[] = array(
					'ExpertCategoryConfiguration' => array_merge($defaultData, array(
						'type' => 'conditions',
						'value' => 'property_action',
					)),
				);
				break;
			case 'ebrosur':
				$dataSave[] = array(
					'ExpertCategoryConfiguration' => array_merge($defaultData, array(
						'type' => 'conditions',
						'value' => 'ebrosur_action',
					)),
				);
				break;
		}

		return $dataSave;
	}

	function doBeforeViewConfigue($value = false){
		$schema = Common::hashEmptyField($value, 'ExpertCategory.Schema.value');
		$expert_category_type = Common::hashEmptyField($value, 'ExpertCategory.type');
		$conditions = Common::hashEmptyField($value, 'ExpertCategory.Condition');
		$master_conditions = Configure::read('__Site.SP.conditions.default');

		if($schema){
			$this->controller->request->data['ExpertCategoryConfiguration']['type'] = $schema;
		}

		if( !empty($master_conditions) ){
			if( !empty($conditions) ) {
				foreach ($master_conditions as $key => $label) {
					$val = hash::Extract($conditions, sprintf('{n}.ExpertCategoryConfiguration[value=%s]', $key));
					$val = array_shift($val);

					if($val){
						$this->controller->request->data['ExpertCategoryConfiguration']['configure_id'][$key] = $key;
					}
				}
			} else if( !empty($expert_category_type) ) {
				$condition_slug = __('%s_action', $expert_category_type);
				$this->controller->request->data['ExpertCategoryConfiguration']['configure_id'][$condition_slug] = $condition_slug;
			}
		}

		$propertyActions = $this->controller->User->Property->PropertyAction->getData('list', array(
			'fields' => array(
				'PropertyAction.slug',
				'PropertyAction.name',
			),
            'cache' => __('PropertyAction.Slug'),
        ));

        $this->controller->set(array(
        	'propertyActions' => $propertyActions,
        ));
	}

	function doBeforeComponentDetail($data, $value, $component = NULL){
		$dataSave = array();

		$expert_category_component_active_id = Common::hashEmptyField($component, 'ExpertCategoryComponentActive.id', null);
		$expert_category_company_id = Common::hashEmptyField($component, 'ExpertCategoryCompany.id', null);
		$expert_category_component_id = Common::hashEmptyField($component, 'ExpertCategoryComponent.id', null);
		$expert_category_component_periode = Common::hashEmptyField($data, 'ExpertCategoryComponent.periode', NULL);

		$expert_category_id = Common::hashEmptyField($value, 'ExpertCategory.id', null);
		$expert_category_active_id = Common::hashEmptyField($value, 'ExpertCategoryActive.id', null);
		$company_id = Configure::read('Principle.id');

		$name = Common::hashEmptyField($data, 'ExpertCategoryComponent.name');
		$note = Common::hashEmptyField($data, 'ExpertCategoryComponent.note');
		$check_point = Common::hashEmptyField($data, 'ExpertCategoryCompany.check_point');
		$check_pinalty = Common::hashEmptyField($data, 'ExpertCategoryCompany.check_pinalty');
		$point = Common::hashEmptyField($data, 'ExpertCategoryCompany.point', NULL);
		$p_pinalty = Common::hashEmptyField($data, 'ExpertCategoryCompany.p_pinalty', NULL);

		$temp = $this->setDataDetail($data, array(
			array(
				'check' => $check_point,
				'rel' => 'point',
				'field' => 'point',
				'modelName' => 'ExpertCategoryCompany',
			),
			array(
				'check' => $check_pinalty,
				'rel' => 'pinalty',
				'field' => 'p_pinalty',
				'modelName' => 'ExpertCategoryCompany',
			),
		));

		$dataSave = array(
			'ExpertCategoryComponent' => array(
				'id' => $expert_category_component_id,
				'name' => $name,
				'note' => $note,
				'expert_category_id' => $expert_category_id,
				'company_id' => $company_id,
				'periode' => $expert_category_component_periode,
			),
			'ExpertCategoryComponentActive' => array(
				array(
					'ExpertCategoryComponentActive' => array(
						'id' => $expert_category_component_active_id,
						'expert_category_active_id' => $expert_category_active_id,
						'company_id' => $company_id,
						'ExpertCategoryCompany' => array_merge(array(
							'id' => $expert_category_company_id,
							'expert_category_active_id' => $expert_category_active_id,
							'company_id' => $company_id,
							'check_point' => $check_point,
							'check_pinalty' => $check_pinalty,
							'point' => $point,
							'p_pinalty' => $p_pinalty,
							'flag_point' => !empty($temp)?true:false,
						), $temp),
					),
				),
			),
		);
		return $dataSave;
	}

	function setDataDetail($data, $params = array()){
		if($data && $params){
			$temp = array();
			$ExpertCategoryCompany = ClassRegistry::init('ExpertCategoryCompany');

			foreach ($params as $key => $param) {
				$check = Common::hashEmptyField($param, 'check');
				$rel = Common::hashEmptyField($param, 'rel');
				$field = Common::hashEmptyField($param, 'field');
				$modelName = Common::hashEmptyField($param, 'modelName');

				if($check){
					$types = Common::hashEmptyField($data, sprintf('ExpertCategoryCompanyDetail.type.%s', $rel));
					$slugs = Common::hashEmptyField($data, sprintf('ExpertCategoryCompanyDetail.slug.%s', $rel));
					$values = Common::hashEmptyField($data, sprintf('ExpertCategoryCompanyDetail.value.%s', $rel));
					$value_ends = Common::hashEmptyField($data, sprintf('ExpertCategoryCompanyDetail.value_end.%s', $rel));
					$compare = Common::hashEmptyField($data, sprintf('ExpertCategoryCompanyDetail.compare.%s', $rel));

					$ExpertCategoryCompany->validator()->add($field, 'required', array(
						'rule' => 'notempty',
		                'message' => 'Poin harap diisi',
					));

					if($types){
						$temp['ExpertCategoryCompanyDetail'][] = array(
							'ExpertCategoryCompanyDetail' => array(
								'slug' => $types,
								'type' => 'schema',
								'from' => ($field == 'point') ? 'plus' : 'pinalty',
							),
						);
					}

					if($slugs){
						$checkExisting = array();

						foreach ($slugs as $key => $slug) {
							$dat = array();

							$slug = Common::hashEmptyField($slugs, $key);
							$value = Common::hashEmptyField($values, $key);
							$value_end = Common::hashEmptyField($value_ends, $key);
							$compare_val = Common::hashEmptyField($compare, $key);
							$keyExisting = null;

							if($slug){
								$dat['slug'] = $slug;
								$keyExisting .= $slug;
							}

							if($value){
								$dat['value'] = $value;
								$keyExisting .= $value;
							}

							if($value_end){
								$dat['value_end'] = $value_end;
								$keyExisting .= $value_end;
							}

							if($compare_val){
								$dat['compare'] = $compare_val;
								$keyExisting .= $compare_val;
							}

							if($dat){
								$from = ($field == 'point') ? 'plus' : 'pinalty';
								$dat['from'] = $from;
								$keyExisting .= $from;

								if( !in_array($slug, array( 'other', 'property_action', 'ebrosur_action' )) ) {
									$type = 'conditions';
								} else {
									$type = $slug;
								}
								
								$dat['type'] = $type;
								$keyExisting .= $from;

								if( empty($checkExisting[$keyExisting]) ) {
									$temp['ExpertCategoryCompanyDetail'][] = array(
										'ExpertCategoryCompanyDetail' => $dat,
									);
									$checkExisting[$keyExisting] = true;
								}
							}
						}

					}
				}
			}

			return $temp;
		}
	}

	function doBeforeViewComponent($component = false){
		$flag = $this->component_from;

		$schemas = Common::hashEmptyField($component, 'ExpertCategoryCompany.Schema');
		$conditions = Common::hashEmptyField($component, 'ExpertCategoryCompany.Condition');
		$dataSave = array();

		$component = Common::_callUnset($component, array(
			'ExpertCategoryCompany' => array(
				'Schema',
				'Condition',
			),
		));

		if($schemas){
			foreach ($schemas as $key => $schema) {
				$from = Common::hashEmptyField($schema, 'ExpertCategoryCompanyDetail.from');
				$slug = Common::hashEmptyField($schema, 'ExpertCategoryCompanyDetail.slug');

				$dataSave['type'][$flag[$from]] = $slug;
			}
		}

		if($conditions){
			foreach ($conditions as $key => $condition) {
				$type = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.type');
				$slug = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.slug');
				$value = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.value');
				$value_end = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.value_end');
				$compare = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.compare');

				$from = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.from');

				if($slug){
					$dataSave['slug'][$flag[$from]][$key] = $slug;
				}
				if($compare){
					$dataSave['compare'][$flag[$from]][$key] = $compare;
				}

				$dataSave['value'][$flag[$from]][$key] = !empty($value)?$value:false;
				$dataSave['value_end'][$flag[$from]][$key] = !empty($value_end)?$value_end:false;
			}
		}


		if($dataSave){
			$component['ExpertCategoryCompanyDetail'] = $dataSave;
		}

		$this->controller->request->data = $component;
	}

	function doBeforeSaveViewComponent($component = false){
		$flag = $this->component_from;
		$components = Common::hashEmptyField($component, 'ExpertCategoryComponentActive');
		$componentDetail = array();

		if( !empty($components) ){
			foreach ($components as $key => $value) {
				$conditions = Common::hashEmptyField($value, 'ExpertCategoryComponentActive.ExpertCategoryCompany.ExpertCategoryCompanyDetail');

				if( !empty($conditions) ){
					foreach ($conditions as $key => $condition) {
						$type = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.type');
						$from = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.from');
						$slug = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.slug');

						if( $type == 'schema' ) {
							$componentDetail['type'][$flag[$from]] = $slug;
						} else {
							$value = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.value');
							$value_end = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.value_end');
							$compare = Common::hashEmptyField($condition, 'ExpertCategoryCompanyDetail.compare');

							if($slug){
								$componentDetail['slug'][$flag[$from]][] = $slug;
							}
							if($compare){
								$componentDetail['compare'][$flag[$from]][] = $compare;
							}

							$componentDetail['value'][$flag[$from]][] = !empty($value)?$value:false;
							$componentDetail['value_end'][$flag[$from]][] = !empty($value_end)?$value_end:false;
						}
					}
				}
			}
		}

		$dataComponentActive = Common::hashEmptyField($component, 'ExpertCategoryComponentActive.0');
		$component = array(
			'ExpertCategoryComponent' => Common::hashEmptyField($component, 'ExpertCategoryComponent'),
			'ExpertCategoryComponentActive' => Common::hashEmptyField($dataComponentActive, 'ExpertCategoryComponentActive'),
			'ExpertCategoryCompany' => Common::hashEmptyField($dataComponentActive, 'ExpertCategoryComponentActive.ExpertCategoryCompany'),
			'ExpertCategoryCompanyDetail' => $componentDetail,
		);
		$component = Common::_callUnset($component, array(
			'ExpertCategoryComponentActive' => array(
				'ExpertCategoryCompany',
			),
			'ExpertCategoryCompany' => array(
				'ExpertCategoryCompanyDetail',
			),
		));

		$this->controller->request->data = $component;
	}

	function doBeforeSaveCategory($data = false, $id = NULL){
		$principle_id = Configure::read('Principle.id');
		$code = $this->controller->RmUser->_generateCode('user_code');
		$parent_id = Common::hashEmptyField($data, 'ExpertCategory.parent_id', '');

		$data = Hash::insert($data, 'ExpertCategory.id', $id);
		$data = Hash::insert($data, 'ExpertCategory.company_id', $principle_id);
		$data = Hash::insert($data, 'ExpertCategory.code', $code);

		if( !empty($id) ) {
			$expertCategoryActive = $this->controller->ExpertCategory->ExpertCategoryActive->getData('first', array(
				'conditions' => array(
					'ExpertCategoryActive.expert_category_id' => $id,
				),
			), array(
				'is_company' => true,
			));
			$expert_category_active_id = Common::hashEmptyField($expertCategoryActive, 'ExpertCategoryActive.id');
		} else {
			$expert_category_active_id = NULL;
		}

		if( !empty($parent_id) ) {
			$root_id = $this->controller->ExpertCategory->_callGetRoot($data);
		} else {
			$root_id = NULL;
		}
		
		$data = Hash::insert($data, 'ExpertCategory.root_id', $root_id);
		$data['ExpertCategoryActive'][] = array(
			'ExpertCategoryActive' => array(
				'id' => $expert_category_active_id,
				'company_id' => $principle_id,
				'parent_id' => $parent_id,
				'root_id' => $root_id,
				'is_allow_agent' => Common::hashEmptyField($data, 'ExpertCategory.is_allow_agent', 0),
			),
		);
		
		return $data;
	}
}