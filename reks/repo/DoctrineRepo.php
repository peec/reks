<?php
namespace reks\repo;

use reks\tool\DoctrineLoader;

/**
 * Doctrine repository.
 * @author peec
 *
 */
class DoctrineRepo extends ARepo{
	
	/**
	 * Entity manager instance.
	 * @var Doctrine\ORM\EntityManager
	 */
	public $em;
	
	
	public function setup(Repository $repo){
		parent::setup($repo);
		if (!isset($this->config['db_doctrine']))throw new \Exception("Could not use Doctrine repository because configuration 'db_doctrine' does not exist in config.php file.");
		
		$app = $this->app;
		$config = $this->config;
		$this->em = $repo->sharedResource(
			get_class(), 
			function() use($app, $config){
				$loader = new DoctrineLoader($app, $config['applicationMode'], $config['db_doctrine']);
				return $loader->getEM();
			}
		);
		
	}
	
	
	
	
	
	
}