<?php 

interface SerieInterface {

}

class Serie implements SerieInterface {

    private $id, $name, $number;

    function __construct() {
    }

    public function init($serieId) {
        $this->number = "";
        if(is_int($serieId)) {
            $success = true;
            $this->id = $serieId;   
            $this->setName();
            if(!$success) throw new Exception('Serie::init - problem with loading details');
            return $this;
        } else {
            throw new Exception('Serie::init - parameter needs to be an integer');
        }
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getNumber() { return $this->number; }

    public function setNumber($value) {
        $this->number = $value;
        return $this;
    }

    public function toArray() {
        $returnValue = array();
        $returnValue["id"] = $this->getId();
        $returnValue["name"] = $this->getName();
        $returnValue["number"] = $this->getNumber();
        return $returnValue;
    }

    private function setName() {
        global $mysqli;
        $returnValue = array();
        $sql = "SELECT s.name FROM serie s WHERE s.id = " . $this->id . "";
        if($result = $mysqli->query($sql)) {
            if($row = $result->fetch_assoc()) {
                $this->name = $row["name"];
            }
        }
        return $returnValue;
    }
}

?>