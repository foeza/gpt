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
	Router::connect('/', array(
		'controller' => 'pages', 
		'action' => 'home'
	));
	Router::connect('/login/*', array(
		'controller' => 'users', 
		'action' => 'login',
		'admin' => true,
	));

	Configure::write('Route.action', 'developers|about|contact|faq|career|developer_detail');
	Router::connect('/:action/*', array(
		'controller' => 'pages', 
	), array(
		'action' => Configure::read('Route.action'),
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

	Router::connect('/p/:mlsid-:slug', array(
		'controller' => 'properties', 
		'action' => 'detail'
	), array(
		'pass' => array('mlsid', 'slug'),
		'mlsid' => '[A-Za-z-0-9]{8}',
	));

	Router::connect('/p/:mlsid', array(
		'controller' => 'properties', 
		'action' => 'shorturl'
	), array(
		'mlsid' => '[A-Za-z-0-9]{8}', 
		'pass' => array('mlsid'),
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