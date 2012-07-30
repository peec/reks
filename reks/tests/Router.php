<?php
namespace reks\tests;


class Router extends \reks\tester\TestCase{
	/**
	 * Router instance.
	 * @var reks\Router
	 */
	private $router;
	
	public function setup(){
		$config['route']['*'] = array(
			'/test' => 'One.index',
			'/@one/@t' => 'Two.two(@one    ,  	@t)',
			'/@reg<\d+>/@exp<[0-5]*>' => 'Three.three(@exp, @reg)'
		);
		
		$this->router = new \reks\Router(
			$this->app,
			microtime(true),
			$config,
			new \reks\Log(0, $this->app->APP_PATH . '/logs'),
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
		list($controller, $method, $args) = $this->router->getComponents('One.index');
		$this->assertEqual($controller, 'One');
		$this->assertEqual($method, 'index');
		$this->assertArray($args);
		$this->assertTrue(count($args) == 0);
		
		// Test reversing the controller / method.
		$reversedLink = $this->router->reverse('One.index');
		$this->assertEqual('/test', $reversedLink);
		
	}
	
	
	public function testRouteTwo(){
		$this->router->setURI('/one/two');
		
		list($controller, $method, $args) = $this->router->getComponents('Two.two(@one    ,  	@t)');
		
		$this->assertEqual($controller, 'Two');
		$this->assertEqual($method, 'two');
		$this->assertArray($args);
		$this->assertTrue(count($args) == 2);
		
		list($from, $vars) = $this->router->vParser('/@one/@t');
		// Check the regexp generated from vParser.
		$this->assertEqual($from, '/([A-Za-z0-9_\.\-]*)/([A-Za-z0-9_\.\-]*)');
		// Check the vars.
		$this->assertEqual($vars[0]['var'], '@one');
		$this->assertEqual($vars[1]['var'], '@t');
		
		// And test reverse.
		$reversedLink = $this->router->reverse('Two.two', array('t' => 'dos', 'one' => 'ono'));
		$this->assertEqual($reversedLink, '/ono/dos');
		
		
	}
	
	public function testRouteThree(){
		$this->router->setURI('/123456789/12345');
		list($controller, $method, $args) = $this->router->getComponents('Three.three(@exp, @reg)');
		
		$this->assertEqual($controller, 'Three');
		$this->assertEqual($method, 'three');
		$this->assertArray($args);
		$this->assertTrue(count($args) == 2);
		
		list($from, $vars) = $this->router->vParser('/@reg<\d+>/@exp<[0-5]*>');
		// Check the regexp generated from vParser.
		$this->assertEqual($from, '/(\d+)/([0-5]*)');
		// Check the vars.
		$this->assertEqual($vars[0]['var'], '@reg');
		$this->assertEqual($vars[1]['var'], '@exp');
		
		// And test reverse.
		$reversedLink = $this->router->reverse('Three.three', array('reg' => 123456789, 'exp' => '12345'));
		$this->assertEqual($reversedLink, '/123456789/12345');
		
	}
	
	
	
}