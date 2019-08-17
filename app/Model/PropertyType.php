<?php
class PropertyType extends AppModel {
	var $name = 'PropertyType';
	var $displayField = 'name';

	function getData( $find = 'all', $options = array(), $elements = array() ) {
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');
		$default_options = array(
			'conditions' => array(
				'PropertyType.status' => 1,
				'PropertyType.name <>' => ''
			),
			'order' => array(),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
            'limit' => array(),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['PropertyType.modified >'] = $lastupdated;
        }

        return $this->merge_options($default_options, $options, $find);
	}

	function getMerge ( $data, $id = false, $fieldName = 'PropertyType.id', $elements = array() ) {
		if( empty($data['PropertyType']) && !empty($id) ) {
            $cache = $this->filterEmptyField($elements, 'cache');
            $options = $this->buildCache($cache, array(
				'conditions' => array(
					$fieldName => $id,
				),
			));

			$value = $this->getData('first', $options);

			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}
}
?>