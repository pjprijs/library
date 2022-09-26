<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$book = new Book();
	$book->init((int)$_REQUEST["book"]);
	$book->add();
	$result["data"]["title"] = $book->getFulltitle();
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 5;
	$result["errorMsg"] = $e->getMessage();
}	

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
?>