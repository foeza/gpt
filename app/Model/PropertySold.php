<?php
class PropertySold extends AppModel {
	var $name = 'PropertySold';

    var $validate = array(
        'property_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih properti yang akan ditandai terjual atau tersewa',
            ),
        ),
        'property_action_id' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih status properti',
            ),
        ),
        'sold_by_name' => array(
            'validateSoldBy' => array(
                'rule' => array('validateSoldBy'),
                'message' => 'Mohon masukkan nama Agen yang menjual properti ini',
            ),
        ),
        'price_sold' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon masukkan harga terjual atau tersewa',
            ),
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Mohon masukkan angka untuk harga terjual atau tersewa',
            ),
        ),
        'sold_date' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Mohon pilih tanggal terjual atau tersewa',
            ),
        ),
        'end_date' => array(
            'getEndDate' => array(
                'rule' => array('getEndDate'),
                'message' => 'Mohon pilih tanggal berakhir sewa',
            ),
        ),
        'sold_by_coBroke_id' => array(
            'validateCoBroke' => array(
                'rule' => array('validateCoBroke'),
                'message' => 'Mohon pilih agen co broking',
            ),
        )
    );

	var $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'sold_by_id',
		),
        'Currency' => array(
            'className' => 'Currency',
            'foreignKey' => 'currency_id',
        ),
        'UserClient' => array(
            'className' => 'UserClient',
            'foreignKey' => 'client_id',
        ),
	);

    function validateCoBroke($data){
        $result = false;

        $sold_by_name = $this->filterEmptyField($this->data, 'PropertySold', 'sold_by_name');
        $is_cobroke = $this->filterEmptyField($this->data, 'PropertySold', 'is_cobroke');

        if(!empty($is_cobroke)){
            if(!empty($data['sold_by_coBroke_id'])){
                $result = true;
            }
        }else{
            $result = true;
        }

        return $result;
    }

    function validateSoldBy($data){
        $result = true;

        if(isset($this->data['PropertySold']['is_cobroke'])){
            if(empty($this->data['PropertySold']['sold_by_coBroke_id']) && empty($data['sold_by_name'])){
                $result = false;
            }
        }else{
            if(empty($data['sold_by_name'])){
                $result = false;
            }
        }

        return $result;
    }

    function getEndDate() {
        $property_action_id = !empty($this->data['PropertySold']['property_action_id'])?$this->data['PropertySold']['property_action_id']:false;

        if( $property_action_id == 2 ) {
            if( !empty($this->data['PropertySold']['end_date']) ) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

	function getData($find = 'all', $options = array() ) {
		$default_options = array(
			'conditions' => array(
				'PropertySold.status' => 1,
			),
			'order' => array(),
            'contain' => array(),
            'fields' => array(),
            'group' => array(),
		);

        return $this->merge_options($default_options, $options, $find);
	}

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  $this->alias.'.id',
                  $this->alias.'.property_id',
                  $this->alias.'.property_action_id',
                  $this->alias.'.client_id',
                  $this->alias.'.sold_by_id',
                  $this->alias.'.currency_id',
                  $this->alias.'.period_id',
                  $this->alias.'.client_name',
                  $this->alias.'.sold_by_name',
                  $this->alias.'.price_sold',
                  $this->alias.'.sold_date',
                  $this->alias.'.end_date',
                  $this->alias.'.note',
                  $this->alias.'.rate',
                  $this->alias.'.commission',
                  $this->alias.'.total_commission',
                  $this->alias.'.agent_commission_gross',
                  $this->alias.'.sharingtocompany_percentage',
                  $this->alias.'.royalty_percentage',
                  $this->alias.'.royalty',
                  $this->alias.'.pph_percentage',
                  $this->alias.'.pph',
                  $this->alias.'.agent_commission_net',
                  $this->alias.'.company_commission',
                  $this->alias.'.status',
                  $this->alias.'.created',
                  $this->alias.'.modified',
                  $this->alias.'.is_cobroke',
                  $this->alias.'.is_bt_commision',
                  $this->alias.'.bt_name',
                  $this->alias.'.bt_address',
                  $this->alias.'.bt_commission',
                  $this->alias.'.bt_commission_percentage',
                  $this->alias.'.bt_type_commission',
                  $this->alias.'.broker_commission',
                  $this->alias.'.broker_percentage',
                  $this->alias.'.broker_type_commision'
                );
            }
        }

        return $options;
    }

    function getMerge ( $data, $property_id ) {
		if( empty($data['PropertySold']) && !empty($property_id) ) {
			$value = $this->getData('first', array(
				'conditions' => array(
					'PropertySold.property_id' => $property_id,
				),
			));
			
			if( !empty($value) ) {
				$data = array_merge($data, $value);
			}
		}

		return $data;
	}

	function doSave( $data, $property_id ) {
        $result = false;
        $default_msg = __('mengubah status properti menjadi terjual atau tersewa');

        if ( !empty($data) ) {
            $this->create();
            // UNDER DEVELOPMENT
            
            // $sold_by = !empty($data['PropertySold']['sold_by_name'])?$data['PropertySold']['sold_by_name']:false;
            // $soldBy = $this->User->getData('first', array(
            //     'conditions' => array(
            //         'User.email' => $sold_by,
            //     ),
            // ), array(
            //     'status' => 'all',
            // ));

            // if( !empty($soldBy) ) {
            //     $data['PropertySold']['sold_by_id'] = $soldBy['User']['id'];
            //     $data['PropertySold']['sold_by_name'] = $soldBy['User']['full_name'];
            //     $data = $this->_callBeforeSold($data, $soldBy, $property_id);    
            // }

            if(!empty($data['PropertySold']['is_bt_commision'])){
                $this->validator()
                    ->add('bt_name', array(
                        'notempty' => array(
                            'rule' => 'notempty',
                            'message' => 'Nama BT harap diisi'
                        )
                    ))
                    ->add('bt_address', array(
                        'notempty' => array(
                            'rule' => 'notempty',
                            'message' => 'Alamat BT harap diisi'
                        )
                    ))
                    ->add('bt_commission_percentage', array(
                        'notempty' => array(
                            'rule' => 'notempty',
                            'message' => 'Komisi BT harap diisi'
                        ),
                        'comparison' => array(
                            'rule' => array('comparison', '>=', 0),
                            'message' => 'Komisi BT harus lebih besar dari 0.'
                        )
                    ))
                    ->add('bt_type_commission', array(
                        'notempty' => array(
                            'rule' => 'notempty',
                            'message' => 'Asal Komisi BT harap diisi'
                        ),
                        'multiple' => array(
                            'rule' => array('multiple', array(
                                'in'  => array('in_corp', 'out_corp'),
                            )),
                            'message' => 'Silahkan pilih tipe asal komisi BT apakah dari "Penjualan Properti" atau "Komisi Agen"'
                        )
                    ));
            }

            $this->set($data);

            if( $this->validates() ) {
                if( $this->save() ) {
                	$id = $this->id;

                	$this->updateAll(array( 
						'PropertySold.status' => 0,
					), array( 
						'PropertySold.property_id' => $property_id,
						'PropertySold.id <>' => $id,
						'PropertySold.status' => 1,
					));

                    $this->Property->set('sold', 1);
                    $this->Property->set('featured', 0);
                    $this->Property->id = $property_id;
                    $this->Property->save();

                    $result = array(
                        'msg' => sprintf(__('Berhasil %s'), $default_msg),
                        'status' => 'success',
                        'Log' => array(
                            'activity' => sprintf(__('Berhasil %s'), $default_msg),
                            'old_data' => $data,
                            'document_id' => $property_id,
                        ),
                    );

                    $sold_by_id = $this->filterEmptyField($data, 'PropertySold', 'sold_by_id');
                    $is_cobroke = $this->filterEmptyField($data, 'PropertySold', 'is_cobroke');

                    $email_sold = $this->Property->CoBrokeProperty->CoBrokeUser->sendEmailSoldCoBroke($property_id, $sold_by_id, $is_cobroke);
                    
                    if(!empty($email_sold)){
                        $result = array_merge($email_sold, $result);
                    }
                } else {
                    $result = array(
                        'msg' => sprintf(__('Gagal %s'), $default_msg),
                        'status' => 'error',
                        'Log' => array(
                            'activity' => sprintf(__('Gagal %s'), $default_msg),
                            'data' => $data,
                            'document_id' => $property_id,
                            'error' => 1,
                        ),
                    );
                }
            } else {
                $validationErrors = array();

                if(!empty($this->validationErrors)){
                    $validationErrors = array_merge($validationErrors, $this->validationErrors);
                }

                $msg = sprintf(__('Gagal %s, mohon lengkapi semua data yang diperlukan'), $default_msg);

                $result = array(
                    'msg' => $msg,
                    'status' => 'error',
                    'Log' => array(
                        'activity' => $msg,
                        'data' => $data,
                        'document_id' => $property_id,
                        'error' => 1,
                    ),
                    'validationErrors' => $validationErrors
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

    function getTotalCommission( $fromDate = false, $toDate = false, $options = false, $filter_per_property = false, $type = 'all' ) {
        
        $_admin = Configure::read('User.admin');

        $this->virtualFields['total'] = 'SUM(PropertySold.agent_commission_net)';
        $this->virtualFields['created'] = 'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\')';

        $values = array();
        $default_options = array(
            'conditions' => array(),
            'order' => false,
            'contain' => array(),
        );
        if( !empty($fromDate) && !empty($toDate) ) {
            $conditionsDate = array(
                'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') >=' => $fromDate,
                'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') <=' => $toDate,
            );
            $default_options['conditions'] = array_merge($default_options['conditions'], $conditionsDate);
        }

        if( !empty($options) ) {
            if( isset($options['conditions']) ) {
                $default_options['conditions'] = array_merge($default_options['conditions'], $options['conditions']);
            }
            if( isset($options['contain']) ) {
                $default_options['contain'] = array_merge($default_options['contain'], $options['contain']);
                $default_options['contain'] = array_unique($default_options['contain']);
            }
        }

        $admin_rumahku = Configure::read('User.Admin.Rumahku');
        $flag = !empty($admin_rumahku)?false:true;
        $values = $this->Property->getData('first', $default_options, array(
            'status' => 'sold',
            'parent' => $flag,
            'admin_mine' => $flag,
            'company' => true,
        ));

        return array(
            'total' => $this->filterEmptyField($values, 'PropertySold', 'total'),
        );
    }
}
?>