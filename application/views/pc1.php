<!DOCTYPE html>
<html>
    <head>
        <title>PeerConection Demo #1</title>
    </head>
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
    <script>
        var RTCPeerConnection = null;
        var getUserMedia = null;
        var attachMediaStream = null;
        var reattachMediaStream = null;
        var webrtcDetectedBrowser = null;
        if(navigator.mozGetUserMedia){
            console.log("This appears to be Firfox");
            webrtcDetectedBrowser = "firefox";
            // the RTCPeerConccection object
            RTCPeerConnection = mozRTCPeerConnection;
            // the RTCSessionDescription object
            RTCSessionDescription = mozRTCSessionDescription;
            // The RTCIceCandidate object
            RTCIceCandidate = mozRTCIceCandidate;
            
            // Get User Media (only if difference is the preffix)
            // Code from Adam Barth
            getUserMedia = navigatior.mozGetUserMedia.bind(navigator);
            
            // Attach a media stream to an element
            attachMediaStream = function(element, stream){
                console.log("Attaching media stream");
                element.mozScrObject = stream;
                element.play();
            }
            
            // Fake get {Video, Audion} Tracks
            MediaStream.prototype.getVideoTracks = function(){
                return[];
            }
            MediaStream.prototype.getAudioTracks = function(){
                return[];
            }
        }else if(navigator.webkitGetUserMedia){
            webrtcDetectedBrowser = "chrome";
            // the RTCPeerConncetion object
            RTCPeerConnection = webkitRTCPeerConnection;
            // Get UserMedia (only difference is the prefix).
            // Code from Adam Barth.
            getUserMedia = navigator.webkitGetUserMedia.bind(navigator);
            
            // Attach a media stream to an element
            attachMediaStream = function(element, stream){
                element.src = webkitURL.createObjectURL(stream);
            }
            reattachMediaStream = function(to, from){
                to.src = from.src;
            }
            // The representation of tracksin a stream is changed in M26
            // Unify them for earlier Chrome versions in the coexisting period
            if(!webkitMediaStream.prototype.getVideoTracks){
                webkitMediaStream.prototype.getVideoTracks = function(){
                    trace("getVideoTracks");
                    return this.videoTracks;
                }
            }
            if(!webkitMediaStream.prototype.getAudoTracks){
                webkitMediaStream.prototype.getAudioTracks = function(){
                    trace("getAutioTracks");
                    return this.audioTracks;
                }
            }
        } else{
            console.log("Browser does not appear to be WebRTC-capable");
        }
    </script>
    <body>
        <video id="vid1" autoplay></video>
        <video id="vid2" autoplay></video>
        <br>
        <button id="btn1" onclick="start()">Start</button>
        <button id="btn2" onclick="call()">Call</button>
        <button id="btn3" onclick="hangup()">Hang Up</button>
        <br>
        <textarea id="ta1"></textarea>
        <textarea id="ta2"></textarea>
        <script>
            btn1.disabled = false; // Start
            btn2.disabled = true; // call
            btn3.disabled = true; // hang up
            var pc1, pc2;
            var localstream;
            function trace(text){
                if('\n' == text[text.length - 1]){
                    text = text.substring(0, text.length - 1);
                }
                console.log((performance.now()/1000).toFixed(3) + ": " + text);
            }
            function gotStream(stream){
                trace("Received local stream");
                attachMediaStream(vid1, stream);
                localstream = stream;
                btn2.disabled = false; // call
            }
            function start(){
                trace("Request local stream");
                btn1.disabled = true; // start
                // call inot getuserMedia via the polyfill (adapter.js)
                // at chrome it is a navigator.webkitGetUserMedia.bind(navigator)
                getUserMedia({audio:true, video:true},gotStream, function(){});
            }
            function call(){
                btn2.disabled = true; // call
                btn3.disabled = false; // hangup
                trace("starting call");
                videoTracks = localstream.getVideoTracks();
                audioTracks = localstream.getAudioTracks();
                if(videoTracks.length > 0){
                    trace('Using Videl device: ' + videoTracks[0].label);
                }
                if(audioTracks.length > 0){
                    trace('Using Audio device: ' + audioTracks[0].label);
                }
                var servers = null;
                pc1 = new RTCPeerConnection(servers);
                trace("Created local peer connectin object pc1");
                pc1.onicecandidate = iceCallback1;
                pc2 = new RTCPeerConnection(servers);
                trace("Created remote peer conncetion object pc2");
                pc2.onicecandidate = iceCallback2;
                pc2.onaddstream = gotRemoteStream;
                
                pc1.addStream(localstream);
                trace("Adding local strem to peer connection");
                
                pc1.createOffer(gotDescription1);

            }
            function gotDescription1(desc){
                pc1.setLocalDescription(desc);
                trace("Offer from pc1 \n" + desc.sdp);
                pc2.setRemoteDescription(desc);
                pc2.createAnswer(gotDescription2);
            }
            function gotDescription2(desc){
                pc2.setLocalDescription(desc);
                trace("Answer from pc2 \n" + desc.sdp);
                pc1.setRemoteDescription(desc);
            }
            function hangup(){
                trace("Ending call");
                pc1.close();
                pc2.close();
                pc1 = null;
                pc2 = null;
                btn3.disabled = true; // hangup
                btn2.disabled = false; // call
            }
            function gotRemoteStream(e){
                vid2.src = webkitURL.createObjectURL(e.stream);
                trace("Receive remote stream");
            }
            function iceCallback1(event){
                if(event.candidate){
                    pc2.addIceCandidate(new RTCIceCandidate(event.candidate));
                    trace("Local ICE candidate: \n" + event.candidate.candidate);
                }
            }
            function iceCallback2(event){
                if(event.candidate){
                    pc1.addIceCandidate(new RTCIceCandidate(event.candidate));
                    trace("Remote ICE candidate: \n");
                }
            }
            
        </script>
    </body>
</html>