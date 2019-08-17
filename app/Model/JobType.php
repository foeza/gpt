<?php
class JobType extends AppModel {
	var $name = 'JobType';

	var $belongsTo = array(
		'KprApplication' => array(
			'className' => 'KprApplication',
			'foreignKey' => 'job_type_id',
			'dependent' => false,
		),
	);

	var $validate = array(
		'name' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Jenis pekerjaan harap dipilih',
			),
		),
	);

	function getData( $find = 'all', $options = array() ,$element = array() ){
		$status = !empty($element['status'])?$element['status']:'active';
		$statusConditions = array();

		$default_options = array(
			'conditions'=> array(
				'JobType.status'=> 1, 
			),
			'order'=> array(),
			'fields' => array(),
			'contain' => array(),
			'group' => array(),
		);


		switch($status){
			case 'active' : 
			$statusConditions = array(
				'JobType.status' => TRUE,
			);

			$status_conditions['conditions'] = $statusConditions;
		}

		if(!empty($status_conditions)){
			$default_options['conditions'] = array_merge($default_options['conditions'], $status_conditions['conditions']);
		}

        return $this->merge_options($default_options, $options, $find);
	}

	function getMerge ( $data, $type_job_id ) {
		if( empty($data['JobType']) && !empty($type_job_id) ) {
			$type_job = $this->getData('first', array(
				'conditions' => array(
					'JobType.id' => $type_job_id,
				),
			));

			if( !empty($type_job) ) {
				$data = array_merge($data, $type_job);
			}
		}

		return $data;
	}

}