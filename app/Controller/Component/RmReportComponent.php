<?php
/*
	- Performance
	- Messages
	- Transaksi
	- User Membership
*/
class RmReportComponent extends Component {
	public $components = array(
		'RmCommon', 'RmProperty',
		'RmImage', 'RmKpr',
		'Export.Export',
		'PhpExcel.PhpExcel',
		'Rest.Rest', 'RmUser',
	);

	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function _callDataAPIConverter ( $data ) {
		$headers = array();

		if( $this->Rest->isActive() && !empty($data) ) {
			$result = array();

			foreach ($data as $key => $value) {
				$text = $this->RmCommon->filterEmptyField($value, 'text', false, null);
				$min_width = $this->RmCommon->filterEmptyField($headers, $key, 'width', false, null);
				$lbl_width = strlen($key);
				$width = strlen($text);

				if( $min_width > $width ) {
					$width = $min_width;
				}
				if( $lbl_width > $width ) {
					$width = $lbl_width;
				}

				$result[] = $text;
				$headers[] = array(
					'label' => $key,
					'width' => $width,
					'style' => $this->RmCommon->filterEmptyField($value, 'style', false, null),
				);
			}
		} else {
			$result = $data;
		}

		$data = array(
			'headers' => $headers,
			'data' => $result,
		);

		return $data;
	}

	// - Performance
	function _callBeforeViewPerformance ( $values ) {
		if( !empty($values) ) {
			foreach ($values as $key => $value) {
				$data = $this->RmCommon->filterEmptyField($value, 'Report', 'data', false, array(
					'type' =>  'unserialize',
				));
				$data = $this->controller->Report->_callProcessData($data);
				$value['Specification'] = $this->RmCommon->filterEmptyField($data, 'Specification');
				$values[$key] = $value;
			}
		}

		$this->controller->set(array(
			'values' => $values, 
		));
	}

	function _callAddBeforeViewPerformance () {
		$companies =  $this->RmCommon->_callCompanies('all');
		$pics =  $this->RmCommon->_callPIC();

		$this->RmCommon->_layout_file('select2');
		$this->controller->set(array(
			'companies' => $companies, 
			'pics' => $pics, 
		));
	}

	function _callDataPerformance ( $params, $offset = false, $limit = 30, $type = false ) {
		$this->controller->loadModel('UserCompanyConfig');

		if( !empty($params) ) {
			$sort_table      = Common::hashEmptyField($this->controller->params->params, 'named', array());
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);

			if (!empty($sort_table)) {
				$params['named'] = array_merge($params['named'], $sort_table);
			}

        } else {
        	$params = $this->controller->params;
        }

        $companyData = Configure::read('Config.Company.data');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

		$options = array(
			'order' => array(
				'UserCompanyConfig.id' => 'DESC',
			),
            'offset' => $offset,
			'limit' => $limit,
		);

		$this->controller->User->UserCompanyConfig->virtualFields['duration_year'] = 'timestampdiff(YEAR, UserCompanyConfig.live_date, UserCompanyConfig.end_date)';
		$this->controller->User->UserCompanyConfig->virtualFields['duration_month'] = 'timestampdiff(MONTH, UserCompanyConfig.live_date, UserCompanyConfig.end_date)';

		$options = $this->controller->User->UserCompanyConfig->_callRefineParams($params, $options);
		
		$this->controller->paginate	= $this->controller->User->UserCompanyConfig->getData('paginate', $options, array(
			'mine' => true,
		));

		$data = $this->controller->paginate('UserCompanyConfig');
		$result = array();

		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'UserCompanyConfig', 'id');

		if( !empty($data) ) {
			App::uses('HtmlHelper', 'View/Helper');
       		$this->Html = new HtmlHelper(new View(null));

			foreach ($data as $key => $value) {
				$value = $this->controller->UserCompanyConfig->getMergeList($value, array(
					'contain' => array(
						'User',
						'UserCompany' => array(
							'foreignKey' => 'user_id',
							'primaryKey' => 'user_id',
						),
						'PIC' => array(
							'uses' => 'User',
							'foreignKey' => 'pic_sales_id',
							'primaryKey' => 'id',
							'elements' => array(
								'status' => 'all',
							),
						),
					),
				));

				$value = $this->controller->User->UserCompany->getMergeList($value, array(
					'contain' => array(
						'Region' => array(
							'cache' => true,
						),
						'City' => array(
							'cache' => true,
						),
					),
				));
				$value = $this->controller->User->getMergeList($value, array(
					'contain' => array(
						'Group',
					),
				));

				$id = $this->RmCommon->filterEmptyField($value, 'User', 'id', null);
				$group_id = $this->RmCommon->filterEmptyField($value, 'User', 'group_id', null);
				
				$group_name = $this->RmCommon->filterEmptyField($value, 'Group', 'name', 'N/A');
				$group_name = $this->RmCommon->filterEmptyField($value, 'Group', 'alias', $group_name);

				switch ($group_id) {
					case '4':
						$principle_id = $this->controller->User->getAgents($id, true, 'list', false, array(
							'role' => 'principle',
						));
						break;
					
					default:
						$principle_id = $id;
						break;
				}

				$value = $this->RmCommon->dataConverter($value,array(
					'date' => array(
						'UserCompanyConfig' => array(
							'contract_date',
							'live_date',
							'end_date',
						),
					),
				), true);

				$name           = Common::hashEmptyField($value, 'UserCompany.name', '-');

				$duration_year  = Common::hashEmptyField($value, 'UserCompanyConfig.duration_year', '-');
				$duration_month = Common::hashEmptyField($value, 'UserCompanyConfig.duration_month', null);
				$contract_date  = Common::hashEmptyField($value, 'UserCompanyConfig.contract_date', '-');
				$domain         = Common::hashEmptyField($value, 'UserCompanyConfig.domain', '-');
				$live_date      = Common::hashEmptyField($value, 'UserCompanyConfig.live_date', '-');
				$end_date       = Common::hashEmptyField($value, 'UserCompanyConfig.end_date', '-');

				$principle_name = Common::hashEmptyField($value, 'User.full_name', '-');

				$pic_name       = Common::hashEmptyField($value, 'PIC.full_name', '-');

				$region         = Common::hashEmptyField($value, 'Region.name', null);

				$city           = Common::hashEmptyField($value, 'City.name', '-');				

				// if( is_array($principle_id) ) {
				// 	$agent_id = array_merge($agent_id, $principle_id);
				// } else {
				// 	$agent_id[] = $principle_id;
				// }
				
				// param search period goes here
				$param_search['named'] = Common::hashEmptyField($params, 'named');

				$activities_from = Common::hashEmptyField($param_search, 'named.date_from');
				$activities_to   = Common::hashEmptyField($param_search, 'named.date_to');

    			// ====== jml user selain agent ======
    			$opt_non_agent = array(
    				'conditions' => array(
    					'User.parent_id'   => $principle_id,
    					
    				),
    			);
    			$non_agent_count = $this->controller->User->getData( 'count', $opt_non_agent, array(
    				'status' => 'active',
    				'role'   => 'non-agent',
    			));

				// ====== jml agent aktif ======
    			$agent_count = $this->controller->User->getAgents( $principle_id, true, 'count' );

    			// ====== jml listing tayang ======
    			$prop_publish_cnt = $this->controller->User->Property->_callPrinciplePropertyCount($principle_id, 'active-pending', $param_search);

    			// ====== jml listing sold ======
    			$prop_sold_cnt = $this->controller->User->Property->_callPrinciplePropertyCount($principle_id, 'sold', $param_search);
    			
    			// ====== jml listing offline (inactive) ======
    			$prop_inactive_cnt = $this->controller->User->Property->_callPrinciplePropertyCount($principle_id, 'inactive', $param_search);
    			
    			// ====== jml ebrosur ======
    			$ebrosur_count = $this->controller->User->UserCompanyEbrochure->_callPrincipleCount($principle_id, 'active', $param_search);

    			// ====== jml pesan ======
    			$message_count = $this->controller->User->Property->Message->_callPrincipleCount($principle_id, 'active', $param_search);

				// ====== jml submit kpr ======
				$kpr_count = $this->controller->User->Kpr->_callPrincipleCount($principle_id, 'active', $param_search);

				// ====== daily activities option ======
				$logViewOptions = array(
					'conditions' => array(
						'LogView.parent_id' => $principle_id,
						'DATE_FORMAT(LogView.created, \'%Y-%m-%d\') >=' => $activities_from,
						'DATE_FORMAT(LogView.created, \'%Y-%m-%d\') <=' => $activities_to,
					),
					'order'=> false,
				);
				$value = $this->controller->User->LogView->_callGetDataView($value, $logViewOptions);

				// total daily activities
				$TotalActivity = Common::hashEmptyField($value, 'TotalActivity');
				$last_login    = Common::hashEmptyField($value, 'LogLogin.created', false, array(
					'date' => 'd M Y H:i',
				));
				$log_view      = Common::hashEmptyField($value, 'LogView.created', false, array(
					'date' => 'd M Y H:i',
				));

				// ====== INIT DATA COUNT RATIO =======
				$cnt_value = array(
					'non_agent_count'   => $non_agent_count,
					'agent_count'       => $agent_count,
					'prop_sold_cnt'     => $prop_sold_cnt,
					'prop_inactive_cnt' => $prop_inactive_cnt,
					'prop_publish_cnt'  => $prop_publish_cnt,
					'ebrosur_count'     => $ebrosur_count,
					'message_count'     => $message_count,
					'kpr_count'         => $kpr_count,
					'daily_activity'    => $TotalActivity,
					// kalau ada tambahan, tambah di sini
				);

				// ====== ratio listing tayang
    			if( !empty($agent_count) ) {
    				$publish_prop_ratio = $this->_calculateData( $cnt_value , 'prop_publish_cnt');
    			} else {
    				$publish_prop_ratio = 0;
    			}

				// ====== ratio ebrosur
    			if( !empty($agent_count) ) {
    				$ebrosur_ratio = $this->_calculateData( $cnt_value , 'ebrosur_count');
    			} else {
    				$ebrosur_ratio = 0;
    			}

    			// ====== ratio message
    			if( !empty($agent_count) ) {
    				$message_ratio = $this->_calculateData( $cnt_value , 'message_count');
    			} else {
    				$message_ratio = 0;
    			}

    			// ====== ratio kpr
    			if( !empty($agent_count) ) {
    				$kpr_ratio = $this->_calculateData( $cnt_value , 'kpr_count');
    			} else {
    				$kpr_ratio = 0;
    			}

    			// ====== ratio aktifitas
    			if (!empty($TotalActivity)) {
					$ratio_activities = $this->_calculateData( $cnt_value , 'daily_activity');
    			} else {
    				$ratio_activities = 0;
    			}

    			// TOTAL USAGE - sum ratio / total ratio 
    			$total_usage = $publish_prop_ratio + $ebrosur_ratio + $message_ratio + $kpr_ratio + $ratio_activities;
    			$total_usage = $total_usage / 5;
    			$total_usage = $this->RmCommon->_callRoundPrice($total_usage, 2, '', array(
					'format' => 'percent',
				));


    			if( !empty($city) && !empty($region) ) {
    				$city = __('%s, %s', $city, $region);
    			}

				if( !$this->Rest->isActive() ) {
	           		if( $group_id != 4 ) {
	           			$kpr_count = ($type == 'view')?$this->Html->link($kpr_count, array(
							'controller' => 'kpr',
		         			'action' => 'info',
		         			$id,
		         			'admin' => true,
		         			'date_from' => $activities_from,
		         			'date_to' => $activities_to,
						), array(
	    					'target' => 'blank',
	         			)):$kpr_count;
	         			$message_count = ($type == 'view')?$this->Html->link($message_count, array(
							'controller' => 'messages',
		         			'action' => 'info',
		         			$id,
		         			'admin' => true,
		         			'date_from' => $activities_from,
		         			'date_to' => $activities_to,
						), array(
	    					'target' => 'blank',
	         			)):$message_count;
	           		}

	           		$name = ($type == 'view')?$this->Html->link($name, array(
						'controller' => 'users',
	         			'action' => 'info',
	         			$id,
	         			'admin' => true,
					), array(
    					'target' => 'blank',
         			)):$name;

         			$principle_name = ($type == 'view')?$this->Html->link($principle_name, array(
						'controller' => 'users',
	         			'action' => 'info',
	         			$id,
	         			'admin' => true,
					), array(
    					'target' => 'blank',
         			)):$principle_name;

         			$domain = ($type == 'view')?$this->Html->link($domain, $domain, array(
    					'target' => 'blank',
         			)):$domain;

         			$non_agent_count = (($type == 'view')?$this->Html->link($non_agent_count, array(
						'controller' => 'users',
	         			'action' => 'user_info',
	         			$id,
	         			'admin' => true,
	         			'non_agent' => true,
					), array(
    					'target' => 'blank',
         			)):$non_agent_count);

         			$agent_count = (($type == 'view')?$this->Html->link($agent_count, array(
						'controller' => 'users',
	         			'action' => 'info_agents',
	         			$id,
	         			'admin' => true,
					), array(
    					'target' => 'blank',
         			)):$agent_count);

         			$prop_publish_cnt = (($type == 'view')?$this->Html->link($prop_publish_cnt, array(
						'controller' => 'properties',
	         			'action' => 'info',
	         			$id,
	         			'status' => 'active-pending',
	         			'admin' => true,
	         			'date_from' => $activities_from,
	         			'date_to' => $activities_to,
					), array(
    					'target' => 'blank',
         			)):$prop_publish_cnt);

         			$prop_sold_cnt = (($type == 'view')?$this->Html->link($prop_sold_cnt, array(
						'controller' => 'properties',
	         			'action' => 'info',
	         			$id,
	         			'status' => 'sold',
	         			'admin' => true,
	         			'date_from' => $activities_from,
	         			'date_to' => $activities_to,
					), array(
    					'target' => 'blank',
         			)):$prop_sold_cnt);

         			$prop_inactive_cnt = (($type == 'view')?$this->Html->link($prop_inactive_cnt, array(
						'controller' => 'properties',
	         			'action' => 'info',
	         			$id,
	         			'status' => 'inactive',
	         			'admin' => true,
	         			'date_from' => $activities_from,
	         			'date_to' => $activities_to,
					), array(
    					'target' => 'blank',
         			)):$prop_inactive_cnt);

         			$ebrosur_count = (($type == 'view')?$this->Html->link($ebrosur_count, array(
						'controller' => 'ebrosurs',
	         			'action' => 'info',
	         			$id,
	         			'admin' => true,
	         			'date_from' => $activities_from,
	         			'date_to' => $activities_to,
					), array(
    					'target' => 'blank',
         			)):$ebrosur_count);

				}

				$contentArr = array(
					__('PIC BDA') => array(
						'text' => $pic_name,
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'pic_name\',width:150',
		                'value_column' => 'pic_name',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Perusahaan') => array(
						'text' => $name,
						'width' => 25,
                		'field_model' => 'UserCompany.name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'company\',width:150',
		                'value_column' => 'company_name',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Area') => array(
						'text' => $city,
						'width' => 25,
                		'field_model' => 'City.name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'area\',width:150',
		                'value_column' => 'area',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Principle') => array(
						'text' => $principle_name,
						'width' => 25,
                		'field_model' => 'User.full_name',
                		'fix_column' => true,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'principle\',width:150',
		                'value_column' => 'principle',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Jenis') => array(
						'text' => $group_name,
						'width' => 12,
                		'field_model' => 'User.group_id',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'group_name\',width:100',
		                'value_column' => 'group_name',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Sign Contract') => array(
						'text' => $contract_date,
						'width' => 12,
                		'field_model' => 'UserCompanyConfig.contract_date',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'contract_date\',width:100',
		                'value_column' => 'contract_date',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Live') => array(
						'text' => $live_date,
						'width' => 12,
                		'field_model' => 'UserCompanyConfig.live_date',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'live_date\',width:100',
		                'value_column' => 'live_date',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Tgl Berakhir') => array(
						'text' => $end_date,
						'width' => 12,
                		'field_model' => 'UserCompanyConfig.end_date',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'end_date\',width:100',
		                'value_column' => 'end_date',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Durasi Contract (Thn)') => array(
						'text' => __('%s Thn', $duration_year),
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'duration\',width:150',
		                'value_column' => 'duration_contract',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Akses Website') => array(
						'text' => $domain,
						'width' => 40,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'domain\',width:250',
		                'value_column' => 'domain',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Non Agen Aktif') => array(
						'text' => !empty($non_agent_count)?$non_agent_count:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'non_agent_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'non_agent_count',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Jml Agen Aktif') => array(
						'text' => !empty($agent_count)?$agent_count:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'agent_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'agent_count',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Jml Listing Sold') => array(
						'text' => !empty($prop_sold_cnt)?$prop_sold_cnt:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_sold_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'property_sold_count',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Jml Listing Offline') => array(
						'text' => !empty($prop_inactive_cnt)?$prop_inactive_cnt:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_inactive_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'property_inactive_count',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
					__('Listing Tayang') => array(
						'text' => false,
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'publish_column\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'empty_string',
		                'rowspan' => 'false',
		                'colspan' => '2',
					),
					__('Jml Listing') => array(
						'text' => !empty($prop_publish_cnt)?$prop_publish_cnt:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_publish_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'property_publish_count',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					__('Ratio Listing') => array(
						'text' => !empty($publish_prop_ratio)?$publish_prop_ratio:'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_publish_ratio\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'property_publish_ratio',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					__('Ebrosur') => array(
						'text' => false,
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'ebrosur_column\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'empty_string',
		                'rowspan' => 'false',
		                'colspan' => '2',
					),
					__('Jml Ebrosur') => array(
						'text' => !empty($ebrosur_count)?$ebrosur_count:'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'ebrosur_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'ebrosur_count',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					__('Ratio Ebrosur') => array(
						'text' => !empty($ebrosur_ratio)?$ebrosur_ratio:'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'ebrosur_ratio\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'ebrosur_ratio',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					__('Pesan') => array(
						'text' => false,
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'message_column\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'empty_string',
		                'rowspan' => 'false',
		                'colspan' => '2',
					),
					__('Jml Pesan') => array(
						'text' => !empty($message_count)?$message_count:'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'message_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'message_count',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					__('Ratio Pesan') => array(
						'text' => !empty($message_ratio)?$message_ratio:'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'message_ratio\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'message_ratio',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					__('KPR') => array(
						'text' => false,
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'kpr_column\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'empty_string',
		                'rowspan' => 'false',
		                'colspan' => '2',
					),
					__('Submit KPR') => array(
						'text' => !empty($kpr_count)?$kpr_count:'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'kpr_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'kpr_count',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					__('Ratio KPR') => array(
						'text' => !empty($kpr_ratio)?$kpr_ratio:'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'kpr_ratio\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'kpr_ratio',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					
					/*
					__('Session Terakhir') => array(
						'text' => !empty($log_view)?$log_view:'-',
						'width' => 12,
                		'field_model' => false,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'log_view\',width:100',
					),
					__('Login Terakhir') => array(
						'text' => !empty($last_login)?$last_login:'-',
						'width' => 12,
                		'field_model' => false,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'last_login\',width:100',
					),
					__('Total Session') => array(
						'text' => Common::hashEmptyField($value, 'LogViewCount', '-'),
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_session\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					*/

					__('Aktivitas') => array(
						'text' => false,
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_login\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'empty_string',
		                'rowspan' => 'false',
		                'colspan' => '2',
					),
					__('Total Aktivitas') => array(
						'text' => !empty($TotalActivity)?$TotalActivity:'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_login\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'total_login',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					__('Ratio Aktivitas') => array(
						'text' => !empty($ratio_activities)?$ratio_activities:'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_login\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'total_login',
		                'rowspan' => 'false',
		                'colspan' => 'false',
		                'second_row' => true,
					),
					__('Total Usage') => array(
						'text' => !empty($total_usage)?$total_usage:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_usage\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
		                'value_column' => 'total_usage',
		                'rowspan' => '2',
		                'colspan' => 'false',
					),
				);

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	// ===================================================================================
	// ====== rumus perhitungan ratio goes here ( edit di sini kalau belum sesuai ) ======
	// ===================================================================================
	function _calculateData ( $data = array(), $type = 'daily_activity' ) {
		$result = false;

		$non_agent_count = Common::hashEmptyField($data, 'non_agent_count');
		$agent_count     = Common::hashEmptyField($data, 'agent_count');

		$cnt_val         = Common::hashEmptyField($data, $type);
		
		switch ($type) {
			case 'prop_publish_cnt':
				$ratio = ($cnt_val/$agent_count)*100; // (jml data / jml agent) dikali 100

				break;
			case 'ebrosur_count':
			case 'message_count':
			case 'kpr_count':
				$ratio = ($cnt_val/$agent_count); // (jml data / jml agent)

				break;
			case 'daily_activity':
				$total_user = $non_agent_count + $agent_count;
				$ratio      = ($cnt_val/$total_user); // (jml data / total user) dikali 100

				break;

		}

		if ($ratio) {

			$result = $this->RmCommon->_callRoundPrice($ratio, 2, '', array(
				'format' => 'percent',
			));

		}
		// debug($result);die();

		return $result;

	}

	function _callDataGraphicPerformance ( $params ) {
		$this->controller->loadModel('UserCompanyConfig');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }

        $companyData = Configure::read('Config.Company.data');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

		$options = array(
			'conditions' => array(
				'UserCompany.name <>' => '',
				'UserCompanyConfig.live_date NOT' => NULL,
				'UserCompanyConfig.live_date NOT' => '0000-00-00',
			),
			'contain' => array(
				'UserCompany',
			),
			'order' => array(
				'UserCompanyConfig.live_date' => 'ASC',
				'UserCompanyConfig.id' => 'DESC',
			),
			'group' => array(
				'UserCompanyConfig.live_date',
			),
			'fields' => array(
				'UserCompanyConfig.live_date',
				'UserCompanyConfig.cnt',
			),
		);

		$this->controller->User->UserCompanyConfig->virtualFields['cnt'] = 'COUNT(UserCompanyConfig.id)';

		$options = $this->controller->User->UserCompanyConfig->_callRefineParams($params, $options);
		$data = $this->controller->User->UserCompanyConfig->getData('list', $options, array(
			'mine' => true,
		));

		return array(
			'data' => $data,
		);
	}

	function _callDataAgents ( $params, $offset = false, $limit = 30, $type = false ) {
        $companyData = Configure::read('Config.Company.data');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $params_named = $this->RmCommon->filterEmptyField($this->controller->params, 'named', false, array());

		$dateRange = $this->RmCommon->_callConvertDateRange($params, Common::hashEmptyField($params, 'Search.date'));
		$dateRange = $this->RmCommon->_callSet(array(
			'date_from',
			'date_to',
		), $dateRange);

		if( !empty($dateRange) ) {
			$params['Search']['transaction_from'] = Common::hashEmptyField($dateRange, 'date_from');
			$params['Search']['transaction_to'] = Common::hashEmptyField($dateRange, 'date_to');
			$dateRange = array(
				'named' => $dateRange,
			);
		}

        $params = $this->RmCommon->_callUnset(array(
			'Search' => array(
				'date',
			),
		), $params);
		$params_named = $this->RmCommon->_callUnset(array(
			'date_from',
			'date_to',
		), $params_named);

		$params['named'] = array_merge($this->RmCommon->processSorting(array(), $params, false, false, false), $params_named);

		$options = array(
			'contain' => array(
				'UserCompanyConfigParent',
			),
			'conditions' => array(
				'UserCompanyConfigParent.user_id NOT' => NULL,
			),
			'group' => array(
				'User.id',
			),
            'offset' => $offset,
			'limit' => $limit,
		);

		$options = $this->controller->User->_callRefineParams($params, $options);
		$options['order']['User.id'] = 'DESC';
		
		$this->controller->User->bindModel(array(
            'hasOne' => array(
                'UserCompanyConfigParent' => array(
                    'className' => 'UserCompanyConfig',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'UserCompanyConfigParent.user_id = User.parent_id',
                	),
                ),
            )
        ), false);

		$this->controller->paginate	= $this->controller->User->getData('paginate', $options, array(
			'admin' => true,
			'company' => true,
			'status' => 'semi-active',
			'role' => 'agent',
			'tree_division' => true,
		));

		$data = $this->controller->paginate('User');
		$result = array();

		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'User', 'id');

		if( !empty($data) ) {
			$total_listing = 0;
			$total_listing_published = 0;
			$total_listing_sold = 0;
			$total_rasio_sold = 0;
			$total_listing_offline = 0;
			$total_ebrosur = 0;
			$total_ratio_ebrosur = 0;
			$total_msg = 0;
			$total_ratio_msg = 0;
			$total_kpr = 0;
			$total_ratio_kpr = 0;
			$grandtotal_session = 0;
			$grandtotal_login = 0;

			$total_client			= 0;
			$total_current_client	= 0;
			$total_crm				= 0;
			$total_activity			= 0;
			$total_attributes		= array();

			$this->AttributeSet		= ClassRegistry::init('AttributeSet');
			$this->AttributeOption	= ClassRegistry::init('AttributeOption');

			$attribute_sets		= $this->AttributeSet->getData('all');
			$attribute_options	= $this->AttributeOption->getData('all', array(
				'conditions' => array(
					'AttributeOption.status'	=> 1, 
					'AttributeOption.show'		=> 1, 
					'AttributeOption.type'		=> 'option', 
				), 
			));

			foreach ($data as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$value = $this->controller->User->getMergeList($value, array(
					'contain' => array(
						'UserConfig',
						'UserProfile',
						'UserCompany' => array(
							'uses' => 'UserCompany',
							'foreignKey' => 'parent_id',
							'primaryKey' => 'user_id',
						),
					),
				));
				$value = $this->RmCommon->dataConverter($value,array(
					'date' => array(
						'UserConfig' => array(
							'last_login',
							'created',
						),
					),
				), true);
				
				$logViewOptions = array(
					'conditions' => array(
						'LogView.user_id' => $id,
					),
					'order'=> false,
				);
				$value = $this->controller->User->LogView->_callGetDataView($value, $logViewOptions);

			//	b:counter ==========================================================================================================

    			$message_count = $this->controller->User->Property->Message->_callAgentCount($id, 'active', $dateRange);
    			$property_count = $this->controller->User->Property->_callAgentPropertyCount($id, 'all', $dateRange);
    			$property_publish_count = $this->controller->User->Property->_callAgentPropertyCount($id, 'active-pending', $dateRange);
    			$property_sold_count = $this->controller->User->Property->_callAgentPropertyCount($id, 'sold', $dateRange);
    			$property_inactive_count = $this->controller->User->Property->_callAgentPropertyCount($id, 'inactive', $dateRange);
    			$ebrosur_count = $this->controller->User->UserCompanyEbrochure->_callAgentCount($id, 'active', $dateRange);
				$kpr_count = $this->controller->User->Kpr->_callAgentCount($id, 'active-all', $dateRange);

				$client_count			= $this->controller->User->_callClientCount($id);
				$current_client_count	= 0;

				if($dateRange){
					$current_client_count = $this->controller->User->_callClientCount($id, $dateRange);
				}

				$activity_summary	= $this->controller->User->_callCrmProjectCount($id, 'summary', $dateRange);
				$crm_count			= Hash::extract($activity_summary, '{n}.ViewAgentCrmProject.crm_count');
				$activity_count		= Hash::extract($activity_summary, '{n}.ViewAgentCrmProject.activity_count');

				$crm_count			= array_sum($crm_count);
				$activity_count		= array_sum($activity_count);

				$activity_detail	= $this->controller->User->_callCrmProjectCount($id, 'detail', $dateRange);
				$attribute_set_rows	= array();

				foreach($attribute_sets as $attribute_set){
					$attribute_set_id	= Common::hashEmptyField($attribute_set, 'AttributeSet.id');
					$attribute_set_slug	= Common::hashEmptyField($attribute_set, 'AttributeSet.slug');
					$attribute_set_name	= Common::hashEmptyField($attribute_set, 'AttributeSet.name');
					$attribute_set_key	= Common::toSlug($attribute_set_name, '_');

					$isset = Hash::check($total_attributes, sprintf('set.%s', $attribute_set_slug));

					if(empty($isset)){
						$total_attributes['set'][$attribute_set_slug] = 0;
					}

					$attribute_selector	= '{n}.ViewAgentCrmProject[attribute_set_id='.$attribute_set_id.'].crm_count';
					$attribute_count	= Hash::extract($activity_detail, $attribute_selector);
					$attribute_count	= array_sum($attribute_count);

					$total_attributes['set'][$attribute_set_slug] = $total_attributes['set'][$attribute_set_slug] + $attribute_count;

					$attribute_set_rows[__('CRM - %s', $attribute_set_name)] = array(
						'text'			=> $attribute_count ?: '-',
						'width'			=> 25,
						'style'			=> 'text-align: center;vertical-align: middle;',
						'data-options'	=> 'field:\''.$attribute_set_key.'\',width:200',
						'align'			=> 'center',
						'mainalign'		=> 'center',
					);
				}

				$activity_option_detail	= $this->controller->User->_callCrmProjectActivityCount($id, $dateRange);
				$attribute_option_rows	= array();

				foreach($attribute_options as $attribute_option){
					$attribute_option_id	= Common::hashEmptyField($attribute_option, 'AttributeOption.id');
					$attribute_option_slug	= Common::hashEmptyField($attribute_option, 'AttributeOption.slug');
					$attribute_option_name	= Common::hashEmptyField($attribute_option, 'AttributeOption.name');
					$attribute_option_key	= Common::toSlug($attribute_option_name, '_');

					$isset = Hash::check($total_attributes, sprintf('option.%s', $attribute_option_slug));

					if(empty($isset)){
						$total_attributes['option'][$attribute_option_slug] = 0;
					}

					$attribute_selector	= '{n}.ViewAgentCrmProjectActivity[attribute_option_id='.$attribute_option_id.'].activity_count';
					$attribute_count	= Hash::extract($activity_option_detail, $attribute_selector);
					$attribute_count	= array_sum($attribute_count);

					$total_attributes['option'][$attribute_option_slug] = $total_attributes['option'][$attribute_option_slug] + $attribute_count;

					$attribute_option_rows[__('Aktivitas CRM - %s', $attribute_option_name)] = array(
						'text'			=> $attribute_count ?: '-',
						'width'			=> 25,
						'style'			=> 'text-align: center;vertical-align: middle;',
						'data-options'	=> 'field:\''.$attribute_option_key.'\',width:200',
						'align'			=> 'center',
						'mainalign'		=> 'center',
					);
				}

			//	e:counter ==========================================================================================================

				$name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name', '-');
				$email = $this->RmCommon->filterEmptyField($value, 'User', 'email', '-');

    			$phones = array();
				$phones[] = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'phone', null);
				$phones[] = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
				$phones[] = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'no_hp_2', null);
    			$phones = array_filter($phones);

				$last_login = Common::hashEmptyField($value, 'LogLogin.created', false, array(
					'date' => 'd M Y H:i',
				));
				$log_view = Common::hashEmptyField($value, 'LogView.created', false, array(
					'date' => 'd M Y H:i',
				));

				$created = Common::hashEmptyField($value, 'User.created', false, array(
					'date' => 'd M Y H:i',
				));
				
    			if( !empty($property_count) ) {
    				$ratio = $property_sold_count/$property_count;
    				$property_sold_ratio = $this->RmCommon->_callRoundPrice($ratio, 2);
    			} else {
    				$property_sold_ratio = 0;
    			}

    			if( !empty($property_count) ) {
    				$ratio = $ebrosur_count/$property_count;
    				$ebrosur_ratio = $this->RmCommon->_callRoundPrice($ratio, 2);
    			} else {
    				$ebrosur_ratio = 0;
    			}

    			if( !empty($property_count) ) {
    				$ratio = $message_count/$property_count;
    				$message_ratio = $this->RmCommon->_callRoundPrice($ratio, 2);
    			} else {
    				$message_ratio = 0;
    			}

    			if( !empty($property_count) ) {
    				$ratio = $kpr_count/$property_count;
    				$kpr_ratio = $this->RmCommon->_callRoundPrice($ratio, 2);
    			} else {
    				$kpr_ratio = 0;
    			}

				$contentArr = array();
				$total_listing += $property_count;
				$total_listing_published += $property_publish_count;
				$total_listing_sold += $property_sold_count;
				$total_rasio_sold += $property_sold_ratio;
				$total_listing_offline += $property_inactive_count;
				$total_ebrosur += $ebrosur_count;
				$total_msg += $message_count;
				$total_kpr += $kpr_count;

				$total_client			= $total_client + $client_count;
				$total_current_client	= $total_current_client + $current_client_count;
				$total_crm				= $total_crm + $crm_count;
				$total_activity			= $total_activity + $activity_count;

				$total_session = Common::hashEmptyField($value, 'LogViewCount');
				$total_login = Common::hashEmptyField($value, 'LogLoginCount');

				$grandtotal_session += $total_session;
				$grandtotal_login += $total_login;

				if( !empty($admin_rumahku) || $group_id == 4 ) {
					$company_name = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'name', '-');
					$contentArr = array(
						__('Perusahaan') => array(
							'text' => $company_name,
							'width' => 15,
	                		'field_model' => 'UserCompany.name',
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'company_name\',width:200',
						),
					);
				}

				$contentArr = array_merge($contentArr, array(
					__('Nama') => array(
						'text' => $name,
						'width' => 25,
                		'field_model' => 'User.full_name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'name\',width:200',
					),
					__('Email') => array(
						'text' => $email,
						'width' => 30,
                		'field_model' => 'User.email',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'email\',width:200',
                		'fix_column' => true,
					),
					__('Handphone') => array(
						'text' => implode(' / ', $phones),
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'no_hp\',width:300',
					),
					__('Session Terakhir') => array(
						'text' => !empty($log_view)?$log_view:'-',
						'width' => 25,
                		'field_model' => false,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'log_view\',width:150',
					),
					__('Login Terakhir') => array(
						'text' => !empty($last_login)?$last_login:'-',
						'width' => 25,
                		'field_model' => false,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'last_login\',width:150',
					),
					__('Tgl Terdaftar') => array(
						'text' => $created ?: '-',
						'width' => 25,
                		'field_model' => 'UserConfig.created',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'created\',width:150',
					),
					__('Total Session') => array(
						'text' => Common::hashEmptyField($value, 'LogViewCount', '-'),
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_session\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Total Login') => array(
						'text' => Common::hashEmptyField($value, 'LogLoginCount', '-'),
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_login\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Total Listing') => array(
						'text' => !empty($property_count)?$property_count:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_count\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Listing Tayang') => array(
						'text' => !empty($property_publish_count)?$property_publish_count:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_publish_count\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Listing Sold') => array(
						'text' => !empty($property_sold_count)?$property_sold_count:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_sold_count\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Ratio Listing Sold') => array(
						'text' => !empty($property_sold_ratio)?$property_sold_ratio:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_sold_ratio\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Listing Offline') => array(
						'text' => !empty($property_inactive_count)?$property_inactive_count:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_inactive_count\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Jml Ebrosur') => array(
						'text' => !empty($ebrosur_count)?$ebrosur_count:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'ebrosur_count\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Ratio Ebrosur') => array(
						'text' => !empty($ebrosur_ratio)?$ebrosur_ratio:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'ebrosur_ratio\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Jml Pesan') => array(
						'text' => !empty($message_count)?$message_count:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'message_count\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Ratio Pesan') => array(
						'text' => !empty($message_ratio)?$message_ratio:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'message_ratio\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Submit KPR') => array(
						'text' => !empty($kpr_count)?$kpr_count:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'kpr_count\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Ratio KPR') => array(
						'text' => !empty($kpr_ratio)?$kpr_ratio:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'kpr_ratio\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
				));

				$contentArr = array_merge($contentArr, $attribute_set_rows, array(
					__('Total CRM') => array(
						'text'			=> $crm_count ?: '-',
						'width'			=> 25,
						'style'			=> 'text-align: center;vertical-align: middle;',
						'data-options'	=> 'field:\'crm_count\',width:150',
						'align'			=> 'center',
						'mainalign'		=> 'center',
					), 
				));

				$contentArr = array_merge($contentArr, $attribute_option_rows, array(
					__('Total Aktivitas CRM') => array(
						'text'			=> $activity_count ?: '-',
						'width'			=> 25,
						'style'			=> 'text-align: center;vertical-align: middle;',
						'data-options'	=> 'field:\'activity_count\',width:150',
						'align'			=> 'center',
						'mainalign'		=> 'center',
					),
					__('Total Klien') => array(
						'text'			=> $client_count ?: '-',
						'width'			=> 25,
						'style'			=> 'text-align: center;vertical-align: middle;',
						'data-options'	=> 'field:\'client_count\',width:150',
						'align'			=> 'center',
						'mainalign'		=> 'center',
					),
				));

				if($dateRange){
					$contentArr[__('Total Klien (Periode Terpilih)')] = array(
						'text'			=> $current_client_count ?: '-',
						'width'			=> 25,
						'style'			=> 'text-align: center;vertical-align: middle;',
						'data-options'	=> 'field:\'current_client_count\',width:150',
						'align'			=> 'center',
						'mainalign'		=> 'center',
					);
				}

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
			}

			if( $type == 'view' && !empty($result) && !$this->Rest->isActive() ) {
				$result[$key+1] = array();

				if( !empty($total_listing) ) {
					$ratio = $total_listing_sold/$total_listing;
					$property_sold_ratio = $this->RmCommon->_callRoundPrice($ratio, 2);
				} else {
					$property_sold_ratio = 0;
				}

				if( !empty($total_listing) ) {
					$ratio = $total_ebrosur/$total_listing;
					$ebrosur_ratio = $this->RmCommon->_callRoundPrice($ratio, 2);
				} else {
					$ebrosur_ratio = 0;
				}

				if( !empty($total_listing) ) {
					$ratio = $total_msg/$total_listing;
					$message_ratio = $this->RmCommon->_callRoundPrice($ratio, 2);
				} else {
					$message_ratio = 0;
				}

				if( !empty($total_listing) ) {
					$ratio = $total_kpr/$total_listing;
					$kpr_ratio = $this->RmCommon->_callRoundPrice($ratio, 2);
				} else {
					$kpr_ratio = 0;
				}

				if( !empty($admin_rumahku) || $group_id == 4 ) {
					$result[$key+1] = array(
						__('Perusahaan') => array(
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'company_name\',width:150',
						),
					);
				}

				$result[$key+1] = array_merge($result[$key+1], array(
					__('Nama') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'name\',width:150',
					),
					__('Email') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'email\',width:250',
					),
					__('Handphone') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'no_hp\',width:150',
					),
					__('Session Terakhir') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'log_view\',width:100',
					),
					__('Login Terakhir') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'last_login\',width:100',
					),
					__('Tgl Terdaftar') => array(
						'text' => __('Total'),
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'created\',width:100',
					),
					__('Total Session') => array(
						'text' => !empty($grandtotal_session)?$grandtotal_session:'-',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_session\',width:80',
					),
					__('Total Login') => array(
						'text' => !empty($grandtotal_login)?$grandtotal_login:'-',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_login\',width:80',
					),
					__('Total Listing') => array(
						'text' => !empty($total_listing)?$total_listing:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Listing Tayang') => array(
						'text' => !empty($total_listing_published)?$total_listing_published:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_publish_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Listing Sold') => array(
						'text' => !empty($total_listing_sold)?$total_listing_sold:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_sold_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Ratio Listing Sold') => array(
						'text' => !empty($property_sold_ratio)?$property_sold_ratio:'-',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_sold_ratio\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Listing Offline') => array(
						'text' => !empty($total_listing_offline)?$total_listing_offline:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_inactive_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Jml Ebrosur') => array(
						'text' => !empty($total_ebrosur)?$total_ebrosur:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'ebrosur_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Ratio Ebrosur') => array(
						'text' => !empty($ebrosur_ratio)?$ebrosur_ratio:'-',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'ebrosur_ratio\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Jml Pesan') => array(
						'text' => !empty($total_msg)?$total_msg:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'message_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Ratio Pesan') => array(
						'text' => !empty($message_ratio)?$message_ratio:'-',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'message_ratio\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Submit KPR') => array(
						'text' => !empty($total_kpr)?$total_kpr:'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'kpr_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Ratio KPR') => array(
						'text' => !empty($kpr_ratio)?$kpr_ratio:'-',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'kpr_ratio\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
				));

				foreach($attribute_sets as  $attribute_set){
					$attribute_set_id	= Common::hashEmptyField($attribute_set, 'AttributeSet.id');
					$attribute_set_slug	= Common::hashEmptyField($attribute_set, 'AttributeSet.slug');
					$attribute_set_name	= Common::hashEmptyField($attribute_set, 'AttributeSet.name');
					$attribute_set_key	= Common::toSlug($attribute_set_name, '_');
					$attribute_count	= Common::hashEmptyField($total_attributes, sprintf('set.', $attribute_set_slug));

					$result[$key+1][__('CRM - %s', $attribute_set_name)] = array(
						'text'			=> $attribute_count ?: '-',
						'data-options'	=> 'field:\''.$attribute_set_key.'\',width:150',
					);
				}

				$result[$key+1] = array_merge($result[$key+1], array(
					__('Total CRM') => array(
						'text'			=> $total_crm ?: '-',
						'data-options'	=> 'field:\'crm_count\',width:150',
					),
				));

				foreach($attribute_options as  $attribute_option){
					$attribute_option_id	= Common::hashEmptyField($attribute_option, 'AttributeOption.id');
					$attribute_option_slug	= Common::hashEmptyField($attribute_option, 'AttributeOption.slug');
					$attribute_option_name	= Common::hashEmptyField($attribute_option, 'AttributeOption.name');
					$attribute_option_key	= Common::toSlug($attribute_option_name, '_');
					$attribute_count		= Common::hashEmptyField($total_attributes, sprintf('option.', $attribute_option_slug));

					$result[$key+1][__('Aktivitas CRM - %s', $attribute_option_name)] = array(
						'text'			=> $attribute_count ?: '-',
						'data-options'	=> 'field:\''.$attribute_option_key.'\',width:150',
					);
				}

				$result[$key+1] = array_merge($result[$key+1], array(
					__('Total Aktivitas CRM') => array(
						'text'			=> $total_activity ?: '-',
						'data-options'	=> 'field:\'activity_count\',width:150',
					),
					__('Total Klien') => array(
						'text'			=> $total_client ?: '-',
						'data-options'	=> 'field:\'client_count\',width:150',
					),
				));

				if($dateRange){
					$result[$key+1][__('Total Klien (Periode Terpilih)')] = array(
						'text'			=> $total_current_client ?: '-',
						'data-options'	=> 'field:\'current_client_count\',width:150',
					);
				}
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	function _callDataGraphicAgents ( $params ) {
        $companyData = Configure::read('Config.Company.data');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }

		$options = array(
			'conditions' => array(),
			'order' => array(
				'User.created' => 'ASC',
				'User.id' => 'DESC',
			),
			'group' => array(
				'DATE_FORMAT(User.created, \'%Y-%m-%d\')',
			),
			'fields' => array(
				'User.created',
				'User.cnt',
			),
		);
		$this->controller->User->virtualFields['cnt'] = 'COUNT(User.id)';
		$this->controller->User->virtualFields['created'] = 'DATE_FORMAT(User.created, \'%Y-%m-%d\')';

		$options = $this->controller->User->_callRefineParams($params, $options);
		$data = $this->controller->User->getData('list', $options, array(
			'admin' => true,
			'company' => true,
			'status' => 'semi-active',
			'role' => 'agent',
		));

		return array(
			'data' => $data,
		);
	}

	function _callAddBeforeViewAgent () {
        $companyData = Configure::read('Config.Company.data');
		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

		if( !empty($admin_rumahku) || $group_id == 4 ) {
			$companies =  $this->RmCommon->_callCompanies();
		}

		$this->RmCommon->_layout_file('select2');
		$this->controller->set(compact('companies'));
	}

	function _callDataSummary ( $params, $offset = false, $limit = 30, $type = false ) {
		$this->controller->loadModel('UserCompany');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }

		$options = $this->controller->User->getData('paginate', false, array(
			'status' => 'semi-active',
			'company' => true,
			'admin' => true,
			'role' => 'principle',
		));
		$conditions = $this->RmCommon->filterEmptyField($options, 'conditions', false, array());

		$options = array(
			'conditions' => array_merge($conditions, array(
				'UserCompanyConfig.id NOT' => NULL,
				'City.id NOT' => NULL,
			)),
			'order' => array(
				'UserCompany.city_id' => 'DESC',
			),
			'contain' => array(
				'City',
				'UserCompanyConfig',
				'User',
			),
            'offset' => $offset,
			'limit' => $limit,
			'group' => array(
				'UserCompany.city_id',
			),
		);
		
		$this->controller->UserCompany->bindModel(array(
            'hasOne' => array(
                'UserCompanyConfig' => array(
                    'className' => 'UserCompanyConfig',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'UserCompanyConfig.user_id = UserCompany.user_id',
                	),
                ),
            )
        ), false);

		$options = $this->controller->User->UserCompany->_callRefineParams($params, $options);
		$this->controller->paginate	= $this->controller->User->UserCompany->getData('paginate', $options, array(
			'company' => true,
		));
		$data = $this->controller->paginate('UserCompany');
		$result = array();

		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'UserCompany', 'city_id');
		$group_type = $this->RmCommon->filterEmptyField($params, 'named', 'group_type');

		if( !empty($data) ) {
			$grantotal = 0;
			$total_active = 0;
			$total_expired = 0;

			foreach ($data as $key => $value) {
				$name = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'name', '-');
				
				$city_id = $this->RmCommon->filterEmptyField($value, 'City', 'id', null);
				$city = $this->RmCommon->filterEmptyField($value, 'City', 'name', '-');
				
    			$expired_count = $this->controller->User->UserCompanyConfig->_callCount($city_id, $params, 'expired');
    			$active_count = $this->controller->User->UserCompanyConfig->_callCount($city_id, $params, 'active');
    			$all_count = $active_count + $expired_count;

				$grantotal += $all_count;
				$total_active += $active_count;
				$total_expired += $expired_count;

				if( $group_type == 'group' ) {
					$labelName = __('Group');
				} else {
					$labelName = __('Perusahaan');
				}

				$contentArr = array(
					__('Kota') => array(
						'text' => $city,
						'width' => 25,
                		'field_model' => 'City.name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'company\',width:150',
					),
					__('Jml Aktif') => array(
						'text' => !empty($active_count)?$active_count:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'active_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Jml Expired') => array(
						'text' => !empty($expired_count)?$expired_count:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'expired_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Total %s', $labelName) => array(
						'text' => !empty($all_count)?$all_count:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'all_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
				);

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
			}

			if( !$this->Rest->isActive() && $type == 'view' ) {
				$result[$key+1] = array(
					__('Kota') => array(
						'text' => __('Total'),
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;font-weight:bold;',
		                'data-options' => 'field:\'label_total\',width:150',
					),
					__('Jml Aktif') => array(
						'text' => !empty($total_active)?$total_active:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;font-weight:bold;',
		                'data-options' => 'field:\'total_active\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Jml Expired') => array(
						'text' => !empty($total_expired)?$total_expired:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;font-weight:bold;',
		                'data-options' => 'field:\'total_expired\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Total') => array(
						'text' => !empty($grantotal)?$grantotal:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;font-weight:bold;',
		                'data-options' => 'field:\'grantotal\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
				);
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	function _callDataGraphicSummary ( $params ) {
		$this->controller->loadModel('UserCompany');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }

		$options = $this->controller->User->getData('paginate', false, array(
			'status' => 'semi-active',
			'company' => true,
			'admin' => true,
			'role' => 'principle',
		));
		$conditions = $this->RmCommon->filterEmptyField($options, 'conditions', false, array());

		$options = array(
			'conditions' => array_merge($conditions, array(
				'UserCompanyConfig.id NOT' => NULL,
			)),
			'order' => array(
				'UserCompany.city_id' => 'DESC',
			),
			'contain' => array(
				'UserCompanyConfig',
				'User',
			),
			'group' => array(
				'UserCompany.city_id',
			),
		);
		
		$this->controller->UserCompany->bindModel(array(
            'hasOne' => array(
                'UserCompanyConfig' => array(
                    'className' => 'UserCompanyConfig',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'UserCompanyConfig.user_id = UserCompany.user_id',
                	),
                ),
            )
        ), false);

		$options = $this->controller->User->UserCompany->_callRefineParams($params, $options);
		$data = $this->controller->User->UserCompany->getData('all', $options, array(
			'company' => true,
			'rest' => false,
		));
		$result = array();

		$group_type = $this->RmCommon->filterEmptyField($params, 'named', 'group_type');

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
				$value = $this->controller->User->UserCompany->getMergeList($value, array(
					'contain' => array(
						'City' => array(
							'cache' => true,
						),
					),
				));
				$name = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'name', '-');
				
				$city_id = $this->RmCommon->filterEmptyField($value, 'City', 'id', null);
				$city = $this->RmCommon->filterEmptyField($value, 'City', 'name');

				if( !empty($city) ) {
	    			$expired_count = $this->controller->User->UserCompanyConfig->_callCount($city_id, $params, 'expired');
	    			$active_count = $this->controller->User->UserCompanyConfig->_callCount($city_id, $params, 'active');

					if( !empty($active_count) ) {
						$result['Active'][$city] = $active_count;
					}

					if( !empty($expired_count) ) {
						$result['Expired'][$city] = $expired_count;
					}
				}
			}
		}

		return array(
			'data' => $result,
		);
	}

	function _callAddBeforeView ( $title_default = false ) {
		$data = $this->controller->request->data;
		$title = $this->RmCommon->filterEmptyField($data, 'Search', 'title', $title_default);

		$region_id = Hash::get($data, 'Search.Provinsi.region_id');
		$city_id = Hash::get($data, 'Search.Kota.city_id');
		$subarea_id = Hash::get($data, 'Search.Area.subareas');
		
		$types = Hash::get($data, 'Search.Tipe.type');
		$status = Hash::get($data, 'Search.Status.status');

		$this->controller->request->data['Search']['region_id'] = $region_id;
		$this->controller->request->data['Search']['city_id'] = $city_id;
		$this->controller->request->data['Search']['title'] = $title;

        if( !empty($city_id) && !empty($region_id) ) {
            $subareas = $this->controller->User->Property->PropertyAddress->Subarea->getSubareas('list', $region_id, $city_id);

            if( !empty($subarea_id) ) {
				$this->controller->request->data['Search']['Area']['subareas'] = array_filter($subarea_id);
			}
            $this->controller->set(compact('subareas'));
        }

        if( !empty($types) ) {
			$this->controller->request->data['Search']['Tipe']['type'] = array_filter($types);
        }
        if( !empty($status) ) {
        	if( is_array($status) ) {
        		$status = array_filter($status);
        	}

			$this->controller->request->data['Search']['Status']['status'] = $status;
        }
	}

	function _callAddBeforeViewSummary () {
		$regions = $this->controller->User->UserProfile->Region->getData('list', array(
            'cache' => 'Region.List',
        ));

		$this->controller->set(array(
			'regions' => $regions, 
		));
	}

	function _callAddBeforeViewProperty () {
		$data = $this->controller->request->data;
		$params = $this->controller->params->params;

		if( empty($data) ) {

			$dateFrom = date('Y-m-d', strtotime('-1 Month'));
	        $dateTo = date('Y-m-d');

			$dateFrom = Common::hashEmptyField($params, 'named.date_from', $dateFrom);
			$dateTo = Common::hashEmptyField($params, 'named.date_to', $dateTo);

            $data['Search']['Periode']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
            $this->controller->request->data = $data;
		}

		$companies =  $this->RmCommon->_callCompanies();
		$this->RmProperty->_callSupportAdvancedSearch();

		$this->RmCommon->_layout_file('select2');
		$this->controller->set(array(
			'companies' => $companies, 
		));
	}

	function _callGroupByProperties ( $value, $params, $type ) {
		$result = array();

        $start_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_from', null);
        $end_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_to', null);
		$user_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id', null);

		$client_id = $this->RmCommon->filterEmptyField($value, 'Property', 'client_id');
		$principle_id = $this->RmCommon->filterEmptyField($value, 'User', 'parent_id');
		$value = $this->controller->User->UserClient->getMerge( $value, $client_id, $principle_id, 'Client' );
		$value = $this->controller->User->Property->getDataList($value, array(
            'contain' => array(
                'Client',
            ),
        ));
		$value = $this->RmCommon->dataConverter($value,array(
			'date' => array(
				'Property' => array(
					'created',
					'pub_date',
				),
			),
		), true);

		if( $type == 'view' ) {
			$divider = '<br>';
		} else {
			$divider = ',';
		}

		$mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id', '-');
		$price = $this->RmProperty->getPrice($value, '-');
		$created = $this->RmCommon->filterEmptyField($value, 'Property', 'created', '-');
		$pub_date = $this->RmCommon->filterEmptyField($value, 'Property', 'pub_date', '-');
		
		$name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name', '-');

		$visitor = $this->RmCommon->filterEmptyField($value, 'PropertyView', false, '-');
		$leads = $this->RmCommon->filterEmptyField($value, 'PropertyLead', false, '-');
		$msg = $this->RmCommon->filterEmptyField($value, 'Message', false, '-');

		$label = $this->RmProperty->getShortPropertyType($value, false);
		$status = $this->RmProperty->getStatus($value);
		$specs = $this->RmProperty->getSpesification($value, array(
			'to_string' => true,
			'empty' => '-',
		));
		$location = $this->RmProperty->getLocationName($value, $divider);
		$location = !empty($location)?$location:'-';
		$address = $this->RmCommon->filterEmptyField($value, 'PropertyAddress');
		$address = Common::getAddress($address, $divider, 'address', true, $divider);

		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $companyData = Configure::read('Config.Company.data');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id', null);

        $sold_date = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'sold_date', null);
		$end_date = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'end_date', null);
		$sold_date = $this->RmCommon->getCombineDate($sold_date, $end_date, '-', false, array(
			'divider' => 's/d',
		));

        $vendor = $this->RmCommon->filterEmptyField($value, 'Client', 'full_name', '-');
        $vendor_email = $this->RmCommon->filterEmptyField($value, 'Client', 'email', '-');
        $vendor_no_hp = $this->RmCommon->filterEmptyField($value, 'Client', 'no_hp');
        $vendor_no_hp2 = $this->RmCommon->filterEmptyField($value, 'Client', 'no_hp_2');
        $vendor_no_hp = Common::_callPhoneNumber($vendor_no_hp, $vendor_no_hp2, '-');

		if( !empty($admin_rumahku) || $group_id == 4 ) {
			$company = $this->RmCommon->filterEmptyField($value, 'User', 'UserCompany');
			$company_name = $this->RmCommon->filterEmptyField($company, 'name', false, '-');

			$result = array(
				__('Perusahaan') => array(
					'text' => $company_name,
					'width' => 15,
            		'field_model' => 'UserCompany.name',
	                'style' => 'text-align: left;vertical-align: middle;',
	                'data-options' => 'field:\'company_name\',width:200',
				),
			);
		}

		$result = array_merge($result, array(
			__('Agen') => array(
				'text' => $name,
				'width' => 25,
        		'field_model' => 'User.full_name',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'full_name\',width:150',
			),
			__('ID Properti') => array(
				'text' => $mls_id,
				'width' => 12,
        		'field_model' => 'Property.mls_id',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'mls_id\',width:100',
        		'fix_column' => !empty($admin_rumahku)?true:false,
			),
			__('Status') => array(
				'text' => !empty($status)?$status:null,
				'width' => 12,
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'Status\',width:100',
                'align' => 'center',
                'mainalign' => 'center',
			),
			__('Pengunjung') => array(
				'text' => !empty($visitor)?$visitor:'-',
				'width' => 12,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'visitor\',width:100',
                'align' => 'center',
                'mainalign' => 'center',
			),
			__('Leads') => array(
				'text' => !empty($leads)?$leads:'-',
				'width' => 12,
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'leads\',width:80',
                'align' => 'center',
                'mainalign' => 'center',
			),
			__('Hot Leads') => array(
				'text' => !empty($msg)?$msg:'-',
				'width' => 12,
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'msg\',width:80',
                'align' => 'center',
                'mainalign' => 'center',
			),
			__('Label') => array(
				'text' => !empty($label)?$label:'-',
				'width' => 25,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'label\',width:150',
        		'fix_column' => !empty($admin_rumahku)?false:true,
			),
			__('Harga') => array(
				'text' => $price,
				'width' => 20,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'price\',width:150',
                'align' => 'right',
                'mainalign' => 'center',
			),
			__('Alamat') => array(
				'text' => $address,
				'width' => 20,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'address\',width:150',
			),
			__('Lokasi') => array(
				'text' => $location,
				'width' => 20,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'location\',width:150',
			),
			__('Keterangan') => array(
				'text' => $specs,
				'width' => 25,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'spec\',width:150',
			),
			__('Tgl Dibuat') => array(
				'text' => $created,
				'width' => 12,
        		'field_model' => 'Property.created',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'created\',width:100',
			),
			__('Tgl Publish') => array(
				'text' => $pub_date,
				'width' => 12,
        		'field_model' => 'Property.pub_date',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'pub_date\',width:100',
			),
			__('Tgl Terjual/Tersewa') => array(
				'text' => $sold_date,
				'width' => 15,
        		'field_model' => 'PropertySold.sold_date',
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'sold_date\',width:150',
			),
			__('Vendor') => array(
				'text' => $vendor,
				'width' => 15,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'vendor\',width:150',
			),
			__('Email Vendor') => array(
				'text' => $vendor_email,
				'width' => 20,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'vendor_email\',width:200',
			),
			__('No. HP Vendor') => array(
				'text' => $vendor_no_hp,
				'width' => 15,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'vendor_no_hp\',width:150',
			),
		));

		return $result;
	}

	function _callGroupByCities ( $value ) {
		$value = $this->controller->User->Property->getMergeList($value, array(
			'contain' => array(
				'PropertyAddress' => array(
					'contain' => array(
						'Region' => array(
							'cache' => true,
						),
						'City' => array(
							'cache' => true,
						),
					),
				),
			),
		));

		$address = $this->RmCommon->filterEmptyField($value, 'PropertyAddress');
		$cnt_property = $this->RmCommon->filterEmptyField($value, 'Property', 'cnt_property');

		$city = $this->RmCommon->filterEmptyField($address, 'City', 'name', '-');
		$cnt_property = !empty($cnt_property)?$this->RmCommon->getFormatPrice($cnt_property):'-';

		$result = array(
			__('Kota') => array(
				'text' => $city,
				'width' => 25,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'city\',width:250',
			),
			__('Jml Properti') => array(
				'text' => $cnt_property,
				'width' => 12,
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'cnt_property\',width:100',
                'align' => 'center',
                'mainalign' => 'center',
			),
		);

		return $result;
	}

	function _callDataProperties ( $params, $offset = false, $limit = 30, $type = false ) {
		$this->controller->loadModel('Property');

		$status = Common::hashEmptyField($params, 'Search.status');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $group_by = $this->RmCommon->filterEmptyField($params, 'named', 'group_by');

		$options = array(
			'conditions' => array(),
			'order' => array(
				'Property.id' => 'DESC',
			),
            'offset' => $offset,
			'limit' => $limit,
		);

		if(!empty($status) && $status == 'sold'){
			$sold_date_from = Common::hashEmptyField($params, 'named.sold_date_from');
			$sold_date_to = Common::hashEmptyField($params, 'named.sold_date_to');

			$date_format = '%Y-%m-%d';
			if(!empty($sold_date_from)){
				$options['conditions']['DATE_FORMAT(PropertySold.sold_date, "'.$date_format.'") >='] = $sold_date_from;
			}
			if(!empty($sold_date_to)){
				$options['conditions']['DATE_FORMAT(PropertySold.sold_date, "'.$date_format.'") <='] = $sold_date_to;
			}

			$options['contain'] = array(
				'PropertySold',
				'User'
			);

			$options['conditions'][] = array(
				'CASE WHEN(Property.property_action_id = 1)THEN(PropertySold.status = 1)ELSE(PropertySold.status IN(0,1))END'
			);

			$this->controller->User->Property->unbindModel(
				array('hasOne' => array('PropertySold'))
			);

			$this->controller->User->Property->bindModel(array(
	            'hasOne' => array(
	                'PropertySold' => array(
	                    'className' => 'PropertySold',
	                    'foreignKey' => 'property_id',
	                ),
	            )
	        ), false);
		}

		$flag = !empty($admin_rumahku)?false:true;
		$options = $this->controller->User->Property->_callRefineParams($params, $options);

		switch ($group_by) {
			case 'cities':
				$this->controller->User->Property->virtualFields['cnt_property'] = 'COUNT(Property.id)';
				$options['contain'][] = 'PropertyAddress';
				$options['group'] = array(
					'PropertyAddress.city_id',
				);
				$options['order'] = array(
					'Property.cnt_property' => 'DESC',
				);
				break;
			default:
				$this->controller->User->Property->virtualFields['pub_date'] = 'IFNULL(Property.publish_date, Property.created)';
				break;
		}

		$this->controller->paginate	= $this->controller->User->Property->getData('paginate', $options, array(
			'status' => 'all',
			'parent' => $flag,
            'admin_mine' => $flag,
            'company' => $flag,
		));

		$data = $this->controller->paginate('Property');

		if(!empty($status) && $status == 'sold'){
			$this->controller->User->Property->unbindModel(
				array('hasOne' => array('PropertySold'))
			);

			$this->controller->User->Property->bindModel(array(
	            'hasOne' => array(
	                'PropertySold' => array(
	                    'className' => 'PropertySold',
	                    'foreignKey' => 'property_id',
	                    'conditions' => array(
							'PropertySold.status' => 1,
						),
	                ),
	            )
	        ), false);
		}

		$result = array();

		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'Property', 'id');

		if( !empty($data) ) {
			$totalListing = 0;
			$totalVisitor = 0;
			$totalMsg = 0;
			$totalLead = 0;
			$paging = $this->RmCommon->filterEmptyField($this->controller->params, 'paging', 'Property');
			$nextPage = $this->RmCommon->filterEmptyField($paging, 'nextPage');

			foreach ($data as $key => $value) {
				switch ($group_by) {
					case 'cities':
						$contentArr = $this->_callGroupByCities($value);
						$resultArr = $this->_callDataAPIConverter($contentArr);
						$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
						$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
						
						$cnt_property = $this->RmCommon->filterEmptyField($value, 'Property', 'cnt_property', 0);
						$totalListing += $cnt_property;
						break;
					
					default:
						$value = $this->controller->User->Property->getMergeList($value, array(
							'contain' => array(
								'User' => array(
									'contain' => array(
										'UserCompany' => array(
											'foreignKey' => 'parent_id',
											'primaryKey' => 'user_id',
										),
									),
								),
								'PropertyType' => array(
									'cache' => true,
								),
								'PropertyAction' => array(
									'cache' => true,
								),
								'Currency' => array(
									'cache' => true,
								),
								'PropertyAsset' => array(
									'contain' => array(
										'LotUnit' => array(
											'cache' => true,
										),
									),
								),
								'PropertyAddress' => array(
									'contain' => array(
										'Region' => array(
											'cache' => true,
										),
										'City' => array(
											'cache' => true,
										),
										'Subarea' => array(
											'cache' => true,
										),
									),
								),
								'PropertySold',
								'PropertyView' => array(
									'type' => 'count',
									// 'conditions' => array(
									// 	'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\') >=' => $start_date,
									// 	'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\') <=' => $end_date,
									// ),
								),
								'PropertyLead' => array(
									'type' => 'count',
									// 'conditions' => array(
									// 	'DATE_FORMAT(PropertyLead.created, \'%Y-%m-%d\') >=' => $start_date,
									// 	'DATE_FORMAT(PropertyLead.created, \'%Y-%m-%d\') <=' => $end_date,
									// ),
								),
								'Message' => array(
									'type' => 'count',
									// 'conditions' => array(
									// 	'Message.to_id' => $user_id,
									// 	'DATE_FORMAT(Message.created, \'%Y-%m-%d\') >=' => $start_date,
									// 	'DATE_FORMAT(Message.created, \'%Y-%m-%d\') <=' => $end_date,
									// ),
								),
							),
						));
						$contentArr = $this->_callGroupByProperties($value, $params, $type);
						$resultArr = $this->_callDataAPIConverter($contentArr);
						$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
						$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');

						$visitor = $this->RmCommon->filterEmptyField($value, 'PropertyView', false, '-');
						$leads = $this->RmCommon->filterEmptyField($value, 'PropertyLead', false, '-');
						$msg = $this->RmCommon->filterEmptyField($value, 'Message', false, '-');

						$totalVisitor += $visitor;
						$totalMsg += $msg;
						$totalLead += $leads;
						break;
				}
			}

			if( ($type == 'view' || empty($nextPage)) && !$this->Rest->isActive() ) {
				switch ($group_by) {
					case 'cities':
						$result[$key+1] = array(
							__('Kota') => array(
								'text' => __('Total'),
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'city\',width:250',
							),
							__('Jml Properti') => array(
								'text' => !empty($totalListing)?$this->RmCommon->getFormatPrice($totalListing):'-',
								'width' => 12,
				                'style' => 'text-align: left;vertical-align: middle;font-weight:bold;',
				                'data-options' => 'field:\'cnt_property\',width:100',
				                'align' => 'center',
				                'mainalign' => 'center',
							),
						);
						break;
					
					default:
						$result[$key+1] = array();
						$admin_rumahku = Configure::read('User.Admin.Rumahku');
				        $companyData = Configure::read('Config.Company.data');
				        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id', null);

						if( !empty($admin_rumahku) || $group_id == 4 ) {
							$company = $this->RmCommon->filterEmptyField($value, 'User', 'UserCompany');
							$company_name = $this->RmCommon->filterEmptyField($company, 'name', false, '-');

							$result[$key+1] = array(
								__('Perusahaan') => array(
					                'style' => 'text-align: left;vertical-align: middle;',
					                'data-options' => 'field:\'company_name\',width:200',
								),
							);
						}

						$result[$key+1] = array_merge($result[$key+1], array(
							__('Agen') => array(
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'full_name\',width:150',
							),
							__('ID Properti') => array(
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'mls_id\',width:100',
							),
							__('Status') => array(
				                'style' => 'text-align: center;vertical-align: middle;',
				                'data-options' => 'field:\'Status\',width:100',
				                'align' => 'center',
				                'mainalign' => 'center',
								'text' => __('Total'),
							),
							__('Pengunjung') => array(
								'text' => !empty($totalVisitor)?$totalVisitor:'-',
								'width' => 12,
				                'style' => 'text-align: left;vertical-align: middle;font-weight:bold;',
				                'data-options' => 'field:\'visitor\',width:100',
				                'align' => 'center',
				                'mainalign' => 'center',
							),
							__('Leads') => array(
								'text' => !empty($totalLead)?$totalLead:'-',
								'width' => 12,
				                'style' => 'text-align: center;vertical-align: middle;font-weight:bold;',
				                'data-options' => 'field:\'leads\',width:80',
				                'align' => 'center',
				                'mainalign' => 'center',
							),
							__('Hot Leads') => array(
								'text' => !empty($totalMsg)?$totalMsg:'-',
								'width' => 12,
				                'style' => 'text-align: center;vertical-align: middle;font-weight:bold;',
				                'data-options' => 'field:\'msg\',width:80',
				                'align' => 'center',
				                'mainalign' => 'center',
							),
							__('Label') => array(
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'label\',width:150',
			            		'fix_column' => true,
							),
							__('Harga') => array(
				                'style' => 'text-align: right;vertical-align: middle;',
				                'data-options' => 'field:\'price\',width:150',
							),
							__('Alamat') => array(
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'address\',width:150',
							),
							__('Lokasi') => array(
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'location\',width:150',
							),
							__('Keterangan') => array(
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'spec\',width:150',
							),
							__('Tgl Dibuat') => array(
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'created\',width:100',
							),
							__('Tgl Publish') => array(
								'width' => 12,
				                'style' => 'text-align: left;vertical-align: middle;font-weight:bold;',
				                'data-options' => 'field:\'pub_date\',width:100',
							),
							__('Tgl Terjual/Tersewa') => array(
								'width' => 15,
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'sold_date\',width:150',
							),
							__('Vendor') => array(
								'width' => 15,
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'vendor\',width:150',
							),
							__('Email Vendor') => array(
								'width' => 20,
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'vendor_email\',width:200',
							),
							__('No. HP Vendor') => array(
								'width' => 15,
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'vendor_no_hp\',width:150',
							),
						));
						break;
				}
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	function _callDataGraphicProperties ( $params ) {
		$this->controller->loadModel('Property');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $group_by = $this->RmCommon->filterEmptyField($params, 'named', 'group_by');

		$options = array();
		$this->controller->User->virtualFields['cnt'] = 'COUNT(Property.id)';

		$flag = !empty($admin_rumahku)?false:true;
		$options = $this->controller->User->Property->_callRefineParams($params, $options);

		switch ($group_by) {
			case 'cities':
				$options = array_merge($options, array(
					'conditions' => array(
						'City.name <>' => NULL,
					),
					'order' => array(
						'City.name' => 'asc',
						'Property.id' => 'DESC',
					),
					'group' => array(
						'PropertyAddress.city_id',
					),
					'fields' => array(
						'City.name',
						'Property.cnt',
					),
					'contain' => array(
						'PropertyAddress',
						'City',
					),
				));

				$this->controller->User->Property->bindModel(array(
		            'hasOne' => array(
		                'City' => array(
		                    'className' => 'City',
		                    'foreignKey' => false,
		                    'conditions' => array(
		                    	'PropertyAddress.city_id = City.id',
		                	),
		                ),
		            )
		        ), false);

				$this->controller->User->Property->virtualFields['cnt'] = 'COUNT(Property.id)';
				break;
			default:
				$this->controller->User->Property->virtualFields['cnt'] = 'COUNT(Property.id)';
				$this->controller->User->Property->virtualFields['publish_date'] = 'DATE_FORMAT(Property.publish_date, \'%Y-%m-%d\')';

				$options = array_merge($options, array(
					'conditions' => array(
						'Property.publish_date NOT' => '0000-00-00',
					),
					'order' => array(
						'Property.publish_date' => 'asc',
						'Property.id' => 'DESC',
					),
					'group' => array(
						'DATE_FORMAT(Property.publish_date, \'%Y-%m-%d\')',
					),
					'fields' => array(
						'Property.publish_date',
						'Property.cnt',
					),
				));
				break;
		}

		$data = $this->controller->User->Property->getData('list', $options, array(
			'status' => 'all',
			'parent' => $flag,
            'admin_mine' => $flag,
            'company' => $flag,
		));

		return array(
			'data' => $data,
		);
	}

	function _callAddBeforeViewVisitor () {
		$data = $this->controller->request->data;

		if( empty($data) ) {
	        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
	        $dateTo = date('Y-m-d');

            $data['Search']['Periode']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
            $this->controller->request->data = $data;
		}

		$companies =  $this->RmCommon->_callCompanies();
        $propertyActions = $this->controller->User->Property->PropertyAction->getData('list', array(
            'cache' => __('PropertyAction.List'),
        ));
        $propertyTypes = $this->controller->User->Property->PropertyType->getData('list', array(
            'cache' => __('PropertyType.List'),
        ));

		$this->RmCommon->_layout_file('select2');
		$this->controller->set(array(
			'companies' => $companies, 
			'propertyActions' => $propertyActions, 
			'propertyTypes' => $propertyTypes, 
		));
	}

	function _callDataVisitors ( $params, $offset = false, $limit = 30 ) {
		$this->controller->loadModel('PropertyView');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $start_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_from');
        $end_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_to');

		$options = array(
			'conditions' => array(),
			'order' => false,
            'offset' => $offset,
			'limit' => $limit,
		);

		$options = $this->controller->PropertyView->_callRefineParams($params, $options);
		$this->controller->paginate	= $this->controller->PropertyView->getData('paginate', $options, array(
			'mine' => true,
		));
		$data = $this->controller->paginate('PropertyView');
		$result = array();

		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'PropertyView', 'id');

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
				$value = $this->controller->PropertyView->getMergeList($value, array(
					'contain' => array(
						'Property' => array(
							'elements' => array(
								'status' => 'all',
								'company' => false,
							),
						),
					),
				));
				$value = $this->controller->User->Property->getMergeList($value, array(
					'contain' => array(
						'User' => array(
							'contain' => array(
								'UserCompany' => array(
									'foreignKey' => 'parent_id',
									'primaryKey' => 'user_id',
								),
							),
							'elements' => array(
								'status' => 'all',
							),
						),
						'PropertyType' => array(
							'cache' => true,
						),
						'PropertyAction' => array(
							'cache' => true,
						),
						'PropertyAsset',
						'Currency',
						'PropertyAddress' => array(
							'contain' => array(
								'Region' => array(
									'cache' => true,
								),
								'City' => array(
									'cache' => true,
								),
								'Subarea' => array(
									'cache' => true,
								),
							),
						),
					),
				));

				$value = $this->RmCommon->dataConverter($value,array(
					'date' => array(
						'PropertyView' => array(
							'created',
						),
					),
				), true);

				$mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id', '-');
				$created = $this->RmCommon->filterEmptyField($value, 'PropertyView', 'created', '-');
                $browser = $this->RmCommon->filterEmptyField($value, 'PropertyView', 'browser', '-');
                $utm = $this->RmCommon->filterEmptyField($value, 'PropertyView', 'utm', '-');
				$name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name', '-');

				// label
				$propertyAction = $this->RmCommon->filterEmptyField($value, 'PropertyAction', 'name', '-');
				$propertyType = $this->RmCommon->filterEmptyField($value, 'PropertyType', 'name', '-');
				$label = sprintf('%s %s', $propertyType, $propertyAction);

				// location
				$address = $this->RmCommon->filterEmptyField($value, 'PropertyAddress');
				$region = $this->RmCommon->filterEmptyField($address, 'Region', 'name', null);

				if( !empty($region) ) {
					$location = $this->RmProperty->getNameCustom($value, true);
				} else {
					$location = '-';
				}

				// price
				$currency = $this->RmCommon->filterEmptyField($value, 'Currency', 'symbol', '-');
				$price = $this->RmCommon->filterEmptyField($value, 'Property', 'price', '-', array(
					'type' => 'currency',
				));
				$property_price = sprintf('%s %s', $currency, $price);

				$contentArr = array();
				$admin_rumahku = Configure::read('User.Admin.Rumahku');
		        $companyData = Configure::read('Config.Company.data');
		        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id', null);

				if( !empty($admin_rumahku) || $group_id == 4 ) {
					$company = $this->RmCommon->filterEmptyField($value, 'User', 'UserCompany');
					$company_name = $this->RmCommon->filterEmptyField($company, 'name', false, '-');

					$contentArr = array(
						__('Perusahaan') => array(
							'text' => $company_name,
							'width' => 15,
	                		'field_model' => 'UserCompany.name',
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'company_name\',width:200',
						),
					);
				}

				$contentArr = array_merge($contentArr, array(
					__('Agen') => array(
						'text' => $name,
						'width' => 25,
                		'field_model' => 'User.full_name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'full_name\',width:150',
                		'fix_column' => !empty($admin_rumahku)?true:false,
					),
					__('ID Properti') => array(
						'text' => $mls_id,
						'width' => 12,
                		'field_model' => 'Property.mls_id',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'mls_id\',width:100',
                		'fix_column' => !empty($admin_rumahku)?false:true,
					),
					__('Label') => array(
						'text' => !empty($label)?$label:'-',
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'label\',width:150',
					),
					__('Lokasi') => array(
						'text' => $location,
						'width' => 20,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'location\',width:250',
					),					
					__('Harga') => array(
						'text' => !empty($property_price)?$property_price:'-',
						'width' => 20,
		                'style' => 'text-align: right;vertical-align: middle;',
		                'data-options' => 'field:\'property_price\',width:150',
		                'align' => 'right',
		                'mainalign' => 'right',
					),
					__('Tgl Kunjung') => array(
						'text' => $created,
						'width' => 12,
                		'field_model' => 'PropertyView.created',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'created\',width:100',
					),
					__('Browser') => array(
						'text' => $browser,
						'width' => 25,
                		'field_model' => 'PropertyView.browser',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'browser\',width:200',
					),
					__('UTM') => array(
						'text' => $utm,
						'width' => 25,
                		'field_model' => 'PropertyView.utm',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'utm\',width:250',
					),
				));

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	function _callDataGraphicVisitors ( $params ) {
		$this->controller->loadModel('PropertyView');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $start_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_from');
        $end_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_to');

		$options = array(
			'conditions' => array(),
			'order' => array(
				'PropertyView.created' => 'ASC',
				'PropertyView.id' => 'DESC',
			),
			'group' => array(
				'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\')',
			),
			'fields' => array(
				'PropertyView.created',
				'PropertyView.cnt',
			),
		);
		$this->controller->PropertyView->virtualFields['cnt'] = 'COUNT(PropertyView.id)';
		$this->controller->PropertyView->virtualFields['created'] = 'DATE_FORMAT(PropertyView.created, \'%Y-%m-%d\')';

		$options = $this->controller->PropertyView->_callRefineParams($params, $options);
		$data = $this->controller->PropertyView->getData('list', $options, array(
			'mine' => true,
		));

		return array(
			'data' => $data,
		);
	}

	function _callAddBeforeViewMessage () {
		$data = $this->controller->request->data;

		if( empty($data) ) {
	        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
	        $dateTo = date('Y-m-d');

            $data['Search']['Periode']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
            $this->controller->request->data = $data;
		}

		$companies =  $this->RmCommon->_callCompanies();

		$this->RmCommon->_layout_file('select2');
		$this->controller->set(array(
			'companies' => $companies, 
		));
	}

	function _callDataMessages ( $params, $offset = false, $limit = 30, $type = false ) {
		App::uses('HtmlHelper', 'View/Helper');
       	$this->Html = new HtmlHelper(new View(null));

		$this->controller->loadModel('Message');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $user_login_id = Configure::read('User.id');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $start_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_from');
        $end_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_to');
        $include_me = $this->RmCommon->filterEmptyField($params, 'named', 'include_me');

		$options = array(
			'conditions' => array(),
			'order' => array(
				'Message.id' => 'DESC',
			),
            'offset' => $offset,
			'limit' => $limit,
		);

		if( empty($include_me) && empty($admin_rumahku) ) {
			$options['conditions']['Message.from_id NOT'] = $user_login_id;
		}

		$flag = !empty($admin_rumahku)?false:true;
		$options = $this->controller->Message->_callRefineParams($params, $options);
		$this->controller->paginate	= $this->controller->Message->getData('paginate', $options, array(
			'mine' => $flag,
		));

		$data = $this->controller->paginate('Message');
		$result = array();

		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'Message', 'id');

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
				$value = $this->controller->Message->getMergeList($value, array(
					'contain' => array(
						'From' => array(
							'uses' => 'User',
							'primaryKey' => 'id',
							'foreignKey' => 'from_id',
						),
						'To' => array(
							'uses' => 'User',
							'primaryKey' => 'id',
							'foreignKey' => 'to_id',
						),
						'Property' => array(
							'contain' => array(
								'User',
							),
							'elements' => array(
								'company' => false,
								'status' => false,
							),
						),
					),
				));

				$value = $this->RmCommon->dataConverter($value,array(
					'date' => array(
						'Message' => array(
							'created',
						),
					),
				), true);

				$dataProperty = $this->RmCommon->filterEmptyField($value, 'Property');
				$parent_id = Common::hashEmptyField($value, 'Property.User.parent_id');

				$value = $this->controller->User->UserCompanyConfig->getMerge($value, $parent_id);
				$domain = $this->RmCommon->filterEmptyField($value, 'UserCompanyConfig', 'domain');

				$mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id');
				$created = $this->RmCommon->filterEmptyField($value, 'Message', 'created', '-');
				$name = $this->RmCommon->filterEmptyField($value, 'Message', 'name', null);
				$email = $this->RmCommon->filterEmptyField($value, 'Message', 'email', null);
				$phone = $this->RmCommon->filterEmptyField($value, 'Message', 'phone', null);
				$msg = $this->RmCommon->filterEmptyField($value, 'Message', 'message', null);
				$utm = $this->RmCommon->filterEmptyField($value, 'Message', 'utm', null);
				$receiveName = $this->RmCommon->filterEmptyField($value, 'To', 'full_name', null);

				$slug = $this->RmProperty->getNameCustom($value, false, true);
				$url_property = $domain.$this->Html->url(array(
                    'controller'=> 'properties',
                    'action' => 'detail',
                    'mlsid' => $mls_id,
                    'slug'=> $slug,
                    'admin'=> false,
                ));

				$mls_id = !empty($mls_id)?$this->Html->link($mls_id, $url_property, array(
					'target' => '_blank',
				)):'-';

				$contentArr = array(
					__('ID Properti') => array(
						'text' => $mls_id,
						'width' => 12,
                		'field_model' => 'Property.mls_id',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'mls_id\',width:100',
					),
					__('Tgl Dikirim') => array(
						'text' => $created,
						'width' => 12,
                		'field_model' => 'Message.created',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'created\',width:100',
					),
					__('Nama') => array(
						'text' => $name,
						'width' => 25,
                		'field_model' => 'Message.name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'name\',width:250',
                		'fix_column' => true,
					),
					__('Email') => array(
						'text' => $email,
						'width' => 25,
                		'field_model' => 'Message.email',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'email\',width:300',
					),
					__('Penerima') => array(
						'text' => $receiveName,
						'width' => 25,
                		'field_model' => 'Message.to_id',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'to_id\',width:300',
					),
					__('No. HP') => array(
						'text' => $phone,
						'width' => 25,
                		'field_model' => 'Message.phone',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'phone\',width:200',
					),
					__('Pesan') => array(
						'text' => $msg,
						'width' => 35,
                		'field_model' => 'Message.message',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'message\',width:250',
					),
					__('UTM') => array(
						'text' => $utm,
						'width' => 25,
                		'field_model' => 'Message.utm',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'utm\',width:300',
					),
				);
				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	function _callDataGraphicMessages ( $params ) {
		$this->controller->loadModel('Message');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $user_login_id = Configure::read('User.id');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $start_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_from');
        $end_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_to');
        $include_me = $this->RmCommon->filterEmptyField($params, 'named', 'include_me');

		$options = array(
			'conditions' => array(),
			'order' => array(
				'Message.created' => 'ASC',
				'Message.id' => 'DESC',
			),
			'group' => array(
				'DATE_FORMAT(Message.created, \'%Y-%m-%d\')',
			),
			'fields' => array(
				'Message.created',
				'Message.cnt',
			),
		);
		$this->controller->Message->virtualFields['cnt'] = 'COUNT(Message.id)';
		$this->controller->Message->virtualFields['created'] = 'DATE_FORMAT(Message.created, \'%Y-%m-%d\')';

		if( empty($include_me) && empty($admin_rumahku) ) {
			$options['conditions']['Message.from_id NOT'] = $user_login_id;
		}

		$flag = !empty($admin_rumahku)?false:true;
		$options = $this->controller->Message->_callRefineParams($params, $options);
		$data = $this->controller->Message->getData('list', $options, array(
			'mine' => $flag,
		));

		return array(
			'data' => $data,
		);
	}

	function _callDataKprs ( $params, $offset = false, $limit = 30 ) {
		$this->controller->loadModel('KprBank');

		$globalData = Configure::read('Global.Data');
		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $start_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_from');
        $end_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_to');
        $genderOptions = $this->RmCommon->filterEmptyField($globalData, 'gender_options');

		$options = array(
			'conditions' => array(
				'KprBank.type' => 'kpr',
				'KprBank.type_log' => 'apply_kpr',
			),
			'order' => array(
				'KprBank.id' => 'DESC',
			),
			'contain' => array(
				'Kpr',
			),
            'offset' => $offset,
			'limit' => $limit,
		);

		$options = $this->controller->KprBank->Kpr->_callRefineParams($params, $options);
		$options = $this->controller->KprBank->_callRefineParams($params, $options);
		$this->controller->paginate	= $this->controller->KprBank->Kpr->getData('paginate', $options, array(
			'admin_mine' => !empty($admin_rumahku)?false:true,
			'company' => !empty($admin_rumahku)?false:true,
			'status' => 'application',
		));

		$data = $this->controller->paginate('KprBank');
		$result = array();

		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'KprBank', 'id');

		$categories = $this->controller->User->CrmProject->CrmProjectDocument->DocumentCategory->getData( 'all', array(
			'conditions' => array(
				'DocumentCategory.is_required' => 1,
				'DocumentCategory.id <>' => array(3, 7, 19, 20),
			),
			'order' => array(
				'DocumentCategory.order' => 'ASC',
				'DocumentCategory.id' => 'ASC',
			),
		));

		if( !empty($data) ) {
			$i = 0;
			foreach ($data as $key => $value) {
				$kpr_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'kpr_id', null);
				$kpr_bank_id = $this->RmCommon->filterEmptyField($value, 'KprBank', 'id', null);
				$bank_apply_category_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'bank_apply_category_id', null);
				$company_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'company_id', null);
				$value = $this->controller->KprBank->getMergeList($value, array(
					'contain' => array(
						'Bank',
						'KprBankInstallment' => array(
							'order'=> array(
								'KprBankInstallment.status_confirm' => 'DESC',
								'KprBankInstallment.modified' => 'DESC',
							),
						),
					),
				));

				$value['KprApplication'] = $this->controller->KprBank->Kpr->KprApplication->getData('all', array(
					'conditions' => array(
						'KprApplication.kpr_id' => $kpr_id,
					),
					'order'=> array(
						'KprApplication.parent_id' => 'ASC',
						'KprApplication.created' => 'ASC',
					),
				));
				$value = $this->controller->KprBank->Kpr->BankApplyCategory->getMerge($value, $bank_apply_category_id);
				$value = $this->controller->User->UserCompany->getMerge($value, $company_id);
				$value = $this->controller->User->Kpr->getMergeList($value, array(
					'contain' => array(
						'User' => array('Kpr', 'agent_id', true, 'AgentProperty'),
						'Property' => array('Kpr','property_id'),
					)
				));
				$value = $this->controller->User->Property->getMergeList($value, array(
					'contain' => array(
						'PropertyAddress' => array(
							'contain' => array(
								'Region' => array(
									'cache' => true,
								),
								'City' => array(
									'cache' => true,
								),
							),
						),
					),
				));

				$installment = !empty($value['KprBankInstallment'][0])?$value['KprBankInstallment'][0]:null;
				$client = !empty($value['KprApplication'][0])?$value['KprApplication'][0]:null;
				$spouse = !empty($value['KprApplication'][1])?$value['KprApplication'][1]:null;
				$client_job_type_id = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'job_type_id', null);
				$property_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'property_id', null);

				$documents = $this->RmKpr->getDocumentSort( array(
					'DocumentCategory.is_required' => 1,
					'DocumentCategory.id <>' => array(3, 7, 19, 20),
				), array(
					'id' => $kpr_id, 
					'kpr_bank_id' => $kpr_bank_id,
					'property_id' => $property_id,
					'document_type' => 'kpr_application',
				));
				$documents = $this->RmKpr->getDocumentsByCategory($documents);

				$client = $this->controller->KprBank->Kpr->KprApplication->JobType->getMerge($client, $client_job_type_id);
				$client = $this->RmCommon->dataConverter($client,array(
					'date' => array(
						'KprApplication' => array(
							'birthday',
						),
					),
				), true);
				$value = $this->RmCommon->dataConverter($value,array(
					'date' => array(
						'Kpr' => array(
							'created',
						),
					),
				), true);

				$created = $this->RmCommon->filterEmptyField($value, 'Kpr', 'created', '-');
				$document_status = $this->RmCommon->filterEmptyField($value, 'Kpr', 'document_status', null);
        		$status = $this->RmKpr->_callStatus( $document_status );
				$bank_code = $this->RmCommon->filterEmptyField($value, 'Bank', 'code', null);
				$bank_name = $this->RmCommon->filterEmptyField($value, 'Bank', 'name', null);
				$category = $this->RmCommon->filterEmptyField($value, 'BankApplyCategory', 'code', null);
				
				$mls_id = $this->RmCommon->filterEmptyField($value, 'Kpr', 'mls_id', '-');
				$dataAddress = $this->RmCommon->filterEmptyField($value, 'PropertyAddress');
				$region = $this->RmCommon->filterEmptyField($value, 'Region', 'name', null);
				$city = $this->RmCommon->filterEmptyField($value, 'City', 'name', null);

				if( !empty($region) ) {
					$location = array();

					if( !empty($city) ) {
						$location[] = $city;
					}

					$location[] = $region;

					if( !empty($location) ) {
						$location = implode(', ', $location);
					}
				}
				
				$property_price = $this->RmCommon->filterEmptyField($installment, 'KprBankInstallment', 'property_price', '-', array(
					'type' => 'currency',
				));
				$loan_price = $this->RmCommon->filterEmptyField($installment, 'KprBankInstallment', 'loan_price', '-', array(
					'type' => 'currency',
				));
				$credit_total = $this->RmCommon->filterEmptyField($installment, 'KprBankInstallment', 'credit_total', null);
				
				$company = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'name', '-');

				$agent_name = $this->RmCommon->filterEmptyField($value, 'AgentProperty', 'full_name', null);
				$agent_email = $this->RmCommon->filterEmptyField($value, 'AgentProperty', 'email', null);

				$name = $this->RmCommon->filterEmptyField($value, 'Kpr', 'client_name', '-');
				$name = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'full_name', $name);
				
				$email = $this->RmCommon->filterEmptyField($value, 'Kpr', 'client_email', '-');
				$email = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'email', $email);

				$gender_id = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'gender_id', null);
				$gender = $this->RmCommon->filterEmptyField($genderOptions, $gender_id);
				$phone = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'phone', '-');
				
				$no_hp = $this->RmCommon->filterEmptyField($value, 'Kpr', 'client_hp', null);
				$no_hp = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'no_hp', $no_hp);
				
				$no_hp_2 = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'no_hp_2', null);
				$ktp = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'ktp', '-');
				$birthplace = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'birthplace', '-');
				$birthday = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'birthday', '-');
				$address = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'address', '-');
				$address_2 = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'address_2', '-');
				$same_as_address_ktp = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'same_as_address_ktp', '-');
				$client_company = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'company', '-');
				$job_type = $this->RmCommon->filterEmptyField($client, 'JobType', 'company', '-');
				$income = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'income', '-', array(
					'type' => 'currency',
				));
				$household_fee = $this->RmCommon->filterEmptyField($client, 'KprApplication', 'household_fee', '-', array(
					'type' => 'currency',
				));

				if( empty($no_hp) ) {
					$no_hp = '-';
				} else if( !empty($no_hp) && !empty($no_hp_2) ) {
					$no_hp = __('%s / %s', $no_hp, $no_hp_2);
				}

				$codeKpr = $this->RmCommon->filterEmptyField($value, 'Kpr', 'code', '-');
				$code = $this->RmCommon->filterEmptyField($value, 'KprBank', 'code', '-');

  				$from_web = $this->RmCommon->filterEmptyField($value, 'KprBank', 'from_web', '-');
				$status_marital = $this->RmCommon->filterEmptyField($value, 'KprBank', 'status_marital', 'single');
				$status_marital = $this->RmCommon->filterEmptyField($globalData, 'status_marital', $status_marital);

				if( !empty($bank_code) ) {
					$contentArr = array();

					if( !empty($admin_rumahku) ) {
						$contentArr = array(
							__('Perusahaan') => array(
								'text' => $company,
								'width' => 15,
		                		'field_model' => 'UserCompany.name',
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\'company_name\',width:150',
							),
						);
					}

					$contentArr = array_merge($contentArr, array(
						__('Kode KPR') => array(
							'text' => $codeKpr,
							'width' => 25,
	                		'field_model' => 'KprBank.code',
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'code_kpr\',width:150',
						),
						__('Tgl Pengajuan') => array(
							'text' => $created,
							'width' => 15,
	                		'field_model' => 'Kpr.created',
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'created\',width:100',
						),
						__('Bank') => array(
							'text' => __('%s (%s)', $bank_name, $bank_code),
							'width' => 25,
	                		'field_model' => 'Bank.name',
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'bank\',width:150',
	                		'fix_column' => !empty($admin_rumahku)?true:false,
						),
						__('Kode Aplikasi') => array(
							'text' => $code,
							'width' => 25,
	                		'field_model' => 'KprBank.code',
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'code\',width:150',
	                		'fix_column' => !empty($admin_rumahku)?false:true,
						),
						__('Jenis') => array(
							'text' => $category,
							'width' => 12,
	                		'field_model' => 'Kpr.bank_apply_category_id',
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'bank_apply_category_id\',width:80',
			                'align' => 'center',
			                'mainalign' => 'center',
						),
						__('Asal Aplikasi') => array(
							'text' => $from_web,
							'width' => 25,
	                		'field_model' => 'KprBank.from_web',
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'from_web\',width:120',
			                'align' => 'center',
			                'mainalign' => 'center',
						),
						__('Agen') => array(
							'text' => $agent_name,
							'width' => 25,
	                		'field_model' => 'AgentProperty.full_name',
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'agent\',width:150',
						),
						__('Email Agen') => array(
							'text' => $agent_email,
							'width' => 25,
	                		'field_model' => 'AgentProperty.email',
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'agent_email\',width:250',
						),
						__('ID Properti') => array(
							'text' => $mls_id,
							'width' => 15,
	                		'field_model' => 'Kpr.mls_id',
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'mls_id\',width:100',
			                'align' => 'center',
			                'mainalign' => 'center',
						),
						__('Lokasi') => array(
							'text' => !empty($location)?$location:'-',
							'width' => 25,
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'location\',width:100',
						),
						__('Harga (%s)', Configure::read('__Site.config_currency_symbol')) => array(
							'text' => !empty($property_price)?$property_price:'-',
							'width' => 25,
			                'style' => 'text-align: right;vertical-align: middle;',
			                'data-options' => 'field:\'property_price\',width:150',
			                'align' => 'right',
			                'mainalign' => 'right',
						),
						__('Nilai Pengajuan (%s)', Configure::read('__Site.config_currency_symbol')) => array(
							'text' => !empty($loan_price)?$loan_price:'-',
							'width' => 25,
			                'style' => 'text-align: right;vertical-align: middle;',
			                'data-options' => 'field:\'loan_price\',width:150',
			                'align' => 'right',
			                'mainalign' => 'right',
						),
						__('Lama Pinjaman (Thn.)') => array(
							'text' => !empty($credit_total)?$credit_total:'-',
							'width' => 25,
			                'style' => 'text-align: right;vertical-align: middle;',
			                'data-options' => 'field:\'credit_total\',width:150',
			                'align' => 'center',
			                'mainalign' => 'center',
						),
						__('Klien') => array(
							'text' => $name,
							'width' => 25,
			                'style' => 'text-align: right;vertical-align: middle;',
			                'data-options' => 'field:\'client\',width:150',
						),
						__('Email Klien') => array(
							'text' => $email,
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'email\',width:250',
						),
						__('KTP') => array(
							'text' => $ktp,
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'ktp\',width:150',
						),
						__('Jenis Kelamin') => array(
							'text' => $gender,
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'gender\',width:150',
						),
						__('Status Menikah') => array(
							'text' => $status_marital,
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'status_marital\',width:150',
						),
						__('Tempat Lahir') => array(
							'text' => $birthplace,
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'birthplace\',width:150',
						),
						__('Tgl Lahir') => array(
							'text' => $birthday,
							'width' => 15,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'birthday\',width:100',
						),
						__('Alamat Sesuai KTP') => array(
							'text' => $address,
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'address\',width:250',
						),
						__('Alamat Domisili') => array(
							'text' => $address_2,
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'address_2\',width:250',
						),
						__('No Telp') => array(
							'text' => $phone,
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'phone\',width:150',
						),
						__('No Handphone') => array(
							'text' => $no_hp,
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'no_hp\',width:150',
						),
						__('Perusahaan (Tempat Bekerja)') => array(
							'text' => $client_company,
							'width' => 30,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'client_company\',width:250',
						),
						__('Jenis Pekerjaan') => array(
							'text' => $job_type,
							'width' => 20,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'job_type\',width:150',
						),
						__('Penghasilan') => array(
							'text' => !empty($income)?$income:'-',
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'income\',width:150',
			                'align' => 'right',
			                'mainalign' => 'right',
						),
						__('Pengeluaran') => array(
							'text' => !empty($household_fee)?$household_fee:'-',
							'width' => 25,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'household_fee\',width:150',
			                'align' => 'right',
			                'mainalign' => 'right',
						),
					));

					if( !empty($categories) ) {
						foreach ($categories as $idx => $cat) {
							$cat_slug = $this->RmCommon->filterEmptyField($cat, 'DocumentCategory', 'slug', null);
							$cat_name = $this->RmCommon->filterEmptyField($cat, 'DocumentCategory', 'name', null);

							$contentArr = array_merge($contentArr, array(
								$cat_name => array(
									'text' => !empty($documents[$cat_slug])?'v':'x',
									'width' => 25,
					                'style' => 'text-align: center;vertical-align: middle;',
					                'data-options' => __('field:\'%s\',width:150', $cat_slug),
					                'align' => 'center',
					                'mainalign' => 'center',
								),
							));
						}
					}

					$contentArr = array_merge($contentArr, array(
						__('Status') => array(
							'text' => $status,
							'width' => 20,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'status\',width:120',
			                'align' => 'center',
			                'mainalign' => 'center',
						),
					));

					$resultArr = $this->_callDataAPIConverter($contentArr);
					$result[$i] = $this->RmCommon->filterEmptyField($resultArr, 'data');
					$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');

					$i++;
				}
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	function _callDataGraphicKprs ( $params ) {
		$this->controller->loadModel('KprBank');

		$globalData = Configure::read('Global.Data');
		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $start_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_from');
        $end_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_to');
        $genderOptions = $this->RmCommon->filterEmptyField($globalData, 'gender_options');

		$options = array(
			'conditions' => array(
			),
			'order' => array(
				'KprBank.created' => 'ASC',
				'KprBank.id' => 'DESC',
			),
			'contain' => array(
				'Kpr',
			),
			'group' => array(
				'DATE_FORMAT(KprBank.created, \'%Y-%m-%d\')',
			),
			'fields' => array(
				'KprBank.created',
				'KprBank.cnt',
			),
		);
		$this->controller->KprBank->virtualFields['cnt'] = 'COUNT(KprBank.id)';
		$this->controller->KprBank->virtualFields['created'] = 'DATE_FORMAT(KprBank.created, \'%Y-%m-%d\')';

		$options = $this->controller->KprBank->Kpr->_callRefineParams($params, $options);
		$options = $this->controller->KprBank->_callRefineParams($params, $options);
		$options = $this->controller->KprBank->Kpr->getData('paginate', $options, array(
			'company' => !empty($admin_rumahku)?false:true,
		));
		$data = $this->controller->KprBank->find('list', $options);

		return array(
			'data' => $data,
		);
	}

	function _callDataCommissions ( $params, $offset = false, $limit = 30, $type = false ) {
		$this->controller->loadModel('Property');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');

        if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $start_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_from');
        $end_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_to');

		$options = array(
			'conditions' => array(),
			'contain' => array(
				'PropertySold',
			),
			'order' => array(
				'Property.id' => 'DESC',
			),
            'offset' => $offset,
			'limit' => $limit,
		);

		$flag = !empty($admin_rumahku)?false:true;
		$params = $this->RmCommon->_callUnset(array(
			'named' => array(
				'date_from',
				'date_to',
			),
		), $params);

		if( !empty($start_date) ) {
			$options['conditions']['DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') >='] = $start_date;

			if( !empty($end_date) ) {
				$options['conditions']['DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') <='] = $end_date;
			}
		}

		$options = $this->controller->User->Property->_callRefineParams($params, $options);
		$this->controller->paginate	= $this->controller->User->Property->getData('paginate', $options, array(
			'status' => 'sold',
			'parent' => $flag,
            'admin_mine' => $flag,
            'company' => $flag,
		));
		$data = $this->controller->paginate('Property');
		$result = array();

		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'Property', 'id');

		if( !empty($data) ) {
			$totalPriceSold = 0;
			$totalCommission = 0;
			$totalRoyalty = 0;
			$paging = $this->RmCommon->filterEmptyField($this->controller->params, 'paging', 'Property');
			$nextPage = $this->RmCommon->filterEmptyField($paging, 'nextPage');

			foreach ($data as $key => $value) {
				$user_id = $this->RmCommon->filterEmptyField($value, 'Property', 'user_id', null);

				$value = $this->controller->User->Property->getMergeList($value, array(
					'contain' => array(
						'Currency' => array(
							'cache' => true,
						),
						'User' => array(
							'contain' => array(
								'UserCompany' => array(
									'foreignKey' => 'parent_id',
									'primaryKey' => 'user_id',
								),
							),
						),
						'PropertyType' => array(
							'cache' => true,
						),
						'PropertyAction' => array(
							'cache' => true,
						),
						'PropertyAsset',
						'PropertyAddress' => array(
							'contain' => array(
								'Region' => array(
									'cache' => true,
								),
								'City' => array(
									'cache' => true,
								),
							),
						),
						'PropertySold' => array(
							'contain' => array(
								'Currency' => array(
									'cache' => true,
								),
							),
						),
					),
				));

				$mls_id = $this->RmCommon->filterEmptyField($value, 'Property', 'mls_id', null);
				
				$price_sold = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'price_sold', 0);
				$agent_commission_net = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'agent_commission_net', 0);
				$company_commission = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'company_commission', 0);
				
				$commission = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'commission', 0);
				$sharingtocompany_percentage = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'sharingtocompany_percentage', 0);
				$royalty_percentage = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'royalty_percentage', 0);
				$pph_percentage = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'pph_percentage', 0);
				
				$sold_date = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'sold_date', null);
				$end_date = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'end_date', null);
				$sold_date = $this->RmCommon->getCombineDate($sold_date, $end_date, '-', false, array(
					'divider' => 's/d',
				));

				$type_commision = $this->RmCommon->getGlobalVariable('type_commision_cobroke');

				$bt_commission = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'bt_commission', null);
				$bt_commission_percentage = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'bt_commission_percentage', null);
				$bt_type_commission = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'bt_type_commission', null);
				
				$bt_text = '-';
				if(!empty($bt_commission) && !empty($bt_commission_percentage) && !empty($bt_type_commission)){
					$text_type_commission = '';

					if(isset($type_commision[$bt_type_commission])){
						$text_type_commission = sprintf('<br><small>%s%% dari %s</small>', $bt_commission_percentage, $type_commision[$bt_type_commission]);
					}

					$bt_text = sprintf('%s%s', $this->RmCommon->getFormatPrice($bt_commission), $text_type_commission);
				}

				$broker_commission = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'broker_commission', null);
				$broker_percentage = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'broker_percentage', null);
				$broker_type_commision = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'broker_type_commision', null);
				$broker_type_price_commission = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'broker_type_price_commission', null);

				$broker_text = '-';
				if(!empty($broker_commission) && !empty($broker_type_commision)){
					$text_type_commission = '';

					if(isset($type_commision[$broker_type_commision]) && $broker_type_price_commission == 'percentage'){
						$text_type_commission = sprintf('<br><small>%s%% dari %s</small>', $broker_percentage, $type_commision[$broker_type_commision]);
					}

					$broker_text = sprintf('%s%s', $this->RmCommon->getFormatPrice($broker_commission), $text_type_commission);
				}
				
				$name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name', null);
				$address = $this->RmCommon->filterEmptyField($value, 'PropertyAddress');
				$region = $this->RmCommon->filterEmptyField($address, 'Region', 'name', null);

				if( !empty($price_sold) ) {
					$price_sold = $this->RmProperty->getMeasurePrice($value);
				}

				$totalPriceSold += $price_sold;
				$totalCommission += $agent_commission_net;
				$totalRoyalty += $company_commission;

				$label = $this->RmProperty->getNameCustom($value);

				if( !empty($region) ) {
					$city = $this->RmCommon->filterEmptyField($address, 'City', 'name', '-');
					$location = __('%s, %s', $city, $region);
				} else {
					$location = '-';
				}

				$contentArr = array();
				$admin_rumahku = Configure::read('User.Admin.Rumahku');

				if( !empty($admin_rumahku) ) {
					$company = $this->RmCommon->filterEmptyField($value, 'User', 'UserCompany');
					$company_name = $this->RmCommon->filterEmptyField($company, 'name', false, '-');

					$contentArr = array(
						__('Perusahaan') => array(
							'text' => $company_name,
							'width' => 15,
	                		'field_model' => 'UserCompany.name',
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'company_name\',width:200',
						),
					);
				}

				$contentArr = array_merge($contentArr, array(
					__('ID Properti') => array(
						'text' => $mls_id,
						'width' => 12,
                		'field_model' => 'Property.mls_id',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'mls_id\',width:100',
					),
					__('Label') => array(
						'text' => $label,
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'label\',width:150',
                		'fix_column' => !empty($admin_rumahku)?true:false,
					),
					__('Lokasi') => array(
						'text' => $location,
						'width' => 20,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'location\',width:150',
                		'fix_column' => !empty($admin_rumahku)?false:true,
					),
					__('Agen') => array(
						'text' => $name,
						'width' => 25,
                		'field_model' => 'User.full_name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'full_name\',width:150',
					),
					__('Harga Terjual/Tersewa (%s)', Configure::read('__Site.config_currency_symbol')) => array(
						'text' => !empty($price_sold)?$this->RmCommon->getFormatPrice($price_sold):'-',
						'width' => 15,
                		'field_model' => 'PropertySold.price_sold',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'price_sold\',width:150',
		                'align' => 'right',
		                'mainalign' => 'right',
					),
					__('Tgl Terjual/Tersewa') => array(
						'text' => $sold_date,
						'width' => 15,
                		'field_model' => 'PropertySold.sold_date',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'sold_date\',width:150',
					),
					__('Komisi Agen (%)') => array(
						'text' => !empty($commission)?$commission:'-',
						'width' => 15,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'commission\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Sharing To Company (%)') => array(
						'text' => !empty($sharingtocompany_percentage)?$sharingtocompany_percentage:'-',
						'width' => 20,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'sharingtocompany_percentage\',width:150',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Royalty (%)') => array(
						'text' => !empty($royalty_percentage)?$royalty_percentage:'-',
						'width' => 12,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'royalty_percentage\',width:120',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('PPH (%)') => array(
						'text' => !empty($pph_percentage)?$pph_percentage:'-',
						'width' => 12,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'leads\',width:120',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Komisi BT (Rp. )') => array(
						'text' => $bt_text,
						'width' => 12,
		                'style' => 'text-align: right;vertical-align: middle;',
		                'data-options' => 'field:\'bt\',width:180',
		                'align' => 'right',
					),
					__('Komisi Co-Broke (Rp. )') => array(
						'text' => $broker_text,
						'width' => 12,
		                'style' => 'text-align: right;vertical-align: middle;',
		                'data-options' => 'field:\'cobroke\',width:180',
		                'align' => 'right',
					),
					__('Total Komisi (%s)', Configure::read('__Site.config_currency_symbol')) => array(
						'text' => !empty($agent_commission_net)?$this->RmCommon->getFormatPrice($agent_commission_net):'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'msg\',width:150',
		                'align' => 'right',
		                'mainalign' => 'right',
					),
					__('Total Komisi Perusahaan (%s)', Configure::read('__Site.config_currency_symbol')) => array(
						'text' => !empty($company_commission)?$this->RmCommon->getFormatPrice($company_commission):'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'Status\',width:250',
		                'align' => 'right',
		                'mainalign' => 'right',
					),
				));

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
			}

			if( ($type == 'view' || empty($nextPage)) && !$this->Rest->isActive() ) {
				$result[$key+1] = array();
				$admin_rumahku = Configure::read('User.Admin.Rumahku');

				if( !empty($admin_rumahku) ) {
					$company = $this->RmCommon->filterEmptyField($value, 'User', 'UserCompany');
					$company_name = $this->RmCommon->filterEmptyField($company, 'name', false, '-');

					$result[$key+1] = array(
						__('Perusahaan') => array(
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'company_name\',width:200',
						),
					);
				}

				$result[$key+1] = array_merge($result[$key+1], array(
					__('ID Properti') => array(
						'width' => 12,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'mls_id\',width:100',
					),
					__('Label') => array(
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'label\',width:150',
					),
					__('Lokasi') => array(
						'width' => 20,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'location\',width:150',
					),
					__('Agen') => array(
						'text' => __('Total'),
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'full_name\',width:150',
					),
					__('Harga Terjual/Tersewa') => array(
						'text' => !empty($totalPriceSold)?$this->RmCommon->getFormatPrice($totalPriceSold):'-',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'price_sold\',width:150',
		                'align' => 'right',
		                'mainalign' => 'right',
					),
					__('Tgl Terjual/Tersewa (%s)', Configure::read('__Site.config_currency_symbol')) => array(
						'width' => 15,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'sold_date\',width:150',
					),
					__('Komisi Agen (%)') => array(
						'width' => 15,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'commission\',width:150',
					),
					__('Sharing To Company (%)') => array(
						'width' => 20,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'sharingtocompany_percentage\',width:150',
					),
					__('Royalty (%)') => array(
						'width' => 12,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'royalty_percentage\',width:120',
					),
					__('PPH (%)') => array(
						'width' => 12,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'leads\',width:120',
					),
					__('Komisi BT (Rp. )') => array(
						'width' => 12,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'bt\',width:180',
					),
					__('Komisi Co-Broke (Rp. )') => array(
						'width' => 12,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'cobroke\',width:180',
					),
					__('Total Komisi (%s)', Configure::read('__Site.config_currency_symbol')) => array(
						'text' => !empty($totalCommission)?$this->RmCommon->getFormatPrice($totalCommission):'-',
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'msg\',width:150',
		                'align' => 'right',
		                'mainalign' => 'right',
					),
					__('Total Komisi Perusahaan (%s)', Configure::read('__Site.config_currency_symbol')) => array(
						'text' => !empty($totalRoyalty)?$this->RmCommon->getFormatPrice($totalRoyalty):'-',
						'width' => 25,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'Status\',width:250',
		                'align' => 'right',
		                'mainalign' => 'right',
					),
				));
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	function _callDataGraphicCommissions ( $params ) {
		$this->controller->loadModel('Property');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
        
        $start_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_from');
        $end_date = $this->RmCommon->filterEmptyField($params, 'named', 'date_to');

		$options = array(
			'conditions' => array(
				'PropertySold.id <>' => null,
			),
			'contain' => array(
				'PropertySold',
			),
			'order' => array(
				'PropertySold.sold_date' => 'ASC',
				'Property.id' => 'DESC',
			),
			'group' => array(
				'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\')',
			),
			'fields' => array(
				'PropertySold.sold_date',
				'Property.agent_commission',
				'Property.company_commission',
			),
		);
		$this->controller->Property->virtualFields['agent_commission'] = 'SUM(IFNULL(PropertySold.agent_commission_net, 0))';
		$this->controller->Property->virtualFields['company_commission'] = 'SUM(IFNULL(PropertySold.company_commission, 0))';

		$flag = !empty($admin_rumahku)?false:true;
		$params = $this->RmCommon->_callUnset(array(
			'named' => array(
				'date_from',
				'date_to',
			),
		), $params);

		if( !empty($start_date) ) {
			$options['conditions']['DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') >='] = $start_date;

			if( !empty($end_date) ) {
				$options['conditions']['DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') <='] = $end_date;
			}
		}

		$options = $this->controller->User->Property->_callRefineParams($params, $options);
		$data	= $this->controller->User->Property->getData('all', $options, array(
			'status' => 'sold',
			'parent' => $flag,
            'admin_mine' => $flag,
            'company' => $flag,
            'rest' => false,
		));
		$result = array();

		if( !empty($data) ) {
			foreach ($data as $key => $value) {
				$sold_date = $this->RmCommon->filterEmptyField($value, 'PropertySold', 'sold_date', null);
				$agent_commission = $this->RmCommon->filterEmptyField($value, 'Property', 'agent_commission', 0);
				$company_commission = $this->RmCommon->filterEmptyField($value, 'Property', 'company_commission', 0);

				$result['agent_commission'][$sold_date] = $agent_commission;
				$result['company_commission'][$sold_date] = $company_commission;
			}
		}

		return array(
			'data' => $result,
		);
	}

	function _callDataReport_pus ( $params, $offset = false, $limit = 30, $type = false ) {
		$this->controller->loadModel('ActivityPus');

        $params_named = $this->RmCommon->filterEmptyField($this->controller->params, 'named', false, array());

        if( !empty($params) ) {
			$params['named'] = array_merge($this->RmCommon->processSorting(array(), $params, false, false, false), $params_named);
        }

        $period = Common::hashEmptyField($params, 'named.period');

		$this->controller->ActivityPus->virtualFields['pus'] = 'IFNULL(ActivityPus.pus, 0)';
		$this->controller->ActivityPus->virtualFields['rank_check'] = 'CASE WHEN ActivityPus.rank IS NULL THEN 1 ELSE 0 END';
		$this->controller->ActivityPus->virtualFields['rank'] = 'IFNULL(ActivityPus.rank, 0)';

    	$optionsPus = $this->controller->ActivityPus->_callRefineParams($params, array(
            'conditions' => array(
            	'ActivityPus.user_id = User.id',
	            'ActivityPus.activity_status' => 'open',
            ),
    	));
		$this->controller->User->bindModel(array(
            'hasOne' => array(
                'ActivityPus' => array(
                    'className' => 'ActivityPus',
                    'foreignKey' => false,
                    'conditions' => Common::hashEmptyField($optionsPus, 'conditions', array()),
                ),
            )
        ), false);
		$options =  $this->controller->User->_callRefineParams($params, array(
			'order' => array(
				'ActivityPus.rank_check' => 'ASC',
				'ActivityPus.rank' => 'ASC',
				'ActivityPus.total_point' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'group' => array(
				'User.id',
			),
			'contain' => array(
				'ActivityPus',
			),
		));

		$data = $this->controller->User->getData('all', $options, array(
			'company' => true,
			'role' => 'agent',
		));
		$result = array();

		if( !empty($data) ) {
			$idx = 1;

			foreach ($data as $key => $value) {
				$value = $this->controller->User->getMergeList($value, array(
					'contain' => array(
						'UserCompany' => array(
							'uses' => 'UserCompany',
							'foreignKey' => 'parent_id',
							'primaryKey' => 'user_id',
						),
					),
				));

				$pus = Common::hashEmptyField($value, 'ActivityPus.pus', 0);
				$pus = floor($pus);

				$contentArr = array(
					__('No') => array(
						'text' => $idx,
						'width' => 10,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'no\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Nama') => array(
						'text' => Common::hashEmptyField($value, 'User.full_name'),
						'width' => 25,
                		'field_model' => 'User.full_name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'name\',width:250',
                		'fix_column' => true,
					),
					__('POIN') => array(
						'text' => Common::hashEmptyField($value, 'ActivityPus.total_point', 0),
						'width' => 15,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'point\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('PUS') => array(
						'text' => $pus,
						'width' => 15,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'pus\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				);

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');

				$idx++;
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
		);
	}

	function _callDataReport_point ( $params, $offset = false, $limit = 30, $type = false ) {
		$this->controller->loadModel('ActivityUser');
		$this->controller->loadModel('ExpertCategory');

        $params_named = $this->RmCommon->filterEmptyField($this->controller->params, 'named', false, array());

        if( !empty($params) ) {
			$params['named'] = array_merge($this->RmCommon->processSorting(array(), $params, false, false, false), $params_named);
        }

        $period = Common::hashEmptyField($params, 'named.period');

		$this->controller->ActivityUser->virtualFields['total_point'] = 'SUM(IFNULL(ActivityUser.point, 0))';

    	$optionsPoint = $this->controller->ActivityUser->_callRefineParams($params, array(
            'conditions' => array(
            	'ActivityUser.user_id = User.id',
            	'ActivityUser.principle_id = User.parent_id',
	            'ActivityUser.activity_status' => 'confirm',
            ),
    	));
		$this->controller->User->unbindModel(
			array('hasMany' => array('ActivityUser'))
		);
		$this->controller->User->bindModel(array(
            'hasOne' => array(
                'ActivityUser' => array(
                    'className' => 'ActivityUser',
                    'foreignKey' => false,
                    'conditions' => Common::hashEmptyField($optionsPoint, 'conditions', array()),
                ),
            )
        ), false);
		$options =  $this->controller->User->_callRefineParams($params, array(
			'order' => array(
				'ActivityUser.total_point' => 'DESC',
				'User.full_name' => 'ASC',
			),
			'group' => array(
				'User.id',
			),
			'contain' => array(
				'ActivityUser',
			),
		));

		$data = $this->controller->User->getData('all', $options, array(
			'company' => true,
			'role' => 'agent',
		));
		$result = array();

		if( !empty($data) ) {
			$idx = 1;
			$categories = $this->controller->ExpertCategory->getData('list', array(
				'fields' => array(
					'ExpertCategory.id',
					'ExpertCategory.name',
				),
	            'order' => array(
					'ExpertCategory.name'=>'ASC',
					'ExpertCategory.company_id'=>'ASC',
					'ExpertCategory.created'=>'ASC',
					'ExpertCategory.parent_id'=>'ASC',
				),
				'group' => array(
					'ExpertCategory.id',
				),
			), array(
				'status' => 'root',
				'with_default' => true,
			));

			foreach ($data as $key => $value) {
				$total_point = 0;
				$user_id = Common::hashEmptyField($value, 'User.id');
				$optionsCategory = $this->controller->ActivityUser->_callRefineParams($params, array(
					'fields' => array(
						'ActivityUser.expert_category_id',
						'ActivityUser.total_point',
					),
					'conditions' => array(
						'ActivityUser.user_id' => $user_id,
					),
					'group' => array(
						'ActivityUser.expert_category_id',
					),
				));
				$current_categories = $this->controller->ActivityUser->getData('list', $optionsCategory, array(
					'status' => 'confirm',
				));
				$value['ExpertCategory'] = $current_categories;

				$contentArr = array(
					__('No') => array(
						'text' => $idx,
						'width' => 10,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'no\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
					__('Nama') => array(
						'text' => Common::hashEmptyField($value, 'User.full_name'),
						'width' => 25,
                		'field_model' => 'User.full_name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'name\',width:250',
                		'fix_column' => true,
					),
				);

				if( !empty($categories) ) {
					$acii = 65; // A

					foreach ($categories as $category_id => $category_name) {
						$charTxt = chr($acii);
						$category_slug = __('point-%s', $category_id);
						$point = Common::hashEmptyField($value, 'ExpertCategory.'.$category_id, 0);

						$contentArr = array_merge($contentArr, array(
							$charTxt => array(
								'text' => $point,
								'width' => 10,
				                'style' => 'text-align: left;vertical-align: middle;',
				                'data-options' => 'field:\''.$category_slug.'\',width:100',
				                'align' => 'center',
				                'mainalign' => 'center',
		                		'excel' => array(
		                			'align' => 'center',
		            			),
							),
						));
						
						$total_point += $point;
						$acii++;
					}
				}

				$contentArr = array_merge($contentArr, array(
					__('Total POIN') => array(
						'text' => $total_point,
						'width' => 15,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'total\',width:100',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
            			),
					),
				));

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');

				$idx++;
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
		);
	}
	
	function _callAddBeforeSave( $data, $report_type_id = 'performance' ) {
		$dataSave = array();

		if( !empty($data) ) {
			$dataSearches = $this->RmCommon->filterEmptyField($data, 'Search');
			$title = $this->RmCommon->filterEmptyField($dataSearches, 'title');

			$dataSave['Report'] = array(
				'user_id' => $this->controller->user_id,
				'report_type_id' => $report_type_id,
				'session_id' => String::uuid(),
			);

			$dataSearches = $this->RmCommon->_callUnset(array(
				'title',
			), $dataSearches);

			if( !empty($dataSearches) ) {
				foreach ($dataSearches as $titleField => $values) {
					$value = reset($values);
					$field = key($values);

					if( is_array($value) ) {
						$value = array_filter($value);

						if( in_array($field, array( 'type', 'status', 'subareas' )) ) {
							$result_value = array();

							foreach ($value as $key => $val) {
								$result_value[] = $key;
							}

							$value = $result_value;
						}

						$search_value = implode(',', $value);
						
						if( !empty($value) ) {
							$value = @serialize($value);
						}
					} else {
						$search_value = $value;
					}

					if( !empty($value) ) {
						switch ($field) {
							case 'date':
								$values = $this->RmCommon->dataConverter($values,array(
									'daterange' => array(
										'date',
									),
								), true);

								$start_date = $this->RmCommon->filterEmptyField($values, 'date', 'start_date');
								$end_date = $this->RmCommon->filterEmptyField($values, 'date', 'end_date');
								$date = $this->RmCommon->getCombineDate($start_date, $end_date);

								$title = str_replace(array( '[%periode_date%]' ), array( $date ), $title);
								break;
						}

						$dataSave['ReportDetail'][] = array(
							'title' => $titleField,
							'field' => $field,
							'value' => $value,
						);
						$dataSave['Search'][$field] = $search_value;
					}
				}
			}

			$dataSave['Report']['title'] = $title;
		}

		return $dataSave;
	}

	function _callProcess( $modelName, $id, $value, $data ) {
		$dataSave = false;
		$file = false;
		
		$last_id = $this->RmCommon->filterEmptyField($data, 'last_id', false, null);
		$data = $this->RmCommon->filterEmptyField($data, 'data');
		$dataSave = array();
		$dataQueue = array();

		if( !empty($data) ) {
			$last_data = end($data);
			$currency_total_data = $this->RmCommon->filterEmptyField($value, 'Report', 'total_data');
			$previously_fetched_data = $this->RmCommon->filterEmptyField($value, 'Report', 'fetched_data');
			
			$paging = $this->RmCommon->filterEmptyField($this->controller->params, 'paging', $modelName);
			$total_current = $this->RmCommon->filterEmptyField($paging, 'current');
			$total_data = $this->RmCommon->filterEmptyField($paging, 'count');
			$limit = $this->RmCommon->filterEmptyField($paging, 'limit');
			$fetched_data = $previously_fetched_data + $total_current;

			$dataQueue['ReportQueue']['last_id'] = $last_id;
			$dataQueue['ReportQueue']['fetched_data'] = $total_current;
			$dataQueue['ReportQueue']['total_data'] = $fetched_data;

			$dataSave['Report']['id'] = $id;
			$dataSave['Report']['fetched_data'] = $fetched_data;
			$dataSave['Report']['on_progress'] = 0;

			if( empty($currency_total_data) ) {
				$dataSave['Report']['total_data'] = $total_data;
				$currency_total_data = $total_data;
			}
			if( $fetched_data >= $currency_total_data ) {
				$dataSave['Report']['document_status'] = 'completed';
			} else {
				$dataSave['Report']['document_status'] = 'progress';
			}

			$file = $this->_callFileCreate($value);
			$dataSave['Report']['filename'] = $this->RmCommon->filterEmptyField($file, 'filename');
		}

		return array(
			'dataSave' => $dataSave,
			'dataQueue' => $dataQueue,
			'file' => $file,
		);
	}

	function formatFilter( $data, $custom_opsi = array() ) {
		$dataSearch = array();

		if( !empty($data) ) {
			$skip_datasearch = $this->RmCommon->filterEmptyField($custom_opsi, 'skip_datasearch', false, false);

			$dataSearches = $this->RmCommon->filterEmptyField($data, 'Search');
			$title = $this->RmCommon->filterEmptyField($dataSearches, 'title');

			$dataSearches = $this->RmCommon->_callUnset(array(
				'title',
			), $dataSearches);

			if ($skip_datasearch) {
				$dataFormat = $data;

			} else {
				if( !empty($dataSearches) ) {
					foreach ($dataSearches as $titleField => $values) {
						$value = reset($values);
						$field = key($values);

						if( is_array($value) ) {
							$value = array_filter($value);

							if( in_array($field, array( 'type', 'status', 'subareas' )) ) {
								$result_value = array();

								foreach ($value as $key => $val) {
									$result_value[] = $key;
								}

								$value = $result_value;
							}

							if( in_array($field, array( 'user_id' )) ) {
								foreach ($value as $key => $val) {
									$id_principals ['user_id'][$val] = $val; 
								}

								$search_value = $id_principals['user_id'];

							}

							if( !empty($value) ) {
								$value = @serialize($value);
							}
						} else {
							$search_value = $value;
						}

						$dataFormat['Search'][$field] = $search_value;
					}
				}

			}
			// debug($dataFormat);die();
			$period    = Common::hashEmptyField($dataFormat, 'Search.period', 'daily');

			// format the date, daily, montly or yearly
			switch ($period) {
				case 'daily':
					$date       = Common::hashEmptyField($dataFormat, 'Search.date');
					$date_range = explode(' - ', $date);

					$date_from  = str_replace('/', '-', $date_range[0]);
					$date_to    = str_replace('/', '-', $date_range[1]);

					$date_from = date('Y-m-d', strtotime($date_from));
					$date_to   = date('Y-m-d', strtotime($date_to));

					$dataFormat['Search']['date_from'] = $date_from;
					$dataFormat['Search']['date_to'] = $date_to;


					unset($dataFormat['Search']['date']);

					break;
				case 'monthly':
					$date_from = Common::hashEmptyField($dataFormat, 'Search.month_from');
					$date_to   = Common::hashEmptyField($dataFormat, 'Search.month_to');

					$date_from = date('Y-m', strtotime($date_from));
					$date_to   = date('Y-m', strtotime($date_to));

					$month     = date('m', strtotime($date_to));
					$year      = date('Y', strtotime($date_to));
					$day_end_date = cal_days_in_month(CAL_GREGORIAN, $month, $year);

					$dataFormat['Search']['date_from'] = __('%s-01',$date_from);
					$dataFormat['Search']['date_to']   = __('%s-%s',$date_to, $day_end_date);


					unset($dataFormat['Search']['month_from']);
					unset($dataFormat['Search']['month_to']);
					break;
				case 'yearly':
					$date_from = Common::hashEmptyField($dataFormat, 'Search.year_from');
					$date_to   = Common::hashEmptyField($dataFormat, 'Search.year_to');

					$dataFormat['Search']['date_from'] = __('%s-01-01',$date_from);
					$dataFormat['Search']['date_to']   = __('%s-12-31',$date_to);


					unset($dataFormat['Search']['year_from']);
					unset($dataFormat['Search']['year_to']);

					break;
			}

			$dataSearch = $dataFormat;
		}
		
		// debug($dataSearch);die();
		return $dataSearch;
	}

	function callCompany($params = array(), $options = array()){
		$this->controller->loadModel('UserCompanyConfig');

		$find = Common::hashEmptyField($options, 'find', 'paginate');
		$exlude = Common::hashEmptyField($options, 'exlude', array());
		$parent_id = Common::hashEmptyField($options, 'parent_id', array());

		$options = array(
			'contain' => array(
				'UserCompany',
			),
			'order' => array(
				'UserCompanyConfig.id' => 'DESC',
			),
		);

		// filter params
		$user_ids = Common::hashEmptyField($params, 'Search.user_id');

		if (!empty($user_ids)) {
			$options['conditions']['UserCompany.user_id'] = $user_ids;
		} else {
			$options['conditions']['UserCompany.name <>'] = '';
		}

		// debug($options);die();
		if($exlude){
			$options['conditions']['UserCompany.user_id !='] = $exlude;
		}

		$this->controller->User->UserCompanyConfig->virtualFields['duration_year'] = 'timestampdiff(YEAR, UserCompanyConfig.live_date, UserCompanyConfig.end_date)';
		$this->controller->User->UserCompanyConfig->virtualFields['duration_month'] = 'timestampdiff(MONTH, UserCompanyConfig.live_date, UserCompanyConfig.end_date)';

		$options = $this->controller->User->UserCompanyConfig->_callRefineParams($params, $options);

		if($find == 'paginate'){
			$this->controller->paginate	= $this->controller->User->UserCompanyConfig->getData('paginate', $options, array(
				'mine' => true,
			));
			return $this->controller->paginate('UserCompanyConfig');
		} else {
			return $this->controller->User->UserCompanyConfig->getData( $find, $options, array(
				'mine' => true,
			));
		}
	}

	function callBeforeDevice($params = array()){
		$values = $this->callCompany($params);

		$date_from = Common::hashEmptyField($params, 'named.date_from');
		$date_to = Common::hashEmptyField($params, 'named.date_to');

		$date_prev = $this->RmCommon->getDateRangeCompare(array(
			'date_from' => $date_from,
			'date_to' => $date_to,
		));

		$this->controller->User->UserCompany->Log->virtualFields['cnt'] = 'COUNT(Log.id)';

		$options = array(
			'primaryKey' => 'parent_id',
			'foreignKey' => 'user_id',
			'conditions' => array(
				'Log.created >=' => $date_from,
				'Log.created <=' => $date_to,
			),
			'elements' => array(
				'activity' => true,
			),
		);

		$options_prev = array(
			'primaryKey' => 'parent_id',
			'foreignKey' => 'user_id',
			'conditions' => array(
				'Log.created >=' => Common::hashEmptyField($date_prev, 'date_from'),
				'Log.created <=' => Common::hashEmptyField($date_prev, 'date_to'),
			),
			'elements' => array(
				'activity' => true,
			),
		);

		return  $this->controller->User->UserCompany->getMergeList($values, array(
			'contain' => array(
				'User' => array(
					'type' => 'count',
					'uses' => 'User',
					'primaryKey' => 'parent_id',
					'foreignKey' => 'user_id',
				),
				'LogCount' => array_merge(array(
					'uses' => 'Log',
				), $options),
				'Log' => array_merge(array(
					'contain' => array(
						'UserCount' => array_merge(array(
							'type' => 'count',
							'uses' => 'Log',
							'group' => array(
								'Log.user_id', // untuk mencari banyaknya user unique
							),
							'grab_parent' => array(
								array(
									'fieldName' => 'Log.device',
									'targetField' => 'Log.device',
								), 
							),
						), array(
							'primaryKey' => 'parent_id',
							'foreignKey' => 'parent_id',
							'conditions' => array(
								'Log.created >=' => $date_from,
								'Log.created <=' => $date_to,
							),
						))
					),
					'type' => 'all',
					'uses' => 'Log',
					'group' => array(
						'Log.device',
					),
				), $options),
				'LogCountPrev' => array_merge(array(
					'uses' => 'Log',
				), $options_prev),
				'LogPrev' => array_merge(array(
					'contain' => array(
						'UserCount' => array_merge(array(
							'type' => 'count',
							'uses' => 'Log',
							'group' => array(
								'Log.user_id', // untuk mencari banyaknya user unique
							),
							'grab_parent' => array(
								array(
									'fieldName' => 'Log.device',
									'targetField' => 'Log.device',
								), 
							),
						), array(
							'primaryKey' => 'parent_id',
							'foreignKey' => 'parent_id',
							'conditions' => array(
								'Log.created >=' => Common::hashEmptyField($date_prev, 'date_from'),
								'Log.created <=' => Common::hashEmptyField($date_prev, 'date_to'),
							),
						))
					),
					'type' => 'all',
					'uses' => 'Log',
					'group' => array(
						'Log.device',
					),
				), $options_prev)
			),
		));
	}

	function callBeforeModuleAll($params = array()){
		$values = $this->callCompany($params);

		if($values){

			$params_search = Common::hashEmptyField($params, 'Search');
			if (!empty($params_search)) {
				$params['named']['date_from'] = Common::hashEmptyField($params, 'Search.date_from');
				$params['named']['date_to']   = Common::hashEmptyField($params, 'Search.date_to');
			}

			$options = $this->controller->User->UserCompany->Log->_callRefineParams($params, array());

			$date_from = Common::hashEmptyField($params, 'named.date_from');
			$date_to   = Common::hashEmptyField($params, 'named.date_to');

			$date_prev = $this->RmCommon->getDateRangeCompare(array(
				'date_from' => $date_from,
				'date_to' => $date_to,
			));

			$prev_options = $this->controller->User->UserCompany->Log->_callRefineParams(array(
				'named' => $date_prev
			));

			$this->controller->User->UserCompany->Log->virtualFields['cnt'] = 'COUNT(Log.id)';
			$this->controller->User->UserCompany->Log->virtualFields['parent_cnt'] = 'COUNT(DISTINCT Log.parent_id)';

			$cntActivity = $this->controller->User->UserCompany->Log->getData('first', $options, array(
				'activity' => true,
			));

			foreach ($values as $key => &$value) {
				$parent_id = Common::hashEmptyField($value, 'UserCompany.user_id');

				$value = $this->controller->User->UserCompany->getMergeList($value, array(
					'contain' => array(
						'Parent' => array(
							'uses' => 'User',
							'contain' => array(
								'UserProfile',
							),
						),
					),
				));
				$userCompany = $this->controller->User->UserCompany->getData('first', array(
					'conditions' => array(
						'UserCompany.user_id' => $parent_id,
					),
				));
			 	$value = Hash::insert($value, 'Parent.UserCompany', Common::hashEmptyField($userCompany, 'UserCompany'));

				$value['Activity'] = $this->controller->User->UserCompany->Log->getData('first', array_merge_recursive($options, array(
					'fields' => array(
						'Log.cnt',
						'Log.parent_cnt',
					),
					'conditions' => array(
						'Log.parent_id' => $parent_id,
					),
				)), array(
					'activity' => true,
				));

				$value['ActivityPrev'] = $this->controller->User->UserCompany->Log->getData('first', array_merge_recursive($prev_options, array(
					'fields' => array(
						'Log.cnt',
						'Log.parent_cnt',
					),
					'conditions' => array(
						'Log.parent_id' => $parent_id,
					),
				)), array(
					'activity' => true,
				));

				// ==== monthly new listing ====
				$new_property_monthly = $this->controller->User->Property->getData('count', array(
					'conditions' => array(
						'Property.principle_id' => $parent_id,
						'DATE_FORMAT(Property.created, \'%Y-%m-%d\') >=' => $date_from,
						'DATE_FORMAT(Property.created, \'%Y-%m-%d\') <=' => $date_to,
					),
					'order' => false,
				), array(
					'status' => 'all'
				));
				$value['NewPropertyMonthly'] = $new_property_monthly;

				// ==== accumulate propertysold ====
				$accumulate_prop_sold = $this->controller->User->Property->getData('count', array(
					'conditions' => array(
						'Property.principle_id' => $parent_id,
						'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') >=' => $date_from,
						'DATE_FORMAT(PropertySold.sold_date, \'%Y-%m-%d\') <=' => $date_to,
					),
					'contain' => array(
						'PropertySold',
					),
					'order' => false,
				), array(
					'status' => 'sold'
				));
				$value['SoldProperty'] = $accumulate_prop_sold;

				// ==== ebrosur company ====
				$option_ebrosur = array(
					'type'      => 'count',
					'parent_id' => $parent_id,
					'period'    => array(
						'from' => $date_from,
						'to'   => $date_to
					),
				);
				$value['EbrosurCompany'] = $this->_callEbrosurCompany($option_ebrosur);

			}
			$values['cntActivity'] = $cntActivity;
		}
		return $values;
	}

	function _callEbrosurCompany($custom_opsi = array()){
		$brosurs = 0;
		if (!empty($custom_opsi)) {
			$type      = Common::hashEmptyField($custom_opsi, 'type', 'all');
			$parent_id = Common::hashEmptyField($custom_opsi, 'parent_id');
			$date_from = Common::hashEmptyField($custom_opsi, 'period.from');
			$date_to   = Common::hashEmptyField($custom_opsi, 'period.to');

			$default_options = array(
				'conditions' => array(
					'DATE_FORMAT(UserCompanyEbrochure.created, \'%Y-%m-%d\') >=' => $date_from,
					'DATE_FORMAT(UserCompanyEbrochure.created, \'%Y-%m-%d\') <=' => $date_to,
					'UserCompanyEbrochure.status' => true,
				)
			);

			$agents = $this->controller->User->getAgents($parent_id, true, 'list', false, array(
				'role' => 'all',
				'skip_is_sales' => true,
			));

			if (!empty($agents)) {
				$default_options['conditions']['or'] = array(
					array(
						'UserCompanyEbrochure.user_id' => $parent_id,
					),
					array(
						'UserCompanyEbrochure.user_id' => $agents,
					),
				);

			}

			$brosurs = $this->controller->User->UserCompanyEbrochure->getData($type, $default_options, array(
				'mine' => false,
				'admin' => true,
				'company' => false,
				'is_sales' => false,
				'status' => 'active',
			));
			// debug($brosurs);die();

		}

		return $brosurs;
	}

	function callBeforeModuleActive($params = array()){
		$values = array();
		$acos_id = Common::hashEmptyField($params, 'named.acosid');
		$slug = Common::hashEmptyField($params, 'named.slug');

		$acos = $this->controller->User->UserCompany->Log->Acos->getData('first', array(
			'conditions' => array(
				'Acos.id' => $acos_id,
			),
		));

		if($acos){
			$alias = Common::hashEmptyField($acos, 'Acos.alias');
			$options = $this->controller->User->UserCompany->Log->_callRefineParams($params);

			$date_prev = $this->RmCommon->getDateRangeCompare(array(
				'date_from' => Common::hashEmptyField($params, 'named.date_from'),
				'date_to' => Common::hashEmptyField($params, 'named.date_to'),
			));

			$prev_options = $this->controller->User->UserCompany->Log->_callRefineParams(array(
				'named' => $date_prev
			));

			$tempOptions = array_merge_recursive($options, array(
				'conditions' => array(
					'Log.model' => $alias,
				),
				'group' => array(
					'Log.parent_id'
				),
			));

			switch ($slug) {
				case 'module-active':
					$this->controller->paginate = $this->controller->Log->getData('paginate', $tempOptions, array(
						'activity' => true,
					));
					$values = $this->controller->paginate('Log');

					$modelGetParent = $this->controller->Log;
					$optionsGetParent = array(
						'Parent' => array(
							'uses' => 'User',
							'primaryKey' => 'id',
							'foreignKey' => 'parent_id',
							'contain' => array(
								'UserProfile',
							),
						),
					);
					break;
				
				case 'module-not-active':
					$companyList = array();
					$companyList = $this->controller->Log->getData('list', array_merge_recursive($tempOptions, array(
						'fields' => array(
							'parent_id', 'parent_id'
						),
					)), array(
						'activity' => true,
					));

					$values = $this->callCompany(array(), array(
						'exlude' => $companyList,
					));

					$modelGetParent = $this->controller->User->UserCompany;
					$optionsGetParent = array(
						'Parent' => array(
							'uses' => 'User',
							'contain' => array(
								'UserProfile',
							),
						),
					);
					break;
			}

			$this->controller->User->UserCompany->Log->virtualFields['cnt'] = 'COUNT(Log.id)';
			$this->controller->User->UserCompany->Log->virtualFields['parent_cnt'] = 'COUNT(DISTINCT Log.parent_id)';

			$cntActivity = $this->controller->User->UserCompany->Log->getData('first', $options, array(
				'activity' => true,
			));

			foreach ($values as $key => &$value) {
				$parent_id = Common::hashEmptyField($value, 'UserCompany.user_id');
				$parent_id = Common::hashEmptyField($value, 'Log.parent_id', $parent_id);

				$value = $modelGetParent->getMergeList($value, array(
					'contain' => $optionsGetParent,
				));

			 	$userCompany = $this->controller->User->UserCompany->getData('first', array(
					'conditions' => array(
						'UserCompany.user_id' => $parent_id,
					),
				));
			 	$value = Hash::insert($value, 'Parent.UserCompany', Common::hashEmptyField($userCompany, 'UserCompany'));

				$value['Activity'] = $this->controller->User->UserCompany->Log->getData('first', array_merge_recursive($options, array(
					'fields' => array(
						'Log.cnt',
						'Log.parent_cnt',
					),
					'conditions' => array(
						'Log.model' => $alias,
						'Log.parent_id' => $parent_id,
					),
				)), array(
					'activity' => true,
				));

				$value['ActivityPrev'] = $this->controller->User->UserCompany->Log->getData('first', array_merge_recursive($prev_options, array(
					'fields' => array(
						'Log.cnt',
						'Log.parent_cnt',
					),
					'conditions' => array(
						'Log.model' => $alias,
						'Log.parent_id' => $parent_id,
					),
				)), array(
					'activity' => true,
				));
			}
			$values['acos'] = $acos;
			$values['cntActivity'] = $cntActivity;
		}

		return $values;
	}

	function callBeforeTimeActive($params = array()){
		$this->controller->loadModel('Log');

		$date_prev = $this->RmCommon->getDateRangeCompare(array(
			'date_from' => Common::hashEmptyField($params, 'named.date_from'),
			'date_to' => Common::hashEmptyField($params, 'named.date_to'),
		));

		$date_from = Common::hashEmptyField($params, 'named.date_from');
		$date_to = Common::hashEmptyField($params, 'named.date_to');
		$hour = Common::hashEmptyField($params, 'named.hour');
		$day = Common::hashEmptyField($params, 'named.day');
		$slug = Common::hashEmptyField($params, 'named.slug');

		$this->controller->Log->virtualFields['cnt'] = 'COUNT(Log.id)';
		$default_options = array(
			'conditions' => array(
				'DATE_FORMAT(Log.created, \'%w\')' => (int) $day,
				'DATE_FORMAT(Log.created, \'%k\')' => (int) $hour,
			),
			'group' => array(
				'Log.parent_id',
			),
			'order' => array(
				'Log.parent_id' => 'ASC'
			),
		);

		$options = $this->controller->Log->_callRefineParams($params, $default_options);

		if($slug == 'time-active'){
			$this->controller->paginate = $this->controller->Log->getData('paginate', $options, array(
				'activity' => true,
			));
			$values = $this->controller->paginate('Log');
		} else {
			$options['fields'] = array(
				'Log.parent_id', 'Log.parent_id'
			);
			$company_list = $this->controller->Log->getData('list', $options, array(
				'activity' => true,
			));

			$values = $this->callCompany($params, array(
				'exlude' => $company_list
			));
		}

		return $this->controller->Log->getMergeList($values, array(
			'contain' => array(
				'UserCompany' => array(
					'uses' => 'UserCompany',
					'primaryKey' => 'user_id',
					'foreignKey' => 'parent_id',
				),
				'Parent' => array(
					'uses' => 'User',
					'primaryKey' => 'id',
					'foreignKey' => 'parent_id',
					'contain' => array(
						'UserProfile',
					),
				),
			),
		));
	}

	function callBeforeActivity($params = array()){
		$this->controller->loadModel('Log');

		$date_prev = $this->RmCommon->getDateRangeCompare(array(
			'date_from' => Common::hashEmptyField($params, 'named.date_from'),
			'date_to' => Common::hashEmptyField($params, 'named.date_to'),
		));

		$values = $this->callCompany($params);

		if($values){
			$group_ids = array_merge(Configure::read('__Site.Admin.Company.id'), array(
				2
			));
			$group_ids = Common::_callUnset($group_ids, array(
				1,
			));

			$this->controller->Log->virtualFields['cnt'] = 'COUNT(Log.id)';
			$options = $this->controller->Log->_callRefineParams($params);
			$prev_options = $this->controller->Log->_callRefineParams(array(
				'named' => $date_prev,
			));

			foreach ($values as $key => &$value) {
				$parent_id = Common::hashEmptyField($value, 'UserCompany.user_id');

				foreach ($group_ids as $key => $group_id) {
					$log = $this->controller->Log->getData('first', array_merge_recursive($options, array(
						'conditions' => array(
							'Log.parent_id' => $parent_id,
							'Log.group_id' => $group_id,
						),
					)), array(
						'activity' => true,
					));

					$logPrev = $this->controller->Log->getData('first', array_merge_recursive($prev_options, array(
						'conditions' => array(
							'Log.parent_id' => $parent_id,
							'Log.group_id' => $group_id,
						),
					)), array(
						'activity' => true,
					));

					$value['Log'][$group_id] = array(
						'current' => Common::hashEmptyField($log, 'Log.cnt'),
						'prev' => Common::hashEmptyField($logPrev, 'Log.cnt'),
					);
				}
			}
		}
		return $values;
	}

	function _callBeforeCSA( $filter_params = array() ){
		$params = $this->controller->params->params;

		if (!empty($filter_params)) {
			$params = array_merge($params, $filter_params);
		}

		$type = Common::hashEmptyField($params, 'named.slug');


		switch ($type) {
			case 'time-active':
			case 'time-not-active':
				return $this->callBeforeTimeActive($params);

			case 'device':
				return $this->callBeforeDevice($params);

			case 'module-active':
			case 'module-not-active':
				return $this->callBeforeModuleActive($params);

			case 'module-all':
				return $this->callBeforeModuleAll($params);

			case 'usage-activity':
				return $this->callBeforeActivity($params);
		}
	}

	function _callAdminUserBeforeSave( $data, $limit = false, $type = false, $format = 'Data' ) {
		$dataReport = array();
		$render = $this->RmCommon->filterEmptyField($data, 'Search', 'report_type_id', null);

		if( empty($limit) ) {
			$limit = $this->controller->limit;
		}

		switch ($render) {
			case 'performance':
				$format = __('_call%sPerformance', $format);
				$dataReport = $this->$format($data, false, $limit, $type);
				$urlBack = array(
					'controller' => 'reports',
					'action' => 'performance_add',
					'admin' => true,
				);
				break;
			case 'summary':
				$format = __('_call%sSummary', $format);
				$dataReport = $this->$format($data, false, $limit, $type);
				$urlBack = array(
					'controller' => 'reports',
					'action' => 'summary_add',
					'admin' => true,
				);
				break;
			case 'properties':
				$format = __('_call%sProperties', $format);
				$dataReport = $this->$format($data, false, $limit, $type);
				$urlBack = array(
					'controller' => 'reports',
					'action' => 'property_add',
					'admin' => true,
				);
				break;
			case 'visitors':
				$format = __('_call%sVisitors', $format);
				$dataReport = $this->$format($data, false, $limit);
				$urlBack = array(
					'controller' => 'reports',
					'action' => 'visitor_add',
					'admin' => true,
				);
				break;
			case 'messages':
				$format = __('_call%sMessages', $format);
				$dataReport = $this->$format($data, false, $limit, $type);
				$urlBack = array(
					'controller' => 'reports',
					'action' => 'message_add',
					'admin' => true,
				);
				break;
			case 'agents':
				$format = __('_call%sAgents', $format);
				$dataReport = $this->$format($data, false, $limit, $type);
				$urlBack = array(
					'controller' => 'reports',
					'action' => 'agent_add',
					'admin' => true,
				);
				break;
			case 'kprs':
				$format = __('_call%sKprs', $format);
				$dataReport = $this->$format($data, false, $limit, $type);
				$urlBack = array(
					'controller' => 'reports',
					'action' => 'kpr_add',
					'admin' => true,
				);
				break;
			case 'commissions':
				$format = __('_call%sCommissions', $format);
				$dataReport = $this->$format($data, false, $limit, $type);
				$urlBack = array(
					'controller' => 'reports',
					'action' => 'commission_add',
					'admin' => true,
				);
				break;
			case 'clients':
				$format = __('_call%sClients', $format);
				$dataReport = $this->$format($data, false, $limit, $type);
				$urlBack = array(
					'controller' => 'users',
					'action' => 'clients',
					'admin' => true,
				);
				break;
			case 'users':
				$format = __('_call%sUsers', $format);
				$dataReport = $this->$format($data, false, $limit, $type);
				$urlBack = array(
					'controller' => 'reports',
					'action' => 'user_add',
					'admin' => true,
				);
				break;
			default:
				$format = __('_call%s%s', $format, ucfirst($render));
				$dataReport = @$this->$format($data, false, $limit, $type);

				if( !empty($dataReport) ) {
					switch ($render) {
						case 'report_pus':
							$urlBack = array(
								'controller' => 'activities',
								'action' => 'ranks',
								'admin' => true,
							);
							break;
						case 'report_point':
							$urlBack = array(
								'controller' => 'activities',
								'action' => 'points',
								'admin' => true,
							);
							break;
						
						default:
							$urlBack = array(
								'controller' => 'reports',
								'action' => $render.'_add',
								'admin' => true,
							);
							break;
					}
				} else {
					$this->RmCommon->redirectReferer(__('Mohon pilih jenis laporan'));
				}
				break;
		}


		$this->controller->set(array(
			'urlBack' => $urlBack,
		));

		return array(
			'data' => $dataReport,
			'type' => $render,
		);
	}

    function _callDetail ( $value ) {
		$details = $this->RmCommon->filterEmptyField($value, 'ReportDetail');

    	if( !empty($details) ) {
			foreach ($details as $key => &$detail) {
				$value_name = false;
				$field = $this->RmCommon->filterEmptyField($detail, 'ReportDetail', 'field');
				$val = $this->RmCommon->filterEmptyField($detail, 'ReportDetail', 'value');
				$vals = @unserialize($val);
				$globalData = Configure::read('Global.Data');

				if( !empty($vals) && is_array($vals) ) {
					switch ($field) {
						case 'subareas':
							$result = array();

							foreach ($vals as $subarea_id) {
								$merge = $this->controller->User->UserProfile->Subarea->getMerge(array(), $subarea_id, 'Subarea', 'Subarea.id', array(
									'cache' => __('Subarea.%s', $subarea_id),
									'cacheConfig' => 'subareas',
								));
								$result[] = $this->RmCommon->filterEmptyField($merge, 'Subarea', 'name');
							}

							$value_name = implode(', ', $result);
							break;
						case 'user_id':
							$result = array();

							foreach ($vals as $key => &$user_id) {
								$merge = $this->controller->User->UserCompany->getMerge(array(), $user_id);
								$result[] = $this->RmCommon->filterEmptyField($merge, 'UserCompany', 'name');
							}

							$value_name = implode(', ', $result);
							break;
						case 'principle_id':
							$result = array();

							foreach ($vals as $key => &$user_id) {
								$merge = $this->controller->User->UserCompany->getMerge(array(), $user_id);
								$result[] = $this->RmCommon->filterEmptyField($merge, 'UserCompany', 'name');
							}

							$value_name = implode(', ', $result);
							break;
						case 'pic_id':
							$result = array();

							foreach ($vals as $key => &$user_id) {
								$merge = $this->controller->User->getMerge(array(), $user_id);
								$result[] = $this->RmCommon->filterEmptyField($merge, 'User', 'full_name');
							}

							$value_name = implode(', ', $result);
							break;
						case 'region_id':
							$result = array();

							foreach ($vals as $key => &$region_id) {
								$merge = $this->controller->User->UserProfile->Region->getMerge(array(), $region_id, 'Region', array(
									'cache' => array(
										'name' => __('Region.%s', $region_id),
									),
								));
								$result[] = $this->RmCommon->filterEmptyField($merge, 'Region', 'name');
							}

							$value_name = implode(', ', $result);
							break;
						case 'status':
							$result = array();
							$propertyStatus = Configure::read('__Site.Property.Status');

							foreach ($vals as $key => &$text) {
								$result[] = $this->RmCommon->filterEmptyField($propertyStatus, $text);
							}

							$value_name = implode(', ', $result);
							break;
						case 'type':
							$result = array();
							
							foreach ($vals as $type_id) {
								$merge = $this->controller->User->Property->PropertyType->getMerge(array(), $type_id, 'PropertyType.id', array(
									'cache' => array(
										'name' => __('PropertyType.%s', $type_id),
									),
								));
								$result[] = $this->RmCommon->filterEmptyField($merge, 'PropertyType', 'name');
							}
							$value_name = implode(', ', $result);
							break;
						default:
							$result = array();

							foreach ($vals as $key => &$text) {
								$result[] = ucwords($text);
							}

							$value_name = implode(', ', $result);
							break;
					}
				} else {
					switch ($field) {
						case 'region':
							$merge = $this->controller->User->UserProfile->Region->getMerge(array(), $val, 'Region', array(
								'cache' => array(
									'name' => __('Region.%s', $val),
								),
							));
							$value_name = $this->RmCommon->filterEmptyField($merge, 'Region', 'name');
							break;
						case 'city':
							$merge = $this->controller->User->UserProfile->City->getMerge(array(), $val, 'City', 'City.id', array(
								'cache' => __('City.%s', $val),
							));
							$value_name = $this->RmCommon->filterEmptyField($merge, 'City', 'name');
							break;
						case 'include_me':
							$value_name = !empty($val)?__('Ya'):false;
							break;
						case 'property_action':
							$merge = $this->controller->User->Property->PropertyAction->getMerge(array(), $val, 'PropertyAction.id', array(
								'name' => __('PropertyAction.%s', $val),
							));
							$value_name = $this->RmCommon->filterEmptyField($merge, 'PropertyAction', 'name');
							break;
						case 'beds':
							$value_name = $this->RmCommon->filterEmptyField($globalData, 'room_options', $val);
							break;
						case 'baths':
							$value_name = $this->RmCommon->filterEmptyField($globalData, 'room_options', $val);
							break;
						case 'lot_size':
							$value_name = $this->RmCommon->filterEmptyField($globalData, 'lot_options', $val);
							break;
						case 'building_size':
							$value_name = $this->RmCommon->filterEmptyField($globalData, 'lot_options', $val);
							break;
						case 'price':
							$value_name = $this->RmCommon->filterEmptyField($globalData, 'price_options', $val);
							break;
						case 'property_direction':
							$merge = $this->controller->User->Property->PropertyAsset->PropertyDirection->getMerge(array(), $val);
							$value_name = $this->RmCommon->filterEmptyField($merge, 'PropertyDirection', 'name');
							break;
						case 'condition':
							$merge = $this->controller->User->Property->PropertyAsset->PropertyCondition->getMerge(array(), $val);
							$value_name = $this->RmCommon->filterEmptyField($merge, 'PropertyCondition', 'name');
							break;
						case 'furnished':
							$value_name = $this->RmCommon->filterEmptyField($globalData, 'furnished', $val);
							break;
						case 'group_by':
							switch ($val) {
								case 'cities':
									$value_name = __('Kota');
									break;
								
								default:
									$value_name = __('Properti');
									break;
							}
							break;
						case 'principle_id':
							$merge = $this->controller->User->UserCompany->getMerge(array(), $val);
							$value_name = $this->RmCommon->filterEmptyField($merge, 'UserCompany', 'name');
							break;
						default:
							$value_name = ucwords($val);
							break;
					}
				}
				
				if( !empty($value_name) ) {
					$detail['ReportDetail']['value_name'] = $value_name;
				}
			}

			$value['ReportDetail'] = $details;
		}

		return $value;
    }

    function _callDataSearch ( $data ) {
		$details = $this->RmCommon->filterEmptyField($data, 'ReportDetail');
		$result['Search']['report_type_id'] = $this->RmCommon->filterEmptyField($data, 'Report', 'report_type_id');

    	if( !empty($details) ) {
			foreach ($details as $key => &$detail) {
				$value_name = false;
				$field = $this->RmCommon->filterEmptyField($detail, 'ReportDetail', 'field');
				$val = $this->RmCommon->filterEmptyField($detail, 'ReportDetail', 'value');

				$vals = @unserialize($val);
				
				if( $field == 'named' ) {
					if( !empty($vals) ) {
						foreach ($vals as $param_field => $param_val) {
							$result['Search'][$param_field] = $param_val;
						}
					}
				} else {
					if( !empty($vals) && is_array($vals) ) {
						$vals = implode(',', $vals);
					} else {
						$vals = $val;
					}

					$result['Search'][$field] = $vals;
				}
			}

			// debug($result);die();
			$period = Common::hashEmptyField($result, 'Search.period');

			if (!empty($period)) {
				$result = $this->formatFilter($result, array('skip_datasearch' => true));
				if( !empty($result) ) {
					$result['named'] = $this->RmCommon->processSorting(array(), $result, false, false, false);
		        }

			}
	        
		}

		return $result;
    }

	function _callDetailBeforeView ( $value ) {
		$table_view     = $this->RmCommon->filterEmptyField($value, 'table_view');
		$last_id        = $this->RmCommon->filterEmptyField($value, 'Report', 'last_id');
		$report_type_id = $this->RmCommon->filterEmptyField($value, 'Report', 'report_type_id');

		$value  = $this->_callDetail($value);
		$data   = $this->_callDataSearch($value);
		$values = $this->_callAdminUserBeforeSave($data, $this->controller->limit_paging, 'view');

		/*
			// start ===========================
			Pilihan lihat detail laporan apakah model freeze tabel atau tampilan table yang baru
			Kalau tabel freeze, load css freeze
		*/
		if( !in_array($report_type_id, array( 'summary' )) ) {

			if ($table_view == 'freeze_table') {
				$this->RmCommon->_layout_file('freeze');
			} else {
				$this->controller->set('_freeze', false);
			}

		} else {
			$this->controller->set('_freeze', false);
		}
		// end ==================================

		if( $this->Rest->isActive() ) {
			$this->controller->set(array(
				'data' => $this->RmCommon->filterEmptyField($values, 'data', 'data', null), 
				'headers' => $this->RmCommon->filterEmptyField($values, 'data', 'headers', null), 
			));
		} else {
			$this->controller->set(array(
				'values' => $values, 
				'value' => $value,
			));
		}
	}

	function _callGraphicBeforeView ( $value ) {
		$report_type_id = $this->RmCommon->filterEmptyField($value, 'Report', 'report_type_id');
		$value = $this->_callDetail($value);

		$data = $this->_callDataSearch($value);
		$values = $this->_callAdminUserBeforeSave($data, false, false, 'DataGraphic');

		if( !in_array($report_type_id, array( 'summary' )) ) {
			$this->RmCommon->_layout_file('freeze');
		} else {
			$this->controller->set('_freeze', false);
		}

		if( $this->Rest->isActive() ) {
			$this->controller->set(array(
				'data' => $this->RmCommon->filterEmptyField($values, 'data', 'data', null), 
				'headers' => $this->RmCommon->filterEmptyField($values, 'data', 'headers', null), 
			));
		} else {
			$this->controller->set(array(
				'values' => $values, 
				'value' => $value, 
			));
		}
	}

	function _callFileCreate( $value ) {
		$prefix = $this->RmCommon->filterEmptyField($value, 'Report', 'session_id');
		$filename = $this->RmCommon->filterEmptyField($value, 'Report', 'filename');
		$path = Configure::read('__Site.report_folder');

		if( empty($filename) ) {
			$filename = sprintf('%s.xlsx', $prefix);
			$path = $this->RmImage->generatePathFolder($filename, $path);
		} else {
			$path = array(
				'filename' => $filename,
				'filename_path' => $this->RmImage->_callGetFolderUploadPath($filename, $path),
			);
		}

		return $path;
	}

	function getPropertyLog($param_dates = array(), $company_ids = array()){
		if($param_dates){
			$result = array();
		
			foreach ($param_dates as $slug => $param_date) {
				$propertyLog = array();
				$date_from = Common::hashEmptyField($param_date, 'date_from');
				$date_to = Common::hashEmptyField($param_date, 'date_to');


				$this->controller->Log->virtualFields['cnt'] = 'COUNT(Log.id)';

				$default_options = array(
					'conditions' => array(
						'Log.group_id' => array_merge(Configure::read('__Site.Admin.Company.id'), array(
							2
						)),
						'Log.created >=' => $date_from,
						'Log.created <=' => $date_to,
					),
					'order' => array(
						'Log.group_id' => 'ASC',
					)
				);

				if($company_ids){
					$default_options['conditions']['Log.parent_id'] = $company_ids;
				}

				$options = array_merge($default_options, array(
					'group' => array(
						'Log.group_id',
					),
				));

				$this->controller->paginate = $this->controller->Log->getData('paginate', $options, array(
					'activity' => true,
				));
				$values = $this->controller->paginate('Log');
				$values = $this->controller->Log->getMergeList($values, array(
					'contain' => array(
						'Group',
					),
				));

				if($values){
					$propertyLog = $this->controller->Log->getData('first', $default_options);
				}
				
				$result[sprintf('values_%s', $slug)] = $values;
				$result[sprintf('propertyLog_%s', $slug)] = $propertyLog;
			}
			return $result;
		}
		return false;
	}

	function _callSaveDataExport ( $title, $report, $data, $value ) {
		if( !$this->Rest->isActive() ) {
			$last_id = $this->RmCommon->filterEmptyField($data, 'last_id');
			$data = $this->RmCommon->filterEmptyField($data, 'data');
			$result = false;

			if( !empty($data) ) {
				$dataSave = $this->RmCommon->filterEmptyField($value, 'dataSave');
				$dataQueue = $this->RmCommon->filterEmptyField($value, 'dataQueue');
				$filename_path = $this->RmCommon->filterEmptyField($value, 'file', 'filename_path');

				$start_date = $this->RmCommon->filterEmptyField($report, 'Report', 'start_date');
				$end_date = $this->RmCommon->filterEmptyField($report, 'Report', 'end_date');
				$periods = $this->RmCommon->getCombineDate($start_date, $end_date);
				$titles = array(
					'title' => $title,
					'periods' => $periods,
				);

				$this->exportExcel($titles, $data, $filename_path);

				if( !empty($dataSave) ) {
					$dataSave['ReportQueue'][] = $dataQueue;
					$result = $this->controller->User->Report->saveAll($dataSave, array('deep' => true));
				}
			}

			return $result;
		} else {
			return false;
		}
	}

	function exportExcel( $titles, $data, $path = false ) {
		if( file_exists($path) ) {
			$this->PhpExcel->loadWorksheet($path);
			$this->PhpExcel->_xls->getActiveSheet();
			$theader = false;
		} else {
			$this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);;
			$this->PhpExcel->setRow(3);
			$theader = true;
		}

		// get table report data
		$this->processReportTableData( $titles, $data, $theader );
		$this->PhpExcel->getDefaultStyle()->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$this->PhpExcel->_xls->getActiveSheet();
		$this->PhpExcel->save($path);
	}

	function processReportTableData( $titles, $data, $theader = true ) {
		$table = array();
		$dimensi = 0; // Acii A

		if( !empty($data[0]) ) {
			$num = 0;

			foreach ($data[0] as $label => $value) {
				$text = Common::hashEmptyField($value, 'text');
				$width = Common::hashEmptyField($value, 'width');
				$childs = Common::hashEmptyField($value, 'child');
				$rowspan = Common::hashEmptyField($value, 'excel.headerrowspan');
				$colspan = Common::hashEmptyField($value, 'excel.headercolspan');

				$dataArr = $this->RmCommon->_callUnset($value, array(
					'text',
					'horizontal',
				));

				$table[$num] = array_merge($dataArr, array(
					'label' => $label,
					'width' => $width,
					'rowspan' => $rowspan,
					'colspan' => $colspan,
				));

				if( !empty($childs) ) {
					foreach ($childs as $key => $child) {
						$label = Common::hashEmptyField($child, 'name', '');
						$width = Common::hashEmptyField($child, 'width');
						$rowspan = Common::hashEmptyField($child, 'excel.headerrowspan');
						$colspan = Common::hashEmptyField($child, 'excel.headercolspan');

						$table[$num]['child'][] = array_merge($dataArr, array(
							'label' => $label,
							'width' => $width,
							'rowspan' => $rowspan,
							'colspan' => $colspan,
						));

						$dimensi++;
					}
				} else {
					$dimensi++;
				}
				
				$num++;
			}
		}

		$cell_end = Common::getNameFromNumber($dimensi);

		if( !empty($theader) ) {
			$title = $this->RmCommon->filterEmptyField($titles, 'title');
			$periods = $this->RmCommon->filterEmptyField($titles, 'periods');
			$this->PhpExcel->setReportHeader($title, $periods, 'A1', 'A2', sprintf('A1:%s1', $cell_end), sprintf('A2:%s2', $cell_end));
			
			$bold = true;
		} else {
			$table = array();
			$bold = false;
		}

		// add heading with different font and bold text
		$this->PhpExcel->addTableHeader($table, array(
			'name' => 'Calibri',
			'bold' => $bold,
			'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
			'fill_color' => '069E55',
			'text_color' => 'FFFFFF',
		), $cell_end);

		if( !empty($data) ) {
			foreach ($data as $label => $values) {
				$dataTable = array();

				if( !empty($values) ) {
					foreach ($values as $key => $value) {
						$text = $this->RmCommon->filterEmptyField($value, 'text', false, '');
						$excel = Common::hashEmptyField($value, 'excel');
						$childs = Common::hashEmptyField($value, 'child');

						if( !empty($childs) ) {
							foreach ($childs as $key => $child) {
								$text = Common::hashEmptyField($child, 'text', '');
								$excel = Common::hashEmptyField($child, 'excel');

								if( !empty($excel) ) {
									$dataTable[] = array(
										'text' => $text,
										'options' => $excel,
									);
								} else {
									$dataTable[] = $text;
								}
							}
						} else {
							if( !empty($excel) ) {
								$dataTable[] = array(
									'text' => $text,
									'options' => $excel,
								);
							} else {
								$dataTable[] = $text;
							}
						}
					}
				}

				if( !empty($dataTable) ) {
				    $this->PhpExcel->addTableRow($dataTable, array(
				    	'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				   	));
				}
			}
		}
	}

	function _callProcessDetail ( $result, $id = null ) {
		if( $this->Rest->isActive() ) {
			$this->controller->admin_detail($id);

			return false;
		} else {
			$this->RmCommon->setProcessParams($result, array(
				'controller' => 'reports',
				'action' => 'detail',
				$id,
				'admin' => true,
			), array(
				'rest' => false,
			));
		}
	}

	function _callDataGrowth ( $params, $last_id = false, $limit = 30, $type = false ) {
		$this->controller->loadModel('ReportAccumulate');

		if( !empty($params) ) {
			$params['named'] = $this->RmCommon->processSorting(array(), $params, false, false, false);
        } else {
        	$params = $this->controller->params;
        }
		
		$admin_rumahku = Configure::read('User.Admin.Rumahku');
		$flag = !empty($admin_rumahku)?false:true;

        $period = Common::hashEmptyField($params, 'named.period');
		$principle_ids = Common::HashEmptyField($params, 'named.user_id');

        switch ($period) {
        	case 'daily':
        		$format = '\'%Y-%m-%d\'';
        		$periodName = $period;

		        $period_from = Common::hashEmptyField($params, 'named.date_from');
		        $period_to = Common::hashEmptyField($params, 'named.date_to');

		        if( empty($period_from) || empty($period_to) ) {
					$this->RmCommon->redirectReferer(__('Silahkkan pilih periode bulan'));
		        }

		        $period_from = date('Y-m-d', strtotime($period_from));
		        $period_to = date('Y-m-d', strtotime($period_to));

		        if( $period_from > $period_to ) {
					$this->RmCommon->redirectReferer(__('Tanggal mulai tidak boleh lebih besar dari tanggal akhir'));
		        }

		        $headerrowspan = 2;
        		break;

        	case 'monthly':
        		$format = '\'%Y-%m\'';
        		$periodName = 'month';

		        $period_from = Common::hashEmptyField($params, 'named.date_from');
		        $period_to = Common::hashEmptyField($params, 'named.date_to');

		        if( empty($period_from) || empty($period_to) ) {
					$this->RmCommon->redirectReferer(__('Silahkkan pilih periode bulan'));
		        }

		        $period_from = date('Y-m', strtotime($period_from));
		        $period_to = date('Y-m', strtotime($period_to));

		        if( $period_from > $period_to ) {
					$this->RmCommon->redirectReferer(__('Bulan mulai tidak boleh lebih besar dari bulan akhir'));
		        }

		        $headerrowspan = 2;
        		break;

        	case 'yearly':
        		$format = '\'%Y\'';
        		$periodName = 'year';

		        $period_from = Common::hashEmptyField($params, 'named.date_from');
		        $period_to = Common::hashEmptyField($params, 'named.date_to');

		        if( empty($period_from) || empty($period_to) ) {
					$this->RmCommon->redirectReferer(__('Silahkkan pilih periode tahun'));
		        } else if( $period_from > $period_to ) {
					$this->RmCommon->redirectReferer(__('Tahun mulai tidak boleh lebih besar dari tahun akhir'));
		        }
		        
		        $headerrowspan = false;
        		break;
        	
        	default:
				$this->RmCommon->redirectReferer(__('Data tidak ditemukan'));
        		break;
        }

		$this->controller->ReportAccumulate->virtualFields['date'] = 'DATE_FORMAT(STR_TO_DATE(ReportAccumulate.date, '.$format.'), '.$format.')';

        // ====================
        // Informasi Perusahaan
        // ====================
		$this->controller->User->UserCompanyConfig->virtualFields['cnt'] = 'COUNT(DISTINCT UserCompanyConfig.user_id)';
		$this->controller->User->UserCompanyConfig->virtualFields['month'] = 'DATE_FORMAT(UserCompanyConfig.live_date, '.$format.')';
		$this->controller->User->UserCompanyConfig->virtualFields['created_month'] = 'DATE_FORMAT(UserCompanyConfig.created, '.$format.')';

		$options = array(
			'fields' => array(
				'UserCompanyConfig.month',
				'UserCompanyConfig.cnt',
			),
			'conditions' => array(
				'UserCompanyConfig.live_date NOT' => NULL,
			),
			'group' => array(
				'UserCompanyConfig.month',
			),
			'order' => false,
		);
		$options = $this->controller->User->UserCompanyConfig->_callRefineParams($params, $options);

		$company_options = $options;
		$company_options['group'] = 'UserCompanyConfig.created_month';
		$company_options['conditions']['DATE_FORMAT(UserCompanyConfig.created, '.$format.') >='] = $period_from;
		$company_options['conditions']['DATE_FORMAT(UserCompanyConfig.created, '.$format.') <='] = $period_to;
		$companies = $this->controller->User->UserCompanyConfig->getData('list', $company_options, array(
			'mine' => true,
		));

		$companiespublish_options = $options;
		$companiespublish_options['conditions']['DATE_FORMAT(UserCompanyConfig.live_date, '.$format.') >='] = $period_from;
		$companiespublish_options['conditions']['DATE_FORMAT(UserCompanyConfig.live_date, '.$format.') <='] = $period_to;
		$companies_publish = $this->controller->User->UserCompanyConfig->getData('list', $companiespublish_options, array(
			'mine' => true,
		));

		$companiesexpired_options = $options;
		$companiesexpired_options['conditions'][]['OR'] = array(
			array(
				'DATE_FORMAT(UserCompanyConfig.end_date, '.$format.') >=' => $period_from,
				'DATE_FORMAT(UserCompanyConfig.end_date, '.$format.') <=' => $period_to,
			),
			'UserCompanyConfig.end_date' => NULL,
		);
		$this->controller->User->UserCompanyConfig->virtualFields['month'] = 'DATE_FORMAT(UserCompanyConfig.end_date, '.$format.')';
		$companies_expired = $this->controller->User->UserCompanyConfig->getData('list', $companiesexpired_options, array(
			'mine' => true,
		));
		
		// ReportAccumulate
		$this->controller->ReportAccumulate->virtualFields['value'] = 'SUM(ReportAccumulate.value)';
		$this->controller->ReportAccumulate->virtualFields['slug'] = 'CONCAT(ReportAccumulate.date, \'-\', ReportAccumulate.name)';

		$optionsAccumulate = array(
			'fields' => array(
				'ReportAccumulate.slug',
				'ReportAccumulate.value',
			),
			'conditions' => array(
				'ReportAccumulate.date >=' => $period_from,
				'ReportAccumulate.date <=' => $period_to,
				'ReportAccumulate.periode' => $periodName,
				'ReportAccumulate.name' => array(
					'user_monthly',
					'users',
					'user_active',
					'user_inactive',
					'property_monthly',
					'properties',
					'property_sold',
				),
			),
			'group' => array(
				'ReportAccumulate.slug',
			),
		);
		$optionsAccumulate = $this->controller->ReportAccumulate->_callRefineParams($params, $optionsAccumulate);
		
		$options = $optionsAccumulate;
		$agents = $this->controller->ReportAccumulate->find('list', $options);

		// $options = $optionsAccumulate;
		// $options['conditions']['ReportAccumulate.name'] = 'users';
		// $agentregistered = $this->controller->ReportAccumulate->find('list', $options);
		
		// // Need Cron
		// $options = $optionsAccumulate;
		// $options['conditions']['ReportAccumulate.name'] = 'user_active';
		// $agentsactive = $this->controller->ReportAccumulate->find('list', $options);
		
		// // Need Cron
		// $options = $optionsAccumulate;
		// $options['conditions']['ReportAccumulate.name'] = 'user_inactive';
		// $agentsinactive = $this->controller->ReportAccumulate->find('list', $options);
		
		// // ====================
  //       // Informasi Property
  //       // ====================
		// $options = $optionsAccumulate;
		// $options['conditions']['ReportAccumulate.name'] = 'property_monthly';
		// $properties = $this->controller->ReportAccumulate->find('list', $options);
		
		// // Need Cron
		// $options = $optionsAccumulate;
		// $options['conditions']['ReportAccumulate.name'] = 'properties';
		// $total_properties = $this->controller->ReportAccumulate->find('list', $options);

		$this->controller->User->Property->unbindModel(
			array('hasOne' => array('PropertySold'))
		);

		$this->controller->User->Property->bindModel(array(
			'hasOne' => array(
				'PropertySold' => array(
					'className' => 'PropertySold',
					'foreignKey' => 'property_id',
					'conditions' => array(
						'PropertySold.sold_by_id = Property.user_id',
						'PropertySold.status' => 1,
					),
				),
			)
		), false);

		$this->controller->User->Property->virtualFields['cnt'] = 'COUNT(PropertySold.id)';
		$this->controller->User->Property->virtualFields['month'] = 'DATE_FORMAT(PropertySold.sold_date, '.$format.')';
		$param_property = array(
			'named' => array(
				'principle_id' => $principle_ids,
			),
		);

		$options = array(
			'fields' => array(
				'Property.month',
				'Property.cnt',
			),
			'conditions' => array(
				'DATE_FORMAT(PropertySold.sold_date, '.$format.') >=' => $period_from,
				'DATE_FORMAT(PropertySold.sold_date, '.$format.') <=' => $period_to,
			),
			'contain' => array(
				'PropertySold',
			),
			'group' => array(
				'Property.month',
			),
			'order' => false,
		);
		$propertiessold_options = $options;
		$elements_sold = array(
			'status' => 'sold',
			'parent' => $flag,
            'admin_mine' => $flag,
            'company' => $flag,
		);
		$propertiessold_options = $this->controller->User->Property->_callRefineParams($param_property, $propertiessold_options);
		$propertiessold = $this->controller->User->Property->getData('list', $propertiessold_options, $elements_sold);

		$this->controller->User->Property->virtualFields['transaction_sold'] = 'SUM(PropertySold.price_sold)';
		$propertiessold_options = $options;
		$propertiessold_options['fields'] = array(
			'Property.month',
			'Property.transaction_sold',
		);
		$propertiessold_options = $this->controller->User->Property->_callRefineParams($param_property, $propertiessold_options);
		$propertiestransactionsold = $this->controller->User->Property->getData('list', $propertiessold_options, $elements_sold);
		
		// Log
		$this->controller->User->LogView->virtualFields['cnt'] = 'COUNT(DISTINCT LogView.user_id)';
		$this->controller->User->LogView->virtualFields['month'] = 'DATE_FORMAT(LogView.created, '.$format.')';
		$this->controller->User->LogView->virtualFields['slug'] = 'CONCAT(DATE_FORMAT(LogView.created, '.$format.'), \'-\', LogView.type)';

		$logViewOptions = array(
			'fields' => array(
				'LogView.slug',
				'LogView.cnt',
			),
			'conditions' => array(
				'DATE_FORMAT(LogView.created, '.$format.') >=' => $period_from,
				'DATE_FORMAT(LogView.created, '.$format.') <=' => $period_to,
				'LogView.group_id' => 2,
				'LogView.type' => array(
					'login',
					'daily',
				),
			),
			'group' => array(
				'LogView.slug',
			),
			'order' => false,
		);
		
		// $login = $this->controller->User->LogView->getData('list', array_merge_recursive($logViewOptions, array(
		$log_views = $this->controller->User->LogView->getData('list', $logViewOptions);

		// behavior User
		$this->controller->User->Log->virtualFields['cnt'] = 'COUNT(Log.id)';
		$this->controller->User->Log->virtualFields['month'] = 'DATE_FORMAT(Log.created, '.$format.')';

		$logOptions = array(
			'fields' => array(
				'Log.month',
				'Log.cnt',
			),
			'conditions' => array(
				'DATE_FORMAT(Log.created, '.$format.') >=' => $period_from,
				'DATE_FORMAT(Log.created, '.$format.') <=' => $period_to,
				'Log.user_id NOT' => NULL,
				'Log.parent_id NOT' => NULL,
			),
			'group' => array(
				'Log.month',
			),
			'order' => false,
		);

		$logParams = array(
			'named' => array(
				'principle_id' => $principle_ids,
			),
		);

		$behavioragents = $this->controller->User->Log->getData('list', $logOptions);

		$this->controller->User->Log->virtualFields['month'] = 'CONCAT(DATE_FORMAT(Log.created, '.$format.'), \'-\', Log.mobile)';
		// $logOptions['conditions']['Log.mobile'] = false;
		$logOptions = $this->controller->User->Log->_callRefineParams($logParams, $logOptions);
		$behavioragentplatform = $this->controller->User->Log->getData('list', $logOptions);

        switch ($period) {
        	case 'daily':
		        $fromMonth = date('m', strtotime($period_from));
		        $fromYear = date('Y', strtotime($period_from));
		        $toMonth = date('m', strtotime($period_to));
		        $toYear = date('Y', strtotime($period_to));

				$dataDiff = Common::dateDiff($period_from, $period_to, 'day');

		        $totalCnt = $toMonth - $fromMonth;
		        $totalYear = $toYear - $fromYear;

		        if( !empty($totalYear) && $totalYear > 0 ) {
		            $currTotalYear = 12 * $totalYear;
		            $totalCnt += $currTotalYear;
		        }

		        $totalYear = $totalCnt;
		        $totalCnt = $dataDiff;
        		break;

        	case 'monthly':
		        $fromMonth = date('m', strtotime($period_from));
		        $fromYear = date('Y', strtotime($period_from));
		        $toMonth = date('m', strtotime($period_to));
		        $toYear = date('Y', strtotime($period_to));

		        $totalCnt = $toMonth - $fromMonth;
		        $totalYear = $toYear - $fromYear;

		        if( !empty($totalYear) && $totalYear > 0 ) {
		            $currTotalYear = 12 * $totalYear;
		            $totalCnt += $currTotalYear;
		        }
        		break;
        		
        	case 'yearly':
		        $totalCnt = $period_to - $period_from;
        		break;
        }

        // kebutuhan behavior user
        $urlLink = array(
        	'controller' => 'reports',
			'action' => 'growth_behavior',
			'date_from' => $period_from,
			'date_to' => $period_to,
			'principle_id' => $principle_ids,
        );

		$headingAttr = array(
			'width' => 35,
            'style' => 'text-align: left;vertical-align: middle;',
            'data-options' => 'field:\'title\',width:250',
    		'fix_column' => true,
            'rowspan' => ($type!='view')?2:false,
		);
		$header = array(
			array(
				'text' => __('Monthly new property agencies'),
				'var' => 'companies',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Monthly publish property agencies'),
				'var' => 'companies_publish',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Monthly expired property agencies'),
				'var' => 'companies_expired',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'title' => __('Empty'),
			),
			array(
				'text' => __('Monthly new agents'),
				'slug' => 'user_monthly',
				'var' => 'agents',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Total agents registered'),
				'slug' => 'users',
				'var' => 'agents',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Total agents active'),
				'slug' => 'user_active',
				'var' => 'agents',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Total agents non-active'),
				'slug' => 'user_inactive',
				'var' => 'agents',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Sign In per month'),
				'slug' => 'login',
				'var' => 'log_views',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Session per month'),
				'slug' => 'daily',
				'var' => 'log_views',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Percentage active user'),
				'var' => 'agents',
				'action' => 'percentage_active_user',
				'attr' => $headingAttr,
			),
			array(
				'title' => __('Empty'),
			),
			array(
				'text' => __('Behavior user browser'),
				'slug' => '0',
				'var' => 'behavioragentplatform',
				'action' => 'behavior_user',
				'attr' => $headingAttr,
				'url' => array_merge($urlLink, array(
					'type' => 'mobile'
				)),
			),
			array(
				'text' => __('Behavior user mobile'),
				'slug' => '1',
				'var' => 'behavioragentplatform',
				'action' => 'behavior_user',
				'attr' => $headingAttr,
				'url' => array_merge($urlLink, array(
					'type' => 'browser'
				)),
			),
			array(
				'text' => __('Total behavior user'),
				'var' => 'behavioragents',
				'action' => 'behavior_user',
				'attr' => $headingAttr,
				'url' => $urlLink,
			),
			array(
				'title' => __('Empty'),
			),
			array(
				'text' => __('Monthly new listing'),
				'slug' => 'property_monthly',
				'var' => 'agents',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Total listing'),
				'slug' => 'properties',
				'var' => 'agents',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Avg. listing per agent'),
				'slug' => 'properties',
				'var' => 'agents',
				'action' => 'avg_listing_per_agent',
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Monthly new listing sold'),
				'var' => 'propertiessold',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Total listing sold'),
				'slug' => 'property_sold',
				'var' => 'agents',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
			),
			array(
				'text' => __('Listing life cycle'),
				'slug' => 'property_monthly',
				'var' => 'agents',
				'action' => 'listing_life_cycle',
				'attr' => $headingAttr,
			),
			array(
				'title' => __('Empty'),
			),
			array(
				'text' => __('Monthly transaction sold'),
				'var' => 'propertiestransactionsold',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
    			'child_attr' => array(
	        		'excel' => array(
	        			'align' => 'right',
	        			'type' => 'string',
	    			),
	            ),
			),
			array(
				'text' => __('Average value per transaction'),
				'var' => 'propertiestransactionsold',
				'action' => 'last_propertiestransactionsold',
				'format' => ($type=='view')?'currency':false,
				'attr' => $headingAttr,
    			'child_attr' => array(
	        		'excel' => array(
	        			'align' => 'right',
	        			'type' => 'string',
	    			),
	            ),
			),
		);
		$result = array();

		if( !empty($header) ) {
			foreach ($header as $key => $value) {
				$text = Common::hashEmptyField($value, 'text');
				$attr = Common::hashEmptyField($value, 'attr', array());
				$var = Common::hashEmptyField($value, 'var');
				$slug = Common::hashEmptyField($value, 'slug', null, array(
					'isset' => true,
				));
				$counter = Common::hashEmptyField($value, 'counter');
				$action = Common::hashEmptyField($value, 'action');
				$formattable = Common::hashEmptyField($value, 'format');
				$url = Common::hashEmptyField($value, 'url');
				$child_attr = Common::hashEmptyField($value, 'child_attr', array());

				$contentArr = array(
					__('PRIME SYSTEM') => array_merge(array(
						'text' => $text,
						'width' => 35,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'title\',width:250',
		        		'fix_column' => true,
		        		'excel' => array(
		        			'headerrowspan' => $headerrowspan,
		    			),
					), $attr),
				);

				if( !empty($var) ) {
			        $child = array();

					if( !empty($$counter) ) {
            			$tmpCounter = $$counter;
					} else {
						$tmpCounter = 0;
					}

			        for ($i=0; $i <= $totalCnt; $i++) {
			        	$tmp = $$var;

				        switch ($period) {
				        	case 'daily':
					        	$currDate = strtotime("+".$i." day", strtotime($period_from));
								$currDate = date('Y-m-d', $currDate);

					            $periodName = Common::formatDate($currDate, 'd');
					            $periodFormat = Common::formatDate($currDate, 'Y-m-d');
					            $yearFormat = Common::formatDate($currDate, 'M Y');
				        		break;

				        	case 'monthly':
					            $monthTime = mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear);
					            $periodName = date('F', $monthTime);
					            $periodFormat = date('Y-m', $monthTime);
					            $yearFormat = date('Y', $monthTime);
				        		break;
				        		
				        	case 'yearly':
					            $periodName = $periodFormat = $period_from + $i;
					            $yearFormat = __('Tahun %s', $periodName);
				        		break;
				        }

	            		if( $slug != NULL ) {
	            			$periodFormatTmp = __('%s-%s', $periodFormat, $slug);
	            		} else {
	            			$periodFormatTmp = $periodFormat;
	            		}

			            switch ($action) {
			            	case 'percentage_active_user':
			            		$agent_count = Common::hashEmptyField($agents, __('%s-users', $periodFormat, $slug), 0);
			            		$login_count = Common::hashEmptyField($log_views, $periodFormat, 0);

			            		if( !empty($agent_count) ) {
			            			$value = $login_count / $agent_count;
			            		} else {
			            			$value = 0;
			            		}

			            		$value = number_format($value, 2);
			            		$value = __('%s%%', $value);
			            		break;

			            	case 'avg_listing_per_agent':
			            		$agent_count = Common::hashEmptyField($agents, __('%s-users', $periodFormat, $slug), 0);

			            		$property_count = Common::hashEmptyField($tmp, $periodFormatTmp, 0);

			            		if( !empty($agent_count) ) {
			            			$value = $property_count / $agent_count;
			            		} else {
			            			$value = 0;
			            		}

			            		$value = number_format($value, 2);
			            		break;

			            	case 'listing_life_cycle':
			            		$propertiessold_count = Common::hashEmptyField($propertiessold, $periodFormat, 0);

			            		$property_count = Common::hashEmptyField($tmp, $periodFormatTmp, 0);

			            		if( !empty($property_count) ) {
			            			$value = $propertiessold_count / $property_count;
			            		} else {
			            			$value = 0;
			            		}

			            		$value = number_format($value, 2);
			            		$value = __('%s%%', $value);
			            		break;

		            		case 'last_propertiestransactionsold':
			            		$propertiessoldtransaction_count = Common::hashEmptyField($propertiestransactionsold, $periodFormat, 0);
			            		$propertiessold_count = Common::hashEmptyField($propertiessold, $periodFormat, 0);

			            		if( !empty($propertiessold_count) ) {
			            			$value = $propertiessoldtransaction_count / $propertiessold_count;
			            		} else {
			            			$value = 0;
			            		}
		            			break;
			            	
			            	default:
	            				$value = Common::hashEmptyField($tmp, $periodFormatTmp, false, array(
	            					'isset' => true,
            					));

	            				if( empty($value) ) {
									$options = array(
										'conditions' => array(
											'ReportAccumulate.date <' => $periodFormat,
											'ReportAccumulate.periode' => $periodName,
										),
									);

					            	switch ($var) {
					            		case 'total_properties':
					            			if( empty($lasttotal_properties) ) {
												$options['conditions']['ReportAccumulate.name'] = 'properties';
												$lasttotal_properties = $this->controller->ReportAccumulate->find('first', $options);
					            				$value = Common::hashEmptyField($lasttotal_properties, 'ReportAccumulate.value', 0);
					            			} else {
					            				$value = Common::hashEmptyField($lasttotal_properties, 'ReportAccumulate.value', 0);
					            			}
					            			break;

					            		case 'property_sold':
					            			if( empty($lastproperty_sold) ) {
												$options['conditions']['ReportAccumulate.name'] = 'property_sold';
												$lastproperty_sold = $this->controller->ReportAccumulate->find('first', $options);
					            				$value = Common::hashEmptyField($lastproperty_sold, 'ReportAccumulate.value', 0);
					            			} else {
					            				$value = Common::hashEmptyField($lastproperty_sold, 'ReportAccumulate.value', 0);
					            			}
					            			break;

					            		default:
				            				$value = 0;
					            			break;
					            	}
					            }
			            		break;
			            }

			            if( !empty($counter) ) {
	            			$tmpCounter += $value;
		            		$value = $tmpCounter;
			            }

			            if( !empty($formattable) ) {
			            	$value = $this->RmCommon->_generateType($formattable, $value, false, 0);
			            }

			            if($action == 'behavior_user'){
				            if($url && $value){
				            	$value = $this->RmCommon->link($value, array_merge($url, array(
				            		'periode' => $period,
				            	)), array(
				            		'target' => '_blank',
				            	));
				            }
			            }

			            $tmpContent = array_merge(array(
			                'name' => $periodName,
							'width' => 20,
			                'text' => !empty($value)?$value:'-',
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\''.$var.'_count_'.$periodFormatTmp.'\',width:120',
			                'align' => 'center',
			                'mainalign' => 'center',
			        		'excel' => array(
			        			'align' => 'center',
			        			'type' => 'number',
			    			),
			            ), $child_attr);

			            switch ($period) {
				        	case 'daily':
			            		$child[$yearFormat][$periodName] = $tmpContent;
				        		break;

				        	case 'monthly':
			            		$child[$yearFormat][$periodName] = $tmpContent;
				        		break;
				        		
				        	case 'yearly':
			            		$contentArr[$yearFormat] = $tmpContent;
				        		break;
				        }
			        }

		            switch ($period) {
			        	case 'daily':
					        for ($i=0; $i <= $totalYear; $i++) {
					        	$currMonth = strtotime("+".$i." month", strtotime($period_from));
								$monthName = date('M Y', $currMonth);
								$month_slug = date('m-Y', $currMonth);

					        	$childContent = Common::hashEmptyField($child, $monthName, array());

					        	$contentArr = array_merge($contentArr, array(
									$monthName => array(
										'text' => $monthName,
										'width' => 20,
						                'style' => 'text-align: center;vertical-align: middle;',
						                'data-options' => 'field:\''.$var.'_count_'.$month_slug.'\',width:120',
					                	'child' => $childContent,
						                'align' => 'center',
						                'mainalign' => 'center',
					            		'excel' => array(
					            			'align' => 'center',
					            			'type' => 'string',
					            			'headercolspan' => count($childContent),
					        			),
									),
								));
					        }
			        		break;
			        	case 'monthly':
					        for ($i=0; $i <= $totalYear; $i++) {
					        	$year = (string)$fromYear+$i;
					        	$childContent = Common::hashEmptyField($child, $year, array());

					        	$contentArr = array_merge($contentArr, array(
									'Tahun '.$year => array(
										'text' => $year,
										'width' => 20,
						                'style' => 'text-align: center;vertical-align: middle;',
						                'data-options' => 'field:\''.$var.'_count_'.$year.'\',width:120',
					                	'child' => $childContent,
						                'align' => 'center',
						                'mainalign' => 'center',
					            		'excel' => array(
					            			'align' => 'center',
					            			'type' => 'string',
					            			'headercolspan' => count($childContent),
					        			),
									),
								));
					        }
			        		break;
			        }
			    }

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
			}
		}

		$this->controller->set(array(
			'_pagination' => false, 
		));

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => false,
		);
	}

	function _callAddBeforeViewGrowth () {
		$companies =  $this->RmCommon->_callCompanies('all');

		$this->RmCommon->_layout_file('select2');
		$this->controller->set(array(
			'companies' => $companies, 
		));
	}

	function _callDataClients ( $params, $offset = false, $limit = 30, $type = false ) {
        $companyData = Configure::read('Config.Company.data');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $params_named = $this->RmCommon->filterEmptyField($this->controller->params, 'named', false, array());

		$dateRange = $this->RmCommon->_callConvertDateRange($params, Common::hashEmptyField($params, 'Search.date'));
		$dateRange = $this->RmCommon->_callSet(array(
			'date_from',
			'date_to',
		), $dateRange);

		if( !empty($dateRange) ) {
			$params['Search']['transaction_from'] = Common::hashEmptyField($dateRange, 'date_from');
			$params['Search']['transaction_to'] = Common::hashEmptyField($dateRange, 'date_to');
			$dateRange = array(
				'named' => $dateRange,
			);
		}

        $params = $this->RmCommon->_callUnset(array(
			'Search' => array(
				'date',
			),
		), $params);
		$params_named = $this->RmCommon->_callUnset(array(
			'date_from',
			'date_to',
		), $params_named);

		$params['named'] = array_merge($this->RmCommon->processSorting(array(), $params, false, false, false), $params_named);
		$is_agent = Common::isAgent();
		$user_login_id = Configure::read('User.id');

		if( !empty($is_agent) ) {
			$this->controller->loadModel('UserClient');
			$this->controller->loadModel('UserClientRelation');
			$options = $this->controller->UserClientRelation->_callRefineParams($params, array(
				'conditions' => array(
					'UserClientRelation.agent_id' => $user_login_id,			
					'User.deleted' => 0,
					'UserClient.status' => 1,
				),
				'contain' => array(
					'User' => array(
						'className' => 'User',
						'foreignKey' => 'user_id',
					),
					'UserClient',
				),
				'order' => array(
					'UserClientRelation.created' => 'DESC',
				),
				'group' => array(
					'UserClientRelation.company_id',
					'UserClientRelation.user_id',
					'UserClientRelation.agent_id',
				),
	            'offset' => $offset,
				'limit' => $limit,
			));

			$this->controller->paginate = $this->controller->UserClientRelation->getData('paginate', $options, array(
				'company' => true,
				'adminRumahku' => false,
			));

			$data = $this->controller->paginate('UserClientRelation');
		} else {
			$this->controller->loadModel('UserClient');
			$options = array(
				'conditions' => array(
					'UserClient.status' => 1,
				),
				'contain' => array(
					'User',
				),
				'order' => array(
					'UserClient.created' => 'DESC',
				),
	            'offset' => $offset,
				'limit' => $limit,
			);
			$data_arr = $this->controller->User->getUserParent($user_login_id);
			$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
			$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

			if($is_sales){
				$options['conditions']['UserClient.agent_id'] = $user_ids;
			} else {
				$options['conditions']['UserClient.company_id'] = $this->controller->parent_id;
			}

			$options = $this->controller->UserClient->_callRefineParams($params, $options);
			$options = $this->controller->User->getData('paginate', $options, array(
				'status' => 'all',
			));
			$this->controller->paginate = $this->controller->User->UserClient->getData('paginate', $options);
			$data = $this->controller->paginate('UserClient');
		}

		$result = array();
		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'UserClient', 'id');

		if( !empty($data) ) {
			$total_listing = 0;
			$total_agent_relation = 0;
        	
        	$genderOptions = Common::hashEmptyField(Configure::read('Global.Data'), 'gender_options');
        	$admin = Configure::read('User.admin');

			App::uses('HtmlHelper', 'View/Helper');
       		$this->Html = new HtmlHelper(new View(null));

			foreach ($data as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'id');
				$user_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'user_id');
				$company_id = $this->RmCommon->filterEmptyField($value, 'UserClient', 'company_id');
				$value = $this->controller->UserClient->getMergeList($value, array(
					'contain' => array(
						'UserCompany' => array(
							'primaryKey' => 'user_id',
							'foreignKey' => 'company_id',
						), 
					),
				));
				$value = $this->controller->User->UserClient->getMergeList($value, array(
					'contain' => array(
						'UserClientMasterReference',
						'Agent' => array(
							'uses' => 'User',
							'primaryKey' => 'id',
							'foreignKey' => 'agent_id',
							'elements' => array(
								'status' => 'all',
							),
						), 
					), 
				));

				$agent_id = Common::hashEmptyField($value, 'UserClient.agent_id');
				$value = $this->RmUser->_callLastActivity($value, array(
					'client_id' => $user_id,
					'agent_id' => $agent_id,
				));

				$property_count = $this->controller->User->Property->getData('count', array(
					'conditions' => array(
						'Property.client_id' => $user_id,
					),
				), array(
					'company' => true,
					'admin_mine' => true,
					'status' => 'active-pending'
				));
				$agent_relation_count = $this->controller->User->UserClientRelation->getData('count', array(
					'conditions' => array(
						'User.id <>' => NULL,
						'UserClientRelation.user_id' => $user_id,
						'UserClientRelation.company_id' => $company_id,
					),
					'contain' => array(
						'User',
					),
					'order' => array(
						'UserClientRelation.primary' => 'DESC',
						'UserClientRelation.id' => 'ASC',
					),
					'group' => array(
						'UserClientRelation.user_id',
						'UserClientRelation.agent_id',
					),
				));

    			$total_listing += $property_count;
    			$total_agent_relation += $agent_relation_count;

				$contentArr = array();
				$created = Common::hashEmptyField($value, 'UserClient.created', '-', array(
					'date' => 'd M Y, H:i',
				));
				$url = array(
  					'controller' => 'users',
  					'action' => 'client_properties',
  					$id,
  					'admin' => true,
                    'full_base' => true,
  				);

				$crm_project_id = Common::hashEmptyField($value, 'LastActivity.crm_project_id');

				if( !empty($crm_project_id) ) {
					$last_activity = Common::hashEmptyField($value, 'LastActivity.note', NULL, array(
						'urldecode' => false,
					));
				} else {
					$last_activity = false;
				}

  				if( $type == 'view' ) {
  					$name = Common::hashEmptyField($value, 'UserClient.full_name');
  					$name = !empty($name)?$this->Html->link($name, $url, array(
						'target' => '_blank',
					)):'-';

					$property_count = !empty($property_count)?$this->Html->link($property_count, $url, array(
						'target' => '_blank',
					)):false;
					$agent_relation_count = !empty($agent_relation_count)?$this->Html->link($agent_relation_count, array_merge($url, array(
						'action' => 'client_related_agents',
					)), array(
						'target' => '_blank',
					)):false;
					
					$last_activity = !empty($last_activity)?implode('<br>', $last_activity):'-';
  				} else {
  					$name = Common::hashEmptyField($value, 'UserClient.full_name', '-');
					
					$last_activity = !empty($last_activity)?implode(PHP_EOL, $last_activity):'-';
  				}

				if( !empty($admin_rumahku) || $group_id == 4 ) {
					$company_name = Common::hashEmptyField($value, 'UserCompany.name', '-');
					$contentArr = array(
						__('Perusahaan') => array(
							'text' => $company_name,
							'width' => 25,
	                		'field_model' => 'UserCompany.name',
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'company_name\',width:150',
						),
					);
				}

				if( !empty($admin) ) {
      				$agent_name = Common::hashEmptyField($value, 'Agent.full_name', '-');
					$contentArr = array_merge($contentArr, array(
						__('Agent Marketing') => array(
							'text' => $agent_name,
							'width' => 20,
	                		'field_model' => 'Agent.full_name',
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'agent_name\',width:150',
						),
			        ));
				}

				$contentArr = array_merge($contentArr, array(
					__('Nama') => array(
						'text' => $name,
						'width' => 25,
                		'field_model' => 'UserClient.full_name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'name\',width:150',
					),
					__('Email') => array(
						'text' => Common::hashEmptyField($value, 'User.email', '-'),
						'width' => 30,
                		'field_model' => 'User.email',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'email\',width:250',
                		'fix_column' => true,
					),
					__('Referensi') => array(
						'text' => Common::hashEmptyField($value, 'UserClientMasterReference.name', '-'),
						'width' => 20,
                		'field_model' => 'UserClientMasterReference.name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'reference\',width:250',
					),
					__('Handphone') => array(
						'text' => Common::hashEmptyField($value, 'UserClient.no_hp', '-', array(
							'urldecode' => false,
						)),
						'width' => 25,
                		'field_model' => 'UserClient.no_hp',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'no_hp\',width:120',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('Handphone 2') => array(
						'text' => Common::hashEmptyField($value, 'UserClient.no_hp_2', '-', array(
							'urldecode' => false,
						)),
						'width' => 25,
                		'field_model' => 'UserClient.no_hp_2',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'no_hp_2\',width:120',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('No. Telepon') => array(
						'text' => Common::hashEmptyField($value, 'UserClient.phone', '-', array(
							'urldecode' => false,
						)),
						'width' => 12,
                		'field_model' => 'UserClient.phone',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'phone\',width:120',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('PIN BB') => array(
						'text' => Common::hashEmptyField($value, 'UserClient.pin_bb', '-'),
						'width' => 12,
                		'field_model' => 'UserClient.pin_bb',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'pin_bb\',width:100',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('Line') => array(
						'text' => Common::hashEmptyField($value, 'UserClient.line', '-'),
						'width' => 20,
                		'field_model' => 'UserClient.line',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'line\',width:80',
                		'excel' => array(
                			'type' => 'string',
            			),
					),
					__('Gender') => array(
						'text' => Common::hashEmptyField($genderOptions, Common::hashEmptyField($value, 'UserClient.gender_id', '-'), '-'),
						'width' => 20,
                		'field_model' => 'UserClient.gender_id',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'gender_id\',width:80',
					),
					__('Jml Listing') => array(
						'text' => !empty($property_count)?$property_count:'-',
						'width' => 20,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'property_count\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
                		'excel' => array(
                			'align' => 'center',
                			'type' => 'string',
            			),
					),
				));

				if( !empty($admin) ) {
					$contentArr = array_merge($contentArr, array(
						__('Jml Agen Terhubung') => array(
							'text' => !empty($agent_relation_count)?$agent_relation_count:'-',
							'width' => 15,
			                'style' => 'text-align: center;vertical-align: middle;',
			                'data-options' => 'field:\'agent_relation_count\',width:80',
			                'align' => 'center',
			                'mainalign' => 'center',
	                		'excel' => array(
	                			'align' => 'center',
	                			'type' => 'string',
	            			),
						),
					));
				}

				$contentArr = array_merge($contentArr, array(
					__('Aktivitas Terakhir') => array(
						'text' => $last_activity,
						'width' => 30,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'last_activity\',width:250',
                		'excel' => array(
                			'align' => 'center',
                			'type' => 'string',
            			),
					),
					__('Tgl Terdaftar') => array(
						'text' => $created,
						'width' => 20,
                		'field_model' => 'UserClient.created',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'created\',width:120',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
				));

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	function _callAddBeforeViewClient () {
        $companyData = Configure::read('Config.Company.data');
		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

		if( !empty($admin_rumahku) || $group_id == 4 ) {
			$companies =  $this->RmCommon->_callCompanies();
		}

		$this->RmCommon->_layout_file('select2');
		$this->controller->set(compact('companies'));
	}

	function activeMenu($value){
		$report_type_id = Common::hashEmptyField($value, 'Report.report_type_id');
		$active_menu = false;

		if($report_type_id){
			switch ($report_type_id) {
				case 'performance':
					$active_menu = 'report_performance';
					break;
				case 'growth':
					$active_menu = 'report_growth';
					break;
				case 'summary':
					$active_menu = 'report_summary';
					break;
				case 'agents':
					$active_menu = 'report_agent';
					break;
				case 'clients':
					$active_menu = 'crm_client';
					break;
				case 'properties':
					$active_menu = 'report_property';
					break;
				case 'kprs':
					$active_menu = 'report_kpr';
					break;
				case 'commissions':
					$active_menu = 'report_commission';
					break;
				case 'visitors':
					$active_menu = 'report_visitor';
					break;
				case 'messages':
					$active_menu = 'report_message';
					break;
				case 'users':
					$active_menu = 'report_user';
					break;
				case 'report_pus':
					$active_menu = 'expert_pus';
					break;
				case 'report_point':
					$active_menu = 'expert_point';
					break;
			}
		}

		$this->controller->set('active_menu', $active_menu);
	}

	function _callDataUsers ( $params, $offset = false, $limit = 30, $type = false ) {
        $companyData = Configure::read('Config.Company.data');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $params_named = $this->RmCommon->filterEmptyField($this->controller->params, 'named', false, array());

		$dateRange = $this->RmCommon->_callConvertDateRange($params, Common::hashEmptyField($params, 'Search.date'));
		$dateRange = $this->RmCommon->_callSet(array(
			'date_from',
			'date_to',
		), $dateRange);

		if( !empty($dateRange) ) {
			$params['Search']['date_from'] = Common::hashEmptyField($dateRange, 'date_from');
			$params['Search']['date_to'] = Common::hashEmptyField($dateRange, 'date_to');
			$dateRange = array(
				'named' => $dateRange,
			);
		}

        $params = $this->RmCommon->_callUnset(array(
			'Search' => array(
				'date',
			),
		), $params);
		$params_named = $this->RmCommon->_callUnset(array(
			'date_from',
			'date_to',
		), $params_named);

		$params['named'] = array_merge($this->RmCommon->processSorting(array(), $params, false, false, false), $params_named);

		$options = array(
			'contain' => array(
				'UserCompanyConfigParent',
			),
			'conditions' => array(
				'UserCompanyConfigParent.user_id NOT' => NULL,
			),
			'group' => array(
				'User.id',
			),
            'offset' => $offset,
			'limit' => $limit,
		);	
		$options = $this->controller->User->_callRefineParams($params, $options);
		$options['order']['User.id'] = 'DESC';

		$this->controller->User->bindModel(array(
            'hasOne' => array(
                'UserCompanyConfigParent' => array(
                    'className' => 'UserCompanyConfig',
                    'foreignKey' => false,
                    'conditions' => array(
                    	'UserCompanyConfigParent.user_id = User.parent_id',
                	),
                ),
            )
        ), false);

		$this->controller->paginate	= $this->controller->User->getData('paginate', $options, array(
			'admin' => true,
			'company' => false,
			'status' => array(
				'active',
				'non-active',
			),
			'role' => 'user-general',
		));

		$data = $this->controller->paginate('User');
		$result = array();

		$last_data = end($data);
		$last_id = $this->RmCommon->filterEmptyField($last_data, 'User', 'id');

		if( !empty($data) ) {
			$genderOptions = Common::hashEmptyField($this->controller->global_variable, 'gender_options');
			
			$grandtotal_session = 0;
			$grandtotal_login = 0;

			foreach ($data as $key => $value) {
				$id = $this->RmCommon->filterEmptyField($value, 'User', 'id');
				$value = $this->controller->User->getMergeList($value, array(
					'contain' => array(
						'Group',
						'Parent' => array(
							'uses' => 'User',
							'primaryKey' => 'id',
							'foreignKey' => 'superior_id',
						),
						'UserConfig',
						'UserProfile',
						'UserCompany' => array(
							'uses' => 'UserCompany',
							'foreignKey' => 'parent_id',
							'primaryKey' => 'user_id',
						),
					),
				));

				$value = $this->RmCommon->dataConverter($value,array(
					'date' => array(
						'UserConfig' => array(
							'last_login',
							'created',
						),
					),
				), true);
				
				$logViewOptions = array(
					'conditions' => array(
						'LogView.user_id' => $id,
					),
					'order'=> false,
				);
				$value = $this->controller->User->LogView->_callGetDataView($value, $logViewOptions);

    			$message_count = $this->controller->User->Property->Message->_callAgentCount($id, 'active', $dateRange);
    			$property_count = $this->controller->User->Property->_callAgentPropertyCount($id, 'all', $dateRange);
    			$property_publish_count = $this->controller->User->Property->_callAgentPropertyCount($id, 'active-pending', $dateRange);
    			$property_sold_count = $this->controller->User->Property->_callAgentPropertyCount($id, 'sold', $dateRange);
    			$property_inactive_count = $this->controller->User->Property->_callAgentPropertyCount($id, 'inactive', $dateRange);
    			$ebrosur_count = $this->controller->User->UserCompanyEbrochure->_callAgentCount($id, 'active', $dateRange);
				$kpr_count = $this->controller->User->Kpr->_callAgentCount($id, 'active-all', $dateRange);


				$parentName = Common::hashEmptyField($value, 'Parent.full_name', '-');

				$name = $this->RmCommon->filterEmptyField($value, 'User', 'full_name', '-');
				$email = $this->RmCommon->filterEmptyField($value, 'User', 'email', '-');
				$gender_id = $this->RmCommon->filterEmptyField($value, 'User', 'gender_id', 1);

				$groupName = Common::hashEmptyField($value, 'Group.name', '-');

				$pin_bb = Common::hashEmptyField($value, 'UserProfile.pin_bb', '-');
				$line = Common::hashEmptyField($value, 'UserProfile.line', '-');
				$gender = Common::hashEmptyField($genderOptions, $gender_id);

    			$phones = array();
				$phones[] = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'phone', null);
				$phones[] = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'no_hp', '-');
				$phones[] = $this->RmCommon->filterEmptyField($value, 'UserProfile', 'no_hp_2', null);
    			$phones = array_filter($phones);

				$last_login = Common::hashEmptyField($value, 'LogLogin.created', false, array(
					'date' => 'd M Y H:i',
				));
				$log_view = Common::hashEmptyField($value, 'LogView.created', false, array(
					'date' => 'd M Y H:i',
				));
				$created = Common::hashEmptyField($value, 'User.created', false, array(
					'date' => 'd M Y H:i',
				));

				$contentArr = array();
				$total_session = Common::hashEmptyField($value, 'LogViewCount');
				$total_login = Common::hashEmptyField($value, 'LogLoginCount');

				$grandtotal_session += $total_session;
				$grandtotal_login += $total_login;

				if( !empty($admin_rumahku) || $group_id == 4 ) {
					$company_name = $this->RmCommon->filterEmptyField($value, 'UserCompany', 'name', '-');
					$contentArr = array(
						__('Perusahaan') => array(
							'text' => $company_name,
							'width' => 15,
	                		'field_model' => 'UserCompanyParent.name',
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'company_name\',width:150',
						),
					);
				}

				$contentArr = array_merge($contentArr, array(
					__('Nama') => array(
						'text' => $name,
						'width' => 25,
                		'field_model' => 'User.full_name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'full_name\',width:150',
					),
					__('Atasan') => array(
						'text' => $parentName,
						'width' => 25,
                		'field_model' => 'User.superior_id',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'parent\',width:150',
					),
					__('Email') => array(
						'text' => $email,
						'width' => 30,
                		'field_model' => 'User.email',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'email\',width:250',
                		'fix_column' => true,
					),
					__('Divisi') => array(
						'text' => $groupName,
						'width' => 25,
                		'field_model' => 'Group.name',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'name\',width:150',
					),
					__('Handphone') => array(
						'text' => implode(' / ', $phones),
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'no_hp\',width:150',
					),
					__('PIN BB') => array(
						'text' => $pin_bb,
						'field_model' => 'UserProfile.pin_bb',
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'pin_bb\',width:150',
					),
					__('Line') => array(
						'text' => $line,
						'field_model' => 'UserProfile.line',
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'line\',width:150',
					),
					__('Gender') => array(
						'text' => $gender,
						'field_model' => 'User.gender_id',
						'width' => 25,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'gender_id\',width:150',
					),
					__('Session Terakhir') => array(
						'text' => !empty($log_view)?$log_view:'-',
						'width' => 12,
                		'field_model' => false,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'log_view\',width:100',
					),
					__('Login Terakhir') => array(
						'text' => !empty($last_login)?$last_login:'-',
						'width' => 12,
                		'field_model' => false,
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'last_login\',width:100',
					),
					__('Tgl Terdaftar') => array(
						'text' => $created,
						'width' => 12,
                		'field_model' => 'UserConfig.created',
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'created\',width:100',
					),
					__('Total Session') => array(
						'text' => Common::hashEmptyField($value, 'LogViewCount', '-'),
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_session\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
					__('Total Login') => array(
						'text' => Common::hashEmptyField($value, 'LogLoginCount', '-'),
						'width' => 15,
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_login\',width:80',
		                'align' => 'center',
		                'mainalign' => 'center',
					),
				));

				$resultArr = $this->_callDataAPIConverter($contentArr);
				$result[$key] = $this->RmCommon->filterEmptyField($resultArr, 'data');
				$headers = $this->RmCommon->filterEmptyField($resultArr, 'headers');
			}

			if( $type == 'view' && !empty($result) && !$this->Rest->isActive() ) {
				$result[$key+1] = array();

				if( !empty($admin_rumahku) || $group_id == 4 ) {
					$result[$key+1] = array(
						__('Perusahaan') => array(
			                'style' => 'text-align: left;vertical-align: middle;',
			                'data-options' => 'field:\'company_name\',width:150',
						),
					);
				}

				$result[$key+1] = array_merge($result[$key+1], array(
					__('Nama') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'name\',width:150',
					),
					__('Atasan') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'parent\',width:150',
					),
					__('Email') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'email\',width:250',
					),
					__('Divisi') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'groupName\',width:250',
					),
					__('Handphone') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'no_hp\',width:150',
					),
					__('PIN BB') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'pin_bb\',width:250',
					),
					__('Line') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'line\',width:150',
					),
					__('Gender') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'gender_id\',width:150',
					),
					__('Session Terakhir') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'log_view\',width:100',
					),
					__('Login Terakhir') => array(
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'last_login\',width:100',
					),
					__('Tgl Terdaftar') => array(
						'text' => __('Total'),
		                'style' => 'text-align: left;vertical-align: middle;',
		                'data-options' => 'field:\'created\',width:100',
					),
					__('Total Session') => array(
						'text' => !empty($grandtotal_session)?$grandtotal_session:'-',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_session\',width:80',
					),
					__('Total Login') => array(
						'text' => !empty($grandtotal_login)?$grandtotal_login:'-',
		                'style' => 'text-align: center;vertical-align: middle;',
		                'data-options' => 'field:\'total_login\',width:80',
					),
				));
			}
		}

		return array(
			'headers' => !empty($headers)?$headers:false,
			'data' => $result,
			'last_id' => $last_id,
		);
	}

	function _callAddBeforeViewUser () {
		$data = $this->controller->request->data;

        $companyData = Configure::read('Config.Company.data');
		$admin_rumahku = Configure::read('User.Admin.Rumahku');
        $group_id = $this->RmCommon->filterEmptyField($companyData, 'User', 'group_id');

		if( !empty($admin_rumahku) || $group_id == 4 ) {
			$companies =  $this->RmCommon->_callCompanies();
		}

		if( empty($data) ) {
	        $dateFrom = date('Y-m-d', strtotime('-1 Month'));
	        $dateTo = date('Y-m-d');

            $data['Search']['Periode']['date'] = sprintf('%s - %s', date('d/m/Y', strtotime($dateFrom)), date('d/m/Y', strtotime($dateTo)));
            $this->controller->request->data = $data;
		}


		$this->RmCommon->_layout_file('select2');
		$this->controller->set(compact('companies'));
	}

	function _callBeforeViewJson( $value = null, $render = null ){
		$params = $this->controller->params->params;
		$type = Common::HashEmptyField($params, 'named.type');

		if( empty($value) ) {
			$params = $this->controller->params->params;
			$data = $this->controller->request->data;

			$group_id = Common::hashEmptyField($params, 'named.group_id');

			$json = Common::hashEmptyField($data, 'json');
			$value = json_decode($json, true);
			$value['group_id'] = $group_id;
		}

		$periode_date_to = date('Y-m-t');
		$periode_date_from = date ("Y-m-01", strtotime('-1 month', strtotime($periode_date_to)));
		
		$currentPeriod = Common::hashEmptyField($value, 'currentPeriod', Common::getCombineDate($periode_date_from, $periode_date_to));
		$activities = Common::hashEmptyField($value, 'activities');
		$period = Common::hashEmptyField($value, 'period');
		$rows = Common::hashEmptyField($value, 'rows');

		$this->controller->request->data['Search']['period'] = $period;

		$dataArr = Common::_callUnset($value, array(
			'rows',
			'cols',
		));

		if($type){
			$dataArr['type'] = $type;
		}

		if($dataArr === null){
			$dataArr = array();
		}
		if( !empty($rows) ) {
			$dataChart = array(
				'rows' => Common::hashEmptyField($value, 'rows'),
				'cols' => Common::hashEmptyField($value, 'cols'),
			);
		} else {
			$dataChart = array();
		}

		$this->controller->set(array_merge(array(
			'autoload' => true,
			'dataChart' => $dataChart,
		), $dataArr));

		$this->controller->render($render);
	}

	function getFillingPorvision($params = array(), $type = false, $groupByFormat = false){
		$default_options =  $this->controller->Kpr->_callRefineParams($params, array(
			'conditions' => array(
				'Kpr.property_id <>' => 0,
			),
			'order' => array(
				'Kpr.id' => 'ASC',
			),
		));

		$elements = array(
			'admin_mine' => true,
			'status' => 'application',
		);

		$this->RmCommon->_callRefineParams($params);

		switch ($type) {
			case 'filling':
				$this->controller->Kpr->virtualFields['cnt'] = 'SUM(Kpr.property_price)';
				$options = array_merge($default_options, array(
					'contain' => array(
						'Property',
					),
				));
				break;
			
			case 'provision':
				$options = $this->getProvision($default_options);
				break;
		}

		$options = $this->controller->Kpr->getData('paginate', $options, $elements);

		$this->controller->paginate = array_merge($options, array(
			'group' => array(
				$groupByFormat,
			),
			'limit' => 12,
		));
		$kprs = $this->controller->paginate('Kpr');

		return array(
			'kprs' => $kprs,
			'options' => $default_options,
			'elements' => $elements,
		);
	}

	function getProvision($options = array(), $elements = array(), $count = false){
	    $this->controller->Kpr->virtualFields['cnt'] = 'SUM(ViewKprBankProvision.commission)';
		$options = array_merge_recursive($options, array(
			'conditions' => array(
				'Kpr.document_status' => array('approved', 'completed'),
			),
			'contain' => array(
				'ViewKprBankProvision',
			),
		));

		if($count){
			$provision = $this->controller->Kpr->getData('first', $options, $elements);					
			return Common::hashEmptyField($provision, 'Kpr.cnt');

		} else {
			return $options;
		}
	}

	function getCountFilling($options = array(), $elements = array()){
		$this->controller->Kpr->virtualFields['cnt'] = 'SUM(Property.price_measure)';

		// get sum properti
		$kpr_price = $this->controller->Kpr->getData('first', array_merge($options, array(
			'contain' => array(
				'Property',
			),
		)), $elements);

		$total_filling = Common::hashEmptyField($kpr_price, 'Kpr.cnt');

		// get sum provision
		$total_provision = $this->getProvision($options, $elements, true);

		return array(
			'total_filling' => $total_filling,
			'total_provision' => $total_provision,
		);
	}

	function rangeProperties($kprs = false){
		$resultRows = $temps = $temp = array();
		$number = 0;
		$high_price = 0;
		$top_price = false;

		if($kprs){
			foreach ($kprs as $key => $kpr) {
				$property_price = Common::hashEmptyField($kpr, 'Kpr.property_price');

				if($property_price < '500000000' ){
					$flag = '< 500 jt';
					$flag = 'under_500jt';
				
				} else if($property_price >= '500000000' && $property_price <= '1000000000'){
					$flag = '500 jt - 1 m';
					$flag = 'under_1m';
				
				} else if($property_price > '1000000000' && $property_price <= '5000000000'){
					$flag = '1 m - 5 m';
					$flag = 'under_5m';
				
				} else if($property_price > '5000000000' && $property_price <= '10000000000'){
					$flag = '5 m - 10 m';
					$flag = 'under_10m';
				
				} else {
					$flag = '> 10 m';
					$flag = 'up_10m';
				}

				if($flag){
					$count = Common::hashEmptyField($temps, $flag);
					$temps[$flag] = $count + 1;
				}
				$number++;
			}

			if($temps){
				$price_properties = Configure::read('Global.Data.price_property');

				foreach ($price_properties as $slug => $name) {
					$value = Common::hashEmptyField($temps, $slug, 0);

					$tmpVal = array(
						'reference' => $name,
						'cnt' => $value,
					);
					$temp[] = $tmpVal;

					if( $high_price < $value ) {
						$high_price = $value;
						$top_price = array(
							'name' => $name,
							'value' => $value,
						);
					}

					if($name){
						$resultRows[$name][0] = __('%s', $name);
						$resultRows[$name][1] = intval($value);
					}
				}
			}
		}

		return array(
			'temps' => $temp,
			'resultRows' => $resultRows,
			'number' => $number,
			'top_price' => $top_price,
		);
	}

	function KprOrganizer($value = false){
		$temp = array();

		if($value){
			$temp = array(
				'bankName' => Common::hashEmptyField($value, 'Bank.name'),
				'logo' => Common::hashEmptyField($value, 'Bank.logo'),
				'cnt' => Common::hashEmptyField($value, 'KprBank.cnt'),
				'interest_rate_fix' => Common::hashEmptyField($value, 'BankSetting.interest_rate_fix'),
				'interest_rate_cabs' => Common::hashEmptyField($value, 'BankSetting.interest_rate_cabs'),
			);
		}
		return $temp;
	}

	function topArea($params = array()){
		$user_group_id = Configure::read('User.group_id');

		$this->RmCommon->_callRefineParams($this->controller->params);

		if($user_group_id == 2){
			$value_arr = $this->getClientRelation($params, array(
				'conditions' => array(
					array(
						'ViewClientRelation.subarea_id <>' => NULL,
					),
					array(
						'ViewClientRelation.subarea_id <>' => 0,
					),
				),
				'order' => array(
					'ViewClientRelation.cnt' => 'DESC',
				),
				'group' => array(
					'ViewClientRelation.subarea_id',
				),
			), array(
				'cnt' => 'COUNT(ViewClientRelation.subarea_id)',
			));
		} else {
			$value_arr = $this->getClients($params, array(
				'conditions' => array(
					'OR' => array(
						array(
							array(
								'UserClient.subarea_id <>' => NULL,
							),
							array(
								'UserClient.subarea_id <>' => 0,
							),
							'OR' => array(
								array(
									'UserClient.client_ref_id' => array(4, 5, 6),
								),
								array(
									'UserClient.client_ref_id' => NULL,
								),
							),
						),
						array(
							'UserClient.client_ref_id' => array(1, 2, 3),
						),
					),
				),
			));
		}

		$count = Common::HashEmptyField($value_arr, 'count');
		$values = Common::HashEmptyField($value_arr, 'values');
		$modelName = Common::HashEmptyField($value_arr, 'modelName');

		$values = $this->filterArea($values, $modelName);

		if($count){
			$values['cnt_data'] = $count;			
		}

		return $values;
	}

	function filterArea($values = array(), $modelName = false){
		$dataTemps = array();

		if($values){
			foreach ($values as $key => $value) {
				$subarea_id = Common::hashEmptyField($value, sprintf('%s.subarea_id', $modelName));
				$client_ref_id = Common::HashEmptyField($value, __('%s.client_ref_id', $modelName));
				$count = Common::hashEmptyField($value, sprintf('%s.cnt', $modelName));

				if( empty($subarea_id) && in_array($client_ref_id, array( 1,2,3 )) ) {
					$subarea_id = Common::HashEmptyField($value, 'UserClient.additional_subarea_id');
				}

				if($subarea_id){
					$cnt = Common::hashEmptyField($dataTemps, $subarea_id);
					$dataTemps[$subarea_id] = $cnt + 1;
				}
			}

			if($dataTemps){
				arsort($dataTemps);
				$temps = $dataTemps;
				unset($dataTemps);

				$this->Subarea = ClassRegistry::init('Subarea');

				foreach ($temps as $subarea_id => $value) {
					$subarea = $this->Subarea->getData('first', array(
						'conditions' => array(
							'Subarea.id' => $subarea_id,
						),
					));

					$subareaName = Common::hashEmptyField($subarea, 'Subarea.name');
					$cityName = Common::hashEmptyField($subarea, 'City.name');
					$containName = sprintf('%s, %s', $subareaName, $cityName);

					$dataTemps[] =  array(
						'UserClient' => array(
							'name' => $containName,
							'cnt' => $value
						),
					);
				}

				$dataTemps['top_value'] = !empty($dataTemps[0])?$dataTemps[0]['UserClient']['name']:false;
			}
		}

		return $dataTemps;
	}

	function getClientRelation($params = array(), $flagOptions  = array(), $virtualFields = array(), $modelName = 'ViewClientRelation'){
		$this->controller->loadModel($modelName);
		$user_login_id = Configure::read('User.id');

		$flagConditions = Common::HashEmptyField($flagOptions, 'conditions', array());
		$contain = Common::HashEmptyField($flagOptions, 'contain', array());
		$orders = Common::HashEmptyField($flagOptions, 'order');
		$groups = Common::HashEmptyField($flagOptions, 'group');

		$data_arr = $this->controller->User->getUserParent($user_login_id);
		$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
		$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

		if($is_sales){
			$conditions = array(
				'ViewClientRelation.agent_id' => $user_ids,
			);
		} else {
			$conditions = array(
				'ViewClientRelation.company_id' => $this->controller->parent_id,
			);
		}

		$conditions = array_merge($conditions, $flagConditions);

		if($virtualFields){
			foreach ($virtualFields as $field => $val) {
					$this->controller->$modelName->virtualFields[$field] = $val;
			}
		}

		$default_options = array(
			'conditions' => $conditions,
			'contain' => $contain,
		);

		$options = $this->controller->$modelName->_callRefineParams($params, $default_options);

		$count = $this->controller->$modelName->getData('count', $options);

		$options = array_merge($options, array(
			'order' => $orders,
			'group' => $groups,
		));

		$values = $this->controller->$modelName->getData('all', $options);

		return array(
			'count' => $count,
			'values' => $values,
			'modelName' => $modelName,
		);
	}

	function getClients($params = array(), $flagOptions  = array(), $virtualFields = array(), $modelName = 'UserClient'){
		$this->controller->loadModel($modelName);
		$user_login_id = Configure::read('User.id');

		$flagConditions = Common::HashEmptyField($flagOptions, 'conditions', array());
		$orders = Common::HashEmptyField($flagOptions, 'order');
		$groups = Common::HashEmptyField($flagOptions, 'group');

		$data_arr = $this->controller->User->getUserParent($user_login_id);
		$user_ids = Common::HashEmptyField($data_arr, 'user_ids');
		$is_sales = Common::HashEmptyField($data_arr, 'is_sales');

		if($virtualFields){
			foreach ($virtualFields as $field => $val) {
				$this->controller->$modelName->virtualFields[$field] = $val;
			}
		}

		$options = $this->controller->UserClient->_callRefineParams($params, array(
			'conditions' => array_merge(array(
				'UserClient.status' => 1,
			), $flagConditions),
		));


		$options = array_merge($options, $this->controller->User->getData('paginate', $options, array(
			'status' => 'all',
		)));
		$options['contain'][] = 'User';

		$count = $this->controller->UserClient->getData('count', $options, array(
			'adminRumahku' => false,
		));

		$options = array_merge($options, array(
			'order' => $orders,
			'group' => $groups,
		));

		$options = Common::_callUnset($options, array(
			'limit',
		));
		$values = $this->controller->UserClient->getData('all', $options, array(
			'adminRumahku' => false,
		));

		return array(
			'count' => $count,
			'values' => $values,
			'modelName' => 'UserClient',
		);
	}

	function clientAge($params = array()){
		$user_group_id = Configure::read('User.group_id');

		$this->RmCommon->_callRefineParams($this->params);

		if($user_group_id == 2){
			$value_arr = $this->getClientRelation($params, array(
				'conditions' => array(
					'ViewClientRelation.birthday <>' => NULL,
				),
				'order' => array(
					'ViewClientRelation.cnt' => 'DESC',
				),
				'group' => array(
					'ViewClientRelation.age',
				),
			), array(
				'cnt' => 'COUNT(ViewClientRelation.id)',
				'age' => 'YEAR(CURDATE()) - YEAR(ViewClientRelation.birthday)',
			));
		} else {
			$value_arr = $this->getClients($params, array(
				'conditions' => array(
					'UserClient.birthday <>' => NULL,
				),
				'order' => array(
					'UserClient.cnt' => 'DESC',
				),
				'group' => array(
					'UserClient.age',
				),
			), array(
				'cnt' => 'COUNT(UserClient.id)',
				'age' => 'YEAR(CURDATE()) - YEAR(UserClient.birthday)',
			));
		}

		$count = Common::HashEmptyField($value_arr, 'count');
		$values = Common::HashEmptyField($value_arr, 'values');
		$modelName = Common::HashEmptyField($value_arr, 'modelName');

		if($values){
			$tmp = $values;
			
			if( !empty($count) ) {
				$values = $this->rangeAges($values, $modelName);

				if(!empty($count)){
					$values['cnt_data'] = $count;
				}
			}
		}

		return $values;
	}

	function _callStatusAge ($age) {
		if($age < '26'){
			$flag = 'child'; 
		} else if($age >= '26' && $age <= '35'){
			$flag = 'teens';
		} else if($age >= '36' && $age <= '50'){
			$flag = 'adult';
		} else if($age > '50') {
			$flag = 'elderly';
		} else {
			$flag = false;
		}

		return $flag;
	}

	function rangeAges($values = array(), $modelName = 'ViewClientRelation'){
		$dataTemps = $temps = array();
		if($values){
			foreach ($values as $key => $value) {
				$age = Common::hashEmptyField($value, sprintf('%s.age', $modelName));
				$cnt = Common::hashEmptyField($value, sprintf('%s.cnt', $modelName), 0);

				$flag = $this->_callStatusAge($age);

				if($flag){
					$count = Common::hashEmptyField($temps, $flag);
					$temps[$flag] = $count + $cnt;
				}
			}

			if($temps){
				$source_clients = Configure::read('Global.Data.source_client');

				foreach ($source_clients as $slug => $name) {
					$value = Common::hashEmptyField($temps, $slug, 0);

					$dataTemps[] = array(
						'UserClient' => array(
							'cnt' => $value,
							'name' => $name,
						),
					);
				}
			}
		}
		return $dataTemps;
	}

	function paymentClient($params = array()){
		$resultRows = array();
		$cnt_data = false;

		$this->controller->loadModel('UserClient');
		$user_login_id = Configure::read('User.id');

		$this->controller->loadModel('ViewPropertyPayment');

		$this->controller->ViewPropertyPayment->virtualFields['cnt'] = 'COUNT(ViewPropertyPayment.id)';

		$data_arr = $this->controller->User->getUserParent($user_login_id);
		$user_ids = Common::hashEmptyField($data_arr, 'user_ids');
		$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

		if($is_sales){
			$conditions = array(
				'ViewPropertyPayment.agent_id' => $user_ids,
			);
		}

		$conditions['ViewPropertyPayment.company_id'] = $this->controller->parent_id;

		$default_options = array(
			'conditions' => array_merge(array(
				'ViewPropertyPayment.created >=' => Common::hashEmptyField($params, 'named.date_from'),
				'ViewPropertyPayment.created <=' => Common::hashEmptyField($params, 'named.date_to'),
			), $conditions),
		);

		$this->RmCommon->_callRefineParams($this->params);
		
		$value = $this->controller->ViewPropertyPayment->getData('first', $default_options);
		$values = $this->controller->ViewPropertyPayment->getData('all', array_merge($default_options, array(
			'group' => array(
				'ViewPropertyPayment.payment_type',
			),
		)));

		$cnt_data = Common::hashEmptyField($value, 'ViewPropertyPayment.cnt');

		if($values){
			foreach ($values as $key => $value) {
				$name = Common::hashEmptyField($value, 'ViewPropertyPayment.payment_type');
				$cnt = Common::hashEmptyField($value, 'ViewPropertyPayment.cnt');

				$resultRows[] = array(
					'UserClient' => array(
						'cnt' => $cnt,
						'name' => $name,
					),
				);
			}

			if($cnt_data){
				$resultRows['cnt_data'] = $cnt_data;
			}
		}
		return $resultRows;
	}

	function typeOfClient($params = array()){
		$resultRows = array();
		$cnt_data = false;

		$user_group_id = Configure::read('User.group_id');

		$this->RmCommon->_callRefineParams($this->params);

		if($user_group_id == 2){
			$value_arr = $this->getClientRelation($params, array(
				'conditions' => array(),
				'order' => array(
					'ViewClientRelation.cnt' => 'DESC',
				),
				'group' => array(
					'ViewClientRelation.client_type_id',
				),
			), array(
				'cnt' => 'COUNT(ViewClientRelation.id)',
			));
		} else {
			$value_arr = $this->getClients($params, array(
				'conditions' => array(),
				'order' => array(
					'UserClient.cnt' => 'DESC',
				),
				'group' => array(
					'UserClient.client_type_id', 
				),
			), array(
				'cnt' => 'COUNT(UserClient.id)',
			));
		}

		$values = Common::HashEmptyField($value_arr, 'values');
		$modelName = Common::HashEmptyField($value_arr, 'modelName');

		if(!empty($values)){
			foreach ($values as $key => &$value) {
				$client_type_id = Common::hashEmptyField($value, sprintf('%s.client_type_id', $modelName));
				$value = $this->controller->$modelName->ClientType->getMerge($value, $client_type_id);

				if(empty($value['ClientType'])){
					$value['ClientType'] = array(
						'name' => __('Lainnya'),
					);
				}

				$cnt = Common::hashEmptyField($value, sprintf('%s.cnt', $modelName));
				$typeName = Common::hashEmptyField($value, 'ClientType.name');

				$resultRows[] = array(
					'UserClient' => array(
						'cnt' => $cnt,
						'name' => $typeName,
					),
				);
				$cnt_data = $cnt_data + $cnt;
			}

			if($cnt_data){
				$resultRows['cnt_data'] = $cnt_data;
			}
		}
		return $resultRows;
	}

	function getSourceClient($type = false, $params = array()){
		$date_from = Common::hashEmptyField($params, 'named.date_from');
		$date_to = Common::hashEmptyField($params, 'named.date_to');
		$result = array();

		switch ($type) {
			case 'area' :
				$result = $this->topArea($params);
				break;

			case 'client_age' :
				$result = $this->clientAge($params);
				break;

			case 'type_of_client' :
				$result = $this->typeOfClient($params);
				break;

			case 'payment' :
				$result = $this->paymentClient($params);
				break;
		}
		return $result;
	}

	function doSummary($values = array(), $options = array()){
		$dataTemp = $temp = array();

		if($values){
			$top_section = Common::hashEmptyField($options, 'top_section', false);
			$modelName = Common::hashEmptyField($options, 'model_name', 'UserClient');
			$fieldName = 'name';

			foreach ($values as $key => $value) {
				if(!empty($top_section)){
					if($key < $top_section){
						$dataTemp[] = $value;
					} else {
						$cnt = !empty($temp['cnt']) ? $temp['cnt'] : 0;
						$temp['cnt'] = $cnt + Common::hashEmptyField($value, $modelName.'.cnt');
					}
				} else {
					$dataTemp[] = $value;
				}
			}

			if(!empty($temp['cnt'])){
				$dataTemp[] = array(
					'UserClient' => array(
						'cnt' => $temp['cnt'],
						$fieldName => __('Lainnya'),
					),
				);
			}

		}
		return $dataTemp;
	}

	function getTargetConfig($params){
		$this->TargetProjectSale = ClassRegistry::init('TargetProjectSale');

		$date 		= $this->RmCommon->getDateRangeReport();
		$date_from 	= Common::hashEmptyField($date, 'date_from');
		$date_to 	= Common::hashEmptyField($date, 'date_to');

		$target_period 	= Common::hashEmptyField($params, 'named.periode_id');
		$current_year 	= Common::hashEmptyField($params, 'named.year', date('Y'));

		$date_diff = Common::monthDiff($date_from, $date_to);

		if($date_diff < 12){
			if($date_diff > 6){
				$target_period = 12;
			}else if($date_diff > 3){
				$target_period = 6;
			}else if($date_diff > 1){
				$target_period = 3;
			}else{
				$target_period = 1;
			}
		}

		$target_data = $this->TargetProjectSale->getTarget($current_year, $target_period, 'other-options');

		$TargetProjectSaleDetail = Common::hashEmptyField($target_data, 'TargetProjectSaleDetail');
		
		$target_listing = Common::hashEmptyField($target_data, 'TargetProjectSale.target_listing', 0);
		$target_revenue = Common::hashEmptyField($target_data, 'TargetProjectSale.target_revenue', 0);
		$target_ebrosur = Common::hashEmptyField($target_data, 'TargetProjectSale.target_ebrosur', 0);

		if(!empty($TargetProjectSaleDetail) && $target_period < 12){
			$target_listing = Common::hashEmptyField($target_data, 'TargetProjectSaleDetail.target_listing', 0);
			$target_revenue = Common::hashEmptyField($target_data, 'TargetProjectSaleDetail.target_revenue', 0);
			$target_ebrosur = Common::hashEmptyField($target_data, 'TargetProjectSaleDetail.target_ebrosur', 0);
			$month_target = Common::hashEmptyField($target_data, 'TargetProjectSaleDetail.month_target', 12);
		}else{
			$month_target = '12';
		}
	
		return array(
			'target_listing' => $target_listing,
			'target_revenue' => $target_revenue,
			'target_ebrosur' => $target_ebrosur,
			'month_target' => $month_target,
		);
	}

	function summaryPropertyVisitor($from, $to){
		$this->ViewUnionPropertyLeads = ClassRegistry::init('ViewUnionPropertyLeads');

		$conditions_visitor = $conditions_leads = array();

		$date_format = '%Y-%m-%d';
		if(!empty($from)){
			$conditions_visitor['DATE_FORMAT(PropertyView.created, "'.$date_format.'") >='] = $from;
			$conditions_leads['DATE_FORMAT(ViewUnionPropertyLeads.created, "'.$date_format.'") >='] = $from;
		}
		if(!empty($to)){
			$conditions_visitor['DATE_FORMAT(PropertyView.created, "'.$date_format.'") <='] = $to;
			$conditions_leads['DATE_FORMAT(ViewUnionPropertyLeads.created, "'.$date_format.'") <='] = $to;
		}

		$summary_leads = $this->ViewUnionPropertyLeads->getData('all', array(
			'conditions' => $conditions_leads,
		));

		$summary_visitor = $this->controller->User->Property->PropertyView->getViewVisitor('all', array(
			'conditions' => $conditions_visitor,
		));

		$count_visitor = 0;
		if(!empty($summary_visitor)){
			foreach ($summary_visitor as $key => $value) {
				$val = (int) Common::hashEmptyField($value, 'PropertyView.cnt', 0);

				$count_visitor += $val;
			}
		}

		$count_leads = $count_hot_leads = 0;
		if(!empty($summary_leads)){
			foreach ($summary_leads as $key => $value) {
				$type_lead = Common::hashEmptyField($value, 'ViewUnionPropertyLeads.type_lead');
				$val = (int) Common::hashEmptyField($value, 'ViewUnionPropertyLeads.cnt', 0);

				if($type_lead == 'leads'){
					$count_leads += $val;
				}else{
					$count_hot_leads += $val;
				}
			}
		}

		return array(
			'count_visitor' 	=> $count_visitor,
			'count_leads' 		=> $count_leads,
			'count_hot_leads' 	=> $count_hot_leads,
		);
	}

	function AVGSoldRent($data_arr = array(), $params = array()){
		$user_id = Common::hashEmptyField($data_arr, 'user_ids');
		$is_sales = Common::hashEmptyField($data_arr, 'is_sales');

		if(!$is_sales){
			$user_id = $this->controller->User->getData('list', array(
				'conditions' => array(
					'User.parent_id' => $this->controller->parent_id,
					'User.group_id' => 2,
				),
				'fields' => array(
					'id', 'id'
				),
			), array(
				'status' => array(
					'active',
				),
			));
		}

		$default_options = array(
			'conditions' => array(
				'PropertySold.sold_by_id' => $user_id,
				'PropertySold.created >=' => Common::hashEmptyField($params, 'named.date_from'),
				'PropertySold.created <=' => Common::hashEmptyField($params, 'named.date_to'),
			),
		);

		// terjual
		$this->controller->User->Property->PropertySold->virtualFields['cnt'] = 'AVG(PropertySold.price_sold)';

		$propertySold = $this->controller->User->Property->PropertySold->getData('first',array_merge_recursive(array(
			'conditions' => array(
				'PropertySold.property_action_id' => 1,
			),
		), $default_options));

		// tersewa
		$propertyRent = $this->controller->User->Property->PropertySold->getData('first',array_merge_recursive(array(
			'conditions' => array(
				'PropertySold.property_action_id' => 2,
			),
		), $default_options));

		return array(
			'avg_sold' => Common::hashEmptyField($propertySold, 'PropertySold.cnt'),
			'avg_rent' => Common::hashEmptyField($propertyRent, 'PropertySold.cnt'),
		);
	}

	function _callDurationTime($result = array(), $max_val = 0){
		$temp = array();

		$divider = floor($max_val / 4);

		for ($i=1; $i <= 24 ; $i++) { 
			for ($j=0; $j < 7 ; $j++) {

				if(strlen($i) == 1){
					$i = '0'.$i;
				}

				$value_arr = Common::hashEmptyField($result, $j, array());
				$value = Hash::Extract($value_arr, sprintf('{n}.Log[hour=%s]', $i));
				$value = array_shift($value);
				$val = Common::hashEmptyField($value, 'cnt', 0);
				$parentCnt = Common::hashEmptyField($value, 'parent_cnt', 0);

				// get color class
				if($val <= $divider){
					$class = 'color-fase-1';
				} else if($val > $divider && $val < ($divider * 2)){
					$class = 'color-fase-2';
				} else if( ($val > ($divider*2)) && $val < ($divider * 3) ){
					$class = 'color-fase-3';
				} else {
					$class = 'color-fase-4';
				}

				$temp[$i][$j] = array(
					'cnt' => $val,
					'class' => $class,
					'parentCnt' => $parentCnt,
				);
			}
		}

		return array(
			'temp' => $temp,
			'divider' => array(
				array(
					'cnt' => $divider,
					'class' => 'color-fase-1',
				),
				array(
					'cnt' => ($divider * 2),
					'class' => 'color-fase-2',
				),
				array(
					'cnt' => ($divider * 3),
					'class' => 'color-fase-3',
				),
				array(
					'cnt' => $max_val,
					'class' => 'color-fase-4',
				),
			),
		);
	}

	function getShare($params = array()){
		$resultRows = array();
		$cnt_data = false;
		$isAgent = Common::isAgent();

		$this->RmCommon->_callRefineParams($this->params);

		$this->controller->loadModel('ShareLog');

		$this->controller->ShareLog->virtualFields['cnt'] = 'COUNT(ShareLog.id)';
		$options = $this->controller->ShareLog->_callRefineParams($params, array(
			'order' => array(
				'ShareLog.cnt' => 'DESC',
			),
		));

		$count = $this->controller->ShareLog->getData('count', $options, array(
			'mine' => !empty($isAgent)?true:false,
		));

		$options['group'] = array(
			'ShareLog.sosmed',
		);
		$values = $this->controller->ShareLog->getData('all', $options, array(
			'mine' => !empty($isAgent)?true:false,
		));

		if(!empty($values)){
			foreach ($values as $key => &$value) {
				$cnt = Common::hashEmptyField($value, 'ShareLog.cnt');
				$typeName = Common::hashEmptyField($value, 'ShareLog.sosmed');

				$resultRows[] = array(
					'ShareLog' => array(
						'cnt' => $cnt,
						'name' => $typeName,
					),
				);
				$cnt_data = $cnt_data + $cnt;
			}

			if($cnt_data){
				$resultRows['cnt_data'] = $cnt_data;
			}
		}

		return $resultRows;
	}
	
	function _callBeforeViewShareDetail ( $values ) {
		if( !empty($values) ) {
			foreach( $values as $key => &$value ) {
				$value = $this->controller->ShareLog->getMergeList($value, array(
					'contain' => array(
						'Group' => array(
							'forceMerge' => true,
						),
						'User' => array(
							'forceMerge' => true,
							'elements' => array(
								'status' => false,
								'role' => array(
									'user-company',
								),
							),
						), 
						'Property' => array(
							'forceMerge' => true,
						),
						'Advice' => array(
							'forceMerge' => true,
						),
						'UserCompanyEbrochure' => array(
							'forceMerge' => true,
						),
					), 
				));
			}
		}

		$divisiOptions = $this->controller->User->Group->getDivisionCompany(array(
			'userID' => $this->controller->parent_id,
		));
		$module_types = Configure::read('__Site.Global.Share.Module');
		$sosmeds = Configure::read('__Site.Global.Share.Sosmed');

		$this->controller->set('active_menu', 'kpi_marketing');
		$this->controller->set(compact(
			'values', 'module_title', 'divisiOptions',
			'module_types', 'sosmeds'
		));
	}
}
?>