<?php
namespace reks;

// We don't need REKS mandatory configuration such as log dir etc... , these are 
// actually inherited from the parent application.
// But we CAN override them.


/*
 * Define routes 
 */


$config['route']['*']  = array(
	
		// Main page
		'/moduletest'		=>			'MyModule.test',
		
		
		
);





