<?php   
		$data = $this->request->data;
		$follow_up = !empty($follow_up)?$follow_up:false;
		$editStatus = isset($editStatus)?$editStatus:true;
		$session_id = !empty($session_id)?$session_id:false;
		$activity_id = !empty($activity_id)?$activity_id:false;
		$value = !empty($value)?$value:true;
		$dataMedias = !empty($dataMedias)?$dataMedias:false;
		$classCol = !empty($classCol)?$classCol:'col-sm-8';

		$labelName = !empty($labelName)?$labelName:__('Aktivitas Baru');
		$labelButton = !empty($labelButton)?$labelButton:__('Catat Aktivitas');
		$classForm = !empty($classForm)?$classForm:false;
		$wrapperAttribute = !empty($wrapperAttribute)?$wrapperAttribute:sprintf('wrapper-attribute%s', $activity_id);
		$dataParams = !empty($dataParams)?$dataParams:false;

		$formModelName = !empty($formModelName)?$formModelName:'CrmProjectActivity';
		$modelName = !empty($modelName)?$modelName:'AttributeSetOption';
		$attributeSetValue = !empty($attributeSetValue)?$attributeSetValue:false;

		$id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'id');
		$crm_attribute_set_id = $this->Rumahku->filterEmptyField($value, 'CrmProject', 'attribute_set_id');
		$attribute_set_id = !empty($attribute_set_id)?$attribute_set_id:$crm_attribute_set_id;

		$dataUpload = array(
			'booking_fee',
		);
		$attributeSetOption['AttributeSetOption'] = $this->Rumahku->filterEmptyField($data, 'AttributeSetOption');

		if( !$this->Crm->unShowType() ) {
			$classHide = 'hide';
		} else {
			$classHide = '';
		}

		if( !$this->Crm->unShowStatus( $attribute_set_id, $value ) ) {
			$classStatusHide = 'hide';
		} else {
			$classStatusHide = '';
		}
?>
<div class="new-activity">
	<?php 
			// echo $this->Html->tag('label', $labelName);
	?>
	<?php 
			echo $this->Form->create($formModelName, array(
				'type' => 'file',
				'id' => 'form-crm-activity',
				'class' => $classForm.' crm-project-form',
				'data-wrapper-write' => '#wrapper-edit-activity',
				'data-type' => 'content',
				'data-reload' => 'true',
			));
	?>
	<div class="activity-detail" id="wrapper-add-activity">
		<div class="row">
			<div class="col-sm-8">
				<div class="row">
					<?php 
							if( empty($follow_up) ) {
								echo $this->Html->tag('div', $this->element('blocks/crm/forms/additional_input', array(
									'modelName' => $modelName,
									'attributeSetValue' => $attributeSetValue,
									'wrapperAttribute' => $wrapperAttribute,
									'dataParams' => $dataParams,
									'crm_project_id' => $id,
									'session_id' => $session_id,
								)), array(
									'class' => 'activity-hide '.$classHide,
								));
							}

							if( !empty($clients) ) {
					?>
					<div class="col-md-4 col-sm-6 mt15 activity-hide <?php echo $classHide; ?>">
						<?php 
								echo $this->Form->label('client_id', __('Klien'));
						?>
						<div class="input-group side">
							<div class="select">
								<?php 
										echo $this->Form->input('client_id', array(
											'label' => false,
							                'required' => false,
							                'div' => false,
							                'error' => false,
							                'class' => 'form-control ajax-attribute',
							                'empty' => __('- Pilih Klien -'),
					                        'data-wrapper-write' => '#'.$wrapperAttribute,
					                        'data-params' => $dataParams,
					                        'data-form' => '#form-crm-activity',
					                        'data-use-current-value' => 'false',
					                        'data-href' => $this->Html->url(array(
					                            'controller' => 'crm',
					                            'action' => 'attributes',
					                            $id,
					                            'session_id' => $session_id,
					                            'activity_id' => $activity_id,
					                            'admin' => true,
					                        )),
							            ));
							            echo $this->Rumahku->icon('rv4-angle-down', false, 'span');
								?>
							</div>
						</div>
						<?php 
								echo $this->Form->error('client_id');
						?>
					</div>
					<?php 
							}
					?>
				</div>
				<div class="row">
					<div class="col-sm-8 mt15">
						<?php 
								echo $this->Form->label('activity_date', __('Tanggal Aktivitas')).$this->Html->tag('div', $this->Form->input('activity_date', array(
									'type' => 'text',
									'label' => false,
					                'required' => false,
					                'div' => false,
					                'error' => false,
					                'class' => 'datepicker on-focus',
					                'placeholder' => __('Tanggal Aktivitas'),
					            )), array(
					            	'class' => 'input-group side',
					            ));
								echo $this->Form->error('activity_date');
						?>
					</div>
					<div class="col-md-4 col-sm-6 mt15">
						<?php 
								echo $this->Form->label('activity_time', __('Jam'));
						?>
						<div class="input-group side time">
							<?php 
									echo $this->Form->input('activity_time', array(
										'type' => 'text',
										'label' => false,
						                'required' => false,
						                'div' => false,
						                'default' => time('H:i'),
						                'error' => false,
						                'class' => 'timepicker',
						                'placeholder' => __('Jam Aktivitas'),
						            ));
							?>
						</div>
						<?php 
								echo $this->Form->error('activity_time');
						?>
					</div>
				</div>
			</div>
			<?php 
					echo $this->Html->tag('div', $this->element('blocks/crm/forms/additional_input', array(
						'attributeSetValue' => $attributeSetOption,
						'wrapperAttribute' => $wrapperAttribute,
						'addClass' => $wrapperAttribute,
						'full_input' => true,
						'crm_project_id' => $id,
						'session_id' => $session_id,
						'classCol' => $classCol,
					)), array(
						'class' => 'row',
					));
			?>
			<div class="col-sm-8">
				<?php 
						if( !empty($dataUpload) ) {
							$contentPhoto = '';

							foreach ($dataUpload as $fieldName) {
								$filename = sprintf('%s_hide', $fieldName);
								$basename = sprintf('%s_name', $fieldName);

	        					$baseName = $this->Rumahku->filterEmptyField($data, 'CrmProjectActivity', $basename);
	        					$imageName = $this->Rumahku->filterEmptyField($data, 'CrmProjectActivity', $filename);

	        					if( !empty($baseName) ) {
									$contentPhoto .= $this->Html->tag('li', $baseName);

									echo $this->Form->hidden(sprintf('CrmProjectActivity.%s_hide', $fieldName), array(
										'value' => $imageName,
									));
									echo $this->Form->hidden(sprintf('CrmProjectActivity.%s_name', $fieldName), array(
										'value' => $baseName,
									));
								}
							}

							if( !empty($contentPhoto) ) {
								echo $this->Html->tag('ul', $contentPhoto, array(
									'class' => 'crm-files',
								));
							}
						}
				?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-8">
			<div class="activity-note mt15">
				<div class="row">
					<div class="col-sm-12">
						<?php 
								echo $this->Form->input('note', array(
									'type' => 'textarea',
									'label' => __('Keterangan Aktivitas'),
					                'required' => false,
					                'div' => false,
					                'rows' => false,
					                'placeholder' => __('Catat Aktivitas yang telah/akan Anda lakukan'),
					            ));
						?>
					</div>
				</div>
			</div>
			<div class="activity-detail">
				<div class="row">
					<?php 
							echo $this->Html->tag('div', $this->Form->button($labelButton, array(
								'type' => 'submit',
								'class' => 'btn blue mb0 ',
				            )), array(
								'class' => 'col-md-4 col-sm-8 mt15',
				            ));
					?>
				</div>
			</div>
		</div>
	</div>
	<?php 
			echo $this->Form->hidden('session_id', array(
				'value' => !empty($session_id)?$session_id:false,
            ));
			echo $this->Form->end(); 
	?>
</div>