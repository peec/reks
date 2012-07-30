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
 * @package reks
 * @author REKS group at Telemark University College
 */
namespace reks;


/**
 * A general implementation of a REST controller.
 * 
 * The rest controller got own methods for rendering.
 * 
 * 
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 */
abstract class RestController extends Controller{
	
	
	
	
	/**
	 * Renders array result to XML and echo the output.
	 * 
	 * Sample usage:
	 * <code>
	 * $allNews = $this->model('News')->getNews();
	 * $this->render($allNews, 201);
	 * </code>
	 * 
	 * @param array $data  Array that should be outputed as xml. Can be multi-dimensional.
	 * @param int $httpCode A valid HTTP code.
	 */
	protected function renderXML(array $data=array(), $httpCode=201){
		$ret = $this->buildResponseArray($data, $httpCode, array('returnType' => 'xml'));
		echo '<rest>'.$this->arrayToXML($ret).'</rest>';
	}
	
	/**
	 * Renders array result to JSON and echo the output.
	 * @param array $data Array that should be outputed as JSON. Can be multi-dimensional.
	 * @param int $httpCode A valid HTTP code.
	 */
	protected function renderJSON(array $data, $httpCode){
		echo json_encode($this->buildResponseArray($data, $httpCode, array('returnType' => 'json')));
	}
	
	/**
	 * Tries to find what client is requesting, if its JSON we render JSON - if its XML, we render XML.
	 * 
	 * @param array $data The data to return to the client excluding the standard elements.
	 * @param int $httpCode A valid HTTP code.
	 */
	protected function render(array $data, $httpCode){
		$type = (strpos($_SERVER['HTTP_ACCEPT'], 'json')) ? 'json' : 'xml';
		switch($type){
			case 'json': $this->renderJSON($data,$httpCode); break;
			case 'xml': $this->renderXML($data,$httpCode); break;
		}
		
	}
	
	/**
	 * Builds a new response array to be parsed to render methods.
	 * @param array $data Array of data, this data can vary depending on model output.
	 * @param int $httpCode Http code to send.
	 */
	private function buildResponseArray(array $data, $httpCode, $additionalRequestParams=null){
		$httpReturn = $this->sendStatus($httpCode);
		$ret = array(
			'request' => array(
				'method' => $this->request->getMethod(),
				'uri' => $_SERVER['REQUEST_URI']
			),
			'response' => array(
				'httpCode' => $httpReturn,
				'result' => $data
			)
		);
		if ($additionalRequestParams){
			foreach($additionalRequestParams as $k => $v){
				$ret['request'][$k] = $v;
			}
		}
		return $ret;
	}
	
	/**
	 * Parses array to XML.
	 * 
	 * @param array $data Array of data.
	 * @return string A XML string.
	 */
	private function arrayToXML(array $data){
		$xml = '';
		
		foreach($data as $key => $value){
			if (is_numeric($key)) $xml .= "<item>";
			else $xml .= "<{$key}>";	
			if(is_array($value)){
				$xml .= "\n".$this->arrayToXML($value);
			}else{
				$xml .= "{$value}";
			}
			if (is_numeric($key)) $xml .= "</item>\n";
			else $xml .= "</{$key}>\n";
		}
		return $xml;
	}
	
}