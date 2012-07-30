<?php
namespace reks;

class Module{
	
	/**
	 * Array of all of the modules.
	 * @var unknown_type
	 */
	public $modules = array();
	
	
	/**
	 * When this flag is active, database instance uses its own database on the other application.
	 * @var int
	 */
	const INHERIT_DATABASE = 1;
	
	/**
	 * When this flag is active, routes will not be imported.
	 * @var int
	 */
	const SKIP_ROUTES = 2;
	
	public $callableHandlers = array();
	
	/**
	 * @var reks\Router
	 */
	public $owner;
	
	public function __construct(Router $router){
		$this->owner = $router;
	}
	
	
	/**
	 * Register a new module 
	 * @param string $moduleIndex Path to app-data.php file for the application.
	 * @param string $flags see constants in this class for available options. Options are separated with | symbol.
	 */
	public function register($moduleIndex, $flags = 0){
		$m =  new ModuleItem($this->owner, $moduleIndex, $flags);
		$this->modules[$m->getName()] = $m;
	}
	
	
	public function registerHandler($callable){
		if (!is_callable($callable))throw new \Exception("registerHandler needs a closure function.");
		$this->callableHandlers[] = $callable;
	}
	
	
	
	public function preload(){
		// Add DB handlers, because we can like these to be cool.
		foreach ($this->callableHandlers as $handler){
			$handler($this, $this->owner->getResource(App::RES_MODELWRAPPER)->raw());
		}
		// Run normal.
		foreach($this->modules as $mod){
			$mod->load();
		}
	}
	
	
	/**
	 * @return reks\ModuleItem
	 */
	public function get($name){
		if (!isset($this->modules[$name]))
			throw new \Exception("Module $name is not registered and can not be loaded.");
		return $this->modules[$name];
	}
	
	
}

class ModuleItem{
	/**
	 * Module folder.
	 * @var string
	 */
	private $moduleIndex;
	
	
	
	/**
	 * Flags for the module.
	 * @var int
	 */
	private $flags;
	
	
	
	/**
	 * @var reks\Router
	 */
	protected $router;
	
	
	private $appData;
	
	/**
	 * 
	 * @var reks\Router
	 */
	protected $targetRouter;
	
	/**
	 * Constructs a module
	 * @param string $moduleIndex Path to app-data.php file in the application.
	 * @param int $flags See Module class for available flags ( constants )
	 * @throws \Exception
	 */
	public function __construct(Router $owner, $moduleIndex, $flags){
		$this->router = $owner;
		
		if (!file_exists($moduleIndex))throw new \Exception("Module $moduleIndex can not be loaded because it does not exist.");
		
		$this->moduleIndex = $moduleIndex;
		$this->flags = $flags;

		
		$appData = null;
		include $this->moduleIndex; // Include app-data.
		// Check existance.
		if (!$appData)throw new \Exception("{$this->moduleIndex} does not seem to be a app-data file for reks based application.");
		
		
		$this->appData = $appData;
		$this->appData['base_reks'] = $owner->app->BASE_REKS;
		$this->appData['app_path'] = dirname($this->moduleIndex);
		
	}
	
	public function getName(){
		return $this->appData['app_name'];
	}
	
	
	
	public function setTargetRouter(Router $router){
		$this->targetRouter = $router;
	}
	
	/**
	 * @return reks\Router
	 */
	public function getTargetRouter(){
		return $this->targetRouter;
	}
	
	/**
	 * Starts load of module.
	 */
	public function load(){
		if ($this->router == null)throw new \Exception("Owner router is NULL. Could not load {$this->moduleIndex} module");
		
		$appData = $this->appData;
		
		$app = new \reks\App($appData, $this->router);
		
		$mRouter = $app->load(0); // Load private.
		
		$app->module = new Module($mRouter); // Reset module container.
		
		
		// Inherit resources.
		foreach($this->router->getResources() as $key => $res){
			$mRouter->setGlobalResource($key, $res);
			$mRouter->setResource($key, $res);
		}
		// Set new privates.
		$app->setResources($mRouter, App::RES_URL | App::RES_LANG | App::RES_VIEW | App::RES_MODELWRAPPER);
		
		
		
		if ((Module::SKIP_ROUTES & $this->flags)){
			$mRouter->deleteRoutes();
		}
		
		
		if ((Module::INHERIT_DATABASE & $this->flags)){
			$mRouter->setResource(App::RES_MODELWRAPPER, $this->router->getResource(App::RES_MODELWRAPPER));				
		}
		
		
		$this->setTargetRouter($mRouter);
		
		$app->loadCallbackConfig($mRouter);
		
		
	}
	
	
}
