<?php
class UserClientMasterReference extends AppModel {
    var $name = 'UserClientMasterReference';
    
    function __construct($id = false, $table = null, $ds = null) {
        parent::__construct($id, $table, $ds);
    }

    function getData( $find = 'all', $options = array() ,$elements = array() ){
        $status = Common::hashEmptyField($elements, 'status', 'active');

        $default_options = array(
            'conditions'=> array(),
            'order'=> array(
                'UserClientMasterReference.name',
                'UserClientMasterReference.id',
            ),
            'fields' => array(),
            'contain' => array(),
            'group' => array(),
        );


        switch($status){
            case 'active' : 
                $default_options['conditions']['UserClientMasterReference.status'] = 1;
                break;
            case 'inactive':
                $default_options['conditions']['UserClientMasterReference.status'] = 0;
                break;
        }

        return $this->merge_options($default_options, $options, $find);
    }

}