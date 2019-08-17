<?php
class PasswordReset extends AppModel {
	var $name = 'PasswordReset';
	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	function getData( $find='all', $options = array(), $is_merge = true, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
		$default_options = array(
			'conditions'=> array(),
			'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
		);

        switch ($status) {
            case 'all':
                $default_options['conditions'] = array();
                break;

            case 'non-active':
                $default_options['conditions'] = array(
					'PasswordReset.status'=> 0, 
            	);
                break;
            
            default:
                $default_options['conditions'] = array(
					'PasswordReset.status'=> 1, 
            	);
                break;
        }

		if($is_merge){
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
		}else{
			$default_options = $options;
		}

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
		
        return $result;
	}

    function doSave( $value = array() ) {
        $data['PasswordReset'] = array_merge($value, array(
            'reminder_time' => time(),
            'expired_time' => 8640,
        ));
        
        $user_id = !empty($value['user_id'])?$value['user_id']:false;
        $full_name = !empty($value['full_name'])?$value['full_name']:false;
        $email = !empty($value['email'])?$value['email']:false;

        $this->create();
        $this->set($data);

        if( $this->save() ) {
            $id = $this->id;
            $this->updateAll(
                array('PasswordReset.status' => 0),
                array(
                    'PasswordReset.user_id' => $user_id,
                    'PasswordReset.id <' => $id,
                )
            );

            $msg = __('Kami telah mengirimkan Anda link untuk mengubah password, periksa SPAM apabila link tidak ditemukan dalam kotak masuk email Anda.');
            $result = array(
                'msg' => $msg,
                'status' => 'success',
                'Log' => array(
                    'activity' => $msg,
                    'document_id' => $id,
                ),
                'SendEmail' => array(
                    'to_name' => $full_name,
                    'to_email' => $email,
                    'subject' => __('Permintaan Reset Password'),
                    'template' => 'forgot_password',
                    'data' => $data,
                ),
            );
        } else {
            $result = array(
                'msg' => __('Gagal melakukan reset password.'),
                'status' => 'error',
                'data' => $data,
            );
        }

        return $result;
    }
}
?>