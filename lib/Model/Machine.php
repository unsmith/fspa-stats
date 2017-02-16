<?php
namespace FSPA\Model;

class Machine extends Base {
    private $machine_id = null;
    private $name = null;
    private $location_id = null;

    private $location = null;

    public function __construct() {
    }

    public function getName() {
        return $this->name;
    }

    public function newFromRow($row = null) {
        $machine = new Machine();
        if (isset($row)) {
            $machine->machine_id = $row['machine_id'];
            $machine->location_id = $row['location_id'];
            $machine->name = $row['name'];
        }
        return $machine;
    }

    public function fetchByID($machineID) {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.machines WHERE machine_id = :machine_id");
        $sth->execute([ ':machine_id' => $machineID ]);
        $row = $sth->fetch();
        if ($row === false) {
            return new Machine();
        }
        else {
            return Machine::newFromRow($row);
        }
    }

    public function fetchAll() {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.machines");
        $sth->execute();

        $machines = [];
        while ($row = $sth->fetch()) {
            array_push($machines, Machine::newFromRow($row));
        }
        return $machines;
    }
}
?>
