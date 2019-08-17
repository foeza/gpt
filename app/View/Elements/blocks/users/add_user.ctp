<?php
        $data = $this->request->data;
        $group_id = Common::hashEmptyField($data, 'User.group_id');

        $self = !empty($self) ? $self : false;
        $user = !empty($user) ? $user : false;
        $recordID = !empty($recordID) ? $recordID : false;
        $parent_id = Common::hashEmptyField($user, 'User.parent_id');

        $subareas = !empty($subareas)?$subareas:false;
        $options = !empty($options)?$options:false;
        $manualUploadPhoto = !empty($manualUploadPhoto)?$manualUploadPhoto:false;
        $_email = !empty($_email)?$_email:false;
        $auth_form = !empty($auth_form)?$auth_form:false;
        $user_type = !empty($user_type)?$user_type:false;
        $inputAddress = isset($inputAddress)?$inputAddress:true;

        $access_membership_rku = !empty($access_membership_rku)?$access_membership_rku:false;

        $custom_form = !empty($custom_form)?$custom_form:false;
        $path_form = !empty($path_form)?$path_form:false;
        $custom_url_back = !empty($custom_url_back)?$custom_url_back:false;
        $value_url_back = !empty($value_url_back)?$value_url_back:false;
        
        if ($custom_url_back) {
           $urlBack = $value_url_back;
        } else {
            $urlBack = array(
                'controller' => 'users',
                'action' => 'user_info',
                'admin' => true,
            );
        }

        if(empty($self)){
            $urlBack[] = $recordID;
        }

        echo $this->Html->tag('h2', __('Informasi Dasar'), array(
        	'class' => 'sub-heading'
        ));
?>

<?php if ($custom_form): ?>

    <div class="row locations-trigger">
        <div class="col-sm-12">
            <?php
                echo $this->element(sprintf('%s', $path_form), array(
                    'options' => $options,
                    'inputAddress' => $inputAddress,
                ));
            ?>
        </div>
    </div>
    
<?php else: ?>
    
    <div class="row locations-trigger">
        <div class="col-sm-12">
            <?php
                    echo $this->element('blocks/users/forms/profile', array(
                        'options' => $options,
                        'manualUploadPhoto' => $manualUploadPhoto,
                        '_email' => $_email,
                        'auth_form' => $auth_form,
                        'user_type' => $user_type,
                        'recordID' => $recordID,
                    ));

        			echo $this->Html->tag('h2', __('Informasi Kontak'), array(
        	        	'class' => 'sub-heading'
        	        ));
                    echo $this->element('blocks/users/forms/contact_info', array(
                        'options' => $options,
                        'user_type' => $user_type,
                    ));

                    if($user_type == 'client'){
                        echo $this->element('blocks/users/forms/budget_info', array(
                            'options' => $options,
                            'user_type' => $user_type,
                        ));
                    }

                    if( !empty($inputAddress) ) {
                        echo $this->Html->tag('h2', __('Alamat'), array(
                            'class' => 'sub-heading'
                        ));

					//	echo $this->element('blocks/users/forms/address', array(
					//		'options' => $options,
					//		'inputAddress' => $inputAddress,
					//	));

						$modelName		= 'UserProfile';
						$mandatory		= Common::hashEmptyField((array) $inputAddress, 'mandatory', '*', array('isset' => true));
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
                    }

                    $content_commission = '';
                    if( $user_type == 'agent' || $group_id == 2 ) {
                        $content_commission = $this->element('blocks/users/forms/commission');
                    }

                    echo $this->Html->tag('div', $content_commission, array(
                        'id' => 'user-group-commission',
                    ));

                    // s: block premium in user agent
                    if ( $access_membership_rku ) {
                        $block_premium = $this->element('blocks/settings/block_premium', array(
                            'display_item'  => true,
                            'custom_item' => array(
                                'label_name' => 'Pilih Paket Membership',
                                'model_name' => 'User',
                                'field_name' => 'membership_package_id',
                            ),
                        ));

                        $heading_premium = $this->Html->tag('h2', __('Membership Premium'), array(
                            'class' => 'sub-heading'
                        ));

                        echo $this->Html->tag('div', $heading_premium.$block_premium, array(
                            'id' => 'user-block-premium',
                        ));
                    }
                    // e: block premium in user agent

    		?>
    	</div>
    </div>

<?php endif ?>

<?php 
        echo $this->element('blocks/users/form_action', array(
            'action_type' => 'bottom',
            'urlBack' => $urlBack,
        ));
?>