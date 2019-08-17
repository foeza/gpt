<?php
class Newsletter extends AppModel {
	var $name = 'Newsletter';

	function getData( $find = 'all', $options = array() ){
		$default_conditions = array(
			'conditions' => array(
				'Newsletter.status' => 0,
				'Newsletter.sent' => 0,
			),
		);

		if(!empty($options)){
			$default_conditions = array_merge($default_conditions, $options);
		}
		
		$data = $this->find($find, $default_conditions);

		if(!empty($data)){
			return $data;
		}else{
			return false;
		}
	}
}
?>