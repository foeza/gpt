<?php
    $generalPath    = Configure::read('__Site.general_folder');
	$companyName    = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
    $metaTitle      = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'meta_title', $companyName);
    $metaDesc       = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'meta_description');
    $layoutTitle    = empty($title_for_layout) ? $metaTitle : $title_for_layout;
    $layoutDesc     = empty($description_for_layout) ? $metaDesc : $description_for_layout;

    $favicon        = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'favicon');
    $trackingCode   = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'tracking_code', FALSE, FALSE);
    $theme          = $this->Rumahku->filterEmptyField($_config, 'Theme', 'slug');
    $configOptions  = $this->Rumahku->webThemeConfig($_config, $_GET);

    $themePath      = empty($theme_path) ? NULL : $theme_path;
    $cssTheme       = sprintf('/css/themes/stylesheet.php?theme=%s&%s', $themePath, $configOptions);

    if ($this->action == 'home') {
        $class_notranslate = 'notranslate';
    } else {
        $class_notranslate = '';
    }

	echo($this->Html->charset('UTF-8').PHP_EOL);
	echo($this->Html->meta(array(
	    'http-equiv'    => 'X-UA-Compatible', 
	    'content'       => 'IE=edge'
	)).PHP_EOL);

	echo($this->Html->meta(array(
	    'name'      => 'viewport', 
	    'content'   => 'width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1, shrink-to-fit=no'
	)).PHP_EOL);

	echo($this->Html->tag('title', $layoutTitle, array('class' => $class_notranslate)).PHP_EOL);
    echo($this->Html->meta('description', $layoutDesc).PHP_EOL);

    echo($this->element('js_init/og_meta'));
    echo($this->element('headers/canonical'));

        $defaultCss = array(
            'jquery',
            'style',
            'custom',
            'global',
            $cssTheme,
        );

        $layoutCSS = empty($layout_css) ? array() : $layout_css;
        if($layoutCSS){
            $defaultCss = array_merge($defaultCss, $layoutCSS);
        }
        echo($this->Html->css($defaultCss).PHP_EOL);
    
    if( !empty($favicon) ) {
        $customFavicon  = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $generalPath, 
            'src'       => $favicon, 
            'thumb'     => FALSE,
            'user_path' => TRUE,
            'url'       => TRUE,
        ));
        echo($this->Html->meta($companyName, $customFavicon, array( 'type' => 'icon' )).PHP_EOL);
    }

    echo($this->element('js_init/meta'));
    echo($this->element('js_init/configure'));
    echo($trackingCode);

?>