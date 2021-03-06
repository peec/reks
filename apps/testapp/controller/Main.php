<?php
namespace controller;

class Main extends MyModule{ // Check this! , we are extending a class that really belongs to another app.
	
	/**
	 * This method just gives you some basic examples of what we can do in the controller.
	 */
	public function helloWorld(){
		
		
		// Test database ...
		// 1. Configurure db_doctrine configuration in config.php.
		// 2. Create a database ( with the above configuration )
		// 3. Go into commandline and to the public folder, run "php index.php orm:schema-tool:create".
		// 4. Uncomment the line below!
		// $this->model->UserRepo->createUser('user','mysecret_password');
		// 5. You can also use normal PDO repo..
		// $this->model->NormalDB->createUser('user','mysecret_password');
		
		// See NormalDB and UserRepo class in model directory to see the inner workings. 
		
		
		// Render test.php in the mymodule module.
		$this->view->childView('mymodule')->render('test');
		
		
		// Doing some USERINPUT things...
		
		// Single file uploade
		$file = $this->request->file->file('single');
		if ($file)$file->upload($this->app->APP_PATH . '/cache/' . $file->getName());
		
		// Multiple files.
		if ($files = $this->request->file->file('testfile')){
			foreach($files as $file){
				
				$file->validator()
					->isImage() // Must be a image.
					->maxSize(1024*1024*5) // In bytes.
					->mime(array('image/jpeg','image/png')) // allowed mime types
					->extensions(array('jpg','png')); // Allowed file extensions.
				
				$file->upload($this->app->APP_PATH . '/cache/' . $file->getName());	
			}
			
			die("Multiple files uploaded.");
		}
		
		// Assign $header to get a specific value.
		$this->view->assign('header', 'REKS Framework!');
		
		// Append SEO friendly title. using the head class - title method.
		$this->view->html->title('REKS Framework');
		$this->view->html->title('Hello World');
		
		// Render a view file.
		$this->view->render('helloworld.twig.html');
	}
	
	
	
	
	public function hello($to){
		$this->activeRoute->reverse();
		
		echo "Hello $to!  ( Only characters a-z and A-Z is allowed up in the url bar ) ";
	}
}