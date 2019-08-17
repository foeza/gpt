<div id="wrapper-modal-write">
	<?php
			echo $this->Form->create('UserIntegratedConfig', array(
	            'class' => 'ajax-form',
	            'data-type' => 'content',
	            'data-wrapper-write' => '#wrapper-modal-write',
	            'data-reload' => 'true',
	        ));

	        // Set Build Input Form
	        $options = array(
	            'frameClass' => 'col-sm-12',
	            'labelClass' => 'col-xl-2 col-sm-2 taright',
	            'class' => 'relative col-sm-9 col-xl-7',
	        );
	?>	
		<div class="content" id="property-sold">
			<?php
					echo $this->Rumahku->buildInputForm('email_sync', array(
		                'frameClass' => 'col-sm-12',
		                'label' => __('Email'),
		                'type' => 'text',
		                'labelClass' => 'col-xl-2 taright col-sm-3',
		                'class' => 'relative col-sm-6 col-xl-4',
		                'infoText' => __('masukan email akun yang telah didaftarkan'),
		                'infoClass' => 'extra-text',
		            ));
			?>
		</div>
		<div class="modal-footer">
			<?php 
					echo $this->Html->link(__('Batal'), '#', array(
	    	            'class' => 'close btn default',
	    	            'data-dismiss' => 'modal',
	    	            'aria-label' => 'close',
	    	        ));
					echo $this->Form->button(__('Submit'), array(
	    	            'class' => 'btn blue',
	    	        ));
			?>
		</div>
	<?php 
	        echo $this->Form->end();
	?>
</div>