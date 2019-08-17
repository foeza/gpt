<?php 
        $bt_commission = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_bt_commission');
        // if( $this->Rumahku->_isAdmin() || $this->Rumahku->_isCompanyAdmin() ) {
            echo $this->Html->tag('h2', __('Komisi'), array(
                'class' => 'sub-heading'
            ));
            echo $this->Rumahku->buildInputForm('UserConfig.commission', array(
                'type' => 'text',
                'label' => __('Target Komisi'),
                'class' => 'relative col-sm-5',
                'textGroup' => __('/ Bulan'),
                'textGroupSecond' => Configure::read('__Site.config_currency_symbol'),
                'inputClass' => 'input_price',
            ));
            echo $this->Rumahku->buildInputForm('UserConfig.sharingtocompany', array(
                'type' => 'text',
                'label' => __('Sharing to Company'),
                'class' => 'relative col-sm-5',
                'textGroup' => __('%'),
            ));
            echo $this->Rumahku->buildInputForm('UserConfig.royalty', array(
                'type' => 'text',
                'label' => __('Royalty'),
                'class' => 'relative col-sm-5',
                'textGroup' => __('%'),
            ));

            if(!empty($bt_commission)){
                echo $this->Rumahku->buildInputForm('UserConfig.bt', array(
                    'type' => 'text',
                    'label' => __('BT'),
                    'class' => 'relative col-sm-5',
                    'textGroup' => __('%'),
                ));
            }
        // }
?>