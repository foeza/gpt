<?php
class RmMarketTrendComponent extends Component {
	public $components = array(
		'RmCommon', 
	);

	public function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	public function getStatistic($params = array()){
		$params		= (array) $params;
		$actionID	= Hash::get($params, 'property_action_id');
		$typeID		= Hash::get($params, 'property_type_id', array());
		$typeSlug	= Hash::get($params, 'property_type', array());
		$period		= Hash::get($params, 'period', 6);
		$periodFrom	= Hash::get($params, 'period_from');
		$periodTo	= Hash::get($params, 'period_to');
		$regionID	= Hash::get($params, 'region_id', 0);
		$cityID		= Hash::get($params, 'city_id', 0);
		$subareaID	= Hash::get($params, 'subarea_id', 0);
		$isSold		= Hash::get($params, 'is_sold', 0);

		$propertyTypes = array();

		if(empty($typeID) && $typeSlug){
			$orderField = is_array($typeSlug) ? implode("', '", $typeSlug) : $typeSlug;
			$orderField = sprintf('FIELD(PropertyType.slug, \'%s\')', $orderField);

			$propertyTypes = $this->controller->User->Property->PropertyType->getData('list', array(
				'conditions'	=> array('PropertyType.slug' => $typeSlug), 
				'order'			=> array($orderField), 
			));

			$typeID = array_keys($propertyTypes);
		}

		if($periodFrom || $periodTo){
		//	get data by date range
		//	do nothing
		}
		else if($period){
		//	get data by period
			$periodTo	= date('Y-m-d');
			$periodFrom	= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $periodTo, $period)));
		}
		else{
		//	get data by active month
			$periodTo	= date('Y-m-d');
			$periodFrom	= date('Y-m-01');
		}

		$controller		= Inflector::camelize($this->controller->name);
		$action			= Inflector::camelize($this->controller->action);

		$companyData	= Configure::read('Config.Company.data');
		$companyID		= Hash::get($companyData, 'UserCompany.id', 0);
	//	$allCompany		= Hash::get($companyData, 'UserCompanyConfig.mt_is_all_company_data');
		$allCompany		= true; // sekarang force true, sampe nanti next dev tambah config
		$cachePrefix	= 'statistic-company';

		if($allCompany){
			$companyID		= 0;
			$cachePrefix	= 'statistic-all';
		}

		$cacheName = array(
			'cache_prefix'			=> $cachePrefix, 
			'controller' 			=> $controller, 
			'action' 				=> $action, 
			'company_id' 			=> $companyID, 
			'property_action_id'	=> $actionID, 
			'property_type_id'		=> is_array($typeID) ? implode('-', $typeID) : $typeID, 
			'region_id'				=> $regionID, 
			'city_id'				=> $cityID, 
			'subarea_id'			=> $subareaID, 
			'is_sold'				=> $isSold, 
			'period_from'			=> date('Ymd', strtotime($periodFrom)),
			'period_to'				=> date('Ymd', strtotime($periodTo)),
		);

		$cacheName		= implode('.', $cacheName);
		$cacheConfig	= 'market_trend';
		$cacheData		= Cache::read($cacheName, $cacheConfig);
		$cacheData		= array_filter((array) $cacheData);

		$results = Hash::get($cacheData, 'result', array()); // ambil dari cache

		if(empty($results)){
			$model = 'ViewUnionPropertySubarea';

			if($isSold){
				$priceField	= $model.'.sold_price_measure';
				$dateField	= $model.'.sold_date';
			}
			else{
				$priceField	= $model.'.price_measure';
				$dateField	= $model.'.publish_date';
			}

			$this->controller->loadModel($model);
			$this->controller->$model->virtualFields = array(
				'property_size'		=> 'SUM(CASE WHEN COALESCE('.$model.'.lot_size, 0) > 0 THEN '.$model.'.lot_size ELSE '.$model.'.building_size END)',
				'property_count'	=> 'COUNT('.$model.'.property_id)',
				'price_measure'		=> 'SUM('.$priceField.')',
				'filter_date'		=> 'DATE_FORMAT('.$dateField.', "%Y-%m")',
				'filter_year'		=> 'DATE_FORMAT('.$dateField.', "%Y")',
			);

			if($period <= 12){
				$this->controller->$model->virtualFields['filter_month'] = 'DATE_FORMAT('.$dateField.', "%m")';
			}

		//	build conditions
			$conditions = array(
				$priceField.' >' => 0, 
			);

			if($companyID){
				$conditions[$model.'.company_id'] = $companyID;
			}

			if($periodFrom){
				$conditions['DATE_FORMAT('.$dateField.', "%Y-%m-%d") >='] = $periodFrom;
			}

			if($periodTo){
				$conditions['DATE_FORMAT('.$dateField.', "%Y-%m-%d") <='] = $periodTo;
			}

			if($regionID){
				$conditions[$model.'.region_id'] = $regionID;
			}

			if($cityID){
				$conditions[$model.'.city_id'] = $cityID;
			}

			if($subareaID){
				$conditions[$model.'.subarea_id'] = $subareaID;
			}

			if($actionID){
				$isSold		= in_array($actionID, array(1, 2)) ? 0 : 1;	// 1 dijual, 2 disewa, 3 terjual, 4 tersewa
				$actionID	= in_array($actionID, array(1, 3)) ? 1 : 2;	// action id chart ini custom

				$conditions[$model.'.property_action_id'] = $actionID;
			}

			$conditions[$model.'.sold'] = $isSold;

			$orderField = $model.'.property_type_id';

			if($typeID){
				$orderField = is_array($typeID) ? implode("', '", $typeID) : $typeID;
				$orderField = sprintf('FIELD(%s.property_type_id, \'%s\')', $model, $orderField);

				$conditions[$model.'.property_type_id'] = $typeID;
			}

			$fields = array(
				$model.'.property_action_id',
				$model.'.property_type_id',
				$model.'.region_id',
				$model.'.city_id',
				$model.'.subarea_id',
				$model.'.filter_date',
				$model.'.sold',
			);

			$orders = array(
				$model.'.filter_date'			=> 'DESC',
				$model.'.property_action_id'	=> 'ASC',
				$orderField,
			);

			$records = $this->controller->$model->getData('all', array(
				'fields'		=> array_merge($fields, array_keys($this->controller->$model->virtualFields)),
				'conditions'	=> $conditions,
				'group'			=> $fields,
				'order'			=> $orders,
			));

		//	if($records){
		//		$currency	= Configure::read('__Site.config_currency_symbol');
		//		$actions	= Set::extract('/ReportPropertySubarea/property_action_id', $records);
		//		$actions	= array_unique($actions);
		//		$results	= array();
		//	}

			$propertyActions = $this->controller->User->Property->PropertyAction->getData('list', array(
				'fields'		=> array('PropertyAction.id', sprintf('PropertyAction.%sname', $isSold ? 'inactive_' : '')), 
				'order'			=> array('PropertyAction.id' => 'DESC'), 
				'conditions'	=> $actionID ? array(
					'PropertyAction.id' => $actionID, 
				) : false, 
			));

			if(empty($propertyTypes)){
				$orderField = is_array($typeID) ? implode("', '", $typeID) : $typeID;
				$orderField = sprintf('FIELD(PropertyType.id, \'%s\')', $orderField);

				$propertyTypes = $this->controller->User->Property->PropertyType->getData('list', array(
					'conditions'	=> array('PropertyType.id' => $typeID), 
					'order'			=> array($orderField), 
				));
			}

			$format		= $this->getChartFormat($period);
			$colCount	= Hash::get($format, 'col_count', 0);
			$dateUnit	= Hash::get($format, 'date_unit');
			$dateFormat	= Hash::get($format, 'date_format');

		//	RESERVING INDEX FOR FIRST COLUMN
			$colData = array(0 => array(__('Tanggal')));
			$rowData = array();

			if($period > 12 && (date('m') > 1 && date('m') < 12)){
				$colCount++;
			}

			$averages = array(
				'avg_price_measure'	=> array(), 
				'avg_lot_price'		=> array(), 
				'max_price'			=> array(), 
				'min_price'			=> array(), 
			);

			for($colIndex = 0; $colIndex < $colCount; $colIndex++){
				$microtime	= strtotime(sprintf('%s + %s %s', $periodFrom, $colIndex, $dateUnit));
				$dateIndex	= date($dateFormat, $microtime);
				$yearIndex	= (int) date('Y', $microtime);
				$monthIndex	= (int) date('m', $microtime);
				$dayIndex	= (int) date('d', $microtime);

			//	RESERVED INDEX FOR EVERY FIRST ROW
				$rowData[$colIndex] = array(0 => $dateIndex);

				foreach($propertyTypes as $propertyTypeID => $propertyTypeName){
				//	B:GET AVERAGE PRICE VALUE FROM RECORDS =======================================================

					foreach($propertyActions as $propertyActionID => $propertyActionName){
						$rowIndex	= sprintf('%s-%s', $propertyTypeID, $propertyActionID);
						$colName	= sprintf('%s %s', $propertyTypeName, $propertyActionName);

						$averagePrice		= 0;
						$averageLotPrice	= 0;

					//	APPEND PROPERTY_TYPE_NAME TO COLUMN
						$colData[0][$rowIndex] = $colName;

					//	$selector = '/%s[property_action_id=%s][property_type_id=%s][created_year=%s]';
						$selector = '/%s[property_action_id=%s][property_type_id=%s][filter_year=%s]';
						$selector = sprintf($selector, $model, $propertyActionID, $propertyTypeID, $yearIndex);

						if($period > 1 && $period <= 12){
						//	$selector = sprintf('%s[created_month=%s]', $selector, str_pad($monthIndex, 2, 0, STR_PAD_LEFT));
							$selector = sprintf('%s[filter_month=%s]', $selector, str_pad($monthIndex, 2, 0, STR_PAD_LEFT));
						}

						$priceMeasure	= Set::extract(sprintf('%s[price_measure>0]/price_measure', $selector), $records);
						$propertyCount	= Set::extract(sprintf('%s[price_measure>0]/property_count', $selector), $records);
						$propertySize	= Set::extract(sprintf('%s[price_measure>0]/property_size', $selector), $records);

						$averages['property_count'][$rowIndex][] = array_sum($propertyCount);

						if($priceMeasure){
							$priceMeasure	= array_sum($priceMeasure) / count($priceMeasure);
							$propertyCount	= array_sum($propertyCount) / count($propertyCount);
							$propertySize 	= array_sum($propertySize) / count($propertySize);

							$averagePrice		= 0;
							$averageLotPrice	= 0;

							if($priceMeasure){
								if($propertyCount){
									$averagePrice = floatval($priceMeasure / $propertyCount);
								}

								if($propertySize){
									$averageLotPrice = floatval($priceMeasure / $propertySize);
								}
							}

						//	$debug = PHP_EOL.$propertyTypeName.' avg per unit = '.$priceMeasure.' / '.$propertyCount.' = '.$averagePrice;
						//	$debug.= PHP_EOL.$propertyTypeName.' avg per meter= '.$priceMeasure.' / '.$propertySize.' = '.$averageLotPrice;

						//	debug($debug);

							if(isset($averages['min_price'][$rowIndex]) === false){
								$averages['min_price'][$rowIndex] = $averagePrice;
								$averages['max_price'][$rowIndex] = $averagePrice;
							}

							if($averagePrice < $averages['min_price'][$rowIndex]){
								$averages['min_price'][$rowIndex] = $averagePrice;
							}

							if($averagePrice > $averages['max_price'][$rowIndex]){
								$averages['max_price'][$rowIndex] = $averagePrice;
							}
						}
						else{
							$priceMeasure = 0;
						}

						$averages['avg_price_measure'][$rowIndex][]	= $averagePrice;
						$averages['avg_lot_price'][$rowIndex][]		= $averageLotPrice;

					//	APPEND AVERAGE PRICE VALUE TO ROW
						$rowData[$colIndex][$rowIndex] = ceil($averagePrice);
					}

				//	E:GET AVERAGE PRICE VALUE FROM RECORDS =======================================================
				}
			}

		//	count average for each property type
			$totalPropertyCount = Hash::extract($averages, 'property_count.{s}.{n}');
			$totalPropertyCount = array_sum($totalPropertyCount);

			foreach($averages as $fieldName => $averageDetails){
				if(in_array($fieldName, array('min_price', 'max_price'))){
				//	skip
					continue;
				}

				$averageDetails = array_filter($averageDetails);

				if($averageDetails){
					foreach($averageDetails as $key => $values){
						$values = array_filter($values);

						if($fieldName == 'property_count'){
						//	count property ga usah hitung rata2
							$values = $values ? array_sum($values) : 0;
						}
						else{
						//	uang hitung rata2
							$values = $values ? array_sum($values) / count($values) : 0;
						}

					//	re-append
						$averages = Hash::insert($averages, sprintf('%s.%s', $fieldName, $key), $values);
					}
				}
			}

			$currency = Configure::read('__Site.config_currency_symbol', 'Rp. ');
			$averages = Hash::insert($averages, 'currency', $currency);
			$averages = Hash::insert($averages, 'total_property_count', $totalPropertyCount);

			$results = array(
				'chart'		=> array_merge(array_values($colData), array_values($rowData)),
				'summary'	=> $averages,
				'data'		=> $records, 
			);

		//	store results to cache
			$cacheData = array(
				'named'		=> $this->controller->request->named,
				'pass'		=> $this->controller->request->pass,
				'query'		=> $this->controller->request->query,
				'result'	=> $results, 
			);

			Cache::write($cacheName, $cacheData, $cacheConfig);
		}
		else{
		//	get data from cache
			$this->controller->request->named	= Hash::get($cacheData, 'named', array());
			$this->controller->request->pass	= Hash::get($cacheData, 'pass', array());
			$this->controller->request->query	= Hash::get($cacheData, 'query', array());
			$results							= Hash::get($cacheData, 'result', array());
		}

		return $results;
	}

	public function getSummary($options = array()){
		$params		= (array) $this->controller->params;
		$options	= (array) $options;

		$isAdmin		= Configure::read('User.admin');
		$authUserID		= Configure::read('User.id');
		$principleID	= Configure::read('Principle.id');

		$propertyCap	= Configure::read('Config.MarketTrend.property_cap');
		$typeSlug		= Configure::read('Config.MarketTrend.default_property_type');

	//	debug($typeSlug);exit;

	//	cache setting
		$controller		= Inflector::camelize($this->controller->name);
		$action			= Inflector::camelize($this->controller->action);

		$companyData	= Configure::read('Config.Company.data');
		$companyID		= Hash::get($companyData, 'UserCompany.id', 0);
	//	$allCompany		= Hash::get($companyData, 'UserCompanyConfig.mt_is_all_company_data');
		$allCompany		= true; // sekarang force true, sampe nanti next dev tambah config

		if($options){
			$period			= Hash::get($options, 'period');
			$periodFrom		= Hash::get($options, 'period_from');
			$periodTo		= Hash::get($options, 'period_to');
			$regionID		= Hash::get($options, 'region_id', 0);
			$cityID			= Hash::get($options, 'city_id', 0);
			$subareaID		= Hash::get($options, 'subarea_id', 0);
			$actionID		= Hash::get($options, 'property_action_id', 0);
			$typeID			= Hash::get($options, 'property_type_id', 0);
			$typeSlug		= Hash::get($options, 'property_type_slug', 0);
		}
		else{
		//	default location by company
			$regionID		= Hash::get($companyData, 'UserCompany.region_id', 0);
			$cityID			= Hash::get($companyData, 'UserCompany.city_id', 0);
			$subareaID		= Hash::get($companyData, 'UserCompany.subarea_id', 0);

		//	location by setting
			$regionID		= Hash::get($companyData, 'UserCompanyConfig.mt_region_id', $regionID);
			$cityID			= Hash::get($companyData, 'UserCompanyConfig.mt_city_id', $cityID);
			$subareaID		= Hash::get($companyData, 'UserCompanyConfig.mt_subarea_id', $subareaID);

		//	location by filter
			$regionID		= Hash::get($params, 'params.named.region', $regionID);
			$cityID			= Hash::get($params, 'params.named.city', $cityID);
			$subareaID		= Hash::get($params, 'params.named.subarea', $subareaID);
			$actionID		= Hash::get($params, 'params.named.property_action', 0);
			$typeID			= Hash::get($params, 'params.named.typeid', 0);
			$typeSlug		= Hash::get($params, 'params.named.type', 0);
			$typeSlug		= explode(',', urldecode($typeSlug));
		}

		$period			= Hash::get($params, 'params.named.period', $period);
		$periodFrom		= Hash::get($params, 'params.named.period_from', $periodFrom);
		$periodTo		= Hash::get($params, 'params.named.period_to', $periodTo);

		if(empty($typeID) && $typeSlug){
			$orderField = is_array($typeSlug) ? implode("', '", $typeSlug) : $typeSlug;
			$orderField = sprintf('FIELD(PropertyType.slug, \'%s\')', $orderField);

			$propertyTypes = $this->controller->User->Property->PropertyType->getData('list', array(
				'conditions'	=> array('PropertyType.slug' => $typeSlug), 
				'order'			=> array($orderField), 
			));

			$typeID = array_keys($propertyTypes);
		}

		if($periodFrom || $periodTo){
		//	get data by date range
		//	do nothing
		}
		else if($period){
		//	get data by period
			$periodTo	= date('Y-m-d');
			$periodFrom	= date('Y-m-01', strtotime(sprintf('%s - %s MONTH', $periodTo, $period)));
		}
		else{
		//	get data by active month
			$periodTo	= date('Y-m-d');
			$periodFrom	= date('Y-m-01');
		}

		$cachePrefix = 'summary-company';

		if($allCompany){
			$companyID		= 0;
			$cachePrefix	= 'summary-all';
		}

		$cacheName = array(
			'cache_prefix'			=> $cachePrefix, 
			'controller' 			=> $controller, 
			'action' 				=> $action, 
			'company_id' 			=> $companyID, 
			'property_action_id'	=> $actionID, 
			'property_type_id'		=> is_array($typeID) ? implode('-', $typeID) : $typeID, 
			'region_id'				=> $regionID, 
			'city_id'				=> $cityID, 
			'subarea_id'			=> $subareaID, 
			'period_from'			=> date('Ymd', strtotime($periodFrom)),
			'period_to'				=> date('Ymd', strtotime($periodTo)),
		);

		$cacheName		= implode('.', $cacheName);
		$cacheConfig	= 'market_trend';
		$cacheData		= Cache::read($cacheName, $cacheConfig);
		$cacheData		= array_filter((array) $cacheData);

		$results = Hash::get($cacheData, 'result', array()); // ambil dari cache

		if(empty($results)){
			$modelName		= 'ViewUnionPropertySubarea';
			$propertyLimit	= false;

			if(empty($this->controller->$modelName->alias)){
				$this->controller->loadModel($modelName);
			}

		//	==========================================================================================================
		//	note : 
		//	kata ko hengky sekarang ga pake limit (awal-awal), mungkin nanti dipake, jadi sekarang di comment
		//	==========================================================================================================

		/*	limit setting
			if($allCompany){
				$countOwned = $this->controller->$modelName->getData('count', array(
					'contain'		=> array('User'), 
					'conditions'	=> array(
						'OR' => array(
							'User.id'			=> $principleID, 
							'User.parent_id'	=> $principleID, 
						)
					),
				));

				if($propertyCap && $countOwned < $propertyCap){
					$propertyLimit = $countOwned;
				}
			}
		*/

		//	B : GET COUNT SUMMARY ====================================================================================

			$this->controller->$modelName->virtualFields = array(
				'property_count'	=> 'COUNT(ViewUnionPropertySubarea.property_id)', 
				'price_measure'		=> 'SUM(CASE WHEN ViewUnionPropertySubarea.sold = 1 THEN ViewUnionPropertySubarea.sold_price_measure ELSE ViewUnionPropertySubarea.price_measure END)', 
			//	'filter_date'		=> 'DATE_FORMAT(CASE WHEN ViewUnionPropertySubarea.sold = 1 THEN ViewUnionPropertySubarea.sold_date ELSE ViewUnionPropertySubarea.publish_date END, "%Y-%m")', 
			);

			$defaultOptions = array(
				'contain' => array(
				//	'PropertyAction', 
				//	'PropertyType', 
				), 
				'fields' => array(
					'ViewUnionPropertySubarea.property_action_id', 
					'ViewUnionPropertySubarea.property_type_id', 
					'ViewUnionPropertySubarea.sold', 
					'ViewUnionPropertySubarea.property_count', 
					'ViewUnionPropertySubarea.price_measure', 
				//	'PropertyAction.*', 
				//	'PropertyType.*', 
				), 
				'group' => array(
					'ViewUnionPropertySubarea.property_action_id', 
					'ViewUnionPropertySubarea.property_type_id', 
					'ViewUnionPropertySubarea.sold', 
				), 
				'order' => array(
					'ViewUnionPropertySubarea.property_action_id', 
					'ViewUnionPropertySubarea.property_type_id', 
					'ViewUnionPropertySubarea.sold', 
				), 
			);

			if($actionID){
				$defaultOptions['conditions']['ViewUnionPropertySubarea.property_action_id'] = $actionID;
			}

			if($typeID){
				$defaultOptions['conditions']['ViewUnionPropertySubarea.property_type_id'] = $typeID;
			}

			if($periodFrom){
				$defaultOptions['conditions']['DATE_FORMAT(ViewUnionPropertySubarea.publish_date, "%Y-%m-%d") >=']	= $periodFrom;
				$defaultOptions['conditions']['DATE_FORMAT(ViewUnionPropertySubarea.sold_date, "%Y-%m-%d") >=']		= $periodFrom;
			}

			if($periodTo){
				$defaultOptions['conditions']['DATE_FORMAT(ViewUnionPropertySubarea.publish_date, "%Y-%m-%d") <=']	= $periodTo;
				$defaultOptions['conditions']['DATE_FORMAT(ViewUnionPropertySubarea.sold_date, "%Y-%m-%d") <=']		= $periodTo;
			}

			if($regionID){
				$defaultOptions['conditions']['ViewUnionPropertySubarea.region_id'] = $regionID;
			}

			if($cityID){
				$defaultOptions['conditions']['ViewUnionPropertySubarea.city_id'] = $cityID;
			}

			if($subareaID){
				$defaultOptions['conditions']['ViewUnionPropertySubarea.subarea_id'] = $subareaID;
			}

		//	copy options value
			$summaryOptions = $defaultOptions;

		/*	limit setting
			if((empty($isAdmin) && empty($allCompany)) || ($allCompany && $propertyLimit)){
				$summaryOptions['contain'][] = 'User';

				$summaryOptions['conditions']['OR']['User.id']			= $principleID;
				$summaryOptions['conditions']['OR']['User.parent_id']	= $principleID;
			}
		*/

		//	SUMMARY INTERNAL COMPANY =================================================================================

			$internalSummaries = $this->controller->$modelName->getData('all', $summaryOptions);
			$internalSummaries = $this->formatSummary($internalSummaries);

		//	get harga lot per meter
			$averageLotOptions = array_replace_recursive($summaryOptions, array(
				'fields'		=> array(
					'ViewUnionPropertySubarea.property_action_id', 
					'ViewUnionPropertySubarea.sold', 
					'ViewUnionPropertySubarea.property_count', 
					'ViewUnionPropertySubarea.price_measure', 
					'ViewUnionPropertySubarea.lot_price_measure', 
				), 
				'conditions'	=> array(
					'ViewUnionPropertySubarea.property_type_id' => 2, // tanah
				), 
			));

		//	itung lot
			$this->controller->$modelName->virtualFields['lot_price_measure'] = 'SUM(ViewUnionPropertySubarea.lot_price_measure)';

			$internalLotSummaries = $this->controller->$modelName->getData('all', $averageLotOptions);
			$internalLotSummaries = $this->formatSummary($internalLotSummaries);

		//	debug($internalLotSummaries);
		//	debug($averageLotOptions);exit;

		/*	limit setting
			if(empty($isAdmin) && $allCompany && $propertyLimit){
			//	reset options value
				$summaryOptions = $defaultOptions;

				$summaryOptions['contain'][]	= 'User';
				$summaryOptions['limit']		= $propertyLimit;

				$summaryOptions['conditions']['User.id <>']			= $principleID;
				$summaryOptions['conditions']['User.parent_id <>']	= $principleID;

			//	SUMMARY EXTERNAL COMPANY =============================================================================

				$externalSummaries = $this->controller->$modelName->getData('all', $summaryOptions);

				if($externalSummaries){
					$externalSummaries = $this->formatSummary($externalSummaries);

					foreach($externalSummaries as $actionSlug => $externalSummary){
						$internalCount = Hash::get($internalSummaries, sprintf('%s.property_count', $actionSlug), 0);
						$externalCount = Hash::get($externalSummary, 'property_count', 0);

						$internalSummaries[$actionSlug]['property_count'] = $internalCount + $externalCount;
					}
				}

				$externalLotSummaries = $this->controller->$modelName->getData('all', $averageLotOptions);

				if($externalLotSummaries){
					$externalLotSummaries = $this->formatSummary($externalLotSummaries);

					foreach($externalLotSummaries as $actionSlug => $externalLotSummary){
						$internalCount = Hash::get($internalLotSummaries, sprintf('%s.property_count', $actionSlug), 0);
						$internalPrice = Hash::get($internalLotSummaries, sprintf('%s.lot_price_measure', $actionSlug), 0);

						$externalCount = Hash::get($externalLotSummary, 'property_count', 0);
						$externalPrice = Hash::get($externalLotSummary, ''lot_price_measure', 0);

						$internalLotSummaries[$actionSlug]['property_count']	= $internalCount + $externalCount;
						$internalLotSummaries[$actionSlug]['lot_price_measure']	= $internalPrice + $externalPrice;
					}
				}
			}
		*/

		//	E : GET COUNT SUMMARY ====================================================================================

		//	B : GET MOST SEARCHED ====================================================================================

			$this->controller->User->Property->PropertyView->virtualFields = array(
				'view_count'		=> 'COUNT(ViewUnionPropertySubarea.property_id)', 
				'min_price_measure'	=> 'MIN(ViewUnionPropertySubarea.price_measure)', 
				'max_price_measure'	=> 'MAX(ViewUnionPropertySubarea.price_measure)', 
			);

			$this->controller->User->Property->PropertyView->bindModel(array(
				'hasOne' => array(
					'ViewUnionPropertySubarea' => array(
						'foreignKey' => false, 
						'conditions' => array(
							'PropertyView.property_id = ViewUnionPropertySubarea.property_id', 
						), 
					), 
				), 
			));

			$options = array(
				'conditions'	=> array(), 
				'fields'		=> array(
					'ViewUnionPropertySubarea.property_action_id', 
					'ViewUnionPropertySubarea.property_type_id', 
					'ViewUnionPropertySubarea.region_id',
					'ViewUnionPropertySubarea.city_id',
					'ViewUnionPropertySubarea.subarea_id',
					'PropertyView.view_count', 
					'PropertyView.min_price_measure', 
					'PropertyView.max_price_measure', 
				), 
				'contain'		=> array('ViewUnionPropertySubarea'), 
				'group'			=> array('ViewUnionPropertySubarea.property_action_id', 'ViewUnionPropertySubarea.property_type_id'), 
				'order'			=> array('PropertyView.view_count' => 'DESC'), 
			);

			if($periodFrom){
				$options['conditions']['DATE_FORMAT(PropertyView.created, "%Y-%m-%d") >='] = $periodFrom;
			}

			if($periodTo){
				$options['conditions']['DATE_FORMAT(PropertyView.created, "%Y-%m-%d") <='] = $periodTo;
			}

			$contains = array('PropertyAction', 'PropertyType');

			if($regionID){
				$contains[] = 'Region';
				$options['conditions']['ViewUnionPropertySubarea.region_id'] = $regionID;
			}

			if($cityID){
				$contains[] = 'City';
				$options['conditions']['ViewUnionPropertySubarea.city_id'] = $cityID;
			}

			if($subareaID){
				$contains[] = 'Subarea';
				$options['conditions']['ViewUnionPropertySubarea.subarea_id'] = $subareaID;
			}

			$searchResult = $this->controller->User->Property->PropertyView->getData('first', $options);

			if($contains){
				foreach($contains as $containModel){
					$fieldName	= Inflector::underscore($containModel);
					$fieldID	= Hash::get($searchResult, sprintf('ViewUnionPropertySubarea.%s_id', $fieldName));

					if($fieldID){
						$searchResult = $this->controller->$modelName->$containModel->getMerge($searchResult, $fieldID);
					}
				}
			}

		//	E : GET MOST SEARCHED ====================================================================================

			$results = array(
				'Summary' => array(
					'lot_price'			=> $internalLotSummaries, 
					'property_count'	=> $internalSummaries, 
					'most_searched'		=> $searchResult, 
				),  
			);

		//	store results to cache
			$cacheData = array(
				'named'		=> $this->controller->request->named,
				'pass'		=> $this->controller->request->pass,
				'query'		=> $this->controller->request->query,
				'result'	=> $results, 
			);

			Cache::write($cacheName, $cacheData, $cacheConfig);
		}
		else{
		//	get data from cache
			$this->controller->request->named	= Hash::get($cacheData, 'named', array());
			$this->controller->request->pass	= Hash::get($cacheData, 'pass', array());
			$this->controller->request->query	= Hash::get($cacheData, 'query', array());
			$results							= Hash::get($cacheData, 'result', array());
		}

		return $results;
	}

	public function formatSummary($summaries = array()){
		$summaries	= (array) $summaries;
		$result		= array();

		if($summaries){
			$status = array(
				1 => array(
					0 => 'dijual', 
					1 => 'terjual', 
				), 
				2 => array(
					0 => 'disewa', 
					1 => 'tersewa', 
				), 
			);

			foreach($status as $actionID => $soldStatuses){
				foreach($soldStatuses as $isSold => $statusName){
					$selector	= sprintf('/ViewUnionPropertySubarea[property_action_id=%s][sold=%s]', $actionID, $isSold);
					$values		= Set::extract($selector, $summaries);
					$counts		= Hash::extract($values, '{n}.ViewUnionPropertySubarea.property_count');
					$prices		= Hash::extract($values, '{n}.ViewUnionPropertySubarea.price_measure');
					$lotPrices	= Hash::extract($values, '{n}.ViewUnionPropertySubarea.lot_price_measure');
					$result		= Hash::insert($result, $statusName, array(
						'property_action_id'	=> $actionID, 
						'sold'					=> $isSold, 
						'property_count'		=> array_sum($counts), 
						'price_measure'			=> array_sum($prices), 
						'lot_price_measure'		=> array_sum($lotPrices), 
					));
				}
			}

		/*
			foreach($summaries as $key => $summary){
				$actionID	= Hash::get($summary, 'ViewUnionPropertySubarea.property_action_id');
				$typeID		= Hash::get($summary, 'ViewUnionPropertySubarea.property_type_id');

				if($actionID){
					$summary = $this->controller->Property->PropertyAction->getMerge($summary, $actionID);
				}

				if($typeID){
					$summary = $this->controller->Property->PropertyType->getMerge($summary, $typeID);
				}

				$summaries[$key] = $summary;
			}
		*/

			$result['data'] = $summaries;
		}

		return $result;
	}

	public function getCompanyPropertyType($options = array()){
		$options = (array) $options;
		$results = array();

		$useDefault		= Hash::get($options, 'use_default', true);
		$companyData	= Configure::read('Config.Company.data');
		$typeSlug		= Hash::get($companyData, 'UserCompanyConfig.mt_property_type');
		$typeSlug		= $typeSlug ? json_decode($typeSlug, true) : array();

		if(empty($typeSlug) && $useDefault){
			$typeSlug = Configure::read('Config.MarketTrend.default_property_type');
		}

		if($typeSlug){
			$results = $this->controller->User->Property->PropertyType->getData('all', array(
				'conditions'	=> array('PropertyType.slug' => $typeSlug), 
				'order'			=> array(
					sprintf('FIELD(PropertyType.slug, "%s")', implode('", "', $typeSlug)), 
				), 
			));
		}

		return $results;
	}

	public function getChartFormat($period = null){
		$format = array();

		if($period){
			if($period == 1){
				$format = array(
					'col_count'		=> 30, 
					'date_unit'		=> 'day',
					'date_format'	=> 'D M', 
				);
			}
			else if($period > 1 && $period <= 12){
			//	MONTHLY
				$format = array(
					'col_count'		=> $period, 
					'date_unit'		=> 'month',
					'date_format'	=> 'M Y', 
				);
			}
			else{
			//	YEARLY
				$format = array(
					'col_count'		=> ceil($period / 12), 
					'date_unit'		=> 'year',
					'date_format'	=> 'Y', 
				);
			}
		}

		return $format;
	}

	public function getMovement($newSummary = array(), $oldSummary = array()){
		$newChart	= Hash::get($newSummary, 'chart', array());
		$oldChart	= Hash::get($oldSummary, 'chart', array());
		$results	= array();

		if($newChart && $oldChart){
			$columns = Hash::get($newChart, 0, array());
			$columns = Hash::remove($columns, 0);

			$newChart = Hash::remove($newChart, 0);
			$oldChart = Hash::remove($oldChart, 0);

		//	chart pergerakan harga
			$movementChart = array();

			if($newChart){
				$currency = Configure::read('__Site.config_currency_symbol');

				foreach($columns as $colTypeID => $colText){
					$lastValue		= Hash::extract($oldChart, sprintf('{n}.%s', $colTypeID));
					$lastValue		= array_filter($lastValue);
					$lastValue		= $lastValue ? array_pop($lastValue) : 0;
					$lastPercentage	= 0;

					foreach($newChart as $rowIndex => $row){
						$currentMonth = Hash::get($row, 0);
						$currentValue = Hash::get($row, $colTypeID);

						$currentValue		= $currentValue > 0 ? $currentValue : $lastValue;
						$currentValueFormat	= sprintf('%s', number_format($currentValue, 2, ',', '.'));
						$lastValueFormat	= sprintf('%s', number_format($lastValue, 2, ',', '.'));
						$percentage			= 0;

					//	kata mas bebun kek gini logic nya
						if(empty($lastValue) && $currentValue || empty($currentValue) || $lastValue == $currentValue){
						//	STAGNAN
							$arrowIcon	= '';
							$arrowClass	= '';
						}
						else if($lastValue && $currentValue){
							$percentage			= abs($currentValue - $lastValue) / ($lastValue / 100);
							$percentageFormat	= sprintf('%s', number_format($percentage, 2, ',', '.'));

						//	increment / decrement
							$percentage	= $lastValue > $currentValue ? $percentage * -1 : $percentage;
							$arrowIcon	= $lastValue > $currentValue ? '{*-*}' : '{*+*}';
							$arrowClass	= $lastValue > $currentValue ? 'red' : 'green';
						}

					/*
					//	kalo persentase bulan ini kosong (bukan increment / decrement) ikut ke bulan sebelumnya supaya di chart datar
						$percentage = $percentage != 0 ? $percentage : $lastPercentage;

						$movementChart[$rowIndex][0]			= $currentMonth;
						$movementChart[$rowIndex][$colTypeID]	= $percentage;
					*/

					//	format langsung disini jadi di google chart ga perlu modif lagi
						$decimal = intval($percentage) && abs($percentage) - intval(abs($percentage)) > 0.01 ? 2 : 0;
						$caption = trim(sprintf('%s %s%%', $arrowIcon, number_format($percentage, $decimal)));
						$caption = sprintf('<span class="text %s">%s</span>', $arrowClass, $caption);
						$caption = sprintf('%s%s (%s)', $currency, number_format($currentValue), $caption);

						$movementChart[$rowIndex][0]			= $currentMonth;
						$movementChart[$rowIndex][$colTypeID]	= array(
							'v' => $lastPercentage + $percentage, 
							'f'	=> $caption, 
						);

						$percentage		= $lastPercentage + $percentage;
						$percentage		= $percentage != 0 ? $percentage : $lastPercentage;

						$lastPercentage	= $percentage;
						$lastValue		= $currentValue;
					}
				}
			}

			$movementChart = array_merge(array(array(0 => __('Tanggal')) + $columns), $movementChart);

		/*
			if($columns){
				$rows = array_merge($oldChart, $newChart);

				foreach($columns as $colTypeID => $colText){
					$lastValue = null;

					foreach($rows as $rowIndex => $row){
						$currentMonth = Hash::get($row, 0);
						$currentValue = Hash::get($row, $colTypeID);

						if($lastValue !== null){
							$currentValue		= $currentValue > 0 ? $currentValue : $lastValue;
							$currentValueFormat	= sprintf('%s', number_format($currentValue, 2, ',', '.'));
							$lastValueFormat	= sprintf('%s', number_format($lastValue, 2, ',', '.'));
							$percentage			= false;

						//	kata mas bebun kek gini logic nya
							if(empty($lastValue) && $currentValue || empty($currentValue) || $lastValue == $currentValue){
							//	STAGNAN
								$currentMessage = __('tidak mengalami perubahan');
							}
							else if($lastValue && $currentValue){
								$percentage			= abs($currentValue - $lastValue) / ($lastValue / 100);
								$percentageFormat	= sprintf('%s', number_format($percentage, 2, ',', '.'));

								if($lastValue < $currentValue){
								//	INCREMENT
									$currentMessage = 'kenaikan';
								}
								else{
								//	DECREMENT
									$currentMessage	= 'penurunan';
									$percentage		= $percentage * -1;
								}

							//	if(floatval($percentage) != 0){
							//		$results[$colTypeID]['movement'][] = $percentage;
							//	}

								$currentMessage = __('mengalami %s %s%%', $currentMessage, $percentageFormat);
							}

						//	$additional = __('from: %s to : %s',$lastValueFormat, $currentValueFormat);
							$additional = false;

							$results[$colTypeID]['type_id']		= $colTypeID;
							$results[$colTypeID]['type_name']	= $colText;
							$results[$colTypeID]['text'][]		= __('Pada %s %s %s', $currentMonth, $currentMessage, $additional);
							$results[$colTypeID]['movement'][]	= $percentage;
						}

						$lastValue = $currentValue;
					}

				//	rata2
					$movement = Hash::get($results, sprintf('%s.movement', $colTypeID), 0);
					$movement = array_filter($movement);

					if($movement){
						$movementCount = count($movement);
						$movementValue = array_sum($movement);

						$movement = number_format($movementValue / $movementCount, 2, '.', '');
					}

					$results[$colTypeID]['avg_movement'] = $movement;
				}
			}
		*/
		}

		$results = array(
			'chart'				=> $movementChart, 
		//	'movement_detail'	=> $results, 
		);

		return $results;
	}

	public function getPropertyFilter(){
		$this->controller->loadModel('PropertyFilter');

		$options = array('contain' => array('PropertyFilterDetail'), 'limit' => 5);
		$filters = $this->controller->PropertyFilter->getData('all', $options, array(
			'mine'			=> true, 
			'use_default'	=> true, 
		));

		$filters = $this->controller->PropertyFilter->PropertyFilterDetail->getMerge($filters);
		return $filters;
	}

	public function parsePropertyFilter($filters = array()){
		$filters = (array) $filters;

		if($filters){
			foreach($filters as $filterKey => $filter){
				$filterID = Hash::get($filter, 'PropertyFilter.id'); 

				if($filterID){
					$filterDetails	= Hash::get($filter, 'PropertyFilterDetail', array());
					$filterParams	= Hash::combine($filterDetails, '{n}.PropertyFilterDetail.field', array(
						'%s%s', 
						'{n}.PropertyFilterDetail.operand', 
						'{n}.PropertyFilterDetail.value',  
					));

				//	append parsed data
					$filters = Hash::insert($filters, sprintf('%s.PropertyFilter.filter_param', $filterKey), $filterParams); 
				}
			}
		}

		return $filters;
	}
}
?>