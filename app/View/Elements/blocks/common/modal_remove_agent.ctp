<div class="modal fade" id="removeAgentConfirmationModal" tabindex="-1" role="dialog">
  	<div class="modal-dialog" role="document">
    	<div class="modal-content">
	      	<?php
        		echo $this->Form->create('UserRemoveAgent');
        	?>
		      	<div class="modal-header">
		        	<?php
		        		echo $this->Form->button(__('&times;'), array(
		                    'type' => 'button', 
		                    'class'=> 'close',
		                    'data-dismiss' => 'modal',
		                    'aria-label' => 'close'
		                ));
		        	?>
		        	<?php
		        		echo $this->Html->tag('h4', __('Konfirmasi Hapus Agen'), array(
		                    'class'=> 'modal-title',
		                    'id' => 'exampleModalLabel',
		                ));
		        	?>
		      	</div>
		      	<div class="modal-body">
			    	<?php
			    		echo $this->Rumahku->buildForm('agent_id', false, array(
			    			'id' => 'hdnAgentId',
			    			'type' => 'hidden',
	                    ));

	                    echo $this->Rumahku->buildForm('reason_principle', __('Reason'), array(
	                        'type' => 'textarea',
	                        'placeholder' => __('Tuliskan alasan mengapa Anda ingin menghapus agen disini'),
	                        'class' => 'form-control',
	                    ));
	                ?>
		      	</div>
		      	<div class="modal-footer">
		      		<?php
						echo $this->Form->button(__('Hapus Agen'), array(
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