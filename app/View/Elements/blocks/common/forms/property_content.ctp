<?php
        $data = $this->request->data;

        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );

        // block setting premium membership RKU
        echo $this->element('blocks/settings/block_premium');
        
        echo $this->element('blocks/settings/block_watermark');
        
        // echo $this->Rumahku->buildInputForm('watermark_type', array_merge($options, array(
        //     'label' => __('Tipe Watermark'),
        //     'class' => 'relative col-sm-3 col-xl-4',
        //     'options' => array(
        //         // 'rumahku' => 'Rumahku',
        //         'logo' => __('Logo Perusahaan'),
        //         'text' => 'Text'
        //     )
        // )));

        // echo $this->Rumahku->buildInputForm('property_listing', array_merge($options, array(
        //     'label' => __('Jumlah Listing'),
        //     'class' => 'relative col-sm-3 col-xl-4',
        //     'type' => 'text'
        // )));

        // echo $this->Rumahku->buildInputForm('premium_listing', array_merge($options, array(
        //     'label' => __('Premium Listing'),
        //     'class' => 'relative col-sm-3 col-xl-4',
        //     'type' => 'text'
        // )));

        // echo $this->Rumahku->buildInputToggle('is_block_premium_listing', array_merge($options, array(
        //     'label' => __('Block Premium Listing Agen'),
        // )));

        echo $this->Rumahku->buildInputToggle('is_approval_property', array_merge($options, array(
            'label' => __('Approval Properti'),
            'attributes' => array(
                'class' => 'handle-toggle-content',
                'data-target' => '.approval-box'
            )
        )));

        $content = $this->Rumahku->buildInputToggle('is_restrict_approval_property', array_merge($options, array(
            'label' => __('Restrict Approval'),
        )));

        echo $this->Html->div('approval-box', $content, array_merge($options, array(
            'style' => 'display:'.(!empty($data['UserCompanyConfig']['is_approval_property']) ? 'block' : 'none')
        )));

        echo $this->Rumahku->buildInputToggle('is_hidden_address_property', array_merge($options, array(
            'label' => __('Sembunyikan Alamat Properti'),
        )));

        echo $this->Rumahku->buildInputToggle('is_hidden_map', array_merge($options, array(
            'label' => __('Sembunyikan Map Properti'),
        )));

        echo $this->Rumahku->buildInputToggle('is_refresh_listing', array_merge($options, array(
            'label' => __('Refresh Listing'),
        )));

        echo $this->Rumahku->buildInputToggle('is_bt_commission', array_merge($options, array(
            'label' => __('BT Komisi'),
        )));

        echo $this->Rumahku->buildInputToggle('is_kolisting_koselling', array_merge($options, array(
            'label' => __('Kolisting Koseling'),
        )));

        echo $this->Rumahku->buildInputToggle('is_edit_property', array_merge($options, array(
            'label' => __('Edit Properti oleh Agen'),
            'default' => true,
        )));

        echo $this->Rumahku->buildInputToggle('is_delete_property', array_merge($options, array(
            'label' => __('Hapus Properti oleh Agen'),
            'default' => true,
        )));

        echo $this->Rumahku->buildInputToggle('is_mandatory_client', array_merge($options, array(
            'label' => __('Klien Harus diisi?'),
            'default' => true,
        )));

        echo $this->Rumahku->buildInputToggle('is_mandatory_no_address', array_merge($options, array(
            'label' => __('Nomor Alamat Harus diisi?'),
            'default' => true,
        )));

        echo $this->Rumahku->buildInputToggle('is_display_address', array_merge($options, array(
            'label' => __('Alamat display Daftar Properti?'),
            'default' => true,
        )));

        echo $this->Rumahku->buildInputToggle('is_easy_mode', array_merge($options, array(
            'label' => __('Easy Mode untuk penambahan Properti baru?'),
            'default' => true,
        )));

		echo $this->Rumahku->buildInputToggle('is_open_listing', array_merge($options, array(
			'label' => __('Listing Properti terbuka?'),
		)));
?>