<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'gurukul';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'Arun@t';
$CFG->prefix    = 'mdl_';
$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_unicode_ci',
);

$CFG->wwwroot   = 'http://localhost/gurukul';
$CFG->dataroot  = '/var/www/gurukuldata';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');


// @error_reporting(E_ALL | E_STRICT);   
// @ini_set('display_errors', '1');       
// $CFG->debug = (E_ALL | E_STRICT);   
// $CFG->debugdisplay = 1;              
// $CFG->debugusers = '2';


