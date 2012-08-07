<?php
namespace reks\view\twig\tags;

use \reks\view\View;


class Asset_Node extends \Twig_Node{
	protected $view;
	public function __construct(View $view, $name, $value, $lineno, $tag = null){
		parent::__construct(array(), array('value' => $value, 'name' => $name), $lineno, $tag);
		$this->view = $view;
	}

	public function compile(\Twig_Compiler $compiler){
		$this->view->scripts->{$this->getAttribute('name')}->add($this->getAttribute('value')); // Add asset.
	}
}