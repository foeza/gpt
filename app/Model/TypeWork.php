<?php
class JobTypes extends AppModel {
	var $name = 'JobTypes';

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
				'JobTypes.status'=> 1, 
			),
			'order'=> array(
				'JobTypes.created' => 'ASC',
			),
			'fields' => array(),
			'contain' => array(),
			'group' => array(),
		);


		switch($status){
			case 'active' : 
			$statusConditions = array(
				'JobTypes.status' => TRUE,
			);

			$status_conditions['conditions'] = $statusConditions;
		}

		if(!empty($status_conditions)){
			$default_options['conditions'] = array_merge($default_options['conditions'], $status_conditions['conditions']);
		}

		if(!empty($options['conditions'])){
			$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
		}
        if(!empty($options['order'])){
            $default_options['order'] = $options['order'];
        }
        if( isset($options['contain']) && empty($options['contain']) ) {
            $default_options['contain'] = false;
        } else if(!empty($options['contain'])){
            $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
        }
        if(!empty($options['fields'])){
            $default_options['fields'] = $options['fields'];
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }
		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getMerge ( $data, $type_job_id ) {
		if( empty($data['JobTypes']) ) {
			$type_job = $this->getData('first', array(
				'conditions' => array(
					'JobTypes.id' => $type_job_id,
				),
			));

			if( !empty($type_job) ) {
				$data = array_merge($data, $type_job);
			}
		}

		return $data;
	}

	function getList(){

		$jobTypes = $this->getData('list', array(
			'fields' => array(
				'id', 'name'
			),
			'order' => array('name ASC')
		));
		
		return $jobTypes;
	}

}