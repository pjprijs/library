<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$book = new Book();
	$book->init((int)$_REQUEST["book"]);
	$amount = (int)(isset($_REQUEST["amount"]) ? $_REQUEST["amount"] : "1");
	$book->addAmount($amount);
	$result["data"]["title"] = $book->getFulltitle();
	$result["data"]["amount"] = $book->getAmount();
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 5;
	$result["errorMsg"] = $e->getMessage();
}	

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
?>