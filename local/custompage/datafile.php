<?php
require_once('../../config.php');
require_login(0, false);

global $DB, $CFG;

// displaying sub categories
if(isset($_POST['quizid'])){
    $quizid  = $_POST['quizid'];
    $parentcat = $DB->get_record('question_categories', array('id'=>$quizid));
    if($quizid)
    $qcat = $DB->get_records('question_categories', array('parent'=>$quizid));
    else
    $data = "";
    if($qcat ){
        $data = '<fieldset class="clearfix collapsible" id="id_qcategories">
        <legend class="ftoggler" id="yui_'.$parentcat->name.'"><a href="#" class="fheader" role="button" aria-controls="id_qcategories" aria-expanded="true" id="yui_'.$parentcat->name.'">'.$parentcat->name.'</a></legend>
        <div class="fcontainer clearfix">';
        // $mform->addElement('header', 'qcategories', $parentcat->name);
        foreach ($qcat as $key => $value) {
            $sqcat = $DB->get_records('question_categories', array('parent'=>$value->id));
            if($sqcat){ 
                $data .= '<fieldset class="clearfix collapsible" id="id_qcategories">
                <legend class="ftoggler" id="yui_'.$value->name.'"><a href="#" class="fheader" role="button" aria-controls="id_qcategories" aria-expanded="true" id="yui_'.$value->name.'">'.$value->name.'</a></legend>
                <div class="fcontainer clearfix">';
                foreach ($sqcat as $key => $value) {
                    $scatid=$DB->get_records_sql("select * from {question} where category=$value->id AND parent=0");
                    if(count($scatid) > 0){
                        $data .='<div id="fitem_id_category_'.$value->id.'" class="form-group row fitem">
                            <div class="col-md-3">
                                <span class="float-sm-right text-nowrap">
                                </span>
                                <label class="col-form-label d-inline " for="id_category_'.$value->id.'">
                                '. $value->name.'( Total Questions - '.count($scatid).' )' .'
                                </label>
                            </div>
                            <div class="col-md-9 form-inline felement" data-fieldtype="text">
                                <input type="text" class="form-control category" name="category['.$value->id.']" id="'.$value->id.'" value="" size="20"></br>
                                <label class="invalid-feedback" id="error_category_'.$value->id.'"></label>
                                <div class="form-control-feedback invalid-feedback" id="id_error_category_'.$value->id.'">
                                </div>
                            </div>
                        </div>';
                    }

                }
                $data .= '</div></fieldset>';
            }else{
                $ccatid=$DB->get_records_sql("select * from {question} where category=$value->id AND parent=0");
                if(count($ccatid) > 0){
                    $data .='<div id="fitem_id_category_'.$value->id.'" class="form-group row fitem">
                        <div class="col-md-3">
                            <span class="float-sm-right text-nowrap">
                            </span>
                            <label class="col-form-label d-inline " for="id_category_'.$value->id.'">
                            '. $value->name.'( Total Questions - '.count($ccatid).' )' .'
                            </label>
                        </div>
                        <div class="col-md-9 form-inline felement" data-fieldtype="text">
                            <input type="text" class="form-control category" name="category['.$value->id.']" id="'.$value->id.'" value="" size="20"></br>
                            <label class="invalid-feedback" id="error_category_'.$value->id.'"></label>
                            <div class="form-control-feedback invalid-feedback" id="id_error_category_'.$value->id.'">
                            </div>
                        </div>
                    </div>';
                }
            }
        }   
        $data .= '</div></fieldset>';
    }
    echo $data;
    // return $data;
}

// checking inserted value and count
if(isset($_POST['category'])){
    if(isset($_POST['value'])){
        $category = $_POST['category'];
        $value = $_POST['value'];
        if(is_numeric($value)){
			$count = $DB->get_records_sql("select id from {question} where category=$category AND parent=0");
            if(count($count) < $value){
                echo 201;
            }
        }else{
            echo 200;
        }
    }

}

// lead course and subcategories
if(isset($_POST['type'])){
    $course = $_POST['course_id'];
    if($course != ""){
        $data=$DB->get_record_sql("select * from {context} where contextlevel=50 and instanceid=$course");
        $contexts=$data->id;
        $top = false;
        $sortorder = 'parent, sortorder, name ASC';
        $topwhere = $top ? '' : 'AND c.parent <> 0';
        
        $parent=$DB->get_record_sql("select * from {question_categories} where contextid=$contexts and name='top'");
        $child=$DB->get_record_sql("select * from {question_categories} where parent=$parent->id");
        $categories = $DB->get_records_sql("SELECT c.name,c.id, (SELECT count(1) FROM {question} q WHERE c.id = q.category AND q.hidden='0' AND q.parent='0') AS questioncount
                    FROM {question_categories} c WHERE c.contextid IN ($contexts) AND c.name!='top' AND c.parent= $child->id $topwhere ORDER BY $sortorder");
        $select_data = '<option value="">---- Choose Question Category ----</option>';
        foreach($categories as $key => $val){
            $select_data .='<option value="'.$val->id.'">'.$val->name.'</option>';
        } 
        // $select_data .= '<option value="">---- Choose Question Category ----</option>';
        echo $select_data;
        // print_r($categories);
    }else{
        $select_data = '<option value="">---- Choose Question Category ----</option>';
        echo $select_data;
    }
}

// taking lead batches using course

if(isset($_POST['courseid'])){
    $select_data ="";
    $course_id = $_POST['courseid'];
    if($_POST['lead_type'] == 1){
        $select_data = '<option value="" selected disabled>---- Choose a Batch ----</option>';
        $batches = $DB->get_records_sql("SELECT * from {lead_batches} lb join {lead_batch_assigned_courses} lbc on lb.id = lbc.batchid where lbc.courseid = $course_id");
        foreach($batches as $keys => $batch){
           $select_data .='<option value =' .$batch->id.'>' .$batch->name.'</option>';
        }

    }
    if($_POST['lead_type'] == 2){
        $select_data = '<option value=""selected disabled>---- Choose a Test ----</option>';
        $tests = $DB->get_records_sql("SELECT * from {lead_test} where courseid = $course_id");
        foreach($tests as $keys => $test){
           $select_data .='<option value =' .$test->id.'>' .$test->name.'</option>';
        }
    }
    echo $select_data;
}

?>
