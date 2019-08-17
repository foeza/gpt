<?php 
        $urlBack = !empty($urlBack)?$urlBack:false;

        if($active_menu == 'director'){
            echo $this->element('blocks/users/director_action');
        } else {
           echo $this->element('blocks/users/form_action');        
        }
        echo $this->Form->create('UserConfig', array(
            'class' => 'mb30',
        ));
?>

<div class="row">
    <div class="col-sm-12">
        <?php
                echo $this->Html->tag('div', 
                    $this->Form->label('', __('Tambahkan media sosial agar tetap dapat terhubung dengan jejaring Sosial Perusahaan Anda.')), 
                    array(
                        'class' => 'sublabel'
                    )
                );

                echo $this->element('blocks/users/social_media');
        ?>
    </div>
</div>

<?php 
        echo $this->element('blocks/users/form_action', array(
            'action_type' => 'bottom',
        ));
        echo $this->Form->end(); 
?>