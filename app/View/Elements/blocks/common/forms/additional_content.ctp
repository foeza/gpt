<?php
        $options = array(
            'frameClass' => 'col-sm-12',
            'labelClass' => 'col-xl-1 col-sm-4 col-md-3 control-label taright',
            'class' => 'relative col-sm-8 col-xl-4',
        );
        
		echo $this->Rumahku->buildInputToggle('is_home_right_filter_cozy', array_merge($options, array(
            'label' => __('Home Right Filter (for Cozy)'),
            'class' => 'relative col-sm-8 col-xl-4',
        )));

        echo $this->Rumahku->buildInputToggle('is_bg_footer_easyliving', array_merge($options, array(
            'label' => __('Background Footer (for Easy Living)'),
            'class' => 'relative col-sm-8 col-xl-4',
        )));

        echo $this->Rumahku->buildInputToggle('is_full_logo',  array_merge($options, array(
            'label' => __('Full Logo (for Easy Living)'),
        )));

        echo $this->Rumahku->buildInputForm('header_content', array_merge($options, array(
            'label' => __('Home Content'),
            'inputClass' => 'ckeditor',
        )));

        echo $this->Rumahku->buildInputForm('sub_header_content', array_merge($options, array(
            'label' => __('Home Sub Content (for Cozy)'),
            'inputClass' => 'ckeditor',
        )));

        echo $this->Rumahku->buildInputForm('footer_content', array_merge($options, array(
            'label' => __('Footer Content'),
        )));
?>