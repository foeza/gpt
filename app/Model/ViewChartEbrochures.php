<?php
App::uses('AppModel', 'Model');
/**
 * ViewChartEbrochures Model
 *
 * @property ViewChartEbrochures $ViewChartEbrochures
 * @property Company $Company
 */
class ViewChartEbrochures extends AppModel {

	public $useTable = 'view_chart_ebrochures'; 

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


	function getData( $find = 'all', $options = array(), $elements = array() ){
		$user_login_id = Configure::read('User.id');
		$group_id = Configure::read('User.group_id');

		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(
				'ViewChartEbrochures.company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			),
			'order'			=> array(
				'ViewChartEbrochures.cnt' => 'DESC', 
			),
		);

		if(!empty($group_id) && $group_id == 2){
			$default_options['conditions']['user_id'] = $user_login_id;
		}

        return $this->merge_options($default_options, $options, $find);
	}

	function getRevenue($from = false, $to = false){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewChartEbrochures.created, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewChartEbrochures.created, "'.$date_format.'") <='] = $to;
		}

		$periode_type = 'week';
		if(!empty($from) && !empty($to)){
			$date_diff = Common::monthDiff($from, $to);

			if($date_diff > 3){
				$periode_type = 'month';
			}
		}

		$revenue = $this->getData('all', array(
			'conditions' => $conditions,
		));

		$fields = array(
			'Periode',
			'Jumlah'
		);

		$total_ebrosur = 0;
		if(!empty($revenue)){
			$temp_arr = $temp = array();
			foreach ($revenue as $key => $value) {
				$created 	= Common::hashEmptyField($value, 'ViewChartEbrochures.created');
				$cnt 		= (int) Common::hashEmptyField($value, 'ViewChartEbrochures.cnt', 0);

				$total_ebrosur += $cnt;

				if($periode_type == 'month'){
					$month_date = date('Y-m', strtotime($created));

					$date_format = 'M Y';
				}else{
					$month_date = $created;

					$date_format = 'Y-m-d';
				}

				$temp_total = (int) Common::hashEmptyField($temp_arr, $month_date, 0);

				$temp_arr[$month_date] = $temp_total+$cnt;

				$temp[$month_date] = array(
					date($date_format, strtotime($created)),
					$temp_arr[$month_date]
				);
			}

			$revenue = $temp;
		}

		return array(
			'rows' 				=> $revenue,
			'fields' 			=> $fields,
			'total_ebrosur' 	=> $total_ebrosur
		);
	}

	function prevRevenue($date_from, $date_to){
		$date_format = '%Y-%m-%d';
		$date_diff = Common::monthDiff($date_from, $date_to);

		$prev_min_target = date('Y-m-01', strtotime($date_from.' -'.$date_diff.' month'));
		$prev_max_target = date('Y-m-t', strtotime($prev_min_target.' +'.($date_diff-1).' month'));

		$conditions = array();
		if(!empty($prev_min_target)){
			$conditions['DATE_FORMAT(ViewChartEbrochures.created, "'.$date_format.'") >='] = $prev_min_target;
		}
		if(!empty($prev_max_target)){
			$conditions['DATE_FORMAT(ViewChartEbrochures.created, "'.$date_format.'") <='] = $prev_max_target;
		}

		$this->virtualFields['sum_cnt'] = 'SUM(ViewChartEbrochures.cnt)';

		$revenue = $this->getData('first', array(
			'conditions' => $conditions,
		));

		if(!empty($revenue)){
			return array(
				'prev_cnt' => Common::hashEmptyField($revenue, 'ViewChartEbrochures.sum_cnt'),
			);
		}else{
			return array();
		}
	}
}