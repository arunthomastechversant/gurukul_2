<?php
// This file is part of Techversant Api moodle plugin

/**
 * This library is Techversant Api Login handler.
 *
 * Redirect here for saml request and response purpose
 *
 * @copyright   2022  Techversant
 * @category    authentication
 * @package     techapi
 */


require(__DIR__ . '/../../config.php');


global $CFG, $USER, $SESSION;
global $_POST, $_GET, $_SERVER;

$quizurl = $CFG->wwwroot.'/auth/techapi/success.php';

if (isset($_POST['username']) && isset($_POST['email']) ) {
    header('Location: '.$quizurl); 
}