<?php
namespace reks\repo;

class Repository{
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
	 * @var reks\repo\Repository
	 */
	public $repo;
	
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
	 * Method to get repository.
	 * Note _ ( UNDERSCORE ) will be replaced by NAMESPACE separator.
	 * This way we can get namespaced stuff like:
	 * 
	 * namespace \model\auth;
	 * class User { function create() {}}
	 * 
	 * = 
	 * 
	 * $this->repo->auth_User->create()
	 * 
	 * @param string $name REPO CLASS
	 * @return object model\$name The model repository.
	 */
	public function __get($name){
		// Replace underscore to NS_SEPARATOR
		$name = (substr($name,0,1) == '_' ? '' : '\model\\').str_replace('_', '\\', $name);
		
		$obj = new $name();
		$obj->setup($this);
		return $obj;
	}
	
	/**
	 * Returns a modules model instance.
	 * @param string $name Module name
	 * @return reks\repo\Repository
	 */
	public function mod($name){
		return $this->app->module->get($name)->getTargetRouter()->getResource(\reks\App::RES_REPOSITORY);
	}
	
	/**
	 * Returns the super application's ( parent application's ) reks\repo\Repository
	 * @return reks\repo\Repository
	 */
	public function super(){
		return $this->app->superRouter->getResource(\reks\App::RES_REPOSITORY);
	}
	
	
	/**
	 * Returns a raw database object using configuration from $config['db'].
	 * Useful for simple operation.
	 * @return reks\tools\DB
	 */
	public function rawDB(){
		return $this->_reks_repo_PDORepo->db;
	}
	
	
}