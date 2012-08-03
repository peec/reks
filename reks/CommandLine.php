<?php
namespace reks;
use Symfony\Component\Console\Application;
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
	
	
	
	static public function reksCommandLine($appDir){
		$base = dirname(__FILE__);
		require_once $base . '/App.php';
		
		
		$app = new \reks\App(array('base_reks' => $base.'/..', 'app_path' => $appDir, 'public_path' => $appDir, 'app_name' => 'commandline'));
		
		
		require_once $app->BASE_REKS . '/reks/doctrine/Doctrine/ORM/Tools/Setup.php';
		$lib = $base . '/doctrine';
		\Doctrine\ORM\Tools\Setup::registerAutoloadDirectory($lib);

		
		$cli = new Application(ReksData::NAME, ReksData::VERSION);
		$cli->setCatchExceptions(true);
		$cli->addCommands(array(
				new \reks\commands\CreateApp()
		));
		$cli->run();
	}
	
	
}