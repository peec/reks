<?php
/**
 * REKS framework is a very lightweight and small footprint PHP 5.3+ Framework.
 * It supports a limited set of features but fully MVC based and Objectoriented.
 * 
 * Copyright (c) 2012, REKS group ( Lars Martin Rørtveit, Andreas Elvatun, Petter Kjelkenes, Pål André Sundt )
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the REKS GROUP nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL "REKS Group" BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @license 3-clause BSD
 * @package reks
 * @author REKS group at Telemark University College
 */
namespace reks\controller;



use reks\http\Url,
	reks\router\Router,
	reks\i18n\Lang,
	reks\http\Userinput,
	reks\security\Csrf,
	reks\view\View,
	reks\http\Request,
	reks\http\Response,
	reks\core\Log,
	reks\router\ActiveRoute;

/**
 * All controllers in the application should EXTEND this super class.
 * This class have features and references to View and running model classes.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 *
 */
abstract class Controller{
	/**
	 * Array of configuration from config.php
	 * @var array Array of configuration
	 */
	public $config;

	/**
	 * The View class reference.
	 * Use this to output html and other view specific tasks.
	 *
	 * @var reks\view\View A reference to the View class.
	 */
	public $view;




	/**
	 * Private reference to the router.
	 * No controllers have access to this.
	 *
	 * @var reks\router\Router Router reference.
	 */
	protected $router;


	/**
	 * Holds the language class variable.
	 * Used for multi language projects.
	 *
	 * @var reks\i18n\Lang language reference variable.
	 */
	public $lang;


	/**
	 * Holds the request object.
	 * 
	 * @var reks\http\Request
	 */
	public $request;
	
	/**
	 * Deals with models.
	 * Wrapper around loading and getting models.
	 * @var reks\repo\Repository
	 */
	public $model;
	
	
	/**
	 * Wrapper around all possible user-input data for PHP.
	 * @var reks\http\Userinput
	 */
	public $ui;
	
	/**
	 * CSRF protection library
	 * @var reks\security\Csrf
	 */
	public $csrf;
	
	

	/**
	 * Logger instance. Used to log application data.
	 * @var reks\core\Log
	 */
	public $log;
	
	/**
	 * Logger instance. Used to log application data.
	 * @var reks\http\Url
	 */
	public $url;
	
	/**
	 * Gives you a object of string representation of controller / method / args that are currently running.
	 * @var reks\router\ActiveRoute
	 */
	public $activeRoute;

	
	/**
	 * Contains this application specific features.
	 * @var reks\core\App
	 */
	public $app;
	
	const C_HTML = '';
	const C_JSON = 'application/json';
	const C_TEXT_PLAIN = 'text/plain';
	const C_JAVASCRIPT = 'text/javascript';
	
	
	/**
	 * Creates a new controller.
	 * If sub classes overrides this method they must remember to call this method with parent::__construct(...)
	 * @param array $config Array of configuration from config.php
	 * @param reks\router\Router $router Router reference.
	 */
	final public function __construct(\reks\core\Config $config, Router $router, Url $url, Lang $lang, Userinput $ui, Csrf $csrf, View $view, Request $request, Log $log, \reks\repo\Repository $repo, ActiveRoute $activeRoute = null){
		$this->config = $config;
		$this->router = $router;
		$this->app = $router->app;
		$this->url = $url;
		
		$this->lang = $lang;
		$this->ui = $ui;
		$this->csrf = $csrf;
		$this->view = $view;
		$this->request = $request;
		$this->log = $log;
		$this->model = $repo;
		$this->activeRoute = $activeRoute;
	}
	
	/**
	 * Factory for controller
	 * @param reks\router\Router $r
	 * @param array $config
	 * @param string $controller
	 * @param reks\http\Url $url
	 * @param reks\i18n\Lang $lang
	 * @param reks\http\Userinput $ui
	 * @param reks\security\Csrf $csrf
	 * @param reks\view\View $view
	 * @param reks\http\Request $request
	 * @param reks\core\Log $log
	 * @param reks\repo\Repository $repo
	 * @param reks\router\ActiveRoute Active route object.
	 * @return reks\controller\Controller
	 */
	static public function init(Router $r, \reks\core\Config $config, $controller, Url $url, Lang $lang, Userinput $ui, Csrf $csrf, View $view, Request $request, Log $log, \reks\repo\Repository $repo, ActiveRoute $activeRoute = null){
		$c = new $controller($config, $r, $url, $lang, $ui, $csrf, $view, $request, $log, $repo, $activeRoute);
		$c->setup();
		return $c;
	}

	/**
	 * Setup method will run right after constructor.
	 * Override this to setup a base controller.
	 * 
	 * Note! This will be run BEFORE any methods.
	 */
	protected function setup(){
		
	}



	/**
	 * A general method to trigger http status codes such as 404, or just to run a specific router rule.
	 *
	 * Example of trigger (404 error):
	 * $this->trigger('404');
	 *
	 * After router rule is triggered, php ends.
	 * 
	 * @param string $key The key of the $config['router'][X], where X is the key of the router rule to run.
	 * @param array $arguments Arguments to be passed to the method of the controller that gets loaded.
	 */
	public function trigger($key, array $arguments=array()){
		$this->router->trigger($key, $arguments);
		die();
	}

	/**
	 * Sends a http status code to browser.
	 * @param string $httpCode A valid http code.
	 */
	public function sendStatus($httpCode){
		header(' ', true, $httpCode);
	}

	/**
	 * Redirects user to a specific location.
	 * @param string $location
	 */
	public function redirect($location){
		$this->log->debug("Recieved redirect to $location.");
		
		header("Location: {$location}");
		die();
	}

	/**
	 * @param string $path The controller / method ( Format: Controller.method )
	 * @param array $args If route has arguments. Arguments with correct key assignment eg. @title, @id should be array( 'title' => 'hello-world', 'id' => 5 )
	 * @param array $params $_GET Parameters 
	 */
	public function internalRedirect($path, array $args=array(), array $params = array()){
		$this->redirect($this->url->reverse($path, $args, null, $params));
	}
	/**
	 * Safe redirect user.
	 * @param string $path The controller / method ( Format: Controller.method )
	 * @param array $args If route has arguments. Arguments with correct key assignment eg. @title, @id should be array( 'title' => 'hello-world', 'id' => 5 )
	 * @param array $params $_GET Parameters
	 */
	public function internalRedirectSafe($path, array $args=array(), array $params = array()){
		$this->redirect($this->url->reverseSafe($path, $args, null, $params));
	}

	/**
	 * Gets execution time at this time.
	 */
	public function getExecutionTime(){
		return $this->router->getExecutionTime();
	}


	/**
	 * Adds a listener to a form created with the view form API.
	 * This method is very useful, contains csrf protection and more.
	 * 
	 * Example:
	 * <code>
	 * // Example of method.
	 * $posted = $this->addFormListener('post', 'loginForm', 'loginRequest');
	 * ...
	 * protected function loginRequest($ui){
	 * 		return $this->model->News->insert($ui->title, $ui->body);
	 * }
	 * // Example with closure
	 * $self = $this;
	 * $posted = $this->addFormListener('post', 'loginForm', function($ui) use ($self){
	 * 		return $self->model->News->insert($ui->title, $ui->body);
	 * });
	 * 
	 * // Example procedural
	 * if ($this->addFormListener('post', 'loginForm')){
	 * 		$ui = $this->ui->post;
	 * 		return $self->model->News->insert($ui->title, $ui->body);
	 * }
	 * </code>
	 * @param string $uiType UI types can be all user input, example. 'request', 'post', 'get' etc.
	 * @param string $formId The form id to add the listener too.
	 * @param mixed $methodOrClosure Can be a closure or method or array. Arguments in these are the ui element for the $uiType. So if $uiType = post you can do like function(\reks\Input $ui){$ui->name $ui->title}
	 * @return reks\view\html\form\FormWrapper
	 */
	public function addFormListener($formId, $uiType, $methodOrClosure=null, $customAction=false){
		$uiType = strtolower($uiType);
		
		// API Failure check.
		if ($uiType != 'post' 
			&& $uiType != 'get' 
			&& $uiType != 'request')
			throw new \Exception("UI Type $uiType is not supported");
		
		// Also support request, but set default to post.
		if ($uiType == 'request')$viewUi = 'post';
		else $viewUi = $uiType;
		
		// Input object.
		$input = null;
		// If we get request to this.
		$posted = $this->request->$uiType && $this->ui->$uiType->$formId;
		
		// Add listener.
		$this->view->form->addListener($formId, ($customAction ? $customAction : $this->url->fetchUri()),$viewUi, $this->csrf->token('csrf_tok_uniq'), $input);
		
		
		// Listener for post.
		if ($posted){
			$this->csrf->assertValidate($this->ui->$uiType->csrf_tok_uniq);
			
			unset($this->ui->$uiType->csrf_tok_uniq);
			unset($this->ui->$uiType->$formId);
			
			// Safe to regenerate.
			$this->csrf->refreshToken('csrf_tok_uniq');
			
			// Set input object, we will deliver this to the view.	
			$input = $this->ui->$uiType;	
			if(is_callable($methodOrClosure))
				$methodOrClosure($input);
			elseif($methodOrClosure === null){
					
			}else 
				$this->$methodOrClosure($input);					
		}
		
		
		$this->log->debug("Added form listener to $formId. ");
		
		return $posted;
	}
	
	
	
	
	/**
	 * Returns a controller instance based on full namespace.
	 * Note: Does not run the setup method.
	 * @param string $ns The full namespace.
	 * @return reks\Controller
	 */
	public function getController($ns){
		$c = new $ns($this->config, $this->router, $this->url, $this->lang, $this->ui, $this->csrf, $this->view, $this->request, $this->log, $this->model, $this->activeRoute);
		return $c;
	}

	
}



