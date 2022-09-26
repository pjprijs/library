<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$result["data"] = (new Library)->setCombineBook($_REQUEST["mainBookId"], $_REQUEST["combineBookArray"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 6;
	$result["errorMsg"] = "SQL error";
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>