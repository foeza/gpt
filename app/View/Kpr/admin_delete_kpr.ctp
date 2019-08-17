<?php
    $mandatory = $this->Html->tag('div',__('*'), array(
        'class' => 'color-red floright',
    ));
?>

<div id="wrapper-modal-write">
	<?php
			echo $this->Form->create('KprBankDate', array(
	            'class' => 'ajax-form',
	            'data-type' => 'content',
	            'data-wrapper-write' => '#wrapper-modal-write',
	            'data-reload' => 'true',
	            // 'data-alert' => __('Anda yakin ingin membatalkan aplikasi KPR ini ?'),
	        ));

	        // Set Build Input Form
	        $options = array(
	            'frameClass' => 'col-sm-12',
	            'labelClass' => 'col-xl-3 col-sm-3',
	            'class' => 'relative col-sm-9 col-xl-7',
	        );
	?>
	<div class="content kpr-commission-payment" id="property-sold">
        <?php
				echo $this->Rumahku->buildInputForm('note', array_merge($options, array(
                    'label' => sprintf(__('Alasan & Keterangan %s'), $mandatory),
                    'type' => 'textarea',
                    'deafult' => __('Aplikasi telah dibatalkan oleh Agen'),
                    'class' => 'relative col-sm-7 col-xl-7',
                    'fieldError' => 'KprBankDate.0.note',
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