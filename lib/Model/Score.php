<?php
namespace FSPA\Model;

class Score extends Base {
    private $score_id = null;
    private $player_id = null;
    private $machine_id = null;
    private $game_id = null;
    private $score = null;
    private $flags = null;
    private $tiebreaker = null;

    private $player = null;
    private $machine = null;
    private $game = null;

    public function __construct() {
    }

    public function getScore() {
        return $this->score;
    }

    public function newFromRow($row = null) {
        $score = new Score();
        if (isset($row)) {
            $score->score_id = $row['score_id'];
            $score->player_id = $row['player_id'];
            $score->machine_id = $row['machine_id'];
            $score->game_id = $row['game_id'];
            $score->score = $row['score'];
            $score->flags = $row['flags'];
            $score->tiebreaker = $row['tiebreaker'];
        }
        return $score;
    }

    public function fetchByID($scoreID) {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.scores WHERE score_id = :score_id");
        $sth->execute([ ':score_id' => $scoreID ]);
        $row = $sth->fetch();
        if ($row === false) {
            return new Score();
        }
        else {
            return Score::newFromRow($row);
        }
    }

    public function fetchAll() {
        $dbo = new \FSPA\DB();
        $sth = $dbo->dbh->prepare("SELECT * FROM fspa.scores");
        $sth->execute();

        $scores = [];
        while ($row = $sth->fetch()) {
            array_push($scores, Score::newFromRow($row));
        }
        return $scores;
    }
}
?>
