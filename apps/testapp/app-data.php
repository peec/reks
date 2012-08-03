<?php
// Configure paths in your application.


$thisDir = dirname(__FILE__);
$appData = array(
		// Unique application name
		'app_name' => 'testapp',
		
		
		// Path to reks framework
		'base_reks' => "$thisDir/../../reks",
		
		// Path to the application folder.
		'app_path' => $thisDir,
		
		// Path to the public folder.
		'public_path' => "$thisDir/public"		
		
);