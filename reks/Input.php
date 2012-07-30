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
 * All userinput gets to use this class as wrapper around the global variable.
 *
 * @author REKS group at Telemark University College
 *
 */
class Input implements \ArrayAccess{
	/**
	 * Type of input see INPUT_* php constants.
	 * @var int
	 */
	private $type;
	/**
	 * Helper for \ArrayAccess interface.
	 * @var int current position
	 */
	private $pos;

	/**
	 * Constructs a new input object.
	 * @param int $type See PHP's INPUT_* variables.
	 */
	public function __construct($type){
		$this->type = $type;
		$this->pos = 0;
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
		return $this->offsetExists($name) ? $this->offsetGet($name) : null;
	}

	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $val) {
		switch($this->type){
			case INPUT_COOKIE:
				$_COOKIE[$offset] = $val;
				break;
			case INPUT_ENV:
				$_ENV[$offset]  = $val;
				break;
			case INPUT_GET:
				$_GET[$offset] = $val;
				break;
			case INPUT_POST:
				$_POST[$offset] = $val;
				break;
			case INPUT_REQUEST:
				$_REQUEST[$offset] = $val;
				break;
			case INPUT_SERVER:
				$_SERVER[$offset] = $val;
				break;
			case INPUT_SESSION:
				$_SESSION[$offset] = $val;
				break;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetExists()
	 */
	public function offsetExists($offset) {
		switch($this->type){
			case INPUT_COOKIE:
				return isset($_COOKIE[$offset]);
				break;
			case INPUT_ENV:
				return isset($_ENV[$offset]);
				break;
			case INPUT_GET:
				return isset($_GET[$offset]);
				break;
			case INPUT_POST:
				return isset($_POST[$offset]);
				break;
			case INPUT_REQUEST:
				return isset($_REQUEST[$offset]);
				break;
			case INPUT_SERVER:
				return isset($_SERVER[$offset]);
				break;
			case INPUT_SESSION:
				return isset($_SESSION[$offset]);
				break;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset) {
		switch($this->type){
			case INPUT_COOKIE:
				unset($_COOKIE[$offset]);
				break;
			case INPUT_ENV:
				unset($_ENV[$offset]);
				break;
			case INPUT_GET:
				unset($_GET[$offset]);
				break;
			case INPUT_POST:
				unset($_POST[$offset]);
				break;
			case INPUT_REQUEST:
				unset($_REQUEST[$offset]);
				break;
			case INPUT_SERVER:
				unset($_SERVER[$offset]);
				break;
			case INPUT_SESSION:
				unset($_SESSION[$offset]);
				break;
		}
	}
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset) {
		switch($this->type){
			case INPUT_COOKIE:
				return $_COOKIE[$offset];
				break;
			case INPUT_ENV:
				return $_ENV[$offset];
				break;
			case INPUT_GET:
				return $_GET[$offset];
				break;
			case INPUT_POST:
				return $_POST[$offset];
				break;
			case INPUT_REQUEST:
				return $_REQUEST[$offset];
				break;
			case INPUT_SERVER:
				return $_SERVER[$offset];
				break;
			case INPUT_SESSION:
				return $_SESSION[$offset];
				break;
		}
	}


}