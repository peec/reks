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
 * Database wrapper around the PDO class.
 * 
 * You would normally want to just do simple selects and inserts, this wrapper makes it easier
 * to update, insert and select data from the default PDO class.
 * 
 * 
 * @author REKS group at Telemark University College
 * @version 1.0
 *
 */
class DB extends \PDO{
	/**
	 * Constructs a new PDO object.
	 * 
	 * @see http://php.net/pdo
	 * @param string $dsn
	 * @param string $username
	 * @param string $passwd
	 * @param array $options
	 */
	public function __construct($dsn, $username, $passwd, array $options=array()){
		parent::__construct($dsn, $username, $passwd, $options);
	}
	
	/**
	 * Use to select many rows at once.
	 * 
	 * Getting all rows from the news table
	 * <code>
	 * 	$rows = $this->db->select('SELECT * FROM news');
	 * </code>
	 * 
	 * @param string $sql SQL code.
	 * @param array $parameters Array of parameters, example: array('reks', 423)
	 */
	public function select($sql, array $parameters = array()){
		$q = $this->prepare($sql);
		if (!$q->execute($parameters)) return array();
		return $q->fetchAll(\PDO::FETCH_ASSOC);
		
	}
	
	
	
	/**
	 * Selects one row from the database table.
	 * 
	 * Sample to get news item from news where id = 523
	 * <code>
	 * 	$this->db->selectRow("SELECT * FROM news WHERE id=?", array(523));
	 * </code>
	 * @param string $sql SQL String
 	 * @param array $parameters Array of parameters.
	 */
	public function selectRow($sql, array $parameters = array()){
		$q = $this->prepare($sql);
		if (!$q->execute($parameters)) return null;
		return $q->fetch(\PDO::FETCH_ASSOC);		
	}
	
	/**
	 * Selects one column from the database.
	 * 
	 * Sample to get the title of the news item from news where id = 523
	 * <code>
	 * 	$this->db->selectOne("SELECT title FROM news WHERE id=?", array(523));
	 * </code>
	 * @param string $sql SQL String
 	 * @param array $parameters Array of parameters.
	 */
	public function selectOne($sql, array $parameters = array()){
		$q = $this->prepare($sql);
		if (!$q->execute($parameters)) return null;
		return $q->fetchColumn();	
	}
	
	/**
	 * Inserts a new item to the database.
	 * 
	 * Sample usage:
	 * <code>
	 * 	$id = $this->db->insert('news', array(
	 * 		'title' => 'Hello world', 
	 * 		'body' => ' some content...'
	 * 	));
	 * </code>
	 * @param string $table Table name
	 * @param array $columnValueArray array of columnname => value.
	 * @return int The ID of the row, null if error.
	 */
	public function insert($table, array $columnValueArray){
		
		$columns = '';
		$params = '';
		foreach($columnValueArray as $col => $val){
			$columns .= "`{$col}`,";
			$params .= '?,';
		}
		$columns = substr($columns, 0, -1);
		$params = substr($params, 0, -1);
		
		$sql = "INSERT INTO `$table` ($columns) VALUES ({$params})";
		
		$q = $this->prepare($sql);
		if ($q->execute(array_values($columnValueArray)))
			return $this->lastInsertId();
		else return null;
	}
	
	/**
	 * Runs a prepared query with secure arguments.
	 * Mostly used to run: INSERT, DELETE and UPDATE statements.
	 * SQL Injection safe.
	 * 
	 * Example:
	 * <code>
	 * 	$this->db->pQuery("UPDATE news SET title=? WHERE id=?", array($title, $id));
	 *  $this->db->pQuery("DELETE FROM news WHERE id=?", array($someId));
	 * </code>
	 * @param string $sql SQL code
	 * @param array $parameters Array of parameters.
	 */
	public function pQuery($sql, array $parameters){
		$q = $this->prepare($sql);
		return $q->execute($parameters);
	}
	
	
}