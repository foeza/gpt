<div class="account">
	<div class="row">
		<div class="col-sm-3">
			<?php
				echo $this->element('sidebars/left_sidebar_menu');
			?>
		</div>

		<div class="col-sm-9">
			<?php
				echo $this->Html->tag('div', $this->Html->tag('h3', __('Ganti Password')), array(
					'class' => 'page-header'
				))
			?>
			<div class="row">
				<div class="col-sm-12">
					<?php
						echo $this->Form->create('User');

						echo $this->Rumahku->buildForm('current_password', __('Password Lama *'), array(
	                        'type' => 'password',
	                        'value' => '',
	                        'autocomplete' => 'off',
	                        'class' => 'form-control',
	                    ));

						echo $this->Rumahku->buildForm('new_password', __('Password Baru *'), array(
							'type' => 'password',
	                        'value' => '',
	                        'autocomplete' => 'off',
	                        'class' => 'form-control',
	                    ));

						echo $this->Rumahku->buildForm('new_password_confirmation', __('Konfirmasi Password *'),array(
							'type' => 'password',
	                        'value' => '',
	                        'autocomplete' => 'off',
	                        'class' => 'form-control',
	                    ));
	                ?>
				    <div class="form-group text-right">
						<?php
							echo $this->Form->button(__('Ganti Password'), array(
			                    'type' => 'submit', 
			                    'class'=> 'btn btn-default',
			                ));
						?>
					</div>

					<?php 
						echo $this->Form->end(); 
					?>
				</div>
			</div>
		</div>
	</div>
</div>