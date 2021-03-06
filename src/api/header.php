<?php
// composer auto loader
require __DIR__ . '/vendor/autoload.php';

// site specific settings
$path = "/home/tinkerblake";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require('setting.php');

// establish a mysql connectoion 
$path = "/home/tinkerblake/www/tinkeronline/api";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
set_include_path(get_include_path() . PATH_SEPARATOR . $path."/lib");
require('mysql_connect.php');

// define tinkercamp auto loader
spl_autoload_register('tinkerAutoLoader');

function tinkerAutoLoader($className)
{
    $path = '/home/tinkerblake/www/tinkeronline/api/lib/';

    include $path.$className.'.php';
}