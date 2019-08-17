<?php
class UserAgentCertificate extends AppModel {
	var $name = 'UserAgentCertificate';

    var $belongsTo = array(
        'AgentCertificate' => array(
            'className' => 'AgentCertificate',
            'foreignKey' => 'agent_certificate_id'
        ),
    );

	var $validate = array(
		'user_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'User harap dipilih'
			),
		),
		'agent_certificate_id' => array(
			'notempty' => array(
				'rule' => array('notempty'),
				'message' => 'Sertifikat harap dipilih'
			),
		),
	);

    function getDataModel ( $data ) {
        $dataSave = array();

        if( !empty($data['UserAgentCertificate']['agent_certificate_id']) ) {
            $values = array_filter($data['UserAgentCertificate']['agent_certificate_id']);

            foreach ($values as $key => $value) {
                $dataSave[]['UserAgentCertificate'] = array(
                    'agent_certificate_id' => $value,
                );
            }
        }
        
        if( !empty($data['UserAgentCertificate']['other_id']) ) {
            $text = !empty($data['UserAgentCertificate']['other_text'])?$data['UserAgentCertificate']['other_text']:false;

            $dataSave[]['UserAgentCertificate'] = array(
                'agent_certificate_id' => -1,
                'other_text' => $text,
            );
        }

        return $dataSave;
    }

    function getRequestData ( $data, $user_id ) {
        $values = $this->find('all', array(
        	'conditions' => array(
        		'UserAgentCertificate.user_id' => $user_id,
    		),
    		'order' => array(
    			'UserAgentCertificate.id' => 'ASC',
			),
    	));
    	$requestData = array();

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
            	$id = !empty($value['UserAgentCertificate']['agent_certificate_id'])?$value['UserAgentCertificate']['agent_certificate_id']:false;
            	$other_text = !empty($value['UserAgentCertificate']['other_text'])?$value['UserAgentCertificate']['other_text']:false;

            	if( $id == -1 ) {
                	$requestData['UserAgentCertificate']['other_id'] = true;
                	$requestData['UserAgentCertificate']['other_text'] = $other_text;
            	} else {
                	$requestData['UserAgentCertificate']['agent_certificate_id'][$id] = true;
                }
            }
        }

        $data = array_merge($data, $requestData);

        return $data;
    }

    function doSave( $datas, $id = false, $user_id, $is_validate = false ) {
        $result = false;
        $default_msg = __('menambahkan sertifikat');

        if( !empty($user_id) && empty($is_validate) ) {
            $this->deleteAll(array(
                'UserAgentCertificate.user_id' => $user_id,
            ));
        }

        if ( !empty($datas) ) {            
            foreach ($datas as $key => $data) {
                if( empty($id) ) {
                    $this->create();
                } else {
                    $this->id = $id;
                }

                if( !empty($user_id) ) {
                    $data['UserAgentCertificate']['user_id'] = $user_id;
                }

                $this->set($data);

                if( $this->validates() ) {
                    if( $is_validate ) {
                        $flagSave = true;
                    } else {
                        $flagSave = $this->save($data);
                    }

                    if( !$flagSave ) {
                        $result = array(
                            'msg' => sprintf(__('Gagal %s'), $default_msg),
                            'status' => 'error',
                        );
                    }
                } else {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                    );
                }
            }
        }

        if( empty($result) ) {
            $result = array(
                'msg' => sprintf(__('Berhasil %s'), $default_msg),
                'status' => 'success',
            );
        }

        return $result;
    }

    function getMerge ( $data, $id = false, $fieldName = 'UserAgentCertificate.user_id' ) {
        if( empty($data['UserAgentCertificate']) ) {
            $value = $this->find('all', array(
                'conditions' => array(
                    $fieldName => $id,
                ),
            ));

            if( !empty($value) ) {
                $data['UserAgentCertificate'] = $value;
            }
        }

        return $data;
    }
}
?>