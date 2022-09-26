<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once("php/prepend.php");

$specArray = array();

function parseSpecGroup($content) {
    if(strpos($content, '<div class="specs">')) {
        list($head, $tail) = explode('<h3>', $content, 2);
        list($index, $tail) = explode('</h3>', $tail, 2);
        list($head, $tail) = explode('<dl class="specs__list">', $tail, 2);
        list($data, $tail) = explode('</dl>', $tail, 2);
        parseSpecList($index, $data);
        parseSpecGroup($tail);
    }
}
function parseSpecList($index, $specList) {
    global $specArray;
    if(strpos($specList, '<dt class="specs__title')) {
        list($head, $tail) = explode('<dt class="specs__title', $specList, 2);
        list($head, $tail) = explode('>', $tail, 2);
        list($varIndex, $tail) = explode('</dt>', $tail, 2);
        $varIndex = trim($varIndex);
        list($head, $tail) = explode('<dd class="specs__value', $tail, 2);
        list($head, $tail) = explode('>', $tail, 2);
        list($varValue, $specList) = explode('</dd>', $tail, 2);
        $varValue = trim($varValue);
        if(strpos($varValue, "\n")) {
            $specArray[$index][$varIndex] = parseSpecsValue($varValue);
        } else {
            $specArray[$index][$varIndex] = strip_tags($varValue);
        }
        parseSpecList($index, $specList);
    }
}

function parseSpecsValue($value) {
    $returnArray = array();
    $value = nl2br($value) . "<br />";
    while(strpos($value, "<br />")) {
        list($tmpItem, $value) = explode("<br />", $value, 2);
        $tmpItem = trim(strip_tags($tmpItem));
        if($tmpItem != "" && !in_array($tmpItem, $returnArray)) $returnArray[count($returnArray)] = $tmpItem;
    }
    return $returnArray;
}

//BOL parser
$isbn = "9789047620785";
if(isset($_REQUEST["isbn"])) $isbn = $_REQUEST["isbn"];
$url = "https://www.bol.com/nl/nl/s/?searchtext=";
$content = file_get_contents($url . $isbn);

list($head, $tail) = explode('product-content"', $content, 2);
list($head, $tail) = explode('<a href="', $tail, 2);
list($link, $tail) = explode('"', $tail, 2);
$content = file_get_contents("https://www.bol.com" . $link);

list($head, $content) = explode('data-test="product-title"', $content, 2);
list($head, $tail) = explode('data-test="title">', $content, 2);
list($title, $tail) = explode('</', $tail, 2);
@list($head, $tail) = explode('data-test="subtitle">', $tail, 2);
@list($subtitle, $tail) = explode('</', $tail, 2);
list($head, $tail) = explode('data-test="current-image"', $content, 2);
list($head, $tail) = explode('<img src="', $tail, 2);
list($image, $tail) = explode('"', $tail, 2);

list($head, $tail) = explode('data-test="brand"', $content, 2);
list($head, $tail) = explode('<a href="', $tail, 2);
list($head, $tail) = explode('>', $tail, 2);
list($author, $tail) = explode('</', $tail, 2);

@list($head, $tail) = explode('data-test="language"', $content, 2);
@list($head, $tail) = explode('<a ', $tail, 2);
@list($head, $tail) = explode('>', $tail, 2);
@list($language, $tail) = explode('</', $tail, 2);

list($head, $content) = explode('data-test="specifications"', $content, 2);
list($specList, $tail) = explode('</div></div>', $content, 2);
parseSpecGroup($specList);


$description = "";
if(strpos($tail, 'data-test="product-description"')) {
    list($head, $tail) = explode('data-test="product-description"', $content, 2);
    if(strpos($tail, 'data-test="description"')) {
        list($head, $tail) = explode('data-test="description"', $tail, 2);
        list($head, $tail) = explode('>', $tail, 2);
        list($description, $tail) = explode('</', $tail, 2);    
    } else if(strpos($tail, 'data-test="text-short"')) {
        list($head, $tail) = explode('data-test="text-short"', $tail, 2);
        list($head, $tail) = explode('>', $tail, 2);
        list($description, $tail) = explode('</', $tail, 2);    
    }
}

@list($head, $tail) = explode('data-test="review-rating-average"', $content, 2);
@list($head, $tail) = explode('>', $tail, 2);
@list($avgScore, $tail) = explode('</', $tail, 2);

list($head, $tail) = explode('<script type="application/ld+json">', $content, 2);
list($json, $tail) = explode('</script>', $tail, 2);
$data = json_decode($json, true);

$specArray["title"] = trim($title);
$specArray["subtitle"] = trim($subtitle);
$specArray["authorString"] = trim($author);
$specArray["imageLarge"] = trim($image);
$specArray["descriptionBreaks"] = trim(str_replace('<br />', "\n", $description));
$specArray["avgScore"] = trim($avgScore);
$specArray = array_merge($specArray, $data);
ksort($specArray);

echo "<pre>";
print_r($specArray);
?>