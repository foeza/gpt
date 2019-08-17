<?php
App::uses('AppModel', 'Model');
/**
 * ViewTopSoldPropertyArea Model
 *
 * @property ViewTopSoldPropertyArea $ViewTopSoldPropertyArea
 * @property Company $Company
 */
class ViewTopSoldPropertyArea extends AppModel {

	public $useTable = 'view_top_sold_property_area'; 

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
    	$date_from = Common::hashEmptyField($data, 'named.date_from', false, array(
            'addslashes' => true,
        ));
        $date_to = Common::hashEmptyField($data, 'named.date_to', false, array(
            'addslashes' => true,
        ));

        if(!empty($region_name)){
        	$default_options['conditions']['ViewTopSoldPropertyArea.region_name LIKE'] = '%'.$region_name.'%';
        }
        if(!empty($city_name)){
        	$default_options['conditions']['ViewTopSoldPropertyArea.city_name LIKE'] = '%'.$city_name.'%';
        }
        if(!empty($subarea_name)){
        	$default_options['conditions']['ViewTopSoldPropertyArea.subarea_name LIKE'] = '%'.$subarea_name.'%';
        }

		if( !empty($date_from) ) {
        	$default_options['conditions']["DATE_FORMAT(ViewTopSoldPropertyArea.created, '%Y-%m-%d') >="] = $date_from;
	        
			if( !empty($date_to) ) {
	        	$default_options['conditions']["DATE_FORMAT(ViewTopSoldPropertyArea.created, '%Y-%m-%d') <="] = $date_to;
	        }
        }

		return $default_options;
	}


	function getData( $find = 'all', $options = array(), $elements = array() ){
		$this->virtualFields['cnt'] = 'SUM(ViewTopSoldPropertyArea.cnt)';
		
		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(
				'ViewTopSoldPropertyArea.custom_name NOT' => null,
				'ViewTopSoldPropertyArea.company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			),
			'group' => array(
				'ViewTopSoldPropertyArea.custom_name'
			),
			'order'			=> array(
				'ViewTopSoldPropertyArea.cnt' => 'DESC', 
			),
		);

        return $this->merge_options($default_options, $options, $find);
	}

	function topSold($from = false, $to = false, $limit = 5, $find = 'all'){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewTopSoldPropertyArea.created, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewTopSoldPropertyArea.created, "'.$date_format.'") <='] = $to;
		}

		$sales = $this->getData($find, array(
			'conditions' => $conditions,
			'limit' => $limit
		));

		return $sales;
	}
}