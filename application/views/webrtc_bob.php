<!DOCTYPE html>
<html>
    <head>
        <title>Bob(Callee)</title>
    <style>
        video {
            border:5px solid black;
            width: 480px;
            height: 360px;
        }
        button {
            font: 18px sans-serif;
            padding: 8px;
        }
        textarea{
            font-family: monospace;
            margin: 2px;
            width: 480px;
            height: 640px;
        }
    </style>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <script>
        ajax_url = "<?php echo site_url(array("webrtc","polling_ajax","b"));?>";
        
       //------------------------------------------------------------------------>    
         function ThePollingSignaller(){
            this.req  = []; // array of request that shall be send
            this.ans  = []; // answers from server that will be fire at the onmessage
            this.time = 2000; // how often the polling request will be
        }
        ThePollingSignaller.prototype.send = function(msg){
            this.req.push(msg);
        }
        // this functin will call itself
        ThePollingSignaller.prototype._request = function(){
            var req_msg = "";
            if( 0 != this.req.length){
                req_msg = this.req.shift();
            }
            var that = this;
             $.post(ajax_url,{'req': req_msg}, function(data){
                that.ans = data;
                if("" != $.trim(data)){
                    console.log("Signaling message:", data);
                    that.onmessage(that.ans);
                }
                window.setTimeout(function(){that._request()},that.time);
            });
        }
       ThePollingSignaller.prototype.onmessage = function(data){
           console.log(data,"onmessage");
       }
       
       function createSignalingChannel(){
           var signaller = new ThePollingSignaller();
           signaller._request();
           return signaller;
       }
       
       $(function(){
           var empty_url = "<?php echo site_url(array("webrtc","polling_ajax","empty_table"));?>";
          // 1 clear table to aviod some problems
          $.post(empty_url,{},function(){});
          // 2. able to start
          $('#btn1').show();
          
          
          });
       
        var signalingChannel;
        $(function(){
            signalingChannel = createSignalingChannel();
            signalingChannel.onmessage = function (data) {
    if (!pc){
        console.log("starting answer");
        start(false);
    }

    var signal = JSON.parse(data); // here at http://www.html5rocks.com/en/tutorials/webrtc/basics/ was event.data
    console.log("Signal: ", signal);
    if (signal.sdp){
        console.log("Set remote descr: ", signal.sdp );
        pc.setRemoteDescription(new RTCSessionDescription(signal.sdp));
    }
    else if (signal.candidate){
        console.log("Set candidate: ", signal.candidate);
        pc.addIceCandidate(new RTCIceCandidate(signal.candidate));
    }
        }
        });
        var pc;
       var configuration =  {"iceServers": [{"url": "stun:stun.l.google.com:19302"}]};
      // var configuration =  {"iceServers": [{"url": "TURN:numb.viagenie.ca:3478"}]};
        var pc_constraints = {"optional": []};

// run start(true) to initiate a call
function start(isCaller) {
     RTCPeerConnection = webkitRTCPeerConnection;
    pc = new RTCPeerConnection(configuration,pc_constraints);

    // send any ice candidates to the other peer
    pc.onicecandidate = function (evt) {
        console.log("Send candidate", evt);
        signalingChannel.send(JSON.stringify({ "candidate": evt.candidate }));
    };

    // once remote stream arrives, show it in the remote video element
    pc.onaddstream = function (evt) {
        remoteView.src = webkitURL.createObjectURL(evt.stream);
    };

    // get the local stream, show it in the local video element and send it
    navigator.webkitGetUserMedia({ "audio": true, "video": true }, function (stream) {
        selfView.src = webkitURL.createObjectURL(stream);
        pc.addStream(stream);

        if (isCaller){
            console.log("Create offer:");
            pc.createOffer(gotDescription);
        }
        else{
            console.log("Create Answer(5):");
            pc.createAnswer(gotDescription);
            console.log("Answer Created");
        }

        function gotDescription(desc) {
            console.log("Send description", desc);
            pc.setLocalDescription(desc);
            signalingChannel.send(JSON.stringify({ "sdp": desc }));
        }
    });
}

//---------------------------------------------------------------------->
    </script>
    </head>
 <body>
        Bob (Callee)<br/>
        <video id="selfView" autoplay></video>
<video id="remoteView" autoplay></video>
<br>
 </body>
</html>