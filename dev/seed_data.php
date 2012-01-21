<?php
define("BASEDIR", __DIR__ . '/../');
require '../lib/rb.php';
require '../lib/Singleton.php';
require '../lib/Cfg.php';
require '../lib/parseCSV.php';
R::setup(Cfg::instance()->db_dsn, Cfg::instance()->db_username, Cfg::instance()->db_password);

$db_name = substr(Cfg::instance()->db_dsn, strrpos(Cfg::instance()->db_dsn, '=') + 1);
echo("DROPPING DATABASE AND CREATING SEED DATA FOR DB NAME: " .$db_name);

R::exec('drop database ' .$db_name);
R::exec('create database ' .$db_name);
R::exec('use ' .$db_name);

// ===================================

$seed = new seedData(BASEDIR . 'dev/csv/XXXXX.csv', 'xxxxxx');
$seed->import();

class seedData {
    private $file;
    private $type;

    public function __construct($file, $type) {
        $this->file = $file;
        $this->type = $type;
    }

    public function import() {
        $csv = new parseCSV($this->file);
        foreach ($csv->data as $item) {
            $bean = R::dispense($this->type);
            $bean->import($item);
            R::store($bean);
        }
    }

}
