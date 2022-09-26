<?php
include_once("../php/prepend.php");

unset($_SESSION["obj"]);

$result["error"] = 6;
$result["errorMsg"] = "";
try {
    $lib = new Library();
    //$result["data"]["avi"] = $lib->getAviLevels();
    $result["data"]["avi"] = $lib->getItems("avi");
    $result["data"]["group"] = $lib->getItems("group");
    $_SESSION["obj"] = serialize($lib);
    $result["error"] = 0;
    $result["errorMsg"] = "";
} catch(Exception $e) {
    $result["errorMsg"] = $e->getMessage();    
}


echo $_REQUEST['callback'] . "(".json_encode($result).")";

?>