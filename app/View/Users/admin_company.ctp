<?php
        $urlBack = !empty($urlBack)?$urlBack:false;

		echo $this->Form->create('UserCompany', array(
            'type' => 'file',
            'class' => 'mb30',
        ));
        echo $this->element('blocks/users/tabs/profile');
        echo $this->element('blocks/users/forms/company');

        echo $this->element('blocks/common/forms/action_custom', array(
            '_with_submit' => true,
            '_margin_class' => 'bottom',
            '_button_text' => __('Simpan'),
            '_textBack' => __('Kembali'),
            '_classBack' => 'btn default floleft',
            '_button_class' => 'floright',
            '_urlBack' => $urlBack,
        ));
        echo $this->Form->end(); 
?>