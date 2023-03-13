<?php

include_once("../php/prepend.php");

echo "\n/*\n";

$result["error"] = 6;
$result["errorMsg"] = "";	
try {
	(new User)->addUserInfo($_REQUEST["name"], $_REQUEST["prefix"], $_REQUEST["surname"], $_REQUEST["group"], $_REQUEST["year"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";	
} catch(Exception $e) {
	$result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>