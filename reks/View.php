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
	 * @var array
	 */
	public $config = array();
	
	/**
	 * Array of view variables. Content of the value can be anything.
	 * @var array
	 */
	protected $viewVars = array();

	/**
	 * Holds the language class variable.
	 * Used for multi language projects.
	 * 
	 * @var reks\Lang language reference variable.
	 */
	public $lang;
	
	/**
	 * Use to add form listeners and create forms in the view.
	 * @var reks\form\FormWrapper Form wrapper class.
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
	 * @var reks\Url
	 */
	public $url;

	/**
	 * Application instance.
	 * @var reks\App
	 */
	public $app;
	
	
	/**
	 * GET csrf token name.
	 * @var string
	 */
	const CSRF_TOKEN_NAME = 'csrf_token_';
	
	/**
	 * Header section class, makes things awfully easy!
	 * @var reks\view\Head
	 */
	public $head;
	
	/**
	 * Array of cache queue
	 * @var array
	 */
	private $cacheQueue = array();
	
	
	
	/**
	 * Constructs a new view object.
	 * @param array $config Global configuration.
	 */
	public function __construct(array $config, Lang $lang, $url, App $app){
		$this->config = $config;
		$this->lang = $lang;
		$this->form = new \reks\form\FormWrapper;
		
		$this->url = $url;
		$this->head = new view\Head($this);
		$this->app = $app;
	}

	public function appendViewHandler(View $v){
		$this->viewHandlers[] = $v;
	}

	/**
	 * Assigns a variable to the view.
	 *
	 * XSS attacks is automatically escaped from the $value field.
	 *
	 * @param string $var Variable name.
	 * @param mixed $value Value ( can be any type such as object, array , string and etc )
	 * @param boolean $stripXss Strips all XSS attacks ( default is true )
	 */
	public function assign($var, $value, $stripXss = true){
		$this->viewVars[$var] = $stripXss ? $this->stripXSS($value) : $value;
	}

	/**
	 * Assigns a multi dimensional variable to the view.
	 *
	 * XSS attacks is automatically escaped from the $value field.
	 * 
	 * Sample usage:
	 * <code>
	 * $this->view->assignMulti('links','home',$this->url->reverse('Home.index'));
	 * </code>
	 * 
	 * @param string $var Variable name.
	 * @param string $key The key of the multidemnsional array.
	 * @param mixed $value Value ( can be any type such as object, array , string and etc )
	 * @param boolean $stripXss Strips all XSS attacks ( default is true )
	 */
	public function assignMulti($var, $key, $value, $stripXss){
		$this->viewVars[$var][$key] = $stripXss ? $this->stripXSS($value) : $value;
	}
	/**
	 * Recursive method.
	 *
	 * Can remove XSS attacks from both strings and arrays.
	 * Uses htmlentities, ENT_QUOTES , UTF-8
	 * 
	 * @param mixed $var A variable to strip for XSS attacks.
	 */
	public function stripXSS($var){
		// Well this was easy, lets escape that shall we ?
		if (is_string($var))return htmlentities($var, ENT_QUOTES, 'UTF-8');

		// This is a array, here we can need recursive...
		if (is_array($var)){
			foreach($var as $k => $v){
				// $v can be array too, and any type for that case ... so .. make it call itself.
				$var[$k] = $this->stripXSS($v);
			}
			// Return the array.
			return $var;
		}

		// Well, this is either a int, float and so fourth - meaning it does not need to be escaped. Return.
		return $var;
	}


	/**
	 * Renders ( outputs ) a view file to the browser.
	 *
	 * @param string $viewFile filepath to the view file ( without reference to view/ and without .php extension )
	 * @param array $extraVars Array of extra variables to be passed to the view.
	 * @param string $charset Default - utf-8.
	 * @param string $contentType The type of the content being rendered, default is text/html. For xml you shoul do text/xml
	 * @param boolean $stringdata In cases where the view file should be threated as a normal file ( no php is supported if true )
	 */
	public function render($viewFile, $extraVars=array(), $charset = 'UTF-8', $contentType='text/html', $stringdata=false){

		// Set the path to the view file ( real path ).
		if (realpath($this->app->APP_PATH . '/view/' . $viewFile . '.php'))$viewFilePath = $this->app->APP_PATH . '/view/' . $viewFile . '.php';
		else $viewFilePath = $viewFile;
		$vars = $this->viewVars;
		$vars = array_merge($vars, $extraVars);
		
		// Extract all the assigned vars. So basically the array will have
		// key => value
		// keyname will be the $variablename to the view file.
		extract($vars);

		// And give access to this class also from the view.
		/**
		 * 
		 * @var reks\View
		 */
		$view = $this;
		// Shortcut to $this->lang.
		
		$lang = $this->lang;
		// Shortcut to $this->url.
		
		$url = $this->url;
		
		if (!$this->headersSent){
			header('Content-Type:'.$contentType.'; charset=' . $charset);
			$this->headersSent = true;
		}

		ob_start();
		
		// And .. finally - lets include the view file with simple include.
		if ($stringdata) echo file_get_contents($viewFilePath);
		else include $viewFilePath;
		
		$content = ob_get_clean();
		
		// if we should cache.
		if (isset($this->cacheQueue[$viewFile])){
			file_put_contents($this->cacheQueue[$viewFile], $content);
		}
		
		echo $content;
	}
	
	/**
	 * Fetches content of a view file.
	 * @param string $viewFile
	 * @param array $extraVars
	 */
	public function fetch($viewFile, $extraVars=array()){
		// Set the path to the view file ( real path ).
		if (realpath($this->app->APP_PATH . '/view/' . $viewFile . '.php'))$viewFilePath = $this->app->APP_PATH . '/view/' . $viewFile . '.php';
		else $viewFilePath = $viewFile;
		$vars = $this->viewVars;
		$vars = array_merge($vars, $extraVars);
		
		// Extract all the assigned vars. So basically the array will have
		// key => value
		// keyname will be the $variablename to the view file.
		extract($vars);
		
		// And give access to this class also from the view.
		/**
		 *
		 * @var reks\View
		 */
		$view = $this;
		// Shortcut to $this->lang.
		
		$lang = $this->lang;
		// Shortcut to $this->url.
		
		$url = $this->url;
		
		ob_start();
		
		include $viewFilePath;
		
		$content = ob_get_clean();
		
		return $content;
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
	 * @param string $charset Charset. default is utf-8.
 	 * @param string $contentType Content type. Default is text/html.
	 */
	public function cache($viewFile, $cacheForSecs = 7200, $seed = null, $charset = 'UTF-8', $contentType='text/html'){
		$token = sha1($viewFile . $seed).'.php';
		$cachePath = $this->app->APP_PATH . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'views';
		$fp = $cachePath . DIRECTORY_SEPARATOR . $token;
		if (file_exists($fp) && filemtime($fp)+$cacheForSecs > time()){
			$this->render($fp, array(), $charset, $contentType, true);
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
	
}