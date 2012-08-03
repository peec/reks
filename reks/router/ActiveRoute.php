<?php
namespace reks\router;

/**
 * Class to return active router pair combination.
 * @author loltroll
 *
 */
class ActiveRoute{

	private $controller;
	private $method;
	private $args;
	private $f_controller;
	private $f_method;
	private $f_args;
	private $varNames;
	/**
	 *
	 * @var reks\RouteRule
	 */
	private $rule;

	/**
	 *
	 * @var reks\Url
	 */
	private $url;

	/**
	 *
	 * @param reks\Url $url
	 * @param reks\RouterRule $rule
	 */
	public function __construct($url, RouteRule $rule, array $backend){
		$this->url = $url;
		$this->rule = $rule;
		list($controller, $method, $args) = $this->rule->parseTo();
		list($regexp, $varNames) = $this->rule->parseFrom();
		$this->controller = $controller;
		$this->method = $method;
		$this->args = $args;
		$this->varNames = $varNames;
		
		list($f_c, $f_m, $f_a) = $backend;
		$this->f_controller = $f_c;
		$this->f_method = $f_m;
		$this->f_args = $f_a;
		
		
	}
	/**
	 * Fetches array of arguments
	 */
	public function getArgs(){
		$args = array();
		
		if ($this->args){
			foreach($this->args as $k => $varName){
				$args[substr($varName,1)] = $this->f_args[$k];
			}
		}
		return $args;
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
		return $this->f_controller;
	}
	/**
	 * Fetches the method name of the active real controller/method.
	 */
	public function getMethod(){
		return $this->f_method;
	}

	/**
	 * Rerverses this route.
	 */
	public function reverse(){
		return $this->url->reverse($this->getRawController().'.'.$this->getMethod(), $this->getArgs(), $this->rule->getType());
	}


}