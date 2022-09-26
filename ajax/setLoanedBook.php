<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	(new Library)->setLoanedBook($_REQUEST["userId"],$_REQUEST["bookId"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 11;
	$result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>