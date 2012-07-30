<?php
namespace controller;

class Main extends MyModule{ // Check this! , we are extending a class that really belongs to another app.
	
	public function helloWorld(){
		
		// Render test.php in the mymodule module.
		$this->view->mod('mymodule')->render('test');
		
		
		// Doing some USERINPUT things...
		
		// Set a session.
		$this->ui->session->testSession = 'Value of sesion..';
		// We can also do:
		$this->ui->session['testSession'] = 'Value of sesion..';
		
		
		// $q will be null if we dont get ?q=... in url.
		if ($q = $this->ui->get->q){
			
		}
		
		
		
		// Assign $header to get a specific value.
		$this->view->assign('header', 'REKS Framework!');
		
		// Append SEO friendly title. using the head class - title method.
		$this->view->head->title('REKS Framework');
		$this->view->head->title('Hello World');
		
		// Render a view file.
		$this->view->render('helloworld');
	}
	
	
	public function hello($to){
		
		echo "Hello $to!  ( Only characters a-z and A-Z is allowed up in the url bar ) ";
	}
}