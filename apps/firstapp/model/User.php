<?php 
namespace model;

/**
 * Example of user entity.
 * @Entity @Table(name="users")
 */
class User extends \reks\DoctrineModel{

	/** @Id @Column(type="integer") @GeneratedValue **/
	protected $id;
	/** @Column(type="string") **/
	protected $username;
	/** @Column(type="string") **/
	protected $password;
	
	
	public function createUser($user, $pass){
		$user = new User();
		$user->password = $pass;
		$user->username = $user;
		$this->em->persist($user);
	}
	
}