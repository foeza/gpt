<?php
class RmApiProjectComponent extends Component {
	var $components = array(
		'RmCommon', 'Rest.Rest', 'RmBooking'
	);

	/**
	*	@param object $controller - inisialisasi class controller
	*/
	function initialize(Controller $controller, $settings = array()) {
		App::import('Helper', 'Html');
		$this->Html = new HtmlHelper(new View(null));

		$this->controller = $controller;
	}

	// API GET DATA FROM PRIMEDEV
	function dataProject( $origin_id ){
		$module_title = __('Product');
		$url_without_http = Configure::read('__Site.domain');
		$title_for_layout = sprintf(__('%s - %s'), $module_title, $url_without_http);

		$get_setting_url = $this->_getSettingData(array(
			'slug_api' => 'primedev-api-projects'
		));
		
		$domain_api = Common::hashEmptyField($get_setting_url, 'Setting.link');

		$apiUrl = $domain_api.$this->Html->url(array(
			'controller' => 'projects',
			'action' => 'get_project',
			$origin_id,
			'ext' => 'json',
			'api' => true,
		));

		$apiUrl = htmlspecialchars_decode($apiUrl);

		$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
		$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data');
		$values  = $this->RmCommon->filterEmptyField($dataApi, 'data', false, array());

		// init og meta developer project
		$save_path = Configure::read('__Site.general_folder');
		$og_title  = Common::hashEmptyField($values, 'Project.name', 'Developer');
		$og_image  = Common::hashEmptyField($values, 'Project.cover_img_sync');
		$promo 	   = Common::hashEmptyField($values, 'Project.promo');

		$og_meta = array(
			'size'  => 'l',
			'path'  => $save_path,
			'title' => $og_title,
			'image' => $og_image,
			'description' => $promo,
		);

		$this->RmCommon->_callRequestSubarea('Search');
		$this->RmCommon->getDataRefineProperty();

		$this->RmCommon->_layout_file(array(
			'map',
			'map-cozy',
		));

		$this->controller->set('active_menu', 'developers');
		$this->controller->set(compact(
			'module_title', 'title_for_layout', 'values', 'og_meta',
			'range_price'
		));
	}

	// api get data product
	function dataProduct( $origin_id ){
		$module_title = __('Product Unit');
		$url_without_http = Configure::read('__Site.domain');
		$title_for_layout = sprintf(__('%s - %s'), $module_title, $url_without_http);

		$get_setting_url = $this->_getSettingData(array(
			'slug_api' => 'primedev-api-projects'
		));

		$domain_api = Common::hashEmptyField($get_setting_url, 'Setting.link');

		$apiUrl = $domain_api.$this->Html->url(array(
			'controller' => 'products',
			'action' => 'get_data_product',
			$origin_id,
			'ext' => 'json',
			'api' => true,
		));

		$apiUrl = htmlspecialchars_decode($apiUrl);
		// debug($apiUrl);
		$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
		$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data');
		$values  = $this->RmCommon->filterEmptyField($dataApi, 'data', false, array());

		// init og meta product
		$save_path = Configure::read('__Site.general_folder');
		$og_title  = Common::hashEmptyField($values, 'Product.name', 'Product');
		$promo 	   = Common::hashEmptyField($values, 'Product.promo_title');

		$og_image  = false;
		if (!empty($values['Gallery'])) {
			foreach ($values['Gallery'] as $key => $value) {
				$document_type      = Common::hashEmptyField($value, 'DocumentMedia.document_type');
				$document_sub_type  = Common::hashEmptyField($value, 'DocumentMedia.document_sub_type');

				if ( $document_type == 'product' && $document_sub_type == 'primary' ) {
					$og_image = Common::hashEmptyField($value, 'Media.name');
				}
			}
		}

		$og_meta = array(
			'size'  => 'l',
			'path'  => $save_path,
			'title' => $og_title,
			'image' => $og_image,
			'description' => $promo,
		);

		$this->RmCommon->_callRequestSubarea('Search');
		$this->RmCommon->getDataRefineProperty();

		$this->RmCommon->_layout_file(array(
			'map',
			'map-cozy',
		));

		$this->controller->set('active_menu', 'developers');
		$this->controller->set(compact(
			'module_title', 'title_for_layout', 'values', 'og_meta',
			'range_price'
		));
	}

	// api get data only list productunit
	// $origin_id = project_id
	// project with no product goes here
	function listUnit( $origin_id ){
		$module_title = __('List Unit');
		$url_without_http = Configure::read('__Site.domain');
		$title_for_layout = sprintf(__('%s - %s'), $module_title, $url_without_http);

		$get_setting_url = $this->_getSettingData(array(
			'slug_api' => 'primedev-api-projects'
		));

		$domain_api = Common::hashEmptyField($get_setting_url, 'Setting.link');

		$apiUrl = $domain_api.$this->Html->url(array(
			'controller' => 'units',
			'action' => 'get_list_unit',
			$origin_id,
			'ext' => 'json',
			'api' => true,
		));

		$apiUrl = htmlspecialchars_decode($apiUrl);
		// debug($apiUrl);
		$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
		$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data');
		$values  = $this->RmCommon->filterEmptyField($dataApi, 'data', false, array());

		// debug($values);die();

		// init og meta product
		$save_path = Configure::read('__Site.general_folder');
		$og_title  = Common::hashEmptyField($values, 'Project.name', 'Developer');
		$promo 	   = Common::hashEmptyField($values, 'Project.promo');
		$og_image  = Common::hashEmptyField($values, 'Project.cover_img_sync');

		$og_meta = array(
			'size'  => 'l',
			'path'  => $save_path,
			'title' => $og_title,
			'image' => $og_image,
			'description' => $promo,
		);

		$this->RmCommon->_callRequestSubarea('Search');
		$this->RmCommon->getDataRefineProperty();

		$this->RmCommon->_layout_file(array(
			'map',
			'map-cozy',
		));

		$this->controller->set('active_menu', 'developers');
		$this->controller->set(compact(
			'module_title', 'title_for_layout', 'values', 'og_meta'
		));
	}

	// api get data product unit
	function dataProductUnit( $origin_id ){
		$module_title = __('Detail Unit');
		$url_without_http = Configure::read('__Site.domain');
		$title_for_layout = sprintf(__('%s - %s'), $module_title, $url_without_http);

		$params = $this->controller->params->params;
		$product_id = Common::hashEmptyField($params, 'named.product');

		$get_setting_url = $this->_getSettingData(array(
			'slug_api' => 'primedev-api-projects'
		));

		$domain_api = Common::hashEmptyField($get_setting_url, 'Setting.link');

		$apiUrl = $domain_api.$this->Html->url(array(
			'controller' => 'units',
			'action' => 'get_data_unit',
			$origin_id,
			$product_id,
			'ext' => 'json',
			'api' => true,
		));

		$apiUrl = htmlspecialchars_decode($apiUrl);
		// debug($apiUrl);
		$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
		$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data');
		$values  = $this->RmCommon->filterEmptyField($dataApi, 'data', false, array());

		// debug($values);die();
		/*check ijin menjual unit*/
		$product_unit_id = Common::hashEmptyField($values, 'ProductUnit.id');
		$project_id 	 = Common::hashEmptyField($values, 'ProductUnit.project_id');
		$status_selling  = $this->RmBooking->checkAllowSelling($project_id);
        // debug($status_selling);die();
        if(!empty($status_selling)){
			$link_api = __('transactions/bloks');
			if(!empty($product_unit_id)){
				$link_api .= '/product_unit_id:'.$product_unit_id;
			}
			if(!empty($product_id)){
				$link_api .= '/product_id:'.$product_id;
			}

			$bloks = $this->RmCommon->getAPI($link_api, array(
                'header' => array(
                    'slug' => 'primedev-api',
                    'data' => array(
                        'project' => $project_id,
                    ),
                ),
            ));

            // debug($bloks);die();

            $this->controller->set(compact('bloks'));
        }

        // init og meta unit
		$desc = Common::hashEmptyField($values, 'ProductUnit.description', false, array(
			'type' => 'strip_tags',
			'truncate' => array(
				'len' => 180,
			),		
		));

		$save_path 	  = Configure::read('__Site.general_folder');
		$unit_name 	  = Common::hashEmptyField($values, 'ProductUnit.name', 'Unit');
		$product_name = Common::hashEmptyField($values, 'Product.name');

		$og_title = sprintf('Unit: %s', $unit_name);
		if (!empty($product_name)) {
			$og_title = sprintf('%s - Unit: %s', $product_name, $unit_name);
		}

		$og_image  = false;
		$primary_image = Common::hashEmptyField($values, 'PrimaryImage');
		if (!empty($primary_image)) {
			$document_type      = Common::hashEmptyField($primary_image, 'DocumentMedia.document_type');
			$document_sub_type  = Common::hashEmptyField($primary_image, 'DocumentMedia.document_sub_type');

			if ( $document_type == 'product_unit' && $document_sub_type == 'primary' ) {
				$og_image = Common::hashEmptyField($primary_image, 'Media.name');
			}
		}

		$og_meta = array(
			'size'  => 'l',
			'path'  => $save_path,
			'title' => $og_title,
			'image' => $og_image,
			'description' => $desc,
		);

		$this->RmCommon->_callRequestSubarea('Search');
		$this->RmCommon->getDataRefineProperty();

		$this->RmCommon->_layout_file(array(
			'map',
			'map-cozy',
		));

		$this->controller->set('active_menu', 'developers');
		$this->controller->set('modal_template', 'modal_booking');

		$this->controller->set(compact(
			'module_title', 'title_for_layout', 'values', 'product_id', 'og_meta',
			'product_unit_id', 'status_selling'
		));

	}

	function _getSettingData( $options = false ){
		$this->controller->loadModel("Setting");
		$slug_api = Common::hashEmptyField($options, 'slug_api');
		$get_setting_url = $this->controller->Setting->find('first', array(
			'conditions' => array(
				'Setting.slug' => $slug_api,
			),
		));

		if (!empty($get_setting_url)) {
			return $get_setting_url;
		} else {
			return $this->RmCommon->redirectReferer(__('Maaf, akses tidak diijinkan.'));
		}
	}
	
	function rawTypeUnitData( $product_unit_id, $product_id = false ){
		$get_setting_url = $this->_getSettingData(array(
			'slug_api' => 'primedev-api-projects'
		));

		$domain_api = Common::hashEmptyField($get_setting_url, 'Setting.link');

		$apiUrl = $domain_api.$this->Html->url(array(
			'controller' => 'units',
			'action' => 'get_data_unit',
			$product_unit_id,
			$product_id,
			'ext' => 'json',
			'api' => true,
		));

		$apiUrl = htmlspecialchars_decode($apiUrl);
		// debug($apiUrl);
		$dataApi = $this->RmCommon->_callGetDataAPI($apiUrl);
		$dataApi = $this->RmCommon->filterEmptyField($dataApi, 'data');
		$values  = $this->RmCommon->filterEmptyField($dataApi, 'data', false, array());
		
		return $values;
	}
}
?>