<?php
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-2 col-sm-2 control-label taright',
            'class' => 'relative col-sm-6 col-xl-6',
        );

        echo $this->Form->create('Career');
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('name', array_merge($options, array(
                'label' => __('Jenis Pekerjaan *'),
            )));
            echo $this->Rumahku->buildInputForm('email', array_merge($options, array(
                'type' => 'text',
                'label' => __('Email Tujuan *'),
            )));
            echo $this->Rumahku->buildInputForm('description', array_merge($options, array(
                'label' => __('Deskripsi Pekerjaan *'),
                'inputClass' => 'ckeditor',
                'class' => 'relative col-sm-10 col-xl-6 large',
            )));
            echo $this->element('blocks/common/multiple_forms', array(
                'modelName' => 'CareerRequirement',
                'labelName' => __('Kualifikasi'),
                'divClassTop' => 'col-sm-8 col-sm-offset-2',
                'divClass' => 'col-sm-8 col-sm-offset-2',
            ));
    ?>
</div>
		
<div class="row">
	<div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
				<?php
                        echo $this->Html->link(__('Kembali'), array(
                            'action' => 'careers',
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