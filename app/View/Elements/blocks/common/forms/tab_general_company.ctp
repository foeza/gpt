<?php

		$data = $this->request->data;
        $sent_app_days = $this->Rumahku->filterEmptyField($_global_variable, 'sent_app_day');

        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );

        $save_path_ebrosur = Configure::read('__Site.ebrosurs_photo');
        $photoSizeEbrosur = $this->Rumahku->_rulesDimensionImage($save_path_ebrosur, 'large', 'size');

        $brochure_custom_sell = $this->Rumahku->filterEmptyField($data, 'UserCompanyConfig', 'brochure_custom_sell');
        $brochure_custom_rent = $this->Rumahku->filterEmptyField($data, 'UserCompanyConfig', 'brochure_custom_rent');
        $type_custom_ebrochure = $this->Rumahku->filterEmptyField($data, 'UserCompanyConfig', 'type_custom_ebrochure');

        if(!empty($type_custom_ebrochure) && $type_custom_ebrochure == 'potrait'){
            $photoSizeEbrosur = '724x1024';
        }

		// echo $this->Rumahku->buildInputToggle('is_block_premium_listing', array_merge($options, array(
  //           'label' => __('Block Premium Listing Agen'),
  //       )));


        if( !empty($is_brochure) ) {
            $isEbrochureBuilder = Common::hashEmptyField($data, 'UserCompanyConfig.is_ebrochure_builder');

            if($isEbrochureBuilder){
                $ebrochureTemplates = empty($ebrochureTemplates) ? array() : $ebrochureTemplates;

                echo $this->element('blocks/ebrosurs/forms/template', array(
                    'templates' => $ebrochureTemplates, 
                ));
            }
            else{
                echo $this->Rumahku->buildInputForm('brochure_custom_sell', array_merge($options, array(
                    'type' => 'file',
                    'label' => sprintf(__('Upload E-Brosur Dijual ( <span class="resolution-broschure">%s</span> )'), $photoSizeEbrosur),
                    'preview' => array(
                        'photo' => $brochure_custom_sell,
                        'save_path' => $save_path_ebrosur,
                        'size' => 's',
                    ),
                    'delete_photo' => array(
                        'url' => array(
                            'controller' => 'settings',
                            'action' => 'delete_template_ebrosur',
                            'brochure_custom_sell',
                            'admin' => true
                        ),
                        'confirm' => __('Apakah Anda yakin ingin menghapus template eBrosur Dijual?')
                    )
                )));

                echo $this->Rumahku->buildInputForm('brochure_custom_rent', array_merge($options, array(
                    'type' => 'file',
                    'label' => sprintf(__('Upload E-Brosur Disewakan ( <span class="resolution-broschure">%s</span> )'), $photoSizeEbrosur),
                    'preview' => array(
                        'photo' => $brochure_custom_rent,
                        'save_path' => $save_path_ebrosur,
                        'size' => 's',
                    ),
                    'delete_photo' => array(
                        'url' => array(
                            'controller' => 'settings',
                            'action' => 'delete_template_ebrosur',
                            'brochure_custom_rent',
                            'admin' => true
                        ),
                        'confirm' => __('Apakah Anda yakin ingin menghapus template eBrosur Disewakan?')
                    )
                )));

                echo $this->Rumahku->buildInputToggle('is_description_ebrochure', array_merge($options, array(
                    'label' => __('E-Brosur : Tampilkan deskripsi properti?'),
                )));

                echo $this->Rumahku->buildInputToggle('is_specification_ebrochure', array_merge($options, array(
                    'label' => __('E-Brosur : Tampilkan spesifikasi properti?'),
                )));
            }

	        echo $this->Rumahku->buildInputToggle('auto_create_ebrochure', array_merge($options, array(
	            'label' => __('Auto Create E-Brosur'),
	        )));

            echo $this->Form->hidden('is_brochure');
            echo $this->Form->hidden('type_custom_ebrochure');
        }

        echo $this->Rumahku->buildInputForm('pph',  array_merge($options, array(
            'type' => 'text',
            'label' => __('PPH'),
            'textGroup' => __('%'),
            'classGroupPosition' => 'inside',
            'class' => 'relative col-sm-4 col-xl-4',
        )));

        echo $this->Rumahku->buildInputToggle('is_sent_app', array_merge( $options, array(
            'label' => __('Auto KPR'),
            'attributes' => array(
                'triggered-selector-class' => 'url-toggle-sent-app'
            ),
        )));

        echo $this->Rumahku->buildInputRadio('sent_app_day', $sent_app_days, array_merge( $options, array(
            'label' => __('Otomatis KPR kirim ke Bank'),
            'frameClass' => 'col-sm-8 url-toggle-sent-app',
            'error' => false,
        )));
        
        echo $this->element('blocks/settings/block_watermark');
?>