<?php
class ViewUnionPropertySubarea extends AppModel {
//	untuk clear cache
	var $name	= 'ViewUnionPropertySubarea';
//	var $actsAs	= array('Containable');

	var $belongsTo = array(
		'PropertyAction' => array(
			'foreignKey'	=> 'property_action_id', 
			'type'			=> 'inner', 
		),
		'PropertyType' => array(
			'foreignKey'	=> 'property_type_id', 
			'type'			=> 'inner', 
		),
		'Region' => array(
			'foreignKey'	=> 'region_id', 
			'type'			=> 'inner', 
		),
		'City' => array(
			'foreignKey' => 'city_id', 
			'type'			=> 'inner', 
		),
		'Subarea' => array(
			'foreignKey' => 'subarea_id', 
			'type'			=> 'inner', 
		), 
		'User' => array(
			'foreignKey'	=> 'user_id', 
			'type'			=> 'inner', 
		),
		'Property' => array(
			'foreignKey'	=> false, 
			'type'			=> 'inner', 
			'conditions'	=> array(
				'Property.id = ViewUnionPropertySubarea.id', 
				'Property.mls_id = ViewUnionPropertySubarea.mls_id', 
			), 
		),
	);

	var $hasOne = array(
		'PropertyAsset'		=> array('foreignKey' => 'property_id'),
		'PropertyAddress'	=> array(
			'foreignKey'	=> 'property_id',
			'order'			=> array(
				'PropertyAddress.id' => 'ASC',
			),
			'limit' => 1,
		),
	);

	public function _callRefineParams($data = array(), $defaultOptions = array(), $modelName = 'ViewUnionPropertySubarea'){
		$data			= (array) $data;
		$defaultOptions	= (array) $defaultOptions;
		$modelName		= (string) $modelName;
		$filterOpts		= array(
			'addslashes' => true,
		);

	//	define filter
		$keyword			= $this->filterEmptyField($data, 'named', 'keyword', false, $filterOpts);
		$dateFrom			= $this->filterEmptyField($data, 'named', 'date_from', false, $filterOpts);
		$dateTo				= $this->filterEmptyField($data, 'named', 'date_to', false, $filterOpts);
		$region				= $this->filterEmptyField($data, 'named', 'region', false, $filterOpts);
		$city				= $this->filterEmptyField($data, 'named', 'city', false, $filterOpts);
		$subarea			= $this->filterEmptyField($data, 'named', 'subarea', false, $filterOpts);
		$subareas			= $this->filterEmptyField($data, 'named', 'subareas', array(), $filterOpts);
		$type				= $this->filterEmptyField($data, 'named', 'type', false, $filterOpts);
		$typeID				= $this->filterEmptyField($data, 'named', 'typeid', false, $filterOpts);
		$beds				= $this->filterEmptyField($data, 'named', 'beds', false, $filterOpts);
		$baths				= $this->filterEmptyField($data, 'named', 'baths', false, $filterOpts);
		$propertyStatusID	= $this->filterEmptyField($data, 'named', 'property_status_id', false, $filterOpts);
		$lotWidth			= $this->filterEmptyField($data, 'named', 'lot_width', false, $filterOpts);
		$lotLength			= $this->filterEmptyField($data, 'named', 'lot_length', false, $filterOpts);
		$lotSize			= $this->filterEmptyField($data, 'named', 'lot_size', false);
		$buildingSize		= $this->filterEmptyField($data, 'named', 'building_size', false, $filterOpts);
		$certificate		= $this->filterEmptyField($data, 'named', 'certificate', false, $filterOpts);
		$condition			= $this->filterEmptyField($data, 'named', 'condition', false, $filterOpts);
		$furnished			= $this->filterEmptyField($data, 'named', 'furnished', false, $filterOpts);
		$propertyAction		= $this->filterEmptyField($data, 'named', 'property_action', false, $filterOpts);
		$propertyDirection	= $this->filterEmptyField($data, 'named', 'property_direction', false, $filterOpts);
		$user				= $this->filterEmptyField($data, 'named', 'user', false, $filterOpts);
		$mlsID				= $this->filterEmptyField($data, 'named', 'mlsid', false, $filterOpts);
		$price				= $this->filterEmptyField($data, 'named', 'price', false, $filterOpts);
		$sold				= $this->filterEmptyField($data, 'named', 'sold', false, $filterOpts);
		$name				= $this->filterEmptyField($data, 'named', 'name', false, $filterOpts);
		$status				= $this->filterEmptyField($data, 'named', 'status', false, $filterOpts);
		$principleID		= $this->filterEmptyField($data, 'named', 'principle_id', false, $filterOpts);
		$period				= $this->filterEmptyField($data, 'named', 'period', 6, $filterOpts);

		$sort				= $this->filterEmptyField($data, 'named', 'sort', false, $filterOpts);
		$direction			= $this->filterEmptyField($data, 'named', 'direction', 'ASC', $filterOpts);

		if($keyword){
			$defaultOptions['conditions']['OR'][sprintf('%s.keyword LIKE', $this->alias)]		= '%'.$keyword.'%';
			$defaultOptions['conditions']['OR'][sprintf('%s.mls_id LIKE', $this->alias)]		= '%'.$keyword.'%';
			$defaultOptions['conditions']['OR'][sprintf('%s.title LIKE', $this->alias)]			= '%'.$keyword.'%';
			$defaultOptions['conditions']['OR'][sprintf('%s.description LIKE', $this->alias)]	= '%'.$keyword.'%';

			$users = $this->User->getData('list', array(
				'fields'		=> array('User.id', 'User.id'),
				'conditions'	=> array(
					'OR' => array(
						'User.first_name LIKE'	=> '%'.$keyword.'%',
						'User.last_name'		=> '%'.$keyword.'%',
						'User.email LIKE'		=> '%'.$keyword.'%',
					),
				),
			), array(
				'company'	=> true,
				'admin'		=> true,
				'role'		=> 'agent',
			));

			$users = array_unique(array_filter($users));

			if($users){
				$defaultOptions['conditions']['OR'][sprintf('%s.user_id', $this->alias)] = $users;
			}
		}

		if($mlsID){
			$defaultOptions['conditions'][sprintf('%s.mls_id', $this->alias)] = $mlsID;
		}

		if($region){
			if(is_numeric($region)){
				$defaultOptions['conditions'][sprintf('%s.region_id', $this->alias)] = $region;
			}
			else{
				$defaultOptions['contain'][] = 'Region';
				$defaultOptions['conditions']['Region.name'] = $region;
			}
		}

		if($city){
			if(is_numeric($city)){
				$defaultOptions['conditions'][sprintf('%s.city_id', $this->alias)] = $city;
			}
			else{
				$defaultOptions['contain'][] = 'City';
				$defaultOptions['conditions']['City.name'] = $city;
			}
		}

		if($subarea){
			if(is_numeric($subarea)){
				$defaultOptions['conditions'][sprintf('%s.subarea_id', $this->alias)] = $subarea;
			}
			else{
				$defaultOptions['contain'][] = 'Subarea';
				$defaultOptions['conditions']['Subarea.name'] = $subarea;
			}
		}

		if($subareas){
			$subareas = explode(',', urldecode($subareas));

			$defaultOptions['contain'][] = 'Subarea';
			$defaultOptions['conditions'][sprintf('%s.subarea_id', $this->alias)] = $subareas;
		}

		if($propertyAction){
			$defaultOptions['conditions'][sprintf('%s.property_action_id', $this->alias)] = $propertyAction;
		}

		if($type){
			$type = explode(',', urldecode($type));

			$defaultOptions['conditions'][sprintf('%s.property_type_id', $this->alias)] = $type;
		}

		if($typeID){
			$defaultOptions['conditions'][sprintf('%s.property_type_id', $this->alias)] = $typeID;
		}

		if($propertyStatusID){
			$defaultOptions['conditions'][sprintf('%s.property_type_id', $this->alias)] = $propertyStatusID;
		}

		if($lotSize){
			$lotSize = urldecode($lotSize);

			if(strstr($lotSize, '-')){
				$lotSize = explode('-', $lotSize);

				if(count($lotSize) == 2){
					$minVal	= Hash::get($lotSize, 0);
					$maxVal	= Hash::get($lotSize, 1);

					$defaultOptions['conditions'][sprintf('%s.lot_size >=', $this->alias)] = $minVal;
					$defaultOptions['conditions'][sprintf('%s.lot_size <=', $this->alias)] = $maxVal;
				}
			}
			else if(strstr($lotSize, '<')){
				$defaultOptions['conditions'][sprintf('%s.lot_size <', $this->alias)] = str_replace('<', '', $lotSize);
			}
			else if(strstr($lot_size, '>')){
				$defaultOptions['conditions'][sprintf('%s.lot_size >', $this->alias)] = str_replace('>', '', $lot_size);
			}

			$defaultOptions['contain'][] = 'PropertyType';

			$defaultOptions['conditions']['AND']['OR'][]['PropertyType.is_lot']		= 1;
			$defaultOptions['conditions']['AND']['OR'][]['PropertyType.is_space']	= 1;
		}

		if($buildingSize){
			$buildingSize = urldecode($buildingSize);

			if(strstr($buildingSize, '-')){
				$buildingSize = explode('-', $buildingSize);

				if(count($buildingSize) == 2){
					$minVal	= Hash::get($buildingSize, 0);
					$maxVal	= Hash::get($buildingSize, 1);

					$defaultOptions['conditions'][sprintf('%s.building_size >=', $this->alias)] = $minVal;
					$defaultOptions['conditions'][sprintf('%s.building_size <=', $this->alias)] = $maxVal;
				}
			}
			else if(strstr($buildingSize, '<')){
				$defaultOptions['conditions'][sprintf('%s.building_size <', $this->alias)] = str_replace('<', '', $buildingSize);
			}
			else if(strstr($buildingSize, '>')){
				$defaultOptions['conditions'][sprintf('%s.building_size >', $this->alias)] = str_replace('>', '', $buildingSize);
			}

			$defaultOptions['contain'][] = 'PropertyType';

			$defaultOptions['conditions']['AND']['OR'][]['PropertyType.is_building']	= 1;
			$defaultOptions['conditions']['AND']['OR'][]['PropertyType.is_space']		= 1;
		}

		if($lotWidth){
			$defaultOptions['contain'][] = 'PropertyAsset';
			$defaultOptions['conditions']['PropertyAsset.lot_width >='] = $lotWidth;
		}

		if($lotLength){
			$defaultOptions['contain'][] = 'PropertyAsset';
			$defaultOptions['conditions']['PropertyAsset.lot_length >='] = $lotLength;
		}

		if($price){
			$operand = substr($price, 0, 1);

			if(in_array($operand, array('>', '<'))){
				$price = trim(substr($price, 1));

				$defaultOptions['conditions'][sprintf('%s.price_measure %s', $this->alias, $operand)] = $price;
			}
			else if(strstr($price, '-')){
				$price = explode('-', $price);

				if(count($buildingSize) == 2){
					$minVal	= Hash::get($price, 0);
					$maxVal	= Hash::get($price, 1);

					$defaultOptions['conditions'][sprintf('%s.price_measure >=', $this->alias)] = $minVal;
					$defaultOptions['conditions'][sprintf('%s.price_measure <=', $this->alias)] = $maxVal;
				}
			}
			else{
				$defaultOptions['conditions'][sprintf('%s.price_measure >=', $this->alias)] = $price;
			}
		}

		if($user){
			$user	= urldecode($user);
			$users	= $this->User->getData('list', array(
				'conditions' => array(
					'User.id' => $user,
				),
				'fields' => array(
					'User.id', 'User.id',
				),
				'limit' => 200,
			), array(
				'company'	=> true,
				'admin'		=> true,
				'status'	=> 'semi-active',
			));

			$defaultOptions['conditions'][sprintf('%s.user_id', $this->alias)] = $users;
		}

		if($condition){
			$defaultOptions['contain'][] = 'PropertyAsset';
			$defaultOptions['conditions']['PropertyAsset.property_condition_id'] = $condition;
		}

		if($furnished){
			$defaultOptions['contain'][] = 'PropertyAsset';
			$defaultOptions['conditions']['PropertyAsset.furnished'] = $furnished;
		}

		if($propertyDirection){
			$defaultOptions['contain'][] = 'PropertyAsset';
			$defaultOptions['conditions']['PropertyAsset.property_direction_id'] = $propertyDirection;
		}

		if($sold){
			$defaultOptions['conditions'][sprintf('%s.sold', $this->alias)] = 1;
		}

		if($dateFrom){
			$defaultOptions['conditions'][sprintf('DATE_FORMAT(%s.created, "%%Y-%%m-%%d") >=', $this->alias)] = $dateFrom;
		}

		if($dateTo){
			$defaultOptions['conditions'][sprintf('DATE_FORMAT(%s.created, "%%Y-%%m-%%d") <=', $this->alias)] = $dateTo;
		}

		if($name){
			$fieldName = 'CONCAT(User.first_name, " ",IFNULL(User.last_name, \'\')) LIKE';

			$defaultOptions['contain'][] = 'User';
			$defaultOptions['conditions']['OR'][][$fieldName]			= '%'.$name.'%';
			$defaultOptions['conditions']['OR'][]['User.email LIKE']	= '%'.$name.'%';
		}

		if($status){
			$status		= explode(',', $status);
			$tempStatus	= array();

			foreach($status as $value){
				$tempStatus[] = $this->_callStatusCondition($value, 'restrict', $data);
			}

			if($tempStatus){
				$defaultOptions['conditions'][]['OR'] = $tempStatus;
			}
		}

		if($principleID) {
			if(is_array($principle_id) === false){
				$principleID = explode(',', $principleID);
			}

			$defaultOptions['contain'][] = 'User';
			$defaultOptions['conditions'][]['OR'] = array(
				'User.parent_id'					=> $principleID,
				sprintf('%s.user_id', $this->alias)	=> $principleID,
			);
		}

		if($period){
			$periodEnd		= date('Y-m-t');
			$periodStart	= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $periodEnd, $period)));

			$defaultOptions['conditions'][sprintf('DATE_FORMAT(%s.created, "%%Y-%%m-%%d") >=', $this->alias)] = $periodStart;
			$defaultOptions['conditions'][sprintf('DATE_FORMAT(%s.created, "%%Y-%%m-%%d") <=', $this->alias)] = $periodEnd;
		}


		if(!empty($defaultOptions['contain'])){
			$defaultOptions['contain'] = array_unique($defaultOptions['contain']);
		}

		return $defaultOptions;
	}

	public function getData($find = 'all', $options = array(), $elements = array()){
		$find		= (string) $find;
		$options	= (array) $options;
		$elements	= (array) $elements;

		$defaultOptions = array(
			'conditions'	=> array(),
			'contain'		=> array(),
			'fields'		=> array(),
			'group'			=> array(),
			'order'			=> array(
				sprintf('%s.created', $this->alias) => 'DESC',
			),
		);

		if($options){
			if(!empty($options['conditions'])){
				$defaultOptions['conditions'] = array_merge($defaultOptions['conditions'], $options['conditions']);
			}
			if(!empty($options['order'])){
				$defaultOptions['order'] = $options['order'];
			}
			if( isset($options['contain']) && empty($options['contain']) ) {
				$defaultOptions['contain'] = false;
			} else if(!empty($options['contain'])){
				$defaultOptions['contain'] = array_merge($defaultOptions['contain'], $options['contain']);
			}
			if(!empty($options['limit'])){
				$defaultOptions['limit'] = $options['limit'];
			}
			if(!empty($options['fields'])){
				$defaultOptions['fields'] = $options['fields'];
			}
			if(!empty($options['group'])){
				$defaultOptions['group'] = $options['group'];
			}
			if(isset($options['offset'])){
				$defaultOptions['offset'] = $options['offset'];
			}
		}

		if($find == 'paginate'){
			$result = $defaultOptions;
		}
		else{
			$result = $this->find($find, $defaultOptions);
		}

		return $result;
	}
}

?>