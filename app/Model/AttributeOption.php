<?php
class AttributeOption extends AppModel {
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Please enter option name',
            ),
        ),
        'order' => array(
            'numeric' => array(
                'allowEmpty' => true,
                'rule' => array('numeric'),
                'message' => 'Please enter number of order',
            ),
        ),
    );

    var $belongsTo = array(
        'Attribute' => array(
            'className' => 'Attribute',
            'foreignKey' => 'attribute_id',
        ),
    );

    var $hasMany = array(
        'ChildAttributeOption' => array(
            'className' => 'AttributeOption',
            'foreignKey' => 'parent_id',
            'conditions' => array(
                'ChildAttributeOption.status' => 1,
            ),
        ),
    );

	function getData( $find, $options = false, $elements = false ){
        $parent = !empty($elements['parent'])?$elements['parent']:false;

        $default_options = array(
            'conditions'=> array(
                'AttributeOption.status' => 1,
            ),
            'order'=> array(
                'AttributeOption.order' => 'ASC',
                'AttributeOption.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($parent) ) {
            $default_options['conditions']['AttributeOption.parent_id'] = 0;
            $default_options['conditions']['AttributeOption.show'] = true;
        }

        if( !empty($options) ){
            if(!empty($options['conditions'])){
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if(!empty($options['order'])){
                $default_options['order'] = $options['order'];
            }
            if(!empty($options['contain'])){
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
            }
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
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

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  $this->name.'.id',
                  $this->name.'.parent_id',
                  $this->name.'.attribute_id',
                  $this->name.'.attribute_set_id',
                  $this->name.'.document_category_id',
                  $this->name.'.slug',
                  $this->name.'.name',
                  $this->name.'.created',
                  $this->name.'.modified',
                );
            }
        }

        return $options;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));

        if( !empty($keyword) ) {
            $default_options['conditions']['OR'] = array(
                'AttributeOption.name LIKE' => '%'.$keyword.'%',
            );
        }
        
        return $default_options;
    }

    public function doSave( $data, $options, $value = false, $id = false ) {
        $result = false;
        $msg = __('Option could not be saved. Please, try again');

        if ( !empty($data) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
            }

            if( !empty($options) ) {
                if( is_array($options) ) {
                    $fieldName = !empty($options['fieldName'])?$options['fieldName']:false;
                    $id = !empty($options['id'])?$options['id']:false;

                    $data['AttributeOption'][$fieldName] = $id;
                } else {
                    $data['AttributeOption']['attribute_id'] = $options;
                }
            }

            $this->set($data);

            if ( $this->validates() ) {
                $name = !empty($data['AttributeOption']['name'])?$data['AttributeOption']['name']:false;

                if( $this->save($data) ) {
                    $id = $this->id;

                    $msg = sprintf(__('Option %s has been saved'), $name);
                    $result = array(
                        'id' => $id,
                        'msg' => $msg,
                        'status' => 'success',
                        'Log' => array(
                            'activity' => $msg,
                            'old_data' => $value,
                            'document_id' => $id,
                        ),
                    );
                } else {
                    $result = array(
                        'msg' => $msg,
                        'status' => 'error',
                        'data' => $data,
                        'Log' => array(
                            'activity' => $msg,
                            'old_data' => $value,
                            'document_id' => $id,
                            'error' => 1,
                        ),
                    );
                }
            } else {
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'data' => $data,
                );
            }
        } else if( !empty($value) ) {
            $result['data'] = $value;
        }

        return $result;
    }

    function doDelete( $id ) {
        $result = false;
        $msg = __('Failed to delete option. Please try again');
        $values = $this->getData('all', array(
            'conditions' => array(
                'AttributeOption.id' => $id,
            ),
        ));

        if ( !empty($values) ) {
            $flag = $this->updateAll(array(
                'AttributeOption.status' => 0,
            ), array(
                'AttributeOption.id' => $id,
            ));

            if( $flag ) {
                $msg = __('Attribute option has been deleted');
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $values,
                    ),
                );
            } else {
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'old_data' => $values,
                        'error' => 1,
                    ),
                );
            }
        } else {
            $result = array(
                'msg' => $msg,
                'status' => 'error',
            );
        }

        return $result;
    }

    function getChids( $data, $id ){
        if(empty($data['Child'])){
            $values = $this->getData('all', array(
                'conditions' => array(
                    'AttributeOption.parent_id' => $id
                ),
            ));
            // debug($values);die();
            if(!empty($values)){
                foreach ($values as $key => $value) {
                    $id = !empty($value['AttributeOption']['id'])?$value['AttributeOption']['id']:false;
                    $value = $this->getChids($value, $id);
                    $values[$key] = $value;
                }

                $data['Child'] = $values;
            }
        }

        return $data;
    }

    function getMerge( $data, $id, $modelName = 'AttributeOption', $type = 'all', $attribute_set_id = false ){
        if(empty($data[$modelName])){
            if( $type == 'all' ) {
                $conditions = array(
                    'AttributeOption.attribute_id' => $id,
                    'AttributeOption.show' => 1,
                );

                if( !empty($attribute_set_id) ) {
                    $conditions['OR'] = array(
                        array( 'AttributeOption.attribute_set_id' => NULL ),
                        array( 'AttributeOption.attribute_set_id' => $attribute_set_id ),
                    );
                }

                $values = $this->getData('all', array(
                    'conditions' => $conditions,
                ));

                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = !empty($value['AttributeOption']['id'])?$value['AttributeOption']['id']:false;
                        $value = $this->getChids($value, $id);
                        $values[$key] = $value;
                    }

                    $data[$modelName] = $values;
                }
            } else {
                $conditions = array(
                    'AttributeOption.id' => $id
                );

                $value = $this->getData('first', array(
                    'conditions' => $conditions,
                ));

                if(!empty($value)){
                    $data[$modelName] = $value['AttributeOption'];
                }
            }
        }

        return $data;
    }
}
?>