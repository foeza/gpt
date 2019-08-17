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

		            echo $this->Rumahku->buildInputMultiple('live_date', 'end_date', array(
		                'label' => sprintf(__('Masa Tayang')),
		                'divider' => 'rv4-bold-min small',
		                'inputClass' => 'datepicker',
		                'inputClass2' => 'to-datepicker',
		                'frameClass' => 'col-sm-12',
		                'labelDivClass' => 'col-xl-2 col-sm-2',
		                'attributes' => array(
		            		'type' => 'text',
	                	),
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
					echo $this->Form->button(__('Simpan'), array(
	    	            'class' => 'btn blue',
	    	        ));
			?>
		</div>
	<?php 
	        echo $this->Form->end();
	?>
</div>