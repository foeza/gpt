<?php
App::uses('AppModel', 'Model');
/**
 * ViewUnionPropertyLeads Model
 *
 * @property ViewUnionPropertyLeads $ViewUnionPropertyLeads
 * @property Company $Company
 */
class ViewUnionPropertyLeads extends AppModel {

	public $useTable = 'view_union_property_leads'; 

	public $belongsTo = array(
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
				'ViewUnionPropertyLeads.company_id' => Configure::read('Config.Company.data.UserCompany.user_id')
			),
			'order'			=> array(
				'ViewUnionPropertyLeads.created' => 'ASC'
			),
			'group'			=> array()
		);

		if(!empty($group_id) && $group_id == 2){
			$default_options['conditions']['owner_id'] = $user_login_id;
		}

        return $this->merge_options($default_options, $options, $find);
	}

	function getLeads($from = false, $to = false){
		$date_format = '%Y-%m-%d';

		$conditions = array();
		if(!empty($from)){
			$conditions['DATE_FORMAT(ViewUnionPropertyLeads.created, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions['DATE_FORMAT(ViewUnionPropertyLeads.created, "'.$date_format.'") <='] = $to;
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

			$date_format = 'Y-m-d';

			foreach ($data as $key => $value) {
				$type_lead 	= Common::hashEmptyField($value, 'ViewUnionPropertyLeads.type_lead');
				$date 		= Common::hashEmptyField($value, 'ViewUnionPropertyLeads.created');
				$val 		= (int) Common::hashEmptyField($value, 'ViewUnionPropertyLeads.cnt', 0);

				$month_date = $date;

				$temp_total = (int) Common::hashEmptyField($temp, $month_date.'.'.$type_lead, 0);

				$temp[$month_date][$type_lead] = $temp_total+$val;
			}

			$temp2 = array();
			foreach ($temp as $date => $value) {
				$temp2[] = array(
					Common::formatDate($date, $date_format),
					Common::hashEmptyField($value, 'leads', 0),
					Common::hashEmptyField($value, 'hot_leads', 0),
				);
			}

			$data = $temp2;
		}

		$fields = array(
			'Periode',
			'Leads',
			'Hot Leads'
		);

		return array(
			'rows' 				=> $data,
			'fields' 			=> $fields,
		);
	}
}