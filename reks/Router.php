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
namespace reks;



/**
 * Router implementation.
 * Very simple router that allows routing of urls to use a specific controller / method.
 * It also supports dynamic arguments.
 * 
 * Router is created with the State Machine principle.
 * See constants for states.
 *
 * @author REKS group at Telemark University College
 * @version 1.0
 *
 */
class Router{
	/**
	 * Array of configuration from config.php
	 * @var array
	 */
	public $config = array();


	/**
	 * The starting time of the constructor of the Router in float microtime.
	 * @var float
	 */
	private $startTime;


	/**
	 * Logger instance. Used to log application data.
	 * @var reks\Log
	 */
	public $log;

	
	
	/**
	 * Array of routes.
	 * @var array
	 */
	public $routes = array();
	
	/**
	 * Path info reference for the URL.
	 * This should be "/" for main page ( no arguments in the url )
	 * Ususally $_SERVER['PATH_INFO'] is just correct to pass into this variable.
	 * @var string
	 */
	public $uri;
	
	public $reverseCache = array();
	
	/**
	 * 
	 * @var reks\App
	 */
	public $app;
	
	
	private $sharedResources = array();
	
	private $globalResources = array();
	
	
	
	/**
	 * Constructs the router values.
	 */
	public function __construct(App $app, $startTime, $pathInfo){
		$this->app = $app;
		$this->startTime = $startTime;
		$this->uri = $pathInfo;
	}
	
	public function setLog(Log $log){
		$this->log = $log;
	}
	public function setRoutes(array $routes){
		// Add built in routes.
		$this->routes[] = new RouteRule($this, '/jsroutes', '/reks/JSController.routes', 'get');
		
		// Add custom routes.
		foreach($routes as $type => $route){
			foreach($route as $from => $to){
				$this->routes[] = new RouteRule($this, $from, $to, $type);
			}
		}
		
	}
	public function setConfig(array $config){
		$this->config = $config;
	}
	
	public function getResources(){
		return $this->sharedResources;
	}
	public function setResource($name, $val){
		$this->sharedResources[$name] = $val;
	}
	public function getResource($name){
		if(isset($this->sharedResources[$name])){
			return $this->sharedResources[$name];
		}else{
			throw new \Exception("REKS Framework asked for resources '$name', but it did not exist.");
		}
	}
	
	public function getGlobalResources(){
		return $this->globalResources;
	}
	
	public function setGlobalResource($name, $val){
		$this->globalResources[$name] = $val;
	}
	
	public function getGlobalResource($name){
		if(isset($this->globalResources[$name])){
			return $this->globalResources[$name];
		}else{
			throw new \Exception("REKS Framework asked for global resource '$name', but it did not exist.");
		}
	}
	
	
	
	/**
	 * Sets the uri.
	 * @param string $uri Input URI ( eg. / or /test )
	 */
	public function setURI($uri){
		$this->uri = $uri;
	}

	
	/**
	 * Reverses a route by controller / method and arguments.
	 * If this method does not find a method - exception will be thrown.
	 * 
	 * Focusing on holding this algorithm fast is a must.
	 * 
	 * @param string $path The path ( controller.method )
	 * @param array $args Arguments, keys must be the same name as in the routes file.
	 * @param string $requestType get, post or  * / null if you want to look for all.
	 * @throws \Exception Exception is thrown if no routes is found.
	 */
	public function reverse($path, $args=array(), $requestType = null){
		// If no path, well return nothing.
		if (!$path) return;
		// We could also implode array_keys of $args, but that takes to much time, enough with values 
		// Wich are indeed the best way to go.
		$hash = $path.implode('', $args);
		// Allow cached version to skip all the parsing.
		if (isset($this->reverseCache[$hash])) return $this->reverseCache[$hash];
		
		// Loop all types.
		foreach($this->routes as $route){
			if ($requestType !== null && $requestType != '*' && $route->getType() != $requestType)continue;
			if ($found = $route->reverse($path, $args)){
				$this->reverseCache[$hash] = $found;	
				return $this->reverseCache[$hash];
			}
		}
	
		throw new RouterReverseException("Could not reverse route $path with arguments ".implode(', ', $args).". No applicable route found for this controller/method combination.");
	}
	
	
	/**
	 * Tries to parse the routes.
	 */
	public function route(){
		$ok = $this->routeTrigger();
		if (!$ok)$this->trigger('404', null, array(), 404);
	}
	

	/**
	 * Tries to route to locations.
	 */
	public function routeTrigger(){
		$this->app->module->preload();
		
		$status = false;
		try{
			if ($components = $this->dispatch()){
				list ($controller, $method, $args) = $components;
				try{
					$this->load($controller, $method, $args);
					return true;
				}catch(InternalServerError $e){
					$c = $this->trigger('500', null, array('message' => $e->getMessage()), 500);
				}
			}else {
				// On exception lets check routing of modules.
				if (count($this->app->module->modules) > 0){
					foreach($this->app->module->modules as $mod){
						try{
							$status = $mod->getTargetRouter()->routeTrigger();
							if($status)return true;
						}catch(\Exception $e){
							$status = false;
						}
					}
				}
			}
		}catch(\Exception $e){
			$this->log->error($e->getMessage());
				
			echo "
			<h1>".get_class($e)."</h1>
			<p><strong>Message:</strong><br />{$e->getMessage()}</p>
			<p><strong>Stack:</strong><br />{$e->getTraceAsString()}</p>
			";
				
			$status = false;
		}
		return $status;
	}
	
	private function dispatch(){
		// Make them to lower.
		$requestType = strtolower($_SERVER['REQUEST_METHOD']);
		
		
		foreach($this->routes as $route){
			if ($requestType == $route->getType()  || $route->getType() == '*'){
				if ($r = $route->compare())return $r;
			}
		}
		return null;
	}
	
	
	public function deleteRoutes(){
		$this->routes = array();
	}
		

	/**
	 * Triggers a specific router rule based on the router rule name.
	 *
	 * @param string $key The key of the router rule.
	 * @param array $arguments Arguments that should be passed to the method.
	 * @return reks\Controller
	 * @throws reks\InternalServerError Throw exception if we could not trigger the route.
	 */
	public function trigger($key, array $arguments = null, array $viewVars = array(), $statusCode = null){
		foreach($this->routes as $route){
			if ($route->getFrom() == $key){
				list($controller, $method, $args) = $route->parseTo();	
				return $this->load($controller, $method, $arguments, $viewVars, $statusCode);
			}
		}
		throw new InternalServerError("Could not load route $key. ");
		
	}


	/**
	 * This method should be run by the router itself.
	 *
	 *
	 * @param string $controller Controller name.
	 * @param string $method Method name.
	 * @param array $args Arguments that should be passed into the Method.
	 * @param array $assignVars View variables to be passed.
	 * @param int $statusCode What HTTP code do you want to use ?
	 * @return reks\Controller A controller object.
	 */
	protected function load($controller, $method, $args=null, array $assignVars=array(), $statusCode = null){
		$rawController = $controller;
		// Conform to php NS syntax.
		$controller = str_replace('/', '\\', $controller);
		
		// Add namespace.
		if (substr($controller, 0, 1) != '\\'){
			$controller = '\controller\\' . $controller;
		}
		
		if (!class_exists($controller))throw new InternalServerError("Could not find class {$controller} because its not really there..");		
		if (!method_exists($controller, $method))throw new InternalServerError("Could not find METHOD {$method} in class {$controller}.");

		// Components....
		
		$c = Controller::init(
			$this, 
			$this->config, 
			$controller, 
			$this->getResource(App::RES_URL), 
			$this->getResource(App::RES_STATE), 
			$this->getResource(App::RES_LANG), 
			$this->getResource(App::RES_UI), 
			$this->getResource(App::RES_CSRF), 
			$this->getResource(App::RES_VIEW), 
			$this->getResource(App::RES_REQUEST), 
			$this->log, 
			$this->getResource(App::RES_MODELWRAPPER),
			new ActiveRoute($this->getResource(App::RES_URL), $rawController, $controller, $method, $args)
		);
				
		
		if ($statusCode){
			$c->sendStatus($statusCode);
		}
		
		$this->log->debug("Loaded $controller::$method");
		
		foreach($assignVars as $key => $val){
			$c->view->assign($key, $val);
		}
		
		if (is_array($args) && count($args) > 0){
			$result = call_user_func_array(array($c, $method), $args);
		}else $result = $c->$method();

		if ($result && $result instanceof Response){
			$result->setView($this->getResource(App::RES_VIEW));
			$result->execute();
		}
		
		return $c;
	}

	/**
	 * Tests all the routes.
	 * 
	 * @throws RouteParseException Throws RouteParseException on failure to compile a specific route.
	 */
	public function testRoutes(){
		foreach($this->routes as $route){
			list($from, $vars) = $route->vParser();
			foreach($vars as $k => $v){
				$vars[$k]['val'] = 0;
			}
			$route->getBackend($vars);
		}
	}
	
	

	/**
	 * Gets execution time at this time.
	 */
	public function getExecutionTime(){
		return microtime(true) - $this->startTime;
	}

}

/**
 * InternalServerError is a custom Exception to be casted when there are errors that should stop all code and stop execution.
 * When this Exception is casted from any controller user will get a 500 http response and a custom 500 page can be set.
 *
 * @author REKS group at Telemark University College
 *
 */
class InternalServerError extends \Exception{

}
class RouterReverseException extends \Exception{
	
}
class RouteParseException extends \Exception{
	public function __construct($message, $expectedSymbols=null){
		if ($expectedSymbols !== null){
			$message .= "Expected One Of $expectedSymbols";
		}
		parent::__construct("Router parse exception ".$message, 1, null);
	}
}

/**
 * Class to return active router pair combination.
 * @author loltroll
 *
 */
class ActiveRoute{
	private $args;
	private $controller;
	private $method;
	private $rawController;
	/**
	 * 
	 * @var reks\Url
	 */
	private $url;
	
	/**
	 * 
	 * @param string $controller Controller name.
	 * @param string $method Method name
	 * @param mixed $args Null or array of args.
	 */
	public function __construct($url, $rawController, $controller, $method, $args){
		$this->args = $args;
		$this->controller = $controller;
		$this->method = $method;
		$this->url = $url;
		$this->rawController = $rawController;
	}
	/**
	 * Fetches array or NULL of arguments passed.
	 */
	public function getArgs(){
		return $this->args;
	}
	/**
	 * Fetches the controller name of the active real controller.
	 */
	public function getController(){
		return $this->controller;
	}
	/**
	 * Returns the raw controller name from routing config.
	 */
	public function getRawController(){
		return $this->rawController;
	}
	/**
	 * Fetches the method name of the active real controller/method.
	 */
	public function getMethod(){
		return $this->method;
	}
	
	/**
	 * Rerverses this route.
	 */
	public function reverse(){
		return $this->url->reverse($this->getRawController().'.'.$this->getMethod(), $this->getArgs());
	}
	
	
}