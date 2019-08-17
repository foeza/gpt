<?php
class EbrosurClientRequest extends AppModel {
	var $name = 'EbrosurClientRequest';

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'client_id'
		)
	);

	var $validate = array(
        'client_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon Pilih klien yang Anda inginkan',
            ),
        )
    );

	function doSave($data, $ebrosur_request_id = false){
		$is_delete = $this->deleteClientRequest($ebrosur_request_id);

		if($is_delete){
			if(!empty($data['EbrosurClientRequest']['client_id'])){
				$temp = array();
				foreach ($data['EbrosurClientRequest']['client_id'] as $id => $value) {
					if(!empty($value)){
						$temp[] = array(
							'EbrosurClientRequest' => array(
								'ebrosur_request_id' => $ebrosur_request_id,
								'client_id' => $id
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

	function deleteClientRequest($ebrosur_request_id){
		$conditions = array(
			'EbrosurClientRequest.ebrosur_request_id' => $ebrosur_request_id
		);

		return $this->deleteAll($conditions, true, true);
	}

	function validate($data){
		$result = false;
		
		if(!empty($data['EbrosurClientRequest'])){
			if(empty($data['EbrosurClientRequest']['client_all']) && !empty($data['EbrosurClientRequest']['client_id'])){
				foreach ($data['EbrosurClientRequest']['client_id'] as $key => $value) {
					if(!empty($value)){
						$result = true;

						break;
					}
				}
			}else if(!empty($data['EbrosurClientRequest']['client_all'])){
				$result = true;
			}
		}

		return $result;
	}

	function getMerge($data, $limit = false, $empty = false){
		if(empty($data['EbrosurClientRequest']) && !empty($data['EbrosurRequest']['id'])){
			$default_options = $options = array(
                'conditions' => array(
                    'EbrosurClientRequest.ebrosur_request_id' => $data['EbrosurRequest']['id'],
                ),
            );

			if(!empty($limit)){
				$options['limit'] = $limit;
			}

			$value = $this->find('all', $options);

            if( !empty($value) ) {
                $data['EbrosurClientRequest'] = $value;

                $data['count_user'] = $this->find('count', $default_options);
                
                foreach ($data['EbrosurClientRequest'] as $key => $value) {
                	$client_id = !empty($value['EbrosurClientRequest']['client_id']) ? $value['EbrosurClientRequest']['client_id'] : false;

                	$data['EbrosurClientRequest'][$key] = $this->User->getMerge($value, $client_id);
                }
            }
		}

		if( !empty($empty) && empty($data['EbrosurClientRequest']) ) {
            $data['EbrosurClientRequest'] = array();
		}

		return $data;
	}
}
?>