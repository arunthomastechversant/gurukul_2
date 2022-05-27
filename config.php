<?php  // Moodle configuration file

unset($CFG);
global $CFG;
$CFG = new stdClass();

$CFG->dbtype    = 'mysqli';
$CFG->dblibrary = 'native';
$CFG->dbhost    = 'localhost';
$CFG->dbname    = 'gurukul_2';
$CFG->dbuser    = 'root';
$CFG->dbpass    = 'Techv@1234';
$CFG->prefix    = 'mdl_';

$CFG->dboptions = array (
  'dbpersist' => 0,
  'dbport' => '3306',
  'dbsocket' => '',
  'dbcollation' => 'utf8mb4_unicode_ci',
  // 'dbssl' => true,
  // 'dbcertificate' => '/var/www/html/DigiCertGlobalRootCA.crt.pem',
);

// $CFG->dbhost    = 'localhost';
// $CFG->dbname    = 'gurukul_2';
// $CFG->dbuser    = 'root';
// $CFG->dbpass    = 'Techv@12?34';

// $CFG->dbhost    = 'epitome-assessment.mysql.database.azure.com';
// $CFG->dbname    = 'epitome_assessment';
// $CFG->dbuser    = 'epitomeAssessmentAdim';
// $CFG->dbpass    = '5T{"=k#EHnG(>83v';

// $CFG->dbhost    = '172.20.0.1';
// $CFG->dbname    = 'gurukul_2';
// $CFG->dbuser    = 'gurukul';
// $CFG->dbpass    = 'jIPROcwBkx3dognvdHlP';

// $CFG->dbhost    = 'epitome-assessment.mysql.database.azure.com';
// $CFG->dbname    = 'epitome_assessment';
// $CFG->dbuser    = 'epitomeAssessmentAdim';
// $CFG->dbuser    = 'gurukul';
// $CFG->dbpass    = 'Guruakls1237dhkdui2j';


$CFG->wwwroot   = 'http://localhost/gurukul_2';
$CFG->dataroot  = '/var/www/datagurukul_2';
$CFG->admin     = 'admin';

$CFG->directorypermissions = 0777;

require_once(__DIR__ . '/lib/setup.php');

// There is no php closing tag in this file,
// it is intentional because it prevents trailing whitespace problems!

// @error_reporting(E_ALL | E_STRICT);   // NOT FOR PRODUCTION SERVERS!
// @ini_set('display_errors', '1');         // NOT FOR PRODUCTION SERVERS!
// $CFG->debug = (E_ALL | E_STRICT);   // === DEBUG_DEVELOPER - NOT FOR PRODUCTION SERVERS!
// $CFG->debugdisplay = 1;              // NOT FOR PRODUCTION SERVERS!