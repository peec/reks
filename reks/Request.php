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
 * This class is used for checking type of request and get data based on the request.
 *
 * Example to check if we are dealing with a post request:
 * <code>
 * if ($this->request->post){
 *     // Do something.
 * }
 * </code>
 *
 * @author REKS group at Telemark University College
 *
 */
class Request{
	/**
	 * Magic get method. Checks method ie. POST against the getter var.
	 * @param string $name Input var.
	 */
	public function __get($name){
		if ($this->getMethod() == strtoupper($name))return true;
		else return false;
	}

	/**
	 * Gets all variables from the request type.
	 * If this is a GET it will return $_GET, if this is a post it will return $_POST
	 * @param string $method The type of input method.
	 */
	public function vars(){
		switch($this->getMethod()){
			case 'POST': return $_POST; break;
			case 'GET': return $_GET; break;
			case 'PUT':
				parse_str(file_get_contents('php://input'), $put_vars);
				return $put_vars;
				break;
		}
		return array();
	}


	/**
	 * Gets the request method ( to upper case allways )
	 */
	public function getMethod(){
		return strtoupper($_SERVER['REQUEST_METHOD']);
	}

}
