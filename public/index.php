<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
spl_autoload_register(function ($classname){
	include(__DIR__ . "/../private/$classname.php");
});
$controller = new Controller($_GET);
$controller->run();
?>
