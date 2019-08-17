<!DOCTYPE html>
<html>
<head lang="en">
	<?php 

		$_flash			= isset($_flash) ? $_flash : true;
		$generalPath	= Configure::read('__Site.general_folder');

		$dataCompany	= empty($dataCompany) ? array() : $dataCompany;
		$_config		= empty($_config) ? array() : $_config;

		$companyName		= $this->Rumahku->filterEmptyField($dataCompany, 'UserCompany', 'name');
		$metaTitle			= $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'meta_title', $companyName);
		$metaDescription	= $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'meta_description');
		$layoutTitle		= empty($title_for_layout) ? $metaTitle : $title_for_layout;
		$layoutDescription	= empty($description_for_layout) ? $metaDescription : $description_for_layout;

		$trackingCode	= $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'tracking_code', false, false);
		$favicon		= $this->Rumahku->filterEmptyField($_config, 'UserCompanyConfig', 'favicon');
		$favicon		= $this->Rumahku->photo_thumbnail(array(
			'save_path'	=> $generalPath, 
			'src'		=> $favicon, 
			'thumb'		=> false,
			'user_path'	=> true,
			'url'		=> true,
		));

		echo($this->Html->charset('UTF-8').PHP_EOL);
		echo($this->Html->meta(array(
			'name'		=> 'viewport', 
			'content'	=> 'width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1', 
		)).PHP_EOL);

		echo($this->Html->tag('title', __('Market Trend - %s', $companyName)).PHP_EOL);
		echo($this->Html->meta('description', $layoutDescription).PHP_EOL);

		echo($this->element('js_init/og_meta').PHP_EOL);
		echo($this->element('headers/canonical').PHP_EOL);

	//	echo($this->Html->css(array(
	//	//	'bootstrap.min.css', 
	//		'font-awesome.min.css', 
	//		'market_trend/style.css',
	//		'market_trend/extrastyle.css',
	//	//	'debugger', 
	//	), array(
	//		'async' => 'async', 
	//		'defer' => 'defer', 
	//	)).PHP_EOL);

		echo($this->Rumahku->loadSource(array(
			'font-awesome.min.css', 
			'market_trend/style.css',
			'market_trend/extrastyle.css',
		), 'css', true, array(
			'async' => 'async', 
			'defer' => 'defer', 
		)).PHP_EOL);

		echo($this->Html->css(array(
			'https://fonts.googleapis.com/css?family=Hind+Madurai:400,600,700', 
			'https://fonts.googleapis.com/css?family=Noticia+Text',
		), array(
			'media'		=> 'none', 
			'onload'	=> "if(media!='all')media='all'"
		)).PHP_EOL);

		echo($this->Html->meta($companyName, $favicon, array(
			'type' => 'icon', 
		)).PHP_EOL);

		echo($this->element('js_init/meta'));
		echo($trackingCode);

	//	otomatis narik ke theme yang aktif
	//	echo($this->element('headers/head_config'));

	?>
</head>
<body>
	<?php

		$template = $this->element('blocks/market_trend/headers/header');
		$template.= $_flash ? $this->element('blocks/common/template_flash') : '';

		echo($template);

	?>
	<section id="body">
		<?php

			$content_for_layout = empty($content_for_layout) ? false : $content_for_layout;
			echo($content_for_layout);

		?>
	</section>
	<?php

	//	otomatis narik ke theme yang aktif
		echo($this->element('blocks/market_trend/footers/footer'));

		echo($this->Html->script(array(
			'https://code.jquery.com/jquery-3.2.1.min.js', 
			'https://www.gstatic.com/charts/loader.js', 
		)));

		echo($this->Rumahku->loadSource(array(
			'jquery.lazyimage.min',
			'market_trend/bootstrap.bundle', 
			'market_trend/custom', 
			'location_home',
			'admin/customs.library',
			'admin/dashboard',
			'functions',
		), 'script', true, array(
			'async' => 'async', 
			'defer' => 'defer', 
		)).PHP_EOL);

	//	echo($this->Rumahku->loadSource(array(
	//		'https://code.jquery.com/jquery-3.2.1.min.js', 
	//		'https://www.gstatic.com/charts/loader.js', 
	//		'market_trend/bootstrap.bundle', 
	//		'market_trend/custom', 
	//		'location_home',
	//		'jquery.library',
	//		'admin/customs.library',
	//		'admin/dashboard',
	//		'functions',
	//	), 'script', true, array(
	//		'async' => 'async', 
	//		'defer' => 'defer', 
	//	)).PHP_EOL);

	//	echo($this->Html->div('clearfix', $this->element('sql_dump')));

	?>
</body>
</html>