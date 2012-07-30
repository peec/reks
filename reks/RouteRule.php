<?php
namespace reks;


/**
 * This is a router rule parser it can parse such as 
 * new RouteRule('URI FROM CLIENT', '/hi', 'TestController.method', 'post')
 * and
 * new RouteRule('URI FROM CLIENT', '/hi/@testarg', 'TestController.method(@testarg)', 'post')
 * 
 * @author peec
 *
 */
class RouteRule{
	private $from;
	private $to;
	private $type;
	
	/**
	 * State: Ordinary - initial state.
	 * @var int
	 */
	const S_ORD = 1;
	/**
	 * State: Variable - when variable @ sign found
	 * @var int
	 */
	const S_VAR = 2;
	/**
	 * State: Regexp - Regexp state, when in a regexp < .. >
	 * @var int
	 */
	const S_IN_REG = 3;
	/**
	 * State: Method - When method is opened.
	 * @var int
	 */
	const S_METHOD_O = 4;
	/**
	 * State: Arguments - When in arguments.
	 * @var int
	 */
	const S_ARGS = 5;
	
	/**
	 * Just string of alpha characters a-zA-Z
	 * @var string
	 */
	const alpha = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	/**
	 * String of numeric chars 0-9
	 * @var string
	 */
	const numeric = '0123456789';
	
	
	// These variables helps a lot for performance.
	private $vParseCache;
	private $componentsCache;
	
	/**
	 * 
	 * @var reks\Router
	 */
	private $router;
	
	public function __construct($router, $from, $to, $type = '*'){
		$this->from = $from;
		$this->to = $to;
		$this->type = $type;
		$this->router = $router;
	}
	
	
	/**
	 * Gets Controller, method and arguments from a $this->to string.
	 */
	public function parseTo(){
		if ($this->componentsCache)return $this->componentsCache;
		$controller = '';
		$method = '';
		$args = array();
	
		$state = self::S_ORD;
	
		$bits = str_split($this->to);
	
	
		$this->toVarPattern = self::alpha.self::numeric.'_@';
		$mPattern = $this->toVarPattern;
		$cPattern = "$this->toVarPattern/";
	
		// Argument key.
		$aK = 0;
	
		foreach ($bits as $c){
			switch($state){
				case self::S_ORD:
					if ($c == '.'){
						$state = self::S_METHOD_O;
					}elseif(strstr($cPattern, $c)){
						$controller .= $c;
					}else{
						throw new RouteParseException("Unexpected character: $c in TO part (Controller) of route $this->to.", ".$cPattern");
					}
					break;
				case self::S_METHOD_O:
					if ($c == '('){
						$state = self::S_ARGS;
					}elseif(strstr($mPattern, $c)){
						$method .= $c;
					}else{
						throw new RouteParseException("Unexpected character: $c in TO part (Method) of route $this->to.", "($mPattern");
					}
					break;
	
				case self::S_ARGS:
					if ($c == ','){
						$aK++;
					}elseif($c == ')'){
						$state = self::S_ORD;
					}elseif(strstr($this->toVarPattern, $c)){
						if (!isset($args[$aK])) $args[$aK] = null;
						$args[$aK] .= $c;
					}else if ($c == ' ' || $c == "\t" || $c == "\n" || $c == "\r"){
						// Just skip, OK.
					}else{
						throw new RouteParseException("Unexpected character: '$c' in TO part (Arguments) of route $this->to.", ",) $this->toVarPattern");
					}
					break;
	
			}
		}
	
		$ret =  array(
				$controller,
				$method,
				$args
		);
	
		$this->componentsCache = $ret;
	
		return $this->componentsCache;
	}
	
	
	/**
	 * Parses a from route to application array of variables.
	 * Returns a parsed regexp from route and array of variables with names only.
	 */
	public function parseFrom(){
		// Enable static caching.
		// This only needs to be run once.
		if ($this->vParseCache){
			return $this->vParseCache;
		}
	
		$bits = str_split($this->from);
		$state = self::S_ORD;
	
		$vars = array();
		$vK = -1;
		// Var length of a variable.
		$varLen = 0;
	
	
		// Tested - Roughly 3 x Faster then regexp.
	
		$varPattern = self::alpha."._";
		$ordPattern = self::numeric.self::alpha."/_-.";
	
		foreach($bits as $k => $c){
			switch($state){
				case self::S_ORD:
					// Start of var.
					if ($c == '@'){
						// Reset var length.
						$varLen = 0;
						// Increase variable iterator.
						$vK++;
	
						$state = self::S_VAR;
	
						// Avoid stupid warnings.
						if (!isset($vars[$vK])){
							$vars[$vK] = null;
						}
						if (!isset($vars[$vK]['var'])){
							$vars[$vK]['var'] = null;
						}
	
						$vars[$vK]['var'] .= $c;
						// Pass ...
					}elseif(strstr($ordPattern, $c)){
	
						// Do not pass. Error.
					}else{
						throw new RouteParseException("Unexpected character: '$c'  in FROM part route $this->from.", "@$ordPattern");
					}
					break;
				case self::S_VAR:
					// Increase var length.
					$varLen++;
					// End of var, remove semicolon.
					if($c == ';' && $varLen > 1){
						unset($bits[$k]);
						$state = self::S_ORD;
						// Also end of var, but keep the slash.
					}elseif($c == '/' && $varLen > 1){
						$state = self::S_ORD;
						// Start regexp
					}elseif($c == '<' && $varLen > 1){
						unset($bits[$k]);
						$state = self::S_IN_REG;
						// Append to var
					}elseif (strstr($varPattern, $c)){
						unset($bits[$k]);
						$vars[$vK]['var'] .= $c;
						// Parser error.
					}else{
						throw new RouteParseException("Unexpected character: '$c'  in FROM part (Variable) of route $this->from.", ($varLen > 1 ? ';/<' : '').$varPattern);
					}
					break;
				case self::S_IN_REG:
					if ($c == '>'){
						unset($bits[$k]);
						$state = self::S_ORD;
						// Allow all characters here.
					}else{
						unset($bits[$k]);
						if (!isset($vars[$vK]['regexp'])) $vars[$vK]['regexp'] = null;
						$vars[$vK]['regexp'] .= $c;
					}
					break;
			}
		}
	
	
	
		$j = 0;
		foreach($bits as $k => $v){
			if ($v == '@'){
				$var = $vars[$j];
				$bits[$k] = isset($var['regexp']) ? '('.$var['regexp'].')' : '([A-Za-z0-9_\.\-]*)';
				$j++;
			}
		}
	
	
		$ret = array(
				implode('',$bits),
				$vars);
	
		$this->vParseCache = $ret;
	
		return $this->vParseCache;
	}
	
	
	
	/**
	 * Gets a finished parsed components from a $to and $vars input.
	 * $vars are from $this->parseFrom().
	 * @param string $to
	 * @param array $vars
	 */
	public function getBackend($vars){
		
		list ($controller, $method, $args) = $this->parseTo();
		
		// And finally find the correct variable and pass value to the argument key.
		foreach($args as $k => $v){
			foreach($vars as $j => $var){
				if ($var['var'] == $v){
					$args[$k] = $var['val'];
					// Dont need this anymore.
					unset($vars[$j]);
				}
			}
		}
	
		// Allows for integration against parameters.
		foreach($vars as $k => $var){
			if (substr($var['var'], 0, 2) == '@.'){
				$exp = explode('.',substr($var['var'],2));
	
				list($t, $n) = $exp;
				switch(strtolower($t)){
					case 'get':
						$_GET[$n] = $var['val'];
						break;
					case 'post':
						$_POST[$n] = $var['val'];
						break;
					case 'session':
						$_SESSION[$n] = $var['val'];
						break;
				}
				unset($vars[$k]);
			}
		}
	
		// Now, for rest of the vars... replace.
		// Rest of the vars will be replaced within the controller or method scope.
		foreach($vars as $j => $var){
			if (strstr($controller.$method, $var['var'])){
				$controller = str_replace($var['var'], $var['val'], $controller);
				$method = str_replace($var['var'], $var['val'], $method);
				unset($vars[$j]);
			}
		}
	
	
		return array($controller,
				$method,
				$args);
	}
	
	
	/**
	 * Compares a from and returns the components if $from matches.
	 * @param string $from
	 * @param string $to
	 */
	public function compare(){
	
	
		list($from, $vars) = $this->parseFrom();
	
		if (preg_match("#^{$from}$#", $this->router->uri, $matches)){
				
			foreach($matches as $k => $match){
				if ($k != 0)$vars[$k-1]['val'] = $match;
			}
			return $this->getBackend($vars);
		}
		return null;
	}
	
	
	public function reverse($path, $args=array()){
		
		$countArgs = count($args);
		// Count up $path var.
		$cnt = strlen($path);
		// Get method + controller from $path input.
		$e = explode('.', $path);
		
		list ($input_co, $input_me) = $e;
		
		// Get components.
		list ($controller, $method, $arguments) = $this->parseTo();
		
		// Check if this is dynamic controller / Method. In this case - some more resources will be used.
		$earlyComponentGet = false;
		if (substr($controller, 0, 1) == '@' || substr($method, 0, 1) == '@'){
			$fakeArgs = array();
			list($from, $vars) = $this->parseFrom();
			// Rewrite controller / method dynamic parts to real parts.
			foreach($vars as $set){
				if ($set['var'] == $controller){
					$controller = $input_co;
					$fakeArgs[substr($set['var'],1)] = $controller;
				}else if ($set['var'] == $method){
					$method = $input_me;
					$fakeArgs[substr($set['var'],1)] = $method;
				}
			}
				
			$earlyComponentGet = true;
		}
		
		
		// If We get example ways News.index , from $path , its all ok to now.
		// This check before $this->parseTo is 2x faster.
		if ($input_co == $controller && $input_me == $method){
				
				
				
			// If amount of args is equal.
			if ($countArgs == count($arguments)){
		
				// No arguments. No parsing is done.
				if ($countArgs == 0 && !$earlyComponentGet)return $this->from;
		
				$argsOk = true;
				// Check if all argument keys matches the real onces.
				foreach($args as $k => $v){
					if (!in_array('@'.$k, $arguments))$argsOk=false;
				}
		
				// Now ... lets get get a script smile - sorry in a good mood.
				// Lets make some check to add fake arguments.
				if ($earlyComponentGet){
					$args = array_merge($args, $fakeArgs);
				}
		
				// All done, lets reroute.
				if ($argsOk){
					// Parse from field.
					if (!$earlyComponentGet)list($from, $vars) = $this->parseFrom();
						
						
					// Explode into bits , separated by "/".
					$bits = explode('/', $from);
						
					// We can still stop this loop if the arguments does not match data type.
					$dataTypeError = false;
						
					$j = 0;
					// Loop each bit.
					foreach($bits as $k => $bit){
		
							
						// If we since start of regexp pattern.
						if (substr($bit, 0, 1) == '('){
							// Change the bit to the real value
							// Escape @ from vars and bind $j from $args
							$realval = $args[substr($vars[$j]['var'], 1)];
								
							// Check if datatype is correct.
							if (preg_match("/^$bit\$/", $realval)){
								$bits[$k] = $realval;
								// Datatype is not correct, lets go all the way to next check.
							}else{
								$dataTypeError = true;
							}
							$j++;
						}
					}
					if (!$dataTypeError){
						// Implode again.
						$ret =  implode('/', $bits);
						return $ret;
					}
				}
			}
		}
	}
	
	
	public function getFrom(){
		return $this->from;
	}
	public function getTo(){
		return $this->to;
	}
	public function getType(){
		return $this->type;
	}
}