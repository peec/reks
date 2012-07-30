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
 * This class can be used to deal with domain names and getting external
 * addresses.
 * @author REKS group at Telemark University College
 * @version 1.0
 *
 */
class Url{

	/**
	 * Router instance.
	 * @var reks\Router
	 */
	private $router;
	
	public $removeScriptpath;
	
	
	/**
	 * Contains the global csrf token.
	 * @var \Closure
	 */
	public $csrfToken;

	
	/**
	 * GET csrf token name.
	 * @var string
	 */
	const CSRF_TOKEN_NAME = 'csrf_token_';
	
	public function __construct(Router $router, $removeScriptpath, $csrfToken){
		$this->router = $router;
		$this->removeScriptpath = $removeScriptpath;
		$this->csrfToken = $csrfToken;
	}
	
	
	/**
	 * Fetches the whole url in the addressbar.
	 * Example: http://example.com/test?v=2&amp;s=2
	 */
	public function fetchUri() {
		$pageURL = $this->fetchDomain();
		$pageURL .= $_SERVER['REQUEST_URI'];
		return htmlentities($pageURL, ENT_QUOTES);
	}
	/**
	 * Fetches the domain including protocol.
	 * Example: http://example.com
	 * or
	 * https://example.com
	 *
	 */
	public function fetchDomain(){
		$pageURL = 'http';
		if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
			$pageURL .= 's';
		}
		$pageURL .= '://';
		if ($_SERVER['SERVER_PORT'] != '80') {
			$pageURL .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'];
		} else {
			$pageURL .= $_SERVER['SERVER_NAME'];
		}
		return htmlentities($pageURL, ENT_QUOTES);
	}

	/**
	 * Urifies a url segment.
	 * Thanks to http://cubiq.org/the-perfect-php-clean-url-generator.
	 * Somewhat modified by the REKS team.
	 * @param string $str The string to urlify.
	 * @param string $delimiter Delimiter , default is "-" .
	 */
	public function urlify($str, $delimiter='-') {
		if (is_array($str)){
			foreach($str as $k=> $s){
				$str[$k] = $this->urlify($s, $delimiter);
			}
			return implode('/', $str);
		}else{
			setlocale(LC_ALL, 'en_US.UTF8');
			$clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
			$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
			$clean = strtolower(trim($clean, '-'));
			$clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);
			return $clean;
		}
	}
	
	/**
	 * Used for generating urls to stuff like images, css files, javascript files, and etc...
	 * Should be used in the view.
	 *
	 * @param string $resource path from the where the index.php resides.
	 */
	public function asset($resource){
		$d = dirname($_SERVER['SCRIPT_NAME']);
		return  ($d != '/' ? $d . '/' : $d) . $resource;
	}
	/**
	 * Used for generating urls to internal websites
	 *
	 * @param string $resource path from the where the index.php resides.
	 * @param array $parameters Additional parameters to url.
	 */
	public function linkTo($resource=null, array $parameters=array()){
		$q = count($parameters) > 0 ? http_build_query($parameters) : null;
	
		$o = $resource;
		if (!$this->removeScriptpath){
			$o = ('index.php' . ($resource ? '/'.$resource : ''));
		}
		return $this->asset($o . ($q ? '?'.$q : null));
	}
	
	/**
	 * Includes CSRF token in link.
	 * It needs to be checked in the controller.
	 * @param string $resource
	 * @param array $parameters Additional parameters to url.
	 */
	public function linkToSafe($resource=null, array $parameters=array()){
		$tok = $this->csrfToken;
		$parameters[self::CSRF_TOKEN_NAME] = $tok();
		return $this->linkTo($resource, $parameters);
	}
	
	/**
	 * Reverses a route to url.
	 * @param string $path The controller / method ( Format: Controller.method )
	 * @param array $args If route has arguments. Arguments with correct key assignment eg. @title, @id should be array( 'title' => 'hello-world', 'id' => 5 )
	 * @param string $requestType By default the reverse algorithm will look on all cases, you can select get, post here if you want a more specific route.
	 * @param array $params Additional $_GET parameters with key => val.
	 * @param boolean $skipUrlfiy If you want to automatically urlify args, set this to true.
	 */
	public function reverse($path, array $args=array(), $requestType = null, array $params = array(), $skipUrlfiy=true){
		if (!$skipUrlfiy){
			foreach($args as $k => $v){
				$args[$k] = $this->urlify($v);
			}
		}
		return $this->linkTo(substr($this->router->reverse($path, $args, $requestType), 1), $params);
	}
	/**
	 * Safe-Reverses a route to url.
	 * @param string $path The controller / method ( Format: Controller.method )
	 * @param array $args If route has arguments. Arguments with correct key assignment eg. @title, @id should be array( 'title' => 'hello-world', 'id' => 5 )
	 * @param string $requestType By default the reverse algorithm will look on all cases, you can select get, post here if you want a more specific route.
	 * @param array $params Additional $_GET parameters with key => val.
	 * @param boolean $skipUrlfiy If you want to automatically urlify args, set this to true.
	 */
	public function reverseSafe($path, array $args=array(), $requestType = null, array $params = array(), $skipUrlfiy=true){
		if (!$skipUrlfiy){
			foreach($args as $k => $v){
				$args[$k] = $this->urlify($v);
			}
		}
		return $this->linkToSafe(substr($this->router->reverse($path, $args, $requestType), 1), $params);
	}
	
	
	/**
	 * Returns a modules url instance.
	 * @param string $name Module name
	 * @return reks\Url
	 */
	public function mod($name){
		return $this->router->app->module->get($name)->getTargetRouter()->getResource(App::RES_URL);
	}
	
	/**
	 * Returns the super application ( parent application ) Url
	 * @return reks\Url
	 */
	public function super(){
		return $this->router->app->superRouter->getResource(App::RES_URL);
	}
	
	
}
