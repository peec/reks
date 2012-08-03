<?php
namespace reks\vendor;

use reks\core\App;

/**
 * A loader that can load 3rdparty libs.
 * @author peec
 *
 */
abstract class Loader{
	
	
	/**
	 * @var reks\core\App
	 */
	protected $app;
	
	
	
	final public function setup(App $app){
		$this->app = $app;
	}
	
	/**
	 * This should include the autoloader or just normal include.
	 */
	abstract public function import();
	
	/**
	 * This configures the import done with import().
	 * Use $this->config or $this->app to configure it.
	 */
	abstract public function configure(\reks\core\Config $config);
	
	
	
	
}