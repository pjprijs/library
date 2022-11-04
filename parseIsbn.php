<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

include_once("php/prepend.php");
include_once("php/Book.php");
include_once("php/Author.php");

/*

//LOAN translate
SELECT tb.book, 1, l.LoanStart, l.LoanEnd FROM `Loan` l
INNER JOIN translate_bookid tb ON l.BookId = tb.old_book

*/

//PARSE ISBN
$lib = new Book();
$sql = "SELECT id, isbn FROM isbn_parsed WHERE parsed = 0";
if($result = $mysqli->query($sql)) {
    while($row = $result->fetch_assoc()) {
        // findNewBook($isbn);
        set_time_limit(10);
        try {
            $lib->createFromIsbn($row["isbn"]);
            $mysqli->query("UPDATE isbn_parsed SET parsed = 1 WHERE id = " . $row["id"]);
        } catch(Exception $e) {
            echo $e->getMessage();
        }
    }
}

?>