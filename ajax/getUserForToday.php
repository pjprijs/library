<?php

include_once("../php/prepend.php");

echo "\n/*\n";

try {
	$tmpGroups = $_REQUEST["selectedGroups"];
	if($tmpGroups == null) $tmpGroups = array();
	$result["data"] = (new Library)->getUserForToday($tmpGroups, $_REQUEST["submitted"], $_REQUEST["loaned"]);
	$result["data"]["elem"] = $_REQUEST["elem"];
	$result["error"] = 0;
	$result["errorMsg"] = "";
} catch(Exception $e) {
	$result["error"] = 5;
	$result["errorMsg"] = $e->getMessage();
}

echo "\n*/\n";
echo $_REQUEST['callback'] . "(".json_encode($result).")";
  
?>