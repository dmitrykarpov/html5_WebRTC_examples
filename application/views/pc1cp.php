<!DOCTYPE html>
<html>
<head>
<title>PeerConnection Demo 1</title>
<!-- Load the polyfill to switch-hit between Chrome and Firefox -->
<script>
    var RTCPeerConnection = null;
var getUserMedia = null;
var attachMediaStream = null;
var reattachMediaStream = null;
var webrtcDetectedBrowser = null;

if (navigator.mozGetUserMedia) {
  console.log("This appears to be Firefox");

  webrtcDetectedBrowser = "firefox";

  // The RTCPeerConnection object.
  RTCPeerConnection = mozRTCPeerConnection;

  // The RTCSessionDescription object.
  RTCSessionDescription = mozRTCSessionDescription;

  // The RTCIceCandidate object.
  RTCIceCandidate = mozRTCIceCandidate;

  // Get UserMedia (only difference is the prefix).
  // Code from Adam Barth.
  getUserMedia = navigator.mozGetUserMedia.bind(navigator);

  // Attach a media stream to an element.
  attachMediaStream = function(element, stream) {
    console.log("Attaching media stream");
    element.mozSrcObject = stream;
    element.play();
  };

  reattachMediaStream = function(to, from) {
    console.log("Reattaching media stream");
    to.mozSrcObject = from.mozSrcObject;
    to.play();
  };

  // Fake get{Video,Audio}Tracks
  MediaStream.prototype.getVideoTracks = function() {
    return [];
  };

  MediaStream.prototype.getAudioTracks = function() {
    return [];
  };
} else if (navigator.webkitGetUserMedia) {
  console.log("This appears to be Chrome");

  webrtcDetectedBrowser = "chrome";

  // The RTCPeerConnection object.
  RTCPeerConnection = webkitRTCPeerConnection;
  
  // Get UserMedia (only difference is the prefix).
  // Code from Adam Barth.
  getUserMedia = navigator.webkitGetUserMedia.bind(navigator);

  // Attach a media stream to an element.
  attachMediaStream = function(element, stream) {
    element.src = webkitURL.createObjectURL(stream);
  };

  reattachMediaStream = function(to, from) {
    to.src = from.src;
  };

  // The representation of tracks in a stream is changed in M26.
  // Unify them for earlier Chrome versions in the coexisting period.
  if (!webkitMediaStream.prototype.getVideoTracks) {
    webkitMediaStream.prototype.getVideoTracks = function() {
      return this.videoTracks;
    };
  }

  if (!webkitMediaStream.prototype.getAudioTracks) {
    webkitMediaStream.prototype.getAudioTracks = function() {
      return this.audioTracks;
    };
  }
} else {
  console.log("Browser does not appear to be WebRTC-capable");
}

</script>
<style>
video {
  border:5px solid black;
  width:480px;
  height:360px;
}
button {
  font: 18px sans-serif;
  padding: 8px;
}
textarea {
  font-family: monospace;
  margin: 2px;
  width:480px;
  height:640px;
}
</style>
</head>
<body>
<video id="vid1" autoplay></video>
<video id="vid2" autoplay></video>
<br>
<button id="btn1" onclick="start()">Start</button>
<button id="btn2" onclick="call()">Call</button>
<button id="btn3" onclick="hangup()">Hang Up</button>
<br>
<xtextarea id="ta1"></textarea>
<xtextarea id="ta2"></textarea>
<script>
//var vid1 = document.getElementById("vid1");
//var vid2 = document.getElementById("vid2");
btn1.disabled = false;
btn2.disabled = true;
btn3.disabled = true;
var pc1,pc2;
var localstream;

function trace(text) {
  // This function is used for logging.
  if (text[text.length - 1] == '\n') {
    text = text.substring(0, text.length - 1);
  }
  console.log((performance.now() / 1000).toFixed(3) + ": " + text);
}

function gotStream(stream){
  trace("Received local stream");
  // Call the polyfill wrapper to attach the media stream to this element.
  attachMediaStream(vid1, stream);
  localstream = stream;
  btn2.disabled = false;
}

function start() {
  trace("Requesting local stream");
  btn1.disabled = true;
  // Call into getUserMedia via the polyfill (adapter.js).
  getUserMedia({audio:true, video:true},
                gotStream, function() {});
}  
  
function call() {
  btn2.disabled = true;
  btn3.disabled = false;
  trace("Starting call");
  videoTracks = localstream.getVideoTracks();
  audioTracks = localstream.getAudioTracks();
  if (videoTracks.length > 0)
    trace('Using Video device: ' + videoTracks[0].label);  
  if (audioTracks.length > 0)
    trace('Using Audio device: ' + audioTracks[0].label);
  var servers = null;
  pc1 = new RTCPeerConnection(servers);
  trace("Created local peer connection object pc1");
  pc1.onicecandidate = iceCallback1; 
  pc2 = new RTCPeerConnection(servers);
  trace("Created remote peer connection object pc2");
  pc2.onicecandidate = iceCallback2;
  pc2.onaddstream = gotRemoteStream; 

  pc1.addStream(localstream);
  trace("Adding Local Stream to peer connection");
  
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

function hangup() {
  trace("Ending call");
  pc1.close(); 
  pc2.close();
  pc1 = null;
  pc2 = null;
  btn3.disabled = true;
  btn2.disabled = false;
}

function gotRemoteStream(e){
  vid2.src = webkitURL.createObjectURL(e.stream);
  trace("Received remote stream");
}

function iceCallback1(event){
  if (event.candidate) {
    pc2.addIceCandidate(new RTCIceCandidate(event.candidate));
    trace("Local ICE candidate: \n" + event.candidate.candidate);
  }
}
      
function iceCallback2(event){
  if (event.candidate) {
    pc1.addIceCandidate(new RTCIceCandidate(event.candidate));
    trace("Remote ICE candidate: \n " + event.candidate.candidate);
  }
}
</script>
</body>
</html>


