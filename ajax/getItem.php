<?php

include_once("../php/prepend.php");

echo "\n/*\n";

$result["data"]["item"] = $_REQUEST["table"];
$result["error"] = 6;
$result["errorMsg"] = "";	
try {
	$result["data"]["data"] = (new Library)->getItems($_REQUEST["table"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";	
} catch(Exception $e) {
	$result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>