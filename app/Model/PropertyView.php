<?php
class PropertyView extends AppModel {
	var $name = 'PropertyView';
	var $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
	);

	/**
	* get data
	*
	* @param string $find - all, list
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	* @param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
	* @return hasil ditemukan return array, hasil tidak ditemukan return false
	*/
	function getData( $find = 'all', $options = array(), $elements = array() ){
        $is_admin   = Configure::read('User.admin');

        $mine = isset($elements['mine'])?$elements['mine']:false;
        $admin_rumahku = isset($elements['admin_rumahku'])?$elements['admin_rumahku']:Configure::read('User.Admin.Rumahku');
        $company = isset($elements['company'])?$elements['company']:false;
        
        $companyData = Configure::read('Config.Company.data');
        $group_id = $this->filterEmptyField($companyData, 'User', 'group_id');
        $parent_id = Configure::read('Principle.id');

		$default_options = array(
			'conditions'=> array(),
			'order'=> array(),
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);

        if( !empty($mine) && empty($admin_rumahku) ) {
			$user_login_id = Configure::read('User.id');
			$user_group_id = Configure::read('User.group_id');

            $data_arr = $this->Property->User->getUserParent($user_login_id);
            $is_sales = Common::hashEmptyField($data_arr, 'is_sales');

            if( !$is_admin || !empty($is_sales)) {
				$is_independent = Common::validateRole('independent_agent', $user_group_id);

				if($is_independent){
					$user_ids = $user_login_id;
				}
				else{
					$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
				}

                $default_options['conditions']['PropertyView.agent_id'] = $user_ids;
            } else {
                if( $group_id == 4 ) {
                    $principle_id = $this->Property->User->getAgents($parent_id, true, 'list', false, array(
                        'role' => 'principle',
                    ));

                    $default_options['conditions']['PropertyView.principle_id'] = $principle_id;
                } else {
                    $default_options['conditions']['PropertyView.principle_id'] = $parent_id;
                }
            }
        }

        if( !empty($company) ) {
            if( $group_id == 4 ) {
                $principle_id = $this->Property->User->getAgents($parent_id, true, 'list', false, array(
                    'role' => 'principle',
                ));
                $default_options['conditions']['PropertyView.principle_id'] = $principle_id;
            } else {
                $default_options['conditions']['PropertyView.principle_id'] = $parent_id;
            }
        }

        return $this->merge_options($default_options, $options, $find);
	}

	function getTotalVisitorReport( $property_id = false, $filter_by = 'city_id', $options = array(), $fromDate = false, $toDate = false ) {

		$result = array();
		$default_options = array(
			'conditions' => array(),
			'fields' => array(),
            'group' => array(),
		);
		if( !empty($property_id) ) {
			$default_options['conditions'] = array_merge($default_options['conditions'], array(
				'PropertyView.property_id' => $property_id,
			));
		}

		if( !empty($filter_by) ) {
			$this->virtualFields['cnt'] = 'COUNT(PropertyView.id)';
			$this->virtualFields[$filter_by] = 'PropertyView.'.$filter_by;
            $default_options['group'] = array(
                'PropertyView.'.$filter_by,
            );
		} else if( !empty($options) ) {
			$default_options = $options;
		}

		if( !empty($fromDate) && !empty($toDate) ) {
            $conditionsDate = array(
                'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\') >=' => $fromDate,
                'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\') <=' => $toDate,
            );
            $default_options['conditions'] = array_merge($default_options['conditions'], $conditionsDate);
        }

        $result = $this->getData('all', $default_options);
        return $result;
	}
	
	function getTotalVisitor( $property_id = false, $fromDate = false, $toDate = false, $options = false, $filter_per_property = false, $type = 'all' ) {
		
		$this->virtualFields['cnt'] = 'COUNT(PropertyView.property_id)';
		$this->virtualFields['created'] = 'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\')';
		
        $values = array();
        $companyData = Configure::read('Config.Company.data');
        $company_group_id = $this->filterEmptyField($companyData, 'User', 'group_id');
		$default_options = array(
			'conditions' => array(),
			'group' => array(
                'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\')',
            ),
            'order' => false,
		);

		if( !empty($property_id) ) {
			$default_options['conditions'] = array(
				'PropertyView.property_id' => $property_id,
			);
		}
		if( !empty($filter_per_property) ) {
			$default_options['group'] = array_merge($default_options['group'], array(
				'PropertyView.property_id',
			));
		}
        if( !empty($fromDate) && !empty($toDate) ) {
            $conditionsDate = array(
                'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\') >=' => $fromDate,
                'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\') <=' => $toDate,
            );
            $default_options['conditions'] = array_merge($default_options['conditions'], $conditionsDate);
        }

        if( !empty($options) ) {
            if( isset($options['conditions']) ) {
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if( isset($options['contain']) ) {
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
                $default_options['contain'] = array_unique($default_options['contain']);
            }
        }

        $contain = $this->filterEmptyField($default_options, 'contain');

        $elements = array(
            'mine' => true,
            'admin_rumahku' => false,
        );

    	if( $type == 'all' ) {
        	$values = $this->getData('all', $default_options, $elements);
        }

        $total = $this->getData('count', $default_options, $elements);

        return array(
        	'data' => !empty($values)?$values:false,
        	'total' => $total,
    	);
	}

	function doSave ( $data ) {
		if( !empty($data) ){
			$this->create();
			$this->set($data);
			
			if($this->save()){
				return true;
			} else {
				return false;
			}
		}
	}

	public function _callRefineParams( $data = '', $default_options = false, $modelName = 'PropertyView' ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));
        $dateFrom = $this->filterEmptyField($data, 'named', 'date_from', false, array(
            'addslashes' => true,
        ));
        $dateTo = $this->filterEmptyField($data, 'named', 'date_to', false, array(
            'addslashes' => true,
        ));
        $sort = $this->filterEmptyField($data, 'named', 'sort', false, array(
            'addslashes' => true,
        ));
        $direction = $this->filterEmptyField($data, 'named', 'direction', 'ASC', array(
            'addslashes' => true,
        ));
        
        $region = $this->filterEmptyField($data, 'named', 'region', false, array(
            'addslashes' => true,
        ));
        $city = $this->filterEmptyField($data, 'named', 'city', false, array(
            'addslashes' => true,
        ));
        $subareas = $this->filterEmptyField($data, 'named', 'subareas', false, array(
            'addslashes' => true,
        ));
        $type = $this->filterEmptyField($data, 'named', 'type', false, array(
            'addslashes' => true,
        ));
        $beds = $this->filterEmptyField($data, 'named', 'beds', false, array(
            'addslashes' => true,
        ));
        $baths = $this->filterEmptyField($data, 'named', 'baths', false, array(
            'addslashes' => true,
        ));
        $lot_size = $this->filterEmptyField($data, 'named', 'lot_size', false, array(
            'addslashes' => true,
        ));
        $building_size = $this->filterEmptyField($data, 'named', 'building_size', false, array(
            'addslashes' => true,
        ));
        $lot_width = $this->filterEmptyField($data, 'named', 'lot_width', false, array(
            'addslashes' => true,
        ));
        $lot_length = $this->filterEmptyField($data, 'named', 'lot_length', false, array(
            'addslashes' => true,
        ));
        $price = $this->filterEmptyField($data, 'named', 'price', false, array(
            'addslashes' => true,
        ));
        $certificate = $this->filterEmptyField($data, 'named', 'certificate', false, array(
            'addslashes' => true,
        ));
        $condition = $this->filterEmptyField($data, 'named', 'condition', false, array(
            'addslashes' => true,
        ));
        $furnished = $this->filterEmptyField($data, 'named', 'furnished', false, array(
            'addslashes' => true,
        ));
        $property_action = $this->filterEmptyField($data, 'named', 'property_action', false, array(
            'addslashes' => true,
        ));
        $property_direction = $this->filterEmptyField($data, 'named', 'property_direction', false, array(
            'addslashes' => true,
        ));
        $principle_id = $this->filterEmptyField($data, 'named', 'principle_id', false, array(
            'addslashes' => true,
        ));

        if( !empty($keyword) ) {
            $default_options['conditions']['OR'] = array(
                'Property.mls_id LIKE ' => '%'.$keyword.'%',
                'Property.title LIKE ' => '%'.$keyword.'%',
                'Property.keyword LIKE ' => '%'.$keyword.'%'
            );

            $default_options['contain'][] = 'Property';
        }

        if( !empty($region) ) {
            $default_options['conditions']['PropertyAddress.region_id'] = $region;
            $default_options['contain'][] = 'PropertyAddress';
        }
        if( !empty($city) ) {
            $default_options['conditions']['PropertyAddress.city_id'] = $city;
            $default_options['contain'][] = 'PropertyAddress';
        }
        if( !empty($subarea) ) {
            $default_options['conditions']['PropertyAddress.subarea_id'] = $subarea;
            $default_options['contain'][] = 'PropertyAddress';
        }
        if( !empty($subareas) ) {
            $subareas = urldecode($subareas);
            $subareas = explode(',', $subareas);
            $default_options['conditions']['PropertyAddress.subarea_id'] = $subareas;
            $default_options['contain'][] = 'PropertyAddress';
        }

        if( !empty($property_action) ) {
            $default_options['conditions']['Property.property_action_id'] = $property_action;
            $default_options['contain'][] = 'Property';
        }
        if( !empty($type) ) {
            $type = urldecode($type);
            $type = explode(',', $type);
            $default_options['conditions']['Property.property_type_id'] = $type;
            $default_options['contain'][] = 'Property';
        }
        if( !empty($beds) ) {
            $this->virtualFields['total_beds'] = 'beds+beds_maid';
            $default_options['conditions']['total_beds >='] = $beds;
            $default_options['conditions']['PropertyType.is_residence'] = 1;

            $default_options['contain'][] = 'PropertyAsset';
            $default_options['contain'][] = 'PropertyType';
        }
        if( !empty($baths) ) {
            $this->virtualFields['total_baths'] = 'baths+baths_maid';
            $default_options['conditions']['total_baths >='] = $baths;
            $default_options['conditions']['PropertyType.is_residence'] = 1;

            $default_options['contain'][] = 'PropertyAsset';
            $default_options['contain'][] = 'PropertyType';
        }
        if( !empty($lot_size) ) {
            $default_options['conditions']['PropertyAsset.lot_size <='] = $lot_size;
            $default_options['conditions']['AND']['OR'][]['PropertyType.is_lot'] = 1;
            $default_options['conditions']['AND']['OR'][]['PropertyType.is_space'] = 1;

            $default_options['contain'][] = 'PropertyAsset';
            $default_options['contain'][] = 'PropertyType';
        }
        if( !empty($building_size) ) {
            $default_options['conditions']['PropertyAsset.building_size <='] = $building_size;
            $default_options['conditions']['AND']['OR'][]['PropertyType.is_building'] = 1;
            $default_options['conditions']['AND']['OR'][]['PropertyType.is_space'] = 1;
            
            $default_options['contain'][] = 'PropertyAsset';
            $default_options['contain'][] = 'PropertyType';
        }
        if( !empty($lot_width) ) {
            $default_options['conditions']['PropertyAsset.lot_width'] = $lot_width;
            $default_options['contain'][] = 'PropertyAsset';
        }
        if( !empty($lot_length) ) {
            $default_options['conditions']['PropertyAsset.lot_length'] = $lot_length;
            $default_options['contain'][] = 'PropertyAsset';
        }

        if( !empty($price) ) {
            $default_options['contain'][] = 'PropertySold';

            $price = explode('-', $price);
            $min_price = !empty($price[0])?$price[0]:false;
            $max_price = !empty($price[1])?$price[1]:false;

            if( !empty($min_price) ) {
                $default_options['conditions']['(CASE WHEN Property.sold = 1 THEN PropertySold.price_sold ELSE Property.price_measure END) >='] = $min_price;
            }
            if( !empty($max_price) ) {
                $default_options['conditions']['(CASE WHEN Property.sold = 1 THEN PropertySold.price_sold ELSE Property.price_measure END) <='] = $max_price;
            }
        }
        if( !empty($certificate) ) {
            $certificates = $this->Property->Certificate->getData('list', array(
                'conditions' => array(
                    'Certificate.slug' => $certificate,
                ),
                'fields' => array(
                    'Certificate.id', 'Certificate.id',
                ),
                'cache' => __('Certificate.Slug.List.%s', $certificate),
            ));

            if( !empty($certificates) ) {
                $default_options['conditions']['Property.certificate_id'] = $certificates;
            } else {
                $default_options['conditions']['Property.certificate_id'] = $certificate;
            }
        }
        
        if( !empty($condition) ) {
            $default_options['conditions']['PropertyAsset.property_condition_id'] = $condition;
            $default_options['contain'][] = 'PropertyAsset';
        }
        if( !empty($furnished) ) {
            $default_options['conditions']['PropertyAsset.furnished'] = $furnished;
            $default_options['contain'][] = 'PropertyAsset';
        }
        if( !empty($property_direction) ) {
            $default_options['conditions']['PropertyAsset.property_direction_id'] = $property_direction;
            $default_options['contain'][] = 'PropertyAsset';
        }

        if( !empty($dateFrom) ) {
            $field = 'created';
            $default_options['conditions']['DATE_FORMAT('.$modelName.'.'.$field.', \'%Y-%m-%d\') >='] = $dateFrom;
            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT('.$modelName.'.'.$field.', \'%Y-%m-%d\') <='] = $dateTo;
            }
        }

        if( !empty($principle_id) || $sort == 'User.full_name' ) {
            $this->bindModel(array(
                'belongsTo' => array(
                    'User' => array(
                        'className' => 'User',
                        'foreignKey' => false,
                        'conditions' => array(
                            'Property.user_id = User.id',
                        ),
                    ),
                )
            ), false);

            $default_options['contain'][] = 'Property';
            $default_options['contain'][] = 'User';
        }

        if( !empty($sort) ) {
            if( $sort == 'Property.mls_id' ) {
                $default_options['contain'][] = 'Property';
            }
        }

        if( !empty($principle_id) ) {
            if( !is_array($principle_id) ) {
                $principle_id = explode(',', $principle_id);
            }
            
            $default_options['conditions']['User.parent_id'] = $principle_id;
        }

        if( !empty($default_options['contain']) ) {
            $default_options['contain'] = array_unique($default_options['contain']);
        }

        return $default_options;
    }

    function getViewVisitor($find = 'all', $options = array()){
        $default_options = array(
            'group' => array(
                'DATE_FORMAT(PropertyView.created, \'%Y-%m\')',
            ),
        );

        if( !empty($options) ) {
            $default_options = array_merge($default_options, $options);
        }

        $this->virtualFields['cnt'] = 'COUNT(PropertyView.id)';
        return $this->getData($find, $default_options, array(
            'mine' => true,
            'company' => true,
        ));
    }

    function getVisitor($from = false, $to = false){
        $date_format = '%Y-%m-%d';

        $conditions = array();
        if(!empty($from)){
            $conditions['DATE_FORMAT(PropertyView.created, "'.$date_format.'") >='] = $from;
        }
        if(!empty($to)){
            $conditions['DATE_FORMAT(PropertyView.created, "'.$date_format.'") <='] = $to;
        }

        $periode_type = 'week';
        if(!empty($from) && !empty($to)){
            $date_diff = Common::monthDiff($from, $to);

            if($date_diff > 3){
                $periode_type = 'month';
            }
        }

        $data = $this->getViewVisitor('all', array(
            'conditions' => $conditions,
        ));

        if(!empty($data)){
            $temp = array();
            $temp_arr = array();
            foreach ($data as $key => $value) {
                $date   = Common::hashEmptyField($value, 'PropertyView.created');
                $val    = (int) Common::hashEmptyField($value, 'PropertyView.cnt');

                if($periode_type == 'month'){
                    $month_date = date('Y-m', strtotime($date));

                    $date_format = 'M Y';
                }else{
                    $month_date = $date;

                    $date_format = 'Y-m-d';
                }

                $temp_total = (int) Common::hashEmptyField($temp_arr, $month_date, 0);

                $temp_arr[$month_date] = $temp_total+$val;

                $temp[$month_date] = array(
                    date($date_format, strtotime($date)),
                    $temp_arr[$month_date]
                );
            }

            $data = $temp;
        }

        $fields = array(
            'Periode',
            'Pengunjung',
        );

        return array(
            'rows'              => $data,
            'fields'            => $fields,
        );
    }

    function topVisitor($from, $to){
        $date_format = '%Y-%m-%d';

        $conditions = array();
        if(!empty($from)){
            $conditions['DATE_FORMAT(PropertyView.created, "'.$date_format.'") >='] = $from;
        }
        if(!empty($to)){
            $conditions['DATE_FORMAT(PropertyView.created, "'.$date_format.'") <='] = $to;
        }

        $data = $this->getViewVisitor('first', array(
            'conditions' => $conditions,
            'order' => array(
                'PropertyView.cnt' => 'DESC'
            ),
        ));

        if( !empty($data) ) {
            $data = $this->getMergeList($data, array(
                'contain' => array(
                    'Property' => array(
                        'elements' => array(
                            'status' => 'all',
                            'company' => false,
                        ),
                    ),
                ),
            ));
        }

        return $data;
    }
}
?>