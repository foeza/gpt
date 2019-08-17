<?php
		$_global_variable = !empty($_global_variable)?$_global_variable:false;
		$default_theme_settings = $this->Rumahku->filterEmptyField($_global_variable, 'theme_custom_badge');
        
        echo $this->Form->create('PropertyStatusListing');
?>
<div class="user-fill">
    <?php
            echo $this->Rumahku->buildInputForm('name', array(
                'label' => __('Nama Kategori *'),
            ));

            echo $this->Rumahku->fieldColorPicker('badge_color', __('Warna Badge'), array(
            	'labelClass' => 'col-xl-2 col-sm-4 control-label taright',
            	'class' => 'relative col-md-4 col-xs-12',
            	'defaultClass' => 'col-md-4 col-xs-12',
				'dataField' => 'badge_color',
				'dataDefault' => $default_theme_settings,
			));
    ?>
</div>

<div class="row">
	<div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
				<?php
			            echo $this->Html->link(__('Kembali'), array(
							'action' => 'status_listing_categories',
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