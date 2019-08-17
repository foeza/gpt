<?php
		$devices 	= $this->Rumahku->mobileAppVersionConfig('device');
		$types 		= $this->Rumahku->mobileAppVersionConfig('type');

        echo $this->Form->create('MobileAppVersion');
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('appversion', array(
                'label' => __('App Version *'),
            ));

            echo $this->Rumahku->buildInputForm('version_code', array(
                'label' => __('Code Version *'),
            ));

            echo $this->Rumahku->buildInputForm('device', array(
                'label' => __('Device *'),
                'options' => $devices,
            ));

            echo $this->Rumahku->buildInputForm('type', array(
                'label' => __('Tipe Update *'),
                'options' => $types,
            ));

            echo $this->Rumahku->buildInputForm('message', array(
                'label' => __('Pesan *'),
            ));

            echo $this->Rumahku->buildInputForm('detail_message', array(
                'label' => __('Detail Pesan'),
            ));

            echo $this->Rumahku->buildInputForm('link', array(
                'label' => __('Link'),
                'type' => 'text'
            ));
    ?>
</div>

<div class="row">
	<div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
				<?php
			            echo $this->Html->link(__('Kembali'), array(
							'action' => 'mobile_app_versions',
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