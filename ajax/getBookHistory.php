<?php

include_once("../php/prepend.php");

echo "\n/*\n";

$book = (new Book)->init((int)$_REQUEST["bookId"]);
try {
	$result["data"] = $book->getLoanHistory($_REQUEST["page"],$_REQUEST["limit"]);
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 5;
	$result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>