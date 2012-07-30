<?php
namespace model;


class SampleModel extends \reks\Model{
	
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