<?php
App::uses('AppModel', 'Model');
/**
 * ViewUnionRatioUserProperty Model
 *
 * @property ViewUnionRatioUserProperty $ViewUnionRatioUserProperty
 * @property Company $Company
 */
class ViewUnionRatioUserProperty extends AppModel {

	public $useTable = 'view_union_ratio_user_property'; 

	public $belongsTo = array(
		'UserCompany' => array(
			'className' => 'UserCompany',
			'foreignKey' => false,
			'conditions' => array(
				'UserCompany.user_id = ViewUnionRatioUserProperty.user_company_id'
			),
		)
	);


	function getData( $find = 'all', $options = array(), $elements = array() ){
		$user_login_id = Configure::read('User.id');
		$group_id = Configure::read('User.group_id');

		$default_options = array(
			'contain'		=> array(), 
			'conditions'	=> array(
				'ViewUnionRatioUserProperty.user_company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			),
			'order'			=> array(
				'ViewUnionRatioUserProperty.date' => 'ASC'
			),
			'group'			=> array()
		);

        return $this->merge_options($default_options, $options, $find);
	}

	function getRatio($from = false, $to = false){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewUnionRatioUserProperty.date, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewUnionRatioUserProperty.date, "'.$date_format.'") <='] = $to;
		}

		$data = $this->getData('all', array(
			'conditions' => $conditions,
		));

		$total_property = $total_agen = 0;
		if(!empty($data)){
			$fields = array(
				'users',
				'properties'
			);

			$temp = array();
			foreach ($data as $key => $value) {
				$name 	= Common::hashEmptyField($value, 'ViewUnionRatioUserProperty.name');
				$date 	= Common::hashEmptyField($value, 'ViewUnionRatioUserProperty.date');
				$val 	= (int) Common::hashEmptyField($value, 'ViewUnionRatioUserProperty.value');

				$month_ratio = date('Y-m', strtotime($date));

				$temp_total = Common::hashEmptyField($temp, $month_ratio.'.'.$name, 0);

				$temp_total += $val;

				if($name == 'users'){
					$total_agen += $val;
				}else{
					$total_property += $val;
				}

				$temp[$month_ratio][$name] = $temp_total;
			}

			$temp2 = array();
			foreach ($temp as $date => $value) {
				$temp2[] = array(
					Common::formatDate($date, 'F Y'),
					Common::hashEmptyField($value, 'users', 0),
					Common::hashEmptyField($value, 'properties', 0),
				);
			}

			$data = $temp2;
		}

		$fields = array(
			'Periode',
			'Agen',
			'Properti'
		);

		return array(
			'rows' 				=> $data,
			'fields' 			=> $fields,
			'total_property' 	=> $total_property,
			'total_agen' 		=> $total_agen,
		);
	}
}