<?php
namespace reks\core;

/**
 * Configuration object.
 * @author peec
 *
 */
class Config implements \ArrayAccess, \Iterator{
	
	private $config = array();
	
	public function __construct($cnf){
		$this->config = $cnf;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $val) {
		$this->config[$offset] = $val;
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return isset($this->config[$offset]);
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		unset($this->config[$offset]);
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		return $this->config[$offset];
	}
	
	public function rewind() {
		return reset($this->config);
	}
	
	public function current() {
		return current($this->config);
	}
	
	public function key() {
		return key($this->config);
	}
	
	public function next() {
		return next($this->config);
	}
	
	public function valid() {
		return key($this->config) !== null;
	}
	
	
	
	public function toArray(){
		return $this->config;
	}
	
}