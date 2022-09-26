<?php

error_reporting(E_ALL);
ini_set('display_errors', 'On');

include_once("config.php");

date_default_timezone_set("Europe/Amsterdam");
setlocale(LC_TIME, 'nl_NL');
session_start();

$mysqli = new mysqli($server, $login, $pwd, $name);
$mysqli->query("SET lc_time_names = 'nl_NL'");

spl_autoload_register('autoloader');

function autoloader($classname) {
    include_once '../php/' . $classname . '.php';
}

$revFile = strrev(__FILE__);
$currentFile = strrev(substr($revFile, 0, strpos($revFile, '/')));
$revFile = strrev($_SERVER['PHP_SELF']);
$originalFile = strrev(substr($revFile, 0, strpos($revFile, '/')));
if(isset($_REQUEST["source"]) && $currentFile != $originalFile) {
    highlight_file($originalFile);
    exit();
} 

?>
