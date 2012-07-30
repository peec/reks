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
 * @package reks\form
 * @author REKS group at Telemark University College
 */
namespace reks\form;
/**
 * All elements in the form will extend this abstract.
 * @author peec
 *
 */
abstract class Element{
	protected $attr = array();
	
	/**
	 * Sets the id="" attribute of the element.
	 * @param string $id The id of the form element.
	 * @return reks\form\Element 
	 */
	public function id($id){
		$this->attr('id', $id);	
		return $this;
	}
	/**
	 * Sets attribute by name / value pairs.
	 * @param string $name Name of the attribute
	 * @param mixed $val Value of the attribute
	 * @return reks\form\Element
	 */
	public function attr($name, $val){
		$this->attr[$name] = $val;
		return $this;
	}
	/**
	 * Removes a attribute
	 * @param string $name Name of the attribute
	 * @return reks\form\Element
	 */
	public function removeAttr($name){
		unset($this->attr[$name]);
		return $this;
	}
	/**
	 * Gets a attribute's value.
	 * @param string $name Name of the attribute.
	 */
	public function getAttr($name){
		if (isset($this->attr[$name]))return $this->attr[$name];
		return null;
	}
	/**
	 * Returns a xml valid string of attirbutes.
	 */
	public function __toString(){
		$str = '';
		foreach($this->attr as $k => $v){
			$str .= ' '.$k . '="'.(is_callable($v) && !is_string($v) ? $v() : $v).'"';
		}
		return $str;
	}
}