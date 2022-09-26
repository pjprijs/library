<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	if($user = (new Library)->getUserById($_REQUEST["userId"])) {
		$result["data"] = $user->toArray();
	}
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 5;
	$result["errorMsg"] = "SQL error";
}		

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>