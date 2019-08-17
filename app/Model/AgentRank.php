<?php
class AgentRank extends AppModel {
	public $belongsTo = array(
		'User' => array(
			'foreignKey' => 'user_id', 
		), 
		'Region' => array(
			'foreignKey' => 'region_id', 
		), 
		'City' => array(
			'foreignKey' => 'city_id', 
		), 
		'Subarea' => array(
			'foreignKey' => 'subarea_id', 
		), 
	);

	public function _callRefineParams($data = null, $defaultOptions = array()){
		$keyword	= Common::hashEmptyField($data, 'named.keyword', false, array('addslashes' => true));
		$status		= Common::hashEmptyField($data, 'named.status', false, array('addslashes' => true));
		$sort		= Common::hashEmptyField($data, 'named.sort');

		$defaultOptions = $this->defaultOptionParams($data, $defaultOptions, array(
			'region' => array(
				'field'		=> 'Region.name',
				'type'		=> 'like',
				'contain'	=> 'Region', 
			),
			'city' => array(
				'field'		=> 'City.name',
				'type'		=> 'like',
				'contain'	=> 'City', 
			),
			'subarea' => array(
				'field'		=> 'Subarea.name',
				'type'		=> 'like',
				'contain'	=> 'Subarea', 
			),
		));

		if($sort){
			$sort	= explode('.', $sort);
			$model	= Common::hashEmptyField($sort, 0);
			$field	= Common::hashEmptyField($sort, 1);

			if($model && $model != $this->alias){
				$defaultOptions['contain'][] = $model;
			}
		}

		return $defaultOptions;
	}

	public function getData($find = 'all', $options = array(), $elements = array()){
		$modelName		= $this->alias;
		$defaultOptions	= array(
			'conditions'	=> array(),
			'contain'		=> array(),
			'fields'		=> array(),
			'group'			=> array(),
			'order'			=> array(),
		);

		return $this->merge_options($defaultOptions, $options, $find);
	}
}
?>