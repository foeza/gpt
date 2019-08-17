<?php
        $data = $this->request->data;
        $save_path = Configure::read('__Site.advice_photo_folder');

        $logo = $this->Rumahku->filterEmptyField($data, 'Advice', 'photo_hide');
        $logoSize = $this->Rumahku->_rulesDimensionImage($save_path, 'large', 'size');
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-2 col-sm-2 control-label taright',
            'class' => 'relative col-sm-6 col-xl-6',
        );

        echo $this->Form->create('Advice', array(
            'type' => 'file',
        ));
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('advice_category_id', array_merge($options, array(
                'label' => __('Kategori *'),
                'empty' => __('Pilih Kategori'),
                'options' => $advice_categories,
            )));
            echo $this->Rumahku->buildInputForm('title', array_merge($options, array(
                'label' => __('Judul *'),
            )));
            echo $this->Rumahku->buildInputForm('photo', array_merge($options, array(
                'type' => 'file',
                'label' => sprintf(__('Foto Utama ( %s ) *'), $logoSize),
                'preview' => array(
                    'photo' => $logo,
                    'save_path' => $save_path,
                    'size' => 'm',
                ),
            )));
            echo $this->Rumahku->buildInputForm('short_content', array_merge($options, array(
				'id' => 'desc-info',
                'type' => 'textarea',
                'label' => __('Keterangan Singkat *'),
				'data_max_lenght' => 200,
                'infoText' => sprintf(__('Limit Karakter : %s'), $this->Html->tag('span', '', array(
                	'class' => 'limit-character'
                ))),
				'infoClass' => ''
            )));
            echo $this->Rumahku->buildInputForm('content', array_merge($options, array(
                'label' => __('Deskripsi *'),
                'inputClass' => 'ckeditor',
                'class' => 'relative col-sm-10 col-xl-6 large',
            )));

			?>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-12">
						<div class="row">
							<div class="col-xl-2 col-sm-2 control-label taright">
								<label class="control-label" for="AdviceMetaTitle">Judul Meta</label>
							</div>
							<div class="relative col-sm-6 col-xl-6">
								<?php

									echo($this->Form->input('meta_title', array(
										'type'				=> 'text', 
										'class'				=> 'form-control input-counter', 
										'maxlength'			=> 60, 
										'label'				=> false, 
										'div'				=> false, 
										'data-container'	=> '#meta-title-counter', 
									)));

								?>
								<small>
									<span>
										Limit Karakter : 
										<span id="meta-title-counter"></span>
									</span>
								</small>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-12">
						<div class="row">
							<div class="col-xl-2 col-sm-2 control-label taright">
								<label class="control-label" for="AdviceMetaDescription">Deskripsi Meta</label>
							</div>
							<div class="relative col-sm-6 col-xl-6">
								<?php

									echo($this->Form->input('meta_description', array(
										'type'				=> 'textarea', 
										'class'				=> 'form-control input-counter form-control', 
										'maxlength'			=> 320, 
										'label'				=> false, 
										'div'				=> false, 
										'data-container'	=> '#meta-description-counter', 
									)));

								?>
								<small>
									<span>
										Limit Karakter : 
										<span id="meta-description-counter"></span>
									</span>
								</small>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php

            echo $this->Rumahku->buildInputForm('order', array_merge($options, array(
                'type' => 'number',
                'label' => __('Order'),
            )));
            echo $this->Rumahku->buildInputToggle('active', array_merge($options, array(
                'label' => __('Status *'),
            )));
    ?>
</div>

<div class="row">
	<div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
				<?php
    		            echo $this->AclLink->link(__('Kembali'), array(
    						'action' => 'index',
    						'admin' => true
    					), array(
    						'class'=> 'btn default',
    					));
                        echo $this->Form->button(__('Simpan'), array(
                            'type' => 'submit', 
                            'class'=> 'btn blue',
                        ));
				?>
			</div>
		</div>
	</div>
</div>

<?php 
	echo $this->Form->end(); 
?>