<?php

	$record = empty($record) ? array() : $record;

	if($record){
		$propertyActions	= empty($propertyActions) ? array() : $propertyActions;
		$propertyTypes		= empty($propertyTypes) ? array() : $propertyTypes;
		$certificates		= empty($certificates) ? array() : $certificates;
		$viewSites			= empty($viewSites) ? array() : $viewSites;
		$lotUnits			= empty($lotUnits) ? array() : $lotUnits;

		$recordID		= Common::hashEmptyField($record, 'Property.id');
		$typeID			= Common::hashEmptyField($record, 'Property.property_type_id', 1);
		$actionID		= Common::hashEmptyField($record, 'Property.property_action_id', 1);
		$certificateID	= Common::hashEmptyField($record, 'Property.certificate_id');
		$lotUnitID		= Common::hashEmptyField($record, 'PropertyAsset.lot_unit_id');
		$isLot			= Common::hashEmptyField($record, 'PropertyType.is_lot');
		$isResidence	= Common::hashEmptyField($record, 'PropertyType.is_residence');
		$isBuilding		= Common::hashEmptyField($record, 'PropertyType.is_building');
		$isSpace		= Common::hashEmptyField($record, 'PropertyType.is_space');

		$actionList			= Hash::combine($propertyActions, '{n}.PropertyAction.id', '{n}.PropertyAction.name');
		$typeList			= Hash::combine($propertyTypes, '{n}.PropertyType.id', '{n}.PropertyType.name');
		$certificateList	= Hash::combine($certificates, '{n}.Certificate.id', '{n}.Certificate.name_id');
		$viewSiteList		= Hash::combine($viewSites, '{n}.ViewSite.id', '{n}.ViewSite.name');
		$lotUnitList		= Hash::combine($lotUnits, '{n}.LotUnit.id', sprintf('{n}.LotUnit.%s', $isSpace ? 'name' : 'slug'));

		$actionName			= Common::hashEmptyField($actionList, $actionID, '');
		$typeName			= Common::hashEmptyField($typeList, $typeID, '');
		$certificateName	= Common::hashEmptyField($certificateList, $certificateID, ''); //$this->Property->getCertificate($record);
		$lotUnitName		= Common::hashEmptyField($lotUnitList, $lotUnitID, '');

		$globalData		= Configure::read('Global.Data');
		$specifications = array();
		$baseApiURL		= array(
			'plugin'		=> false, 
			'api'			=> true, 
			'controller'	=> 'api_properties', 
			'action'		=> 'master_data', 
			'ext'			=> 'json', 
		);

		?>
		<div id="property-spec-wrapper" class="mb20">
			<?php

				$ajaxURL = $this->Html->url(array(
					'backprocess'	=> true, 
					'controller'	=> 'properties', 
					'action'		=> 'toggle_specification', 
					$recordID, 
				), true);

				$dataMatch = str_replace('"', "'", json_encode(array(
					array('#sell-price-placeholder', array('1'), 'slide'), 
					array('#rent-price-placeholder', array('2'), 'slide'), 
				)));

				$inputTemplate = '<select class="handle-toggle" data-match="' . $dataMatch . '" data-reset-target="false">';

				$specifications[] = array(
					'label'	=> __('Status Properti *'), 
					'value'	=> $this->Html->link($actionName, '#', array(
						'data-value'	=> $actionID, 
						'data-name'		=> 'data[Property][property_action_id]', 
						'data-type'		=> 'select', 
						'data-mode'		=> 'inline', 
						'data-source'	=> str_replace('"', '\'', json_encode($actionList)), 
						'class'			=> 'editable editable-fullwidth editable-click', 
						'data-tpl'		=> $inputTemplate, 
						'data-role'		=> 'property-spec-toggle', 
						'data-wrapper'	=> '#property-spec-wrapper', 
						'data-url'		=> $ajaxURL, 
					)), 
				);

				$specifications[] = array(
					'label'	=> __('Jenis Properti *'), 
					'value'	=> $this->Html->link($typeName, '#', array(
						'data-value'	=> $typeID, 
						'data-name'		=> 'data[Property][property_type_id]', 
						'data-type'		=> 'select', 
						'data-mode'		=> 'inline', 
						'data-source'	=> str_replace('"', '\'', json_encode($typeList)), 
						'class'			=> 'editable editable-fullwidth editable-click', 
						'data-role'		=> 'property-spec-toggle', 
						'data-wrapper'	=> '#property-spec-wrapper', 
						'data-url'		=> $ajaxURL, 
					)), 
				);

				$label	= $isSpace ? 'Harga Satuan' : 'Satuan Luas';
				$temp	= array(array('value' => '', 'text' => __('Pilih %s', $label)));

				foreach($lotUnitList as $value => $text){
					$temp[]	= array('value' => $value, 'text' => $text);
				}

				$lotUnitList = $temp;

				$specifications[] = array(
					'label'	=> __('%s *', $label), 
					'value'	=> $this->Html->link($lotUnitName, '#', array(
						'data-value'	=> $lotUnitID, 
						'data-name'		=> 'data[PropertyAsset][lot_unit_id]', 
						'data-type'		=> 'select', 
						'data-mode'		=> 'inline', 
						'data-source'	=> str_replace('"', '\'', json_encode($lotUnitList)), 
						'class'			=> 'editable editable-fullwidth editable-click', 
					)), 
				);

				$temp = array(array('value' => '', 'text' => 'Pilih Sertifikat'));

				foreach($certificateList as $value => $text){
					$temp[]	= array('value' => $value, 'text' => $text);
				}

				$temp[] = array('value' => -1, 'text' => 'Lainnya');

				$certificateList = $temp;

				$dataMatch = str_replace('"', "'", json_encode(array(
					array('.other-text', array('-1'), 'slide'), 
				)));

				$inputTemplate = '<select class="handle-toggle" data-match="' . $dataMatch . '">';

				$othersCertificate = Common::hashEmptyField($record, 'Property.others_certificate');

				$othersClass = $certificateID >= 0 ? 'display:none;' : '';
				$othersInput = $certificateID >= 0 ? '' : $othersCertificate;
				$othersInput = $this->Html->link($othersInput, '#', array(
					'data-value'	=> $othersInput, 
					'data-name'		=> 'data[Property][others_certificate]', 
					'data-type'		=> 'text', 
					'data-mode'		=> 'inline', 
					'class'			=> 'editable editable-click other-text', 
					'style'			=> 'display:none;', 
				));

				$specifications[] = array(
					'label'	=> __('Sertifikat *'), 
					'value'	=> $this->Html->link($certificateName, '#', array(
						'data-value'	=> $certificateID, 
						'data-name'		=> 'data[Property][certificate_id]', 
						'data-type'		=> 'select', 
						'data-mode'		=> 'inline', 
						'data-source'	=> str_replace('"', '\'', json_encode($certificateList)), 
						'class'			=> 'editable editable-click', 
						'data-tpl'		=> $inputTemplate, 
					)) . $othersInput, 
				);

				if($isBuilding){
					$buildingSize		= Common::hashEmptyField($record, 'PropertyAsset.building_size');
					$specifications[]	= array(
						'label'	=> __('Luas Bangunan *'), 
						'value'	=> $this->Html->link($buildingSize, '#', array(
							'data-value'	=> $buildingSize, 
							'data-name'		=> 'data[PropertyAsset][building_size]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);
				}

				if($isLot){
					$lotSize			= Common::hashEmptyField($record, 'PropertyAsset.lot_size');
					$specifications[]	= array(
						'label'	=> __('Luas Tanah *'), 
						'value'	=> $this->Html->link($lotSize, '#', array(
							'data-value'	=> $lotSize, 
							'data-name'		=> 'data[PropertyAsset][lot_size]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);
				}

				$lotLength			= Common::hashEmptyField($record, 'PropertyAsset.lot_length');
				$specifications[]	= array(
					'label'	=> __('Panjang Tanah'), 
					'value'	=> $this->Html->link($lotLength, '#', array(
						'data-value'	=> $lotLength, 
						'data-name'		=> 'data[PropertyAsset][lot_length]', 
						'data-type'		=> 'text', 
						'data-mode'		=> 'inline', 
						'class'			=> 'editable editable-fullwidth editable-click', 
						'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
					)), 
				);

				$lotWidth			= Common::hashEmptyField($record, 'PropertyAsset.lot_width');
				$specifications[]	= array(
					'label'	=> __('Lebar Tanah'),
					'value'	=> $this->Html->link($lotWidth, '#', array(
						'data-value'	=> $lotWidth, 
						'data-name'		=> 'data[PropertyAsset][lot_width]', 
						'data-type'		=> 'text', 
						'data-mode'		=> 'inline', 
						'class'			=> 'editable editable-fullwidth editable-click', 
						'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
					)), 
				);

				if($isResidence){
					$beds		= Common::hashEmptyField($record, 'PropertyAsset.beds');
					$bedsMaid	= Common::hashEmptyField($record, 'PropertyAsset.beds_maid');
					$baths		= Common::hashEmptyField($record, 'PropertyAsset.baths');
					$bathsMaid	= Common::hashEmptyField($record, 'PropertyAsset.baths_maid');

					$specifications[] = array(
						'label'	=> __('Kamar Tidur *'),
						'value'	=> $this->Html->link($beds, '#', array(
							'data-value'	=> $beds, 
							'data-name'		=> 'data[PropertyAsset][beds]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);

					$specifications[] = array(
						'label'	=> __('Kamar Tidur Ekstra'),
						'value'	=> $this->Html->link($bedsMaid, '#', array(
							'data-value'	=> $bedsMaid, 
							'data-name'		=> 'data[PropertyAsset][beds_maid]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);

					$specifications[] = array(
						'label'	=> __('Kamar Mandi *'),
						'value'	=> $this->Html->link($baths, '#', array(
							'data-value'	=> $baths, 
							'data-name'		=> 'data[PropertyAsset][baths]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);

					$specifications[] = array(
						'label'	=> __('Kamar Mandi Ekstra'),
						'value'	=> $this->Html->link($bathsMaid, '#', array(
							'data-value'	=> $bathsMaid, 
							'data-name'		=> 'data[PropertyAsset][baths_maid]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);
				}

				if($isBuilding){
					$level			= Common::hashEmptyField($record, 'PropertyAsset.level');
					$cars			= Common::hashEmptyField($record, 'PropertyAsset.cars');
					$carports		= Common::hashEmptyField($record, 'PropertyAsset.carports');
					$phoneline		= Common::hashEmptyField($record, 'PropertyAsset.phoneline');
					$electricity	= Common::hashEmptyField($record, 'PropertyAsset.electricity');
					$furnished		= Common::hashEmptyField($record, 'PropertyAsset.furnished');
					$yearBuilt		= Common::hashEmptyField($record, 'PropertyAsset.year_built');
					$yearBuilt		= intval($yearBuilt) ? $yearBuilt : false;

					$specifications[] = array(
						'label'	=> __('Jumlah Lantai'),
						'value'	=> $this->Html->link($level, '#', array(
							'data-value'	=> $level, 
							'data-name'		=> 'data[PropertyAsset][level]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);

					$specifications[] = array(
						'label'	=> __('Garasi'),
						'value'	=> $this->Html->link($cars, '#', array(
							'data-value'	=> $cars, 
							'data-name'		=> 'data[PropertyAsset][cars]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);

					$specifications[] = array(
						'label'	=> __('Carport'),
						'value'	=> $this->Html->link($carports, '#', array(
							'data-value'	=> $carports, 
							'data-name'		=> 'data[PropertyAsset][carports]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);

					$specifications[] = array(
						'label'	=> __('Line Telepon'),
						'value'	=> $this->Html->link($phoneline, '#', array(
							'data-value'	=> $phoneline, 
							'data-name'		=> 'data[PropertyAsset][phoneline]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);

					$specifications[] = array(
						'label'	=> __('Daya Listrik'),
						'value'	=> $this->Html->link($electricity, '#', array(
							'data-value'	=> $electricity, 
							'data-name'		=> 'data[PropertyAsset][electricity]', 
							'data-type'		=> 'text', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-tpl'		=> '<input type="text" class="input_number form-control input-sm">', 
						)), 
					);

					$label			= 'Interior';
					$temp			= array(array('value' => '', 'text' => __('Pilih %s', $label)));
					$furnishList	= Common::hashEmptyField($globalData, 'furnished');
					$furnishedName	= Common::hashEmptyField($furnishList, $furnished);

					foreach($furnishList as $value => $text){
						$temp[]	= array('value' => $value, 'text' => $text);
					}

					$furnishList		= $temp;
					$specifications[]	= array(
						'label'	=> $label, 
						'value'	=> $this->Html->link($furnishedName, '#', array(
							'data-value'	=> $furnished, 
							'data-name'		=> 'data[PropertyAsset][furnished]', 
							'data-type'		=> 'select', 
							'data-mode'		=> 'inline', 
							'data-source'	=> str_replace('"', '\'', json_encode($furnishList)), 
							'class'			=> 'editable editable-fullwidth editable-click', 
						)), 
					);

					$directionID	= Common::hashEmptyField($record, 'PropertyAsset.property_direction_id');
					$conditionID	= Common::hashEmptyField($record, 'PropertyAsset.property_condition_id');
					$viewSiteID		= Common::hashEmptyField($record, 'PropertyAsset.view_site_id');

					$directionName	= Common::hashEmptyField($record, 'PropertyAsset.PropertyDirection.name');
					$conditionName	= Common::hashEmptyField($record, 'PropertyAsset.PropertyCondition.name');
					$viewSiteName	= Common::hashEmptyField($record, 'PropertyAsset.ViewSite.name');

					$label				= 'Arah Bangunan';
					$specifications[]	= array(
						'label'	=> __($label), 
						'value'	=> $this->Html->link($directionName, '#', array(
							'data-value'	=> $directionID, 
							'data-name'		=> 'data[PropertyAsset][property_direction_id]', 
							'data-type'		=> 'select', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-source'	=> $this->Html->url(array_merge($baseApiURL, array(
								'property_direction', 
								'list', 
								'?' => array('empty' => __('Pilih %s', $label)),
							)), true), 
						)), 
					);

					$label			= 'Tahun Dibangun';
					$years			= array(array('value' => '', 'text' => __('Pilih %s', $label)));
					$currentYear	= date('Y');

					for($index = date('Y'); $index > ($currentYear - 50) ; $index--){
						$index		= intval($index);
						$years[]	= array('value' => $index, 'text' => $index);
					}

					$specifications[]	= array(
						'label'	=> $label, 
						'value'	=> $this->Html->link($yearBuilt, '#', array(
							'data-value'	=> $yearBuilt, 
							'data-name'		=> 'data[PropertyAsset][year_built]', 
							'data-type'		=> 'select', 
							'data-mode'		=> 'inline', 
							'data-source'	=> str_replace('"', '\'', json_encode($years)), 
							'class'			=> 'editable editable-fullwidth editable-click', 
						)), 
					);

					$label				= 'Kondisi Bangunan';
					$specifications[]	= array(
						'label'	=> __($label), 
						'value'	=> $this->Html->link($conditionName, '#', array(
							'data-value'	=> $conditionID, 
							'data-name'		=> 'data[PropertyAsset][property_condition_id]', 
							'data-type'		=> 'select', 
							'data-mode'		=> 'inline', 
							'class'			=> 'editable editable-fullwidth editable-click', 
							'data-source'	=> $this->Html->url(array_merge($baseApiURL, array(
								'property_condition', 
								'list', 
								'?' => array('empty' => __('Pilih %s', $label)),
							)), true), 
						)), 
					);

					if($viewSiteList){
						$label	= __('View %s', $typeName);
						$temp	= array(array('value' => '', 'text' => __('Pilih %s', $label)));

						foreach($viewSiteList as $value => $text){
							$temp[]	= array('value' => $value, 'text' => $text);
						}

						$viewSiteList		= $temp;
						$specifications[]	= array(
							'label'	=> __($label), 
							'value'	=> $this->Html->link($viewSiteName, '#', array(
								'data-value'	=> $viewSiteID, 
								'data-name'		=> 'data[PropertyAsset][view_site_id]', 
								'data-type'		=> 'select', 
								'data-mode'		=> 'inline', 
								'data-source'	=> str_replace('"', '\'', json_encode($viewSiteList)), 
								'class'			=> 'editable editable-fullwidth editable-click', 
							)), 
						);
					}
				}

			//	generate all
				echo($this->element('blocks/properties/forms/easy_mode_specification_item', array(
					'items'	=> $specifications, 
				)));

			/*
				$specifications = $this->Property->_getSpecification($record, array(), array(
					'show_empty'	=> true, 
					'editable'		=> true, 
					'editable_mode'	=> 'inline', 
				));

				$searchTags		= array('<ul>', '</ul>', '<li>', '</li>', '<span>', '</span>', '<strong>', '</strong>');
				$replaceTags	= array(
					'', 
					'',
					'<div class="col-sm-6"><div class="form-group no-margin"><div class="row">', 
					'</div></div></div>',
					'<div class="col-xs-6 no-pright"><label class="control-label">',
					'</label></div>', 
					'<div class="col-xs-6 relative no-pleft no-pright">',
					'</div>',
				);

				echo(str_replace($searchTags, $replaceTags, $specifications));
			*/

			?>
		</div>
		<?php

	}

?>