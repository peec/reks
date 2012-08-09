<?php
namespace reks\view\twig;

abstract class AExtension extends \Twig_Extension{
	
	/**
	 *
	 * @var reks\view\View
	 */
	protected $view;
	
	public function __construct(&$view){
		$this->view = &$view;
	}
}
