<?php
namespace FSPA\Model;

class Season extends Base {
    private $season_id = null;
    private $name = null;
    private $league_id = null;

    private $league = null;
    private $meets = []; # Likely 10 meet records, representing each week of play in the season

    public function __construct() {
    }

    public function getName() {
        return $this->name;
    }

    public function newFromRow($row = null) {
        $season = new Season();
        if (isset($row)) {
            $season->season_id = $row['season_id'];
            $season->league_id = $row['league_id'];
            $season->name = $row['name'];
        }
        return $season;
    }

    public function fetchByID($seasonID) {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.seasons WHERE season_id = :season_id");
        $sth->execute([ ':season_id' => $seasonID ]);
        $row = $sth->fetch();
        if ($row === false) {
            return new Season();
        }
        else {
            return Season::newFromRow($row);
        }
    }

    public function fetchAll() {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.seasons");
        $sth->execute();

        $seasons = [];
        while ($row = $sth->fetch()) {
            array_push($seasons, Season::newFromRow($row));
        }
        return $seasons;
    }
}
?>
