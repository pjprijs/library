<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
    $book = (new Book)->init((int)$_REQUEST["book"], false);
    $book->delete($_REQUEST["book"]);
    $result["error"] = 0;
    $result["errorMsg"] = "";    
} catch(Exception $e) {
    $result["error"] = 13;
    $result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>