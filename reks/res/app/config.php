<?php
namespace reks\core; // Please don't change me.
$config=array(); // Do not change me!



/**
 * Application mode.
 * Production or Development.
 * 
 * Either: App::APP_MODE_DEV or App::APP_MODE_PROD
 * @var string
 */
$config['applicationMode'] = App::APP_MODE_DEV;

/**
 * The default language.
 * @var string
 */
$config['language'] = 'en';


////// DATABASE CONNECTIONS... Use what you like, Doctrine or simple PDO.

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
		// Main page.
		'/'		=>			'Main.index',
);
