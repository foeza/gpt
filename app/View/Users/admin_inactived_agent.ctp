<?php
			$name = Common::hashEmptyField($value, 'User.full_name');
			$group_id = Common::hashEmptyField($value, 'User.group_id');

			echo $this->element('blocks/users/forms/agent', array(
				'inactive' => true,
			));
?>
<div id="modal-remove-agent" class="modal-subheader transparent">
	<div id="wrapper-modal-write">
		<?php
				echo $this->Form->create('UserActivedAgent', array(
		            'class' => 'ajax-form',
		            'data-type' => 'content',
		            'data-wrapper-write' => '#wrapper-modal-write',
		            'data-reload' => 'true',
		            // 'data-alert' => __('Anda yakin ingin aktifkan kembali agen %s', $name),
		        ));

		        // Set Build Input Form
		        $options = array(
		            'frameClass' => 'col-sm-12',
		            'labelClass' => 'col-xl-2 col-sm-4',
		            'class' => 'relative col-sm-8 col-xl-7',
		        );
		?>
		<div class="content">
			<?php
		            echo $this->Rumahku->buildInputForm('rollback_reason', array_merge($options, array(
						'type' => 'textarea',
		                'label' => __('Alasan diaktifkan kembali *'),
		            )));

		            if($group_id == '2'){
						echo $this->Rumahku->buildInputToggle('is_rollback', array_merge($options, array(
			                'label' => __('Kembalikan data properti & klien'),
			                'class' => 'relative col-sm-8 col-xl-6 large',
			                'infoText' => __('* Aktifkan centang apabila anda ingin mengembalikan data (properti dan klien) sebelumnya kepada agen ini.'),
			                'infoTextStyle' => 'font-style: italic;',
			                'infoClass' => false,
			                // 'attributes' => array(
			                // 	'default' => true,
			                // ),
			            )));
		            }
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
</div>