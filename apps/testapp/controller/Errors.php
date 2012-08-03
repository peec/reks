<?php
namespace controller;

class Errors extends \reks\controller\Controller{
	public function internalServerError(){
		die('500');
	}
	
	public function pageNotFound(){
		die('404');
	}
}