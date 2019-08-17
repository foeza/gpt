<?php

	$record 		= empty($record) ? array() : $record;
	$cobroke_types 	= Configure::read('__Site.cobroke_type');

//	if($record){
		$isAdmin	= Configure::read('User.admin');
		$savePath	= Configure::read('__Site.profile_photo_folder');
		$options	= empty($options) ? array() : $options;

		$userModel		= Common::hashEmptyField($options, 'user_model', 'User');
		$profileModel	= Common::hashEmptyField($options, 'profile_model', 'UserProfile');
		$showProfile	= Common::hashEmptyField($options, 'show_profile', true, array('isset' => true));
		$showPhoto		= Common::hashEmptyField($options, 'show_photo', true, array('isset' => true));
		$showInput		= Common::hashEmptyField($options, 'show_input');

	//	agent data
		$photo		= Common::hashEmptyField($record, sprintf('%s.photo', $userModel));
		$fullName	= Common::hashEmptyField($record, sprintf('%s.full_name', $userModel));
		$email		= Common::hashEmptyField($record, sprintf('%s.email', $userModel));
		$created	= Common::hashEmptyField($record, sprintf('%s.created', $userModel));
		$created	= $this->Rumahku->formatDate($created, 'F Y');

	//	agent profile data
		$noHP	= Common::hashEmptyField($record, sprintf('%s.no_hp', $profileModel));
		$noHP2	= Common::hashEmptyField($record, sprintf('%s.no_hp_2', $profileModel));
		$phone	= Common::hashEmptyField($record, sprintf('%s.phone', $profileModel));

	// cobroke_note
		$cobroke_note	= Common::hashEmptyField($this->data, 'Property.cobroke_note');

		$clientEmail	= Common::hashEmptyField($record, 'Property.client_email');
		$status			= $this->Property->getStatus($record, 'span');
		$photo			= $this->Rumahku->photo_thumbnail(array(
			'save_path'	=> $savePath, 
			'src'		=> $photo, 
			'size'		=> 'pxl',
		), array(
			'class' => 'img-responsive',
		));

		$titleClass	= Common::hashEmptyField($options, 'title_class', 'custom-heading');
		$labelClass	= Common::hashEmptyField($options, 'label_class', 'col-sm-4 col-md-3 no-pright');
		$valueClass	= Common::hashEmptyField($options, 'value_class', 'col-sm-8 col-md-9 no-pleft');

		if($showProfile){
			$fields = array(
				array(
					'label'	=> __('Nama'), 
					'value'	=> $fullName, 
				), 
				array(
					'label'	=> __('Email'), 
					'value'	=> $email ? $this->Html->link($email, 'mailto:'.$email) : false, 
				), 
				array(
					'label'	=> __('No. HP 1'), 
					'value'	=> $noHP ? $this->Html->link($noHP, 'tel:'.$noHP) : false, 
				), 
				array(
					'label'	=> __('No. HP %s', ($noHP ? 2 : 1)), 
					'value'	=> $noHP2 ? $this->Html->link($noHP2, 'tel:'.$noHP2) : false, 
				), 
				array(
					'label'	=> __('No. Telp.'), 
					'value'	=> $phone ? $this->Html->link($phone, 'tel:'.$phone) : false, 
				), 
			);

		//	PHOTO
			if($showPhoto){
				echo($this->Html->div('row mb20', $this->Html->div('col-xs-12', $photo)));
			}

		//	IDENTITY
			foreach($fields as $key => $field){
				$label = Common::hashEmptyField($field, 'label');
				$value = Common::hashEmptyField($field, 'value');

				?>
				<div class="form-group-static">
					<div class="row">
						<div class="<?php echo($labelClass); ?>">
							<?php

								echo($this->Html->tag('label', __($label), array(
									'class' => 'control-label', 
								)));

							?>
						</div>
						<div class="<?php echo($valueClass); ?>">
							<?php

								echo($this->Html->tag('p', __($value), array(
									'class' => 'form-control-static', 
								)));

							?>
						</div>
					</div>
				</div>
				<?php

			}
		}

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

				$this->request->data	= Hash::insert($this->request->data, 'Property.is_cobroke', true);
			}
			else{
				$defaultAgentCommission				= false;
				$defaultCoBrokeCommission			= false;
				$defaultTypeCoBrokeCommission		= false;
				$defaultTypePriceCoBrokeCommission	= false;
			}

			$replacementData = array(
				'Property' => array(
					'commission'					=> Common::hashEmptyField($this->request->data, 'Property.commission', $defaultAgentCommission), 
					'co_broke_commision'			=> Common::hashEmptyField($this->request->data, 'Property.co_broke_commision', $defaultCoBrokeCommission), 
					'type_co_broke_commission'		=> Common::hashEmptyField($this->request->data, 'Property.type_co_broke_commission', $defaultTypeCoBrokeCommission), 
					'type_price_co_broke_commision'	=> Common::hashEmptyField($this->request->data, 'Property.type_price_co_broke_commision', $defaultTypePriceCoBrokeCommission), 
				), 
			);

			$record					= array_replace_recursive($record, $replacementData);
			$this->request->data	= array_replace_recursive($this->request->data, $replacementData);

		//	property data
			$contractDate	= Common::hashEmptyField($record, 'Property.contract_date', '');
			$commission		= Common::hashEmptyField($record, 'Property.commission', '');
			$isCoBroke		= Common::hashEmptyField($record, 'Property.is_cobroke');

		//	EMAIL ==========================================================================================================

			if($isAdmin){

				?>
				<div class="form-group">
					<div class="row">
						<div class="<?php echo($labelClass); ?>">
							<?php

								echo($this->Html->tag('label', __('Email *'), array(
									'class' => 'control-label', 
								)));

							?>
						</div>
						<div class="<?php echo($valueClass); ?>">
							<?php

								$autocompleteURL = $this->Html->url(array(
									'admin'			=> false,
									'controller'	=> 'ajax',
									'action'		=> 'list_users',
									2, 
								), true);

								echo($this->Html->link($email, '#', array(
									'data-value'	=> $email, 
									'data-name'		=> 'data[Property][agent_email]', 
									'data-type'		=> 'text', 
									'data-mode'		=> 'inline', 
									'class'			=> 'editable editable-click', 
									'data-tpl'		=> '<input type="text" data-role="autocomplete" data-ajax-url="'.$autocompleteURL.'">', 
								)));

							?>
						</div>
					</div>
				</div>
				<?php

			}

		//	CONTRACT DATE ==================================================================================================

			$label	= __('Tgl. Kontrak');
			$notice	= __('Tanggal Kontrak / Kesepakatan dengan Vendor');

			?>
			<div class="form-group">
				<div class="row">
					<div class="<?php echo($labelClass); ?>">
						<?php

							$notice = $this->Rumahku->popover($label, $notice);

							echo($this->Html->tag('label', __($label) . $notice, array(
								'class' => 'control-label', 
							)));

						?>
					</div>
					<div class="<?php echo($valueClass); ?>">
						<?php

							echo($this->Html->link($contractDate, '#', array(
								'data-value'	=> $contractDate, 
								'data-name'		=> 'data[Property][contract_date]', 
								'data-type'		=> 'text', 
								'data-mode'		=> 'inline', 
								'class'			=> 'editable editable-click', 
								'data-tpl'		=> '<input type="text" class="datepicker">', 
							)));

						?>
					</div>
				</div>
			</div>
			<?php

		//	COMMISSION =====================================================================================================

			$label = __('Komisi Pemilik Listing');

			if($cfg_isCoBroke){
				$notice	= __('Jika Anda ingin menjadikan properti menjadi listing Co-Broke, maka Anda harus memasukkan komisi agen terlebih dahulu');
				$notice	= $this->Rumahku->popover($label, $notice);
			}
			else{
				$notice = false;
			}

			?>
			<div class="form-group">
				<div class="row">
					<div class="<?php echo($labelClass); ?>">
						<?php

							echo($this->Html->tag('label', __('%s * %s %s', $label, $this->Html->tag('strong', '(%)'), $notice), array(
								'class' => 'control-label', 
							)));

						?>
					</div>
					<div class="<?php echo($valueClass); ?>">
						<?php

							echo($this->Html->link($commission, '#', array(
								'data-value'	=> $commission, 
								'data-name'		=> 'data[Property][commission]', 
								'data-type'		=> 'text', 
								'data-mode'		=> 'inline', 
								'class'			=> 'editable editable-click', 
								'data-tpl'		=> '<input type="text" class="input_number" maxlength="5">', 
							)));

						?>
					</div>
				</div>
			</div>
			<?php

		//	BT COMMISION ===================================================================================================

			if($cfg_isBtCommission){
				$label	= __('%s', $cfg_isCoBroke ? 'Broker Tradisional' : 'Komisi Perantara');
				$notice	= __('%s adalah mereka yang menjadi perantara / jasa penjual properti yang tidak terdaftar di kantor broker properti resmi', $this->Html->tag('strong', $label));

				?>
				<div class="form-group">
					<div class="row">
						<div class="<?php echo($labelClass); ?>">
							<?php

								$notice = $this->Rumahku->popover($label, $notice);

								echo($this->Html->tag('label', __('%s %s %s', $label, $this->Html->tag('strong', '(%)'), $notice), array(
									'class' => 'control-label', 
								)));

							?>
						</div>
						<div class="<?php echo($valueClass); ?>">
							<?php

								$inputValue = Common::hashEmptyField($User, 'UserConfig.bt', '');
								$inputValue = Common::hashEmptyField($this->request->data, 'Property.bt', $inputValue);

								echo($this->Html->link($inputValue, '#', array(
									'data-value'	=> $inputValue, 
									'data-name'		=> 'data[Property][bt]', 
									'data-type'		=> 'text', 
									'data-mode'		=> 'inline', 
									'class'			=> 'editable editable-click', 
									'data-tpl'		=> '<input type="text" class="input_price input_number" maxlength="3">', 
								)));

							?>
						</div>
					</div>
				</div>
				<?php

			}

			if($cfg_isCoBroke){
                if( !empty($cfg_isOpenCoBroke) && empty($isCoBroke) ) {
                    $infoCobroke = 'hide';
                } else {
                    $infoCobroke = '';
                }

				echo($this->Html->tag('h3', __('Informasi Co-Broke'), array(
					'class' => $titleClass.' '.$infoCobroke, 
				)));

				$message = $this->Html->tag('p', __('Anda bisa dengan mudah menampilkan listing properti Anda di channel Co-Broke dengan hanya klik "%s"', $this->Html->tag('strong', __('Jadikan listing Co-Broke?'))));
				$message.= $this->Html->tag('p', __('%s jadikan listing Co-Broke hanya bisa dilakukan jika Komisi Broker lebih dari 0', $this->Html->tag('strong', __('Catatan :'))));
		
				echo($this->Html->tag('div', $message, array(
					'class' => 'info-full alert mb20 '.$infoCobroke, 
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

								/* edit by iksan karena adanya perubahan ketika cobroke sedang berlangsung*/
								$approveCobroke = Common::hashEmptyField($record, 'CoBrokeProperty.approve');

								$toggleCobrokeOption = array(
									'mt'			=> 'mt10',
									'class'			=> 'handle-toggle-content',
									'data-target'	=> '.commision-cobroke-box', 
									'checked'		=> $isCoBroke, 
								);

								if(!empty($isCoBroke) && !empty($approveCobroke)){
									$toggleCobrokeOption['class'] .= ' false-alert';
									$toggleCobrokeOption['data-alert'] = __('Apakah Anda yakin ingin menghentikan Co Broke yang sedang berlangsung?');
								}

								echo $this->Rumahku->checkbox('Property.is_cobroke', $toggleCobrokeOption);

							//	echo($this->Html->tag('div', $this->Form->input('Property.is_cobroke', array(
							//		'type'		=> 'checkbox', 
							//		'div'		=> 'cb-checkmark', 
							//		'label'		=> false, 
							//		'value'		=> 1, 
							//		'checked'	=> $isCoBroke, 
							//	)) . $this->Form->label('Property.is_cobroke', '&nbsp;'), array(
							//		'class' => 'cb-custom mt10', 
							//	)));

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

								$inputValue		= Common::hashEmptyField($record, 'Property.type_price_co_broke_commision', 'percentage');
								$typeOptions	= array(
									'percentage'	=> __('Persentase'),
									'nominal'		=> __('Nominal'), 
								);

								echo($this->Html->link('', '#', array(
									'data-value'	=> $inputValue, 
									'data-name'		=> 'data[Property][type_price_co_broke_commision]', 
									'data-type'		=> 'select', 
									'data-mode'		=> 'inline', 
									'data-source'	=> str_replace('"', '\'', json_encode($typeOptions)), 
									'class'			=> 'editable editable-click', 
								)));

								$inputValue = Common::hashEmptyField($record, 'Property.co_broke_commision', '');

								echo($this->Html->link($inputValue, '#', array(
									'data-value'	=> $inputValue, 
									'data-name'		=> 'data[Property][co_broke_commision]', 
									'data-type'		=> 'text', 
									'data-mode'		=> 'inline', 
									'class'			=> 'editable editable-click', 
									'data-tpl'		=> '<input type="text" class="input_price input_number">', 
								)));

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

								echo($this->Html->link('', '#', array(
									'data-value'	=> $inputValue, 
									'data-name'		=> 'data[Property][type_co_broke_commission]', 
									'data-type'		=> 'select', 
									'data-mode'		=> 'inline', 
									'data-source'	=> str_replace('"', '\'', json_encode($commissionTypes)), 
									'class'			=> 'editable editable-click', 
								)));

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

								$cobroke_types = empty($cobroke_types) ? array() : $cobroke_types;

								$inputValue	= array_keys($cobroke_types);
								$inputValue	= array_shift($inputValue);
								$inputValue	= Common::hashEmptyField($record, 'Property.co_broke_type', $inputValue);

								echo($this->Html->link('', '#', array(
									'data-value'	=> $inputValue, 
									'data-name'		=> 'data[Property][co_broke_type]', 
									'data-type'		=> 'select', 
									'data-mode'		=> 'inline', 
									'data-source'	=> str_replace('"', '\'', json_encode($cobroke_types)), 
									'class'			=> 'editable editable-click', 
								)));

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
								echo($this->Html->link($cobroke_note, '#', array(
									'data-value'		=> $cobroke_note, 
									'data-name'			=> 'data[Property][cobroke_note]', 
									'data-type'			=> 'textarea', 
									'data-mode'			=> 'inline', 
									'data-placeholder'	=> $placeholder_cobroke_note, 
									'class'				=> 'editable editable-fullwidth editable-click', 
								)));

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

								echo($this->Html->link($inputValue, '#', array(
									'data-value'		=> $inputValue, 
									'data-name'			=> 'data[Property][kolisting_koselling]', 
									'data-type'			=> 'text', 
									'data-mode'			=> 'inline', 
									'class'				=> 'editable editable-click', 
								)));

							?>
						</div>
					</div>
				</div>
				<?php

			}
		}
//	}

?>