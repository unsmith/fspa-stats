<?php
namespace FSPA;

# DB class
class DB {
    public static $dbh = null;
    public $connected = false;
    public $error = null;

    public function __construct() {
        if (isset($this->dbh)) {
            return $this->dbh;
        }

        try {
            $this->dbh = new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS, [ \PDO::ATTR_PERSISTENT => DB_USE_PERSISTENT ]);
            $this->dbh->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->connected = true;
        }
        catch (PDOException $e) {
            $this->connected = false;
            $this->error = $e->getMessage();
        }
    }
}
?>
