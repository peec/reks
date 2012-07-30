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
 * All models should extend this super class.
 *
 * @author REKS group at Telemark University College
 * @version 1.0
 *
 */
abstract class Model{

	/**
	 * Global database connections, meaning we dont need 1 db connection per model but these share the same connection.
	 *
	 * @var array Array of database connections ( reks\DB objects )
	 */
	static protected $connectedDbs = array();

	/**
	 * Tries to initialize a database connection to the global scope if not initialized already.
	 * @param string $configIndex Refers to $config['db'][X] where X is the configIndex.
	 * @param array $dbconfig The database configuration array.
	 */
	static public function initDB($configIndex, array $dbconfig){
		if (!isset(self::$connectedDbs[$configIndex])){
			self::$connectedDbs[$configIndex] = new DB($dbconfig['dsn'], $dbconfig['username'], $dbconfig['password'], $dbconfig['driver_options']);
		}
	}


	/**
	 * Database instance.
	 * Rememeber to run $this->openDB() first.
	 * @var reks\DB
	 */
	public $db;

	/**
	 * Array of configuration from config.php
	 * @var array
	 */
	public $config;

	/**
	 * Language instance.
	 * @var reks\Lang
	 */
	public $lang;
	
	/**
	 * Model loader.
	 * @var reks\ModelWrapper
	 */
	public $model;

	/**
	 * Logger instance. Used to log application data.
	 * @var reks\Log
	 */
	public $log;
	
	
	/**
	 * 
	 * @var reks\App
	 */
	public $app;
	
	/**
	 * Constructor for each model.
	 * @param reks\ModelWrapper $modelwrapper Model wrapper object.
	 */
	public function __construct(ModelWrapper &$modelwrapper){
		$this->config = &$modelwrapper->config;
		$this->lang = &$modelwrapper->lang;
		$this->model = &$modelwrapper;
		$this->log = &$modelwrapper->log;
		$this->app = &$modelwrapper->app;
	}



	/**
	 * Opens a new database connection.
	 * If the connection is already opened in another model or this model - it will just reference to that connection.
	 *
	 * @param string $configIndex Refers to $config['db'][X] where X is the configIndex.
	 * @return reks\DB
	 */
	public function openDB($configIndex = null){
		// Koble til databasen og assign, hvis den ikke er oppe allerede.
		if (!$this->db){
			$this->log->debug("Opening database connection ...");
			
			if (!isset($this->config['db']))throw new \Exception("Could not load database connection because could not found 'db' index configuration settings in config.", 0);
			
			if ($configIndex !== null)$dbconfig = $this->config['db'][$configIndex];
			else $dbconfig = $this->config['db'];

			Model::initDB($configIndex, $dbconfig);
			$this->db = Model::$connectedDbs[$configIndex];
		}
		return $this->db;
	}

	/**
	 * Closes and removes the database object from all indexes from the global scope.
	 *
	 * @param string $configIndex Refers to $config['db'][X] where X is the configIndex.
	 */
	public function closeDB($configIndex = null){
		if (isset(Model::$connectedDbs[$configIndex])){
			$this->db = null;
			unset(Model::$connectedDbs[$configIndex]);
		}
	}

	

}