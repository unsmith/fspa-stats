<?php
namespace FSPA;

class Stats {
    private $dbo = null;

    public function __construct() {
        $this->dbo = new DB();
    }

    public function fetchStats($args = []) {
        $binds = [];
        if (isset($args['playerID'])) {
            $binds['playerID'] = $args['playerID'];
        }
        if (isset($args['leagueID'])) {
            $binds['leagueID'] = $args['leagueID'];
        }

        $sql = "SELECT
                    (" . $this->leagueSQL($args)     . ") AS leagues,
                    (" . $this->seasonSQL($args)     . ") AS seasons,
                    (" . $this->playerSQL($args)     . ") AS players,
                    (" . $this->machineSQL($args)    . ") AS machines,
                    (" . $this->locationSQL($args)   . ") AS locations,
                    (" . $this->meetSQL($args)       . ") AS meets,
                    (" . $this->groupSQL($args)      . ") AS groups,
                    (" . $this->gameSQL($args)       . ") AS games,
                    (" . $this->scoreSQL($args)      . ") AS scores,
                    (" . $this->oldestDateSQL($args) . ") AS oldest_date,
                    (" . $this->newestDateSQL($args) . ") AS newest_date,
                    (" . $this->totalScoreSQL($args) . ") AS total_score";

        $sth = $this->dbo->dbh->prepare($sql);
        $sth->execute($binds);
        $stats = $sth->fetch();
        return $stats;
    }

    private function leagueSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT count(distinct lg.name) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT count(name) FROM fspa.leagues WHERE league_id = :leagueID";
        }
        else {
            return "SELECT count(league_id) FROM fspa.leagues";
        }
    }

    private function seasonSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT count(distinct sn.name) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT count(distinct sn.name) FROM fspa.seasons sn INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT count(season_id) FROM fspa.seasons";
        }
    }

    private function playerSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT count(name) FROM fspa.players WHERE player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT count(distinct pl.name) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT count(player_id) FROM fspa.players";
        }
    }

    private function machineSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT count(distinct ma.name) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.machines ma ON sc.machine_id = ma.machine_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT count(distinct ma.name) FROM fspa.machines ma INNER JOIN fspa.scores sc ON sc.machine_id = ma.machine_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT count(machine_id) FROM fspa.machines";
        }
    }

    private function locationSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT count(distinct lo.name) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id INNER JOIN fspa.locations lo ON lg.location_id = lo.location_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT count(distinct lo.name) FROM fspa.leagues lg INNER JOIN fspa.locations lo ON lg.location_id = lo.location_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT count(location_id) FROM fspa.locations";
        }
    }

    private function meetSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT count(distinct mt.date) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT count(distinct mt.date) FROM fspa.meets mt INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT count(meet_id) FROM fspa.meets";
        }
    }

    private function groupSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT count(distinct mt.date, gr.group_num) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT count(distinct mt.date, gr.group_num) FROM fspa.groups gr INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT count(group_id) FROM fspa.groups";
        }
    }

    private function gameSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT count(distinct mt.date, gr.group_num, ga.game_num) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT count(distinct mt.date, gr.group_num, ga.game_num) FROM fspa.games ga INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT count(game_id) FROM fspa.games";
        }
    }

    private function scoreSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT count(distinct mt.date, gr.group_num, ga.game_num, sc.score) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT count(distinct mt.date, gr.group_num, ga.game_num, sc.score) FROM fspa.scores sc INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT count(score_id) FROM fspa.scores";
        }
    }

    private function oldestDateSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT distinct min(mt.date) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT distinct min(mt.date) FROM fspa.meets mt INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT min(date) FROM fspa.meets";
        }
    }

    private function newestDateSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT distinct max(mt.date) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT distinct max(mt.date) FROM fspa.meets mt INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT max(date) FROM fspa.meets";
        }
    }

    private function totalScoreSQL($args = []) {
        if (isset($args['playerID'])) {
            return "SELECT sum(sc.score) FROM fspa.players pl INNER JOIN fspa.scores sc ON sc.player_id = pl.player_id WHERE pl.player_id = :playerID";
        }
        elseif (isset($args['leagueID'])) {
            return "SELECT sum(sc.score) FROM fspa.scores sc INNER JOIN fspa.games ga ON sc.game_id = ga.game_id INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id INNER JOIN fspa.meets mt ON gr.meet_id = mt.meet_id INNER JOIN fspa.seasons sn ON mt.season_id = sn.season_id INNER JOIN fspa.leagues lg ON sn.league_id = lg.league_id WHERE lg.league_id = :leagueID";
        }
        else {
            return "SELECT sum(score) FROM fspa.scores";
        }
    }
}
?>
