<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$result["data"] = (new Library)->getAuthors($_REQUEST["search"],$_REQUEST["page"],$_REQUEST["limit"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";
	$_SESSION["lib"]["obj"] = serialize($lib);
} catch(Exception $e) {
	$result["error"] = 5;
	$result["errorMsg"] = "SQL error";
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>