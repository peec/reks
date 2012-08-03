<?php
namespace reks;

/**
 * Holds information about reks framework.
 * @author peec
 *
 */
class ReksData{
	
	/**
	 * @var string The version number of reks
	 */
	const VERSION = '1.3.3';
	
	/**
	 * @var string The name of this framework.
	 */
	const NAME = 'REKS Framework';
	
	
	/**
	 * Checks if version is stable release.
	 * @return boolean
	 */
	static public function isStable(){
		$last = substr(self::VERSION, -1);
		if ($last == 'a' || $last == 'b')return false;
		
		return true;
	}
	
	
	/**
	 * Gets the path to the reks folder.
	 * @return string
	 */
	static public function getReksPath(){
		return dirname(__FILE__);
	}
	
}