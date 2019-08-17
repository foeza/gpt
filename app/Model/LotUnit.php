<?php
class LotUnit extends AppModel {
	var $name = 'LotUnit';
	var $displayField = 'name';
	
	var $belongsTo = array(
		'PropertyAction' => array(
			'className' => 'PropertyAction',
			'foreignKey' => 'property_action_id',
		),
	);

	function getData( $find = 'all', $options = array(), $element = array() ) {
		$property_action_id = isset($element['property_action_id'])?$element['property_action_id']:false;
		$is_space = isset($element['is_space'])?$element['is_space']:false;
		$is_lot = isset($element['is_lot'])?$element['is_lot']:false;
        $lastupdated = $this->filterEmptyField($element, 'lastupdated');

		$default_options = array(
			'conditions' => array(),
			'order' => array(),
			'group' => array(),
			'contain' => array(),
            'fields'=> array(),
		);

        if( !empty($lastupdated) ) {
            $default_options['conditions']['LotUnit.modified >'] = $lastupdated;
        }

		if( !empty($property_action_id) || !empty($is_space) ) {
			$default_options['conditions']['OR'][0] = array(
				'LotUnit.property_action_id' => 0,
				'LotUnit.is_space' => 1,
			);

			if( !empty($property_action_id) ) {
				$default_options['conditions']['OR'][1]['LotUnit.property_action_id'] = $property_action_id;
			}
			if( !empty($is_space) ) {
				$default_options['conditions']['OR'][1]['LotUnit.is_space'] = $is_space;
			}
		}
		if( !empty($is_lot) ) {
			$default_options['conditions']['LotUnit.is_lot'] = true;
		}

        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'LotUnit.id',
                  'LotUnit.property_action_id',
                  'LotUnit.slug',
                  'LotUnit.is_lot',
                  'LotUnit.is_space',
                  'LotUnit.name',
                  'LotUnit.measure'
                );
            }
        }

        return $options;
    }

	function getMerge ( $data, $id = false, $modelName = 'LotUnit', $parentModelName = false, $elements = array() ) {
		if( empty($data['LotUnit']) && !empty($id) ) {
            $cache = $this->filterEmptyField($elements, 'cache');
            $options = $this->buildCache($cache, array(
				'conditions' => array(
					'LotUnit.id' => $id,
				),
			));

			$value = $this->getData('first', $options);

			if( !empty($value) ) {
				if( !empty($parentModelName) ) {
					$data[$parentModelName][$modelName] = $value['LotUnit'];
				} else {
					$data[$modelName] = $value['LotUnit'];
				}
			}
		}

		return $data;
	}
}
?>