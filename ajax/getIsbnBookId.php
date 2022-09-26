<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$result["data"]["isbn"]	= $_REQUEST["isbn"];
	$result["data"]["bookId"] = 0;
	$result["data"]["amount"] = 0;
	$book = new Book;
	if($book->initIsbn($_REQUEST["isbn"], false)) {
		$result["data"]["bookId"] = $book->getId();
		$result["data"]["amount"] = $book->getAmount();
		$result["data"]["title"] = $book->getTitle();
	}
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 5;
	$result["errorMsg"] = $e->getMessage();
}	

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>