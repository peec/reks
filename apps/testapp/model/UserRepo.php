<?php
namespace model;

/**
 * Example of a repository for the model User.
 * Here we use the database.
 * @author peec
 *
 */
class UserRepo extends \reks\repo\DoctrineRepo{
	
	public function createUser($user, $pass){
		$user = new User($pass, $user);
		$this->em->persist($user);
		$this->em->flush();
	}
	
}