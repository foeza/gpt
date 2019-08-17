<div class="locations-trigger">
	<?php

		$content		= '';
		$data			= $this->request->data;
		$isMarketTrend 	= Hash::get($data, 'UserCompanyConfig.mt_is_show_trend');
		$options		= array(
			'class'			=> 'relative col-sm-8 col-xl-4',
			'labelClass'	=> 'col-xl-1 col-sm-4 col-md-3 control-label taright',
			'frameClass'	=> 'col-sm-12',
		);

		echo($this->Rumahku->buildInputToggle('mt_is_show_trend', array_merge($options, array(
			'label'			=> __('Tampilkan Market Trend?'),
			'attributes'	=> array(
				'class'			=> 'handle-toggle-content',
				'data-target'	=> '.market-trend-box', 
			), 
		))));

		$content.= $this->Rumahku->buildInputToggle('mt_is_show_widget', array_merge($options, array(
			'label' => __('Tampilkan Widget Market Trend?'),
		)));

	//	$content.= $this->Rumahku->buildInputToggle('mt_is_all_company_data', array_merge($options, array(
	//		'label' => __('Gunakan data semua Perusahaan?'),
	//	)));

	//	https://basecamp.com/1789306/projects/10415456/todos/359349920 - [EN] - Area dibuat autocomplete
	//	$content.= $this->Html->tag('div', $this->element('blocks/properties/forms/location_picker', array(
	//		'options'	=> array(
	//			'mandatory'		=> false, 
	//			'model'			=> 'UserCompanyConfig', 
	//			'field_prefix'	=> 'mt_', 
	//			'class'			=> 'relative col-sm-8 col-xl-4',
	//			'labelClass'	=> 'col-xl-1 col-sm-4 col-md-3 control-label taright',
	//			'frameClass'	=> 'col-sm-12',
	//		), 
	//	)));

		$content.= $this->Rumahku->setFormAddress('UserCompanyConfig');
		$content.= $this->Rumahku->buildInputForm('mt_region_id', array_merge($options, array(
			'inputClass'=> 'regionId',
			'label'		=> __('Provinsi'),
			'empty'		=> __('Pilih Provinsi'),
		)));

		$content.= $this->Rumahku->buildInputForm('mt_city_id', array_merge($options, array(
			'inputClass'=> 'cityId',
			'label'		=> __('Kota'),
			'empty'		=> __('Pilih Kota'),
			'options'	=> empty($cities) ? array() : $cities, 
		)));

		$content.= $this->Rumahku->buildInputForm('mt_subarea_id', array_merge($options, array(
			'inputClass'=> 'subareaId',
			'label'		=> __('Area'),
			'empty'		=> __('Pilih Area'),
			'options'	=> empty($subareas) ? array() : $subareas, 
		)));

		$selected = Common::hashEmptyField($this->data, 'UserCompanyConfig.mt_property_type', array());
		$selected = is_string($selected) ? json_decode($selected, true) : $selected;;

		$label = $this->Html->tag('label', __('Tipe Properti (maksimal 4 tipe)'), array('class' => 'control-label'));
		$label = $this->Html->tag('div', $label, array('class' => 'col-xl-1 col-sm-4 col-md-3 control-label taright'));
		$input = '';

		$propertyTypes = empty($propertyTypes) ? array() : $propertyTypes;
		foreach($propertyTypes as $key => $propertyType){
			$typeID		= Common::hashEmptyField($propertyType, 'PropertyType.id');
			$typeSlug	= Common::hashEmptyField($propertyType, 'PropertyType.slug');
			$typeName	= Common::hashEmptyField($propertyType, 'PropertyType.name');

			$input.= $this->Html->div('col-sm-12 col-md-4', $this->Form->input(sprintf('UserCompanyConfig.mt_property_type.%s', $key), array(
				'error'			=> false, 
				'label'			=> __($typeName), 
				'div'			=> 'form-group', 
				'type'			=> 'checkbox', 
				'checked'		=> in_array($typeSlug, $selected), 
				'value'			=> $typeSlug, 
			)));
		}

		$error = $this->Form->error('UserCompanyConfig.mt_property_type');
		$input = $this->Html->div('relative col-sm-8 col-xl-4', $this->Html->div('row multi-checkbox-wrapper', $input.$error));

		$content.= $this->Html->div('form-group multi-checkbox', $this->Html->div('row', $label.$input));

		echo($this->Html->div('market-trend-box', $content, array(
			'style' => sprintf('display:%s;', $isMarketTrend ? 'block' : 'none'), 
		)));

	?>
</div>