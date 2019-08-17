<?php
		$mandatory = $this->Html->tag('span',__('*'), array(
    		'class' => 'color-red',
    	));
?>
<div id="wrapper-modal-write">
	<?php
			echo $this->Form->create('KprBank', array(
	            'class' => 'ajax-form',
	            'data-type' => 'content',
	            'data-wrapper-write' => '#wrapper-modal-write',
	            'data-reload' => 'true',
	            // 'data-alert' => __('Aplikasi akan di proses dan dikirimkan ke bank'),
	        ));

	        // Set Build Input Form
	        $options = array(
	            'frameClass' => 'col-sm-12',
	            'labelClass' => 'col-xl-2 col-sm-3',
	            'class' => 'relative col-sm-9 col-xl-7',
	        );
	?>
	<div class="content kpr-commission-payment">
		<?php
				echo $this->Rumahku->buildInputForm('sales_id', array_merge($options, array(
                    'type' => 'select',
                    'label' => __('Marketing Bank'),
                    'class' => 'relative col-sm-9 col-xl-10',
                    'inputClass' => 'centered form-control sales-bank chosen-select',
                    'empty' => __('Marketing Bank (Optional)'),
                    'attributes' => array(
                		'options' => !empty($sales)?$sales:false,
                    ),
                )));
				echo $this->Rumahku->buildInputForm('noted', array_merge($options, array(
                    'label' => __('Keterangan'),
                    'placeholder' => __('Masukkan keterangan tambahan untuk bank jika diperlukan'),
                    'type' => 'textarea',
                    'class' => 'relative col-sm-9 col-xl-10',
                    'attributes' => array(
                    	'cols' => '3',
                    	'rows' => '3',
                    ),
                    'inputClass' => 'centered',
                    'infoText' => __('* Kosongkan untuk lanjut tanpa keterangan tambahan'),
                    'infoClass' => 'info-forward',
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