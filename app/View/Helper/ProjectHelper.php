<?php
class ProjectHelper extends AppHelper {
	var $helpers = array( 'Rumahku', 'Html', 'Number', );

	/*
		- check if request already exist
		- return true or false
	*/
	public function _callCheckRequest( $value, $parent_id ) {
		$result = false;

		$project_request = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper');
		$principle_id = $this->Rumahku->filterEmptyField($project_request, 'ApiRequestDeveloper', 'principle_id');

		if ( !empty($project_request) && $principle_id == $parent_id) {
			$result = true;
		}

		return $result;
	}

	public function _callInfoDeveloper($value) {
		$result_address = '';
		$property_type = $this->Rumahku->filterEmptyField($value, 'PropertyType', 'name', '');
		if (!empty($property_type)) {
			$property_type = sprintf(__('%s - &nbsp;'), $property_type);
		}

		$companyName = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloperCompany', 'name');
		$dev_name = sprintf(__('by : %s'), $this->Html->tag('strong', $companyName));

		$address = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloperCompany', 'address');
		$zip = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloperCompany', 'zip');
		$city = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloperCompany', 'City');
		$region = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloperCompany', 'Region');
		$dev_city = $this->Rumahku->filterEmptyField($city, 'name');
		$dev_region = $this->Rumahku->filterEmptyField($region, 'name');

		$have_parent = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloperCompany', 'parent_id');

		// Data Parent Company
		if ( $have_parent ) {
			$ParentCompany = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloperCompany', 'ParentCompany');

			$companyName = $this->Rumahku->filterEmptyField($ParentCompany, 'ApiAdvanceDeveloperCompany', 'name');
			$dev_name = sprintf(__('by : %s'), $this->Html->tag('strong', $companyName));

			$developer_name = $this->Rumahku->filterEmptyField($ParentCompany, 'ApiAdvanceDeveloperCompany', 'name');
			$address = $this->Rumahku->filterEmptyField($ParentCompany, 'ApiAdvanceDeveloperCompany', 'address');
			$zip = $this->Rumahku->filterEmptyField($ParentCompany, 'ApiAdvanceDeveloperCompany', 'zip');
			$city = $this->Rumahku->filterEmptyField($ParentCompany, 'City');
			$region = $this->Rumahku->filterEmptyField($ParentCompany, 'Region');
			$dev_city = $this->Rumahku->filterEmptyField($city, 'name');
			$dev_region = $this->Rumahku->filterEmptyField($region, 'name');
		}

		if (!empty($address) && !empty($dev_city)) {
			$addressDev = sprintf(__('%s, %s, %s, %s'), $address, $dev_city, $dev_region, $zip);
			$result_address = $addressDev;
		}

		$infoDev['Result'] = array(
			'property_type' => $property_type,
			'dev_name' => $dev_name,
			'result_address' => $result_address,
		);

		return $infoDev;
	}

	public function _callInfoProject($value, $value_only = false) {
		$project_name = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'name');

		$address = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'address');
		$zip = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloper', 'zip');
		$city = $this->Rumahku->filterEmptyField($value, 'City');
		$region = $this->Rumahku->filterEmptyField($value, 'Region');
		$project_city = $this->Rumahku->filterEmptyField($city, 'name');
		$project_region = $this->Rumahku->filterEmptyField($region, 'name');

		$have_parent = $this->Rumahku->filterEmptyField($value, 'ApiAdvanceDeveloperCompany', 'parent_id');

		$addressProject = sprintf(__('%s, %s, %s, %s'), $address, $project_city, $project_region, $zip);
		if ($value_only) {
			$result_address = $addressProject;
		} else {
			$result_address = sprintf(__('Lokasi : %s'), $addressProject);
		}

		$infoDev['Result'] = array(
			'project_name' => $project_name,
			'result_address' => $result_address,
		);

		return $infoDev;
	}

	public function _callProjectContact( $values = array() ) {
		$data_project = false;

		if (!empty($values)) {
			foreach ($values as $key => $data) {
				$value_contact = $this->Rumahku->filterEmptyField($data, 'ApiDeveloperContactInfo', 'value');
				$label = $this->Rumahku->filterEmptyField($data, 'ApiDeveloperContactInfo', 'label');
				$type = $this->Rumahku->filterEmptyField($data, 'ApiDeveloperContactInfo', 'type');

				// format to save
				if ($label == 'phone') {
					$tmp_val['phone'] = $value_contact;
				} elseif ($label == 'fax') {
					$tmp_val['fax'] = $value_contact;
				} elseif ($label == 'email' && $type == 'cc') {
					$tmp_val['email_cc'] = $value_contact;
				} elseif ($label == 'email') {
					$tmp_val['email'] = $value_contact;
				}
			}
			
			$data_project['ProjectContact'] = $tmp_val;
		}

		return $data_project;
	}

	public function infoDeveloper($value) {
		$result_address= '';
		$property_type = Common::hashEmptyField($value, 'PropertyType.name', '');
		$companyName   = Common::hashEmptyField($value, 'ApiAdvanceDeveloperCompany.name');
		$address 	   = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.address');
		$zip 		   = Common::hashEmptyField($value, 'ApiAdvanceDeveloper.zip');
		$dev_city      = Common::hashEmptyField($value, 'City.name');
		$dev_region    = Common::hashEmptyField($value, 'Region.name');
		$dev_name 	   = __('by : %s', $this->Html->tag('strong', $companyName, array('class' => 'notranslate')));

		if (!empty($property_type)) {
			$property_type = sprintf(__('%s - &nbsp;'), $property_type);
		}

		if (!empty($address) && !empty($dev_city)) {
			$addressDev = sprintf(__('%s, %s, %s, %s'), $address, $dev_city, $dev_region, $zip);
			$result_address = $addressDev;
		}

		$infoDev['Result'] = array(
			'dev_name' 		 => $dev_name,
			'property_type'  => $property_type,
			'result_address' => $result_address,
		);

		return $infoDev;
	}

	// format array from api
	public function infoProject($values, $value_only = false) {
		$project_name   = Common::hashEmptyField($values, 'Project.name');
		$project_addr   = Common::hashEmptyField($values, 'Project.address');
		$project_zip    = Common::hashEmptyField($values, 'Project.zip');
		$project_city   = Common::hashEmptyField($values, 'City.name');
		$project_region = Common::hashEmptyField($values, 'Region.name');

		$addressProject = sprintf(__('%s, %s, %s, %s'), $project_addr, $project_city, $project_region, $project_zip);
		if ($value_only) {
			$result_address = $addressProject;
		} else {
			$result_address = sprintf(__('Lokasi : %s'), $addressProject);
		}

		$infoDev['Result'] = array(
			'project_name' => $project_name,
			'result_address' => $result_address,
		);

		return $infoDev;
	}

	function getPrice( $data, $empty = false, $just_data_array = false, $display_price_sold = true ){
		$for_data_array = array();

        $price = Common::hashEmptyField($data, 'ProductUnit.price');
        $currency = Common::hashEmptyField($data, 'Currency.symbol');

		$for_data_array['price'] = $price;

		if( !empty($price) ) {
			$price = $this->Number->currency($price, $currency.' ', array('places' => 0));
		} else if( !empty($empty) ) {
			$price = $this->Html->tag('span', $empty, array(
				'class' => 'disabled',
			));
		}

		if($just_data_array == true){
			return $for_data_array;
		}else{
			return $price;
		}
	}

	function getRangePriceUnit($values, $_format = 'range') {
		$range_price = '';
		$format_price_max = '';
		$format_price_min = '';

		foreach ($values as $key => $data) {	
			$max_price = $data['maxPrice'];
			$min_price = $data['minPrice'];
			if (!empty($max_price) && !empty($min_price)) {
				$format_price_max = $this->Number->currency($max_price, 'Rp.'.' ', array('places' => 0));
				$format_price_min = $this->Number->currency($min_price, 'Rp.'.' ', array('places' => 0));
			}

			if ($_format == 'range') {
				$range_price = $format_price_min.' - '.$format_price_max;
			} else {
				$range_price = 'Mulai dari '.$format_price_min;
			}
		}

		return $range_price;
	}

	function _callListUnitMaterial( $data, $options = false, $wrapper = true ) {
		$result = '';

		$UnitMaterial = Common::hashEmptyField($data, 'ProductUnitSpecification');

		if (!empty($UnitMaterial)) {
			foreach ($UnitMaterial as $key => $value) {
				$name_material = Common::hashEmptyField($value, 'ProductUnitSpecification.value');
				$name_material = ucfirst($name_material);
				$type_material = Common::hashEmptyField($value, 'UnitMaterial.name');

				if (!empty($name_material)) {
					// Level Building
					$spec[] = array(
						'name' 	=> $type_material,
						'value' => $name_material,
					);
				}
			}
		}

		$list_options = $this->Rumahku->filterEmptyField($options, 'list_options', false, array(
			'class' => 'clearafter'
		));
 
		if( !empty($spec) ) {
			if( !empty($wrapper) ) {
				$contentLi = '';

				if( is_array($wrapper) ) {
					$wrapperLabel = $wrapper['wrapperLabel'];
					$wrapperValue = $wrapper['wrapperValue'];
				} else {
					$wrapperLabel = 'span';
					$wrapperValue = 'strong';
				}

				foreach ($spec as $key => $value) {
					$lblSpec = $this->Html->tag($wrapperLabel, sprintf('%s: ', $value['name']));
					$lblSpec .= $this->Html->tag($wrapperValue, $value['value']);
					
					$lblSpec = $this->Html->tag('span', sprintf('%s: ', $value['name']));
					$lblSpec .= $this->Html->tag('strong', $value['value']);

					$contentLi .= $this->Html->tag('li', $lblSpec, $list_options);
				}

				$result = $this->Html->tag('ul', $contentLi, $options);
			} else {
				$result = $spec;
			}
		}

		return $result;
	}

	function _callSpecUnit( $data, $options = false, $wrapper = true ) {
		$result = '';

		$building_size = Common::hashEmptyField($data, 'ProductUnit.building_size');
		$lot_size 	   = Common::hashEmptyField($data, 'ProductUnit.lot_size');
		$beds 		   = Common::hashEmptyField($data, 'ProductUnit.beds');
		$baths 		   = Common::hashEmptyField($data, 'ProductUnit.baths');
		$level 		   = Common::hashEmptyField($data, 'ProductUnit.level');

		$list_options = $this->Rumahku->filterEmptyField($options, 'list_options', false, array(
			'class' => 'clearafter'
		));

		if (!empty($baths)) {
			// Bathroom
			$spec[] = array(
				'alias' => __('KM'),
				'name' => __('Kamar Mandi'),
				'value' => $baths
			);
		}

		if (!empty($beds)) {
			// Bedroom
			$spec[] = array(
				'alias' => __('KT'),
				'name' => __('Kamar Tidur'),
				'value' => $beds
			);
		}

		if (!empty($lot_size)) {
			// Lot Size
			$spec[] = array(
				'alias' => __('LT'),
				'name' => __('Luas Tanah'),
				'value' => trim(sprintf('%s M%s', $lot_size, $this->Html->tag('sup', '2')))
			);
		}
		
		if (!empty($building_size)) {
			// Building Size
			$spec[] = array(
				'alias' => __('LB'),
				'name' => __('L. Bangunan'),
				'value' => trim(sprintf('%s M%s', $building_size, $this->Html->tag('sup', '2')))
			);
		}

		if (!empty($level)) {
			// Level Building
			$spec[] = array(
				'alias' => __('Lt'),
				'name' => __('Lantai'),
				'value' => trim(sprintf('%s', $level))
			);
		}
 
		if( !empty($spec) ) {
			if( !empty($wrapper) ) {
				$contentLi = '';

				if( is_array($wrapper) ) {
					$wrapperLabel = $wrapper['wrapperLabel'];
					$wrapperValue = $wrapper['wrapperValue'];
				} else {
					$wrapperLabel = 'span';
					$wrapperValue = 'strong';
				}

				foreach ($spec as $key => $value) {
					$lblSpec = $this->Html->tag($wrapperLabel, sprintf('%s: ', $value['name']));
					$lblSpec .= $this->Html->tag($wrapperValue, $value['value']);
					
					$lblSpec = $this->Html->tag('span', sprintf('%s: ', $value['name']));
					$lblSpec .= $this->Html->tag('strong', $value['value']);

					$contentLi .= $this->Html->tag('li', $lblSpec, $list_options);
				}

				$result = $this->Html->tag('ul', $contentLi, $options);
			} else {
				$result = $spec;
			}
		}

		return $result;
	}

	function callSpecUnit( $data, $options = false, $wrapper = true ) {
		$result = '';

		$building_size = Common::hashEmptyField($data, 'ProductUnit.building_size');
		$lot_size 	   = Common::hashEmptyField($data, 'ProductUnit.lot_size');
		$beds 		   = Common::hashEmptyField($data, 'ProductUnit.beds');
		$baths 		   = Common::hashEmptyField($data, 'ProductUnit.baths');
		$level 		   = Common::hashEmptyField($data, 'ProductUnit.level');

		$list_options = $this->Rumahku->filterEmptyField($options, 'list_options', false, array(
			'class' => 'clearafter'
		));

		if (!empty($baths)) {
			// Bathroom
			$spec[] = array(
				'alias' => __('KM'),
				'name' => __('Kamar Mandi'),
				'value' => $baths,
			);
		}

		if (!empty($beds)) {
			// Bedroom
			$spec[] = array(
				'alias' => __('KT'),
				'name' => __('Kamar Tidur'),
				'value' => $beds,
			);
		}

		if (!empty($lot_size)) {
			// Lot Size
			$spec[] = array(
				'alias' => __('LT'),
				'name' => __('Luas Tanah'),
				'value' => trim(sprintf('%s M%s', $lot_size, $this->Html->tag('sup', '2'))),
			);
		}
		
		if (!empty($building_size)) {
			// Building Size
			$spec[] = array(
				'alias' => __('LB'),
				'name' => __('L. Bangunan'),
				'value' => trim(sprintf('%s M%s', $building_size, $this->Html->tag('sup', '2'))),
			);
		}

		if (!empty($level)) {
			// Level Building
			$spec[] = array(
				'alias' => __('Lt'),
				'name' => __('Lantai'),
				'value' => trim(sprintf('%s', $level)),
			);
		}
 
		if( !empty($spec) ) {
			if( !empty($wrapper) ) {
				$contentLi = '';

				if( is_array($wrapper) ) {
					$wrapperLabel = $wrapper['wrapperLabel'];
					$wrapperValue = $wrapper['wrapperValue'];
				} else {
					$wrapperLabel = 'span';
					$wrapperValue = 'strong';
				}

				foreach ($spec as $key => $value) {
					$lblSpec = $this->Html->tag($wrapperLabel, sprintf('%s: ', $value['name']));
					$lblSpec .= $this->Html->tag($wrapperValue, $value['value']);
					
					$lblSpec = $this->Html->tag('span', sprintf('%s: ', $value['name']));
					$lblSpec .= $this->Html->tag('strong', $value['value']);

					$contentLi .= $this->Html->tag('li', $lblSpec, $list_options);
				}

				$result = $this->Html->tag('ul', $contentLi, $options);
			} else {
				$result = $spec;
			}
		}

		return $result;
	}

	function callShortSpec($values) {
		$result = '';
		$contentLi = '';
		$wrapperLabel = 'span';
		$wrapperValue = 'strong';

		if (!empty($values)) {
			foreach ($values as $key => $data) {
				$data_arr = array('KM', 'KT');
				if (in_array($data['alias'], $data_arr) ) {
					$lblSpec = $this->Html->tag($wrapperLabel, sprintf('%s: ', $data['name']));
					$lblSpec .= $this->Html->tag($wrapperValue, $data['value']);
					
					$lblSpec = $this->Html->tag('span', sprintf('%s: ', $data['name']));
					$lblSpec .= $this->Html->tag('strong', $data['value']);

					$contentLi .= $this->Html->tag('li', $lblSpec);
				}
			}

			$result = $this->Html->tag('ul', $contentLi);
		}

		return $result;
	}

	function getUnitMaterial( $data, $options = false, $wrapper = true ) {
		$result = '';

		$UnitMaterial = Common::hashEmptyField($data, 'ProductUnitSpecification');

		if (!empty($UnitMaterial)) {
			foreach ($UnitMaterial as $key => $value) {
				$name_material = Common::hashEmptyField($value, 'ProductUnitSpecification.value');
				$name_material = ucfirst($name_material);
				$type_material = Common::hashEmptyField($value, 'UnitMaterial.name');

				if (!empty($name_material)) {
					// Level Building
					$spec[] = array(
						'name' 	=> $type_material,
						'value' => $name_material,
					);
				}
			}
		}

		$list_options = $this->Rumahku->filterEmptyField($options, 'list_options', false, array(
			'class' => 'clearafter'
		));
 
		if( !empty($spec) ) {
			if( !empty($wrapper) ) {
				$contentLi = '';

				if( is_array($wrapper) ) {
					$wrapperLabel = $wrapper['wrapperLabel'];
					$wrapperValue = $wrapper['wrapperValue'];
				} else {
					$wrapperLabel = 'span';
					$wrapperValue = 'strong';
				}

				foreach ($spec as $key => $value) {
					$lblSpec = $this->Html->tag($wrapperLabel, sprintf('%s: ', $value['name']));
					$lblSpec .= $this->Html->tag($wrapperValue, $value['value']);
					
					$lblSpec = $this->Html->tag('span', sprintf('%s: ', $value['name']));
					$lblSpec .= $this->Html->tag('strong', $value['value']);

					$contentLi .= $this->Html->tag('li', $lblSpec, $list_options);
				}

				$result = $this->Html->tag('ul', $contentLi, $options);
			} else {
				$result = $spec;
			}
		}

		return $result;
	}

}