<?php
        $genderDefault = $this->Rumahku->filterEmptyField($this->request->data, 'User', 'gender_id');
		$genderOptions = !empty($_global_variable['gender_options'])?$_global_variable['gender_options']:false;
		$options = !empty($options)?$options:array(
            'frameClass' => 'col-sm-12 col-md-8',
        );
        $urlBack = !empty($urlBack)?$urlBack:array(
            'controller' => 'users',
            'action' => 'admins',
            'admin' => true,
        );

        echo $this->Html->tag('h2', __('Informasi Dasar'), array(
        	'class' => 'sub-heading'
        ));

        echo $this->Form->create('User', array(
            'type' => 'file',
        ));
?>

<div class="row">
    <div class="col-sm-12">  	
		<?php
            echo $this->element('blocks/users/forms/profile', array(
                'user_type' => 'Admin',
                'manualUploadPhoto' => true,
                '_email' => true,
                'genderDefault' => $genderDefault,
                'genderOptions' => $genderOptions,
                'options' => array(
                    'frameClass' => 'col-sm-12 col-md-8',
                ),
            ));

			echo $this->Html->tag('h2', __('Alamat'), array(
	        	'class' => 'sub-heading'
	        ));

        //  echo $this->element('blocks/users/forms/address', array(
        //      'options' => $options,
        //  ));

            echo $this->element('blocks/users/forms/address', array(
                'use_location_picker'   => true, 
                'options'               => array_replace(array(
                    'frameClass'    => 'col-sm-12 col-md-8',
                    'labelClass'    => 'col-sm-4 col-xl-2 taright control-label',
                    'class'         => 'relative col-sm-8 col-xl-4'
                ), $options), 
            ));

			echo $this->Html->tag('h2', __('Informasi Kontak'), array(
	        	'class' => 'sub-heading'
	        ));
            echo $this->element('blocks/users/forms/contact_info', array(
                'options' => $options,
            ));
		?>
	</div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
                <?php
                        echo $this->Html->link(__('Kembali'), $urlBack, array(
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