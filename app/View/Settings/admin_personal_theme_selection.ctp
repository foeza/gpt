<?php 

	$user		= empty($user) ? array() : $user;
	$records	= empty($records) ? array() : $records;

	$userID		= Common::hashEmptyField($user, 'User.id');
	$chosenID	= Common::hashEmptyField($user, 'UserConfig.theme_id');

?>
<div class="launcher-themes mt30">
	<div class="row mb30">
		<?php

			if($records){
				foreach($records as $key => $record){
					$recordID	= Common::hashEmptyField($record, 'Theme.id');
					$photo		= Common::hashEmptyField($record, 'Theme.photo');
					$name		= Common::hashEmptyField($record, 'Theme.name');
					$ownerType	= Common::hashEmptyField($record, 'Theme.owner_type');

					$photo = $this->Html->image($photo);

					if($chosenID == $recordID){
						$action = $this->Html->link(__('Terpilih'), 'javascript:void(0);', array(
							'class' => 'btn green primary',
						));
					}
					else{
						$action = $this->Html->link(__('Pilih Tema'), array(
							'admin'			=> true,
							'controller'	=> 'ajax',
							'action'		=> 'theme',
							$recordID,
							$userID, 
						), array(
							'class'			=> 'btn default ajax-link disable-drag',
							'data-type'		=> 'content',
							'data-alert'	=> __('Anda yakin ingin memilih tema ini ?'),
						));
					}

					$action = $this->Html->tag('div', $action, array(
						'class' => 'primary-file',
					));

					?>
					<div class="template-download col-sm-4">
						<div class="item">
							<?php 

								echo($this->Html->tag('div', $photo . $action, array(
									'class' => 'preview relative',
								)));

								echo($this->Html->tag('label', __($name)));

							//	if($ownerType == 'company'){

									?>
									<div class="action">
										<div class="form-group">
											<?php 

												echo($this->Html->link(__('Setting Tampilan'), array(
													'admin'			=> true,
													'controller'	=> 'settings',
													'action'		=> 'customizations',
													$recordID, 
												), array(
													'class' => 'btn blue',
												)));

											?>
										</div>
									</div>
									<?php

							//	}

							?>
						</div>
					</div>
					<?php

				}
			}

		?>
	</div>
</div>