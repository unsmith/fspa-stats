<?php
namespace FSPA\Model;

class League extends Base {
    private $league_id = null;
    private $name = null;
    private $location_id = null;

    private $location = null;

    public function __construct() {
    }

    public function getID() {
        return $this->league_id;
    }

    public function getName() {
        return $this->name;
    }

    public function newFromRow($row = null) {
        $league = new League();
        if (isset($row)) {
            $league->league_id = $row['league_id'];
            $league->location_id = $row['location_id'];
            $league->name = $row['name'];
        }
        return $league;
    }

    public function fetchByID($leagueID) {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.leagues WHERE league_id = :league_id");
        $sth->execute([ ':league_id' => $leagueID ]);
        $row = $sth->fetch();
        if ($row === false) {
            return new League();
        }
        else {
            return League::newFromRow($row);
        }
    }

    public function fetchAll() {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.leagues");
        $sth->execute();

        $leagues = [];
        while ($row = $sth->fetch()) {
            array_push($leagues, League::newFromRow($row));
        }
        return $leagues;
    }
}
?>
