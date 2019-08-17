<div class="account">
	<div class="row">
		<div class="col-sm-3">
			<?php
				echo $this->element('sidebars/left_sidebar_menu');
			?>
		</div>

		<div class="col-sm-9">
			<?php
				echo $this->Html->tag('div', $this->Html->tag('h3', __('Ganti Email')), array(
					'class' => 'page-header'
				))
			?>
			<div class="row">
				<div class="col-sm-12">
					<?php
						echo $this->Form->create('User');

						echo $this->Rumahku->buildForm('email', __('Email *'),array(
	                        'type' => 'text',
	                        'value' => $user['User']['email'],
	                        'class' => 'form-control',
	                    ));

	                    echo $this->Html->tag('p', __('contoh: myaccount@gmail.com'), array(
	                        'class' => 'help-block',
	                    ));
	                ?>

				    <div class="form-group text-right">
						<?php
							echo $this->Form->button(__('Ganti Email'), array(
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