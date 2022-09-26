<?php

interface AuthorInterface {

    /**
     * Initialize Author Objects.
     *
     * @param int $authorId     Id to look up information
     * @throws Exception        On SQL issue
     * @return bool             True if data is set, otherwise false
     */
    public function init($authorId);

    public function create($authorName);

    /**
     * Returns author ID
     * @return int              ID of author
     */
    public function getId();
    
    /**
     * Returns author name
     * @return string           name of author
     */
    public function getName();
    
    /**
     * Returns author  display name
     * @return string           display name of author
     */
    public function getDisplayName();
}

class Author implements AuthorInterface {
    private $id;
    private $name;
    private $displayName;

    function __construct() {
    }

    public function toArray() {
        $returnValue = array();
        $returnValue["id"] = $this->getId();
        $returnValue["name"] = $this->getName();
        $returnValue["displayName"] = $this->getDisplayName();
        return $returnValue;
    }

    public function init($authorId) {
        if(is_int($authorId)) {
            $success = true;
            $this->id = $authorId;  
            $this->getDetails() ? null : $success = false; 
            if(!$success) throw new Exception('Author::init - problem with loading details');
            return $this;
        } else {
            throw new Exception('Author::init - parameter needs to be an integer');
        }
    }

    public function create($authorName) {
        global $mysqli;
        $authorName = trim($authorName);
        $sql = "INSERT INTO author(name, display_name) 
        VALUES('" . $mysqli->real_escape_string($authorName) . "', '" . $mysqli->real_escape_string($authorName) . "')
        ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)
        ";
        $mysqli->query($sql);
        if($mysqli->errno > 0) return false;
        $this->init($mysqli->insert_id);
        return $this; 
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getDisplayName() { return $this->displayName; }

    private function getDetails() {
        global $mysqli;
        $sql = "SELECT name, display_name FROM author WHERE id = " . $mysqli->real_escape_string($this->id);
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $this->name = $row["name"];
                $this->displayName = $row["display_name"];
            }
            $result->close();
        } else {
            return false;
        }
        return true;
    }

}
?>