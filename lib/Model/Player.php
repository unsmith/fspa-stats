<?php
namespace FSPA\Model;

class Player extends Base {
    private $player_id = null;
    private $name = null;
    private $first_name = null;
    private $last_name = null;

    public function __construct() {
    }

    public function getID() {
        return $this->player_id;
    }

    public function getName() {
        return $this->name;
    }

    public function getFirstName() {
        return $this->first_name;
    }

    public function getLastName() {
        return $this->last_name;
    }

    public function newFromRow($row = null) {
        $player = new Player();
        if (isset($row)) {
            $player->player_id = $row['player_id'];
            $player->name = $row['name'];
            list($player->first_name, $player->last_name) = explode(" ", $player->name, 2);
        }
        return $player;
    }

    public function fetchByID($playerID) {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.players WHERE player_id = :player_id");
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
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.players ORDER BY name");
        $sth->execute();

        $players = [];
        while ($row = $sth->fetch()) {
            array_push($players, Player::newFromRow($row));
        }
        return $players;
    }
}
?>
