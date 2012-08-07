<?php
namespace reks\view\twig;

class Extension extends \Twig_Extension{

	
	/**
	 * 
	 * @var reks\view\View
	 */
	private $view;
	
	public function __construct($view){
		$this->view = $view;
	}
	
	public function getName(){
		return "REKS Framework";
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
			return $this->view->html->__toString();	
		}else throw new \Exception("There is no type of script $type, please provide a valid type such as 'js' or 'css'.");
	}
	
	public function asset($src){
		return $this->view->url->asset($src);
	}
	
	public function lang($var){
		return $this->view->lang->$var;
	}
}