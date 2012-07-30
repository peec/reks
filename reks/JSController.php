<?php
namespace reks;

/**
 * Javascript controller.
 * 
 * 
 * @author peec
 *
 */
class JSController extends Controller{

	
	public function routes(){
		
		$this->view->assign('jsRoutes', $this->router->routes);
		
		return new Response($this->view->fetch($this->app->BASE_REKS . '/reks/res/jsRoutes.php'), self::C_JAVASCRIPT);
	}
	
	
}