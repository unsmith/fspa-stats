<?php
namespace FSPA\Model;

class Group extends Base {
    private $group_id = null;
    private $group_num = null;
    private $meet_id = null;

    private $meet = null;
    private $games = []; # Likely exactly 4 game records for that group's play

    public function __construct() {
    }

    public function getGroupNum() {
        return $this->group_num;
    }

    public function newFromRow($row = null) {
        $group = new Group();
        if (isset($row)) {
            $group->group_id = $row['group_id'];
            $group->group_num = $row['group_num'];
            $group->meet_id = $row['meet_id'];
        }
        return $group;
    }

    public function fetchByID($groupID) {
        $dbo = new \FSPA\DB();
        $dbh = $dbo->dbh;
        $sth = $dbh->prepare("SELECT * FROM fspa.group WHERE group_id = :group_id");
        $sth->execute([ ':group_id' => $groupID ]);
        $row = $sth->fetch();
        if ($row === false) {
            return new Group();
        }
        else {
            return Group::newFromRow($row);
        }
    }

    public function fetchAll() {
        $dbo = new \FSPA\DB();
        $dbh = $dbo->dbh;
        $sth = $dbh->prepare("SELECT * FROM fspa.groups");
        $sth->execute();

        $groups = [];
        while ($row = $sth->fetch()) {
            array_push($groups, Group::newFromRow($row));
        }
        return $groups;
    }
}
?>
