<?php
class PropertyFilter extends AppModel {
//	untuk clear cache
	var $name		= 'PropertyFilter';
	var $hasMany	= array(
		'PropertyFilterDetail' => array(
			'className'		=> 'PropertyFilterDetail',
			'foreignKey'	=> 'property_filter_id',
		),
	);

	public function afterSave($created, $options = array()){
		$dataCompany	= Configure::read('Config.Company.data');
		$companyID		= Hash::get($dataCompany, 'UserCompany.id', false);
		$cacheGroups	= array(
			'Properties.Filter' => 'properties__filter_', 
		);

		foreach($cacheGroups as $cacheGroup => $cacheNameInfix){
			$cachePath	= CACHE.$cacheGroup;
			$wildCard	= sprintf('*.%s.%s.*', $cacheNameInfix, $companyID);
			$cleared	= clearCache($wildCard, $cacheGroup, null);
		}

		if(isset($this->id) && $this->id){
			$cacheConfig	= 'properties_filter';
			$cacheName		= sprintf('Properties.Filter.%s.%s', $companyID, $this->id);

			Cache::delete($cacheName, $cacheConfig);
		}
	}

	public function getData($find = 'all', $options = array(), $elements = array()){
		$modelName	= $this->alias;
		$find		= (string) $find;
		$options	= (array) $options;
		$elements	= (array) $elements;

		$status		= Hash::get($elements, 'status', 'active');
		$mine		= Hash::get($elements, 'mine', false);
		$useDefault	= Hash::get($elements, 'use_default', false);

		$authUserID		= Configure::read('User.id');
		$dataCompany	= Configure::read('Config.Company.data');
		$companyID		= Hash::get($dataCompany, 'UserCompany.id', false);

		if($companyID && $useDefault){
			$filterCount = $this->find('count', array(
				'conditions' => array(
					$modelName.'.user_company_id' => $companyID, 
				), 
			));

		//	paksa company id jadi 0, jadi data yang di tarik adalah data default
			$companyID = $filterCount > 0 ? $companyID : 0;
		}

	//	build conditions
		$defaultOptions	= array(
			'conditions'	=> array(),
			'contain'		=> array(),
			'fields'		=> array(),
			'group'			=> array(),
			'order'			=> array($modelName.'.id' => 'ASC'),
		);

		if(in_array($status, array('active', 'inactive'))){
			$fieldValue = $status == 'active' ? 1 : 0;

			$defaultOptions['conditions']['COALESCE('.$modelName.'.status, 0)'] = $fieldValue;
		}

		if($mine || $useDefault){
			$defaultOptions['conditions']['COALESCE('.$modelName.'.user_company_id, 0)'] = $companyID;
		}

		if($options){
			if(!empty($options['conditions'])){
				$defaultOptions['conditions'] = array_merge($defaultOptions['conditions'], $options['conditions']);
			}

			if(!empty($options['order'])){
				$defaultOptions['order'] = $options['order'];
			}

			if(isset($options['contain']) && empty($options['contain'])){
				$defaultOptions['contain'] = false;
			}
			else if(!empty($options['contain'])){
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