<?php
namespace controller;

class MyModule extends \reks\controller\Controller{
	
	public function test(){
		
		echo "This is just a module but it can also be used as application. See the 'firstapp' example.";
	}
}