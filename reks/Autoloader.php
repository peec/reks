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
 * Simple autoloader.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 */
class Autoloader{
	
	/**
	 * 
	 * @var reks\App
	 */
	private $app;
	
	/**
	 * Factory method to load autoloader.
	 * @param reks\App $app
	 * 
	 */
	static public function create(App $app){
		return new Autoloader($app);
	}
	
	/**
	 * Registers the autoloader method.
	 * @param reks\App $app
	 */
	public function __construct(App $app){
		$this->app = $app;
		spl_autoload_register(array($this, 'autoloader'));
	}
	
	/**
	 * This is the simple autoloader for the REKS framework.
	 * This is possible because we use namespaces to refer to folder names / class names.
	 *
	 * PHP calls this method, this should never be used!!
	 *
	 * @param string $className Full class name including namespace.
	 */
	private function autoloader($className){
		if (substr($className, 0, 1) == '\\')$className = substr($className, 1);
		$fil = 
		 (substr($className, 0, 5) == 'reks\\' ? $this->app->BASE_REKS : $this->app->APP_PATH)
		 . DIRECTORY_SEPARATOR
		 . str_replace('\\',DIRECTORY_SEPARATOR, $className)
		 .'.php';
		
		if (file_exists($fil))include $fil;
	}
	
}