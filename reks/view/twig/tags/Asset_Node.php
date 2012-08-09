<?php
namespace reks\view\twig\tags;

use \reks\view\View;


class Asset_Node extends \Twig_Node{
	protected $view;
	public function __construct(View $view, $name, $value, $addCompile,  $lineno, $tag = null){
		parent::__construct(array(), array('value' => $value, 'name' => $name, 'addCompile' => $addCompile), $lineno, $tag);
		$this->view = $view;
	}

	public function compile(\Twig_Compiler $compiler){
		$addCompile = $this->getAttribute('addCompile') ? 'true' : 'false';
		
		$code = "\$context['view']->scripts->".$this->getAttribute('name')."->add('".$this->getAttribute('value')."', ".$addCompile.");\n";

		$compiler
		->addDebugInfo($this)
		->write($code)
		;
	}
}