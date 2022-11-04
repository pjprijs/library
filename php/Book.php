<?php

interface BookInterface {
    /**
     * Initialize Book Object.
     *
     * @param int $bookId       id to look up information
     * @throws Exception        if parameter is not an integer / loading details failed
     * @return Book             Bok objects for chaining
     */
    public function init($bookId);

    /**
     * Create Book Object.
     *
     * @param string $isbn      ISBN to find book info online
     * @return bool             true on success, false on rollback
     */

    public function delete();

    public function createFromIsbn($isbn);

    public function getLoanHistory($page = 1, $limit = 25);

    public function getLoanedAmount();

    public function setAvi($avi);

    /**
     * Returns book ID
     * @return int              ID of book
     */
    public function getId();

    /**
     * Returns book title
     * @return string           title
     */
    public function getTitle();

    /**
     * Returns book subtitle
     * @return string           subtitle
     */
    public function getSubtitle();

    /**
     * Returns book subtitle
     * @return string           subtitle
     */
    public function getFulltitle();

    /**
     * Returns book description
     * @return string           description
     */
    public function getDescription();

    /**
     * Returns book published date
     * @return string           published date
     */
    public function getPublishedDate();

    /**
     * Returns book AVI ID
     * @return int              ID of AVI level
     */
    public function getAviId();

    /**
     * Returns book AVI name
     * @return string           AVI name/text
     */
    public function getAvi();

    /**
     * Returns book title
     * @return int              amount of books in library
     */
    public function getAmount();

    /**
     * Returns book pagecount
     * @return string           pagecount
     */
    public function getPageCount();

    /**
     * Returns ISBN data
     * @return array            array of IBN data (isbn, edition, manual[ly added])
     */
    public function getIsbn();

    /**
     * Returns Serie(s)
     * @return array            array of Serie Objects
     */
    public function getSeries();

    /**
     * Returns Author(s)
     * @return array            array of Author Objects
     */
    public function getAuthors();
}

class Book implements BookInterface {
    private $id;
    private $title, $subtitle, $description;
    private $publishedDate, $avi;
    private $amount, $pagecount, $aviId;
    private $isbnArray, $authorArray, $serieArray;

    function __construct() {
        $this->isbnArray = array();
        $this->authorArray = array();
        $this->serieArray = array();
    }

    public function toArray() {
        $returnValue = array();
        $returnValue["id"] = $this->getId();
        $returnValue["title"] = $this->getTitle();
        $returnValue["subtitle"] = $this->getSubtitle();
        $returnValue["fulltitle"] = $this->getFulltitle();
        //$returnValue["text"] = $this->getFulltitle();
        $returnValue["description"] = $this->getDescription();
        $returnValue["publishedDate"] = $this->getPublishedDate();
        $returnValue["avi"] = $this->getAvi();
        $returnValue["amount"] = $this->getAmount();
        $returnValue["loaned"] = $this->getLoanedAmount();
        $returnValue["pagecount"] = $this->getPageCount();
        $returnValue["aviId"] = $this->getAviId();
        $returnValue["isbn"] = $this->getIsbn();
        $returnValue["author"] = $this->getAuthorsToArray();
        $returnValue["serie"] = $this->getSeriesToArray();
        return $returnValue;
    }

    public function init($bookId, $loadDetails = true, $loadArrays = true) {
        if(is_int($bookId)) {
            $success = true;
            $this->id = $bookId;   
            if($loadDetails) {         
                $this->getBookDetails() ? null : $success = false;
                if($loadArrays) {
                    $this->getIsbnArray() ? null : $success = false;
                    $this->getAuthorArray() ? null : $success = false;
                    $this->getSerieArray() ? null : $success = false;
                }
            }
            if(!$success) throw new Exception('Book::init - problem with loading details');
            return $this;
        } else {
            throw new Exception('Book::init - parameter needs to be an integer');
        }
    }

    public function initIsbn($isbn, $loadArrays = true) {
        $success = false;
        if($this->getBookDetails($isbn)) {
            $success = true;
            if($loadArrays) {
                $this->getIsbnArray() ? null : $success = false;
                $this->getAuthorArray() ? null : $success = false;
                $this->getSerieArray() ? null : $success = false;
            }
        }
        return $success;
    }

    public function delete() {
        global $mysqli;
        // todo: check uitleningen!!!
        $sql = "UPDATE book SET amount = 0 WHERE id = " . $this->id;
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("Book::delete - " . $mysqli->error);
        $this->amount = 0;
        return $this;
    }

    public function remove() {
        // todo: check uitleningen!!!
        $returnValue = true;
        if($this->amount > 0) {
            //$amount = $this->amount - 1;
            $returnValue = $this->addAmount(-1);
        }
        return $returnValue;
    }

    public function add() {
        return $this->addAmount(1);
    }

    public function addAmount($amount) {
        global $mysqli;
        $this->amount = $amount;
        $sql = "UPDATE book SET amount = amount + " . $this->amount . " WHERE id = " . $this->id;
        $mysqli->query($sql);
        if($mysqli->errno > 0) {
            throw new Exception("Book::addAmount - " . $mysqli->error);
        }
        return $this;
    }

    public function createFromIsbn($isbn) {
        $isbn = $this->formatIsbn($isbn);
        if(!$isbn) throw new Exception("Book::createFromIsbn - No ISBN number");
        $data = false;
        $dataGoogle = $this->findBookGoogle($isbn);
        $dataWorldCat = $this->findBookWorldCat($isbn);
        $data = $dataGoogle;
        if(is_array($data) && is_array($dataWorldCat)) {
            $data = array_merge($dataWorldCat, $data);
            $data["title"] = $dataWorldCat["title"];
            if(is_array($dataWorldCat["authors"])) $data["authors"] = $dataWorldCat["authors"];
        } else if(is_array($dataWorldCat)) {
            $data = $dataWorldCat;
        }
        if(is_array($data)) {
            $data["isbn"] = $isbn;
            return $this->addNewBook($data);
        } else {
            throw new Exception('test');
            return false;
        }
    }

    public function addNewBook($data) {
        global $mysqli;
        $mysqli->autocommit(FALSE);

        $returnValue = true;
        $manual = 0;
        if(isset($data["manual"])) $manual = $data["manual"];
        $title = trim($data["title"]);
        $subtitle = @trim($data["subtitle"]);
        $description = @trim($data["description"]);
        $isbn = $data["isbn"];
        $pageCount = @trim($data["pageCount"]);
        if($pageCount == "") $pageCount = 0;
        $edition = @trim($data["edition"]);
        $publishedDate = @trim($data["publishedDate"]);
        $language = @trim($data["language"]);
        $avi = @trim($data["avi"]);
        $authorString = $this->getAuthorString($data["authors"]);
        $md5hash = md5(strtolower($title . $subtitle) . strtolower($authorString));

        $this->addBook($title, $subtitle, $avi, $publishedDate, $language, $description, $pageCount, $md5hash) ? null : $returnValue=false;
        $this->setBookAuthor() ? null : $returnValue=false;
        $this->setBookIsbn($isbn, $edition, $manual) ? null : $returnValue=false;
        /*
        if(array_key_exists("publication", $data)) {
            @$this->addSerie($bookId, $data["publication"], $data["publicationNr"]);
        }
        */
        $this->findBookCover($isbn);

        $returnValue ? $mysqli->commit() : $mysqli->rollback();
        $mysqli->autocommit(TRUE);
        return $returnValue;
    }    

    public function getLoanHistory($page = 1, $limit = 25) {
        $start = ($page-1)*$limit;
        $amountOfLoans = $this->getLoanHistoryTotalCount();
        $data["results"] = $this->getLoanHistoryData($page, $limit);
        $data["pagination"]["more"] = ($amountOfLoans > ($start + $limit));
        $data["amount"] = $amountOfLoans;
        return $data;
    }

    public function getLoanedAmount() {
        global $mysqli;
        $returnValue = 0;
        $sql = "SELECT COUNT(1) AS amount FROM loan WHERE book = " . $this->id . " AND enddate = '0000-00-00 00:00:00'";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $returnValue = $row["amount"];
            }
        }
        return $returnValue;
    }

    public function setAvi($avi) {
        global $mysqli;
        $sql = "UPDATE book SET avi = '" . $mysqli->real_escape_string($avi) . "' WHERE id = " . $this->id;
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("Book::setAvi - " . $mysqli->error);
        return $this;
    }



    public function getId() { return $this->id; }
    public function getTitle() { return $this->title; }
    public function getSubtitle() { return $this->subtitle; }
    public function getFulltitle() { 
        $fulltitle = $this->title;
        if(trim($this->subtitle) != "") $fulltitle .= " - " . $this->subtitle;
        return $fulltitle; 
    }
    public function getDescription() { return $this->description; }
    public function getPublishedDate() { return $this->publishedDate; }
    public function getAviId() { return $this->aviId; }
    public function getAvi() { return $this->avi; }
    public function getAmount() { return $this->amount; }
    public function getPageCount() { return $this->pagecount; }
    public function getIsbn() { return $this->isbnArray; }
    public function getAuthors() { return $this->authorArray; }
    public function getSeries() { return $this->serieArray; }
    public function getAuthorsToArray() { 
        $returnValue = array();
        foreach($this->authorArray as $author) {
            $returnValue[count($returnValue)] = $author->toArray();
        }
        return $returnValue; 
    }
    public function getSeriestoArray() { 
        $returnValue = array();
        foreach($this->serieArray as $serie) {
            $returnValue[count($returnValue)] = $serie->toArray();
        }
        return $returnValue; 
    }


    private function findBookGoogle($isbn) {
        $string = file_get_contents("https://www.googleapis.com/books/v1/volumes?q=isbn:" . $isbn);
        if ($string === false) return false;
        $json = json_decode($string, true);
        if ($json === null) return false;
        if($json["totalItems"] < 1) return false;
        @list($title, $edition) = explode(" / druk", $json["items"][0]["volumeInfo"]["title"], 2);
        $json["items"][0]["volumeInfo"]["title"] = $title;
        $json["items"][0]["volumeInfo"]["edition"] = $edition;
        return $json["items"][0]["volumeInfo"];
    }

    private function findBookWorldCat($isbn) {
        $data = array();
        $url = "http://www.worldcat.org/search?q=bn:" . $isbn . "&fq=x0:book";
        $infoWebsite = file_get_contents($url);
        @list($head, $tail) = explode("<div class=\"name\">", $infoWebsite, 2);
        @list($title, $tail) = explode("</div>", $tail, 2);
        @list($head, $tail2) = explode("<strong>", $title, 2);
        @list($title, $tail2) = explode("</strong>", $tail2, 2);
        @list($data["title"], $data["subtitle"]) = explode(":", $title, 2);
        $data["title"] = trim($data["title"]);
        $data["subtitle"] = trim($data["subtitle"]);
        if($data["title"] == "") return false;
    
        if(strpos($tail, "Publication:")) {
            @list($a, $b) = explode("Publication:", $tail, 2);
            @list($publication, $b) = explode("</div>", $b, 2);
            $data["publication"] = trim($publication);
            if(strpos($publication, "dl.")) {
                @list($publication, $bookNr) = explode("dl.", $publication, 2);
                $data["publication"] = trim($publication);
                $data["publicationNr"] = trim($bookNr);
            } else if(strpos($publication, ",")) {
                @list($publication, $bookNr) = explode(",", $publication, 2);
                $data["publication"] = trim($publication);
                $data["publicationNr"] = trim($bookNr);
            }  
        }
        @list($head, $tail) = explode("<div class=\"author\">", $tail, 2);
        @list($author, $tail) = explode("</div>", $tail, 2);
        $author = trim(str_replace("by", "", $author));
        $data["authors"] = explode("; ", $author);
    
        @list($head, $tail) = explode("<div class=\"type\">", $tail, 2);
        @list($type, $tail) = explode("</div>", $tail, 2);
        @list($head, $tail2) = explode("<span class='itemType'>", $type, 2);
        @list($data["type"], $tail2) = explode("</span>", $tail2, 2);
        @list($data["typeInfo"], $tail2) = explode("<a", $tail2, 2);
    
        @list($head, $tail) = explode("<span class=\"itemLanguage\">", $tail, 2);
        @list($data["language"], $tail) = explode("</span>", $tail, 2);
        if($data["language"] == "Dutch") $data["language"] = "nl";
    
        @list($head, $tail) = explode("<span class=\"itemPublisher\">", $tail, 2);
        @list($publisher, $tail) = explode("</span>", $tail, 2);
        preg_match_all('!\d+!', $publisher, $matches);
        $data["publishedDate"] = @$matches[0][0];
        return $data;
    }

    private function findBookCover($isbn) {
        $url = "https://fbn.hostedwise.nl/cgi-bin/momredir.pl?size=300&isbn=" . $isbn;
        $folder = str_replace("/ajax", "", getcwd());
        $target = $folder . "/images/isbn/i";
        
        $opts = array('http' =>
            array(
                'follow_location' => 0,
            )
        );
        $context = stream_context_create($opts);
        $result = file_get_contents($url, false, $context);
        if(strpos($result, "302 Found")) {
            return false;
        } else {
            file_put_contents($target . $isbn . ".jpg", $result);
            return true;
        }        
    }

    private function getAuthorString($inputArray) {
        $authorString = "";
        $this->authorArray = array();
        foreach($inputArray as $value) {
            if($authorString != "") $authorString .= "|";
            $author = new Author();
            if(!is_numeric($value)) {
                $author->create($value);
            } else {
                $author->init((int)$value);
            }
            $this->authorArray[count($this->authorArray)] = $author;
            $authorString .= "_" . $author->getId() . "_";
        }
        //print_r($this->authorArray);
        return $authorString;
    }

    private function addBook($title, $subtitle, $avi, $publishedDate, $language, $description, $pageCount, $md5hash) {
        global $mysqli;
        if($avi == "") $avi = 1;
        $sql = "INSERT INTO book(title, subtitle, avi, amount, published_date, language, description, pagecount, md5hash) 
                VALUES('" . $mysqli->real_escape_string($title) . "', '" . $mysqli->real_escape_string($subtitle) . "', '" . $mysqli->real_escape_string($avi) . "', '1', '" . $mysqli->real_escape_string($publishedDate) . "', '" . $mysqli->real_escape_string($language) . "', '" . $mysqli->real_escape_string($description) . "', '" . $mysqli->real_escape_string($pageCount) . "', '" . $md5hash . "')
                ON DUPLICATE KEY UPDATE amount=amount+1
                ";
        $mysqli->query($sql);
        if($mysqli->errno > 0) {
            echo $sql;
            echo $mysqli->error;
            return false;
        }
        $this->init((int)$mysqli->insert_id); 
        return true;
    }

    private function setBookAuthor() {
        global $mysqli;
        $returnValue = true;
        foreach($this->authorArray as $author) {
            $sql = "INSERT IGNORE INTO book_author(book, author)
            VALUES('" . $mysqli->real_escape_string($this->id) . "', '" . $mysqli->real_escape_string($author->getId()) . "')
            ";
            $mysqli->query($sql);
            if($mysqli->errno > 0) {
                echo $sql;
                echo $mysqli->error;
                $returnValue = false;
            }
        }
        return $returnValue;
    }

    private function setBookIsbn($isbn, $edition, $manual) {
        global $mysqli;
        $sql = "INSERT IGNORE INTO book_isbn(book, isbn, edition, manual) VALUES('" . $mysqli->real_escape_string($this->id) . "','" . $mysqli->real_escape_string($isbn) . "','" . $mysqli->real_escape_string($edition) . "','" . $mysqli->real_escape_string($manual) . "')";
        $mysqli->query($sql);
        if($mysqli->errno > 0) {
            echo $sql;
            echo $mysqli->error;
            return false;
        }
        return true;
    }


    private function getBookDetails($isbn = null) {
        global $mysqli;
        $returnValue = false;
        $sql = "SELECT b.id, b.title, b.subtitle, b.amount, b.description, b.published_date AS publishedDate, b.pagecount, a.id AS aviId, a.name AS avi 
                FROM `book` b
                LEFT JOIN avi a ON b.avi = a.id 
                ";
        if($isbn != null) {
            $sql .= "INNER JOIN book_isbn bi ON b.id = bi.book
                WHERE bi.isbn = '" . $mysqli->real_escape_string($isbn) . "'";
        } else {
            $sql .= "WHERE b.id = " . $mysqli->real_escape_string($this->id);
        }

        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $this->id = (int)$row["id"];
                $this->title = $row["title"];
                $this->subtitle = $row["subtitle"];
                $this->amount = (int)$row["amount"];
                $this->description = $row["description"];
                $this->publishedDate = $row["publishedDate"];
                $this->pagecount = (int)$row["pagecount"];
                $this->aviId = (int)$row["aviId"];
                $this->avi = $row["avi"];
                $returnValue = true;
            }
            $result->close();
        }
        return $returnValue;
    }

    private function getIsbnArray() {
        global $mysqli;
        $returnValue = false;
        $sql = "SELECT bi.isbn, bi.edition, bi.manual 
        FROM book_isbn bi
        WHERE bi.book = " . $this->id;
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $this->isbnArray[count($this->isbnArray)] = $row;
            }
            $returnValue = true;
            $result->close();
        } 
        return $returnValue;
    }

    private function getAuthorArray() {
        global $mysqli;
        $returnValue = false;
        $sql = "SELECT ba.author FROM book_author ba WHERE ba.book = " . $this->id;
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $author = (new Author)->init((int) $row["author"]);
                $this->authorArray[count($this->authorArray)] = $author;
            }
            $returnValue = true;
            $result->close();
        }
        return $returnValue;
    }

    public function getSerieArray() {
        global $mysqli;
        $returnValue = false;
        $sql = "SELECT bs.serie, bs.serie_nr FROM book_serie bs WHERE bs.book = " . $this->id;
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $serie = (new Serie)->init((int) $row["serie"]);
                $serie->setNumber($row["serie_nr"]);
                $this->serieArray[count($this->serieArray)] = $serie;
            }
            print_r($this->serieArray);
            $returnValue = true;
            $result->close();
        }
        return $returnValue;
    }

    private function formatIsbn($isbn) {
        $isbn = trim($isbn);
        if (strlen($isbn) == 13) {
            return $isbn;
        } else if (strlen($isbn) == 12) { // if number is UPC just add zero
            return '0' . $isbn;
        } else {
            $isbn2 = substr("978" . trim($isbn), 0, -1);
            $sum13 = $this->genchksum13($isbn2);
            $isbn13 = "$isbn2$sum13";
            return ($isbn13);
        }
        return false;
    }

    private function genchksum13($isbn) {
        $isbn = trim($isbn);
        $tb = 0;
        for ($i = 0; $i <= 12; $i++) {
            $tc = substr($isbn, -1, 1);
            $isbn = substr($isbn, 0, -1);
            $ta = ($tc * 3);
            $tci = substr($isbn, -1, 1);
            $isbn = substr($isbn, 0, -1);
            $tb = $tb + $ta + $tci;
        }
    
        $tg = ($tb / 10);
        $tint = intval($tg);
        if ($tint == $tg) {
            return 0;
        }
        $ts = substr($tg, -1, 1);
        $tsum = (10 - $ts);
        return $tsum;
    }

    private function getLoanHistoryTotalCount() {
        global $mysqli;
        $amountOfLoans = 0;
        $sqlCount = "SELECT COUNT(1) AS amount FROM `loan` l WHERE l.book = " . $this->id;
        if($result = $mysqli->query($sqlCount)) {
            if($row = $result->fetch_assoc()) {
                $amountOfLoans = $row["amount"];
            }
        }     
        return $amountOfLoans;
    }

    private function getLoanHistoryData($page, $limit) {
        global $mysqli;
        $start = ($page-1)*$limit;
        $sql = "SELECT l.id, l.book, DATE_FORMAT(l.startdate, '%e %M \'%y') AS start_date, DATE_FORMAT(l.enddate, '%e %M \'%y') AS end_date, l.enddate, CONCAT(u.name, ' ', TRIM(CONCAT(u.prefix, ' ', u.surname))) as fullname 
                FROM `loan` l 
                LEFT JOIN user u ON l.user = u.id
                WHERE l.book = " . $this->id;
        $sql2 = $sql;
        $sql .= " AND l.enddate = '0000-00-00 00:00:00'";
        $sql2 .= " AND l.enddate != '0000-00-00 00:00:00'";
        $sql .= " ORDER BY l.startdate DESC LIMIT " . $start . ", " . $limit;
        $sql2 .= " ORDER BY l.startdate DESC LIMIT " . $start . ", " . $limit;
        //Open loans
        $returnValue = array();
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $returnValue[count($returnValue)] = $row;
            }
        }
        //Returned loans
        if($result = $mysqli->query($sql2)) {
            while($row = $result->fetch_assoc()) {
                $returnValue[count($returnValue)] = $row;
            }
        }
        return $returnValue;
    }
}
?>