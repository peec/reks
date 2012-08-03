<?php
namespace model;

/**
 * This uses no db.. Hence we just extend a normal Repo class.
 * @author peec
 *
 */
class SampleModel extends \reks\repo\Repo{
	
	public function hello(){
		return 'Hello';
	}
	
	public function throwException($throw){
		if ($throw)
			throw new \Exception("Ugh, some error here sorry.");
		else 
			return 'Happy?';
	}
	
}