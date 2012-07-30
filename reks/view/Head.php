<?php
/**
 * REKS framework is a very lightweight and small footprint PHP 5.3+ Framework.
 * It supports a limited set of features but fully MVC based and Objectoriented.
 * 
 * Copyright (c) 2012, REKS group ( Lars Martin Rørtveit, Andreas Elvatun, Petter Kjelkenes, Pål André Sundt )
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the REKS GROUP nor the
 *       names of its contributors may be used to endorse or promote products
 *       derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL "REKS Group" BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @license 3-clause BSD
 * @package reks\view
 * @author REKS group at Telemark University College
 */
namespace reks\view;
/**
 * Head section handler.
 * Useful to use in <head> section of a html document
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 *
 */
class Head{
	private $html = array();
	
	/**
	 * Javascript compiler api.
	 * @var reks\view\JsCompiler
	 */
	public $js;
	
	/**
	 * Javascript compiler api.
	 * @var reks\view\ScriptCompiler
	 */
	public $css;	
	
	/**
	 * 
	 * Enter description here ...
	 * @var reks\View
	 */
	private $view;
	
	/**
	 * SEO.
	 * Adds a canonical meta element to head section.
	 * 
	 * @param string $canonical The internal link. Example: news/1/hi-there
	 */
	public function canonical($canonical){
		$canonical = $this->view->stripXSS($canonical);
		$this->html['canonical'] = '<link rel="canonical" href="'.$this->view->url->fetchDomain() . $canonical.'" />';
		return $this;
	}
	/**
	 * Adds a description tag.
	 * @param string $description A string giving description of the current page. If its over 160 chars it will be truncated.
	 */
	public function description($description){
		$description = $this->view->stripXSS($description);
		$description = substr($description,0,160);
		$this->html['meta.description'] = '<meta name="description" content="'.$description.'" />';
		return $this;
	}
	/**
	 * Adds keyword(s) to the keywords meta tag.
	 * @param string $keywords Array of many keywords -  or string of keyword.
	 * @param boolean $clean true to clean the keyword array.
	 */
	public function keyword($keywords, $clean=false){
		$keywords = $this->view->stripXSS($keywords);
		if ($clean)$this->html['meta.keywords'] = array();
		if (is_array($keywords)){
			if (!isset($this->html['meta.keywords']))$this->html['meta.keywords'] = array();
			$this->html['meta.keywords'] = array_merge($this->html['meta.keywords'], $keywords);
		}else $this->html['meta.keywords'][] = $keywords;
		return $this;
	}
	
	/**
	 * SEO.
	 * 
	 * Used to add prev / next meta tags to head section.
	 * 
	 * If you have pagination, this should be used.
	 * @param int $pageAmount Amount of pages
	 * @param int $curPage Current page from 1 to amount of pages.
	 * @param int Used for url to next / prev page. Note sprintf is used! Example: $url news/1/title?page=%d OR news/1/title/page/%d
	 */
	public function pagination($pageAmount, $curPage, $url){
		$url = $this->view->stripXSS($url);
		if (!isset($this->html['pagination']))$this->html['pagination'] = '';
		if ($curPage != $pageAmount && $pageAmount != 1)$this->html['pagination'] .= '<link rel="next" href="'.$this->view->url->fetchDomain() .sprintf($url, $curPage+1).'" />';
		if ($curPage != 1)$this->html['pagination'] .= '<link rel="prev" href="'.$this->view->url->fetchDomain() . sprintf($url, $curPage-1).'" />';
		return $this;
	}
	/**
	 * SEO, best practice.
	 * Sets or add title to the page. 
	 * Typically you have a base controller that in the constructor uses this method - then 
	 * subcontrollers / methods uses this method again to make titles like
	 * <title>News - Mysite.com</title>
	 * and
	 * <title>Comments - News - Mysite.com</title>
	 * etc.
	 * @param string $title The title.
	 * @param boolean $prepend Default is true, it will prepend title. This is best practice SEO
	 */
	public function title($title, $prepend=true){
		$title = $this->view->stripXSS($title);
		if ($prepend){
			if (isset($this->html['title']))array_unshift($this->html['title'],$title);
			else $this->html['title'] = array($title);
		}
		else {
			$this->html['title'] = array($title);
		}
		return $this;
	}
	
	/**
	 * Returns the content of all tags that should be added with the methods used 
	 * in this class.
	 * Use like 
	 * <code>
	 * echo $view->head;
	 * </code>
	 */
	public function __toString(){
		$html = '';
		foreach($this->html as $type => $c){
			switch($type){
				case 'title':
					$html .= '<title>'.$this->getTitle().'</title>';
					break;
				case 'meta.keywords':
					$html .= '<meta name="keywords" content="'.implode(',',$c).'">';
					break;
				default:
					$html .= $c;
			}
		}
		return $html;
	}
	
	public function getTitle(){
		return implode(' - ', $this->html['title']);
	}
	
	/**
	 * Initializes the head class
	 * @param reks\View $view The view instance.
	 */
	public function __construct($view){
		$this->js = new JsCompiler($view, 'js');
		$this->css = new ScriptCompiler($view,'css');
		$this->view = $view;
	}
	
	
}

