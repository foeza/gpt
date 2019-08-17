<?php
class Attribute extends AppModel {
    public $actsAs = array('Tree');

    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Please enter attribute name',
            ),
        ),
        'order' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Please enter number of order',
            ),
        ),
    );

    var $belongsTo = array(
        'ParentAttribute' => array(
            'className' => 'Attribute',
            'foreignKey' => 'parent_id',
        ),
        'AttributeSetOption' => array(
            'className' => 'AttributeSetOption',
            'foreignKey' => 'attribute_id',
        ),
    );

    var $hasMany = array(
        'AttributeOption' => array(
            'className' => 'AttributeOption',
            'foreignKey' => 'attribute_id',
        ),
    );

	function getData( $find, $options = false, $elements = false ){
        $parent = !empty($elements['parent'])?$elements['parent']:false;

        $default_options = array(
            'conditions'=> array(
                'Attribute.status' => 1,
            ),
            'order'=> array(
                'Attribute.parent_id' => 'ASC',
                'Attribute.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($parent) ) {
            $default_options['conditions']['OR'] = array(
                'Attribute.parent_id' => 0,
                'Attribute.parent_id' => NULL,
            );
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

        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        return $result;
    }

    public function _callRefineParams( $data = '', $default_options = false ) {
        $keyword = $this->filterEmptyField($data, 'named', 'keyword', false, array(
            'addslashes' => true,
        ));

        if( !empty($keyword) ) {
            $default_options['conditions']['OR'] = array(
                'Attribute.name LIKE' => '%'.$keyword.'%',
            );
        }
        
        return $default_options;
    }

    public function doSave( $data, $value = false, $id = false ) {
        $result = false;
        $msg = __('Attribute could not be saved. Please, try again');

        if ( !empty($data) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
            }

            $this->set($data);

            if ( $this->validates() ) {
                $name = !empty($data['Attribute']['name'])?$data['Attribute']['name']:false;

                if( $this->save($data) ) {
                    $id = $this->id;

                    $msg = sprintf(__('Attribute %s has been saved'), $name);
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

    function getMerge( $data, $id, $contain = true, $attribute_set_id = false ){
        if(empty($data['Attribute'])){
            $conditions = array(
                'Attribute.id' => $id
            );

            $value = $this->getData('first', array(
                'conditions' => $conditions,
            ));
        } else {
            $value = $data;
        }

        if(!empty($value['Attribute']) && !empty($contain) ){
            if(!empty($value)){
                $value = $this->AttributeOption->getMerge($value, $id, 'AttributeOption', 'all', $attribute_set_id);

                $data = array_merge($data, $value);
            }
        } else {
            $data = array_merge($data, $value);
        }

        return $data;
    }

    function getDataScope ( $scope ) {
        $values = $this->getData('all', array(
            'conditions' => array(
                'Attribute.scope' => $scope,
            ),
        ));

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = !empty($value['Attribute']['id'])?$value['Attribute']['id']:false;
                $value = $this->getMerge($value, $id);

                $values[$key] = $value;
            }
        }

        return $values;
    }

    function getDataSearchOptions () {
        $values = $this->getData('all', array(
            'conditions' => array(
                'Attribute.search_view' => true,
            ),
        ));
        $data = array();

        if( !empty($values) ) {
            foreach ($values as $key => $value) {
                $id = !empty($value['Attribute']['id'])?$value['Attribute']['id']:false;
                $value = $this->getMerge($value, $id);

                if( !empty($value['AttributeOption']) ) {
                    foreach ($value['AttributeOption'] as $key => $val) {
                        $option_id = !empty($val['AttributeOption']['id'])?$val['AttributeOption']['id']:false;
                        $option_name = !empty($val['AttributeOption']['name'])?$val['AttributeOption']['name']:false;
                        $data[$option_id] = $option_name;
                    }
                }
            }
        }

        return $data;
    }
}
?>