<?php
class PropertyRevision extends AppModel {
	var $name = 'PropertyRevision';
	var $displayField = 'property_id';
	var $validate = array(
		'property_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				'message' => 'Invalid property'
			),
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Please select property'
			),
		),
		'status' => array(
			'boolean' => array(
				'rule' => array('boolean'),
				'message' => 'Invalid status'
			),
		),
	);

	var $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		)
	);

	function getRevision($property_id, $status = 'active', $step = false){
		$stat = 0;

		if($status == 'active'){
			$stat = 1;
		}

		$conditions = array(
			'PropertyRevision.status' => $stat,
			'PropertyRevision.property_id' => $property_id
		);

		if(!empty($step)){
			$conditions['OR'] = array(
				'PropertyRevision.step' => array( $step, '', NULL ),
			);
		}

		$result = $this->find('all', array(
			'conditions' => $conditions,
			'fields' => array(
				'PropertyRevision.model', 'PropertyRevision.field', 'PropertyRevision.value',
				'PropertyRevision.step', 'PropertyRevision.created'
			),
			'order' => array(
				'PropertyRevision.step' => 'ASC',
				'PropertyRevision.id' => 'ASC',
			),
		));
		
		return $result;
	}

	function doSave($data, $property = false, $property_id, $step = false){
		$value = $this->getRevision($property_id);
		$user_id = !empty($property['Property']['user_id'])?$property['Property']['user_id']:false;
		$mls_id = !empty($property['Property']['mls_id'])?$property['Property']['mls_id']:false;

		$is_delete = $this->deleteRevision($property_id, $step);

		if($is_delete){
			if(!empty($data) && $this->saveMany($data)){
				if( empty($value) ) {
                    $notifMsg = sprintf(__('Terjadi perubahan terhadap Properti dengan ID %s pada tanggal %s, harap ditinjau ulang'), $mls_id, date('d M Y'));
					$this->Property->User->Notification->doSave(array(
                        'Notification' => array(
                            'user_id' => 'admin_company',
                            'name' => $notifMsg,
                            'link' => array(
                                'controller' => 'properties',
                                'action' => 'index',
                                'keyword' => $mls_id,
                                'admin' => true,
                            ),
                        ),
                    ));
				}

				return true;
			}
		}

		return false;
	}

	function deleteRevision($property_id, $step = false){
		$conditions = array(
			'PropertyRevision.status' => 1,
			'PropertyRevision.property_id' => $property_id
		);

		if(!empty($step)){
			$conditions['PropertyRevision.step'] = $step;
		}

		return $this->deleteAll($conditions, true, true);
	}

	function unactivateRevision($id){
		$this->updateAll(
			array(
				'PropertyRevision.status' => 0
			),
			array(
				'PropertyRevision.property_id' => $id,
				'PropertyRevision.status' => 1
			)
		);
		$this->Property->PropertyMedias->updateAll(
			array(
				'PropertyMedias.status' => 0
			),
			array(
				'PropertyMedias.property_id' => $id,
				'PropertyMedias.status' => 1,
				'PropertyMedias.approved' => 0,
			)
		);
		$this->Property->PropertyVideos->updateAll(
			array(
				'PropertyVideos.status' => 0
			),
			array(
				'PropertyVideos.property_id' => $id,
				'PropertyVideos.status' => 1,
				'PropertyVideos.approved' => 0,
			)
		);
	}

	function getData( $find, $options = false, $elements = array() ){
        $default_options = array(
            'conditions'=> array(),
            'order' => array(
                'PropertyRevision.id' => 'ASC', 
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($options) ){
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
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }
}
?>