<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	(new Library)->returnLoanedBook($_REQUEST["loanId"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 12;
	$result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>