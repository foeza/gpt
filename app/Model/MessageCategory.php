<?php
class MessageCategory extends AppModel {
	var $name = 'MessageCategory';
	var $displayField = 'name';

	function getData( $find = 'all', $options = array() ) {
		$default_options = array(
			'conditions' => array(
				'MessageCategory.status' => 1,
			),
			'order' => array(
				'MessageCategory.id' => 'ASC'
			)
		);

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

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

	function getMerge ( $data, $id = false, $fieldName = 'MessageCategory.id' ) {
		if( empty($data['MessageCategory']) && !empty($id) ) {
			$value = $this->getData('first', array(
				'conditions' => array(
					$fieldName => $id,
				),
			));

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}
}
?>