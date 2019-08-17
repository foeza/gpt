<?php
/**
 * Exception Renderer
 *
 * Provides Exception rendering features. Which allow exceptions to be rendered
 * as HTML pages.
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
 * @package       Cake.Error
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Sanitize', 'Utility');
App::uses('Router', 'Routing');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');

/**
 * Exception Renderer.
 *
 * Captures and handles all unhandled exceptions. Displays helpful framework errors when debug > 1.
 * When debug < 1 a CakeException will render 404 or 500 errors. If an uncaught exception is thrown
 * and it is a type that ExceptionHandler does not know about it will be treated as a 500 error.
 *
 * ### Implementing application specific exception rendering
 *
 * You can implement application specific exception handling in one of a few ways:
 *
 * - Create an AppController::appError();
 * - Create a subclass of ExceptionRenderer and configure it to be the `Exception.renderer`
 *
 * #### Using AppController::appError();
 *
 * This controller method is called instead of the default exception handling. It receives the
 * thrown exception as its only argument. You should implement your error handling in that method.
 *
 * #### Using a subclass of ExceptionRenderer
 *
 * Using a subclass of ExceptionRenderer gives you full control over how Exceptions are rendered, you
 * can configure your class in your core.php, with `Configure::write('Exception.renderer', 'MyClass');`
 * You should place any custom exception renderers in `app/Lib/Error`.
 *
 * @package       Cake.Error
 */
class ExceptionRenderer {

/**
 * Controller instance.
 *
 * @var Controller
 */
	public $controller = null;

/**
 * template to render for CakeException
 *
 * @var string
 */
	public $template = '';

/**
 * The method corresponding to the Exception this object is for.
 *
 * @var string
 */
	public $method = '';

/**
 * The exception being handled.
 *
 * @var Exception
 */
	public $error = null;

/**
 * Creates the controller to perform rendering on the error response.
 * If the error is a CakeException it will be converted to either a 400 or a 500
 * code error depending on the code used to construct the error.
 *
 * @param Exception $exception Exception
 */
	public function __construct(Exception $exception) {
		$this->controller = $this->_getController($exception);

		if (method_exists($this->controller, 'appError')) {
			$this->controller->appError($exception);
			return;
		}

		$method		= $template = Inflector::variable(str_replace('Exception', '', get_class($exception)));
		$code		= $exception->getCode();
		$message	= $exception->getMessage();

		$methodExists = method_exists($this, $method);

		if ($exception instanceof CakeException && !$methodExists) {
			$method = '_cakeError';
			if (empty($template) || $template === 'internalError') {
				$template = 'error500';
			}
		} elseif ($exception instanceof PDOException) {
			$method = 'pdoError';
			$template = 'pdo_error';
			$code = 500;
		} elseif (!$methodExists) {
			$method = 'error500';
			if ($code >= 400 && $code < 500) {
				$method = 'error400';
			}
		}

	//	set as global variable
		$this->debug_mode = Configure::read('debug');

		if(empty($this->debug_mode)){
			if($method === '_cakeError'){
				$method = 'error400';
			}
			else if($code == 500){
				$method = 'error500';
			}
		}

		$this->template	= $template;
		$this->method	= $method;
		$this->error	= $exception;

	//	if($isNotDebug && $code != '404'){
		//	prime agent punya theme jadi disini settingnya
			$prefix	= $this->controller->params->prefix ?: 'default';
			$layout	= $prefix;

			if($prefix == 'admin'){
				$theme	= null;
			}
			else{
			//	untuk handle theme
				$companyData	= (array) Configure::read('Config.Company.data');
				$theme			= Hash::get($companyData, 'Theme.name');

				if(empty($companyData) || empty($theme)){
				//	empty company data	: kemungkinan error dari sisi appcontroller dan belum sempat isi variable config
				//	empty theme			: company belum setting theme / atau data theme di config ke apus

				//	pake layout ini karena ga banyak nuntut variable config database
					$layout = 'memberships';
				}
			}

			$siteName	= Configure::read('__Site.site_name');
		//	$message	= __('Error : %s %s', $code, $message);
			$message	= __('Error : %s', $code);

			$this->controller->layout	= $layout;
			$this->controller->theme	= $theme;

			$this->controller->set(array(
				'module_title' => $message, 
			));
	//	}
	}

/**
 * Get the controller instance to handle the exception.
 * Override this method in subclasses to customize the controller used.
 * This method returns the built in `CakeErrorController` normally, or if an error is repeated
 * a bare controller will be used.
 *
 * @param Exception $exception The exception to get a controller for.
 * @return Controller
 */
	protected function _getController($exception) {
		App::uses('AppController', 'Controller');
		App::uses('CakeErrorController', 'Controller');
		if (!$request = Router::getRequest(true)) {
			$request = new CakeRequest();
		}
		$response = new CakeResponse();

		if (method_exists($exception, 'responseHeader')) {
			$response->header($exception->responseHeader());
		}

		if (class_exists('AppController')) {
			try {
				$controller = new CakeErrorController($request, $response);
				$controller->startupProcess();
				$startup = true;
			} catch (Exception $e) {
				$startup = false;
			}
			// Retry RequestHandler, as another aspect of startupProcess()
			// could have failed. Ignore any exceptions out of startup, as
			// there could be userland input data parsers.
			if ($startup === false &&
				!empty($controller) &&
				$controller->Components->enabled('RequestHandler')
			) {
				try {
					$controller->RequestHandler->startup($controller);
				} catch (Exception $e) {
				}
			}
		}
		if (empty($controller)) {
			$controller = new Controller($request, $response);
			$controller->viewPath = 'Errors';
		}
		return $controller;
	}

/**
 * Renders the response for the exception.
 *
 * @return void
 */
	public function render() {
		if ($this->method) {
			call_user_func_array(array($this, $this->method), array($this->error));
		}
	}

/**
 * Generic handler for the internal framework errors CakePHP can generate.
 *
 * @param CakeException $error The exception to render.
 * @return void
 */
	protected function _cakeError(CakeException $error) {
		$url		= $this->controller->request->here();
		$code		= ($error->getCode() >= 400 && $error->getCode() < 506) ? $error->getCode() : 500;
		$message	= $error->getMessage();

	//	CUSTOM ERROR REPORT PRIME =======================================================================
	/*
		$this->RmCommon = $this->controller->Components->load('RmCommon');

		$reportMessage	= __('CAKEPHP ERROR %s : %s', $code, $message);
		$reportOptions	= array();

		if($error->queryString){
			$reportOptions['sql_dump'] = $error->queryString;
		}

		$this->RmCommon->errorReport($reportMessage, $code, $message, $error->getLine(), $error->getFile());
	*/
	//	=================================================================================================

		$this->controller->response->statusCode($code);
		$this->controller->set(array(
			'code'		=> $code, 
			'name'		=> h($message),
			'message'	=> h($message),
			'url'		=> h($url),
			'error'		=> $error,
			'_serialize' => array('code', 'name', 'message', 'url', 'error'), 
		));

		$this->controller->set($error->getAttributes());
		$this->_outputMessage('general_error');
	}

/**
 * Convenience method to display a 400 series page.
 *
 * @param Exception $error The exception to render.
 * @return void
 */
	public function error400($error){
		$url	= $this->controller->request->here();
		$code	= $error->getCode();

		if(empty($this->debug_mode) && $error instanceof CakeException){
			$message = __d('cake', 'Not Found');
		}
		else{
			$message = $error->getMessage();
		}

		$this->controller->response->statusCode($code);
		$this->controller->set(array(
			'code'		=> $code, 
			'name'		=> h($message),
			'message'	=> h($message),
			'url'		=> h($url),
			'error'		=> $error,
			'_serialize' => array('code', 'name', 'message', 'url', 'error'), 
		));

		$this->_outputMessage('general_error');
	}

/**
 * Convenience method to display a 500 page.
 *
 * @param Exception $error The exception to render.
 * @return void
 */
	public function error500($error){
		$url	= $this->controller->request->here();
		$code	= ($error->getCode() > 500 && $error->getCode() < 506) ? $error->getCode() : 500;

		if(empty($this->debug_mode)){
			$message = __d('cake', 'An Internal Error Has Occurred.');
		}
		else{
			$message = $error->getMessage();
		}

		$this->controller->response->statusCode($code);
		$this->controller->set(array(
			'code'		=> $code, 
			'name'		=> h($message),
			'message'	=> h($message),
			'url'		=> h($url),
			'error'		=> $error,
			'_serialize' => array('code', 'name', 'message', 'url', 'error'), 
		));

		$this->_outputMessage('general_error');
	}

/**
 * Convenience method to display a PDOException.
 *
 * @param PDOException $error The exception to render.
 * @return void
 */
	public function pdoError(PDOException $error){
		$url		= $this->controller->request->here();
		$code		= 500;
		$message	= $error->getMessage();

	//	CUSTOM ERROR REPORT PRIME =======================================================================
	/*
		$this->RmCommon = $this->controller->Components->load('RmCommon');

		$reportMessage	= __('DATABASE ERROR %s : %s', $code, $message);
		$reportOptions	= array();

		if($error->queryString){
			$reportOptions['sql_dump'] = $error->queryString;
		}

		$this->RmCommon->errorReport($reportMessage, $code, $message, $error->getLine(), $error->getFile(), $reportOptions);
	*/
	//	=================================================================================================

		$this->controller->response->statusCode($code);
		$this->controller->set(array(
			'code'		=> $code, 
			'name'		=> h($message),
			'message'	=> h($message),
			'url'		=> h($url),
			'error'		=> $error,
			'_serialize' => array('code', 'name', 'message', 'url', 'error'), 
		));

	//	$this->_outputMessage($this->template);
		$this->_outputMessage('general_error');
	}

/**
 * Generate the response using the controller object.
 *
 * @param string $template The template to render.
 * @return void
 */
	protected function _outputMessage($template) {
		try {
			$this->controller->render($template);
			$this->controller->afterFilter();
			$this->controller->response->send();
		} catch (MissingViewException $e) {
			$attributes = $e->getAttributes();
			if (isset($attributes['file']) && strpos($attributes['file'], 'error500') !== false) {
				$this->_outputMessageSafe('error500');
			} else {
				$this->_outputMessage('error500');
			}
		} catch (MissingPluginException $e) {
			$attributes = $e->getAttributes();
			if (isset($attributes['plugin']) && $attributes['plugin'] === $this->controller->plugin) {
				$this->controller->plugin = null;
			}
			$this->_outputMessageSafe('error500');
		} catch (Exception $e) {
			$this->_outputMessageSafe('error500');
		}
	}

/**
 * A safer way to render error messages, replaces all helpers, with basics
 * and doesn't call component methods.
 *
 * @param string $template The template to render
 * @return void
 */
	protected function _outputMessageSafe($template) {
		$this->controller->layoutPath = null;
		$this->controller->subDir = null;
		$this->controller->viewPath = 'Errors';
		$this->controller->layout = 'error';
		$this->controller->helpers = array('Form', 'Html', 'Session');

		$view = new View($this->controller);
		$this->controller->response->body($view->render($template, 'error'));
		$this->controller->response->type('html');
		$this->controller->response->send();
	}

}
