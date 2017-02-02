<?php
namespace FSPA;

class DataParser {
    public $infile;
    private $infile_handle;
    public $outfile;
    private $outfile_handle;
    public $ok = true;
    public $messages = [];
    private $parsed_data = [];

    public function __construct($file) {
        $this->infile = $file;
    }

    public function createSQL($outfile = '/tmp/db.sql') {
        $this->outfile = $outfile;
        if (!$this->infile) {
            $this->ok = false;
            array_push($this->messages, "No input file provided");
            return;
        }

        $this->infile_handle = fopen($this->infile, 'r');
        if (!$this->infile_handle) {
            $this->ok = false;
            array_push($this->messages, "Unable to open input file for reading");
            return;
        }

        $junk = explode("\t", rtrim(fgets($this->infile_handle))); // read away header line
        $this->startSQLFile();
        $this->parseInputToStructs();
        $this->writePlayers();
        $this->writeLocations();
        $this->writeLeagues();
        $this->writeSeasons();
        $this->writeMachines();
        $this->writeMeets();
        $this->writeGroups();
        $this->writeGames();
        $this->writeScores();

        $this->finishSQLFile();
    }

    public function startSQLFile() {
        $this->outfile_handle = fopen($this->outfile, 'w');
        if (!$this->outfile_handle) {
            $this->ok = false;
            array_push($this->messages, "Unable to open output file for writing");
            return;
        }
        $sql = <<<SQL

-- set up DB
DROP DATABASE IF EXISTS fspa;
CREATE DATABASE fspa;
USE fspa;
GRANT SELECT, INSERT, UPDATE, DELETE ON `fspa`.* TO 'fspa-web'@'%';

-- create tables
CREATE TABLE locations (
    location_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    name TEXT NOT NULL
);

CREATE TABLE machines (
    machine_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    location_id INTEGER UNSIGNED NOT NULL,
    name TEXT NOT NULL,
    CONSTRAINT machines_location_id_fkey FOREIGN KEY (location_id) REFERENCES fspa.locations (location_id)
);

CREATE TABLE players (
    player_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    name TEXT NOT NULL
);

CREATE TABLE leagues (
    league_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    location_id INTEGER UNSIGNED NOT NULL,
    name TEXT NOT NULL,
    CONSTRAINT leagues_location_id_fkey FOREIGN KEY (location_id) REFERENCES fspa.locations (location_id)
);

CREATE TABLE seasons (
    season_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    league_id INTEGER UNSIGNED NOT NULL,
    name TEXT NOT NULL,
    CONSTRAINT seasons_league_id_fkey FOREIGN KEY (league_id) REFERENCES fspa.leagues (league_id)
);

CREATE TABLE meets (
    meet_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    date DATE NOT NULL,
    season_id INTEGER UNSIGNED NOT NULL,
    CONSTRAINT meets_season_id_fkey FOREIGN KEY (season_id) REFERENCES fspa.seasons (season_id)
);

CREATE TABLE groups (
    group_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    group_num TINYINT UNSIGNED NOT NULL,
    meet_id INTEGER UNSIGNED NOT NULL,
    CONSTRAINT groups_meet_id_fkey FOREIGN KEY (meet_id) REFERENCES fspa.meets (meet_id)
);

CREATE TABLE games (
    game_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    game_num TINYINT UNSIGNED NOT NULL,
    group_id INTEGER UNSIGNED NOT NULL,
    CONSTRAINT games_group_id_fkey FOREIGN KEY (group_id) REFERENCES fspa.groups (group_id)
);

CREATE TABLE scores (
    score_id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
    player_id INTEGER UNSIGNED NOT NULL,
    machine_id INTEGER UNSIGNED NOT NULL,
    game_id INTEGER UNSIGNED NOT NULL,
    score BIGINT UNSIGNED NOT NULL,
    flags TINYINT UNSIGNED NOT NULL,
    tiebreaker TINYINT UNSIGNED NOT NULL,
    CONSTRAINT scores_player_id_fkey FOREIGN KEY (player_id) REFERENCES fspa.players (player_id),
    CONSTRAINT scores_machine_id_fkey FOREIGN KEY (machine_id) REFERENCES fspa.machines (machine_id),
    CONSTRAINT scores_game_id_fkey FOREIGN KEY (game_id) REFERENCES fspa.games (game_id)
);

SQL;
        fputs($this->outfile_handle, $sql);
    }

    public function parseInputToStructs() {
        while (!feof($this->infile_handle)) {
            $line = explode("\t", rtrim(fgets($this->infile_handle)));

            # Auto-vivify substructs
            if (!isset($this->parsed_data['locations'])) {
                $this->parsed_data['players'] = [];
                $this->parsed_data['locations'] = [];
                $this->parsed_data['leagues'] = [];
                $this->parsed_data['seasons'] = [];
                $this->parsed_data['machines'] = [];
                $this->parsed_data['meets'] = [];
                $this->parsed_data['groups'] = [];
                $this->parsed_data['games'] = [];
                $this->parsed_data['scores'] = [];
            }

            # Consume the line
            $this->parsed_data['players'][$line[0]] = 1;
            $this->parsed_data['locations'][$line[3]] = 1;
            $this->parsed_data['leagues'][ implode('|', [ $line[3], $line[4] ]) ] = 1;
            $this->parsed_data['seasons'][ implode('|', [ $line[4], $line[5] ]) ] = 1;
            $this->parsed_data['machines'][ implode('|', [ $line[1], $line[3] ]) ] = 1;
            $this->parsed_data['meets'][ implode('|', [ $line[2], $line[5] ]) ] = 1;
            $this->parsed_data['groups'][ implode('|', [ $line[7], $line[2], $line[5] ]) ] = 1;
            $this->parsed_data['games'][ implode('|', [ $line[8], $line[7], $line[2], $line[5] ]) ] = 1;
            $this->parsed_data['scores'][ implode('|', [ $line[0], $line[1], $line[3], $line[8], $line[7], $line[2], $line[5], $line[4] ]) ] = [ 'score' => $line[9], 'flags' => $line[10], 'tiebreaker' => $line[11] ];
        }
    }

    public function writePlayers() {
        $sql = "\n-- Players";
        foreach ($this->parsed_data['players'] as $player => $junk) {
            $sql .= "
INSERT INTO fspa.players (name) VALUES (
    '" . $this->escape($player) . "'
);\n";
        }
        fputs($this->outfile_handle, $sql);
    }

    public function writeLocations() {
        $sql = "\n-- Locations";
        foreach ($this->parsed_data['locations'] as $location => $junk) {
            $sql .= "
INSERT INTO fspa.locations (name) VALUES (
    '" . $this->escape($location) . "'
);\n";
        }
        fputs($this->outfile_handle, $sql);
    }

    public function writeLeagues() {
        $sql = "\n-- Leagues";
        foreach ($this->parsed_data['leagues'] as $league_data => $junk) {
            list($location, $league) = explode('|', $league_data);
            $sql .= "
INSERT INTO fspa.leagues (location_id, name) VALUES (
    (SELECT location_id FROM fspa.locations WHERE name = '" . $this->escape($location) . "'),
    '" . $this->escape($league) . "'
);\n";
        }
        fputs($this->outfile_handle, $sql);
    }

    public function writeSeasons() {
        $sql = "\n-- Seasons";
        foreach ($this->parsed_data['seasons'] as $season_data => $junk) {
            list($league, $name) = explode('|', $season_data);
            $sql .= "
INSERT INTO fspa.seasons (league_id, name) VALUES (
    (SELECT league_id FROM fspa.leagues WHERE name = '" . $this->escape($league) . "'),
    '" . $this->escape($name) . "'
);\n";
        }
        fputs($this->outfile_handle, $sql);
    }

    public function writeMachines() {
        $sql = "\n-- Machines";
        foreach ($this->parsed_data['machines'] as $machine_data => $junk) {
            list($name, $location) = explode('|', $machine_data);
            $sql .= "
INSERT INTO fspa.machines (location_id, name) VALUES (
    (SELECT location_id FROM fspa.locations WHERE name = '" . $this->escape($location) . "'),
    '" . $this->escape($name) . "'
);\n";
        }
        fputs($this->outfile_handle, $sql);
    }

    public function writeMeets() {
        $sql = "\n-- Meets";
        foreach ($this->parsed_data['meets'] as $meet_data => $junk) {
            list($date, $season) = explode('|', $meet_data);
            $sql .= "
INSERT INTO fspa.meets (season_id, date) VALUES (
    (SELECT season_id FROM fspa.seasons WHERE name = '" . $this->escape($season) . "'),
    '$date'
);\n";
        }
        fputs($this->outfile_handle, $sql);
    }

    public function writeGroups() {
        $sql = "\n-- Groups";
        foreach ($this->parsed_data['groups'] as $group_data => $junk) {
            list($group_num, $meet, $season) = explode('|', $group_data);
            $sql .= "
INSERT INTO fspa.groups (meet_id, group_num) VALUES (
    (
        SELECT m.meet_id
        FROM fspa.meets m
        INNER JOIN fspa.seasons s ON m.season_id = s.season_id
        WHERE m.date = '$meet'
        AND s.name = '" . $this->escape($season) . "'
    ),
    $group_num
);\n";
        }
        fputs($this->outfile_handle, $sql);
    }

    public function writeGames() {
        $sql = "\n-- Games";
        foreach ($this->parsed_data['games'] as $game_data => $junk) {
            list($game_num, $group_num, $meet, $season) = explode('|', $game_data);
            $sql .= "
INSERT INTO fspa.games (group_id, game_num) VALUES (
    (
        SELECT g.group_id FROM fspa.groups g
        INNER JOIN fspa.meets m ON g.meet_id = m.meet_id
        INNER JOIN fspa.seasons s ON m.season_id = s.season_id
        WHERE g.group_num = $group_num
        AND m.date = '$meet'
        AND s.name = '" . $this->escape($season) . "'
    ),
    $game_num
);\n";
        }
        fputs($this->outfile_handle, $sql);
    }

    public function writeScores() {
        $sql = "\n-- Scores";
        foreach ($this->parsed_data['scores'] as $score_fields => $score_data) {
            list($player, $machine, $location, $game_num, $group_num, $meet, $season, $league) = explode('|', $score_fields);
            # Man this is ugly, but such is life with denormalized data
            $sql .= "
INSERT INTO fspa.scores (player_id, machine_id, game_id, score, flags, tiebreaker) VALUES (
    (SELECT player_id FROM fspa.players WHERE name = '" . $this->escape($player) . "'),
    (
        SELECT m.machine_id
        FROM fspa.machines m
        INNER JOIN fspa.locations l ON m.location_id = l.location_id
        WHERE m.name = '" . $this->escape($machine) . "'
        AND l.name = '" . $this->escape($location) . "'
    ),
    (
        SELECT ga.game_id
        FROM fspa.games ga
        INNER JOIN fspa.groups gr ON ga.group_id = gr.group_id
        INNER JOIN fspa.meets m ON gr.meet_id = m.meet_id
        INNER JOIN fspa.seasons s ON m.season_id = s.season_id
        INNER JOIN fspa.leagues lg ON s.league_id = lg.league_id
        INNER JOIN fspa.locations lo ON lg.location_id = lo.location_id
        WHERE ga.game_num = $game_num
        AND gr.group_num = $group_num
        AND m.date = '$meet'
        AND s.name = '" . $this->escape($season) . "'
        AND lg.name = '" . $this->escape($league) . "'
        AND lo.name = '" . $this->escape($location) . "'
    ),
    " . $score_data['score'] . ",
    " . $score_data['flags'] . ",
    " . $score_data['tiebreaker'] . "
);\n";
        }
        fputs($this->outfile_handle, $sql);
    }

    public function finishSQLFile() {
        $sql = <<<SQL

-- finishing operations

SQL;
        fputs($this->outfile_handle, $sql);
        fclose($this->outfile_handle);
    }

    private function escape($str) {
        return str_replace("'", "''", $str);
    }
}
?>
