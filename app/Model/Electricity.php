<?php
class Electricity extends AppModel {
	var $name = 'Electricity';
	var $displayField = 'name';

	function getData( $find = 'all', $options = array(), $elements = array() ) {
        $status = $this->filterEmptyField($elements, 'status', false, 'active');
		$default_options = array(
			'conditions' => array(
				'Electricity.status' => 1,
			),
			'order' => array(
				'Electricity.name' => 'ASC'
			)
		);

		switch ($status) {
			case 'active':
            	$default_options['conditions']['Electricity.status'] = 1;
				break;
		}

		if(!empty($options['conditions'])){
			$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
		}
		if(!empty($options['contain'])){
			$default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
		}
		if(!empty($options['limit'])){
			$default_options['limit'] = $options['limit'];
		}
		if(!empty($options['fields'])){
			$default_options['fields'] = $options['fields'];
		}
		if(!empty($options['order']) && !empty($default_options['order'])){
			$default_options['order'] = array_merge($default_options['order'], $options['order']);
		}

		// $default_options = $this->_callFieldForAPI($find, $default_options);

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function _callFieldForAPI ( $find, $options ) {
		if( !in_array($find, array( 'list', 'count' )) ) {
			$rest_api = Configure::read('Rest.token');

			if( !empty($rest_api) ) {
				$options['fields'] = array(
					'Electricity.id',
					'Electricity.name',
				);
			}
		}

		return $options;
	}
}
?>