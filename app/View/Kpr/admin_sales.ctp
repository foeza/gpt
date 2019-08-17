<?php
		$globalData = Configure::read('Global.Data');
		$value = isset($value) ? $value : NULL;

		$name = $this->Rumahku->filterEmptyField($value, 'BankUser', 'full_name');
		$email = $this->Rumahku->filterEmptyField($value, 'BankUser', 'email');
		$photo = $this->Rumahku->filterEmptyField($value, 'BankUser', 'photo');
		$gender_id = $this->Rumahku->filterEmptyField($value, 'BankUser', 'gender_id', 1);

		$bank_name = $this->Rumahku->filterEmptyField($value, 'Bank', 'name');
		$bank_branch_name = $this->Rumahku->filterEmptyField($value, 'BankBranch', 'name', '-');
		$phone = $this->Rumahku->filterEmptyField($value, 'BankUserProfile', 'phone');
		$no_hp_is_whatsapp = $this->Rumahku->filterEmptyField($value, 'BankUserProfile', 'no_hp_is_whatsapp');
		$no_hp_2_is_whatsapp = $this->Rumahku->filterEmptyField($value, 'BankUserProfile', 'no_hp_2_is_whatsapp');
		$no_hp = $this->Rumahku->filterEmptyField($value, 'BankUserProfile', 'no_hp', '-', true, array(
			'wa' => $no_hp_is_whatsapp,
		));
		$no_hp_2 = $this->Rumahku->filterEmptyField($value, 'BankUserProfile', 'no_hp_2', '-', true, array(
			'wa' => $no_hp_2_is_whatsapp,
		));
		$pin_bb = $this->Rumahku->filterEmptyField($value, 'BankUserProfile', 'pin_bb', '-');
		$line = $this->Rumahku->filterEmptyField($value, 'BankUserProfile', 'line', '-');

		$gender = $this->Rumahku->filterEmptyField($globalData, 'gender_options', $gender_id);

		$photoPath = Configure::read('__Site.profile_photo_folder');
		$photoImage = $this->Html->tag('span', 
			$this->Rumahku->photo_thumbnail(array(
				'save_path' => $photoPath, 
				'src' => $photo, 
				'size' => 'pm'
			), array(
				'title' => $name, 
				'alt' => $name
			))
		);
?>
<div class="tabs-box">
	<div class="row">
		<div class="<?php echo !empty($dataCompany)?'col-xs-12 col-md-6':'col-md-12'; ?>">
			<div class="box box-success">
				<div class="box-header with-border">
					<?php echo($this->Html->tag('h3', $module_title)); ?>
				</div>
				<div class="box-footer">
					<?php
							echo $this->Html->tag('p', $photoImage, array(
								'align' => 'center', 
								'class' => 'form-control-static',
							));
					?>
				</div>
				<div class="box-body">
					<?php
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Nama')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $name, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Cabang')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $bank_branch_name, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Email')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $email, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Jenis Kelamin')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $gender, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('h3', __('Informasi Kontak'), array(
								'class' => 'mt15',
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Telepon')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $phone, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Handphone')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $no_hp, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Handphone #2')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $no_hp_2, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Line')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $line, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('BB')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $pin_bb, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));

					?>
				</div>
			</div>
		</div>
		<?php 
				if( !empty($bank_name) ) {
					// $phone = $this->Rumahku->filterEmptyField($value, 'Bank', 'phone', '-');
					$phone_center = Common::hashEmptyField($value, 'Bank.phone_center', '-', array(
						'type' => 'tel',
					));
					$fax = Common::hashEmptyField($value, 'Bank.fax', '-', array(
						'type' => 'tel',
					));
					$logo = $this->Rumahku->filterEmptyField($value, 'Bank', 'logo');
					$contacts = Common::hashEmptyField($value, 'Bank.BankContact');

					$logoPath = Configure::read('__Site.logo_photo_folder');
					$logoImage = $this->Html->tag('span', 
						$this->Rumahku->photo_thumbnail(array(
							'save_path' => $logoPath, 
							'src' => $logo, 
							'size' => 'xm'
						), array(
							'title' => $bank_name, 
							'alt' => $bank_name
						))
					);
		?>
		<div class="col-xs-12 col-md-6">
			<div class="box box-success">
				<div class="box-header with-border">
					<?php echo($this->Html->tag('h3', __('Informasi Bank'))); ?>
				</div>
				<div class="box-footer">
					<?php
							echo $this->Html->tag('p', $logoImage, array(
								'align' => 'center', 
								'class' => 'form-control-static',
							));
					?>
				</div>
				<div class="box-body">
					<?php
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Nama Bank')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $bank_name, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Call Center')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $phone_center, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));

							if( !empty($contacts) ) {
								$i = 1;

								foreach ($contacts as $key => $contact) {
									$phone = Common::hashEmptyField($contact, 'BankContact.phone', null, array(
										'type' => 'tel',
									));

									echo $this->Html->tag('div', 
										$this->Html->tag('div',
											$this->Html->tag('label', __('Telepon #%s', $i)), array(
											'class' => 'col-xs-12 col-md-4'
										)).
										$this->Html->tag('div', 
											$this->Html->tag('p', $phone, array(
												'class' => 'form-control-static'
											)), array(
											'class' => 'col-xs-12 col-md-8'
										)), array(
										'class' => 'row form-group-static'
									));
									
									$i++;
								}
							}
							echo $this->Html->tag('div', 
								$this->Html->tag('div',
									$this->Html->tag('label', __('Fax')), array(
									'class' => 'col-xs-12 col-md-4'
								)).
								$this->Html->tag('div', 
									$this->Html->tag('p', $fax, array(
										'class' => 'form-control-static'
									)), array(
									'class' => 'col-xs-12 col-md-8'
								)), array(
								'class' => 'row form-group-static'
							));
					?>	
				</div>
			</div>
		</div>
		<?php 
				}
		?>
	</div>
</div>