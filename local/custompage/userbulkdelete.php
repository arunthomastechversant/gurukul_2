<?php

require_once('../../config.php');
require_login(0, false);

global $DB, $CFG;
if(isset($_POST['idlist'])){
    $userids = $_POST['idlist'];
    $result = 0;
    list($in, $params) = $DB->get_in_or_equal($userids);
    $users = $DB->get_recordset_select('user', "deleted = 0 and id $in", $params);
    foreach($users as $user){
        delete_user($user);
        $DB->delete_records('rsl_user_detail',array('userid' => $user->id));
        $result++;
    }
    $result .= ' Users Deleted Successfully';
    echo $result;
}
?>