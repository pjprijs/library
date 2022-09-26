<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$result["data"] = (new Library)->setMissingBook($_REQUEST["loanId"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 15;
	$result["errorMsg"] = "SQL error";
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>