<?php
class AttributeSet extends AppModel {
    var $validate = array(
        'name' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Please enter attribute set name',
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

    var $hasMany = array(
        'AttributeSetOption' => array(
            'className' => 'AttributeSetOption',
            'foreignKey' => 'attribute_set_id',
        ),
    );

	function getData( $find, $options = false, $elements = false ){
        $show = !empty($elements['show'])?$elements['show']:false;

        $default_options = array(
            'conditions'=> array(
                'AttributeSet.status' => 1,
            ),
            'order'=> array(
                'AttributeSet.order' => 'ASC',
                'AttributeSet.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

        if( !empty($show) ) {
            $default_options['conditions']['AttributeSet.show'] = true;
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
                  $this->alias.'.id',
                  $this->alias.'.slug',
                  $this->alias.'.name',
                  $this->alias.'.bg_color',
                  $this->alias.'.description',
                  $this->alias.'.fix',
                  $this->alias.'.scope',
                  $this->alias.'.order',
                  $this->alias.'.show',
                  $this->alias.'.status',
                  $this->alias.'.created',
                  $this->alias.'.modified' 
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
                'AttributeSet.name LIKE' => '%'.$keyword.'%',
            );
        }
        
        return $default_options;
    }

    public function doSave( $data = false, $value = false, $id = false ) {
        $result = false;
        $msg = __('Attribute set could not be saved. Please, try again');

        if ( !empty($data) ) {
            if( !empty($id) ) {
                $this->id = $id;
            } else {
                $this->create();
            }

            $this->set($data);

            if ( $this->validates() ) {
                $name = !empty($data['AttributeSet']['name'])?$data['AttributeSet']['name']:false;

                if( $this->save($data) ) {
                    $id = $this->id;

                    $msg = sprintf(__('Attribute set %s has been saved'), $name);
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
        $msg = __('Failed to delete attribute set. Please try again');
        $values = $this->getData('all', array(
            'conditions' => array(
                'AttributeSet.id' => $id,
            ),
        ));

        if ( !empty($values) ) {
            $flag = $this->updateAll(array(
                'AttributeSet.status' => 0,
            ), array(
                'AttributeSet.id' => $id,
            ));

            if( $flag ) {
                $msg = __('Attribute set has been deleted');
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

    function getMerge( $data, $id, $scope = false, $with_contain = true ){
        $options = array(
            'conditions' => array(
                'AttributeSet.id' => $id,
            ),
        );

        if( !empty($scope) ) {
            $options['conditions']['AttributeSet.scope'] = $scope;
        }

        if( !empty($with_contain) ) {
            $options['contain'][] = 'AttributeSetOption';
        }

        $value = $this->getData('first', $options);

        if(!empty($value)){
            if( !empty($value['AttributeSetOption']) ) {
                foreach ($value['AttributeSetOption'] as $key => $val) {
                    $attribute_id = !empty($val['attribute_id'])?$val['attribute_id']:false;
                    $val = $this->AttributeSetOption->Attribute->getMerge($val, $attribute_id);

                    $value['AttributeSetOption'][$key] = $val;
                }
            }
            
            if( !empty($data) ) {
                $data = array_merge($data, $value);
            } else {
                $data = $value;
            }
        }

        return $data;
    }

    function getDataList ( $data, $crm = false ) {
        $type = array();

        if( !empty($crm['CrmProjectPayment']) ) {
            $type['payment'] = true;
        }

        if( !empty($data) ) {
            if( !empty($data[0]) ) {
                foreach($data as $key => $value) {
                    $id = !empty($value['AttributeSet']['id'])?$value['AttributeSet']['id']:false;

                    $value = $this->AttributeSetOption->getMerge($value, $id, 'AttributeSetOption', true, $type);

                    $data[$key] = $value;
                }
            } else if( !empty($data['AttributeSet']) ) {
                $id = !empty($data['AttributeSet']['id'])?$data['AttributeSet']['id']:false;

                $data = $this->AttributeSetOption->getMerge($data, $id, 'AttributeSetOption', true, $type);
            }
        }

        return $data;
    }
}
?>