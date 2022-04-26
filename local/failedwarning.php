<?php
require_once('../config.php'); 
require_login();
// require_user();
global $DB,$USER;
$key = optional_param('key', '', PARAM_RAW);
$returnurl = $CFG->wwwroot.'/login/logout.php?sesskey='.sesskey();
// print_r($key);exit();
if($key == 'camfindwitherror'){
    $msg = 'Need to enable Webcam';
    $errormsg = 'In your browser currently webcam access is denied please allow webcam access and login again...! ';
}else if($key == 'scriptwitherror'){
    $msg = 'Need to enable Browser Javascript Feature';
    $errormsg = 'In your Browser currently javacript feature is disabled please turn on the javascript feature and login again...!';
}else{
    $msg = 'Something went wrong';
    $errormsg = 'Please logout and try again';
}
$imagepath = "";
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            .thank-bg{
                background: url(<?php echo $imagepath ?>);
            }
        </style>
        <title>QuESTer:Warning</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../theme/moove/templates/core/style.css"/>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    </head>
    <body>
        <div class="thankyou-wrapper">
            <div class="thank-bg"></div>
             <h1 class="fnt-w-700 text-center pt-5 pb-3"><?php echo $msg ?></h1>
            <p class="thank-para m-auto text-center pe-3 ps-3"><?php echo $errormsg ?></p>
            <a href="<?php echo $returnurl ?>"><button class="text-center log-btn go-back pt-2 pb-2 fnt-w-600 rounded border-0 w-100 text-white mt-4" type="button">Logout</button></a>
        </div>
        <!-- JavaScript Bundle with Popper -->
    </body>
</html>
