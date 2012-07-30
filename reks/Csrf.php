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
 * Cross-site request forgery protection script.
 * 
 * To check for token validation use assertValidate.
 * 
 * 
 * @author peec
 */
class Csrf{
	/**
	 * User input
	 * @var reks\Userinput
	 */
	private $ui;
	
	/**
	 * Type of storage ( cookie or session )
	 * @var string
	 */
	private $storage;
	
	
	/**
	 * Flag for storing in session
	 * @var string
	 */
	const STORE_IN_SESSION = 'session';
	/**
	 * Flag for storing in cookie
	 * @var string
	 */
	const STORE_IN_COOKIE = 'cookie';
	
	/**
	 * Constructs a new Csrf protection object.
	 * @param \reks\Userinput $ui User input class.
	 * @param string $storage See class constants STORE_IN_COOKIE OR STORE_IN_SESSION. 
	 */
	public function __construct(\reks\Userinput $ui, $storage = self::STORE_IN_SESSION){
		$this->ui = $ui;
		$this->storage = $storage;
		// If not generated token, generate. 
		$this->generate('csrf_tok_uniq');
		$this->generate('safe_link_csrf');
	}
	
	/**
	 * Refreshes the token, you must be sure that no 
	 * check with assertValidate is done in this session.
	 */
	public function refreshToken($tokenName='csrf_tok_uniq'){
		unset($this->ui->{$this->storage}->{$tokenName});
		
		session_regenerate_id();
		$this->generate();
	}
	
	
	
	/**
	 * Generates a new token if its not already set.
	 */
	protected function generate($tokenName='csrf_tok_uniq'){
		if (!$this->ui->{$this->storage}->{$tokenName})$this->ui->{$this->storage}->{$tokenName} = sha1(uniqid(rand(), true));
	}
	
	/**
	 * Returns a closure to get the value from.
	 * Example 
	 * <code>
	 * $tok = $this->token();
	 * echo $tok();
	 * </code>
	 */
	public function token($tokenName='csrf_tok_uniq'){
		$storage = $this->ui->{$this->storage};
		return function() use (&$storage, $tokenName){
			return $storage->$tokenName;
		};
	}
	
	/**
	 * Validates a token.
	 * @param string $token Token name.
	 * @throws CsrfException If token is not validated this will throw exception.
	 */
	public function assertValidate($token, $tokenName='csrf_tok_uniq'){
		// CSRF Check.
		$tokSrv = $this->token($tokenName);
		
		
		if (!$tokSrv() || $token != $tokSrv()){
			throw new CsrfException("CSRF token invalid.");
		}
		return true;
	}
	/**
	 * Checks a URL if the token is correct.
	 * @throws CsrfException If token is not validated tis will throw exception.
	 */
	public function assertSafelink($tokenName='safe_link_csrf'){
		$token = $this->ui->get->{View::CSRF_TOKEN_NAME};
		$tokSrv = $this->token($tokenName);
		
		if (!$tokSrv() || $token != $tokSrv()){
			throw new CsrfException("CSRF token invalid.");
		}
		return true;
	}
	
	
	
	
}
/**
 * CsrfException is casted once validation of csrf token was not OK.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 *
 */
class CsrfException extends \Exception{
	
}