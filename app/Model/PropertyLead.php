<?php
class PropertyLead extends AppModel {
	var $name = 'PropertyLead';
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
	function getData( $find = 'all', $options = array() ){
		$default_options = array(
			'conditions'=> array(),
			'order'=> array(
				'PropertyLead.created' => 'ASC',
			),
            'offset' => false,
			'contain' => array(),
			'fields' => array(),
			'group' => array(),
		);

        return $this->merge_options($default_options, $options, $find);
	}

	function getTotalLeadReport( $property_id = false, $filter_by = 'city_id', $fromDate = false, $toDate = false ) {

		$result = array();
		$default_options = array(
			'conditions' => array(),
			'fields' => array(),
            'group' => array(),
		);
		if( !empty($property_id) ) {
			$default_options['conditions'] = array_merge($default_options['conditions'], array(
				'PropertyLead.property_id' => $property_id,
			));
		}

		if( !empty($filter_by) ) {
			$this->virtualFields['cnt'] = 'COUNT(PropertyLead.id)';
			$this->virtualFields[$filter_by] = 'PropertyLead.'.$filter_by;
            $default_options['group'] = array(
                'PropertyLead.'.$filter_by,
            );
		} else if( !empty($options) ) {
			$default_options = $options;
		}

		if( !empty($fromDate) && !empty($toDate) ) {
            $conditionsDate = array(
                'DATE_FORMAT(PropertyLead.created, \'%Y-%m-%d\') >=' => $fromDate,
                'DATE_FORMAT(PropertyLead.created, \'%Y-%m-%d\') <=' => $toDate,
            );
            $default_options['conditions'] = array_merge($default_options['conditions'], $conditionsDate);
        }

        $result = $this->getData('all', $default_options);
        return $result;
	}

	function getTotalLead( $property_id = false, $fromDate = false, $toDate = false, $options = false, $filter_per_property = false, $type = 'all' ) {

		$this->virtualFields['cnt'] = 'COUNT(PropertyLead.property_id)';
		$this->virtualFields['created'] = 'DATE_FORMAT(PropertyLead.created, \'%Y-%m-%d\')';

		$default_options = array(
			'conditions' => array(),
            'group' => array(
                'DATE_FORMAT(PropertyLead.created, \'%Y-%m-%d\')',
            ),
            'contain' => array(
            	'Property',
            ),
            'order' => false,
		);
		if( !empty($property_id) ) {
			$default_options['conditions'] = array(
				'PropertyLead.property_id' => $property_id,
			);
		}
		if( !empty($filter_per_property) ) {
			$default_options['group'] = array_merge($default_options['group'], array(
				'PropertyLead.property_id',
			));
		}
        if( !empty($fromDate) && !empty($toDate) ) {
            $conditionsDate = array(
                'DATE_FORMAT(PropertyLead.created, \'%Y-%m-%d\') >=' => $fromDate,
                'DATE_FORMAT(PropertyLead.created, \'%Y-%m-%d\') <=' => $toDate,
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

    	if( $type == 'all' ) {
            $values = $this->getData('all', $default_options);
        }

        $total = $this->getData('count', $default_options);

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

	public function _callRefineParams( $data = '', $default_options = false, $modelName = 'PropertyLead' ) {
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

        if( !empty($keyword) ) {
            // $this->Property->virtualFields['find_keyword'] = sprintf(
            //     'MATCH(
            //         Property.title,
            //         Property.keyword
            //     ) AGAINST(\'%s\' IN BOOLEAN MODE)', $keyword
            // );

            // $default_options['conditions']['OR'] = array(
            //     'Property.mls_id LIKE ' => '%'.$keyword.'%',
            //     'MATCH(
            //         Property.title,
            //         Property.keyword
            //     ) AGAINST(? IN BOOLEAN MODE)' => $keyword,
            // );

            $default_options['conditions']['OR'] = array(
                'Property.mls_id LIKE ' => '%'.$keyword.'%',
                'Property.title LIKE ' => '%'.$keyword.'%',
                'Property.keyword LIKE ' => '%'.$keyword.'%'
            );

            $users = $this->Property->User->getData('list', array(
                'conditions' => array(
                    'OR' => array(
                        'MATCH(User.first_name) AGAINST(? IN BOOLEAN MODE)' => $keyword,
                        'MATCH(IFNULL(User.last_name, \'\')) AGAINST(? IN BOOLEAN MODE)' => $keyword,
                        'CONCAT(User.first_name,\' \',IFNULL(User.last_name, \'\')) LIKE' => '%'.$keyword.'%',
                        'User.email LIKE' => '%'.$keyword.'%',
                    ),
                ),
                'fields' => array(
                    'User.id'
                ),
            ), array(
                'company' => true,
                'admin' => true,
                'role' => 'agent',
            ));

            if( !empty($users) ) {
                $default_options['conditions']['OR']['PropertyLead.user_id'] = $users;
            }

            $default_options['order'] = array(
                'Property.find_keyword' => 'DESC',
            );
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
        }
        if( !empty($type) ) {
            $type = urldecode($type);
            $type = explode(',', $type);
            $default_options['conditions']['Property.property_type_id'] = $type;
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

        if( !empty($default_options['contain']) ) {
            $default_options['contain'] = array_unique($default_options['contain']);
        }

        if( !empty($dateFrom) ) {
            $field = 'created';
            $default_options['conditions']['DATE_FORMAT('.$modelName.'.'.$field.', \'%Y-%m-%d\') >='] = $dateFrom;
            if( !empty($dateTo) ) {
                $default_options['conditions']['DATE_FORMAT('.$modelName.'.'.$field.', \'%Y-%m-%d\') <='] = $dateTo;
            }
        }

        return $default_options;
    }
}
?>