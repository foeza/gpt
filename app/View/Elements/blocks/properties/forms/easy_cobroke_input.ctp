<?php

	$isAdmin		= Configure::read('User.admin');
	$cobrokeTypes 	= Configure::read('__Site.cobroke_type');

	$record 	= empty($record) ? array() : $record;
	$options	= empty($options) ? array() : $options;
	$labelClass	= Common::hashEmptyField($options, 'label_class', 'col-sm-4 col-md-3 no-pright');
	$valueClass	= Common::hashEmptyField($options, 'value_class', 'col-sm-8 col-md-9 no-pleft');

	$showProfile	= Common::hashEmptyField($options, 'show_profile', true, array('isset' => true));
	$showPhoto		= Common::hashEmptyField($options, 'show_photo', true, array('isset' => true));
	$showInput		= Common::hashEmptyField($options, 'show_input');
	$isEditable		= Common::hashEmptyField($options, 'is_editable', true, array('isset' => true));

	if($showInput){
		$User						= empty($User) ? array() : $User;
		$_config					= empty($_config) ? array() : $_config;
		$cfg_isCoBroke				= Common::hashEmptyField($_config, 'UserCompanyConfig.is_co_broke');
		$cfg_isOpenCoBroke			= Common::hashEmptyField($_config, 'UserCompanyConfig.is_open_cobroke');
		$cfg_isBtCommission			= Common::hashEmptyField($_config, 'UserCompanyConfig.is_bt_commission');
		$cfg_isKolistingKoselling	= Common::hashEmptyField($_config, 'UserCompanyConfig.is_kolisting_koselling');

		if($cfg_isCoBroke && $cfg_isOpenCoBroke){
			$defaultAgentCommission				= Common::hashEmptyField($_config, 'UserCompanyConfig.default_agent_commission');
			$defaultCoBrokeCommission			= Common::hashEmptyField($_config, 'UserCompanyConfig.default_co_broke_commision');
			$defaultTypeCoBrokeCommission		= Common::hashEmptyField($_config, 'UserCompanyConfig.default_type_co_broke_commission');
			$defaultTypePriceCoBrokeCommission	= Common::hashEmptyField($_config, 'UserCompanyConfig.default_type_price_co_broke_commision');

			$this->request->data = Hash::insert($this->data, 'Property.is_cobroke', true);
		}
		else{
			$defaultAgentCommission				= false;
			$defaultCoBrokeCommission			= false;
			$defaultTypeCoBrokeCommission		= false;
			$defaultTypePriceCoBrokeCommission	= false;
		}

		$replacementData = array(
			'Property' => array(
				'commission'					=> Common::hashEmptyField($this->data, 'Property.commission', $defaultAgentCommission), 
				'co_broke_commision'			=> Common::hashEmptyField($this->data, 'Property.co_broke_commision', $defaultCoBrokeCommission), 
				'type_co_broke_commission'		=> Common::hashEmptyField($this->data, 'Property.type_co_broke_commission', $defaultTypeCoBrokeCommission), 
				'type_price_co_broke_commision'	=> Common::hashEmptyField($this->data, 'Property.type_price_co_broke_commision', $defaultTypePriceCoBrokeCommission), 
			), 
		);

		$record					= array_replace_recursive($record, $replacementData);
		$this->request->data	= array_replace_recursive($this->data, $replacementData);

	//	property data
		$contractDate	= Common::hashEmptyField($record, 'Property.contract_date', '');
		$commission		= Common::hashEmptyField($record, 'Property.commission', '');
	//	$isCoBroke		= Common::hashEmptyField($record, 'Property.is_cobroke');
		$isCoBroke		= Common::hashEmptyField($this->data, 'Property.is_cobroke');

		$cobroke_note	= Common::hashEmptyField($this->data, 'Property.cobroke_note');

		if($cfg_isCoBroke){
			$message = $this->Html->tag('p', __('Anda bisa dengan mudah menampilkan listing properti Anda di channel Co-Broke dengan hanya klik "%s"', $this->Html->tag('strong', __('Jadikan listing Co-Broke?'))));
			$message.= $this->Html->tag('p', __('%s jadikan listing Co-Broke hanya bisa dilakukan jika Komisi Broker lebih dari 0', $this->Html->tag('strong', __('Catatan :'))));
	
			echo($this->Html->tag('div', $message, array(
				'class' => 'info-full alert mb20', 
			)));

			$checkClass = $cfg_isOpenCoBroke ? 'hide' : '';

			?>
			<div class="form-group <?php echo($checkClass); ?>">
				<div class="row">
					<div class="<?php echo($labelClass); ?>">
						<?php

							echo($this->Html->tag('label', __('Jadikan Listing Co-Broke?'), array(
								'class' => 'control-label', 
							)));

						?>
					</div>
					<div class="<?php echo($valueClass); ?>">
						<?php

						//	edit by iksan karena adanya perubahan ketika cobroke sedang berlangsung
							$approveCobroke			= Common::hashEmptyField($record, 'CoBrokeProperty.approve');
							$toggleCobrokeOption	= array(
								'mt'			=> 'mt10',
								'class'			=> 'handle-toggle-content',
								'data-target'	=> '.commision-cobroke-box', 
								'checked'		=> $isCoBroke, 
							);

							if(!empty($isCoBroke) && !empty($approveCobroke)){
								$toggleCobrokeOption['class'] .= ' false-alert';
								$toggleCobrokeOption['data-alert'] = __('Apakah Anda yakin ingin menghentikan Co Broke yang sedang berlangsung?');
							}

							echo($this->Rumahku->checkbox('Property.is_cobroke', $toggleCobrokeOption));

						?>
					</div>
				</div>
			</div>
			<div class="form-group commision-cobroke-box" style="<?php echo($isCoBroke ? '' : 'display: none;'); ?>">
				<div class="row">
					<div class="<?php echo($labelClass); ?>">
						<?php

							$label	= 'Bagaimana cara perhitungan komisi Co-Broke ?';
							$notice	= 'Komisi broker akan di hitung setelah perhitungan komisi dari agen terhadap perusahaan. Rumus : total komisi agen x persentase komisi broker';
							$notice	= $this->Rumahku->popover($label, $notice);

							echo($this->Html->tag('label', __('Komisi Broker *') . $notice, array(
								'class' => 'control-label', 
							)));

						?>
					</div>
					<div class="<?php echo($valueClass); ?>">
						<?php

							$commissionType		= Common::hashEmptyField($record, 'Property.type_price_co_broke_commision', 'percentage');
							$commissionValue	= Common::hashEmptyField($record, 'Property.co_broke_commision', '');
							$typeOptions		= array(
								'percentage'	=> __('Persentase'),
								'nominal'		=> __('Nominal'), 
							);

							if($isEditable){
								echo($this->Html->link('', '#', array(
									'data-value'	=> $commissionType, 
									'data-name'		=> 'data[Property][type_price_co_broke_commision]', 
									'data-type'		=> 'select', 
									'data-mode'		=> 'inline', 
									'data-source'	=> str_replace('"', '\'', json_encode($typeOptions)), 
									'class'			=> 'editable editable-click', 
								)));

								echo($this->Html->link($commissionValue, '#', array(
									'data-value'	=> $commissionValue, 
									'data-name'		=> 'data[Property][co_broke_commision]', 
									'data-type'		=> 'text', 
									'data-mode'		=> 'inline', 
									'class'			=> 'editable editable-click', 
									'data-tpl'		=> '<input type="text" class="input_price input_number">', 
								)));
							}
							else{
								$inputs = $this->Form->input('Property.type_price_co_broke_commision', array(
									'class'		=> 'form-control',
									'div'		=> 'col-xs-6 no-pright',
									'label'		=> false,
									'value'		=> $commissionType, 
									'options'	=> $typeOptions, 
									'empty'		=> false,
								));

								$inputs.= $this->Form->input('Property.co_broke_commision', array(
									'type'		=> 'text', 
									'class'		=> 'form-control input_number input_price',
									'div'		=> 'col-xs-6 no-lpad',
									'label'		=> false,
									'value'		=> $commissionValue, 
								));

								echo($this->Html->tag('div', $inputs, array(
									'class' => 'row', 
								)));
							}

						?>
					</div>
				</div>
			</div>
			<div class="form-group commision-cobroke-box" style="<?php echo($isCoBroke ? '' : 'display: none;'); ?>">
				<div class="row">
					<div class="<?php echo($labelClass); ?>">
						<?php

							echo($this->Html->tag('label', __('Asal Komisi Broker *'), array(
								'class' => 'control-label', 
							)));

						?>
					</div>
					<div class="<?php echo($valueClass); ?>">
						<?php

							$commissionTypes = empty($commissionTypes) ? array() : $commissionTypes;

							$inputValue	= array_keys($commissionTypes);
							$inputValue	= array_shift($inputValue);
							$inputValue	= Common::hashEmptyField($record, 'Property.type_co_broke_commission', $inputValue);

							if($isEditable){
								echo($this->Html->link('', '#', array(
									'data-value'	=> $inputValue, 
									'data-name'		=> 'data[Property][type_co_broke_commission]', 
									'data-type'		=> 'select', 
									'data-mode'		=> 'inline', 
									'data-source'	=> str_replace('"', '\'', json_encode($commissionTypes)), 
									'class'			=> 'editable editable-click', 
								)));
							}
							else{
								echo($this->Form->input('Property.type_co_broke_commission', array(
									'class'		=> 'form-control',
									'div'		=> false,
									'label'		=> false,
									'value'		=> $inputValue, 
									'options'	=> $commissionTypes, 
									'empty'		=> false,
								)));
							}

						?>
					</div>
				</div>
			</div>
			<div class="form-group commision-cobroke-box" style="<?php echo($isCoBroke ? '' : 'display: none;'); ?>">
				<div class="row">
					<div class="<?php echo($labelClass); ?>">
						<?php

							$label	= 'Apa itu Co-Broke Internal dan Eksternal ?';
							$notice	= 'Internal hanya akan muncul di internal perusahaan saja, sedangkan eksternal hanya akan muncul diluar dari perusahaan';
							$notice	= $this->Rumahku->popover($label, $notice);

							echo($this->Html->tag('label', __('Tipe Co-Broke *').$notice, array(
								'class' => 'control-label', 
							)));

						?>
					</div>
					<div class="<?php echo($valueClass); ?>">
						<?php

							$cobrokeTypes = empty($cobrokeTypes) ? array() : $cobrokeTypes;

							$inputValue	= array_keys($cobrokeTypes);
							$inputValue	= array_shift($inputValue);
							$inputValue	= Common::hashEmptyField($record, 'Property.co_broke_type', $inputValue);

							if($isEditable){
								echo($this->Html->link('', '#', array(
									'data-value'	=> $inputValue, 
									'data-name'		=> 'data[Property][co_broke_type]', 
									'data-type'		=> 'select', 
									'data-mode'		=> 'inline', 
									'data-source'	=> str_replace('"', '\'', json_encode($cobrokeTypes)), 
									'class'			=> 'editable editable-click', 
								)));
							}
							else{
								echo($this->Form->input('Property.co_broke_type', array(
									'class'		=> 'form-control',
									'div'		=> false,
									'label'		=> false,
									'value'		=> $inputValue, 
									'options'	=> $cobrokeTypes, 
									'empty'		=> false,
								)));
							}

						?>
					</div>
				</div>
			</div>
			<div class="form-group commision-cobroke-box" style="<?php echo($isCoBroke ? '' : 'display: none;'); ?>">
				<div class="row">
					<div class="<?php echo($labelClass); ?>">
						<?php

							echo($this->Html->tag('label', __('Co-Broke Note'), array(
								'class' => 'control-label', 
							)));

						?>
					</div>
					<div class="<?php echo($valueClass); ?>">
						<?php
							// co_broke note
            				$placeholder_cobroke_note = __('Berikan catatan apabila Anda menjadikan listing ini sebagai listing co-broke, untuk mempercepat penjualan. Contoh : Dapatkan bonus berupa trip perjalanan ke Perancis selama 3 Hari. S&K berlaku.');
							echo $this->Rumahku->buildInputForm('Property.cobroke_note', array(
			                    'label'       => false,
			                    'frameClass'  => 'col-sm-12',
			                    'placeholder' => $placeholder_cobroke_note,
			                    'type'        => 'textarea',
			                    'class'       => 'col-sm-12 col-xl-4',
			                    'rows'        => 8,
			                ));

						?>
					</div>
				</div>
			</div>
			<?php

		}

	//	CO-LISTING CO-SELLING ==========================================================================================

		if($cfg_isKolistingKoselling){

			?>
			<div class="form-group">
				<div class="row">
					<div class="<?php echo($labelClass); ?>">
						<?php

							echo($this->Html->tag('label', __('Kolisting Koseling'), array(
								'class' => 'control-label', 
							)));

						?>
					</div>
					<div class="<?php echo($valueClass); ?>">
						<?php

							$inputValue = Common::hashEmptyField($record, 'Property.kolisting_koselling', '');
							$inputValue = Common::hashEmptyField($this->request->data, 'Property.kolisting_koselling', $inputValue);

							if($isEditable){
								echo($this->Html->link($inputValue, '#', array(
									'data-value'		=> $inputValue, 
									'data-name'			=> 'data[Property][kolisting_koselling]', 
									'data-type'			=> 'text', 
									'data-mode'			=> 'inline', 
									'class'				=> 'editable editable-click', 
								)));
							}
							else{
								echo($this->Form->input('Property.kolisting_koselling', array(
									'type'		=> 'text', 
									'class'		=> 'form-control',
									'div'		=> false,
									'label'		=> false,
									'value'		=> $inputValue, 
								)));
							}

						?>
					</div>
				</div>
			</div>
			<?php

		}
	}

?>