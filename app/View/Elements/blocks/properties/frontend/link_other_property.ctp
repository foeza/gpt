<?php
		// debug($value);die();
		$custom_class_link = !empty($custom_class_link)?$custom_class_link:array();

        $property_address = Common::hashEmptyField($value, 'PropertyAddress');
        $property_action  = Common::hashEmptyField($value, 'Property.property_action_id');
        $region_slug 	  = Common::hashEmptyField($property_address, 'Region.slug');
        $city_name 		  = Common::hashEmptyField($property_address, 'City.name');
        $city_slug 		  = Common::hashEmptyField($property_address, 'City.slug');
        $subarea_name 	  = Common::hashEmptyField($property_address, 'Subarea.name');
        $subarea_slug 	  = Common::hashEmptyField($property_address, 'Subarea.slug');

        $default_array = array(
      		'class' => 'link-underline notranslate'
      	);

		$search_city = $this->Html->link($city_name, array(
			'controller'		=> 'properties',
			'action'			=> 'find',
			'region'			=> $region_slug,
			'city'				=> $city_slug,
			'property_action'	=> $property_action,
		), array_merge($default_array, $custom_class_link));

		$search_area = $this->Html->link($subarea_name, array(
			'controller'		=> 'properties',
			'action'			=> 'find',
			'region'			=> $region_slug,
			'city'				=> $city_slug,
			'subarea'			=> $subarea_slug,
			'property_action'	=> $property_action,
		), array_merge($default_array, $custom_class_link));
		
		$link_other_property = __('Cari properti lainnya di area %s atau %s', $this->Html->tag('strong', $search_area), $this->Html->tag('strong', $search_city));

		echo $this->Html->tag('div', $link_other_property, array(
			'class' => 'link-another-property'
		));
?>