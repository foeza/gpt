<?php

	$options	= empty($options) ? array() : $options;
	$modelName	= Common::hashEmptyField($options, 'model', 'UserConfig');

	echo($this->Rumahku->buildInputForm($modelName.'.facebook', array_merge($options, array(
		'label'			=> __('Facebook'),
		'placeholder'	=> __('Masukkan URL Facebook Anda'), 
		'class'			=> 'relative col-sm-5 col-xl-7',
	))));

	echo($this->Rumahku->buildInputForm($modelName.'.twitter', array_merge($options, array(
		'label'			=> __('Twitter'),
		'placeholder'	=> __('Masukkan URL Twitter Anda'), 
		'class'			=> 'relative col-sm-5 col-xl-7',
	))));

	echo($this->Rumahku->buildInputForm($modelName.'.google_plus', array_merge($options, array(
		'label'			=> __('Google Plus'),
		'placeholder'	=> __('Masukkan URL Google Plus Anda'), 
		'class'			=> 'relative col-sm-5 col-xl-7',
	))));

	echo($this->Rumahku->buildInputForm($modelName.'.linkedin', array_merge($options, array(
		'label'			=> __('Linkedin'),
		'placeholder'	=> __('Masukkan URL Linkedin Anda'), 
		'class'			=> 'relative col-sm-5 col-xl-7',
	))));

	echo($this->Rumahku->buildInputForm($modelName.'.pinterest', array_merge($options, array(
		'label'			=> __('Pinterest'),
		'placeholder'	=> __('Masukkan URL Pinterest Anda'), 
		'class'			=> 'relative col-sm-5 col-xl-7',
	))));

	echo($this->Rumahku->buildInputForm($modelName.'.instagram', array_merge($options, array(
		'label'			=> __('Instagram'),
		'placeholder'	=> __('Masukkan URL Instagram Anda'), 
		'class'			=> 'relative col-sm-5 col-xl-7',
	))));

?>