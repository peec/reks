<?php
namespace reks\core;

use reks\router\RouterFactory,
	reks\router\Router,
	reks\http as http,
	reks\security as security;

class App{
	const RES_UI = 1;
	const RES_CSRF = 2;
	const RES_URL = 4;
	const RES_LANG = 16;
	const RES_VIEW = 32;
	const RES_REQUEST = 64;
	const RES_REPOSITORY = 128;
	
	const RES_ALL = 255;
	
	
	const APP_MODE_PROD = 'prod';
	const APP_MODE_DEV = 'dev';
	
	
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
	 * @var reks\core\Module
	 */
	public $module;
	
	
	/**
	 * Closure to custom config handler.
	 * @var mixed
	 */
	public $configHandler;
	
	/**
	 * Super router of this.
	 * @var reks\router\Router
	 */
	public $superRouter;
	
	/**
	 * 
	 * @var array Array of loaded vendors see loadVendor
	 */
	public $loadedVendors = array();
	
	/**
	 * Internal Router
	 * @var reks\router\Router
	 */
	public $internalRouter;
	
	
	
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
		
		require_once $this->BASE_REKS . '/core/Autoloader.php';
		// Register autoloader.
		$loader = Autoloader::create($this);
		
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
		$router = RouterFactory::create($this, $this->superRouter);
		
		$this->setResources($router, $flags);
		
		return $router;
	}
	
	/**
	 * Sets new resources to a router based on flags.
	 * @param reks\router\Router $router
	 * @param int $flags Flags RES_ constants.
	 */
	public function setResources(Router $router, $flags){
		
		
		// Shared
		if (self::RES_UI & $flags){
			$router->setResource(self::RES_UI, new http\Userinput());
		}
		
		// Shared
		if (self::RES_CSRF & $flags){
			$router->setResource(self::RES_CSRF, new security\Csrf($router->getResource(self::RES_UI)));
		}
		
		// Private
		if (self::RES_URL & $flags){
			$router->setResource(self::RES_URL, new http\Url($router, $router->config['remove_scriptpath'], $router->getResource(self::RES_CSRF)->token('safe_link_csrf')));
		}
		
		
		// Private
		if (self::RES_LANG & $flags){
			$router->setResource(self::RES_LANG, new \reks\i18n\Lang($this->APP_PATH . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $router->config['language'] . '.php'));
		}
		
		// Private
		if (self::RES_VIEW & $flags){
			$router->setResource(self::RES_VIEW, new \reks\view\View($router->config, $router->getResource(self::RES_LANG), $router->getResource(self::RES_URL), $this, $router));
		}
		
		// Shared
		if (self::RES_REQUEST & $flags){
			$router->setResource(self::RES_REQUEST, new http\Request());
		}
		
		// Private
		if (self::RES_REPOSITORY & $flags){
			if (!$router->getResource(self::RES_LANG))$this->setResources($router, self::RES_LANG);
			$router->setResource(self::RES_REPOSITORY, new \reks\repo\Repository($router->config, $router->getResource(self::RES_LANG), $router->log, $this));
		}
	}
	
	

	/**
	 * Loads configuration from db ( calback really decides what to do ).
	 * @param Router $router
	 */
	public function loadCallbackConfig(Router $router){
		$configHandler = $this->configHandler;
		if ($configHandler != null){
			$router->config = $configHandler($router->getResource(App::RES_REPOSITORY)->rawDB());
		}
	}
	
	
	/**
	 * Starts the web application. Entry point. 
	 */
	public function main(){
		$router = $this->load(self::RES_ALL);
		$this->internalRouter = $router;
		
		$this->loadCallbackConfig($router);
		
		return $router->route();
	}
	
	/**
	 * Returns true if application is in production, false if not.
	 */
	public function inProduction(){
		$this->internalRouter->config['applicationMode'] == self::APP_MODE_PROD;
	}
	
	
	/**
	 * Loads a vendor ( A class that extends \reks\vendor\Loader )
	 * Returns instance of the vendor, if already loaded it returns the same instance.
	 * 
	 * @param string $loaderName The loader name.
	 * @param boolean $configure Run the configure method ?
	 * @throws \Exception Throws Exception if this is not a Loader implementation.
	 * @return multitype:|Ambigous <\reks\vendor\Loader, unknown>
	 */
	public function loadVendor($loaderName, $configure = true){
		
		if (isset($this->loadedVendors[$loaderName]))return $this->loadedVendors[$loaderName];
		
		$loader = new $loaderName();
		if (!($loader instanceof \reks\vendor\Loader)){
			throw new \Exception("Vendor $loaderName Loader must extend \reks\vendor\Loader.");
		}
		
		$loader->setup($this);
		$loader->import();
		if ($configure)$loader->configure($this->internalRouter->config);
		
		$this->loadedVendors[$loaderName] = $loader;
		
		return $loader;
	}
	
	
}