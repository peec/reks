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
		
		$wrapper = $this->router
			->getResource(App::RES_MODELWRAPPER);
		
		$wrapper
			->useDoctrine();
		
		// Set the helpers needed.
		$this->set = new \Symfony\Component\Console\Helper\HelperSet(array(
				'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($wrapper->em()->getConnection()),
				'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($wrapper->em())
				));
	}
	
	public function run(){
		\Doctrine\ORM\Tools\Console\ConsoleRunner::run($this->set);
	}
	
	
}