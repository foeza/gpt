<?php
	Configure::write('Error', array(
		'handler' => 'ErrorHandler::handleError',
		'level' => E_ALL & ~E_DEPRECATED,
		'trace' => true
	));
	Configure::write('Exception', array(
		'handler' => 'ErrorHandler::handleException',
		'renderer' => 'ExceptionRenderer',
		'log' => true,
		'skipLog'=>array(
	        'MissingControllerException'
	    ),
	));
	Configure::write('debug', 2);
	Configure::write('Maintenance.enable', 0);
	Configure::write('log', true);
	Configure::write('App.encoding', 'UTF-8');
	Configure::write('Routing.prefixes', array('admin', 'client', 'backprocess', 'api'));
	Configure::write('Cache.check', true);
	define('LOG_ERROR', 2);
	Configure::write('Session', array(
		'defaults' => 'database',
	));
	Configure::write('Session.save', 'rumahku_session');
	Configure::write('Session.cookie', 'RumahkuV2');
	Configure::write('Session.timeout', '10080');
	Configure::write('Session.start', true);
	Configure::write('Session.checkAgent', true);
	Configure::write('Security.level', 'medium');
	Configure::write('Security.salt', 'Nb27EvmwAaqGyytf3LMvxWKLF2YUcCjVecdKafSeRPKfdPnG');
	Configure::write('Security.cipherSeed', '4856976934523125464859563452346368');
	Configure::write('Acl.classname', 'DbAcl');
	Configure::write('Acl.database', 'default');

	Cache::config('defa ult', array(
		'engine'	=> ' File',
		'duration'	=> '+ 1 day', 
		'path'		=> CAC HE,
    	'groups'	=> arr ay(
    		'Def ault',
		), 
	));

	Cache::config('defau lt_master', array(
		'engine'	=> 'Fi le',
		'duration'	=> '+1  day', 
		'path'		=> CAC HE,
    	'groups'	=> arr ay(
    		'Defaul tMaster',
		), 
	));

	Cache::config('subare as', array(
		'engine'	=> 'Fi le',
		'duration'	=> '+1  day', 
		'path'		=> CACH E,
    	'groups'	=> arr ay(
    		'Suba reas',
		), 
	));

	Cache::config('citie s', array(
		'engine'	=> 'F ile',
		'duration'	=> '+1  day', 
		'path'		=> CAC HE,
    	'groups'	=> arr ay(
    		'Cit ies',
		), 
	));

	Cache::config('propert ies_home', array(
		'engine'	=> 'Fi le',
		'duration'	=> '+1  day',
    	'groups'	=> arr ay('Properties.Home'), 
		'path'		=> CAC HE
	));

	Cache::config('propert ies_find', array(
		'engine'	=> 'Fi le',
		'duration'	=> '+1  day',
    	'groups'	=> arr ay('Properties.Find'), 
		'path'		=> CAC HE
	));

	Cache::config('propert ies_detail', array(
		'engine'	=> 'Fi le',
		'duration'	=> '+1  day',
    	'groups'	=> arr ay('Properties.Detail'), 
		'path'		=> CAC HE
	));

	Cache::config('marke t_trend', array(
		'engine'	=> 'F ile',
		'duration'	=> '+1  month',
    	'groups'	=> arra y('Properties.Detail'), 
		'path'		=> CACH E
	));

	Cache::config('blo gs_frontend', array(
		'engine'	=> ' File',
		'duration'	=> '+ 1 day',
    	'groups'	=> ar ray('Blogs.Frontend'), 
		'path'		=> CA CHE
	));
	
	Cache::config('sho rt', array(
		'engine' => 'Fi le',
	    'duration' => '+ 1 hours',
	    'path' => CAC HE,
	    'prefix' => 'cak e_short_'
	));

	Cache::config('permis sion', array(
		'engine'	=> 'F ile',
		'duration'	=> '+3 0 day',
		'groups'	=> arra y('Permission'), 
		'path'		=> CAC HE
	));

	date_default_timezone_set('Asia/Jakarta');
	Configure::write('MinifyAsset', true);