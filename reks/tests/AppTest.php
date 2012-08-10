<?php
namespace reks\tests;

use reks\core\App;

use reks\core\Config;

use \reks\router\RouteRule;

class AppTest extends \reks\controller\UnitTest{
	
	
	public function testAppCreate(){
		$app = new App(array(
				'base_reks' => $this->app->BASE_REKS,
				'app_path' => 'path',
				'public_path' => 'public',
				'app_name' => 'name'
				));
		
		$this->assertIdentical($app->BASE_REKS, $this->app->BASE_REKS);
		$this->assertIdentical($app->APP_PATH, 'path');
		$this->assertIdentical($app->PUBLIC_PATH, 'public');
		$this->assertIdentical($app->APP_NAME, 'name');
		
		
	}
	
	
	
	
}