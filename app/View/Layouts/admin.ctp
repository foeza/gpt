<?php 
        $general_path = Configure::read('__Site.general_folder');
        $layout_js = isset($layout_js) ? $layout_js : array();
        $layout_css = isset($layout_css) ? $layout_css : array();
        $_flash = isset($_flash)?$_flash:true;
        
        $company_name = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
        $favicon = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'favicon');

        $default = isset($minimalismenu) ? array() : array(
            'admin/minimalismenu',
        );

        $customFavicon = $this->Rumahku->photo_thumbnail(array(
            'save_path' => $general_path, 
            'src'=> $favicon, 
            'thumb' => false,
            'user_path' => true,
            'url' => true,
        ));

        if( !empty($_angular) ) {
            if( is_string($_angular) ) {
                $ngApp = $_angular;
            } else {
                $ngApp = 'primeApp';
            }
        } else {
            $ngApp = false;
        }
?>
<!DOCTYPE html>
<html lang="id" ng-app="<?php echo $ngApp; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php
            $default_css = array(
				'admin/jquery',
				'jquery-ui',
				'admin/bootstrap-style',
			//	'admin/rv4-style',
				'admin/style',
				'admin/minimalismenu',
				'date/bootstrap-combined.min',
				'/js/editable/css/bootstrap-editable', 
            );

		//	error dary ExceptionRenderer
			$error = empty($error) ? null : $error;

			if($error){
			//	$default[]		= 'google.prettify';
			//	$default_css[]	= 'google.prettify.desert';
				$default_css[]	= 'prime-error-handler';
			}

            echo $this->Rumahku->initializeMeta( $_global_variable );
            echo $this->Html->css($default_css).PHP_EOL;

            if(isset($layout_css) && !empty($layout_css)){
                foreach ($layout_css as $key => $value) {
                    echo $this->Html->css($value).PHP_EOL;
                }
            }

            echo $this->Html->css(array(
                'admin/custom',
            )).PHP_EOL;
            echo $this->Html->meta($company_name, $customFavicon, array(
                'type' => 'icon'
            )) . PHP_EOL;
            
            $class_body = !empty($class_body)?$class_body:false;
            
            if( !empty($_angular) ) {
                echo $this->Html->script(array(
                    'admin/angular.min',
                ));
            }
            
            echo($this->element('js_init/meta'));
    ?>
</head>
<body class="<?php echo $class_body; ?>" data-ajax="ready">
    <div class="ajax-loading"></div>
    <?php
            // $sdkscript = isset($sdkscript) ? $sdkscript : false;
            // if($sdkscript){
            //     echo $this->element('js_init/sdkscript');
            // }

            $is_theme_setting = isset($is_theme_setting) ? $is_theme_setting : false;

            if(!$is_theme_setting){
    ?>
    <div id="big-wrapper">
        <?php 
                echo $this->element('sidebars/left_menus').PHP_EOL;
        ?>
        <div id="content-wrapper">
            <?php 
                    echo $this->element('headers/header').PHP_EOL;
                    echo $this->element('headers/breadcrumb').PHP_EOL;

                    if( !empty($_flash) ) {
                        echo $this->element('blocks/common/flash').PHP_EOL;
                    }
            ?>
            <div id="content">
                <?php 
                        echo $this->Html->tag('div', $this->fetch('content'), array(
                            'id' => 'wrapper-write',
                        ));
                        // echo($this->Html->div('clearfix', $this->element('sql_dump')));
                ?>
            </div>
        </div>
    </div>
    <?php
            // if( !empty($_widget_help) ) {
            //         echo $this->element('widgets/help');
            //     }
            }else{
                echo $this->element('blocks/common/flash');
                echo $this->fetch('content');
            }

            $default_js = array_merge(array(
                'admin/jquery.library.js?v3',
                'location_home.js',
                'admin/modernizr-custom',
                'admin/classie',
			//	'html2canvas', 
            ), $default);
            echo $this->Html->script($default_js).PHP_EOL;

            if(isset($layout_js) && !empty($layout_js)){
                $layout_js_bottom = Common::hashEmptyField($layout_js, 'bottom');
                $layout_js = Common::_callUnset($layout_js, array(
                    'bottom',
                ));
                
                foreach ($layout_js as $key => $value) {
                    echo $this->Html->script($value).PHP_EOL;
                }
            }

            echo $this->Html->script(array(
                'jquery-ui.min', 
                'admin/customs.library.js?v3', 
                'editable/js/bootstrap-editable', 
                'editable/js/prime-address', 
                'admin/functions',
                // 'admin/dashboard',
            )).PHP_EOL;

            if( !empty($layout_js_bottom) ) {
                foreach ($layout_js_bottom as $key => $value) {
                    echo $this->Html->script($value).PHP_EOL;
                }
            }
            
            if( !empty($_angular) ) {
                echo $this->Html->script(array(
                    'admin/customs.angular',
                )).PHP_EOL;
            }

            // echo $this->element('js_init/script');
            echo $this->element('blocks/common/modal').PHP_EOL;
    ?>
    <?php

	//	EXPORT PDF REPORT
		$formURL = $this->Html->url(array(
			'backprocess'	=> true, 
			'controller'	=> 'reports', 
			'action'		=> 'export', 
		), true);

    ?>
    <form id="pdf-export-form" action="<?php echo($formURL); ?>" data-target="div.content-wrapper" method="post"></form>
</body>
</html>
