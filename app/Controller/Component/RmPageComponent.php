<?php
class RmPageComponent extends Component {
	var $components = array(
		'RmCommon',
	);

	/**
	*	@param object $controller - inisialisasi class controller
	*/
	function initialize(Controller $controller, $settings = array()) {
		$this->controller = $controller;
	}

	function _callRedirect301 () {
		$action = $this->controller->action;
		$params = $this->controller->params;
		$url = $params->url;
		$parameters = $params->params;

		$pass = $this->RmCommon->filterEmptyField($parameters, 'pass');
		$named = $this->RmCommon->filterEmptyField($parameters, 'named');
		$parameters = $this->RmCommon->_callUnset(array(
			'pass',
			'named',
		), $parameters);

		if( !empty($pass) ) {
			$parameters = array_merge($parameters, $pass);
		}
		if( !empty($named) ) {
			$parameters = array_merge($parameters, $named);
		}

		$actionRoute = Configure::read('Route.action');
		$actionRoutes = explode('|', $actionRoute);

		if( in_array($action, $actionRoutes) ) {
			$find = strpos($url, 'pages/');

			if( $find === 0 ) {
				$this->controller->redirect($parameters, array(
					'status' => 301,
				));
			}
		}
	}

	function limitDashboard($dataCompany = false){
		$params = $this->controller->params;
		// data DB OR theme
		$limit_top_agent = Common::hashEmptyField($dataCompany, 'UserCompanySetting.limit_top_agent');
		$limit_property_list = Common::hashEmptyField($dataCompany, 'UserCompanySetting.limit_property_list');
		$limit_property_popular = Common::hashEmptyField($dataCompany, 'UserCompanySetting.limit_property_popular');
		$limit_latest_news = Common::hashEmptyField($dataCompany, 'UserCompanySetting.limit_latest_news');

		//  GET
		$get_limit_top_agent = Common::hashEmptyField($params->query, 'limit_top_agent', $limit_top_agent);
		$get_limit_property_list = Common::hashEmptyField($params->query, 'limit_property_list', $limit_property_list);
		$get_limit_property_popular = Common::hashEmptyField($params->query, 'limit_property_popular', $limit_property_popular);
		$get_limit_latest_news = Common::hashEmptyField($params->query, 'limit_latest_news', $limit_latest_news);

		return array(
			'limit_top_agent' => $get_limit_top_agent,
			'limit_property_list' => $get_limit_property_list,
			'limit_property_popular' => $get_limit_property_popular,
			'limit_latest_news' => $get_limit_latest_news,
		);
	}

	public function callBeforeViewHomepage($type = null){
		$isPersonalPage	= Configure::read('Config.Company.is_personal_page');
		$projectSlug	= $this->RmCommon->_callProjectSlug();

		if($type && in_array($type, array('company', 'personal_page'))){
			$isPersonalPage = $type == 'personal_page';
		}

		if(empty($isPersonalPage) && $projectSlug == 'btn'){
			$this->controller->render('btntermandconditions');
		}
		else{
			$this->controller->loadModel('BannerSlide');
			$this->controller->loadModel('BannerDeveloper');

			$controller		= $this->controller->name;
			$action			= Inflector::camelize($this->controller->action);
			$queryParams	= $this->controller->params->query;

			$domain			= Configure::read('__Site.domain');
			$parentID		= Configure::read('Principle.id');
			$companyData	= Configure::read('Config.Company.data');
			$userID			= Common::hashEmptyField($companyData, 'User.id', 0);
			$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id', 0);
			$userFullName	= Common::hashEmptyField($companyData, 'User.full_name');
			$companyID		= Common::hashEmptyField($companyData, 'UserCompany.id', 0);
			$companyName	= Common::hashEmptyField($companyData, 'UserCompany.name');
			$isIndependent	= Common::validateRole('independent_agent', $userGroupID);

			$id_status_listing	= Common::hashEmptyField($companyData, 'UserCompanySetting.id_status_listing');

			// ** Ini utk apa? Sudah pasti dapat pada saat pengecekan domain
			// if(empty($isIndependent) && $parentID){
			// 	$companyID = $this->controller->User->UserCompany->field('UserCompany.id', array(
			// 		'UserCompany.user_id' => $parentID, 
			// 	));
			// }

			$cacheConfig	= 'properties_home';
			$cacheName		= sprintf('Properties.%s.%s.%s', $action, $companyID, $userID);
		//	$cacheData		= Cache::read($cacheName, $cacheConfig);
			$cacheData		= array();

			if($isPersonalPage){
			//	HOMEPAGE PERSONAL PAGE
				$propertyLimit			= Configure::read('__Site.config_limit_listing_home');
				$popularPropertyLimit	= 12;
				$latestNewsLimit		= 0;
				$topAgentLimit			= 0;
			}
			else{
			//	HOMEPAGE COMPANY
				$limitDashboard			= $this->limitDashboard($companyData);
				$propertyLimit			= Common::hashEmptyField($limitDashboard, 'limit_property_list', 0);
				$popularPropertyLimit	= Common::hashEmptyField($limitDashboard, 'limit_property_popular', 0);
				$latestNewsLimit		= Common::hashEmptyField($limitDashboard, 'limit_latest_news', 0);
				$topAgentLimit			= Common::hashEmptyField($limitDashboard, 'limit_top_agent', 0);
			}

			$properties = array();

			if($propertyLimit){
				if(empty($queryParams) && $cacheData){
				//	find all query, get results from cache (if exist)
					$this->controller->request->named	= $cacheData['named'];
					$this->controller->request->pass	= $cacheData['pass'];
					$this->controller->request->query	= $cacheData['query'];
					$properties							= $cacheData['result'];
				}
				else{
					if($isPersonalPage){
					//	PERSONAL PAGE PROPERTIES
						$propertiesConditions = array(
							'Property.user_id'					=> $userID, 
						);

						if( !empty($companyID) ) {
							$propertiesConditions['COALESCE(Property.company_id, 0)'] = $companyID;
						}

						$properties = $this->controller->User->Property->getData('all', array(
							'limit'			=> $propertyLimit,
							'conditions'	=> $propertiesConditions, 
						), array(
							'status'		=> 'active-pending-sold',
							'company'		=> false,
							'skip_is_sales'	=> true,
						));

						$properties = $this->controller->User->Property->getDataList($properties, array(
							'contain' => array(
								'MergeDefault',
								'PropertyAddress',
								'PropertyAsset',
								'PropertySold',
								// ** Gk perlu tampilin jml fotonya, bikin berat
								// 'PropertyMediasCount',
								'PropertyStatusListing',
								'User',
							),
						));

						$properties = $this->controller->User->Property->mergeMedia($properties);
					}
					else{
					//	COMPANY PROPERTIES
						$properties = $this->controller->User->Property->getData('all', array(
							'limit' => $propertyLimit,
						), array(
							'status'		=> 'active-pending-sold',
							'company'		=> true,
							'skip_is_sales'	=> true,
						));

						$properties = $this->controller->User->Property->getDataList($properties, array(
							'contain' => array(
								'MergeDefault',
								'PropertyAsset',
								'PropertyAddress',
								// ** Gk perlu tampilin jml fotonya, bikin berat
								// 'PropertyMediasCount',
								'PropertyStatusListing',
							),
						));

						$isDirector = $this->RmCommon->_callIsDirector();

						if($isDirector && $properties){
							foreach($properties as &$property){
								$property = $this->controller->User->getMergeList($property, array(
									'contain' => array(
										'UserCompanyConfig' => array(
											'primaryKey' => 'user_id',
											'foreignKey' => 'parent_id',
										),
									),
								));
							}
						}
					}

					if(empty($queryParams)){
					//	find all query, generate cache
						$cacheData = array(
							'named'		=> $this->controller->request->named, 
							'pass'		=> $this->controller->request->pass, 
							'query'		=> $this->controller->request->query, 
							'result'	=> $properties, 
						);

						Cache::write($cacheName, $cacheData, $cacheConfig);
					}
				}
			}

		//	predefined arrays
			$banners = $developers = $partnerships = $populars = $agents = $advices = $highlight = array();

			$bannerOptions = array(
				'cache'			=> sprintf('BannerSlide.HomePage.%s.%s', $companyID, $userID),
				'cacheConfig'	=> 'default',
			);

			if($isPersonalPage){
				$bannerOptions = array_merge($bannerOptions, array(
					'limit'	=> 1, // dipaksa 1 aja katanya untuk personal page
					'order'	=> array(
						'BannerSlide.start_date'	=> 'asc', 
						'BannerSlide.id'			=> 'asc', 
					), 
				));
			}

			$banners = $this->controller->BannerSlide->getData('all', $bannerOptions, array(
				'status' => 'active',
			));

			// s: get property highlight
			if(!empty($id_status_listing)){
				$cacheName		= sprintf('PropertyHighlight.HomePage.%s', $companyID);
				$cacheConfig	= 'default';

				if(empty($highlight) || $queryParams) {

					$limit_highlight = 5;
					if (!empty($propertyLimit)) {
						$limit_highlight = $propertyLimit;
					}

					$highlight = $this->controller->User->Property->getData('all', array(
						'conditions' => array(
							'Property.property_status_id' => $id_status_listing,
						),
						'limit' => $limit_highlight,
					), array(
						'status'		=> 'active-pending',
						'company'		=> true,
						'skip_is_sales'	=> true,
					));

					$highlight = $this->controller->User->Property->getDataList($highlight, array(
						'contain' => array(
							'MergeDefault',
							'PropertyAddress',
							'PropertyStatusListing',
							// 'PropertyAsset',
							// 'PropertyMediasCount',
						),
					));
					// debug($highlight);die();
					Cache::write($cacheName, $highlight, $cacheConfig);
				}
			}
			// e: get property highlight

			if($popularPropertyLimit){
	    		$populars = $this->controller->User->Property->populers($popularPropertyLimit, array(), $queryParams);
				$populars = $this->controller->User->Property->mergeMedia($populars);
        	}
        	
			$partnerships = $this->controller->User->Partnership->getData('all', array(
				'cache'			=> sprintf('Partnership.HomePage.%s', $companyID),
				'cacheConfig'	=> 'default',
				'limit'			=> 30,
			));

			if($topAgentLimit){
				$agents = $this->controller->User->populers($topAgentLimit, $queryParams);
			}

			if($latestNewsLimit){
				$cacheName		= sprintf('Advice.HomePage.%s.%s', $companyID, $userID);
				$cacheConfig	= 'default';
			//	$advices		= Cache::read($cacheName, $cacheConfig);

				if(empty($advices) || $queryParams){
					$this->controller->User->Advice->virtualFields['null_last'] = 'ISNULL(Advice.order)';

					$advices = $this->controller->User->Advice->getData('all', array(
						'limit'	=> $latestNewsLimit,
						'order'	=> array(
							'Advice.null_last'	=> 'ASC',
							'Advice.order'		=> 'ASC',
							'Advice.modified'	=> 'DESC',
							'Advice.id'			=> 'DESC'
						),
					));

					$advices = $this->controller->User->Advice->getDataList($advices);

					Cache::write($cacheName, $advices, $cacheConfig);
				}
			}

			$layoutTitle		= Common::hashEmptyField($companyData, 'UserCompanyConfig.meta_title', $companyName);
			$layoutDescription	= Common::hashEmptyField($companyData, 'UserCompanyConfig.meta_description');

			if(empty($layoutDescription)){
				$regionName			= Common::hashEmptyField($companyData, 'UserCompany.Region.name');
				$cityName			= Common::hashEmptyField($companyData, 'UserCompany.City.name');
				$subareaName		= Common::hashEmptyField($companyData, 'UserCompany.Subarea.name');
				$locationName		= array_filter(array(ucwords($regionName), ucwords($cityName), ucwords($subareaName)));
				$locationName		= implode(', ', $locationName);
				$layoutDescription	= __('%s adalah situs properti terlengkap di %s. Pasang iklan rumah dijual, sewa, atau mencari Rumah Baru disini!', $domain, $locationName);
			}

			$captchaCode = $this->controller->Captcha->generateEquation();

			$this->RmCommon->getDataRefineProperty();
			$this->RmCommon->_layout_file(array('map', 'map-cozy'));
			$this->controller->set(array(
				'_breadcrumb'				=> false, 
				'active_menu'				=> 'home', 
				'title_for_layout'			=> $layoutTitle, 
				'description_for_layout'	=> $layoutDescription, 
				'captcha_code'				=> $captchaCode, 
				'properties'				=> $properties, 
				'banners'					=> $banners, 
				'developers'				=> $developers, 
				'partnerships'				=> $partnerships, 
				'populars'					=> $populars, 
				'populers'					=> $populars, 
				'agents'					=> $agents, 
				'advices'					=> $advices,
				'highlight'					=> $highlight,
			));

			if($isPersonalPage){
				$this->controller->render('../Profiles/index');
			}
		}
	}

}
?>