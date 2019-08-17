<?php
class PropertyDraft extends AppModel {
	var $name = 'PropertyDraft';

    var $belongsTo = array(
        'Property' => array(
            'className' => 'Property',
            'foreignKey' => 'property_id',
        ),
        'Region' => array(
            'className' => 'Region',
            'foreignKey' => 'region_id',
        ),
        'City' => array(
            'className' => 'City',
            'foreignKey' => 'city_id',
        ),
        'Subarea' => array(
            'className' => 'Subarea',
            'foreignKey' => 'subarea_id',
        ),
    );

	function getData( $find, $options = false, $elements = array() ){
        $user_login_id = Configure::read('User.id');
        $status = isset($elements['status'])?$elements['status']:'active';

        $default_options = array(
            'conditions'=> array(
                'PropertyDraft.status' => 1,
                'PropertyDraft.user_id' => $user_login_id,
            ),
			'order' => array(
				'PropertyDraft.created' => 'DESC', 
			),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
        );

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
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    function doSave( $data, $session_id = false, $id = false ) {
        $result = false;
        $msg = __('menyimpan Properti kedalam Draft');

        if( !empty($data['PropertyDraft']) ) {
            $dataDraft['PropertyDraft'] = $data['PropertyDraft'];
            unset($data['PropertyDraft']);
        }

        if ( !empty($dataDraft) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
            }
            
            $propertyMedia = $this->Property->PropertyMedias->getData('first', array(
                'conditions' => array(
                    'PropertyMedias.session_id' => $session_id,
                ),
                'order' => array(
                    'PropertyMedias.primary' => 'DESC',
                    'PropertyMedias.id' => 'ASC',
                ),
            ), array(
                'status' => 'all',
            ));
            $photo = !empty($propertyMedia['PropertyMedias']['name'])?$propertyMedia['PropertyMedias']['name']:false;
            $data['Property']['photo'] = $photo;
            
            $dataDraft['PropertyDraft']['content'] = serialize($data);
            $this->set($dataDraft);

            if( $this->validates() ) {
                if( $this->save() ) {
                    $id = $this->id;
                    $msg = sprintf(__('Berhasil %s'), $msg);
                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $msg,
                            'document_id' => $id,
                        ),
                    );
                } else {
                    $msg = sprintf(__('Gagal %s'), $msg);
                    $result = array(
                        'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'error' => 1,
                        ),
                    );
                }
            } else {
                $result = array(
                    'msg' => sprintf(__('Gagal %s'), $msg),
                    'status' => 'error',
                );
            }
        }

        return $result;
    }

    function doToggle( $id = false ) {
        $msg = __('menghapus draft properti');

        $this->id = $id;
        $this->set('status', 0);

        if( $this->save() ) {
            $msg = sprintf(__('Berhasil %s'), $msg);
            $result = array(
                'msg' => $msg,
                'status' => 'success',
                'Log' => array(
                    'activity' => $msg,
                    'document_id' => $id,
                ),
            );
        } else {
            $msg = sprintf(__('Gagal %s'), $msg);
            $result = array(
                'msg' => $msg,
                'status' => 'error',
                'Log' => array(
                    'activity' => $msg,
                    'document_id' => $id,
                    'error' => 1,
                ),
            );
        }

        return $result;
    }

    function doCompleted( $id = false, $property_id = false ) {
        $msg = __('menyelesaikan draft properti');

        $this->id = $id;
        $this->set('status', 0);
        $this->set('completed', 1);
        $this->set('property_id', $property_id);

        if( $this->save() ) {
            $msg = sprintf(__('Berhasil %s'), $msg);
            $result = array(
                'Log' => array(
                    'activity' => $msg,
                    'document_id' => $id,
                ),
            );
        } else {
            $msg = sprintf(__('Gagal %s'), $msg);
            $result = array(
                'Log' => array(
                    'activity' => $msg,
                    'document_id' => $id,
                    'error' => 1,
                ),
            );
        }

        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));

        if( !empty($keyword) ) {
            $this->virtualFields['find_keyword'] = sprintf(
                'MATCH(
                    PropertyDraft.keyword
                ) AGAINST(\'%s\' IN BOOLEAN MODE)', $keyword
            );

            $default_options['conditions'] = array(
                'OR' => array(
                    'MATCH(
                    PropertyDraft.keyword
                    ) AGAINST(? IN BOOLEAN MODE)' => $keyword,
                    'PropertyDraft.keyword LIKE' => '%'.$keyword.'%',
                ),
            );
            $default_options['order'] = array(
                'find_keyword' => 'DESC',
            );
        }
        
        return $default_options;
    }

    function getById ( $id ) {
        if( !empty($id) ) {
            $data = $this->getData('first', array(
                'conditions' => array(
                    'PropertyDraft.id' => $id,
                ),
            ));

            if( !empty($data['PropertyDraft']['content']) ) {
                $content = unserialize($data['PropertyDraft']['content']);

                $data = array_merge($content, $data);
            }
        } else {
            $data = false;
        }

        return $data;
    }

    function getMerge ( $id = false, $data = array(), $fieldName = 'PropertyDraft.id' ) {
        if( empty($data['PropertyDraft']) && !empty($id) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    $fieldName => $id,
                ),
            ));

            if( !empty($value) ) {
                $data = array_merge($data, $value);
            }
        }

        return $data;
    }
}
?>