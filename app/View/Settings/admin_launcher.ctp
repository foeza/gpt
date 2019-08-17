<?php 
        $chosen = $this->Rumahku->filterEmptyField($launcher, 'UserCompanyLauncher', 'theme_launcher_id');
?>
<div class="launcher-themes mt30">
    <div class="row mb30">
        <?php
                if( !empty($values) ) {
                    foreach ($values as $key => $value) {
                        $id = $this->Rumahku->filterEmptyField($value, 'ThemeLauncher', 'id');
                        $photo = $this->Rumahku->filterEmptyField($value, 'ThemeLauncher', 'photo');
                        $name = $this->Rumahku->filterEmptyField($value, 'ThemeLauncher', 'name');
                    
                        $customPhoto = $this->Html->image($photo);
                        // $customPhoto = $this->Html->tag('div', $this->Rumahku->photo_thumbnail(array(
                        //     'save_path' => Configure::read('__Site.general_folder'), 
                        //     'src' => $photo, 
                        //     'size' => 'm',
                        // )), array(
                        //     'class' => 'user-radius-photo',
                        // ));

                        if( $chosen == $id ) {
                            $action = $this->Html->tag('div', $this->Html->link(__('Terpilih'), '#', array(
                                'class' => 'btn green primary',
                            )), array(
                                'class' => 'primary-file',
                            ));
                        } else {
                            $action = $this->Html->tag('div', $this->Html->link(__('Pilih Tema'), array(
                                'controller' => 'ajax',
                                'action' => 'theme_launcher',
                                $id,
                                'admin' => true,
                            ), array(
                                'class' => 'btn default ajax-link disable-drag',
                                'data-type' => 'content',
                                'data-alert' => __('Anda yakin ingin memilih tema ini ?'),
                            )), array(
                                'class' => 'primary-file',
                            ));
                        }
        ?>
        <div class="template-download col-sm-4">
            <div class="item">
                <?php 
                        echo $this->Html->tag('div', $customPhoto.$action, array(
                            'class' => 'preview relative',
                        ));
                        echo $this->Html->tag('label', $name);
                ?>
                <div class="action">
                    <div class="form-group">
                        <?php 
                                echo $this->Html->link(__('Pilih'), array(
                                    'controller' => 'settings',
                                    'action' => 'launcher_theme',
                                    $id,
                                    'admin' => true,
                                ), array(
                                    'class' => 'btn blue',
                                ));
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
                    }
                }
        ?>
    </div>
</div>
<?php
        echo $this->Form->end();
?>