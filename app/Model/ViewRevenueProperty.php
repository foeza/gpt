<?php
App::uses('AppModel', 'Model');
/**
 * ViewRevenueProperty Model
 *
 * @property ViewRevenueProperty $ViewRevenueProperty
 * @property Company $Company
 */
class ViewRevenueProperty extends AppModel {

	public $useTable = 'view_revenue_property'; 

	public $belongsTo = array(
		'Company' => array(
			'className' => 'User',
			'foreignKey' => 'company_id', 
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'owner_id', 
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
        	$default_options['conditions']['ViewRevenueProperty.region_name LIKE'] = '%'.$region_name.'%';
        }
        if(!empty($city_name)){
        	$default_options['conditions']['ViewRevenueProperty.city_name LIKE'] = '%'.$city_name.'%';
        }
        if(!empty($subarea_name)){
        	$default_options['conditions']['ViewRevenueProperty.subarea_name LIKE'] = '%'.$subarea_name.'%';
        }

		if( !empty($date_from) ) {
        	$default_options['conditions']["DATE_FORMAT(ViewRevenueProperty.sold_date, '%Y-%m-%d') >="] = $date_from;
	        
			if( !empty($date_to) ) {
	        	$default_options['conditions']["DATE_FORMAT(ViewRevenueProperty.sold_date, '%Y-%m-%d') <="] = $date_to;
	        }
        }

		return $default_options;
	}


	function getData( $find = 'all', $options = array(), $elements = array() ){
		$user_login_id = Configure::read('User.id');
		$group_id = Configure::read('User.group_id');

		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(
				'ViewRevenueProperty.company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			),
			'order'			=> array(
				'ViewRevenueProperty.sold_date' => 'ASC', 
			),
			'group' => array()
		);

		if(!empty($group_id) && $group_id == 2){
			$default_options['conditions']['owner_id'] = $user_login_id;
		}

        return $this->merge_options($default_options, $options, $find);
	}

	function getRevenue($from = false, $to = false, $type = 'komisi'){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewRevenueProperty.sold_date, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewRevenueProperty.sold_date, "'.$date_format.'") <='] = $to;
		}

		$revenue = $this->getData('all', array(
			'conditions' => $conditions,
		));

		$field = 'agent_commission';
		$fields = array(
			'Periode',
			'Komisi'
		);
		if($type == 'unit'){
			$field = 'cnt';

			$fields = array(
				'Periode',
				'Unit'
			);
		}

		$total_revenue = $total_listing = $total_commission = 0;

		if(!empty($revenue)){
			$temp_arr = $temp = array();
			foreach ($revenue as $key => $value) {
				$sold_date 			= Common::hashEmptyField($value, 'ViewRevenueProperty.sold_date');
				$cnt 				= (int) Common::hashEmptyField($value, 'ViewRevenueProperty.cnt', 0);
				$agent_commission 	= Common::hashEmptyField($value, 'ViewRevenueProperty.agent_commission', 0);
				$total_revenue_curr	= Common::hashEmptyField($value, 'ViewRevenueProperty.total_revenue', 0);

				$temp_total = Common::hashEmptyField($temp_arr, $sold_date, 0);

				if($type == 'komisi'){
					$temp_arr[$sold_date] = $temp_total+$agent_commission;

					$temp[$sold_date] = array(
						$sold_date,
						array(
							'value' => $temp_arr[$sold_date],
							'alias' => 'Rp. '.Common::getFormatPrice($temp_arr[$sold_date])
						)
					);
				}else{
					$temp_arr[$sold_date] = $temp_total+$cnt;
					
					$temp[$sold_date] = array(
						$sold_date,
						array(
							'value' => $temp_arr[$sold_date],
							'alias' => $temp_arr[$sold_date].' listing'
						)
					);
				}

				$total_listing += $cnt;
				$total_commission += $agent_commission;
				$total_revenue += $total_revenue_curr;
			}

			$revenue = $temp;
		}

		return array(
			'rows' 				=> $revenue,
			'fields' 			=> $fields,
			'total_listing' 	=> $total_listing,
			'total_commission' 	=> $total_commission,
			'total_revenue' 	=> $total_revenue,
		);
	}


	function prevRevenue($date_from, $date_to){
		$date_format = '%Y-%m-%d';
		$date_diff = Common::monthDiff($date_from, $date_to);

		$prev_min_target = date('Y-m-01', strtotime($date_from.' -'.$date_diff.' month'));
		$prev_max_target = date('Y-m-t', strtotime($prev_min_target.' +'.($date_diff-1).' month'));

		$conditions = array();
		if(!empty($prev_min_target)){
			$conditions['DATE_FORMAT(ViewRevenueProperty.sold_date, "'.$date_format.'") >='] = $prev_min_target;
		}
		if(!empty($prev_max_target)){
			$conditions['DATE_FORMAT(ViewRevenueProperty.sold_date, "'.$date_format.'") <='] = $prev_max_target;
		}

		$this->virtualFields['sum_commission'] = 'SUM(ViewRevenueProperty.agent_commission)';
		$this->virtualFields['sum_listing'] = 'SUM(ViewRevenueProperty.cnt)';
		$this->virtualFields['sum_revenue'] = 'SUM(ViewRevenueProperty.total_revenue)';

		$revenue = $this->getData('first', array(
			'conditions' => $conditions,
		));

		if(!empty($revenue)){
			return array(
				'prev_commission' => Common::hashEmptyField($revenue, 'ViewRevenueProperty.sum_commission'),
				'prev_listing' => Common::hashEmptyField($revenue, 'ViewRevenueProperty.sum_listing'),
				'prev_revenue' => Common::hashEmptyField($revenue, 'ViewRevenueProperty.sum_revenue'),
			);
		}else{
			return array();
		}
	}
}