<?php
namespace model;

class NormalDB extends \reks\repo\PDORepo{
	
	public function createUser($user, $password){
		$this->db->insert('user', array('username' => $user, 'password' => $password));
	}
}