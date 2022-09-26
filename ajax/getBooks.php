<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$result["data"] = (new Library)->getBooks($_REQUEST["search"],$_REQUEST["page"],$_REQUEST["limit"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 5;
	$result["errorMsg"] = $e->getMessage();
}	

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>