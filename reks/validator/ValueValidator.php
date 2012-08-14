<?php
namespace reks\validator;
/**
 * Value validator can validate array of strings or just plain strings.
 * Also works on recursive array.
 * 
 * @author Petter Kjelkenes<kjelkenes@gmail.com>
 *
 */
class ValueValidator extends Validator{
	
	/**
	 * Creates a new validator.
	 * @return reks\validator\ValueValidator
	 */
	static public function create(){
		return new ValueValidator();
	}
	
	/**
	 * Sets the maximum length of a string.
	 * @param int $length Max length
	 */
	public function maxLen($length){
		$this->add(function($value) use($length){
			if (strlen($value) > $length)return sprintf("Value can have maximum of %d characters.", $length);
		});
	}
	
	/**
	 * Sets the minimum length of a string.
	 * @param int $length Min length
	 */
	public function minLen($length){
		$this->add(function($value) use($length){
			if (strlen($value) < $length)return sprintf("Value must have minimum %d characters.", $length);
		});
	}
	
	/**
	 * Regexp pattern testing.
	 * @param string $pattern Regexp pattern.
	 */
	public function regexp($pattern){
		$this->add(function($value) use($length){
			if (preg_match($pattern, $value))return sprintf("Value must follow the pattern %s.", $pattern);
		});
	}
	
	
	
	/**
	 * Validates array or string.
	 * @param mixed $args Arguments to validate.
	 * @see reks\validator.Validator::validate()
	 */
	public function validate($args = null){
		if (is_array($args)){
			foreach($args as $v){
				$this->validate($v);
			}
		}else{
			foreach($this->rules as $rule){
				if ($msg = $rule($args))throw new ValidationException($msg);
			}
		}
	}
	
	
}