<?php
namespace controller\tests;

class Tests extends \reks\tester\TestCase{
	
	/**
	 * Expect that we echo "Hello World!"
	 * Also take use of assertEqual.
	 * 
	 * @expect output "Hello World!"
	 */
	protected function testLanguage(){
		// See lang/en.php
		$this->assertEqual($this->lang->hello_world, 'Hello World!');
		// Not useful, but it can be done.
		echo $this->lang->hello_world;
	}
	
	/**
	 * Tests if the hello method of model/SampleModel.php returns "Hello".
	 * 
	 * @expect exception \Exception
	 */
	protected function testSampleModel(){
		$this->assertEqual(
				$this->model->SampleModel->hello(),
				'Hello');
		
		$this->assertEqual(
			$this->model->SampleModel->throwException(false),
			'Happy?'
		);
		
		// The method iThrowException throws \Exception.
		$this->model->SampleModel->throwException(true);
		
	}
	
	
	
	
}