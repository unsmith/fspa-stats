<?php
namespace FSPA\Model;

class Meet extends Base {
    private $meet_id = null;
    private $date = null;
    private $season_id = null;

    private $season = null;
    private $groups = []; # At least one group record for those player's weekly play

    public function __construct() {
    }

    public function getDate() {
        return $this->date;
    }

    public function newFromRow($row = null) {
        $meet = new Meet();
        if (isset($row)) {
            $meet->meet_id = $row['meet_id'];
            $meet->season_id = $row['season_id'];
            $meet->date = $row['date'];
        }
        return $meet;
    }

    public function fetchByID($meetID) {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.meets WHERE meet_id = :meet_id");
        $sth->execute([ ':meet_id' => $meetID ]);
        $row = $sth->fetch();
        if ($row === false) {
            return new Meet();
        }
        else {
            return Meet::newFromRow($row);
        }
    }

    public function fetchAll() {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.meets");
        $sth->execute();

        $meets = [];
        while ($row = $sth->fetch()) {
            array_push($meets, Meet::newFromRow($row));
        }
        return $meets;
    }
}
?>
