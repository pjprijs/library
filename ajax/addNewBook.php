<?php

include_once("../php/prepend.php");

echo "\n/*\n";

$result["error"] = 6;
$result["errorMsg"] = "SQL error";
try {
	$book = new Book();
	if($book->addNewBook($_REQUEST["data"])){
		$result["data"]["bookId"] = $book->getId();
		$result["data"]["title"] = $book->getFulltitle();
		$result["error"] = 0;
		$result["errorMsg"] = "";
	}
} catch(Exception $e) {
	$result["errorMsg"] = "SQL error - " . $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>