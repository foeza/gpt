<?php

	$value		= empty($value) ? array() : $value;
	$urlBack	= isset($urlBack) ? $urlBack : '';

	if($value){
		$globalData		= Common::config('Global.Data', array());
		$photoPath		= Common::config('__Site.profile_photo_folder', false);
		$emptyString	= 'N/A';

		$isShowParent	= Hash::check($value, 'Parent');
		$isShowCompany	= Hash::check($value, 'UserCompany');
		$colClass		= $isShowCompany ? 'col-md-6' : 'col-md-12';

		$userGroupID	= Common::hashEmptyField($value, 'Group.id', 0);
		$userGroupName	= Common::hashEmptyField($value, 'Group.name', $emptyString);

		$userFullName	= Common::hashEmptyField($value, 'User.full_name', $emptyString);
		$userEmail		= Common::hashEmptyField($value, 'User.email', false);
		$userPhoto		= Common::hashEmptyField($value, 'User.photo', false);
		$userGenderID	= Common::hashEmptyField($value, 'User.gender_id', 1);

		$userBirthday	= Common::hashEmptyField($value, 'UserProfile.birthday', false);
		$userPhone		= Common::hashEmptyField($value, 'UserProfile.phone', false);
		$userNoHp		= Common::hashEmptyField($value, 'UserProfile.no_hp', false);
		$userNoHp2		= Common::hashEmptyField($value, 'UserProfile.no_hp_2', false);
		$noHpIsWA		= Common::hashEmptyField($value, 'UserProfile.no_hp_is_whatsapp', true);
		$noHp2IsWA		= Common::hashEmptyField($value, 'UserProfile.no_hp_2_is_whatsapp', true);
		$userBbPin		= Common::hashEmptyField($value, 'UserProfile.pin_bb', $emptyString);
		$userLine		= Common::hashEmptyField($value, 'UserProfile.line', $emptyString);
		$userAddress	= Common::hashEmptyField($value, 'UserProfile.address', $emptyString);
		$userRegionName	= Common::hashEmptyField($value, 'UserProfile.Region.name', $emptyString);
		$userCityName	= Common::hashEmptyField($value, 'UserProfile.City.name', $emptyString);
		$userAreaName	= Common::hashEmptyField($value, 'UserProfile.Subarea.name', $emptyString);
		$userZipCode	= Common::hashEmptyField($value, 'UserProfile.Subarea.zip', $emptyString);
		$userZipCode	= Common::hashEmptyField($value, 'UserProfile.zip', $userZipCode);

		$genderOptions	= Common::hashEmptyField($globalData, 'gender_options', array());
		$userGenderName	= Common::hashEmptyField($genderOptions, $userGenderID, $emptyString);

		$userEmail		= $userEmail ? $this->Html->link($userEmail, sprintf('mailto:%s', $userEmail)) : $emptyString;
		$userPhone		= $userPhone ? $this->Html->link($userPhone, sprintf('tel:%s', $userPhone)) : $emptyString;

		if($userNoHp){
			$userNoHp	= $this->Html->link($userNoHp, sprintf('tel:%s', $userNoHp));
			$userNoHp	= sprintf('%s %s', $userNoHp, $noHpIsWA ? '(WA)' : '');
		}
		else{
			$userNoHp = $emptyString;
		}

		if($userNoHp2){
			$userNoHp2	= $this->Html->link($userNoHp2, sprintf('tel:%s', $userNoHp2));
			$userNoHp2	= sprintf('%s %s', $userNoHp2, $noHp2IsWA ? '(WA)' : '');
		}
		else{
			$userNoHp2 = $emptyString;
		}

		$userBirthday	= $userBirthday ? $this->Rumahku->getIndoDateCutom($userBirthday) : $emptyString;
		$userPhoto		= $this->Rumahku->photo_thumbnail(array(
			'save_path'	=> $photoPath, 
			'src'		=> $userPhoto, 
			'size'		=> 'pm', 
		), array(
			'title'		=> $userFullName, 
			'alt'		=> $userFullName, 
			'class' 	=> 'info-thumbnail', 
		));

		$contents = array(
			'Informasi Dasar' => array(
				'Nama Lengkap'	=> $userFullName, 
				'Divisi'		=> $userGroupName, 
				'Email'			=> $userEmail, 
				'Jenis Kelamin'	=> $userGenderName, 
				'Tgl. Lahir'	=> $userBirthday, 
			), 
			'Informasi Kontak' => array(
				'No. Telepon'		=> $userPhone, 
				'No. Handphone 1'	=> $userNoHp, 
				'No. Handphone 2'	=> $userNoHp2, 
				'Line'				=> $userLine, 
				'BB'				=> $userBbPin, 
			), 
			'Informasi Alamat' => array(
				'Alamat'		=> $userAddress, 
				'Provinsi'		=> $userRegionName, 
				'Kota'			=> $userCityName, 
				'Area'			=> $userAreaName, 
				'Kode Pos'		=> $userZipCode, 
			), 
		);

		if($isShowParent){
			$parentFullName	= Common::hashEmptyField($value, 'Parent.full_name', $emptyString);
			$parentEmail	= Common::hashEmptyField($value, 'Parent.email', false);
			$parentNoHp		= Common::hashEmptyField($value, 'Parent.UserProfile.no_hp', false);
			$parentPhone	= Common::hashEmptyField($value, 'Parent.UserProfile.phone', false);

			$parentEmail	= $parentEmail ? $this->Html->link($parentEmail, sprintf('mailto:%s', $parentEmail)) : $emptyString;
			$parentNoHp		= $parentNoHp ? $this->Html->link($parentNoHp, sprintf('tel:%s', $parentNoHp)) : $emptyString;
			$parentPhone	= $parentPhone ? $this->Html->link($parentPhone, sprintf('tel:%s', $parentPhone)) : $emptyString;

			$contents['Informasi Atasan'] = array(
				'Nama Lengkap'	=> $parentFullName, 
				'Email'			=> $parentEmail, 
				'No. Handphone'	=> $parentNoHp, 
				'No. Telepon'	=> $parentPhone, 
			);
		}

		echo($this->element('blocks/users/tabs/info'));

		?>
		<div class="tabs-box">
			<div class="row">
				<div class="<?php echo($colClass); ?>">
					<div class="box box-success">
						<div class="box-header with-border">
							<?php echo($this->Html->tag('h3', __('Informasi Personal'))); ?>
						</div>
						<div class="box-footer info-thumbnail-placeholder" align="center">
							<?php echo($userPhoto); ?>
						</div>
						<div class="box-body">
							<div class="mb15">
								<?php

									foreach($contents as $contentTitle => $fields){
										echo($this->Html->tag('h3', __($contentTitle), array(
											'class' => 'mt15 mb15',
										)));

										foreach($fields as $label => $text){

											?>
											<div class="row form-group-static">
												<div class="col-xs-12 col-md-4">
													<label><?php echo($label); ?></label>
												</div>
												<div class="col-xs-12 col-md-8">
													<p class="form-control-static"><?php echo($text); ?></p>
												</div>
											</div>
											<?php

										}
									}

								?>
							</div>
						</div>
					</div>
				</div>
				<?php

					if($isShowCompany){
						$logoPath = Common::config('__Site.logo_photo_folder', false);

						$companyName	= Common::hashEmptyField($value, 'UserCompany.name', $emptyString);
						$companyPhone	= Common::hashEmptyField($value, 'UserCompany.phone', false);
						$companyPhone2	= Common::hashEmptyField($value, 'UserCompany.phone_2', false);
						$companyFax		= Common::hashEmptyField($value, 'UserCompany.fax', false);
						$companyLogo	= Common::hashEmptyField($value, 'UserCompany.logo', false);

						$companyAddress	= $this->Rumahku->getFullAddress(Hash::get($value, 'UserCompany', array()), '<br>');
						$companyPhone	= $companyPhone ? $this->Html->link($companyPhone, sprintf('tel:%s', $companyPhone)) : $emptyString;
						$companyPhone2	= $companyPhone2 ? $this->Html->link($companyPhone2, sprintf('tel:%s', $companyPhone2)) : $emptyString;
						$companyFax		= $companyFax ? $this->Html->link($companyFax, sprintf('tel:%s', $companyFax)) : $emptyString;

						$companyLogo = $this->Rumahku->photo_thumbnail(array(
							'save_path'	=> $logoPath, 
							'src'		=> $companyLogo, 
							'size'		=> 'xm', 
						), array(
							'title'		=> $companyName, 
							'alt'		=> $companyName, 
							'class' 	=> 'info-thumbnail', 
						));

						$contents = array(
							'Informasi Dasar' => array(
								'Nama'				=> $companyName, 
								'No. Telepon 1'		=> $companyPhone, 
								'No. Telepon 2'		=> $companyPhone2, 
								'No. Fax'			=> $companyFax, 
								'Alamat'			=> $companyAddress, 
							), 
						);

						?>
						<div class="<?php echo($colClass); ?>">
							<div class="box box-success">
								<div class="box-header with-border">
									<?php echo($this->Html->tag('h3', __('Informasi Perusahaan'))); ?>
								</div>
								<div class="box-footer info-thumbnail-placeholder" align="center">
									<?php echo($companyLogo); ?>
								</div>
								<div class="box-body">
									<div class="mb15">
										<?php

											foreach($contents as $contentTitle => $fields){
												echo($this->Html->tag('h3', __($contentTitle), array(
													'class' => 'mt15 mb15',
												)));

												foreach($fields as $label => $text){

													?>
													<div class="row form-group-static">
														<div class="col-xs-12 col-md-4">
															<label><?php echo($label); ?></label>
														</div>
														<div class="col-xs-12 col-md-8">
															<p class="form-control-static"><?php echo($text); ?></p>
														</div>
													</div>
													<?php

												}
											}

										?>
									</div>
								</div>
							</div>
						</div>
						<?php

					}

				?>
			</div>
		</div>
		<?php

	}

	$isAdmin = Common::validateRole('admin');

	if($isAdmin){

		?>
		<div class="row">
			<div class="col-xs-12">
				<div class="box box-success">
					<div class="box-header with-border">
						<?php echo($this->Html->tag('h3', __('Histori User'))); ?>
					</div>
					<div class="box-body">
						<?php

							$histories = empty($histories) ? array() : $histories;

							echo($this->Html->tag('div', $this->element('blocks/users/tables/histories', array(
								'record'	=> $value, 
								'histories'	=> $histories, 
							)), array(
								'class' => 'mt15', 
							)));

						?>
					</div>
				</div>
			</div>
		</div>
		<?php

	}

	if($value || $urlBack){

		?>
		<div class="action-group bottom">
			<div class="tacenter">
				<?php

					if($urlBack){
						echo($this->Html->link(__('Kembali'), $urlBack, array(
							'class' => 'btn default inline',
						)));
					}

					echo($this->User->_callActionEdit($value));

				?>
			</div>
		</div>
		<?php

	}

?>