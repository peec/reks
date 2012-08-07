<?php
namespace reks\view\twig\tags;

use \reks\view\View;

class Js_Node extends \Twig_Node{
	protected $view;
	public function __construct(View $view = null, \Twig_NodeInterface $body, $lineno, $tag = null)
	{
		parent::__construct(array('body' => $body), array(), $lineno, $tag);
		$this->view = $view;
	}

	/**
	 * Compiles the node to PHP.
	 *
	 * @param Twig_Compiler A Twig_Compiler instance
	 */
	public function compile(\Twig_Compiler $compiler){
		
		$compiler
		->addDebugInfo($this)
		->write("\$context['view']->scripts->js->start();\n")
		->subcompile($this->getNode('body'))
		->write("\$context['view']->scripts->js->end();\n")
		;
		
		
		
	}
}
