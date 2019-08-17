<div id="wrapper-modal-write">
	<div id="share-kpr-form">
		<?php 
				$result = !empty($result)?$result:false;
				$msg = $this->Rumahku->filterEmptyField($result, 'msg');
				$status = $this->Rumahku->filterEmptyField($result, 'status');

				$bank_code = $this->Rumahku->filterEmptyField($log_kpr, 'Bank', 'code');
				$mls_id = $this->Rumahku->filterEmptyField($log_kpr, 'LogKpr', 'mls_id');
				$credit_fix = $this->Rumahku->filterEmptyField($log_kpr, 'LogKpr', 'credit_fix');
				$credit_float = $this->Rumahku->filterEmptyField($log_kpr, 'LogKpr', 'credit_float');
				$interest_rate = $this->Rumahku->filterEmptyField($log_kpr, 'LogKpr', 'interest_rate');

				$bank_code = strtolower($bank_code);
				$credit_total = $credit_fix + $credit_float;

				if( $status == 'error' ) {
					echo $this->element('flash_error', array(
						'message' => $msg,
					));
				}

				echo $this->Html->tag('div', $status, array(
					'id' => 'msg-status',
					'class' => 'hide',
				));

				echo $this->Form->create('SharingKpr', array(
					'url'=> $this->Html->url( null, true ), 
					'inputDefaults' => array('div' => false),
					'class' => 'ajax-form',
		            'data-wrapper-write' => '#wrapper-modal-write',
	            	'data-reload' => 'true',
	            	'data-type' => 'content',
	            	'data-reload-url' => $this->Html->url(array(
	            		'controller' => 'kpr', 
						'action' => 'bank_calculator',
						'slug' => 'kalkulator-kpr',
						'bank_code' => $bank_code,
						'mls_id' => $mls_id,
						$credit_total,
						$interest_rate,
	        		)),
				)); 
		?>
		<div id="form-content">
			<?php
					echo $this->Rumahku->buildFrontEndInputForm('sender_name', false, array(
		                'frameClass' => 'form-group',
		                'label' => __('Nama Pengirim: *'),
		            ));
					echo $this->Rumahku->buildFrontEndInputForm('receiver_name', false, array(
		                'frameClass' => 'form-group',
		                'label' => __('Nama Penerima: *'),
		            ));
					echo $this->Rumahku->buildFrontEndInputForm('receiver_email', false, array(
		                'frameClass' => 'form-group',
		                'label' => __('Email Tujuan: *'),
		            ));

					echo $this->Rumahku->buildFrontEndInputForm('receiver_phone', false, array(
		                'frameClass' => 'form-group',
		                'label' => __('No Telepon Tujuan: '),
		            ));
			?>
			
			<div class="form-group">
		        <div class="checkbox">
		            <label>
		                <?php   
		                        echo $this->Form->input('security_code',array(
		                            'type' => 'checkbox',
		                            'label'=> false,
		                            'required' => false,
		                            'class' => false, 
		                            'required' => false,
		                            'div' => false,
		                            'value' => $captcha_code,
		                        ));
		                        echo __('Saya bukan robot');
		                ?>
		            </label>
		        </div>
		    </div>
			<div class="form-group">
				<?php
						echo $this->Form->button(__('Kirim'), array(
							'div' => false, 
							'id' => 'btn-submit-form',
							'type_action' => 'share_kpr',
							'class'=> 'btn btn-success',
							'type' => 'submit'
						));
				?>
			</div>
		</div>
		<?php 
				echo $this->Form->end();
		?>
	</div>
</div>