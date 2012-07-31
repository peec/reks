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
	
	
	public function __construct(ModelWrapper $modelwrapper){
		parent::__construct($modelwrapper);
	}
	
	public function openDB(){
		$this->em = $modelwrapper->em();
	}
	public function closeDB(){
		
	}
	
	
	/* Custom methods for repository!*/
	
	
}