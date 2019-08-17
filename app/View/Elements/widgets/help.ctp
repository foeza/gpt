<?php
		$subjects = Configure::read('Global.Data.subjects');
		$userPhoto = $this->Rumahku->filterEmptyField($User, 'photo');
		$userFullName = $this->Rumahku->filterEmptyField($User, 'full_name');
		$userEmail = $this->Rumahku->filterEmptyField($User, 'email');
		$userPhone = $this->Rumahku->filterEmptyField($User, 'phone');
		$_open = !empty($_open)?'open':'';

		echo $this->Form->create('Contact', array(
            'class' => 'ajax-form',
            'data-type' => 'content',
            'data-wrapper-write' => '#wrapper-widget-write',
            'data-reload' => 'true',
            'url' => array(
            	'controller' => 'ajax',
	            'action' => 'contact',
	            'admin' => true,
            ),
        ));
?>

<div id="wrapper-widget-write" class="hidden-print">
	<div id="help">
		<?php
				echo $this->Html->link(__('Bantuan Cepat'), '#', array(
					'id' => 'open-message',
					'class' => 'btn orange'
				));
		?>	
		<div id="message" class="<?php echo $_open; ?>">
			<div class="head">
				<?php
						echo $this->Html->tag('div', __('Kirim Pesan'), array(
							'class' => 'title'
						));
						echo $this->Html->link(__('x'), '#', array(
							'class' => 'min'
						));
				?>	
			</div>
			<div class="content">
				<form action="">
					<div class="user-info">
						<?php
								echo $this->Html->tag('div', $this->Rumahku->photo_thumbnail(array(
					                'save_path' => Configure::read('__Site.profile_photo_folder'), 
					                'src'=> $userPhoto, 
					                'size' => 'pl',
					            ), array(
					            	'title' => $userFullName,
					            	'alt' => $userFullName,
					            )), array(
					            	'class' => 'photo',
					            ));
						?>
						<div class="info">
							<?php
									echo $this->Html->tag('span', $userFullName, array(
										'class' => 'name'
									));
									echo $this->Html->tag('span', $userEmail, array(
										'class' => 'email'
									));
									echo $this->Html->tag('span', $userPhone, array(
										'class' => 'phone'
									));
							?>
						</div>
					</div>
					<?php
							echo $this->Form->input('subject', array(
		                        'options' => $subjects,
		                        'label' => __('Pilih Subjek'),
		                        'div' => array(
		                            'class' => 'form-group'
		                        ),
		                    ));
		                    echo $this->Form->input('message', array(
		                        'type' => 'textarea',
		                        'cols' => 10,
		                        'rows' => 10,
		                        'required' => false,
		                        'label' => __('Pesan'),
		                        'div' => array(
		                            'class' => 'form-group'
		                        ),
		                    ));
					?>
			</div>
			<div class="foot">
				<?php
						echo $this->Form->button(__('Kirim'), array(
		    	            'class' => 'btn orange',
		    	        ));
				?>
			</div>
		</div>
	</div>
	<?php
			echo $this->Form->end();
	?>
</div>