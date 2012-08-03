<?php
namespace reks\tests;

use \reks\router\RouteRule;

class Router extends \reks\controller\UnitTest{
	/**
	 * Router instance.
	 * @var reks\Router
	 */
	protected $router;
	
	public function setup(){
		$config = array();
		$this->router = new \reks\router\Router(
			$this->app,
			microtime(true),
			$config,
			new \reks\core\Log(0, $this->app->APP_PATH . '/logs'),
			'/'
		);
		
	}
	
	/**
	 * @expect exception \Exception
	 * @throws \Exception
	 */
	public function testRouteOne(){
		throw new \Exception(":Pp.PP:P:P:Pp.p.pp:P.pp.P::P.p");
		$this->router->setURI('/test');
		
		// Test getComponents.
		$route = new RouteRule($this->router, '/test','One.index','*');
		list($controller, $method, $args) = $route->parseTo();
		$this->assertEqual($controller, 'One');
		$this->assertEqual($method, 'index');
		$this->assertArray($args);
		$this->assertTrue(count($args) == 0);
		
		// Test reversing the controller / method.
		$reversedLink = $route->reverse('One.index');
		$this->assertEqual('/test', $reversedLink);
		
	}
	
	
	public function testRouteTwo(){
		$this->router->setURI('/one/two');
		
		
		$route = new RouteRule($this->router, '/@one/@t','Two.two(@one    ,  	@t)','*');
		list($controller, $method, $args) = $route->parseTo();
		
		$this->assertEqual($controller, 'Two');
		$this->assertEqual($method, 'two');
		$this->assertArray($args);
		$this->assertTrue(count($args) == 2);
		
		list($from, $vars) = $route->parseFrom();
		
		// Check the regexp generated from vParser.
		$this->assertEqual($from, '/([A-Za-z0-9_\.\-]*)/([A-Za-z0-9_\.\-]*)');
		// Check the vars.
		$this->assertEqual($vars[0]['var'], '@one');
		$this->assertEqual($vars[1]['var'], '@t');
		
		// And test reverse.
		$reversedLink = $route->reverse('Two.two', array('t' => 'dos', 'one' => 'ono'));
		$this->assertEqual($reversedLink, '/ono/dos');
		
		
	}
	
	public function testRouteThree(){
		$this->router->setURI('/123456789/12345');
		
		$route = new RouteRule($this->router, '/@reg<\d+>/@exp<[0-5]*>','Three.three(@exp, @reg)','*');
		list($controller, $method, $args) = $route->parseTo();
		
		$this->assertEqual($controller, 'Three');
		$this->assertEqual($method, 'three');
		$this->assertArray($args);
		$this->assertTrue(count($args) == 2);
		
		list($from, $vars) = $route->parseFrom();
		// Check the regexp generated from vParser.
		$this->assertEqual($from, '/(\d+)/([0-5]*)');
		// Check the vars.
		$this->assertEqual($vars[0]['var'], '@reg');
		$this->assertEqual($vars[1]['var'], '@exp');
		
		// And test reverse.
		$reversedLink = $route->reverse('Three.three', array('reg' => 123456789, 'exp' => '12345'));
		$this->assertEqual($reversedLink, '/123456789/12345');
		
	}
	
	
	
}