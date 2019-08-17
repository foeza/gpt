<?php
        // Set Build Input Form
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-2 col-sm-2 taright',
            'class' => 'relative col-sm-9 col-xl-7',
        );
		echo $this->Form->create('UserClientSosmedReference', array(
			'class' => 'form-client-addsosmed',
		));
?>
<div id="wrapper-modal-write" class="wrapper-client-addsosmed">
	<div class="content" id="property-sold">
		<?php
				echo $this->Rumahku->buildInputForm('name', array_merge($options, array(
					'type' => 'text',
	                'label' => __('Nama Sosmed'),
					'placeholder' => 'Contoh: German Expo 2017',
	            )));
				echo $this->Rumahku->buildInputForm('url', array_merge($options, array(
					'type' => 'text',
	                'label' => __('URL Sosmed'),
					'placeholder' => 'Contoh: http://web.facebook.com/example-event-url',
	            )));
		?>
	</div>
	<div class="modal-footer">
		<?php 
				echo $this->Html->link(__('Batal'), '#', array(
    	            'class' => 'close btn default close-modal',
    	            'data-dismiss' => 'modal',
    	            'aria-label' => 'close',
    	        ));
				echo $this->Html->link(__('Simpan'), $this->here, array(
    	            'class' => 'btn blue ajax-link',
					'data-form' => '.form-client-addsosmed',
					'data-wrapper-write-page' => '.sosmed-id-placeholder,.wrapper-client-addsosmed',
					'data-on-click' => '[[\'.sosmed-id-placeholder\', \'.close-modal\']]',
    	        ));
		?>
	</div>
</div>
<?php
		echo $this->Form->end();
?>