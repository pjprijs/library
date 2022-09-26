<?php

class Library {

    function __construct() {
        //
    }

    public function getAviLevels() {
        global $mysqli;
        $aviLevels = array();
        $sql = "SELECT id, name, `order` FROM avi ORDER BY `order` ASC";
        if($result = $mysqli->query($sql)){
            while($row = $result->fetch_assoc()) {
                $aviLevels[$row["order"]]["id"] = $row["id"];
                $aviLevels[$row["order"]]["name"] = $row["name"];
                $aviLevelsOrder[$row["id"]] = $row["order"];
            }
        }
        $returnVal["aviLevels"] = $aviLevels;
        $returnVal["aviLevelsOrder"] = $aviLevelsOrder;
        return $returnVal;
    }

    public function getUserById($userId) {
        global $mysqli;
        $data = null;
        $sql = "SELECT u.id, CONCAT(u.name, ' ', TRIM(CONCAT(u.prefix, ' ', u.surname))) as fullname 
            , s.name AS schoolyear, IFNULL(g.name, '') AS groupname
            FROM user u
            LEFT JOIN user_schoolyear us ON u.id = us.user
            LEFT JOIN schoolyear s ON us.schoolyear = s.id
            LEFT JOIN `group` g ON us.`group` = g.id
            WHERE u.id = " . $this->esc($userId) . "
        ";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $data = array();
                $data["id"] = $row["id"];
                $data["fullname"] = $row["fullname"];
                $data["schoolyear"] = $row["schoolyear"];
                $data["groupname"] = $row["groupname"];
            }
        }    
        if($mysqli->errno > 0) throw new Exception("getUserById($userId) - " . $mysqli->error);;
        return $data;
    }

    public function getUsers($searchString, $page, $limit = 25, $orderField = "u.name", $orderDir = 0) {
        global $mysqli;

        //CREATE WHERE STATEMENT
        $where = "";
        $searchString = trim($this->esc($searchString));
        if($searchString != "") {
            $where = " WHERE u.name LIKE '%" . $searchString . "%'
                OR  u.surname LIKE '%" . $searchString . "%'
                OR CONCAT(u.name, ' ', TRIM(CONCAT(u.prefix, ' ', u.surname))) LIKE '%" . $searchString . "%'
            ";
        }

        //TOTAL RESULTS FOR PAGINATION
        $totalResult = 0;
        $sql = "SELECT COUNT(1) as amount FROM user u" . $where;
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $totalResult = $row["amount"];
            }
        }
        if($mysqli->errno > 0) throw new Exception("getUsers($searchString, $page, $limit, $orderField, $orderDir) - Pagination - " . $mysqli->error);;

        $data = array();
        if($totalResult > 0) {
            //MAIN QUERY 
            $start = ((int)$page-1) * $limit;
            $sql = "SELECT u.id, CONCAT(u.name, ' ', TRIM(CONCAT(u.prefix, ' ', u.surname))) as fullname 
                    , s.name AS schoolyear, IFNULL(g.name, '') AS groupname
                FROM user u
                LEFT JOIN user_schoolyear us ON u.id = us.user
                LEFT JOIN schoolyear s ON us.schoolyear = s.id
                LEFT JOIN `group` g ON us.`group` = g.id
                " . $where;
            $sql .= " ORDER BY " . $orderField;
            if($orderDir == 0) {
                $sql .= " ASC";
            } else {
                $sql .= " DESC";
            }
            $sql .= " LIMIT " . $start . ", " . $limit;

            //RETREIVE DATA
            if($result = $mysqli->query($sql)) {
                while($row = $result->fetch_assoc()) {
                    $index = count($data);
                    $data[$index]["id"] = (int)$row["id"];
                    $data[$index]["fullname"] = $row["fullname"];
                    $data[$index]["schoolyear"] = $row["schoolyear"];
                    $data[$index]["groupname"] = $row["groupname"];
                }
            }   
            if($mysqli->errno > 0) throw new Exception("getUsers($searchString, $page, $limit, $orderField, $orderDir) - Retreive data - " . $mysqli->error);; 
        }
        $returnVar["results"] = $data;
        $returnVar["pagination"]["more"] = ($totalResult > $limit);
        $returnVar["amount"] = $totalResult;
        return $returnVar;
    }

    public function getAuthors($searchString, $page, $limit = 25) {
        global $mysqli;

        //CREATE WHERE STATEMENT
        $where = "";
        $searchString = trim($this->esc($searchString));
        if($searchString != "") {
            $where = " WHERE a.name LIKE '%" . $searchString . "%'
                OR  a.display_name LIKE '%" . $searchString . "%'
            ";
        }

        //TOTAL RESULTS FOR PAGINATION
        $totalResult = 0;
        $sql = "SELECT COUNT(1) as amount FROM author a" . $where;
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $totalResult = $row["amount"];
            }
        }
        if($mysqli->errno > 0) throw new Exception("getAuthors($searchString, $page, $limit) - Pagination - " . $mysqli->error);;

        $data = array();
        if($totalResult > 0) {
            //MAIN QUERY 
            $start = ((int)$page-1) * $limit;
            $sql = "SELECT a.id, a.name, a.display_name AS text
                FROM author a
                " . $where;
            $sql .= " LIMIT " . $start . ", " . $limit;

            //RETREIVE DATA
            if($result = $mysqli->query($sql)) {
                while($row = $result->fetch_assoc()) {
                    $index = count($data);
                    $data[$index]["id"] = (int)$row["id"];
                    $data[$index]["name"] = $row["name"];
                    $data[$index]["text"] = $row["text"];
                }
            }   
            if($mysqli->errno > 0) throw new Exception("getAuthors($searchString, $page, $limit) - Retreive data - " . $mysqli->error);;
        }
        $returnVar["results"] = $data;
        $returnVar["pagination"]["more"] = ($totalResult > $limit);
        $returnVar["amount"] = $totalResult;
        return $returnVar;
    }    

    public function updateBookAmount($bookId, $amount = 1) {
        global $mysqli;
        $sql = "UPDATE book SET amount = amount+" . $amount . " WHERE id = " . $bookId;
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("updateBookAmount($bookId) - " . $mysqli->error);;
        return $this;
    }

    public function getIsbnBookId($isbn) {
        global $mysqli;
        $returnValue["isbn"] = $isbn;
        $returnValue["bookId"] = 0;
        $returnValue["amount"] = 0;
        $sql = "SELECT bi.book, b.amount
                FROM book_isbn bi
                LEFT JOIN book b ON bi.book = b.id
                WHERE bi.isbn = '" . $isbn . "'";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $returnValue["bookId"] = (int)$row["book"];
                $returnValue["amount"] = (int)$row["amount"];
            }
        }
        if($mysqli->errno > 0) throw new Exception("getIsbnBookId($isbn) - " . $mysqli->error);;
        return $returnValue;
    }

    public function findNewBook($isbn) {
        $isbn = $this->formatIsbn($isbn);
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
            return (int)$this->addNewBook($data);
        } else {
            return false;
        }
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
        /*
            [printType] => BOOK
            [maturityRating] => NOT_MATURE
            [language] => nl
        */
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

    public function addNewBook($data) {
        $isbn = $data["isbn"];
        $manual = 0;
        if(isset($data["manual"])) $manual = $data["manual"];
        //@list($title, $edition) = explode(" / druk", $data["title"], 2);
        $title = trim($data["title"]);
        @$subtitle = trim($data["subtitle"]);
        @$description = trim($data["description"]);
        @$pageCount = trim($data["pageCount"]);
        @$edition = trim($data["edition"]);
        @$publishedDate = $data["publishedDate"];
        @$language = trim($data["language"]);
        @$avi = trim($data["avi"]);
        $authorString = "";
        $authorArray = [];
        foreach($data["authors"] as $value) {
            if($authorString != "") $authorString .= "|";
            $authorId = $value;
            if(!is_numeric($authorId)) {
                $authorId = $this->addAuthor($value);
            }
            $authorArray[count($authorArray)] = $authorId;
            $authorString .= "_" . $authorId . "_";
        }
        $md5hash = md5(strtolower($title . $subtitle) . strtolower($authorString));
        $bookId = $this->addBook($title, $subtitle, $avi, $publishedDate, $language, $description, $pageCount, $md5hash);
        $this->setBookAuthor($bookId, $authorArray)->setBookIsbn($bookId, $isbn, $edition, $manual);
        if(array_key_exists("publication", $data)) {
            @$this->addSerie($bookId, $data["publication"], $data["publicationNr"]);
        }
        $this->findBookCover($isbn);
        return $bookId;
    }

    private function addSerie($book, $serie, $serieNr) {
        global $mysqli;
        $sql = "INSERT INTO serie(name) VALUES('" . $this->esc($serie) . "') ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("addSerie($book, $serie, $serieNr) - " . $mysqli->error);;
        $serie = $mysqli->insert_id; 
        $sql = "INSERT IGNORE INTO book_serie(book, serie, serie_nr) VALUES('" . $book . "', '" . $serie . "', '" . $serieNr . "')";
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("addSerie($book, $serie, $serieNr) - " . $mysqli->error);;
        return true;
    }

    private function setBookIsbn($book, $isbn, $edition, $manual) {
        global $mysqli;
        $sql = "INSERT IGNORE INTO book_isbn(book, isbn, edition, manual) VALUES('" . $this->esc($book) . "','" . $this->esc($isbn) . "','" . $this->esc($edition) . "','" . $this->esc($manual) . "')";
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("setBookIsbn($book, $isbn, $edition, $manual) - " . $mysqli->error);;
        return $this;
    }

    private function setBookAuthor($book, $authorArray) {
        global $mysqli;
        foreach($authorArray as $value) {
            $sql = "INSERT IGNORE INTO book_author(book, author)
            VALUES('" . $this->esc($book) . "', '" . $this->esc($value) . "')
            ";
            $mysqli->query($sql);
            if($mysqli->errno > 0) throw new Exception("setBookAuthor($book, $authorArray) - " . $mysqli->error);;
        }
        return $this;
    }

    private function addAuthor($name) {
        global $mysqli;
        $authSql = "INSERT INTO author(name, display_name) 
        VALUES('" . $this->esc($name) . "', '" . $this->esc($name) . "')
        ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
        ";
        $mysqli->query($authSql);
        if($mysqli->errno > 0) throw new Exception("addAuthor($name) - " . $mysqli->error);;
        return $mysqli->insert_id; 
    }

    private function addBook($title, $subtitle, $avi, $publishedDate, $language, $description, $pageCount, $md5hash) {
        global $mysqli;
        if($avi == "") $avi = 1;
        $sql = "INSERT INTO book(title, subtitle, avi, amount, published_date, language, description, pagecount, md5hash) 
                VALUES('" . $this->esc($title) . "', '" . $this->esc($subtitle) . "', '" . $this->esc($avi) . "', '1', '" . $this->esc($publishedDate) . "', '" . $this->esc($language) . "', '" . $this->esc($description) . "', '" . $this->esc($pageCount) . "', '" . $md5hash . "')
                ON DUPLICATE KEY UPDATE amount=amount+1
                ";
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("addBook($title, $subtitle, $avi, $publishedDate, $language, $description, $pageCount, $md5hash) - " . $mysqli->error);;
        return $mysqli->insert_id; 
    }

    public function setCombineBook($mainBookId, $combineBookArray) {
        global $mysqli;
        $mysqli->autocommit(FALSE);
        $all_query_ok=true;
        $extraAmountBooks = 0;
        $bookIdList = "";
        foreach($combineBookArray as $value) {
            if($value != $mainBookId) {
                if($bookIdList != "") $bookIdList .= ",";
                $bookIdList .= $this->esc($value);
            }
        }

        $sql = "SELECT SUM(amount) AS amount FROM book WHERE id IN(" . $bookIdList . ")";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $extraAmountBooks = $row["amount"];
            }
        }

        $sql = "UPDATE IGNORE book_author SET book = " . $this->esc($mainBookId) . " WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;
        $sql = "DELETE FROM book_author WHERE book IN(" . $bookIdList . ")";        
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $sql = "UPDATE IGNORE book_isbn SET book = " . $this->esc($mainBookId) . " WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;
        $sql = "DELETE FROM book_isbn WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $sql = "UPDATE IGNORE book_serie SET book = " . $this->esc($mainBookId) . " WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;
        $sql = "DELETE FROM book_serie WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $sql = "UPDATE IGNORE loan SET book = " . $this->esc($mainBookId) . " WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;
        $sql = "DELETE FROM loan WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $sql = "DELETE FROM book WHERE id IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $all_query_ok ? $mysqli->commit() : $mysqli->rollback();
        $mysqli->autocommit(TRUE);

        $extraAmountBooks = count($combineBookArray)-1;
        $this->updateBookAmount($mainBookId, $extraAmountBooks);
        return true;
    }

    public function getBooks($searchValue = "", $page = 0, $limit = 25, $orderField = "title", $orderDir = 0) {
        global $mysqli;

        //CREATE WHERE STATEMENT
        $where = " WHERE b.amount > 0";
        if($searchValue != "") {
            $where .= " AND (b.title LIKE '%" . $searchValue . "%' OR b.subtitle LIKE '%" . $searchValue . "%'";

            $bookIdList = "";
            $sql = "SELECT DISTINCT ba.book FROM author a 
            LEFT JOIN book_author ba ON a.id = ba.author
            WHERE a.name LIKE '%" . $searchValue . "%' OR  a.display_name LIKE '%" . $searchValue . "%'";
            if($result = $mysqli->query($sql)) {
                while($row = $result->fetch_assoc()){
                    if($bookIdList != "") $bookIdList .= ",";
                    $bookIdList .= $row["book"];
                }
            }
            $sql = "SELECT DISTINCT bi.book FROM book_isbn bi 
            WHERE bi.isbn LIKE '%" . $searchValue . "%'";
            if($result = $mysqli->query($sql)) {
                while($row = $result->fetch_assoc()){
                    if($bookIdList != "") $bookIdList .= ",";
                    $bookIdList .= $row["book"];
                }
            }
            $sql = "SELECT DISTINCT bs.book FROM `serie` s 
            INNER JOIN book_serie bs ON s.id = bs.serie 
            WHERE s.name LIKE '%" . $searchValue . "%'";
            if($result = $mysqli->query($sql)) {
                while($row = $result->fetch_assoc()){
                    if($bookIdList != "") $bookIdList .= ",";
                    $bookIdList .= $row["book"];
                }
            }
            if($bookIdList != "") $where .= " OR b.id IN(" . $bookIdList . ")";
            $where .= ")";
        }

        //MAIN QUERY AND LIMIT
        $start = ($page-1)*$limit;
        $sql = "SELECT b.id, b.title, b.subtitle, b.amount, b.published_date, b.pagecount, a.name AS avi
                FROM `book` b
                LEFT JOIN avi a ON b.avi = a.id
        " . $where;
        $sql .= " ORDER BY " . $orderField;
        if($orderDir == 0) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }
        $sql .= " LIMIT " . $start . ", " . $limit;

        //TOTAL AMOUNT OF BOOKS FOR PAGINATION
        $sqlCount = "SELECT COUNT(b.id) AS amount FROM `book` b
        " . $where;
        $amountOfBooks = 0;
        if($result = $mysqli->query($sqlCount)) {
            if($row = $result->fetch_assoc()) {
                $amountOfBooks = $row["amount"];
            }
        }        

        //RETREIVE INFORMATION
        $returnValue = array();
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $index = count($returnValue);
                $returnValue[$index]["id"] = $row["id"];
                $returnValue[$index]["text"] = $row["title"] . ($row["subtitle"] != "" ? " - " . $row["subtitle"] : "");
                $returnValue[$index]["title"] = $row["title"];
                $returnValue[$index]["subtitle"] = $row["subtitle"];
                $returnValue[$index]["avi"] = $row["avi"];
                $returnValue[$index]["amount"] = $row["amount"];
                $returnValue[$index]["published_date"] = $row["published_date"];
                $returnValue[$index]["pagecount"] = $row["pagecount"];
                $returnValue[$index]["loaned"] = $this->getLoanedBookAmount($row["id"]);
                $returnValue[$index]["author"] = $this->getBookAuthor($row["id"]);
                $returnValue[$index]["serie"] = $this->getBookSerie($row["id"]);
            }
        }
        $data["results"] = $returnValue;
        $data["pagination"]["more"] = ($amountOfBooks > ($start + $limit));
        $data["amount"] = $amountOfBooks;
        return $data;
    }

    public function getBookDetails($bookId) {
        global $mysqli;
        $sql = "SELECT b.id, b.title, b.subtitle, b.amount, b.description, b.published_date, b.pagecount, a.id AS aviId, a.name AS avi 
                FROM `book` b
                LEFT JOIN avi a ON b.avi = a.id
                WHERE b.id = " . $this->esc($bookId);
        $returnValue = array();
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $returnValue["id"] = $row["id"];
                $returnValue["title"] = $row["title"];
                $returnValue["subtitle"] = $row["subtitle"];
                $returnValue["aviId"] = $row["aviId"];
                $returnValue["avi"] = $row["avi"];
                $returnValue["amount"] = $row["amount"];
                $returnValue["description"] = $row["description"];
                $returnValue["published_date"] = $row["published_date"];
                $returnValue["pagecount"] = $row["pagecount"];
                $returnValue["loaned"] = $this->getLoanedBookAmount($row["id"]);
                $returnValue["author"] = $this->getBookAuthor($row["id"]);
                $returnValue["isbn"] = $this->getIsbn($row["id"]);
                $returnValue["serie"] = $this->getBookSerie($row["id"]);
            }
        }
        return $returnValue;
    }

    public function setBookAvi($bookId, $avi) {
        echo "TEST!!";
        global $mysqli;
        $sql = "UPDATE book SET avi = '" . $this->esc($avi) . "' WHERE id = " . $this->esc($bookId);
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("setBookAvi($bookId, $avi) - " . $mysqli->error);
        echo $sql;
        return true;
    }

    public function getIsbn($bookId){
        global $mysqli;
        $returnValue = array();
        $sql = "SELECT bi.isbn 
        FROM book_isbn bi
        WHERE bi.book = " . $bookId . "
        ";
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $returnValue[count($returnValue)] = $row;
            }
        }
        return $returnValue;
    }

    public function getAuthor($authorId) {
        global $mysqli;
        $returnValue = "";
        $sql = "SELECT name FROM author WHERE id = " . $this->esc($authorId);
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $returnValue = $row["name"];
            }
        }
        return $returnValue;
    }

    public function getBookAuthor($bookId) {
        global $mysqli;
        $returnValue = array();
        $sql = "SELECT DISTINCT a.name , a.display_name
        FROM book_author ba
        LEFT JOIN author a ON ba.author = a.id
        WHERE ba.book = " . $bookId . "
        ";
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $returnValue[count($returnValue)] = $row;
            }
        }
        return $returnValue;
    }

    public function getBookSerie($bookId) {
        global $mysqli;
        $returnValue = array();
        $sql = "SELECT DISTINCT s.name, bs.serie_nr
        FROM book_serie bs
        LEFT JOIN serie s ON bs.serie = s.id
        WHERE bs.book = " . $bookId . "
        ";
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $returnValue[count($returnValue)] = $row;
            }
        }
        return $returnValue;
    }

    public function delBook($bookId) {
        global $mysqli;
        // todo: check uitleningen!!!
        $sql = "UPDATE book SET amount = 0 WHERE id = " . $bookId;
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("delBook($bookId) - " . $mysqli->error);;
        return $this;
    }

    public function getLoanedBooks($userId, $open = "true", $page = 1, $limit = 25) {
        global $mysqli;
        $start = ($page-1)*$limit;

        $sql = "SELECT b.id, b.title, b.subtitle, l.id AS loan, DATE_FORMAT(l.startdate, '%e %M \'%y') AS start_date, DATE_FORMAT(l.enddate, '%e %M \'%y') AS end_date, l.enddate 
            FROM loan l
            LEFT JOIN book b ON l.book = b.id
            WHERE l.user = " . $this->esc($userId) . "
            AND enddate " . ($open === "true" ? "" : " !") . "= '0000-00-00 00:00:00'
            ORDER BY l.startdate " . ($open === "true" ? "ASC" : "DESC") . "
        ";
        $sql .= " LIMIT " . $start . ", " . $limit;

        //TOTAL AMOUNT OF BOOKS FOR PAGINATION
        $sqlCount = "SELECT COUNT(1) AS amount FROM `loan` l 
            WHERE l.user = " . $this->esc($userId) . "
            AND enddate " . ($open === "true" ? "" : " !") . "= '0000-00-00 00:00:00'";
        $amountOfLoans = 0;
        if($result = $mysqli->query($sqlCount)) {
            if($row = $result->fetch_assoc()) {
                $amountOfLoans = $row["amount"];
            }
        }     
        if($mysqli->errno > 0) throw new Exception("getLoanedBooks($userId, $open) - " . $mysqli->error);;

        $returnValue = array();
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $returnValue[count($returnValue)] = $row;
            }
        }   
        if($mysqli->errno > 0) throw new Exception("getLoanedBooks($userId, $open) - " . $mysqli->error);;
        $data["results"] = $returnValue;
        $data["pagination"]["more"] = ($amountOfLoans > ($start + $limit));
        $data["amount"] = $amountOfLoans;
        return $data;
    }

    public function setLoanedBook($userId, $bookId) {
        global $mysqli;
        $sql = "INSERT INTO loan(book, user, startdate) VALUES('" . $this->esc($bookId) . "', '" . $this->esc($userId) . "', NOW())";
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("setLoanedBook($userId, $bookId) - " . $mysqli->error);
        return $this;
    }

    public function returnLoanedBook($loanId) {
        global $mysqli;
        $sql = "UPDATE loan SET enddate=NOW() WHERE id=" . $this->esc($loanId);
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("returnLoanedBook($loanId) - " . $mysqli->error);
        return $this;
    }

    public function setMissingBook($loanId) {
        global $mysqli;
        $sql = "UPDATE loan SET enddate='1111-11-11 11:11:11' WHERE id=" . $this->esc($loanId);
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("setMissingBook($loanId) - " . $mysqli->error);

        $sql = "SELECT book FROM loan WHERE id = " . $this->esc($loanId);
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $sqlDel = "UPDATE book SET amount = amount-1 WHERE id = " . $row["book"];
                echo $sqlDel;
                $mysqli->query($sqlDel);
            }
        }
        if($mysqli->errno > 0) throw new Exception("setMissingBook($loanId) - " . $mysqli->error);;
        return $this;
    }

    public function getLoanAvailableBook($bookId) {
        $returnVal["loaned"] = (int)$this->getLoanedBookAmount($bookId);
        $returnVal["total"] = (int)$this->getTotalBookAmount($bookId);
        return $returnVal;
    }

    public function getBookTitle($bookId) {
        global $mysqli;
        $returnValue = array();        
        $sql = "SELECT title, subtitle FROM book WHERE id = " . $bookId;
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $returnValue["title"] = $row["title"];
                if(trim($row["subtitle"]) != "") $returnValue["title"] .= " - " . $row["subtitle"];
                $returnValue["bookId"] = (int)$bookId;
            }
        }
        return $returnValue;
    }

    private function getLoanedBookAmount($bookId) {
        global $mysqli;
        $returnValue = 0;
        $sql = "SELECT COUNT(1) AS amount FROM loan WHERE book = " . $bookId . " AND enddate = '0000-00-00 00:00:00'";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $returnValue = $row["amount"];
            }
        }
        if($mysqli->errno > 0) throw new Exception("getLoanedBookAmount($bookId) - " . $mysqli->error);
        return $returnValue;
    }

    private function getTotalBookAmount($bookId) {
        global $mysqli;
        $returnValue = 0;
        $sql = "SELECT amount FROM book WHERE id = " . $bookId;
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $returnValue = $row["amount"];
            }
        }
        if($mysqli->errno > 0) throw new Exception("getTotalBookAmount($bookId) - " . $mysqli->error);
        return $returnValue;
    }

    private function esc($value) {
        global $mysqli;
        return $mysqli->real_escape_string($value);
    }

    public function getBookHistory($bookId, $page = 0, $limit = 25) {
        global $mysqli;

        //MAIN QUERY AND LIMIT
        $start = ($page-1)*$limit;
        $sql = "SELECT l.id, l.book, DATE_FORMAT(l.startdate, '%e %M \'%y') AS start_date, DATE_FORMAT(l.enddate, '%e %M \'%y') AS end_date, l.enddate, CONCAT(u.name, ' ', TRIM(CONCAT(u.prefix, ' ', u.surname))) as fullname 
                FROM `loan` l 
                LEFT JOIN user u ON l.user = u.id
                WHERE l.book = " . $this->esc($bookId);
        $sql2 = $sql;
        $sql .= " AND l.enddate = '0000-00-00 00:00:00'";
        $sql2 .= " AND l.enddate != '0000-00-00 00:00:00'";
        $sql .= " ORDER BY l.startdate DESC LIMIT " . $start . ", " . $limit;
        $sql2 .= " ORDER BY l.startdate DESC LIMIT " . $start . ", " . $limit;

        //TOTAL AMOUNT OF BOOKS FOR PAGINATION
        $sqlCount = "SELECT COUNT(1) AS amount FROM `loan` l WHERE l.book = " . $this->esc($bookId);
        $amountOfLoans = 0;
        if($result = $mysqli->query($sqlCount)) {
            if($row = $result->fetch_assoc()) {
                $amountOfLoans = $row["amount"];
            }
        }     
        if($mysqli->errno > 0) throw new Exception("getBookHistory($bookId, $page, $limit) - " . $mysqli->error);

        //RETREIVE INFORMATION
        $returnValue = array();
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $returnValue[count($returnValue)] = $row;
            }
        }
        //RETREIVE INFORMATION
        if($result = $mysqli->query($sql2)) {
            while($row = $result->fetch_assoc()) {
                $returnValue[count($returnValue)] = $row;
            }
        }
        if($mysqli->errno > 0) throw new Exception("getBookHistory($bookId, $page, $limit) - " . $mysqli->error);
        $data["results"] = $returnValue;
        $data["pagination"]["more"] = ($amountOfLoans > ($start + $limit));
        $data["amount"] = $amountOfLoans;
        return $data;
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
    
}

?>