<?php
class LogKpr extends AppModel {
	var $name = 'LogKpr';
	var $displayField = 'name';

	var $hasMany = array(
		'HelpApplyKpr' => array(
			'className' => 'HelpApplyKpr',
			'foreignKey' => 'log_kpr_id',
		),
		'SharingKpr' => array(
			'className' => 'SharingKpr',
			'foreignKey' => 'log_kpr_id',
		),
	);

	var $belongsTo = array(
		'Bank' => array(
		  'className' => 'Bank',
		  'foreignKey' => 'bank_id',
		),
	);

	function doSave( $data ) {

		$this->create();
		$this->set($data);

		if( $this->save( $data ) ) {
			return $this->id;
		} else {
			return false;
		}
	}

	function getFirstData($id){
		return $this->find('first', array(
			'conditions' => array(
				'LogKpr.id' => $id
			)
		));
	}

	function data_sync(){
    	$result = $this->find('list', array(
   			'conditions' => array(
   				'LogKpr.sync' => 0
   			),
   			'fields' => array(
   				'LogKpr.id'
   			), 
   			'limit' => 200
   		));

   		if(!empty($result)){
   			$this->updateAll(
			    array('LogKpr.sync' => 1),
			    array(
			    	'LogKpr.sync' => 0,
			    	'LogKpr.id' => $result
			    )
			);

			$result = $this->find('all', array(
	   			'conditions' => array(
	   				'LogKpr.sync' => 1,
	   				'LogKpr.id' => $result,
	   			),
	   		));

	   		if(!empty($result)){
                $this->Property = ClassRegistry::init('Property');
                $this->PropertyAddress = ClassRegistry::init('PropertyAddress');
                $this->PropertyAsset = ClassRegistry::init('PropertyAsset');
                $this->PropertyMedias = ClassRegistry::init('PropertyMedias');
                $this->User = ClassRegistry::init('User');

                foreach ($result as $key => $value) {
                	if(!empty($value['LogKpr']['user_id'])){
                		$result[$key] = $this->User->getMergeUser($value, $value['LogKpr']['user_id']);
                	}

                    if(!empty($value['LogKpr']['property_id'])){
                		$property = $this->Property->getProperties('first', array(
                            'conditions' => array(
                                'Property.id' => $value['LogKpr']['property_id']
                            )
                        ));
                		
                        if(!empty($property)){
                        	$data = array();
                        	$user = $this->User->getMergeUser($data, $property['Property']['user_id']);
                        	
                        	if(!empty($user)){
                        		$property['Property']['User'] = $user['User'];
                        	}
                        	
                            $property = $this->PropertyAddress->getMergeAddress($property);
                            $property = $this->PropertyAsset->getMergeAsset($property);
                            $property = $this->PropertyMedias->getMergePropertyMedias($property);
                        }

                        $result[$key] = array_merge($result[$key], $property);
                    }
                }
            }
   		}

   		return $result;
    }
}
?>