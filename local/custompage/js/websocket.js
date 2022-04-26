// get video dom element
const video = document.querySelector('video');
// alert(video);
// roomName should be unique user_name or user_id
var roomName =2;

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
	const FPS = 1;
    const ws = new WebSocket(
        'wss://webproctor.demoserver.work/ws/live/' + roomName + '/');
    ws.onopen = () => {
        console.log(`Connected`);
        setInterval(() => {
            ws.send(getFrame());
        }, 5000 / FPS);
    }

    //Disable cut copy paste
    $('body').bind('cut copy paste', function (e) {
        e.preventDefault();
    });
    
    //Disable mouse right click
    $("body").on("contextmenu",function(e){
        return false;
    });

    document.addEventListener('visibilitychange', function() {
        // document.title = document.visibilityState;
        // console.log(document.visibilityState);
        // tabchange
        if(document.visibilityState === 'hidden'){
            console.log("Tab Changed");
            cnt = parseInt($("#tabChangeCount").val());
            cnt += 1;
            $("#tabChangeCount").val(cnt);
            $("#tabChange").show();
        }
    });

    window.addEventListener('blur', function(){
        // windowchange
        console.log('blur');
        cnt_ = parseInt($("#windowChangeCount").val());
        cnt_ += 1;
        $("#windowChangeCount").val(cnt_);
        $("#windowChange").show();
    });
});