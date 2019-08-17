<?php
        $options = array(
            'frameClass' => 'col-sm-7',
            'labelClass' => 'col-xl-2 col-sm-3',
            'class' => 'relative col-sm-8 col-xl-7',
        );

        echo $this->Form->create('UserClient');
?>
<div class="row">
    <div class="col-sm-12">
        <?php
                echo $this->Rumahku->buildInputForm('new_password', array_merge($options, array(
                    'type' => 'password',
                    'label' => __('Password Baru *'),
                    'autocomplete' => 'off',
                )));
        ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="action-group bottom">
            <div class="btn-group">
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