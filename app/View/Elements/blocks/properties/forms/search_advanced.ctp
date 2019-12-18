<?php 
		$data = $this->request->data;
		$_is_advanced_search = isset($_is_advanced_search) ? $_is_advanced_search : 'adv-search';
		$_with_action = isset($_with_action) ? $_with_action : true;
		$_display = isset($_display) ? $_display : 'hide';
		$_datepicker = isset($_datepicker) ? $_datepicker : false;

		$categoryStatus = !empty($categoryStatus)?$categoryStatus:false;
		$propertyTypes = !empty($propertyTypes)?$propertyTypes:false;
		$propertyActions = !empty($propertyActions)?$propertyActions:false;
		$propertyDirections = !empty($propertyDirections)?$propertyDirections:false;
		$certificates = !empty($certificates)?$certificates:false;
        $certificates = $this->Property->_callCertificates($certificates);

        $furnishedOptions = $this->Rumahku->filterEmptyField($_global_variable, 'furnished');
        $roomOptions = $this->Rumahku->filterEmptyField($_global_variable, 'room_options');
        $lotOptions = $this->Rumahku->filterEmptyField($_global_variable, 'lot_options');
        $priceOptions = $this->Rumahku->filterEmptyField($_global_variable, 'price_options');

        $city = $this->Rumahku->filterEmptyField($data, 'Search', 'city');

        $resetUrl = isset($resetUrl) ? $resetUrl : array(
        	'controller' => 'properties',
			'action' => 'index',
            'admin' => true,
        );

        $temp_hide = false;
?>
<?php if ($temp_hide): ?>

<div class="search-box <?php echo $_display; ?> locations-trigger">
	<div class="detail-menu <?php echo $_is_advanced_search ?>">
		<div class="row">
			<div class="col-sm-4">
				<div class="basic">
					<div class="row">
						<?php 
        						echo $this->Rumahku->setFormAddress('Search');
								echo $this->Rumahku->buildInputDropdown('Search.property_action',  array(
					            	'frameClass' => 'col-sm-4 pr7 mb30',
									'label' => __('Status'),
					                'empty' => false,
					                'empty' => __('Semua'),
					                'options' => $propertyActions,
					            ));
					            echo $this->Rumahku->buildInputDropdown('Search.type',  array(
					            	'frameClass' => 'col-sm-8 pl7 mb30',
									'label' => __('Tipe'),
					                'empty' => __('Semua'),
					                'options' => $propertyTypes,
					                '_checkbox' => true,
					            ));
						?>
					</div>
					<div class="row">
						<div class="col-sm-12 mb30 location">
							<?php 

									$emptyText = __('- Semua Provinsi -');
									echo $this->Html->tag('div', $this->Form->input('Search.region', array(
										'class'	=> 'regionId', 
										'label'	=> __('Lokasi'), 
										'empty'	=> $emptyText, 
										'data-empty' => $emptyText, 
										'div'	=> array('class' => 'form-group')
									)), array(
						            	'class' => 'loc-select',
						            ));

									$emptyText = __('- Semua Kota (Pilih Provinsi Dahulu) -');
						            echo $this->Html->tag('div', $this->Rumahku->buildInputForm('Search.city',  array(
						                'type' => 'select',
						            	'inputClass' => 'cityId',
										'label' => false,
						                'empty' => $emptyText,
						                'wrapperClass' => false,
						                'frameClass' => false,
						                'class' => false,
						                'labelClass' => false,
						                'attributes' => array(
						                	'data-empty' => $emptyText,
					                	),
						            )), array(
						            	'class' => 'loc-select',
						            ));
							?>
							<div class="multiple-area">
								<?php 
										if( !empty($city) && !empty($subareas) ) {
											echo $this->element('blocks/common/forms/search/get_list_subareas', array(
												'values' => $subareas,
											));
										} else {
											echo $this->Html->tag('div', $this->Html->link(sprintf(__('- Semua Area (Pilih Kota Dahulu) - %s'), $this->Html->tag('span', $this->Rumahku->icon('rv4-angle-down'))), '#', array(
												'escape' => false,
								            	'class' => 'dropdown-toggle',
								            	'data-toggle' => 'dropdown',
								            	'aria-expanded' => 'false',
								            	'aria-hashpopup' => 'true',
								            )), array(
								            	'class' => 'dropdown-group',
								            ));
										}
								?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="space">
					<div class="row">
						<?php 
								echo $this->Rumahku->buildInputDropdown('Search.beds',  array(
					            	'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Kamar Tidur'),
					                'options' => $roomOptions,
					            ));
					            echo $this->Rumahku->buildInputDropdown('Search.baths',  array(
					            	'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Kamar Mandi'),
					                'options' => $roomOptions,
					            ));
						?>
					</div>
					<div class="row">
						<?php 
								echo $this->Rumahku->buildInputDropdown('Search.lot_size',  array(
					            	'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Luas Tanah'),
					                'options' => $lotOptions,
					            ));
					            echo $this->Rumahku->buildInputDropdown('Search.building_size',  array(
					            	'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Luas Bangunan'),
					                'options' => $lotOptions,
					            ));
						?>
					</div>
					<?php 
							echo $this->Rumahku->buildInputMultiple('Search.lot_width', 'Search.lot_length', array(
				                'label' => sprintf(__('Dimensi (m%s)'), $this->Html->tag('sup', 2)),
				                'frameClass' => 'col-sm-12',
				                'labelDivClass' => 'col-sm-12',
				                'labelClass' => false,
				                'class' => 'col-sm-6 pr7 mb30',
				                'separator' => false,
				                'inputClass' => 'input_number',
				                'inputClass2' => 'input_number',
				                'placeholder1' => __('Lebar'),
				                'placeholder2' => __('Panjang'),
				            ));
					?>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="extra">
					<div class="row">
						<?php
					            echo $this->Rumahku->buildInputDropdown('Search.property_status_id',  array(
					            	'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Status Kategori'),
					                'empty' => __('- Status Kategori -'),
					                'options' => $categoryStatus,
					            ));

								echo $this->Rumahku->buildInputDropdown('Search.price',  array(
					            	'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Harga (Rp)'),
					                'empty' => __('- Range Harga -'),
					                'options' => $priceOptions,
					            ));
						?>
					</div>
					<div class="row">
						<?php 
								echo $this->Rumahku->buildInputDropdown('Search.certificate',  array(
					            	'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Sertifikat'),
					                'empty' => __('Semua'),
					                'options' => $certificates,
					            ));
								echo $this->Rumahku->buildInputDropdown('Search.property_direction',  array(
					            	'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Hadap'),
					                'empty' => __('Semua'),
					                'options' => $propertyDirections,
					            ));
						?>
					</div>
					<div class="row">
						<?php 
								echo $this->Rumahku->buildInputDropdown('Search.condition',  array(
					                'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Kondisi'),
					                'empty' => __('Semua'),
					                'options' => $propertyConditions,
					            ));
								echo $this->Rumahku->buildInputDropdown('Search.furnished',  array(
					            	'frameClass' => 'col-sm-6 pr7 mb30',
									'label' => __('Interior'),
					                'empty' => __('Semua'),
					                'options' => $furnishedOptions,
					            ));
						?>
					</div>
				</div>
			</div>
		</div>
		<?php
				if( !empty($_datepicker) ) {
		?>
		<div class="row">
			<div class="col-sm-12">
				<?php
						echo $this->Rumahku->buildInputMultiple('date_from', 'date_to', array(
				            'label' => __('Range Tanggal'),
				            'labelDivClass' => 'col-sm-2 col-xl-2',
				            'divider' => 'rv4-bold-min small',
				            'inputClass' => 'datepicker',
				            'inputClass2' => 'to-datepicker',
				            'frameClass' => 'col-sm-10',
				            'attributes' => array(
				                'type' => 'text',
				            ),
				        ));
				?>
			</div>
		</div>
		<?php
				}
				
				if( !empty($_with_action) ) {
		?>
		<div class="action-btn">
			<?php 
					echo $this->Html->link(__('Reset'), $resetUrl);
					
					echo $this->Form->button(__('Cari Properti'),  array(
		            	'class' => 'btn blue',
		            ));
			?>
		</div>
		<?php
				}
		?>
	</div>
</div>
	
<?php else: ?>
	
<?php endif ?>