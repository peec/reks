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
 * Language class, allows multilanguage strings.
 * 
 * Efficient algorithms is very important for this class.
 * 
 * @author REKS group at Telemark University College
 */
class Lang{
	
	/**
	 * Stores all the language keys -> string.
	 * @var string[] The language array.
	 */
	private $store = array();
	
	/**
	 * This variable contains the full path of the language file currently loaded.
	 * @var string
	 */
	private $langFile;
	
	/**
	 * This initializes the language object but does not initialize the array.
	 * 
	 * @param string $langFile Full path to the language file.
	 */
	public function __construct($langFile){
		$this->langFile = $langFile;
	}
	
	
	
	/**
	 * Loads the language file.
	 * @throws InternalServerError
	 */
	private function load(){
		if (!@include($this->langFile)){
			throw new InternalServerError("\reks\Lang: Could not load language file ( {$this->langFile} ). It does not exist.");
		}
		
		$this->store = $lang;
	}
	
	/**
	 * Magic method override.
	 * This is called by PHP.
	 * @param string $name Name of attribute.
	 * @throws InternalServerError
	 */
	public function __get($name){
		if (empty($this->store)){
			$this->load();
		}
		
		if (array_key_exists($name, $this->store))return new LangVar($this->store[$name]);
		else throw new InternalServerError("There are some errors in the language file {$this->langFile}. We could not find \"{$name}\" as key in the \$lang array.");
	}
	
}


/**
 * All language variables will automatically get into this class when you get them.
 * @author REKS group at Telemark University College
 *
 */
class LangVar{
	/**
	 * The value of the language var.
	 * @var string
	 */
	private $value = '';
	
	/**
	 * Constructor.
	 * @param string $value
	 */
	public function __construct($value){
		$this->value = $value;
	}
	
	/**
	 * Arguments to pass to sprintf in string.
	 * $args can be both array and simple string.
	 * @param mixed $args Argument(s) to pass, array or any other single type line string, int, etc.
	 */
	public function args($args){
		$args = (array) $args;
		foreach($args as $k => $v){
			$args[$k] = htmlentities($v, ENT_QUOTES);
		}
		$this->value = vsprintf($this->value, $args);
		return $this;
	}
	
	/**
	 * Returns the value of the lang variable in RAW format.
	 * Useful if it's an array and not a string.
	 */
	public function val(){
		return $this->value;
	}
	/**
	 * Php magic method.
	 * Returns the string of value if this object is called in a string sequence.
	 */
	public function __toString(){
		return $this->value;
	}
}