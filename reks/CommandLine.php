<?php
namespace reks;

class CommandLine{
	/**
	 * 
	 * @var reks\Router
	 */
	private $router;
	
	/**
	 * 
	 * @var Symfony\Component\Console\Helper\HelperSet
	 */
	private $set;
	
	public function __construct(Router $r){
		$this->router = $r;
		
		
		$em = $this->router->getResource(App::RES_REPOSITORY)->_reks_repo_DoctrineRepo->em;
		
		// Set the helpers needed.
		$this->set = new \Symfony\Component\Console\Helper\HelperSet(array(
				'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
				'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
				));
	}
	
	public function run(){
		\Doctrine\ORM\Tools\Console\ConsoleRunner::run($this->set);
	}
	
	
}