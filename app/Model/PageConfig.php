<?php
class PageConfig extends AppModel {
	var $name = 'PageConfig';

	public function getData( $find = 'all', $options = array(), $elements = array()  ) {
		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
            'order' => array(
				'PageConfig.created'=>'ASC',
				'PageConfig.id'=>'ASC',
			),
		);

		if( !empty($options) ) {
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
        }

		if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
	}

	public function getDataModel( $data, $page_type = false ) {
		$result = array();

		if ( !empty($data) ) {
			foreach ($data as $fieldName => $value) {
				$value = trim($value);
				
				if( !empty($value) ) {
					$result[] = array(
						'PageConfig' => array(
							'page_type' => $page_type,
							'field_name' => $fieldName,
							'value' => trim($value),
						),
					);
				}
			}
		}

		return $result;
	}

	public function doSave( $data, $page_id = false, $page_type = false ) {
		$result = true;

		if ( !empty($data) && !empty($page_id) ) {
			foreach ($data as $fieldName => $value) {
				$exist = $this->getData('first', array(
					'conditions' => array(
						'PageConfig.page_id' => $page_id,
						'PageConfig.page_type' => $page_type,
						'PageConfig.field_name' => $fieldName,
					),
				));

				if( !empty($exist['PageConfig']['id']) ) {
					$this->id = $exist['PageConfig']['id'];
				} else {
					$this->create();
				}

				$this->set('page_id', $page_id);
				$this->set('page_type', $page_type);
				$this->set('field_name', $fieldName);
				$this->set('value', trim($value));

				if( !$this->save() ) {
					$result = false;
				}
			}
		}

		return $result;
	}

	public function doSaveMany( $data, $page_id = false ) {
		$result = true;

		if ( !empty($data) && !empty($page_id) ) {
			foreach ($data as $key => $value) {
				$value['PageConfig']['page_id'] = $page_id;

				$data[$key] = $value;
			}

			$this->deleteAll(array(
				'PageConfig.page_id' => $page_id,
				'PageConfig.page_type' => 'property',
			));

			if( !$this->saveMany($data) ) {
				$result = false;
			}
		}

		return $result;
	}

	public function getMerge( $data, $page_id = false, $page_type = 'property' ) {
		if ( !empty($page_id) ) {
			$values = $this->getData('list', array(
				'conditions' => array(
					'PageConfig.page_id' => $page_id,
					'PageConfig.page_type' => $page_type,
				),
				'fields' => array(
					'PageConfig.field_name', 'PageConfig.value',
				),
			));

			if( !empty($values) ) {
				foreach ($values as $field_name => $value) {
					$data['PageConfig'][$field_name] = $value;
				}
			}
		}

		return $data;
	}
}
?>