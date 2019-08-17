<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	// if ( in_array($_SERVER['HTTP_HOST'], array( 'ww.v2.companyweb.com', 'www.primesystem.id', 'primesystem.id' )) ) {
	// 	Router::connect('/', array(
	// 		'controller' => 'memberships', 
	// 		'action' => 'index'
	// 	));

	// 	Router::connect('/terms_and_conditions', array(
	// 		'controller' => 'memberships', 
	// 		'action' => 'terms_and_conditions'
	// 	));
	// } else {
	// }
	if(in_array($_SERVER['HTTP_HOST'], array('www.primesystem.id', 'primesystem.id'))){
		Router::connect('/', array('controller' => 'memberships', 'action' => 'dashboard'));
		// Router::connect('/login/*', array('controller' => 'users', 'action' => 'redirect_home', 'admin' => false));
		Router::connect('/login/*', array(
			'controller' => 'users', 
			'action' => 'login',
			'admin' => true,
		));
	} else if(in_array($_SERVER['HTTP_HOST'], array('www.agent.primesystem.id', 'agent.primesystem.id'))){
		Router::connect('/', array('controller' => 'memberships', 'action' => 'index'));
		Router::connect('/login/*', array('controller' => 'users', 'action' => 'redirect_home', 'admin' => false));
	} else {
		Router::connect('/', array(
			'controller' => 'pages', 
			'action' => 'home'
		));
		Router::connect('/login/*', array(
			'controller' => 'users', 
			'action' => 'login',
			'admin' => true,
		));
	}

	Router::connect('/apps', array(
		'controller' => 'pages', 
		'action' => 'apps'
	));

	Configure::write('Route.action', 'developers|about|contact|faq|career|developer_detail');
	Router::connect('/:action/*', array(
		'controller' => 'pages', 
	), array(
		'action' => Configure::read('Route.action'),
	));

//	B:NEW ROUTEE EASY MODE ======================================================

	Router::connect('/admin/properties/add/*', array(
		'controller'	=> 'properties', 
		'action'		=> 'easy_add',
		'admin'			=> true, 
	));

	Router::connect('/admin/properties/modify/*', array(
		'controller'	=> 'properties', 
		'action'		=> 'easy_preview',
		'admin'			=> true, 
	), array(
	//	'property_id'	=> '[0-9]{11}', 
	//	'action'		=> 'modify', 
	//	'pass'			=> array('property_id'),
	));

//	E:NEW ROUTEE EASY MODE ======================================================

	Router::connect('/download/*', array(
		'controller' => 'ebrosurs', 
		'action' => 'download',
		'admin' => false
	));

	Router::connect('/admin/dashboard', array(
		'controller' => 'users', 
		'action' => 'account',
		'admin' => true,
	));

	Router::connect('/profiles/:property_action/*', array(
		'controller'	=> 'profiles',
		'action'		=> 'property_find', 
	), array(
		'property_action' => 'dijual|disewakan|\[%var:([^\]]*)\%]',
	));

	Router::connect('/profiles/:property_action', array(
		'controller'	=> 'profiles',
		'action'		=> 'property_find', 
	), array(
		'property_action' => 'dijual|disewakan',
	));

//	B:REQUEST NEW ROUTE IPA ======================================================

	Router::connect('/:property_action/*', array(
		'controller'	=> 'properties',
		'action'		=> 'find', 
	), array(
		'property_action' => 'dijual|disewakan|\[%var:([^\]]*)\%]',
	));

	Router::connect('/:property_action', array(
		'controller'	=> 'properties',
		'action'		=> 'find', 
	), array(
		'property_action' => 'dijual|disewakan',
	));

//	E:REQUEST NEW ROUTE IPA ======================================================

	Router::connect('/p/:mlsid/:slug/*', array(
		'controller' => 'properties', 
		'action' => 'detail'
	), array(
		'mlsid' => '[A-Za-z-0-9]{8}',
		'pass' => array('slug'),
	));
	
	/* ROUTER FOR KALKULATOR KPR */

	Router::connect('/:bank_code/apply_kpr/*', array(
		'controller' => 'kpr', 
		'action' => 'apply_kpr'
	), array(
		'bank_code' => '.+[a-zA-Z-]',
	));

	Router::connect('/:bank_code/:slug/*', array(
		'controller' => 'kpr', 
		'action' => 'bank_calculator'
	), array(
		'slug' => 'kalkulator-kpr',
		'bank_code' => '.+[a-zA-Z-]',
	));

	Router::connect('/p/:mlsid', array(
		'controller' => 'properties', 
		'action' => 'shorturl'
	), array(
		'mlsid' => '[A-Za-z-0-9]{8}', 
		'pass' => array('mlsid'),
	));

	// Admin Director
	Router::connect('/admin/users/:slug/admins/add/*', array(
		'controller' => 'users', 
		'action' => 'add_admin',
		'admin' => true,
	), array(
		'slug' => 'director', 
	));
	Router::connect('/admin/users/:slug/admins/edit/*', array(
		'controller' => 'users', 
		'action' => 'edit_admin',
		'admin' => true,
	), array(
		'slug' => 'director', 
	));
	Router::connect('/admin/users/:slug/admins/*', array(
		'controller' => 'users', 
		'action' => 'admins',
		'admin' => true,
	), array(
		'slug' => 'director', 
	));

	/*
	**	BTN Term and Conditions
	*/

	Router::connect('/:project_slug/:params', array(
		'controller' => 'pages', 
		'action' => 'home'
	), array(
		'params' => 'termandconditions',
		'project_slug' => 'btn',
	));

	/**
     * HybridAuth
     */
    Router::connect('/social_login/*', array( 'controller' => 'users', 'action' => 'social_login'));
    Router::connect('/social_endpoint/*', array( 'controller' => 'users', 'action' => 'social_endpoint'));

/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	// Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
	
	Router::parseExtensions('json', 'xml');

//	minifier routes
	Router::connect('/min-js', array('plugin' => 'Minify', 'controller' => 'minify', 'action' => 'index', 'js'));
	Router::connect('/min-css', array('plugin' => 'Minify', 'controller' => 'minify', 'action' => 'index', 'css'));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';