<?php
require __DIR__ . '/vendor/autoload.php';

require('../../../setting.php');
require('mysql_connect.php');

spl_autoload_register('tinkerAutoLoader');

function tinkerAutoLoader($className)
{
    $path = '/home/tinkerblake/www/tinkeronline/lib';

    include $path.$className.'.php';
}