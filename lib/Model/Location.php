<?php
namespace FSPA\Model;

class Location extends Base {
    private $location_id = null;
    private $name = null;

    public function __construct() {
    }

    public function getName() {
        return $this->name;
    }

    public function newFromRow($row = null) {
        $location = new Location();
        if (isset($row)) {
            $location->location_id = $row['location_id'];
            $location->name = $row['name'];
        }
        return $location;
    }

    public function fetchByID($locationID) {
        $dbo = new \FSPA\DB();
        $dbh = $dbo->dbh;
        $sth = $dbh->prepare("SELECT * FROM fspa.locations WHERE location_id = :location_id");
        $sth->execute([ ':location_id' => $locationID ]);
        $row = $sth->fetch();
        if ($row === false) {
            return new Location();
        }
        else {
            return Location::newFromRow($row);
        }
    }

    public function fetchAll() {
        $dbo = new \FSPA\DB();
        $dbh = $dbo->dbh;
        $sth = $dbh->prepare("SELECT * FROM fspa.locations");
        $sth->execute();

        $locations = [];
        while ($row = $sth->fetch()) {
            array_push($locations, Location::newFromRow($row));
        }
        return $locations;
    }
}
?>
