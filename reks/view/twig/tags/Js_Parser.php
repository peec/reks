<?php
namespace reks\view\twig\tags;

/**
 * Adds a javascript ( but does not output it until {{scripts('js')}} is used.
 * <pre>
 * {% js %}
 *  <script>
 *   Javascript here.
 *  </script>
 * {% endjs %}
 * </pre>
 */
class Js_Parser extends TagParser{
	/**
	 * Parses a token and returns a node.
	 *
	 * @param Twig_Token $token A Twig_Token instance
	 *
	 * @return Twig_NodeInterface A Twig_NodeInterface instance
	 */
	public function parse(\Twig_Token $token){
		$lineno = $token->getLine();
		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
		$body = $this->parser->subparse(array($this, 'decideJsEnd'), true);
		$this->parser->getStream()->expect(\Twig_Token::BLOCK_END_TYPE);
		return new Js_Node($this->view, $body, $lineno, $this->getTag());
	}

	public function decideJsEnd(\Twig_Token $token){
		return $token->test(array('endjs'));
	}

	/**
	 * Gets the tag name associated with this token parser.
	 *
	 * @return string The tag name
	 */
	public function getTag()
	{
		return 'js';
	}
}
