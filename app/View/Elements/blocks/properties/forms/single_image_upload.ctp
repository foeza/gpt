<?php

	$fileupload	= isset($fileupload) ? $fileupload : true;
	$record		= empty($record) ? array() : $record;
	$sessionID	= Common::hashEmptyField($this->request->data, 'Property.session_id');

?>
<div class="row">
	<div class="form-group">
		<div class="row">
			<div class="col-xl-2 taright col-sm-3">
				<?php

					echo($this->Html->tag('label', __('Foto Properti *'), array(
						'class' => 'control-label', 
					)));

				?>
			</div>
			<div class="relative col-sm-7 col-xl-4">
				<div id="simple-info" class="property-photo-upload">
					<div class="quick-response">
						<div id="user-action">
							<div class="user-information">
								<div class="user-photo">
									<?php
											if($fileupload){
												echo($this->UploadForm->loadCustom(array(
													'admin'			=> false,
													'plugin'		=> false,
													'controller'	=> 'ajax',
													'action'		=> 'property_photo',
													'?'				=> array(
														'append_session'	=> true, 
														'session_id'		=> $sessionID, 
													)
												), array(
									                'scriptTemps'	=> 'easy_mode',
									                'loadTemps'		=> 'easy_mode',
									                'record'		=> $record,
									            )));
											}
									?>
								</div>
							</div>
						</div>
					</div>
					<?php

						echo($this->Form->error('Property.photo'));

					?>
				</div>
				<?php // echo($this->element('blocks/users/simple_info')); ?>
			</div>
		</div>
	</div>
</div>