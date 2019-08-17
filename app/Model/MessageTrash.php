<?php
class MessageTrash extends AppModel {
	var $name = 'MessageTrash';

    var $validate = array(
        'user_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Pengirim tidak ditemukan',
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Pengirim tidak ditemukan',
            ),
        ),
        'message_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Pesan tidak ditemukan',
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Pesan tidak ditemukan',
            ),
        ),
    );

    function doToggle( $message_id = false, $msg = 'menghapus pesan' ) {
        $user_login_id = Configure::read('User.id');

        $this->create();
        $this->set('user_id', $user_login_id);
        $this->set('message_id', $message_id);

        if( $this->save() ) {
            $result = array(
                'msg' => sprintf(__('Berhasil %s'), $msg),
                'status' => 'success',
            );
        } else {
            $result = array(
                'msg' => sprintf(__('Gagal %s'), $msg),
                'status' => 'error',
            );
        }

        return $result;
    }

    function doMoveToTrash( $data ) {
        $dataSave = array();
        $flagSave = true;
        $user_login_id = Configure::read('User.id');

        if( !empty($data) ) {
            foreach ($data as $key => $value) {
                $message_id = !empty($value['Message']['id'])?$value['Message']['id']:false;
                $dataTmp['MessageTrash'] = array(
                    'user_id' => $user_login_id,
                    'message_id' => $message_id,
                );
                $this->set($dataTmp);

                if( !$this->validates() ) {
                    $flagSave = false;
                }

                $dataSave[] = $dataTmp;
            }
            
            if( !empty($flagSave) ) {
                if( $this->saveMany($dataSave) ) {
                    $result = array(
                        'msg' => __('Berhasil menghapus pesan'),
                        'status' => 'success',
                    );
                } else {
                    $result = array(
                        'msg' => __('Gagal menghapus pesan'),
                        'status' => 'error',
                    );
                }
            } else {
                $result = array(
                    'msg' => __('Gagal menghapus pesan'),
                    'status' => 'error',
                );
            }
        } else {
            $result = array(
                'msg' => __('Pesan tidak ditemukan'),
                'status' => 'error',
            );
        }

        return $result;
    }
}
?>