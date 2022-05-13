<?php
require_once('../config.php'); 
require_login();
// require_user();
global $DB,$CFG,$PAGE,$USER,$SESSION,$PAGE,$OUTPUT;
$returnurl = $CFG->wwwroot.'/login/logout.php?sesskey='.sesskey();
$companyid = $DB->get_record('company_users',array('userid' => $USER->id))->companyid;
$context = context_user::instance($companyid, MUST_EXIST);
$context1 = context_system::instance();
$fs = get_file_storage();
if ($files = $fs->get_area_files($context->id, 'local_custompage', 'welcomepage',false, 'sortorder', false)) 
{
   
    foreach ($files as $file) 
    { 
        $imagepath = moodle_url::make_pluginfile_url($context->id, 'local_custompage', 'welcomepage', $file->get_itemid(), $file->get_filepath(), $file->get_filename());
    }
    $imagepath = $imagepath->__toString();
}
if ($files = $fs->get_area_files($context->id, 'local_custompage', 'assessmentpage',false, 'sortorder', false)) 
{
   
    foreach ($files as $file) 
    { 
        $assessment = moodle_url::make_pluginfile_url($context->id, 'local_custompage', 'assessmentpage', $file->get_itemid(), $file->get_filepath(), $file->get_filename());
    }
    $assessment = $assessment->__toString();
}
if ($files = $fs->get_area_files($context1->id, 'local_custompage', 'gurukullogo',false, 'sortorder', false)) 
{
    foreach ($files as $key => $file) 
    {
        $gurukullogo = moodle_url::make_pluginfile_url($context1->id, 'local_custompage', 'gurukullogo', $file->get_itemid(), $file->get_filepath(), $file->get_filename());
    }
    $gurukullogo = $gurukullogo->__toString();
}
// print_r($gurukullogo);exit();
$data = $DB->get_record('cms', array('company_id'=>$companyid , 'type' => 'welcome'));
$heading = $data->heading;
$content = $data->content; 
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            .welcome-banner{
                background:linear-gradient(to bottom right,rgba(67, 247, 255, 0.3), rgba(22, 166, 172,0.8)), url(<?php echo $imagepath ?>);
            }
        </style>
        <title>QuESTer:Welcome</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../user/style.css"/>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    </head>
    <body>
        <div class="welcome-banner d-flex align-items-center">
            <div class="welcome_wrapper bg-white ps-2 pe-2 pt-5 pb-5 text-center">
                <h3><?php echo $heading ?> 
		<!-- <a href=""><img class="w_gurukul_logo" src="<?php echo $gurukullogo ?>" alt="Gurukul Logo"/></a> -->
		</h3>
                <p><?php echo $content ?></p>
                <img class="mt-4 w-75 border-top pt-4" src="<?php echo $assessment ?>" alt="for_participant" />
                <!-- <a href="../user/privacy.php"><button class="text-center log-btn welcome-btn pt-2 pb-2 fnt-w-600 rounded border-0 w-100 text-white mt-4" type="submit">Enter Your Details</button></a> -->
                <a href="../user/privacy.php"><button class="text-center log-btn welcome-btn pt-2 pb-2 fnt-w-600 rounded border-0 w-100 text-white mt-4" type="submit">Privacy Notice</button></a>

            </div>
        </div>
        <!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    </body>
</html>

