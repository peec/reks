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
		$loader = new DoctrineLoader($this->app, $this->config['applicationMode'], $this->config['db_doctrine']);
		$this->em = $loader->getEM();
	}
	
	
	
	
	
	
}