<?php
namespace reks\view\twig\tags;

class Asset_Parser extends TagParser{
	public function parse(\Twig_Token $token){
		$lineno = $token->getLine();
		
		$scriptType = $this->parser->getStream()->expect(\Twig_Token::STRING_TYPE, array('css','js'))->getValue();
		$value = $this->parser->getStream()->expect(\Twig_Token::STRING_TYPE)->getValue();

		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);

		return new Asset_Node($this->view, $scriptType, $value, $lineno, $this->getTag());
	}

	public function getTag(){
		return 'asset';
	}
}