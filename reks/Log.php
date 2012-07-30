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
 * Static logger class.
 * Stack will be written to file once destructor is initialized by php.
 * @author REKS group at Telemark University College
 */
class Log{
	
	/**
	 * Warning message
	 * @var int
	 */
	const E_WARN = 1;
	/**
	 * Debug message
	 * @var int
	 */
	const E_DEBUG = 2;
	/**
	 * Info message
	 * @var int
	 */
	const E_INFO = 4;
	/**
	 * Error message
	 * @var int
	 */
	const E_ERROR = 8;
	
	/**
	 * Keeps flags for what too log.
	 * @var array
	 */
	private $logFlags;
	/**
	 * Stack of log entries
	 * @var array
	 */
	private $stack = array();
	/**
	 * Log directory.
	 * @var string
	 */
	private $logLocation;
	
	/**
	 * Creates a new logger instance.
	 * @param int $logFlags Flags of what to log.
	 * @param string $logLocation Log location ( must be a directory )
	 */
	public function __construct($logFlags, $logLocation){
		$this->logFlags = $logFlags;
		$this->logLocation = $logLocation;
	}
	
	/**
	 * Gets what method / controller that called the method.
	 */
	protected function getTraceString(){
		$trace = debug_backtrace();
		return "{$trace[2]['class']}::{$trace[2]['function']}";
	}
	
	/**
	 * Logs a debug message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function debug($msg, $flags=null){
		if ($this->logFlags & self::E_DEBUG)$this->stack[] = array('DEBUG', time(), $msg, $flags, $this->getTraceString());
	}
	
	/**
	 * Logs a warning message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function warn($msg, $flags=null){
		if ($this->logFlags & self::E_WARN)$this->stack[] = array('WARNING', time(), $msg, $flags, $this->getTraceString());
	}
	/**
	 * Logs a error message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function error($msg, $flags=null){
		if ($this->logFlags & self::E_ERROR)$this->stack[] = array('ERROR', time(), $msg, $flags, $this->getTraceString());
	}
	/**
	 * Logs a info message.
	 * @param string $msg The message to log.
	 * @param int $flags Additional flags.
	 */
	public function info($msg, $flags=null){
		if ($this->logFlags & self::E_INFO)$this->stack[] = array('INFO', time(), $msg, $flags, $this->getTraceString());
	}
	
	
	/**
	 * Builds a message string from a stack entry.
	 * @param array $data array of elements in the stack
	 */
	protected function buildMsg($data){
		$msg =  "[{$data[0]}] ".'['.date('r', $data[1]).'] '."[{$data[4]}]".$data[2]."\n";
		return $msg;
	}
	/**
	 * Writes the stack to file and cleans the stack.
	 */
	protected function writeStack(){
		$s = '';
		foreach($this->stack as $d){
			$s .= $this->buildMsg($d);
		}
		$fp = $this->logLocation . DIRECTORY_SEPARATOR . date('D-d-m-Y', time()) . '.txt';
		if ($s)file_put_contents($fp,$s, FILE_APPEND);
		$this->stack = array();
	}
	
	/**
	 * Writes the log stack to file.
	 */
	public function __destruct(){
		$this->writeStack();
	}
	
}