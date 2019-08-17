<?php
class GoogleFont extends AppModel {
	public $validate = array(
		'name' => array(
			'notBlank' => array(
				'rule'		=> array('notBlank'),
				'message'	=> 'Mohon masukan Nama', 
			),
		),
	);

	public function getData($find = 'all', $options = array(), $elements = array()){
		$modelName		= $this->alias;
		$defaultOptions	= array(
			'conditions'	=> array(), 
			'contain'		=> array(), 
			'order'			=> array(
				sprintf('%s.name', $modelName) => 'ASC',
			),
		);

		$status	= Common::hashEmptyField($elements, 'status', null, 'active');

		if(in_array($status, array('active', 'inactive'))){
			$value = $status == 'active' ? 1 : 0;

			$defaultOptions['conditions'] = array_merge($defaultOptions['conditions'], array(
				sprintf('%s.status', $modelName) => $value,  
			));
		}

		$results = $this->merge_options($defaultOptions, $options, $find);
		return $results;
	}
}
?>