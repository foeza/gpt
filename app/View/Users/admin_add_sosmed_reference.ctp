<?php
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-2 col-sm-2 control-label taright',
            'class' => 'relative col-sm-6 col-xl-6',
        );

        echo $this->Form->create('UserClientSosmedReference', array(
            'type' => 'file',
        ));
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('name', array_merge($options, array(
				'placeholder' => 'Contoh: German Expo 2017',
				'label' => __('Nama Sosmed *'),
            )));
            echo $this->Rumahku->buildInputForm('url', array_merge($options, array(
				'placeholder' => 'Contoh: http://web.facebook.com/example-event-url',
				'label' => __('URL Sosmed *'),
            )));
            echo $this->Rumahku->buildInputToggle('active', array_merge($options, array(
                'label' => __('Status'),
            )));
    ?>
</div>

<div class="row">
	<div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
				<?php
    		            echo $this->AclLink->link(__('Kembali'), array(
    						'action' => 'sosmed_reference',
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