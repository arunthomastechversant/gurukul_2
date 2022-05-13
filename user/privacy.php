<?php
require_once('../config.php'); 
require_login();
// require_user();
$returnurl = $CFG->wwwroot.'/login/logout.php?sesskey='.sesskey();
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            .welcome-banner{
                background:linear-gradient(to bottom right,rgba(67, 247, 255, 0.3), rgba(22, 166, 172,0.8)), url(<?php echo $imagepath ?>);
            }
            .privacy_wrapper{
                background:#fff;
                padding: 30px;
                max-width: 95%;
                margin: 0 auto;
                box-shadow: 0 0 10px rgba(0,0,0,0.3);
            }
            .privacy_wrapper h4{
                border-bottom: 2px solid #ccc;
                margin-bottom: 30px;
                padding-bottom: 10px;
            }
            .privacy_wrapper ul{
                list-style:disc;
                height: 400px;
                overflow-y: auto;
                scrollbar-width: thin;
            }
            .privacy_wrapper ul li{
                margin-bottom: 10px;
            }
        </style>
        <title>QuESTer:Privacy Notice</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../user/style.css"/>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    </head>
    <body>
    <div class="d-flex align-items-center vh-100" style="background: #17a6ac;">
            <div class="privacy_wrapper"><h4><i class="icon fa slicon-exclamation text-danger fa-fw " title="Required" aria-label="Required"></i> Privacy Notice</h4>
                <ul>
                    <li>QuEST Global, including its affiliated companies and subsidiaries, will process the personal information about you, to provide talent assessment services to you or our client (this may impact you, for example, where you are the employee, prospective employee or candidate of our client), monitor your activities during the assessments to identify possible suspicious activity and malpractices, administer and manage the talent assessment services and conduct data analytics.</li>
                    <li>The information we collect about you may include the following: </br>Basic identification information, such as your title, first and last name, date of birth, gender, academic details, skills and work experience, contact details, address and proof of identification;</li>
                    <li>Technical Information, such as public IP address, time and date of access, browser activities and location tracking, browser settings, device information and log-in identification data;</li>
                    <li>Assessment information, such as your answers, test duration and assessment results;</li>
                    <li>Physical attributes, such as your appearance (including capturing your images and voice through live & recorded video feed), physical health condition and ethnicity.</li>
                    <li>QuEST Global as a service provider does not employ systems based solely on automated decision-making processes and therefore does not make such decisions that have legal or similar significant effects on you. The ultimate decision as to whether you will receive a position for a program, for example, remains with our clients. However, QuEST Global may later decide automatically based on your final assessment results whether to move forward with your application. Please note that QuEST Global deploys facial recognition technology for identity verification purpose only, and we do not analyse your facial expressions or otherwise exploit physical attributes shown on screen during a proctored assessment. If you wish to obtain human intervention in the automated decisions and/or profiling made or contest a decision made in this context, please contact privacy@quest-global.com directly.</li>
                    <li>We will also share your personal information with relevant third parties, such as assessors, proctors, invigilators, training providers and third-party data center provider where necessary to enable us to provide, administer and manage the talent assessment services.</li>
                    <li>Further details on how we use your personal information can be found in the Privacy Notice on our website at https://www.quest-global.com/privacy.aspx . Please read the full Privacy Notice to ensure you understand how we collect and use your personal information.</li>
                </ul>
                <div class="d-sm-flex justify-content-around">
                    <a href="../user/edit.php"><button class="text-center log-btn pt-2 pb-2 ps-4 pe-4 fnt-w-600 rounded border-0 w-100 text-white mt-4" type="submit">Agree and Proceed</button></a>
                    <a href="<?php echo $returnurl ?>"><button class="text-center log-btn pt-2 pb-2 ps-4 pe-4 fnt-w-600 rounded border-0 w-100 text-white mt-4" type="submit">Disagree and Logout</button></a>
                </div>
            </div>
        </div>
        <!-- JavaScript Bundle with Popper -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    </body>
</html>

