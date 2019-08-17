<?php 
        $admin_rumahku = Configure::read('User.Admin.Rumahku');

        $data = $this->request->data;
        $options = !empty($options)?$options:array();
        $user_type = !empty($user_type)?$user_type:false;
        $recordID = !empty($recordID)?$recordID:false;
        $modelName = !empty($modelName)?$modelName:'User';
        $modelNameProfile = !empty($modelNameProfile)?$modelNameProfile:'UserProfile';
        $userList = !empty($userList) ? $userList : false;

        $groups = !empty($groups) ? $groups : false;

        $save_path = Configure::read('__Site.profile_photo_folder');

        $genderOptions = !empty($_global_variable['gender_options'])?$_global_variable['gender_options']:false;
        $photoSize = $this->Rumahku->_rulesDimensionImage($save_path, 'large', 'size');

        $photo = $this->Rumahku->filterEmptyField($data, $modelName, 'photo_hide');
        $frameClass = $this->Rumahku->filterEmptyField($options, 'frameClass', false, 'col-sm-8');
        $labelClass = $this->Rumahku->filterEmptyField($options, 'labelClass', false, 'col-sm-4 col-xl-2 taright control-label');
        $class = $this->Rumahku->filterEmptyField($options, 'class', false, 'relative col-sm-8 col-xl-4');

        if( !empty($manualUploadPhoto) ) {
            // $mandatory = $this->Rumahku->filterIssetField($manualUploadPhoto, 'mandatory', false, '*');
            echo $this->Rumahku->buildInputForm('photo', array_merge($options, array(
                'type' => 'file',
                // 'label' => __('Foto Profil ( %s ) %s', $photoSize, $mandatory),
                'label' => __('Foto Profil ( %s )', $photoSize),
                'preview' => array(
                    'photo' => $photo,
                    'save_path' => $save_path,
                    'size' => 'pm',
                ),
            )));
        }

        if ($user_type == 'AdminRku') {
            // add field group
            echo $this->Rumahku->buildInputForm('group_id', array(
                'label' => __('Divisi *'),
                'autocomplete' => 'off',
                'options' => $group_rku_admin,
                'empty' => __('Pilih Divisi Anda')
            ));
        } else if( $this->action != 'admin_edit' && !in_array($user_type, array('Principle', 'client', 'Direktur')) ){

            $dataMatch = str_replace('"', "'", json_encode(array(
                array('#user-block-premium', array('2'), 'slide'), 
            )));

            echo $this->Rumahku->buildInputForm('group_id', array(
                'label' => __('Divisi *'),
                'autocomplete' => 'off',
                'options' => $groups,
                'attributes' => array(
                    'data-url' => $this->Html->url(array(
                        'controller' => 'users',
                        'action' => 'group_parent',
                        'backprocess' => true,
                        $recordID,
                    )),
                    'data-form' => '#add-user',
                    // 'data-wrapper-write' => '#user-group-commission',
                    'data-wrapper-write-page' => '#user-group-commission, #parent-user',
                    'data-match' => $dataMatch,
                ),
                'inputClass' => 'handle-toggle ajax-change',
                'empty' => __('Pilih Divisi Anda')
            ));

            $content = false;
            if($userList){
                $content = $this->element('blocks/users/forms/superior');
            }

            echo $this->Html->tag('div', $content, array(
                'id' => 'parent-user',
            ));

        }

        if ( $user_type == 'user' && !empty($admin_rumahku) ) {
            // add field email
            echo $this->Rumahku->buildInputForm('parent_email', array_merge($options, array(
                'type' => 'text',
                'label' => __('Email Principle'),
                'id' => 'autocomplete',
                'attributes' => array(
                    'autocomplete' => 'off',
                    'data-ajax-url' => $this->Html->url(array(
                        'controller' => 'ajax',
                        'action' => 'list_users',
                        3,
                        'admin' => false,
                    ))
                ),
                'infoText' => __('Anda bisa kosongkan, system akan mengambil data default'),
                'rowFormClass' => 'row handling-group ',
                // 'fieldError' => 'User.parent_id',
            )));
        }

        echo $this->User->_callParentUser($user_type, $options);

        if( !empty($_email) ) {
            echo $this->element('blocks/users/forms/add', array(
                'options' => $options,
                'user_type' => $user_type,
            ));
        }

        if( !empty($auth_form) && ($user_type != 'client' && empty($edit)) ) {
            $mandatory = !empty($user) ? false : '*';
            echo $this->Rumahku->buildInputForm('password', array_merge($options, array(
                'type' => 'password',
                'label' => __('Password %s', $mandatory),
                'autocomplete' => 'off',
            )));
            echo $this->Rumahku->buildInputForm('password_confirmation', array_merge($options, array(
                'type' => 'password',
                'label' => __('Konfirmasi Password %s', $mandatory),
                'autocomplete' => 'off',
            )));
        }
        
        echo $this->Rumahku->buildInputForm('full_name', array_merge($options, array(
            'label' => __('Nama Lengkap *'),
        )));
?>

<?php
        // if( $user_type != 'client' ) {
?>
<div class="form-group">
    <div class="row">
        <div class="<?php echo $frameClass; ?>">
            <div class="row">
                <?php 
                        echo $this->Html->tag('div', $this->Form->label('birthday', __('Tanggal Lahir')), array(
                            'class' => $labelClass,
                        ));
                ?>
                <div class="<?php echo $class; ?>">
                    <div class="row">
                    <?php
                            echo $this->Rumahku->setFormBirthdate($modelNameProfile);
                    ?>
                    </div>
                </div>
            </div>
         </div>
    </div>
</div>
<?php
        // }
?>
  	
<?php
        echo $this->Rumahku->buildInputRadio('gender_id', $genderOptions, array_merge($options, array(
            'label' => __('Jenis Kelamin *'),
            'error' => false,
        )));
?>
