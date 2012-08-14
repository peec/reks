<?php
namespace reks\validator;
/**
 * Abstract validator class.
 * @author peec
 *
 */
abstract class Validator{
	
	private $rules = array();

	public function add($closure){
		$this->rules[] = $closure;
	}
	
	public function validate($args = null){
		foreach($this->rules as $rule){
			if ($msg = $rule($args))throw new ValidationException($msg);
		}
	}
	
}
