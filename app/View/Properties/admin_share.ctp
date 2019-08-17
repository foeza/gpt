<div id="wrapper-modal-write">
	<?php
			echo $this->Form->create('SharingProperty', array(
	            'class' => 'ajax-form',
	            'data-type' => 'content',
	            'data-wrapper-write' => '#wrapper-modal-write',
	            'data-reload' => 'true',
	        ));

	        // Set Build Input Form
	        $options = array(
	            'frameClass' => 'col-sm-12',
	            'labelClass' => 'col-xl-1 col-sm-2 taright',
	            'class' => 'relative col-sm-7 col-xl-7',
	        );

	        $result = !empty($result)?$result:false;
			$msg = $this->Rumahku->filterEmptyField($result, 'msg');
			$status = $this->Rumahku->filterEmptyField($result, 'status');

			if( $status == 'error' ) {
				echo $this->element('flash_error', array(
					'message' => $msg,
				));
			}
	?>
		<div class="content" id="property-sold">
			<?php 
					echo $this->Rumahku->buildInputForm('sender_name', array_merge($options, array(
		                'label' => __('Nama Pengirim *'),
		            )));
		            echo $this->Rumahku->buildInputForm('receiver_name', array_merge($options, array(
		                'label' => __('Nama Penerima *'),
		            )));
		            echo $this->Rumahku->buildInputForm('receiver_email', array_merge($options, array(
		                'label' => __('Email Tujuan *'),
		            )));
		            echo $this->Rumahku->buildInputForm('receiver_phone', array_merge($options, array(
		                'label' => __('No. HP Tujuan *'),
		            )));
		            echo $this->Rumahku->buildInputToggle('security_code', array_merge($options, array(
		                'label' => __('Saya Bukan Robot'),
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