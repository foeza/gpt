<?php 
        $options = !empty($options)?$options:array();
        $user_type = !empty($user_type)?$user_type:false;
        $modelName = !empty($modelName)?$modelName:'UserProfile';
        
        echo $this->Rumahku->buildInputForm($modelName.'.phone', array_merge($options, array(
            'label' => __('No. Telepon'),
            'class' => 'relative col-sm-5 col-xl-7',
            'infoText' => __('Harap gunakan kode area untuk nomor telepon. Contoh: 0215332555'),
        )));
        echo $this->Rumahku->buildInputForm($modelName.'.no_hp', array_merge($options, array(
            'label' => __('No. handphone #1 *'),
            'class' => 'relative col-sm-5 col-xl-7',
            'infoText' => __('Harap masukkan nomor handphone dengan benar. Contoh: 0822121212'),
            'custom' => array(
                'type' => 'whatsapp',
                'fieldName' => $modelName.'.no_hp_is_whatsapp',
                'label' => __('termasuk no. whatsapp'),
            ),
        )));
        echo $this->Rumahku->buildInputForm($modelName.'.no_hp_2', array_merge($options, array(
            'label' => __('No. handphone #2'),
            'class' => 'relative col-sm-5 col-xl-7',
            'infoText' => __('Harap masukkan nomor handphone dengan benar. Contoh: 0822121212'),
            'custom' => array(
                'type' => 'whatsapp',
                'fieldName' => $modelName.'.no_hp_2_is_whatsapp',
                'label' => __('termasuk no. whatsapp'),
            ),
        )));
        echo $this->Rumahku->buildInputForm($modelName.'.pin_bb', array_merge($options, array(
            'label' => __('Pin Blackberry'),
            'class' => 'relative col-sm-5 col-xl-7',
        )));
        echo $this->Rumahku->buildInputForm($modelName.'.line', array_merge($options, array(
            'label' => __('ID Line Messenger'),
            'class' => 'relative col-sm-5 col-xl-7',
        )));

        if( (!empty($modelName) && $modelName == 'UserClient') || $user_type == 'client' ){
            echo $this->Rumahku->buildInputToggle($modelName.'.is_get_birthday_email', array_merge($options, array(
                'label' => __('Terima Ucapan Ulang Tahun'),
                'class' => 'relative col-sm-5 col-xl-7', 
                'labelClass' => 'col-xl-2 col-sm-4 control-label taright',
            )));
        }
?>