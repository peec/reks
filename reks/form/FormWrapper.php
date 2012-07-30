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
 * The form wrapper is a wrapper between controller / view to make dynamic features available.
 * features such as CSRF protection can get automated in all forms.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 */
class FormWrapper{
	/**
	 * Array of forms ( VIEW )
	 * @var array
	 */
	private $forms = array();
	/**
	 * Array of listeners to forms ( CONTROLLER )
	 * @var array
	 */
	private $listeners = array();
	
	/**
	 * Gets a form by id.
	 * @param string $formName The id of the form.
	 */
	public function __get($formName){
		if (isset($this->forms[$formName]))return $this->forms[$formName];
	}
	
	/**
	 * Creates a new form. Called from the view.
	 * 
	 * @param string $formId Unique id.
	 * @param string $targetUrl CAN BE NULL, if you add action listener in controller this is not needed.
	 * @param string $method CAN BE NULL, if you add action listener in controller this is not needed.
	 * @param string $csrfToken CAN BE NULL, if you add action listener in controller this is not needed.
	 * @param reks\Input $input CAN BE NULL, if you add action listener in controller this is not needed.
	 * 
	 */
	public function create($formId, $targetUrl = null, $method = null, $csrfToken = null, $input = null){
		if (isset($this->listeners[$formId])){
			list($targetUrl, $method, $csrfToken, $input) = $this->listeners[$formId];
		}
		$f = new Form($formId,  $targetUrl, $method, $input);
		// Add Hidden field with formId as value to 1.
		$f->addHidden($formId, '1');
		// Add csrf token
		if ($csrfToken){
			$f->addHidden('csrf_tok_uniq', $csrfToken);
		}
		
		
		return $f;
	}
	
	
	/**
	 * Should be called from the controller. See Controller.addFormListener.
	 * This method is used internally to assign values to the form.
	 * @param string $formId Unique form identity
	 * @param string $targetUrl The post / get url of the form
	 * @param string $method Type of the request. ( POST / GET )
	 */
	public function addListener($formId, $targetUrl, $method, $csrfToken, $input){
		$this->listeners[$formId] = array($targetUrl, $method, $csrfToken, $input);
		return $this;
	}
	
}