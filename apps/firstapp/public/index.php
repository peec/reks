<?php
// app-data.php file location
$app_data_path = dirname(__FILE__) . '/../app-data.php'; 


// -- Do not edit below --
session_start();
include $app_data_path;
include $appData['base_reks'] . '/reks/App.php';
$app = new \reks\App($appData);
$app->main();