<?php
// Configure paths in your application.


$thisDir = dirname(__FILE__);
$appData = array(
		// Unique application name
		'app_name' => 'firstapp',
		
		
		// Path to where the "reks" folder is located
		// ( Note not into the folder but where "reks" folder is located. ).
		'base_reks' => "$thisDir/../..",
		
		// Path to the application folder.
		'app_path' => $thisDir,
		
		// Path to the public folder.
		'public_path' => "$thisDir/public"		
		
);