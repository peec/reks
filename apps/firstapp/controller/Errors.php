<?php
namespace controller;

class Errors extends \reks\Controller{
	public function internalServerError(){
		die('500');
	}
	
	public function pageNotFound(){
		die('404');
	}
}