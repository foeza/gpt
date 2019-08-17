<?php

	$draftID		= Configure::read('__Site.PropertyDraft.id');
	$savePath		= Configure::read('__Site.property_photo_folder');
	$companyData	= Configure::read('Config.Company.data');
	$isHideAddress	= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_hidden_address_property');
	$isHideMap		= Common::hashEmptyField($companyData, 'UserCompanyConfig.is_hidden_map');

	$_global_variable	= empty($_global_variable) ? array() : $_global_variable;
	$propertyActions	= empty($propertyActions) ? array() : $propertyActions;
	$propertyTypes		= empty($propertyTypes) ? array() : $propertyTypes;
	$lotUnits			= empty($lotUnits) ? array() : $lotUnits;
	$currencies			= empty($currencies) ? array() : $currencies;
	$facilities			= empty($facilities) ? array() : $facilities;
	$periods			= empty($periods) ? array() : $periods;

	$recordID		= Common::hashEmptyField($this->data, 'Property.id');
	$sessionID		= Common::hashEmptyField($this->data, 'Property.session_id');
	$actionID		= Common::hashEmptyField($this->data, 'Property.property_action_id');
	$typeID			= Common::hashEmptyField($this->data, 'Property.property_type_id');
	$currencyID		= Common::hashEmptyField($this->data, 'PropertyPrice.currency_id');
	$title			= Common::hashEmptyField($this->data, 'Property.title');
	$photo			= Common::hashEmptyField($this->data, 'Property.photo');
	$price			= Common::hashEmptyField($this->data, 'Property.price', 0);
	$commission		= Common::hashEmptyField($this->data, 'Property.commission', 0);
	$description	= Common::hashEmptyField($this->data, 'Property.description');
	$created		= Common::hashEmptyField($this->data, 'Property.created');
	$modified		= Common::hashEmptyField($this->data, 'Property.modified');

	$customCreated	= $this->Rumahku->formatDate($modified, 'F Y');
	$customModified	= $this->Rumahku->formatDate($modified, 'F Y');

	$regionID		= Common::hashEmptyField($this->data, 'PropertyAddress.Region.id');
	$regionName		= Common::hashEmptyField($this->data, 'PropertyAddress.Region.name');
	$cityID			= Common::hashEmptyField($this->data, 'PropertyAddress.City.id');
	$cityName		= Common::hashEmptyField($this->data, 'PropertyAddress.City.name');
	$subareaID		= Common::hashEmptyField($this->data, 'PropertyAddress.Subarea.id');
	$subareaName	= Common::hashEmptyField($this->data, 'PropertyAddress.Subarea.name');

	$address		= Common::hashEmptyField($this->data, 'PropertyAddress.address');
	$addressNo		= Common::hashEmptyField($this->data, 'PropertyAddress.no');
	$addressRT		= Common::hashEmptyField($this->data, 'PropertyAddress.rt');
	$addressRW		= Common::hashEmptyField($this->data, 'PropertyAddress.rw');
	$addressZIP		= Common::hashEmptyField($this->data, 'PropertyAddress.zip');

	$actionList	= Hash::combine($propertyActions, '{n}.PropertyAction.id', '{n}.PropertyAction.name');
	$typeList	= Hash::combine($propertyTypes, '{n}.PropertyType.id', '{n}.PropertyType.name');

	$actionName	= Common::hashEmptyField($actionList, $actionID);
	$typeName	= Common::hashEmptyField($typeList, $typeID);

	echo($this->Form->create('Property', array(
		'id'			=> 'sell-property', 
	//	'class'			=> 'form-horizontal', 
		'inputDefaults' => array(
			'div'		=> false, 
			'required'	=> false, 
		),
	)));

	$backURL = $this->Html->url(array(
		'admin'			=> true, 
		'controller'	=> 'properties', 
		'action'		=> 'index', 
	), true);

	$baseApiURL = array(
		'plugin'		=> false, 
		'api'			=> true, 
		'controller'	=> 'api_properties', 
		'action'		=> 'master_data', 
	);

//	echo($this->Html->link(__('%s Hapus Foto', $this->Rumahku->icon('rv4-cross')), array(
//		'admin'			=> false,
//		'controller'	=> 'ajax', 
//		'action'		=> 'property_photo_delete',
//		'draft'			=> $draftID,
//		$sessionID,
//		$recordID, 
//	), array(
//		'escape'				=> false,
//		'class'					=> 'btn red fly-button-media ajax-link',
//		'data-form'				=> '#fileupload',
//		'data-alert'			=> __('Anda yakin ingin menghapus foto ini?'),
//		'data-action'			=> 'reset-file-upload',
//		'data-wrapper-write'	=> '.wrapper-upload-medias',
//		'style'					=> 'z-index: 10000; display: none;', 
//	)));

?>
<div class="box box-success">
	<div class="box-header with-border hidden-print">
		<div class="action-group">
			<div class="btn-group floright hidden-print">
				<?php

					echo($this->Html->link(__('Kembali'), $backURL, array(
						'escape'	=> false, 
						'class'		=> 'btn default', 
					)));

					echo($this->Html->link(__('Simpan'), 'javascript:void(0);', array(
						'escape'		=> false,
						'class'			=> 'btn green', 
						'data-role'		=> 'editable-submit', 
						'data-target'	=> 'form#sell-property', 
					)));

				?>
			</div>
		</div>
	</div>
	<div class="box-body">
		<div class="row" id="user-detail">
			<div class="col-xs-12 col-md-8">
				<div class="row">
					<div class="col-md-3">
						<?php

							$photo = $this->Rumahku->photo_thumbnail(array(
								'save_path'	=> $savePath, 
								'src'		=> $photo, 
								'size'		=> 'm',
							), array(
								'alt'	=> 'property-thumbnail',
								'class'	=> 'img-responsive property-thumb', 
							));

							echo($this->Html->tag('div', $photo, array(
								'class' => 'property-primary-photo mb20', 
							)));

							$mediaTitle	= __('Media Properti');
							$mediaURL	= $this->Html->url(array(
								'admin'			=> true, 
								'controller'	=> 'properties', 
								'action'		=> 'easy_media', 
								$recordID, 
								'photo',
							), true);

							echo($this->Html->link($mediaTitle, $mediaURL, array(
								'title'		=> $mediaTitle, 
								'data-size'	=> 'modal-fluid', 
								'class'		=> 'btn green ajaxModal mb20', 
								'escape'	=> false,
							)));

						?>
					</div>
					<div class="col-md-9">
						<?php

							$editableInput = $this->Html->link($title, '#', array(
								'data-value'		=> $title, 
								'data-name'			=> 'data[Property][title]', 
								'data-type'			=> 'text', 
								'data-mode'			=> 'inline', 
								'data-placeholder'	=> __('Masukkan kalimat promosi Anda disini'), 
								'class'				=> 'editable editable-fullwidth editable-click', 
								'data-tpl'			=> '<input type="text" class="form-control input-sm" maxlength="60">', 
							));

							echo($this->Html->tag('h2', $editableInput));

							echo($this->element('blocks/properties/forms/easy_mode_price', array(
								'record' => $this->data, 
							)));

						?>
					</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-4">
				<?php

					echo($this->Html->tag('h3', __('Deskripsi'), array(
						'class' => 'custom-heading', 
					)));

				?>
				<div class="row">
					<div class="col-xs-12">
						<?php

							echo($this->Html->link($description, '#', array(
								'data-value'		=> $description, 
								'data-name'			=> 'data[Property][description]', 
								'data-type'			=> 'textarea', 
								'data-mode'			=> 'inline', 
								'data-placeholder'	=> __('Masukkan deskripsi properti Anda disini'), 
								'class'				=> 'editable editable-fullwidth editable-click', 
							)));

						?>
					</div>
				</div>
			</div>
		</div>
		<div class="row bordered-col">
			<div class="col-xs-12 col-md-8">
				<?php

					echo($this->Html->tag('h3', __('Detail Spesifikasi'), array(
						'class' => 'custom-heading', 
					)));

					echo($this->element('blocks/properties/forms/easy_mode_specification', array(
						'record'			=> $this->data, 
						'propertyActions'	=> $propertyActions, 
						'propertyTypes'		=> $propertyTypes, 
					)));

					echo($this->Html->tag('h3', __('Detail Alamat'), array(
						'class' => 'custom-heading', 
					)));

				?>
				<div class="row">
					<?php /*
					<div class="col-sm-12">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 col-sm-3 no-pright">
									<?php

										echo($this->Html->tag('label', __('Alamat Properti *'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 col-sm-9 relative no-pleft">
									<?php

										$addressValue = array(
											'address'	=> $address ?: '', 
											'no'		=> $addressNo ?: '',  
											'rt'		=> $addressRT ?: '', 
											'rw'		=> $addressRW ?: '', 
										);

										$address = implode(' ', array_filter(array(
											$address, 
											$addressNo ? sprintf('No. %s', $addressNo) : false, 
											$addressRT ? sprintf('RT. %s', $addressRT) : false, 
											$addressRW ? sprintf('RW. %s', $addressRW) : false, 
										)));

										echo($this->Html->link($address, '#', array(
											'data-value'	=> str_replace('"', '\'', json_encode($addressValue)), 
											'data-name'		=> 'data[PropertyAddress]', 
											'data-type'		=> 'prime_address', 
											'data-mode'		=> 'inline', 
											'class'			=> 'editable editable-fullwidth editable-click', 
										)));

									?>
								</div>
							</div>
						</div>
					</div>
					*/ ?>
					<div class="col-md-12">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 col-sm-3 no-pright">
									<?php

										echo($this->Html->tag('label', __('Alamat Properti *'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 col-sm-9 relative no-pleft">
									<?php

										echo($this->Html->link($address, '#', array(
											'data-value'		=> $address, 
											'data-name'			=> 'data[PropertyAddress][address]', 
											'data-type'			=> 'text', 
											'data-mode'			=> 'inline', 
											'data-placeholder'	=> __('Masukkan alamat properti Anda disini'), 
											'class'				=> 'editable editable-fullwidth editable-click', 
											'data-tpl'			=> '<input type="text" class="form-control input-sm">', 
										)));

									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 no-pright">
									<?php

										$mandatory = $this->Rumahku->_callLblConfigValue('is_mandatory_no_address', '*');

										echo($this->Html->tag('label', __('No %s', $mandatory), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 relative no-pleft">
									<?php

										echo($this->Html->link($addressNo, '#', array(
											'data-value'		=> $addressNo, 
											'data-name'			=> 'data[PropertyAddress][no]', 
											'data-type'			=> 'text', 
											'data-mode'			=> 'inline', 
											'class'				=> 'editable editable-fullwidth editable-click', 
											'data-tpl'			=> '<input type="text" class="form-control input-sm">', 
										)));

									?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-6">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 no-pright">
									<?php

										echo($this->Html->tag('label', __('RT'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 relative no-pleft">
									<?php

										echo($this->Html->link($addressRT, '#', array(
											'data-value'		=> $addressRT, 
											'data-name'			=> 'data[PropertyAddress][rt]', 
											'data-type'			=> 'text', 
											'data-mode'			=> 'inline', 
											'class'				=> 'editable editable-fullwidth editable-click', 
											'data-tpl'			=> '<input type="text" class="form-control input-sm">', 
										)));

									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-6">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 no-pright">
									<?php

										echo($this->Html->tag('label', __('RW'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 relative no-pleft">
									<?php

										echo($this->Html->link($addressRW, '#', array(
											'data-value'		=> $addressRW, 
											'data-name'			=> 'data[PropertyAddress][rw]', 
											'data-type'			=> 'text', 
											'data-mode'			=> 'inline', 
											'class'				=> 'editable editable-fullwidth editable-click', 
											'data-tpl'			=> '<input type="text" class="form-control input-sm">', 
										)));

									?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="row locations-trigger">
					<?php /*
					<div class="col-md-12">
						<?php

							echo($this->element('blocks/properties/forms/location_picker', array(
								'options' => array(
									'frameClass'		=> 'col-sm-12',
									'formGroupClass'	=> 'form-group no-margin', 
									'labelClass'		=> 'col-xs-12 col-md-3 no-pright',
									'class'				=> 'col-xs-6 col-sm-9 relative no-pleft',
									'inputClass'		=> 'input-sm', 
								), 
							)));

						?>
					</div>
					*/ ?>
					<?php /*
					<div class="col-md-12">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-12 col-md-3 no-pright">
									<?php

										echo($this->Html->tag('label', __('Area Properti'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-12 col-md-9 relative no-pleft no-pright">
									<?php

										$addressLocation = array(
											'region_id'		=> $regionID, 
											'region_name'	=> $regionName, 
											'city_id'		=> $cityID, 
											'city_name'		=> $cityName, 
											'subarea_id'	=> $subareaID, 
											'subarea_name'	=> $subareaName, 
											'zip'			=> $addressZIP, 
										);

										$areaName = array_filter(array($subareaName, $cityName, $regionName));
										$areaName = implode(', ', $areaName);

										if($addressZIP){
											$areaName.= '. ' . $addressZIP;
										}

										echo($this->Form->hidden(false, array(
											'id'	=> 'currRegionID', 
											'value'	=> $regionID, 
										)));

										echo($this->Form->hidden(false, array(
											'id'	=> 'currCityID', 
											'value'	=> $cityID, 
										)));

									//	$subareas = empty($subareas) ? array() : $subareas;
									//	echo($this->Html->link($areaName, '#', array(
									//		'data-value'	=> str_replace('"', '\'', json_encode($addressLocation)), 
									//		'data-name'		=> 'data[Editable][PropertyAddress]', 
									//		'data-type'		=> 'prime_location', 
									//		'data-mode'		=> 'inline', 
									//		'data-source'	=> str_replace('"', '\'', json_encode($subareas)), 
									//		'class'			=> 'editable editable-fullwidth editable-click', 
									//	)));

									?>
								</div>
							</div>
						</div>
					</div>
					*/ ?>
					<?php

						echo($this->Html->tag('div', $this->element('blocks/properties/forms/location_picker', array(
							'options'	=> array(
								'mandatory'			=> true, 
								'model'				=> 'PropertyAddress', 
								'frameClass'		=> 'col-sm-12',
								'labelClass'		=> 'col-xs-6 col-md-3 no-pright',
								'class'				=> 'col-xs-6 col-md-9 relative no-pleft',
								'inputClass'		=> 'location-picker input-sm', 
								'formGroupClass'	=> 'form-group no-margin', 
							), 
						)), array(
							'class' => 'col-sm-12', 
						)));

					?>
					<?php /*
					<div class="col-sm-12">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 col-md-3 no-pright">
									<?php

										echo($this->Rumahku->setFormAddress('PropertyAddress'));
										echo($this->Html->tag('label', __('Provinsi *'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 col-md-9 relative no-pleft">
									<?php

										echo($this->Form->input('PropertyAddress.region_id', array(
											'id'	=> 'regionId', 
											'type'	=> 'select', 
											'class'	=> 'form-control input-sm regionId', 
											'label'	=> false, 
											'div'	=> false, 
										)));

									?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 col-md-3 no-pright">
									<?php

										echo($this->Html->tag('label', __('Kota *'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 col-md-9 relative no-pleft">
									<?php

										echo($this->Form->input('PropertyAddress.city_id', array(
											'id'	=> 'cityId', 
											'type'	=> 'select', 
											'class'	=> 'form-control input-sm cityId', 
											'label'	=> false, 
											'div'	=> false, 
										)));

									?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 col-md-3 no-pright">
									<?php

										echo($this->Html->tag('label', __('Area *'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 col-md-9 relative no-pleft">
									<?php

										echo($this->Form->input('PropertyAddress.subarea_id', array(
											'id'		=> 'subareaId', 
											'class'		=> 'form-control input-sm subareaId', 
											'options'	=> $subareas, 
											'label'	=> false, 
											'div'	=> false, 
										)));

									?>
								</div>
							</div>
						</div>
					</div>
					*/?>
					<div class="col-sm-12">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 col-md-3 no-pright">
									<?php

										echo($this->Html->tag('label', __('Kode Pos *'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 col-md-9 relative no-pleft">
									<?php

										echo($this->Form->input('PropertyAddress.zip', array(
											'type'	=> 'text', 
											'class'	=> 'form-control input-sm rku-zip', 
											'label'	=> false, 
											'div'	=> false, 
										)));

									?>
								</div>
							</div>
						</div>
					</div>

					<?php if(empty($isHideAddress)): ?>
					<div class="col-sm-6">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 no-pright">
									<?php

										echo($this->Html->tag('label', __('Sembunyikan Alamat'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 relative no-pleft">
									<?php

									//	$hideOptions = array(31 => __('Tidak'), 32 => __('Ya'));
										$hideAddress = Common::hashEmptyField($this->data, 'PropertyAddress.hide_address', 0);

										echo($this->Rumahku->checkbox('PropertyAddress.hide_address', array(
											'mt'			=> 'mt10',
											'class'			=> 'handle-toggle-content',
											'data-target'	=> '.commision-cobroke-box', 
											'checked'		=> $hideAddress, 
										)));

									//	echo($this->Form->input('PropertyAddress.hide_address', array(
									//		'value'		=> 1, 
									//		'checked'	=> $hideAddress, 
									//		'div'		=> false, 
									//		'label'		=> false, 
									//		'style'		=> 'margin:12px 0;width:auto;', 
									//	)));

									//	echo($this->Html->link(false, '#', array(
									//		'data-value'	=> $actionID, 
									//		'data-name'		=> 'data[PropertyAddress][hide_address]', 
									//		'data-type'		=> 'select', 
									//		'data-mode'		=> 'inline', 
									//		'data-value'	=> $hideAddress, 
									//		'data-source'	=> str_replace('"', '\'', json_encode($hideOptions)), 
									//		'class'			=> 'editable editable-fullwidth editable-click', 
									//	)));

									?>
								</div>
							</div>
						</div>
					</div>
					<?php endif; ?>

					<?php
							/*
							Sementara
							if(empty($isHideMap)):
					?>
					<div class="col-sm-6">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-6 no-pright">
									<?php

										echo($this->Html->tag('label', __('Sembunyikan Peta'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-6 relative no-pleft">
									<?php

										$hideMap = Common::hashEmptyField($this->data, 'PropertyAddress.hide_map', 0);

										echo($this->Rumahku->checkbox('PropertyAddress.hide_map', array(
											'mt'			=> 'mt10',
											'class'			=> 'handle-toggle-content',
											'data-target'	=> '.commision-cobroke-box', 
											'checked'		=> $hideMap, 
										)));

									//	echo($this->Form->input('PropertyAddress.hide_map', array(
									//		'value'		=> 1, 
									//		'checked'	=> $hideMap, 
									//		'div'		=> false, 
									//		'label'		=> false, 
									//		'style'		=> 'margin:12px 0;width:auto;', 
									//	)));

									//	echo($this->Html->link(false, '#', array(
									//		'data-value'	=> $actionID, 
									//		'data-name'		=> 'data[PropertyAddress][hide_map]', 
									//		'data-type'		=> 'select', 
									//		'data-mode'		=> 'inline', 
									//		'data-value'	=> $hideMap, 
									//		'data-source'	=> str_replace('"', '\'', json_encode($hideOptions)), 
									//		'class'			=> 'editable editable-fullwidth editable-click', 
									//	)));

									?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group no-margin">
							<div class="row">
								<div class="col-xs-12">
									<?php

										echo($this->Html->tag('label', __('Peta Lokasi'), array(
											'class' => 'control-label', 
										)));

									?>
								</div>
								<div class="col-xs-12">
									<div id="map_container">
										<div id="gmap-rku"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php
							endif;
							*/
					?>
				</div>
			</div>
			<div class="col-xs-12 col-md-4">
				<div class="row mb20">
					<div class="col-xs-12">
						<?php

							echo($this->Html->tag('h3', __('Detail Nilai Lebih'), array(
								'class' => 'custom-heading', 
							)));

							echo($this->element('blocks/properties/forms/easy_mode_point_plus', array(
								'record' => $this->data, 
							)));

						?>
					</div>
				</div>
				<div class="row mb20">
					<div class="col-xs-12">
						<?php

							echo($this->Html->tag('h3', __('Detail Fasilitas'), array(
								'class' => 'custom-heading', 
							)));

							echo($this->element('blocks/properties/forms/easy_mode_facility', array(
								'record' => $this->data, 
							)));

						?>
					</div>
				</div>
				<div class="row mb20">
					<div class="col-xs-12">
						<?php

							echo($this->Html->tag('h3', __('Meta Tag SEO (Optional)'), array(
								'class' => 'custom-heading', 
							)));

						?>
						<div class="row">
							<div class="col-xs-12">
								<div class="form-group">
									<div class="row">
										<div class="col-xs-12">
											<?php

												echo($this->Html->tag('label', __('Judul Meta'), array(
													'class' => 'control-label', 
												)));

											?>
										</div>
										<div class="col-xs-12">
											<?php

												$metaTitle = Common::hashEmptyField($this->data, 'PageConfig.meta_title', '');

												echo($this->Html->link($metaTitle, '#', array(
													'data-value'		=> $metaTitle, 
													'data-name'			=> 'data[PageConfig][meta_title]', 
													'data-type'			=> 'text', 
													'data-mode'			=> 'inline', 
													'data-placeholder'	=> __('Masukkan Judul Meta disini'), 
													'class'				=> 'editable editable-fullwidth editable-click', 
												)));

											?>
										</div>
									</div>
								</div>
								<div class="form-group">
									<div class="row">
										<div class="col-xs-12">
											<?php

												echo($this->Html->tag('label', __('Deskripsi Meta'), array(
													'class' => 'control-label', 
												)));

											?>
										</div>
										<div class="col-xs-12">
											<?php

												$metaDescription = Common::hashEmptyField($this->data, 'PageConfig.meta_description', '');

												echo($this->Html->link($metaDescription, '#', array(
													'data-value'		=> $metaDescription, 
													'data-name'			=> 'data[PageConfig][meta_description]', 
													'data-type'			=> 'textarea', 
													'data-mode'			=> 'inline', 
													'data-placeholder'	=> __('Masukkan Deskripsi Meta disini'), 
													'class'				=> 'editable editable-fullwidth editable-click', 
												)));

											?>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php /*
				<div class="container-fluid no-pright no-pleft mb20">
					<?php

						echo($this->Html->tag('h3', __('Detail Agen'), array(
							'class' => 'custom-heading', 
						)));

						echo($this->element('blocks/properties/agent_detail', array(
							'record'	=> $this->data, 
							'options'	=> array(
								'show_input' => true, 
							), 
						)));

					?>
				</div>
				*/ ?>
			</div>
		</div>
		<div class="row bordered-col">
			<div class="col-xs-12">
				<div class="row mb20">
					<div class="col-xs-12">
						<?php

							echo($this->Html->tag('h3', __('Detail Agen'), array(
								'class' => 'custom-heading', 
							)));

							$commissionTypes = empty($commissionTypes) ? array() : $commissionTypes;

							echo($this->element('blocks/properties/agent_detail', array(
								'record'	=> $this->data, 
								'options'	=> array(
									'show_profile'	=> false, 
									'show_input'	=> true, 
								), 
								'commissionTypes' => $commissionTypes, 
							)));

						?>
					</div>
				</div>
				<div class="row mb20">
					<div class="col-xs-12">
						<?php

							echo($this->Html->tag('h3', __('Detail Vendor'), array(
								'class' => 'custom-heading mt20', 
							)));

							$message = $this->Html->tag('p', __('Untuk dapat menyimpan informasi Vendor/Client, Email, Nama dan No. HP harus diisi'));
		
							echo($this->Html->tag('div', $message, array(
								'class' => 'info-full alert mb20', 
							)));

							$search = array(
							//	'form-group', 
								'col-xl-2 taright col-sm-3', 
								'relative  col-sm-5 col-xl-4',
								'relative col-sm-8 col-xl-4 cb-custom', 
							//	'form-control', 
							);

							$replace = array(
							//	'form-group mb5', 
								'col-sm-4 col-md-3 no-pright', 
								'col-sm-8 col-md-4 no-pleft', 
								'col-sm-8 col-md-4 cb-custom no-pleft', 
							//	'form-control input-sm', 
							);

							$clientForm = $this->element('blocks/properties/forms/input_client', array(
								'ajax_blur'		=> false, 
								'action_type'	=> 'easy_mode', 
							));

							echo(str_replace($search, $replace, $clientForm));

						?>
					</div>
				</div>
				<?php
						echo $this->element('blocks/properties/forms/easy_block_integration');
				?>
			</div>
		</div>
	</div>
	<div class="box-footer hidden-print">
		<div class="row">
			<div class="col-sm-12">
				<div class="action-group">
					<div class="btn-group floright">
						<?php

							echo($this->Html->link(__('Kembali'), $backURL, array(
								'escape'	=> false, 
								'class'		=> 'btn default', 
							)));

							echo($this->Html->link(__('Simpan'), 'javascript:void(0);', array(
								'escape'		=> false,
								'class'			=> 'btn green', 
								'data-role'		=> 'editable-submit', 
								'data-target'	=> 'form#sell-property', 
							)));

						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 

	echo($this->Form->end());

?>