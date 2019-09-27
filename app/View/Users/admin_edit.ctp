<?php
		$genderDefault = $this->Rumahku->filterEmptyField($this->request->data, 'User', 'gender_id');
        $subareas = !empty($subareas)?$subareas:false;
		$_email = isset($_email)?$_email:true;

        echo $this->element('blocks/users/simple_info');
        echo $this->element('blocks/users/tabs/profile');
        echo $this->Html->tag('h2', __('Informasi Dasar'), array(
        	'class' => 'sub-heading'
        ));

        echo $this->Form->create('User', array(
            'type' => 'file',
            'class' => 'mb30',
        ));
?>

<div class="row">
    <div class="col-sm-12">
		<?php
                echo $this->element('blocks/users/forms/profile', array(
                    '_email' => $_email,
                    'user_type' => 'User',
                    'genderDefault' => $genderDefault,
                    'options' => array(
                        'frameClass' => 'col-sm-12 col-md-8',
                    ),
                ));

    			echo $this->Html->tag('h2', __('Tentang Saya'), array(
    	        	'class' => 'sub-heading'
    	        ));
                echo $this->Rumahku->buildInputForm('UserProfile.description', array(
                    'label' => __('Informasi Biografi'),
                    'inputClass' => 'ckeditor',
                    'frameClass' => 'col-sm-12 col-md-8',
                ));

    			echo $this->Html->tag('h2', __('Alamat'), array(
    	        	'class' => 'sub-heading'
    	        ));

				echo $this->element('blocks/users/forms/address', array(
					'use_location_picker'	=> true, 
					'options'				=> array(
						'frameClass'	=> 'col-sm-12 col-md-8',
						'labelClass'	=> 'col-sm-4 col-xl-2 taright control-label',
						'class'			=> 'relative col-sm-8 col-xl-4'
					), 
				));

			//	echo $this->element('blocks/users/forms/address', array(
			//		'options' => array(
			//			'frameClass' => 'col-sm-12 col-md-8',
			//		),
			//	));

                echo $this->Html->tag('h2', __('Informasi Kontak'), array(
                    'class' => 'sub-heading'
                ));
                echo $this->element('blocks/users/forms/contact_info', array(
                    'options' => array(
                        'frameClass' => 'col-sm-12 col-md-8',
                    ),
                ));

                $isAdmin        = $this->Rumahku->_isAdmin();
				$authGroupID	= Configure::read('User.group_id');
				$packageID		= Configure::read('User.data.UserConfig.membership_package_id');
				$isAgent		= Common::validateRole('agent', $authGroupID);

				if($isAdmin || ($isAgent && $packageID)){
					echo($this->Html->tag('h2', __('Informasi Lainnya'), array(
						'class' => 'sub-heading'
					)));

					$logoPath	= Configure::read('__Site.logo_photo_folder');
					$logoSize	= $this->Rumahku->_rulesDimensionImage($logoPath, 'large', 'size');
					$logo		= Common::hashEmptyField($this->data, 'UserConfig.logo');

					echo($this->Rumahku->buildInputForm('UserConfig.logo', array(
						'label'		=> sprintf(__('Logo ( %s )'), $logoSize),
						'type'		=> 'file',
						'class'		=> 'col-sm-6 col-xl-4',
						'preview'	=> array(
							'photo'		=> $logo,
							'save_path'	=> $logoPath,
							'size'		=> 'xxsm',
						),
					)));

                    $isIndependent = Common::validateRole('independent_agent', $authGroupID);

                    if($isIndependent){
				        echo($this->element('blocks/users/forms/social_media_info', array(
				        	'options' => array(
				        		'frameClass' => 'col-sm-12 col-md-8',
				        	),
				        )));
                    }
				}
		?>
	</div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group floright">
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