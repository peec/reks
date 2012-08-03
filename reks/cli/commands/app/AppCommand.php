<?php 
namespace reks\cli\commands\app;

use 
Symfony\Component\Console\Input\InputArgument,
Symfony\Component\Console\Input\InputOption,
Symfony\Component\Console,
Doctrine\ORM\Tools\Console\MetadataFilter,
reks\core\ReksData;

/**
 * Command to create a new application
 */
abstract class AppCommand extends Console\Command\Command{
	
	/**
	 * @var reks\core\App
	 */
	public $app;
	
	public function setApp(\reks\core\App $app){
		$this->app = $app;
	}
	
	/**
	 * @see Console\Command\Command
	 */
	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output){
		$output->writeln(sprintf('%s v. %s', $this->app->APP_NAME, 1));
	
		$output->writeln($this->app->inProduction() ? 'In production mode' : 'In development mode');
	}

	
	static public function appCommandFactory(AppCommand $command, \reks\core\App $app){
		$command->setName($app->APP_NAME . ':' . $command->getName());
		$command->setApp($app);
		return $command;
	}
	
}