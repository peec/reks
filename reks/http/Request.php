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
namespace reks\http;

use \reks\core\App;
/**
 * A HTTP Request. This class represents it, but creates a wrapper around the strings and arrays.
 * 
 * @author REKS group at Telemark University College
 *
 */
class Request{
	/**
	 * @var reks\http\Input
	 */
	public $post;
	/**
	 * @var reks\http\Input
	 */
	public $get;
	/**
	 * @var reks\http\Input
	 */
	public $cookie;
	/**
	 * @var reks\http\Input
	 */
	public $server;
	/**
	 * @var reks\http\Input
	 */
	public $session;
	/**
	 * @var reks\http\InputFiles
	 */
	public $file;
	/**
	 * @var reks\http\Input
	 */
	public $request;
	/**
	 * @var reks\http\Input
	 */
	public $env;
	
	public function __construct(App $app, $post, $get, $cookie, $server, $session, $file, $request, $env){
		$this->post = new Input($post);
		$this->get = new Input($get);
		$this->cookie = new Input($cookie);
		$this->server = new Input($server);
		$this->session = new Input($session);
		$this->file = new InputFiles($app, $file);
		$this->request = new Input($request);
		$this->env = new Input($env);
	}

	
	static public function bindFromGlobals(App $app){
		$req = new Request($app, 
				!isset($_POST) || !is_array($_POST) ? array() : $_POST, 
				!isset($_GET) || !is_array($_GET) ? array() : $_GET, 
				!isset($_COOKIE) || !is_array($_COOKIE) ? array() : $_COOKIE, 
				!isset($_SERVER) || !is_array($_SERVER) ? array() : $_SERVER, 
				!isset($_SESSION) || !is_array($_SESSION) ? array() : $_SESSION, 
				!isset($_FILES) || !is_array($_FILES) ? array() : $_FILES,
				!isset($_REQUEST) || !is_array($_REQUEST) ? array() : $_REQUEST,
				!isset($_ENV) || !is_array($_ENV) ? array() : $_ENV
		);
		$req->session->setSaveHandler(function ($data){
			$_SESSION = $data;
		});
		return $req;
	}

	/**
	 * The HTTP method
	 */
	public function method(){
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}	
	
	/**
	 * The HTTP headers.
	 */
	public function headers(){
		$headers = getallheaders();
		
		$h = array();
		foreach($headers as $k => $v){
			$h[strtolower($k)] = $v;
		}
		return $h;
	}
	
	/**
	 * Array of accept languages.
	 */
	public function acceptLanguages(){
		$parcels = explode(',', $this->server['HTTP_ACCEPT_LANGUAGE']);
		$items = array();
		
		foreach ($parcels as $parcel) {
			$match = preg_match('/([-a-zA-Z]+)\s*;\s*q=([0-9\.]+)/', $parcel, $matches);
			if ($match === 0)
				$items[] = substr($parcel, 0, 2);
			else
				$items[] = substr($matches[1], 0, 2);
		}		
		return $items;
	}
	
	/**
	 * The client IP.
	 */
	public function ip(){
		foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
			if (array_key_exists($key, $this->server) === true){
				foreach (explode(',', $this->server[$key]) as $ip){
					$ip = trim($ip); // just to be safe
					if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
						return $ip;
					}
				}
			}
		}
	}
	
	
	
}