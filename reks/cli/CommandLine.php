<?php
namespace reks\cli;
use Symfony\Component\Console\Application;
use \reks\core\ReksData;
use \reks\core\App;
use \reks\router\Router;


class CommandLine{
	/**
	 * 
	 * @var reks\core\App
	 */
	private $app;
	
	/**
	 * 
	 * @var Symfony\Component\Console\Helper\HelperSet
	 */
	private $set;
	
	public function __construct(\reks\core\App $r){
		$this->app = $r;
		
		
		$em = $this->app->model->_reks_repo_DoctrineRepo->em;
		
		// Set the helpers needed.
		$this->set = new \Symfony\Component\Console\Helper\HelperSet(array(
				'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($em->getConnection()),
				'em' => new \Doctrine\ORM\Tools\Console\Helper\EntityManagerHelper($em)
				));
		
		
		
	}
	
	public function run(){
		
		$appname = $this->app->APP_NAME;
		
		$commands = array(
			new commands\app\PrepareProduction("prepare-production")	
		);
		
		foreach($commands as $k => $c){
			$commands[$k] = commands\app\AppCommand::appCommandFactory($c, $this->app);
		}
		
		\Doctrine\ORM\Tools\Console\ConsoleRunner::run($this->set, $commands);
	}
	
	
	
	static public function reksCommandLine($appDir){
		$base = dirname(__FILE__);
		require_once $base . '/../core/App.php';
		
		
		$app = new \reks\core\App(array('base_reks' => $base.'/..', 'app_path' => $appDir, 'public_path' => $appDir, 'app_name' => 'commandline'));
		
		$app->loadVendor('\reks\vendor\DoctrineLoader', false);
		
		$cli = new Application(ReksData::NAME, ReksData::VERSION);
		$cli->setCatchExceptions(true);
		$cli->addCommands(array(
				new commands\CreateApp()
		));
		$cli->run();
	}
	
	
}