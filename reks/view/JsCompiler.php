<?php
namespace reks\view;

/**
 * Js compiler, for javascripts .
 * @author peec
 *
 */
class JsCompiler extends ScriptCompiler{
	
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
	
	/**
	 * Renders the scripts in normal way of just outputting many tags for each file.
	 */
	public function render(){
		
		// Render script files.
		foreach($this->scripts as $script){
			$this->show($this->view->url->asset($script));
		}
		if ($this->jsRoutes){
			$this->show($this->view->url->reverse('/reks/JSController.routes'));
		}
		
		// Render <script> tags.
		foreach($this->jsScripts as $content){
			echo $content;
		}
		
	}
	
	public function addJsRoutes(){
		$this->jsRoutes = true;
		return $this;
	}
}