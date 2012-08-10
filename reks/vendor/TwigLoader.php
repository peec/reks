<?php
namespace reks\vendor;

use reks\view\twig\Extension;

use reks\core\App;
use \reks\view\twig\tags as tags;
class TwigLoader extends Loader{
	
	/**
	 * @var \Twig_Environment
	 */
	private $twig;
	
	private $loader;
	
	public function getTwig(){
		return $this->twig;
	}
	
	public function import(){
		require_once $this->app->BASE_REKS . '/vendor/twig/lib/Twig/Autoloader.php';
		\Twig_Autoloader::register();
		
		$this->loader = new \Twig_Loader_Filesystem($this->app->APP_PATH . '/view');
	}
	
	
	/**
	 * 
	 * @see \reks\vendor\Loader::configure()
	 */
	public function configure(\reks\core\Config $cnf){
		$def = array('charset' => 'utf-8');
		if ($this->app->inProduction()){
			$def['cache'] = $this->app->APP_PATH . '/cache/twig/php';
		}else{
			$def['cache'] = false;
			$def['debug'] = true;
		}
		
		$cnf['twig'] = isset($cnf['twig']) ? $cnf['twig'] : array();
		
		
		$this->twig = new \Twig_Environment($this->loader, array_merge($def, $cnf['twig']));

		$view = $this->app->view;

		$this->twig->addExtension(new \reks\view\twig\Extension($view));
	}
	
	
}