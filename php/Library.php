<?php
interface LibraryInterface {

}

class Library implements LibraryInterface {

    private $aviLevels, $aviLevelsOrder;

    function __construct() {        
    }

    public function getAuthors($searchString, $page = 1, $limit = 25) {
        $where = $this->getAuthorsWhere($searchString);
        $totalResult = $this->getAuthorsTotalCount($where);

        $data = array();
        if($totalResult > 0) $data = $this->getAuthorsData($where, $page, $limit);

        $returnVar["results"] = $data;
        $returnVar["pagination"]["more"] = ($totalResult > $limit);
        $returnVar["amount"] = $totalResult;
        return $returnVar;
    }

    public function getUsers($searchString, $page = 1, $limit = 25, $orderField = "u.name", $orderDir = 0) {
        $where = $this->getUsersWhere($searchString);
        $totalResult = $this->getUsersTotalCount($where);

        $data = array();
        if($totalResult > 0) { $data = $this->getUsersData($where, $page, $limit, $orderField, $orderDir); }
        $returnVar["results"] = $data;
        $returnVar["pagination"]["more"] = ($totalResult > $limit);
        $returnVar["amount"] = $totalResult;
        return $returnVar;
    }

    public function getLoanedBooks($userId, $open = "true", $page = 1, $limit = 25) {
        $start = ($page-1)*$limit;
        $amountOfLoans = $this->getLoanedBooksTotalCount($userId, $open);
        $data["results"] = $this->getLoanedBooksData($userId, $open, $start, $limit);
        $data["pagination"]["more"] = ($amountOfLoans > ($start + $limit));
        $data["amount"] = $amountOfLoans;
        return $data;
    }

    public function setLoanedBook($userId, $bookId) {
        global $mysqli;
        $sql = "INSERT INTO loan(book, user, startdate) VALUES('" . $mysqli->real_escape_string($bookId) . "', '" . $mysqli->real_escape_string($userId) . "', NOW())";
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("Library::setLoanedBook($userId, $bookId) - " . $mysqli->error);
        return $this;
    }

    public function returnLoanedBook($loanId) {
        global $mysqli;
        $sql = "UPDATE loan SET enddate=NOW() WHERE id=" . $this->escapeSql($loanId);
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("Library::returnLoanedBook($loanId) - " . $mysqli->error);
        return $this;
    }

    public function setMissingBook($loanId) {
        global $mysqli;
        $mysqli->autocommit(FALSE);
        $success = true;
        $sql = "UPDATE loan SET enddate='1111-11-11 11:11:11' WHERE id=" . $this->escapeSql($loanId);
        $mysqli->query($sql) ? null : $success = false;

        $sql = "SELECT book FROM loan WHERE id = " . $this->escapeSql($loanId);
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $book = (new Book)->init((int)$row["book"], true, false);
                $book->remove() ? null : $success = false;;
            }
            $result->close();
        }
        if($mysqli->errno > 0) $success = false;
        $success ? $mysqli->commit() : $mysqli->rollback();
        $mysqli->autocommit(TRUE);
        if(!$success) throw new Exception("Library::setMissingBook($loanId) - could not update loan or remove book");
        return $this;
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
                $bookIdList .= $this->escapeSql($value);
            }
        }

        $sql = "SELECT SUM(amount) AS amount FROM book WHERE id IN(" . $bookIdList . ")";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $extraAmountBooks = $row["amount"];
            }
            $result->close();
        }

        $sql = "UPDATE IGNORE book_author SET book = " . $this->escapeSql($mainBookId) . " WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;
        $sql = "DELETE FROM book_author WHERE book IN(" . $bookIdList . ")";        
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $sql = "UPDATE IGNORE book_isbn SET book = " . $this->escapeSql($mainBookId) . " WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;
        $sql = "DELETE FROM book_isbn WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $sql = "UPDATE IGNORE book_serie SET book = " . $this->escapeSql($mainBookId) . " WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;
        $sql = "DELETE FROM book_serie WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $sql = "UPDATE IGNORE loan SET book = " . $this->escapeSql($mainBookId) . " WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;
        $sql = "DELETE FROM loan WHERE book IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $sql = "DELETE FROM book WHERE id IN(" . $bookIdList . ")";
        $mysqli->query($sql) ? null : $all_query_ok=false;

        $all_query_ok ? $mysqli->commit() : $mysqli->rollback();
        $mysqli->autocommit(TRUE);

        $extraAmountBooks = count($combineBookArray)-1;

        $book = new Book();
        $book->init((int)$mainBookId, true, false);
        $book->addAmount($extraAmountBooks);
        return true;
    }    

    public function getBooks($searchValue = "", $page = 0, $limit = 25, $orderField = "title", $orderDir = 0) {
        global $mysqli;

        $where = $this->getBooksWhere($searchValue);
        $amountOfBooks = $this->getBooksTotalCount($where);  
        $start = ($page-1)*$limit;

        $data["results"] = $this->getBooksData($where, $page, $limit, $orderField, $orderDir);
        $data["pagination"]["more"] = ($amountOfBooks > ($start + $limit));
        $data["amount"] = $amountOfBooks;
        return $data;
    }    

    public function getUserById($userId) {
        $user = new User();
        if($user->init((int)$userId)) {
            return $user;
        }
        return false;
    }

    public function addItem($table, $value) {
        global $mysqli;
        $this->checkItemAmount($table, $value);
        $maxOrder = $this->getItemMaxOrder($table, $value);
        $sql = "INSERT INTO `" . $mysqli->real_escape_string($table) . "`(name, `order`)
                VALUES('" . $mysqli->real_escape_string($value) . "', '" . $maxOrder . "')
        ";
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception($mysqli->error);        
        return $mysqli->insert_id;
    }

    public function delItem($table, $value) {
        global $mysqli;
        $sql = "DELETE FROM `" . $this->escapeSql($table) . "`
                WHERE id = " . $this->escapeSql($value);
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception($mysqli->error);        
        return $mysqli->insert_id;
    }

    public function setItemActive($table, $value, $active) {
        global $mysqli;
        $sql = "UPDATE `" . $this->escapeSql($table) . "`
                SET active = '" . $this->escapeSql($active) . "'
                WHERE id = " . $this->escapeSql($value);
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception($mysqli->error);        
        return $mysqli->insert_id;
    }

    public function setItemName($table, $value, $name) {
        global $mysqli;
        $sql = "UPDATE `" . $this->escapeSql($table) . "`
                SET name = '" . $this->escapeSql($name) . "'
                WHERE id = " . $this->escapeSql($value);
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception($mysqli->error);        
        return $mysqli->insert_id;
    }

    public function getItems($item) {
        global $mysqli;
        $itemArray = array();
        $sql = "SELECT id, name, `order`, active FROM `" . $this->escapeSql($item) . "` ORDER BY `order` ASC";
        if($result = $mysqli->query($sql)){
            while($row = $result->fetch_assoc()) {
                $itemArray[$row["order"]]["id"] = $row["id"];
                $itemArray[$row["order"]]["name"] = $row["name"];
                $itemArray[$row["order"]]["active"] = $row["active"];
            }
            $result->close();
        }
        if($mysqli->errno > 0) throw new Exception($mysqli->error);
        return $itemArray;
    }

    public function setItemOrder($table, $data) {
        global $mysqli;
        $mysqli->autocommit(FALSE);
        $success = true;
        $errorMsg = "";
        foreach($data as $index=>$value) {
            $sql = "UPDATE `" . $this->escapeSql($table) . "`
                SET `order` = " . $this->escapeSql($index) . "
                WHERE id = " . $this->escapeSql($value) . "
            ";
            if(!$mysqli->query($sql)) {
                $success = false;
                $errorMsg = $mysqli->error;
            }
        }
        $success ? $mysqli->commit() : $mysqli->rollback();
        $mysqli->autocommit(TRUE);
        if(!$success) throw new Exception($errorMsg);
        return $this;
    }

    private function getAuthorsWhere($searchString) {
        global $mysqli;
        $where = "";
        $searchString = trim($this->escapeSql($searchString));
        if($searchString != "") {
            $where = " WHERE a.name LIKE '%" . $searchString . "%'
                OR  a.display_name LIKE '%" . $searchString . "%'
            ";
        }
        return $where;
    }

    private function getAuthorsTotalCount($where) {
        global $mysqli;
        $totalResult = 0;
        $sql = "SELECT COUNT(1) as amount FROM author a" . $where;
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $totalResult = $row["amount"];
            }
            $result->close();
        }
        return $totalResult;
    }

    private function getAuthorsData($where, $page, $limit) {
        global $mysqli;
        $data = array();
        $start = ((int)$page-1) * $limit;
        $sql = "SELECT a.id, a.name, a.display_name AS text
            FROM author a
            " . $where;
        $sql .= " LIMIT " . $start . ", " . $limit;

        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $index = count($data);
                $data[$index]["id"] = (int)$row["id"];
                $data[$index]["name"] = $row["name"];
                $data[$index]["text"] = $row["text"];
            }
            $result->close();
        }   
        return $data;
    }

    private function getUsersWhere($searchString) {
        global $mysqli;
        $where = "";
        $searchString = trim($this->escapeSql($searchString));
        if($searchString != "") {
            $where = " WHERE u.name LIKE '%" . $searchString . "%'
                OR  u.surname LIKE '%" . $searchString . "%'
                OR CONCAT(u.name, ' ', TRIM(CONCAT(u.prefix, ' ', u.surname))) LIKE '%" . $searchString . "%'
            ";
        }
        return $where;
    }
    private function getUsersTotalCount($where) {
        global $mysqli;
        $totalResult = 0;
        $sql = "SELECT COUNT(1) as amount FROM user u" . $where;
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $totalResult = $row["amount"];
            }
        }
        return $totalResult;
    }

    private function getUsersData($where, $page, $limit, $orderField, $orderDir) {
        global $mysqli;
        $data = array();
        $start = ((int)$page-1) * $limit;
        $sql = "SELECT u.id FROM user u
            LEFT JOIN user_schoolyear us ON u.id = us.user
            LEFT JOIN schoolyear s ON us.schoolyear = s.id
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
                $user = new User();
                $user->init((int)$row["id"]);
                $index = count($data);
                $data[$index]["id"] = $user->getId();
                $data[$index]["fullname"] = $user->getFullname();
                $data[$index]["name"] = $user->getName();
                $data[$index]["prefix"] = $user->getPrefix();
                $data[$index]["surname"] = $user->getSurname();
                $data[$index]["schoolyear"] = $user->getSchoolyear();
                $data[$index]["groupname"] = $user->getGroupname();

            }
        }
        return $data; 
    }    

    private function getLoanedBooksTotalCount($userId, $open) {
        global $mysqli;
        $sqlCount = "SELECT COUNT(1) AS amount FROM `loan` l 
            WHERE l.user = " . $this->escapeSql($userId) . "
            AND enddate " . ($open === "true" ? "" : " !") . "= '0000-00-00 00:00:00'";
        $amountOfLoans = 0;
        if($result = $mysqli->query($sqlCount)) {
            if($row = $result->fetch_assoc()) {
                $amountOfLoans = $row["amount"];
            }
            $result->close();
        }     
        return $amountOfLoans;
    }

    private function getLoanedBooksData($userId, $open, $start, $limit) {
        global $mysqli;
        $sql = "SELECT l.id AS loan, l.book, DATE_FORMAT(l.startdate, '%e %M \'%y') AS start_date, DATE_FORMAT(l.enddate, '%e %M \'%y') AS end_date, l.enddate 
            FROM loan l
            WHERE l.user = " . $this->escapeSql($userId) . "
            AND l.enddate " . ($open === "true" ? "" : " !") . "= '0000-00-00 00:00:00'
            ORDER BY l.startdate " . ($open === "true" ? "ASC" : "DESC") . "
        ";
        $sql .= " LIMIT " . $start . ", " . $limit;

        $returnValue = array();
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $book = (new Book)->init((int) $row["book"], true, false);
                $returnValue[count($returnValue)] = array_merge($row, $book->toArray());
            }
            $result->close();
        }   
        return $returnValue;
    }

    private function getBookIdListAuthors($searchValue) {
        global $mysqli;
        $bookIdList = "";
        $sql = "SELECT DISTINCT ba.book FROM author a 
        LEFT JOIN book_author ba ON a.id = ba.author
        WHERE a.name LIKE '%" . $this->escapeSql($searchValue) . "%' OR  a.display_name LIKE '%" . $this->escapeSql($searchValue) . "%'";
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()){
                if($bookIdList != "") $bookIdList .= ",";
                $bookIdList .= $row["book"];
            }
        }
        return $bookIdList;
    }

    private function getBookIdListIsbn($searchValue) {
        global $mysqli;
        $bookIdList = "";
        $sql = "SELECT DISTINCT bi.book FROM book_isbn bi 
        WHERE bi.isbn LIKE '%" . $this->escapeSql($searchValue) . "%'";
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()){
                if($bookIdList != "") $bookIdList .= ",";
                $bookIdList .= $row["book"];
            }
        }
        return $bookIdList;
    }

    private function getBookIdListSerie($searchValue) {
        global $mysqli;
        $bookIdList = "";
        $sql = "SELECT DISTINCT bs.book FROM `serie` s 
        INNER JOIN book_serie bs ON s.id = bs.serie 
        WHERE s.name LIKE '%" . $this->escapeSql($searchValue) . "%'";
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()){
                if($bookIdList != "") $bookIdList .= ",";
                $bookIdList .= $row["book"];
            }
        }
        return $bookIdList;
    }

    private function getBooksWhere($searchValue) {
        global $mysqli;
        $where = " WHERE b.amount > 0";
        $searchValue = trim($this->escapeSql($searchValue));
        if($searchValue != "") {
            $where .= " AND (b.title LIKE '%" . $searchValue . "%' OR b.subtitle LIKE '%" . $searchValue . "%'";
            $bookIdList = $this->getBookIdListAuthors($searchValue);
            $bookIdList .= $this->getBookIdListIsbn($searchValue);
            $bookIdList .= $this->getBookIdListSerie($searchValue);
            if($bookIdList != "") $where .= " OR b.id IN(" . $bookIdList . ")";
            $where .= ")";
        }  
        return $where;      
    }
    private function getBooksTotalCount($where) {
        global$mysqli;
        $sqlCount = "SELECT COUNT(b.id) AS amount FROM `book` b
        " . $where;
        $amountOfBooks = 0;
        if($result = $mysqli->query($sqlCount)) {
            if($row = $result->fetch_assoc()) {
                $amountOfBooks = $row["amount"];
            }
        }        
        return $amountOfBooks;
    }

    private function getBooksData($where, $page, $limit, $orderField, $orderDir) {
        global $mysqli;
        $start = ($page-1)*$limit;
        $sql = "SELECT b.id FROM `book` b" . $where;
        $sql .= " ORDER BY " . $orderField;
        if($orderDir == 0) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }
        $sql .= " LIMIT " . $start . ", " . $limit;

        $returnValue = array();
        if($result = $mysqli->query($sql)) {
            while($row = $result->fetch_assoc()) {
                $book = (new Book)->init((int)$row["id"]);
                $index = count($returnValue);
                $returnValue[$index] = $book->toArray();
            }
        }
        return $returnValue;
    }

    private function checkItemAmount($table, $value) {
        global $mysqli;
        $sql = "SELECT COUNT(1) AS amount FROM `" . $this->escapeSql($table) . "` WHERE name = '" . $this->escapeSql(strtolower($value)) . "'";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                if($row["amount"] > 0) throw new Exception("Value '" . $value . "' already exists in table " . $table);
            }
            $result->close();
        }
        if($mysqli->errno > 0) throw new Exception($mysqli->error);
        return $this;
    }

    private function getItemMaxOrder($table, $value) {
        global $mysqli;
        $maxOrder = 0;
        $sql = "SELECT MAX(`order`) AS maxOrder FROM `" . $this->escapeSql($table) . "`";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $maxOrder = (int)$row["maxOrder"];
                $maxOrder++;
            }
            $result->close();
        }
        if($mysqli->errno > 0) throw new Exception($mysqli->error);
        return $maxOrder;
    }

    private function escapeSql($value) {
        global $mysqli;
        $value = str_replace('%','\%', $value);
        return $mysqli->real_escape_string($value);
    }

}
?>