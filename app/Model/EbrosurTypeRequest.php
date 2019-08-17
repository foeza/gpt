<?php
class EbrosurTypeRequest extends AppModel {
	var $name = 'EbrosurTypeRequest';

	var $belongsTo = array(
		'PropertyType' => array(
			'className' => 'PropertyType',
			'foreignKey' => 'property_type_id'
		)
	);

	var $validate = array(
        'property_type_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon Pilih tipe properti yang Anda inginkan',
            ),
        )
    );

	function doSave($data, $ebrosur_request_id = false){
		$is_delete = $this->deleteAgentRequest($ebrosur_request_id);

		if($is_delete){
			if(!empty($data['EbrosurTypeRequest']['property_type_id'])){
				$temp = array();
				foreach ($data['EbrosurTypeRequest']['property_type_id'] as $id => $value) {
					if(!empty($value)){
						$temp[] = array(
							'EbrosurTypeRequest' => array(
								'ebrosur_request_id' => $ebrosur_request_id,
								'property_type_id' => $id
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
			'EbrosurTypeRequest.ebrosur_request_id' => $ebrosur_request_id
		);

		return $this->deleteAll($conditions, true, true);
	}

	function getMerge($data, $empty = false){
		if(empty($data['EbrosurTypeRequest']) && !empty($data['EbrosurRequest']['id'])){
			$value = $this->find('all', array(
                'conditions' => array(
                    'EbrosurTypeRequest.ebrosur_request_id' => $data['EbrosurRequest']['id'],
                ),
                'contain' => array(
                	'PropertyType'
                )
            ));

            if( !empty($value) ) {
                $data['EbrosurTypeRequest'] = $value;
            }
		}

		if( !empty($empty) && empty($data['EbrosurTypeRequest']) ) {
            $data['EbrosurTypeRequest'] = array();
		}

		return $data;
	}
}
?>