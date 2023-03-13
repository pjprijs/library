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
    private $schoolyear, $groupname, $groupId;

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
        $returnValue["groupId"] = $this->getGroupId();
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
    public function getGroupId() { return $this->groupId; }

    public function setUserInfo($userId, $name, $prefix, $surname, $group, $year) {
        $this->id = $userId;
        if(trim($name) != "" && trim($surname != "")) {
            $this->name = trim($name);
            $this->prefix = trim($prefix);
            $this->surname = trim($surname);
            $this->groupId = (int) $group;
            $this->schoolYear = (int) $year;
            $this->setUserName();
            $this->setUserSchoolYear();
        } else {
            throw new Exception('User::setUserInfo - name or surname cannot be empty');
        }
        return $this;
    }

    public function addUserInfo($name, $prefix, $surname, $group, $year) {
        if(trim($name) != "" && trim($surname != "")) {
            $this->name = trim($name);
            $this->prefix = trim($prefix);
            $this->surname = trim($surname);
            $this->groupId = (int) $group;
            $this->schoolYear = (int) $year;
            $this->addUserName();
            $this->addUserSchoolYear();
        } else {
            throw new Exception('User::addUserInfo - name or surname cannot be empty');
        }
        return $this;
    }

    private function setUserSchoolYear() {
        global $mysqli;
        $sql = "UPDATE user_schoolyear us SET schoolyear = '" . $this->schoolYear . "', `group` = '" . $this->groupId . "' WHERE user = " . $this->id;
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("User::setUserSchoolYear - " . $mysqli->error);
        return $this;
    }

    private function setUserName() {
        global $mysqli;
        $sql = "UPDATE user u SET name = '" . $mysqli->escape_string($this->name) . "', surname = '" . $mysqli->escape_string($this->surname) . "', prefix = '" . $mysqli->escape_string($this->prefix) . "' WHERE id = " . $this->id;
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("User::setUserName - " . $mysqli->error);
        return $this;
    }

    private function addUserSchoolYear() {
        global $mysqli;
        $sql = "INSERT INTO user_schoolyear(user, schoolyear, `group`) VALUES('" . $this->id . "','" . $this->schoolYear . "', '" . $this->groupId . "')
                ON DUPLICATE KEY UPDATE schoolyear=VALUES(schoolyear), `group`=VALUES(`group`)";
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("User::setUserSchoolYear - " . $mysqli->error);
        return $this;
    }

    private function addUserName() {
        global $mysqli;
        $sql = "INSERT INTO user(`name`, `surname`, `prefix`) VALUES('" . $mysqli->escape_string($this->name) . "', '" . $mysqli->escape_string($this->surname) . "', '" . $mysqli->escape_string($this->prefix) . "')";
        $mysqli->query($sql);
        if($mysqli->errno > 0) throw new Exception("User::setUserName - " . $mysqli->error);
        $this->id = $mysqli->insert_id;
        return $this;
    }

    private function getUserDetails() {
        global $mysqli;
        $returnValue = true;
        $sql = "SELECT u.id, u.name, u.prefix, u.surname 
            , s.name AS schoolyear, IFNULL(g.name, '') AS groupname
            , g.id AS groupId
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
                $this->groupId = $row["groupId"];
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