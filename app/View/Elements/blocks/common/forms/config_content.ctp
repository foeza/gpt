<?php
		$data = $this->request->data;

        $favicon = $this->Rumahku->filterEmptyField($data, 'UserCompanyConfig', 'favicon');
        $about_bg = $this->Rumahku->filterEmptyField($data, 'UserCompanyConfig', 'about_bg');

		$save_path_general = Configure::read('__Site.general_folder');
		$photoSizeGeneral = $this->Rumahku->_rulesDimensionImage($save_path_general, 'large', 'size');
	
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );

        $sent_app_days = $this->Rumahku->filterEmptyField($_global_variable, 'sent_app_day');

        $list_language = Configure::read('__Site.language');

		echo $this->Rumahku->buildInputForm('favicon', array_merge($options, array(
            'type' => 'file',
            'label' => __('Favicon ( 16x16 )'),
            'preview' => array(
                'photo' => $favicon,
                'save_path' => $save_path_general,
                'size' => 's',
            ),
        )));

        echo $this->Rumahku->buildInputForm('about_bg',  array_merge($options, array(
            'type' => 'file',
            'label' => sprintf(__('Bg Hal. Tentang Kami ( %s )'), $photoSizeGeneral),
            'preview' => array(
                'photo' => $about_bg,
                'save_path' => $save_path_general,
                'size' => 'm',
            ),
        )));

        echo $this->Rumahku->buildInputForm('google_analytic',  array_merge($options, array(
            'label' => __('Google Analytic'),
        )));

        echo $this->Rumahku->buildInputForm('meta_tag',  array_merge($options, array(
            'label' => __('Script Code Header / Meta Tag'),
        )));

        echo $this->Rumahku->buildInputForm('body_tag',  array_merge($options, array(
            'label' => __('Body Tag'),
        )));

        echo $this->Rumahku->buildInputForm('form_api_code',  array_merge($options, array(
            'label' => __('Form API Code'),
        )));

        echo $this->Rumahku->buildInputForm('facebook_appid',  array_merge($options, array(
            'label' => __('Facebook Appid'),
        )));

        echo $this->Rumahku->buildInputForm('max_admin',  array_merge($options, array(
            'label' => __('Maksimal Admin'),
            'class' => 'relative col-sm-3 col-xl-4',
            'type' => 'text'
        )));

        echo $this->Rumahku->buildInputForm('max_agent',  array_merge($options, array(
            'label' => __('Maksimal Agen'),
            'class' => 'relative col-sm-3 col-xl-4',
            'type' => 'text'
        )));

        echo $this->Rumahku->buildInputForm('pph',  array_merge($options, array(
            'type' => 'text',
            'label' => __('PPH'),
            'textGroup' => __('%'),
            'classGroupPosition' => 'inside',
            'class' => 'relative col-sm-4 col-xl-4',
        )));

        echo $this->Rumahku->buildInputForm('language',  array_merge($options, array(
            'label' => __('Bahasa'),
            'class' => 'relative col-sm-3 col-xl-4',
            'options' => $list_language,
            'infopopover' => array(
                'title' => __('Apa ini ?'),
                'content' => __('Multi bahasa hanya berlaku untuk penamaan Menu'),
                'options' => array(
                    'data-modal-size' => 'modal-md col-md-3'
                )
            ),
        )));

        echo $this->Rumahku->buildInputForm('domain_zimbra',  array_merge($options, array(
            'type' => 'text',
            'label' => __('Domain Zimbra'),
            'infoText' => __('Masukan URL zimbra yang digunakan'),
            'class' => 'relative col-sm-4 col-xl-4',
            'infoClass' => ' tajustify'
        )));

        echo $this->Rumahku->buildInputToggle('hide_powered',  array_merge($options, array(
            'label' => __('Hide Powered By'),
        )));

        echo $this->Rumahku->buildInputForm('text_powered',  array_merge($options, array(
            'label' => __('Text Powered By'),
            'class' => 'relative col-sm-3 col-xl-4',
            'type' => 'text'
        )));

        echo $this->Rumahku->buildInputToggle('g_translate',  array_merge($options, array(
            'label' => __('Google Translate'),
        )));

        // echo $this->Rumahku->buildInputToggle('is_agent_personal_website',  array_merge($options, array(
        //     'label' => __('Agent Personal Website'),
        // )));

        // echo $this->Rumahku->buildInputToggle('is_home_banner_title', array(
        //     'label' => __('Show Home Banner Title'),
        // )));
?>