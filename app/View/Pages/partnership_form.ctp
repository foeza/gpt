<?php
        $data = $this->request->data;
        $save_path = Configure::read('__Site.logo_photo_folder');

        $logo = $this->Rumahku->filterEmptyField($data, 'Partnership', 'photo_hide');
        $logoSize = $this->Rumahku->_rulesDimensionImage($save_path, 'large', 'size');

        echo $this->Form->create('Partnership', array(
            'type' => 'file',
        ));
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('photo', array(
                'type' => 'file',
                'label' => sprintf(__('Foto Partnership ( %s ) *'), $logoSize),
                'preview' => array(
                    'photo' => $logo,
                    'save_path' => $save_path,
                    'size' => 'xxsm',
                ),
            ));
            echo $this->Rumahku->buildInputForm('title', array(
                'label' => __('Nama Partner *'),
            ));
            echo $this->Rumahku->buildInputForm('url', array(
                'type' => 'text',
                'label' => __('URL'),
                'infoText' => __('Masukkan URL lengkap menggunakan HTTP://'),
            ));
    ?>
</div>

<div class="row">
	<div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
				<?php
                        echo $this->Html->link(__('Kembali'), array(
                            'action' => 'partnerships',
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