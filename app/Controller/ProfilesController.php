<?php
App::uses('AppController', 'Controller');

class ProfilesController extends AppController {
	public $uses	= array('Property');
	public $helpers	= array(
	//	'FileUpload.UploadForm',
		'Property',
		'Paginator',
	);

	public $components = array(
		'RmCrm', 
		'RmPage', 
		'RmImage',
		'RmProperty', 
		'RmEbroschure', 
		'RmMarketTrend', 
		'Captcha', 
	);

	public function beforeFilter(){
		parent::beforeFilter();

		$authGroupID = $this->Auth->user('group_id');

		if(empty($authGroupID) || in_array($authGroupID, array(1, 2))){
			$allowedMethods	= array(
				'find', 
				'search', 
				'index', 
				'property_find', 
				'property_detail', 
				'market_trend', 
				'kpr_calculator', 
				'detail', 
				'contact', 
			);

			if($authGroupID){
				$allowedMethods = array_merge($allowedMethods, array(
					'admin_property_transfer', 
				));

				$this->set('active_menu', 'personal_page');
			}

			$this->Auth->allow($allowedMethods);

			$companyData = Configure::read('Config.Company.data');
			$og_meta = array(
				'title' => Common::hashEmptyField($companyData, 'User.full_name'),
				'image' => Common::hashEmptyField($companyData, 'User.photo'),
				'path' => Configure::read('__Site.profile_photo_folder'),
				'size' => 'pxl',
			);

			$this->set(array(
				'og_meta' => $og_meta,
			));
		}
		else{
			$dashboardUrl = Configure::read('User.dashboard_url');
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman tersebut'), 'error', $dashboardUrl);
		}
	}

	public function admin_search($action = 'index', $_admin = true, $addParam = false){
		$data	= $this->request->data;
		$params	= array(
			'admin'		=> $_admin,
			'action'	=> $action,
			$addParam,
		);

		$this->RmCommon->processSorting($params, $data);
	}

	public function search($action = 'index', $addParam = false){
		$this->admin_search($action, $addParam);
	}

	public function index(){
		$this->RmPage->callBeforeViewHomepage();

	/*
		$this->loadModel('BannerSlide');
		$this->loadModel('BannerDeveloper');
		$this->loadModel('ApiAdvanceDeveloper');

		$propertyLimit	= Configure::read('__Site.config_limit_listing_home');
		$parentID		= Configure::read('Principle.id');
		$companyData	= Configure::read('Config.Company.data');
		$userID			= Common::hashEmptyField($companyData, 'User.id', 0);
		$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $userGroupID);
		$companyID		= 0;

		$cacheAppend = sprintf('personal.%s', $userID);

		if(empty($isIndependent) && $parentID){
			$companyID = $this->User->UserCompany->field('UserCompany.id', array(
				'UserCompany.user_id' => $parentID, 
			));
		}

		$properties = array();

		if($propertyLimit){
		//	cache setting
			$cacheConfig	= 'properties_home';
			$cacheData		= $this->RmCommon->getQueryCache($cacheConfig, array('append' => $cacheAppend));
		//	$properties		= Common::hashEmptyField($cacheData, 'record', array());
			$properties		= array();

			$userID			= Common::hashEmptyField($companyData, 'User.id');
			$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
			$userFullName	= Common::hashEmptyField($companyData, 'User.full_name');

			if(empty($this->params->query) && $properties){
			//	do nothing
			}
			else{
				$properties = $this->Property->getData('all', array(
					'limit'			=> $propertyLimit,
					'conditions'	=> array(
						'Property.user_id'					=> $userID, 
						'COALESCE(Property.company_id, 0)'	=> $companyID, 
					), 
				), array(
					'status'		=> 'active-pending-sold',
					'company'		=> empty($isIndependent),
					'skip_is_sales'	=> true,
				));

				$properties = $this->Property->getDataList($properties, array(
					'contain' => array(
						'MergeDefault',
						'PropertyAddress',
						'PropertyAsset',
						'PropertySold',
						'PropertyMediasCount',
						'PropertyStatusListing',
						'User',
					),
				));

				$properties = $this->Property->mergeMedia($properties);

			//	if(empty($this->params->query)){
			//		$this->RmCommon->setQueryCache($cacheConfig, $properties, array(
			//			'name' => Common::hashEmptyField($cacheData, 'cache.name'), 
			//		));
			//	}
			}
		}

		$populars = $this->Property->populers(12, array(), $this->params->query);
		$populars = $this->Property->mergeMedia($populars);

	//	get banner list
		$banners = $this->BannerSlide->getData('all', array(
			'cache'			=> sprintf('BannerSlide.HomePage.%s.%s', $companyID, $cacheAppend),
			'cacheConfig'	=> 'default',
			'limit'			=> 1, // dipaksa 1 aja katanya
			'order'			=> array(
				'BannerSlide.start_date'	=> 'asc', 
				'BannerSlide.id'			=> 'asc', 
			), 
		), array(
			'status' => 'active', 
		));

	//	$developers = $this->ApiAdvanceDeveloper->getData('all', array(
	//		'limit' => 9,
	//		'cache' => __('ApiAdvanceDeveloper.HomePage.%s', $companyID),
	//		'cacheConfig' => 'default',
	//	), array(
	//		'company' => true,
	//		'status' => 'active',
	//	));

	//	$partnerships = $this->User->Partnership->getData('all', array(
	//		'limit' => 30,
	//		'cache' => __('Partnership.HomePage.%s', $companyID),
	//		'cacheConfig' => 'default',
	//	));

	//	khusus easyliving
	//	$propertyTypes = $this->Property->PropertyType->getData('list', array(
	//		'cache' => __('PropertyType.List'),
	//	));

	//	$propertyActions = $this->Property->PropertyAction->getData('list', array(
	//		'cache' => __('PropertyAction.List'),
	//	));

		$propertyTypeShortcuts = $this->Property->PropertyType->getData('all', array(
			'conditions' => array(
				'PropertyType.id' => array(1, 2, 3, 7), 
			),
		));

		$categoryStatus = $this->RmCommon->getGlobalVariable('category_status');

		$this->RmCommon->_layout_file(array('map', 'map-cozy'));

	//	set tag title
		$domain					= Configure::read('__Site.domain');
		$title_for_layout		= __('%s - Agent Personal Page', $userFullName);
		$description_for_layout	= __('%s memudahkan Anda untuk mencari rumah dijual, sewa, atau mencari Rumah Baru. Cari poperti yang sesuai dengan kebutuhan Anda disini.', $domain);

		$this->set('title_for_layout', $title_for_layout);
		$this->set('description_for_layout', $description_for_layout);
		$this->set('_breadcrumb', false);
		$this->set('active_menu', 'home');

		$this->set(compact(
			'banners', 'properties', 'developers',
			'advices', 'partnerships', 'populars',
			'agents', 'propertyTypes', 'propertyActions', 
			'categoryStatus', 'propertyTypeShortcuts'
		));

		$this->RmCommon->getDataRefineProperty();
	*/
	}

	public function property_find(){
		$this->set('active_menu', 'property');

		$namedParams	= $this->params->named;
		$actionSlug		= $this->params->property_action;
		$actionID		= Common::hashEmptyField($namedParams, 'property_action');
		$currentPage	= Common::hashEmptyField($namedParams, 'page', 1);
		$displayShow	= Common::hashEmptyField($namedParams, 'show', 'grid');

		if($actionSlug && is_numeric($actionSlug) === false){
			$actionID = $actionSlug == 'disewakan' ? 2 : 1;

			$this->request->params['named']['property_action'] = $actionID;
		}

		$propertyAction = $this->Property->PropertyAction->getMerge(array(), $actionID, 'PropertyAction.id', array(
			'cache' => array(
				'name' => __('PropertyAction.%s', $actionID),
			),
		));

		if($propertyAction){
			$companyData	= Configure::read('Config.Company.data');
			$userID			= Common::hashEmptyField($companyData, 'User.id');
			$cacheAppend	= sprintf('personal.%s.%s.%s', $userID, $actionID, $currentPage);

			$cacheConfig	= 'properties_find';
			$cacheData		= $this->RmCommon->getQueryCache($cacheConfig, array('append' => $cacheAppend));
		//	$records		= Common::hashEmptyField($cacheData, 'record', array());
			$records		= array();

			$nonFilterParams	= array('page', 'property_action', 'show');
			$filterParams		= array_diff(array_keys($namedParams), $nonFilterParams);

			if(empty($filterParams) && $records){
				$this->request->params['named']['show']	= $displayShow;
				$this->request->params['pass']			= array();
			}
			else{
				$this->RmCommon->_callRefineParams($this->params);

				$parentID		= Configure::read('Principle.id');
				$propertyLimit	= Configure::read('__Site.config_limit_listing_home');
				$companyData	= Configure::read('Config.Company.data');
				$userID			= Common::hashEmptyField($companyData, 'User.id');
				$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
				$isIndependent	= Common::validateRole('independent_agent', $userGroupID);
				$companyID		= 0;

				if(empty($isIndependent) && $parentID){
					$companyID = $this->User->UserCompany->field('UserCompany.id', array(
						'UserCompany.user_id' => $parentID, 
					));
				}

				$options = $this->Property->_callRefineParams($this->params, array(
					'limit'			=> 12, 
					'conditions'	=> array(
						'Property.user_id'					=> $userID, 
						'COALESCE(Property.company_id, 0)'	=> $companyID, 
					), 
				));

				$this->paginate	= $this->Property->getData('paginate', $options, array(
					'status'		=> 'active-pending-sold',
					'company'		=> empty($isIndependent),
					'skip_is_sales' => true,
				));

				$records = $this->paginate('Property');
				$records = $this->Property->getDataList($records, array(
					'contain' => array(
						'MergeDefault',
						'PropertyAddress',
						'PropertyAsset',
						'PropertySold',
						'User',
						'PropertyMediasCount',
						'PropertyStatusListing',
					),
				));

			//	if(empty($filterParams)){
			//		$this->RmCommon->setQueryCache($cacheConfig, $records, array(
			//			'name' => Common::hashEmptyField($cacheData, 'cache.name'), 
			//		));
			//	}
			}

			$this->RmCommon->_callRequestSubarea('Search');
			$this->RmCommon->getDataRefineProperty();

			$domain			= Configure::read('__Site.domain');
			$actionName		= Common::hashEmptyField($propertyAction, 'PropertyAction.name');

			$module_title	= __('Properti %s', $actionName);
			$module_page	= '';

			if($currentPage > 1){
				$module_page = __(' Page %s', $currentPage);
			}

			$title_for_layout		= __('%s%s - %s', $module_title, $module_page, $domain);
			$keywords_for_layout	= __('%s murah di %s', $module_title, $domain);
			$description_for_layout	= __('Cari %s di %s dengan harga properti terjangkau!', $module_title, $domain);

		//	$mt_propertyTypes	= $this->RmMarketTrend->getCompanyPropertyType();
		//	$mt_location		= $this->RmUser->getLocation($this->params->named, array(
		//		'use_default'	=> true, 
		//		'market_trend'	=> true, 
		//	));

			$categoryStatus		= $this->RmCommon->getGlobalVariable('category_status');
		//	$propertyCategories	= $this->Property->PropertyCategory->getData('all');

			$this->set('flag_menu', 'properties');
			$this->set(compact(
				'records', 
				'displayShow', 
				'module_title',
				'title_for_layout', 
				'keywords_for_layout', 
				'description_for_layout',
				'mt_location', 
				'mt_propertyTypes', 
				'propertyAction', 
				'categoryStatus'
			));

			$this->RmCommon->getDataRefineProperty();
		}
		else{
			$queryParams = $this->params->query;
			$redirectURL = array_merge(array(
				'admin'				=> false,
				'controller'		=> 'profiles',
				'action'			=> 'property_find',
				'property_action'	=> 'dijual',
			), $namedParams);

			if($queryParams){
				$redirectURL = array_merge($redirectURL, array('?' => $queryParams));
			}

			$this->redirect($redirectURL, array(
				'status' => 301, 
			));
		}
	}

	public function property_detail(){
		$this->set('active_menu', 'property');

		$mlsID	= Common::hashEmptyField($this->params->named, 'mlsid');
		$slug	= Common::hashEmptyField($this->params->named, 'slug');

		if($mlsID){
			$cacheAppend	= $mlsID;
			$cacheConfig	= 'properties_detail';
			$cacheData		= $this->RmCommon->getQueryCache($cacheConfig, array('append' => $cacheAppend));
			$record			= Common::hashEmptyField($cacheData, 'record', array());

			if(empty($record)){
				$record = $this->Property->getData('first', array(
					'conditions' => array(
						'Property.mls_id' => $mlsID,
					),
				), array(
					'status'		=> 'active-pending-sold',
					'restrict_type'	=> 'mine',
					'company'		=> false,
					'skip_is_sales'	=> true,
				));

				if($record){
					$record = $this->Property->getDataList($record, array(
						'contain' => array(
							'MergeDefault',
							'PropertyAddress',
							'PropertyAsset',
							'PropertySold',
							'PropertyFacility',
							'PropertyPointPlus',
							'PropertyStatusListing',
							'PropertyPrice',
							'User',
						),
					));
				}

				$this->RmCommon->setQueryCache($cacheConfig, $record, array(
					'name' => Common::hashEmptyField($cacheData, 'cache.name'), 
				));
			}

			if($record){
				$label = $this->RmProperty->getNameCustom($record);
				$label = $this->RmCommon->toSlug($label);

				if($label == $slug){
					$recordID		= Common::hashEmptyField($record, 'Property.id');
					$userID			= Common::hashEmptyField($record, 'Property.user_id');
					$title			= Common::hashEmptyField($record, 'Property.title');
					$photo			= Common::hashEmptyField($record, 'Property.photo');
					$description	= Common::hashEmptyField($record, 'Property.description');
					$active			= Common::hashEmptyField($record, 'Property.active');
					$periodID		= Common::hashEmptyField($record, 'PropertySold.period_id');

					$record = $this->Property->PageConfig->getMerge($record, $recordID);
					$record = $this->User->UserProfile->getMerge($record, $userID, true);
					$record = $this->User->UserConfig->getMerge($record, $userID);

					$propertySold = Common::hashEmptyField($record, 'PropertySold');

					if($propertySold){
						$propertySold	= $this->Property->Period->getMerge($propertySold, $periodID);
						$record			= Hash::insert($record, 'PropertySold', $propertySold);
					}

				//	append media
					$statusMedia = $active ? 'active' : 'all';

					$propertyMedias = $this->Property->PropertyMedias->getMerge(array(), $recordID, 'all', $statusMedia);
					$propertyVideos = $this->Property->PropertyVideos->getMerge(array(), $recordID, 'all', $statusMedia);

					if($propertyVideos){
						$videos = Common::hashEmptyField($propertyVideos, 'PropertyVideos', array());
						foreach($videos as $key => $video){
							$youtubeID		= Common::hashEmptyField($video, 'PropertyVideos.youtube_id');
							$videoThumbnail	= '';

							if($youtubeID){
								$videoDetail	= $this->RmCommon->getYoutubeDetail($youtubeID);
								$videoThumbnail	= Common::hashEmptyField($videoDetail, 'thumbnail_url');
							}

							$arrayPath		= sprintf('PropertyVideos.%s.PropertyVideos.thumbnail_url', $key);
							$propertyVideos = Hash::insert($propertyVideos, $arrayPath, $videoThumbnail);
						}
					}

					$record = array_merge($record, $propertyMedias, $propertyVideos);

					$exclusiveBank = $this->User->Kpr->KprBank->Bank->getExclusive();
				//	$exclusiveBank = $this->User->Kpr->KprBank->Bank->getData('first', array(
				//		'conditions'	=> array('Bank.is_exclusive' => 1), 
				//		'contain'		=> array(
				//			'BankSetting' => array(
				//				'conditions'	=> array(
				//					'BankSetting.type' => 'default', 
				//				), 
				//			), 
				//		), 
				//	));

				//	Proses Contact
					if($this->request->data){
					//	untuk agent yang masih terikat company cc juga ke admin company
						$recipients = array();

						if(empty($isIndependent)){
							$recipients = $this->User->getListAdmin(true, true);
						}

					//	proses contact
						$recipients	= array_merge($recipients, array($userID));
						$data		= $this->RmUser->_callMessageBeforeSave($recipients, $recordID);
						$result		= $this->User->Message->doSendMany($data, $recipients);

						$this->RmCommon->setProcessParams($result, false, array(
							'noRedirect' => true, 
						));

						$status = Common::hashEmptyField($result, 'status');

						if($status == 'success'){
							$this->request->data = array();
						}
					}

				//	$agents		= $this->User->populers();
					$dataView	= $this->RmCommon->_callSaveVisitor($recordID, 'PropertyView');
					$neighbours	= $this->Property->getNeighbours($record);

					$this->Property->PropertyView->doSave($dataView);
					$this->RmCommon->_callRequestSubarea('Search');
					$this->RmCommon->getDataRefineProperty();

					$baseURL	= Configure::read('__Site.domain');
					$savePath	= Configure::read('__Site.property_photo_folder');
					$og_meta	= array(
						'title'			=> $title,
						'image'			=> $photo,
						'path'			=> $savePath,
						'description'	=> $description,
						'size'			=> 'company', 
					);

					$this->RmCommon->_layout_file(array(
						'map', 
						'map-cozy', 
						'bank', 
					));

					$metaTitle			= Common::hashEmptyField($record, 'PageConfig.meta_title');
					$metaKeyword		= Common::hashEmptyField($record, 'PageConfig.meta_keyword');
					$metaDescription	= Common::hashEmptyField($record, 'PageConfig.meta_description');

					$actionID		= Common::hashEmptyField($record, 'PropertyAction.id');
					$actionName		= Common::hashEmptyField($record, 'PropertyAction.name');
					$typeName		= Common::hashEmptyField($record, 'PropertyType.name');
					$regionName		= Common::hashEmptyField($record, 'PropertyAddress.Region.name');
					$cityName		= Common::hashEmptyField($record, 'PropertyAddress.City.name');
					$subareaName	= Common::hashEmptyField($record, 'PropertyAddress.Subarea.name');
					$zip			= Common::hashEmptyField($record, 'PropertyAddress.zip');

					if(empty($metaTitle)){
						$metaTitle = __('%s %s %s, %s %s - %s', $typeName, $actionName, $subareaName, $cityName, $mlsID, $baseURL);
					}

					if(empty($metaKeyword)){
						$metaKeyword = __('%s %s %s, %s %s %s di %s', $typeName, $actionName, $subareaName, $cityName, $zip, $mlsID, $baseURL);
					}

					if(empty($metaDescription)){
						if($actionID == 2){
							$price = $this->RmProperty->_callRentPrice($record, false, false, false);
						}
						else{
							$price = $this->RmProperty->getPrice($record, false, false, false);
						}

						$metaDescription = __('%s %s %s, %s %s %s %s di %s dengan harga properti terjangkau!', $typeName, $actionName, $subareaName, $cityName, $zip, $mlsID, $price, $baseURL);
					}

					$this->set(array(
						'title_for_layout'			=> $metaTitle, 
						'keywords_for_layout'		=> $metaKeyword, 
						'description_for_layout'	=> $metaDescription, 
						'_canonical'				=> true, 
						'captcha_code'				=> $this->Captcha->generateEquation(), 
					));

					$this->set(compact('record', 'og_meta', 'neighbours', 'exclusiveBank'));

				//	untuk theme the nest atau tema lain yang bisa liat detail via ajax modal ========================

					$isModal	= Common::hashEmptyField($this->params->named, 'modal');
					$isAjax		= $this->RequestHandler->isAjax();

					if($isAjax && $isModal){
						$this->layout = false;
						$this->render('property_detail_modal');
					}

				//	=================================================================================================
				}
				else{
					$this->redirect(array(
						'admin'			=> false,
						'controller'	=> 'profiles',
						'action'		=> 'property_detail',
						'mlsid'			=> $mlsID,
						'slug'			=> $label,
					), array(
						'status' => 301, 
					));
				}
			}
			else{
				$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
			}
		}
		else{
			$this->RmCommon->redirectReferer(__('Properti tidak ditemukan'), 'error');
		}
	}

	public function market_trend(){
		$this->set('active_menu', 'property');
	}

	public function kpr_calculator(){
		$this->set('active_menu', 'kpr');

		$mlsID = Common::hashEmptyField($this->params->named, 'mlsid');

		$exclusiveBank = $this->User->Kpr->KprBank->Bank->getExclusive();
	//	$exclusiveBank = $this->User->Kpr->KprBank->Bank->getData('first', array(
	//		'conditions'	=> array('Bank.is_exclusive' => 1), 
	//		'contain'		=> array(
	//			'BankSetting' => array(
	//				'conditions'	=> array(
	//					'BankSetting.type' => 'default', 
	//				), 
	//			), 
	//		), 
	//	));

		if($exclusiveBank){
			if($mlsID){
				$record = $this->Property->getData('first', array(
					'conditions' => array(
						'Property.mls_id' => $mlsID,
					),
				), array(
					'status'		=> 'active-pending-sold',
					'restrict_type'	=> 'mine',
					'company'		=> false,
					'skip_is_sales'	=> true,
				));

				$typeID			= Common::hashEmptyField($record, 'Property.property_type_id');
				$propertyPrice	= Common::hashEmptyField($record, 'Property.price_measure');
			}
			else{
				$typeID			= $this->Property->PropertyType->field('PropertyType.id', array('PropertyType.slug' => 'rumah'));
				$propertyPrice	= 200000000;

				$record	= array(
					'Property' => array(
						'property_type_id'	=> $typeID, 
						'price_measure'		=> $propertyPrice, 
					), 
				);
			}

			if($this->data){
				$this->request->data = $this->RmCommon->dataConverter($this->data, array(
					'price' => array(
						'Kpr' => array(
							'property_price',
							'down_payment',
						),
					)
				));

				$propertyPrice		= Common::hashEmptyField($this->data, 'Kpr.property_price');
				$dpPercentage		= Common::hashEmptyField($this->data, 'Kpr.dp');
				$periodeInstallment	= Common::hashEmptyField($this->data, 'Kpr.periode_installment');

				$record			= Hash::insert($record, 'Property.price_measure', $propertyPrice);
				$exclusiveBank	= Hash::insert($exclusiveBank, 'BankSetting.dp', $dpPercentage);
				$exclusiveBank	= Hash::insert($exclusiveBank, 'BankSetting.periode_installment', $periodeInstallment);
			}

		//	komisi
			$commissionSetting = $this->User->Kpr->KprBank->Bank->BankCommissionSetting->getKomisi(array($exclusiveBank), array(
				'property_type_id'	=> $typeID,
				'price'				=> $propertyPrice,
			//	'region_id'			=> $region_id,
			//	'city_id'			=> $city_id,
				'data'				=> $record,
			));

			$commissionSetting	= Common::hashEmptyField($commissionSetting, 'qualify.0.BankCommissionSetting', array());
			$exclusiveBank		= Hash::insert($exclusiveBank, 'BankCommissionSetting', $commissionSetting);

			$baseURL	= Configure::read('__Site.domain');
			$savePath	= Configure::read('__Site.logo_photo_folder');
			$userLogo	= Configure::read('Config.Company.data.UserConfig.logo');

			$metaTitle			= Common::hashEmptyField($record, 'PageConfig.meta_title');
			$metaKeyword		= Common::hashEmptyField($record, 'PageConfig.meta_keyword');
			$metaDescription	= Common::hashEmptyField($record, 'PageConfig.meta_description');

			if(empty($metaTitle)){
				$metaTitle = __('Kalkulator KPR - %s', $baseURL);
			}

			if(empty($metaKeyword)){
				$metaKeyword = $metaTitle;
			}
	
			$og_meta = array(
				'image'			=> $userLogo,
				'path'			=> $savePath,
				'title'			=> $metaTitle,
				'description'	=> $metaDescription,
				'size'			=> 'company', 
			);

			$this->RmCommon->_layout_file(array('bank'));
			$this->set(array(
				'title_for_layout'			=> $metaTitle, 
				'keywords_for_layout'		=> $metaKeyword, 
				'description_for_layout'	=> $metaDescription, 
				'_canonical'				=> true, 
			));

			$this->set(compact('record', 'og_meta', 'exclusiveBank'));
		}
		else{
			$this->RmCommon->redirectReferer(__('Promo Bank tidak ditemukan'), 'error');
		}
	}

	public function detail(){
		$companyData	= Configure::read('Config.Company.data');
		$userID			= Common::hashEmptyField($companyData, 'User.id');

		$record = $this->User->getData('first', array(
			'conditions' => array('User.id' => $userID),
		), array(
			'role'		=> array('user', 'agent'),
			'status'	=> 'semi-active',
			'company'	=> false,
		));

		if($record){
			$record = $this->User->getMergeList($record, array(
				'contain' => array(
					'UserProfile',
					'UserConfig',
				),
			));

			$agentName = Common::hashEmptyField($record, 'User.full_name');

			$parentID		= Configure::read('Principle.id');
			$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
			$isIndependent	= Common::validateRole('independent_agent', $userGroupID);

			if($this->request->data){
			//	untuk agent yang masih terikat company cc jug ke admin ompany
				$recipients = array();

				if(empty($isIndependent)){
					$recipients = $this->User->getListAdmin(true, true);
				}

			//	proses contact
				$recipients	= array_merge($recipients, array($userID));
				$data		= $this->RmUser->_callMessageBeforeSave($recipients);
				$result		= $this->User->Message->doSendMany($data, $recipients);

				$this->RmCommon->setProcessParams($result, false, array(
					'noRedirect' => true, 
				));

				$status = Common::hashEmptyField($result, 'status');

				if($status == 'success'){
					$this->request->data = array();
				}
			}

			$companyID	= 0;
			$company	= array();

			if(empty($isIndependent) && $parentID){
				$company = $this->User->UserCompany->getData('first', array(
					'conditions' => array('UserCompany.user_id' => $parentID),
				));

				if($company){
					$companyID	= Common::hashEmptyField($company, 'UserCompany.id');
					$company = $this->User->getMerge($company, $parentID);
					$company = $this->User->UserCompanyConfig->getMerge($company, $parentID);
					$company = $this->User->UserCompanySetting->getMerge($company, $parentID);
				}
			}

		/*
			if(empty($isIndependent)){
				$parentID = Common::hashEmptyField($record, 'User.parent_id', $userID);

				$record	= $this->User->UserCompany->getMerge($record, $parentID);
				$record	= $this->User->UserCompanyConfig->getMerge($record, $parentID);
				$record	= $this->User->UserCompanySetting->getMerge($record, $parentID);
			}
		
			$propertyLimit = Configure::read('__Site.config_limit_listing_home');

			$properties = $this->Property->getData('count', array(
				'conditions' => array(
					'Property.user_id'					=> $userID, 
					'COALESCE(Property.company_id, 0)'	=> $companyID, 
				),
			), array(
				'status'		=> 'active-pending-sold',
				'company'		=> empty($isIndependent),
				'skip_is_sales'	=> true,
			));

			$properties = $this->paginate('Property');
			$properties = $this->Property->getDataList($properties, array(
				'contain' => array(
					'MergeDefault',
					'PropertyAddress',
					'PropertyAsset',
					'PropertySold',
					'PropertyStatusListing',
				),
			));
		*/

			$this->Property->virtualFields['count'] = 'COUNT(*)';

			$activeProperties = $this->Property->getData('all', array(
				'fields'		=> array('Property.property_action_id', 'Property.count'), 
				'group'			=> array('Property.property_action_id'), 
				'conditions'	=> array(
					'Property.user_id'					=> $userID, 
					'COALESCE(Property.company_id, 0)'	=> $companyID, 
				),
			), array(
				'status'		=> 'active',
				'company'		=> empty($isIndependent),
				'skip_is_sales'	=> true,
			));

			$soldProperties = $this->Property->getData('count', array(
				'conditions' => array(
					'Property.user_id'					=> $userID, 
					'COALESCE(Property.company_id, 0)'	=> $companyID, 
				),
			), array(
				'status'		=> 'sold',
				'company'		=> empty($isIndependent),
				'skip_is_sales'	=> true,
			));

			$propertyActions = $this->Property->PropertyAction->getData('all');

			$this->RmCommon->getDataRefineProperty();

		//	log view
			$dataView = $this->RmCommon->_callSaveVisitor($userID, 'UserView', 'profile_id');
			$this->User->UserView->doSave($dataView);

			$domain					= Configure::read('__Site.domain');
			$title_for_layout 		= sprintf('%s - Agen Properti - %s', $agentName, $domain);
			$keywords_for_layout	= sprintf('%s Agen Properti %s', $agentName, $domain);
			$description_for_layout = sprintf(__('%s Agen Properti Terbaik dan Terpercaya!'), $agentName, $domain);

			$this->set('module_title', 'Profil');
			$this->set('active_menu', 'profile');
			$this->set('title_for_layout', $title_for_layout);
			$this->set('keywords_for_layout', $keywords_for_layout);
			$this->set('description_for_layout', $description_for_layout);
			$this->set('captcha_code', $this->Captcha->generateEquation());
			$this->set(compact(
				'record', 
				'company', 
			//	'properties',
				'propertyActions', 
				'activeProperties', 
				'soldProperties'
			));
		}
		else{
			$this->RmCommon->redirectReferer(__('Agen tidak ditemukan'));
		}
	}

	public function contact(){
		$companyData	= Configure::read('Config.Company.data');
		$userID			= Common::hashEmptyField($companyData, 'User.id');
		$userGroupID	= Common::hashEmptyField($companyData, 'User.group_id');
		$isIndependent	= Common::validateRole('independent_agent', $userGroupID);

		$record = $this->User->getData('first', array(
			'conditions' => array('User.id' => $userID),
		), array(
			'role'		=> array('user', 'agent'),
			'status'	=> 'semi-active',
			'company'	=> false,
		));

		if($record){
			$record = $this->User->getMergeList($record, array(
				'contain' => array(
					'UserProfile',
					'UserConfig',
				),
			));

		//	untuk agent yang masih terikat company cc juga ke admin ompany
			$recipients = array();

			if(empty($isIndependent)){
				$recipients = $this->User->getListAdmin(true, true);
			}

		//	proses contact
			$recipients	= array_merge($recipients, array($userID));
			$data		= $this->RmUser->_callMessageBeforeSave($recipients);
			$result		= $this->User->Message->doSendMany($data, $recipients);
			$status		= Common::hashEmptyField($result, 'status');
			$message	= Common::hashEmptyField($result, 'msg');

			if($status == 'success' && $message){
				$result = Hash::insert($result, 'msg', __('Pesan Anda telah berhasil dikirim.'));
			}

		//	debug($result);exit;

			$this->RmCommon->setProcessParams($result, array(
				'admin'			=> false,
				'controller'	=> 'profiles',
				'action'		=> 'contact',
			));
			
	    //	$this->RmCommon->_layout_file(array('map', 'map-cozy'));

			$domain					= Configure::read('__Site.domain');
			$module_title			= __('Hubungi Saya');
			$title_for_layout 		= sprintf('%s - %s', $module_title, $domain);
			$description_for_layout	= sprintf(__('hubungi saya di %s Dapatkan properti pilhan Anda dengan cara menghubungi saya'), $domain);
			$keywords_for_layout	= sprintf('hubungi saya %s', $domain);
			$captcha_code			= $this->Captcha->generateEquation();

			$this->set('module_title', 'Kontak');
			$this->set('active_menu', 'contact');
			$this->set('title_for_layout', $title_for_layout);
			$this->set('keywords_for_layout', $keywords_for_layout);
			$this->set('description_for_layout', $description_for_layout);
			$this->set('captcha_code', $captcha_code);
			$this->set(compact('record'));
		}
		else{
			$this->RmCommon->redirectReferer(__('Agen tidak ditemukan'));
		}
	}

	public function admin_property_transfer(){
		$authGroupID	= Configure::read('User.group_id');
		$isCompanyAgent	= Common::validateRole('company_agent', $authGroupID);

		if($isCompanyAgent){
			if($this->data){
				$recordID	= Common::hashEmptyField($this->data, 'Property.id');
				$result		= $this->Property->doTransfer($recordID);

				$this->RmCommon->setProcessParams($result);
			}

			$options = array(
				'type_merge'	=> 'regular_merge', 
				'conditions'	=> array('COALESCE(Property.company_id, 0)' => 0), 
				'order'			=> array(
					'Property.created'		=> 'DESC',
					'Property.change_date'	=> 'DESC',
				),
			);

			$records = $this->RmProperty->_callBeforeViewProperties($options, array(
				'mine'		=> true, 
				'company'	=> false, 
			));

			$this->set(array(
				'module_title'		=> __('Daftar Transfer Properti'),
				'title_for_layout'	=> __('Daftar Transfer Properti'),
				'active_menu'		=> 'property_transfer', 
				'records'			=> $records,
			));

			$this->RmCommon->renderRest(array('is_paging' => true));
		}
		else{
			$dashboardUrl = Configure::read('User.dashboard_url');
			$this->RmCommon->redirectReferer(__('Anda tidak memiliki hak untuk mengakses halaman tersebut'), 'error', $dashboardUrl);
		}
	}
}