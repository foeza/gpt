<?php

	$options = empty($options) ? array() : $options;
	$baseURL = array(
		'plugin'		=> false,
		'admin'			=> false,
		'controller'	=> 'ajax',
	);

	$model			= empty($model) ? 'PropertyAddress' : $model;
	$model			= Common::hashEmptyField($options, 'model', $model);
	$field			= Common::hashEmptyField($options, 'field', 'location_name');
	$fieldPrefix	= Common::hashEmptyField($options, 'field_prefix');
	$mandatory		= Common::hashEmptyField($options, 'mandatory', true, array('isset' => true));
	$label			= Common::hashEmptyField($options, 'label', 'Lokasi', array('isset' => true));
	$isSetAddress	= Common::hashEmptyField($options, 'set_address', true, array('isset' => true));

	$wrapperWrite	= Common::hashEmptyField($options, 'attributes.data-wrapper-write', '.location-wrapper');
	$getURL			= Common::hashEmptyField($options, 'attributes.data-ajax-url', array_merge($baseURL, array('action' => 'get_location')));
	$setURL			= Common::hashEmptyField($options, 'attributes.href', array_merge($baseURL, array('action' => 'set_location', $model)));

	$getURL = is_array($getURL) ? $this->Html->url($getURL, true) : $getURL;
	$setURL = is_array($setURL) ? $this->Html->url($setURL, true) : $setURL;

//	SEKARANG AJAX SET LOCATION UDAH GA DIPAKE JADI GA BUTUH INI
//	karena layoutnya standalone jadi harus bisa passing2 options lewat ajax request
//	echo($this->Form->hidden('Search.location_picker_options', array(
//		'id'	=> 'location-picker-options', 
//		'value'	=> json_encode($options), 
//	)));

//	remove above options
	$options = Common::_callUnset($options, array('model', 'field', 'field_prefix', 'mandatory', 'label', 'set_address'));

	if($label && $mandatory){
		$mandatory	= is_bool($mandatory) ? '*' : $mandatory;
		$label		= $label . ' ' . $mandatory;
	}

	$field			= sprintf('%s.%s%s', $model, $fieldPrefix, $field);
	$wrapperOptions	= array(
		'frameClass'	=> 'col-sm-12',
		'labelClass'	=> 'col-xl-2 taright col-xl-2 taright col-sm-2',
		'class'			=> 'relative col-sm-7 col-xl-4',
	);

	$locationName	= Common::hashEmptyField($this->data, $field);
	$inputs			= $this->Rumahku->buildInputForm($field, array_replace_recursive(array(
		'label'			=> __($label),
		'type'			=> 'text',
		'inputClass'	=> 'location-picker', 
		'infoText'		=> 'Format : Area, Kota/Kabupaten, Provinsi (* gunakan spasi setelah koma)<br>Contoh : Tomang, Jakarta Barat, Jakarta', 
		'infoClass'		=> 'extra-text',
		'attributes'	=> array(
			'data-role'					=> 'autocomplete',
			'autocomplete'				=> 'off',
			'data-change'				=> 'true',
			'data-location'				=> 'true',
			'data-highlighter'			=> 'true', 
			'data-loadingbar'			=> 'true', 
			'data-type'					=> 'content',
			'data-wrapper-write'		=> $wrapperWrite,
			'data-ajax-url'				=> $getURL,
			'href'						=> $setURL, 
			'data-selected'				=> !empty($locationName),
			'data-form'					=> '#location-picker-options', 
		), 
	), $wrapperOptions, $options));

//	$inputs.= $this->Rumahku->buildInputForm(sprintf('%s.zip', $model), array_replace_recursive(array(
//		'label'			=> __('Kode Pos *'),
//		'type'			=> 'text',
//		'inputClass'	=> 'rku-zip input_number',
//	), $wrapperOptions));

//	$regionID	= Common::hashEmptyField($this->data, sprintf('%s.%sregion_id', $model, $fieldPrefix));
//	$cityID		= Common::hashEmptyField($this->data, sprintf('%s.%scity_id', $model, $fieldPrefix));

//	$this->request->data = Hash::insert($this->request->data, sprintf('%s.current_region_id', $model), $regionID);
//	$this->request->data = Hash::insert($this->request->data, sprintf('%s.current_city_id', $model), $cityID);

	$hiddenInputs = '';

	if($isSetAddress){
		$hiddenInputs = $this->Rumahku->setFormAddress($model);
	}

	$hiddenInputs.= $this->Form->hidden(sprintf('%s.%sregion_id', $model, $fieldPrefix), array('class' => 'regionId'));
	$hiddenInputs.= $this->Form->hidden(sprintf('%s.%scity_id', $model, $fieldPrefix), array('class' => 'cityId'));
	$hiddenInputs.= $this->Form->hidden(sprintf('%s.%ssubarea_id', $model, $fieldPrefix), array('class' => 'subareaId'));

	echo($this->Html->tag('div', $inputs . $hiddenInputs, array(
		'class'	=> 'location-wrapper', 
	)));

//	$message = $this->Html->tag('p', __('Format : Area, Kota/Kabupaten, Provinsi (* Gunakan spasi setelah koma)'));
//	$message.= $this->Html->tag('p', __('Contoh : Tomang, Jakarta Barat, Jakarta'));

//	echo($this->Html->tag('div', $message, array(
//		'class' => 'info-full alert mb20', 
//	)));

?>