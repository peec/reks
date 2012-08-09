<?php
namespace reks\view\twig;

class Extension extends AExtension{
	
	public function getName(){
		return "REKS Framework";
	}
	
	public function getFilters(){
		return array(
				'truncate' => new \Twig_Filter_Method($this, 'f_truncate'),
				);
	}
	
	public function getTokenParsers(){
		return array(
				new tags\Asset_Parser($this->view),
				new tags\Js_Parser($this->view)
				);
	}
	
	public function getFunctions(){
		return array(
				'scripts' => new \Twig_Function_Method($this, 'scripts', array('is_safe' => array('html'))),
				'asset' => new \Twig_Function_Method($this, 'asset', array('is_safe' => array('html'))),
				'lang' => new \Twig_Function_Method($this, 'lang', array('is_safe' => array('html'))),
				);
	}
	
	public function getGlobals(){
		return array(
				'url' => $this->view->url,
				'view' => $this->view
				);
	}
	
	
	/**
	 * Renders assets of type.
	 * @param string $type css / js
	 */
	public function scripts($type){
		if ($type == 'js' || $type == 'css'){
			return $this->view->scripts->$type->get();
		}elseif($type == 'head'){
			$raw = (string)$this->view->html->compile();
			return $raw;	
		}else{
			throw new \Exception("There is no type of script $type, please provide a valid type such as 'js' or 'css'.");
		}
	}
	
	public function asset($src){
		return $this->view->url->asset($src);
	}
	
	public function lang($var, $args = null){
		$var = $this->view->lang->$var;
		
		if ($args)return (string) $var->args($args);
		else return (string)$var;
	}
	
	public function f_truncate($string, $num, $after = ''){
		if (strlen($string) > $num){
			return substr($string, 0, $num) . $after;
		}else{
			return $string;
		}
	}
}