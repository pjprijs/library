<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$book = new Book();
	$result["data"]["bookId"] = false;
	if($book->createFromIsbn($_REQUEST["isbn"])) {
		$result["data"]["title"] = $book->getFulltitle();
		$result["data"]["bookId"] = $book->getId();
	}
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 0;
	$result["errorMsg"] = $e->getMessage();
}	

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>