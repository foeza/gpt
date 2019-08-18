<?php  
       	$general_path = Configure::read('__Site.general_folder');
		$breadcrumb = (isset($_breadcrumb)) ? $_breadcrumb : true;
		$company_name = $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');

		$meta_title = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'meta_title', $company_name);
		$meta_description = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'meta_description');
		
		$title_for_layout = !empty($title_for_layout)?$title_for_layout:$meta_title;
		$description_for_layout = !empty($description_for_layout)?$description_for_layout:$meta_description;

		// $banner_title = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'is_home_banner_title');
		$favicon = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'favicon');
		$tracking_code = $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'tracking_code', false, false);

		$theme = $this->Rumahku->filterEmptyField($_config, 'Theme', 'slug');
		$template = $this->Rumahku->filterEmptyField($_config, 'Template', 'slug');

		$opt_config = $this->Rumahku->webThemeConfig($_config, $_GET);
		$cssTheme = sprintf('/css/themes/stylesheet.php?theme=%s&%s', $theme_path, $opt_config);

		$customFavicon = $this->Rumahku->photo_thumbnail(array(
			'save_path' => $general_path, 
			'src'=> $favicon, 
			'thumb' => false,
			'user_path' => true,
			'url' => true,
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
				echo $this->Html->meta(array('name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1')) . PHP_EOL;

				echo $this->Html->tag('title', $title_for_layout) . PHP_EOL;
				echo $this->element('js_init/og_meta');
				echo $this->element('headers/canonical');

				$minify_css = array(
					'https://fonts.googleapis.com/css?family=Titillium+Web&display=swap', 
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
				echo $tracking_code;
		?>
	</head>
	<body class="<?php echo $template; ?>">
		<?php 
				echo $this->element('blocks/common/sdkscript');
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
					'https://www.gstatic.com/charts/loader.js', // market trend
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
					'market_trend/custom.js', // market trend
				));
			    echo $this->element('blocks/common/floating_contact');
				echo $this->element('blocks/common/forms/frontend/mobile_quick_search');
            	echo $this->element('blocks/common/modal');
		?>
		<script src='https://www.google.com/recaptcha/api.js?onload=renderRecaptcha&render=explicit' async defer></script>
	</body>
</html>