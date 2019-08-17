<?php
		$li = false;
		$commission = Common::hashEmptyField($value, 'KprBankInstallment.commission');
?>
<div id="wrapper-modal-write">
	<?php
			echo $this->Form->create('KprBank', array(
	            'class' => 'ajax-form',
	            'data-type' => 'content',
	            'data-wrapper-write' => '#wrapper-modal-write',
	            'data-reload' => 'true',
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

				$flag = $this->Form->input('flag', array(
					'type' => 'hidden',
					'value' => true,
				));

				$text_approve_pk = $this->Html->tag('strong', __('Setujui Akad'));
				
				if($commission){
					$text_illustration = __('* %s pengajuan klaim hanya dapat dilakukan secara offline.', $text_approve_pk);
					$text_illustration = $this->Html->tag('div', $text_illustration, array(
						'class' => 'illustration'
					));

					$toggle = $this->Rumahku->buildInputToggle('send_email', array(
						'data-height' => '20px',
					));

					$li = $this->Html->tag('li', __('Centang ajukan klaim untuk pengajuan klaim provisi, sistem kami tidak menghandle pengajuan klaim setelah Anda lakukan %s. %s %s', $text_approve_pk, $text_illustration, $toggle));

				}

				echo $this->Html->tag('div', 
                    $this->Html->tag('div', $this->Html->tag('label', __('INFO')).$this->Html->tag('ul', $li), array(
                        'class' => 'wrapper-alert',
                    )), array(
                    	'class' => 'crm-tips kpr-alert',
                	)
                );
                echo $flag;
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