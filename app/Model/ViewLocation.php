<?php
class ViewLocation extends AppModel {
	public function getData($find = 'all', $options = array(), $elements = array()){
		$modelName		= $this->alias;
		$defaultOptions	= array(
			'conditions'	=> array(),
			'contain'		=> array(),
			'fields'		=> array(),
			'group'			=> array(),
			'order'			=> array(
				sprintf('%s.region_name', $modelName)	=> 'ASC',
				sprintf('%s.city_name', $modelName)		=> 'ASC',
				sprintf('%s.subarea_name', $modelName)	=> 'ASC',
			),
		);

		return $this->merge_options($defaultOptions, $options, $find);
	}
}
?>