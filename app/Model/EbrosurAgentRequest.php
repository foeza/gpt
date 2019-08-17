<?php
class EbrosurAgentRequest extends AppModel {
	var $name = 'EbrosurAgentRequest';

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'agent_id'
		)
	);

	var $validate = array(
        'agent_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon Pilih agen yang Anda inginkan',
            ),
        )
    );

    function getData($find, $options = array()){
		$default_options = array(
			'conditions' => array(),
			'contain' => array(),
			'order' => array(
				'UserCompanyEbrochure.id' => 'DESC'
			),
			'fields' => array(),
			'limit' => array(),
			'group' => array(),
		);

		if(!empty($options['conditions'])){
			$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
		}
		if(!empty($options['order'])){
			$default_options['order'] = array_merge($options['order'], $default_options['order'] );
		}
		if(!empty($options['fields'])){
			$default_options['fields'] = array_merge($options['fields'], $default_options['fields'] );
		}
        if( isset($options['contain']) && empty($options['contain']) ) {
            $default_options['contain'] = false;
        } else if(!empty($options['contain'])){
            $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
        }
        if(!empty($options['limit'])){
            $default_options['limit'] = $options['limit'];
        }
        if(!empty($options['group'])){
            $default_options['group'] = $options['group'];
        }

		if($find == 'paginate'){
			$result = $default_options;
		}else{
			$result = $this->find($find, $default_options);
		}

		return $result;
	}

	function doSave($data, $ebrosur_request_id = false){
		$is_delete = $this->deleteAgentRequest($ebrosur_request_id);

		if($is_delete){
			if(!empty($data['EbrosurAgentRequest']['agent_id'])){
				$temp = array();
				foreach ($data['EbrosurAgentRequest']['agent_id'] as $id => $value) {
					if(!empty($value)){
						$temp[] = array(
							'EbrosurAgentRequest' => array(
								'ebrosur_request_id' => $ebrosur_request_id,
								'agent_id' => $id
							)
						); 
					}
				}

				if(!empty($temp) && $this->saveMany($temp)){
					return true;
				}
			}
		}

		return false;
	}

	function deleteAgentRequest($ebrosur_request_id){
		$conditions = array(
			'EbrosurAgentRequest.ebrosur_request_id' => $ebrosur_request_id
		);

		return $this->deleteAll($conditions, true, true);
	}

	function validate($data){
		$result = false;
		
		if(!empty($data['EbrosurAgentRequest'])){
			if(empty($data['EbrosurAgentRequest']['agent_all']) && !empty($data['EbrosurAgentRequest']['agent_id'])){
				foreach ($data['EbrosurAgentRequest']['agent_id'] as $key => $value) {
					if(!empty($value)){
						$result = true;

						break;
					}
				}
			}else if(!empty($data['EbrosurAgentRequest']['agent_all'])){
				$result = true;
			}
		}

		return $result;
	}

	function getMerge($data, $limit = false){
		if(empty($data['EbrosurAgentRequest']) && !empty($data['EbrosurRequest']['id'])){
			$default_options = $options = array(
                'conditions' => array(
                    'EbrosurAgentRequest.ebrosur_request_id' => $data['EbrosurRequest']['id'],
                ),
            );

			if(!empty($limit)){
				$options['limit'] = $limit;
			}

			$value = $this->find('all', $options);

            if( !empty($value) ) {
                $data['EbrosurAgentRequest'] = $value;

                $data['count_user'] = $this->find('count', $default_options);
                
                foreach ($data['EbrosurAgentRequest'] as $key => $value) {
                	$agent_id = !empty($value['EbrosurAgentRequest']['agent_id']) ? $value['EbrosurAgentRequest']['agent_id'] : false;

                	$data['EbrosurAgentRequest'][$key] = $this->User->getMerge($value, $agent_id);
                }
            }
		}
		return $data;
	}
}
?>