<?php
namespace reks\repo;

abstract class ARepo{
	
	/**
	 * Array of configuration from config.php
	 * @var array
	 */
	public $config;
	
	/**
	 * Language instance.
	 * @var reks\i18n\Lang
	 */
	public $lang;
	
	/**
	 * Model loader.
	 * @var reks\repo\Repository
	 */
	public $model;
	
	/**
	 * Logger instance. Used to log application data.
	 * @var reks\core\Log
	 */
	public $log;

	/**
	 *
	 * @var reks\core\App
	 */
	public $app;

	
	public function __construct(){}
	
	/**
	 * Constructor for each model.
	 * @param reks\repo\Repository $repo repository object
	 */
	public function setup(Repository $repo){
		$this->config = &$repo->config;
		$this->lang = &$repo->lang;
		$this->model = &$repo;
		$this->log = &$repo->log;
		$this->app = &$repo->app;
	}
	
	
}