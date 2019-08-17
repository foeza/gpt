<?php
class Notification extends AppModel {
	var $name = 'Notification';

	var $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		),
	);

	function getData( $find, $options = false, $elements = array() ){
        $mine = isset($elements['mine'])?$elements['mine']:false;
        $user_login_id = Configure::read('User.id');

        $default_options = array(
            'conditions'=> array(
				'Notification.status' => 1,
        	),
			'order' => array(
				'Notification.created' => 'DESC',
				'Notification.id' => 'DESC',
			),
			'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        if( !empty($mine) ) {
        	$default_options['conditions'][]['OR'] = array(
                'Notification.user_id' => $user_login_id,
                array(
                    'Notification.user_id' => 0,
                    'Notification.global' => 1,
                ),
            );
        }

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
        }

        if( $find == 'paginate' ) {
            if( empty($default_options['limit']) ) {
                $default_options['limit'] = 10;
            }

            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function getNotif () {
        $data = $this->getData('all', array(
        	'limit' => 5,
    	), array(
            'mine' => true,
        ));

        $cnt = $this->getData('count', array(
        	'conditions' => array(
                'Notification.read' => 0,
            ),
    	), array(
            'mine' => true,
        ));

        return array(
            'cnt' => $cnt,
            'data' => $data,
        );
    }

    function doSave( $data = NULL ){
		if( !empty($data) ) {
            $flag = true;
            $users = !empty($data['Notification']['user_id'])?$data['Notification']['user_id']:0;

            switch ($users) {
                case 'admin_company':
                    $users = $this->User->getListAdmin( true, true );
                    break;
            }

            if( !is_array($users) ) {         
                $users = array(
                    $users,
                );
            }

            if( !empty($data['Notification']['link']) ) {
                $data['Notification']['link'] = serialize($data['Notification']['link']);
            }

            if( !empty($users) ) {
                foreach ($users as $key => $user_id) {
                    $this->create();
                    $data['Notification']['user_id'] = $user_id;

                    if( !$this->save($data) ) {
                        $flag = false;
                    }
                }
            }

            return $flag;
		}
	}

    function doRead( $id ) {
        $default_msg = __('mengubah status notifikasi menjadi sudah dibaca');

        $this->id = $id;
        $this->set('read', 1);

        if( $this->save() ) {
            $msg = sprintf(__('Berhasil %s'), $default_msg);
            $result = array(
                'msg' => $msg,
                'status' => 'success',
                'Log' => array(
                    'activity' => $msg,
                    'document_id' => $id,
                ),
            );
        } else {
            $msg = sprintf(__('Gagal %s'), $default_msg);
            $result = array(
                'msg' => $msg,
                'status' => 'error',
                'Log' => array(
                    'activity' => $msg,
                    'document_id' => $id,
                ),
            );
        }

        return $result;
    }

    function doSaveMany($data){
        $this->create();

        return $this->saveMany($data);
    }
}
?>