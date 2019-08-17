<?php
	$options = array(
		'frameClass'	=> 'col-sm-8',
		'labelClass'	=> 'col-sm-4',
		'class'			=> 'relative col-sm-8 col-xl-7',
	);

	echo($this->Form->create('User'));
?>
<div class="row">
	<div class="col-sm-12">
		<?php

			echo($this->Rumahku->buildInputForm('new_password', array_merge($options, array(
				'label'			=> __('Password Baru *'),
				'type'			=> 'password',
				'autocomplete'	=> 'off',
			))));

			echo($this->Rumahku->buildInputForm('new_password_confirmation', array_merge($options, array(
				'label'			=> __('Konfirmasi Password Baru *'),
				'type'			=> 'password',
				'autocomplete'	=> 'off',
			))));

		?>
	</div>
</div>
<div class="row">
	<div class="col-sm-12">
		<div class="action-group bottom">
			<div class="btn-group">
				<?php

					echo($this->Form->button(__('Simpan'), array(
						'type'	=> 'submit', 
						'class'	=> 'btn blue',
					)));

				?>
			</div>
		</div>
	</div>
</div>
<?php
	echo($this->Form->end());
?>