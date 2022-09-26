<?php

interface UserInterface {

    public function init($userId);
    public function getId();
    public function getName();
    public function getSurname();
    public function getPrefix();
    public function getFullname();
    public function getSchoolyear();
    public function getGroupname();

}

class User implements UserInterface {
    private $id;
    private $name, $surname, $prefix;
    private $schoolyear, $groupname;

    function __construct() {
    }

    public function toArray() {
        $returnValue = array();
        $returnValue["id"] = $this->getId();
        $returnValue["name"] = $this->getName();
        $returnValue["surname"] = $this->getSurname();
        $returnValue["prefix"] = $this->getPrefix();
        $returnValue["fullname"] = $this->getFullname();
        $returnValue["schoolyear"] = $this->getSchoolyear();
        $returnValue["groupname"] = $this->getGroupname();
        return $returnValue;
    }


    public function init($userId) {
        if(is_int($userId)) {
            $success = true;
            $this->id = $userId;            
            $this->getUserDetails() ? null : $success = false;
            return $success;
        } else {
            return false;
        }
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getSurname() { return $this->surname; }
    public function getPrefix() { return $this->prefix; }
    public function getFullname() { 
        $fullname = $this->name . " " . trim($this->prefix . " " . $this->surname);
        return $fullname; 
    }
    public function getSchoolyear() { return $this->schoolyear; }
    public function getGroupname() { return $this->groupname; }

    private function getUserDetails() {
        global $mysqli;
        $returnValue = true;
        $sql = "SELECT u.id, u.name, u.prefix, u.surname 
            , s.name AS schoolyear, IFNULL(g.name, '') AS groupname
            FROM user u
            LEFT JOIN user_schoolyear us ON u.id = us.user
            LEFT JOIN schoolyear s ON us.schoolyear = s.id
            LEFT JOIN `group` g ON us.`group` = g.id
            WHERE u.id = " . $mysqli->real_escape_string($this->id) . "
        ";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $this->id = $row["id"];
                $this->name = $row["name"];
                $this->surname = $row["surname"];
                $this->prefix = $row["prefix"];
                $this->schoolyear = $row["schoolyear"];
                $this->groupname = $row["groupname"];
            } else {
                $returnValue = false;
            }
            $result->close();
        }    
        if($mysqli->errno > 0) $returnValue = false;
        return $returnValue;        
    }

}

?>