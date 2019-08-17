<?php
		$message_info = !empty($message_info)?$message_info:false;

		$mandatory = $this->Html->tag('span', '*', array(
            'class' => 'error-message', 
        ));
?>

<div id="wrapper-modal-write">
	<?php
			echo $this->Form->create('KprBankDate', array(
	            'class' => 'ajax-form',
				'data-type' => 'content',
	            'data-wrapper-write' => '#wrapper-modal-write',
	            'data-reload' => 'true',
	        ));

	        // Set Build Input Form
	        $options = array(
	            'frameClass' => 'col-sm-12',
	            'labelClass' => 'col-xl-3 col-sm-3',
	            // 'class' => 'relative col-sm-9 col-xl-7',
	        );
	?>
	<div class="content" id="property-sold">
		<?php
				echo $this->Html->tag('div', 
                    $this->Html->tag('div', $this->Html->tag('label', __('INFO')).$this->Html->tag('p', $message_info), array(
                    	'class' => 'wrapper-alert',
                    )), array(
                    	'class' => 'crm-tips kpr-alert',
                ));

                echo $this->Rumahku->buildInputForm('process_date', array_merge( $options, array(
	                'label' => sprintf(__('Tanggal Akad %s'), $mandatory),
	                'type' => 'text',
	                'inputClass' => 'datepicker',
	                // 'default' => date('d/m/Y'),
	                'class' => 'relative col-sm-4 col-xl-4',
	            )));

	            echo $this->Rumahku->buildInputForm('process_time', array_merge( $options, array(
	                'label' => sprintf(__('Jam Akad %s'), $mandatory),
	                'type' => 'text',
	                'inputClass' => 'timepicker',
	                // 'default' => date('H:i:s'),
	                'class' => 'relative col-sm-2 col-xl-2',
	            )));

	            echo $this->Rumahku->buildInputForm('note', array_merge( $options, array(
	                'label' => sprintf(__('Keterangan')),
	                'type' => 'textarea',
	                'rows' => 3,
	                'class' => 'relative col-sm-8 col-xl-8',
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