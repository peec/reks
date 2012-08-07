<?php
namespace reks\view\script\compilers;

/**
 * Js compiler, for javascripts .
 * @author peec
 *
 */
class JsCompiler extends Compiler{
	
	private $jsScripts = array();
	
	
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
	
	
	public function get(){
		$ret = '';
		$ret .= $this->show($this->view->url->reverse('/reks/controller/JSController.routes'));
		
		if ($this->view->app->inProduction()){
			$ret .= $this->show($this->view->url->reverse('/reks/controller/JSController.cacheRoutes') . "?cacheRoutes={$this->encodeJavascripts()}");
		}else{
			$ret .= $this->render();
		}
		$ret .= $this->render(); // Render the rest of non cached scripts.
		
		// Render <script> tags.
		foreach($this->jsScripts as $content){
			$ret .= $content;
		}
		return $ret;
	}
	
	public function display(){
		echo $this->get();
	}
	
	
	public function show($src){
		return '<script type="text/javascript" src="'.$src.'"></script>';
	}
	
}