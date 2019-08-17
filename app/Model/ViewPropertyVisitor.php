<?php
App::uses('AppModel', 'Model');
/**
 * ViewPropertyVisitor Model
 *
 * @property ViewPropertyVisitor $ViewPropertyVisitor
 * @property Company $Company
 */
class ViewPropertyVisitor extends AppModel {

	public $useTable = 'view_property_visitor'; 

	public $belongsTo = array(
		'Property' => array(
			'className' => 'Property',
			'foreignKey' => 'property_id',
		),
		'Company' => array(
			'className' => 'User',
			'foreignKey' => 'company_id',
		)
	);


	function getData( $find = 'all', $options = array(), $elements = array() ){
		$user_login_id = Configure::read('User.id');
		$group_id = Configure::read('User.group_id');

		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(
				'ViewPropertyVisitor.company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			),
			'order'			=> array(
				'ViewPropertyVisitor.created' => 'ASC'
			),
			'group'			=> array()
		);

		if(!empty($group_id) && $group_id == 2){
			$default_options['conditions']['owner_id'] = $user_login_id;
		}

        return $this->merge_options($default_options, $options, $find);
	}

	function getVisitor($from = false, $to = false){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewPropertyVisitor.created, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewPropertyVisitor.created, "'.$date_format.'") <='] = $to;
		}

		$group = array();
		$periode_type = 'week';
		if(!empty($from) && !empty($to)){
			$date_diff = Common::monthDiff($from, $to);

			if($date_diff > 3){
				$periode_type = 'month';
			}
		}

		$data = $this->getData('all', array(
			'conditions' => $conditions,
			'group' => $group
		));

		if(!empty($data)){
			$temp = array();
			$temp_arr = array();
			foreach ($data as $key => $value) {
				$date 	= Common::hashEmptyField($value, 'ViewPropertyVisitor.created');
				$val 	= (int) Common::hashEmptyField($value, 'ViewPropertyVisitor.cnt');

				if($periode_type == 'month'){
					$month_date = date('Y-m', strtotime($date));

					$date_format = 'M Y';
				}else{
					$month_date = $date;

					$date_format = 'Y-m-d';
				}

				$temp_total = (int) Common::hashEmptyField($temp_arr, $month_date, 0);

				$temp_arr[$month_date] = $temp_total+$val;

				$temp[$month_date] = array(
					date($date_format, strtotime($date)),
					$temp_arr[$month_date]
				);
			}

			$data = $temp;
		}

		$fields = array(
			'Periode',
			'Pengunjung',
		);

		return array(
			'rows' 				=> $data,
			'fields' 			=> $fields,
		);
	}

	function topVisitor($from, $to){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewPropertyVisitor.created, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewPropertyVisitor.created, "'.$date_format.'") <='] = $to;
		}

		$data = $this->getData('first', array(
			'conditions' => $conditions,
			'order' => array(
				'ViewPropertyVisitor.cnt' => 'DESC'
			),
			'contain' => array(
				'Property'
			)
		));

		return $data;
	}
}