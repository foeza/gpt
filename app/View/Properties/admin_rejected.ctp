<div id="wrapper-modal-write">
	<?php
			echo $this->Form->create('PropertyNotification', array(
	            'class' => 'ajax-form',
	            'data-type' => 'content',
	            'data-wrapper-write' => '#wrapper-modal-write',
	            'data-reload' => 'true',
	            'data-alert' => __('Anda yakin ingin menolak properti ini ?'),
	        ));

	        // Set Build Input Form
	        $options = array(
	            'frameClass' => 'col-sm-12',
	            'labelClass' => 'col-xl-2 col-sm-2',
	            'class' => 'relative col-sm-9 col-xl-7',
	        );
        	$periodOptions = Configure::read('__Site.periode_options');
	?>
		<div class="content" id="property-sold">
			<?php 
					echo $this->Rumahku->buildInputForm('message', array_merge($options, array(
						'type' => 'textarea',
		                'label' => __('Keterangan'),
		            )));
			?>
		</div>
		<div class="modal-footer">
			<?php 
					echo $this->Html->link(__('Batal'), '#', array(
	    	            'class' => 'close btn default',
	    	            'data-dismiss' => 'modal',
	    	            'aria-label' => 'close',
	    	        ));
					echo $this->Form->button(__('Simpan'), array(
	    	            'class' => 'btn blue',
	    	        ));
			?>
		</div>
	<?php 
	        echo $this->Form->end();
	?>
</div>