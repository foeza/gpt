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

	Cache::config('default', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day', 
		'path'		=> CACHE,
    	'groups'	=> array(
    		'Default',
		), 
	));

	Cache::config('default_master', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day', 
		'path'		=> CACHE,
    	'groups'	=> array(
    		'DefaultMaster',
		), 
	));

	Cache::config('subareas', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day', 
		'path'		=> CACHE,
    	'groups'	=> array(
    		'Subareas',
		), 
	));

	Cache::config('cities', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day', 
		'path'		=> CACHE,
    	'groups'	=> array(
    		'Cities',
		), 
	));

	Cache::config('properties_home', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day',
    	'groups'	=> array('Properties.Home'), 
		'path'		=> CACHE
	));

	Cache::config('properties_find', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day',
    	'groups'	=> array('Properties.Find'), 
		'path'		=> CACHE
	));

	Cache::config('properties_detail', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day',
    	'groups'	=> array('Properties.Detail'), 
		'path'		=> CACHE
	));

	Cache::config('market_trend', array(
		'engine'	=> 'File',
		'duration'	=> '+1 month',
    	'groups'	=> array('Properties.Detail'), 
		'path'		=> CACHE
	));

	Cache::config('ebrosurs_find', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day',
    	'groups'	=> array('Ebrosurs.Find'), 
		'path'		=> CACHE
	));

	Cache::config('ebrosurs_detail', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day',
    	'groups'	=> array('Ebrosurs.Detail'), 
		'path'		=> CACHE
	));

	Cache::config('advices_find', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day',
    	'groups'	=> array('Advices.Find'), 
		'path'		=> CACHE
	));

	Cache::config('advices_detail', array(
		'engine'	=> 'File',
		'duration'	=> '+1 day',
    	'groups'	=> array('Advices.Detail'), 
		'path'		=> CACHE
	));
	
	Cache::config('short', array(
		'engine' => 'File',
	    'duration' => '+1 hours',
	    'path' => CACHE,
	    'prefix' => 'cake_short_'
	));

	Cache::config('permission', array(
		'engine'	=> 'File',
		'duration'	=> '+30 day',
		'groups'	=> array('Permission'), 
		'path'		=> CACHE
	));
/** 
 * HybridAuth component
 *
 */
 	Configure::write('Hybridauth', array(
	//	openid providers
		'Google' => array(
			'enabled'			=> true,
		//	'approval_prompt'	=> 'force', 
			'keys'				=> array(
				'id'		=> '194083791417-pj7h2b06094pl4fg6k27oqdv69k94pc3.apps.googleusercontent.com',
				'secret'	=> 'y7kA-ResfpjOkO19awGcg3Jr', 
			),
		),
		'Facebook' => array(
			'enabled'			=> true,
			'trustForwarded'	=> true, 
		//	'auth_type'			=> 'rerequest', 
			'scope'  			=> array('email'), 
			'keys'				=> array(
				'id'		=> '268939743710959',
				'secret'	=> '62b952af4453c1a4f2ba57f7b037db31', 
			),
		),
		'Twitter' => array(
			'enabled'		=> true,
		//	'redirect_uri'	=> false, 
			'keys'			=> array(
				'id'		=> '',
				'secret'	=> '', 
			),
		),
		'OpenID' => array(
			'enabled'	=> true,
			'keys'		=> array(
				'id'		=> '',
				'secret'	=> '', 
			),
		),
		'Yahoo' => array(
			'enabled'	=> false,
			'keys'		=> array(
				'id'		=> '',
				'secret'	=> '', 
			),
		),
		'AOL' => array(
			'enabled'	=> false,
			'keys'		=> array(
				'id'		=> '',
				'secret'	=> '', 
			),
		),
		'Live' => array(
			'enabled'	=> false,
			'keys'		=> array(
				'id'		=> '',
				'secret'	=> '', 
			),
		),
		'MySpace' => array(
			'enabled'	=> false,
			'keys'		=> array(
				'id'		=> '',
				'secret'	=> '', 
			),
		),
		'LinkedIn' => array(
			'enabled'	=> false,
			'keys'		=> array(
				'id'		=> '',
				'secret'	=> '', 
			),
		),
		'Foursquare' => array(
			'enabled'	=> false,
			'keys'		=> array(
				'id'		=> '',
				'secret'	=> '', 
			),
		),
	));

	date_default_timezone_set('Asia/Jakarta');
	Configure::write('MinifyAsset', true);