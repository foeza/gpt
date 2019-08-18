<?php
        $data = $this->request->data;

        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );
        
        echo $this->element('blocks/settings/block_watermark');

        // echo $this->Rumahku->buildInputToggle('is_hidden_address_property', array_merge($options, array(
        //     'label' => __('Sembunyikan Alamat Properti'),
        // )));

        // echo $this->Rumahku->buildInputToggle('is_hidden_map', array_merge($options, array(
        //     'label' => __('Sembunyikan Map Properti'),
        // )));

        echo $this->Rumahku->buildInputToggle('is_easy_mode', array_merge($options, array(
            'label' => __('Easy Mode untuk penambahan Properti baru?'),
            'default' => true,
        )));

?>