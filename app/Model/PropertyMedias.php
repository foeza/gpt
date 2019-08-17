<?php
class PropertyMedias extends AppModel {
	var $name = 'PropertyMedias';

	var $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
        'CategoryMedias' => array(
            'className' => 'CategoryMedias',
            'foreignKey' => 'category_media_id',
        ),
	);

    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
        $this->virtualFields['order_sort'] = sprintf('CASE WHEN %s.order IS NULL THEN 1 ELSE 0 END', $this->alias);
    }

	function getData( $find, $options = false, $elements = array() ){
        $status = isset($elements['status'])?$elements['status']:'active';
        $type = isset($elements['type'])?$elements['type']:'photo';

        $default_options = array(
            'conditions'=> array(),
			'order' => array(
                'PropertyMedias.primary' => 'DESC', 
                'PropertyMedias.order_sort' => 'ASC', 
				'PropertyMedias.order' => 'ASC', 
				'PropertyMedias.id' => 'ASC', 
			),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

        switch ($status) {
            case 'non-active':
                $default_options['conditions']['PropertyMedias.status'] = 0;
                break;

            case 'all':
                $default_options['conditions']['PropertyMedias.status'] = 1;
                break;

            case 'pending':
                $default_options['conditions']['PropertyMedias.status'] = 1;
                $default_options['conditions']['PropertyMedias.approved'] = 0;
                break;
            
            case 'active':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
					'PropertyMedias.status'=> 1,
					'PropertyMedias.approved' => 1,
            	));
                break;

            case 'primary':
                $default_options['conditions']['PropertyMedias.status'] = 1;
                $default_options['conditions']['PropertyMedias.primary'] = 1;
                break;
        }

        switch ($type) {
            case 'video':
                $default_options['conditions']['PropertyMedias.type'] = 2;
                break;
            
            case 'photo':
                $default_options['conditions']['PropertyMedias.type'] = 1;
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
        } else if( !empty($options) ) {
            $default_options = $options;
        }

        $default_options = $this->_callFieldForAPI($find, $default_options);

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'PropertyMedias.id',
                  'PropertyMedias.property_id',
                  'PropertyMedias.session_id',
                  'PropertyMedias.category_media_id',
                  'PropertyMedias.alias',
                  'PropertyMedias.name',
                  'PropertyMedias.title',
                  'PropertyMedias.primary',
                  'PropertyMedias.approved',
                  'PropertyMedias.status',
                  'PropertyMedias.modified',
                );
            }
        }

        return $options;
    }

    function doSave( $data, $session_id = false ) {
        $is_rest = Configure::read('__Site.is_rest');
        
        $result = new stdClass();
        $result_arr = array();

        $isAdmin = Configure::read('User.admin');
        $approval = Configure::read('Config.Approval.Property');

        if ( !empty($data) ) {
        	if( !empty($data['error']) ){
	  			$result->message = $data['message'];
				$result->error = 'Error:';
	  		}else{
	            $this->create();

                if( !empty($isAdmin) || empty($approval) ) {
                    $data['PropertyMedias']['approved'] = 1;
                }

	            $this->set($data);

	            if( !$this->save() ) {
                    $result->error = 'Error:';
	  				$result->message = __('Gagal menyimpan foto properti');

                    // untuk kebutuhan API
                    $result_arr = array(
                        'status'    => 'error',
                        'msg'       => $result->message
                    );
	            } else {
                    $result_arr = array(
                        'status'    => 'success',
                        'msg'       => __('Berhasil mengunggah foto')
                    );

	            	$media_id = $this->id;

                    $result->delete_url = sprintf('/ajax/property_photo_delete/%s/%s/', $media_id, $session_id);
                    $result->primary_url = sprintf('/ajax/property_photo_primary/%s/%s/', $media_id, $session_id);
		  			$result->primary = !empty($data['PropertyMedias']['primary'])?$data['PropertyMedias']['primary']:false;

                    $temp_arr['PropertyMedias']['id'] = $result->id = $media_id;

		  			if(!empty($data['imagePath'])){
			  			$temp_arr['PropertyMedias']['thumbnail_url'] = $result->thumbnail_url = $data['imagePath'];
		  			}

		  			if(!empty($data['name'])){
			  			$result->name = $data['name'];
		  			}

                    $result_arr = array_merge($result_arr, $temp_arr);
	            }
	  		}
        }

        if($is_rest){
            $result = $result_arr;
        }

        return $result;
    }

    function doToggle( $id = false, $session_id = false ) {
        $find_media = $this->getData('first', array(
            'conditions' => array(
                'PropertyMedias.id' => $id
            )
        ), array(
            'status' => 'all'
        ));
        $property_id = $this->filterEmptyField($find_media, 'PropertyMedias', 'property_id');

        if( !empty($find_media) ) {
            $media_name = !empty($find_media['PropertyMedias']['name'])?$find_media['PropertyMedias']['name']:false;

            if(empty($session_id) && !empty($find_media['PropertyMedias']['session_id'])){
                $session_id = $find_media['PropertyMedias']['session_id'];
            }
        } else {
            $media_name = false;
        }

        $default_msg = __('menghapus foto properti');
        $flagDelete = $this->updateAll(array(
            'PropertyMedias.status' => 0,
            'PropertyMedias.modified' => "'".date('Y-m-d H:i:s')."'",
        ), array(
            'PropertyMedias.id' => $id,
            'PropertyMedias.session_id' => $session_id,
            'PropertyMedias.primary' => 0,
        ));

        if(is_array($id)){
            $list_id = implode(', ', $id);
        }else{
            $list_id = $id;
        }

        if( !empty($flagDelete) ) {
            $msg = sprintf(__('Berhasil %s'), $default_msg);
        	$result = array(
                'msg' => $msg,
                'status' => 'success',
                'media_name' => $media_name,
                'Log' => array(
                    'activity' => __('%s #%s', $msg, $list_id),
                    'document_id' => $property_id,
                ),
            );
        } else {
            $msg = sprintf(__('Gagal %s'), $default_msg);
        	$result = array(
                'msg' => $msg,
                'status' => 'error',
                'media_name' => $media_name,
                'Log' => array(
                    'activity' => __('%s #%s', $msg, $list_id),
                    'document_id' => $property_id,
                    'error' => 1,
                ),
            );
        }

        return $result;
    }

    function doPrimary( $id = false, $property_id = 0, $session_id = false ) {
        $this->id = $id;
        $this->set('primary', 1);
        $default_msg = __('menjadikan foto utama');
        $is_admin = Configure::read('User.admin');

        if( $this->save() ) {
            $conditions = array( 
                'PropertyMedias.id <>' => $id,
            );

            if( !empty($property_id) ) {
                $conditions['PropertyMedias.property_id'] = $property_id;
            } else {
                $conditions['PropertyMedias.session_id'] = $session_id;
            }

            $this->updateAll(array( 
                'PropertyMedias.primary' => 0,
                'PropertyMedias.modified' => "'".date('Y-m-d H:i:s')."'",
            ), $conditions);

            if( !empty($property_id) ) {
                $media = $this->getData('first', array(
                    'conditions' => array(
                        'PropertyMedias.id' => $id,
                    ),
                ), array(
                    'status' => 'all',
                ));

                $media_approved = (!empty($media['PropertyMedias']['approved'])) ? true : false;
                $media_name = (!empty($media['PropertyMedias']['name'])) ? $media['PropertyMedias']['name'] : false;

                $property = $this->Property->getData('first', array(
                    'conditions' => array(
                        'Property.id' => $property_id
                    )
                ));

                $active_property = !empty($property['Property']['active']) ? true : false;

                if( !empty($media_name) && ( ($media_approved || empty($active_property)) || $is_admin) ) {
                    $this->Property->id = $property_id;
                    $this->Property->set('photo', $media_name);
                    $this->Property->save();
                }
            }

            $msg = sprintf(__('Berhasil %s'), $default_msg);
            $result = array(
                'msg' => $msg,
                'status' => 'success',
                'Log' => array(
                    'activity' => __('%s #%s', $msg, $id),
                    'document_id' => $property_id,
                ),
            );
        } else {
            $msg = sprintf(__('Gagal %s'), $default_msg);
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

        return $result;
    }

    function doOrder( $id = false, $session_id = false ) {
        $default_msg = __('mengurutkan foto properti');
        $data = $this->getData('list', array(
            'conditions' => array(
                'PropertyMedias.id' => $id,
                'PropertyMedias.session_id' => $session_id,
            ),
            'fields' => array(
                'PropertyMedias.id', 'PropertyMedias.id',
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

    function doSavePhoto( $id = false, $session_id = false, $photo_name = false, $photo_id = false ) {
        if( !empty($photo_name) ) {
            
            $this->Property->updateAll(array( 
                'Property.photo' => "'".$photo_name."'",
            ), array( 
                'Property.id' => $id,
            ));

            if( !empty($photo_id) ) {
                $this->id = $photo_id;
                $this->set('primary', 1);
                $this->save();
            }
        }

        return $this->updateAll(array( 
			'PropertyMedias.property_id' => $id,
            'PropertyMedias.modified' => "'".date('Y-m-d H:i:s')."'",
		), array( 
			'PropertyMedias.session_id' => $session_id,
			'PropertyMedias.status' => 1,
		));
    }

    function getMerge ( $data, $id, $fields = 'all', $status = 'all' ) {
        if( empty($data['PropertyMedias']) && !empty($id) ) {
            $value = $this->getData($fields, array(
                'conditions' => array(
                    'PropertyMedias.property_id' => $id,
                ),
            ), array(
                'status' => $status,
            ));

            if( !empty($value) ) {
                if( $fields == 'count' ) {
                    $data['PropertyMedias']['cnt'] = $value;
                } else {
                    if( !empty($value['PropertyMedias']) ) {
                        $category_media_id = !empty($value['PropertyMedias']['category_media_id'])?$value['PropertyMedias']['category_media_id']:false;
                        $value = $this->CategoryMedias->getMerge($value, $category_media_id);
                    } else if( !empty($value[0]) ) {
                        foreach ($value as $key => $val) {
                            $category_media_id = !empty($val['PropertyMedias']['category_media_id'])?$val['PropertyMedias']['category_media_id']:false;
                            $value[$key] = $this->CategoryMedias->getMerge($val, $category_media_id);
                        }
                    }
                    $data['PropertyMedias'] = $value;
                }
            }
        }

        return $data;
    }

    function getFromSessionID ( $id, $session_id ) {
        return $this->getData('first', array(
            'conditions' => array(
                'PropertyMedias.id' => $id,
                'PropertyMedias.session_id' => $session_id,
            ),
        ), array(
            'status' => 'all',
        ));
    }

    function doTitle( $id, $media, $category_id ) {
        $result = false;
        $defaul_msg = __('mengubah judul foto');

        if( !empty($media) ) {
            $property_id = $this->filterEmptyField($media, 'PropertyMedias', 'property_id');

            $this->id = $id;
            $this->set('category_media_id', $category_id);
            $this->set('title', '');

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

    function doTitleForm( $id, $media, $title ) {
        $result = false;
        $defaul_msg = __('mengubah judul foto');

        if( !empty($media) ) {
            $this->id = $id;
            $this->set('category_media_id', NULL);
            $this->set('title', $title);
            $this->set('category_media_id', NULL);

            if( $this->save() ) {
                $msg = sprintf(__('Berhasil %s'), $defaul_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => __('%s #%s', $msg, $id),
                        // 'document_id' => $property_id,
                    ),
                );
            } else {
                $msg = sprintf(__('Gagal %s'), $defaul_msg);
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => __('%s #%s', $msg, $id),
                        // 'document_id' => $property_id,
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

    function getMergePrimaryPhoto($data, $property_id){
        if(empty($data['PropertyMedias'])){
            $result = $this->getData('first', array(
                'conditions' => array(
                    'PropertyMedias.property_id' => $property_id,
                ),
                'order' => array(
                    'PropertyMedias.primary' => 'DESC', 
                    'PropertyMedias.order_sort' => 'ASC', 
                    'PropertyMedias.order' => 'ASC', 
                    'PropertyMedias.id' => 'ASC', 
                ),
            ));

            if(empty($result)){
                $result = $this->approveMultiple($property_id);
            }

            $data = array_merge($data, $result);
        }

        return $data;
    }

    function doApprove($id, $approve = true){
        $result = array(
            'msg' => __('Gagal menyetujui media'),
            'status' => 'error'
        );

        if(!$approve){
            $result['msg'] = __('Gagal menolak media');
        }

        if(!empty($id)){
            $find_media = $this->getData('count', array(
                'conditions' => array(
                    'PropertyMedias.id' => $id
                )
            ), array(
                'status' => 'pending'
            ));

            if($find_media > 0){
                $this->id = $id;

                $this->set('approved', $approve);

                if($this->save()){
                    $result['status'] = 'success';

                    if($approve){
                        $result['msg'] = __('Berhasil menyetujui media');
                    }else{
                        $result['msg'] = __('Berhasil menolak media');
                    }
                }
            }
        }

        return $result;
    }

    function approveMultiple($property_id, $id = false){
        if(!empty($id)){
            $this->updateAll(array( 
                'PropertyMedias.approved' => 1,
                'PropertyMedias.modified' => "'".date('Y-m-d H:i:s')."'",
            ), array( 
                'PropertyMedias.id' => $id,
                'PropertyMedias.status' => 1,
            ));

            $media = $this->getData('first', array(
                'conditions' => array(
                    'PropertyMedias.id' => $id,
                    'PropertyMedias.primary' => 1
                )
            ), array(
                'status' => 'active'
            ));

            if( !empty($media['PropertyMedias']['name']) ) {
                $this->Property->updateAll(array( 
                    'Property.photo' => "'".$media['PropertyMedias']['name']."'",
                    'Property.modified' => "'".date('Y-m-d H:i:s')."'",
                ), array( 
                    'Property.id' => $property_id,
                ));
            }

            $this->declineApproval($property_id);
        }else{
            $this->updateAll(array( 
                'PropertyMedias.approved' => 1,
                'PropertyMedias.modified' => "'".date('Y-m-d H:i:s')."'",
            ), array( 
                'PropertyMedias.property_id' => $property_id,
                'PropertyMedias.status' => 1,
            ));

            $primary_photo = $this->getData('first', array(
                'conditions' => array(
                    'PropertyMedias.property_id' => $property_id,
                    'PropertyMedias.primary' => 1
                )
            ), array(
                'status' => 'active'
            ));

            if(empty($primary_photo)){
                $primary_photo = $this->getData('first', array(
                    'conditions' => array(
                        'PropertyMedias.property_id' => $property_id,
                    )
                ), array(
                    'status' => 'active'
                ));

                if(!empty($primary_photo)){
                    $this->id = $primary_photo['PropertyMedias']['id'];

                    $this->set('primary', 1);

                    if($this->save()){
                        $primary_photo['PropertyMedias']['primary'] = 1;
                    }
                }
            }

            return $primary_photo;
        }
    }

    function declineApproval($property_id){
        $this->updateAll(array( 
            'PropertyMedias.approved' => 0,
            'PropertyMedias.status' => 0,
            'PropertyMedias.modified' => "'".date('Y-m-d H:i:s')."'",
        ), array( 
            'PropertyMedias.property_id' => $property_id,
            'PropertyMedias.status' => 1,
            'PropertyMedias.approved' => 0,
        ));
    }

    function doRePrimary ( $id, $property, $photo_deleted = false, $media = array() ) {
        $photo = !empty($property['Property']['photo'])?$property['Property']['photo']:false;
        $reprimary = false;

        if( empty($photo_deleted) ) {
            $reprimary = true;
        } else if( $photo == $photo_deleted ) {
            $reprimary = true;
        }

        if( !empty($reprimary) ) {
            if( empty($media) ) {
                $media = $this->getData('first', array(
                    'conditions' => array(
                        'PropertyMedias.property_id' => $id,
                    ),
                    'fields' => array(
                        'PropertyMedias.id',
                        'PropertyMedias.name',
                    ),
                ), array(
                    'status' => 'all'
                ));
            }
            
            $photoMedia = !empty($media['PropertyMedias']['name'])?$media['PropertyMedias']['name']:false;
            $photoMediaId = !empty($media['PropertyMedias']['id'])?$media['PropertyMedias']['id']:false;

            if( !empty($photoMedia) ) {
                $this->Property->id = $id;
                $this->Property->set('photo', $photoMedia);
                $this->Property->save();

                $this->id = $photoMediaId;
                $this->set('primary', $photoMedia);
                $this->save();

                $this->updateAll(
                    array(
                        'PropertyMedias.primary' => 0,
                        'PropertyMedias.modified' => "'".date('Y-m-d H:i:s')."'",
                    ),
                    array(
                        'PropertyMedias.property_id' => $id,
                        'PropertyMedias.id <>' => $photoMediaId,
                    )
                );
            }
        }
    }
}
?>