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
 * Created in order to get a cleaner model loading feature.
 * You can load models with:
 * <code>
 * $this->model->load('User')
 * </code>
 * Then you can use methods on the model like:
 * <code>
 * $this->model->User->login('username','password');
 * </code>
 * @author REKS group at Telemark University College
 */
class ModelWrapper{

	/**
	 * Keeps all models in this array.
	 * @var array
	 */
	private $models = array();

	/**
	 * Global configuration.
	 * @var array
	 */
	public $config;

	/**
	 * Logger instance. Used to log application data.
	 * @var reks\Log
	 */
	public $log;


	public $lang;
	
	
	/**
	 *
	 * @var reks\App
	 */
	public $app;
	
	
	/**
	 * Constructs the model loader.
	 * @param array $config The global config array.
	 * @param reks\Lang $lang The language instance.
	 */
	public function __construct($config, $lang, $log, $app){
		$this->config = &$config;
		$this->lang = $lang;
		$this->log = $log;
		$this->app = $app;
	}

	/**
	 * Loads a new model and passes global configuration to its constructor.
	 * @param string $model Model class name.
	 */
	public function load($model){

		$this->log->debug("Loading model $model ...");

		$p = '\model\\' . $model;
		if (!isset($this->models[$model])){
			if (class_exists($p)){
				$this->models[$model] = new $p($this);
			}else{
				throw new \Exception("Could not find the model {$p}. It seems to not exist.");
			}
		}

	}

	public function newModel($model){
		$p = '\model\\' . $model;
		return new $p($this);
	}

	/**
	 * Magic method.
	 * Used for getting models.
	 * @param string $model Model class name.
	 */
	public function __get($model){
		if (isset($this->models[$model]))return $this->models[$model];
		else{
			$this->load($model);
			return $this->models[$model];
		}

	}

	/**
	 * Returns a modules model instance.
	 * @param string $name Module name
	 * @return reks\ModelWrapper
	 */
	public function mod($name){
		return $this->router->app->module->get($name)->getTargetRouter()->getResource(App::RES_MODELWRAPPER);
	}
	
	/**
	 * Returns the super application ( parent application ) ModelWrapper
	 * @return reks\ModelWrapper
	 */
	public function super(){
		return $this->router->app->superRouter->getResource(App::RES_MODELWRAPPER);
	}
	
	
	/**
	 * Returns a raw model object to direct usage.
	 * @return reks\RawModel
	 */
	public function raw(){
		return new RawModel($this);
	}

}