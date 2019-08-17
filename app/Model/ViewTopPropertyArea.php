<?php
App::uses('AppModel', 'Model');
/**
 * ViewTopPropertyArea Model
 *
 * @property ViewTopPropertyArea $ViewTopPropertyArea
 * @property Company $Company
 */
class ViewTopPropertyArea extends AppModel {

	public $useTable = 'view_top_property_area'; 

	public $belongsTo = array(
		'Company' => array(
			'className' => 'User',
			'foreignKey' => 'company_id', 
		), 
	);

	public function _callRefineParams( $data = '', $default_options = false ){
		$region_name = Common::hashEmptyField($data, 'named.region_name', false, array(
            'addslashes' => true,
        ));
        $city_name = Common::hashEmptyField($data, 'named.city_name', false, array(
            'addslashes' => true,
        ));
        $subarea_name = Common::hashEmptyField($data, 'named.subarea_name', false, array(
            'addslashes' => true,
        ));
        $zip = Common::hashEmptyField($data, 'named.zip', false, array(
            'addslashes' => true,
        ));
    	$date_from = Common::hashEmptyField($data, 'named.date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = Common::hashEmptyField($data, 'named.date_to', false, array(
            'addslashes' => true,
        ));

        if(!empty($region_name)){
        	$default_options['conditions']['ViewTopPropertyArea.region_name LIKE'] = '%'.$region_name.'%';
        }
        if(!empty($city_name)){
        	$default_options['conditions']['ViewTopPropertyArea.city_name LIKE'] = '%'.$city_name.'%';
        }
        if(!empty($subarea_name)){
        	$default_options['conditions']['ViewTopPropertyArea.subarea_name LIKE'] = '%'.$subarea_name.'%';
        }
        if(!empty($zip)){
        	$default_options['conditions']['ViewTopPropertyArea.zip LIKE'] = '%'.$zip.'%';
        }

		if( !empty($date_from) ) {
        	$default_options['conditions']["DATE_FORMAT(ViewTopPropertyArea.created, '%Y-%m-%d') >="] = $date_from;
	        
			if( !empty($date_to) ) {
	        	$default_options['conditions']["DATE_FORMAT(ViewTopPropertyArea.created, '%Y-%m-%d') <="] = $date_to;
	        }
        }

		return $default_options;
	}


	function getData( $find = 'all', $options = array(), $elements = array() ){
		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(
				'ViewTopPropertyArea.company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			),
			'order'			=> array(
				'ViewTopPropertyArea.cnt' => 'DESC', 
			),
		);

        return $this->merge_options($default_options, $options, $find);
	}

	function topSearch($from = false, $to = false, $limit = 5, $find = 'all'){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewTopPropertyArea.created, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewTopPropertyArea.created, "'.$date_format.'") <='] = $to;
		}

		$this->virtualFields['sum_cnt'] = 'SUM(ViewTopPropertyArea.cnt)';
		$sales = $this->getData($find, array(
			'conditions' => $conditions,
			'limit' => $limit,
			'group' => array(
				'ViewTopPropertyArea.custom_name'
			),
			'order' => array(
				'ViewTopPropertyArea.sum_cnt' => 'DESC'
			)
		));

		return $sales;
	}
}