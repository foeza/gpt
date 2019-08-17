<?php
			$parent_id = Common::hashEmptyField($value, 'User.parent_id');
			$group_id = Common::hashEmptyField($value, 'User.group_id');
			$name = Common::hashEmptyField($value, 'User.full_name');
			$user_id = Common::hashEmptyField($value, 'User.id');

			echo $this->element('blocks/users/forms/agent');
?>
<div id="modal-remove-agent" class="modal-subheader transparent">
	<div id="wrapper-modal-write">
		<?php
				echo $this->Form->create('UserActivedAgent', array(
		            'class' => 'ajax-form',
		            'data-type' => 'content',
		            'data-wrapper-write' => '#wrapper-modal-write',
		            'data-reload' => 'true',
		            // 'data-alert' => __('Anda yakin ingin menonaktifkan agen %s', $name),
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
					if($group_id == '2'){
						echo $this->Rumahku->buildInputForm('agent_email', array_merge($options, array(
							'type' => 'text',
			                'label' => __('Alihkan semua data ke agen *'),
			                'id' => 'autocomplete',
			                'attributes' => array(
	                    		'autocomplete' => 'off',
	                    		'data-ajax-url' => $this->Html->url(array(
				                    'controller' => 'ajax',
				                    'action' => 'list_users',
				                    'admin' => false,
				                    2,
				                    'parent_id' => $parent_id,
				                    'user_id' => $user_id,
				                    'document_status' => 'active',
				                    'type' => 'active-inactive',
				                )),
	                		),
			            )));
			            $textInfo = __('* Perlu diingat bahwa ketika anda menonaktifkan agen, maka seluruh data agen yang dinonaktifkan akan berpindah kepemilikannya kepada agen yang anda masukkan pada kolom "Alihkan semua data ke agen"');
					} else {
						$textInfo = __('* user yang dinonaktifkan hanya bersifat sementara, anda bisa aktifkan kembali pada kolom "AKTIF"');
					}
		            
					echo $this->Rumahku->buildInputForm('reason', array_merge($options, array(
						'type' => 'textarea',
		                'label' => __('Alasan Non-Aktif *'),
		            )));

					echo $this->Html->tag('strong', $textInfo);
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