<?php
class QueueUserDuplicateProperty extends AppModel {
	var $name = 'QueueUserDuplicateProperty';
	var $displayField = 'name';

	var $belongsTo = array(
		'User' => array(
            'className' => 'User',
            'foreignKey' => 'root_user_id'
        )
	);

	/**
	* 	@param string $find - all, list, paginate
	*		string all - Pick semua field berupa array
	*		string list - Pick semua field berupa key dan value array
	*		string paginate - Pick opsi query
	* 	@param array $options - Menampung semua opsi-opsi yang dibutuhkan dalam melakukan query
	* 	@param boolean $is_merge - True merge default opsi dengan opsi yang diparsing, False gunakan hanya opsi yang diparsing
	*/
	function getData($find = 'all', $options = array()) {
        $status = isset($elements['status']) ? $elements['status']:'non-complete';

        $default_options = array(
            'conditions'=> array(),
            'contain' => array(),
            'fields'=> array(),
            'group'=> array()
        );

        switch ($status) {
            case 'completed':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                    'QueueUserDuplicateProperty.completed' => 1,
                ));
                break;
            
            case 'non-complete':
                $default_options['conditions'] = array_merge($default_options['conditions'], array(
                    'QueueUserDuplicateProperty.completed' => 0,
                    'QueueUserDuplicateProperty.status' => 1,
                ));
                break;
        }

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
        
        if( $find == 'paginate' ) {
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }

        return $result;
    }

    function getMerge ( $data, $id = false, $modelName = 'QueueUserDuplicateProperty', $fieldName = 'QueueUserDuplicateProperty.id' ) {
        if( empty($data[$modelName]) ) {
            $value = $this->getQueueUserDuplicateProperty($id, $fieldName);

            if( !empty($value['QueueUserDuplicateProperty']) ) {
                $data[$modelName] = $value['QueueUserDuplicateProperty'];
            }
        }

        return $data;
    }

    function doSave($data, $value = false, $is_validate = true, $region_id = false){
        if(!empty($data)){
            if(!empty($region_id)){
                $this->id = $region_id;
                $text = 'mengubah';
            }else{
                $this->create();
                $text = 'membuat';
            }

            $this->set($data);

            $result = array(
                'msg' => sprintf(__('Gagal %s duplikat properti.'), $text),
                'status' => 'error',
            );

            $validate = true;
            if($is_validate){
                $validate = $this->validates($data);
            }

            if($validate){
                if($this->save($data)){
                    $id = $this->id;
                    $msg = sprintf(__('Berhasil %s duplikat properti.'), $text);

                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'id' => $id,
                        'Log' => array(
                            'activity' => $msg,
                            'old_data' => $data,
                        ),
                    );
                }
            }else{
                if(!empty($region_id) && !empty($value)){
                    $data = $value;
                }

                $result['data'] = $data;
            }
        }else{
            $result['data'] = $value;
        }

        return $result;
    }

    function offset_data($user_id){
        $this->User->bindModel(array(
            'hasOne' => array(
                'QueueUserDuplicateProperty' => array(
                    'className' => 'QueueUserDuplicateProperty',
                    'foreignKey' => 'root_user_id'
                )
            ),
        ), false);

        $user = $this->User->getData('first', array(
            'conditions' => array(
                'User.id' => $user_id
            ),
        ), array(
            'role' => 'agent',
            'status' => 'all'
        ));
        
        $result = array();

        if(!empty($user)){
            $queue = $this->find('first', array(
                'conditions' => array(
                    'QueueUserDuplicateProperty.root_user_id' => $user_id
                ),
                'order' => array(
                    'QueueUserDuplicateProperty.id' => 'DESC'
                )
            ));

            $parent_id = $this->filterEmptyField($user, 'User', 'parent_id');

            if(!empty($queue['QueueUserDuplicateProperty']['id'])){
                $completed = $this->filterEmptyField($queue, 'QueueUserDuplicateProperty', 'completed');

                if(!$completed){
                    $offset_user_id = $this->filterEmptyField($queue, 'QueueUserDuplicateProperty', 'offset_user_id');
                    $offset_property_id = $this->filterEmptyField($queue, 'QueueUserDuplicateProperty', 'offset_property_id');
                    $id = $this->filterEmptyField($queue, 'QueueUserDuplicateProperty', 'id');

                    $result = array(
                        'offset_user_id' => $offset_user_id,
                        'offset_property_id' => $offset_property_id,
                        'id' => $id
                    );
                }
            }else if(empty($queue)){
                $user_agent = $this->User->getData('first', array(
                    'conditions' => array(
                        'User.parent_id' => $parent_id,
                        'User.id <>' => $user_id
                    ),
                    'order' => array(
                        'User.id' => 'ASC'
                    )
                ), array(
                    'role' => 'agent',
                    'status' => 'all'
                ));

                $offset_user_id = $this->filterEmptyField($user_agent, 'User', 'id');
                
                $data['QueueUserDuplicateProperty'] = $result = array(
                    'offset_user_id'    => $offset_user_id,
                    'root_user_id'      => $user_id
                );

                
                $resl = $this->doSave($data);

                $result['id'] = $this->filterEmptyField($resl, 'id');
            }
        }

        return $result;
    }

    function complete_offset($offset_id, $user_id, $offset_user_id){
        $this->updateAll(
            array(
                'QueueUserDuplicateProperty.completed' => 1
            ),
            array(
                'QueueUserDuplicateProperty.offset_user_id' => $offset_user_id,
                'QueueUserDuplicateProperty.root_user_id' => $user_id,
                'QueueUserDuplicateProperty.id' => $offset_id,
            )
        );

        $user = $this->User->getData('first', array(
            'conditions' => array(
                'User.id' => $user_id
            ),
        ), array(
            'role' => 'agent',
            'status' => 'all'
        ));

        $parent_id = $this->filterEmptyField($user, 'User', 'parent_id');

        $user_agent = $this->User->getData('first', array(
            'conditions' => array(
                'User.id >' => $offset_user_id,
                'User.id <>' => $user_id,
                'User.parent_id' => $parent_id
            ),
            'order' => array(
                'User.id' => 'ASC'
            )
        ), array(
            'role' => 'agent',
            'status' => 'all'
        ));

        if(!empty($user_agent)){
            $offset_user_id = $this->filterEmptyField($user_agent, 'User', 'id');

            $data['QueueUserDuplicateProperty'] = array(
                'offset_user_id'    => $offset_user_id,
                'root_user_id'      => $user_id
            );
            
            $this->doSave($data);
        }
    }

    function last_offset_property($id, $offset_property_id){
        return $this->updateAll(
            array(
                'QueueUserDuplicateProperty.offset_property_id' => $offset_property_id
            ),
            array(
                'QueueUserDuplicateProperty.id' => $id
            )
        );
    }

    function checkPropertySync($offset_id, $user_id, $offset_user_id, $property_id){
        $check_property = $this->User->Property->getData('first', array(
            'conditions' => array(
                'Property.id >' => $property_id,
                'Property.user_id' => $user_id
            )
        ), array(
            'status' => 'all'
        ));

        if(empty($check_property)){
            $this->complete_offset($offset_id, $user_id, $offset_user_id);
        }
    }
}
?>