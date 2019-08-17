<?php
class PropertyNotification extends AppModel {
	var $name = 'PropertyNotification';

    var $validate = array(
        'property_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih properti yang akan ditandai terjual atau tersewa',
            ),
        ),
        'message' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Keterangan ditolak harap diisi',
            ),
        ),
    );

	var $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
		)
	);

	function getData($find = 'all', $options = array(), $is_merge = true ) {
		$default_options = array(
			'conditions' => array(
				'PropertyNotification.status' => 1,
			),
			'order' => array(
				'PropertyNotification.id' => 'ASC'
			),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
		);

		if( $is_merge ) {
			if(!empty($options['conditions'])){
				$default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
			}
			if(!empty($options['contain'])){
				$default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
			}
			if(!empty($options['limit'])){
				$default_options['limit'] = $options['limit'];
			}
			if(!empty($options['fields'])){
				$default_options['fields'] = $options['fields'];
			}
			if(!empty($options['order']) && !empty($default_options['order'])){
				$default_options['order'] = array_merge($default_options['order'], $options['order']);
			}
		} else if( !empty($options) && !$is_merge ) {
			$default_options = $options;
		}

		if( $find == 'paginate' ) {
			$result = $default_options;
		} else {
			$result = $this->find($find, $default_options);
		}
        return $result;
	}

    function getMerge ( $data, $property_id ) {
		if( empty($data['PropertyNotification']) && !empty($property_id) ) {
			$value = $this->getData('first', array(
				'conditions' => array(
					'PropertyNotification.property_id' => $property_id,
				),
			));
			
			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}

	function doSave( $data, $value, $property_id ) {
        $result = false;
        $default_msg = __('menolak properti');
        $user_login_id = Configure::read('User.id');

        $message = !empty($data['PropertyNotification']['message'])?$data['PropertyNotification']['message']:false;

        $mls_id = !empty($value['Property']['mls_id'])?$value['Property']['mls_id']:false;
        $user_id = !empty($value['Property']['user_id'])?$value['Property']['user_id']:false;
        $in_update = !empty($value['Property']['in_update'])?$value['Property']['in_update']:false;

        if ( !empty($data) ) {
            $this->create();

            $data['PropertyNotification']['user_id'] = $user_login_id;
            $data['PropertyNotification']['in_updated'] = $in_update;

            $this->set($data);

            if( $this->validates() ) {
                if( $this->save() ) {
                	$id = $this->id;
                    $msg = sprintf(__('Properti dengan ID %s telah ditolak dengan alasan %s'), $mls_id, $message);

                    if( !empty($in_update) ) {
                        $options = array( 
                            'Property.in_update' => 0,
                        );

                        $this->Property->PropertyRevision->unactivateRevision($property_id);
                        $this->Property->PropertyMedias->doRePrimary($property_id, $value);
                        $msg = sprintf(__('Revisi %s'), $msg);
                    } else {
                        $options = array( 
                            'Property.status' => 0,
                        );
                    }

                	$this->Property->updateAll($options, array( 
						'Property.id' => $property_id,
					));

                    $this->updateAll(array(
                        'PropertyNotification.status' => 0,
                    ), array( 
                        'PropertyNotification.id <>' => $id,
                    ));

                    $msg = sprintf(__('Berhasil %s'), $default_msg);

                    $result = array(
                        'msg' => $msg,
                        'status' => 'success',
                        'Notification' => array(
                            'user_id' => $user_id,
                            'name' => sprintf(__('Properti dengan ID %s telah ditolak dengan alasan %s'), $mls_id, $message),
                            'link' => array(
                                'controller' => 'properties',
                                'action' => 'index',
                                'keyword' => $mls_id,
                                'admin' => true,
                            ),
                        ),
                        'Log' => array(
                            'activity' => $msg,
                            'old_data' => $data,
                            'document_id' => $property_id
                        ),
                    );
                } else {
                    $msg = sprintf(__('Gagal %s'), $default_msg);

                    $result = array(
                        'msg' => $msg,
                        'status' => 'error',
                        'Log' => array(
                            'activity' => $msg,
                            'data' => $data,
                            'document_id' => $property_id,
                            'error' => 1,
                        ),
                    );
                }
            } else {
                $msg = sprintf(__('Gagal %s mohon lengkapi semua data yang diperlukan'), $default_msg);

                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'data' => $data,
                        'document_id' => $property_id,
                        'error' => 1,
                    ),
                );
            }
        }

        return $result;
    }

	function get_total_revenue_per_agent( $agent_id = 0, $type = 'interval', $params = array() ){
        
		$this->bindModel(array(
            'belongsTo' => array(
                'Property' => array(
                    'foreignKey' => 'property_id',
                ),
            )
        ), false);

        $total_revenue_per_agent = array();
        $default_options = array(
        	'conditions' => array(
                'Property.user_id' => $agent_id,
            ),
            'group' => array(
                'Property.user_id',
            ),
            'contain' => array(
                'Property',
            ),
            'fields' => array(
                'Property.user_id', 
                'SUM(PropertySold.price_sold) as total_revenue',
            ),
            'order' => array(
                'total_revenue' => 'DESC',
            ),
            'limit' => 5
        );

        if( $type == 'interval' ) {
            
            $total_revenue_per_agent = $this->getData('all', $default_options);

        } else if ( $type == 'range' ) {

            if( !empty($params) && isset($params['date_from']) && isset($params['date_to']) ){
                
                $date_from = $params['date_from'];
                $date_to = $params['date_to'];

                $default_options['conditions']["DATE_FORMAT(PropertySold.sold_date, '%Y-%m-%d') >="] = $date_from;
                $default_options['conditions']["DATE_FORMAT(PropertySold.sold_date, '%Y-%m-%d') <="] = $date_to;

                $total_revenue_per_agent = $this->getData('all', $default_options );
            }
        }

        return $total_revenue_per_agent;
    }

    function doToggle( $id = false ) {
        return $this->updateAll(array(
            'PropertyNotification.status' => 0,
            'PropertyNotification.modified' => "'".date('Y-m-d H:i:s')."'",
        ), array( 
            'PropertyNotification.property_id' => $id,
        ));;
    }
}
?>