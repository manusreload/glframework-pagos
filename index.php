<?php
/**
 * Created by PhpStorm.
 * User: manus
 * Date: 30/11/16
 * Time: 19:14
 */

if (!file_exists("vendor/autoload.php")) {
    $path = realpath(".");
    die('I can\'t find \'vendor/autoload.php\' in the current path: ' . $path . '. Ensure that is valid and/or execute 
    \'composer install\' to install the framework!');
}
require_once "vendor/autoload.php";
\GLFramework\Bootstrap::start(".");
