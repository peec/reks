<?php
namespace reks\view\script;

use compilers\JsCompiler;
use compilers\CssCompiler;


class Scripts{
	
	/**
	 * @var reks\view\script\compilers\JsCompiler
	 */
	public $js;
	
	/**
	 * @var reks\view\script\compilers\CssCompiler
	 */
	public $css;
	
	public function __construct(\reks\view\View $view){
		$this->js = new compilers\JsCompiler($view);
		$this->css = new compilers\CssCompiler($view);
		
		$this->js->setup();
		$this->css->setup();
		
	}
	
}