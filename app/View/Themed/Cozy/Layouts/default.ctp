<?php  
       	$general_path 	= Configure::read('__Site.general_folder');
		$breadcrumb 	= (isset($_breadcrumb)) ? $_breadcrumb : true;
		$company_name	= Common::hashEmptyField($dataCompany, 'UserCompany.name');

		$meta_title		= Common::hashEmptyField($_config, 'UserCompanyConfig.meta_title', $company_name);
		$meta_desc		= Common::hashEmptyField($_config, 'UserCompanyConfig.meta_description');
		$favicon		= Common::hashEmptyField($_config, 'UserCompanyConfig.favicon');
		$theme			= Common::hashEmptyField($_config, 'Theme.slug');
		
		$meta_title 	= !empty($title_for_layout)?$title_for_layout:$meta_title;

		$opt_config 	= $this->Rumahku->webThemeConfig($_config, $_GET);
		if (!empty($_GET)) {
			$cssTheme   = sprintf('/css/themes/stylesheet.php?theme=%s&%s', $theme_path, $opt_config);
		} else {
			$cssTheme   = false;
		}

		$customFavicon = $this->Rumahku->photo_thumbnail(array(
			'save_path' => $general_path, 
			'src'		=> $favicon, 
			'thumb' 	=> false,
			'user_path' => true,
			'url' 		=> true,
		));

		if($breadcrumb){
			$breadcrumb = $this->Html->tag('div', $this->element('headers/breadcrumb'), array(
				'class' => 'hidden-print'
			));
		}
?>
<!DOCTYPE html>
<html>
	<head>	
		<?php 
			echo $this->Html->charset('UTF-8') . PHP_EOL;
			echo $this->Html->tag('title', $meta_title) . PHP_EOL;
			echo $this->element('js_init/og_meta');

			$minify_css = array(
				'//fonts.googleapis.com/css?family=Roboto:100,300,400,500,700|Google+Sans:400,500|Product+Sans:400&lang=en', 
				'jquery',
				'cozy-real-estate-font',
				'style',
				'custom',
				'global',
				$cssTheme,
			);

			if(isset($layout_css) && !empty($layout_css)) {
				$minify_css = array_merge($minify_css, $layout_css);
			}
			
			echo $this->Html->css($minify_css) . PHP_EOL;
			echo $this->Html->meta($company_name, $customFavicon, array(
				'type' => 'icon'
			)) . PHP_EOL;
			echo $this->element('js_init/meta');
			echo $this->element('js_init/configure');
		?>
	</head>
	<body class="">
		<?php 
				// echo $this->element('blocks/common/sdkscript');
		?>
    	<div id="wrapper">
			<?php
                    echo $this->element('headers/header');
                    echo $breadcrumb;
        			echo $this->Session->flash('success');
	    			echo $content_for_layout;
					echo $this->element('footers/footer');
			?>
		
		</div>
		<?php
				$minify_js = array(
					'location_home.js',
					// 'https://www.gstatic.com/charts/loader.js', // market trend
					'jquery.library',
					'default.library',
				);

				if(isset($layout_js) && !empty($layout_js)) {
					$minify_js = array_merge($minify_js, $layout_js);
				}
				echo $this->Html->script($minify_js);
				echo $this->Html->script(array(
					'admin/customs.library',
					'functions',
					'default_functions', 
					// 'market_trend/custom.js', // market trend
				));
			    echo $this->element('blocks/common/floating_contact');
				echo $this->element('blocks/common/forms/frontend/mobile_quick_search');
            	echo $this->element('blocks/common/modal');
		?>
		<script src='https://www.google.com/recaptcha/api.js?onload=renderRecaptcha&render=explicit' async defer></script>
	</body>
</html>