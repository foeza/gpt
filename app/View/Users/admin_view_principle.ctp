<?php

	$record = isset($record) ? $record : NULL;
	if($record){
		$defaultOptions = array(
			'frameClass'	=> 'col-xs-12', 
			'labelClass'	=> 'col-xs-12 col-md-4 control-label taright',
			'class'			=> 'relative col-xs-12 col-md-8',
		);

		$createDate = $this->Rumahku->filterEmptyField($this->request->data, 'MembershipOrder', 'created');
		$createDate = date('d/m/Y H:i', strtotime($createDate));

		$this->request->data['MembershipOrder']['created'] = $createDate;

		?>
		<div class="row">
			<div class="col-xs-12 col-md-7">
				<div class="box box-success">
					<div class="box-header with-border">
						<?php echo($this->Html->tag('h3', __('Detail Principal'))); ?>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								<?php

									$recordID		= $this->Rumahku->filterEmptyField($record, 'User', 'id');
									$name			= $this->Rumahku->filterEmptyField($record, 'User', 'full_name');
									$email			= $this->Rumahku->filterEmptyField($record, 'User', 'email');
									$companyName	= $this->Rumahku->filterEmptyField($record, 'UserCompany', 'name');
									$logoPath		= Configure::read('__Site.logo_photo_folder');
									$photoSize		= $this->Rumahku->_rulesDimensionImage($logoPath, 'large', 'size');
									$logo			= $this->Rumahku->filterEmptyField($record, 'UserCompany', 'logo');
									$logoImage		= $this->Html->tag(
										'span', 
										$this->Rumahku->photo_thumbnail(
											array('save_path' => $logoPath, 'src' => $logo, 'size' => 'xm'), 
											array('title' => sprintf('%s Logo', $companyName), 'alt' => Inflector::slug($companyName, '-').'-logo')
										)
									);

									$content = $this->Html->tag('div', $this->Form->label('User.full_name', $this->Html->tag('b', __('Nama Principal'))), array('class' => 'col-xs-12 col-md-4'));
									$content.= $this->Html->tag('div', $this->Html->tag('p', $name, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-8'));

									echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

									$content = $this->Html->tag('div', $this->Form->label('User.email', $this->Html->tag('b', __('Email Principal'))), array('class' => 'col-xs-12 col-md-4'));
									$content.= $this->Html->tag('div', $this->Html->tag('p', $email, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-8'));

									echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

									$content = $this->Html->tag('div', $this->Form->label('UserCompany.name', $this->Html->tag('b', __('Nama Perusahaan'))), array('class' => 'col-xs-12 col-md-4'));
									$content.= $this->Html->tag('div', $this->Html->tag('p', $companyName, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-8'));

									echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

									$content = $this->Html->tag('div', $this->Form->label('UserCompany.name', $this->Html->tag('b', __('Logo Perusahaan'))), array('class' => 'col-xs-12'));

									echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

								?>
							</div>
						</div>
					</div>
					<div class="box-footer">
						<?php echo($this->Html->tag('p', $logoImage, array('align' => 'center', 'class' => 'form-control-static'))); ?>
					</div>
					<div class="box-footer">&nbsp;</div>
				</div>
			</div>
			<div class="col-xs-12 col-md-5">
				<div class="box box-success">
					<div class="box-header with-border">
						<?php echo($this->Html->tag('h3', __('Informasi Paket Membership'))); ?>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-xs-12">
								<?php

									$packageID		= $this->Rumahku->filterEmptyField($record, 'MembershipPackage', 'id');
									$packageName	= $this->Rumahku->filterEmptyField($record, 'MembershipPackage', 'name');
									$monthDuration	= $this->Rumahku->filterEmptyField($record, 'MembershipPackage', 'month_duration');
									$liveDate		= $this->Rumahku->filterEmptyField($record, 'UserCompanyConfig', 'live_date');
									$endDate		= $this->Rumahku->filterEmptyField($record, 'UserCompanyConfig', 'end_date');

									$content = $this->Html->tag('div', $this->Form->label('MembershipPackage.name', $this->Html->tag('b', __('Nama Paket'))), array('class' => 'col-xs-12 col-md-4'));
									$content.= $this->Html->tag('div', $this->Html->tag('p', $packageName, array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-8'));

									echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

									$content = $this->Html->tag('div', $this->Form->label('MembershipPackage.name', $this->Html->tag('b', __('Periode'))), array('class' => 'col-xs-12 col-md-4'));
									$content.= $this->Html->tag('div', $this->Html->tag('p', sprintf('%s bulan', $monthDuration), array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-8'));

									echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

									$content = $this->Html->tag('div', $this->Form->label('UserCompanyConfig.end_date', $this->Html->tag('b', __('Berlaku Sampai'))), array('class' => 'col-xs-12 col-md-4'));
									$content.= $this->Html->tag('div', $this->Html->tag('p', date('d/m/Y', strtotime($endDate)), array('class' => 'form-control-static')), array('class' => 'col-xs-12 col-md-8'));

									echo($this->Html->tag('div', $content, array('class' => 'row form-group-static')));

								?>	
							</div>
						</div>
					</div>
					<div class="box-footer">&nbsp;</div>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-xs-12">
				<div class="action-group bottom">
					<div class="btn-group floright">
						<?php

							echo($this->Html->link(__('Kembali'), 
								array('action' => 'index', 'admin' => TRUE), 
								array('class' => 'btn default')
							));

							echo($this->Html->link(__('Kirim Email Renewal'), 
								array('action' => 'view_principle', 'admin' => TRUE, $recordID, 'send_notification' => TRUE), 
								array('class' => 'btn blue')
							));

						?>
					</div>
				</div>
			</div>
		</div>

		<?php

	}
	else{
		
	}

?>