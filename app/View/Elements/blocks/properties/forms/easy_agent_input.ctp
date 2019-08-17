<?php

	$isAdmin		= Configure::read('User.admin');
	$cobrokeTypes 	= Configure::read('__Site.cobroke_type');
	$savePath		= Configure::read('__Site.profile_photo_folder');

	$record 	= empty($record) ? array() : $record;
	$options	= empty($options) ? array() : $options;
	$titleClass	= Common::hashEmptyField($options, 'title_class', 'custom-heading');
	$labelClass	= Common::hashEmptyField($options, 'label_class', 'col-sm-4 col-md-3 no-pright');
	$valueClass	= Common::hashEmptyField($options, 'value_class', 'col-sm-8 col-md-9 no-pleft');

	$showProfile	= Common::hashEmptyField($options, 'show_profile', true, array('isset' => true));
	$showPhoto		= Common::hashEmptyField($options, 'show_photo', true, array('isset' => true));
	$showInput		= Common::hashEmptyField($options, 'show_input');
	$isEditable		= Common::hashEmptyField($options, 'is_editable', true, array('isset' => true));
	$email			= false;

	if($record && $showProfile){
		$userModel		= Common::hashEmptyField($options, 'user_model', 'User');
		$profileModel	= Common::hashEmptyField($options, 'profile_model', 'UserProfile');

	//	agent data
		$userID = Common::hashEmptyField($record, sprintf('%s.id', $userModel));

		if($userID){
			$photo		= Common::hashEmptyField($record, sprintf('%s.photo', $userModel));
			$fullName	= Common::hashEmptyField($record, sprintf('%s.full_name', $userModel));
			$email		= Common::hashEmptyField($record, sprintf('%s.email', $userModel));
			$created	= Common::hashEmptyField($record, sprintf('%s.created', $userModel));
			$created	= $this->Rumahku->formatDate($created, 'F Y');

		//	agent profile data
			$noHP	= Common::hashEmptyField($record, sprintf('%s.no_hp', $profileModel));
			$noHP2	= Common::hashEmptyField($record, sprintf('%s.no_hp_2', $profileModel));
			$phone	= Common::hashEmptyField($record, sprintf('%s.phone', $profileModel));

		//	$status	= $this->Property->getStatus($record, 'span');
			$photo	= $this->Rumahku->photo_thumbnail(array(
				'save_path'	=> $savePath, 
				'src'		=> $photo, 
				'size'		=> 'pxl',
			), array(
				'class' => 'img-responsive',
			));

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
			if($isEditable){

				?>
				<div class="form-group">
					<div class="row">
						<div class="<?php echo($labelClass); ?>">
							<?php

								echo($this->Html->tag('label', __('Email'), array(
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
			else{
				echo($this->element('blocks/properties/forms/input_email', array(
					'options'	=> array(
						'wrapper_class'		=> 'col-sm-12', 
						'frame_label_class'	=> $labelClass, 
						'frame_input_class'	=> $valueClass, 
					), 
				)));
			}
		}

	//	CONTRACT DATE ==================================================================================================

		$label	= __('Tanggal Kontrak');
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

						if($isEditable){
							echo($this->Html->link($contractDate, '#', array(
								'data-value'	=> $contractDate, 
								'data-name'		=> 'data[Property][contract_date]', 
								'data-type'		=> 'text', 
								'data-mode'		=> 'inline', 
								'class'			=> 'editable editable-click', 
								'data-tpl'		=> '<input type="text" class="datepicker">', 
							)));
						}
						else{
							$contractDate = Common::hashEmptyField($this->request->data, 'Property.contract_date', $contractDate);

							if(strtotime($contractDate)){
								$contractDate = date('d/m/Y', strtotime($contractDate));
							}

							echo($this->Form->input('Property.contract_date', array(
								'type'	=> 'text', 
								'class'	=> 'form-control datepicker', 
								'value'	=> $contractDate, 
								'div'	=> false, 
								'label'	=> false, 
							)));
						}

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

						$label = __('%s * %s', $label, $isEditable ? $this->Html->tag('strong', '(%)') : '');
						$label = __('%s %s', $label, $notice);

						echo($this->Html->tag('label', $label, array(
							'class' => 'control-label', 
						)));

					?>
				</div>
				<div class="<?php echo($valueClass); ?>">
					<?php

						if($isEditable){
							echo($this->Html->link($commission, '#', array(
								'data-value'	=> $commission, 
								'data-name'		=> 'data[Property][commission]', 
								'data-type'		=> 'text', 
								'data-mode'		=> 'inline', 
								'class'			=> 'editable editable-click', 
								'data-tpl'		=> '<input type="text" class="input_number" maxlength="5">', 
							)));
						}
						else{
							echo($this->Form->input('Property.commission', array(
								'type'		=> 'text', 
								'class'		=> 'form-control input_number has-side-control at-right',
								'div'		=> 'input-group no-margin',
								'label'		=> false,
								'error'		=> false,
								'value'		=> $commission, 
								'maxlength'	=> 5, 
								'after'		=> $this->Html->div('input-group-addon at-right', '%'), 
							)));

							echo($this->Form->error('Property.commission'));
						}

					?>
				</div>
			</div>
		</div>
		<?php

	//	BT COMMISION ===================================================================================================

		if($cfg_isBtCommission){
			$label	= __('%s', $cfg_isCoBroke ? 'Broker Tradisional' : 'Komisi Perantara');
			$notice	= __('%s adalah mereka yang menjadi perantara / jasa penjual properti yang tidak terdaftar di kantor broker properti resmi', $this->Html->tag('strong', $label));
			$notice	= $this->Rumahku->popover($label, $notice);

			?>
			<div class="form-group">
				<div class="row">
					<div class="<?php echo($labelClass); ?>">
						<?php

							$label = __('%s %s', $label, $isEditable ? $this->Html->tag('strong', '(%)') : '');
							$label = __('%s %s', $label, $notice);

							echo($this->Html->tag('label', $label, array(
								'class' => 'control-label', 
							)));

						?>
					</div>
					<div class="<?php echo($valueClass); ?>">
						<?php

							$inputValue = Common::hashEmptyField($User, 'UserConfig.bt', '');
							$inputValue = Common::hashEmptyField($this->request->data, 'Property.bt', $inputValue);

							if($isEditable){
								echo($this->Html->link($inputValue, '#', array(
									'data-value'	=> $inputValue, 
									'data-name'		=> 'data[Property][bt]', 
									'data-type'		=> 'text', 
									'data-mode'		=> 'inline', 
									'class'			=> 'editable editable-click', 
									'data-tpl'		=> '<input type="text" class="input_price input_number" maxlength="3">', 
								)));
							}
							else{
								echo($this->Form->input('Property.bt', array(
									'type'		=> 'text', 
									'class'		=> 'form-control input_number has-side-control at-right',
									'div'		=> 'input-group no-margin',
									'label'		=> false,
									'error'		=> false, 
									'value'		=> $inputValue, 
									'maxlength'	=> 3, 
									'after'		=> $this->Html->div('input-group-addon at-right', '%'), 
								)));

								echo($this->Form->error('Property.bt'));
							}

						?>
					</div>
				</div>
			</div>
			<?php

		}
	}

?>