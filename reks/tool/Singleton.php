<?php
namespace reks\tool;

abstract class Singleton {
	/**
	 * Object.
	 * @var object
	 */
	protected static $factory;
	
	
	/**
	 * Returns the only instance of this singleton.
	 * @return object
	 */
	public static function getInstance(){
		if (!self::$factory) {
			$class = get_called_class();
			self::$factory = new $class();
		}
		return self::$factory;
	}
}
