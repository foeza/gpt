<?php
App::uses('AppModel', 'Model');
/**
 * ViewTopAgent Model
 *
 * @property ViewTopAgent $ViewTopAgent
 * @property Company $Company
 */
class ViewTopAgent extends AppModel {

	public $useTable = 'view_top_agents'; 

	public $belongsTo = array(
		'Company' => array(
			'className' => 'User',
			'foreignKey' => 'company_id', 
		), 
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'id', 
		), 
		'UserProfile' => array(
			'className' => 'UserProfile',
			'foreignKey' => false,
			'conditions' => array(
				'ViewTopAgent.id = UserProfile.user_id'
			) 
		), 
	);

	public function _callRefineParams( $data = '', $default_options = false ){
		$full_name = Common::hashEmptyField($data, 'named.full_name', false, array(
            'addslashes' => true,
        ));
        $email = Common::hashEmptyField($data, 'named.email', false, array(
            'addslashes' => true,
        ));
        $no_hp = Common::hashEmptyField($data, 'named.no_hp', false, array(
            'addslashes' => true,
        ));
    	$date_from = Common::hashEmptyField($data, 'named.date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = Common::hashEmptyField($data, 'named.date_to', false, array(
            'addslashes' => true,
        ));

        if(!empty($full_name)){
        	$default_options['conditions']['ViewTopAgent.full_name LIKE'] = '%'.$full_name.'%';
        }

        if(!empty($email)){
        	$default_options['conditions']['User.email LIKE'] = '%'.$email.'%';
        	$default_options['contain'][] = 'User';
        }

        if(!empty($no_hp)){
        	$default_options['conditions']['UserProfile.no_hp LIKE'] = '%'.$no_hp.'%';
        	$default_options['contain'][] = 'UserProfile';
        }

		if( !empty($date_from) ) {
        	$default_options['conditions']["DATE_FORMAT(ViewTopAgent.sold_date, '%Y-%m-%d') >="] = $date_from;
	        
			if( !empty($date_to) ) {
	        	$default_options['conditions']["DATE_FORMAT(ViewTopAgent.sold_date, '%Y-%m-%d') <="] = $date_to;
	        }
        }

		return $default_options;
	}


	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(
				'ViewTopAgent.company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			),
			'order'			=> array(
				'ViewTopAgent.cnt' => 'DESC', 
			),
		);

        return $this->merge_options($default_options, $options, $find);
	}

	function topAgent($from = false, $to = false, $limit = 5, $find = 'all'){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewTopAgent.sold_date, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewTopAgent.sold_date, "'.$date_format.'") <='] = $to;
		}

		$this->virtualFields['sum_cnt'] = 'SUM(ViewTopAgent.cnt)';
		$sales = $this->getData($find, array(
			'conditions' => $conditions,
			'group' => array(
				'ViewTopAgent.full_name'
			),
			'order' => array(
				'ViewTopAgent.sum_cnt' => 'DESC'
			),
			'limit' => $limit
		));

		return $sales;
	}
}