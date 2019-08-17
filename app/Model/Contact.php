<?php
class Contact extends AppModel {
    var $name = 'Contact';
    var $validate = array(
        'subject' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Silakan pilih subyek'
            ),
        ),
        'message' => array(
			'notempty' => array(
				'rule' => array('notempty'),
                'message' => 'Mohon masukkan pesan Anda'
			),
        ),
    );

    public function doSave( $data, $user_id = false ) {
        $result = false;
        $default_msg = __('mengirim pesan');

        if ( !empty($data) ) {

            $data['Contact']['message'] = trim($data['Contact']['message']);

            $this->create();
            $this->set($data);
            if ( $this->validates() ) {
                if( $this->save($data) ) {
                    $debug = Configure::read('debug');

                    if( !empty($debug) ) {
                        $sendEmail = array(
                            Configure::read('__Site.company_profile.email'),
                            'randy@rumahku.com',
                            'alsyifa@rumahku.com',
                            'wulanrumahku@gmail.com',
                            'rikarumahku@gmail.com',
                        );
                    } else {
                        $sendEmail = array(
                            Configure::read('__Site.company_profile.email'),
                            'nabilarumahku@gmail.com',
                            'anarumahku@gmail.com',
                            'ramdanirumahku@gmail.com',
                            'matheaus@rumahku.com',
                            'wulanrumahku@gmail.com',
                            'randy@rumahku.com',
                        );
                    }

                    $msg = sprintf(__('Berhasil %s. Team support kami akan segera menghubungi Anda.'), $default_msg);
                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $msg,
                        ),
                        'SendEmail' => array(
                            'to_name' => __('Support %s', Configure::read('__Site.site_name')),
                            // 'to_email' => 'adminrumahku@yopmail.com',
                            'to_email' => $sendEmail,
                            'subject' => __('Pesan Bantuan'),
                            'template' => 'contact',
                            'data' => $data,
                        ),
                    );
                } else {
                    $validationErrors = array();

                    if(!empty($this->validationErrors)){
                        $validationErrors = array_merge($validationErrors, $this->validationErrors);
                    }

                    $msg = sprintf(__('Gagal %s'), $default_msg);
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                        'data' => $data,
                        'Log' => array(
                            'activity' => $msg,
                            'error' => 1,
                        ),
                        'validationErrors' => $validationErrors
                    );
                }
            } else {
                $validationErrors = array();

                if(!empty($this->validationErrors)){
                    $validationErrors = array_merge($validationErrors, $this->validationErrors);
                }

                $result = array(
                    'msg' => sprintf(__('Gagal %s'), $default_msg),
                    'status' => 'error',
                    'data' => $data,
                    'validationErrors' => $validationErrors
                );
            }
        }
        
        return $result;
    }
}
?>