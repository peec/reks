<?php
namespace controller;

/**
 * Sample controller.
 * @author Petter Kjelkenes<kjelkenes@gmail.com>
 */
class Main extends \reks\controller\Controller{
	
	/**
	 * Our home page method
	 */
	public function index(){
		// Assign $world to "World" to the view.
		$this->view->assign('world', 'World!!!!!');
		
		$this->view->html->title('My website');
		
		// Render view/index.php
		$this->view->render('index');
	}
}