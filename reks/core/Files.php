<?php
namespace reks\core;

class Files{
	
	/**
	 * @var reks\core\App
	 */
	private $app;
	
	public function __construct(App $app){
		$this->app = $app;
	}
	
	/**
	 * @return string The path to the routes cache file.
	 */
	public function getRoutesCacheFile(){
		return $this->app->APP_PATH . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'reks.routes.cache.php';
	}
	/**
	 * @return string The path to the configuration file.
	 */
	public function getConfigFile(){
		return $this->app->APP_PATH . DIRECTORY_SEPARATOR . 'config.php';
	}
	
	public function getLangFile(){
		return $this->app->APP_PATH . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $this->app->config['language'] . '.php';
	}
	
}