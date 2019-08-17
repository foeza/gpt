<?php
class AttributeSetOption extends AppModel {
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
        'AttributeSet' => array(
            'className' => 'AttributeSet',
            'foreignKey' => 'attribute_set_id',
        ),
        'Attribute' => array(
            'className' => 'Attribute',
            'foreignKey' => 'attribute_id',
        ),
    );

	function getData( $find, $options = false, $elements = false ){
        $parent = !empty($elements['parent'])?$elements['parent']:false;

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'AttributeSetOption.id' => 'ASC',
            ),
            'contain' => array(),
            'fields' => array(),
        );

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
                'AttributeSetOption.name LIKE' => '%'.$keyword.'%',
            );
        }
        
        return $default_options;
    }

    public function doSave( $datas, $attribute_set_id, $value = false, $id = false ) {
        $result = false;
        $flag = true;
        $msg = __('Option could not be saved. Please, try again');

        if(!empty($datas)){
            if(isset($datas['AttributeSet']['target'])){
				$datas = is_array($datas['AttributeSet']['target']) ? array_filter($datas['AttributeSet']['target']) : trim($datas['AttributeSet']['target']);
            }

            if( !empty($datas) ) {
                $flag = $this->deleteAll(array(
                    'AttributeSetOption.attribute_set_id' => $attribute_set_id,
                ));

				if($flag){
					foreach($datas as $key => $attribute_id){
						$this->create();
						$data = array(
							'AttributeSetOption' => array(
								'attribute_set_id' => $attribute_set_id,
								'attribute_id' => $attribute_id,
							),
						);

						$this->set($data);
						if( !$this->save($data) ) {
							$flag = false;
						}
					}
				}
            }

            if( !empty($flag) ) {
                $msg = __('Option set has been saved');
                $result = array(
                    'msg' => $msg,
                    'status' => 'success',
                    'Log' => array(
                        'activity' => $msg,
                        'document_id' => $attribute_set_id,
                    ),
                );
            } else {
                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'data' => $data,
                    'Log' => array(
                        'activity' => $msg,
                        'document_id' => $attribute_set_id,
                        'error' => 1,
                    ),
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
                'AttributeSetOption.id' => $id,
            ),
        ));

        if ( !empty($values) ) {
            $flag = $this->updateAll(array(
                'AttributeSetOption.status' => 0,
            ), array(
                'AttributeSetOption.id' => $id,
            ));

            if( $flag ) {
                $msg = __('Option has been deleted');
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

    function getMerge( $data, $id, $modelName = 'AttributeSetOption', $with_contain = true, $type = false ){
        if(empty($data[$modelName])){
            $data_merge = $this->getData('all', array(
                'conditions' => array(
                    'AttributeSetOption.attribute_set_id' => $id
                ),
            ));

            if(!empty($data_merge)){
                if( !empty($with_contain) ) {
                    foreach ($data_merge as $key => $val) {
                        $attribute_id = !empty($val['AttributeSetOption']['attribute_id'])?$val['AttributeSetOption']['attribute_id']:false;

                        $val = $this->Attribute->getMerge($val, $attribute_id, true, $id, $type);
                        $data_merge[$key] = $val;
                    }
                }
                $data[$modelName] = $data_merge;
            }
        }

        return $data;
    }
}
?>