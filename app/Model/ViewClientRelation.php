<?php
App::uses('AppModel', 'Model');
/**
 * ViewTopAgent Model
 *
 * @property ViewTopAgent $ViewTopAgent
 * @property Company $Company
 */
class ViewClientRelation extends AppModel {

	public $useTable = 'view_client_relations';

	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'agent_id', 
		), 
		'ClientType' => array(
            'className' => 'ClientType',
            'foreignKey' => 'client_type_id'
        ),
	); 

	public function _callRefineParams( $data = '', $default_options = false ){
		$date_from = Common::hashEmptyField($data, 'named.date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = Common::hashEmptyField($data, 'named.date_to', false, array(
            'addslashes' => true,
        ));

		if( !empty($date_from) ) {
        	$default_options['conditions']["DATE_FORMAT(ViewClientRelation.client_created, '%Y-%m-%d') >="] = $date_from;
	        
			if( !empty($date_to) ) {
	        	$default_options['conditions']["DATE_FORMAT(ViewClientRelation.client_created, '%Y-%m-%d') <="] = $date_to;
	        }
        }

		return $default_options;
	}

	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(),
			'order'			=> array(),
		);

        return $this->merge_options($default_options, $options, $find);
	}
}
?>