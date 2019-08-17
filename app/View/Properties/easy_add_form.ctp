<?php

	$_global_variable	= empty($_global_variable) ? array() : $_global_variable;
	$propertyActions	= empty($propertyActions) ? array() : $propertyActions;
	$propertyTypes		= empty($propertyTypes) ? array() : $propertyTypes;
	$lotUnits			= empty($lotUnits) ? array() : $lotUnits;
	$currencies			= empty($currencies) ? array() : $currencies;
	$certificates		= empty($certificates) ? array() : $certificates;
	$periods			= empty($periods) ? array() : $periods;
	$subareas			= empty($subareas) ? array() : $subareas;

	$recordID	= empty($recordID) ? false : $recordID;
	$recordID	= Common::hashEmptyField($this->data, 'Property.id', $recordID);
	$actionID	= Common::hashEmptyField($this->data, 'Property.property_action_id');
	$typeID		= Common::hashEmptyField($this->data, 'Property.property_type_id');
	$currencyID	= Common::hashEmptyField($this->data, 'PropertyPrice.currency_id');
	$isSpace	= Common::hashEmptyField($this->data, 'PropertyType.is_space');

	$subareas = empty($subareas) ? array() : $subareas;

	$this->request->data = array_replace_recursive($this->request->data, array(
		'Property' => array(
			'property_action_id'	=> $actionID, 
			'property_type_id'		=> $typeID, 
		), 
	));

	echo($this->Html->tag('h2', __('Informasi Dasar'), array(
		'class' => 'sub-heading'
	)));

?>
<div class="container-fluid">
	<?php

	//	upload image
		echo($this->element('blocks/properties/forms/single_image_upload', array(
			'record' => $this->data, 
		)));

	?>
</div>
<?php

	echo($this->Form->create('Property', array(
		'id'			=> 'sell-property', 
		'class'			=> 'form-horizontal', 
		'inputDefaults' => array(
			'div'		=> false, 
			'required'	=> false, 
		),
	)));

	echo($this->Form->hidden('Property.id', array('value' => $recordID)));
	echo($this->Form->hidden('Property.session_id'));

	?>
	<div class="container-fluid">
		<div class="row">
			<?php

				$dataMatch = str_replace('"', "'", json_encode(array(
					array('#sell-price-placeholder', array('1'), 'slide'), 
					array('#rent-price-placeholder', array('2'), 'slide'), 
				)));

				$options = Hash::combine($propertyActions, '{n}.PropertyAction.id', '{n}.PropertyAction.name');

				echo($this->Rumahku->buildForm('Property.property_action_id', __('Status Properti *'), array(
					'type'			=> 'radio',
					'frame-size'	=> 'large',
					'options'		=> $options,
					'value'			=> $actionID,
					'inputOptions'	=> array(
						'class'					=> 'form-control ajax-change info-radio-id', 
						'data-wrapper-write'	=> '#dynamic-input', 
						'data-form'				=> 'form#sell-property', 
					), 
				), 'horizontal'));

				$options = Hash::combine($propertyTypes, '{n}.PropertyType.id', '{n}.PropertyType.name');

				echo($this->Rumahku->buildForm('Property.property_type_id', __('Jenis Properti *'), array(
					'type'			=> 'radio',
					'frame-size'	=> 'large',
					'options'		=> $options,
					'value'			=> $typeID,
					'inputOptions'	=> array(
						'class'					=> 'form-control ajax-change info-radio-id', 
						'data-wrapper-write'	=> '#dynamic-input', 
						'data-form'				=> 'form#sell-property', 
					), 
				), 'horizontal'));

			?>
		</div>
	</div>
	<div id="dynamic-input" class="container-fluid">
		<div class="row">
			<?php

				$currencyList = Hash::combine($currencies, '{n}.Currency.id', '{n}.Currency.symbol');

				if($actionID == 1){

					?>
					<div id="sell-price-placeholder" class="form-group">
						<div class="row">
							<div class="col-sm-12">
								<div class="row">
									<?php

										echo $this->Html->tag('div', $this->Form->label('price', __('Harga *'), array(
											'class' => 'control-label',
										)), array(
											'class' => 'col-xl-2 taright col-sm-3',
										));

									?>
									<div class="relative col-sm-7 col-xl-4">
										<div class="input-group no-margin">
											<?php 

												echo($this->Form->input('Property.currency_id', array(
													'id'		=> 'currency',
													'class'		=> 'input-group-addon',
													'label'		=> false,
													'options'	=> $currencyList, 
												)));

												echo($this->Form->input('Property.price', array(
													'id'		=> 'price',
													'type'		=> 'text',
													'class'		=> 'form-control has-side-control at-left input_price',
													'label'		=> false,
													'error'		=> false,
												)));
											?>
										</div>
										<?php echo($this->Form->error('Property.price')); ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php

				}
				else{

					?>
					<div id="rent-price-placeholder" class="price-list relative form-added">
						<ul>
							<?php

								$inputLength	= $currencyID ? count($currencyID) : 1;
								$periodList		= Hash::combine($periods, '{n}.Period.id', '{n}.Period.name');

								for($index = 0; $index < $inputLength; $index++){
									echo($this->element('blocks/properties/forms/price_items', array(
										'idx'			=> $index,
										'currencies'	=> $currencyList, 
										'periods'		=> $periodList, 
										'options'		=> array(
											'wrapper_class'		=> 'col-sm-12', 
											'frame_label_class'	=> 'col-xl-2 taright col-sm-3', 
											'frame_input_class'	=> 'relative col-sm-7 col-xl-4', 
										)
									)));
								}

							?>
						</ul>
						<div class="row">
							<div class="col-sm-12">
								<div class="col-sm-offset-3 col-sm-6">
									<div class="form-group">
										<?php 

											$contentLink = $this->Html->tag('span', $this->Rumahku->icon('rv4-bold-plus'), array(
												'class' => 'btn dark small-fixed',
											));

											$contentLink.= $this->Html->tag('span', __('Tambah Harga per Periode'));

											echo($this->Html->link($contentLink, '#', array(
												'escape'	=> false,
												'role'		=> 'button',
												'class'		=> 'field-added',
											)));

										?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php

				}

			?>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-12">
						<div class="row">
							<?php

								echo($this->Html->tag('div', $this->Form->label('price', __('Satuan *'), array(
									'class' => 'control-label',
								)), array(
									'class' => 'col-xl-2 taright col-sm-3',
								)));

							?>
							<div class="relative col-sm-7 col-xl-4">
								<?php 

									$options = Hash::combine($lotUnits, '{n}.LotUnit.id', sprintf('{n}.LotUnit.%s', $isSpace ? 'name' : 'slug'));

									echo($this->Form->input('PropertyAsset.lot_unit_id', array(
										'id'		=> 'lot-unit-id', 
										'class'		=> 'form-control',
										'options'	=> $options, 
										'empty'		=> __('Pilih'), 
										'label'		=> false,
									)));

								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php

				$propertyType	= Set::extract(sprintf('/PropertyType[id=%s]', $typeID), $propertyTypes);
				$propertyType	= array_shift($propertyType);

				if($propertyType){
					$isLot			= Common::hashEmptyField($propertyType, 'PropertyType.is_lot');
					$isResidence	= Common::hashEmptyField($propertyType, 'PropertyType.is_residence');
					$isBuilding		= Common::hashEmptyField($propertyType, 'PropertyType.is_building');
					$isSpace		= Common::hashEmptyField($propertyType, 'PropertyType.is_space');

					$certificateList	= Hash::combine($certificates, '{n}.Certificate.id', '{n}.Certificate.name_id');
					$lotSize			= Common::hashEmptyField($this->request->data, 'PropertyAsset.lot_size');
					$specifications[]	= array(
						'label'		=> __('Sertifikat'), 
						'field'		=> 'Property.certificate_id',
						'mandatory'	=> true, 
					);

					if($isLot){
						$lotSize			= Common::hashEmptyField($this->request->data, 'PropertyAsset.lot_size');
						$specifications[]	= array(
							'label'		=> __('Luas Tanah'), 
							'field'		=> 'PropertyAsset.lot_size', 
							'mandatory'	=> true, 
						);
					}

					if($isBuilding){
						$buildingSize		= Common::hashEmptyField($this->request->data, 'PropertyAsset.building_size');
						$specifications[]	= array(
							'label'		=> __('Luas Bangunan'), 
							'field'		=> 'PropertyAsset.building_size', 
							'mandatory'	=> true, 
						);
					}

					if($isResidence){
						$beds		= Common::hashEmptyField($this->request->data, 'PropertyAsset.beds');
						$bedsMaid	= Common::hashEmptyField($this->request->data, 'PropertyAsset.beds_maid');
						$baths		= Common::hashEmptyField($this->request->data, 'PropertyAsset.baths');
						$bathsMaid	= Common::hashEmptyField($this->request->data, 'PropertyAsset.baths_maid');

						$specifications[] = array(
							'label'		=> __('Kamar Tidur'),
							'field'		=> 'PropertyAsset.beds', 
							'mandatory'	=> true, 
							'increment'	=> true, 
						);

						$specifications[] = array(
							'label'		=> __('Kamar Mandi'),
							'field'		=> 'PropertyAsset.baths', 
							'mandatory'	=> true, 
							'increment'	=> true, 
						);
					}

					foreach($specifications as $specification){
						$label 		= Common::hashEmptyField($specification, 'label');
						$field		= Common::hashEmptyField($specification, 'field');
						$increment	= Common::hashEmptyField($specification, 'increment');
						$mandatory	= Common::hashEmptyField($specification, 'mandatory');
						$mandatory	= $mandatory ? '*' : false;

						if($field == 'Property.certificate_id'){
							$dataMatch			= str_replace('"', "'", json_encode(array(array('.other-text', array('-1'), 'slide'))));
							$certificateList	= Hash::insert($certificateList, '-1', __('Lainnya'));

							?>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-12">
										<div class="row">
											<?php 

												echo($this->Html->tag('div', $this->Form->label($field, __('%s %s', $label, $mandatory), array(
													'class' => 'control-label',
												)), array(
													'class' => 'col-xl-2 taright col-sm-3',
												)));

											?>
											<div class="relative col-sm-3">
												<?php 

													echo($this->Form->input($field, array(
														'empty'			=> __('Pilih Sertifikat'), 
														'class'			=> 'form-control handle-toggle', 
														'data-match'	=> $dataMatch, 
														'options'		=> $certificateList, 
														'label'			=> false, 
													)));

												?>
											</div>
											<div class="relative col-sm-4 no-padding-left">
												<?php 

													echo($this->Form->input('Property.others_certificate', array(
														'placeholder'	=> __('Sertifikat Lainnya'),
														'class'			=> 'form-control',
														'div'			=> 'other-text', 
														'label'			=> false,
													)));

												?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php

						}
						else{

							?>
							<div class="form-group">
								<div class="row">
									<div class="col-sm-12">
										<div class="row">
											<?php


												echo($this->Html->tag('div', $this->Form->label('price', __('%s %s', $label, $mandatory), array(
													'class' => 'control-label',
												)), array(
													'class' => 'col-xl-2 taright col-sm-3',
												)));

											?>
											<div class="relative col-sm-3 col-xl-4">
												<?php

													$class = 'form-control input_number has-side-control at-right';
													$input = array();

													if($increment){
														$class = 'form-control input_number has-side-control at-bothway tmp-increment';

														$input[] = $this->Html->link($this->Rumahku->icon('rv4-bold-min mr0 fs085'), 'javascript:void(0);', array(
															'role'			=> 'button',
															'class'			=> 'input-group-addon at-left op-min',
															'data-action'	=> 'min',
															'data-target'	=> 'tmp-increment',
															'escape'		=> false,
														));
													}

													$input[] = $this->Form->input($field, array(
														'placeholder'	=> __($label), 
														'type'			=> 'text', 
														'class'			=> $class, 
														'label'			=> false, 
														'error'			=> false, 
													));

													if($increment){
														$input[] = $this->Html->link($this->Rumahku->icon('rv4-bold-plus mr0 fs085'), 'javascript:void(0);', array(
															'role'			=> 'button',
															'class'			=> 'input-group-addon at-right op-plus',
															'data-action'	=> 'plus',
															'data-target'	=> 'tmp-increment',
															'escape'		=> false,
														));
													}
													else{
														$input[] = $this->Html->div('input-group-addon at-right lot-unit', '&nbsp;');
													}

													echo($this->Html->div('input-group increment mb0', implode('', $input)));
													echo($this->Form->error($field, null, array(
														'class' => 'error-message', 
													)));

												?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php

						}
					}
				}

			?>
		</div>
	</div>
	<?php

		echo($this->Html->tag('h2', __('Alamat'), array(
			'class' => 'sub-heading'
		)));

	?>
	<div class="container-fluid">
		<div class="row locations-trigger">
			<div class="form-group">
				<div class="row">
					<div class="col-sm-12">
						<div class="row">
							<?php 

								echo $this->Html->tag('div', $this->Form->label('address', __('Alamat *'), array(
									'class' => 'control-label',
								)), array(
									'class' => 'col-xl-2 taright col-sm-3',
								));

							?>
							<div class="relative col-sm-7 col-xl-4">
								<?php 

									echo $this->Form->input('PropertyAddress.address', array(
										'placeholder'	=> __('Nama jalan'),
										'id'			=> 'rku-address',
										'class'			=> 'form-control',
										'label'			=> false,
									));

								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php 

			//  https://basecamp.com/1789306/projects/10415456/todos/359349920 - [EN] - Area dibuat autocomplete
				echo($this->Html->tag('div', $this->element('blocks/properties/forms/location_picker', array(
					'options' => array(
						'frameClass'	=> 'col-sm-12',
						'labelClass'	=> 'col-xl-2 taright col-sm-3',
						'class'			=> 'relative col-sm-7 col-xl-4',
					), 
				))));

				echo($this->Rumahku->buildForm('PropertyAddress.zip', __('Kode Pos *'), array(
					'class'				=> 'rku-zip',
					'size'				=> 'small',
					'frame-class'		=> 'col-sm-12',
					'frame-label-class'	=> 'col-xl-2 taright col-sm-3',
				), 'horizontal'));

				/*
				Sementara
			?>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-12">
						<div class="row">
							<?php 

								echo $this->Html->tag('div', $this->Form->label('address', __('Peta Lokasi'), array(
									'class' => 'control-label',
								)), array(
									'class' => 'col-xl-2 taright col-sm-3',
								));

							?>
							<div class="relative col-sm-7 col-xl-4">
								<div id="map_container">
									<div id="gmap-rku"></div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			*/
			?>
		</div>
		<?php

			$elementOptions = array(
				'options' => array(
					'is_editable'	=> false, 
					'show_profile'	=> false, 
					'show_input'	=> true, 
					'title_class'	=> 'sub-heading', 
					'label_class'	=> 'col-xl-2 taright col-sm-3', 
					'value_class'	=> 'relative col-sm-3 col-xl-4', 
				), 
			);

		//	AGENT
			$content = $this->Html->tag('h2', __('Detail Agen'), array('class' => 'sub-heading'));
			$content.= $this->element('blocks/properties/forms/easy_agent_input', $elementOptions);

			echo($this->Html->tag('div', $content, array('class' => 'row')));

		//	CO BROKE
			$elementOptions = Hash::insert($elementOptions, 'options.value_class', 'relative col-sm-7 col-xl-4');

			$_config		= empty($_config) ? array() : $_config;
			$cfg_isCoBroke	= Common::hashEmptyField($_config, 'UserCompanyConfig.is_co_broke');

			$content = '';

			if($cfg_isCoBroke){
				$content.= $this->Html->tag('h2', __('Detail Co-Broke'), array('class' => 'sub-heading'));
			}     

			$content.= $this->element('blocks/properties/forms/easy_cobroke_input', $elementOptions);

			echo($this->Html->tag('div', $content, array('class' => 'row')));

		//	VENDOR
			$search = array(
				'col-sm-4 col-md-3 no-pright', 
				'relative  col-sm-5 col-xl-4',
				'col-sm-8 col-md-4 no-pleft', 
			);

			$replace = array(
				'col-xl-2 taright col-sm-3', 
				'relative col-sm-3 col-xl-4', 
				'relative col-sm-3 col-xl-4',
			);

			$note = $this->Html->tag('p', __('Untuk dapat menyimpan informasi Vendor/Client, Email, Nama dan No. HP harus diisi'));
			$note = $this->Html->div('row', $this->Html->div('col-xs-12', $note));
			$note = $this->Html->div('form-group', $note);

			$content = $this->Html->tag('h2', __('Informasi Vendor'), array('class' => 'sub-heading'));
			$content.= $note;
			$content.= str_replace($search, $replace, $this->element('blocks/properties/forms/input_client', array(
				'ajax_blur'		=> false, 
				'is_editable'	=> false, 
				'action_type'	=> 'easy_mode', 
			)));

			echo($this->Html->tag('div', $content, array('class' => 'row')));

		?>
		<div class="row">
			<div class="col-sm-12">
				<div class="action-group bottom">
					<div class="btn-group floright">
						<?php

							echo($this->Form->button(__('Simpan'), array(
								'type'	=> 'submit', 
								'class'	=> 'btn blue',
							)));

						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php 

	echo($this->Form->end());

?>