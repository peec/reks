<?php
namespace reks;

/**
 * Useful when you create a interface to your models that communicates with the database.
 * 
 * @author peec
 *
 */
abstract class DoctrineModel extends Model{
	
	/**
	 * 
	 * @var Doctrine\ORM\EntityManager
	 */
	public $em;
	
	

	
	public function openDB(){
		$this->em = $this->model->em();
	}
	public function closeDB(){
		
	}
	
	
	/* Custom methods for repository!*/
	
	
}