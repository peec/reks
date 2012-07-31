<?php
namespace reks;

/**
 * Configuration array.
 * 
 * @var array
 */
$config=array(); // Do not change me!

/**
 * Application mode.
 * Either: 'dev' or 'prod'.
 * @var string
 */
$config['applicationMode'] = 'dev';

/**
 * 
 * Enter description here ...
 * @var string
 */
$config['language'] = 'en';



/**
 * PDO Database configuration ( NO ORM )
 * @var array
 */
$config['db'] = array(
	'dsn' => 'mysql:dbname=my_database;host=127.0.0.1',
	'username' => 'root',
	'password' => '',
	'driver_options' => array(
		\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"
	),
);


/**
 * Doctrine Database configuration to access entity manager.
 * use \model\em() function to access the enitity manager.
 * @var array
 */
$config['db_doctrine'] = array(
    'driver'   => 'pdo_mysql',
    'user'     => 'root',
    'password' => '',
    'dbname'   => 'foo',
);





/**
 * Logging directory. Must be writable.
 * @var string
 */
$config['log_dir'] = $app->APP_PATH . '/logs';

/**
 * What to log to the logs.
 * There are these types: Log::E_DEBUG, Log::E_ERROR, Log::E_INFO, Log::E_WARN
 * You can use many with the bitwise "|" separator.
 * @var int
 */
$config['log_level'] = Log::E_ERROR | Log::E_INFO | Log::E_WARN;

/**
 * Do we want to remove index.php from URL? 
 * In most cases YES, but we need external .htaccess file, not all servers
 * supports this.
 * @var boolean true if we want to remove index.php and false if not.
 */
$config['remove_scriptpath'] = false;


/*
 * Define routes 
 */


$config['route']['*']  = array(
	
		// Main page
		'/'		=>			'Main.helloWorld',
		'/hello/@to<[A-Za-z]*>'	=>	'Main.hello(@to)',

		
		// Link to sample unit testing.
		'/unittests' => 'tests/Tests.index',
		
		
		// Development tools
		
		// Useful for developing , not for production.
		'/dev/testroutes' => 		'/reks/tester/Routes.index',
		
		// Unit test the whole framework.
		'/dev/unittest-framework' =>  '/reks/tests/Main.index',
		
		
		
		// Error handlers
		'500'		=>		'Errors.internalServerError',
		'404'		=>		'Errors.pageNotFound',
		
		
);





// Register a module
$module->register(
		dirname(__FILE__).'/../mymodule/app-data.php', 
		Module::INHERIT_DATABASE );


// This is just example of DB load modules...
// Register a DB handler ( Note, you need database table , uncomment if you dont want it )
// CREATE TABLE `modules` (module_id int(11)  PRIMARY KEY AUTO_INCREMENT, module_name varchar(255), module_flags int(11), active boolean);
// Also create folder "modules".
$module->registerHandler(function(\reks\Module $module, \reks\RawModel $db){
	/*try{
		$db->openDB();
		$rows = $db->select("SELECT module_name, module_flags, active FROM modules");
		foreach($rows as $row){
			$module->register(
					$module->owner->app->APP_PATH . "/modules/{$row['module_name']}/app-data.php", 
					\reks\Module::INHERIT_DATABASE
			);
		}
	}catch(\PDOException $e){
		// Dont do anything.
	}*/
	
});

// This is just example of DB load config ...
// - This is also using the table from the above example.
// Register a DB handler ( Note, you need database table , uncomment if you dont want it )
// CREATE TABLE `configuration` (module_id int(11), conf_key varchar(255), conf_val TEXT, PRIMARY KEY(module_id, conf_key), FOREIGN KEY (module_id) REFERENCES modules(module_id));
$configHandler = function(\reks\RawModel $db) use ($config){
	/*try{
		$db->openDB();
		$rows = $db->select("
				SELECT
					conf_key, conf_val, module_name
				FROM configuration
				INNER JOIN modules ON modules.module_id = configuration.module_id
				WHERE module_name = ?
				", array($db->app->APP_NAME));
		foreach($rows as $row){
			$config[$row['conf_key']] = $row['conf_val'];
		}
	}catch(\PDOException $e){
		// Dont do anything.
	}
	return $config;*/
	return $config;
};
