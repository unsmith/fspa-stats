<?php
namespace FSPA\Model;

class Base {
    protected $dbo;

    public function __construct() {
        $this->dbo = new \FSPA\DB();
    }
}
?>
