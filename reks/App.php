<?php
namespace reks;


class App{
	const RES_UI = 1;
	const RES_CSRF = 2;
	const RES_URL = 4;
	const RES_STATE = 8;
	const RES_LANG = 16;
	const RES_VIEW = 32;
	const RES_REQUEST = 64;
	const RES_MODELWRAPPER = 128;
	
	const RES_ALL = 255;
	
	public $APP_PATH;
	public $PUBLIC_PATH;
	public $BASE_REKS;
	
	
	public $APP_NAME;
	
	/**
	 * 
	 * @var array Static metadata for the application.
	 */
	public $META;
	
	/**
	 * 
	 * @var reks\Module
	 */
	public $module;
	
	
	/**
	 * Closure to custom config handler.
	 * @var mixed
	 */
	public $configHandler;
	
	/**
	 * Super router of this.
	 * @var reks\Router
	 */
	public $superRouter;
	
	/**
	 * Application class.
	 * @param array App data config array.
	 */
	public function __construct($appData, Router $superRouter = null){
		$this->APP_PATH = $appData['app_path'];
		$this->PUBLIC_PATH = $appData['public_path'];
		$this->BASE_REKS = $appData['base_reks'];
		$this->APP_NAME = $appData['app_name'];	
		
		$this->META = isset($appData['meta']) ? $appData['meta'] : null;
		
		require_once $this->BASE_REKS . '/reks/Autoloader.php';
		// Register autoloader.
		$loader = \reks\Autoloader::create($this);
		
		$this->superRouter = $superRouter;
	}
	
	public function setSuper(Router $router){
		$this->superRouter = $router;
	}
	
	/**
	 * Loads a new app.
	 * @param int $flags Amount of new resources.
	 * @return reks\Router
	 */
	public function load($flags = self::RES_ALL){
		$router = \reks\RouterFactory::create($this, $this->superRouter);
		
		$this->setResources($router, $flags);
		
		
		
		return $router;
	}
	
	/**
	 * Sets new resources to a router based on flags.
	 * @param Router $router
	 * @param int $flags Flags RES_ constants.
	 */
	public function setResources(Router $router, $flags){
		
		
		// Shared
		if (self::RES_UI & $flags){
			$router->setResource(self::RES_UI, new Userinput());
		}
		
		// Shared
		if (self::RES_CSRF & $flags){
			$router->setResource(self::RES_CSRF, new Csrf($router->getResource(self::RES_UI)));
		}
		
		// Private
		if (self::RES_URL & $flags){
			$router->setResource(self::RES_URL, new Url($router, $router->config['remove_scriptpath'], $router->getResource(self::RES_CSRF)->token('safe_link_csrf')));
		}
		
		// Shared
		if (self::RES_STATE & $flags){
			$router->setResource(self::RES_STATE, new State($router->getResource(self::RES_URL)));
		}
		
		// Private
		if (self::RES_LANG & $flags){
			$router->setResource(self::RES_LANG, new Lang($this->APP_PATH . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $router->config['language'] . '.php'));
		}
		
		// Private
		if (self::RES_VIEW & $flags){
			$router->setResource(self::RES_VIEW, new View($router->config, $router->getResource(self::RES_LANG), $router->getResource(self::RES_URL), $this));
		}
		
		// Shared
		if (self::RES_REQUEST & $flags){
			$router->setResource(self::RES_REQUEST, new Request());
		}
		
		// Private
		if (self::RES_MODELWRAPPER & $flags){
			if (!$router->getResource(self::RES_LANG))$this->setResources($router, self::RES_LANG);
			$router->setResource(self::RES_MODELWRAPPER, new ModelWrapper($router->config, $router->getResource(self::RES_LANG), $router->log, $this));
		}
	}
	
	

	/**
	 * Loads configuration from db ( calback really decides what to do ).
	 * @param Router $router
	 */
	public function loadCallbackConfig(Router $router){
		$configHandler = $this->configHandler;
		if ($configHandler != null){
			$router->config = $configHandler($router->getResource(App::RES_MODELWRAPPER)->raw());
		}
	}
	
	
	/**
	 * Starts the web application. Entry point. 
	 */
	public function main(){
		$router = $this->load(self::RES_ALL);
		
		$this->loadCallbackConfig($router);
		
		return $router->route();
	}
	
	
}