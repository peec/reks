<?php
namespace reks\view\twig\tags;

abstract class TagParser extends \Twig_TokenParser{
	/**
	 * @var reks\view\View
	 */
	protected $view;
	
	public function __construct(\reks\view\View $view){
		$this->view = $view;
	}
	
}