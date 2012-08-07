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
class SecurityCheck extends AppCommand{
	

	/**
	 * @see Console\Command\Command
	 */
	protected function configure(){
		$this
		->setDescription('Checks if your app contains security holes.')
		->setHelp(<<<EOT
This command checks if you got any security holes.
It tries to find XSS injectable code.
EOT
		);
	}
	
	

	/**
	 * @see Console\Command\Command
	 */
	protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output){
		parent::execute($input, $output);
		
		$ite = new \RecursiveDirectoryIterator($this->app->APP_PATH . DIRECTORY_SEPARATOR . 'view');
		
		$errors = array();
		$checked = 0;
		
		$output->writeln("Reading files...");
		foreach (new \RecursiveIteratorIterator($ite) as $file=>$cur) {
			$c = file_get_contents($file);
			
			$output->writeln(" - " . $file);
			if ($block = $this->codeBlock($c, stripos($c, 'echo'))){
				
				$errors[$file] = $block;
			}
			$checked++;
		}
		
		foreach($errors as $file => $block){
			
			$output->writeln("ERROR: ".$file);
			$output->writeln("--------------------");
			$output->writeln($block);
			$output->writeln("--------------------");
		}
		
		$output->writeln(count($errors)." errors. ( Total files $checked checked )");
	}
	
	public function codeBlock($c, $pos){
		if (!$pos)return false;
		$block = '';
		$max = 0;
		for($i = $pos; $i > 1 && $max < 30; $i--){
			$block = $c[$i-1] . $block;
			$max++;
		}
		$max = 0;
		$k = strlen($c);
		for($i = $pos; $i < $k && $max < 30; $i++){
			$block = $block . $c[$i];
			$max++;
		}
		
		return $block;
	}

}