<?php
namespace reks\controller;

use \reks\http\Response;

class RouteErrorHandler extends Controller{
	public function pageNotFound(){
		$this->view->assign('code', 404);
		$this->view->assign('requesturi', $_SERVER['REQUEST_URI']);
				
		return new Response($this->app->BASE_REKS . DIRECTORY_SEPARATOR . 'res' . DIRECTORY_SEPARATOR . 'errorException.php', Controller::C_HTML, 404);
	}
	
	public function internalServerError(){
		$this->view->assign('requesturi', $_SERVER['REQUEST_URI']);
		
		return new Response($this->app->BASE_REKS . DIRECTORY_SEPARATOR . 'res' . DIRECTORY_SEPARATOR . 'errorException.php', Controller::C_HTML, 500);
	}
}

