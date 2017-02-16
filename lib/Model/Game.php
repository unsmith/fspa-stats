<?php
namespace FSPA\Model;

class Game extends Base {
    private $game_id = null;
    private $game_num = null;
    private $group_id = null;

    private $group = null;
    private $scores = [];  # Between 1 and 4 score records

    public function __construct() {
    }

    public function getGameNum() {
        return $this->game_num;
    }

    public function newFromRow($row = null) {
        $game = new Game();
        if (isset($row)) {
            $game->game_id = $row['game_id'];
            $game->game_num = $row['game_num'];
            $game->group_id = $row['group_id'];
        }
        return $game;
    }

    public function fetchByID($gameID) {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.games WHERE game_id = :game_id");
        $sth->execute([ ':game_id' => $gameID ]);
        $row = $sth->fetch();
        if ($row === false) {
            return new Game();
        }
        else {
            return Game::newFromRow($row);
        }
    }

    public function fetchAll() {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.games");
        $sth->execute();

        $games = [];
        while ($row = $sth->fetch()) {
            array_push($games, Game::newFromRow($row));
        }
        return $games;
    }
}
?>
