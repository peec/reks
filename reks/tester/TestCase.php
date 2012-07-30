<?php
/**
 * REKS framework is a very lightweight and small footprint PHP 5.3+ Framework.
 * It supports a limited set of features but fully MVC based and Objectoriented.
 *
 * Copyright (c) 2012, REKS group ( Lars Martin Rørtveit, Andreas Elvatun, Petter Kjelkenes, Pål André Sundt )
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the REKS GROUP nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL "REKS Group" BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @license 3-clause BSD
 * @package reks
 * @author REKS group at Telemark University College
 */
namespace reks\tester;

/**
 * This abstract Controller class lets you unit test your code.
 * This extends \reks\Controller.
 *
 * All tests should have the method name starting with "test", these are automatically run.
 * In your test method you should include assertions by using the assert* methods in this class.
 *
 * Once you have written your tests you can add a route to it and run it.
 * Note that it should route to a index method ( final method ).
 *
 * Example: $config['route']['get']['/testmodels'] = 'MyTestController.index';
 *
 * Browse to the url /testmodels and see unit testing in action.
 *
 * @author peec
 *
 */
abstract class TestCase extends \reks\Controller{

	private $testcontrollers = array();
	private $tests = array();

	/**
	 * Should be routed to.
	 */
	final public function index(){

		$result = $this->runTests();

		$this->view->assign('testName', get_class($this));
		$this->view->assign('result', $result);

		$this->view->render($this->app->BASE_REKS . '/reks/res/testview.php');
	}



	/**
	 * Adds a new test controller to this instance.
	 * @param mixed $controller A object extending \reks\tester\TestCase or a string representation of the class (eg. controller\TestController ).
	 */
	final protected function addTest($controller){
		$this->testcontrollers[] = $controller;
	}

	/**
	 * Runs all tests added including self.
	 * @return reks\tester\TestResult
	 */
	final public function runTests(){

		// Create a new router.
		$r = \reks\RouterFactory::create($this->app);


		$result = new TestResult();


		foreach($this->testcontrollers as $controller){
			if (!($controller instanceof TestCase)){
				$controller = \reks\Controller::init($r, $this->config, $controller, $this->url, $this->state, $this->lang, $this->ui, $this->csrf, $this->view, $this->request, $this->log, $this->model, $this->activeRoute);
			}
			// Note: Recursive call.
			$result->add($controller->runTests());
		}


		$reflector = new \ReflectionClass($this);
			
		foreach($reflector->getMethods() as $method){
				
			$m =  $method->getName();
			
			
			// If the method starts with "test".
			if (substr($m, 0, 4) == 'test'){
				// Get docblock of method.
				$docblock = $method->getDocComment();
					
				// Format of allowed docblocks in @expect
				$expectations = array(
						'exception' => array(),
						'output' => null
				);
					
				// Parse @expect docblock.
				$pattern = "/@expect[\s]+(exception|output)[\s]+(|\"(.*?)\"|([A-Za-z0-9_\\\]*))\s/";
				if (preg_match_all($pattern, $docblock, $matches)){
					foreach($matches[1] as $k => $expect){
						if ($matches[3][$k])$value = $matches[3][$k]; // Quoted.
						else $value = $matches[4][$k]; // Not quoted
							
						// Has value
						if ($value)$expectations[strtolower($expect)][] = trim($value);
						// No value.
						else $expectations[strtolower($expect)] = true;
					}
				}
					
					
				// OB for expect content.
				ob_start();
					
				$exceptionThrown = false;
				$methodOutput = false;
					
				try{
					$this->$m();
				}catch(\Exception $e){
					$exceptionThrown = true;
					// Is this exception expected ?
					$exOk = false;
					// Loop the docblock parsed...
					foreach($expectations['exception'] as $ex){
						if ($e instanceof $ex){
							$exOk = true;
							continue;
						}
					}
					// Add it to the stack.
					$result->addCase(new TestResultExceptionItem(get_class($this), $m, $e, $exOk));
				}
				// Check content expectations.
				$content = ob_get_clean();
				if ($content)$methodOutput = true;
				

				if (!is_array($expectations['output']) && $expectations['output'] === true && !$content){
					$result->addCase(new OutputExpectedItem(get_class($this), $m, $expectations['output']));
				}elseif(is_array($expectations['output'])){
					$opOk = false;
					foreach($expectations['output'] as $op){
						if ($op == $content)$opOk = true;
					}
					if (!$opOk)$result->addCase(new OutputExpectedItem(get_class($this), $m, $expectations['output']));
				}
					
				
					
				// Oops , we expected exception here...
				if (!$exceptionThrown && count($expectations['exception']) > 0){
					$result->addCase(new TestResultExceptionExpectedItem(get_class($this), $m, $expectations['exception']));
				}
				// Oops, we did not expect output.
				if ($methodOutput && !$expectations['output']){
					$result->addCase(new OutputNotExpectedItem(get_class($this), $m, $content));
				}
					
				foreach($this->tests as $test){
					list($method, $ok) = $test;
					$result->addCase(new TestResultItem(get_class($this), $m, $method, $ok));

				}
					
					
					
				$this->tests = array();
			}
		}



		return $result;
	}




	protected function assertTrue($val){
		$this->tests[] = array(__FUNCTION__, $val == true);
		return $this;
	}

	protected function assertFalse($val){
		$this->tests[] = array(__FUNCTION__, $val == false);
		return $this;
	}

	protected function assertObject($val){
		$this->tests[] = array(__FUNCTION__, is_object($val));
		return $this;
	}
	protected function assertString($val){
		$this->tests[] = array(__FUNCTION__, is_string($val));
		return $this;
	}
	protected function assertBool($val){
		$this->tests[] = array(__FUNCTION__, is_bool($val));
		return $this;
	}
	protected function assertInt($val){
		$this->tests[] = array(__FUNCTION__, is_int($val));
		return $this;
	}
	protected function assertDouble($val){
		$this->tests[] = array(__FUNCTION__, is_double($val));
		return $this;
	}
	protected function assertFloat($val){
		$this->tests[] = array(__FUNCTION__, is_float($val));
		return $this;
	}
	protected function assertNumeric($val){
		$this->tests[] = array(__FUNCTION__, is_numeric($val));
		return $this;
	}
	protected function assertArray($val){
		$this->tests[] = array(__FUNCTION__, is_array($val));
		return $this;
	}
	protected function assertNull($val){
		$this->tests[] = array(__FUNCTION__, is_null($val));
		return $this;
	}
	protected function assertInstanceOf($object, $class){
		$this->tests[] = array(__FUNCTION__, ($object instanceof $class));
		return $this;
	}

	protected function assertIdentical($var, $var2){
		$this->tests[] = array(__FUNCTION__, $var === $var2);
		return $this;
	}
	protected function assertEqual($var, $var2){
		$this->tests[] = array(__FUNCTION__, $var == $var2);
		return $this;
	}
	protected function assertPattern($var, $pattern){
		$this->tests[] = array(__FUNCTION__, preg_match($pattern, $var));
		return $this;
	}

}



class TestResult{

	private $tests = array();

	public function addCase(TestResultItem $data){
		$this->tests[] = $data;
	}

	public function add(TestResult $res){
		$this->tests = array_merge($this->tests, $res->getCases());
	}

	public function isEmpty(){
		return count($this->tests) == 0;
	}

	public function hasErrors(){
		return $this->getFailCount() > 0;
	}


	public function getCases(){
		return $this->tests;
	}
	public function getFailedCases(){
		$ret = array();
		foreach($this->tests as $case){
			if (!$case->isSuccess())$ret[] = $case;
		}
		return $ret;
	}
	public function getSuccessCases(){
		$ret = array();
		foreach($this->tests as $case){
			if ($case->isSuccess())$ret[] = $case;
		}
		return $ret;
	}
	public function getFailCount(){
		return count($this->getFailedCases());
	}

	public function getSuccessCount(){
		return count($this->getSuccessCases());
	}

	public function getCount(){
		return count($this->tests);
	}
	public function getExceptionCount(){
		$i = 0;
		foreach($this->tests as $case){
			if ($case instanceof TestResultExceptionItem)$i++;
		}
		return $i;
	}
	public function getTestMethodCount(){
		$ar = array();
		foreach($this->tests as $test){
			if (!in_array($test->getTestName(), $ar))$ar[] = $test->getTestName();
		}
		return count($ar);
	}

	public function getTestControllers(){
		$ar = array();
		foreach($this->tests as $test){
			if (!in_array($test->getClass(), $ar))$ar[] = $test->getClass();
		}
		return $ar;
	}



}


class TestResultItem{
	private $testName;
	private $method;
	private $success;
	private $class;

	public function __construct($class, $testName, $method, $success){
		$this->testName = $testName;
		$this->method = $method;
		$this->success = $success;
		$this->class = $class;
	}
	public function isSuccess(){
		return $this->success;
	}

	public function getMethod(){
		return $this->method;
	}

	public function getTestName(){
		return $this->testName;
	}

	public function getClass(){
		return $this->class;
	}
}

class OutputNotExpectedItem extends TestResultItem{
	private $output;
	public function __construct($class, $testName, $output){
		parent::__construct($class, $testName, 'Unexpected OUTPUT', false);
		$this->output = $output;
	}
	public function getOutput(){
		return $this->output;
	}
}
class OutputExpectedItem extends TestResultItem{
	private $output;
	public function __construct($class, $testName, $output){
		parent::__construct($class, $testName, 'Expected OUTPUT', false);
		$this->output = $output;
	}

	public function getOutput(){
		if (!is_array($this->output) && $this->output === true) return '-- ANY OUTPUT --';
		else return $this->output;
	}
}
class TestResultExceptionExpectedItem extends TestResultItem{
	private $expectedExceptions;
	public function __construct($class, $testName, $expectedExceptions){
		parent::__construct($class, $testName, 'Expected Exception', false);
		$this->expectedExceptions = $expectedExceptions;
	}

	public function getExceptions(){
		return $this->expectedExceptions;
	}

}
class TestResultExceptionItem extends TestResultItem{
	private $exception;
	public function __construct($class, $testName, $ex,  $success=false){
		parent::__construct($class, $testName, get_class($ex), $success);
		$this->exception = $ex;
	}

	public function getException(){
		return $this->exception;
	}

}