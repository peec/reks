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
 * @package reks\form
 * @author REKS group at Telemark University College
 */
namespace reks\form;

/**
 * A Form object can be used to generate one specific form.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 */
class Form extends Element{
	/**
	 * Array of hidden elements
	 * @var array
	 */
	protected $hiddenElements=array();
	/**
	 * If we want to remember data we can set this to true.
	 * @var boolean Default is false.
	 */
	public $rememberData = false;
	/**
	 * Input class used
	 * @var reks\Input
	 */
	public $input = null;
	
	/**
	 * Constructs a new form
	 * @param string $formId Form identity
	 * @param string $targetUrl Target url
	 * @param string $method Method used ( eg. post )
	 * @param reks\Input $input Input class used.
	 */
	public function __construct($formId, $targetUrl, $method, $input){
		$this->attr('id', $formId);
		$this->attr('action', $targetUrl);
		$this->attr('method', $method);
		$this->input = $input;
		
	}
	/**
	 * Use this to set form to remember data if we get data.
	 */
	public function rememberData(){
		$this->rememberData = true;
		return $this;
	}
	
	/**
	 * Adds hidden elements and closes the form with </form>.
	 */
	public function close(){
		$str = '';
		foreach($this->hiddenElements as $ele){
			$str .= $ele->__toString();
		}
		
		return $str.'</form>';
	}
	
	
	
	/**
	 * Adds a <input .. element.
	 * 
	 * @param string $type Type of the input eg. checkbox, text, password, radio etc.
	 * @param string $name Name of the input eg. title
	 * @return reks\form\Input
	 */
	public function input($type, $name=null, $val=null){
		return new Input($this, $type, $name, $val);
	}
	/**
	 * Adds a <select .. element.
	 * @param string $name Name of the select.
	 * @param array $list Array of key => value pairs or just NULL and use the Select class to add elements to the select.
	 * @return reks\form\Select
	 */
	public function select($name, $list = null){
		return new Select($this,$name, $list);
	}
	
	/**
	 * Adds a <textarea .. element
	 * @param string $name Name of the textarea
	 * @param int $cols Amount of columns, default 45.
	 * @param int $rows Amount of rows, default 7.
	 * @return reks\form\Textarea
	 */
	public function textarea($name, $cols=45, $rows=7){
		return new Textarea($this,$name, $cols, $rows);
	}
	
	
	/**
	 * Adds a new hidden element.
	 * @param string $name Name of the hidden field.
	 * @param mixed $val Value of the hidden field.
	 */
	public function addHidden($name, $val){
		$this->hiddenElements[] =  new Input($this,'hidden', $name,$val);
		return $this;
	}
	
	/**
	 * Returns the <form> opening tag including all attributes.
	 * @see reks\form.Element::__toString()
	 */
	public function __toString(){
		return "<form".parent::__toString().">";	
	}
	
	
}

/**
 * Input class reflects the <input ...> element.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 */
class Input extends FormElement{
	/**
	 * Constructs a new input element
	 * @param reks\form\Form $form Form instance.
	 * @param string $type Type of the input
	 * @param string $name Name of the input
	 * @param string $val Value of the input
	 */
	public function __construct(&$form, $type, $name=null, $val=null){
		parent::__construct($form, $name);
		$this->attr('type', $type);
		if($name!==null)$this->attr('name', $name);
		if($val!==null)$this->attr('value',$val);
	}
	
	/**
	 * Sets the value of the input element.
	 * @param string $val Value
	 * @return reks\form\Input
	 */
	public function val($val){
		$this->attr('value',$val);
		return $this;
	}
	
	/**
	 * Returns the <input> element
	 * @see reks\form.Element::__toString()
	 */
	public function __toString(){
		// First set real value
		$v = $this->realValue($this->name, $this->getAttr('value'));
		if ($v)$this->attr('value', $v);
		return "<input".parent::__toString()." />";	
	}
}


/**
 * Select class reflects the <select> element.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 */
class Select extends FormElement{
	/**
	 * List of option instances.
	 * @var array
	 */
	private $optionList = array();
	
	/**
	 * Constructs the new Select element
	 * @param reks\form\Form $form Form instance.
	 * @param string $name Name of the select element.
	 * @param array $list Null or array of key => value pairs.
	 */
	public function __construct(&$form,$name, $list=null){
		parent::__construct($form, $name);
		if ($list !== null){
			foreach($list as $k => $v){
				$this->optionList[] = new Option($k, $v);
			}
		}
	}
	
	/**
	 * Adds a new option to the select box.
	 * 
	 * @param string $value Value of the option ( <option VALUE=""> ..
	 * @param string $name Name / label of the option. <option ..>NAME</option>
	 * @param boolean $selected Is this selected ? Default is false.
	 * @return reks\form\Select
	 */
	public function option($value, $name, $selected=false){
		$this->optionList[] = new Option($value, $name, $selected);
		return $this;
	}
	
	/**
	 * In some cases you would want a numer range in select box,
	 * this is a easy way of doing just that
	 * Example:
	 * <code>
	 * $this->setRange(1,31,1)
	 * </code>
	 * @param int $from From value
	 * @param int $to To value
	 * @param int $incrementBy Incremented by , default = 1.
	 */
	public function setRange($from, $to, $incrementBy=1){
		for($i = $from; $i < $to; $i += $incrementBy){
			$this->optionList[] = new Option($i,$i);
		}
		return $this;
	}
	
	/**
	 * Sets the value selected.
	 * Most easy way to set the selected value.
	 * @param string $value Sets the value selected.
	 * @return reks\form\Select
	 */
	public function valueSelected($value){
		foreach($this->optionList as $o){
			if ($o->getAttr('selected') && $o->getAttr('value') != $value)$o->removeAttr('selected');
			elseif($o->getAttr('value') == $value){
				$o->attr('selected','selected');
			}
		} 
		return $this;
	}
	/**
	 * Gets the selected value.
	 * Returns null if no.
	 */
	public function getSelectedValue(){
		foreach($this->optionList as $o){
			if ($o->getAttr('selected'))$o->getAttr('value');
		} 
		return null;
	}
	/**
	 * Generates the option list.
	 */
	protected function generateOptionList(){
		$str = '';
		foreach($this->optionList as $k => $val){
			if ($val instanceof Option)$str .= $val->__toString();
		}
		return $str;
	}
	/**
	 * Returns the full select elements including option elements.
	 * @see reks\form.Element::__toString()
	 */
	public function __toString(){
		$real = $this->realValue($this->name, null);
		if ($real)$this->valueSelected($real);
		return "<select".parent::__toString().">{$this->generateOptionList()}</select>";	
	}
}

/**
 * Option class references the <option> element.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 */
class Option extends Element{
	/**
	 * Name of the option
	 * @var string
	 */
	private $name;
	
	/**
	 * Constructs a new option element.
	 * @param string $value Value of the option
	 * @param string $name Label of the option.
	 * @param boolean $selected Is this selected ? default is false.
	 */
	public function __construct($value,$name, $selected=false){
		$this->attr('value', $value);
		$this->name = $name;
		if ($selected)$this->attr('selected','selected');
	}
	/**
	 * Returns the full <option> element.
	 * @see reks\form.Element::__toString()
	 */
	public function __toString(){
		return "<option".parent::__toString().">{$this->name}</option>";	
	}
}

/**
 * Textarea class reflects the <textarea ...> element.
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 */
class Textarea extends FormElement{
	/**
	 * Value of the element.
	 * @var string
	 */
	private $value;
	
	/**
	 * Creates a new textarea element
	 * @param reks\form\Form $form Form instance
	 * @param string $name Name of the textarea 
	 * @param int $cols Columns
	 * @param int $rows Rows
	 */
	public function __construct(&$form,$name, $cols, $rows){
		parent::__construct($form, $name);
		
		$this->attr('cols', $cols);
		$this->attr('rows', $rows);
	}
	/**
	 * Sets the value of the textarea element.
	 * @param string $val Value
	 */
	public function val($val){
		$this->value=$val;
		return $this;
	}
	
	/**
	 * Returns the full <textarea> element.
	 * @see reks\form.Element::__toString()
	 */
	public function __toString(){
		$this->value = $this->realValue($this->name, $this->value);
		return "<textarea".parent::__toString().">{$this->value}</textarea>";	
	}
}
