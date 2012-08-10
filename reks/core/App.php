<?php
namespace reks\core;


use reks\http\Request;

use reks\repo\Repository;

use reks\i18n\Lang;

use reks\security\Csrf;

use reks\http\Userinput;

use reks\http\Url;

use reks\view\View;

use reks\router\RouterFactory,
	reks\router\Router,
	reks\http as http,
	reks\security as security;

/**
 * The app class is the heart of any REKS application.
 * The app stub contains global variables that are related to the current application.
 * 
 * Instead of global scoped code, we use App that represents a application.
 * 
 * @author peec
 *
 */
class App{
	
	
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
	 * @var array Array of loaded vendors see loadVendor
	 */
	public $loadedVendors = array();
	
	/**
	 * Internal Router
	 * @var reks\router\Router
	 */
	public $router;
	
	
	/**
	 * @var reks\core\Files
	 */
	public $files;
	
	/**
	 * @var reks\core\Config
	 */
	public $config;
	/**
	 * The starting time of the constructor of the Router in float microtime.
	 * @var float
	 */
	private $startTime;
	
	
	/**
	 * @var reks\core\Log
	 */
	public $log;
	
	
	/**
	 * The View class reference.
	 * Use this to output html and other view specific tasks.
	 *
	 * @var reks\view\View A reference to the View class.
	 */
	public $view;
	
	
	/**
	 * Holds the language class variable.
	 * Used for multi language projects.
	 *
	 * @var reks\i18n\Lang language reference variable.
	 */
	public $lang;
	
	/**
	 * Holds the request object.
	 *
	 * @var reks\http\Request
	 */
	public $request;
	
	/**
	 * Deals with models.
	 * Wrapper around loading and getting models.
	 * @var reks\repo\Repository
	 */
	public $model;
	
	
	/**
	 * Wrapper around all possible user-input data for PHP.
	 * @var reks\http\Userinput
	 */
	public $ui;
	
	/**
	 * CSRF protection library
	 * @var reks\security\Csrf
	 */
	public $csrf;
	
	/**
	 * Logger instance. Used to log application data.
	 * @var reks\http\Url
	 */
	public $url;
	
	
	/**
	 * @var array Array of sub applications where key is the app name and value is a App instance.
	 */
	private $subApps;
	
	/**
	 * @var reks\core\App The parent application ( if any ).
	 */
	private $parentApp;
	
	/**
	 * @var reks\core\App The Absolute super app ( entry point application ). If this is the super app it's a reference to itself.
	 */
	private $superApp;
	
	
	
	/**
	 * Application class.
	 * @param array App data config array.
	 */
	public function __construct($appData){
		$this->startTime = microtime(true);
		
		// Set public path if not defined in the app-data.
		if(!isset($appData['public_path']))$appData['public_path'] = $appData['app_path'] . DIRECTORY_SEPARATOR . 'public';
		
		
		$this->APP_PATH = $appData['app_path'];
		$this->PUBLIC_PATH = $appData['public_path'];
		$this->BASE_REKS = $appData['base_reks'];
		$this->APP_NAME = $appData['app_name'];
		$this->META = isset($appData['meta']) ? $appData['meta'] : null;
		
		// Add the autoloader.
		require_once $this->BASE_REKS . '/core/Autoloader.php';
		\reks\core\Autoloader::create($this);
		
		$this->files = new Files($this);
	}
	
	public function initComponents(){
		// Create the router.
		$this->router = RouterFactory::create($this, new Config($this->config['route']));
		unset($this->config['route']); // This is very important. Unset routes from configuration.
		
		
		// Create obligatory libraries to stub.
		if (!$this->log)$this->log = new Log($this->config['log_level'], $this->config['log_dir']);
		if (!$this->ui)$this->ui = new Userinput();
		if (!$this->csrf)$this->csrf = new Csrf($this->ui);
		if (!$this->url)$this->url = new Url($this->router, $this->config['remove_scriptpath'], $this->csrf->token(isset($this->config['csrf_token_name']) ? $this->config['csrf_token_name'] : 'safe_link_csrf'));
		if (!$this->lang)$this->lang = new Lang($this->files->getLangFile());
		if (!$this->view)$this->view = new View($this);
		if (!$this->model)$this->model = new Repository($this);
		if (!$this->request)$this->request = new Request();
		

		// Register modules now.
		if (isset($this->config['modules'])){
			foreach($this->config['modules'] as $k => $mod){
				$this->register($mod);
			}
		}
		
	}
	public function initConfig(Config $currentConfig = null){
		// Allow access to $app.
		$app = $this;
		
		
		if ($currentConfig){
			$config = $currentConfig->toArray();
			// Remove routes + modules. ( else it's a never ending loop right there on modules ).
			unset($config['modules']);
			unset($config['route']);
		}
		
		require $this->files->getConfigFile(); // get $config array.
		if (!isset($config))throw new \Exception(sprintf('$config array not found in %s. Please create the standard configuration file. ', $this->files->getConfigFile()));
		
		$cfg = new Config($config);
		$this->config = $cfg;
		
		
	}
	

	/**
	 * Fresh is used once per page load / application run.
	 * @param array $appData Array of application data.
	 * @return \reks\core\App
	 */
	static public function fresh($appData){
		$app = new App($appData);
		$app->superApp = $app;
		$app->initConfig();
		$app->initComponents();
		
		return $app;
	}
	
	
	/**
	 * Starts the application.
	 */
	public function main(){
		return $this->router->route();
	}
	
	
	
	/**
	 * Returns true if application is in production, false if not.
	 */
	public function inProduction(){
		return $this->config['applicationMode'] == self::APP_MODE_PROD;
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
		if ($configure)$loader->configure($this->config);
		
		$this->loadedVendors[$loaderName] = $loader;
		
		return $loader;
	}
	
	/**
	 * Gets execution time at this time.
	 */
	public function getExecutionTime(){
		return microtime(true) - $this->startTime;
	}
	
	/**
	 * Registers another application.
	 * @param string $appDataFile File path to the other application's app-data file.
	 */
	protected function register($appDataFile){
		require $appDataFile;
		// Set paths already known.
		$appData['base_reks'] = $this->BASE_REKS;
		$appData['app_path'] = dirname($appDataFile);
		

		$app = new App($appData);
		$app->initConfig($this->config); // Then init config ( Can override some config, or every config ).
		
		// Set shared resources ( Don't use more memory then we need )
		$app->ui = $this->superApp->ui;
		$app->csrf = $this->superApp->csrf;
		$app->request = $this->superApp->request;
		
		
		$app->initComponents();
		$this->subApps[$app->APP_NAME] = $app;
		$this->subApps[$app->APP_NAME]->setParentApp($this);
		$this->subApps[$app->APP_NAME]->setSuperApp($this->superApp);
		
	}
	
	/**
	 * Returns the App instance of the module.
	 * @param string $appName The application name as defined in app-data.php
	 * @throws \Exception
	 * @return reks\core\App
	 */
	public function childApp($appName){
		if (!isset($this->subApps[$appName]))throw new \Exception("$appName is not registered as a sub application of this application. Please do so in config.php with \$app->register( .. PATH TO APP-DATA OF SUB APP ..).");
		return $this->subApps[$appName];
	}
	/**
	 * Gets the current parent application if any.
	 * @return \reks\core\App The super application
	 */
	public function parentApp(){
		return $this->parentApp;
	}
	
	public function superApp(){
		return $this->superApp;
	}
	
	/**
	 * Sets the application's parent application ( Parent application )
	 * @param \reks\core\App $app The super application to be set.
	 */
	protected function setParentApp(App $app){
		$this->parentApp = $app;
	}
	
	/**
	 * Sets the current super application.
	 * @param App $app
	 */
	protected function setSuperApp(App $app){
		$this->superApp = $app;
	}
	
	
	
	
}