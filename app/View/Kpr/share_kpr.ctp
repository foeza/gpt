<?php
		if( $this->theme == 'EasyLiving' ) {
			echo $this->element('blocks/common/sub_header', array(
	            'title' => __('Bagikan Info KPR'),
	        ));
		}
?>

<div id="main-share-kpr" class="main">
	<div class="container">
		<div class="row">
			<div class="col-sm-10">
				<div id="share-kpr-form">
					<?php 
							echo $this->Html->tag('h1', __('Bagikan KPR'), array(
	                            'class' => 'page-header'
	                        ));

							echo $this->Session->flash('error');
							echo $this->Form->create('SharingKpr', array(
								'url'=> $this->Html->url( null, true ), 
								'inputDefaults' => array('div' => false)
							)); 
					?>
					<div id="form-content">
						<?php 
								echo $this->Rumahku->buildFrontEndInputForm('sender_name', false, array(
					                'frameClass' => 'form-group col-sm-10',
					                'label' => __('Nama Pengirim: *'),
					            ));
								echo $this->Rumahku->buildFrontEndInputForm('receiver_name', false, array(
					                'frameClass' => 'form-group col-sm-10',
					                'label' => __('Nama Penerima: *'),
					            ));
								echo $this->Rumahku->buildFrontEndInputForm('receiver_email', false, array(
					                'frameClass' => 'form-group col-sm-10',
					                'label' => __('Email Tujuan: *'),
					            ));

								echo $this->Rumahku->buildFrontEndInputForm('receiver_phone', false, array(
					                'frameClass' => 'form-group col-sm-10',
					                'label' => __('No Telepon Tujuan: '),
					            ));
						?>

						<div class="form-group col-sm-12">
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

						<div class="form-group col-sm-12">
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
		</div>
	</div>
</div>