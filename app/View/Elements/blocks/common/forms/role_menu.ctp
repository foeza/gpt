<?php
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );

        echo $this->Rumahku->buildInputToggle('is_launcher',  array_merge($options, array(
            'label' => __('Launcher'),
			'attributes' => array(
				'triggered-selector-class' => 'url-toggle-input'
			),
        )));

		echo $this->Rumahku->buildInputForm('launcher_url',  array_merge($options, array(
			'type' => 'text',
			'label' => __('URL Launcher'),
			'inputClass' => 'url-toggle-input',
			'infoText' => __('Masukkan URL lengkap menggunakan HTTP://'),
            'infoClass' => ' tajustify'
        )));

        echo $this->Rumahku->buildInputToggle('is_blog',  array_merge($options, array(
            'label' => Configure::read('Global.Data.translates.id.blog'),
            'attributes' => array(
                'triggered-selector-class' => 'display-advice-toggle-input'
            ),
        )));

        echo $this->Rumahku->buildInputToggle('is_faq',  array_merge($options, array(
            'label' => __('FAQ'),
        )));

        echo $this->Rumahku->buildInputToggle('is_developer_page',  array_merge($options, array(
            'label' => __('Developer'),
        )));

        echo $this->Rumahku->buildInputToggle('is_career',  array_merge($options, array(
            'label' => __('Karir'),
        )));

        echo $this->Rumahku->buildInputToggle('is_expert_system',  array_merge($options, array(
            'label' => __('Sistem Pakar'),
        )));
?>