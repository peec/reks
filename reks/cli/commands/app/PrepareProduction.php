<?php 
namespace reks\cli\commands\app;

use Symfony\Component\Console\Input\InputArgument,
Symfony\Component\Console\Input\InputOption,
Symfony\Component\Console,
Doctrine\ORM\Tools\Console\MetadataFilter,
\reks\core\ReksData;

/**
 * Command to create a new application
 */
class PrepareProduction extends AppCommand{
	

	/**
	 * @see Console\Command\Command
	 */
	protected function configure(){
		$this
		->setDescription('Prepares application for production mode.')
		->setHelp(<<<EOT
Prepares the application for production mode.
- Compiles dynamic parsers.

Note! If you use doctrine, run orm:generate-proxies aswell.
Note! that applicationMode must be set to 'prod' after.
EOT
		);
	}
	
	

	/**
	 * @see Console\Command\Command
	 */
	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		
		
		$cache = array();
		foreach($this->app->internalRouter->routes as $route){
			$cache[$route->getFrom()] = $route->parseAll();
		}

		$output->writeln("- Creating route parse cache to " . $this->app->getRoutesCacheFile());
		file_put_contents($this->app->getRoutesCacheFile(), ('<?php /* Generated '.date('y/m/d').' */ $parseCache = ' . var_export($cache, true) . ';'));
		
		
		$output->writeln("Production ready.");
	}
	
	
	
	
	




}