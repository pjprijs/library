<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$book = (new Book)->init((int)$_REQUEST["bookId"]);
	$result["data"]["loaned"] = $book->getLoanedAmount();
	$result["data"]["total"] = $book->getAmount();
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 5;
	$result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>