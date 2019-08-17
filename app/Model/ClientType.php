<?php
class ClientType extends AppModel {
	var $name = 'ClientType';

    function getData( $find = 'all', $options = array(), $elements = array() ) {
        $lastupdated = $this->filterEmptyField($elements, 'lastupdated');
        $default_options = array(
            'conditions'=> array(),
            'contain' => array(),
            'fields'=> array(),
            'group'=> array(),
        );

        if( !empty($lastupdated) ) {
            $default_options['conditions']['ClientType.modified >'] = $lastupdated;
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
            if(!empty($options['fields'])){
                $default_options['fields'] = $options['fields'];
            }
            if(!empty($options['limit'])){
                $default_options['limit'] = $options['limit'];
            }
            if(!empty($options['group'])){
                $default_options['group'] = $options['group'];
            }
        }

        $default_options = $this->_callFieldForAPI($find, $default_options);

        if( $find == 'conditions' && !empty($default_options['conditions']) ) {
            $result = $default_options['conditions'];
        } else if( $find == 'paginate' ) {
            if( empty($default_options['limit']) ) {
                $default_options['limit'] = Configure::read('__Site.config_admin_pagination');
            }
            
            $result = $default_options;
        } else {
            $result = $this->find($find, $default_options);
        }
        
        return $result;
    }

    function getList () {
        return $this->getData('list', array(
            'order' => array(
                'ClientType.order' => 'ASC',
                'ClientType.id' => 'ASC',
            ),
    	));
    }

    function _callFieldForAPI ( $find, $options ) {
        if( !in_array($find, array( 'list', 'count', 'conditions' )) ) {
            $rest_api = Configure::read('Rest.token');

            if( !empty($rest_api) ) {
                $options['fields'] = array(
                  'ClientType.id',
                  'ClientType.name',
                );
            }
        }

        return $options;
    }

    function getMerge( $data, $id ){
        if( !empty($id) ) {
            $value = $this->getData('first', array(
                'conditions' => array(
                    'ClientType.id' => $id
                ),
            ));

            if(!empty($value) ){
                $data = array_merge($data, $value);
            }
        }

        return $data;
    }
}
?>