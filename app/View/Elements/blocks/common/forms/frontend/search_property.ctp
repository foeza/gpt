<?php

	$globalVars			= empty($_global_variable) ? array() : $_global_variable;
	$roomOptions		= Common::hashEmptyField($globalVars, 'room_options', array());
	$lotOptions			= Common::hashEmptyField($globalVars, 'lot_options', array());
	$priceOptions		= Common::hashEmptyField($globalVars, 'price_options', array());
	$furnishedOptions	= Common::hashEmptyField($globalVars, 'furnished', array());

	$propertyTypes		= empty($propertyTypes) ? array() : $propertyTypes;
	$propertyActions	= empty($propertyActions) ? array() : $propertyActions;
	$propertyDirections	= empty($propertyDirections) ? array() : $propertyDirections;
	$categoryStatus		= empty($categoryStatus) ? array() : $categoryStatus;
	$certificates		= empty($certificates) ? array() : $certificates;

	$template = '';
	$template.= $this->Form->hidden('show');
	$template.= $this->Form->hidden('sort', array('id' => 'hidden-sort'));

	if(!empty($list_companies)){
		$template.= $this->Form->input('principle_id', array(
			'label'		=> __('Perusahaan'), 
			'empty'		=> __('Pilih Semua'),
			'options'	=> $list_companies,
			'class'		=> 'form-control clearit',
		));
	}

	$template.= $this->Form->input('keyword', array(
		'placeholder' => __('Provinsi, Kota, Area, dll...'),
	));

	$template.= $this->Form->input('property_action', array(
		'label'		=> __('Jenis Properti'),
		'options'	=> $propertyActions,
	));

	$template.= $this->Form->input('user', array(
		'label'			=> __('Nama/Email Agen'),
		'placeholder'	=> __('Nama/Email Agen'),
	));

	$template.= $this->Form->input('typeid', array(
		'id'		=> 'propertyType',
		'label'		=> __('Tipe Properti'), 
		'empty'		=> __('Pilih Tipe Properti'),
		'options'	=> $propertyTypes,
	));

	$template.= $this->Form->input('property_status_id', array(
		'id'		=> 'baths',
		'label'		=> __('Kategori Properti'), 
		'empty'		=> __('Pilih Kategori'),
		'options'	=> $categoryStatus,
	));

	$template.= $this->Form->input('region', array(
		'type'	=> 'select',
		'label'	=> __('Provinsi'), 
		'empty'	=> __('Semua'),
		'class'	=> 'form-control clearit regionId',
	));

	$template.= $this->Form->input('city', array(
		'type'	=> 'select',
		'label'	=> __('Kota'), 
		'empty'	=> __('Semua'),
		'class'	=> 'form-control clearit cityId',
	));

	$template.= $this->Form->input('subarea', array(
		'type'	=> 'select',
		'label'	=> __('Area'), 
		'empty'	=> __('Semua'),
		'class'	=> 'form-control clearit subareaId',
	));

	$template.= $this->Form->input('lot_size', array(
		'id'		=> 'lotSizeId',
		'label'		=> __('Luas Tanah'), 
		'empty'		=> __('Semua'),
		'options'	=> $lotOptions,
	));

	$template.= $this->Form->input('building_size', array(
		'id'		=> 'buildingSizeId',
		'label'		=> __('Luas Bangunan'), 
		'empty'		=> __('Semua'),
		'options'	=> $lotOptions,
	));

	$template.= $this->Form->input('beds', array(
		'id'		=> 'beds',
		'label'		=> __('Kamar Tidur'), 
		'empty'		=> __('Semua'),
		'options'	=> $roomOptions,
	));

	$template.= $this->Form->input('baths', array(
		'id'		=> 'baths',
		'label'		=> __('Kamar Mandi'), 
		'empty'		=> __('Semua'),
		'options'	=> $roomOptions,
	));

	$template.= $this->Form->input('property_direction', array(
		'label'		=> __('Arah Bangunan'), 
		'empty'		=> __('Semua'),
		'options'	=> $propertyDirections,
	));

	$priceLabel = $this->Form->label('Search.price', __('Range Harga'));
	$priceInput = $this->element('blocks/common/forms/dynamic_price_input', array(
		'empty'		=> __('Semua'), 
		'model'		=> 'Search', 
		'field'		=> 'price', 
		'freetext'	=> true, 
		'options'	=> $priceOptions,
	));

	$template.= $this->Html->tag('div', $priceLabel . $priceInput, array(
		'class' => 'form-group', 
	));

//	$template.= $this->Form->input('price', array(
//		'id'		=> 'baths',
//		'label'		=> __('Range Harga'), 
//		'empty'		=> __('Semua'),
//		'options'	=> $priceOptions,
//	));

	$template.= $this->Form->input('certificate', array(
		'label'		=> __('Sertifikat'), 
		'empty'		=> __('Semua'),
		'options'	=> $certificates,
	));

	$template.= $this->Form->input('furnished', array(
		'label'		=> __('Interior'), 
		'empty'		=> __('Semua'),
		'options'	=> $furnishedOptions,
	));

	echo($template);

?>