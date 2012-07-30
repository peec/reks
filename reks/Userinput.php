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
 * Wrapper for all user input possible in php.
 *
 * Usage ( sessions):
 * <code>
 * $this->ui->session['qwd'] =  21312;
 * echo $this->ui->session['qwd'];
 * </code>
 * Usage (post):
 * <code>
 *  if (isset($this->ui->post['sad'])){
 *  	// do somewith with post['sad']
 *  }
 * </code>
 * @author peec
 *
 */
class Userinput{
	/**
	 * Input data
	 * @var reks\Input
	 */
	public $post;
	/**
	 * Input data
	 * @var reks\Input
	 */
	public $get;
	/**
	 * Input data
	 * @var reks\Input
	 */
	public $request;
	/**
	 * Input data
	 * @var reks\Input
	 */
	public $server;
	/**
	 * Input data
	 * @var reks\Input
	 */
	public $cookie;
	/**
	 * Input data
	 * @var reks\Input
	 */
	public $env;
	/**
	 * Input data
	 * @var reks\Input
	 */
	public $session;

	/**
	 * Constructs the input handler.
	 */
	public function __construct(){
		$this->post = new Input(INPUT_POST);
		$this->session = new Input(INPUT_SESSION);
		$this->get = new Input(INPUT_GET);
		$this->env = new Input(INPUT_ENV);
		$this->request = new Input(INPUT_REQUEST);
		$this->cookie = new Input(INPUT_COOKIE);
		$this->server = new Input(INPUT_SERVER);
	}
}