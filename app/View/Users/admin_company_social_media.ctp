<?php 
        $urlBack = !empty($urlBack)?$urlBack:false;

        echo $this->element('blocks/users/tabs/profile');
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
        echo $this->element('blocks/common/forms/action_custom', array(
            '_with_submit' => true,
            '_margin_class' => 'bottom',
            '_button_text' => __('Simpan Perubahan'),
            '_textBack' => __('Kembali'),
            '_classBack' => 'btn default floleft',
            '_button_class' => 'floright',
            '_urlBack' => $urlBack,
        ));
        echo $this->Form->end(); 
?>