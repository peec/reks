<?php 
namespace reks\commands;

use Symfony\Component\Console\Input\InputArgument,
Symfony\Component\Console\Input\InputOption,
Symfony\Component\Console,
Doctrine\ORM\Tools\Console\MetadataFilter,
\reks\ReksData;

/**
 * Command to create a new application
 */
abstract class ACommand extends Console\Command\Command{
	
	/**
	 * @see Console\Command\Command
	 */
	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output){
		$output->writeln(sprintf('%s v. %s', ReksData::NAME, ReksData::VERSION));
	
		if (!ReksData::isStable()){
			$output->writeln('WARNING: You are not running a stable release.');
		}
	
	}
	
}