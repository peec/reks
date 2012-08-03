<?php
namespace reks\view\script\compilers;

/**
 * Js compiler, for javascripts .
 * @author peec
 *
 */
class JsCompiler extends Compiler{
	
	private $jsScripts = array();
	
	private $jsRoutes = false;
	
	/**
	 * Use BEFORE <script> tags in middle of content
	 */
	public function start(){
		ob_start();
	}

	/**
	 * Use AFTER </script> tags.
	 */
	public function end(){
		$this->jsScripts[] = ob_get_clean();
	}
	
	public function setup(){
		$this->setExtension('js');	
		$this->setCompiler(function($file, $content){
			return \reks\vendor\jsmin\JSMin::minify($content);
		});
	}
	
	
	public function encodeJavascripts(){
		$str = '';
		foreach($this->scripts as $script => $cacheInProd){
			if ($cacheInProd){
				$str .= $script .',';
				unset($this->scripts[$script]);
			}
		}
		return $this->view->url->encryption()->encode($str);
	}
	
	public function display(){
		
		if ($this->jsRoutes){
			echo $this->show($this->view->url->reverse('/reks/controller/JSController.routes'));
		}
		if ($this->view->app->inProduction()){ // @todo FIX.
			echo $this->show($this->view->url->reverse('/reks/controller/JSController.cacheRoutes') . "?cacheRoutes={$this->encodeJavascripts()}");
		}else{
			echo $this->render();
		}
		echo $this->render(); // Render the rest of non cached scripts.
		
		// Render <script> tags.
		foreach($this->jsScripts as $content){
			echo $content;
		}
	}
	
	
	public function addJsRoutes(){
		$this->jsRoutes = true;
		return $this;
	}
	
	public function show($src){
		return '<script type="text/javascript" src="'.$src.'"></script>';
	}
	
}