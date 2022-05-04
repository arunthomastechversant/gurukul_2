<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This script displays a particular page of a quiz attempt that is in progress.
 *
 * @package   mod_quiz
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->dirroot . '/mod/quiz/locallib.php');

// Look for old-style URLs, such as may be in the logs, and redirect them to startattemtp.php.
if ($id = optional_param('id', 0, PARAM_INT)) {
    redirect($CFG->wwwroot . '/mod/quiz/startattempt.php?cmid=' . $id . '&sesskey=' . sesskey());
} else if ($qid = optional_param('q', 0, PARAM_INT)) {
    if (!$cm = get_coursemodule_from_instance('quiz', $qid)) {
        print_error('invalidquizid', 'quiz');
    }
    redirect(new moodle_url('/mod/quiz/startattempt.php',
            array('cmid' => $cm->id, 'sesskey' => sesskey())));
}

// Get submitted parameters.
$attemptid = required_param('attempt', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$cmid = optional_param('cmid', null, PARAM_INT);

$attemptobj = quiz_create_attempt_handling_errors($attemptid, $cmid);
$page = $attemptobj->force_page_number_into_range($page);
$PAGE->set_url($attemptobj->attempt_url(null, $page));

// print_r($page);exit();

// Check login.
require_login($attemptobj->get_course(), false, $attemptobj->get_cm());

// Check that this attempt belongs to this user.
if ($attemptobj->get_userid() != $USER->id) {
    if ($attemptobj->has_capability('mod/quiz:viewreports')) {
        redirect($attemptobj->review_url(null, $page));
    } else {
        throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'notyourattempt');
    }
}

// Check capabilities and block settings.
if (!$attemptobj->is_preview_user()) {
    $attemptobj->require_capability('mod/quiz:attempt');
    if (empty($attemptobj->get_quiz()->showblocks)) {
        $PAGE->blocks->show_only_fake_blocks();
    }

} else {
    navigation_node::override_active_url($attemptobj->start_attempt_url());
}

// If the attempt is already closed, send them to the review page.
if ($attemptobj->is_finished()) {
    redirect($attemptobj->review_url(null, $page));
} else if ($attemptobj->get_state() == quiz_attempt::OVERDUE) {
    redirect($attemptobj->summary_url());
}

// Check the access rules.
$accessmanager = $attemptobj->get_access_manager(time());
$accessmanager->setup_attempt_page($PAGE);
$output = $PAGE->get_renderer('mod_quiz');
$messages = $accessmanager->prevent_access();
if (!$attemptobj->is_preview_user() && $messages) {
    print_error('attempterror', 'quiz', $attemptobj->view_url(),
            $output->access_messages($messages));
}
if ($accessmanager->is_preflight_check_required($attemptobj->get_attemptid())) {
    redirect($attemptobj->start_attempt_url(null, $page));
}
// Set up auto-save if required.
$autosaveperiod = get_config('quiz', 'autosaveperiod');
if ($autosaveperiod) {
    $PAGE->requires->yui_module('moodle-mod_quiz-autosave',
            'M.mod_quiz.autosave.init', array($autosaveperiod));
}

// Log this page view.
$attemptobj->fire_attempt_viewed_event();

// Get the list of questions needed by this page.
$slots = $attemptobj->get_slots($page);

// Check.
if (empty($slots)) {
    throw new moodle_quiz_exception($attemptobj->get_quizobj(), 'noquestionsfound');
}

// Update attempt page, redirecting the user if $page is not valid.
if (!$attemptobj->set_currentpage($page)) {
    redirect($attemptobj->start_attempt_url(null, $attemptobj->get_currentpage()));
}

// Initialise the JavaScript.
$headtags = $attemptobj->get_html_head_contributions($page);
$PAGE->requires->js_init_call('M.mod_quiz.init_attempt_form', null, false, quiz_get_js_module());

// Arrange for the navigation to be displayed in the first region on the page.
$navbc = $attemptobj->get_navigation_panel($output, 'quiz_attempt_nav_panel', $page);
$regions = $PAGE->blocks->get_regions();
$PAGE->blocks->add_fake_block($navbc, reset($regions));

$headtags = $attemptobj->get_html_head_contributions($page);
$PAGE->set_title($attemptobj->attempt_page_title($page));
//$PAGE->set_heading($attemptobj->get_course()->fullname);
$PAGE->set_heading($attemptobj->get_quiz()->name);

if ($attemptobj->is_last_page($page)) {
    $nextpage = -1;
} else {
    $nextpage = $page + 1;
}
echo '<noscript><meta http-equiv="refresh" content="0;url=../../local/failedwarning.php?key=scriptwitherror"></noscript>';

echo $output->attempt_page($attemptobj, $page, $accessmanager, $messages, $slots, $id, $nextpage);


$cmid = optional_param('cmid','',PARAM_INT);
if($cmid ){
    $quizdata = $cmid.'_'.$USER->id;
}
global $DB;
$userid=$USER->id;
$quizid = $cmid;
$attempt = $attemptobj->get_attempt();
$proctoring = $DB->get_record_sql("SELECT eproctoringrequired from {quizaccess_eproctoring} where quizid = $attempt->quiz");
if($proctoring == 1){

?>
<script>
// websocket.js - start
// get video dom element
var userid=<?php echo $userid; ?>;
var quizid=<?php echo $quizid; ?>;
var ws;
const video = document.querySelector('video');
// alert(video);
// roomName should be unique user_name or user_id

var roomName =userid+'_'+quizid;
console.log(roomName);
// request access to webcam
navigator.mediaDevices.getUserMedia({video: {width: 426, height: 240}}).then((stream) => video.srcObject = stream);

// returns a frame encoded in base64
const getFrame = () => {
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const data = canvas.toDataURL('image/png');
    return data;
}

$(document).ready(function () {
    reload = 0;
    checkDeviceSupport(function() {
        if(isWebcamAlreadyCaptured == false){
            let url = '<?php echo $CFG->wwwroot.'/local/failedwarning.php?key=camfindwitherror'?>';
            // alert(url);
            window.location.href = url;
        }
    });
    $('.btn-secondary').val('Submit');
	const FPS = 1;
   	 ws = new WebSocket(
        'wss://guruku1-pr0ct0r1ng.quest-global.com:8030/ws/live/' + roomName + '/');
    	//console.log(ws);
	//console.log(WebSocket.OPEN);
	//if (WebSocket.OPEN) {
      	    //ws.close();
	    //console.log(`Closed`);
   	//}
	localStorage.setItem('ws', JSON.stringify(ws));
	ws.onopen = () => {
        console.log(`Connected`);
        setInterval(() => {
            ws.send(getFrame());
        }, 30000 / FPS);
    }
    //ws.close();
    // "wss://192.168.12.246/ws/live/' + roomName + '/"
    //Disable cut copy paste
    $('body').bind('cut copy paste', function (e) {
        e.preventDefault();
    });
    
    //Disable mouse right click
    $("body").on("contextmenu",function(e){
        return false;
    });
    window.addEventListener("beforeunload", function (e) {
        console.log('Reload');
        reload = 1;
    });

    document.addEventListener('visibilitychange', function() {
        // document.title = document.visibilityState;
        // console.log(document.visibilityState);
        // tabchange
        if(document.visibilityState === 'hidden'){
            if(reload == 0){
                $.ajax({
                    url: 'ajax.php',
                    dataType: 'json',
                    type: 'post',
                    data:{tab:1,userid:userid,quizid:quizid,},
                    success: function(data) {
                        //console.log(data.response);
                    }
                });
                console.log("Tab Changed");
                cnt = parseInt($("#tabChangeCount").val());
                cnt += 1;
                $("#tabChangeCount").val(cnt);
                $("#tabChange").show();
            }
        }
    });

    window.addEventListener('blur', function(){
        // windowchange
        // if(reload == 0){
            $.ajax({
                url: 'ajax.php',
                dataType: 'json',
                type: 'post',
                data:{window:1,userid:userid,quizid:quizid,},
                success: function(data) {
                //    alert(data.response);
                }
            });

            console.log('Window Change');
            cnt_ = parseInt($("#windowChangeCount").val());
            cnt_ += 1;
            $("#windowChangeCount").val(cnt_);
            $("#windowChange").show();
        // }
    });
	$('.mod_quiz-next-nav').on('click',function(){
        ws.close();
		//alert('Closing the socket');
   	})
    $('.mod_quiz-prev-nav').on('click',function(){
    	ws.close();
		//alert('clicked');
    })
});
// websocket.js - end

// volume checker -- starts


var meter = null;
var WIDTH = 500;
var recordingStarted = false;

// initialize SpeechRecognition object
let recognition = new webkitSpeechRecognition();
recognition.maxAlternatives = 1;
recognition.continuous = true;

navigator.getUserMedia({audio: true}, startUserMedia, function(e) {
  __log('No live audio input: ' + e);
});

startSpeech();

function startUserMedia(stream) {
    const ctx = new AudioContext();
    const analyser = ctx.createAnalyser();
    const streamNode = ctx.createMediaStreamSource(stream);
    streamNode.connect(analyser);

    // Create a new volume meter and connect it.
    meter = createAudioMeter(ctx);
    streamNode.connect(meter);

    drawLoop();

}

// Detect the said words
recognition.onresult = e => {

    var current = event.resultIndex;

    // Get a transcript of what was said.
    var transcript = event.results[current][0].transcript;

    // Add the current transcript with existing said values
    var noteContent = $('#saidwords').val();
    noteContent += ' ' + transcript;
    $('#saidwords').val(noteContent);

}

// Stop recording
// function stopSpeech(){

//     // Change status
//     $('#status').text('Stopped checking background noise');
//     recordingStarted = false;

//     // Stop recognition
//     recognition.stop();
// }

// Start recording
function startSpeech(){
    try{ // calling it twice will throw..
        $('#status').text('Checking background noise'); 
        $('#saidwords').val('');
        recordingStarted = true;

        // Start recognition
        recognition.start();
    }
    catch(e){}
}

// Create pitch bar
function drawLoop( time ) {

    var pitchVolume = meter.volume*WIDTH*1.4;

    var width = 0;

    // Pitch detection minimum volume
    var minimum_volume = 10;

    // Get width if Recording started
    if(recordingStarted){

        if(pitchVolume < minimum_volume){
            width = 0;
        }else if(pitchVolume >= minimum_volume && pitchVolume < (minimum_volume+20) ){
            // volume check
            $.ajax({
                url: 'ajax.php',
                dataType: 'json',
                type: 'post',
                data:{volume:1,userid:userid,quizid:quizid,},
                success: function(data) {
                // alert(data.response);
                }
            });
            //console.log('voice');
        }
        else if(pitchVolume >= minimum_volume && pitchVolume < (minimum_volume+20) ){
            width = 10;
        }else if(pitchVolume >= (minimum_volume+20) && pitchVolume < (minimum_volume+40)){
            width = 20;
        }else if(pitchVolume >= (minimum_volume+40) && pitchVolume < (minimum_volume+60)){
            width = 30;
        }else if(pitchVolume >= (minimum_volume+60) && pitchVolume < (minimum_volume+80)){
            width = 40;
        }else if(pitchVolume >= (minimum_volume+80) && pitchVolume < (minimum_volume+100)){
            width = 50;
        }else if(pitchVolume >= (minimum_volume+100) && pitchVolume < (minimum_volume+120)){
            width = 60;
        }else if(pitchVolume >= (minimum_volume+120) && pitchVolume < (minimum_volume+140)){
            width = 70;
        }else if(pitchVolume >= (minimum_volume+140) && pitchVolume < (minimum_volume+160)){
            width = 80;
        }else if(pitchVolume >= (minimum_volume+160) && pitchVolume < (minimum_volume+180)){
            width = 90;
        }else if(pitchVolume >= (minimum_volume+180)){
            width = 100;
        }
    }

    // Update width
    document.getElementById('voiceVolume').style.width = width+'%';

    rafID = window.requestAnimationFrame( drawLoop );
}
// volume checker ends


if (navigator.mediaDevices && navigator.mediaDevices.enumerateDevices) {
    // Firefox 38+ seems having support of enumerateDevicesx
    navigator.enumerateDevices = function(callback) {
        navigator.mediaDevices.enumerateDevices().then(callback);
    };
}

var MediaDevices = [];
var isHTTPs = location.protocol === 'https:';
var canEnumerate = false;

if (typeof MediaStreamTrack !== 'undefined' && 'getSources' in MediaStreamTrack) {
    canEnumerate = true;
} else if (navigator.mediaDevices && !!navigator.mediaDevices.enumerateDevices) {
    canEnumerate = true;
}


var isMicrophoneAlreadyCaptured = false;
var isWebcamAlreadyCaptured = false;

function checkDeviceSupport(callback) {
    if (!canEnumerate) {
        return;
    }

    if (!navigator.enumerateDevices && window.MediaStreamTrack && window.MediaStreamTrack.getSources) {
        navigator.enumerateDevices = window.MediaStreamTrack.getSources.bind(window.MediaStreamTrack);
    }

    if (!navigator.enumerateDevices && navigator.enumerateDevices) {
        navigator.enumerateDevices = navigator.enumerateDevices.bind(navigator);
    }

    if (!navigator.enumerateDevices) {
        if (callback) {
            callback();
        }
        return;
    }

    MediaDevices = [];
    navigator.enumerateDevices(function(devices) {
        devices.forEach(function(_device) {
            var device = {};
            for (var d in _device) {
                device[d] = _device[d];
            }

            if (!device.label) {
                device.label = 'Please invoke getUserMedia once.';
                if (!isHTTPs) {
                    device.label = 'HTTPs is required to get label of this ' + device.kind + ' device.';
                }
            } else {
                if (device.kind === 'videoinput' && !isWebcamAlreadyCaptured) {
                    isWebcamAlreadyCaptured = true;
                    $("#snap").show();
                }

                if (device.kind === 'audioinput' && !isMicrophoneAlreadyCaptured) {
                    isMicrophoneAlreadyCaptured = true;
                }
            }

            MediaDevices.push(device);
        });

        if (callback) {
            callback();
            
        }
    });
    
}
</script>
<?php } ?>

<style>
body.pagelayout-incourse .activity-navigation {
    display: none;
}
</style>