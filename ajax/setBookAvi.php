<?php

include_once("../php/prepend.php");

echo "\n/*\n";

$result["error"] = 6;
$result["errorMsg"] = "SQL error";
try {
	$book = new Book();
	if($book->init((int) $_REQUEST["bookId"], false)){
		$book->setAvi($_REQUEST["avi"]);
		$result["error"] = 0;
		$result["errorMsg"] = "";
	}	
} catch(Exception $e) {
	$result["errorMsg"] = "SQL error - " . $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>