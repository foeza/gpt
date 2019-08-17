<?php
class PropertyVideos extends AppModel {
    var $name = 'PropertyVideos';

    var $validate = array(
        'url' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon masukkan url youtube',
            ),
        ),
    );

    var $belongsTo = array(
        'Property' => array(
            'className' => 'Property',
            'foreignKey' => 'property_id',
        ),
    );

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['order_sort'] = sprintf('CASE WHEN %s.order IS NULL THEN 1 ELSE 0 END', $this->alias);
    }

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('__Site.is_rest');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'PropertyVideos.id',
                  'PropertyVideos.property_id',
                  'PropertyVideos.session_id',
                  'PropertyVideos.youtube_id',
                  'PropertyVideos.url',
                  'PropertyVideos.title',
                  'PropertyVideos.approved',
                  'PropertyVideos.status',
                  'PropertyVideos.modified',
                );
            }
        }

        return $options;
    }

    function getData( $find, $options = false, $elements = array() ){
        $is_rest = Configure::read('__Site.is_rest');

        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(),
            'order' => array(
                'PropertyVideos.order_sort' => 'ASC', 
                'PropertyVideos.order' => 'ASC', 
                'PropertyVideos.id' => 'ASC', 
            ),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'non-active':
                $default_options['conditions']['PropertyVideos.status'] = 0;
                break;

            case 'all':
                $default_options['conditions']['PropertyVideos.status'] = 1;
                break;

            case 'pending':
                $default_options['conditions']['PropertyVideos.status'] = 1;
                $default_options['conditions']['PropertyVideos.approved'] = 0;
                break;
            
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                    'PropertyVideos.status'=> 1,
                    'PropertyVideos.approved' => 1,
                ));
                break;
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
            if(isset($options['offset'])){
                $default_options['offset'] = $options['offset'];
            }
        }

        $default_options = $this->_callFieldForAPI($find, $default_options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function doSave( $datas, $validate = false ) {
        $contentErrorURL = array();
        $isAdmin = Configure::read('User.admin');
        $approval = Configure::read('Config.Approval.Property');

        if ( !empty($datas) ) {
            foreach ($datas as $key => $data) {
                $this->create();

                if( !empty($isAdmin) || empty($approval) ) {
                    $data['PropertyVideos']['approved'] = 1;
                }

                $this->set($data);
                $url = !empty($data['PropertyVideos']['url'])?trim($data['PropertyVideos']['url']):false;

                if( $this->validates() ) {
                    if( empty($validate) ) {
                        if( !$this->save($data) ) {
                            $contentErrorURL[] = $url;
                        }
                    }
                } else {
                    $contentErrorURL[] = $url;
                }
            }
        }

        return $contentErrorURL;
    }

    function doSaveVideo( $datas, $property_id = false ) {
        $result = false;
        $contentErrorURL = array();

        if ( !empty($datas) ) {
            $contentErrorURL = $this->doSave($datas, true);

            if( empty($contentErrorURL) ) {
                $this->doSave($datas);
				$data	= $this->read();
                
                $msg = __('Berhasil menambahkan video');
                $result	= array(
                    'msg' => $msg,
                    'status' => 'success',
					'data'	=> $data, 
                    'Log' => array(
                        'activity' => $msg,
                        'document_id' => $property_id,
                    ),
                );
            } else {
                $contentErrorURL = array_filter($contentErrorURL);
                $contentMsg = '';

                if( !empty($contentErrorURL) ) {
                    $contentMsg = sprintf(': <ul><li>%s</li></ul>', implode('</li><li>', $contentErrorURL));
                }

                $msg = sprintf(__('Gagal menambahkan Video%s. Mohon lengkapi URL video yang ingin Anda unggah.'), $contentMsg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'document_id' => $property_id,
                        'error' => 1,
                    ),
                );
            }
        }

        return $result;
    }

    function doChange( $id = false, $session_id = false ) {
        return $this->updateAll(array( 
            'PropertyVideos.property_id' => $id,
            'PropertyVideos.modified' => "'".date('Y-m-d H:i:s')."'",
        ), array( 
            'PropertyVideos.session_id' => $session_id,
            'PropertyVideos.status' => 1,
        ));
    }

    function doTitle( $id, $value, $title = false ) {
        $result = false;
        $defaul_msg = __('mengubah judul video');

        if( !empty($value) ) {
            $property_id = $this->filterEmptyField($value, 'PropertyVideos', 'property_id');

            $this->id = $id;
            $this->set('title', $title);

            if( $this->save() ) {
                $msg = sprintf(__('Berhasil %s'), $defaul_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => __('%s #%s', $msg, $id),
                        'document_id' => $property_id,
                    ),
                );
            } else {
                $msg = sprintf(__('Gagal %s'), $defaul_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => __('%s #%s', $msg, $id),
                        'document_id' => $property_id,
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result = array(
                'msg' => sprintf(__('Gagal %s'), $defaul_msg),
                'status' => 'error',
            );
        }

        return $result;
    }

    function doToggle( $property_id = false, $id = false, $session_id = false ) {
    //  if(empty($session_id)){
            $find_media = $this->getData('first', array(
                'conditions' => array(
                    'PropertyVideos.id' => $id
                )
            ), array(
                'status' => 'all'
            ));

            if($find_media){
                $session_id = Common::hashEmptyField($find_media, 'PropertyVideos.session_id');
            }

		//	if(!empty($find_media['PropertyVideos']['session_id'])){
		//		$session_id = $find_media['PropertyVideos']['session_id'];
		//	}
    //  }

		$conditions = array('PropertyVideos.id' => $id);

		if($session_id){
			$conditions = array_merge($conditions, array(
				'PropertyVideos.session_id' => $session_id, 
			));
		}

        $default_msg = __('menghapus video properti');
        $flagDelete = $this->updateAll(array(
            'PropertyVideos.status' => 0,
            'PropertyVideos.modified' => "'".date('Y-m-d H:i:s')."'",
        ), $conditions);

        if(is_array($id)){
            $list_id = implode(', ', $id);
        }else{
             $list_id = $id;
        }

        if( !empty($flagDelete) ) {
            $msg = sprintf(__('Berhasil %s'), $default_msg);
            $result = array(
                'msg' => sprintf(__('Berhasil %s'), $default_msg),
                'status' => 'success',
                'Log' => array(
                    'activity' => __('%s #%s', $msg, $list_id),
                    'document_id' => $property_id,
                ),
            );
        } else {
            $msg = sprintf(__('Gagal %s'), $default_msg);
            $result = array(
                'msg' => sprintf(__('Gagal %s'), $default_msg),
                'status' => 'error',
                'Log' => array(
                    'activity' => __('%s #%s', $msg, $list_id),
                    'document_id' => $property_id,
                    'error' => 1,
                ),
            );
        }

        return $result;
    }

    function getFromSessionID ( $id, $session_id ) {
        return $this->getData('first', array(
            'conditions' => array(
                'PropertyVideos.id' => $id,
                'PropertyVideos.session_id' => $session_id,
            ),
        ), array(
            'status' => 'all',
        ));
    }

    function doOrder( $id = false, $session_id = false ) {
        $default_msg = __('mengurutkan video properti');
        $data = $this->getData('list', array(
            'conditions' => array(
                'PropertyVideos.id' => $id,
                'PropertyVideos.session_id' => $session_id,
            ),
            'fields' => array(
                'PropertyVideos.id', 'PropertyVideos.id',
            ),
        ), array(
            'status' => 'all',
        ));

        if( !empty($id) ) {
            $idx = 0;
            foreach ($id as $key => $value) {
                if( !empty($data[$value]) ) {
                    $this->set('order', $idx);
                    $this->id = $value;
                    $this->save();
                    $idx++;
                }
            }
        }

        return array(
            'msg' => sprintf(__('Berhasil %s'), $default_msg),
            'status' => 'success',
        );
    }

    function getMerge ( $data, $id, $fields = 'all', $status = 'all' ) {
        if( empty($data['PropertyVideos']) && !empty($id) ) {
            $value = $this->getData($fields, array(
                'conditions' => array(
                    'PropertyVideos.property_id' => $id,
                ),
            ), array(
                'status' => $status,
            ));

            if( !empty($value) ) {
                if( $fields == 'count' ) {
                    $data['PropertyVideos']['cnt'] = $value;
                } else {
                    $data['PropertyVideos'] = $value;
                }
            }
        }

        return $data;
    }

    function approveMultiple($property_id, $id = false){
        if(!empty($id)){
            $this->updateAll(array( 
                'PropertyVideos.approved' => 1,
                'PropertyVideos.modified' => "'".date('Y-m-d H:i:s')."'",
            ), array( 
                'PropertyVideos.id' => $id,
                'PropertyVideos.status' => 1,
            ));

            $this->declineApproval($property_id);
        }else{
            $this->updateAll(array( 
                'PropertyVideos.approved' => 1,
                'PropertyVideos.modified' => "'".date('Y-m-d H:i:s')."'",
            ), array( 
                'PropertyVideos.property_id' => $property_id,
                'PropertyVideos.status' => 1,
            ));
        }
    }

    function declineApproval($property_id){
        $this->updateAll(array( 
            'PropertyVideos.approved' => 0,
            'PropertyVideos.status' => 0,
            'PropertyVideos.modified' => "'".date('Y-m-d H:i:s')."'",
        ), array( 
            'PropertyVideos.property_id' => $property_id,
            'PropertyVideos.status' => 1,
            'PropertyVideos.approved' => 0,
        ));
    }
}
?>