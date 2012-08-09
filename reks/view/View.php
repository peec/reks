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
namespace reks\view;

use reks\i18n\Lang,
	reks\core\App,
	reks\router\Router;

/**
 * This View class is accessible from the controller in
 * $this->view and in the view files referenced with $view.
 *
 *
 * @author REKS group at Telemark University College
 * @version 1.0
 */
class View{
	
	/**
	 * Array of configuration from config.php
	 * @var reks\core\Config
	 */
	public $config;
	
	/**
	 * Array of view variables. Content of the value can be anything.
	 * @var array
	 */
	protected $viewVars = array();

	/**
	 * Holds the language class variable.
	 * Used for multi language projects.
	 * 
	 * @var reks\core\Lang language reference variable.
	 */
	public $lang;
	
	/**
	 * Use to add form listeners and create forms in the view.
	 * @var reks\view\html\form\FormWrapper Form wrapper class.
	 */
	public $form;
	
	/**
	 * This will become true after render() method is run for the first time.
	 * First time render will send additional header data to the browser.
	 * @var boolean
	 */
	private $headersSent = false;
	

	
	/**
	 * Url helper.
	 * @var reks\http\Url
	 */
	public $url;

	/**
	 * Application instance.
	 * @var reks\core\App
	 */
	public $app;
	
	
	/**
	 * GET csrf token name.
	 * @var string
	 */
	const CSRF_TOKEN_NAME = 'csrf_token_';
	
	/**
	 * @var reks\view\html\Html HTML instance, manipulate the html.
	 */
	public $html;
	
	/**
	 * Array of cache queue
	 * @var array
	 */
	private $cacheQueue = array();
	protected $viewQueue = array();
	
	
	/**
	 * Router instance. Private.
	 * @var reks\router\Router
	 */
	public $router;
	
	/**
	 * Deal with scripts ( JS and CSS )
	 * @var reks\view\script\Scripts
	 */
	public $scripts;
	
	
	
	public $reservedVariables = array(
			'e', // Output closure.
			'url', // Url class
			'view', // View instance ($this)
			'lang' // Language
			);
	
	/**
	 * Constructs a new view object.
	 * @param array $config Global configuration.
	 */
	public function __construct(\reks\core\Config $config, Lang $lang, $url, App $app, Router $router){
		$this->config = $config;
		$this->lang = $lang;
		$this->form = new html\form\FormWrapper;
		
		$this->url = $url;
		$this->app = $app;
		$this->router = $router;
		$this->scripts = new script\Scripts($this);

		$self = $this;

		$this->html = new html\Html($this);
		
		// Assign core vars;
		$this->viewVars['out'] = function($var) use($self){
			$self->out($var);
		};
		$this->viewVars['url'] = $url;
		$this->viewVars['view'] = $this;
		$this->viewVars['lang'] = $lang;
		
	}

	public function appendViewHandler(View $v){
		$this->viewHandlers[] = $v;
	}

	/**
	 * Assigns a variable to the view.
	 *
	 * @param string $var Variable name.
	 * @param mixed $value Value ( can be any type such as object, array , string and etc )
	 * @param boolean $stripXss Strips all XSS attacks ( default is true )
	 */
	public function assign($var, $value){
		if (in_array($var, $this->reservedVariables))throw new \Exception("Can not assign $var to $value. $var is a reserved variable in reks framework.");
		$this->viewVars[$var] = $value;
	}
	
	/**
	 * Echo XSS stripped variable. Uses escape() method.
	 * @param string $var
	 */
	public function out($var){
		if (is_object($var))echo $var;
		else echo $this->escape($var);
	}
	
	/**
	 * Alias of stripXSS.
	 * @param mixed $var
	 */
	public function escape($var){
		return htmlspecialchars($var, ENT_QUOTES, 'UTF-8');
	}
	/**
	 * Fetches content of a view file.
	 * @param string $viewFile
	 * @param array $extraVars
	 */
	public function fetch($viewFile, $extraVars=array(), $runQueue = false, $stringdata=false){
		$charset = 'UTF-8';
		$contentType='text/html';
		
		// Set the path to the view file ( real path ).
		$fp = $this->app->APP_PATH . '/view/' . $viewFile . '.php';
		
		if (realpath($fp))$viewFilePath = $fp; // If this is a  absolute path.
		else $viewFilePath = $viewFile;
		
		$vars = $this->viewVars;
		$vars = array_merge($vars, $extraVars);
		
		// Extract all the assigned vars. So basically the array will have
		// key => value
		// keyname will be the $variablename to the view file.
		extract($vars);
		
		if (!$this->headersSent){
			header('Content-Type:'.$contentType.'; charset=' . $charset);
			$this->headersSent = true;
		}
		ob_start();
		
		// And .. finally - lets include the view file with simple include.
		if ($stringdata) echo file_get_contents($viewFilePath);
		elseif(substr($viewFile, -10) == '.twig.html'){ // Use twig.
			// Load twig if not loaded.
			$twig = $this->app->loadVendor('\reks\vendor\TwigLoader')->getTwig();
			echo $twig->render($viewFilePath, $this->viewVars);
		}else{
			include $viewFilePath;
		}
		
		
		$content = ob_get_clean();			
		$this->runQueue($runQueue);
		
		// if we should cache.
		if (isset($this->cacheQueue[$viewFile])){
			file_put_contents($this->cacheQueue[$viewFile], $content);
		}
		return $content;
	}
	/**
	 * Renders ( outputs ) a view file to the browser.
	 *
	 * @param string $viewFile filepath to the view file ( without reference to view/ and without .php extension )
	 * @param array $extraVars Array of extra variables to be passed to the view.
	 * @param boolean $runQueue Run the view queue ? 
	 * @param boolean $stringdata In cases where the view file should be threated as a normal file ( no php is supported if true )
	 */
	public function render($viewFile, $extraVars=array(), $runQueue = false, $stringdata=false){
		echo $this->fetch($viewFile, $extraVars, $runQueue, $stringdata);
	}
	/**
	 * Runs view queue. After method run view Queue is reset.
	 * true = Runs all in queue.
	 * string = View file to run in queue.
	 * closure = function($queue){ foreach($queue as $q){ }}
	 * array = array('header','functions') , array of view files.
	 * @param mixed $runQueue Can be array, string, closure and true.
	 */
	protected function runQueue($runQueue){
		if ($runQueue === true){
			// Run queue.
			foreach($this->viewQueue as $key => $q){
				unset($this->viewQueue[$key]);
				$q->render();
			}
		}elseif($runQueue !== null && is_string($runQueue)){
			$this->viewQueue[$runQueue]->render();
			unset($this->viewQueue[$runQueue]);
		}elseif($runQueue !== null && is_callable($runQueue)){
			$runQueue($this->viewQueue);
			$this->viewQueue = array();
		}elseif($runQueue !== null && is_array($runQueue)){
			foreach($this->viewQueue as $key => $q){
				if (in_array($key, $runQueue)){
					$q->render();
					unset($this->viewQueue[$key]);
				}
			}
		}
	}
	

	/**
	 * Sets caching mode on - on a specific view file, 
	 * : if we get a cache file this method will 
	 *  - render the cached content.
	 *  - return true.
	 * : if we dont get any cache
	 *  - set the view file for cache appending.
	 *  - on next reload, the cache file will be loaded.
	 * Example of use ( in controller ):
	 * <code>
	 * 		if ($this->view->cache('viewfile')){
	 * 			return;
	 * 		}
	 * </code>
	 * @param string $viewFile The view file to be cached.
	 * @param int $cacheForSecs Amount of seconds to cache the cached file.
	 * @param mixed $seed A string or int making this cached file unique. Useful for db content. Example to include db id. 
	 */
	public function cache($viewFile, $cacheForSecs = 7200, $seed = null){
		$token = sha1($viewFile . $seed).'.php';
		$cachePath = $this->app->APP_PATH . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'views';
		$fp = $cachePath . DIRECTORY_SEPARATOR . $token;
		if (file_exists($fp) && filemtime($fp)+$cacheForSecs > time()){
			$this->render($fp, array(), null, true);
			return true;
		}
		
		if (!file_exists($cachePath))mkdir($cachePath);
		
		$this->cacheQueue[$viewFile] = $fp;
		
		return false;
	}

	/**
	 * Returns a $_GET key stripped for XSS injections.
	 * 
	 * Note! Returns null if key is not set.
	 * @param string $key Key of the $_GET array.
	 */
	public function get($key){
		return isset($_GET[$key]) ? $this->stripXSS($_GET[$key]) : null;
	}
	
	/**
	 * Returns a $_POST key stripped for XSS injections.
	 * 
	 * Note! Returns null if key is not set.
	 * @param string $key Key of the $_GET array.
	 */
	public function post($key){
		return isset($_POST[$key]) ? $this->stripXSS($_POST[$key]) : null;
	}
	
	/**
	 * Gets all the assigned view variables as key / value pair.
	 */
	public function getVars(){
		return $this->viewVars;
	}
	
	/**
	 * Returns a modules view instance.
	 * @param string $name Module name
	 * @return reks\View
	 */
	public function mod($name){
		return $this->app->module->get($name)->getTargetRouter()->getResource(App::RES_VIEW);
	}

	/**
	 * Returns the super application ( parent application ) View
	 * @return reks\View
	 */
	public function super(){
		return $this->app->superRouter->getResource(App::RES_VIEW);
	}
	
	public function getExecutionTime(){
		return $this->router->getExecutionTime();
	}
	
	
	/**
	 * Queue's a view file, when the next render method is used it will be rendered.
	 * Especially useful for header files and such.
	 * @param unknown_type $viewFile
	 * @param unknown_type $extraVars
	 */
	public function queue($viewFile, $extraVars=array()){
		$this->viewQueue[$viewFile] = new ViewQueue($this, $viewFile, $extraVars);
	}
	
	public function getQueue($viewFile){
		if (!isset($this->viewQueue[$viewFile]))throw new \Exception("Queue key $viewFile is not registered.");
		return $this->viewQueue[$viewFile]->view;
	}
	
	
}

class ViewQueue{
	public $view;
	public $viewFile;
	public $extraVars = array();
	public function __construct(View $view, $viewFile, array $extraVars = array()){
		$this->view = $view;
		$this->viewFile = $viewFile;
		$this->extraVars = $extraVars;
	}
	
	public function render(){
		$this->view->render($this->viewFile, $this->extraVars);
	}
}