<?php
class RmProjectComponent extends Component {
	var $components = array(
		'RmCommon', 'Rest.Rest'
	);

	/**
	*	@param object $controller - inisialisasi class controller
	*/
	function initialize(Controller $controller, $settings = array()) {
		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));

		$this->controller = $controller;
	}

	function _callSupportAdvancedSearchProject ( $return_value = false ) {
        $propertyTypes = $this->controller->ApiAdvanceDeveloper->PropertyType->getData('list', array(
            'cache' => __('PropertyType.List'),
        ));
        $regions = $this->controller->ApiAdvanceDeveloper->Region->getData('list', array(
            'cache' => 'Region.List',
        ));

        $data = $this->controller->request->data;
        $region = $this->RmCommon->filterEmptyField($data, 'Search', 'region');
        $city = $this->RmCommon->filterEmptyField($data, 'Search', 'city');

        if( !empty($region) ) {
            $cities = $this->controller->ApiAdvanceDeveloper->City->getData('list', array(
                'conditions' => array(
                    'City.region_id' => $region,
                ),
            ));

            if( !empty($city) ) {
                $subareas = $this->controller->ApiAdvanceDeveloper->Subarea->getSubareas('list', $region, $city);
            }
        }

        $this->controller->set(compact(
            'propertyTypes', 'regions',
            'cities', 'subareas'
        ));

        if( !empty($return_value) ) {
            
            return array(
                'propertyTypes' => $propertyTypes,
            );
        }
    }

	function _callFormatSaveForm( $data, $data_company_applicant, $data_request ) {
		$original_id = $this->RmCommon->filterEmptyField($data, 'ApiAdvanceDeveloper', 'original_id', null);
		$slug_project = $this->RmCommon->filterEmptyField($data, 'ApiAdvanceDeveloper', 'slug');
		$project_name = $this->RmCommon->filterEmptyField($data, 'ApiAdvanceDeveloper', 'name');
		$name = $this->RmCommon->filterEmptyField($data, 'User', 'full_name');
		$no_hp = $this->RmCommon->filterEmptyField($data, 'UserProfile', 'no_hp');
		$email = $this->RmCommon->filterEmptyField($data, 'User', 'email');

		$principle_id = $this->RmCommon->filterEmptyField($data_company_applicant, 'UserCompany', 'user_id', null);
		$company_name = $this->RmCommon->filterEmptyField($data_company_applicant, 'UserCompany', 'name');
		$company_logo = $this->RmCommon->filterEmptyField($data_company_applicant, 'UserCompany', 'logo');
		$company_address = $this->RmCommon->filterEmptyField($data_company_applicant, 'UserCompany', 'address');
		$region_id = $this->RmCommon->filterEmptyField($data_company_applicant, 'UserCompany', 'region_id', null);
		$city_id = $this->RmCommon->filterEmptyField($data_company_applicant, 'UserCompany', 'city_id', null);
		$subarea_id = $this->RmCommon->filterEmptyField($data_company_applicant, 'UserCompany', 'subarea_id', null);

		$company_domain = $this->RmCommon->filterEmptyField($data_company_applicant, 'UserCompanyConfig', 'domain');

		$description  = $this->RmCommon->filterEmptyField($data_request, 'ApiRequestDeveloper', 'description');
		// $is_banner 	  = $this->RmCommon->filterEmptyField($data_request, 'ApiRequestDeveloper', 'is_banner');
		// $is_sell_unit = $this->RmCommon->filterEmptyField($data_request, 'ApiRequestDeveloper', 'is_sell_unit');

		$dataFormat['ApiRequestDeveloper'] = array(
			'principle_id' => $principle_id,
			'api_advance_developer_id' => $original_id,
			'name' => $name,
    		'phone' => $no_hp,
    		'email' => $email,
    		'company_name' => $company_name,
    		'company_logo' => $company_logo,
    		'company_domain' => $company_domain,
    		'company_address' => $company_address,
    		'region_id' => $region_id,
    		'city_id' => $city_id,
    		'subarea_id' => $subarea_id,
    		'slug_project' => $slug_project,
    		'project_name' => $project_name,
    		'description' => $description,
    		// 'is_banner' => $is_banner,
    		// 'is_sell_unit' => $is_sell_unit,
		);

		return $dataFormat;
	}

	function _callBeforeViewRequest ( $options = array(), $elements = array() ) {
		$data_merge = array();
        $option_merge = array_merge(array(
            'limit' => Configure::read('__Site.config_new_table_pagination'),
            'order' => array(
                'ApiRequestDeveloper.modified' => 'DESC',
            ),
        ), $options);

        $options =  $this->controller->ApiRequestDeveloper->_callRefineParams($this->controller->params, $option_merge);

        $params['named'] = $this->RmCommon->filterEmptyField($this->controller->params, 'named');
        $params = $this->RmCommon->defaultSearch($params, array(
            'filter' => 'request_updated-desc',
        ));

        $elements = array_merge($this->RmCommon->_callRefineParams($params), $elements);
        $elements['status'] = $this->RmCommon->filterEmptyField($elements, 'status', false, 'all');

        $this->controller->paginate = $this->controller->ApiAdvanceDeveloper->getData('paginate', $options);
        $data_request = $this->controller->paginate('ApiAdvanceDeveloper');

        if(!empty($data_request)){
            foreach ($data_request as $key => $value) {
		        $data_request = $this->controller->ApiAdvanceDeveloper->getMergeById($value, true);

		        $data_merge[$key] = $data_request;
            }
        }

        return $data_merge;
    }

	function _callBeforeViewProjects ( $options = array(), $elements = array() ) {
        $option_merge = array_merge(array(
            'limit' => Configure::read('__Site.config_new_table_pagination'),
            'order' => array(
                'ApiAdvanceDeveloper.modified' => 'DESC',
            ),
        ), $options);

        $options =  $this->controller->ApiAdvanceDeveloper->_callRefineParams($this->controller->params, $option_merge);

        $params['named'] = $this->RmCommon->filterEmptyField($this->controller->params, 'named');
        $params = $this->RmCommon->defaultSearch($params, array(
            'filter' => 'project_updated-desc',
        ));

        $elements = array_merge($this->RmCommon->_callRefineParams($params), $elements);
        $elements['status'] = $this->RmCommon->filterEmptyField($elements, 'status', false, 'all');

        $this->controller->paginate = $this->controller->ApiAdvanceDeveloper->getData('paginate', $options);

        $data_projects = $this->controller->paginate('ApiAdvanceDeveloper');

        if(!empty($data_projects)){
            foreach ($data_projects as $key => $value) {
            	$id_project = $this->RmCommon->filterEmptyField($value, 'ApiAdvanceDeveloper', 'id');

            	$total_product = $this->controller->ApiAdvanceDeveloper->ApiAdvanceDeveloperProduct->getData('count', array(
            		'conditions' => array(
            			'ApiAdvanceDeveloperProduct.project_id' => $id_project,
            		)), array(
						'status' => 'active',
				));

				$total_unit = $this->controller->ApiAdvanceDeveloper->ApiAdvanceDeveloperProductUnit->getData('count', array(
            		'conditions' => array(
            			'ApiAdvanceDeveloperProductUnit.project_id' => $id_project,
            		)), array(
						'status' => 'active',
				));

                $contain = array(
                	'City',
                	'Region',
		            'PropertyType',
		            'ApiAdvanceDeveloperCompany',
		        );

		        $value = $this->controller->ApiAdvanceDeveloper->getMergeList($value, array(
		            'contain' => $contain,
		        ));

		        // merge if any request
				$value = $this->controller->ApiRequestDeveloper->getMergeRequest($value, true);

		        // merge region, city, parentCompany
				$value = $this->controller->ApiAdvanceDeveloper->getMerge($value, true);

				// total product
				$value['TotalProduct'] = $total_product;
				// total productUnit
				$value['TotalProductUnit'] = $total_unit;

				$data_projects[$key] = $value;
            }
        }

        $this->_callSupportAdvancedSearchProject();

        return $data_projects;
    }

    // format data to save project, developer, developer parent
	function _callFormatDataToSave($data){
    	if(!empty($data)){
	    	$result['Project'] = array(
	    		'user_id' => Null,
	    		'type_developer'   => 'project_primedev',
	    		'original_id'	   => Common::hashEmptyField($data, 'Project.id', null),
	    		'api_advance_company_id' => Common::hashEmptyField($data, 'Project.company_id', null),
	    		'property_type_id' => Common::hashEmptyField($data, 'Project.property_type_id', null),
	    		'slug' 			   => Common::hashEmptyField($data, 'Project.slug'),
	    		'name' 			   => Common::hashEmptyField($data, 'Project.name'),
	    		'promo' 		   => Common::hashEmptyField($data, 'Project.promo'),
	    		'logo' 			   => Common::hashEmptyField($data, 'Project.logo'),
	    		'cover_img_sync'   => Common::hashEmptyField($data, 'Project.cover_img_sync'),
	    		'description' 	   => Common::hashEmptyField($data, 'Project.description'),
	    		'region_id' 	   => Common::hashEmptyField($data, 'Project.region_id', null),
	    		'city_id' 		   => Common::hashEmptyField($data, 'Project.city_id', null),
	    		'subarea_id' 	   => Common::hashEmptyField($data, 'Project.subarea_id', null),
	    		'address' 		   => Common::hashEmptyField($data, 'Project.address'),
	    		'address2' 		   => Common::hashEmptyField($data, 'Project.address2'),
	    		'zip' 			   => Common::hashEmptyField($data, 'Project.zip'),
	    		'start_date' 	   => Common::hashEmptyField($data, 'Project.live_date_from'),
	    		'end_date' 		   => Common::hashEmptyField($data, 'Project.live_date_to'),
	    		'domain' 		   => Common::hashEmptyField($data, 'Project.domain'),
	    		'order' 		   => Common::hashEmptyField($data, 'Project.order'),
	    		'active' 		   => Common::hashEmptyField($data, 'Project.active'),
	    		'status' 		   => Common::hashEmptyField($data, 'Project.status'),
	    		'created' 		   => Common::hashEmptyField($data, 'Project.created'),
	    		'modified' 		   => Common::hashEmptyField($data, 'Project.modified'),
	    		'is_published' 	   => Common::hashEmptyField($data, 'Project.is_published'),
	    		'is_trial' 		   => Common::hashEmptyField($data, 'Project.is_trial'),
	    		'teaser_benefit'   => Common::hashEmptyField($data, 'Project.teaser_benefit'),
	    		'HavingProducts'   => Common::hashEmptyField($data, 'Project.HavingProducts'),
	    		'HavingUnits' 	   => Common::hashEmptyField($data, 'Project.HavingUnits'),
	    		'ProjectContact'   => Common::hashEmptyField($data, 'Project.ProjectContact'),
	    	);

	    	$result['Company'] = array(
	    		'original_id' => Common::hashEmptyField($data, 'Company.id', null),
	    		'user_id' 	  => Common::hashEmptyField($data, 'Company.user_id', null),
	    		'parent_id'   => Common::hashEmptyField($data, 'Company.parent_id', null),
	    		'lft' 		  => Common::hashEmptyField($data, 'Company.lft'),
	    		'rght' 		  => Common::hashEmptyField($data, 'Company.rght'),
	    		'country_id'  => Common::hashEmptyField($data, 'Company.country_id', null),
	    		'region_id'   => Common::hashEmptyField($data, 'Company.region_id', null),
	    		'city_id' 	  => Common::hashEmptyField($data, 'Company.city_id', null),
	    		'subarea_id'  => Common::hashEmptyField($data, 'Company.subarea_id', null),
	    		'slug' 		  => Common::hashEmptyField($data, 'Company.slug'),
	    		'name'		  => Common::hashEmptyField($data, 'Company.name'),
	    		'logo' 		  => Common::hashEmptyField($data, 'Company.logo'),
	    		'description' => Common::hashEmptyField($data, 'Company.description'),
	    		'address' 	  => Common::hashEmptyField($data, 'Company.address'),
	    		'zip' 		  => Common::hashEmptyField($data, 'Company.zip'),
	    		'location' 	  => Common::hashEmptyField($data, 'Company.location'),
	    		'longitude'   => Common::hashEmptyField($data, 'Company.longitude'),
	    		'latitude' 	  => Common::hashEmptyField($data, 'Company.latitude'),
	    		'type' 		  => Common::hashEmptyField($data, 'Company.type'),
	    		'active' 	  => Common::hashEmptyField($data, 'Company.active'),
	    		'status' 	  => Common::hashEmptyField($data, 'Company.status'),
	    		'modified' 	  => Common::hashEmptyField($data, 'Company.modified'),
	    		'created'  	  => Common::hashEmptyField($data, 'Company.created')
	    	);

	    	$result['ParentCompany'] = array(
	    		'original_id' => Common::hashEmptyField($data, 'ParentCompany.id', null),
	    		'user_id' 	  => Common::hashEmptyField($data, 'ParentCompany.user_id', null),
	    		'parent_id'   => Common::hashEmptyField($data, 'ParentCompany.parent_id', null),
	    		'lft' 		  => Common::hashEmptyField($data, 'ParentCompany.lft'),
	    		'rght' 		  => Common::hashEmptyField($data, 'ParentCompany.rght'),
	    		'country_id'  => Common::hashEmptyField($data, 'ParentCompany.country_id', null),
	    		'region_id'   => Common::hashEmptyField($data, 'ParentCompany.region_id', null),
	    		'city_id' 	  => Common::hashEmptyField($data, 'ParentCompany.city_id', null),
	    		'subarea_id'  => Common::hashEmptyField($data, 'ParentCompany.subarea_id', null),
	    		'slug' 		  => Common::hashEmptyField($data, 'ParentCompany.slug'),
	    		'name'		  => Common::hashEmptyField($data, 'ParentCompany.name'),
	    		'logo' 		  => Common::hashEmptyField($data, 'ParentCompany.logo'),
	    		'description' => Common::hashEmptyField($data, 'ParentCompany.description'),
	    		'address' 	  => Common::hashEmptyField($data, 'ParentCompany.address'),
	    		'zip' 		  => Common::hashEmptyField($data, 'ParentCompany.zip'),
	    		'location' 	  => Common::hashEmptyField($data, 'ParentCompany.location'),
	    		'longitude'   => Common::hashEmptyField($data, 'ParentCompany.longitude'),
	    		'latitude' 	  => Common::hashEmptyField($data, 'ParentCompany.latitude'),
	    		'type' 		  => Common::hashEmptyField($data, 'ParentCompany.type'),
	    		'active' 	  => Common::hashEmptyField($data, 'ParentCompany.active'),
	    		'status' 	  => Common::hashEmptyField($data, 'ParentCompany.status'),
	    		'modified' 	  => Common::hashEmptyField($data, 'ParentCompany.modified'),
	    		'created'  	  => Common::hashEmptyField($data, 'ParentCompany.created')
	    	);
    	}

    	$endResult['dataToSave'] = $result;

    	return $endResult;
    }

    // format value to update the project status
    function _callFormatUpdateStatusProjects($data) {
		$result = array();
    	if(!empty($data)){
	    	$result['ApiAdvanceDeveloper'] = array(
	    		'original_id' => $this->RmCommon->filterEmptyField($data, 'Project', 'id', null),
	    		'active' => $this->RmCommon->filterEmptyField($data, 'Project', 'active'),
	    		'status' => $this->RmCommon->filterEmptyField($data, 'Project', 'status'),
	    		'created' => $this->RmCommon->filterEmptyField($data, 'Project', 'created'),
	    		'modified' => $this->RmCommon->filterEmptyField($data, 'Project', 'modified'),
	    	);

    	}

    	return $result;

    }

	function getDataProjects(){		
		$this->controller->loadModel("Setting");
		$this->controller->loadModel("ApiQueueProject");
		$this->controller->loadModel("ApiAdvanceDeveloper");

		$get_setting_url = $this->controller->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'primedev-api-projects',
			),
		));
		
		$domain_api = Common::hashEmptyField($get_setting_url, 'Setting.link');
		$passkey 	= Common::hashEmptyField($get_setting_url, 'Setting.token');

		$first_create = $this->controller->ApiQueueProject->find('first', array(
			'conditions' => array(
				'ApiQueueProject.type_api' => 'data_project',
			),
		));

		if (!empty($first_create)) {

			$api_queue = $this->controller->ApiQueueProject->find('first', array(
				'conditions' => array(
					'ApiQueueProject.completed' => 0,
					'ApiQueueProject.type_api' => 'data_project',
				),
				'contain' => array(
					'ApiAdvanceDeveloper'
				),
				'order' => array(
					'ApiQueueProject.id' => 'DESC'
				)
			));

			if (!empty($api_queue)) {
				$id = Common::hashEmptyField($api_queue, 'ApiQueueProject.id');
				$last_api_modified = Common::hashEmptyField($api_queue, 'ApiQueueProject.last_api_advance_modified');

				$pass = array(
					'passkey' => $passkey,
					'lastupdated' => $last_api_modified,
				);

				$params = $this->RmCommon->filterEmptyField($this->controller->params->params, 'named', false, array());

				$apiUrl = $domain_api.$this->Html->url(array_merge(array(
					'controller' => 'projects',
					'action' => 'get_all_data_projects',
					'?' => $pass,
					'ext' => 'json',
					'api' => true,
				), $params));

				$apiUrl = htmlspecialchars_decode($apiUrl);

				$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
				$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data');

				$paging = $this->RmCommon->filterEmptyField($dataApi, 'paging', false, array());
				$datas = $this->RmCommon->filterEmptyField($dataApi, 'data', false, array());

				if(!empty($datas)){
					foreach ($datas as $key => $value) {

						$value = $this->_callFormatDataToSave($value);

						$dataProject = Common::hashEmptyField($value, 'dataToSave');

						$modified    = Common::hashEmptyField($dataProject, 'Project.modified');
						$original_id = Common::hashEmptyField($dataProject, 'Project.original_id');

						$current_project = $this->controller->ApiAdvanceDeveloper->find('first', array(
							'conditions' => array(
								'ApiAdvanceDeveloper.original_id' => $original_id
							)
						));

						if (empty($current_project)) {
							// first time / new data
							$result = $this->controller->ApiAdvanceDeveloper->doSave($dataProject, false, true);
							$current_project_id = $this->RmCommon->filterEmptyField($result, 'id');

							$msg = __('berhasil menyimpan data baru.');
							echo $msg;
						} else {
							$current_project_id = $this->RmCommon->filterEmptyField($current_project, 'ApiAdvanceDeveloper', 'id');
							$this->controller->ApiAdvanceDeveloper->doSave($dataProject, $current_project_id, true);

							$msg = __('data sudah ada.');
							echo $msg;
						}
						
						// update and save last project modified
						$this->controller->ApiQueueProject->updateLastApiAdvanceModified( $id, $current_project_id, $modified );
					}
				}else{
					$this->controller->ApiQueueProject->completedTask( $api_queue );
					$msg = __('belum ada data terbaru. Sync proyek selesai (complete task).');
					echo $msg;
				}

				die();
			} else {
				$options = array(
					'type_api' => 'data_project',
				);
				$result = $this->controller->ApiQueueProject->createNewJob( $options );

				if(!empty($result)){
					$this->getDataProjects();
				}
			}
		} else {
			$options = array(
				'type_api' => 'data_project',
			);
			$result = $this->controller->ApiQueueProject->createFirstJob( $options );

			if(!empty($result)){
				$this->getDataProjects();
			}	
		}

	}

	// get update status project from primedev
	function getDataUpdateProjects(){
		$this->controller->loadModel("Setting");
		$this->controller->loadModel("ApiQueueProject");
		$this->controller->loadModel("ApiAdvanceDeveloper");

		$get_setting_url = $this->controller->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'primedev-api-projects',
			),
		));
		
		$domain_api = $this->RmCommon->filterEmptyField($get_setting_url, 'Setting', 'link');

		$first_create = $this->controller->ApiQueueProject->find('first', array(
			'conditions' => array(
				'ApiQueueProject.type_api' => 'update_data_project',
			),
		));

		if (!empty($first_create)) {

			$api_queue = $this->controller->ApiQueueProject->find('first', array(
				'conditions' => array(
					'ApiQueueProject.completed' => 0,
					'ApiQueueProject.type_api' => 'update_data_project',
				),
			));

			$id_queue = $this->RmCommon->filterEmptyField($api_queue, 'ApiQueueProject', 'id');
			$last_api_modified = $this->RmCommon->filterEmptyField($api_queue, 'ApiQueueProject', 'last_api_advance_modified');

			$pass = array(
				'device' => 'primedev-update-projects',
				'passkey' => '571dea10-c1d8-4d35-b81f-0c3465ca98e3',
				'lastupdated' => $last_api_modified,
			);

			$params = $this->RmCommon->filterEmptyField($this->controller->params->params, 'named', false, array());

			$apiUrl = $domain_api.$this->Html->url(array_merge(array(
				'controller' => 'projects',
				'action' => 'update_status_projects',
				'?' => $pass,
				'ext' => 'json',
				'api' => true,
			), $params));

			$apiUrl = htmlspecialchars_decode($apiUrl);

			$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
			$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data');
			$datas 	 = $this->RmCommon->filterEmptyField($dataApi, 'data', false, array());
			
			if(!empty($datas)){

				foreach ($datas as $key => $value) {
					$value 		 = $this->_callFormatUpdateStatusProjects($value);
					$original_id = $this->RmCommon->filterEmptyField($value, 'ApiAdvanceDeveloper', 'original_id');
					$modified 	 = $this->RmCommon->filterEmptyField($value, 'ApiAdvanceDeveloper', 'modified');

					$saved_project = $this->controller->ApiAdvanceDeveloper->find('first', array(
						'conditions' => array(
							'ApiAdvanceDeveloper.original_id' => $original_id
						)
					));

			        $saved_project = $this->controller->ApiAdvanceDeveloper->getMergeList($saved_project, array(
			            'contain' => array(
			            	'City',
			            	'Region',
				            'PropertyType',
				            'ApiAdvanceDeveloperCompany',
				        ),
			        ));

					$saved_project = $this->controller->ApiAdvanceDeveloper->ApiDeveloperContactInfo->getMergeContact($saved_project, true);

			
					// if project saved update the status
					if (!empty($saved_project)) {
						$project_name = $this->RmCommon->filterEmptyField($saved_project, 'ApiAdvanceDeveloper', 'name');
						$result = $this->controller->ApiAdvanceDeveloper->doUpdateStatus( $value, $saved_project );
						$msg = __('Update status project %s dengan original_id %s, berhasil.<br>', $project_name, $original_id);
						echo $msg;
					} else {
						$result = array();
						$msg = __('Original_id %s project tidak ditemukan.<br>', $original_id);
						echo $msg;
					}
					
					// update and save by last project modified
					$this->controller->ApiQueueProject->updateLastApiAdvanceModified( $id_queue, $original_id, $modified );
					$this->RmCommon->setProcessParams($result, array(
						'controller' => 'projects',
						'action' => 'get_update_status_projects',
					), array(
						'ajaxFlash' => false,
						'flash' => false,
					));
				}

			} else {
				$msg = __('Belum ada data terbaru.');
				echo $msg;
			}

			die();
		} else {
			$options = array(
				'type_api' => 'update_data_project',
			);
			$result = $this->controller->ApiQueueProject->createFirstJob( $options );

			if(!empty($result)){
				$this->getDataUpdateProjects();
			}	
		}

	}

	function getDataResultRequest(){
		$this->controller->loadModel("Setting");
		$this->controller->loadModel("ApiQueueProject");
		$this->controller->loadModel("ApiRequestDeveloper");

		$get_setting_url = $this->controller->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => 'primedev-api-projects',
			),
		));
		
		$domain_api = $this->RmCommon->filterEmptyField($get_setting_url, 'Setting', 'link');

		$first_create = $this->controller->ApiQueueProject->find('first', array(
			'conditions' => array(
				'ApiQueueProject.type_api' => 'result_request',
			),
		));

		if (!empty($first_create)) {

			$api_queue = $this->controller->ApiQueueProject->find('first', array(
				'conditions' => array(
					'ApiQueueProject.completed' => 0,
					'ApiQueueProject.type_api' => 'result_request',
				),
				'order' => array(
					'ApiQueueProject.id' => 'DESC'
				)
			));

			if (!empty($api_queue)) {
				$id = $this->RmCommon->filterEmptyField($api_queue, 'ApiQueueProject', 'id');
				$last_api_modified = $this->RmCommon->filterEmptyField($api_queue, 'ApiQueueProject', 'last_api_advance_modified');

				$pass = array(
					'device' => 'primedev-api-projects',
					'passkey' => '571dea10-c1d8-4d35-b81f-0c3465ca98e3',
					'lastupdated' => $last_api_modified,
				);

				$params = $this->RmCommon->filterEmptyField($this->controller->params->params, 'named', false, array());

				$apiUrl = $domain_api.$this->Html->url(array_merge(array(
					'controller' => 'projects',
					'action' => 'get_result_request_project',
					'?' => $pass,
					'ext' => 'json',
					'api' => true,
				), $params));

				$apiUrl = htmlspecialchars_decode($apiUrl);

				$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
				$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data');

				$paging = $this->RmCommon->filterEmptyField($dataApi, 'paging', false, array());
				$datas = $this->RmCommon->filterEmptyField($dataApi, 'data', false, array());

				if(!empty($datas)){

					foreach ($datas as $key => $value) {
						$modified = $this->RmCommon->filterEmptyField($value, 'ApiRequestDeveloper', 'modified');
						$original_id = $this->RmCommon->filterEmptyField($value, 'ApiRequestDeveloper', 'api_advance_developer_id');
						$principle_id = $this->RmCommon->filterEmptyField($value, 'ApiRequestDeveloper', 'principle_id');
						$id_req = $this->RmCommon->filterEmptyField($value, 'ApiRequestDeveloper', 'id');

						$current_request = $this->controller->ApiRequestDeveloper->find('first', array(
							'conditions' => array(
								'ApiRequestDeveloper.principle_id' => $id_req,
								'ApiRequestDeveloper.principle_id' => $principle_id,
								'ApiRequestDeveloper.api_advance_developer_id' => $original_id
							),
							'order' => array(
								'ApiRequestDeveloper.modified' => 'DESC',
							),
						));

						$modified_req = $this->RmCommon->filterEmptyField($current_request, 'ApiRequestDeveloper', 'modified');
						$proj_name = $this->RmCommon->filterEmptyField($current_request, 'ApiRequestDeveloper', 'project_name');
						$status_request = $this->RmCommon->filterEmptyField($current_request, 'ApiRequestDeveloper', 'status_request');

						if (!empty($current_request) && $status_request != 'canceled') {
							$id_request = $this->RmCommon->filterEmptyField($value, 'ApiRequestDeveloper', 'id');
							$result = $this->controller->ApiRequestDeveloper->updateRequest($value, $id_request);

							// update and save last project modified
							$this->controller->ApiQueueProject->updateLastApiAdvanceModified( $id, $id_request, $modified );
							
							$this->RmCommon->setProcessParams($result, array(
								'controller' => 'projects',
								'action' => 'get_result_request_primedev',
							), array(
								'ajaxFlash' => false,
							));
						} elseif ( !empty($current_request) && $status_request == 'canceled') {
							$message = __('Request %s tidak diproses karena telah dibatalkan pada tgl %s. ', $proj_name, $modified_req);
							echo $message;
						}
					}

				}else{
					$this->controller->ApiQueueProject->completedTask( $api_queue );
					$msg = __('Sync result request selesai, (complete task).');
					echo $msg;
				}
			} else {
				$options = array(
					'type_api' => 'result_request',
					'create_new_update' => true,
				);
				$result = $this->controller->ApiQueueProject->createNewJobRequest( $options );

				if(!empty($result)){
					$this->getDataResultRequest();
				}
			}

			die();
		} else {
			$options = array(
				'type_api' => 'result_request',
			);
			$result = $this->controller->ApiQueueProject->createFirstJob( $options );

			if(!empty($result)){
				$this->getDataResultRequest();
			}	
		}
	}
	
}
?>