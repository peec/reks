<?php
namespace reks\repo;

use \reks\core\App;

class Repository{
	/**
	 * Array of configuration from config.php
	 * @var reks\core\Config
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
	public $repo;
	
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
	
	
	private $sharedResources = array();
	
	
	/**
	 * Constructs the model loader.
	 * @param reks\core\Config $config The global config array.
	 * @param reks\Lang $lang The language instance.
	 */
	public function __construct(App $app){
		$this->config = $app->config;
		$this->lang = $app->lang;
		$this->log = $app->log;
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
	
	public function sharedResource($id, $closure){
		if (!is_callable($closure))throw new \Exception("Shared resource closure must be callable.");
		if (isset($this->sharedResources[$id]))return $this->sharedResources[$id];
		$this->sharedResources[$id] = $closure();
		return $this->sharedResources[$id];
	}
	
	/**
	 * Returns a child Repository instance.
	 * @param string $appName The application name.
	 * @return reks\repo\Repository
	 */
	public function childModel($appName){
		return $this->app->childApp($appName)->model;
	}
	/**
	 * Returns a parent Repository instance.
	 * @param string $appName The application name.
	 * @return reks\repo\Repository
	 */
	public function parentModel($appName){
		return $this->app->parentApp($appName)->model;
	}
	/**
	 * Returns a super Repository instance.
	 * @param string $appName The application name.
	 * @return reks\repo\Repository
	 */
	public function superModel(){
		return $this->app->superApp()->model;
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