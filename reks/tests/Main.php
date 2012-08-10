<?php
namespace reks\tests;

class Main extends \reks\controller\UnitTest{
	
	public function setup(){
		$this->addTest('\reks\tests\Router');
		$this->addTest('\reks\tests\AppTest');
	}
	
}