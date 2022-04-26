<?php
require_once('../config.php'); 
require_login();
//require_user();
global $DB,$CFG,$PAGE,$USER,$SESSION,$PAGE,$OUTPUT;
$returnurl = $CFG->wwwroot.'/login/logout.php?sesskey='.sesskey();
$companyid = $DB->get_record('company_users',array('userid' => $USER->id))->companyid;
$context = context_user::instance($companyid, MUST_EXIST);
$fs = get_file_storage();
if ($files = $fs->get_area_files($context->id, 'local_custompage', 'thankyoupage',false, 'sortorder', false)) 
{
   
    foreach ($files as $file) 
    { 
        $imagepath = moodle_url::make_pluginfile_url($context->id, 'local_custompage', 'thankyoupage', $file->get_itemid(), $file->get_filepath(), $file->get_filename());
    }
    $imagepath = $imagepath->__toString();
}
$data = $DB->get_record('cms', array('company_id'=>$companyid , 'type' => 'thankyou'));
$heading = $data->heading;
$content = $data->content; 
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            .thank-bg{
                background: url(<?php echo $imagepath ?>);
            }
        </style>
        <title>QuESTer:Thankyou</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="../local/style.css"/>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
        <!-- CSS only -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    </head>
    <body>
        <div class="thankyou-wrapper">
            <div class="thank-bg"></div>
            <h1 class="fnt-w-700 text-center pt-5 pb-3"><?php echo $heading ?></h1>
            <p class="thank-para m-auto text-center pe-3 ps-3 border-bottom pb-3"><?php echo $content ?></p>
            <!-- <button class="text-center log-btn go-back pt-2 pb-2 fnt-w-600 rounded border-0 w-100 text-white mt-4" type="button">Go Back</button> -->
            <div class="feed-comments">
            <h3 class="fnt-w-700 text-center pt-2 pb-3">Please Provide Feedback</h3>
            <form action="courses.php" method="POST">
            <div class="star-rating w-100 text-center">
                <input id="star-5" type="radio" class="star" required name="rating" value="5" />
                <label for="star-5" title="5 stars">
                  <i class="active fa fa-star" aria-hidden="true"></i>
                </label>
                <input id="star-4" class="star" type="radio" name="rating" value="4" />
                <label for="star-4" title="4 stars">
                  <i class="active fa fa-star" aria-hidden="true"></i>
                </label>
                <input id="star-3" class="star" type="radio" name="rating" value="3" />
                <label for="star-3" title="3 stars">
                  <i class="active fa fa-star" aria-hidden="true"></i>
                </label>
                <input id="star-2" class="star" type="radio" name="rating" value="2" />
                <label for="star-2" title="2 stars">
                  <i class="active fa fa-star" aria-hidden="true"></i>
                </label>
                <input id="star-1" class="star" type="radio" name="rating" value="1" />
                <label for="star-1" title="1 star">
                  <i class="active fa fa-star" aria-hidden="true"></i>
                </label>
            </div>
            <textarea class="w-100 d-table p-3 border rounded mt-3" name="comments"></textarea>
            <button class="text-center log-btn go-back pt-2 pb-2 fnt-w-600 rounded border-0 w-100 text-white mt-4" id="Save_Data"  style="display: none;" name="Save_Data" type="submit">Submit</button></br></br>
            </form>
            </div>
        </div>
        <!-- JavaScript Bundle with Popper -->
	<script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.5.1.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js" integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous"></script>
    </body>
</html>
<?php
if(isset($_POST['Save_Data'])){
  $data = new stdclass();
  $data->star = $_POST['rating'];
  $data->comments =  $_POST['comments'];
  $data->userid = $USER->id;
  $data->companyid = $companyid;
  $DB->insert_record('user_feedback',$data);
  redirect($returnurl);
}

?>
<script>
$('.star').on('click',function(){
    if($('.star').val() > 0)
	$('#Save_Data').show();
	
})
</script>

