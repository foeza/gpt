<?php 
        $urlBack = !empty($urlBack)?$urlBack:false;

        echo $this->element('blocks/users/simple_info');
        echo $this->element('blocks/users/tabs/profile');

        echo $this->Form->create('UserConfig', array(
            'class' => 'mb30',
        ));
        echo $this->Html->tag('h2', __('Hubungkan'), array(
            'class' => 'sub-heading-pd-top-only'
        ));
        echo $this->Html->tag('div', 
            $this->Form->label('', __('Tambahkan media sosial di profil Anda agar tetap dapat terhubung dengan jejaring Anda.')), 
            array(
                'class' => 'sublabel'
            )
        );

        echo $this->element('blocks/users/social_media');
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