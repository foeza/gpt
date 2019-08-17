<?php
class RmActivityComponent extends Component {
	var $components = array('Auth', 'RmCommon', 'RumahkuApi'); 

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function doBeforeView(){
		$data = $this->controller->request->data;
		$user_activities = array();

		if( empty($data) ) {
			$data = Hash::insert($data, 'Activity.action_date', date('d/m/Y'));
		} else {
			$data = Common::dataConverter($data, array(
				'date' => array(
					'Activity' => array(
						'action_date',
					),
				),
			), true);

			if( !empty($data['Activity']) ) {
				$activityUser = $data['Activity'];
			} else if( !empty($data['ActivityUser']) ) {
				$activityUser = $data['ActivityUser'];
			} else {
				$activityUser = null;
			}

			$expert_category_id = Common::hashEmptyField($activityUser, 'expert_category_id');
			$expert_category_component_active_id = Common::hashEmptyField($activityUser, 'expert_category_component_active_id');
			$type = Common::hashEmptyField($activityUser, 'type');
			$point_type = Common::hashEmptyField($activityUser, 'point_type');
			
			$user_activities = Common::hashEmptyField($data, 'ActivityUser.user_id');
			$user_activities = !empty($user_activities)?$user_activities:Set::extract('/ActivityUser/user_id', $data);

			$this->pick_expert_category($expert_category_id);
			$this->pick_component_category($expert_category_id, $type);
			$this->pick_component($expert_category_id, $type, $expert_category_component_active_id);
			$this->pick_get_point($expert_category_id, $expert_category_component_active_id, $point_type);
		}

		$this->controller->request->data = $data;
		$expertCategories = $this->controller->Activity->ExpertCategory->ViewExpertCategoryCompany->getData('list', array(
			'order' => array(
				'ViewExpertCategoryCompany.name',
			),
		), array(
			'allow_me' => true,
			'is_listing' => true,
		));

		$this->controller->User->virtualFields['label'] = 'CONCAT(TRIM(CONCAT(User.first_name, " ", User.last_name)), " ( ", User.email, " )")';

		$userOptions = array(
			'fields' => array(
				'User.id',
				'User.label',
			),
			'order' => array(
				'User.label',
			),
		);
		$userElements = array(
			'status' => 'active',
			'role' => 'agent',
			'company' => true,
		);

		$agents = $this->controller->User->getData('list', array_merge($userOptions, array(
			'conditions' => array(
				'User.id NOT' => $user_activities,
			),
		)), $userElements);
		$agentActivities = $this->controller->User->getData('list', array_merge($userOptions, array(
			'conditions' => array(
				'User.id' => $user_activities,
			),
		)), $userElements);

		$this->controller->set(array(
			'agents' => $agents,
			'agentActivities' => $agentActivities,
			'expertCategories' => $expertCategories,
			'active_menu' => 'expert_activities'
		));
	}

	function checkDataValid ( $pointData, $expert_category_id, $empty_value = false ) {
		$data_valid = array();
		$default_expert_category_component = array();

		if( !empty($pointData) ) {
			$tmpPointData = array();
			$default_expert_category_component_actives = Set::extract('/ViewExpertCategoryCompanyDetail/expert_category_component_active_id', $pointData);

			$this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['label'] = 'CONCAT(ViewExpertCategoryCompanyDetail.from, \'-\', ViewExpertCategoryCompanyDetail.expert_category_component_active_id)';
			$this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['cnt'] = 'COUNT(ViewExpertCategoryCompanyDetail.expert_category_component_active_id)';

			$default_expert_category_component = $this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('list', array(
				'fields' => array(
					'ViewExpertCategoryCompanyDetail.label',
					'ViewExpertCategoryCompanyDetail.cnt',
				),
				'conditions' => array(
					'OR' => array(
						array(
							'ViewExpertCategoryCompanyDetail.type' => array( 'conditions', 'other', 'property_action', 'ebrosur_action' ),
							'ViewExpertCategoryCompanyDetail.expert_category_component_active_id' => $default_expert_category_component_actives,
						),
						array(
							'ViewExpertCategoryCompanyDetail.type' => array( 'schema' ),
							'ViewExpertCategoryCompanyDetail.expert_category_id' => $expert_category_id,
							'ViewExpertCategoryCompanyDetail.point_type' => !empty($empty_value)?array( 'min', 'plus' ):'min',
						),
					),
				),
				'group' => array(
					'ViewExpertCategoryCompanyDetail.label',
				),
			), array(
				'company_id' => false,
			));

			foreach ($pointData as $key => $pointVal) {
				$from = Common::hashEmptyField($pointVal, 'ViewExpertCategoryCompanyDetail.from');
				$expert_category_component_active_id = Common::hashEmptyField($pointVal, 'ViewExpertCategoryCompanyDetail.expert_category_component_active_id');
				$point_slug = __('%s-%s', $from, $expert_category_component_active_id);
				
				if( !empty($tmpPointData[$point_slug]) ) {
					$tmpPointData[$point_slug] += 1;
				} else {
					$tmpPointData[$point_slug] = 1;
				}
			}

			if( !empty($default_expert_category_component) ) {
				foreach ($default_expert_category_component as $curr_id => $curr_cnt) {
					$cnt = Common::hashEmptyField($tmpPointData, $curr_id, 0);

					if( $cnt == $curr_cnt ) {
						$data_valid[] = explode('-', $curr_id);
					}
				}
			}
		}

		return array(
			'default_expert_category_component' => $default_expert_category_component,
			'data_valid' => $data_valid,
		);
	}

	function doBeforeSave($data = false, $id = null, $users = null, $activity_user_id = null){
		$isCompanyAdmin	= Configure::read('User.companyAdmin');
		$data = Hash::insert($data, 'Activity.id', $id);
		$data = Common::dataConverter($data, array(
			'date' => array(
				'Activity' => array(
					'action_date',
				),
			),
		));

		// Jika dia bukan admin akan dipaksa ke user yg login
		if( empty($isCompanyAdmin) ) {
			$user_login_id = Configure::read('User.id');

			$users[$user_login_id] = $user_login_id;
			$activity_status = 'pending';
		} else {
			$users = Common::hashEmptyField($data, 'ActivityUser.user_id', $users);
			$activity_status = 'approved';
		}

		if( !empty($users) ) {
			$dataUser = array();
			$expert_category_id = Common::hashEmptyField($data, 'Activity.expert_category_id');
			$action_date = Common::hashEmptyField($data, 'Activity.action_date');
			$achievement_value = Common::hashEmptyField($data, 'Activity.value', 0);
			$note = Common::hashEmptyField($data, 'Activity.note');

			$expert_category = $this->controller->ExpertCategory->getData('first', array(
				'conditions' => array(
					'ExpertCategory.id' => $expert_category_id,
				),
			), array(
				'with_default' => true,
			));
			$expert_category = $this->controller->ExpertCategory->getMergeList($expert_category, array(
				'contain' => array(
					'Schema' => array(
						'type' => 'first',
						'uses' => 'ExpertCategoryConfiguration',
						'elements' => array(
							'with_default' => true,
						),
						'conditions' => array(
							'type' => 'scheme',
						),
					),
				),
			));
			$schema = Common::hashEmptyField($expert_category, 'Schema.value');
			$point_type = Common::hashEmptyField($data, 'Activity.point_type');
			$point = 0;

			$dataSave = array(
				'id' => $activity_user_id,
				'expert_category_id' => $expert_category_id,
				'point_type' => $point_type,
				'action_date' => $action_date,
				'value' => $achievement_value,
				'note' => $note,
				'activity_status' => $activity_status,
			);

			if( !empty($schema) ) {
				switch ($schema) {
					case 'comparison':
					case 'direct':
					case 'accumulation':
						$expert_category_component_active_id = Common::hashEmptyField($data, 'Activity.expert_category_component_active_id');
						$type = Common::hashEmptyField($data, 'Activity.type');
						$options = array(
							'conditions' => array(
								'ViewExpertCategoryCompanyDetail.expert_category_id' => $expert_category_id,
							),
						);

						if( !empty($expert_category_component_active_id) && $type == 'direct' ) {
							$options['conditions']['ViewExpertCategoryCompanyDetail.expert_category_component_active_id'] = $expert_category_component_active_id;
							$options['conditions']['ViewExpertCategoryCompanyDetail.slug'] = $type;
						} else {
							$day = Common::hashEmptyField($data, 'Activity.day');
							$time = Common::hashEmptyField($data, 'Activity.time');
							$empty_value = true;
							$optionsOR = array();

							if( !empty($achievement_value) ) {
								// Supaya Jadi Number
								$achievement_value = $achievement_value * 1;
								$optionsOR[] = array(
									__('
										CASE WHEN ViewExpertCategoryCompanyDetail.compare = \'less_than\'
											THEN ViewExpertCategoryCompanyDetail.value > %s
										WHEN ViewExpertCategoryCompanyDetail.compare = \'between\'
											THEN ViewExpertCategoryCompanyDetail.value_end >= %s
											AND ViewExpertCategoryCompanyDetail.value <= %s
										WHEN ViewExpertCategoryCompanyDetail.compare = \'more_than\'
											THEN ViewExpertCategoryCompanyDetail.value_end < %s
										ELSE ViewExpertCategoryCompanyDetail.value = %s
										END
									', $achievement_value, $achievement_value, $achievement_value, $achievement_value, $achievement_value),
									'ViewExpertCategoryCompanyDetail.slug' => 'ranges',
								);

								$optionsOR[] = array(
									'ViewExpertCategoryCompanyDetail.value' => $achievement_value,
									'ViewExpertCategoryCompanyDetail.slug' => 'other',
								);

								$empty_value = false;
							}
							if( !empty($day) ) {
								$optionsOR[] = array(
									'ViewExpertCategoryCompanyDetail.slug' => 'day',
									'ViewExpertCategoryCompanyDetail.value' => $day,
								);

								$empty_value = false;
								$dataSave['day'] = $day;
							}
							if( !empty($time) ) {
								$optionsOR[] = array(
									__('
										CASE WHEN ViewExpertCategoryCompanyDetail.compare = \'less_than\'
											THEN ViewExpertCategoryCompanyDetail.value > \'%s\'
										WHEN ViewExpertCategoryCompanyDetail.compare = \'between\'
											THEN ViewExpertCategoryCompanyDetail.value_end >= \'%s\'
											AND ViewExpertCategoryCompanyDetail.value <= \'%s\'
										WHEN ViewExpertCategoryCompanyDetail.compare = \'more_than\'
											THEN ViewExpertCategoryCompanyDetail.value_end < \'%s\'
										ELSE ViewExpertCategoryCompanyDetail.value = \'%s\'
										END
									', $time, $time, $time, $time, $time),
									'ViewExpertCategoryCompanyDetail.slug' => 'time',
								);

								$empty_value = false;
								$dataSave['time'] = $time;
							}
							
							if( !empty($empty_value) )	 {
								$options['conditions'][]['OR'] = array(
									'ViewExpertCategoryCompanyDetail.type' => array( 'schema' ),
									array(
										'OR' => array(
											array( 'ViewExpertCategoryCompanyDetail.value' => $achievement_value ),
											array( 'ViewExpertCategoryCompanyDetail.value' => NULL ),
											array( 'ViewExpertCategoryCompanyDetail.value' => '' ),
										),
										'ViewExpertCategoryCompanyDetail.slug' => 'other',
									),
								);
							} else {
								$options['conditions'][]['OR'] = $optionsOR;
							}
						}

						switch ($type) {
							case 'direct':
								$pointData = $this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('first', $options);

								if( empty($point_type) ) {
									$point_type = Common::hashEmptyField($pointData, 'ViewExpertCategoryCompanyDetail.point_type');
								}

								if( !empty($point_type) ) {
									$point = Common::hashEmptyField($pointData, 'ViewExpertCategoryCompanyDetail.point_'.$point_type, 0);
								} else {
									$point = Common::hashEmptyField($pointData, 'ViewExpertCategoryCompanyDetail.point', 0);
								}

								switch ($point_type) {
									case 'min':
										$point = $point * -1;
										break;
								}

								foreach ($users as $key => $user_id) {
									$dataUser[] = array_merge($dataSave, array(
										'user_id' => $user_id,
										'expert_category_component_active_id' => $expert_category_component_active_id,
										'type' => $type,
										'point_type' => $point_type,
										'point' => $point,
									));
								}
								break;
							
							default:
								$pointData = $this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('all', $options);
								$dataValid = $this->checkDataValid($pointData, $expert_category_id, $empty_value);
								
								$data_valid = Common::hashEmptyField($dataValid, 'data_valid');
								$default_expert_category_component = Common::hashEmptyField($dataValid, 'default_expert_category_component');

								if( !empty($data_valid) ) {
									// Untuk Sorting urutas selain schema di atas
									$this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->virtualFields['order_sort'] = 'CASE WHEN ViewExpertCategoryCompanyDetail.type = \'schema\' THEN 1 ELSE 0 END';

									foreach ($data_valid as $key => $valid) {
										$from = !empty($valid[0])?$valid[0]:null;
										$curr_id = !empty($valid[1])?$valid[1]:null;

		            					$default_exp_cat_comp = $this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('first', array(
											'conditions' => array(
												'ViewExpertCategoryCompanyDetail.type' => array( 'conditions', 'other', 'property_action', 'schema', 'ebrosur_action' ),
												'ViewExpertCategoryCompanyDetail.from' => $from,
												'ViewExpertCategoryCompanyDetail.expert_category_component_active_id' => $curr_id,
											),
											'order' => array(
												'ViewExpertCategoryCompanyDetail.order_sort',
											),
										));

		            					if( !empty($default_expert_category_component) ) {
											$point_type = Common::hashEmptyField($default_exp_cat_comp, 'ViewExpertCategoryCompanyDetail.point_type', $point_type);
											
											if( $from == 'plus' ) {
												$point = Common::hashEmptyField($default_exp_cat_comp, 'ViewExpertCategoryCompanyDetail.point_plus', 0);
											} else {
												$point = Common::hashEmptyField($default_exp_cat_comp, 'ViewExpertCategoryCompanyDetail.point_min', 0);
												$point = abs($point) * -1;
											}

											foreach ($users as $key => $user_id) {
												$dataUser[] = array_merge($dataSave, array(
													'expert_category_component_active_id' => $curr_id,
													'user_id' => $user_id,
													'point_type' => $point_type,
													'point' => $point,
												));
											}
										}
									}
								} else {
									foreach ($users as $key => $user_id) {
										$dataUser[] = array_merge($dataSave, array(
											// 'expert_category_component_active_id' => $curr_id,
											'user_id' => $user_id,
										));
									}
								}
								break;
						}

						$data = Hash::insert($data, 'Activity.point', $point);
						break;
				}

				$data = Hash::insert($data, 'ActivityUser', $dataUser);
			}
		} else {
			$data = Hash::insert($data, 'Activity.flag_user', false);
		}

		return $data;
	}

	function pick_expert_category( $id = null ){
		$value = $this->controller->ExpertCategory->getData('first', array(
			'conditions' => array(
				'ExpertCategory.id' => $id,
			),
		), array(
			'with_default' => true,
		));
		$render = '/Elements/blocks/activities/get_action';

		if( !empty($value) ){
			$value = $this->controller->ExpertCategory->getMergeList($value, array(
				'contain' => array(
					'ExpertCategoryActive' => array(
						'type' => 'first',
						'elements' => array(
							'is_company' => true,
						),
						'contain' => array(
							'ViewExpertCategoryCompanyDetail' => array(
								'type' => 'list',
								'fields' => array(
									'ViewExpertCategoryCompanyDetail.slug',
									'ViewExpertCategoryCompanyDetail.type_name',
								),
								'conditions' => array(
									'ViewExpertCategoryCompanyDetail.type' => 'schema',
								),
								'group' => array(
									'ViewExpertCategoryCompanyDetail.slug',
								),
							),
						),
					),
					'Schema' => array(
						'type' => 'first',
						'uses' => 'ExpertCategoryConfiguration',
						'elements' => array(
							'with_default' => true,
						),
						'conditions' => array(
							'type' => 'scheme',
						),
					),
					'Condition' => array(
						'uses' => 'ExpertCategoryConfiguration',
						'elements' => array(
							'with_default' => true,
						),
						'conditions' => array(
							'ExpertCategoryConfiguration.type' => 'conditions',
						),
						'group' => array(
							'ExpertCategoryConfiguration.value',
						),
					),
				),
			));

            $conditions = Set::extract('/Condition/ExpertCategoryConfiguration/value', $value);
            $schema_value = Common::hashEmptyField($value, 'Schema.value');

            switch ($schema_value) {
            	case 'direct':
            		$direct_conditions = Common::hashEmptyField($value, 'ExpertCategoryActive.ViewExpertCategoryCompanyDetail');

            		if( !empty($direct_conditions) && !empty($direct_conditions['direct']) ) {
            			$this->controller->set(array(
            				'direct_conditions' => $direct_conditions,
        				));
            			
            			$render = '/Elements/blocks/activities/get_action';
            		}
            		break;
            	case 'comparison':
            		$conditions = Common::hashEmptyField($value, 'Condition');

        			$this->controller->set(array(
        				'conditions' => $conditions,
    				));
        			$render = '/Elements/blocks/activities/get_action_comparison';
            		break;
            	case 'accumulation':
            		$conditions = Common::hashEmptyField($value, 'Condition');
					$render = '/Elements/blocks/activities/get_action_comparison';

        			$this->controller->set(array(
        				'conditions' => $conditions,
    				));
            		break;
            }
		} else {
            $schema_value = null;
		}

		$this->controller->set(array(
			'expert_category_id' => $id,
			'schema' => $schema_value,
		));

		return $render;
	}

	function pick_component_category( $expert_category_id = null, $type = null ){
		$value = $this->controller->ExpertCategory->getData('first', array(
			'conditions' => array(
				'ExpertCategory.id' => $expert_category_id,
			),
		), array(
			'with_default' => true,
		));
		$render = '/Elements/blocks/activities/get_type';

		if( !empty($value) ){
			$expert_category_components = $this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('list', array(
				'fields' => array(
					'ViewExpertCategoryCompanyDetail.expert_category_component_active_id',
					'ViewExpertCategoryCompanyDetail.name',
				),
				'conditions' => array(
					'ViewExpertCategoryCompanyDetail.slug' => $type,
					'ViewExpertCategoryCompanyDetail.expert_category_id' => $expert_category_id,
				),
				'group' => array(
					'ViewExpertCategoryCompanyDetail.expert_category_component_active_id'
				),
			));

			$this->controller->set(array(
				'expert_category_components' => $expert_category_components,
			));

            switch ($type) {
            	case 'direct':
        			$render = '/Elements/blocks/activities/get_type';
            		break;
            	case 'other':
            	case 'accumulation':
        			$render = '/Elements/blocks/activities/get_type_manual';
            		break;
            }
		}

		$this->controller->set(array(
			'expert_category_id' => $expert_category_id,
			'activity_type' => $type,
		));

		return $render;
	}

	function pick_component( $expert_category_id = null, $activity_type = null, $expert_category_component_active_id = null ){
		$value = $this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('first', array(
			'conditions' => array(
				'ViewExpertCategoryCompanyDetail.expert_category_component_active_id' => $expert_category_component_active_id,
				'ViewExpertCategoryCompanyDetail.expert_category_id' => $expert_category_id,
				'ViewExpertCategoryCompanyDetail.slug' => $activity_type,
			),
		));
		$render = '/Elements/blocks/activities/get_point_type';

		if( !empty($value) ){
			$check_point = Common::hashEmptyField($value, 'ViewExpertCategoryCompanyDetail.check_point');
			$check_pinalty = Common::hashEmptyField($value, 'ViewExpertCategoryCompanyDetail.check_pinalty');

			if( empty($check_point) && empty($check_pinalty) ) {
				return false;
			} else {
				if( !empty($check_point) && !empty($check_pinalty) ) {
    				$render = '/Elements/blocks/activities/get_point_type';
				} else {
					if( !empty($check_point) ) {
						$point_type = 'plus';
					} else if( !empty($check_pinalty) ) {
						$point_type = 'min';
					}
    				
					$render = $this->pick_get_point($expert_category_id, $expert_category_component_active_id, $point_type);
				}
			}
		}

		$this->controller->set(array(
			'expert_category_id' => $expert_category_id,
			'expert_category_component_active_id' => $expert_category_component_active_id,
			'expert_category_component' => $value,
		));

		return $render;
	}

	function pick_get_point( $expert_category_id = null, $expert_category_component_active_id = null, $check_point_type = null ){
		$value = $this->controller->ExpertCategory->ExpertCategoryActive->ViewExpertCategoryCompanyDetail->getData('first', array(
			'conditions' => array(
				'ViewExpertCategoryCompanyDetail.expert_category_component_active_id' => $expert_category_component_active_id,
				'ViewExpertCategoryCompanyDetail.expert_category_id' => $expert_category_id,
			),
		));

		$point = Common::hashEmptyField($value, 'ViewExpertCategoryCompanyDetail.point_'.$check_point_type, 0);

		switch ($check_point_type) {
			case 'min':
				$point = $point * -1;
				break;
		}

		$this->controller->set(array(
			'point' => $point,
		));
		
		return '/Elements/blocks/activities/get_point';
	}

	function pick_input_value( $expert_category_component_active_id = null ){
		$component = $this->controller->ExpertCategory->ExpertCategoryActive->ExpertCategoryComponentActive->getData('first', array(
			'conditions' => array(
				'ExpertCategoryComponentActive.id' => $expert_category_component_active_id,
			),
		));
		$component = $this->controller->ExpertCategory->ExpertCategoryActive->ExpertCategoryComponentActive->getMergeList($component, array(
			'contain' => array(
				'ExpertCategoryCompany' => array(
					'contain' => array(
						'Condition' => array(
							'uses' => 'ExpertCategoryCompanyDetail',
							'conditions' => array(
								'type' => array( 'conditions', 'other', 'property_action', 'ebrosur_action' ),
							),
							'order' => array(
								'ExpertCategoryCompanyDetail.id',
							),
						),
					),
				),
			),
		));

		$conditions = Common::hashEmptyField($component, 'ExpertCategoryCompany.Condition');

		$this->controller->set(array(
			'conditions' => $conditions,
		));
		$render = '/Elements/blocks/activities/get_input_value_form';

		$this->controller->set(array(
			'expert_category_component_active_id' => $expert_category_component_active_id,
		));

		return $render;
	}
}