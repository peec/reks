<?php
namespace reks\vendor;

use reks\core\App;

class DoctrineLoader extends Loader{
	/**
	 * Entity manager instance.
	 * @var Doctrine\ORM\EntityManager
	 */
	protected $em;
	
	/**
	 * Entity manager instance.
	 * @var Doctrine\ORM\EntityManager
	 */
	public function getEM(){
		return $this->em;
	}
	
	
	public function import(){
		require_once $this->app->BASE_REKS . '/vendor/doctrine/Doctrine/ORM/Tools/Setup.php';
		
		$lib = $this->app->BASE_REKS . '/vendor/doctrine';
		
		\Doctrine\ORM\Tools\Setup::registerAutoloadDirectory($lib);
		
	}
	
	public function configure(\reks\core\Config $cnf){
		if (!$this->app->inProduction() || !function_exists('apc_fetch')) {
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
		
		
		if (!$this->app->inProduction()) {
			$config->setAutoGenerateProxyClasses(true);
		} else {
			$config->setAutoGenerateProxyClasses(false);
		}
		
		$this->em = \Doctrine\ORM\EntityManager::create($cnf['db_doctrine'], $config);
	}
	
	
}