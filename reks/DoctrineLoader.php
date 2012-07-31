<?php
namespace reks;

class DoctrineLoader extends \reks\tool\Singleton{
	/**
	 * Entity manager instance.
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	protected $loaded = false;
	
	/**
	 * 
	 * @var reks\App
	 */
	protected $app;
	
	protected $config;
	
	protected $appMode;
	
	/**
	 * Entity manager instance.
	 * @var Doctrine\ORM\EntityManager
	 */
	public function getEM(){
		if (!$this->loaded)$this->load();
		return $this->em;
	}
	
	/**
	 * Populates this singleton with app instance.
	 * @param App $app
	 */
	public function populate(App $app, $appMode, array $config){
		$this->app = $app;
		$this->config = $config;
		$this->appMode = $appMode;
	}
	
	
	protected function load(){
		
		require_once $this->app->BASE_REKS . '/reks/doctrine/Doctrine/ORM/Tools/Setup.php';
	
		$lib = $this->app->BASE_REKS . '/reks/doctrine';
	
		\Doctrine\ORM\Tools\Setup::registerAutoloadDirectory($lib);
	
	
		if ($this->appMode == 'dev') {
			$cache = new \Doctrine\Common\Cache\ArrayCache;
		}else{
			$cache = new \Doctrine\Common\Cache\ApcCache;
		}
	
		$config = new \Doctrine\ORM\Configuration;
		$config->setMetadataCacheImpl($cache);
		$driverImpl = $config->newDefaultAnnotationDriver($this->app->APP_PATH . '/model');
		$config->setMetadataDriverImpl($driverImpl);
		$config->setQueryCacheImpl($cache);
		$config->setProxyDir($this->app->APP_PATH . '/proxies');
		$config->setProxyNamespace('proxies');
	
		
		if ($this->appMode == 'dev') {
			$config->setAutoGenerateProxyClasses(true);
		} else {
			$config->setAutoGenerateProxyClasses(false);
		}
		$this->loaded = true;
		$this->em = \Doctrine\ORM\EntityManager::create($this->config, $config);
	
	
	}
	
	
}

namespace model;
/**
 * Function to get entity manager. Note if this is a module using this, the entity manager of the super application is used.
 * @return \Doctrine\ORM\EntityManager
 */
function em(){
	return \reks\DoctrineLoader::getInstance()->getEM();
}

