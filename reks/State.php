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
 * States can be saved. More formally the  current URL.
 *
 * One can save a key to the URL and trigger them later on.
 * Example of use can be save state when trying to access a user only feature
 * Trigger the state after successful logins.
 *
 * <code>
 * // In restricted area.
 * $this->state->save('login');
 *
 * // In Login method of controller.
 * $this->state->trigger('login');
 * </code>
 * @author REKS group at Telemark University College
 *
 */
class State{
	/**
	 * Url
	 * @var reks\Url
	 */
	private $url;

	/**
	 * Constructs the state object.
	 * @param reks\Url $url Url
	 */
	public function __construct($url){
		$this->url = $url;
	}


	/**
	 * Saves the current url to a key, it can be triggered with the trigger() method.
	 * @param string $key A string key for the state name.
	 */
	public function save($key){
		$_SESSION['reks_variables__']['states'][$key] = $this->url->fetchUri();
	}

	/**
	 * Cheks if there are any state of the key, if any it will redirect user to the url and delete the state.
	 * @param string $key The id of the state.
	 */
	public function trigger($key){
		if (isset($_SESSION['reks_variables__']['states'][$key])){
			$url = $_SESSION['reks_variables__']['states'][$key];
			unset($_SESSION['reks_variables__']['states'][$key]);
			header('Location: '.$url);
			die();
		}
	}



}