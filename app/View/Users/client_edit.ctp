<?php
		$genderDefault = $this->Rumahku->filterEmptyField($this->request->data, 'User', 'gender_id');
		$subareas = !empty($subareas)?$subareas:false;

        echo $this->element('blocks/users/simple_info');
        echo $this->Html->tag('h2', __('Informasi Dasar'), array(
        	'class' => 'sub-heading'
        ));

        echo $this->Form->create('User');
?>

<div class="row">
    <div class="col-sm-12">
		<?php
                echo $this->element('blocks/users/forms/profile', array(
                    'genderDefault' => $genderDefault,
                    'modelName' => 'UserProfile',
                    'user_type' => 'client',
                ));
    			echo $this->Html->tag('h2', __('Alamat'), array(
    	        	'class' => 'sub-heading'
    	        ));
			//	echo $this->element('blocks/users/forms/address', array(
			//		'modelName' => 'UserProfile',
			//	));

    			echo $this->element('blocks/users/forms/address', array(
					'modelName'				=> 'UserProfile',
					'use_location_picker'   => true, 
	            ));

                echo $this->Html->tag('h2', __('Informasi Kontak'), array(
                    'class' => 'sub-heading'
                ));
                echo $this->element('blocks/users/forms/contact_info', array(
                    'modelName' => 'UserProfile'
                ));
		?>
	</div>
</div>

<div class="row">
    <div class="col-sm-6 col-sm-offset-2 extra-margin-left">
        <div class="action-group bottom">
            <div class="btn-group floleft">
                <?php
                    echo $this->Form->button(__('Simpan Perubahan'), array(
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