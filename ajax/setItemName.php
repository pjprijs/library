<?php

include_once("../php/prepend.php");

echo "\n/*\n";

$result["error"] = 6;
$result["errorMsg"] = "";	
$result["data"]["item"] = $_REQUEST["table"];
$result["data"]["id"] = $_REQUEST["value"];
$result["data"]["name"] = $_REQUEST["name"];
try {
	(new Library)->setItemName($_REQUEST["table"], $_REQUEST["value"], $_REQUEST["name"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";	
} catch(Exception $e) {
	$result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>