<?php 
namespace model;

/**
 * Example of user entity. ( Doctrine 2 )
 * 
 * Here we don't use any database for best practices!
 * It's a clean object, we use a repository to deal with the database, see "UserRepo" class.
 * @Entity @Table(name="users")
 */
class User{

	/** @Id @Column(type="integer") @GeneratedValue **/
	protected $id;
	/** @Column(type="string") **/
	protected $username;
	/** @Column(type="string") **/
	protected $password;
	
	
	public function __construct($user, $pass){
		$this->username = $user;
		$this->password = $pass;
	}
	
}