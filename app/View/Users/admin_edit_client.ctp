<?php
        $genderDefault = $this->Rumahku->filterEmptyField($this->request->data, 'User', 'gender_id');
		$genderDefault = $this->Rumahku->filterEmptyField($this->request->data, 'User', 'gender_id');
        $genderOptions = !empty($_global_variable['gender_options'])?$_global_variable['gender_options']:false;
		$options = !empty($options)?$options:array(
            'frameClass' => 'col-sm-12 col-md-8',
        );
        $modelName = 'UserClient';

        if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() ) {
            $_email = true;
        } else {
            $_email = false;
        }

        echo $this->Form->create($modelName, array(
            'type' => 'file',
        ));
?>

<div class="row">
    <div class="col-sm-12 locations-root">
        <?php
                echo $this->element('blocks/users/clients/forms/client_source');

                echo $this->Html->tag('h2', __('Informasi Dasar'), array(
                    'class' => 'sub-heading'
                ));
                echo $this->element('blocks/users/forms/profile', array(
                    'user_type' => 'client',
                    'manualUploadPhoto' => array(
                        'mandatory' => false,
                    ),
                    '_email' => $_email,
                    'genderDefault' => $genderDefault,
                    'genderOptions' => $genderOptions,
                    'modelName' => $modelName,
                    'modelNameProfile' => $modelName,
                    'options' => array(
                        'frameClass' => 'col-sm-12 col-md-8',
                    ),
                ));

                echo $this->Html->tag('h2', __('Informasi Kontak'), array(
                    'class' => 'sub-heading'
                ));
                echo $this->element('blocks/users/forms/contact_info', array(
                    'options' => $options,
                    'modelName' => $modelName,
                ));

                echo $this->element('blocks/users/forms/budget_info', array(
                    'options' => $options,
                ));
        ?>
    </div>
</div>
<div class="row locations-trigger">
    <div class="col-sm-12">
        <?php
                echo $this->Html->tag('h2', __('Alamat'), array(
                    'class' => 'sub-heading'
                ));

			//	echo $this->element('blocks/users/forms/address', array(
			//		'options' => $options,
			//		'modelName' => $modelName,
			//		'inputAddress' => array(
			//			'mandatory' => false,
			//		),
			//	));

				$mandatory		= false;
				$inputOptions	= array(
					'frameClass'	=> 'col-sm-12 col-md-8',
					'labelClass'	=> 'col-xl-2 col-sm-4 control-label taright',
					'class'			=> 'relative col-sm-8 col-xl-4',
				);

				echo($this->Rumahku->buildInputForm($modelName.'.address', array_replace($inputOptions, array(
					'label' => __('Alamat Tempat Tinggal %s', $mandatory),
				))));

				echo($this->element('blocks/properties/forms/location_picker', array(
					'options' => array_replace($inputOptions, array(
						'mandatory'	=> $mandatory, 
						'model'		=> $modelName, 
					)), 
				)));

				echo($this->Rumahku->buildInputForm($modelName.'.zip', array_replace($inputOptions, array(
					'label'			=> __('Kode Pos %s', $mandatory),
					'inputClass'	=> 'rku-zip', 
					'class'			=> 'relative col-sm-4 col-xl-4',
				))));
        ?>
 	</div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
                <?php
                        echo $this->Html->link(__('Kembali'), array(
                            'controller' => 'users',
                            'action' => 'client_info',
                            'admin' => true,
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