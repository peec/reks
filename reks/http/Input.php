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
/**
 * All userinput gets to use this class as wrapper around the global variable.
 *
 * @author REKS group at Telemark University College
 *
 */
class Input implements \ArrayAccess{
	
	protected $data;
	protected $saveObjectHandler;
	
	public function __construct(array $data){
		$this->data = $data;	
	}

	public function setSaveHandler($saveHandler){
		$this->saveObjectHandler = $saveHandler;
	}
	/**
	 * Magic method
	 * @param string $name Name of the input
	 */
	public function __unset($name){
		$this->offsetUnset($name);
	}
	/**
	 * Magic method
	 * @param string $name Name of the input
	 */
	public function __isset($name){
		return $this->offsetExists($name);
	}
	/**
	 * Magic method.
	 * Allow $this->s = '2';
	 * @param string $name Name of the input
	 * @param mixed $value Value of the input
	 */
	public function __set($name, $value){
		return $this->offsetSet($name, $value);
	}

	/**
	 * Magic method.
	 * Allows to return $this->var
	 * Returns null if it is not set. ( isset)
	 * @param string $name Name of input
	 */
	public function __get($name){
		return $this->get($name);
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $val) {
		$this->data[$offset] = $val;
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		return isset($this->data[$offset]);
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		unset($this->data[$offset]);
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		return $this->get($offset);
	}

	/**
	 * Gets the value of a KEY.
	 * If no value NULL is returned.
	 * @param string $offset
	 */
	public function get($offset){
		return isset($this->data[$offset]) ? $this->data[$offset] : null;
	}
	
	public function __destruct(){
		$m = $this->saveObjectHandler;
		if ($m)$m($this->data);
	}
}