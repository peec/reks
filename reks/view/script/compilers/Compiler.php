<?php
namespace reks\view\script\compilers;

abstract class Compiler{
	
	
	/**
	 * View reference.
	 * @var reks\view\View
	 */
	protected $view;
	
	/**
	 * Array of scripts added. Relative path from public
	 * @var array
	 */
	protected $scripts=array();
	
	
	protected $compiler;
	
	private $ext;
	
	/**
	 * Creates a new object of the Script compiler.
	 * @param reks\View $view View instance
	 */
	public function __construct($view){
		$this->view = $view;
	}
	
	public function setCompiler($compiler){
		$this->compiler = $compiler;
	}
	
	/**
	 * Adds a resource.
	 * Example:
	 * <code>
	 * 		$this->js->add('js/jquery.js');
	 * </code>
	 * @param string $resource Resource from the public space. ( Where index.php is )
	 */
	public function add($resource, $cacheInProd = true){
		if (isset($this->scripts[$resource]))return $this;
	
		$this->scripts[$resource] = $cacheInProd;
		return $this;
	}
	
	protected function getHash(){
		$hash = implode('',array_values($this->scripts));
		foreach($this->scripts as $script => $cacheInProd){
			if ($cacheInProd)$hash .= filemtime($script);
		}
		return md5($hash);
	}
	
	
	public function compile(){
		$c = $this->compiler;
		$op = '';
		foreach($this->scripts as $resource => $cacheInProd){
			if ($cacheInProd){
				$op .= $c($this->view->app->PUBLIC_PATH . "/$resource", file_get_contents($this->view->app->PUBLIC_PATH . "/$resource"));
			}
		}
		return $op;
	}
	
	public function cache(){
		if (count($this->scripts) == 0)return null;
		
		
		
		
		$hash = $this->getHash();
		$ext = $this->getExt();
		$cF = $this->view->app->PUBLIC_PATH . "/cache/{$hash}.$ext";
		
		if (!file_exists($cF)){
			
			$content = $this->compile();
			file_put_contents($cF, $content);
		}
		
		foreach($this->scripts as $resource => $cacheInProd){
			if ($cacheInProd)unset($this->scripts[$resource]);
		}
		
		return $this->show($this->view->url->asset("cache/{$hash}.{$ext}"));
	}
	
	/**
	 * Renders the scripts in normal way of just outputting many tags for each file.
	 */
	public function render(){
		$r = '';
		foreach($this->scripts as $script => $cacheInProd){
			$r .= $this->show($this->view->url->asset($script));
			unset($this->scripts[$script]);
		}
		return $r;
	}
	
	
	public function display(){
		if ($this->view->app->inProduction()){
			echo $this->cache();
		}
		echo $this->render();
	}
	
	
	public function setExtension($ext){
		$this->ext = $ext;
	}
	
	public function getExt(){
		return $this->ext;
	}
	
	abstract public function show($src);
	
	abstract public function setup();
	
	
}