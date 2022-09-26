<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once("php/prepend.php");
//include_once("php/Library.php");

//update group random: UPDATE user_schoolyear SET `group` = ELT(0.5 + RAND() * 15, 1,2,3,4,5,6,7,8,9,15, 16, 17, 18, 19, 20)

//PARSE ISBN
//$lib = new Library();
$sql = "SELECT DISTINCT b1.title, b1.subtitle
FROM `book` b1
INNER JOIN book b2 ON b1.title = b2.title AND b1.subtitle = b2.subtitle AND b1.id != b2.id";
if($result = $mysqli->query($sql)) {
    while($row = $result->fetch_assoc()) {
        $sqlCheck = "SELECT id FROM book WHERE title ='" . $row["title"] . "' AND subtitle = '" . $row["subtitle"] . "'";
        if($resultCheck = $mysqli->query($sqlCheck)) {
            $firstId = -1;
            $idList = "";
            while($rowCheck = $resultCheck->fetch_assoc()) {
                if($firstId == -1) {
                    $firstId = $rowCheck["id"];
                } else {
                    $idList .= ",";
                }
                $idList .= $rowCheck["id"];
            }
            echo $firstId . " - SELECT * FROM book WHERE id IN(" . $idList . ")<br/>";
        }
    }
}

?>