<?php 
        $chosen = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompanyConfig', 'theme_id');
?>
<div class="launcher-themes mt30">
    <div class="row mb30">
        <?php
                if( !empty($values) ) {
                    foreach ($values as $key => $value) {
                        $id = $this->Rumahku->filterEmptyField($value, 'Theme', 'id');
                        $photo = $this->Rumahku->filterEmptyField($value, 'Theme', 'photo');
                        $name = $this->Rumahku->filterEmptyField($value, 'Theme', 'name');
                    
                        $customPhoto = $this->Html->image($photo);

                        if( $chosen == $id ) {
                            $action = $this->Html->tag('div', $this->Html->link(__('Terpilih'), '#', array(
                                'class' => 'btn green primary',
                            )), array(
                                'class' => 'primary-file',
                            ));
                        } else {
                            $action = $this->Html->tag('div', $this->Html->link(__('Pilih Tema'), array(
                                'controller' => 'ajax',
                                'action' => 'theme',
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
                                echo $this->Html->link(__('Setting Tampilan'), array(
                                    'controller' => 'settings',
                                    'action' => 'customizations',
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