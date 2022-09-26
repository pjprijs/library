<?php

include_once("../php/prepend.php");

echo "\n/*\n";

$result["error"] = 6;
$result["errorMsg"] = "";	
$result["data"] = $_REQUEST["table"];
try {
	(new Library)->setItemOrder($_REQUEST["table"], $_REQUEST["data"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";	
} catch(Exception $e) {
	$result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>