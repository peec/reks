<?php
namespace reks\controller;

use \reks\http\Response;
/**
 * Javascript controller.
 * 
 * 
 * @author peec
 *
 */
class JSController extends Controller{

	
	public function routes(){
		
		$this->view->assign('jsRoutes', $this->router->routes, false);
		
		return new Response($this->view->fetch($this->app->BASE_REKS . '/res/jsRoutes.php'), self::C_JAVASCRIPT);
	}
	
	
	/**
	 * 
	 * @return \reks\http\Response
	 */
	public function cacheRoutes(){
		$pubPath = realpath($this->app->PUBLIC_PATH);
		
		$scripts = explode(',', $this->url->encryption()->decode($this->request->get->cacheRoutes));

		$content = '';
		
		$done = array();
		foreach($scripts as $script){
			
			if (trim($script) && !in_array($script, $done)){
				$scriptPath = realpath($this->app->PUBLIC_PATH . DIRECTORY_SEPARATOR . $script);
				$jsFile = "cache.js." . md5($script) . '.js';
				$cF = $this->app->APP_PATH . DIRECTORY_SEPARATOR  . 'cache' . DIRECTORY_SEPARATOR . $jsFile;
				// SECURITY CHECK.
				if (substr($scriptPath, 0, strlen(realpath($pubPath))) == $pubPath && file_exists($scriptPath)){
					if (!file_exists($cF) || filemtime($scriptPath) > filemtime($cF)){
						file_put_contents($cF, \reks\vendor\jsmin\JSMin::minify(file_get_contents($script)));
					}else{
						$content .= file_get_contents($cF). "\n";
					}
					
				}else{
					// Just ignore.
				}
			}
		}
		return new \reks\http\Response($content, Controller::C_JAVASCRIPT);
	}
	
	
}