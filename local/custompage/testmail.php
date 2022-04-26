<?php
require(__DIR__.'/../../config.php');
global $DB;

$username  = "username";
$password  = "password";

$search    = array("{{username}}", "{{password}}");
$replace   = array($username, $password );

require_once('phpmailer/PHPMailerAutoload.php');

$data = $DB->get_record('mail_templates', array('company_id' =>2 ,'type_id' => 5));

$message = str_replace($search,$replace,$data->content ); 
//echo $testmsg;
//echo $data->subject;

// $mail = new PHPMailer();
// $mail->isSMTP();
// // $mail->Host = 'localhost';
// // $mail->SMTPAuth = false;
// // $mail->SMTPAutoTLS = false; 
// // $mail->Port = 25; 
// // $mail->isSMTP();
// $mail->SMTPAuth = true;
// $mail->SMTPSecure = 'ssl';
// $mail->Host = 'smtp.gmail.com';
// $mail->Port = '465';
// $mail->isHTML();
// // $mail->Username = 'vishnu.zewia@gmail.com';
// // $mail->Password = 'aswathy9895489005';
// $mail->Username = 'arunthomas@techversantinfo.com';
// $mail->Password = 'arun@login2020';
// $mail->SetFrom('arunthomas@techversantinfo.com');
// $mail->Subject = $data->subject;
// $mail->Body = $testmsg;
// $mail->AddAddress('aruneathakattu@gmail.com');
// $mail->Send();
// $supportuser = core_user::get_support_user();
                
// $message = get_string('emailupdatemessage', 'auth', $a);
// $subject = get_string('emailupdatetitle', 'auth', $a);
$user = $DB->get_record_sql("select * from {user} where id = 2");
$user->email = 'aruneathakattu@gmail.com';
$subject = $data->subject;
$noreplyuser = core_user::get_noreply_user();
// print_r($user);exit();

$status = email_to_user($user, $noreplyuser, $subject, $message);
print_r($status);exit();

$mail = new PHPMailer();
$mail->isSMTP();
$mail->SMTPAuth = true;

// $mail->SMTPSecure = 'none';
// $mail->Host = 'qmail.quest-global.com';
// $mail->Port = '25';
// $mail->isHTML();
// $mail->Username = 'Quester@quest-global.com';
// $mail->Password = 'Quest1234';
// $mail->SetFrom('Quester@quest-global.com');
// $mail->Subject = $data->subject;
// $mail->Body = $testmsg;
// $mail->AddAddress('arunthomas@techversantinfo.com');
// $mail->Send();

// print_r($mail);exit();

?>

.recorder{
        border: 1px solid black;
        width: 320px;
        height: 50px;
        text-align: center;
    }
    #volumeBar{
        width:200px;
        height: 10px;
        border: 1px solid black;
        position: relative;
        margin: 0 auto;
    }
    #voiceVolume{
        width: 0px;
        height: 10px;
        background: green;
    }
.tool_dataprivacy {
display:none;
}
/*#page-footer .container-fluid{
     display:none !important;
}*/

/*Jerin - to hide login page */
.loginpanel .instructions{
display:none;
}

#page-login-index .logo{
display:none;
}

#page-login-index #loginbtn {
	margin-top: 5px;
	text-transform: uppercase;
	background: #16a6ac;
	border: 1px solid #16a6ac;
	height: 50px;
	font-weight: 600;
}

#page-login-index .loginpanel{
background:none;
}

#page-login-index .loginpanel .form{
margin-left: 564px;
margin-top: 6em;
}
/* login page customizations- end */

/* hide profile from menu */

div.usermenu a[data-title^="profile"],
#nav-drawer a[data-key="myhome"] {
    display: none;
}

/*footer
.madewithmoodle{
display:none;
}*/

/*hide bottom navigation - quiz*/
.activity-navigation{
display:none;
}

/*hide general in profile field */
#id_moodle{
display:none;
}

/*hide forgot password link */
.forgetpass{
display:none;
}

/*AK edit*/
/*Version1
nav.navbar .drawer-toggle{
    background-color: #fff !important;
}
nav.navbar .drawer-toggle .nav-link{
    font-size:20px !important;
    color:#000 !important;
}
nav.navbar .navbar-brand .logo img {
    max-height: 50px !important;
}
#region-main{
overflow-x: inherit !important;
}
nav.navbar ul.navbar-nav .popover-region .popover-region-toggle{
color: #16a6ac !important;
}
#page-my-index .small-box .icon{
top: 5px !important;
right: 30px !important;
font-size: 40px !important;
color: rgba(255,255,255,0.5) !important;
}
#page-my-index .bg-1 {
    background-color: #16a6ac !important;
}
#page-my-index .bg-2 {
    background-color: #1dc1c8 !important;
}
#page-my-index .bg-3 {
    background-color: #1fcbd3 !important;
}
#page-my-index .bg-4 {
    background-color: #18d8e1 !important;
}
a {
    color: #16a6ac !important;
}
#nav-drawer .list-group .list-group-item a:hover {
    color: #16a6ac !important;
}
#nav-drawer-footer{
background-color: #16a6ac !important;
border-top: 2px solid #272c33 !important;
}
#page-my-index .small-box{
border-radius: 5px !important;
}
#page-my-index .small-box p{
font-size: 14px !important;
}
#page-my-index .small-box h3{
font-size: 24px !important;
}
#page-my-index #region-main-box #region-main .card:not([class*="dashboard-card"]) {
    border-top: solid 2px #16a6ac !important;
}
#page-my-index #region-main-box #region-main .card.mymaincontent{
border-top: 0 !important;
}
.actiondescription{
font-size:13px;
}
.iomadicon .fa img{
width:45px;
}
.nav-tabs .nav-link:focus, .usermenu:focus-within, a.dropdown-item:focus{
box-shadow: inherit !important;
}
.block_mycourses .mycourselisting .courseimage{
width:inherit !important;
}
.block_mycourses .mycourselisting .mycoursedetails{
width:inherit !important;
float:left !important;
}
.mycourseheading h4{
font-size: 16px !important;
margin-bottom: 0 !important;
line-height: 1.5 !important;
}
#sidepreopen-control {
    background-color: #16a6ac !important;
}
#sidepreopen-control:hover {
    -webkit-box-shadow: 0 0 6px #16a6ac !important;
    box-shadow: 0 0 6px #16a6ac !important;
}
#page-my-index .small-box{
display:none !important;
}
#page-footer{
background-color: #272c33 !important;
}
#nav-drawer .list-group .list-group-item a{
color:#fff !important;
}
#nav-drawer{
background-color: #16a6ac !important;
}
#nav-drawer .list-group .list-group-item:hover {
    background-color: #272c33 !important;
}
#top-footer{
display:none;
}
#dashboardheader {
    color: #272c33 !important;
}
nav.navbar ul.navbar-nav .popover-region{
display:none;
}*/


/*Version 2
nav.navbar .drawer-toggle{
    background-color: #fff !important;
}
nav.navbar .drawer-toggle .nav-link{
    font-size:20px !important;
    color:#000 !important;
}
nav.navbar .navbar-brand .logo img {
    max-height: 50px !important;
}
#region-main{
overflow-x: inherit !important;
}
nav.navbar ul.navbar-nav .popover-region .popover-region-toggle{
color: #0078d4 !important;
}
#page-my-index .small-box .icon{
top: 5px !important;
right: 30px !important;
font-size: 40px !important;
color: rgba(255,255,255,0.5) !important;
}
#page-my-index .bg-1 {
    background-color: #16a6ac !important;
}
#page-my-index .bg-2 {
    background-color: #1dc1c8 !important;
}
#page-my-index .bg-3 {
    background-color: #1fcbd3 !important;
}
#page-my-index .bg-4 {
    background-color: #18d8e1 !important;
}
a {
    color: #0078d4 !important;
}
#nav-drawer .list-group .list-group-item a:hover {
    color: #0078d4 !important;
}
#nav-drawer-footer{
background-color: #0078d4 !important;
border-top: 2px solid #272c33 !important;
}
#page-my-index .small-box{
border-radius: 5px !important;
}
#page-my-index .small-box p{
font-size: 14px !important;
}
#page-my-index .small-box h3{
font-size: 24px !important;
}
#page-my-index #region-main-box #region-main .card:not([class*="dashboard-card"]) {
    border-top: solid 2px #0078d4 !important;
}
#page-my-index #region-main-box #region-main .card.mymaincontent{
border-top: 0 !important;
}
.actiondescription{
font-size:13px;
}
.iomadicon .fa img{
width:45px;
}
.nav-tabs .nav-link:focus, .usermenu:focus-within, a.dropdown-item:focus{
box-shadow: inherit !important;
}
.block_mycourses .mycourselisting .courseimage{
width:inherit !important;
}
.block_mycourses .mycourselisting .mycoursedetails{
width:inherit !important;
float:left !important;
}
.mycourseheading h4{
font-size: 16px !important;
margin-bottom: 0 !important;
line-height: 1.5 !important;
}
#sidepreopen-control {
    background-color: #0078d4 !important;
}
#sidepreopen-control:hover {
    -webkit-box-shadow: 0 0 6px #16a6ac !important;
    box-shadow: 0 0 6px #16a6ac !important;
}
#page-my-index .small-box{
display:none !important;
}
#page-footer{
background-color: #272c33 !important;
}
#nav-drawer .list-group .list-group-item a{
color:#fff !important;
}
#nav-drawer{
background-color: #0078d4 !important;
}
#nav-drawer .list-group .list-group-item:hover {
    background-color: #272c33 !important;
}
#top-footer{
display:none;
}
#dashboardheader {
    color: #272c33 !important;
}
nav.navbar ul.navbar-nav .popover-region{
display:none;
}
.badge-info{
background-color: #0078d4 !important;
}*/


/*Version 3
nav.navbar .drawer-toggle{
    background-color: #fff !important;
}
nav.navbar .drawer-toggle .nav-link{
    font-size:20px !important;
    color:#000 !important;
}
nav.navbar .navbar-brand .logo img {
    max-height: 50px !important;
}
#region-main{
overflow-x: inherit !important;
}
nav.navbar ul.navbar-nav .popover-region .popover-region-toggle{
color: #0078d4 !important;
}
#page-my-index .small-box .icon{
top: 5px !important;
right: 30px !important;
font-size: 40px !important;
color: rgba(255,255,255,0.5) !important;
}
#page-my-index .bg-1 {
    background-color: #16a6ac !important;
}
#page-my-index .bg-2 {
    background-color: #1dc1c8 !important;
}
#page-my-index .bg-3 {
    background-color: #1fcbd3 !important;
}
#page-my-index .bg-4 {
    background-color: #18d8e1 !important;
}
a {
    color: #0078d4 !important;
}
#nav-drawer .list-group .list-group-item a:hover {
    color: #0078d4 !important;
}
#nav-drawer-footer{
background-color: #fff !important;
border-top: 2px solid #272c33 !important;
}
#page-my-index .small-box{
border-radius: 5px !important;
}
#page-my-index .small-box p{
font-size: 14px !important;
}
#page-my-index .small-box h3{
font-size: 24px !important;
}
#page-my-index #region-main-box #region-main .card:not([class*="dashboard-card"]) {
    border-top: solid 2px #0078d4 !important;
}
#page-my-index #region-main-box #region-main .card.mymaincontent{
border-top: 0 !important;
}
.actiondescription{
font-size:13px;
}
.iomadicon .fa img{
width:45px;
}
.nav-tabs .nav-link:focus, .usermenu:focus-within, a.dropdown-item:focus{
box-shadow: inherit !important;
}
.block_mycourses .mycourselisting .courseimage{
width:inherit !important;
}
.block_mycourses .mycourselisting .mycoursedetails{
width:inherit !important;
float:left !important;
}
.mycourseheading h4{
font-size: 16px !important;
margin-bottom: 0 !important;
line-height: 1.5 !important;
}
#sidepreopen-control {
    background-color: #0078d4 !important;
}
#sidepreopen-control:hover {
    -webkit-box-shadow: 0 0 6px #16a6ac !important;
    box-shadow: 0 0 6px #16a6ac !important;
}
#page-my-index .small-box{
display:none !important;
}
#page-footer{
background-color: #272c33 !important;
}
#nav-drawer .list-group .list-group-item a{
color:#fff !important;
}
#nav-drawer{
background-color: #0078d4 !important;
}
#nav-drawer .list-group .list-group-item:hover {
    background-color: #272c33 !important;
}
#top-footer{
display:none;
}
#dashboardheader {
    color: #272c33 !important;
}
nav.navbar ul.navbar-nav .popover-region{
display:none;
}
.badge-info{
background-color: #0078d4 !important;
}
#nav-drawer .list-group .list-group-item a::before{
background: #fff !important;
color: #0078d4 !important;
padding: 8px !important;
border-radius: 50% !important;
}
#nav-drawer-footer #themesettings-control{
color: #0078d4 !important;
}*/


/*Version4*/
nav.navbar .drawer-toggle{
    background-color: #fff !important;
}
nav.navbar .drawer-toggle .nav-link{
    font-size:20px !important;
    color:#000 !important;
}
nav.navbar .navbar-brand .logo img {
    max-height: 50px !important;
}
#region-main{
overflow-x: inherit !important;
}
nav.navbar ul.navbar-nav .popover-region .popover-region-toggle{
color: #16a6ac !important;
}
#page-my-index .small-box .icon{
top: 5px !important;
right: 30px !important;
font-size: 40px !important;
color: rgba(255,255,255,0.5) !important;
}
#page-my-index .bg-1 {
    background-color: #16a6ac !important;
}
#page-my-index .bg-2 {
    background-color: #1dc1c8 !important;
}
#page-my-index .bg-3 {
    background-color: #1fcbd3 !important;
}
#page-my-index .bg-4 {
    background-color: #18d8e1 !important;
}
a {
    color: #16a6ac !important;
}
#nav-drawer .list-group .list-group-item a:hover {
    color: #16a6ac !important;
}
#nav-drawer-footer{
background-color: #fff !important;
border-top: 2px solid #272c33 !important;
}
#page-my-index .small-box{
border-radius: 5px !important;
}
#page-my-index .small-box p{
font-size: 14px !important;
}
#page-my-index .small-box h3{
font-size: 24px !important;
}
#page-my-index #region-main-box #region-main .card:not([class*="dashboard-card"]) {
    border-top: solid 2px #16a6ac !important;
}
#page-my-index #region-main-box #region-main .card.mymaincontent{
border-top: 0 !important;
}
.actiondescription{
font-size:13px;
}
.iomadicon .fa img{
width:45px;
}
.nav-tabs .nav-link:focus, .usermenu:focus-within, a.dropdown-item:focus{
box-shadow: inherit !important;
}
.block_mycourses .mycourselisting .courseimage{
width:inherit !important;
}
.block_mycourses .mycourselisting .mycoursedetails{
width:inherit !important;
float:left !important;
}
.mycourseheading h4{
font-size: 16px !important;
margin-bottom: 0 !important;
line-height: 1.5 !important;
}
#sidepreopen-control {
    background-color: #16a6ac !important;
}
#sidepreopen-control:hover {
    -webkit-box-shadow: 0 0 6px #16a6ac !important;
    box-shadow: 0 0 6px #16a6ac !important;
}
#page-my-index .small-box{
display:none !important;
}
#page-footer{
background-color: #272c33 !important;
}
#nav-drawer .list-group .list-group-item a{
color:#fff !important;
}
#nav-drawer{
background-color: #16a6ac !important;
}
#nav-drawer .list-group .list-group-item:hover {
    background-color: #272c33 !important;
}
#top-footer{
display:none;
}
#dashboardheader {
    color: #272c33 !important;
}
nav.navbar ul.navbar-nav .popover-region{
display:none;
}
#nav-drawer .list-group .list-group-item a::before{
background: #fff !important;
color: #16a6ac !important;
padding: 8px !important;
border-radius: 50% !important;
}
#nav-drawer-footer #themesettings-control{
color: #16a6ac !important;
}


/*Version5
nav.navbar .drawer-toggle{
    background-color: #fff !important;
}
nav.navbar .drawer-toggle .nav-link{
    font-size:20px !important;
    color:#000 !important;
}
nav.navbar .navbar-brand .logo img {
    max-height: 50px !important;
}
#region-main{
overflow-x: inherit !important;
}
nav.navbar ul.navbar-nav .popover-region .popover-region-toggle{
color: #16a6ac !important;
}
#page-my-index .small-box .icon{
top: 5px !important;
right: 30px !important;
font-size: 40px !important;
color: rgba(255,255,255,0.5) !important;
}
#page-my-index .bg-1 {
    background-color: #16a6ac !important;
}
#page-my-index .bg-2 {
    background-color: #1dc1c8 !important;
}
#page-my-index .bg-3 {
    background-color: #1fcbd3 !important;
}
#page-my-index .bg-4 {
    background-color: #18d8e1 !important;
}
a {
    color: #16a6ac !important;
}
#nav-drawer .list-group .list-group-item a:hover {
    color: #16a6ac !important;
}
#nav-drawer-footer{
background-color: #16a6ac !important;
border-top: 2px solid #272c33 !important;
}
#page-my-index .small-box{
border-radius: 5px !important;
}
#page-my-index .small-box p{
font-size: 14px !important;
}
#page-my-index .small-box h3{
font-size: 24px !important;
}
#page-my-index #region-main-box #region-main .card:not([class*="dashboard-card"]) {
    border-top: solid 2px #16a6ac !important;
}
#page-my-index #region-main-box #region-main .card.mymaincontent{
border-top: 0 !important;
}
.actiondescription{
font-size:13px;
}
.iomadicon .fa img{
width:45px;
}
.nav-tabs .nav-link:focus, .usermenu:focus-within, a.dropdown-item:focus{
box-shadow: inherit !important;
}
.block_mycourses .mycourselisting .courseimage{
width:inherit !important;
}
.block_mycourses .mycourselisting .mycoursedetails{
width:inherit !important;
float:left !important;
}
.mycourseheading h4{
font-size: 16px !important;
margin-bottom: 0 !important;
line-height: 1.5 !important;
}
#sidepreopen-control {
    background-color: #16a6ac !important;
}
#sidepreopen-control:hover {
    -webkit-box-shadow: 0 0 6px #16a6ac !important;
    box-shadow: 0 0 6px #16a6ac !important;
}
#page-my-index .small-box{
display:none !important;
}
#page-footer{
background-color: #272c33 !important;
}
#nav-drawer .list-group .list-group-item a{
color:#16a6ac !important;
}
#nav-drawer{
background-color: #fff !important;
}
#nav-drawer .list-group .list-group-item:hover {
    background-color: #272c33 !important;
}
#top-footer{
display:none;
}
#dashboardheader {
    color: #272c33 !important;
}
nav.navbar ul.navbar-nav .popover-region{
display:none;
}
#nav-drawer .list-group .list-group-item a::before{
background: #16a6ac !important;
color: #fff !important;
padding: 8px !important;
border-radius: 50% !important;
}
#nav-drawer-footer #themesettings-control{
color: #fff !important;
}
#nav-drawer .list-group .list-group-item{
border-bottom: 1px solid #16a6ac !important;
}
#nav-drawer .list-group + .list-group{
border-top:0 !important;
}
.has-logo{
width:100% !important;
}
.has-logo .d-sm-inline{
display:table !important;
margin: 0 auto;
}*/


/*Version6
nav.navbar .drawer-toggle{
    background-color: #fff !important;
}
nav.navbar .drawer-toggle .nav-link{
    font-size:20px !important;
    color:#000 !important;
}
nav.navbar .navbar-brand .logo img {
    max-height: 50px !important;
}
#region-main{
overflow-x: inherit !important;
}
nav.navbar ul.navbar-nav .popover-region .popover-region-toggle{
color:#0078d4 !important;
}
#page-my-index .small-box .icon{
top: 5px !important;
right: 30px !important;
font-size: 40px !important;
color: rgba(255,255,255,0.5) !important;
}
#page-my-index .bg-1 {
    background-color: #16a6ac !important;
}
#page-my-index .bg-2 {
    background-color: #1dc1c8 !important;
}
#page-my-index .bg-3 {
    background-color: #1fcbd3 !important;
}
#page-my-index .bg-4 {
    background-color: #18d8e1 !important;
}
a {
    color: #0078d4 !important;
}
#nav-drawer .list-group .list-group-item a:hover {
    color: #0078d4 !important;
}
#nav-drawer-footer{
background-color: #0078d4 !important;
border-top: 2px solid #272c33 !important;
}
#page-my-index .small-box{
border-radius: 5px !important;
}
#page-my-index .small-box p{
font-size: 14px !important;
}
#page-my-index .small-box h3{
font-size: 24px !important;
}
#page-my-index #region-main-box #region-main .card:not([class*="dashboard-card"]) {
    border-top: solid 2px #0078d4 !important;
}
#page-my-index #region-main-box #region-main .card.mymaincontent{
border-top: 0 !important;
}
.actiondescription{
font-size:13px;
}
.iomadicon .fa img{
width:45px;
}
.nav-tabs .nav-link:focus, .usermenu:focus-within, a.dropdown-item:focus{
box-shadow: inherit !important;
}
.block_mycourses .mycourselisting .courseimage{
width:inherit !important;
}
.block_mycourses .mycourselisting .mycoursedetails{
width:inherit !important;
float:left !important;
}
.mycourseheading h4{
font-size: 16px !important;
margin-bottom: 0 !important;
line-height: 1.5 !important;
}
#sidepreopen-control {
    background-color: #0078d4 !important;
}
#sidepreopen-control:hover {
    -webkit-box-shadow: 0 0 6px #0078d4 !important;
    box-shadow: 0 0 6px #0078d4 !important;
}
#page-my-index .small-box{
display:none !important;
}
#page-footer{
background-color: #272c33 !important;
}
#nav-drawer .list-group .list-group-item a{
color:#0078d4 !important;
}
#nav-drawer{
background-color: #fff !important;
}
#nav-drawer .list-group .list-group-item:hover {
    background-color: #272c33 !important;
}
#top-footer{
display:none;
}
#dashboardheader {
    color: #272c33 !important;
}
nav.navbar ul.navbar-nav .popover-region{
display:none;
}
#nav-drawer .list-group .list-group-item a::before{
background: #0078d4 !important;
color: #fff !important;
padding: 8px !important;
border-radius: 50% !important;
}
#nav-drawer-footer #themesettings-control{
color: #fff !important;
}
#nav-drawer .list-group .list-group-item{
border-bottom: 1px solid #0078d4 !important;
}
#nav-drawer .list-group + .list-group{
border-top:0 !important;
}
.has-logo{
width:100% !important;
}
.has-logo .d-sm-inline{
display:table !important;
margin: 0 auto;
}
.badge-info{
background-color: #0078d4 !important;
}*/

/*navbar-gurukul-logo*/
nav.navbar{
background-image: url("//202.88.246.92/iomad_dev/pluginfile.php/1/theme_moove/logo/1610360745/compain-logo-1.png");
background-repeat: no-repeat;
background-position: 95% center;
background-size: 90px;
}


/*Common*/
.iomadlink_container > a{
background: #fff;
border-radius: 5px;
transition:all 0.3s ease 0.0s;
}
.iomadlink_container > a:hover{
box-shadow: 0 0 5px rgba(0,0,0,0.1);
}

/*AK edits 12-02-2021*/

.form-inline .form-control,.form-inline .custom-select,.form-inline .filemanager{
width:75% !important;
}
.mform fieldset.collapsible legend a.fheader{
text-transform: uppercase;
font-size: 20px;
text-decoration:none;
}
<!-- .fitem span i,.fdescription i{
display:none;
} -->
<!-- #id_category_1 .fitem label::after,.fdescription::after,label.col-form-label::after{
content:'*';
color:red;
} -->
.qtext{
font-weight:bold;
}
.path-mod-quiz #mod_quiz_navblock .qnbutton.answersaved .trafficlight{
background-color: #16a6ac !important;
}

/*...AK edits 23-03-2021...*/

.snap-photo{
border: 0;
padding: 5px 10px;
border-radius: 5px;
}
#save{
background: #16a6ac;
color: #fff;
}
.video-container,.canvas-container{
border: 1px solid #ccc;
padding: 5px;
}
.imp-div{
background:#eee;
padding:30px;
}
.imp-ul{
border-top:1px solid #ccc;
padding-top:10px;
}
.imp-ul li{
margin-bottom:10px;
}
#page-mod-quiz-view #page .quizattempt{
text-align:left;
}
.quizstartbuttondiv button{
background:#16a6ac !important;
border:0 !important;
color:#fff !important;
}

.submitbtns .controls .singlebutton button{
background:#16a6ac !important;
border:0 !important;
color:#fff !important;
}
.quizinfo p{
display:none;
}

#page-mod-quiz-view  .continuebutton {
display:none;
}

#page-mod-quiz-attempt .outcome{
display:none;
}

#page-mod-quiz-attempt #connection-error{
display:none;
}

#page-mod-quiz-attempt .prompt{
display:none;
}
/* drivestart day css  */
#id_startdate_day, #id_enddate_day, #id_startdate_month, #id_enddate_month, #id_startdate_year, #id_enddate_year, #id_startdate_hour, #id_enddate_hour,  #id_startdate_minute, #id_enddate_minute {
width: 100% !important;
}

/*----------------------------------------------*/


.flex-wrapper {
  display: flex;
  min-height: 100vh;
  flex-direction: column;
  justify-content: space-between;
}

/* */
#quiz-timer{position: fixed;
font-size:39px;
}

/*
.sticky2 {
 position: fixed;
}


#quiz-timer{
font-size: 1.9375rem;
}
*/

/*----------------------------------------------*/

#id_profile_field_urdcDOB_day,#id_profile_field_urdcDOB_month,#id_profile_field_urdcDOB_year{
    width: 100% !important;
}



<span class="float-sm-right text-nowrap">
            <abbr class="initialism text-danger" title="Required"><i class="icon fa slicon-exclamation text-danger fa-fw " title="Required" aria-label="Required"></i></abbr>
            
            
        </span>


        <label class="col-form-label d-inline " for="id_profile_field_urdcbranch">
                    Branch
                </label>