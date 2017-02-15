<?php
namespace FSPA\Model;

class Player extends Base {
    private $player_id = null;
    private $name = null;

    public function __construct() {
    }

    public function getName() {
        return $this->name;
    }

    public function newFromRow($row = null) {
        $player = new Player();
        if (isset($row)) {
            $player->player_id = $row['player_id'];
            $player->name = $row['name'];
        }
        return $player;
    }

    public function fetchByID($playerID) {
        $dbo = new \FSPA\DB();
        $dbh = $dbo->dbh;
        $sth = $dbh->prepare("SELECT * FROM fspa.players WHERE player_id = :player_id");
        $sth->execute([ ':player_id' => $playerID ]);
        $row = $sth->fetch();
        if ($row === false) {
            return new Player();
        }
        else {
            return Player::newFromRow($row);
        }
    }

    public function fetchAll() {
        $dbo = new \FSPA\DB();
        $dbh = $dbo->dbh;
        $sth = $dbh->prepare("SELECT * FROM fspa.players");
        $sth->execute();

        $players = [];
        while ($row = $sth->fetch()) {
            array_push($players, Player::newFromRow($row));
        }
        return $players;
    }
}
?>
