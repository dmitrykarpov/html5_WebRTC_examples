<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <title>getUserMedia 1</title>
        <style>
            video{
                border: 5px solid black;
                width: 480px;
                height: 360px;
            }
            button {
                font: 18px sans-serif;
                padding: 8px;
            }
        </style>
    </head>
    <body>
        <video id="vid" autoplay="true"></video>
        <br/>
        <button id="btn" onclick="start()">Start</button>
        <script>
            video = document.getElementById('vid');
            function start(){
                navigator.webkitGetUserMedia({video:true},gotStream, function(){});
                btn.disabled = true;
            }
            function gotStream(stream){
                video.src = webkitURL.createObjectURL(stream);
            }
        </script>
    </body>
</html>