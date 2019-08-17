<?php
App::uses('AppModel', 'Model');
/**
 * ViewChartAddListing Model
 *
 * @property ViewChartAddListing $ViewChartAddListing
 * @property Company $Company
 */
class ViewChartAddListing extends AppModel {

	public $useTable = 'view_chart_add_listing'; 

	public $belongsTo = array(
		'Company' => array(
			'className' => 'User',
			'foreignKey' => 'company_id', 
		), 
	);


	function getData( $find = 'all', $options = array(), $elements = array() ){
		$user_login_id = Configure::read('User.id');
		$group_id = Configure::read('User.group_id');

		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(
				'ViewChartAddListing.company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			),
			'order'			=> array(
				'ViewChartAddListing.created' => 'ASC', 
			),
			'field' => array()
		);

		if(!empty($group_id) && $group_id == 2){
			$default_options['conditions']['user_id'] = $user_login_id;
		}

        return $this->merge_options($default_options, $options, $find);
	}

	function dataListing($from = false, $to = false, $find = 'all'){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewChartAddListing.created, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewChartAddListing.created, "'.$date_format.'") <='] = $to;
		}

		$data = $this->getData($find, array(
			'conditions' => $conditions,
		));

		if($find == 'paginate'){
			return $data;
		}else{
			$total = 0;
			$rows = array();
			if(!empty($data)){
				$temp_arr = $temp = array();
				foreach ($data as $key => $value) {
					$month_listing = Common::hashEmptyField($value, 'ViewChartAddListing.month_listing');
					$cnt = (int) Common::hashEmptyField($value, 'ViewChartAddListing.cnt');

					$total += $cnt;

					$temp_total = (int) Common::hashEmptyField($temp_arr, $month_listing, 0);

					$temp_arr[$month_listing] = $temp_total + $cnt;

					$temp[$month_listing] = array(
						$month_listing,
						$temp_arr[$month_listing]
					);
				}

				$rows = $temp;
			}

			return array(
				'rows' => $rows,
				'total' => $total
			);
		}
	}


	function getSumListing($from = false, $to = false){
		$this->virtualFields['sum_cnt'] = 'SUM(ViewChartAddListing.cnt)';

		$options = $this->dataListing($from, $to, 'paginate');
		
		$result = $this->getData('first', $options); 
		
		return Common::hashEmptyField($result, 'ViewChartAddListing.sum_cnt', 0);
	}
}