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
	
	
}